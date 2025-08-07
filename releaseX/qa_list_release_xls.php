<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=release.xls");
header("Pragma: no-cache");
header("Expires: 0");
require_once('clogin.php');
require_once('config/db_connect.php');

$v_tgl1    	=$_GET[tgl1];
$v_tgl2    	=$_GET[tgl2];
$v_ptgs		=$_GET[ptgs];
$v_stts		=$_GET[stts];
?>

<font size="4" color=00008B>RINCIAN <b>PRODUK RELEASE</b></font><br><br>
<table border=0 cellpadding=4>
<tr>
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
        <tr>
            <th>Label & Identitas</th>
            <th>Kode Unik</th>
            <th>Tgl. Aftap</th>
            <th>Tgl. Produksi</th>
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
		  WHERE DATE(rtgl)>='$v_tgl1' AND date(rtgl)<='$v_tgl2' and rstatus like '%$v_stts%' and `ruser` like '%$v_ptgs%' order by rnotrans asc";
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
        <tr style="font-size:11px; color:#000000; font-family:Verdana;">
			<td align="right"><?=$no.'.'?></td>
			<td align="left"><?=$tmp['rnotrans']?></td>
            <td align="left" nowrap><?=$tmp['rtgl']?></td>
            <td align="center" nowrap><?=$tmp['rnokantong']?></td>
            <td align="left" nowrap><?=$tmp['rproduk']?></td>
            <td align="center"><?=$tmp['rgolda']?></td>
            <?if ($tmp['rspek_kantong']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rkode_unik']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <td align="left" nowrap><?=$tmp['rtgl_aftap']?></td>
            <td align="left" nowrap><?=$tmp['rtgl_olah']?></td>
            <td align="left" nowrap><?=$tmp['rtgl_ed']?></td>
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
            <td align="left" nowrap><?=$tmp['rsatus_ket']?></td>
            <td align="left"><?=$tmp['rnote']?></td>
            <td align="left"><?=$tmp['ruser']?></td>
            <td align="left"><?=$tmp['rchecker']?></td>
            <td align="left"><?=$tmp['rpengesah']?></td>
		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=31 align="center">Tidak ada data release produk dari tanggal, petugas dan status yang dipilih</td>
	<?}?>
	</table>
