<?php
include "koneksi.php";

// ================== AMBIL DATA POST ==================
$nomor      = $_POST['nomor'];
$bidang     = $_POST['bidang'];
$nama1      = $_POST['nama1'];
$nama2      = $_POST['nama2'];
$tingkat    = $_POST['tingkat'];
$kontrol1   = $_POST['kontrol1'];
$kontrol2   = $_POST['kontrol2'];
$kontrol3   = $_POST['kontrol3'];
$periode    = $_POST['periode'];
$no_versi   = $_POST['no_versi'];
$tgl_setuju     = $_POST['tgl_setuju'];
$tgl_pelaksanaan = $_POST['tgl_pelaksanaan'];
$tgl_peninjauan = $_POST['tgl_peninjauan'];
$pembuat  = $_POST['pembuat'];
$pemeriksa = $_POST['pemeriksa'];
$pengesah = $_POST['pengesah'];
$pengesah2 = $_POST['pengesah2'];
$nama_ubah = $_POST['nama_ubah']; // untuk riwayat terkait

// ================== DATA RIWAYAT ==================
$bidang_riwayat    = $_POST['bidang_riwayat'];
$nama1_riwayat     = $_POST['nama1_riwayat'];
$nama2_riwayat     = $_POST['nama2_riwayat'];
$tingkat_riwayat   = $_POST['tingkat_riwayat'];
$kontrol_riwayat   = $_POST['kontrol_riwayat'];
$kontrol2_riwayat  = $_POST['kontrol2_riwayat'];
$kontrol3_riwayat  = $_POST['kontrol3_riwayat'];
$periode_riwayat   = $_POST['periode_riwayat'];
$versi_riwayat     = $_POST['versi_riwayat'];
$setuju_riwayat    = $_POST['setuju_riwayat'];
$pelaksanaan_riwayat = $_POST['pelaksanaan_riwayat'];
$peninjauan_riwayat  = $_POST['peninjauan_riwayat'];
$pembuat_riwayat     = $_POST['pembuat_riwayat'];
$pemeriksa_riwayat   = $_POST['pemeriksa_riwayat'];
$pengesah_riwayat    = $_POST['pengesah_riwayat'];
$pengesah_riwayat2   = $_POST['pengesah_riwayat2'];

// ================== HITUNG TGL NOTIF ==================
$tgl2 = date('Y-m-d', strtotime('-60 days', strtotime($tgl_peninjauan)));

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

// ================== INSERT RIWAYAT ==================
$riwayat_sql = "INSERT INTO riwayat
    (bidang,nama1,nama2,tingkat,kontrol,kontrol2,kontrol3,periode,no_versi,
     tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,pembuat,pemeriksa,pengesah,pengesah2,
     terkait,fileku)
    VALUES
    ('$bidang_riwayat','$nama1_riwayat','$nama2_riwayat','$tingkat_riwayat',
     '$kontrol_riwayat','$kontrol2_riwayat','$kontrol3_riwayat','$periode_riwayat','$versi_riwayat',
     '$setuju_riwayat','$pelaksanaan_riwayat','$peninjauan_riwayat','$pembuat_riwayat',
     '$pemeriksa_riwayat','$pengesah_riwayat','$pengesah_riwayat2','$nama1_riwayat',
     '" . ($fileku != '' ? $fileku : '') . "')";

mysql_query($riwayat_sql) or die("Gagal input riwayat: " . mysql_error());

// ================== UPDATE RIWAYAT TERKAIT ==================
mysql_query("UPDATE riwayat SET terkait='$nama1' WHERE terkait='$nama_ubah'")
	or die("Gagal update riwayat terkait: " . mysql_error());

// ================== UPDATE IKA ==================
$sql = "UPDATE ika SET
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
        tgl_notif='$tgl2'
        " . ($fileku != '' ? ", fileku='$fileku'" : "") . "
        WHERE nomor='$nomor'";

mysql_query($sql) or die("Gagal update IKA: " . mysql_error());

echo "<script>alert('Update IKA berhasil'); location='ika.php';</script>";
exit;