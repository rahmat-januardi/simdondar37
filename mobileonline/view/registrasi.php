
<?php
    error_reporting (E_ALL ^ E_NOTICE);
    session_start();
    include '../adm/config.php';
    $utd    = mysqli_fetch_array(mysqli_query($con,"SELECT * from utd where `aktif`=1"));
    $idudd  = $utd['id'];
    $kodep  = $_GET['id'];
    $id     = $_SESSION['instansi'];
    $unit   = $_SESSION['unit'];
    $user   = $_SESSION['user'];
    $client_ip = $_SESSION['client_ip'];
    if ($unit=="" || $id===""){
        header("location: ?page=index");
    } else {
    
    
    //CARI NAMA INSTANSI
    $ins = mysqli_fetch_assoc(mysqli_query($con, "SELECT nama from detailinstansi where KodeDetail='$id'"));
    $namains = $ins['nama'];
    
    
    
    
    $data = mysqli_fetch_assoc(mysqli_query($con, $jd));
    
      $today1=date("Y-m-d H:i:s");
      $today2=date("Y-m-d");
      $jam_donor=date("H:i:s");
      $tipe_donor='0';
      if ($data['Jk']=='0'){$kel="Laki-laki";}else{$kel="Perempuan";}
      if ($data['Status']=='0'){$nikah="Belum Menikah";}else{$nikah="Sudah Menikah";}
      if ($data['Rhesus']=='-'){$rhes="Negatif";}else{$rhes="Positif";}
      
      //Shift Petugas
      $shift  = mysqli_fetch_assoc(mysqli_query($con,"SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
      //$shif   = $shift['nama'];
      if ($shift['nama']=="1"){
        $shif   = "1";
      } else if ($shift['nama']=="2"){
        $shif   = "2";
      } else if ($shift['nama']=="3"){
        $shif   = "3";
      } else {
        $shif   = "4";
      }
    
    //JIKA POST
    if(isset($_POST['proses'])){
        //SIMPAN PENDONOR
        $year = date("Y");
        $kodep    = $_POST['kodep'];
        $ktp      = $_POST['ktp'];
        $gol      = $_POST['gol'];
        $rh       = $_POST['rh'];
        $jmldnr   = $_POST['jmldnr'];
        $donorke  = '1';
        $namap    = $_POST['namap'];
        $jk       = $_POST['jk'];
        $alamat   = $_POST['alamat'];
        $kel      = $_POST['kel'];
        $kec      = $_POST['kec'];
        $wil      = $_POST['wil'];
        $telp     = $_POST['telp'];
        $tmp_lhr  = $_POST['tmp_lhr'];
        $tgl       = $_POST['tgl'];
        $bln       = $_POST['bln'];
        $thn       = $_POST['thn'];
        
        $tgl_lhr  = $thn."-".$bln."-".$tgl;
        $pekerjaan= $_POST['pekerjaan'];
        $nikah    = $_POST['nikah'];
        $jenis_donor= $_POST['jenis_donor'];
        $metode   = $_POST['metode'];
        $lengan   = $_POST['lengan'];
        $umur     = $year - $thn;
        $aph=$tpk="0";
        switch ($_POST['metode']) {
            case '2': $aph=1;break;
            case '3': $tpk=1;break;
            default:break;
        }
        //------------------------ set id pendonor ------------------------->
        //digit pendonor 14 digit, 4kode utd, 3 nama, 2 tmpt aftap, 6 sequence,

        function getInitials($string) {
          $cleanString = preg_replace('/[\s\W]+/', '', $string);
          return substr($cleanString, 0, 3);
        }
        $initials = getInitials($namap);
        
        $nama1 = str_replace(".","",$namap);
        $nama1 = str_replace(" ","",$nama1);
        $nama1 = str_replace(",","",$nama1);
        
        $nm=strtoupper(substr($initials,0,3));
        $kdtp    = $utd['id'].$unit.$nm;
        $idp    = mysqli_query($con,"select Kode from pendonor where Kode like '$kdtp%'
                     order by Kode DESC");
        $idp1    = mysqli_fetch_assoc($idp);
        $idp2    = substr($idp1['Kode'],9,6);
        if ($idp2<1) {
             $idp2="00000";
        }
        $int_idp2=(int)$idp2+1;
        $j_nol1= 6-(strlen(strval($int_idp2)));
        for ($i=0; $i<$j_nol1; $i++){
             $idp4 .="0";
        }
        $kodep=$kdtp.$idp4.$int_idp2;
        //---------------------- END set id pendonor ------------------------->
        
        $sekarang = date("Y-m-d h:m:s");
        $now    = date("Y-m-d");
                               
        $insertdonor="insert into pendonor
        (`Kode`,`NoKTP`,`Nama`,`Alamat`,`Jk`,`Pekerjaan`,
        `telp`,`TempatLhr`,`TglLhr`,`Status`,`GolDarah`,
        `Rhesus`,`Call`,`kelurahan`,`kecamatan`,`wilayah`,`jumDonor`,`title`,
        `telp2`,`umur`,`tglkembali`,
        `pencatat`,`mu`,`cekal`,`up`,`waktu_update`,`tanggal_entry`,`apheresis`)
        values ('$kodep','$ktp','$namap','$alamat','$jk','$pekerjaan',
        '$telp','$tmp_lhr','$tgl_lhr','$nikah','$gol',
        '$rh','1','$kel','$kec','$wil','0','-',
        '$telp','$umur','$now',
        '$user','','0','1','$sekarang','$sekarang','0')";
        echo $insertdonor;
        //$tambah=mysql_query($tambah_sql,$con);


        //CURL DBNASIONAL
        $curlinsdn = curl_init();
                    curl_setopt_array($curlinsdn, array(
                        CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/insertpendonor.php",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 5,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => array('idudd' => $idudd, 'Kode' => $kodep, 'NoKTP' => $ktp, 'Nama' => $namap, 'Alamat' => $alamat, 'Jk' => $jk, 'Pekerjaan' => $pekerjaan, 'TempatLhr' => $tmp_lhr, 'TglLhr' => $tgl_lhr, 'Status' => $nikah, 'kelurahan' => $kel, 'kecamatan' => $kec, 'wilayah' => $wil, 'telp2' => $telp, 'GolDarah' => $gol, 'Rhesus' => $rh, 'jumDonor' => '0', 'Call' => '1', 'tglkembali' => $now, 'umur' => $umur, 'metode' => 'insert'),
                    ));
                    $response = curl_exec($curlinsdn);
                    $datains = json_decode($response, true);
                    //echo "<pre>"; print_r($response); echo "</pre>";
                    curl_close($curlinsdn);
        
        
        if (mysqli_query($con,$insertdonor)){
        //KODE HTRANSAKSI
        //------------------------ set id transaksi ------------------------->
        $udd1   = mysqli_query($con,"select id from utd where aktif='1'");
        $udd    = mysqli_fetch_assoc($udd1);
        //$idp      = mysqli_query($con,"select * from tempat_donor where active='1'");
        //$idp1      = mysqli_fetch_assoc($idp);
        $th          = substr(date("Y"),2,2);
        $bl          = date("m");
        $tgl      = date("d");
        $kdtp      = $unit.$tgl.$bl.$th."-".$udd[id]."-";
        $idp      = mysqli_query($con,"select NoTrans from htransaksi where NoTrans like '$kdtp%' order by NoTrans DESC");
        $idp1      = mysqli_fetch_assoc($idp);
        $idp2      = substr($idp1[NoTrans],14,4);
        if ($idp2<1) {$idp2="0000";}
        $idp3      = (int)$idp2+1;
        $id31      = strlen($idp2)-strlen($idp3);
        $idp4      = "";
        for ($i=0; $i<$id31; $i++){
            $idp4 .="0";
        }
        $id_transaksi_baru=$kdtp.$idp4.$idp3;
        $v_notransaksi = $id_transaksi_baru;
        //------------------------ END set id transaksi ------------------------->

        $v_notransaksi = $id_transaksi_baru;
        //=======Audit Trial====================================================================================
        $log_mdl = 'REGISTRASI';
        $log_aksi= 'Manambah data: '.$kodep.' - '.$namap;
        $log= mysqli_query($con, "INSERT INTO `user_log` (`komputer`, `user`, `modul`, `aksi_user`,`tempat`, `keterangan`) VALUES
        ('$client_ip', '$user', '$log_mdl', '$log_aksi','$unit', '')");
        //=====================================================================================================
                               
        }
        
        
            echo $msg."<br>";
        //insert htransaksi
        if ($umur >= 17){
        $idtrans=substr($id_transaksi_baru,0,8);
        $check_p=mysqli_num_rows(mysqli_query($con,"select KodePendonor from htransaksi where NoTrans like '$idtrans%' and KodePendonor='$kodep'"));
        if ($check_p==0) {

            $q_htrans="insert into htransaksi
                (NoTrans,KodePendonor,KodePendonor_lama,Tgl,Pengambilan,ketBatal,tempat,Instansi,
                JenisDonor,id_permintaan,Status,Nopol,apheresis,kendaraan,shift,kota,umur,donorbaru,jk,
                gol_darah,rhesus,pekerjaan,donorke,user,jam_mulai,rs, donor_tpk)
                value
                ('$id_transaksi_baru','$kodep','$kodep','$today1','-','-','0','$namains',
                 '$jenis_donor','','0','-','$aph','','$shif','$udd[id]','$umur','0','$jk',
                         '$gol','$rh','$pekerjaan','$donorke','$iduser','$jam_donor','','$tpk')";
            if (mysqli_query($con,$q_htrans)){
                $msg .= '- Pendaftaran - berhasil<br>';
                $lanjut ='0';
                
                           
            }else{
                $msg .= '- Pendaftaran - GAGAL<br>';
                $lanjut='1';
            }
            
            }}else{
                $lanjut='1';
            }
                if ($lanjut =="0" ){
                    echo $msg."- Silahkan Lanjutkan Medical CheckUp";
                    ?>
                    <script>
                          alert("Transaksi Donor berhasil disimpan");
                    </script>
                    <?php
                    header("location: ?page=dash");
                    ?>
                    <!--META http-equiv="refresh" content="5; url=../../formulir_donor_PDF2.php?kp=<?=$kodep?>"-->
                <?php }else{
                    echo $msg."- Belum Saatnya Donor";
                    //header("location: ?page=dash");
                    ?>
                    <META http-equiv="refresh" content="5; url=?page=dash">
                    <!--META http-equiv="refresh" content="5; url=../../formulir_donor_PDF2.php?kp=<?=$kodep?>"-->
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
.body{
    font-size:12px;
}
.padding {
    
    background-image: url('dist/img/white.jpg');
    background-size: cover;
}
.box{

    height: 25px;
    padding: 20px;
}
.box2{

    height: 25px;
    padding: 20px;
}
.box3{

    height: 100px;
    
}
.copyright{
        bottom: 0;
    width: 100%;
    position: fixed;
    height:40px;
    line-height:50px;
    background:RED;
    color:#fff;
    padding-left: 10px;
  }
  .input-tanggal{
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
  <h4 class="text-center" style="font-size:24px; font-weight:bold;color:#ff0000;text-shadow: 1px 1px 1px #000000; font-family:Helvetica, Arial, san-serif;">REGISTRASI DATA PENDONOR<br><?php echo $ins['nama'];?></h4>
</div>

  <div class="col-12 col-sm-12">
    <div class="card-body">
<!--content-->
    <form action="" method="post">
    <div class="row">
    <div class="col-lg-6">
    <div class="table-responsive">
        <table width=100%>
            
            <tr><td>No. KTP</td>         <td class="warning"><input type="text" name="ktp" class="form-control" required></td></tr>
            <tr><td>Nama Pendonor</td>  <td class="warning text-danger"><strong><input type="text" name="namap" class="form-control" required></strong></td></tr>
            <tr><td>Jenis Kelamin</td>  <td class="warning">
                  
                  <input type="radio" name="jk" value="0" required>
                  Laki-laki &nbsp;
                  <input type="radio" name="jk" value="1" >
                  Perempuan
              </td></tr>
            <tr><td>Alamat</td>         <td class="warning"><input type="text" name="alamat" class="form-control" required></td></tr>
            <tr><td>Keluarahan</td>     <td class="warning"><input type="text" name="kel" class="form-control" ></td></tr>
            <tr><td>Kecamatan</td>      <td class="warning"><input type="text" name="kec" class="form-control" ></td></tr>
            <tr><td>Wilayah</td>        <td class="warning"><input type="text" name="wil" class="form-control" required></td></tr>
            <tr><td>Gol. Darah</td><td>
            <div class="input-group">
                                        <select name="gol" class="form-control" required>
                                        <option value="">== Pilih ==</option>
                                        <option value="X">X</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="O">O</option>
                                        <option value="AB">AB</option>
                                        </select>
            <select name="rh" class="form-control" required>
                    <option value="+">+</option>
                    <option value="-">-</option>
                    </select></div></td></tr>
          </table>
    </div>
    </div>
    <div class="col-lg-6">
    <div class="table-responsive">
      <table width=100%>
        <tr><td>No. Handphone</td>           <td class="warning"><input type="text" name="telp" class="form-control" required></td></tr>
        <tr><td>Tempat Lahir</td>      <td class="warning"><input type="text" name="tmp_lhr" class="form-control" required></td></tr>
        <tr><td>Tanggal Lahir</td>      <td class="warning">
            <div class="input-group">
                <input type="text"  class="form-control" name="tgl" placeholder="dd" size='2' maxlength="2" required>
                <input type="text"  class="form-control" name="bln" placeholder="mm" size='2' maxlength="2" required>
                <input type="text"  class="form-control" name="thn" placeholder="yyyy" size='4' maxlength="4" required>
            </div></td></tr>
        <tr><td>Pekerjaan</td>      <td class="warning">
              <select name="pekerjaan" class="form-control" required>
                  <option value="">== Pilih ==</option>
                      <?php
                          $q="select * from pekerjaan";
                          $do=mysqli_query($con,$q);
                          
                              while($datap = mysqli_fetch_assoc($do)){
                                  
                      ?>
                          <option value="<?=$datap['Nama']?>">
                              <?php echo $datap['Nama']?>
                          </option>
                          <?php
                              
                          }?>
              </select>

              </td></tr>
        <tr><td>Status Nikah</td>         <td class="warning">
              
                  <input type="radio" name="nikah" value="0" >
                      Belum Nikah &nbsp;
                  <input type="radio" name="nikah" value="1" required>
                      Nikah
      
       </td></tr>
        
        <tr><td>Jenis Donor</td>
            <td class="warning">
              <select name="jenis_donor" class="form-control" required>
                  <option value="0">Sukarela</option>
                  <option value="1">Pengganti</option>
              </select>
            </td>
        </tr>
        
        <tr><td>Metode Donor</td>
            <td class="warning">
              <select name="metode" class="form-control" required>
                  <option value="1">Donor Biasa</option>
                  <option value="2">Donor Apheresis</option>
                  <option value="3">Donor Plasma Konvalesen</option>
              </select>
            </td>
        </tr>
        <tr><td>Pilih Lengan Donor</td>
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
                          <div class="col-lg-6" align="right">
                          
                          </div>
                          <div class="col-lg-6" align="right">
                            <a href="?page=caridonor"><input type="button" value="BATALKAN" class="btn btn-danger btn-sm" style=";box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 4px 8px 0 rgba(0, 0, 0, 0.19); height:50px;width:100px" ></a>
                            <button name="proses" type="submit" class="btn btn-success" style=";box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 4px 8px 0 rgba(0, 0, 0, 0.19); height:50px;width:100px">SIMPAN</button>
                              
                            
                              
                          <!--/form-->
                          </div>
                      
                      </div>
                          
      </div>
    </div>

</form>

<!--content-->
    </div>
  </div>
<p class="box3">
<div class="copyright">
    <p align="center"><a href="https://pmi.or.id"><font style="color:white">Copyright @ 2025 | PALANG MERAH INDONESIA</a>
</div>





<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$( function() {
  //Date picker
  
  $("#datepicker").datepicker({
         dateFormat:"yy-mm-dd",
      });
} );
</script>
<script type="text/javascript">
    function validasiregistrasi()
    {
        if (document.getElementById('iddonor').value == '')
        {
            if (document.getElementById('nama').value.length < 3)
            {
                    alert('Pencarian Nama harus diisi/minimal 3 Karakter!');return false;
            }
        }
//        if (document.getElementById('alamat').value.length == 0)
//        {
//            alert('Alamat diisi dengan jelas dan lengkap ..');return false;
//        }
    }
</script>
<script>
$( function() {
  //Date picker
  $('#reservationdate').datetimepicker({
      format: 'yyyy-MM-DD'
  });

  //Date picker
  $('#reservationdate2').datetimepicker({
      format: 'yyyy-MM-DD'
  });

  //Date and time picker
  $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

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
  $('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Today'       : [moment(), moment()],
        'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month'  : [moment().startOf('month'), moment().endOf('month')],
        'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment().subtract(29, 'days'),
      endDate  : moment()
    },
    function (start, end) {
      $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
    }
  )
  $("#datepicker2").datepicker({
         dateFormat:"yy-mm-dd",
      });
} );
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- jQuery -->
<script src="../tpksoloplugins/jquery/jquery.min.js"></script>

</body>
</html>

<?php } ?>
