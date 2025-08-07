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
$namauser = $_SESSION['namauser'];
$lv0='pmi'.$_SESSION['leveluser'];
require_once('config/dbi_connect.php');
//cek internet
function cek_net(){
    $dbinected = @fsockopen("dbdonor.pmi.or.id", 80);
        if ($dbinected) {
        $is_conn = true; // jika koneksi tersambung
        fclose($dbinected);
    }else{
        $is_conn = false; //jika koneksi gagal
        fclose($dbinected);
        }
    return $is_conn;
    }
//
$msg="";
$msgtipe="alert-info";
$msgupload="";

//hostname
$td0    = php_uname('n');
$td0    = strtoupper($td0);
$td0    = substr($td0,0,2);
if ($td0 == 'SE'){
    $char = "DG";
    $mu="";
    $tempat = "0";
} else {
    $char   = $td0;
    $tempat = "M";
    $mu="1";
        
    
}

$utd = mysqli_fetch_array(mysqli_query($dbi, "SELECT * from utd where `aktif`=1"));
$notrans = $_GET['NoTrans'];




  //CARI NAMA INSTANSI
  $ins = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama from detailinstansi where KodeDetail='$id'"));
  $namains = $ins['nama'];


  //Shift Petugas
  $shift  = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
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

  $check  = mysqli_query($dbi, "select * from pmi.htransaksi where (NoTrans='$_GET[NoTrans]') limit 1");
  $check1 = mysqli_fetch_assoc($check);

  $check1[KodePendonor]   = str_replace("'", "\'", $check1['KodePendonor']);
  $data   = mysqli_query($dbi, "select Nama from pendonor where Kode='$check1[KodePendonor]' limit 1");
  $data1  = mysqli_fetch_array($data);

//SIMPAN AFTAP -- START
if (isset($_POST['simpan'])){
    
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
    
    $lastDigit=substr($id_kantong, -1);
    if($lastDigit=='A'){
    
    
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
          $jumdonor = $pendonor1['jumDonor']+1;
            
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
              CURLOPT_POSTFIELDS => array('Kode' => $kodependonor,'tglkembali' => $kembali, 'metode' => 'update', 'pjmldonor' => $pjmldonor ),
            ));
            $response = curl_exec($curlinsdn);
            $datains  = json_decode($response, true);
            //echo "<pre>"; print_r($response); echo "</pre>";
            curl_close($curlinsdn);
            
            
          $ono_kantong0 = substr($id_kantong, 0, -1);
          $tambah2    = "UPDATE stokkantong SET Status='5',hasil='5', tgl_Aftap='$tglp1',gol_darah='$GolDarah',noSelang='$no_selang',RhesusDrh='$Rhesus',produk='WB',sah='0',kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 35 day),mu='$mu',lama_pengambilan='$lama_pengambilan' WHERE noKantong='$id_kantong'";
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
               $log_mdl ='PENGAMBILAN';
               $log_aksi='Pengambilan darah: '.$notrans.' Pendonor: '.$kodependonor.' Kantong: '.$id_kantong.' status: '.$keberhasilan;
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
              <META http-equiv="refresh" content="2; url=<?=$lv0?>.php?module=spengambilan"><?php

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
      if ($lama_pengambilan >= 1){

      $noKantong  = trim(mysqli_real_escape_string($dbi, $_POST['id_kantong11']));
      $kantong    = mysqli_query($dbi, "SELECT * from stokkantong where noKantong ='$noKantong' AND `Status`='0' and StatTempat='1' and kadaluwarsa_ktg >'$today1'");
      $stok1      = mysqli_fetch_array($kantong);
      $numkantong = mysqli_num_rows($kantong);

      //jika kantong ada
      if ($numkantong > 0) { //simpan berhasil
        $pendonor   = mysqli_query($dbi, "select * from pendonor where Kode='$kodependonor' ");
        $pendonor1  = mysqli_fetch_assoc($pendonor);
        //Update Htransaksi
        $jumdonor = $pendonor1['jumDonor']+1;
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
        $tambah2    = "UPDATE stokkantong SET Status='1',tgl_Aftap='$tglp1',noSelang='$no_selang',gol_darah='$GolDarah',RhesusDrh='$Rhesus',produk='WB',sah='0',kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 35 day),mu='$mu',lama_pengambilan='$lama_pengambilan' WHERE noKantong='$id_kantong'";
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
          CURLOPT_POSTFIELDS => array('Kode' => $kodependonor,'tglkembali' => $kembali, 'metode' => 'update', 'pjmldonor' => $jumdonor ),
        ));
        $response = curl_exec($curlup);
        $datains  = json_decode($response, true);
        //echo "<pre>"; print_r($response); echo "</pre>";
        curl_close($curlup);

        //echo $tambah5."<br>";
        if ($sk2query) {
            //=======Audit Trial====================================================================================
           $log_mdl ='PENGAMBILAN';
           $log_aksi='Pengambilan darah Biasa: '.$notrans.' Pendonor: '.$kodependonor.' Kantong: '.$id_kantong.' status: '.$keberhasilan;
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
          <META http-equiv="refresh" content="2; url=<?=$lv0?>.php?module=spengambilan"><?php

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
                                                                  }else{?>
                                                                    <script> alert("Durasi pengambilan Salah...!\nPeriksa Jam Ambil dan Jam Selesai"); </script> 
                                                              <?php    }
                                                                  }

    }else{?>
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

                        <div class="panel-heading" >
                            <div class="row">
                                <div class="col-lg-12" align="center">
                                    <div class="panel-title"><h4><strong>PENYADAPAN DARAH PENDONOR</strong></h4></div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!--form cari start-->
                        <div class="panel-body">
                            <form class="form-horizontal" method="POST"  id="ambildarah" name="ambildarah"  onsubmit="return validasiregistrasi()">
                            <div class="row" >
                                <div class="col-lg-6">
                                <!--row1--->
                                <div class="form-group">
                                        <label class="control-label col-lg-4">Kode Pendonor</label>
                                        <div class="col-lg-8">
                                            <input type="text"  class="form-control" name="kodependonor" value="<?php echo $check1[KodePendonor];?>" id="iddonor"  readonly required>
                                        </div>
                                </div>

                                <div class="form-group">
                                        <label class="control-label col-lg-4">Pengambilan</label>
                                        <div class="col-lg-4">
                                            <div class="radio-custom radio-primary">
                                                <label class="radio-inline"><input type="radio" id="inlineRadio1" value="0" name="keberhasilan" style="margin-top:1px;" required>Berhasil</label>
                                                <label class="radio-inline"><input type="radio" id="inlineRadio2" value="1" name="keberhasilan" style="margin-top:1px;">Batal</label>
                                                <label class="radio-inline"><input type="radio" id="inlineRadio3" value="2" name="keberhasilan" style="margin-top:1px;">Gagal</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <select name="catatan" class="form-control">
                                              <option value="">Pilih Jika Gagal</option>
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
                                        <label class="control-label col-lg-4">Jam Aftap</label>
                                        <div class="col-lg-3">
                                            <input type="hidden" name="caraambil" value="0" class="form-control" id="caraambil">
                                            <input name="ambil" value="" class="form-control" id="jam_ambil" placeholder="mm:dd" autocomplete="off" required>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="control-label"> Selesai </label>
                                        </div>
                                        <div class="col-lg-3">
                                            <input name="selesai" value="" class="form-control" id="jam_ambil" placeholder="mm:dd" autocomplete="off" required>
                                        </div>
                                </div>

                                <div class="form-group">
                                        <label class="control-label col-lg-4">Nomor Kantong</label>
                                        <div class="col-lg-8">
                                            <input name="id_kantong11" id="id_kantong11" onkeypress="search(event)" class="form-control" autocomplete="off" placeholder="Nomor Kantong" required>
                                        </div>
                                </div>

                                <div class="form-group">
                                        <label class="control-label col-lg-4">Nomor Selang</label>
                                        <div class="col-lg-8">
                                            <input name="no_selang" id="no_selang" onkeydown="chang(event,this);" class="form-control" autocomplete="off" placeholder="Nomor Selang" required>
                                        </div>
                                </div>

                                <div class="form-group">
                                        <label class="control-label col-lg-4">Petugas Aftap</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" name="petugas" required>
                                                <option class="form-control" value="">-- Pilih Petugas --</option>
                                                <?php
                                                $aftaper = mysqli_query($dbi,"SELECT * from user where bagian like '%AFTAP%' order by nama_lengkap ");
                                                while ($ptgsaft = mysqli_fetch_array($aftaper)){?>
                                                    <option class="form-control" value=<?php echo $ptgsaft['id_user'];?>><?php echo $ptgsaft['nama_lengkap'];?></option>
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
            <input type="text"  class="form-control" value="<?php echo strtoupper($data1[Nama]);?>" readonly>
        </div>
</div>
<div class="form-group">
        <label class="control-label col-lg-4">Donor Ke</label>
        <div class="col-lg-8">
            <input type="text"  class="form-control" value="<?php echo $check1[donorke];?> Kali" readonly>
        </div>
</div>
<div class="form-group">
        <label class="control-label col-lg-4">Golongan Darah</label>
        <div class="col-lg-8">
            <input type="text"  name="goldarah" class="form-control" value="<?php echo $check1[gol_darah].' ('.$check1[rhesus].')';?>" readonly>
            <input type="hidden"  name="goldarah" class="form-control" value="<?php echo $check1[gol_darah];?>">
            <input type="hidden"  name="Rhesus" class="form-control" value="<?php echo $check1[rhesus];?>">

        </div>
</div>
<div class="form-group">
        <label class="control-label col-lg-4">Berat Badan</label>
        <div class="col-lg-8">
            <input type="text"  class="form-control" value="<?php echo $check1[beratBadan];?> Kg" readonly>
        </div>
</div>
<div class="form-group">
        <label class="control-label col-lg-4">Tekanan Darah</label>
        <div class="col-lg-8">
            <input type="text"  class="form-control" value="<?php echo $check1[tensi];?> mmHg" readonly>
        </div>
</div>
<div class="form-group">
        <label class="control-label col-lg-4">Hemmoglobin</label>
        <div class="col-lg-8">
            <input type="text"  class="form-control" value="<?php echo $check1[Hb];?> g/dL" readonly>
        </div>
</div>
<div class="form-group">
        <label class="control-label col-lg-4">Suhu</label>
        <div class="col-lg-8">
            <input type="text"  class="form-control" value="<?php echo $check1[suhu];?>" readonly>
        </div>
</div>
<div class="form-group">
        <label class="control-label col-lg-4">Nadi</label>
        <div class="col-lg-8">
            <input type="text"  class="form-control" value="<?php echo $check1[nadi];?> BPM" readonly>
        </div>
</div>

                                <!--row2--->
                                </div>
                            </div>
                        <!--form cari end-->
                        <br>
                        <div class="panel-footer">
                            <button class="btn btn-success" onclick="history.back()"><i class="fa fa-arrow-circle-o-left"></i> Kembali</button>
                            <button name="simpan" id="simpan" type="submit" class="btn btn-danger"><i class="fa fa-save"></i>  Simpan</button>
                            
                        </div>
                        </form>
                    </div>
            </div>
        </div>
    </div>
    <div class="loader" class="tengah"></div>



</body>
</html>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
        $(".loader").fadeOut();
                      
                      
                              $("#ambildarah").on("keypress", function (event) {
                                  console.log("aaya");
                                  var keyPressed = event.keyCode || event.which;
                                  if (keyPressed === 13) {
                                      //alert("You pressed the Enter key!!");
                                      event.preventDefault();
                                      return false;
                                  }
                              });
                      
                      
        
    });
    
    
</script>

<script>
function search(event) {
  let value= event.which;
  if(value === 13){
      //onkeydown="return event.key != 'Enter';"
      //call your function or anything else
      
      getkantong = document.getElementById("id_kantong11").value;
      
      
      $.ajax({
          method:"POST",
          url:"carinoselang.php",
          data: {ktg : getkantong},
          success:function(server_response){
             document.ambildarah.no_selang.value = server_response;
                          
       }
      });
      //alert('Nomor Kantong : ' + getkantong);
      document.getElementById("no_selang").focus();
  }
}
    
</script>



