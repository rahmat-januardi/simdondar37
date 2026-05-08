<?php
require_once("../../config/dbi_connect.php");
// require_once("../config/dbi_connect.php");
$dbi->set_charset('utf8');

$response = array('success' => false, 'message' => '');

$noTrans = isset($_POST['NoTrans']) ? $_POST['NoTrans'] : '';
$kPetugas = isset($_POST['petugas']) ? $_POST['petugas'] : '';

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

        // amankan input
        $value = mysqli_real_escape_string($dbi, $value);

        // ================= CEK DATA =================
        $cekKantong = "SELECT id FROM dpengolahan WHERE noKantong = '$value' LIMIT 1";
        $resultCek = $dbi->query($cekKantong);

        if (!$resultCek) {
            throw new Exception("Query cek gagal: " . $dbi->error);
        }

        if ($resultCek->num_rows > 0) {

            // ================= UPDATE =================
            $update_sql = "
        UPDATE dpengolahan d
        JOIN dpengolahan_temp t ON d.noKantong = t.noKantong
        SET 
            d.NoTrans = t.NoTrans,
            d.Produk = t.Produk,
            d.petugas = t.petugas,
            d.tgl = t.tgl,
            d.tglPengerjaan = t.tglPengerjaan,
            d.aPutar = t.aPutar,
            d.aPisah = t.aPisah,
            d.aBeku = t.aBeku,
            d.pcepat = t.pcepat,
            d.psuhu = t.psuhu,
            d.pwaktu = t.pwaktu,
            d.pisah = t.pisah,
            d.metode = t.metode,
            d.noseri = t.noseri,
            d.goldarah = t.goldarah,
            d.rhesus = t.rhesus,
            d.jenis = t.jenis,
            d.up_data = t.up_data,
            d.shift = t.shift,
            d.mulaiPutar = t.mulaiPutar,
            d.selesaiPutar = t.selesaiPutar,
            d.mulaiPisah = t.mulaiPisah,
            d.selesaiPisah = t.selesaiPisah,
            d.mulaiBeku = t.mulaiBeku,
            d.selesaiBeku = t.selesaiBeku,
            d.mulai = t.mulai,
            d.selesai = t.selesai,
            d.bstatus = t.bstatus,
            d.bsuhu = t.bsuhu,
            d.verifikator = t.verifikator,
            d.musnah = t.musnah
        WHERE d.noKantong = '$value'
        LIMIT 1
    ";

            if (!$dbi->query($update_sql)) {
                throw new Exception("Gagal UPDATE dpengolahan: " . $dbi->error);
            }
        } else {

            // ================= INSERT =================
            $insert_sql = "
        INSERT INTO dpengolahan 
        (NoTrans, noKantong, Produk, petugas, tgl, tglPengerjaan, aPutar, aPisah, aBeku, pcepat, psuhu, pwaktu, pisah, metode, noseri, goldarah, rhesus, jenis, up_data, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulaiBeku, selesaiBeku, mulai, selesai, bstatus, bsuhu, verifikator, musnah)
        SELECT 
            NoTrans, noKantong, Produk, petugas, tgl, tglPengerjaan, aPutar, aPisah, aBeku, pcepat, psuhu, pwaktu, pisah, metode, noseri, goldarah, rhesus, jenis, up_data, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulaiBeku, selesaiBeku, mulai, selesai, bstatus, bsuhu, verifikator, musnah
        FROM dpengolahan_temp
        WHERE noKantong = '$value'
        LIMIT 1
    ";

            if (!$dbi->query($insert_sql)) {
                throw new Exception("Gagal INSERT dpengolahan: " . $dbi->error);
            }
        }

        // $selDTemp = "SELECT NoTrans, noKantong, Produk, petugas, tgl, tglPengerjaan, aPutar, aPisah, aBeku, pcepat, psuhu, pwaktu, pisah, metode, noseri, goldarah, rhesus, jenis, up_data, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulaiBeku, selesaiBeku, mulai, selesai, bstatus, bsuhu, verifikator, musnah, CONCAT(DATE(tgl), ' ', TIME(selesai)) AS tglPengolahan  FROM dpengolahan_temp WHERE noKantong = '$noKantong'";


        $selDTemp = "SELECT substring(noKantong, -1) as nK, NoTrans, noKantong, Produk, petugas, tgl, tglPengerjaan, tglAftap, ed_produk, goldarah, rhesus, jenis, volume, shift, mulai, selesai, bstatus, bsuhu, verifikator, musnah, CONCAT(DATE(tgl), ' ', TIME(selesai)) AS tglPengolahan 
            FROM dpengolahan_temp WHERE noKantong = ?";

        if ($stmtSelect = $dbi->prepare($selDTemp)) {
            // Prepare both update queries
            $upStokAll = "UPDATE stokkantong SET tgl_Aftap = ?, kadaluwarsa = ?, produk = ?, volume = ?, tglpengolahan = ? WHERE noKantong = ?";
            $upStokNoAftap = "UPDATE stokkantong SET kadaluwarsa = ?, produk = ?, volume = ?, tglpengolahan = ? WHERE noKantong = ?";

            // Query to insert a log entry into the user_log table
            $logq = "INSERT INTO `user_log`(`time_aksi`,`komputer`, `user`, `modul`, `aksi_user`, `keterangan`, `tempat`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            session_start();
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

                        if ($nkSatelite === 'A') {
                            // Tidak update tgl_Aftap
                            $cekTglPengerjaan = (empty($tglPengerjaan) || substr($tglPengerjaan, 0, 10) == '0000-00-00') ? $tgl : $tglPengerjaan;
                            if ($stmtUpdate = $dbi->prepare($upStokNoAftap)) {
                                $stmtUpdate->bind_param('sssss', $ed_produk, $produk, $volum, $cekTglPengerjaan, $noKantongRes);
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
                                $stmtUpdate->bind_param('ssssss', $tglAftap, $ed_produk, $produk, $volum, $cekTglPengerjaan, $noKantongRes);
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
            $time_aksi = 'PENGOLAHAN'; // Ini tampaknya salah, seharusnya date, tapi sesuai asli
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