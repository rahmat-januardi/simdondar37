
<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
  session_start();
  include '../adm/config.php';
  $kode   	= trim($_POST['kode']);
  $kode_sql	= mysqli_real_escape_string($con, $kode);
  $idudd  	= $_SESSION['idudd'];
  $ip		= $_SESSION['ipserver'];
  $namautd            = mysqli_fetch_assoc(mysqli_query($con,"SELECT id,nama,alamat,daerah,telp from utd where aktif='1' limit 1"));
  $utd                = strtoupper($namautd['nama']);

$curl = curl_init();
if (!$curl) {
  die("Couldn't initialize a cURL handle");
}
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/gettransaksidonor.php",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 4,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => array("id" => "$kode"),
));
$response = curl_exec($curl);
curl_close($curl);
//echo $response;
$data   = json_decode($response, true);
$jml    = count($data['data']);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Palang Merah Indonesia</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!--PMI STYLE-->
  <link rel="stylesheet" href="../dist/css/bspmi.css">
</head>
<style type="text/css">
    .padding {
        
        background-image: url('../dist/img/registrasi.png');
        background-size: cover;
    }
    .padding2 {
        padding: 10px 10px 10px 10px;
    }
    .box{
    height: 200px;
    
    }
    .box2{

    height: 80px;

    }
    .copyright{
      bottom: 0;
      width: 100%;
      position: fixed;
      height:50px;
      line-height:50px;
      background:RED;
      color:#fff;
      padding-left: 10px;
    }
    .button3 {
      border-radius: 8px;
      background-color: #159404;
      color: white;
      padding: 15px 12px;
    }

    .button4 {
      border-radius: 8px;
      background-color: #f44336;
      color: white;
      padding: 15px 12px;
    }
    .padd {
      padding-left: 10px;
      padding-top: 5px; 
      padding-right: 225px; 
      font-size: 12px;
    }
    </style>
<body class="padding">
<div class="padd" align="right">
<font style="font-size: 18px;"><b><u><?php echo $utd;?></u></b></font><br>
<?php echo $namautd['alamat'].', '.$namautd['daerah'];?><br>
Telp. <?php echo $namautd['telp'];?><br>
  </div>


<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../dist/img/logo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

<div class="padding2">
    <div class="box2">
    </div>
    
    <?php
        if($jml > 0){
          //echo $response; //tampilkan data pendonor
          //Data transaksi Ayodonor ditemukan ******************************
          ?>
          <meta http-equiv="refresh" content="0; url=formdonor.php?id=<?php echo htmlspecialchars(serialize($data['data'][0]));?>">
          <?php
        } else {
          //cari di lokal ******************************
                  
        /*$curl = curl_init();
        if (!$curl) {
          die("Couldn't initialize a cURL handle");
        }
        curl_setopt_array($curl, array(
          CURLOPT_URL => "192.168.28.231/antridonor/api/apipendonorlokal.php",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => array("id" => "$kode", "ip" => "$ip"),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        //echo $response;
        $data     = json_decode($response, true);
        $jmlpd    = count($data['data']);
		
		echo "Jumlah Data adalah = ".$jmlpd;
		echo "<br>kode Pendonor ".$kode." ke IP Adress ".$ip." gagal eksekusi ";*/
		
		$query = mysqli_query($con,"select * from pendonor where (tglkembali = '0000-00-00' OR tglkembali <= curdate()) AND (tglkembali_apheresis = '0000-00-00' OR tglkembali_apheresis <= curdate()) and (`Kode`='$kode_sql' OR `NoKTP`='$kode_sql' OR `telp`='$kode_sql') AND Cekal='0' limit 1");
		$jmlpd = mysqli_num_rows($query);
		
        if($jmlpd > 0){
			$data = mysqli_fetch_assoc($query);
          //echo $data['data'][0]['Kode'];
          //echo $response; //tampilkan data pendonor
          echo "Data transaksi Ayodonor ditemukan";
          ?>
          <meta http-equiv="refresh" content="0; url=formdonorlokal.php?id=<?php echo $data['Kode'];?>">
          <?php
        } else {
          //cari pendonor di Ad ***********************
          //echo "cari pendonor di ayodonor";
          $curl = curl_init();
        if (!$curl) {
          die("Couldn't initialize a cURL handle");
        }
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/getpendonor.php",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 4,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => array("id" => "$kode"),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        //echo $response;
        $dataad     = json_decode($response, true);
        $jmlad      = count($dataad['data']);
        if($jmlad > 0){
          ?>
          <meta http-equiv="refresh" content="0; url=formdonorAD.php?id=<?php echo htmlspecialchars(serialize($dataad['data'][0]));?>">
          <?php
        }else {
          echo "<p>
          <center> <img src='../dist/img/cloud.png' width='300'><br>MAAF, BELUM SAATNYA ANDA DONOR. SILAHKAN HUBUNGI ADMINISTRASI DONOR
          <meta http-equiv='refresh' content='5; url=donordarah.php'>";
        }
          //pendonor ad
        }
        
      }
    ?>
</div><!--padding-->






<?php //mysqli_close()?>
<!--div class="copyright">
  <p align="center">Copyright @ 2021 | PALANG MERAH INDONESIA
</div-->


<?php //mysqli_close()?>
  <!-- jQuery -->
  <script src="../plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.min.js"></script>
  <!--webcam-->
<script src="../dist/js/webcam.min.js"></script>

  </body>
  </html>
