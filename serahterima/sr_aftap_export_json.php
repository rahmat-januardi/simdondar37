<?php
session_start();
include '../config/dbi_connect.php';
header("Content-Type: application/json");

if (!isset($_GET['noTransaksi'])) {
    echo json_encode(array("status" => "error", "message" => "Parameter tidak lengkap"));
    exit;
}

$g_noserahterima = $_GET['noTransaksi'];
$g_udd_asal      = isset($_GET['uddAsal'])     ? trim($_GET['uddAsal'])     : '';
$g_udd_penerima  = isset($_GET['uddPenerima'])  ? trim($_GET['uddPenerima']) : '';

// ── Simpan hst_udd_asal & hst_udd_penerima ke tabel serahterima ──────────────
if ($g_udd_asal !== '' || $g_udd_penerima !== '') {
    $udd_asal_esc    = mysqli_real_escape_string($dbi, $g_udd_asal);
    $udd_penerima_esc = mysqli_real_escape_string($dbi, $g_udd_penerima);
    $notrans_esc     = mysqli_real_escape_string($dbi, $g_noserahterima);

    mysqli_query($dbi, "UPDATE `serahterima`
                        SET `hst_dariudd`     = '$udd_asal_esc',
                            `hst_keudd` = '$udd_penerima_esc'
                        WHERE `hst_notrans`    = '$notrans_esc'");
}
// ─────────────────────────────────────────────────────────────────────────────

$arr_sr      = array();
$arr_srd     = array();
$arr_kantong = array();
$arr_ht      = array();
$arr_pd      = array();
$arr_usl     = array();

$qry_sr_head = mysqli_query($dbi, "SELECT * FROM `serahterima` WHERE `hst_notrans`='$g_noserahterima'");
$arr_sr = mysqli_fetch_assoc($qry_sr_head);
if (!$arr_sr) {
    $arr_sr = array();
}

$qry_sr_detail = mysqli_query($dbi, "SELECT * FROM `serahterima_detail` WHERE `dst_notrans`='$g_noserahterima'");
while ($row = mysqli_fetch_assoc($qry_sr_detail)) {
    $arr_srd[] = $row;
    $no_kantong = $row['dst_nokantong'];
    $kodedonor  = $row['dst_kodedonor'];
    $htrans     = $row['dst_no_aftap'];
    $base_nokantong   = substr($no_kantong, 0, -1);
    $length_nokantong = strlen($no_kantong);

    // Data Kantong
    $qrykantong = mysqli_query($dbi, "SELECT * FROM `stokkantong`
                                      WHERE `noKantong` LIKE '$base_nokantong%'
                                        AND LENGTH(`noKantong`) = $length_nokantong;");
    while ($dtkantong = mysqli_fetch_assoc($qrykantong)) {
        $arr_kantong[] = $dtkantong;
    }

    // Data Pendonor
    $qrypd = mysqli_query($dbi, "SELECT * FROM `pendonor` WHERE `Kode` = '$kodedonor'");
    while ($dtpd = mysqli_fetch_assoc($qrypd)) {
        $arr_pd[] = $dtpd;
    }

    // Data Htransaksi
    $qryht = mysqli_query($dbi, "SELECT * FROM `htransaksi` WHERE `NoTrans` = '$htrans'");
    while ($dtht = mysqli_fetch_assoc($qryht)) {
        $arr_ht[] = $dtht;
    }

    // Data Userlog
    $qryusl = mysqli_query($dbi, "SELECT * FROM `user_log` WHERE `aksi_user` LIKE '%$no_kantong%'");
    while ($dtusl = mysqli_fetch_assoc($qryusl)) {
        $arr_usl[] = $dtusl;
    }
}

$arr_export = array(
    'notransaksi'       => $g_noserahterima,
    'udd_asal'          => $g_udd_asal,
    'udd_penerima'      => $g_udd_penerima,
    'serahterima'       => $arr_sr,
    'serahterimadetail' => $arr_srd,
    'stokkantong'       => $arr_kantong,
    'pendonor'          => $arr_pd,
    'htransaksi'        => $arr_ht,
    'user_log'          => $arr_usl
);

$json_data = json_encode($arr_export);
if (!$json_data) {
    echo json_encode(array("status" => "error", "message" => "Gagal mengonversi data ke JSON"));
    exit;
}

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="export_data_' . $g_noserahterima . '.json"');
echo $json_data;
exit;
