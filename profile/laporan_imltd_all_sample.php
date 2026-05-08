<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMDONDAR</title>
    <link href="bootsrap337/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootsrap337/js/html5shiv.min.js"></script>
    <script src="bootsrap337/js/respond.min.js"></script>
    <link href="bootsrap337/bspmi.css" rel="stylesheet">
    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />

</head>
<body>
<?php
session_start();
include('config/db_connect.php');
$tahun      = date("Y");
$bulan      = date('m');
$v_tahun    = $_POST['tahun'];
$v_bulan    = $_POST['bulan'];
if (empty($v_tahun)){$v_tahun=$tahun;}
if (empty($v_bulan)){$v_bulan=$bulan;}
$udd="select * from utd where aktif='1'";
$udd=mysql_fetch_assoc(mysql_query($udd));
$id_udd=$udd['id'];
$nama_udd=$udd['nama'];
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
<div class="container">
    <div class="row">
        <div class="col-lg-12">
        <br>
        <div class="panel with-nav-tabs panel-primary" id="shadow1">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-lg-6">
                        <div><h4 style="text-transform: uppercase;">LAPORAN UJI SARING IMLTD</h4></div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel-title pull-right">
                            <form class="form-inline"  method="POST" action="pmitatausaha.php?module=imltd">
                                <div class="form-group">
                                    Bulan
                                    <?php
                                    $b1='';$b2='';$b3='';$b4='';$b5='';$b6='';$b7='';$b8='';$b8='';$b10='';$b11='';$b12='';
                                    switch ($v_bulan){
                                        case '01';$b1='Selected';break;case '02';$b2='Selected';break;case '03';$b3='Selected';break;case '04';$b4='Selected';break;case '05';$b5='Selected';break;
                                        case '06';$b6='Selected';break;case '07';$b7='Selected';break;case '08';$b8='Selected';break;case '09';$b9='Selected';break;case '10';$b10='Selected';break;
                                        case '11';$b11='Selected';break;case '12';$b12='Selected';break;
                                    }
                                    ?>
                                    <select class="form-control" name="bulan">
                                        <option value="1" <?php echo $b1;?> >Januari</option>
                                        <option value="2" <?php echo $b2;?> >Februari</option>
                                        <option value="3" <?php echo $b3;?> >Maret</option>
                                        <option value="4" <?php echo $b4;?> >April</option>
                                        <option value="5" <?php echo $b5;?> >Mei</option>
                                        <option value="6" <?php echo $b6;?> >Juni</option>
                                        <option value="7" <?php echo $b7;?> >Juli</option>
                                        <option value="8" <?php echo $b8;?> >Agustus</option>
                                        <option value="9" <?php echo $b9;?> >September</option>
                                        <option value="10" <?php echo $b10;?> >Oktober</option>
                                        <option value="11" <?php echo $b11;?> >November</option>
                                        <option value="12" <?php echo $b12;?> >Desember</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="tahun">
                                        <?php
                                        $s1='';$s2='';$s3='';$s4='';$s5='';$s6='';
                                        switch ($v_tahun){
                                            case $tahun-5 : $s1='selected';break;
                                            case $tahun-4 : $s2='selected';break;
                                            case $tahun-3 : $s3='selected';break;
                                            case $tahun-2 : $s4='selected';break;
                                            case $tahun-1 : $s5='selected';break;
                                            case $tahun   : $s6='selected';break;
                                        }
                                        ?>
                                        <option value='<?php echo $tahun-5;?>' <?php echo $s1; ?> > <?php echo $tahun-5?> </option>
                                        <option value='<?php echo $tahun-4;?>' <?php echo $s2; ?> > <?php echo $tahun-4?> </option>
                                        <option value='<?php echo $tahun-3;?>' <?php echo $s3; ?> > <?php echo $tahun-3?> </option>
                                        <option value='<?php echo $tahun-2;?>' <?php echo $s4; ?> > <?php echo $tahun-2?> </option>
                                        <option value='<?php echo $tahun-1;?>' <?php echo $s5; ?> > <?php echo $tahun-1?> </option>
                                        <option value='<?php echo $tahun;?>'   <?php echo $s6; ?> > <?php echo $tahun?> </option>
                                    </select>
                                </div>
                                <button class="btn btn-default" type="submit" id="shadow2"><i class="fa fa-check mr-1"></i> OK</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <h4 class="text-center">LAPORAN UJI SARING INFEKSI MENULAR LEWAT TRANSFUSI DARAH</h4>
                        <h4 class="text-center"><?php echo $nama_udd; ?></h4>
                        <h4 class="text-center"><?php echo 'BULAN '.$namaperiode.' '.$v_tahun;?></h4>
                    </div>

                    <div class="col-lg-12">
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
                                    `Hasil`='0' AND month(`tgl_tes`)='$v_bulan' AND year(`tgl_tes`)='$v_tahun'";
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
                        <h5>B.1. UJI SARING INFEKSI MENULAR LEWAT TRANSFUSI DARAH (IMLTD)</h5>
                        <div style="overflow-x:auto;">
                        <table class="table table-bordered table-responsive">
                            <thead class="pmi">
                                <tr>
                                    <th colspan="15" class="text-center">Hasil Pemeriksaan Uji Saring</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-center">Hepatitis B</th>
                                    <th colspan="3" class="text-center">Hepatitis C</th>
                                    <th colspan="3" class="text-center">HIV</th>
                                    <th colspan="3" class="text-center">Sifilis</th>
                                    <th colspan="3" class="text-center">Malaria</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Total Diperiksa</th>
                                    <th class="text-center">Reaktif</th>
                                    <th class="text-center">Reaktif Ulang</th>
                                    <th class="text-center">Total Diperiksa</th>
                                    <th class="text-center">Reaktif</th>
                                    <th class="text-center">Reaktif Ulang</th>
                                    <th class="text-center">Total Diperiksa</th>
                                    <th class="text-center">Reaktif</th>
                                    <th class="text-center">Reaktif Ulang</th>
                                    <th class="text-center">Total Diperiksa</th>
                                    <th class="text-center">Reaktif</th>
                                    <th class="text-center">Reaktif Ulang</th>
                                    <th class="text-center">Total Diperiksa</th>
                                    <th class="text-center">Reaktif</th>
                                    <th class="text-center">Reaktif Ulang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><?php echo $e_tot['hbv']+$r_tot['hbv'];?></td>
                                    <td class="text-center"><?php echo $e_ir['hbv']+$r_ir['hbv'];?></td>
                                    <td class="text-center"><?php echo $e_rr_hbv+$r_rr_hbv;?></td>
                                    <td class="text-center"><?php echo $e_tot['hcv']+$r_tot['hcv'];?></td>
                                    <td class="text-center"><?php echo $e_ir['hcv']+$r_ir['hcv'];?></td>
                                    <td class="text-center"><?php echo $e_rr_hcv+$r_rr_hcv;?></td>
                                    <td class="text-center"><?php echo $e_tot['hiv']+$r_tot['hiv'];?></td>
                                    <td class="text-center"><?php echo $e_ir['hiv']+$r_ir['hiv'];?></td>
                                    <td class="text-center"><?php echo $e_rr_hiv+$r_rr_hiv;?></td>
                                    <td class="text-center"><?php echo $e_tot['syp']+$r_tot['syp'];?></td>
                                    <td class="text-center"><?php echo $e_ir['syp']+$r_ir['syp'];?></td>
                                    <td class="text-center"><?php echo $e_rr_syp+$r_rr_syp;?></td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                </tr>
                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                        </div> <!--Overflow table -->
                    </div>
                    <div class="col-lg-8">
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
                        <h5>B.2. JUMLAH UJI SARING IMLTD BERDASARKAN METODE</h5>
                        <table class="table table-bordered table-responsive">
                            <thead class="pmi">
                            <tr>
                                <th class="text-center" rowspan="2" style="vertical-align: middle;">No</th>
                                <th class="text-center" rowspan="2" style="vertical-align: middle;">Jenis IMLTD</th>
                                <th class="text-center" colspan="4">Jumlah Pemeriksaan</th>
                            </tr>
                            <tr>
                                <th class="text-center">RAPID</th>
                                <th class="text-center">CHLIA</th>
                                <th class="text-center">EIA</th>
                                <th class="text-center">NAT</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr><td class="text-right">1.</td><td>Hepatitis B</td>  <td class="text-center"><?php echo $reag_rapid['rpd_b'];?></td>    <td class="text-center"><?php echo $reag_elisa['chl_b'];?></td>  <td class="text-center"><?php echo $reag_elisa['eia_b'];?></td>  <td class="text-center" style="vertical-align: middle;" rowspan="4"><?php echo $nat['jml_nat'];?></td></tr>
                                <tr><td class="text-right">2.</td><td>Hepatitis C</td>  <td class="text-center"><?php echo $reag_rapid['rpd_c'];?></td>    <td class="text-center"><?php echo $reag_elisa['chl_c'];?></td>  <td class="text-center"><?php echo $reag_elisa['eia_c'];?></td>  </tr>
                                <tr><td class="text-right">3.</td><td>HIV</td>          <td class="text-center"><?php echo $reag_rapid['rpd_i'];?></td>    <td class="text-center"><?php echo $reag_elisa['chl_i'];?></td>  <td class="text-center"><?php echo $reag_elisa['eia_i'];?></td>  </tr>
                                <tr><td class="text-right">4.</td><td>Sifilis</td>      <td class="text-center"><?php echo $reag_rapid['rpd_s'];?></td>    <td class="text-center"><?php echo $reag_elisa['chl_s'];?></td>  <td class="text-center"><?php echo $reag_elisa['eia_s'];?></td>  </tr>
                                <tr><td class="text-right">5.</td><td>Malaria</td>      <td class="text-center">0</td>                                     <td class="text-center">0</td>                                   <td class="text-center">0</td>                                   <td class="text-center">0</td></tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="col-lg-8">
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
                        <h5>B.3. NAMA REAGEN UJI SARING IMLTD</h5>
                        <table class="table table-bordered table-responsive">
                            <thead class="pmi">
                            <tr>
                                <th class="text-center" rowspan="2" style="vertical-align: middle;">No</th>
                                <th class="text-center" rowspan="2" style="vertical-align: middle;">Jenis IMLTD</th>
                                <th class="text-center" colspan="4">Metode Pemeriksaan</th>
                            </tr>
                            <tr>
                                <th class="text-center">RAPID</th>
                                <th class="text-center">CHLIA</th>
                                <th class="text-center">EIA</th>
                                <th class="text-center">NAT</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr><td class="text-right">1.</td><td>Hepatitis B</td>  <td><?php echo $nm_rpd_hbv; ?></td>    <td><?php echo $reag_chl_hbv; ?></td>  <td><?php echo $reag_eia_hbv; ?></td>  <td class="text-center" style="vertical-align: middle;" rowspan="4"><?php echo $reagean_nat;?></td></tr>
                            <tr><td class="text-right">2.</td><td>Hepatitis C</td>  <td><?php echo $nm_rpd_hcv; ?></td>    <td><?php echo $reag_chl_hcv; ?></td>  <td><?php echo $reag_eia_hcv; ?></td>  </tr>
                            <tr><td class="text-right">3.</td><td>HIV</td>          <td><?php echo $nm_rpd_hiv; ?></td>    <td><?php echo $reag_chl_hiv; ?></td>  <td><?php echo $reag_eia_hiv; ?></td>  </tr>
                            <tr><td class="text-right">4.</td><td>Sifilis</td>      <td><?php echo $nm_rpd_syp; ?></td>    <td><?php echo $reag_chl_syp; ?></td>  <td><?php echo $reag_eia_syp; ?></td>  </tr>
                            <tr><td class="text-right">5.</td><td>Malaria</td>      <td></td>    <td></td>  <td></td>  <td></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <a href="pmitatausaha.php?module=upload&mdl=musnah&t=<?php echo $v_tahun;?>&b=<?php echo $v_periode;?>&t1=<?php echo $tanggalawal;?>&t2=<?php echo $tanggalakhir;?>" class="btn btn-default" id="shadow2" title="Upload Laporan ke UDD Pusat. Yang dapat diupload adalah periode Bulanan"><i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;Upload ke Pusat</a>
                <a href="pmitatausaha.php?module=rpt_imltd&thn=<?php echo $v_tahun;?>&bln=<?php echo $v_bulan;?>" class="btn btn-default" id="shadow2"><i class="fa fa-print" aria-hidden="true" title="Cetak Laporan"></i>&nbsp;&nbsp;Cetak Laporan</a>
                <a href="pmitatausaha.php?module=laporan" class="btn btn-default" id="shadow2" title="Kembali ke Menu Laporan"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
            </div>
        </div>
    </div>
    </div>
</div>

</body>
</html>
