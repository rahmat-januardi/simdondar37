<?php
session_start();

// $_get_host = $_SERVER['HTTP_HOST'];
// $_server_public_ip = file_get_contents('https://api.ipify.org');
// $allow_login = ($_get_host == $_server_public_ip) ? false : true;
// $allow_login = true;

include_once 'config/db_config.php';
date_default_timezone_set('Asia/Makassar');
include 'config/dbi_connect.php';
mysqli_query($dbi, "SET SESSION sql_mode = ''");

$sql = mysqli_query($dbi, "SELECT `nama`, `alamat`, `id`, `lat`, `lng`, `telp` FROM `utd` WHERE `aktif`='1';");
$udd = mysqli_fetch_assoc($sql);
$nama_udd = $udd['nama'];
$_SESSION['namaudd'] = $udd['nama'];
$id_udd = $udd['id'];

$date = date('d-m-Y H:i:s', time());

$nama_ultah = array();
$sbd = mysqli_query($dbi, "SELECT `nama_lengkap`, `tgllahir` FROM user WHERE (STR_TO_DATE( CONCAT( YEAR( CURDATE( ) ) , '-', MONTH( `tgllahir` ) , '-', DAY( `tgllahir` ) ) , '%Y-%m-%d' ) = CURDATE() ) AND aktif='0'");
while ($dbd = mysqli_fetch_assoc($sbd)) {
    $nama_ultah[] = $dbd['nama_lengkap'];
}

function get_days_in_month($month, $year)
{
    $days_in_month = array(
        'JAN' => 31,
        'FEB' => 28,
        'MAR' => 31,
        'APR' => 30,
        'MEI' => 31,
        'JUN' => 30,
        'JUL' => 31,
        'AGT' => 31,
        'SEP' => 30,
        'OKT' => 31,
        'NOV' => 30,
        'DES' => 31
    );
    if ($month == 'FEB' && (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0))) {
        return 29;
    }
    return isset($days_in_month[$month]) ? $days_in_month[$month] : 30;
}

$labels = array();
$jumlah = array();
$sukses = array();
$batal = array();
$gagal = array();
$jmlkegiatan = array();
$jmlkegiatan_hari = array();
$labels_chat2 = array();
$keberhasilan_chat2 = array();
$keberhasilan = array();

$query_mu = "SELECT 
    DATE_FORMAT(`TglPenjadwalan`, '%y') AS tahun, 
    ELT(MONTH(`TglPenjadwalan`), 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGT', 'SEP', 'OKT', 'NOV', 'DES') AS Bulan, 
    COUNT(*) AS total_kegiatan, 
    SUM(`jumlah`) AS total_jumlah, 
    SUM(`sukses`) AS total_sukses, 
    SUM(`batal`) AS total_batal, 
    SUM(`gagal`) AS total_gagal
FROM `kegiatan`
WHERE (`jamselesai` IS NOT NULL OR `jamselesai` <> '00:00:00') 
AND DATE(`TglPenjadwalan`) BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 12 MONTH), '%Y-%m-01') AND LAST_DAY(CURDATE())  
GROUP BY YEAR(`TglPenjadwalan`), MONTH(`TglPenjadwalan`)
ORDER BY YEAR(`TglPenjadwalan`), MONTH(`TglPenjadwalan`);";

$result_mu = mysqli_query($dbi, $query_mu);
while ($row = mysqli_fetch_assoc($result_mu)) {
    $days_in_month = get_days_in_month($row['Bulan'], '20' . $row['tahun']);
    $jmlkegiatan[] = $row['total_kegiatan'];
    $jmlkegiatan_hari[] = round($row['total_kegiatan'] / $days_in_month);
    $labels[] = $row['Bulan'];
    $jumlah[] = $row['total_jumlah'];
    $sukses[] = $row['total_sukses'];
    $batal[] = $row['total_batal'];
    $gagal[] = $row['total_gagal'];
    if ($row['total_jumlah'] > 0) {
        $keberhasilan[] = number_format(($row['total_sukses'] / $row['total_jumlah']) * 100, 2);
    } else {
        $keberhasilan[] = 0;
    }
    $labels_chat2[] = $row['Bulan'] . ' ' . $row['tahun'];
    $keberhasilan_chat2[] = number_format(($row['total_sukses'] / $row['total_jumlah']) * 100, 2);
}

$labels_json = json_encode($labels_chat2);
$keberhasilan_json = json_encode($keberhasilan_chat2);
$json_jumlahkegiatan = json_encode($jmlkegiatan);

$tgl_sekarang = date('Y-m-d');
$tglsebelumnya = date('Y-m-01', strtotime('-12 months'));

$sql = "SELECT DATE_FORMAT(tgl, '%y') AS tahun, 
    ELT(MONTH(tgl), 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGT', 'SEP', 'OKT', 'NOV', 'DES') AS Bulan, 
    DATE_FORMAT(tgl, '%Y-%m-01') AS First_Day, 
    COUNT(NoTrans) AS Donasi 
FROM htransaksi 
WHERE tgl BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 12 MONTH), '%Y-%m-01') AND LAST_DAY(CURDATE()) AND Pengambilan = '0'
GROUP BY YEAR(tgl), MONTH(tgl) 
ORDER BY YEAR(tgl), MONTH(tgl);";

$sql_gagal = "SELECT DATE_FORMAT(tgl, '%y') AS tahun, 
    ELT(MONTH(tgl), 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGT', 'SEP', 'OKT', 'NOV', 'DES') AS Bulan, 
    DATE_FORMAT(tgl, '%Y-%m-01') AS First_Day, 
    COUNT(NoTrans) AS Donasi 
FROM htransaksi 
WHERE tgl BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 12 MONTH), '%Y-%m-01') AND LAST_DAY(CURDATE()) AND Pengambilan = '2'
GROUP BY YEAR(tgl), MONTH(tgl) 
ORDER BY YEAR(tgl), MONTH(tgl);";

$tahun = date('y');

$labels = array();
$query = mysqli_query($dbi, $sql);
while ($b = mysqli_fetch_array($query)) {
    $labels[] = $b['Bulan'] . ' ' . $b['tahun'];
}

$donasi_berhasil = array();
$query = mysqli_query($dbi, $sql);
while ($p = mysqli_fetch_array($query)) {
    $donasi_berhasil[] = $p['Donasi'];
}

$donasi_gagal = array();
$query_gagal = mysqli_query($dbi, $sql_gagal);
while ($p_gagal = mysqli_fetch_array($query_gagal)) {
    $donasi_gagal[] = $p_gagal['Donasi'];
}

$galeri_dir = dir('poster/');
$count = 1;
$pattern = '/(gif|jpg|jpeg|png)/';
$imagearray = array();
while ($file = $galeri_dir->read()) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (preg_match($pattern, $ext)) {
        $imagearray[$count] = $file;
        $count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="57x57" href="index/pavicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="index/pavicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="index/pavicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="index/pavicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="index/pavicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="index/pavicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="index/pavicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="index/pavicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="index/pavicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="index/pavicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="index/pavicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="index/pavicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="index/pavicon/favicon-16x16.png">
    <link rel="manifest" href="index/pavicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="index/pavicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title><?php include 'version.php'; ?></title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="stylesheet" href="bootsrap337/fonts/awesome/css/all.css">
    <link href="assets/css/material-kit.css?v=2.0.7" rel="stylesheet" />
    <link href="index/simdondar.css" rel="stylesheet" />
    <link href="index/aos.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="index/materialicon.css" />
    <link rel="stylesheet" href="index/font-awesome.min.css">
    <link rel="stylesheet" href="index/Audiowide.css">
    <link href="index/DataTables/datatables.min.css" rel="stylesheet">
    <style>
    #chartJumlah,
    #chartPersentase,
    #chartJmlMU {
        height: 300px !important;
    }

    #chartdonasi {
        height: 400px !important;
    }

    @media (max-width: 768px) {

        #chartJumlah,
        #chartPersentase,
        #chartJmlMU {
            height: 200px !important;
        }

        #chartdonasi {
            height: 300px !important;
        }
    }
    </style>
</head>

<body class="index-page" onselectstart="return false">
    <div id="loading"></div>
    <div id="tsimdondar"></div>
    <nav class="navbar navbar-transparent navbar-color-on-scroll fixed-top navbar-expand-lg" color-on-scroll="100"
        id="sectionsNav">
        <div class="container">
            <div class="navbar-translate">
                <a class="navbar-brand" href="#">
                    <img id="brandimage" style="max-width: auto; height:70px;" src="index/logosimdondar.png" />
                    <span class="h3 text-center font-weight-bold d-none d-sm-inline"
                        id="logo">&nbsp;<?php include 'version.php'; ?></span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="navbar-toggler-icon"></span>
                    <span class="navbar-toggler-icon"></span>
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="#loginModal" data-toggle="modal"
                            title="Masuk ke SIMDONDAR" data-target="#loginModal"><i
                                class="material-icons">lock_open</i>&nbsp;Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="mobileonline/" target="_blank"><i
                                class="material-icons">tv</i>&nbsp;MU Online</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://ayodonor.pmi.or.id" target="_blank"><i
                                class="material-icons">bloodtype</i>&nbsp;Ayodonor</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://uddhelpdesk.pmi.or.id" target="_blank"><i
                                class="material-icons">live_help</i>&nbsp;Helpdesk</a></li>
                    <li class="dropdown nav-item">
                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown"><i
                                class="material-icons">apps</i> More</a>
                        <div class="dropdown-menu dropdown-with-icons">
                            <a href="antridonor/" class="dropdown-item"><i class="material-icons">layers</i> Antrian
                                Donor</a>
                            <a href="javascript:void(0)" class="dropdown-item" onclick="scrollToStok()"><i
                                    class="material-icons">layers</i> Stok Darah</a>
                            <a href="javascript:void(0)" class="dropdown-item" onclick="scrollToMU()"><i
                                    class="material-icons">book</i> Mobile Unit</a>
                            <a href="javascript:void(0)" class="dropdown-item" onclick="scrollToContactMe()"><i
                                    class="material-icons">contacts</i> Contact Me</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="page-header header-filter purple-filter" data-parallax="true"
        style="background-image: url('index/images/bali.png');">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-10 ml-auto mr-auto">
                    <div class="brand" data-aos="fade-right">
                        <div class="d-sm-none d-block">
                            <h2 class="font-weight-bold simdondar-xs"><?php echo $nama_udd; ?></h2>
                        </div>
                        <div class="d-none d-sm-block">
                            <h1 class="font-weight-bold simdondar"><?php echo $nama_udd; ?></h1>
                        </div>
                        <a class="btn btn-danger btn-round shadow" href="#loginModal" data-toggle="modal"
                            title="Masuk ke SIMDONDAR" data-target="#loginModal"><i
                                class="fa fa-key"></i>&nbsp;Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($nama_ultah)) { ?>
    <div class="modal fade" id="birthdayModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-login modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body text-center bg-dark">
                    <div class="col-sm-12" style="min-height: 450px;">
                        <div id="ultah">
                            <h4>Keluarga besar <br><strong><?php echo $nama_udd; ?></strong></h4>
                            <div>mengucapkan</div>
                            <h3 class="font-weight-bold" style="font-family: 'Condiment';">Selamat Ulang Tahun</h3>
                            <div><i>kepada:</i></div>
                            <div class="font-weight-bold" style="font-size: 1em;">
                                <?php foreach ($nama_ultah as $nama) {
                                        echo $nama . '<br>';
                                    } ?>
                            </div>
                            <h3 style="font-size: 0.8em;">Semoga panjang umur, sehat & selalu dikaruniai</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="main main-raised">
        <div class="section section-white">
            <div class="container">
                <div id="navigation-pills">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <ul class="nav nav-pills nav-pills-icons nav-pills-warning flex" role="tablist">
                                <li class="nav-item"><a class="nav-link active" href="#simdondar1" role="tab"
                                        data-toggle="tab"><i class="fa fa-info-circle"></i>
                                        <div class="d-none d-sm-block">Umum</div>
                                    </a></li>
                                <li class="nav-item"><a class="nav-link" href="#simdondar2" role="tab"
                                        data-toggle="tab"><i class="fa fa-lock"></i>
                                        <div class="d-none d-sm-block">Keamanan</div>
                                    </a></li>
                                <li class="nav-item"><a class="nav-link" href="#simdondar3" role="tab"
                                        data-toggle="tab"><i class="fa fa-check-circle"></i>
                                        <div class="d-none d-sm-block">Validasi</div>
                                    </a></li>
                                <li class="nav-item"><a class="nav-link" href="#simdondar4" role="tab"
                                        data-toggle="tab"><i class="fa fa-network-wired"></i>
                                        <div class="d-none d-sm-block">Integrasi</div>
                                    </a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-content tab-space">
                                    <div class="tab-pane active" id="simdondar1">
                                        <div class="card">
                                            <div class="card-header card-header-success">
                                                <h4 class="card-title">Informasi Umum SIMDONDAR</h4>
                                            </div>
                                            <div class="card-body">
                                                <p><b>SIMDONDAR</b> adalah Sistem Informasi Manajemen Unit Donor Darah
                                                    yang digunakan oleh UDD PMI di seluruh Indonesia untuk mendukung
                                                    proses pelayanan donor darah, pengelolaan stok darah, serta
                                                    distribusi ke rumah sakit. <b>SIMDONDAR telah terdaftar dan memiliki
                                                        hak paten resmi sebagai sistem informasi milik PMI, dengan Nomor
                                                        Pencatatan: 000777750 tertanggal 14 Agustus 2017 di Direktorat
                                                        Jenderal Kekayaan Intelektual (DJKI).</b></p>
                                                <ul>
                                                    <li>SIMDONDAR beroperasi secara <b>offline</b> dan <b>online</b>,
                                                        dengan mekanisme <b>sinkronisasi data</b> ke server pusat yang
                                                        dilakukan setiap malam melalui cronjob.</li>
                                                    <li>SIMDONDAR dikembangkan sejak tahun <b>2009</b></li>
                                                    <li><b>2025, Versi terbaru SIMDONDAR adalah 3.7</b>, yang merupakan
                                                        perbaikan dari versi 3.5 dengan berbagai peningkatan fitur,
                                                        termasuk:
                                                        <ul>
                                                            <li><b>Mobile Unit Online</b>: Memungkinkan pengelolaan
                                                                donor darah dari unit mobile secara real-time.</li>
                                                            <li><b>Sistem Antrean Donor</b>: Memberikan sistem antrean
                                                                yang lebih terstruktur untuk donor darah.</li>
                                                            <li><b>Plasma Master File</b>: Pengelolaan database plasma
                                                                yang lebih terintegrasi.</li>
                                                            <li><b>Modul untuk Fraksionasi Plasma</b>: Menyediakan fitur
                                                                khusus untuk proses fraksionasi plasma.</li>
                                                            <li><b>Perbaikan laporan online</b>: Optimalisasi dalam
                                                                pembuatan dan pelaporan data donor.</li>
                                                            <li><b>Penambahan fitur di hampir semua proses
                                                                    SIMDONDAR</b>: Peningkatan efisiensi dan pengelolaan
                                                                data.</li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="simdondar2">
                                        <div class="card">
                                            <div class="card-header card-header-danger">
                                                <h4 class="card-title">Keamanan</h4>
                                            </div>
                                            <div class="card-body">
                                                <ul>
                                                    <li>Setiap <b>90 hari</b> sekali, SIMDONDAR akan memberikan
                                                        notifikasi untuk mengganti password.</li>
                                                    <li>Jangan pernah menggunakan <b>informasi pribadi</b> seperti nama,
                                                        ulang tahun, username, atau alamat email untuk password Anda.
                                                    </li>
                                                    <li>Gunakan password yang panjang dengan kombinasi angka, simbol,
                                                        huruf kapital dan kecil.</li>
                                                    <li>Hindari menggunakan password dengan <b>pola pengulangan</b> atau
                                                        kata-kata yang dapat ditemukan di dalam kamus.</li>
                                                    <li>Jangan pernah menyimpan password pada komputer yang diakses oleh
                                                        banyak pengguna.</li>
                                                    <li>Tidak memberikan password kepada personil lain, misalnya untuk
                                                        membantu memasukkan data dengan login Anda.</li>
                                                    <li><b>Hak akses pada SIMDONDAR</b> diberikan berdasarkan peran dan
                                                        tanggung jawab masing-masing personil di UDD.</li>
                                                    <li>SIMDONDAR menggunakan sistem <b>log aktivitas</b> untuk mencatat
                                                        setiap perubahan data yang dilakukan oleh pengguna.</li>
                                                    <li>Seluruh data yang disinkronisasi ke server pusat dienkripsi
                                                        untuk menjaga kerahasiaan informasi.</li>
                                                    <li>Pastikan server SIMDONDAR memiliki <b>firewall dan akses
                                                            terbatas</b> hanya untuk personil yang berwenang.</li>
                                                    <li>Backup data harus dilakukan secara berkala untuk mengantisipasi
                                                        kehilangan atau kerusakan data.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="simdondar3">
                                        <div class="card">
                                            <div class="card-header card-header-warning">
                                                <h4 class="card-title">Validasi</h4>
                                            </div>
                                            <div class="card-body">
                                                <p>Setiap Unit Donor Darah (UDD) yang ingin menerapkan SIMDONDAR sebagai
                                                    sistem pendukung layanan wajib melalui tahapan validasi sebagai
                                                    berikut:</p>
                                                <p><b>Minimum Kualifikasi yang Wajib Dilakukan:</b></p>
                                                <ul>
                                                    <li><b>Kualifikasi Instalasi (IQ - Installation Qualification):</b>
                                                        <ul>
                                                            <li>Memastikan perangkat keras dan lunak telah dipasang
                                                                sesuai dengan spesifikasi yang direkomendasikan.</li>
                                                            <li>Memverifikasi bahwa semua komponen sistem telah
                                                                terinstal dengan benar, termasuk server, database, dan
                                                                konfigurasi jaringan.</li>
                                                        </ul>
                                                    </li>
                                                    <li><b>Kualifikasi Operasional (OQ - Operational Qualification):</b>
                                                        <ul>
                                                            <li>Melakukan pengujian setiap modul sistem sesuai dengan
                                                                prosedur yang telah ditentukan.</li>
                                                            <li>Memastikan bahwa semua fitur utama SIMDONDAR, seperti
                                                                input data donor, pengolahan darah, distribusi, dan
                                                                pelaporan, berfungsi dengan baik.</li>
                                                        </ul>
                                                    </li>
                                                    <li><b>Kualifikasi Kinerja (PQ - Performance Qualification):</b>
                                                        <ul>
                                                            <li>Melakukan uji coba sistem dalam kondisi beban kerja
                                                                aktual untuk melihat kestabilan dan kinerjanya.</li>
                                                            <li>Memastikan sistem dapat menangani jumlah pengguna yang
                                                                diperkirakan tanpa mengalami penurunan performa yang
                                                                signifikan.</li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                                <p><b>Idealnya, Tahapan Validasi yang Dilakukan:</b></p>
                                                <ul>
                                                    <li><b>Kualifikasi Desain (DQ - Design Qualification):</b>
                                                        <ul>
                                                            <li>Memastikan desain sistem SIMDONDAR telah memenuhi
                                                                kebutuhan operasional dan regulasi yang berlaku.</li>
                                                            <li>Mengevaluasi arsitektur sistem, keamanan data, dan skema
                                                                penyimpanan informasi.</li>
                                                        </ul>
                                                    </li>
                                                    <li><b>Kualifikasi Instalasi (IQ - Installation Qualification)</b> –
                                                        (Seperti di atas).</li>
                                                    <li><b>Kualifikasi Operasional (OQ - Operational Qualification)</b>
                                                        – (Seperti di atas).</li>
                                                    <li><b>Kualifikasi Kinerja (PQ - Performance Qualification)</b> –
                                                        (Seperti di atas).</li>
                                                </ul>
                                                <p><b>Proses Verifikasi:</b></p>
                                                <ul>
                                                    <li>Setiap tahapan kualifikasi harus didokumentasikan dengan laporan
                                                        hasil uji coba.</li>
                                                    <li>Hasil uji coba harus diperiksa dan disetujui oleh tim IT UDD dan
                                                        manajemen sebelum sistem diimplementasikan secara penuh.</li>
                                                    <li>Jika ditemukan ketidaksesuaian, maka perlu dilakukan tindakan
                                                        perbaikan sebelum sistem digunakan.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="simdondar4">
                                        <div class="card">
                                            <div class="card-header card-header-primary">
                                                <h4 class="card-title">Integrasi</h4>
                                            </div>
                                            <div class="card-body">
                                                <p>SIMDONDAR telah dikembangkan dengan sistem terbuka dan modular,
                                                    memungkinkan integrasi dengan berbagai sistem dan perangkat yang
                                                    mendukung operasional Unit Donor Darah (UDD) PMI.</p>
                                                <p><b>Integrasi dengan Peralatan Otomatis</b></p>
                                                <ul>
                                                    <li>Terhubung dengan seluruh peralatan otomatis yang digunakan di
                                                        UDD PMI, seperti:
                                                        <ul>
                                                            <li>Hematology Analyzer</li>
                                                            <li>Alat Immunologi untuk pemeriksaan Skrining IMLTD</li>
                                                            <li>NAT Screening System</li>
                                                            <li>Dapat diintegrasikan dengan Blood Bank Refrigerator yang
                                                                sudah mendukung protokol LIS</li>
                                                        </ul>
                                                    </li>
                                                    <li>Memungkinkan hasil pemeriksaan laboratorium langsung masuk ke
                                                        sistem tanpa input manual.</li>
                                                    <li>Memastikan proses donor darah lebih efisien dan mengurangi
                                                        risiko kesalahan input data.</li>
                                                </ul>
                                                <p><b>Integrasi Nasional</b></p>
                                                <ul>
                                                    <li>Terhubung dengan seluruh pengguna SIMDONDAR di Indonesia melalui
                                                        Server Nasional, memungkinkan:
                                                        <ul>
                                                            <li>Sinkronisasi data donor secara real-time.</li>
                                                            <li>Distribusi informasi stok darah antar-UDD.</li>
                                                            <li>Pembaruan jadwal donor dan event secara nasional.</li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                                <p><b>Integrasi dengan Aplikasi & Website Ayo Donor</b></p>
                                                <ul>
                                                    <li>Terhubung dengan aplikasi mobile <b>Ayo Donor</b>, sehingga
                                                        pendonor dapat:
                                                        <ul>
                                                            <li>Mengecek riwayat donasi darahnya.</li>
                                                            <li>Melihat stok darah di UDD terdekat.</li>
                                                            <li>Mendapatkan pengingat jadwal donor berikutnya.</li>
                                                        </ul>
                                                    </li>
                                                    <li>Terintegrasi dengan website resmi PMI: <a
                                                            href="https://ayodonor.pmi.or.id"
                                                            target="_blank">ayodonor.pmi.or.id</a>, yang menyediakan
                                                        informasi stok darah dan jadwal kegiatan donor.</li>
                                                </ul>
                                                <p><b>Integrasi dengan Sistem Kesehatan Nasional</b></p>
                                                <ul>
                                                    <li>Sudah terdaftar di <b>Satu Sehat</b> sebagai bagian dari sistem
                                                        informasi dalam <b>Rekam Medis Elektronik (RME)</b>,
                                                        memungkinkan interoperabilitas dengan rumah sakit dan fasilitas
                                                        kesehatan lainnya.</li>
                                                    <li>Terhubung dengan <b>data nasional untuk Plasma Fraksionasi</b>,
                                                        memastikan pengelolaan plasma konvalesen dan komponen darah
                                                        lebih terstruktur dan terintegrasi.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="space-100"></div>
    <div class="main main-raised">
        <div class="section section-basics" id="stok">
            <div class="container">
                <div class="row">
                    <div class="col-12" data-aos="fade-left">
                        <div id="infostok"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="space-100"></div>
    <div class="main main-raised">
        <div class="section section-basic" id="jadwalmu">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title text-center">JADWAL MOBILE UNIT</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatables" class="table table-bordered table-hover" cellspacing="0"
                                        width="100%" style="width:100%">
                                        <thead class="card-header-primary">
                                            <th class="text-center" style="vertical-align: middle;">NO.</th>
                                            <th class="text-center" style="vertical-align: middle;">HARI</th>
                                            <th class="text-center" style="vertical-align: middle;">TGL</th>
                                            <th class="text-center" style="vertical-align: middle;">JAM</th>
                                            <th class="text-center" style="vertical-align: middle;">JML</th>
                                            <th class="text-center" style="vertical-align: middle;">TEMPAT KEGIATAN</th>
                                            <th class="text-center" style="vertical-align: middle;">STATUS</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="space-100"></div>
    <div class="main main-raised">
        <div class="section section-basic" id="grafikdonnasi">
            <div class="container-fluid text-center">
                <div class="row">
                    <div class="col-12" data-aos="flip-left">
                        <div class="card">
                            <div class="card-header card-header-warning">
                                <h4 class="card-title text-center">PEROLEHAN DONASI PER BULAN</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="chartdonasi"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="space-100"></div>
    <div class="main main-raised">
        <div class="section section-basic" id="grafikmobileunit">
            <div class="container-fluid text-center">
                <div class="row">
                    <div class="col-12" data-aos="flip-left">
                        <div class="card">
                            <div class="card-header card-header-warning">
                                <h4 class="card-title text-center">Kegiatan Mobile Unit</h4>
                            </div>
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body">
                                        <canvas id="chartJumlah"></canvas>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <canvas id="chartPersentase"></canvas>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <canvas id="chartJmlMU"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="space-100"></div>
    <div class="main main-raised">
        <div class="section section-basic">
            <div class="container">
                <div id="nav-tabs" data-aos="zoom-in">
                    <h3><strong>7 PRINSIP</strong> <small>gerakan Palang Merah dan Bulan Sabit Merah
                            Internasional</small></h3>
                    <div class="row">
                        <div class="col-md-12" data-aos="fade-down">
                            <div class="card card-nav-tabs">
                                <div class="card-header card-header-danger">
                                    <div class="nav-tabs-navigation">
                                        <div class="nav-tabs-wrapper">
                                            <ul class="nav nav-tabs" data-tabs="tabs">
                                                <li class="nav-item"><a class="nav-link active" href="#satu"
                                                        data-toggle="tab">
                                                        <div class="d-sm-none d-block">1</div>
                                                        <div class="d-none d-md-block">Kemanusiaan</div>
                                                    </a></li>
                                                <li class="nav-item"><a class="nav-link" href="#dua" data-toggle="tab">
                                                        <div class="d-md-none d-block">2</div>
                                                        <div class="d-none d-md-block">Kesamaan</div>
                                                    </a></li>
                                                <li class="nav-item"><a class="nav-link" href="#tiga" data-toggle="tab">
                                                        <div class="d-md-none d-block">3</div>
                                                        <div class="d-none d-md-block">Kenetralan</div>
                                                    </a></li>
                                                <li class="nav-item"><a class="nav-link" href="#empat"
                                                        data-toggle="tab">
                                                        <div class="d-md-none d-block">4</div>
                                                        <div class="d-none d-md-block">Kemandirian</div>
                                                    </a></li>
                                                <li class="nav-item"><a class="nav-link" href="#lima" data-toggle="tab">
                                                        <div class="d-md-none d-block">5</div>
                                                        <div class="d-none d-md-block">Kesukarelaan</div>
                                                    </a></li>
                                                <li class="nav-item"><a class="nav-link" href="#enam" data-toggle="tab">
                                                        <div class="d-md-none d-block">6</div>
                                                        <div class="d-none d-md-block">Kesatuan</div>
                                                    </a></li>
                                                <li class="nav-item"><a class="nav-link" href="#tujuh"
                                                        data-toggle="tab">
                                                        <div class="d-md-none d-block">7</div>
                                                        <div class="d-none d-md-block">Kesemestaan</div>
                                                    </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="satu">
                                            <div class="row">
                                                <div class="col-3">
                                                    <a href="#" data-toggle="modal" data-target="#lightbox"
                                                        class="prinsip"><img
                                                            src="index/images/7PRINSIP-01-Kemanusiaan-724x1024.jpg"
                                                            alt="Raised Image" class="img-raised rounded img-fluid"></a>
                                                </div>
                                                <div class="col-9">
                                                    <h4 class="card-title">1. Kemanusiaan</h4>
                                                    <p> Gerakan palang Merah dan Bulan Sabit Merah lnternasional lahir
                                                        dari keinginan untuk memberi bantuan tanpa diskriminasi kepada
                                                        mereka yang terluka di medan pertempuran, berusaha untuk
                                                        mencegah dan meringankan penderitaan manusia di mana pun dalam
                                                        lingkup kapasitas internasional dan nasional. </p>
                                                    <p> Tujuannya adalah untuk melindungi kehidupan dan kesehatan serta
                                                        untuk menjamin penghormatan terhadap manusia. Gerakan ini
                                                        mendorong saling pengertian, persahabatan, kerja sama, dan
                                                        perdamaian abadi di antara semua bangsa. </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="dua">
                                            <div class="row">
                                                <div class="col-3">
                                                    <a href="#" data-toggle="modal" data-target="#lightbox"
                                                        class="prinsip"><img
                                                            src="index/images/7PRINSIP-Kesamaan-724x1024.jpg"
                                                            alt="Raised Image" class="img-raised rounded img-fluid"></a>
                                                </div>
                                                <div class="col-9">
                                                    <h4 class="card-title">2. Kesamaan</h4>
                                                    <p> Tidak ada diskriminasi untuk bangsa, ras, agama, kelas atau
                                                        pendapat potitik. Gerakan ini berusaha meringankan penderitaan
                                                        individu, yang hanya dipandu oleh kebutuhan mereka, dan
                                                        memberikan prioritas kepada kebutuhan yang paling mendesak.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tiga">
                                            <div class="row">
                                                <div class="col-3">
                                                    <a href="#" data-toggle="modal" data-target="#lightbox"
                                                        class="prinsip"><img
                                                            src="index/images/7PRINSIP-Kenetralan-724x1024.jpg"
                                                            alt="Raised Image" class="img-raised rounded img-fluid"></a>
                                                </div>
                                                <div class="col-9">
                                                    <h4 class="card-title">3. Kenetralan</h4>
                                                    <p>Untuk memperoleh kepercayaan dari semua pihak, Gerakan ini tidak
                                                        memihak dalam permusuhan atau terlibat dalam kontroversi yang
                                                        bersifat politik, rasial, keagamaan atau ideologi.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="empat">
                                            <div class="row">
                                                <div class="col-3">
                                                    <a href="#" data-toggle="modal" data-target="#lightbox"
                                                        class="prinsip"><img
                                                            src="index/images/7PRINSIP-Kemandirian-724x1024.jpg"
                                                            alt="Raised Image" class="img-raised rounded img-fluid"></a>
                                                </div>
                                                <div class="col-9">
                                                    <h4 class="card-title">4. Kemandirian</h4>
                                                    <p>Gerakan ini bersifat mandiri/independen. Perhimpunan Nasional,
                                                        walaupun merupakan perpanjangan tangan pemerintah dan tunduk
                                                        pada hukum negara masing-masing, harus selalu menjaga otonomi
                                                        yang dimiliki sehingga mereka dapat bertindak sesuai dengan
                                                        prinsip- prinsip Gerakan setiap saat.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="lima">
                                            <div class="row">
                                                <div class="col-3">
                                                    <a href="#" data-toggle="modal" data-target="#lightbox"
                                                        class="prinsip"><img
                                                            src="index/images/7PRINSIP-Kesukarelaan-724x1024.jpg"
                                                            alt="Raised Image" class="img-raised rounded img-fluid"></a>
                                                </div>
                                                <div class="col-9">
                                                    <h4 class="card-title">5. Kesukarelaan</h4>
                                                    <p>Gerakan ini adalah gerakan bantuan sukarela, yang tidak didorong
                                                        oleh keinginan untuk mendapatkan keuntungan dalam bentuk apa
                                                        pun.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="enam">
                                            <div class="row">
                                                <div class="col-3">
                                                    <a href="#" data-toggle="modal" data-target="#lightbox"
                                                        class="prinsip"><img
                                                            src="index/images/7PRINSIP-Kesamaan-724x1024.jpg"
                                                            alt="Raised Image" class="img-raised rounded img-fluid"></a>
                                                </div>
                                                <div class="col-9">
                                                    <h4 class="card-title">6. Kesatuan</h4>
                                                    <p>Hanya ada satu Palang Merah atau Bulan Sabit Merah di dalam satu
                                                        negara. Lembaga ini harus terbuka untuk semua. Lembaga ini harus
                                                        menyelenggarakan pelayanan kemanusiaan di seluruh wilayahnya.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tujuh">
                                            <div class="row">
                                                <div class="col-3">
                                                    <a href="#" data-toggle="modal" data-target="#lightbox"
                                                        class="prinsip"><img
                                                            src="index/images/7PRINSIP-Kesemestaan-724x1024.jpg"
                                                            alt="Raised Image" class="img-raised rounded img-fluid"></a>
                                                </div>
                                                <div class="col-9">
                                                    <h4 class="card-title">7. Kesemestaan</h4>
                                                    <p>Gerakan palang Merah dan Bulan Sabit Merah lnternasional, yang
                                                        menjunjung status yang sama serta tugas dan tanggung jawab yang
                                                        sama bagi seluruh perhimpunannya, saling membantu satu sama
                                                        lain, di seluruh dunia.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Visi & Misi -->
        <div class="section section-tabs">
            <div class="container">
                <h2>VISI & MISI PMI</h2>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="card card-signup" data-aos="zoom-in-up">
                            <div class="modal-header">
                                <div class="card-header card-header-primary text-center">
                                    <h4 class="card-title">VISI</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3>TERWUJUDNYA PMI YANG PROFESIONAL DAN BERINTEGRITAS SERTA BERGERAK BERSAMA MASYARAKAT
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="card card-signup" data-aos="zoom-in-up">
                            <div class="modal-header">
                                <div class="card-header card-header-primary text-center">
                                    <h4 class="card-title">MISI</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-left">
                                    <ol>
                                        <li>Memelihara reputasi organisasi PMI di tingkat nasional dan internasional
                                        </li>
                                        <li>Menjadi organisasi kemanusiaan terdepan yang memberikan layanan berkualitas
                                            kepada masyarakat sesuai dengan Prinsip-prinsip Dasar Gerakan Internasional
                                            Palang Merah dan Bulan Sabit Merah</li>
                                        <li>Meningkatkan integritas dan kemandirian organisasi melalui kerjasama
                                            strategis yang berkesinambungan dengan pemerintah, swasta, mitra gerakan,
                                            masyarakat, dan pemangku kepentingan lainnya di semua tingkatan PMI dengan
                                            mengutamakan keberpihakan kepada masyarakat yang memerlukan bantuan</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="space-100"></div>
    <div class="main main-raised">
        <div class="section text-center section-">
            <div class="container" data-aos="zoom-in-down">
                <h2>Tujuan Strategi Pelayanan Darah</h2>
                <div class="row">
                    <div class="col-12">
                        <h3>Meningkatkan ketersediaan darah yang aman, mudah dijangkau, berkualitas dan berkesinambungan
                            di seluruh Indonesia.</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="section text-center section-tabs">
            <div class="container">
                <h2>Indikator Nasional Mutu UTD</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="d-none d-sm-block" data-aos="zoom-out-down">
                            <a class="btn btn-info" href="#mdlimn" data-id="1" data-toggle="modal"
                                data-target="#mdlimn"><i class="fa fa-1"></i>&nbsp;Kepatuhan Kebersihan Tangan</a>
                            <a class="btn btn-info" href="#mdlimn" data-id="2" data-toggle="modal"
                                data-target="#mdlimn"><i class="fa fa-2"></i>&nbsp;Kepatuhan Penggunaan Alat Pelindung
                                Diri</a>
                            <a class="btn btn-info" href="#mdlimn" data-id="3" data-toggle="modal"
                                data-target="#mdlimn"><i class="fa fa-3"></i>&nbsp;Pemenuhan Kebutuhan Darah Oleh
                                UTD</a>
                            <a class="btn btn-info" href="#mdlimn" data-id="4" data-toggle="modal"
                                data-target="#mdlimn"><i class="fa fa-4"></i>&nbsp;Donasi dari Pendonor Darah
                                Sukarela</a>
                            <a class="btn btn-info" href="#mdlimn" data-id="5" data-toggle="modal"
                                data-target="#mdlimn"><i class="fa fa-5"></i>&nbsp;Hasil Golongan Darah Pendonor Yang
                                Berbeda Dengan Uji KGD</a>
                            <a class="btn btn-info" href="#mdlimn" data-id="6" data-toggle="modal"
                                data-target="#mdlimn"><i class="fa fa-6"></i>&nbsp;Suhu Penyimpanan Produk Darah</a>
                            <a class="btn btn-info" href="#mdlimn" data-id="7" data-toggle="modal"
                                data-target="#mdlimn"><i class="fa fa-7"></i>&nbsp;Kepuasan Pelanggan</a>
                        </div>
                        <div class="d-block d-sm-none" data-aos="zoom-out-down">
                            <a href="#mdlimn" data-id="1" data-toggle="modal" data-target="#mdlimn"
                                class="badge badge-pill badge-info">Kepatuhan Kebersihan Tangan;</a>
                            <a href="#mdlimn" data-id="2" data-toggle="modal" data-target="#mdlimn"
                                class="badge badge-pill badge-info">Kepatuhan Penggunaan Alat Pelindung Diri;</a>
                            <a href="#mdlimn" data-id="3" data-toggle="modal" data-target="#mdlimn"
                                class="badge badge-pill badge-info">Pemenuhan Kebutuhan Darah Oleh UTD;</a>
                            <a href="#mdlimn" data-id="4" data-toggle="modal" data-target="#mdlimn"
                                class="badge badge-pill badge-info">Donasi dari Pendonor Darah Sukarela;</a>
                            <a href="#mdlimn" data-id="5" data-toggle="modal" data-target="#mdlimn"
                                class="badge badge-pill badge-info">Hasil Golongan Darah Pendonor Yang Berbeda Dengan
                                Uji KGD;</a>
                            <a href="#mdlimn" data-id="6" data-toggle="modal" data-target="#mdlimn"
                                class="badge badge-pill badge-info">Suhu Penyimpanan Produk Darah;</a>
                            <a href="#mdlimn" data-id="7" data-toggle="modal" data-target="#mdlimn"
                                class="badge badge-pill badge-info">Kepuasan Pelanggan;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="space-100"></div>
    <div class="main main-raised">
        <div class="section section-basics" id="carousel">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 mr-auto ml-auto">
                        <div class="card card-raised card-carousel">
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="3000">
                                <ol class="carousel-indicators">
                                    <?php
                                    $count = 1;
                                    foreach ($imagearray as $key => $image) {
                                        $active = ($count == 1) ? ' class="active"' : '';
                                        echo '<li data-target="#carouselExampleIndicators" data-slide-to="' . $count . '"' . $active . '></li>';
                                        $count++;
                                    }
                                    ?>
                                </ol>
                                <div class="carousel-inner">
                                    <?php
                                    $count = 1;
                                    foreach ($imagearray as $key => $image) {
                                        $active = ($count == 1) ? ' active' : '';
                                        echo '<div class="carousel-item text-center' . $active . '"><img class="img-fluid" src="poster/' . $image . '" alt="' . $image . '" style="height:500px;"></div>';
                                        $count++;
                                    }
                                    ?>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                    <i class="fa fa-arrow-left"></i>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                    <i class="fa fa-arrow-right"></i>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="space-100"></div>
    <div class="main main-raised" id="contact">
        <div class="section section-tabs">
            <div class="container-fluid text-center">
                <div class="row">
                    <div class="col-12" data-aos="flip-left">
                        <div class="card">
                            <div class="card-header card-header-info">
                                <h4 class="card-title text-center"><?php echo $nama_udd; ?></h4>
                            </div>
                            <div class="card-body">
                                <?php
                                $lat = $udd['lat'];
                                $lng = $udd['lng'];
                                ?>
                                <iframe style="border:0; width: 100%; min-height:300px; max-height: 600px;"
                                    src="https://maps.google.com/maps?q=<?php echo $lat . ',' . $lng; ?>&output=embed"></iframe>
                                <h5><?php echo $udd['alamat'] . ', Telp: ' . $udd['telp']; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlimn" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-login modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="card card-signup card-plain">
                    <div class="modal-header">
                        <div class="card-header card-header-info text-center">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                    class="fa fa-close"></i></button>
                            <h5 class="card-title">INM</h5>
                            <div class="social-line">Indikator Nasional Mutu</div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div id="dlgcontent" class="text-center textBold"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-login modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="card card-signup card-plain">
                    <div class="modal-header">
                        <div class="card-header card-header-primary text-center">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                    class="fa fa-close"></i></button>
                            <p>Masukan user dan password</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form class="formlogin" id="formlogin" name="formlogin" method="POST" action="">
                            <div class="card-body">
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="fa fa-user"></i></span></div>
                                        <input type="text" class="form-control" name="username" id="username"
                                            placeholder="username" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <div class="input-group" style="margin-top: 0px;">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="fa fa-lock"></i></span></div>
                                        <input type="password" class="form-control" name="password" id="password"
                                            placeholder="Password" autocomplete="off" required><i class="fa fa-eye"
                                            id="togglePassword"></i>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span
                                                class="input-group-text bg-primary text-white"
                                                id="kode">*</span>&nbsp;&nbsp;</div>
                                        <input type="text" class="form-control" name="kodeval" id="kodeval"
                                            placeholder="masukkan kode" autocomplete="off" required>
                                        <input type="hidden" name="kodeval1" id="kodeval1">
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <input type="submit" name="submit" id="login" value="Login" class="btn btn-primary btn-round">
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="lightbox" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="" alt="Raised Image" class="img-raised rounded img-fluid img-modal">
                </div>
            </div>
        </div>
    </div>
    <footer class="footer" data-background-color="black">
        <div class="container">
            <nav class="float-left">
                <ul>
                    <li><a href="https://pmi.or.id/" class="text-danger"><?php echo $nama_udd; ?></a></li>
                </ul>
            </nav>
            <div class="copyright float-right"><?php include 'version.php'; ?>&copy;2024<a
                    class="btn btn-fab btn-round btn-danger" href="javascript:void(0)" onclick="scrollToSimdondar()"><i
                        class="fa fa-home"></i></a></div>
        </div>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="assets/js/core/popper.min.js" type="text/javascript"></script>
    <script src="assets/js/core/bootstrap-material-design.min.js" type="text/javascript"></script>
    <script src="assets/js/plugins/moment.min.js"></script>
    <script src="assets/js/material-kit.js?v=2.0.7" type="text/javascript"></script>
    <script src="index/simdondar.js" type="text/javascript"></script>
    <script src="index/aos.js"></script>
    <script src="index/Chart.bundle.js"></script>
    <script src="index/sweetalert2.11.js"></script>
    <script src="index/DataTables/datatables.min.js"></script>
    <script>
    AOS.init();
    $(document).ready(function() {
        if ($('#birthdayModal').length) {
            $('#birthdayModal').modal('show');
        }

        // Muat captcha pertama kali saat halaman siap
        fillkode();

        // Muat ulang captcha setiap kali modal login dibuka
        $('#loginModal').on('show.bs.modal', function() {
            fillkode();
        });

        $.ajax({
            async: true,
            url: "index/simdondar.php?m=<?php echo md5('jadwalmu2'); ?>",
            dataType: "json",
            beforeSend: function() {
                $('tbody').html(
                    '<tr><td colspan="7" style="text-align:center">Memuat data...</td></tr>');
            },
            success: function(result) {
                var table = $('#datatables').DataTable({
                    language: {
                        'paginate': {
                            'previous': '<i class="material-icons">chevron_left</i>',
                            'next': '<i class="material-icons">chevron_right</i>'
                        },
                        "lengthMenu": "Tampilkan _MENU_ per halaman",
                        "info": "Menampilkan _START_ / _END_ dari _TOTAL_ data",
                        "infoEmpty": "Menampilkan 0 / 0 dari 0 data",
                        "search": "Cari:",
                        "zeroRecords": "Data tidak ditemukan"
                    },
                    pageLength: 5,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
                    ],
                    initComplete: function() {
                        $('.dataTables_paginate').addClass('pull-right');
                        $('.dataTables_filter input').addClass('form-control').attr(
                            'placeholder', 'Cari...');
                        $('.dataTables_length select').addClass('form-control');
                    },
                    data: result,
                    columns: [{
                            data: 'no',
                            className: 'text-center'
                        },
                        {
                            data: 'hari',
                            className: 'text-center'
                        },
                        {
                            data: 'tanggal',
                            className: 'text-center'
                        },
                        {
                            data: 'jam',
                            className: 'text-center'
                        },
                        {
                            data: 'jumlah',
                            className: 'text-center'
                        },
                        {
                            data: null,
                            className: 'text-left',
                            render: function(data, type, row) {
                                var tempat = '<span class="text-primary">' + row
                                    .nama + '</span> <small>' + row.tempat +
                                    '</small>';
                                return tempat;
                            }
                        },
                        {
                            data: 'status',
                            className: 'text-center',
                            render: function(data, type, row) {
                                if (data === 'Terjadwal') {
                                    return '<span class="card-header-warning" style="padding: 5px 10px; border-radius: 4px; display: inline-block;">' +
                                        data + '</span>';
                                } else {
                                    return data;
                                }
                            }
                        }
                    ],
                    responsive: true,
                    destroy: true
                });
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });

        $('body').bind('cut copy', function(e) {
            e.preventDefault();
        });

        // fillkode();

        $.ajax({
            async: true,
            url: "index/simdondar.php?m=<?php echo md5('stokdarah'); ?>",
            success: function(result) {
                $("#infostok").show();
                $("#infostok").html(result);
            }
        });

        $(function() {
            var navMain = $(".navbar-collapse");
            navMain.on("click", "a", null, function() {
                navMain.collapse('hide');
            });
        });

        $('.prinsip').click(function(e) {
            e.preventDefault();
            var src = $(this).find('img').attr('src');
            $('.img-modal').attr("src", src);
        });

        var ctx = document.getElementById("chartdonasi");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                        label: 'Donasi Berhasil',
                        data: [<?php echo implode(',', $donasi_berhasil); ?>],
                        backgroundColor: 'rgba(76, 175, 80, 0.6)',
                        borderColor: '#4CAF50',
                        borderWidth: 1,
                        hoverBackgroundColor: '#388E3C'
                    },
                    {
                        label: 'Donasi Gagal',
                        data: [<?php echo implode(',', $donasi_gagal); ?>],
                        backgroundColor: 'rgba(255, 87, 34, 0.6)',
                        borderColor: '#FF5722',
                        borderWidth: 1,
                        hoverBackgroundColor: '#D32F2F'
                    }
                ]
            },
            options: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: 'Donasi per bulan',
                    fontSize: 22
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Jumlah Donasi'
                        },
                        stacked: true
                    }],
                    xAxes: [{
                        stacked: true
                    }]
                }
            }
        });

        var ctx1 = document.getElementById('chartJumlah').getContext('2d');
        var chartJumlah = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                        label: 'Target Donor',
                        data: <?php echo json_encode($jumlah); ?>,
                        backgroundColor: 'blue'
                    },
                    {
                        label: 'Sukses',
                        data: <?php echo json_encode($sukses); ?>,
                        backgroundColor: 'green'
                    },
                    {
                        label: 'Batal',
                        data: <?php echo json_encode($batal); ?>,
                        backgroundColor: 'orange'
                    },
                    {
                        label: 'Gagal',
                        data: <?php echo json_encode($gagal); ?>,
                        backgroundColor: 'red'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        var ctx = document.getElementById("chartPersentase").getContext("2d");
        var labels = <?php echo $labels_json; ?>;
        var keberhasilan = <?php echo $keberhasilan_json; ?>;

        function movingAverage(data, period) {
            let trendline = [];
            for (let i = 0; i < data.length; i++) {
                let start = Math.max(0, i - period + 1);
                let subset = data.slice(start, i + 1);
                let sum = subset.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                trendline.push((sum / subset.length).toFixed(2));
            }
            return trendline;
        }

        var trendData = movingAverage(keberhasilan, 3);
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: "% Donasi dari Target",
                        data: keberhasilan,
                        borderColor: "blue",
                        backgroundColor: "rgba(0, 0, 255, 0.1)",
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: "blue",
                        fill: true
                    },
                    {
                        label: "Trendline",
                        data: trendData,
                        borderColor: "red",
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointRadius: 0,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: "Persentase Keberhasilan (%)"
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(2) + "%";
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.raw.toFixed(2) + "%";
                            }
                        }
                    }
                }
            }
        });

        var ctx = document.getElementById("chartJmlMU").getContext("2d");
        var labels = <?php echo json_encode($labels); ?>;
        var dataKegiatan = <?php echo json_encode($jmlkegiatan); ?>;
        var dataRataRata = <?php echo json_encode($jmlkegiatan_hari); ?>;

        var chartJmlMU = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: "Jumlah Kegiatan",
                        data: dataKegiatan,
                        backgroundColor: "rgba(75, 192, 192, 0.6)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    },
                    {
                        label: "Rata-rata Kegiatan per Hari",
                        data: dataRataRata,
                        backgroundColor: "rgba(255, 99, 132, 0.6)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        var load = document.getElementById("loading");
        window.addEventListener('load', function() {
            load.style.display = "none";
        });
    });

    $("#formlogin").submit(function(event) {
        $('#loginModal').modal('hide');
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "index/simdondar.php?m=<?php echo md5('login'); ?>",
            data: $('form.formlogin').serialize(),
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(respon) {
                var exrespon = respon.split('*');
                $('#loading').hide();
                switch (exrespon[0]) {
                    case '0':
                        window.location.replace(exrespon[2]);
                        break;
                    case '1':
                        Swal.fire({
                            title: "Login",
                            text: exrespon[1],
                            icon: "error",
                            timer: 5000,
                            timerProgressBar: true,
                            dangerMode: true,
                            showConfirmButton: true,
                        });
                        break;
                    case '2':
                        Swal.fire({
                            title: "Login",
                            icon: "info",
                            text: exrespon[1],
                            showDenyButton: true,
                            showCancelButton: false,
                            confirmButtonText: "Ganti Password",
                            denyButtonText: `Tunda`,
                            showClass: {
                                popup: 'animated zoomInLeft faster'
                            },
                            hideClass: {
                                popup: 'animated zoomOutUp faster'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.replace(exrespon[2]);
                            } else if (result.isDenied) {
                                Swal.fire("<?php include 'version.php'; ?>",
                                    "Anda tidak bisa login, sebelum mengganti password!!",
                                    "info");
                            }
                        });
                        break;
                }
            }
        });
        $('#formlogin')[0].reset();
        fillkode();
        return false;
    });

    function fillkode() {
        $.ajax({
            url: "index/simdondar.php?m=<?php echo md5('acakkode'); ?>",
            success: function(respon) {
                $('#kode').html(respon);
                $('#kodeval1').val(respon);
            }
        });
    }
    </script>
</body>

</html>