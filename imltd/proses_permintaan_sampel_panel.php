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

$swal_icon  = '';
$swal_title = '';
$swal_text  = '';

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

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : array();
unset($_SESSION['flash']);

if (!isset($_SESSION['panel_proses'])) {
    $_SESSION['panel_proses'] = array();
}

$redirect = "pmikasir2.php?module=proses_permintaan_sampel_panel";

/* =========================
   START PROSES
========================= */
if (isset($_POST['submit']) && $_POST['submit'] == 'proses') {
    $header_id = isset($_POST['header_id']) ? intval($_POST['header_id']) : 0;

    if ($header_id <= 0) {
        setFlash('error', 'Gagal', 'Data permintaan tidak valid.');
        header("Location: " . $redirect);
        exit;
    }

    $qHeader = mysql_query("SELECT * FROM {$tableHeader} WHERE {$pkField} = '{$header_id}' LIMIT 1");
    if (!$qHeader || mysql_num_rows($qHeader) == 0) {
        setFlash('error', 'Gagal', 'Data permintaan tidak ditemukan.');
        header("Location: " . $redirect);
        exit;
    }

    $header = mysql_fetch_assoc($qHeader);

    $_SESSION['panel_proses'] = array(
        'header_id'           => $header['id'],
        'notrans'             => $header['notrans'],
        'reaktif_target'      => (int)$header['reaktif'],
        'nonreaktif_target'   => (int)$header['nonreaktif'],
        'items'               => array()
    );



    // setFlash('success', 'Berhasil', 'Silakan input nomor kantong.');
    header("Location: " . $redirect);
    exit;
}

/* =========================
   TAMBAH KANTONG KE SESSION
========================= */
if (isset($_POST['form_action']) && $_POST['form_action'] == 'tambah_kantong') {
    $nokantong = isset($_POST['nokantong']) ? trim($_POST['nokantong']) : '';

    if (!isset($_SESSION['panel_proses']['notrans'])) {
        setFlash('error', 'Gagal', 'Silakan klik Proses terlebih dahulu.');
        header("Location: " . $redirect);
        exit;
    }

    if ($nokantong == '') {
        setFlash('error', 'Gagal', 'Nomor kantong wajib diisi.');
        header("Location: " . $redirect);
        exit;
    }

    $nokantong_sql = esc($nokantong);

    // Cek apakah kantong sudah pernah di simpan di database untuk permintaan sampel panel ini
    $qCek = mysql_query("SELECT notrans FROM {$tableDetail} WHERE nokantong = '$nokantong_sql'  LIMIT 1");
    if ($qCek && mysql_num_rows($qCek) > 0) {

        setFlash('error', 'Gagal', 'Kantong ini sudah pernah diproses untuk permintaan sampel panel dengan nomor transaksi: ' . mysql_result($qCek, 0, 'notrans'));
        header("Location: " . $redirect);
        exit;
    }

    $qStok = mysql_query("SELECT NoKantong, produk, hasilNAT FROM stokkantong WHERE NoKantong = '$nokantong_sql' LIMIT 1");
    if (!$qStok || mysql_num_rows($qStok) == 0) {
        setFlash('error', 'Gagal', 'Kantong tidak ada.');
        header("Location: " . $redirect);
        exit;
    }

    $stok = mysql_fetch_assoc($qStok);


    // ========= Cek apakah kantong ada di table hasilelisa ==============
    // Ambil Kantong A
    $kantongA = substr_replace($nokantong, 'A', -1);

    // Cek apakah ada datanya di table hasilelisa
    $qHasilelisa = mysql_query("SELECT noKantong FROM hasilelisa WHERE noKantong = '$kantongA'");

    // Jika Ada, Kemudian coba cek apakah ada salah satu Reaktif atau Semua Nonreaktif dari 4 parameter
    if (mysql_num_rows($qHasilelisa) > 0) {
        $qReaktif = mysql_query("SELECT noKantong FROM hasilelisa WHERE noKantong = '$kantongA' AND Hasil = 1 LIMIT 1");
        if (mysql_num_rows($qReaktif) > 0) {
            $hasilNat = '1'; // REAKTIF
        } else {
            $hasilNat = '0'; // NONREAKTIF
        }
    } else {
        setFlash('error', 'Gagal', 'Kantong tidak memiliki hasil di table hasilelisa.');
        header("Location: " . $redirect);
        exit;
    }
    // ====================================================================

    if ($hasilNat === '1') {
        $jenis_sampel = 'REAKTIF';
    } else if ($hasilNat === '0') {
        $jenis_sampel = 'NONREAKTIF';
    } else {
        setFlash('error', 'Gagal', 'Nilai hasilNAT tidak dikenali. Hanya 1 atau 0 yang diizinkan.');
        header("Location: " . $redirect);
        exit;
    }

    $items = isset($_SESSION['panel_proses']['items']) ? $_SESSION['panel_proses']['items'] : array();

    // cek duplikat kantong di session
    for ($i = 0; $i < count($items); $i++) {
        if ($items[$i]['nokantong'] == $stok['NoKantong']) {
            setFlash('error', 'Gagal', 'Kantong ini sudah dimasukkan.');
            header("Location: " . $redirect);
            exit;
        }
    }

    $countReaktif = 0;
    $countNonreaktif = 0;
    for ($i = 0; $i < count($items); $i++) {
        if ($items[$i]['jenis_sampel'] == 'REAKTIF') {
            $countReaktif++;
        } else if ($items[$i]['jenis_sampel'] == 'NONREAKTIF') {
            $countNonreaktif++;
        }
    }

    $targetReaktif    = isset($_SESSION['panel_proses']['reaktif_target']) ? (int)$_SESSION['panel_proses']['reaktif_target'] : 0;
    $targetNonreaktif = isset($_SESSION['panel_proses']['nonreaktif_target']) ? (int)$_SESSION['panel_proses']['nonreaktif_target'] : 0;

    if ($jenis_sampel == 'REAKTIF' && $countReaktif >= $targetReaktif) {
        setFlash('error', 'Penuh', 'Batas kantong REAKTIF sudah terpenuhi.');
        header("Location: " . $redirect);
        exit;
    }

    if ($jenis_sampel == 'NONREAKTIF' && $countNonreaktif >= $targetNonreaktif) {
        setFlash('error', 'Penuh', 'Batas kantong NONREAKTIF sudah terpenuhi.');
        header("Location: " . $redirect);
        exit;
    }

    $_SESSION['panel_proses']['items'][] = array(
        'nokantong'    => $stok['NoKantong'],
        'produk'       => $stok['produk'],
        'hasilNAT'     => $hasilNat,
        'jenis_sampel' => $jenis_sampel,
        'up_data'      => 0,
        'ptgs_komp'    => $namauser,
        'created'      => date('Y-m-d H:i:s')
    );

    // // Audit trail
    // $log_mdl  = 'KOMPONEN';
    // $log_aksi = 'Menambahkan kantong ' . $stok['NoKantong'] . ' ke proses Permintaan Sampel Panel No Transaksi: ' . $_SESSION['panel_proses']['notrans'];
    // include("user_log.php");

    // setFlash('success', 'Berhasil', 'Kantong berhasil ditambahkan.');
    header("Location: " . $redirect);
    exit;
}

/* =========================
   HAPUS KANTONG DARI SESSION
========================= */
if (isset($_POST['form_action']) && $_POST['form_action'] == 'hapus_session') {
    $idx = isset($_POST['hapus_idx']) ? intval($_POST['hapus_idx']) : -1;

    if (isset($_SESSION['panel_proses']['items'][$idx])) {
        $hapus_nokantong = $_SESSION['panel_proses']['items'][$idx]['nokantong'];

        unset($_SESSION['panel_proses']['items'][$idx]);
        $_SESSION['panel_proses']['items'] = array_values($_SESSION['panel_proses']['items']);

        // // Audit trail
        // $log_mdl  = 'KOMPONEN';
        // $log_aksi = 'Menghapus kantong ' . $hapus_nokantong . ' dari daftar proses Permintaan Sampel Panel No Transaksi: ' . $_SESSION['panel_proses']['notrans'];
        // include("user_log.php");

        setFlash('success', 'Berhasil', 'Kantong dihapus dari daftar.');
    }

    header("Location: " . $redirect);
    exit;
}

/* =========================
   BATAL PROSES
========================= */
if (isset($_POST['form_action']) && $_POST['form_action'] == 'reset_proses') {
    if (isset($_SESSION['panel_proses']['notrans'])) {
        $log_mdl  = 'KOMPONEN';
        $log_aksi = 'Membatalkan proses Permintaan Sampel Panel No Transaksi: ' . $_SESSION['panel_proses']['notrans'];
        include("user_log.php");
    }

    unset($_SESSION['panel_proses']);
    // setFlash('success', 'Berhasil', 'Proses dibatalkan.');
    header("Location: " . $redirect);
    exit;
}

/* =========================
   SIMPAN DETAIL KE DATABASE
========================= */
if (isset($_POST['form_action']) && $_POST['form_action'] == 'simpan_detail') {
    if (!isset($_SESSION['panel_proses']['notrans'])) {
        setFlash('error', 'Gagal', 'Tidak ada proses aktif.');
        header("Location: " . $redirect);
        exit;
    }

    $notrans = esc($_SESSION['panel_proses']['notrans']);
    $items   = isset($_SESSION['panel_proses']['items']) ? $_SESSION['panel_proses']['items'] : array();

    if (count($items) == 0) {
        setFlash('error', 'Gagal', 'Belum ada kantong yang dimasukkan.');
        header("Location: " . $redirect);
        exit;
    }

    $targetReaktif   = isset($_SESSION['panel_proses']['reaktif_target']) ? (int)$_SESSION['panel_proses']['reaktif_target'] : 0;
    $targetNonreaktif = isset($_SESSION['panel_proses']['nonreaktif_target']) ? (int)$_SESSION['panel_proses']['nonreaktif_target'] : 0;

    $countReaktif = 0;
    $countNonreaktif = 0;
    for ($i = 0; $i < count($items); $i++) {
        if ($items[$i]['jenis_sampel'] == 'REAKTIF') {
            $countReaktif++;
        } else if ($items[$i]['jenis_sampel'] == 'NONREAKTIF') {
            $countNonreaktif++;
        }
    }

    if ($countReaktif != $targetReaktif || $countNonreaktif != $targetNonreaktif) {
        setFlash('error', 'Gagal', 'Jumlah kantong belum sesuai dengan permintaan.');
        header("Location: " . $redirect);
        exit;
    }

    mysql_query("START TRANSACTION");

    $sukses = true;
    for ($i = 0; $i < count($items); $i++) {
        $nokantong_sql = esc($items[$i]['nokantong']);
        $jenis_sql     = esc($items[$i]['jenis_sampel']);
        $ptgs_sql      = esc($namauser);

        $sqlInsert = "INSERT INTO {$tableDetail}
                        (notrans, nokantong, jenis_sampel, up_data, ptgs_komp, created)
                      VALUES
                        ('$notrans', '$nokantong_sql', '$jenis_sql', 0, '$ptgs_sql', NOW())";

        $qInsert = mysql_query($sqlInsert);
        if (!$qInsert) {
            $sukses = false;
            break;
        }
    }

    if ($sukses) {
        $header_id = isset($_SESSION['panel_proses']['header_id']) ? intval($_SESSION['panel_proses']['header_id']) : 0;
        if ($header_id > 0) {
            $sqlUpdateHeader = "UPDATE {$tableHeader} SET status = '1' WHERE {$pkField} = '$header_id'";
            mysql_query($sqlUpdateHeader);
        }

        mysql_query("COMMIT");

        // Audit trail
        $log_mdl  = 'KOMPONEN';
        $log_aksi = 'Menyimpan detail Permintaan Sampel Panel untuk No Transaksi: ' . $notrans;
        include("user_log.php");

        unset($_SESSION['panel_proses']);

        setFlash('success', 'Berhasil', 'Data detail berhasil disimpan.');
    } else {
        mysql_query("ROLLBACK");
        setFlash('error', 'Gagal', 'Gagal menyimpan detail: ' . mysql_error());
    }

    header("Location: " . $redirect);
    exit;
}

/* =========================
   QUERY DATA HEADER
========================= */
$query_sampel_panel = "SELECT * FROM {$tableHeader} WHERE status = 0 ORDER BY id DESC";
$result_sampel_panel = mysql_query($query_sampel_panel);
if (!$result_sampel_panel) {
    die(mysql_error());
}

/* =========================
   SESSION ACTIVE DATA
========================= */
$panel_active = isset($_SESSION['panel_proses']['notrans']) ? true : false;
$panel_items  = ($panel_active && isset($_SESSION['panel_proses']['items'])) ? $_SESSION['panel_proses']['items'] : array();
$targetReaktif = $panel_active ? (int)$_SESSION['panel_proses']['reaktif_target'] : 0;
$targetNonreaktif = $panel_active ? (int)$_SESSION['panel_proses']['nonreaktif_target'] : 0;

$countReaktif = 0;
$countNonreaktif = 0;
for ($i = 0; $i < count($panel_items); $i++) {
    if ($panel_items[$i]['jenis_sampel'] == 'REAKTIF') {
        $countReaktif++;
    } else if ($panel_items[$i]['jenis_sampel'] == 'NONREAKTIF') {
        $countNonreaktif++;
    }
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
    </style>
</head>

<body>

    <?php if ($panel_active) { ?>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Proses Permintaan Sampel Panel</h4>
            <form method="POST" action="" style="margin:0;">
                <input type="hidden" name="form_action" value="reset_proses">
                <button type="submit" class="btn btn-secondary">Batal Proses</button>
            </form>
        </div>

        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="alert alert-info mb-0">
                        <strong>No Transaksi:</strong><br>
                        <?php echo h($_SESSION['panel_proses']['notrans']); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning mb-0">
                        <strong>Jumlah Permintaan Kantong/Sampel Reaktif:</strong> <?php echo $targetReaktif; ?><br>
                        <strong>Terinput:</strong> <?php echo $countReaktif; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning mb-0">
                        <strong>Jumlah Permintaan Kantong/Sampel NonReaktif:</strong>
                        <?php echo $targetNonreaktif; ?><br>
                        <strong>Terinput:</strong> <?php echo $countNonreaktif; ?>
                    </div>
                </div>
            </div>

            <form method="POST" action="" class="row g-3 mb-4">
                <input type="hidden" name="form_action" value="tambah_kantong">

                <div class="col-md-8">
                    <label class="form-label">Nomor Kantong</label>
                    <input type="text" name="nokantong" id="nokantong" class="form-control"
                        placeholder="Scan / Input No Kantong" autocomplete="off" autofocus>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Tambah Kantong</button>
                </div>
            </form>

            <table id="detailTable" class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No Kantong</th>
                        <th>Produk</th>
                        <th>Jenis Sampel</th>
                        <th>Tanggal Input</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (count($panel_items) > 0) {
                            for ($i = 0; $i < count($panel_items); $i++) {
                        ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo h($panel_items[$i]['nokantong']); ?></td>
                        <td><?php echo h($panel_items[$i]['produk']); ?></td>
                        <td>
                            <?php if ($panel_items[$i]['jenis_sampel'] == 'REAKTIF') { ?>
                            <span class="badge bg-danger">REAKTIF</span>
                            <?php } else { ?>
                            <span class="badge bg-success">NONREAKTIF</span>
                            <?php } ?>
                        </td>
                        <td><?php echo h($panel_items[$i]['created']); ?></td>
                        <td>
                            <form method="POST" action="" style="margin:0;">
                                <input type="hidden" name="form_action" value="hapus_session">
                                <input type="hidden" name="hapus_idx" value="<?php echo $i; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                            }
                        }
                        ?>
                </tbody>
            </table>

            <form method="POST" action="" class="mt-3">
                <input type="hidden" name="form_action" value="simpan_detail">
                <button type="submit" class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>

    <?php } else { ?>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Permintaan Sampel Panel</h4>
            <button type="button" class="btn btn-primary" aria-label="Rekap"
                onclick="window.location.href='pmikasir2.php?module=rekap_sampel_panel'">
                Rekap
            </button>
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
                            <form method="POST" action="" style="margin:0;">
                                <input type="hidden" name="header_id" value="<?php echo h($rowId); ?>">
                                <button type="submit" name="submit" value="proses" class="btn btn-primary btn-sm">
                                    Proses
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
    });
    </script>

    <script>
    function fokusKantong() {
        setTimeout(function() {
            $('#nokantong').focus();
            $('#nokantong').select();
        }, 200);
    }

    $(document).ready(function() {
        fokusKantong();
    });
    </script>

    <?php if (!empty($flash['title'])) { ?>
    <script>
    Swal.fire({
        icon: '<?php echo $flash['icon']; ?>',
        title: '<?php echo addslashes($flash['title']); ?>',
        text: '<?php echo addslashes($flash['text']); ?>'
    }).then(function() {
        fokusKantong();
    });
    </script>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>