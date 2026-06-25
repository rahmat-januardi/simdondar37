<?php

/**
 * ksl_terima_hapus_dl.php
 * Hapus data import konsolidasi via Download dari tabel staging lokal.
 * Kompatibel PHP 5.3+
 */
session_start();
require_once('../config/dbi_connect.php');
header('Content-Type: application/json');

if (!isset($_POST['notrans']) || trim($_POST['notrans']) === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Parameter tidak lengkap.'));
    exit;
}

$notrans = mysqli_real_escape_string($dbi, trim($_POST['notrans']));

$cek = mysqli_query($dbi, "SELECT `id`,`status` FROM `ksl_import_antrian` WHERE `notrans`='$notrans' LIMIT 1");
$row = mysqli_fetch_assoc($cek);

if (!$row) {
    echo json_encode(array('status' => 'error', 'message' => 'Data tidak ditemukan.'));
    exit;
}
if ($row['status'] == 1) {
    echo json_encode(array('status' => 'error', 'message' => 'Data sudah diproses, tidak bisa dihapus.'));
    exit;
}

mysqli_query($dbi, "DELETE FROM `ksl_import_antrian_detail` WHERE `notrans`='$notrans'");
mysqli_query($dbi, "DELETE FROM `ksl_import_antrian`        WHERE `notrans`='$notrans'");

echo json_encode(array('status' => 'success', 'message' => "Data $notrans berhasil dihapus."));
exit;
