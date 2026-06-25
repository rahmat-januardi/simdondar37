<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json; charset=utf-8');

include_once dirname(__FILE__) . '/../config/db_connect.php';

if (empty($_SESSION['namauser'])) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Session login tidak ditemukan. Silakan login ulang.'
    ));
    exit;
}

$namauser = $_SESSION['namauser'];

function getJenisLabel($jenis)
{
    $map = array(
        '1' => 'Single',
        '2' => 'Double',
        '3' => 'Triple',
        '4' => 'Quadruple',
        '6' => 'Pediatrik'
    );

    $jenis = trim((string)$jenis);
    return isset($map[$jenis]) ? $map[$jenis] : $jenis;
}

function clean($value)
{
    return mysql_real_escape_string(trim($value));
}

$merk = isset($_POST['merk']) ? trim($_POST['merk']) : '';
if ($merk === 'lainnya') {
    $merk = isset($_POST['merk_lainnya']) ? trim($_POST['merk_lainnya']) : '';
}

$asal = isset($_POST['asal_sampel']) ? trim($_POST['asal_sampel']) : '';
if ($asal === 'lainnya') {
    $asal = isset($_POST['asal_sampel_lainnya']) ? trim($_POST['asal_sampel_lainnya']) : '';
}

$nokantong = isset($_POST['nokantong']) ? $_POST['nokantong'] : '';
$nokantong = preg_replace("/[^A-Za-z0-9]/", "", strtoupper($nokantong));

$produk   = isset($_POST['produk']) ? $_POST['produk'] : '';
$volume   = isset($_POST['volume']) ? $_POST['volume'] : '';
$goldarah = isset($_POST['goldarah']) ? $_POST['goldarah'] : '';
$rhesus   = isset($_POST['rh']) ? $_POST['rh'] : '';
$tglaftap = isset($_POST['tglaftap']) ? $_POST['tglaftap'] : '';
$tglkad   = isset($_POST['tglkad']) ? $_POST['tglkad'] : '';
$tglolah  = isset($_POST['tglolah']) ? $_POST['tglolah'] : '';
$pengirim = isset($_POST['pengirim']) ? $_POST['pengirim'] : '';
$jenis2   = isset($_POST['jenis2']) ? $_POST['jenis2'] : '';

if ($nokantong === '') {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'No kantong tidak boleh kosong.'
    ));
    exit;
}

$cek = mysql_query("SELECT id FROM registrasi_luarqc_temp WHERE nokantong='" . clean($nokantong) . "' AND user_input='" . clean($namauser) . "' LIMIT 1");
if (!$cek) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Query cek gagal: ' . mysql_error()
    ));
    exit;
}

if (mysql_num_rows($cek) > 0) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'No kantong sudah ada di tabel sementara.'
    ));
    exit;
}

$sql = "INSERT INTO registrasi_luarqc_temp
        (nokantong, produk, volume, goldarah, rhesus, tglaftap, tgl_pengolahan, kadaluwarsa, pengirim, merk, jenis, asal_utd, user_input)
        VALUES
        ('" . clean($nokantong) . "',
         '" . clean($produk) . "',
         '" . clean($volume) . "',
         '" . clean($goldarah) . "',
         '" . clean($rhesus) . "',
         '" . clean($tglaftap) . "',
         '" . clean($tglolah) . "',
         '" . clean($tglkad) . "',
         '" . clean($pengirim) . "',
         '" . clean($merk) . "',
         '" . clean($jenis2) . "',
         '" . clean($asal) . "',
         '" . clean($namauser) . "')";

$q = mysql_query($sql);
if (!$q) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Insert gagal: ' . mysql_error()
    ));
    exit;
}

$html = '';
$no = 1;

$list = mysql_query("SELECT * FROM registrasi_luarqc_temp WHERE user_input='" . clean($namauser) . "' ORDER BY id ASC");
if (!$list) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Query tampil gagal: ' . mysql_error()
    ));
    exit;
}

while ($d = mysql_fetch_assoc($list)) {
    $asalDisplay = !empty($d['nama_utd']) ? $d['nama_utd'] : $d['asal_utd'];

    $html .= "<tr>
        <td><input type='checkbox' name='pilih[]' value='" . htmlspecialchars($d['id']) . "'></td>
        <td>" . $no++ . "</td>
        <td>" . htmlspecialchars($d['nokantong']) . "</td>
        <td>" . htmlspecialchars($d['volume']) . "</td>
        <td>" . htmlspecialchars($d['merk']) . "</td>
        <td>" . htmlspecialchars(getJenisLabel($d['jenis'])) . "</td>
        <td>" . htmlspecialchars($asalDisplay) . "</td>
        <td>" . htmlspecialchars($d['produk']) . "</td>
        <td>" . htmlspecialchars($d['tglaftap']) . "</td>
        <td>" . htmlspecialchars($d['kadaluwarsa']) . "</td>
        <td>" . htmlspecialchars($d['tgl_pengolahan']) . "</td>
        <td>" . htmlspecialchars($d['goldarah']) . "</td>
        <td>" . htmlspecialchars($d['rhesus']) . "</td>
        <td>" . htmlspecialchars($d['pengirim']) . "</td>
    </tr>";
}

echo json_encode(array(
    'status' => 'success',
    'msg' => 'Data berhasil disimpan ke tabel sementara.',
    'html' => $html
));
exit;