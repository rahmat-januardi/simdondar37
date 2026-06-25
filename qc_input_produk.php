<?php
ob_start();
require_once('clogin.php');
require_once('config/db_connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$namauser    = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];

$nkt = '';
$pesan = '';
$alert = '';

$data_reg = false;
$data_stok = false;
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
    $pesan = 'Data QC berhasil disimpan.';
    $alert = 'success';

    var_dump($_POST); // For debugging purposes, you can remove this line in production

    $notrans    = generateNoTrans();
    $nokantong = isset($_POST['nokantong']) ? $_POST['nokantong'] : '';
    $produk = isset($_POST['produk']) ? $_POST['produk'] : '';
    $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
    $merk = isset($_POST['merk']) ? $_POST['merk'] : '';
    $gol_darah = isset($_POST['gol_darah']) ? $_POST['gol_darah'] : '';
    $rhesus = isset($_POST['rhesus']) ? $_POST['rhesus'] : '';
    $tglaftap = isset($_POST['tglaftap']) ? $_POST['tglaftap'] : '';
    $kadaluwarsa = isset($_POST['kadaluwarsa']) ? $_POST['kadaluwarsa'] : '';
    $asal_utd = isset($_POST['asal_utd']) ? $_POST['asal_utd'] : '';
    $tglqc = isset($_POST['tglqc']) ? $_POST['tglqc'] : '';
    $beratjenis = isset($_POST['beratjenis']) ? $_POST['beratjenis'] : '';
    $beratkosong = isset($_POST['beratkosong']) ? $_POST['beratkosong'] : '';
    $antikogulan = isset($_POST['antikogulan']) ? $_POST['antikogulan'] : '';
    $beratisi = isset($_POST['beratisi']) ? $_POST['beratisi'] : '';
    $volume_hasil = isset($_POST['volume_hasil']) ? $_POST['volume_hasil'] : '';
    $vi_hemolisis = isset($_POST['vi_hemolisis']) ? $_POST['vi_hemolisis'] : '';
    $vi_lipemik = isset($_POST['vi_lipemik']) ? $_POST['vi_lipemik'] : '';
    $vi_penggumpalan = isset($_POST['vi_penggumpalan']) ? $_POST['vi_penggumpalan'] : '';
    $vi_warna = isset($_POST['vi_warna']) ? $_POST['vi_warna'] : '';
    $vi_tdk = isset($_POST['vi_tdk']) ? $_POST['vi_tdk'] : '';
    $hct = isset($_POST['hct']) ? $_POST['hct'] : '';
    $plasma = isset($_POST['plasma']) ? $_POST['plasma'] : '';
    $ttlhb = isset($_POST['ttlhb']) ? $_POST['ttlhb'] : '';
    $hemolisis = isset($_POST['hemolisis']) ? $_POST['hemolisis'] : '';
    $volhem = isset($_POST['volhem']) ? $_POST['volhem'] : '';
    $kadhb = isset($_POST['kadhb']) ? $_POST['kadhb'] : '';
    $hemoglobin = isset($_POST['hemoglobin']) ? $_POST['hemoglobin'] : '';
    $aerob = isset($_POST['aerob']) ? $_POST['aerob'] : '';
    $anaerob = isset($_POST['anaerob']) ? $_POST['anaerob'] : '';
    $petugas = isset($_POST['petugas']) ? $_POST['petugas'] : '';
    $dicek_oleh = isset($_POST['dicek_oleh']) ? $_POST['dicek_oleh'] : '';
    $disahkan_oleh = isset($_POST['disahkan_oleh']) ? $_POST['disahkan_oleh'] : '';

    // gabungkan hasil visual
    // $visual = array('$vi_hemolisis', '$vi_lipemik', '$vi_penggumpalan', '$vi_warna', '$vi_tdk');
    // $hsl_visual = implode(', ', array_filter($visual, function ($value) {
    //     return $value !== '';
    // }));

    // Cek Table QC apakah sudah ada data dengan nokantong yang sama
    $qcek = mysql_query("SELECT * FROM qc WHERE nokantong='" . mysql_real_escape_string($nokantong) . "' LIMIT 1");
    $data_qc = mysql_fetch_assoc($qcek);

    if (empty($data_qc)) {
        // Insert new QC data
        $qinsert = mysql_query("
            INSERT INTO qc (
                notrans, qctgl, nokantong, produk, jenis, merk, gol_darah, rhesus, tglaftap, tglpengolahan, kadaluwarsa,
                beratisi, beratkosong, beratjenis, antikogulan, volume, volhem, kadhb, hct, plasma, ttlhb, swirling, ph, hemolisis,
                hemolisis_manual, hematokrit, hemoglobin, leuoksit,
            ) VALUES (
                '" . mysql_real_escape_string($nokantong) . "',
                '" . mysql_real_escape_string($produk) . "',
                '" . mysql_real_escape_string($jenis) . "',
                '" . mysql_real_escape_string($merk) . "',
                '" . mysql_real_escape_string($gol_darah) . "',
                '" . mysql_real_escape_string($rhesus) . "',
                '" . mysql_real_escape_string($tglaftap) . "',
                '" . mysql_real_escape_string($kadaluwarsa) . "',
                '" . mysql_real_escape_string($asal_utd) . "',
                NOW(),
                '" . mysql_real_escape_string($beratjenis) . "',
                '" . mysql_real_escape_string($beratkosong) . "',
                '" . mysql_real_escape_string($antikogulan) . "',
                '" . mysql_real_escape_string($beratisi) . "',
                '" . mysql_real_escape_string($volume_hasil) . "',
                '" . mysql_real_escape_string($hsl_visual) . "',
                '" . mysql_real_escape_string($hct) . "',
                '" . mysql_real_escape_string($plasma) . "',
                '" . mysql_real_escape_string($ttlhb) . "',
                '" . mysql_real_escape_string($hemolisis) . "',
                '" . mysql_real_escape_string($volhem) . "',
                '" . mysql_real_escape_string($kadhb) . "',
                '" . mysql_real_escape_string($hemoglobin) . "',
                '" . mysql_real_escape_string($aerob) . "',
                '" . mysql_real_escape_string($anaerob) . "',
                '" . mysql_real_escape_string($petugas) . "',
                '" . mysql_real_escape_string($dicek_oleh) . "',
                '" . mysql_real_escape_string($disahkan_oleh) . "'
            )
        ");
    } else {
        // Update existing QC data
        $qupdate = mysql_query("
            UPDATE qc SET
                produk='" . mysql_real_escape_string($produk) . "',
                jenis='" . mysql_real_escape_string($jenis) . "',
                merk='" . mysql_real_escape_string($merk) . "',
                gol_darah='" . mysql_real_escape_string($gol_darah) . "',
                rhesus='" . mysql_real_escape_string($rhesus) . "',
                tglaftap='" . mysql_real_escape_string($tglaftap) . "',
                kadaluwarsa='" . mysql_real_escape_string($kadaluwarsa) . "',
                asal_utd='" . mysql_real_escape_string($asal_utd) . "',
                tglqc=NOW(),
                beratjenis='" . mysql_real_escape_string($beratjenis) . "',
                beratkosong='" . mysql_real_escape_string($beratkosong) . "',
                antikogulan='" . mysql_real_escape_string($antikogulan) . "',
                beratisi='" . mysql_real_escape_string($beratisi) . "',
                volume_hasil='" . mysql_real_escape_string($volume_hasil) . "',
                visual='" . mysql_real_escape_string($hsl_visual) . "',
                hct='" . mysql_real_escape_string($hct) . "',
                plasma='" . mysql_real_escape_string($plasma) . "',
                ttlhb='" . mysql_real_escape_string($ttlhb) . "',
                hemolisis='" . mysql_real_escape_string($hemolisis) . "',
                volhem='" . mysql_real_escape_string($volhem) . "',
                kadhb='" . mysql_real_escape_string($kadhb) . "',
                hemoglobin='" . mysql_real_escape_string($hemoglobin) . "',
                aerob='" . mysql_real_escape_string($aerob) . "',
                anaerob='" . mysql_real_escape_string($anaerob) . "',
                petugas='" . mysql_real_escape_string($petugas) . "',
                dicek_oleh='" . mysql_real_escape_string($dicek_oleh) . "',
                disahkan_oleh='" . mysql_real_escape_string($disahkan_oleh) . "'
            WHERE nokantong='" . mysql_real_escape_string($nokantong) . "' LIMIT 1
        ");
    }
}

if (isset($_POST['cari'])) {
    $nkt = strtoupper(trim($_POST['nokantong']));

    if ($nkt != '') {
        $qreg = mysql_query("
            SELECT *
            FROM registrasi_qc
            WHERE nokantong='" . mysql_real_escape_string($nkt) . "'
              AND up_data='0'
            LIMIT 1
        ");

        if ($qreg) {
            $data_reg = mysql_fetch_assoc($qreg);
        }

        if ($data_reg) {
            // panggil data user level qc untuk ditampilkan pada form input qc
            $dataQc = array();

            $qcek_sah = mysql_query("SELECT nama_lengkap FROM user WHERE level LIKE '%qc%' ORDER BY nama_lengkap ASC");

            while ($row = mysql_fetch_array($qcek_sah)) {
                $dataQc[] = $row['nama_lengkap'];
            }

            if ($data_reg['jns_asal'] == '2') {
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
            } else {
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
    function hitung() {
        var beratisi = parseFloat(document.getElementById('beratisi').value);
        var beratkosong = parseFloat(document.getElementById('beratkosong').value);
        var beratjenis = parseFloat(document.getElementById('beratjenis').value);
        var antikogulan = parseFloat(document.getElementById('antikogulan').value);

        if (isNaN(beratisi)) beratisi = 0;
        if (isNaN(beratkosong)) beratkosong = 0;
        if (isNaN(beratjenis)) beratjenis = 0;
        if (isNaN(antikogulan)) antikogulan = 0;

        var total = beratisi - beratkosong;
        var total1 = 0;
        var total2 = 0;

        if (beratjenis != 0) {
            total1 = total / beratjenis;
            total2 = total1 - antikogulan;
        }

        var volume = document.getElementById('volume_hasil');
        if (volume) {
            volume.value = total2.toFixed(2);
        }

        var hct = parseFloat(document.getElementById('hct').value);
        var plasma = parseFloat(document.getElementById('plasma').value);
        var ttlhb = parseFloat(document.getElementById('ttlhb').value);

        if (isNaN(hct)) hct = 0;
        if (isNaN(plasma)) plasma = 0;
        if (isNaN(ttlhb)) ttlhb = 0;

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

        var kadhb = parseFloat(document.getElementById('kadhb').value);
        if (isNaN(kadhb)) kadhb = 0;

        var hemoglobin = tVolhem1 + kadhb;
        var hgb = document.getElementById('hemoglobin');
        if (hgb) {
            hgb.value = hemoglobin.toFixed(2);
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

                <?php if ($data_reg) { ?>

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
                                        <td class="label-kecil">Volume Kantong Kosong</td>
                                        <td><?php echo h($volume != '' ? $volume : '-'); ?> ml</td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Jenis Kantong</td>
                                        <td><?php echo h(jenisKantong($jenis)); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Aftap</td>
                                        <td><?php echo h(tglIndo($tglaftap)); ?></td>
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
                                        <td class="label-kecil">Asal UTD</td>
                                        <td><?php echo h(isset($asal_utd) ? $asal_utd : '-'); ?>
                                        </td>
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
                                        <td class="label-kecil">Tanggal Aftap</td>
                                        <td><?php echo h(tglIndo(isset($data_reg['tglaftap']) ? $data_reg['tglaftap'] : '')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Kedaluwarsa</td>
                                        <td><?php echo h(tglIndo(isset($data_reg['kadaluwarsa']) ? $data_reg['kadaluwarsa'] : '')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Pengolahan</td>
                                        <td><?php echo h(tglIndo(isset($data_reg['tgl_pengolahan']) ? $data_reg['tgl_pengolahan'] : '')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Petugas Serah</td>
                                        <td><?php echo h(isset($data_reg['petugas_serah']) ? $data_reg['petugas_serah'] : '-'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Tanggal Terima Sampel</td>
                                        <td><?php echo h(tglIndo(isset($data_reg['tgl']) ? $data_reg['tgl'] : '')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-kecil">Petugas Terima</td>
                                        <td><?php echo h(isset($data_reg['petugas_terima']) ? $data_reg['petugas_terima'] : '-'); ?>
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
                        <form method="post" action="" onkeydown="if(event.keyCode==13){return false;}"
                            onsubmit="return false;">
                            <input type="hidden" name="nokantong" value="<?php echo h($nkt); ?>">
                            <input type="hidden" name="produk" value="<?php echo h($produk_nama); ?>">
                            <input type="hidden" name="jenis" value="<?php echo h($jenis); ?>">
                            <input type="hidden" name="merk" value="<?php echo h($merk); ?>">
                            <input type="hidden" name="gol_darah" value="<?php echo h($goldarah); ?>">
                            <input type="hidden" name="rhesus" value="<?php echo h($rhesus); ?>">
                            <input type="hidden" name="tglaftap" value="<?php echo h($tglaftap); ?>">
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
                                    case 'WHOLE BLOOD':
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = true;
                                        $tampil_volume = true;
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
                                        break;

                                    case 'PRC':
                                    case 'PACKED RED CELL':
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = false;
                                        $tampil_volume = true;
                                        $tampil_hct = true;
                                        $tampil_plasma = false;
                                        $tampil_ttlhb = true;
                                        $tampil_hemolisis = true;
                                        $tampil_volhem = true;
                                        $tampil_kadhb = true;
                                        $tampil_hemoglobin = true;
                                        $tampil_visual = true;
                                        $tampil_hsl_hematologi = false;
                                        $tampil_bakteri = false;
                                        break;

                                    case 'TC':
                                    case 'THROMBOCYTE CONCENTRATE':
                                    case 'FFP':
                                    case 'FRESH FROZEN PLASMA':
                                    case 'AHF':
                                    case 'CRYOPRECIPITATE':
                                    case 'PLASMA AFERESIS':
                                        $tampil_beratjenis = false;
                                        $tampil_beratkosong = false;
                                        $tampil_antikogulan = false;
                                        $tampil_volume = true;
                                        $tampil_hct = false;
                                        $tampil_plasma = false;
                                        $tampil_ttlhb = false;
                                        $tampil_hemolisis = false;
                                        $tampil_volhem = false;
                                        $tampil_kadhb = false;
                                        $tampil_hemoglobin = false;
                                        $tampil_visual = true;
                                        $tampil_hsl_hematologi = false;
                                        $tampil_bakteri = false;
                                        break;

                                    default:
                                        $tampil_beratjenis = true;
                                        $tampil_beratkosong = true;
                                        $tampil_antikogulan = true;
                                        $tampil_volume = true;
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
                                                        value="<?php echo date('Y-m-d'); ?>" required>
                                                </td>
                                            </tr>

                                            <?php if ($tampil_beratjenis) { ?>
                                            <tr>
                                                <td class="label-kecil">Berat Jenis</td>
                                                <td>
                                                    <input type="text" name="beratjenis" id="beratjenis"
                                                        class="form-control" value="<?php echo h($beratjenis); ?>"
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
                                                        class="form-control" value="<?php echo h($beratkosong); ?>"
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
                                                        class="form-control" value="<?php echo h($antikogulan); ?>"
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
                                                            onchange="hitung()" required>
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
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">ml</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_visual) { ?>
                                            <tr>
                                                <td class="label-kecil">Perubahan Visual</td>
                                                <td>
                                                    <label>
                                                        <input type="checkbox" class="visual-option" name="vi_hemolisis"
                                                            value="1">
                                                        Hemolisis
                                                    </label><br>

                                                    <label>
                                                        <input type="checkbox" class="visual-option" name="vi_lipemik"
                                                            value="1">
                                                        Lipemik
                                                    </label><br>

                                                    <label>
                                                        <input type="checkbox" class="visual-option"
                                                            name="vi_penggumpalan" value="1">
                                                        Penggumpalan
                                                    </label><br>

                                                    <label>
                                                        <input type="checkbox" class="visual-option" name="vi_warna"
                                                            value="1">
                                                        Perubahan Warna
                                                    </label><br>

                                                    <label>
                                                        <input type="checkbox" id="vi_tdk" name="vi_tdk" value="1">
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
                                    <div class="card-header bg-primary text-white">
                                        PEMERIKSAAN HEMOLISIS
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <?php if ($tampil_hct) { ?>
                                            <tr>
                                                <td class="label-kecil">HCT</td>
                                                <td>
                                                    <input type="text" name="hct" id="hct" class="form-control"
                                                        onchange="hitung()" required>
                                                    <input type="hidden" name="hct1" id="hct1">
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_plasma) { ?>
                                            <tr>
                                                <td class="label-kecil">Total Plasma Low HB</td>
                                                <td>
                                                    <input type="text" name="plasma" id="plasma" class="form-control"
                                                        onchange="hitung()" required>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                            <?php if ($tampil_ttlhb) { ?>
                                            <tr>
                                                <td class="label-kecil">Total HB</td>
                                                <td>
                                                    <input type="text" name="ttlhb" id="ttlhb" class="form-control"
                                                        onchange="hitung()" required>
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
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <div class="card-header bg-primary text-white">
                                                PEMERIKSAAN HEMATOLOGI
                                            </div>
                                            <?php if ($tampil_volhem) { ?>
                                            <tr>
                                                <td class="label-kecil">Volume Hemoglobin</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="volhem" id="volhem"
                                                            class="form-control" onchange="hitung()" readonly="readonly"
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
                                                            onchange="hitung()" required>
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
                                                            style="background-color:#BDB76B;" required>
                                                        <span class="input-group-text">g/unit</span>
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
                                </div>

                                <div class="card mb-4 col-md-4">
                                    <div class="card-header bg-primary text-white">
                                        PEMERIKSAAN KONTAMINASI BAKTERI
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <?php if ($tampil_bakteri) { ?>
                                            <tr>
                                                <td class="label-kecil">Aerob</td>
                                                <td>
                                                    <select name="aerob" class="form-select" required>
                                                        <option value="">-</option>
                                                        <option value="positif">Positif</option>
                                                        <option value="negatif">Negatif</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label-kecil">Anaerob</td>
                                                <td>
                                                    <select name="anaerob" class="form-select" required>
                                                        <option value="">-</option>
                                                        <option value="positif">Positif</option>
                                                        <option value="negatif">Negatif</option>
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
                                                                echo '<option value="' . h($nama) . '">' . h($nama) . '</option>';
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
                                                                echo '<option value="' . h($nama) . '">' . h($nama) . '</option>';
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
                                <button class="btn btn-danger" type="submit" name="simpan">
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

        function updateVisualCheckbox() {

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

        tidakAda.addEventListener('change', updateVisualCheckbox);

        opsiVisual.forEach(function(cb) {
            cb.addEventListener('change', updateVisualCheckbox);
        });

        updateVisualCheckbox();
    });
    </script>
</body>

</html>