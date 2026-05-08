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
                        <h4 style="text-transform: uppercase;">LAPORAN PERMINTAAN DARAH</h4>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel-title pull-right">
                            <form class="form-inline" role="form" method="POST" action="pmitatausaha.php?module=permintaan">
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
                        <div class="text-center"><h4>LAPORAN PERMINTAAN DARAH</h4></div>
                        <div class="text-center"><h4><?php echo $nama_udd;?></h4></div>
                        <div class="text-center"><h4 style="text-transform: uppercase;"> <?php echo $namaperiode.' '.$v_tahun;?></h4></div>
                    </div>
                        <?php
                        //=================================TOTAL PERMINTAAN=======================================
                        $sql="SELECT
                                h.`rs` as rskode,
                                SUM(CASE WHEN h.`bagian`='ANAK' THEN t.`Jumlah` ELSE  0 END ) as anak,
                                SUM(CASE WHEN h.`bagian`='BEDAH' THEN t.`Jumlah` ELSE  0 END ) as bedah,
                                SUM(CASE WHEN h.`bagian`='DALAM' THEN t.`Jumlah` ELSE  0 END ) as interna,
                                SUM(CASE WHEN h.`bagian`='KANDUNGAN' THEN t.`Jumlah` ELSE  0 END ) as keb,
                                SUM(CASE WHEN h.`bagian`='HD' THEN t.`Jumlah` ELSE  0 END ) as tht,
				SUM(CASE WHEN h.`bagian`='HND' THEN t.`Jumlah` ELSE  0 END ) as hnd,
				SUM(CASE WHEN h.`bagian`='ICU' THEN t.`Jumlah` ELSE  0 END ) as icu,
                                SUM(CASE WHEN h.`bagian`='Lain - lain' THEN t.`Jumlah` ELSE  0 END ) as ll
                                FROM `htranspermintaan` h
                                left join `pasien` p on p.`no_rm`=h.`no_rm`
                                left join `dtranspermintaan` t on t.`NoForm`=h.`noform` WHERE
                                month(h.`tgl_register`)='$bulan1' and year(h.`tgl_register`)='$tahun1'";

                        //echo "$sql";
                        $qraw=mysql_query($sql);
                        $jml_anak=0;
                        $jml_bedah=0;
                        $jml_interna=0;
                        $jml_keb=0;
                        $jml_tht=0;
			$jml_hnd=0;
			$jml_icu=0;
                        $jml_ll=0;
                        $jml_row=0;

                        $qraw1=mysql_fetch_assoc($qraw);
                        $jml_anak=$jml_anak + $qraw1['anak'];
                        $jml_bedah=$jml_bedah + $qraw1['bedah'];
                        $jml_interna=$jml_interna + $qraw1['interna'];
                        $jml_keb=$jml_keb + $qraw1['keb'];
                        $jml_tht=$jml_tht + $qraw1['tht'];
			$jml_hnd=$jml_hnd + $qraw1['hnd'];
			$jml_icu=$jml_icu + $qraw1['icu'];
                        $jml_ll=$jml_ll + $qraw1['ll'] + $qraw1['tht'] + $qraw1['hnd'] + $qraw1['icu'];
                        $row_ttl= $jml_anak + $jml_bedah + $jml_interna + $jml_keb + $jml_ll;
                        //=========================jumlah terpenuhi==============================================

                        $sql1="SELECT
                                h.`rs` as rskode,
                                SUM(CASE WHEN h.`bagian`='ANAK' THEN 1 ELSE  0 END ) as anak1,
                                SUM(CASE WHEN h.`bagian`='BEDAH' THEN 1 ELSE  0 END ) as bedah1,
                                SUM(CASE WHEN h.`bagian`='DALAM' THEN 1 ELSE  0 END ) as interna1,
                                SUM(CASE WHEN h.`bagian`='KANDUNGAN' THEN 1 ELSE  0 END ) as keb1,
                                SUM(CASE WHEN h.`bagian`='HD' THEN 1 ELSE  0 END ) as tht1,
				SUM(CASE WHEN h.`bagian`='HND' THEN 1 ELSE  0 END ) as hnd1,
				SUM(CASE WHEN h.`bagian`='ICU' THEN 1 ELSE  0 END ) as icu1,
                                SUM(CASE WHEN h.`bagian`='Lain - lain' THEN 1 ELSE  0 END ) as ll1
                                FROM `htranspermintaan` h
                                inner join `pasien` p on p.`no_rm`=h.`no_rm`
                                inner join `dtransaksipermintaan` d on d.`NoForm`=h.`noform` WHERE
                                month(d.`tgl`)='$bulan1' and year(d.`tgl`)='$tahun1'";
                        //echo "$sql";
                        $qraw1=mysql_query($sql1);
                        $jml_anak1=0;
                        $jml_bedah1=0;
                        $jml_interna1=0;
                        $jml_keb1=0;
                        $jml_tht1=0;
			$jml_hnd1=0;
			$jml_icu1=0;
                        $jml_ll1=0;

                        $qraw4=mysql_fetch_assoc($qraw1);
                        $jml_anak1=$jml_anak1 + $qraw4['anak1'];
                        $jml_bedah1=$jml_bedah1 + $qraw4['bedah1'];
                        $jml_interna1=$jml_interna1 + $qraw4['interna1'];
                        $jml_keb1=$jml_keb1 + $qraw4['keb1'];
                        $jml_tht1=$jml_tht1 + $qraw4['tht1'];
			$jml_hnd1=$jml_hnd1 + $qraw4['hnd1'];
			$jml_icu1=$jml_icu1 + $qraw4['icu1'];
                        $jml_ll1=$jml_ll1 + $qraw4['ll1'] + $qraw4['tht1'] + $qraw4['hnd1'] + $qraw4['icu1'];
                        $row_ttl1 = $jml_anak1 + $jml_bedah1 + $jml_interna1 + $jml_keb1 + $jml_ll1;
                        //==========================JUMLAH TERPAKAI==================================================

                        $sql2="SELECT
                            h.`rs` as rskode,
                            SUM(CASE WHEN h.`bagian`='ANAK' THEN 1 ELSE  0 END ) as anak2,
                            SUM(CASE WHEN h.`bagian`='BEDAH' THEN 1 ELSE  0 END ) as bedah2,
                            SUM(CASE WHEN h.`bagian`='DALAM' THEN 1 ELSE  0 END ) as interna2,
                            SUM(CASE WHEN h.`bagian`='KANDUNGAN' THEN 1 ELSE  0 END ) as keb2,
                            SUM(CASE WHEN h.`bagian`='HD' THEN 1 ELSE  0 END ) as tht2,
			    SUM(CASE WHEN h.`bagian`='HND' THEN 1 ELSE  0 END ) as hnd2,
			    SUM(CASE WHEN h.`bagian`='ICU' THEN 1 ELSE  0 END ) as icu2,
                            SUM(CASE WHEN h.`bagian`='Lain - lain' THEN 1 ELSE  0 END ) as ll2
                            FROM `htranspermintaan` h
                            inner join `pasien` p on p.`no_rm`=h.`no_rm`
                            inner join `dtransaksipermintaan` d on d.`NoForm`=h.`noform` WHERE
                            month(d.`tgl_keluar`)='$bulan1' and year(d.`tgl_keluar`)='$tahun1' AND d.`Status`='0'";
                        //echo "$sql";
                        $qraw2=mysql_query($sql2);
                        $jml_anak2=0;
                        $jml_bedah2=0;
                        $jml_interna2=0;
                        $jml_keb2=0;
                        $jml_tht2=0;
			$jml_hnd2=0;
			$jml_icu2=0;
                        $jml_ll2=0;

                        $qraw3=mysql_fetch_assoc($qraw2);
                        $jml_anak2=$jml_anak2 + $qraw3['anak2'];
                        $jml_bedah2=$jml_bedah2 + $qraw3['bedah2'];
                        $jml_interna2=$jml_interna2 + $qraw3['interna2'];
                        $jml_keb2=$jml_keb2 + $qraw3['keb2'];
                        $jml_tht2=$jml_tht2 + $qraw3['tht2'];
			$jml_hnd2=$jml_hnd2 + $qraw3['hnd2'];
			$jml_icu2=$jml_icu2 + $qraw3['icu2'];
                        $jml_ll2=$jml_ll2 + $qraw3['ll2'] + $qraw3['tht2'] + $qraw3['hnd2'] + $qraw3['icu2'];
                        $row_ttl2 = $jml_anak2 + $jml_bedah2 + $jml_interna2 + $jml_keb2 + $jml_ll2;
                        //PEMENUHAN
                        $penuhi_anak=$jml_anak1/$jml_anak*100;
                        $penuhi_bedah=$jml_bedah1/$jml_bedah*100;
                        $penuhi_interna=$jml_interna1/$jml_interna*100;
                        $penuhi_keb=$jml_keb1/$jml_keb*100;
                        $penuhi_ll=$jml_ll1/$jml_ll*100;
                        $penuhi_ttl=$row_ttl1/$row_ttl*100;

                        //PEMBULATAN
                        $persen_penuhi_anak=round($penuhi_anak,1);
                        $persen_penuhi_bedah=round($penuhi_bedah,1);
                        $persen_penuhi_interna=round($penuhi_interna,1);
                        $persen_penuhi_keb=round($penuhi_keb,1);
                        $persen_penuhi_ll=round($penuhi_ll,1);
                        $persen_penuhi_ttl=round($penuhi_ttl,1);

                        //PERSEN PEMAKAIAN
                        $pakai_anak=$jml_anak2/$jml_anak*100;
                        $pakai_bedah=$jml_bedah2/$jml_bedah*100;
                        $pakai_interna=$jml_interna2/$jml_interna*100;
                        $pakai_keb=$jml_keb2/$jml_keb*100;
                        $pakai_ll=$jml_ll2/$jml_ll*100;
                        $pakai_ttl=$row_ttl2/$row_ttl*100;

                        // PEMBULATAN
                        $persen_pakai_anak=round($pakai_anak,1);
                        $persen_pakai_bedah=round($pakai_bedah,1);
                        $persen_pakai_interna=round($pakai_interna,1);
                        $persen_pakai_keb=round($pakai_keb,1);
                        $persen_pakai_ll=round($pakai_ll,1);
                        $persen_pakai_ttl=round($pakai_ttl,1);
                        ?>
                    <div class="col-lg-12">
                    <h4>E.1. JUMLAH PERMINTAAN DARAH</h4>
                    <table class="table table-bordered table-responsive">
                        <thead class="pmi">
                            <tr>
                                <th rowspan="2" class="text-center">NO.</th>
                                <th rowspan="2" class="text-center">BAGIAN PERAWATAN DI RS</th>
                                <th rowspan="2" class="text-center">Jumlah Total Permintaan Darah (kantong)</th>
                                <th rowspan="2" class="text-center">Jumlah Permintaan Darah Yang Dapat Dipenuhi (kantong)</th>
                                <th rowspan="2" class="text-center">Jumlah Permintaan Darah Yang Terpakai (kantong)</th>
                                <th colspan="2" class="text-center">PERSENTASE(%)</th>
                            </tr>
                            <tr>
                                <th class="text-center">PEMENUHAN</th>
                                <th class="text-center">TERPAKAI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Anak</td>
                                <td class="text-center"><?=$jml_anak;?></td>
                                <td class="text-center"><?=$jml_anak1;?></td>
                                <td class="text-center"><?=$jml_anak2;?></td>
                                <td class="text-center"><?=$persen_penuhi_anak;?>%</td>
                                <td class="text-center"><?=$persen_pakai_anak;?>%</td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>Bedah</td>
                                <td class="text-center"><?=$jml_bedah;?></td>
                                <td class="text-center"><?=$jml_bedah1;?></td>
                                <td class="text-center"><?=$jml_bedah2;?></td>
                                <td class="text-center"><?=$persen_penuhi_bedah;?>%</td>
                                <td class="text-center"><?=$persen_pakai_bedah;?>%</td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td>Penyakit Dalam</td>
                                <td class="text-center"><?=$jml_interna;?></td>
                                <td class="text-center"><?=$jml_interna1;?></td>
                                <td class="text-center"><?=$jml_interna2;?></td>
                                <td class="text-center"><?=$persen_penuhi_interna;?>%</td>
                                <td class="text-center"><?=$persen_pakai_interna;?>%</td>
                            </tr>
                            <tr>
                                <td class="text-center">4</td>
                                <td>Kandungan</td>
                                <td class="text-center"><?=$jml_keb;?></td>
                                <td class="text-center"><?=$jml_keb1;?></td>
                                <td class="text-center"><?=$jml_keb2;?></td>
                                <td class="text-center"><?=$persen_penuhi_keb;?>%</td>
                                <td class="text-center"><?=$persen_pakai_keb;?>%</td>
                            </tr>
                            <tr>
                                <td class="text-center">5</td>
                                <td>Lain-lain</td>
                                <td class="text-center"><?=$jml_ll;?></td>
                                <td class="text-center"><?=$jml_ll1;?></td>
                                <td class="text-center"><?=$jml_ll2;?></td>
                                <td class="text-center"><?=$persen_penuhi_ll;?>%</td>
                                <td class="text-center"><?=$persen_pakai_ll;?>%</td>
                            </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                                <th colspan="2" class="text-center"> TOTAL </th>
                                <th class="text-center"> <?=$row_ttl;?> </th>
                                <th class="text-center"> <?=$row_ttl1;?> </th>
                                <th class="text-center"> <?=$row_ttl2;?> </th>
                                <th class="text-center"> <?=$persen_penuhi_ttl;?>% </th>
                                <th class="text-center"> <?=$persen_pakai_ttl;?>%</th>
                            </tr>
                        </tfoot>
                    </table>
                    </div> <!--Col lg 12-->
                    <div class="col-lg-8"><!-- Rumah Sakit yang dilayani -->
                        <?php
                        //============================JUMLAH_RS_YANG_DILAYANI=======================================================
                        $rs_dlm=mysql_query("SELECT COUNT(DISTINCT `rs`) AS rsd FROM `htranspermintaan` WHERE `wilayah`='0' AND
                        month(`tgl_register`)='$bulan1' and year(`tgl_register`)='$tahun1'");
                        $dlm_kota=mysql_fetch_assoc($rs_dlm);
                        $hasil_dlm_kota=0;
                        $hasil_dlm_kota= $hasil_dlm_kota + $dlm_kota['rsd'];

                        $rs_luar=mysql_query("SELECT COUNT(DISTINCT `rs`) AS rsl FROM `htranspermintaan` WHERE `wilayah`='1' AND
                        month(`tgl_register`)='$bulan1' and year(`tgl_register`)='$tahun1'");
                        $luar_kota=mysql_fetch_assoc($rs_luar);
                        $hasil_luar_kota=0;
                        $hasil_luar_kota= $hasil_luar_kota + $luar_kota['rsl'];

                        $total_rs=$hasil_dlm_kota + $hasil_luar_kota;
                        ?>
                        <h4>E.2. JUMLAH RS YANG DILAYANI</h4>
                        <table class="table table-bordered table-responsive">
                            <thead class="pmi">
                                <tr>
                                    <th class="text-center">NO.</th>
                                    <th class="text-center">JENIS RS YANG DILAYANI BERDASARKAN LOKASI</th>
                                    <th class="text-center">JUMLAH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-left">Dalam Kota</td>
                                    <td class="text-center"><?=$hasil_dlm_kota;?></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-left" align="left">Luar Kota</td>
                                    <td class="text-center"><?=$hasil_luar_kota;?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-center"> JUMLAH </th>
                                    <th class="text-center"><?=$total_rs;?></th>
                                </tr>
                            </tfoot>
                        </table>


                    </div> <!-- Rumah Sakit yang dilayani -->
                    <div class="col-lg-8"> <!-- Dropping -->
                        <?php
                        //====================DISTRIBUSI KOMPONEN DARAH================================
                        $cari=mysql_query("SELECT
                                        SUM(CASE WHEN k.`status`='0' THEN 1 ELSE 0 END) as bdrs
                                        FROM `kirimbdrs` k
                                        inner join `bdrs` b on b.`kode`=k.`bdrs`
                                        inner join `stokkantong` s on s.`noKantong`=k.`nokantong`
                                        inner join `user` u on u.`id_user`= k.`petugas`
                                        WHERE
                                        k.`status`='0' and `tglkembali` is null and `tglbatal` is null and
                                        month(k.`tgl`)='$bulan1' AND year(k.`tgl`)='$tahun1'");
                        $hasil_cari=mysql_fetch_assoc($cari);
                        $bdrs=0;
                        $bdrs=$bdrs + $hasil_cari['bdrs'];

                        $cari1=mysql_query("SELECT COUNT(DISTINCT `nokantong`) AS udd FROM `kirimudd` WHERE `status`='0' AND month(`tgl`)='$bulan1' and year (`tgl`)='$tahun1' ");
                        $hasil_cari1=mysql_fetch_assoc($cari1);
                        $udd=0;
                        $udd=$udd + $hasil_cari1['udd'];
                        $total_kirim = $bdrs + $udd;
                        ?>

                        <h4>E.3. DISTRIBUSI KOMPONEN DARAH</h4>
                        <table class="table table-bordered table-responsive">
                            <thead class="pmi">
                                <tr>
                                    <th class="text-center">NO.</th>
                                    <th class="text-center">TUJUAN DISTRIBUSI</th>
                                    <th class="text-center">JUMLAH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-left">BDRS</td>
                                    <td class="text-center"><?=$bdrs;?></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-left" align="left">UDD lain</td>
                                    <td class="text-center"><?=$udd;?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-center"> JUMLAH </th>
                                    <th class="text-center"><?=$total_kirim;?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div> <!-- Dropping -->
                </div>

            </div>
            <div class="panel-footer">
                <a href="pmitatausaha.php?module=rpt_permintaan&t=<?php echo $v_tahun;?>&b=<?php echo $v_periode;?>" class="btn btn-default" id="shadow2"><i class="fa fa-print" aria-hidden="true" title="Cetak Laporan"></i>&nbsp;&nbsp;Cetak Laporan</a>
                <a href="pmitatausaha.php?module=laporan" class="btn btn-default" id="shadow2" title="Kembali ke Menu Laporan"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
