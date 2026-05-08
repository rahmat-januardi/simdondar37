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

if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Tidak ada data yang dipilih.'
    ));
    exit;
}

$ids = array();
foreach ($_POST['ids'] as $id) {
    $id = (int)$id;
    if ($id > 0) {
        $ids[] = $id;
    }
}

if (empty($ids)) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'ID data tidak valid.'
    ));
    exit;
}

$idList = implode(',', $ids);

$del = mysql_query("
    DELETE FROM registrasi_luarqc_temp
    WHERE user_input='" . clean($namauser) . "'
    AND id IN ($idList)
");

if (!$del) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Delete gagal: ' . mysql_error()
    ));
    exit;
}

$html = '';
$no = 1;

$q = mysql_query("
    SELECT t.*, u.nama AS nama_utd
    FROM registrasi_luarqc_temp t
    LEFT JOIN utd u ON u.id = t.asal_utd
    WHERE t.user_input='" . clean($namauser) . "'
    ORDER BY t.id ASC
");

if (!$q) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Query tampil gagal: ' . mysql_error()
    ));
    exit;
}

while ($d = mysql_fetch_assoc($q)) {
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
    'msg' => 'Data berhasil dihapus.',
    'html' => $html
));
exit;