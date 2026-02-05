<script language=javascript src="js/bdrs.js" type="text/javascript"> </script>
<script language=javascript src="js/jquery-latest.js" type="text/javascript"> </script>
<script language=javascript src="js/alert.js" type="text/javascript"> </script>
<script language=javascript src="js/jquery-1.4.2.min.js"></script>
<script language=javascript src="js/jquery-ui-1.8.6.custom.min.js"></script>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<style type="text/css">
	.alert { font: 62.5% "Trebuchet MS", sans-serif; }
</style>
<script type="text/javascript">
  jQuery(document).ready(function(){
  $('#instansi').autocomplete({source:'modul/suggest_bdrs.php', minLength:2});});
  </script>

<?
include('clogin.php');
include('config/db_connect.php');
require_once("modul/background_process.php");

$today=date("Y-m-d H:i:s");
$tgl1=date("d",strtotime($today));
$bln1=date("n",strtotime($today));
$thn1=date("Y",strtotime($today));
$bulan=array(1=>"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
$bln11=$bulan[$bln1];

$namauser=$_SESSION[namauser];
//------------------------------ Submit ----------------------------//
if (isset($_POST[submit])) {
	$nk1='';
	$rs=$_POST[rmhskt];
	$pdbdrs='';
	$myfile="bdrs/$rs-$today.zip";
	if (file_exists($myfile)) { $fh=fopen($myfile,'a') or die ("Cant open file"); } else { $fh=fopen($myfile,'w') or die ("Cant open file"); }
	for ($i=0;$i<count($_POST[nk]);$i++) {
		$nkantong=$_POST[nk][$i];
		$tambah=mysql_query("update stokkantong set Status='3',stat2='$rs',tgl_keluar='$today',log='1' where noKantong='$nkantong'");
		$pd=mysql_fetch_assoc(mysql_query("select * from stokkantong where noKantong='$nkantong'"));
		$qbdrs=mysql_fetch_assoc(mysql_query("select nama from bdrs where kode='$rs'"));
		//=======Audit Trial====================================================================================
		$log_mdl ='DISTRIBUSI';
		$log_aksi='Kirim ke: '.$qbdrs[nama].' Kantong: '.$nkantong.' - '.$pd[produk];
		include("user_log.php");
		//=====================================================================================================		
		$pd_sql="insert into stokkantong values ('$pd[noKantong]','$pd[jenis]','$pd[Status]','$pd[tglTerima]',
		'$pd[volume]','$pd[merk]','$pd[kantongAsal]','$pd[produk]','$pd[sah]','$pd[Isi]','$pd[gol_darah]',
		'$pd[RhesusDrh]','$pd[stat2]','$pd[StatTempat]','$pd[kodePendonor]','$pd[kodePendonor_lama]',
		'$pd[statKonfirmasi]','$pd[statQC]',
		'$pd[AsalUTD]','$pd[tgl_Aftap]','$pd[kadaluwarsa]','$pd[tglpengolahan]','$pd[mu]','$pd[stokcheck]','$pd[tgl_keluar]')";
		$bdrs_sql=mysql_query("insert into kirimbdrs (nokantong,bdrs,tgl,petugas,status) values ('$pd[noKantong]','$pd[stat2]','$pd[tgl_keluar]','$namauser', '0')");

		$pd_sql=base64_encode($pd_sql.';');
		
		$nk1=$nk1.";".$nkantong;
		fwrite($fh,$pd_sql);
		fwrite($fh,"\n");
	}
	fclose($fh);
	//-----------------------------------
	if ($tambah) {
        echo "Data Telah berhasil dimasukkan $rs<br>";
		backgroundPost('http://localhost/simudda/modul/background_up.php');
		?>
		<ol>
		<li><a href=modul/form_bdrsxls.php?nk1=<?=$nk1?>&rs=<?=$rs?>&namauser=<?=$namauser?>>Laporan Pengiriman Darah Ke BDRS</a></li>
		<li><a href=<?=$myfile?>>File Update BDRS <?=$rs?></a></li>
		</ol>
		<?
	}
}
//------------------------------ END Submit ----------------------------//
?>
<a href=bdrs>ALL File BDRS</a>
<h1 class="table">Pengiriman Ke BDRS</h1>
<br>
<form name="bdrs" id="bdrs" onsubmit="return ok()" method="POST" action="<?=$PHPSELF?>">
<table>
<tr><td>Tujuan :</td><td><input type=text name="rmhskt" id="instansi">
	</td></tr>
<tr><td>Tanggal :</td><td><?=$tgl1?> - <?=$bln11?> - <?=$thn1?></td></tr>
<tr><td>No Kantong :</td><td>
	<INPUT type="text"  name="nokantong" id="nokantong" onkeydown="chang(event,this);" onchange="addRow('box-table-b0')"/></td></tr></table>
       <!--INPUT name="nokantong" type="text" id="nokantong" onkeydown="chang(event,this);" onchange="addRow('box-table-b0');focus(document.periksa.nokantong);"/></td></tr></table-->

				<TABLE class="form" id="box-table-b0" >
					<tr class="field">
						<th rowspan=2>No. </th>
						<th rowspan=2>No.Kantong</th>
						<th rowspan=2>Gol. Darah</th>						
						<th rowspan=2>Komponen</th>
						<th rowspan=2>Rhesus Darah</th>
						<th rowspan=2>Tgl Aftap</th>
						<th rowspan=2>Tgl Kadaluarsa</th>
						<th rowspan=2>Tgl Pengolahan</th>
						<th colspan=4>Hasil Pemeriksaan**</th>
					</tr>
				<tr><th>HIV</th><th>HBsAg</th><th>HCV</th><th>SYPHILIS</th></tr>
				
				</TABLE>
			<!--INPUT type="button" value="Delete Row" onclick="deleteRow('box-table-b0')" /-->
	<table>	
		<tr>
			<td>Yang Menyerahkan :</td>
			<td><? echo $namauser;?></td></tr>
		<tr>
			<td>** R : Reaktif, NR : Non Reaktif</td>
		</tr>
		
</table>
<br>
	<input type="submit" value="Submit" name="submit">
</form>



<div class="alert" id="alert">
	<div id="pilih_tes" title="Pilih jenis tes..!">
		<p>Silahkan pilih jenis tes</p>
	</div>
	<div id="ganti_reagen" title="Waktu Ganti Reagen..!">
		<p>Silahkan isikan hasil test dan submit terlebih dahulu. Ganti reagen yang telah habis</p>
	</div>
	<div id="kantong_tdk_sesuai" title="Kantong tidak sesuai..!">
		<p>Silahkan cek kembali kantong yang anda masukkan, atau masukkan kantong lain</p>
	</div>
	<div id="pilih_reagen" title="Pilih reagen..!">
		<p>Silahkan pilih reagen terlebih dahulu sebelum memasukkan nomor kantong</p>
	</div>
	<div id="kantong_sudah_diinput" title="Kantong sudah diinput..!">
		<p>Silahkan masukkan kantong yang lain</p>
	</div>
	<div id="kantong_reaktif" title="Kantong sudah ditest...!">
		<p>Silahkan masukkan kantong yang lain</p>
	</div>
</div>
</body>
