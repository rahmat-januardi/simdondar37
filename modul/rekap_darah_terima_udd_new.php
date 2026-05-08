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
<?php
require_once('config/db_connect.php');
session_start();
$namaudd = $_SESSION[namaudd];
$col = mysql_query("SELECT `status` FROM `kirimudd`");
if (!$col) {
	mysql_query("ALTER TABLE `kirimudd` ADD `status` INT( 1 ) NOT NULL DEFAULT '0' COMMENT '0=keluar 1=kembali'");
}
?>


<?
include('config/db_connect.php');
$today = date('Y-m-d');
$today1 = $today;
$src_rs = "";
$src_lay = "";
$src_shift = "";
if (isset($_POST[minta1])) {
	$today = $_POST[minta1];
	$today1 = $today;
}
if ($_POST[minta2] != '') $today1 = $_POST[minta2];
if ($_POST[bdrs] != '') $src_bdrs = $_POST[bdrs];
if ($_POST[produk] != '') $src_produk = $_POST[produk];
if ($_POST[status] != '') $src_status = $_POST[status];



?>
<h1>REKAP DARAH TERIMA DARI UTD LAIN</h1>
<form method=post> Mulai:
	TANGGAL : <input type=text name=minta1 id=datepicker size=10 value=<?= $today ?>>
	S/D <input type=text name=minta2 id=datepicker1 size=10 value=<?= $today1 ?>><br>

	ASAL UDD
	<select name="bdrs">
		<option value="" selected>-SEMUA-</option>
		<?php
		$ql = mysql_query("select * from utd ");

		while ($rowl1 = mysql_fetch_array($ql)) {
			echo "<option value=$rowl1[id]>$rowl1[nama]</option>";
		}
		?>
	</select>

	PRODUK
	<select name="produk">
		<option value="" selected>- SEMUA -</option>
		<?php
		$qrs = mysql_query("select * from produk ");

		while ($rowrs1 = mysql_fetch_array($qrs)) {
			echo "<option value=$rowrs1[Nama]>$rowrs1[Nama]</option>";
		}
		?>
	</select>

	<input type="submit" name="submit" value="Lihat" class="swn_button_blue">


</form>

<br>
<form name=xls method=post action=modul/rekap_darah_terima_udd_new_xls.php>
	<input type=hidden name=today value='<?= $today ?>'>
	<input type=hidden name=today1 value='<?= $today1 ?>'>
	<input type=hidden name=status value='<?= $src_status ?>'>
	<input type=hidden name=bdrs value='<?= $src_bdrs ?>'>
	<input type=hidden name=produk value='<?= $src_produk ?>'>
	<input type=submit name=submit2 value='Print Rekap Darah Terima dari UTD lain (.XLS)'>
</form>


<?
$transaksipermintaan = mysql_query("select s.noKantong,s.jenis,s.gol_darah,s.produk,s.RhesusDrh,s.tgl_Aftap,s.kadaluwarsa,s.tglperiksa,s.tglTerima,s.AsalUTD,k.petugas from stokkantong as s, terimaudd as k where s.tglTerima >='$today' and s.tglTerima <= '$today1'  and s.nokantong=k.noKantong and s.asalUTD like '%$src_bdrs%' and s.produk like '%$src_produk%' order by s.tglTerima ASC  ");

//$transaksipermintaan=mysql_query("select noKantong,jenis,gol_darah,produk,RhesusDrh,tgl_Aftap,kadaluwarsa,tglperiksa,stat2,tgl_keluar from stokkantong where CAST(tgl_keluar as date) >='$today' and CAST(tgl_keluar as date) <='$today1' and Status='3' and stat2 like 'b%' order by tgl_keluar ASC");



$countp = mysql_num_rows($transaksipermintaan);
$namabdrs = mysql_fetch_assoc(mysql_query("select nama from utd where id = '$src_bdrs' "));
echo "<br><br>";
echo "Total Darah Terima dari:  $namabdrs[nama] sebanyak ";
echo "<b>";
echo $countp;
echo "</b>";
echo " kantong";
?>

<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="100%">
	<tr style="background-color:#FF4356; font-size:11px; color:#FFFFFF; font-family:Verdana;"
		onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
		<!--th colspan=12><b>Total = <? $TRec ?> Kantong</b></th></tr><tr class="field"-->
		<td>No</td>
		<td>Nama UDD</td>
		<td>No Kantong</td>
		<td>Gol (Rh) Darah</td>
		<td>Komponen</td>
		<td>Tgl Aftap</td>
		<td>Tgl Exp.</td>
		<td>Tgl Periksa</td>
		<td>Jenis</td>
		<td>Petugas</td>
		<!--td>Status</td-->
	</tr>


	<?
	$no = 1;
	while ($datatransaksipermintaan = mysql_fetch_array($transaksipermintaan)) {
	?>
		<tr style="background-color:#FFFFFF; font-size:11px; color:#000000; font-family:Verdana;"
			onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td align="center"><?= $no ?></td>


			<?
			$bdrs = mysql_fetch_assoc(mysql_query("select * from utd where id='$datatransaksipermintaan[AsalUTD]'"));

			?>
			<td align="left"><?= $bdrs['nama'] ?></td>
			<td align="left"><?= $datatransaksipermintaan['noKantong'] ?></td>
			<td align="left"><?= $datatransaksipermintaan['gol_darah'] ?> (<?= $datatransaksipermintaan['RhesusDrh'] ?> )</td>
			<td align="left"><?= $datatransaksipermintaan['produk'] ?></td>
			<td align="left"><?= $datatransaksipermintaan['tgl_Aftap'] ?></td>
			<td align="left"><?= $datatransaksipermintaan['kadaluwarsa'] ?></td>
			<td align="left"><?= $datatransaksipermintaan['tglperiksa'] ?></td>
			<?
			$jns = "Double";
			if ($a_dtransaksipermintaan[jenis] == '1') $jns = "Single";
			if ($a_dtransaksipermintaan[jenis] == '3') $jns = "Threeple";
			if ($a_dtransaksipermintaan[jenis] == '4') $jns = "Quadruple";
			if ($a_dtransaksipermintaan[jenis] == '6') $jns = "Pediatrik";

			?>
			<td align="left"><?= $jns ?></td>
			<td align="left"><?= $datatransaksipermintaan['petugas'] ?></td>
			<!--?
			if ($datatransaksipermintaan[status]=='0') $status='keluar';
			if ($datatransaksipermintaan[status]=='1') $status='Kembali';	
			 ?>
	<td align="left"><?= $status ?></td-->
		</tr>
	<? $no++;
	} ?>
</table>


<?
mysql_close();
?>