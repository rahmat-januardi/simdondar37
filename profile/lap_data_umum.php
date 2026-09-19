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
    $tahun      = date("Y");
    $v_tahun    = $_POST['tahun'];
    if (empty($v_tahun)) {
        $v_tahun = $tahun;
    }

    $utd            = mysql_fetch_assoc(mysql_query("select * from `utd` where `aktif`='1'"));

    $dt_um          = "SELECT `u_id`, `u_tahun`, `u_id_udd`, `u_nama`, `u_alamat`, `u_kab`, `u_prov`, `u_kpos`, `u_telp`,
                   `u_email`, `u_ka_nama`, `u_ka_hp`, `u_ka_email`,
                   `u_komite_mdk`, `u_distr_terbuka`, `u_distr_cold_chain`,
                   `u_jml_dokter_kompeten`, `u_ptgs_komptn`, `u_inform_c`, `u_lbr_mon_td`, `u_jml_pasien_td`, `u_jml_pasien_rtd`,
                   `u_periksa_kgd`, `u_kgd_auto`, `u_kgd_semi`, `u_kgd_manual`, `u_periksa_abs`, `u_abs_auto`, `u_abs_semi`,
                   `u_abs_manual`, `u_periksa_iab`, `u_iab_auto`, `u_iab_semi`, `u_iab_manual`, `u_periksa_cross`, `u_cross_auto`,
                   `u_cross_semi`, `u_cross_manual`,`u_tgl_laporan` FROM `rpt_data_umum` WHERE `u_id_udd`='$utd[id]' and `u_tahun`='$tahun'";
    $dt_um          = mysql_fetch_assoc(mysql_query($dt_um));


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
                                    <h4 style="text-transform: uppercase;">LAPORAN DATA UMUM</h4>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="panel-title pull-right">
                                    <form class="form-inline" method="POST" action="pmitatausaha.php?module=lap_umum">
                                        <div class="form-group">TAHUN
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
                                                <option value='<?php echo $tahun - 5; ?>' <?php echo $s1; ?>>
                                                    <?php echo $tahun - 5 ?> </option>
                                                <option value='<?php echo $tahun - 4; ?>' <?php echo $s2; ?>>
                                                    <?php echo $tahun - 4 ?> </option>
                                                <option value='<?php echo $tahun - 3; ?>' <?php echo $s3; ?>>
                                                    <?php echo $tahun - 3 ?> </option>
                                                <option value='<?php echo $tahun - 2; ?>' <?php echo $s4; ?>>
                                                    <?php echo $tahun - 2 ?> </option>
                                                <option value='<?php echo $tahun - 1; ?>' <?php echo $s5; ?>>
                                                    <?php echo $tahun - 1 ?> </option>
                                                <option value='<?php echo $tahun; ?>' <?php echo $s6; ?>>
                                                    <?php echo $tahun ?> </option>
                                            </select>
                                        </div>
                                        <button class="btn btn-default" type="submit" id="shadow2"><i
                                                class="fa fa-check mr-1"></i> OK</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <table style="width: 100%;" cellpadding="3" border="0">
                                <tr>
                                    <td class="text-center" colspan="7">
                                        <h4><strong>DATA UMUM</strong></h4>
                                        <h4><strong><?php echo $dt_um['u_nama']; ?></strong></h4>
                                        <h4><strong><?php echo 'TAHUN ' . $v_tahun; ?></strong></h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <h5><strong>A. DATA UMUM</strong></h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;width: 25px;"><strong>I.&nbsp;</strong></td>
                                    <td colspan="6"><strong>Administrasi</strong></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Alamat UDD PMI</td>
                                    <td>:</td>
                                    <td><?php echo $dt_um['u_alamat']; ?></td>
                                    <td>Kode POS</td>
                                    <td>:</td>
                                    <td style="width: 30%;"><?php echo $dt_um['u_kpos']; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Kabupaten/Kota : <?php echo $dt_um['u_kab']; ?></td>
                                    <td>Provinsi</td>
                                    <td>:</td>
                                    <td><?php echo $dt_um['u_prov']; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>No. Telepon</td>
                                    <td>:</td>
                                    <td><?php echo $dt_um['u_telp']; ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Email UDD</td>
                                    <td>:</td>
                                    <td><?php echo $dt_um['u_email']; ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="7">&nbsp;&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;"><strong>II.&nbsp;</strong></td>
                                    <td colspan="6"><strong>Kepala UDD PMI</strong></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td><?php echo $dt_um['u_ka_nama']; ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Ponsel</td>
                                    <td>:</td>
                                    <td><?php echo $dt_um['u_ka_hp']; ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Email</td>
                                    <td>:</td>
                                    <td><?php echo $dt_um['u_ka_email']; ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br><br>
                            <table border="1" width="100%" cellpadding="2" cellspacing="2">
                                <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2" style="vertical-align: middle;">Kepemilikan
                                        </th>
                                        <th class="text-center" rowspan="2" style="vertical-align: middle;">Kelas RS
                                        </th>
                                        <th class="text-center" rowspan="2" style="vertical-align: middle;">Kelas UTD
                                        </th>
                                        <th class="text-center" rowspan="2" style="vertical-align: middle;">Tingkatan
                                        </th>
                                        <th class="text-center" colspan="2" style="vertical-align: middle;">Asal Dana
                                        </th>
                                        <th class="text-center" rowspan="2" style="vertical-align: middle;">
                                            Operasional<br>Sejak<br>Tahun</th>
                                        <th class="text-center" colspan="2" style="vertical-align: middle;">Bantuan
                                            Anggaran</th>
                                        <th class="text-center" rowspan="2" style="vertical-align: middle;">BPPD</th>
                                        <th class="text-center" rowspan="2" style="vertical-align: middle;">Dasar Hukum
                                            BPPD</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="vertical-align: middle;">Bangunan</th>
                                        <th class="text-center" style="vertical-align: middle;">Alat</th>
                                        <th class="text-center" style="vertical-align: middle;">Ya</th>
                                        <th class="text-center" style="vertical-align: middle;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $select_query = "SELECT `b_id`, `b_tahun`, `b_id_udd`, `b_kpemilikan`, `b_klasrs`, `b_klas_udd`, `b_tingkat_udd`,
                                                    `b_dana_bngunan`, `b_dana_alat`, `b_th_operasional`, case when `b_dana_apbd`='0' then 'Tidak' ELSE 'Ya' END as b_dana_apbd, `b_jml_dana_apbd`, `b_bppd`,
                                                    `b_sk_bppd` FROM `rpt_data_bangunan` order by `b_id`";
                                    $result = mysql_query($select_query);
                                    $no = 0;
                                    while ($row = mysql_fetch_array($result)) {
                                        $no++;
                                        if ($row["b_tingkat_udd"] == "Kabupaten") {
                                            $kelas_utd = "Kabupaten/Kota";
                                        } else {
                                            $kelas_utd = $row["b_tingkat_udd"];
                                        }
                                        if ($row["b_kpemilikan"] == "0") {
                                            $milik = "PMI";
                                        } else {
                                            $milik = "Pemerintah";
                                        }
                                        echo '<tr>
                                        <td nowrap class="text-center">&nbsp;<br>' . $milik . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . $row["b_klasrs"] . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . $row["b_klas_udd"] . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . $kelas_utd . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . $row["b_dana_bngunan"] . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . $row["b_dana_alat"] . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . $row["b_th_operasional"] . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . $row["b_dana_apbd"] . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . number_format($row["b_jml_dana_apbd"], 0, ',', '.') . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . number_format($row["b_bppd"], 0, ',', '.') . '<br>&nbsp;</td>
                                        <td nowrap class="text-center">&nbsp;<br>' . $row["b_sk_bppd"] . '<br>&nbsp;</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <p style="font-size: 11px;">* : Khusus untuk UDD di RS<br>
                                ** : Sebutkan semua yang sesuai (APBN/DAK/APBD/sumber lain)</p>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="pmitatausaha.php?module=upload&mdl=umum&t=<?php echo $tahun; ?>"
                            class="btn btn-default" id="shadow2" title="Upload Laporan ke UDD Pusat"><i
                                class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;Upload ke Pusat</a>
                        <a href="pmitatausaha.php?module=rpt_umum&thn=<?php echo $v_tahun; ?>" class="btn btn-default"
                            id="shadow2"><i class="fa fa-print" aria-hidden="true"
                                title="Cetak Laporan"></i>&nbsp;&nbsp;Cetak Laporan</a>
                        <a href="pmitatausaha.php?module=laporan" class="btn btn-default" id="shadow2"><i
                                class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

</html>