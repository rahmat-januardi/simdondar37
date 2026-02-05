<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<?php
include('clogin.php');
include('config/db_connect.php');
//print_r($_GET);
//print_r($_POST);

if ($_GET[aksi] == 'hapus') {
    $hapus = mysql_query("delete from shift where nama='$_GET[nama]'");
    if ($hapus) {
        echo ("Data shift telah dihapus !!
        <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=pmiadmin.php?module=tambah_shift\">");
    }
}

if (isset($_POST[submit1])) {
    $_POST[submit1] = "";
    $tambah = mysql_query("INSERT INTO `shift` (`nama`,`jam`, `sampai_jam`) values ('$_POST[nama]','$_POST[jam]','$_POST[sampai_jam]')");
}
if ($tambah)
    echo ("Jam shift UDD telah ditambah !!
    <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=$PHP_SELF\">");

if ($_GET[aksi] == 'edit') {
    $q_edit = mysql_query("select * from shift where nama='$_GET[nama]'");
    $a_edit = mysql_fetch_assoc($q_edit);
?>
    <h1 class="table">FORM JAM SHIFT UDD`</h1>
    <form method="post" action="pmiadmin.php?module=tambah_shift">
        <table class="form" cellspacing="1" cellpadding="2">
            <tr>
                <td>nama shift</td>
                <td class="input">
                    <input name="nama" type="text" size="3" value="<?= $a_edit[nama] ?>">
                </td>
                <td>Jam shift</td>
                <td class="input">
                    <input name="jam" type="time" size="10" value="<?= $a_edit[jam] ?>">
                </td>
                <td>Sampai Jam</td>
                <td class="input">
                    <input name="sampai_jam" type="time" size="10" value="<?= $a_edit[sampai_jam] ?>">
                </td>
            </tr>
        </table>
        <input border=0 type="submit" name="submit1" value="Simpan">
    </form>

<?php
    $del = mysql_query("delete from shift where `nama`='$_GET[nama]'");
} else {
?>
    <h1 class="table">FORM JAM SHIFT UDD</h1>
    <h2 class="table">Penulisan Nama shift harus pakai huruf romawi I=Pagi, II=Siang, III=Malam</h2>
    <h2 class="table">Penulisan Jam shift harus pakai format 00:00:00</h2>
    <form method="post" action="pmiadmin.php?module=tambah_shift">
        <table class="form" cellspacing="1" cellpadding="2">
            <tr>
                <td>Nama Shift</td>
                <td class="input">
                    <input name="nama" type="text" size="3">
                </td>
                <td>Jam Shift</td>
                <td class="input">
                    <? $time = date('H:i:s') ?>
                    <input name="jam" type="Time" size="10">
                </td>
                <td>Sampai Jam</td>
                <td class="input">
                    <? $time = date('H:i:s') ?>
                    <input name="sampai_jam" type="Time" size="10">
                </td>
            </tr>
        </table>
        <input border=0 type="submit" name="submit1" value="Simpan">
    </form>
<?php
}
?>
<br>
<br>
<table class="ui-widget ui-widget-content">
    <tr class="ui-widget-header">
        <th>No.</th>
        <th>Shift</th>
        <th>Jam</th>
        <th>Sampai Jam</th>
        <th>Aksi</th>
    </tr>
    <?php
    $q_dr = mysql_query("select * from shift");
    $jum_shift = mysql_numrows($q_dr);
    while ($a_dr = mysql_fetch_assoc($q_dr)) {
        $no++;
        echo "<tr>";
        echo "<td>" . $no . "</td>" .
            "<td>" . $a_dr[nama] . "</td>" .
            "<td>" . $a_dr[jam] . "</td>" .
            "<td>" . $a_dr[sampai_jam] . "</td>";
        echo "<td>
				<ul id=\"icons\" class=\"ui-widget ui-helper-clearfix\">
                    <a href=\"" . $PHP_SELF . "?module=tambah_shift&aksi=hapus&nama=" . $a_dr[nama] . "\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Hapus\">
                        <span class=\"ui-icon ui-icon-closethick\"></span></li>
                    </a>
                    <a href=\"" . $PHP_SELF . "?module=tambah_shift&aksi=edit&nama=" . $a_dr[nama] . "\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Ubah\">
                        <span class=\"ui-icon ui-icon-pencil\"></span></li>
                    </a>
            </ul></td>";
        echo "</tr>";
    }
    ?>
</table>