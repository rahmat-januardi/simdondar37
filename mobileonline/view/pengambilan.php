<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include '../adm/config.php';

$utd = mysqli_fetch_array(mysqli_query($con, "SELECT * from utd where `aktif`=1"));
$idudd = $utd['id'];
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
          $tambah2    = "UPDATE stokkantong SET Status='5',hasil='5', tgl_Aftap='$tglp1',gol_darah='$GolDarah',RhesusDrh='$Rhesus',produk='WB',sah='0',kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 35 day),mu='$mu',lama_pengambilan='$lama_pengambilan', noSelang='$no_selang', AsalUTD='$idudd' WHERE noKantong='$id_kantong'";
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
                                                                          $tambah2    = "UPDATE stokkantong SET Status='1',tgl_Aftap='$tglp1',gol_darah='$GolDarah',RhesusDrh='$Rhesus',produk='WB',sah='0',kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 35 day),mu='$mu',lama_pengambilan='$lama_pengambilan', noSelang='$no_selang', AsalUTD='$idudd' WHERE noKantong='$id_kantong'";
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

    <!-- Inputmask Plugin (dipindah ke bawah setelah jQuery) -->
    <!-- <script src="plugins/inputmask/jquery.inputmask.min.js"></script> -->

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
      .swal2-popup {
        font-size: 18px !important;
      }

      .swal2-title {
        font-size: 24px !important;
      }

      .swal2-html-container {
        font-size: 18px !important;
      }

      .nav-tabs>li>a {
        color: #333 !important;
        font-size: 16px;
        font-weight: bold;
      }

      .tab-content {
        overflow: visible !important;
        padding: 20px;
        border: 1px solid #ddd;
        border-top: none;
        background: #fff;
        border-radius: 0 0 4px 4px;
      }

      /* Padding untuk card body */
      .card-body {
        padding: 20px;
      }

      /* Padding untuk row di dalam form */
      .tab-content .row {
        margin-left: 0;
        margin-right: 0;
      }

      .disabled-tab {
        color: #999 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
        opacity: 0.6;
      }

      .nav-tabs {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: white;
        margin-bottom: 0;
        border-bottom: 1px solid #ddd;
      }

      .nav-tabs>li.disabled>a {
        color: #999 !important;
        cursor: not-allowed !important;
        background-color: #f5f5f5 !important;
        border-color: #ddd !important;
      }

      .nav-tabs>li.active>a {
        color: #000 !important;
      }

      /* Perbaikan tampilan form */
      .form-group {
        margin-bottom: 15px;
      }

      /* Styling untuk tabel verifikasi */
      .table-bordered>thead>tr>th,
      .table-bordered>tbody>tr>td {
        vertical-align: middle;
      }

      /* Styling untuk checkbox inline */
      .checkbox-inline {
        margin-right: 20px;
      }

      /* Styling untuk keterangan otomatis */
      #keterangan_auto {
        font-size: 14px;
        text-align: center;
        cursor: default;
      }

      /* Styling untuk input field */
      .input-lg {
        height: 46px;
        padding: 10px 16px;
        font-size: 18px;
        line-height: 1.33;
        border-radius: 6px;
      }

      .font-weight-bold {
        font-weight: bold !important;
      }

      /* Styling untuk small text */
      .form-text.text-muted {
        font-size: 12px;
        margin-top: 5px;
      }


      .form-control {
        border-radius: 4px;
        height: 38px;
      }

      .card-header h4 {
        margin: 0;
      }

      .panel-title h4 {
        margin: 0;
        color: #333;
      }

      .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
      }

      .text-right button {
        padding: 10px 20px;
      }

      /* Alignment untuk dua kolom form */
      .row .col-lg-6 .form-group {
        display: flex;
        align-items: center;
      }

      .row .col-lg-6 .form-group label {
        flex: 0 0 40%;
        max-width: 40%;
        padding-right: 15px;
        text-align: right;
      }

      .row .col-lg-6 .form-group .col-lg-8,
      .row .col-lg-6 .form-group .col-lg-4,
      .row .col-lg-6 .form-group .col-lg-3 {
        flex: 0 0 60%;
        max-width: 60%;
      }

      /* Styling table verifikasi */
      .table thead th {
        background-color: #17a2b8;
        color: white;
        text-align: center;
      }

      .table td,
      .table th {
        vertical-align: middle;
      }

      .checkbox-inline {
        margin-right: 15px;
      }

      #keterangan_auto {
        font-weight: bold;
      }

      /* Kurangi padding untuk lebih lebar */
      .card-body {
        padding: 15px;
        /* Kurangi dari 20px jadi 15px */
      }

      .tab-content {
        padding: 15px;
        /* Sama, kurangi padding */
      }

      /* Buat form-group lebih lebar: label lebih sempit, input lebih panjang */
      .row .col-lg-6 .form-group label {
        flex: 0 0 30%;
        /* Kurangi dari 40% jadi 30% */
        max-width: 30%;
        padding-right: 10px;
        /* Kurangi padding kanan */
      }

      .row .col-lg-6 .form-group .col-md-8,
      /* Ini sebenarnya bukan col-md-8, tapi div biasa */
      .row .col-lg-6 .form-group>div:not(.form-check) {
        /* Target div input */
        flex: 0 0 70%;
        /* Naikkan dari 60% jadi 70% */
        max-width: 70%;
      }

      /* Buat card lebih lebar dengan kurangi margin row */
      .row {
        margin-left: -10px;
        /* Kurangi margin negatif default Bootstrap (-15px) */
        margin-right: -10px;
      }

      /* Opsional: Buat input lebih tinggi/lebih readable */
      .form-control {
        height: 40px;
        /* Naikkan sedikit dari 38px */
        font-size: 16px;
        /* Besarkan font untuk terasa lebih penuh */
      }
    </style>
  </head>

  <body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">
      <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="dist/img/logo.png" alt="AdminLTELogo" height="60" width="60">
      </div>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper" style="margin-left: 0 !important; padding-left: 0 !important; padding-right: 0 !important;">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-12">
                <h1 class="m-0 text-center text-danger" style="font-weight: bold; text-shadow: 1px 1px 1px #000;">PENYADAPAN DARAH PENDONOR<br><?php echo strtoupper($namains); ?></h1>
              </div>
            </div>
          </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title text-center">PENGAMBILAN DARAH PENDONOR</h3>
                    <a href="?page=searchaftap" class="btn btn-info btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
                  </div>
                  <div class="card-body">
                    <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                      <li class="nav-item active">
                        <a class="nav-link active" id="verifikasi-tab" data-toggle="tab" href="#tab-verifikasi" role="tab" aria-controls="tab-verifikasi" aria-selected="true">
                          <i class="fas fa-check-square"></i> 1. Verifikasi Kantong
                        </a>
                      </li>
                      <li class="nav-item disabled" id="tab-pengambilan-li">
                        <a class="nav-link disabled-tab" id="pengambilan-tab" data-toggle="tab" href="#tab-pengambilan" role="tab" aria-controls="tab-pengambilan" aria-selected="false">
                          <i class="fas fa-tint"></i> 2. Pengambilan Darah
                        </a>
                      </li>
                    </ul>
                    <div class="tab-content" id="custom-tabsContent">
                      <!-- TAB 1: VERIFIKASI KANTONG -->
                      <div class="tab-pane fade show active" id="tab-verifikasi" role="tabpanel" aria-labelledby="verifikasi-tab">
                        <form>
                          <hr>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="popup_id_kantong"><strong>Nomor Kantong (Barcode):</strong></label>
                                <input type="text" class="form-control input-md text-center font-weight-bold" id="popup_id_kantong" placeholder="Scan atau ketik nomor kantong" autofocus required>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="no_selang_display"><strong>Nomor Selang:</strong></label>
                                <input type="text" class="form-control input-md text-center font-weight-bold" id="no_selang_display" placeholder="Otomatis dari nomor kantong">
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="tanggal_buka"><strong>Tanggal Buka Kemasan <span class="text-danger">*</span></strong></label>
                                <input type="datetime-local" name="tanggal_buka" id="tanggal_buka" class="form-control" required>
                                <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Tanggal saat kemasan kantong pertama kali dibuka/dikeluarkan dari bungkus.</small>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label><strong>Tanggal Verifikasi</strong></label>
                                <input type="text" class="form-control input-md text-center" id="tgl_verifikasi_display"
                                  value="<?php echo date('d-m-Y H:i'); ?>" readonly
                                  style="background:#f0f4f8; color:#555; font-weight:bold;">
                              </div>
                            </div>
                          </div>

                          <!-- Info kantong (hidden, diisi otomatis dari AJAX) -->
                          <div class="row" style="display:none;">
                            <div class="col-md-4">
                              <label class="form-label">Merk Kantong</label>
                              <input type="text" class="form-control text-center" id="merk_kantong" placeholder="Merk Kantong" readonly>
                            </div>
                            <div class="col-md-4">
                              <label class="form-label">Jenis Kantong</label>
                              <input type="text" class="form-control text-center" id="jenis_kantong" placeholder="Jenis Kantong" readonly>
                            </div>
                            <div class="col-md-4">
                              <label class="form-label">Volume Kantong (ml)</label>
                              <input type="text" class="form-control text-center" id="volume_kantong" placeholder="Volume" readonly>
                            </div>
                          </div>

                          <hr>

                          <table class="table table-bordered table-striped table-sm">
                            <thead class="bg-info text-white">
                              <tr>
                                <th width="5%">No</th>
                                <th width="25%">Parameter</th>
                                <th width="70%">Pemeriksaan</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td><strong>Kemasan</strong></td>
                                <td>
                                  <label class="checkbox-inline"><input type="checkbox" id="kemasan_utuh" checked="checked"> Keadaan Utuh</label>
                                  <label class="checkbox-inline"><input type="checkbox" id="kemasan_expired" checked="checked"> Belum Expired</label>
                                  <label class="checkbox-inline"><input type="checkbox" id="kemasan_bocor" checked="checked"> Tidak Bocor</label>
                                </td>
                              </tr>
                              <tr>
                                <td>2</td>
                                <td><strong>Selang</strong></td>
                                <td>
                                  <label class="checkbox-inline"><input type="checkbox" id="selang_baik" checked="checked"> Baik</label>
                                  <label class="checkbox-inline"><input type="checkbox" id="selang_tertekuk"> Tertekuk</label>
                                </td>
                              </tr>
                              <tr>
                                <td>3</td>
                                <td><strong>Jarum</strong></td>
                                <td>
                                  <label class="checkbox-inline"><input type="checkbox" id="jarum_baik" checked="checked"> Baik</label>
                                  <label class="checkbox-inline"><input type="checkbox" id="jarum_bengkok"> Bengkok</label>
                                </td>
                              </tr>
                              <tr>
                                <td>4</td>
                                <td><strong>Antikoagulan</strong></td>
                                <td>
                                  <label class="checkbox-inline"><input type="checkbox" id="anti_jernih" checked="checked"> Jernih</label>
                                  <label class="checkbox-inline"><input type="checkbox" id="anti_berubah"> Berubah Warna</label>
                                </td>
                              </tr>
                            </tbody>
                          </table>

                          <div class="form-group row" style="margin-top:15px;">
                            <label for="keterangan_auto" class="col-sm-2 col-form-label">
                              <strong>Keterangan:</strong>
                            </label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control font-weight-bold" id="keterangan_auto" readonly style="background:#f8f9fa;">
                            </div>
                          </div>
                          <hr>

                          <div class="alert alert-warning" style="margin-top:15px;">
                            <strong>Catatan:</strong> Centang hanya kondisi yang benar-benar sesuai dengan keadaan kantong.
                          </div>

                          <div class="text-right">
                            <button type="button" class="btn btn-success btn-md" onclick="submitValidasiKantong()">
                              <i class="fa fa-check"></i> Simpan Verifikasi Kantong
                            </button>
                          </div>
                        </form>
                      </div>

                      <!-- TAB 2: PENGAMBILAN DARAH -->
                      <div class="tab-pane fade" id="tab-pengambilan" role="tabpanel" aria-labelledby="pengambilan-tab">
                        <form class="form-horizontal" method="POST" id="ambildarah" name="ambildarah" onsubmit="return validasiPengambilanDarah()">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Kode Pendonor</label>
                                <div class="col-md-9">
                                  <input type="text" class="form-control" name="kodependonor" value="<?php echo $check1['KodePendonor']; ?>" readonly required>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Pengambilan</label>
                                <div class="col-md-9">
                                  <div class="row">
                                    <!-- Radio buttons -->
                                    <div class="col-md-7">
                                      <label class="radio-inline">
                                        <input type="radio" value="0" name="keberhasilan" required> Berhasil
                                      </label>
                                      <label class="radio-inline">
                                        <input type="radio" value="1" name="keberhasilan"> Batal
                                      </label>
                                      <label class="radio-inline">
                                        <input type="radio" value="2" name="keberhasilan"> Gagal
                                      </label>
                                    </div>

                                    <!-- Dropdown Catatan (sejajar di sebelah kanan) -->
                                    <div class="col-md-5">
                                      <select name="catatan" class="form-control">
                                        <option value="">-- Tidak Ada Catatan --</option>
                                        <option value="Mislek">Mislek</option>
                                        <option value="Saran Dokter">Saran Dokter</option>
                                        <option value="Permintaan Pendonor">Permintaan Pendonor</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                              </div>


                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Diambil Sebanyak (cc)</label>
                                <div class="col-md-9">
                                  <input type="text" name="volume_darah" class="form-control" value="350" required>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Reaksi Donor</label>
                                <div class="col-md-9">
                                  <select name="reaksi" class="form-control">
                                    <option value="Mual">Mual</option>
                                    <option value="Pusing">Pusing</option>
                                    <option value="Pingsan">Pingsan</option>
                                    <option selected value="Normal">Tidak Ada Keluhan</option>
                                  </select>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Cara Ambil</label>
                                <div class="col-md-9">
                                  <select name="caraambil" class="form-control" required>
                                    <option value="0" selected>Biasa</option>
                                    <option value="1">Tromboferesis</option>
                                    <option value="2">Leukaferesis</option>
                                    <option value="3">Plasmaferesis</option>
                                    <option value="4">Eritroferesis</option>
                                    <option value="5">Aferesis</option>
                                    <!-- tambah opsi lain sesuai kebutuhan UTD Anda -->
                                  </select>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Jam Mulai</label>
                                <div class="col-md-3">
                                  <input name="ambil" class="form-control" id="jam_ambil" placeholder="HH:mm" autocomplete="off" required>
                                </div>

                                <label class="col-md-3 col-form-label text-right">Jam Selesai</label>
                                <div class="col-md-3">
                                  <input name="selesai" class="form-control" id="jam_selesai" placeholder="HH:mm" autocomplete="off" required>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Nomor Kantong</label>
                                <div class="col-md-9">
                                  <input name="id_kantong11" id="id_kantong11" class="form-control" placeholder="Hasil verifikasi akan muncul di sini" readonly required>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Nomor Selang</label>
                                <div class="col-md-9">
                                  <input name="no_selang" id="no_selang" class="form-control" placeholder="Hasil verifikasi akan muncul di sini" required>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-3 col-form-label text-right">Petugas Aftap</label>
                                <div class="col-md-9">
                                  <select name="petugas" id="petugas" class="form-control select2" style="width:100%;" required>
                                    <?php
                                    $usr = mysqli_query($con, "select * from v_petugasmu where (date(TglPenjadwalan)=curdate()) AND kodeinstansi='$id' AND (jabatan between 2 AND 4) ORDER BY nama ASC");

                                    while ($data = mysqli_fetch_array($usr)) {
                                      echo "<option value=$data[nama] selected>$data[nama]</option>";
                                    } ?>
                                  </select>

                                </div>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right">Nama Pendonor</label>
                                <div class="col-md-8">
                                  <input type="text" class="form-control" value="<?php echo strtoupper($data1['Nama']); ?>" readonly>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right">Donor Ke</label>
                                <div class="col-md-8">
                                  <input type="text" class="form-control" value="<?php echo $check1['donorke']; ?> Kali" readonly>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right">Golongan Darah</label>
                                <div class="col-md-8">
                                  <input type="text" class="form-control" value="<?php echo $check1['gol_darah'] . ' (' . $check1['rhesus'] . ')'; ?>" readonly>
                                  <input type="hidden" name="goldarah" value="<?php echo $check1['gol_darah']; ?>">
                                  <input type="hidden" name="Rhesus" value="<?php echo $check1['rhesus']; ?>">
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right">Berat Badan</label>
                                <div class="col-md-8">
                                  <input type="text" class="form-control" value="<?php echo $check1['beratBadan']; ?> Kg" readonly>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right">Tekanan Darah</label>
                                <div class="col-md-8">
                                  <input type="text" class="form-control" value="<?php echo $check1['tensi']; ?> mmHg" readonly>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right">Hemoglobin</label>
                                <div class="col-md-8">
                                  <input type="text" class="form-control" value="<?php echo $check1['Hb']; ?> g/dL" readonly>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right">Suhu</label>
                                <div class="col-md-8">
                                  <input type="text" class="form-control" value="<?php echo $check1['suhu']; ?> °C" readonly>
                                </div>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right">Nadi</label>
                                <div class="col-md-8">
                                  <input type="text" class="form-control" value="<?php echo $check1['nadi']; ?> BPM" readonly>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="card-footer text-right">
                            <button type="button" class="btn btn-secondary" onclick="history.back()"><i class="fas fa-arrow-left"></i> Kembali</button>
                            <button type="submit" name="simpan" class="btn btn-danger"><i class="fas fa-save"></i> Simpan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->

      <footer class="main-footer text-center">
        <strong>Copyright &copy; 2022 <a href="https://pmi.or.id">Palang Merah Indonesia</a>.</strong> All rights reserved.
      </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- Inputmask (harus setelah jQuery) -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <script>
      $(function() {
        //Initialize Select2 Elements
        $('.select2').select2();
        // Khusus select petugas
        $('#petugas').select2();
      });

      $(document).ready(function() {
        $("#jam_ambil").focus();
        $('#jam_ambil, #jam_selesai').inputmask("99:99", {
          placeholder: "HH:mm",
          insertMode: false
        });

        // Validasi submit form: pastikan verifikasi kantong sudah dilakukan
        $('#ambildarah').on('submit', function(e) {
          if ($('#id_kantong11').val() === '' || $('#no_selang').val() === '') {
            e.preventDefault();
            Swal.fire({
              icon: 'warning',
              title: 'Verifikasi Belum Dilakukan',
              text: 'Silakan verifikasi kantong darah terlebih dahulu sebelum menyimpan!',
              confirmButtonText: 'OK'
            });
            return false;
          }
        });

        // Prevent clicking on disabled tab
        $('#tab-pengambilan-li a').on('click', function(e) {
          if ($(this).hasClass('disabled-tab') || $(this).parent().hasClass('disabled')) {
            e.preventDefault();
            e.stopPropagation();
            Swal.fire({
              icon: 'warning',
              title: 'Verifikasi Diperlukan',
              text: 'Silakan verifikasi kantong darah terlebih dahulu sebelum melanjutkan ke pengambilan darah!',
              confirmButtonText: 'OK'
            });
            return false;
          }
        });

        $("#ambildarah").on("keypress", function(event) {
          var keyPressed = event.keyCode || event.which;
          if (keyPressed === 13) {
            event.preventDefault();
            return false;
          }
        });
      });

      // Fungsi validasi form sebelum submit (dipanggil oleh onsubmit)
      function validasiPengambilanDarah() {
        if ($('#id_kantong11').val() === '' || $('#no_selang').val() === '') {
          Swal.fire({
            icon: 'warning',
            title: 'Verifikasi Belum Dilakukan',
            text: 'Silakan verifikasi kantong darah terlebih dahulu!',
            confirmButtonText: 'OK'
          });
          return false;
        }
        return true;
      }

      // Saat nomor kantong di-scan/ketik, otomatis ambil no selang + info kantong + tanggal_buka
      $('#popup_id_kantong').on('change', function() {
        var ktg = $(this).val().trim();
        if (ktg.length >= 11) {
          $.ajax({
            url: 'carinoselang_1.php',
            method: 'POST',
            data: {
              ktg: ktg
            },
            success: function(res) {
              const parts = res.split('|');

              if (parts.length >= 5) {
                $('#no_selang_display').val(parts[0]);
                $('#merk_kantong').val(parts[1] || '-');
                $('#volume_kantong').val(parts[2] || '-');
                $('#jenis_kantong').val(parts[3] || '-');

                // === AUTO FILL TANGGAL BUKA KEMASAN dari DB ===
                if (parts[4]) {
                  $('#tanggal_buka').val(parts[4]).css({
                    'background-color': '#d4edda',
                    'border-color': '#28a745',
                    'font-weight': 'bold'
                  });
                } else {
                  $('#tanggal_buka').val('').css('background-color', '');
                }
              } else if (parts.length >= 1) {
                $('#no_selang_display').val(parts[0]);
              }
            },
            error: function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal mengambil data',
                text: 'Tidak dapat terhubung ke server.'
              });
            }
          });
        }
      });

      // Membuat checkbox mutually exclusive dan trigger update keterangan
      $('#selang_baik').on('change', function() {
        if ($(this).is(':checked')) {
          $('#selang_tertekuk').prop('checked', false);
        }
        updateKeteranganSimpel();
      });
      $('#selang_tertekuk').on('change', function() {
        if ($(this).is(':checked')) {
          $('#selang_baik').prop('checked', false);
        }
        updateKeteranganSimpel();
      });
      $('#jarum_baik').on('change', function() {
        if ($(this).is(':checked')) {
          $('#jarum_bengkok').prop('checked', false);
        }
        updateKeteranganSimpel();
      });
      $('#jarum_bengkok').on('change', function() {
        if ($(this).is(':checked')) {
          $('#jarum_baik').prop('checked', false);
        }
        updateKeteranganSimpel();
      });
      $('#anti_jernih').on('change', function() {
        if ($(this).is(':checked')) {
          $('#anti_berubah').prop('checked', false);
        }
        updateKeteranganSimpel();
      });
      $('#anti_berubah').on('change', function() {
        if ($(this).is(':checked')) {
          $('#anti_jernih').prop('checked', false);
        }
        updateKeteranganSimpel();
      });

      // Event handler untuk checkbox kemasan
      $('#kemasan_utuh, #kemasan_expired, #kemasan_bocor').on('change', function() {
        updateKeteranganSimpel();
      });


      // Update keterangan otomatis
      function updateKeteranganSimpel() {
        // Cek apakah SEMUA parameter sudah dipilih
        let semuaSudahDicek = true;

        // Cek kemasan (3 checkbox harus semua dicek)
        // if (!$('#kemasan_utuh').is(':checked') || !$('#kemasan_expired').is(':checked') || !$('#kemasan_bocor').is(':checked')) {
        //   semuaSudahDicek = false;
        // }

        // Cek selang (minimal salah satu harus dicek)
        if (!$('#selang_baik').is(':checked') && !$('#selang_tertekuk').is(':checked')) {
          semuaSudahDicek = false;
        }

        // Cek jarum (minimal salah satu harus dicek)
        if (!$('#jarum_baik').is(':checked') && !$('#jarum_bengkok').is(':checked')) {
          semuaSudahDicek = false;
        }

        // Cek antikoagulan (minimal salah satu harus dicek)
        if (!$('#anti_jernih').is(':checked') && !$('#anti_berubah').is(':checked')) {
          semuaSudahDicek = false;
        }

        // Jika belum semua dicek
        if (!semuaSudahDicek) {
          $('#keterangan_auto')
            .val("Silakan centang semua parameter pemeriksaan terlebih dahulu")
            .css({
              'color': '#6c757d',
              'font-weight': 'bold',
              'background': '#f8f9fa'
            });
          return;
        }

        // Cek apakah SEMUA kondisi baik
        const semuaBaik =
          $('#kemasan_utuh').is(':checked') &&
          $('#kemasan_expired').is(':checked') &&
          $('#kemasan_bocor').is(':checked') &&
          $('#selang_baik').is(':checked') &&
          !$('#selang_tertekuk').is(':checked') &&
          $('#jarum_baik').is(':checked') &&
          !$('#jarum_bengkok').is(':checked') &&
          $('#anti_jernih').is(':checked') &&
          !$('#anti_berubah').is(':checked');

        if (semuaBaik) {
          $('#keterangan_auto')
            .val("Kantong dalam kondisi BAIK dan DAPAT digunakan untuk pengambilan darah.")
            .css({
              'color': 'green',
              'font-weight': 'bold',
              'background': '#e8f5e9'
            });
        } else {
          $('#keterangan_auto')
            .val("Kantong TIDAK BAIK dan TIDAK DAPAT digunakan untuk pengambilan darah.")
            .css({
              'color': 'red',
              'font-weight': 'bold',
              'background': '#ffebee'
            });
        }
      }

      // Jalankan update keterangan saat load halaman
      $(document).ready(function() {
        updateKeteranganSimpel();
      });

      let isForceReverif = false;

      function submitValidasiKantong() {
        const kantong = $('#popup_id_kantong').val().trim();
        const tanggal_buka = $('#tanggal_buka').val().trim();

        if (!kantong) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Nomor kantong belum diisi!',
            confirmButtonText: 'OK',
            width: '600px' // Ukuran lebih besar
          });
          return;
        }

        if (!tanggal_buka) {
          Swal.fire({
            icon: 'warning',
            title: 'Tanggal Buka Kemasan Belum Diisi',
            text: 'Silakan pilih tanggal buka kemasan sebelum melakukan verifikasi!',
            confirmButtonText: 'OK',
            width: '600px'
          });
          $('#tanggal_buka').focus();
          return;
        }

        // Cek jika ada parameter kurang bagus
        const kemasan_ok = $('#kemasan_utuh').is(':checked') && $('#kemasan_expired').is(':checked') && $('#kemasan_bocor').is(':checked');
        const selang_ok = $('#selang_baik').is(':checked') && !$('#selang_tertekuk').is(':checked');
        const jarum_ok = $('#jarum_baik').is(':checked') && !$('#jarum_bengkok').is(':checked');
        const anti_ok = $('#anti_jernih').is(':checked') && !$('#anti_berubah').is(':checked');

        const is_all_ok = kemasan_ok && selang_ok && jarum_ok && anti_ok;

        if (!is_all_ok) {
          Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi',
            text: 'Terdapat parameter pemeriksaan kantong yang tidak sesuai.',
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Periksa Ulang',
            width: '600px' // Ukuran lebih besar
          }).then((result) => {
            if (!result.isConfirmed) {
              return;
            } else {
              kirimValidasi(kantong);
            }
          });
        } else {
          Swal.fire({
            icon: 'success',
            title: 'Konfirmasi',
            text: 'Semua parameter pemeriksaan kantong dalam kondisi BAIK.\n\nLanjutkan ke proses pengambilan darah?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Periksa Ulang',
            width: '600px'
          }).then((result) => {
            if (result.isConfirmed) {
              kirimValidasi(kantong);
            }
            // jika cancel → user bisa periksa ulang checkbox
          });
        }
      }

      function kirimValidasi(kantong) {
        const tanggal_buka = $('#tanggal_buka').val().trim();
        $.ajax({
          url: '../../../modul/simpan_verifikasi_kantong.php',
          type: 'POST',
          data: {
            submit_verif: '1',
            no_kantong: kantong,
            tanggal_buka: tanggal_buka,
            kemasan_utuh: $('#kemasan_utuh').prop('checked') ? 1 : 0,
            kemasan_expired: $('#kemasan_expired').prop('checked') ? 1 : 0,
            kemasan_bocor: $('#kemasan_bocor').prop('checked') ? 1 : 0,
            selang_baik: $('#selang_baik').prop('checked') ? 1 : 0,
            selang_tertekuk: $('#selang_tertekuk').prop('checked') ? 1 : 0,
            jarum_baik: $('#jarum_baik').prop('checked') ? 1 : 0,
            jarum_bengkok: $('#jarum_bengkok').prop('checked') ? 1 : 0,
            anti_jernih: $('#anti_jernih').prop('checked') ? 1 : 0,
            anti_berubah: $('#anti_berubah').prop('checked') ? 1 : 0,
            force_reverif: isForceReverif ? 1 : 0
          },
          success: function(res) {
            res = res.trim();

            if (res === 'OK') {
              // Sukses pertama kali, semua baik
              $('#id_kantong11').val(kantong);
              $('#no_selang').val($('#no_selang_display').val());
              $('#tab-pengambilan-li').removeClass('disabled');
              $('#tab-pengambilan-li a').removeClass('disabled-tab');
              $('a[href="#tab-pengambilan"]').tab('show');
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Verifikasi kantong berhasil! Kantong dapat digunakan untuk aftap.',
                confirmButtonText: 'OK',
                width: '600px'
              });
            } else if (res.startsWith('INVALID')) {
              // Parameter kurang bagus → rusak
              Swal.fire({
                icon: 'error',
                title: 'Invalid',
                text: res,
                confirmButtonText: 'OK',
                width: '600px'
              });
            } else if (res.indexOf('TIDAK DAPAT DIGUNAKAN') !== -1 || res.indexOf('Rusak') !== -1) {
              // Blok khusus untuk status rusak / tidak boleh pakai
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: res,
                confirmButtonText: 'OK',
                width: '600px'
              });
            } else if (res.indexOf('KADALUWARSA') !== -1) {
              Swal.fire({
                icon: 'error',
                title: 'Kadaluarsa',
                text: res,
                confirmButtonText: 'OK',
                width: '600px'
              });
            } else if (res.indexOf('SUDAH PERNAH diverifikasi') !== -1) {
              // Hanya kasus sudah pernah verifikasi → boleh paksa
              Swal.fire({
                icon: 'info',
                title: 'Re-verifikasi Diperlukan',
                text: res + '\n\nSilakan lakukan pemeriksaan ulang kantong.',
                confirmButtonText: 'OK',
                width: '600px'
              }).then(() => {
                isForceReverif = true; // Set flag untuk submit selanjutnya
                $('#popup_id_kantong').val(kantong); // isi otomatis
                $('#popup_id_kantong').trigger('change'); // ambil no selang + info kantong
              });
            } else {
              // Semua kasus lain (error DB, dll)
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: res || 'Terjadi kesalahan tidak diketahui.',
                confirmButtonText: 'OK',
                width: '600px'
              });
            }
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Koneksi error! Verifikasi tidak tersimpan.',
              confirmButtonText: 'OK',
              width: '600px' // Ukuran lebih besar
            });
          }
        });
      }
    </script>
  </body>

  </html>
<?php } ?>