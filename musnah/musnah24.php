<?php
ob_start();
session_start();
include('config/dbi_connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 0);

function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

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

    return "Alasan Belum Dipilih";
}

function getStatusKantong($status, $dst_sahktg = "")
{
    switch ($status) {
        case '0':
            return "Kosong";
        case '1':
            return ($dst_sahktg == "0") ? "Aftap" : "Karantina";
        case '2':
            return "Sehat";
        case '3':
            return "Keluar";
        case '4':
            return "Rusak-Reaktif";
        case '5':
            return "Rusak-Gagal";
        case '6':
            return "Rusak-Dimusnahkan";
        default:
            return "Kantong Belum Terdaftar";
    }
}

function getAsalUTD($dbi)
{
    $q = mysqli_query($dbi, "SELECT `id` FROM `utd` WHERE `aktif`='1' LIMIT 1");
    if ($q && ($row = mysqli_fetch_assoc($q))) {
        return $row['id'];
    }
    return '';
}

$nodokumen = "-";

$now   = date("dmyHi");
$today = date("Y-m-d H:i:s");

$namauser    = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';
$namauserlkp = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : '';
$level       = isset($_SESSION['leveluser']) ? $_SESSION['leveluser'] : '';

if ($level == "komponen") {
    $trans = 'KP-' . $now;
} else if ($level == "konfirmasi") {
    $trans = 'KF-' . $now;
} else if ($level == "imltd") {
    $trans = 'IMLTD-' . $now;
} else if ($level == "qa") {
    $trans = 'PR-' . $now;
} else if ($level == "laboratorium") {
    $trans = 'CRM-' . $now;
} else {
    $trans = 'MUSNAH-' . $now;
}


if (isset($_POST['trans']) && $_POST['trans'] != '') {
    $trans = $_POST['trans'];
}

$shift_terima_res = mysqli_query($dbi, "SELECT nama FROM `shift` WHERE `jam` <= CURRENT_TIME() AND `sampai_jam` >= CURRENT_TIME() LIMIT 1");
$shift_terima = $shift_terima_res ? mysqli_fetch_assoc($shift_terima_res) : array('nama' => '');

$message    = "";
$alasanList = getAlasanList($dbi);

if (isset($_POST['submit1'])) {
    $no_kantong = isset($_POST['nomorkantong']) ? trim($_POST['nomorkantong']) : '';
    $trans_post = isset($_POST['trans']) ? trim($_POST['trans']) : '';
    $alasan     = isset($_POST['alasan']) ? trim($_POST['alasan']) : '';

    $no_kantong_sql = mysqli_real_escape_string($dbi, $no_kantong);
    $alasan_sql     = mysqli_real_escape_string($dbi, $alasan);
    $trans_sql      = mysqli_real_escape_string($dbi, $trans_post);

    $cekDup = mysqli_query(
        $dbi,
        "SELECT `noKantong` FROM `ar_stokkantongtemp`
             WHERE `noKantong` = '$no_kantong_sql'
               AND `bagian` = '$level'
               AND `user` = '$namauser'
             LIMIT 1"
    );

    if ($cekDup && mysqli_num_rows($cekDup) > 0) {
        $message = "Nomor <b>$no_kantong_sql SUDAH ADA</b> dalam list";
    } else {
        $cari = "
                SELECT
                    s.`noKantong`, s.`kantongAsal`, s.`AsalUTD`, s.`mu`, s.`Status`, s.`StatTempat`,
                    s.`tglpengolahan`, s.`tglTerima`, s.`kodePendonor`, s.`jenis`, s.`produk`,
                    s.`gol_darah`, s.`RhesusDrh`, s.`merk`, s.`tgl_Aftap`, s.`sah`,
                    s.`tglperiksa`, s.`kadaluwarsa`, s.`volume`
                FROM `stokkantong` s
                LEFT JOIN `htransaksi` h ON s.`noKantong` = h.`NoKantong`
                WHERE s.`noKantong` = '$no_kantong_sql'
            ";

        $ck_res = mysqli_query($dbi, $cari);
        $ck = $ck_res ? mysqli_fetch_assoc($ck_res) : array();

        if (!$ck) {
            $message = "Nomor <b>$no_kantong_sql</b> tidak ditemukan";
        } else {
            $asalUTD = '';
            if (isset($ck['AsalUTD']) && trim($ck['AsalUTD']) !== '') {
                $asalUTD = $ck['AsalUTD'];
            } else {
                $asalUTD = getAsalUTD($dbi);
            }

            $asalUTD_sql = mysqli_real_escape_string($dbi, $asalUTD);

            if (
                ($ck['Status'] == "0") ||
                ($ck['Status'] == "1") ||
                ($ck['Status'] == "2") ||
                ($ck['Status'] == "7") ||
                ($ck['Status'] == "4") ||
                ($ck['Status'] == "5")
            ) {
                $jenis         = mysqli_real_escape_string($dbi, $ck['jenis']);
                $status        = mysqli_real_escape_string($dbi, $ck['Status']);
                $tglTerima     = mysqli_real_escape_string($dbi, $ck['tglTerima']);
                $volume        = mysqli_real_escape_string($dbi, $ck['volume']);
                $merk          = mysqli_real_escape_string($dbi, $ck['merk']);
                $produk        = mysqli_real_escape_string($dbi, $ck['produk']);
                $sah           = mysqli_real_escape_string($dbi, $ck['sah']);
                $gol_darah     = mysqli_real_escape_string($dbi, $ck['gol_darah']);
                $RhesusDrh     = mysqli_real_escape_string($dbi, $ck['RhesusDrh']);
                $StatTempat    = mysqli_real_escape_string($dbi, $ck['StatTempat']);
                $kodePendonor  = mysqli_real_escape_string($dbi, $ck['kodePendonor']);
                $tgl_Aftap     = mysqli_real_escape_string($dbi, $ck['tgl_Aftap']);
                $kadaluwarsa   = mysqli_real_escape_string($dbi, $ck['kadaluwarsa']);
                $tglpengolahan = mysqli_real_escape_string($dbi, $ck['tglpengolahan']);
                $mu            = mysqli_real_escape_string($dbi, $ck['mu']);

                $sql_tmp = "
                        INSERT INTO `ar_stokkantongtemp`
                        (
                            notrans, bagian, noKantong, jenis, `Status`, tglTerima, volume, merk, kantongAsal,
                            produk, sah, gol_darah, RhesusDrh,  StatTempat, kodePendonor,
                            statKonfirmasi, statQC, AsalUTD, tgl_Aftap, kadaluwarsa, tglpengolahan, mu,
                            alasan_buang, tgl_buang, user
                        )
                        VALUES
                        (
                            '$trans_post', '$level', '$no_kantong_sql', '$jenis', '$status', '$tglTerima', '$volume', '$merk', '$asalUTD_sql',
                            '$produk', '$sah', '$gol_darah', '$RhesusDrh',  '$StatTempat', '$kodePendonor',
                            '1', '0', '$asalUTD_sql', '$tgl_Aftap', '$kadaluwarsa', '$tglpengolahan', '$mu',
                            '$alasan_sql', '$today', '$namauser'
                        )
                    ";

                $add = mysqli_query($dbi, $sql_tmp);

                if ($add) {
                    $message = "Nomor <b>$no_kantong_sql</b> berhasil dimasukkan dalam list";
                } else {
                    // cek kantong di ar_stokkantongtemp
                    $cekListTemp = mysqli_query($dbi, "SELECT `bagian`, `noKantong`, `user` FROM `ar_stokkantongtemp` WHERE  `noKantong` = '$no_kantong_sql' LIMIT 1");
                    $rowListTemp = mysqli_fetch_assoc($cekListTemp);
                    $rowBagian = $rowListTemp['bagian'];
                    $rowUser   = $rowListTemp['user'];
                    $message = "Gagal memasukkan nomor <b>$no_kantong_sql</b> Karena sudah ada dilist pada bidang <b> $rowBagian </b> dengam petugas <b>$rowUser</b>";
                    mysqli_error($dbi);
                }
            } elseif ($ck['Status'] == "6") {
                $message = "Nomor <b>$no_kantong_sql</b> sudah dimusnahkan";
            } elseif ($ck['Status'] == "3") {
                $message = "Nomor <b>$no_kantong_sql</b> tidak dapat dimusnahkan karena status sudah <b>keluar</b>";
            } else {
                $message = "Nomor <b>$no_kantong_sql</b> tidak dapat dimasukkan dalam list, silahkan cek kantong";
            }
        }
    }
}

if (isset($_POST['submit2'])) {
    $trans_post   = isset($_POST['trans']) ? trim($_POST['trans']) : '';
    $shift        = isset($shift_terima['nama']) ? $shift_terima['nama'] : '';

    $trans_sql    = mysqli_real_escape_string($dbi, $trans_post);
    $shift_sql    = mysqli_real_escape_string($dbi, $shift);
    $namauser_sql = mysqli_real_escape_string($dbi, $namauser);


    // Hitung total volume dari list sementara
    $sumq = "
        SELECT COALESCE(
            SUM(CAST(REPLACE(volume, ',', '.') AS DECIMAL(10,2))),
            0
        ) AS total_volume_ml
        FROM ar_stokkantongtemp
        WHERE bagian='$level'
          AND user='$namauser_sql'
    ";
    $sumres = mysqli_query($dbi, $sumq);
    if (!$sumres) {
        $message = "Gagal hitung total volume: " . mysqli_error($dbi);
        exit;
    }

    $sumrow = mysqli_fetch_assoc($sumres);
    $total_volume_ml = isset($sumrow['total_volume_ml']) ? floatval($sumrow['total_volume_ml']) : 0;
    $berat_kg = round($total_volume_ml / 1000, 3);

    $sq  = "SELECT * FROM `ar_stokkantongtemp` WHERE `bagian`='$level' AND `user`='$namauser_sql'";
    $tmp = mysqli_query($dbi, $sq);

    if (!$tmp) {
        $message = "Gagal baca temporary: " . mysqli_error($dbi);
        exit;
    } else {

        $sukses_semua = 1;
        $jml = 0;
        $rows_temp = array();

        while ($row_temp = mysqli_fetch_assoc($tmp)) {
            $rows_temp[] = $row_temp;
        }

        foreach ($rows_temp as $dta) {
            $jml++;

            $noKantongCurrent = isset($dta['noKantong']) ? trim($dta['noKantong']) : '';
            $noKantongSql = mysqli_real_escape_string($dbi, $noKantongCurrent);

            // cek dahulu nokantong pada ar_stokkantong
            $cekDup2 = mysqli_query($dbi, "SELECT `noKantong` FROM `ar_stokkantong` WHERE `noKantong` = '$noKantongSql' LIMIT 1");
            if ($cekDup2 && mysqli_num_rows($cekDup2) > 0) {
                $sq_del_single = mysqli_query(
                    $dbi,
                    "DELETE FROM `ar_stokkantongtemp`
                     WHERE `bagian`='$level'
                       AND `user`='$namauser_sql'
                       AND `noKantong`='$noKantongSql'"
                );

                if (!$sq_del_single) {
                    $sukses_semua = 0;
                    $message = "Gagal hapus data duplikat dari temporary: " . mysqli_error($dbi);
                    break;
                }

                $message = "Nomor <b>$noKantongCurrent</b> sudah ada di database pemusnahan dan dilewati.";
                continue;
            }

            $asalUTDFinal = (isset($dta['AsalUTD']) && trim($dta['AsalUTD']) !== '')
                ? $dta['AsalUTD']
                : getAsalUTD($dbi);

            $q_detail = "
                    INSERT INTO `ar_stokkantong`
                    (
                        notrans, bagian, noKantong, jenis, `Status`, tglTerima, volume, merk, kantongAsal,
                        produk, sah, gol_darah, RhesusDrh, StatTempat, kodePendonor,
                        statKonfirmasi, statQC, AsalUTD, tgl_Aftap, kadaluwarsa, tglpengolahan, mu,
                        alasan_buang, tgl_buang, user
                    )
                    VALUES
                    (
                        '$trans_sql', '$level',
                        '" . mysqli_real_escape_string($dbi, $dta['noKantong']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['jenis']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['Status']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['tglTerima']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['volume']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['merk']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['kantongAsal']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['produk']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['sah']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['gol_darah']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['RhesusDrh']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['StatTempat']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['kodePendonor']) . "',
                        '1', '1',
                        '" . mysqli_real_escape_string($dbi, $asalUTDFinal) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['tgl_Aftap']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['kadaluwarsa']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['tglpengolahan']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['mu']) . "',
                        '" . mysqli_real_escape_string($dbi, $dta['alasan_buang']) . "',
                        '$today',
                        '$namauser_sql'
                    )
                ";

            $add_d = mysqli_query($dbi, $q_detail);
            if (!$add_d) {
                $sukses_semua = 0;
                $message = "Gagal simpan detail: " . mysqli_error($dbi);
                break;
            }

            $noKantong_del = mysqli_real_escape_string($dbi, $dta['noKantong']);
            $updatektg3 = mysqli_query($dbi, "UPDATE `stokkantong` SET `Status`='6' WHERE `noKantong`='$noKantong_del'");
            if (!$updatektg3) {
                $sukses_semua = 0;
                $message = "Gagal update stokkantong: " . mysqli_error($dbi);
                break;
            }

            $sq_del_single = mysqli_query(
                $dbi,
                "DELETE FROM `ar_stokkantongtemp`
                 WHERE `bagian`='$level'
                   AND `user`='$namauser_sql'
                   AND `noKantong`='$noKantongSql'"
            );
            if (!$sq_del_single) {
                $sukses_semua = 0;
                $message = "Gagal hapus temporary: " . mysqli_error($dbi);
                break;
            }

            $log_mdl = $level;
            $log_aksi = 'Pemusnahan Produk Darah : ' . $dta['noKantong'] . '  No. transaksi: ' . $trans . ' Kode Pemusnahan : ' . $dta['alasan_buang'];
            include "user_log.php";
        }

        if ($sukses_semua > 0 && $jml > 0) {
            $sa = "INSERT INTO `ar_stokkantong_trans`
           (notrans, tgl, bagian, ptgs_musnah, shift, berat)
           VALUES
           ('$trans_sql', '$today', '$level', '$namauser_sql', '$shift_sql', '$berat_kg')";

            $a = mysqli_query($dbi, $sa);

            if (!$a) {
                $message = "Gagal simpan transaksi: " . mysqli_error($dbi);
                exit;
            } else {

                echo "TRANSAKSI PEMUSNAHAN SUKSES, Kantong berpindah ke PEMUSNAHAN";
                // echo "<meta http-equiv='refresh' content='2;url=musnah_label.php?notrans=$trans_sql'>";
                echo "<meta http-equiv='refresh' content='2;url=pmi$level.php?module=musnahlist'>";
                exit;
            }
        }
    }
}
?>

<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>

<style>
.awesomeText {
    color: #000;
    font-size: 100%;
}

#serahterima {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    font-size: 14px;
    border-collapse: collapse;
}

#serahterima td,
#serahterima th {
    border: 1px solid #ddd;
    padding: 3px;
}

#serahterima tr:nth-child(even) {
    background-color: #ffe6e6;
}

#serahterima tr:hover {
    background-color: #ddd;
}

#serahterima th {
    padding-top: 2px;
    padding-bottom: 2px;
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

#entrybox {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    font-size: 14px;
    border-collapse: collapse;
}

#entrybox td,
#entrybox th {
    border: 1px solid #ddd;
    background-color: #ffe6e6;
    padding: 3px;
}

#entrybox th {
    padding-top: 2px;
    padding-bottom: 2px;
    text-align: left;
    font-weight: lighter;
    background-color: #ffe6e6;
    color: #000000;
}

#entrybox input,
#entrybox select {
    padding-top: 2px;
    padding-bottom: 2px;
    text-align: left;
    font-weight: bold;
    background-color: #e6ffe6;
    color: #000000;
}
</style>

<script type="text/javascript">
function setFocus() {
    var el = document.getElementById('nomorkantong');
    if (el) {
        el.focus();
    }
}

function validasiInputList() {
    var alasan = document.getElementById('alasan').value;
    var noKantong = document.getElementById('nomorkantong').value;

    if (alasan === '') {
        alert('Alasan pemusnahan wajib dipilih sebelum masuk list.');
        document.getElementById('alasan').focus();
        return false;
    }

    if (noKantong === '') {
        alert('Nomor kantong tidak boleh kosong.');
        document.getElementById('nomorkantong').focus();
        return false;
    }

    return true;
}
</script>

<body onLoad="setFocus();">
    <a name="atas" id="atas"></a>

    <center>
        <div
            style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">
            PEMUSNAHAN PRODUK DARAH
        </div>
    </center>

    <p>
        <hr style="width: 100%;text-align:left;margin-left:0;color: #0099ff">

    <form name="sahdarah" method="post" enctype="multipart/form-data">
        <table
            style="width: 100%; border-collapse: collapse;border: 2px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr>
                <td style="vertical-align: top; width:100%;">
                    <table id="serahterima" style="width: 98%;">
                        <tr>
                            <th>Nomor Transaksi</th>
                            <td>
                                <input type="hidden" name="trans" value="<?php echo e($trans); ?>">
                                <?php echo e($trans); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Asal Pemusnahan</th>
                            <td><?php echo strtoupper(e($level)); ?></td>
                        </tr>
                        <tr>
                            <th>Petugas Pemusnahan</th>
                            <td><?php echo strtoupper(e($namauserlkp)); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <br>

        <table id="entrybox" width="100%"
            style="border-collapse: collapse;border: 2px solid #ff0000;width: 100%; box-shadow: 1px 2px 2px #800000;">
            <tr>
                <td>Alasan Pemusnahan <span style="color:red">*</span></td>
                <td>
                    <select name="alasan" id="alasan" onchange="document.getElementById('nomorkantong').focus();">
                        <option value="">Pilih Alasan</option>
                        <?php foreach ($alasanList as $kode => $nama) { ?>
                        <option value="<?php echo e($kode); ?>"
                            <?php echo ($alasan !== '' && $alasan == $kode) ? 'selected' : ''; ?>>
                            <?php echo e($nama); ?>
                        </option>
                        <?php } ?>
                    </select>
                </td>
                <td>Masukkan Nomor Kantong</td>
                <td><input type="text" name="nomorkantong" id="nomorkantong"></td>
                <td><input type="submit" name="submit1" value="Ok" class="swn_button_red" style="color: #ffff00"
                        onclick="return validasiInputList();"></td>
            </tr>
            <tr>
                <td style="height: 30px">Status Proses</td>
                <td colspan="4"><?php echo $message; ?></td>
            </tr>
        </table>

        <br>

        <table id="serahterima" width="100%"
            style="border-collapse: collapse;border: 1px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr style="font-size: 12px; ">
                <th style="height: 40px;text-align: center;font-weight: bold">No</th>
                <th style="height: 40px;text-align: center;font-weight: bold">No Kantong</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jenis<br>Ktg</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Merk</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Status</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Gol<br>Drh</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Tgl<br>Aftap</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Kode Donor</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Alasan Pemusnahan</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Aksi</th>
            </tr>
            <?php
            $qry = "SELECT * FROM ar_stokkantongtemp WHERE bagian='$level' AND user='$namauser' ORDER BY inserr_on DESC";
            $sql = mysqli_query($dbi, $qry);

            $rows = array();
            if ($sql) {
                while ($r = mysqli_fetch_assoc($sql)) {
                    $rows[] = $r;
                }
            }

            $no = count($rows) + 1;
            foreach ($rows as $tmp) {
                $no--;

                $ckt_status = getStatusKantong($tmp['Status'], isset($tmp['dst_sahktg']) ? $tmp['dst_sahktg'] : '');
                $alsn = getAlasanLabel($tmp['alasan_buang'], $dbi);
            ?>
            <tr style="font-size: 12px">
                <td align="right"><?php echo $no; ?>.</td>
                <td><?php echo e($tmp['noKantong']); ?></td>
                <td align="center"><?php echo e($tmp['jenis']); ?></td>
                <td><?php echo e($tmp['merk']); ?></td>
                <td><?php echo e($ckt_status); ?></td>
                <td style="text-align: center"><?php echo e($tmp['gol_darah'] . $tmp['RhesusDrh']); ?></td>
                <td align="center"><?php echo e($tmp['tgl_Aftap']); ?></td>
                <td align="center"><?php echo e($tmp['kodePendonor']); ?></td>
                <td align="center"><?php echo e($alsn); ?></td>
                <td>
                    <a href="pmi<?php echo e($level); ?>.php?module=musnahdelrow&op=del&ktg=<?php echo urlencode($tmp['noKantong']); ?>&usr=<?php echo urlencode($namauser); ?>&bagian=<?php echo urlencode($level); ?>"
                        onclick="return confirm('PERHATIAN \n \nYakin akan menghapus Nomor kantong \n<?php echo e($tmp['noKantong']); ?> ?');">
                        Hapus
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <hr style="width: 100%;text-align:left;margin-left:0; line-height: 1px">

        <input type="submit" name="submit2" value="Simpan Transaksi Pemusnahan"
            onclick="return confirm('PERHATIAN \n \nSimpan transaksi pemusnahan darah ini?');" class="swn_button_blue">

        <a href="pmi<?php echo e($level); ?>.php?module=musnahbatal&op=batal&usr=<?php echo urlencode($namauser); ?>&bagian=<?php echo urlencode($level); ?>"
            onclick="return confirm('PERHATIAN \n \nYakin akan membatalkan transaksi pemusnahan darah ini?');"
            class="swn_button_blue">Batalkan Transaksi Pemusnahan</a>
    </form>
</body>