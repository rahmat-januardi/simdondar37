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
    if (empty($v_tahun)) {
        $v_tahun = $tahun;
    }
    if (empty($v_bulan)) {
        $v_bulan = $bulan;
    }
    $udd = "select * from utd where aktif='1'";
    $udd = mysql_fetch_assoc(mysql_query($udd));
    $id_udd = $udd['id'];
    $nama_udd = $udd['nama'];
    switch ($v_bulan) {
        case '1':
            $namaperiode = "JANUARI";
            break;
        case '2':
            $namaperiode = "FEBRUARI";
            break;
        case '3':
            $namaperiode = "MARET";
            break;
        case '4':
            $namaperiode = "APRIL";
            break;
        case '5':
            $namaperiode = "MEI";
            break;
        case '6':
            $namaperiode = "JUNI";
            break;
        case '7':
            $namaperiode = "JULI";
            break;
        case '8':
            $namaperiode = "AGUSTUS";
            break;
        case '9':
            $namaperiode = "SEPTEMBER";
            break;
        case '10':
            $namaperiode = "OKTOBER";
            break;
        case '11':
            $namaperiode = "NOVEMBER";
            break;
        case '12':
            $namaperiode = "DESEMBER";
            break;
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
                                <div>
                                    <h4 style="text-transform: uppercase;">LAPORAN DONASI DARAH LENGKAP</h4>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="panel-title pull-right">
                                    <form class="form-inline" method="POST" action="pmitatausaha.php?module=lap_donasi_wb">
                                        <div class="form-group">
                                            Bulan
                                            <?php
                                            $b1 = '';
                                            $b2 = '';
                                            $b3 = '';
                                            $b4 = '';
                                            $b5 = '';
                                            $b6 = '';
                                            $b7 = '';
                                            $b8 = '';
                                            $b8 = '';
                                            $b10 = '';
                                            $b11 = '';
                                            $b12 = '';
                                            switch ($v_bulan) {
                                                case '01';
                                                    $b1 = 'Selected';
                                                    break;
                                                case '02';
                                                    $b2 = 'Selected';
                                                    break;
                                                case '03';
                                                    $b3 = 'Selected';
                                                    break;
                                                case '04';
                                                    $b4 = 'Selected';
                                                    break;
                                                case '05';
                                                    $b5 = 'Selected';
                                                    break;
                                                case '06';
                                                    $b6 = 'Selected';
                                                    break;
                                                case '07';
                                                    $b7 = 'Selected';
                                                    break;
                                                case '08';
                                                    $b8 = 'Selected';
                                                    break;
                                                case '09';
                                                    $b9 = 'Selected';
                                                    break;
                                                case '10';
                                                    $b10 = 'Selected';
                                                    break;
                                                case '11';
                                                    $b11 = 'Selected';
                                                    break;
                                                case '12';
                                                    $b12 = 'Selected';
                                                    break;
                                            }
                                            ?>
                                            <select class="form-control" name="bulan">
                                                <option value="1" <?php echo $b1; ?>>Januari</option>
                                                <option value="2" <?php echo $b2; ?>>Februari</option>
                                                <option value="3" <?php echo $b3; ?>>Maret</option>
                                                <option value="4" <?php echo $b4; ?>>April</option>
                                                <option value="5" <?php echo $b5; ?>>Mei</option>
                                                <option value="6" <?php echo $b6; ?>>Juni</option>
                                                <option value="7" <?php echo $b7; ?>>Juli</option>
                                                <option value="8" <?php echo $b8; ?>>Agustus</option>
                                                <option value="9" <?php echo $b9; ?>>September</option>
                                                <option value="10" <?php echo $b10; ?>>Oktober</option>
                                                <option value="11" <?php echo $b11; ?>>November</option>
                                                <option value="12" <?php echo $b12; ?>>Desember</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" name="tahun">
                                                <?php
                                                $s1 = '';
                                                $s2 = '';
                                                $s3 = '';
                                                $s4 = '';
                                                $s5 = '';
                                                $s6 = '';
                                                switch ($v_tahun) {
                                                    case $tahun - 5:
                                                        $s1 = 'selected';
                                                        break;
                                                    case $tahun - 4:
                                                        $s2 = 'selected';
                                                        break;
                                                    case $tahun - 3:
                                                        $s3 = 'selected';
                                                        break;
                                                    case $tahun - 2:
                                                        $s4 = 'selected';
                                                        break;
                                                    case $tahun - 1:
                                                        $s5 = 'selected';
                                                        break;
                                                    case $tahun:
                                                        $s6 = 'selected';
                                                        break;
                                                }
                                                ?>
                                                <option value='<?php echo $tahun - 5; ?>' <?php echo $s1; ?>> <?php echo $tahun - 5 ?> </option>
                                                <option value='<?php echo $tahun - 4; ?>' <?php echo $s2; ?>> <?php echo $tahun - 4 ?> </option>
                                                <option value='<?php echo $tahun - 3; ?>' <?php echo $s3; ?>> <?php echo $tahun - 3 ?> </option>
                                                <option value='<?php echo $tahun - 2; ?>' <?php echo $s4; ?>> <?php echo $tahun - 2 ?> </option>
                                                <option value='<?php echo $tahun - 1; ?>' <?php echo $s5; ?>> <?php echo $tahun - 1 ?> </option>
                                                <option value='<?php echo $tahun; ?>' <?php echo $s6; ?>> <?php echo $tahun ?> </option>
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
                                <h4 class="text-center">LAPORAN DONASI DARAH LENGKAP (WHOLE BLOOD/WB)</h4>
                                <h4 class="text-center"><?php echo $nama_udd; ?></h4>
                                <h4 class="text-center"><?php echo 'BULAN ' . $namaperiode . ' ' . $v_tahun; ?></h4>
                            </div>

                            <div class="col-lg-12">
                                <?php
                                $q_dnr = "SELECT
                                sum(case when (h.`JenisDonor`='0' and h.`tempat`='0' and h.`donorbaru`='0') then 1 else 0 end ) as dg_ds_br,
                                sum(case when (h.`JenisDonor`='0' and h.`tempat`='0' and h.`donorbaru`='1') then 1 else 0 end ) as dg_ds_ul,
                                sum(case when (h.`JenisDonor`='1' and h.`tempat`='0') then 1 else 0 end ) as dg_dp,
                                sum(case when (h.`tempat` in ('2','3','M') and h.`donorbaru`='0') then 1 else 0 end ) as mu_ds_br,
                                sum(case when (h.`tempat` in ('2','3','M') and h.`donorbaru`='1') then 1 else 0 end ) as mu_ds_ul,
                                sum(case when (h.`jk`='0') then 1 else 0 end ) as lk,
                                sum(case when (h.`jk`='1') then 1 else 0 end ) as pr,
                                -- sum(case when (h.`umur`<=17) then 1 else 0 end ) as u17,
                                -- sum(case when (h.`umur`>=18 and h.`umur`<=24) then 1 else 0 end ) as u1824,
                                -- sum(case when (h.`umur`>=25 and h.`umur`<=44) then 1 else 0 end ) as u2544,
                                -- sum(case when (h.`umur`>=45 and h.`umur`<=64) then 1 else 0 end ) as u4564,
                                -- sum(case when (h.`umur`>=65) then 1 else 0 end ) as u65,
                                -- sum(case when (h.`umur`<=17) then 1 else 0 end ) as u17,
                                sum(case when (h.`umur`>=17 and h.`umur`<=20) then 1 else 0 end ) as u1720,
                                sum(case when (h.`umur`>=21 and h.`umur`<=50) then 1 else 0 end ) as u2150,
                                sum(case when (h.`umur`>=51) then 1 else 0 end ) as u51,
                                sum(case when (h.`gol_darah`='A' and h.`rhesus`='+') then 1 else 0 end ) as a_pos,
                                sum(case when (h.`gol_darah`='B' and h.`rhesus`='+') then 1 else 0 end ) as b_pos,
                                sum(case when (h.`gol_darah`='O' and h.`rhesus`='+') then 1 else 0 end ) as o_pos,
                                sum(case when (h.`gol_darah`='AB' and h.`rhesus`='+') then 1 else 0 end ) as ab_pos,
                                sum(case when (h.`gol_darah`='A' and h.`rhesus`='-') then 1 else 0 end ) as a_neg,
                                sum(case when (h.`gol_darah`='B' and h.`rhesus`='-') then 1 else 0 end ) as b_neg,
                                sum(case when (h.`gol_darah`='O' and h.`rhesus`='-') then 1 else 0 end ) as o_neg,
                                sum(case when (h.`gol_darah`='AB' and h.`rhesus`='-') then 1 else 0 end ) as ab_neg,
                                sum(case when (LENGTH(h.`KodePendonor`)>0) then 1 else 0 end ) as total
                                FROM `htransaksi` h inner join `pendonor` p on p.`Kode`=h.`KodePendonor`
                                WHERE
                                year(h.`Tgl`)='$v_tahun' and month(h.`Tgl`)='$v_bulan' and h.`pengambilan`='0' and h.`caraAmbil`='0'";
                                $q_dnr = mysql_fetch_assoc(mysql_query($q_dnr));
                                ?>
                                <h5>A.1.a. DONASI (Jumlah kantong darah yang didapatkan dari pendonor darah)</h5>
                                <div style="overflow-x:auto;">
                                    <table class="table table-bordered table-responsive table-condensed">
                                        <thead class="pmi">
                                            <tr>
                                                <th class="text-center" colspan="4" style="vertical-align: middle;">Jml Donasi Dalam Gedung yg berasal dari</th>
                                                <th class="text-center" colspan="2" style="vertical-align: middle;">Jml Donasi Sukarela dari Kegiatan MU</th>
                                                <th class="text-center" rowspan="3" style="vertical-align: middle;">Jml Total Donasi</th>
                                                <th class="text-center" colspan="2" style="vertical-align: middle;">Jml Donasi Darah Menurut Jenis Kelamin</th>
                                                <th class="text-center" colspan="3" style="vertical-align: middle;">Jml Donasi Darah Menurut Kelompok Umur</th>
                                                <th class="text-center" colspan="8" style="vertical-align: middle;">Jml Donasi Darah Menurut Golongan dan Rhesus Darahr</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" colspan="2" style="vertical-align: middle;">Pendonor Sukarela</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">Pendonor Pengganti</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">Pendonor Bayaran</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">Baru</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">Ulang</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">Laki-laki</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">Wanita</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">17-20 Tahun</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">21-50 Tahun</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;"> <u>></u>50 Tahun </th>
                                                <!-- <th class="text-center" rowspan="2" style="vertical-align: middle;">17 Tahun</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">18-24 Tahun</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">25-44 Tahun</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;">45-64 Tahun</th>
                                                <th class="text-center" rowspan="2" style="vertical-align: middle;"> <u>></u>65 Tahun </th> -->
                                                <th class="text-center" colspan="2" style="vertical-align: middle;">A</th>
                                                <th class="text-center" colspan="2" style="vertical-align: middle;">B</th>
                                                <th class="text-center" colspan="2" style="vertical-align: middle;">O</th>
                                                <th class="text-center" colspan="2" style="vertical-align: middle;">AB</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="vertical-align: middle;">Baru</th>
                                                <th class="text-center" style="vertical-align: middle;">Ulang</th>
                                                <th class="text-center" style="vertical-align: middle;">Pos</th>
                                                <th class="text-center" style="vertical-align: middle;">Neg</th>
                                                <th class="text-center" style="vertical-align: middle;">Pos</th>
                                                <th class="text-center" style="vertical-align: middle;">Neg</th>
                                                <th class="text-center" style="vertical-align: middle;">Pos</th>
                                                <th class="text-center" style="vertical-align: middle;">Neg</th>
                                                <th class="text-center" style="vertical-align: middle;">Pos</th>
                                                <th class="text-center" style="vertical-align: middle;">Neg</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['dg_ds_br']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['dg_ds_ul']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['dg_dp']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br>0<br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['mu_ds_br']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['mu_ds_ul']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['total']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['lk']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['pr']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['u1720']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['u2150']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['u51']; ?><br>&nbsp;</td>
                                                <!-- <td class="text-center">&nbsp;<br><?php echo $q_dnr['u17']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['u1824']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['u2544']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['u4564']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['u65']; ?><br>&nbsp;</td> -->
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['a_pos']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['a_neg']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['b_pos']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['b_neg']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['o_pos']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['o_neg']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['ab_pos']; ?><br>&nbsp;</td>
                                                <td class="text-center">&nbsp;<br><?php echo $q_dnr['ab_neg']; ?><br>&nbsp;</td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div> <!--Overflow table -->
                            </div>
                            <div class="col-lg-8">
                                <?php
                                $q_btl = "SELECT
                                count(case when (h.`pengambilan`='2' and h.`ketBatal`='12') then 1 END) AS a7_gagal_aftap,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='0') then 1 END) AS a4_tensi_rendah,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='1') then 1 END) AS a4_tensi_tinggi,
                                count(case when (h.`pengambilan`='1' and (h.`ketBatal`='2' or h.`ketBatal`='3')) then 1 END) AS a3_hb_rendah,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='4') then 1 END) AS a4_hb_tinggi,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='5') then 1 END) AS a1_bb_kurang,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='6') then 1 END) AS a4_obat,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='7') then 1 END) AS a6_bepergian,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='8') then 1 END) AS a4_medis,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='9') then 1 END) AS a5_prilaku,
                                count(case when ((h.`ketBatal`='10' or h.`ketBatal`='') and h.`pengambilan`='1') then 1 END) AS a7_lain_lain
                                FROM `htransaksi` h inner join `pendonor` p on p.`Kode`=h.`KodePendonor`
                                where year(h.`Tgl`)='$v_tahun' and month(`Tgl`)='$v_bulan' and (h.`caraAmbil` NOT IN ('1','2','3','4','5'))";
                                $q_btl = mysql_fetch_assoc(mysql_query($q_btl));
                                ?>
                                <h5>A.1.b. JUMLAH DONASI BERDASARKAN ALASAN PENDONOR DITOLAK</h5>
                                <table class="table table-bordered table-responsive table-condensed">
                                    <thead class="pmi">
                                        <tr>
                                            <th class="text-center" style="vertical-align: middle;">No</th>
                                            <th class="text-center" style="vertical-align: middle;">Alasan Penolakan</th>
                                            <th class="text-center" style="vertical-align: middle;">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-right">1.</td>
                                            <td class="text-left">Berat Badan Kurang (< 45 Kg)</td>
                                            <td class="text-center"><?php echo $q_btl['a1_bb_kurang']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">2.</td>
                                            <td class="text-left">Usia < 17 tahun</td>
                                            <td class="text-center">0</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">3.</td>
                                            <td class="text-left">Kadar Hb Rendah ( < 12,5 Gr/dl)</td>
                                            <td class="text-center"><?php echo $q_btl['a3_hb_rendah']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">4.</td>
                                            <td class="text-left">Riwayat Medis Lain (Hipertensi, Hipotensi, Minum Obat, Pasca Operasi, Kadar Hb Tinggi > 17 Gr/dl)</td>
                                            <td class="text-center"><?php echo $q_btl['a4_tensi_rendah'] + $q_btl['a4_tensi_tinggi'] + $q_btl['a4_hb_tinggi'] + $q_btl['a4_obat'] + $q_btl['a4_medis']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">5.</td>
                                            <td class="text-left">Perilaku Beresiko Tinggi (Homo Seksual, Tato/Tindik Kurang dari 6 Bulan, Sex Bebas, Penasun, Napi)</td>
                                            <td class="text-center"><?php echo $q_btl['a5_prilaku']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">6.</td>
                                            <td class="text-left">Riwayat Bepergian ( Daerah Endemis Malaria, Negara dengan Kasus HIV Tinggi, Negara Dengan Kasus Sapi Gila)</td>
                                            <td class="text-center"><?php echo $q_btl['a6_bepergian']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">7.</td>
                                            <td class="text-left">Alasan Lain (Gagal pengambilan darah)</td>
                                            <td class="text-center"><?php echo $q_btl['a7_gagal_aftap'] + $q_btl['a7_lain_lain']; ?></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center" colspan="2">Jumlah</th>
                                            <th class="text-center">
                                                <?php echo
                                                $q_btl['a7_gagal_aftap'] +
                                                    $q_btl['a4_tensi_rendah'] +
                                                    $q_btl['a4_tensi_tinggi'] +
                                                    $q_btl['a3_hb_rendah'] +
                                                    $q_btl['a4_hb_tinggi'] +
                                                    $q_btl['a1_bb_kurang'] +
                                                    $q_btl['a4_obat'] +
                                                    $q_btl['a6_bepergian'] +
                                                    $q_btl['a4_medis'] +
                                                    $q_btl['a5_prilaku'] +
                                                    $q_btl['a7_lain_lain'];
                                                ?></th>
                                        </tr>
                                    </tfoot>


                                </table>

                            </div>
                            <div class="col-lg-6">
                                <?php
                                $terima0 = "SELECT DISTINCT t.`udd`, u.`nama`, count(t.`nokantong`) as jumlah
                                FROM `terimaudd` t inner join `utd` u on u.`id`=t.`udd`
                                INNER JOIN `stokkantong` s on s.`noKantong`=t.`nokantong`
                                WHERE YEAR(t.`tgl`)='$v_tahun' and MONTH(t.`Tgl`)='$v_bulan' AND s.`produk` not like '%aph%' ";
                                $terima = mysql_query($terima0);
                                ?>
                                <h5>A.1.c. TERIMA DONASI DARI UDD LAIN</h5>
                                <table class="table table-bordered table-responsive table-condensed">
                                    <thead class="pmi">
                                        <tr>
                                            <th class="text-center" style="vertical-align: middle">No</th>
                                            <th class="text-center" style="vertical-align: middle">Nama UDD</th>
                                            <th class="text-center" style="vertical-align: middle">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $total = 0;
                                        while ($t_udd = mysql_fetch_assoc($terima)) {
                                            $no++;
                                            $total1 = $total + $t_udd['jumlah'];
                                            echo '<tr>';
                                            echo '<td class="text-right">' . $no . '.</td>';
                                            echo '<td class="text-left">' . $t_udd[nama] . '</td>';
                                            echo '<td class="text-center">' . $t_udd[jumlah] . '</td>';
                                            echo '</tr>';
                                        }
                                        if ($no == 0) {
                                            echo '<tr>';
                                            echo '<td class="text-right">1.</td>';
                                            echo '<td class="text-left">-</td>';
                                            echo '<td class="text-center"> </td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td class="text-center">Jumlah</td>
                                            <td class="text-center"> <?php echo $total1; ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="pmitatausaha.php?module=upload&mdl=musnah&t=<?php echo $v_tahun; ?>&b=<?php echo $v_periode; ?>&t1=<?php echo $tanggalawal; ?>&t2=<?php echo $tanggalakhir; ?>" class="btn btn-default" id="shadow2" title="Upload Laporan ke UDD Pusat. Yang dapat diupload adalah periode Bulanan"><i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;Upload ke Pusat</a>
                        <a href="pmitatausaha.php?module=rpt_donasi_wb&thn=<?php echo $v_tahun; ?>&bln=<?php echo $v_bulan; ?>" class="btn btn-default" id="shadow2"><i class="fa fa-print" aria-hidden="true" title="Cetak Laporan"></i>&nbsp;&nbsp;Cetak Laporan</a>
                        <a href="pmitatausaha.php?module=laporan" class="btn btn-default" id="shadow2" title="Kembali ke Menu Laporan"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>