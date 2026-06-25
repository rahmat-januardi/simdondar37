<?php
session_start();
include "../../config/dbi_connect.php";
// include "../config/dbi_connect.php";

$iPetugas = $_SESSION['namauser'];
// $iPetugas = "irawanDB";

$dAllDay = date("Y-m-d H:i:s");
$dDay = DATE("Y-m-d");
$d = DATE('d');
$m = DATE('m');
$yr = DATE('Y');
$y = substr($yr, 2, 2);

function formatTanggal($inputTanggal)
{
    // Daftar bulan dalam Bahasa Indonesia
    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    // Cek apakah input valid
    if (strtotime($inputTanggal) === false) {
        return "Invalid date";
    }

    // versi simple
    // $d = new DateTime($inputTanggal);
    // $formattedDate = $d->format("d") . ' ' . $bulan[(int)$d->format("m")] . ' ' . $d->format("Y - H:i");
    // =========================================
    // Pisahkan tanggal dan waktu menggunakan DateTime
    $dateTime = new DateTime($inputTanggal);

    // Format tanggal sesuai kebutuhan
    $tanggal = $dateTime->format("d");
    $bulanIndex = (int) $dateTime->format("m");
    $tahun = $dateTime->format("Y");
    $waktu = $dateTime->format("H:i");

    // Gabungkan dalam format yang diinginkan
    // $formattedDate = $tanggal . ' ' . $bulan[$bulanIndex] . ' ' . $tahun . ' - ' . $waktu . ' WIB';
    $formattedDate = $tanggal . ' ' . $bulan[$bulanIndex] . ' ' . $tahun . ' - ' . $waktu;
    // ========================================
    return $formattedDate;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $kPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '-';
    $nomorKantong = isset($_POST['nomorKantong']) ? $_POST['nomorKantong'] : null;

    // Tambahkan log atau debug statement
    error_log("Nomor Kantong diterima: " . $nomorKantong);

    if ($nomorKantong === null || empty($nomorKantong)) {
        echo json_encode(array('status' => 'error', 'message' => 'Nomor Kantong tidak ditemukan, nomor kantong adalah sebgai berikut .' . $nomorKantong));
        exit;
    }

    $tglPengerjaan = mysqli_real_escape_string($dbi, $_POST['tglPengerjaan']);
    $alatPutar = mysqli_real_escape_string($dbi, $_POST['alatPemutaran']);
    $alatPisah = mysqli_real_escape_string($dbi, $_POST['alatPemisahan']);
    $alatPembekuan = mysqli_real_escape_string($dbi, $_POST['alatPembekuan']);
    // $jamMulaiPutar = mysqli_real_escape_string($dbi, $_POST['jamMulaiPutar'] . ':00');
    // $jamSelesaiPutar = mysqli_real_escape_string($dbi, $_POST['jamSelesaiPutar'] . ':00');
    // $jamMulaiPisah = mysqli_real_escape_string($dbi, $_POST['jamMulaiPisah'] . ':00');
    // $jamSelesaiPisah = mysqli_real_escape_string($dbi, $_POST['jamSelesaiPisah'] . ':00');
    $jamMulaiPutar = mysqli_real_escape_string($dbi, $_POST['jamMulaiPutar'] . ':' . date('s'));
    $jamSelesaiPutar = mysqli_real_escape_string($dbi, $_POST['jamSelesaiPutar'] . ':' . date('s'));
    $jamMulaiPisah = mysqli_real_escape_string($dbi, $_POST['jamMulaiPisah'] . ':' . date('s'));
    $jamSelesaiPisah = mysqli_real_escape_string($dbi, $_POST['jamSelesaiPisah'] . ':' . date('s'));
    $jamMulaiBeku = mysqli_real_escape_string($dbi, $_POST['jamMulaiBeku'] . ':' . date('s'));
    $jamSelesaiBeku = mysqli_real_escape_string($dbi, $_POST['jamSelesaiBeku'] . ':' . date('s'));

    //$nK = mysqli_real_escape_string($dbi, $_POST['nomorKantong']);
    $nK = strtoupper(mysqli_real_escape_string($dbi, $_POST['nomorKantong']));
    $shift = mysqli_real_escape_string($dbi, $_POST['shift']);

    //error_log("Nomor Kantong setelah sanitasi: " . $nK);
    //echo "Debugging: Nomor Kantong setelah sanitasi adalah: " . $nK;


    //$kPetugas = mysqli_real_escape_string($dbi, $sPetugas);

    //------------------------ set Nomor Transaksi Pengolahan ------------------------->
    // digit pasien 19 digit, 2 digit KV (konvensional), 4 digit wilayah/id udd, 1 digit '-', 6 digit tanggal bulan tahun, 1 digit '-', 4 sequence,
    $q_wilayah = mysqli_query($dbi, "SELECT `id` FROM utd WHERE `aktif` = 1");
    $area = mysqli_fetch_assoc($q_wilayah);
    $kdtp = "KV" . $area['id'] . "-" . $d . $m . $y . "-";
    $idp = mysqli_query($dbi, "SELECT `noTrans` FROM dpengolahan WHERE `noTrans` LIKE '$kdtp%' AND  `petugas` = '$kPetugas' ORDER BY `noTrans` DESC");
    $idp1 = mysqli_fetch_assoc($idp);
    if ($idp1 && isset($idp1['noTrans'])) {
        $idp2 = substr($idp1['noTrans'], 15, 4);
    } else {
        $idp2 = "000";
    }
    // $idp2       = substr($idp1['kd_pasien'], 6, 5);
    if ($idp2 < 1) {
        $idp2 = "000";
    }
    $int_idp2 = (int) $idp2 + 1;
    $j_nol1 = 4 - (strlen(strval($int_idp2)));
    $idp4 = "";
    for ($i = 0; $i < $j_nol1; $i++) {
        $idp4 .= "0";
    }
    $nT = $kdtp . $idp4 . $int_idp2;
    //---------------------- END set Nomor Transaksi ------------------------->


    $valResult = isValidNomorKantong($nK, $dbi);
    if ($valResult === true) {
        $sData = "SELECT substring(noKantong, -1) as nK, LEFT(noKantong, LENGTH(noKantong) - 1) as tanpaSatelite, tgl_Aftap, gol_darah, RhesusDrh, jenis, produk, kadaluwarsa, volume AS volLengkap, REPLACE(volume, '\xb1','') AS volume FROM stokkantong WHERE noKantong = '$nK'";
        // $sData = "SELECT substring(noKantong, -1) as nK, LEFT(noKantong, LENGTH(noKantong) - 1) as tanpaSatelite, tgl_Aftap, gol_darah, RhesusDrh, jenis, produk, kadaluwarsa, volume, metoda FROM stokkantong WHERE noKantong = '$nK'";
        $result = mysqli_query($dbi, $sData);

        if ($result) {
            $selData = mysqli_fetch_assoc($result);
            $tAftap = $selData['tgl_Aftap'];
            $jKantong = $selData['jenis'];
            $jkomp = $selData['produk'];
            $jKUtama = $selData['tanpaSatelite'] . 'A';
            $jKInput = $selData['tanpaSatelite'] . $selData['nK'];

            $tglEd = new DateTime($tAftap);

            $selProduk = "SELECT * FROM produk";
            $sProduk = mysqli_query($dbi, $selProduk);

            $produkData = array();
            while ($sP = mysqli_fetch_assoc($sProduk)) {
                $produkData[$sP['Nama']] = array(
                    'umurhari' => (int) $sP['umurhari'],
                    'umurjam' => (int) $sP['umurjam'],
                    'suhusimpan' => $sP['suhusimpan'],
                    // 'volume' => (int)$sP['volume']
                    'volume' => $sP['volume']
                );
            }

            // Cek apakah produk yang diambil ada di dalam data produk
            if (isset($produkData[$jkomp])) {
                $produkInfo = $produkData[$jkomp];
                $daysToAdd = $produkInfo['umurhari'];
                $hoursToAdd = $produkInfo['umurjam'];

                $tglEd->modify("+$daysToAdd days");
                $tglEd->modify("+$hoursToAdd hours");

                $tglEdObject = $tglEd->format('Y-m-d H:i:s');
            } else {
                $tglEdObject = '0000-00-00 00:00:00';
            }

            // Fungsi Menghitung durasi menjadi Satuan Menit Gess
            $start = new DateTime($jamMulaiPutar);
            $end = new DateTime($jamSelesaiPutar);
            $interval = $start->diff($end);
            $waktuPutar = ($interval->h * 60) + $interval->i;

            $selTemp = "SELECT * FROM stokkantong WHERE noKantong = '$nK' LIMIT 1";
            $rTemp = mysqli_query($dbi, $selTemp);
            $dataSource = mysqli_fetch_assoc($rTemp);

	    // Fallback tgl_Aftap dari kantong A jika satelit tidak punya
            if (empty($dataSource['tgl_Aftap']) || $dataSource['tgl_Aftap'] == '0000-00-00' || $dataSource['tgl_Aftap'] == '0000-00-00 00:00:00') {
                $selKantongA = "SELECT tgl_Aftap FROM stokkantong WHERE noKantong = '$jKUtama' LIMIT 1";
                $rKantongA = mysqli_query($dbi, $selKantongA);
                $dataKantongA = mysqli_fetch_assoc($rKantongA);
                if ($dataKantongA && !empty($dataKantongA['tgl_Aftap']) && $dataKantongA['tgl_Aftap'] != '0000-00-00') {
                    $dataSource['tgl_Aftap'] = $dataKantongA['tgl_Aftap'];
                }
            }

            $pCepat = 5000;
            $pSuhu = 4;
            $pisah = 0;
            $bSuhu = -50;

            $selTemp = "INSERT INTO dpengolahan_temp (NoTrans, noKantong, tgl, tglAftap, goldarah, rhesus, jenis, metoda, Produk, volume, ed_produk, petugas, aPutar, aPisah, aBeku, pcepat, psuhu, pwaktu, pisah, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulaiBeku, selesaiBeku, mulai, selesai, bstatus, bsuhu, tglPengerjaan) 
                        VALUES 
                        ('$nT', '$nK', '$dAllDay', '{$dataSource['tgl_Aftap']}', '{$dataSource['gol_darah']}', '{$dataSource['RhesusDrh']}', '{$dataSource['jenis']}', '{$dataSource['metoda']}', '$jkomp', '{$dataSource['volume']}', '$tglEdObject', '$iPetugas', '$alatPutar', '$alatPisah', '$alatPembekuan', '$pCepat', '$pSuhu', '$waktuPutar', 0, '$shift', '$jamMulaiPutar','$jamSelesaiPutar','$jamMulaiPisah','$jamSelesaiPisah', '$jamMulaiBeku', '$jamSelesaiBeku', '$jamMulaiPutar', '$jamSelesaiPisah', 0, '$bSuhu', '$tglPengerjaan')";


            if (mysqli_query($dbi, $selTemp)) {
                // echo "Data berhasil disimpan";
                error_log("Berhasil disimpan");
                echo json_encode(array('status' => 'success'));
            } else {
                // echo "Error: " . mysqli_error($dbi);
                error_log("Error: " . mysqli_error($dbi));
                echo json_encode(array('status' => 'error', 'message' => 'Error: ' . mysqli_error($dbi) . ', Jam Mulai ' . $jamMulaiPutar));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error querying stokkantong: ' . mysqli_error($dbi)));
        }
    } else {
        // echo "Nomor Kantong tidak valid: " . $valResult;
        error_log("Nomor Kantong tidak valid: " . $valResult);
        echo json_encode(array('status' => 'error', 'message' => 'Nomor Kaaaantong tidak valid'));
    }
}

function isValidNomorKantong($nK, $dbi)
{

    $nK_esc = mysqli_real_escape_string($dbi, $nK);

    $cekDp = mysqli_query($dbi, "SELECT * FROM dpengolahan WHERE noKantong LIKE '%$nK_esc%'");
    if ($cekDp && mysqli_num_rows($cekDp) > 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Kantong <b>' . $nK . '</b> sudah ada pada data pengolahan.'));
        exit;
    }

    $sData0 = "SELECT substring(noKantong, -1) as nK, 
                    LEFT(noKantong, LENGTH(noKantong) - 1) as tanpaSatelite, 
                    `jenis`, `produk`, `tglpengolahan`, `Status`, 
                    `volume`, `merk`, `lama_pengambilan`, 
                    `sah`, `kadaluwarsa`, DATE(tgl_Aftap) AS tglAftap 
            FROM stokkantong 
            WHERE `noKantong` = '$nK'";
    $result0 = mysqli_query($dbi, $sData0);

    if ($result0 && mysqli_num_rows($result0) > 0) {
        $sD = mysqli_fetch_assoc($result0);
        $ktgUtama = $sD['tanpaSatelite'] . 'A';

        // Simpan data penting SEBELUM fallback
$originalNK     = $sD['nK'];
$originalStatus = $sD['Status'];

if ($sD['nK'] != 'A') {
    if (empty($sD['tglAftap']) || $sD['tglAftap'] == '0000-00-00') {
        $sData1 = "SELECT `jenis`, `produk`, `tglpengolahan`, `Status`, 
                       `volume`, `merk`, `lama_pengambilan`, 
                       `sah`, `kadaluwarsa`, DATE(tgl_Aftap) AS tglAftap 
               FROM stokkantong 
               WHERE noKantong = '$ktgUtama'";
        $result1 = mysqli_query($dbi, $sData1);
        if ($result1 && mysqli_num_rows($result1) > 0) {
            $sD = mysqli_fetch_assoc($result1);
        }
    }
    // Kembalikan nK dan Status asli satelit setelah fallback
    $sD['nK']     = $originalNK;
    $sD['Status'] = $originalStatus;
}

        $tglAftap = (is_null($sD['tglAftap'])) ? "KOSONG" : (($sD['tglAftap'] == '0000-00-00') ? '0000-00-00' : $sD['tglAftap']);
        $kedaluwarsa = strtotime($sD['kadaluwarsa']);

        $selDpTemp = mysqli_query($dbi, "SELECT `noKantong` FROM dpengolahan_temp WHERE `noKantong` = '$nK'");
        if (mysqli_num_rows($selDpTemp) > 0) {
            echo json_encode(array('status' => 'error', 'message' => "Kantong <b>SUDAH ADA DALAM DAFTAR</b>, lihat daftar antrian pengolahan darah dan periksa kembali nomor kantong yang anda masukkan."));
            exit;
        }

        // $nkSudahPengolahan = mysqli_query($dbi, "SELECT `noKantong` FROM dpengolahan WHERE `noKantong` = '$nK'");
        // if (mysqli_num_rows($nkSudahPengolahan) > 0) {
        //     echo json_encode(array('status' => 'error', 'message' => "Kantong <b>SUDAH PERNAH DIPROSES</b>, harap periksa kembali nomor kantong yang anda masukkan."));
        //     exit;
        // }

        if ($sD['Status'] != 5) {
            if ($sD['Status'] == 0) {
                echo json_encode(array('status' => 'error', 'message' => 'Status Kantong Kosong, harap periksa kembali nomor kantong yang anda masukkan'));
                exit;
            }
        }

        // ?? Validasi status keluar
        if ($sD['nK'] == 'A' && $sD['Status'] == 3) {
            echo json_encode(array('status' => 'error', 'message' => 'Status <b>Kantong Utama (A) Keluar</b>, tidak bisa diproses.'));
            exit;
        }

        /*if ($sD['nK'] != 'A') {
            // satelit hanya boleh status 2
            if ($sD['Status'] != 2) {
                echo json_encode(array('status' => 'error', 'message' => 'Status <b>Kantong Satelit</b> tidak sesuai. Hanya kantong satelit dengan status 2 yang bisa diproses.'));
                exit;
            }
        }*/

	if ($sD['nK'] != 'A') {
            if ($sD['Status'] == 0) {
                echo json_encode(array('status' => 'error', 'message' => 'Status <b>Kantong Satelit</b> tidak sesuai. Karena Kantong Satelit masih status dilogistik.'));
                exit;
            }
        }


        //if ($sD['Status'] == 7) {
        //    echo json_encode(array('status' => 'error', 'message' => 'Status Kantong <b>REAKTIF</b> !!! <br>Silahkan Periksa Kantong yang Anda masukkan atau masukkan kantong lain.'));
        //    exit;
        //}

        if ($sD['sah'] == 0 || is_null($sD['sah'])) {
            echo json_encode(array('status' => 'error', 'message' => 'Kantong darah <b>BELUM DISAHKAN.</b>!'));
            exit;
        }

        if ($sD['tglAftap'] == '0000-00-00' || is_null($sD['tglAftap'])) {
            echo json_encode(array('status' => 'error', 'message' => 'DATA KANTONG TIDAK LENGKAP ! Tanggal Aftap: <b>' . $tglAftap . '</b>'));
            exit;
        }

        //if ($sD['Status'] != 1 && $sD['Status'] != 2 && $sD['Status'] != 5) {
        //    echo json_encode(array('status' => 'error', 'message' => 'Status Kantong Tidak Sesuai, harap periksa kembali nomor kantong yang anda masukkan. <b>Status Kantong Bukan Darah (Karantina atau Darah Sehat).</b>'));
        //    exit;
        //}

        if ($sD['nK'] == 'A') {
            if (!is_null($sD['kadaluwarsa']) && $kedaluwarsa < time()) {
                echo json_encode(array('status' => 'error', 'message' => 'Kantong darah sudah <b>KEDALUWARSA</b> pada tanggal: <br><b>' . formatTanggal($sD['kadaluwarsa']) . '</b> dan tidak dapat diproses.'));
                exit;
            }
        }

        $batasWaktu = (stripos($sD['merk'], 'HAEMONETICS') !== false) ? 90 : 15;

        if ($sD['lama_pengambilan'] <= 0 || $sD['lama_pengambilan'] >= $batasWaktu) {
            echo json_encode(array('status' => 'error', 'message' => 'Darah tidak dapat diproses, karena tidak memenuhi syarat pembuatan komponen. (Durasi Pengambilan <b>' . $sD['lama_pengambilan'] . ' menit)</b>.'));
            exit;
        }

        // TAMBAHAN PENGECEKAN SERAH TERIMA (BARU)
        $cekSerah = "SELECT dst_stat_receive1 FROM serahterima_detail WHERE dst_nokantong = '$nK' LIMIT 1";
        $resultCek = mysqli_query($dbi, $cekSerah);
        if ($resultCek && mysqli_num_rows($resultCek) > 0) {
            $rowCek = mysqli_fetch_assoc($resultCek);
            if ($rowCek['dst_stat_receive1'] == 0) {
                echo json_encode(array('status' => 'error', 'message' => 'Kantong belum diterima. Tidak bisa membuat komponen.'));
                exit;
            }
        }
        // END TAMBAHAN PENGECEKAN

        return true;
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Nomor Kantong berikut <b>' . $nK . '</b> tidak ditemukan.'));
        exit;
    }
}
