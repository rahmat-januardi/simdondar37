<?php
require_once('../config/koneksi.php');

    
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

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

//require_once('23april2011/simudda/tcpdf/config/lang/eng.php');
//require_once('23april2011/simudda/tcpdf/tcpdf.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sudarko');
$pdf->SetTitle('Simbada Kode Kantong');
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
$pdf->SetMargins(0,1, 0);
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
$pdf->SetFont('helvetica', '', 10);
// add a page
//$pdf->AddPage('L','BK');
$resolution= array(100, 20);
 $pdf->AddPage('L', $resolution);
//$pdf->AddPage();

// define barcode style
$style = array(
    'position' => 'S',
    'stretch' => false,
        'fitwidth' => true,
        'cellfitalign' => '',
    'border' => false,
    //'padding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);
    
//define 2D
    $style2 = array(
        'border' => true,
        'fgcolor' => array(0,0,0),
        'bgcolor' => false, //array(255,255,255)
        'module_width' => 1, // width of a single module in points
        'module_height' => 1 // height of a single module in points
    );
// PRINT VARIOUS 1D BARCODES

$idpendonor = $_GET[idpendonor];
$sql=mysql_fetch_assoc(mysql_query("select * from pendonor where Kode='$idpendonor'"));

$lahir=$sql[TglLhr];
$tgllahir=date("d/m/Y",strtotime($lahir));
    
$code = $idpendonor;

$pdf->SetXY(1);
    $pdf->write2DBarcode($code, 'QRCODE,H', 1, 2.5, 15, 15, $style, 'N');
$pdf->SetX(54);
    $pdf->write2DBarcode($code, 'QRCODE,H', 54, 2.5, 15, 15, $style, 'N');

$pdf->SetFont('helvetica', '', 7);
$pdf->SetXY(17,2);
$pdf->Cell(17, 0,''.$idpendonor,0, 0,'L');
$pdf->SetXY(17,5);
$pdf->Cell(17, 0,''.$sql[Nama],0, 0,'L');
$pdf->SetXY(17,8);
$pdf->Cell(17, 0,'Tgl.Lahir: ' .$tgllahir,0, 0,'L');
$pdf->SetFont('', 'B', 15);
$pdf->SetXY(34,12);
$pdf->Cell(17, 0,'' .$sql[GolDarah].' ['.$sql[Rhesus].']',0, 0,'L');
    
$pdf->SetFont('helvetica', '', 7);
$pdf->SetXY(70,2);
$pdf->Cell(70, 0,''.$idpendonor,0, 0,'L');
$pdf->SetXY(70,5);
$pdf->Cell(70, 0,''.$sql[Nama],0, 0,'L');
$pdf->SetXY(70,8);
$pdf->Cell(70, 0,'Tgl.Lahir: ' .$tgllahir,0, 0,'L');
$pdf->SetFont('', 'B', 15);
$pdf->SetXY(87,12);
$pdf->Cell(70, 0,'' .$sql[GolDarah].' ['.$sql[Rhesus].']',0, 0,'L');
    
//Close and output PDF document
$pdf->Output('idcard.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+
?>

