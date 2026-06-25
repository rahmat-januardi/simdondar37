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
session_start();
$namauser = $_SESSION[namauser];
$nama_lengkap = $_SESSION[nama_lengkap];
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');
function rp($a)
{
    $by1 = (string)((int)$a);
    $nby1 = strlen($by1);
    $nspasi = 11 - $nby1;
    $nsp = '';
    for ($i = 0; $i < $nspasi; $i++) {
        $nsp .= " ";
    }
    if ($nby1 > 3) $byr1 = "Rp. " . $nsp . substr($by1, 0, $nby1 - 3) . "." . substr($by1, $nby1 - 3, 3) . ",00";
    if ($nby1 > 6) $byr1 = "Rp. " . $nsp . substr($by1, 0, $nby1 - 6) . "." . substr($by1, $nby1 - 6, 3) . "." . substr($by1, $nby1 - 3, 3) . ",00";
    return $byr1;
}

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
$pdf->SetMargins(2, 2, 2);
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
$pdf->SetFont('courier', '', 12);
// add a page
//$pdf->AddPage('L','IDH');
$pdf->AddPage('P', 'A4');
//$pdf->AddPage();

// define barcode style
$style = array(
    'position' => 'R',
    'border' => false,
    'padding' => 'auto',
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'courier', //'courier',
    'fontsize' => 10,
    'stretchtext' => 4
);

// PRINT VARIOUS 1D BARCODES

// CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
//$pdf->Cell(0, 0, 'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9', 0, 1);
require_once('config/koneksi.php');
/*
	 $idp	= mysql_query("select nomor from kwitansi order by nomer desc limit 1");
	 $idp1	= mysql_fetch_assoc($idp);
	 $idp2	= (int)(substr($idp1[Kode],2,3));
	 $idp3=(int)$idp2+1;
	 $j_nol1= 3-(strlen(strval($idp3)));
	 for ($i=0; $i<$j_nol1; $i++){
		  $idp4 .="0";
	 }
	 $kode=$kd.$idp4.$idp3;

*/
//------------------------ set id transaksi ------------------------->
$idp    = mysql_query("select * from tempat_donor where active='1'");
$idp1    = mysql_fetch_assoc($idp);
$kd = 'DG';
if ($td1 == 'B') $kd = $td0;
$th        = substr(date("Y"), 2, 2);
$bl        = date("m");
$tgl    = date("d");
$kdtp    = $tgl . $bl . $th . "-";
$idp    = mysql_query("select nomer from kwitansi where nomer like '%$kdtp%' order by nomer DESC");
$idp1    = mysql_fetch_assoc($idp);
//$idp2	= (int)(substr($idp1[nomer],9,3));
$idp2    = substr($idp1[nomer], 9, 3);
if ($idp2 < 1) {
    $idp2 = "000";
}
$idp3    = (int)$idp2 + 1;
$id31    = 3 - (strlen(strval($idp3)));
//$id31	= strlen($idp2)-strlen($idp3);
$idp4    = "";
for ($i = 0; $i < $id31; $i++) {
    $idp4 .= "0";
}
$nomerkw = $kd . $kdtp . $idp4 . $idp3;
$noform1 = $_GET[noform];
$jumlah1 = mysql_fetch_assoc(mysql_query("select Jumlah from pembayaran where Tgl=(select max(Tgl) from pembayaran where NoTrans='$_GET[noform]')"));
$jumlah2 = mysql_fetch_assoc(mysql_query("select dpem.shift,dpem.kodeBrg,
dper.NoKantong,dper.tempat,dper.no_rm,dper.rs,dper.layanan 
from dpembayaranpermintaan as dpem,
dtransaksipermintaan as dper where dpem.notrans='$_GET[noform]' and dper.NoForm='$_GET[noform]'  order by dper.NoKantong "));
$today = date("Y-m-d");
//------------------------ END set id transaksi ------------------------->
/*
$Y=3;
$X=3;
$bd1=php_uname('n');
$td0=php_uname('n');
$td0=strtoupper($td0);
$td0=substr($td0,0,2);
$td1=substr($td0,0,1); 
$kd='dg';
if ($td1=='B') $kd=$td0; 
//if ($td1!='B') $kd='b1';
$idp	= mysql_query("select * from kwitansi where nomer like '$kd%' order by nomer DESC");
$idp1	= mysql_fetch_assoc($idp);
$idp2	= substr($idp1[nomer],1);
$idp3	= (int)$idp2+1;
$id31	= strlen($idp2)-strlen($idp3);
$idp4	= "";
for ($i=0; $i<$id31; $i++){
	$idp4 .="0";
}
$nomerkw=$kd.'-'.$idp4.$idp3;
*/

//mysql_query("insert into kwitansi (nomer,NoForm,jumlah,Tgl,petugas,shift,tempat,kodebiaya,no_rm,rs,layanan) values ('$nomerkw','$noform1','$jumlah1[Jumlah]','$today','$namauser','$jumlah2[shift]','$jumlah2[tempat]','$jumlah2[kodeBrg]','$jumlah2[no_rm]','$jumlah2[rs]','$jumlah2[layanan]')");

mysql_query("update dpembayaranpermintaan set rs='$jumlah2[rs]',layanan='$jumlah2[layanan]',kwitansi='$nomerkw',stat='1' where notrans='$noform1' and stat='0'");
//mysql_query("update pembayaran set nokwitansi='$nomerkw' where noform='$GET_[noform]'");
for ($ii = 0; $ii < 1; $ii++) {
    $bayar1 = mysql_fetch_assoc(mysql_query("select noform,no_rm,rs,bagian,jenis,no_rm,ruangan from htranspermintaan 
                                      where noform='$_GET[noform]'"));
    $bayar7 = mysql_fetch_assoc(mysql_query("select JenisDarah,GolDarah,Rhesus,tempat from dtranspermintaan 
                                      where NoForm='$_GET[noform]'"));
    $bayar8 = mysql_fetch_assoc(mysql_query("select nama,alamat from pasien 
                                      where no_rm='$bayar1[no_rm]'"));
    $bayar9 = mysql_fetch_assoc(mysql_query("select notrans,no_kantong from pasien 
                                      where notrans='$bayar1[noform]'"));
    /*
$bayar1=mysql_fetch_assoc(mysql_query("select 
ht.noform, ht.no_rm, ht.rs, ht.diagnosa, ht.jenis,
dp.BiayaLD,dp.TotPotongan,dp.TotDibayar,dp.tgl,
ds.JenisDarah,ds.GolDarah,ds.Rhesus,ds.tempat,
pp.namabrg,pp.subTotal,pp.notrans,pp.no_kantong,
pa.nama,pa.alamat,pa.gol_darah,pa.rhesus,pa.kelamin
                                      from 
htranspermintaan as ht,
dpembayaran as dp,
dtranspermintaan as ds,
dpembayaranpermintaan as pp, 
pasien as pa
                                      where 

dp.noForm='$_GET[noform]' and 
ds.NoForm=dp.noForm and 
ht.NoForm=ds.NoForm and 
pp.notrans=dp.noForm and 
pa.no_rm=ht.no_rm and
pp.namabrg not like '%crossmatch%'"));
*/
    $rs = mysql_fetch_assoc(mysql_query("select NamaRs from rmhsakit where Kode='$bayar1[rs]'"));
    $layanan = mysql_fetch_assoc(mysql_query("select nama from jenis_layanan where kode='$bayar1[jenis]'"));
    $bayar2 = mysql_fetch_assoc(mysql_query("select Jumlah from pembayaran where Tgl=(select max(Tgl) from pembayaran where NoTrans='$_GET[noform]')"));
    $bayar_sbl = mysql_fetch_assoc(mysql_query("select sum(subTotal) from dpembayaranpermintaan
					where notrans='$_GET[noform]'"));
    $bayar3 = mysql_fetch_assoc(mysql_query("select sum(subTotal),kodeBrg from dpembayaranpermintaan
                                      where notrans='$_GET[noform]' and namabrg not like '%CROSSMATCH%'"));
    $jbiaya = mysql_fetch_assoc(mysql_query("select NamaBiaya, Harga from biaya where Kode='$bayar3[kodeBrg]'"));
    $bayar4 = mysql_fetch_assoc(mysql_query("select TotDibayar,TotPotongan from dpembayaran where noForm='$_GET[noform]'"));
    $bayar5 = mysql_fetch_assoc(mysql_query("select sum(subTotal) from dpembayaranpermintaan
                                      where notrans='$_GET[noform]' and namabrg like '%CROSSMATCH%'"));

    $pdf->Image("logo_pmi.png", 4, 1.5, 15, 15);


    $udd = mysql_fetch_assoc(mysql_query("select nama,alamat from utd where down='1' and aktif='1'"));
    $bdd = mysql_fetch_assoc(mysql_query("select * from bdrs where kode='$bd1'"));
    $pdf->SetFont('courier', '', 12);
    $pdf->SetXY(20 + $X, $Y);
    $pdf->Cell(0, 12, 'PALANG MERAH INDONESIA', 0, 1, 'L');
    $pdf->SetFont('courier', '', 12);
    $pdf->SetXY(20 + $X, $Y + 4);
    $pdf->Cell(0, 12, $udd[nama], 0, 1, 'L');
    $pdf->SetXY(20 + $X, $Y + 9);
    $pdf->Cell(0, 10, $udd[alamat], 0, 1, 'L');
    $pdf->SetXY(4 + $X, $Y + 11.2);
    $pdf->Cell(0, 12, '==========================================================================================================', 0, 1, 'L');
    $pdf->SetFont('courier', '', 12);
    $pdf->SetXY(4 + $X, $Y + 15);
    $pdf->Cell(0, 12, 'No Formulir      : ' . $bayar1[noform] . '                     Cetak Ulang ', 0);

    $kolf = mysql_num_rows(mysql_query("select NoKantong from dtransaksipermintaan where NoForm='$_GET[noform]' and Status='0'"));
    $kolf1 = mysql_num_rows(mysql_query("select NoKantong from dtransaksipermintaan where NoForm='$_GET[noform]' and Status='1'"));
    $kolf2 = mysql_num_rows(mysql_query("select NoKantong from dtransaksipermintaan where NoForm='$_GET[noform]' and Status='B'"));
    $pdf->SetXY(4 + $X, $Y + 19);
    $pdf->SetFont('courier', '', 12);
    if ($_GET[yby] == '') $_GET[yby] = $bayar8[nama];
    $pdf->Cell(0, 13, 'Telah Terima dari   : ' . $_GET[yby], 0);

    $pdf->SetXY(4 + $X, $Y + 23);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Nama Pasien         : ' . $bayar8[nama], 0);

    $pdf->SetXY(4 + $X, $Y + 28);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Alamat Pasien       : ' . $bayar8[alamat], 0);

    $pdf->SetXY(4 + $X, $Y + 32);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Darah Diminta       : ' . $bayar7[GolDarah] . '(' . $bayar7[Rhesus] . ')' . $bayar7[JenisDarah], 0);

    $pdf->SetXY(4 + $X, $Y + 37);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Jumlah Dibawa       : ' . $kolf . '  Kolf', 0);

    $pdf->SetXY(4 + $X, $Y + 42);
    $pdf->SetFont('courier', '', 12);
    //$pdf->Cell(0, 13, 'Jumlah DiTitip      : '.$kolf1.' kolf',0);
    $pdf->Cell(0, 13, 'Jumlah Dititip      : ' . $kolf1 . ' Kolf, Jumlah Dibatalkan : ' . $kolf2 . ' Kolf', 0);

    $pdf->SetXY(4 + $X, $Y + 47);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Nama RS             : ' . $rs[NamaRs], 0);

    $pdf->SetXY(4 + $X, $Y + 52);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Bagian              : ' . $bayar1[bagian] . '    Ruangan  : ' . $bayar1[ruangan], 0);

    $pdf->SetXY(4 + $X, $Y + 57);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Jenis Layanan       : ' . $layanan[nama] . '    bppd :' . rp($jbiaya[Harga]) . '/kolf', 0);

    $tglbayar = date("Y-m-d");
    $tgl1 = date("d", strtotime($tglbayar));
    $bln1 = date("n", strtotime($tglbayar));
    $thn1 = date("Y", strtotime($tglbayar));
    $bulan = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
    $bln11 = $bulan[$bln1];

    $pdf->SetXY(4 + $X, $Y + 62);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(25, 13, 'Rincian:            ', 0, 0, 'L');
    //$pdf->Cell(74, 13,'- LD                     '.rp($bayar1[BiayaLD]),0,0,'R');
    $pdf->Cell(30, 13, ' - ', 0, 0, 'R');
    //$pdf->Cell(70,13,.$jbiaya[NamaBiaya].,0,0,'R');
    //$pdf->Cell(70, 13,      $jbiaya[NamaBiaya].'  '.                 rp($bayar3['sum(subTotal)']),0,0,'R');


    $pdf->SetXY(4 + $X, $Y + 67);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(25, 13, '', 0, 0, 'L');
    $pdf->Cell(20, 13, 'Biaya Crossmatch:', 0, 0, 'R');
    $pdf->Cell(70, 13,                        rp($bayar5['sum(subTotal)']), 0, 0, 'R');

    //$pdf->SetXY(4+$X,$Y+72);
    //$pdf->SetFont('courier', '', 12);
    //$pdf->Cell(12, 13, 'Sudah dibayar       :', 0,0,'L');
    //$pdf->Cell(100, 13,                           rp($bayar_sbl['sum(subTotal)']-$bayar2[Jumlah]),0,0,'R');


    $pdf->SetXY(4 + $X, $Y + 77);
    $pdf->Cell(126, 13,                              '----------------------------', 0, 1, 'R');
    $pdf->SetXY(7 + $X, $Y + 81);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(12, 13,     'Jumlah Dibayar      :', 0, 0, 'L');
    $pdf->Cell(100, 13,                           rp($bayar2[Jumlah] - $biaya1[Totpotongan]), 0, 0, 'R');
    //$pdf->SetXY(4+$X,$Y+80);
    //$pdf->Cell(110, 13,                              '___________________________',0, 1, 'R');

    //$pdf->SetXY(4+$X,$Y+51);
    //$pdf->SetFont('courier', '', 12);
    //$pdf->Cell(12, 13, 'Kekurangan :', 0,0,'L');
    //$pdf->Cell(45, 13,rp($bayar1[BiayaLD]-$bayar4[TotDibayar]-$bayar4[TotPotongan]),0,0,'R');

    $no = 0;
    //$kan=mysql_query("select no_kantong from dpembayaranpermintaan where notrans='$bayar1[noform]' ");
    $kan = mysql_query("select NoKantong from dtransaksipermintaan where NoForm='$bayar1[noform]' and (Status='0' or Status='B') ");
    $nkk = 0;
    while ($kan1 = mysql_fetch_assoc($kan)) {
        $bayar6 = mysql_fetch_assoc(mysql_query("select gol_darah,produk,RhesusDrh from stokkantong where nokantong='$kan1[NoKantong]'"));
        $pdf->SetFont('courier', '', 12);
        $nk1 = $nkk + 1;
        if ($nkk == '0') {

            $pdf->SetXY(105 + $X, $Y + 19 + $no);
            $pdf->Cell(0, 13, 'No.Kantong : ' . $nk1 . '. ' . $kan1[NoKantong] . '   ' . $bayar6[gol_darah] . '(' . $bayar6[RhesusDrh] . ')' . $bayar6[produk] . ' **', 0);
        }
        if ($nkk > '0') {
            $pdf->SetXY(138 + $X, $Y + 20 + $no);
            $pdf->Cell(0, 13, $nk1 . '. ' . $kan1[NoKantong] . '   ' . $bayar6[gol_darah] . '(' . $bayar6[RhesusDrh] . ')' . $bayar6[produk] . ' **', 0);
        }
        $nkk++;
        $no = $no + 3;
    }
    /*
$pdf->SetXY(50,14);
$pdf->SetFont('courier', '', 12);
$pdf->Cell(0, 13, 'No Kantong : '.$kan1[NoKantong], 0);
*/
    $pdf->SetXY(125 + $X, $Y + 86);
    $pdf->SetFont('courier', '', 12);
    $kt = explode(' ', $udd[nama]);
    $kota = $kt[3] . ' ' . $kt[4];
    //if ($kt[4]!='') $kota=$kota.' '.$kt[4];
    $pdf->Cell(0, 13, $kota . ', ' . $tgl1 . ' ' . $bln11 . ' ' . $thn1, 0, 0, 'C');

    $pdf->SetXY(4 + $X, $Y + 90);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Keluarga Pasien', 0, 0, 'L');
    //$pdf->SetXY(4+$X,$Y+97);
    //$pdf->SetFont('courier', '', 12);
    //$pdf->Cell(0, 13,'___________________', 0,0,'L');


    $pdf->SetXY(125 + $X, $Y + 90);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, 'Petugas Piket', 0, 0, 'C');
    $pdf->SetXY(125 + $X, $Y + 99);
    $pdf->SetFont('courier', '', 12);
    $pdf->Cell(0, 13, $nama_lengkap, 0, 0, 'C');



    //$pdf->SetXY(4+$X,$Y+77);
    //$pdf->SetFont('courier', 'b', 9);
    //$pdf->Cell(0, 13, 'Darah yang sudah di proses atau diambil, tidak dapat dikembalikan', 0,0,'L');
    $pdf->SetXY(4 + $X, $Y + 101);
    if ($ii < 2) $pdf->Cell(0, 13, '==========================================================================================================', 0, 1, 'L');
    $pdf->SetXY(4 + $X, $Y + 104);
    $pdf->SetFont('courier', 'bu', 10);
    $pdf->Cell(0, 13, 'PERHATIAN :', 0, 0, 'L');

    $pdf->SetXY(4 + $X, $Y + 107);
    $pdf->SetFont('courier', '', 10);
    $pdf->Cell(0, 13, '- Biaya Yang tertera dikwitansi ini BUKAN HARGA DARAH, karena darah tidak untuk diperjual belikan', 0, 0, 'L');

    $pdf->SetXY(4 + $X, $Y + 111);
    $pdf->SetFont('courier', '', 10);
    $pdf->Cell(0, 13, '- Biaya ini merupakan BIAYA PENGGANTI PENGOLAHAN DARAH (BPPD) yang meliputi Biaya :  ', 0, 0, 'L');

    $pdf->SetXY(8 + $X, $Y + 115);
    $pdf->SetFont('courier', '', 10);
    $pdf->Cell(0, 13, '1. Kantong Darah  2. Proses Pengambilan darah donor  3. Pemeriksaan Anti HBsAg,HCV,HIV,Syphilis  ', 0, 0, 'L');

    $pdf->SetXY(8 + $X, $Y + 119);
    $pdf->SetFont('courier', '', 10);
    $pdf->Cell(0, 13, '4. Pengolahan Komponen darah  5. Penyimpanan dan perawatan komponen darah  6. CROSSMATCH  ', 0, 0, 'L');

    $pdf->SetXY(4 + $X, $Y + 123);
    $pdf->SetFont('courier', '', 10);
    $pdf->Cell(0, 13, '- Darah yang sudah di proses atau diambil, tidak dapat dikembalikan ', 0, 0, 'L');

    $pdf->SetXY(4 + $X, $Y + 128);
    $pdf->SetFont('courier', '', 11);
    $pdf->Cell(0, 13, '--== TERIMA KASIH dan SEMOGA LEKAS SEMBUH ==-- ', 0, 0, 'C');
    $Y = $Y + 127;
}
$pdf->Output('nota.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
