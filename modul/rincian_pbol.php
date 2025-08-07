<?php

/***********************************************
 * Author 	: suwena 
 * Modifier : irawan
 * Date 	: 19 Januari 2024
 * Fungsi	: Daftar Permintaan Dropping Darah BDRS (Online)
 * Keterangan Modul : 
 * 		MENU BARU
 * Table terkait : 
 *		- Select 	: stokkantong, dpermintaan_darah JOIN hpermintaan_darah
 *		- exec (local)  : stokkantong (UPDATE), kirim_bdrs (INSERT)
 *		- exec (online) : b_minta (UPDATE), b_mintad (UPDATE), b_stok (INSERT)
 ***********************************************/
?>
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
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />

<style>
    #serahterima {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 16px;
        border-collapse: collapse;
    }

    #serahterima td,
    #serahterima th {
        border: 1px solid #ddd;
        padding: 5px;
    }

    #serahterima tr:nth-child(even) {
        background-color: #ffe6e6;
    }

    #serahterima tr:hover {
        background-color: #ddd;
    }

    #serahterima th {
        padding-top: 3px;
        padding-bottom: 3px;
        text-align: left;
        font-weight: lighter;
        background-color: #ff9999;
        color: #000000;
    }

    #serahterima input {
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: left;
        background-color: lightyellow;
        color: #000000;
    }
</style>

<?php
require_once('clogin.php');
require_once('config/dbi_connect.php');
$namauser       = $_SESSION['namauser'];
$namalengkap    = $_SESSION['nama_lengkap'];
$tglawal        = date("Y-m-d");
$hariini        = date("Y-m-d");
$notransaksi    = "";

function formatTanggal($inputTanggal)
{
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $dateTime = new DateTime($inputTanggal);
    $split = explode('-', $inputTanggal);
    $formattedDate = $dateTime->format("d ") . $bulan[(int)$split[1]] . $dateTime->format(" Y - H:i") . " WIB";

    return $formattedDate;
}

?>

<body>
    <a name="atas" id="atas"></a>
    <div style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">RINCIAN PERMINTAAN DROPPING DARAH (BDRS) - <i>Online</i></div>
    <br>
    <?php
    if (isset($_GET['no'])) {
    ?>
        <table id="rincianPBOL" style="border-collapse: collapse; border: 2px solid #808080; box-shadow: 1px 2px 2px #000000;">
            <tr style="font-size: 12px">
                <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">No</th>
                <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">No. Transaksi</th>
                <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">Tanggal</th>
                <!-- <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">Waktu</th> -->
                <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">Asal</th>
                <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">Petugas</th>
                <th style="height: 40px; text-align: center; font-weight: bold" colspan="4">Jumlah</th>
                <!-- <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">Lihat Formulir<br>Permintaan Dropping</th> -->
                <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">Aksi</th>
            </tr>
            <tr style="font-size: 12px">
                <th style="height: 40px; text-align: center; font-weight: bold">WB</th>
                <th style="height: 40px; text-align: center; font-weight: bold">PRC</th>
                <th style="height: 40px; text-align: center; font-weight: bold">TC</th>
                <th style="height: 40px; text-align: center; font-weight: bold">FFP</th>
            </tr>
            <?php
            $no = 0;
            $qry = "SELECT d.`id_permintaan`, d.`bdrs_udd`, d.`noTrans`, d.`kodeBDRS`, d.`nosurat`, d.`ptgMinta`, DATE_FORMAT(d.`tgl`,'%d-%m-%Y') as tgl, d.`tgl_proses`, d.`status`
              FROM `dpermintaan_darah` d
              WHERE (DATE(d.`tgl`)>='$tglawal' AND DATE(d.`tgl`)<='$hariini') AND d.`noTrans` IS NOT NULL AND d.`kodeBDRS` IS NOT NULL AND d.`status` = '0'";
            //echo "$qry";
            $sql = mysqli_query($dbi, $qry);
            $no = 0;
            $jmltotal = 0;
            while ($tmp = mysqli_fetch_assoc($sql)) {
                $no++;
                $selBdrs = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama` FROM `bdrs` WHERE `kd_online` = '$tmp[kodeBDRS]'"));
                $jumWB = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COALESCE(SUM(`jumlah`), 0) AS Jumlah FROM `hpermintaan_darah` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'WB'"));
                $jumPRC = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COALESCE(SUM(`jumlah`), 0) AS Jumlah FROM `hpermintaan_darah` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'PRC'"));
                $jumTC = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COALESCE(SUM(`jumlah`), 0) AS Jumlah FROM `hpermintaan_darah` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'TC'"));
                $jumFFP = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COALESCE(SUM(`jumlah`), 0) AS Jumlah FROM `hpermintaan_darah` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'FFP'"));
            ?>
                <tr style="font-size: 12px;height: 30px;">
                    <td align="right"><?php echo $no; ?>.</td>
                    <td style="text-align: center"><?php echo $tmp['noTrans']; ?></td>
                    <td style="text-align: center"><?php echo $tmp['tgl']; ?></td>
                    <td style="text-align: center"><?php echo $selBdrs['nama']; ?></td>
                    <td style="text-align: center"><?php echo $tmp['ptgMinta']; ?></td>
                    <td style="text-align: center"><?php echo $jumWB['Jumlah']; ?></td>
                    <td style="text-align: center"><?php echo $jumPRC['Jumlah']; ?></td>
                    <td style="text-align: center"><?php echo $jumTC['Jumlah']; ?></td>
                    <td style="text-align: center"><?php echo $jumFFP['Jumlah']; ?></td>
                    <!--
                <td style="text-align: center">
			<a href="#">Lihat</a>
		</td>
		-->
                    <td>
                        <a href="pmiqa.php?module=detail_bdrs_ol&no=<?= $tmp['noTrans'] ?>">Rincian</a> |
                        <a href="pmiqa.php?module=proses_bdrs_ol&no=<?= $tmp['noTrans'] ?>">Proses</a> |
                        <a href="pmiqa.php?module=reject_bdrs_ol&no=<?= $tmp['noTrans'] ?>">Reject</a>
                    </td>
                </tr>
            <?php
                $jmltotal = $jmltotal + $jumWB['Jumlah'] + $jumPRC['Jumlah'] + $jumTC['Jumlah'] + $jumFFP['Jumlah'];
            }
        } else {
            if ($no == "0") {
            ?><tr style="font-size: 16px;height: 40px; text-align: center;">
                    <td colspan="10">Tidak ada data</td>
                </tr>
            <?

            } else { ?>
                <tr style="font-size: 12px;height: 40px; text-align: center;">
                    <td style="text-align: right" colspan="5">JUMLAH PERMINTAAN</td>
                    <td style="text-align: center" colspan="4"><?= $jmltotal ?></td>
                    <td style="text-align: center"></td>
                </tr>

        <? }
        } ?>
        </table>
        <br>
        <div style="font-size:10px; color:#000000; font-family: " Helvetica Neue", Helvetica, Arial, sans-serif;">Build : 19-01-2024</div>