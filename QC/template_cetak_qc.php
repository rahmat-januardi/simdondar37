<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/db_connect.php');

if (!isset($_GET['ids']) || $_GET['ids'] == '') {
    die('Data tidak ditemukan.');
}

$pakai_sertifikat = isset($_GET['pakai_sertifikat']) ? trim($_GET['pakai_sertifikat']) : '0';

$ids = explode(',', $_GET['ids']);
$ids_safe = array();

foreach ($ids as $id) {
    $id = trim($id);
    if ($id != '') {
        $ids_safe[] = "'" . mysql_real_escape_string($id) . "'";
    }
}

if (count($ids_safe) == 0) {
    die('Data tidak ditemukan.');
}

function getJenisKantongKode($jenis)
{
    switch ($jenis) {
        case '1':
            return "SG";
        case '2':
            return "DB";
        case '3':
            return "TR";
        case '4':
            return "QD";
        case '5':
            return "QT";
        default:
            return "";
    }
}

$where_ids = implode(',', $ids_safe);
// echo $where_ids; // Debugging line to check the value of $where_ids
/*
    Ambil data utama dari registrasi_qc + qc + utd
    Asumsi:
    - registrasi_qc.id = id yang dipilih
    - registrasi_qc.nokantong = nomor kantong
    - registrasi_qc.produk = PRC
    - registrasi_qc.asal_utd = id UTD
    - qc.nosurat, qc.tglsurat sudah tersimpan per nokantong
*/
$sql = mysql_query("
    SELECT
        rq.tgl as tgl_registrasi,
        rq.volume as volume_asal,
        rq.petugas_terima,
        rq.petugas_serah,
        rq.asal_utd,
        rq.suhu,
        rq.catatan,
        q.*,
        u.alamat,
        u.telp,
        u.fax,
        u.nama AS nama_utd
    FROM registrasi_qc rq
    LEFT JOIN qc q ON q.nokantong = rq.nokantong
    LEFT JOIN utd u ON u.id = rq.asal_utd
    WHERE rq.id IN ($where_ids)
    ORDER BY rq.id ASC
") or die(mysql_error());

$data = array();
while ($row = mysql_fetch_assoc($sql)) {
    $data[] = $row;
}

if (count($data) == 0) {
    die('Data tidak ditemukan.');
}

/* Data header dari baris pertama */
$head = $data[0];
var_dump($head); // Debugging line to check the contents of $head

$standar_produk = getStandarProduk(isset($head['produk']) ? $head['produk'] : '', isset($head['volume_asal']) ? $head['volume_asal'] : 0);
$produk_supported = $standar_produk['supported'];

function h($nilai)
{
    return htmlspecialchars($nilai, ENT_QUOTES);
}

function toFloatVal($nilai)
{
    $nilai = trim((string)$nilai);
    if ($nilai === '') {
        return 0;
    }
    return (float) str_replace(',', '.', $nilai);
}

function persenAngka($lolos, $total)
{
    if ($total <= 0) {
        return 0;
    }
    return (int) round(($lolos / $total) * 100);
}

function persenLulus($lolos, $total)
{
    return persenAngka($lolos, $total) . '%';
}

function ambangVolumeHb($volume_asal, $produk)
{
    $volume_asal = (int) $volume_asal;

    if ($volume_asal == 350) {
        if ($produk == 'PRC' || $produk == 'PRC 450') {
            return array(179, 257, 35); /// min volume, max volume, minimal Hb
        } elseif ($produk == 'WB' || $produk == 'WB 450') {
            return array(315, 385, 35);
        } elseif ($produk == 'TC') {
            return array(31, 150, 0);
        }
    } elseif ($volume_asal == 450) {
        if ($produk == 'PRC' || $produk == 'PRC 450') {
            return array(230, 330, 45);
        } elseif ($produk == 'WB' || $produk == 'WB 450') {
            return array(405, 495, 45);
        } elseif ($produk == 'TC') {
            return array(40, 160, 0);
        }
    }
    return array(0, 0, 0);
}

function ambangVolumeAferesis($volume_asal, $produk)
{
    $volume_asal = (int) $volume_asal;

    if ($produk == 'TC AFERESIS') {
        if ($volume_asal == 350) {
            return array(100, 400, 0); // min, max, min Hb
        } elseif ($volume_asal == 450) {
            return array(100, 400, 0);
        }
    }
    return array(0, 0, 0);
}

function ambangTrombositLeukosit($volume_asal, $produk)
{
    $volume_asal = (int) $volume_asal;

    if ($volume_asal == 350) {
        if ($produk == 'TC') {
            return array(47, 0.16); // min trombosit, min leukosit (satuan x 10^9)
        } elseif ($produk == 'TC AFERESIS') {
            return array(200, 0.005); // min trombosit (>= 2 x 10^11 = 200 x 10^9), min leukosit (< 5 x 10^6 = 0.005 x 10^9)
        }
    } elseif ($volume_asal == 450) {
        if ($produk == 'TC') {
            return array(60, 0.2);
        } elseif ($produk == 'TC AFERESIS') {
            return array(200, 0.005);
        }
    }
    return array(0, 0);
}

function isNegatif($nilai)
{
    $nilai = strtolower(trim((string)$nilai));
    return (
        $nilai == 'negatif' ||
        $nilai == 'negative' ||
        $nilai == '-' ||
        $nilai == '0' ||
        $nilai == 'non reaktif' ||
        $nilai == 'nonreaktif'
    );
}

function getStandarProduk($produk, $volume_asal)
{
    $produk_norm = strtoupper(trim((string)$produk));
    $standar = array(
        'produk' => $produk_norm,
        'supported' => false,
        'note' => '',
        'hasil' => array(),
        'threshold' => array(),
        'volume_min' => 0,
        'volume_max' => 0,
        'hemolisis_max' => 0,
        'hematokrit_min' => 0,
        'hematokrit_max' => 0,
        'hemoglobin_min' => 0,
        'ph_min' => 0,
        'swirling' => '', // 0 = ada, 1 = tidak ada
        'trombosit_min' => 0,
        'leukosit_max' => 0,
    );

    /*
        Saat ini yang dihitung penuh hanya PRC.
        Untuk WB, PRC Leucoreduction, PRC Leucodepleted, TC, TC Aferesis,
        FFP, AHF, dan Plasma Aferesis, angka ambang batasnya perlu kamu isi
        sesuai standar yang kamu pakai.
    */
    if ($produk_norm == 'PRC' || $produk_norm == 'PRC 450') {
        $standar['supported'] = true;

        list($min_volume, $max_volume, $min_hb) = ambangVolumeHb($volume_asal, $produk_norm);
        $standar['volume_min'] = $min_volume;
        $standar['volume_max'] = $max_volume;
        $standar['hemolisis_max'] = 0.8;
        $standar['hematokrit_min'] = 65;
        $standar['hematokrit_max'] = 75;
        $standar['hemoglobin_min'] = $min_hb;

        $standar['hasil'] = array(
            'volume'     => 75,
            'hemolisis'  => 75,
            'hematokrit' => 75,
            'hemoglobin' => 75,
            'aerob'      => 100,
            'anaerob'    => 100,
        );
        $standar['threshold'] = $standar['hasil'];
    } elseif (
        $produk_norm == 'WB' ||
        $produk_norm == 'WB 450'
    ) {
        $standar['supported'] = true;

        list($min_volume, $max_volume, $min_hb) = ambangVolumeHb($volume_asal, $produk_norm);

        $standar['volume_min'] = $min_volume;
        $standar['volume_max'] = $max_volume;
        $standar['hemolisis_max'] = 0.8;
        $standar['hemoglobin_min'] = $min_hb;

        $standar['hasil'] = array(
            'volume'     => 75,
            'hemolisis'  => 75,
            'hemoglobin' => 75,
            'aerob'      => 100,
            'anaerob'    => 100,
        );
        $standar['threshold'] = $standar['hasil'];
    } elseif ($produk_norm == 'TC') {
        $standar['supported'] = true;

        list($min_volume, $max_volume, $min_hb) = ambangVolumeHb($volume_asal, $produk_norm);
        list($min_trombosit, $min_leukosit) = ambangTrombositLeukosit($volume_asal, $produk_norm);

        $standar['volume_min'] = $min_volume;
        $standar['volume_max'] = $max_volume;
        $standar['ph_min'] = 6.4;
        $standar['swirling'] = 0;
        $standar['trombosit_min'] = $min_trombosit;
        $standar['leukosit_max'] = $min_leukosit;

        $standar['hasil'] = array(
            'volume'     => 75,
            'ph'         => 75,
            'swirling'   => 100,
            'trombosit'  => 75,
            'leukosit'   => 90,
            'aerob'      => 100,
            'anaerob'    => 100,
        );
        $standar['threshold'] = $standar['hasil'];
    } elseif ($produk_norm == 'TC AFERESIS') {
        $standar['supported'] = true;

        list($min_volume, $max_volume, $min_hb) = ambangVolumeAferesis($volume_asal, $produk_norm);
        list($min_trombosit, $min_leukosit) = ambangTrombositLeukosit($volume_asal, $produk_norm);

        $standar['volume_min'] = $min_volume;
        $standar['volume_max'] = $max_volume;
        $standar['ph_min'] = 6.4;
        $standar['swirling'] = 0;
        $standar['trombosit_min'] = $min_trombosit;
        $standar['leukosit_max'] = $min_leukosit;

        $standar['hasil'] = array(
            'volume'     => 75,
            'ph'         => 75,
            'swirling'   => 100,
            'trombosit'  => 75,
            'leukosit'   => 90,
            'aerob'      => 100,
            'anaerob'    => 100,
        );
        $standar['threshold'] = $standar['hasil'];
    } elseif (
        $produk_norm == 'FFP' ||
        $produk_norm == 'AHF' ||
        $produk_norm == 'PRC LEUCOREDUCTION' ||
        $produk_norm == 'PRC LEUCODEPLETED' ||
        $produk_norm == 'PLASMA AFERESIS'
    ) {
        $standar['note'] = 'Standar produk ini belum diisi pada file ini. Silakan sesuaikan ambang batasnya.';
    } else {
        $standar['note'] = 'Produk belum dikenali di file ini.';
    }

    return $standar;
}

function lulusRange($nilai, $min, $max)
{
    if ($min == 0 && $max == 0) {
        return false;
    }
    return ($nilai >= $min && $nilai <= $max);
}

function lulusMin($nilai, $min)
{
    if ($min == 0) {
        return false;
    }
    return ($nilai > $min);
}

function lulusMax($nilai, $max)
{
    if ($max == 0) {
        return false;
    }
    return ($nilai < $max);
}

function displayCell($nilai, $lulus, $format = 'text')
{
    $class = $lulus ? '' : 'fail-cell';

    if ($format == 'number1') {
        $nilai = number_format((float) $nilai, 1, '.', '');
    } elseif ($format == 'number2') {
        $nilai = number_format((float) $nilai, 2, '.', '');
    } elseif ($format == 'round') {
        $nilai = round((float) $nilai);
    } elseif ($format == 'upper') {
        $nilai = strtoupper(trim((string)$nilai));
    } else {
        $nilai = (string) $nilai;
    }

    return '<span class="' . $class . '">' . h($nilai) . '</span>';
}

function hasilLulusText($persen, $ambang)
{
    return ($persen >= $ambang) ? 'Lulus' : 'Tidak Lulus';
}

/* Contoh nilai yang bisa kamu sesuaikan */
$nama_udd_pengirim = isset($head['nama_utd']) ? $head['nama_utd'] : '-';
$alamat_udd_pengirim = isset($head['alamat']) ? $head['alamat'] : '-';
$no_telp_fax = '';
if (isset($head['telp']) && $head['telp'] != '') {
    $no_telp_fax .= 'Telepon: ' . $head['telp'];
}
if (isset($head['fax']) && $head['fax'] != '') {
    $no_telp_fax .= ', Fax: ' . $head['fax'];
}
$bln_indo = array(
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
);

$bln_terima = isset($head['tgl_registrasi']) ? $bln_indo[date('m', strtotime($head['tgl_registrasi']))] : '';
$tanggal_terima = isset($head['tgl_registrasi']) ? date('d', strtotime($head['tgl_registrasi'])) . ' ' . $bln_terima . ' ' . date('Y', strtotime($head['tgl_registrasi'])) : '-';
$periode = isset($head['tgl_registrasi']) ? $bln_terima . ' ' . date('Y', strtotime($head['tgl_registrasi'])) : '-';
$kondisi_sampel = 'Baik';
$suhu_sampel = isset($head['suhu']) ? $head['suhu'] . '&deg;C' : '-';
$penanggung_jawab = isset($head['petugas_serah']) ? $head['petugas_serah'] : '-';

/* Sertifikat: kalau ada di tabel qc, pakai itu */
$no_sertifikat = isset($head['nosurat']) ? $head['nosurat'] : '';
$tgl_sertifikat = isset($head['tglsurat']) ? $head['tglsurat'] : '';

/* Nomor surat bisa kamu ubah sesuai format yang dipakai */
$nomor_surat = $pakai_sertifikat != '0' ? 'Nomor: ' . $no_sertifikat : '';

$total_data = count($data);

$pass_volume     = 0;
$pass_hemolisis  = 0;
$pass_hematokrit = 0;
$pass_hemoglobin = 0;
$pass_ph         = 0;
$pass_swirling   = 0;
$pass_trombosit  = 0;
$pass_leukosit   = 0;
$pass_aerob      = 0;
$pass_anaerob    = 0;

$hasil_volume_num     = 0;
$hasil_hemolisis_num  = 0;
$hasil_hematokrit_num = 0;
$hasil_hemoglobin_num = 0;
$hasil_ph_num         = 0;
$hasil_swirling_num   = 0;
$hasil_trombosit_num  = 0;
$hasil_leukosit_num   = 0;
$hasil_aerob_num      = 0;
$hasil_anaerob_num    = 0;

$ambang_volume_num     = 0;
$ambang_hemolisis_num  = 0;
$ambang_hematokrit_num = 0;
$ambang_hemoglobin_num = 0;
$ambang_ph_num         = 0;
$ambang_swirling_num   = 0;
$ambang_trombosit_num  = 0;
$ambang_leukosit_num   = 0;
$ambang_aerob_num      = 0;
$ambang_anaerob_num    = 0;

$min_volume = 0;
$max_volume = 0;
$min_hb = 0;

if ($produk_supported) {
    foreach ($data as $row) {
        $row_produk = isset($row['produk']) ? $row['produk'] : (isset($head['produk']) ? $head['produk'] : '');
        $row_volume_asal = isset($row['volume_asal']) ? $row['volume_asal'] : 0;
        $row_standar = getStandarProduk($row_produk, $row_volume_asal);

        if (!$row_standar['supported']) {
            continue;
        }

        $min_volume = $row_standar['volume_min'];
        $max_volume = $row_standar['volume_max'];
        $min_hb = $row_standar['hemoglobin_min'];
        $min_ph = $row_standar['ph_min'];

        $volume     = toFloatVal(isset($row['volume']) ? $row['volume'] : 0);
        $hemolisis  = toFloatVal(isset($row['hemolisis']) ? $row['hemolisis'] : 0);
        $hematokrit = toFloatVal(isset($row['hematokrit']) ? $row['hematokrit'] : 0);
        $hemoglobin = toFloatVal(isset($row['hemoglobin']) ? $row['hemoglobin'] : 0);
        $ph         = toFloatVal(isset($row['ph']) ? $row['ph'] : 0);
        $swirling   = isset($row['swirling']) ? trim($row['swirling']) : '';
        $trombosit  = toFloatVal(isset($row['trombosit']) ? $row['trombosit'] : 0);
        $leukosit = toFloatVal(isset($row['leukosit']) ? $row['leukosit'] : 0);

        if (lulusRange($volume, $min_volume, $max_volume)) {
            $pass_volume++;
        }
        if (lulusMax($hemolisis, $row_standar['hemolisis_max'])) {
            $pass_hemolisis++;
        }
        if (lulusRange($hematokrit, $row_standar['hematokrit_min'], $row_standar['hematokrit_max'])) {
            $pass_hematokrit++;
        }
        if (lulusMin($hemoglobin, $min_hb)) {
            $pass_hemoglobin++;
        }
        if (lulusMin($ph, $min_ph)) {
            $pass_ph++;
        }
        if (isNegatif($swirling)) {
            $pass_swirling++;
        }
        if (lulusMin($trombosit, $row_standar['trombosit_min'])) {
            $pass_trombosit++;
        }
        if (lulusMax($leukosit, $row_standar['leukosit_max'])) {
            $pass_leukosit++;
        }
        if (isNegatif(isset($row['aerob']) ? $row['aerob'] : '')) {
            $pass_aerob++;
        }
        if (isNegatif(isset($row['anaerob']) ? $row['anaerob'] : '')) {
            $pass_anaerob++;
        }
    }

    $hasil_volume_num     = persenAngka($pass_volume, $total_data);
    $hasil_hemolisis_num  = persenAngka($pass_hemolisis, $total_data);
    $hasil_hematokrit_num = persenAngka($pass_hematokrit, $total_data);
    $hasil_hemoglobin_num = persenAngka($pass_hemoglobin, $total_data);
    $hasil_ph_num         = persenAngka($pass_ph, $total_data);
    $hasil_swirling_num   = persenAngka($pass_swirling, $total_data);
    $hasil_trombosit_num  = persenAngka($pass_trombosit, $total_data);
    $hasil_leukosit_num   = persenAngka($pass_leukosit, $total_data);
    $hasil_aerob_num      = persenAngka($pass_aerob, $total_data);
    $hasil_anaerob_num    = persenAngka($pass_anaerob, $total_data);

    $ambang_volume_num     = isset($standar_produk['threshold']['volume']) ? (int)$standar_produk['threshold']['volume'] : 0;
    $ambang_hemolisis_num  = isset($standar_produk['threshold']['hemolisis']) ? (int)$standar_produk['threshold']['hemolisis'] : 0;
    $ambang_hematokrit_num = isset($standar_produk['threshold']['hematokrit']) ? (int)$standar_produk['threshold']['hematokrit'] : 0;
    $ambang_hemoglobin_num = isset($standar_produk['threshold']['hemoglobin']) ? (int)$standar_produk['threshold']['hemoglobin'] : 0;
    $ambang_ph_num         = isset($standar_produk['threshold']['ph']) ? (int)$standar_produk['threshold']['ph'] : 0;
    $ambang_swirling_num   = isset($standar_produk['threshold']['swirling']) ? (int)$standar_produk['threshold']['swirling'] : 0;
    $ambang_trombosit_num  = isset($standar_produk['threshold']['trombosit']) ? (int)$standar_produk['threshold']['trombosit'] : 0;
    $ambang_leukosit_num   = isset($standar_produk['threshold']['leukosit']) ? (int)$standar_produk['threshold']['leukosit'] : 0;
    $ambang_aerob_num      = isset($standar_produk['threshold']['aerob']) ? (int)$standar_produk['threshold']['aerob'] : 0;
    $ambang_anaerob_num    = isset($standar_produk['threshold']['anaerob']) ? (int)$standar_produk['threshold']['anaerob'] : 0;
}

$ket_volume     = $produk_supported ? hasilLulusText($hasil_volume_num, $ambang_volume_num) : '-';
$ket_hemolisis  = $produk_supported ? hasilLulusText($hasil_hemolisis_num, $ambang_hemolisis_num) : '-';
$ket_hematokrit = $produk_supported ? hasilLulusText($hasil_hematokrit_num, $ambang_hematokrit_num) : '-';
$ket_hemoglobin = $produk_supported ? hasilLulusText($hasil_hemoglobin_num, $ambang_hemoglobin_num) : '-';
$ket_ph         = $produk_supported ? hasilLulusText($hasil_ph_num, $ambang_ph_num) : '-';
$ket_swirling   = $produk_supported ? hasilLulusText($hasil_swirling_num, $ambang_swirling_num) : '-';
$ket_trombosit  = $produk_supported ? hasilLulusText($hasil_trombosit_num, $ambang_trombosit_num) : '-';
$ket_leukosit   = $produk_supported ? hasilLulusText($hasil_leukosit_num, $ambang_leukosit_num) : '-';
$ket_aerob      = $produk_supported ? hasilLulusText($hasil_aerob_num, $ambang_aerob_num) : '-';
$ket_anaerob    = $produk_supported ? hasilLulusText($hasil_anaerob_num, $ambang_anaerob_num) : '-';

$kesimpulan_umum = '-';
if ($produk_supported) {
    $kesimpulan_umum = (
        $hasil_volume_num >= $ambang_volume_num &&
        $hasil_hemolisis_num >= $ambang_hemolisis_num &&
        $hasil_hematokrit_num >= $ambang_hematokrit_num &&
        $hasil_hemoglobin_num >= $ambang_hemoglobin_num &&
        $hasil_ph_num >= $ambang_ph_num &&
        $hasil_swirling_num >= $ambang_swirling_num &&
        $hasil_trombosit_num >= $ambang_trombosit_num &&
        $hasil_leukosit_num >= $ambang_leukosit_num &&
        $hasil_aerob_num >= $ambang_aerob_num &&
        $hasil_anaerob_num >= $ambang_anaerob_num
    ) ? 'Lulus' : 'Tidak Lulus';
}

// Penamaan Judul Surat berdasarkan Produknya
if ($head['produk'] == 'PRC' || $head['produk'] == 'PRC 450') {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN DARAH MERAH PEKAT";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN DARAH MERAH PEKAT (PRC)";
} elseif ($head['produk'] == 'WB' || $head['produk'] == 'WB 450') {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN DARAH LENGKAP";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN DARAH LENGKAP (WB)";
} elseif ($head['produk'] == 'TC') {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN TROMBOSIT PEKAT";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN TROMBOSIT PEKAT (TC)";
} elseif ($head['produk'] == 'FFP') {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN PLASMA SEGERA BEKU";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN PLASMA SEGERA BEKU (FFP)";
} elseif ($head['produk'] == 'AHF') {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN ANTI HEMOFILI FAKTOR (AHF)";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN ANTI HEMOFILI FAKTOR (AHF)";
} elseif (strcasecmp($head['produk'], 'TC Aferesis') == 0) {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN TROMBOSIT AFERESIS";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN TROMBOSIT AFERESIS (TC AFERESIS)";
} elseif ($head['produk'] == 'PRC LEUCODEPLETED') {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN DARAH MERAH PEKAT - LEUCODEPLETED";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN DARAH MERAH PEKAT (PRC) - LEUCODEPLETED";
} elseif ($head['produk'] == 'PRC LEUCOREDUCTION') {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN DARAH MERAH PEKAT - LEUCOREDUCTION";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN DARAH MERAH PEKAT (PRC) - LEUCOREDUCTION";
} elseif ($head['produk'] == 'PLASMA AFERESIS') {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN PLASMA AFERESIS";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN PLASMA AFERESIS";
} else {
    $judul_surat = "SURAT KETERANGAN UJI MUTU KOMPONEN DARAH";
    $lembar_analisa = "LEMBAR DATA ANALISA UJI MUTU KOMPONEN DARAH";
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Template PRC</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
            min-height: 1000px;
            padding: 20px 24px;
            box-sizing: border-box;
        }

        .page-break {
            page-break-after: always;
        }

        .header-top {
            text-align: center;
            margin-top: 60px;
            line-height: 1.4;
        }

        .header-top .title {
            font-weight: bold;
            font-size: 13px;
        }

        .header-top .subtitle {
            font-weight: bold;
            font-size: 13px;
        }

        .line {
            border-top: 1px solid #000;
            margin: 8px 0 15px 0;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-top: 14px;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .sample-table,
        .result-table {
            margin-top: 6px;
        }

        .sample-table th,
        .sample-table td,
        .result-table th,
        .result-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .sample-table th,
        .result-table th {
            text-align: center;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .small {
            font-size: 10px;
        }

        .signature {
            margin-top: 30px;
            font-size: 13px;
        }

        .signature .name {
            margin-top: 55px;
            font-weight: bold;
        }

        .footer-note {
            margin-top: 12px;
            font-size: 13px;
        }

        .lampiran-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 18px;
        }

        .box-note {
            border: 1px solid #000;
            padding: 8px;
            width: 260px;
            float: right;
            margin-top: 10px;
            font-size: 10px;
        }

        .box-note-hasil {
            padding: 8px;
            width: 260px;
            float: left;
            margin-top: 10px;
            font-size: 10px;
        }

        .fail-cell {
            background: #f8d7da;
            color: #b00020;
            font-weight: bold;
            /* padding: 2px 4px;
            display: inline-block;
            border-radius: 2px; */
        }

        .pass-cell {
            padding: 2px 4px;
            display: inline-block;
        }
    </style>
</head>

<body>

    <!-- HALAMAN 1 -->
    <div class="page page-break">
        <div class="header-top">
            <div class="title">LABORATORIUM PENGAWASAN MUTU</div>
            <div class="subtitle"><?= htmlspecialchars($nama_udd_pengirim, ENT_QUOTES) ?></div>
            <div><?= htmlspecialchars($alamat_udd_pengirim, ENT_QUOTES) ?></div>
            <div><?= !empty($no_telp_fax) ? htmlspecialchars($no_telp_fax, ENT_QUOTES) : '' ?></div>
            <div class="line"></div>
            <div class="title"><?= htmlspecialchars($judul_surat, ENT_QUOTES) ?></div>
            <div><b><?php echo htmlspecialchars($nomor_surat, ENT_QUOTES); ?></b></div>
        </div>

        <div class="section-title">1. Informasi UDD Pengirim</div>
        <table class="info-table">
            <tr>
                <td width="250">1.1 Nama UDD</td>
                <td width="5">:</td>
                <td><?php echo htmlspecialchars($nama_udd_pengirim, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>1.2 Alamat</td>
                <td>:</td>
                <td><?= htmlspecialchars($alamat_udd_pengirim, ENT_QUOTES) ?></td>
            </tr>
            <tr>
                <td>1.3 No. Telp/Fax</td>
                <td>:</td>
                <td><?= htmlspecialchars($no_telp_fax, ENT_QUOTES) == '' ? '-' : htmlspecialchars($no_telp_fax, ENT_QUOTES) ?>
                </td>
            </tr>
            <tr>
                <td>1.4 Penanggung Jawab</td>
                <td>:</td>
                <td><?= htmlspecialchars($penanggung_jawab, ENT_QUOTES) ?></td>
            </tr>
        </table>

        <div class="section-title">2. Informasi Sampel</div>
        <table class="info-table">
            <tr>
                <td width="250">2.1 No. Identitas Sampel</td>
                <td width="5">:</td>
                <td></td>
            </tr>
        </table>

        <table class="sample-table">
            <thead>
                <tr>
                    <th width="40" rowspan="2">No.</th>
                    <th rowspan="2">No. Kantong</th>
                    <th rowspan="2">Jenis Kantong</th>
                    <th colspan="2">Tanggal</th>
                </tr>
                <tr>
                    <th>Pembuatan</th>
                    <th>Kedaluwarsa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($data as $row) {
                    $tgl_aftap = !empty($row['tglaftap']) ? date('d/m/Y', strtotime($row['tglaftap'])) : '-';
                    $tgl_exp   = !empty($row['kadaluwarsa']) ? date('d/m/Y', strtotime($row['kadaluwarsa'])) : '-';
                    $jenis_kantong = getJenisKantongKode($row['jenis']);
                ?>
                    <tr>
                        <td class="center"><?php echo $no++; ?></td>
                        <td class="center"><?php echo htmlspecialchars($row['nokantong'], ENT_QUOTES); ?></td>
                        <td class="center">
                            <?php echo htmlspecialchars($row['merk'], ENT_QUOTES) ?>
                            <?= $jenis_kantong; ?>
                            <?= htmlspecialchars($row['volume_asal'], ENT_QUOTES) ?>
                        </td>
                        <td class="center"><?php echo $tgl_aftap; ?></td>
                        <td class="center"><?php echo $tgl_exp; ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

        <div class="section-title">3. Informasi Pemeriksaan</div>
        <table class="info-table">
            <tr>
                <td width="250">3.1 Tanggal Pemeriksaan</td>
                <td width="5">:</td>
                <td><?php echo htmlspecialchars($tanggal_terima, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>3.2 Metode Pemeriksaan</td>
                <td>:</td>
                <td>Manual dan Otomatis</td>
            </tr>
            <tr>
                <td>3.3 Hasil Pemeriksaan</td>
                <td>:</td>
                <td>
                    <!-- <?php if ($produk_supported) { ?>
                        <span class="<?php echo ($kesimpulan_umum == 'Lulus') ? 'pass-cell' : 'fail-cell'; ?>">
                            <?php echo h($kesimpulan_umum); ?>
                        </span>
                    <?php } else { ?>
                        -
                    <?php } ?> -->
                </td>
            </tr>
        </table>


        <table class="result-table">
            <thead>
                <tr>
                    <th width="40">No.</th>
                    <th>Jenis Pemeriksaan</th>
                    <th>Parameter yang diperiksa</th>
                    <th>Hasil Persentase</th>
                    <th>Kriteria % yang diterima</th>
                    <th>Kesimpulan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($head['produk'] == 'PRC' || $head['produk'] == 'PRC 450') { ?>
                    <?php if (!$produk_supported) { ?>
                        <tr>
                            <td class="center" colspan="6"><?php echo h($standar_produk['note']); ?></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="center">1.</td>
                            <td>Pemeriksaan Fisik</td>
                            <td>Volume</td>
                            <td class="center <?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_volume_num; ?>%</td>
                            <td class="center"><?php echo $ambang_volume_num; ?>%</td>
                            <td class="center <?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_volume; ?></td>
                        </tr>
                        <tr>
                            <td class="center"></td>
                            <td></td>
                            <td>Hemolisis</td>
                            <td class="center <?php echo ($hasil_hemolisis_num < $ambang_hemolisis_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_hemolisis_num; ?>%</td>
                            <td class="center"><?php echo $ambang_hemolisis_num; ?>%</td>
                            <td class="center <?php echo ($hasil_hemolisis_num < $ambang_hemolisis_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_hemolisis; ?></td>
                        </tr>
                        <tr>
                            <td class="center">2.</td>
                            <td>Pemeriksaan Hematologi</td>
                            <td>Hematokrit</td>
                            <td
                                class="center <?php echo ($hasil_hematokrit_num < $ambang_hematokrit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_hematokrit_num; ?>%</td>
                            <td class="center"><?php echo $ambang_hematokrit_num; ?>%</td>
                            <td
                                class="center <?php echo ($hasil_hematokrit_num < $ambang_hematokrit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_hematokrit; ?></td>
                        </tr>
                        <tr>
                            <td class="center"></td>
                            <td></td>
                            <td>Hemoglobin</td>
                            <td
                                class="center <?php echo ($hasil_hemoglobin_num < $ambang_hemoglobin_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_hemoglobin_num; ?>%</td>
                            <td class="center"><?php echo $ambang_hemoglobin_num; ?>%</td>
                            <td
                                class="center <?php echo ($hasil_hemoglobin_num < $ambang_hemoglobin_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_hemoglobin; ?></td>
                        </tr>
                        <tr>
                            <td class="center">3.</td>
                            <td>Pemeriksaan Kontaminasi Bakteri</td>
                            <td>Aerob</td>
                            <td class="center <?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_aerob_num; ?>%</td>
                            <td class="center"><?php echo $ambang_aerob_num; ?>%</td>
                            <td class="center <?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_aerob; ?></td>
                        </tr>
                        <tr>
                            <td class="center"></td>
                            <td></td>
                            <td>An-Aerob</td>
                            <td class="center <?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_anaerob_num; ?>%</td>
                            <td class="center"><?php echo $ambang_anaerob_num; ?>%</td>
                            <td class="center <?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_anaerob; ?></td>
                        </tr>
                    <?php }
                } elseif ($head['produk'] == 'WB' || $head['produk'] == 'WB 450') { ?>
                    <?php if (!$produk_supported) { ?>
                        <tr>
                            <td class="center" colspan="6"><?php echo h($standar_produk['note']); ?></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="center">1.</td>
                            <td>Pemeriksaan Fisik</td>
                            <td>Volume</td>
                            <td class="center <?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_volume_num; ?>%</td>
                            <td class="center"><?php echo $ambang_volume_num; ?>%</td>
                            <td class="center <?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_volume; ?></td>
                        </tr>
                        <tr>
                            <td class="center"></td>
                            <td></td>
                            <td>Hemolisis</td>
                            <td class="center <?php echo ($hasil_hemolisis_num < $ambang_hemolisis_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_hemolisis_num; ?>%</td>
                            <td class="center"><?php echo $ambang_hemolisis_num; ?>%</td>
                            <td class="center <?php echo ($hasil_hemolisis_num < $ambang_hemolisis_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_hemolisis; ?></td>
                        </tr>
                        <tr>
                            <td class="center">2.</td>
                            <td>Pemeriksaan Hematologi</td>
                            <td>Hemoglobin</td>
                            <td
                                class="center <?php echo ($hasil_hemoglobin_num < $ambang_hemoglobin_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_hemoglobin_num; ?>%</td>
                            <td class="center"><?php echo $ambang_hemoglobin_num; ?>%</td>
                            <td
                                class="center <?php echo ($hasil_hemoglobin_num < $ambang_hemoglobin_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_hemoglobin; ?></td>
                        </tr>
                        <tr>
                            <td class="center">3.</td>
                            <td>Pemeriksaan Kontaminasi Bakteri</td>
                            <td>Aerob</td>
                            <td class="center <?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_aerob_num; ?>%</td>
                            <td class="center"><?php echo $ambang_aerob_num; ?>%</td>
                            <td class="center <?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_aerob; ?></td>
                        </tr>
                        <tr>
                            <td class="center"></td>
                            <td></td>
                            <td>An-Aerob</td>
                            <td class="center <?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_anaerob_num; ?>%</td>
                            <td class="center"><?php echo $ambang_anaerob_num; ?>%</td>
                            <td class="center <?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_anaerob; ?></td>
                        </tr>
                    <?php }
                } elseif ($head['produk'] == 'TC') { ?>
                    <?php if (!$produk_supported) { ?>
                        <tr>
                            <td class="center" colspan="6"><?php echo h($standar_produk['note']); ?></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="center" rowspan='3'>1.</td>
                            <td rowspan='3'>Pemeriksaan Fisik</td>
                            <td>Volume</td>
                            <td class="center <?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_volume_num; ?>%</td>
                            <td class="center"><?php echo $ambang_volume_num; ?>%</td>
                            <td class="center <?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_volume; ?></td>
                        </tr>
                        <tr>
                            <td>pH</td>
                            <td class="center <?php echo ($hasil_ph_num < $ambang_ph_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_ph_num; ?>%</td>
                            <td class="center"><?php echo $ambang_ph_num; ?>%</td>
                            <td class="center <?php echo ($hasil_ph_num < $ambang_ph_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_ph; ?></td>
                        </tr>
                        <tr>
                            <td>Swirling</td>
                            <td class="center <?php echo ($hasil_swirling_num < $ambang_swirling_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_swirling_num; ?>%</td>
                            <td class="center"><?php echo $ambang_swirling_num; ?>%</td>
                            <td class="center <?php echo ($hasil_swirling_num < $ambang_swirling_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_swirling; ?></td>
                        </tr>
                        <tr>
                            <td class="center" rowspan='2'>2.</td>
                            <td rowspan='2'>Pemeriksaan Hematologi</td>
                            <td>Trombosit</td>
                            <td class="center <?php echo ($hasil_trombosit_num < $ambang_trombosit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_trombosit_num; ?>%</td>
                            <td class="center"><?php echo $ambang_trombosit_num; ?>%</td>
                            <td class="center <?php echo ($hasil_trombosit_num < $ambang_trombosit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_trombosit; ?></td>
                        </tr>
                        <tr>
                            <td>Leukosit</td>
                            <td class="center <?php echo ($hasil_leukosit_num < $ambang_leukosit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_leukosit_num; ?>%</td>
                            <td class="center"><?php echo $ambang_leukosit_num; ?>%</td>
                            <td class="center <?php echo ($hasil_leukosit_num < $ambang_leukosit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_leukosit; ?></td>
                        </tr>
                        <tr>
                            <td class="center" rowspan='2'>3.</td>
                            <td rowspan='2'>Pemeriksaan Kontaminasi Bakteri</td>
                            <td>Aerob</td>
                            <td class="center <?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_aerob_num; ?>%</td>
                            <td class="center"><?php echo $ambang_aerob_num; ?>%</td>
                            <td class="center <?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_aerob; ?></td>
                        </tr>
                        <tr>
                            <td>An-Aerob</td>
                            <td class="center <?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_anaerob_num; ?>%</td>
                            <td class="center"><?php echo $ambang_anaerob_num; ?>%</td>
                            <td class="center <?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_anaerob; ?></td>
                        </tr>
                    <?php }
                } elseif (strcasecmp($head['produk'], 'TC Aferesis') == 0) { ?>
                    <?php if (!$produk_supported) { ?>
                        <tr>
                            <td class="center" colspan="6"><?php echo h($standar_produk['note']); ?></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="center" rowspan='3'>1.</td>
                            <td rowspan='3'>Pemeriksaan Fisik</td>
                            <td>Volume</td>
                            <td class="center <?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_volume_num; ?>%</td>
                            <td class="center"><?php echo $ambang_volume_num; ?>%</td>
                            <td class="center <?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_volume; ?></td>
                        </tr>
                        <tr>
                            <td>pH</td>
                            <td class="center <?php echo ($hasil_ph_num < $ambang_ph_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_ph_num; ?>%</td>
                            <td class="center"><?php echo $ambang_ph_num; ?>%</td>
                            <td class="center <?php echo ($hasil_ph_num < $ambang_ph_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_ph; ?></td>
                        </tr>
                        <tr>
                            <td>Swirling</td>
                            <td class="center <?php echo ($hasil_swirling_num < $ambang_swirling_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_swirling_num; ?>%</td>
                            <td class="center"><?php echo $ambang_swirling_num; ?>%</td>
                            <td class="center <?php echo ($hasil_swirling_num < $ambang_swirling_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_swirling; ?></td>
                        </tr>
                        <tr>
                            <td class="center" rowspan='2'>2.</td>
                            <td rowspan='2'>Pemeriksaan Hematologi</td>
                            <td>&Sigma; Trombosit/Unit ( &times; 10<sup>11</sup> )</td>
                            <td class="center <?php echo ($hasil_trombosit_num < $ambang_trombosit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_trombosit_num; ?>%</td>
                            <td class="center"><?php echo $ambang_trombosit_num; ?>%</td>
                            <td class="center <?php echo ($hasil_trombosit_num < $ambang_trombosit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_trombosit; ?></td>
                        </tr>
                        <tr>
                            <td>Leukosit ( &times; 10<sup>6</sup> )</td>
                            <td class="center <?php echo ($hasil_leukosit_num < $ambang_leukosit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_leukosit_num; ?>%</td>
                            <td class="center"><?php echo $ambang_leukosit_num; ?>%</td>
                            <td class="center <?php echo ($hasil_leukosit_num < $ambang_leukosit_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_leukosit; ?></td>
                        </tr>
                        <tr>
                            <td class="center" rowspan='2'>3.</td>
                            <td rowspan='2'>Pemeriksaan Kontaminasi Bakteri</td>
                            <td>Aerob</td>
                            <td class="center <?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_aerob_num; ?>%</td>
                            <td class="center"><?php echo $ambang_aerob_num; ?>%</td>
                            <td class="center <?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_aerob; ?></td>
                        </tr>
                        <tr>
                            <td>An-Aerob</td>
                            <td class="center <?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $hasil_anaerob_num; ?>%</td>
                            <td class="center"><?php echo $ambang_anaerob_num; ?>%</td>
                            <td class="center <?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                                <?php echo $ket_anaerob; ?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>

        <div class="footer-note">
            Ket: Kriteria % yang diterima pada semua parameter yang diperiksa sesuai dengan standar Permenkes No. 91
            Tahun 2015 tentang Standar Pelayanan Transfusi Darah.
        </div>

        <div class="footer-note" style="margin-top:18px;">
            <b>Kesimpulan:</b>
            <textarea name="kesimpulan" rows="4" cols="30" style="width: 100%;"></textarea>
        </div>

        <div class="signature">
            Jakarta, <?php echo date('d') . ' ' . $bln_indo[date('m')] . ' ' .  date('Y'); ?><br><br>
            Unit Donor Darah Pusat<br>
            PALANG MERAH INDONESIA<br>
            Manajer Kualitas
            <div class="name">dr. Sri Hartaty, M. Biomed</div>
        </div>
    </div>

    <!-- HALAMAN 2 -->
    <div class="page">
        <div class="lampiran-title">Lampiran 1. <?php echo $lembar_analisa; ?></div>

        <table class="info-table">
            <tr>
                <td width="250">Nama UDD</td>
                <td width="5">:</td>
                <td><?php echo htmlspecialchars($nama_udd_pengirim, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>Tanggal Terima</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($tanggal_terima, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($periode, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>Kondisi Sampel</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($kondisi_sampel, ENT_QUOTES); ?></td>
            </tr>
            <tr>
                <td>Suhu sampel saat diterima</td>
                <td>:</td>
                <td><?php echo $suhu_sampel; ?></td>
            </tr>
        </table>
        <?php
        if ($head['produk'] == 'PRC' || $head['produk'] == "PRC 450") {
        ?>
            <table class="sample-table">
                <thead>
                    <tr>
                        <th rowspan="4">No</th>
                        <!-- <th>Asal Sampel</th> -->
                        <th rowspan="4">Tanggal Periksa</th>
                        <th colspan="8">Pemeriksaan Fisik</th>
                        <th colspan="2" rowspan="2">Pemeriksaan Hematologi</th>
                        <th colspan="2" rowspan="2">Pemeriksaan Kontaminasi Bakteri</th>
                    </tr>
                    <tr>
                        <th rowspan="3">Jenis Kantong</th>
                        <th colspan="4">Label Identitas Kantong</th>
                        <th rowspan="3">Berat (gr)</th>
                        <th rowspan="3">Volume</th>
                        <th rowspan="3">Inspeksi<br>Hemolisis<br>(< 0,8%) </th>
                    </tr>
                    <tr>
                        <th rowspan="2">No. Kantong</th>
                        <th rowspan="2">Gol. Darah</th>
                        <th colspan="2">Tanggal</th>
                        <th rowspan="2">Hematokrit<br>65 - 75 %</th>
                        <th rowspan="2">Hemoglobin</th>
                        <th rowspan="2">Aerob</th>
                        <th rowspan="2">An-Aerob</th>
                    </tr>
                    <tr>
                        <th>Pembuatan</th>
                        <th>Kedaluwarsa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($data as $row) {
                        $tgl_aftap = !empty($row['tglaftap']) ? date('d/m/Y', strtotime($row['tglaftap'])) : '-';
                        $tgl_exp   = !empty($row['kadaluwarsa']) ? date('d/m/Y', strtotime($row['kadaluwarsa'])) : '-';
                        $tgl_qc    = !empty($row['qctgl']) ? date('d/m/Y', strtotime($row['qctgl'])) : '-';

                        $volume_val     = toFloatVal(isset($row['volume']) ? $row['volume'] : 0);
                        $hemolisis_val  = toFloatVal(isset($row['hemolisis']) ? $row['hemolisis'] : 0);
                        $hematokrit_val = toFloatVal(isset($row['hematokrit']) ? $row['hematokrit'] : 0);
                        $hemoglobin_val = toFloatVal(isset($row['hemoglobin']) ? $row['hemoglobin'] : 0);

                        $row_produk = isset($row['produk']) ? $row['produk'] : (isset($head['produk']) ? $head['produk'] : '');
                        $row_volume_asal = isset($row['volume_asal']) ? $row['volume_asal'] : 0;
                        $row_standar = getStandarProduk($row_produk, $row_volume_asal);

                        if ($row_standar['supported']) {
                            $volume_lulus     = lulusRange($volume_val, $row_standar['volume_min'], $row_standar['volume_max']);
                            $hemolisis_lulus  = lulusMax($hemolisis_val, $row_standar['hemolisis_max']);
                            $hematokrit_lulus = lulusRange($hematokrit_val, $row_standar['hematokrit_min'], $row_standar['hematokrit_max']);
                            $hemoglobin_lulus = lulusMin($hemoglobin_val, $row_standar['hemoglobin_min']);
                            $aerob_lulus      = isNegatif(isset($row['aerob']) ? $row['aerob'] : '');
                            $anaerob_lulus    = isNegatif(isset($row['anaerob']) ? $row['anaerob'] : '');
                        } else {
                            $volume_lulus = true;
                            $hemolisis_lulus = true;
                            $hematokrit_lulus = true;
                            $hemoglobin_lulus = true;
                            $aerob_lulus = true;
                            $anaerob_lulus = true;
                        }

                        $aerob_display   = isNegatif(isset($row['aerob']) ? $row['aerob'] : '') ? 'Negatif' : trim((string)(isset($row['aerob']) ? $row['aerob'] : '-'));
                        $anaerob_display = isNegatif(isset($row['anaerob']) ? $row['anaerob'] : '') ? 'Negatif' : trim((string)(isset($row['anaerob']) ? $row['anaerob'] : '-'));
                        if ($aerob_display === '') {
                            $aerob_display = '-';
                        }
                        if ($anaerob_display === '') {
                            $anaerob_display = '-';
                        }
                    ?>
                        <tr>
                            <td class="center"><?php echo $no++; ?></td>
                            <td class="center"><?php echo h($tgl_qc); ?></td>
                            <td class="center">
                                <?php echo h(isset($row['merk']) ? $row['merk'] : '') . ' ' . h(getJenisKantongKode(isset($row['jenis']) ? $row['jenis'] : '')) . ' ' . h(isset($row['volume_asal']) ? $row['volume_asal'] : ''); ?>
                            </td>
                            <td class="center"><?php echo h(isset($row['nokantong']) ? $row['nokantong'] : '-'); ?></td>
                            <td class="center">
                                <?php echo h(isset($row['gol_darah']) ? $row['gol_darah'] . ' ' . (isset($row['rhesus']) ? $row['rhesus'] : '') : '-'); ?>
                            </td>
                            <td class="center"><?php echo h($tgl_aftap); ?></td>
                            <td class="center"><?php echo h($tgl_exp); ?></td>
                            <td class="center"><?php echo h(isset($row['berat_isi']) ? $row['berat_isi'] : '-'); ?></td>
                            <td class="center">
                                <?php echo displayCell(isset($row['volume']) ? $row['volume'] : '-', $volume_lulus, 'text'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($hemolisis_val, $hemolisis_lulus, 'number1'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell(round($hematokrit_val), $hematokrit_lulus, 'round'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell(round($hemoglobin_val), $hemoglobin_lulus, 'round'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($aerob_display, $aerob_lulus, 'text'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($anaerob_display, $anaerob_lulus, 'text'); ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr class="center">
                        <td colspan="7" style="text-align: left; border-left:0; border-bottom:0;">Diperiksa oleh:
                            <?= $row['qcuser'] ?></td>
                        <td>%Lulus</td>
                        <td class="<?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_volume_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hemolisis_num < $ambang_hemolisis_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_hemolisis_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hematokrit_num < $ambang_hematokrit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_hematokrit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hemoglobin_num < $ambang_hemoglobin_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_hemoglobin_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_aerob_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_anaerob_num . '%' : '-'; ?></td>
                    </tr>
                    <tr class="center">
                        <td colspan="7" style="text-align: left; border-left:0; border-top:0; border-bottom:0;">Dicek oleh:
                            <?= $row['qcchecker'] ?></td>
                        <td>Hasil</td>
                        <td class="<?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_volume_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hemolisis_num < $ambang_hemolisis_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_hemolisis_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hematokrit_num < $ambang_hematokrit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_hematokrit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hemoglobin_num < $ambang_hemoglobin_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_hemoglobin_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_aerob_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_anaerob_num . '%' : '-'; ?></td>
                    </tr>
                    <?php if (!$produk_supported) { ?>
                        <tr>
                            <td colspan="14" class="center"><?php echo h($standar_produk['note']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        <?php } elseif ($head['produk'] == 'WB' || $head['produk'] == "WB 450") { ?>
            <table class="sample-table">
                <thead>
                    <tr>
                        <th rowspan="4">No</th>
                        <!-- <th>Asal Sampel</th> -->
                        <th rowspan="4">Tanggal Periksa</th>
                        <th colspan="8">Pemeriksaan Fisik</th>
                        <th rowspan="2">Pemeriksaan Hematologi</th>
                        <th colspan="2" rowspan="2">Pemeriksaan Kontaminasi Bakteri</th>
                    </tr>
                    <tr>
                        <th rowspan="3">Jenis Kantong</th>
                        <th colspan="4">Label Identitas Kantong</th>
                        <th rowspan="3">Berat (gr)</th>
                        <th rowspan="3">Volume</th>
                        <th rowspan="3">Inspeksi<br>Hemolisis<br>(< 0,8%) </th>
                    </tr>
                    <tr>
                        <th rowspan="2">No. Kantong</th>
                        <th rowspan="2">Gol. Darah</th>
                        <th colspan="2">Tanggal</th>
                        <th rowspan="2">Hemoglobin</th>
                        <th rowspan="2">Aerob</th>
                        <th rowspan="2">An-Aerob</th>
                    </tr>
                    <tr>
                        <th>Pembuatan</th>
                        <th>Kedaluwarsa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($data as $row) {
                        $tgl_aftap = !empty($row['tglaftap']) ? date('d/m/Y', strtotime($row['tglaftap'])) : '-';
                        $tgl_exp   = !empty($row['kadaluwarsa']) ? date('d/m/Y', strtotime($row['kadaluwarsa'])) : '-';
                        $tgl_qc    = !empty($row['qctgl']) ? date('d/m/Y', strtotime($row['qctgl'])) : '-';

                        $volume_val     = toFloatVal(isset($row['volume']) ? $row['volume'] : 0);
                        $hemolisis_val  = toFloatVal(isset($row['hemolisis']) ? $row['hemolisis'] : 0);
                        $hematokrit_val = toFloatVal(isset($row['hematokrit']) ? $row['hematokrit'] : 0);
                        $hemoglobin_val = toFloatVal(isset($row['hemoglobin']) ? $row['hemoglobin'] : 0);

                        $row_produk = isset($row['produk']) ? $row['produk'] : (isset($head['produk']) ? $head['produk'] : '');
                        $row_volume_asal = isset($row['volume_asal']) ? $row['volume_asal'] : 0;
                        $row_standar = getStandarProduk($row_produk, $row_volume_asal);

                        if ($row_standar['supported']) {
                            $volume_lulus     = lulusRange($volume_val, $row_standar['volume_min'], $row_standar['volume_max']);
                            $hemolisis_lulus  = lulusMax($hemolisis_val, $row_standar['hemolisis_max']);
                            $hematokrit_lulus = lulusRange($hematokrit_val, $row_standar['hematokrit_min'], $row_standar['hematokrit_max']);
                            $hemoglobin_lulus = lulusMin($hemoglobin_val, $row_standar['hemoglobin_min']);
                            $aerob_lulus      = isNegatif(isset($row['aerob']) ? $row['aerob'] : '');
                            $anaerob_lulus    = isNegatif(isset($row['anaerob']) ? $row['anaerob'] : '');
                        } else {
                            $volume_lulus = true;
                            $hemolisis_lulus = true;
                            $hematokrit_lulus = true;
                            $hemoglobin_lulus = true;
                            $aerob_lulus = true;
                            $anaerob_lulus = true;
                        }

                        $aerob_display   = isNegatif(isset($row['aerob']) ? $row['aerob'] : '') ? 'Negatif' : trim((string)(isset($row['aerob']) ? $row['aerob'] : '-'));
                        $anaerob_display = isNegatif(isset($row['anaerob']) ? $row['anaerob'] : '') ? 'Negatif' : trim((string)(isset($row['anaerob']) ? $row['anaerob'] : '-'));
                        if ($aerob_display === '') {
                            $aerob_display = '-';
                        }
                        if ($anaerob_display === '') {
                            $anaerob_display = '-';
                        }
                    ?>
                        <tr>
                            <td class="center"><?php echo $no++; ?></td>
                            <td class="center"><?php echo h($tgl_qc); ?></td>
                            <td class="center">
                                <?php echo h(isset($row['merk']) ? $row['merk'] : '') . ' ' . h(getJenisKantongKode(isset($row['jenis']) ? $row['jenis'] : '')) . ' ' . h(isset($row['volume_asal']) ? $row['volume_asal'] : ''); ?>
                            </td>
                            <td class="center"><?php echo h(isset($row['nokantong']) ? $row['nokantong'] : '-'); ?></td>
                            <td class="center">
                                <?php echo h(isset($row['gol_darah']) ? $row['gol_darah'] . ' ' . (isset($row['rhesus']) ? $row['rhesus'] : '') : '-'); ?>
                            </td>
                            <td class="center"><?php echo h($tgl_aftap); ?></td>
                            <td class="center"><?php echo h($tgl_exp); ?></td>
                            <td class="center"><?php echo h(isset($row['berat_isi']) ? $row['berat_isi'] : '-'); ?></td>
                            <td class="center">
                                <?php echo displayCell(isset($row['volume']) ? $row['volume'] : '-', $volume_lulus, 'text'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($hemolisis_val, $hemolisis_lulus, 'number1'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell(round($hemoglobin_val), $hemoglobin_lulus, 'round'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($aerob_display, $aerob_lulus, 'text'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($anaerob_display, $anaerob_lulus, 'text'); ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr class="center">
                        <td colspan="7" style="text-align: left; border-left:0; border-bottom:0;">Diperiksa oleh:
                            <?= $row['qcuser'] ?></td>
                        <td>%Lulus</td>
                        <td class="<?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_volume_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hemolisis_num < $ambang_hemolisis_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_hemolisis_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hemoglobin_num < $ambang_hemoglobin_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_hemoglobin_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_aerob_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_anaerob_num . '%' : '-'; ?></td>
                    </tr>
                    <tr class="center">
                        <td colspan="7" style="text-align: left; border-left:0; border-top:0; border-bottom:0;">Dicek oleh:
                            <?= $row['qcchecker'] ?></td>
                        <td>Hasil</td>
                        <td class="<?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_volume_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hemolisis_num < $ambang_hemolisis_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_hemolisis_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_hemoglobin_num < $ambang_hemoglobin_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_hemoglobin_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_aerob_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_anaerob_num . '%' : '-'; ?></td>
                    </tr>
                    <?php if (!$produk_supported) { ?>
                        <tr>
                            <td colspan="14" class="center"><?php echo h($standar_produk['note']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } elseif ($head['produk'] == 'TC') { ?>
            <table class="sample-table">
                <thead>
                    <tr>
                        <th rowspan="4">No</th>
                        <!-- <th>Asal Sampel</th> -->
                        <th rowspan="4">Tanggal Periksa</th>
                        <th colspan="9">Pemeriksaan Fisik</th>
                        <th rowspan="2" colspan="2">Pemeriksaan Hematologi</th>
                        <th colspan="2" rowspan="2">Pemeriksaan Kontaminasi Bakteri</th>
                    </tr>
                    <tr>
                        <th rowspan="3">Jenis Kantong</th>
                        <th colspan="4">Label Identitas Kantong</th>
                        <th rowspan="3">Berat (gr)</th>
                        <th rowspan="3">Volume<br> > 31 mL (350mL)<br> > 40 mL (450 mL)</th>
                        <th rowspan="3">pH<br> > 6.4 </th>
                        <th rowspan="3">Swirling<br> (ada) </th>
                    </tr>
                    <tr>
                        <th rowspan="2">No. Kantong</th>
                        <th rowspan="2">Gol. Darah</th>
                        <th colspan="2">Tanggal</th>
                        <th rowspan="2">&Sigma; Trombosit/Unit<br>( &times; 10<sup>9</sup> )</th>
                        <th rowspan="2">Leukosit<br>( &times; 10<sup>9</sup> )</th>
                        <th rowspan="2">Aerob</th>
                        <th rowspan="2">An-Aerob</th>
                    </tr>
                    <tr>
                        <th>Pembuatan</th>
                        <th>Kedaluwarsa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($data as $row) {
                        $tgl_aftap = !empty($row['tglaftap']) ? date('d/m/Y', strtotime($row['tglaftap'])) : '-';
                        $tgl_exp   = !empty($row['kadaluwarsa']) ? date('d/m/Y', strtotime($row['kadaluwarsa'])) : '-';
                        $tgl_qc    = !empty($row['qctgl']) ? date('d/m/Y', strtotime($row['qctgl'])) : '-';

                        $volume_val     = toFloatVal(isset($row['volume']) ? $row['volume'] : 0);
                        $ph_val         = toFloatVal(isset($row['ph']) ? $row['ph'] : 0);
                        $swirling_val   = isset($row['swirling']) ? $row['swirling'] : '';
                        $trombosit_val  = toFloatVal(isset($row['trombosit']) ? round($row['trombosit']) : 0);
                        $leukosit_val   = toFloatVal(isset($row['leukosit']) ? $row['leukosit'] : 0);

                        $row_produk = isset($row['produk']) ? $row['produk'] : (isset($head['produk']) ? $head['produk'] : '');
                        $row_volume_asal = isset($row['volume_asal']) ? $row['volume_asal'] : 0;
                        $row_standar = getStandarProduk($row_produk, $row_volume_asal);

                        if ($row_standar['supported']) {
                            $volume_lulus     = lulusRange($volume_val, $row_standar['volume_min'], $row_standar['volume_max']);
                            $ph_lulus         = lulusMin($ph_val, $row_standar['ph_min']);
                            $swirling_lulus   = isNegatif($swirling_val);
                            $trombosit_lulus  = lulusMin($trombosit_val, $row_standar['trombosit_min']);
                            $leukosit_lulus   = lulusMax($leukosit_val, $row_standar['leukosit_max']);
                            $aerob_lulus      = isNegatif(isset($row['aerob']) ? $row['aerob'] : '');
                            $anaerob_lulus    = isNegatif(isset($row['anaerob']) ? $row['anaerob'] : '');
                        } else {
                            $volume_lulus = true;
                            $ph_lulus = true;
                            $swirling_lulus = true;
                            $trombosit_lulus = true;
                            $leukosit_lulus = true;
                            $aerob_lulus = true;
                            $anaerob_lulus = true;
                        }

                        $swirling_display   = $swirling_val == 0 ? 'Ada' : 'Tidak ada';

                        $aerob_display   = isNegatif(isset($row['aerob']) ? $row['aerob'] : '') ? 'Negatif' : trim((string)(isset($row['aerob']) ? $row['aerob'] : '-'));
                        $anaerob_display = isNegatif(isset($row['anaerob']) ? $row['anaerob'] : '') ? 'Negatif' : trim((string)(isset($row['anaerob']) ? $row['anaerob'] : '-'));
                        if ($aerob_display === '') {
                            $aerob_display = '-';
                        }
                        if ($anaerob_display === '') {
                            $anaerob_display = '-';
                        }
                    ?>
                        <tr>
                            <td class="center"><?php echo $no++; ?></td>
                            <td class="center"><?php echo h($tgl_qc); ?></td>
                            <td class="center">
                                <?php echo h(isset($row['merk']) ? $row['merk'] : '') . ' ' . h(getJenisKantongKode(isset($row['jenis']) ? $row['jenis'] : '')) . ' ' . h(isset($row['volume_asal']) ? $row['volume_asal'] : ''); ?>
                            </td>
                            <td class="center"><?php echo h(isset($row['nokantong']) ? $row['nokantong'] : '-'); ?></td>
                            <td class="center">
                                <?php echo h(isset($row['gol_darah']) ? $row['gol_darah'] . ' ' . (isset($row['rhesus']) ? $row['rhesus'] : '') : '-'); ?>
                            </td>
                            <td class="center"><?php echo h($tgl_aftap); ?></td>
                            <td class="center"><?php echo h($tgl_exp); ?></td>
                            <td class="center"><?php echo h(isset($row['berat_isi']) ? $row['berat_isi'] : '-'); ?></td>
                            <td class="center">
                                <?php echo displayCell(isset($row['volume']) ? $row['volume'] : '-', $volume_lulus, 'text'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($ph_val, $ph_lulus, 'number1'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($swirling_display, $swirling_lulus, 'text'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($trombosit_val, $trombosit_lulus, 'round'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($leukosit_val, $leukosit_lulus, 'number2'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($aerob_display, $aerob_lulus, 'text'); ?>
                            </td>
                            <td class="center">
                                <?php echo displayCell($anaerob_display, $anaerob_lulus, 'text'); ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr class="center">
                        <td colspan="7" style="text-align: left; border-left:0; border-bottom:0;">Diperiksa oleh:
                            <?= $row['qcuser'] ?></td>
                        <td>%Lulus</td>
                        <td class="<?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_volume_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_ph_num < $ambang_ph_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_ph_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_swirling_num < $ambang_swirling_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_swirling_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_trombosit_num < $ambang_trombosit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_trombosit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_leukosit_num < $ambang_leukosit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_leukosit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_aerob_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_anaerob_num . '%' : '-'; ?></td>
                    </tr>
                    <tr class="center">
                        <td colspan="7" style="text-align: left; border-left:0; border-top:0; border-bottom:0;">Dicek oleh:
                            <?= $row['qcchecker'] ?></td>
                        <td>Hasil</td>
                        <td class="<?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_volume_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_ph_num < $ambang_ph_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_ph_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_swirling_num < $ambang_swirling_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_swirling_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_trombosit_num < $ambang_trombosit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_trombosit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_leukosit_num < $ambang_leukosit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_leukosit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_aerob_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_anaerob_num . '%' : '-'; ?></td>
                    </tr>
                    <?php if (!$produk_supported) { ?>
                        <tr>
                            <td colspan="14" class="center"><?php echo h($standar_produk['note']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } elseif ($head['produk'] == 'FFP') { ?>

        <?php } elseif ($head['produk'] == 'AHF') { ?>

        <?php } elseif (strcasecmp($head['produk'], 'TC Aferesis') == 0) { ?>
            <table class="sample-table">
                <thead>
                    <tr>
                        <th rowspan="4">No</th>
                        <!-- <th>Asal Sampel</th> -->
                        <th rowspan="4">Tanggal Periksa</th>
                        <th colspan="9">Pemeriksaan Fisik</th>
                        <th rowspan="2" colspan="2">Pemeriksaan Hematologi</th>
                        <th colspan="2" rowspan="2">Pemeriksaan Kontaminasi Bakteri</th>
                    </tr>
                    <tr>
                        <th rowspan="3">Jenis Kantong</th>
                        <th colspan="4">Label Identitas Kantong</th>
                        <th rowspan="3">Berat (gr)</th>
                        <th rowspan="3">Volume<br> 100 - 400 mL</th>
                        <th rowspan="3">pH<br> &gt; 6,4 </th>
                        <th rowspan="3">Swirling<br> (ada) </th>
                    </tr>
                    <tr>
                        <th rowspan="2">No. Kantong</th>
                        <th rowspan="2">Gol. Darah</th>
                        <th colspan="2">Tanggal</th>
                        <th rowspan="2">&Sigma; Trombosit/Unit<br>( &times; 10<sup>11</sup> )</th>
                        <th rowspan="2">Leukosit<br>( &times; 10<sup>6</sup> )</th>
                        <th rowspan="2">Aerob</th>
                        <th rowspan="2">An-Aerob</th>
                    </tr>
                    <tr>
                        <th>Pembuatan</th>
                        <th>Kedaluwarsa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($data as $row) {
                        $row_produk = isset($row['produk']) ? $row['produk'] : (isset($head['produk']) ? $head['produk'] : '');
                        if (strcasecmp($row_produk, 'TC Aferesis') != 0) continue;

                        $tgl_aftap = !empty($row['tglaftap']) ? date('d/m/Y', strtotime($row['tglaftap'])) : '-';
                        $tgl_exp   = !empty($row['kadaluwarsa']) ? date('d/m/Y', strtotime($row['kadaluwarsa'])) : '-';
                        $tgl_qc    = !empty($row['qctgl']) ? date('d/m/Y', strtotime($row['qctgl'])) : '-';

                        $volume_val     = toFloatVal(isset($row['volume']) ? $row['volume'] : 0);
                        $ph_val         = toFloatVal(isset($row['ph']) ? $row['ph'] : 0);
                        $swirling_val   = isset($row['swirling']) ? $row['swirling'] : '';
                        $trombosit_val  = toFloatVal(isset($row['trombosit']) ? $row['trombosit'] : 0);
                        $leukosit_val   = toFloatVal(isset($row['leukosit']) ? $row['leukosit'] : 0);
                    ?>
                        <tr class="center">
                            <td><?= $no++; ?></td>
                            <td><?= $tgl_qc; ?></td>
                            <td><?= isset($row['jeniskantong']) ? h($row['jeniskantong']) : '-'; ?></td>
                            <td><?= isset($row['nokantong']) ? h($row['nokantong']) : '-'; ?></td>
                            <td><?= (isset($row['gol_darah']) ? h($row['gol_darah']) : '-') . ' ' . (isset($row['RhesusDrh']) ? h($row['RhesusDrh']) : ''); ?>
                            </td>
                            <td><?= $tgl_aftap; ?></td>
                            <td><?= $tgl_exp; ?></td>
                            <td><?= isset($row['berat_isi']) ? number_format($row['berat_isi'], 0) : '-'; ?></td>
                            <td><?= number_format($volume_val, 0); ?></td>
                            <td><?= number_format($ph_val, 1); ?></td>
                            <td><?= h($swirling_val); ?></td>
                            <td><?= number_format($trombosit_val, 1); ?></td>
                            <td><?= number_format($leukosit_val, 2); ?></td>
                            <td><?= isset($row['aerob']) ? h($row['aerob']) : '-'; ?></td>
                            <td><?= isset($row['anaerob']) ? h($row['anaerob']) : '-'; ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="center">
                        <td colspan="7" style="text-align: left; border-left:0; border-top:0; border-bottom:0;">Diperiksa
                            oleh:
                            <?= isset($data[0]['qcchecker']) ? h($data[0]['qcchecker']) : ''; ?></td>
                        <td>%Lulus</td>
                        <td class="<?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_volume_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_ph_num < $ambang_ph_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_ph_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_swirling_num < $ambang_swirling_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_swirling_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_trombosit_num < $ambang_trombosit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_trombosit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_leukosit_num < $ambang_leukosit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_leukosit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_aerob_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $ambang_anaerob_num . '%' : '-'; ?></td>
                    </tr>
                    <tr class="center">
                        <td colspan="7" style="text-align: left; border-left:0; border-top:0; border-bottom:0;">Dicek oleh:
                            <?= isset($data[0]['qcuser']) ? h($data[0]['qcuser']) : ''; ?></td>
                        <td>Hasil</td>
                        <td class="<?php echo ($hasil_volume_num < $ambang_volume_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_volume_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_ph_num < $ambang_ph_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_ph_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_swirling_num < $ambang_swirling_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_swirling_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_trombosit_num < $ambang_trombosit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_trombosit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_leukosit_num < $ambang_leukosit_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_leukosit_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_aerob_num < $ambang_aerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_aerob_num . '%' : '-'; ?></td>
                        <td class="<?php echo ($hasil_anaerob_num < $ambang_anaerob_num) ? 'fail-cell' : ''; ?>">
                            <?php echo $produk_supported ? $hasil_anaerob_num . '%' : '-'; ?></td>
                    </tr>
                    <?php if (!$produk_supported) { ?>
                        <tr>
                            <td colspan="15" class="center"><?php echo h($standar_produk['note']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } elseif ($head['produk'] == 'PRC LEUCODEPLETED') { ?>

        <?php } elseif ($head['produk'] == 'PRC LEUCOREDUCTION') { ?>

        <?php } elseif ($head['produk'] == 'PLASMA AFERESIS') { ?>

        <?php } ?>

        <div class="box-note-hasil">
            <b>Hasil: </b><br>
            <!-- Inputan Text Area untuk hasil nya -->
            <textarea name="hasil" rows="4" cols="30" style="width: 100%;"></textarea>
        </div>

        <div class="box-note">
            <b>Catatan:</b>
            * Standar Kelulusan*<br>
            <?php if ($head['produk'] == 'PRC' || $head['produk'] == 'PRC 450') { ?>
                Volume PRC (350 mL) = 218 ± 39 mL (179 - 257 mL)<br>
                Volume PRC (450 mL) = 280 ± 50 mL (230 - 330 mL)<br><br>
                HB/unit (350 mL) = &gt; 35 g/unit<br>
                HB/unit (450 mL) = &gt; 45 g/unit
            <?php } elseif ($head['produk'] == 'WB' || $head['produk'] == 'WB 450') { ?>
                Volume PRC (350 mL) = 350 ± 10 mL (315 - 385 mL)<br>
                Volume PRC (450 mL) = 450 ± 10 mL (405 - 495 mL)<br><br>
                HB/unit (350 mL) = &gt; 35 g/unit<br>
                HB/unit (450 mL) = &gt; 45 g/unit
            <?php } elseif ($head['produk'] == 'TC') { ?>
                Kadar Trombosit/Unit (350 mL) = &gt; 47 x 10 <sup>9</sup><br>
                Kadar Trombosit/Unit (450 mL) = &gt; 60 x 10 <sup>9</sup><br><br>

                Kadar Leukosit/Unit (350 mL) = &lt; 0,16 x 10 <sup>9</sup><br>
                Kadar Leukosit/Unit (450 mL) = &lt; 0,2 x 10 <sup>9</sup><br>
            <?php } elseif (strcasecmp($head['produk'], 'TC Aferesis') == 0) { ?>
                Volume = 100 - 400 mL<br><br>

                Kadar Trombosit/Unit = &ge; 2 x 10 <sup>11</sup><br><br>

                Kadar Leukosit/Unit = &lt; 5 x 10 <sup>6</sup><br>
            <?php } ?>
        </div>

        <div style="clear:both;"></div>

        <div style="margin-top: 160px;">
            <b>Penanggung jawab:</b><br>
            Pjs. Ka. Sub. Bid. Pengawasan Mutu
            <div class="signature">
                <div class="name">dr. Sri Hartaty, M.Biomed</div>
            </div>
        </div>
    </div>

</body>

</html>