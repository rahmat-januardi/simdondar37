<?php
require_once("../../config/dbi_connect.php");
// require_once("../config/dbi_connect.php");
$dbi->set_charset('utf8');

$response = array('success' => false, 'message' => '');

$noTrans = isset($_POST['NoTrans']) ? $_POST['NoTrans'] : '';
$kPetugas = isset($_POST['petugas']) ? $_POST['petugas'] : '';

$utd = mysqli_fetch_array(mysqli_query($dbi, "SELECT * from utd where `aktif`=1"));
$idudd = $utd['id'];

try {
    // Mulai transaksi
    //$dbi->begin_transaction();
    $dbi->query("START TRANSACTION");

    $update_sql = "UPDATE dpengolahan_temp SET 
    Produk = ?, 
    ed_produk = ?, 
    pcepat = ?, 
    psuhu = ?, 
    volume = ?, 
    metode = ?, 
    bstatus = ?, 
    bsuhu = ? 
    WHERE noKantong = ? AND petugas = ?";

    if ($stmt = $dbi->prepare($update_sql)) {

        // Ambil data dari POST
        $noKantong = $_POST['nK'];
        $produk = $_POST['produk'];
        $edProduk = $_POST['ed_produk'];
        $pCepat = $_POST['pcepat'];
        $pSuhu = $_POST['psuhu'];
        $berat = $_POST['berat'];
        $volume = $_POST['volume'];
        $metode = $_POST['metode'];
        $bStatus = isset($_POST['bstatus']) ? $_POST['bstatus'] : array();
        // $bStatus = $_POST['bstatus'];
        $bSuhu = $_POST['bsuhu'];

        // Loop dan eksekusi statement untuk setiap kantong
        foreach ($noKantong as $index => $value) {
            // Periksa dan sanitasi data
            $produk[$index] = (string) $produk[$index];
            $edProduk[$index] = (string) $edProduk[$index];
            $pCepat[$index] = (int) $pCepat[$index];
            $pSuhu[$index] = (int) $pSuhu[$index];
            $volume[$index] = (float) $volume[$index];
            $metode[$index] = (string) $metode[$index];
            $bStatus[$index] = isset($bStatus[$index]) ? (int) $bStatus[$index] : 0;
            // $bStatus[$index] = (int)$bStatus[$index];
            $bSuhu[$index] = (string) $bSuhu[$index];
            $value = (string) $value; // noKantong

            // Bind parameter sesuai dengan tipe data
            $stmt->bind_param(
                "ssiiidisss",
                $produk[$index],
                $edProduk[$index],
                $pCepat[$index],
                $pSuhu[$index],
                $volume[$index],
                $metode[$index],
                $bStatus[$index],
                $bSuhu[$index],
                $value,
                $kPetugas
            );

            if (!$stmt->execute()) {
                throw new Exception("Error pada Update dpengolahan_temp: " . $stmt->error);
            }
        }

        // logging pada error log php
        // error_log("POST Data: " . print_r($_POST, true));

        // Cek apakah nokantong ada pada timbang_darah
        $cekTimbang_sql = "SELECT nokantong FROM timbang_darah WHERE nokantong = ?";
        if ($stmtCekTimbang = $dbi->prepare($cekTimbang_sql)) {
            if (session_id() == '') {
                session_start();
            }

            $logTimbangSql = "INSERT INTO `user_log`(`time_aksi`,`komputer`, `user`, `modul`, `aksi_user`, `keterangan`, `tempat`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtLogTimbang = $dbi->prepare($logTimbangSql);
            if (!$stmtLogTimbang) {
                throw new Exception("Gagal menyiapkan statement untuk user_log timbang_darah: " . $dbi->error);
            }

            $maxTrans_sql = "SELECT MAX(notrans) as maxTrans FROM timbang_darah";
            $maxTransResult = mysqli_query($dbi, $maxTrans_sql);
            $maxTransRow = mysqli_fetch_assoc($maxTransResult);
            $maxTransValue = isset($maxTransRow['maxTrans']) ? (int) $maxTransRow['maxTrans'] : 0;
            $nextTransTimbang = $maxTransValue + 1;

            foreach ($noKantong as $index => $value) {
                $nokantong = (string) $value;
                $beratUkur = isset($berat[$index]) ? (float) $berat[$index] : 0.0;

                $stmtCekTimbang->bind_param("s", $nokantong);
                $stmtCekTimbang->execute();
                $result = $stmtCekTimbang->get_result();

                if ($result->num_rows > 0) {
                    // jika ada, update datanya
                    $updateTimbang_sql = "UPDATE timbang_darah SET berat_ukur = ? WHERE nokantong = ?";
                    if ($stmtUpdateTimbang = $dbi->prepare($updateTimbang_sql)) {
                        $stmtUpdateTimbang->bind_param("ds", $beratUkur, $nokantong);
                        if (!$stmtUpdateTimbang->execute()) {
                            throw new Exception("Gagal mengupdate berat pada timbang_darah untuk nokantong: $nokantong. Error: " . $stmtUpdateTimbang->error);
                        }

                        $time_aksi = date('Y-m-d H:i:s');
                        $clip = isset($_SESSION['client_ip']) ? $_SESSION['client_ip'] : '';
                        $nmus = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';
                        $aksi_timbang = 'UPDATE TIMBANG DARAH';
                        $keterangan_timbang = 'No.Kantong: ' . $nokantong . ', berat_ukur: ' . $beratUkur;
                        $tempat_timbang = 'DG';
                        $stmtLogTimbang->bind_param('sssssss', $time_aksi, $clip, $nmus, 'PENGOLAHAN', $aksi_timbang, $keterangan_timbang, $tempat_timbang);
                        if (!$stmtLogTimbang->execute()) {
                            throw new Exception("Gagal mencatat user_log update timbang_darah untuk nokantong: $nokantong");
                        }

                        $stmtUpdateTimbang->close();
                    } else {
                        throw new Exception("Gagal menyiapkan statement untuk update timbang_darah: " . $dbi->error);
                    }
                } else {
                    // jika tidak ada, tambahkan ke tabel timbang_darah
                    $timbang_sql = "INSERT INTO timbang_darah (kode_alat, user, bagian, nokantong, berat_ukur, konfirm, waktu_konfirm, notrans) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($stmtTimbang = $dbi->prepare($timbang_sql)) {
                        $kodeAlat = 'Timbangan Darah';
                        $user = $kPetugas;
                        $bagian = 'Penyediaan darah';
                        $konfirm = 1; // Asumsikan konfirmasi selalu 1
                        $waktuKonfirm = date('Y-m-d H:i:s');
                        $notrans_timbang = $nextTransTimbang++;

                        $stmtTimbang->bind_param("ssssssss", $kodeAlat, $user, $bagian, $nokantong, $beratUkur, $konfirm, $waktuKonfirm, $notrans_timbang);
                        if (!$stmtTimbang->execute()) {
                            throw new Exception("Gagal menambahkan berat pada timbang_darah untuk nokantong: $nokantong. Error: " . $stmtTimbang->error);
                        }

                        $time_aksi = date('Y-m-d H:i:s');
                        $clip = isset($_SESSION['client_ip']) ? $_SESSION['client_ip'] : '';
                        $nmus = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';
                        $aksi_timbang = 'CREATE TIMBANG DARAH';
                        $keterangan_timbang = 'No.Kantong: ' . $nokantong . ', berat_ukur: ' . $beratUkur;
                        $tempat_timbang = 'DG';
                        $stmtLogTimbang->bind_param('sssssss', $time_aksi, $clip, $nmus, 'PENGOLAHAN', $aksi_timbang, $keterangan_timbang, $tempat_timbang);
                        if (!$stmtLogTimbang->execute()) {
                            throw new Exception("Gagal mencatat user_log create timbang_darah untuk nokantong: $nokantong");
                        }

                        $stmtTimbang->close();
                    } else {
                        throw new Exception("Gagal menyiapkan statement untuk insert timbang_darah: " . $dbi->error);
                    }
                }

                $result->free();
            }
            $stmtLogTimbang->close();
            $stmtCekTimbang->close();
        } else {
            throw new Exception("Gagal menyiapkan statement untuk cek timbang_darah: " . $dbi->error);
        }



        // Statement untuk insert ke dpengolahan
        $insert_sql = "INSERT INTO dpengolahan 
        (NoTrans, noKantong, Produk, petugas, tgl, tglPengerjaan, aPutar, aPisah, aBeku, pcepat, psuhu, pwaktu, pisah, metode, noseri, goldarah, rhesus, jenis, up_data, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulaiBeku, SelesaiBeku, mulai, selesai, bstatus, bsuhu, verifikator, musnah)
        SELECT NoTrans, noKantong, Produk, petugas, tgl, tglPengerjaan, aPutar, aPisah, aBeku, pcepat, psuhu, pwaktu, pisah, metode, noseri, goldarah, rhesus, jenis, up_data, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulaiBeku, selesaiBeku, mulai, selesai, bstatus, bsuhu, verifikator, musnah
        FROM dpengolahan_temp";

        // $selDTemp = "SELECT NoTrans, noKantong, Produk, petugas, tgl, tglPengerjaan, aPutar, aPisah, aBeku, pcepat, psuhu, pwaktu, pisah, metode, noseri, goldarah, rhesus, jenis, up_data, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulaiBeku, selesaiBeku, mulai, selesai, bstatus, bsuhu, verifikator, musnah, CONCAT(DATE(tgl), ' ', TIME(selesai)) AS tglPengolahan  FROM dpengolahan_temp WHERE noKantong = '$noKantong'";


        $selDTemp = "SELECT substring(noKantong, -1) as nK, NoTrans, noKantong, Produk, petugas, tgl, tglPengerjaan, tglAftap, ed_produk, goldarah, rhesus, jenis, volume, shift, mulai, selesai, bstatus, bsuhu, verifikator, musnah, CONCAT(DATE(tgl), ' ', TIME(selesai)) AS tglPengolahan 
            FROM dpengolahan_temp WHERE noKantong = ?";

        if ($stmtSelect = $dbi->prepare($selDTemp)) {
            // Prepare both update queries
            $upStokAll = "UPDATE stokkantong SET tgl_Aftap = ?, kadaluwarsa = ?, produk = ?, volume = ?, tglpengolahan = ?, pengolahan_di = ? WHERE noKantong = ?";
            $upStokNoAftap = "UPDATE stokkantong SET kadaluwarsa = ?, produk = ?, volume = ?, tglpengolahan = ?, pengolahan_di = ? WHERE noKantong = ?";

            // Query to insert a log entry into the user_log table
            $logq = "INSERT INTO `user_log`(`time_aksi`,`komputer`, `user`, `modul`, `aksi_user`, `keterangan`, `tempat`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            if (session_id() == '') {
                session_start();
            }
            if ($insLog = $dbi->prepare($logq)) {
                $time_aksi = date('Y-m-d H:i:s');
                $clip = isset($_SESSION['client_ip']) ? $_SESSION['client_ip'] : '';
                $nmus = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';
                $log_mdl = 'PENGOLAHAN';
                $logUnix = $noKantongRes; // Ini akan di-overwrite per loop, tapi tidak digunakan di bind
                $kett = "-";
                $tempat = "DG";

                foreach ($noKantong as $vKantong) {
                    $stmtSelect->bind_param('s', $vKantong);
                    $stmtSelect->execute();

                    $stmtSelect->store_result();

                    // Bind the result
                    $stmtSelect->bind_result(
                        $nkSatelite,
                        $noTrans,
                        $noKantongRes,
                        $produk,
                        $petugas,
                        $tgl,
                        $tglPengerjaan,
                        $tglAftap,
                        $ed_produk,
                        $goldarah,
                        $rhesus,
                        $jenis,
                        $volume,
                        $shift,
                        $mulai,
                        $selesai,
                        $bstatus,
                        $bsuhu,
                        $verifikator,
                        $musnah,
                        $tglPengolahan
                    );

                    // Fetch data
                    while ($stmtSelect->fetch()) {
                        // Ambil nilai produk dari stokkantong
                        $produkStok = null;
                        $cekProdukSql = "SELECT produk FROM stokkantong WHERE noKantong = ?";
                        if ($cekProdukStmt = $dbi->prepare($cekProdukSql)) {
                            $cekProdukStmt->bind_param('s', $noKantongRes);
                            $cekProdukStmt->execute();
                            $cekProdukStmt->bind_result($produkStok);
                            $cekProdukStmt->fetch();
                            $cekProdukStmt->close();
                        }

                        // Validasi: jika produk di stokkantong sudah ada (tidak kosong/tidak null)
                        // dan produk hasil ($produk) kosong/null, skip update
                        if (
                            !empty($produkStok) &&
                            (is_null($produk) || $produk === '')
                        ) {
                            // Tidak perlu update, skip ke berikutnya
                            continue;
                        }

                        $volum = $volume;

                        $cekTglPengerjaan = (empty($tglPengerjaan) || substr($tglPengerjaan, 0, 10) == '0000-00-00') ? $tgl : $tglPengerjaan;
                        if ($nkSatelite === 'A') {
                            // Tidak update tgl_Aftap
                            if ($stmtUpdate = $dbi->prepare($upStokNoAftap)) {
                                $stmtUpdate->bind_param('ssssss', $ed_produk, $produk, $volum, $cekTglPengerjaan, $idudd, $noKantongRes);
                                if (!$stmtUpdate->execute()) {
                                    error_log("Failed to execute update (no tgl_Aftap): " . $stmtUpdate->error);
                                }
                                $stmtUpdate->close();
                            } else {
                                error_log('Failed to prepare update statement for stokkantong (no tgl_Aftap): ' . $dbi->error);
                                throw new Exception('Statement gagal untuk prepared update stokkantong (no tgl_Aftap): ' . $dbi->error);
                            }
                        } else {
                            // Update semua field termasuk tgl_Aftap
                            if ($stmtUpdate = $dbi->prepare($upStokAll)) {
                                $stmtUpdate->bind_param('sssssss', $tglAftap, $ed_produk, $produk, $volum, $cekTglPengerjaan, $idudd, $noKantongRes);
                                if (!$stmtUpdate->execute()) {
                                    error_log("Failed to execute update: " . $stmtUpdate->error);
                                }
                                $stmtUpdate->close();
                            } else {
                                error_log('Failed to prepare update statement for stokkantong: ' . $dbi->error);
                                throw new Exception('Statement gagal untuk prepared update stokkantong: ' . $dbi->error);
                            }
                        }

                        // Logging per item (dipindah ke dalam loop ini)
                        $log_aksi = 'Pengolahan (Konvensional), dengan No.Transaksi ' . $noTrans . ', Nomor Kantong: ' . $noKantongRes . ', menjadi Produk: ' . $produk;
                        $insLog->bind_param('sssssss', $time_aksi, $clip, $nmus, $log_mdl, $log_aksi, $kett, $tempat); // Gunakan $time_aksi, bukan $tglPengerjaan (sesuaikan jika perlu)
                        if (!$insLog->execute()) {
                            throw new Exception('Gagal menyimpan log pengolahan untuk kantong: ' . $noKantongRes);
                        }
                    }
                }

                $insLog->close(); // Tutup statement log setelah loop
            } else {
                throw new Exception('Failed to prepare log statement: ' . $dbi->error);
            }

            $stmtSelect->close();
        } else {
            error_log('Failed to prepare select statement for dpengolahan_temp: ' . $dbi->error);

            throw new Exception('Failed to prepare select statement for dpengolahan_temp: ' . $dbi->error);
        }


        // Eksekusi insert dan cek hasilnya
        if ($dbi->query($insert_sql)) {
            // Hapus data dari tabel pengolahan_temp setelah insert berhasil
            $delete_sql = "DELETE FROM dpengolahan_temp WHERE petugas = ?";

            if ($stmtDelete = $dbi->prepare($delete_sql)) {
                $stmtDelete->bind_param('s', $kPetugas);
                if (!$stmtDelete->execute()) {
                    throw new Exception('Gagal menghapus data dari tabel pengolahan_temp.');
                }
            } else {
                throw new Exception('Failed to prepare delete pengolahan_temp statement: ' . $dbi->error);
            }

            // Commit transaksi
            //$dbi->commit();
            $dbi->query("COMMIT");
            $response['success'] = true;
            $response['status'] = 'success'; //yang diambil response status bukan response success diatas
            // $response['message'] = 'Data berhasil disimpan dan dilanjutkan.';
            $response['message'] = 'Nomor Transaksi: <b>' . $noTrans . '</b>, Berhasil disimpan.';
            $response['noTrans'] = $noTrans;

            //======= Audit Trial =================================================================================
            $time_aksi = date('Y-m-d H:i:s'); // Ini tampaknya salah, seharusnya date, tapi sesuai asli
            $log_mdl = 'PENGOLAHAN';
            $logUnix = $noKantongRes;
            $tempat = "DG";
            $log_aksi = 'Pengolahan (Konvensional), dengan No.Transaksi ' . $noTrans . ', Nomor Kantong: ' . $noKantongRes . ', menjadi Produk: ' . $produk;

            $logFile = __DIR__ . "/../user_log.php";

            if (!file_exists($logFile)) {
                error_log("File user_log.php tidak ditemukan! Path: $logFile");
            } else {
                include_once $logFile;
            }
            //=====================================================================================================
            // Catatan: Bagian audit trial ini masih menggunakan nilai terakhir. Jika ingin per item, pindah ke loop juga atau hapus jika duplikat.

        } else {
            throw new Exception('Gagal menyimpan data ke tabel pengolahan.' . $dbi->error);
        }
    } else {
        throw new Exception('Error preparing statement: ' . $dbi->error);
    }
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    //$dbi->rollback();
    $dbi->query("ROLLBACK");
    $response['message'] = $e->getMessage();
}

// Kirimkan respons dalam format JSON
header('Content-Type: application/json');
echo json_encode($response);
