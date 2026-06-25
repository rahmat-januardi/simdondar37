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
    <font size="4" color=00008B>QC Produk Plasma Aferesis ==Masukkan No.Kantong==<br> </font><br/>
    <INPUT type="text"  name="noktg" style="text-transform:uppercase" minlength="5" required="">
    <input type=submit name=cari value=OK class="swn_button_blue">
    <a href="pmiqc.php?module=menu_ffp"class="swn_button_blue">Kembali</a>
</form>
<?
if (isset($_POST[cari])) {
    $nkt=$_POST[noktg];
    $sql="select * from stokkantong where upper(nokantong)=upper('$nkt')";
    $stokkantong=mysql_fetch_assoc(mysql_query($sql));
    if (($stokkantong['Status']=='2') and ($stokkantong['sah']=='1') and ($stokkantong['statQC']=='0') and ($stokkantong['statKonfirmasi']=='1') and ($stokkantong['produk']=='LP Aferesis')){
        $URL="pmiqc.php?module=qcffp_prosespa&nokantong=$nkt&mode=2";
        header("Location: $URL");
    }else{
        switch ($stokkantong['Status']){
            case '0' :  $statuskantong='Kosong';
                        if ($stokkantong[StatTempat]==NULL) $statuskantong='Kosong - di Logistik';
                        if ($stokkantong[StatTempat]=='0')  $statuskantong='Kosong - di Logistik';
                        if ($stokkantong[StatTempat]=='1')  $statuskantong='Kosong - di Aftap';
                        break;
            case '1' :  if ($stokkantong['sah']=="1"){
                            $statuskantong='Karantina';
                        } else{
                            $statuskantong='Belum disahkan';
                        }
                        break;
            case '2' :  $statuskantong='Sehat';
                        if (substr($stokkantong[stat2],0,1)=='b') $tempat=" (BDRS)";
                        break;
            case '3' : $statuskantong='Keluar';break;
            case '4' : $statuskantong='Rusak';break;
            case '5' : $statuskantong='Rusak-Gagal';break;
            case '6' : $statuskantong='Dimusnahkan';break;
            default  : $statuskantong='-';
        }
        echo "<SCRIPT>alert('Silahkan cek kantong produk yang anda masukkan');</SCRIPT>";
        //echo "<SCRIPT>alert('Produk/Komponen darah yang anda masukkan tidak dapat dilakukan release, karena statusnya : $statuskantong.');</SCRIPT>";
    }
}?>
</body>
</html> 

