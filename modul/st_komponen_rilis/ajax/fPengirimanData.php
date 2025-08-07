<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/dbi_connect.php';
date_default_timezone_set('Asia/Jakarta');

$hariini = date("Y-m-d H:i:s");
$jamSekarang = date('H:i');

if ($jamSekarang >= '08:00' && $jamSekarang <= '14:00') {
    $shiftST = 'PAGI';
} elseif ($jamSekarang >= '14:01' && $jamSekarang <= '21:00') {
    $shiftST = 'SORE';
} elseif ($jamSekarang >= '21:01' || $jamSekarang == '00:00') {
    $shiftST = 'MALAM1';
} elseif ($jamSekarang >= '00:01' && $jamSekarang <= '07:59') {
    $shiftST = 'MALAM2';
} else {
    $shiftST = 'TIDAK DIKETAHUI';
}

$petugas = isset($_POST['dst_us']) ? $_POST['dst_us'] : '';
$noTrans = isset($_POST['dst_nt']) ? $_POST['dst_nt'] : '';
$statReceive = 1;
$dataSuhu = isset($_POST['suhu_pengiriman']) ? $_POST['suhu_pengiriman'] : '-';

if (!$petugas || !$noTrans) {
    http_response_code(400);
    echo json_encode(array('status' => 'notok', 'message' => 'Data tidak lengkap.'));
    exit;
}

// php 5.3++
$dbi->autocommit(false);
try {
    // php 5.5.0++
    // $dbi->begin_transaction();

    // UPDATE 1 - serahterima
    $upd1 = $dbi->prepare("UPDATE serahterima SET hst_penerima = ?, hts_tgl_terima = ?, hst_suhuterima = ?, hst_shift_penerima = ? WHERE hst_notrans = ?");
    if (!$upd1) {
        throw new Exception("Gagal prepare update serahterima: " . $dbi->error);
    }

    $upd1->bind_param('sssss', $petugas, $hariini, $dataSuhu, $shiftST, $noTrans);
    if (!$upd1->execute()) {
        throw new Exception("Gagal execute update serahterima: " . $upd1->error);
    }
    $upd1->close();

    // UPDATE 2 - serahterima_detail
    $upd2 = $dbi->prepare("UPDATE serahterima_detail SET dst_receive1 = ?, dst_stat_receive1 = ?, dst_date_receive1 = ?, dst_shift_receive1 = ? WHERE dst_notrans = ? AND dst_sah = 1");
    if (!$upd2) {
        throw new Exception("Gagal prepare update serahterima_detail: " . $dbi->error);
    }

    $upd2->bind_param('sisss', $petugas, $statReceive, $hariini, $shiftST, $noTrans);
    if (!$upd2->execute()) {
        throw new Exception("Gagal execute update serahterima_detail: " . $upd2->error);
    }
    $upd2->close();

    // SELECT dst_nokantong dari serahterima_detail
    $selectKantong = $dbi->prepare("SELECT dst_nokantong FROM serahterima_detail WHERE dst_notrans = ? AND dst_sah = 1");
    if (!$selectKantong) {
        throw new Exception("Gagal prepare select dst_nokantong: " . $dbi->error);
    }
    $selectKantong->bind_param('s', $noTrans);
    if (!$selectKantong->execute()) {
        throw new Exception("Gagal execute select dst_nokantong: " . $selectKantong->error);
    }

    // $resultKantong = $selectKantong->get_result();
    $selectKantong->store_result();
    $selectKantong->bind_result($noKantong);

    if ($selectKantong->num_rows > 0) {
        // while ($row = $resultKantong->fetch_assoc()) {
            // $noKantong = $row['dst_nokantong'];

        while ($selectKantong->fetch()) {

            // UPDATE stokkantong position_bag = 'RILIS' berdasarkan noKantong
            //$upd3 = $dbi->prepare("UPDATE stokkantong SET position_bag = 'RILIS' WHERE noKantong = ? AND (position_bag = 'KOMPONEN' OR position_bag IS NULL)");
            $upd3 = $dbi->prepare("UPDATE stokkantong SET position_bag = 3 WHERE noKantong = ? AND (position_bag = 2 OR position_bag IS NULL)");
            if (!$upd3) {
                throw new Exception("Gagal prepare update stokkantong: " . $dbi->error);
            }
            $upd3->bind_param('s', $noKantong);
            if (!$upd3->execute()) {
                throw new Exception("Gagal execute update stokkantong: " . $upd3->error);
            }
            $upd3->close();
        }
    } else {
        throw new Exception("Tidak ada nomor kantong yang ditemukan untuk transaksi ini. notrans: " . $noTrans);
    }
    $selectKantong->close();

    // DELETE - hapus temp
    // $del1 = $dbi->prepare("DELETE FROM serahterima_detail_tmp WHERE kode_transaksi = ?");
    // if (!$del1) {
    //     throw new Exception("Gagal prepare delete temp: " . $dbi->error);
    // }

    // $del1->bind_param('s', $noTrans);
    // if (!$del1->execute()) {
    //     throw new Exception("Gagal execute delete temp: " . $del1->error);
    // }
    // $del1->close();

    // Commit jika semua berhasil
    $dbi->commit();

    echo json_encode(array('status' => 'ok', 'message' => 'Berhasil finalisasi dan hapus data temp.'));
    error_log("Finalisasi berhasil untuk transaksi $noTrans oleh $petugas");
} catch (Exception $e) {
    $dbi->rollback();
    error_log("Finalisasi gagal: " . $e->getMessage());
    echo json_encode(array('status' => 'notok', 'message' => $e->getMessage()));
}

// php 5.3++
$dbi->autocommit(true);

