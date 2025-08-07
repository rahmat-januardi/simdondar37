<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem InforMasi DONor DARah</title>
    <link href="bootsrap337/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootsrap337/js/html5shiv.min.js"></script>
    <script src="bootsrap337/js/respond.min.js"></script>
    <link href="bootsrap337/bspmi.css" rel="stylesheet">
    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="bootsrap337/datepicker/css/bootstrap-datepicker.min.css">
    <script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="bootsrap337/datepicker/locales/bootstrap-datepicker.id.min.js"></script>
    <script type="text/javascript" src="tpk/medical_checkup.js"></script>
</head>
<?php
include ('config/dbi_connect.php');
include ('clogin.php');
$q_udd=mysqli_fetch_assoc(mysqli_query($dbi,"select * from utd where aktif='1'"));
$zona_waktu=$q_udd['zonawaktu'];
date_default_timezone_set($zona_waktu);
$namaudd=$_SESSION['namaudd'];
$lv5=$_SESSION['leveluser'];
$tempat = mysqli_fetch_assoc(mysqli_query($dbi,"select * from tempat_donor where active='1'"));
$shift = mysqli_fetch_assoc(mysqli_query($dbi,"SELECT nama,jam,sampai_jam FROM `shift` WHERE (time(jam)<=current_time and time(sampai_jam)>=current_time)"));
$shif = $shift['nama'];
$idp1 = mysqli_fetch_assoc($tempat); (substr($idp1['id1'],0,1)=="M") ? $mu="1" : $mu="";
$msg='';
if (isset($_POST['simpan'])) {
    $v_notransaksi = $_POST['notransaksi'];
    $v_kodedonor   = $_POST['kodedonor'];
    $v_donortpk    = $_POST['reqdoonortpk'];
    $v_goldarah    = $_POST['goldarah'];
    $v_rhesus      = $_POST['rhesus'];
    $v_goldarah_a  = $_POST['abo_pendonor'];
    $v_rhesus_a    = $_POST['rh_pendonor'];
    $v_bb          = $_POST['reqberat_badan'];
    $v_tb          = $_POST['tinggi_badan'];
    $v_sistol      = $_POST['reqtensi_sistol'];
    $v_diastol     = $_POST['reqtensi_diastol'];
    $v_tensi       = $v_sistol.'/'.$v_diastol;
    $v_suhu        = $_POST['reqtemperatur'];
    $v_nadi        = $_POST['reqnadi'];
    $v_oksigen     = $_POST['oksigen'];
    $v_ptgdokter   = $_POST['id_dokter'];
    $v_ptgtensi    = $_POST['id_tensi'];
    $v_ptgshb      = $_POST['id_hb'];
    $v_lolos       = $_POST['h_medical'];
    $v_alasan      = $_POST['alasan'];
    $v_no1         = $_POST['no1'];if ($v_no1=='0'){$av_no1="TIDAK";}else{$av_no1="YA";}
    $v_no2         = $_POST['no2'];if ($v_no2=='0'){$av_no2="TIDAK";}else{$av_no2="YA";}
    $v_no3         = $_POST['no3'];if ($v_no3=='0'){$av_no3="TIDAK";}else{$av_no3="YA";}
    $v_no4         = $_POST['no4'];if ($v_no4=='0'){$av_no4="TIDAK";}else{$av_no4="YA";}
    $v_no5         = $_POST['no5'];if ($v_no5=='0'){$av_no5="TIDAK";}else{$av_no5="YA";}
    $v_no6         = $_POST['no6'];if ($v_no6=='0'){$av_no6="TIDAK";}else{$av_no6="YA";}
    $v_no7         = $_POST['no7'];if ($v_no7=='0'){$av_no7="TIDAK";}else{$av_no7="YA";}
    $v_no8         = $_POST['no8'];if ($v_no8=='0'){$av_no8="TIDAK";}else{$av_no8="YA";}
    $v_no9         = $_POST['no9'];if ($v_no9=='0'){$av_no9="TIDAK";}else{$av_no9="YA";}
    $v_no10        = $_POST['no10'];if ($v_no10=='0'){$av_no10="TIDAK";}else{$av_no10="YA";}
    $v_no11        = $_POST['no11'];if ($v_no11=='0'){$av_no11="TIDAK";}else{$av_no11="YA";}
    $v_no12        = $_POST['no12'];if ($v_no12=='0'){$av_no12="TIDAK";}else{$av_no12="YA";}
    $v_no13        = $_POST['no13'];if ($v_no13=='0'){$av_no13="TIDAK";}else{$av_no13="YA";}
    $v_no14        = $_POST['no14'];if ($v_no14=='0'){$av_no14="TIDAK";}else{$av_no14="YA";}
    $v_no15        = $_POST['no15'];if ($v_no15=='0'){$av_no15="TIDAK";}else{$av_no15="YA";}
    $v_no16        = $_POST['no16'];if ($v_no16=='0'){$av_no16="TIDAK";}else{$av_no16="YA";}
    $v_no17        = $_POST['no17'];if ($v_no17=='0'){$av_no17="TIDAK";}else{$av_no17="YA";}
    $v_no18        = $_POST['no18'];if ($v_no18=='0'){$av_no18="TIDAK";}else{$av_no18="YA";}
    $v_no19        = $_POST['no19'];if ($v_no19=='0'){$av_no19="TIDAK";}else{$av_no19="YA";}
    $v_no20        = $_POST['no20'];if ($v_no20=='0'){$av_no20="TIDAK";}else{$av_no20="YA";}
    $v_no21        = $_POST['no21'];if ($v_no21=='0'){$av_no21="TIDAK";}else{$av_no21="YA";}
    $v_no22        = $_POST['no22'];if ($v_no22=='0'){$av_no22="TIDAK";}else{$av_no22="YA";}
    $v_no23        = $_POST['no23'];if ($v_no23=='0'){$av_no23="TIDAK";}else{$av_no23="YA";}
    $v_no24        = $_POST['no24'];if ($v_no24=='0'){$av_no24="TIDAK";}else{$av_no24="YA";}
    $v_no25        = $_POST['no25'];if ($v_no25=='0'){$av_no25="TIDAK";}else{$av_no25="YA";}
    $v_no26        = $_POST['no26'];if ($v_no26=='0'){$av_no26="TIDAK";}else{$av_no26="YA";}
    $v_no27        = $_POST['no27'];if ($v_no27=='0'){$av_no27="TIDAK";}else{$av_no27="YA";}
    $v_no28        = $_POST['no28'];if ($v_no28=='0'){$av_no28="TIDAK";}else{$av_no28="YA";}
    $v_no29        = $_POST['no29'];if ($v_no29=='0'){$av_no29="TIDAK";}else{$av_no29="YA";}
    $v_no30        = $_POST['no30'];if ($v_no30=='0'){$av_no30="TIDAK";}else{$av_no30="YA";}
    $v_no31        = $_POST['no31'];if ($v_no31=='0'){$av_no31="TIDAK";}else{$av_no31="YA";}
    $v_no32        = $_POST['no32'];if ($v_no32=='0'){$av_no32="TIDAK";}else{$av_no32="YA";}
    $v_no33        = $_POST['no33'];if ($v_no33=='0'){$av_no33="TIDAK";}else{$av_no33="YA";}
    $v_no34        = $_POST['no34'];if ($v_no34=='0'){$av_no34="TIDAK";}else{$av_no34="YA";}
    $v_no35        = $_POST['no35'];if ($v_no35=='0'){$av_no35="TIDAK";}else{$av_no35="YA";}
    $v_no36        = $_POST['no36'];if ($v_no36=='0'){$av_no36="TIDAK";}else{$av_no36="YA";}
    $v_no37        = $_POST['no37'];if ($v_no37=='0'){$av_no37="TIDAK";}else{$av_no37="YA";}
    $v_no38        = $_POST['no38'];if ($v_no38=='0'){$av_no38="TIDAK";}else{$av_no38="YA";}
    $v_no39        = $_POST['no39'];if ($v_no39=='0'){$av_no39="TIDAK";}else{$av_no39="YA";}
    $v_no40        = $_POST['no40'];if ($v_no40=='0'){$av_no40="TIDAK";}else{$av_no40="YA";}
    $v_no41        = $_POST['no41'];if ($v_no41=='0'){$av_no41="TIDAK";}else{$av_no41="YA";}
    $v_no42        = $_POST['no42'];if ($v_no42=='0'){$av_no42="TIDAK";}else{$av_no42="YA";}
    $v_no43        = $_POST['no43'];if ($v_no43=='0'){$av_no43="TIDAK";}else{$av_no43="YA";}
    $v_no44        = $_POST['no44'];if ($v_no44=='0'){$av_no44="TIDAK";}else{$av_no44="YA";}
    $v_sample      = $_POST['reqid_sample'];
    $v_hematokrit  = $_POST['reqhematokrit'];
    $v_hemoglobin  = $_POST['reqhemoglobin'];
    $v_trombosit   = $_POST['reqtrombosit'];
    $v_leukosit    = $_POST['reqleukosit'];
    $v_tgl_pcr_pos = $_POST['tgl_positif'];
    $v_fdonor      = $_POST['pernahdonor'];
    $v_fdonor_a    = $_POST['pernahdonor_a'];
    $v_rtrans      = $_POST['pernahdonor_t'];
    $v_hamil       = $_POST['hamil'];
    $v_jmlanak     = $_POST['jumlahanak'];
    $v_jantung     = $_POST['jantung'];
    $v_hipertensi  = $_POST['hipertensi'];
    $v_paru        = $_POST['paru'];
    $v_hati        = $_POST['hati'];
    $v_ginjal      = $_POST['ginjal'];
    $v_kronik      = $_POST['kronik'];
    $v_hiv         = $_POST['hiv'];
    $v_panas       = $_POST['panas'];
    $v_batuk       = $_POST['batuk'];
    $v_tenggorokan = $_POST['tenggorokan'];
    $v_sesak       = $_POST['sesak'];
    $v_pilek       = $_POST['pilek'];
    $v_lesu        = $_POST['lesu'];
    $v_kepala      = $_POST['kepala'];
    $v_diare       = $_POST['diare'];
    $v_muntah      = $_POST['muntah'];
    $lanjuthb      = $_POST['lanjuthb'];
    $v_titer_cov   = '';
    $etglkembali   = $_POST['kembalidonor'];
    
    //Insert htransaksi_ic===================================================================
    $chkic=mysqli_query($dbi,"SELECT * from htransaksi_ic WHERE notrans='$v_notransaksi'");
    if (mysqli_num_rows($chkic)=='0'){
        $t_ic=mysqli_query($dbi,"INSERT INTO `htransaksi_ic`(`notrans`, `pendonor`, `satu`, `dua`, `tiga`, `empat`, `lima`, `enam`, `tujuh`, `delapan`, `sembilan`, `sepuluh`, `sebls`, `duabls`, `tigabls`, `empatbls`, `limabls`, `enambls`, `tujuhbls`, `delapanbls`, `sembilanbls`, `duapuluh`, `duasatu`, `duadua`, `duatiga`, `duaempat`, `dualima`, `duaenam`, `duatujuh`, `duadelapan`, `duasembilan`, `tigapuluh`, `tigasatu`, `tigadua`, `tigatiga`, `tigaempat`, `tigalima`, `tigaenam`, `tigatujuh`, `tigadelapan`, `tigasembilan`, `empatpuluh`, `empatsatu`, `empatdua`, `empattiga`, `empatempat`)
                                VALUES ('$v_notransaksi','$v_kodedonor','$v_no1','$v_no2','$v_no3','$v_no4','$v_no5','$v_no6','$v_no7','$v_no8','$v_no9','$v_no10','$v_no11','$v_no12','$v_no13','$v_no14','$v_no15','$v_no16','$v_no17','$v_no18','$v_no19','$v_no20','$v_no21','$v_no22','$v_no23','$v_no24','$v_no25','$v_no26','$v_no27','$v_no28','$v_no29','$v_no30','$v_no31','$v_no32','$v_no33','$v_no34','$v_no35','$v_no36','$v_no37','$v_no38','$v_no39','$v_no40','$v_no41','$v_no42','$v_no43','$v_no44')");
        
        $q_antrian = "UPDATE `antrian` set `satu`='$av_no1', `dua`='$av_no2', `tiga`='$av_no3', `empat`='$av_no4', `lima`='$av_no5', `enam`='$av_no6', `tujuh`='$av_no7', `delapan`='$av_no8', `sembilan`='$av_no9', `sepuluh`='$av_no10',`sebls`='$av_no11', `duabls`='$av_no12',`tigabls`='$av_no13', `empatbls`='$av_no14', `limabls`='$av_no15', `enambls`='$av_no16', `tujuhbls`='$av_no17', `delapanbls`='$av_no18', `sembilanbls`='$av_no19', `duapuluh`='$av_no20',`duasatu`='$av_no21', `duadua`='$av_no22', `duatiga`='$av_no23', `duaempat`='$av_no24', `dualima`='$av_no25', `duaenam`='$av_no26', `duatujuh`='$av_no27', `duadelapan`='$av_no28', `duasembilan`='$av_no29', `tigapuluh`='$av_no30',`tigasatu`='$av_no31', `tigadua`='$av_no32', `tigatiga`='$av_no33', `tigaempat`='$av_no34', `tigalima`='$av_no35', `tigaenam`='$av_no36', `tigatujuh`='$av_no37', `tigadelapan`='$av_no38', `tigasembilan`='$av_no39', `empatpuluh`='$av_no40',`empatsatu`='$av_no41', `empatdua`='$av_no42', `empattiga`='$av_no43', `empatempat`='$av_no44' where `transaksi`='$v_notransaksi'";
        $q_antri = mysqli_query($dbi,$q_antrian);
        //echo $q_antrian;
        if($t_ic){
            //$msq .= '<br>Inform Concent ok';
        }else{
            //$msg .= '<br>ERROR saat penyisipan Inform Concent';
        }
    }else{
        $iu_ic=mysqli_query($dbi,"UPDATE `htransaksi_ic` SET
                  `satu`       ='$v_no1'
                , `dua`        ='$v_no2'
                , `tiga`       ='$v_no3'
                , `empat`      ='$v_no4'
                , `lima`       ='$v_no5'
                , `enam`       ='$v_no6'
                , `tujuh`      ='$v_no7'
                , `delapan`    ='$v_no8'
                , `sembilan`   ='$v_no9'
                , `sepuluh`    ='$v_no10'
                , `sebls`      ='$v_no11'
                , `duabls`     ='$v_no12'
                , `tigabls`    ='$v_no13'
                , `empatbls`   ='$v_no14'
                , `limabls`    ='$v_no15'
                , `enambls`    ='$v_no16'
                , `tujuhbls`   ='$v_no17'
                , `delapanbls` ='$v_no18'
                , `sembilanbls`='$v_no19'
                , `duapuluh`   ='$v_no20'
                , `duasatu`    ='$v_no21'
                , `duadua`     ='$v_no22'
                , `duatiga`    ='$v_no23'
                , `duaempat`   ='$v_no24'
                , `dualima`    ='$v_no25'
                , `duaenam`    ='$v_no26'
                , `duatujuh`   ='$v_no27'
                , `duadelapan` ='$v_no28'
                , `duasembilan`='$v_no29'
                , `tigapuluh`  ='$v_no30'
                , `tigasatu`   ='$v_no31'
                , `tigadua`    ='$v_no32'
                , `tigatiga`   ='$v_no33'
                , `tigaempat`  ='$v_no34'
                , `tigalima`   ='$v_no35'
                , `tigaenam`   ='$v_no36'
                , `tigatujuh`  ='$v_no37'
                , `tigadelapan`='$v_no38'
                , `tigasembilan`='$v_no39'
                , `empatpuluh` ='$v_no40'
                , `empatsatu`  ='$v_no41'
                , `empatdua`   ='$v_no42'
                , `empattiga`  ='$v_no43'
                , `empatempat` ='$v_no44'
                WHERE
                (`notrans`='$v_notransaksi') AND (`pendonor`='$v_kodedonor')");
        
        $q_antrian = "UPDATE `antrian` set `satu`='$av_no1', `dua`='$av_no2', `tiga`='$av_no3', `empat`='$av_no4', `lima`='$av_no5', `enam`='$av_no6', `tujuh`='$av_no7', `delapan`='$av_no8', `sembilan`='$av_no9', `sepuluh`='$av_no10',`sebls`='$av_no11', `duabls`='$av_no12',`tigabls`='$av_no13', `empatbls`='$av_no14', `limabls`='$av_no15', `enambls`='$av_no16', `tujuhbls`='$av_no17', `delapanbls`='$av_no18', `sembilanbls`='$av_no19', `duapuluh`='$av_no20',`duasatu`='$av_no21', `duadua`='$av_no22', `duatiga`='$av_no23', `duaempat`='$av_no24', `dualima`='$av_no25', `duaenam`='$av_no26', `duatujuh`='$av_no27', `duadelapan`='$av_no28', `duasembilan`='$av_no29', `tigapuluh`='$av_no30',`tigasatu`='$av_no31', `tigadua`='$av_no32', `tigatiga`='$av_no33', `tigaempat`='$av_no34', `tigalima`='$av_no35', `tigaenam`='$av_no36', `tigatujuh`='$av_no37', `tigadelapan`='$av_no38', `tigasembilan`='$av_no39', `empatpuluh`='$av_no40',`empatsatu`='$av_no41', `empatdua`='$av_no42', `empattiga`='$av_no43', `empatempat`='$av_no44' where `transaksi`='$v_notransaksi'";
        $q_antri = mysqli_query($dbi,$q_antrian);
       // echo $q_antrian;
        if($iu_ic){$msg .= '<br>Update Inform Concent';}else{ $msg .='<br>ERROR update Inform Concent';}
    }
    //Insert/Update attrib_tpk =========================================================================
    if ($v_donortpk=='1'){
        echo ' Donor TPK ';
        $chkic=mysqli_query($dbi,"SELECT * from attrib_tpk WHERE notrans='$v_notransaksi'");
        if (mysqli_num_rows($chkic)=='0'){
            $t_tpk=mysqli_query($dbi,"INSERT INTO  `attrib_tpk`( `notrans`, `pkode`, `hamil`, `j_anak`, `f_bb`, `f_tb`, `f_donor`, `f_donor_a`, `p_jantung`, `p_hipertns`, `p_paru`, `p_hati`, `p_ginjal`, `p_kronik`, `p_hiv`, `tgl_pdp`, `g_demam`, `g_batuk`, `g_tenggrk`, `g_sesak`, `g_pilek`, `g_lesu`, `g_pusing`, `g_diare`, `g_mual`, `r_transf`, `accept`, `petugas`) VALUE (
                                      '$v_notransaksi', '$v_kodedonor', '$v_hamil', '$v_jmlanak','$v_bb','$v_tb','$v_fdonor','$v_fdonor_a','$v_jantung','$v_hipertensi','$v_paru','$v_hati','$v_ginjal','$v_kronik','$v_hiv','$v_tgl_pcr_pos','$v_panas','$v_batuk','$v_tenggorokan','$v_sesak','$v_pilek','$v_lesu','$v_kepala','$v_diare','$v_muntah','$v_rtrans','1','$v_ptgdokter')");
            if($t_tpk){ $msg .='<br>Seleksi Donor PK berhasil ditambahkan, ';}else{$msg .='<br>Seleksi Donor PK, gagal ditambahkan, ';}
        }else{
            $u_tpk=mysqli_query($dbi,"UPDATE`attrib_tpk` SET
              `hamil`='$v_hamil'
            , `j_anak`='$v_jmlanak'
            , `f_bb`='$v_bb'
            , `f_tb`='$v_tb'
            , `f_donor`='$v_fdonor'
            , `f_donor_a`='$v_fdonor_a'
            , `p_jantung`='$v_jantung'
            , `p_hipertns`='$v_hipertensi'
            , `p_paru`='$v_paru'
            , `p_hati`='$v_hati'
            , `p_ginjal`='$v_ginjal'
            , `p_kronik`='$v_kronik'
            , `p_hiv`='$v_hiv'
            , `tgl_pdp`='$v_tgl_pcr_pos'
            , `g_demam`='$v_panas'
            , `g_batuk`='$v_batuk'
            , `g_tenggrk`='$v_tenggorokan'
            , `g_sesak`='$v_sesak'
            , `g_pilek`='$v_pilek'
            , `g_lesu`='$v_lesu'
            , `g_pusing`='$v_kepala'
            , `g_diare`='$v_diare'
            , `g_mual`='$v_muntah'
            , `r_transf`='$v_rtrans'
            , `accept`='1'
            , `petugas`='$v_ptgdokter'
            WHERE
            (`notrans` = '$v_notransaksi') AND (`pkode`= '$v_kodedonor')");
            if($u_tpk){ $msg .='<br>Update seleksi Donor PK berhasil, ';}else{$msg .='<br>RROR update Seleksi Donor PK ';}
        }
        //Update titer cov-19
        $cov_titer=mysqli_fetch_assoc(mysqli_query($dbi,"SELECT `cov_titer` FROM `covid` WHERE `cov_sampel`='$v_sample'"));
        $v_titer_cov=$cov_titer['cov_titer'];
    }
    
    //UPDATE HTRANSAKSI=====================================================
    if ($v_lolos=='1') {
        $pengambilan="1";
        $status="-";
        $jumHB="-";
        $ketbatal=$v_alasan;
    }else{
        $pengambilan='3';
        $status="0";
        $jumHB='1';
        $ketbatal='-';
    }
    $sql_transaksi="UPDATE `htransaksi` SET
                    `namadokter`='$v_ptgdokter',
                    `petugasHB`='$v_ptgshb',
                    `petugasTensi`='$v_ptgtensi',
                    `beratbadan`='$v_bb',
                    `tensi`='$v_tensi',
                    `suhu`='$v_suhu',
                    `nadi`='$v_nadi',
                    `jumHB`='$jumHB',
                    `Hb`='$v_hemoglobin',
                    `hct`='$v_hematokrit',
                    `status_test`='1',
                    `Status`='$status',
                    `Pengambilan`='$pengambilan',
                    `ketBatal`='$ketbatal',
                    `mu`='$mu',
                    `pendonor_abo`='$v_goldarah_a',
                    `pendonor_rh`='$v_rhesus_a',
                    `hematokrit`='$v_hematokrit',
                    `hemoglobin`='$v_hemoglobin',
                    `leukosit`='$v_leukosit',
                    `trombosit`='$v_trombosit',
                    `sturasi_oksigen`='$v_oksigen',
                    `titer_cov19`='$v_titer_cov',
                    `id_sample`='$v_sample',
                    `jnsperiksa`='1', `cek_dokter`='1'
                WHERE (`NoTrans`='$v_notransaksi')";
    $upd_htransaksi=mysqli_query($dbi,$sql_transaksi);
    //UPDATE PENDONOR DGN GOL DARAH=========================================
    //$update_pendonor=mysqli_query($dbi,"UPDATE pendonor SET GolDarah='$v_goldarah',Rhesus='$v_rhesus' WHERE Kode='$v_kodedonor'");
    //Update kode_sampel====================================================
    //$updSample=mysqli_query($dbi,"UPDATE samplekode SET `sk_donor`='$v_kodedonor' WHERE `sk_kode`='$v_sample'");
    if ($updSample){
        //$msg .='<br>Sample Kode Update OK, ';
    }else{
        //$msg .='<br>Sample Kode Tidak Ada, ';
    }
    echo $msg;
                                
    if ($v_lolos=="0"){
        if($lanjuthb =="1"){
            echo '<META http-equiv="refresh" content="1; url=pmi'.$lv5.'.php?module=hb_gol&kode='.$v_kodedonor.'&NoTrans='.$v_notransaksi.'">';
        }else{
            echo '<META http-equiv="refresh" content="1; url=pmi'.$lv5.'.php?module=dokter&jenis=0">';
        }
    } else {//JIKA TIDAK LOLOS
        //UPDATE LOKAL
        $edit   = mysqli_query($dbi,"UPDATE pendonor set tglkembali='$etglkembali' where Kode='$v_kodedonor'");
        
        
        //CURL DB NASIONAL
        $curlinsdn = curl_init();
        curl_setopt_array($curlinsdn, array(
          CURLOPT_URL => "https://dbdonor.pmi.or.id/pmi/api/simdondar/updatedonorkembali.php",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 5,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => array('Kode' => $v_kodedonor,'tglkembali' => $etglkembali, 'metode' => 'update' ),
        ));
        $response = curl_exec($curlinsdn);
        $datains  = json_decode($response, true);
        //echo "<pre>"; print_r($response); echo "</pre>";
        curl_close($curlinsdn);
        
        //Kembali ke Antrian
        echo '<META http-equiv="refresh" content="1; url=pmi'.$lv5.'.php?module=dokter&jenis=0">';
                   }
}

$kode_donor=$_GET['kode'];
$kode_transaksi=$_GET['trx'];
$sqlsample="SELECT `sk_id`, `sk_trans`, `sk_tgl`, `sk_jenis`, `sk_kode`, `sk_donor`, `sk_gol`, `sk_rh`, `sk_user`, `sk_ptg_plebotomi`, `sk_tgl_plebotomi`, `sk_vol_plebotomi`, `sk_tmp_plebotomi`, `sk_on_insert`
            FROM `samplekode` WHERE `sk_donor`='$kode_donor' ORDER BY `sk_tgl_plebotomi` DESC LIMIT 1";
$sqsample=mysqli_query($dbi,$sqlsample);
$ada_sample='0';
if (mysqli_num_rows($sqsample)>0){
    $ada_sample='1';
    $sqsample=mysqli_fetch_assoc($sqsample);
    $kode_sample=$sqsample['sk_kode'];
    $sample_info="--";
    include "tpk/getssample_info.php";
    $ex_sampleinfo=explode(';',$sample_info);
    
}
$namauser = $_SESSION['namauser'];
$lv0='pmi'.$_SESSION['leveluser'];
$today1=date("Y-m-d H:i:s");
$today2=date("Y-m-d");
$jam_donor=date("H:i:s");
$tipe_donor='0';
$qdonor=mysqli_query($dbi,"select * from `pendonor` where `Kode`='$kode_donor'");
$qtrans=mysqli_fetch_assoc(mysqli_query($dbi,"select * from `htransaksi` where `NoTrans`='$kode_transaksi'"));
$dtdonor=mysqli_fetch_assoc($qdonor);
$jenis_pengambilan='Donor Biasa';
$msg = "DONOR DARAH";
if($qtrans['apheresis']=='1'){$jenis_pengambilan="Donor Apheresis";$msg="DONOR DARAH APHERESIS";}
if($qtrans['donor_tpk']=='1'){$jenis_pengambilan="Donor Plasma Konvalesen";$msg="DONOR DARAH PLASMA KONVALESEN";}
$qtrans['KodePendonor']=str_replace("'","\'",$qtrans['KodePendonor']);
$swab=mysqli_fetch_assoc(mysqli_query($dbi,"SELECT *, DATEDIFF(current_date,`pcr_tglperiksa`) as hari FROM `covid_pcr` WHERE `pcr_pendonor`='$kode_donor' order by `pcr_tglperiksa` DESC limit 1"));
$hasiswab='Tidak ada data';
$ln=strlen($swab['pcr_hasil']);
if($ln>0){
    $hasiswab=$swab['pcr_tglperiksa'].', (<b>'.$swab['pcr_hasil'].'</b>)'.' - '.$swab['hari'].' hari';
}

?>
<div class="container-fluid" style="margin:20px;">
    <div class="row">
        <div class="col-6 pull-left">
            <div style="font-size:20px; font-weight:bold;color:#ff0000;text-shadow: 1px 1px 1px #000000; font-family:Helvetica, Arial, san-serif;">MEDICAL CHECK UP - <?php echo $msg;?></div>
        </div>
        <div class="col-6 pull-right">
            <div class="text-danger blink" id="alert1"></div>
        </div>
    </div>
    <div class="row">
        <form name="periksa" autocomplete="off" id="periksa" action="" method="post" onsubmit="return konfirmasi()">
            <input type="hidden" name="notransaksi" value="<?php echo $kode_transaksi;?>">
            <input type="hidden" name="kodedonor" value="<?php echo $kode_donor;?>">
            <div class="panel with-nav-tabs panel-default bayangan">
                <div class="panel-heading">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_ic" data-toggle="tab">Kuesioner</a></li>
                            <li><a href="#tab_pendonor" data-toggle="tab">Check Up</a></li>
                            
                            <?php
                        if ($qtrans['apheresis']=='1'){
                            echo '<li><a data-toggle="tab" href="#tab_sampel">Hasil Lab</a></li>';
                        } else if ($qtrans['donor_tpk']=='1'){
                            echo '<li><a data-toggle="tab" href="#tab_sampel">Hasil Lab</a></li>';
                            echo '<li><a data-toggle="tab" href="#tab_ictpk">Inform Concent PK</a></li>';
                        }
                        ?>
                        <li><a data-toggle="tab" href="#tab_riwayat">Riwayat Donor</a></li>
                        </ul>
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                        
                        <div id="tab_pendonor" class="tab-pane fade">
                            <div class="col-xs-6" >
                                <?php
                                ($dtdonor['Jk']=='0') ? $kel="Laki-laki" : $kel="Perempuan" ;
                                ($dtdonor['Status']=='0') ? $status="Belum Menikah" : $status="Sudah Menikah" ;
                                ?>
                                <div class="table-responsive" id="shadow1">
                                    <table class="table borderless table-striped table-hover">
                                        <tr><td>Kode</td><td><?php echo $dtdonor['Kode'];?></td></tr>
                                        <tr><td>NIK</td><td><?php echo $dtdonor['NoKTP'];?></td></tr>
                                        <tr><td>Nama</td><td><strong><?php echo $dtdonor['Nama'];?></strong></td></tr>
                                        <tr><td>Gol</td>
                                            <td>
                                                <input type="hidden" name="abo_pendonor" value="<?php echo $dtdonor['GolDarah'];?>">
                                                <input type="hidden" name="rh_pendonor" value="<?php echo $dtdonor['Rhesus'];?>">
                                                <input type="hidden" name="goldarah" value="<?php echo $dtdonor['GolDarah'];?>">
                                                <input type="hidden" name="rhesus" value="<?php echo $dtdonor['Rhesus'];?>">
                                                <?php echo $dtdonor['GolDarah']."( ".$dtdonor['Rhesus']." )";?>
                                            </td></tr>
                                        <tr><td>Kelamin</td><td><?php echo $kel;?>, Status : <?php echo $status;?></td></tr>
                                        <tr><td>Tempat Lahir</td><td><?php echo $dtdonor['TempatLhr'];?></td></tr>
                                        <tr><td>Tanggal Lahir</td><td><?php echo $dtdonor['TglLhr'];?></td></tr>
                                        <tr><td>Donor Ke</td><td><?php echo $dtdonor['jumDonor']+1;?></td></tr>
                                        <tr><td>Tgl Kembali Donor</td><td><?php echo $dtdonor['tglkembali'];?></td></tr>
                                        <tr><td>Tgl Kembali Apheresis</td><td><?php echo $dtdonor['tglkembali_apheresis'];?></td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="table-responsive">
                                    <table class="table borderless table-striped table-hover" id="shadow1">
                                        <tr>
                                            <td>Berat Badan<sup style="color:red;"><strong>*</strong></sup></td>
                                            <td><input name="reqberat_badan" id="berat_badan" type="text" style="width:15mm;" onChange="berat(this.value)" maxlength="3" required > kg</td>
                                        </tr>
                                        <tr>
                                            <td>Tinggi Badan<sup style="color:red;"><strong>*</strong></sup></td>
                                            <td><input name="tinggi_badan" type="text" style="width:15mm;" maxlength="3" required> cm</td>
                                        </tr>
                                        <tr>
                                            <td>Tensi<sup style="color:red;"><strong>*</strong></sup></td>
                                            <td><input name="reqtensi_sistol" id="tensi_sistol" type="text"  style="width:15mm;"  onChange="sistol(this.value)" maxlength="3" required> /
                                                <input name="reqtensi_diastol" id="tensi_diastol" type="text" style="width:15mm;"  onChange="diastol(this.value)" maxlength="3" required> mmHg
                                            </td>
                                        </tr>
                                        <tr><td>Suhu<sup style="color:red;"><strong>*</strong></sup></td>
                                            <td><input name="reqtemperatur" id="temperature" type="text" style="width:15mm;" maxlength="5" onChange="suhutubuh(this.value)" required>&nbsp;<sup>o</sup>C</td>
                                        </tr>
                                        <tr><td>Nadi<sup style="color:red;"><strong>*</strong></sup></td>
                                            <td><input name="reqnadi" type="text" style="width:15mm;" maxlength="3" required> BPM</td></tr>
                                        <tr><td>Saturasi Oksigen</td>
                                            <td><input name="oksigen" type="text" style="width:15mm;" maxlength="3" required> %</td></tr>
                                        <tr><td>Ptg Anamnesa</td>
                                            <td>
                                                <select name="id_dokter" style="width:70mm;">
                                                    <?
                                                    $dokter=mysqli_query($dbi,"select * from dokter_periksa where aktif='1' order by nama");
                                                    $tmpdktr=mysqli_fetch_assoc(mysqli_query($dbi,"select dokter from tempudd where modul='MU CHECKUP'"));
                                                    while($data=mysqli_fetch_array($dokter)){
                                                    if ($data['kode'] == $tmpdktr['dokter']){
                                                            echo "<option value=$data[kode] selected>$data[Nama]</option>";
                                                        }else{
                                                            echo "<option value=$data[kode]>$data[Nama]</option>";
                                                        }
                                                    } ?>
                                                </select>
                                            </td></tr>
                                            <tr></tr>
                                            <tr>
                                                <td><b>Lanjutkan HB & Gol. Darah </b></td>

                                                <td><div class="onoffswitch">
                                                        <input type="hidden" name="lanjuthb" value="0">
                                                        <input type="checkbox" name="lanjuthb" class="onoffswitch-checkbox" id="lanjuthb" tabindex="1" value="1">
                                                        <label class="onoffswitch-label" for="lanjuthb"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div></td>
                                            </tr>

                                        <!--tr><td>Ptgs Tensi</td>
                                            <td>
                                                <select name="id_tensi" style="width:70mm;">
                                                    <?
                                                    $usr=mysqli_query($dbi,"select * from `user` where `aktif`='0' order by `nama_lengkap`");
                                                    $tmpdktr=mysqli_fetch_assoc(mysqli_query($dbi,"select * from tempudd where modul='MU CHECKUP'"));
                                                    while($data=mysqli_fetch_array($usr)){
                                                    if ($data['id_user'] == $tmpdktr['petugas1']){
                                                            echo "<option value=$data[id_user] selected>$data[nama_lengkap]</option>";
                                                        }else{
                                                            echo "<option value=$data[id_user]>$data[nama_lengkap]</option>";
                                                        }
                                                    } ?>
                                                </select>
                                            </td></tr>
                                        <tr><td>Ptgs HB</td>
                                            <td>
                                                <select name="id_hb" style="width:70mm;">
                                                    <?
                                                    $usr=mysqli_query($dbi,"select * from `user` where `aktif`='0' order by `nama_lengkap`");
                                                    $tmpdktr=mysqli_fetch_assoc(mysqli_query($dbi,"select * from tempudd where modul='MU CHECKUP'"));
                                                    while($data=mysqli_fetch_array($usr)){
                                                    if ($data['id_user'] == $tmpdktr['petugas2']){
                                                            echo "<option value=$data[id_user] selected>$data[nama_lengkap]</option>";
                                                        }else{
                                                            echo "<option value=$data[id_user]>$data[nama_lengkap]</option>";
                                                        }
                                                    } ?>
                                                </select>
                                            </td></tr-->
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="tab_ic" class="tab-pane fade in active">

                            <?php
                                function switchvalue($asal){
                                    if ($asal=="YA"||$asal=="1"){
                                        return "checked";
                                    }else{
                                        return "";
                                    }
                                }

                                $sq_ic=mysqli_query($dbi,"SELECT * FROM htransaksi_ic WHERE notrans='$kode_transaksi'");
                                if ($sq_ic){
                                    $dtIC=mysqli_fetch_assoc($sq_ic);
                                }
                            ?>
                            <div class="col-xs-6">
                                <div class="table-responsive" id="shadow1">
                                    <table class="table borderless table-striped table-condensed">
                                    <tr><td colspan=3><b>Dalam hari Ini ꞉</b></td><tr>
                                        <tr><td>1</td><td>Merasa sehat pada hari ini? (tidak sedang flue/batuk/demam/pusing)</td>
                                            <td><div class="onoffswitch">
                                                    <input type="hidden" name="no1" value="0">
                                                    <input type="checkbox" name="no1" class="onoffswitch-checkbox" id="no1" tabindex="1" value="1" <?php echo switchvalue($dtIC['satu']);?>>
                                                    <label class="onoffswitch-label" for="no1"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>2</td><td>Sedang minum antibiotik ?</td>
                                            <td><div class="onoffswitch">
                                                    <input type="hidden" name="no2" value="0">
                                                    <input type="checkbox" name="no2" class="onoffswitch-checkbox" id="no2" tabindex="1" value="1" <?php echo switchvalue($dtIC['dua']);?>>
                                                    <label class="onoffswitch-label" for="no2"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>3</td><td>Sedang minum obat lain untuk infeksi ?</td>
                                            <td><div class="onoffswitch">
                                                    <input type="hidden" name="no3" value="0">
                                                    <input type="checkbox" name="no3" class="onoffswitch-checkbox" id="no3" tabindex="1" value="1" <?php echo switchvalue($dtIC['tiga']);?>>
                                                    <label class="onoffswitch-label" for="no3"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Dalam waktu 48 jam terakhir ꞉</b></td><tr>
                                        <tr><td>4</td><td>Apakah anda sedang minum aspirin atau obat yang mengandung aspirin ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no4" value="0">
                                                    <input type="checkbox" name="no4" class="onoffswitch-checkbox" id="no4" tabindex="1" value="1" <?php echo switchvalue($dtIC['empat']);?>>
                                                    <label class="onoffswitch-label" for="no4"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Dalam waktu 1 minggu terakhir</b></td><tr>
                                        <tr><td>5</td><td>Apakah anda mengalami sakit kepala dan demam bersamaan ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no5" value="0">
                                                    <input type="checkbox" name="no5" class="onoffswitch-checkbox" id="no5" tabindex="1" value="1" <?php echo switchvalue($dtIC['lima']);?>>
                                                    <label class="onoffswitch-label" for="no5"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Dalam waktu 6 minggu terakhir</b></td><tr>
                                        <tr><td>6</td><td>Jika wanita ꞉ apakah anda saat ini sedang hamil ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no6" value="0">
                                                    <input type="checkbox" name="no6" class="onoffswitch-checkbox" id="no6" tabindex="1" value="1" <?php echo switchvalue($dtIC['enam']);?>>
                                                    <label class="onoffswitch-label" for="no6"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Dalam waktu 8 minggu terakhir</b></td><tr>
                                        <tr><td>7</td><td>Apakah anda mendonorkan darah lengkap ? </td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no7" value="0">
                                                    <input type="checkbox" name="no7" class="onoffswitch-checkbox" id="no7" tabindex="1" value="1" <?php echo switchvalue($dtIC['tujuh']);?>>
                                                    <label class="onoffswitch-label" for="no7"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>8</td><td>Apakah anda menerima vaksinasi atau suntikan lainnya ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no8" value="0">
                                                    <input type="checkbox" name="no8" class="onoffswitch-checkbox" id="no8" tabindex="1" value="1" <?php echo switchvalue($dtIC['delapan']);?>>
                                                    <label class="onoffswitch-label" for="no8"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>9</td><td>Apakah anda pernah kontak dengan orang yang menerima vaksinasi smallpox/cacar air  ?  </td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no9" value="0">
                                                    <input type="checkbox" name="no9" class="onoffswitch-checkbox" id="no9" tabindex="1" value="1" <?php echo switchvalue($dtIC['sembilan']);?>>
                                                    <label class="onoffswitch-label" for="no9"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Dalam waktu 16 minggu terakhir</b></td><tr>
                                        <tr><td>10</td><td>Apakah anda mendonorkan 2 kantong sel darah merah melalui proses aferesis ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no10" value="0">
                                                    <input type="checkbox" name="no10" class="onoffswitch-checkbox" id="no10" tabindex="1" value="1" <?php echo switchvalue($dtIC['sepuluh']);?>>
                                                    <label class="onoffswitch-label" for="no10"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Dalam waktu 6 bulan terakhir</b></td><tr>
                                        <tr><td>11</td><td>Apakah anda pernah mengunjungi daerah endemis malaria?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no11" value="0">
                                                    <input type="checkbox" name="no11" class="onoffswitch-checkbox" id="no11" tabindex="1" value="1" <?php echo switchvalue($dtIC['sebls']);?>>
                                                    <label class="onoffswitch-label" for="no11"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Dalam waktu 1 tahun terakhir</b></td><tr>
                                        <tr><td>12</td><td>Apakah anda pernah menerima transfusi darah ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no12" value="0">
                                                    <input type="checkbox" name="no12" class="onoffswitch-checkbox" id="no12" tabindex="1" value="1" <?php echo switchvalue($dtIC['duabls']);?>>
                                                    <label class="onoffswitch-label" for="no12"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>13</td><td>Apakah anda pernah mendapat transplantasi, organ, jaringan atau sumsum tulang ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no13" value="0">
                                                    <input type="checkbox" name="no13" class="onoffswitch-checkbox" id="no13" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigabls']);?>>
                                                    <label class="onoffswitch-label" for="no13"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>14</td><td>Apakah anda pernah cangkok organ ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no14" value="0">
                                                    <input type="checkbox" name="no14" class="onoffswitch-checkbox" id="no14" tabindex="1" value="1" <?php echo switchvalue($dtIC['empatbls']);?>>
                                                    <label class="onoffswitch-label" for="no14"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>15</td><td>Apakah anda pernah tertusuk jarum medis ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no15" value="0">
                                                    <input type="checkbox" name="no15" class="onoffswitch-checkbox" id="no15" tabindex="1" value="1" <?php echo switchvalue($dtIC['limabls']);?>>
                                                    <label class="onoffswitch-label" for="no15"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>16</td><td>Apakah anda pernah berhubungan seksual dengan orang dengan HIV/AIDS ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no16" value="0">
                                                    <input type="checkbox" name="no16" class="onoffswitch-checkbox" id="no16" tabindex="1" value="1" <?php echo switchvalue($dtIC['enambls']);?>>
                                                    <label class="onoffswitch-label" for="no16"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>17</td><td>Apakah anda pernah berhubungan seksual dengan pekerja seks komersial ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no17" value="0">
                                                    <input type="checkbox" name="no17" class="onoffswitch-checkbox" id="no17" tabindex="1" value="1" <?php echo switchvalue($dtIC['tujuhbls']);?>>
                                                    <label class="onoffswitch-label" for="no17"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>18</td><td>Apakah anda pernah berhubungan seksual dengan pengguna narkoba jarum suntik ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no18" value="0">
                                                    <input type="checkbox" name="no18" class="onoffswitch-checkbox" id="no18" tabindex="1" value="1" <?php echo switchvalue($dtIC['delapanbls']);?>>
                                                    <label class="onoffswitch-label" for="no18"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>19</td><td>Apakah anda pernah berhubungan seksual dengan pengguna konsentrat faktor pembekuan?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no19" value="0">
                                                    <input type="checkbox" name="no19" class="onoffswitch-checkbox" id="no19" tabindex="1" value="1" <?php echo switchvalue($dtIC['sembilanbls']);?>>
                                                    <label class="onoffswitch-label" for="no19"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>20</td><td>Donor wanita ꞉ apakah anda pernah berhubungan seksual dengan laki-laki yang biseksual ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no20" value="0">
                                                    <input type="checkbox" name="no20" class="onoffswitch-checkbox" id="no20" tabindex="1" value="1" <?php echo switchvalue($dtIC['duapuluh']);?>>
                                                    <label class="onoffswitch-label" for="no20"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>21</td><td>Apakah anda pernah berhubungan seksual dengan penderita hepatitis ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no21" value="0">
                                                    <input type="checkbox" name="no21" class="onoffswitch-checkbox" id="no21" tabindex="1" value="1" <?php echo switchvalue($dtIC['duasatu']);?>>
                                                    <label class="onoffswitch-label" for="no21"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>22</td><td>Apakah anda tinggal bersama penderita hepatitis ?</td>
                                            <td><div class="onoffswitch">
                                                    <input type="hidden" name="no22" value="0">
                                                    <input type="checkbox" name="no22" class="onoffswitch-checkbox" id="no22" tabindex="1" value="1" <?php echo switchvalue($dtIC['duadua']);?>>
                                                    <label class="onoffswitch-label" for="no22"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        </table>
                                        </div>
                                        </div>
                                        <div class="col-xs-6">
                                        <div class="table-responsive" id="shadow1">
                                        <table class="table borderless table-striped table-condensed"">

                                        <tr><td>23</td><td>Apakah anda memiliki tatto ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no23" value="0">
                                                    <input type="checkbox" name="no23" class="onoffswitch-checkbox" id="no23" tabindex="1" value="1" <?php echo switchvalue($dtIC['duatiga']);?>>
                                                    <label class="onoffswitch-label" for="no23"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>24</td><td>Apakah anda memiliki tindik telinga atau bagian tubuh lainnya ? </td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no24" value="0">
                                                    <input type="checkbox" name="no24" class="onoffswitch-checkbox" id="no24" tabindex="1" value="1" <?php echo switchvalue($dtIC['duaempat']);?>>
                                                    <label class="onoffswitch-label" for="no24"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>25</td><td>Apakah anda sedang atau pernah mendapat pengobatan siﬁlis atau GO (kencing nanah) ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no25" value="0">
                                                    <input type="checkbox" name="no25" class="onoffswitch-checkbox" id="no25" tabindex="1" value="1" <?php echo switchvalue($dtIC['dualima']);?>>
                                                    <label class="onoffswitch-label" for="no25"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>26</td><td>Apakah anda pernah ditahan dipenjara untuk waktu lebih dari 72 jam ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no26" value="0">
                                                    <input type="checkbox" name="no26" class="onoffswitch-checkbox" id="no26" tabindex="1" value="1" <?php echo switchvalue($dtIC['duaenam']);?>>
                                                    <label class="onoffswitch-label" for="no26"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Dalam waktu 3 tahun terakhir</b></td><tr>
                                        <tr><td>27</td><td>Apakah anda menetap diberbagai alamat yang berbeda?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no27" value="0">
                                                    <input type="checkbox" name="no27" class="onoffswitch-checkbox" id="no27" tabindex="1" value="1" <?php echo switchvalue($dtIC['duatujuh']);?>>
                                                    <label class="onoffswitch-label" for="no27"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>28</td><td>Apakah anda pernah berada di luar wilayah Indonesia ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no28" value="0">
                                                    <input type="checkbox" name="no28" class="onoffswitch-checkbox" id="no28" tabindex="1" value="1" <?php echo switchvalue($dtIC['duadelapan']);?>>
                                                    <label class="onoffswitch-label" for="no28"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Tahun 1980 hingga sekarang</b></td><tr>
                                        <tr><td>29</td><td>Apakah anda tinggal selama 5 tahun atau lebih di Eropa ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no29" value="0">
                                                    <input type="checkbox" name="no29" class="onoffswitch-checkbox" id="no29" tabindex="1" value="1" <?php echo switchvalue($dtIC['duasembilan']);?>>
                                                    <label class="onoffswitch-label" for="no29"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>30</td><td>Apakah anda menerima transfusi darah di Inggris ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no30" value="0">
                                                    <input type="checkbox" name="no30" class="onoffswitch-checkbox" id="no30" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigapuluh']);?>>
                                                    <label class="onoffswitch-label" for="no30"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Tahun 1980 hingga 1996</b></td><tr>
                                        <tr><td>31</td><td>Apakah anda tinggal selama 3 bulan atau lebih di Inggris?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no31" value="0">
                                                    <input type="checkbox" name="no31" class="onoffswitch-checkbox" id="no31" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigasatu']);?>>
                                                    <label class="onoffswitch-label" for="no31"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    <tr><td colspan=3><b>Apakah Anda Pernah :</b></td><tr>
                                        <tr><td>32</td><td>Apakah anda menerima uang, obat atau pembayaran lainnya untuk seks ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no32" value="0">
                                                    <input type="checkbox" name="no32" class="onoffswitch-checkbox" id="no32" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigadua']);?>>
                                                    <label class="onoffswitch-label" for="no32"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>33</td><td>Laki-laki ꞉ Apakah anda pernah berhubungan seksual dengan laki-laki, walaupun sekali? </td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no33" value="0">
                                                    <input type="checkbox" name="no33" class="onoffswitch-checkbox" id="no33" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigatiga']);?>>
                                                    <label class="onoffswitch-label" for="no33"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>34</td><td>Apakah anda pernah mendapat hasil Positif untuk test HIV/AIDS?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no34" value="0">
                                                    <input type="checkbox" name="no34" class="onoffswitch-checkbox" id="no34" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigaempat']);?>>
                                                    <label class="onoffswitch-label" for="no34"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>35</td><td>Apakah anda pernah melakukan bekam/fasdhu ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no35" value="0">
                                                    <input type="checkbox" name="no35" class="onoffswitch-checkbox" id="no35" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigalima']);?>>
                                                    <label class="onoffswitch-label" for="no35"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>36</td><td>Menggunakan jarum suntik untuk obat-obatan, Steroid yang tidak diresepkan dokter?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no36" value="0">
                                                    <input type="checkbox" name="no36" class="onoffswitch-checkbox" id="no36" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigaenam']);?>>
                                                    <label class="onoffswitch-label" for="no36"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>37</td><td>Apakah anda menggunakan kosentrat faktor pembekuan ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no37" value="0">
                                                    <input type="checkbox" name="no37" class="onoffswitch-checkbox" id="no37" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigatujuh']);?>>
                                                    <label class="onoffswitch-label" for="no37"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>38</td><td>Apakah anda Menderita hepatitis ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no38" value="0">
                                                    <input type="checkbox" name="no38" class="onoffswitch-checkbox" id="no38" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigadelapan']);?>>
                                                    <label class="onoffswitch-label" for="no38"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>39</td><td>Apakah anda Menderita malaria ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no39" value="0">
                                                    <input type="checkbox" name="no39" class="onoffswitch-checkbox" id="no39" tabindex="1" value="1" <?php echo switchvalue($dtIC['tigasembilan']);?>>
                                                    <label class="onoffswitch-label" for="no39"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>40</td><td>Apakah anda Menderita kanker termasuk leukemia ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no40" value="0">
                                                    <input type="checkbox" name="no40" class="onoffswitch-checkbox" id="no40" tabindex="1" value="1" <?php echo switchvalue($dtIC['empatpuluh']);?>>
                                                    <label class="onoffswitch-label" for="no40"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>41</td><td>Apakah anda bermasalah dengan jantung dan paru-paru ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no41" value="0">
                                                    <input type="checkbox" name="no41" class="onoffswitch-checkbox" id="no41" tabindex="1" value="1" <?php echo switchvalue($dtIC['empatsatu']);?>>
                                                    <label class="onoffswitch-label" for="no41"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>42</td><td>Apakah anda pernah menderita pendarahan atau penyakit berhubungan dengan darah ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no42" value="0">
                                                    <input type="checkbox" name="no42" class="onoffswitch-checkbox" id="no42" tabindex="1" value="1" <?php echo switchvalue($dtIC['empatdua']);?>>
                                                    <label class="onoffswitch-label" for="no42"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>43</td><td>Apakah Anda berhubungan seksual dengan orang yang tinggal di Afrika?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no43" value="0">
                                                    <input type="checkbox" name="no43" class="onoffswitch-checkbox" id="no43" tabindex="1" value="1" <?php echo switchvalue($dtIC['empattiga']);?>>
                                                    <label class="onoffswitch-label" for="no43"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                        <tr><td>44</td><td>Apakah anda pernah tinggal di Afrika ?</td>
                                        <td><div class="onoffswitch">
                                                    <input type="hidden" name="no44" value="0">
                                                    <input type="checkbox" name="no44" class="onoffswitch-checkbox" id="no44" tabindex="1" value="1" <?php echo switchvalue($dtIC['empattiga']);?>>
                                                    <label class="onoffswitch-label" for="no44"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                            </div></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="tab_sampel" class="tab-pane fade">
                            <div class="col-xs-6">
                                <div class="table-responsive" id="shadow1">
                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <td>Kode Sample</td>
                                            <td colspan=""><?php echo $kode_sample;?></td>
                                        </tr>
                                        <?php
                                        if($qtrans['donor_tpk']=='1'){
                                            echo'
                                                <tr>
                                                    <td colspan="2" style="font-size:110%;font-weight:bold;">SYARAT DONOR PLASMA KONVALESEN
                                                    <input type="hidden" name="reqdoonortpk" value="'.$qtrans['donor_tpk'].'">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Swab PCR Covid-19</td><td>'.$hasiswab.'</td>
                                                </tr>
                                                <tr>
                                                    <td>Titer Antibody Covid-19</td>
                                                    <td><div id="titer">'.$ex_sampleinfo[3].'</div></td>
                                                </tr>';
                                        }
                                        ?>
                                        
                                        <tr>
                                            <td>Antibody Screening</td> <td colspan="3"  style="white-space:nowrap;" id="abs"><?php echo $ex_sampleinfo[8];?></td>
                                        </tr>
                                    </table>
                                </div>

                            </div>
                            <div class="col-xs-6">
                                <div class="table-responsive" id="shadow1">
                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <td colspan="4" style="font-size:110%;font-weight:bold;">HASIL DARAH LENGKAP</td>
                                        </tr>
                                        <tr>
                                            <td>Hematokrit<sup style="color:red;"><strong>*</strong></sup></td><td style="white-space:nowrap;"><input style='width:15mm' name="reqhematokrit" id="hematokrit" type="text" maxlength="3" value="<?php echo $ex_sampleinfo[5];?>"> %</td>
                                            <td>Hemoglobin<sup style="color:red;"><strong>*</strong></sup></td><td style="white-space:nowrap;"><input style='width:15mm' name="reqhemoglobin" id="hemoglobin" type="text" maxlength="5" value="<?php echo $ex_sampleinfo[4];?>"> g/dL</td>
                                        </tr>
                                        <tr>
                                            <td>Trombosit<sup style="color:red;"><strong>*</strong></sup></td><td style="white-space:nowrap;"><input style='width:15mm' name="reqtrombosit" id="trombosit" type="text" maxlength="5" value="<?php echo $ex_sampleinfo[6];?>">10<sup>3</sup>/&micro;l</td>
                                            <td>Leukosit<sup style="color:red;"><strong>*</strong></sup></td><td><input style='width:15mm' name="reqleukosit" id="leukosit"  type="text" maxlength="5" value="<?php echo $ex_sampleinfo[7];?>">10<sup>3</sup>/&micro;l</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"  style="font-size:110%;font-weight:bold;">UJI SARING & KGD</td>
                                        </tr>
                                        <tr>
                                            <td>IMLTD Elisa/Chlia</td><td colspan="3"  style="white-space:nowrap;" id="imltd"><?php echo $ex_sampleinfo[0];?></td>
                                        </tr>
                                        <tr>
                                            <td>IMLTD NAT</td><td colspan="3"  style="white-space:nowrap;" id="nat"><?php echo $ex_sampleinfo[1];?></td>
                                        </tr>
                                        <tr>
                                            <td>Konfirmasi Gol Darah</td><td colspan="3"  style="white-space:nowrap;" id="kgd"><?php echo $ex_sampleinfo[2];?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="tab_ictpk" class="tab-pane fade">
                            <div class="col-xs-4">
                                <div class="table-responsive" id="shadow1">
                                    <?php
                                        $ic_tpk=mysqli_query($dbi,"SELECT * FROM attrib_tpk WHERE notrans='$kode_transaksi'");
                                        if ($ic_tpk){
                                            $attTPK=mysqli_fetch_assoc($ic_tpk);
                                        }
                                    ?>
                                    <table class="table borderless table-striped">
                                        <tr><td>Tgl Pos Covid-19<sup style="color:red;"><strong>*</strong></sup></td><td><input type="text" name="tgl_positif" id="datepicker" class="form-control input-sm" value="<?php echo $attTPK['tgl_pdp'];?>"></td></tr>
                                        <tr>
                                            <td>Pernah Donor biasa</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="pernahdonor" value="0">
                                                    <input type="checkbox" name="pernahdonor" class="onoffswitch-checkbox" id="pernahdonor" tabindex="0" value="1" <?php echo switchvalue($attTPK['f_donor']);?>>
                                                    <label class="onoffswitch-label" for="pernahdonor"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Pernah Donor Apheresis</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="pernahdonor_a" value="0">
                                                    <input type="checkbox" name="pernahdonor_a" class="onoffswitch-checkbox" id="pernahdonor_a" tabindex="1" value="1" <?php echo switchvalue($attTPK['f_donor_a']);?>>
                                                    <label class="onoffswitch-label" for="pernahdonor_a"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Pernah Transfusi (6 bln terakhir)</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="pernahdonor_t" value="0">
                                                    <input type="checkbox" name="pernahdonor_t" class="onoffswitch-checkbox" id="pernahdonor_t" tabindex="1" value="1" <?php echo switchvalue($attTPK['r_transf']);?>>
                                                    <label class="onoffswitch-label" for="pernahdonor_t"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                                if ($dtdonor['Jk']=='1'){
                                                    $pernahhamil=switchvalue($attTPK['hamil']);
                                                    echo '
                                                    <tr>
                                                        <td>Pernah hamil?</td>
                                                        <td>
                                                            <div class="onoffswitch">
                                                                <input type="hidden" name="hamil" value="0">
                                                                <input type="checkbox" name="hamil" class="onoffswitch-checkbox" id="hamil" tabindex="1" value="1" '.$pernahhamil.'>
                                                                <label class="onoffswitch-label" for="hamil"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jumlah anak</td>
                                                        <td>
                                                            <input type="text" class="form-control input-sm" name="jumlahanak" value=" '.$attTPK['j_anak'].'">
                                                        </td>
                                                    </tr>
                                                    ';
                                                }
                                                ?>
                                    </table>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="table-responsive" id="shadow1">
                                    
                                    <table class="table borderless  table-striped">
                                        <tr><td colspan="2" class="bg-danger"><strong>Penyakit penyerta/komorbid</strong></td></tr>
                                        <tr>
                                            <td>Penyakit jantung</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="jantung" value="0">
                                                    <input type="checkbox" name="jantung" class="onoffswitch-checkbox" id="jantung" tabindex="1" value="1" <?php echo switchvalue($attTPK['p_jantung']);?>>
                                                    <label class="onoffswitch-label" for="jantung"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Penyakit Hipertensi</td>
                                            <td style="width:15mm;">
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="hipertensi" value="0">
                                                    <input type="checkbox" name="hipertensi" class="onoffswitch-checkbox" id="hipertensi" tabindex="1" value="1" <?php echo switchvalue($attTPK['p_hipertns']);?>>
                                                    <label class="onoffswitch-label" for="hipertensi"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Penyakit paru-paru</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="paru" value="0">
                                                    <input type="checkbox" name="paru" class="onoffswitch-checkbox" id="paru" tabindex="1" value="1" <?php echo switchvalue($attTPK['p_paru']);?>>
                                                    <label class="onoffswitch-label" for="paru"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Penyakit Hati/Liver</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="hati" value="0">
                                                    <input type="checkbox" name="hati" class="onoffswitch-checkbox" id="hati" tabindex="1" value="1" <?php echo switchvalue($attTPK['p_hati']);?>>
                                                    <label class="onoffswitch-label" for="hati"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Penyakit Ginjal</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="ginjal" value="0">
                                                    <input type="checkbox" name="ginjal" class="onoffswitch-checkbox" id="ginjal" tabindex="1" value="1" <?php echo switchvalue($attTPK['p_ginjal']);?>>
                                                    <label class="onoffswitch-label" for="ginjal"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Penyakit Kronik/Neuromuskular</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="kronik" value="0">
                                                    <input type="checkbox" name="kronik" class="onoffswitch-checkbox" id="kronik" tabindex="1" value="1" <?php echo switchvalue($attTPK['p_kronik']);?>>
                                                    <label class="onoffswitch-label" for="kronik"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Penyakit HIV</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="hiv" value="0">
                                                    <input type="checkbox" name="hiv" class="onoffswitch-checkbox" id="hiv" tabindex="1" value="1" <?php echo switchvalue($attTPK['p_hiv']);?>>
                                                    <label class="onoffswitch-label" for="hiv"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="table-responsive" id="shadow1">
                                    <table class="table borderless table-striped">
                                        <tr><td colspan="2" class="bg-danger"><strong>Riwayat Gejala klinis</strong></td></tr>
                                        <tr>
                                            <td>Panas/demam > 38<sup>o</sup> celcius</td>
                                            <td  style="width:15mm;">
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="panas" value="0">
                                                    <input type="checkbox" name="panas" class="onoffswitch-checkbox" id="panas" tabindex="0" value="1" <?php echo switchvalue($attTPK['g_demam']);?>>
                                                    <label class="onoffswitch-label" for="panas"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Batuk</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="batuk" value="0">
                                                    <input type="checkbox" name="batuk" class="onoffswitch-checkbox" id="batuk" tabindex="1" value="1" <?php echo switchvalue($attTPK['g_batuk']);?>>
                                                    <label class="onoffswitch-label" for="batuk"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sakit tenggorokan</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="tenggorokan" value="0">
                                                    <input type="checkbox" name="tenggorokan" class="onoffswitch-checkbox" id="tenggorokan" tabindex="1" value="1" <?php echo switchvalue($attTPK['g_tenggrk']);?>>
                                                    <label class="onoffswitch-label" for="tenggorokan"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sesak napas</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="sesak" value="0">
                                                    <input type="checkbox" name="sesak" class="onoffswitch-checkbox" id="sesak" tabindex="1" value="1" <?php echo switchvalue($attTPK['g_sesak']);?>>
                                                    <label class="onoffswitch-label" for="sesak"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Pilek</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="pilek" value="0">
                                                    <input type="checkbox" name="pilek" class="onoffswitch-checkbox" id="pilek" tabindex="1" value="1" <?php echo switchvalue($attTPK['g_pilek']);?>>
                                                    <label class="onoffswitch-label" for="pilek"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Lesu</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="lesu" value="0">
                                                    <input type="checkbox" name="lesu" class="onoffswitch-checkbox" id="lesu" tabindex="1" value="1" <?php echo switchvalue($attTPK['g_lesu']);?>>
                                                    <label class="onoffswitch-label" for="lesu"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sakit kepala</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="kepala" value="0">
                                                    <input type="checkbox" name="kepala" class="onoffswitch-checkbox" id="kepala" tabindex="1" value="1" <?php echo switchvalue($attTPK['g_pusing']);?>>
                                                    <label class="onoffswitch-label" for="kepala"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Diare</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="diare" value="0">
                                                    <input type="checkbox" name="diare" class="onoffswitch-checkbox" id="diare" tabindex="1" value="1" <?php echo switchvalue($attTPK['g_diare']);?>>
                                                    <label class="onoffswitch-label" for="diare"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Mual dan Muntah</td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="hidden" name="muntah" value="0">
                                                    <input type="checkbox" name="muntah" class="onoffswitch-checkbox" id="muntah" tabindex="1" value="1" <?php echo switchvalue($attTPK['g_mual']);?>>
                                                    <label class="onoffswitch-label" for="muntah"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="tab_riwayat" class="tab-pane fade">
                            <div class="col-xs-12">
                                <div class="table-responsive" id="shadow1">
                                    <table class="table table-striped table-hover table-bordered table-condensed" style="height:100%;">
                                        <tr>
                                            <td colspan="8" class="bg-danger" style="font-size:16px;font-weight:bold;">Catatan Donasi sebelumnya</td>
                                        </tr>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Berat Badan</th>
                                            <th>Tensi</th>
                                            <th>Hemoglobin</td>
                                            <th>Nadi</th>
                                            <th>Suhu</th>
                                            <th>Kantong</th>
                                        </tr>
                                        <?php
                                        $no=1;
                                        $histori=mysqli_query($dbi,"SELECT Kodependonor, Hb, date_format(Tgl, '%Y-%m-%d') AS Tgl1,Tgl,case when Pengambilan='1' then 'Batal' when Pengambilan='0' then 'Berhasil' when Pengambilan='2' Then 'Gagal' END As Pengambilan,beratBadan,nadi,suhu, caraAmbil,tensi,JenisDonor,tempat,Instansi,UPPER(NoKantong) as NoKantong,petugasHB,petugasTensi,user,petugas,donorke
                                                                FROM htransaksi where (KodePendonor='$kode_donor' and Pengambilan<>'-' and date(Tgl)<>CURRENT_DATE)  ORDER BY Tgl DESC limit 10 ");
                                        while($row=mysqli_fetch_assoc($histori)){
                                            echo '
                                                <tr>
                                                    <td>'.$no.'</td>
                                                    <td>'.$row['Tgl1'].'</td>
                                                    <td>'.$row['beratBadan'].'</td>
                                                    <td>'.$row['tensi'].'</td>
                                                    <td>'.$row['Hb'].'</td>
                                                    <td>'.$row['nadi'].'</td>
                                                    <td>'.$row['suhu'].'</td>
                                                    <td>'.$row['NoKantong'].'</td>
                                                    </tdr>';
                                            $no++;
                                        }
                                        ?>
                                        <tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-6 pull-left">
                                                        <table class="table-striped" width="120%" style="margin-left:20px;">
                                                        <tr>
                                                            <td><label>Hasil Seleksi </label></td>
                                                            <td>: </td>
                                                            <td><label class="radio-inline"><input type="radio" checked value="0" name="h_medical" id="h_medicall" style="margin-top:1px;">Lolos</label>
                                                            <label class="radio-inline"><input type="radio" value="1" name="h_medical" id="h_medicalt" style="margin-top:1px;">Tidak Lolos</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label id="alasantl">Alasan Tidak Lolos</label></td>
                                                            <td>: </td>
                                                            <td><select name="alasan" id="alasan" style="width:50mm;">
                                                                    <option value="">Pilih Jika Tidak Lolos</option>
                                                                    <option value="0">Tensi Rendah</option>
                                                                    <option value="1">Tensi Tinggi</option>
                                                                    <option value="2">HB Mengapung</option>
                                                                    <option value="3">HB Melayang</option>
                                                                    <option value="4">HB Tinggi</option>
                                                                    <option value="5">BB Kurang</option>
                                                                    <option value="6">Habis Minum Obat</option>
                                                                    <option value="7">Riwayat Bepergian</option>
                                                                    <option value="8">Kondisi Medis Lain</option>
                                                                    <option value="9">Perilaku Beresiko</option>
                                                                    <option value="10">Alasan Lain</option>
                                                                    <option value="11">Titer Antibody Covid-19 Rendah</option>
                                                                    <option value="12">Hasil IMLTD Reaktif</option>
                                                                    <option value="13">Syarat Apheresis/TPK tidak terpenuhi</option>
                                                                </select></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label id="kembalitl">Kembali Tanggal</label></td>
                                                            <td>: </td>
                                                            <td><input type="text" name="kembalidonor" id="kembali" size="22" value=<?=$today?>></td>
                                                        </tr>
                                                        </table>
                        </div>
                        <div class="col-6 pull-right" style="margin-right:20px;">
                            <input type=submit name="simpan" value="Proses" class="btn btn-primary bayangan">
                            <a href="<?=$lv0?>.php?module=dokter&jenis=0" class="btn btn-danger bayangan" style="color: black;">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
                <script>
                    $(document).ready(function(){
                            document.getElementById("kembali").disabled = true;
                            document.getElementById("alasan").disabled = true;
                           
                            document.getElementById('h_medicalt').addEventListener('click', () => {
                            document.getElementById("kembali").disabled = false;
                            document.getElementById("alasan").disabled = false;
                            document.getElementById("alasan").required = true;
                            document.getElementById("kembali").required = true;
                            document.getElementById("alasan").focus();
                            });
                           
                            document.getElementById('h_medicall').addEventListener('click', () => {
                            document.getElementById("kembali").disabled = true;
                            document.getElementById("alasan").disabled = true;
                            });
                       
                    });
                    
                function konfirmasi() {
                    var tolak = $("input[name='h_medical']:checked").val();
                    if (tolak =="0"){
                        return confirm('Apakah data sudah benar?');
                    }else if (tolak =="1"){
                        return confirm('Hasil Seleksi Tidak Lolos?');
                    }else{
                        alert('Pilih Hasil Seleksi!');
                    }
                }
                </script>
                <script>
                  $( function() {
                    $( "#kembali" ).datepicker({
                      autoclose:true,
                      todayHighlight:true,
                      format:'yyyy-mm-dd',
                      language: 'id'
                    });
                  } );
                  </script>

<style>
.top-buffer { margin-top:20px; }
    a:link {
        color: white;
        background-color: transparent;
        text-decoration: none;
    }
    a:visited {
        color: pink;
        background-color: transparent;
        text-decoration: none;
    }
    a:hover {
        color: red;
        background-color: transparent;
        text-decoration: underline;
    }
    a:active {
        color: yellow;
        background-color: transparent;
        text-decoration: underline;
    }
.blink {
        animation: blinker 1.7s linear infinite;
        color: red;
        font-size: 16px;
        font-weight: bold;
      }
      @keyframes blinker {
        50% {
          opacity: 0;
        }
      }
</style>

