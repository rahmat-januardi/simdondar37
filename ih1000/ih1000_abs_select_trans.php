<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$tglsebelum = mktime(0,0,0,date("m"),1,date("Y"));
$tglawal=date("Y-m-d");
$hariini = date("Y-m-d");
$notransaksi="";
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

<body>
    <?php
    if (isset($_POST[waktu])) {$tglawal=$_POST[waktu];$hariini=$hariini;}
    if ($_POST[waktu1]!='') $hariini=$_POST[waktu1];
    ?>
    <table border=0><tr>
        <td align="left" style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">Antrian Konfirmasi ABO & ABS  - Erytra Eflexys</td></tr>
    </table>
    <form name="cari" method="POST" action="<?echo $PHPSELF?>">
        <table class="list" cellpadding=1 cellspacing="0" border="0">
            <tr class="field">
                <td align="left" nowrap>Dari tanggal :</td>
                <td><input name="waktu" id="datepicker"  value="<?=$tglawal?>" type=text size=10></td>
                <td align="right" nowrap>sampai tanggal :</td>
                <td><input name="waktu1" id="datepicker1" value="<?=$hariini?>" type=text size=10></td>
                <td><input type=submit name=submit class="swn_button_blue" value="Ok"></td>
            </tr>
        </table>
    </form>

    <table class="list" border=1 cellpadding=5 cellspacing=10 style="border-collapse:collapse" width="50%">
        <tr class="field">
            <td>NO.</td>
            <td>No. Mesin</td>
            <td>Tanggal</td>
            <td>ID Transaksi</td>
            <td>Operator<br>Erytra</td>
            <td>Jumlah <br>Sample</td>
            <td>Aksi</td>
        </tr>
    <?php
    $no=0;
    $jml=0;
    $sql="SELECT *, date(tgl_periksa) as tgl, count(id) as jml from lis_pmi.eflexys
          WHERE jenisperiksa=0 AND stat='' AND DATE(tgl_periksa)>='$tglawal' and DATE(tgl_periksa)<='$hariini'
          group by notrans";
    //echo $sql;
    $qraw=mysql_query($sql);
    while($tmp=mysql_fetch_assoc($qraw)){
        $no++;
        $jml=$jml+$tmp['jml'];
        ?>
        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            
            <td align="right"><?=$no.'.'?></td>
            <td><?=$tmp['mesin']?></td>
            <td><?=$tmp['tgl']?></td>
            <td><?=$tmp['notrans']?></td>
            <td><?=$tmp['petugas']?></td>
            <td align="right"><?=number_format($tmp['jml'],0,',','.')?></td>
            <td align="center"><a href="pmikonfirmasi.php?module=konfirm_ih1000&notrans=<?=$tmp['notrans']?>" class="swn_button_blue">Konfirm</a>
                                <a href="pmikonfirmasi.php?module=del_ih1000&notrans=<?=$tmp['notrans']?>" class="swn_button_red">Hapus</a></td>
        </tr>
    <?}
    if ($no==0){?>
        <tr class="record">
            <td colspan=7>Tidak ada data</td></tr>
    <?} else {
        ?><tr class="field">
            <td colspan=5>Total Sample</td>
            <td align="right"><?=number_format($jml,0,',','.')?></td>
            <td></td>
        </tr><?
    }?>
    </table><br>
    <a href="pmikonfirmasi.php?module=ih1000" class="swn_button_blue">Kembali ke Awal</a>
    <?
?>
</body>
</html>



