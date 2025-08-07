<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript">
	jQuery(document).ready(function () {
		document.getElementById('terima').focus();
		$('#instansi').autocomplete({ source: 'modul/suggest_zipnama.php', minLength: 2 });
	});
</script>
<?php
require_once("modul/background_process.php");
include('config/dbi_connect.php');

$namauser = $_SESSION['namauser'];
$today = date('Y-m-d');
$today1 = $today;
$src_nomorf = "";
$src_ambil = "";
$src_status = "";
$src_shift = "";
$src_ktg = "";
$src_drh = "";
$src_jk = "";
$hasil = "";
$src_rh = "";
$src_ds = "";
$src_baru = "";

if (isset($_POST['minta1'])) {
	$today = $_POST['minta1'];
	$today1 = $today;
}
if (!empty($_POST['minta2']))
	$today1 = $_POST['minta2'];
if (!empty($_POST['hasil']))
	$hasil = $_POST['hasil'];
if (!empty($_POST['nomorf']))
	$src_nomorf = $_POST['nomorf'];
if (!empty($_POST['gol_status']))
	$src_status = $_POST['gol_status'];
if (!empty($_POST['gol_ambil']))
	$src_ambil = $_POST['gol_ambil'];
if (!empty($_POST['gol_shift']))
	$src_shift = $_POST['gol_shift'];
if (!empty($_POST['gol_ktg']))
	$src_ktg = $_POST['gol_ktg'];
if (!empty($_POST['gol_drh']))
	$src_drh = $_POST['gol_drh'];
if (!empty($_POST['gol_jk']))
	$src_jk = $_POST['gol_jk'];
if (!empty($_POST['gol_rh']))
	$src_rh = $_POST['gol_rh'];
if (!empty($_POST['ds']))
	$src_ds = $_POST['ds'];
if (!empty($_POST['baru']))
	$src_baru = $_POST['baru'];

if (isset($_POST['terima'])) {
	$no_kantong = mysqli_real_escape_string($dbi, $_POST['terima']);
	$ck = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT Status,sah FROM stokkantong WHERE noKantong='$no_kantong' and Status='1' and (sah='0' or sah is null or sah='')"));
	$cek1 = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT * FROM stokkantong WHERE nokantong='$no_kantong'"));
	$cek2 = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT * FROM htransaksi WHERE nokantong='$no_kantong'"));
	$pengesahan = mysqli_query($dbi, "insert into pengesahan (nokantong,tgl,ygmenyerahkan,jns,ket,shift) value ('$no_kantong','$today','$namauser','$cek1[jenis]','$cek2[Pengambilan]','$cek2[shift]')");

	if ($ck['Status'] == '1') {
		$updatektg = mysqli_query($dbi, "update stokkantong set sah='1' WHERE noKantong='$no_kantong'");
		//Eksekusi SMS
		//---Minta Id pendonor untuk set data pendonor----
		$pendonor = mysqli_query($dbi, "SELECT kodependonor FROM stokkantong WHERE nokantong='$no_kantong'");
		$datapendonor = mysqli_fetch_assoc($pendonor);
		$idpendonor = $datapendonor['kodependonor'];
		//---End Minta Id pendonor untuk set data pendonor----
		//kirim ucapan terimakasih
		$dk = mysqli_query($dbi, "SELECT nama,Jk,Status,telp2 FROM pendonor WHERE Kode='$idpendonor' and LENGTH(telp2)>9");
		if (mysqli_num_rows($dk) == 1) {
			$dk1 = mysqli_fetch_assoc($dk);
			if ($dk1['Jk'] == '0' and $dk1['Status'] == '0')
				$sapa = 'Bpk';
			if ($dk1['Jk'] == '0' and $dk1['Status'] == '1')
				$sapa = 'Sdr';
			if ($dk1['Jk'] == '1' and $dk1['Status'] == '0')
				$sapa = 'Ibu';
			if ($dk1['Jk'] == '1' and $dk1['Status'] == '1')
				$sapa = 'Sdri';
			$ud = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT pesan FROM sms_setting WHERE id='3'"));
			$telp = $dk1['telp2'];
			$pesan = 'Yth. ' . $sapa . '. ' . $dk1['nama'] . ', ' . $ud['pesan'];
			$kirim = mysqli_query($dbi, "insert into sms.outbox (DestinationNumber,TextDecoded,CreatorID) 
                                            values ('$telp','$pesan','1')");
		}
		// end ucapan

		echo "Darah dengan NoKantong<b> $no_kantong </b>Telah Berhasil disahkan";
		function backgroundPost($url)
		{
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_exec($ch);
			curl_close($ch);
		}
		backgroundPost('http://localhost/simudda/modul/background_up_karantina.php');
	} else {
		echo "NoKantong<b> $no_kantong </b> TIDAK DITEMUKAN Atau Telah disahkan sebelumnya, silahkan Check Kantong melalui menu CHECK STOK";
	}
}
/*
$today=date('Y-m-d');
$today1=$today;
if (isset($_POST[terima1])) {$today=$_POST[terima1];$today1=$today;}
if ($_POST[terima2]!='') $today1=$_POST[terima2];*/
$perbln = substr($today, 5, 2);
$pertgl = substr($today, 8, 2);
$perthn = substr($today, 0, 4);
$perbln1 = substr($today1, 5, 2);
$pertgl1 = substr($today1, 8, 2);
$perthn1 = substr($today1, 0, 4);
?>
<h1>RINCIAN TRANSAKSI DONOR</h1>
<form method=post>
	TANGGAL <input type=text name=minta1 id=datepicker size=10 value=<?php echo $today ?>>
	S/D <input type=text name=minta2 id=datepicker1 size=10 value=<?php echo $today1 ?>>
	INSTANSI <input type=text name=nomorf id=instansi size=10 value=<?php echo $src_nomorf ?>><br>
	STATUS <select name="gol_status">
		<option value="">-SEMUA-</option>
		<option value="0">BERHASIL</option>
		<option value="1">BATAL</option>
		<option value="2">GAGAL</option>
		<option value="3">LOLOS MCU</option>
		<option value="4">GAGAL MCU</option>
	</select>

	TEMPAT<SELECT name="hasil">
		<option value="">-SEMUA-</option>
		<option value="0">DALAM GEDUNG</option>
		<option value="M">MOBIL UNIT</option>
	</SELECT>
	CARA AMBIL<SELECT name="gol_ambil">
		<option value="">-SEMUA-</option>
		<option value="0">BIASA</option>
		<option value="1">TROMBOFERESIS</option>
		<option value="2">LEUKAFERESIS</option>
		<option value="3">PLASMAFERESIS</option>
		<option value="4">ERITOFERESIS</option>
	</SELECT>
	SHIFT<SELECT name="gol_shift">
		<option value="">-SEMUA-</option>
		<option value="1">SHIFT I</option>
		<option value="2">SHIFT II</option>
		<option value="3">SHIFT III</option>
		<option value="4">SHIFT IV</option>
	</SELECT>

	KANTONG<SELECT name="gol_ktg" id="jenis" onchange="viewjenis()">

		<option value="">-SEMUA-</option>
		<option value="1">SINGLE</option>
		<option value="2">DOUBLE</option>
		<option value="3">TRIPLE</option>
		<option value="4">QUADRUPLE</option>
		<option value="6">PEDIATRIK</option>
	</SELECT>
	<!--list quadriple 050518-->
	&nbsp;&nbsp;
	<span id="bt" style="display: none">
		JENIS :
		<SELECT name="metoda" id="metoda">
			<option value="" selected>SEMUA</option>
			<!--							<option value="BS">Biasa</option>-->
			<option value="TTB">TOP & TOP (Biasa)</option>
			<option value="TTF">TOP & TOP (Filter)</option>
			<option value="TBB">TOP & BOTTOM (Biasa)</option>
			<option value="TBF">TOP & BOTTOM (Filter)</option>
			<!--							<option value="FT">FILTER</option>-->
		</SELECT>
	</span>
	<br>
	GOL DARAH<SELECT name="gol_drh">
		<option value="">-SEMUA-</option>
		<option value="A">A</option>
		<option value="B">B</option>
		<option value="O">O</option>
		<option value="AB">AB</option>
	</SELECT>
	Rh<SELECT name="gol_rh">
		<option value="">-SEMUA-</option>
		<option value="+">POS</option>
		<option value="-">NEG</option>

	</SELECT>

	JK<SELECT name="gol_jk">
		<option value="">-SEMUA-</option>
		<option value="0">PRIA</option>
		<option value="1">WANITA</option>

	</SELECT>
	DS/DP<SELECT name="ds">
		<option value="">-SEMUA-</option>
		<option value="0">DS</option>
		<option value="1">DP</option>

	</SELECT>
	BARU/ULANG<SELECT name="baru">
		<option value="">-SEMUA-</option>
		<option value="0">Baru</option>
		<option value="1">Ulang</option>

	</SELECT>
	<input type="submit" name="submit" value="Lihat" class="swn_button_blue"><br>

</form>
<?php

$whereStatus = "";

if (isset($_POST['gol_status']) && $_POST['gol_status'] !== "") {
	$src_status = $_POST['gol_status'];

	switch ($src_status) {
		case "0":
			$whereStatus = " AND COALESCE(Pengambilan , '') = '0' ";
			break;
		case "1":
			$whereStatus = " AND COALESCE(Pengambilan , '') = '1' ";
			break;
		case "2":
			$whereStatus = " AND COALESCE(Pengambilan , '') = '2' OR jumHB IN ('2','3','4')) ";
			break;
		case "3":
			$whereStatus = " AND COALESCE(Pengambilan , '') = '3' AND jumHB = '1' ";
			break;
		case "4":
			$whereStatus = " AND jumHB IN ('2','3','4') ";
			break;
	}
}

//TEST QUERY
switch (isset($_POST['gol_drh']) ? $_POST['gol_drh'] : '') {
	case '':
		$transaksipermintaan = mysqli_query($dbi, "SELECT * FROM htransaksi WHERE 
					CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' 
					$whereStatus 
					and COALESCE(tempat, '') like '%$hasil%' 
					and COALESCE(shift,'') like '%$src_shift%' 
					and COALESCE(caraambil,'') like '%$src_ambil%' 
					and COALESCE(jeniskantong,'') like '%$src_ktg%' 
					and COALESCE(gol_darah,'') like '%$src_drh%' 
					and COALESCE(jk,'') like '%$src_jk%' 
					and COALESCE(Instansi,'') like '%$src_nomorf%' 
					and COALESCE(rhesus,'') like '%$src_rh%' 
					and COALESCE(JenisDonor,'') like '%$src_ds%' 
					and COALESCE(donorbaru,'') like '%$src_baru%' order by NoTrans ASC  ");
		break;
	default:
		$transaksipermintaan = mysqli_query($dbi, "SELECT * FROM htransaksi WHERE 
					CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' 
					$whereStatus 
					and COALESCE(tempat,'') like '%$hasil%' 
					and COALESCE(shift,'') like '%$src_shift%' 
					and COALESCE(caraambil,'') like '%$src_ambil%' 
					and COALESCE(jeniskantong,'') like '%$src_ktg%' 
					and COALESCE(gol_darah,'') ='$src_drh' 
					and COALESCE(jk,'') like '%$src_jk%' 
					and COALESCE(Instansi,'') like '%$src_nomorf%' 
					and COALESCE(rhesus,'') like '%$src_rh%' 
					and COALESCE(JenisDonor,'') like '%$src_ds%' 
					and COALESCE(donorbaru,'') like '%$src_baru%' order by Tgl, NoTrans ASC  ");
}


/*$transaksipermintaan=mysqli_query($dbi, "SELECT * FROM htransaksi WHERE CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah='$src_drh' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' order by NoTrans ASC  ");*/
?>
<h1 class="table">Rincian Transaksi Donor dari tgl : <?php echo $pertgl ?>-<?php echo $perbln ?>-<?php echo $perthn ?>
	s/d
	<?php echo $pertgl1 ?>-<?php echo $perbln1 ?>-<?php echo $perthn1 ?>
	<?php
	$countp = mysqli_num_rows($transaksipermintaan);
	echo ", Jumlah :  ";
	echo "<b>";
	echo $countp;
	echo "</b>";
	echo " data";
	?>
</h1>

<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="100%">
	<tr style="background-color:#FF0000; font-size:11px; color:#FFFFFF; font-family:Verdana;"
		onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
		<!--th colspan=12><b>Total = <?php echo $TRec ?> Kantong</b></th></tr><tr class="field"-->
		<td rowspan='2' align="center">No</td>
		<td rowspan='2' align="center">NoTrans</td>
		<td rowspan='2' align="center">Tanggal</td>
		<td colspan='11' align="center">Pendonor</td>
		<td colspan='12' align="center">Aftap</td>
		<td colspan='5'>Piagam</td>
		<td colspan='5' align="center">Petugas</td>

	</tr>
	<tr style="background-color:#FF0000; font-size:11px; color:#FFFFFF; font-family:Verdana;"
		onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
		<td align="center">ID</td>
		<td align="center">Nama</td>
		<td align="center">Alamat</td>
		<td align="center">HP</td>
		<td align="center">Umur</td>
		<td align="center">Gol<br>(Rh)</td>
		<td align="center">JK</td>
		<td align="center">DS<br>DP</td>
		<td align="center">Baru<br>Ulang</td>
		<td align="center">Donor<br>Ke-</td>
		<td align="center">Jam<br>Antri</td>

		<td align="center">Jenis</td>
		<td align="center">No<br>Kantong</td>
		<td align="center">Penge-<br>sahan</td>
		<td align="center">Jam<br>Entry</td>
		<td align="center">Durasi<br>Pengambilan</td>
		<td align="center">Status</td>
		<td align="center">Keterangan<br>Batal</td>
		<td align="center">Cara Ambil</td>
		<td align="center">CC</td>
		<td align="center">Shift</td>
		<td align="center">DG<br>MU</td>
		<td align="center">Instansi</td>

		<td align="center">10x</td>
		<td align="center">25x</td>
		<td align="center">50x</td>
		<td align="center">75x</td>
		<td align="center">100x</td>

		<td align="center">Dokter</td>
		<td align="center">Tensi</td>
		<td align="center">Hb</td>
		<td align="center">Aftap</td>
		<td align="center">Input</td>


	</tr>


	<?php
	$no = 1;
	while ($datatransaksipermintaan = mysqli_fetch_array($transaksipermintaan)) {
		?>


		<tr style="background-color:#FFEFD5; font-size:11px; color:#000000; font-family:Verdana;"
			onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td align="center"><?php echo $no ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['NoTrans'] ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['Tgl'] ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['KodePendonor'] ?></td>


			<?php
			$kantong0 = mysqli_query($dbi, "SELECT * FROM stokkantong WHERE noKantong='$datatransaksipermintaan[NoKantong]'");
			$kantong = mysqli_fetch_array($kantong0);
			$pendonor0 = mysqli_query($dbi, "SELECT * FROM pendonor WHERE Kode='$datatransaksipermintaan[KodePendonor]'");
			$pendonor = mysqli_fetch_array($pendonor0);
			$jamantri = substr($datatransaksipermintaan['Tgl'], 11);
			$jamaftap = substr($kantong['tgl_Aftap'], 11);
			if ($datatransaksipermintaan['jk'] == 0)
				$jk = 'Pria';
			if ($datatransaksipermintaan['jk'] == 1)
				$jk = 'Wanita';
			$peng = 'Antri';
			if ($datatransaksipermintaan['jumHB'] == '1')
				$peng = 'Lolos MCU';
			if ($datatransaksipermintaan['jumHB'] == '2')
				$peng = 'Gagal MCU';
			if ($datatransaksipermintaan['jumHB'] == '3')
				$peng = 'Gagal MCU';
			if ($datatransaksipermintaan['jumHB'] == '4')
				$peng = 'Gagal MCU';
			if ($datatransaksipermintaan['Pengambilan'] == '0')
				$peng = 'Berhasil';
			if ($datatransaksipermintaan['Pengambilan'] == '2')
				$peng = 'Gagal';
			if ($datatransaksipermintaan['Pengambilan'] == '1')
				$peng = 'Batal';

			if ($datatransaksipermintaan['caraAmbil'] == '0')
				$caraambil = 'Biasa';
			if ($datatransaksipermintaan['caraAmbil'] == '1')
				$caraambil = 'Tromboferesis';
			if ($datatransaksipermintaan['caraAmbil'] == '2')
				$caraambil = 'Leukaferesis';
			if ($datatransaksipermintaan['caraAmbil'] == '3')
				$caraambil = 'Plasmaferesis';
			if ($datatransaksipermintaan['caraAmbil'] == '4')
				$caraambil = 'Eritoferesis';

			if ($datatransaksipermintaan['JenisDonor'] == '0')
				$ds = 'DS';
			if ($datatransaksipermintaan['JenisDonor'] == '1')
				$ds = 'DP';
			if ($datatransaksipermintaan['JenisDonor'] == '2')
				$ds = 'Autologus';

			if ($datatransaksipermintaan['donorbaru'] == '0')
				$baru = 'Baru';
			if ($datatransaksipermintaan['donorbaru'] == '1')
				$baru = 'Ulang';

			$ketstatus = '-';
			if ($datatransaksipermintaan['ketBatal'] == '0')
				$ketstatus = 'Tensi Rendah ' . $datatransaksipermintaan['tensi'];
			if ($datatransaksipermintaan['ketBatal'] == '1')
				$ketstatus = 'Tensi Tinggi ' . $datatransaksipermintaan['tensi'];
			if ($datatransaksipermintaan['ketBatal'] == '2')
				$ketstatus = 'HB Rendah ' . $datatransaksipermintaan['Hb'];
			if ($datatransaksipermintaan['ketBatal'] == '3')
				$ketstatus = 'HB Melayang';
			if ($datatransaksipermintaan['ketBatal'] == '4')
				$ketstatus = 'HB Tinggi ' . $datatransaksipermintaan['Hb'];
			if ($datatransaksipermintaan['ketBatal'] == '5')
				$ketstatus = 'BB Kurang';
			if ($datatransaksipermintaan['ketBatal'] == '6')
				$ketstatus = 'Habis Minum Obat';
			if ($datatransaksipermintaan['ketBatal'] == '7')
				$ketstatus = 'Riwayat Bepergian';
			if ($datatransaksipermintaan['ketBatal'] == '8')
				$ketstatus = 'Kondisi Medis Lain';
			if ($datatransaksipermintaan['ketBatal'] == '9')
				$ketstatus = 'Perilaku Berisiko';
			if ($datatransaksipermintaan['ketBatal'] == '10')
				$ketstatus = 'Alasan Lain';

			switch ($kantong['metoda']) {
				//            case "BS":  $metkantong ="BIASA";        break;
//            case "FT":  $metkantong ="FILTER";       break;
				case "TTB":
					$metkantong = "TOP & TOP (Biasa)";
					break;
				case "TTF":
					$metkantong = "TOP & TOP (Filter)";
					break;
				case "TBB":
					$metkantong = "TOP & BOTTOM (Biasa)";
					break;
				case "TBF":
					$metkantong = "TOP & BOTTOM (Filter)";
					break;
			}

			switch ($datatransaksipermintaan['jeniskantong']) {
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
					$jenis = 'Quadruple (' . $metkantong . ')';
					break;
				case '6':
					$jenis = 'Pediatrik';
					break;
				default:
					$jenis = '';
			}

			if ($kantong['sah'] == '0')
				$sah1 = 'Belum';
			if ($kantong['sah'] == '1')
				$sah1 = 'Sudah';
			if ($datatransaksipermintaan['NoKantong'] == NULL)
				$sah1 = '-';
			?>

			<td align="left"><?php echo $pendonor['Nama'] ?></td>
			<td align="center"><?php echo $pendonor['Alamat'] ?></td>
			<td align="center"><?php echo $pendonor['telp2'] ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['umur'] ?></td>
			<td align="center">
				<?php echo $datatransaksipermintaan['gol_darah'] ?> 	<?php echo $datatransaksipermintaan['rhesus'] ?>
			</td>
			<td align="center"><?php echo $jk ?></td>
			<td align="center"><?php echo $ds ?></td>
			<td align="center"><?php echo $baru ?></td>
			<td align="center"><?php echo $pendonor['jumDonor'] ?></td>
			<td align="center"><?php echo $jamantri ?></td>
			<td align="center"><?php echo $jenis ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['NoKantong'] ?></td>
			<td align="center"><?php echo $sah1 ?></td>
			<td align="center"><?php if ($sah1 == "-") {
				echo "-";
			} else {
				echo $jamaftap;
			} ?></td>
			<td align="center"><?php if ($sah1 == "-") {
				echo "-";
			} else {
				echo $kantong['lama_pengambilan'] . " menit";
			} ?></td>
			<td align="center"><?php echo $peng ?></td>
			<td align="center"><?php echo $ketstatus ?></td>
			<td align="center"><?php echo $caraambil ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['volumekantong'] ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['shift'] ?></td>
			<?php
			if ($datatransaksipermintaan['tempat'] == 'M')
				$tempat1 = 'MU';
			if ($datatransaksipermintaan['tempat'] != 'M')
				$tempat1 = 'DG';
			?>

			<td align="center"><?php echo $tempat1 ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['Instansi'] ?></td>


			<?php
			$p10 = 'Sdh';
			if ($pendonor['jumDonor'] > 9 and $pendonor['p10'] == 0)
				$p10 = 'Blm';
			if ($pendonor['jumDonor'] < 10)
				$p10 = '-';
			$p25 = 'Sdh';
			if ($pendonor['jumDonor'] > 24 and $pendonor['p25'] == '0')
				$p25 = 'Blm';
			if ($pendonor['jumDonor'] < 25)
				$p25 = '-';
			$p50 = 'Sdh';
			if ($pendonor['jumDonor'] > 49 and $pendonor['p50'] == '0')
				$p50 = 'Blm';
			if ($pendonor['jumDonor'] < 50)
				$p50 = '-';
			$p75 = 'Sdh';
			if ($pendonor['jumDonor'] > 74 and $pendonor['p75'] == '0')
				$p75 = 'Blm';
			if ($pendonor['jumDonor'] < 75)
				$p75 = '-';
			$p100 = 'Sdh';
			if ($pendonor['jumDonor'] > 99 and $pendonor['p100'] == '0')
				$p100 = 'Blm';
			if ($pendonor['jumDonor'] < 100)
				$p100 = '-';
			?>
			<td class=input align="right"><?php echo $p10 ?></td>
			<td class=input align="right"><?php echo $p25 ?></td>
			<td class=input align="right"><?php echo $p50 ?></td>
			<td class=input align="right"><?php echo $p75 ?></td>
			<td class=input align="right"><?php echo $p100 ?></td>



			<?php
			$dokter = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT Nama FROM dokter_periksa WHERE kode='$datatransaksipermintaan[NamaDokter]'"));
			?>
			<td class=input><?php echo $dokter['Nama'] ?></td>

			<td align="center"><?php echo $datatransaksipermintaan['petugasTensi'] ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['petugasHB'] ?></td>
			<!--? 	
	$kantong1=mysqli_query($dbi, "SELECT * FROM stokkantong WHERE NoKantong='$datatransaksipermintaan[NoKantong]'");
	$ambilkantong1=mysqli_fetch_array($kantong1);
	
	?-->
			<td align="center"><?php echo $datatransaksipermintaan['petugas'] ?></td>
			<td align="center"><?php echo $datatransaksipermintaan['user'] ?></td>


		</tr>
		<?php $no++;
	} ?>
</table>

<br>

<tr>
	<td>
		<form name=xls method=post action=modul/rekap_transaksi_donor_xls.php>
			<input type=hidden name=today value='<?php echo $today ?>'>
			<input type=hidden name=today1 value='<?php echo $today1 ?>'>
			<input type=hidden name=instansi value='<?php echo $src_nomorf ?>'>
			<input type=hidden name=status value='<?php echo $src_status ?>'>
			<input type=hidden name=ambil value='<?php echo $src_ambil ?>'>
			<input type=hidden name=hasil value='<?php echo $hasil ?>'>
			<input type=hidden name=shift value='<?php echo $src_shift ?>'>
			<input type=hidden name=ktg value='<?php echo $src_ktg ?>'>
			<input type=hidden name=drh value='<?php echo $src_drh ?>'>
			<input type=hidden name=jk value='<?php echo $srcr_jk ?>'>
			<input type=hidden name=rh value='<?php echo $src_rh ?>'>
			<input type=hidden name=ds value='<?php echo $src_ds ?>'>
			<input type=hidden name=baru value='<?php echo $src_baru ?>'>
			<input type=hidden name=namauser value='<?php echo $namauser ?>'>
			<input type=submit name=submit2 value='Print Rincian Transaksi Donor Lengkap (.XLS)'>
		</form>
	</td>
	<td>
		<form name=xls method=post action=modul/rekap_transaksi_donor_xls1.php>
			<input type=hidden name=today value='<?php echo $today ?>'>
			<input type=hidden name=today1 value='<?php echo $today1 ?>'>
			<input type=hidden name=instansi value='<?php echo $src_nomorf ?>'>
			<input type=hidden name=status value='<?php echo $src_status ?>'>
			<input type=hidden name=ambil value='<?php echo $src_ambil ?>'>
			<input type=hidden name=hasil value='<?php echo $hasil ?>'>
			<input type=hidden name=shift value='<?php echo $src_shift ?>'>
			<input type=hidden name=ktg value='<?php echo $src_ktg ?>'>
			<input type=hidden name=drh value='<?php echo $src_drh ?>'>
			<input type=hidden name=jk value='<?php echo $srcr_jk ?>'>
			<input type=hidden name=rh value='<?php echo $src_rh ?>'>
			<input type=hidden name=ds value='<?php echo $src_ds ?>'>
			<input type=hidden name=baru value='<?php echo $src_baru ?>'>
			<input type=hidden name=namauser value='<?php echo $namauser ?>'>
			<input type=submit name=submit3 value='Print Rincian Transaksi Donor Kirim Kebagian (.XLS)'>
		</form>
</tr>

<script>
	function viewjenis() {
		var jenis = document.getElementById("jenis").value;
		if (jenis == '4') {
			document.getElementById("bt").style.display = "inline";
		} else {
			document.getElementById("bt").style.display = "none";
		}
	}
</script>

<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;">
</DIV>
