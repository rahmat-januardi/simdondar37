<?php
ob_start();
session_start();
require_once('config/db_connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 0);

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function esc($str)
{
    return mysql_real_escape_string(trim($str));
}

function paramNama($jenisPeriksa)
{
    switch ((int)$jenisPeriksa) {
        case 0:
            return 'HBsAg';
        case 1:
            return 'HCV';
        case 2:
            return 'HIV';
        case 3:
            return 'Sifilis';
        default:
            return '';
    }
}

function normalisasiNoKantongHasil($nokantong)
{
    $nokantong = trim($nokantong);
    if ($nokantong === '') {
        return '';
    }

    $akhir = strtoupper(substr($nokantong, -1));
    if ($akhir !== 'A') {
        $nokantong = substr($nokantong, 0, strlen($nokantong) - 1) . 'A';
    }

    return $nokantong;
}

$header_id = isset($_GET['header_id']) ? (int)$_GET['header_id'] : 0;
if ($header_id <= 0) {
    die('Header tidak valid.');
}

$qHeader = mysql_query("SELECT * FROM sampel_panel_trans WHERE id = '{$header_id}' LIMIT 1");
if (!$qHeader || mysql_num_rows($qHeader) == 0) {
    die('Data header tidak ditemukan.');
}

$header = mysql_fetch_assoc($qHeader);
$notrans = isset($header['notrans']) ? trim($header['notrans']) : '';
$notrans_sql = esc($notrans);

$qDetail = mysql_query("SELECT * FROM sampel_panel_detail WHERE notrans = '{$notrans_sql}' ORDER BY id ASC");
if (!$qDetail) {
    die(mysql_error());
}

$detailRows = array();
while ($r = mysql_fetch_assoc($qDetail)) {
    $detailRows[] = $r;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Label Sampel Panel</title>
    <style>
    @page {
        size: 86mm 56mm;
        margin: 0;
    }

    html,
    body {
        width: 86mm;
        height: 56mm;
        margin: 0;
        padding: 0;
        background: #fff;
        font-family: Arial, sans-serif;
    }

    body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .label-page {
        width: 86mm;
        height: 56mm;
        box-sizing: border-box;
        page-break-after: always;
        overflow: hidden;
        padding: 1.5mm;
    }

    .label-box {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        border: 0.35mm solid #000;
        padding: 1.5mm 2mm 1.2mm 2mm;
    }

    .title {
        text-align: center;
        font-size: 9.5pt;
        font-weight: 700;
        line-height: 1.1;
        padding-bottom: 1mm;
        margin-bottom: 1mm;
        border-bottom: 0.25mm solid #000;
    }

    .info {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 6.8pt;
        line-height: 1.1;
    }

    .info td {
        padding: 0;
        vertical-align: top;
    }

    .info .lbl {
        width: 35mm;
        font-weight: 700;
        padding-right: 1mm;
    }

    .info .colon {
        width: 2mm;
        text-align: center;
    }

    .barcode-wrap {
        text-align: center;
        margin-top: 1.2mm;
    }

    .barcode-svg {
        width: 100%;
        height: 12mm;
        display: block;
    }

    .no-kantong {
        text-align: center;
        font-size: 9pt;
        font-weight: 700;
        margin-top: 0.5mm;
        line-height: 1;
    }

    .result-title {
        text-align: center;
        font-size: 13.5pt;
        font-weight: 700;
        line-height: 1.1;
        padding: 3mm 0 0.9mm 0;
        margin-top: 2mm;
        border-top: 0.25mm solid #000;
    }

    .result-title-nr {
        text-align: center;
        font-size: 13.5pt;
        font-weight: 700;
        line-height: 1.1;
        padding: 5mm 0 0.9mm 0;
        margin-top: 4mm;
        border-top: 0.25mm solid #000;
    }

    .checks {
        width: 100%;
        display: table;
        table-layout: fixed;
        border-top: 0.25mm solid #000;
        margin-top: 2mm;
    }

    .check-item {
        display: table-cell;
        width: 25%;
        text-align: center;
        vertical-align: middle;
        font-size: 6.4pt;
        line-height: 1;
        padding: 0.8mm 0 0.5mm 0;
        border-right: 0.25mm solid #000;
        white-space: nowrap;
    }

    .check-item:last-child {
        border-right: 0;
    }

    .check-item input[type="checkbox"] {
        width: 2.8mm;
        height: 2.8mm;
        vertical-align: middle;
        margin: 0 1px 0 0;
    }
    </style>
</head>

<body onload="window.print()">

    <?php
    $asal_sampel = 'UDDP';

    foreach ($detailRows as $detail) {
        $nokantong = isset($detail['nokantong']) ? trim($detail['nokantong']) : '';
        $jenisSampel = isset($detail['jenis_sampel']) ? strtoupper(trim($detail['jenis_sampel'])) : 'NONREAKTIF';
        $nokantong_sql = esc($nokantong);

        $tglAftap = '-';
        $tglPengolahan = '-';

        $qStok = mysql_query("SELECT tgl_Aftap, tglpengolahan FROM stokkantong WHERE noKantong = '{$nokantong_sql}' LIMIT 1");
        if ($qStok && mysql_num_rows($qStok) > 0) {
            $stok = mysql_fetch_assoc($qStok);
            if (!empty($stok['tgl_Aftap'])) {
                $tglAftap = date('d-m-Y H:i', strtotime($stok['tgl_Aftap']));
            }
            if (!empty($stok['tglpengolahan'])) {
                $tglPengolahan = date('d-m-Y H:i', strtotime($stok['tglpengolahan']));
            }
        }

        $hasilLabel = ($jenisSampel === 'REAKTIF') ? 'REAKTIF' : 'NON REAKTIF';
        $checked = array(
            'HBsAg'   => false,
            'HCV'     => false,
            'HIV'     => false,
            'Sifilis' => false
        );

        if ($jenisSampel === 'REAKTIF') {
            $nokantongCari = normalisasiNoKantongHasil($nokantong);
            $nokantongCariSql = esc($nokantongCari);

            $qHasil = mysql_query("SELECT noKantong, hasil, jenisPeriksa FROM hasilelisa WHERE noKantong = '{$nokantongCariSql}' AND hasil = 1");
            if ($qHasil && mysql_num_rows($qHasil) > 0) {
                while ($hr = mysql_fetch_assoc($qHasil)) {
                    $namaParam = paramNama($hr['jenisPeriksa']);
                    if ($namaParam !== '') {
                        $checked[$namaParam] = true;
                    }
                }
            }
        }
    ?>
    <div class="label-page">
        <div class="label-box">
            <div class="title">LABEL KANTONG UNTUK SAMPEL PANEL</div>

            <table class="info">
                <tr>
                    <td class="lbl">ASAL SAMPEL</td>
                    <td class="colon">:</td>
                    <td><?php echo h($asal_sampel); ?></td>
                </tr>
                <tr>
                    <td class="lbl">TANGGAL AFTAP</td>
                    <td class="colon">:</td>
                    <td><?php echo h($tglAftap); ?></td>
                </tr>
                <tr>
                    <td class="lbl">TANGGAL PENGOLAHAN</td>
                    <td class="colon">:</td>
                    <td><?php echo h($tglPengolahan); ?></td>
                </tr>
            </table>

            <div class="barcode-wrap">
                <svg class="barcode-svg" data-value="<?php echo h($nokantong); ?>"></svg>
                <div class="no-kantong"><?php echo h($nokantong); ?></div>
            </div>

            <?php if ($jenisSampel === 'REAKTIF') { ?>
            <div class="result-title"><?php echo h($hasilLabel); ?></div>
            <?php } else { ?>
            <div class="result-title-nr"><?php echo h($hasilLabel); ?></div>
            <?php } ?>

            <?php if ($jenisSampel === 'REAKTIF') { ?>
            <div class="checks">
                <div class="check-item">
                    <input type="checkbox" <?php echo $checked['HBsAg'] ? 'checked' : ''; ?> disabled> HBsAg
                </div>
                <div class="check-item">
                    <input type="checkbox" <?php echo $checked['HCV'] ? 'checked' : ''; ?> disabled> HCV
                </div>
                <div class="check-item">
                    <input type="checkbox" <?php echo $checked['HIV'] ? 'checked' : ''; ?> disabled> HIV
                </div>
                <div class="check-item">
                    <input type="checkbox" <?php echo $checked['Sifilis'] ? 'checked' : ''; ?> disabled> Sifilis
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script>
    document.querySelectorAll('.barcode-svg').forEach(function(el) {
        JsBarcode(el, el.getAttribute('data-value'), {
            format: 'CODE128',
            displayValue: false,
            height: 30,
            margin: 0
        });
    });
    </script>
</body>

</html>