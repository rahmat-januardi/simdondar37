
<?php
  //session_start();
  error_reporting (E_ALL ^ E_NOTICE);
  include '../adm/config.php';

  if ($_SESSION['ipserver']==''){?>
    <META http-equiv="refresh" content="0; url=index.php">
    <?php
  }
  
  $namautd            = mysqli_fetch_assoc(mysqli_query($con,"SELECT id,nama,alamat,daerah,telp from utd where aktif='1' limit 1"));
  $utd                = strtoupper($namautd['nama']);
  $_SESSION['idudd']  = $namautd['id'];
  $ip	              	= $_SESSION['ipserver'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PALANG MERAH INDONESIA</title>

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
    .box{
    height: 125px;
    
    }
    .padd {
      padding-left: 10px;
      padding-top: 5px; 
      padding-right: 225px; 
      font-size: 12px;
    }

    .box2{

    height: 50px;

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


  
  <div class="box" align="center">
  </div>

  <div class="row" align="center">
    <div class="col-lg-12">
        <a href="https://play.google.com/store/apps/details?id=solo.pmi.id.pmi_solo"><img src="../dist/img/logo.png" width="80px"></a><br>
        
        <!--font color="red"><b><?php echo $utd;?></b></font-->
      </div>
    </div>
<p>
  

<center>  
<div class="login-box" align="center">
  <!-- /.login-logo -->
  <div id="wrapper">
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><img src="../dist/img/registrasidonor.png" width="250px"></p>

      <form action="postdonor.php" method="post">
        <div class="input-group mb-3">
          <input type="text" name="kode" class="form-control" placeholder="ketik kode pendonor / No. KTP / No. HP" autocomplete="off" autofocus required >
        </div>
        

        <div class="row">
          <div class="col-12">
            <button type="submit" name="cari" class="btn btn-success btn-block" style=";box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 4px 8px 0 rgba(0, 0, 0, 0.19); ">MASUK</button>
          </div>
        </div>
      </form>

  




    </div>
    <!-- /.login-card-body -->
  </div>
  
</div>
  </div>
  </div>
  </center>

  <div class="copyright">
      <p align="center"><a href="https://pmi.or.id"><font style="color:white">Copyright @ 2021 | PALANG MERAH INDONESIA</a> 
  </div>


<?php //mysqli_close()?>
  <!-- jQuery -->
  <script src="../plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.min.js"></script>
  </body>
  </html>
