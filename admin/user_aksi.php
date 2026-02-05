<head>
    <link href="bootsrap337/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootsrap337/js/html5shiv.min.js"></script>
    <script src="bootsrap337/js/respond.min.js"></script>
    <link href="bootsrap337/bspmi.css" rel="stylesheet">
    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="bootsrap337/chosen/chosen.css">
    <style>
        .shadow {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .shadow-xx {
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.19);
        }

        .form-group {
            margin-top: 1px;
            margin-bottom: 1px;
        }
    </style>
    <?php
    include "config/dbi_connect.php";
    if (isset($_POST['submit'])) {
        $v_tgl = date("Y-m-d");
        $v_mode = $_POST['mode'];
        $v_iduser = strtoupper($_POST['id_user']);
        $v_nama = strtoupper($_POST['nama_lengkap']);
        $pwd = $_POST['password'];
        if (!$pwd == "") {
            $v_pwd = md5($pwd);
        }
        $v_email = $_POST['email'];
        $v_level = $_POST['leveluser'];
        $v_multi_level = '';
        if (!empty($_POST['multilevel'])) {
            foreach ($_POST['multilevel'] as $selectedOption)
                $v_multi_level .= $selectedOption . ",";
        }
        $v_multi_level = substr($v_multi_level, 0, -1);
        $v_bagian = $_POST['bagian'];
        $v_jabatan = $_POST['jabatan'];
        $v_telp = $_POST['telp'];
        $v_multi_bagian = '';
        if (!empty($_POST['multibagian'])) {
            foreach ($_POST['multibagian'] as $selectedOption)
                $v_multi_bagian .= $selectedOption . ",";
        }
        $v_multi_bagian = substr($v_multi_bagian, 0, -1);
        if ($v_mode == 'edituser') {
            $v_aktif = $_POST['status'];
            if ($pwd == "") {
                $text_pwd = "Password tidak dirubah";
                $sql = "UPDATE `user` SET 
                        `nama_lengkap`='$v_nama',
                        `email`='$v_email',
                        `level`='$v_level',
                        `multi`='$v_multi_level',
                        `telp`='$v_telp',
                        `bagian`='$v_bagian',
                        `jabatan`='$v_jabatan',
                        `aktif`='$v_aktif',
                        `multi_bagian`='$v_multi_bagian' 
                        WHERE 
                        `id_user`='$v_iduser'";
            } else {
                $text_pwd = "Password dirubah";
                $sql = "UPDATE `user` SET 
                        `password`='$v_pwd',
                        `tglpwd`='$v_tgl',
                        `nama_lengkap`='$v_nama',
                        `email`='$v_email',
                        `level`='$v_level',
                        `multi`='$v_multi_level',
                        `telp`='$v_telp',
                        `bagian`='$v_bagian',
                        `jabatan`='$v_jabatan',
                        `aktif`='$v_aktif',
                        `multi_bagian`='$v_multi_bagian' 
                        WHERE 
                        `id_user`='$v_iduser'";
            }
            $upd = mysqli_query($dbi, $sql);
            if ($upd) {
                echo '<div class="alert alert-success" role="alert">Update user <strong>berhasil</strong> ' . $text_pwd . '</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Upate user <strong>TIDAK</srong> berhasil</div>';
            }
        } else {
            $sql = "INSERT INTO `user`(`id_user`, `password`, `tglpwd`, `nama_lengkap`, `email`, `level`, `multi`, `telp`,  `bagian`, `jabatan`, `multi_bagian`) 
                VALUES ('$v_iduser','$v_pwd','$v_tgl','$v_nama','$v_email','$v_level','$v_multi_level','$telp','$v_bagian','$v_jabatan','$v_multi_bagian')";
            $qins = mysqli_query($dbi, $sql);
            if ($qins) {
                echo '<div class="alert alert-success" role="alert">Penambahan  user baru <strong>berhasil</strong> dilakukan</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert"><strong>GAGAL</srong> menambahkan user baru</div>';
            }
        }
        echo '<META http-equiv="refresh" content="1; url=pmiadmin.php?module=aturuser">';
    }
    $g_act      = $_GET['act'];
    $g_iduser   = $_GET['id'];
    $title = "Tambah User";
    $msg_pwd = "";
    $pwd_reqiered = "required";
    $username = $nama = $pwd = $email = $level = $telp = $bagian = $jabatan = $v_readonly = $multibagian = $multilevel = "";
    if ($g_act == "edituser") {
        $pwd_reqiered = "";
        $v_readonly = "readonly";
        $title = "Edit User";
        $msg_pwd = "Kosongkan apabila password tidak dirubah";
        $usr = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `user`.`id_user`,
            `user`.`aktif`,`user`.`password`,`user`.`tglpwd`,`user`.`nama_lengkap`,`user`.`email`,`user`.`level`,
            `user`.`multi`,`user`.`telp`,`user`.`sms_forward`,`user`.`bagian`,`user`.`jabatan`,`user`.`tgllahir`,`user`.`up_data`,
            `user`.`insert_on`,`user`.`update_on`,`user`.`status_user`,`user`.`usr_level`,`user`.`multi_bagian`
            FROM `pmi`.`user`
            WHERE `id_user`='$g_iduser' "));
        $username = $usr['id_user'];
        $nama = $usr['nama_lengkap'];
        $email = $usr['email'];
        $level = $usr['level'];
        $telp = $usr['telp'];
        $bagian = $usr['bagian'];
        $jabatan = $usr['jabatan'];
        $multi_bagian = explode(",", $usr['multi_bagian']);
        $multi_level = explode(",", $usr['multi']);
        $aktif = $usr['aktif'];
    }
    ?>
</head>

<body>
    <div class="container-fluid" style="margin-left: 20px;margin-right:20px;margin-top:20px;">
        <div class="row">
            <div class="col-xs-10 col-md-8">
                <div class="panel panel-primary shadow">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo $title; ?></h4>
                    </div>
                    <form method="POST" action="" class="form-horizontal">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="id_user">ID:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control input-sm" id="id_user" name="id_user" <?php echo $v_readonly; ?> value="<?php echo $username; ?>" required>
                                            <input type="hidden" name="mode" value="<?php echo $g_act; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="nama_lengkap">Nama:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control input-sm" id="nama_lengkap" name="nama_lengkap" value="<?php echo $nama; ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="password">Password:</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control input-sm" id="password" name="password" <?php echo $pwd_reqiered; ?>><small class="text-danger"><?php echo $msg_pwd; ?></small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="email">Email:</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control input-sm" id="email" name="email" value="<?php echo $email; ?>">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="telp">Telp/HP:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control input-sm" id="telp" name="telp" value="<?php echo $telp; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="jabatan">Jabatan:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control input-sm" id="jabatan" name="jabatan" value="<?php echo $jabatan; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="bagian">Bagian</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control input-sm" id="bagian" name="bagian" value="<?php echo $bagian; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="leveluser">Level:</label>
                                        <div class="col-sm-10">
                                            <select data-placeholder="Piih salah satu bagian" class="chosen-select form-control" tabindex="8" name="leveluser">
                                                <?php
                                                $tampil = mysqli_query($dbi, "SELECT * FROM `level` order by `urutan` asc");
                                                while ($r = mysqli_fetch_array($tampil)) {
                                                    if ($level == $r['level']) {
                                                        echo '<option value="' . $r['level'] . '" selected>' . $r['level'] . '</option>';
                                                    } else {
                                                        echo '<option value="' . $r['level'] . '">' . $r['level'] . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                    if ($g_act == "edituser") {
                                        $rg1 = $rg2 = "";
                                        if ($aktif == "0") {
                                            $rg1 = " checked ";
                                        } else {
                                            $rg2 = " checked ";
                                        }
                                        echo '
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="bagian">Status</label>
                                            <div class="col-sm-10">
                                                <label class="radio-inline"><input type="radio" name="status" value="0" ' . $rg1 . '>Aktif</label>
                                                <label class="radio-inline"><input type="radio" name="status" value="1" ' . $rg2 . '>Tidak Aktif</label>
                                            </div>
                                        </div>';
                                    }
                                    ?>

                                </div>
                            </div>
                            <hr>
                            <!-- Multilevel -->
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="leveluser">Multilevel:</label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <?php
                                        $tampil = mysqli_query($dbi, "SELECT * FROM `level` ORDER BY `urutan` ASC");
                                        $count = 0;
                                        while ($r = mysqli_fetch_array($tampil)) {
                                            $checked = in_array($r['level'], $multi_level) ? 'checked' : '';
                                            echo '<div class="col-sm-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="multilevel[]" value="' . $r['level'] . '" ' . $checked . '> ' . $r['level'] . '
                            </label>
                          </div>
                      </div>';
                                            $count++;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <hr />

                            <!-- Multi Bagian -->
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="multibagian">Multi Bagian:</label>
                                <div class="col-sm-10">

                                    <div class="row" style="max-height: 300px; overflow: hidden;">
                                        <?php
                                        // $tampil = mysqli_query($dbi, "SELECT * FROM `level` ORDER BY `urutan` ASC");
                                        $tampil = mysqli_query($dbi, "SELECT * FROM `level` WHERE `level`='AFTAP' ");
                                        $count = 0;
                                        while ($r = mysqli_fetch_array($tampil)) {
                                            $checked = in_array($r['level'], $multi_bagian) ? 'checked' : '';
                                            echo '<div class="col-sm-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="multibagian[]" value="' . $r['level'] . '" ' . $checked . '> ' . $r['level'] . '
                            </label>
                          </div>
                      </div>';
                                            $count++;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-center">
                                <input type="submit" value="Simpan" class="btn btn-success shadow" name="submit">
                                <a href="pmiadmin.php?module=aturuser" class="btn btn-warning shadow">Kembali</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="bootsrap337/chosen/docsupport/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="bootsrap337/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="bootsrap337/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
<script src="bootsrap337/chosen/docsupport/init.js" type="text/javascript" charset="utf-8"></script>