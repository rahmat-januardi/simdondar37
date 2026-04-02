<?php
include('clogin.php');
include('config/db_connect.php');
$namauser = $_SESSION[namauser];

if (isset($_POST[submit])) {
    $Kode		= strtoupper($_POST[Kode]);
    $Jenis		= $_POST[Jenis];
    $Nama		= strtoupper($_POST[Nama]);
    $Alamat		= strtoupper($_POST[Alamat]);
    $Telp1		= $_POST[Telp1];
    $namaCp		= strtoupper($_POST[namaCp]);
    $alamatcp	= strtoupper($_POST[alamatcp]);
    $telpcp1	= $_POST[telpcp1];
    $telpcp2	= $_POST[telpcp2];
    $Keterangan	= strtoupper($_POST[Keterangan]);


    $tambah = mysql_query("insert into supplier (Kode,Jenis, Nama,Alamat,Telp1,namaCp,alamatcp,telpcp1,telpcp2,Keterangan) 
		values ('$Kode','$Jenis', '$Nama','$Alamat','$Telp1','$namaCp','$alamatcp','$telpcp1','$telpcp2','$Keterangan')");
    if ($tambah) {
        echo "Data telah berhasil dientry <script>parent.$.fn.colorbox.close();</script>";
        ?>
<META http-equiv="refresh" content="1; url=pmilogistik.php?module=kontak"><?php
    } else {
        echo "Data gagal dimasukkan <script>parent.$.fn.colorbox.close();</script>";
        ?>
<META http-equiv="refresh" content="1; url=pmilogistik.php?module=kontak"><?php
    }
}
?>
<form name="supplier" method="POST" action="<?=$PHPSELF?>">
    <h1>PENAMBAHAN KONTAK BARU</h1>
    <table class="form" border="0" cellspacing="2" cellpadding="2">
        <tr>
            <td> Kode</td>
            <td class="input"><input name="Kode" type="text" size="10" placeholder="Kode unik" readonly>
            </td>
        </tr>
        <tr>
            <td>Jenis Kontak</td>
            <td class="input">
                <select name="Jenis" onchange="generateKode(this.value)">
                    <option>Pilih Jenis Supplier</option>
                    <option value="0">Supplier</option>
                    <option value="1">Customer</option>
                    <option value="2">Bagian di UDD</option>
                    <option value="3">Lain-lain</option>
                    <option value="4">Pengelola Limbah</option>
                </select>
        </tr>
        <tr>
            <td>Nama Suplier</td>
            <td class="input"><input name="Nama" type="text" size="30" placeholder="Nama Suplier"
                    value="<?=$_POST[Nama]?>"></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td class="input"><input name="Alamat" type="text" size="30" placeholder="Alamat suplier"
                    value="<?=$_POST[Alamat]?>"></td>
        </tr>
        <tr>
            <td>Telp. Perusahaan</td>
            <td class="input"><input name="Telp1" type="text" size="15" placeholder="No. Telp"
                    value="<?=$_POST[Telp1]?>"></td>
        </tr>
        <tr>
            <td>Kontak Person</td>
            <td class="input"><input name="namaCp" type="text" size="30" placeholder="Kontak person suplier"
                    value="<?=$_POST[namaCp]?>"></td>
        </tr>
        <tr>
            <td>Alamat Kontak Person</td>
            <td class="input"><input name="alamatcp" type="text" size="30" value="<?=$_POST[alamatcp]?>"></td>
        </tr>
        <tr>
            <td>Telp Kontak Person</td>
            <td class="input"><input name="telpcp1" type="text" size="15" value="<?=$_POST[telpcp1]?>"></td>
        </tr>
        <tr>
            <td>HP Kontak Person</td>
            <td class="input"><input name="telpcp2" type="text" size="15" value="<?=$_POST[telpcp2]?>"></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td class="input"><input name="Keterangan" type="text" size="30" value="<?=$_POST[Keterangan]?>"></td>
        </tr>
    </table>
    <input name="submit" type="submit" value="Simpan">
    <DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;">
    </DIV>
</form>

<script>
function generateKode(jenis) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "modul/ajax_kode_supplier.php?jenis=" + jenis, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementsByName("Kode")[0].value = xhr.responseText;
        }
    };
    xhr.send();
}
</script>