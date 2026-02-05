<?php
include "koneksi.php";

// ================== AMBIL DATA POST ==================
$nama_ubah     = $_POST['nama_ubah'];
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

// RIWAYAT
$bidang_riwayat    = $_POST['bidang_riwayat'];
$nama1_riwayat     = $_POST['nama1_riwayat'];
$nama2_riwayat     = $_POST['nama2_riwayat'];
$tingkat_riwayat   = $_POST['tingkat_riwayat'];
$kontrol2_riwayat  = $_POST['kontrol2_riwayat'];
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

$fileku_update = ''; // default = tidak ada perubahan
if (!empty($_FILES['fileupload']['name'])) {
	$nama_asli = $_FILES['fileupload']['name'];
	$nama_bersih = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $nama_asli);
	$fileku_update = time() . "_" . $nama_bersih;

	if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $temp . $fileku_update)) {
		die("Gagal upload file!");
	}
}

// ================== UPDATE PKS ==================
$sql = "UPDATE pks SET 
        bidang='$bidang',
        nama1='$nama1',
        nama2='$nama2',
        tingkat='$tingkat',
        kontrol2='$kontrol2',
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

if (!empty($fileku_update)) {
	$sql .= ", fileku='$fileku_update'";
}

$sql .= " WHERE nomor='$nomor'";

$result = mysql_query($sql) or die("Gagal update PKS: " . mysql_error());

// ================== UPDATE RIWAYAT ==================
$riwayat = "INSERT INTO riwayat
            (bidang,nama1,nama2,tingkat,kontrol2,periode,no_versi,
             tgl_setuju,tgl_pelaksanaan,tgl_peninjauan,pembuat,pemeriksa,
             pengesah,pengesah2,terkait,fileku)
            VALUES
            ('$bidang_riwayat','$nama1_riwayat','$nama2_riwayat','$tingkat_riwayat',
             '$kontrol2_riwayat','$periode_riwayat','$versi_riwayat',
             '$setuju_riwayat','$pelaksanaan_riwayat','$peninjauan_riwayat',
             '$pembuat_riwayat','$pemeriksa_riwayat','$pengesah_riwayat',
             '$pengesah_riwayat2','$nama1_riwayat','" . ($fileku_update != '' ? $fileku_update : '') . "')";

$result7 = mysql_query($riwayat) or die("Gagal insert riwayat: " . mysql_error());

// ================== UPDATE TERKAIT DI TABLE LAIN ==================
$related_tables = array('ik', 'ika', 'formulir', 'pendukung', 'eksternal');
foreach ($related_tables as $tbl) {
	mysql_query("UPDATE $tbl SET terkait='$nama1' WHERE terkait='$nama_ubah'");
}

echo "<script>alert('Update PKS berhasil'); location='pks.php';</script>";
exit;