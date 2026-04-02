<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMDONDAR</title>
    <link href="bootsrap337/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootsrap337/js/html5shiv.min.js"></script>
    <script src="bootsrap337/js/respond.min.js"></script>
    <link href="bootsrap337/bspmi.css" rel="stylesheet">
    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
    <link type="text/css" href="../../css/blitzer/jquery-ui.min.css" rel="stylesheet" />
    <script type="text/javascript" charset="utf-8" src="./../js/jquery-ui.min.js"></script>
    <style>
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
        }
    </style>
    <script>
        jQuery(document).ready(function() {
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
                    $("#cari").click();
                }
            });
        });
    </script>
</head>

<body>

    <?php
    session_start();
    require_once('config/dbi_connect.php');
    //cek internet
    function cek_net()
    {
        $connected = @fsockopen("dbdonor.pmi.or.id", 80);
        if ($connected) {
            $is_conn = true; // jika koneksi tersambung
            fclose($connected);
        } else {
            $is_conn = false; //jika koneksi gagal
            fclose($connected);
        }
        return $is_conn;
    }
    //
    $msg = "";
    $msgtipe = "alert-info";
    $msgupload = "";

    //hostname
    $td0 = php_uname('n');
    $td0 = strtoupper($td0);
    $td0 = substr($td0, 0, 2);
    if ($td0 == 'SE') {
        $char = "DG";
    } else {
        $char = $td0;
        $tempat = "MU";
    }

    $tahun = date('Y');
    $udd = mysqli_fetch_array(mysqli_query($dbi, "SELECT * from utd where `aktif`='1'"));
    $unit = mysqli_fetch_array(mysqli_query($dbi, "SELECT id1 from tempat_donor where `active`=1"));
    $idudd = $udd['id'];

    //SIMPAN DONOR BARU -- START
    if (isset($_POST['tambah'])) {
        $sekarang = date("Y-m-d H:i:s");
        $now = date("Y-m-d");
        $year = date("Y");
        $mnoktp = $_POST['mnoktp'];
        $mnama = $_POST['mnama'];
        $mjk = $_POST['mjk'];
        $mtmplahir = $_POST['mtmplahir'];
        $mtgl = $_POST['mtgl'];
        $mbln = $_POST['mbln'];
        $mthn = $_POST['mthn'];
        $malamat = $_POST['malamat'];
        $mkelurahan = $_POST['mkelurahan'];
        $mkecamatan = $_POST['mkecamatan'];
        $mwilayah = $_POST['mwilayah'];
        $mtelp = $_POST['mtelp'];
        $mgoldarah = $_POST['mgoldarah'];
        $mrhesus = $_POST['mrhesus'];
        $mmenikah = $_POST['mmenikah'];
        $mpekerjaan = $_POST['mpekerjaan'];
        $mjumdonor = $_POST['mjumdonor'];
        $mdonorke = $mjumdonor + 1;
        $mtgl_lhr = $mthn . "-" . $mbln . "-" . $mtgl;
        $idudd = $udd['id'];
        $namauser = $_SESSION['namauser'];

        $edaftar = $_POST['mdaftar'];
        $jenis_donor = $_POST['mjenis_donor'];
        $metode = $_POST['mmetode'];
        $lengan = $_POST['mlengan'];
        $tcetak = $_POST['mcetak'];
        $jam_donor = date("H:i:s");
        $mumur = $year - $mthn;
        $aph = $tpk = "0";
        switch ($_POST['mmetode']) {
            case '2':
                $aph = 1;
                break;
            case '3':
                $tpk = 1;
                break;
            default:
                break;
        }

        //CARI KTP Pendonor
        $KTP = mysqli_num_rows(mysqli_query($dbi, "select Kode from pendonor where NoKTP ='$mnoktp'"));
        if ($KTP > 0) { ?>
            <script>
                alert("Nomor KTP sudah digunakan sebelumnya");
            </script>
            <?php

        } else {

            //------------------------ set id pendonor ------------------------->
            //digit pendonor 14 digit, 4kode utd, 3 nama, 2 tmpt aftap, 6 sequence,

            function getInitials($string)
            {
                $cleanString = preg_replace('/[\s\W]+/', '', $string);
                return substr($cleanString, 0, 3);
            }
            $initials = getInitials($mnama);

            $nama1 = str_replace(".", "", $mnama);
            $nama1 = str_replace(" ", "", $mnama);
            $nama1 = str_replace(",", "", $mnama);
            $nm = strtoupper(substr($initials, 0, 3));
            $kdtp = $udd['id'] . $unit['id1'] . $nm;
            $idp = mysqli_query($dbi, "select Kode from pendonor where Kode like '$kdtp%'
                     order by Kode DESC");
            $idp1 = mysqli_fetch_assoc($idp);
            $idp2 = substr($idp1['Kode'], 9, 6);
            if ($idp2 < 1) {
                $idp2 = "00000";
            }
            $int_idp2 = (int) $idp2 + 1;
            $j_nol1 = 6 - (strlen(strval($int_idp2)));
            for ($i = 0; $i < $j_nol1; $i++) {
                $idp4 .= "0";
            }
            $kodep = $kdtp . $idp4 . $int_idp2;
            //---------------------- END set id pendonor ------------------------->

            $insertdonor = "insert into pendonor
        (`Kode`,`NoKTP`,`Nama`,`Alamat`,`Jk`,`Pekerjaan`,
        `telp`,`TempatLhr`,`TglLhr`,`Status`,`GolDarah`,
        `Rhesus`,`Call`,`kelurahan`,`kecamatan`,`wilayah`,`jumDonor`,`title`,
        `telp2`,`umur`,`tglkembali`,
        `pencatat`,`mu`,`cekal`,`up`,`waktu_update`,`tanggal_entry`,`apheresis`)
        values ('$kodep','$mnoktp','$mnama','$malamat','$mjk','$mpekerjaan',
        '$mtelp','$mtmplahir','$mtgl_lhr','$mmenikah','$mgoldarah',
        '$mrhesus','1','$mkelurahan','$mkecamatan','$mwilayah','$mjumdonor','-',
        '$mtelp','$mumur','$now',
        'test data','','0','','$sekarang','$sekarang','0')";
            //echo $insertdonor;

            $qins = mysqli_query($dbi, $insertdonor);
            if ($qins) {

                //Audit Trail
                //=======Audit Trial====================================================================================
                $log_mdl = 'REGISTRASI';
                $log_aksi = 'Tambah Pendonor: ' . $kodep . ' Nama: ' . $mnama;
                include_once "user_log.php";
                //=====================================================================================================

                if (cek_net() == true) {
                    //insert ke nasional
                    $curlinsdn = curl_init();
                    curl_setopt_array($curlinsdn, array(
                        CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/insertpendonor.php",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 5,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => array('idudd' => $idudd, 'Kode' => $kodep, 'NoKTP' => $mnoktp, 'Nama' => $mnama, 'Alamat' => $malamat, 'Jk' => $mjk, 'Pekerjaan' => $mpekerjaan, 'TempatLhr' => $mtmplahir, 'TglLhr' => $mtgl_lhr, 'Status' => $mmenikah, 'kelurahan' => $mkelurahan, 'kecamatan' => $mkecamatan, 'wilayah' => $mwilayah, 'telp2' => $mtelp, 'GolDarah' => $mgoldarah, 'Rhesus' => $mrhesus, 'jumDonor' => $mjumdonor, 'Call' => '1', 'tglkembali' => $now, 'umur' => $mumur, 'metode' => 'insert'),
                    ));
                    $response = curl_exec($curlinsdn);
                    $datains = json_decode($response, true);
                    //echo "<pre>"; print_r($response); echo "</pre>";
                    curl_close($curlinsdn);
                }

                //Jika Daftar Donor
                if ($edaftar == "1") {
                    $selectht = mysqli_num_rows(mysqli_query($dbi, "select * from htransaksi where KodePendonor='$kodep' AND date(Tgl)='$now'"));
                    if ($selectht > 0) { ?>
                        <script>
                            alert("Pendonor Sudah Terdaftar di Antrian");
                        </script><?php
                                } else {
                                    //Shift Petugas
                                    $shift = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
                                    //$shif   = $shift['nama'];
                                    if ($shift['nama'] == "I") {
                                        $shif = "1";
                                    } else if ($shift['nama'] == "II") {
                                        $shif = "2";
                                    } else if ($shift['nama'] == "III") {
                                        $shif = "3";
                                    } else {
                                        $shif = "4";
                                    }
                                    //------------------------ set id transaksi ------------------------->

                                    $idtp = mysqli_query($dbi, "select * from tempat_donor where active='1'");
                                    $idtp1 = mysqli_fetch_assoc($idtp);
                                    $th = substr(date("Y"), 2, 2);
                                    $bl = date("m");
                                    $tgl = date("d");
                                    $kdtp = substr($idtp1['id1'], 0, 2) . $tgl . $bl . $th . "-" . $udd['id'] . "-";
                                    $idp = mysqli_query($dbi, "select NoTrans from htransaksi where NoTrans like '$kdtp%' order by NoTrans DESC");
                                    $idp1 = mysqli_fetch_assoc($idp);
                                    $idp2 = substr($idp1['NoTrans'], 14, 4);
                                    if ($idp2 < 1) {
                                        $idp2 = "0000";
                                    }
                                    $idp3 = (int) $idp2 + 1;
                                    $id31 = strlen($idp2) - strlen($idp3);
                                    $idp4 = "";
                                    for ($i = 0; $i < $id31; $i++) {
                                        $idp4 .= "0";
                                    }
                                    $id_transaksi_baru = $kdtp . $idp4 . $idp3;
                                    //------------------------ END set id transaksi ------------------------->

                                    $namauser = $_SESSION['namauser'];
                                    $lv0 = $_SESSION['leveluser'];
                                    $v_notransaksi = $id_transaksi_baru;


                                    //ID SERVER
                                    if ($metode == "1") {
                                        $aph = "0";
                                        $tpk = "0";
                                    } else {
                                        $aph = "1";
                                        $tpk = "0";
                                    }
                                    if ($donorke > 1) {
                                        $donorbaru = 1;
                                    } else {
                                        $donorbaru = 0;
                                    }
                                    if (substr($idtp1['id1'], 0, 1) == "M") {
                                        $tempat = "M";
                                        $rs1 = mysqli_fetch_assoc(mysqli_query($dbi, "select * from detailinstansi where aktif='1'"));
                                        $namains = $rs1['nama'];
                                    } else {
                                        $tempat = "M";
                                        $namains = "";
                                    }
                                    //ID SERVER

                                    //QUERY Htransaksi
                                    $q_htrans = "insert into htransaksi
                                (NoTrans,KodePendonor,KodePendonor_lama,Tgl,Pengambilan,ketBatal,tempat,Instansi, JenisDonor,id_permintaan,Status,Nopol,apheresis,kendaraan,shift,kota,umur,donorbaru,jk, gol_darah,rhesus,pekerjaan,donorke,user,jam_mulai,rs, donor_tpk) value ('$v_notransaksi','$kodep','$kodep','$sekarang','-','-','0','$namains','$jenis_donor','','0','-','$aph','','$shif','$udd[id]','$mumur','$donorbaru','$mjk','$mgoldarah','$mrhesus','$mpekerjaan','$mdonorke','$namauser','$jam_donor','','$tpk')";
                                    if (mysqli_query($dbi, $q_htrans)) {

                                        $lanjut = '0';
                                        if ($lanjut == "0") {
                                            //=======Audit Trial====================================================================================
                                            $log_mdl = 'REGISTRASI';
                                            $log_aksi = 'Registrasi: ' . $id_transaksi_baru . ' Donor: ' . $id . ' ' . $jenis_donasi;
                                            include_once "user_log.php";
                                            //=====================================================================================================

                                            /////=========> Antrian Donor
                                            $antri = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT count(nomor) as nomor from `antrian` where tgl= curdate() limit 1"));
                                            $no_antri = $antri['nomor'] + 1;

                                            $q_antri = "INSERT INTO `antrian` (`transaksi`, `pendonor`, `nama`,`nomor`,`tgl`,`donorke`,`lengan`) VALUES ('$v_notransaksi', '$kodep','$mnama','$no_antri','$now','$mdonorke','$lengan')";
                                            $antrikan = mysqli_query($dbi, $q_antri);
                                            //echo $q_antri;

                                            /////=========> Informed Consent
                                            if ($tcetak == '1') {
                                                //echo "Data Telah berhasil dimasukkan, isikan inform concent pendonor<br>"; 
                                    ?>
                                    <META http-equiv="refresh" content="1; url=Formulir23-st.php?kp=<?= $kodep ?>&trans=<?= $v_notransaksi ?>"><?php
                                                                                                                                            } else { ?>
                                    <script>
                                        alert("Transaksi Donor berhasil disimpan");
                                    </script>
                <?php }
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo "Data GAGAL<br>";
                                                                                                                                    }
                                                                                                                                    //Query htransaksi
                                                                                                                                }
                                                                                                                            }
                                                                                                                        } else { ?>
                <script>
                    alert("Transaksi Donor Gagal disimpan");
                </script>
        <?php
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                                //SIMPAN DONOR BARU -- END


                                                                                                                //EDIT DONOR LOKAL
                                                                                                                if (isset($_POST['editlokal'])) {
                                                                                                                    $sekarang = date("Y-m-d H:i:s");
                                                                                                                    $now = date("Y-m-d");
                                                                                                                    $year = date("Y");
                                                                                                                    $mnoktp = $_POST['enoktp'];
                                                                                                                    $ekode = $_POST['ekode'];
                                                                                                                    $mnama = $_POST['enama'];
                                                                                                                    $mjk = $_POST['ejk'];
                                                                                                                    $mtmplahir = $_POST['etmplahir'];
                                                                                                                    $mtgl = $_POST['etgl'];
                                                                                                                    $mbln = $_POST['ebln'];
                                                                                                                    $mthn = $_POST['ethn'];
                                                                                                                    $malamat = $_POST['ealamat'];
                                                                                                                    $mkelurahan = $_POST['ekelurahan'];
                                                                                                                    $mkecamatan = $_POST['ekecamatan'];
                                                                                                                    $mwilayah = $_POST['ewilayah'];
                                                                                                                    $mtelp = $_POST['etelp'];
                                                                                                                    $mgoldarah = $_POST['egoldarah'];
                                                                                                                    $mrhesus = $_POST['erhesus'];
                                                                                                                    $mmenikah = $_POST['emenikah'];
                                                                                                                    $mpekerjaan = $_POST['epekerjaan'];
                                                                                                                    $mjumdonor = $_POST['ejumdonor'];
                                                                                                                    $etglkembali = $_POST['etglkembali'];
                                                                                                                    $eumur = $year - $mthn;
                                                                                                                    $mdonorke = $mjumdonor + 1;
                                                                                                                    $mtgl_lhr = $mthn . "-" . $mbln . "-" . $mtgl;
                                                                                                                    $edaftar = $_POST['edaftar'];
                                                                                                                    $jenis_donor = $_POST['ejenis_donor'];
                                                                                                                    $metode = $_POST['emetode'];
                                                                                                                    $lengan = $_POST['elengan'];
                                                                                                                    $tcetak = $_POST['ecetak'];
                                                                                                                    $jam_donor = date("H:i:s");
                                                                                                                    //echo "Edaftar ==> ".$edaftar;

                                                                                                                    $udd1 = mysqli_query($dbi, "select id from utd where aktif='1'");
                                                                                                                    $udd = mysqli_fetch_assoc($udd1);
                                                                                                                    $idudd = $udd['id'];


                                                                                                                    //Edit Data Lokal
                                                                                                                    $edit = mysqli_query($dbi, "UPDATE pendonor set NoKTP='$mnoktp', Nama='$mnama', Alamat= '$malamat', Jk='$mjk', Pekerjaan= '$mpekerjaan', TempatLhr= '$mtmplahir', TglLhr='$mtgl_lhr', Status= '$mmenikah', kelurahan= '$mkelurahan', kecamatan= '$mkecamatan', wilayah='$mwilayah', GolDarah= '$mgoldarah', Rhesus= '$mrhesus', jumDonor='$mjumdonor', telp2='$mtelp', tglkembali='$etglkembali', umur = '$eumur' where Kode='$ekode'");


                                                                                                                    //insert ke nasional
                                                                                                                    $curlinsdn = curl_init();
                                                                                                                    curl_setopt_array($curlinsdn, array(
                                                                                                                        CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/insertpendonor.php",
                                                                                                                        CURLOPT_RETURNTRANSFER => true,
                                                                                                                        CURLOPT_ENCODING => "",
                                                                                                                        CURLOPT_MAXREDIRS => 10,
                                                                                                                        CURLOPT_TIMEOUT => 5,
                                                                                                                        CURLOPT_FOLLOWLOCATION => true,
                                                                                                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                                                                                        CURLOPT_CUSTOMREQUEST => "POST",
                                                                                                                        CURLOPT_POSTFIELDS => array('idudd' => $idudd, 'Kode' => $ekode, 'NoKTP' => $mnoktp, 'Nama' => $mnama, 'Alamat' => $malamat, 'Jk' => $mjk, 'Pekerjaan' => $mpekerjaan, 'TempatLhr' => $mtmplahir, 'TglLhr' => $mtgl_lhr, 'Status' => $mmenikah, 'kelurahan' => $mkelurahan, 'kecamatan' => $mkecamatan, 'wilayah' => $mwilayah, 'telp2' => $mtelp, 'GolDarah' => $mgoldarah, 'Rhesus' => $mrhesus, 'jumDonor' => $mjumdonor, 'Call' => '1', 'tglkembali' => $etglkembali, 'umur' => $eumur, 'metode' => 'insert'),
                                                                                                                    ));
                                                                                                                    $response = curl_exec($curlinsdn);
                                                                                                                    $datains = json_decode($response, true);
                                                                                                                    //echo "<pre>"; print_r($response); echo "</pre>";
                                                                                                                    curl_close($curlinsdn);
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-success alert-dismissable" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <strong>Data Pendonor</strong> Tersimpan
                </div>
            </div>
        </div>
        <?php
                                                                                                                    //insert ke nasional --- END

                                                                                                                    //JIKA DAFTAR DONOR
                                                                                                                    //CARI TRANSAKSI SEBELUMNYA
                                                                                                                    if ($edaftar == "1") {
                                                                                                                        $selectht = mysqli_num_rows(mysqli_query($dbi, "select * from htransaksi where KodePendonor='$ekode' AND date(Tgl)='$now'"));
                                                                                                                        if ($selectht > 0) { ?>
                <script>
                    alert("Pendonor Sudah Terdaftar di Antrian");
                </script><?php
                                                                                                                        } else {
                                                                                                                            //Shift Petugas
                                                                                                                            $shift = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
                                                                                                                            //$shif   = $shift['nama'];
                                                                                                                            if ($shift['nama'] == "I") {
                                                                                                                                $shif = "1";
                                                                                                                            } else if ($shift['nama'] == "II") {
                                                                                                                                $shif = "2";
                                                                                                                            } else if ($shift['nama'] == "III") {
                                                                                                                                $shif = "3";
                                                                                                                            } else {
                                                                                                                                $shif = "4";
                                                                                                                            }
                                                                                                                            //------------------------ set id transaksi ------------------------->

                                                                                                                            $idtp = mysqli_query($dbi, "select * from tempat_donor where active='1'");
                                                                                                                            $idtp1 = mysqli_fetch_assoc($idtp);
                                                                                                                            $th = substr(date("Y"), 2, 2);
                                                                                                                            $bl = date("m");
                                                                                                                            $tgl = date("d");
                                                                                                                            $kdtp = substr($idtp1['id1'], 0, 2) . $tgl . $bl . $th . "-" . $udd['id'] . "-";
                                                                                                                            $idp = mysqli_query($dbi, "select NoTrans from htransaksi where NoTrans like '$kdtp%' order by NoTrans DESC");
                                                                                                                            $idp1 = mysqli_fetch_assoc($idp);
                                                                                                                            $idp2 = substr($idp1['NoTrans'], 14, 4);
                                                                                                                            if ($idp2 < 1) {
                                                                                                                                $idp2 = "0000";
                                                                                                                            }
                                                                                                                            $idp3 = (int) $idp2 + 1;
                                                                                                                            $id31 = strlen($idp2) - strlen($idp3);
                                                                                                                            $idp4 = "";
                                                                                                                            for ($i = 0; $i < $id31; $i++) {
                                                                                                                                $idp4 .= "0";
                                                                                                                            }
                                                                                                                            $id_transaksi_baru = $kdtp . $idp4 . $idp3;
                                                                                                                            //------------------------ END set id transaksi ------------------------->

                                                                                                                            $namauser = $_SESSION['namauser'];
                                                                                                                            $lv0 = $_SESSION['leveluser'];
                                                                                                                            $v_notransaksi = $id_transaksi_baru;


                                                                                                                            //ID SERVER
                                                                                                                            if ($metode == "1") {
                                                                                                                                $aph = "0";
                                                                                                                                $tpk = "0";
                                                                                                                            } else {
                                                                                                                                $aph = "1";
                                                                                                                                $tpk = "0";
                                                                                                                            }
                                                                                                                            if ($donorke == "1") {
                                                                                                                                $donorbaru = "0";
                                                                                                                            } else {
                                                                                                                                $donorbaru = "1";
                                                                                                                            }
                                                                                                                            if (substr($idtp1['id1'], 0, 1) == "M") {
                                                                                                                                $tempat = "M";
                                                                                                                                $rs1 = mysqli_fetch_assoc(mysqli_query($dbi, "select * from detailinstansi where aktif='1'"));
                                                                                                                                $namains = $rs1['nama'];
                                                                                                                            } else {
                                                                                                                                $tempat = "M";
                                                                                                                                $namains = "";
                                                                                                                            }
                                                                                                                            //ID SERVER

                                                                                                                            //QUERY Htransaksi
                                                                                                                            $q_htrans = "insert into htransaksi
                    (NoTrans,KodePendonor,KodePendonor_lama,Tgl,Pengambilan,ketBatal,tempat,Instansi, JenisDonor,id_permintaan,Status,Nopol,apheresis,kendaraan,shift,kota,umur,donorbaru,jk, gol_darah,rhesus,pekerjaan,donorke,user,jam_mulai,rs, donor_tpk) value ('$v_notransaksi','$ekode','$ekode','$sekarang','-','-','0','$namains','$jenis_donor','','0','-','$aph','','$shif','$udd[id]','$eumur','$donorbaru','$mjk','$mgoldarah','$mrhesus','$mpekerjaan','$mdonorke','$namauser','$jam_donor','','$tpk')";
                                                                                                                            if (mysqli_query($dbi, $q_htrans)) {

                                                                                                                                $lanjut = '0';
                                                                                                                            } else { ?>
                    <script>
                        alert("Transaksi Donor Gagal disimpan");
                    </script>
                    <?php
                                                                                                                            }
                                                                                                                            //Query htransaksi

                                                                                                                            //Cetak Formulir
                                                                                                                            if ($lanjut == "0") {
                                                                                                                                //=======Audit Trial====================================================================================
                                                                                                                                $log_mdl = 'REGISTRASI';
                                                                                                                                $log_aksi = 'Registrasi: ' . $id_transaksi_baru . ' Donor: ' . $id . ' ' . $jenis_donasi;
                                                                                                                                include_once "user_log.php";
                                                                                                                                //=====================================================================================================

                                                                                                                                /////=========> Antrian Donor
                                                                                                                                $antri = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT count(nomor) as nomor from `antrian` where tgl= curdate() limit 1"));
                                                                                                                                $no_antri = $antri['nomor'] + 1;

                                                                                                                                $q_antri = "INSERT INTO `antrian` (`transaksi`, `pendonor`, `nama`,`nomor`,`tgl`,`donorke`,`lengan`) VALUES ('$v_notransaksi', '$ekode','$mnama','$no_antri','$now','$mdonorke','$lengan')";
                                                                                                                                $antrikan = mysqli_query($dbi, $q_antri);
                                                                                                                                //echo $q_antri;

                                                                                                                                /////=========> Informed Consent
                                                                                                                                if ($tcetak == '1') {
                                                                                                                                    //echo "Data Telah berhasil dimasukkan, isikan inform concent pendonor<br>"; 
                    ?>
                        <META http-equiv="refresh" content="1; url=Formulir23-st.php?kp=<?= $ekode ?>&trans=<?= $v_notransaksi ?>"><?php
                                                                                                                                } else { ?>
                        <script>
                            alert("Transaksi Donor berhasil disimpan");
                        </script>
                <?php

                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                    //JIKA DAFTAR DONOR ---- END

                                                                                                                }
                                                                                                                //EDIT DONOR LOKAL -- END

                                                                                                                //EDIT DONOR NASIONAL
                                                                                                                if (isset($_POST['editnasional'])) {
                                                                                                                    $sekarang = date("Y-m-d H:i:s");
                                                                                                                    $now = date("Y-m-d");
                                                                                                                    $year = date("Y");
                                                                                                                    $mnoktp = $_POST['nnoktp'];
                                                                                                                    $ekode = $_POST['nkode'];
                                                                                                                    $mnama = $_POST['nnama'];
                                                                                                                    $mjk = $_POST['njk'];
                                                                                                                    $mtmplahir = $_POST['ntmplahir'];
                                                                                                                    $mtgl = $_POST['ntgl'];
                                                                                                                    $mbln = $_POST['nbln'];
                                                                                                                    $mthn = $_POST['nthn'];
                                                                                                                    $malamat = $_POST['nalamat'];
                                                                                                                    $mkelurahan = $_POST['nkelurahan'];
                                                                                                                    $mkecamatan = $_POST['nkecamatan'];
                                                                                                                    $mwilayah = $_POST['nwilayah'];
                                                                                                                    $mtelp = $_POST['ntelp'];
                                                                                                                    $mgoldarah = $_POST['ngoldarah'];
                                                                                                                    $mrhesus = $_POST['nrhesus'];
                                                                                                                    $mmenikah = $_POST['nmenikah'];
                                                                                                                    $mpekerjaan = $_POST['npekerjaan'];
                                                                                                                    $mjumdonor = $_POST['njumdonor'];
                                                                                                                    $etglkembali = $_POST['ntglkembali'];
                                                                                                                    $eumur = $year - $mthn;
                                                                                                                    $mdonorke = $mjumdonor + 1;
                                                                                                                    $mtgl_lhr = $mthn . "-" . $mbln . "-" . $mtgl;
                                                                                                                    $edaftar = $_POST['ndaftar'];
                                                                                                                    $jenis_donor = $_POST['njenis_donor'];
                                                                                                                    $metode = $_POST['nmetode'];
                                                                                                                    $lengan = $_POST['nlengan'];
                                                                                                                    $tcetak = $_POST['ncetak'];
                                                                                                                    $jam_donor = date("H:i:s");
                                                                                                                    //echo "Edaftar ==> ".$edaftar;

                                                                                                                    $udd1 = mysqli_query($dbi, "select id from utd where aktif='1'");
                                                                                                                    $udd = mysqli_fetch_assoc($udd1);
                                                                                                                    $idudd = $udd['id'];


                                                                                                                    //Compare Lokal
                                                                                                                    $carilokal = mysqli_query($dbi, "select * from pendonor where Kode = '$ekode'");
                                                                                                                    $rowlokal = mysqli_num_rows($carilokal);

                                                                                                                    // Jika Lokal Ada
                                                                                                                    if ($rowlokal > 0) {

                                                                                                                        $datalokal = mysqli_fetch_array($carilokal);
                                                                                                                        $jum = $datalokal['jumDonor'];
                                                                                                                        $jumnas = (int) $mjumdonor;


                                                                                                                        //Update Nasional ke Lokal

                                                                                                                        if ($jumnas > $jum) {
                                                                                                                            $edit = mysqli_query($dbi, "UPDATE pendonor set NoKTP='$mnoktp', Nama='$mnama', Alamat= '$malamat', Jk='$mjk', Pekerjaan= '$mpekerjaan', TempatLhr= '$mtmplahir', TglLhr='$mtgl_lhr', Status= '$mmenikah', kelurahan= '$mkelurahan', kecamatan= '$mkecamatan', wilayah='$mwilayah', GolDarah= '$mgoldarah', Rhesus= '$mrhesus', jumDonor='$mjumdonor', tglkembali='$etglkembali', umur = '$eumur'    where Kode='$ekode'");

                                                                                                                            //echo "Nasional lebih banyak";
                                                                                                                        } else if ($jum > $jumnas) {
                                                                                                                            //Update lokal ke Nasional
                                                                                                                            //insert ke nasional
                                                                                                                            $curlinsdn = curl_init();
                                                                                                                            curl_setopt_array($curlinsdn, array(
                                                                                                                                CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/insertpendonor.php",
                                                                                                                                CURLOPT_RETURNTRANSFER => true,
                                                                                                                                CURLOPT_ENCODING => "",
                                                                                                                                CURLOPT_MAXREDIRS => 10,
                                                                                                                                CURLOPT_TIMEOUT => 5,
                                                                                                                                CURLOPT_FOLLOWLOCATION => true,
                                                                                                                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                                                                                                CURLOPT_CUSTOMREQUEST => "POST",
                                                                                                                                CURLOPT_POSTFIELDS => array('idudd' => $idudd, 'Kode' => $ekode, 'NoKTP' => $mnoktp, 'Nama' => $mnama, 'Alamat' => $malamat, 'Jk' => $mjk, 'Pekerjaan' => $mpekerjaan, 'TempatLhr' => $mtmplahir, 'TglLhr' => $mtgl_lhr, 'Status' => $mmenikah, 'kelurahan' => $mkelurahan, 'kecamatan' => $mkecamatan, 'wilayah' => $mwilayah, 'telp2' => $mtelp, 'GolDarah' => $mgoldarah, 'Rhesus' => $mrhesus, 'jumDonor' => $jum, 'Call' => '1', 'tglkembali' => $etglkembali, 'umur' => $eumur, 'metode' => 'update'),
                                                                                                                            ));
                                                                                                                            $response = curl_exec($curlinsdn);
                                                                                                                            $datains = json_decode($response, true);
                                                                                                                            //echo "<pre>"; print_r($response); echo "</pre>";
                                                                                                                            curl_close($curlinsdn);
                                                                                                                            //insert ke nasional --- END

                                                                                                                            //echo "Lokal lebih banyak";
                                                                                                                        } else {
                                                                                                                            $edit = mysqli_query($dbi, "UPDATE pendonor set NoKTP='$mnoktp', Nama='$mnama', Alamat= '$malamat', Jk='$mjk', Pekerjaan= '$mpekerjaan', TempatLhr= '$mtmplahir', TglLhr='$mtgl_lhr', Status= '$mmenikah', kelurahan= '$mkelurahan', kecamatan= '$mkecamatan', wilayah='$mwilayah', GolDarah= '$mgoldarah', Rhesus= '$mrhesus', jumDonor='$mjumdonor', tglkembali='$etglkembali', umur = '$eumur'    where Kode='$ekode'");

                                                                                                                            //update ke nasional
                                                                                                                            $curlinsdn = curl_init();
                                                                                                                            curl_setopt_array($curlinsdn, array(
                                                                                                                                CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/insertpendonor.php",
                                                                                                                                CURLOPT_RETURNTRANSFER => true,
                                                                                                                                CURLOPT_ENCODING => "",
                                                                                                                                CURLOPT_MAXREDIRS => 10,
                                                                                                                                CURLOPT_TIMEOUT => 5,
                                                                                                                                CURLOPT_FOLLOWLOCATION => true,
                                                                                                                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                                                                                                CURLOPT_CUSTOMREQUEST => "POST",
                                                                                                                                CURLOPT_POSTFIELDS => array('idudd' => $idudd, 'Kode' => $ekode, 'NoKTP' => $mnoktp, 'Nama' => $mnama, 'Alamat' => $malamat, 'Jk' => $mjk, 'Pekerjaan' => $mpekerjaan, 'TempatLhr' => $mtmplahir, 'TglLhr' => $mtgl_lhr, 'Status' => $mmenikah, 'kelurahan' => $mkelurahan, 'kecamatan' => $mkecamatan, 'wilayah' => $mwilayah, 'telp2' => $mtelp, 'GolDarah' => $mgoldarah, 'Rhesus' => $mrhesus, 'jumDonor' => $mjumdonor, 'Call' => '1', 'tglkembali' => $etglkembali, 'umur' => $eumur, 'metode' => 'update'),
                                                                                                                            ));
                                                                                                                            $response = curl_exec($curlinsdn);
                                                                                                                            $datains = json_decode($response, true);
                                                                                                                            //echo "<pre>"; print_r($response); echo "</pre>";
                                                                                                                            curl_close($curlinsdn);
                                                                                                                            //update ke nasional --- END

                                                                                                                            //echo "Lokal dan Nasional Sama" ;
                                                                                                                        }
                                                                                                                    } else { // Jika Lokal Tidak Ada
                                                                                                                        //INSERT LOKAL
                                                                                                                        $insertdonor = "insert into pendonor
                    (`Kode`,`NoKTP`,`Nama`,`Alamat`,`Jk`,`Pekerjaan`,
                    `telp`,`TempatLhr`,`TglLhr`,`Status`,`GolDarah`,
                    `Rhesus`,`Call`,`kelurahan`,`kecamatan`,`wilayah`,`jumDonor`,`title`,
                    `telp2`,`umur`,`tglkembali`,
                    `pencatat`,`mu`,`cekal`,`up`,`waktu_update`,`tanggal_entry`,`apheresis`)
                    values ('$ekode','$mnoktp','$mnama','$malamat','$mjk','$mpekerjaan',
                    '$mtelp','$mtmplahir','$mtgl_lhr','$mmenikah','$mgoldarah',
                    '$mrhesus','1','$mkelurahan','$mkecamatan','$mwilayah','$mjumdonor','-',
                    '$mtelp','$mumur','$etglkembali',
                    'test data','','0','','$sekarang','$sekarang','0')";
                                                                                                                        //echo $insertdonor;

                                                                                                                        $qinsert = mysqli_query($dbi, $insertdonor);
                                                                                                                    }
                                                                                                                    //Compare Lokal ---- END


                                                                                                                    //JIKA DAFTAR DONOR
                                                                                                                    //CARI TRANSAKSI SEBELUMNYA
                                                                                                                    if ($edaftar == "1") {
                                                                                                                        $selectht = mysqli_num_rows(mysqli_query($dbi, "select * from htransaksi where KodePendonor='$ekode' AND date(Tgl)='$now'"));
                                                                                                                        if ($selectht > 0) { ?>
                <script>
                    alert("Pendonor Sudah Terdaftar di Antrian");
                </script><?php
                                                                                                                        } else {
                                                                                                                            //Shift Petugas
                                                                                                                            $shift = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
                                                                                                                            //$shif   = $shift['nama'];
                                                                                                                            if ($shift['nama'] == "I") {
                                                                                                                                $shif = "1";
                                                                                                                            } else if ($shift['nama'] == "II") {
                                                                                                                                $shif = "2";
                                                                                                                            } else if ($shift['nama'] == "III") {
                                                                                                                                $shif = "3";
                                                                                                                            } else {
                                                                                                                                $shif = "4";
                                                                                                                            }
                                                                                                                            //------------------------ set id transaksi ------------------------->

                                                                                                                            $idtp = mysqli_query($dbi, "select * from tempat_donor where active='1'");
                                                                                                                            $idtp1 = mysqli_fetch_assoc($idtp);
                                                                                                                            $th = substr(date("Y"), 2, 2);
                                                                                                                            $bl = date("m");
                                                                                                                            $tgl = date("d");
                                                                                                                            $kdtp = substr($idtp1['id1'], 0, 2) . $tgl . $bl . $th . "-" . $udd['id'] . "-";
                                                                                                                            $idp = mysqli_query($dbi, "select NoTrans from htransaksi where NoTrans like '$kdtp%' order by NoTrans DESC");
                                                                                                                            $idp1 = mysqli_fetch_assoc($idp);
                                                                                                                            $idp2 = substr($idp1['NoTrans'], 14, 4);
                                                                                                                            if ($idp2 < 1) {
                                                                                                                                $idp2 = "0000";
                                                                                                                            }
                                                                                                                            $idp3 = (int) $idp2 + 1;
                                                                                                                            $id31 = strlen($idp2) - strlen($idp3);
                                                                                                                            $idp4 = "";
                                                                                                                            for ($i = 0; $i < $id31; $i++) {
                                                                                                                                $idp4 .= "0";
                                                                                                                            }
                                                                                                                            $id_transaksi_baru = $kdtp . $idp4 . $idp3;
                                                                                                                            //------------------------ END set id transaksi ------------------------->

                                                                                                                            $namauser = $_SESSION['namauser'];
                                                                                                                            $lv0 = $_SESSION['leveluser'];
                                                                                                                            $v_notransaksi = $id_transaksi_baru;


                                                                                                                            //ID SERVER
                                                                                                                            if ($metode == "1") {
                                                                                                                                $aph = "0";
                                                                                                                                $tpk = "0";
                                                                                                                            } else {
                                                                                                                                $aph = "1";
                                                                                                                                $tpk = "0";
                                                                                                                            }
                                                                                                                            if ($donorke == "1") {
                                                                                                                                $donorbaru = "0";
                                                                                                                            } else {
                                                                                                                                $donorbaru = "1";
                                                                                                                            }
                                                                                                                            if (substr($idtp1['id1'], 0, 1) == "M") {
                                                                                                                                $tempat = "M";
                                                                                                                                $rs1 = mysqli_fetch_assoc(mysqli_query($dbi, "select * from detailinstansi where aktif='1'"));
                                                                                                                                $namains = $rs1['nama'];
                                                                                                                            } else {
                                                                                                                                $tempat = "M";
                                                                                                                                $namains = "";
                                                                                                                            }
                                                                                                                            //ID SERVER

                                                                                                                            //QUERY Htransaksi
                                                                                                                            $q_htrans = "insert into htransaksi
                    (NoTrans,KodePendonor,KodePendonor_lama,Tgl,Pengambilan,ketBatal,tempat,Instansi, JenisDonor,id_permintaan,Status,Nopol,apheresis,kendaraan,shift,kota,umur,donorbaru,jk, gol_darah,rhesus,pekerjaan,donorke,user,jam_mulai,rs, donor_tpk) value ('$v_notransaksi','$ekode','$ekode','$sekarang','-','-','0','$namains','$jenis_donor','','0','-','$aph','','$shif','$udd[id]','$eumur','$donorbaru','$mjk','$mgoldarah','$mrhesus','$mpekerjaan','$mdonorke','$namauser','$jam_donor','','$tpk')";
                                                                                                                            if (mysqli_query($dbi, $q_htrans)) {

                                                                                                                                $lanjut = '0';
                                                                                                                            } else { ?>
                    <script>
                        alert("Transaksi Donor Gagal disimpan");
                    </script>
                    <?php
                                                                                                                            }
                                                                                                                            //Query htransaksi

                                                                                                                            //Cetak Formulir
                                                                                                                            if ($lanjut == "0") {
                                                                                                                                //=======Audit Trial====================================================================================
                                                                                                                                $log_mdl = 'REGISTRASI';
                                                                                                                                $log_aksi = 'Registrasi: ' . $id_transaksi_baru . ' Donor: ' . $id . ' ' . $jenis_donor;
                                                                                                                                include_once "user_log.php";
                                                                                                                                //=====================================================================================================

                                                                                                                                /////=========> Antrian Donor
                                                                                                                                $antri = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT count(nomor) as nomor from `antrian` where tgl= curdate() limit 1"));
                                                                                                                                $no_antri = $antri['nomor'] + 1;

                                                                                                                                $q_antri = "INSERT INTO `antrian` (`transaksi`, `pendonor`, `nama`,`nomor`,`tgl`,`donorke`,`lengan`) VALUES ('$v_notransaksi', '$ekode','$mnama','$no_antri','$now','$mdonorke','$lengan')";
                                                                                                                                $antrikan = mysqli_query($dbi, $q_antri);
                                                                                                                                //echo $q_antri;

                                                                                                                                /////=========> Informed Consent
                                                                                                                                if ($tcetak == '1') {
                                                                                                                                    //echo "Data Telah berhasil dimasukkan, isikan inform concent pendonor<br>"; 
                    ?>
                        <META http-equiv="refresh" content="1; url=Formulir23-st.php?kp=<?= $ekode ?>&trans=<?= $v_notransaksi ?>"><?php
                                                                                                                                } else {
                                                                                                                                    ?>
                        <script>
                            alert("Transaksi Donor berhasil disimpan");
                        </script>
            <?php
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                    //JIKA DAFTAR DONOR ---- END
                                                                                                                    echo $sqlup;
                                                                                                                }
                                                                                                                //EDIT DONOR NASIONAL -- END



                                                                                                                //Transaksi Donor -- Start
                                                                                                                if (isset($_POST['btndonor'])) {
                                                                                                                    $udd1 = mysqli_query($dbi, "select id from utd where aktif='1'");
                                                                                                                    $udd = mysqli_fetch_assoc($udd1);
                                                                                                                    $kodep = $_POST['MInpKode'];
                                                                                                                    $today1 = date('Y-m-d');
                                                                                                                    $namauser = $_SESSION['namauser'];
                                                                                                                    $lv0 = $_SESSION['leveluser'];
                                                                                                                    $id = $_POST['MInpKode'];
                                                                                                                    $nama = $_POST['MInpNama'];
                                                                                                                    $sekarang = date("Y-m-d H:i:s");

                                                                                                                    $gol = $_POST['tgol'];
                                                                                                                    $rh = $_POST['jrh'];
                                                                                                                    $jmldnr = $_POST['tjmld'];
                                                                                                                    $donorke = $_POST['tjmld'] + 1;
                                                                                                                    $tgl_lhr = $_POST['jtgllhr'];
                                                                                                                    $pekerjaan = $_POST['jkerja'];
                                                                                                                    $jenis_donor = $_POST['tjenis_donor'];
                                                                                                                    $metode = $_POST['tmetode'];
                                                                                                                    $lengan = $_POST['tlengan'];
                                                                                                                    $tcetak = $_POST['tcetak'];
                                                                                                                    $jk = $_POST['jjk'];

                                                                                                                    $jam_donor = date("H:i:s");
                                                                                                                    $year = date('Y');
                                                                                                                    $thnpd = substr($tgl_lhr, 0, 4);
                                                                                                                    $umur = $year - $thnpd;


                                                                                                                    //CARI TRANSAKSI SEBELUMNYA
                                                                                                                    $selectht = mysqli_num_rows(mysqli_query($dbi, "select * from htransaksi where KodePendonor='$kodep' AND date(Tgl)='$today1'"));
                                                                                                                    if ($selectht > 0) { ?>
            <script>
                alert("Pendonor Sudah Terdaftar di Antrian");
            </script><?php
                                                                                                                    } else {
                                                                                                                        //COMPARE PENDONOR DI LOKAL & NASIONAL --- START
                                                                                                                        //Cari di Lokal
                                                                                                                        $carilokal = mysqli_query($dbi, "select * from pendonor where Kode = '$id'");
                                                                                                                        $rowlokal = mysqli_num_rows($carilokal);

                                                                                                                        //Jika Lokal Ada
                                                                                                                        if ($rowlokal < 1) {
                                                                                                                            //$dtlokal = mysqli_fetch_array($carilokal);

                                                                                                                            //Cari di Nasional
                                                                                                                            //Insert dari Nasional
                                                                                                                            $curl = curl_init();
                                                                                                                            curl_setopt_array($curl, array(
                                                                                                                                CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/caripendonor.php",
                                                                                                                                CURLOPT_RETURNTRANSFER => true,
                                                                                                                                CURLOPT_ENCODING => "",
                                                                                                                                CURLOPT_MAXREDIRS => 10,
                                                                                                                                CURLOPT_TIMEOUT => 10,
                                                                                                                                CURLOPT_FOLLOWLOCATION => true,
                                                                                                                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                                                                                                CURLOPT_CUSTOMREQUEST => "POST",
                                                                                                                                CURLOPT_POSTFIELDS => array('kode' => $id),
                                                                                                                            ));
                                                                                                                            $response = curl_exec($curl);
                                                                                                                            curl_close($curl);
                                                                                                                            //echo $response;
                                                                                                                            $tgl = date("Y/m/d");
                                                                                                                            $data = json_decode($response, true);
                                                                                                                            $jmlad = count($data['data']);

                                                                                                                            $sekarang = date("Y-m-d H:i:s");
                                                                                                                            $now = date("Y-m-d");
                                                                                                                            $year = date("Y");
                                                                                                                            $mkodep = $data['data'][0]['pkode'];
                                                                                                                            $mnoktp = $data['data'][0]['pnoktp'];
                                                                                                                            $mnama = $data['data'][0]['pnama'];
                                                                                                                            $mjk = $data['data'][0]['pjk'];
                                                                                                                            $mtmplahir = $data['data'][0]['ptempatlahir'];
                                                                                                                            $malamat = $data['data'][0]['palamat'];
                                                                                                                            $mkelurahan = $data['data'][0]['pekelurahan'];
                                                                                                                            $mkecamatan = $data['data'][0]['pkecamatan'];
                                                                                                                            $mwilayah = $data['data'][0]['pwilayah'];
                                                                                                                            $mtelp = $data['data'][0]['ptelp2'];
                                                                                                                            $mgoldarah = $data['data'][0]['pgoldarah'];
                                                                                                                            $mrhesus = $data['data'][0]['prhesus'];
                                                                                                                            $mmenikah = $data['data'][0]['pstatus'];
                                                                                                                            $mpekerjaan = $data['data'][0]['ppekerjaan'];
                                                                                                                            $mtglkembali = $data['data'][0]['ptglkembali'];
                                                                                                                            $mtglkembaliaph = $data['data'][0]['ptglkembaliapheresis'];
                                                                                                                            $mjumdonor = $data['data'][0]['pjmldonor'];
                                                                                                                            $mdonorke = $mjumdonor + 1;
                                                                                                                            $mtgl_lhr = $data['data'][0]['ptgllahir'];
                                                                                                                            $idudd = $udd['nama'];
                                                                                                                            $tahun = date('Y');
                                                                                                                            $mthnpd = substr($mtgl_lhr, 0, 4);
                                                                                                                            $umur = $tahun - $mthnpd;


                                                                                                                            $insertdonor = "insert into pendonor
                (`Kode`,`NoKTP`,`Nama`,`Alamat`,`Jk`,`Pekerjaan`,
                `telp`,`TempatLhr`,`TglLhr`,`Status`,`GolDarah`,
                `Rhesus`,`Call`,`kelurahan`,`kecamatan`,`wilayah`,`jumDonor`,`title`,
                `telp2`,`umur`,`tglkembali`,
                `pencatat`,`mu`,`cekal`,`up`,`waktu_update`,`tanggal_entry`,`apheresis`)
                values ('$mkodep','$mnoktp','$mnama','$malamat','$mjk','$mpekerjaan',
                '$mtelp','$mtmplahir','$mtgl_lhr','$mmenikah','$mgoldarah',
                '$mrhesus','1','$mkelurahan','$mkecamatan','$mwilayah','$mjumdonor','-',
                '$mtelp','$mumur','$mtglkembali',
                'test data','','0','','$sekarang','$sekarang','0')";
                                                                                                                            //echo $insertdonor;

                                                                                                                            $qinsertdonor = mysqli_query($dbi, $insertdonor);
                                                                                                                        }



                                                                                                                        //COMPARE PENDONOR DI LOKAL & NASIONAL --- END



                                                                                                                        //Shift Petugas
                                                                                                                        $shift = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama,jam,sampai_jam FROM `shift` WHERE time(now()) between time(jam) AND time(sampai_jam)"));
                                                                                                                        //$shif   = $shift['nama'];
                                                                                                                        if ($shift['nama'] == "I") {
                                                                                                                            $shif = "1";
                                                                                                                        } else if ($shift['nama'] == "II") {
                                                                                                                            $shif = "2";
                                                                                                                        } else if ($shift['nama'] == "III") {
                                                                                                                            $shif = "3";
                                                                                                                        } else {
                                                                                                                            $shif = "4";
                                                                                                                        }
                                                                                                                        //------------------------ set id transaksi ------------------------->

                                                                                                                        $idtp = mysqli_query($dbi, "select * from tempat_donor where active='1'");
                                                                                                                        $idtp1 = mysqli_fetch_assoc($idtp);
                                                                                                                        $th = substr(date("Y"), 2, 2);
                                                                                                                        $bl = date("m");
                                                                                                                        $tgl = date("d");
                                                                                                                        $kdtp = substr($idtp1['id1'], 0, 2) . $tgl . $bl . $th . "-" . $udd['id'] . "-";
                                                                                                                        $idp = mysqli_query($dbi, "select NoTrans from htransaksi where NoTrans like '$kdtp%' order by NoTrans DESC");
                                                                                                                        $idp1 = mysqli_fetch_assoc($idp);
                                                                                                                        $idp2 = substr($idp1['NoTrans'], 14, 4);
                                                                                                                        if ($idp2 < 1) {
                                                                                                                            $idp2 = "0000";
                                                                                                                        }
                                                                                                                        $idp3 = (int) $idp2 + 1;
                                                                                                                        $id31 = strlen($idp2) - strlen($idp3);
                                                                                                                        $idp4 = "";
                                                                                                                        for ($i = 0; $i < $id31; $i++) {
                                                                                                                            $idp4 .= "0";
                                                                                                                        }
                                                                                                                        $id_transaksi_baru = $kdtp . $idp4 . $idp3;
                                                                                                                        //------------------------ END set id transaksi ------------------------->
                                                                                                                        $v_notransaksi = $id_transaksi_baru;


                                                                                                                        if ($metode == "1") {
                                                                                                                            $aph = "0";
                                                                                                                            $tpk = "0";
                                                                                                                        } else {
                                                                                                                            $aph = "1";
                                                                                                                            $tpk = "0";
                                                                                                                        }
                                                                                                                        if ($mdonorke > 1) {
                                                                                                                            $donorbaru = '1';
                                                                                                                        } else {
                                                                                                                            $donorbaru = '0';
                                                                                                                        }
                                                                                                                        //if ($donorke == "1"){ $donorbaru = "0";}else{ $donorbaru = "1";}
                                                                                                                        if (substr($idtp1['id1'], 0, 1) == "M") {
                                                                                                                            $tempat = "M";
                                                                                                                            $rs1 = mysqli_fetch_assoc(mysqli_query($dbi, "select * from detailinstansi where aktif='1'"));
                                                                                                                            $namains = $rs1['nama'];
                                                                                                                        } else {
                                                                                                                            $tempat = "M";
                                                                                                                            $namains = "";
                                                                                                                        }

                                                                                                                        $q_htrans = "insert into htransaksi
        (NoTrans,KodePendonor,KodePendonor_lama,Tgl,Pengambilan,ketBatal,tempat,Instansi, JenisDonor,id_permintaan,Status,Nopol,apheresis,kendaraan,shift,kota,umur,donorbaru,jk, gol_darah,rhesus,pekerjaan,donorke,user,jam_mulai,rs, donor_tpk) value ('$v_notransaksi','$kodep','$kodep','$sekarang','-','-','0','$namains','$jenis_donor','','0','-','$aph','','$shif','$udd[id]','$umur','$donorbaru','$jk','$gol','$rh','$pekerjaan','$donorke','$namauser','$jam_donor','','$tpk')";
                                                                                                                        if (mysqli_query($dbi, $q_htrans)) {
                                                                                                                            //$msg .= '- Pendaftaran - berhasil<br>';
                                                                                                                            $lanjut = '0';
                                                                                                                        }

                                                                                                                        if ($lanjut == '0') {
                                                                                                                            //=======Audit Trial====================================================================================
                                                                                                                            $log_mdl = 'REGISTRASI';
                                                                                                                            $log_aksi = 'Registrasi: ' . $v_notransaksi . ' Donor: ' . $id . ' ' . $jenis_donor;
                                                                                                                            include_once "user_log.php";
                                                                                                                            //=====================================================================================================

                                                                                                                            /////=========> Antrian Donor
                                                                                                                            $antri = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT count(nomor) as nomor from `antrian` where tgl= curdate() limit 1"));
                                                                                                                            $no_antri = $antri['nomor'] + 1;

                                                                                                                            $q_antri = "INSERT INTO `antrian` (`transaksi`, `pendonor`, `nama`,`nomor`,`tgl`,`donorke`,`lengan`) VALUES ('$v_notransaksi', '$kodep','$nama','$no_antri','$today1','$donorke','$lengan')";
                                                                                                                            $antrikan = mysqli_query($dbi, $q_antri);



                                                                                                                            /////=========> Informed Consent
                                                                                                                            if ($tcetak == '1') {
                                                                                                                                //echo "Data Telah berhasil dimasukkan, isikan inform concent pendonor<br>"; 
                        ?>
                    <META http-equiv="refresh" content="1; url=Formulir23-st.php?kp=<?= $kodep ?>&trans=<?= $v_notransaksi ?>"><?php

                                                                                                                            } else { ?>
                    <script>
                        alert("Transaksi Donor berhasil disimpan");
                    </script>
                <?php

                                                                                                                            }
                                                                                                                        } else { ?>
                <script>
                    alert("Transaksi Donor Gagal disimpan");
                </script>
    <?php

                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                                //Transaksi Donor -- End

    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <br>

                <div class="panel with-nav-tabs panel-primary" id="shadow1">

                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="panel-title">
                                    <h4><strong>CARI DATA PENDONOR</strong></h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-lg-6">
                                <div class="panel-title pull-right">
                                    <button class="btn btn-sm btn-success  btn-round shadow" data-target="#MdlAdd"
                                        data-id="0" data-toggle="modal" type="button"><i class="icon wb-add-file"
                                            aria-hidden="true"></i>+ Pendonor Baru</button>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!--form cari start-->
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" onkeydown="return event.key != 'Enter';"
                            onsubmit="return validasiregistrasi()">
                            <div class="row" align="center">
                                <div class="col-lg-6">
                                    <!--row1--->
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Kode Pendonor</label>
                                        <div class="col-lg-8">
                                            <input type="text" name="kode" class="form-control" id="iddonor"
                                                autocomplete="off" placeholder="ID KARTU DONOR"
                                                onchange='disabletext(this.value);'>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">No. KTP</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="idktp" name="NoKTP" autocomplete="off"
                                                placeholder="No. KTP">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Nama Pendonor</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="nama" id="nama"
                                                autocomplete="off" placeholder="Nama Pendonor" minlength="3">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Alamat</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="alamat" autocomplete="off"
                                                placeholder="Alamat">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Kelurahan</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="kelurahan"
                                                placeholder="Kelurahan Alamat">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Kecamatan</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="kecamatan"
                                                placeholder="Kecamatan Alamat">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Wilayah</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="wilayah"
                                                placeholder="Kab / Kota">
                                        </div>
                                    </div>
                                    <!--row1--->
                                </div>
                                <div class="col-lg-6">
                                    <!--row2--->
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">ID UDD PMI</label>
                                        <div class="col-lg-2">
                                            <input type="text" class="form-control" name="udd" id="instansi"
                                                value="<?php echo $udd['id']; ?>" placeholder="Kab / Kota" required
                                                readonly>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="namauddpmi" id="instansi"
                                                value="<?php echo $udd['nama']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Tempat Lahir</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="tmplahir"
                                                placeholder="Tempat Lahir">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Tanggal Lahir</label>
                                        <div class="col-lg-2">
                                            <input type="text" class="form-control" name="tgl" id="tgl"
                                                autocomplete="off" placeholder="dd" size='2' maxlength="2">
                                        </div>
                                        <div class="col-lg-2">
                                            <input type="text" class="form-control" name="bln" id="bln"
                                                autocomplete="off" placeholder="mm" size='2' maxlength="2">
                                        </div>
                                        <div class="col-lg-2">
                                            <input type="text" class="form-control" name="thn" id="thn"
                                                autocomplete="off" placeholder="yyyy" size='4' maxlength="4">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">Golongan Darah</label>
                                        <div class="col-lg-2">
                                            <select class="form-control" name="goldarah">
                                                <option value="">SEMUA</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="O">O</option>
                                                <option value="AB">AB</option>
                                            </select>
                                        </div>
                                        <label class="control-label col-lg-1">Rhesus</label>
                                        <div class="col-lg-2">
                                            <select class="form-control" name="rhesus">
                                                <option value="">SEMUA</option>
                                                <option value="+">+</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">No. Handphone</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="telp" autocomplete="off"
                                                placeholder="Nomor Handphone">
                                        </div>
                                    </div>
                                    <!--row2--->
                                </div>
                            </div>
                    </div>
                    <!--form cari end-->

                    <div class="panel-footer">
                        <button name="cari" id="cari" type="submit" class="btn btn-danger"><i class="fa fa-search"></i>
                            Cari Data</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="loader" class="tengah"></div>

    <?php
    //Query Dinamis
    $nama = $_POST['nama'];
    $tgl = $_POST['tgl'];
    $bln = $_POST['bln'];
    $thn = $_POST['thn'];
    $tgllhr = $thn . "-" . $bln . "-" . $tgl;
    if ($_POST['kode'] != '') {
        $srckode = $_POST['kode'];
        $qkode = " AND Kode = '$srckode' ";
    } else {
        $qkode = "";
    }

    if ($_POST['NoKTP'] != '') {
        $srcktp = $_POST['NoKTP'];
        $qktp = " AND NoKTP = '$srcktp' ";
    } else {
        $qktp = "";
    }

    if ($_POST['alamat'] != '') {
        $srcalamat = $_POST['alamat'];
        $qalamat = " AND Alamat = '$srcalamat' ";
    } else {
        $qalamat = "";
    }

    if ($_POST['kelurahan'] != '') {
        $srckelurahan = $_POST['kelurahan'];
        $qkelurahan = " AND kelurahan = '$srckelurahan' ";
    } else {
        $qkelurahan = "";
    }

    if ($_POST['kecamatan'] != '') {
        $srckecamatan = $_POST['kecamatan'];
        $qkecamatan = " AND kecamatan = '$srckecamatan' ";
    } else {
        $qkecamatan = "";
    }

    if ($_POST['wilayah'] != '') {
        $srcwilayah = $_POST['wilayah'];
        $qwilayah = " AND wilayah = '$srcwilayah' ";
    } else {
        $qwilayah = "";
    }

    if ($_POST['udd'] != '') {
        $srcudd = $_POST['udd'];
        $qudd = " AND Kode = '$srcudd' ";
    } else {
        $qudd = "";
    }

    if ($_POST['tmplahir'] != '') {
        $srctmplahir = $_POST['tmplahir'];
        $qtmplahir = " AND TempatLhr = '$srctmplahir' ";
    } else {
        $qtmplahir = "";
    }

    if (($tgl != '') && ($bln != '') && ($thn != '')) {
        $srctgl = $tgllhr;
        $qtgl = " AND TglLhr = '$srctgl' ";
    } else {
        $qtgl = "";
    }

    if ($_POST['goldarah'] != '') {
        $srcgoldarah = $_POST['goldarah'];
        $qgoldarah = " AND GolDarah = '$srcgoldarah' ";
    } else {
        $qgoldarah = "";
    }

    if ($_POST['rhesus'] != '') {
        $srcrhesus = $_POST['rhesus'];
        $qrhesus = " AND Rhesus = '$srcrhesus' ";
    } else {
        $qrhesus = "";
    }

    if ($_POST['telp'] != '') {
        $srctelp = $_POST['telp'];
        $qtelp = " AND telp2 = '$srctelp' ";
    } else {
        $qtelp = "";
    }
    //Query dinamis end

    //Tombol Cari
    if (isset($_POST['cari'])) {
        //cek koneksi inet
        //if (cek_net() == true) {
        //CARI LOKAL
        $jd = "select * from pendonor where Nama like '%$nama%' $qkode $qktp $qalamat $qkelurahan $qkecamatan $qwilayah $qtmplahir $qtgl $qgoldarah $qrhesus $qtelp order by Nama asc";
        //echo $jd;
        $qjd = mysqli_query($dbi, $jd);
        $num = mysqli_num_rows($qjd);

        if ($num > 0) {
    ?>
            <div class="container-fluid">
                <div class="panel with-nav-tabs panel-primary" id="shadow1">
                    <div class="text-center" <h5><strong>DATA SERVER LOKAL</strong></h5>
                    </div>
                    <div class="table-responsive">
                        <table
                            class="table table-hover dataTable table-striped w-full table-bordered dt-responsive nowrap table-sm"
                            id="exampleTableSearch">
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
                            </tr>
                            <?php
                            $today = date('Y-m-d');
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
                                } else if (($data['Cekal'] == '0') and ($data['jumDOnor'] == '0')) {
                                    $imltd = "-";
                                } else {
                                    $imltd = "OK";
                                }

                            ?>
                                <tr <?php echo $style; ?> onMouseOver="this.className='highlight'"
                                    onMouseOut="this.className='normal'">
                                    <td align="center">
                                        <?php echo $data['Kode']; ?><br>

                                        <!-- Jika Wanita <4 & PRIA >4-->
                                        <?php if ($data['Jk'] == "1") {
                                            $tahun = date('Y');
                                            $jumtransaksiperempuan = mysqli_query($dbi, "select * from htransaksi where KodePendonor='$kode' and year(tgl)='$tahun'");
                                            if (date('Y-m-d') >= $data['tglkembali'] and ($data['Cekal'] == '0') and (mysqli_num_rows($jumtransaksiperempuan) < '4')) { ?>
                                                <a href="#" data-target="#MdlDonor" data-toggle="modal"
                                                    data-id="<?php echo $data['Kode'] . '*' . $data['Nama'] . '*' . $data['jumDonor'] . '*' . $data['GolDarah'] . '*' . $data['Rhesus'] . '*' . $data['TglLhr'] . '*' . $data['Pekerjaan'] . '*' . $data['Jk']; ?>"
                                                    title='Daftarkan donor' class="btn btn-icon btn-outline btn-danger btn-sm"><img
                                                        src="../images/bloodbag.png" width=15
                                                        height=15 /></a><?php }
                                                                } else if (date('Y-m-d') >= $data['tglkembali'] and ($data['Cekal'] == '0')) { ?>
                                            <a href="#" data-target="#MdlDonor" data-toggle="modal"
                                                data-id="<?php echo $data['Kode'] . '*' . $data['Nama'] . '*' . $data['jumDonor'] . '*' . $data['GolDarah'] . '*' . $data['Rhesus'] . '*' . $data['TglLhr'] . '*' . $data['Pekerjaan'] . '*' . $data['Jk']; ?>"
                                                title='Daftarkan donor' class="btn btn-icon btn-outline btn-danger btn-sm"><img
                                                    src="../images/bloodbag.png" width=15 height=15 /></a><?php } ?>

                                        <a href="#" data-target="#MdlEdit<?php echo $data['Kode']; ?>" data-toggle="modal"
                                            title='Edit data pendonor' class="btn btn-icon btn-outline btn-success btn-sm"> <img
                                                src="../images/ubah.png" width=15 height=15 /></a>
                                        <!-- Modal Edit-->
                                        <div class="modal modal-danger fade modal-fade-in-scale-up"
                                            id="MdlEdit<?php echo $data['Kode']; ?>" aria-hidden="true" aria-labelledby="MdlEdit"
                                            role="dialog" tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-simple modal-center">
                                                <div class="modal-content">
                                                    <div class="modal-header shadow">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close"><span aria-hidden="true">×</span></button>
                                                        <h4 class="modal-title" style="color:white">Edit Data Pendonor</h4>
                                                    </div>
                                                    <form class="form-horizontal" onkeydown="return event.key != 'Enter';" action=""
                                                        method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <!--start row-->
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">No. KTP</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="hidden" class="form-control" name="ekode"
                                                                                id="ekode" value="<?php echo $data['Kode']; ?>">

                                                                            <input type="text" class="form-control" name="enoktp"
                                                                                id="enoktp" value="<?php echo $data['NoKTP']; ?>"
                                                                                placeholder="No. KTP">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Nama Pendonor</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text" class="form-control" name="enama"
                                                                                id="enama" placeholder="Nama Pendonor"
                                                                                value="<?php echo $data['Nama']; ?>">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Jenis Kelamin</label>
                                                                        <?php
                                                                        $type = $data['Jk'];
                                                                        $checked[$type] = "checked";

                                                                        ?>
                                                                        <div class="col-lg-8" align="left">

                                                                            <label class="radio-inline"><input type="radio"
                                                                                    value="0" name="ejk" id="ejk0" <?php echo $checked["0"]; ?>
                                                                                    style="margin-top:1px;">Laki-laki</label>
                                                                            <label class="radio-inline"><input type="radio"
                                                                                    value="1" name="ejk" id="ejk1" <?php echo $checked["1"]; ?>
                                                                                    style="margin-top:1px;">Perempuan</label>
                                                                        </div>

                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Tempat Lahir</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text" class="form-control" name="etmplahir"
                                                                                id="etmplahir" placeholder="Tempat Lahir"
                                                                                value="<?php echo $data['TempatLhr']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                    $tglP = substr($data['TglLhr'], 8, 2);
                                                                    $blnP = substr($data['TglLhr'], 5, 2);
                                                                    $thnP = substr($data['TglLhr'], 0, 4);
                                                                    ?>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Tanggal Lahir</label>
                                                                        <div class="col-lg-8">
                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control"
                                                                                        name="etgl" id="etgl" placeholder="dd"
                                                                                        size='2' maxlength="2"
                                                                                        value="<?php echo $tglP; ?>">
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control"
                                                                                        name="ebln" id="ebln" placeholder="mm"
                                                                                        size='2' maxlength="2"
                                                                                        value="<?php echo $blnP; ?>">
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control"
                                                                                        name="ethn" id="ethn" placeholder="yyyy"
                                                                                        size='4' maxlength="4"
                                                                                        value="<?php echo $thnP; ?>">
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Alamat</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text" class="form-control" name="ealamat"
                                                                                id="ealamat" placeholder="Alamat"
                                                                                value="<?php echo $data['Alamat']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Kelurahan</label>
                                                                        <div class="col-md-8">
                                                                            <input type="text" class="form-control"
                                                                                name="ekelurahan" id="ekelurahan"
                                                                                placeholder="Kelurahan Alamat"
                                                                                value="<?php echo $data['kelurahan']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">

                                                                        <label class="control-label col-lg-4">Kecamatan</label>
                                                                        <div class="col-md-8">
                                                                            <input type="text" class="form-control"
                                                                                name="ekecamatan" id="ekecamatan"
                                                                                placeholder="Kecamatan Alamat"
                                                                                value="<?php echo $data['kecamatan']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end row-->
                                                                <!--start row-->
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Wilayah</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text" class="form-control" name="ewilayah"
                                                                                id="ewilayah" placeholder="Kab / Kota"
                                                                                value="<?php echo $data['wilayah']; ?>">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">No. Handphone</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text" class="form-control" name="etelp"
                                                                                id="etelp" placeholder="Nomor Handphone"
                                                                                value="<?php echo $data['telp2']; ?>">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Golongan Darah</label>
                                                                        <div class="col-lg-3">
                                                                            <select class="form-control" name="egoldarah"
                                                                                id="egoldarah">
                                                                                <option value="<?php echo $data['GolDarah']; ?>">
                                                                                    <?php echo $data['GolDarah']; ?></option>
                                                                                <option value="A">A</option>
                                                                                <option value="B">B</option>
                                                                                <option value="O">O</option>
                                                                                <option value="AB">AB</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Rhesus</label>
                                                                        <div class="col-lg-3">
                                                                            <select class="form-control" name="erhesus"
                                                                                id="erhesus">
                                                                                <option value="<?php echo $data['Rhesus']; ?>">
                                                                                    <?php echo $data['Rhesus']; ?></option>
                                                                                <option value="+">+</option>
                                                                                <option value="-">-</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <?php
                                                                    $nikah = $data['Status'];
                                                                    $checkedN[$nikah] = "checked";

                                                                    ?>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Status Menikah</label>
                                                                        <div class="col-lg-4" align="left">
                                                                            <div class="radio-custom radio-primary">
                                                                                <input type="radio" id="inputRadiosUnchecked"
                                                                                    value="0" name="emenikah" id="emenikah0" <?php echo $checkedN["0"]; ?>>
                                                                                <label for="inputRadiosUnchecked">Belum
                                                                                    Menikah</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4" align="left">
                                                                            <div class="radio-custom radio-primary">
                                                                                <input type="radio" id="inputRadiosUnchecked"
                                                                                    value="1" name="emenikah" id="emenikah1" <?php echo $checkedN["1"]; ?>>
                                                                                <label for="inputRadiosUnchecked">Sudah
                                                                                    Menikah</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Pekerjaan</label>
                                                                        <div class="col-lg-8">
                                                                            <select class="form-control" name="epekerjaan"
                                                                                id="epekerjaan" required>
                                                                                <option value="<?php echo $data['Pekerjaan']; ?>">
                                                                                    <?php echo $data['Pekerjaan']; ?></option>
                                                                                <?php
                                                                                //CARI Pekerjaan
                                                                                $result = $dbi->query("select * from pekerjaan");
                                                                                while ($row = $result->fetch_assoc()) { ?>
                                                                                    <option value="<?php echo $row['Nama']; ?>">
                                                                                        <?php echo $row['Nama']; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <?php
                                                                    $aph = $data['apheresis'];
                                                                    $checkedA[$aph] = "checked";

                                                                    if ($data['jumDonor'] > 4) { ?>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-lg-4">Bersedia
                                                                                Apheresis</label>

                                                                            <div class="col-lg-3">
                                                                                <div class="radio-custom radio-primary">
                                                                                    <input type="radio" id="inputRadiosUnchecked"
                                                                                        value="0" name="eaph" id="eaph0" <?php echo $checkedA["0"]; ?>>
                                                                                    <label for="inputRadiosUnchecked">Tidak</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div class="radio-custom radio-primary">
                                                                                    <input type="radio" id="inputRadiosUnchecked"
                                                                                        value="1" name="eaph" id="eaph1" <?php echo $checkedA["1"]; ?>>
                                                                                    <label for="inputRadiosUnchecked">Ya</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php }
                                                                    ?>


                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Jumlah Donor</label>
                                                                        <div class="col-md-4">
                                                                            <input type="number" class="form-control"
                                                                                name="ejumdonor" id="ejumdonor"
                                                                                value="<?php echo $data['jumDonor']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-lg-4">Tanggal Kembali
                                                                            Donor</label>
                                                                        <div class="col-md-4">
                                                                            <input type="text" class="form-control"
                                                                                name="etglkembali" id="etglkembali"
                                                                                value="<?php echo $data['tglkembali']; ?>">
                                                                        </div>
                                                                    </div>



                                                                </div>
                                                                <!--end row-->
                                                            </div>
                                                            <hr>
                                                            <?php
                                                            $now = date('Y-m-d');
                                                            $kembali = $data['tglkembali'];
                                                            if ($kembali <= $now) { ?>
                                                                <div class="row">
                                                                    <div class="col-lg-6">

                                                                        <div class="form-group">
                                                                            <label class="control-label col-lg-4">Daftar Donor ?</label>
                                                                            <div class="col-lg-8" align="left">
                                                                                <label class="radio-inline"><input type="radio"
                                                                                        value="1" name="edaftar" id="edaftar0"
                                                                                        style="margin-top:1px;">Ya</label>
                                                                                <label class="radio-inline"><input type="radio"
                                                                                        value="0" name="edaftar" id="edaftar0"
                                                                                        style="margin-top:1px;" checked>Tidak</label>

                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group row input-group-sm">
                                                                            <label class="control-label col-lg-4">Jenis Aftap</label>
                                                                            <div class="col-md-4">
                                                                                <select class="form-control" name="emetode">
                                                                                    <option value="1">Biasa</option>
                                                                                    <option value="2">Apheresis</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row input-group-sm">
                                                                            <label class="control-label col-lg-4">Jenis Donor</label>
                                                                            <div class="col-md-4">
                                                                                <select class="form-control" id="ejenis_donor"
                                                                                    name="ejenis_donor">
                                                                                    <option value="0">Sukarela</option>
                                                                                    <option value="1">Pengganti</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="No. Permintaan" id="eenorm"
                                                                                    name="enorm">
                                                                            </div>
                                                                        </div>


                                                                    </div><!--collg6-->
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group row input-group-sm">
                                                                            <label class="control-label col-lg-4">Lengan Donor</label>
                                                                            <div class="col-lg-8" align="left">
                                                                                <div class="radio-custom radio-primary">
                                                                                    <label class="radio-inline"><input type="radio"
                                                                                            id="inputRadiosUnchecked" value="0"
                                                                                            name="elengan" checked
                                                                                            style="margin-top:1px;">Keduanya</label>
                                                                                    <label class="radio-inline"><input type="radio"
                                                                                            id="inputRadiosUnchecked" value="1"
                                                                                            name="elengan"
                                                                                            style="margin-top:1px;">Kanan</label>
                                                                                    <label class="radio-inline"><input type="radio"
                                                                                            id="inputRadiosUnchecked" value="2"
                                                                                            name="elengan"
                                                                                            style="margin-top:1px;">Kiri</label>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row input-group-sm">
                                                                            <label class="control-label col-lg-4">Cetak Formulir</label>
                                                                            <div class="col-md-4" align="left">
                                                                                <label class="radio-inline"><input type="radio"
                                                                                        value="1" name="ecetak" id="edaftar0"
                                                                                        style="margin-top:1px;">Ya</label>
                                                                                <label class="radio-inline"><input type="radio"
                                                                                        value="0" name="ecetak" id="edaftar0"
                                                                                        style="margin-top:1px;" checked>Tidak</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div><!--collg6-->
                                                            <?php } else { ?>
                                                                <input type="hidden" value="0" name="edaftar">
                                                            <?php } ?>

                                                        </div>
                                                        <p>
                                                        <div class="modal-footer bg-grey-200">
                                                            <button type="button"
                                                                class="btn btn-primary btn-outline  btn-pill-left shadow"
                                                                data-dismiss="modal">Batal</button>
                                                            <button type="submit" name="editlokal"
                                                                class="btn btn-success btn-outline btn-pill-right shadow">Simpan</button>
                                                        </div>
                                                    </form>
                                                    <?php

                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Modal Edit-->
                                        <a href="#" data-target="#Mdlhistori<?php echo $data['Kode']; ?>" data-toggle="modal" title='Klik melihat histori donor'
                                            class="btn btn-icon btn-outline btn-warning btn-sm"> <img src="../images/health.png"
                                                width=15 height=15 /></a>
                                        <!-- Modal History Donor-->
                                        <div class="modal modal-danger fade modal-fade-in-scale-up" id="Mdlhistori<?php echo $data['Kode']; ?>"
                                            aria-hidden="true" aria-labelledby="Mdlhistori" role="dialog" tabindex="-1">

                                            <div class="modal-dialog modal-lg modal-simple modal-center">
                                                <div class="modal-content">
                                                    <div class="modal-header shadow">
                                                        <h4 class="modal-title" style="color:white">History Donor</h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <iframe
                                                            src="pmi<?php echo $_SESSION['leveluser']; ?>.php?module=history&q=<?php echo $data['Kode']; ?>"
                                                            width="800" height="480"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal History Donor END-->
                                        <a href="#" data-target="#Mdlbarcode<?php echo $data['Kode']; ?>" data-toggle="modal" title='Cetak kode donor'
                                            class="btn btn-icon btn-outline btn-info btn-sm"> <img src="../images/barcode.png"
                                                width=15 height=15 /></a>
                                        <!-- Modal Barcode Pendonor -->
                                        <div class="modal modal-danger fade modal-fade-in-scale-up" id="Mdlbarcode<?php echo $data['Kode']; ?>"
                                            aria-hidden="true" aria-labelledby="MdlDonor" role="dialog" tabindex="-1">
                                            <div class="modal-dialog modal-simple modal-center">
                                                <div class="modal-content">
                                                    <div class="modal-header shadow">
                                                        <h4 class="modal-title" style="color:white">Kode Pendonor</h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <iframe src="idcard_barcode.php?idpendonor=<?php echo $data['Kode']; ?>"
                                                            width="560" height="140"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal Barcode END-->

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
                                    <!-- kartu donor td align="center"><?php echo $data['cetak'] = $data['cetak'] . " kali</a> " ?></td-->

                                </tr>
                            <?php } ?>
                        </table>

                    </div>
                </div>
            </div><?php

                } //DARI NASIONAL
                else {
                    //Cari Nasional
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/caripendonor.php",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => array('kode' => $srckode, 'nama' => $nama, 'NoKTP' => $srcktp, 'alamat' => $srcalamat, 'kelurahan' => $srckelurahan, 'kecamatan' => $srckecamatan, 'wilayah' => $srcwilayah, 'tmplahir' => $srctmplahir, 'tgllhr' => $srctgl, 'goldarah' => $srcgoldarah, 'rhesus' => $srcrhesus, 'telp2' => $srctelp),
                    ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    //echo $response;
                    $tgl = date("Y/m/d");
                    $data = json_decode($response, true);
                    $jmlad = count($data['data']);
                    //echo "<pre>"; print_r($response); echo "</pre>";
                    //echo "jumlah data ====> ".$jmlad;
                    //JIKA ADA DARI NASIONAL
                    if ($jmlad > 0) { ?>
                <div class="container-fluid">
                    <div class="panel with-nav-tabs panel-primary" id="shadow1">
                        <div class="text-center" <h5><strong>DATA SERVER NASIONAL</strong></h5>
                        </div>
                        <div class="table-responsive">
                            <table
                                class="table table-hover dataTable table-striped w-full table-bordered dt-responsive nowrap table-sm"
                                id="exampleTableSearch">
                                <tr style="background-color:#FF6346;  color:#FFFFFF; font-family:Verdana;">
                                    <td align="center">Kode Pendonor</td>
                                    <td align="center">Registrasi UDD</td>
                                    <td align="center">Nama</td>
                                    <td align="center">Jenis Kelamin</td>
                                    <td align="center">Alamat</td>
                                    <td align="center">Gol Darah</td>
                                    <td align="center">Tempat<br>Tgl. Lahir</td>
                                    <td align="center">Telp/Hp</td>
                                    <td align="center">Jumlah Donor</td>
                                    <td align="center">Tanggal Kembali<br>Donor</td>
                                    <td align="center">IMLTD<br>Terakhir</td>
                                    <td align="center">Foto</td>
                                </tr>
                                <?php

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
                                        } else if (($data['data'][$a]['pcekal'] == '0') and ($data['data'][$a]['pjmldonor'] == '0')) {
                                            $imltd = "-";
                                        } else {
                                            $imltd = "OK";
                                        }
                                ?>
                                        <tr <?php echo $style; ?> onMouseOver="this.className='highlight'"
                                            onMouseOut="this.className='normal'">

                                            <td align="center"><?php echo $data['data'][$a]['pkode']; ?><br>
                                                <!-- Jika Wanita <4 & PRIA >4-->
                                                <?php if ($data['data'][$a]['pjk'] == "1") {
                                                    $tahun = date('Y');
                                                    $jumtransaksiperempuan = mysqli_query($dbi, "select * from htransaksi where KodePendonor='$kode' and year(tgl)='$tahun'");
                                                    if (date('Y-m-d') >= $data['data'][$a]['ptglkembali'] and ($data['data'][$a]['pcekal'] == '0') and (mysqli_num_rows($jumtransaksiperempuan) < '4')) { ?>
                                                        <a href="#" data-target="#MdlDonor" data-toggle="modal"
                                                            data-id="<?php echo $data['data'][$a]['pkode'] . '*' . $data['data'][$a]['pnama'] . '*' . $data['data'][$a]['pjmldonor'] . '*' . $data['data'][$a]['pgoldarah'] . '*' . $data['data'][$a]['prhesus'] . '*' . $data['data'][$a]['ptgllahir'] . '*' . $data['data'][$a]['ppekerjaaan'] . '*' . $data['data'][$a]['pjk']; ?>"
                                                            class="btn btn-icon btn-outline btn-danger btn-sm"><img src="../images/bloodbag.png"
                                                                width=15 height=15 /></a>
                                                    <?php }
                                                } else if (date('Y-m-d') >= $data['data'][$a]['ptglkembali'] and ($data['data'][$a]['pcekal'] == '0')) { ?>
                                                    <a href="#" data-target="#MdlDonor" data-toggle="modal"
                                                        data-id="<?php echo $data['data'][$a]['pkode'] . '*' . $data['data'][$a]['pnama'] . '*' . $data['data'][$a]['pjmldonor'] . '*' . $data['data'][$a]['pgoldarah'] . '*' . $data['data'][$a]['prhesus'] . '*' . $data['data'][$a]['ptgllahir'] . '*' . $data['data'][$a]['ppekerjaan'] . '*' . $data['data'][$a]['pjk']; ?>"
                                                        class="btn btn-icon btn-outline btn-danger btn-sm"><img src="../images/bloodbag.png"
                                                            width=15 height=15 /></a> <?php }
                                                                                    //echo '<a href="#" data-target="#MdlAdd" data-toggle="modal" data-id="'.implode('*',$row).'" class="btn btn-icon btn-outline btn-success btn-sm"> <i class="icon wb-edit" aria-hidden="true"></i></a>';
                                                                                        ?>
                                                <a href="#" data-target="#MdlEditnas<?php echo $data['data'][$a]['pkode']; ?>"
                                                    data-toggle="modal" class="btn btn-icon btn-outline btn-success btn-sm"> <img
                                                        src="../images/ubah.png" width=15 height=15 /></a>
                                                <!-- Modal Editnas-->
                                                <div class="modal modal-danger fade modal-fade-in-scale-up"
                                                    id="MdlEditnas<?php echo $data['data'][$a]['pkode']; ?>" aria-hidden="true"
                                                    aria-labelledby="MdlEditnas" role="dialog" tabindex="-1">
                                                    <div class="modal-dialog modal-lg modal-simple modal-center">
                                                        <div class="modal-content">
                                                            <div class="modal-header shadow">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                <h4 class="modal-title" style="color:white">Edit Data Pendonor</h4>
                                                            </div>
                                                            <form class="form-horizontal" onkeydown="return event.key != 'Enter';" action=""
                                                                method="POST" enctype="multipart/form-data">
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <!--start row-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">No. KTP</label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="hidden" class="form-control" name="nkode"
                                                                                        id="nkode"
                                                                                        value="<?php echo $data['data'][$a]['pkode']; ?>">

                                                                                    <input type="text" class="form-control" name="nnoktp"
                                                                                        id="nnoktp"
                                                                                        value="<?php echo $data['data'][$a]['pnoktp']; ?>"
                                                                                        placeholder="No. KTP">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Nama Pendonor</label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="text" class="form-control" name="nnama"
                                                                                        id="nnama" placeholder="Nama Pendonor"
                                                                                        value="<?php echo $data['data'][$a]['pnama']; ?>">
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Jenis Kelamin</label>
                                                                                <?php
                                                                                $type = $data['data'][$a]['pjk'];
                                                                                $checked[$type] = "checked";

                                                                                ?>
                                                                                <div class="col-lg-8" align="left">

                                                                                    <label class="radio-inline"><input type="radio"
                                                                                            value="0" name="njk" id="ejk0" <?php echo $checked["0"]; ?>
                                                                                            style="margin-top:1px;">Laki-laki</label>
                                                                                    <label class="radio-inline"><input type="radio"
                                                                                            value="1" name="njk" id="ejk1" <?php echo $checked["1"]; ?>
                                                                                            style="margin-top:1px;">Perempuan</label>
                                                                                </div>

                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Tempat Lahir</label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="text" class="form-control" name="ntmplahir"
                                                                                        id="etmplahir" placeholder="Tempat Lahir"
                                                                                        value="<?php echo $data['data'][$a]['ptempatlahir']; ?>">
                                                                                </div>
                                                                            </div>
                                                                            <?php
                                                                            $tglP = substr($data['data'][$a]['ptgllahir'], 8, 2);
                                                                            $blnP = substr($data['data'][$a]['ptgllahir'], 5, 2);
                                                                            $thnP = substr($data['data'][$a]['ptgllahir'], 0, 4);
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Tanggal Lahir</label>
                                                                                <div class="col-lg-8">
                                                                                    <div class="row">
                                                                                        <div class="col-md-4">
                                                                                            <input type="text" class="form-control"
                                                                                                name="ntgl" id="ntgl" placeholder="dd"
                                                                                                size='2' maxlength="2"
                                                                                                value="<?php echo $tglP; ?>">
                                                                                        </div>
                                                                                        <div class="col-md-4">
                                                                                            <input type="text" class="form-control"
                                                                                                name="nbln" id="nbln" placeholder="mm"
                                                                                                size='2' maxlength="2"
                                                                                                value="<?php echo $blnP; ?>">
                                                                                        </div>
                                                                                        <div class="col-md-4">
                                                                                            <input type="text" class="form-control"
                                                                                                name="nthn" id="nthn" placeholder="yyyy"
                                                                                                size='4' maxlength="4"
                                                                                                value="<?php echo $thnP; ?>">
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Alamat</label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="text" class="form-control" name="nalamat"
                                                                                        id="nalamat" placeholder="Alamat"
                                                                                        value="<?php echo $data['data'][$a]['palamat']; ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Kelurahan</label>
                                                                                <div class="col-md-8">
                                                                                    <input type="text" class="form-control"
                                                                                        name="nkelurahan" id="nkelurahan"
                                                                                        placeholder="Kelurahan Alamat"
                                                                                        value="<?php echo $data['data'][$a]['pkelurahan']; ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">

                                                                                <label class="control-label col-lg-4">Kecamatan</label>
                                                                                <div class="col-md-8">
                                                                                    <input type="text" class="form-control"
                                                                                        name="nkecamatan" id="nkecamatan"
                                                                                        placeholder="Kecamatan Alamat"
                                                                                        value="<?php echo $data['data'][$a]['pkecamatan']; ?>">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!--end row-->
                                                                        <!--start row-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Wilayah</label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="text" class="form-control" name="nwilayah"
                                                                                        id="nwilayah" placeholder="Kab / Kota"
                                                                                        value="<?php echo $data['data'][$a]['pwilayah']; ?>">
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">No. Handphone</label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="text" class="form-control" name="ntelp"
                                                                                        id="ntelp" placeholder="Nomor Handphone"
                                                                                        value="<?php echo $data['data'][$a]['ptelp2']; ?>">
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Golongan Darah</label>
                                                                                <div class="col-lg-3">
                                                                                    <select class="form-control" name="ngoldarah"
                                                                                        id="ngoldarah">
                                                                                        <option
                                                                                            value="<?php echo $data['data'][$a]['pgoldarah']; ?>">
                                                                                            <?php echo $data['data'][$a]['pgoldarah']; ?>
                                                                                        </option>
                                                                                        <option value="A">A</option>
                                                                                        <option value="B">B</option>
                                                                                        <option value="O">O</option>
                                                                                        <option value="AB">AB</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Rhesus</label>
                                                                                <div class="col-lg-3">
                                                                                    <select class="form-control" name="nrhesus"
                                                                                        id="nrhesus">
                                                                                        <option
                                                                                            value="<?php echo $data['data'][$a]['prhesus']; ?>">
                                                                                            <?php echo $data['data'][$a]['prhesus']; ?>
                                                                                        </option>
                                                                                        <option value="+">+</option>
                                                                                        <option value="-">-</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <?php
                                                                            $nikah = $data['data'][$a]['pstatus'];
                                                                            $checkedN[$nikah] = "checked";

                                                                            ?>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Status Menikah</label>
                                                                                <div class="col-lg-4" align="left">
                                                                                    <div class="radio-custom radio-primary">
                                                                                        <input type="radio" id="inputRadiosUnchecked"
                                                                                            value="0" name="nmenikah" id="nmenikah0" <?php echo $checkedN["0"]; ?>>
                                                                                        <label for="inputRadiosUnchecked">Belum
                                                                                            Menikah</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-4" align="left">
                                                                                    <div class="radio-custom radio-primary">
                                                                                        <input type="radio" id="inputRadiosUnchecked"
                                                                                            value="1" name="nmenikah" id="nmenikah1" <?php echo $checkedN["1"]; ?>>
                                                                                        <label for="inputRadiosUnchecked">Sudah
                                                                                            Menikah</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Pekerjaan</label>
                                                                                <div class="col-lg-8">
                                                                                    <select class="form-control" name="npekerjaan"
                                                                                        id="npekerjaan" required>
                                                                                        <option
                                                                                            value="<?php echo $data['data'][$a]['ppekerjaan']; ?>">
                                                                                            <?php echo $data['data'][$a]['ppekerjaan']; ?>
                                                                                        </option>
                                                                                        <?php
                                                                                        //CARI Pekerjaan
                                                                                        $result = $dbi->query("select * from pekerjaan");
                                                                                        while ($row = $result->fetch_assoc()) { ?>
                                                                                            <option value="<?php echo $row['Nama']; ?>">
                                                                                                <?php echo $row['Nama']; ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <?php
                                                                            $aph = $data['data'][$a]['papheresis'];
                                                                            $checkedA[$aph] = "checked";

                                                                            if ($data['data'][$a]['pjumdonor'] > 4) { ?>
                                                                                <div class="form-group">
                                                                                    <label class="control-label col-lg-4">Bersedia
                                                                                        Apheresis</label>

                                                                                    <div class="col-lg-3">
                                                                                        <div class="radio-custom radio-primary">
                                                                                            <input type="radio" id="inputRadiosUnchecked"
                                                                                                value="0" name="naph" id="naph0" <?php echo $checkedA["0"]; ?>>
                                                                                            <label for="inputRadiosUnchecked">Tidak</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-3">
                                                                                        <div class="radio-custom radio-primary">
                                                                                            <input type="radio" id="inputRadiosUnchecked"
                                                                                                value="1" name="naph" id="naph1" <?php echo $checkedA["1"]; ?>>
                                                                                            <label for="inputRadiosUnchecked">Ya</label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php }
                                                                            ?>


                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Jumlah Donor</label>
                                                                                <div class="col-md-4">
                                                                                    <input type="number" class="form-control"
                                                                                        name="njumdonor" id="ejumdonor"
                                                                                        value="<?php echo $data['data'][$a]['pjmldonor']; ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-lg-4">Tanggal Kembali
                                                                                    Donor</label>
                                                                                <div class="col-md-4">
                                                                                    <input type="text" class="form-control"
                                                                                        name="ntglkembali" id="ntglkembali"
                                                                                        value="<?php echo $data['data'][$a]['ptglkembali']; ?>">
                                                                                </div>
                                                                            </div>



                                                                        </div>
                                                                        <!--end row-->
                                                                    </div>

                                                                    <?php
                                                                    $now = date('Y-m-d');
                                                                    $kembali = $data['data'][$a]['ptglkembali'];
                                                                    if ($kembali <= $now) { ?>
                                                                        <hr>
                                                                        <div class="row">
                                                                            <div class="col-lg-6">

                                                                                <div class="form-group">
                                                                                    <label class="control-label col-lg-4">Daftar Donor ?</label>
                                                                                    <div class="col-lg-8" align="left">
                                                                                        <label class="radio-inline"><input type="radio"
                                                                                                value="1" name="ndaftar" id="ndaftar0"
                                                                                                style="margin-top:1px;">Ya</label>
                                                                                        <label class="radio-inline"><input type="radio"
                                                                                                value="0" name="ndaftar" id="ndaftar0"
                                                                                                style="margin-top:1px;" checked>Tidak</label>

                                                                                    </div>

                                                                                </div>
                                                                                <div class="form-group row input-group-sm">
                                                                                    <label class="control-label col-lg-4">Jenis Aftap</label>
                                                                                    <div class="col-md-4">
                                                                                        <select class="form-control" name="nmetode">
                                                                                            <option value="1">Biasa</option>
                                                                                            <option value="2">Apheresis</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row input-group-sm">
                                                                                    <label class="control-label col-lg-4">Jenis Donor</label>
                                                                                    <div class="col-md-4">
                                                                                        <select class="form-control" id="njenis_donor"
                                                                                            name="njenis_donor">
                                                                                            <option value="0">Sukarela</option>
                                                                                            <option value="1">Pengganti</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-md-4">
                                                                                        <input type="text" class="form-control"
                                                                                            placeholder="No. Permintaan" id="eenorm"
                                                                                            name="enorm">
                                                                                    </div>
                                                                                </div>


                                                                            </div><!--collg6-->
                                                                            <div class="col-lg-6">
                                                                                <div class="form-group row input-group-sm">
                                                                                    <label class="control-label col-lg-4">Lengan Donor</label>
                                                                                    <div class="col-lg-8" align="left">
                                                                                        <div class="radio-custom radio-primary">
                                                                                            <label class="radio-inline"><input type="radio"
                                                                                                    id="inputRadiosUnchecked" value="0"
                                                                                                    name="elengan" checked
                                                                                                    style="margin-top:1px;">Keduanya</label>
                                                                                            <label class="radio-inline"><input type="radio"
                                                                                                    id="inputRadiosUnchecked" value="1"
                                                                                                    name="elengan"
                                                                                                    style="margin-top:1px;">Kanan</label>
                                                                                            <label class="radio-inline"><input type="radio"
                                                                                                    id="inputRadiosUnchecked" value="2"
                                                                                                    name="elengan"
                                                                                                    style="margin-top:1px;">Kiri</label>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row input-group-sm">
                                                                                    <label class="control-label col-lg-4">Cetak Formulir</label>
                                                                                    <div class="col-md-4" align="left">
                                                                                        <label class="radio-inline"><input type="radio"
                                                                                                value="1" name="ncetak" id="ndaftar0"
                                                                                                style="margin-top:1px;">Ya</label>
                                                                                        <label class="radio-inline"><input type="radio"
                                                                                                value="0" name="ncetak" id="ndaftar0"
                                                                                                style="margin-top:1px;" checked>Tidak</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div><!--collg6-->
                                                                    <?php } else { ?>
                                                                        <input type="hidden" value="0" name="ndaftar">
                                                                    <?php } ?>

                                                                </div>
                                                                <p>
                                                                <div class="modal-footer bg-grey-200">
                                                                    <button type="button"
                                                                        class="btn btn-primary btn-outline  btn-pill-left shadow"
                                                                        data-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="editnasional"
                                                                        class="btn btn-success btn-outline btn-pill-right shadow">Simpan</button>
                                                                </div>
                                                            </form>
                                                            <?php

                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Modal Edit-->

                                                <!-- Modal History Donor-->
                                                <a href="#" data-target="#Mdlhistorinas<?php echo $data['data'][$a]['pkode']; ?>"
                                                    data-toggle="modal" title='Klik melihat histori donor'
                                                    class="btn btn-icon btn-outline btn-warning btn-sm"> <img src="../images/health.png"
                                                        width=15 height=15 /></a>
                                                <!-- Modal History Donor-->
                                                <div class="modal modal-danger fade modal-fade-in-scale-up"
                                                    id="Mdlhistorinas<?php echo $data['data'][$a]['pkode']; ?>" aria-hidden="true"
                                                    aria-labelledby="Mdlhistorinas" role="dialog" tabindex="-1">
                                                    <div class="modal-dialog modal-lg modal-simple modal-center">
                                                        <div class="modal-content">
                                                            <div class="modal-header shadow">
                                                                <h4 class="modal-title" style="color:white">History Donor</h4>
                                                            </div>

                                                            <div class="modal-body">
                                                                <iframe
                                                                    src="pmi<?php echo $_SESSION['leveluser']; ?>.php?module=historynas&q=<?php echo $data['data'][$a]['pkode']; ?>"
                                                                    width="800" height="480"></iframe>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal History Donor END-->

                                                <!-- Modal Barcode-->
                                                <a href="#" data-target="#Mdlbarcodenas<?php echo $data['data'][$a]['pkode']; ?>"
                                                    data-toggle="modal" class="btn btn-icon btn-outline btn-info btn-sm"> <img
                                                        src="../images/barcode.png" width=15 height=15 /></a>
                                                <!-- Modal Barcode Pendonor -->
                                                <div class="modal modal-danger fade modal-fade-in-scale-up"
                                                    id="Mdlbarcodenas<?php echo $data['data'][$a]['pkode']; ?>" aria-hidden="true"
                                                    aria-labelledby="MdlDonor" role="dialog" tabindex="-1">
                                                    <div class="modal-dialog modal-simple modal-center">
                                                        <div class="modal-content">
                                                            <div class="modal-header shadow">
                                                                <h4 class="modal-title" style="color:white">Kode Pendonor</h4>
                                                            </div>

                                                            <div class="modal-body">
                                                                <iframe
                                                                    src="idcard_barcodenas.php?idpendonor=<?php echo $data['data'][$a]['pkode']; ?>"
                                                                    width="560" height="140"></iframe>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal Barcode END-->

                                            </td>
                                            <td align="left"><?php echo $data['data'][$a]['namaudd']; ?></td>
                                            <td align="center"><?php echo $data['data'][$a]['pnama']; ?></td>
                                            <td align="center"><?php echo $jeniskelamin; ?></td>
                                            <td align="center">
                                                <?php echo $data['data'][$a]['palamat'] . " <br>" . $data['data'][$a]['pkelurahan'] . " " . $data['data'][$a]['pkecamatan'] . " " . $data['data'][$a]['pwilayah']; ?>
                                            </td>
                                            <td align="center">
                                                <?php echo $data['data'][$a]['pgoldarah'] . " (" . $data['data'][$a]['prhesus'] . ")"; ?></td>
                                            <td align="center">
                                                <?php echo $data['data'][$a]['ptempatlahir'] . ", <br>" . $data['data'][$a]['ptgllahir']; ?></td>
                                            <td align="center"><?php echo $data['data'][$a]['ptelp2']; ?></td>
                                            <td align="center"><?php echo $data['data'][$a]['pjmldonor']; ?> kali</td>
                                            <td align="center"><?php echo $data['data'][$a]['ptglkembali']; ?></td>
                                            <td align="center"><?php echo $imltd; ?></td>
                                            <td align="center" width="10%"><img class="img-hover-zoom--slowmo"
                                                    src="https://dbdonor.pmi.or.id/pmi/image/<?php echo $data['data'][$a]['userfoto']; ?>"
                                                    style="max-width: 40%; height: auto;"></td>



                                    <?php }
                                }
                                if ($no == '0') {
                                    echo '<tr>';
                                    echo '<td colspan="16" style="font-size:20px;" class="text-center">Tidak ada data Pendonor Nasional</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody>
                                    </table>';
                                // TOMBOL SERVER NASIONAL END
                                    ?>
                        </div>
                    </div>
                </div>
    <?php





                    }
                    //}//cek net
                    //else{ echo "internet DOWN <br>"; }
                }
            }
            //POST END
    ?>



    <!-- Modal Transaksi Donor -->
    <div class="modal modal-danger fade modal-fade-in-scale-up" id="MdlDonor" aria-hidden="true"
        aria-labelledby="MdlDonor" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header shadow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title" style="color:white">Transaksi Donor</h4>
                </div>
                <form class="form-horizontal" action="" onkeydown="return event.key != 'Enter';" method="POST"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row mt-5 input-group-sm">
                                    <label class="col-md-3 form-control-label">Kode Pendonor</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm" id="MInpKode"
                                            name="MInpKode" autocomplete="off" required minlength="3" maxlength="20"
                                            style="text-transform: uppercase" readonly />
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control form-control-sm" id="MInpNama"
                                            name="MInpNama" autocomplete="off" required minlength="3" maxlength="50"
                                            readonly />
                                        <input type="hidden" id="Mjmld" name="tjmld" />
                                        <input type="hidden" id="Mgol" name="tgol" />
                                        <input type="hidden" id="Mrh" name="jrh" />
                                        <input type="hidden" id="Mtgllhr" name="jtgllhr" />
                                        <input type="hidden" id="Mkerja" name="jkerja" />
                                        <input type="hidden" id="Mjk" name="jjk" />
                                    </div>
                                </div>
                                <div class="form-group row input-group-sm">
                                    <label class="col-md-3 form-control-label">Jenis Aftap</label>
                                    <div class="col-md-4">
                                        <select class="form-control" name="tmetode">
                                            <option value="1">Biasa</option>
                                            <option value="2">Apheresis</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row input-group-sm">
                                    <label class="col-md-3 form-control-label">Jenis Donor</label>
                                    <div class="col-md-4">
                                        <select class="form-control" id="tjenis_donor" name="tjenis_donor">
                                            <option value="0">Sukarela</option>
                                            <option value="1">Pengganti</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" placeholder="No. Permintaan" id="tnorm"
                                            name="tnorm">
                                    </div>
                                </div>
                                <div class="form-group row input-group-sm">
                                    <label class="col-md-3 form-control-label">Lengan Donor</label>
                                    <div class="col-lg-8">
                                        <div class="radio-custom radio-primary">
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="0" name="tlengan" checked
                                                    style="margin-top:1px;">Keduanya</label>
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="1" name="tlengan" style="margin-top:1px;">Kanan</label>
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="2" name="tlengan" style="margin-top:1px;">Kiri</label>

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row input-group-sm">
                                    <label class="col-md-3 form-control-label">Cetak Formulir</label>
                                    <div class="col-md-4">
                                        <label class="radio-inline"><input type="radio" value="1" name="tcetak"
                                                id="edaftar0" style="margin-top:1px;">Ya</label>
                                        <label class="radio-inline"><input type="radio" value="0" name="tcetak"
                                                id="edaftar0" style="margin-top:1px;" checked>Tidak</label>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-grey-200">
                        <button type="button" class="btn btn-danger btn-outline  btn-pill-left shadow"
                            data-dismiss="modal">Batal</button>
                        <button type="submit" name="btndonor"
                            class="btn btn-success btn-outline btn-pill-right shadow">Lanjut</button>

                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- Modal Transaksi Donor END-->

    <!-- Modal Donor Baru-->
    <div class="modal modal-danger fade modal-fade-in-scale-up" id="MdlAdd" aria-hidden="true" aria-labelledby="MdlAdd"
        role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header shadow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title" style="color:white">Data Pendonor Baru</h4>
                </div>
                <form class="form-horizontal" onkeydown="return event.key != 'Enter';" action="" method="POST"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <!--start row-->
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label class="control-label col-lg-4">No. KTP</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="mnoktp" id="mnoktp" maxlength="16"
                                            minlength="16" placeholder="No. KTP" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Nama Pendonor</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="mnama" id="nama" minlength="4"
                                            placeholder="Nama Pendonor" minlength="3" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-4">Jenis Kelamin</label>
                                    <div class="col-lg-8" align="left">
                                        <div class="radio-custom radio-primary">
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="0" name="mjk" style="margin-top:1px;">Laki-laki</label>
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="1" name="mjk" style="margin-top:1px;">Perempuan</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-4">Tempat Lahir</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="mtmplahir"
                                            placeholder="Tempat Lahir" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Tanggal Lahir</label>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" name="mtgl" placeholder="dd"
                                                    size='2' maxlength="2" required>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" name="mbln" placeholder="mm"
                                                    size='2' maxlength="2" required>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" name="mthn" placeholder="yyyy"
                                                    size='4' maxlength="4" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Alamat</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="malamat" placeholder="Alamat"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Kelurahan</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="mkelurahan"
                                            placeholder="Kelurahan Alamat" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Kecamatan</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="mkecamatan"
                                            placeholder="Kecamatan Alamat" required>
                                    </div>
                                </div>
                            </div>
                            <!--end row-->
                            <!--start row-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-lg-4">Wilayah</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="mwilayah" placeholder="Kab / Kota"
                                            required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-4">No. Handphone</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="mtelp"
                                            placeholder="Nomor Handphone" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-4">Golongan Darah</label>
                                    <div class="col-lg-8">
                                        <select class="form-control" name="mgoldarah" required>
                                            <option value="X">X</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="O">O</option>
                                            <option value="AB">AB</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-4">Rhesus</label>
                                    <div class="col-lg-8">
                                        <select class="form-control" name="mrhesus" required>
                                            <option value="+">+</option>
                                            <option value="-">-</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-4">Status Menikah</label>
                                    <div class="col-lg-8">
                                        <div class="radio-custom radio-primary">
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="0" name="mmenikah" style="margin-top:1px;">Belum
                                                Menikah</label>
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="1" name="mmenikah" style="margin-top:1px;">Sudah
                                                Menikah</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-4">Pekerjaan</label>
                                    <div class="col-lg-8">
                                        <select class="form-control" name="mpekerjaan" required>
                                            <option value="">++ pilih ++</option>
                                            <?php
                                            //CARI Pekerjaan
                                            $result = $dbi->query("select * from pekerjaan");
                                            while ($row = $result->fetch_assoc()) { ?>
                                                <option value="<?php echo $row['Nama']; ?>"><?php echo $row['Nama']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-4">Jumlah Donor</label>
                                    <div class="col-lg-4">
                                        <input type="number" class="form-control" value="0" name="mjumdonor">
                                    </div>
                                </div>
                            </div>
                            <!--end row-->
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">

                                <div class="form-group">
                                    <label class="control-label col-lg-4">Daftar Donor ?</label>
                                    <div class="col-lg-8" align="left">
                                        <label class="radio-inline"><input type="radio" value="1" name="mdaftar"
                                                id="mdaftar0" style="margin-top:1px;">Ya</label>
                                        <label class="radio-inline"><input type="radio" value="0" name="mdaftar"
                                                id="mdaftar0" style="margin-top:1px;" checked>Tidak</label>
                                    </div>
                                </div>
                                <div class="form-group row input-group-sm">
                                    <label class="control-label col-lg-4">Jenis Aftap</label>
                                    <div class="col-md-4">
                                        <select class="form-control" name="mmetode">
                                            <option value="1">Biasa</option>
                                            <option value="2">Apheresis</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row input-group-sm">
                                    <label class="control-label col-lg-4">Jenis Donor</label>
                                    <div class="col-md-4">
                                        <select class="form-control" id="mjenis_donor" name="mjenis_donor">
                                            <option value="0">Sukarela</option>
                                            <option value="1">Pengganti</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" placeholder="No. Permintaan" id="mnorm"
                                            name="mnorm">
                                    </div>
                                </div>


                            </div><!--collg6-->
                            <div class="col-lg-6">
                                <div class="form-group row input-group-sm">
                                    <label class="control-label col-lg-4">Lengan Donor</label>
                                    <div class="col-lg-8" align="left">
                                        <div class="radio-custom radio-primary">
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="0" name="mlengan" checked
                                                    style="margin-top:1px;">Keduanya</label>
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="1" name="mlengan" style="margin-top:1px;">Kanan</label>
                                            <label class="radio-inline"><input type="radio" id="inputRadiosUnchecked"
                                                    value="2" name="mlengan" style="margin-top:1px;">Kiri</label>

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row input-group-sm">
                                    <label class="control-label col-lg-4">Cetak Formulir</label>
                                    <div class="col-md-4" align="left">
                                        <label class="radio-inline"><input type="radio" value="1" name="mcetak"
                                                id="mdaftar0" style="margin-top:1px;">Ya</label>
                                        <label class="radio-inline"><input type="radio" value="0" name="mcetak"
                                                id="mdaftar0" style="margin-top:1px;" checked>Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div><!--ROW END-->



                        <p>
                        <div class="modal-footer bg-grey-200">
                            <button type="button" class="btn btn-primary btn-outline  btn-pill-left shadow"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" name="tambah"
                                class="btn btn-success btn-outline btn-pill-right shadow">Simpan</button>
                        </div>
                </form>

            </div>
        </div>
    </div>
    <!-- End Donor Baru-->




</body>

</html>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        $(".loader").fadeOut();
        if (document.getElementById('cari').clicked == true) {
            document.getElementById("cari").disabled = true;
        }
    });
    $("#MdlDonor").on('show.bs.modal', function(e) {
        var id = $(e.relatedTarget).attr('data-id');
        var x = document.getElementById("tnorm");
        x.disabled = true;
        x.setAttribute("type", "hidden");

        $('#MdlDonor .modal-title').html('Transaksi Donor');
        var ide = id.split('*');
        $('#MdlAdd #MInpKode').attr('readonly', true);
        $('#MdlDonor #MInpKode').val(ide[0]);
        $('#MdlDonor #MInpNama').val(ide[1]);
        $('#MdlDonor #Mjmld').val(ide[2]);
        $('#MdlDonor #Mgol').val(ide[3]);
        $('#MdlDonor #Mrh').val(ide[4]);
        $('#MdlDonor #Mtgllhr').val(ide[5]);
        $('#MdlDonor #Mkerja').val(ide[6]);
        $('#MdlDonor #Mjk').val(ide[7]);


        $('#tjenis_donor').on('change', function() {
            var jdonor = $("#tjenis_donor option:selected").val();
            if (jdonor == '1') {
                x.disabled = false;
                x.setAttribute("type", "text");
            } else {
                x.disabled = true;
            }
        });
    });

    $("#MdlAdd").on('show.bs.modal', function(e) {
        var s = document.getElementById("mnorm");
        s.disabled = true;
        s.setAttribute("type", "hidden");
        $('#mjenis_donor').on('change', function() {
            var jdonor = $("#mjenis_donor option:selected").val();
            if (jdonor == '1') {
                s.disabled = false;
                s.setAttribute("type", "text");
            } else {
                s.disabled = true;
            }
        });
    });


    $("#MdlEdit").on('show.bs.modal', function(e) {
        var e = document.getElementById("enorm");
        e.disabled = true;
        e.setAttribute("type", "hidden");
        $('#ejenis_donor').on('change', function() {
            var jdonor = $("#ejenis_donor option:selected").val();
            if (jdonor == '1') {
                e.disabled = false;
                e.setAttribute("type", "text");
            } else {
                e.disabled = true;
            }
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
        if (document.getElementById('idktp').value && document.getElementById('idktp').value.length < 16) {
            alert('Masukan No. KTP minimal 16 Karakter !');
            document.getElementById("idktp").focus();
            return false;
        }
        if (document.getElementById('iddonor').value == '' && document.getElementById('idktp').value == '') {
            if (document.getElementById('nama').value.length < 3) {
                alert(' Lengkapi Nama minimal 3 Karakter !');
                document.getElementById("nama").focus();
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
    }
</script>