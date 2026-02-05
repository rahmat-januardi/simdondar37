<?
include "config/db_connect.php";
$barang=mysql_query("select NoTrans, KodePendonor, gol_darah, rhesus, jk, donorke, jam_ambil, jam_selesai,
umur, beratBadan, tensi, suhu, nadi, Hb, JenisDonor, Pengambilan from htransaksi where NoKantong='$_GET[kode]'");
echo '{"barang": ';
    while($barang1=mysql_fetch_assoc($barang)){
        $kode=$barang1['kode'];
        $kodependonor=$barang1['KodePendonor'];
	$notrans=$barang1['NoTrans'];
	$goldarah=$barang1['gol_darah'];
	$rhesus=$barang1['rhesus'];
	$jk=$barang1['jk'];
	$donorke=$barang1['donorke'];
	$jamambil=$barang1['jam_ambil'];
	$jamselesai=$barang1['jam_selesai'];
	$umur=$barang1['umur'];
	$berat=$barang1['beratBadan'];
	$tensi=$barang1['tensi'];
	$suhu=$barang1['suhu'];
	$nadi=$barang1['nadi'];
	$hb=$barang1['Hb'];
	$pengambilan=$barang1['Pengambilan'];
	$jenisdonor=$barang1['JenisDonor'];
	
	
        
        echo '{
            "kode":"'.$kode.'",
            "kodependonor":"'.$kodependonor.'",
	    "notrans":"'.$notrans.'",
	    "goldarah":"'.$goldarah.'",
	    "rhesus":"'.$rhesus.'",
	    "jk":"'.$jk.'",
	    "donorke":"'.$donorke.'",
	    "jamambil":"'.$jamambil.'",
	    "jamselesai":"'.$jamselesai.'",
	    "umur":"'.$umur.'",
	    "berat":"'.$berat.'",
	    "tensi":"'.$tensi.'",
	    "suhu":"'.$suhu.'",
	    "nadi":"'.$nadi.'",
	    "hb":"'.$hb.'",
	    "pengambilan":"'.$pengambilan.'",
	    "jenisdonor":"'.$jenisdonor.'"
	    
	    
            
        }';
    }
    echo '}';
?>
