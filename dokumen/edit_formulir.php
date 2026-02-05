<?php
include "koneksi.php";

// ================== AMBIL DATA ==================
$nomor         = $_POST['nomor'];
$bidang        = $_POST['bidang'];
$nama1         = $_POST['nama1'];
$nama2         = $_POST['nama2'];
$tingkat       = $_POST['tingkat'];
$kontrol1      = $_POST['kontrol1'];
$kontrol2      = $_POST['kontrol2'];
$kontrol3      = $_POST['kontrol3'];
$periode       = $_POST['periode'];
$no_versi      = $_POST['no_versi'];
$tgl_setuju    = $_POST['tgl_setuju'];
$tgl_pelaksanaan = $_POST['tgl_pelaksanaan'];
$tgl_peninjauan  = $_POST['tgl_peninjauan'];
$pembuat       = $_POST['pembuat'];
$pemeriksa     = $_POST['pemeriksa'];
$pengesah      = $_POST['pengesah'];
$pengesah2     = $_POST['pengesah2'];
$terkait       = $_POST['terkait'];
$fileku        = $_POST['fileku']; // file lama

$tgl_notif = date('Y-m-d', strtotime('-60 days', strtotime($tgl_peninjauan)));

// ================== FILE UPLOAD ==================
$ImageName = $_FILES['fileupload']['name'];
$ImageTmp  = $_FILES['fileupload']['tmp_name'];
$namaFileFinal = $fileku; // default pakai file lama

if (!empty($ImageName)) {
  $temp = "upload/";
  if (!file_exists($temp)) mkdir($temp, 0777, true);

  // beri nama file unik
  $namaBersih = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $ImageName);
  $namaFileFinal = time() . "_" . $namaBersih;

  if (!move_uploaded_file($ImageTmp, $temp . $namaFileFinal)) {
    die("<script>alert('Gagal upload file!'); history.back();</script>");
  }
}

// ================== SIMPAN RIWAYAT ==================
$riwayat = "
INSERT INTO riwayat
(bidang,nama1,nama2,tingkat,kontrol,kontrol2,kontrol3,periode,no_versi,tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,pembuat,pemeriksa,pengesah,pengesah2,terkait,fileku)
VALUES
('$bidang','$nama1','$nama2','$tingkat','$kontrol1','$kontrol2','$kontrol3','$periode','$no_versi','$tgl_setuju','$tgl_pelaksanaan','$tgl_peninjauan','$pembuat','$pemeriksa','$pengesah','$pengesah2','$terkait','$namaFileFinal')
";

// ================== UPDATE FORMULIR ==================
$sql = "
UPDATE formulir SET 
  bidang='$bidang',
  nama1='$nama1',
  nama2='$nama2',
  tingkat='$tingkat',
  kontrol='$kontrol1',
  kontrol2='$kontrol2',
  kontrol3='$kontrol3',
  periode='$periode',
  no_versi='$no_versi',
  tgl_setuju='$tgl_setuju',
  tgl_pelaksanaan='$tgl_pelaksanaan',
  tgl_peninjauan='$tgl_peninjauan',
  pembuat='$pembuat',
  pemeriksa='$pemeriksa',
  pengesah='$pengesah',
  pengesah2='$pengesah2',
  tgl_notif='$tgl_notif',
  fileku='$namaFileFinal'
WHERE nomor='$nomor'
";

// ================== EKSEKUSI ==================
$result  = mysql_query($sql) or die("Gagal update: " . mysql_error());
$result2 = mysql_query($riwayat) or die("Gagal simpan riwayat: " . mysql_error());

// ================== FEEDBACK ==================
if ($result && $result2) {
  if (!empty($ImageName)) {
    echo "<script>alert('Data dan file berhasil disimpan.'); location='formulir.php'</script>";
  } else {
    echo "<script>alert('Data berhasil disimpan tanpa mengubah file.'); location='formulir.php'</script>";
  }
} else {
  echo "<script>alert('Gagal menyimpan data!'); history.back();</script>";
}