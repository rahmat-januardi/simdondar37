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
  $qdonor = mysqli_query($con, "select * from `pendonor` where `Kode`='$kodep'");
  $qtrans = mysqli_fetch_assoc(mysqli_query($con, "select * from `htransaksi` where `NoTrans`='$notrans'"));
  $dtdonor = mysqli_fetch_assoc($qdonor);

  if (isset($_POST['simpan'])) {
    $v_goldarah_a  = $_POST['goldarah'];
    $v_rhesus_a    = $_POST['rhesus'];
    $v_bb          = $_POST['reqberat_badan'];
    $v_tb          = $_POST['tinggi_badan'];
    $v_sistol      = $_POST['reqtensi_sistol'];
    $v_diastol     = $_POST['reqtensi_diastol'];
    $v_tensi       = $v_sistol . '/' . $v_diastol;
    $v_nadi        = $_POST['reqnadi'];
    $v_ptgdokter   = $_POST['id_dokter'];

    $v_ptgtensi    = $_POST['id_tensi'];
    $v_ptgshb      = $_POST['id_hb'];
    $v_lolos       = $_POST['h_medical'];
    $v_alasan      = $_POST['alasan'];
    $v_hemoglobin  = $_POST['reqhemoglobin'];
    $v_suhu        = $_POST['reqtemperatur'];

    $dokter = mysqli_query($con, "select * from `dokter_periksa` where `Nama`='$v_ptgdokter' limit 1");
    $dokter2 = mysqli_fetch_assoc($dokter);
    $kode = $dokter2['kode'];
    //echo "nama dokter =============>" . $v_ptgdokter . "<br>";
    //echo "kode dokter =============>" . $kode . "<br>";

    //UPDATE HTRANSAKSI=====================================================
    if ($v_lolos == '1') {
      $pengambilan = "1";
      $status = "-";
      $jumHB = "-";
      $ketbatal = $v_alasan;
    } else {
      $pengambilan = '3';
      $status = "1";
      $jumHB = '1';
      $ketbatal = '-';
    }
    $sql_transaksi = "UPDATE `htransaksi` SET
                        `NamaDokter`='$kode',
                        `petugasHB`='$v_ptgshb',
                        `petugasTensi`='$v_ptgtensi',
                        `beratbadan`='$v_bb',
                        `tensi`='$v_tensi',
                        `suhu`='$v_suhu',
                        `nadi`='$v_nadi',
                        `jumHB`='$jumHB',
                        `Hb`='$v_hemoglobin',
                        `status_test`='1',
                        `Status`='$status',
                        `Pengambilan`='$pengambilan',
                        `ketBatal`='$ketbatal',
			`tempat`='M',
                        `mu`='1',
                        `gol_darah`='$v_goldarah_a',
                        `user`='$user',
                        `rhesus`='$v_rhesus_a'
                    WHERE (`NoTrans`='$notrans')";

    $update_pendonor=mysqli_query($con,"UPDATE pendonor SET GolDarah='$v_goldarah_a',Rhesus='$v_rhesus_a' WHERE Kode='$kodep'");
    //echo $sql_transaksi;
    $tambah     = mysqli_query($con, $sql_transaksi);
    //=======Audit Trial====================================================================================
    $log_mdl = 'SELEKSI';
    $log_aksi = 'Check Up: ' . $notrans . ', Pendonor: ' . $kodep . ' Status: ' . $status;
    $log = mysqli_query($con, "INSERT INTO `user_log` (`komputer`, `user`, `modul`, `aksi_user`,`tempat`, `keterangan`) VALUES
        ('$client_ip', '$user', '$log_mdl', '$log_aksi','$unit', '')");
    //=====================================================================================================

    if ($tambah) {
  $msg .= '- Medical Checkup berhasil disimpan<br>';
  
  if ($v_lolos == '0') { // Lolos
    echo $msg . "Silahkan Lanjutkan Pengambilan Darah";
    ?>
    <META http-equiv="refresh" content="3; url=?page=aftap&NoTrans=<?php echo $notrans; ?>&kodep=<?php echo $kodep; ?>">
    <?php
    exit; // penting: hentikan eksekusi lebih lanjut
  } else { // Tidak Lolos
    echo $msg . "Donor Tidak Lolos Seleksi Medical Checkup";
    ?>
    <META http-equiv="refresh" content="5; url=?page=dash">
    <?php
    exit;
  }
} else {
  echo "Gagal menyimpan data Medical Checkup";
  ?>
  <META http-equiv="refresh" content="5; url=?page=dash">
  <?php
}
    //mysqli_close;
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
    
     <!-- Edit -->
	<script type="text/javascript" src="../../tpk/medical_checkup.js"></script>

    <script>
      function hbmeter() {
        const hbInput = document.getElementById("hemoglobin").value.trim();
        const hb = parseFloat(hbInput);
        const warning = document.getElementById("warning_hb");

        console.log("Nilai HB:", hb);

        if (hbInput === "" || isNaN(hb)) {
          warning.textContent = "";
          return;
        }

        if (hb < 12.5 || hb > 17.0) {
          warning.textContent = "Nilai hemoglobin di luar batas normal (12.5 - 17.0 g/dL)";
        } else {
          warning.textContent = "";
        }

        chb(hb);
      }
    </script>
    <!-- End -->



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
      <h4 class="text-center" style="font-size:24px; font-weight:bold;color:#ff0000;text-shadow: 1px 1px 1px #000000; font-family:Helvetica, Arial, san-serif;">MEDICAL CHECKUP PENDONOR<br><?php echo $ins['nama']; ?></h4>
      <a href="?page=searchmcu"><button name="baru" class="btn btn-info float-right"><i class="nav-icon ion ion-android-arrow-back"></i> Kembali</button></a>
    </div>

    <div class="col-12 col-sm-12">
      <div class="card-body">
        <!--content-->
        <form method="post" action="">
          <div class="row">
            <div class="col-lg-6">
              <?php
              ($dtdonor['Jk'] == '0') ? $kel = "Laki-laki" : $kel = "Perempuan";
              ($dtdonor['Status'] == '0') ? $status = "Belum Menikah" : $status = "Sudah Menikah";
              ?>
              <div class="table-responsive" id="shadow1">
                <table class="table borderless table-striped table-hover">
                  <tr>
                    <td>Kode</td>
                    <td><?php echo $dtdonor['Kode']; ?></td>
                  </tr>
                  <tr>
                    <td>NIK</td>
                    <td><?php echo $dtdonor['NoKTP']; ?></td>
                  </tr>
                  <tr>
                    <td>Nama</td>
                    <td><strong><?php echo $dtdonor['Nama']; ?></strong></td>
                  </tr>
                  <tr>
                    <td>Gol</td>
                    <td>
                      <input type="hidden" name="abo_pendonor" value="<?php echo $dtdonor['GolDarah']; ?>">
                      <input type="hidden" name="rh_pendonor" value="<?php echo $dtdonor['Rhesus']; ?>">
                      <?
                      $sA = '';
                      $sB = '';
                      $sAB = '';
                      $sO = '';
                      if ($dtdonor['GolDarah'] == 'A') $sA = 'selected';
                      if ($dtdonor['GolDarah'] == 'B') $sB = 'selected';
                      if ($dtdonor['GolDarah'] == 'AB') $sAB = 'selected';
                      if ($dtdonor['GolDarah'] == 'O') $sO = 'selected';
                      if ($dtdonor['GolDarah'] == 'X') $sX = 'selected';
                      ?>
                      <select name="goldarah" required>
                        <option value="" >--Pilih--</option>
                        <option value="A" <?= $sA ?>>A</option>
                        <option value="B" <?= $sB ?>>B</option>
                        <option value="AB" <?= $sAB ?>>AB</option>
                        <option value="O" <?= $sO ?>>O</option>
                        
                      </select>
                      Rh &nbsp;

                      <?
                      $rn = '';
                      $rp = '';
                      if ($dtdonor['Rhesus'] == "-") $rn = 'selected';
                      if ($dtdonor['Rhesus'] == "+") $rp = 'selected';
                      ?>
                      <select name="rhesus" required>
                        <option value="+" <?= $rp ?>>(+)</option>
                        <option value="-" <?= $rn ?>>(-)</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Kelamin</td>
                    <td><?php echo $kel; ?>, Status : <?php echo $status; ?></td>
                  </tr>
                  <tr>
                    <td>Tempat Lahir</td>
                    <td><?php echo $dtdonor['TempatLhr']; ?></td>
                  </tr>
                  <tr>
                    <td>Tanggal Lahir</td>
                    <td><?php echo $dtdonor['TglLhr']; ?></td>
                  </tr>
                  <tr>
                    <td>Jumlah Donasi</td>
                    <td><?php echo $dtdonor['jumDonor']; ?></td>
                  </tr>
                  <tr>
                    <td>Tgl Kembali Donor</td>
                    <td><?php echo $dtdonor['tglkembali']; ?></td>
                  </tr>
                  <tr>
                    <td>Tgl Kembali Apheresis</td>
                    <td><?php echo $dtdonor['tglkembali_apheresis']; ?></td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="table-responsive">
                <table class="table borderless table-striped table-hover" id="shadow1">
                  <tr>
                    <td>Berat Badan<sup style="color:red;"><strong>*</strong></sup></td>
                    <td><input name="reqberat_badan" id="berat_badan" type="text" style="width:15mm;" onChange="berat(this.value)" maxlength="3" required> kg</td>
                  </tr>
                  <tr>
                    <td>Tinggi Badan</td>
                    <td><input name="tinggi_badan" type="text" style="width:15mm;" maxlength="3" required> cm</td>
                  </tr>
                  <tr>
                    <td>Tensi<sup style="color:red;"><strong>*</strong></sup></td>
                    <td><input name="reqtensi_sistol" id="tensi_sistol" type="text" style="width:15mm;" onChange="sistol(this.value)" maxlength="3" required> /
                      <input name="reqtensi_diastol" id="tensi_diastol" type="text" style="width:15mm;" onChange="diastol(this.value)" maxlength="3" required> mmHg
                    </td>
                  </tr>
                  <tr>
                    <td>Suhu<sup style="color:red;"><strong>*</strong></sup></td>
                    <td><input name="reqtemperatur" id="temperature" type="text" style="width:15mm;" maxlength="5" onChange="suhutubuh(this.value)" required>&nbsp;<sup>o</sup>C</td>
                  </tr>
                  <tr>
                    <td>Nadi<sup style="color:red;"><strong>*</strong></sup></td>
                    <td><input name="reqnadi" type="text" style="width:15mm;" maxlength="3" required> BPM</td>
                  </tr>
                    
                     <!-- Edit -->
                 <tr>
                    <td>Hemoglobin<sup style="color:red;"><strong>*</strong></sup></td>
                    <td style="white-space:nowrap;"><input style='width:15mm' name="reqhemoglobin" id="hemoglobin" type="text" maxlength="5" onChange="hbmeter()" required> g/dL
                      <br>
                      <span id="warning_hb" style="font-size: 12px; color: red;"></span>
                    </td>
                  </tr>
                     <!-- End -->
            
                  <tr>
                    <td>Ptg Anamnesa</td>
                    <td>
                      <select name="id_dokter" style="width:70mm;" required>
                        <?
                        $usr = mysqli_query($con, "select * from v_petugasmu where (date(TglPenjadwalan)=curdate()) AND kodeinstansi='$id' AND (jabatan between 1 AND 4) ORDER BY nama ASC");

                        while ($data = mysqli_fetch_array($usr)) { ?>

                          <option value="<?php echo $data['nama']; ?>" selected> <?php echo $data['nama']; ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Ptgs Tensi</td>
                    <td>
                      <select name="id_tensi" style="width:70mm;" required>
                        <?
                        $usr = mysqli_query($con, "select * from v_petugasmu where (date(TglPenjadwalan)=curdate()) AND kodeinstansi='$id' AND (jabatan between 1 AND 4) ORDER BY nama ASC");

                        while ($data = mysqli_fetch_array($usr)) { ?>

                          <option value="<?php echo $data['nama']; ?>" selected><?php echo $data['nama']; ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Ptgs HB</td>
                    <td>
                      <select name="id_hb" style="width:70mm;" required>
                        <?
                        $usr = mysqli_query($con, "select * from v_petugasmu where (date(TglPenjadwalan)=curdate()) AND kodeinstansi='$id' AND (jabatan between 1 AND 4) ORDER BY nama ASC");

                        while ($data = mysqli_fetch_array($usr)) { ?>

                          <option value="<?php echo $data['nama']; ?>" selected> <?php echo $data['nama']; ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="form-group">
                        <label>Hasil Seleksi : </label>
                        <label class="radio-inline"><input type="radio" checked value="0" name="h_medical" id="h_medical" style="margin-top:1px;">Lolos</label>
                        <label class="radio-inline"><input type="radio" value="1" name="h_medical" id="h_medical" style="margin-top:1px;">Tidak Lolos</label>
                    </td>
                    <td>
                      <select name="alasan" style="width:50mm;">
                        <option value="">-</option>
                        <option value="0">Tensi Rendah</option>
                        <option value="1">Tensi Tinggi</option>
                        <option value="2">HB Rendah</option>
                        <option value="4">HB Tinggi</option>
                        <option value="5">BB Kurang</option>
                        <option value="6">Habis Minum Obat</option>
                        <option value="7">Riwayat Bepergian</option>
                        <option value="8">Kondisi Medis Lain</option>
                        <option value="9">Perilaku Beresiko</option>
                        <option value="10">Alasan Lain</option>
                        <option value="11">Titer Antibody Covid-19 Rendah</option>
                        <option value="12">Hasil IMLTD Reaktif</option>
                        <option value="13">Syarat Apheresis/TPK tidak terpenuhi</option>
                      </select>

                    </td>
              </div>
              </td>
              </tr>
              </table>
              <div class="col-lg-12" align="right">
                <input type=submit name="simpan" value="Proses" class="btn btn-success">
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
      <p align="center"><a href="https://pmi.or.id">
          <font style="color:white">Copyright @ 2022 | PALANG MERAH INDONESIA
        </a>
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

    <!-- Page specific script -->
    <script>
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
    </script>
    <!-- Page specific script -->

    <script>
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
    </script>
    <script type="text/javascript">
      $(document).on("click", "#batal", function() {
        var id = $(this).data('id');

        $("#batal-edit #id").val(id);

      })
    </script>

  </body>

  </html>


<?php } ?>
