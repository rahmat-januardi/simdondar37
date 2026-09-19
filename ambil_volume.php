<?php

/***********************************************
 * Fungsi : Ambil volume kantong otomatis (AJAX)
 * Catatan penting:
 *   stokkantong.volumeasal = volume NOMINAL kantong (350/450),
 *   dipakai untuk filter master_kantong.vol supaya berat_ku
 *   yang diambil sesuai ukuran kantong (merk+jenis saja tidak
 *   cukup karena bisa ada beberapa vol untuk merk+jenis yang sama).
 *
 * Prioritas hasil:
 *   1. timbang_darah.berat_ukur terbaru -> dihitung ke ml
 *      pakai formula sama seperti qa_release.php, dengan
 *      berat_ku yang sudah difilter sesuai volumeasal
 *   2. stokkantong.volumeasal (fallback jika timbang_darah kosong
 *      atau berat_ku tidak ketemu)
 * Dipanggil dari sr_komponen.php via fetch/AJAX
 * berdasarkan nomor kantong yang diketik user.
 ***********************************************/
require_once('config/db_connect.php');
header('Content-Type: application/json');

$nokantong = isset($_GET['nokantong']) ? trim($_GET['nokantong']) : '';
$response  = array('volume' => '', 'sumber' => '', 'pesan' => '');

if ($nokantong === '') {
    $response['pesan'] = 'Nomor kantong kosong';
    echo json_encode($response);
    exit;
}

$nokantong_esc = mysql_real_escape_string($nokantong);

// Data stokkantong: merk, jenis, dan volumeasal (volume nominal 350/450)
$q1   = mysql_query("SELECT volumeasal, merk, jenis FROM stokkantong WHERE noKantong='$nokantong_esc' LIMIT 1");
$stok = ($q1 && mysql_num_rows($q1) > 0) ? mysql_fetch_assoc($q1) : null;

// 1) Coba hitung dari timbang_darah.berat_ukur terbaru
$q2 = mysql_query("SELECT berat_ukur FROM timbang_darah WHERE nokantong='$nokantong_esc' ORDER BY waktu DESC LIMIT 1");
$tb = ($q2 && mysql_num_rows($q2) > 0) ? mysql_fetch_assoc($q2) : null;

if ($tb && $stok && !empty($stok['volumeasal'])) {
    $merk        = $stok['merk'];
    $jenis       = $stok['jenis'];
    $volumeasal_esc = mysql_real_escape_string($stok['volumeasal']);

    // filter tambahan `vol` = volumeasal, supaya tidak salah ambil
    // baris master_kantong ketika merk+jenis sama tapi vol beda
    //
    // Catatan: `antikoagulant` DIPISAH dari bkantong (tidak ikut dijumlah
    // di sini) karena antikoagulan bukan darah, sehingga tidak boleh ikut
    // dikonversi memakai BJ darah (1.055). Lihat penjelasan rumus di bawah.
    $msktg = mysql_fetch_assoc(mysql_query(
        "SELECT (`berat_ku`+`berat_s1`+`berat_s2`+`berat_s3`+`berat_s4`+`berat_s5`+`berat_s6`+`berat_s7`) AS bkantong,
                `antikoagulant` AS antikoagulant
         FROM `master_kantong`
         WHERE `merk`='$merk' AND `jenis`='$jenis' AND `vol`='$volumeasal_esc' LIMIT 1"
    ));
    $bktg          = isset($msktg['bkantong'])      ? round($msktg['bkantong'], 0)      : 0;
    $antikoagulant = isset($msktg['antikoagulant']) ? (float) $msktg['antikoagulant']   : 0;

    if ($bktg > 0) {
        $volcc = (($tb['berat_ukur'] - $bktg) / 1.055) - $antikoagulant;
        $response['volume'] = round($volcc, 0);
        $response['sumber'] = 'Timbangan darah';
    }
}

// 2) Fallback: stokkantong.volumeasal jika timbang_darah tidak ada/gagal dihitung
if ($response['volume'] === '' && $stok && !empty($stok['volumeasal']) && $stok['volumeasal'] > 0) {
    $response['volume'] = round($stok['volumeasal'], 0);
    $response['sumber'] = 'Volume asal kantong';
}

if ($response['volume'] === '') {
    $response['pesan'] = 'Data volume tidak ditemukan untuk kantong ini';
}

echo json_encode($response);