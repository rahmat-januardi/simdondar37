<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json; charset=utf-8');

include_once dirname(__FILE__) . '/../config/db_connect.php';

if (empty($_SESSION['namauser'])) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Session user tidak ditemukan.'
    ));
    exit;
}

function esc($val)
{
    return mysql_real_escape_string(trim($val));
}

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

function renderTempRows($namauser)
{
    $namauser = mysql_real_escape_string($namauser);
    $no = 1;
    $html = '';

    $q = mysql_query("
        SELECT t.*, u.nama AS nama_utd
        FROM registrasi_luarqc_temp t
        LEFT JOIN utd u ON u.id = t.asal_utd
        WHERE t.user_input='$namauser'
        ORDER BY t.id ASC
    ");

    if (!$q) {
        return '';
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

    return $html;
}

$namauser = esc($_SESSION['namauser']);

$q = mysql_query("
    SELECT r.*, u.nama AS nama_utd
    FROM registrasi_luarqc_temp r
    LEFT JOIN utd u ON u.id = r.asal_utd
    WHERE user_input = '$namauser'
    ORDER BY id ASC
");

if (!$q) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Query temp gagal: ' . mysql_error()
    ));
    exit;
}

if (mysql_num_rows($q) == 0) {
    echo json_encode(array(
        'status' => 'error',
        'msg' => 'Tidak ada data sementara untuk disimpan.'
    ));
    exit;
}

$berhasil = 0;
$gagalList = array();
$suksesIds = array();

while ($d = mysql_fetch_assoc($q)) {
    $nokantong = esc($d['nokantong']);

    $cek = mysql_query("SELECT 1 FROM registrasi_qc WHERE nokantong = '$nokantong' LIMIT 1");
    if (!$cek) {
        $gagalList[] = array(
            'nokantong' => $d['nokantong'],
            'alasan' => 'Gagal cek duplikat: ' . mysql_error()
        );
        continue;
    }

    if (mysql_num_rows($cek) > 0) {
        $gagalList[] = array(
            'nokantong' => $d['nokantong'],
            'alasan' => 'Sudah ada di registrasi QC'
        );
        continue;
    }

    $produk         = esc($d['produk']);
    $volume         = esc($d['volume']);
    $goldarah       = esc($d['goldarah']);
    $rhesus         = esc($d['rhesus']);
    $tglaftap       = esc($d['tglaftap']);
    $kadaluwarsa    = esc($d['kadaluwarsa']);
    $tgl_pengolahan = esc($d['tgl_pengolahan']);
    $petugas_terima = esc($_SESSION['namauser']);
    $petugas_serah  = esc($d['pengirim']);
    $asal_utd       = esc($d['asal_utd']);
    $jns_asal       = '2';
    $suhu           = '2-6';
    $catatan        = '';

    $sql = "
        INSERT INTO registrasi_qc
        (nokantong, produk, volume, goldarah, rhesus, tgl, tglaftap, kadaluwarsa, tgl_pengolahan, petugas_terima, petugas_serah, asal_utd, suhu, jns_asal, catatan)
        VALUES
        ('$nokantong', '$produk', '$volume', '$goldarah', '$rhesus', NOW(), '$tglaftap', '$kadaluwarsa', '$tgl_pengolahan', '$petugas_terima', '$petugas_serah', '$asal_utd', '$suhu', '$jns_asal', '$catatan')
    ";

    $insert = mysql_query($sql);
    if (!$insert) {
        $gagalList[] = array(
            'nokantong' => $d['nokantong'],
            'alasan' => 'Insert gagal: ' . mysql_error()
        );
        continue;
    }

    //=======Audit Trial====================================================================================
    $log_mdl = 'UJI MUTU';
    $log_aksi = 'Registrasi QC produk komponen darah: ' . $nokantong . ' - ' . $produk . ' - asal ' . ($d['nama_utd'] ? $d['nama_utd'] : $asal_utd);
    include_once __DIR__ . '/user_log.php';
    //=====================================================================================================	

    $berhasil++;
    $suksesIds[] = (int)$d['id'];
}

if (!empty($suksesIds)) {
    $idList = implode(',', $suksesIds);
    $del = mysql_query("DELETE FROM registrasi_luarqc_temp WHERE user_input = '$namauser' AND id IN ($idList)");

    if (!$del) {
        echo json_encode(array(
            'status' => 'error',
            'msg' => 'Data berhasil disimpan, tetapi gagal hapus data sementara: ' . mysql_error()
        ));
        exit;
    }
}

$remainingHtml = renderTempRows($namauser);

if ($berhasil > 0 && empty($gagalList)) {
    echo json_encode(array(
        'status' => 'success',
        'msg' => $berhasil . ' data berhasil disimpan ke registrasi QC.',
        'berhasil' => $berhasil,
        'gagal' => array(),
        'html' => $remainingHtml
    ));
    exit;
}

if ($berhasil > 0 && !empty($gagalList)) {
    echo json_encode(array(
        'status' => 'partial',
        'msg' => $berhasil . ' data berhasil disimpan, tetapi ada ' . count($gagalList) . ' data yang gagal.',
        'berhasil' => $berhasil,
        'gagal' => $gagalList,
        'html' => $remainingHtml
    ));
    exit;
}

echo json_encode(array(
    'status' => 'error',
    'msg' => 'Semua data gagal disimpan.',
    'berhasil' => 0,
    'gagal' => $gagalList,
    'html' => $remainingHtml
));
exit;