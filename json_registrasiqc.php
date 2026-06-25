<?php
include "config/db_connect.php";
header('Content-Type: application/json; charset=utf-8');

$noKantong = isset($_GET['NoKantong']) ? $_GET['NoKantong'] : '';

if ($noKantong == '') {
    echo json_encode(array(
        'valid' => '0',
        'pesan' => 'No kantong kosong'
    ));
    exit;
}

$cekDistribusi = mysql_fetch_assoc(mysql_query("
    SELECT kode, nama 
    FROM bdrs 
    WHERE nama LIKE '%QC%' 
    LIMIT 1
"));
$kodeDistribusi = isset($cekDistribusi['kode']) ? $cekDistribusi['kode'] : '';

$data = mysql_fetch_assoc(mysql_query("
    SELECT * 
    FROM stokkantong 
    WHERE noKantong='$noKantong'
"));

if (!$data) {
    echo json_encode(array(
        'valid' => '0',
        'pesan' => 'No kantong tidak ditemukan'
    ));
    exit;
}

if (!in_array($data['Status'], array('2', '3'))) {
    echo json_encode(array(
        'valid' => '2',
        'pesan' => 'Status kantong tidak sesuai, harus Sehat atau Keluar'
    ));
    exit;
}

if (!empty($data['stat2']) && $data['stat2'] != $kodeDistribusi) {
    echo json_encode(array(
        'valid' => '3',
        'pesan' => 'Kantong belum di distribusi ke QC'
    ));
    exit;
}

if ($data['statKonfirmasi'] != '1') {
    echo json_encode(array(
        'valid' => '4',
        'pesan' => 'Belum dikonfirmasi'
    ));
    exit;
}

if ($data['statQC'] == '0') {
    echo json_encode(array(
        'valid' => '5',
        'pesan' => 'Sudah serah terima QC, belum melakukan QC'
    ));
    exit;
}

if ($data['statQC'] == '1') {
    echo json_encode(array(
        'valid' => '6',
        'pesan' => 'Kantong sudah pernah QC'
    ));
    exit;
}

if ($data['hasilNAT'] == '1') {
    echo json_encode(array(
        'valid' => '7',
        'pesan' => 'Hasil NAT tidak memenuhi'
    ));
    exit;
}

if ($data['hasil_release'] != '1') {
    echo json_encode(array(
        'valid' => '8',
        'pesan' => 'Belum release'
    ));
    exit;
}

$aftap = ($data['tgl_Aftap'] != '') ? date("Y-m-d", strtotime($data['tgl_Aftap'])) : '-';
$kadaluwarsa = ($data['kadaluwarsa'] != '') ? date("Y-m-d", strtotime($data['kadaluwarsa'])) : '-';
$tglpengolahan = ($data['tglpengolahan'] != '') ? date("Y-m-d", strtotime($data['tglpengolahan'])) : '-';

switch ($data['jenis']) {
    case '1':
        $jenisKantong = 'Single';
        break;
    case '2':
        $jenisKantong = 'Double';
        break;
    case '3':
        $jenisKantong = 'Triple';
        break;
    default:
        $jenisKantong = '-';
        break;
}

echo json_encode(array(
    'valid' => '1',
    'pesan' => '',
    'darah' => array(
        'gol_darah' => $data['gol_darah'],
        'produk' => $data['produk'],
        'RhesusDrh' => $data['RhesusDrh'],
        'tgl_Aftap' => $aftap,
        'kadaluwarsa' => $kadaluwarsa,
        'tglpengolahan' => $tglpengolahan,
        'volumeasal' => $data['volumeasal'],
        'jeniskantong' => $jenisKantong
    )
));
exit;
