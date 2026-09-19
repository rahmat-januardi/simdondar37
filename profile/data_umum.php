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
    require_once('config/db_connect.php');
    session_start();
    if (isset($_POST['submit'])) {
        $ada_data = $_POST['ada_data'];
        $name = $_POST['udd'];
        $tahun = date("Y");
        //echo "$tahun<br>";

        //echo "<b> $name </b>";
        if ($ada_data == '1') {
            echo "<br>Update Data. <br>";
            $q_umm = "UPDATE `rpt_data_umum` SET
                `u_tahun` = '$tahun',
                `u_id_udd` = '$_POST[id_udd]',
                `u_nama` =  '$_POST[udd]',
                `u_alamat` = '$_POST[alamat]',
                `u_kab`	= '$_POST[kab]',
                `u_prov` = '$_POST[prop]',
                `u_kpos` = '$_POST[kpos]',
                `u_telp` ='$_POST[telp]',
                `u_email` ='$_POST[email]',
                `u_ka_nama` = '$_POST[ka_utd]',
                `u_ka_hp` = '$_POST[ka_telp]',
                `u_ka_email` = '$_POST[ka_email]'
                WHERE
                `u_id`='$_POST[id_edit]'";
            //echo "<br>$q_umm";
            $q_umm = mysql_query($q_umm);

            // Update Nama UDD berdasarkan data umum
            $q_upd_utd = mysql_query("UPDATE `utd` SET `nama`= '$_POST[udd]' WHERE aktif=1");
            if ($q_upd_utd) {
                echo "update nama udd berhasil<br>";
            } else {
                mysql_error();
            }

            if ($q_umm) {
                echo "Update Sukses<br>";
            } else {
                echo "Update Error<br>";
            }
        } else {
            echo "<br>Tambah Data.";
            $q_umm  = "INSERT INTO `rpt_data_umum`(`u_tahun`, `u_id_udd`, `u_nama`, `u_alamat`, `u_kab`, `u_prov`, `u_kpos`, `u_telp`, `u_email`,
                  `u_ka_nama`, `u_ka_hp`, `u_ka_email`)
                  VALUES ('$tahun', '$_POST[id_udd]','$_POST[udd]','$_POST[alamat]','$_POST[kab]','$_POST[prop]', '$_POST[kpos]','$_POST[telp]','$_POST[email]',
                  '$_POST[ka_utd]', '$_POST[ka_telp]', '$_POST[ka_email]')";
            //echo "<br>$q_umm";
            $q_umm = mysql_query($q_umm);

            // Update Nama UDD berdasarkan data umum
            $q_upd_utd = mysql_query("UPDATE `utd` SET `nama`= '$_POST[udd]' WHERE aktif=1");
            if ($q_upd_utd) {
                echo "update nama udd berhasil<br>";
            } else {
                mysql_error();
            }

            if ($q_umm) {
                echo "Insert Sukses<br>";
            } else {
                echo "Error Insert<br>";
            }
        }
    ?>
    <META http-equiv="refresh" content="0;URL=pmitatausaha.php?module=dataumum">
    <?php
    } else {
        $tahun = date('Y');
        $udd = "select * from utd where aktif='1'";
        $udd = mysql_fetch_assoc(mysql_query($udd));
        $qlap = "SELECT * FROM `rpt_data_umum` WHERE `u_id_udd`='$udd[id]' and `u_tahun`='$tahun'";
        $qlap = mysql_fetch_assoc(mysql_query($qlap));
        if ($qlap['u_id_udd'] == $udd['id']) {
            $nama_udd = $qlap['u_nama'];
            $alamat_udd = $qlap['u_alamat'];
            $id_udd = $qlap['u_id_udd'];
            $ada_data = '1';
        } else {
            $ada_data = '0';
            $nama_udd = $udd['nama'];
            $alamat_udd = $udd['alamat'];
            $id_udd = $udd['id'];
        }
    ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <br>
                <form class="form-horizontal" method="post" action="pmitatausaha.php?module=dataumum">
                    <div class="panel with-nav-tabs panel-primary" id="shadow1">
                        <div class="panel-heading">
                            <h4>DATA UMUM</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="udd">UTD/UDD</label>
                                        <div class="col-sm-10">
                                            <strong><input type="text" class="form-control input-sm panel-custom-red"
                                                    id="udd" name="udd" required
                                                    value="<?php echo $nama_udd; ?>"></strong>
                                            <input type="hidden" value="<?php echo $ada_data; ?>" name="ada_data">
                                            <input type="hidden" value="<?php echo $id_udd; ?>" name="id_udd">
                                            <input type="hidden" value="<?php echo $qlap['u_id']; ?>" name="id_edit">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="alamat">Alamat</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control input-sm" id="alamat"
                                                placeholder="Alamat" name="alamat" required
                                                value="<?php echo $alamat_udd; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="kota">Kab/Kota</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control input-sm" id="kota"
                                                placeholder="kota" name="kab" value="<?php echo $qlap['u_kab']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="alamat">Provinsi</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control input-sm" id="prop"
                                                placeholder="Provinsi" name="prop"
                                                value="<?php echo $qlap['u_prov']; ?>">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="kodepos">Kd Pos</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control input-sm" id="kpos" name="kpos"
                                                value="<?php echo $qlap['u_kpos']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="telp">Telp</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control input-sm" id="telp" name="telp"
                                                value="<?php echo $qlap['u_telp']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="email">E-mail</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control input-sm" id="email"
                                                placeholder="email@email.com" name="email"
                                                value="<?php echo $qlap['u_email']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="judul"> Data Pimpinan</p>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="udd">Nama</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control input-sm" id="ka_utd" name="ka_utd"
                                                value="<?php echo $qlap['u_ka_nama']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="ka_telp">Telp/HP</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-sm" id="ka_telp"
                                                placeholder="No Telp/HP Ka UTD" name="ka_telp"
                                                value="<?php echo $qlap['u_ka_hp']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="ka_email">Email</label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control input-sm" id="ka_email"
                                                placeholder="email@email.com" name="ka_email"
                                                value="<?php echo $qlap['u_ka_email']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" name="submit" class="btn btn-default" id="shadow2"><i
                                    class="fa fa-save" aria-hidden="true"></i>&nbsp;&nbsp;Simpan Data</button>
                            <a href="pmitatausaha.php?module=profile" class="btn btn-default" id="shadow2"><i
                                    class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>
</body>

</html>

<script>
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
</script>