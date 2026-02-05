<?php
session_start();
require_once('config/dbi_connect.php');

$utd = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `id`, `nama`, `alamat` FROM `utd` WHERE `aktif`='1';"));

// Filter tanggal (escape input untuk hindari SQL injection)
$tgl_awal  = isset($_GET['awal']) ? mysqli_real_escape_string($dbi, $_GET['awal']) : date('Y-m-d');
$tgl_akhir = isset($_GET['akhir']) ? mysqli_real_escape_string($dbi, $_GET['akhir']) : date('Y-m-d');

if ($tgl_awal > $tgl_akhir) {
    $temp = $tgl_awal;
    $tgl_awal = $tgl_akhir;
    $tgl_akhir = $temp;
}

// Fungsi helper untuk jenis (ganti switch yang salah)
function getJenisDarah($jenis)
{
    switch ($jenis) {
        case '1':
            return 'Single';
        case '2':
            return 'Double';
        case '3':
            return 'Triple';
        case '4':
            return 'Quadruple';
        case '5':
            return 'Quintuple';
        case '6':
            return 'Pediatrik';
        default:
            return 'Lainnya';
    }
}

function getStatusText($Status)
{
    switch ($Status) {
        case '0':
            return "Kosong";
        case '1':
            return "Baru Isi / Karantina";
        case '2':
            return "Sehat";
        case '3':
            return "Keluar Bawa / Titip";
        case '4':
            return "Rusak";
        case '5':
            return "Rusak / Gagal Aftap";
        case '6':
            return "Dimusnahkan";
        case '7':
            return "Reaktif";
        case '8':
            return "Darah Flebotomi";
        default:
            return "Status Tidak Dikenal ($Status[Status])";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Rekap Verifikasi Kantong Darah</title>
    <link href="bootsrap337/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                font-size: 9pt;
            }

            table {
                font-size: 8pt;
            }

            @page {
                size: portrait;
                /* Utama: Ubah ke landscape agar tabel lebar muat */
                margin: 0.5cm;
            }
        }

        th {
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-3">

        <div class="row no-print mb-3">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-file-text"></i> Rekap Verifikasi Kantong Darah</h4>
                    </div>
                    <div class="panel-body">
                        <form class="form-inline" method="get" action="" id="formFilter">
                            <!-- Tambahkan hidden input untuk module dan level -->
                            <input type="hidden" name="module" value="<?= isset($_GET['module']) ? $_GET['module'] : 'home' ?>">
                            <input type="hidden" name="level" value="<?= isset($_GET['level']) ? $_GET['level'] : 'aftap' ?>">

                            <div class="form-group">
                                <label>Tanggal: </label>
                                <input type="date" class="form-control ml-2" name="awal" id="tgl_awal" value="<?= $tgl_awal ?>" required>
                                <span class="ml-2 mr-2">s/d</span>
                                <input type="date" class="form-control" name="akhir" id="tgl_akhir" value="<?= $tgl_akhir ?>" required>
                            </div>
                            <button type="submit" class="btn btn-success ml-3">
                                <i class="fa fa-search"></i> Tampilkan
                            </button>
                            <button type="button" class="btn btn-info ml-2" onclick="window.print()">
                                <i class="fa fa-print"></i> Cetak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="text-center">REKAPITULASI VERIFIKASI KANTONG DARAH</h3>
        <h4 class="text-center">
            <?= strtoupper($utd['nama']) ?><br>
            Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s/d <?= date('d-m-Y', strtotime($tgl_akhir)) ?>
        </h4>
        <hr>

        <?php
        $sql = "SELECT v.*, s.jenis, s.volumeasal, s.Status 
                FROM verifikasi_kantong v
                LEFT JOIN stokkantong s ON v.no_kantong = s.noKantong
                WHERE DATE(v.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                ORDER BY v.tanggal DESC";

        $query = mysqli_query($dbi, $sql);
        if (!$query) {
            echo "<div class='alert alert-danger'>Error query: " . mysqli_error($dbi) . "</div>";
        }
        $total = mysqli_num_rows($query);
        $lolos = 0;
        ?>

        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th width="3%" rowspan="2" style="text-align: center; vertical-align: middle;">No</th>
                    <th width="14%" rowspan="2" style="text-align: center; vertical-align: middle;">No. Kantong</th>
                    <th width="12%" rowspan="2" style="text-align: center; vertical-align: middle;">Tanggal & Jam</th>
                    <th width="8%" rowspan="2" style="text-align: center; vertical-align: middle;">Jenis</th>
                    <th width="6%" rowspan="2" style="text-align: center; vertical-align: middle;">Vol (ml)</th>
                    <th colspan="3" style="text-align: center; vertical-align: middle;">Kemasan</th>
                    <th colspan="2" style="text-align: center; vertical-align: middle;">Selang</th>
                    <th colspan="2" style="text-align: center; vertical-align: middle;">Jarum</th>
                    <th colspan="2" style="text-align: center; vertical-align: middle;">Antikoagulan</th>
                    <th width="9%" rowspan="2" style="text-align: center; vertical-align: middle;">Status Kantong</th>
                    <th width="9%" rowspan="2" style="text-align: center; vertical-align: middle;">Hasil Verifikasi</th>
                </tr>
                <tr>
                    <th style="text-align: center; vertical-align: middle;">Keadaan Utuh</th>
                    <th style="text-align: center; vertical-align: middle;">Belum Expired</th>
                    <th style="text-align: center; vertical-align: middle;">Tidak Bocor</th>
                    <th style="text-align: center; vertical-align: middle;">Baik</th>
                    <th style="text-align: center; vertical-align: middle;">Tertekuk</th>
                    <th style="text-align: center; vertical-align: middle;">Baik</th>
                    <th style="text-align: center; vertical-align: middle;">Bengkok</th>
                    <th style="text-align: center; vertical-align: middle;">Jernih</th>
                    <th style="text-align: center; vertical-align: middle;">Berubah Warna</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($r = mysqli_fetch_array($query)) {
                    $hasil = $r['status_valid']
                        ? '<span class="label label-success"><i class="fa fa-check"></i> LOLOS</span>'
                        : '<span class="label label-danger"><i class="fa fa-times"></i> TIDAK LOLOS</span>';

                    if ($r['status_valid']) $lolos++;
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= $r['no_kantong'] ?></strong></td>
                        <td><?= date('d-m-Y H:i', strtotime($r['tanggal'])) ?></td>
                        <td><?= getJenisDarah($r['jenis']) ?></td>
                        <td><?= $r['volumeasal'] ?: '-' ?></td>

                        <!-- KEMASAN -->
                        <td align="center">
                            <?= $r['kemasan_utuh']     ? '<i class="fa fa-check text-success" title="Keadaan Utuh"></i>' : '<i class="fa fa-times text-danger" title="Tidak Utuh"></i>' ?><br>
                        </td>
                        <td align="center">
                            <?= $r['kemasan_expired']  ? '<i class="fa fa-check text-success" title="Belum Expired"></i>' : '<i class="fa fa-times text-danger" title="Expired"></i>' ?><br>
                        </td>
                        <td align="center">
                            <?= $r['kemasan_bocor']    ? '<i class="fa fa-check text-success" title="Tidak Bocor"></i>' : '<i class="fa fa-times text-danger" title="Bocor"></i>' ?>
                        </td>

                        <!-- SELANG -->
                        <td align="center">
                            <?= $r['selang_baik']      ? '<i class="fa fa-check text-success" title="Baik"></i>' : '<i class="fa fa-times text-danger" title="Tidak Baik"></i>' ?><br>
                        </td>
                        <td align="center">
                            <?= $r['selang_tertekuk'] ? '<i class="fa fa-check text-success" title="Tertekuk"></i>' : '<i class="fa fa-times text-danger" title="Tidak Tertekuk"></i>' ?>
                        </td>

                        <!-- JARUM -->
                        <td align="center">
                            <?= $r['jarum_baik']       ? '<i class="fa fa-check text-success" title="Baik"></i>' : '<i class="fa fa-times text-danger" title="Tidak Baik"></i>' ?><br>
                        </td>
                        <td align="center">
                            <?= $r['jarum_bengkok']   ? '<i class="fa fa-check text-success" title="Bengkok"></i>' : '<i class="fa fa-times text-danger" title="Tidak Bengkok"></i>' ?>
                        </td>

                        <!-- ANTIKOAGULAN -->
                        <td align="center">
                            <?= $r['anti_jernih']      ? '<i class="fa fa-check text-success" title="Jernih"></i>'      : '<i class="fa fa-times text-danger" title="Tidak Jernih"></i>' ?><br>
                        </td>
                        <td align="center">
                            <?= $r['anti_berubah']    ? '<i class="fa fa-check text-success" title="Berubah Warna"></i>' : '<i class="fa fa-times text-danger" title="Tidak Berubah Warna"></i>' ?>
                        </td>

                        <td align="center"><?= getStatusText($r['Status']) ?></td>
                        <td align="center"><?= $hasil ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="info">
                    <th colspan="15" class="text-right">TOTAL KANTONG DIVERIFIKASI</th>
                    <th style="text-align: center;"><?= $total ?></th>
                </tr>
                <tr class="success">
                    <th colspan="15" class="text-right">LOLOS VERIFIKASI (SIAP AFTAP)</th>
                    <th style="text-align: center;"><?= $lolos ?></th>
                </tr>
                <tr class="danger">
                    <th colspan="15" class="text-right">TIDAK LOLOS VERIFIKASI</th>
                    <th style="text-align: center;"><?= $total - $lolos ?></th>
                </tr>
                <tr class="warning">
                    <th colspan="15" class="text-right">PERSENTASE LOLOS</th>
                    <th style="text-align: center;"><?= $total > 0 ? round(($lolos / $total) * 100, 2) : 0 ?> %</th>
                </tr>
            </tfoot>
        </table>

    </div>

    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <script>
        // Debug form submission
        document.getElementById('formFilter').addEventListener('submit', function(e) {
            var tglAwal = document.getElementById('tgl_awal').value;
            var tglAkhir = document.getElementById('tgl_akhir').value;

            console.log('Form submitted!');
            console.log('Tanggal Awal:', tglAwal);
            console.log('Tanggal Akhir:', tglAkhir);

            if (!tglAwal || !tglAkhir) {
                alert('Silakan pilih tanggal terlebih dahulu!');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>

</html>