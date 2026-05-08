<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include '../adm/config.php';
$utd = mysqli_fetch_array(mysqli_query($con, "SELECT * from utd where `aktif`=1"));
$kodep = $_GET['id'];
$id = $_SESSION['instansi'];
$unit = $_SESSION['unit'];
$user = $_SESSION['user'];
$client_ip = $_SESSION['client_ip'];

if ($unit == "" || $id === "") {
  header("location: ?page=index");
} else {
  //CARI NAMA INSTANSI
  $ins = mysqli_fetch_assoc(mysqli_query($con, "SELECT nama from detailinstansi where KodeDetail='$id'"));
  $namains = $ins['nama'];

  $jd = "select * from pendonor where `Kode`='$kodep'";
  $data = mysqli_fetch_assoc(mysqli_query($con, $jd));

  $today1 = date("Y-m-d H:i:s");
  $today2 = date("Y-m-d");
  $jam_donor = date("H:i:s");
  $tipe_donor = '0';
  if ($data['Jk'] == '0') {
    $kel = "Laki-laki";
  } else {
    $kel = "Perempuan";
  }
  if ($data['Status'] == '0') {
    $nikah = "Belum Menikah";
  } else {
    $nikah = "Sudah Menikah";
  }
  if ($data['Rhesus'] == '-') {
    $rhes = "Negatif";
  } else {
    $rhes = "Positif";
  }

  //Shift Petugas
  $shift = mysqli_fetch_assoc(mysqli_query($con, "SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
  if ($shift['nama'] == "I") {
    $shif = "1";
  } else if ($shift['nama'] == "II") {
    $shif = "2";
  } else if ($shift['nama'] == "III") {
    $shif = "3";
  } else {
    $shif = "4";
  }

  //JIKA POST
  if (isset($_POST['proses'])) {
    $lanjut = '0';
    $error_reason = '';

    // Cek apakah pendonor dicekal
    $jd = "select Cekal from pendonor where `Kode`='$kodep'";
    $cekal_data = mysqli_fetch_assoc(mysqli_query($con, $jd));
    if ($cekal_data['Cekal'] == '1') {
      $msg .= '- Pendaftaran Gagal - Pendonor dicekal<br>';
      $lanjut = '1';
      $error_reason = 'cekal';
    } else {
      //KODE HTRANSAKSI
      $udd1 = mysqli_query($con, "select id from utd where aktif='1'");
      $udd = mysqli_fetch_assoc($udd1);
      $th = substr(date("Y"), 2, 2);
      $bl = date("m");
      $tgl = date("d");
      $kdtp = $unit . $tgl . $bl . $th . "-" . $udd['id'] . "-";
      $idp = mysqli_query($con, "select NoTrans from htransaksi where NoTrans like '$kdtp%' order by NoTrans DESC");
      $idp1 = mysqli_fetch_assoc($idp);
      $idp2 = substr($idp1['NoTrans'], 14, 4);
      if ($idp2 < 1) {
        $idp2 = "0000";
      }
      $idp3 = (int)$idp2 + 1;
      $id31 = strlen($idp2) - strlen($idp3);
      $idp4 = "";
      for ($i = 0; $i < $id31; $i++) {
        $idp4 .= "0";
      }
      $id_transaksi_baru = $kdtp . $idp4 . $idp3;

      //POST DATA
      $v_notransaksi = $id_transaksi_baru;
      $kodep = $_POST['kodep'];
      $ktp = $_POST['ktp'];
      $gol = $_POST['gol'];
      $rh = $_POST['rh'];
      $jmldnr = $_POST['jmldnr'];
      $donorke = $_POST['jmldnr'] + 1;
      $namap = $_POST['namap'];
      $jk = $_POST['jk'];
      $alamat = $_POST['alamat'];
      $kel = $_POST['kel'];
      $kec = $_POST['kec'];
      $wil = $_POST['wil'];
      $telp = $_POST['telp'];
      $tmp_lhr = $_POST['tmp_lhr'];
      $tgl_lhr = $_POST['tgl_lhr'];
      $pekerjaan = $_POST['pekerjaan'];
      $nikah = $_POST['nikah'];
      $jenis_donor = $_POST['jenis_donor'];
      $metode = $_POST['metode'];
      $lengan = $_POST['lengan'];
      $umur = $_POST['umur'];
      $aph = $tpk = "0";
      switch ($_POST['metode']) {
        case '2':
          $aph = 1;
          break;
        case '3':
          $tpk = 1;
          break;
        default:
          break;
      }

      //CARI PENDONOR DI LOKAL & UPDATE
      if ($data['Rhesus'] == 'Neg') {
        $rhesus = '-';
      } else {
        $rhesus = '+';
      }
      $sql_upd = "UPDATE `pendonor` SET
                        `NoKTP`='$ktp',
                        `Nama`='$namap',
                        `Alamat`='$alamat',
                        `Jk`='$jk',
                        `Pekerjaan`='$pekerjaan',
                        `TempatLhr`='$tmp_lhr',
                        `TglLhr`='$tgl_lhr',
                        `Status`='$nikah',
                        `GolDarah`='$gol',
                        `Rhesus`='$rh',
                        `kelurahan`='$kel',
                        `kecamatan`='$kec',
                        `wilayah`='$wil',
                        `telp2`='$telp',
                        `pencatat`='$user',
                        `umur`='$umur',
                        `up_data`='2'
                        WHERE `Kode`='$kodep'";

      if (mysqli_query($con, $sql_upd)) {
        $msg .= '- Update data Pendonor - sukses<br>';
        $log_mdl = 'REGISTRASI';
        $log_aksi = 'Edit pendonor: ' . $kodep . ' - ' . $namap;
        $log = mysqli_query($con, "INSERT INTO `user_log` (`komputer`, `user`, `modul`, `aksi_user`,`tempat`, `keterangan`) VALUES
                            ('$client_ip', '$user', '$log_mdl', '$log_aksi','$unit', '')");
        $lanjut = '0';
      } else {
        $msg .= '- Update data Pendonor - Gagal<br>';
        $lanjut = '1';
        $error_reason = 'update_gagal';
      }

      //insert htransaksi
      if ($lanjut == '0' && date('Y-m-d') >= $data['tglkembali']) {
        $idtrans = substr($id_transaksi_baru, 0, 8);
        $check_p = mysqli_num_rows(mysqli_query($con, "select KodePendonor from htransaksi where NoTrans like '$idtrans%' and KodePendonor='$kodep'"));
        if ($check_p == 0) {
          $q_htrans = "insert into htransaksi
                                (NoTrans,KodePendonor,KodePendonor_lama,Tgl,Pengambilan,ketBatal,tempat,Instansi,
                                JenisDonor,id_permintaan,Status,Nopol,apheresis,kendaraan,shift,kota,umur,donorbaru,jk,
                                gol_darah,rhesus,pekerjaan,donorke,user,jam_mulai,rs, donor_tpk)
                                value
                                ('$id_transaksi_baru','$kodep','$kodep','$today1','-','-','M','$namains',
                                 '$jenis_donor','','0','-','$aph','','$shif','$udd[id]','$umur','1','$jk',
                                 '$gol','$rh','$pekerjaan','$donorke','$user','$jam_donor','','$tpk')";
          if (mysqli_query($con, $q_htrans)) {
            $msg .= '- Pendaftaran - berhasil<br>';
            $lanjut = '0';
          } else {
            $msg .= '- Pendaftaran - GAGAL<br>';
            $lanjut = '1';
            $error_reason = 'pendaftaran_gagal';
          }
        }
      } else if ($lanjut == '0') {
        $lanjut = '1';
        $error_reason = 'tanggal';
      }
    }

    if ($lanjut == "0") {
      echo $msg . "- Silahkan Lanjutkan Medical CheckUp";
?>
      <META http-equiv="refresh" content="5; url=?page=mcu&id=<?php echo $kodep; ?>&NoTrans=<?php echo $id_transaksi_baru; ?>">
    <?php
    } else {
      if ($error_reason == 'cekal') {
        echo $msg . "- Pendonor Dicekal";
      } elseif ($error_reason == 'tanggal') {
        echo $msg . "- Belum Saatnya Donor";
      } else {
        echo $msg . "- Gagal Mendaftar";
      }
    ?>
      <META http-equiv="refresh" content="5; url=?page=dash">
  <?php
    }
  }

  //SINKRON AYODONOR
  if (isset($_POST['sinkron'])) {
    $kodep = $_POST['kodep'];
    $ktp = $_POST['ktp'];
    $gol = $_POST['gol'];
    $rh = $_POST['rh'];
    $jmldnr = $_POST['jmldnr'];
    $donorke = $_POST['jmldnr'] + 1;
    $namap = $_POST['namap'];
    $jk = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $kel = $_POST['kel'];
    $kec = $_POST['kec'];
    $wil = $_POST['wil'];
    $telp = $_POST['telp'];
    $tmp_lhr = $_POST['tmp_lhr'];
    $tgl_lhr = $_POST['tgl_lhr'];
    $pekerjaan = $_POST['pekerjaan'];
    $nikah = $_POST['nikah'];
    $jenis_donor = $_POST['jenis_donor'];
    $metode = $_POST['metode'];
    $lengan = $_POST['lengan'];
    $umur = $_POST['umur'];
    $aph = $tpk = "0";
    switch ($_POST['metode']) {
      case '2':
        $aph = 1;
        break;
      case '3':
        $tpk = 1;
        break;
      default:
        break;
    }

    //CARI PENDONOR DI LOKAL & UPDATE
    if ($data['Rhesus'] == 'Neg') {
      $rhesus = '-';
    } else {
      $rhesus = '+';
    }
    $sql_upd = "UPDATE `pendonor` SET
                    `NoKTP`='$ktp',
                    `Nama`='$namap',
                    `Alamat`='$alamat',
                    `Jk`='$jk',
                    `Pekerjaan`='$pekerjaan',
                    `TempatLhr`='$tmp_lhr',
                    `TglLhr`='$tgl_lhr',
                    `Status`='$nikah',
                    `GolDarah`='$gol',
                    `Rhesus`='$rh',
                    `kelurahan`='$kel',
                    `kecamatan`='$kec',
                    `wilayah`='$wil',
                    `telp2`='$telp',
                    `pencatat`='$user',
                    `umur`='$umur',
                    `up_data`='2'
                    WHERE `Kode`='$kodep'";

    if (mysqli_query($con, $sql_upd)) {
      $msg .= '- Update data Pendonor - sukses<br>';
      $log_mdl = 'REGISTRASI';
      $log_aksi = 'Sinkron Data Pendonor Nasional : ' . $kodep . ' - ' . $namap;
      $log = mysqli_query($con, "INSERT INTO `user_log` (`komputer`, `user`, `modul`, `aksi_user`,`tempat`, `keterangan`) VALUES
                                ('$client_ip', '$user', '$log_mdl', '$log_aksi','$unit', '')");
      $lanjut = '0';
    } else {
      $msg .= '- Update data Pendonor - Gagal<br>';
      $lanjut = '1';
    }

    if ($lanjut == "0") {
      header("location: ?page=sinkron&Kode=$kodep");
    } else {
      header("location: ?page=dash");
    }
  }
  // AYODONOR
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMDONDAR</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style type="text/css" title="currentStyle">
      @import "./../css/dt_page.css";
      @import "./../css/dt_table.css";
      @import "./../css/dt_table_jui.css";
    </style>
    <link type="text/css" href="../../css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
    <link type="text/css" href="./../css/TableTools_JUI.css" rel="stylesheet" />
    <script type="text/javascript" language="javascript" src="./../js/jquery-1.5.2.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="./../js/jquery-ui-1.8.9.custom.min.js"></script>
    <script type="text/javascript" language="javascript" src="./../js/jquery.dataTables.js"></script>
  </head>
  <style>
    .body {
      font-size: 12px;
    }

    .padding {
      background-image: url('dist/img/white.jpg');
      background-size: cover;
    }

    .box {
      height: 25px;
      padding: 20px;
    }

    .box2 {
      height: 25px;
      padding: 20px;
    }

    .box3 {
      height: 100px;
    }

    .copyright {
      bottom: 0;
      width: 100%;
      position: fixed;
      height: 40px;
      line-height: 50px;
      background: RED;
      color: #fff;
      padding-left: 10px;
    }

    .input-tanggal {
      padding: 10px;
      font-size: 14pt;
    }
  </style>

  <body class="padding">
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="dist/img/logo.png" alt="AdminLTELogo" height="60" width="60">
    </div>
    <p>
    <div class="card-header">
      <h4 class="text-center" style="font-size:24px; font-weight:bold;color:#ff0000;text-shadow: 1px 1px 1px #000000; font-family:Helvetica, Arial, san-serif;">EDIT DATA PENDONOR<br><?php echo $ins['nama']; ?></h4>
      <a href="?page=dash"><button name="baru" class="btn btn-info float-right"><i class="nav-icon ion ion-android-arrow-back"></i> Kembali</button></a>
    </div>
    <div class="col-12 col-sm-12">
      <div class="card-body">
        <form action="" method="post">
          <div class="row">
            <div class="col-lg-6">
              <div class="table-responsive">
                <table width=100%>
                  <tr>
                    <td>Kode Pendonor</td>
                    <td class="warning"><input type="text" name="kodep" class="form-control" value=<?php echo $data['Kode']; ?> readonly></td>
                  </tr>
                  <tr>
                    <td>No. KTP</td>
                    <td class="warning"><input type="text" name="ktp" class="form-control" value=<?php echo $data['NoKTP']; ?>>
                      <input type="hidden" name="gol" value=<?php echo $data['GolDarah']; ?>>
                      <input type="hidden" name="rh" value=<?php echo $data['Rhesus']; ?>>
                      <input type="hidden" name="jmldnr" value=<?php echo $data['jumDonor']; ?>>
                      <input type="hidden" name="umur" value=<?php echo $data['umur']; ?>>
                    </td>
                  </tr>
                  <tr>
                    <td>Nama Pendonor</td>
                    <td class="warning text-danger"><strong><input type="text" name="namap" class="form-control" value="<?php echo $data['Nama']; ?>"></strong></td>
                  </tr>
                  <tr>
                    <td>Jenis Kelamin</td>
                    <td class="warning">
                      <?php
                      $type = $data['Jk'];
                      $checked[$type] = "checked";
                      ?>
                      <input type="radio" name="jk" value="0" <?= $checked["0"] ?>>Laki-laki &nbsp;
                      <input type="radio" name="jk" value="1" <?= $checked["1"] ?>>Perempuan
                    </td>
                  </tr>
                  <tr>
                    <td>Alamat</td>
                    <td class="warning"><input type="text" name="alamat" class="form-control" value="<?php echo $data['Alamat']; ?>"></td>
                  </tr>
                  <tr>
                    <td>Kelurahan</td>
                    <td class="warning"><input type="text" name="kel" class="form-control" value=<?php echo $data['kelurahan']; ?>></td>
                  </tr>
                  <tr>
                    <td>Kecamatan</td>
                    <td class="warning"><input type="text" name="kec" class="form-control" value=<?php echo $data['kecamatan']; ?>></td>
                  </tr>
                  <tr>
                    <td>Wilayah</td>
                    <td class="warning"><input type="text" name="wil" class="form-control" value=<?php echo $data['wilayah']; ?>></td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="table-responsive">
                <table width=100%>
                  <tr>
                    <td>No. Handphone</td>
                    <td class="warning"><input type="text" name="telp" class="form-control" value="<?php echo $data['telp2']; ?>"></td>
                  </tr>
                  <tr>
                    <td>Tempat Lahir</td>
                    <td class="warning"><input type="text" name="tmp_lhr" class="form-control" value="<?php echo $data['TempatLhr']; ?>"></td>
                  </tr>
                  <tr>
                    <td>Tanggal Lahir</td>
                    <td class="warning"><input type="text" name="tgl_lhr" id="datepicker" style="width:4cm;height:0.75cm" class="form-control" value="<?php echo $data['TglLhr']; ?>"></td>
                  </tr>
                  <tr>
                    <td>Pekerjaan</td>
                    <td class="warning">
                      <select name="pekerjaan" class="form-control">
                        <?php
                        $q = "select * from pekerjaan";
                        $do = mysqli_query($con, $q);
                        $select = "";
                        while ($datap = mysqli_fetch_assoc($do)) {
                          if ($datap['Nama'] == $data['Pekerjaan']) $select = 'selected';
                        ?>
                          <option value="<?= $datap['Nama'] ?>" <?php echo $select ?>><?php echo $datap['Nama'] ?></option>
                        <?php
                          $select = "";
                        } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Status Nikah</td>
                    <td class="warning">
                      <?php
                      $type = $data['Status'];
                      $checked["0"] = '';
                      $checked["1"] = '';
                      $checked[$type] = "checked";
                      ?>
                      <input type="radio" name="nikah" value="0" <?= $checked["0"] ?>>Belum Nikah &nbsp;
                      <input type="radio" name="nikah" value="1" <?= $checked["1"] ?>>Nikah
                    </td>
                  </tr>
                  <tr>
                    <td>Jenis Donor</td>
                    <td class="warning">
                      <select name="jenis_donor" class="form-control">
                        <option value="0">Sukarela</option>
                        <option value="1">Pengganti</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Metode Donor</td>
                    <td class="warning">
                      <select name="metode" class="form-control">
                        <option value="1">Donor Biasa</option>
                        <option value="2">Donor Apheresis</option>
                        <option value="3">Donor Plasma Konvalesen</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Pilih Lengan Donor</td>
                    <td class="warning">
                      <select name="lengan" class="form-control">
                        <option value="0">Keduanya</option>
                        <option value="1">Kiri</option>
                        <option value="2">Kanan</option>
                      </select>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="col-lg-12">
              <center>
                <p>
                <div class="row">
                  <div class="col-lg-6" align="right"></div>
                  <div class="col-lg-6" align="right">
                    <button name="sinkron" type="submit" class="btn btn-warning" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 4px 8px 0 rgba(0, 0, 0, 0.19); height:50px;width:100px">SINKRON</button>
                    <a href="?page=caridonor"><input type="button" value="BATALKAN" class="btn btn-danger btn-sm" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 4px 8px 0 rgba(0, 0, 0, 0.19); height:50px;width:100px"></a>
                    <button name="proses" type="submit" class="btn btn-success" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 4px 8px 0 rgba(0, 0, 0, 0.19); height:50px;width:100px">LANJUT</button>
                  </div>
                </div>
              </center>
            </div>
          </div>
        </form>
      </div>
    </div>
    <p class="box3">
    <div class="copyright">
      <p align="center"><a href="https://pmi.or.id">
          <font style="color:white">Copyright @ 2022 | PALANG MERAH INDONESIA</font>
        </a>
    </div>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
      $(function() {
        $("#datepicker").datepicker({
          dateFormat: "yy-mm-dd",
        });
      });
    </script>
    <script type="text/javascript">
      function validasiregistrasi() {
        if (document.getElementById('iddonor').value == '') {
          if (document.getElementById('nama').value.length < 3) {
            alert('Pencarian Nama harus diisi/minimal 3 Karakter!');
            return false;
          }
        }
      }
    </script>
    <script>
      $(function() {
        $('#reservationdate').datetimepicker({
          format: 'yyyy-MM-DD'
        });
        $('#reservationdate2').datetimepicker({
          format: 'yyyy-MM-DD'
        });
        $('#reservationdatetime').datetimepicker({
          icons: {
            time: 'far fa-clock'
          }
        });
        $('#reservation').daterangepicker();
        $('#reservationtime').daterangepicker({
          timePicker: true,
          timePickerIncrement: 30,
          locale: {
            format: 'MM/DD/YYYY hh:mm A'
          }
        });
        $('#daterange-btn').daterangepicker({
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        }, function(start, end) {
          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        });
        $("#datepicker2").datepicker({
          dateFormat: "yy-mm-dd",
        });
      });
    </script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script src="../tpksoloplugins/jquery/jquery.min.js"></script>
  </body>

  </html>
<?php } ?>