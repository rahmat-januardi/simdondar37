<?php

session_start();
$namauser=$_SESSION[namauser];
$nama_lengkap=$_SESSION[nama_lengkap];
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('RedcrossDev');
$pdf->SetTitle('Formulir Donor');
$pdf->SetSubject('Formulir');
$pdf->SetKeywords('Simudda, PDF, formulir, donor, pmi, Indonesia');

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
$pdf->SetMargins(0, 0, 0);
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
$pdf->AddPage('L','F4'); // L untuk Landscape P untuk Potrait

// define barcode style
$style = array(
	'position' => '',
	'border' => false,
	'padding' => 'auto',
	'fgcolor' => array(0,0,0),
	'bgcolor' => false, //array(255,255,255),
	'text' => true,
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 4
);

// Garis Putus Putus //
$style1 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,20,5,10', 'phase' => 10, 'color' => array(255, 0, 0));
// Garis Biasa //
$style2 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
// Garis Titik Titik //
$style3 = array('width' => 1, 'cap' => 'round', 'join' => 'round', 'dash' => '2,10', 'color' => array(255, 0, 0));
$style4 = array('L' => 0,
    'T' => array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => '20,10', 'phase' => 10, 'color' => array(100, 100, 255)),
    'R' => array('width' => 0.50, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50, 50, 127)),
    'B' => array('width' => 0.75, 'cap' => 'square', 'join' => 'miter', 'dash' => '30,10,5,10'));
$style5 = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 64, 128));
$style6 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,10', 'color' => array(0, 128, 0));
$style7 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 128, 0));

// PRINT VARIOUS 1D BARCODES

// CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
require_once('config/koneksi.php');
$bulan=array(1=>"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
$skg=DATE("Y-m-d");
$thn_skrg=DATE("Y");
$bln_skrg=DATE("m");
$tgl_skrg=DATE("d");
$namaudd=$_SESSION['namaudd'];
$bs=DATE("n",strtotime(DATE("Y-m-d")));
$sekarang=$tgl_skrg.' '.$bulan[$bs].' '.$thn_skrg;

$pdr=mysql_query("select * from pendonor where Kode='$_GET[kp]'");
$notrans0=mysql_query("select * from htransaksi where KodePendonor='$_GET[kp]' order by Tgl DESC limit 1");
$htrans0=mysql_query("select donorke from htransaksi where KodePendonor='$_GET[kp]' order by Tgl DESC limit 1");

if ($notrans0) $notrans=mysql_fetch_assoc($notrans0);
if ($pdr) $pd=mysql_fetch_assoc($pdr);
if ($htrans0) $htrans=mysql_fetch_assoc($htrans0);

$tgl=date("d",strtotime(DATE("Y-m-d")));
$bln=date("n",strtotime(DATE("Y-m-d")));
$thn=date("Y",strtotime(DATE("Y-m-d")));
$bln=$bulan[$bln];

$tgll=date("d",strtotime($pd[TglLhr]));
$blnl=date("n",strtotime($pd[TglLhr]));
$thnl=date("Y",strtotime($pd[TglLhr]));
$blnl=$bulan[$blnl];

$tgl_lahir=$pd[TglLhr];
$thn_lhr=substr($pd[TglLhr],0,4);
$bln_lhr=substr($pd[TglLhr],6,2);
$tgl_lhr=substr($pd[TglLhr],8,2);

if($pd[Jk]=='0'){$jk="Laki-Laki";}else{$jk="Perempuan";}
if($pd[Status]=='0'){$nikah="Belum Menikah";}else{$nikah="Menikah";}
if($pd[Call]=='0'){$call="Tidak";}else{$call="Dapat";}

$tt=mysql_query("SELECT substr(tglpembuatan,12,8) as waktu, tglpembuatan, user FROM user_komponen WHERE nokantong='$_GET[noKantong]' ORDER BY tglpembuatan DESC LIMIT 1");
$tglproses=mysql_fetch_assoc($tt);
if(mysql_num_rows($tt)<1){
$petugas=$_SESSION['namauser'];
}else{
$petugas=$tglproses[user];
}
$ptt=$_GET['usr'];

$udd=mysql_fetch_assoc(mysql_query("select nama,alamat,telp,daerah from utd where down='1' and aktif='1'"));
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY(10,1);
$pdf->Cell(0, 14,$udd[nama],0, 10, 'L');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetXY(10,1);
$pdf->Cell(10, 24,'Formulir Kuesioner dan Informed Consent Donor',0, 10, 'L');
$pdf->Rect(10, 18, 310, 0, 'D', array('all' => $style2)); // garis atas panjang

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(298,1);
$pdf->Cell(0, 28,'Halaman 1 dari 2',0, 1, 'L');

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(10,1);
$pdf->Cell(0, 40,'Selamat Datang, Terima Kasih Atas Kesediaan Anda Meluangkan Waktu Untuk Menyumbangkan Darah',0, 1, 'C');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,1);
$pdf->Cell(0, 47,'Mohon Formulir ini Diisi Dengan Sejujurnya Untuk Keselamatan Anda dan Calon Penerima Darah Anda',0, 1, 'C');

//$pdf->Rect(10, 28, 310, 0, 'D', array('all' => $style2)); // garis atas panjang
$pdf->Rect(10, 73, 310, 0, 'D', array('all' => $style2)); // garis atas Kuesioner
$pdf->Rect(170, 73, 0, 107, 'D', array('all' => $style2)); // Garis Lurus Tengah
$pdf->Rect(10, 73, 0, 134, 'D', array('all' => $style2)); // Garis Lurus kiri
$pdf->Rect(320, 73, 0, 134, 'D', array('all' => $style2)); // Garis Lurus kanan
$pdf->Rect(10, 180, 310, 0, 'D', array('all' => $style2)); // garis bawah panjang
$pdf->Rect(10, 207, 310, 0, 'D', array('all' => $style2)); // garis bawah2 panjang

$pdf->Rect(10, 28, 40, 7, 'D', array('all' => $style2)); // Persegi Panjang tempat penyumbangan
$pdf->Rect(50, 28, 270, 7, 'D', array('all' => $style2)); // Persegi Panjang tengah setelah tempat penyumbangan
$pdf->Rect(200, 28, 40, 7, 'D', array('all' => $style2)); // Persegi Panjang tanggal

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(10,7);
$pdf->Cell(0, 49, 'Tempat Penyumbangan :');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(50,7);
$pdf->Cell(0, 49,' '.$udd[nama]);

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(200,7);
$pdf->Cell(0, 49, 'Tanggal Donor : ');
$pdf->SetXY(240,7);
$pdf->Cell(0, 49, ' '.$sekarang);

//Barcode Transaksi
$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(283,32);
$pdf->Cell(0, 44, 'No.Transaksi Donor :');
$pdf->SetXY(273,55);
$pdf->write1DBarcode($notrans[NoTrans], 'C128', '', '', 48, 15, '', $style, 'N');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(10,16);
$pdf->Cell(0, 44, 'No.KTP');
$pdf->SetXY(45,16);
$pdf->Cell(0, 44, ':  '.$pd[NoKTP]);

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(140,16);
$pdf->Cell(0,44, 'No. Kartu Donor');
$pdf->SetXY(168,16);
$pdf->Cell(0, 44, ': '.$pd[Kode]);

$pdf->SetXY(140,16);
$pdf->Cell(0, 52, 'Pekerjaan');
$pdf->SetXY(168,16);
$pdf->Cell(0, 52, ':  '.$pd[Pekerjaan]);

$pdf->SetXY(140,16);
$pdf->Cell(0, 60, 'Alamat');
$pdf->SetXY(168,16);
$pdf->Cell(0, 60, ':  '.$pd[Alamat].', Kel. '.$pd[kelurahan].', Kec.'.$pd[kecamatan].', '.$pd[wilayah]);

$pdf->SetXY(140,16);
$pdf->Cell(0, 68, 'Nomor');
$pdf->SetXY(168,16);
$pdf->Cell(0, 68, ': Telp : '.$pd[telp].', HP : '.$pd[telp2]);

$pdf->SetXY(10,19);
$pdf->Cell(0, 45, 'Nama Lengkap Donor');
$pdf->SetXY(45,19);
$pdf->Cell(0, 45, ':  '.$pd[Nama]);

$pdf->SetXY(10,19);
$pdf->Cell(0, 52, 'Tempat, Tanggal Lahir');
$pdf->SetXY(45,19);
$pdf->Cell(0, 52, ':  '.$pd[TempatLhr].', '.$tgll.' '.$blnl.' '.$thnl);

$pdf->SetXY(10,19);
$pdf->Cell(0, 60, 'Umur');
$pdf->SetXY(45,19);
$pdf->Cell(0, 60, ':  '.$pd[umur]. ' tahun');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(10,19);
$pdf->Cell(0, 68, 'Jenis Kelamin');
$pdf->SetXY(45,19);
$pdf->Cell(0, 68, ':  '.$jk);

$pdf->SetXY(10,18);
$pdf->Cell(0, 80, 'Penghargaan yang telah diterima :');

$pdf->Rect(65, 56.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox 10X

$pdf->SetXY(68,18);
$pdf->Cell(0, 80, '10X');

$pdf->Rect(78, 56.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox 25X

$pdf->SetXY(82,18);
$pdf->Cell(0, 80, '25X');

$pdf->Rect(91, 56.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox 50X

$pdf->SetXY(95,18);
$pdf->Cell(0, 80, '50X');

$pdf->Rect(104, 56.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox 50X

$pdf->SetXY(108,18);
$pdf->Cell(0, 80, '75X');

$pdf->Rect(117, 56.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox 50X

$pdf->SetXY(121,18);
$pdf->Cell(0, 80, '100X');

$pdf->SetXY(10,18);
$pdf->Cell(0, 88, 'Bersediakah saudara donor pada waktu bulan puasa :');

$pdf->Rect(91, 60.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox ya


$pdf->SetXY(95,18);
$pdf->Cell(0, 88, 'Ya');

$pdf->Rect(104, 60.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox ya

$pdf->SetXY(108,18);
$pdf->Cell(0, 88, 'Tidak');

$pdf->SetXY(10,18);
$pdf->Cell(0, 96, 'Bersediakah saudara donor saat dibutuhkan untuk komponen darah tertentu :');

$pdf->Rect(124, 64.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox Ya

$pdf->SetXY(128,18);
$pdf->Cell(0, 96, 'Ya');

$pdf->Rect(140, 64.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Checkbox Tidak

$pdf->SetXY(142,18);
$pdf->Cell(0, 96, 'Tidak');

$pdf->SetXY(10,18);
$pdf->Cell(0, 104, 'Donor yang terakhir tanggal : ............................................ sekarang donor yang ke :  '.$htrans[donorke].'          kali');


//Keterangan Jawaban
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(216,67);
$pdf->Cell(0, 0,'Pilih dan Lengkapi Jawaban Anda dengan Tanda Centang  V  ',0, 1, 'L');
$pdf->Rect(315, 67.5, 4, 4, 'D', array('all' => $gariskecil));


//Kuesioner


$pdf->SetFont('helvetica', 'B',8 );
$pdf->SetXY(10,64);
$pdf->Cell(10, 24,'Dalam Hari Ini :',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(128,64);
$pdf->Cell(10, 24,'  YA    TIDAK  Diisi Petugas:',0, 10, 'L');

//CHECKBOX 1
$pdf->Rect(132, 78, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 78, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 82, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 82, 2.5, 2.5, 'D', array('all' => $gariskecil)); 
$pdf->Rect(132, 88, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 88, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 92, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 92, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 98, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 98, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 106, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 106, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 112, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 112, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 118, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 118, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 123, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 123, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 129, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 129, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 140, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 140, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 147, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 147, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 154, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 154, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 158, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 158, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 162, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 162, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 166, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 166, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 172, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 172, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(132, 176, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(140, 176, 2.5, 2.5, 'D', array('all' => $gariskecil));

//CHECKBOX 2
$pdf->Rect(284, 78, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 78, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 84, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 84, 2.5, 2.5, 'D', array('all' => $gariskecil)); 
$pdf->Rect(284, 88, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 88, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 95, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 95, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 99, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 99, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 103, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 103, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 107, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 107, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 112, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 112, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 116, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 116, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 120, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 120, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 123, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 123, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 127, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 127, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 133, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 133, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 137, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 137, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 140.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 140.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 144, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 144, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 147.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 147.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 151, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 151, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 154.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 154.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 158, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 158, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 161.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 161.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 165, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 165, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 168.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 168.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 172, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 172, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(284, 175.5, 2.5, 2.5, 'D', array('all' => $gariskecil));
$pdf->Rect(292, 175.5, 2.5, 2.5, 'D', array('all' => $gariskecil));


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,67.5);
$pdf->Cell(10, 24,'1. Merasa sehat, tidak sedang flu/ batuk/ demam/ pusing ? .....................................................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,67.5);
$pdf->Cell(10, 24,$notrans[satu],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,67.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,71);
$pdf->Cell(10, 24,'2. Apakah Anda semalam tidur minimal 4 jam ?........................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,71);
$pdf->Cell(10, 24,$notrans[dua],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,71);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(10,74.5);
$pdf->Cell(10, 24,'Dalam 3 Hari Terakhir',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,78);
$pdf->Cell(10, 24,'3. Apakah Anda sedang minum obat ?......................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,78);
$pdf->Cell(10, 24,$notrans[tiga],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,78);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,81.5);
$pdf->Cell(10, 24,'4. Apakah Anda sedang minum Jamu ?....................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,81.5);
$pdf->Cell(10, 24,$notrans[empat],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,81.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(10,85);
$pdf->Cell(10, 24,'Dalam waktu 1 minggu terakhir',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,88.5);
$pdf->Cell(10, 24,'5. Apakah Anda mencabut gigi ? ..............................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,88.5);
$pdf->Cell(10, 24,$notrans[lima],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,88.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(10,92);
$pdf->Cell(10, 24,'Dalam 2 Minggu Terakhir',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,95.5);
$pdf->Cell(10, 24,'6. Apakah Anda mengalami demam
lebih dari 38 derajat celcius? ..........................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,95.5);
$pdf->Cell(10, 24,$notrans[enam],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,95.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(10,98.5);
$pdf->Cell(10, 24,'Dalam 6 Minggu Terakhir',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,101.5);
$pdf->Cell(10, 24,'7. Wanita: Apakah saat ini Anda sedang/pernah hamil?...........................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,101.5);
$pdf->Cell(10, 24,$notrans[tujuh],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,101.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(10,105);
$pdf->Cell(10, 24,'Dalam 2 Bulan Terakhir',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,108.5);
$pdf->Cell(10, 24,'8. Apakah Anda mendonorkan darah,
trombosit atau plasma ? ............................................... ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,108.5);
$pdf->Cell(10, 24,$notrans[delapan],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,108.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');



$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,112);
$pdf->Cell(10, 24,'9. Apakah Anda menerima vaksinasi
atau suntikan lain ? ........................................................ ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,112);
$pdf->Cell(10, 24,$notrans[sembilan],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,112);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,115.5);
$pdf->Cell(10, 24,'10. Apakah Anda pernah kontak dengan
orang yang pernah',0, 10, 'L');



$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(14,119);
$pdf->Cell(10, 24,'- menerima vaksinasi smallpox ?..........................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,119);
$pdf->Cell(10, 24,$notrans[sepuluh],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,119);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(10,122.5);
$pdf->Cell(10, 24,'Dalam 4 Bulan Terakhir',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,126);
$pdf->Cell(10, 24,'11. Apakah Anda mendonorkan 2 kantong
sel darah merah',0, 10, 'L');



$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(14,129.5);
$pdf->Cell(10, 24,'-  melalui proses Aferesis ?..................................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,129.5);
$pdf->Cell(10, 24,$notrans[sebls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,129.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(10,133);
$pdf->Cell(10, 24,'Dalam 6 Bulan Terakhir',0, 10, 'L');



$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,136.5);
$pdf->Cell(10, 24,'12. Wanita: Apakah Anda saat ini sedang menyusui?..............................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,136.5);
$pdf->Cell(10, 24,$notrans[duabls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,136.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(10,140.5);
$pdf->Cell(10, 24,'Dalam 1 Tahun terakhir',0, 10, 'L');



$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,144);
$pdf->Cell(10, 24,'13. Apakah Anda pernah menerima tranfusi darah ?................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,144);
$pdf->Cell(10, 24,$notrans[tigabls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,144);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,147.5);
$pdf->Cell(10, 24,'14. Apakah Anda pernah mendapat transplatasi organ?..........................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,147.5);
$pdf->Cell(10, 24,$notrans[empatbls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,147.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,151);
$pdf->Cell(10, 24,'15. Apakah Anda pernah cangkok tulang untuk kulit ?..............................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,151);
$pdf->Cell(10, 24,$notrans[limabls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,151);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,154.5);
$pdf->Cell(10, 24,'16. Apakah Anda pernah tusuk jarum medis tanpa sengaja ? .................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,154.5);
$pdf->Cell(10, 24,$notrans[enambls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,154.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,158);
$pdf->Cell(10, 24,'17. Apakah Anda pernah berhubungan seksual
dengan',0, 10, 'L');


$pdf->SetFont('helvetica', '', 7.5);
$pdf->SetXY(14,161.5);
$pdf->Cell(10, 24,'- dengan orang dengan HIV/AIDS ?..............................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,161.5);
$pdf->Cell(10, 24,$notrans[tujuhbls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,161.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,165);
$pdf->Cell(10, 24,'18. Apakah Anda pernah behubungan seksual dengan PSK ?................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(130,165);
$pdf->Cell(10, 24,$notrans[delapanbls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(148,165);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 7);
$pdf->SetXY(10,176);
$pdf->Cell(0, 14,'Yth. '.$udd[nama],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,180);
$pdf->Cell(0, 14,'Saya telah mendapatkan dan membaca semua informasi yang diberikan serta menjawab pertanyaan dengan jujur. Saya mengerti dan bersedia ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,184);
$pdf->Cell(0, 14,'meyumbangkan darah dengan volume sesuai standar yang diberlakukan dan setuju diambil contoh darahnya untuk keperluan pemeriksaan ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,188);
$pdf->Cell(0, 14,'laboratorium berupa uji golongan darah, HIV, Hepatitis B, Hepatitis C, Sifilis dan infeksi lainnya yang diperlukan saya serta untuk kepentingan ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,192);
$pdf->Cell(0, 14,'penelitian. Bila ternyata hasil pemeriksaan laboratorium perlu ditindaklanjuti, maka saya setuju untuk diberi kabar tertulis. Jika komponen plasma ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(10,196);
$pdf->Cell(0, 14,'tidak terpakai untuk transfusi, saya setuju dapat dijadikan produk plasma untuk pengobatan',0, 10, 'L');


//BARIS 2

$pdf->SetFont('helvetica', 'B',8 );
$pdf->SetXY(171,64);
$pdf->Cell(10, 24,'Dalam 1 Tahun terakhir',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(280,64);
$pdf->Cell(10, 24,'  YA    TIDAK  Diisi Petugas:',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,67.5);
$pdf->Cell(10, 24,'19. Pernah berhubungan seksual
dengan pengguna narkoba jarum suntik ?................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,67.5);
$pdf->Cell(10, 24,$notrans[sembilanbls],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,67.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,71);
$pdf->Cell(10, 24,'20. Apakah Anda pernah berhubungan dengan pengguna ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(176,74.5);
$pdf->Cell(10, 24,'konsentrat faktor pembekuan ?................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,74.5);
$pdf->Cell(10, 24,$notrans[duapuluh],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,74.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,78);
$pdf->Cell(10, 24,'21. Wanita : Apakah Anda pernah
berhubungan seksual dengan laki-laki
biseksual?...',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,78);
$pdf->Cell(10, 24,$notrans[duasatu],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,78);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,81.5);
$pdf->Cell(10, 24,'22. Apakah Anda pernah berhubungan seksual
dengan',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(176,85);
$pdf->Cell(10, 24,'orang penderita hepatitis ?.......................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,85);
$pdf->Cell(10, 24,$notrans[duadua],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,85);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,88.5);
$pdf->Cell(10, 24,'23. Apakah Anda pernah tinggal bersama
penderita hepatitis ?....................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,88.5);
$pdf->Cell(10, 24,$notrans[duatiga],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,88.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,92);
$pdf->Cell(10, 24,'24. Apakah Anda memiliki tatto ?...................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,92);
$pdf->Cell(10, 24,$notrans[duaempat],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,92);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,95.5);
$pdf->Cell(10, 24,'25. Apakah Anda menindik telinga atau bagian
tubuh lainnya ?....................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,95.5);
$pdf->Cell(10, 24,$notrans[dualima],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,95.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,98.5);
$pdf->Cell(10, 24,'26. Apakah Anda sedang atau pernah mendapatkan
pengobatan',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(175,101.5);
$pdf->Cell(10, 24,' Sifilis atau GO ( kencing nanah ) ?...........................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,101.5);
$pdf->Cell(10, 24,$notrans[duaenam],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,101.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,105);
$pdf->Cell(10, 24,'27. Apakah Anda pernah ditahan / dipenjara
dalam waktu 72 jam ?.............................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,105);
$pdf->Cell(10, 24,$notrans[duatujuh],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,105);
$pdf->Cell(10, 24,'.................',0, 10, 'L');



$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,108.5);
$pdf->Cell(10, 24,'28. Apakah Anda pernah berada di luar wilayah
Indonesia?.........................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,108.5);
$pdf->Cell(10, 24,$notrans[duadelapan],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,108.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');



$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,112);
$pdf->Cell(10, 24,'29. Apakah Anda menerima uang, obat atau pembayaran lainnya untuk seks ?..........',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,112);
$pdf->Cell(10, 24,$notrans[duasembilan],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,112);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,115.5);
$pdf->Cell(10, 24,'30. Laki-laki : Apakah Anda pernah berhubungan seks dengan laki-laki?.....................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,115.5);
$pdf->Cell(10, 24,$notrans[tigapuluh],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,115.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(171,119);
$pdf->Cell(10, 24,'Tahun 1980 hingga sekarang',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,122.5);
$pdf->Cell(10, 24,'31. Tinggal selama 5 tahun
atau lebih di eropa ?...........................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,122.5);
$pdf->Cell(10, 24,$notrans[tigasatu],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,122.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,126);
$pdf->Cell(10, 24,'32. Pernah menerima tranfusi
darah di Inggris ?...........................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,126);
$pdf->Cell(10, 24,$notrans[tigadua],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,126);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,129.5);
$pdf->Cell(10, 24,'33. Tinggal selama 3 bulan atau
lebih di Inggris ?.........................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,129.5);
$pdf->Cell(10, 24,$notrans[tigatiga],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,129.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,133);
$pdf->Cell(10, 24,'34. Mendapat hasil positif untuk test HIV/AIDS ?..........................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,133);
$pdf->Cell(10, 24,$notrans[tigaempat],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,133);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,136.5);
$pdf->Cell(10, 24,'35. Menggunakan jarum suntik untuk obat-obatan?......................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,136.5);
$pdf->Cell(10, 24,$notrans[tigalima],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,136.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,140.5);
$pdf->Cell(10, 24,'36. Menggunakan konsentrat faktor pembekuan ?........................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,140.5);
$pdf->Cell(10, 24,$notrans[tigaenam],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,140.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,144);
$pdf->Cell(10, 24,'37. Menderita Hepatitis ?...............................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,144);
$pdf->Cell(10, 24,$notrans[tigatujuh],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,144);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,147.5);
$pdf->Cell(10, 24,'38. Menderita Malaria ?.................................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,147.5);
$pdf->Cell(10, 24,$notrans[tigadelapan],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,147.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,151);
$pdf->Cell(10, 24,'39. Menderita kanker ?..................................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,151);
$pdf->Cell(10, 24,$notrans[tigasembilan],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,151);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,154.5);
$pdf->Cell(10, 24,'40. Bermasalah dengan jantung dan paru-paru ?.........................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,154.5);
$pdf->Cell(10, 24,$notrans[empatpuluh],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,154.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,158);
$pdf->Cell(10, 24,'41. Menderita pendarahan atau penyakit berhubungan dengan darah ?......................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,158);
$pdf->Cell(10, 24,$notrans[empatsatu],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,158);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,161.5);
$pdf->Cell(10, 24,'42. Berhubungan seksual dengan orang yang tinggal di Afrika?...................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,161.5);
$pdf->Cell(10, 24,$notrans[empatdua],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,161.5);
$pdf->Cell(10, 24,'.................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(171,165);
$pdf->Cell(10, 24,'43. Pernah tinggal di Afrika ?........................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(282,165);
$pdf->Cell(10, 24,$notrans[empattiga],0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(300,165);
$pdf->Cell(10, 24,'.................',0, 10, 'L');


$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(213,175);
$pdf->Cell(10, 24,'Tanda Tangan Petugas',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(213,200);
$pdf->Cell(10, 0,'..........................................',0, 10, 'L');


$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(272,175);
$pdf->Cell(10, 24,'Tanda Tangan Pendonor',0, 10, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(272,200);
$pdf->Cell(10, 0,'..........................................',0, 10, 'L');














$udd=mysql_fetch_assoc(mysql_query("select nama,alamat,telp,daerah from utd where down='1' and aktif='1'"));
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetXY(10,195);
$pdf->Cell(10, 18,$udd[nama],0, 10, 'L');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetXY(10,2);
$pdf->Cell(10, 24,'Formulir Kuesioner dan Informed Consent Donor',0, 10, 'L');
$pdf->Rect(10, 17, 310, 0, 'D', array('all' => $style2)); // garis atas panjang

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(297,1);
$pdf->Cell(0, 27,'Halaman 2 dari 2',0, 1, 'L');


$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(10,1);
$pdf->Cell(0, 42,'MOHON DIISI LENGKAP MENGGUNAKAN HURUF KAPITAL',0, 1, 'C');

//LOGO PMI
$pdf->Image('tcpdf/images/Logo_PMI_Besar.png', 300, 18, 20, 9.5, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);


//$pdf->Rect(10, 28, 310, 0, 'D', array('all' => $style2)); // garis atas panjang
$pdf->Rect(10, 40, 310, 0, 'D', array('all' => $style2)); // garis bawah2 panjang
//$pdf->Rect(10, 73, 310, 0, 'D', array('all' => $style2)); // garis atas Kuesioner
$pdf->Rect(160, 40, 0, 157, 'D', array('all' => $style2)); // Garis Lurus Tengah
$pdf->Rect(10, 40, 0, 157, 'D', array('all' => $style2)); // Garis Lurus kiri
$pdf->Rect(320, 40, 0, 157, 'D', array('all' => $style2)); // Garis Lurus kanan
$pdf->Rect(10, 102, 150, 0, 'D', array('all' => $style2)); // garis bawah panjang
$pdf->Rect(10, 197, 310, 0, 'D', array('all' => $style2)); // garis bawah2 panjang

$pdf->Rect(10, 28, 40, 7, 'D', array('all' => $style2)); // Persegi Panjang tempat penyumbangan
$pdf->Rect(50, 28, 270, 7, 'D', array('all' => $style2)); // Persegi Panjang tengah setelah tempat penyumbangan
$pdf->Rect(200, 28, 40, 7, 'D', array('all' => $style2)); // Persegi Panjang tanggal

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(10,7);
$pdf->Cell(0, 49, 'Tempat Penyumbangan :');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(50,7);
$pdf->Cell(0, 49,' '.$udd[nama]);

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(200,7);
$pdf->Cell(0, 49, 'Tanggal Donor : ');

//Admin
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(13,33);
$pdf->Cell(10, 24,'DIISI OLEH ADMINISTRASI PENDAFTARAN',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,50);
$pdf->Cell(10, 5,'Validasi Data Pendonor ',0, 10, 'L');



$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(85,50);
$pdf->Cell(10, 5,'Kartu Pendonor ',0, 10, 'L');

$pdf->Rect(80, 50.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak KTP

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(85,50);
$pdf->Cell(10, 13,'KTP ',0, 10, 'L');

$pdf->Rect(80, 55, 3, 3, 'D', array('all' => $gariskecil)); // Kotak Kartudonor

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(85,50);
$pdf->Cell(10, 22,'SIM',0, 10, 'L');

$pdf->Rect(80, 59.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak SIM

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(85,50);
$pdf->Cell(10, 31,'Kartu Pelajar/ Mahasiswa',0, 10, 'L');

$pdf->Rect(80, 64, 3, 3, 'D', array('all' => $gariskecil)); // Kotak KTM

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(85,50);
$pdf->Cell(10, 40,'Paspor',0, 10, 'L');

$pdf->Rect(80, 68.5, 3, 3, 'D', array('all' => $gariskecil)); // Kotak PASPOR

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,50);
$pdf->Cell(10, 60,'Riwayat Donor Sebelumnya :',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(75,50);
$pdf->Cell(10, 0,':',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,60);
$pdf->Cell(10, 63,'......................................................................................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,60);
$pdf->Cell(10, 73,'......................................................................................',0, 10, 'L');

/*$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,60);
$pdf->Cell(10, 83,'......................................................................................',0, 10, 'L');*/

$pdf->SetFont('helvetica', 'b', 9);
$pdf->SetXY(115,50);
$pdf->Cell(10, 60,'Tanda Tangan Petugas',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(115,60);
$pdf->Cell(10, 73,'.........................................',0, 10, 'L');

//AFTAP
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(13,104);
$pdf->Cell(10, 0,'DIISI OLEH PETUGAS AFTAP',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,112);
$pdf->Cell(10, 0,'Nama Petugas Aftap',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(80,112);
$pdf->Cell(10, 0,': ................................................................ ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,112);
$pdf->Cell(10, 15,'Verifikasi Kantong Darah',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(80,112);
$pdf->Cell(10, 15,':',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(90,112);
$pdf->Cell(10, 15,'Nomor Selang & Kantong Sesuai',0, 10, 'L');

$pdf->Rect(85, 118, 3, 3, 'D', array('all' => $gariskecil)); // Valid nomor

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(90,112);
$pdf->Cell(10, 25,'Selang Kantong Tidak Terlipat',0, 10, 'L');

$pdf->Rect(85, 123, 3, 3, 'D', array('all' => $gariskecil)); // Valid nomor

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(90,112);
$pdf->Cell(10, 35,'Kantong Tidak/ Belum Kadaluwarsa',0, 10, 'L');

$pdf->Rect(85, 128, 3, 3, 'D', array('all' => $gariskecil)); // Valid nomor

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(90,112);
$pdf->Cell(10, 45,'Jarum Tajam & Tertutup',0, 10, 'L');

$pdf->Rect(85, 133, 3, 3, 'D', array('all' => $gariskecil)); // Valid nomor

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(90,112);
$pdf->Cell(10, 55,'Tidak Ada Tanda Kebocoran Kantong',0, 10, 'L');

$pdf->Rect(85, 138, 3, 3, 'D', array('all' => $gariskecil)); // Valid nomor

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,144);
$pdf->Cell(10, 0,'Pengambilan    :',0, 10, 'L');

$pdf->Rect(50, 144.5, 3, 3, 'D', array('all' => $gariskecil)); // Lancar

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(55,144);
$pdf->Cell(10, 0,'Lancar',0, 10, 'L');

$pdf->Rect(68, 144.5, 3, 3, 'D', array('all' => $gariskecil)); // Lancar

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(73,144);
$pdf->Cell(10, 0,'Tidak Lancar',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(94,144);
$pdf->Cell(10, 0,'Stop ........................... cc' ,0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,149);
$pdf->Cell(10, 0,'Reaksi Donor   :',0, 10, 'L');

$pdf->Rect(50, 149.5, 3, 3, 'D', array('all' => $gariskecil)); // Lancar

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(55,149);
$pdf->Cell(10, 0,'Pusing',0, 10, 'L');

$pdf->Rect(68, 149.5, 3, 3, 'D', array('all' => $gariskecil)); // Lancar

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(73,149);
$pdf->Cell(10, 0,'Muntah',0, 10, 'L');

$pdf->Rect(86, 149.5, 3, 3, 'D', array('all' => $gariskecil)); // Lancar

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(90,149);
$pdf->Cell(10, 0,'Hematom',0, 10, 'L');

$pdf->Rect(107, 149.5, 3, 3, 'D', array('all' => $gariskecil)); // Lancar

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(111,149);
$pdf->Cell(10, 0,'Lain-lain..............................',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,154);
$pdf->Cell(10, 0,'Jenis Kantong  :',0, 10, 'L');

$pdf->Rect(50, 154.5, 3, 3, 'D', array('all' => $gariskecil)); // Jenis Ktg

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(55,154);
$pdf->Cell(10, 0,'Single',0, 10, 'L');

$pdf->Rect(68, 154.5, 3, 3, 'D', array('all' => $gariskecil)); // Jenis Ktg

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(73,154);
$pdf->Cell(10, 0,'Double',0, 10, 'L');

$pdf->Rect(86, 154.5, 3, 3, 'D', array('all' => $gariskecil)); // Jenis Ktg

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(90,154);
$pdf->Cell(10, 0,'Triple',0, 10, 'L');

$pdf->Rect(101, 154.5, 3, 3, 'D', array('all' => $gariskecil)); // Jenis Ktg

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(105,154);
$pdf->Cell(10, 0,'Quadruple',0, 10, 'L');

$pdf->Rect(124, 154.5, 3, 3, 'D', array('all' => $gariskecil)); // Jenis Ktg

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(128,154);
$pdf->Cell(10, 0,'Kit Apheresis',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,159);
$pdf->Cell(10, 0,'Diambil             :',0, 10, 'L');

$pdf->Rect(50, 159.5, 3, 3, 'D', array('all' => $gariskecil)); // Vol

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(55,159);
$pdf->Cell(10, 0,'250 cc',0, 10, 'L');

$pdf->Rect(68, 159.5, 3, 3, 'D', array('all' => $gariskecil)); // Vol

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(73,159);
$pdf->Cell(10, 0,'350 cc',0, 10, 'L');

$pdf->Rect(86, 159.5, 3, 3, 'D', array('all' => $gariskecil)); // Vol

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(90,159);
$pdf->Cell(10, 0,'450 cc',0, 10, 'L');

$pdf->Rect(101, 159.5, 3, 3, 'D', array('all' => $gariskecil)); // Vol

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(105,159);
$pdf->Cell(10, 0,'.........',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,166);
$pdf->Cell(10, 0,'Jam Aftap         :            ........ : ........      sd      ........ : .........',0, 10, 'L');

$pdf->Rect(50, 174, 60, 20, 'D', array('all' => $style2)); // Persegi Panjang No Kantong

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(15,172);
$pdf->Cell(10, 0,'Nomor Kantong',0, 10, 'L');

$pdf->SetFont('helvetica', 'b', 9);
$pdf->SetXY(115,144);
$pdf->Cell(10, 60,'Tanda Tangan Petugas',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(115,170);
$pdf->Cell(10, 40,'.....................................',0, 10, 'L');

//Petugas MCU

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(165,33);
$pdf->Cell(10, 24,'DIISI OLEH PETUGAS MEDICAL CHECKUP',0, 10, 'L');



$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,58);
$pdf->Cell(10, 0,'Jenis Donor',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(220,58);
$pdf->Cell(10, 0,':         Sukarela          Pengganti',0, 10, 'L');

$pdf->Rect(225, 58.5, 3, 3, 'D', array('all' => $gariskecil)); // JenisDonor
$pdf->Rect(247, 58.5, 3, 3, 'D', array('all' => $gariskecil)); // JenisDOnor

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,66);
$pdf->Cell(10, 0,'Metode Pengambilan',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(220,66);
$pdf->Cell(10, 0,':         Biasa                Apheresis         Autologus',0, 10, 'L');

$pdf->Rect(225, 66.5, 3, 3, 'D', array('all' => $gariskecil)); // JenisDonor
$pdf->Rect(247, 66.5, 3, 3, 'D', array('all' => $gariskecil)); // JenisDOnor
$pdf->Rect(269, 66.5, 3, 3, 'D', array('all' => $gariskecil)); // JenisDOnor

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,74);
$pdf->Cell(10, 0,'Golongan Darah',0, 10, 'L');

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(220,74);
$pdf->Cell(10, 0,':     A        B       O       AB',0, 10, 'L');

$pdf->Rect(225, 73.5, 7, 6, 'D', array('all' => $gariskecil)); // Gol
$pdf->Rect(235, 73.5, 7, 6, 'D', array('all' => $gariskecil)); // Gol
$pdf->Rect(245, 73.5, 7, 6, 'D', array('all' => $gariskecil)); // Gol
$pdf->Rect(255, 73.5, 7.5, 6, 'D', array('all' => $gariskecil)); // Gol

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,85);
$pdf->Cell(10, 0,'Rhesus Darah',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,85);
$pdf->Cell(10, 0,':     Positif          Negatif',0, 10, 'L');

$pdf->Rect(225, 84.5, 14, 6, 'D', array('all' => $gariskecil)); // Gol
$pdf->Rect(245, 84.5, 14, 6, 'D', array('all' => $gariskecil)); // Gol

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,95);
$pdf->Cell(10, 0,'Berat Badan',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,95);
$pdf->Cell(10, 0,': .................................................................. Kg',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,103);
$pdf->Cell(10, 0,'Tinggi Badan',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,103);
$pdf->Cell(10, 0,': .................................................................. cm',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,111);
$pdf->Cell(10, 0,'Nilai Hemoglobin',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,111);
$pdf->Cell(10, 0,': .................................................................. g/dL',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,119);
$pdf->Cell(10, 0,'Tekanan Darah',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,119);
$pdf->Cell(10, 0,': .................................................................. mmHg',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,127);
$pdf->Cell(10, 0,'Denyut Nadi',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,127);
$pdf->Cell(10, 0,': .................................................................. x / menit',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,135);
$pdf->Cell(10, 0,'Suhu',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,135);
$pdf->Cell(10, 0,': .................................................................. c',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,143);
$pdf->Cell(10, 0,'Riwayat Medis',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,143);
$pdf->Cell(10, 0,': .................................................................. ',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,151);
$pdf->Cell(10, 0,'Keadaan Umum',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(220,151);
$pdf->Cell(10, 0,': .................................................................. ',0, 10, 'L');

$pdf->Rect(169, 162, 4, 4, 'D', array('all' => $style2)); // Persegi Panjang Diterima
$pdf->SetFont('helvetica', 'b', 10);
$pdf->SetXY(174,162);
$pdf->Cell(10, 0,'Diterima',0, 10, 'L');

$pdf->Rect(198, 162, 4, 4, 'D', array('all' => $style2)); // Persegi Panjang Ditolak
$pdf->SetFont('helvetica', 'b', 10);
$pdf->SetXY(203,162);
$pdf->Cell(10, 0,'Ditolak',0, 10, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(168,172);
$pdf->Cell(10, 0,'Keterangan (Jika Ada) :',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(168,185);
$pdf->Cell(10, 0,'.................................... ',0, 10, 'L');

$pdf->SetFont('helvetica', 'b', 9);
$pdf->SetXY(270,165);
$pdf->Cell(10, 0,'Tanda Tangan Petugas',0, 10, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(270,185);
$pdf->Cell(10, 0,'.................................... ',0, 10, 'L');
















$pdf->Output('Form_Donor.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>
