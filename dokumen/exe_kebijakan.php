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
$kontrol5 = !empty($_POST['kontrol5']) ? $_POST['kontrol5'] : '';
$kontrol6 = !empty($_POST['kontrol6']) ? $_POST['kontrol6'] : '';
$kontrol7 = !empty($_POST['kontrol7']) ? $_POST['kontrol7'] : '';
$kontrol8 = !empty($_POST['kontrol8']) ? $_POST['kontrol8'] : '';
$kontrol9 = !empty($_POST['kontrol9']) ? $_POST['kontrol9'] : '';
$tipe_dokumen = $_POST['tipe_dokumen'];
$periode       = $_POST['periode'];
$no_versi      = $_POST['no_versi'];
$tgl_setuju    = $_POST['tgl_setuju'];
$tgl_pelaksanaan = $_POST['tgl_pelaksanaan'];
$tgl_peninjauan  = $_POST['tgl_peninjauan'];
$pembuat       = $_POST['pembuat'];
$pemeriksa     = $_POST['pemeriksa'];
$pengesah      = $_POST['pengesah'];
$pengesah2     = $_POST['pengesah2'];

// file lama (untuk edit)
$file_lama = isset($_POST['file_lama']) ? $_POST['file_lama'] : '';

// SUSUN NOKONTROL
$cek_klausul = ($kontrol7 > 0) ? '-' . $kontrol7 . '-' : '-';
if ($tipe_dokumen != 'udd') {
    $nokontrol = !empty($kontrol9) ? $kontrol5 . $kontrol6 . $cek_klausul . $kontrol8 . '-' . $kontrol9
        : $kontrol5 . $kontrol6 . $cek_klausul . $kontrol8;
} else {
    $nokontrol = $kontrol1 . $kontrol2 . $kontrol3 . $kontrol4;
}

// TGL NOTIF
$tgl2 = date('Y-m-d', strtotime('-60 days', strtotime($tgl_peninjauan)));

// UPLOAD FILE
if (isset($_POST['upload'])) {
    $temp = 'upload/';
    if (!file_exists($temp)) mkdir($temp, 0777, true);

    $fileupload = $file_lama; // default = file lama

    if (!empty($_FILES['fileupload']['name'])) {

        // hapus file lama jika ada
        if (!empty($file_lama) && file_exists($temp . $file_lama)) {
            unlink($temp . $file_lama);
        }

        $nama_asli = $_FILES['fileupload']['name'];
        $nama_bersih = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $nama_asli);
        $fileupload = time() . '_' . $nama_bersih;

        if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $temp . $fileupload)) {
            die("Gagal upload file!");
        }
    }

    // INSERT menggunakan mysql_*
    $sql = "INSERT INTO kebijakan
        (bidang,nama1,nama2,tingkat,kontrol,periode,no_versi,
        tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,
        pembuat,pemeriksa,pengesah,pengesah2,
        tgl_notif,fileku)
        VALUES
        ('$bidang','$nama1','$nama2','$tingkat','$nokontrol',
        '$periode','$no_versi','$tgl_setuju','$tgl_pelaksanaan','$tgl_peninjauan',
        '$pembuat','$pemeriksa','$pengesah','$pengesah2',
        '$tgl2','$fileupload')";

    $result = mysql_query($sql) or die("Gagal insert: " . mysql_error());

    echo "<script>alert('Berhasil disimpan'); location='kebijakan.php';</script>";
    exit;
}