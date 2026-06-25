<?php
ob_start();
session_start();
require_once('config/db_connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Jakarta');

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

function setFlash($icon, $title, $text)
{
    $_SESSION['flash'] = array(
        'icon'  => $icon,
        'title' => $title,
        'text'  => $text
    );
}

function statusBadge($status)
{
    $status = (int)$status;
    if ($status == 2) {
        return '<span class="badge bg-success">Selesai</span>';
    } else if ($status == 1) {
        return '<span class="badge bg-warning text-dark">Telah dipenuhi</span>';
    } else {
        return '<span class="badge bg-secondary">Belum dipenuhi</span>';
    }
}

function getUri()
{
    if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '') {
        return $_SERVER['REQUEST_URI'];
    }
    return basename($_SERVER['PHP_SELF']);
}

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : array();
unset($_SESSION['flash']);

$uri_self = getUri();

$verif_active = isset($_SESSION['verif_panel']['notrans']) ? true : false;
$verif_data   = array();
$verif_rows   = array();

if ($verif_active) {
    $verif_notrans = $_SESSION['verif_panel']['notrans'];
    $verif_notrans_sql = esc($verif_notrans);

    $qHeaderActive = mysql_query("SELECT * FROM {$tableHeader} WHERE notrans = '{$verif_notrans_sql}' LIMIT 1");
    if ($qHeaderActive && mysql_num_rows($qHeaderActive) > 0) {
        $verif_data = mysql_fetch_assoc($qHeaderActive);
    } else {
        unset($_SESSION['verif_panel']);
        $verif_active = false;
    }

    if ($verif_active) {
        $qDetailActive = mysql_query("SELECT * FROM {$tableDetail} WHERE notrans = '{$verif_notrans_sql}' ORDER BY id ASC");
        if ($qDetailActive) {
            while ($r = mysql_fetch_assoc($qDetailActive)) {
                $verif_rows[] = $r;
            }
        }
    }
}

/* =========================
   MULAI VERIFIKASI
========================= */
if (isset($_POST['submit']) && $_POST['submit'] == 'verifikasi') {
    $header_id = isset($_POST['header_id']) ? intval($_POST['header_id']) : 0;

    if ($header_id <= 0) {
        setFlash('error', 'Gagal', 'Data permintaan tidak valid.');
        header("Location: " . $uri_self);
        exit;
    }

    $qHeader = mysql_query("SELECT * FROM {$tableHeader} WHERE {$pkField} = '{$header_id}' LIMIT 1");
    if (!$qHeader || mysql_num_rows($qHeader) == 0) {
        setFlash('error', 'Gagal', 'Data permintaan tidak ditemukan.');
        header("Location: " . $uri_self);
        exit;
    }

    $header = mysql_fetch_assoc($qHeader);

    $_SESSION['verif_panel'] = array(
        'header_id' => $header['id'],
        'notrans'   => $header['notrans']
    );

    setFlash('success', 'Berhasil', 'Mode verifikasi aktif. Silakan scan / input nomor kantong.');
    header("Location: " . $uri_self);
    exit;
}

/* =========================
   SCAN / INPUT KANTONG
========================= */
if (isset($_POST['form_action']) && $_POST['form_action'] == 'scan_kantong') {
    if (!isset($_SESSION['verif_panel']['notrans'])) {
        setFlash('error', 'Gagal', 'Silakan klik tombol Verifikasi terlebih dahulu.');
        header("Location: " . $uri_self);
        exit;
    }

    $notrans = trim($_SESSION['verif_panel']['notrans']);
    $nokantong = isset($_POST['nokantong']) ? trim($_POST['nokantong']) : '';

    if ($nokantong == '') {
        setFlash('error', 'Gagal', 'Nomor kantong wajib diisi.');
        header("Location: " . $uri_self);
        exit;
    }

    $notrans_sql = esc($notrans);
    $nokantong_sql = esc($nokantong);

    $qDetail = mysql_query("SELECT * FROM {$tableDetail} WHERE notrans = '{$notrans_sql}' AND nokantong = '{$nokantong_sql}' LIMIT 1");
    if (!$qDetail || mysql_num_rows($qDetail) == 0) {
        setFlash('error', 'Gagal', 'Kantong tidak ditemukan pada detail transaksi ini.');
        header("Location: " . $uri_self);
        exit;
    }

    $detail = mysql_fetch_assoc($qDetail);

    if ((int)$detail['up_data'] == 1) {
        setFlash('info', 'Info', 'Kantong ini sudah diverifikasi sebelumnya.');
        header("Location: " . $uri_self);
        exit;
    }

    $qUpdate = mysql_query("UPDATE {$tableDetail} SET up_data = 1 WHERE id = '" . (int)$detail['id'] . "' LIMIT 1");

    //  update status kantong
    $qUpdateStatus = mysql_query("UPDATE `stokkantong` SET `Status` = 9, `tgl_keluar`='" . $detail['created'] . "' WHERE `noKantong` = '" . $nokantong . "'");

    if ($qUpdate) {
        $log_mdl  = 'IMLTD';
        $log_aksi = 'Memverifikasi Sampel Panel dengan No Kantong ' . $nokantong . ' pada No Transaksi: ' . $notrans;
        include("user_log.php");

        setFlash('success', 'Berhasil', 'Kantong berhasil diverifikasi.');
    } else {
        setFlash('error', 'Gagal', mysql_error());
    }

    header("Location: " . $uri_self);
    exit;
}

/* =========================
   BATAL: TUTUP SESSION SAJA
========================= */
if (isset($_POST['form_action']) && $_POST['form_action'] == 'batal_verif') {
    if (!isset($_SESSION['verif_panel']['notrans'])) {
        setFlash('error', 'Gagal', 'Tidak ada proses verifikasi aktif.');
        header("Location: " . $uri_self);
        exit;
    }

    $notrans = isset($_SESSION['verif_panel']['notrans']) ? $_SESSION['verif_panel']['notrans'] : '';

    unset($_SESSION['verif_panel']);

    setFlash('success', 'Berhasil', 'Sesi verifikasi ditutup.');
    header("Location: " . $uri_self);
    exit;
}

/* =========================
   SIMPAN: SELESAIKAN VERIFIKASI
========================= */
if (isset($_POST['form_action']) && $_POST['form_action'] == 'selesai_verif') {
    if (!isset($_SESSION['verif_panel']['notrans'])) {
        setFlash('error', 'Gagal', 'Tidak ada proses verifikasi aktif.');
        header("Location: " . $uri_self);
        exit;
    }

    $notrans = trim($_SESSION['verif_panel']['notrans']);
    $notrans_sql = esc($notrans);

    $qTotal = mysql_query("SELECT COUNT(*) AS total FROM {$tableDetail} WHERE notrans = '{$notrans_sql}'");
    $qDone  = mysql_query("SELECT COUNT(*) AS total FROM {$tableDetail} WHERE notrans = '{$notrans_sql}' AND up_data = 1");

    $total = 0;
    $done  = 0;

    if ($qTotal) {
        $rTotal = mysql_fetch_assoc($qTotal);
        $total = isset($rTotal['total']) ? (int)$rTotal['total'] : 0;
    }

    if ($qDone) {
        $rDone = mysql_fetch_assoc($qDone);
        $done = isset($rDone['total']) ? (int)$rDone['total'] : 0;
    }

    if ($total > 0 && $done == $total) {
        $header_id = isset($_SESSION['verif_panel']['header_id']) ? intval($_SESSION['verif_panel']['header_id']) : 0;
        if ($header_id > 0) {
            mysql_query("UPDATE {$tableHeader} SET status = 2 WHERE {$pkField} = '{$header_id}' LIMIT 1");
        }

        $log_mdl  = 'IMLTD';
        $log_aksi = 'Menyelesaikan verifikasi Sampel Panel dengan No Transaksi: ' . $notrans;
        include("user_log.php");

        unset($_SESSION['verif_panel']);

        setFlash('success', 'Berhasil', 'Verifikasi selesai dan data sudah disimpan.');
        header("Location: " . $uri_self);
        exit;
    } else {
        setFlash('error', 'Gagal', 'Masih ada kantong yang belum diverifikasi.');
        header("Location: " . $uri_self);
        exit;
    }
}

/* =========================
   QUERY DATA HEADER
========================= */
$query_sampel_panel = "SELECT * FROM {$tableHeader} WHERE status = 1 ORDER BY id DESC";
$result_sampel_panel = mysql_query($query_sampel_panel);
if (!$result_sampel_panel) {
    die(mysql_error());
}

/* =========================
   COUNT DATA VERIFIKASI AKTIF
========================= */
$jumlah_total = 0;
$jumlah_done  = 0;

if ($verif_active && isset($_SESSION['verif_panel']['notrans'])) {
    $notrans_count_sql = esc($_SESSION['verif_panel']['notrans']);

    $qCountTotal = mysql_query("SELECT COUNT(*) AS total FROM {$tableDetail} WHERE notrans = '{$notrans_count_sql}'");
    $qCountDone  = mysql_query("SELECT COUNT(*) AS total FROM {$tableDetail} WHERE notrans = '{$notrans_count_sql}' AND up_data = 1");

    if ($qCountTotal) {
        $r = mysql_fetch_assoc($qCountTotal);
        $jumlah_total = isset($r['total']) ? (int)$r['total'] : 0;
    }

    if ($qCountDone) {
        $r = mysql_fetch_assoc($qCountDone);
        $jumlah_done = isset($r['total']) ? (int)$r['total'] : 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sampel Panel</title>

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

    #myTable,
    #detailTable {
        width: 100% !important;
    }

    #myTable th,
    #myTable td,
    #detailTable th,
    #detailTable td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .scan-input {
        font-size: 1.1rem;
        padding: 0.85rem 1rem;
    }
    </style>
</head>

<body>

    <?php if ($verif_active) { ?>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Verifikasi Sampel Panel</h4>
                <small>Mode scan aktif untuk No Transaksi: <?php echo h($verif_data['notrans']); ?></small>
            </div>

            <form method="POST" action="" style="margin:0;">
                <input type="hidden" name="form_action" value="batal_verif">
                <button type="submit" class="btn btn-secondary">Batal</button>
            </form>
        </div>

        <div class="card-body">
            <div class="alert alert-info">
                Arah kerja:
                <strong>scan / input nomor kantong</strong> ? sistem cek pada detail transaksi ?
                jika cocok maka tanda centang muncul di daftar.
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="alert alert-info mb-0">
                        <strong>No Transaksi:</strong><br>
                        <?php echo h($verif_data['notrans']); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning mb-0">
                        <strong>Total Kantong:</strong> <?php echo (int)$jumlah_total; ?><br>
                        <strong>Terverifikasi:</strong> <?php echo (int)$jumlah_done; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success mb-0">
                        <strong>Petugas:</strong><br>
                        <?php echo h($verif_data['petugas']); ?>
                    </div>
                </div>
            </div>

            <form method="POST" action="" class="row g-3 mb-4">
                <input type="hidden" name="form_action" value="scan_kantong">

                <div class="col-md-8">
                    <label class="form-label">Scan / Input No Kantong</label>
                    <input type="text" name="nokantong" id="nokantong" class="form-control scan-input"
                        autocomplete="off" autofocus>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 btn-lg">Verifikasi</button>
                </div>
            </form>

            <div class="table-responsive">
                <table id="detailTable" class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No Kantong</th>
                            <th>Jenis Sampel</th>
                            <th>Status</th>
                            <th>Petugas Komp</th>
                            <th>Created</th>
                            <th>Verif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if (count($verif_rows) > 0) {
                                $noDet = 1;
                                for ($i = 0; $i < count($verif_rows); $i++) {
                                    $row = $verif_rows[$i];
                                    $upText = ((int)$row['up_data'] == 1) ? 'Sudah Verifikasi' : 'Belum Verifikasi';
                                    $verifMark = ((int)$row['up_data'] == 1)
                                        ? '<span class="badge bg-success">&#10003;</span>'
                                        : '<span class="badge bg-secondary">-</span>';
                            ?>
                        <tr>
                            <td><?php echo $noDet++; ?></td>
                            <td><?php echo h($row['nokantong']); ?></td>
                            <td>
                                <?php if ($row['jenis_sampel'] == 'REAKTIF') { ?>
                                <span class="badge bg-danger">REAKTIF</span>
                                <?php } else { ?>
                                <span class="badge bg-success">NONREAKTIF</span>
                                <?php } ?>
                            </td>
                            <td><?php echo h($upText); ?></td>
                            <td><?php echo h($row['ptgs_komp']); ?></td>
                            <td><?php echo h($row['created']); ?></td>
                            <td class="text-center"><?php echo $verifMark; ?></td>
                        </tr>
                        <?php
                                }
                            }
                            ?>
                    </tbody>
                </table>
            </div>

            <form method="POST" action="" class="mt-3">
                <input type="hidden" name="form_action" value="selesai_verif">
                <button type="submit" class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>

    <?php } else { ?>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Verifikasi Sampel Panel</h4>
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
                        <th>Petugas IMLTD</th>
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
                        ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo h($row['notrans']); ?></td>
                        <td><?php echo (int)$row['reaktif']; ?></td>
                        <td><?php echo (int)$row['nonreaktif']; ?></td>
                        <td><?php echo $tglTampil; ?></td>
                        <td><?php echo h($row['petugas']); ?></td>
                        <td><?php echo statusBadge($row['status']); ?></td>
                        <td>
                            <form method="POST" action="" style="margin:0; display:inline-block;">
                                <input type="hidden" name="header_id" value="<?php echo h($rowId); ?>">
                                <button type="submit" name="submit" value="verifikasi" class="btn btn-primary btn-sm">
                                    Verifikasi
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php } ?>

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

        if ($('#detailTable').length) {
            $('#detailTable').DataTable({
                scrollX: true,
                autoWidth: false,
                responsive: false,
                paging: false,
                searching: false,
                info: false,
                language: {
                    emptyTable: "Data belum ada"
                }
            });
        }

        if ($('#nokantong').length) {
            $('#nokantong').focus();
        }
    });
    </script>

    <?php if (!empty($flash['title'])) { ?>
    <script>
    Swal.fire({
        icon: '<?php echo $flash['icon']; ?>',
        title: '<?php echo addslashes($flash['title']); ?>',
        text: '<?php echo addslashes($flash['text']); ?>'
    }).then(function() {
        var inp = document.getElementById('nokantong');
        if (inp) {
            inp.focus();
            inp.select();
        }
    });
    </script>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>