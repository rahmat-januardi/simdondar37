<?php
include 'koneksi.php';

// ================== AMBIL DATA POST ==================
$terkait    = $_POST['terkait'];
$bidang     = $_POST['bidang'];
$nama1      = $_POST['nama1'];
$nama2      = $_POST['nama2'];
$tingkat    = $_POST['tingkat'];
$kontrol1   = $_POST['kontrol1'];
$kontrol2   = $_POST['kontrol2'];
$kontrol3   = $_POST['kontrol3'];
$kontrol4   = $_POST['kontrol4'];
$kontrol5   = $_POST['kontrol5'];
$kontrol6   = $_POST['kontrol6'];
$kontrol7   = $_POST['kontrol7'];
$kontrol8   = $_POST['kontrol8'];
$kontrol9   = $_POST['kontrol9'];
$tipe_dokumen = $_POST['tipe_dokumen'];
$periode    = $_POST['periode'];
$no_versi   = $_POST['no_versi'];
$tgl_setuju = $_POST['tgl_setuju'];
$tgl_pelaksanaan = $_POST['tgl_pelaksanaan'];
$tgl_peninjauan  = $_POST['tgl_peninjauan'];
$pembuat    = $_POST['pembuat'];
$pemeriksa  = $_POST['pemeriksa'];
$pengesah   = $_POST['pengesah'];
$pengesah2  = $_POST['pengesah2'];

// ================== HITUNG TGL NOTIF ==================
$tgl2 = date('Y-m-d', strtotime('-60 days', strtotime($tgl_peninjauan)));
$cek_klausul = ($kontrol7 > 0) ? '-' . $kontrol7 . '-' : '-';

// ================== FILE UPLOAD ==================
$temp = "upload/";
if (!file_exists($temp)) mkdir($temp, 0777, true);

$fileku = '';
if (!empty($_FILES['fileupload']['name'])) {
    $nama_asli = $_FILES['fileupload']['name'];
    $nama_bersih = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $nama_asli);
    $fileku = time() . "_" . $nama_bersih;

    if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $temp . $fileku)) {
        die("Gagal upload file!");
    }
}

// ================== SUSUN SQL ==================
if ($tipe_dokumen === 'udd') {
    $kontrol_concat  = $kontrol1 . $kontrol2 . $kontrol3 . $kontrol4;
    $kontrol2_concat = $kontrol2 . $kontrol5 . $kontrol3;
} else {
    $kontrol_concat  = $kontrol5 . $kontrol6 . $cek_klausul . $kontrol8 . '-' . $kontrol9;
    $kontrol2_concat = $kontrol6 . $cek_klausul . $kontrol8 . '-';
}

$sql = "INSERT INTO ik
        (terkait,bidang,nama1,nama2,tingkat,kontrol,kontrol2,kontrol3,
         periode,no_versi,tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,
         pembuat,pemeriksa,pengesah,pengesah2,tgl_notif,fileku)
        VALUES
        ('$terkait','$bidang','$nama1','$nama2','$tingkat',
         '$kontrol1','$kontrol_concat','$kontrol2_concat',
         '$periode','$no_versi','$tgl_setuju','$tgl_pelaksanaan','$tgl_peninjauan',
         '$pembuat','$pemeriksa','$pengesah','$pengesah2','$tgl2','" . ($fileku != '' ? $fileku : '') . "')";

// ================== MASTER NOMOR ==================
if ($tipe_dokumen === 'udd') {
    $master_nomor_sql = "INSERT INTO master_nomorik(bidang,nama1,kontrol2)
                         VALUES ('$bidang','$nama1','$kontrol_concat')";
} else {
    $master_nomor_sql = "INSERT INTO master_nomorik(bidang,nama1,kontrol2)
                         VALUES ('$bidang','$nama1','$kontrol_concat')";
}

// ================== EKSEKUSI ==================
mysql_query($sql) or die("Gagal insert IK: " . mysql_error());
mysql_query($master_nomor_sql) or die("Gagal insert master_nomorik: " . mysql_error());

echo "<script>alert('Data IK berhasil di input'); location='ik.php';</script>";
exit;