<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/disable_enter.js"></script>

<?php
include('clogin.php');
include('config/db_connect.php');

/* =========================
   PROSES UPDATE
========================= */
if (isset($_POST['submit'])) {

    $KodeBaru = strtoupper($_POST['Kode']);
    $KodeLama = strtoupper($_POST['KodeLama']);

    $Jenis      = $_POST['jenis'];
    $Nama       = strtoupper($_POST['Nama']);
    $Alamat     = strtoupper($_POST['Alamat']);
    $Telp1      = $_POST['Telp1'];
    $namaCp     = strtoupper($_POST['namaCp']);
    $alamatcp   = strtoupper($_POST['alamatcp']);
    $telpcp1    = $_POST['telpcp1'];
    $telpcp2    = $_POST['telpcp2'];
    $Keterangan = strtoupper($_POST['Keterangan']);

    $sqlupdate = mysql_query("UPDATE supplier SET
                Kode='$KodeBaru',
                jenis='$Jenis',
                Nama='$Nama',
                Alamat='$Alamat',
                Telp1='$Telp1',
                namaCp='$namaCp',
                alamatcp='$alamatcp',
                telpcp1='$telpcp1',
                telpcp2='$telpcp2',
                Keterangan='$Keterangan'
                WHERE Kode='$KodeLama'");

    if ($sqlupdate) {
        echo "Data '$Nama' telah berhasil di-Update <br>";
        ?>
<meta http-equiv="refresh" content="1; url=pmilogistik.php?module=kontak">
<?php
    } else {
        echo "Ada kesalahan, data tidak bisa diupdate";
    }
}

/* =========================
   LOAD DATA EDIT
========================= */
if (isset($_GET['Kode'])) {

    $ssql = mysql_query("SELECT * FROM supplier WHERE Kode='$_GET[Kode]'");
    $row  = mysql_fetch_assoc($ssql);

    if (!$row) {
        // echo "Error loading data";
        ?>
<meta http-equiv="refresh" content="1; url=pmilogistik.php?module=kontak">
<?php
    } else {

        $jenis = $row['jenis'];
        $selected[$jenis] = "selected";
        ?>

<h1 class="table">EDIT DATA KONTAK</h1>

<form name="editdata" autocomplete="off" method="post">

    <table class="form" border="1" cellspacing="2" cellpadding="3">

        <tr>
            <td>Kode</td>
            <td class="input">
                <input type="text" name="Kode" value="<?= $row['Kode'] ?>" readonly>
                <input type="hidden" name="KodeLama" value="<?= $row['Kode'] ?>">
                <input type="hidden" id="KodeAwal" value="<?= $row['Kode'] ?>">
                <input type="hidden" id="JenisAwal" value="<?= $row['jenis'] ?>">
            </td>
        </tr>

        <tr>
            <td>Jenis</td>
            <td class="input">
                <select name="jenis" onchange="generateKode(this.value)">
                    <option value="0" <?= $selected["0"] ?>>Supplier</option>
                    <option value="1" <?= $selected["1"] ?>>Customer</option>
                    <option value="2" <?= $selected["2"] ?>>Bagian di UDD</option>
                    <option value="3" <?= $selected["3"] ?>>Lain-lain</option>
                    <option value="4" <?= $selected["4"] ?>>Pengelola Limbah</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>Nama Kontak</td>
            <td class="input">
                <input name="Nama" type="text" size="50" value="<?= $row['Nama'] ?>">
            </td>
        </tr>

        <tr>
            <td>Alamat</td>
            <td class="input">
                <input name="Alamat" type="text" size="50" value="<?= $row['Alamat'] ?>">
            </td>
        </tr>

        <tr>
            <td>Telp</td>
            <td class="input">
                <input name="Telp1" type="text" size="15" value="<?= $row['Telp1'] ?>">
            </td>
        </tr>

        <tr>
            <td>Kontak Person</td>
            <td class="input">
                <input name="namaCp" type="text" size="50" value="<?= $row['namaCp'] ?>">
            </td>
        </tr>

        <tr>
            <td>Alamat Kontak Person</td>
            <td class="input">
                <input name="alamatcp" type="text" size="50" value="<?= $row['alamatcp'] ?>">
            </td>
        </tr>

        <tr>
            <td>Telp Kontak Person</td>
            <td class="input">
                <input name="telpcp1" type="text" size="15" value="<?= $row['telpcp1'] ?>">
            </td>
        </tr>

        <tr>
            <td>HP Kontak Person</td>
            <td class="input">
                <input name="telpcp2" type="text" size="15" value="<?= $row['telpcp2'] ?>">
            </td>
        </tr>

        <tr>
            <td>Keterangan</td>
            <td class="input">
                <input name="Keterangan" type="text" size="50" value="<?= $row['Keterangan'] ?>">
            </td>
        </tr>

    </table>

    <br>
    <input type="submit" value="Simpan perubahan" name="submit">
</form>

<?php
    }
}
?>

<script>
function generateKode(jenisBaru) {

    var jenisAwal = document.getElementById("JenisAwal").value;
    var kodeAwal = document.getElementById("KodeAwal").value;
    var inputKode = document.querySelector('input[name="Kode"]');

    // Jika kembali ke jenis awal → kembalikan kode awal
    if (jenisBaru == jenisAwal) {
        inputKode.value = kodeAwal;
        return;
    }

    // Jika jenis berubah → generate kode baru
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "modul/ajax_kode_supplier.php?jenis=" + jenisBaru, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            inputKode.value = xhr.responseText;
        }
    };
    xhr.send();
}
</script>