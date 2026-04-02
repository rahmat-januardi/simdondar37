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
<?php
if (isset($_POST[submit])) {
$jamSelesai=$_POST[jamSelesai];

$sql=mysql_query("SELECT * FROM `dpembekuan` WHERE `noTrans`='$noTrx'");
	while($dtBekuUp=mysql_fetch_assoc($sql)){

	$noKantong=$dtBekuUp['noKantong'];		
	$jamMulai=$dtBekuUp['jamMulai'];

		$Update=mysql_query("UPDATE `dpembekuan` SET `jamSelesai`='$jamSelesai',`status`='1' WHERE 		`noTrans`='$noTrx' and `noKantong`='$noKantong'");

		//=======Audit Trial==================================================
		$log_mdl ='PENGOLAHAN';
		$log_aksi='Pengolahan : Pembekuan No Kantong: '.$noKantong.' Jam Mulai : '.$jamMulai. ' Jam Selesai : '.$jamMulai;
		include("user_log.php");
		//====================================================================	

	}

	echo 	"<script>alert('Transaksi Output Pembekuan sudah diproses');
		window.location.replace('pmikomponen.php?module=pembekuan');
		</script>";

}
?>

	<font size="5" color=00008B><b>Input Pembekuan Produk</b></font><br><br>
<form name="pembekuan" id="pembekuan" onsubmit="return ok()" method="POST" action="<?=$PHPSELF?>">
	<table border=0 cellpadding=4  style="border-collapse:collapse">
        <tr>
            <th>No Transaksi</a></th>
            <th>:</th>
            <th align="left"><?=$noTrx?></th>
	</tr>
        <tr>
            <th>Petugas</a></th>
            <th>:</th>
            <th align="left"><?=$namalengkap?></th>
	</tr>
        <tr>
            <th>Tgl</a></th>
            <th>:</th>
            <th align="left"><?= $now?></th>
	</tr>
        <tr>
            <th>Jam Selesai</a></th>
            <th>:</th>
            <th align="left"><INPUT type="text"  name="jamSelesai" id="jamSelesai"></th>
	</tr>
	</table>

<br>

	<table border=1 cellpadding=4  style="border-collapse:collapse" id="box-table-b0">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;" id="box-table-b0">
            <th>No.</th>
	    <th>No Unik</th>
            <th>tgl Pembekuan</th>
            <th>Alat</th>
            <th>Jam Mulai</th>
	</tr>

<?php
$no=0;
$sql=mysql_query("SELECT * FROM `dpembekuan` WHERE `noTrans`='$noTrx'");
	while($dtBeku=mysql_fetch_assoc($sql)){
	$no++;
	?>
        <tr style="font-size:11px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
	   	<td align="right"><?=$no.'.'?></td>
		<td align="left"><?=$dtBeku['noKantong']?></td>
            	<td align="left" nowrap><?=$dtBeku['tgl']?></td>
            	<td align="center" nowrap><?=$dtBeku['alat']?></td>
            	<td align="left" nowrap><?=$dtBeku['jamMulai']?></td>
		</tr>
	<?}



?>


        </table>
<br>
<input type='submit' value='Submit' name='submit'>
</form>
</body>
</html>

