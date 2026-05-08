<?php
session_start();
require_once('config/db_connect.php');

$namaudd = isset($_SESSION['namaudd']) ? $_SESSION['namaudd'] : '';

$today    = date('Y-m-d');
$today1   = $today;
$src_produk   = '';
$src_status   = '';
$src_golongan = '';
$src_rhesus   = '';
$src_utd      = '';

if (isset($_POST['minta1']) && $_POST['minta1'] != '') {
    $today = $_POST['minta1'];
    $today1 = $today;
}

if (isset($_POST['minta2']) && $_POST['minta2'] != '') {
    $today1 = $_POST['minta2'];
}

if (isset($_POST['produk']) && $_POST['produk'] != '') {
    $src_produk = $_POST['produk'];
}

if (isset($_POST['status']) && $_POST['status'] != '') {
    $src_status = $_POST['status'];
}

if (isset($_POST['golongan']) && $_POST['golongan'] != '') {
    $src_golongan = $_POST['golongan'];
}

if (isset($_POST['rhesus']) && $_POST['rhesus'] != '') {
    $src_rhesus = $_POST['rhesus'];
}

if (isset($_POST['utd']) && $_POST['utd'] != '') {
    $src_utd = $_POST['utd'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">

    <link type="text/css" href="css/calender.css" rel="stylesheet" />
    <link type="text/css" href="css/table1.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <style>
    tr {
        background-color: #FDF5E6;
    }

    .initial {
        background-color: #FDF5E6;
        color: #000000;
    }

    .normal {
        background-color: #FDF5E6;
    }

    .highlight {
        background-color: #7FFF00;
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="js/tgl_rekap.js"></script>
</head>

<body>

    <a name="atas"></a>
    <h2>RINCIAN PENERIMAAN SAMPLE QC PRODUK KOMPONEN DARAH</h2>

    <form method="post">
        Mulai: TANGGAL :
        <input type="text" name="minta1" id="datepicker" size="10" value="<?php echo htmlspecialchars($today); ?>">
        S/D
        <input type="text" name="minta2" id="datepicker1" size="10" value="<?php echo htmlspecialchars($today1); ?>">
        <br>

        PRODUK
        <select name="produk">
            <option value="" <?php echo ($src_produk == '') ? 'selected' : ''; ?>>- SEMUA -</option>
            <option value="WB" <?php echo ($src_produk == 'WB') ? 'selected' : ''; ?>>WB</option>
            <option value="PRC" <?php echo ($src_produk == 'PRC') ? 'selected' : ''; ?>>PRC</option>
            <option value="TC" <?php echo ($src_produk == 'TC') ? 'selected' : ''; ?>>TC</option>
            <option value="FFP" <?php echo ($src_produk == 'FFP') ? 'selected' : ''; ?>>FFP</option>
            <option value="AHF" <?php echo ($src_produk == 'AHF') ? 'selected' : ''; ?>>AHF</option>
            <option value="TC Aferesis" <?php echo ($src_produk == 'TC Aferesis') ? 'selected' : ''; ?>>TC Aferesis
            </option>
        </select>

        GOL Darah
        <select name="golongan">
            <option value="" <?php echo ($src_golongan == '') ? 'selected' : ''; ?>>- SEMUA -</option>
            <option value="A" <?php echo ($src_golongan == 'A') ? 'selected' : ''; ?>>A</option>
            <option value="B" <?php echo ($src_golongan == 'B') ? 'selected' : ''; ?>>B</option>
            <option value="O" <?php echo ($src_golongan == 'O') ? 'selected' : ''; ?>>O</option>
            <option value="AB" <?php echo ($src_golongan == 'AB') ? 'selected' : ''; ?>>AB</option>
        </select>

        Rh Darah
        <select name="rhesus">
            <option value="" <?php echo ($src_rhesus == '') ? 'selected' : ''; ?>>- SEMUA -</option>
            <option value="+" <?php echo ($src_rhesus == '+') ? 'selected' : ''; ?>>Positif</option>
            <option value="-" <?php echo ($src_rhesus == '-') ? 'selected' : ''; ?>>Negatif</option>
        </select>

        Asal Sampel
        <select name="utd" class="select2" style="width: 300px;">
            <option value="" <?php echo ($src_utd == '') ? 'selected' : ''; ?>>-SEMUA-</option>
            <?php
            $ql = mysql_query("SELECT * FROM utd ORDER BY daerah ASC");
            while ($rowl1 = mysql_fetch_array($ql)) {
                $selected = ($src_utd == $rowl1['id']) ? 'selected' : '';
                echo "<option value='" . $rowl1['id'] . "' $selected>" . htmlspecialchars($rowl1['nama']) . "</option>";
            }
            ?>
        </select>

        <input type="submit" name="submit" value="Tampilkan data" class="swn_button_blue">
    </form>

    <form name="xls" method="post" action="QC/rekap_register_xls.php">
        <input type="hidden" name="today" value="<?php echo htmlspecialchars($today); ?>">
        <input type="hidden" name="today1" value="<?php echo htmlspecialchars($today1); ?>">
        <input type="hidden" name="bdrs" value="">
        <input type="hidden" name="produk" value="<?php echo htmlspecialchars($src_produk); ?>">
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($src_status); ?>">
        <input type="hidden" name="golongan" value="<?php echo htmlspecialchars($src_golongan); ?>">
        <input type="hidden" name="rhesus" value="<?php echo htmlspecialchars($src_rhesus); ?>">
        <input type="hidden" name="utd" value="<?php echo htmlspecialchars($src_utd); ?>">
        <input type="submit" name="submit2" class="swn_button_blue" value="Print Rekap Penerimaan Sample QC">
    </form>

    <?php
    $transaksipermintaan = mysql_query("
    SELECT nokantong, goldarah, rhesus, produk, tglaftap, kadaluwarsa, tgl, petugas_terima, petugas_serah, asal_utd
    FROM registrasi_qc
    WHERE CAST(tgl AS DATE) >= '$today'
      AND CAST(tgl AS DATE) <= '$today1'
      AND asal_utd LIKE '%$src_utd%'
    ORDER BY tgl ASC
");
    ?>

    <table border="2" cellpadding="5" cellspacing="1" style="border-collapse:collapse" width="100%">
        <tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;"
            onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td align="center">No</td>
            <td align="center">No. Kantong</td>
            <td align="center">Gol Darah</td>
            <td align="center">Rhesus</td>
            <td align="center">Produk</td>
            <td align="center">Tgl Aftap</td>
            <td align="center">Tgl Kadaluwarsa</td>
            <td align="center">Tgl Penerimaan Sampel</td>
            <td align="center">Petugas Yg Menyerahkan</td>
            <td align="center">Petugas Yg Menerima</td>
            <td align="center">Asal Sampel</td>
            <td align="center">Status</td>
        </tr>

        <?php
        $no = 1;
        while ($datatransaksipermintaan = mysql_fetch_array($transaksipermintaan)) {
            $asalutd = mysql_fetch_assoc(mysql_query("SELECT nama FROM utd WHERE id='" . $datatransaksipermintaan['asal_utd'] . "'"));
            $utd = isset($asalutd['nama']) ? $asalutd['nama'] : '';

            $statusQC = mysql_fetch_assoc(mysql_query("SELECT statQC FROM stokkantong WHERE noKantong='" . $datatransaksipermintaan['nokantong'] . "'"));
            if (isset($statusQC['statQC']) && $statusQC['statQC'] == '1') {
                $status = 'Sudah QC';
            } else {
                $status = 'Belum QC';
            }
        ?>
        <tr style="font-size:11px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
            onMouseOut="this.className='normal'">
            <td align="center"><?php echo $no; ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['nokantong']); ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['goldarah']); ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['rhesus']); ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['produk']); ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['tglaftap']); ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['kadaluwarsa']); ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['tgl']); ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['petugas_terima']); ?></td>
            <td align="center"><?php echo htmlspecialchars($datatransaksipermintaan['petugas_serah']); ?></td>
            <td align="center"><?php echo htmlspecialchars($utd); ?></td>
            <td align="center"
                style="background-color: <?php echo ($status == 'Sudah QC' ? 'lightgreen' : 'lightcoral'); ?>;">
                <?php echo $status; ?>
            </td>
        </tr>
        <?php
            $no++;
        }
        ?>
    </table>

    <script>
    $(function() {
        if ($.fn.datepicker) {
            $('#datepicker').datepicker({
                dateFormat: 'yy-mm-dd'
            });

            $('#datepicker1').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        }

        if ($.fn.select2) {
            $('.select2').select2({
                width: '300px',
                minimumResultsForSearch: 0
            });
        }
    });
    </script>

</body>

</html>
<?php
mysql_close();
?>