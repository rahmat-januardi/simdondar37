<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<?
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser = $_SESSION[namauser];
$namalengkap = $_SESSION[nama_lengkap];
$today = date('Y-m-d');
$today1 = $today;
$srcnama = "";
$srcrm = "";
$srcform = "";
$src_abo = "";
$src_rh = "";
$src_rs = "";
$src_lyn = "";
$src_shift = "";

if (isset($_POST[minta1])) {
    $today = $_POST[minta1];
    $today1 = $today;
}
if ($_POST[minta2] != '') $today1 = $_POST[minta2];
if ($_POST[nama] != '') $srcnama = $_POST[nama];
if ($_POST[rm] != '') $srcrm = $_POST[rm];
if ($_POST[nomorf] != '') $srcform = $_POST[nomorf];
if ($_POST[gol_abo] != '') $src_abo = $_POST[gol_abo];
if ($_POST[gol_rh] != '') $src_rh = $_POST[gol_rh];
if ($_POST[gol_rs] != '') $src_rs = $_POST[gol_rs];
if ($_POST[gol_lyn] != '') $src_lyn = $_POST[gol_lyn];
if ($_POST[gol_shift] != '') $src_shift = $_POST[gol_shift];
if ($_POST[gol_rs] != '') $src_rs = $_POST[gol_rs];
?>
<style>
    tr {
        background-color: #FDF5E6
    }

    .initial {
        background-color: #FDF5E6;
        color: #000000
    }

    .normal {
        background-color: #FDF5E6
    }

    .highlight {
        background-color: #8888FF
    }
</style>
<font size="5" color=red>CETAK ULANG KWITANSI</font><br>
<form method=post>
    <font size="2" color=black>
        TANGGAL : <input type=text name=minta1 id=datepicker size=10 value=<?= $today ?>>
        S/D <input type=text name=minta2 id=datepicker1 size=10 value=<?= $today1 ?>><br>
        NO.FORM <input type=text name=nomorf id=nomorf size=10 value=<?= $srcform ?>>
        NO.CM <input type=text name=rm id=rm size=10 value=<?= $srcrm ?>>
        NAMA PASIEN<input type=text name=nama id=nama size=8 value=<?= $srcnama ?>>
        Gol Darah <select name="gol_abo">
            <option value="">PILIIH</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="AB">AB</option>
            <option value="O">O</option>
        </select>

        RH<select name="gol_rh">
            <option value="">PILIIH</option>
            <option value="+">+</option>
            <option value="-">-</option>
        </select>
        LAYANAN
        <select name="gol_lyn">
            <option value="" selected>- SEMUA -</option>
            <?php
            $ql = mysql_query("select * from jenis_layanan ");

            while ($rowl1 = mysql_fetch_array($ql)) {
                echo "<option value=$rowl1[nama]>$rowl1[nama]</option>";
            }
            ?>
        </select>

        <!--JENIS LAYANAN<input type=text name=gol_lyn id=gol_lyn size=10 value=<?= $src_lyn ?>>
	</font-->
        <br>
        RS<select name="gol_rs">
            <option value=0 selected>- PILIH -</option>
            <?php
            $q = mysql_query("select * from rmhsakit ");

            while ($row1 = mysql_fetch_array($q)) {
                echo "<option value=$row1[Kode]>$row1[NamaRs]</option>";
            }
            ?>
        </select>
        <td class="styled-select" bgcolor="#ffa688">

            SHIFT<select name="gol_shift">
                <option value="">PILIIH</option>
                <option value="1">SHIFT I</option>
                <option value="2">SHIFT II</option>
                <option value="3">SHIFT III</option>
                <option value="4">SHIFT IV</option>
            </select>

            <input type="submit" name="submit" value="Lihat" class="swn_button_blue">
</form>
<?


$allcount = mysql_query("select * from htranspermintaan where CAST(htranspermintaan.tgl_register as date) >='$today' and CAST(htranspermintaan.tgl_register as date)<='$today1' ");
$trans0 = mysql_query("select htranspermintaan.*, pasien.nama, pasien.alamat, pasien.gol_darah, pasien.rhesus, pasien.kelamin from htranspermintaan inner join pasien on pasien.no_rm=htranspermintaan.no_rm
						 where CAST(htranspermintaan.tgl_register as date) >='$today' and CAST(htranspermintaan.tgl_register as date)<='$today1'
						 and pasien.nama like '%$srcnama%'
						 and pasien.no_rm like '%$srcrm%'
						 and htranspermintaan.noform like '%$srcform%'
						 and pasien.gol_darah like '%$src_abo%'
						 and pasien.rhesus like '%$src_rh%'
						 and htranspermintaan.jenis like '%$src_lyn%'
						 and htranspermintaan.shift like '%$src_shift%'
						 and htranspermintaan.rs like '%$src_rs%'
						 order by noform");
$rows = mysql_num_rows($trans0);
$rows2 = mysql_num_rows($allcount);

echo '<b>';
echo $rows;
echo ' data dari total ';
echo $rows2;
echo ' data permintaan ';
echo '<b>';
?>
<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="110%">
    <tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;"
        onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
        <td rowspan=2 align="center">NO</td>
        <td rowspan=2 align="center">NO FORM</td>
        <td rowspan=2 align="center">TGL MINTA</td>
        <td rowspan=2 align="center">NO. RM</td>
        <td rowspan=2 align="center">NAMA PASIEN</td>
        <td rowspan=2 align="center">KEL</td>
        <td rowspan=2 align="center">ALAMAT</td>
        <td rowspan=2 align="center">RUMAH SAKIT</td>
        <td rowspan=2 align="center">BAGIAN</td>
        <td rowspan=2 align="center">KLAS</td>
        <td rowspan=2 align="center">JENIS<br>LAYANAN</td>
        <td rowspan=2 align="center">GOL</td>
        <td rowspan=2 align="center">TGL DIPERLUKAN</td>
        <td rowspan=2 align="center">JENIS DARAH/JML</td>
        <td colspan=2 align="center">STATUS</td>
        <td rowspan=2 align="center">JENIS<br>PERMINTAAN</td>
        <td rowspan=2 align="center">SHIFT</td>
        <td rowspan=2 align="center">TEMPAT</td>
        <td rowspan=2 align="center">PETUGAS INPUT</td>
    </tr>
    <tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;"
        onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
        <td>BAWA</td>
        <td>TITIP</td>
    </tr>
    <?
    $no = 1;
    while ($trans = mysql_fetch_assoc($trans0)) {
        $jenisminta = mysql_fetch_assoc(mysql_query("select group_concat(' ',`JenisDarah`,'(',jumlah,')') as jenis from dtranspermintaan where `NoForm`='$trans[noform]'"));
        $dtrans = mysql_fetch_assoc(mysql_query("select sum(Jumlah) as Jumlah,GolDarah,JenisDarah from dtranspermintaan where NoForm='$trans[noform]'"));
        $norm = $trans[no_rm]; ?>
        <tr style="font-size:11px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
            onMouseOut="this.className='normal'">
            <td align="right"><?= $no++ ?>.</td>
            <?
            $bawa = mysql_fetch_assoc(mysql_query("select count(dt.NoKantong) as noform from dtransaksipermintaan as dt where dt.NoForm='$trans[noform]' and (dt.Status='0' or dt.Status='L')"));
            $titip = mysql_fetch_assoc(mysql_query("select count(dt.NoKantong) as noform from dtransaksipermintaan as dt where dt.NoForm='$trans[noform]' and dt.Status='1'"));
            $total = $bawa[noform] + $titip[noform];
            if ($_SESSION[leveluser] == 'laboratorium' or $_SESSION[leveluser] == 'bdrs') {
                if ($dtrans[Jumlah] > $total) {
                    if ($_SESSION[leveluser] == 'laboratorium') echo "<td class=input><a href=pmilaboratorium.php?module=crossmatch&noform=$trans[noform]>$trans[noform]</a></td>";
                    if ($_SESSION[leveluser] == 'bdrs') echo "<td class=input><a href=pmibdrs.php?module=crossmatch&noform=$trans[noform]>$trans[noform]</a></td>";
                } else { ?>
                    <td class=input><?= $trans[noform] ?></td>
                    <?
                }
            } else {
                if ($_SESSION[leveluser] == 'kasir2' or $_SESSION[leveluser] == 'bdrs') {
                    $bayar = mysql_query("select * from dtransaksipermintaan where noForm='$trans[noform]' and (status='0' or status='1')");
                    $nbayar = mysql_num_rows($bayar);
                    if ($nbayar > 0) {
                        echo "<td class=input><a href=pmikasir2.php?module=pembayaran_ulang&noform=$trans[noform]>$trans[noform]</a></td>";
                    } else { ?>
                        <td class=input><?= $trans[noform] ?></td>
                    <?
                    }
                } else { ?>
                    <td class=input><?= $trans[noform] ?></td>
            <?
                }
            } ?>
            <td class=input nowrap><?= $trans[tgl_register] ?></td>
            <td class=input><?= $trans[no_rm] ?></td>
            <td class=input><?= $trans[nama] ?></td>
            <td class=input><?= $trans[kelamin] ?></td>
            <td class=input><?= $trans[alamat] ?></td>
            <? $rmhskt = mysql_fetch_assoc(mysql_query("select NamaRs from rmhsakit where Kode='$trans[rs]'")); ?>
            <td class=input><?= $rmhskt[NamaRs] ?></td>
            <td class=input><?= $trans[bagian] ?></td>
            <td class=input nowrap><?= $trans[kelas] ?></td>
            <?
            $jenis = mysql_fetch_assoc(mysql_query("select nama from jenis_layanan where kode='$trans[jenis]'"));
            ?>
            <td class=input><?= $jenis[nama] ?></td>
            <td class=input><?= $dtrans[GolDarah] . '(' . $trans[rhesus] . ')' ?></td>
            <td class=input><?= $trans[tglminta] ?></td>
            <td class=input><?= $jenisminta[jenis] ?></td>
            <td class=input><?= $bawa[noform] ?></td>
            <td class=input><?= $titip[noform] ?></td>
            <? $shif3 = '';
            if ($trans[shift] == '1') $shif3 = 'I';
            if ($trans[shift] == '2') $shif3 = 'II';
            if ($trans[shift] == '3') $shif3 = 'III';
            if ($trans[shift] == '4') $shif3 = 'IV';
            ?>
            <td class=input align=center><?= $trans[jenis_permintaan] ?></td>
            <td class=input>
                <? echo $shif3 ?>
            </td>
            <td class=input><?= $trans[tempat] ?></td>
            <td class=input><?= $trans[petugas] ?></td>
        </tr>
    <?
    }
    ?>
</table>
<br>
<form name=xls method=post action=modul/rekap_permintaan_harian_xls.php>
    <input type=hidden name=today value='<?= $today ?>'>
    <input type=hidden name=today1 value='<?= $today1 ?>'>
    <input type=hidden name=srcnama value='<?= $srcnama ?>'>
    <input type=hidden name=srcrm value='<?= $srcrm ?>'>
    <input type=hidden name=srcform value='<?= $srcform ?>'>
    <input type=hidden name=src_abo value='<?= $src_abo ?>'>
    <input type=hidden name=src_rh value='<?= $src_rh ?>'>
    <input type=hidden name=src_rs value='<?= $src_rs ?>'>
    <input type=hidden name=src_lyn value='<?= $src_lyn ?>'>
    <input type=submit name=submit2 value='Print Rekap permintaan harian (.XLS)'>
</form>