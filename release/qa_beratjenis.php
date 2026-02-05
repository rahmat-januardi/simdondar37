<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/disable_enter.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<style>
	/* CSS Normal (Layar) */
	.print-value {
		display: none;
		/* Sembunyikan span di layar */
	}

	/* CSS untuk Print */
	@media print {
		body * {
			visibility: hidden;
		}

		.print-area,
		.print-area * {
			visibility: visible;
		}

		.print-area {
			position: absolute;
			left: 0;
			top: 0;
			width: 100%;
		}

		/* Sembunyikan elemen tidak perlu */
		.swn_button_blue,
		form input[type="submit"],
		form input[type="text"],
		form input[type="hidden"],
		a[href*="input_qa"],
		a[href*="cetak_density"],
		.no-print {
			display: none !important;
		}

		/* Tampilkan value statis */
		.print-value {
			display: inline;
			/* Atau block jika perlu */
			text-align: right;
			width: 100%;
		}

		table.list {
			width: 100%;
			border-collapse: collapse;
		}

		table.list th,
		table.list td {
			border: 1px solid #000;
			padding: 6px;
			font-size: 11pt;
			text-align: center;
			/* Default center, override untuk left/right */
		}

		table.list td.input {
			text-align: right;
			/* Align right untuk value numerik */
		}

		table.list td[align="left"] {
			text-align: left;
		}

		h1 {
			font-size: 16pt;
			margin-bottom: 10px;
			text-align: center;
		}

		.print-date {
			font-size: 10pt;
			text-align: right;
			margin: 10px 0;
		}
	}
</style>

<script>
	$(".angka").on("keyup", function() {
		var valid = /^\d{0,15}(\.\d{0,2})?$/.test(this.value),
			val = this.value;
		if (!valid) {
			this.value = val.substring(0, val.length - 1);
		}
	});
</script>

<?php
include('clogin.php');
include('config/dbi_connect.php');
session_start();
$namauser = $_SESSION['namauser'];

if ($_POST['submit']) {
	$beratjenis = $_POST['beratjenis'];
	$suhu = $_POST['suhu'];
	$hari = $_POST['hari'];
	$jam = $_POST['jam'];
	$volstandar = $_POST['volume'];
	$volmin = $_POST['volmin'];
	$volmax = $_POST['volmax'];
	$nama = $_POST['nama'];
	$lengkap = $_POST['namalengkap']; // Perbaiki nama POST
	$nomor = $_POST['nomor'];
	$olah = $_POST['olah']; // Ubah dari 'oleh' ke 'olah' untuk konsisten
	$aftap = $_POST['aftap'];
	for ($i = 0; $i < count($nomor); $i++) {
		$sql = "UPDATE produk set
            umurhari = '$hari[$i]',
            umurjam = '$jam[$i]',
            suhusimpan = '$suhu[$i]',
            beratjenis = '$beratjenis[$i]',
            volume = '$volstandar[$i]',
            vol_min = '$volmin[$i]',
            vol_max = '$volmax[$i]',
            waktu_pengolahan = '$olah[$i]',
            lama_pengambilan = '$aftap[$i]'
            where `no` = '$nomor[$i]';";
		$simpan = mysqli_query($dbi, $sql);
	}
	//=======Audit Trial====================================================================================
	$log_mdl = 'PROLIS';
	$log_aksi = 'Mengedit data berat jenis kantong';
	include_once "user_log.php";
	//=====================================================================================================
}

$sq = mysqli_query($dbi, "SELECT `Nama`, `no`, `kantongbaru`, `lengkap`, `umurhari`, `umurjam`, `suhusimpan`,
    `volume`, `beratjenis`, `vol_min`, `vol_max`, waktu_pengolahan, lama_pengambilan FROM `produk` order by `no`");
?>

<form name='transaksi' method='post' action=''>
	<div class="print-area">
		<h1>SETTING KOMPONEN DARAH</h1>
		<!-- <div class="print-date">Dicetak pada: <?= date('d-m-Y H:i') ?> oleh <?= $namauser ?></div> -->
		<table class="list" border="1" cellspacing="4" cellpadding="5" style="border-collapse:collapse">
			<tr class="field">
				<td align="center" rowspan="2">NO</td>
				<td align="center" rowspan="2">NAMA KOMPONEN DARAH</td>
				<td align="center" rowspan="2">SINGKATAN</td>
				<td align="center" rowspan="2">VOLUME <br>DEFAULT (ml)</td>
				<td align="center" rowspan="2">BERAT JENIS</td>
				<td align="center" rowspan="2">SUHU SIMPAN<br>(<sup>o</sup>C)</td>
				<td align="center" colspan="2">MASA KADALUARSA</td>
				<td align="center" colspan="2">VOLUME RELEASE (ml)</td>
				<td align="center" colspan="2">WAKTU</td>
			</tr>
			<tr class="field">
				<td align="center">HARI</td>
				<td align="center">JAM</td>
				<td align="center">MINIMAL</td>
				<td align="center">MAKSIMAL</td>
				<td align="center">AFTAP (menit)</td>
				<td align="center">PENGOLAHAN (Jam)</td>
			</tr>
			<?php
			$no = 0;
			while ($dtrans = mysqli_fetch_assoc($sq)) {
				$no++;
			?>
				<tr class='record'>
					<td align="center"><?= $dtrans['no'] ?><input type="hidden" name='nomor[]' value="<?= $dtrans['no'] ?>"></td>
					<td align="left"><?= $dtrans['lengkap'] ?><input type="hidden" name='namalengkap[]' value="<?= $dtrans['lengkap'] ?>"></td>
					<td align="left"><?= $dtrans['Nama'] ?><input type="hidden" name='nama[]' value="<?= $dtrans['Nama'] ?>"></td>
					<td class='input'><span class="print-value"><?= $dtrans['volume'] ?></span><input name='volume[]' type='text' size='7' value="<?= $dtrans['volume'] ?>" style='text-align:right'></td>
					<td class='input'><span class="print-value"><?= $dtrans['beratjenis'] ?></span><input name='beratjenis[]' type='text' size='7' value="<?= $dtrans['beratjenis'] ?>" style='text-align:right'></td>
					<td class='input'><span class="print-value"><?= $dtrans['suhusimpan'] ?></span><input name='suhu[]' type='text' size='7' value="<?= $dtrans['suhusimpan'] ?>" style='text-align:right'></td>
					<td class='input'><span class="print-value"><?= $dtrans['umurhari'] ?></span><input name='hari[]' type='text' size='5' value="<?= $dtrans['umurhari'] ?>" style='text-align:right'></td>
					<td class='input'><span class="print-value"><?= $dtrans['umurjam'] ?></span><input name='jam[]' type='text' size='5' value="<?= $dtrans['umurjam'] ?>" style='text-align:right'></td>
					<td class='input'><span class="print-value"><?= $dtrans['vol_min'] ?></span><input name='volmin[]' type='text' size='6' value="<?= $dtrans['vol_min'] ?>" style='text-align:right'></td>
					<td class='input'><span class="print-value"><?= $dtrans['vol_max'] ?></span><input name='volmax[]' type='text' size='6' value="<?= $dtrans['vol_max'] ?>" style='text-align:right'></td>
					<td class='input'><span class="print-value"><?= $dtrans['lama_pengambilan'] ?></span><input name='aftap[]' type='text' size='3' value="<?= $dtrans['lama_pengambilan'] ?>" style='text-align:right'></td>
					<td class='input'><span class="print-value"><?= $dtrans['waktu_pengolahan'] ?></span><input name='olah[]' type='text' size='3' value="<?= $dtrans['waktu_pengolahan'] ?>" style='text-align:right'></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<p class="no-print">Gunakan . (titik) untuk desimal</p>
	<br class="no-print">
	<input name="submit" type="submit" value="Simpan" class="swn_button_blue no-print">
	<a href="pmiqa.php?module=input_qa" class="swn_button_blue no-print">Kembali</a>
	<button type="button" onclick="window.print()" class="swn_button_blue no-print">Cetak</button>
</form>