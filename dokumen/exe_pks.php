<?php
include 'koneksi.php';

// AMBIL DATA POST
$bidang = $_POST['bidang'];
$nama1 = $_POST['nama1'];
$nama2 = $_POST['nama2'];
$tingkat = $_POST['tingkat'];
$kontrol1 = $_POST['kontrol1'];
$kontrol2 = $_POST['kontrol2'];
$kontrol3 = $_POST['kontrol3'];
$kontrol4 = $_POST['kontrol4'];
$kontrol5 = $_POST['kontrol5'];
$kontrol6 = $_POST['kontrol6'];
$kontrol7 = $_POST['kontrol7'];
$kontrol8 = $_POST['kontrol8'];
$kontrol9 = $_POST['kontrol9'];
$tipe_dokumen = $_POST['tipe_dokumen'];
$periode = $_POST['periode'];
$no_versi = $_POST['no_versi'];
$tgl_setuju = $_POST['tgl_setuju'];
$tgl_pelaksanaan = $_POST['tgl_pelaksanaan'];
$tgl_peninjauan = $_POST['tgl_peninjauan'];
$pembuat = $_POST['pembuat'];
$pemeriksa = $_POST['pemeriksa'];
$pengesah = $_POST['pengesah'];
$pengesah2 = $_POST['pengesah2'];

// ================= FILE UPLOAD =================
$temp = 'upload/';
if (!file_exists($temp)) mkdir($temp, 0777, true);

$fileku_update = '';
if (!empty($_FILES['fileupload']['name'])) {
    $nama_asli = $_FILES['fileupload']['name'];
    $nama_bersih = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $nama_asli);
    $fileku_update = time() . "_" . $nama_bersih;
    if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $temp . $fileku_update)) {
        die("Gagal upload file!");
    }
}

// ================= KONTROL CONCAT =================
$cek_klausul = ($kontrol7 > 0) ? '-' . $kontrol7 . '-' : '-';

if ($tipe_dokumen === 'udd') {
    $kontrol_concat = $kontrol1;
    $kontrol2_concat = $kontrol1 . $kontrol2 . $kontrol3 . $kontrol4;
    $kontrol3_concat = $kontrol2 . $kontrol3;
} else {
    $kontrol_concat = $kontrol5;
    $kontrol2_concat = $kontrol5 . $kontrol6 . $cek_klausul . $kontrol8 . (!empty($kontrol9) ? '-' . $kontrol9 : '');
    $kontrol3_concat = $kontrol6 . $cek_klausul . $kontrol8;
}

// ================= TANGGAL NOTIF =================
$tgl2 = date('Y-m-d', strtotime('-60 days', strtotime($tgl_peninjauan)));

// ================= INSERT =================
$sql = "INSERT INTO pks 
    (bidang,nama1,nama2,tingkat,kontrol,kontrol2,kontrol3,
     periode,no_versi,tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,
     pembuat,pemeriksa,pengesah,pengesah2,tgl_notif";

if (!empty($fileku_update)) {
    $sql .= ",fileku";
}

$sql .= ") VALUES 
    ('$bidang','$nama1','$nama2','$tingkat','$kontrol_concat','$kontrol2_concat','$kontrol3_concat',
     '$periode','$no_versi','$tgl_setuju','$tgl_pelaksanaan','$tgl_peninjauan',
     '$pembuat','$pemeriksa','$pengesah','$pengesah2','$tgl2'";

if (!empty($fileku_update)) {
    $sql .= ",'$fileku_update'";
}

$sql .= ")";

$result = mysql_query($sql) or die("Gagal insert PKS: " . mysql_error());

if ($result) {
    echo "<script>alert('Data PKS berhasil di input'); location='pks.php';</script>";
    exit;
}