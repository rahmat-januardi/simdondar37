<?php
header('Content-Type: text/plain');
session_start();
require_once('../config/dbi_connect.php');

// Debug
// file_put_contents('debug_verif.txt', date('Y-m-d H:i:s') . " - DIPANGGIL\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);

if (empty($_POST['no_kantong']) || !isset($_POST['submit_verif'])) {
    die('ERROR: Data tidak lengkap');
}

$no_kantong = mysqli_real_escape_string($dbi, $_POST['no_kantong']);

// 1. Cek status kantong di stokkantong
$cek_stok = mysqli_query($dbi, "SELECT Status FROM stokkantong WHERE noKantong = '$no_kantong'");
if (mysqli_num_rows($cek_stok) == 0) {
    die("ERROR: Nomor kantong tidak ditemukan di stok!");
}

$stok = mysqli_fetch_assoc($cek_stok);
function getStatusText($stok)
{
    switch ($stok['Status']) {
        case '0':
            if ($stok['StatTempat'] == NULL || $stok['StatTempat'] == '0') return "Kosong Di Logistik";
            if ($stok['StatTempat'] == '1') return "Kosong Di Aftap";
            return "Kosong";
        case '1':
            if ($stok['sah'] == '1') return "Baru Isi / Karantina";
            return "Sedang Aftap";
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
            return "Status Tidak Dikenal ($stok[Status])";
    }
}

$status_text = getStatusText($stok);

// Hanya izinkan jika status benar-benar kosong dan siap dipakai (Status = 0 dan di Aftap/Logistik)
if ($stok['Status'] != '0') {
    die("Kantong ini TIDAK DAPAT DIGUNAKAN!\nStatus saat ini: $status_text\nTidak boleh digunakan untuk aftap.");
}

// 2. Cek apakah kantong ini SUDAH PERNAH diverifikasi sebelumnya
$cek_verif = mysqli_query($dbi, "SELECT no_kantong, tanggal FROM verifikasi_kantong WHERE no_kantong = '$no_kantong'");
if (mysqli_num_rows($cek_verif) > 0) {
    $row = mysqli_fetch_assoc($cek_verif);
    die("Kantong ini SUDAH PERNAH diverifikasi pada " . date('d-m-Y H:i', strtotime($row['tanggal'])) .
        "\nTetapi masih boleh digunakan karena belum diproses aftap.");
}

// Paksa semua nilai jadi 0 dulu, baru yang ada di POST jadi 1
$kemasan_utuh     = isset($_POST['kemasan_utuh']) ? (int)$_POST['kemasan_utuh'] : 0;
$kemasan_expired  = isset($_POST['kemasan_expired']) ? (int)$_POST['kemasan_expired'] : 0;
$kemasan_bocor    = isset($_POST['kemasan_bocor']) ? (int)$_POST['kemasan_bocor'] : 0;
$selang_baik      = isset($_POST['selang_baik']) ? (int)$_POST['selang_baik'] : 0;
$selang_tertekuk  = isset($_POST['selang_tertekuk']) ? (int)$_POST['selang_tertekuk'] : 0;
$jarum_baik       = isset($_POST['jarum_baik']) ? (int)$_POST['jarum_baik'] : 0;
$jarum_bengkok    = isset($_POST['jarum_bengkok']) ? (int)$_POST['jarum_bengkok'] : 0;
$anti_jernih      = isset($_POST['anti_jernih']) ? (int)$_POST['anti_jernih'] : 0;
$anti_berubah     = isset($_POST['anti_berubah']) ? (int)$_POST['anti_berubah'] : 0;

// Tentukan apakah valid
$is_valid = (
    $kemasan_utuh == 1 && $kemasan_expired == 1 && $kemasan_bocor == 1 &&  // Kemasan harus semua baik
    $selang_baik == 1 && $selang_tertekuk == 0 &&                         // Selang baik, tidak tertekuk
    $jarum_baik == 1 && $jarum_bengkok == 0 &&                            // Jarum baik, tidak bengkok
    $anti_jernih == 1 && $anti_berubah == 0                               // Anti jernih, tidak berubah
) ? 1 : 0;

// Query simpan verifikasi (tambah valid)
$sql = "INSERT INTO verifikasi_kantong 
        (no_kantong, kemasan_utuh, kemasan_expired, kemasan_bocor,
         selang_baik, selang_tertekuk, jarum_baik, jarum_bengkok,
         anti_jernih, anti_berubah, status_valid, tanggal)
        VALUES 
        ('$no_kantong', $kemasan_utuh, $kemasan_expired, $kemasan_bocor,
         $selang_baik, $selang_tertekuk, $jarum_baik, $jarum_bengkok,
         $anti_jernih, $anti_berubah, $is_valid, NOW())
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
         tanggal=NOW()";

if (mysqli_query($dbi, $sql)) {
    if ($is_valid == 1) {
        echo "OK";
    } else {
        // Jika invalid, update stokkantong agar tidak bisa dipakai (Status=5, seperti gagal)
        $update_stok = "UPDATE stokkantong SET Status='4' WHERE noKantong='$no_kantong'";
        mysqli_query($dbi, $update_stok);
        echo "INVALID: Verifikasi disimpan, tapi kantong TIDAK DAPAT DIGUNAKAN karena ada parameter kurang bagus.";
    }
} else {
    echo "ERROR DB: " . mysqli_error($dbi);
}
