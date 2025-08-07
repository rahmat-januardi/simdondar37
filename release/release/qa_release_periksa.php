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
            <td colspan="13" class=input align="center">TIDAK ADA DATA PEMERIKSAAN IMLTD METODE ELISA</td>
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
$sq_nat="SELECT `id`, `date_transfer`, `sample_id`, `interpretation`, `protocol`, `run_number`, `date`, `flag`, `internal_control_rlu`,
        `internal_Control_result`, `analyte_rlu`, `analyte_s_co`, `kinetic_index`, `operator_name`, `internal_control_cutoff`,
        `analyte_cutoff`, `neg_calibrator_analyte_avg`, `neg_calibrator_ic_avg`, `hiv_pos_analyte_avg`, `hiv_pos_calibrator_ic_avg`,
        `hcv_pos_analyte_avg`, `hcv_pos_calibartor_ic_avg`, `lot_number`, `lot_date`, `procleix_sn`, `procleix_firmware`,
        `run_number_prefix`, `type_of_tube`, `hbv_pos_calibrator_avg`, `hbv_pos_calibrator_ic_avg`, `userinput`, `konfirmasi`,
        `tgl_konfirmasi`, `userkonfirmasi` FROM `imltd_procleix_raw` WHERE `sample_id`='$no_kantonga'";
$sql_nat=mysql_query($sq_nat);
//echo "$sq_nat";
?>
<font size="2" color=black><b>Pemeriksaan metode NAT</font></b>
<table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th rowspan="2">ID</th>
        <th rowspan="2">Protokol</th>
        <th rowspan="2">Waktu Periksa</th>
        <th rowspan="2">Operator</th>
        <th colspan="2">Master Lot</th>
        <th rowspan="2">Overall<br>Interpretarion</th>
        <th rowspan="2">Status<br>Flag</th>
        <th colspan="2">Internal Control</th>
        <th colspan="2">Analyte</th>
        <th colspan="2">Kofirmasi Hasil</th>
    </tr>
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th>Number</th>
        <th>Date</th>
        <th>RLU</th>
        <th>Result</th>
        <th>RLU</th>
        <th>S/CO</th>
        <th>Tanggal</th>
        <th>User</th>
    </tr>
    <?
    $no="0";
    while ($nat=mysql_fetch_assoc($sql_nat)){$no++; ?>
        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td class=input align="right"><?=$nat['id']?></td>
            <td class=input><?=$nat['protocol']." ".$nat['procleix_sn']?></td>
            <td class=input><?=$nat['date']?></td>
            <td class=input align="center"><?=$nat['operator_name']?></td>
            <td class=input><?=$nat['lot_number']?></td>
            <td class=input><?=$nat['lot_date']?></td>
            <td class=input align="center"><?=$nat['interpretation']?></td>
            <td class=input align="center"><?=$nat['flag']?></td>
            <td class=input><?=$nat['internal_control_rlu']?></td>
            <td class=input><?=$nat['internal_Control_result']?></td>
            <td class=input><?=$nat['analyte_rlu']?></td>
            <td class=input><?=$nat['analyte_s_co']?></td>
            <td class=input><?=$nat['tgl_konfirmasi']?></td>
            <td class=input align="center"><?=$nat['userkonfirmasi']?></td>
        </tr>
    <?}?>
    <?
    if ($no=="0"){
        ?><tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="14" class=input align="center">TIDAK ADA DATA PEMERIKSAAN NAT</td>
        </tr>
    <?}
    if ($no!=='0'){$var_jenis_imltd='1';}
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
<input type="hidden" name="jenis_imltd" value="<?=$var_jenis_imltd?>">