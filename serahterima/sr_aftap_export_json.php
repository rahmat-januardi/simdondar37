<?php
session_start();
include '../config/dbi_connect.php';
header("Content-Type: application/json");

if (!isset($_GET['noTransaksi'])) {
    echo json_encode(array("status" => "error", "message" => "Parameter tidak lengkap"));
    exit;
}

$g_noserahterima = $_GET['noTransaksi'];

$arr_sr = array();
$arr_srd = array();
$arr_kantong = array();
$arr_ht = array();
$arr_pd = array();
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
    $base_nokantong = substr($no_kantong, 0, -1);
    $length_nokantong = strlen($no_kantong);
    //Data Kantong
    $qrykantong = mysqli_query($dbi, "SELECT * FROM `stokkantong` WHERE `noKantong` LIKE '$base_nokantong%' AND LENGTH(`noKantong`) = $length_nokantong;");
    while ($dtkantong = mysqli_fetch_assoc($qrykantong)) {
        $arr_kantong[] = $dtkantong;
    }
    //Data Pendonor
    $qrypd  = mysqli_query($dbi, "SELECT * FROM `pendonor` WHERE `Kode` = '$kodedonor'");
    while ($dtpd = mysqli_fetch_assoc($qrypd)) {
        $arr_pd[] = $dtpd;
    }
    //Data Htrans
    $qryht  = mysqli_query($dbi, "SELECT * FROM `htransaksi` WHERE `NoTrans` = '$htrans'");
    while ($dtht = mysqli_fetch_assoc($qryht)) {
        $arr_ht[] = $dtht;
    }
}
$arr_export = array(
    'notransaksi' => $g_noserahterima,
    'serahterima' => $arr_sr,
    'serahterimadetail' => $arr_srd,
    'stokkantong' => $arr_kantong,
    'pendonor' => $arr_pd,
    'htransaksi' => $arr_ht
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
