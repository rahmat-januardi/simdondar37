<?php
include "koneksi.php";

$today = date("Y-m-d H:i:s");
$bidang = $_POST['bidang'];
$nama1 = $_POST['nama1'];
$kontrol2 = $_POST['kontrol2'];
$no_versi = $_POST['no_versi'];
$tujuan1 = $_POST['tujuan1'];
$jumlah1 = $_POST['jumlah1'];
$pengantar1 = $_POST['pengantar1'];
$tgl_keluar1 = $_POST['tgl_keluar1'];
$petugas1 = $_POST['petugas1'];
$penerima = $_POST['penerima'];
$ImageName       = $_FILES['fileupload']['name'];

// upload file
$fileku = "";
if (!empty($_FILES['fileku']['name'])) {
  $fileku = time() . "_" . $_FILES['fileku']['name'];
  move_uploaded_file($_FILES['fileku']['tmp_name'], "upload/" . $fileku);
}


$riwayat = "insert into riwayat_keluar
(bidang,nama1,kontrol2,no_versi,tujuan,jumlah,nomor_pengantar,tgl_keluar,petugas,fileku,penerima,on_insert) values
('$bidang','$nama1','$kontrol2','$no_versi','$tujuan1','$jumlah1','$pengantar1','$tgl_keluar1','$petugas1','$fileku','$penerima','$today')";

$riwayat2 = "insert into dokumen_keluar
(bidang,nama1,kontrol2,no_versi,tujuan,jumlah,nomor_pengantar,tgl_keluar,petugas,fileku,penerima,on_insert) values
('$bidang','$nama1','$kontrol2','$no_versi','$tujuan1','$jumlah1','$pengantar1','$tgl_keluar1','$petugas1','$fileku','$penerima','$today')";


$result1 = mysql_query($riwayat);
$result2 = mysql_query($riwayat2);


if (!$result1) {
  die("Data riwayat gagal diinsert : " . mysql_error());
}
if (!$result2) {
  die("Data dokumen keluar gagal diinsert : " . mysql_error());
}


if ($result1 && $result2) {
  echo "<script>
        alert('Dokumen berhasil disimpan');
        window.location.href = 'keluar_ika.php?detail=$kontrol2';
    </script>";
}