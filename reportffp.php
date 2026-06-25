<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$tglsebelum = mktime(0,0,0,date("m"),1,date("Y"));
$tglawal=date("Y-m-d");
$hariini = date("Y-m-d");
?>
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<script language=javascript src="util.js" type="text/javascript"> </script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<style>
    tr { background-color: #ffffff;}
    .initial { background-color: #ffffff; color:#000000 }
    .normal { background-color: #ffffff; }
    .highlight { background-color: #7CFC00 }
</style>
<style type="text/css">.styled-select select {background-color: #FCF9F9; border: none;width: auto;padding: 3px;font-size: 15px;cursor: pointer; }</style>
<style>
    table {
        border-collapse: collapse;
    }
    table, th, td {
        border: 1px solid brown;
    }
</style>
<html xmlns="http://www.w3.org/1999/xhtml">
<style>body {font-family: "Lato", sans-serif;}</style>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>SIMDONDAR</title>
</head>

<body>
	<?
		if (isset($_POST[waktu])) {$tglawal=$_POST[waktu];$hariini=$hariini;}
		if ($_POST[waktu1]!='') $hariini=$_POST[waktu1];
		if ($_POST[hasilqc]!='') $src_hasilqc=$_POST[hasilqc];
        $status=$_POST['status'];
        $petugas=$_POST['petugas'];       
	?>
    <a name="atas" id="atas"></a>
	<font size="4" color=00008B>RINCIAN <b>QC PRODUK FFP</b></font><br><br>
	<form name="cari" method="POST" action="<?echo $PHPSELF?>">
		<table cellpadding=1 cellspacing="0" border="0">
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
				<td align="left" nowrap>Tanggal <input name="waktu" id="datepicker"  value="<?=$tglawal?>" type=text size="10" style="font-family:monospace"></td>
				<td align="right" nowrap>s/d <input name="waktu1" id="datepicker1" value="<?=$hariini?>" type=text size="10" style="font-family:monospace"></td>

    <td align="right" nowrap>&nbsp;Hasil QC
    <select name="hasilqc">
            <option value="" selected>- SEMUA -</option>
            <option value="Lulus">Lulus</option>
            <option value="Tidak Lulus">Tidak Lulus</option>
            
    </select>
				
                
				<td><input type=submit name=submit class="swn_button_blue" value="Tampilkan data">
                	
                	<a href="pmiqc.php?module=qc_laporan"class="swn_button_blue">Kembali</a></td>
			</tr>
		</table>	
	</form>
	<table border=1 cellpadding=4  style="border-collapse:collapse">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
	    <th rowspan="2">No.</th>
            <th rowspan="2">No. Kantong</th>
            <th rowspan="2">Merk</th>
	    <th rowspan="2">Gol Darah</th>
            <th rowspan="2">Rhesus</th>
	    <th rowspan="2">Produk</th>
            <th rowspan="2">Tgl Aftap</th>
	    <th rowspan="2">Tgl Kadaluwarsa Produk</th>
	    <th rowspan="2">Berat</th>
	    <th rowspan="2">Volume <br>&plusmn 10%</th>
	    <th rowspan="2">Kebocoran</th>
	    <th rowspan="2">Perubahan Visual</th>
	    <th rowspan="2">Leukosit <br><0,1 x 10<sup>9</sup>/L</th>
	    <th rowspan="2">Haematokrit <br>%</th>
	    <th rowspan="2">Haemoglobin</th>
	    <th rowspan="2">Trombosit <br><50 x 10<sup>9</sup>/L</th>
	    <th rowspan="2">Faktor VIII <br><0,70 iu/mL</th>
	    <th rowspan="2">Aerob</th>
	    <th rowspan="2">Anaerob</th>
	    <th rowspan="2">Hasil</th>
	    <!--<th colspan="4">Hasil Screening IMLTD</th>-->
	   	 <th rowspan="2">Petugas</th>
		 <th rowspan="2">Checker</th>
		 <th rowspan="2">Yg Mengesahkan</th>
	</tr>

        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <!--<th>HBsAg</th>
	    <th>HCV</th>
	    <th>HIV</th>
	    <th>Syphilis</th>-->
	 </tr>

	<?php
	$no=0;
	$sql="SELECT * FROM `qc`
		  WHERE DATE(qctgl)>='$tglawal' AND date(qctgl)<='$hariini' and produk like '%FFP%' and qc_status like '$src_hasilqc%' order by notrans asc";
	//echo "$sql";
	$qraw=mysql_query($sql);
    $statusrelease='';
	while($tmp=mysql_fetch_assoc($qraw)){$no++;
        switch ($tmp['status']){
            case '0' : $statusrelease='Lulus';break;
            case '1' : $statusrelease='Tidak Lulus';break;
            case '2' : $statusrelease='Lulus dengan Catatan';break;
            default  : $statusrelease='-';
        }
		?>
        <tr style="font-size:11px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
	    <td align="center"><?=$no.'.'?></td>
	    <td align="center"><?=$tmp['nokantong']?></td>
	    <td align="center"><?=$tmp['merk']?></td>
	    <td align="center"><?=$tmp['gol_darah']?></td>
            <td align="center"><?=$tmp['RhesusDrh']?></td>
	    <td align="center"><?=$tmp['produk']?></td>
	    <td align="center"><?=$tmp['tglaftap']?></td>	
	    <td align="center"><?=$tmp['kadaluwarsa']?></td>
	    <td align="center"><?=$tmp['berat']?> gr</td>
	    <td align="center"><?=$tmp['volume']?> ml</td>
	    <td align="center"><?=$tmp['kebocoran']?></td>
	    <td align="center"><?=$tmp['visual']?></td>
	    <td align="center"><?=$tmp['lekosit']?></td>
	    <td align="center"><?=$tmp['hematokrit']?></td>
	    <td align="center"><?=$tmp['hemoglobin']?> g/unit</td>
	    <td align="center"><?=$tmp['trombosit']?>per unit</td>
	    <td align="center"><?=$tmp['vaktorviii']?></td>
	    <td align="center"><?=$tmp['aerob']?></td>
	    <td align="center"><?=$tmp['anaerob']?></td>
	    <td align="center"><?=$tmp['qc_status']?></td>
	    <td align="center"><?=$tmp['qcuser']?></td>
	    <td align="center"><?=$tmp['qcchecker']?></td>
	    <td align="center"><?=$tmp['qcpengesah']?></td>

		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=31 align="center">Tidak ada data pemeriksaan QC</td>
	<?}?>
	</table><br>
	<a href="pmiqc.php?module=qc_laporan"class="swn_button_blue">Kembali</a>
	<?
		if ($no!==0){
		?><a href="pmiqc.php?module=cetak_rekap-ffp&tgl1=<?=$tglawal?>&tgl2=<?=$hariini?>&stts=<?=$status?>&ptgs=<?=$petugas?>&hasil=<?=$src_hasilqc?>" class="swn_button_blue">Export ke Excel</a><?
		}
	?>
    
    <a name="bawah" id="bawah"></a>
	<?
?>
</body>
</html>

