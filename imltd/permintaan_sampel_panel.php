<?php
ob_start();
session_start();
require_once('config/db_connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$namauser = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';

$pkField = 'id';
$table   = 'sampel_panel_trans';

/* =========================
   HELPERS
========================= */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function generateNoTrans()
{
    return 'SP-' . date('YmdHis') . '-' . rand(100, 999);
}

/* =========================
   DEFAULT FILTER
========================= */
$today = date('Y-m-d');
$today1 = $today;
$src_instansi = ''; // disimpan agar pola referensi tetap mirip
$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : (isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil');

if (isset($_POST['tgl_awal']) && $_POST['tgl_awal'] != '') {
    $today = $_POST['tgl_awal'];
}
if (isset($_POST['tgl_akhir']) && $_POST['tgl_akhir'] != '') {
    $today1 = $_POST['tgl_akhir'];
}
if (isset($_GET['tgl_awal']) && $_GET['tgl_awal'] != '') {
    $today = $_GET['tgl_awal'];
}
if (isset($_GET['tgl_akhir']) && $_GET['tgl_akhir'] != '') {
    $today1 = $_GET['tgl_akhir'];
}
if (isset($_POST['instansi']) && $_POST['instansi'] != '') {
    $src_instansi = $_POST['instansi'];
}
if (isset($_GET['instansi']) && $_GET['instansi'] != '') {
    $src_instansi = $_GET['instansi'];
}

if ($today > $today1) {
    $tmp = $today;
    $today = $today1;
    $today1 = $tmp;
}

$swal_icon = '';
$swal_title = '';
$swal_text  = '';

/* =========================
   CRUD ACTION
========================= */
if (isset($_POST['form_action']) && $_POST['form_action'] == 'simpan') {
    $id         = isset($_POST['id']) ? mysql_real_escape_string($_POST['id']) : '';
    $notrans    = isset($_POST['notrans']) && $_POST['notrans'] != '' ? $_POST['notrans'] : generateNoTrans();
    $reaktif    = isset($_POST['reaktif']) ? intval($_POST['reaktif']) : 0;
    $nonreaktif = isset($_POST['nonreaktif']) ? intval($_POST['nonreaktif']) : 0;
    $tgl        = isset($_POST['tgl']) ? mysql_real_escape_string($_POST['tgl']) : date('Y-m-d');
    $tgl_jam    = $tgl . ' ' . date('H:i:s');
    $petugas    = isset($_POST['petugas']) ? mysql_real_escape_string($_POST['petugas']) : mysql_real_escape_string($namauser);

    $notrans_sql = mysql_real_escape_string($notrans);

    /* cek notrans duplikat */
    if ($id == '') {
        $cekNotrans = mysql_query("SELECT id FROM {$table} WHERE notrans = '{$notrans_sql}' LIMIT 1");
    } else {
        $cekNotrans = mysql_query("SELECT id FROM {$table} WHERE notrans = '{$notrans_sql}' AND id <> '{$id}' LIMIT 1");
    }

    if ($cekNotrans && mysql_num_rows($cekNotrans) > 0) {
        $swal_icon  = 'error';
        $swal_title = 'Gagal';
        $swal_text  = 'No transaksi sudah dipakai. Silakan generate ulang.';
    } else {
        if ($id == '') {
            $sql = "INSERT INTO {$table}
                    (notrans, reaktif, nonreaktif, tgl_permintaan, petugas, status, created)
                    VALUES
                    ('{$notrans_sql}', '{$reaktif}', '{$nonreaktif}', '{$tgl_jam}', '{$petugas}', '0', NOW())";
            $query = mysql_query($sql);

            if ($query) {
                $swal_icon  = 'success';
                $swal_title = 'Berhasil';
                $swal_text  = 'Data berhasil ditambahkan.';

                //=======Audit Trial====================================================================================
                $log_mdl  = 'IMLTD';
                $log_aksi = 'Melakukan penyimpanan Permintaan Sampel Panel dengan No Transaksi: ' . $notrans_sql;
                include("user_log.php");
                //=====================================================================================================
            } else {
                $swal_icon  = 'error';
                $swal_title = 'Gagal';
                $swal_text  = mysql_error();
            }
        } else {
            $id_sql = mysql_real_escape_string($id);

            $sql = "UPDATE {$table}
                    SET notrans = '{$notrans_sql}',
                        reaktif = '{$reaktif}',
                        nonreaktif = '{$nonreaktif}',
                        tgl_permintaan = '{$tgl_jam}',
                        petugas = '{$petugas}'
                    WHERE {$pkField} = '{$id_sql}'";
            $query = mysql_query($sql);

            if ($query) {
                $swal_icon  = 'success';
                $swal_title = 'Berhasil';
                $swal_text  = 'Data berhasil diupdate.';

                //=======Audit Trial====================================================================================
                $log_mdl  = 'IMLTD';
                $log_aksi = 'Melakukan update Permintaan Sampel Panel dengan No Transaksi: ' . $notrans_sql;
                include("user_log.php");
                //=====================================================================================================
            } else {
                $swal_icon  = 'error';
                $swal_title = 'Gagal';
                $swal_text  = mysql_error();
            }
        }
    }
}

if (isset($_POST['form_action']) && $_POST['form_action'] == 'hapus') {
    $id = isset($_POST['hapus_id']) ? mysql_real_escape_string($_POST['hapus_id']) : '';
    $notrans = isset($_POST['hapus_notrans']) ? mysql_real_escape_string($_POST['hapus_notrans']) : '';

    if ($id != '') {
        $sql = "DELETE FROM {$table} WHERE {$pkField} = '{$id}'";
        $query = mysql_query($sql);

        if ($query) {
            //=======Audit Trial====================================================================================
            $log_mdl  = 'IMLTD';
            $log_aksi = 'Melakukan penghapusan Permintaan Sampel Panel dengan ID: ' . $id . ' dan No Transaksi: ' . $notrans;
            include("user_log.php");
            //=====================================================================================================

            $swal_icon  = 'success';
            $swal_title = 'Berhasil';
            $swal_text  = 'Data berhasil dihapus.';
        } else {
            $swal_icon  = 'error';
            $swal_title = 'Gagal';
            $swal_text  = mysql_error();
        }
    }
}

/* =========================
   QUERY DATA
========================= */
$awal_sql  = mysql_real_escape_string($today);
$akhir_sql = mysql_real_escape_string($today1);

$query_sampel_panel = "
    SELECT *
    FROM {$table}
    WHERE CAST(tgl_permintaan AS date) >= '{$awal_sql}'
      AND CAST(tgl_permintaan AS date) <= '{$akhir_sql}'
    ORDER BY tgl_permintaan DESC
";

$result_sampel_panel = mysql_query($query_sampel_panel);
if (!$result_sampel_panel) {
    die(mysql_error() . '<br><br>' . $query_sampel_panel);
}

/* =========================
   EXPORT EXCEL
========================= */
if ($aksi == 'excel') {
    ob_end_clean();

    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=permintaan_sampel_panel_" . date('Ymd_His') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<html>";
    echo "<head><meta charset='utf-8'></head>";
    echo "<body>";
    echo "<table border='1'>";
    echo "<tr>
            <th>No</th>
            <th>No Transaksi</th>
            <th>Reaktif</th>
            <th>NonReaktif</th>
            <th>Tgl Permintaan</th>
            <th>Petugas</th>
            <th>Status</th>
          </tr>";

    $no = 1;
    mysql_data_seek($result_sampel_panel, 0);
    while ($row = mysql_fetch_assoc($result_sampel_panel)) {
        $tglTampil = (!empty($row['tgl_permintaan'])) ? date('d-m-Y H:i', strtotime($row['tgl_permintaan'])) : '-';

        if ((int)$row['status'] == 2) {
            $statusTampil = 'Selesai';
        } else if ((int)$row['status'] == 1) {
            $statusTampil = 'Telah dipenuhi';
        } else {
            $statusTampil = 'Belum dipenuhi';
        }

        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['notrans']) . "</td>";
        echo "<td>" . (int)$row['reaktif'] . "</td>";
        echo "<td>" . (int)$row['nonreaktif'] . "</td>";
        echo "<td>" . $tglTampil . "</td>";
        echo "<td>" . htmlspecialchars($row['petugas']) . "</td>";
        echo "<td>" . $statusTampil . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "</body>";
    echo "</html>";
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Permintaan Sampel Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    body {
        background: #f4f7fb;
    }

    .card {
        margin: 20px;
    }

    #myTable {
        width: 100% !important;
    }

    #myTable th,
    #myTable td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .datepicker {
        cursor: pointer;
    }
    </style>
</head>

<body>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Permintaan Sampel Panel</h4>
            <button type="button" class="btn btn-success" onclick="openTambah()" data-bs-toggle="modal"
                data-bs-target="#modalSampel">
                Tambah Data
            </button>
        </div>

        <div class="card-body">
            <form class="row g-3" method="POST" action="">
                <div class="col-md-4">
                    <label class="form-label">Tanggal</label>
                    <div class="input-group">
                        <input type="date" class="form-control datepicker" name="tgl_awal"
                            value="<?php echo htmlspecialchars($today); ?>" max="<?php echo date('Y-m-d'); ?>">
                        <span class="input-group-text">s/d</span>
                        <input type="date" class="form-control datepicker" name="tgl_akhir"
                            value="<?php echo htmlspecialchars($today1); ?>" max="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" type="submit" name="aksi" value="tampil">Tampilkan</button>
                    <button class="btn btn-success" type="submit" name="aksi" value="excel">Export Excel</button>
                </div>
            </form>

            <hr>

            <table id="myTable" class="table table-striped align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Reaktif</th>
                        <th>NonReaktif</th>
                        <th>Tgl Permintaan</th>
                        <th>Petugas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysql_fetch_assoc($result_sampel_panel)) {
                        $rowId = isset($row[$pkField]) ? $row[$pkField] : '';
                        $tglEdit = '';
                        if (isset($row['tgl_permintaan']) && $row['tgl_permintaan'] != '') {
                            $tglEdit = substr($row['tgl_permintaan'], 0, 10);
                        }

                        $tglTampil = (!empty($row['tgl_permintaan'])) ? date('d-m-Y H:i', strtotime($row['tgl_permintaan'])) : '-';

                        if ((int)$row['status'] == 2) {
                            $statusBadge = '<span class="badge bg-success">Selesai</span>';
                        } else if ((int)$row['status'] == 1) {
                            $statusBadge = '<span class="badge bg-warning text-dark">Telah dipenuhi</span>';
                        } else {
                            $statusBadge = '<span class="badge bg-secondary">Belum dipenuhi</span>';
                        }
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['notrans']); ?></td>
                        <td><?php echo (int)$row['reaktif']; ?></td>
                        <td><?php echo (int)$row['nonreaktif']; ?></td>
                        <td><?php echo $tglTampil; ?></td>
                        <td><?php echo htmlspecialchars($row['petugas']); ?></td>
                        <td><?php echo $statusBadge; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm me-1"
                                onclick='openEdit(<?php echo json_encode($rowId); ?>, <?php echo json_encode($row["notrans"]); ?>, <?php echo json_encode((int)$row["reaktif"]); ?>, <?php echo json_encode((int)$row["nonreaktif"]); ?>, <?php echo json_encode($tglEdit); ?>, <?php echo json_encode($row["petugas"]); ?>, <?php echo json_encode((int)$row["status"]); ?>)'
                                data-bs-toggle="modal" data-bs-target="#modalSampel">
                                Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm"
                                onclick='confirmDelete(<?php echo json_encode($rowId); ?>, <?php echo json_encode($row["notrans"]); ?>)'>
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL ADD/EDIT -->
    <div class="modal fade" id="modalSampel" tabindex="-1" aria-labelledby="modalSampelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSampelLabel">Tambah Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="form_action" value="simpan">
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="petugas" id="petugas"
                            value="<?php echo htmlspecialchars($namauser); ?>">
                        <input type="hidden" name="status" id="status" value="0">

                        <!-- simpan filter aktif -->
                        <input type="hidden" name="back_tgl_awal" value="<?php echo htmlspecialchars($today); ?>">
                        <input type="hidden" name="back_tgl_akhir" value="<?php echo htmlspecialchars($today1); ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">No Transaksi</label>
                                <input type="text" class="form-control" name="notrans" id="notrans" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tgl" id="tgl" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kantong Reaktif</label>
                                <input type="number" class="form-control" name="reaktif" id="reaktif" min="0" value="0"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kantong NonReaktif</label>
                                <input type="number" class="form-control" name="nonreaktif" id="nonreaktif" min="0"
                                    value="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form method="POST" action="" id="formHapus" style="display:none;">
        <input type="hidden" name="form_action" value="hapus">
        <input type="hidden" name="hapus_id" id="hapus_id" value="">
        <input type="hidden" name="hapus_notrans" id="hapus_notrans" value="">
        <input type="hidden" name="back_tgl_awal" value="<?php echo htmlspecialchars($today); ?>">
        <input type="hidden" name="back_tgl_akhir" value="<?php echo htmlspecialchars($today1); ?>">
    </form>

    <script>
    function generateNoTrans() {
        var d = new Date();
        var pad = function(n) {
            return (n < 10 ? '0' : '') + n;
        };
        return 'SP-' +
            d.getFullYear() +
            pad(d.getMonth() + 1) +
            pad(d.getDate()) +
            pad(d.getHours()) +
            pad(d.getMinutes()) +
            pad(d.getSeconds()) +
            '-' +
            Math.floor(Math.random() * 900 + 100);
    }

    $(document).ready(function() {
        $('#myTable').DataTable({
            scrollX: true,
            autoWidth: false,
            responsive: false,
            language: {
                emptyTable: "Data belum ada"
            }
        });
    });

    function openTambah() {
        document.getElementById('modalSampelLabel').innerHTML = 'Tambah Data';
        document.getElementById('id').value = '';
        document.getElementById('notrans').value = generateNoTrans();
        document.getElementById('tgl').value = '<?php echo date('Y-m-d'); ?>';
        document.getElementById('reaktif').value = '0';
        document.getElementById('nonreaktif').value = '0';
        document.getElementById('petugas').value = '<?php echo addslashes($namauser); ?>';
        document.getElementById('status').value = '0';
    }

    function openEdit(id, notrans, reaktif, nonreaktif, tgl, petugas, status) {
        document.getElementById('modalSampelLabel').innerHTML = 'Edit Data';
        document.getElementById('id').value = id;
        document.getElementById('notrans').value = notrans;
        document.getElementById('tgl').value = tgl;
        document.getElementById('reaktif').value = reaktif;
        document.getElementById('nonreaktif').value = nonreaktif;
        document.getElementById('petugas').value = petugas;
        document.getElementById('status').value = status;
    }

    function confirmDelete(id, notrans) {
        Swal.fire({
            title: 'Yakin hapus data?',
            text: 'Data yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('hapus_id').value = id;
                document.getElementById('hapus_notrans').value = notrans;
                document.getElementById('formHapus').submit();
            }
        });
    }
    </script>

    <script>
    document.querySelectorAll('.datepicker').forEach(function(el) {
        el.addEventListener('click', function() {
            if (this.showPicker) {
                this.showPicker();
            }
        });

        el.addEventListener('focus', function() {
            if (this.showPicker) {
                this.showPicker();
            }
        });
    });
    </script>

    <?php if ($swal_title != '') { ?>
    <script>
    Swal.fire({
        icon: '<?php echo $swal_icon; ?>',
        title: '<?php echo addslashes($swal_title); ?>',
        text: '<?php echo addslashes($swal_text); ?>'
    });
    </script>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>