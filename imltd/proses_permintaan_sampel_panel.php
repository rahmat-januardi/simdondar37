<?php
ob_start();
session_start();
require_once('config/db_connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$namauser = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';

$pkField = 'id';
$table   = 'sampel_panel_trans';

$result_sampel_panel = mysql_query("SELECT * FROM $table WHERE status=0");

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
    <?php
    if (isset($_POST['submit'])) { ?>
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Proses Permintaan Sampel Panel</h4>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card-body">
            <p><?= $_POST['notrans'] ?></p>
        </div>
    </div>
    <? } else { ?>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Permintaan Sampel Panel</h4>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card-body">
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
                            <form method="POST" action="" style="margin:0;">
                                <input type="hidden" name="notrans"
                                    value="<?php echo htmlspecialchars($row['notrans']); ?>">
                                <input type="hidden" name="reaktif"
                                    value="<?php echo htmlspecialchars($row['reaktif']); ?>">
                                <input type="hidden" name="nonreaktif"
                                    value="<?php echo htmlspecialchars($row['nonreaktif']); ?>">
                                <button type="submit" class="btn btn-primary btn-sm" name="submit">
                                    Proses
                                </button>
                            </form>

                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <hr>
        </div>
    </div>
    <?php } ?>

    <script>
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