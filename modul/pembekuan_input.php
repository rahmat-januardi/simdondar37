<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$now = date("Y-m-d");
$noTr= "KO-CSF-".date("ymdHis");



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
$jamMulai=$_POST[jamMulai];
$csf=$_POST[csf];

	for ($i=0;$i<count($_POST[nokantong]);$i++) {
	      $nkantong=$_POST[nokantong][$i];

	      $sqlPembekuan=mysql_query("INSERT INTO `dpembekuan`(`noTrans`, `alat`, `noKantong`, `jamMulai`, `userProses`) VALUES ('$noTr','$csf','$nkantong','$jamMulai','$namauser')");

	}
	echo 	"<script>alert('Transaksi Input Pembekuan sudah diproses');
		window.location.replace('pmikomponen.php?module=pembekuan');
		</script>";

}
?>

	<font size="5" color=00008B><b>Input Pembekuan Produk</b></font><br><br>
<form name="pembekuan" id="pembekuan" onsubmit="return ok()" method="POST" action="<?=$PHPSELF?>">
	<table border=0 cellpadding=4  style="border-collapse:collapse">
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
            <th>Jam Mulai</a></th>
            <th>:</th>
            <th align="left"><INPUT type="text"  name="jamMulai" id="jamMulai"></th>
	</tr>
        <tr>
            <th>Alat</a></th>
            <th>:</th>
            <th align="left">
		<select name="csf" id="csf">
			<option value="1.2.1.4.6.68">Contact Shock Freezer</option>
			<option value="1.2.1.4.6.131">Contact Shock Freezer</option>
		</select>
	    </th>
	</tr>
        <tr>
            <th>No kantong</a></th>
            <th>:</th>
            <th align="left"><INPUT type="text"  name="nokantong" id="nokantong" onkeydown="chang(event,this);" onchange="addRow('box-table-b0')"/></th>
	</tr>
	</table>

<br>
	<table border=1 cellpadding=4  style="border-collapse:collapse" id="box-table-b0">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;" id="box-table-b0">
            <th>No.</th>
            <th>No Trans</th>
	    <th>No Unik</th>
	    <th>Produk</th>
	    <th>Gol Darah<br>(rh)</th>
            <th>tgl Pengolahan</th>
            <th>tgl Aftap</th>
            <th>Lama Ambil</th>
            <th>Kadalwarsa</th>
            <th>Petugas<br>Olah</th>
	</tr>
        </table>
<br>
<input type='submit' value='Submit' name='submit'>
</form>
</body>
</html>

