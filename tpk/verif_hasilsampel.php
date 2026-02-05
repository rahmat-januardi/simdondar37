<head>
    <script language=javascript src="js/jquery-latest.js" type="text/javascript"> </script>
    <script language=javascript src="js/util.js" type="text/javascript"> </script>
    <script language="javascript" src="js/AjaxRequest.js" type="text/javascript"></script>
    <link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
    <link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
    <script type="text/javascript" src="js/tgl_rekap.js"></script>
    <script type="text/javascript" src="js/disable_enter.js"></script>
</head>
<?

require_once('config/dbi_connect.php');
require_once('clogin.php');
$namauser = $_SESSION['namauser'];
$lv0 = $_SESSION['leveluser'];
$q_udd = mysqli_fetch_assoc(mysqli_query($dbi, "select * from utd where aktif='1'"));
$zona_waktu = $q_udd['zonawaktu'];
date_default_timezone_set($zona_waktu);
$namaudd = $q_udd['nama'];
$today = date('Y-m-d');
$namalab = $namaudd;
$kode        = $_GET['kode'];
$act         = $_GET['act'];
$donor       = $_GET['donor'];
$transaksi   = $_GET['trans'];

if ($act == "lanjut") {


    $qinst = "UPDATE samplekode set sk_hasil='1', sk_verifikator='$namauser' where sk_kode='$kode'";
    $inst = mysqli_query($dbi, $qinst);
    if ($inst) {
        $dk = mysqli_query($dbi, "select nama,telp2 from pendonor where Kode='$donor' and LENGTH(telp2)>9");
        if (mysqli_num_rows($dk) == 1) {
            $dk1 = mysqli_fetch_assoc($dk);
            $telp = $dk1['telp2'];
            $pesan = 'Yth. ' . $dk1[nama] . ', ' . 'Hasil Pemeriksaan Uji Klinis Sampel Darah Anda dinyatakan LULUS untuk menjadi pendonor Plasma, selanjutnya Anda akan dijadwalkan pengambilan donor oleh PMI. Untuk keterangan lebih lanjut silahkan menghubungi UDD PMI. Terima Kasih & Sehat Selalu';
            $kirim = mysqli_query($dbi, "INSERT into wagw.outbox(`wa_mode`,`wa_no`,`wa_text`) values('0','$telp','$pesan')");
        }

        $log_mdl = 'HEMATOLOGI';
        $log_aksi = 'Pelulusan Pemeriksaan Darah Lengkap Sample/Kantong : ' . $kode;
        include('user_log.php');
        echo ("<font size=3>Data pemeriksaan sample sudah berhasil disimpan<br></font>
            <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"1; URL=pmi" . $lv0 . ".php?module=hasilsampel\">");
    } else {
        echo 'GAGAL, dalam penyimpanan hasil<br>';
    }
} else if ($act == "gagal") {


    $qinst = "UPDATE samplekode set sk_hasil='2', sk_verifikator='$namauser' where sk_kode='$kode'";
    $inst = mysqli_query($dbi, $qinst);
    $ht = "UPDATE htransaksi set pengambilan='1',`Status`='3' where NoTrans='$transaksi'";
    if ($inst) {
        $dk = mysqli_query($dbi, "select nama,telp2 from pendonor where Kode='$donor' and LENGTH(telp2)>9");
        if (mysqli_num_rows($dk) == 1) {
            $dk1 = mysqli_fetch_assoc($dk);
            $telp = $dk1['telp2'];
            $pesan = 'Yth. ' . $dk1[nama] . ', ' . 'Hasil Pemeriksaan Uji Klinis Sampel Darah Anda dinyatakan TIDAK LULUS untuk menjadi pendonor Plasma. Untuk keterangan lebih lanjut silahkan menghubungi UDD PMI, Terima Kasih & Sehat Selalu';
            $kirim = mysqli_query($dbi, "INSERT into wagw.outbox(`wa_mode`,`wa_no`,`wa_text`) values('0','$telp','$pesan')");
        }

        $log_mdl = 'HEMATOLOGI';
        $log_aksi = 'Pelulusan Pemeriksaan Darah Lengkap Sample/Kantong : ' . $kode;
        include('user_log.php');
        echo ("<font size=3>Data pemeriksaan sample sudah berhasil disimpan<br></font>
            <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"1; URL=pmi" . $lv0 . ".php?module=hasilsampel\">");
    } else {
        echo 'GAGAL, dalam penyimpanan hasil<br>';
    }
} else if ($act == "ulang") {


    $qinst = "UPDATE samplekode set sk_hasil='1', sk_verifikator='$namauser' where sk_kode='$kode'";
    $inst = mysqli_query($dbi, $qinst);
    if ($inst) {
        $dk = mysqli_query($dbi, "select nama,telp2 from pendonor where Kode='$donor' and LENGTH(telp2)>9");
        if (mysqli_num_rows($dk) == 1) {
            $dk1 = mysqli_fetch_assoc($dk);
            $telp = $dk1['telp2'];
            $pesan = 'Yth. ' . $dk1[nama] . ', ' . 'Hasil Pemeriksaan Uji Klinis Sampel Darah Anda harus kami cek hematologi ulang untuk menjadi pendonor Plasma. Untuk keterangan lebih lanjut silahkan menghubungi UDD PMI, Terima Kasih & Sehat Selalu ';
            $kirim = mysqli_query($dbi, "INSERT into wagw.outbox(`wa_mode`,`wa_no`,`wa_text`) values('0','$telp','$pesan')");
        }
        $log_mdl = 'HEMATOLOGI';
        $log_aksi = 'Cek Ulang Pemeriksaan Darah Lengkap Sample/Kantong : ' . $kode;
        include('user_log.php');
        echo ("<font size=3>Data pemeriksaan sample sudah berhasil disimpan<br></font>
            <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"1; URL=pmi" . $lv0 . ".php?module=hasilsampel\">");
    } else {
        echo 'GAGAL, dalam penyimpanan hasil<br>';
    }
} else if ($act == "batal") {


    $qinst = "UPDATE samplekode set sk_hasil='2', sk_verifikator='$namauser' where sk_kode='$kode'";
    $inst = mysqli_query($dbi, $qinst);
    $ht = "UPDATE htransaksi set pengambilan='1',`Status`='3' where NoTrans='$transaksi'";
    $event = "update `events` set stat =2 where notrans='$transaksi'";
    if ($inst) {


        $log_mdl = 'HEMATOLOGI';
        $log_aksi = 'Pembatalan Penjadwalan  Sample/Kantong : ' . $kode;
        include('user_log.php');
        echo ("<font size=3>Data pemeriksaan sample sudah berhasil disimpan<br></font>
            <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"1; URL=pmi" . $lv0 . ".php?module=sampellulus\">");
    } else {
        echo 'GAGAL, dalam penyimpanan hasil<br>';
    }
}


?>