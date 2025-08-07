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
	<?
		if (isset($_POST[waktu])) {$tglawal=$_POST[waktu];$hariini=$hariini;}
		if ($_POST[waktu1]!='') $hariini=$_POST[waktu1];
        $status=$_POST['status'];
	?>
    <a name="atas" id="atas"></a>
	<font size="4" color=00008B>RINCIAN <b>PRODUK RELEASE</b></font><br><br>
	<form name="cari" method="POST" action="<?echo $PHPSELF?>">
		<table cellpadding=1 cellspacing="0" border="0">
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
				<td align="left" nowrap>Dari tanggal <input name="waktu" id="datepicker"  value="<?=$tglawal?>" type=text size=10></td>
				<td align="right" nowrap>sampai dengan tanggal <input name="waktu1" id="datepicker1" value="<?=$hariini?>" type=text size=10></td>
                <td align="right" nowrap>Status Release
                <?
                    $sel1='';$sel2='';$sel3='';$sel4='0';
                    switch ($status){
                        case '':$sel4='selected';break;
                        case '0':$sel1='selected';break;
                        case '1':$sel2='selected';break;
                        case '2':$sel3='selected';break;
                    }
                ?>
                <select name="status" class="styled-select">
                        <option value="0" <?=$sel1?>>Lulus</option>
                        <option value="1" <?=$sel2?>>Tidak Lulus</option>
                        <option value="2" <?=$sel3?>>Tidak Lulus dengan Catatan</option>
                        <option value=""  <?=$sel4?>>Semua</option>
                </select>

                </td>
				<td><input type=submit name=submit class="swn_button_blue" value="Tampilkan data"></td>
                    <td><a href="#bawah" class="swn_button_blue">Ke bawah</a></td>
			</tr>
		</table>	
	</form>
	<table border=1 cellpadding=4  style="border-collapse:collapse">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
			<th rowspan="2">NO.</th>
            <th rowspan="2">NO. RELEASE</th>
            <th rowspan="2">TGL. RELEASE</th>
			<th rowspan="2">NO. KANTONG</th>
            <th rowspan="2">JENIS PRODUK</th>
			<th rowspan="2">GOL. DARAH</th>
            <th colspan="5">SPESIFIKASI KANTONG & IDENTITAS</th>
            <th colspan="7">VISUAL</th>
            <th colspan="2">SELEKSI & AFTAP</th>
            <th rowspan="2">PENGO-<br>LAHAN</th>
            <th colspan="3">VOLUME PRODUK</th>
            <th colspan="2">PEMERIKSAAN LAB</th>
            <th colspan="5">HASIL PRODUK PROLIS</th>
		</tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th>Label & Identitas</th>
            <th>Kode Unik</th>
            <th>Tgl. Aftap</th>
            <th>Tgl. Olah</th>
            <th>Tgl. ED</th>

            <th>Kebocoran</th>
            <th>Selang</th>
            <th>Hemolysis</th>
            <th>Lipemik</th>
            <th>Ikterik</th>
            <th>Kehijauan</th>
            <th>Bekuan</th>

            <th>Seleksi</th>
            <th>Lama Aftap</th>

            <th>Berat</th>
            <th>Volume</th>
            <th>Standar Vol</th>
            <th>IMLTD & KGD</th>
            <th>History Donor</th>
            <th>Status</th>
            <th>Catatan</th>
            <th>Petugas</th>
            <th>Checker</th>
            <th>P.Jawab</th>
        </tr>

	<?php
	$no=0;
	$sql="SELECT * FROM `release`
		  WHERE DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '%$status%' order by rnotrans asc";
	//echo "$sql";
	$qraw=mysql_query($sql);
    $statusrelease='';
	while($tmp=mysql_fetch_assoc($qraw)){$no++;
        switch ($tmp['status']){
            case '0' : $statusrelease='Lulus';break;
            case '1' : $statusrelease='Tidak Lulus';break;
            case '2' : $statusrelease='Lulus dengan Catatan';break;
            default  : $statusrelease='-';
        }
		?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td align="right"><?=$no.'.'?></td>
			<td align="left"><?=$tmp['rnotrans']?></td>
            <td align="left"><?=$tmp['rtgl']?></td>
            <td align="left"><?=$tmp['rnokantong']?></td>
            <td align="left"><?=$tmp['rproduk']?></td>
            <td align="center"><?=$tmp['rgolda']?></td>
            <?if ($tmp['rspek_kantong']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rkode_unik']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <td align="left"><?=$tmp['rtgl_aftap']?></td>
            <td align="left"><?=$tmp['rtgl_olah']?></td>
            <td align="left"><?=$tmp['rtgl_ed']?></td>
            <?if ($tmp['rkebocoran']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rselang']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rhemolysis']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rlipemik']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rikterik']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rkehijauan']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rbekuan']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_seleksi']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_aftap']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_pengolahan']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
			<td align="right" nowrap><?=number_format($tmp['rberat_timbang'],2)?> gr</td>
            <td align="right" nowrap><?=number_format(round($tmp['rvolume'],2))?> ml</td>
            <?if ($tmp['rspek_volume']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_imltd']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_imltd_old']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <td align="left"><?=$tmp['rsatus_ket']?></td>
            <td align="left"><?=$tmp['rnote']?></td>
            <td align="left"><?=$tmp['ruser']?></td>
            <td align="left"><?=$tmp['rchecker']?></td>
            <td align="left"><?=$tmp['rpengesah']?></td>
		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=31 align="center">Tidak ada data penimbangan komponen darah</td>
	<?}?>
	</table><br>
	<a href="pmiqa.php?module=input_qa"class="swn_button_blue">Kembali</a>
	<a href="pmiqa.php?module=cetak_list_timbang"class="swn_button_blue">Cetak</a>
    <a href="#atas" class="swn_button_blue">Ke Atas</a>
    <a name="bawah" id="bawah"></a>
	<?
?>
</body>
</html>

