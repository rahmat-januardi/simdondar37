<style type="text/css">
	<!--
	/* @import url("topstyle.css"); */
	-->
</style>
<?
$namauser = $_SESSION[namauser];
$today = date("Y-m-d H:i:s");
$array_bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
include('clogin.php');
include('config/db_connect.php');
//echo $_SESSION[namauser];
if (isset($_POST['Button'])) {
	if ($_POST[NoKantong]) {
		// $hasil = mysql_query("select noKantong from stokkantong where noKantong='$_POST[NoKantong]' and Status='3' and stat2 like 'b%' AND date(kadaluwarsa) >= curdate() + interval 10 DAY ");
		$hasil = mysql_query("select noKantong from stokkantong where noKantong='$_POST[NoKantong]' and Status='3' and stat2 like 'b%' ");

		$hasil1 = mysql_fetch_assoc(mysql_query("select * from stokkantong where noKantong='$_POST[NoKantong]' "));

		$nhasil = mysql_num_rows($hasil);

		$kembali = mysql_query("insert into kembalititipan (noKantong,Status,volume,produk,gol_darah,RhesusDrh,tgl_Aftap,
				      kadaluwarsa,tglpengolahan)
				      select noKantong,Status,volume,produk,gol_darah,RhesusDrh,tgl_Aftap,
				      kadaluwarsa,tglpengolahan
				      from stokkantong where noKantong='$_POST[NoKantong]'");
		$kembali1 = mysql_query("update kembalititipan set tgl_kembali='$today', user='$namauser' where noKantong='$_POST[NoKantong]'");
		if ($nhasil == 1) {
			$qbdrs = mysql_fetch_assoc(mysql_query("select nama from bdrs where kode='$hasil1[stat2]'"));
			$keluarkan = mysql_query("update stokkantong set Status='2',sah='1',stat2=null ,log='0',tgl_keluar=NULL where noKantong='$_POST[NoKantong]'");
			$keluarkan1 = mysql_query("update dtransaksipermintaan set Status='B' where NoKantong='$_POST[NoKantong]'");
			$hapuskirimbdrs = mysql_query("update kirimbdrs SET status='1' , petugas='$namauser' , tglkembali='$today' where nokantong='$_POST[NoKantong]'");
			$hapuskirimudd = mysql_query("update kirimudd SET status='1' , petugas='$namauser' , tglkembali='$today' where nokantong='$_POST[NoKantong]'");
			$qbdrs = mysql_fetch_assoc(mysql_query("select nama from bdrs where kode='$hasil1[stat2]'"));
			//=======Audit Trial====================================================================================
			$log_mdl = 'KOMPONEN';
			$log_aksi = 'Kembali dari: ' . $qbdrs[nama] . ' kantong: ' . $_POST[NoKantong] . ' - ' . $hasil1[produk];
			include_once "user_log.php";
			//=====================================================================================================

			// Tambahan alert sukses
			echo "<script>alert('Data sudah diterima dan status telah diubah.');</script>";
		} else {
			echo "HANYA DARAH DARI BANK DARAH RS YANG BISA DIPROSES DISINI";
			echo "<META http-equiv='refresh' content='3; url='<?echo $PHPSELF;?>";
		}
	}
	if ($keluarkan) {
		echo "<BR>Darah Sudah diubah Statusnya<BR>";
		echo "<META http-equiv='refresh' content='0; url='<?echo $PHPSELF;?>";
	}
} else {
	$hasil = mysql_query("select noKantong from stokkantong where noKantong='$_POST[NoKantong]' and Status='3' and stat2 like 'b%' and hasil !='4'");
	$TRec = mysql_num_rows($hasil);
?>
	<form align="left" method="post" action="<? echo $PHPSELF ?>">
		No Kantong<input type="text" name="NoKantong">
		<br><input type="submit" name="Button" value="Submit">
		<table class="list" id="box-table-b">
			<tr class="field">
				<th><b>No</b></th>
				<th><b> <?= mysql_num_rows($hasil) ?>-Kantong</b></th>
				<th>Gol Darah</th>
				<th>No Formulir</th>
				<th>Tanggal Permintaan</th>
			</tr>
			<input type="hidden" name="jumlah" value="<?= mysql_num_rows($hasil) ?>">
			<?
			$no = 1;
			while ($baris = mysql_fetch_assoc($hasil)) { ?>
				<tr class="record">
					<td>
						<div align="center">
							<font size="2"><?= $no ?>.</font>
						</div>
					</td>
					<td><?= $baris[dtn] ?></td>
					<td><?= $baris[gol_darah] ?></td>
					<td><?= $baris[NoForm] ?></td>
					<td><?= $baris[tgll] ?></td>
				</tr><?
						$no++;
					} ?>
		</table>
	</form>
<? } ?>