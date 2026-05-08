<?
    /***********************************************
     * Author 	: suwena 
     * Date 	: 9 Juni 2019
     ***********************************************/
session_start();
require_once('config/db_connect.php');
$tahun      = date("Y");
$bulan      = date('m');
$v_tahun    = $_GET['t'];
$v_bulan    = $_GET['b'];
if (empty($v_tahun)){$v_tahun=$tahun;}
if (empty($v_bulan)){$v_bulan=$bulan;}
$utd            = mysql_fetch_assoc(mysql_query("select * from `utd` where `aktif`='1'"));
$dt_um          = "SELECT `u_id`, `u_tahun`, `u_id_udd`, `u_nama`, `u_alamat`, `u_kab`, `u_prov`, `u_kpos`, `u_telp`,
                   `u_email`, `u_ka_nama`, `u_ka_hp`, `u_ka_email`,
                   `u_komite_mdk`, `u_distr_terbuka`, `u_distr_cold_chain`,
                   `u_jml_dokter_kompeten`, `u_ptgs_komptn`, `u_inform_c`, `u_lbr_mon_td`, `u_jml_pasien_td`, `u_jml_pasien_rtd`,
                   `u_periksa_kgd`, `u_kgd_auto`, `u_kgd_semi`, `u_kgd_manual`, `u_periksa_abs`, `u_abs_auto`, `u_abs_semi`,
                   `u_abs_manual`, `u_periksa_iab`, `u_iab_auto`, `u_iab_semi`, `u_iab_manual`, `u_periksa_cross`, `u_cross_auto`,
                   `u_cross_semi`, `u_cross_manual`,day(`u_tgl_laporan`) as tgl_lap, month(`u_tgl_laporan`) as bln_lap, year(`u_tgl_laporan`) as thn_lap
                   FROM `rpt_data_umum` WHERE `u_id_udd`='$utd[id]'";
$dt_um          = mysql_fetch_assoc(mysql_query($dt_um));
switch($dt_um['bln_lap']){
    case '1'  : $bln_lap='Januari';break;
    case '2'  : $bln_lap='Februari';break;
    case '3'  : $bln_lap='Maret';break;
    case '4'  : $bln_lap='April';break;
    case '5'  : $bln_lap='Mei';break;
    case '6'  : $bln_lap='Juni';break;
    case '7'  : $bln_lap='Juli';break;
    case '8'  : $bln_lap='Agustus';break;
    case '9'  : $bln_lap='September';break;
    case '10' : $bln_lap='Oktober';break;
    case '11' : $bln_lap='Nopember';break;
    case '12' : $bln_lap='Desember';break;
}
switch ($v_bulan){
    case '1' : $namaperiode="JANUARI";break;
    case '2' : $namaperiode="FEBRUARI";break;
    case '3' : $namaperiode="MARET";break;
    case '4' : $namaperiode="APRIL";break;
    case '5' : $namaperiode="MEI";break;
    case '6' : $namaperiode="JUNI";break;
    case '7' : $namaperiode="JULI";break;
    case '8' : $namaperiode="AGUSTUS";break;
    case '9' : $namaperiode="SEPTEMBER";break;
    case '10' :$namaperiode="OKTOBER";break;
    case '11' :$namaperiode="NOVEMBER";break;
    case '12' :$namaperiode="DESEMBER";break;
}
//lisis masuk pada masalah pada penyimpanan
$rkp="SELECT
      COUNT( CASE WHEN `alasan_buang`='0' THEN 1 ELSE NULL END) AS  'Gagal_Pengambilan_Darah',
      COUNT( CASE WHEN `alasan_buang` in ('4','6','11','20','21') THEN 1 ELSE NULL END) AS  'IMLTD_Reaktif',
      COUNT( CASE WHEN `alasan_buang`='2' THEN 1 ELSE NULL END) AS  'Kedaluwarsa',
      COUNT( CASE WHEN `alasan_buang` in ('10','13','9','15') THEN 1 ELSE NULL END) AS  'Masalah_dalam_proses_produksi',
      COUNT( CASE WHEN `alasan_buang`='1' THEN 1 ELSE NULL END) AS  'Masalah_dalam_proses_penyimpanan',
      COUNT( CASE WHEN `alasan_buang` in ('3','5','7','8','12','14','16','17','18','19') THEN 1 ELSE NULL END) AS  'Penyebab_Lain'
      FROM `ar_stokkantong`
      WHERE
      month(`tgl_buang`)='$v_bulan' and year(`tgl_buang`)='$v_tahun'";

$q_sel=mysql_query($rkp);
$no=0;
$total=0;
$result=mysql_fetch_assoc($q_sel);
$total=$result['Gagal_Pengambilan_Darah']+$result['IMLTD_Reaktif']+$result['Kedaluwarsa']+$result['Masalah_dalam_proses_produksi']+$result['Penyebab_Lain'];

?>

<title>Laporan UDD</title>
<head>
<style type="text/css" media="print">
    @page
    {
        size: landscape;
        margin-bottom: 20mm;
        margin-left: 0mm;
        margin-right: 0mm;
        margin-top: 15mm;
        header : {display: none !important;}
    }
    html
    {
        background-color: #ffffff;
        margin: 3px;  /* this affects the margin on the html before sending to printer */
    }
    body
    {
        border: solid 0px #ffffff ;
        margin: 0mm 15mm 10mm 10mm; /* margin you want for the content */
    }
    table th td {text-align: left;}
</style>
</head>
<body onload="window.print()">
<table class="list" border="0" cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr>
        <td style="height: 40px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            LAPORAN DARAH YANG DIMUSNAHKAN<br>
            <?php echo $dt_um['u_nama'];?><br>
            <?php echo 'BULAN '.$namaperiode.' '.$v_tahun;?>
        </td>
    </tr>
</table>
<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br><br>C.JUMLAH KANTONG DARAH YANG DIMUSNAHKAN BERDASARKAN PENYEBAB
</div>


<table class="list" border="1" cellpadding="3" cellspacing="2" width="100%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center">No</th>
        <th align="center">Penyebab Darah Dimusnahkan</th>
        <th align="center">Jumlah Kantong Darah <br>yang Dimusnahkan</th>
    </tr>
    </thead>
    <tbody>
    <tr><td>1.</td><td>Gagal Pengambilan Darah</td><td align="center"><?php echo number_format($result['Gagal_Pengambilan_Darah'],0,",","."); ?></td></tr>
    <tr><td>2.</td><td>IMLTD Reaktif</td><td align="center"><?php echo number_format($result['IMLTD_Reaktif'],0,",","."); ?></td></tr>
    <tr><td>3.</td><td>Kedaluwarsa</td><td align="center"><?php echo number_format($result['Kedaluwarsa'],0,",","."); ?></td></tr>
    <tr><td>4.</td><td>Masalah dalam proses produksi</td><td align="center"><?php echo number_format($result['Masalah_dalam_proses_produksi'],0,",","."); ?></td></tr>
    <tr><td>5.</td><td>Masalah dalam proses penyimpanan</td><td align="center"><?php echo number_format($result['Masalah_dalam_proses_penyimpanan'],0,",","."); ?></td></tr>
    <tr><td>6.</td><td>Penyebab Lain</td><td align="center"><?php echo number_format($result['Penyebab_Lain'],0,",","."); ?></td></tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2" align="center">Jumlah</th>
        <td align="center"><?php echo number_format($total,0,",",".");?></td>
    </tr>
    </tfoot>
</table>

<br><br>

<table border="0" width="100%">
    <tr style="background-color: #ffffff;font-wight:none; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td width="25%"></td>
        <td width="25%"></td>
        <td width="25%" nowrap>
            <?php echo $dt_um['u_kab'].', '.$dt_um['tgl_lap'].' '.$bln_lap.' '.$dt_um['thn_lap'];?>
            <br>KEPALA <?php echo $dt_um['u_nama'].',';?><br><br><br><br>
        </td>
    </tr>
    <tr style="background-color: #ffffff;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td width="25%"></td>
        <td width="25%"></td>
        <td width="25%" nowrap>
            <?php echo $dt_um['u_ka_nama'];?>
        </td>
    </tr>
</table>
<? echo "<meta http-equiv='refresh' content='2;url=pmitatausaha.php?module=musnah&t=$v_tahun'";?>
</body>
