<head>
<link href="modul/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="modul/thickbox/thickbox.js"></script>
<script language="javascript">
function selectSupplier(Kode){
	  $('input[@name=kodeSup]').val(Kode);
	  tb_remove(); 
}
function selectKode(Kode){
	  $('input[@name=kode]').val(Kode);
 	  tb_remove(); 
	  dbar(Kode);
}
</script>
</head>

<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<?
include('clogin.php');
include('config/db_connect.php');
$namauser=$_SESSION[namauser];
$sekarang = date("Y-m-d h:m:s");
                
if (isset($_POST[submit])) {
	$kode		=$_POST[kode];
	$kodependonor	=$_POST[kodependonor];
	$goldarah	=$_POST[goldarah];
	$rhesus		=$_POST[rhesus];
	$jk		=$_POST[jk];
	$berat		=$_POST[berat];
	$tensi		=$_POST[tensi];
	$suhu		=$_POST[suhu];
	$nadi		=$_POST[nadi];
	$hb		=$_POST[hb];
	$umur		=$_POST[umur];
	$donorke	=$_POST[donorke];
	$jamambil	=$_POST[jamambil];
	$jamselesai	=$_POST[jamselesai];
	$pengambilan	=$_POST[pengambilan];
	$jenisdonor	=$_POST[jenisdonor];
	
        
	
	
	//update stokkantong darah4
	$update_transaksi=mysql_query("update htransaksi set KodePendonor='$kodependonor', gol_darah='$goldarah', rhesus='$rhesus', 
jk='$jk', beratBadan='$berat', tensi='$tensi', suhu='$suhu', nadi='$nadi', Hb='$hb', umur='$umur', donorke='$donorke',
jam_ambil='$jamambil', jam_selesai='$jamselesai', Pengambilan='$pengambilan', JenisDonor='$jenisdonor' where NoKantong='$kode'");

	if ($update_transaksi) {
		// ----- LOG AKSI (menggunakan user_log.php) -----
		$log_mdl   = $_SESSION['leveluser']; 
		$log_aksi  = "Edit transaksi donor | No.Kantong: $kode | Kode Pendonor: $kodependonor | Jam Ambil: $jamambil | Jam Selesai: $jamselesai";
		include_once "user_log.php";
		// -------------------------------------------

		echo "Data transaksi berhasil diupdate!<br>";
		echo "<meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=$PHP_SELF\">";
	} else {
		echo "Gagal update transaksi. Error: " . mysql_error();
	}
	
//histori data
$inshist=mysql_query("INSERT INTO histori
             (notrans, username, level_editor, waktu, action, jenis, tempat, up)
             VALUES
             ('$id_transaksi_baru','$namauser','Perbaikan data', '$sekarang','Perbaikan transaksi no.kantong : $kode, kode pendonor : $kodependonor, jam_ambil : $jamambil, jam_selesai : $jamselesai ','0', '$temp', '1')");
	
	
	if ($update) echo ("Data telah ter-update !!
	  <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"1; URL=$PHP_SELF\">");
}

?>

<form name="masterbarang" method="POST" action="<?=$PHPSELF?>">
<h1 class="table">UPDATE DATA TRANSAKSI DONASI</h1>
<table class="form" border="1" cellpadding=2 cellspacing=3>
<script type="text/javascript">
function dbar(browser){
     var brg1;
     var brg2;
     var brg3;
     var brg4;
     var brg5;
     var brg6;
     var brg7;
     var brg8;
     var brg9;
     var brg10;
	var brg11;
	var brg12;
	var brg13;
	var brg14;
	var brg15;
	var brg16;
	var brg17;
     
	
          $.ajax({
                    url: "cari_transaksi.php?kode="+browser,
		    
                    async: false,
                    dataType: 'json',
                    success: function(json) {
			      brg1 	= json.barang.kode;
			      brg2 	= json.barang.kodependonor;
			      brg3 	= json.barang.notrans;
			      brg4 	= json.barang.goldarah;
			      brg5 	= json.barang.rhesus;
			      brg6 	= json.barang.jk;
			      brg7 	= json.barang.donorke;
			      brg8 	= json.barang.jamambil;
			      brg9 	= json.barang.jamselesai;
			      brg10 	= json.barang.umur;
				brg11 	= json.barang.berat;
				brg12 	= json.barang.tensi;
				brg13 	= json.barang.suhu;
				brg14 	= json.barang.nadi;
				brg15 	= json.barang.hb;
				brg16 	= json.barang.pengambilan;
				brg17 	= json.barang.jenisdonor;
                     	     
			      
                    }
                });
	  document.masterbarang.kodependonor.value=brg2;
	  document.masterbarang.notrans.value=brg3;
	  document.masterbarang.goldarah.value=brg4;
	  document.masterbarang.rhesus.value=brg5;
	  document.masterbarang.jk.value=brg6;
	  document.masterbarang.donorke.value=brg7;
	  document.masterbarang.jamambil.value=brg8;
	  document.masterbarang.jamselesai.value=brg9;
	  document.masterbarang.umur.value=brg10;
		document.masterbarang.berat.value=brg11;
		document.masterbarang.tensi.value=brg12;
		document.masterbarang.suhu.value=brg13;
		document.masterbarang.nadi.value=brg14;
		document.masterbarang.hb.value=brg15;
		document.masterbarang.pengambilan.value=brg16;
	  	document.masterbarang.jenisdonor.value=brg17;
	  
        }
</script>


<tr>
	<td>No. Kantong</td>
	<td class="input"><input name="kode" readonly="readonly" type="text" size="20" placeholder="Klik LUV cek data -->"> <a href="modul/cari_transaksi2.php?&width=400&height=350" class="thickbox"><img src="images/button_search.png" border="0" /></a> </td>
	</tr>

<tr>
	<td>No. Transaksi</td>
	<td class="input"><input name="notrans" type="text" size="20" readonly="readonly" ></td>
	</tr>

<tr>
	<td>Kode Pendonor</td>
	<td class="input"><input name="kodependonor" type="text" size="20" ><a href="modul/cari_donor2.php?&width=400&height=350" class="thickbox"><img src="images/button_search.png" border="0" /></a></td>
	</tr>

<tr>
	<td>Gol darah</td>
	<td class="input"><input name="goldarah" type="text" size="5"></td>
	</tr>

<tr>
	<td>Rhesus</td>
	<td class="input"><input name="rhesus" type="text" size="5"></td>
	</tr>

<tr>
	<td>Jenis Kelamin</td>
	<td class="input"><input name="jk" type="text" size="5"><br/>0:laki-laki &nbsp; 1:perempuan</td>
	</tr>

<tr>
	<td>Berat Badan</td>
	<td class="input"><input name="berat" type="text" size="5">Kg</td>
	</tr>

<tr>
	<td>Tensi</td>
	<td class="input"><input name="tensi" type="text" size="5"></td>
	</tr>

<tr>
	<td>Suhu</td>
	<td class="input"><input name="suhu" type="text" size="5"></td>
	</tr>

<tr>
	<td>Nadi</td>
	<td class="input"><input name="nadi" type="text" size="5"></td>
	</tr>

<tr>
	<td>Hb</td>
	<td class="input"><input name="hb" type="text" size="5"></td>
	</tr>


<tr>
	<td>Umur</td>
	<td class="input"><input name="umur" type="text" size="5">Tahun</td>
	</tr>

<tr>
	<td>Donor Ke ?</td>
	<td class="input"><input name="donorke" type="text" size="5">Kali</td>
	</tr>

<tr>
	<td>Jam Ambil</td>
	<td class="input"><input name="jamambil" type="text" size="5"></td>
	</tr>

<tr>
	<td>Jam Selesai</td>
	<td class="input"><input name="jamselesai" type="text" size="5"></td>
	</tr>

<tr>
	<td>Status Pengambilan</td>
	<td class="input"><input name="pengambilan" type="text" size="5"><br/>0:Berhasil &nbsp; 2:Gagal &nbsp; 1:Batal</td>
	</tr>

<tr>
	<td>Jenis Donor</td>
	<td class="input"><input name="jenisdonor" type="text" size="5"><br/>0:Sukarela &nbsp; 1:Pengganti</td>
	</tr>



</table>
<input name="submit" type="submit" value="Update">
</form>
<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
