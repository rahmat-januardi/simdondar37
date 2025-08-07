<?php
// ajax/rData.php
session_start();
include '../config/dbi_connect.php';

header('Content-Type: application/json');

$user_id = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : "irawanDBaa";
//$user_id = $_SESSION['namauser'];
if (!$user_id) {
    echo json_encode(array('error' => 'User tidak ditemukan'));
    exit;
}

// Debugging untuk query total
$sqlTotal = "SELECT COUNT(*) as total FROM serahterima_detail_tmp WHERE dst_user = '$user_id'";
$resultTotal = $dbi->query($sqlTotal);
if (!$resultTotal) {
    error_log('Error pada query total: ' . $dbi->error);
    echo json_encode(array('error' => 'Terjadi kesalahan, silakan cek log'));
    exit;
}
$total = 0;
if ($row = $resultTotal->fetch_assoc()) {
    $total = $row['total'];
}

// Debugging untuk query per produk
$sqlProduk = "SELECT dst_produk, COUNT(*) as jumlah FROM serahterima_detail_tmp WHERE dst_user = '$user_id' GROUP BY dst_produk";
$resultProduk = $dbi->query($sqlProduk);
if (!$resultProduk) {
    error_log('Error pada query per produk: ' . $dbi->error);
    echo json_encode(array('error' => 'Terjadi kesalahan, silakan cek log'));
    exit;
}
$per_produk = array();
while ($row = $resultProduk->fetch_assoc()) {
    $per_produk[$row['dst_produk']] = $row['jumlah'];
}

// Debugging untuk query hasil dari hasilelisa
$sqlNR = "SELECT noKantong, Hasil FROM hasilelisa 
        WHERE noKantong IN (
            SELECT dst_nokantong FROM serahterima_detail_tmp WHERE dst_user = '$user_id')";
$resultNR = $dbi->query($sqlNR);
if (!$resultNR) {
    error_log('Error pada query hasil dari hasilelisa: ' . $dbi->error);
    echo json_encode(array('error' => 'Terjadi kesalahan, silakan cek log'));
    exit;
}
$hasilPerKantong = array();
while ($row = $resultNR->fetch_assoc()) {
    $no = $row['noKantong'];
    $hasilPerKantong[$no][] = (int) $row['Hasil'];
}

// Debugging untuk query hasil dari hasilnat
$sqlNAT = "SELECT noKantong, Hasil FROM hasilnat 
        WHERE noKantong IN (
            SELECT dst_nokantong FROM serahterima_detail_tmp WHERE dst_user = '$user_id')";
$resultNAT = $dbi->query($sqlNAT);
if (!$resultNAT) {
    error_log('Error pada query hasil dari hasilnat: ' . $dbi->error);
    echo json_encode(array('error' => 'Terjadi kesalahan, silakan cek log'));
    exit;
}
while ($row = $resultNAT->fetch_assoc()) {
    $no = $row['noKantong'];
    $hasilPerKantong[$no][] = (int) $row['Hasil'];
}

$nr = 0;
$r = 0;
// $gz = 0;

foreach ($hasilPerKantong as $no => $listHasil) {
    if (in_array(1, $listHasil)) {
        $r++;
    } elseif (in_array(2, $listHasil)) {
        $r++;
    } else {
        $nr++;
    }
}

// Debugging untuk query rekap jenis kantong
$sqlJenis = "SELECT dst_jenisktg, COUNT(*) as jumlah FROM serahterima_detail_tmp WHERE dst_user = '$user_id' GROUP BY dst_jenisktg";
$resultJenis = $dbi->query($sqlJenis);
if (!$resultJenis) {
    error_log('Error pada query rekap jenis kantong: ' . $dbi->error);
    echo json_encode(array('error' => 'Terjadi kesalahan, silakan cek log'));
    exit;
}
$jenis_kantong = array('Single' => 0, 'Double' => 0, 'Tripple' => 0, 'Quadruple' => 0, 'Pediatrik' => 0);
while ($row = $resultJenis->fetch_assoc()) {
    switch ($row['dst_jenisktg']) {
        case 1:
            $jenis_kantong['Single'] += $row['jumlah'];
            break;
        case 2:
            $jenis_kantong['Double'] += $row['jumlah'];
            break;
        case 3:
            $jenis_kantong['Tripple'] += $row['jumlah'];
            break;
        case 4:
            $jenis_kantong['Quadruple'] += $row['jumlah'];
            break;
        case 5:
        case 6:
            $jenis_kantong['Pediatrik'] += $row['jumlah'];
            break;
        case 7:
    }
}

// Debugging untuk query rekap metoda kantong
$sqlMetoda = "SELECT s.metoda, COUNT(*) as jumlah 
            FROM serahterima_detail_tmp d 
            JOIN stokkantong s ON d.dst_nokantong = s.noKantong 
            WHERE d.dst_user = '$user_id' 
            GROUP BY s.metoda";
$resultMetoda = $dbi->query($sqlMetoda);
if (!$resultMetoda) {
    error_log('Error pada query rekap metoda kantong: ' . $dbi->error);
    echo json_encode(array('error' => 'Terjadi kesalahan, silakan cek log'));
    exit;
}
$metoda_kantong = array(
    'Top & Top (TT)' => 0,
    'Top & Bottom (TB)' => 0,
    'Filter' => 0,
    'Lainnya' => 0
);

while ($row = $resultMetoda->fetch_assoc()) {
    switch (strtoupper($row['metoda'])) {
        case 'TT':
            $metoda_kantong['Top & Top (TT)'] += $row['jumlah'];
            break;
        case 'TB':
            $metoda_kantong['Top & Bottom (TB)'] += $row['jumlah'];
            break;
        case 'TBF':
            $metoda_kantong['Filter'] += $row['jumlah'];
            break;
        default:
            $metoda_kantong['Lainnya'] += $row['jumlah'];
            break;
    }
}

$dbi->close();

echo json_encode(array(
    'total' => $total,
    'per_produk' => $per_produk,
    'nr' => $nr,
    'r' => $r,
    // 'gz' => $gz,
    'jenis_kantong' => $jenis_kantong,
    'metoda_kantong' => $metoda_kantong,
    'total_kantong' => array_sum($jenis_kantong)
));

