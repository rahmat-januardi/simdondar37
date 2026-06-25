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

  <!-- Inputmask Plugin -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- SweetAlert2 CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .swal2-popup {
      font-size: 15px !important;
      /* ukuran teks keseluruhan */
    }

    .swal2-title {
      font-size: 22px !important;
      /* judul "Gagal", "Berhasil", dll */
    }

    .swal2-html-container {
      font-size: 15px !important;
      /* isi pesan utama */
    }

    .nav-tabs>li>a {
      color: #333 !important;
      font-size: 14px;
      font-weight: bold;
    }

    .tab-content {
      overflow: visible !important;
      padding: 15px;
      border: 1px solid #ddd;
      border-top: none;
      background: #fff;
    }

    .disabled-tab {
      color: #999 !important;
      cursor: not-allowed !important;
      pointer-events: none !important;
      opacity: 0.6;
    }

    /* Ensure tab navigation stays visible */
    .nav-tabs {
      position: sticky;
      /* Atau fixed jika ingin selalu di atas */
      top: 0;
      /* Sesuaikan dengan tinggi header PMI Anda, misalnya 100px jika header tinggi */
      z-index: 1000;
      /* Pastikan di atas konten lain */
      background: white;
      /* Biar tidak transparan */
      margin-bottom: 0;
    }

    .nav-tabs>li.disabled {
      pointer-events: none;
    }

    .nav-tabs>li.disabled>a {
      color: #999 !important;
      cursor: not-allowed !important;
      background-color: #f5f5f5 !important;
      border-color: #ddd !important;
    }

    .nav-tabs>li.active>a {
      color: #000 !important;
      /* Warna lebih gelap untuk tab active */
    }
  </style>
</head>

<body>

  <?php
  session_start();
  $namauser = $_SESSION['namauser'];
  $lv0 = 'pmi' . $_SESSION['leveluser'];
  require_once('config/dbi_connect.php');
  //cek internet
  function cek_net()
  {
    $dbinected = @fsockopen("dbdonor.pmi.or.id", 80);
    if ($dbinected) {
      $is_conn = true; // jika koneksi tersambung
      fclose($dbinected);
    } else {
      $is_conn = false; //jika koneksi gagal
      fclose($dbinected);
    }
    return $is_conn;
  }
  //
  $msg = "";
  $msgtipe = "alert-info";
  $msgupload = "";

  //hostname
  $td0    = php_uname('n');
  $td0    = strtoupper($td0);
  $td0    = substr($td0, 0, 2);

  $utd = mysqli_fetch_array(mysqli_query($dbi, "SELECT * from utd where `aktif`=1"));
  $idudd = $utd['id'];
  $notrans = $_GET['NoTrans'];

  $prefix = strtoupper(substr($notrans, 0, 2));
  if ($prefix == 'DG') {
    $char = "DG";
    $mu = "";
    $tempat = "0";
  } else {
    $char   = $prefix;
    $tempat = "M";
    $mu = "1";
  }


  //CARI NAMA INSTANSI
  $ins = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama from detailinstansi where KodeDetail='$id'"));
  $namains = $ins['nama'];


  //Shift Petugas
  $shifts = mysqli_query($dbi, "SELECT nama, jam, sampai_jam FROM `shift` ORDER BY nama");
  $current_time = date("H:i:s");
  $shif = "4"; // Default ke Shift IV jika tidak ada kecocokan

  while ($shift = mysqli_fetch_assoc($shifts)) {
    $jam = $shift['jam'];
    $sampai_jam = $shift['sampai_jam'];

    // Validasi format waktu
    if (
      !preg_match("/^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $jam) ||
      !preg_match("/^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $sampai_jam)
    ) {
      continue; // Lewati jika format waktu tidak valid
    }

    // Periksa shift normal
    if ($current_time >= $jam && $current_time <= $sampai_jam) {
      if ($shift['nama'] == "I") {
        $shif = "1";
      } elseif ($shift['nama'] == "II") {
        $shif = "2";
      } elseif ($shift['nama'] == "III") {
        $shif = "3";
      }
      break;
    }

    // Penanganan shift yang melintasi tengah malam
    if ($shift['nama'] == "III" && $sampai_jam < $jam) {
      if ($current_time >= $jam || $current_time <= $sampai_jam) {
        $shif = "3";
        break;
      }
    }
  }
  $today1 = date("Y-m-d H:i:s");
  $today2 = date("Y-m-d");
  $jam_donor = date("H:i:s");
  $tipe_donor = '0';

  $check  = mysqli_query($dbi, "select * from pmi.htransaksi where (NoTrans='$_GET[NoTrans]') limit 1");
  $check1 = mysqli_fetch_assoc($check);

  $check1[KodePendonor]   = str_replace("'", "\'", $check1['KodePendonor']);
  $data   = mysqli_query($dbi, "select Nama from pendonor where Kode='$check1[KodePendonor]' limit 1");
  $data1  = mysqli_fetch_array($data);

  //SIMPAN AFTAP -- START
  if (isset($_POST['simpan'])) {

    $keberhasilan   = $_POST['keberhasilan'];
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
    $GolDarah       = $_POST['goldarah'];
    $Rhesus         = $_POST['Rhesus'];
    $ambil3         = $_POST['ambil'];
    $selesai3       = $_POST['selesai'];
    $petugas        = $_POST['petugas'];
    $no_selang      = $_POST['no_selang'];
    $tglp1          = $today1 . ' ' . $ambil3;

    $ambil2         = str_replace(";", ":", $ambil3);
    $ambil1         = str_replace(",", ":", $ambil2);
    $ambil          = str_replace(".", ":", $ambil1);

    $selesai2       = str_replace(";", ":", $selesai3);
    $selesai1       = str_replace(",", ":", $selesai2);
    $selesai        = str_replace(".", ":", $selesai1);

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

    //echo $jam.'-'.$menit;
    //jika batal/gagal

    $lastDigit = substr($id_kantong, -1);
    if ($lastDigit == 'A') {


      if ($keberhasilan != "0") { //simpan gagal
        $noKantong  = trim(mysqli_real_escape_string($dbi, $_POST['id_kantong11']));
        $kantong    = mysqli_query($dbi, "SELECT * from stokkantong where noKantong ='$noKantong' AND `Status`='0' and StatTempat='1' and kadaluwarsa_ktg >'$today1'");
        $stok1      = mysqli_fetch_array($kantong);
        $numkantong = mysqli_num_rows($kantong);

        //jika kantong ada
        if ($numkantong > 0) { //simpan Gagal
          $pendonor   = mysqli_query($dbi, "select * from pendonor where Kode='$kodependonor' ");
          $pendonor1  = mysqli_fetch_assoc($pendonor);
          //Update Htransaksi
          $pjmldonor = $pendonor1['jumDonor'];
          $jumdonor = $pendonor1['jumDonor'] + 1;

          $tambah = "UPDATE htransaksi
                                  SET diambil='$volume_darah',reaksi='$reaksi',
                                      pengambilan='$keberhasilan',catatan='$catatan',ketBatal='12',jeniskantong='$stok1[jenis]',volumekantong='$stok1[volumeasal]',
                                      nokantong='$id_kantong',petugas='$petugas',
                                      caraambil='$caraambil',status_test='2',Status='2',mu='$mu',gol_darah='$pendonor1[GolDarah]',jam_ambil='$ambil', jam_selesai='$selesai',rhesus='$pendonor1[Rhesus]',jk='$pendonor1[Jk]',pekerjaan='$pendonor1[Pekerjaan]', `tempat`='$tempat'
                                  WHERE (Status='1' and NoTrans='$notrans')";
          //echo $tambah."<br>";
          $htquery    = mysqli_query($dbi, $tambah);
          //Update Htransaksi
          $kembali1   = "UPDATE pendonor SET tglkembali='$kembali',jumDonor='$jumdonor',mu='$mu',up=b'1',up_data='2',tglkembali_apheresis='$kembali' WHERE Kode='$kodependonor'";
          //echo $kembali1."<br>";
          $pdquery    = mysqli_query($dbi, $kembali1);

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
            CURLOPT_POSTFIELDS => array('Kode' => $kodependonor, 'tglkembali' => $kembali, 'metode' => 'update', 'pjmldonor' => $pjmldonor),
          ));
          $response = curl_exec($curlinsdn);
          $datains  = json_decode($response, true);
          //echo "<pre>"; print_r($response); echo "</pre>";
          curl_close($curlinsdn);


          $ono_kantong0 = substr($id_kantong, 0, -1);
          $tambah2    = "UPDATE stokkantong SET Status='5',hasil='5', tgl_Aftap='$tglp1',gol_darah='$GolDarah',noSelang='$no_selang',RhesusDrh='$Rhesus',produk='WB',sah='0',kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 35 day),mu='$mu',lama_pengambilan='$lama_pengambilan', AsalUTD='$idudd' WHERE noKantong='$id_kantong'";
          //echo $tambah2."<br>";
          $skquery    = mysqli_query($dbi, $tambah2);
          $tambah4    = "UPDATE htransaksi set donorbaru='1' where NoTrans='$notrans' and donorke >'1' ";
          $tambah4x    = mysqli_query($dbi, $tambah4);
          //echo $tambah4."<br>";
          $tambah5    = "UPDATE stokkantong set lama_pengambilan='$lama_pengambilan', gol_darah='$GolDarah',RhesusDrh='$Rhesus'  WHERE noKantong like '$ono_kantong0%'";
          $sk2query    = mysqli_query($dbi, $tambah5);
          //echo $tambah5."<br>";
          if ($sk2query) {
            //=======Audit Trial====================================================================================
            $log_mdl = 'PENGAMBILAN';
            $log_aksi = 'Pengambilan darah: ' . $notrans . ' Pendonor: ' . $kodependonor . ' Kantong: ' . $id_kantong . ' status: ' . $keberhasilan;
            include_once "user_log.php";
            //=====================================================================================================

  ?>
            <div class="row">
              <div class="col-lg-12">
                <div class="alert alert-success alert-dismissable" role="alert">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                  <strong>Data Gagal Aftap</strong> Berhasil Entry
                </div>
              </div>
            </div>
            <META http-equiv="refresh" content="2; url=<?= $lv0 ?>.php?module=spengambilan"><?php

                                                                                          }
                                                                                        } else { //Nomor Kantong Tidak Ada
                                                                                            ?>
          <div class="row">
            <div class="col-lg-12">
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

                                                                                        //Jika Durasi tidak memenuhi syarat
                                                                                        if ($lama_pengambilan >= 1) {

                                                                                          $noKantong  = trim(mysqli_real_escape_string($dbi, $_POST['id_kantong11']));
                                                                                          $kantong    = mysqli_query($dbi, "SELECT * from stokkantong where noKantong ='$noKantong' AND `Status`='0' and StatTempat='1' and kadaluwarsa_ktg >'$today1'");
                                                                                          $stok1      = mysqli_fetch_array($kantong);
                                                                                          $numkantong = mysqli_num_rows($kantong);

                                                                                          //jika kantong ada
                                                                                          if ($numkantong > 0) { //simpan berhasil
                                                                                            $pendonor   = mysqli_query($dbi, "select * from pendonor where Kode='$kodependonor' ");
                                                                                            $pendonor1  = mysqli_fetch_assoc($pendonor);
                                                                                            //Update Htransaksi
                                                                                            $jumdonor = $pendonor1['jumDonor'] + 1;
                                                                                            $pjmldonor = $pendonor1['jumDonor'];

                                                                                            $tambah = "UPDATE htransaksi
                                SET diambil='$volume_darah',reaksi='$reaksi',
                                    pengambilan='$keberhasilan',catatan='$catatan',ketBatal='-',jeniskantong='$stok1[jenis]',volumekantong='$stok1[volumeasal]',
                                    nokantong='$id_kantong',petugas='$petugas',
                                    caraambil='$caraambil',status_test='2',Status='2',mu='$mu',gol_darah='$pendonor1[GolDarah]',jam_ambil='$ambil', jam_selesai='$selesai',rhesus='$pendonor1[Rhesus]',jk='$pendonor1[Jk]',pekerjaan='$pendonor1[Pekerjaan]', `tempat`='$tempat'
                                WHERE (Status='1' and NoTrans='$notrans')";
                                                                                            //echo $tambah."<br>";
                                                                                            $htquery    = mysqli_query($dbi, $tambah);
                                                                                            //Update Htransaksi
                                                                                            $kembali1   = "UPDATE pendonor SET tglkembali='$kembali',jumDonor='$jumdonor',mu='$mu',up=b'1',up_data='2',tglkembali_apheresis='$kembali' WHERE Kode='$kodependonor'";
                                                                                            //echo $kembali1."<br>";
                                                                                            $pdquery    = mysqli_query($dbi, $kembali1);
                                                                                            $ono_kantong0 = substr($id_kantong, 0, -1);
                                                                                            $tambah2    = "UPDATE stokkantong SET Status='1',tgl_Aftap='$tglp1',noSelang='$no_selang',gol_darah='$GolDarah',RhesusDrh='$Rhesus',produk='WB',sah='0',kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 35 day),mu='$mu',lama_pengambilan='$lama_pengambilan', AsalUTD='$idudd' WHERE noKantong='$id_kantong'";
                                                                                            //echo $tambah2."<br>";
                                                                                            $skquery    = mysqli_query($dbi, $tambah2);

                                                                                            $tambah4    = "UPDATE htransaksi set donorbaru='1' where NoTrans='$notrans' and donorke > '1' ";
                                                                                            $tambah4x    = mysqli_query($dbi, $tambah4);
                                                                                            //echo $tambah4."<br>";


                                                                                            $tambah5    = "UPDATE stokkantong set `lama_pengambilan`='$lama_pengambilan', `Status`='1', `tgl_Aftap`='$tglp1', `gol_darah`='$GolDarah',`RhesusDrh`='$Rhesus'  WHERE noKantong like '$ono_kantong0%'";
                                                                                            $sk2query    = mysqli_query($dbi, $tambah5);

                                                                                            //CURL DB NASIONAL
                                                                                            $curlup = curl_init();
                                                                                            curl_setopt_array($curlup, array(
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
                                                                                            $response = curl_exec($curlup);
                                                                                            $datains  = json_decode($response, true);
                                                                                            //echo "<pre>"; print_r($response); echo "</pre>";
                                                                                            curl_close($curlup);

                                                                                            //echo $tambah5."<br>";
                                                                                            if ($sk2query) {
                                                                                              //=======Audit Trial====================================================================================
                                                                                              $log_mdl = 'PENGAMBILAN';
                                                                                              $log_aksi = 'Pengambilan darah Biasa: ' . $notrans . ' Pendonor: ' . $kodependonor . ' Kantong: ' . $id_kantong . ' status: ' . $keberhasilan;
                                                                                              include_once "user_log.php";
                                                                                              //=====================================================================================================
          ?>
              <div class="row">
                <div class="col-lg-12">
                  <div class="alert alert-success alert-dismissable" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <strong>Data Aftap</strong> Berhasil Entry
                  </div>
                </div>
              </div>
              <META http-equiv="refresh" content="2; url=<?= $lv0 ?>.php?module=spengambilan"><?php

                                                                                            }
                                                                                          } else { //Selesai Entry Aftap
                                                                                              ?>
            <div class="row">
              <div class="col-lg-12">
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
            Swal.fire({
              icon: 'error',
              title: 'Durasi Pengambilan Salah!',
              text: 'Periksa Jam Ambil dan Jam Selesai',
              confirmButtonText: 'OK'
            });
          </script>
      <?php    }
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
  <?php }
                                                                                  }
                                                                                  //SIMPAN AFTAP -- END

  ?>

  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <br>

        <div class="panel with-nav-tabs panel-primary" id="shadow1">

          <div class="panel-heading">
            <div class="row">
              <div class="col-lg-12" align="center">
                <div class="panel-title">
                  <h4><strong>PENGAMBILAN DARAH PENDONOR</strong></h4>
                </div>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>

          <!--form cari start-->
          <div class="panel-body">
            <ul class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active">
                <a href="#tab-verifikasi" aria-controls="tab-verifikasi" role="tab" data-toggle="tab">
                  <i class="fa fa-check-square-o"></i> 1. Verifikasi Kantong
                </a>
              </li>
              <li role="presentation" id="tab-pengambilan-li" class="disabled">
                <a href="#tab-pengambilan" aria-controls="tab-pengambilan" role="tab" data-toggle="tab" class="disabled-tab">
                  <i class="fa fa-tint"></i> 2. Pengambilan Darah
                </a>
              </li>
            </ul>

            <div class="tab-content">

              <!-- TAB 1: VERIFIKASI KANTONG -->
              <div role="tabpanel" class="tab-pane active" id="tab-verifikasi">
                <div class="row" style="margin-top: 5px;">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><strong>Nomor Kantong (Barcode)</strong></label>
                      <input type="text" class="form-control input-md text-center" id="popup_id_kantong" placeholder="Scan atau ketik nomor kantong" autofocus required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><strong>Nomor Selang</strong></label>
                      <input type="text" class="form-control input-md text-center" id="no_selang_display" placeholder="Scan atau ketik nomor selang">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-bottom: 5px;">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><strong>Tanggal Buka Kemasan <span class="text-danger">*</span></strong></label>
                      <input type="datetime-local"
                        name="tanggal_buka"
                        id="tanggal_buka"
                        class="form-control"
                        required>
                      <span class="help-block text-muted" style="font-size:11px;">
                        <i class="fa fa-info-circle"></i> Tanggal saat kemasan kantong pertama kali dibuka/dikeluarkan dari bungkus.
                      </span>
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

                <div class="row g-3 mb-4" style="display:none;">
                  <div class="col-md-4">
                    <label class="form-label">Merk Kantong</label>
                    <input type="text" class="form-control form-control-lg text-center" id="merk_kantong"
                      placeholder="Merk Kantong" readonly>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Jenis Kantong</label>
                    <input type="text" class="form-control form-control-lg text-center" id="jenis_kantong"
                      placeholder="Jenis Kantong" readonly>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Volume Kantong (ml)</label>
                    <input type="text" class="form-control form-control-lg text-center" id="volume_kantong"
                      placeholder="Volume" readonly>
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
                    <input type="text"
                      class="form-control font-weight-bold"
                      id="keterangan_auto"
                      readonly
                      style="background:#f8f9fa;">
                  </div>
                </div>
                <hr>

                <div class="alert alert-warning">
                  <strong>Catatan:</strong> Centang hanya kondisi yang benar-benar sesuai dengan keadaan kantong.
                </div>

                <div class="text-right">
                  <button type="button" class="btn btn-success btn-lg" onclick="submitValidasiKantong()">
                    <i class="fa fa-check"></i> Simpan Verifikasi Kantong
                  </button>
                </div>

              </div>

              <!-- TAB 2: PENGAMBILAN DARAH -->
              <div role="tabpanel" class="tab-pane" id="tab-pengambilan">
                <form class="form-horizontal" method="POST" id="ambildarah" name="ambildarah" onsubmit="return validasiregistrasi()">
                  <div class="row" style="margin-top: 5px;">
                    <div class="col-lg-6">
                      <!--row1--->
                      <div class="form-group">
                        <label class="control-label col-lg-4">Kode Pendonor</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" name="kodependonor" value="<?php echo $check1['KodePendonor']; ?>" id="iddonor" readonly required>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-lg-4">Pengambilan</label>
                        <div class="col-lg-4">
                          <div class="radio-custom radio-primary">
                            <label class="radio-inline"><input type="radio" id="inlineRadio1" value="0" name="keberhasilan" style="margin-top:1px;" required>Berhasil</label>
                            <label class="radio-inline"><input type="radio" id="inlineRadio2" value="1" name="keberhasilan" style="margin-top:1px;">Batal</label><br>
                            <label class="radio-inline"><input type="radio" id="inlineRadio3" value="2" name="keberhasilan" style="margin-top:1px;">Gagal</label>
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <select name="catatan" class="form-control" id="catatan">
                            <option value="">-- Tidak Ada Catatan --</option>
                            <option value="Mislek">Mislek</option>
                            <option value="Saran Dokter">Saran Dokter</option>
                            <option value="Permintaan Pendonor">Permintaan Pendonor</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-lg-4">Diambil Sebanyak (cc)</label>
                        <div class="col-lg-3">
                          <input type="text" name="volume_darah" class="form-control" id="vol" value="350" required>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-lg-4">Reaksi Donor</label>
                        <div class="col-lg-8">
                          <select name="reaksi" class="form-control">
                            <option value="Mual">Mual</option>
                            <option value="Pusing">Pusing</option>
                            <option value="Pingsan">Pingsan</option>
                            <option selected value="Normal">Tidak Ada Keluhan</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-lg-4">Jam Mulai</label>
                        <div class="col-lg-3">
                          <input type="hidden" name="caraambil" value="0" class="form-control" id="caraambil">
                          <input name="ambil" value="" class="form-control" id="jam_ambil" placeholder="mm:dd" autocomplete="off" required>
                        </div>
                        <div class="col-lg-2">
                          <label class="control-label"> Jam Selesai</label>
                        </div>
                        <div class="col-lg-3">
                          <input name="selesai" value="" class="form-control" id="jam_selesai" placeholder="mm:dd" autocomplete="off" required>
                        </div>
                      </div>

                      <!-- Field Nomor Kantong dan Selang Dipindah ke Sini (Hasil Verifikasi) -->
                      <div class="form-group">
                        <label class="control-label col-lg-4">Nomor Kantong</label>
                        <div class="col-lg-8">
                          <input name="id_kantong11" id="id_kantong11" class="form-control" autocomplete="off" placeholder="Hasil verifikasi akan muncul di sini" readonly required>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-lg-4">Nomor Selang</label>
                        <div class="col-lg-8">
                          <input name="no_selang" id="no_selang" class="form-control" autocomplete="off" placeholder="Hasil verifikasi akan muncul di sini" required>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-lg-4">Petugas Aftap</label>
                        <div class="col-lg-8">
                          <select class="form-control" id="petugas" name="petugas" style="width: 100%;" required>
                            <option class="form-control" value="">-- Pilih Petugas --</option>
                            <?php
                            $aftaper = mysqli_query($dbi, "SELECT * from user where bagian like '%AFTAP%' or multi_bagian like '%AFTAP%' order by nama_lengkap ");
                            while ($ptgsaft = mysqli_fetch_array($aftaper)) { ?>
                              <option class="form-control" value=<?php echo $ptgsaft['id_user']; ?>><?php echo $ptgsaft['nama_lengkap']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <!--row1--->
                    </div>
                    <div class="col-lg-6">
                      <!--row2--->
                      <div class="form-group">
                        <label class="control-label col-lg-4">Nama Pendonor</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" value="<?php echo strtoupper($data1[Nama]); ?>" readonly>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-lg-4">Donor Ke</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" value="<?php echo $check1[donorke]; ?> Kali" readonly>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-lg-4">Golongan Darah</label>
                        <div class="col-lg-8">
                          <input type="text" name="goldarah" class="form-control" value="<?php echo $check1[gol_darah] . ' (' . $check1[rhesus] . ')'; ?>" readonly>
                          <input type="hidden" name="goldarah" class="form-control" value="<?php echo $check1[gol_darah]; ?>">
                          <input type="hidden" name="Rhesus" class="form-control" value="<?php echo $check1[rhesus]; ?>">

                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-lg-4">Berat Badan</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" value="<?php echo $check1[beratBadan]; ?> Kg" readonly>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-lg-4">Tekanan Darah</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" value="<?php echo $check1[tensi]; ?> mmHg" readonly>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-lg-4">Hemmoglobin</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" value="<?php echo $check1[Hb]; ?> g/dL" readonly>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-lg-4">Suhu</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" value="<?php echo $check1[suhu]; ?>" readonly>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-lg-4">Nadi</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" value="<?php echo $check1[nadi]; ?> BPM" readonly>
                        </div>
                      </div>

                      <!--row2--->
                    </div>
                  </div>
                  <!--form cari end-->
                  <br>
                  <div class="panel-footer">
                    <button class="btn btn-success" onclick="history.back()"><i class="fa fa-arrow-circle-o-left"></i> Kembali</button>
                    <button name="simpan" id="simpan" type="submit" class="btn btn-danger"><i class="fa fa-save"></i> Simpan</button>
                  </div>

                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="loader" class="tengah"></div>
  </div>

</body>

</html>
<script>
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $(".loader").fadeOut();


    $("#ambildarah").on("keypress", function(event) {
      console.log("aaya");
      var keyPressed = event.keyCode || event.which;
      if (keyPressed === 13) {
        //alert("You pressed the Enter key!!");
        event.preventDefault();
        return false;
      }
    });

    // Prevent clicking on disabled tabs
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

  });
</script>

<script>
  // Saat nomor kantong di-scan/ketik, otomatis ambil no selang
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

            // === AUTO FILL TANGGAL BUKA KEMASAN ===
            if (parts[4]) {
              $('#tanggal_buka').val(parts[4]); // langsung isi datetime-local

              // Tampilan cantik (opsional tapi recommended)
              $('#tanggal_buka').css({
                'background-color': '#d4edda',
                'border-color': '#28a745',
                'font-weight': 'bold'
              });

              // Info kecil di keterangan
              // $('#keterangan_auto').val('✅ Tanggal buka kemasan diambil otomatis dari verifikasi sebelumnya');
              // $('#keterangan_auto').css('color', '#155724');
            } else {
              $('#tanggal_buka').val('').css('background-color', '');
            }
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

  // Membuat checkbox mutually exclusive untuk selang, jarum, antikoagulan
  $('#selang_baik').on('change', function() {
    if ($(this).is(':checked')) {
      $('#selang_tertekuk').prop('checked', false);
    }
  });
  $('#selang_tertekuk').on('change', function() {
    if ($(this).is(':checked')) {
      $('#selang_baik').prop('checked', false);
    }
  });

  $('#jarum_baik').on('change', function() {
    if ($(this).is(':checked')) {
      $('#jarum_bengkok').prop('checked', false);
    }
  });
  $('#jarum_bengkok').on('change', function() {
    if ($(this).is(':checked')) {
      $('#jarum_baik').prop('checked', false);
    }
  });

  $('#anti_jernih').on('change', function() {
    if ($(this).is(':checked')) {
      $('#anti_berubah').prop('checked', false);
    }
  });
  $('#anti_berubah').on('change', function() {
    if ($(this).is(':checked')) {
      $('#anti_jernih').prop('checked', false);
    }
  });

  let isForceReverif = false; // Flag global untuk force re-verif

  function submitValidasiKantong() {
    const kantong = $('#popup_id_kantong').val().trim();
    const tanggal_buka = $('#tanggal_buka').val().trim();

    if (!kantong) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Nomor kantong belum diisi!',
        confirmButtonText: 'OK',
        width: '600px'
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
    // Kirim ke server
    $.ajax({
      url: 'modul/simpan_verifikasi_kantong.php',
      type: 'POST',
      data: {
        submit_verif: '1',
        no_kantong: kantong,
        tanggal_buka: tanggal_buka,
        kemasan_utuh: $('#kemasan_utuh').is(':checked') ? 1 : 0,
        kemasan_expired: $('#kemasan_expired').is(':checked') ? 1 : 0,
        kemasan_bocor: $('#kemasan_bocor').is(':checked') ? 1 : 0,
        selang_baik: $('#selang_baik').is(':checked') ? 1 : 0,
        selang_tertekuk: $('#selang_tertekuk').is(':checked') ? 1 : 0,
        jarum_baik: $('#jarum_baik').is(':checked') ? 1 : 0,
        jarum_bengkok: $('#jarum_bengkok').is(':checked') ? 1 : 0,
        anti_jernih: $('#anti_jernih').is(':checked') ? 1 : 0,
        anti_berubah: $('#anti_berubah').is(':checked') ? 1 : 0,
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
            $('#popup_id_kantong').trigger('change'); // ambil no selang
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

  // Fungsi untuk generate keterangan otomatis
  function updateKeteranganSimpel() {
    // Daftar semua checkbox yang WAJIB dicek (baik atau tidak baik)
    const allCheckboxes = [
      // '#kemasan_utuh', '#kemasan_expired', '#kemasan_bocor',
      '#selang_baik', '#selang_tertekuk',
      '#jarum_baik', '#jarum_bengkok',
      '#anti_jernih', '#anti_berubah'
    ];

    // Cek apakah SEMUA checkbox sudah disentuh (ada yang checked)
    let semuaSudahDicek = true;
    for (let sel of allCheckboxes) {
      if (!$(sel).is(':checked') && $(sel).attr('type') === 'checkbox') {
        // Khusus mutually exclusive seperti selang_baik & selang_tertekuk,
        // cukup salah satu yang checked sudah dianggap dicek
        if (sel === '#selang_baik' || sel === '#selang_tertekuk') {
          if (!$('#selang_baik').is(':checked') && !$('#selang_tertekuk').is(':checked')) {
            semuaSudahDicek = false;
            break;
          }
        } else if (sel === '#jarum_baik' || sel === '#jarum_bengkok') {
          if (!$('#jarum_baik').is(':checked') && !$('#jarum_bengkok').is(':checked')) {
            semuaSudahDicek = false;
            break;
          }
        } else if (sel === '#anti_jernih' || sel === '#anti_berubah') {
          if (!$('#anti_jernih').is(':checked') && !$('#anti_berubah').is(':checked')) {
            semuaSudahDicek = false;
            break;
          }
        } else {
          // Untuk kemasan (3 checkbox terpisah), harus semua dicek
          semuaSudahDicek = false;
          break;
        }
      }
    }

    if (!semuaSudahDicek) {
      $('#keterangan_auto')
        .val("Silakan centang semua parameter pemeriksaan terlebih dahulu")
        .css({
          'color': '#6c757d', // abu-abu
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

  // Bind ke semua checkbox yang relevan
  $(document).ready(function() {
    const checkboxes = [
      '#kemasan_utuh', '#kemasan_expired', '#kemasan_bocor',
      '#selang_baik', '#selang_tertekuk',
      '#jarum_baik', '#jarum_bengkok',
      '#anti_jernih', '#anti_berubah'
    ];

    checkboxes.forEach(function(sel) {
      $(sel).on('change', updateKeteranganSimpel);
    });

    // Jalankan saat modal muncul (untuk inisialisasi)
    updateKeteranganSimpel();
  });

  $(document).ready(function() {
    $('#petugas').select2();
    $('#jam_ambil, #jam_selesai').inputmask("99:99", {
      placeholder: "mm:dd",
      insertMode: false
    });

    // Validasi submit form: pastikan verifikasi kantong sudah dilakukan
    $('#ambildarah').on('submit', function(e) {
      if ($('#id_kantong11').val() === '' || $('#no_selang').val() === '') {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'Verifikasi Belum Dilakukan',
          text: 'Silakan verifikasi kantong darah terlebih dahulu!',
        });
      }
    });
  });
</script>