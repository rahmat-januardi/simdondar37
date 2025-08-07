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

//-------------------- add a page ------------------------
$hasil=mysql_query("select * from stokkantong where NoKantong='$_GET[noKantong]'");
while($hasil1=mysql_fetch_assoc($hasil)){
	$kantong1=mysql_fetch_assoc(mysql_query("select * from stokkantong where noKantong='$hasil1[NoKantong]'"));
	$pdf->AddPage('P','IDH');
	$pdf->SetFillColor(255,255,255);
	$udd=mysql_fetch_assoc(mysql_query("select nama,alamat from utd where down='1' and aktif='1'"));
	$pdf->SetFont('helvetica', '',11);
	$pdf->SetXY(0,0);
	$pdf->Cell(0, 12,'PALANG MERAH INDONESIA',0, 1, 'C');
	$pdf->SetFont('helvetica', 'b', 10);
	$pdf->SetXY(0,4);
	$pdf->Cell(0, 12,$udd[nama],0, 1, 'C');
	$pdf->SetFont('helvetica', '', 7);
	$pdf->SetXY(0,7);
	$pdf->Cell(0, 12,$udd[alamat],0, 1, 'C');

	$pdf->SetLineWidth(0.4);
	$pdf->Line(0,15,110,15);

	$pdf->SetXY(5,14);
	$pdf->SetFont('helvetica', 'b', 9);
	$pdf->Cell(0, 12, 'No Kantong', 0,1,'L');
	$pdf->SetXY(25,15.5);
	$pdf->write1DBarcode($hasil1[noKantong], 'C39', '', '', '', 8, 0.28, $style, 'N');
	
	$pdf->SetXY(25,21);
	$pdf->SetFont('helvetica', 'b', 12);
	$pdf->Cell(0, 10, $hasil1[noKantong], 0,1,'L');

	//produk
	$pdf->SetXY(4,30);
	$pdf->SetFont('helvetica', 'b', 12);
	$pdf->Cell(0, 0, 'Produk', 0);
	$pdf->SetXY(20,30);
	$pdf->SetFont('helvetica', 'b', 12);
	$pdf->Cell(0, 0, ':', 0);
	$pdf->SetXY(24,28);
	$pdf->SetFont('helvetica', 'b', 22);
	$pdf->Cell(0, 0,$hasil1[produk], 0);

	//Volume
	$pdf->SetXY(4,36);
	$pdf->SetFont('helvetica', 'b', 12);
	$pdf->Cell(0, 0, 'Volume', 0);
	$pdf->SetXY(20,36);
	$pdf->SetFont('helvetica', 'b', 12);
	$pdf->Cell(0, 0, ':', 0);
	$pdf->SetXY(24,36);
	$pdf->SetFont('helvetica', 'b', 14);
	$pdf->Cell(0, 0, $hasil1[volume].' ml', 0);
	//Golda
	$pdf->SetXY(4,42);
	$pdf->SetFont('helvetica', 'b', 12);
	$pdf->Cell(0, 0, 'Gol,Rh', 0);
	$pdf->SetXY(20,42);
	$pdf->SetFont('helvetica', 'b', 12);
	$pdf->Cell(0, 0, ':', 0);
	$pdf->SetXY(22,40);
	$pdf->SetFont('helvetica', 'b', 40);
	$pdf->Cell(0, 0,$hasil1[gol_darah].''.$hasil1[RhesusDrh], 0);

	
	//IMLTD
	$pdf->SetXY(54,30);
	$pdf->SetFont('helvetica','bu', 9);
	$pdf->Cell(0, 0, 'Non Reaktif terhadap:', 0,1);
	$pdf->SetFont('helvetica','', 9);
	$pdf->SetXY(54,34);	$pdf->Cell(0, 0, '1. Anti HIV', 0);
	$pdf->SetXY(54,37);	$pdf->Cell(0, 0, '2. Anti HCV', 0);
	$pdf->SetXY(74,34);	$pdf->Cell(0, 0, '3. HBsAg', 0);
	$pdf->SetXY(74,37);	$pdf->Cell(0, 0, '4. Shypilis', 0);
	
	//Informasi Tanggal
	$tgl_aftap=$hasil1[tgl_Aftap];
	$tgl1=date("d",strtotime($tgl_aftap));
	$bln1=date("n",strtotime($tgl_aftap));
	$thn1=date("Y",strtotime($tgl_aftap));
	$bulan=array(1=>"Jan","Feb","Mar","Apr","Mei","Juni","Juli","Agust","Sept","Okt","Nov","Des");
	$bln11=$bulan[$bln1];
	$jam = date("H:i",strtotime($tgl_aftap));

	$pdf->SetFont('helvetica', 9);
	$pdf->SetXY(54,42);	$pdf->Cell(0, 0, 'Aftap', 0);
	$pdf->SetXY(63,42);	$pdf->Cell(0, 0, ':', 0);
	$pdf->SetXY(65,42);	$pdf->Cell(0, 0, $tgl1.' ' .$bln11. ' ' .$thn1. ' ' .$jam, 0);
	
	$tgl_olah=$hasil1[tglpengolahan];
	$tgl3=date("d",strtotime($tgl_olah));
	$bln3=date("n",strtotime($tgl_olah));
	$thn3=date("Y",strtotime($tgl_olah));
	$bulan=array(1=>"Jan","Feb","Mar","Apr","Mei","Juni","Juli","Agust","Sept","Okt","Nov","Des");
	$bln33=$bulan[$bln3];
	$jam3 = date("H:i",strtotime($tgl_olah));
	
	$pdf->SetFont('helvetica', 9);
	$pdf->SetXY(54,46);	$pdf->Cell(0, 0, 'Diolah', 0);
	$pdf->SetXY(63,46);	$pdf->Cell(0, 0, ':', 0);
	$pdf->SetXY(65,46);	$pdf->Cell(0, 0, $tgl3.' ' .$bln33. ' ' .$thn3. ' ' .$jam3, 0);
		

	$kadaluwarsa=$hasil1[kadaluwarsa];
	$tgl2=date("d",strtotime($kadaluwarsa));
	$bln2=date("n",strtotime($kadaluwarsa));
	$thn2=date("Y",strtotime($kadaluwarsa));
	$bulan=array(1=>"Jan","Feb","Mar","Apr","Mei","Juni","Juli","Agust","Sept","Okt","Nov","Des");
	$bln22=$bulan[$bln2];
	$jam2 = date("H:i",strtotime($kadaluwarsa));

	$pdf->SetFont('helvetica', 9);
	$pdf->SetXY(54,50);	$pdf->Cell(0, 0, 'ED', 0);
	$pdf->SetXY(63,50);	$pdf->Cell(0, 0, ':', 0);
	$pdf->SetXY(65,50);	$pdf->Cell(0, 0, $tgl2.' ' .$bln22. ' ' .$thn2. ' ' .$jam2, 0);
	//Informasi Penyimpanan
	$pdf->SetXY(54,54);
	$pdf->SetFont('helvetica','', 9);
	$pdf->Cell(0, 0, 'Simpan pada Suhu', 0);
	$pdf->SetXY(84,54);
	$pdf->SetFont('helvetica','b', 9);
	if ($hasil1[produk]=='TC') {
   		$pdf->writeHTML(': 20-24<sup>o</sup>C', true, 0, true, 0);
	} elseif ($hasil1[produk]=='FFP'){
   		$pdf->writeHTML(': -20<sup>o</sup>C', true, 0, true, 0);
	} else {
   		$pdf->writeHTML(': 2-6<sup>o</sup>C', true, 0, true, 0);
	}
	$pdf->SetLineWidth(0.4);
	$pdf->Line(0,60,110,60);
	$pdf->SetXY(0,60); 
	$pdf->SetFont('helvetica','i', 7);
	$pdf->Cell(0, 0,'Harap Diperiksa kembali sebelum ditransfusikan', 0,1,'L');
	$pdf->SetXY(0,60); 
	$pdf->SetFont('helvetica','', 6);
	$pdf->Cell(0, 0,'No.Dok.:UTD BALI-RLS-L3-001', 0,1,'R');
}

$pdf->Output('idcross.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>
