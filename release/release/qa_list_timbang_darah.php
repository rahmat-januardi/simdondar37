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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	<?
		if (isset($_POST[waktu])) {$tglawal=$_POST[waktu];$hariini=$hariini;}
		if ($_POST[waktu1]!='') $hariini=$_POST[waktu1];
        $status=$_POST['status'];
	?>
	<font size="4" color=00008B>REKAP PENIMBANGAN KOMPONEN DARAH <b>PRODUK RELEASE</b></font><br><br>
	<form name="cari" method="POST" action="<?echo $PHPSELF?>">
		<table class="list" cellpadding=1 cellspacing="0" border="0">
			<tr class="field">
				<td align="left" nowrap>Dari tanggal :</td>
				<td><input name="waktu" id="datepicker"  value="<?=$tglawal?>" type=text size=10></td>
				<td align="right" nowrap>sampai dengan tanggal :</td>
				<td><input name="waktu1" id="datepicker1" value="<?=$hariini?>" type=text size=10></td>
                <td align="right" nowrap>Status konfirmasi :</td>
                <?
                    $sel1='';$sel2='';$sel3='';
                    switch ($status){
                        case '':$sel3='selected';break;
                        case '0':$sel1='selected';break;
                        case '1':$sel2='selected';break;
                    }
                ?>
                <td><select name="status">
                        <option value="0" <?=$sel1?>>Belum diperiksa</option>
                        <option value="1" <?=$sel2?>>Sudah diperiksa</option>
                        <option value="" <?=$sel3?>>Semua</option>
                </select>

                </td>
				<td><input type=submit name=submit class="swn_button_blue" value="Ok"></td>
			</tr>
		</table>	
	</form>
	<table class="list" border=1 cellpadding=4  style="border-collapse:collapse">
		<tr class="field">
			<td>NO.</td>
            <td>TANGGAL</td>
			<td>NO KANTONG</td>
            <td>STATUS KANTONG</td>
			<td>BERAT (gr)</td>
			<td>PETUGAS TIMBANG</td>
			<td>RELEASE</td>
			<td>NO.TRANS</td>
			<td>TANGGAL RELEASE</td>
			<td>AKSI</td>
		</tr>
	<?php
	$no=0;
	$sql="SELECT t.`id`, t.`waktu`, t.`user`, u.`nama_lengkap`, t.`bagian`, t.`nokantong`, t.`berat_ukur`,
          case when t.`konfirm`='0' then '-' else 'Sudah' end as konfirm,
          t.`waktu_konfirm`, t. `notrans`,  s.status, s.sah, s.StatTempat, s.stat2
          FROM `timbang_darah` t left join `user` u on t.`user`=u.`id_user`
          left join stokkantong s on t.nokantong=s.noKantong
		  WHERE  t.`bagian`='PROLIS' AND DATE(t.waktu)>='$tglawal AND date(t.waktu)<=$hariini' and t.`konfirm` like '%$status%' ";
	$qraw=mysql_query($sql);
    $statuskantong='';

	while($tmp=mysql_fetch_assoc($qraw)){
		$no++;
        $status_ktg=$tmp['status'];
        switch ($status_ktg){
            case '0' : $statuskantong='Kosong';
                if ($tmp[StatTempat]==NULL) $statuskantong='Kosong di Logistik';
                if ($tmp[StatTempat]=='0')  $statuskantong='Kosong diLogistik';
                if ($tmp[StatTempat]=='1')  $statuskantong='Kosong di Aftap';
                break;
            case '1' : if ($tmp['sah']=="1"){
                $statuskantong='Karantina';
            } else{
                $statuskantong='Belum disahkan';
            }
                break;
            case '2' : $statuskantong='Sehat';
                if (substr($tmp[stat2],0,1)=='b') $tempat=" (BDRS)";
                break;
            case '3' : $statuskantong='Keluar';break;
            case '4' : $statuskantong='Rusak';break;
            case '5' : $statuskantong='Rusak-Gagal';break;
            case '6' : $statuskantong='Dimusnahkan';break;
            default  : $statuskantong='Tidak diketahui';
        }
		?>
		<tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td align="right"><?=$no.'.'?></td>
            <td align="left"><?=$tmp['waktu']?></td>
			<td align="left"><?=$tmp['nokantong']?></td>
            <td align="left"><?=$statuskantong?></td>
			<td align="right"><?=number_format($tmp['berat_ukur'],2)?></td>
			<td align="left"><?=$tmp['nama_lengkap']?></td>
            <?if ($tmp['konfirm']=='-'){?><td align="center">-</td><?}else{?><td align="center"><b>&radic;</b></td><?}?>
			<td align="left"><?=$tmp['notrans']?></td>
			<td align="left"><?=$tmp['waktu_konfirm']?></td>
			<td>
				<?
				if(($tmp['konfirm']=='-') and ($status_ktg=='2')){?>
					<a href="pmiqa.php?module=release_proses&nokantong=<?=$tmp['nokantong']?>&mode=1&id=<?=$tmp['id']?>">Release</a>
				<?}?>
			</td>	
		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=10 align="center">Tidak ada data penimbangan komponen darah</td>
	<?}?>
	</table><br>
	<a href="pmiqa.php?module=input_qa"class="swn_button_blue">Kembali</a>
	<a href="pmiqa.php?module=cetak_list_timbang"class="swn_button_blue">Cetak</a>
	<?
?>
</body>
</html>

