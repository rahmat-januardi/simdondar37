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

	<font size="4" color=00008B><b>Pembekuan Produk FFP & FP24</b></font><br><br>
<div>
<a href="pmi<?echo $_SESSION[leveluser] ?>.php?module=inputPembekuan" class="swn_button_blue">Input Pembekuan</a></div>
<br>



	<table border=1 cellpadding=4  style="border-collapse:collapse">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th>No</th>
            <th>No Transaksi</th>
            <th>Tgl<br>Proses</th>
	    <th>Petugas</th>
            <th>Status</th>
	    <th>Aksi</th>
	</tr>
        

	<?php
	$no=0;
	$sql=mysql_query("SELECT * FROM `dpembekuan` WHERE `status`='0' group by noTrans");
	while($dtBeku=mysql_fetch_assoc($sql)){
	$no++;
	if($dtBeku['status']=='0')$status='Belum Selesai';
	if($dtBeku['status']=='1')$status='SelesaiProses';
	?>
        <tr style="font-size:11px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
	   	<td align="right"><?=$no.'.'?></td>
		<td align="left"><?=$dtBeku['noTrans']?></td>
            	<td align="left" nowrap><?=$dtBeku['tgl']?></td>
            	<td align="center"><?=$dtBeku['userProses']?></td>
            	<td align="center" nowrap><?=$status?></td>
            	<td align="center"><a href="pmi<?echo $_SESSION[leveluser] ?>.php?module=outputPembekuan&notrx=<? echo $dtBeku['noTrans'] ?>" class="swn_button_blue">Proses</a></td>
		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=31 align="center">tidak ada transaksi yang di proses</td>
	<?}?>
	</table><br>

</body>
</html>

