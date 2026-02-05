<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Rekap Transaksi Pengolahan</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
		body {
			background-color: #f8f9fa;
		}

		.navbar {
			background-color: #d9534f;
			color: white;
		}

		.table th {
			text-align: center;
			vertical-align: middle;
			background-color: #d9534f;
			color: white;
		}

		.table td,
		.table th {
			white-space: nowrap;
		}

		.scroll-top,
		.scroll-bottom {
			position: fixed;
			right: 20px;
			padding: 10px;
			background: #d9534f;
			color: white;
			cursor: pointer;
			border-radius: 50%;
		}

		.scroll-top {
			bottom: 80px;
		}

		.scroll-bottom {
			bottom: 20px;
		}
	</style>
</head>

<body>
	<div class="container-fluid">
		<h2 class="text-center" style="color: #d9534f;">Rekap Transaksi Pengolahan Komponen Darah</h2>

		<form method="POST" action="">
			<div class="row">
				<div class="col-md-2">
					<label>Dari:</label>
					<input type="text" id="tanggal_awal" name="dari" class="form-control" required>
				</div>
				<div class="col-md-2">
					<label>Sampai:</label>
					<input type="text" id="tanggal_akhir" name="sampai" class="form-control" required>
				</div>
				<div class="col-md-2">
					<label>Shift:</label>
					<select name="shift" class="form-control">
						<option value="">Semua</option>
						<option value="I">PAGI</option>
						<option value="II">SORE</option>
						<option value="III">MALAM 1</option>
						<option value="IV">MALAM 2</option>
					</select>
				</div>
				<div class="col-md-2">
					<label>Komponen:</label>
					<select name="komponen" class="form-control">
						<option value="">Semua</option>
						<?php
						include 'config/dbi_connect.php';

						$result = mysqli_query($dbi, "SELECT DISTINCT Nama FROM produk");
						while ($row = mysqli_fetch_assoc($result)) {
							echo "<option>" . $row['Nama'] . "</option>";
						}
						mysqli_close($dbi);
						?>
					</select>
				</div>
				<div class="col-md-2">
					<label>Nama Petugas:</label>
					<input type="text" name="petugas" id="petugas" class="form-control" autocomplete="off">
				</div>
			</div>
			<br>
			<button type="submit" class="btn btn-danger">Tampilkan Data</button>
		</form>

		<br>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th rowspan="2" class="text-center align-middle">No</th>
					<th colspan="11" class="text-center align-middle">Data Pengolahan</th>
					<th colspan="3" class="text-center align-middle">Metode</th>
				</tr>
				<tr>
					<th>Tanggal</th>
					<th>No. Kantong</th>
					<th>Jenis</th>
					<th>ABO (Rh)</th>
					<th>Komponen</th>
					<th>Volume</th>
					<th>Tgl Aftap</th>
					<th>Tgl Kedaluwarsa</th>
					<th>Tgl Periksa</th>
					<th>Status</th>
					<th>Petugas</th>
					<th>Shift</th>
					<th>Mesin</th>
					<th>No. Seri</th>
					<th>Metode</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['dari']) && !empty($_POST['sampai'])) {
					include 'config/dbi_connect.php';

					$tgl_awal = isset($_POST['dari']) ? $_POST['dari'] : date('Y-m-d');
					$tgl_akhir = isset($_POST['sampai']) ? $_POST['sampai'] : date('Y-m-d');
					$shift = !empty($_POST['shift']) ? "AND shift='" . $_POST['shift'] . "'" : "";
					$komponen = !empty($_POST['produk']) ? "AND Produk='" . $_POST['komponen'] . "'" : "";
					$petugas = !empty($_POST['petugas']) ? "AND petugas LIKE '%" . $_POST['petugas'] . "%'" : "";

					$query = "SELECT * FROM dpengolahan WHERE DATE(tgl) BETWEEN '$tgl_awal' AND '$tgl_akhir' $shift $komponen $petugas ORDER BY tgl ASC";
					$result = mysqli_query($dbi, $query);

					$no = 1;
					while ($row = mysqli_fetch_assoc($result)) {
						$selSK = "SELECT `Status`, `jenis`, `tgl_Aftap`, `kadaluwarsa`, `tglPeriksa`, `volume` FROM stokkantong WHERE `NoKantong` = '" . $row['noKantong'] . "'";
						$queSK = mysqli_query($dbi, $selSK);
						$datSK = mysqli_fetch_assoc($queSK);

						switch ($datSK['jenis']) {
							case '1':
								$jenis = 'Single';
								break;
							case '2':
								$jenis = 'Double';
								break;
							case '3':
								$jenis = 'Triple';
								break;
							case '4':
								$jenis = 'Quadruple';
								break;
							case '6':
								$jenis = 'Pediatrik';
								break;
							default:
								$jenis = 'Tidak Diketahui';
								break;
						}

						switch ($datSK['Status']) {
							case '0':
								$statSK = 'Kosong/diLogistik';
								break;
							case '1':
								$statSK = 'Baru Isi/Karantina';
								break;
							case '2':
								$statSK = 'Sehat';
								break;
							case '3':
								$statSK = 'Keluar';
								break;
							case '4':
								$statSK = 'Rusak';
								break;
							case '5':
								$statSK = 'Rusak-Gagal';
								break;
							case '6':
								$statSK = 'Musnah';
								break;
							case '7':
								$statSK = 'Reaktif';
								break;
							default:
								$statSK = 'Tidak Diketahui';
								break;
						}
						echo "<tr class='text-center'>
                                <td>{$no}</td>
                                <td>{$row['tgl']}</td>
                                <td>{$row['noKantong']}</td>
                                <td>{$jenis}</td>
								<td>$row[goldarah] ($row[rhesus])</td>
                                <td>{$row['Produk']}</td>
								<td>" . number_format($datSK['volume'], 0) . " ml</td>
                                <td>{$datSK['tgl_Aftap']}</td>
                                <td>{$datSK['kadaluwarsa']}</td>
                                <td>{$datSK['tglPeriksa']}</td>
                                <td>{$statSK}</td>
                                <td>{$row['petugas']}</td>
                                <td>{$row['shift']}</td>
                                <td>{$row['aPutar']}</td>
                                <td>{$row['aPisah']}</td>
                                <td>{$row['metode']}</td>
                        </tr>";
						$no++;
					}
				}
				?>
			</tbody>
		</table>
	</div>

	<div style="margin: 20px;">
		<form name=xls method=post action=modul/rincian_pembuatan_komponen_xls.php>
			<input type=hidden name=tgl_awal value='<?= $tgl_awal ?>'>
			<input type=hidden name=tgl_akhir value='<?= $tgl_akhir ?>'>
			<input type=hidden name=shift2 value='<?= $shift ?>'>
			<input type=hidden name=komponen2 value='<?= $komponen ?>'>
			<input type=hidden name=petugas2 value='<?= $petugas ?>'>
			<input type=submit name=submit2 class="btn btn-success" value='Print Rekap Komponen (.XLS)'>
		</form>
	</div>

	<div class="scroll-top" onclick="window.scrollTo(0, 0);">
		<span class="glyphicon glyphicon-chevron-up"></span>
	</div>
	<div class="scroll-bottom" onclick="window.scrollTo(0, document.body.scrollHeight);">
		<span class="glyphicon glyphicon-chevron-down"></span>
	</div>

	<script>
		$(document).ready(function() {
			$("#tanggal_awal, #tanggal_akhir").datepicker({
				dateFormat: "yy-mm-dd",
				onSelect: function() {
					$(this).blur();
				}
			});
		});
	</script>
</body>

</html>