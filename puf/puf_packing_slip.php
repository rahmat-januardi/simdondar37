<?php
/*
TIM IT 2024-11-15
Fitur baru: Input dan Generate Packing Slip
*/

require_once("clogin.php");
require_once("config/dbi_connect.php");

$leveluser = $_SESSION["level"];
$namauser = $_SESSION["namauser"];
$namalengkap = $_SESSION["nama_lengkap"];

$f_tanggal1 = date("Y-m-d");
$f_tanggal2 = date("Y-m-d");
$f_shipment_date = date("d F Y"); // Default shipment date dalam format full seperti contoh
$f_bol = ""; // Default kosong

// === PROSES TAMPILKAN ===
if (isset($_POST["btnTampilkan"])) {
    $f_tanggal1 = $_POST["tanggal_kirim1"];
    $f_tanggal2 = $_POST["tanggal_kirim2"];
    $f_shipment_date = $_POST["shipment_date"]; // Input manual jika diubah
    $f_bol = $_POST["bol_no"]; // Input manual jika diperlukan

    if ($f_tanggal2 < $f_tanggal1) {
        $temp = $f_tanggal1;
        $f_tanggal1 = $f_tanggal2;
        $f_tanggal2 = $temp;
    }

    // === AMBIL BOL OTOMATIS DARI TABEL pmf_distribusi ===
    $sql_bol = "SELECT pmfd_no_bol 
                FROM pmf_distribusi 
                WHERE DATE(pmfd_tanggal) BETWEEN '$f_tanggal1' AND '$f_tanggal2'
                  AND pmfd_no_bol IS NOT NULL 
                  AND TRIM(pmfd_no_bol) != ''
                ORDER BY pmfd_tanggal DESC, pmfd_notransaksi DESC 
                LIMIT 1";

    $qbol = mysqli_query($dbi, $sql_bol);
    if ($row_bol = mysqli_fetch_assoc($qbol)) {
        $f_bol = $row_bol["pmfd_no_bol"];
    } else {
        $f_bol = ""; // atau bisa generate otomatis seperti SKP|YY|MM|UDD|001
    }
}

// === AMBIL KODE UDD ===
$utd = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `id`, `nama`, `alamat` FROM `utd` WHERE `aktif`='1';"));
$udd_kode = $utd["id"];
$udd_nama = $utd["nama"];
$udd_alamat = $utd["alamat"];

// === PROSES SIMPAN PACKING SLIP (OPTIONAL: Simpan ke DB jika diperlukan, misal table baru pmf_packing_slip) ===
if (isset($_POST["btnSimpanPackingSlip"])) {
    // Di sini bisa simpan data packing slip ke table baru jika dibutuhkan
    // Misal: INSERT INTO pmf_packing_slip (bol_no, shipment_date, tgl1, tgl2, total_liters, ...) VALUES (...)
    // Untuk sekarang, kita fokus generate/export, jadi skip simpan DB kecuali diperlukan
    echo "<script>
        Swal.fire('Berhasil', 'Packing Slip disimpan.', 'success');
    </script>";
}

// === LOGIKA DINAMIS SHIPPED TO BERDASARKAN PREFIX BOL ===
$prefix = strtoupper(substr(trim($f_bol), 0, 3));
$shipped_to_name = '';
$shipped_to_address = '';

if ($prefix === 'SKP') {
    $shipped_to_name = 'SK Plasma Co., Ltd';
    $shipped_to_address = '310, Pangyo-ro, Bundang-gu, Seongnam-si, Gyeonggi-do, 13494, Republic of Korea';
} elseif ($prefix === 'TKD') {
    $shipped_to_name = 'TAKEDA manufacturing Austria AG';
    $shipped_to_address = 'Vienna, Austria Pasettistrasse, 76, 1200 Vienna, Austria';
} else {
    // Default jika prefix tidak cocok
    $shipped_to_name = 'TAKEDA manufacturing Austria AG';
    $shipped_to_address = 'Vienna, Austria Pasettistrasse, 76, 1200 Vienna, Austria';
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Packing Slip Pengiriman</title>
    <link rel="stylesheet" href="bootsrap337/bspmi.css">
    <link rel="stylesheet" href="bootsrap337/w3.css">
    <link rel="stylesheet" href="puf/puf.css">
    <link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
    <link href="bootsrap337/datepicker/css/bootstrap-datepicker.css" rel="stylesheet">
    <link rel="stylesheet" href="bootsrap337/chosen/chosen.css">
    <link href="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <style>
        .table th {
            text-align: center;
            vertical-align: middle;
        }

        .total-row {
            background-color: #d9edf7;
            font-weight: bold;
        }

        .form-inline .form-group {
            margin-right: 10px;
        }

        .btn-export {
            background-color: #337ab7;
            color: white;
        }

        .btn-export:hover {
            background-color: #286090;
        }

        .preview-bol {
            font-family: monospace;
            font-weight: bold;
            color: #f15006ff;
        }

        .modal-content {
            background-color: rgba(255, 255, 255, 1) !important;
            color: #000;
            color: #000;
            border-radius: 10px;
        }

        .modal-header {
            background-color: #a2aeccff !important;
            color: #000;
            border-bottom: 1px solid #bbb;
        }
    </style>
</head>

<body style="font-size:13px;">
    <div id="loading"></div>
    <div class="container-fluid" style="margin: 30px;">
        <div class="row">
            <div class="col-md-12">
                <div class="panel w3-border-theme shadow">
                    <div class="panel-heading w3-theme-d5 clearfix">
                        <div class="col-lg-9 col-md-8 col-sm-7 col-xs-8 text-left text-shadow" style="font-size: 150%;font-weight: bold;">
                            Packing Slip Pengiriman
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-4 text-right">
                            <a href="?module=fp_transport" class="w3-btn w3-theme-l3 w3-hover-yellow">
                                <i class="glyphicon glyphicon-repeat"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">

                        <!-- FORM FILTER & INPUT -->
                        <form method="POST" class="form-inline" style="margin-bottom: 15px;">
                            <div class="form-group">
                                <label>Tanggal Kirim</label>
                                <input type="text" class="form-control datepicker" name="tanggal_kirim1" value="<?= $f_tanggal1 ?>" style="width:120px;">
                                <span style="margin:0 5px;">s/d</span>
                                <input type="text" class="form-control datepicker" name="tanggal_kirim2" value="<?= $f_tanggal2 ?>" style="width:120px;">
                            </div>
                            <div class="form-group">
                                <label>Shipment Date</label>
                                <input type="text" class="form-control" name="shipment_date" value="<?= $f_shipment_date ?>" placeholder="dd Month YYYY" style="width:150px;">
                            </div>
                            <div class="form-group">
                                <label>BOL No.</label>
                                <input type="text" class="form-control preview-bol" name="bol_no" value="<?= $f_bol ?>" placeholder="SKP|YY|MM|UDD|TAHAP" style="width:150px;">
                            </div>
                            <button type="submit" name="btnTampilkan" class="w3-btn w3-theme-d5 w3-hover-yellow">
                                <i class="glyphicon glyphicon-search"></i> Tampilkan
                            </button>
                            <button type="button" class="w3-btn btn-export w3-hover-yellow" onclick="exportToExcel()">
                                <i class="glyphicon glyphicon-download"></i> Export XLS
                            </button>
                            <br><br>
                            <!-- <button type="submit" name="btnSimpanPackingSlip" class="w3-btn w3-theme-d3 w3-hover-green">
                                <i class="glyphicon glyphicon-save"></i> Simpan Packing Slip
                            </button> -->
                        </form>

                        <!-- INFO STATIS DARI CONTOH -->
                        <div class="well">
                            <strong>Shipped To:</strong> <?= $shipped_to_name ?><br>
                            <?= $shipped_to_address ?><br><br>
                            <strong>Shipped From:</strong> <?= $udd_nama ?><br>
                            <?= $udd_alamat ?><br><br>
                            <strong>Blood Establishment:</strong> <?= $udd_nama ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-condensed" id="tabledata">
                                <thead class="w3-theme-d4">
                                    <tr>
                                        <th style="text-align: center;">Carton ID</th>
                                        <th style="text-align: center;">Total Components (Unit)</th>
                                        <th style="text-align: center;">Carton Volume (L)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // === QUERY DATA ===
                                    $sql = "SELECT 
                                                d.pmfd_kodebox AS carton_id,
                                                COUNT(s.ktg_kantong) AS total_units,
                                                SUM(s.ktg_volume) AS total_volume,
                                                MIN(s.ktg_tgl_aftap) AS earliest_collection,
                                                MAX(s.ktg_tgl_aftap) AS latest_collection
                                            FROM pmf_distribusi d
                                            LEFT JOIN pmf_stokkantong s ON d.pmfd_notransaksi = s.puf_trxdistribusi
                                            WHERE DATE(d.pmfd_tanggal) BETWEEN '$f_tanggal1' AND '$f_tanggal2'
                                              AND d.pmfd_no_bol = '$f_bol'
                                              AND s.puf_trxdistribusi IS NOT NULL
                                              AND s.puf_status = '1'
                                            GROUP BY d.pmfd_kodebox
                                            ORDER BY d.pmfd_kodebox ASC";

                                    $query = mysqli_query($dbi, $sql);

                                    $total_cartons = 0;
                                    $total_units = 0;
                                    $total_volume = 0;
                                    $earliest_collection = null;
                                    $latest_collection = null;

                                    while ($row = mysqli_fetch_assoc($query)) {
                                        $total_cartons++;
                                        $total_units += $row['total_units'];
                                        $total_volume += $row['total_volume'];

                                        if (is_null($earliest_collection) || $row['earliest_collection'] < $earliest_collection) {
                                            $earliest_collection = $row['earliest_collection'];
                                        }
                                        if (is_null($latest_collection) || $row['latest_collection'] > $latest_collection) {
                                            $latest_collection = $row['latest_collection'];
                                        }

                                        echo "<tr>
                                                <td style='text-align: center;'>{$row['carton_id']}</td>
                                                <td style='text-align: center;'>{$row['total_units']}</td>
                                                <td style='text-align: center;'>" . number_format($row['total_volume'], 0) . " L</td>
                                              </tr>";
                                    }

                                    // Format tanggal collection
                                    $earliest_formatted = $earliest_collection ? date('d F Y', strtotime($earliest_collection)) : '-';
                                    $latest_formatted = $latest_collection ? date('d F Y', strtotime($latest_collection)) : '-';
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td colspan="2" style="text-align: center;"><strong>Total Volume:</strong></td>
                                        <!-- <td style="text-align: center;"><strong><?= $total_cartons ?></strong></td> -->
                                        <td style="text-align: center;"><strong><?= number_format($total_volume, 0) ?> L</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- INFO TAMBAHAN DARI DB -->
                        <div class="well">
                            <strong>Earliest Collection Date:</strong> <?= $earliest_formatted ?><br>
                            <strong>Latest Collection Date:</strong> <?= $latest_formatted ?><br>
                            <strong>Total Liters in Carton:</strong> <?= number_format($total_volume, 0) ?> liters
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // Kirim data total dari PHP ke JS
        window.exportData = {
            shipmentDate: '<?= $f_shipment_date ?>',
            bol: '<?= $f_bol ?>',
            bloodCenter: '<?= $udd_nama ?>',
            address: '<?= $udd_alamat ?>',
            earliest: '<?= $earliest_formatted ?>',
            latest: '<?= $latest_formatted ?>',
            totalLiters: <?= $total_volume ?>,
            totalCartons: <?= $total_cartons ?>,
            totalUnits: <?= $total_units ?>,
            shippedToName: '<?= $shipped_to_name ?>',
            shippedToAddress: '<?= $shipped_to_address ?>'
        };
    </script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            $('#tabledata').DataTable({
                paging: true,
                searching: false,
                info: false,
                lengthChange: false,
                order: [
                    [0, 'asc']
                ]
            });
        });

        function exportToExcel() {
            const dtTable = $('#tabledata').DataTable();

            // Ambil semua data (bukan hanya yang terlihat!)
            const bodyData = dtTable.rows({
                search: 'applied',
                order: 'applied',
                page: 'all'
            }).data().toArray();

            // Ambil header tabel
            const headerRow = dtTable.columns().header().toArray().map(h => h.innerText.trim());

            // Ambil data dari PHP yang sudah dikirim via window.exportData
            const d = window.exportData;

            // Format tanggal shipment sesuai input user (dd Month YYYY)
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const shipmentDateObj = new Date(d.shipmentDate.split(' ').reverse().join('-')); // konversi "20 Oktober 2025" ? Date
            const shipmentFormatted = d.shipmentDate; // sudah sesuai format

            // Header sesuai contoh Packing Slip resmi
            const headerData = [
                ['Shipment Donor List/Packing Slip'],
                ['Shipment Date:', shipmentFormatted],
                [],
                ['Shipped To:'],
                [d.shippedToName],
                [d.shippedToAddress],
                [],
                ['Shipped From:'],
                [d.bloodCenter],
                [d.address],
                [],
                ['Bill of Lading:', d.bol || '-'],
                [],
                ['Blood Establishment:', d.bloodCenter],
                [],
                ['Earliest Collection Date:', d.earliest],
                ['Latest Collection Date:', d.latest],
                ['Total Liters in Carton:', parseFloat(d.totalLiters).toFixed(0) + ' liters'],
                [],
                ['Packing Summary'],
                [] // baris kosong sebelum tabel
            ];

            // Total row di bawah tabel
            const totalRow = [
                'TOTAL',
                d.totalUnits + ' Units (' + d.totalCartons + ' Cartons)',
                parseFloat(d.totalLiters).toFixed(0) + ' L'
            ];

            // Gabungkan: header + tabel header + data + total
            const wsData = [
                ...headerData,
                headerRow,
                ...bodyData,
                [], // baris kosong
                totalRow
            ];

            // Buat workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(wsData);

            // Lebar kolom rapi
            ws['!cols'] = [{
                    wch: 25
                }, // Carton ID
                {
                    wch: 28
                }, // Total Components
                {
                    wch: 20
                } // Carton Volume
            ];

            // Styling: bold untuk bagian penting
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let R = 0; R <= range.e.r; ++R) {
                for (let C = 0; C <= range.e.c; ++C) {
                    const cell = ws[XLSX.utils.encode_cell({
                        r: R,
                        c: C
                    })];
                    if (!cell) continue;

                    // Baris judul besar & total row
                    if (R === 0 || R === range.e.r || R === 20 || R === 21) {
                        cell.s = {
                            font: {
                                bold: true,
                                sz: 14
                            }
                        };
                    }
                    // Label-label (Shipment Date:, Shipped To:, dll)
                    else if (C === 0 && wsData[R] && wsData[R][0] && wsData[R][0].toString().includes(':')) {
                        cell.s = {
                            font: {
                                bold: true
                            }
                        };
                    }
                    // Header tabel & total row
                    else if (R === 21 || R === wsData.length - 1) {
                        cell.s = {
                            font: {
                                bold: true
                            }
                        };
                    }
                }
            }

            XLSX.utils.book_append_sheet(wb, ws, 'Packing Slip');

            // Nama file
            const date1 = document.querySelector('input[name="tanggal_kirim1"]').value;
            const date2 = document.querySelector('input[name="tanggal_kirim2"]').value;
            XLSX.writeFile(wb, `Packing_Slip_${date1}_sd_${date2}.xlsx`);
        }

        const load = document.getElementById("loading");
        window.addEventListener('load', () => load.style.display = "none");
    </script>
</body>

</html>