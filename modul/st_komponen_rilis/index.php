<?php
include "config/dbi_connect.php";
$verStatus = filemtime('modul/st_komponen_rilis/assets/js/status-kantong.js');
$iPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : "-";
//error_log('User dari index saat ini adalah: ' . $iPetugas);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Pengiriman Darah Komponen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

    <script src="modul/st_komponen_rilis/assets/js/status-kantong.js?v=<?= $verStatus ?>"></script>
    <link rel="stylesheet" href="modul/st_komponen_rilis/css/mastheo-posting.css">
    <script src="modul/st_komponen_rilis/assets/js/sweetAlert2.min.js"></script>
    <script src="modul/st_komponen_rilis/assets/js/mastheo-posting.js"></script>
    <link rel="stylesheet" href="modul/st_komponen_rilis/css/st_komponen_rilis.css">
    <link rel="stylesheet" href="modul/st_komponen_rilis/css/notif-blink-blink.css">
</head>

<body>
    <div class="container col-sm-12">
        <h4 class="text-center" style="color:rgb(59, 59, 59);">FORM PENGIRIMAN KOMPONEN DARAH - RILIS
        </h4>

        <div class="box bayangan">
            <div class="box-header">Informasi Pengiriman</div>
            <form id="formPengiriman">
                <div class="row">
                    <div class="form-group col-sm-2">
                        <label>Bagian yang Mengirimkan</label>
                        <input type="text" class="form-control" name="bagian_kirim" value="Produksi" readonly>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Jenis Serah Terima</label>
                        <input type="text" class="form-control" value="Kantong (Komponen Darah)" readonly>
                    </div>
                    <!-- <div class="form-group col-sm-2">
                        <label>Bagian yang Menerima</label>
                        <input type="text" class="form-control" value="RILIS" readonly>
                    </div> -->
                    <div class="form-group col-sm-2">
                        <label>Peruntukan</label>
                        <input type="text" class="form-control" value="POSTING HASIL PENGOLAHAN DARAH" readonly>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Suhu Saat Pengiriman</label>
                        <input type="text" class="form-control" name="suhu_pengiriman" accept="text"
                            placeholder="Celsius" required>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Kondisi Saat Pengiriman</label>
                        <input type="text" class="form-control" name="keadaan" required>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Kode Alat Pengiriman</label>
                        <input type="text" class="form-control" name="kode_alat" required>
                    </div>
                </div>
            </form>
        </div>

        <div class="box">
            <div class="box-header">Data Komponen Darah</div>
            <form id="formKantong" onsubmit="return false;">
                <div class="bayangan input-group">
                    <input type="text" class="form-control" id="nomor_kantong" placeholder="Scan / Ketik No. Kantong"
                        autocomplete="off">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" onclick="insertKantong()">Tambah</button>
                    </span>
                </div>
            </form>
            <br>
            <div id="kantongTableContainer">
                <div id="tableContainer">Memuat data.....</div>
            </div>
            <br>
            <button class="btn btn-danger" onclick="hapusTerpilih()">Hapus Data Terpilih</button>
        </div>

        <!-- BOX: RINGKASAN DATA -->
        <!-- <div class="box" id="boxRingkasan">
            <div class="box-header">Ringkasan Data Kantong Darah</div>
            <div id="ringkasanContent">Memuat data ringkasan...</div>
        </div> -->

        <!-- BOX: RINGKASAN DATA -->
        <div class="box" id="boxRingkasan">
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
                    <div class="bayangan-kecil panel panel-default">
                        <div class="panel-heading">Jumlah Per Produk</div>
                        <div class="panel-body" id="perProduk">
                            <em>Memuat data produk...</em>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="box">
            <div class="box-header">Finalisasi & Simpan</div>
            <form id="formFinal">
                <div class="row">
                    <div class="col-md-4">
                        <label>Catatan Tambahan</label>
                        <input type="text" class="form-control" name="catatan">
                    </div>
                </div>
                <br>
                <button class="btn btn-info">SIMPAN</button>
            </form>
        </div> -->
    </div>
    <div class="box">
        <form id="formFinal">
            <?php
            $selData = "SELECT dst_notrans FROM serahterima_detail_tmp WHERE dst_user = '" . $iPetugas . "'";
            $resultData = $dbi->query($selData);
            $rowData = $resultData->fetch_assoc();
            $rowData['dst_notrans'] = isset($rowData['dst_notrans']) ? $rowData['dst_notrans'] : null;
            ?>
            <input type="hidden" name="dst_us" value="<?= $iPetugas ?>">
            <input type="hidden" name="dst_nt" value="<?= $rowData['dst_notrans'] ?>">
            <button id="simpanLanjut" class="btn btn-success"
                style="background-color:rgb(17, 170, 0); color: white;">SIMPAN dan LANJUTKAN</button>
        </form>
    </div>

    <!-- Modal Detail Kantong / Pendonor -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"
                    style="background-color: #912200; color: white; box-shadow: 0 7px 10px rgba(0, 0, 0, 0.2);">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Detail Data</h4>
                </div>
                <div class="modal-body" id="detailContent">Loading...</div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <script src="modul/st_komponen_rilis/assets/js/irawanDB.js"></script>
</body>

</html>
