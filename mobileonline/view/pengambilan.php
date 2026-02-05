<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include '../adm/config.php';
$utd = mysqli_fetch_array(mysqli_query($con, "SELECT * from utd where `aktif`=1"));
$kodep = $_GET['id'];
$notrans = $_GET['NoTrans'];
$id = $_SESSION['instansi'];
$unit = $_SESSION['unit'];
$user = $_SESSION['user'];
$client_ip = $_SESSION['client_ip'];
$kodependonor = $_GET['kodep'];
if ($unit == "" || $id === "") {
  header("location: ?page=index");
} else {


  //CARI NAMA INSTANSI
  $ins = mysqli_fetch_assoc(mysqli_query($con, "SELECT nama from detailinstansi where KodeDetail='$id'"));
  $namains = $ins['nama'];


  //Shift Petugas
  $shift  = mysqli_fetch_assoc(mysqli_query($con, "SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
  //$shif   = $shift['nama'];
  if ($shift['nama'] == "I") {
    $shif   = "1";
  } else if ($shift['nama'] == "II") {
    $shif   = "2";
  } else if ($shift['nama'] == "III") {
    $shif   = "3";
  } else {
    $shif   = "4";
  }
  $today1 = date("Y-m-d H:i:s");
  $today2 = date("Y-m-d");
  $jam_donor = date("H:i:s");
  $tipe_donor = '0';

  $check  = mysqli_query($con, "select * from pmi.htransaksi where (NoTrans='$_GET[NoTrans]')");
  $check1 = mysqli_fetch_assoc($check);

  $check1[KodePendonor]   = str_replace("'", "\'", $check1['KodePendonor']);
  $data   = mysqli_query($con, "select Nama,GolDarah,Rhesus from pendonor where Kode='$check1[KodePendonor]'");
  $data1  = mysqli_fetch_array($data);

  if (isset($_POST['simpan'])) {
    $keberhasilan   = $_POST['keberhasilan'];
    $catatan        = $_POST['catatan'];
    $today1          = date("Y-m-d");

    $kdl            = mktime(0, 0, 0, date("m"), date("d") + 14, date("Y"));
    $kembali0       = mktime(0, 0, 0, date("m"), date("d") + 60, date("Y"));
    $tensi          = $_POST['tensi_diastol'] . "/" . $_POST['tensi_sistol'];
    $status_test    = "1";
    $today          = date('Y-m-d H:i:s');
    $kembali        = date('Y-m-d', $kembali0);
    $kadaluwarsa    = date('Y-m-d H:i:s', $kdl);
    $kodependonor   = $_POST['kodependonor'];
    $volume_darah   = $_POST['volume_darah'];
    $catatan        = $_POST['catatan'];
    $reaksi         = $_POST['reaksi'];
    $caraambil      = $_POST['caraambil'];
    $id_kantong     = $_POST['id_kantong11'];
    $no_selang      = $_POST['no_selang'];
    $GolDarah       = $_POST['goldarah'];
    $Rhesus         = $_POST['Rhesus'];
    $ambil3         = $_POST['ambil'];
    $selesai3       = $_POST['selesai'];
    $petugas        = $_POST['petugas'];
    $tglp1          = $today1 . ' ' . $ambil3;

    $ambil2         = str_replace(";", ":", $ambil3);
    $ambil1         = str_replace(",", ":", $ambil2);
    $ambil          = str_replace(".", ":", $ambil1);

    $selesai2       = str_replace(";", ":", $selesai3);
    $selesai1       = str_replace(",", ":", $selesai2);
    $selesai        = str_replace(".", ":", $selesai1);
    $mu = "1";



    //interval AFTAP-----------
    $jama = $ambil;
    $jamb = $selesai;
    $test1 = substr($jama, 0, 2);
    $test2 = substr($jama, 3, 2);
    $test3 = substr($jama, 6);
    $test4 = substr($jamb, 0, 2);
    $test5 = substr($jamb, 3, 2);
    $test6 = substr($jamb, 6);
    $waktua = mktime($test1, $test2, $test3);
    $waktub = mktime($test4, $test5, $test6);
    $selisih = $waktub - $waktua;
    $sisa = $selisih % 86400;
    $jam = floor($sisa / 3600);
    $sisa = $sisa % 3600;
    $menit = floor($sisa / 60);

    $lama_pengambilan = $menit;
    $tglp1 = $today1 . ' ' . $ambil;

    $lastDigit = substr($id_kantong, -1);
    if ($lastDigit == 'A' or $lastDigit == 'a') {

      //echo $jam.'-'.$menit;
      //jika batal/gagal
      if ($keberhasilan != "0") { //simpan gagal
        $noKantong  = trim(mysqli_real_escape_string($con, $_POST['id_kantong11']));
        $kantong    = mysqli_query($con, "SELECT * from stokkantong where noKantong ='$noKantong' AND `Status`='0' and StatTempat='1' and kadaluwarsa_ktg >'$today1'");
        $stok1      = mysqli_fetch_array($kantong);
        $numkantong = mysqli_num_rows($kantong);

        //jika kantong ada
        if ($numkantong > 0) { //simpan Gagal
          $pendonor   = mysqli_query($con, "select * from pendonor where Kode='$kodependonor' ");
          $pendonor1  = mysqli_fetch_assoc($pendonor);
          //Update Htransaksi
          $jumdonor = $pendonor1['jumDonor'] + 1;



          $tambah = "UPDATE htransaksi
                                  SET diambil='$volume_darah',reaksi='$reaksi',
                                      pengambilan='$keberhasilan',catatan='$catatan',ketBatal='12',jeniskantong='$stok1[jenis]',volumekantong='$stok1[volumeasal]',
                                      nokantong='$id_kantong',petugas='$petugas',
                                      caraambil='$caraambil',status_test='2',Status='2',mu='$mu',gol_darah='$pendonor1[GolDarah]',jam_ambil='$ambil', jam_selesai='$selesai',rhesus='$pendonor1[Rhesus]',jk='$pendonor1[Jk]',pekerjaan='$pendonor1[Pekerjaan]',umur='$pendonor1[umur]',donorke='$jumdonor', `tempat`='M'
                                  WHERE (Status='1' and NoTrans='$notrans')";
          //echo $tambah."<br>";
          $htquery    = mysqli_query($con, $tambah);
          //Update Htransaksi
          $kembali1   = "UPDATE pendonor SET tglkembali='$kembali',jumDonor='$jumdonor',mu='$mu',up=b'1',up_data='2',tglkembali_apheresis='$kembali' WHERE Kode='$kodependonor'";
          //echo $kembali1."<br>";

          //CURL DB NASIONAL
          $curlinsdn = curl_init();
          curl_setopt_array($curlinsdn, array(
            CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/updatedonorkembali.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('Kode' => $kodependonor, 'tglkembali' => $kembali, 'metode' => 'update', 'pjmldonor' => $jumdonor),
          ));
          $response = curl_exec($curlinsdn);
          $datains  = json_decode($response, true);
          //echo "<pre>"; print_r($response); echo "</pre>";
          curl_close($curlinsdn);


          $pdquery    = mysqli_query($con, $kembali1);
          $ono_kantong0 = substr($id_kantong, 0, -1);
          $tambah2    = "UPDATE stokkantong SET Status='5',hasil='5', tgl_Aftap='$tglp1',gol_darah='$GolDarah',RhesusDrh='$Rhesus',produk='WB',sah='0',kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 35 day),mu='$mu',lama_pengambilan='$lama_pengambilan', noSelang='$no_selang' WHERE noKantong='$id_kantong'";
          //echo $tambah2."<br>";
          $skquery    = mysqli_query($con, $tambah2);
          $tambah4    = "UPDATE htransaksi set donorbaru='1' where NoTrans='$notrans' and donorke > 1 ";
          //echo $tambah4."<br>";
          $tambah5    = "UPDATE stokkantong set lama_pengambilan='$lama_pengambilan' WHERE noKantong like '$ono_kantong0%'";
          $sk2query    = mysqli_query($con, $tambah5);


          //=======Audit Trial====================================================================================
          $log_mdl = 'PENGAMBILAN';
          $log_aksi = 'Pengambilan darah: ' . $notrans . ' Pendonor: ' . $kodependonor . ' Kantong: ' . $id_kantong . ' status: ' . $keberhasilan;
          $log = mysqli_query($con, "INSERT INTO `user_log` (`komputer`, `user`, `modul`, `aksi_user`,`tempat`, `keterangan`) VALUES
              ('$client_ip', '$user', '$log_mdl', '$log_aksi','$unit', '')");
          //=====================================================================================================

          //echo $tambah5."<br>";
          if ($sk2query) { ?>
            <div class="row">
              <div class="col-lg-12 col-lg-offset-3">
                <div class="alert alert-success alert-dismissable" role="alert">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                  <strong>Data Gagal Aftap</strong> Berhasil Entry
                </div>
              </div>
            </div>
            <META http-equiv="refresh" content="2; url=?page=searchaftap"><?php

                                                                        }
                                                                      } else { //Nomor Kantong Tidak Ada
                                                                          ?>
          <div class="row">
            <div class="col-lg-12 col-lg-offset-3">
              <div class="alert alert-danger alert-dismissable" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <strong>Entry Gagal</strong> Nomor Kantong Tidak Ada!!!
              </div>
            </div>
          </div>

          <?php
                                                                      }
                                                                    } else { //jika berhasil
                                                                      $noKantong  = trim(mysqli_real_escape_string($con, $_POST['id_kantong11']));
                                                                      $kantong    = mysqli_query($con, "SELECT * from stokkantong where noKantong ='$noKantong' AND `Status`='0' and StatTempat='1' and kadaluwarsa_ktg >'$today1'");
                                                                      $stok1      = mysqli_fetch_array($kantong);
                                                                      $numkantong = mysqli_num_rows($kantong);

                                                                      if ($lama_pengambilan >= 1) {

                                                                        //jika kantong ada
                                                                        if ($numkantong > 0) { //simpan berhasil
                                                                          $pendonor   = mysqli_query($con, "select * from pendonor where Kode='$kodependonor' ");
                                                                          $pendonor1  = mysqli_fetch_assoc($pendonor);
                                                                          //Update Htransaksi
                                                                          $jumdonor = $pendonor1['jumDonor'] + 1;

                                                                          $tambah = "UPDATE htransaksi
                                SET diambil='$volume_darah',reaksi='$reaksi',
                                    pengambilan='$keberhasilan',catatan='$catatan',ketBatal='-',jeniskantong='$stok1[jenis]',volumekantong='$stok1[volumeasal]',
                                    nokantong='$id_kantong',petugas='$petugas',
                                    caraambil='$caraambil',status_test='2',Status='2',mu='$mu',gol_darah='$pendonor1[GolDarah]',jam_ambil='$ambil', jam_selesai='$selesai',rhesus='$pendonor1[Rhesus]',jk='$pendonor1[Jk]',pekerjaan='$pendonor1[Pekerjaan]',umur='$pendonor1[umur]',donorke='$jumdonor', `tempat`='M'
                                WHERE (Status='1' and NoTrans='$notrans')";

                                                                          //CURL DB NASIONAL
                                                                          $curlinsdn = curl_init();
                                                                          curl_setopt_array($curlinsdn, array(
                                                                            CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/updatedonorkembali.php",
                                                                            CURLOPT_RETURNTRANSFER => true,
                                                                            CURLOPT_ENCODING => "",
                                                                            CURLOPT_MAXREDIRS => 10,
                                                                            CURLOPT_TIMEOUT => 5,
                                                                            CURLOPT_FOLLOWLOCATION => true,
                                                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                                            CURLOPT_CUSTOMREQUEST => "POST",
                                                                            CURLOPT_POSTFIELDS => array('Kode' => $kodependonor, 'tglkembali' => $kembali, 'metode' => 'update', 'pjmldonor' => $jumdonor),
                                                                          ));
                                                                          $response = curl_exec($curlinsdn);
                                                                          $datains  = json_decode($response, true);
                                                                          //echo "<pre>"; print_r($response); echo "</pre>";
                                                                          curl_close($curlinsdn);

                                                                          //echo $tambah."<br>";
                                                                          $htquery    = mysqli_query($con, $tambah);
                                                                          //Update Htransaksi
                                                                          $kembali1   = "UPDATE pendonor SET tglkembali='$kembali',jumDonor='$jumdonor',mu='$mu',up=b'1',up_data='2',tglkembali_apheresis='$kembali' WHERE Kode='$kodependonor'";
                                                                          //echo $kembali1."<br>";
                                                                          $pdquery    = mysqli_query($con, $kembali1);
                                                                          $ono_kantong0 = substr($id_kantong, 0, -1);
                                                                          $tambah2    = "UPDATE stokkantong SET Status='1',tgl_Aftap='$tglp1',gol_darah='$GolDarah',RhesusDrh='$Rhesus',produk='WB',sah='0',kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 35 day),mu='$mu',lama_pengambilan='$lama_pengambilan', noSelang='$no_selang' WHERE noKantong='$id_kantong'";
                                                                          //echo $tambah2."<br>";
                                                                          $skquery    = mysqli_query($con, $tambah2);

                                                                          $tambah4    = "UPDATE htransaksi set donorbaru='1' where NoTrans='$notrans' and donorke > 1 ";
                                                                          //echo $tambah4."<br>";


                                                                          $tambah5    = "UPDATE stokkantong set lama_pengambilan='$lama_pengambilan' WHERE noKantong like '$ono_kantong0%'";
                                                                          $sk2query    = mysqli_query($con, $tambah5);


                                                                          //=======Audit Trial====================================================================================
                                                                          $log_mdl = 'PENGAMBILAN';
                                                                          $log_aksi = 'Pengambilan darah: ' . $notrans . ' Pendonor: ' . $kodependonor . ' Kantong: ' . $id_kantong . ' status: ' . $keberhasilan;
                                                                          $log = mysqli_query($con, "INSERT INTO `user_log` (`komputer`, `user`, `modul`, `aksi_user`,`tempat`, `keterangan`) VALUES
            ('$client_ip', '$user', '$log_mdl', '$log_aksi','$unit', '')");
                                                                          //=====================================================================================================


                                                                          //echo $tambah5."<br>";
                                                                          if ($sk2query) { ?>
              <div class="row">
                <div class="col-lg-12 col-lg-offset-3">
                  <div class="alert alert-success alert-dismissable" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <strong>Data Aftap</strong> Berhasil Entry
                  </div>
                </div>
              </div>
              <META http-equiv="refresh" content="2; url=?page=searchaftap"><?php

                                                                          }
                                                                        } else { //Selesai Entry Aftap
                                                                            ?>
            <div class="row">
              <div class="col-lg-12 col-lg-offset-3">
                <div class="alert alert-danger alert-dismissable" role="alert">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                  <strong>Entry Gagal</strong> Nomor Kantong Tidak Ada!!!
                </div>
              </div>
            </div>

          <?php
                                                                        }
                                                                      } else { ?>
          <script>
            alert("Durasi pengambilan Salah...!\nPeriksa Jam Ambil dan Jam Selesai");
          </script>
      <?php

                                                                      }
                                                                    }
                                                                  } else { ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="alert alert-danger alert-dismissable" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <strong>Entry Gagal</strong> Silahkan Masukan Nomor Kantong Utama (A)!!!
          </div>
        </div>
      </div>
  <?php
                                                                  }
                                                                }






  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMDONDAR</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

    <!-- <link rel="stylesheet" href="code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
    <script type="text/javascript">
      document.periksa.jam_ambil.focus();
    </script>





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

    .spasi {

      width: 20px;

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
      <h4 class="text-center" style="font-size:24px; font-weight:bold;color:#ff0000;text-shadow: 1px 1px 1px #000000; font-family:Helvetica, Arial, san-serif;">PENYADAPAN DARAH PENDONOR<br><?php echo $ins['nama']; ?></h4>
      <a href="?page=searchaftap"><button name="baru" class="btn btn-info float-right"><i class="nav-icon ion ion-android-arrow-back"></i> Kembali</button></a>
    </div>

    <div class="col-12 col-sm-12">
      <div class="card-body">
        <!--content-->
        <!--form name="periksa" method="post" action="" id="ambildarah" onkeydown="return event.key != 'Enter';"-->
        <form name="periksa" method="post" action="" id="ambildarah" onsubmit="return validasiregistrasi()">
          <div class="row">
            <div class="col-6 col-sm-6">
              <!--row1--->
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Kode Pendonor</span>
                  </div>
                  <input type="text" name="kode" class="form-control" id="iddonor" placeholder="ID KARTU DONOR" value="<?php echo $kodependonor; ?>" onchange='disabletext(this.value);' readonly>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Pengambilan</span>
                  </div>
                  <script>
                    function disabletext(val) {
                      if (val == '0') {
                        document.getElementById('comments').disabled = true;
                        document.getElementById('id_kantong11').disabled = false;
                        document.getElementById('id_kantong11').type = 'text';
                      }
                      if (val == '2') {
                        document.getElementById('id_kantong11').type = 'text';
                        document.getElementById('comments').disabled = false;
                      }
                      if (val == '1') {
                        document.getElementById('comments').disabled = false;
                        document.getElementById('id_kantong11').type = 'hidden';

                      }
                    }
                  </script>
                  <div class="spasi"></div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="keberhasilan" id="inlineRadio1" value="0" checked>
                    <label class="form-check-label" for="inlineRadio1">Berhasil</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="keberhasilan" id="inlineRadio2" value="2">
                    <label class="form-check-label" for="inlineRadio2">Gagal</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="keberhasilan" id="inlineRadio3" value="1" <label class="form-check-label" for="inlineRadio3">Batal</label>
                  </div>&nbsp;

                  <select name="catatan" class="form-control">
                    <option value="">Pilih Jika Gagal</option>
                    <option value="Mislek">Mislek</option>
                    <option value="Saran Dokter">Saran Dokter</option>
                    <option value="Permintaan Pendonor">Permintaan Pendonor</option>

                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Diambil Sebanyak (cc)</span>
                  </div>
                  <input type="text" name="volume_darah" class="form-control" id="iddonor" value="350">
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Reaksi Donor</span>
                  </div>
                  <select name="reaksi" class="form-control">
                    <option value="Mual">Mual</option>
                    <option value="Pusing">Pusing</option>
                    <option value="Pingsan">Pingsan</option>
                    <option selected value="Normal">Tidak Ada Keluhan</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Cara Ambil</span>
                  </div>
                  <select name="caraambil" class="form-control">
                    <option selected value="0">Biasa</option>
                    <option value="1">Tromboferesis</option>
                    <option value="2">Leukaferesis</option>
                    <option value="3">Plasmaferesis</option>
                    <option value="4">Eritoferesis</option>
                    <option value="5">Aferesis</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Menit Mulai</span>
                  </div>
                  <input size="6" name="ambil" value="" class="form-control" id="jam_ambil" placeholder="13:00" autocomplete="off" required>
                  </input>
                  <div class="input-group-prepend">
                    <span class="input-group-text">Menit Selesai</span>
                  </div>
                  <input size="6" name="selesai" class="form-control" value="" id="jam_selesai" autocomplete="off" placeholder="13:20" required>
                  </input>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Nomor Kantong</span>
                  </div>
                  <input name="id_kantong11" id="id_kantong11" class="form-control" autocomplete="off" placeholder="Klik untuk verifikasi kantong" readonly required onclick="showKantongPopup()">
                  <!-- <input name="id_kantong11" id="id_kantong11" onkeypress="search(event)" class="form-control" autocomplete="off" placeholder="Nomor Kantong" oninput="this.value = this.value.toUpperCase();" required> -->
                </div>
              </div>

              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Nomor Selang</span>
                  </div>
                  <input name="no_selang" id="no_selang" onkeypress="search(event)" class="form-control" autocomplete="off" placeholder="Nomor Selang" oninput="this.value = this.value.toUpperCase();" required>

                </div>
              </div>



              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Petugas Aftap</span>
                  </div>
                  <select name="petugas" class="form-control" required>
                    <?php
                    $usr = mysqli_query($con, "select * from v_petugasmu where (date(TglPenjadwalan)=curdate()) AND kodeinstansi='$id' AND (jabatan between 2 AND 4) ORDER BY nama ASC");

                    while ($data = mysqli_fetch_array($usr)) {
                      echo "<option value=$data[nama] selected>$data[nama]</option>";
                    } ?>
                  </select>

                </div>
              </div>


              <!--row1--->
            </div>
            <div class="col-6 col-sm-6">
              <!--row2--->
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Nama Pendonor</span>
                  </div>
                  <input class="form-control" value="<?php echo $data1['Nama']; ?>" readonly>

                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Golongan Darah</span>
                  </div>
                  <input class="form-control" value="<?php echo $data1['GolDarah'] . "(" . $data1[Rhesus] . ")"; ?>" readonly>
                  <input type="text" class="form-control" value="<?php echo $check1['NamaDokter']; ?>" readonly>
                </div>
              </div>
              <!--div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Nama Dokter</span>
                  </div>
                  <input class="form-control" value="<?php echo $check1['NamaDokter']; ?>" readonly>

                </div>
              </div-->
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Berat Badan (Kg)</span>
                  </div>
                  <input class="form-control" value="<?php echo $check1['beratBadan']; ?>" readonly>

                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">CuSO<sub>4</span>
                  </div>
                  <input class="form-control" value="<?php echo $check1['Hb']; ?>" readonly>

                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Tensi Darah</span>
                  </div>
                  <input class="form-control" value="<?php echo $check1['tensi']; ?>" readonly>

                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Suhu Badan</span>
                  </div>
                  <input class="form-control" value="<?php echo $check1['suhu']; ?>" readonly>

                </div>
              </div>
              <!--row2--->
            </div>
          </div>
          <input type="hidden" name="paket" value="1">
          <input type="hidden" name="notrans" value="<?= $_GET[NoTrans] ?>">
          <input type="hidden" name="kodependonor" value="<?= $check1[KodePendonor] ?>">
          <input type="hidden" name="goldarah" value="<?= $data1[GolDarah] ?>">
          <input type="hidden" name="Rhesus" value="<?= $data1[Rhesus] ?>">
          <div class="col-lg-12" align="left">
            <input type=submit name="simpan" value="SIMPAN" class="btn btn-success">
          </div>
        </form>

        <!--content-->
      </div>
    </div>
    <p class="box3">
    <div class="copyright">
      <p align="center"><a href="https://pmi.or.id">
          <font style="color:white">Copyright @ 2022 | PALANG MERAH INDONESIA
        </a>
    </div>

    <!-- Modal Verifikasi Kantong -->
    <div class="modal fade" id="kantongModal" tabindex="-1" role="dialog" aria-labelledby="kantongModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title" id="kantongModalLabel">
              <i class="fa fa-check-square-o"></i> Verifikasi Kantong Darah
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label style="color: #000000;"><strong>Nomor Kantong (Barcode)</strong></label>
                  <input type="text" class="form-control input-lg text-center" id="popup_id_kantong" placeholder="Scan atau ketik nomor kantong" autofocus required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label style="color: #000000;"><strong>Nomor Selang</strong></label>
                  <input type="text" class="form-control input-lg text-center" id="no_selang_display" placeholder="Scan atau ketik nomor selang">
                </div>
              </div>
            </div>

            <hr>

            <table class="table table-bordered table-striped">
              <thead class="bg-info text-black">
                <tr>
                  <th width="5%">No</th>
                  <th width="25%">Parameter</th>
                  <th width="70%">Pemeriksaan</th>
                </tr>
              </thead>
              <tbody style="color: #000000;">
                <tr>
                  <td>1</td>
                  <td><strong>Kemasan</strong></td>
                  <td>
                    <label class="checkbox-inline"><input type="checkbox" id="kemasan_utuh"> Keadaan Utuh</label>
                    <label class="checkbox-inline"><input type="checkbox" id="kemasan_expired"> Belum Expired</label>
                    <label class="checkbox-inline"><input type="checkbox" id="kemasan_bocor"> Tidak Bocor</label>
                  </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td><strong>Selang</strong></td>
                  <td>
                    <label class="checkbox-inline"><input type="checkbox" id="selang_baik"> Baik</label>
                    <label class="checkbox-inline"><input type="checkbox" id="selang_tertekuk"> Tertekuk</label>
                  </td>
                </tr>
                <tr>
                  <td>3</td>
                  <td><strong>Jarum</strong></td>
                  <td>
                    <label class="checkbox-inline"><input type="checkbox" id="jarum_baik"> Baik</label>
                    <label class="checkbox-inline"><input type="checkbox" id="jarum_bengkok"> Bengkok</label>
                  </td>
                </tr>
                <tr>
                  <td>4</td>
                  <td><strong>Antikoagulan</strong></td>
                  <td>
                    <label class="checkbox-inline"><input type="checkbox" id="anti_jernih"> Jernih</label>
                    <label class="checkbox-inline"><input type="checkbox" id="anti_berubah"> Berubah Warna</label>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="alert alert-warning">
              <strong>Catatan:</strong> Centang hanya kondisi yang benar-benar sesuai dengan keadaan kantong.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-success btn-lg" onclick="submitValidasiKantong()">
              <i class="fa fa-check"></i> Kantong Valid – Lanjut Aftap
            </button>
          </div>
        </div>
      </div>
    </div>



    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>

    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- BS-Stepper -->
    <script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>
    <!-- dropzonejs -->
    <script src="plugins/dropzone/min/dropzone.min.js"></script>
    <!-- bootstrap color picker -->
    <script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

    <script>
      function search(event) {
        let value = event.which;
        if (value === 13) {
          //onkeydown="return event.key != 'Enter';"
          //call your function or anything else

          getkantong = document.getElementById("id_kantong11").value;


          $.ajax({
            method: "POST",
            url: "carinoselang.php",
            data: {
              ktg: getkantong
            },
            success: function(server_response) {
              document.periksa.no_selang.value = server_response;

            }
          });
          //alert('Nomor Kantong : ' + getkantong);
          document.getElementById("no_selang").focus();
        }
      }

      $(document).ready(function() {
        $("#jam_ambil").focus();
        $('#jam_ambil, #jam_selesai').inputmask("99:99", {
          placeholder: "mm:dd",
          insertMode: false
        });

        $("#ambildarah").on("keypress", function(event) {
          var keyPressed = event.keyCode || event.which;
          if (keyPressed === 13) {
            event.preventDefault();
            return false;
          }
        });
      });

      $(function() {
        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
          theme: 'bootstrap4'
        })

        //Datemask dd/mm/yyyy
        $('#datemask').inputmask('dd/mm/yyyy', {
          'placeholder': 'dd/mm/yyyy'
        })
        //Datemask2 mm/dd/yyyy
        $('#datemask2').inputmask('mm/dd/yyyy', {
          'placeholder': 'mm/dd/yyyy'
        })
        //Money Euro
        $('[data-mask]').inputmask()

        //Date picker
        $('#reservationdate').datetimepicker({
          format: 'yyyy-MM-DD'
        });

        //Date picker
        $('#reservationdate2').datetimepicker({
          format: 'yyyy-MM-DD'
        });

        //Date and time picker
        $('#reservationdatetime').datetimepicker({
          icons: {
            time: 'far fa-clock'
          }
        });

        //Date range picker
        $('#reservation').daterangepicker()
        //Date range picker with time picker
        $('#reservationtime').daterangepicker({
          timePicker: true,
          timePickerIncrement: 30,
          locale: {
            format: 'MM/DD/YYYY hh:mm A'
          }
        })
        //Date range as a button
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
          },
          function(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
          }
        )

        //Timepicker
        $('#timepicker').datetimepicker({
          format: 'LT'
        })



        //Bootstrap Duallistbox
        $('.duallistbox').bootstrapDualListbox()

        //Colorpicker
        $('.my-colorpicker1').colorpicker()
        //color picker with addon
        $('.my-colorpicker2').colorpicker()

        $('.my-colorpicker2').on('colorpickerChange', function(event) {
          $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
        })

        $("input[data-bootstrap-switch]").each(function() {
          $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })

      })
      // BS-Stepper Init
      document.addEventListener('DOMContentLoaded', function() {
        window.stepper = new Stepper(document.querySelector('.bs-stepper'))
      })

      // DropzoneJS Demo Code Start
      Dropzone.autoDiscover = false

      // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
      var previewNode = document.querySelector("#template")
      previewNode.id = ""
      var previewTemplate = previewNode.parentNode.innerHTML
      previewNode.parentNode.removeChild(previewNode)

      var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: "/target-url", // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        previewTemplate: previewTemplate,
        autoQueue: false, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
      })

      myDropzone.on("addedfile", function(file) {
        // Hookup the start button
        file.previewElement.querySelector(".start").onclick = function() {
          myDropzone.enqueueFile(file)
        }
      })

      // Update the total progress bar
      myDropzone.on("totaluploadprogress", function(progress) {
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
      })

      myDropzone.on("sending", function(file) {
        // Show the total progress bar when upload starts
        document.querySelector("#total-progress").style.opacity = "1"
        // And disable the start button
        file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
      })

      // Hide the total progress bar when nothing's uploading anymore
      myDropzone.on("queuecomplete", function(progress) {
        document.querySelector("#total-progress").style.opacity = "0"
      })

      // Setup the buttons for all transfers
      // The "add files" button doesn't need to be setup because the config
      // `clickable` has already been specified.
      document.querySelector("#actions .start").onclick = function() {
        myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
      }
      document.querySelector("#actions .cancel").onclick = function() {
        myDropzone.removeAllFiles(true)
      }
      // DropzoneJS Demo Code End

      $(function() {
        $("#example1").DataTable({
          "responsive": true,
          "lengthChange": false,
          "autoWidth": false,
          "buttons": ["copy", "excel", "pdf", "print"]
          //"buttons": ["pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": false,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
        });
      });

      $(document).on("click", "#batal", function() {
        var id = $(this).data('id');

        $("#batal-edit #id").val(id);

      })

      function showKantongPopup() {
        // Reset checkbox ke kondisi default (semua baik = checked)
        $('#kantongModal input[type="checkbox"]').prop('checked', false);
        $('#kemasan_utuh').prop('checked', true);
        $('#kemasan_expired').prop('checked', true);
        $('#kemasan_bocor').prop('checked', true);
        $('#selang_baik').prop('checked', true);
        $('#jarum_baik').prop('checked', true);
        $('#anti_jernih').prop('checked', true);

        $('#popup_id_kantong').val('');
        $('#no_selang_display').val('');

        $('#kantongModal').modal({
          backdrop: 'static',
          keyboard: false
        });

        $('#popup_id_kantong').focus();
      }

      // Auto ambil nomor selang saat scan/ketik nomor kantong
      $('#popup_id_kantong').on('change', function() {
        var ktg = $(this).val().trim();
        if (ktg.length >= 11) { // asumsi minimal panjang barcode
          $.ajax({
            url: 'carinoselang.php',
            method: 'POST',
            data: {
              ktg: ktg
            },
            success: function(res) {
              $('#no_selang_display').val(res.trim());
            },
            error: function() {
              alert('Gagal mengambil nomor selang');
            }
          });
        }
      });

      function submitValidasiKantong() {
        const kantong = $('#popup_id_kantong').val().trim();
        if (!kantong) {
          alert('Nomor kantong belum diisi!');
          return;
        }

        // Cek apakah semua parameter OK
        const kemasan_ok = $('#kemasan_utuh').is(':checked') && $('#kemasan_expired').is(':checked') && $('#kemasan_bocor').is(':checked');
        const selang_ok = $('#selang_baik').is(':checked') && !$('#selang_tertekuk').is(':checked');
        const jarum_ok = $('#jarum_baik').is(':checked') && !$('#jarum_bengkok').is(':checked');
        const anti_ok = $('#anti_jernih').is(':checked') && !$('#anti_berubah').is(':checked');

        const is_all_ok = kemasan_ok && selang_ok && jarum_ok && anti_ok;

        if (!is_all_ok) {
          if (!confirm('Ada parameter yang kurang bagus.\nKantong ini TIDAK DAPAT DIGUNAKAN.\n\nTetap lanjutkan?')) {
            return;
          }
        }

        $.ajax({
          url: '../../../modul/simpan_verifikasi_kantong.php',
          type: 'POST',
          data: {
            submit_verif: '1',
            no_kantong: kantong,
            kemasan_utuh: $('#kemasan_utuh').is(':checked') ? 1 : 0,
            kemasan_expired: $('#kemasan_expired').is(':checked') ? 1 : 0,
            kemasan_bocor: $('#kemasan_bocor').is(':checked') ? 1 : 0,
            selang_baik: $('#selang_baik').is(':checked') ? 1 : 0,
            selang_tertekuk: $('#selang_tertekuk').is(':checked') ? 1 : 0,
            jarum_baik: $('#jarum_baik').is(':checked') ? 1 : 0,
            jarum_bengkok: $('#jarum_bengkok').is(':checked') ? 1 : 0,
            anti_jernih: $('#anti_jernih').is(':checked') ? 1 : 0,
            anti_berubah: $('#anti_berubah').is(':checked') ? 1 : 0
          },
          success: function(res) {
            res = res.trim();
            if (res === 'OK') {
              $('#id_kantong11').val(kantong);
              $('#no_selang').val($('#no_selang_display').val());
              $('#kantongModal').modal('hide');
              alert('Verifikasi kantong berhasil! Siap untuk aftap.');
            } else {
              alert('Gagal verifikasi: ' + res);
            }
          }
        });


        // Versi sederhana tanpa simpan ke DB (hanya client-side)
        $('#id_kantong11').val(kantong);
        $('#no_selang').val($('#no_selang_display').val());
        $('#kantongModal').modal('hide');
        alert('Kantong telah diverifikasi.');
      }
    </script>

  </body>

  </html>
<?php } ?>