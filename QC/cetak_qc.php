<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/db_connect.php');

if (!isset($_GET['ids']) || trim($_GET['ids']) == '') {
    die('Tidak ada data yang dipilih.');
}

$selected_ids = trim($_GET['ids']);
$pakai_sertifikat = isset($_GET['pakai_sertifikat']) ? trim($_GET['pakai_sertifikat']) : '0';

$id_array = explode(',', $selected_ids);
$id_clean = array();

foreach ($id_array as $id) {
    $id = trim($id);
    if ($id !== '') {
        $id_clean[] = "'" . mysql_real_escape_string($id) . "'";
    }
}

if (count($id_clean) == 0) {
    die('Data tidak valid.');
}

$where_ids = implode(',', $id_clean);

$sql_data = mysql_query("
    SELECT
        rq.id,
        rq.nokantong,
        rq.produk,
        rq.asal_utd,
        u.nama AS nama_utd,
        q.nosurat,
        q.tglsurat
    FROM registrasi_qc rq
    LEFT JOIN utd u ON u.id = rq.asal_utd
    LEFT JOIN qc q ON q.nokantong = rq.nokantong
    WHERE rq.id IN ($where_ids)
    ORDER BY rq.id ASC
") or die(mysql_error());

if (mysql_num_rows($sql_data) == 0) {
    die('Data tidak ditemukan.');
}

$first_row = mysql_fetch_assoc($sql_data);
$produk_awal = strtoupper(trim($first_row['produk']));

mysql_data_seek($sql_data, 0);

if (
    $produk_awal == 'PRC' ||
    $produk_awal == 'PRC 450' ||
    $produk_awal == 'PRC LEUCODEPLETED' ||
    $produk_awal == 'PRC LEUCOREDUCTION' ||
    $produk_awal == 'WB 450' ||
    $produk_awal == 'WB' ||
    $produk_awal == 'TC' ||
    $produk_awal == 'TC AFERESIS' ||
    $produk_awal == 'AHF' ||
    $produk_awal == 'FFP' ||
    $produk_awal == 'PLASMA AFERESIS'
) {
    header("Location: template_cetak_qc.php?ids=" . urlencode($selected_ids) . "&pakai_sertifikat=" . urlencode($pakai_sertifikat));
    exit;
}

die('Template cetak untuk produk "' . htmlspecialchars($produk_awal, ENT_QUOTES) . '" belum dibuat.');