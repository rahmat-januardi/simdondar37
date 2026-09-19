<?php

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('clogin.php');
include('config/db_connect.php');

$level = $_SESSION['leveluser'];

$today = date("Y-m-d");
$show_table = false;
$tanggal_mulai = $today;
$tanggal_selesai = $today;
$asal_kantong = '';
$sql_utd = mysql_query("SELECT id, nama FROM utd ORDER BY nama ASC");
$sql_data = false;

function normalizeProduk($value)
{
	$produk = trim(strtoupper((string) $value));

	if (preg_match('/^(PRC|WB)\b/', $produk)) {
		$produk = preg_replace('/\s*\d+(\.\d+)?$/', '', $produk);
		$produk = trim($produk);
	}

	return $produk;
}

if (isset($_POST['submit_tampil'])) {
	$show_table = true;
	$tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : $today;
	$tanggal_selesai = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : $today;
	$asal_kantong = isset($_POST['asal_kantong']) ? $_POST['asal_kantong'] : '';
	$where = " WHERE rq.up_data = '1' ";

	// var_dump($tanggal_mulai);
	// var_dump($tanggal_selesai);
	// var_dump($asal_kantong);

	if ($tanggal_mulai != '' && $tanggal_selesai != '') {
		$where .= " AND DATE(qc.qctgl) BETWEEN '" . mysql_real_escape_string($tanggal_mulai) . "' AND '" . mysql_real_escape_string($tanggal_selesai) . "' ";
	}

	if ($asal_kantong != '') {
		$where .= " AND rq.asal_utd = '" . mysql_real_escape_string($asal_kantong) . "' ";
	}

	$sql_data = mysql_query("
    SELECT rq.*, qc.qctgl, u.nama AS nama_utd
    FROM registrasi_qc rq
    LEFT JOIN utd u ON u.id = rq.asal_utd
    LEFT JOIN qc ON qc.nokantong = rq.nokantong
    $where
    ORDER BY rq.id DESC
") or die(mysql_error());
}

?>

<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <title>Laporan QC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_red.css">

    <style>
    body {
        background: #f6f7fb;
    }

    .card-header h4 {
        margin-bottom: 0;
        font-weight: 700;
    }

    .card-header p {
        margin-bottom: 0;
        opacity: 0.9;
    }

    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #212529;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .table thead th,
    .table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .checkbox-col {
        width: 45px;
        text-align: center;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
    }
    </style>


</head>

<body>
    <div class="container-fluid p-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h4>Laporan QC</h4>
                    <p>Lembar data analisa Uji Mutu</p>
                </div>
                <!-- <a href="javascript:history.back()" class="btn btn-light btn-sm">Kembali</a> -->
            </div>


            <div class="card-body">
                <form method="POST" action="" autocomplete="off">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="text" id="tanggal_mulai" name="tanggal_mulai" class="form-control"
                                value="<?php echo $tanggal_mulai; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="text" id="tanggal_selesai" name="tanggal_selesai" class="form-control"
                                value="<?php echo $tanggal_selesai; ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="asal_kantong" class="form-label">Asal Kantong</label>
                            <select name="asal_kantong" id="asal_kantong" class="form-select">
                                <option value="">Semua Asal Kantong</option>
                                <?php
								if ($sql_utd && mysql_num_rows($sql_utd) > 0) {
									while ($row_utd = mysql_fetch_assoc($sql_utd)) {
										$id_utd   = $row_utd['id'];
										$nama_utd = $row_utd['nama'];
										$selected = ($asal_kantong == $id_utd) ? 'selected="selected"' : '';
										echo '<option value="' . htmlspecialchars($id_utd, ENT_QUOTES) . '" ' . $selected . '>' .
											htmlspecialchars($nama_utd, ENT_QUOTES) .
											'</option>';
									}
								}
								?>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="submit_tampil" class="btn btn-primary w-100">
                                Tampilkan
                            </button>
                        </div>
                    </div>


                    <div id="table_container" class="table-responsive"
                        style="<?php echo $show_table ? '' : 'display:none;'; ?>">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle w-100"
                            id="dtable">
                            <thead class="table-danger text-center">
                                <tr>
                                    <th class="checkbox-col"></th>
                                    <th>No</th>
                                    <th>No. Kantong</th>
                                    <th>Gol Darah</th>
                                    <th>Rhesus</th>
                                    <th>Produk</th>
                                    <th>Tgl Aftap</th>
                                    <th>Tgl Kadaluwarsa</th>
                                    <th>Tgl Penerimaan Sampel</th>
                                    <th>Petugas Yg Menyerahkan</th>
                                    <th>Petugas Yg Menerima</th>
                                    <th>Asal Sampel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
								if ($show_table) {
									if ($sql_data && mysql_num_rows($sql_data) > 0) {
										$no = 1;
										while ($row = mysql_fetch_assoc($sql_data)) {
								?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="qc-check" name="pilih[]"
                                            value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES); ?>"
                                            data-produk="<?php echo htmlspecialchars(normalizeProduk($row['produk']), ENT_QUOTES); ?>"
                                            data-asal="<?php echo htmlspecialchars($row['asal_utd'], ENT_QUOTES); ?>">
                                    </td>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['nokantong'], ENT_QUOTES); ?></td>
                                    <td class="text-center">
                                        <?php echo htmlspecialchars($row['goldarah'], ENT_QUOTES); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($row['rhesus'], ENT_QUOTES); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(normalizeProduk($row['produk']), ENT_QUOTES); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['tglaftap'], ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($row['kadaluwarsa'], ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($row['tgl'], ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($row['petugas_serah'], ENT_QUOTES); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['petugas_terima'], ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_utd'], ENT_QUOTES); ?></td>
                                </tr>
                                <?php
										}
									}
								}
								?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-none justify-content-end gap-2" id="action_area">
                        <button type="button" class="btn btn-outline-primary" id="btn_pilih_grup">
                            Pilih Semua Grup Ini
                        </button>

                        <button type="button" class="btn btn-success" id="btn_cetak_laporan">
                            Cetak Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCetak" tabindex="-1" aria-labelledby="modalCetakLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalCetakLabel">Cetak Laporan QC</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pakai Sertifikat?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pakai_sertifikat" id="sertifikat_tidak"
                                value="0" checked>
                            <label class="form-check-label" for="sertifikat_tidak">Tidak</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pakai_sertifikat" id="sertifikat_ya"
                                value="1">
                            <label class="form-check-label" for="sertifikat_ya">Ya</label>
                        </div>
                    </div>

                    <div id="box_sertifikat" style="display:none;">
                        <div class="mb-3">
                            <label for="no_sertifikat" class="form-label">Nomor Sertifikat</label>
                            <input type="text" class="form-control" id="no_sertifikat" name="no_sertifikat"
                                placeholder="Opsional">
                        </div>

                        <div class="mb-3">
                            <label for="tgl_sertifikat" class="form-label">Tanggal Sertifikat</label>
                            <input type="text" class="form-control" id="tgl_sertifikat" name="tgl_sertifikat"
                                placeholder="Opsional">
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        Data cetak untuk list yang dipilih akan diproses setelah tombol cetak dikonfirmasi.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btn_lanjut_cetak">Lanjut Cetak</button>
                </div>
            </div>
        </div>
    </div>

    <form id="form_proses_cetak" method="POST" action="QC/proses_cetak_qc.php" style="display:none;">
        <input type="hidden" name="selected_ids" id="selected_ids">
        <input type="hidden" name="pakai_sertifikat" id="hidden_pakai_sertifikat">
        <input type="hidden" name="no_sertifikat" id="hidden_no_sertifikat">
        <input type="hidden" name="tgl_sertifikat" id="hidden_tgl_sertifikat">
    </form>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    $(function() {

        $('#asal_kantong').select2({
            width: '100%',
            placeholder: 'Pilih Asal Kantong',
            allowClear: true
        });

        <?php if ($show_table): ?>
        $('#dtable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            autoWidth: false,
            scrollX: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            }
        });
        <?php endif; ?>


        flatpickr("#tanggal_mulai", {
            dateFormat: "Y-m-d",
            defaultDate: "<?php echo $tanggal_mulai; ?>",
            allowInput: true
        });

        flatpickr("#tanggal_selesai", {
            dateFormat: "Y-m-d",
            defaultDate: "<?php echo $tanggal_selesai; ?>",
            allowInput: true
        });

        flatpickr("#tgl_sertifikat", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        function normalizeProduk(value) {
            var produk = String(value || '').trim().toUpperCase();

            if (/^(PRC|WB)\b/.test(produk)) {
                produk = produk.replace(/\s*\d+(\.\d+)?$/, '').trim();
            }

            return produk;
        }

        function resetCheckboxGroup() {
            $('.qc-check').prop('disabled', false);
            $('.qc-check').prop('checked', false);
            updateActionArea();
        }

        function updateActionArea() {
            var checkedCount = $('.qc-check:checked').length;

            if (checkedCount > 0) {
                $('#action_area').removeClass('d-none').addClass('d-flex');
            } else {
                $('#action_area').removeClass('d-flex').addClass('d-none');
            }
        }

        function lockByGroup($cb) {
            var produk = normalizeProduk($cb.data('produk'));
            var asal = String($cb.data('asal'));

            $('.qc-check').each(function() {
                var $item = $(this);
                var itemProduk = normalizeProduk($item.data('produk'));
                var itemAsal = String($item.data('asal'));

                var sameGroup = (produk === itemProduk && asal === itemAsal);

                if (sameGroup) {
                    $item.prop('disabled', false);
                } else {
                    $item.prop('checked', false);
                    $item.prop('disabled', true);
                }
            });
        }

        function getSelectedGroup() {
            var $checked = $('.qc-check:checked').first();
            if ($checked.length === 0) return null;

            return {
                produk: $checked.data('produk'),
                asal: $checked.data('asal')
            };
        }

        $(document).on('change', '.qc-check', function() {
            var checkedCount = $('.qc-check:checked').length;

            if (checkedCount > 0) {
                lockByGroup($('.qc-check:checked').first());
            } else {
                resetCheckboxGroup();
            }

            updateActionArea();
        });

        $('#btn_pilih_grup').on('click', function() {
            var grp = getSelectedGroup();
            if (!grp) return;

            $('.qc-check').each(function() {
                var $item = $(this);
                var itemProduk = normalizeProduk($item.data('produk'));
                var itemAsal = String($item.data('asal'));

                if (itemProduk === normalizeProduk(grp.produk) && itemAsal === String(grp
                        .asal)) {
                    $item.prop('checked', true);
                }
            });

            updateActionArea();
        });

        $('input[name="pakai_sertifikat"]').on('change', function() {
            if ($('#sertifikat_ya').is(':checked')) {
                $('#box_sertifikat').slideDown(150);
            } else {
                $('#box_sertifikat').slideUp(150);
                $('#no_sertifikat').val('');
                $('#tgl_sertifikat').val('');
            }
        });

        $('#btn_cetak_laporan').on('click', function() {
            var modalCetak = new bootstrap.Modal(document.getElementById('modalCetak'));
            modalCetak.show();
        });

        $('#btn_lanjut_cetak').on('click', function() {
            var selectedIds = [];
            $('.qc-check:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                alert('Silahkan pilih minimal 1 data terlebih dahulu.');
                return;
            }

            $('#selected_ids').val(selectedIds.join(','));
            $('#hidden_pakai_sertifikat').val($('input[name="pakai_sertifikat"]:checked').val());
            $('#hidden_no_sertifikat').val($('#no_sertifikat').val());
            $('#hidden_tgl_sertifikat').val($('#tgl_sertifikat').val());

            $('#form_proses_cetak').submit();
            console.log('Form submitted with selected IDs:', selectedIds.join(','));
        });

        updateActionArea();
    });
    </script>


</body>

</html>