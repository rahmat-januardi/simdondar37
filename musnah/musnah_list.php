<?php

/***********************************************
 * Author  : suwena
 * Date    : 26 Mei 2018
 * Fungsi  : Form Serah Terima Darah dari Aftap/Mobile unit utk Karantina
 * Keterangan Modul :
 *      Pengganti pengesahan kantong
 *      Sekaligus membuat formulir Serah Terima ke
 *          - Bag Karantina atau Komponen
 *          - Bag Uji Saring Darah IMLTD
 *          - Bag Uji Konfirmasi Golongan Darah
 *      Status Darah yang sah langsung menjadi KARANTINA
 *      Stok Position : PENYIMPANAN DARAH KARANTINA
 * Table terkait :
 *      - Select : stokkantong join htransaksi
 *      - exec   : serahterima_h, serahterima_detail, serahterima_detail_tmp
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
include('config/dbi_connect.php');

$namauser    = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';
$namalengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : '';
$level       = isset($_SESSION['leveluser']) ? $_SESSION['leveluser'] : '';

$tglawal = date("Y-m-d");
$hariini = date("Y-m-d");
$notransaksi = "";

function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

function build_self_url($extra = array())
{
    $params = array();

    if (isset($_GET['module']) && $_GET['module'] !== '') {
        $params['module'] = $_GET['module'];
    }

    if (isset($_GET['no']) && $_GET['no'] !== '') {
        $params['no'] = $_GET['no'];
    }

    foreach ($extra as $k => $v) {
        if ($v === null) {
            if (isset($params[$k])) {
                unset($params[$k]);
            }
        } else {
            $params[$k] = $v;
        }
    }

    $qs = http_build_query($params);
    return $_SERVER['PHP_SELF'] . ($qs ? '?' . $qs : '');
}

function getStatusLabel($stat)
{
    switch ($stat) {
        case '1':
            return 'Sudah Diambil';
        default:
            return 'Belum Diambil';
    }
}

function getBgStatus($stat)
{
    return ($stat == '1') ? "background-color:green;color:white;" : "background-color:red;color:white;";
}
?>

<body>
    <a name="atas" id="atas"></a>
    <div
        style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">
        DAFTAR TRANSAKSI PEMUSNAHAN PRODUK DARAH
    </div>

    <br>

    <?php
    if (isset($_POST['waktu']) && $_POST['waktu'] != '') {
        $tglawal = $_POST['waktu'];
    }
    if (isset($_POST['waktu1']) && $_POST['waktu1'] != '') {
        $hariini = $_POST['waktu1'];
    }

    $srcshift = '';
    $srcstatus = '';
    $srctrans  = '';

    $qshift = "";
    $qambil = "";
    $qtrans = "";

    if (isset($_POST['shift']) && $_POST['shift'] != '') {
        $srcshift = $_POST['shift'];
        $srcshift_sql = mysqli_real_escape_string($dbi, $srcshift);
        $qshift = " AND t.shift = '$srcshift_sql' ";
    }

    if (isset($_POST['pengambilan']) && $_POST['pengambilan'] !== '') {
        $srcstatus = $_POST['pengambilan'];
        $srcstatus_sql = mysqli_real_escape_string($dbi, $srcstatus);
        $qambil = " AND t.stat = '$srcstatus_sql' ";
    }

    if (isset($_POST['transaksimusnah']) && trim($_POST['transaksimusnah']) != '') {
        $srctrans = trim($_POST['transaksimusnah']);
        $srctrans_sql = mysqli_real_escape_string($dbi, $srctrans);
        $qtrans = " AND t.notrans LIKE '%$srctrans_sql%' ";
    }

    $tglawal_sql = mysqli_real_escape_string($dbi, $tglawal);
    $hariini_sql  = mysqli_real_escape_string($dbi, $hariini);

    // Query utama: hanya tampilkan transaksi yang punya minimal 1 produk
    $qry = "
    SELECT
        t.*,
        COUNT(d.noKantong) AS jmltrans
    FROM ar_stokkantong_trans t
    LEFT JOIN ar_stokkantong d ON d.notrans = t.notrans
    WHERE DATE(t.tgl) >= '$tglawal_sql'
      AND DATE(t.tgl) <= '$hariini_sql'
      $qshift
      $qambil
      $qtrans
    GROUP BY t.notrans
    HAVING jmltrans > 0
    ORDER BY t.tgl ASC
";

    $sql = mysqli_query($dbi, $qry);
    if (!$sql) {
        die('Query gagal: ' . mysqli_error($dbi));
    }
    ?>

    <form name="cari" method="POST" action="<?php echo e(build_self_url()); ?>">
        <table id="serahterima" cellpadding="1" cellspacing="0" border="0">
            <tr class="field">
                <td align="left" nowrap>Dari tanggal :</td>
                <td><input name="waktu" id="datepicker" value="<?php echo e($tglawal); ?>" type="text" size="10"></td>

                <td align="right" nowrap>Sampai tanggal :</td>
                <td><input name="waktu1" id="datepicker1" value="<?php echo e($hariini); ?>" type="text" size="10"></td>

                <td align="right" nowrap>Shift :</td>
                <td>
                    <select name="shift">
                        <option value="">Semua</option>
                        <option value="I" <?php echo ($srcshift == 'I') ? 'selected' : ''; ?>>I</option>
                        <option value="II" <?php echo ($srcshift == 'II') ? 'selected' : ''; ?>>II</option>
                        <option value="III" <?php echo ($srcshift == 'III') ? 'selected' : ''; ?>>III</option>
                        <option value="IV" <?php echo ($srcshift == 'IV') ? 'selected' : ''; ?>>IV</option>
                    </select>
                </td>

                <td align="right" nowrap>Status :</td>
                <td>
                    <select name="pengambilan">
                        <option value="">Semua</option>
                        <option value="0" <?php echo ($srcstatus === '0') ? 'selected' : ''; ?>>Belum Diambil</option>
                        <option value="1" <?php echo ($srcstatus === '1') ? 'selected' : ''; ?>>Sudah Diambil</option>
                    </select>
                </td>

                <td align="right" nowrap>No. Transaksi :</td>
                <td><input name="transaksimusnah" type="text" value="<?php echo e($srctrans); ?>"></td>

                <td><input type="submit" name="submit" class="swn_button_blue" value="Ok"></td>
            </tr>
        </table>
    </form>

    <br>

    <table id="serahterima" width="100%"
        style="border-collapse: collapse;border: 2px solid #808080;box-shadow: 1px 2px 2px #000000;">
        <tr style="font-size: 12px">
            <th style="height: 40px;text-align: center;font-weight: bold">No</th>
            <th style="height: 40px;text-align: center;font-weight: bold">No. Transaksi</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Aksi</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Tanggal</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Shift</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Petugas Pemusnahan</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Lampiran Berita Acara</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Instansi<br>Pengelola Limbah</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Petugas<br>Pengelola</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Status</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Jumlah Produk</th>
            <th style="height: 40px;text-align: center;font-weight: bold">Berat (Kg)</th>
        </tr>

        <?php
        $no = 0;
        $jmltotal = 0;
        $brtotal = 0.0;
        $ada_data = false;

        while ($tmp = mysqli_fetch_assoc($sql)) {
            $ada_data = true;
            $no++;

            $jmltrans = isset($tmp['jmltrans']) ? intval($tmp['jmltrans']) : 0;
            $beratkg  = isset($tmp['berat']) ? floatval($tmp['berat']) : 0.0;

            $jmltotal += $jmltrans;
            $brtotal  += $beratkg;

            $status_label = getStatusLabel($tmp['stat']);
            $bg = getBgStatus($tmp['stat']);

            $pt = '-';
            if (isset($tmp['pengelola']) && trim($tmp['pengelola']) != '') {
                $kodePengelola = mysqli_real_escape_string($dbi, $tmp['pengelola']);
                $qpt = mysqli_query($dbi, "SELECT Nama FROM supplier WHERE Kode = '$kodePengelola' LIMIT 1");
                if ($qpt && mysqli_num_rows($qpt) > 0) {
                    $rowpt = mysqli_fetch_assoc($qpt);
                    $pt = $rowpt['Nama'];
                }
            }
        ?>
        <tr style="font-size: 12px;height: 30px;">
            <td align="right"><?php echo $no; ?>.</td>
            <td style="text-align: center"><?php echo e($tmp['notrans']); ?></td>

            <td style="text-align: left">
                <?php if ($tmp['stat'] == "0") { ?>
                <a
                    href="pmi<?php echo e($level); ?>.php?module=musnah_serahterima&no=<?php echo urlencode($tmp['notrans']); ?>">
                    <input type="button" class="swn_button_red"
                        onclick="return confirm('Proses Pengiriman Limbah Produk <?php echo e($tmp['notrans']); ?> ?')"
                        value="Proses" />
                </a>
                &nbsp;
                <a
                    href="pmi<?php echo e($level); ?>.php?module=musnah_cetakberita&notrans=<?php echo urlencode($tmp['notrans']); ?>">
                    <input type="button" class="swn_button_blue" value="Cetak" />
                </a>
                &nbsp;
                <a
                    href="pmi<?php echo e($level); ?>.php?module=musnah_rpt_view&notrans=<?php echo urlencode($tmp['notrans']); ?>">
                    <input type="button" class="swn_button_green" value="Lihat" />
                </a>
                <?php } else { ?>
                <a
                    href="pmi<?php echo e($level); ?>.php?module=musnah_rpt_view&notrans=<?php echo urlencode($tmp['notrans']); ?>">
                    <input type="button" class="swn_button_green" value="Lihat" />
                </a>
                <?php } ?>
            </td>

            <td style="text-align: center"><?php echo e($tmp['tgl']); ?></td>
            <td style="text-align: center"><?php echo e($tmp['shift']); ?></td>
            <td style="text-align: center"><?php echo e($tmp['ptgs_musnah']); ?></td>

            <td style="text-align: center">
                <?php
                    if (empty($tmp['file_berita_acara'])) {
                        echo '-';
                    } else {
                        $file = $tmp['file_berita_acara'];
                        $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $url  = 'musnah/file_berita_acara/' . $file;

                        echo '<a href="' . e($url) . '" target="_blank">';
                        if (in_array($ext, array('jpg', 'jpeg', 'png'))) {
                            echo 'Lihat Gambar';
                        } else {
                            echo 'Lihat PDF';
                        }
                        echo '</a>';
                    }
                    ?>
            </td>

            <td style="text-align: center"><?php echo e($pt); ?></td>
            <td style="text-align: center"><?php echo e($tmp['ptgs_limbah']); ?></td>

            <td style="text-align: center; <?php echo $bg; ?>">
                <?php echo e($status_label); ?>
            </td>

            <td style="text-align: right"><b><?php echo $jmltrans; ?></b></td>
            <td style="text-align: right"><?php echo number_format($beratkg, 3, ',', '.'); ?></td>
        </tr>
        <?php
        }

        if (!$ada_data) {
        ?>
        <tr style="font-size: 16px;height: 40px; text-align: center;">
            <td colspan="12">Tidak ada data</td>
        </tr>
        <?php
        }
        ?>

        <tr style="font-size: 12px;height: 40px; text-align: center;">
            <td style="text-align: right" colspan="10"><b>TOTAL</b></td>
            <td style="text-align: right"><b><?php echo $jmltotal; ?></b></td>
            <td style="text-align: right"><b><?php echo number_format($brtotal, 3, ',', '.'); ?></b></td>
        </tr>
    </table>

    <br>
    <div style="font-size:10px; color:#000000; font-family: Helvetica, Arial, sans-serif;">Build : 21-08-2024</div>