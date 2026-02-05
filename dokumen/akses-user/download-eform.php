<?php
session_start();
include "../koneksi.php";

$namauser = $_SESSION['nama_lengkap'];
$today = date('YmdHis');
$notrans = $today . "-" . $namauser;
$level = $_SESSION['bagian'];

if (isset($_GET['file'])) {
    if (isset($_GET['edokumen']) || $_GET['edokumen'] == 'ya') {
        $file = '../upload/' . $_GET['file']; // amankan jalur file bila perlu
    } else {
        $file = '../' . $_GET['file']; // amankan jalur file bila perlu
    }

    if (file_exists($file)) {
        $filename = $_GET['file'];
        $tambah = mysql_query("insert into lacakdokumen (notrans, nama_pengakses, level_pengakses, tanggal_akses, nama_dokumen, keterangan) values ('$notrans', '$namauser', '$level', '$today', '$filename', '$namauser melakukan aktivitas DOWNLOAD, dokumen: $filename')");

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        echo "File tidak ditemukan.";
    }
}