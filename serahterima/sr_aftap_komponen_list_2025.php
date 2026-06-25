<?php
session_start();
$msg = "";
require_once('clogin.php');
require_once('config/dbi_connect.php');

$leveluser = $_SESSION['level'];
$namauser = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];

$udd = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama, id FROM utd WHERE aktif='1'"));
$id_uddaktif = $udd['id'];
$nama_uddaktif = $udd['nama'];

(isset($_SESSION['tanggal1'])) ? $f_tanggal1 = $_SESSION['tanggal1'] : $f_tanggal1 = date('Y-m-d');
(isset($_SESSION['tanggal2'])) ? $f_tanggal2 = $_SESSION['tanggal2'] : $f_tanggal2 = date('Y-m-d');
(isset($_SESSION['status'])) ? $f_status = $_SESSION['status'] : $f_status = "";

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
?>

<head>
    <meta charset="utf-8">
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
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .modal-fullscreen {
            width: 98%;
            padding: 0;
        }

        .modal-content {
            width: 100%;
            border-radius: 25px;
            margin: 0;
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
            to {
                transform: rotate(360deg);
            }
        }

        .swal2-popup {
            font-size: 1.6rem !important;
        }
    </style>
</head>

<body>
    <div id="loading"></div>
    <div class="container-fluid" style="margin: 30px;">
        <div class="row">
            <div class="col-md-12">
                <div class="panel w3-border-theme shadow">
                    <div class="panel-heading w3-theme-d5 clearfix">
                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-8 text-left text-shadow" style="font-size: 150%; font-weight: bold;">
                            DATA FORMULIR SERAH TERIMA DARAH & SAMPEL
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-4 text-right">
                            <a href="" class="w3-btn w3-theme w3-hover-yellow" data-toggle="modal" data-target="#mFilter">Filter</a>
                            <a href="?module=serahterima" class="w3-btn w3-theme w3-hover-yellow">Kembali</a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12"><?php echo $msg; ?></div>
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover table-md display" id="dtaudittrail">
                                        <thead class="w3-theme-d4">
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th>Transaksi</th>
                                                <th>Tanggal</th>
                                                <th>Jam</th>
                                                <th>Asal</th>
                                                <th>Suhu</th>
                                                <th>Jumlah</th>
                                                <th>UDD Asal</th>
                                                <th>UDD Penerima</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $qry = "SELECT 
                                            h.hst_notrans, 
                                            h.hst_tgl, 
                                            DATE_FORMAT(h.hst_tgl,'%d-%m-%Y') as dt, 
                                            DATE_FORMAT(h.hst_tgl,'%H:%i') as tm, 
                                            h.hst_asal,
                                            h.hst_suhuterima,
                                            COUNT(d.dst_notrans) as jumlah,
                                            h.hst_dariudd,
                                            COALESCE(ua.nama, '-') as udd_asal_nama,
                                            h.hst_keudd,
                                            COALESCE(up.nama, '-') as udd_penerima_nama,
                                            h.up_data
                                        FROM serahterima h 
                                        INNER JOIN serahterima_detail d ON d.dst_notrans = h.hst_notrans
                                        LEFT JOIN utd ua ON ua.id = h.hst_dariudd
                                        LEFT JOIN utd up ON up.id = h.hst_keudd
                                        WHERE h.hst_modul = 'KARANTINA' 
                                          AND DATE(h.hst_tgl) BETWEEN '$f_tanggal1' AND '$f_tanggal2'
                                        GROUP BY h.hst_notrans, h.hst_tgl, h.hst_asal, h.hst_suhuterima,
                                                 h.hst_dariudd, h.hst_keudd, h.up_data";

                                            $query = mysqli_query($dbi, $qry);
                                            $no = 0;
                                            while ($row = mysqli_fetch_assoc($query)) {
                                                $no++;
                                                $status_kons = !empty($row['hst_keudd'])
                                                    ? '<span class="label label-warning">Konsolidasi</span>'
                                                    : '<span class="label label-success">Internal</span>';
                                            ?>
                                                <tr>
                                                    <td class="text-right"><?= $no ?></td>
                                                    <td><?= $row['hst_notrans'] ?></td>
                                                    <td><?= $row['dt'] ?></td>
                                                    <td><?= $row['tm'] ?></td>
                                                    <td><?= $row['hst_asal'] ?></td>
                                                    <td><?= $row['hst_suhuterima'] ?><sup>o</sup>C</td>
                                                    <td class="text-center"><?= $row['jumlah'] ?></td>
                                                    <td><?= htmlspecialchars($row['udd_asal_nama']) ?></td>
                                                    <td><?= htmlspecialchars($row['udd_penerima_nama']) ?></td>
                                                    <td class="text-center"><?= $status_kons ?></td>
                                                    <td>
                                                        <a href="?module=sr_rpt_ktg&no=<?= $row['hst_notrans'] ?>">Kantong</a> |
                                                        <a href="?module=sr_rpt_imltd&no=<?= $row['hst_notrans'] ?>">IMLTD</a> |
                                                        <a href="?module=sr_rpt_kgd&no=<?= $row['hst_notrans'] ?>">KGD</a> |
                                                        <a href="?module=sr_rpt_nat&no=<?= $row['hst_notrans'] ?>">NAT</a>
                                                        <a href="#" class="btn-export-json" data-id="<?= $row['hst_notrans'] ?>"
                                                            data-toggle="modal" data-target="#mExportJson"> | Konsolidasi via Download</a>
                                                        <?php if ($row['up_data'] == '0'): ?>
                                                            <a href="#" class="btn-kirim" data-id="<?= $row['hst_notrans'] ?>"
                                                                data-toggle="modal" data-target="#mKirim"> | Konsolidasi via Online</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filter -->
    <div class="modal fade" id="mFilter" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header w3-theme shadow">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">Filter Data</h4>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <label class="control-label col-md-3">Tanggal</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control startdate" name="fltTanggal1" id="fltTanggal1" value="<?= $f_tanggal1 ?>">
                                    <span class="input-group-addon">s/d</span>
                                    <input type="text" class="form-control enddate" name="fltTanggal2" id="fltTanggal2" value="<?= $f_tanggal2 ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Status</label>
                            <div class="col-sm-9">
                                <select name="fltstatus" class="form-control chosen-select">
                                    <option value="0" <?= $f_status == "0" ? 'selected' : '' ?>>Semua</option>
                                    <option value="1" <?= $f_status == "1" ? 'selected' : '' ?>>Terkirim</option>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="vfilter" class="w3-btn w3-theme-d4">OK</button>
                    <button type="submit" name="vreset" class="w3-btn w3-theme-d4">Reset</button>
                    <button class="w3-btn w3-theme" data-dismiss="modal">Batal</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kirim -->
    <div class="modal fade" id="mKirim" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header w3-theme shadow">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">Pengiriman Kantong</h4>
                </div>
                <div class="modal-body">
                    <form id="mFrmKirim">
                        <div class="form-group">
                            <label class="control-label col-md-3">No Transaksi</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="InpNotransaksi" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Dari</label>
                            <div class="col-md-9">
                                <input type="hidden" id="InpDari" value="<?= $id_uddaktif ?>">
                                <input type="text" class="form-control" value="<?= $nama_uddaktif ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Kirim ke</label>
                            <div class="col-sm-9">
                                <select name="InpKirimke" class="form-control chosen-select">
                                    <?php
                                    $query_udd = mysqli_query($dbi, "SELECT nama, id FROM utd WHERE aktif='0'");
                                    while ($dt = mysqli_fetch_assoc($query_udd)) {
                                        $selected = ($dt['id'] == '317D') ? 'selected' : '';
                                        echo "<option value='{$dt['id']}' $selected>{$dt['nama']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="vKirim" class="w3-btn w3-theme-d4">Kirim Kantong</button>
                    <button class="w3-btn w3-theme" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export JSON -->
    <div class="modal fade" id="mExportJson" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header w3-theme shadow">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">Export JSON — Serah Terima</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Lengkapi informasi UDD sebelum mengekspor data.
                    </div>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-md-4">No. Transaksi</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="expNoTransaksi" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">UDD Asal <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control" id="expUddAsal">
                                    <?php
                                    $q = mysqli_query($dbi, "SELECT id, nama FROM utd ORDER BY nama");
                                    while ($r = mysqli_fetch_assoc($q)) {
                                        $sel = ($r['id'] == $id_uddaktif) ? 'selected' : '';
                                        echo "<option value='{$r['id']}' $sel>{$r['nama']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">UDD Penerima <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control" id="expUddPenerima">
                                    <option value="">-- Pilih UDD Penerima --</option>
                                    <?php
                                    mysqli_data_seek($q, 0);
                                    while ($r = mysqli_fetch_assoc($q)) {
                                        echo "<option value='{$r['id']}'>{$r['nama']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnDoExportJson" class="w3-btn w3-theme-d4">
                        <span class="glyphicon glyphicon-download-alt"></span> Export JSON
                    </button>
                    <button class="w3-btn w3-theme" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="bootsrap337/datepicker/custom.js"></script>
    <script src="bootsrap337/chosen/chosen.jquery.js"></script>
    <script src="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.js"></script>
    <script src="bootsrap337/sweetalert2/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Datepicker
            $('.startdate, .enddate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            // DataTable
            $('#dtaudittrail').DataTable({
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ]
            });

            // Loading
            $('#loading').fadeOut();

            // Kirim Kantong
            $(document).on('click', '.btn-kirim', function() {
                $('#InpNotransaksi').val($(this).data('id'));
            });

            // Export JSON
            $(document).on('click', '.btn-export-json', function() {
                $('#expNoTransaksi').val($(this).data('id'));
            });

            // Tombol Do Export JSON
            $('#btnDoExportJson').on('click', function() {
                var noTrans = $('#expNoTransaksi').val();
                var uddAsal = $('#expUddAsal').val();
                var uddPenerima = $('#expUddPenerima').val();

                if (!uddAsal || !uddPenerima) {
                    Swal.fire('Peringatan', 'Harap pilih UDD Asal dan UDD Penerima.', 'warning');
                    return;
                }

                var url = 'serahterima/sr_aftap_export_json.php?noTransaksi=' + encodeURIComponent(noTrans) +
                    '&uddAsal=' + encodeURIComponent(uddAsal) +
                    '&uddPenerima=' + encodeURIComponent(uddPenerima);

                $('#mExportJson').modal('hide');

                // Buat download tanpa meninggalkan halaman
                var link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', ''); // memaksa download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Refresh halaman setelah download dimulai
                setTimeout(function() {
                    location.reload();
                }, 800); // beri waktu 800ms agar download sempat mulai
            });

            // Kirim via Online
            $('#vKirim').on('click', function() {
                var noTransaksi = $('#InpNotransaksi').val();
                var dari = $('#InpDari').val();
                var kirimKe = $('select[name="InpKirimke"]').val();

                if (!noTransaksi || !dari || !kirimKe) {
                    Swal.fire("Gagal", "Harap isi semua data.", "error");
                    return;
                }

                $('#mKirim').modal('hide');
                Swal.fire({
                    title: "Mengirim...",
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: 'serahterima/sr_aftap_kirimkantong.php',
                    type: 'POST',
                    data: {
                        noTransaksi,
                        dari,
                        kirimKe
                    },
                    dataType: "json",
                    success: function(res) {
                        Swal.close();
                        if (res && res.status === "success") {
                            Swal.fire("Sukses", res.message || "Data berhasil dikirim", "success").then(() => location.reload());
                        } else {
                            Swal.fire("Gagal", res.message || "Terjadi kesalahan", "error");
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire("Error", "Gagal menghubungi server", "error");
                    }
                });
            });

            $('.chosen-select').chosen({
                width: "100%"
            });
        });
    </script>
</body>