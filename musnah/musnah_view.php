<?

$nodokumen = " ";
include('config/dbi_connect.php');
// date_default_timezone_set("Asia/Jakarta");
$now            = date("dmyHi");

$today            = date("Y-m-d H:i:s");
$namauser        = $_SESSION['namauser'];
$namauserlkp    = $_SESSION['nama_lengkap'];
$level            = $_SESSION['leveluser'];
$notransaksi    = $_GET['notrans'];

//echo "$notransaksi";

function getAlasanList($dbi)
{
    $list = array();
    $sql = mysqli_query($dbi, "SELECT nomor, alasan FROM alasan_musnah ORDER BY nomor ASC");
    if ($sql) {
        while ($row = mysqli_fetch_assoc($sql)) {
            $list[$row['nomor']] = $row['alasan'];
        }
    }
    return $list;
}

function getAlasanLabel($kode, $dbi)
{
    static $list = null;

    if ($list === null) {
        $list = getAlasanList($dbi);
    }

    if (isset($list[$kode])) {
        return $list[$kode];
    }

    return 'Tidak ada';
}

$alasanList = getAlasanList($dbi);
?>

<title>SIMDONDAR</title>

<head>
    <style type="text/css" media="print">
    @page {
        size: F4;
        margin-bottom: 20mm;
        margin-left: 0mm;
        margin-right: 0mm;
        margin-top: 15mm;

        header : {
            display: none !important;
        }
    }

    html {
        background-color: #ffffff;
        margin: 3px;
        /* this affects the margin on the html before sending to printer */
    }

    body {
        border: solid 0px #ffffff;
        margin: 0mm 15mm 10mm 10mm;
        /* margin you want for the content */
    }

    table th td {
        text-align: left;
    }
    </style>
</head>

<body>

    <!--img src="musnah/kop_pemusnahan.jpg" width="100%"-->

    <?php
    $sql_h  = "SELECT *,  DATE_FORMAT(`tgl`, '%d %M %Y %H:%i') as `tglmusnah` from ar_stokkantong_trans  WHERE `notrans`='$notransaksi'";
    $sql_h1 = mysqli_fetch_assoc(mysqli_query($dbi, $sql_h));

    if ($sql_h1['stat'] == "0") {
        $statuslimbah = 'Belum Diambil';
    } else {
        $statuslimbah = 'Sudah Diambil';
    }

    $usr    = mysqli_fetch_assoc(mysqli_query($dbi, "select Nama from supplier where jenis='4' AND Kode='$sql_h1[pengelola]' limit 1"));
    ?>

    <table class="list" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
        <tr>
            <td style="height: 80px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;"
                colspan="2"><b>BERITA ACARA<br>TRANSAKSI PEMUSNAHAN DARAH</b></td>
            <p>
        </tr>

        <tr style="font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td style="vertical-align: top; width=50%;">
                <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%"
                    style="border-collapse:collapse">
                    <tr>
                        <td style="text-align: left">No. transaksi</td>
                        <td><?php echo $sql_h1['notrans']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Tanggal pemusnahan</td>
                        <td><?php echo $sql_h1['tglmusnah']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Bagian yang memusnahkan</td>
                        <td><?php echo strtoupper($sql_h1['bagian']); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Shift</td>
                        <td><?php echo $sql_h1['shift']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Petugas Pengiriman</td>
                        <td><?php echo $sql_h1['ptgs_kirim']; ?></td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;width=50%">
                <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%"
                    style="border-collapse:collapse">
                    <!-- <tr><td style="text-align: left">Kode Pengelola</td>              <td><?php echo $sql_h1['pengelola']; ?></td></tr> -->
                    <tr>
                        <td style="text-align: left">Instansi Pengelola</td>
                        <td><?php echo $usr['Nama']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Status Limbah</td>
                        <td><?php echo $statuslimbah; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Tanggal Pengambilan</td>
                        <td><?php echo $sql_h1['diambil']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Berat (Kg)</td>
                        <td><?php echo $sql_h1['berat']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?
    $sql_d = "SELECT ar_stokkantong.*,
        COALESCE(alasan_musnah.alasan, 'Tidak ada') AS alasan_buang
        FROM ar_stokkantong
        LEFT JOIN alasan_musnah ON alasan_musnah.nomor = ar_stokkantong.alasan_buang
        WHERE ar_stokkantong.notrans = '$notransaksi'";
    //echo "$sql_d<br>";
    $sql_d1 = mysqli_query($dbi, $sql_d);
    ?>
    <p>
    <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
        <thead
            style="background-color:#DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <tr
                style="background-color: #DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                <th style="height: 40px;text-align: center;font-weight: bold">No</th>
                <th style="height: 40px;text-align: center;font-weight: bold">No Kantong</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Merk</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Produk</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Gol<br>Drh</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Tgl<br>Aftap</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Tgl<br>Kadaluwarsa</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Alasan Pemusnahan</th>
            </tr>
        </thead>
        <tbody>
            <?
            $no = 0;
            while ($sgd = mysqli_fetch_assoc($sql_d1)) {
                $no++;
            ?>
            <tr
                style="font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                <td style="text-align: right;"> <?php echo $no . '.'; ?> </td>
                <td style="text-align: center;"> <?php echo $sgd['noKantong']; ?> </td>
                <td style="text-align: center;"> <?php echo $sgd['merk']; ?> </td>
                <td style="text-align: center;"> <?php echo $sgd['produk']; ?> </td>
                <td style="text-align: center;"> <?php echo $sgd['gol_darah'] . '(' . $sgd['RhesusDrh'] . ')'; ?> </td>
                <td style="text-align: center;"> <?php echo $sgd['tgl_Aftap']; ?> </td>
                <td style="text-align: center;"> <?php echo $sgd['kadaluwarsa']; ?> </td>
                <td style="text-align: center;"> <?php echo $sgd['alasan_buang']; ?> </td>
            </tr>
            <?
            }
            ?>
        </tbody>
    </table>
    <br>

    <!--Detail Pemusnahan-->

    <hr style="width: 100%;text-align:left;margin-left:0; line-height: 1px">
    <?php
    $sum = "SELECT COALESCE(alasan_musnah.alasan, 'Tidak ada') AS alasan, COUNT(*) AS jml
            FROM ar_stokkantong
            LEFT JOIN alasan_musnah ON alasan_musnah.nomor = ar_stokkantong.alasan_buang
            WHERE ar_stokkantong.notrans = '$notransaksi'
            GROUP BY alasan_musnah.alasan
            ORDER BY jml DESC";
    //echo $sum;
    $sumq    = mysqli_query($dbi, $sum);
    $sumnum  = mysqli_num_rows($sumq);
    ?>
    <br>
    <!--input type="submit" name="submit2" value="Simpan Transaksi Pemusnahan" onclick="return confirm('PERHATIAN \n \nSimpan transaksi pemusnahan darah ini?');" class="swn_button_blue"-->

    <div
        style="background-color: #ffffff;font-size:16px; color:#0f0f0f;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">
        REKAPITULASI PEMUSNAHAN PRODUK</div>
    <br>
    <table class="list" border=1 cellpadding="2" cellspacing="2" width="50%" style="border-collapse:collapse">
        <tr>
            <th style="height: 40px;text-align: center;font-weight: bold">No.</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Pemusnahan Produk Darah</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Jumlah</th>
        </tr>
        <?php
        $nosum = 1;
        if ($sumnum > 0) {
            while ($dtsum = mysqli_fetch_assoc($sumq)) { ?>
        <tr style="font-size: 12px;height: 30px;">
            <td><?php echo $nosum; ?></td>
            <td><?php echo $dtsum['alasan']; ?></td>
            <td><?php echo $dtsum['jml']; ?></td>
        </tr>
        <?php $nosum++;
            }
        } else { ?>
        <tr>
            <td colspan="3" align="center">Tidak Ada Data</td>
        </tr>
        <?php }
        ?>
    </table>

    <!--Detail Pemusnahan-->
    <br>


    <table width="100%" border=1 cellpadding="5" cellspacing="5" style="border-collapse:collapse">

        <tr>
            <td style="text-align: center;height: 30px;">Petugas Penerima</td>
            <td style="text-align: center;height: 30px;">Disahkan Oleh</td>
            <td style="text-align: center;height: 30px;">Petugas Pemusnahan</td>
        </tr>
        <tr height="80"
            style="font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td style="vertical-align:bottom;text-align: center;height: 30px;">____________________</td>
            <td style="vertical-align:bottom;text-align: center;height: 30px;">____________________</td>
            <td style="vertical-align:bottom;text-align: center;height: 30px;"><?php echo $sql_h1['ptgs_musnah']; ?>
            </td>
        </tr>
    </table>
    <br>
    <table border=0>
        <tr>
            <td align="right"><a
                    href="pmi<?php echo $level; ?>.php?module=musnah_cetakberita&notrans=<?php echo $notransaksi; ?>"><input
                        type="button" class="swn_button_green" value="Cetak Laporan"></td>
            <td align="right"><a href="musnah_label.php?notrans=<?php echo $notransaksi; ?>"><input type="button"
                        class="red" value="Cetak Label"></td>
            <td align="right"><a href="pmi<?php echo $level; ?>.php?module=rincian_darah_buang"><input type="button"
                        class="swn_button_blue" value="Kembali"></a></td>
        </tr>

    </table>

</body>