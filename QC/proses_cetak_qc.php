<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/db_connect.php');

if (!isset($_POST['selected_ids']) || $_POST['selected_ids'] == '') {
    die('Tidak ada data yang dipilih.');
}

$selected_ids     = $_POST['selected_ids'];
$pakai_sertifikat = isset($_POST['pakai_sertifikat']) ? $_POST['pakai_sertifikat'] : '0';
$no_sertifikat    = isset($_POST['no_sertifikat']) ? trim($_POST['no_sertifikat']) : '';
$tgl_sertifikat   = isset($_POST['tgl_sertifikat']) ? trim($_POST['tgl_sertifikat']) : '';

$tgl_jam_sertifikat = $tgl_sertifikat . ' ' . date('H:i:s');

$id_array = explode(',', $selected_ids);

foreach ($id_array as $id) {
    $id = trim($id);
    if ($id == '') continue;

    $q = mysql_query("
        SELECT rq.id, rq.nokantong
        FROM registrasi_qc rq
        WHERE rq.id = '" . mysql_real_escape_string($id) . "'
        LIMIT 1
    ") or die(mysql_error());

    if (mysql_num_rows($q) == 0) {
        continue;
    }

    $row = mysql_fetch_assoc($q);
    $nokantong = trim($row['nokantong']);

    if ($nokantong == '') {
        continue;
    }

    $cek = mysql_query("
        SELECT id
        FROM qc
        WHERE nokantong = '" . mysql_real_escape_string($nokantong) . "'
        LIMIT 1
    ") or die(mysql_error());

    if (mysql_num_rows($cek) > 0) {
        if ($pakai_sertifikat == '1') {
            mysql_query("
                UPDATE qc
                SET
                    nosurat = '" . mysql_real_escape_string($no_sertifikat) . "',
                    tglsurat = '" . mysql_real_escape_string($tgl_jam_sertifikat) . "'
                WHERE nokantong = '" . mysql_real_escape_string($nokantong) . "'
            ") or die(mysql_error());
        }
    }
}

header("Location: cetak_qc.php?ids=" . urlencode($selected_ids) . "&pakai_sertifikat=" . urlencode($pakai_sertifikat));
exit;