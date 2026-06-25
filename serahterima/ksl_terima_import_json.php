<?php

/**
 * ksl_terima_import_json.php
 * Backend: Terima upload file JSON konsolidasi (via Download),
 * parse, lalu simpan ke tabel staging lokal.
 * Kompatibel PHP 5.3+
 *
 * CHANGELOG:
 *  - Tambah kolom `raw_json` MEDIUMTEXT pada tabel staging ksl_import_antrian
 *    agar seluruh isi JSON (termasuk stokkantong, pendonor, htransaksi)
 *    tersimpan dan bisa dibaca kembali oleh ksl_terima_proses.php.
 */
session_start();
require_once('../config/dbi_connect.php');
header('Content-Type: application/json');

// ── Auto-create tabel staging jika belum ada ──────────────────────────────────
mysqli_query($dbi, "CREATE TABLE IF NOT EXISTS `ksl_import_antrian` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `notrans`        VARCHAR(60)   NOT NULL DEFAULT '',
    `hst_tgl`        VARCHAR(30)   DEFAULT '',
    `udd_asal_id`    VARCHAR(20)   DEFAULT '',
    `udd_asal_nama`  VARCHAR(150)  DEFAULT '',
    `udd_penerima`   VARCHAR(20)   DEFAULT '',
    `hst_asal`       VARCHAR(100)  DEFAULT '',
    `jumlahA`        INT           DEFAULT 0,
    `jumlahB`        INT           DEFAULT 0,
    `jumlahO`        INT           DEFAULT 0,
    `jumlahAB`       INT           DEFAULT 0,
    `jumlah`         INT           DEFAULT 0,
    `status`         TINYINT       DEFAULT 0,
    `tgl_import`     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `imported_by`    VARCHAR(50)   DEFAULT '',
    `raw_json`       MEDIUMTEXT    DEFAULT NULL,
    UNIQUE KEY `uk_notrans` (`notrans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

// Jika tabel sudah ada tapi kolom raw_json belum ada (upgrade), tambahkan
$colCheck = mysqli_query($dbi, "SHOW COLUMNS FROM `ksl_import_antrian` LIKE 'raw_json'");
if (mysqli_num_rows($colCheck) == 0) {
    mysqli_query($dbi, "ALTER TABLE `ksl_import_antrian` ADD COLUMN `raw_json` MEDIUMTEXT DEFAULT NULL");
}

mysqli_query($dbi, "CREATE TABLE IF NOT EXISTS `ksl_import_antrian_detail` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `notrans`        VARCHAR(60)  NOT NULL DEFAULT '',
    `dst_nokantong`  VARCHAR(50)  DEFAULT '',
    `dst_tglaftap`   VARCHAR(20)  DEFAULT '',
    `dst_kodedonor`  VARCHAR(30)  DEFAULT '',
    `dst_merk`       VARCHAR(50)  DEFAULT '',
    `dst_volambil`   VARCHAR(10)  DEFAULT '',
    `dst_golda`      VARCHAR(5)   DEFAULT '',
    `dst_rh`         VARCHAR(5)   DEFAULT '',
    INDEX `idx_notrans` (`notrans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
// ─────────────────────────────────────────────────────────────────────────────

// Validasi: harus ada file
if (!isset($_FILES['jsonFile']) || $_FILES['jsonFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('status' => 'error', 'message' => 'File tidak ditemukan atau gagal diupload.'));
    exit;
}

// Baca & decode JSON
$jsonContent = file_get_contents($_FILES['jsonFile']['tmp_name']);
$jsonData    = json_decode($jsonContent, true);

if (!$jsonData || !isset($jsonData['notransaksi'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Format file JSON tidak valid atau bukan file konsolidasi.'));
    exit;
}

$notrans      = mysqli_real_escape_string($dbi, $jsonData['notransaksi']);
$udd_asal_id  = mysqli_real_escape_string($dbi, isset($jsonData['udd_asal'])     ? $jsonData['udd_asal']     : '');
$udd_penerima = mysqli_real_escape_string($dbi, isset($jsonData['udd_penerima']) ? $jsonData['udd_penerima'] : '');
$sr           = isset($jsonData['serahterima'])       ? $jsonData['serahterima']       : array();
$srd          = isset($jsonData['serahterimadetail']) ? $jsonData['serahterimadetail'] : array();
$imported_by  = mysqli_real_escape_string($dbi, isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '');

// Simpan seluruh isi JSON mentah (untuk dipakai ulang saat proses)
$raw_json_esc = mysqli_real_escape_string($dbi, $jsonContent);

// Ambil nama UDD asal dari tabel utd (jika ada), fallback ke field hst_dariudd
$udd_asal_nama = '';
if ($udd_asal_id !== '') {
    $qryUdd = mysqli_query($dbi, "SELECT `nama` FROM `utd` WHERE `id`='$udd_asal_id' LIMIT 1");
    if ($rowUdd = mysqli_fetch_assoc($qryUdd)) {
        $udd_asal_nama = $rowUdd['nama'];
    }
}
if ($udd_asal_nama === '' && isset($sr['hst_dariudd'])) {
    $udd_asal_nama = $sr['hst_dariudd'];
}
$udd_asal_nama = mysqli_real_escape_string($dbi, $udd_asal_nama);

$hst_tgl  = mysqli_real_escape_string($dbi, isset($sr['hst_tgl'])  ? $sr['hst_tgl']  : '');
$hst_asal = mysqli_real_escape_string($dbi, isset($sr['hst_asal']) ? $sr['hst_asal'] : '');

// Hitung jumlah per golda dari detail
$jumlahA  = 0;
$jumlahB  = 0;
$jumlahO  = 0;
$jumlahAB = 0;
foreach ($srd as $d) {
    $golda = strtoupper(trim(isset($d['dst_golda']) ? $d['dst_golda'] : ''));
    if ($golda === 'A') {
        $jumlahA++;
    } elseif ($golda === 'B') {
        $jumlahB++;
    } elseif ($golda === 'O') {
        $jumlahO++;
    } elseif ($golda === 'AB') {
        $jumlahAB++;
    }
}
$jumlah = $jumlahA + $jumlahB + $jumlahO + $jumlahAB;

// Cek duplikat
$cek      = mysqli_query($dbi, "SELECT `id`,`status` FROM `ksl_import_antrian` WHERE `notrans`='$notrans' LIMIT 1");
$existRow = mysqli_fetch_assoc($cek);

if ($existRow) {
    if ($existRow['status'] == 1) {
        echo json_encode(array('status' => 'error', 'message' => "No. Transaksi $notrans sudah pernah diproses sebelumnya."));
        exit;
    }
    // UPDATE: timpa data lama termasuk raw_json
    mysqli_query($dbi, "UPDATE `ksl_import_antrian` SET
        `hst_tgl`       = '$hst_tgl',
        `udd_asal_id`   = '$udd_asal_id',
        `udd_asal_nama` = '$udd_asal_nama',
        `udd_penerima`  = '$udd_penerima',
        `hst_asal`      = '$hst_asal',
        `jumlahA`       = $jumlahA,
        `jumlahB`       = $jumlahB,
        `jumlahO`       = $jumlahO,
        `jumlahAB`      = $jumlahAB,
        `jumlah`        = $jumlah,
        `tgl_import`    = NOW(),
        `imported_by`   = '$imported_by',
        `raw_json`      = '$raw_json_esc'
    WHERE `notrans` = '$notrans'");
    mysqli_query($dbi, "DELETE FROM `ksl_import_antrian_detail` WHERE `notrans`='$notrans'");
} else {
    // INSERT baru, sertakan raw_json
    mysqli_query($dbi, "INSERT INTO `ksl_import_antrian`
        (`notrans`,`hst_tgl`,`udd_asal_id`,`udd_asal_nama`,`udd_penerima`,
         `hst_asal`,`jumlahA`,`jumlahB`,`jumlahO`,`jumlahAB`,`jumlah`,`imported_by`,`raw_json`)
    VALUES
        ('$notrans','$hst_tgl','$udd_asal_id','$udd_asal_nama','$udd_penerima',
         '$hst_asal',$jumlahA,$jumlahB,$jumlahO,$jumlahAB,$jumlah,'$imported_by','$raw_json_esc')");
}

if (mysqli_error($dbi)) {
    echo json_encode(array('status' => 'error', 'message' => 'Gagal simpan header: ' . mysqli_error($dbi)));
    exit;
}

// Insert detail (untuk keperluan preview / listdetail)
$inserted = 0;
foreach ($srd as $d) {
    $nokantong = mysqli_real_escape_string($dbi, isset($d['dst_nokantong']) ? $d['dst_nokantong'] : '');
    $tglaftap  = mysqli_real_escape_string($dbi, isset($d['dst_tglaftap'])  ? $d['dst_tglaftap']  : '');
    $kodedonor = mysqli_real_escape_string($dbi, isset($d['dst_kodedonor']) ? $d['dst_kodedonor'] : '');
    $merk      = mysqli_real_escape_string($dbi, isset($d['dst_merk'])      ? $d['dst_merk']      : '');
    $volambil  = mysqli_real_escape_string($dbi, isset($d['dst_volambil'])  ? $d['dst_volambil']  : '');
    $golda     = mysqli_real_escape_string($dbi, isset($d['dst_golda'])     ? $d['dst_golda']     : '');
    $rh        = mysqli_real_escape_string($dbi, isset($d['dst_rh'])        ? $d['dst_rh']        : '');

    if ($nokantong === '') continue;

    mysqli_query($dbi, "INSERT INTO `ksl_import_antrian_detail`
        (`notrans`,`dst_nokantong`,`dst_tglaftap`,`dst_kodedonor`,`dst_merk`,`dst_volambil`,`dst_golda`,`dst_rh`)
    VALUES
        ('$notrans','$nokantong','$tglaftap','$kodedonor','$merk','$volambil','$golda','$rh')");
    $inserted++;
}

echo json_encode(array(
    'status'  => 'success',
    'message' => "Data berhasil diimport. $inserted kantong ditemukan.",
    'notrans' => $notrans,
    'jumlah'  => $jumlah
));
exit;
