<?php
file_put_contents('log_sdata.txt', print_r($_POST, true), FILE_APPEND);

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/dbi_connect.php';
require_once '../config/anuu.php';

date_default_timezone_set('Asia/Jakarta');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dst_us = isset($_POST['dst_us']) ? $_POST['dst_us'] : '';
    $dst_nt = isset($_POST['dst_nt']) ? $_POST['dst_nt'] : '';
    $hariini = date('Y-m-d H:i:s');
    $bagPenerima = "RILIS";
    $jenisKirim = "Kantong (Produk Darah)";
    $penerima = "";
    $penerima2 = "";
    $penerima3 = "";
    $tglTerima = null;
    $suhuTerima = "";
    $peruntukan = "POSTING HASIL PENGOLAHAN DARAH";
    $modul = "KOMPONEN";
    $shift_penerima = "-";

    if (!empty($dst_nt)) {
        $stmt1 = $dbi->prepare("SELECT dst_asal, dst_kodealat, dst_keadaan FROM serahterima_detail_tmp WHERE dst_notrans = ? LIMIT 1");
        // $stmt1 = $dbi->prepare("SELECT `dst_asal`, `dst_no_aftap`, `dst_tglaftap`, `dst_kodealat`, `dst_suhu`, `dst_keadaan`, `dst_notrans`, `dst_nokantong`, `dst_produk`, `dst_statusktg`, `dst_old_position`, `dst_sahktg`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_kodedonor`, `dst_berat`, `dst_volumektg`, `dst_jenisktg`, `dst_sample`, `dst_sah`, `dst_modul`, `dst_user`, `dst_dsdp`, `dst_lamabaru`, `dst_umur`, `dst_lama_aftap`, `dst_statuspengambilan`, `dst_kel`, `dst_ptgaftap`, `dst_volambil`, `dst_shift_pengirim` FROM serahterima_detail_tmp WHERE hst_notrans = ? LIMIT 1");
        $stmt1->bind_param('s', $dst_nt);
        $stmt1->execute();
        $stmt1->bind_result($bagpengirim, $kodealat, $kondisiumum);

        if ($stmt1->fetch()) {
            $stmt1->close();

            $stmt2 = $dbi->prepare("INSERT INTO serahterima (
                hst_notrans, hst_bagpengirim, hst_bagpenerima, hst_tgl, hst_asal, hst_jenis_st,
                hst_user, hst_pengirim, hst_penerima, hst_penerima2, hst_penerima3, hts_tgl_terima,
                hst_kode_alat, hst_suhuterima, hst_kondisiumum, hst_peruntukan, hst_modul, hst_shift_pengirim, hst_shift_penerima
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            anuu($stmt2, "Prepare");

            $stmt2->bind_param(
                'sssssssssssssssssss',
                $dst_nt,
                $bagpengirim,
                $bagPenerima,
                $hariini,
                $bagpengirim,
                $jenisKirim,
                $dst_us,
                $dst_us,
                $penerima,
                $penerima2,
                $penerima3,
                $tglTerima,
                $kodealat,
                $suhuTerima,
                $kondisiumum,
                $peruntukan,
                $modul,
                $shiftST,
                $shift_penerima
            );

            anuu($stmt2, "Bind");

            if ($stmt2->execute()) {
                anuu($stmt2, "Execute");

                $stmt2->close();

                $stmt3 = $dbi->prepare("SELECT 
                `dst_asal`, `dst_no_aftap`, `dst_tglaftap`, `dst_kodealat`, `dst_suhu`, `dst_keadaan`, `dst_notrans`, `dst_nokantong`, `dst_produk`, 
                `dst_statusktg`, `dst_old_position`, `dst_sahktg`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_kodedonor`, `dst_berat`, `dst_volumektg`, 
                `dst_jenisktg`, `dst_sample`, `dst_sah`, `dst_modul`, `dst_user`, `dst_dsdp`, `dst_lamabaru`, `dst_umur`, `dst_lama_aftap`, 
                `dst_statuspengambilan`, `dst_kel`, `dst_ptgaftap`, `dst_volambil`, `dst_shift_pengirim` 
                FROM serahterima_detail_tmp 
                WHERE dst_notrans = ?");
                $stmt3->bind_param('s', $dst_nt);
                $stmt3->execute();
                $stmt3->bind_result($asal, $no_aftap, $tgl_aftap, $kodealat, $suhu, $keadaan, $dst_notrans, $dst_nokantong, $produk, $statusktg, $old_position, $sahktg, $merk, $golda, $rh, $kodedonor, $berat, $volumektg, $jenisktg, $sampleST, $sahST, $modulST, $user_st, $dsdpST, $lama_baruST, $umurST, $lama_aftapST, $statuspengambilanST, $kelST, $petugas_aftapST, $volambilST, $shift_pengirimST);

                $dataStmt3 = array();
                while ($stmt3->fetch()) {
                    $dataStmt3[] = array(
                        'asal' => $asal,
                        'no_aftap' => $no_aftap,
                        'tgl_aftap' => $tgl_aftap,
                        'kodealat' => $kodealat,
                        'suhu' => $suhu,
                        'keadaan' => $keadaan,
                        'dst_notrans' => $dst_notrans,
                        'dst_nokantong' => $dst_nokantong,
                        'produk' => $produk,
                        'statusktg' => $statusktg,
                        'old_position' => $old_position,
                        'sahktg' => $sahktg,
                        'merk' => $merk,
                        'golda' => $golda,
                        'rh' => $rh,
                        'kodedonor' => $kodedonor,
                        'berat' => $berat,
                        'volumektg' => $volumektg,
                        'jenisktg' => $jenisktg,
                        'sampleST' => $sampleST,
                        'sahST' => $sahST,
                        'modulST' => $modulST,
                        'user_st' => $user_st,
                        'dsdpST' => $dsdpST,
                        'lama_baruST' => $lama_baruST,
                        'umurST' => $umurST,
                        'lama_aftapST' => $lama_aftapST,
                        'statuspengambilanST' => $statuspengambilanST,
                        'kelST' => $kelST,
                        'petugas_aftapST' => $petugas_aftapST,
                        'volambilST' => $volambilST
                    );
                }
                
                // Debugging
                // error_log("Data yang diterima: " . print_r($dataStmt3, true));

                $stmt3->close();
                $stmt4 = $dbi->prepare("INSERT INTO serahterima_detail (
                    `dst_no_aftap`, `dst_tglaftap`, `dst_notrans`, `dst_nokantong`, `dst_produk`, `dst_statusktg`, `st_statusktg_new`, `dst_old_position`, `dst_new_position`, 
                    `dst_sahktg`, `dst_sahktg_new`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_kodedonor`, `dst_berat`, `dst_volumektg`, `dst_jenisktg`, 
                    `dst_sample`, `dst_sah`, `dst_dsdp`, `dst_lamabaru`, `dst_umur`, `dst_lama_aftap`, `dst_statuspengambilan`, `dst_kel`, `dst_ptgaftap`, 
                    `dst_volambil`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");
                // if (!$stmt4) {
                //     error_log("Prepare failed: (" . $dbi->errno . ") " . $dbi->error);
                // }

                foreach ($dataStmt3 as $row) {
                    $selStokk = $dbi->prepare("SELECT `Status`, `sah`, `position` FROM stokkantong WHERE noKantong = ?");
                    if (!$selStokk) {
                        error_log("Prepare failed: (" . $dbi->errno . ") " . $dbi->error);
                    }
                    $selStokk->bind_param('s', $row['dst_nokantong']);
                    $selStokk->execute();
                    $selStokk->bind_result($statusktg_new, $sahktg_new, $new_position);
                    if ($selStokk->errno) {
                        error_log("Execute failed: (" . $selStokk->errno . ") " . $selStokk->error);
                    }
                    $selStokk->fetch();
                    $selStokk->close();

                    $no_aftap = $row['no_aftap'];
                    $tgl_aftap = $row['tgl_aftap'];
                    $dst_notrans = $row['dst_notrans'];
                    $dst_nokantong = $row['dst_nokantong'];
                    $produk = $row['produk'];
                    $statusktg = $row['statusktg'];
                    $old_position = $row['old_position'];
                    $sahktg = $row['sahktg'];
                    $merk = $row['merk'];
                    $golda = $row['golda'];
                    $rh = $row['rh'];
                    $kodedonor = $row['kodedonor'];
                    $berat = $row['berat'];
                    $volumektg = $row['volumektg'];
                    $jenisktg = $row['jenisktg'];
                    $sampleST = $row['sampleST'];

                    $stmt4->bind_param(
                        'ssssssssssssssssssssssssssss',
                        $no_aftap,
                        $tgl_aftap,
                        $dst_notrans,
                        $dst_nokantong,
                        $produk,
                        $statusktg,
                        $statusktg_new,
                        $old_position,
                        $new_position,
                        $sahktg,
                        $sahktg_new,
                        $merk,
                        $golda,
                        $rh,
                        $kodedonor,
                        $berat,
                        $volumektg,
                        $jenisktg,
                        $sampleST,
                        $sahST,
                        $dsdpST,
                        $lama_baruST,
                        $umurST,
                        $lama_aftapST,
                        $statuspengambilanST,
                        $kelST,
                        $petugas_aftapST,
                        $volambilST
                    );
                    if ($stmt4->errno) {
                        error_log("Bind Param Error: (" . $stmt4->errno . ") " . $stmt4->error);
                    }
                    if ($stmt4->execute()) {
                        anuu($stmt4, "Execute");
                    } else {
                        error_log("Execute failed: (" . $stmt4->errno . ") " . $stmt4->error);
                    }
                }
                $stmt4->close();
                $stmt5 = $dbi->prepare("DELETE FROM serahterima_detail_tmp WHERE dst_user = ?");
                if ($stmt5) {
                    $stmt5->bind_param('s', $dst_us);
                    if ($stmt5->execute()) {
                        anuu($stmt5, "Execute");
                    } else {
                        error_log("Execute failed: (" . $stmt5->errno . ") " . $stmt5->error);
                    }
                    $stmt5->close();
                } else {
                    error_log("Prepare failed: (" . $dbi->errno . ") " . $dbi->error);
                }

                echo json_encode(array('status' => 'success', 'message' => 'Data berhasil disimpan'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Gagal menyimpan ke serahterima<br><b style="font-size:16px;">' . htmlspecialchars($dbi->error) . '</b>'));
            }

        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Data tidak ditemukan di serahterima_detail_tmp' . $dbi->error));
        }

    } else {
        echo json_encode(array('status' => 'error', 'message' => 'No. Transaksi kosong' . $dbi->error));
    }

} else {
    echo json_encode(array('status' => 'error', 'message' => 'Metode tidak valid' . $dbi->error));
}

$dbi->close();
