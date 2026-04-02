<?php
session_start();
require_once('config/koneksi.php');

$no_kantong = mysql_real_escape_string($_POST['no_kantong']);

$cek = mysql_query("SELECT vk.status_verifikasi, vk.tgl_verifikasi, vk.tanggal_buka, u.nama_lengkap AS petugas
                           FROM verifikasi_kantong vk
                           LEFT JOIN user u ON vk.petugas_verifikasi = u.id_user
                           WHERE vk.no_kantong = '$no_kantong'");

if (mysql_num_rows($cek) > 0) {
    $row = mysql_fetch_assoc($cek);
    echo json_encode([
        'status'       => $row['status_verifikasi'],
        'tanggal'      => $row['tgl_verifikasi'],
        'tanggal_buka' => $row['tanggal_buka'],
        'petugas'      => $row['petugas']
    ]);
} else {
    echo json_encode(['status' => 'BELUM_DIVERIFIKASI']);
}
