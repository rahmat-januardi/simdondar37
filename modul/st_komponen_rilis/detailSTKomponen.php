<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "config/dbi_connect.php";
$verStatus = filemtime('assets/js/status-kantong.js');
$nT = isset($_GET['nT']) ? $_GET['nT'] : "-";
$iPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : "irawanDB";

$selData = "SELECT hst_notrans, hst_bagpengirim, hst_jenis_st, hst_peruntukan, hst_kondisiumum, hst_kode_alat FROM serahterima WHERE hst_notrans = '" . $nT . "'";
$resultData = $dbi->query($selData);
$rowData = $resultData->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Penerimaan Darah Komponen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(t => new bootstrap.Tooltip(t));
        });
    </script>

    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

    <script src="assets/js/status-kantong.js?v=<?php echo $verStatus; ?>"></script>
    <link rel="stylesheet" href="css/mastheo-posting.css">
    <script src="assets/js/sweetAlert2.min.js"></script>
    <script src="assets/js/mastheo-posting.js"></script>
    <link rel="stylesheet" href="css/st_komponen_rilis.css">
    <link rel="stylesheet" href="css/notif-blink-blink.css">
</head>

<body>
    <div class="container col-sm-12">
        <h2 class="text-center" style="color:rgb(59, 59, 59);">FORM PENERIMAAN KOMPONEN DARAH - RILIS
        </h2>

        <div class="box bayangan">
            <form id="formPengiriman">
                <div class="box-header">Informasi Pengiriman</div>
                <div class="row g-4">
                    <div class="col-12 col-md-2">
                        <div class="bayangan mb-3">
                            <label class="form-label">No. Transaksi</label>
                            <div class="form-control-plaintext fw-semibold text-dark">
                                <?php echo $nT; ?>
                                <input type="hidden" id="nttr" value="<?php echo htmlspecialchars($nT); ?>">
                            </div>
                        </div>
                        <!-- <hr style="margin: 10px 0;"> -->
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="bayangan mb-3">
                            <label class="form-label">Bagian yang Mengirimkan</label>
                            <div class="form-control-plaintext">
                                <?php echo $rowData['hst_bagpengirim']; ?>
                                <input type="hidden" name="bagKirim" value="<?php echo $rowData['hst_bagpengirim']; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-2">
                        <div class="bayangan mb-3">
                            <label class="form-label">Kondisi Saat Pengiriman</label>
                            <div class="form-control-plaintext">
                                <?php echo $rowData['hst_kondisiumum']; ?>
                                <input type="hidden" name="keadaan" value="<?php echo $rowData['hst_kondisiumum']; ?>">
                            </div>
                        </div>
                        <!-- <hr style="margin: 10px 0;"> -->
                    </div>
                    <div class="col-12 col-md-2">
                        <div class="bayangan mb-2">
                            <label class="form-label">Kode Alat Pengiriman</label>
                            <div class="form-control-plaintext">
                                <?php echo $rowData['hst_kode_alat']; ?>
                                <input type="hidden" name="kode_alat" value="<?php echo $rowData['hst_kode_alat']; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 3 -->
                    <div class="col-12 col-md-3">
                        <div class="bayangan mb-3">
                            <label class="form-label">Suhu Saat Diterima</label>
                            <input type="text" class="form-control" name="suhu_pengiriman" placeholder="°C" required>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="box">
            <div class="box-header">Data Komponen Darah</div>
            <form id="formKantong" onsubmit="return false;">
                <div class="bayangan input-group">
                    <input type="text" class="form-control" id="nomor_kantong"
                        placeholder="Scan / Ketik No. Kantong ATAU No. Transaksi" autocomplete="off">
                    <span class="input-group-btn">
                        <button class="btn" style="background-color: #006A71; color: white;"
                            onclick="insertKantong()">Lanjutkan</button>
                    </span>
                </div>
            </form>
            <br>
            <div id="kantongTableContainer" style="border: 1px solid #ccc; border-radius: 5px;">
                <div id="tableContainer">Memuat data.....</div>
            </div>
            <br>
            <button id="btnTerimaTerpilih" class="btn btn-success" onclick="terimaTerpilih()">Proses Data
                Terpilih</button>
        </div>

        <div class="box bayangan" id="boxRingkasan">
            <div class="box-header">Ringkasan Data Kantong Darah</div>
            <div class="row text-center">
                <div class="col-md-2">
                    <div class="bayangan-kecil panel panel-info" style="border: 1px solid info;">
                        <div class="panel-heading" style="background-color:rgb(17, 128, 183); color: white;">Total Data
                        </div>
                        <div class="panel-body"><strong id="jmlTotal">0</strong> kantong</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="bayangan-kecil panel panel-success" style="border: 1px solid success;">
                        <div class="panel-heading" style="background-color:rgb(6, 168, 27); color: white;">Non-Reaktif
                        </div>
                        <div class="panel-body"><strong id="jmlNR">0</strong></div>
                    </div>
                </div>
                <!-- <div class="col-md-2">
                    <div class="panel panel-warning">
                        <div class="panel-heading" style="background-color:rgb(237, 194, 3); color: white;">GreyZone
                        </div>
                        <div class="panel-body"><strong id="jmlGZ">0</strong></div>
                    </div>
                </div> -->
                <div class="col-md-2">
                    <div class="bayangan-kecil panel panel-danger" style="border: 1px solid danger;">
                        <div class="panel-heading" style="background-color:rgb(183, 17, 17); color: white;">Reaktif
                        </div>
                        <div class="panel-body"><strong id="jmlR">0</strong></div>
                    </div>
                </div>
                <div class="col-md-4" id="rekapKantong"></div>
            </div>

            <!-- Panel: Per Produk -->
            <div class="row">
                <div class="col-md-12">
                    <div class="bayangan panel panel-default">
                        <div class="panel-heading">Jumlah Per Produk</div>
                        <div class="panel-body" id="perProduk">
                            <em>Memuat data produk...</em>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box">
        <form id="formFinal">
            <input type="hidden" name="dst_us" value="<?php echo $iPetugas; ?>">
            <input type="hidden" name="dst_nt" value="<?php echo htmlspecialchars($nT); ?>">
            <!-- <button id="simpanLanjut" class="btn btn-success" style="background-color: #48A6A7; color: white;">SIMPAN
                dan LANJUTKAN</button> -->
            <button type="button" id="simpanLanjut" class="btn btn-success"
                style="background-color: #48A6A7; color: white;">
                SIMPAN dan LANJUTKAN
            </button>

        </form>
    </div>

    <!-- Modal Detail Kantong / Pendonor -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"
                    style="background-color: #0aacb5; color: white; box-shadow: 0 7px 10px rgba(0, 0, 0, 0.2);">
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                    <!-- <h4 class="modal-title">Detail Data</h4> -->
                </div>
                <div class="modal-body" id="detailContent">Loading...</div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->

    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <script>
        const nT = "<?php echo $nT; ?>";
    </script>
    <script src="assets/js/irawanDB-rilis.js"></script>

</body>

</html>
