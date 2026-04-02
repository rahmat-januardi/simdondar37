<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$now = date("Y-m-d");
$noTrx= $_GET[notrx];



?>
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script language=javascript src="js/pembekuanBms.js" type="text/javascript"> </script>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>



<script language=javascript src="util.js" type="text/javascript"> </script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<style>
    tr { background-color: #ffffff;}
    .initial { background-color: #ffffff; color:#000000 }
    .normal { background-color: #ffffff; }
    .highlight { background-color: #78acff }
</style>
<style type="text/css">.styled-select select {background-color: #FCF9F9; border: none;width: auto;padding: 3px;font-size: 20px;cursor: pointer; }</style>
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

	<font size="5" color=00008B><b>List Data Pembekuan Produk</b></font><br><br>
<form name="pembekuan" id="pembekuan" onsubmit="return ok()" method="POST" action="<?=$PHPSELF?>">
	<table border=0 cellpadding=4  style="border-collapse:collapse">
        <tr>
            <th>Tgl Pembekuan</th>
            <th>:</th>
            <th align="left"><th align="left"><INPUT type="date" name="tglAwal" id="tglAwal"></th>
            <th><input type='submit' value='Submit' name='submit'></th>
	</tr>
	</table>
<br>

	<table border=1 cellpadding=4  style="border-collapse:collapse" id="box-table-b0">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;" id="box-table-b0">
            <th>No.</th>
	    <th>No Transaksi</th>
            <th>No Unik</th>
            <th>Gol Darah(rh)</th>
            <th>Produk</th>
            <th>Volume</th>
            <th>Tgl Pengambilan</th>
            <th>Tgl Pembekuan</th>
            <th>Alat</th>
            <th>Jam<br>Mulai</th>
            <th>Jam<br>Selesai</th>
            <th>Durasi</th>
            <th>Petugas</th>
	</tr>
</form>

<?php
if (isset($_POST[submit])) {
$no=0;
$tglCari=$_post[tglAwal];
$sql=mysql_query("SELECT `dpembekuan`.`noTrans`, `dpembekuan`.`tgl`, `dpembekuan`.`alat`, `dpembekuan`.`noKantong`, `dpembekuan`.`jamMulai`, `dpembekuan`.`jamSelesai`, `dpembekuan`.`suhuInti`, `dpembekuan`.`status`, `dpembekuan`.`userProses`, 
`dpengolahan`.`Produk`, `petugas`, `dpengolahan`.`tgl`,`dpengolahan`.`goldarah`, `dpengolahan`.`rhesus`,
`stokkantong`.`volume`, `stokkantong`.`tgl_Aftap`
FROM `dpembekuan` 
inner join `dpengolahan` on `dpengolahan`.`noKantong` = `dpembekuan`.`noKantong`
inner join `stokkantong` on `stokkantong`.`noKantong` = `dpembekuan`.`noKantong`

WHERE `dpembekuan`.`tgl` >= '$tglCari'");
	while($dtBeku=mysql_fetch_assoc($sql)){
	$no++;
	$tglaftap=strtotime($dtBeku['tgl_Aftap']);
	$tglolah=strtotime($dtBeku['tgl']);
	$durasi= floor(($tglolah-$tglaftap)/(60*60));
	?>
        <tr style="font-size:11px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">

	   	<td align="right"><?=$no.'.'?></td>
		<td align="left"><?=$dtBeku['noTrans']?></td>
            	<td align="center" nowrap><?=$dtBeku['noKantong']?></td>
            	<td align="center" nowrap><?=$dtBeku['goldarah']."(".$dtBeku['rhesus'].")"?></td>
            	<td align="center" nowrap><?=$dtBeku['Produk']?></td>
            	<td align="center" nowrap><?=$dtBeku['volume']?> ml</td>
            	<td align="center" nowrap><?=$dtBeku['tgl_Aftap']?></td>            	
		<td align="center" nowrap><?=$dtBeku['tgl']?></td>
            	<td align="center" nowrap><?=$dtBeku['alat']?></td>
            	<td align="center" nowrap><?=$dtBeku['jamMulai']?></td>
            	<td align="center" nowrap><?=$dtBeku['jamSelesai']?></td>
            	<td align="center" nowrap><?=$durasi?> jam</td>
            	<td align="left" nowrap><?=$dtBeku['userProses']?></td>
		</tr>
	<?}
}


?>


        </table>

</body>
</html>

