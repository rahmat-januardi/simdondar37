<?php
include "config/dbi_connect.php";
$iPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : "irawanDB";

$selData = "SELECT dst_notrans FROM serahterima_detail_tmp WHERE dst_user = '" . $iPetugas . "'";
$resultData = $dbi->query($selData);
$rowData = $resultData->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Pengiriman Darah Komponen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- Link CSS Bootstrap -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->

    <!-- Link Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- JS untuk Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link rel="stylesheet" href="modul/st_komponen_rilis/css/mastheo-posting.css">
    <script src="modul/st_komponen_rilis/assets/js/sweetAlert2.min.js"></script>
    <script src="modul/st_komponen_rilis/assets/js/mastheo-posting.js"></script>
    <link rel="stylesheet" href="modul/st_komponen_rilis/css/st_komponen_rilis.css">

    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #0aacb5 !important;
            color: white !important;
            border: none;
            border-radius: 4px;
            margin: 0 2px;
            padding: 6px 12px;
            font-size: 14px;
            text-decoration: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #2980b9 !important;
            color: white !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #2980b9 !important;
            color: white !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background-color: #0aacb5 !important;
            color: white !important;
            border: 1px solid #3498db !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background-color: white !important;
            color: #3498db !important;
            cursor: not-allowed;
        }

        #tabelRekap thead {
            border: 1px solid #ccc !important;
            background-color: #0aacb5;
            /* background-color: #f2a5a5; */
            color: white !important;
        }

        #tabelRekap th {
            font-weight: bold;
            text-align: center;
        }

        #tabelRekap th {
            text-align: center;
            vertical-align: middle;
            color: white !important;
            font-size: 14px;
        }

        #tabelRekap td {
            text-align: center;
            vertical-align: middle;
            color: #333;
            font-size: 14px;
        }

        .spinner-loading {
            display: inline-block;
            width: 3rem;
            height: 3rem;
            border: 0.4rem solid #f3f3f3;
            border-top: 0.4rem solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .spinner-btn {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #fff;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 5px;
        }

        .icon-link {
            margin-right: 10px;
        }

        .link-text {
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="container col-sm-12">
        <h2 class="text-center" style="color:rgb(59, 59, 59);">REKAP PENGIRIMAN KOMPONEN DARAH - RILIS
        </h2>
        <!-- Form Pencarian -->
        <form id="formPencarian" onsubmit="return false;">
            <div class="row">
                <div class="col-md-2">
                    <label for="tanggalMulai">Tanggal Mulai</label>
                    <input type="date" class="bayangan form-control" style="background-color: white;" id="tanggalMulai"
                        required>
                </div>
                <div class="col-md-2">
                    <label for="tanggalSampai">Tanggal Sampai</label>
                    <input type="date" class="bayangan form-control" style="background-color: white;" id="tanggalSampai"
                        required>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <!-- <button type="submit" class="btn btn-primary" onclick="cariRekap()">Cari Data</button> -->
                    <button id="btnCariData" type="submit" class="btn" style="background-color: #006A71; color: white;"
                        onclick="cariRekap()">Cari
                        Data</button>
                </div>
            </div>
        </form>
        <hr>

        <!-- BOX: REKAP SERAH TERIMA -->
        <div class="box bayangan" id="boxRekapSerahTerima">
            <div class="box-header">Rekap Serah Terima Komponen Darah</div>

            <!-- Tabel Hasil Rekap -->
            <div id="hasilRekap">
                <em>Silakan pilih range tanggal untuk menampilkan data...</em>
            </div>
        </div>

        <!-- Modal QR Code -->
        <!-- <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="path_to_qr_image.png" alt="QR Code" class="img-fluid">
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Modal Detail -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">Detail Komponen Darah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Tampilkan detail yang relevan di sini -->
                        <p>Informasi Detail Komponen Darah akan muncul di sini.</p>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->

    <script>
        function cariRekap() {
            var tglMulai = $('#tanggalMulai').val();
            var tglSampai = $('#tanggalSampai').val();
            var unit = $('#unit').val();

            if (!tglMulai || !tglSampai) {
                alert("Tanggal mulai dan tanggal sampai wajib diisi.");
                return;
            }

            // Tampilkan spinner loading di hasilRekap
            $('#hasilRekap').html(`
                <div class="text-center" style="margin: 30px 0; text-align:center;">
                    <div class="spinner-loading"></div>
                    <div style="margin-top: 10px;">Memuat data, mohon tunggu...</div>
                </div>
            `);

            // Disable tombol dan ganti dengan spinner kecil
            var $btnCari = $('#btnCariData');
            $btnCari.prop('disabled', true);
            $btnCari.html(`<span class="spinner-btn"></span> Memuat...`);

            $.ajax({
                url: 'modul/st_komponen_rilis/ajax/rekapSTKomponen.php',
                method: 'POST',
                data: {
                    tanggal_mulai: tglMulai,
                    tanggal_sampai: tglSampai,
                    unit: unit
                },
                success: function (response) {
                    if (response.error) {
                        $('#hasilRekap').html('<div class="alert alert-danger">' + response.error + '</div>');
                        return;
                    }
                    $('#hasilRekap').html(response);

                    $('#tabelRekap').DataTable({
                        pageLength: 10,
                        responsive: true,
                        language: {
                            "lengthMenu": "Tampilkan _MENU_ entri",
                            "zeroRecords": "Data tidak ditemukan",
                            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                            "infoFiltered": "(disaring dari _MAX_ total entri)",
                            "search": "Pencarian:",
                            "paginate": {
                                "first": "Pertama",
                                "last": "Terakhir",
                                "next": "Selanjutnya",
                                "previous": "Sebelumnya"
                            },
                            "loadingRecords": "Memuat...",
                            "processing": "Sedang diproses...",
                            "emptyTable": "Tidak ada data tersedia"
                        }
                    });
                },
                error: function (xhr, status, error) {
                    $('#hasilRekap').html('<div class="alert alert-danger">Gagal memuat data: ' + error + '</div>');
                },
                complete: function () {
                    // Enable kembali tombol
                    $btnCari.prop('disabled', false);
                    $btnCari.html('Cari Data');
                }
            });
        }

        $(document).ready(function () {
            var today = new Date().toISOString().split('T')[0];
            $('#tanggalMulai').val(today);
            $('#tanggalSampai').val(today);

            cariRekap();
        });

        flatpickr("#tanggalMulai", {
            defaultDate: "today",
            onChange: function (selectedDates, dateStr, instance) {
                instance.close(); // Menutup manual setelah pilih tanggal
            }
        });

        flatpickr("#tanggalSampai", {
            defaultDate: "today",
            onChange: function (selectedDates, dateStr, instance) {
                instance.close(); // Menutup manual setelah pilih tanggal
            }
        });
    </script>

</body>

</html>
