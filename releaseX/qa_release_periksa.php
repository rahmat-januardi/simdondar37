<font size="2" color=black><br><b>Pemeriksaan metode ELISA/CLHIA</font></b>
<?
$var_imltd='0';
$var_jenis_imltd='0';
//DATA PEMERIKSAAN IMLTDELISA ==========================================================================================
$sq_elisa=mysql_query("SELECT `id`, `noKantong`, `OD`, `COV`, `notrans`,
                          case
                            when `jenisPeriksa`='0' then 'HBsAg'
                            when `jenisPeriksa`='1' then 'Anti HCV'
                            when `jenisPeriksa`='2' then 'Anti HIV'
                            when `jenisPeriksa`='3' then 'Syphilis' End As Parameter,
                          case
                            when `Hasil`='0' then 'Non Reaktif'
                            when `Hasil`='1' then 'Reaktif'
                            when `Hasil`='2' then 'Grayzone'  End As Hasil,
                          `tglPeriksa`, `dicatatOleh`, `dicekOleh`, `DisahkanOleh`, `noLot`, `Metode`, `ulang`, `up_data`, `insert_on`
                          FROM `hasilelisa` WHERE `noKantong`='$no_kantonga' order by `id`");

?>
<table cellpadding=3 cellspacing=3 width="100%">
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th rowspan="2">ID</th>
        <th rowspan="2">Kantong<br> Utama</th>
        <th rowspan="2">Transaksi</th>
        <th rowspan="2">Tanggal</th>
        <th rowspan="2">Parameter</th>
        <th rowspan="2">OD</th>
        <th rowspan="2">Hasil</th>
        <th colspan="3">Reagen</th>
        <th rowspan="2">Pencatat</th>
        <th rowspan="2">Di Cek</th>
        <th rowspan="2">Disahkan</th>
    </tr>
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th>Nama</th>
        <th>Lot</th>
        <th>ED</th>
    </tr>
    <?
    $no="0";
    while ($imltd=mysql_fetch_assoc($sq_elisa)){$no++;
        if (($imltd[Hasil]=="Reaktif") or ($imltd[Hasil]=="Grayzone")){$var_imltd='1';}
        $sq_reagen=mysql_fetch_assoc(mysql_query("SELECT `Nama`, `noLot`, `tglKad`  FROM `reagen` WHERE kode='$imltd[noLot]'"));
	if ($sq_reagen[noLot]==""){
		$sq_reagen=mysql_fetch_assoc(mysql_query("SELECT `Nama`, `noLot`, `tglKad`  FROM `reagen` WHERE noLot='$imltd[noLot]'"));
	}
        ?>
        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td class=input><?=$imltd[id]?></td>
            <td class=input><?=$imltd[noKantong]?></td>
            <td class=input><?=$imltd[notrans]?></td>
            <td class=input><?=$imltd[tglPeriksa]?></td>
            <td class=input><?=$imltd[Parameter]?></td>
            <td class=input><?=$imltd[OD]?></td>
            <?if($imltd[Hasil]=="Non Reaktif"){?><td align="left"><font color="black"><?=$imltd[Hasil]?></font></td><?} else {?><td align="left"><font color="red"><?=$imltd[Hasil]?></font></td><?}?>
            <td class=input><?=$sq_reagen[Nama]?></td>
            <td class=input><?=$sq_reagen[noLot]?></td>
            <td class=input><?=$sq_reagen[tglKad]?></td>
            <td class=input><?=$imltd[dicatatOleh]?></td>
            <td class=input><?=$imltd[dicekOleh]?></td>
            <td class=input><?=$imltd[DisahkanOleh]?></td>
        </tr>
    <?}
    if ($no=="0"){
        ?><tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="13" class=input align="center">TIDAK ADA DATA PEMERIKSAAN IMLTD METODE ELISA/CHLIA</td>
        </tr>
    <?}
    if ($no!=='0'){$var_jenis_imltd='0';}
    ?>
</table>

<?
//DATA PEMERIKSAAN IMLTD RAPID================================================================================================
$sq_rapid=mysql_query("SELECT `id`, `NoTrans`, `noKantong`, `Kontrol`,
						case
							when `jenisperiksa`='0' then 'HBsAg'
						    when `jenisperiksa`='1' then 'Anti HCV'
						    when `jenisperiksa`='2' then 'Anti HIV'
						    when `jenisperiksa`='3' then 'Syphilis' End As Parameter,
						 case
						    when `Hasil`='1' then 'Non Reaktif'
						    when `Hasil`='0' then 'Reaktif' End As Hasil,
						 `nolot`, `tgl_tes`, `dicatatoleh`, `dicekOleh`, `DisahkanOleh`, `Metode`, `ulang`, `up_data`
						FROM `drapidtest` WHERE `noKantong`='$nkt' order by `id`");
?>
<font size="2" color=black><b>Pemeriksaan metode RAPID</font></b>
<table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th rowspan="2">ID</th>
        <th rowspan="2">Kantong<br>Utama</th>
        <th rowspan="2">Transaksi</th>
        <th rowspan="2">Tanggal</th>
        <th rowspan="2">Parameter</th>
        <th rowspan="2">Kontrol</th>
        <th rowspan="2">Hasil</th>
        <th colspan="3">Reagen</th>
        <th rowspan="2">Pencatat</th>
        <th rowspan="2">Di Cek</th>
        <th rowspan="2">Disahkan</th>
    </tr>
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th>Nama</th>
        <th>Lot</th>
        <th>ED</th>
    </tr>
    <?
    $no="0";
    while ($imltd_r=mysql_fetch_assoc($sq_rapid)){$no++;
        if ($imltd_r[Hasil]=="Reaktif"){$var_imltd='1';}
        $sq_reagen=mysql_fetch_assoc(mysql_query("SELECT `Nama`, `noLot`, `tglKad`  FROM `reagen` WHERE kode='$imltd_r[nolot]'"));
        ?>
        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td class=input><?=$imltd_r[id]?></td>
            <td class=input><?=$imltd_r[noKantong]?></td>
            <td class=input><?=$imltd_r[NoTrans]?></td>
            <td class=input><?=$imltd_r[tgl_tes]?></td>
            <td class=input><?=$imltd_r[Parameter]?></td>
            <td class=input><?=$imltd_r[Kontrol]?></td>
            <?if($imltd_r[Hasil]=="Non Reaktif"){?><td align="left"><font color="black"><?=$imltd_r[Hasil]?></font></td><?} else {?><td align="left"><font color="red"><?=$imltd_r[Hasil]?></font></td><?}?>
            <td class=input><?=$sq_reagen[Nama]?></td>
            <td class=input><?=$sq_reagen[noLot]?></td>
            <td class=input><?=$sq_reagen[tglKad]?></td>
            <td class=input><?=$imltd_r[dicatatoleh]?></td>
            <td class=input><?=$imltd_r[dicekOleh]?></td>
            <td class=input><?=$imltd_r[DisahkanOleh]?></td>
        </tr>
    <?}
    if ($no=="0"){
        ?><tr style="color:#000000; " onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="13" class=input align="center">TIDAK ADA DATA PEMERIKSAAN IMLTD METODE RAPID</td>
        </tr>
    <?}
    if ($no!=='0'){$var_jenis_imltd='0';}
    ?>
</table>
<?
//DATA PEMERIKSAAN NAT===========================================================================================

$sq_nat=mysql_query("SELECT *,
                         
                          case
                            when `Hasil`='0' then 'Non Reaktif'
                            when `Hasil`='1' then 'Reaktif'
                            when `Hasil`='2' then 'Grayzone'  End As Hasil
                          
                          FROM `hasilnat` WHERE `noKantong` = '$no_kantonga' order by `natid`");

?>
<font size="2" color=black><b>Pemeriksaan metode NAT</font></b>
<table cellpadding=3 cellspacing=3 width="100%">
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th rowspan="2">ID</th>
        <th rowspan="2">Kantong<br> Utama</th>
       
        <th rowspan="2">Tanggal</th>
        
        <th rowspan="2">OD</th>
        <th rowspan="2">Hasil</th>
        <th colspan="3">Reagen</th>
        <th rowspan="2">Pencatat</th>
        <th rowspan="2">Di Cek</th>
        <th rowspan="2">Disahkan</th>
    </tr>
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th>Nama</th>
        <th>Lot</th>
        <th>ED</th>
    </tr>
    <?
    $no="0";
    while ($imltdn=mysql_fetch_assoc($sq_nat)){$no++;
        if (($imltdn[Hasil]=="Reaktif") or ($imltdn[Hasil]=="Grayzone")){$var_imltd='1';}
        $sq_reagen=mysql_fetch_assoc(mysql_query("SELECT `Nama`, `noLot`, `tglKad`  FROM `reagen` WHERE kode='$imltd[noLot]'"));
	
        ?>
        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td class=input><?=$imltdn[natid]?></td>
            <td class=input><?=$imltdn[noKantong]?></td>
            
            <td class=input><?=$imltdn[tglPeriksa]?></td>
            
            <td class=input><?=$imltdn[OD]?></td>
            <?if($imltdn[Hasil]=="Non Reaktif"){?><td align="left"><font color="black"><?=$imltdn[Hasil]?></font></td><?} else {?><td align="left"><font color="red"><?=$imltdn[Hasil]?></font></td><?}?>
            <td class=input>Ultrio</td>
            <td class=input><?=$imltdn[noLot]?></td>
            <td class=input><?=$imltdn[ed]?></td>
            <td class=input><?=$imltdn[dicatatOleh]?></td>
            <td class=input><?=$imltdn[dicatatOleh]?></td>
            <td class=input><?=$imltdn[DisahkanOleh]?></td>
        </tr>
    <?}
    if ($no=="0"){
        ?><tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="13" class=input align="center">TIDAK ADA DATA PEMERIKSAAN NAT</td>
        </tr>
    <?}
    if ($no!=='0'){$var_jenis_imltd='0';}
    ?>
</table>
<?
$var_kgd='0';
//DATA KONFIRMASI GOLONGAN DARAH================================================================================
?>
<font size="2" color=black><b>Pemeriksaan Konfirmasi Golongan Darah</font></b>
<table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr style="background-color:mistyrose; font-size:10px; color:#000000;">
        <td rowspan='3'>No</td>
        <td rowspan='3'>Tanggal</td>
        <td rowspan='3'>No Konfirmasi</td>
        <td rowspan='3'>Kantong Utama</td>
        <td rowspan='3'>Gol(Rh) Darah Asal</td>
        <td rowspan='3'>Gol(Rh) Darah Baru</td>
        <td rowspan='3'>Hasil</td>
        <td rowspan='3'>Metode</td>
        <td colspan='3'>Anti A</td>
        <td colspan='3'>Anti B</td>
        <td colspan='3'>Anti D</td>
        <td rowspan='3'>TS-A</td>
        <td rowspan='3'>TS-B</td>
        <td rowspan='3'>TS-O</td>
        <td rowspan='3'>AC</td>
        <td rowspan='3'>BA 6%</td>
        <td rowspan='3'>Petugas</td>
    </tr>
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <td rowspan='2'>Nilai</td>
        <td rowspan='2'>Nolot</td>
        <td rowspan='2'>Epx.</td>

        <td rowspan='2'>Nilai</td>
        <td rowspan='2'>Nolot</td>
        <td rowspan='2'>Epx.</td>

        <td rowspan='2'>Nilai</td>
        <td rowspan='2'>Nolot</td>
        <td rowspan='2'>Epx.</td>
    </tr>
    <tr style="background-color:#FFF8DC; font-size:12px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">

    </tr>
    <?
    $a=mysql_query("select * from dkonfirmasi where  NoKantong='$no_kantonga' order by NoKonfirmasi ASC");
    $no=1;
    while($a_dtransaksipermintaan=mysql_fetch_assoc($a)){
        if(($a_dtransaksipermintaan[Cocok]=='1')){$var_kgd='1';}
        ?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td align="right"><?=$no++?>.</td><?
            $cocok1='-';
            if ($a_dtransaksipermintaan[Cocok]=='0') $cocok1='Cocok';
            if ($a_dtransaksipermintaan[Cocok]=='1') $cocok1='Tidak Cocok';
            if($a_dtransaksipermintaan[sel]=='0') $sel='Ya';
            if($a_dtransaksipermintaan[sel]=='1') $sel='Tidak';
            if($a_dtransaksipermintaan[serum]=='0') $serum='Ya';
            if($a_dtransaksipermintaan[serum]=='1') $serum='Tidak';
            if($a_dtransaksipermintaan[ac]=='0') $ac='Pos';
            if($a_dtransaksipermintaan[ac]=='1') $ac='Neg';
            if($a_dtransaksipermintaan[ba]=='0') $ba='Pos';
            if($a_dtransaksipermintaan[ba]=='1') $ba='Neg';

            $pengolahan=$a_dtransaksipermintaan[tgl];
            $tglkel0=date("Y-m-d",strtotime($pengolahan));
            ?>
            <td><?=$tglkel0?></td>
            <td><?=$a_dtransaksipermintaan[NoKonfirmasi]?></td>
            <td><?=$a_dtransaksipermintaan[NoKantong]?></td>
            <td align="center"><?=$a_dtransaksipermintaan[goldarah_asal]?>(<?=$a_dtransaksipermintaan[rhesus_asal]?>)</td>
            <td align="center"><?=$a_dtransaksipermintaan[GolDarah]?>(<?=$a_dtransaksipermintaan[Rhesus]?>)</td>
            <td align="center" nowrap><?=$cocok1?></td>
            <td class=input><?=$a_dtransaksipermintaan[metode]?></td>
            <td class=input><?=$a_dtransaksipermintaan[antiA]?></td>
            <td class=input><?=$a_dtransaksipermintaan[nolot_aa]?></td>
            <td class=input><?=$a_dtransaksipermintaan[expa]?></td>
            <td class=input><?=$a_dtransaksipermintaan[antiB]?></td>
            <td class=input><?=$a_dtransaksipermintaan[nolot_ab]?></td>
            <td class=input><?=$a_dtransaksipermintaan[expb]?></td>
            <td class=input><?=$a_dtransaksipermintaan[antiD]?></td>
            <td class=input><?=$a_dtransaksipermintaan[nolot_ad]?></td>
            <td class=input><?=$a_dtransaksipermintaan[expd]?></td>
            <td class=input><?=$a_dtransaksipermintaan[tA]?></td>
            <td class=input><?=$a_dtransaksipermintaan[tB]?></td>
            <td class=input><?=$a_dtransaksipermintaan[tsO]?></td>
            <td class=input><?=$ac?></td>
            <td class=input><?=$ba?></td>
            <td class=input><?=$a_dtransaksipermintaan[petugas]?></td>
        </tr>
    <?}?>
    <?
    if ($no=="1"){
        ?><tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="23" class=input align="center">TIDAK ADA DATA PEMERERIKSAAN KONFIRMASI GOLONGAN DARAH</td>
        </tr>
    <?}
    ?>
</table>

<!--TABLE ABS-->
<?
$var_kgd='0';
//DATA ABS================================================================================
?>
<font size="2" color=black><b>Pemeriksaan Antibody Screening</font></b>
<table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr style="background-color:mistyrose; font-size:10px; color:#000000;">
        <td rowspan='2'>No</td>
        <td rowspan='2'>Tanggal</td>
        <td rowspan='2'>No Transaksi</td>
        <td rowspan='2'>Kantong Utama</td>
        <td rowspan='2'>Kode Pendonor</td>
        <td rowspan='2'>Metode</td>
        <td rowspan='2'>Hasil</td>
        <td rowspan='2'>Petugas</td>
    </tr>
    
    <tr style="background-color:#FFF8DC; font-size:12px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">

    </tr>
    <?
    $b=mysql_query("select * from abs where abs_sample_id ='$no_kantonga'");
	//echo 'nomor kantong ======'.$no_kantonga;
    $no=1;
    while($b_dtransaksipermintaan=mysql_fetch_assoc($b)){
        if(($b_dtransaksipermintaan[abs_result]=='Neg')){$var_kgd='1';}
        ?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td align="right"><?=$no++?>.</td><?
            $cocok1='-';
            if ($b_dtransaksipermintaan[abs_result]=='Neg') $cocok2='Negatif';
            if ($b_dtransaksipermintaan[abs_result]=='Pos') $cocok2='Positif';

            $tglabs=$b_dtransaksipermintaan[abs_tgl];
            $tglkel1=date("Y-m-d",strtotime($tglabs));
            ?>
            <td><?=$tglkel1?></td>
            <td><?=$b_dtransaksipermintaan[abs_notrans]?></td>
            <td><?=$b_dtransaksipermintaan[abs_sample_id]?></td>
			<td><?=$b_dtransaksipermintaan[abs_id_donor]?></td>
			<td class=input><?=$b_dtransaksipermintaan[abs_metode]?></td>
            <td align="center" nowrap><?=$cocok2?></td>
            <td class=input><?=$b_dtransaksipermintaan[abs_user]?></td>
        </tr>
    <?}?>
    <?
    if ($no=="1"){
        ?><tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="23" class=input align="center">TIDAK ADA DATA PEMERERIKSAAN ANTIBODY SCREENING</td>
        </tr>
    <?}
    ?>
</table>

<!-- END TABLE-->

<input type="hidden" name="jenis_imltd" value="<?=$var_jenis_imltd?>">
