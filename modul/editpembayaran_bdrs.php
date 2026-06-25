<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_lahir.js"></script>
<script type="text/javascript" src="js/tgl_butuh.js"></script>
<script type="text/javascript" src="js/disable_enter.js"></script>

<?php

include('clogin.php');
include('config/db_connect.php');
$lv0 = $_SESSION[leveluser];

if (isset($_POST[submit])) {
    $noid        = $_POST["id"];
    $noform    = $_POST["noform"];
    $no_rm            = $_POST["no_rm"];
    $reg_rs    = $_POST["reg_rs"];

    $nama_bagian    = mysql_real_escape_string($_POST["nama_bagian"]);
    $nama_kelas     = mysql_real_escape_string($_POST["nama_kelas"]);
    $nama_ruangan    = mysql_real_escape_string($_POST["nama_ruangan"]);
    $nama_dokter    = mysql_real_escape_string($_POST["nama_dokter"]);
    $diagnosa    = mysql_real_escape_string($_POST["diagnosa"]);
    $jenis        = $_POST["jenis"];
    $noreglayanan    = $_POST["noreglayanan"];

    $jml_kantong    = $_POST["kantong"];
    $jml_duit    = $_POST["duit"];
    $kode_biaya    = $_POST["kode_biaya"];
    $harga        = $_POST["harga"];
    $jnspermintaan    = $_POST["jnspermintaan"];

    $shift       = $_POST["shift"];
    $mintaid    = $_POST["idminta"];
    $namauser     = $_SESSION[namauser];
    $sekarang    = date("Y-m-d h:m:s");

    $jumlah    = $_POST["jumlah"];
    $total_terbayar = $_POST[jumlah] * $_POST[bppd];
    $nama_rs     = $_POST["nama_rs"];
    $bppd         = $_POST["bppd"];


    //tabel pembayaran NoTrans 	Tgl 	Jumlah 
    // tabel dpembayran   noForm 	BiayaLD 	tgl 	TotPotongan 	TotDibayar 	totPeriksa
    //tabel kwitansi      NoForm 	jumlah 	Tgl 	petugas 	nokantong 	shift 	tempat 	kodebiaya
    // notrans 	kodeBrg 	jum 	subTotal 	namabrg 	harga 	temphj 	no_kantong 	petugas 	shift 	tgl_keluar 	tempat
    $jenis_biaya1 = mysql_query("select * from biaya where Kode='$kode_biaya'");
    $jenis_biaya = mysql_fetch_array($jenis_biaya1);

    $tambahlagi1 = mysql_query("UPDATE stokkantong inner join kirimbdrs on stokkantong.noKantong=kirimbdrs.nokantong set
stokkantong.stat2='$nama_rs' where kirimbdrs.no_permintaan='$noform'");

    $tambahlagi2 = mysql_query("UPDATE kwitansi_bdrs set rs='$nama_rs', biaya_satuan='$bppd', jumlah_dist='$jumlah', total_biaya='$total_terbayar' where noform='$noform'");

    $tambahlagi3 = mysql_query("UPDATE kirimbdrs set bdrs='$nama_rs', nama_bdrs='$nama_rs' where no_permintaan='$noform'");
    if ($tambahlagi2) {
        echo "Data Telah berhasil di-Update <br> ";
        switch ($lv0) {
            case "kasir2": ?>
                <META http-equiv="refresh" content="0; url=pmikasir2.php?module=rekap_pembayaran3">
        <?
            default:
                echo "$lv0 ANDA tidak memiliki hak akses";
        }
    }
    $_POST['periksa'] = "";
}
if (isset($_GET[kode])) {
    $sql = "SELECT noform, jumlah_dist, biaya_satuan, total_biaya, rs FROM kwitansi_bdrs  WHERE noform='$_GET[kode]'";
    $perintah = mysql_query($sql);
    $nrow = 0;
    if ($perintah) {
        $nrow = mysql_num_rows($perintah);
        $row = mysql_fetch_assoc($perintah);
        $idminta = $_GET[id];
    }
    if ($row < 1) {
        echo "No Form yang anda masukkan belum terdaftar";
        ?>
        <META http-equiv="refresh" content="2; url=pmikasir2.php?module=rekap_pembayaran3">
    <?
    } else { ?>


        <?

        $duit = mysql_fetch_assoc(mysql_query("SELECT * FROM kwitansi_bdrs WHERE noform='$r[noform]'"));

        ?>

        <br>
        <form name="reg" autocomplete="off" method="post" action="<?= $PHPSELF ?>">
            <h1 class="table">FORM EDIT PEMBAYARAN BPPD KE BDRS</h1>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top">

                        <table class="form" cellspacing="1" cellpadding="0">
                            <tr>
                                <td>No. Kwitansi</td>
                                <td class="input"><?= $row[noform] ?></td>
                            </tr>

                            <tr>
                                <td>Nama BDRS</td>
                                <td class="input">
                                    <select name="nama_rs">
                                        <?php $permintaan3 = "select * from bdrs";
                                        $do3 = mysql_query($permintaan3);
                                        while ($data3 = mysql_fetch_assoc($do3)) {
                                            if ($data3[kode] == $row[rs]) $select = 'selected';
                                        ?><option value="<?= $data3[kode] ?>" <?= $select ?>><?= $data3[nama] ?></option>
                                        <? $select = "";
                                        } ?>
                                    </select>
                                </td>
                            </tr>



                            <tr>
                                <td>Jumlah Permintaan</td>
                                <td class="input"><input name="jumlah" type="text" size="4" value="<?= $row[jumlah_dist] ?>">
                                    Kantong</td>
                            </tr>
                            <tr>
                                <td>BPPD @KANTONG</td>
                                <td class="input"><input type=text name="bppd" id="bppd" value="<?= $row[biaya_satuan] ?>"></td>
                            </tr>



                        </table>
                    </td>


                    <!--<input type="submit" value="Update" name="submit" class="swn_button_blue">-->

                </tr>
            </table>
            <input type="submit" value="Update" name="submit" class="swn_button_blue">
            <input type="hidden" value="1" name="periksa">
            <input type="hidden" value="<?= $row[noform] ?>" name="noform">
            <input type="hidden" value="<?= $idminta ?>" name="idminta">
            <input type="hidden" value="<?= $row[no_rm] ?>" name="no_rm">
        </form>
<?
    }
} ?>