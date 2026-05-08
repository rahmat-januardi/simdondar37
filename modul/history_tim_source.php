<?php
$filename = "modul/history_update.csv";
$pemisah  = "|";
$v_cari   = "";

// ====================== PROSES TAMBAH DATA ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $tanggal_input = trim($_POST['tanggal']);           // ini masih YYYY-MM-DD dari form
    $catatan       = trim($_POST['catatan']);

    if (!empty($tanggal_input) && !empty($catatan)) {
        
        // Ubah dari YYYY-MM-DD menjadi DD-MM-YYYY
        $tanggal = date('d-m-Y', strtotime($tanggal_input));

        $new_line = $tanggal . $pemisah . $catatan . PHP_EOL;

        // Tambahkan di paling atas file
        if (file_exists($filename)) {
            $existing = file_get_contents($filename);
            file_put_contents($filename, $new_line . $existing);
        } else {
            file_put_contents($filename, $new_line);
        }

        // Refresh halaman
        header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
        exit;
    }
}
// ====================== PROSES PENCARIAN ======================
if (isset($_POST['cari'])) {
    $v_cari = trim($_POST['cari']);
}

// Baca file untuk menghitung jumlah dan ambil tanggal pertama & terakhir
$jmlupdate = 0;
$mulai = "";
$sampai = "";

if (file_exists($filename)) {
    $datacsv = fopen($filename, "r");
    while (($data = fgetcsv($datacsv, 10000, $pemisah)) !== FALSE) {
        $jmlupdate++;
        if ($jmlupdate == 1) {
            $sampai = $data[0];
        }
        $mulai = $data[0];
    }
    fclose($datacsv);
}

// Buka lagi untuk ditampilkan
$datacsv = file_exists($filename) ? fopen($filename, "r") : false;
$nomor = $jmlupdate + 1;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Catatan Perbaikan dan Pengembangan SIMDONDAR</title>
    <link href="bootsrap337/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootsrap337/bspmi.css" rel="stylesheet">
    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <script src="bootsrap337/js/respond.min.js"></script>

    <style>
        table.table-bordered {
            border: 1px solid red;
        }

        table.table-bordered>thead>tr>th,
        table.table-bordered>tbody>tr>td {
            border: 1px solid red;
        }

        .modal-header {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>

<body onload="document.frmcari.cari.select();">

    <div class="container-fluid" style="margin: 30px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary bayangan">
                    <div class="panel-heading" style="background-color: #0d6efd; color: whitesmoke;">
                        <div class="panel-title clearfix">
                            <span style="font-size:120%;" class="text-shadow pull-left">
                                CATATAN PERBAIKAN DAN PENGEMBANGAN SIMDONDAR
                            </span>
                            <span class="pull-right">
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalTambah">
                                    <i class="glyphicon glyphicon-plus"></i> Tambah Catatan Baru
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">

                            <p style="margin:0;">
                                Diurutkan dari history terkini. Terdapat
                                <b><?= $jmlupdate ?></b> catatan
                                sejak <?= $mulai ?> s/d <?= $sampai ?>
                            </p>

                            <!-- Form Pencarian -->
                            <form name="frmcari" method="POST" action="" class="form-inline" style="margin:0;">
                                <div class="form-group">
                                    <input type="text" name="cari" class="form-control input-sm"
                                        placeholder="Cari catatan..." autofocus
                                        value="<?= htmlspecialchars($v_cari) ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                                </div>
                            </form>

                        </div>

                        <table class="table table-bordered table-striped table-hover">
                            <thead class="pmi" style="height: 30px;">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 90px;">Tanggal</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($datacsv): ?>
                                    <?php while (($data = fgetcsv($datacsv, 10000, $pemisah)) !== FALSE): ?>
                                        <?php
                                        $nomor--;
                                        if ($v_cari !== "" && stripos($data[1], $v_cari) === false) {
                                            continue;
                                        }
                                        ?>
                                        <tr>
                                            <td valign="top" align="right"><?= $nomor ?>. </td>
                                            <td class="text-center" nowrap><?= htmlspecialchars($data[0]) ?></td>
                                            <td style="padding-left:15px;"><?= nl2br(htmlspecialchars($data[1])) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada catatan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== MODAL TAMBAH CATATAN ====================== -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Tambah Catatan Baru</h4>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Catatan / Keterangan Perbaikan</label>
                            <textarea name="catatan" class="form-control" rows="5"
                                placeholder="Masukkan catatan perbaikan atau pengembangan..."
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan Catatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>

<?php
if ($datacsv) fclose($datacsv);
?>