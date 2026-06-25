<?php
ob_start();
session_start();
require_once('config/db_connect.php');

// date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
ini_set('display_errors', 0);

$namauser = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';

$pkField     = 'id';
$tableHeader = 'sampel_panel_trans';
$tableDetail = 'sampel_panel_detail';

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function esc($str)
{
    return mysql_real_escape_string(trim($str));
}

function jenisKomponenLabel($jenis)
{
    $jenis = trim((string)$jenis);

    if ($jenis == '2') {
        return 'Double';
    } else if ($jenis == '3') {
        return 'Triple';
    }

    return '-';
}

function setFlash($icon, $title, $text)
{
    $_SESSION['flash'] = array(
        'icon'  => $icon,
        'title' => $title,
        'text'  => $text
    );
}

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : array();
unset($_SESSION['flash']);

$detail_header = array();
$detail_rows = array();
$show_detail_modal = false;

$redirect = "pmikasir2.php?module=rekap_sampel_panel";

/* =========================
   ACTION DETAIL / EXPORT
========================= */
if (isset($_POST['submit'])) {
    $action  = $_POST['submit'];
    $notrans = isset($_POST['notrans']) ? trim($_POST['notrans']) : '';

    if ($notrans == '') {
        setFlash('error', 'Gagal', 'No transaksi tidak ditemukan.');
        header("Location: " . $redirect);
        exit;
    }

    $notrans_sql = esc($notrans);

    $qHeader = mysql_query("SELECT * FROM {$tableHeader} WHERE notrans = '{$notrans_sql}' LIMIT 1");
    if (!$qHeader || mysql_num_rows($qHeader) == 0) {
        setFlash('error', 'Gagal', 'Data tidak ditemukan.');
        header("Location: " . $redirect);
        exit;
    }

    $detail_header = mysql_fetch_assoc($qHeader);

    $qDetail = mysql_query("SELECT * FROM {$tableDetail} WHERE notrans = '{$notrans_sql}' ORDER BY id ASC");
    if ($qDetail) {
        while ($r = mysql_fetch_assoc($qDetail)) {
            $detail_rows[] = $r;
        }
    }

    if ($action == 'export_excel') {
        ob_end_clean();

        $filename = 'rekap_sampel_panel_' . $notrans . '.xls';

        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<html>";
        echo "<head><meta charset='utf-8'></head>";
        echo "<body>";

        echo "<table border='1' cellspacing='0' cellpadding='5'>";
        echo "<tr><th colspan='2'>Rekap Sampel Panel</th></tr>";
        echo "<tr><td>No Transaksi</td><td>" . h($detail_header['notrans']) . "</td></tr>";
        echo "<tr><td>Reaktif</td><td>" . (int)$detail_header['reaktif'] . "</td></tr>";
        echo "<tr><td>NonReaktif</td><td>" . (int)$detail_header['nonreaktif'] . "</td></tr>";
        echo "<tr><td>Tanggal Permintaan</td><td>" . (!empty($detail_header['tgl_permintaan']) ? date('d-m-Y H:i', strtotime($detail_header['tgl_permintaan'])) : '-') . "</td></tr>";
        echo "<tr><td>Petugas IMLTD</td><td>" . h($detail_header['petugas']) . "</td></tr>";
        echo "<tr><td>Status</td><td>" . ((int)$detail_header['status'] == 2 ? 'Selesai' : ((int)$detail_header['status'] == 1 ? 'Telah dipenuhi' : 'Belum dipenuhi')) . "</td></tr>";
        echo "</table><br>";

        echo "<table border='1' cellspacing='0' cellpadding='5'>";
        echo "<tr>
                <th>No</th>
                <th>No Kantong</th>
                <th>Jenis Sampel</th>
                <th>Status Verifikasi</th>
                <th>Petugas Distribusi</th>
                <th>Tanggal & Waktu Pengerjaan</th>
              </tr>";

        $no = 1;
        if (count($detail_rows) > 0) {
            foreach ($detail_rows as $row) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . h($row['nokantong']) . "</td>";
                echo "<td>" . h($row['jenis_sampel']) . "</td>";
                echo "<td>" . ((int)$row['up_data'] == 1 ? 'Sudah Verifikasi' : 'Belum Verifikasi') . "</td>";
                echo "<td>" . h($row['ptgs_komp']) . "</td>";
                echo "<td>" . h($row['created']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' align='center'>Data detail belum ada</td></tr>";
        }

        echo "</table>";
        echo "</body>";
        echo "</html>";
        exit;
    }

    if ($action == 'print_serah_terima') {
        $notrans_sql = esc($notrans);

        $qHeader = mysql_query("SELECT * FROM {$tableHeader} WHERE notrans = '{$notrans_sql}' LIMIT 1");
        if (!$qHeader || mysql_num_rows($qHeader) == 0) {
            die('Data header tidak ditemukan.');
        }

        $detail_header = mysql_fetch_assoc($qHeader);

        $qDetailPrint = mysql_query("
            SELECT d.nokantong, d.ptgs_komp, d.jenis_sampel, s.jenis, s.volume, s.merk, s.produk, s.gol_Darah, s.RhesusDrh, s.volumeasal
            FROM {$tableDetail} d
            LEFT JOIN stokkantong s ON s.noKantong = d.nokantong
            WHERE d.notrans = '{$notrans_sql}'
            ORDER BY d.id ASC
        ");

        $detail_rows_print = array();
        if ($qDetailPrint) {
            while ($r = mysql_fetch_assoc($qDetailPrint)) {
                $detail_rows_print[] = $r;
            }
        }

        $tglTampil = (!empty($detail_header['tgl_permintaan']))
            ? date('d-m-Y H:i', strtotime($detail_header['tgl_permintaan']))
            : '-';

        ob_end_clean();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Serah Terima Kantong untuk Sampel Panel</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        margin: 25px;
        color: #000;
    }

    .judul {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    table,
    th,
    td {
        border: 1px solid #000;
    }

    th,
    td {
        padding: 7px;
        vertical-align: top;
    }

    .no-border,
    .no-border td {
        border: none !important;
    }

    .center {
        text-align: center;
    }

    .signature td {
        height: 50px;
        text-align: center;
        vertical-align: middle;
    }

    @media print {
        .noprint {
            display: none;
        }

        body {
            margin: 10mm;
        }
    }
    </style>
</head>

<body onload="window.print(); setTimeout(function(){ window.close(); }, 500);">

    <div class="judul">Serah Terima Kantong untuk Sampel Panel</div>

    <table>
        <tr>
            <td>
                <b>Tanggal Transaksi</b>
            </td>
            <td>
                <?php echo h($tglTampil); ?>
            </td>
            <td>
                <b>Bagian yang mengirimkan</b>
            </td>
            <td>Distribusi</td>
        </tr>
        <tr>
            <td><b>No. Transaksi</b></td>
            <td><?php echo h($detail_header['notrans']); ?></td>
            <td> <b>Bagian yang menerima:</b></td>
            <td>IMLTD</td>
        </tr>
        <tr>
            <td><b>Keadaan Kantong</b></td>
            <td colspan="3">Baik</td>
        </tr>
    </table>

    <table>
        <tr class="center">
            <th>No.</th>
            <th>Nomor Kantong</th>
            <th>Jenis Kantong</th>
            <th>Volume</th>
            <th>Merk</th>
            <th>Golongan Darah</th>
            <th>Hasil Pemeriksaan</th>
        </tr>
        <?php
                if (count($detail_rows_print) > 0) {
                    $no = 1;
                    foreach ($detail_rows_print as $rowPrint) {
                        $jenisLabel = jenisKomponenLabel(isset($rowPrint['jenis']) ? $rowPrint['jenis'] : '');
                        $volumeAsal  = isset($rowPrint['volumeasal']) ? $rowPrint['volumeasal'] : '-';
                        $volume  = isset($rowPrint['volume']) ? $rowPrint['volume'] : '-';
                        $golDarah    = isset($rowPrint['gol_Darah']) ? $rowPrint['gol_Darah'] : '-';
                        $rhesus      = isset($rowPrint['RhesusDrh']) ? $rowPrint['RhesusDrh'] : '-';
                        $jenisSampel = isset($rowPrint['jenis_sampel']) ? $rowPrint['jenis_sampel'] : '-';

                        $golRhesus = trim($golDarah . ' ' . $rhesus);
                ?>
        <tr class="center">
            <td class="center"><?php echo $no++; ?></td>
            <td><?php echo h($rowPrint['nokantong']); ?></td>
            <td><?php echo h($jenisLabel . ' ' . $volumeAsal); ?></td>
            <td><?php echo h($volume); ?></td>
            <td><?php echo h(isset($rowPrint['merk']) ? $rowPrint['merk'] : '-'); ?></td>
            <td><?php echo h($golRhesus); ?></td>
            <td><?php echo h($jenisSampel); ?></td>
        </tr>
        <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="center">Data kantong belum ada</td></tr>';
                }
                ?>
    </table>

    <table class="signature" style="width: 70%;">
        <tr class="center">
            <th>Keterangan</th>
            <th>Nama Petugas</th>
            <th>Tanda Tangan</th>
        </tr>
        <tr>
            <td>Petugas Menyerahkan Bag Distribusi</td>
            <td><?php echo h($rowPrint['ptgs_komp']); ?></td>
            <td></td>
        </tr>
        <tr>
            <td>Petugas Penerima Bag IMLTD</td>
            <td><?php echo h($detail_header['petugas']); ?></td>
            <td></td>
        </tr>
    </table>

</body>

</html>
<?php
        exit;
    }

    if ($action == 'detail') {
        $show_detail_modal = true;
    }
}

/* =========================
   QUERY DATA HEADER
========================= */
$query_sampel_panel = "SELECT * FROM {$tableHeader} WHERE status != 0 ORDER BY id DESC";
$result_sampel_panel = mysql_query($query_sampel_panel);
if (!$result_sampel_panel) {
    die(mysql_error());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Sampel Panel</title>

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
    </style>
</head>

<body>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Rekap Sampel Panel</h4>
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
                        <td><?php echo h($row['notrans']); ?></td>
                        <td><?php echo (int)$row['reaktif']; ?></td>
                        <td><?php echo (int)$row['nonreaktif']; ?></td>
                        <td><?php echo $tglTampil; ?></td>
                        <td><?php echo h($row['petugas']); ?></td>
                        <td><?php echo $statusBadge; ?></td>
                        <td>
                            <form method="POST" action="" style="margin:0; display:inline-block;">
                                <input type="hidden" name="notrans" value="<?php echo h($row['notrans']); ?>">
                                <button type="submit" name="submit" value="detail" class="btn btn-primary btn-sm">
                                    Detail
                                </button>
                            </form>

                            <form method="POST" action="" style="margin:0; display:inline-block;">
                                <input type="hidden" name="notrans" value="<?php echo h($row['notrans']); ?>">
                                <button type="submit" name="submit" value="export_excel" class="btn btn-success btn-sm">
                                    Export Excel
                                </button>
                            </form>

                            <!-- Cetak Form Serah Terima Kantong Untuk Sampel Panel -->
                            <form method="POST" action="" target="_blank" style="margin:0; display:inline-block;">
                                <input type="hidden" name="notrans" value="<?php echo h($row['notrans']); ?>">
                                <button type="submit" name="submit" value="print_serah_terima"
                                    class="btn btn-info btn-sm ms-1">
                                    Cetak Serah Terima
                                </button>
                            </form>
                            <a href="pmi<?= $_SESSION['leveluser']; ?>.php?module=cetak_label_panel&header_id=<?php echo (int)$row['id']; ?>"
                                target="_blank" class="btn btn-warning btn-sm ms-1">
                                Cetak Label
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL DETAIL -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="detailModalLabel">Detail Rekap Sampel Panel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?php if (!empty($detail_header)) { ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <strong>No Transaksi:</strong><br>
                                <?php echo h($detail_header['notrans']); ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3 bg-light">
                                <strong>Reaktif:</strong><br>
                                <?php echo (int)$detail_header['reaktif']; ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="border rounded p-3 bg-light">
                                <strong>NonReaktif:</strong><br>
                                <?php echo (int)$detail_header['nonreaktif']; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <strong>Tgl Permintaan:</strong><br>
                                <?php echo (!empty($detail_header['tgl_permintaan'])) ? date('d-m-Y H:i', strtotime($detail_header['tgl_permintaan'])) : '-'; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <strong>Petugas:</strong><br>
                                <?php echo h($detail_header['petugas']); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <strong>Status:</strong><br>
                                <?php
                                    if ((int)$detail_header['status'] == 2) {
                                        echo '<span class="badge bg-success">Selesai</span>';
                                    } else if ((int)$detail_header['status'] == 1) {
                                        echo '<span class="badge bg-warning text-dark">Telah dipenuhi</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">Belum dipenuhi</span>';
                                    }
                                    ?>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr style="text-align: center;">
                                    <th>No</th>
                                    <th>No Kantong</th>
                                    <th>Jenis Sampel</th>
                                    <th>Status</th>
                                    <th>Petugas Distribusi</th>
                                    <th>Tanggal Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (count($detail_rows) > 0) {
                                        $noDet = 1;
                                        foreach ($detail_rows as $dt) {
                                            $upText = ((int)$dt['up_data'] == 1) ? 'Sudah Verifikasi' : 'Belum Verifikasi';
                                            $verifMark = ((int)$dt['up_data'] == 1)
                                                ? '<span class="badge bg-success">&#10003;</span>'
                                                : '<span class="badge bg-danger">&#10007;</span>';
                                            $badgeJenis = ($dt['jenis_sampel'] == 'REAKTIF')
                                                ? '<span class="badge bg-danger">REAKTIF</span>'
                                                : '<span class="badge bg-success">NONREAKTIF</span>';
                                    ?>
                                <tr style="text-align: center;">
                                    <td><?php echo $noDet++; ?></td>
                                    <td><?php echo h($dt['nokantong']); ?></td>
                                    <td><?php echo $badgeJenis; ?></td>
                                    <td><?php echo $verifMark; ?></td>
                                    <td><?php echo h($dt['ptgs_komp']); ?></td>
                                    <td><?php echo h($dt['created']); ?></td>
                                </tr>
                                <?php
                                        }
                                    } else {
                                        ?>
                                <tr>
                                    <td colspan=" 6" class="text-center text-muted">Data detail belum ada</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } else { ?>
                    <div class="alert alert-warning mb-0">Data detail tidak ditemukan.</div>
                    <?php } ?>
                </div>

                <div class="modal-footer">
                    <?php if (!empty($detail_header)) { ?>
                    <form method="POST" action="" style="margin:0; display:inline-block;">
                        <input type="hidden" name="notrans" value="<?php echo h($detail_header['notrans']); ?>">
                        <button type="submit" name="submit" value="export_excel" class="btn btn-success">
                            Export Excel
                        </button>
                    </form>
                    <?php } ?>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        if ($('#myTable').length) {
            $('#myTable').DataTable({
                scrollX: true,
                autoWidth: false,
                responsive: false,
                language: {
                    emptyTable: "Data belum ada"
                }
            });
        }
    });
    </script>

    <?php if ($show_detail_modal) { ?>
    <script>
    $(document).ready(function() {
        var detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    });
    </script>
    <?php } ?>

    <?php if (!empty($flash['title'])) { ?>
    <script>
    Swal.fire({
        icon: '<?php echo $flash['icon']; ?>',
        title: '<?php echo addslashes($flash['title']); ?>',
        text: '<?php echo addslashes($flash['text']); ?>'
    });
    </script>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>