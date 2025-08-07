<?php
require_once('config/db_connect.php');

session_start();
$namaudd=$_SESSION[namaudd];
$trans = $_GET['notrans'];
?>
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>

<style type="text/css">
    @import url("topstyle.css");tr { background-color: #FFF8DC}.initial { background-color: #FFF8DC; color:#000000 }
    .normal { background-color: #FFF8DC }.highlight { background-color: #7FFF00 }
</style>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SIMDONDAR</title>
</head>
<style>
 .awesomeText{
  color : #000;
  font-size : 150%;
}
</style>
<STYLE>
  tr { background-color: #FFF8DC}
  .initial { background-color: #FFF8DC; color:#000000 }
  .normal { background-color: #FFF8DC }
  .highlight { background-color: #7CFC00 }
</style>
<?
include('clogin.php');
include('config/db_connect.php');
$namauser=$_SESSION[namauser];
$today3=date('Y-m-d H:i:s');
$trans = $_GET['notrans'];

 ?>
    <body onLoad=setFocus()>
    <form name="kantong" method="POST" action="<?=$PHPSELF?>" onkeydown="return event.key != 'Enter';">
    
    <?
        $no =0;
        $ambil =     "SELECT\n".
"                lis_pmi.eflexys.tgl_periksa,\n".
"                lis_pmi.eflexys.nolot_abo,\n".
"                lis_pmi.eflexys.nolot_abs,\n".
"                lis_pmi.eflexys.expired_abo,\n".
"                lis_pmi.eflexys.expired_abs,\n".
"                SUBSTR(lis_pmi.eflexys.nokantong,1,8) as NoKantong,\n".
"                stokkantong.NoKantong as nokt,                \n".
"                stokkantong.gol_darah as gollama,\n".
"                stokkantong.Status as statk,\n".
"                stokkantong.sah,                \n".
"                stokkantong.RhesusDrh as rhlama,\n".
"                lis_pmi.eflexys.gol AS golbaru,\n".
"                lis_pmi.eflexys.rh  AS rhesus,\n".
"                CASE lis_pmi.eflexys.gol\n".
"                   WHEN 'A' THEN '4+'\n".
"                   WHEN 'O' THEN 'Neg'\n".
"                   WHEN 'B' THEN 'Neg'\n".
"                   WHEN 'AB' THEN '4+'\n".
"                   ELSE lis_pmi.eflexys.gol END AS antiA,\n".
"                CASE lis_pmi.eflexys.gol\n".
"                   WHEN 'A' THEN 'Neg'\n".
"                   WHEN 'O' THEN 'Neg'\n".
"                     WHEN 'B' THEN '4+'\n".
"                   WHEN 'AB' THEN '4+'\n".
"                   ELSE lis_pmi.eflexys.gol END AS antiB,\n".
"                CASE lis_pmi.eflexys.gol\n".
"                   WHEN 'A' THEN '4+'\n".
"                   WHEN 'O' THEN 'Neg'\n".
"                     WHEN 'B' THEN '4+'\n".
"                   WHEN 'AB' THEN '4+'\n".
"                   ELSE lis_pmi.eflexys.gol END AS antiAB,\n".
"                CASE lis_pmi.eflexys.gol\n".
      "                   WHEN 'A' THEN 'Neg'\n".
      "                   WHEN 'O' THEN '4+'\n".
      "                     WHEN 'B' THEN '4+'\n".
      "                   WHEN 'AB' THEN 'Neg'\n".
      "                   ELSE lis_pmi.eflexys.gol END AS TA,\n".
      "                CASE lis_pmi.eflexys.gol\n".
      "                   WHEN 'A' THEN '4+'\n".
      "                   WHEN 'O' THEN '4+'\n".
      "                     WHEN 'B' THEN 'Neg'\n".
      "                   WHEN 'AB' THEN 'Neg'\n".
      "                   ELSE lis_pmi.eflexys.gol END AS TB,\n".
"                CASE lis_pmi.eflexys.gol\n".
"                   WHEN 'A' THEN '4+'\n".
"                   WHEN 'O' THEN '4+'\n".
"                     WHEN 'B' THEN 'Neg'\n".
"                   WHEN 'AB' THEN 'Neg'\n".
"                   ELSE lis_pmi.eflexys.gol END AS TEO,\n".
"                    CASE lis_pmi.eflexys.gol\n".
"                   WHEN 'A' THEN 'Neg'\n".
"                   WHEN 'O' THEN 'Neg'\n".
"                     WHEN 'B' THEN 'Neg'\n".
"                   WHEN 'AB' THEN 'Neg'\n".
"                   ELSE lis_pmi.eflexys.gol END AS AC,\n".
"                CASE lis_pmi.eflexys.rh\n".
"                   WHEN '+' THEN '4+'\n".
"                   WHEN '-' THEN 'Neg'\n".
"                   ELSE lis_pmi.eflexys.rh END AS DVI,\n".
"                lis_pmi.eflexys.petugas,\n".
"                stokkantong.`Status`,\n".
"                stokkantong.kodePendonor\n".
"                FROM\n".
"                lis_pmi.eflexys\n".
"                INNER JOIN stokkantong ON lis_pmi.eflexys.nokantong = stokkantong.noKantong\n".
"                WHERE lis_pmi.eflexys.stat='1' AND lis_pmi.eflexys.notrans='$trans' AND lis_pmi.eflexys.jenisperiksa=0\n".
"                GROUP BY lis_pmi.eflexys.nokantong order by tgl_periksa ASC";
?>
<?
        // $ambilreagen =    mysql_fetch_assoc(mysql_query($ambil));
?>
      
      <h1 style="color:red;font-weight:bold;">REKAP PEMERIKSAAN KONFIRMASI GOLONGAN DARAH ERYTRA EFLEXIS</h1>


      <table border=1 cellpadding=3 cellspacing=5 style="border-collapse:collapse" >
      <tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;"
        onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align='center'>
                        <td align='center'>No</td>
                        <td align='center' width=100px>Tgl. Insert</td>
                        <td align='center' width=100px>No Kantong</td>
                        <td align='center' width=50px>Gol Darah Lama</td>
                        <td align='center'>Konfirmasi</td>
                        <td align='center'>Status</td>
                        
                        <td align='center' width=100px>Anti A</td>
                        <td align='center' width=100px>Anti B</td>
                        <td align='center' width=100px>Anti AB</td>

                        <td align='center' width=100px>DVI-</td>
                        <td align='center' width=100px>DVI+</td>
                        <td align='center' width=100px>Ctl</td>

                        <td align='center' width=100px>N/A1</td>
                        <td align='center' width=100px>N/B</td>
                        <td align='center' width=100px>ABS</td>
                        <td align='center' width=100px>No. Lot ABO</td>
                        <td align='center' width=100px>Exp. ABO</td>
                        <td align='center' width=100px>No. Lot ABS</td>
                        <td align='center' width=100px>Exp. ABS</td>
                        <!--td align='center' width=100px>Kode Pendonor</td-->
                        <td align='center' width=100px>Status Kantong</td>
                        
                        </tr>
        <?
        $ambildata =    mysql_query($ambil);
        while ($temp=mysql_fetch_assoc($ambildata)) {
        $no++;
        ?>
        
            <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
                <td class=input><?=$no?></td>
                <td class=input><?=$temp[tgl_periksa]?></td>
                <td class=input><?=$temp[nokt]?></td>
                <td class=input><?=$temp[gollama]?>(<?=$temp[rhlama]?>)</td>
                <td class=input align=center><?=$temp[golbaru]?>(<?=$temp[rhesus]?>)</td>
                
                 
                <?
                    if (($temp[golbaru]==$temp[gollama])){
                        $Cocok="Cocok";
                    }else{
                        $Cocok="Tidak Cocok";
                    }
            
                    if ($temp[AC]=='Neg'){$act = "1";}else{$act = "0";}
                    
                ?>
                <td class=input><?=$Cocok?></td>
                
                <td class=input><?=$temp[antiA]?></td>
                <td class=input><?=$temp[antiB]?></td>
                <td class=input><?=$temp[antiAB]?></td>
            
                <td class=input><?=$temp[DVI]?></td>
                <td class=input><?=$temp[DVI]?></td>
                <td class=input><?=$temp[AC]?></td>
            
                <td class=input><?=$temp[TA]?></td>
                <td class=input><?=$temp[TB]?></td>
                
                
                <?php $abs = mysql_fetch_assoc(mysql_query("select abs from lis_pmi.eflexys where nokantong='$temp[nokt]' and jenisperiksa=1"));?>
                <td class=input><?=$abs[abs]?></td>
                <td class=input><?=$temp[nolot_abo]?></td>
                <td class=input><?=$temp[expired_abo]?></td>
                <td class=input><?=$temp[nolot_abs]?></td>
                <td class=input><?=$temp[expired_abs]?></td>
                <!--td class=input><input type="hidden" name=kodep[] value=<?=$temp[kodePendonor]?>><?=$temp[kodePendonor]?></td-->
                <?
                $status_ktg=$temp['Status']; $kantong_sah=$temp['sah'];
            switch ($status_ktg){
                case '0' : $statuskantong='Kosong('.$status_ktg.')';
                           if ($c_ktg[StatTempat]==NULL) $statuskantong='Kosong-Logistik('.$status_ktg.')';
                           if ($c_ktg[StatTempat]=='0')  $statuskantong='Kosong-Logistik ('.$status_ktg.')';
                           if ($c_ktg[StatTempat]=='1')  $statuskantong='Kosong-Aftap('.$status_ktg.')';
                           break;
                case '1' : $statuskantong='Karantina('.$status_ktg.')';
                            break;
                case '2' : $statuskantong='Sehat';
                            if (substr($c_ktg[stat2],0,1)=='b') $tempat=" (BDRS)";
                            break;
                case '3' : $statuskantong='Keluar';break;
                case '4' : $statuskantong='Rusak';break;
                case '5' : $statuskantong='Rusak-Gagal';break;
                case '6' : $statuskantong='Dimusnahkan';break;
                default  : $statuskantong='-';
            }
                ?>
                <td class=input><?=$statuskantong?></td>

        </tr>
                <?}?>
                </table>
<br>
      <a href="pmikonfirmasi.php?module=ih1000_to_data" class="swn_button_blue">Kembali</a>
</form>


