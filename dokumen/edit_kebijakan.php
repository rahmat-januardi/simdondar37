<?php
include "koneksi.php";

// AMBIL DATA POST
$nomor = $_POST['nomor'];
$bidang = $_POST['bidang'];
$nama1 = $_POST['nama1'];
$nama2 = $_POST['nama2'];
$tingkat = $_POST['tingkat'];
$kontrol = $_POST['kontrol'];
$periode = $_POST['periode'];
$no_versi = $_POST['no_versi'];
$tgl_setuju = $_POST['tgl_setuju'];
$tgl_pelaksanaan = $_POST['tgl_pelaksanaan'];
$tgl_peninjauan = $_POST['tgl_peninjauan'];
$pembuat = $_POST['pembuat'];
$pemeriksa = $_POST['pemeriksa'];
$pengesah = $_POST['pengesah'];
$pengesah2 = $_POST['pengesah2'];

// Riwayat
$bidang_riwayat = $_POST['bidang_riwayat'];
$nama1_riwayat = $_POST['nama1_riwayat'];
$nama2_riwayat = $_POST['nama2_riwayat'];
$tingkat_riwayat = $_POST['tingkat_riwayat'];
$kontrol_riwayat = $_POST['kontrol_riwayat'];
$periode_riwayat = $_POST['periode_riwayat'];
$versi_riwayat = $_POST['versi_riwayat'];
$setuju_riwayat = $_POST['setuju_riwayat'];
$pelaksanaan_riwayat = $_POST['pelaksanaan_riwayat'];
$peninjauan_riwayat = $_POST['peninjauan_riwayat'];
$pembuat_riwayat = $_POST['pembuat_riwayat'];
$pemeriksa_riwayat = $_POST['pemeriksa_riwayat'];
$pengesah_riwayat = $_POST['pengesah_riwayat'];
$pengesah_riwayat2 = $_POST['pengesah_riwayat2'];

$tgl2 = date('Y-m-d', strtotime('-60 days', strtotime($tgl_peninjauan)));

// ================= UPLOAD FILE =================
$temp = "upload/";
if (!file_exists($temp)) mkdir($temp, 0777, true);

$fileku_update = ''; // hanya diisi jika ada file baru

if (!empty($_FILES['fileupload']['name'])) {
	$file_lama = isset($_POST['fileku']) ? $_POST['fileku'] : '';

	// hapus file lama jika ada
	if (!empty($file_lama) && file_exists($temp . $file_lama)) {
		unlink($temp . $file_lama);
	}

	// sanitize nama file
	$nama_asli = $_FILES['fileupload']['name'];
	$nama_bersih = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $nama_asli);
	$fileku_update = time() . "_" . $nama_bersih;

	// upload file baru
	if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $temp . $fileku_update)) {
		die("Gagal upload file!");
	}
}

// ================= UPDATE DATABASE =================
$sql = "UPDATE kebijakan SET 
    bidang='$bidang',
    nama1='$nama1',
    nama2='$nama2',
    tingkat='$tingkat',
    kontrol='$kontrol',
    periode='$periode',
    no_versi='$no_versi',
    tgl_setuju='$tgl_setuju',
    tgl_pelaksanaan='$tgl_pelaksanaan',
    tgl_peninjauan='$tgl_peninjauan',
    pembuat='$pembuat',
    pemeriksa='$pemeriksa',
    pengesah='$pengesah',
    pengesah2='$pengesah2',
    tgl_notif='$tgl2'";

// update kolom fileku hanya jika ada file baru
if (!empty($fileku_update)) {
	$sql .= ", fileku='$fileku_update'";
}

$sql .= " WHERE nomor='$nomor'";

$result = mysql_query($sql) or die("Gagal update: " . mysql_error());

// ================== INSERT RIWAYAT ==================
$riwayat = "INSERT INTO riwayat 
    (bidang,nama1,nama2,tingkat,kontrol,periode,no_versi,tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,pembuat,pemeriksa,pengesah,pengesah2,terkait,fileku)
    VALUES
    ('$bidang_riwayat','$nama1_riwayat','$nama2_riwayat','$tingkat_riwayat','$kontrol_riwayat','$periode_riwayat','$versi_riwayat','$setuju_riwayat','$pelaksanaan_riwayat','$peninjauan_riwayat','$pembuat_riwayat','$pemeriksa_riwayat','$pengesah_riwayat','$pengesah_riwayat2','$nama1_riwayat','$fileupload')";
$result7 = mysql_query($riwayat) or die("Gagal insert riwayat: " . mysql_error());

// ================== UPDATE TERKAIT RIWAYAT ==================
$riwayat2 = "UPDATE riwayat SET terkait='$nama1' WHERE terkait='$nama1_riwayat'";
$result8 = mysql_query($riwayat2) or die("Gagal update riwayat terkait: " . mysql_error());

if ($result) {
	echo "<script>alert('Dokumen Kebijakan berhasil di edit'); location='kebijakan.php';</script>";
	exit;
} else {
	die("ERROR update kebijakan: " . mysql_error());
}