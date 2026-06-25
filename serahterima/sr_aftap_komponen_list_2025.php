<?php
session_start();
$msg = "";
require_once('clogin.php');
require_once('config/dbi_connect.php');
$leveluser = $_SESSION['level'];
$namauser = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];
$udd = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama`,`id` FROM `utd` WHERE `aktif`='1';"));
$id_uddaktif = $udd['id'];
$nama_uddaktif = $udd['nama'];

(isset($_SESSION['tanggal1'])) ? $f_tanggal1 = $_SESSION['tanggal1'] : $f_tanggal1 = date('Y-m-d');
(isset($_SESSION['tanggal2'])) ? $f_tanggal2 = $_SESSION['tanggal2'] : $f_tanggal2 = date('Y-m-d');
(isset($_SESSION['status'])) ? $f_status = $_SESSION['status'] : $f_status = "";

if (isset($_POST['vfilter'])) {
    $f_status       = $_SESSION['status']    = $_POST['fltstatus'];
    $f_tanggal1     = $_SESSION['tanggal1']  = $_POST['fltTanggal1'];
    $f_tanggal2     = $_SESSION['tanggal2']    = $_POST['fltTanggal2'];
}
if (isset($_POST['vreset'])) {
    $f_status        = "";
    $f_tanggal1     = date('Y-m-d');
    $f_tanggal2     = date('Y-m-d');
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

        .btn-pref .btn {
            border-radius: 0 !important;
        }

        .modal-fullscreen {
            width: 98%;
            padding: 0;
        }

        .modal-content {
            /* min-height: 90%; */
            width: 100%;
            border-radius: 25;
            margin: 0 0;
        }

        .modal-footer {
            border-radius: 25;
            bottom: 0px;
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
            /* word-break:break-all; */
        }

        .table tbody td {
            font-size: 1em;
            white-space: nowrap;
            vertical-align: middle !important;
        }

        .text-vertical {
            vertical-align: middle;
            text-align: center;
            transform: rotate(-90deg);
            white-space: nowrap;
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
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        a {
            text-decoration: none !important;
        }

        .table td.text {
            max-width: 300px;
        }

        .table td.text span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            max-width: 100%;
        }

        .swal2-popup {
            font-size: 1.6rem !important;
        }

        .swal-footer {
            text-align: center;
        }

        .custom-swal {
            background: linear-gradient(to bottom, white, red) !important;
            border-radius: 15px !important;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.5) !important;
            width: 800px !important;
            max-width: 95% !important;
            padding: 20px !important;
            overflow: hidden;
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
                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-8 text-left text-shadow" style="font-size: 150%;font-weight: bold;">DATA FORMULIR SERAH TERIMA DARAH & SAMPEL</div>
                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-4 text-right">
                            <a href="" class="w3-btn w3-theme w3-hover-yellow" data-toggle="modal" data-target="#mFilter">Filter</a>
                            <!-- <a href="" class="w3-btn w3-theme w3-hover-yellow" data-toggle="modal" data-target="#mImportJson">TERIMA KONSOLIDASI</a> -->
                            <a href="?module=serahterima" class="w3-btn w3-theme w3-hover-yellow">Kembali</a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12"><?php echo $msg; ?></div>
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-responsive table-bordered table-striped table-md table-hover table-md display" id="dtaudittrail">
                                        <thead class="w3-theme-d4" style="height: 40px;">
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
    h.`hst_notrans`, 
    h.`hst_tgl`, 
    DATE_FORMAT(h.`hst_tgl`,'%d-%m-%Y') as `dt`, 
    DATE_FORMAT(h.`hst_tgl`,'%H:%i') as `tm`, 
    h.`hst_asal`,
    h.`hst_user`, 
    h.`hst_kode_alat`,
    h.`hst_suhuterima`,
    h.`hst_kondisiumum`, 
    count(d.`dst_notrans`) as `jumlah`, 
    h.up_data,
    -- === TAMBAHAN BARU ===
    h.`hst_dariudd`,
    COALESCE(ua.`nama`, '-') as `udd_asal_nama`,
    h.`hst_keudd`,
    COALESCE(up.`nama`, '-') as `udd_penerima_nama`
FROM `serahterima` h 
INNER JOIN serahterima_detail d ON d.`dst_notrans`=h.`hst_notrans`
LEFT JOIN `utd` ua ON ua.`id` = h.`hst_dariudd`
LEFT JOIN `utd` up ON up.`id` = h.`hst_keudd`
WHERE `hst_modul`='KARANTINA' 
  AND (DATE(h.`hst_tgl`) BETWEEN '$f_tanggal1' AND '$f_tanggal2')
GROUP BY h.`hst_notrans`, h.`hst_tgl`, h.`hst_asal`, h.`hst_user`, 
         h.`hst_kode_alat`, h.`hst_suhuterima`, h.`hst_kondisiumum`,
         h.`hst_dariudd`, h.`hst_keudd`";

                                            $query = mysqli_query($dbi, $qry);
                                            $no = 0;
                                            while ($row = mysqli_fetch_assoc($query)) {
                                                $no++;
                                                echo '<tr>';
                                                echo '<td class="text-right">' . $no . '</td>';
                                                echo '<td>' . $row['hst_notrans'] . '</td>';
                                                echo '<td>' . $row['dt'] . '</td>';
                                                echo '<td>' . $row['tm'] . '</td>';
                                                echo '<td>' . $row['hst_asal'] . '</td>';
                                                echo '<td>' . $row['hst_suhuterima'] . '<sup>o</sup>C</td>';
                                                echo '<td class="text-center">' . $row['jumlah'] . '</td>';

                                                // === TAMBAHAN BARU ===
                                                echo '<td>' . htmlspecialchars($row['udd_asal_nama']) . '</td>';
                                                echo '<td>' . htmlspecialchars($row['udd_penerima_nama']) . '</td>';

                                                // Status Konsolidasi
                                                $status_kons = '<span class="label label-default">-</span>';
                                                if (!empty($row['hst_keudd'])) {
                                                    $status_kons = '<span class="label label-warning">Konsolidasi</span>';
                                                } else {
                                                    $status_kons = '<span class="label label-success">Internal</span>';
                                                }
                                                echo '<td class="text-center">' . $status_kons . '</td>';

                                                // Aksi (tetap sama seperti sebelumnya)
                                                echo '<td>';
                                                echo '<a href="?module=sr_rpt_ktg&no=' . $row['hst_notrans'] . '">Kantong</a> | ';
                                                echo '<a href="?module=sr_rpt_imltd&no=' . $row['hst_notrans'] . '">IMLTD</a> | ';
                                                echo '<a href="?module=sr_rpt_kgd&no=' . $row['hst_notrans'] . '">KGD</a> | ';
                                                echo '<a href="?module=sr_rpt_nat&no=' . $row['hst_notrans'] . '">NAT</a> ';
                                                // if (empty($row['hst_udd_penerima'])) {
                                                echo '<a href="#" class="btn-export-json" data-id="' . $row['hst_notrans'] . '" data-toggle="modal" data-target="#mExportJson"> | Konsolidasi via Download</a>';
                                                // }
                                                if ($row['up_data'] == '0') {
                                                    echo '<a href="#" class="btn-kirim" data-id="' . $row['hst_notrans'] . '" data-toggle="modal" data-target="#mKirim"> | Konsolidasi via Online</a>';
                                                }
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
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

    <div class="modal fade" id="mFilter" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header w3-theme shadow">
                    <button type="button" class="close " data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">Filter Data</h4>
                </div>
                <div class="modal-body">
                    <form name="mFrmFilter" class="form-horizontal" id="mFrmFilter" action="" method="POST">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="fltTanggal1">Tanggal</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control startdate" value="<?php echo $f_tanggal1; ?>" name="fltTanggal1" id="fltTanggal1" />
                                    <span class="input-group-addon input-sm">s/d</span>
                                    <input type="text" class="form-control enddate" value="<?php echo $f_tanggal2; ?>" name="fltTanggal2" id="fltTanggal2" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="fltStatus">Status</label>
                            <div class="col-sm-9">
                                <select name="fltStatus" class="form-control input-sm chosen-select">
                                    <?php
                                    $arr_status = array("0" => "Semua", "1" => "Terkirim");
                                    foreach ($arr_status as $val => $cap) {
                                        if ($val == $f_status) {
                                            echo '<option value="' . $val . '" selected>' . $cap . '</option>';
                                        } else {
                                            echo '<option value="' . $val . '">' . $cap . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="vfilter" id="vfilter" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">OK</button>
                    <button type="submit" name="vreset" id="vreset" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">Reset</button>
                    <button class="w3-btn w3-theme w3-hover-indigo w3-card" data-dismiss="modal">Batal</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mKirim" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header w3-theme shadow">
                    <button type="button" class="close " data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">Pengiriman Kantong</h4>
                </div>
                <div class="modal-body">
                    <form name="mFrmKirim" class="form-horizontal" id="mFrmKirim" action="" method="POST">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="InpNomor">No Transaksi</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm" name="InpNotransaksi" id="InpNotransaksi" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="InpDari">Dari</label>
                            <div class="col-md-9">
                                <input type="hidden" class="form-control" value="<?php echo $id_uddaktif; ?>" name="InpDari" id="InpDari">
                                <input type="text" class="form-control input-sm" value="<?php echo $nama_uddaktif; ?>" name="InpDariNama" id="InpDariNama" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="InpKirimke">Kirim ke</label>
                            <div class="col-sm-9">
                                <select name="InpKirimke" class="form-control chosen-select">
                                    <?php
                                    $query_udd = mysqli_query($dbi, "SELECT `nama`,`id` FROM `utd` WHERE `aktif`='0';");
                                    while ($dt = mysqli_fetch_assoc($query_udd)) {
                                        if ($dt['id'] == '317D') {
                                            echo '<option value="' . $dt['id'] . '" selected>' . $dt['nama'] . '</option>';
                                        } else {
                                            echo '<option value="' . $dt['id'] . '">' . $dt['nama'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" name="vKirim" id="vKirim" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">Kirim Kantong</button>
                    <button class="w3-btn w3-theme w3-hover-indigo w3-card" data-dismiss="modal">Batal</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Export JSON -->
    <div class="modal fade" id="mExportJson" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header w3-theme shadow">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">
                        <span class="glyphicon glyphicon-export"></span> Export JSON — Serah Terima
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size:0.95em; margin-bottom:14px;">
                        <span class="glyphicon glyphicon-info-sign"></span>
                        Lengkapi informasi UDD sebelum mengekspor data. Data ini akan disimpan pada record serah terima.
                    </div>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-md-4">No. Transaksi</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control input-sm" id="expNoTransaksi" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">UDD Asal <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control" id="expUddAsal" name="expUddAsal">
                                    <option value="">-- Pilih UDD Asal --</option>
                                    <?php
                                    $qry_udd_exp1 = mysqli_query($dbi, "SELECT id, nama FROM utd ORDER BY nama ASC;");
                                    while ($dt_exp1 = mysqli_fetch_assoc($qry_udd_exp1)) {
                                        $sel1 = ($dt_exp1['id'] == $id_uddaktif) ? ' selected' : '';
                                        echo '<option value="' . htmlspecialchars($dt_exp1['id']) . '"' . $sel1 . '>' . htmlspecialchars($dt_exp1['nama']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <small class="text-muted">UDD/UTD pengirim (asal) data ini</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">UDD Penerima <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control" id="expUddPenerima" name="expUddPenerima">
                                    <option value="">-- Pilih UDD Penerima --</option>
                                    <?php
                                    $qry_udd_exp2 = mysqli_query($dbi, "SELECT id, nama FROM utd ORDER BY nama ASC;");
                                    while ($dt_exp2 = mysqli_fetch_assoc($qry_udd_exp2)) {
                                        echo '<option value="' . htmlspecialchars($dt_exp2['id']) . '">' . htmlspecialchars($dt_exp2['nama']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <small class="text-muted">UDD/UTD tujuan penerima file JSON ini</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnDoExportJson" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">
                        <span class="glyphicon glyphicon-download-alt"></span> Export JSON
                    </button>
                    <button class="w3-btn w3-theme w3-hover-indigo w3-card" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import JSON -->
    <div class="modal fade" id="mImportJson" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header w3-green shadow">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">
                        <span id="importModalTitle">Import Data Serah Terima dari File JSON</span>
                    </h4>
                </div>
                <div class="modal-body">

                    <!-- STEP 1: Pilih File -->
                    <div id="stepPilihFile">
                        <div style="margin-bottom:12px;">
                            <span class="label label-success" style="font-size:1em; padding:5px 12px;">Langkah 1 dari 3</span>
                            <strong style="margin-left:8px;">Pilih File &amp; Opsi Import</strong>
                        </div>
                        <form id="frmImportJson" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="control-label">File JSON</label>
                                <input type="file" class="form-control" id="fileJson" name="fileJson" accept=".json" required>
                                <small class="text-muted">Pilih file JSON (hasil ekspor dari sistem ini)</small>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Jika data sudah ada, pilih tindakan berikut:</label>
                                <select class="form-control" name="aksiDuplikat" id="aksiDuplikat">
                                    <option value="skip">Lewati — Data lama tetap, data dari file yang sama akan diabaikan</option>
                                    <option value="update">Perbarui Data — Data lama akan diganti dengan data dari file</option>
                                    <option value="replace">Ganti Semua Data — Semua data lama dihapus dan diganti dari file</option>
                                </select>
                                <div style="margin-top:8px; padding:8px 10px; background:#fff8e1; border-left:4px solid #ffc107; border-radius:3px; font-size:0.9em;">
                                    <strong>Lewati</strong>: Aman, tidak mengubah data yang sudah ada.<br>
                                    <strong>Perbarui</strong>: Mengubah field data yang sudah ada dengan nilai terbaru dari file.<br>
                                    <strong>Ganti Total</strong>: Menghapus detail lama lalu memasukkan ulang seluruh data dari file.
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Tindakan jika ada data yang gagal diimport:</label>
                                <select class="form-control" name="aksiError" id="aksiError">
                                    <option value="commit_success">Simpan yang berhasil, laporkan yang gagal (Partial)</option>
                                    <option value="rollback_all">Batalkan semua jika ada 1 yang gagal (All-or-Nothing)</option>
                                </select>
                                <div style="margin-top:8px; padding:8px 10px; background:#e8f4fd; border-left:4px solid #5bc0de; border-radius:3px; font-size:0.9em;">
                                    <strong>Partial</strong>: Data yang berhasil tetap tersimpan. Data yang gagal bisa diunduh sebagai JSON untuk dicoba ulang.<br>
                                    <strong>All-or-Nothing</strong>: Jika ada 1 saja yang gagal, seluruh import dibatalkan &amp; tidak ada yang tersimpan.
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- STEP 2: Pratinjau Validasi -->
                    <div id="stepValidasi" style="display:none;">
                        <div style="margin-bottom:12px;">
                            <span class="label label-warning" style="font-size:1em; padding:5px 12px;">Langkah 2 dari 3</span>
                            <strong style="margin-left:8px;">Pratinjau &amp; Verifikasi Data</strong>
                        </div>
                        <div id="validasiContent"></div>
                    </div>

                    <!-- STEP 3: Kroscek Fisik -->
                    <div id="stepKroscek" style="display:none;">
                        <div style="margin-bottom:12px;">
                            <span class="label label-danger" style="font-size:1em; padding:5px 12px;">Langkah 3 dari 3</span>
                            <strong style="margin-left:8px;">Kroscek Fisik Kantong</strong>
                        </div>

                        <div class="alert alert-info" style="font-size:0.9em; padding:8px 12px; margin-bottom:10px;">
                            <span class="glyphicon glyphicon-barcode"></span>
                            <strong>Scan atau ketik</strong> nomor kantong fisik satu per satu, lalu tekan <kbd>Enter</kbd>.
                            Sistem akan mencocokkan dengan daftar kantong di file JSON.
                        </div>

                        <!-- Input scan -->
                        <div class="input-group" style="margin-bottom:10px;">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-barcode"></span></span>
                            <input type="text" id="inputScanKantong" class="form-control input-sm"
                                placeholder="Scan / ketik nomor kantong, tekan Enter..."
                                autocomplete="off" autocorrect="off" spellcheck="false" style="font-size:1.1em;">
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-sm" id="btnAddScan" type="button">
                                    <span class="glyphicon glyphicon-plus"></span> Tambah
                                </button>
                                <button class="btn btn-danger btn-sm" id="btnResetScan" type="button"
                                    title="Reset semua hasil scan">
                                    <span class="glyphicon glyphicon-refresh"></span> Reset
                                </button>
                            </span>
                        </div>

                        <!-- Progress -->
                        <div id="kroscekProgress" style="margin-bottom:10px;"></div>

                        <!-- Ringkasan badges -->
                        <div id="kroscekBadges" style="margin-bottom:10px; font-size:0.95em;"></div>

                        <!-- Tabel kroscek -->
                        <div style="max-height:280px; overflow-y:auto; border:1px solid #ddd; border-radius:4px;">
                            <table class="table table-condensed table-bordered" id="tblKroscek" style="margin-bottom:0; font-size:0.85em;">
                                <thead style="background:#e8e8e8; position:sticky; top:0; z-index:1;">
                                    <tr>
                                        <th class="text-center" style="width:40px;">#</th>
                                        <th>No. Kantong</th>
                                        <th class="text-center" style="width:110px;">Status Fisik</th>
                                        <th class="text-center" style="width:70px;">Di JSON</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyKroscek"></tbody>
                            </table>
                        </div>

                        <!-- Opsi jika tidak semua terkroscek -->
                        <div id="kroscekWarning" style="display:none; margin-top:10px;">
                            <div class="alert alert-warning" style="padding:8px 12px; font-size:0.9em; margin-bottom:6px;">
                                <span class="glyphicon glyphicon-warning-sign"></span>
                                <strong>Ada kantong di JSON yang belum discan.</strong>
                                Centang opsi di bawah jika ingin tetap melanjutkan import.
                            </div>
                            <div class="checkbox" style="margin:0;">
                                <label style="font-size:0.9em;">
                                    <input type="checkbox" id="cbkLanjutMeski">
                                    Saya mengerti, tetap lanjutkan import meskipun ada kantong yang belum dikroscek
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <!-- Footer Step 1 -->
                    <div id="footerStep1">
                        <button type="button" id="btnValidasiFile" class="w3-btn w3-teal w3-hover-dark-grey">
                            <span class="glyphicon glyphicon-search"></span> Validasi &amp; Pratinjau Data
                        </button>
                        <button class="w3-btn w3-red w3-hover-dark-red" data-dismiss="modal">Batal</button>
                    </div>
                    <!-- Footer Step 2 -->
                    <div id="footerStep2" style="display:none;">
                        <button type="button" id="btnLanjutKroscek" class="w3-btn w3-blue w3-hover-dark-blue">
                            <span class="glyphicon glyphicon-barcode"></span> Lanjut ke Kroscek Fisik
                        </button>
                        <button type="button" id="btnKembaliStep1" class="w3-btn w3-orange w3-hover-dark-grey">
                            <span class="glyphicon glyphicon-arrow-left"></span> Kembali
                        </button>
                        <button class="w3-btn w3-red w3-hover-dark-red" data-dismiss="modal">Batal</button>
                    </div>
                    <!-- Footer Step 3 -->
                    <div id="footerStep3" style="display:none;">
                        <!-- <button type="button" id="btnSimpanProgress" class="w3-btn w3-blue-grey w3-hover-dark-grey" title="Simpan progress scan ke browser, bisa dilanjutkan nanti">
                            <span class="glyphicon glyphicon-floppy-disk"></span> Simpan Progress
                        </button>
                        <button type="button" id="btnMuatProgress" class="w3-btn w3-cyan w3-hover-dark-cyan" title="Muat progress scan yang pernah disimpan" style="display:none;">
                            <span class="glyphicon glyphicon-folder-open"></span> Lanjutkan Progress
                        </button> -->
                        <button type="button" id="btnKonfirmasiImport" class="w3-btn w3-green w3-hover-dark-green">
                            <span class="glyphicon glyphicon-import"></span> Konfirmasi &amp; Jalankan Import
                        </button>
                        <button type="button" id="btnKembaliStep2" class="w3-btn w3-orange w3-hover-dark-grey">
                            <span class="glyphicon glyphicon-arrow-left"></span> Kembali
                        </button>
                        <button class="w3-btn w3-red w3-hover-dark-red" data-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="bootsrap337/js/jquery.min.js"></script>
<script src="bootsrap337/js/bootstrap.min.js"></script>
<script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="bootsrap337/datepicker/custom.js"></script>
<script src="bootsrap337/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.js"></script>
<script src="bootsrap337/sweetalert2/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        setDateRangePicker(".startdate", ".enddate")
        var table = $('#dtaudittrail').DataTable({
            lengthMenu: [
                [5, 10, 15, 25, 50, -1],
                [5, 10, 15, 25, 50, 'All']
            ]
        });
        var load = document.getElementById("loading");
        window.addEventListener('load', function() {
            load.style.display = "none";
        });

        $(document).on('click', '.btn-kirim', function() {
            var noTrans = $(this).data('id');
            $('#InpNotransaksi').val(noTrans);
        });

        // Handler tombol EXPORT JSON -> buka modal
        $('body').on('click', '.btn-export-json', function() {
            var noTrans = $(this).data('id');
            $('#expNoTransaksi').val(noTrans);
        });

        // Tombol Do Export JSON di modal
        $('#btnDoExportJson').on('click', function() {
            var noTrans = $('#expNoTransaksi').val();
            var uddAsal = $('#expUddAsal').val();
            var uddPenerima = $('#expUddPenerima').val();
            if (!uddAsal || !uddPenerima) {
                Swal.fire('Peringatan', 'Harap pilih UDD Asal dan UDD Penerima.', 'warning');
                return;
            }
            $('#mExportJson').modal('hide');
            window.location.href = 'serahterima/sr_aftap_export_json.php?noTransaksi=' + encodeURIComponent(noTrans) +
                '&uddAsal=' + encodeURIComponent(uddAsal) +
                '&uddPenerima=' + encodeURIComponent(uddPenerima);
        });

        $('#vKirim').on('click', function() {
            var noTransaksi = $('#InpNotransaksi').val();
            var dari = $('#InpDari').val();
            var kirimKe = $('select[name="InpKirimke"]').val();
            if (!noTransaksi || !dari || !kirimKe) {
                Swal.fire({
                    title: "Gagal!",
                    text: "Harap isi semua data sebelum mengirim.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                return;
            }
            $('#mKirim').modal('hide');
            Swal.fire({
                title: "Mengirim Data...",
                text: "Harap tunggu, data sedang dikirim.",
                icon: "info",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'serahterima/sr_aftap_kirimkantong.php',
                type: 'POST',
                data: {
                    noTransaksi: noTransaksi,
                    dari: dari,
                    kirimKe: kirimKe
                },
                dataType: "json",
                success: function(response) {
                    console.log("Response dari server:", response);
                    Swal.close();

                    if (response && response.status === "success") {
                        Swal.fire({
                            title: "Sukses!",
                            text: response.message || "Data berhasil dikirim.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: response.message || "Terjadi kesalahan saat mengirim data.",
                            icon: "error",
                            confirmButtonText: "Coba Lagi"
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);

                    let errorMessage = "Terjadi kesalahan saat menghubungi server.";
                    if (jqXHR.responseText) {
                        try {
                            let errorResponse = JSON.parse(jqXHR.responseText);
                            errorMessage = errorResponse.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing JSON response:", e);
                        }
                    }

                    Swal.close();
                    Swal.fire({
                        title: "Gagal!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: "Coba Lagi"
                    });
                }
            });
        });
    });

    $('.chosen-select').chosen({
        width: "100%"
    });

    function setDateRangePicker(start, end) {
        $(start).datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        }).on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            $(end).datepicker('setStartDate', startDate);
            if ($(end).val() === '') {
                $(end).datepicker('setDate', startDate);
            }
        });

        $(end).datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        }).on('changeDate', function(selected) {
            var endDate = new Date(selected.date.valueOf());
            $(start).datepicker('setEndDate', endDate);
        });
    }

    // ========== Kroscek Fisik: state ==========
    var kantongListFromJson = []; // [{nokantong, kodedonor, no_aftap}, ...]
    var scannedKantong = {}; // { nokantong: true }
    var extraScanned = []; // scanned but NOT in JSON

    function renderKroscekTable() {
        var matched = 0;
        var missing = 0;
        var extra = extraScanned.length;
        var rows = '';

        // Baris dari JSON (cocok / belum scan)
        $.each(kantongListFromJson, function(i, item) {
            var isScanned = scannedKantong.hasOwnProperty(item.nokantong);
            if (isScanned) matched++;
            else missing++;

            var rowBg = isScanned ? 'background:#dff0d8;' : 'background:#fff8e1;';
            var badge = isScanned ?
                '<span class="label label-success"><span class="glyphicon glyphicon-ok"></span> Cocok</span>' :
                '<span class="label label-warning"><span class="glyphicon glyphicon-time"></span> Belum scan</span>';

            rows += '<tr style="' + rowBg + '">' +
                '<td class="text-center">' + (i + 1) + '</td>' +
                '<td><strong>' + item.nokantong + '</strong></td>' +
                '<td class="text-center">' + badge + '</td>' +
                '<td class="text-center"><span class="label label-info">Ada</span></td>' +
                '</tr>';
        });

        // Baris ekstra (scan tidak ada di JSON)
        $.each(extraScanned, function(i, nk) {
            rows += '<tr style="background:#f2dede;">' +
                '<td class="text-center">—</td>' +
                '<td><strong>' + nk + '</strong></td>' +
                '<td class="text-center"><span class="label label-danger"><span class="glyphicon glyphicon-remove"></span> Tidak ada di JSON</span></td>' +
                '<td class="text-center"><span class="label label-default">—</span></td>' +
                '</tr>';
        });

        $('#tbodyKroscek').html(rows);

        // Progress bar
        var total = kantongListFromJson.length;
        var pct = total > 0 ? Math.round((matched / total) * 100) : 0;
        var pbClass = pct === 100 ? 'progress-bar-success' : (pct > 0 ? 'progress-bar-warning' : 'progress-bar-danger');
        $('#kroscekProgress').html(
            '<div class="progress" style="margin-bottom:4px; height:20px;">' +
            '<div class="progress-bar ' + pbClass + '" style="width:' + pct + '%; min-width:30px; line-height:20px;">' +
            pct + '%</div></div>'
        );

        // Badges ringkasan
        $('#kroscekBadges').html(
            '<span class="label label-success" style="font-size:0.95em; margin-right:4px;">' +
            '<span class="glyphicon glyphicon-ok"></span> Cocok: ' + matched + '</span>' +
            '<span class="label label-warning" style="font-size:0.95em; margin-right:4px;">' +
            '<span class="glyphicon glyphicon-time"></span> Belum scan: ' + missing + '</span>' +
            '<span class="label label-danger" style="font-size:0.95em;">' +
            '<span class="glyphicon glyphicon-remove"></span> Tidak ada di JSON: ' + extra + '</span>' +
            '<span style="margin-left:10px; font-size:0.9em; color:#555;">(' + matched + ' / ' + total + ' terkroscek)</span>'
        );

        // Tampilkan warning jika ada yang belum scan atau ada ekstra
        if (missing > 0 || extra > 0) {
            $('#kroscekWarning').show();
        } else {
            $('#kroscekWarning').hide();
            $('#cbkLanjutMeski').prop('checked', false);
        }
    }

    // ========== Simpan / Muat Progress Kroscek ==========
    // var _kroscekNoTrans = ''; // diisi saat masuk step 3

    // // Saat masuk step 3, cek apakah ada progress tersimpan
    // $('#btnLanjutKroscek').on('click', function() {
    //     // ... kode lanjut step yang sudah ada tetap dijalankan ...

    //     // Ambil noTrans dari validasi
    //     _kroscekNoTrans = ($('#importModalTitle').text().replace('Pratinjau — ', '') || 'unknown').trim();

    //     // Cek apakah ada progress tersimpan untuk transaksi ini
    //     var saved = null;
    //     try {
    //         saved = JSON.parse(localStorage.getItem('kroscek_' + _kroscekNoTrans));
    //     } catch (e) {}
    //     if (saved && saved.scanned && Object.keys(saved.scanned).length > 0) {
    //         var jumlahSaved = Object.keys(saved.scanned).length;
    //         $('#btnMuatProgress').show().attr('title',
    //             'Ada ' + jumlahSaved + ' kantong tersimpan dari sesi sebelumnya. Klik untuk melanjutkan.');
    //     } else {
    //         $('#btnMuatProgress').hide();
    //     }
    // });

    // // Tombol Simpan Progress
    // $('#btnSimpanProgress').on('click', function() {
    //     if (!_kroscekNoTrans) return;
    //     var jumlah = Object.keys(scannedKantong).length;
    //     if (jumlah === 0) {
    //         Swal.fire('Info', 'Belum ada kantong yang discan.', 'info');
    //         return;
    //     }
    //     var progress = {
    //         noTrans: _kroscekNoTrans,
    //         savedAt: new Date().toLocaleString('id-ID'),
    //         scanned: scannedKantong,
    //         extra: extraScanned
    //     };
    //     try {
    //         localStorage.setItem('kroscek_' + _kroscekNoTrans, JSON.stringify(progress));
    //         Swal.fire({
    //             title: 'Progress Disimpan',
    //             html: '<b>' + jumlah + ' kantong</b> sudah terscan tersimpan di browser ini.<br>' +
    //                 '<small style="color:#888;">Buka kembali file JSON yang sama dan klik <b>"Lanjutkan Progress"</b> untuk melanjutkan.</small>',
    //             icon: 'success',
    //             timer: 3000,
    //             showConfirmButton: false
    //         });
    //     } catch (e) {
    //         Swal.fire('Gagal', 'Tidak dapat menyimpan ke localStorage: ' + e.message, 'error');
    //     }
    // });

    // // Tombol Muat Progress
    // $('#btnMuatProgress').on('click', function() {
    //     var saved = null;
    //     try {
    //         saved = JSON.parse(localStorage.getItem('kroscek_' + _kroscekNoTrans));
    //     } catch (e) {}
    //     if (!saved) {
    //         Swal.fire('Info', 'Tidak ada progress tersimpan untuk transaksi ini.', 'info');
    //         return;
    //     }
    //     var jumlah = Object.keys(saved.scanned || {}).length;
    //     Swal.fire({
    //         title: 'Muat Progress?',
    //         html: 'Ada <b>' + jumlah + ' kantong</b> tersimpan dari sesi <b>' + saved.savedAt + '</b>.<br>' +
    //             'Progress saat ini akan <b>digabungkan</b> dengan yang tersimpan.',
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonText: 'Ya, Muat',
    //         cancelButtonText: 'Batal'
    //     }).then(function(result) {
    //         if (result.isConfirmed) {
    //             // Gabungkan (merge), bukan replace — agar scan baru tidak hilang
    //             $.extend(scannedKantong, saved.scanned || {});
    //             $.each(saved.extra || [], function(i, nk) {
    //                 if ($.inArray(nk, extraScanned) === -1) extraScanned.push(nk);
    //             });
    //             renderKroscekTable();
    //             $('#btnMuatProgress').hide();
    //             Swal.fire({
    //                 title: 'Progress Dimuat',
    //                 text: jumlah + ' kantong berhasil dimuat.',
    //                 icon: 'success',
    //                 timer: 2000,
    //                 showConfirmButton: false
    //             });
    //         }
    //     });
    // });

    function processKroscekScan(val) {
        val = $.trim(val);
        if (!val) return;

        // Cek duplikat
        if (scannedKantong.hasOwnProperty(val)) {
            // Flash kuning tanda sudah scan
            $('#inputScanKantong').css('background', '#fffacd');
            setTimeout(function() {
                $('#inputScanKantong').css('background', '');
            }, 400);
            $('#inputScanKantong').val('').focus();
            return;
        }

        // Cek apakah ada di JSON
        var foundInJson = false;
        $.each(kantongListFromJson, function(i, item) {
            if (item.nokantong === val) {
                foundInJson = true;
                return false;
            }
        });

        if (foundInJson) {
            scannedKantong[val] = true;
            $('#inputScanKantong').css('background', '#dff0d8');
        } else {
            // Tidak ada di JSON → ekstra
            if ($.inArray(val, extraScanned) === -1) extraScanned.push(val);
            $('#inputScanKantong').css('background', '#f2dede');
        }

        setTimeout(function() {
            $('#inputScanKantong').css('background', '');
        }, 500);
        $('#inputScanKantong').val('').focus();
        renderKroscekTable();
    }

    // Enter key scan
    $('#inputScanKantong').on('keydown', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            processKroscekScan($(this).val());
        }
    });

    // Tombol Tambah
    $('#btnAddScan').on('click', function() {
        processKroscekScan($('#inputScanKantong').val());
    });

    // Tombol Reset scan
    $('#btnResetScan').on('click', function() {
        scannedKantong = {};
        extraScanned = [];
        renderKroscekTable();
        $('#inputScanKantong').val('').focus();
    });

    // Tombol Lanjut ke Kroscek Fisik (Step 2 → Step 3)
    $('#btnLanjutKroscek').on('click', function() {
        if (kantongListFromJson.length === 0) {
            Swal.fire('Peringatan', 'Data validasi belum tersedia. Silakan validasi file JSON terlebih dahulu.', 'warning');
            return;
        }
        $('#stepValidasi').hide();
        $('#footerStep2').hide();
        $('#stepKroscek').show();
        $('#footerStep3').show();
        $('#importModalTitle').text('Kroscek Fisik Kantong');
        renderKroscekTable();
        // Autofocus input scan
        setTimeout(function() {
            $('#inputScanKantong').focus();
        }, 300);
    });

    // ========== Import JSON: 2-Step Flow ==========

    // Reset modal ke Step 1 setiap kali dibuka
    $('#mImportJson').on('show.bs.modal', function() {
        resetImportModal();
    });

    function resetImportModal() {
        $('#stepPilihFile').show();
        $('#stepValidasi').hide();
        $('#stepKroscek').hide();
        $('#footerStep1').show();
        $('#footerStep2').hide();
        $('#footerStep3').hide();
        $('#importModalTitle').text('Import Data Serah Terima dari File JSON');
        $('#validasiContent').html('');
        $('#fileJson').val('');
        $('#aksiDuplikat').val('skip');
        $('#aksiError').val('commit_success');
        // Reset kroscek
        kantongListFromJson = [];
        scannedKantong = {};
        extraScanned = [];
        $('#inputScanKantong').val('');
        $('#kroscekProgress').html('');
        $('#kroscekBadges').html('');
        $('#tbodyKroscek').html('');
        $('#kroscekWarning').hide();
        $('#cbkLanjutMeski').prop('checked', false);
    }

    // Tombol Kembali (Step 2 → Step 1)
    $('#btnKembaliStep1').on('click', function() {
        resetImportModal();
    });

    // Tombol Kembali (Step 3 → Step 2)
    $('#btnKembaliStep2').on('click', function() {
        $('#stepKroscek').hide();
        $('#footerStep3').hide();
        $('#stepValidasi').show();
        $('#footerStep2').show();
        $('#importModalTitle').text('Pratinjau — ' + (kantongListFromJson.length ? '' : ''));
    });

    // STEP 1 → Validasi
    $('#btnValidasiFile').on('click', function() {
        var fileInput = $('#fileJson')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire("Peringatan", "Silakan pilih file JSON terlebih dahulu.", "warning");
            return;
        }

        var formData = new FormData();
        formData.append('fileJson', fileInput.files[0]);

        Swal.fire({
            title: "Memvalidasi File...",
            text: "Sedang memeriksa isi file JSON. Mohon tunggu...",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: function() {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'serahterima/sr_aftap_validate_json.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.status === 'success') {
                    var p = response.preview;
                    var aksi = $('#aksiDuplikat').val();

                    // Simpan daftar kantong dari JSON untuk kroscek fisik
                    kantongListFromJson = [];
                    scannedKantong = {};
                    extraScanned = [];
                    $.each(p.detail_list, function(i, d) {
                        kantongListFromJson.push({
                            nokantong: d.nokantong,
                            kodedonor: d.kodedonor,
                            no_aftap: d.no_aftap
                        });
                    });

                    var badgeHeader = p.header_exists ?
                        '<span class="label label-warning">Sudah Ada di Sistem</span>' :
                        '<span class="label label-success">Data Baru</span>';

                    var aksiLabel = {
                        'skip': '<span class="label label-default">Lewati</span>',
                        'update': '<span class="label label-info">Perbarui</span>',
                        'replace': '<span class="label label-danger">Ganti Total</span>'
                    };

                    var rowsDetail = '';
                    $.each(p.detail_list, function(i, d) {
                        var statusBadge = d.detail_exists ? '<span class="label label-warning">Ada</span>' : '<span class="label label-success">Baru</span>';
                        var kantongBadge = d.kantong_exists ? '<span class="label label-warning">Ada</span>' : '<span class="label label-success">Baru</span>';
                        var pdBadge = d.pendonor_exists ? '<span class="label label-warning">Ada</span>' : '<span class="label label-success">Baru</span>';
                        rowsDetail += '<tr>' +
                            '<td class="text-center">' + (i + 1) + '</td>' +
                            '<td>' + d.nokantong + '</td>' +
                            '<td>' + d.kodedonor + '</td>' +
                            '<td>' + d.no_aftap + '</td>' +
                            '<td class="text-center">' + statusBadge + '</td>' +
                            '<td class="text-center">' + kantongBadge + '</td>' +
                            '<td class="text-center">' + pdBadge + '</td>' +
                            '</tr>';
                    });

                    var html =
                        '<table class="table table-condensed table-bordered" style="margin-bottom:6px;">' +
                        '<tr><th style="width:40%">No. Transaksi</th><td><strong>' + p.notransaksi + '</strong> &nbsp;' + badgeHeader + '</td></tr>' +
                        '<tr><th>Tanggal</th><td>' + p.tanggal + '</td></tr>' +
                        '<tr><th>Asal</th><td>' + p.asal + '</td></tr>' +
                        '<tr><th>Suhu Terima</th><td>' + p.suhu + ' °C</td></tr>' +
                        '<tr><th>Petugas</th><td>' + p.petugas + '</td></tr>' +
                        '<tr><th>Tindakan Duplikat</th><td>' + (aksiLabel[aksi] || aksi) + '</td></tr>' +
                        '</table>' +
                        '<table class="table table-condensed table-bordered table-striped" style="margin-bottom:8px;">' +
                        '<tr>' +
                        '<th class="text-center" style="background:#5cb85c;color:#fff;">Total</th>' +
                        '<th class="text-center" style="background:#5cb85c;color:#fff;">Baru</th>' +
                        '<th class="text-center" style="background:#f0ad4e;color:#fff;">Sudah Ada</th>' +
                        '<th class="text-center" style="background:#5bc0de;color:#fff;">Kantong</th>' +
                        '<th class="text-center" style="background:#5bc0de;color:#fff;">Pendonor</th>' +
                        '<th class="text-center" style="background:#5bc0de;color:#fff;">H.Trans</th>' +
                        '</tr>' +
                        '<tr class="text-center">' +
                        '<td><strong>' + p.jumlah_detail + '</strong></td>' +
                        '<td><strong class="text-success">' + p.detail_baru + '</strong></td>' +
                        '<td><strong class="text-warning">' + p.detail_sudah_ada + '</strong></td>' +
                        '<td>' + p.jumlah_kantong + '</td>' +
                        '<td>' + p.jumlah_pendonor + '</td>' +
                        '<td>' + p.jumlah_htransaksi + '</td>' +
                        '</tr>' +
                        '</table>' +
                        '<p style="font-size:0.9em; font-weight:bold; margin-bottom:4px;">Rincian per Kantong:</p>' +
                        '<div style="max-height:200px; overflow-y:auto;">' +
                        '<table class="table table-condensed table-bordered table-striped" style="font-size:0.85em;">' +
                        '<thead style="background:#e8e8e8;"><tr>' +
                        '<th class="text-center">#</th><th>No. Kantong</th><th>Kode Donor</th>' +
                        '<th>No. Aftap</th><th class="text-center">Detail</th>' +
                        '<th class="text-center">Kantong</th><th class="text-center">Pendonor</th>' +
                        '</tr></thead><tbody>' + rowsDetail + '</tbody></table></div>';

                    $('#validasiContent').html(html);
                    $('#stepPilihFile').hide();
                    $('#stepValidasi').show();
                    $('#footerStep1').hide();
                    $('#footerStep2').show();
                    $('#importModalTitle').text('Pratinjau — ' + p.notransaksi);
                } else {
                    Swal.fire("Validasi Gagal", response.message || "Terjadi kesalahan saat memvalidasi file", "error");
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire("Error", "Gagal terhubung ke server: " + error, "error");
            }
        });
    });

    // STEP 3 → Jalankan Import
    $('#btnKonfirmasiImport').on('click', function() {
        // Cek apakah semua kantong sudah dikroscek
        var totalJson = kantongListFromJson.length;
        var totalScanned = Object.keys(scannedKantong).length;
        var extraCount = extraScanned.length;
        var allOk = (totalScanned === totalJson && extraCount === 0);

        if (!allOk && !$('#cbkLanjutMeski').is(':checked')) {
            var missingCount = totalJson - totalScanned;
            var msg = '';
            if (missingCount > 0) msg += missingCount + ' kantong belum discan. ';
            if (extraCount > 0) msg += extraCount + ' kantong scan tidak ada di JSON. ';
            Swal.fire('Kroscek Belum Lengkap', msg +
                'Centang opsi "Tetap lanjutkan" jika ingin melanjutkan import.', 'warning');
            return;
        }

        var fileInput = $('#fileJson')[0];
        var formData = new FormData();
        formData.append('fileJson', fileInput.files[0]);
        formData.append('aksiDuplikat', $('#aksiDuplikat').val());
        formData.append('aksiError', $('#aksiError').val());
        // Kirim ringkasan kroscek ke server (opsional — untuk dicatat)
        formData.append('kroscekTotal', totalJson);
        formData.append('kroscekCocok', totalScanned);
        formData.append('kroscekExtra', extraCount);
        formData.append('kroscekMissing', totalJson - totalScanned);

        Swal.fire({
            title: "Mengimpor Data...",
            text: "Sedang menyimpan data ke sistem. Mohon tunggu...",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: function() {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'serahterima/sr_aftap_import_json.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.close();

                var d = response.detail || {};

                if (response.status === 'success') {
                    // Semua berhasil
                    Swal.fire({
                        title: "Import Berhasil!",
                        html: response.message +
                            '<br><br>' + buildStatTable(d),
                        icon: "success"
                    }).then(function() {
                        location.reload();
                    });

                } else if (response.status === 'partial') {
                    // Sebagian gagal → tampilkan tabel hasil + tombol download retry
                    var detailRows = buildResultRows(response.results || []);
                    var retryBtn = '';
                    if (response.failed_json) {
                        // Encode JSON untuk download client-side
                        var failedStr = JSON.stringify(response.failed_json, null, 2);
                        var blob = new Blob([failedStr], {
                            type: 'application/json'
                        });
                        var blobUrl = URL.createObjectURL(blob);
                        var noTrans = response.failed_json.notransaksi || 'gagal';
                        var fname = 'retry_gagal_' + noTrans + '.json';
                        retryBtn = '<a href="' + blobUrl + '" download="' + fname + '" ' +
                            'class="w3-btn w3-orange w3-hover-dark-orange" style="margin-top:10px; display:inline-block;">' +
                            '<span class="glyphicon glyphicon-download-alt"></span> ' +
                            'Unduh JSON Gagal (' + d.failed + ' kantong) untuk Coba Ulang</a>';
                    }
                    Swal.fire({
                        title: "Import Selesai Sebagian",
                        html: '<div style="text-align:left;">' +
                            '<p>' + response.message + '</p>' +
                            buildStatTable(d) +
                            '<br>' +
                            '<div style="max-height:220px; overflow-y:auto; font-size:0.9em;">' +
                            '<table class="table table-condensed table-bordered table-striped">' +
                            '<thead style="background:#e8e8e8;"><tr>' +
                            '<th>No. Kantong</th><th class="text-center">Status</th><th>Keterangan</th>' +
                            '</tr></thead><tbody>' + detailRows + '</tbody></table>' +
                            '</div>' +
                            retryBtn +
                            '</div>',
                        icon: "warning",
                        width: 700,
                        confirmButtonText: "OK, Tutup"
                    }).then(function() {
                        location.reload();
                    });

                } else {
                    // Error total
                    Swal.fire("Import Gagal", response.message || "Terjadi kesalahan", "error");
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire("Error", "Gagal terhubung ke server: " + error, "error");
            }
        });
    });

    function buildStatTable(d) {
        return '<table style="margin:8px auto; border-collapse:collapse; font-size:1em; min-width:260px;">' +
            '<tr><td style="padding:3px 14px; text-align:right; color:#555;">Total diproses</td><td style="padding:3px 10px; font-weight:bold;">' + (d.total || 0) + ' kantong</td></tr>' +
            '<tr><td style="padding:3px 14px; text-align:right; color:#555;">Ditambahkan</td><td style="padding:3px 10px; color:green; font-weight:bold;">' + (d.inserted || 0) + '</td></tr>' +
            '<tr><td style="padding:3px 14px; text-align:right; color:#555;">Diperbarui</td><td style="padding:3px 10px; color:#1a6eb5; font-weight:bold;">' + (d.updated || 0) + '</td></tr>' +
            '<tr><td style="padding:3px 14px; text-align:right; color:#555;">Dilewati</td><td style="padding:3px 10px; color:gray; font-weight:bold;">' + (d.skipped || 0) + '</td></tr>' +
            '<tr><td style="padding:3px 14px; text-align:right; color:#555;">Gagal</td><td style="padding:3px 10px; color:red; font-weight:bold;">' + (d.failed || 0) + '</td></tr>' +
            '</table>';
    }

    function buildResultRows(results) {
        var rows = '';
        var colorMap = {
            'ditambahkan': 'success',
            'diperbarui': 'info',
            'dilewati': 'default',
            'gagal': 'danger'
        };
        $.each(results, function(i, r) {
            var cls = colorMap[r.status] || 'default';
            rows += '<tr>' +
                '<td>' + r.nokantong + '</td>' +
                '<td class="text-center"><span class="label label-' + cls + '">' + r.status + '</span></td>' +
                '<td style="color:' + (r.status === 'gagal' ? 'red' : 'inherit') + '">' + r.pesan + '</td>' +
                '</tr>';
        });
        return rows;
    }
</script>
<?php

?>