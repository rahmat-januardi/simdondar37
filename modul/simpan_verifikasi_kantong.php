<?php
header('Content-Type: text/plain');
session_start();
require_once('../config/dbi_connect.php');

// Debug (bisa dihapus nanti)
// file_put_contents('debug_verif.txt', date('Y-m-d H:i:s') . " - DIPANGGIL\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);

if (empty($_POST['no_kantong']) || !isset($_POST['submit_verif'])) {
    die('ERROR: Data tidak lengkap');
}

if (empty($_POST['tanggal_buka'])) {
    die('ERROR: Tanggal buka kemasan harus diisi!');
}

// Validasi format datetime (mendukung datetime-local)
$tanggal_buka_raw = trim($_POST['tanggal_buka']);

// Terima YYYY-MM-DDTHH:MM atau YYYY-MM-DD HH:MM:SS
if (!preg_match('/^\d{4}-\d{2}-\d{2}(T|\s)\d{2}:\d{2}(:\d{2})?$/', $tanggal_buka_raw)) {
    die('ERROR: Format tanggal buka tidak valid! Gunakan YYYY-MM-DD HH:MM');
}

// Normalisasi ke format MySQL (ganti T menjadi spasi + tambah detik jika belum ada)
$tanggal_buka_raw = str_replace('T', ' ', $tanggal_buka_raw);
if (strlen($tanggal_buka_raw) === 16) {
    $tanggal_buka_raw .= ':00';
}

// Tidak boleh di masa depan
if ($tanggal_buka_raw > date('Y-m-d H:i:s')) {
    die('ERROR: Tanggal buka kemasan tidak boleh di masa depan!');
}

$no_kantong = mysqli_real_escape_string($dbi, $_POST['no_kantong']);
$tanggal_buka = mysqli_real_escape_string($dbi, $tanggal_buka_raw);

// Flag untuk force re-verifikasi (dari JS)
$force_reverif = isset($_POST['force_reverif']) && $_POST['force_reverif'] == 1 ? true : false;

// 1. Ambil data kantong dari stokkantong
$cek_stok = mysqli_query($dbi, "SELECT * FROM stokkantong WHERE noKantong = '$no_kantong'");
if (mysqli_num_rows($cek_stok) == 0) {
    die("ERROR: Nomor kantong tidak ditemukan di stok!");
}
$stok = mysqli_fetch_assoc($cek_stok);

// Ambil lama_buka (dalam hari) dari master_kantong
$merk = mysqli_real_escape_string($dbi, $stok['merk']); // Sesuaikan kolom jika matchingnya beda
$cek_master = mysqli_query($dbi, "SELECT lama_buka FROM master_kantong WHERE merk = '$merk' LIMIT 1");
if (mysqli_num_rows($cek_master) == 0) {
    die("ERROR: Data master_kantong tidak ditemukan untuk merk/produk '$merk'!");
}
$master = mysqli_fetch_assoc($cek_master);
$lama_buka_hari = (int)$master['lama_buka']; // dalam hari

function getStatusText($stok)
{
    switch ($stok['Status']) {
        case '0':
            if ($stok['StatTempat'] == NULL || $stok['StatTempat'] == '0') return "Kosong Di Logistik";
            if ($stok['StatTempat'] == '1') return "Kosong Di Aftap";
            return "Kosong";
        case '1':
            if ($stok['sah'] == '1') return "Baru Isi / Karantina";
            return "Baru Isi / Karantina";
        case '2':
            if (substr($stok['stat2'], 0, 1) == 'b') return "Sehat (BDRS)";
            return "Sehat";
        case '3':
            return "Keluar Bawa / Titip";
        case '4':
            return "Rusak";
        case '5':
            return "Rusak / Gagal Aftap";
        case '6':
            return "Dimusnahkan";
        case '7':
            return "Reaktif";
        case '8':
            return "Darah Flebotomi";
        default:
            return "Status Tidak Dikenal ({$stok['Status']})";
    }
}

$status_text = getStatusText($stok);

// Hanya izinkan jika status kosong dan siap dipakai
if ($stok['Status'] != '0') {
    die("Kantong ini TIDAK DAPAT DIGUNAKAN!\n\nStatus saat ini: $status_text.\nTidak boleh digunakan untuk aftap.");
}

// 2. Cek apakah sudah pernah diverifikasi
$cek_verif = mysqli_query($dbi, "SELECT no_kantong, tanggal, tanggal_buka FROM verifikasi_kantong WHERE no_kantong = '$no_kantong'");
if (mysqli_num_rows($cek_verif) > 0) {
    $row = mysqli_fetch_assoc($cek_verif);

    // Gunakan tanggal_buka jika ada, fallback ke tanggal verifikasi pertama
    $ref_date_str = !empty($row['tanggal_buka']) ? $row['tanggal_buka'] : $row['tanggal'];
    $ref_date = new DateTime($ref_date_str);
    $now = new DateTime();

    // Hitung selisih hari (floor, tidak termasuk hari partial)
    $interval = $ref_date->diff($now);
    $selisih_hari = $interval->days; // jumlah hari penuh yang sudah lewat

    if ($selisih_hari > $lama_buka_hari) {
        // Kadaluarsa → update status rusak
        mysqli_query($dbi, "UPDATE stokkantong SET Status='4' WHERE noKantong='$no_kantong'");
        mysqli_query($dbi, "UPDATE verifikasi_kantong SET status_valid='0' WHERE no_kantong='$no_kantong'");
        die("Kantong ini SUDAH PERNAH diverifikasi (Tanggal buka kemasan: " . $ref_date->format('d-m-Y') .
            "). \nNamun kantong sudah KADALUWARSA (lebih dari $lama_buka_hari hari sejak buka kemasan).\nKantong TIDAK DAPAT DIGUNAKAN.");
    } else {
        // Masih dalam batas waktu
        if (!$force_reverif) {
            $sisa_hari = $lama_buka_hari - $selisih_hari;
            // PHP 5.3 compatibility: tidak bisa (new DateTime())->method(), harus pakai variabel
            $dt_verif_pertama = new DateTime($row['tanggal']);
            die("Kantong ini SUDAH PERNAH diverifikasi pada " . $dt_verif_pertama->format('d-m-Y') .
                ". \nTanggal buka kemasan: " . $ref_date->format('d-m-Y') .
                ". \nMasih dapat digunakan (sisa $sisa_hari hari dari batas $lama_buka_hari hari).");
        }
    }
}

// Jika belum pernah diverifikasi → lanjut simpan verifikasi baru
$kemasan_utuh     = isset($_POST['kemasan_utuh'])     ? (int)$_POST['kemasan_utuh']     : 0;
$kemasan_expired  = isset($_POST['kemasan_expired'])  ? (int)$_POST['kemasan_expired']  : 0;
$kemasan_bocor    = isset($_POST['kemasan_bocor'])    ? (int)$_POST['kemasan_bocor']    : 0;
$selang_baik      = isset($_POST['selang_baik'])      ? (int)$_POST['selang_baik']      : 0;
$selang_tertekuk  = isset($_POST['selang_tertekuk'])  ? (int)$_POST['selang_tertekuk']  : 0;
$jarum_baik       = isset($_POST['jarum_baik'])       ? (int)$_POST['jarum_baik']       : 0;
$jarum_bengkok    = isset($_POST['jarum_bengkok'])    ? (int)$_POST['jarum_bengkok']    : 0;
$anti_jernih      = isset($_POST['anti_jernih'])      ? (int)$_POST['anti_jernih']      : 0;
$anti_berubah     = isset($_POST['anti_berubah'])     ? (int)$_POST['anti_berubah']     : 0;

// Tentukan apakah valid
$is_valid = (
    $kemasan_utuh == 1 && $kemasan_expired == 1 && $kemasan_bocor == 1 &&
    $selang_baik == 1 && $selang_tertekuk == 0 &&
    $jarum_baik == 1 && $jarum_bengkok == 0 &&
    $anti_jernih == 1 && $anti_berubah == 0
) ? 1 : 0;

// Simpan / update verifikasi
// - tanggal   : waktu verifikasi PERTAMA (INSERT saja, tidak diupdate saat re-verif)
// - tanggal_update : waktu re-verifikasi (selalu diperbarui)
// - tanggal_buka   : tanggal buka kemasan (dipilih oleh petugas)
$sql = "INSERT INTO verifikasi_kantong 
        (no_kantong, kemasan_utuh, kemasan_expired, kemasan_bocor,
         selang_baik, selang_tertekuk, jarum_baik, jarum_bengkok,
         anti_jernih, anti_berubah, status_valid, tanggal, tanggal_update, tanggal_buka)
        VALUES 
        ('$no_kantong', $kemasan_utuh, $kemasan_expired, $kemasan_bocor,
         $selang_baik, $selang_tertekuk, $jarum_baik, $jarum_bengkok,
         $anti_jernih, $anti_berubah, $is_valid, NOW(), NOW(), '$tanggal_buka')
        ON DUPLICATE KEY UPDATE
         kemasan_utuh=$kemasan_utuh,
         kemasan_expired=$kemasan_expired,
         kemasan_bocor=$kemasan_bocor,
         selang_baik=$selang_baik,
         selang_tertekuk=$selang_tertekuk,
         jarum_baik=$jarum_baik,
         jarum_bengkok=$jarum_bengkok,
         anti_jernih=$anti_jernih,
         anti_berubah=$anti_berubah,
         status_valid=$is_valid,
         tanggal_buka='$tanggal_buka',
         tanggal_update=NOW()";

if (mysqli_query($dbi, $sql)) {
    if ($is_valid == 1) {
        echo "OK";
    } else {
        // Invalid → rusak
        mysqli_query($dbi, "UPDATE stokkantong SET Status='4' WHERE noKantong='$no_kantong'");
        echo "Kantong TIDAK DAPAT DIGUNAKAN karena ada parameter TIDAK BAIK.";
    }
} else {
    echo "ERROR DB: " . mysqli_error($dbi);
}
