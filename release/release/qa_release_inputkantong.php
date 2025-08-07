<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$tglsebelum = mktime(0,0,0,date("m"),1,date("Y"));
$tglawal=date("Y-m-d");
$hariini = date("Y-m-d");
?>
<!DOCTYPE html>
<link type="text/css" href="../css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="../css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="../css/style.css" rel="stylesheet" />
<html>

<head>
<style>
    body {font-family: "Lato", sans-serif;}
</style>

<script type="text/javascript" language="JavaScript">
  document.forms['prolis'].elements['noktg'].focus();
</script>

</head>
<body OnLoad="document.prolis.noktg.focus();">
<form name="prolis" method=post>
    <font size="4" color=00008B>PELULUSAN <br> </font>
    <INPUT type="text"  name="noktg" style="text-transform:uppercase" minlength="5" required="">
    <input type=submit name=cari value=OK class="swn_button_blue">
    <a href="pmiqa.php?module=input_qa"class="swn_button_blue">Kembali</a>
</form>
<?
if (isset($_POST[cari])) {
    $nkt=$_POST[noktg];
    $sql="select * from stokkantong where upper(nokantong)=upper('$nkt') and status='2' and sah='1'";
    //echo "$sql";
    $stokkantong=mysql_fetch_assoc(mysql_query($sql));
    if (strtoupper($stokkantong['noKantong'])==strtoupper($nkt)){
        $URL="pmiqa.php?module=release_proses&nokantong=$nkt&mode=2";
        header("Location: $URL");
    }else{
        echo "<SCRIPT>alert('Nomor kode komponen darah yang anda masukkan tidak ada atau belum selesai proses di sistem!!.');</SCRIPT>";
    }
}?>
</body>
</html> 

