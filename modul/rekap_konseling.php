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

<?
include('config/db_connect.php');
$today = date('Y-m-d');
$today1 = $today;
$src_rs = "";
$src_lay = "";
$src_shift = "";
if (isset($_POST['minta1'])) {
	$today = $_POST['minta1'];
	$today1 = $today;
}
if ($_POST['minta2'] != '') $today1 = $_POST['minta2'];
if ($_POST['gol_status'] != '') $src_status = $_POST['gol_status'];
if ($_POST['hasil'] != '') $src_hasil = $_POST['hasil'];
if ($_POST['nomorf'] != '') $srcform = $_POST['nomorf'];

?>
<h1>REKAP TRANSAKSI KONSELING </h1>
<form method=post> Mulai:
	TANGGAL : <input type=text name=minta1 id=datepicker size=10 value=<?= $today ?>>
	S/D <input type=text name=minta2 id=datepicker1 size=10 value=<?= $today1 ?>><br>
	ID. DONOR <input type=text name=nomorf id=nomorf size=10 value=<?= $srcform ?>>

	PARAMETER<select name="gol_status">
		<option value="">SEMUA</option>
		<option value="0">HBsAg</option>
		<option value="1">HCV</option>
		<option value="2">HIV</option>
		<option value="3">SYPHILIS</option>
	</select>

	TINDAKAN
	<select name="hasil">
		<option value="">SEMUA</option>
		<option value="0">Dirujuk</option>
		<option value="1">Diberikan Obat</option>
		<option value="2">Konsul</option>
	</select>
	<input type="submit" name="submit" value="Lihat" class="swn_button_blue">
</form>
<?
// Menggunakan MIN(status) agar jika ada status 0 (Cabut Cekal), status 0 tersebut yang terambil
$transaksipermintaan = mysql_query("SELECT a.notrans, a.kodependonor, a.tgl, a.parameter, a.nilai, a.hasil, a.ket, a.petugas, 
b.Nama, b.Alamat, b.Jk, b.TempatLhr, b.TglLhr, 
c.min_status AS status_cekal 
FROM konseling AS a 
JOIN pendonor AS b ON b.Kode = a.kodependonor 
LEFT JOIN (
    SELECT kode_pendonor, MIN(status) AS min_status 
    FROM cekal 
    GROUP BY kode_pendonor
) AS c ON c.kode_pendonor = a.kodependonor 
WHERE CAST(a.tgl as date)>='$today' 
  AND CAST(a.tgl as date)<='$today1' 
  AND a.notrans LIKE '%$srcform%' 
  AND a.parameter LIKE '%$src_status%' 
  AND a.hasil LIKE '%$src_hasil%' 
ORDER BY a.tgl ASC");

$countp = mysql_num_rows($transaksipermintaan);
echo "<br><br>";
echo "Konseling yang sudah dilakukan kepada pendonor sebanyak :   ";
echo "<b>";
echo $countp;
echo "</b>";
echo " Kali";
?>

<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="100%">
	<tr style="background-color:#FF4356; font-size:11px; color:#FFFFFF; font-family:Verdana;"
		onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
		<td rowspan='2' align="center">No</td>
		<td rowspan='2' align="center">TGL.</td>
		<td rowspan='2' align="center">NoTrans</td>
		<td rowspan='2' align="center">ID DONOR</td>
		<td rowspan='2' align="center">NAMA PENDONOR</td>
		<td colspan='2' align="center">LAHIR</td>
		<td rowspan='2' align="center">PARAMETER</td>
		<td rowspan='2' align="center">NILAI<br>TITER</td>
		<td rowspan='2' align="center">TINDAKAN</td>
		<td rowspan='2' align="center">KET</td>
		<td rowspan='2' align="center">STATUS CEKAL</td>
		<td rowspan='2' align="center">PETUGAS</td>
	</tr>
	<tr style="background-color:#FF4356; font-size:11px; color:#FFFFFF; font-family:Verdana;"
		onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
		<td align="center">TEMPAT</td>
		<td align="center">TANGGAL</td>
	</tr>

	<?
	$no = 1;
	while ($datatransaksipermintaan = mysql_fetch_array($transaksipermintaan)) {
	?>
		<tr style="background-color:#FF6346; font-size:11px; color:#000000; font-family:Verdana;"
			onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td align="center"><?= $no ?></td>
			<td align="center"><?= $datatransaksipermintaan['tgl'] ?></td>
			<td align="center"><?= $datatransaksipermintaan['notrans'] ?></td>
			<td align="center"><?= $datatransaksipermintaan['kodependonor'] ?></td>
			<td align="center"><?= $datatransaksipermintaan['Nama'] ?></td>
			<td align="center"><?= $datatransaksipermintaan['TempatLhr'] ?></td>
			<td align="center"><?= $datatransaksipermintaan['TglLhr'] ?></td>
			<?
			$parameter = 'HBsAg';
			if ($datatransaksipermintaan['parameter'] == "1") $parameter = 'HCV';
			if ($datatransaksipermintaan['parameter'] == "2") $parameter = 'HIV';
			if ($datatransaksipermintaan['parameter'] == "3") $parameter = 'SYPHILIS';

			$tindakan = 'Dirujuk';
			if ($datatransaksipermintaan['hasil'] == "1") $tindakan = 'Diberikan Obat';
			if ($datatransaksipermintaan['hasil'] == "2") $tindakan = 'Konsul';

			// Jika minimal status = 0, berarti ada aksi Cabut Cekal. Selain itu dianggap "Tidak".
			if ($datatransaksipermintaan['status_cekal'] !== NULL && $datatransaksipermintaan['status_cekal'] == "0") {
				$status_cekal_text = 'Cabut Cekal';
			} else {
				$status_cekal_text = 'Tercekal';
			}
			?>
			<td align="center"><?= $parameter ?></td>
			<td align="center"><?= $datatransaksipermintaan['nilai'] ?></td>
			<td align="center"><?= $tindakan ?></td>
			<td align="center"><?= $datatransaksipermintaan['ket'] ?></td>
			<td align="center"><?= $status_cekal_text ?></td>
			<td align="center"><?= $datatransaksipermintaan['petugas'] ?></td>

		</tr>
	<? $no++;
	} ?>
</table>
<br>
<form name=xls method=post action=modul/rekap_konseling_xls.php>
	<input type=hidden name=today value='<?= $today ?>'>
	<input type=hidden name=today1 value='<?= $today1 ?>'>
	<input type=hidden name=gol_status value='<?= $src_status ?>'>
	<input type=hidden name=nomorf value='<?= $srcform ?>'>
	<input type=hidden name=hasil value='<?= $src_hasil ?>'>
	<input type=submit name=submit2 value='Export Rekap Konseling (.XLS)'>
</form>

<?
mysql_close();
?>