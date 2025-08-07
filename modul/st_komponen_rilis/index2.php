<?php
$pdo = new PDO("mysql:host=localhost;dbname=pmimojo", "root", "root");

// Reset NoTrans (opsional, untuk keperluan development saja)
$pdo->exec("UPDATE donor_log SET NoTrans = NULL");

// Ambil data donor_log terurut
$stmt = $pdo->query("SELECT iddonorlog, lokasi, tanggal FROM donor_log ORDER BY DATE(tanggal), iddonorlog");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counterPerDate = [];

foreach ($data as $row) {
    $id = $row['iddonorlog'];
    $lokasi = $row['lokasi'];
    $tanggal = $row['tanggal'];

    $tglOnly = substr($tanggal, 0, 10);
    $tglFormat = date('dmy', strtotime($tanggal));
    $kodeLokasi = (stripos($lokasi, 'UDD PMI KAB MOJOKERTO') !== false) ? 'DG' : 'M1';

    if (!isset($counterPerDate[$tglOnly])) {
        $counterPerDate[$tglOnly] = 1;
    } else {
        $counterPerDate[$tglOnly]++;
    }

    $noTrans = $kodeLokasi . $tglFormat . '-3516-' . str_pad($counterPerDate[$tglOnly], 4, '0', STR_PAD_LEFT);

    $update = $pdo->prepare("UPDATE donor_log SET NoTrans = ? WHERE iddonorlog = ?");
    $update->execute([$noTrans, $id]);
}

// -----------------------------------
// TAMPILKAN DATA GANDA NoTrans
// -----------------------------------
echo "<h2>Daftar NoTrans Ganda</h2>";
echo "<table border='1' cellpadding='6' cellspacing='0'>";
echo "<tr><th>NoTrans</th><th>Jumlah</th><th>Rincian ID</th></tr>";

$stmt = $pdo->query("
    SELECT NoTrans, COUNT(*) as jumlah 
    FROM donor_log 
    WHERE NoTrans IS NOT NULL 
    GROUP BY NoTrans 
    HAVING jumlah > 1
");

$duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($duplicates) === 0) {
    echo "<tr><td colspan='3'><b>Tidak ada NoTrans ganda</b></td></tr>";
} else {
    foreach ($duplicates as $dup) {
        $noTrans = $dup['NoTrans'];
        $jumlah = $dup['jumlah'];

        // Ambil ID yang memiliki NoTrans tersebut
        $stmtDetail = $pdo->prepare("SELECT iddonorlog FROM donor_log WHERE NoTrans = ?");
        $stmtDetail->execute([$noTrans]);
        $ids = $stmtDetail->fetchAll(PDO::FETCH_COLUMN);

        echo "<tr>";
        echo "<td>{$noTrans}</td>";
        echo "<td>{$jumlah}</td>";
        echo "<td>" . implode(", ", $ids) . "</td>";
        echo "</tr>";
    }
}

echo "</table>";
?>
