<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');
session_start();
include "../config/dbi_connect.php";

$nomor = $_POST['nomor_kantong'];

if (strlen($nomor) > 0) {
    $tanpaSatelite = substr($nomor, 0, -1);
} else {
    echo json_encode(array("status" => "error", "message" => "Nomor kantong tidak valid."));
    exit;
}

$iPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : "irawanDB";
//error_log('User dari ajax tambahDataPengiriman saat ini adalah: ' . $iPetugas);

$d = date('d');
$m = date('m');
$y = substr(date('Y'), 2, 2);

$jamSekarang = date('H:i');

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
$bagKirim = "KOMPONEN";
$jenisKirim = "Kantong (Produk Darah)";
$bagTerima = "RILIS";
$peruntukan = "POSTING HASIL PENGOLAHAN DARAH";
$kodeAlat = isset($_POST['kode_alat']) ? $_POST['kode_alat'] : '-';
$suhuPengiriman = isset($_POST['suhu_pengiriman']) ? $_POST['suhu_pengiriman'] : '-';
$keadaan = isset($_POST['keadaan']) ? $_POST['keadaan'] : '-';
$sampleST = "0";
$sahST = "0";
$modulST = "KOMPONEN";
// $shiftST = "I";
// $tujuan = "3509";

// cek dulu apakah sudah ada transaksi untuk user ini
$q_cek_trans = mysqli_query($dbi, "SELECT dst_notrans FROM serahterima_detail_tmp WHERE dst_user = '$iPetugas' ORDER BY dst_id ASC LIMIT 1");
$row_cek_trans = mysqli_fetch_assoc($q_cek_trans);

if ($row_cek_trans && isset($row_cek_trans['dst_notrans'])) {
    // Jika sudah ada, gunakan dst_notrans yang sudah ada
    $nT = $row_cek_trans['dst_notrans'];
} else {
    //------------------------ set Nomor Transaksi Pengiriman ------------------------->
// digit pasien 19 digit, 2 digit CS (Collecting Site), 4 digit wilayah/id udd, 1 digit '-', 6 digit tanggal bulan tahun, 1 digit '-', 4 sequence,
    $q_wilayah = mysqli_query($dbi, "SELECT `id` FROM utd WHERE `aktif` = 1");
    $area = mysqli_fetch_assoc($q_wilayah);
    $kdtp = "KR" . $d . $m . $y . "-" . $area['id'] . "-";
    $idp = mysqli_query($dbi, "SELECT `dst_notrans` FROM serahterima_detail_tmp WHERE `dst_notrans` LIKE '$kdtp%' AND  `dst_user` = '$iPetugas' ORDER BY `dst_notrans` DESC");
    $idp1 = mysqli_fetch_assoc($idp);
    if ($idp1 && isset($idp1['dst_notrans'])) {
        $idp2 = substr($idp1['dst_notrans'], 15, 4);
    } else {
        $idp2 = "000";
    }
    // $idp2       = substr($idp1['kd_pasien'], 6, 5);
    if ($idp2 < 1) {
        $idp2 = "000";
    }
    $int_idp2 = (int) $idp2 + 1;
    $j_nol1 = 4 - (strlen(strval($int_idp2)));
    $idp4 = "";
    for ($i = 0; $i < $j_nol1; $i++) {
        $idp4 .= "0";
    }
    $nT = $kdtp . $idp4 . $int_idp2;
    //---------------------- END set Nomor Transaksi ------------------------->

}

// SELEKSI apabil terdapat data ditemporary yang belum diselesaikan oleh user yang sama
$query = "SELECT dst_notrans FROM serahterima_detail_tmp WHERE dst_user = '$iPetugas' ORDER BY dst_id ASC LIMIT 1";
$result = mysqli_query($dbi, $query);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
}

$queryST = "SELECT jenis, `Status`, `position`, produk, sah, merk, gol_darah, RhesusDrh, KodePendonor, '0' AS berat, volume, lama_pengambilan  FROM stokkantong WHERE noKantong = '$nomor'";
$resultST = mysqli_query($dbi, $queryST);

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
    WHERE NoKantong LIKE '$tanpaSatelite%' ORDER BY Tgl DESC LIMIT 1";
    $resultHT = mysqli_query($dbi, $queryHT);
    if (!$resultHT) {
        echo json_encode(array("status" => "error", "message" => "QUERY GAGAL."));
        exit;
    }
    $ht = mysqli_fetch_assoc($resultHT);
    if (!$ht) {
        echo json_encode(array("status" => "error", "message" => "Nomor kantong tidak terdaftar didalam htransaksi."));
        exit;
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
} else {
    error_log("Nomor kantong tidak ditemukan di dalam tabel stok kantong.");
    echo json_encode(array("status" => "error", "message" => "Nomor kantong tidak terdaftar didalam sistem."));
    exit;
}

mysqli_free_result($resultST);
mysqli_free_result($resultHT);

if (!empty($nomor)) {

    $selStkk = "SELECT COUNT(*) AS jumlah FROM stokkantong WHERE noKantong = ?";
    $stmtStkk = $dbi->prepare($selStkk);
    if (!$stmtStkk) {
        echo json_encode(array("status" => "error", "message" => "Prepare failed: {$dbi->error}"));
        exit;
    }
    $stmtStkk->bind_param("s", $nomor);
    $stmtStkk->execute();
    // $resultStkk = $stmtStkk->get_result();
    // $rowStkk = $resultStkk->fetch_assoc();
    $stmtStkk->bind_result($jumlahStok);
    $stmtStkk->fetch();
    $stmtStkk->close();
    if ($jumlahStok == 0) {
        echo json_encode(array("status" => "error", "message" => "Nomor kantong tidak ditemukan di dalam tabel stok kantong."));
        exit;
    }

    $checkQueryTmp = "SELECT COUNT(*) AS count FROM serahterima_detail_tmp WHERE dst_nokantong = ?";
    $checkStmtTmp = $dbi->prepare($checkQueryTmp);
    if (!$checkStmtTmp) {
        echo json_encode(array("status" => "error", "message" => "Prepare failed: {$dbi->error}"));
        exit;
    }
    $checkStmtTmp->bind_param("s", $nomor);
    $checkStmtTmp->execute();
    // $checkResultTmp = $checkStmtTmp->get_result();
    // $rowTmp = $checkResultTmp->fetch_assoc();
    $checkStmtTmp->bind_result($countTmp);
    $checkStmtTmp->fetch();
    $checkStmtTmp->close();

    if ($countTmp> 0) {
        echo json_encode(array("status" => "error", "message" => "Nomor kantong sudah tersedia di dalam daftar Antrian Serah Terima"));
        exit;
    }

    // Check if the nomor_kantong already exists in serahterima_detail
    $checkQueryDetail = "SELECT COUNT(*) AS count FROM serahterima_detail WHERE dst_nokantong = ?";
    $checkStmtDetail = $dbi->prepare($checkQueryDetail);
    if (!$checkStmtDetail) {
        echo json_encode(array("status" => "error", "message" => "Prepare failed: {$dbi->error}"));
        exit;
    }
    $checkStmtDetail->bind_param("s", $nomor);
    $checkStmtDetail->execute();
    // $checkResultDetail = $checkStmtDetail->get_result();
    // $rowDetail = $checkResultDetail->fetch_assoc();
    $checkStmtDetail->bind_result($countDetail);
    $checkStmtDetail->fetch();
    $checkStmtDetail->close();

    if ($countDetail > 0) {
        echo json_encode(array("status" => "error", "message" => "Nomor kantong sudah pernah dilakukan Serah Terima."));
        exit;
    }

    $sql = "INSERT INTO serahterima_detail_tmp (`dst_asal`, `dst_no_aftap`, `dst_tglaftap`, `dst_kodealat`, `dst_suhu`, `dst_keadaan`, `dst_notrans`, `dst_nokantong`, `dst_produk`, `dst_statusktg`, `dst_old_position`, `dst_sahktg`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_kodedonor`, `dst_berat`, `dst_volumektg`, `dst_jenisktg`, `dst_sample`, `dst_sah`, `dst_modul`, `dst_user`, `dst_dsdp`, `dst_lamabaru`, `dst_umur`, `dst_lama_aftap`, `dst_statuspengambilan`, `dst_kel`, `dst_ptgaftap`, `dst_volambil`, `dst_shift_pengirim`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $dbi->prepare($sql);
    if (!$stmt) {
        echo json_encode(array("status" => "error", "message" => "Prepare failed: {$dbi->error}"));
        exit;
    }

    $stmt->bind_param("ssssssssssisssssssssissssissssss", $bagKirim, $noTransAftap, $tglAftap, $kodeAlat, $suhuPengiriman, $keadaan, $nT, $nomor, $produk, $stts, $posisi, $sah, $merk, $golDarah, $rhesus, $kodeDonor, $berat, $volktg, $jenisKtg, $sampleST, $sahST, $modulST, $iPetugas, $jenisDonor, $donorbaru, $umur, $lamaAmbil, $pengambilan, $jk, $ptgs, $volAmbil, $shiftST);

    if ($stmt->execute()) {
        echo json_encode(array("status" => "success", "message" => "Data berhasil disimpan."));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error: {$stmt->error}"));
    }

    $stmt->close();
}

$dbi->close();
