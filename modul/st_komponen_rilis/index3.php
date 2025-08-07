<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Tingkatkan batas memori sementara
ini_set('memory_limit', '512M');

// Koneksi database
$mysqli = new mysqli("localhost", "root", "root", "pmimojo");

// Cek koneksi
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Proses data dalam batch
$batchSize = 1000;
$offset = 0;

do {
    $sql = "SELECT 
                d.NoTrans, d.idpendonor, p.KD, dk.tanggal, 
                dk.barcode, 
                JSON_UNQUOTE(JSON_EXTRACT(IFNULL(dld.detail, '{}'), '$.custom_3')) AS petugas,
                JSON_UNQUOTE(JSON_EXTRACT(IFNULL(dld.detail, '{}'), '$.custom_4')) AS `user`,
                d.berat AS beratBadan, d.lokasi AS Instansi, YEAR(dk.tanggal) AS tahun,
                JSON_UNQUOTE(JSON_EXTRACT(IFNULL(dld.detail, '{}'), '$.custom_1')) AS Hb,
                REPLACE(REPLACE(dk.golda, '+', ''), '-', '') AS pendonor_abo, 
                dk.rhesus, TIMESTAMPDIFF(YEAR, p.TglLhr, CURDATE()) AS umur_tahun, 
                p.pekerjaan, p.jk
            FROM pddr p 
            JOIN donor_log d ON p.Kode_lama = d.idpendonor 
            LEFT JOIN donor_kantong dk ON d.iddonorlog = dk.iddonorlog 
            LEFT JOIN donor_log_detil dld ON dk.iddonor = dld.iddonor 
            WHERE dld.detail IS NOT NULL AND TRIM(dld.detail) != ''
            LIMIT $batchSize OFFSET $offset;";

    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Proses data
            $noTrans = $row['NoTrans'];
            $kodePendonorLama = $row['idpendonor'];
            $kodePendonor = $row['KD'];
            $tgl = $row['tanggal'];

            // Periksa apakah NoTrans sudah ada di tabel ht
            $checkQuery = $mysqli->prepare("SELECT COUNT(*) FROM ht WHERE NoTrans = ?");
            $checkQuery->bind_param("s", $noTrans);
            $checkQuery->execute();
            $checkQuery->bind_result($count);
            $checkQuery->fetch();
            $checkQuery->close();

            if ($count > 0) {
                continue;
            }

            // Query INSERT
            $insertQuery = $mysqli->prepare("
                INSERT INTO ht (`NoTrans`, `KodePendonor_lama`, `KodePendonor`, `Tgl`, `beratBadan`, `Instansi`, `tahun`, `Hb`, `pendonor_abo`, `rhesus`, `umur`, `pekerjaan`, `jk`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertQuery->bind_param(
                "sssssssssssss",
                $noTrans,
                $kodePendonorLama,
                $kodePendonor,
                $tgl,
                $row['beratBadan'],
                $row['Instansi'],
                $row['tahun'],
                $row['Hb'],
                $row['pendonor_abo'],
                $row['rhesus'],
                $row['umur_tahun'],
                $row['pekerjaan'],
                $row['jk']
            );

            if (!$insertQuery->execute()) {
                echo "Error: " . $insertQuery->error . "<br>";
            }

            $insertQuery->close();
        }
    }

    $offset += $batchSize;
} while ($result->num_rows > 0);

echo "Data successfully inserted.";

// Tutup koneksi
$mysqli->close();
?>