<?php
// ajax/rData.php
session_start();
require_once '../config/dbi_connect.php';

header('Content-Type: application/json');

$user_id = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : "irawanDB";
if (!$user_id) {
    echo json_encode(['error' => 'User tidak ditemukan']);
    exit;
}

$sqlTotal = "SELECT COUNT(*) as total FROM serahterima_detail_tmp WHERE dst_user = '$user_id'";
$resultTotal = $dbi->query($sqlTotal);
$total = 0;
if ($row = $resultTotal->fetch_assoc()) {
    $total = $row['total'];
}

// Per produk
$sqlProduk = "SELECT dst_produk, COUNT(*) as jumlah FROM serahterima_detail_tmp WHERE dst_user = '$user_id' GROUP BY dst_produk";
$resultProduk = $dbi->query($sqlProduk);
$per_produk = array();
while ($row = $resultProduk->fetch_assoc()) {
    $per_produk[$row['dst_produk']] = $row['jumlah'];
}
$hasilPerKantong = array();

// Ambil dari hasilelisa
$sqlNR = "SELECT noKantong, Hasil FROM hasilelisa 
        WHERE noKantong IN (
            SELECT dst_nokantong FROM serahterima_detail_tmp WHERE dst_user = '$user_id')";
$resultNR = $dbi->query($sqlNR);
while ($row = $resultNR->fetch_assoc()) {
    $no = $row['noKantong'];
    $hasilPerKantong[$no][] = (int) $row['Hasil'];
}

// Ambil dari hasilnat juga
$sqlNAT = "SELECT noKantong, Hasil FROM hasilnat 
        WHERE noKantong IN (
            SELECT dst_nokantong FROM serahterima_detail_tmp WHERE dst_user = '$user_id')";
$resultNAT = $dbi->query($sqlNAT);
while ($row = $resultNAT->fetch_assoc()) {
    $no = $row['noKantong'];
    $hasilPerKantong[$no][] = (int) $row['Hasil'];
}

$nr = 0;
$r = 0;
$gz = 0;

foreach ($hasilPerKantong as $no => $listHasil) {
    if (in_array(1, $listHasil)) {
        $r++;
    } elseif (in_array(2, $listHasil)) {
        $gz++;
    } else {
        $nr++;
    }
}

// Rekap Jenis Kantong
$jenis_kantong = array(
    'Single' => 0,       // SG
    'Double' => 0,       // DB
    'Tripple' => 0,      // TR
    'Quadruple' => 0,    // QD
    'Pediatrik' => 0     // PB
);

$sqlJenis = "SELECT dst_jenisktg, COUNT(*) as jumlah FROM serahterima_detail_tmp WHERE dst_user = '$user_id' GROUP BY dst_jenisktg";
$resultJenis = $dbi->query($sqlJenis);
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

// Rekap Metoda Kantong
$sqlMetoda = "SELECT s.metoda, COUNT(*) as jumlah 
            FROM serahterima_detail_tmp d 
            JOIN stokkantong s ON d.dst_nokantong = s.noKantong 
            WHERE d.dst_user = '$user_id' 
            GROUP BY s.metoda";
$resultMetoda = $dbi->query($sqlMetoda);
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
    'gz' => $gz,
    'jenis_kantong' => $jenis_kantong,
    'metoda_kantong' => $metoda_kantong,
    'total_kantong' => array_sum($jenis_kantong)

));

