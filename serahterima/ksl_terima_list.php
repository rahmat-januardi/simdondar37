<?php
session_start();
$msg = "";
require_once('clogin.php');
require_once('config/dbi_connect.php');
$leveluser   = $_SESSION['level'];
$level       = $_SESSION['leveluser'];
$namauser    = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];
$udd         = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama`,`id` FROM `utd` WHERE `aktif`='1';"));
$id_uddaktif   = $udd['id'];
$nama_uddaktif = $udd['nama'];

$tgl   = date('Ymd');
$token = "17091945" . $tgl;

(isset($_SESSION['tanggal1'])) ? $f_tanggal1 = $_SESSION['tanggal1'] : $f_tanggal1 = date('Y-m-d');
(isset($_SESSION['tanggal2'])) ? $f_tanggal2 = $_SESSION['tanggal2'] : $f_tanggal2 = date('Y-m-d');
(isset($_SESSION['status']))   ? $f_status   = $_SESSION['status']   : $f_status   = "";

if (isset($_POST['vfilter'])) {
    $f_status   = $_SESSION['status']   = $_POST['fltstatus'];
    $f_tanggal1 = $_SESSION['tanggal1'] = $_POST['fltTanggal1'];
    $f_tanggal2 = $_SESSION['tanggal2'] = $_POST['fltTanggal2'];
}
if (isset($_POST['vreset'])) {
    $f_status   = "";
    $f_tanggal1 = date('Y-m-d');
    $f_tanggal2 = date('Y-m-d');
}

// ── Ambil data ONLINE dari dbdonor ────────────────────────────────────────────
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_terima_transaksi.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => "",
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => "POST",
    CURLOPT_POSTFIELDS     => array('udd' => $id_uddaktif, 'key' => $token),
));
$response  = curl_exec($curl);
$curlError = curl_error($curl);
curl_close($curl);

$dataOnline = array();
if ($response && !$curlError) {
    $decoded = json_decode($response, true);
    if (isset($decoded['data']) && is_array($decoded['data'])) {
        $dataOnline = $decoded['data'];
    }
}

// ── Ambil data VIA DOWNLOAD dari tabel lokal ─────────────────────────────────
mysqli_query($dbi, "CREATE TABLE IF NOT EXISTS `ksl_import_antrian` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `notrans`        VARCHAR(60)  NOT NULL DEFAULT '',
    `hst_tgl`        VARCHAR(30)  DEFAULT '',
    `udd_asal_id`    VARCHAR(20)  DEFAULT '',
    `udd_asal_nama`  VARCHAR(150) DEFAULT '',
    `udd_penerima`   VARCHAR(20)  DEFAULT '',
    `hst_asal`       VARCHAR(100) DEFAULT '',
    `jumlahA`        INT DEFAULT 0,
    `jumlahB`        INT DEFAULT 0,
    `jumlahO`        INT DEFAULT 0,
    `jumlahAB`       INT DEFAULT 0,
    `jumlah`         INT DEFAULT 0,
    `status`         TINYINT DEFAULT 0,
    `tgl_import`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `imported_by`    VARCHAR(50) DEFAULT '',
    UNIQUE KEY `uk_notrans` (`notrans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$dataDownload = array();
$qryDl = mysqli_query($dbi, "SELECT * FROM `ksl_import_antrian` WHERE `status`=0 ORDER BY `tgl_import` DESC");
while ($rowDl = mysqli_fetch_assoc($qryDl)) {
    $dataDownload[] = $rowDl;
}

$cntOnline   = count($dataOnline);
$cntDownload = count($dataDownload);
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="15">   <!-- 15 detik -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootsrap337/bspmi.css">
    <link rel="stylesheet" href="bootsrap337/w3.css">
    <link rel="stylesheet" href="pmf/pmfstyle.css">
    <link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
    <link href="bootsrap337/datepicker/css/bootstrap-datepicker.css" rel="stylesheet">
    <link rel="stylesheet" href="bootsrap337/chosen/chosen.css">
    <link href="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.css" rel="stylesheet">
    <style>
        .shadow {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, .2), 0 6px 20px 0 rgba(0, 0, 0, .19);
        }

        .modal-half {
            width: 70%;
            padding: 0;
            position: fixed;
            left: 15%;
        }

        .modal-content {
            width: 100%;
            margin: 0 0;
        }

        .modal-footer {
            bottom: 0;
            position: relative;
            width: 100%;
        }

        .form-group {
            margin-top: 1px;
            margin-bottom: 1px;
        }

        .table thead th {
            height: 40px;
            padding: 2px !important;
            text-align: center !important;
            vertical-align: middle !important;
            text-shadow: 1px 1px 2px black;
            font-size: 1.2em;
        }

        .table tbody td {
            font-size: 1em;
            white-space: nowrap;
            vertical-align: middle !important;
        }

        #loading {
            width: 50px;
            height: 50px;
            border-radius: 100%;
            border: 5px solid #ccc;
            border-top-color: #ff6a00;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            z-index: 99;
            animation: sp 2s ease infinite;
        }

        @keyframes sp {
            from {
                transform: rotate(0deg)
            }

            to {
                transform: rotate(360deg)
            }
        }

        a {
            text-decoration: none !important;
        }

        .swal2-popup {
            font-size: 1.6rem !important;
        }

        .nav-tabs-ksl {
            border-bottom: 2px solid #8b1a1a;
            margin-bottom: 15px;
        }

        .nav-tabs-ksl>li>a {
            color: #8b1a1a;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }

        .nav-tabs-ksl>li.active>a,
        .nav-tabs-ksl>li.active>a:hover {
            background: #8b1a1a;
            color: #fff;
            border-color: #8b1a1a;
        }

        .nav-tabs-ksl>li>a:hover {
            background: #f2d4d4;
            color: #8b1a1a;
        }

        .upload-area {
            border: 2px dashed #8b1a1a;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #fff8f8;
            cursor: pointer;
        }

        .upload-area:hover {
            background: #f2d4d4;
        }

        .upload-area .up-icon {
            font-size: 36px;
            color: #8b1a1a;
        }

        .upload-area p {
            margin: 6px 0 0;
            color: #555;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div id="loading"></div>
    <div class="container-fluid" style="margin:30px;">
        <div class="row">
            <div class="col-md-12">
                <div class="panel w3-border-theme shadow">

                    <div class="panel-heading w3-theme-d5 clearfix">
                        <div class="col-lg-9 col-md-8 col-sm-7 col-xs-8 text-left text-shadow"
                            style="font-size:150%;font-weight:bold;">
                            ANTRIAN PENERIMAAN DARAH KONSOLIDASI
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-4 text-right">
                            <a href="?module=rekapksl" class="w3-btn w3-theme w3-hover-yellow">REKAP PENERIMAAN</a>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-xs-12"><?php echo $msg; ?></div>

                        <!-- Tab navigation -->
                        <ul class="nav nav-tabs nav-tabs-ksl" id="tabKonsolidasi">
                            <li class="active">
                                <a href="#tab-download" data-toggle="tab">
                                    Via Download (Import JSON)
                                    <?php if ($cntDownload > 0) {
                                        echo '<span class="badge" style="background:#1a6a8b;">' . $cntDownload . '</span>';
                                    } ?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-online" data-toggle="tab">
                                    Via Online
                                    <?php if ($cntOnline > 0) {
                                        echo '<span class="badge" style="background:#1a8b3a;">' . $cntOnline . '</span>';
                                    } ?>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <!-- TAB ONLINE -->
                            <div class="tab-pane" id="tab-online">
                                <?php if ($curlError) { ?>
                                    <div class="alert alert-warning">
                                        <strong>Perhatian:</strong> Gagal menghubungi server dbdonor.pmi.or.id.
                                        (<?php echo htmlspecialchars($curlError); ?>)
                                    </div>
                                <?php } ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover display" id="tblOnline">
                                        <thead class="w3-theme-d4">
                                            <tr>
                                                <th>No</th>
                                                <th>Transaksi</th>
                                                <th>Tanggal</th>
                                                <th>Asal UDD</th>
                                                <th>Tempat<br>Pengambilan</th>
                                                <th>A</th>
                                                <th>B</th>
                                                <th>O</th>
                                                <th>AB</th>
                                                <th>Jumlah<br>Kantong</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            for ($a = 0; $a < count($dataOnline); $a++) {
                                                if (strlen($dataOnline[$a]['hst_notrans']) == 0) continue;
                                                $no++;
                                                echo "<tr>";
                                                echo "<td class='text-right'>" . $no . ".</td>";
                                                echo "<td>" . htmlspecialchars($dataOnline[$a]['hst_notrans']) . "</td>";
                                                echo "<td>" . htmlspecialchars($dataOnline[$a]['hst_tgl'])     . "</td>";
                                                echo "<td>" . htmlspecialchars($dataOnline[$a]['nama'])        . "</td>";
                                                echo "<td>" . htmlspecialchars($dataOnline[$a]['hst_asal'])    . "</td>";
                                                echo "<td align='right'>" . $dataOnline[$a]['jumlahA']  . "</td>";
                                                echo "<td align='right'>" . $dataOnline[$a]['jumlahB']  . "</td>";
                                                echo "<td align='right'>" . $dataOnline[$a]['jumlahO']  . "</td>";
                                                echo "<td align='right'>" . $dataOnline[$a]['jumlahAB'] . "</td>";
                                                echo "<td align='right'>" . $dataOnline[$a]['jumlah']   . "</td>";
                                                echo "<td align='center'>";
                                                echo "<a href='pmi" . $level . ".php?module=sr_aftap_knsdt&mode=proses&id=" . urlencode($dataOnline[$a]['hst_notrans']) . "&source=online'>PROSES</a>";
                                                echo " | ";
                                                echo "<a href='pmi" . $level . ".php?module=hapus_knsdt&mode=hapus&id=" . urlencode($dataOnline[$a]['hst_notrans']) . "' onclick=\"return confirm('Yakin Hapus Data Konsolidasi?')\">HAPUS</a>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                            if ($no == 0) {
                                                echo '<tr class="text-center">';
                                                echo '<td colspan="11" style="font-size:16px; padding:30px !important;">';
                                                echo 'Tidak ada data antrian konsolidasi online';
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- /tab-online -->

                            <!-- TAB DOWNLOAD -->
                            <div class="tab-pane active" id="tab-download">

                                <!-- Area upload JSON -->
                                <div class="row" style="margin-bottom:15px;">
                                    <div class="col-md-6 col-md-offset-3">
                                        <div class="upload-area" id="uploadArea"
                                            onclick="document.getElementById('inputJsonFile').click();">
                                            <div class="up-icon">
                                                <span class="glyphicon glyphicon-upload"></span>
                                            </div>
                                            <p><strong>Klik di sini atau seret file JSON ke area ini</strong></p>
                                            <p>File hasil ekspor dari <em>Konsolidasi via Download</em></p>
                                            <p class="mt-1" id="selectedFileName" style="color:#8b1a1a;font-weight:bold;font-size:17px;"></p>
                                        </div>
                                        <input type="file" id="inputJsonFile" accept=".json" style="display:none;">
                                        <div style="text-align:center;margin-top:8px;">
                                            <button class="w3-btn w3-theme-d4 w3-hover-green w3-card"
                                                id="btnImportJson" disabled="disabled">
                                                <span class="glyphicon glyphicon-import"></span> Import JSON
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel antrian import -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover display" id="tblDownload">
                                        <thead class="w3-theme-d4">
                                            <tr>
                                                <th>No</th>
                                                <th>Transaksi</th>
                                                <th>Tgl. Serah Terima</th>
                                                <th>Asal UDD</th>
                                                <th>Tempat<br>Pengambilan</th>
                                                <th>A</th>
                                                <th>B</th>
                                                <th>O</th>
                                                <th>AB</th>
                                                <th>Jumlah<br>Kantong</th>
                                                <th>Tgl. Import</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $noDl = 0;
                                            for ($b = 0; $b < count($dataDownload); $b++) {
                                                $noDl++;
                                                $dl = $dataDownload[$b];
                                                echo "<tr>";
                                                echo "<td class='text-right'>" . $noDl . ".</td>";
                                                echo "<td>" . htmlspecialchars($dl['notrans'])       . "</td>";
                                                echo "<td>" . htmlspecialchars($dl['hst_tgl'])       . "</td>";
                                                echo "<td>" . htmlspecialchars($dl['udd_asal_nama']) . "</td>";
                                                echo "<td>" . htmlspecialchars($dl['hst_asal'])      . "</td>";
                                                echo "<td align='right'>" . $dl['jumlahA']  . "</td>";
                                                echo "<td align='right'>" . $dl['jumlahB']  . "</td>";
                                                echo "<td align='right'>" . $dl['jumlahO']  . "</td>";
                                                echo "<td align='right'>" . $dl['jumlahAB'] . "</td>";
                                                echo "<td align='right'>" . $dl['jumlah']   . "</td>";
                                                echo "<td>" . htmlspecialchars($dl['tgl_import']) . "</td>";
                                                echo "<td align='center'>";
                                                echo "<a href='pmi" . $level . ".php?module=sr_aftap_knsdt&mode=proses&id=" . urlencode($dl['notrans']) . "&source=download'>PROSES</a>";
                                                echo " | ";
                                                echo "<a href='#' data-notrans='" . htmlspecialchars($dl['notrans']) . "' onclick=\"hapusDownload(this);return false;\">HAPUS</a>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                            if ($noDl == 0) { ?>
                                                <tr class="text-center">
                                                    <td colspan="12" style="padding:40px 20px !important; font-size:16px;">
                                                        Belum ada data konsolidasi yang diimport via Download.<br>
                                                        <small>Gunakan tombol import di atas untuk menambahkan data.</small>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- /tab-download -->

                        </div><!-- /tab-content -->
                    </div><!-- /panel-body -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filter -->
    <div class="modal fade" id="mFilter" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header w3-theme shadow">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">Filter Data</h4>
                </div>
                <div class="modal-body">
                    <form name="mFrmFilter" class="form-horizontal" action="" method="POST">
                        <div class="form-group">
                            <label class="control-label col-md-3">Tanggal</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control startdate"
                                        value="<?php echo $f_tanggal1; ?>" name="fltTanggal1">
                                    <span class="input-group-addon">s/d</span>
                                    <input type="text" class="form-control enddate"
                                        value="<?php echo $f_tanggal2; ?>" name="fltTanggal2">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="vfilter" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">OK</button>
                            <button type="submit" name="vreset" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">Reset</button>
                            <button class="w3-btn w3-theme w3-hover-indigo w3-card" data-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

<script src="bootsrap337/js/jquery.min.js"></script>
<script src="bootsrap337/js/bootstrap.min.js"></script>
<script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="bootsrap337/datepicker/custom.js"></script>
<script src="bootsrap337/chosen/chosen.jquery.js"></script>
<script src="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.js"></script>
<script src="bootsrap337/sweetalert2/sweetalert2@11"></script>
<script>
    $(document).ready(function() {

        var load = document.getElementById('loading');
        window.addEventListener('load', function() {
            load.style.display = 'none';
        });

        // Fungsi Inisialisasi DataTable yang lebih aman
        function initDataTable(tableId) {
            // Jangan inisialisasi jika tabel kosong (hanya ada baris pesan colspan)
            if ($(tableId + ' tbody td[colspan]').length > 0) {
                console.log('DataTable skip ' + tableId + ': tabel kosong (ada colspan)');
                return;
            }
            // Jangan inisialisasi jika tidak ada baris sama sekali
            if ($(tableId + ' tbody tr').length === 0) {
                console.log('DataTable skip ' + tableId + ': tidak ada baris');
                return;
            }

            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }

            $(tableId).DataTable({
                lengthMenu: [
                    [5, 10, 15, 25, 50, -1],
                    [5, 10, 15, 25, 50, 'All']
                ],
                pageLength: 25,
                destroy: true,
                ordering: true,
                searching: true,
                info: true,
                language: {
                    emptyTable: "Tidak ada data yang tersedia",
                    zeroRecords: "Tidak ditemukan data yang sesuai",
                    infoEmpty: "Tidak ada data yang ditampilkan"
                }
            });
        }

        // Inisialisasi kedua tabel saat halaman load
        // Guard sudah ada di dalam fungsi initDataTable, jadi langsung panggil saja
        setTimeout(function() {
            initDataTable('#tblDownload');
            initDataTable('#tblOnline');
        }, 500);

        // Saat ganti tab — guard sudah ada di dalam initDataTable
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('href');
            setTimeout(function() {
                if (target === '#tab-download') {
                    initDataTable('#tblDownload');
                } else if (target === '#tab-online') {
                    initDataTable('#tblOnline');
                }
            }, 200);
        });

        // ====================== UPLOAD JSON ======================
        $('#inputJsonFile').on('change', function() {
            var fileName = this.files[0] ? this.files[0].name : '';
            $('#selectedFileName').text(fileName ? '📄 ' + fileName : '');
            $('#btnImportJson').prop('disabled', !fileName);
        });

        var ua = document.getElementById('uploadArea');
        ua.addEventListener('dragover', e => {
            e.preventDefault();
            $(ua).css('background', '#f2d4d4');
        });
        ua.addEventListener('dragleave', () => $(ua).css('background', '#fff8f8'));
        ua.addEventListener('drop', function(e) {
            e.preventDefault();
            $(ua).css('background', '#fff8f8');
            var files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('inputJsonFile').files = files;
                $('#selectedFileName').text('📄 ' + files[0].name);
                $('#btnImportJson').prop('disabled', false);
            }
        });

        $('#btnImportJson').on('click', function() {
            var file = document.getElementById('inputJsonFile').files[0];
            if (!file) return;

            var formData = new FormData();
            formData.append('jsonFile', file);

            Swal.fire({
                title: 'Mengimport...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: 'serahterima/ksl_terima_import_json.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    Swal.close();
                    if (res.status === 'success') {
                        Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Gagal!', 'Gagal upload file.', 'error');
                }
            });
        });

        window.hapusDownload = function(el) {
            var notrans = $(el).data('notrans');
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Yakin hapus: ' + notrans + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus'
            }).then(r => {
                if (r.isConfirmed) {
                    $.post('serahterima/ksl_terima_hapus_dl.php', {
                        notrans: notrans
                    }, function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Dihapus!', res.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    }, 'json');
                }
            });
        };
    });
</script>