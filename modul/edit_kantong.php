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
	$pendonor	=$_POST[pendonor];
	$tglaftap	=$_POST[tglaftap];
	$kadaluwarsa	=$_POST[kadaluwarsa];
	$expirektg	=$_POST[expirektg];
	$durasi		=$_POST[durasi];
	$status		=$_POST[status];
	$hasilimltd	=$_POST[hasilimltd];
	$goldarah	=$_POST[goldarah];
	$rhesus		=$_POST[rhesus];
        
	//data sebelumnya
	 $q_cek_gol=mysql_fetch_assoc(mysql_query("SELECT Status, kodePendonor, tgl_Aftap, kadaluwarsa, kadaluwarsa_ktg, lama_pengambilan from stokkantong where noKantong='$kode'"));
	
	//update stokkantong darah
	$update_kantong=mysql_query("update stokkantong set kodePendonor='$pendonor', tgl_Aftap='$tglaftap',
kadaluwarsa='$kadaluwarsa', kadaluwarsa_ktg='$expirektg', lama_pengambilan='$durasi', Status='$status', hasil='$hasilimltd', 
gol_darah='$goldarah', RhesusDrh='$rhesus' where noKantong='$kode'");
	
	if ($update_kantong) {
		// ----- LOG AKSI (menggunakan user_log.php) -----
		$log_mdl   = $_SESSION['leveluser'];
		$log_aksi  = "Edit data kantong darah | No.Kantong: $kode | Kode Pendonor: $pendonor | Tgl Aftap: $tglaftap | Durasi: $durasi menit | Status: $status";
		include_once "user_log.php";
		// -------------------------------------------

		echo "Data kantong darah berhasil diupdate!<br>";
		echo "<meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=$PHP_SELF\">";
	} else {
		echo "Gagal update kantong. Error: " . mysql_error();
	}
	
	//histori data
$inshist=mysql_query("INSERT INTO histori
             (notrans, username, level_editor, waktu, action, jenis, tempat, up)
             VALUES
             ('$id_transaksi_baru','$namauser','Perbaikan data', '$sekarang','Perbaikan no.kantong : $kode, lama pengambilan : $durasi menit','0', '$temp', '1')");
	
	if ($update) echo ("Data telah ter-update !!
	  <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"1; URL=$PHP_SELF\">");
}

?>

<form name="masterbarang" method="POST" action="<?=$PHPSELF?>">
<h1 class="table">UPDATE DATA KANTONG DARAH</h1>
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
     
	
          $.ajax({
                    url: "cari_kantong.php?kode="+browser,
		    
                    async: false,
                    dataType: 'json',
                    success: function(json) {
			      brg1 	= json.barang.kode;
			      brg2 	= json.barang.nama;
                     	      brg3 	= json.barang.pendonor;
			      brg4 	= json.barang.goldarah;
			      brg5 	= json.barang.rhesus;
			      brg6 	= json.barang.tglaftap;
			      brg7 	= json.barang.kadaluwarsa;
			      brg8 	= json.barang.expirektg;
			      brg9 	= json.barang.durasi;
			      brg10 	= json.barang.status;
			      brg11	= json.barang.hasilimltd;
			      
                    }
                });
	  document.masterbarang.namabarang.value=brg2;
	  document.masterbarang.pendonor.value=brg3;
	  document.masterbarang.goldarah.value=brg4;
	  document.masterbarang.rhesus.value=brg5;
	  document.masterbarang.tglaftap.value=brg6;
	  document.masterbarang.kadaluwarsa.value=brg7;
	  document.masterbarang.expirektg.value=brg8;
	  document.masterbarang.durasi.value=brg9;
	  document.masterbarang.status.value=brg10;
	  document.masterbarang.hasilimltd.value=brg11;
	  
        }
</script>


<tr>
	<td>No. Kantong</td>
	<td class="input"><input name="kode" readonly="readonly" type="text" size="20" placeholder="Klik LUV cek data -->"> <a href="modul/cari_kantong2.php?&width=400&height=350" class="thickbox"><img src="images/button_search.png" border="0" /></a> </td>
	</tr>

<tr>
	<td>Kode Pendonor</td>
	<td class="input"><input name="pendonor" type="text" size="20" ><a href="modul/cari_donor2.php?&width=400&height=350" class="thickbox"><img src="images/button_search.png" border="0" /></a></td>
	</tr>

<tr>
	<td>Produk</td>
	<td class="input"><input name="namabarang" type="text" size="5" ></td>
	</tr>

<tr>
	<td>Gol. Darah</td>
	<td class="input"><input name="goldarah" type="text" size="5" ></td>
	</tr>
<tr>
	<td>Rhesus</td>
	<td class="input"><input name="rhesus" type="text" size="5" ></td>
	</tr>

<tr>
	<td>Tgl Aftap</td>
	<td class="input"><input name="tglaftap" type="text" size="10" ></td>
	</tr>

<tr>
	<td>Tgl Expired Produk</td>
	<td class="input"><input name="kadaluwarsa" type="text" size="10" ></td>
	</tr>

<tr>
	<td>Tgl Expired Kantong</td>
	<td class="input"><input name="expirektg" type="text" size="10" ></td>
	</tr>

<tr>
	<td>Durasi Pengambilan</td>
	<td class="input"><input name="durasi" type="text" size="10" >Menit</td>
	</tr>

<tr>
	<td>Hasil imltd</td>
	<td class="input"><input name="hasilimltd" type="text" size="10" >2:Non Reaktif &nbsp; 4:Reaktif</td>
	</tr>

<tr>
	<td>Status Kantong</td>
	<td class="input"><input name="status" type="text" size="10" ><br/>0:Kosong diaftap &nbsp; 1:Karantina &nbsp; 2:Sehat
								      <br/>3:Keluar &nbsp; 6:Musnah &nbsp; 7:Karantina Reaktif</td>
	</tr>




</table>
<input name="submit" type="submit" value="Update">
</form>
<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
