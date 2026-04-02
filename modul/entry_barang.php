<?php
require_once('config/db_connect.php');
session_start();
$namaudd = $_SESSION[namaudd];
$ptg = mysql_query("SELECT `kategori` FROM `hstok` ");
if (!$ptg) {
    mysql_query("ALTER TABLE `hstok` ADD `kategori` CHAR( 1 ) NOT NULL DEFAULT '0'");
}
?>

<link href="modul/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="modul/thickbox/thickbox.js"></script>
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/disable_enter.js"></script>
<script language="javascript" src="modul/thickbox/thickbox.js"></script>
<SCRIPT LANGUAGE="JavaScript" SRC="common.js"></SCRIPT>

<script language="javascript">
function selectReagen(nama_reagen, positif, negatif, greyzone) {
    $('input[@name=nama_reag]').val(nama_reagen);
    document.getElementById('nama_reagen').innerHTML = nama_reagen;
    tb_remove();
}
</script>
<?php
//include('../clogin.php');
include('../config/db_connect.php');
$namauser = $_SESSION[namauser];

// Generate Kode Barang Otomatis
$tahun = date('y');

$queryKode = mysql_query("
    SELECT MAX(Kode) AS kodeterbesar 
    FROM hstok 
    WHERE LEFT(Kode,2) = '$tahun'
");

$data = mysql_fetch_array($queryKode);
$kodeTerbesar = $data['kodeterbesar'];

if ($kodeTerbesar == "") {
    $urutan = 1;
} else {
    $urutan = (int) substr($kodeTerbesar, -4);
    $urutan++;
}

$tanggal = date('ymd');
$kodeBarang = $tanggal . sprintf("%04d", $urutan);


if (isset($_POST[submit])) {
    $kodetmp	= $_POST[Kode];
    $kodebr 	= str_replace(" ", "_", $kodetmp);

    $Kode = strtoupper($kodebr);
    $reagenujs = $_POST[reagenujs];
    $nama_reagen = explode(";", $_POST[nama_reag]);
    $nama_reagenujs = $nama_reagen[0];
    $metode = $nama_reagen[1];
    $Nama = strtoupper($_POST[Nama]);
    $status = $_POST[status];
    $min = $_POST[min];
    $satuan = strtoupper($_POST[satuan]);
    $ketsatuan = $_POST[ketsatuan];
    $merk = strtoupper($_POST[merk]);
    $hjual = $_POST[hjual];
    $hbeli = $_POST[hbeli];
    $reorder = $_POST[reorder];
    $kategori = $_POST[kategori];

    if ($Kode !== "") {
        $tambah = mysql_query("insert into hstok (Kode, reagenujs ,nama_reagen, metode, NamaBrg, StokTotal, Harga, status, satuan, min, 
					ketSatuan, snack, merk, hjual,reorder,kategori)
				                values ('$Kode','$reagenujs','$nama_reagenujs','$metode','$Nama',0,'$hbeli','$status','$satuan','$min','$ketsatuan','','$merk','$hjual','$reorder','$kategori')");

        echo "$namareagen";
        if ($tambah) {
            echo "Data Barang Telah berhasil dientry <script>parent.$.fn.colorbox.close();</script>";
            ?>
<META http-equiv="refresh" content="1; url=pmilogistik.php?module=entry_barang"><?php
        } else {
            echo "Data Barang gagal dimasukkan <script>parent.$.fn.colorbox.close();</script>";
            ?>
<META http-equiv="refresh" content="1; url=pmilogistik.php?module=master_barang"><?php
        }
    } else {
        echo "Data tidak bisa dimasukkan Kode minimal 3 karakter <script>parent.$.fn.colorbox.close();</script>";
    }

}
?>
<form name="barang" method="POST" action="<?=$PHPSELF?>">
    <h1>Entry Master Barang</h1>
    <table class="form" border="2">
        <tr>
            <td>Kode Barang</td>
            <td class="input"><input name="Kode" type="text" size="15" value="<?= $kodeBarang ?>"></td>
        </tr>

        <tr>
            <td>Reagen IMLTD?</td>
            <td class="input">
                <select name="reagenujs">
                    <option value="0">Tidak</option>
                    <option value="1">Reagen IMLTD</option>
                </select>
        <tr>
            <td>Nama Reagen</td>
            <td class="input"><input name="nama_reag" type="hidden">
                <div id="nama_reagen" style="float:left">
                </div>&nbsp;&nbsp;<a href="modul/reag.php?&width=300&height=400" class="thickbox"><img
                        src="images/button_search.png" border="0" /></a>&nbsp;&nbsp;Harus klik tanda LUV saat input
                Reagen
            </td>
        </tr>
        <tr>
            <td>Nama Barang</td>
            <td class="input"><input name="Nama" type="text" size="50"></td>
        </tr>
        <tr>
            <td>Merk</td>
            <td class="input"><input name="merk" type="text" size="15"></td>
        </tr>
        <tr>
            <td>Kategori Barang</td>
            <td class="input">
                <select name="kategori">
                    <option value="0">Barang Habis Pakai</option>
                    <option value="1">Barang Tidak Habis Pakai</option>

                </select>
        </tr>
        <tr>
            <td>Jenis Barang</td>
            <td class="input">
                <select name="status">
                    <option value="BAG" <?=$sA?>>Kantong Darah</option>
                    <option value="REAG" <?=$sB?>>Reagensia</option>
                    <option value="BHP" <?=$sC?>>Bahan Habis Pakai</option>
                    <option value="AHP" <?=$sD?>>Alat Habis Pakai</option>
                    <option value="APD" <?=$sE?>>Alat Pelindung Diri</option>
                    <option value="ATK" <?=$sF?>>Alat Tulis Kantor & Cetakan</option>
                    <option value="KEB" <?=$sG?>>Kebersihan</option>
                    <option value="SOUV" <?=$sH?>>Souvenir</option>
                    <option value="LAIN" <?=$sI?>>Lain-lain</option>
                    <option value="INV" <?=$sJ?>>Inventaris</option>
                </select>
        </tr>
        <tr>
            <td>Satuan</td>
            <td class="input"><input name="satuan" type="text" size="15" </td>
        </tr>
        <tr>
            <td>Min Stok</td>
            <td class="input"><input name="min" type="text" size="5" </td>
        </tr>
        <tr>
            <td>Re-Order</td>
            <td class="input"><input name="reorder" type="text" size="5" </td>
        </tr>
        <tr>
            <td>Isi Satuan</td>
            <td class="input"><input name="ketsatuan" type="text" size="15"></td>
        </tr>
        <tr>
            <td>Harga Beli</td>
            <td class="input"><input name="hbeli" type="text" size="19"></td>
        </tr>
        <tr>
            <td>Harga Jual</td>
            <td class="input"><input name="hjual" type="text" size="10"></td>
        </tr>

    </table>
    <input name="submit" type="submit" value="Simpan Data">
</form>