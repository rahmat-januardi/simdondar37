<?php
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
$pdf->SetMargins(false);
//$pdf->SetMargins(0,0,0);
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

// set font
$pdf->SetFont('helvetica', '', 10);

require_once('config/koneksi.php');
$nktnat     = $_GET['noKantong'];
$noktnat    = substr_replace($nktnat,'A',-1,1);

//-------------------- add a page ------------------------
$hasil=mysql_query("SELECT  `rnokantong`, round(`rvolume`,0) as volume,
                    `rproduk`, `rgolda`,DATE_FORMAT(`rtgl_aftap`, '%d-%m-%Y') as tglaftap,
                    DATE_FORMAT(`rtgl_olah`, '%d-%m-%Y') as tglolah ,
                    DATE_FORMAT(`rtgl_ed`, '%d-%m-%Y %H:%i') as tgled,rstatus,rsatus_ket
                    FROM `release` WHERE rnokantong='$_GET[noKantong]' order by rid DESC");
$hasil1=mysql_fetch_assoc($hasil);
//$pdf->AddPage('P','IDH'); ==> original
$pdf->AddPage('L','QA');
$pdf->SetFillColor(255,255,255);
$udd=mysql_fetch_assoc(mysql_query("select nama,alamat from utd where down='1' and aktif='1'"));
$abs=mysql_fetch_assoc(mysql_query("select abs, noSelang from stokkantong where noKantong='$_GET[noKantong]'"));
$nat=mysql_num_rows(mysql_query("select noKantong from hasilnat where noKantong='$noktnat'"));
$pdf->SetFont('helvetica', 'b', 12);
$pdf->SetXY(4,2);$pdf->Cell(0, 0,$udd[nama],0, 1, 'L');
$pdf->SetXY(4,7);$pdf->write1DBarcode(strtoupper($hasil1[rnokantong]), 'C39', '', '', '', 8, 0.28, $style, 'N');
$pdf->SetFont('helvetica', 'b', 9);
$pdf->SetXY(4,10);$pdf->Cell(0, 16, 'No Kantong:', 0,1,'L');
$pdf->SetXY(24,12);$pdf->SetFont('helvetica', 'b', 12);
$pdf->Cell(0, 12, strtoupper($hasil1[rnokantong]), 0,1,'L');


$selang = $abs['noSelang'];
if ($selang ==""){$selang = $nktnat;}
$pdf->SetFont('helvetica','',8);
$pdf->SetXY(4,14);$pdf->Cell(0, 16, 'No Selang : '.$selang, 0,1,'L');

//Garis
$style2 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
$pdf->Rect(75, 2, 8, 5, 'D', array('all' => $style2)); // Persegi Panjang QC
$pdf->Rect(83, 2, 15, 5, 'D', array('all' => $style2)); // Persegi Panjang Status QC

//Jika Status Lulus
if ($hasil1[rstatus]=='0'){
    
    $pdf->SetFont('helvetica', 'b', 10);
    $pdf->SetXY(75,2);$pdf->Cell(0, 0,'QA',0, 1, 'L');
    $pdf->SetFont('helvetica', 'b', 8);
    $pdf->SetXY(84,2.5);$pdf->Cell(0, 0,'LULUS',0, 1, 'L');
}else{
    $pdf->SetFont('helvetica', 'b', 10);
    $pdf->SetXY(75,2);$pdf->Cell(0, 0,'QA',0, 1, 'L');
    $pdf->SetFont('helvetica', 'b', 5.5);
    $pdf->SetXY(83,3);$pdf->Cell(0, 0,'TIDAK LULUS',0, 1, 'L');
}
                   


//info NAT
if ($nat =="1"){
    $pdf->SetFont('helvetica', 'b', 8);
    $pdf->SetXY(72,8);$pdf->Cell(0, 0,'NAT |',0, 1, 'L');
    $pdf->SetFont('helvetica',  6);
    $pdf->SetXY(80,8);$pdf->Cell(0, 0,'Non Reactive',0, 1, 'L');
}

//produk, vol, Golda
if (strlen($hasil1[rproduk])>5){
    
    $pdf->SetXY(4,24);$pdf->SetFont('helvetica', 'b', 10);
    $pdf->Cell(40, 0,$hasil1[rproduk], 0, 5, 'C');
}else{
    $pdf->SetXY(4,24);$pdf->SetFont('helvetica', 'b', 12);
    $pdf->Cell(40, 0,$hasil1[rproduk], 0, 5, 'C');
}
$pdf->SetXY(4,28);$pdf->SetFont('helvetica', 'b', 16);
$pdf->Cell(40, 0, $hasil1[volume].' ml', 0, 5, 'C');
$pdf->SetXY(4,32);$pdf->SetFont('helvetica', 'b', 40);
$pdf->Cell(40, 0,$hasil1[rgolda], 0, 5, 'C');

    
//IMLTD
$pdf->SetXY(45,20);$pdf->SetFont('helvetica','',8);
$pdf->Cell(0, 0, 'Non Reaktif terhadap:', 0,1);
$pdf->SetXY(45,23);
$pdf->Cell(0, 0, 'Anti HIV, Anti HCV, HBsAg & Syphilis', 0,1);
$pdf->SetLineWidth(0.1);$pdf->Line(45,28,99,28);
    

//Informasi Tanggal
$pdf->SetFont('helvetica', 10);
$pdf->SetXY(45,29);    $pdf->Cell(0, 0, 'Aftap', 0);
$pdf->SetXY(59,29);    $pdf->Cell(0, 0, ':', 0);
$pdf->SetXY(62,29);    $pdf->Cell(0, 0, $hasil1[tglaftap], 0);
$pdf->SetFont('helvetica', 10);
$pdf->SetXY(45,33);    $pdf->Cell(0, 0, 'Produksi', 0);
$pdf->SetXY(59,33);    $pdf->Cell(0, 0, ':', 0);
if ($hasil1[rproduk]=='WB'){
$pdf->SetXY(62,33);    $pdf->Cell(0, 0, 'Tidak Ada', 0);
                   } else {
$pdf->SetXY(62,33);    $pdf->Cell(0, 0, $hasil1[tglolah], 0);
                   }
$pdf->SetFont('helvetica', 10);
$pdf->SetXY(45,37);    $pdf->Cell(0, 0, 'ED', 0);
$pdf->SetXY(59,37);    $pdf->Cell(0, 0, ':', 0);
$pdf->SetXY(62,37);    $pdf->Cell(0, 0, $hasil1[tgled], 0);

//Info ABS
$pdf->SetFont('helvetica', 10);
$pdf->SetXY(45,41);    $pdf->Cell(0, 0, 'ABS', 0);
$pdf->SetXY(59,41);    $pdf->Cell(0, 0, ':', 0);
$pdf->SetXY(62,41);    $pdf->Cell(0, 0, $abs[abs], 0);


//Informasi Penyimpanan
$pdf->SetXY(45,44);    $pdf->SetFont('helvetica','', 9);
$pdf->Cell(0, 0, 'Simpan pada Suhu', 0);
$pdf->SetXY(78,44);    $pdf->SetFont('helvetica','b', 9);
if (($hasil1[rproduk]=='TC') or ($hasil1[rproduk]=='TC Aferesis')) {
          $pdf->writeHTML(': 20-24<sup>o</sup>C', true, 0, true, 0);
} elseif ($hasil1[rproduk]=='FFP'){
        $pdf->writeHTML(': -30<sup>o</sup>C', true, 0, true, 0);
} elseif ($hasil1[rproduk]=='FFP Konvalesen'){
        $pdf->writeHTML(': -30<sup>o</sup>C', true, 0, true, 0);
} elseif ($hasil1[rproduk]=='Plasmaconcurrent'){
        $pdf->writeHTML(': -20<sup>o</sup>C', true, 0, true, 0);
} elseif ($hasil1[rproduk]=='Plasmapheresis'){
        $pdf->writeHTML(': -20<sup>o</sup>C', true, 0, true, 0);
} else {
           $pdf->writeHTML(': 2-6<sup>o</sup>C', true, 0, true, 0);
}
$pdf->Output('label_release.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>
