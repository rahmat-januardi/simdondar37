<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
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
        .checkbox-custom {
            display: none;
        }
        .checkbox-custom-label {
            display: inline-block;
            position: relative;
            vertical-align: middle;
            margin: 5px;
            cursor: pointer;
        }
        .checkbox-custom + .checkbox-custom-label:before {
            content: '';
            background: #fff;
            border-radius: 5px;
            border: 2px solid #ff0000;
            display: inline-block;
            vertical-align: middle;
            width: 8px; height: 8px;
            padding: 2px; margin-right: 8px;
        }
        .checkbox-custom:checked + .checkbox-custom-label:after {
            content: "";
            padding: 2px;
            position: absolute;
            width: 1px;
            height: 4px;
            border: solid blue;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
            top: 2px; left: 4px;
        }
    </style>
<style type="text/css">.styled-select select {background-color: #FCF9F9; border: none;width: auto;padding: 3px;font-size: 15px;cursor: pointer; }</style>
<style>
    table {
    border-collapse: collapse;
    }
    table, th, td {
    border: 1px solid brown;
    }
</style>
<style>
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
.tablink:hover {
    background-color: #777;
}
/* Style the tab content */
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
    $v_rspek_kantong=$_POST['spek_kantong'];
    $v_rselang=$_POST['selang'];
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

    switch($v_rstatus){
        case '0':$v_rsatus_ket='LULUS';$v_statusktg='1';break;
        case '1':$v_rsatus_ket='TIDAK LULUS';$v_statusktg='2';break;
        case '2':$v_rsatus_ket='LULUS (CATATAN)';$v_statusktg='3';break;
    }

    //Generated NoTransaksi===============================================
    $sql_r	= mysql_query("SELECT MAX(CONVERT(rnotrans, SIGNED INTEGER)) AS Kode FROM `release`");
    $dta_r	= mysql_fetch_assoc($sql_r);
    $int_r  = (int)($dta_r[Kode]);
    $int_no=$int_r;
    $int_no_inc=(int)$int_no+1;
    $j_nol= 10-(strlen(strval($int_no_inc)));
    for ($i=0; $i<$j_nol; $i++){$no_tmp .="0";}
    $v_rnotrans = $no_tmp.$int_no_inc;
    //echo "No. Transaksi :  ".$notrans." Tanggal Periksa : ".$today1." (".date_default_timezone_get().")<br>";
    //------------ END Generate no transaksi ---------------

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
    //=======Audit Trial====================================================================================
    $log_mdl ='PROLIS';
    $log_aksi='Release Produk nomor kantong : '.$nkt.', Status Release: '.$ket_release_status;
    include_once "user_log.php";
    //=====================================================================================================

    //Save relase table
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
    $qupd="UPDATE stokkantong set `tgl_release`='$v_rtgl',`hasil_release`='$v_statusktg' where `noKantong`='$nkt'";
    $qupd1=mysql_query($qupd);
    //If ($cetak=='1'){
    //    echo "<br> MENCETAK<br>";
    //} else{
    //    echo "<br>TIDAK MENCETAK<br>";
    //}
    if ($mode_kembali==1){
        echo "<meta http-equiv='refresh' content='5;url=pmiqa.php?module=timbang'>";
    } else{
        echo "<meta http-equiv='refresh' content='5;url=pmiqa.php?module=release'>";
    }
} //post
    ?>
    <button class="tablink" onclick="bukatab('visual', this, 'Blue')" id="defaultOpen">Visual & Timbangan</button>
    <button class="tablink" onclick="bukatab('kantong', this, 'Blue')">Kantong & Donasi</button>
    <button class="tablink" onclick="bukatab('pemeriksaan', this, 'Blue')">Pemeriksaan</button>
    <button class="tablink" onclick="bukatab('pengolahan', this, 'Blue')">Pengolahan</button>
    <button class="tablink" onclick="bukatab('trace', this, 'Blue')">Trace Kantong</button>
	<button class="tablink" onclick="bukatab('history', this, 'Blue')">Jejak Pemeriksaan</button>

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

    <div id="trace" class="tabcontent">
        <font size="4" color=00008B><br>REKAM JEJAK KANTONG</font><br>
        <? include "release/qa_release_trace.php";?>
    </div>

    <div id="history" class="tabcontent">
        <? include "release/qa_release_periksa_last.php";?>
    </div>

    <div id="visual" class="tabcontent">
        <font size="4" color=00008B><br>PENGAMATAN VISUAL dan PENIMBANGAN BERAT KOMPONEN DARAH</font>
        <table cellpadding=3 cellspacing=3 width="100%" style="border: 0px; border-color: #ffffff;">
            <tr>
                <td valign="top">
                    <input type="checkbox" class="checkbox-custom" name="spek_kantong" id="spek_kantong" value="1" checked>
                          <label for="spek_kantong" class="checkbox-custom-label">Identitas dan pemakaian kantong darah sesuai spesifikasi</label><br>
                    <input type="checkbox" class="checkbox-custom" name="seleksi" id="seleksi" value="1" checked>
                    <label for="seleksi" class="checkbox-custom-label">Seleksi donor memenuhi kriteria</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="bocor" id="bocor" value="1" checked>
                           <label for="bocor" class="checkbox-custom-label">Tidak ada tanda-tanda visual kebocoran kantong</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="kode_unik" id="kode_unik" value="1" checked>
                           <label for="kode_unik" class="checkbox-custom-label">Kode unik sesuai dengan spesifikasi</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="selang" id="selang" value="1" checked>
                           <label for="selang" class="checkbox-custom-label">Selang kantong sesuai dengan spesifikasi</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="hemolysis" id="hemolysis" value="1" checked>
                           <label for="hemolysis" class="checkbox-custom-label">Tidak ada Hemolysis</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="lipemik" id="lipemik" value="1" checked>
                           <label for="lipemik" class="checkbox-custom-label">Tidak Lipemik</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="ikterik" id="ikterik" value="1" checked>
                           <label for="ikterik" class="checkbox-custom-label">Tidak Ikterik</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="kehijauan" id="kehijauan" value="1" checked>
                           <label for="kehijauan" class="checkbox-custom-label">Plasma tidak Kehijauan</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="bekuan" id="bekuan" value="1" checked>
                           <label for="bekuan" class="checkbox-custom-label">Tidak ada bekuan pada Sel Darah Merah</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="waktu_aftap" id="waktu_aftap" value="1" checked>
                           <label for="waktu_aftap" class="checkbox-custom-label">Waktu pengambilan terpenuhi</label><br>
                    <input type="checkbox" class="checkbox-custom"  name="waktu_komponen" id="waktu_komponen" value="1" checked>
                           <label for="waktu_komponen" class="checkbox-custom-label">Waktu selesai pengolahan terpenuhi</label><br>
                    <?if ($var_volume_kantong=='0'){
                        ?><input type="checkbox" name="volume_ok" class="checkbox-custom"  id="volume_ok" value="1" checked>
                             <label for="volume_ok" class="checkbox-custom-label">Volume produk sesuai spesifikasi</label><br><?
                     } else {
                         ?><input type="checkbox" name="volume_ok" class="checkbox-custom"  id="volume_ok" value="0">
                             <label for="volume_ok" class="checkbox-custom-label">Volume produk sesuai spesifikasi</label><br><?
                     }?>

                    <input type="checkbox" name="imltd_ok" class="checkbox-custom"  id="imltd_ok" value="1" checked>
                           <label for="imltd_ok" class="checkbox-custom-label">Hasil pemeriksaan memenuhi spesifikasi</label><br>
                    <input type="checkbox" name="jejak_imltd" class="checkbox-custom"  id="jejak_imltd" value="1" checked>
                           <label for="jejak_imltd" class="checkbox-custom-label">Pemeriksaan donasi sebelumnya terpenuhi (bila ada)</label><br>
                </td>
                <td valign="top">
                    <table width="100%" cellpadding="1" cellspacing="1">
                        <tr><td style="background-color: mistyrose" colspan="2">Nomor Kantong</td><td><?=$nkt?></td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Status kantong</td><td><?=$posisikantong.' - '.$statuskantong?> </td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Nama Produk</td><td><?=$jeniskomponen.' - '.$namakomponen?></td></tr>
                            <input type='hidden' name='nama_produk' value='<?=$jeniskomponen?>'>
                        <tr><td style="background-color: mistyrose" colspan="2">Tanggal ED Produk</td><td><?=$tgledkomponen?></td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Berat Kantong Kosong</td><td><?=$beratkantongkosong?> gram</td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Berat jenis komponen</td><td><?=$beratjenis?></td></tr>
                        <tr><td style="background-color: mistyrose" rowspan="4">Hasil<br>penimbangan<br>produk</td></tr>
                        <tr><td style="background-color: mistyrose">Tanggal</td><td><?=$tgltimbang?></td></tr>
                        <tr><td style="background-color: mistyrose">Petugas</td><td><?=$namapetugastimbang.' ('.$usertimbang.') '?></td></tr>
                        <tr><td style="background-color: mistyrose">Berat</td><td><input name="berat" type="text" size="7" value=<?=$berattimbang?>>gram</td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Volume standar</td><td><?=$vol_min.' - '.$vol_max;?> ml</td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Volume komponen darah </td><td><input type="text" name="vol_akhir" size="5" value=<?=$volumekomponendarah?>> pembulatan :  <?=round($volumekomponendarah,0)?>
                        <?if ($var_volume_kantong=='1'){?>
                                ml </td></tr>
                        <?} else {?>
                            ml <font color="blue"><b>&radic;</b></font> </td></tr>
                        <?}?>

                        <tr><td style="background-color: mistyrose" colspan="2"><b>STATUS RELEASE</b></td>
                            <td>
                                <?
                                $sel1='';$sel2='';
                                if (($var_volume_kantong=='1') or ($var_kgd_old=='1') or ($var_ed_kantong=='1') or ($var_kgd=='1') or ($var_imltd=='1'))
                                {$sel1='';$sel2='selected';}
                                ?>
                                <select id="prolis" name="prolis" class="styled-select">
                                    <option value="0" <?=$sel1?>>LULUS</option>
                                    <option value="1" <?=$sel2?>>TIDAK LULUS</option>
                                    <option value="2" >DILULUSKAN DENGAN CATATAN</option>
                                </select>
                            </td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Catatan</td><td><input type="text" name="catatan"></td></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Petugas</td><td><?echo $namalengkap;?></td>
                            <input type="hidden" name="petugas" value=<?=$namauser?>></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Dicek oleh</td>
                            <td>
                                <select name="dicekoleh" class="styled-select"> <?
                                    $user1="select * from user order by nama_lengkap";
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
                                    $user1="select * from user order by nama_lengkap";
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
    <input type="submit" name="Button" value="Simpan" title="Proses kantong" class="swn_button_red">
    <? if ($data_combo['petugas3']=='1'){?>
        <input type="checkbox" class="checkbox-custom" name="cetak" id="cetak" value="1" checked>
            <label for="cetak" class="checkbox-custom-label">Cetak Label saat menyimpan</label><br>
    <?} else {?>
        <input type="checkbox" class="checkbox-custom" name="cetak" id="cetak" value="1">
        <label for="cetak" class="checkbox-custom-label">Cetak Label saat menyimpan</label><br>
    <?}?>
</form>

<script>
function bukatab(namatab,elmnt,color) {
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
