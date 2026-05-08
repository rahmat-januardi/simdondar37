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
<?
session_start();
include('config/db_connect.php');
$hariini=time();
$today=date('Y-m-d',$hariini);
$tahun=date("Y", $hariini);
$bulan =date("m", $hariini);
$bulan1=$_POST['bulan'];
$tahun1=$_POST['tahun'];
$tahun       = date("Y");
$bl          = date("m");

$v_periode   = $b1;
$v_tahun     = $tahun;
$v_tahun     = $_POST['tahun'];
$v_periode   = $_POST['bulan'];
if (empty($v_tahun)){$v_tahun=$tahun;}
if (empty($v_periode)){$v_periode=$bl;}

$udd="select * from utd where aktif='1'";
$udd=mysql_fetch_assoc(mysql_query($udd));
$id_udd=$udd['id'];
$nama_udd=$udd['nama'];
switch ($v_periode){
    case '1' : $namaperiode="Bulan Januari";break;
    case '2' : $namaperiode="Bulan Februari";break;
    case '3' : $namaperiode="Bulan Maret";break;
    case '4' : $namaperiode="Bulan April";break;
    case '5' : $namaperiode="Bulan Mei";break;
    case '6' : $namaperiode="Bulan Juni";break;
    case '7' : $namaperiode="Bulan Juli";break;
    case '8' : $namaperiode="Bulan Agustus";break;
    case '9' : $namaperiode="Bulan September";break;
    case '10' :$namaperiode="Bulan Oktober";break;
    case '11' :$namaperiode="Bulan November";break;
    case '12' :$namaperiode="Bulan Desember";break;
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
                            <h4 style="text-transform: uppercase;">LAPORAN KOMPONEN DARAH</h4>
                        </div>
                        <div class="col-lg-6">
                            <div class="panel-title pull-right">
                                <form class="form-inline" role="form" method="POST" action="pmitatausaha.php?module=komponen">
                                    <div class="form-group">
                                        Bulan
                                        <?php
                                        $b1='';$b2='';$b3='';$b4='';$b5='';$b6='';$b7='';$b8='';$b8='';$b10='';$b11='';$b12='';
                                        $b31='';$b32='';$b33='';$b34='';$b61='';$b61='';$b121='';
                                        switch ($v_periode){
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
                                <div class="text-center"><h4>LAPORAN KOMPONEN DARAH</h4></div>
                                <div class="text-center"><h4><?php echo $nama_udd;?></h4></div>
                                <div class="text-center"><h4 style="text-transform: uppercase;"> <?php echo $namaperiode.' '.$v_tahun;?></h4></div>
                                <div class="text-left"><h4>D.KOMPONEN DARAH</h4></div>
                            </div>
                            <div class="col-lg-12">
                            <table class="table table-bordered table-responsive">
                                <thead class="pmi">
                                    <tr>
                                        <th class="text-center" colspan="2" rowspan="2">NAMA KOMPONEN DARAH</th>
                                        <th class="text-center" colspan="3">JUMLAH</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">PRODUKSI</th>
                                        <th class="text-center">PERMINTAAN</th>
                                        <th class="text-center">PEMAKAIAN</th>
                                    </tr>
                                </thead>
                                <?php
				$q_wb="select count(distinct(case when `produk`='WB' THEN `noKantong` END )) AS WB1 from stokkantong where month(`tgl_Aftap`)='$bulan1' and year(`tgl_Aftap`)='$tahun1'";
				$q_komponen1=mysql_query($q_wb);
				$komp1=mysql_fetch_assoc($q_komponen1);

                                $q_komponen = "SELECT
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='WB' THEN `noKantong` END )) AS WB,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='PRC' THEN `noKantong` END )) AS PRC,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='LP' THEN `noKantong` END )) AS LP,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='FFP' THEN `noKantong` END )) AS FFP,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='TC' THEN `noKantong` END )) AS TC,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='AHF' THEN `noKantong` END )) AS AHF,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='WE' THEN `noKantong` END )) AS WE,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Leucodepleted' THEN `noKantong` END )) AS LEUCO,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='TC Aferesis' THEN `noKantong` END )) AS TC_APH,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Buffycoat Removal' THEN `noKantong` END )) AS BUF_R,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Bedside Filter Leukosit' THEN `noKantong` END )) AS BF_LEUCO,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Lab Type Filter Leukosit' THEN `noKantong` END )) AS LTF_LEUCO,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='PRC Aferesis' THEN `noKantong` END )) AS PRC_APH,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Plasma Aferesis' THEN `noKantong` END )) AS LP_APH
                                                FROM `dpengolahan`
                                                WHERE
                                                month(`tgl`)='$bulan1' and year(`tgl`)='$tahun1'";
                                $q_komponen=mysql_query($q_komponen);
				$komp=mysql_fetch_assoc($q_komponen);
                                $tot_produksi= $komp1['WB1']+$komp['PRC']+$komp['LP']+$komp['FFP']+$komp['TC']+$komp['AHF']+$komp['WE']+$komp['LEUCO']+ $komp['TC_APH']+$komp['BUF_R']+$komp['BF_LEUCO']+$komp['LTF_LEUCO']+$komp['PRC_APH']+$komp['LP_APH'];
                                //mencari data apheresis pada table htransaksi karena apheresis tidak melaewati table pengolahan
                                $q_aph="SELECT
                                        count(case when `caraAmbil`='1' then `NoKantong` END) as TC_Aph,
                                        count(case when `caraAmbil`='3' then `NoKantong` END) as LP_Aph,
                                        count(case when `caraAmbil`='4' then `NoKantong` END) as PRC_Aph
                                        FROM `htransaksi`
                                        WHERE year(`Tgl`)='$tahun1' and month(`Tgl`)='$bulan1' and  `Pengambilan`='0' and `caraAmbil` in ('1','2','3','4')";
                                $q_aph=mysql_fetch_assoc(mysql_query($q_aph));
                                $aph_tc=$q_aph['TC_Aph'];
                                $aph_lpc=$q_aph['LP_Aph'];
                                $aph_prc=$q_aph['PRC_Aph'];
                                $tot_produksi=$tot_produksi+$aph_tc=$q_aph['TC_Aph']+$aph_tc=$q_aph['LP_Aph']+$aph_tc=$q_aph['PRC_Aph'];
                                //=========================BDRS===================================================
                                $sqlb="SELECT
                                        k.`bdrs` as bdrskode,
                                        SUM(CASE WHEN s.`produk`='PRC' THEN 1 ELSE 0 END) as prcb,
                                        SUM(CASE WHEN s.`produk`='TC' THEN 1 ELSE 0 END) as tcb,
                                        SUM(CASE WHEN s.`produk`='TC Aferesis' THEN 1 ELSE 0 END) as aphb,
                                        SUM(CASE WHEN s.`produk`='AHF' THEN 1 ELSE 0 END) as ahfb,
                                        SUM(CASE WHEN s.`produk`='FFP' THEN 1 ELSE 0 END) as ffpb,
                                        SUM(CASE WHEN s.`produk`='WB' THEN 1 ELSE 0 END) as wbb,
                                        SUM(CASE WHEN s.`produk`='WE' THEN 1 ELSE 0 END) as web,
                                        SUM(CASE WHEN s.`produk`='LP' THEN 1 ELSE 0 END) as lpb,
                                        SUM(CASE WHEN s.`produk`='FP' THEN 1 ELSE 0 END) as fpb,
                                        SUM(CASE WHEN s.`produk`='Leucodepleted' THEN 1 ELSE 0 END) as ldb,
                                        SUM(CASE WHEN s.`produk`='Buffycoat Removal' THEN 1 ELSE 0 END) as brb,
                                        SUM(CASE WHEN s.`produk`='Bedside Filter Leukosit' THEN 1 ELSE 0 END) as bflb,
                                        SUM(CASE WHEN s.`produk`='Lab Type Filter Leukosit' THEN 1 ELSE 0 END) as ltflb,
                                        SUM(CASE WHEN s.`produk`='PRC Aferesis' THEN 1 ELSE 0 END) as prcaphb,
                                        SUM(CASE WHEN s.`produk`='Plasma Aferesis' THEN 1 ELSE 0 END) as lpaphb
                                        FROM `kirimbdrs` k
                                        inner join `bdrs` b on b.`kode`=k.`bdrs`
                                        inner join `stokkantong` s on s.`noKantong`=k.`nokantong`
                                        WHERE month(k.`tgl`)='$bulan1' AND year(k.`tgl`)='$tahun1' AND k.`status`='0'";

                                                            //echo "$sql";
                                                            $drop=mysql_query($sqlb);
                                                            $jml_prcb=0;
                                                            $jml_tcb=0;
                                                            $jml_tcaphb=0;
                                                            $jml_ahfb=0;
                                                            $jml_ffpb=0;
                                                            $jml_wbb=0;
                                                            $jml_web=0;
                                                            $jml_lpb=0;
                                                            $jml_fpb=0;
                                                            $jml_ldb=0;
                                                            $jml_brb=0;
                                                            $jml_bflb=0;
                                                            $jml_ltflb=0;
                                                            $jml_prcaphb=0;
                                                            $jml_lpaphb=0;
                                                            $drop1=mysql_fetch_assoc($drop);
                                                            $jml_prcb=$jml_prcb + $drop1['prcb'];
                                                            $jml_tcb=$jml_tcb + $drop1['tcb'];
                                                            $jml_tcaphb=$jml_tcaphb + $drop1['aphb'];
                                                            $jml_ahfb=$jml_ahfb + $drop1['ahfb'];
                                                            $jml_ffpb=$jml_ffpb + $drop1['ffpb'];
                                                            $jml_wbb=$jml_wbb + $drop1['wbb'];
                                                            $jml_web=$jml_web + $drop1['web'];
                                                            $jml_lpb=$jml_lpb + $drop1['lpb'];
                                                            $jml_fpb=$jml_fpb + $drop1['fpb'];
                                                            $jml_ldb=$jml_ldb + $drop1['ldb'];
                                                            $jml_brb=$jml_brb + $drop1['brb'];
                                                            $jml_bflb=$jml_bflb + $drop1['bflb'];
                                                            $jml_ltflb=$jml_ltflb + $drop1['ltflb'];
                                                            $jml_prcaphb=$jml_prcaphb + $drop1['prcaphb'];
                                                            $jml_lpaphb=$jml_lpaphb + $drop1['lpaphb'];
                                                            $jml_drop=$jml_prcb + $jml_tcb + $jml_tcaphb + $jml_ahfb + $jml_ffpb + $jml_wbb + $jml_web + $jml_lpb + $jml_fpb + $jml_ldb+$jml_brb+$jml_bflb+$jml_ltflb+$jml_prcaphb+$jml_lpaphb;
                                //==================================DROPING UDD===========================================
                                $sqlu="SELECT
                                        k.`udd` as uddkode,
                                        SUM(CASE WHEN s.`produk`='PRC' THEN 1 ELSE 0 END) as prcu,
                                        SUM(CASE WHEN s.`produk`='TC' THEN 1 ELSE 0 END) as tcu,
                                        SUM(CASE WHEN s.`produk`='TC Aferesis' THEN 1 ELSE 0 END) as aphu,
                                        SUM(CASE WHEN s.`produk`='AHF' THEN 1 ELSE 0 END) as ahfu,
                                        SUM(CASE WHEN s.`produk`='FFP' THEN 1 ELSE 0 END) as ffpu,
                                        SUM(CASE WHEN s.`produk`='WB' THEN 1 ELSE 0 END) as wbu,
                                        SUM(CASE WHEN s.`produk`='WE' THEN 1 ELSE 0 END) as weu,
                                        SUM(CASE WHEN s.`produk`='LP' THEN 1 ELSE 0 END) as lpu,
                                        SUM(CASE WHEN s.`produk`='FP' THEN 1 ELSE 0 END) as fpu,
                                        SUM(CASE WHEN s.`produk`='Leucodepleted' THEN 1 ELSE 0 END) as ldu,
                                        SUM(CASE WHEN s.`produk`='Buffycoat Removal' THEN 1 ELSE 0 END) as brb,
                                        SUM(CASE WHEN s.`produk`='Bedside Filter Leukosit' THEN 1 ELSE 0 END) as bflu,
                                        SUM(CASE WHEN s.`produk`='Lab Type Filter Leukosit' THEN 1 ELSE 0 END) as ltflu,
                                        SUM(CASE WHEN s.`produk`='PRC Aferesis' THEN 1 ELSE 0 END) as prcaphu,
                                        SUM(CASE WHEN s.`produk`='Plasma Aferesis' THEN 1 ELSE 0 END) as lpaphu
                                        FROM `kirimudd` k
                                        inner join `utd` b on b.`id`=k.`udd`
                                        inner join `stokkantong` s on s.`noKantong`=k.`nokantong`
                                        WHERE month(k.`tgl`)='$bulan1' AND year(k.`tgl`)='$tahun1' AND k.`status`='0'";
                                $qrawu=mysql_query($sqlu);
                                $jml_prcu=0;
                                $jml_tcu=0;
                                $jml_tcaphu=0;
                                $jml_ahfu=0;
                                $jml_ffpu=0;
                                $jml_wbu=0;
                                $jml_weu=0;
                                $jml_lpu=0;
                                $jml_fpu=0;
                                $jml_ldu=0;
                                $jml_bru=0;
                                $jml_bflu=0;
                                $jml_ltflu=0;
                                $jml_prcaphu=0;
                                $jml_lpaphu=0;
                                $dropu=mysql_fetch_assoc($qrawu);

                                $jml_prcu=$jml_prcu + $dropu['prcu'];
                                $jml_tcu=$jml_tcu + $dropu['tcu'];
                                $jml_tcaphu=$jml_tcaphu + $dropu['aphu'];
                                $jml_ahfu=$jml_ahfu + $dropu['ahfu'];
                                $jml_ffpu=$jml_ffpu + $dropu['ffpu'];
                                $jml_wbu=$jml_wbu + $dropu['wbu'];
                                $jml_weu=$jml_weu + $dropu['weu'];
                                $jml_lpu=$jml_lpu + $dropu['lpu'];
                                $jml_fpu=$jml_fpu + $dropu['fpu'];
                                $jml_ldu=$jml_ldu + $dropu['ldu'];
                                $jml_bru=$jml_bru + $dropu['bru'];
                                $jml_bflu=$jml_bflu + $dropu['bflu'];
                                $jml_ltflu=$jml_ltflu + $dropu['ltflu'];
                                $jml_prcaphu=$jml_prcaphu + $dropu['prcaphu'];
                                $jml_lpaphu=$jml_lpaphu + $dropu['lpaphu'];
                                $jml_dropu=$jml_prcu + $jml_tcu + $jml_tcaphu + $jml_ahfu + $jml_ffpu + $jml_wbu + $jml_weu + $jml_lpu + $jml_fpu + $jml_ldu+$jml_bru+$jml_bflu+$jml_ltflu+$jml_prcaphu+$jml_lpaphu;

                                // ===============================PERMINTAAN===========================================
                                $sql="SELECT
                                        h.`rs` as rskode,
                                        r.`NamaRS` as rsnama,
                                        SUM(CASE WHEN d.`JenisDarah`='PRC' THEN d.`Jumlah` ELSE  0 END ) as prc,
                                        SUM(CASE WHEN d.`JenisDarah`='AHF' THEN d.`Jumlah` ELSE  0 END ) as ahf,
                                        SUM(CASE WHEN d.`JenisDarah`='FFP' THEN d.`Jumlah` ELSE  0 END ) as ffp,
                                        SUM(CASE WHEN d.`JenisDarah`='FP' THEN d.`Jumlah` ELSE  0 END ) as fp,
                                        SUM(CASE WHEN d.`JenisDarah`='Leucodepleted' THEN d.`Jumlah` ELSE  0 END )as ld,
                                        SUM(CASE WHEN d.`JenisDarah`='LP' THEN d.`Jumlah` ELSE  0 END ) as lp,
                                        SUM(CASE WHEN d.`JenisDarah`='TC' THEN d.`Jumlah` ELSE  0 END ) as tc,
                                        SUM(CASE WHEN (d.`JenisDarah`='TC Aferesis' or d.`JenisDarah`='TC Apheresis' or d.`JenisDarah`='TC-APH' ) THEN d.`Jumlah` ELSE  0 END ) as aph,
                                        SUM(CASE WHEN d.`JenisDarah`='WB' THEN d.`Jumlah` ELSE  0 END ) as wb,
                                        SUM(CASE WHEN (d.`JenisDarah`='WE' or d.`JenisDarah`='WRC') THEN d.`Jumlah` ELSE  0 END ) as we,
                                        SUM(CASE WHEN (d.`JenisDarah`='--Pilih-' or d.`JenisDarah`='' or d.`JenisDarah`='PRP') THEN d.`Jumlah` ELSE  0 END ) as ll,
                                        SUM(CASE WHEN d.`JenisDarah`='Buffycoat Removal' THEN d.`Jumlah` ELSE  0 END ) as br,
                                        SUM(CASE WHEN d.`JenisDarah`='Bedside Filter Leukosit' THEN d.`Jumlah` ELSE  0 END ) as bfl,
                                        SUM(CASE WHEN d.`JenisDarah`='Lab Type Filter Leukosit' THEN d.`Jumlah` ELSE  0 END ) as ltfl,
                                        SUM(CASE WHEN d.`JenisDarah`='PRC Aferesis' THEN d.`Jumlah` ELSE  0 END ) as prcaph,
                                        SUM(CASE WHEN d.`JenisDarah`='Plasma Aferesis' THEN d.`Jumlah` ELSE  0 END )as lpaph
                                        FROM `htranspermintaan` h
                                        left join `pasien` p on p.`no_rm`=h.`no_rm`
                                        left join `dtranspermintaan` d on d.`NoForm`=h.`noform`
                                        left join `rmhsakit` r on r.`Kode`=h.`rs`WHERE
                                        month(h.`tgl_register`)='$bulan1' and year(h.`tgl_register`)='$tahun1'";
                                $qraw=mysql_query($sql);
                                $jml_prc=0;
                                $jml_tc=0;
                                $jml_tcaph=0;
                                $jml_ahf=0;
                                $jml_ffp=0;
                                $jml_wb=0;
                                $jml_we=0;
                                $jml_lp=0;
                                $jml_fp=0;
                                $jml_ld=0;
                                $jml_ll=0;
                                $jml_br=0;
                                $jml_bfl=0;
                                $jml_ltfl=0;
                                $jml_prcaph=0;
                                $jml_lpaph=0;
                                $qraw1=mysql_fetch_assoc($qraw);
                                $jml_prc   = $jml_prc + $qraw1['prc'] + $jml_prcb + $jml_prcu;
                                $jml_tc    = $jml_tc + $qraw1['tc'] + $jml_tcb + $jml_tcu;
                                $jml_tcaph = $jml_tcaph + $qraw1['aph'] + $jml_tcaphb + $jml_tcaphu;
                                $jml_ahf   = $jml_ahf + $qraw1['ahf'] + $jml_ahfb  + $jml_ahfu;
                                $jml_ffp   = $jml_ffp + $qraw1['ffp'] + $jml_ffpb + $jml_ffpu;
                                $jml_wb    = $jml_wb + $qraw1['wb'] + $jml_wbb + $jml_wbu;
                                $jml_we    = $jml_we + $qraw1['we'] + $jml_web + $jml_weu;
                                $jml_lp    = $jml_lp + $qraw1['lp'] + $jml_lpb + $jml_lpu;
                                $jml_fp    = $jml_fp + $qraw1['fp'] + $jml_fpb + $jml_fpu;
                                $jml_ld    = $jml_ld + $qraw1['ld'] + $jml_ldb + $jml_ldu;
                                $jml_ll    = $jml_ll + $qraw1['ll'] ;
                                $jml_br    = $jml_br + $qraw1['br'] + $jml_brb + $jml_bru;
                                $jml_bfl    = $jml_bfl + $qraw1['bfl'] + $jml_bflb + $jml_bflu;
                                $jml_ltfl    = $jml_ltfl + $qraw1['ltfl'] + $jml_ltflb + $jml_ltflu;
                                $jml_prcaph    = $jml_prcaph + $qraw1['pcraph'] + $jml_prcaphb + $jml_prcaphu;
                                $jml_lpaph    = $jml_lpaph + $qraw1['lpaph'] + $jml_lpaphb + $jml_lpaphu;

                                $totminta=$jml_prc + $jml_tc + $jml_tcaph + $jml_ahf + $jml_ffp + $jml_wb + $jml_we + $jml_lp + $jml_fp + $jml_ld + $jml_ll +$jml_br+$jml_bfl+$jml_ltfl+$jml_prcaph+$jml_lpaph;


                                //===========================PEMAKAIAN===============================================

                                $lap="SELECT
                                    h.`rs` as rskode,
                                    r.`NamaRS` as rsnama,
                                    SUM(CASE WHEN d.`produk_darah`='PRC' THEN 1 ELSE  0 END ) as prca,
                                    SUM(CASE WHEN d.`produk_darah`='AHF' THEN 1 ELSE  0 END ) as ahfa,
                                    SUM(CASE WHEN d.`produk_darah`='FFP' THEN 1 ELSE  0 END ) as ffpa,
                                    SUM(CASE WHEN d.`produk_darah`='FP' THEN 1 ELSE  0 END ) as fpa,
                                    SUM(CASE WHEN d.`produk_darah`='Leucodepleted' THEN 1 ELSE  0 END )as lda,
                                    SUM(CASE WHEN d.`produk_darah`='LP' THEN 1 ELSE  0 END ) as lpa,
                                    SUM(CASE WHEN d.`produk_darah`='TC' THEN 1 ELSE  0 END ) as tca,
                                    SUM(CASE WHEN (d.`produk_darah`='TC Aferesis' or d.`produk_darah`='TC Apheresis' or d.`produk_darah`='TC-APH' ) THEN 1 ELSE  0 END ) as apha,
                                    SUM(CASE WHEN d.`produk_darah`='WB' THEN 1 ELSE  0 END ) as wba,
                                    SUM(CASE WHEN (d.`produk_darah`='WE' or d.`produk_darah`='WRC') THEN 1 ELSE  0 END ) as wea,
                                    SUM(CASE WHEN (d.`produk_darah`='--Pilih-' or d.`produk_darah`='' or d.`produk_darah`='PRP') THEN 1 ELSE  0 END ) as lla,
                                    SUM(CASE WHEN d.`produk_darah`='Buffycoat Removal' THEN 1 ELSE  0 END ) as bra,
                                    SUM(CASE WHEN d.`produk_darah`='Bedside Filter Leukosit' THEN 1 ELSE  0 END ) as bfla,
                                    SUM(CASE WHEN d.`produk_darah`='Lab Type Filter Leukosit' THEN 1 ELSE  0 END )as ltfla,
                                    SUM(CASE WHEN d.`produk_darah`='PRC Aferesis' THEN 1 ELSE  0 END ) as prcapha,
                                    SUM(CASE WHEN d.`produk_darah`='Plasma Aferesis' THEN 1 ELSE  0 END ) as lpapha
                                    FROM `htranspermintaan` h
                                    inner join `pasien` p on p.`no_rm`=h.`no_rm`
                                    inner join `dtransaksipermintaan` d on d.`NoForm`=h.`noform`
                                    inner join `rmhsakit` r on r.`Kode`=h.`rs`
                                    WHERE
                                    month(d.`tgl_keluar`)='$bulan1' and year(d.`tgl_keluar`)='$tahun1'
                                    and d.`Status`='0'";

                                $pakai=mysql_query($lap);
                                $jml_prca=0;
                                $jml_tca=0;
                                $jml_tcapha=0;
                                $jml_ahfa=0;
                                $jml_ffpa=0;
                                $jml_wba=0;
                                $jml_wea=0;
                                $jml_lpa=0;
                                $jml_fpa=0;
                                $jml_lda=0;
                                $jml_lla=0;

                                $jml_bra=0;
                                $jml_bfla=0;
                                $jml_ltfla=0;
                                $jml_prcapha=0;
                                $jml_lpapha=0;
                                $pakai1=mysql_fetch_assoc($pakai);
                                $jml_prca   = $jml_prca + $pakai1['prca'] + $jml_prcb + $jml_prcu;
                                $jml_tca    = $jml_tca + $pakai1['tca'] + $jml_tcb + $jml_tcu;
                                $jml_tcapha = $jml_tcapha + $pakai1['apha'] + $jml_tcaphb + $jml_tcaphu;
                                $jml_ahfa   = $jml_ahfa + $pakai1['ahfa'] + $jml_ahfb  + $jml_ahfu;
                                $jml_ffpa   = $jml_ffpa + $pakai1['ffpa'] + $jml_ffpb + $jml_ffpu;
                                $jml_wba    = $jml_wba + $pakai1['wba'] + $jml_wbb + $jml_wbu;
                                $jml_wea    = $jml_wea + $pakai1['wea'] + $jml_web + $jml_weu;
                                $jml_lpa    = $jml_lpa + $pakai1['lpa'] + $jml_lpb + $jml_lpu;
                                $jml_fpa    = $jml_fpa + $pakai1['fpa'] + $jml_fpb + $jml_fpu;
                                $jml_lda    = $jml_lda + $pakai1['lda'] + $jml_ldb + $jml_ldu;
                                $jml_lla    = $jml_lla + $pakai1['lla'];

                                $jml_bra    = $jml_bra + $pakai1['bra'] + $jml_brb + $jml_bru;
                                $jml_bfla    = $jml_bfla + $pakai1['bfla'] + $jml_bflb + $jml_bflu;
                                $jml_ltfla    = $jml_ltfla + $pakai1['ltfla'] + $jml_ltflb + $jml_ltflu;
                                $jml_prcapha    = $jml_prcapha + $pakai1['prcapha'] + $jml_prcaphb + $jml_prcaphu;
                                $jml_lpapha    = $jml_lpapha + $pakai1['lpapha'] + $jml_lpaph + $jml_lpaphu;
                                $totpakai=$jml_prca + $jml_tca + $jml_tcapha + $jml_ahfa + $jml_ffpa + $jml_wba + $jml_wea + $jml_lpa + $jml_fpa + $jml_lda + $jml_lla + $jml_bra+$jml_bfla+$jml_prcapha+$jml_lpapha;
                                ?>
                                <tbody>
                                    <tr>
                                        <td rowspan="7">BIASA</td>
                                        <td>Whole Blood (WB)</td>                   <td class="text-center"><?php echo $komp1['WB1'];?></td>     <td class="text-center"><?php echo $jml_wb;?></td>  <td class="text-center"><?php echo $jml_wba;?></td>
                                    </tr>
                                    <tr> <td>Packed Red cell (PRC)</td>             <td class="text-center"><?php echo $komp['PRC'];?></td>    <td class="text-center"><?php echo $jml_prc;?></td>  <td class="text-center"><?php echo $jml_prca;?></td></tr>
                                    <tr> <td>Plasma/ Liquid Plasma (LP)</td>        <td class="text-center"><?php echo $komp['LP'];?></td>     <td class="text-center"><?php echo $jml_lp;?></td>  <td class="text-center"><?php echo $jml_lpa;?></td></tr>
                                    <tr> <td>Fresh Frozen Plasma (FFP)</td>         <td class="text-center"><?php echo $komp['FFP'];?></td>    <td class="text-center"><?php echo $jml_ffp;?></td>  <td class="text-center"><?php echo $jml_ffpa;?></td></tr>
                                    <tr> <td>Trombocyte Concentrat (TC)</td>        <td class="text-center"><?php echo $komp['TC'];?></td>     <td class="text-center"><?php echo $jml_tc;?></td>  <td class="text-center"><?php echo $jml_tca;?></td></tr>
                                    <tr> <td>Cryo-precipitate/ AHF</td>             <td class="text-center"><?php echo $komp['AHF'];?></td>    <td class="text-center"><?php echo $jml_ahf;?></td>  <td class="text-center"><?php echo $jml_ahfa;?></td></tr>
                                    <tr> <td>Washed Erythrocytes (WE)</td>          <td class="text-center"><?php echo $komp['WE'];?></td>     <td class="text-center"><?php echo $jml_we;?></td>  <td class="text-center"><?php echo $jml_wea;?></td></tr>
                                    <tr>
                                        <td rowspan="4">LEUKODEPLETED</td>
                                        <td>Buffycoat Removal (Leukoreduced)</td>                       <td class="text-center"><?php echo $komp['BUF_R'];?></td>      <td class="text-center"><?php echo $jml_br;?></td>  <td class="text-center"><?php echo $jml_bra;?></td>
                                    </tr>
                                    <tr> <td>Inline Filter Leukosit (Pre-storage Leukodepleted)</td>    <td class="text-center"><?php echo $komp['LEUCO'];?></td>      <td class="text-center"><?php echo $jml_ld;?></td>  <td class="text-center"><?php echo $jml_lda;?></td></tr>
                                    <tr> <td>Bedside Filter Leukosit (Post-storage Leukodepleted)</td>  <td class="text-center"><?php echo $komp['BF_LEUCO'];?></td>   <td class="text-center"><?php echo $jml_bfl;?></td>  <td class="text-center"><?php echo $jml_bfla;?></td></tr>
                                    <tr> <td>Lab Type Filter Leukosit (Post-storage Leukodepleted)</td> <td class="text-center"><?php echo $komp['LTF_LEUCO'];?></td>  <td class="text-center"><?php echo $jml_ltfl;?></td>  <td class="text-center"><?php echo $jml_ltfla;?></td></tr>

                                    <tr>
                                        <td rowspan="3">APHERESIS</td>
                                        <td>Packed Red cell (PRC)</td>              <td class="text-center"><?php echo $komp['PRC_APH']+$q_aph['PRC_Aph'];?></td>         <td class="text-center"><?php echo $jml_prcaph;?></td>  <td class="text-center"><?php echo $jml_prcapha;?></td>
                                    </tr>
                                    <tr> <td>Trombocyte Concentrat (TC)</td>        <td class="text-center"><?php echo $komp['TC_APH']+$q_aph['TC_Aph'];?></td>     <td class="text-center"><?php echo $jml_tcaph;?></td>  <td class="text-center"><?php echo $jml_tcapha;?></td></tr>
                                    <tr> <td>Plasma</td>                            <td class="text-center"><?php echo $komp['LP_APH']+$q_aph['LP_Aph'];?></td>     <td class="text-center"><?php echo $jml_lpaph;?></td>  <td class="text-center"><?php echo $jml_lpapha;?></td></tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-center">JUMLAH</th>
                                        <th class="text-center"><?php echo $tot_produksi;?></th>
                                        <th class="text-center"><?php echo $totminta;?></th>
                                        <th class="text-center"><?php echo $totpakai;?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <div class="panel-footer">
                    <a href="pmitatausaha.php?module=rpt_komponen&t=<?php echo $v_tahun;?>&b=<?php echo $v_periode;?>" class="btn btn-default" id="shadow2"><i class="fa fa-print" aria-hidden="true" title="Cetak Laporan"></i>&nbsp;&nbsp;Cetak Laporan</a>
                    <a href="pmitatausaha.php?module=laporan" class="btn btn-default" id="shadow2" title="Kembali ke Menu Laporan"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
