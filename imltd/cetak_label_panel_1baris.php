<?php
ob_start();
session_start();

require_once('../config/db_connect.php');
include_once dirname(__FILE__) . '/../tcpdf/tcpdf.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function esc($str)
{
    return mysql_real_escape_string(trim($str));
}

function getJenisKantongKode($jenis)
{
    switch ((string)$jenis) {
        case '1':
            return array('SG', 'Single');
        case '2':
            return array('DB', 'Double');
        case '3':
            return array('TR', 'Triple');
        case '4':
            return array('QD', 'Quadruple');
        case '5':
            return array('QT', 'Pediatrik/Quintuple');
        case '6':
            return array('SX', 'Sextuple');
        default:
            return array('', '');
    }
}

function renderLabel($pdf, $noKantong, $volume, $merk, $produk, $tglEd, $jenis, $utd, $jenisSampel)
{
    $jenisKode = '';
    $jenisNama = '';

    if (is_array($jenis)) {
        $jenisKode = isset($jenis[0]) ? $jenis[0] : '';
        $jenisNama = isset($jenis[1]) ? $jenis[1] : '';
    }

    $style = array(
        'position'   => '',
        'align'      => 'L',
        'stretch'    => false,
        'fitwidth'   => true,
        'cellfitalign' => '',
        'border'     => false,
        'hpadding'   => 0,
        'vpadding'   => 0,
        'fgcolor'    => array(0, 0, 0),
        'bgcolor'    => false,
        'text'       => false,
        'font'       => 'helvetica',
        'fontsize'   => 8,
        'stretchtext' => 1
    );

    $pdf->AddPage();

    // kiri atas: nama UTD
    $pdf->SetFont('helvetica', 'B', 6.5);
    $pdf->SetXY(1, 0.8);
    $pdf->Cell(30, 3, $utd, 0, 0, 'L');

    // kanan atas: jenis kantong
    $pdf->SetFont('helvetica', 'B', 6.5);
    $pdf->SetXY(35, 0.8);
    $pdf->Cell(14, 3, $jenisKode, 0, 0, 'R');

    // barcode
    $pdf->write1DBarcode(
        strtoupper($noKantong),
        'C128',
        2,
        4.5,
        45,
        6.8,
        0.33,
        $style,
        'N'
    );

    // nomor kantong rata kiri sejajar barcode
    $pdf->SetFont('helvetica', 'B', 8.5);
    $pdf->SetXY(1, 11.5);
    $pdf->Cell(46, 3, strtoupper($noKantong), 0, 0, 'L');

    // Keterangan Reaktif atau nonReaktif
    $pdf->SetFont('helvetica', '', 6);
    $pdf->SetXY(21, 12);
    $pdf->Cell(27, 3, $jenisSampel, 0, 0, 'R');

    // kiri bawah: sampel panel
    $pdf->SetFont('helvetica', '', 6);
    $pdf->SetXY(1, 15.6);
    $pdf->Cell(20, 3, 'Sampel Panel', 0, 0, 'L');

    // kanan bawah: keterangan kantong
    $infoParts = array();

    if (!empty($produk)) {
        $infoParts[] = $produk;
    } else if (!empty($merk)) {
        $infoParts[] = $merk;
    }

    if (!empty($volume)) {
        $infoParts[] = $volume . ' ml';
    }

    $ket = implode(' | ', $infoParts);

    $pdf->SetFont('helvetica', '', 6);
    $pdf->SetXY(21, 15.6);
    $pdf->Cell(27, 3, $ket, 0, 0, 'R');
}

$notrans = isset($_POST['notrans']) ? trim($_POST['notrans']) : '';
$modeCetak = isset($_POST['modeCetak']) ? trim($_POST['modeCetak']) : '1baris';
$jumlahInputs = isset($_POST['jumlah']) && is_array($_POST['jumlah']) ? $_POST['jumlah'] : array();

if ($notrans == '') {
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error</title></head><body>No transaksi tidak ditemukan.</body></html>';
    exit;
}

$notrans_sql = esc($notrans);

$qHeader = mysql_query("SELECT * FROM sampel_panel_trans WHERE notrans = '{$notrans_sql}' LIMIT 1");
if (!$qHeader || mysql_num_rows($qHeader) == 0) {
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error</title></head><body>Data header tidak ditemukan.</body></html>';
    exit;
}

$header = mysql_fetch_assoc($qHeader);

if (ob_get_length()) {
    ob_end_clean();
}

mysql_query("SET NAMES utf8");

// nama UTD cukup diambil sekali
$utd = '';
$qUtd = mysql_query("SELECT nama FROM utd WHERE aktif='1' LIMIT 1");
if ($qUtd && mysql_num_rows($qUtd) > 0) {
    $rUtd = mysql_fetch_assoc($qUtd);
    $utd = isset($rUtd['nama']) ? $rUtd['nama'] : '';
}

$pdf = new TCPDF('L', 'mm', array(50, 20), true, 'UTF-8', false);
$pdf->SetTitle('Label Tabung');
$pdf->SetSubject('Label Tabung');
$pdf->SetAuthor('SIMDONDAR');
$pdf->SetCreator('SIMDONDAR');
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$labelPrinted = 0;

foreach ($jumlahInputs as $detailId => $qty) {
    $detailId = (int)$detailId;
    $qty = (int)$qty;

    if ($detailId <= 0 || $qty <= 0) {
        continue;
    }

    $qDetail = mysql_query("
        SELECT id, notrans, nokantong, jenis_sampel
        FROM sampel_panel_detail
        WHERE id = '{$detailId}'
          AND notrans = '{$notrans_sql}'
        LIMIT 1
    ");

    if (!$qDetail || mysql_num_rows($qDetail) == 0) {
        continue;
    }

    $detail = mysql_fetch_assoc($qDetail);
    $noKantong = isset($detail['nokantong']) ? trim($detail['nokantong']) : '';
    $jenisSampel = isset($detail['jenis_sampel']) ? trim($detail['jenis_sampel']) : '';

    if ($noKantong == '') {
        continue;
    }

    $volume = '';
    $merk   = '';
    $produk = '';
    $tglEd  = '';
    $jenis  = array('', '');

    $noKantong_sql = esc($noKantong);

    $qStok = mysql_query("
        SELECT jenis, volume, merk, produk, kadaluwarsa
        FROM stokkantong
        WHERE noKantong = '{$noKantong_sql}'
        LIMIT 1
    ");

    if ($qStok && mysql_num_rows($qStok) > 0) {
        $stok = mysql_fetch_assoc($qStok);

        $volume = isset($stok['volume']) ? $stok['volume'] : '';
        $merk   = isset($stok['merk']) ? $stok['merk'] : '';
        $produk = isset($stok['produk']) ? $stok['produk'] : '';

        if (isset($stok['jenis'])) {
            $jenis = getJenisKantongKode($stok['jenis']);
        }

        if (!empty($stok['kadaluwarsa'])) {
            $tglEd = date('d/m/Y', strtotime($stok['kadaluwarsa']));
        }
    }

    for ($i = 0; $i < $qty; $i++) {
        renderLabel($pdf, $noKantong, $volume, $merk, $produk, $tglEd, $jenis, $utd, $jenisSampel);
        $labelPrinted++;
    }
}

if ($labelPrinted == 0) {
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error</title></head><body>Tidak ada label yang dipilih untuk dicetak.</body></html>';
    exit;
}

$pdf->Output('label_tabung_' . $notrans . '.pdf', 'I');
exit;