<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION['namauser'];
$namalengkap=$_SESSION['nama_lengkap'];
$jamskr=new DateTime(date("Y-m-d H:i:s"));
$hariini = date("Y-m-d");
?>
<!DOCTYPE html>
<link type="text/css" href="../css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="../css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="../css/style.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<html>
<head>
    <style>
        tr { background-color: #ffffff;}
        .initial { background-color: #ffffff; color:#000000 }
        .normal { background-color: #ffffff; }
        .highlight { background-color: #7CFC00 }
    </style>

    <style>
        .control {
            font-family: arial;
            display: block;
            position: relative;
            padding-left: 30px;
            margin-bottom: 2px;
            padding-top: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        .control input {
            position: absolute;
            z-index: -1;
            opacity: 0;
        }
        .control_indicator {
            position: absolute;
            top: 2px;
            left: 0;
            height: 20px;
            width: 20px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }
        .control-radio .control_indicator {
            border-radius: undefined%;
        }

        .control:hover input ~ .control_indicator,
        .control input:focus ~ .control_indicator {
            background: #cccccc;
        }

        .control input:checked ~ .control_indicator {
            background: #ff0000;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator,
        .control input:checked:focus ~ .control_indicator {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }
        .control_indicator:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }
        .control input:checked ~ .control_indicator:after {
            display: block;
        }
        .control-checkbox .control_indicator:after {
            left: 8px;
            top: 4px;
            width: 3px;
            height: 8px;
            border: solid #ffffff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .control-checkbox input:disabled ~ .control_indicator:after {
            border-color: #7b7b7b;
        }
    </style>

<style type="text/css">
    .styled-select select {
        background-color: #F0FFFF; border: none;width: auto;padding: 3px;font-size: 16px;cursor: pointer;
    }
    table {
    border-collapse: collapse;
    }
    table, th, td {
    border: 1px solid brown;
    }
    body {font-family: "Lato", sans-serif;}
    .tablink {
        background-color: red;
        color: white;
        float: left;
        border: 1px solid brown;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        font-size: 15px;
        width: 16.6%;
    }
    .tablink:hover, .tablink:disabled {
        background-color: #777;
    }
    .tabcontent {
        color: red;
        display: none;
        padding: 10px;
        border: 1px solid brown;
    }
    #visual {background-color:white;}
    #kantong {background-color:white;}
    #pemeriksaan {background-color:white;}
    #pengolahan {background-color:white;}
    #trace {background-color:white;}
    #history {background-color:white;}
</style>

</head>
<body>
<?php
if(isset($_POST['Button']))  {
    $id_timbang=$_GET['id'];
    $nkt=$_GET['nokantong'];
    $mode_kembali=$_GET['mode'];
    $v_rstatus=$_POST['prolis'];
    $v_rtgl = date("Y-m-d H:i:s");
    $v_rberattimbang  =$_POST['berat'];
    $v_rvolume   =$_POST['vol_akhir'];
    $v_rproduk=$_POST['nama_produk'];
    $v_rgolda=$_POST['golda'];
    $v_rtgl_aftap=$_POST['tgl_aftap'];
    $v_rtgl_olah    =$_POST['tgl_komponen'];
    $v_rtgl_ed=$_POST['tgl_ed_produk'];
    $v_rspek_kantong =$_POST['spek_kantong'];
    $v_rselang=(isset($_POST['selang'])) ? 1 : 0;
    $v_rkebocoran=$_POST['bocor'];
    $v_rkode_unik=$_POST['kode_unik'];
    $v_rhemolysis=$_POST['hemolysis'];
    $v_rlipemik=$_POST['lipemik'];
    $v_rikterik=$_POST['ikterik'];
    $v_rkehijauan=$_POST['kehijauan'];
    $v_rbekuan=$_POST['bekuan'];
    $v_rspek_seleksi=$_POST['seleksi'];
    $v_rspek_aftap=$_POST['waktu_aftap'];
    $v_rspek_pengolahan=$_POST['waktu_komponen'];
    $v_rspek_volume=$_POST['volume_ok'];
    $v_rspek_imltd=$_POST['imltd_ok'];
    $v_rjenis_imltd=$_POST['jenis_imltd'];
    $v_rspek_imltd_old=$_POST['jejak_imltd'];
    $v_rspek_kgd='1';
    $v_rspek_kgd_old='1';
    $v_rstatus=$_POST['prolis'];
    $v_rnote=$_POST['catatan'];
    $v_ruser=$_POST['petugas'];
    $v_rchecker=$_POST['dicekoleh'];
    $v_rpengesah=$_POST['disahkanoleh'];
    $hasilrilis=$_POST['hasil_rilis'];

    switch($v_rstatus){
        case '0':$v_rsatus_ket='LULUS';$v_statusktg='1';break;
        case '1':$v_rsatus_ket='TIDAK LULUS';$v_statusktg='2';break;
        case '2':$v_rsatus_ket='LULUS (CATATAN)';$v_statusktg='3';break;
    }
    //Save tamporary pilihan petugas===============================================
    $checker=$_POST['dicekoleh'];
    $pengesah=$_POST['disahkanoleh'];
    $cetak=$_POST['cetak'];
    $cek_tmpudd=1;
    $cek_tmpudd=mysql_num_rows(mysql_query("Select * from tempudd where modul='PROLIS'"));
    if ($cek_tmpudd==0) {
            $tambah_tmp=mysql_query("INSERT INTO tempudd (modul,dokter,petugas1,petugas2, petugas3) values ('PROLIS','$namalengkap','$checker','$pengesah', '$cetak')");
    } else {
            $tambah_tmp=mysql_query("UPDATE tempudd  SET dokter='$namalengkap',petugas1='$checker', petugas2='$pengesah', petugas3='$cetak' where modul='PROLIS'");
    }
    $cek="select `rnokantong` ,`rnotrans` from `release` where `rnokantong`='$nkt'";
    $cek1=mysql_fetch_assoc(mysql_query($cek));
    $notrans_upd=$cek1['rnotrans'];
    if ($cek1['rnokantong']==$nkt){
        //=======Audit Trial====================================================================================
        $log_mdl ='PROLIS';
        $log_aksi='Update Release Produk nomor kantong : '.$nkt.', Volume : '.$v_rvolume.' ml, Status Release: '.$v_rsatus_ket;
        include_once "user_log.php";
        //=====================================================================================================
        //Save Upd release table
        $sql="UPDATE `release` SET
            `rtgl`            ='$v_rtgl',
              `rberat_timbang`='$v_rberattimbang',
              `rvolume`         ='$v_rvolume',
              `rspek_volume`    ='$v_rspek_volume',
              `rproduk`        ='$v_rproduk',
              `rgolda`        ='$v_rgolda',
              `rtgl_aftap`    ='$v_rtgl_aftap',
              `rtgl_olah`        ='$v_rtgl_olah',
              `rtgl_ed`        ='$v_rtgl_ed',
              `rspek_kantong`    ='$v_rspek_kantong',
              `rselang`         ='$v_rselang',
              `rkebocoran`    ='$v_rkebocoran',
              `rkode_unik`    ='$v_rkode_unik',
              `rhemolysis`    ='$v_rhemolysis',
              `rlipemik`         ='$v_rlipemik',
              `rikterik`        ='$v_rikterik',
              `rkehijauan`    ='$v_rkehijauan',
              `rbekuan`         ='$v_rbekuan',
              `rspek_seleksi`    ='$v_rspek_seleksi',
              `rspek_aftap`     ='$v_rspek_aftap',
              `rspek_pengolahan`='$v_rspek_pengolahan',
              `rspek_imltd`     ='$v_rspek_imltd',
              `rjenis_imltd`    ='$v_rjenis_imltd',
              `rspek_imltd_old`='$v_rspek_imltd_old',
              `rspek_kgd`     ='$v_rspek_kgd',
              `rspek_kgd_old`    ='$v_rspek_kgd_old',
              `rstatus`         ='$v_rstatus',
              `rsatus_ket`    ='$v_rsatus_ket',
              `rnote`         ='$v_rnote',
              `ruser`            ='$v_ruser',
              `rchecker`        ='$v_rchecker',
              `rpengesah`     ='$v_rpengesah',
              `on_update`     ='$v_rtgl'
              WHERE
            `rnotrans`        ='$notrans_upd' and `rnokantong`    ='$nkt'";
        $qact=mysql_query($sql);
        $sql="UPDATE `timbang_darah` SET `konfirm`='1',`waktu_konfirm`='$v_rtgl',`notrans`='$v_rnotrans' where (`id`='$id_timbang') or (`nokantong`)='$nkt'";
        $update=mysql_query($sql);
        $qupd="UPDATE stokkantong set `tgl_release`='$v_rtgl',`hasil_release`='$v_statusktg',`volume`='$hasilrilis' where `noKantong`='$nkt'";
        $qupd1=mysql_query($qupd);
        echo "PROSES <i>UPDATE</i> PRODUK RELEASE BERHASIL";
    }else{
        //Generated NoTransaksi===============================================
        $sql_r    = mysql_query("SELECT MAX(CONVERT(rnotrans, SIGNED INTEGER)) AS Kode FROM `release`");
        $dta_r    = mysql_fetch_assoc($sql_r);
        $int_r  = (int)($dta_r[Kode]);
        $int_no=$int_r;
        $int_no_inc=(int)$int_no+1;
        $j_nol= 10-(strlen(strval($int_no_inc)));
        for ($i=0; $i<$j_nol; $i++){$no_tmp .="0";}
        $v_rnotrans = $no_tmp.$int_no_inc;
        //echo "No. Transaksi :  ".$notrans." Tanggal Periksa : ".$today1." (".date_default_timezone_get().")<br>";
        //------------ END Generate no transaksi ---------------

        
        //=======Audit Trial====================================================================================
        $log_mdl ='PROLIS';
        $log_aksi='Release Produk nomor kantong : '.$nkt.', Status Release: '.$v_rsatus_ket;
        include_once "user_log.php";
        //=====================================================================================================

        //Save Add release table
        $sql="INSERT INTO `release`(`rnotrans`, `rnokantong`, `rtgl`,
          `rberat_timbang`, `rvolume`, `rspek_volume`, `rproduk`, `rgolda`, `rtgl_aftap`, `rtgl_olah`, `rtgl_ed`,
          `rspek_kantong`, `rselang`, `rkebocoran`, `rkode_unik`, `rhemolysis`, `rlipemik`, `rikterik`,
          `rkehijauan`, `rbekuan`, `rspek_seleksi`, `rspek_aftap`, `rspek_pengolahan`, `rspek_imltd`, `rjenis_imltd`,
          `rspek_imltd_old`, `rspek_kgd`, `rspek_kgd_old`, `rstatus`, `rsatus_ket`, `rnote`, `ruser`,
          `rchecker`, `rpengesah`)
          VALUES ('$v_rnotrans','$nkt','$v_rtgl',
          '$v_rberattimbang','$v_rvolume','$v_rspek_volume','$v_rproduk','$v_rgolda','$v_rtgl_aftap','$v_rtgl_olah','$v_rtgl_ed',
          '$v_rspek_kantong','$v_rselang','$v_rkebocoran','$v_rkode_unik','$v_rhemolysis','$v_rlipemik','$v_rikterik',
          '$v_rkehijauan','$v_rbekuan','$v_rspek_seleksi','$v_rspek_aftap','$v_rspek_pengolahan','$v_rspek_imltd','$v_rjenis_imltd',
          '$v_rspek_imltd_old','$v_rspek_kgd','$v_rspek_kgd_old','$v_rstatus','$v_rsatus_ket','$v_rnote','$v_ruser',
          '$v_rchecker','$v_rpengesah')";
        $qact=mysql_query($sql);
        $sql="UPDATE `timbang_darah` SET `konfirm`='1',`waktu_konfirm`='$v_rtgl',`notrans`='$v_rnotrans' where (`id`='$id_timbang') or (`nokantong`)='$nkt'";


        $update=mysql_query($sql);
        $qupd="UPDATE stokkantong set `tgl_release`='$v_rtgl',`hasil_release`='$v_statusktg',`volume`='$hasilrilis' where `noKantong`='$nkt'";
        $qupd1=mysql_query($qupd);
        echo "PROSES RELEASE PRODUK BERHASIL";
    }

    //If ($cetak=='1'){
    //    echo "<br> MENCETAK<br>";
    //} else{
    //    echo "<br>TIDAK MENCETAK<br>";
    //}
    if ($mode_kembali==1){
        echo "<meta http-equiv='refresh' content='2;url=pmiqa.php?module=timbang'>";
    } else{
	//sebelumnya
        // echo "<meta http-equiv='refresh' content='2;url=qa_label_cetak.php?noKantong=$nkt'>";
	//diganti
        $tipe_barcode='C128';
        //?kantong='.$v_kantong.'&tipe='.$v_barcodetipe
        echo "<meta http-equiv='refresh' content='2;url=release/qa_release_label_90cm.php?kantong=$nkt&tipe=C128'>";
    }
} //post
    ?>
    
    <button class="tablink" onclick="bukatab('kantong', this, 'Blue')" id="defaultOpen">Kantong & Donasi</button>
    <button class="tablink" onclick="bukatab('pemeriksaan', this, 'Blue')">Pemeriksaan</button>
    <button class="tablink" onclick="bukatab('pengolahan', this, 'Blue')">Pengolahan</button>
    <button class="tablink" onclick="bukatab('trace', this, 'Blue')">Trace Kantong</button>
    <button class="tablink" onclick="bukatab('history', this, 'Blue')">Jejak Pemeriksaan</button>
    <button class="tablink" onclick="bukatab('visual', this, 'Blue')">Visual & Timbangan</button>

<form name="form_prolis" align="left" method="post" action="<?echo $PHPSELF?>">
    <div id="kantong" class="tabcontent">
        <font size="4" color=00008B><br>DATA KANTONG DAN DONASI</font>
        <? include "release/qa_release_donasi.php";?>
    </div>

    <div id="pemeriksaan" class="tabcontent">
        <font size="4" color=00008B><br>DATA PEMERIKSAAN IMLTD & KGD</font>
        <? include "release/qa_release_periksa.php";?>
    </div>

    <div id="pengolahan" class="tabcontent">
        <font size="4" color=00008B><br>DATA PENGOLAHAN KOMPONEN DARAH</font>
        <? include "release/qa_release_komponen.php";?>
    </div>

    <!--div id="trace" class="tabcontent">
        <font size="4" color=00008B><br>REKAM JEJAK KANTONG</font><br>
        <? //include "release/qa_release_trace.php";?>
    </div-->

    <div id="history" class="tabcontent">
        <? include "release/qa_release_periksa_last.php";?>
    </div>

    <div id="visual" class="tabcontent">
        <font size="4" color=00008B><br>PENGAMATAN VISUAL dan PENIMBANGAN BERAT KOMPONEN DARAH</font>
        <table cellpadding=3 cellspacing=3 width="100%" style="border: 0px; border-color: #ffffff;">
            <tr>
                <td valign="top">
        <table width="100%" cellpadding="1" cellspacing="1">
                <tr><td valign="top">Identitas dan pemakaian kantong darah sesuai spesifikasi
        <td>
                       <input type="radio" required=""  id="spek_kantong1" name="spek_kantong" value="1" checked>
                              <label for="spek_kantong1">Ya</label>
               <input type="radio" required="" id="spek_kantong2" name="spek_kantong" value="0"  >
                              <label for="spek_kantong2">Tidak</label>
                 
                </td>


        <tr><td valign="top">Seleksi donor memenuhi kriteria</td>
        <td>
                       <input type="radio" required=""  id="seleksi1" name="seleksi" value="1"  checked>
                              <label for="seleksi1">Ya</label>
               <input type="radio" required="" id="seleksi2" name="seleksi" value="0"  >
                              <label for="seleksi2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Waktu pengambilan terpenuhi</td>
        <td>
                       <input type="radio" required=""  id="waktu_aftap1" name="waktu_aftap" value="1"  checked>
                              <label for="waktu_aftap1">Ya</label>
               <input type="radio" required="" id="waktu_aftap2" name="waktu_aftap" value="0"  >
                              <label for="waktu_aftap2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Waktu selesai pengolahan terpenuhi</td>
        <td>
                       <input type="radio" required=""  id="waktu_komponen1" name="waktu_komponen" value="1"  checked>
                              <label for="waktu_komponen1">Ya</label>
               <input type="radio" required="" id="waktu_komponen2" name="waktu_komponen" value="0"  >
                              <label for="waktu_komponen2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Pemeriksaan donasi sebelumnya terpenuhi (bila ada)</td>
        <td>
                       <input type="radio" required=""  id="jejak_imltd1" name="jejak_imltd" value="1"  checked>
                              <label for="jejak_imltd1">Ya</label>
               <input type="radio" required="" id="jejak_imltd2" name="jejak_imltd" value="0"  >
                              <label for="jejak_imltd2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Hasil pemeriksaan memenuhi spesifikasi</td>
        <td>
                       <input type="radio" required="" id="imltd_ok1" name="imltd_ok" value="1"  checked>
                              <label for="imltd_ok1">Ya</label>
               <input type="radio" required="" id="imltd_ok2" name="imltd_ok" value="0"  >
                              <label for="imltd_ok2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Tidak ada tanda-tanda visual kebocoran kantong</td>
        <td>
                       <input type="radio" required="" id="bocor1" name="bocor" value="1"  checked>
                              <label for="bocor1">Ya</label>
               <input type="radio" required="" id="bocor2" name="bocor" value="0"  >
                              <label for="bocor2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Kode unik sesuai dengan spesifikasi</td>
        <td>
                       <input type="radio" required="" id="kode_unik1" name="kode_unik" value="1"  checked>
                              <label for="kode_unik1">Ya</label>
               <input type="radio" required="" id="kode_unik2" name="kode_unik" value="0"  >
                              <label for="kode_unik2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Selang kantong sesuai dengan spesifikasi</td>
        <td>
                       <input type="radio" required="" id="selang1" name="selang" value="1"  checked>
                              <label for="selang1">Ya</label>
               <input type="radio" required="" id="selang2" name="selang" value="0"  >
                              <label for="selang2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Tidak ada Hemolysis</td>
        <td>
                       <input type="radio" required="" id="hemolysis1" name="hemolysis" value="1"  checked>
                              <label for="hemolysis1">Ya</label>
               <input type="radio" required="" id="hemolysis2" name="hemolysis" value="0"  >
                              <label for="hemolysis2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Tidak Lipemik</td>
        <td>
                       <input type="radio" required="" id="lipemik1" name="lipemik" value="1"  checked>
                              <label for="lipemik1">Ya</label>
               <input type="radio" required="" id="lipemik2" name="lipemik" value="0"  >
                              <label for="lipemik2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Tidak Ikterik</td>
        <td>
                       <input type="radio" required="" id="ikterik1" name="ikterik" value="1"  checked>
                              <label for="ikterik1">Ya</label>
               <input type="radio" required="" id="ikterik2" name="ikterik" value="0"  >
                              <label for="ikterik2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Plasma tidak Kehijauan</td>
        <td>
                       <input type="radio" required="" id="kehijauan1" name="kehijauan" value="1"  checked>
                              <label for="kehijauan1">Ya</label>
               <input type="radio" required="" id="kehijauan2" name="kehijauan" value="0"  >
                              <label for="kehijauan2">Tidak</label>
                 
                </td>


        <tr><td valign="top">Tidak ada bekuan pada Sel Darah Merah</td>
        <td>
                       <input type="radio" required="" id="bekuan1" name="bekuan" value="1"  checked>
                              <label for="bekuan1">Ya</label>
               <input type="radio" required="" id="bekuan2" name="bekuan" value="0"  >
                              <label for="bekuan2">Tidak</label>
                 
                </td>

        <tr><td valign="top">Volume produk sesuai spesifikasi</td>
        <td>
                       <input type="radio" required="" id="volume_ok1" name="volume_ok" value="1"  checked>
                              <label for="bekuan1">Ya</label>
               <input type="radio" required="" id="volume_ok2" name="volume_ok" value="0"  >
                              <label for="bekuan2">Tidak</label>
                 
                </td>

        </table>




                <td valign="top">
                    <table width="100%" cellpadding="1" cellspacing="1">
                        <tr><td style="background-color: mistyrose" colspan="2">Nomor Kantong</td><td><?=$nkt?></td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Status kantong</td><td><?=$posisikantong.' - '.$statuskantong?> </td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Nama Produk</td><td><?=$jeniskomponen.' - '.$namakomponen?></td></tr>
                            <input type='hidden' name='nama_produk' value='<?=$jeniskomponen?>'>
            <tr><td style="background-color: mistyrose" colspan="2">Gol Darah</td><td><?=$golongandarah_donasi?>(<?=$rhesus_donasi?>)</td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Tanggal ED Produk</td><td><?=$tgledkomponen?></td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Berat Kantong Kosong</td><td><?=$beratkantongkosong?> gram</td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Berat jenis komponen</td><td><?=$beratjenis?></td></tr>
                        <tr><td style="background-color: mistyrose" rowspan="4">Hasil<br>penimbangan<br>produk</td></tr>
                        <tr><td style="background-color: mistyrose">Tanggal</td><td><?=$tgltimbang?></td></tr>
                        <tr><td style="background-color: mistyrose">Petugas</td><td><?=$namapetugastimbang.' ('.$usertimbang.') '?></td></tr>
                        <tr><td style="background-color: mistyrose">Berat</td><td><input required="" name="berat" type="text" size="7" style="font-family:monospace;" value=<?=$berattimbang?>>gram</td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Volume standar</td><td><?=$vol_min.' - '.$vol_max;?> ml</td></tr>
<!-- Hitung berat -->
<?
if ($jeniskomponen=="WB"){
$ml=(($berattimbang )-$antikoagulant-$beratkantongkosong)/$beratjenis;
} else {
$ml=(($berattimbang )-$beratkantongkosong)/$beratjenis;
}
?>

                        <tr><td style="background-color: mistyrose" colspan="2">Volume komponen darah </td><td><input required="" type="text" name="vol_akhir" size="10" style="font-family:monospace;" value="<?=round($ml,3)?>" required placeholder="<?=$volume?>"> pembulatan :  <input type="text" name="hasil_rilis" size="7" style="font-family:monospace;" value=<?=round($ml,0)?>>
                        <?if ($var_volume_kantong=='1'){?>
                                ml </td></tr>
                        <?} else {?>
                            ml <font color="blue"><b>&radic;</b></font> </td></tr>
                        <?}?>

                        <tr><td style="background-color: mistyrose" colspan="2"><b>STATUS RELEASE</b></td>
                            <td>
                                <?
                                $sel1='';$sel2='';
                                //if (($var_volume_kantong=='1') or ($var_kgd_old=='1') or ($var_ed_kantong=='1') or ($var_kgd=='1') or ($var_imltd=='1'))
                                if (($var_volume_kantong=='1') or ($var_ed_kantong=='1') or  ($var_imltd=='1') or ($var_imltd_old=='1'))
                                {$sel1='';$sel3='selected';}
                                ?>
                                <select id="prolis" name="prolis" class="styled-select" required="">
                                    <option disabled selected value></option>
                                    <!-- <option value="-" <?=$sel3?>>-</option> -->
                                    <option value="0" <?=$sel2?>>LULUS</option>
                                    <option value="1" <?=$sel1?>>TIDAK LULUS</option>
                                    <option value="2" >DILULUSKAN DENGAN CATATAN</option>
                                </select>
                            </td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Catatan</td><td><input type="text" name="catatan"></td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Petugas</td><td><?echo $namalengkap;?></td>
                            <input type="hidden" name="petugas" value=<?=$namauser?>></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Dicek oleh</td>
                            <td>
                                <select name="dicekoleh" class="styled-select"> <?
                                    $user1="select * from user where multi like '%qa%' order by nama_lengkap ASC";
                                    $do1=mysql_query($user1);
                                    while($data1=mysql_fetch_assoc($do1)) {
                                        if ($data1[id_user]==$data_combo[petugas1]){
                                            $select=" selected";
                                        } else{
                                            $select="";
                                        }?>
                                        <option value="<?=$data1[id_user]?>"<?=$select?>><?=$data1[nama_lengkap]?></option><?
                                    }?>
                                </select>
                            </td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Disahkan oleh</td>
                            <td>
                                <select name="disahkanoleh" class="styled-select"> <?
                                    $user1="select * from user where multi like '%qa%' order by nama_lengkap ASC";
                                    $do1=mysql_query($user1);
                                    while($data1=mysql_fetch_assoc($do1)) {
                                        if ($data1[id_user]==$data_combo[petugas2]){
                                            $select=" selected";
                                        } else{
                                            $select="";
                                        }?>
                                        <option value="<?=$data1[id_user]?>"<?=$select?>><?=$data1[nama_lengkap]?></option><?
                                    }?>
                                </select>
                            </td></tr>


                    </table>

                </td>
            <tr>
        </table>
    </div>
    <?
    if ($mode_kembali==1){
        ?><a href="pmiqa.php?module=timbang"class="swn_button_blue">Kembali</a><?
    }else{
        ?><a href="pmiqa.php?module=release"class="swn_button_blue">Kembali</a><?
    }
    ?>

    <a href="pmiqa.php?module=input_qa"class="swn_button_blue">Kembali ke awal</a>
    <!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
    <button id="lanjut-btn" type="button" class="swn_button_blue">Lanjut</button>
    <input id="simpan-btn" type="submit" name="Button" value="Simpan" title="Proses kantong" class="swn_button_red">
    <!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
    <? if ($data_combo['petugas3']=='1'){?>
        <input type="checkbox" class="checkbox-custom" name="cetak" id="cetak" value="1" checked>
            <label for="cetak" class="checkbox-custom-label">Cetak Label saat menyimpan</label><br>
    <?} else {?>
        <input type="checkbox" class="checkbox-custom" name="cetak" id="cetak" value="1">
        <label for="cetak" class="checkbox-custom-label">Cetak Label saat menyimpan</label><br>
    <?}?>
</form>

<script>
/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
let tabList = ["kantong", "pemeriksaan", "pengolahan", "trace", "history", "visual"];
let curTabIdx = 0;
let simpanBtn = document.getElementById("simpan-btn");
simpanBtn.disabled = true;

let tabLinks = document.getElementsByClassName("tablink");
/* disable semua tabLinks, kecuali yang pertama */
for (let i=1; i < tabLinks.length; i++) {
  tabLinks[i].disabled = true;
}

let lanjutBtn = document.getElementById("lanjut-btn");
lanjutBtn.addEventListener("click", (e) => {
  if (curTabIdx === tabLinks.length) return;
  curTabIdx += 1;
  tabLinks[curTabIdx].disabled = false;
  bukatab(tabList[curTabIdx], tabLinks[curTabIdx], "Blue");
});
/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
    
function bukatab(namatab, elmnt, color) {
    /* !!!!!!!!!!!!!!!!!! */
    curTabIdx = tabList.indexOf(namatab);
    lanjutBtn.disabled = (curTabIdx === tabList.length - 1);
    simpanBtn.disabled = (curTabIdx !== tabList.length - 1);
    /* !!!!!!!!!!!!!!!!!! */
    
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
    }
    document.getElementById(namatab).style.display = "block";
    elmnt.style.backgroundColor = color;
}
document.getElementById("defaultOpen").click();
</script>
</body>
</html>

