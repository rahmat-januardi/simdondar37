<?php
include 'koneksi.php';

// ================== AMBIL DATA POST ==================
$terkait   = $_POST['terkait'];
$bidang    = $_POST['bidang'];
$nama1     = $_POST['nama1'];
$nama2     = $_POST['nama2'];
$tingkat   = $_POST['tingkat'];
$kontrol1  = $_POST['kontrol1'];
$kontrol2  = $_POST['kontrol2'];
$kontrol3  = $_POST['kontrol3'];
$kontrol4  = $_POST['kontrol4'];
$kontrol5  = $_POST['kontrol5'];
$kontrol6  = $_POST['kontrol6'];
$kontrol7  = $_POST['kontrol7'];
$kontrol8  = $_POST['kontrol8'];
$kontrol9  = $_POST['kontrol9'];
$tipe_dokumen = $_POST['tipe_dokumen'];
$periode   = $_POST['periode'];
$no_versi  = $_POST['no_versi'];
$tgl_pelaksanaan = $_POST['tgl_pelaksanaan'];
$tgl_peninjauan  = $_POST['tgl_peninjauan'];

// ================== FILE UPLOAD ==================
$temp = 'upload/';
if (!file_exists($temp)) mkdir($temp, 0777, true);

$ImageName = '';
if (!empty($_FILES['fileupload']['name'])) {
    $nama_asli = $_FILES['fileupload']['name'];
    $nama_bersih = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $nama_asli);
    $ImageName = time() . "_" . $nama_bersih;

    if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $temp . $ImageName)) {
        die("Gagal upload file!");
    }
}

// ================== CEK KLAUSUL ==================
$cek_klausul = ($kontrol7 > 0) ? '-' . $kontrol7 . '-' : '-';

// ================== HITUNG TGL_NOTIF ==================
$tgl_notif = date('Y-m-d', strtotime('-60 days', strtotime($tgl_peninjauan)));

// ================== INSERT FORMULIR ==================
if ($tipe_dokumen === 'udd') {
    $sql = "INSERT INTO formulir
        (terkait,bidang,nama1,nama2,tingkat,kontrol,kontrol2,kontrol3,
         periode,no_versi,tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,
         pembuat,pemeriksa,pengesah,pengesah2,tgl_notif,fileku)
        VALUES
        ('$terkait','$bidang','$nama1','$nama2','$tingkat','$kontrol1',
         CONCAT('$kontrol1','$kontrol2','$kontrol3','$kontrol4'),
         CONCAT('$kontrol2','$kontrol5','$kontrol3'),
         '$periode','$no_versi','-','$tgl_pelaksanaan','$tgl_peninjauan',
         '-','-','-','-','$tgl_notif','$ImageName')";

    $master_nomor = mysql_query("INSERT INTO master_nomor_formulir
        (bidang,nama1,kontrol2)
        VALUES
        ('$bidang','$nama1',CONCAT('$kontrol1','$kontrol2','$kontrol3','$kontrol4'))");
} else {
    $sql = "INSERT INTO formulir
        (terkait,bidang,nama1,nama2,tingkat,kontrol,kontrol2,kontrol3,
         periode,no_versi,tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,
         pembuat,pemeriksa,pengesah,pengesah2,tgl_notif,fileku)
        VALUES
        ('$terkait','$bidang','$nama1','$nama2','$tingkat','$kontrol5',
         CONCAT('$kontrol5','$kontrol6','$cek_klausul','$kontrol8','-','$kontrol9'),
         CONCAT('$kontrol6','$cek_klausul','$kontrol8','-'),
         '$periode','$no_versi','-','$tgl_pelaksanaan','$tgl_peninjauan',
         '-','-','-','-','$tgl_notif','$ImageName')";

    $master_nomor = mysql_query("INSERT INTO master_nomor_formulir
        (bidang,nama1,kontrol2)
        VALUES
        ('$bidang','$nama1',CONCAT('$kontrol5','$kontrol6','$cek_klausul','$kontrol8','-','$kontrol9'))");
}

// ================== EKSEKUSI ==================
$result = mysql_query($sql) or die("Gagal insert formulir: " . mysql_error());

if ($result) {
    include 'formulir.php';
    echo "Dokumen Formulir berhasil diinput";
} else {
    echo "ERROR";
}