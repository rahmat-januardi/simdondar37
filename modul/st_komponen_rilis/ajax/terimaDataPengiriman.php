<?php
// File: ajax/tambahDataPengiriman.php
date_default_timezone_set('Asia/Jakarta');
session_start();
include "../config/dbi_connect.php";

$nomor = isset($_POST['nomor_kantong']) ? $_POST['nomor_kantong'] : '';
$iPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : "irawanDB";

$d = date('d');
$m = date('m');
$y = substr(date('Y'), 2, 2);

$jamSekarang = date('H:i');

// if ($jamSekarang >= '08:00' && $jamSekarang <= '14:00') {
//     $shiftST = 'PAGI';
// } elseif ($jamSekarang <= '21:00') {
//     $shiftST = 'SORE';
// } elseif ($jamSekarang <= '23:59') {
//     $shiftST = 'MALAM1';
// } else {
//     $shiftST = 'MALAM2';
// }

if ($jamSekarang >= '08:00' && $jamSekarang <= '14:00') {
    $shiftST = 'PAGI';
} elseif ($jamSekarang >= '14:01' && $jamSekarang <= '21:00') {
    $shiftST = 'SORE';
} elseif ($jamSekarang >= '21:01' || $jamSekarang == '00:00') {
    $shiftST = 'MALAM1';
} elseif ($jamSekarang >= '00:01' && $jamSekarang <= '07:59') {
    $shiftST = 'MALAM2';
} else {
    $shiftST = 'TIDAK DIKETAHUI';
}

// $bagKirim = isset($_POST['bagian_kirim']) ? $_POST['bagian_kirim'] : '-';
$bagKirim = isset($_POST['bagKirim']) ? $_POST['bagKirim'] : "KOMPONEN";
$jenisKirim = "Kantong (Produk Darah)";
$bagTerima = "RILIS";
$peruntukan = "POSTING HASIL PENGOLAHAN DARAH";
$kodeAlat = isset($_POST['kode_alat']) ? $_POST['kode_alat'] : '-';
$suhuPengiriman = isset($_POST['suhu_pengiriman']) ? $_POST['suhu_pengiriman'] : '-';
$keadaan = isset($_POST['keadaan']) ? $_POST['keadaan'] : '-';
$sampleST = "0";
$sahST = 1;
$modulST = "KOMPONEN";
// $shiftST = "I";
// $tujuan = "3509";

$queryST = "SELECT jenis, `Status`, `position`, produk, sah, merk, gol_darah, RhesusDrh, KodePendonor, '0' AS berat, volume, lama_pengambilan  FROM stokkantong WHERE noKantong = '$nomor'";
$resultST = mysqli_query($dbi, $queryST);

if (!$resultST) {
    die("Query stokkantong gagal: " . mysqli_error($dbi));
}

if ($row = mysqli_fetch_assoc($resultST)) {
    $queryHT = "SELECT noTrans, Tgl, JenisDonor, Pengambilan, Diambil, petugas, umur, donorbaru, donorke, jk, 
    -- CONCAT(DATE(Tgl), ' ',CASE WHEN LENGTH(jam_ambil) = 5 THEN CONCAT(jam_ambil, ':00') ELSE jam_ambil END) AS tglAftap 
    STR_TO_DATE(
        CONCAT(
            DATE(Tgl), ' ',
            CASE 
                WHEN LENGTH(jam_ambil) = 5 THEN CONCAT(jam_ambil, ':00')
                ELSE jam_ambil
            END
        ),
        '%Y-%m-%d %H:%i:%s'
    ) AS tglAftap
    FROM htransaksi 
    WHERE NoKantong = '$nomor'";
    $resultHT = mysqli_query($dbi, $queryHT);

    if (!$resultHT) {
        die("Query htransaki failed or error: " . mysqli_error($dbi));
    }

    $ht = mysqli_fetch_assoc($resultHT);
    if (!$ht) {
        die("Tidak ditemukkan data NoKantong pada htransaksi: " . $nomor);
    }

    $kodeDonor = isset($row['KodePendonor']) ? $row['KodePendonor'] : '-';
    $jenisKtg = isset($row['jenis']) ? $row['jenis'] : '-';
    $golDarah = isset($row['gol_darah']) ? $row['gol_darah'] : '-';
    $rhesus = isset($row['rhesus']) ? $row['rhesus'] : '-';
    $produk = isset($row['produk']) ? $row['produk'] : '-';
    $stts = isset($row['Status']) ? $row['Status'] : '-';
    $posisi = isset($row['position']) ? $row['position'] : '-';
    $sah = isset($row['sah']) ? $row['sah'] : '-';
    $merk = isset($row['merk']) ? $row['merk'] : '-';
    $rhesus = isset($row['RhesusDrh']) ? $row['RhesusDrh'] : '-';
    $berat = isset($row['berat']) ? $row['berat'] : '0';
    $volktg = isset($row['volume']) ? $row['volume'] : '0';
    $lamaAmbil = isset($row['lama_pengambilan']) ? $row['lama_pengambilan'] : '0';

    $noTransAftap = isset($ht['noTrans']) ? $ht['noTrans'] : '-';
    $tglAftap = isset($ht['tglAftap']) ? $ht['tglAftap'] : null;
    $jenisDonor = isset($ht['JenisDonor']) ? $ht['JenisDonor'] : '-';
    $pengambilan = isset($ht['Pengambilan']) ? $ht['Pengambilan'] : '-';
    $volAmbil = isset($ht['Diambil']) ? $ht['Diambil'] : '0';
    $ptgs = isset($ht['petugas']) ? $ht['petugas'] : '-';
    $umur = isset($ht['umur']) ? $ht['umur'] : '0';
    $donorbaru = isset($ht['donorbaru']) ? $ht['donorbaru'] : '0';
    $donorKe = isset($ht['donorke']) ? $ht['donorke'] : '-';
    $jk = isset($ht['jk']) ? $ht['jk'] : '-';

    mysqli_free_result($resultHT);

} else {


    if (!empty($_POST['multi']) && is_array($_POST['multi'])) {
        $kantongs = $_POST['multi'];

        // UNTUK PHP 5.4++
        $placeholders = implode(',', array_fill(0, count($kantongs), '?'));
        $types = str_repeat('i', count($kantongs)); // asumsikan dst_id adalah integer

        $selUpdate = "UPDATE serahterima_detail SET dst_sah = ? WHERE dst_iddetail IN ($placeholders)";
        $stmtUpdate = $dbi->prepare($selUpdate);

        if (!$stmtUpdate) {
            die("Prepare update temp failed: {$dbi->error}");
        }

        // gabungkan semua parameter
        $params = array_merge(array($sahST), $kantongs);
        $bindTypes = 'i' . $types; // 'i' untuk dst_sah, lalu 'i...' untuk dst_id

        // konversi array ke referensi untuk call_user_func_array
        $bindNames = array();
        $bindNames[] = $bindTypes;
        foreach ($params as $key => $value) {
            $bindNames[] = &$params[$key];
        }

        call_user_func_array(array($stmtUpdate, 'bind_param'), $bindNames);

        if ($stmtUpdate->execute()) {
            // echo "Field dst_shift_penerima berhasil diperbarui untuk NoKantong: $nomor";
            // error_log("Field dst_shift_penerima berhasil diperbarui untuk NoKantong: $nomor");
        } else {
            // error_log("Error executing update temp: {$stmtUpdate->error}");
            // echo "Error executing update temp: {$stmtUpdate->error}";
        }
        $stmtUpdate->close();

        // UNTUK PHP 5.3
        // $placeholders = implode(',', array_fill(0, count($kantongs), '?'));
        // $types = str_repeat('i', count($kantongs));
        // $sql = "DELETE FROM serahterima_detail_tmp WHERE dst_id IN ($placeholders)";
        // $stmt = $dbi->prepare($sql);

        // $stmtUpdate->close();

        echo json_encode(array("status" => "ok", "updated" => count($kantongs)));
    } else {
        // die("Data tidak ditemukan untuk NoKantong: " . $nomor);
    }

}

mysqli_free_result($resultST);

if (!empty($nomor)) {

    if (empty($nomor) || !preg_match('/^[a-zA-Z0-9]+$/', $nomor)) {
        die("Invalid input for NoKantong: " . htmlspecialchars($nomor));
    }

    $selTemp = "SELECT `dst_no_aftap`, `dst_tglaftap`, `dst_notrans`, `dst_nokantong`, `dst_produk` FROM serahterima_detail WHERE dst_nokantong = ?";
    if (!is_string($nomor)) {
        die("Invalid data type for nomor. Expected a string.");
    }
    $stmtTemp = $dbi->prepare($selTemp);
    if (!$stmtTemp) {
        die("Prepare failed: {$dbi->error}");
    }
    $stmtTemp->bind_param("s", $nomor);
    if (!$stmtTemp) {
        die("Prepare select temp failed: {$dbi->error}");
    }
    // $stmtTemp->bind_param("s", $nomor);
    if ($stmtTemp->execute()) {
        $stmtTemp->store_result();
        if ($stmtTemp->num_rows > 0) {
            $stmtTemp->bind_result($noAftap, $tglAftap, $noTrans, $noKantong, $produk);
            while ($stmtTemp->fetch()) {
            }
            $updateTemp = "UPDATE serahterima_detail SET dst_sah  = 1 WHERE dst_nokantong = ?";
            $stmtUpdate = $dbi->prepare($updateTemp);
            if (!$stmtUpdate) {
                die("Prepare update temp failed: {$dbi->error}");
            }
            $stmtUpdate->bind_param("s", $nomor);
            if ($stmtUpdate->execute()) {
                echo "Field dst_shift_penerima berhasil diperbarui untuk NoKantong: $nomor";
                error_log("Field dst_shift_penerima berhasil diperbarui untuk NoKantong: $nomor");
            } else {
                error_log("Error executing update temp: {$stmtUpdate->error}");
                echo "Error executing update temp: {$stmtUpdate->error}";
            }
            $stmtUpdate->close();

            //echo "Data already exists in serahterima_detail_tmp for NoKantong: $nomor";
            //error_log("Duplicate data found in serahterima_detail_tmp for NoKantong: $nomor");
        } else {
            echo "No duplicate data found in serahterima_detail_tmp for NoKantong: $nomor";
            error_log("Error: Failed to execute select temp query: {$dbi->error}");
        }
    } else {
        error_log("Error executing select temp: {$dbi->error}");
        echo "Error executing select temp: {$dbi->error}";
    }
    $stmtTemp->close();


    // $insST = "INSERT INTO serahterima_detail (`dst_no_aftap`, `dst_tglaftap`, `dst_notrans`, `dst_nokantong`, `dst_produk`, `dst_statusktg`, `st_statusktg_new`, `dst_old_position`, `dst_new_position`, `dst_sahktg`, `dst_sahktg_new`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_kodedonor`, `dst_berat`, `dst_volumektg`, `dst_jenisktg`, `dst_sample`, `dst_sah`, `dst_dsdp`, `dst_lamabaru`, `dst_umur`, `dst_lama_aftap`, `dst_statuspengambilan`, `dst_kel`, `dst_ptgaftap`, `dst_volambil`, `dst_receive1`, `dst_stat_receive1`, `dst_date_receive1`, `dst_shift_receive1`, `simltd`, `skgd`, `snat`, `packing`, `label`, `splasma`, `sserum`, `swb`, `volket`, `lisis`, `dokumen`, `infoklinis`, `up_data`, `dst_donasi`, `dst_samplejml`) 
    //         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    // $stmtST = $dbi->prepare($sql);
    // if (!$stmtST) {
    //     die("Prepare inst ST failed: {$dbi->error}");
    // }
    // $stmtST->bind_param("sssssssssssssssssssssssssssssssssssssssssssssss", $noTransAftap, $tglAftap, $nT, $nomor, $produk, $stts, $sttsNew, $posisi, $posisiNew, $sah, $sahNew, $merk, $golDarah, $rhesus, $kodeDonor, $berat, $volktg, $jenisKtg, $sampleST, $sahST, $dsdp, $lamaBaru, $umur, $lamaAftap, $statusPengambilan, $kel, $ptgsAftap, $volAmbil, $receive1, $statReceive1, $dateReceive1, $shiftReceive1, $simltd, $skgd, $snat, $packing, $label, $splasma, $sserum, $swb, $volket, $lisis, $dokumen, $infoklinis, $upData, $donasi, $sampleJml);

    // if ($stmtMT->execute()) {
    //     echo "Data berhasil disimpan.";
    //     error_log("Data berhasil disimpan: $nT");
    // } else {
    //     error_log("Error: {$stmt->error}");
    //     echo "Error: {$stmt->error}";
    // }

    // $stmtMT->close();

    // $sql = "INSERT INTO serahterima_detail_tmp (`dst_asal`, `dst_no_aftap`, `dst_tglaftap`, `dst_kodealat`, `dst_suhu`, `dst_keadaan`, `dst_notrans`, `dst_nokantong`, `dst_produk`, `dst_statusktg`, `dst_old_position`, `dst_sahktg`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_kodedonor`, `dst_berat`, `dst_volumektg`, `dst_jenisktg`, `dst_sample`, `dst_sah`, `dst_modul`, `dst_user`, `dst_dsdp`, `dst_lamabaru`, `dst_umur`, `dst_lama_aftap`, `dst_statuspengambilan`, `dst_kel`, `dst_ptgaftap`, `dst_volambil`, `dst_shift_pengirim`) 
    //         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // $stmt = $dbi->prepare($sql);
    // if (!$stmt) {
    //     die("Prepare failed: {$dbi->error}");
    // }
    // $stmt->bind_param("ssssssssssisssssssssissssissssss", $bagKirim, $noTransAftap, $tglAftap, $kodeAlat, $suhuPengiriman, $keadaan, $nT, $nomor, $produk, $stts, $posisi, $sah, $merk, $golDarah, $rhesus, $kodeDonor, $berat, $volktg, $jenisKtg, $sampleST, $sahST, $modulST, $iPetugas, $jenisDonor, $donorbaru, $umur, $lamaAmbil, $pengambilan, $jk, $ptgs, $volAmbil, $shiftST);

    // if ($stmt->execute()) {
    //     echo "Data berhasil disimpan.";
    //     error_log("Data berhasil disimpan: $nT");
    // } else {
    //     error_log("Error: {$stmt->error}");
    //     echo "Error: {$stmt->error}";
    // }

    // $stmt->close();
}

$dbi->close();
