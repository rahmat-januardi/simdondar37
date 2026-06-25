<?php
require_once('config/dbi_connect.php');
//============================================================+
// File name   : example_027.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 027 for TCPDF class
//               1D Barcodes
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: 1D Barcodes.
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
//require_once('../tcpdf/tcpdf.php');
require_once('tcpdf/tcpdf.php');

$nT = isset($_GET['nT']) ? $_GET['nT'] : null;
$transaksi = isset($_GET['transaksi']) ? $_GET['transaksi'] : $_GET['transaksi'];
// $nT = "KV317D-160425-0001";

function formatTanggal($inputTanggal)
{
    if (is_null($inputTanggal) || $inputTanggal === '0000-00-00' || $inputTanggal === '0000-00-00 00:00:00') {
        return "Format Tanggal Tidak Valid";
    }

    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    try {
        $dateTime = new DateTime($inputTanggal);
        $split = explode('-', $inputTanggal);

        // Check if time is included in the input
        if (strpos($inputTanggal, ':') !== false) {
            $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i");
        } else {
            $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y");
        }

        return $formattedDate;
    } catch (Exception $e) {
        return "Format Tanggal Tidak Valid";
    }
}

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('irawanDB');
$pdf->SetTitle('SIMDONDAR');
$pdf->SetSubject('LABEL KOMPONEN DARAH');
$pdf->SetKeywords('SIMDONDAR, PDF, barcode, pmi, udd, jember');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(-1, 5, 0);   //$pdf->SetMargins(-1,2,0);
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// define barcode style
$style = array(
    'position' => 'S',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    //'hpadding' => 'auto',
    //'vpadding' => 'auto',
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false, //array(255,255,255),
    // Setting Text pada bagian bawah barcodes //
    'text' => false,
    'font' => 'dejavusans',
    'fontsize' => 12,
    'stretchtext' => 1
);


if (isset($transaksi)) {
    if ($transaksi === 'transaksi') {
        $query = "SELECT noKantong, Produk, petugas, tgl, DATE(tgl) as tanggal, tglPengerjaan, DATE(tglPengerjaan) as tanggalPengerjaan, goldarah, rhesus, jenis 
                FROM `dpengolahan` 
                WHERE NoTrans='$nT'";
    } elseif ($transaksi === 'kantong') {
        $query = "SELECT noKantong, Produk, petugas, tgl, DATE(tgl) as tanggal, tglPengerjaan, DATE(tglPengerjaan) as tanggalPengerjaan, goldarah, rhesus, jenis 
                FROM `dpengolahan` 
                WHERE noKantong='$nT'";
    } else {
        $query = null;
    }

    if ($query) {
        $result = mysqli_query($dbi, $query);
    } else {
        $result = false;
    }
} else {
    $result = false;
}
$alphabet = range('A', 'Z');

$resolution = array(50, 20);

$barcodeType = isset($_GET['barcode']) ? $_GET['barcode'] : 'C128';
$alphabet = range('A', 'Z');
//$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
$rows = array();
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}


// Fetch the 'metoda' value from 'stokkantong' table based on matching 'noKantong'
foreach ($rows as &$row) {
    $noKantong = $row['noKantong'];
    $metodaQuery = "SELECT metoda FROM stokkantong WHERE noKantong = '$noKantong'";
    $metodaResult = mysqli_query($dbi, $metodaQuery);

    if ($metodaResult && mysqli_num_rows($metodaResult) > 0) {
        $metodaRow = mysqli_fetch_assoc($metodaResult);
        $row['metoda'] = $metodaRow['metoda'];
    } else {
        $row['metoda'] = null; // Default value if no match is found
    }
}
unset($row); // Unset reference to avoid side effects


$namaUDDQuery = "SELECT nama FROM utd WHERE aktif = 1 LIMIT 1";
$namaUDDResult = mysqli_query($dbi, $namaUDDQuery);

if ($namaUDDResult && mysqli_num_rows($namaUDDResult) > 0) {
    $namaUDDRow = mysqli_fetch_assoc($namaUDDResult);
    $namaUDD = $namaUDDRow['nama'];
} else {
    $namaUDD = 'UDD PMI Kabupaten Jember';
}

$fontsize = 7.5;

// OPSI SATU BARIS DUA LABEL DAN SATU KANTONG DUA LABEL DALAM SATU BARIS
$pdf->AddPage('L', $resolution);
$maxY = 20; // tinggi halaman
$marginTop = 4.5;
$labelHeight = 10; // kira-kira tinggi tiap label

$index = 0;

foreach ($rows as $row) {
    $kodeSatelit = $alphabet[$index % count($alphabet)];
    $noKantong = $row['noKantong'];
    $jenis = $row['jenis'];

    $fontSizeUDD = strlen($namaUDD) > 30 ? 4.4 : 6;

    // Cek jika posisi Y melebihi batas halaman
    $posY = $marginTop + ($index % 1) * $labelHeight;
    if ($index > 0) {
        $pdf->AddPage('L', $resolution);
        $posY = $marginTop;
    }

    // Ukuran font dinamis tergantung panjang kode
    $bagsize = strlen($noKantong) <= 8 ? 9 : (strlen($noKantong) <= 12 ? 7.5 : 6.5);

    switch ($jenis) {
        case '2':
            $label = 'DOUBLE';
            $jn = 35;
            break;
        case '3':
            $label = 'TRIPLE';
            $jn = 36.7;
            break;
        case '4':
            $label = 'QUADRUPLE';
            $jn = 30;
            break;
        case '6':
            $label = 'PEDIATRIK';
            $jn = 32;
            break;
        default:
            $label = 'SINGLE';
            $jn = 37;
            break;
    }

    $pdf->SetFont('dejavusans', '', $fontSizeUDD);
    $adjustedY = strlen($namaUDD) > 30 ? $posY - 2.5 : $posY - 3;
    $pdf->SetXY(2.7, $adjustedY);
    $pdf->Cell(0, 0, $namaUDD, 0, 0, 'L');
    switch ($row['goldarah']) {
        case 'AB':
            $posX = 38;
            break;
        default:
            $posX = 39.5;
            break;
    }

    switch ($row['rhesus']) {
        case '+':
            $posX += 0.2;
            break;
        case '-':
            $posX -= 0.2;
            break;
        default:
            break;
    }

    $pdf->SetFont('dejavusans', '', 6);
    $pdf->SetXY($posX, $posY - 2.7);
    $goldarah = $row['goldarah'];
    $rhesus = $row['rhesus'] === '+' ? 'POS' : ($row['rhesus'] === '-' ? 'NEG' : $row['rhesus']);
    $pdf->Cell(0, 0, $goldarah . ' ' . $rhesus, 0, 0, 'L');

    if (!empty($row['metoda'])) {
        $pdf->SetFont('dejavusans', '', 4);

        switch ($row['metoda']) {
            case 'TT':
                $metodaLabel = "Top & Top";
                $metodaX = 38.7;
                break;
            case 'TB':
                $metodaLabel = "Top & Bottom";
                $metodaX = 36.2;
                break;
            case 'TBF':
                $metodaLabel = "Filter";
                $metodaX = 42.3;
                break;
            default:
                $metodaLabel = $row['metoda'];
                $metodaX = 44;
                break;
        }

        $pdf->SetXY($metodaX, $posY + 9.9);
        $pdf->Cell(0, 0, $metodaLabel, 0, 0, 'L');
    }

    if (!empty($row['Produk'])) {
        $pdf->SetFont('dejavusans', 'B', 7);
        $pdf->SetXY(3, $posY + 9.5);
        $pdf->Cell(0, 0, $row['Produk'], 0, 0, 'L');
        $pdf->SetFont('dejavusans', '', 4);
        $pdf->SetXY(3, $posY + 12.3);
        $pdf->Cell(0, 0, 'Pengolahan: ' . formatTanggal($row['tglPengerjaan']), 0, 0, 'L');
    }

    $pdf->SetXY(4, $posY);
    $pdf->write1DBarcode($noKantong, $barcodeType, '', '', 43, 7, '', $style, 'T');
    // $pdf->SetX(58);
    // $pdf->write1DBarcode($fullKode, $barcodeType, '', '', 43, 7, '', $style, 'N');

    // Label jenis kantong
    $pdf->SetFont('helvetica', '', $bagsize);
    $pdf->SetXY($jn, $posY + 7);
    $pdf->Cell(0, 0, $label, 0, 0, 'L');

    // No Kantong teks
    $pdf->SetFont('helvetica', '', $bagsize);
    $pdf->SetXY(3, $posY + 7);
    $pdf->Cell(0, 0, $noKantong, 0, 0, 'L');

    $index++;
}
// END OF OPSI SATU BARIS DUA LABEL DAN SATU KANTONG DUA LABEL DALAM SATU BARIS

$pdf->Output('LabelKomponen2Kolom.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
