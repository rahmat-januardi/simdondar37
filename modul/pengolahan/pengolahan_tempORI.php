<?php
session_start();
include "../../config/dbi_connect.php";
//require_once("config/dbi_connect.php");

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

    return $formattedDate;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $kPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : 'ikadek';
    $nomorKantong = isset($_POST['nomorKantong']) ? $_POST['nomorKantong'] : null;

    // Tambahkan log atau debug statement
    error_log("Nomor Kantong diterima: " . $nomorKantong);

    if ($nomorKantong === null || empty($nomorKantong)) {
        echo json_encode(array('status' => 'error', 'message' => 'Nomor Kantong tidak ditemukan, nomor kantong adalah sebgai berikut .' . $nomorKantong));
        exit;
    }

    $alatPutar = mysqli_real_escape_string($dbi, $_POST['alatPemutaran']);
    $alatPisah = mysqli_real_escape_string($dbi, $_POST['alatPemisahan']);
    // $jamMulaiPutar = mysqli_real_escape_string($dbi, $_POST['jamMulaiPutar'] . ':00');
    // $jamSelesaiPutar = mysqli_real_escape_string($dbi, $_POST['jamSelesaiPutar'] . ':00');
    // $jamMulaiPisah = mysqli_real_escape_string($dbi, $_POST['jamMulaiPisah'] . ':00');
    // $jamSelesaiPisah = mysqli_real_escape_string($dbi, $_POST['jamSelesaiPisah'] . ':00');
    $jamMulaiPutar = mysqli_real_escape_string($dbi, $_POST['jamMulaiPutar'] . ':' . date('s'));
    $jamSelesaiPutar = mysqli_real_escape_string($dbi, $_POST['jamSelesaiPutar'] . ':' . date('s'));
    $jamMulaiPisah = mysqli_real_escape_string($dbi, $_POST['jamMulaiPisah'] . ':' . date('s'));
    $jamSelesaiPisah = mysqli_real_escape_string($dbi, $_POST['jamSelesaiPisah'] . ':' . date('s'));

    $nK = mysqli_real_escape_string($dbi, $_POST['nomorKantong']);
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
        $sData = "SELECT substring(noKantong, -1) as nK, LEFT(noKantong, LENGTH(noKantong) - 1) as tanpaSatelite, tgl_Aftap, gol_darah, RhesusDrh, jenis, produk, kadaluwarsa, volume AS volLengkap, 
REPLACE(volume, '\xb1','') AS volume FROM stokkantong WHERE noKantong = '$nK'";
        $result = mysqli_query($dbi, $sData);

        if ($result) {
            $selData = mysqli_fetch_assoc($result);
            $tAftap = $selData['tgl_Aftap'];
            $jKantong = $selData['jenis'];
            $jkomp = $selData['produk'];

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

                // if ($jKantong == "4") {
                //     $daysToAdd += 7; // Jika jKantong == "4", tambahkan 7 hari
                // }

                // Modifikasi tanggal kedaluwarsa
                $tglEd->modify("+$daysToAdd days");
                if ($hoursToAdd > 0) {
                    $tglEd->modify("+$hoursToAdd hours");
                }

                // Cek suhu simpan jika diperlukan
                // ...
            } else {
                // Jika produk tidak ditemukan dalam data produk
                // Handle default case atau log error
            }

            // STATIS ED Produk
            // switch ($jkomp) {
            //     case "WB":
            //     case "PRC":
            //         $daysToAdd = ($jKantong == "4") ? 42 : 35;
            //         $tglEd->modify("+$daysToAdd days");
            //         break;
            //     case "WE":
            //         $tglEd = new DateTime();
            //         $tglEd->modify("+5 hours");
            //         break;
            //     case "TC":
            //     case "BC":
            //         $tglEd->modify("+5 days");
            //         break;
            //     case "FFP":
            //     case "FP24":
            //     case "FP72":
            //     case "FFP Leucodepletet":
            //     case "FFP Konvalesen":
            //         $tglEd->modify("+365 days");
            //         break;
            //     case "AHF":
            //     case "LP":
            //     case "LP Leucodepletet":
            //     case "LP Apheresis":
            //     case "TC Leucodepletet":
            //     case "TC Apheresis":
            //         $tglEd->modify("+5 days");
            //         break;
            //     case "PRC Leucoreduction":
            //     case "PRC Leucodepletet":
            //     case "WB Leucodepletet":
            //         $tglEd->modify("+35 days");
            //         break;
            //     case "PRC Apheresis":
            //         $tglEd->modify("+42 days");
            //         break;
            //     default:
            //         // Tambahkan default case jika perlu
            //         break;
            // }


            // merubah string ed produk ke object
            $tglEdObject = $tglEd->format("Y-m-d H:i:s");

            switch ((int) ($selData['jenis'])) {
                case 1:
                case 2:
                    $pVol = ($selData['nK'] == 'B') ? "150" : "200";
                    $pCepat = "3000";
                    $pSuhu = 4;
                    $bSuhu = "4";
                    break;
                case 6:
                    $pCepat = "3000";
                    $pSuhu = 4;
                    $bSuhu = "4";
                    break;
                case 3:
                    if ($selData['nK'] == 'A') {
                        $pVol = "200";
                    } elseif ($selData['nK'] == 'B') {
                        $pVol = "150";
                    } else {
                        $pVol = "150";
                    }
                    $pCepat = ($selData['nK'] == 'A') ? "2000" : "4000";
                    $pSuhu = 22;
                    $bSuhu = "22";
                    break;
                case 4:
                    if ($selData['nK'] == 'A') {
                        $pVol = "200";
                        $pCepat = "4000";
                        $pSuhu = 22;
                        $bSuhu = "22";
                    } elseif ($selData['nK'] == 'B') {
                        $pVol = "150";
                        $pCepat = "170";
                        $pSuhu = 22;
                        $bSuhu = "22";
                    } elseif ($selData['nK'] == 'C') {
                        $pVol = "150";
                        $pCepat = "3000";
                        $pSuhu = 4;
                        $bSuhu = "4";
                    } else {
                        $pVol = "150";
                        $pCepat = "4000";
                        $pSuhu = 22;
                        $bSuhu = "22";
                    }
                    break;
                default:
                    $pVol = "200";
                    $pSuhu = 22;
                    $bSuhu = "22";
                    $pCepat = "3000";
                    break;
            }

            // Menghitung durasi menjadi Satuan Menit Gess
            $start = new DateTime($jamMulaiPutar);
            $end = new DateTime($jamSelesaiPutar);
            $interval = $start->diff($end);
            $waktuPutar = ($interval->h * 60) + $interval->i;

            if ($selData['nK'] != 'A') {
                $resultKantong = mysqli_query($dbi, "SELECT jenis, volume as volLengkap, 
REPLACE(volume, '\xb1', '') AS volume, gol_darah, RhesusDrh, tgl_Aftap FROM stokkantong WHERE noKantong LIKE '$selData[tanpaSatelite]%' AND noKantong LIKE '%A'");
                if (!$resultKantong || mysqli_num_rows($resultKantong) == 0) {
                    // Handle the error, e.g., log it, throw an exception, or set a default value
                    echo json_encode(array('status' => 'error', 'message' => 'Data kantong tidak ditemukan atau query gagal'));
                    exit;
                }
                $selKantong = mysqli_fetch_assoc($resultKantong);

                $selTemp = "INSERT INTO dpengolahan_temp 
                        (noTrans, noKantong, tgl, tglAftap, goldarah, rhesus, jenis, Produk, volume, ed_produk, metode, petugas, aPutar, aPisah, pcepat, psuhu, pwaktu, pisah, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulai, selesai, bstatus, bsuhu) 
                        VALUES 
                        ('$nT', '$nK', '$dAllDay', '$selKantong[tgl_Aftap]', '$selKantong[gol_darah]', '$selKantong[RhesusDrh]', '$selKantong[jenis]', '$jkomp', '$pVol', '$tglEdObject', 1, '$_SESSION[namauser]', '$alatPutar', '$alatPisah', '$pCepat', '$pSuhu', '$waktuPutar', 0, '$shift', '$jamMulaiPutar','$jamSelesaiPutar','$jamMulaiPisah','$jamSelesaiPisah', '$jamMulaiPutar', '$jamSelesaiPisah', 0, '$bSuhu')";
                error_log("Fetched Data !A - Produk: $jkomp, Volume: $selKantong[volume], TglPengolahan: $dAllDay, NoKantong: $nK");
            } else {
                $selTemp = "INSERT INTO dpengolahan_temp 
                        (noTrans, noKantong, tgl, tglAftap, goldarah, rhesus, jenis, Produk, volume, ed_produk, metode, petugas, aPutar, aPisah, pcepat, psuhu, pwaktu, pisah, shift, mulaiPutar, selesaiPutar, mulaiPisah, selesaiPisah, mulai, selesai, bstatus, bsuhu) 
                        VALUES 
                        ('$nT', '$nK', '$dAllDay', '$selData[tgl_Aftap]', '$selData[gol_darah]', '$selData[RhesusDrh]', '$selData[jenis]', '$jkomp', '$selData[volume]', '$tglEdObject', 1, '$_SESSION[namauser]', '$alatPutar', '$alatPisah', '$pCepat', '$pSuhu', '$waktuPutar', 0, '$shift', '$jamMulaiPutar','$jamSelesaiPutar','$jamMulaiPisah','$jamSelesaiPisah', '$jamMulaiPutar', '$jamSelesaiPisah', 0, '$bSuhu')";
                error_log("Fetched Data A - Produk: $jkomp, Volume: $selData[volume], TglPengolahan: $dAllDay, NoKantong: $nK");
            }

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
            // echo "Nomor Kantong tidak valid: " . $valResult;
            error_log("Nomor Kantong tidak valid: " . $valResult);
            echo json_encode(array('status' => 'error', 'message' => 'Nomor Kantong tidak valid'));
        }
    }
}

function isValidNomorKantong($nK, $dbi)
{
    $sData0 = "SELECT `jenis`, `produk`, `tglpengolahan`, `Status`, `volume`, `merk`, `lama_pengambilan`, `sah`, `kadaluwarsa` FROM stokkantong WHERE `noKantong` = '$nK'";
    $result0 = mysqli_query($dbi, $sData0);


    if ($result0 && mysqli_num_rows($result0) > 0) {
        $sD = mysqli_fetch_assoc($result0);
        $kedaluwarsa = strtotime($sD['kadaluwarsa']);

        $selDpTemp = mysqli_query($dbi, "SELECT `noKantong` FROM dpengolahan_temp WHERE `noKantong` = '$nK'");

        if (mysqli_num_rows($selDpTemp) > 0) {

            $pesanExist = "Kantong <b>SUDAH ADA DALAM DAFTAR</b>, lihat daftar antrian pengolahan darah dan periksa kembali nomor kantong yang anda masukkan.";
            echo json_encode(array('status' => 'error', 'message' => $pesanExist));
            exit;
        }

        if ($sD['Status'] == 0) {
            echo json_encode(array('status' => 'error', 'message' => 'Status Kantong Kosong, harap periksa kembali nomor kantong yang anda masukkan'));
            exit;
        }

        if ($sD['Status'] == 3) {
            echo json_encode(array('status' => 'error', 'message' => 'Status <b>Kantong Keluar</b>, harap periksa kembali nomor kantong yang anda masukkan'));
            exit;
        }

        if ($sD['sah'] == 0 || is_null($sD['sah'])) {
            echo json_encode(array('status' => 'error', 'message' => 'Kantong darah <b>BELUM DISAHKAN.</b>!'));
            exit;
        }

        if ($sD['Status'] != 1 && $sD['Status'] != 2) {
            echo json_encode(array('status' => 'error', 'message' => 'Status Kantong Tidak Sesuai, harap periksa kembali nomor kantong yang anda masukkan. <b>Status Kantong Bukan Darah (Karantina atau Darah Sehat).</b>'));
            exit;
        }

        if (!is_null($sD['kadaluwarsa']) && $kedaluwarsa < time()) {
            echo json_encode(array('status' => 'error', 'message' => 'Kantong darah sudah <b>KEDALUWARSA</b> pada tanggal: <br><b>' . formatTanggal($sD['kadaluwarsa']) . '</b> dan tidak dapat diproses.'));
            exit;
        }

        if ($sD['lama_pengambilan'] <= 0 || $sD['lama_pengambilan'] >= 14) {
            echo json_encode(array('status' => 'error', 'message' => 'Darah tidak dapat diproses, karena tidak memenuhi syarat pembuatan komponen. (Durasi Pengambilan <b>' . $sD['lama_pengambilan'] . ' menit)</b>.'));
            exit;
        }

        return true;
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Nomor Kantong berikut <b>' . $nK . '</b> tidak ditemukan.'));
        // error_log("Nomor Kantong diterima: " . $nomorKantong);
        exit;
    }
}
