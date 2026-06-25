<?
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_history_donor.xls");
header("Pragma: no-cache");
header("Expires: 0");
include('../config/db_connect.php');


$q = $_POST[q1];
$query = $_POST[queryx];
$query1 = $_POST[queryx2];
$cekal1 = $_POST[cekalx];
$instansi = $_POST[instansix];
$kendaraan = $_POST[kendaraanx];


?>
<!--h5 class="table">History Donor : <?= $pertgl ?> - <?= $perbln ?> - <?= $perthn ?> sampai Tgl: <?= $pertgl1 ?> - <?= $perbln1 ?> - <?= $perthn1 ?><br>
</h5-->

<table bgcolor="#000012" cellspacing="1" cellpadding="3">
	<tr bgcolor="#DDDD12">
		<th colspan=4><b>INFO DATA PENDONOR</b></th>
	</tr>
</table>
<table bgcolor="#000000" cellspacing="1" cellpadding="3">
	<tr bgcolor="#DDDDDD">
		<td rowspan='2'>Kode Pendonor</td>
		<td rowspan='2'>Nama Pendonor</td>
		<td rowspan='2'>Alamat</td>
		<td colspan='2' align="center">No. Telp</td>
		<td rowspan='2'>Gol (Rh) Drh</td>
		<td rowspan='2'>Jml Donor</td>
		<td rowspan='2'>Tgl Donor Kembali</td>
		<td rowspan='2'>Status</td>
	</tr>
	<tr bgcolor="#DDDDDD">
		<td>Handphone</td>
		<td>Rumah/Ktr</td>
	</tr>

	<?
	$trans1 = mysql_query("select * from pendonor where Kode='$q' ");
	while ($dtrans1 = mysql_fetch_assoc($trans1)):
	?>
		<?
		$cekal = 'OK';
		if ($cekal1[cekal] == '1') $cekal = 'Confirm';
		?>
		<tr bgcolor="#FFFFFF">
			<td><?= $dtrans1[Kode] ?></td>
			<td><?= $dtrans1[Nama] ?></td>
			<td><?= $dtrans1[Alamat] ?></td>
			<td><?= $dtrans1[telp2] ?></td>
			<td><?= $dtrans1[telp] ?></td>
			<td align="center"><?= $dtrans1[GolDarah] ?> (<?= $dtrans1[Rhesus] ?>)</td>
			<td align="center"><?= $dtrans1[jumDonor] ?> Kali</td>
			<td align="center"><?= $dtrans1[tglkembali] ?></td>
			<td align="center"><?= $cekal ?></td>
		</tr>
	<? endwhile; ?>
</table>

<br></br>
<table bgcolor="#000012" cellspacing="1" cellpadding="3">
	<tr bgcolor="#DDDD12">
		<th colspan=4><b>INFO HISTORY DONOR</b></th>
	</tr>

</table>

<table bgcolor="#000000" cellspacing="1" cellpadding="3">
	<tr bgcolor="#DDDDDD">
		<td rowspan='2'>No.</td>
		<td rowspan='2'>Tanggal</td>
		<td rowspan='2'>Donor Ke</td>
		<td rowspan='2'>BB</td>
		<td rowspan='2'>Tensi</td>
		<td rowspan='2'>Jenis</td>
		<td rowspan='2'>Tempat</td>
		<td rowspan='2'>Instansi</td>
		<td rowspan='2'>Nokantong</td>
		<td rowspan='2'>Status<br>Aftap</td>
		<td colspan='4' align="center">petugas</td>
	</tr>
	<tr bgcolor="#DDDDDD">
		<td>Input</td>
		<td>Tensi</td>
		<td>HB</td>
		<td>Aftap</td>
	</tr>
	<?
	$no = 1;
	//$trans=mysql_query("select * from htransaksi where KodePendonor='$q' order by Tgl ASC");
	$trans = mysql_query("SELECT Kodependonor,Tgl,Pengambilan,beratBadan,tensi,JenisDonor,tempat,Instansi,NoKantong,petugasHB,petugasTensi,user,petugas,donorke FROM htransaksi where KodePendonor='$q' 
		UNION
			SELECT Kodependonor,Tgl,Pengambilan,beratBadan,tensi,JenisDonor,tempat,Instansi,NoKantong,petugasHB,petugasTensi,user,petugas,'' FROM htransaksilama where KodePendonor='$q' order by Tgl ASC");
	while ($dtrans = mysql_fetch_assoc($trans)):

		$jenis = 'DS';
		if ($dtrans[JenisDonor] == '1') $jenis = 'DP';
		$tempat = 'DG';
		if ($dtrans[tempat] == 'M') $tempat = 'MU';


	?>

		<tr bgcolor="#FFFFFF">
			<td align="center"><?= $no++ ?></td>
			<td align="center"><?= $dtrans[Tgl] ?></td>
			<td align="center"><?= $dtrans[donorke] ?> Kali </td>
			<td align="center"><?= $dtrans[beratBadan] ?> kg</td>
			<td align="center"><?= $dtrans[tensi] ?></td>
			<td align="center"><?= $jenis ?></td>
			<td align="center"><?= $tempat ?></td>
			<td align="center"><?= $dtrans[Instansi] ?></td>
			<td align="center"><?= $dtrans[NoKantong] ?></td>
			<?
			//$pengambilan=mysql_fetch_assoc(mysql_query("select Pengambilan from htransaksi where KodePendonor='$q'"));
			if ($dtrans[Pengambilan] == '0') $pengambilan1 = "Berhasil";
			if ($dtrans[Pengambilan] == '2') $pengambilan1 = "Gagal Aftap";
			if ($dtrans[Pengambilan] == '1') $pengambilan1 = "Batal";
			?>
			<td align="center"><?= $pengambilan1 ?></td>
			<td align="center"><?= $dtrans[user] ?></td>
			<td align="center"><?= $dtrans[petugasHB] ?></td>
			<td align="center"><?= $dtrans[petugasTensi] ?></td>
			<td align="center"><?= $dtrans[petugas] ?></td>
		</tr>

	<? endwhile; ?>
</table>

<?php
if ($_POST[konseling] == "konseling") {
?>
	<br></br>
	<table bgcolor="#000012" cellspacing="1" cellpadding="3">
		<tr bgcolor="#DDDD12">
			<th colspan=4><b>INFO HISTORY KONSELING DONOR</b></th>
		</tr>
	</table>

	<table bgcolor="#000000" cellspacing="1" cellpadding="3">
		<tr bgcolor="#FAEBD7">
			<td>No.</td>
			<td>Tanggal</td>
			<td align="center">No. TRANSAKSI</td>
			<td align="center">PARAMETER</td>
			<td align="center">NILAI<br>TITER</td>
			<td align="center">TINDAKAN</td>
			<td align="center">KET</td>
			<td align="center">PETUGAS</td>
		</tr>
		<?
		$no = 1;
		$trans = mysql_query("select Tgl,notrans,parameter,nilai,hasil,ket,petugas from konseling where kodependonor='$q' order by Tgl");
		while ($dtrans = mysql_fetch_assoc($trans)):
			$notr = $dtrans[notrans];
			$pengambilan = 'Dirujuk';
			if ($dtrans[hasil] == '1') $pengambilan = 'Diberi Obat';
			if ($dtrans[hasil] == '2') $pengambilan = 'Konsul';
			$cara_ambil = 'HBsAg';
			if ($dtrans[parameter] == '1') $cara_ambil = 'HCV';
			if ($dtrans[parameter] == '2') $cara_ambil = 'HIV';
			if ($dtrans[parameter] == '3') $cara_ambil = 'SYPHILIS';
		?>
			<tr bgcolor="#FFFFFF">
				<td><?= $no++ ?></td>
				<td><?= $dtrans[Tgl] ?></td>
				<td align="center"><?= $dtrans[notrans] ?></td>
				<td align="center"><?= $cara_ambil ?></td>
				<td align="center"><?= $dtrans[nilai] ?></td>
				<td align="center"><?= $pengambilan ?></td>
				<td align="center"><?= $dtrans[ket] ?></td>
				<td align="left"><?= $dtrans[petugas] ?></td>

			</tr>
		<? endwhile; ?>
	</table>
<?
}
?>




<?
mysql_close();
?>