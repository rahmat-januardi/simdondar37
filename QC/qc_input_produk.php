<?php
ob_start();
require_once('clogin.php');
require_once('config/db_connect.php');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

$namauser    = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];

$nkt = '';
$pesan = '';
$alert = '';

$data_reg = false;
$data_stok = false;
$data_qc_lama = false;
$sumber_data = '';

$produk_nama = '';
$produk_lengkap = '';
$beratjenis = '';
$vol_min = '';
$vol_max = '';

$merk = '';
$jenis = '';
$goldarah = '';
$rhesus = '';
$tglaftap = '';
$kadaluwarsa = '';
$tglpengolahan = '';
$volume = '';
$posisi_kantong = '';
$beratkosong = '';
$antikogulan = '';
$master_kantong_found = false;
$produk_master_found = false;

$qc_existing_data = false;
$qc_tglqc = date('Y-m-d');
$qc_beratisi = '';
$qc_beratkosong = '';
$qc_beratjenis = '';
$qc_antikogulan = '';
$qc_volume_hasil = '';
$qc_volhem = '';
$qc_kadhb = '';
$qc_hct = '';
$qc_plasma = '';
$qc_ttlhb = '';
$qc_swirling = '';
$qc_ph = '';
$qc_hemolisis = '';
$qc_hematokrit = '';
$qc_hemoglobin = '';
$qc_kadar_wbc = '';
$qc_leukosit = '';
$qc_kadar_trombosit = '';
$qc_trombosit = '';
$qc_faktorviii = '';
$qc_aerob = '';
$qc_anaerob = '';
$qc_dicek_oleh = '';
$qc_disahkan_oleh = '';
$qc_vi_hemolisis = '0';
$qc_vi_lipemik = '0';
$qc_vi_penggumpalan = '0';
$qc_vi_warna = '0';
$qc_vi_tdk = '0';

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

function posisiKantong($nkt)
{
    $kantongke = strtoupper(substr($nkt, -1));
    switch ($kantongke) {
        case 'A':
            return 'Kantong Utama';
        case 'B':
            return 'Kantong Satelite 1';
        case 'C':
            return 'Kantong Satelite 2';
        case 'D':
            return 'Kantong Satelite 3';
        case 'E':
            return 'Kantong Satelite 4';
        case 'F':
            return 'Kantong Satelite 5';
        case 'G':
            return 'Kantong Satelite 6';
        case 'H':
            return 'Kantong Satelite 7';
        default:
            return '';
    }
}

function jenisKantong($jenis)
{
    switch ($jenis) {
        case '1':
            return 'Single';
        case '2':
            return 'Double';
        case '3':
            return 'Triple';
        case '4':
            return 'Quadruple';
        case '5':
            return 'Quadruple T&B';
        case '6':
            return 'Pediatrik';
        default:
            return '-';
    }
}

function tglIndo($tgl)
{
    if ($tgl == '' || $tgl == '0000-00-00' || $tgl == '0000-00-00 00:00:00') {
        return '-';
    }
    return date('d-m-Y H:i:s', strtotime($tgl));
}

function statusKantong($row)
{
    if (!$row) {
        return '-';
    }

    $status = isset($row['Status']) ? $row['Status'] : '';

    switch ($status) {
        case '0':
            if (isset($row['StatTempat']) && $row['StatTempat'] == '0') return 'Kosong - di Logistik';
            if (isset($row['StatTempat']) && $row['StatTempat'] == '1') return 'Kosong - di Aftap';
            return 'Kosong';

        case '1':
            if (isset($row['sah']) && $row['sah'] == '1') return 'Karantina';
            return 'Belum disahkan';

        case '2':
            return 'Sehat';

        case '3':
            return 'Keluar';

        case '4':
            return 'Rusak';

        case '5':
            return 'Rusak-Gagal';

        case '6':
            return 'Dimusnahkan';

        default:
            return '-';
    }
}

function generateNoTrans()
{
    return 'QC-' . date('YmdHis') . '-' . rand(100, 999);
}

/* =========================
   CRUD ACTION
========================= */
if (isset($_POST['simpan'])) {
    // Handle form submission for saving QC data
    // You can add your logic here to process the form data and save it to the database
    // For example, you can retrieve the form values using $_POST and perform database operations
    // After processing, you can set a success message or redirect as needed

    // var_dump($_POST); // For debugging purposes, you can remove this line in production
    $asal_utd = isset($_POST['asal_utd']) ? $_POST['asal_utd'] : '';

    $notrans    = generateNoTrans();
    $nokantong = isset($_POST['nokantong']) ? $_POST['nokantong'] : '';
    $tglqc = isset($_POST['tglqc']) ? $_POST['tglqc'] . ' ' . date('H:i:s') : date("Y-m-d H:i:s");
    $merk = isset($_POST['merk']) ? $_POST['merk'] : '';
    $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
    $gol_darah = isset($_POST['gol_darah']) ? $_POST['gol_darah'] : '';
    $rhesus = isset($_POST['rhesus']) ? $_POST['rhesus'] : '';
    $produk = isset($_POST['produk']) ? $_POST['produk'] : '';
    $tglaftap = isset($_POST['tglaftap']) ? $_POST['tglaftap'] : '';
    $tglpengolahan = isset($_POST['tglpengolahan']) ? $_POST['tglpengolahan'] : '';
    $kadaluwarsa = isset($_POST['kadaluwarsa']) ? $_POST['kadaluwarsa'] : '';
    $beratisi = isset($_POST['beratisi']) ? $_POST['beratisi'] : '';
    $beratkosong = isset($_POST['beratkosong']) ? $_POST['beratkosong'] : '';
    $beratjenis = isset($_POST['beratjenis']) ? $_POST['beratjenis'] : '';
    $antikogulan = isset($_POST['antikogulan']) ? $_POST['antikogulan'] : '';
    $volume_hasil = isset($_POST['volume_hasil']) ? $_POST['volume_hasil'] : '';
    $volhem = isset($_POST['volhem']) ? $_POST['volhem'] : '';
    $kadhb = isset($_POST['kadhb']) ? $_POST['kadhb'] : '';
    $hct = isset($_POST['hct']) ? $_POST['hct'] : '';
    $plasma = isset($_POST['plasma']) ? $_POST['plasma'] : '';
    $ttlhb = isset($_POST['ttlhb']) ? $_POST['ttlhb'] : '';
    $swirling = isset($_POST['swirling']) ? $_POST['swirling'] : '';
    $ph = isset($_POST['ph']) ? $_POST['ph'] : '';
    $hemolisis = isset($_POST['hemolisis']) ? $_POST['hemolisis'] : '';
    $hematokrit = isset($_POST['hematokrit']) ? $_POST['hematokrit'] : '0';
    $hemoglobin = isset($_POST['hemoglobin']) ? $_POST['hemoglobin'] : '';
    $kadwbc = isset($_POST['kadar_wbc']) ? $_POST['kadar_wbc'] : '0';
    $leukosit = isset($_POST['leukosit']) ? $_POST['leukosit'] : '0';
    $kadar_trombosit = isset($_POST['kadar_trombosit']) ? $_POST['kadar_trombosit'] : '0';
    $trombosit = isset($_POST['trombosit']) ? $_POST['trombosit'] : '0';
    $faktorviii = isset($_POST['faktorviii']) ? $_POST['faktorviii'] : '';
    $aerob = isset($_POST['aerob']) ? $_POST['aerob'] : '';
    $anaerob = isset($_POST['anaerob']) ? $_POST['anaerob'] : '';
    $petugas = isset($_POST['petugas']) ? $_POST['petugas'] : '';
    $dicek_oleh = isset($_POST['dicek_oleh']) ? $_POST['dicek_oleh'] : '';
    $disahkan_oleh = isset($_POST['disahkan_oleh']) ? $_POST['disahkan_oleh'] : '';
    $vi_hemolisis = isset($_POST['vi_hemolisis']) ? $_POST['vi_hemolisis'] : '0';
    $vi_lipemik = isset($_POST['vi_lipemik']) ? $_POST['vi_lipemik'] : '0';
    $vi_penggumpalan = isset($_POST['vi_penggumpalan']) ? $_POST['vi_penggumpalan'] : '0';
    $vi_warna = isset($_POST['vi_warna']) ? $_POST['vi_warna'] : '0';
    $vi_tdk = isset($_POST['vi_tdk']) ? $_POST['vi_tdk'] : '0';

    // Cek Table QC apakah sudah ada data dengan nokantong yang sama
    $qcek = mysql_query("SELECT * FROM qc WHERE nokantong='" . mysql_real_escape_string($nokantong) . "' LIMIT 1");
    $data_qc = mysql_fetch_assoc($qcek);

    if (empty($data_qc)) {
        // Insert new QC data
        $qinsert = mysql_query("
            INSERT INTO qc (
                notrans, qctgl, nokantong, merk, jenis, gol_darah, rhesus, produk, tglaftap, tglpengolahan, kadaluwarsa,
                berat_isi, berat_kosong, berat_jenis, antikogulan, volume, volhem, kadhb, hct, plasma, ttlhb, swirling, ph, hemolisis,
                hematokrit, hemoglobin, kad_wbc, leukosit, kadar_trombosit, trombosit, faktorviii, aerob, anaerob, qcuser, qcchecker, qcpengesah,
                v_hemolisis, v_lipemik, v_penggumpalan, v_warna, v_tdk
            ) VALUES (
                '" . mysql_real_escape_string($notrans) . "',
                '" . mysql_real_escape_string($tglqc) . "',
                '" . mysql_real_escape_string($nokantong) . "',
                '" . mysql_real_escape_string($merk) . "',
                '" . mysql_real_escape_string($jenis) . "',
                '" . mysql_real_escape_string($gol_darah) . "',
                '" . mysql_real_escape_string($rhesus) . "',
                '" . mysql_real_escape_string($produk) . "',
                '" . mysql_real_escape_string($tglaftap) . "',
                '" . mysql_real_escape_string($tglpengolahan) . "',
                '" . mysql_real_escape_string($kadaluwarsa) . "',
                '" . mysql_real_escape_string($beratisi) . "',
                '" . mysql_real_escape_string($beratkosong) . "',
                '" . mysql_real_escape_string($beratjenis) . "',
                '" . mysql_real_escape_string($antikogulan) . "',
                '" . mysql_real_escape_string($volume_hasil) . "',
                '" . mysql_real_escape_string($volhem) . "',
                '" . mysql_real_escape_string($kadhb) . "',
                '" . mysql_real_escape_string($hct) . "',
                '" . mysql_real_escape_string($plasma) . "',
                '" . mysql_real_escape_string($ttlhb) . "',
                '" . mysql_real_escape_string($swirling) . "',
                '" . mysql_real_escape_string($ph) . "',
                '" . mysql_real_escape_string($hemolisis) . "',
                '" . mysql_real_escape_string($hematokrit) . "',
                '" . mysql_real_escape_string($hemoglobin) . "',
                '" . mysql_real_escape_string($kadwbc) . "',
                '" . mysql_real_escape_string($leukosit) . "',
                '" . mysql_real_escape_string($kadar_trombosit) . "',
                '" . mysql_real_escape_string($trombosit) . "',
                '" . mysql_real_escape_string($faktorviii) . "',
                '" . mysql_real_escape_string($aerob) . "',
                '" . mysql_real_escape_string($anaerob) . "',
                '" . mysql_real_escape_string($petugas) . "',
                '" . mysql_real_escape_string($dicek_oleh) . "',
                '" . mysql_real_escape_string($disahkan_oleh) . "',
                '" . mysql_real_escape_string($vi_hemolisis) . "',
                '" . mysql_real_escape_string($vi_lipemik) . "',
                '" . mysql_real_escape_string($vi_penggumpalan) . "',
                '" . mysql_real_escape_string($vi_warna) . "',
                '" . mysql_real_escape_string($vi_tdk) . "'
            )
        ");

        if ($qinsert) {
            $pesan = 'Data QC berhasil disimpan.';
            $alert = 'success';

            // Cek apakah nokantong ada di stokkantong, jika ada update statusnya menjadi 1 (Sudah di QC)
            $qcek_stok = mysql_query("SELECT * FROM stokkantong WHERE nokantong='" . mysql_real_escape_string($nokantong) . "' LIMIT 1");

            // Jika nokantong tidak ada distokkantong, maka cukup update status QC di registrasi_qc menjadi 1
            $qcekRegistrasi = mysql_query("SELECT * FROM registrasi_qc WHERE nokantong='" . mysql_real_escape_string($nokantong) . "' LIMIT 1");

            if ($qcek_stok && mysql_num_rows($qcek_stok) > 0) {
                mysql_query("UPDATE stokkantong SET statQC='1' WHERE nokantong='" . mysql_real_escape_string($nokantong) . "'");
                mysql_query("UPDATE registrasi_qc SET up_data='1' WHERE nokantong='" . mysql_real_escape_string($nokantong) . "'");
            }

            if ($qcekRegistrasi && mysql_num_rows($qcekRegistrasi) > 0) {
                mysql_query("UPDATE registrasi_qc SET up_data='1' WHERE nokantong='" . mysql_real_escape_string($nokantong) . "'");
            }

            //=======Audit Trial====================================================================================
            $log_mdl  = 'QC';
            $log_aksi = 'Melakukan Uji Mutu dengan Nomor Sampel: ' . $nokantong;
            include("user_log.php");
            //=====================================================================================================
        } else {
            $pesan = 'Gagal menyimpan data QC: ' . mysql_error();
            $alert = 'danger';
        }
    } else {
        // Update existing QC data
        $qupdate = mysql_query("
            UPDATE qc SET
                qctgl='" . mysql_real_escape_string($tglqc) . "',
                berat_isi='" . mysql_real_escape_string($beratisi) . "',
                berat_kosong='" . mysql_real_escape_string($beratkosong) . "',
                berat_jenis='" . mysql_real_escape_string($beratjenis) . "',
                antikogulan='" . mysql_real_escape_string($antikogulan) . "',
                volume='" . mysql_real_escape_string($volume_hasil) . "',
                volhem='" . mysql_real_escape_string($volhem) . "',
                kadhb='" . mysql_real_escape_string($kadhb) . "',
                hct='" . mysql_real_escape_string($hct) . "',
                plasma='" . mysql_real_escape_string($plasma) . "',
                ttlhb='" . mysql_real_escape_string($ttlhb) . "',
                swirling='" . mysql_real_escape_string($swirling) . "',
                ph='" . mysql_real_escape_string($ph) . "',
                hemolisis='" . mysql_real_escape_string($hemolisis) . "',
                hematokrit='" . mysql_real_escape_string($hematokrit) . "',
                hemoglobin='" . mysql_real_escape_string($hemoglobin) . "',
                kad_wbc='" . mysql_real_escape_string($kadwbc) . "',
                leukosit='" . mysql_real_escape_string($leukosit) . "',
                kadar_trombosit='" . mysql_real_escape_string($kadar_trombosit) . "',
                trombosit='" . mysql_real_escape_string($trombosit) . "',
                faktorviii='" . mysql_real_escape_string($faktorviii) . "',
                aerob='" . mysql_real_escape_string($aerob) . "',
                anaerob='" . mysql_real_escape_string($anaerob) . "',
                qcchecker='" . mysql_real_escape_string($dicek_oleh) . "',
                qcpengesah='" . mysql_real_escape_string($disahkan_oleh) . "',
                v_hemolisis='" . mysql_real_escape_string($vi_hemolisis) . "',
                v_lipemik='" . mysql_real_escape_string($vi_lipemik) . "',
                v_penggumpalan='" . mysql_real_escape_string($vi_penggumpalan) . "',
                v_warna='" . mysql_real_escape_string($vi_warna) . "',
                v_tdk='" . mysql_real_escape_string($vi_tdk) . "',
                petugas_update='" . mysql_real_escape_string($petugas) . "'
            WHERE nokantong='" . mysql_real_escape_string($nokantong) . "'
        ");

        if ($qupdate) {
            //=======Audit Trial====================================================================================
            $log_mdl  = 'QC';
            $log_aksi = 'Melakukan pembaruan Data QC dengan Nomor Sampel: ' . $nokantong;
            include("user_log.php");
            //=====================================================================================================

            // Cek apakah nokantong ada di stokkantong, jika ada update statusnya menjadi 1 (Sudah di QC)
            $qcek_stok = mysql_query("SELECT * FROM stokkantong WHERE nokantong='" . mysql_real_escape_string($nokantong) . "' LIMIT 1");

            // Jika nokantong tidak ada distokkantong, maka cukup update status QC di registrasi_qc menjadi 1
            $qcekRegistrasi = mysql_query("SELECT * FROM registrasi_qc WHERE nokantong='" . mysql_real_escape_string($nokantong) . "' LIMIT 1");

            if ($qcek_stok && mysql_num_rows($qcek_stok) > 0) {
                mysql_query("UPDATE stokkantong SET statQC='1' WHERE nokantong='" . mysql_real_escape_string($nokantong) . "'");
                mysql_query("UPDATE registrasi_qc SET up_data='1' WHERE nokantong='" . mysql_real_escape_string($nokantong) . "'");
            }

            if ($qcekRegistrasi && mysql_num_rows($qcekRegistrasi) > 0) {
                mysql_query("UPDATE registrasi_qc SET up_data='1' WHERE nokantong='" . mysql_real_escape_string($nokantong) . "'");
            }

            $pesan = 'Data QC berhasil diperbarui.';
            $alert = 'success';
        } else {
            $pesan = 'Gagal memperbarui data QC: ' . mysql_error();
            $alert = 'danger';
        }
    }
}

if (isset($_POST['cari'])) {
    $nkt = strtoupper(trim($_POST['nokantong']));

    if ($nkt != '') {
        $qreg = mysql_query("
            SELECT *
            FROM registrasi_qc
            WHERE nokantong='" . mysql_real_escape_string($nkt) . "'
            LIMIT 1
        ");

        if ($qreg) {
            $data_reg = mysql_fetch_assoc($qreg);
        }

        $qqc_lama = mysql_query("SELECT * FROM qc WHERE nokantong='" . mysql_real_escape_string($nkt) . "' LIMIT 1");
        if ($qqc_lama) {
            $data_qc_lama = mysql_fetch_assoc($qqc_lama);
        }

        // Cek Jika No Kantong ada di Registrasi QC atau sebelumnya pernah di QC
        if ($data_reg || $data_qc_lama) {
            if ($data_qc_lama) {
                $qc_existing_data = true;
                $qc_tglqc = isset($data_qc_lama['qctgl']) ? substr($data_qc_lama['qctgl'], 0, 10) : $qc_tglqc;
                $qc_beratisi = isset($data_qc_lama['berat_isi']) ? $data_qc_lama['berat_isi'] : '';
                $qc_beratkosong = isset($data_qc_lama['berat_kosong']) ? $data_qc_lama['berat_kosong'] : '';
                $qc_beratjenis = isset($data_qc_lama['berat_jenis']) ? $data_qc_lama['berat_jenis'] : '';
                $qc_antikogulan = isset($data_qc_lama['antikogulan']) ? $data_qc_lama['antikogulan'] : '';
                $qc_volume_hasil = isset($data_qc_lama['volume']) ? $data_qc_lama['volume'] : '';
                $qc_volhem = isset($data_qc_lama['volhem']) ? $data_qc_lama['volhem'] : '';
                $qc_kadhb = isset($data_qc_lama['kadhb']) ? $data_qc_lama['kadhb'] : '';
                $qc_hct = isset($data_qc_lama['hct']) ? $data_qc_lama['hct'] : '';
                $qc_plasma = isset($data_qc_lama['plasma']) ? $data_qc_lama['plasma'] : '';
                $qc_ttlhb = isset($data_qc_lama['ttlhb']) ? $data_qc_lama['ttlhb'] : '';
                $qc_swirling = isset($data_qc_lama['swirling']) ? $data_qc_lama['swirling'] : '';
                $qc_ph = isset($data_qc_lama['ph']) ? $data_qc_lama['ph'] : '';
                $qc_hemolisis = isset($data_qc_lama['hemolisis']) ? $data_qc_lama['hemolisis'] : '';
                $qc_hematokrit = isset($data_qc_lama['hematokrit']) ? $data_qc_lama['hematokrit'] : '';
                $qc_hemoglobin = isset($data_qc_lama['hemoglobin']) ? $data_qc_lama['hemoglobin'] : '';
                $qc_kadar_wbc = isset($data_qc_lama['kad_wbc']) ? $data_qc_lama['kad_wbc'] : '';
                $qc_leukosit = isset($data_qc_lama['leukosit']) ? $data_qc_lama['leukosit'] : '';
                $qc_kadar_trombosit = isset($data_qc_lama['kadar_trombosit']) ? $data_qc_lama['kadar_trombosit'] : '';
                $qc_trombosit = isset($data_qc_lama['trombosit']) ? $data_qc_lama['trombosit'] : '';
                $qc_faktorviii = isset($data_qc_lama['faktorviii']) ? $data_qc_lama['faktorviii'] : '';
                $qc_aerob = isset($data_qc_lama['aerob']) ? $data_qc_lama['aerob'] : '';
                $qc_anaerob = isset($data_qc_lama['anaerob']) ? $data_qc_lama['anaerob'] : '';
                $qc_dicek_oleh = isset($data_qc_lama['qcchecker']) ? $data_qc_lama['qcchecker'] : '';
                $qc_disahkan_oleh = isset($data_qc_lama['qcpengesah']) ? $data_qc_lama['qcpengesah'] : '';
                $qc_vi_hemolisis = isset($data_qc_lama['v_hemolisis']) ? $data_qc_lama['v_hemolisis'] : '0';
                $qc_vi_lipemik = isset($data_qc_lama['v_lipemik']) ? $data_qc_lama['v_lipemik'] : '0';
                $qc_vi_penggumpalan = isset($data_qc_lama['v_penggumpalan']) ? $data_qc_lama['v_penggumpalan'] : '0';
                $qc_vi_warna = isset($data_qc_lama['v_warna']) ? $data_qc_lama['v_warna'] : '0';
                $qc_vi_tdk = isset($data_qc_lama['v_tdk']) ? $data_qc_lama['v_tdk'] : '0';

                if ($merk == '') {
                    $merk = isset($data_qc_lama['merk']) ? $data_qc_lama['merk'] : $merk;
                }
                if ($jenis == '') {
                    $jenis = isset($data_qc_lama['jenis']) ? $data_qc_lama['jenis'] : $jenis;
                }
                if ($goldarah == '') {
                    $goldarah = isset($data_qc_lama['gol_darah']) ? $data_qc_lama['gol_darah'] : $goldarah;
                }
                if ($rhesus == '') {
                    $rhesus = isset($data_qc_lama['rhesus']) ? $data_qc_lama['rhesus'] : $rhesus;
                }
                if ($produk_nama == '') {
                    $produk_nama = isset($data_qc_lama['produk']) ? $data_qc_lama['produk'] : $produk_nama;
                }
                if ($tglpengolahan == '') {
                    $tglpengolahan = isset($data_qc_lama['tglpengolahan']) ? $data_qc_lama['tglpengolahan'] : $tglpengolahan;
                }
                if ($kadaluwarsa == '') {
                    $kadaluwarsa = isset($data_qc_lama['kadaluwarsa']) ? $data_qc_lama['kadaluwarsa'] : $kadaluwarsa;
                }
                if ($tglaftap == '') {
                    $tglaftap = isset($data_qc_lama['tglaftap']) ? $data_qc_lama['tglaftap'] : $tglaftap;
                }
            }

            // Cek apakah No Kantong sudah di QC
            $cek_qc = $data_reg['up_data'];
            if ($cek_qc == '1') {
                $pesan = 'No. Kantong ' . h($nkt) . ' sudah pernah di QC.';
                $alert = 'warning';
            }

            // panggil data user level qc untuk ditampilkan pada form input qc
            $dataQc = array();

            $qcek_sah = mysql_query("SELECT nama_lengkap FROM user WHERE level LIKE '%qc%' ORDER BY nama_lengkap ASC");

            while ($row = mysql_fetch_array($qcek_sah)) {
                $dataQc[] = $row['nama_lengkap'];
            }

            if ($data_reg && isset($data_reg['jns_asal']) && $data_reg['jns_asal'] == '2') {
                $sumber_data = 'registrasi_qc';

                $merk           = isset($data_reg['merk']) ? $data_reg['merk'] : '';
                $jenis          = isset($data_reg['jenis_produk']) ? $data_reg['jenis_produk'] : '';
                $produk_nama   = isset($data_reg['produk']) ? $data_reg['produk'] : '';
                $goldarah      = isset($data_reg['goldarah']) ? $data_reg['goldarah'] : '';
                $rhesus        = isset($data_reg['rhesus']) ? $data_reg['rhesus'] : '';
                $tglaftap      = isset($data_reg['tglaftap']) ? $data_reg['tglaftap'] : '';
                $kadaluwarsa   = isset($data_reg['kadaluwarsa']) ? $data_reg['kadaluwarsa'] : '';
                $tglpengolahan = isset($data_reg['tgl_pengolahan']) ? $data_reg['tgl_pengolahan'] : '';
                $volume        = isset($data_reg['volume']) ? $data_reg['volume'] : '';
                $posisi_kantong = posisiKantong($nkt);

                // AMBIL asal_utd untuk dicari ditable utd berdasarkan id
                $qutd = mysql_query("
                    SELECT nama
                    FROM utd
                    WHERE id='" . mysql_real_escape_string($data_reg['asal_utd']) . "'
                    LIMIT 1
                ");

                $asal_utd = '';
                if ($qutd) {
                    $data_utd = mysql_fetch_assoc($qutd);
                    $asal_utd = isset($data_utd['nama']) ? $data_utd['nama'] : '';
                }

                $qproduk = mysql_query("
                    SELECT *
                    FROM produk
                    WHERE Nama='" . mysql_real_escape_string($produk_nama) . "'
                    LIMIT 1
                ");

                if ($qproduk) {
                    $data_produk = mysql_fetch_assoc($qproduk);
                    if ($data_produk) {
                        $produk_master_found = true;
                        $produk_lengkap = isset($data_produk['lengkap']) ? $data_produk['lengkap'] : '';
                        $beratjenis     = isset($data_produk['beratjenis']) ? $data_produk['beratjenis'] : '';
                        $vol_min        = isset($data_produk['vol_min']) ? $data_produk['vol_min'] : '';
                        $vol_max        = isset($data_produk['vol_max']) ? $data_produk['vol_max'] : '';
                    }
                }

                $qmk = mysql_query("
                        SELECT *
                        FROM master_kantong_qc
                        WHERE merk='" . mysql_real_escape_string($merk) . "'
                          AND jenis='" . mysql_real_escape_string($jenis) . "'
                          AND vol='" . mysql_real_escape_string($volume) . "'
                        LIMIT 1
                    ");

                if ($qmk) {
                    $data_mk = mysql_fetch_assoc($qmk);
                }

                if ($data_mk) {
                    $master_kantong_found = true;

                    $kantongke = strtoupper(substr($nkt, -1));
                    switch ($kantongke) {
                        case 'A':
                            $beratkosong = isset($data_mk['berat_ku']) ? $data_mk['berat_ku'] : '';
                            break;
                        case 'B':
                            $beratkosong = isset($data_mk['berat_s1']) ? $data_mk['berat_s1'] : '';
                            break;
                        case 'C':
                            $beratkosong = isset($data_mk['berat_s2']) ? $data_mk['berat_s2'] : '';
                            break;
                        case 'D':
                            $beratkosong = isset($data_mk['berat_s3']) ? $data_mk['berat_s3'] : '';
                            break;
                        case 'E':
                            $beratkosong = isset($data_mk['berat_s4']) ? $data_mk['berat_s4'] : '';
                            break;
                        case 'F':
                            $beratkosong = isset($data_mk['berat_s5']) ? $data_mk['berat_s5'] : '';
                            break;
                        case 'G':
                            $beratkosong = isset($data_mk['berat_s6']) ? $data_mk['berat_s6'] : '';
                            break;
                        case 'H':
                            $beratkosong = isset($data_mk['berat_s7']) ? $data_mk['berat_s7'] : '';
                            break;
                        default:
                            $beratkosong = '';
                            break;
                    }

                    $antikogulan = isset($data_mk['antikoagulant']) ? $data_mk['antikoagulant'] : '';
                }
            } elseif ($data_reg) {
                $sumber_data = 'stokkantong';

                $qstok = mysql_query("
                    SELECT *
                    FROM stokkantong
                    WHERE nokantong='" . mysql_real_escape_string($nkt) . "'
                    LIMIT 1
                ");

                if ($qstok) {
                    $data_stok = mysql_fetch_assoc($qstok);
                }

                if ($data_stok) {
                    $merk           = isset($data_stok['merk']) ? $data_stok['merk'] : '';
                    $jenis          = isset($data_stok['jenis']) ? $data_stok['jenis'] : '';
                    $produk_nama    = isset($data_stok['produk']) ? $data_stok['produk'] : '';
                    $goldarah       = isset($data_stok['gol_darah']) ? $data_stok['gol_darah'] : '';
                    $rhesus         = isset($data_stok['RhesusDrh']) ? $data_stok['RhesusDrh'] : '';
                    $tglaftap       = isset($data_stok['tgl_Aftap']) ? $data_stok['tgl_Aftap'] : '';
                    $kadaluwarsa    = isset($data_stok['kadaluwarsa']) ? $data_stok['kadaluwarsa'] : '';
                    $tglpengolahan  = isset($data_stok['tglpengolahan']) ? $data_stok['tglpengolahan'] : '';
                    $volume         = isset($data_stok['volumeasal']) ? $data_stok['volumeasal'] : '';
                    $posisi_kantong = posisiKantong($nkt);

                    $qproduk = mysql_query("
                        SELECT *
                        FROM produk
                        WHERE Nama='" . mysql_real_escape_string($produk_nama) . "'
                        LIMIT 1
                    ");

                    if ($qproduk) {
                        $data_produk = mysql_fetch_assoc($qproduk);
                        if ($data_produk) {
                            $produk_master_found = true;
                            $produk_lengkap = isset($data_produk['lengkap']) ? $data_produk['lengkap'] : '';
                            $beratjenis     = isset($data_produk['beratjenis']) ? $data_produk['beratjenis'] : '';
                            $vol_min        = isset($data_produk['vol_min']) ? $data_produk['vol_min'] : '';
                            $vol_max        = isset($data_produk['vol_max']) ? $data_produk['vol_max'] : '';
                        }
                    }

                    $qmk = mysql_query("
                        SELECT *
                        FROM master_kantong_qc
                        WHERE merk='" . mysql_real_escape_string($merk) . "'
                          AND jenis='" . mysql_real_escape_string($jenis) . "'
                          AND vol='" . mysql_real_escape_string($volume) . "'
                        LIMIT 1
                    ");

                    if ($qmk) {
                        $data_mk = mysql_fetch_assoc($qmk);
                    }

                    if ($data_mk) {
                        $master_kantong_found = true;

                        $kantongke = strtoupper(substr($nkt, -1));
                        switch ($kantongke) {
                            case 'A':
                                $beratkosong = isset($data_mk['berat_ku']) ? $data_mk['berat_ku'] : '';
                                break;
                            case 'B':
                                $beratkosong = isset($data_mk['berat_s1']) ? $data_mk['berat_s1'] : '';
                                break;
                            case 'C':
                                $beratkosong = isset($data_mk['berat_s2']) ? $data_mk['berat_s2'] : '';
                                break;
                            case 'D':
                                $beratkosong = isset($data_mk['berat_s3']) ? $data_mk['berat_s3'] : '';
                                break;
                            case 'E':
                                $beratkosong = isset($data_mk['berat_s4']) ? $data_mk['berat_s4'] : '';
                                break;
                            case 'F':
                                $beratkosong = isset($data_mk['berat_s5']) ? $data_mk['berat_s5'] : '';
                                break;
                            case 'G':
                                $beratkosong = isset($data_mk['berat_s6']) ? $data_mk['berat_s6'] : '';
                                break;
                            case 'H':
                                $beratkosong = isset($data_mk['berat_s7']) ? $data_mk['berat_s7'] : '';
                                break;
                            default:
                                $beratkosong = '';
                                break;
                        }

                        $antikogulan = isset($data_mk['antikoagulant']) ? $data_mk['antikoagulant'] : '';
                    }
                }
            }
        } else {
            $alert = 'danger';
            $pesan = 'Data tidak ditemukan pada registrasi QC';
        }
    } else {
        $alert = 'danger';
        $pesan = 'Nomor kantong wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input QC Kantong</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
    body {
        background: #f4f7fb;
    }

    .label-kecil {
        background: mistyrose;
        width: 240px;
        white-space: nowrap;
    }

    .label-judul {
        background-color: #e46464;
    }
    </style>

    <script type="text/javascript">
    function validateForm() {
        // Validasi minimal satu checkbox visual harus dipilih
        const visualCheckboxes = document.querySelectorAll('.visual-option');
        const viTdk = document.getElementById('vi_tdk');

        const adaYangDipilih = [...visualCheckboxes].some(cb => cb.checked) || viTdk.checked;

        if (!adaYangDipilih) {
            alert('Minimal satu pilihan untuk "Perubahan Visual" harus dipilih');
            return false;
        }

        // Validasi select fields tidak boleh kosong
        const selectFields = document.querySelectorAll('select[required]');
        for (let select of selectFields) {
            if (select.value === '') {
                alert('Semua pilihan wajib dipilih. Harap lengkapi: ' + select.name);
                select.focus();
                return false;
            }
        }

        return true;
    }

    function getNumberValue(id) {
        var el = document.getElementById(id);
        if (!el || el.value === '') {
            return 0;
        }

        var value = parseFloat(el.value);
        return isNaN(value) ? 0 : value;
    }

    function hitung() {
        var beratisi = getNumberValue('beratisi');
        var beratkosong = getNumberValue('beratkosong');
        var beratjenis = getNumberValue('beratjenis');
        var antikogulan = getNumberValue('antikogulan');

        var total = beratisi - beratkosong;
        var total1 = 0;
        var total2 = 0;

        if (beratjenis != 0) {
            total1 = total / beratjenis;
            total2 = total1 - antikogulan;
        }

        var volume = document.getElementById('volume_hasil');
        if (volume) {
            volume.value = Math.round(total2);
        }

        var hct = getNumberValue('hct');
        var plasma = getNumberValue('plasma');
        var ttlhb = getNumberValue('ttlhb');

        var totalhct = 100 - hct;
        var hct1 = document.getElementById('hct1');
        if (hct1) {
            hct1.value = totalhct;
        }

        var hemolisis = 0;
        if (ttlhb != 0) {
            hemolisis = (totalhct * plasma) / ttlhb;
        }

        var hem = document.getElementById('hemolisis');
        if (hem) {
            hem.value = hemolisis.toFixed(2);
        }

        var volhemEl = document.getElementById('volhem');
        var tVolhem1 = (total2 + antikogulan) / 100;

        if (volhemEl) {
            volhemEl.value = tVolhem1.toFixed(2);
        }

        var kadhb = getNumberValue('kadhb');
        var hemoglobin = tVolhem1 * kadhb;
        var hgb = document.getElementById('hemoglobin');
        if (hgb) {
            hgb.value = hemoglobin.toFixed(2);
        }

        var kadarwbc = getNumberValue('kadar_wbc');
        var volume_fisik = Math.round(total2);
        var leuko = volume_fisik * kadarwbc;
        var leukosit = document.getElementById('leukosit');
        if (leukosit) {
            leukosit.value = leuko.toFixed(2);
        }

        var kadar_trombosit = getNumberValue('kadar_trombosit');
        var trombo = (volume_fisik * kadar_trombosit) / 1000;
        var trombosit = document.getElementById('trombosit');
        if (trombosit) {
            trombosit.value = trombo.toFixed(2);
        }
    }
    </script>
</head>

<body onload="document.input_qc.nokantong.focus();">
    <div class="container-fluid p-3">

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Input Kantong QC</h4>
                <a href="javascript:history.back()" class="btn btn-light btn-sm">Kembali</a>
            </div>

            <div class="card-body">

                <?php if ($pesan != '') { ?>
                <div class="alert alert-<?php echo h($alert); ?> mb-3">
                    <?php echo $pesan; ?>
                </div>
                <?php } ?>

                <form name="input_qc" method="post" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="nokantong" class="form-label">Input Nomor Kantong</label>
                            <input type="text" class="form-control" name="nokantong" id="nokantong"
                                style="text-transform:uppercase" minlength="5" value="<?php echo h($nkt); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success" type="submit" name="cari">Proses</button>
                        </div>
                    </div>
                </form>

                <?php if ($data_reg || $data_qc_lama) { ?>

                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        Informasi Kantong
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <?php if ($sumber_data == 'stokkantong') { ?>

                            <div class="col-md-6">
                                <table class="table table-bordered mb-0">
                                    <tr>
                                        <td class="label-kecil">No. Kantong</td>
                                        <td><?php echo h($nkt); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Status Kantong</td>
                                        <td><?php echo h(statusKantong($data_stok)); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Posisi Kantong</td>
                                        <td><?php echo h($posisi_kantong); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Merk</td>
                                        <td><?php echo h($merk != '' ? $merk : '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Produk</td>
                                        <td><?php echo h($produk_nama != '' ? $produk_nama : '-'); ?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered mb-0">
                                    <tr>
                                        <td class="label-kecil">Gol Darah & Rhesus</td>
                                        <td>
                                            <?php
                                                    $gol = ($goldarah != '' ? $goldarah : '-');
                                                    $rh  = ($rhesus != '' ? $rhesus : '-');
                                                    echo h($gol . ' (' . $rh . ')');
                                                    ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Jenis & Volume Kantong Kosong</td>
                                        <td><?php echo h(jenisKantong($jenis)) . ' ' . h($volume != '' ? $volume : '-'); ?>
                                            ml</td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Aftap</td>
                                        <td><?php echo h(tglIndo($tglaftap)); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Pengolahan</td>
                                        <td><?php echo h(tglIndo($tglpengolahan)); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Kedaluwarsa Produk</td>
                                        <td><?php echo h(tglIndo($kadaluwarsa)); ?></td>
                                    </tr>
                                </table>
                            </div>

                            <?php } else { ?>

                            <div class="col-md-6">
                                <table class="table table-bordered mb-0">
                                    <tr>
                                        <td class="label-kecil">No. Kantong</td>
                                        <td><?php echo h(isset($data_reg['nokantong']) ? $data_reg['nokantong'] : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Merk</td>
                                        <td><?php echo h(isset($data_reg['merk']) ? $data_reg['merk'] : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Produk</td>
                                        <td><?php echo h(isset($data_reg['produk']) ? $data_reg['produk'] : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Jenis Kantong</td>
                                        <td><?php echo h(jenisKantong($data_reg['jenis_produk']) ? jenisKantong($data_reg['jenis_produk']) : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Volume Kantong Kosong</td>
                                        <td><?php echo h(isset($data_reg['volume']) ? $data_reg['volume'] : '-'); ?> ml
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Golongan Darah</td>
                                        <td><?= h(isset($data_reg['goldarah']) ? $data_reg['goldarah'] : '-') . ' ' . h(isset($data_reg['rhesus']) ? $data_reg['rhesus'] : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Posisi Kantong</td>
                                        <td><?php echo h($posisi_kantong); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Suhu</td>
                                        <td><?php echo h(isset($data_reg['suhu']) ? $data_reg['suhu'] : '-'); ?> &deg;C
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered mb-0">
                                    <tr>
                                        <td class="label-kecil">Asal UTD</td>
                                        <td><?php echo h(isset($asal_utd) ? $asal_utd : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Aftap</td>
                                        <td><?php echo h(tglIndo(isset($data_reg['tglaftap']) ? $data_reg['tglaftap'] : '')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Pengolahan</td>
                                        <td><?php echo h(tglIndo(isset($data_reg['tgl_pengolahan']) ? $data_reg['tgl_pengolahan'] : '')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Kedaluwarsa Produk</td>
                                        <td><?php echo h(tglIndo(isset($data_reg['kadaluwarsa']) ? $data_reg['kadaluwarsa'] : '')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Petugas Serah</td>
                                        <td><?php echo h(isset($data_reg['petugas_serah']) ? $data_reg['petugas_serah'] : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Petugas Terima</td>
                                        <td><?php echo h(isset($data_reg['petugas_terima']) ? $data_reg['petugas_terima'] : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Terima Sampel</td>
                                        <td><?php echo h(tglIndo(isset($data_reg['tgl']) ? $data_reg['tgl'] : '')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Catatan</td>
                                        <td><?php echo h(isset($data_reg['catatan']) && $data_reg['catatan'] != '' ? $data_reg['catatan'] : '-'); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        PEMERIKSAAN
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <input type="hidden" name="nokantong" value="<?php echo h($nkt); ?>">
                            <input type="hidden" name="produk" value="<?php echo h($produk_nama); ?>">
                            <input type="hidden" name="jenis" value="<?php echo h($jenis); ?>">
                            <input type="hidden" name="merk" value="<?php echo h($merk); ?>">
                            <input type="hidden" name="gol_darah" value="<?php echo h($goldarah); ?>">
                            <input type="hidden" name="rhesus" value="<?php echo h($rhesus); ?>">
                            <input type="hidden" name="tglaftap" value="<?php echo h($tglaftap); ?>">
                            <input type="hidden" name="tglpengolahan" value="<?php echo h($tglpengolahan); ?>">
                            <input type="hidden" name="kadaluwarsa" value="<?php echo h($kadaluwarsa); ?>">
                            <input type="hidden" name="asal_utd" value="<?php echo h($asal_utd); ?>">

                            <input type="hidden" name="ihbs" value="Non Reaktif">
                            <input type="hidden" name="ihcv" value="Non Reaktif">
                            <input type="hidden" name="ihiv" value="Non Reaktif">
                            <input type="hidden" name="isyp" value="Non Reaktif">

                            <?php
                                $produk_key = strtoupper(trim($produk_nama));
                                switch ($produk_key) {
                                    case 'WB':
                                    case 'WB 450':
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = true;
                                        $tampil_volume = true;
                                        $tampil_menu_hemolisis = true;
                                        $tampil_hct = true;
                                        $tampil_plasma = true;
                                        $tampil_ttlhb = true;
                                        $tampil_hemolisis = true;
                                        $tampil_volhem = true;
                                        $tampil_kadhb = true;
                                        $tampil_hemoglobin = true;
                                        $tampil_visual = true;
                                        $tampil_bakteri = true;
                                        $tampil_hsl_fisik = false;
                                        $tampil_hsl_hematologi = false;
                                        $tampil_hsl_qc = false;
                                        $tampil_hematokrit = false;
                                        $tampil_leukosit = false;
                                        $tampil_ph = false;
                                        $tampil_swirling = false;
                                        $tampil_trombosit = false;
                                        $tampil_menu_hematologi = true;
                                        $tampil_menu_koagulasi = false;
                                        break;

                                    case 'PRC':
                                    case 'PRC 450':
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = false;
                                        $tampil_volume = true;
                                        $tampil_menu_hemolisis = true;
                                        $tampil_hct = true;
                                        $tampil_plasma = true;
                                        $tampil_ttlhb = true;
                                        $tampil_hemolisis = true;
                                        $tampil_volhem = true;
                                        $tampil_kadhb = true;
                                        $tampil_hemoglobin = true;
                                        $tampil_visual = true;
                                        $tampil_bakteri = true;
                                        $tampil_hsl_fisik = false;
                                        $tampil_hsl_hematologi = false;
                                        $tampil_hsl_qc = false;
                                        $tampil_hematokrit = true;
                                        $tampil_leukosit = false;
                                        $tampil_ph = false;
                                        $tampil_swirling = false;
                                        $tampil_trombosit = false;
                                        $tampil_menu_hematologi = true;
                                        $tampil_menu_koagulasi = false;
                                        break;

                                    case 'PRC LEUCODEPLETED':
                                    case 'PRC LEUCOREDUCTION':
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = false;
                                        $tampil_volume = true;
                                        $tampil_menu_hemolisis = true;
                                        $tampil_hct = true;
                                        $tampil_plasma = true;
                                        $tampil_ttlhb = true;
                                        $tampil_hemolisis = true;
                                        $tampil_volhem = true;
                                        $tampil_kadhb = true;
                                        $tampil_hemoglobin = true;
                                        $tampil_visual = true;
                                        $tampil_bakteri = true;
                                        $tampil_hsl_fisik = false;
                                        $tampil_hsl_hematologi = false;
                                        $tampil_hsl_qc = false;
                                        $tampil_hematokrit = true;
                                        $tampil_leukosit = true;
                                        $tampil_ph = false;
                                        $tampil_swirling = false;
                                        $tampil_trombosit = false;
                                        $tampil_menu_hematologi = true;
                                        $tampil_menu_koagulasi = false;
                                        break;

                                    case 'TC':
                                    case 'TC AFERESIS':
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = false;
                                        $tampil_volume = true;
                                        $tampil_menu_hemolisis = false;
                                        $tampil_hct = false;
                                        $tampil_plasma = false;
                                        $tampil_ttlhb = false;
                                        $tampil_hemolisis = false;
                                        $tampil_volhem = false;
                                        $tampil_kadhb = false;
                                        $tampil_hemoglobin = false;
                                        $tampil_visual = true;
                                        $tampil_bakteri = true;
                                        $tampil_hsl_fisik = false;
                                        $tampil_hsl_hematologi = false;
                                        $tampil_hsl_qc = false;
                                        $tampil_hematokrit = false;
                                        $tampil_leukosit = true;
                                        $tampil_ph = true;
                                        $tampil_swirling = true;
                                        $tampil_trombosit = true;
                                        $tampil_menu_hematologi = true;
                                        $tampil_menu_koagulasi = false;
                                        break;

                                    case 'FFP':
                                    case 'AHF':
                                    case 'PLASMA AFERESIS':
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = false;
                                        $tampil_volume = true;
                                        $tampil_menu_hemolisis = false;
                                        $tampil_hct = false;
                                        $tampil_plasma = false;
                                        $tampil_ttlhb = false;
                                        $tampil_hemolisis = false;
                                        $tampil_volhem = false;
                                        $tampil_kadhb = false;
                                        $tampil_hemoglobin = false;
                                        $tampil_visual = true;
                                        $tampil_hematokrit = false;
                                        $tampil_hsl_fisik = false;
                                        $tampil_hsl_hematologi = false;
                                        $tampil_hsl_qc = false;
                                        $tampil_bakteri = false;
                                        $tampil_leukosit = false;
                                        $tampil_ph = false;
                                        $tampil_swirling = false;
                                        $tampil_trombosit = false;
                                        $tampil_menu_hematologi = false;
                                        $tampil_menu_koagulasi = true;
                                        break;

                                    default:
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = true;
                                        $tampil_volume = true;
                                        $tampil_menu_hemolisis = true;
                                        $tampil_hct = true;
                                        $tampil_plasma = true;
                                        $tampil_ttlhb = true;
                                        $tampil_hemolisis = true;
                                        $tampil_volhem = true;
                                        $tampil_kadhb = true;
                                        $tampil_hemoglobin = true;
                                        $tampil_visual = true;
                                        $tampil_bakteri = true;
                                        $tampil_hsl_fisik = false;
                                        $tampil_hsl_hematologi = false;
                                        $tampil_hsl_qc = false;
                                        $tampil_hematokrit = false;
                                        $tampil_leukosit = false;
                                        $tampil_ph = false;
                                        $tampil_swirling = false;
                                        $tampil_trombosit = false;
                                        $tampil_menu_hematologi = true;
                                        $tampil_menu_koagulasi = false;
                                        break;
                                }
                                ?>
                            <div class="row g-3">
                                <div class="card mb-4 col-md-4">
                                    <div class="card-header bg-primary text-white">
                                        PEMERIKSAAN FISIK
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <td class="label-kecil">Tanggal Pemeriksaan</td>
                                                <td>
                                                    <input type="date" name="tglqc" id="tanggal" class="form-control"
                                                        value="<?php echo h($qc_tglqc); ?>" required>
                                                </td>
                                            </tr>

                                            <?php if ($tampil_beratjenis) { ?>
                                            <tr>
                                                <td class="label-kecil">Berat Jenis</td>
                                                <td>
                                                    <input type="text" name="beratjenis" id="beratjenis"
                                                        class="form-control"
                                                        value="<?php echo h($qc_beratjenis != '' ? $qc_beratjenis : $beratjenis); ?>"
                                                        onchange="hitung()" required
                                                        <?php echo ($beratjenis != '' ? 'readonly="readonly"' : ''); ?>>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_beratkosong) { ?>
                                            <tr>
                                                <td class="label-kecil">Berat Kantong Kosong</td>
                                                <td>
                                                    <input type="text" name="beratkosong" id="beratkosong"
                                                        class="form-control"
                                                        value="<?php echo h($qc_beratkosong != '' ? $qc_beratkosong : $beratkosong); ?>"
                                                        onchange="hitung()" required
                                                        <?php echo ($beratkosong != '' ? 'readonly="readonly"' : ''); ?>>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_antikogulan) { ?>
                                            <tr>
                                                <td class="label-kecil">Antikogulan</td>
                                                <td>
                                                    <input type="text" name="antikogulan" id="antikogulan"
                                                        class="form-control"
                                                        value="<?php echo h($qc_antikogulan != '' ? $qc_antikogulan : $antikogulan); ?>"
                                                        onchange="hitung()" required
                                                        <?php echo ($antikogulan != '' ? 'readonly="readonly"' : ''); ?>>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_volume) { ?>
                                            <tr>
                                                <td class="label-kecil">Berat Kantong Isi</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="beratisi" id="beratisi"
                                                            class="form-control" placeholder="Isi angka"
                                                            value="<?php echo h($qc_beratisi); ?>" onchange="hitung()"
                                                            required>
                                                        <span class="input-group-text">gram</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-kecil">Hasil Perhitungan Volume</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="volume_hasil" id="volume_hasil"
                                                            class="form-control" readonly="readonly"
                                                            value="<?php echo h($qc_volume_hasil); ?>"
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">mL</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_ph) { ?>
                                            <tr>
                                                <td class="label-kecil">pH</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="ph" id="ph" class="form-control"
                                                            value="<?php echo h($qc_ph); ?>" required>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_swirling) { ?>
                                            <tr>
                                                <td class="label-kecil">Swirling</td>
                                                <td>
                                                    <select name="swirling" id="swirling" class="form-select" required>
                                                        <option value="">-</option>
                                                        <option value="0"
                                                            <?php echo ($qc_swirling == '0' ? 'selected' : ''); ?>>
                                                            Ada</option>
                                                        <option value="1"
                                                            <?php echo ($qc_swirling == '1' ? 'selected' : ''); ?>>
                                                            Tidak Ada</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_visual) { ?>
                                            <tr>
                                                <td class="label-kecil">Perubahan Visual</td>
                                                <td>
                                                    <label>
                                                        <input type="checkbox" class="visual-option" name="vi_hemolisis"
                                                            value="1"
                                                            <?php echo ($qc_vi_hemolisis == '1' ? 'checked' : ''); ?>>
                                                        Hemolisis
                                                    </label><br>

                                                    <label>
                                                        <input type="checkbox" class="visual-option" name="vi_lipemik"
                                                            value="1"
                                                            <?php echo ($qc_vi_lipemik == '1' ? 'checked' : ''); ?>>
                                                        Lipemik
                                                    </label><br>

                                                    <label>
                                                        <input type="checkbox" class="visual-option"
                                                            name="vi_penggumpalan" value="1"
                                                            <?php echo ($qc_vi_penggumpalan == '1' ? 'checked' : ''); ?>>
                                                        Penggumpalan
                                                    </label><br>

                                                    <label>
                                                        <input type="checkbox" class="visual-option" name="vi_warna"
                                                            value="1"
                                                            <?php echo ($qc_vi_warna == '1' ? 'checked' : ''); ?>>
                                                        Perubahan Warna
                                                    </label><br>

                                                    <label>
                                                        <input type="checkbox" id="vi_tdk" name="vi_tdk" value="1"
                                                            <?php echo ($qc_vi_tdk == '1' ? 'checked' : ''); ?>>
                                                        Tidak Ada
                                                    </label>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <?php if ($tampil_hsl_fisik) { ?>
                                            <tr>
                                                <td class="label-kecil">Hasil Pemeriksaan Fisik</td>
                                                <td>
                                                    <select name="hasil_fisik" class="form-select" required>
                                                        <option value="">-</option>
                                                        <option value="0">Lulus</option>
                                                        <option value="1">Tidak Lulus</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>

                                <div class="card mb-4 col-md-4">
                                    <?php if ($tampil_menu_hemolisis) { ?>
                                    <div class="card-header bg-primary text-white">
                                        PEMERIKSAAN HEMOLISIS
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <?php if ($tampil_hct) { ?>
                                            <tr>
                                                <td class="label-kecil">Hematokrit kedua</td>
                                                <td>
                                                    <input type="text" name="hct" id="hct" class="form-control"
                                                        value="<?php echo h($qc_hct); ?>" onchange="hitung()" required>
                                                    <input type="hidden" name="hct1" id="hct1">
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_plasma) { ?>
                                            <tr>
                                                <td class="label-kecil">Total Plasma Low HB</td>
                                                <td>
                                                    <input type="text" name="plasma" id="plasma" class="form-control"
                                                        value="<?php echo h($qc_plasma); ?>" onchange="hitung()"
                                                        required>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_ttlhb) { ?>
                                            <tr>
                                                <td class="label-kecil">Total HB</td>
                                                <td>
                                                    <input type="text" name="ttlhb" id="ttlhb" class="form-control"
                                                        value="<?php echo h($qc_ttlhb); ?>" onchange="hitung()"
                                                        required>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_hemolisis) { ?>
                                            <tr>
                                                <td class="label-kecil">Hasil Perhitungan Hemolisis</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="hemolisis" id="hemolisis"
                                                            class="form-control" readonly="readonly"
                                                            value="<?php echo h($qc_hemolisis); ?>"
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                    <?php } ?>

                                    <?php if ($tampil_menu_hematologi) { ?>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <div class="card-header bg-primary text-white">
                                                PEMERIKSAAN HEMATOLOGI
                                            </div>
                                            <?php if ($tampil_hematokrit) { ?>
                                            <tr>
                                                <td class="label-kecil">Hematokrit Awal</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="hematokrit" id="hematokrit"
                                                            class="form-control"
                                                            value="<?php echo h($qc_hematokrit); ?>" required>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <?php if ($tampil_volhem) { ?>
                                            <tr>
                                                <td class="label-kecil">Volume Hemoglobin</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="volhem" id="volhem"
                                                            class="form-control" onchange="hitung()" readonly="readonly"
                                                            value="<?php echo h($qc_volhem); ?>"
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">ml</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_kadhb) { ?>
                                            <tr>
                                                <td class="label-kecil">Kadar HB</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="kadhb" id="kadhb" class="form-control"
                                                            value="<?php echo h($qc_kadhb); ?>" onchange="hitung()"
                                                            required>
                                                        <span class="input-group-text">g/dL</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_hemoglobin) { ?>
                                            <tr>
                                                <td class="label-kecil">Hemoglobin</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="hemoglobin" id="hemoglobin"
                                                            class="form-control" readonly="readonly"
                                                            value="<?php echo h($qc_hemoglobin); ?>"
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">g/unit</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_leukosit) { ?>
                                            <tr>
                                                <td class="label-kecil">Kadar WBC</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="kadar_wbc" id="kadar_wbc"
                                                            class="form-control" value="<?php echo h($qc_kadar_wbc); ?>"
                                                            onchange="hitung()" required>
                                                        <span class="input-group-text">sel/mcL</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-kecil">Leukosit</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="leukosit" id="leukosit"
                                                            class="form-control" readonly="readonly"
                                                            value="<?php echo h($qc_leukosit); ?>"
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">/uL</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_trombosit) { ?>
                                            <tr>
                                                <td class="label-kecil">Kadar Trombosit</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="kadar_trombosit" id="kadar_trombosit"
                                                            class="form-control"
                                                            value="<?php echo h($qc_kadar_trombosit); ?>"
                                                            onchange="hitung()" required>
                                                        <span class="input-group-text">sel/mcL</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-kecil">Trombosit</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="trombosit" id="trombosit"
                                                            class="form-control" readonly="readonly"
                                                            value="<?php echo h($qc_trombosit); ?>"
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">/uL</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_hsl_hematologi) { ?>
                                            <tr>
                                                <td class="label-kecil">Hasil Pemeriksaan Hematologi</td>
                                                <td>
                                                    <select name="hasil_hematologi" class="form-select" required>
                                                        <option value="">-</option>
                                                        <option value="0">Lulus</option>
                                                        <option value="1">Tidak Lulus</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                    <?php } ?>

                                    <?php if ($tampil_menu_koagulasi) { ?>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <div class="card-header bg-primary text-white">
                                                PEMERIKSAAN KOAGULASI
                                            </div>
                                            <tr>
                                                <td class="label-kecil">Faktor VIII</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="faktorviii" id="faktorviii"
                                                            class="form-control"
                                                            value="<?php echo h($qc_faktorviii); ?>" required>
                                                        <span class="input-group-text">IU/mL</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php } ?>
                                </div>

                                <div class="card mb-4 col-md-4">
                                    <div class="card-header bg-primary text-white">
                                        <?php if ($produk_key == 'FFP' || $produk_key == 'AHF' || $produk_key == 'PLASMA AFERESIS') { ?>
                                        PETUGAS YANG MENGERJAKAN PEMERIKSAAN
                                        <?php } else { ?>
                                        PEMERIKSAAN KONTAMINASI BAKTERI
                                        <?php } ?>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <?php if ($tampil_bakteri) { ?>
                                            <tr>
                                                <td class="label-kecil">Aerob</td>
                                                <td>
                                                    <select name="aerob" class="form-select" required>
                                                        <option value="">-</option>
                                                        <option value="positif"
                                                            <?php echo ($qc_aerob == 'positif' ? 'selected' : ''); ?>>
                                                            Positif</option>
                                                        <option value="negatif"
                                                            <?php echo ($qc_aerob == 'negatif' ? 'selected' : ''); ?>>
                                                            Negatif</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-kecil">Anaerob</td>
                                                <td>
                                                    <select name="anaerob" class="form-select" required>
                                                        <option value="">-</option>
                                                        <option value="positif"
                                                            <?php echo ($qc_anaerob == 'positif' ? 'selected' : ''); ?>>
                                                            Positif</option>
                                                        <option value="negatif"
                                                            <?php echo ($qc_anaerob == 'negatif' ? 'selected' : ''); ?>>
                                                            Negatif</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_hsl_qc) { ?>
                                            <tr>
                                                <td class="label-kecil">Hasil QC</td>
                                                <td>
                                                    <select name="hasilqc" class="form-select" required>
                                                        <option value="">-</option>
                                                        <option value="0">Lulus</option>
                                                        <option value="1">Tidak Lulus</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                <td class="label-kecil">Petugas Yg Mengerjakan</td>
                                                <td>
                                                    <?php echo h($namalengkap); ?>
                                                    <input type="hidden" name="petugas"
                                                        value="<?php echo h($namalengkap); ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-kecil">Dicek Oleh</td>
                                                <td>
                                                    <select name="dicek_oleh" class="form-select" required>
                                                        <option value="">-</option>
                                                        <?php
                                                            foreach ($dataQc as $nama) {
                                                                $selected = ($qc_dicek_oleh != '' && $nama == $qc_dicek_oleh) ? ' selected' : '';
                                                                echo '<option value="' . h($nama) . '"' . $selected . '>' . h($nama) . '</option>';
                                                            }
                                                            ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-kecil">Disahkan Oleh</td>
                                                <td>
                                                    <select name="disahkan_oleh" class="form-select" required>
                                                        <option value="">-</option>
                                                        <?php
                                                            foreach ($dataQc as $nama) {
                                                                $selected = ($qc_disahkan_oleh != '' && $nama == $qc_disahkan_oleh) ? ' selected' : '';
                                                                echo '<option value="' . h($nama) . '"' . $selected . '>' . h($nama) . '</option>';
                                                            }
                                                            ?>
                                                    </select>
                                                </td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-danger" type="submit" name="simpan"
                                    onclick="if (!validateForm()) return false; return confirm('Apakah Anda yakin ingin menyimpan pemeriksaan ini?');">
                                    Simpan Pemeriksaan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php } ?>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        const tidakAda = document.getElementById('vi_tdk');
        const opsiVisual = document.querySelectorAll('.visual-option');

        hitung();

        function updateVisualCheckbox() {
            if (!tidakAda) {
                return;
            }

            if (tidakAda.checked) {
                opsiVisual.forEach(function(cb) {
                    cb.checked = false;
                    cb.disabled = true;
                });
            } else {
                opsiVisual.forEach(function(cb) {
                    cb.disabled = false;
                });

                const adaYangDipilih = [...opsiVisual].some(cb => cb.checked);
                tidakAda.disabled = adaYangDipilih;
            }
        }

        if (tidakAda) {
            tidakAda.addEventListener('change', updateVisualCheckbox);
        }

        opsiVisual.forEach(function(cb) {
            cb.addEventListener('change', updateVisualCheckbox);
        });

        updateVisualCheckbox();
    });
    </script>
</body>

</html>