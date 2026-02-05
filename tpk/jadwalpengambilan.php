<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<style>
    .awesomeText {
        color: #000;
        font-size: 75%;
    }

    tr {
        background-color: #ffffe6
    }

    .initial {
        background-color: #ffffe6;
        color: #000000
    }

    .normal {
        background-color: #ffffe6
    }

    .highlight {
        background-color: #7CFC00
    }
</style>

<?php
include('config/dbi_connect.php');
require_once('clogin.php');
$tgl1 = date('Y-m-d');
$tgl2 = $tgl1;
if (isset($_POST['tgl1'])) {
    $tgl1 = $_POST['tgl1'];
    $$tgl2 = $tgl1;
}
if ($_POST['tgl2'] != '') $tgl2 = $_POST['tgl2'];

$file = basename($_SERVER['PHP_SELF']);
?>
<div style="font-size:18px;color:#00008B;">
    <center>
        <H2>ANTRIAN PENGAMBILAN DARAH APHERESIS & TPK</b></H2>
    </center>
</div>
<div class="awesomeText">
    <form name=mintadarah1 method=post> Mulai:
        <input type=text name="tgl1" id=datepicker size=10 value="<?php echo $tgl1; ?>"> Sampai :
        <input type=text name="tgl2" id=datepicker1 size=10 value="<?php echo $tgl2; ?>">
        <input type=submit name=submit value="Tampikan data" class="swn_button_blue">
        <a href="<?php echo $file; ?>?module=jadwalambil" class="swn_button_blue">Edit Jadwal</a>
    </form>
</div>
<br>
<table border=1 cellpadding=5 cellspacing=5 style="border-collapse:collapse">
    <tr style="background-color:red; font-size:14px; color:#FFFFFF; font-family:Verdana;" align='center'>
        <td rowspan="2">No</td>
        <td colspan="2">Tanggal <br>Pengambilan</td>
        <td rowspan="2">ID Sampel</td>
        <td rowspan="2">Pendonor</td>
        <td rowspan="2">Gol.<br>Darah</td>
        <td rowspan="2">No. Telp</td>
        <td rowspan="2">Jenis<br>Donor</td>
        <td colspan="2">Permintaan Darah</td>
        <td rowspan="2">Keterangan</td>

    </tr>
    <tr style="background-color:red; font-size:14px; color:#FFFFFF; font-family:Verdana;" align='center'>
        <td>Tanggal</td>
        <td>Jam</td>

        <td>Nama Pasien</td>
        <td>Rumah Sakit</td>

    </tr>
    <?php
    //pagination


    $sql = "SELECT * from v_jadwal_apheresis WHERE tgl between '$tgl1' AND '$tgl2' order by jam ASC";
    $sq = mysqli_query($dbi, $sql);
    $no = 0;
    $jum = mysqli_num_rows($sq);
    if ($jum < 1) { ?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td align="center" colspan="22">Tidak Ada Data Pemeriksaan</td>
        </tr>
        <?php
    } else {
        while ($dt = mysqli_fetch_assoc($sq)) {
            $no++;
        ?>
            <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
                <td align="right"><?php echo $no; ?></td>
                <td align="left"><?php echo $dt['tgl']; ?></td>
                <td align="left"><?php echo $dt['jam']; ?></td>
                <td align="left"><?php echo $dt['kd_sample']; ?></td>
                <td align="left"><?php echo $dt['title']; ?></td>
                <td align="left"><?php echo $dt['gol_drh']; ?></td>

                <td align="left"><?php echo $dt['telp2']; ?></td>
                <td align="left"><?php echo $dt['JenisDonor'] . ' (' . $dt['pk'] . ')'; ?></td>

                <td align="left"><?php echo $dt['nama']; ?></td>
                <td align="left"><?php echo $dt['NamaRs']; ?></td>


                <?php
                if ($dt['stat'] == 0) { ?>
                    <td><a href="<?php echo $file; ?>?module=checkup_aph&kode=<?php echo $dt['Kode']; ?>&tpk=1&trx=<?php echo $dt['NoTrans']; ?>&sampel=<?php echo $dt['kd_sample']; ?>"><input type="button" class="swn_button_green" onclick="return confirm('Proses Data Donor')" value="PROSES"></a> <a href="<?php echo $file; ?>?module=bataljadwal&nama=<?php echo $dt['title']; ?>&nohp=<?php echo $donor['telp2']; ?>&trx=<?php echo $dt['NoTrans']; ?>"><input type="button" class="swn_button_red" onclick="return confirm('Batalkan Proses Donor ?, Anda tidak dapat mengubah atau menjadwalkan kembali setelahnya')" value="BATAL"></a></td>
                <? } else if ($dt['stat'] == 1) { ?>
                    <td>Proses Selesai</td>
                <? } else if ($dt['stat'] == 2) { ?>
                    <td>Proses Batal</td>
                <? } ?>
            </tr>
    <?php
        }
    } ?>
</table>