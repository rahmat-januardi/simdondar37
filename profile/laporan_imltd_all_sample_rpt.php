<?
    /***********************************************
     * Author 	: suwena 
     * Date 	: 9 Juni 2019
     ***********************************************/
session_start();
require_once('config/db_connect.php');
$tahun      = date("Y");
$bulan      = date('m');
$v_tahun    = $_GET['thn'];
$v_bulan    = $_GET['bln'];
if (empty($v_tahun)){$v_tahun=$tahun;}
if (empty($v_bulan)){$v_bulan=$bulan;}

$utd            = mysql_fetch_assoc(mysql_query("select * from `utd` where `aktif`='1'"));
$dt_um          = "SELECT `u_id`, `u_tahun`, `u_id_udd`, `u_nama`, `u_alamat`, `u_kab`, `u_prov`, `u_kpos`, `u_telp`,
                   `u_email`, `u_ka_nama`, `u_ka_hp`, `u_ka_email`,
                   `u_komite_mdk`, `u_distr_terbuka`, `u_distr_cold_chain`,
                   `u_jml_dokter_kompeten`, `u_ptgs_komptn`, `u_inform_c`, `u_lbr_mon_td`, `u_jml_pasien_td`, `u_jml_pasien_rtd`,
                   `u_periksa_kgd`, `u_kgd_auto`, `u_kgd_semi`, `u_kgd_manual`, `u_periksa_abs`, `u_abs_auto`, `u_abs_semi`,
                   `u_abs_manual`, `u_periksa_iab`, `u_iab_auto`, `u_iab_semi`, `u_iab_manual`, `u_periksa_cross`, `u_cross_auto`,
                   `u_cross_semi`, `u_cross_manual`,day(`u_tgl_laporan`) as tgl_lap, month(`u_tgl_laporan`) as bln_lap, year(`u_tgl_laporan`) as thn_lap
                   FROM `rpt_data_umum` WHERE `u_id_udd`='$utd[id]'";
$dt_um          = mysql_fetch_assoc(mysql_query($dt_um));
switch($dt_um['bln_lap']){
    case '1'  : $bln_lap='Januari';break;
    case '2'  : $bln_lap='Februari';break;
    case '3'  : $bln_lap='Maret';break;
    case '4'  : $bln_lap='April';break;
    case '5'  : $bln_lap='Mei';break;
    case '6'  : $bln_lap='Juni';break;
    case '7'  : $bln_lap='Juli';break;
    case '8'  : $bln_lap='Agustus';break;
    case '9'  : $bln_lap='September';break;
    case '10' : $bln_lap='Oktober';break;
    case '11' : $bln_lap='Nopember';break;
    case '12' : $bln_lap='Desember';break;
}

switch ($v_bulan){
    case '1' : $namaperiode="JANUARI";break;
    case '2' : $namaperiode="FEBRUARI";break;
    case '3' : $namaperiode="MARET";break;
    case '4' : $namaperiode="APRIL";break;
    case '5' : $namaperiode="MEI";break;
    case '6' : $namaperiode="JUNI";break;
    case '7' : $namaperiode="JULI";break;
    case '8' : $namaperiode="AGUSTUS";break;
    case '9' : $namaperiode="SEPTEMBER";break;
    case '10' :$namaperiode="OKTOBER";break;
    case '11' :$namaperiode="NOVEMBER";break;
    case '12' :$namaperiode="DESEMBER";break;
}
?>

<title>Laporan UDD</title>
<head>
<style type="text/css" media="print">
    @page
    {
        size: landscape;
        margin-bottom: 20mm;
        margin-left: 0mm;
        margin-right: 0mm;
        margin-top: 15mm;
        header : {display: none !important;}
    }
    html
    {
        background-color: #ffffff;
        margin: 3px;  /* this affects the margin on the html before sending to printer */
    }
    body
    {
        border: solid 0px #ffffff ;
        margin: 0mm 15mm 10mm 10mm; /* margin you want for the content */
    }
    table th td {text-align: left;}
</style>
</head>
<body onload="window.print()">
<table class="list" border="0" cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr>
        <td style="height: 40px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            LAPORAN UJI SARING INFEKSI MENULAR LEWAT TRANSFUSI DARAH<br>
            <?php echo $dt_um['u_nama'];?><br>
            <?php echo 'BULAN '.$namaperiode.' '.$v_tahun;?>
        </td>
    </tr>
</table>
<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br><br>B.1. UJI SARING INFEKSI MENULAR LEWAT TRANSFUSI DARAH (IMLTD)
</div>
<?php
$e_tot="SELECT
                                    COUNT(DISTINCT(CASE WHEN `jenisPeriksa`='0'  THEN `noKantong` END )) AS hbv,
                                    COUNT(DISTINCT(CASE WHEN `jenisPeriksa`='1'  THEN `noKantong` END )) AS hcv,
                                    COUNT(DISTINCT(CASE WHEN `jenisPeriksa`='2'  THEN `noKantong` END )) AS hiv,
                                    COUNT(DISTINCT(CASE WHEN `jenisPeriksa`='3'  THEN `noKantong` END )) AS syp
                                    from `hasilelisa`
                                    where
                                    month(`tglPeriksa`)='$v_bulan' and year(`tglPeriksa`)='$v_tahun'";
$e_tot=mysql_fetch_assoc(mysql_query($e_tot));
$r_tot="SELECT
                                    COUNT(DISTINCT(CASE WHEN `jenisperiksa`='0'  THEN `noKantong` END )) AS hbv,
                                    COUNT(DISTINCT(CASE WHEN `jenisperiksa`='1'  THEN `noKantong` END )) AS hcv,
                                    COUNT(DISTINCT(CASE WHEN `jenisperiksa`='2'  THEN `noKantong` END )) AS hiv,
                                    COUNT(DISTINCT(CASE WHEN `jenisperiksa`='3'  THEN `noKantong` END )) AS syp
                                    from `drapidtest`
                                    where
                                    month(`tgl_tes`)='$v_bulan' and year(`tgl_tes`)='$v_tahun'";
$r_tot=mysql_fetch_assoc(mysql_query($r_tot));

$e_ir="SELECT
                                    COUNT(DISTINCT(CASE WHEN `jenisPeriksa`='0'  THEN `noKantong` END )) AS hbv,
                                    COUNT(DISTINCT(CASE WHEN `jenisPeriksa`='1'  THEN `noKantong` END )) AS hcv,
                                    COUNT(DISTINCT(CASE WHEN `jenisPeriksa`='2'  THEN `noKantong` END )) AS hiv,
                                    COUNT(DISTINCT(CASE WHEN `jenisPeriksa`='3'  THEN `noKantong` END )) AS syp
                                    from `hasilelisa`
                                    where
                                    `Hasil`='1' AND month(`tglPeriksa`)='$v_bulan' AND year(`tglPeriksa`)='$v_tahun'";
$e_ir=mysql_fetch_assoc(mysql_query($e_ir));
$r_ir="SELECT
                                    COUNT(DISTINCT(CASE WHEN `jenisperiksa`='0'  THEN `noKantong` END )) AS hbv,
                                    COUNT(DISTINCT(CASE WHEN `jenisperiksa`='1'  THEN `noKantong` END )) AS hcv,
                                    COUNT(DISTINCT(CASE WHEN `jenisperiksa`='2'  THEN `noKantong` END )) AS hiv,
                                    COUNT(DISTINCT(CASE WHEN `jenisperiksa`='3'  THEN `noKantong` END )) AS syp
                                    from `drapidtest`
                                    where
                                    `Hasil`='0' AND month(`tglPeriksa`)='$v_bulan' AND year(`tglPeriksa`)='$v_tahun'";
$r_ir=mysql_fetch_assoc(mysql_query($r_ir));
$e_rr_b ="SELECT `noKantong`, COUNT(*) as Pengulangan FROM `hasilelisa`
                                WHERE `jenisPeriksa`='0' AND  `Hasil`='1' AND month(`tglPeriksa`)='$v_bulan' AND year(`tglPeriksa`)='$v_tahun'
                                GROUP BY `noKantong`
                                HAVING  COUNT(`noKantong`) > 1";
$e_rr_hbv = mysql_num_rows(mysql_query($e_rr_b));

$e_rr_c ="SELECT `noKantong`, COUNT(*) as Pengulangan FROM `hasilelisa`
                                WHERE `jenisPeriksa`='1' AND  `Hasil`='1' AND month(`tglPeriksa`)='$v_bulan' AND year(`tglPeriksa`)='$v_tahun'
                                GROUP BY `noKantong`
                                HAVING  COUNT(`noKantong`) > 1";
$e_rr_hcv = mysql_num_rows(mysql_query($e_rr_c));

$e_rr_i ="SELECT `noKantong`, COUNT(*) as Pengulangan FROM `hasilelisa`
                                WHERE `jenisPeriksa`='2' AND  `Hasil`='1' AND month(`tglPeriksa`)='$v_bulan' AND year(`tglPeriksa`)='$v_tahun'
                                GROUP BY `noKantong`
                                HAVING  COUNT(`noKantong`) > 1";
$e_rr_hiv = mysql_num_rows(mysql_query($e_rr_i));

$e_rr_s ="SELECT `noKantong`, COUNT(*) as Pengulangan FROM `hasilelisa`
                                WHERE `jenisPeriksa`='3' AND  `Hasil`='1' AND month(`tglPeriksa`)='$v_bulan' AND year(`tglPeriksa`)='$v_tahun'
                                GROUP BY `noKantong`
                                HAVING  COUNT(`noKantong`) > 1";
$e_rr_syp = mysql_num_rows(mysql_query($e_rr_s));

$r_rr_b ="SELECT `noKantong`, COUNT(*) as Pengulangan FROM `hasilelisa`
                                WHERE `jenisperiksa`='0' AND  `Hasil`='0' AND month(`tgl_tes`)='$v_bulan' AND year(`tgl_tes`)='$v_tahun'
                                GROUP BY `noKantong`
                                HAVING  COUNT(`noKantong`) > 1";
$r_rr_hbv = mysql_num_rows(mysql_query($r_rr_b));

$r_rr_c ="SELECT `noKantong`, COUNT(*) as Pengulangan FROM `hasilelisa`
                                WHERE `jenisperiksa`='1' AND  `Hasil`='0' AND month(`tgl_tes`)='$v_bulan' AND year(`tgl_tes`)='$v_tahun'
                                GROUP BY `noKantong`
                                HAVING  COUNT(`noKantong`) > 1";
$r_rr_hcv = mysql_num_rows(mysql_query($r_rr_c));

$r_rr_i ="SELECT `noKantong`, COUNT(*) as Pengulangan FROM `hasilelisa`
                                WHERE `jenisperiksa`='2' AND  `Hasil`='0' AND month(`tgl_tes`)='$v_bulan' AND year(`tgl_tes`)='$v_tahun'
                                GROUP BY `noKantong`
                                HAVING  COUNT(`noKantong`) > 1";
$r_rr_hiv = mysql_num_rows(mysql_query($r_rr_i));

$r_rr_s ="SELECT `noKantong`, COUNT(*) as Pengulangan FROM `hasilelisa`
                                WHERE `jenisperiksa`='2' AND  `Hasil`='0' AND month(`tgl_tes`)='$v_bulan' AND year(`tgl_tes`)='$v_tahun'
                                GROUP BY `noKantong`
                                HAVING  COUNT(`noKantong`) > 1";
$r_rr_syp = mysql_num_rows(mysql_query($r_rr_s));
?>
<table class="list" border="1" cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th colspan="15" align="center">Hasil Pemeriksaan Uji Saring</th>
    </tr>
    <tr>
        <th colspan="3" align="center">Hepatitis B</th>
        <th colspan="3" align="center">Hepatitis C</th>
        <th colspan="3" align="center">HIV</th>
        <th colspan="3" align="center">Sifilis</th>
        <th colspan="3" align="center">Malaria</th>
    </tr>
    <tr>
        <th align="center">Total Diperiksa</th>
        <th align="center">Reaktif</th>
        <th align="center">Reaktif Ulang</th>
        <th align="center">Total Diperiksa</th>
        <th align="center">Reaktif</th>
        <th align="center">Reaktif Ulang</th>
        <th align="center">Total Diperiksa</th>
        <th align="center">Reaktif</th>
        <th align="center">Reaktif Ulang</th>
        <th align="center">Total Diperiksa</th>
        <th align="center">Reaktif</th>
        <th align="center">Reaktif Ulang</th>
        <th align="center">Total Diperiksa</th>
        <th align="center">Reaktif</th>
        <th align="center">Reaktif Ulang</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="center"><?php echo $e_tot['hbv']+$r_tot['hbv'];?></td>
        <td align="center"><?php echo $e_ir['hbv']+$r_ir['hbv'];?></td>
        <td align="center"><?php echo $e_rr_hbv+$r_rr_hbv;?></td>
        <td align="center"><?php echo $e_tot['hcv']+$r_tot['hcv'];?></td>
        <td align="center"><?php echo $e_ir['hcv']+$r_ir['hcv'];?></td>
        <td align="center"><?php echo $e_rr_hcv+$r_rr_hcv;?></td>
        <td align="center"><?php echo $e_tot['hiv']+$r_tot['hiv'];?></td>
        <td align="center"><?php echo $e_ir['hiv']+$r_ir['hiv'];?></td>
        <td align="center"><?php echo $e_rr_hiv+$r_rr_hiv;?></td>
        <td align="center"><?php echo $e_tot['syp']+$r_tot['syp'];?></td>
        <td align="center"><?php echo $e_ir['syp']+$r_ir['syp'];?></td>
        <td align="center"><?php echo $e_rr_syp+$r_rr_syp;?></td>
        <td align="center">0</td>
        <td align="center">0</td>
        <td align="center">0</td>
    </tr>
    </tbody>
    <tfoot>

    </tfoot>
</table>

<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br><br>B.2. JUMLAH UJI SARING IMLTD BERDASARKAN METODE
</div>
<?php
$reag_elisa="select
                                    COUNT(DISTINCT(CASE WHEN `Metode`='clia' and `jenisPeriksa`='0' THEN `noKantong` end)) as chl_b,
                                    COUNT(DISTINCT(CASE WHEN `Metode`='clia' and `jenisPeriksa`='1' THEN `noKantong` end)) as chl_c,
                                    COUNT(DISTINCT(CASE WHEN `Metode`='clia' and `jenisPeriksa`='2' THEN `noKantong` end)) as chl_i,
                                    COUNT(DISTINCT(CASE WHEN `Metode`='clia' and `jenisPeriksa`='3' THEN `noKantong` end)) as chl_s,

                                    COUNT(DISTINCT(CASE WHEN `Metode`='elisa' and `jenisPeriksa`='0' THEN `noKantong` end)) as eia_b,
                                    COUNT(DISTINCT(CASE WHEN `Metode`='elisa' and `jenisPeriksa`='1' THEN `noKantong` end)) as eia_c,
                                    COUNT(DISTINCT(CASE WHEN `Metode`='elisa' and `jenisPeriksa`='2' THEN `noKantong` end)) as eia_i,
                                    COUNT(DISTINCT(CASE WHEN `Metode`='elisa' and `jenisPeriksa`='3' THEN `noKantong` end)) as eia_s
                                    from hasilelisa
                                    where
                                    month(`tglPeriksa`)='$v_bulan' and year(`tglPeriksa`)='$v_tahun'";
$reag_elisa=mysql_fetch_assoc(mysql_query($reag_elisa));
$reag_rapid="SELECT
                                    count(DISTINCT(case when `jenisperiksa`='0' then `noKantong` end)) as rpd_b,
                                    count(DISTINCT(case when `jenisperiksa`='1' then `noKantong` end)) as rpd_c,
                                    count(DISTINCT(case when `jenisperiksa`='2' then `noKantong` end)) as rpd_i,
                                    count(DISTINCT(case when `jenisperiksa`='3' then `noKantong` end)) as rpd_s
                                    FROM `drapidtest`
                                    where
                                    month(`tgl_tes`)='$v_bulan' and year(`tgl_tes`)='$v_tahun'";
$reag_rapid=mysql_fetch_assoc(mysql_query($reag_rapid));
$nat="SELECT count(DISTINCT(`sample_id`)) as jml_nat FROM `imltd_procleix_raw`
                              where month(`date`)='$v_bulan' and  year(`date`)='$v_tahun'";
$nat=mysql_fetch_assoc(mysql_query($nat));
?>
<table class="list" border="1" cellpadding="2" cellspacing="2" width="60%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center" rowspan="2" style="vertical-align: middle;">No</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">Jenis IMLTD</th>
        <th align="center" colspan="4">Jumlah Pemeriksaan</th>
    </tr>
    <tr>
        <th align="center">RAPID</th>
        <th align="center">CHLIA</th>
        <th align="center">EIA</th>
        <th align="center">NAT</th>
    </tr>
    <tbody>
    <tr><td align="right">1.</td><td>Hepatitis B</td>  <td align="center"><?php echo $reag_rapid['rpd_b'];?></td>    <td align="center"><?php echo $reag_elisa['chl_b'];?></td>  <td align="center"><?php echo $reag_elisa['eia_b'];?></td>  <td align="center" style="vertical-align: middle;" rowspan="4"><?php echo $nat['jml_nat'];?></td></tr>
    <tr><td align="right">2.</td><td>Hepatitis C</td>  <td align="center"><?php echo $reag_rapid['rpd_c'];?></td>    <td align="center"><?php echo $reag_elisa['chl_c'];?></td>  <td align="center"><?php echo $reag_elisa['eia_c'];?></td>  </tr>
    <tr><td align="right">3.</td><td>HIV</td>          <td align="center"><?php echo $reag_rapid['rpd_i'];?></td>    <td align="center"><?php echo $reag_elisa['chl_i'];?></td>  <td align="center"><?php echo $reag_elisa['eia_i'];?></td>  </tr>
    <tr><td align="right">4.</td><td>Sifilis</td>      <td align="center"><?php echo $reag_rapid['rpd_s'];?></td>    <td align="center"><?php echo $reag_elisa['chl_s'];?></td>  <td align="center"><?php echo $reag_elisa['eia_s'];?></td>  </tr>
    <tr><td align="right">5.</td><td>Malaria</td>      <td align="center">0</td>                                     <td align="center">0</td>                                   <td align="center">0</td>                                   <td align="center">0</td></tr>
    </tbody>
</table>


<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br><br>B.3. NAMA REAGEN UJI SARING IMLTD
</div>
<?php
$nm_chl="select e.`Metode`, e.`jenisPeriksa`, r.`Nama`,
                                 count(e.`noKantong`) as jml
                                 from hasilelisa e inner join `reagen` r on r.`noLot`=e.`noLot`
                                 where
                                 year(e.`tglPeriksa`)='$v_tahun' and
                                 month(e.`tglPeriksa`)='$v_bulan'
                                 group by e.`Metode`,e.`jenisPeriksa`,r.`Nama`";
$reag_chl_hbv='';$reag_chl_hcv='';$reag_chl_hiv='';$reag_chl_syp='';
$reag_eia_hbv='';$reag_eia_hcv='';$reag_eia_hiv='';$reag_eia_syp='';
$result=mysql_query($nm_chl);
while ($row=mysql_fetch_assoc($result)){
    switch ($row['Metode']){
        case 'clia' :
            switch($row['jenisPeriksa']){
                case '0': if (strlen($reag_chl_hbv)==0){$reag_chl_hbv = $row['Nama'];}else{$reag_chl_hbv = $reag_chl_hbv.'; '.$row['Nama'];}break;
                case '1': if (strlen($reag_chl_hcv)==0){$reag_chl_hcv = $row['Nama'];}else{$reag_chl_hcv = $reag_chl_hcv.'; '.$row['Nama'];}break;
                case '2': if (strlen($reag_chl_hiv)==0){$reag_chl_hiv = $row['Nama'];}else{$reag_chl_hiv = $reag_chl_hiv.'; '.$row['Nama'];}break;
                case '3': if (strlen($reag_chl_syp)==0){$reag_chl_syp = $row['Nama'];}else{$reag_chl_syp = $reag_chl_syp.'; '.$row['Nama'];}break;
            }
            break;
        case 'elisa' :
            switch($row['jenisPeriksa']){
                case '0': if (strlen($reag_eia_hbv)==0){$reag_eia_hbv = $row['Nama'];}else{$reag_eia_hbv = $reag_eia_hbv.'; '.$row['Nama'];}break;
                case '1': if (strlen($reag_eia_hcv)==0){$reag_eia_hcv = $row['Nama'];}else{$reag_eia_hcv = $reag_eia_hcv.'; '.$row['Nama'];}break;
                case '2': if (strlen($reag_eia_hiv)==0){$reag_eia_hiv = $row['Nama'];}else{$reag_eia_hiv = $reag_eia_hiv.'; '.$row['Nama'];}break;
                case '3': if (strlen($reag_eia_syp)==0){$reag_eia_syp = $row['Nama'];}else{$reag_eia_syp = $reag_eia_syp.'; '.$row['Nama'];}break;
            }
            break;
    }

}
$nm_nat="SELECT  `protocol`
                                    FROM `imltd_procleix_raw`
                                    WHERE year(`date`)='$v_tahun' and month(`date`)='$v_bulan'
                                    group by `protocol`";
$nm_nat=mysql_query($nm_nat);
$reagean_nat='';
while ($row=mysql_fetch_assoc($nm_nat)){
    if (strlen($reagean_nat)==0){$reagean_nat=$row['protocol'];}else{$reagean_nat=$reagean_nat.'; '.$row['protocol'];}
}
$nm_rapid="select e.`jenisperiksa`, r.`Nama`,
                                    count(e.`noKantong`) as jml
                                    from `drapidtest` e inner join `reagen` r on r.`noLot`=e.`nolot`
                                    where
                                    year(e.`tgl_tes`)='$v_tahun' AND MONTH(e.`tgl_tes`)='$v_bulan'
                                    group by e.`jenisperiksa`,r.`Nama`";
$nm_rpd_hbv='';$nm_rpd_hcv='';$nm_rpd_hiv='';$nm_rpd_syp='';
$nm_rapid=mysql_query($nm_rapid);
while ($row=mysql_fetch_assoc($nm_rapid)){
    switch ($row['jenisperiksa']){
        case '0'; if (strlen($nm_rpd_hbv)==0){$nm_rpd_hbv = $row['Nama'];}else{$nm_rpd_hbv = $nm_rpd_hbv.'; '.$row['Nama'];}break;
        case '1'; if (strlen($nm_rpd_hcv)==0){$nm_rpd_hcv = $row['Nama'];}else{$nm_rpd_hcv = $nm_rpd_hcv.'; '.$row['Nama'];}break;
        case '2'; if (strlen($nm_rpd_hiv)==0){$nm_rpd_hiv = $row['Nama'];}else{$nm_rpd_hiv = $nm_rpd_hiv.'; '.$row['Nama'];}break;
        case '3'; if (strlen($nm_rpd_syp)==0){$nm_rpd_syp = $row['Nama'];}else{$nm_rpd_syp = $nm_rpd_syp.'; '.$row['Nama'];}break;
    }
}
?>
<table class="list" border="1" cellpadding="2" cellspacing="2" width="60%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center" rowspan="2" style="vertical-align: middle;">No</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">Jenis IMLTD</th>
        <th align="center" colspan="4">Metode Pemeriksaan</th>
    </tr>
    <tr>
        <th align="center">RAPID</th>
        <th align="center">CHLIA</th>
        <th align="center">EIA</th>
        <th align="center">NAT</th>
    </tr>
    </thead>
    <tbody>
    <tr><td align="right">1.</td><td>Hepatitis B</td>  <td><?php echo $nm_rpd_hbv; ?></td>    <td><?php echo $reag_chl_hbv; ?></td>  <td><?php echo $reag_eia_hbv; ?></td>  <td align="center" style="vertical-align: middle;" rowspan="4"><?php echo $reagean_nat;?></td></tr>
    <tr><td align="right">2.</td><td>Hepatitis C</td>  <td><?php echo $nm_rpd_hcv; ?></td>    <td><?php echo $reag_chl_hcv; ?></td>  <td><?php echo $reag_eia_hcv; ?></td>  </tr>
    <tr><td align="right">3.</td><td>HIV</td>          <td><?php echo $nm_rpd_hiv; ?></td>    <td><?php echo $reag_chl_hiv; ?></td>  <td><?php echo $reag_eia_hiv; ?></td>  </tr>
    <tr><td align="right">4.</td><td>Sifilis</td>      <td><?php echo $nm_rpd_syp; ?></td>    <td><?php echo $reag_chl_syp; ?></td>  <td><?php echo $reag_eia_syp; ?></td>  </tr>
    <tr><td align="right">5.</td><td>Malaria</td>      <td></td>    <td></td>  <td></td>  <td></td></tr>
    </tbody>
</table>
<br><br>

<table border="0" width="100%">
    <tr style="background-color: #ffffff;font-wight:none; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td width="25%"></td>
        <td width="25%"></td>
        <td width="25%" nowrap>
            <?php echo $dt_um['u_kab'].', '.$dt_um['tgl_lap'].' '.$bln_lap.' '.$dt_um['thn_lap'];?>
            <br>KEPALA <?php echo $dt_um['u_nama'].',';?><br><br><br><br>
        </td>
    </tr>
    <tr style="background-color: #ffffff;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td width="25%"></td>
        <td width="25%"></td>
        <td width="25%" nowrap>
            <?php echo $dt_um['u_ka_nama'];?>
        </td>
    </tr>
</table>
<? echo "<meta http-equiv='refresh' content='2;url=pmitatausaha.php?module=imltd&tahun=$v_tahun&bulan=$v_bulan'";?>
</body>
