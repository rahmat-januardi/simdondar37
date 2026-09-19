<?php
require_once('config/koneksi.php');
//============================================================+
// File name   : example_027.php
// Begin       : 2008-03-04
// Last Update : 2010-05-02
//
// Description : Example 027 for TCPDF class
//               1D Barcodes
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: 1D Barcodes.
 * @author Nicola Asuni
 * @copyright 2004-2009 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2008-03-04
 */

require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sudarko');
$pdf->SetTitle('Kartu Donor Darah');
$pdf->SetSubject('Barcode');
$pdf->SetKeywords('Simbada, PDF, barcode,kantong, pmi, jember');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins(0, 0, 5);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetAutoPageBreak(TRUE, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set a barcode on the page footer
//$pdf->setBarcode(date('Y-m-d H:i:s'));

// set font
$pdf->SetFont('helvetica', 'b', 10);
// add a page
$pdf->AddPage('L', 'IDC');
//$pdf->AddPage();

// define barcode style
$style = array(
	'position' => 'R',
	'border' => false,
	'padding' => 'auto',
	'fgcolor' => array(0, 0, 0),
	'bgcolor' => false, //array(255,255,255),
	'text' => false,
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 10
);

// PRINT VARIOUS 1D BARCODES

// CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
//$pdf->Cell(0, 0, 'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9', 0, 1);

//	$pdf->Image("header_pmi.png",0,0,77,7);

$idpendonor = trim($_GET[idpendonor]);
$sql = mysql_fetch_assoc(mysql_query("select * from pendonor where Kode='$idpendonor'"));
$udd = mysql_fetch_assoc(mysql_query("select nama,alamat from utd where down='1' and aktif='1'"));
$pdf->SetFont('helvetica', 'b', 9);
$pdf->SetXY(0, 6);
$pdf->Cell(0, 10, 'KARTU DONOR DARAH', 0, 1, 'C');
$pdf->SetFont('helvetica', 'bu', 9);
$pdf->SetXY(0, 9);
$pdf->Cell(0, 10, $udd[nama], 0, 1, 'C');
//$pdf->SetXY(0,15);
//$pdf->Image("1.png",0,15,20,20);
//	$pdf->Image("upload/foto_$idpendonor.jpg",0,14,19,22);

//$nama=strtoupper($sql[Nama]);
$pdf->SetXY(19, 11);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(72, 12, 'No. ID', 0, 0, 'L');
$pdf->SetXY(32, 11);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(50, 12, ':  ' . $sql[Kode], 0, 0, 'L');

$pdf->SetXY(19, 13.5);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(27, 12, 'Nama', 0, 0, 'L');
$pdf->SetXY(32, 13.5);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(50, 12, ':  ' . $sql[Nama], 0, 0, 'L');

$pdf->SetXY(19, 16);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(27, 12, 'Tpt. Lahir', 0, 0, 'L');
$pdf->SetXY(32, 16);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(30, 12, ':  ' . $sql[TempatLhr], 0, 0, 'L');

$lahir = $sql[TglLhr];
$tgl2 = date("d", strtotime($lahir));
$bln2 = date("n", strtotime($lahir));
$thn2 = date("Y", strtotime($lahir));
$bulan = array(1 => "Januari", "Pebruari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "Nopember", "Desember");
$bln22 = $bulan[$bln2];

$pdf->SetXY(19, 18.5);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(27, 12, 'Tgl. Lahir', 0, 0, 'L');
$pdf->SetXY(32, 18.5);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(50, 12, ':  ' . $tgl2 . ' ' . $bln22 . ' ' . $thn2, 0, 0, 'L');

$pdf->SetXY(19, 21);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(27, 12, 'Alamat', 0, 0, 'L');
$pdf->SetXY(32, 21);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(50, 12, ':  ' . $sql[Alamat], 0, 0, 'L');

$pdf->SetXY(19, 23.5);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(27, 12, 'Wilayah', 0, 0, 'L');
$pdf->SetXY(32, 23.5);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(50, 12, ':  ' . $sql[wilayah], 0, 0, 'L');

$pdf->SetXY(19, 26);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(27, 12, 'Telp', 0, 0, 'L');
$pdf->SetXY(32, 26);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(50, 12, ':  ' . $sql[telp2], 0, 0, 'L');

if ($sql[Jk] == '0') {
	$jk = "Pria";
} else {
	$jk = "Perempuan";
}
$pdf->SetXY(19, 28.5);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(27, 12, 'Jns Klmn', 0, 0, 'L');
$pdf->SetXY(32, 28.5);
$pdf->SetFont('helvetica', 'b', 7);
$pdf->Cell(50, 12, ':  ' . $jk, 0, 0, 'L');

$idpendonor_value = trim($_GET['idpendonor']);

$pdf->SetXY(1, 30.8);
$pdf->SetFont('helvetica', 'b', 26);
$pdf->Cell(0, 20, $sql[GolDarah] . $sql[Rhesus], 0);
//$pdf->SetXY(11,30); 
//$pdf->SetFont('helvetica', 'b', 15);
//$pdf->Cell(0, 20, '('.$sql[Rhesus].')', 0);
$pdf->SetXY(19, 35);
//$pdf->SetXY(0,10); 
//$pdf->write1DBarcode($idpendonor_value, 'C128B', '', '', 8, 4, 0.24, $style, 'T');
$pdf->write1DBarcode($idpendonor_value, 'C128', '', '', '', 14, 0.29, $style, 'N');

$qrStyle = array(
	'border' => false,
	'padding' => 2,
	'fgcolor' => array(0, 0, 0),
	'bgcolor' => false
);
$pdf->write2DBarcode($idpendonor_value, 'QRCODE,H', 3, 20, 15, 15, $qrStyle, 'N');

//Close and output PDF document
$nama_file = $_GET[idpendonor] . ".pdf";
$pdf->Output('idcard.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+

mysql_close();