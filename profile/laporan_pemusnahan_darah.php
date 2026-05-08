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
require_once('config/db_connect.php');

$tahun       = date("Y");
$bulan       = date("m");
$v_tahun     = $_POST['tahun_lap'];
$v_bulan     = $_POST['periode'];
if (empty($v_tahun)){$v_tahun=$tahun;}
if (empty($v_bulan)){$v_periode=$bulan;}

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
            <div class="col-lg-8">
                <br>
                    <div class="panel with-nav-tabs panel-primary" id="shadow1">
                        <div class="panel-heading">
                            <h4 style="text-transform: uppercase;">LAPORAN DARAH YANG DIMUSNAHKAN <?php echo 'BULAN '.$namaperiode.' '.$v_tahun;?></h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form class="form-inline" role="form" method="POST" action="pmitatausaha.php?module=musnah">
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
                                            <select class="form-control" name="periode">
                                                <option value="1" <?php echo $b1;?> >Bulan Januari</option>
                                                <option value="2" <?php echo $b2;?> >Bulan Februari</option>
                                                <option value="3" <?php echo $b3;?> >Bulan Maret</option>
                                                <option value="4" <?php echo $b4;?> >Bulan April</option>
                                                <option value="5" <?php echo $b5;?> >Bulan Mei</option>
                                                <option value="6" <?php echo $b6;?> >Bulan Juni</option>
                                                <option value="7" <?php echo $b7;?> >Bulan Juli</option>
                                                <option value="8" <?php echo $b8;?> >Bulan Agustus</option>
                                                <option value="9" <?php echo $b9;?> >Bulan September</option>
                                                <option value="10" <?php echo $b10;?> >Bulan Oktober</option>
                                                <option value="11" <?php echo $b11;?> >Bulan November</option>
                                                <option value="12" <?php echo $b12;?> >Bulan Desember</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" name="tahun_lap">
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
                                    <hr>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="text-center"><h4>LAPORAN DARAH YANG DIMUSNAHKAN</h4></div>
                                    <div class="text-center"><h4><?php echo $nama_udd;?></h4></div>
                                    <div class="text-center"><h4 style="text-transform: uppercase;"> <?php echo 'BULAN '.$namaperiode.' '.$v_tahun;?></h4></div>
                                    <div class="text-left"><h5>C.JUMLAH KANTONG DARAH YANG DIMUSNAHKAN BERDASARKAN PENYEBAB</h5></div>
                                    <table class="table table-hover table-bordered">
                                        <thead class="pmi">
                                            <th class="text-center">No</th>
                                            <th class="text-center">Penyebab Darah Dimusnahkan</th>
                                            <th class="text-center">Jumlah Kantong Darah <br>yang Dimusnahkan</th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        //lisis masuk pada masalah pada penyimpanan
                                            $rkp="SELECT
                                                    COUNT( CASE WHEN `alasan_buang`='0' THEN 1 ELSE NULL END) AS  'Gagal_Pengambilan_Darah',
                                                    COUNT( CASE WHEN `alasan_buang` in ('4','6','11','20','21') THEN 1 ELSE NULL END) AS  'IMLTD_Reaktif',
                                                    COUNT( CASE WHEN `alasan_buang`='2' THEN 1 ELSE NULL END) AS  'Kedaluwarsa',
                                                    COUNT( CASE WHEN `alasan_buang` in ('10','13','9','15') THEN 1 ELSE NULL END) AS  'Masalah_dalam_proses_produksi',
                                                    COUNT( CASE WHEN `alasan_buang`='1' THEN 1 ELSE NULL END) AS  'Masalah_dalam_proses_penyimpanan',
                                                    COUNT( CASE WHEN `alasan_buang` in ('3','5','7','8','12','14','16','17','18','19') THEN 1 ELSE NULL END) AS  'Penyebab_Lain'
                                                    FROM `ar_stokkantong`
                                                    WHERE
                                                    month(`tgl_buang`)='$v_bulan' and year(`tgl_buang`)='$v_tahun'";
                                            //echo "$rkp<br>";
                                            //echo "$tanggalawal<br>";
                                            //echo "$tanggalakhir<br>";
                                            $q_sel=mysql_query($rkp);
                                            $no=0;
                                            $total=0;
                                            $result=mysql_fetch_assoc($q_sel);
                                            $total=$result['Gagal_Pengambilan_Darah']+$result['IMLTD_Reaktif']+$result['Kedaluwarsa']+$result['Masalah_dalam_proses_produksi']+$result['Penyebab_Lain'];

                                            ?>
                                        <tr><td>1.</td><td>Gagal Pengambilan Darah</td><td class="text-right"><?php echo number_format($result['Gagal_Pengambilan_Darah'],0,",","."); ?></td></tr>
                                        <tr><td>2.</td><td>IMLTD Reaktif</td><td class="text-right"><?php echo number_format($result['IMLTD_Reaktif'],0,",","."); ?></td></tr>
                                        <tr><td>3.</td><td>Kedaluwarsa</td><td class="text-right"><?php echo number_format($result['Kedaluwarsa'],0,",","."); ?></td></tr>
                                        <tr><td>4.</td><td>Masalah dalam proses produksi</td><td class="text-right"><?php echo number_format($result['Masalah_dalam_proses_produksi'],0,",","."); ?></td></tr>
                                        <tr><td>5.</td><td>Masalah dalam proses penyimpanan</td><td class="text-right"><?php echo number_format($result['Masalah_dalam_proses_penyimpanan'],0,",","."); ?></td></tr>
                                        <tr><td>6.</td><td>Penyebab Lain</td><td class="text-right"><?php echo number_format($result['Penyebab_Lain'],0,",","."); ?></td></tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-right">Jumlah</th>
                                            <td class="text-right"><?php echo number_format($total,0,",",".");?></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <a href="pmitatausaha.php?module=upload&mdl=musnah&t=<?php echo $v_tahun;?>&b=<?php echo $v_periode;?>&t1=<?php echo $tanggalawal;?>&t2=<?php echo $tanggalakhir;?>" class="btn btn-default" id="shadow2" title="Upload Laporan ke UDD Pusat. Yang dapat diupload adalah periode Bulanan"><i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;Upload ke Pusat</a>
                            <a href="pmitatausaha.php?module=rpt_musnah&t=<?php echo $v_tahun;?>&b=<?php echo $v_bulan;?>" class="btn btn-default" id="shadow2"><i class="fa fa-print" aria-hidden="true" title="Cetak Laporan"></i>&nbsp;&nbsp;Cetak Laporan</a>
                            <a href="pmitatausaha.php?module=laporan" class="btn btn-default" id="shadow2" title="Kembali ke Menu Laporan"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
                        </div>
            </div>
        </div>
    </div>
</body>
</html>
