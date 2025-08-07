<?php
session_start();
require_once('config/dbi_connect.php');
//require_once('config/dbi_checker.php');
$namauser = "irawanDB";
$namalengkap = "Eko Putra Irawan";
$lvl0 = "qa";

?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SIMDONDAR</title>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- DataTables CSS -->
	<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

	<!-- Tagify CSS -->
	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css"> -->

	<!-- Tagify JS -->
	<!-- <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script> -->

	<link rel="stylesheet" href="../assets/tagify/tagify.css">
	<script src="../assets/tagify/tagify.js"></script>

	<style>
		/* WARNA GRADASI KOP PMI  
		UTAMA: #e5002c
		KEDUA: #ff7276
		KETIGA: #ff8674
		KEEMPAT: #f5deda
		KELIMA:rgb(252, 233, 233)
		ATAU 
		KELIMA: #fce9e9
		*/
		h2 {
			color: rgb(169, 8, 8);
		}

		.btn-ubah {
			background-color: rgb(169, 8, 8);
			color: white;
		}

		.btn-ubah:hover {
			background-color: #ff7276;
			color: white;
		}

		.btn-ubah:active {
			background-color: #d9534f;
			color: white;
			transform: scale(0.95);
			transition: transform 0.1s ease-in-out;
		}

		.btn-simpan {
			background-color: rgb(169, 8, 8);
			color: white;
		}

		.btn-ubah:hover {
			background-color: #ff7276;
			color: white;
		}

		.modal-header {
			background-color: #007bff;
			color: white;
		}

		.table-container {
			width: 99%;
			margin: 0 auto;
			overflow-x: auto;
		}

		.custom-table thead th {
			background-color: #ED6161 !important;
			color: white !important;
			font-size: 12px !important;
			white-space: normal !important;
			/* white-space: nowrap !important; */
			word-break: break-word !important;
			text-align: center !important;
			vertical-align: middle !important;
		}

		.custom-table tbody td {
			background-color: #f8f9fa !important;
			font-size: 14px !important;
			text-align: center !important;
			vertical-align: middle !important;
		}

		.custom-table tbody tr:hover {
			background-color: #e9ecef !important;
		}

		.modal-lg {
			max-width: 80%;
		}

		/* Warna navigasi pagination */
		.dataTables_paginate .pagination .page-item.active .page-link {
			background-color: rgb(169, 8, 8) !important;
			color: white !important;
			border-color: rgb(169, 8, 8) !important;
		}

		.dataTables_paginate .pagination .page-link {
			color: rgb(169, 8, 8) !important;
		}

		.dataTables_paginate .pagination .page-link:hover {
			background-color: #ff7276 !important;
			color: white !important;
		}

		.btn-tambah {
			background-color: rgb(169, 8, 8);
			color: white;
			border-radius: 5px;
			padding: 8px 12px;
			margin-left: 10px;
		}

		.btn-tambah:hover {
			background-color: #ff7276;
			color: white;
		}

		.bg-modal-tambah {
			background-color: rgb(169, 8, 8);
			color: white;
		}

		.bg-modal-detail {
			background-color: rgb(169, 8, 8);
			color: white;
		}

		.tagify.is-invalid {
			border: 1px solid red !important;
			border-radius: 0.375rem;
		}
		
		#kantongTable thead th {
			font-size: 10px !important;
		}

		#kantongTable tbody td {
			font-size: 13px;
		}

		#kantongTable {
			font-size: 13px !important;
		}
	</style>
</head>

<body>
	<div class="table-container mt-4">
		<h2 class="text-center">Master Berat Kantong Kosong</h2>
		<div class="table-responsive">
			<div class="d-flex justify-content-between mb-3">
				<div>
					<button type="button" class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#tambahModal">
						+ Tambah Data
					</button>
				</div>
			</div>

			<table id="kantongTable" class="table table-striped table-bordered custom-table" style="width:100%;">
				<thead>
					<tr>
						<th>NO.</th>
						<th>MERK KANTONG</th>
						<th>JENIS</th>
						<th>VOLUME KANTONG</th>
						<th>VOL. ANTIKOAGULANT</th>
						<th>LAMA BUKA (HARI)</th>
						<th>KANTONG UTAMA</th>
						<th>SATELIT 1</th>
						<th>SATELIT 2</th>
						<th>SATELIT 3</th>
						<th>SATELIT 4</th>
						<th>SATELIT 5</th>
						<th>SATELIT 6</th>
						<th>SATELIT 7</th>
						<th>AKSI</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$no = 0;
					$sql = "SELECT `id`, `merk`, `jenis`, 
						CASE `jenis`
							WHEN '1' THEN 'Single'
							WHEN '2' THEN 'Double'
							WHEN '3' THEN 'Tripple'
							WHEN '4' THEN 'Quadrupple'
							WHEN '5' THEN 'Quadrupple T&B'
							WHEN '6' THEN 'Pediatrik'
						END AS jeniskantong, `vol`, `antikoagulant`,
						`berat_ku`, `berat_s1`, `berat_s2`, `berat_s3`, `berat_s4`, `berat_s5`, `berat_s6`, `berat_s7`, `lama_buka` 
						FROM `master_kantong`
						ORDER BY `merk`, `jenis`";
					$qraw = mysqli_query($dbi, $sql);
					while ($tmp = mysqli_fetch_assoc($qraw)) {
						$no++;
						?>
						<tr>
							<td><?php echo $no; ?></td>
							<td>
								<span class="klik-merk text-danger" style="cursor:pointer;"
									data-merk="<?php echo htmlspecialchars($tmp['merk']); ?>"
									data-id="<?php echo $tmp['id']; ?>">
									<?php echo htmlspecialchars($tmp['merk']); ?>
								</span>
							</td>
							<td><?php echo $tmp['jeniskantong']; ?></td>
							<td><?php echo $tmp['vol']; ?></td>
							<td><?php echo $tmp['antikoagulant']; ?></td>
							<td><?php echo $tmp['lama_buka']; ?></td>
							<td><?php echo $tmp['berat_ku']; ?></td>
							<td><?php echo $tmp['berat_s1'] ?: ''; ?></td>
							<td><?php echo $tmp['berat_s2'] ?: ''; ?></td>
							<td><?php echo $tmp['berat_s3'] ?: ''; ?></td>
							<td><?php echo $tmp['berat_s4'] ?: ''; ?></td>
							<td><?php echo $tmp['berat_s5'] ?: ''; ?></td>
							<td><?php echo $tmp['berat_s6'] ?: ''; ?></td>
							<td><?php echo $tmp['berat_s7'] ?: ''; ?></td>
							<td>
								<button class="btn btn-ubah btn-sm edit-btn" data-id="<?php echo $tmp['id']; ?>"
									data-bs-toggle="modal" data-bs-target="#editModal">Ubah</button>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<!-- <a href="pmi<?php echo $lvl0; ?>.php?module=kantong" class="btn btn-success mt-3">Tambah</a> -->
	</div>

	<!-- Modal Detail Merk -->
	<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-modal-detail text-white d-flex justify-content-between align-items-start">
					<div>
						<h5 class="modal-title mb-0" id="detailModalLabel">Detail Master Berat Kantong</h5>
					</div>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" id="isiDetail">
					<!-- Konten detail akan dimuat via JS -->
					Memuat data...
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Tambah Data -->
	<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-modal-tambah text-white d-flex justify-content-between align-items-start">
					<div>
						<h5 class="modal-title mb-0" id="tambahModalLabel">Tambah Master Berat Kantong</h5>
						<small><em>(*) required atau harus diisi</em></small>
					</div>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<div class="modal-body">
					<form id="tambahForm">
						<h6>Master Data*</h6>
						<div class="row mb-3">
							<div class="col-md">
								<label for="merk" class="form-label">Merk</label>
								<select class="form-select" id="merk" name="merk" required>
									<option value="">Pilih Merk</option>
									<?php
									$queryMerk = "SELECT `merk` FROM `kantong_merk` WHERE `aktif` = 0 ORDER BY `id`";
									$resultMerk = mysqli_query($dbi, $queryMerk);
									while ($rowMerk = mysqli_fetch_assoc($resultMerk)) {
										echo '<option value="' . htmlspecialchars($rowMerk['merk']) . '">' . htmlspecialchars($rowMerk['merk']) . '</option>';
									}
									?>
								</select>
							</div>
							<div class="col-md">
								<label for="jenis" class="form-label">Jenis Kantong</label>
								<select class="form-select" id="jenis" name="jenis" required>
									<option value="">Pilih Jenis</option>
									<option value="1">Single</option>
									<option value="2">Double</option>
									<option value="3">Triple</option>
									<option value="4">Quadruple</option>
									<!-- <option value="5">Quadruple T&B</option> -->
									<option value="6">Pediatrik</option>
								</select>
							</div>
							<div class="col-md">
								<label for="lama_buka" class="form-label">Lama Buka (Hari)</label>
								<input type="number" class="form-control" id="lama_buka" name="lama_buka" required>
							</div>
							<div class="col-md">
								<label for="volume" class="form-label">Volume</label>
								<input type="text" class="form-control" id="volume" name="volume"
									pattern="[0-9]+(\.[0-9]+)?" required>
							</div>
							<div class="col-md">
								<label for="berat_ku" class="form-label">
									Berat Kantong Utama
									<span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
										title="Berat kantong utama dalam satuan gram (gr)"
										style="font-size: 0.6em; display: inline-flex; align-items: flex-start; justify-content: center; width: 0.8em; height: 0.8em; border-radius: 50%; background-color: #f0f0f0; position: relative; top: -0.9em;">
										<i class="bi bi-question-circle" style="font-size: 1em;"></i>
									</span>
								</label>
								<input type="text" class="form-control" id="berat_ku" name="berat_ku"
									pattern="[0-9]+(\.[0-9]+)?" required>
							</div>
							<div class="col-md">
								<label for="antikoagulant" class="form-label">
									Antikoagulant
									<span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
										title="Antikoagulant adalah zat yang digunakan untuk mencegah pembekuan darah."
										style="font-size: 0.6em; display: inline-flex; align-items: flex-start; justify-content: center; width: 0.8em; height: 0.8em; border-radius: 50%; background-color: #f0f0f0; position: relative; top: -0.9em;">
										<i class="bi bi-question-circle" style="font-size: 1em;"></i>
									</span>
								</label>
								<input type="text" class="form-control" id="antikoagulant" name="antikoagulant"
									pattern="[0-9]+(\.[0-9]+)?" required>
							</div>
						</div>
						<div class="row mb-3">
						</div>
						<h6>Detail Data Kantong (Rincian)</h6>
						<div class="row mb-3">
							<div class="col-md">
								<label for="namakantong" class="form-label">Nama Kantong</label>
								<input type="text" class="form-control" id="namakantong" name="namakantong"
									maxlength="50">
							</div>
							<div class="col-md-4">
								<label for="company" class="form-label">Perusahaan</label>
								<input type="text" class="form-control" id="company" name="company" maxlength="100">
							</div>
							<div class="col-md-2">
								<label for="composition" class="form-label">Komposisi</label>
								<input type="text" class="form-control" id="composition" name="composition"
									maxlength="255">
							</div>
							<div class="col-md">
								<label for="texture" class="form-label">Tekstur</label>
								<input type="text" class="form-control" id="texture" name="texture" maxlength="100">
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-2">
								<label for="anticoagulant_name" class="form-label">Nama Antikoagulan</label>
								<select class="form-select" id="anticoagulant_name" name="anticoagulant_name" required>
									<option value="">Pilih</option>
									<?php
									$queryAnticoagulant = "SELECT `acd_id`, `acd_name` FROM `pmf_anticoagulants` ORDER BY `acd_name`";
									$resultAnticoagulant = mysqli_query($dbi, $queryAnticoagulant);
									while ($rowAnticoagulant = mysqli_fetch_assoc($resultAnticoagulant)) {
										echo '<option value="' . htmlspecialchars($rowAnticoagulant['acd_id']) . '">' . htmlspecialchars($rowAnticoagulant['acd_name']) . '</option>';
									}
									?>
								</select>
							</div>
							<div class="col-md-2">
								<label for="standard_bag" class="form-label">Standar Kantong</label>
								<input type="text" class="form-control" id="standard_bag" name="standard_bag"
									maxlength="10">
							</div>
							<div class="col-md-2">
								<label for="standard_acd" class="form-label">Standar ACD</label>
								<input type="text" class="form-control" id="standard_acd" name="standard_acd"
									maxlength="10">
							</div>
							<div class="col-md">
								<label for="license" class="form-label">Lisensi</label>
								<input type="text" class="form-control" id="license" name="license" maxlength="50">
							</div>
							<div class="col-md">
								<label for="licenseby" class="form-label">Dilisensikan Oleh</label>
								<input type="text" class="form-control" id="licenseby" name="licenseby" maxlength="100">
							</div>
						</div>
						<div class="row mb-3">
						</div>
						<div class="row mb-1">
							<div class="col-md-2">
								<label for="dimensi_tinggi" class="form-label">Dimensi Tinggi (cm)</label>
								<input type="number" step="0.1" class="form-control" id="dimensi_tinggi"
									name="dimensi_tinggi">
							</div>
							<div class="col-md-2">
								<label for="dimensi_lebar" class="form-label">Dimensi Lebar (cm)</label>
								<input type="number" step="0.1" class="form-control" id="dimensi_lebar"
									name="dimensi_lebar">
							</div>
							<div class="col-md-2">
								<label for="panjangselang" class="form-label">Panjang Selang (cm)</label>
								<input type="number" step="0.1" class="form-control" id="panjangselang"
									name="panjangselang">
							</div>
							<div class="col-md-3">
								<label for="beratkeseluruhan" class="form-label">Berat Keseluruhan (kg)</label>
								<input type="number" step="0.001" class="form-control" id="beratkeseluruhan"
									name="beratkeseluruhan">
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-2">
								<label for="ev_utdptanggal" class="form-label">Tanggal UTD</label>
								<input type="date" class="form-control" id="ev_utdptanggal" name="ev_utdptanggal">
							</div>
							<div class="col-md-2">
								<label for="ev_utdpnomor" class="form-label">Nomor UTD</label>
								<input type="text" class="form-control" id="ev_utdpnomor" name="ev_utdpnomor"
									maxlength="50">
							</div>
							<div class="col-md-2">
								<label for="expired" class="form-label">Masa Berlaku (Hari)</label>
								<input type="number" class="form-control" id="expired" name="expired">
							</div>
						</div>
						<div class="row mb-3">
						</div>
						<h6>Berat Kantong Satelit</h6>
						<div class="row">
							<div class="col-md-1 mb-2">
								<label for="berat_s1" class="form-label">Satelit 1</label>
								<input type="text" class="form-control" id="berat_s1" name="berat_s1"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="berat_s2" class="form-label">Satelit 2</label>
								<input type="text" class="form-control" id="berat_s2" name="berat_s2"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="berat_s3" class="form-label">Satelit 3</label>
								<input type="text" class="form-control" id="berat_s3" name="berat_s3"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="berat_s4" class="form-label">Satelit 4</label>
								<input type="text" class="form-control" id="berat_s4" name="berat_s4"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="berat_s5" class="form-label">Satelit 5</label>
								<input type="text" class="form-control" id="berat_s5" name="berat_s5"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="berat_s6" class="form-label">Satelit 6</label>
								<input type="text" class="form-control" id="berat_s6" name="berat_s6"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="berat_s7" class="form-label">Satelit 7</label>
								<input type="text" class="form-control" id="berat_s7" name="berat_s7"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
						</div>
						<h6>Produk Kantong</h6>
						<div class="row">
							<div class="col-md-3 mb-2">
								<label for="pr_utama" class="form-label">Utama</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="pr_utama" name="pr_utama" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-2">
								<label for="pr_s1" class="form-label">Satelit 1</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="pr_s1" name="pr_s1" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="pr_s2" class="form-label">Satelit 2</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="pr_s2" name="pr_s2" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="pr_s3" class="form-label">Satelit 3</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="pr_s3" name="pr_s3" oninput="this.value = this.value.toUpperCase();">
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 mb-3">
								<label for="pr_s4" class="form-label">Satelit 4</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="pr_s4" name="pr_s4" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="pr_s5" class="form-label">Satelit 5</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="pr_s5" name="pr_s5" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="pr_s6" class="form-label">Satelit 6</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="pr_s6" name="pr_s6" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="pr_s7" class="form-label">Satelit 7</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="pr_s7" name="pr_s7" oninput="this.value = this.value.toUpperCase();">
							</div>
						</div>
						<input type="hidden" name="aksi" value="tambah">
						<button type="submit" class="btn btn-success">Simpan</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Edit Data Master Berat Kantong -->
	<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-modal-tambah text-white d-flex justify-content-between align-items-start">
					<div>
						<h5 class="modal-title mb-0" id="editModalLabel">Ubah Data Berat Kantong</h5>
						<small><em>(*) required atau harus diisi</em></small>
					</div>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="editForm">
						<input type="hidden" id="editId" name="id">
						<!-- <div class="mb-3">
							<label for="editMerk" class="form-label">Merk Kantong</label>
							<input type="text" class="form-control" id="editMerk" name="merk" required>
						</div>
						<div class="mb-3">
							<label for="editVolume" class="form-label">Volume Kantong</label>
							<input type="text" class="form-control" id="editVolume" name="volume" required>
						</div> -->
						<h6>Master Data*</h6>
						<div class="row mb-3">
							<div class="col-md">
								<label for="editMerk" class="form-label">Merk</label>
								<select class="form-select" id="editMerk" name="editMerk" disabled>
									<option value="">Pilih Merk</option>
									<?php
									$queryMerk = "SELECT `merk` FROM `kantong_merk` WHERE `aktif` = 0 ORDER BY `id`";
									$resultMerk = mysqli_query($dbi, $queryMerk);
									while ($rowMerk = mysqli_fetch_assoc($resultMerk)) {
										echo '<option value="' . htmlspecialchars($rowMerk['merk']) . '">' . htmlspecialchars($rowMerk['merk']) . '</option>';
									}
									?>
								</select>
								<input type="hidden" name="editMerkHidden" id="editMerkHidden">
							</div>
							<div class="col-md">
								<label for="editJenis" class="form-label">Jenis Kantong</label>
								<select class="form-select" id="editJenis" name="editJenis" disabled>
									<option value="">Pilih Jenis</option>
									<option value="1">Single</option>
									<option value="2">Double</option>
									<option value="3">Triple</option>
									<option value="4">Quadruple</option>
									<!-- <option value="5">Quadruple T&B</option> -->
									<option value="6">Pediatrik</option>
								</select>
								<input type="hidden" name="editJenisHidden" id="editJenisHidden">
							</div>
							<div class="col-md">
								<label for="editLamaBuka" class="form-label">Lama Buka (Hari)</label>
								<input type="number" class="form-control" id="editLamaBuka" name="editLamaBuka"
									required>
							</div>
							<div class="col-md">
								<label for="editVolume" class="form-label">Volume</label>
								<input type="text" class="form-control" id="editVolume" name="editVolume"
									pattern="[0-9]+(\.[0-9]+)?" required>
							</div>
							<div class="col-md">
								<label for="editBeratKU" class="form-label">Berat Kantong Utama</label>
								<input type="text" class="form-control" id="editBeratKU" name="editBeratKU"
									pattern="[0-9]+(\.[0-9]+)?" required>
							</div>
							<div class="col-md">
								<label for="editAntikoagulant" class="form-label">
									Antikoagulant
									<span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
										title="Antikoagulant adalah zat yang digunakan untuk mencegah pembekuan darah."
										style="font-size: 0.6em; display: inline-flex; align-items: flex-start; justify-content: center; width: 0.8em; height: 0.8em; border-radius: 50%; background-color: #f0f0f0; position: relative; top: -0.9em;">
										<i class="bi bi-question-circle" style="font-size: 1em;"></i>
									</span>
								</label>
								<input type="text" class="form-control" id="editAntikoagulant" name="editAntikoagulant"
									pattern="[0-9]+(\.[0-9]+)?" required>
							</div>
						</div>
						<div class="row mb-3">
						</div>
						<h6>Detail Data Kantong (Rincian)</h6>
						<div class="row mb-3">
							<div class="col-md">
								<label for="editNamaKantong" class="form-label">Nama Kantong</label>
								<input type="text" class="form-control" id="editNamaKantong" name="editNamaKantong"
									maxlength="50">
							</div>
							<div class="col-md-4">
								<label for="editCompany" class="form-label">Perusahaan</label>
								<input type="text" class="form-control" id="editCompany" name="editCompany"
									maxlength="100">
							</div>
							<div class="col-md-2">
								<label for="editComposition" class="form-label">Komposisi</label>
								<input type="text" class="form-control" id="editComposition" name="editComposition"
									maxlength="255">
							</div>
							<div class="col-md">
								<label for="editTexture" class="form-label">Tekstur</label>
								<input type="text" class="form-control" id="editTexture" name="editTexture"
									maxlength="100">
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-2">
								<label for="editAnticoagulantName" class="form-label">Nama Antikoagulan</label>
								<select class="form-select" id="editAnticoagulantName" name="editAnticoagulantName"
									required>
									<option value="">Pilih</option>
									<?php
									$queryAnticoagulant = "SELECT `acd_id`, `acd_name` FROM `pmf_anticoagulants` ORDER BY `acd_name`";
									$resultAnticoagulant = mysqli_query($dbi, $queryAnticoagulant);
									while ($rowAnticoagulant = mysqli_fetch_assoc($resultAnticoagulant)) {
										echo '<option value="' . htmlspecialchars($rowAnticoagulant['acd_id']) . '">' . htmlspecialchars($rowAnticoagulant['acd_name']) . '</option>';
									}
									?>
								</select>
							</div>
							<div class="col-md-2">
								<label for="editStandardBag" class="form-label">Standar Kantong</label>
								<input type="text" class="form-control" id="editStandardBag" name="editStandardBag"
									maxlength="10">
							</div>
							<div class="col-md-2">
								<label for="editStandardAcd" class="form-label">Standar ACD</label>
								<input type="text" class="form-control" id="editStandardAcd" name="editStandardAcd"
									maxlength="10">
							</div>
							<div class="col-md">
								<label for="editLicense" class="form-label">Lisensi</label>
								<input type="text" class="form-control" id="editLicense" name="editLicense"
									maxlength="50">
							</div>
							<div class="col-md">
								<label for="editLicenseby" class="form-label">Dilisensikan Oleh</label>
								<input type="text" class="form-control" id="editLicenseby" name="editLicenseby"
									maxlength="100">
							</div>
						</div>
						<div class="row mb-3">
						</div>
						<div class="row mb-1">
							<div class="col-md-2">
								<label for="editDimensiTinggi" class="form-label">Dimensi Tinggi (cm)</label>
								<input type="number" step="0.1" class="form-control" id="editDimensiTinggi"
									name="editDimensiTinggi">
							</div>
							<div class="col-md-2">
								<label for="editDimensiLebar" class="form-label">Dimensi Lebar (cm)</label>
								<input type="number" step="0.1" class="form-control" id="editDimensiLebar"
									name="editDimensiLebar">
							</div>
							<div class="col-md-2">
								<label for="editPanjangSelang" class="form-label">Panjang Selang (cm)</label>
								<input type="number" step="0.1" class="form-control" id="editPanjangSelang"
									name="editPanjangSelang">
							</div>
							<div class="col-md-3">
								<label for="editBeratKeseluruhan" class="form-label">Berat Keseluruhan (kg)</label>
								<input type="number" step="0.001" class="form-control" id="editBeratKeseluruhan"
									name="editBeratKeseluruhan">
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-2">
								<label for="editEvUtdptanggal" class="form-label">Tanggal UTD</label>
								<input type="date" class="form-control" id="editEvUtdptanggal" name="editEvUtdptanggal">
							</div>
							<div class="col-md-2">
								<label for="editEvUtdpnomor" class="form-label">Nomor UTD</label>
								<input type="text" class="form-control" id="editEvUtdpnomor" name="editEvUtdpnomor"
									maxlength="50">
							</div>
							<div class="col-md-2">
								<label for="editExpired" class="form-label">Masa Berlaku (Hari)</label>
								<input type="number" class="form-control" id="editExpired" name="editExpired">
							</div>
						</div>
						<div class="row mb-3">
						</div>
						<h6>Berat Kantong Satelit</h6>
						<div class="row">
							<div class="col-md-1 mb-2">
								<label for="edit_berat_s1" class="form-label">Satelit 1</label>
								<input type="text" class="form-control" id="edit_berat_s1" name="edit_berat_s1"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="edit_berat_s2" class="form-label">Satelit 2</label>
								<input type="text" class="form-control" id="edit_berat_s2" name="edit_berat_s2"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="edit_berat_s3" class="form-label">Satelit 3</label>
								<input type="text" class="form-control" id="edit_berat_s3" name="edit_berat_s3"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="edit_berat_s4" class="form-label">Satelit 4</label>
								<input type="text" class="form-control" id="edit_berat_s4" name="edit_berat_s4"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="edit_berat_s5" class="form-label">Satelit 5</label>
								<input type="text" class="form-control" id="edit_berat_s5" name="edit_berat_s5"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="edit_berat_s6" class="form-label">Satelit 6</label>
								<input type="text" class="form-control" id="edit_berat_s6" name="edit_berat_s6"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
							<div class="col-md-1 mb-3">
								<label for="edit_berat_s7" class="form-label">Satelit 7</label>
								<input type="text" class="form-control" id="edit_berat_s7" name="edit_berat_s7"
									pattern="[0-9]+(\.[0-9]+)?">
							</div>
						</div>
						<h6>Produk Kantong</h6>
						<div class="row">
							<div class="col-md-3 mb-2">
								<label for="edit_pr_utama" class="form-label">Utama</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="edit_pr_utama"
									name="edit_pr_utama" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-2">
								<label for="edit_pr_s1" class="form-label">Satelit 1</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="edit_pr_s1" name="edit_pr_s1" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="edit_pr_s2" class="form-label">Satelit 2</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="edit_pr_s2" name="edit_pr_s2" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="edit_pr_s3" class="form-label">Satelit 3</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="edit_pr_s3" name="edit_pr_s3" oninput="this.value = this.value.toUpperCase();">
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 mb-3">
								<label for="edit_pr_s4" class="form-label">Satelit 4</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="edit_pr_s4" name="edit_pr_s4" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="edit_pr_s5" class="form-label">Satelit 5</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="edit_pr_s5" name="edit_pr_s5" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="edit_pr_s6" class="form-label">Satelit 6</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="edit_pr_s6" name="edit_pr_s6" oninput="this.value = this.value.toUpperCase();">
							</div>
							<div class="col-md-3 mb-3">
								<label for="edit_pr_s7" class="form-label">Satelit 7</label>
								<input type="text" class="form-control produk-tagify text-uppercase" id="edit_pr_s7" name="edit_pr_s7" oninput="this.value = this.value.toUpperCase();">
							</div>
						</div>
						<input type="hidden" name="aksi" value="edit">
						<button type="submit" class="btn btn-primary">Simpan</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Scripts -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

	<!-- Include Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			// Initialize Bootstrap tooltips
			var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
			var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
				return new bootstrap.Tooltip(tooltipTriggerEl);
			});
		});
	</script>

	<script>
		$(document).ready(function () {
			let table = $('#kantongTable').DataTable({
				scrollX: true,
				autoWidth: false,
				language: {
					"lengthMenu": "Tampilkan _MENU_ data",
					"zeroRecords": "Tidak ada data yang ditemukan",
					"info": "Menampilkan _START_ sampai _END_ dari total _TOTAL_ data",
					"infoEmpty": "Menampilkan 0 sampai 0 dari total 0 data",
					"infoFiltered": "(difilter dari _MAX_ total data)",
					"search": "Pencarian:",
					"paginate": {
						"first": "Awal",
						"last": "Akhir",
						"next": "Selanjutnya",
						"previous": "Sebelumnya"
					},
					"aria": {
						"sortAscending": ": aktifkan untuk mengurutkan kolom secara naik",
						"sortDescending": ": aktifkan untuk mengurutkan kolom secara turun"
					}
				}
			});

			$('.edit-btn').on('click', function () {
				const id = $(this).data('id');
				$('#editId').val(id);
				$('#editMerk').val('Example Merk');
				$('#editVolume').val('Example Volume');
			});
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			// Tangkap klik pada merk
			document.querySelectorAll(".klik-merk").forEach(el => {
				el.addEventListener("click", function () {
					const merk = this.dataset.merk;
					const id = this.dataset.id;

					// Tampilkan modal terlebih dahulu
					const modal = new bootstrap.Modal(document.getElementById('detailModal'));
					document.getElementById('isiDetail').innerHTML = "Memuat data detail merk: <b>" + merk + "</b>...";
					modal.show();

					// Kirim permintaan detail via AJAX
					fetch("release/detailMasterKantong.php?id=" + encodeURIComponent(id))
						.then(res => res.text())
						.then(html => {
							document.getElementById('isiDetail').innerHTML = html;
						})
						.catch(err => {
							console.error(err);
							document.getElementById('isiDetail').innerHTML = "Gagal memuat detail.";
						});
				});
			});
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const jenisKantong = document.getElementById("jenis");

			// Field yang selalu muncul
			const prUtama = document.getElementById("pr_utama");

			// Field satelit
			const beratFields = [
				document.getElementById("berat_s1"),
				document.getElementById("berat_s2"),
				document.getElementById("berat_s3"),
				document.getElementById("berat_s4"),
				document.getElementById("berat_s5"),
				document.getElementById("berat_s6"),
				document.getElementById("berat_s7"),
			];

			const prFields = [
				document.getElementById("pr_s1"),
				document.getElementById("pr_s2"),
				document.getElementById("pr_s3"),
				document.getElementById("pr_s4"),
				document.getElementById("pr_s5"),
				document.getElementById("pr_s6"),
				document.getElementById("pr_s7"),
			];

			function updateSatelitFields() {
				const jenisValue = jenisKantong.value;
				let jumlahSatelit = 0;

				if (jenisValue === "2") jumlahSatelit = 1; // Double
				else if (jenisValue === "3") jumlahSatelit = 2; // Triple
				else if (jenisValue === "4" || jenisValue === "5") jumlahSatelit = 3; // Quadruple
				else if (jenisValue === "6") jumlahSatelit = 7; // Pediatrik

				// Tampilkan pr_utama
				prUtama.parentElement.style.display = "block";

				// // Tampilkan berat dan pr_s sesuai jumlah satelit
				// beratFields.forEach((field, index) => {
				// 	if (index < jumlahSatelit) {
				// 		field.parentElement.style.display = "block";
				// 		prFields[index].parentElement.style.display = "block";
				// 	} else {
				// 		field.parentElement.style.display = "none";
				// 		field.value = "";
				// 		prFields[index].parentElement.style.display = "none";
				// 		prFields[index].value = "";
				// 	}
				// });

				// Tampilkan atau sembunyikan field satelit + atur required
				beratFields.forEach((field, index) => {
					if (index < jumlahSatelit) {
						field.parentElement.style.display = "block";
						field.required = true;

						// Paksa reflow agar tooltip required muncul dengan rapi
						void field.offsetWidth;
					} else {
						field.parentElement.style.display = "none";
						field.value = "";
						field.required = false;
					}
				});

				prFields.forEach((field, index) => {
					if (index < jumlahSatelit) {
						field.parentElement.style.display = "block";
						field.required = true;

						// Reflow juga untuk field pr_sX
						void field.offsetWidth;
					} else {
						field.parentElement.style.display = "none";
						field.value = "";
						field.required = false;
					}
				});

			}

			// Inisialisasi
			prUtama.parentElement.style.display = "block";
			beratFields.concat(prFields).forEach(field => field.parentElement.style.display = "none");

			// Event listener
			jenisKantong.addEventListener("change", updateSatelitFields);
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const editJenisKantong = document.getElementById("editJenis");

			// Field yang selalu muncul
			const editPrUtama = document.getElementById("edit_pr_utama");

			// Field satelit
			const editBeratFields = [
				document.getElementById("edit_berat_s1"),
				document.getElementById("edit_berat_s2"),
				document.getElementById("edit_berat_s3"),
				document.getElementById("edit_berat_s4"),
				document.getElementById("edit_berat_s5"),
				document.getElementById("edit_berat_s6"),
				document.getElementById("edit_berat_s7"),
			];

			const editPrFields = [
				document.getElementById("edit_pr_s1"),
				document.getElementById("edit_pr_s2"),
				document.getElementById("edit_pr_s3"),
				document.getElementById("edit_pr_s4"),
				document.getElementById("edit_pr_s5"),
				document.getElementById("edit_pr_s6"),
				document.getElementById("edit_pr_s7"),
			];

			function updateEditSatelitFields() {
				const editJenisValue = editJenisKantong.value;
				let jumlahEditSatelit = 0;

				if (editJenisValue === "2") jumlahEditSatelit = 1; // Double
				else if (editJenisValue === "3") jumlahEditSatelit = 2; // Triple
				else if (editJenisValue === "4" || editJenisValue === "5") jumlahEditSatelit = 3; // Quadruple
				else if (editJenisValue === "6") jumlahEditSatelit = 7; // Pediatrik

				// Tampilkan pr_utama
				editPrUtama.parentElement.style.display = "block";

				// Tampilkan berat dan pr_s sesuai jumlah satelit
				editBeratFields.forEach((field, index) => {
					if (index < jumlahEditSatelit) {
						field.parentElement.style.display = "block";
						editPrFields[index].parentElement.style.display = "block";
					} else {
						field.parentElement.style.display = "none";
						field.value = "";
						editPrFields[index].parentElement.style.display = "none";
						editPrFields[index].value = "";
					}
				});
			}

			// Inisialisasi (sembunyikan semua saat load pertama)
			editPrUtama.parentElement.style.display = "block";
			editBeratFields.concat(editPrFields).forEach(field => field.parentElement.style.display = "none");

			// Event listener jika user mengganti jenis secara manual
			editJenisKantong.addEventListener("change", updateEditSatelitFields);

			// Saat tombol edit diklik
			document.querySelectorAll(".edit-btn").forEach(button => {
				button.addEventListener("click", function () {
					const id = this.getAttribute("data-id");

					// Ambil data dari server pakai fetch AJAX
					fetch(`release/ambilBeratKantong.php?id=${id}`)
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								// Set value jenis dulu baru panggil update field
								document.getElementById("editJenis").value = data.data.jenis;
								document.getElementById("editJenisHidden").value = data.data.jenis;
								updateEditSatelitFields(); // ← Sekarang ini aman

								// Isi semua field lainnya
								document.getElementById("editId").value = data.data.id;
								document.getElementById("editMerk").value = data.data.merk;
								document.getElementById("editMerkHidden").value = data.data.merk;
								document.getElementById("editLamaBuka").value = data.data.lama_buka;
								document.getElementById("editVolume").value = data.data.vol;
								document.getElementById("editBeratKU").value = data.data.berat_ku;
								document.getElementById("editAntikoagulant").value = data.data.antikoagulant;
								document.getElementById("editNamaKantong").value = data.data.namakantong;
								document.getElementById("editCompany").value = data.data.company;
								document.getElementById("editComposition").value = data.data.composition;
								document.getElementById("editTexture").value = data.data.texture;
								document.getElementById("editAnticoagulantName").value = data.data.anticoagulant_name;
								document.getElementById("editStandardBag").value = data.data.standard_bag;
								document.getElementById("editStandardAcd").value = data.data.standard_acd;
								document.getElementById("editLicense").value = data.data.license;
								document.getElementById("editLicenseby").value = data.data.licenseby;
								document.getElementById("editDimensiTinggi").value = data.data.dimensi_tinggi;
								document.getElementById("editDimensiLebar").value = data.data.dimensi_lebar;
								document.getElementById("editPanjangSelang").value = data.data.panjang_selang;
								document.getElementById("editBeratKeseluruhan").value = data.data.berat_keseluruhan;
								document.getElementById("editEvUtdptanggal").value = data.data.ev_utdptanggal;
								document.getElementById("editEvUtdpnomor").value = data.data.ev_utdpnomor;
								document.getElementById("editExpired").value = data.data.expired;

								// Berat satelit
								document.getElementById("edit_berat_s1").value = data.data.berat_s1;
								document.getElementById("edit_berat_s2").value = data.data.berat_s2;
								document.getElementById("edit_berat_s3").value = data.data.berat_s3;
								document.getElementById("edit_berat_s4").value = data.data.berat_s4;
								document.getElementById("edit_berat_s5").value = data.data.berat_s5;
								document.getElementById("edit_berat_s6").value = data.data.berat_s6;
								document.getElementById("edit_berat_s7").value = data.data.berat_s7;

								// Produk satelit
								document.getElementById("edit_pr_utama").value = data.data.pr_utama;
								document.getElementById("edit_pr_s1").value = data.data.pr_s1;
								document.getElementById("edit_pr_s2").value = data.data.pr_s2;
								document.getElementById("edit_pr_s3").value = data.data.pr_s3;
								document.getElementById("edit_pr_s4").value = data.data.pr_s4;
								document.getElementById("edit_pr_s5").value = data.data.pr_s5;
								document.getElementById("edit_pr_s6").value = data.data.pr_s6;
								document.getElementById("edit_pr_s7").value = data.data.pr_s7;
							} else {
								alert("Data tidak ditemukan!");
							}
						})
						.catch(error => {
							console.error("Gagal mengambil data:", error);
							alert("Terjadi kesalahan saat mengambil data.");
						});
				});
			});
		});
	</script>

	<!-- AKSI SIMPAN MODAL TAMBAH dan EDIT MASTER BERAT KANTONG -->
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const form = document.getElementById("tambahForm");

			form.addEventListener("submit", function (event) {
				event.preventDefault(); // Mencegah reload halaman

				const formData = new FormData(form);

				fetch("release/aksiMasterBeratKantong.php", {
					method: "POST",
					body: formData
				})
					.then(response => {
						console.log("Raw response:", response);
						return response.text(); // ubah dulu ke text agar bisa dilihat
					})
					.then(text => {
						console.log("Response as text:", text);

						// Coba parse ke JSON secara manual, agar bisa tangani error-nya
						let data;
						try {
							data = JSON.parse(text);
						} catch (e) {
							console.error("Gagal parsing JSON:", e);
							alert("Respon dari server bukan JSON yang valid:\n" + text);
							return;
						}

						if (data.success) {
							alert("Data berhasil disimpan!");
							form.reset(); // Reset form setelah sukses
							setTimeout(() => location.reload(), 500);
						} else {
							alert("Gagal menyimpan: " + data.message);
						}
					})
					.catch(error => {
						console.error("Error (network atau fetch):", error);
						alert("Terjadi kesalahan saat menyimpan.");
					});
			});
		});

		// Untuk editForm
		document.getElementById("editForm").addEventListener("submit", function (event) {
			event.preventDefault();

			const formData = new FormData(this);

			fetch("release/aksiMasterBeratKantong.php", {
				method: "POST",
				body: formData
			})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert("Data berhasil diupdate!");
						// form.reset(); // Reset form setelah sukses
						setTimeout(() => location.reload(), 500);
					} else {
						alert("Gagal update: " + data.message);
					}
				})
				.catch(error => {
					console.error("Error:", error);
					alert("Terjadi kesalahan saat update.");
				});
		});

	</script>

	<script>
		document.addEventListener("DOMContentLoaded", () => {
			document.querySelectorAll(".produk-tagify").forEach(inputEl => {
				// Inisialisasi Tagify
				let tagify = new Tagify(inputEl, {
					enforceWhitelist: false,
					dropdown: {
						enabled: 1,
						maxItems: 10,
						classname: "produk-suggestions",
						closeOnSelect: false
					},
					whitelist: []
				});

				// Saat user ketik, ambil dari server (suggest)
				tagify.on("input", e => {
					const value = e.detail.value;

					fetch(`release/mProduk.php?term=${encodeURIComponent(value)}`)
						.then(res => res.json())
						.then(suggestions => {
							// Ambil nama-nama yang sudah dipilih di field ini
							const currentValues = tagify.value.map(item => item.value.toUpperCase());

							// Filter agar yang sudah dipilih tidak muncul lagi
							const filtered = suggestions
								.map(item => item.Nama)
								.filter(nama => !currentValues.includes(nama.toUpperCase()));

							tagify.settings.whitelist = filtered;
							tagify.dropdown.show(value);
						});
				});
			});
		});
	</script>

</body>

</html>
