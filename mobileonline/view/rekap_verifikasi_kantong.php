<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include '../adm/config.php';
$utd = mysqli_fetch_array(mysqli_query($con, "SELECT * from utd where `aktif`=1"));
$kodep = $_GET['id'];
$id = $_SESSION['instansi'];
$unit = $_SESSION['unit'];
$user = $_SESSION['user'];
if ($unit == "" || $id === "") {
    header("location: ?page=index");
} else {

    //CARI NAMA INSTANSI
    $ins = mysqli_fetch_assoc(mysqli_query($con, "SELECT nama from detailinstansi where KodeDetail='$id'"));
    $namains = $ins['nama'];


    //Shift Petugas
    $shift  = mysqli_fetch_assoc(mysqli_query($con, "SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
    //$shif   = $shift['nama'];
    if ($shift['nama'] == "1") {
        $shif   = "1";
    } else if ($shift['nama'] == "2") {
        $shif   = "2";
    } else if ($shift['nama'] == "3") {
        $shif   = "3";
    } else {
        $shif   = "4";
    }

    // Filter tanggal (escape input untuk hindari SQL injection)
    $tgl_awal  = isset($_GET['awal']) ? mysqli_real_escape_string($con, $_GET['awal']) : date('Y-m-d');
    $tgl_akhir = isset($_GET['akhir']) ? mysqli_real_escape_string($con, $_GET['akhir']) : date('Y-m-d');

    if ($tgl_awal > $tgl_akhir) {
        $temp = $tgl_awal;
        $tgl_awal = $tgl_akhir;
        $tgl_akhir = $temp;
    }

    // Fungsi helper untuk jenis (ganti switch yang salah)
    function getJenisDarah($jenis)
    {
        switch ($jenis) {
            case '1':
                return 'Single';
            case '2':
                return 'Double';
            case '3':
                return 'Triple';
            case '4':
                return 'Quadruple';
            case '5':
                return 'Quintuple';
            case '6':
                return 'Pediatrik';
            default:
                return 'Lainnya';
        }
    }

    function getStatusText($Status)
    {
        switch ($Status) {
            case '0':
                return "Kosong";
            case '1':
                return "Baru Isi / Karantina";
            case '2':
                return "Sehat";
            case '3':
                return "Keluar Bawa / Titip";
            case '4':
                return "Rusak";
            case '5':
                return "Rusak / Gagal Aftap";
            case '6':
                return "Dimusnahkan";
            case '7':
                return "Reaktif";
            case '8':
                return "Darah Flebotomi";
            default:
                return "Status Tidak Dikenal ($Status[Status])";
        }
    }

    $sql = "SELECT v.*, s.jenis, s.volumeasal, s.Status 
            FROM verifikasi_kantong v
            LEFT JOIN stokkantong s ON v.no_kantong = s.noKantong
            WHERE DATE(v.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
            ORDER BY v.tanggal DESC";

    $hasil = mysqli_query($con, $sql);
    $rowst = mysqli_num_rows($hasil);

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

        @media print {
            .no-print {
                display: none;
            }

            body {
                font-size: 9pt;
            }

            table {
                font-size: 8pt;
            }

            @page {
                size: portrait;
                margin: 0.5cm;
            }
        }

        th {
            background-color: #f5f5f5;
        }
    </style>

    <body class="padding">

        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/logo.png" alt="AdminLTELogo" height="60" width="60">
        </div>
        <p>
        <div class="card-header">
            <h4 class="text-center" style="font-size:24px; font-weight:bold;color:#ff0000;text-shadow: 1px 1px 1px #000000; font-family:Helvetica, Arial, san-serif;">REKAPITULASI VERIFIKASI KANTONG DARAH<br><?php echo $ins['nama']; ?></h4>
            <a href="?page=dash"><button name="baru" class="btn btn-info float-right"><i class="nav-icon ion ion-android-arrow-back"></i> Kembali</button></a>
        </div>

        <!-- Form Filter Tanggal -->
        <div class="row no-print" style="margin: 15px 0;">
            <div class="col-md-12">
                <form method="GET" class="form-inline">
                    <input type="hidden" name="page" value="rekap_validktg"> <!-- sesuaikan dengan nama page kamu -->

                    <div class="form-group mr-3">
                        <label for="awal" class="mr-2">Tanggal Awal :</label>
                        <input type="date"
                            class="form-control input-tanggal"
                            id="awal"
                            name="awal"
                            value="<?php echo htmlspecialchars($tgl_awal); ?>"
                            required>
                    </div>

                    <div class="form-group mr-3">
                        <label for="akhir" class="mr-2">Tanggal Akhir :</label>
                        <input type="date"
                            class="form-control input-tanggal"
                            id="akhir"
                            name="akhir"
                            value="<?php echo htmlspecialchars($tgl_akhir); ?>"
                            required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Filter
                    </button>

                    <a href="?page=rekap_verifikasi_kantong" class="btn btn-default ml-2">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </form>
            </div>
        </div>

        <div class="col-12 col-sm-12">
            <div class="card-body">
                <!--content-->

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr style="background-color:RED; font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center">
                            <th width="3%" rowspan="2" style="text-align: center; vertical-align: middle;">No</th>
                            <th width="14%" rowspan="2" style="text-align: center; vertical-align: middle;">No. Kantong</th>
                            <th width="12%" rowspan="2" style="text-align: center; vertical-align: middle;">Tanggal & Jam</th>
                            <th width="8%" rowspan="2" style="text-align: center; vertical-align: middle;">Jenis</th>
                            <th width="6%" rowspan="2" style="text-align: center; vertical-align: middle;">Vol (ml)</th>
                            <th colspan="3" style="text-align: center; vertical-align: middle;">Kemasan</th>
                            <th colspan="2" style="text-align: center; vertical-align: middle;">Selang</th>
                            <th colspan="2" style="text-align: center; vertical-align: middle;">Jarum</th>
                            <th colspan="2" style="text-align: center; vertical-align: middle;">Antikoagulan</th>
                            <th width="9%" rowspan="2" style="text-align: center; vertical-align: middle;">Status Kantong</th>
                            <th width="9%" rowspan="2" style="text-align: center; vertical-align: middle;">Hasil Verifikasi</th>
                        </tr>
                        <tr style="background-color:RED; font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center">
                            <th style="text-align: center; vertical-align: middle;">Keadaan Utuh</th>
                            <th style="text-align: center; vertical-align: middle;">Belum Expired</th>
                            <th style="text-align: center; vertical-align: middle;">Tidak Bocor</th>
                            <th style="text-align: center; vertical-align: middle;">Baik</th>
                            <th style="text-align: center; vertical-align: middle;">Tertekuk</th>
                            <th style="text-align: center; vertical-align: middle;">Baik</th>
                            <th style="text-align: center; vertical-align: middle;">Bengkok</th>
                            <th style="text-align: center; vertical-align: middle;">Jernih</th>
                            <th style="text-align: center; vertical-align: middle;">Berubah Warna</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Hasil Cari
                        if ($rowst > 0) {
                            $a = 0;
                            $lolos = 0;

                            $color = "";
                            while ($r = mysqli_fetch_array($hasil)) {
                                $a++;
                                $hasil_ver = $r['status_valid']
                                    ? '<span class="label label-success"><i class="fa fa-check"></i> LOLOS</span>'
                                    : '<span class="label label-danger"><i class="fa fa-times"></i> TIDAK LOLOS</span>';

                                if ($r['status_valid']) $lolos++;

                                echo '<tr style="background-color: #f5f5f5;">'; ?>
                                <td align="center"><?= $a ?></td>
                                <td align="center"><strong><?= $r['no_kantong'] ?></strong></td>
                                <td align="center"><?= date('d-m-Y H:i', strtotime($r['tanggal'])) ?></td>
                                <td align="center"><?= getJenisDarah($r['jenis']) ?></td>
                                <td align="center"><?= $r['volumeasal'] ?: '-' ?></td>

                                <!-- KEMASAN -->
                                <td align="center">
                                    <?= $r['kemasan_utuh']     ? '<i class="fa fa-check text-success" title="Keadaan Utuh"></i>' : '<i class="fa fa-times text-danger" title="Tidak Utuh"></i>' ?><br>
                                </td>
                                <td align="center">
                                    <?= $r['kemasan_expired']  ? '<i class="fa fa-check text-success" title="Belum Expired"></i>' : '<i class="fa fa-times text-danger" title="Expired"></i>' ?><br>
                                </td>
                                <td align="center">
                                    <?= $r['kemasan_bocor']    ? '<i class="fa fa-check text-success" title="Tidak Bocor"></i>' : '<i class="fa fa-times text-danger" title="Bocor"></i>' ?>
                                </td>

                                <!-- SELANG -->
                                <td align="center">
                                    <?= $r['selang_baik']      ? '<i class="fa fa-check text-success" title="Baik"></i>' : '<i class="fa fa-times text-danger" title="Tidak Baik"></i>' ?><br>
                                </td>
                                <td align="center">
                                    <?= $r['selang_tertekuk'] ? '<i class="fa fa-check text-success" title="Tertekuk"></i>' : '<i class="fa fa-times text-danger" title="Tidak Tertekuk"></i>' ?>
                                </td>

                                <!-- JARUM -->
                                <td align="center">
                                    <?= $r['jarum_baik']       ? '<i class="fa fa-check text-success" title="Baik"></i>' : '<i class="fa fa-times text-danger" title="Tidak Baik"></i>' ?><br>
                                </td>
                                <td align="center">
                                    <?= $r['jarum_bengkok']   ? '<i class="fa fa-check text-success" title="Bengkok"></i>' : '<i class="fa fa-times text-danger" title="Tidak Bengkok"></i>' ?>
                                </td>

                                <!-- ANTIKOAGULAN -->
                                <td align="center">
                                    <?= $r['anti_jernih']      ? '<i class="fa fa-check text-success" title="Jernih"></i>'      : '<i class="fa fa-times text-danger" title="Tidak Jernih"></i>' ?><br>
                                </td>
                                <td align="center">
                                    <?= $r['anti_berubah']    ? '<i class="fa fa-check text-success" title="Berubah Warna"></i>' : '<i class="fa fa-times text-danger" title="Tidak Berubah Warna"></i>' ?>
                                </td>

                                <td align="center"><?= getStatusText($r['Status']) ?></td>
                                <td align="center"><?= $hasil_ver ?></td>
                        <?php

                                echo "</tr>";
                            }
                        }

                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="info">
                            <th colspan="15" class="text-right">TOTAL KANTONG DIVERIFIKASI</th>
                            <th style="text-align: center;"><?= $rowst ?></th>
                        </tr>
                        <tr class="success">
                            <th colspan="15" class="text-right">LOLOS VERIFIKASI (SIAP AFTAP)</th>
                            <th style="text-align: center;"><?= $lolos ?></th>
                        </tr>
                        <tr class="danger">
                            <th colspan="15" class="text-right">TIDAK LOLOS VERIFIKASI</th>
                            <th style="text-align: center;"><?= $rowst - $lolos ?></th>
                        </tr>
                        <tr class="warning">
                            <th colspan="15" class="text-right">PERSENTASE LOLOS</th>
                            <th style="text-align: center;"><?= $rowst > 0 ? round(($lolos / $rowst) * 100, 2) : 0 ?> %</th>
                        </tr>
                    </tfoot>
                </table>




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