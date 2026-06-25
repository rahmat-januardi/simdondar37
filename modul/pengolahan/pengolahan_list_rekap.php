<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser    = $_SESSION[namauser];
$namalengkap = $_SESSION[nama_lengkap];
$tglawal     = date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y")));
$hariini     = date("Y-m-d");
?>
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<script language="javascript" src="util.js" type="text/javascript"></script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SIMDONDAR – Rekap Komponen</title>
</head>
<style>
    body {
        font-family: "Lato", sans-serif;
    }

    table {
        border-collapse: collapse;
    }

    tr {
        background-color: #ffffff;
    }

    .highlight {
        background-color: #7CFC00;
    }

    .normal {
        background-color: #ffffff;
    }

    .styled-select select {
        background-color: #FCF9F9;
        border: none;
        width: auto;
        padding: 3px;
        font-size: 15px;
        cursor: pointer;
    }
</style>

<body>
    <?php
    /* ------------------------------------------------------------------ */
    /*  FILTER BUILDER                                                      */
    /* ------------------------------------------------------------------ */
    $filterTanggal = "";

    if (!empty($_POST['tglAftap']) && !empty($_POST['tglAftap1'])) {
        $startAftap    = $_POST['tglAftap'];
        $endAftap      = $_POST['tglAftap1'];
        $filterTanggal = "AND DATE(r.rtgl_aftap) >= '$startAftap' AND DATE(r.rtgl_aftap) <= '$endAftap'";
    } elseif (!empty($_POST['tglProduksi']) && !empty($_POST['tglProduksi1'])) {
        $startProduksi = $_POST['tglProduksi'];
        $endProduksi   = $_POST['tglProduksi1'];
        $filterTanggal = "AND DATE(r.rtgl_olah) >= '$startProduksi' AND DATE(r.rtgl_olah) <= '$endProduksi'";
    } elseif (!empty($_POST['tglKadaluwarsa']) && !empty($_POST['tglKadaluwarsa1'])) {
        $startKadaluwarsa = $_POST['tglKadaluwarsa'];
        $endKadaluwarsa   = $_POST['tglKadaluwarsa1'];
        $filterTanggal    = "AND DATE(r.rtgl_ed) >= '$startKadaluwarsa' AND DATE(r.rtgl_ed) <= '$endKadaluwarsa'";
    } else {
        if (!empty($_POST['waktu']))  $tglawal = $_POST['waktu'];
        if (!empty($_POST['waktu1'])) $hariini = $_POST['waktu1'];
        $filterTanggal = "AND DATE(r.rtgl) >= '$tglawal' AND DATE(r.rtgl) <= '$hariini'";
    }

    $status      = isset($_POST['status'])      ? $_POST['status']      : '';
    $jenisProduk = isset($_POST['jenisProduk']) ? $_POST['jenisProduk'] : '';
    $golDar      = isset($_POST['golDar'])      ? $_POST['golDar']      : '';
    $rhesus      = isset($_POST['rhesus'])      ? $_POST['rhesus']      : '';
    $gold_rhe    = $golDar . $rhesus;
    ?>

    <a name="atas" id="atas"></a>
    <font size="4" color="00008B"><b>Rekap Komponen Darah – Volume Rilis &amp; Volume Komponen</b></font><br><br>

    <form name="cari" method="POST" action="<? echo $PHPSELF ?>">
        <table cellpadding="1" cellspacing="0" border="0">

            <!-- Baris 1: Tanggal Release & Tanggal Produksi -->
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Tanggal&nbsp;Release</td>
                <td>
                    <input name="waktu" id="datepicker" value="<?= $tglawal ?>" type="text" size="10" style="font-family:monospace">
                    s/d
                    <input name="waktu1" id="datepicker1" value="<?= $hariini ?>" type="text" size="10" style="font-family:monospace">
                </td>
                <td>&nbsp;&nbsp;Tanggal&nbsp;Produksi</td>
                <td>
                    <input name="tglProduksi" value="<?= htmlspecialchars(isset($startProduksi) ? $startProduksi : '') ?>" type="date" style="font-family:monospace">
                    s/d
                    <input name="tglProduksi1" value="<?= htmlspecialchars(isset($endProduksi) ? $endProduksi : '') ?>" type="date" style="font-family:monospace">
                </td>
            </tr>

            <!-- Baris 2: Tanggal Aftap -->
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Tanggal&nbsp;Aftap</td>
                <td>
                    <input name="tglAftap" value="<?= htmlspecialchars(isset($startAftap) ? $startAftap : '') ?>" type="date" style="font-family:monospace">
                    s/d
                    <input name="tglAftap1" value="<?= htmlspecialchars(isset($endAftap) ? $endAftap : '') ?>" type="date" style="font-family:monospace">
                </td>
                <td>&nbsp;&nbsp;Tanggal&nbsp;Kadaluwarsa</td>
                <td>
                    <input name="tglKadaluwarsa" value="<?= htmlspecialchars(isset($startKadaluwarsa) ? $startKadaluwarsa : '') ?>" type="date" style="font-family:monospace">
                    s/d
                    <input name="tglKadaluwarsa1" value="<?= htmlspecialchars(isset($endKadaluwarsa) ? $endKadaluwarsa : '') ?>" type="date" style="font-family:monospace">
                </td>
            </tr>

            <!-- Baris 3: Jenis Produk & Golongan Darah -->
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Jenis&nbsp;Produk</td>
                <td>
                    <?php
                    $prodOpts = array(
                        '' => 'SEMUA',
                        'PRC' => 'PRC',
                        'WB' => 'WB',
                        'FFP' => 'FFP',
                        'FP 72' => 'FP72',
                        'LP' => 'LP',
                        'TC' => 'TC'
                    );
                    ?>
                    <select name="jenisProduk" class="styled-select">
                        <?php foreach ($prodOpts as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ($jenisProduk == $val ? 'selected' : '') ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>&nbsp;&nbsp;Golongan&nbsp;Darah</td>
                <td>
                    <select name="golDar" class="styled-select">
                        <option value="">SEMUA</option>
                        <?php foreach (array('A', 'B', 'AB', 'O') as $g): ?>
                            <option value="<?= $g ?>" <?= ($golDar == $g ? 'selected' : '') ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="rhesus" class="styled-select">
                        <option value="">-SEMUA-</option>
                        <option value="+" <?= ($rhesus == '+' ? 'selected' : '') ?>>Positif</option>
                        <option value="-" <?= ($rhesus == '-' ? 'selected' : '') ?>>Negatif</option>
                    </select>
                </td>
            </tr>

            <!-- Baris 4: Status Release & tombol -->
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Status&nbsp;Release</td>
                <td>
                    <select name="status" class="styled-select">
                        <option value="" <?= ($status == ''  ? 'selected' : '') ?>>SEMUA</option>
                        <option value="0" <?= ($status == '0' ? 'selected' : '') ?>>LULUS</option>
                        <option value="1" <?= ($status == '1' ? 'selected' : '') ?>>TIDAK LULUS</option>
                        <option value="2" <?= ($status == '2' ? 'selected' : '') ?>>LULUS DENGAN CATATAN</option>
                    </select>
                </td>
                <td colspan="2">
                    <input type="submit" name="submit" class="swn_button_blue" value="Tampilkan data">
                    &nbsp;<a href="#bawah" class="swn_button_blue">Ke bawah</a>
                    &nbsp;<a href="pmikomponen.php?module=laborat_komponen" class="swn_button_blue">Kembali</a>
                </td>
            </tr>

        </table>
    </form>

    <!-- ================================================================ -->
    <!--  TABEL HASIL                                                      -->
    <!-- ================================================================ -->
    <br>
    <table border="1" cellpadding="4" style="border-collapse:collapse; font-size:11px;">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000; text-align:center;">
            <th>NO.</th>
            <th>NO.&nbsp;KANTONG</th>
            <th>PRODUK</th>
            <th>GOL.&nbsp;DARAH</th>
            <th>TGL.&nbsp;AFTAP</th>
            <th>TGL.&nbsp;PRODUKSI</th>
            <th>TGL.&nbsp;ED</th>
            <th>VOL.&nbsp;RILIS&nbsp;(ml)</th>
            <th>VOL.&nbsp;KOMPONEN&nbsp;(ml)</th>
            <th>STATUS</th>
        </tr>

        <?php
        $no = 0;

        /*
     * JOIN release (alias r) dengan stokkantong (alias sk)
     * berdasarkan no. kantong.
     * Volume rilis   : r.rvolume
     * Volume komponen: sk.volume
     */
        $sql = "SELECT
                r.rnokantong,
                r.rproduk,
                r.rgolda,
                r.rtgl_aftap,
                r.rtgl_olah,
                r.rtgl_ed,
                r.rvolume        AS vol_rilis,
                sk.volume        AS vol_komponen,
                r.rstatus        AS rstatus,
                r.rsatus_ket
            FROM `release` r
            LEFT JOIN `stokkantong` sk ON sk.nokantong = r.rnokantong
            WHERE 1=1
              $filterTanggal
              AND r.rproduk  LIKE '%$jenisProduk%'
              AND r.rgolda   LIKE '%$gold_rhe%'
              AND r.rstatus  LIKE '%$status%'
            ORDER BY r.rnokantong ASC";

        // echo "<!-- DEBUG: $sql -->"; // aktifkan saat debugging

        $qraw = mysql_query($sql);

        while ($tmp = mysql_fetch_assoc($qraw)):
            $no++;

            switch ($tmp['rstatus']) {
                case '0':
                    $statusLabel = 'Lulus';
                    $statusColor = '#d4edda';
                    break;
                case '1':
                    $statusLabel = 'Tidak Lulus';
                    $statusColor = '#f8d7da';
                    break;
                case '2':
                    $statusLabel = 'Lulus dg Catatan';
                    $statusColor = '#fff3cd';
                    break;
                default:
                    $statusLabel = '-';
                    $statusColor = '#ffffff';
            }

            $volRilis     = ($tmp['vol_rilis']     !== null) ? number_format((float)$tmp['vol_rilis'],     2) . ' ml' : '-';
            $volKomponen  = ($tmp['vol_komponen']  !== null) ? number_format((float)$tmp['vol_komponen'],  2) . ' ml' : '-';
        ?>
            <tr style="color:#000000; font-family:Verdana;"
                onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
                <td align="right"><?= $no . '.' ?></td>
                <td align="left" nowrap><?= htmlspecialchars($tmp['rnokantong']) ?></td>
                <td align="center"><?= htmlspecialchars($tmp['rproduk']) ?></td>
                <td align="center"><?= htmlspecialchars($tmp['rgolda']) ?></td>
                <td align="center" nowrap><?= $tmp['rtgl_aftap'] ?></td>
                <td align="center" nowrap><?= $tmp['rtgl_olah']  ?></td>
                <td align="center" nowrap><?= $tmp['rtgl_ed']    ?></td>
                <td align="right"><?= $volRilis ?></td>
                <td align="right"><?= $volKomponen ?></td>
                <td align="center" style="background-color:<?= $statusColor ?>;"><?= $statusLabel ?></td>
            </tr>
        <?php endwhile; ?>

        <?php if ($no == 0): ?>
            <tr>
                <td colspan="10" align="center" style="font-size:13px; padding:10px;">
                    Tidak ada data untuk filter yang dipilih.
                </td>
            </tr>
        <?php endif; ?>

    </table>
    <br>

    <a href="pmikomponen.php?module=laborat_komponen" class="swn_button_blue">Kembali</a>
    <!-- <?php if ($no > 0): ?>
        &nbsp;<a href="pmiqa.php?module=cetak_rekap_komponen&tgl1=<?= $tglawal ?>&tgl2=<?= $hariini ?>&stts=<?= $status ?>"
            class="swn_button_blue">Export ke Excel</a>
    <?php endif; ?> -->
    &nbsp;<a href="#atas" class="swn_button_blue">Ke Atas</a>

    <a name="bawah" id="bawah"></a>
</body>

</html>