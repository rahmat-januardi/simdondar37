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
	<font size="4" color=00008B>MASTER BERAT KANTONG KOSONG</font><br><br>
	<table class="list" border=2 cellpadding=4 cellspacing=4 style="border-collapse:collapse" width="800px">
		<tr class="field">
			<td rowspan="2">NO.</td>
			<td rowspan="2">MERK KANTONG</td>
            <td rowspan="2">LAMA BUKA (HARI)</td>
			<td rowspan="2">VOLUME KANTONG</td>
			<td rowspan="2">JENIS</td>
			<td colspan="8">BERAT KANTONG KOSONG<br>  satuan : gram</td>
            <td rowspan="2">AKSI</td>
		</tr>
		<tr class="field">
			<td>KANTONG UTAMA</td>
			<td>SATELIT 1</td>
			<td>SATELIT 2</td>
			<td>SATELIT 3</td>
			<td>SATELIT 4</td>
			<td>SATELIT 5</td>
			<td>SATELIT 6</td>
			<td>SATELIT 7</td>
		</tr>
	<?php
	$no=0;
	$sql="SELECT `id`, `merk`, `jenis`,
	 case `jenis`
        when '1' then 'Single'
        when '2' then 'Double'
        when '3' then 'Tripple'
        when '4' then 'Quadrupple'
        when '5' then 'Quadrupple T&B'
        when '6' then 'Pediatrik'
        end as jeniskantong, `vol`,
	`ket`, `berat_ku`, `berat_s1`, `berat_s2`, `berat_s3`, `berat_s4`, `berat_s5`, `berat_s6`, `berat_s7`, `lama_buka` FROM `master_kantong`
	order by `merk`, `jenis`";
	$qraw=mysql_query($sql);
	while($tmp=mysql_fetch_assoc($qraw)){
		$no++;
		if ($tmp['berat_s7']=='0.000') {$brts7='';}else{$brts7=$tmp['berat_s7'];}
		if ($tmp['berat_s6']=='0.000') {$brts6='';}else{$brts6=$tmp['berat_s6'];}
		if ($tmp['berat_s5']=='0.000') {$brts5='';}else{$brts5=$tmp['berat_s5'];}
		if ($tmp['berat_s4']=='0.000') {$brts4='';}else{$brts4=$tmp['berat_s4'];}
		if ($tmp['berat_s3']=='0.000') {$brts3='';}else{$brts3=$tmp['berat_s3'];}
		if ($tmp['berat_s2']=='0.000') {$brts2='';}else{$brts2=$tmp['berat_s2'];}
		if ($tmp['berat_s1']=='0.000') {$brts1='';}else{$brts1=$tmp['berat_s1'];}
		?>
		<tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td align="right"><?=$no.'.'?></td>
			<td><?=$tmp['merk']?></td>
            <td><?=$tmp['lama_buka']?></td>
			<td><?=$tmp['vol']?></td>
			<td><?=$tmp['jeniskantong']?></td>
			<td><?=$tmp['berat_ku']?></td>
			<td><?=$brts1?></td>
			<td><?=$brts2?></td>
			<td><?=$brts3?></td>
			<td><?=$brts4?></td>
			<td><?=$brts5?></td>
			<td><?=$brts6?></td>
			<td><?=$brts7?></td>

			<td nowrap>
				<a href="pmiqa.php?module=edit_beratkantong&id=<?=$tmp['id']?>" alt="Mengubah berat kantong"  >Ubah</a>
		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=13 align="center">Belum ada data berat kantong kosong, silahkan tambah data</td>
	<?}?>
	</table><br>
	<a href="pmiqa.php?module=input_qa"class="swn_button_blue">Kembali</a>
    <a href="pmiqa.php?module=kantong"class="swn_button_blue">Tambah</a>
    <a href="pmiqa.php?module=cetak_list_kantong"class="swn_button_blue">Cetak</a>
	<?
?>
</body>
</html>

