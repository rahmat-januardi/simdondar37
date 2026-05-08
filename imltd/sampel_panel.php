<?php
require_once('config/db_connect.php');
session_start();

$namauser = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';

$pkField = 'id';

/* =========================
   HELPERS
========================= */
function generateNoTrans()
{
    return 'SP-' . date('YmdHis') . '-' . rand(100, 999);
}

/* =========================
   DEFAULT FILTER
========================= */
$today = date('Y-m-d');
$today1 = $today;
$src_instansi = '';
$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : 'tampil';

if (isset($_POST['tgl_awal']) && $_POST['tgl_awal'] != '') {
    $today = $_POST['tgl_awal'];
}

if (isset($_POST['tgl_akhir']) && $_POST['tgl_akhir'] != '') {
    $today1 = $_POST['tgl_akhir'];
}

if (isset($_POST['instansi']) && $_POST['instansi'] != '') {
    $src_instansi = $_POST['instansi'];
}

/* =========================
   CRUD ACTION
========================= */
$swal_icon = '';
$swal_title = '';
$swal_text = '';

if (isset($_POST['form_action']) && $_POST['form_action'] == 'simpan') {
    $id       = isset($_POST['id']) ? $_POST['id'] : '';
    $notrans  = isset($_POST['notrans']) && $_POST['notrans'] != '' ? $_POST['notrans'] : generateNoTrans();
    $nokantong = isset($_POST['nokantong']) ? mysql_real_escape_string($_POST['nokantong']) : '';
    $tgl      = isset($_POST['tgl']) ? mysql_real_escape_string($_POST['tgl']) : date('Y-m-d');
    $tgl_jam  = $tgl . ' ' . date('H:i:s');
    $instansi = isset($_POST['instansi_data']) ? mysql_real_escape_string($_POST['instansi_data']) : '';
    $petugas  = isset($_POST['petugas']) ? mysql_real_escape_string($_POST['petugas']) : '';

    $notrans_sql = mysql_real_escape_string($notrans);

    $cekKantong = mysql_query("SELECT * FROM stokkantong WHERE noKantong='$nokantong' LIMIT 1");
    $q_ktg = mysql_fetch_assoc($cekKantong);

    // PENGECEKAN KANTONG UTAMA PADA TABLE HISTORI TRANSAKSI
    $potong_ktg = substr($nokantong, 0, -1);
    $ktg_a = $potong_ktg . 'A';
    $cekHistory = mysql_query("SELECT * FROM htransaksi WHERE NoKantong=UPPER('$ktg_a') LIMIT 1");
    $q_cekHistory = mysql_fetch_assoc($cekHistory);

    // PENGECEKAN INSTANSI PADA TABLE HISTORI TRANSAKSI
    $cek_instansi = substr($q_cekHistory['NoTrans'], 0, 1) == 'M' ? $q_cekHistory['Instansi'] : 'DALAM GEDUNG';

    if ($id == '') {
        if ($q_ktg['Status'] == '7' || $q_ktg['Status'] == '6') {
            $sql = "INSERT INTO sampel_panel (notrans, nokantong, tgl, instansi, petugas)
                VALUES ('$notrans_sql', '$nokantong', '$tgl_jam', '$cek_instansi', '$petugas')";
            $query = mysql_query($sql);

            if ($query) {
                $swal_icon = 'success';
                $swal_title = 'Berhasil';
                $swal_text = 'Data berhasil ditambahkan.';

                //=======Audit Trial====================================================================================
                $log_mdl = 'IMLTD';
                $log_aksi = 'Melakukan penyimpanan Sampel Panel pada nomor kantong/sampel: ' . $nokantong;
                include("user_log.php");
                //=====================================================================================================	

                /* =========================================
                Ubah Status Sampel Panel pada stokkantong
             ========================================== */
                $updateStatus = mysql_query("UPDATE stokkantong SET Status='9' WHERE noKantong='$nokantong'");
            } else {
                $swal_icon = 'error';
                $swal_title = 'Gagal';
                $swal_text = mysql_error();
            }
        } else {
            $swal_icon = 'error';
            $swal_title = 'Gagal';
            $swal_text = 'Kantong tidak memenuhi syarat untuk dijadikan sampel panel (Status harus Musnah atau Reaktif).';
        }
    } else {
        $id_sql = mysql_real_escape_string($id);

        $sql = "UPDATE sampel_panel
                SET notrans = '$notrans_sql',
                    nokantong = '$nokantong',
                    tgl = '$tgl_jam',
                    instansi = '$instansi',
                    petugas = '$petugas'
                WHERE $pkField = '$id_sql'";
        $query = mysql_query($sql);

        if ($query) {
            $swal_icon = 'success';
            $swal_title = 'Berhasil';
            $swal_text = 'Data berhasil diupdate.';
            //=======Audit Trial====================================================================================
            $log_mdl = 'IMLTD';
            $log_aksi = 'Melakukan Update Sampel Panel pada nomor kantong/sampel: ' . $nokantong;
            include("user_log.php");
            //=====================================================================================================	
        } else {
            $swal_icon = 'error';
            $swal_title = 'Gagal';
            $swal_text = mysql_error();
        }
    }
}

if (isset($_POST['form_action']) && $_POST['form_action'] == 'hapus') {
    $id = isset($_POST['hapus_id']) ? mysql_real_escape_string($_POST['hapus_id']) : '';
    $nokantong = isset($_POST['hapus_nokantong']) ? mysql_real_escape_string($_POST['hapus_nokantong']) : '';

    $cekKantong = mysql_query("SELECT * FROM stokkantong WHERE noKantong='$nokantong'");
    $q_ktg = mysql_fetch_assoc($cekKantong);

    if ($id != '') {
        $sql = "DELETE FROM sampel_panel WHERE $pkField = '$id'";
        $query = mysql_query($sql);

        //=======Audit Trial====================================================================================
        $log_mdl = 'IMLTD';
        $log_aksi = 'Melakukan penghapusan Sampel Panel dengan ID: ' . $id . ' dan No Kantong: ' . $nokantong;
        include("user_log.php");
        //=====================================================================================================	

        /* =========================================
                Ubah Status Sampel Panel pada stokkantong
            ========================================== */
        $updateStatus = mysql_query("UPDATE stokkantong SET Status='6' WHERE noKantong='$nokantong'");

        if ($query) {
            $swal_icon = 'success';
            $swal_title = 'Berhasil';
            $swal_text = 'Data berhasil dihapus.';
        } else {
            $swal_icon = 'error';
            $swal_title = 'Gagal';
            $swal_text = mysql_error();
        }
    }
}

/* =========================
   QUERY DATA
========================= */
$awal_sql = mysql_real_escape_string($today);
$akhir_sql = mysql_real_escape_string($today1);
$instansi_sql = mysql_real_escape_string($src_instansi);

$query_sampel_panel = "
    SELECT *
    FROM sampel_panel
    WHERE CAST(tgl AS date) >= '$awal_sql'
      AND CAST(tgl AS date) <= '$akhir_sql'
      AND instansi LIKE '%$instansi_sql%'
    ORDER BY tgl DESC
";

$result_sampel_panel = mysql_query($query_sampel_panel);
if (!$result_sampel_panel) {
    die(mysql_error());
}

/* =========================
   EXPORT EXCEL
========================= */
if ($aksi == 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=sampel_panel_" . date('Ymd_His') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<html>";
    echo "<head><meta charset='utf-8'></head>";
    echo "<body>";
    echo "<table border='1'>";
    echo "<tr>
            <th>No</th>
            <th>No Transaksi</th>
            <th>No Kantong</th>
            <th>Tanggal</th>
            <th>Instansi</th>
            <th>Petugas</th>
          </tr>";

    $no = 1;
    mysql_data_seek($result_sampel_panel, 0);
    while ($row = mysql_fetch_assoc($result_sampel_panel)) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['notrans']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nokantong']) . "</td>";
        echo "<td>" . htmlspecialchars($row['tgl']) . "</td>";
        echo "<td>" . htmlspecialchars($row['instansi']) . "</td>";
        echo "<td>" . htmlspecialchars($row['petugas']) . "</td>";
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
    <title>Sampel Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

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

    <div class="card">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Sample Panel</h4>
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
                            value="<?php echo htmlspecialchars($today1); ?>"
                            min="<?php echo htmlspecialchars($today); ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Instansi</label>
                    <input type="text" id="instansi" class="form-control" name="instansi"
                        value="<?php echo htmlspecialchars($src_instansi); ?>" placeholder="Ketik instansi..."
                        autocomplete="off">
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
                        <!-- <th>No Transaksi</th> -->
                        <th>No Kantong</th>
                        <th>Tanggal</th>
                        <th>Instansi</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysql_fetch_assoc($result_sampel_panel)) {
                        $rowId = isset($row[$pkField]) ? $row[$pkField] : '';
                        $tglEdit = '';
                        if (isset($row['tgl']) && $row['tgl'] != '') {
                            $tglEdit = substr($row['tgl'], 0, 10);
                        }

                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        // echo "<td>" . htmlspecialchars($row['notrans']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nokantong']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tgl']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['instansi']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['petugas']) . "</td>";
                        echo "<td>
                            <button type='button' class='btn btn-warning btn-sm me-1'
                                onclick='openEdit(" . json_encode($rowId) . ", " . json_encode($row['notrans']) . ", " . json_encode($row['nokantong']) . ", " . json_encode($tglEdit) . ", " . json_encode($row['instansi']) . ", " . json_encode($row['petugas']) . ")'
                                data-bs-toggle='modal' data-bs-target='#modalSampel'>
                                Edit
                            </button>
                            <button type='button' class='btn btn-danger btn-sm'
                                onclick='confirmDelete(" . json_encode($rowId) . ", " . json_encode($row['nokantong']) . ")'>
                                Hapus
                            </button>
                          </td>";
                        echo "</tr>";
                    }
                    ?>
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

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">No Transaksi</label>
                                <input type="text" class="form-control" name="notrans" id="notrans" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No Kantong</label>
                                <input type="text" class="form-control" name="nokantong" id="nokantong" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tgl" id="tgl" required>
                            </div>

                            <div class="col-md-6" style="display:none;">
                                <label class="form-label">Instansi</label>
                                <input type="text" class="form-control" name="instansi_data" id="instansi_data"
                                    autocomplete="off">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Petugas</label>
                                <input type="text" class="form-control" name="petugas" id="petugas"
                                    value="<?php echo htmlspecialchars($namauser); ?>" required>
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
        <input type="hidden" name="hapus_nokantong" id="hapus_nokantong" value="">
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

        function setupInstansiAutocomplete(selector, appendTarget) {
            $(selector).autocomplete({
                appendTo: appendTarget,
                source: function(request, response) {
                    $.ajax({
                        url: "modul/suggest_instansi_transaksi.php",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function() {
                            console.log("Autocomplete error");
                        }
                    });
                },
                minLength: 2,
                delay: 300,
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    return false;
                }
            });
        }

        $(document).ready(function() {
            setupInstansiAutocomplete("#instansi", "body");
            setupInstansiAutocomplete("#instansi_data", "#modalSampel");

            $('#myTable').DataTable({
                scrollX: true,
                autoWidth: false,
                responsive: false
            });
        });

        function openTambah() {
            document.getElementById('modalSampelLabel').innerHTML = 'Tambah Data';
            document.getElementById('id').value = '';
            document.getElementById('notrans').value = generateNoTrans();
            document.getElementById('nokantong').value = '';
            document.getElementById('tgl').value = '<?php echo date('Y-m-d'); ?>';
            document.getElementById('instansi_data').value = '';
            document.getElementById('petugas').value = '';

            // AUTO ISI PETUGAS
            document.getElementById('petugas').value = "<?php echo addslashes($namauser); ?>";
        }

        function openEdit(id, notrans, nokantong, tgl, instansi, petugas) {
            document.getElementById('modalSampelLabel').innerHTML = 'Edit Data';
            document.getElementById('id').value = id;
            document.getElementById('notrans').value = notrans;
            document.getElementById('nokantong').value = nokantong;
            document.getElementById('tgl').value = tgl;
            document.getElementById('instansi_data').value = instansi;
            document.getElementById('petugas').value = petugas;
        }

        function confirmDelete(id, nokantong) {
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
                    document.getElementById('hapus_nokantong').value = nokantong;
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