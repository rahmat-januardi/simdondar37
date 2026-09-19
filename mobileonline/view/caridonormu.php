<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
include '../adm/config.php';
$utd = mysqli_fetch_array(mysqli_query($con, "SELECT * from utd where `aktif`=1"));

$id = $_SESSION['instansi'];
$unit = $_SESSION['unit'];
$user = $_SESSION['user'];

// ================= PENCARIAN PENDONOR NASIONAL (DISAMAKAN DENGAN cari_pendonor_utdp.php) =================
// Menggantikan pemanggilan curl_init() manual yang sebelumnya langsung di tengah halaman tanpa
// timeout (CURLOPT_TIMEOUT => 0 = bisa menggantung tanpa batas) dan tanpa validasi respons.
// Sekarang ada timeout wajar + retry khusus timeout, plus pesan error yang jelas ke user
// ketika server nasional tidak bisa dihubungi / mengirim format yang tidak terduga.
function cari_pendonor_nasional($postFields, &$error)
{
    $error = '';

    if (!function_exists('curl_init')) {
        $error = 'Ekstensi cURL tidak tersedia pada server.';
        return array();
    }

    $requestFields = $postFields;
    if (isset($requestFields['kode'])) {
        $requestFields['kode'] = trim($requestFields['kode']);
    }
    if (isset($requestFields['udd'])) {
        $requestFields['udd'] = trim($requestFields['udd']);
    }

    $response = false;
    $curlError = '';
    $curlErrno = 0;
    $httpCode = 0;

    // Endpoint nasional sesekali timeout. Ulang satu kali khusus error timeout.
    for ($attempt = 1; $attempt <= 2; $attempt++) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://dbdonor.pmi.or.id/pmi/api/simdondar/caripendonor.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => ($attempt === 1 ? 15 : 20),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $requestFields,
        ));

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $curlErrno = curl_errno($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response !== false || $curlErrno !== 28) {
            break;
        }
    }

    if ($response === false) {
        $error = 'Tidak dapat menghubungi server nasional' . ($curlError !== '' ? ': ' . $curlError : '.');
        return array();
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        $error = 'Server nasional mengembalikan HTTP ' . $httpCode . '.';
        return array();
    }

    $response = trim($response, "\xEF\xBB\xBF\x00\x09\x0A\x0D\x20");

    // Respons lama endpoint ketika data tidak ditemukan.
    $plainResponse = trim(html_entity_decode(strip_tags($response), ENT_QUOTES, 'UTF-8'));
    if ($response === '' || stripos($plainResponse, 'Error bro') === 0) {
        return array();
    }

    $decoded = json_decode($response, true);

    // Beberapa instalasi endpoint menuliskan warning sebelum JSON.
    if (!is_array($decoded)) {
        $jsonStart = strpos($response, '{');
        $jsonEnd = strrpos($response, '}');
        if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
            $jsonOnly = substr($response, $jsonStart, ($jsonEnd - $jsonStart) + 1);
            $decoded = json_decode($jsonOnly, true);
        }
    }

    // Respons kosong/tidak ditemukan dari beberapa versi API.
    if (($decoded === null && strtolower($response) === 'null') || $decoded === false || $decoded === array()) {
        return array();
    }

    if (is_array($decoded) && !empty($decoded['error']) && !isset($decoded['data'])) {
        return array();
    }

    // Ada versi API yang mengirim daftar donor langsung tanpa pembungkus "data".
    if (is_array($decoded) && isset($decoded[0]) && is_array($decoded[0])) {
        return $decoded;
    }

    // Ada versi API yang mengirim satu donor langsung sebagai object.
    if (is_array($decoded) && isset($decoded['pkode'])) {
        return array($decoded);
    }

    if (!is_array($decoded) || !array_key_exists('data', $decoded)) {
        $jenisRespons = (stripos($response, '<html') !== false || stripos($response, '<!doctype') !== false)
            ? 'halaman HTML'
            : 'teks non-JSON';
        $error = 'Server nasional mengirim ' . $jenisRespons . ' (' . strlen($response) . ' byte), bukan data pendonor.';
        return array();
    }

    if ($decoded['data'] === null || $decoded['data'] === '' || $decoded['data'] === false) {
        return array();
    }

    // Sebagian endpoint membungkus JSON data sebagai string.
    if (is_string($decoded['data'])) {
        $dataDalamString = json_decode($decoded['data'], true);
        if (is_array($dataDalamString)) {
            $decoded['data'] = $dataDalamString;
        }
    }

    if (!is_array($decoded['data'])) {
        $error = 'Server nasional mengirim field data dengan tipe yang tidak dikenali.';
        return array();
    }

    // Beberapa versi endpoint mengirim satu donor sebagai object, bukan list.
    if (isset($decoded['data']['pkode'])) {
        return array($decoded['data']);
    }

    return $decoded['data'];
}

if ($unit == "" || $id === "" || $user == "") {
    header("location: ?page=index");
} else {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SIMDONDAR</title>

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
        <!-- JQVMap -->
        <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/adminlte.min.css">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
        <!-- summernote -->
        <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

        <link rel="stylesheet" href="code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style type="text/css" title="currentStyle">
            @import "./../css/dt_page.css";
            @import "./../css/dt_table.css";
            @import "./../css/dt_table_jui.css";
        </style>
        <link type="text/css" href="../../css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
        <link type="text/css" href="./../css/TableTools_JUI.css" rel="stylesheet" />
        <script type="text/javascript" language="javascript" src="./../js/jquery-1.5.2.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="./../js/jquery-ui-1.8.9.custom.min.js"></script>
        <script type="text/javascript" language="javascript" src="./../js/jquery.dataTables.js"></script>



        <script>
            jQuery(document).ready(function() {
                $("#searchLoaderOverlay").hide();

                $('#instansi').autocomplete({
                    source: '../../modul/suggest_udd.php',
                    minLength: 2
                });

                $("#nama").autocomplete({
                    source: '../../modul/suggest_pendonor.php',
                    minLength: 3,
                    select: function(event, ui) {
                        const tgl = ui.item.tgl;
                        const bln = ui.item.bln;
                        const thn = ui.item.thn;
                        const nama = ui.item.value;
                        if (tgl != '' && bln != '' && thn != '') {
                            $("#thn").val(thn);
                            $("#bln").val(bln);
                            $("#tgl").val(tgl);
                        }
                        $("#nama").val(nama);
                        $("#cariPendonor").click();
                    }
                });
            });

            function disabletext(val) {
                if (val == '0') {
                    document.getElementById('comments').hidden = true;
                } else {
                    document.getElementById('comments').hidden = false;
                }
            }
        </script>

    </head>

    <style>
        .body {
            font-size: 12px;
        }

        .padding {

            background-image: url('dist/img/white.jpg');
            background-size: cover;
        }

        .box {

            height: 25px;
            padding: 20px;
        }

        .box2 {

            height: 25px;
            padding: 20px;
        }

        .box3 {

            height: 100px;

        }

        .copyright {
            bottom: 0;
            width: 100%;
            position: fixed;
            height: 40px;
            line-height: 50px;
            background: RED;
            color: #fff;
            padding-left: 10px;
        }

        .input-tanggal {
            padding: 10px;
            font-size: 14pt;
        }

        #searchLoaderOverlay {
            display: none;
            position: fixed;
            z-index: 99999;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.58);
        }

        #searchLoaderOverlay .search-loader-box {
            min-width: 280px;
            padding: 28px 32px;
            border-radius: 10px;
            background: #fff;
            color: #333;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
        }

        #searchLoaderOverlay .search-loader-spinner {
            width: 46px;
            height: 46px;
            margin: 0 auto 15px;
            border: 5px solid #eee;
            border-top-color: #d9534f;
            border-radius: 50%;
            animation: search-loader-spin 0.8s linear infinite;
        }

        #searchLoaderOverlay .search-loader-title {
            margin-bottom: 5px;
            font-size: 17px;
            font-weight: bold;
        }

        #searchLoaderOverlay .search-loader-info {
            margin: 0;
            color: #777;
            font-size: 13px;
        }

        @keyframes search-loader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Dipakai sebagai pengganti atribut disabled=true pada tombol submit.
   Kalau tombol submit di-set disabled di dalam fungsi yang dipanggil oleh
   onsubmit, browser akan mengecualikan name/value tombol tersebut
   (name="lokal") dari data yang dikirim, sehingga $_POST['lokal'] di PHP
   tidak ke-set dan pencarian gagal jalan. Class ini hanya membuat tombol
   terlihat & berperilaku nonaktif tanpa mengubah atribut disabled aslinya.
*/
        .btn-processing {
            pointer-events: none;
            opacity: 0.65;
            cursor: not-allowed;
        }
    </style>

    <body class="padding">



        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/logo.png" alt="AdminLTELogo" height="60" width="60">
        </div>
        <div id="searchLoaderOverlay" aria-live="polite" aria-busy="true">
            <div class="search-loader-box">
                <div class="search-loader-spinner"></div>
                <div class="search-loader-title">Mencari data pendonor...</div>
                <p class="search-loader-info">Memeriksa data lokal dan nasional</p>
            </div>
        </div>
        <p>
        <div class="card-header">
            <h4 class="text-center"
                style="font-size:24px; font-weight:bold;color:#ff0000;text-shadow: 1px 1px 1px #000000; font-family:Helvetica, Arial, san-serif;">
                CARI DATA PENDONOR</h4>
            <a href="?page=donorbaru"><button name="baru" class="btn btn-warning"><i
                        class="nav-icon ion ion-android-person-add"></i> Donor Baru</button></a>
            <button class="btn btn-sm btn-info btn-round shadow" data-target="#MdlPanduanNasional" data-toggle="modal"
                type="button"><i class="fa fa-info-circle" aria-hidden="true"></i> Panduan Cari
                Nasional</button>
            <a href="?page=dash"><button name="baru" class="btn btn-info float-right"><i
                        class="nav-icon ion ion-android-arrow-back"></i> Kembali</button></a>
        </div>

        <div class="col-12 col-sm-12">
            <div class="card-body">
                <!--content-->




                <form method="POST" onkeydown="return event.key != 'Enter';" onsubmit="return validasiregistrasi()">
                    <div class="row" align="center">
                        <div class="col-lg-6">
                            <!--row1--->
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Kode Pendonor</span>
                                    </div>
                                    <input type="text" name="kode" class="form-control" id="iddonor"
                                        placeholder="ID KARTU DONOR" onchange='disabletext(this.value);'>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">No. KTP/Identitas</span>
                                    </div>
                                    <input type="text" class="form-control" id="NoKTP" name="NoKTP"
                                        placeholder="No. KTP/Identitas">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Nama Pendonor</span>
                                    </div>
                                    <input type="text" class="form-control" name="nama" id="nama"
                                        placeholder="Nama Pendonor" minlength="3">
                                </div>
                            </div>

                            <!-- edit -->
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Tanggal Lahir</span>
                                    </div>
                                    <input type="text" class="form-control" id="tgl" name="tgl" placeholder="dd" size='2'
                                        maxlength="2">
                                    <input type="text" class="form-control" id="bln" name="bln" placeholder="mm" size='2'
                                        maxlength="2">
                                    <input type="text" class="form-control" id="thn" name="thn" placeholder="yyyy" size='4'
                                        maxlength="4">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Golongan Darah</span>
                                    </div>
                                    <select class="form-control" name="goldarah">
                                        <option value="">SEMUA</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="O">O</option>
                                        <option value="AB">AB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rhesus Darah</span>
                                    </div>
                                    <select class="form-control" name="rhesus">
                                        <option value="">SEMUA</option>
                                        <option value="+">+</option>
                                        <option value="-">-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">No. Handphone</span>
                                    </div>
                                    <input type="text" class="form-control" name="telp" placeholder="Nomor Handphone">
                                </div>
                            </div>

                            <!-- sampai sini  -->
                            <!--row1--->
                        </div>
                        <div class="col-lg-6">
                            <!--row2--->
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Asal UDD PMI</span>
                                    </div>
                                    <input type="text" class="form-control" name="udd" id="instansi"
                                        value="<?php echo $utd['id']; ?>" placeholder="Kab / Kota" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Tempat Lahir</span>
                                    </div>
                                    <input type="text" class="form-control" name="tmplahir" placeholder="Tempat Lahir">
                                </div>
                            </div>

                            <!-- edit dari ini -->
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Alamat</span>
                                    </div>
                                    <input type="text" class="form-control" name="alamat" placeholder="Alamat">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Kelurahan</span>
                                    </div>
                                    <input type="text" class="form-control" name="kelurahan" placeholder="Kelurahan Alamat">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Kecamatan</span>
                                    </div>
                                    <input type="text" class="form-control" name="kecamatan" placeholder="Kecamatan Alamat">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Wilayah</span>
                                    </div>
                                    <input type="text" class="form-control" name="wilayah" placeholder="Kab / Kota">
                                </div>
                            </div>
                            <!-- sampai sini  -->

                            <!--row2--->
                        </div>
                        <div class="form-group">
                            <!--button name="nasional" type="submit" class="btn btn-danger"><i class="fa fa-search"></i>  Cari Data Nasional</button-->

                            <button id="cariPendonor" name="lokal" type="submit" class="btn btn-success"><i
                                    class="fa fa-search"></i> Cari Data </button>
                        </div>
                    </div>
                </form>
                <!--p class="box"-->

                <?php
                //Query Dinamis
                $nama   = $_POST['nama'];
                $tgl    = $_POST['tgl'];
                $bln    = $_POST['bln'];
                $thn    = $_POST['thn'];
                $tgllhr = $thn . "-" . $bln . "-" . $tgl;
                if ($_POST['kode'] != '') {
                    $srckode  = $_POST['kode'];
                    $qkode       = " AND Kode = '$srckode' ";
                } else {
                    $qkode    = "";
                }

                if ($_POST['NoKTP'] != '') {
                    $srcktp  = $_POST['NoKTP'];
                    $qktp       = " AND NoKTP = '$srcktp' ";
                } else {
                    $qktp    = "";
                }

                if ($_POST['alamat'] != '') {
                    $srcalamat  = $_POST['alamat'];
                    $qalamat       = " AND Alamat = '$srcalamat' ";
                } else {
                    $qalamat    = "";
                }

                if ($_POST['kelurahan'] != '') {
                    $srckelurahan  = $_POST['kelurahan'];
                    $qkelurahan       = " AND kelurahan = '$srckelurahan' ";
                } else {
                    $qkelurahan    = "";
                }

                if ($_POST['kecamatan'] != '') {
                    $srckecamatan  = $_POST['kecamatan'];
                    $qkecamatan       = " AND kecamatan = '$srckecamatan' ";
                } else {
                    $qkecamatan    = "";
                }

                if ($_POST['wilayah'] != '') {
                    $srcwilayah  = $_POST['wilayah'];
                    $qwilayah       = " AND wilayah = '$srcwilayah' ";
                } else {
                    $qwilayah    = "";
                }

                if ($_POST['udd'] != '') {
                    $srcudd  = $_POST['udd'];
                    $qudd       = " AND Kode = '$srcudd' ";
                } else {
                    $qudd    = "";
                }

                if ($_POST['tmplahir'] != '') {
                    $srctmplahir  = $_POST['tmplahir'];
                    $qtmplahir       = " AND TempatLhr = '$srctmplahir' ";
                } else {
                    $qtmplahir    = "";
                }

                if (($tgl != '') && ($bln != '') && ($thn != '')) {
                    $srctgl  = $tgllhr;
                    $qtgl       = " AND TglLhr = '$srctgl' ";
                } else {
                    $qtgl    = "";
                }

                if ($_POST['goldarah'] != '') {
                    $srcgoldarah  = $_POST['goldarah'];
                    $qgoldarah       = " AND GolDarah = '$srcgoldarah' ";
                } else {
                    $qgoldarah    = "";
                }

                if ($_POST['rhesus'] != '') {
                    $srcrhesus  = $_POST['rhesus'];
                    $qrhesus       = " AND Rhesus = '$srcrhesus' ";
                } else {
                    $qrhesus    = "";
                }

                if ($_POST['telp'] != '') {
                    $srctelp  = $_POST['telp'];
                    $qtelp       = " AND telp2 = '$srctelp' ";
                } else {
                    $qtelp    = "";
                }


                //Tombol Lokal
                if (isset($_POST['lokal'])) {

                    //echo "Nama hostname : ".$td0." & data lokal dipilih<br>";
                    $jd = "select * from pendonor where Nama like '%$nama%' $qkode $qktp $qalamat $qkelurahan $qkecamatan $qwilayah $qtmplahir $qtgl $qgoldarah $qrhesus $qtelp order by Nama asc";
                    //echo $jd;
                    $qjd = mysqli_query($con, $jd);
                    $num = mysqli_num_rows($qjd);

                    if ($num > 0) {
                        $today = date('Y-m-d');
                ?><table cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                            <tr style="background-color:#FF6346;  color:#FFFFFF; font-family:Verdana;">
                                <td align="center">Kode Pendonor</td>
                                <td align="center">Nama</td>
                                <td align="center">Jenis Kelamin</td>
                                <td align="center">Alamat</td>
                                <td align="center">Gol Darah</td>
                                <td align="center">Tempat<br>Tgl. Lahir</td>
                                <td align="center">Telp/Hp</td>
                                <td align="center">Jumlah Donor</td>
                                <td align="center">Tanggal Kembali<br>Donor</td>
                                <td align="center">IMLTD</td>
                                <td align="center">Cetak<br>Kartu</td>
                            </tr>
                            <?php

                            while ($data = mysqli_fetch_array($qjd)) {
                                //backcolor cekal
                                if ($data['Cekal'] == '1' || $data['Cekal'] == '2') {
                                    $style = "style=background-color:#FF6346; font-size:12px;";
                                } else {
                                    $style = "style=background-color:#FFFFFF; font-size:12px;";
                                }

                                //jenis kelamin
                                if ($data['Jk'] == '0') {
                                    $jeniskelamin = "Laki-Laki";
                                }
                                if ($data['Jk'] == '1') {
                                    $jeniskelamin = "Perempuan";
                                }

                                //Cekal
                                if ($data['Cekal'] == '1') {
                                    $imltd = "Konfirm ke Dokter";
                                } else if ($data['Cekal'] == '2') {
                                    $imltd = "Pernah Cek Ulang IMLTD";
                                } else {
                                    $imltd = "OK";
                                }

                            ?>
                                <tr <?php echo $style; ?> onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
                                    <td align="center">
                                        <!-- Jika Wanita <4 & PRIA >4-->
                                        <?php if ($data['Jk'] == "1") {
                                            $tahun = date('Y');
                                            $jumtransaksiperempuan = mysqli_query($dbi, "select * from htransaksi where KodePendonor='$kode' and year(tgl)='$tahun'");
                                            if (date('Y-m-d') >= $data['tglkembali'] and ($data['Cekal'] == '0') and (mysqli_num_rows($jumtransaksiperempuan) < '4')) { ?>
                                                <img src="../images/bloodbag.png" width=25
                                                    height=15 /><?php }
                                                        } else if (date('Y-m-d') >= $data['tglkembali'] and ($data['Cekal'] == '0')) { ?>
                                            <img src="../images/bloodbag.png" width=25 height=15 /> <?php } ?>
                                        <a href="idcard_barcode.php?idpendonor=<? echo $data['Kode'] ?>"><img
                                                src="../images/barcode.png" width=25 height=15 /></a>
                                        <a href="../idcard_full.php?idpendonor=<?php echo $data['Kode']; ?>" target="_blank">
                                            <img src="../images/idcard.png" width="25" height="15">
                                        </a>
                                        <a href="?page=edits&id=<?php echo $data['Kode'] ?>"><img src="../images/ubah.png" width=25
                                                height=15 /></a>
                                        <a href="?page=histori&q=<?php echo $data['Kode'] ?>" target="isiadmin"
                                            class="fisheyeItem"><?php echo $data['Kode']; ?></a>
                                    </td>
                                    <td align="center"><?php echo $data['Nama']; ?></td>
                                    <td align="center"><?php echo $jeniskelamin; ?></td>
                                    <td align="center">
                                        <?php echo $data['Alamat'] . " <br>" . $data['kelurahan'] . " " . $data['kecamatan'] . " " . $data['wilayah']; ?>
                                    </td>
                                    <td align="center"><?php echo $data['GolDarah'] . " (" . $data['Rhesus'] . ")"; ?></td>
                                    <td align="center"><?php echo $data['TempatLhr'] . ",<br>" . $data['TglLhr']; ?></td>
                                    <td align="center"><?php echo $data['telp2']; ?></td>
                                    <td align="center"><?php echo $data['jumDonor']; ?> kali</td>
                                    <td align="center"><?php echo $data['tglkembali']; ?></td>
                                    <td align="center"><?php echo $imltd; ?></td>
                                    <td align="center"><?php echo $data['cetak'] = $data['cetak'] . " kali</a> " ?></td>

                                </tr>
                            <?php }
                        } else {
                            //Cari Nasional
                            $apiError = '';
                            $dataNasional = cari_pendonor_nasional(array(
                                'kode' => isset($srckode) ? $srckode : '',
                                'udd' => $utd['id'],
                                'nama' => $nama,
                                'NoKTP' => isset($srcktp) ? $srcktp : '',
                                'alamat' => isset($srcalamat) ? $srcalamat : '',
                                'kelurahan' => isset($srckelurahan) ? $srckelurahan : '',
                                'kecamatan' => isset($srckecamatan) ? $srckecamatan : '',
                                'wilayah' => isset($srcwilayah) ? $srcwilayah : '',
                                'tmplahir' => isset($srctmplahir) ? $srctmplahir : '',
                                'tgllhr' => isset($srctgl) ? $srctgl : '',
                                'goldarah' => isset($srcgoldarah) ? $srcgoldarah : '',
                                'rhesus' => isset($srcrhesus) ? $srcrhesus : '',
                                'telp2' => isset($srctelp) ? $srctelp : '',
                            ), $apiError);
                            $tgl = date("Y/m/d");
                            $data = array('data' => $dataNasional); ?>
                            <table cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                <tr style="background-color:#FF6346;  color:#FFFFFF; font-family:Verdana;">
                                    <td align="center">Kode Pendonor</td>
                                    <td align="center">Nama</td>
                                    <td align="center">Jenis Kelamin</td>
                                    <td align="center">Alamat</td>
                                    <td align="center">Gol Darah</td>
                                    <td align="center">Tempat<br>Tgl. Lahir</td>
                                    <td align="center">Telp/Hp</td>
                                    <td align="center">Jumlah Donor</td>
                                    <td align="center">Tanggal Kembali<br>Donor</td>
                                    <td align="center">IMLTD</td>
                                    <td align="center">Foto</td>
                                </tr><?php

                                        $no = 0;
                                        for ($a = 0; $a < count($data['data']); $a++) {
                                            $no = $a + 1;
                                            $chkdata = strlen($data['data'][$a]['pkode']);
                                            if ($chkdata > 0) {
                                                if ($data['data'][$a]['pcekal'] == '1' || $data['data'][$a]['pcekal'] == '2') {
                                                    $style = "style=background-color:#FF6346; font-size:12px;";
                                                } else {
                                                    $style = "style=background-color:#FFFFFF; font-size:12px;";
                                                }

                                                //jenis kelamin
                                                if ($data['data'][$a]['pjk'] == '0') {
                                                    $jeniskelamin = "Laki-Laki";
                                                }
                                                if ($data['data'][$a]['pjk'] == '1') {
                                                    $jeniskelamin = "Perempuan";
                                                }

                                                //Cekal
                                                if ($data['data'][$a]['pcekal'] == '1') {
                                                    $imltd = "Konfirm ke Dokter";
                                                } else if ($data['data'][$a]['pcekal'] == '2') {
                                                    $imltd = "Pernah Cek Ulang IMLTD";
                                                } else {
                                                    $imltd = "OK";
                                                }
                                        ?>
                                        <tr <?php echo $style; ?> onMouseOver="this.className='highlight'"
                                            onMouseOut="this.className='normal'">
                                            <td align="center">
                                                <!-- Jika Wanita <4 & PRIA >4-->
                                                <?php if ($data['data'][$a]['pjk'] == "1") {
                                                    $tahun = date('Y');
                                                    $jumtransaksiperempuan = mysqli_query($dbi, "select * from htransaksi where KodePendonor='$kode' and year(tgl)='$tahun'");
                                                    if (date('Y-m-d') >= $data['data'][$a]['ptglkembali'] and ($data['data'][$a]['pcekal'] == '0') and (mysqli_num_rows($jumtransaksiperempuan) < '4')) { ?>
                                                        <img src="../images/bloodbag.png" width=25
                                                            height=15 /><?php }
                                                                } else if (date('Y-m-d') >= $data['data'][$a]['ptglkembali'] and ($data['data'][$a]['pcekal'] == '0')) { ?>
                                                    <img src="../images/bloodbag.png" width=25 height=15 /> <?php }
                                                                                                        echo  '<a href="?page=editn&id=' . htmlspecialchars(serialize($data['data'][$a])) . '"><img src="../images/ubah.png" width=25 height=15 /></a>';
                                                                                                            ?>
                                                <br>
                                                <a href="pmi<?php echo $_SESSION['leveluser'] ?>.php?module=history_luar&q=<?php echo $data['data'][$a]['pkode'] ?>"
                                                    target="isiadmin" class="fisheyeItem"><?php echo $data['data'][$a]['pkode']; ?></a>
                                            </td>
                                            <td align="center"><?php echo $data['data'][$a]['pnama']; ?></td>
                                            <td align="center"><?php echo $jeniskelamin; ?></td>
                                            <td align="center">
                                                <?php echo $data['data'][$a]['palamat'] . " <br>" . $data['data'][$a]['pkelurahan'] . " " . $data['data'][$a]['pkecamatan'] . " " . $data['data'][$a]['pwilayah']; ?>
                                            </td>
                                            <td align="center">
                                                <?php echo $data['data'][$a]['pgoldarah'] . " (" . $data['data'][$a]['prhesus'] . ")"; ?>
                                            </td>
                                            <td align="center">
                                                <?php echo $data['data'][$a]['ptempatlahir'] . ", <br>" . $data['data'][$a]['ptgllahir']; ?>
                                            </td>
                                            <td align="center"><?php echo $data['data'][$a]['ptelp2']; ?></td>
                                            <td align="center"><?php echo $data['data'][$a]['pjmldonor']; ?> kali</td>
                                            <td align="center"><?php echo $data['data'][$a]['ptglkembali']; ?></td>
                                            <td align="center"><?php echo $imltd; ?></td>
                                            <td align="center"><img class="img-hover-zoom--slowmo"
                                                    src="https://dbdonor.pmi.or.id/pmi/image/<?php echo $data['data'][$a]['userfoto']; ?>"
                                                    style="max-width: 40%; height: auto;"></td>



                                <?php }
                                        }
                                        if ($no == '0') {
                                            echo '<tr>';
                                            if ($apiError !== '') {
                                                echo '<td colspan="11" style="font-size:16px;color:#b00000;" class="text-center">Pencarian nasional gagal: ' . htmlspecialchars($apiError) . '</td>';
                                            } else {
                                                echo '<td colspan="11" style="font-size:20px;" class="text-center">Tidak ada data Pendonor Nasional</td>';
                                            }
                                            echo '</tr>';
                                        }
                                        echo '</tbody>
       </table>';






                                        //echo "<tr><td align='center' colspan=12>Tidak Ada Data Pendonor</td></tr>";
                                    } ?>
                            </table>
                        <?php }


                    ///==>TOMBOL NASIONAL
                    if (isset($_POST['nasional'])) {
                    } //ENDDDD



                        ?>



                        <!--content-->
            </div>
        </div>
        <p class="box3">
        <div class="copyright">
            <p align="center"><a href="https://pmi.or.id">
                    <font style="color:white">Copyright @ 2022 | PALANG MERAH INDONESIA
                </a>
        </div>

        <!-- Modal Panduan Cari Pendonor Nasional -->
        <div class="modal fade modal-fade-in-scale-up" id="MdlPanduanNasional" aria-hidden="true"
            aria-labelledby="MdlPanduanNasional" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-simple modal-center">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#ffc107;">
                        <h4 class="modal-title" style="color:#212529;"><i class="fa fa-info-circle"></i> Panduan
                            Pencarian Pendonor Nasional</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert"
                            style="border-left: 6px solid #ff9800; font-size:14px;">
                            <strong>Cara Mencari Pendonor Nasional:</strong>
                            <ol style="margin-top:10px; margin-bottom:0; padding-left:20px;">
                                <li>Tanya apakah sudah pernah donor pada PMI kota lain?</li>
                                <li>Jika belum, maka daftar seperti biasa.</li>
                                <li>Jika sudah, selanjutnya ketik lengkap nama pendonor.</li>
                                <li>Isi tanggal lahir dan tempat lahir.</li>
                                <li>Isi golongan darah.</li>
                            </ol>
                        </div>
                        <p style="margin-bottom:0; color:#6c757d; font-size:13px;">
                            Panduan ini membantu memastikan pencarian data nasional lebih akurat, baik untuk
                            pendonor lama maupun pendonor baru.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Mengerti</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Panduan Cari Pendonor Nasional END -->





        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
            $(function() {
                //Date picker

                $("#datepicker").datepicker({
                    dateFormat: "yy-mm-dd",
                });
            });
        </script>
        <script type="text/javascript">
            function validasiregistrasi() {

                if (document.getElementById('iddonor').value && document.getElementById('iddonor').value.length < 10) {
                    alert('Masukan Kode Pendonor minimal 10 Karakter !');
                    document.getElementById("iddonor").focus();
                    return false;
                }
                if (document.getElementById('NoKTP').value && document.getElementById('NoKTP').value.length < 16) {
                    alert('Masukan No. KTP minimal 16 Karakter !');
                    document.getElementById("NoKTP").focus();
                    return false;
                }
                if (document.getElementById('iddonor').value == '' && document.getElementById('NoKTP').value == '') {
                    if (document.getElementById('nama').value.length < 3) {
                        alert('Pencarian Nama harus diisi/minimal 3 Karakter!');
                        return false;
                    }
                    if (document.getElementById('tgl').value == '') {
                        alert('Masukan Tanggal Lahir !');
                        document.getElementById("tgl").focus();
                        return false;
                    }
                    if (document.getElementById('bln').value == '') {
                        alert('Masukan Bulan Lahir !');
                        document.getElementById("bln").focus();
                        return false;
                    }
                    if (document.getElementById('thn').value == '') {
                        alert('Masukan Tahun Lahir !');
                        document.getElementById("thn").focus();
                        return false;
                    }
                }
                //        if (document.getElementById('alamat').value.length == 0)
                //        {
                //            alert('Alamat diisi dengan jelas dan lengkap ..');return false;
                //        }

                // Cegah klik berkali-kali & beri tahu user proses pencarian sedang berjalan.
                // CATATAN: tombol TIDAK di-set disabled=true di sini. Men-disable tombol
                // submit sebelum form benar-benar terkirim membuat browser mengecualikan
                // name/value tombol itu (name="lokal") dari data yang dikirim, sehingga
                // $_POST['lokal'] di PHP tidak ke-set dan pencarian gagal berjalan sama
                // sekali. Jadi cukup gunakan class CSS supaya tombol terlihat & berperilaku
                // nonaktif (pointer-events: none) tanpa mengubah atribut disabled aslinya.
                var tombolCari = document.getElementById('cariPendonor');
                if (tombolCari) {
                    if (tombolCari.classList.contains('btn-processing')) {
                        return false;
                    }
                    tombolCari.classList.add('btn-processing');
                    tombolCari.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mencari...';
                }

                var loader = document.getElementById('searchLoaderOverlay');
                if (loader) {
                    loader.style.display = 'flex';
                }

                return true;
            }
        </script>
        <script>
            $(function() {
                //Date picker
                $('#reservationdate').datetimepicker({
                    format: 'yyyy-MM-DD'
                });

                //Date picker
                $('#reservationdate2').datetimepicker({
                    format: 'yyyy-MM-DD'
                });

                //Date and time picker
                $('#reservationdatetime').datetimepicker({
                    icons: {
                        time: 'far fa-clock'
                    }
                });

                //Date range picker
                $('#reservation').daterangepicker()
                //Date range picker with time picker
                $('#reservationtime').daterangepicker({
                    timePicker: true,
                    timePickerIncrement: 30,
                    locale: {
                        format: 'MM/DD/YYYY hh:mm A'
                    }
                })
                //Date range as a button
                $('#daterange-btn').daterangepicker({
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                                'month').endOf('month')]
                        },
                        startDate: moment().subtract(29, 'days'),
                        endDate: moment()
                    },
                    function(start, end) {
                        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                            'MMMM D, YYYY'))
                    }
                )
                $("#datepicker2").datepicker({
                    dateFormat: "yy-mm-dd",
                });
            });
        </script>
        <!-- Bootstrap 4 -->
        <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/adminlte.min.js"></script>
        <!-- jQuery -->
        <script src="../tpksoloplugins/jquery/jquery.min.js"></script>

    </body>

    </html>
<?php } ?>