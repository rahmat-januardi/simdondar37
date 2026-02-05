<?
include "config/db_connect.php";
$barang=mysql_query("select produk, kodePendonor, gol_darah, RhesusDrh, tgl_Aftap, 
kadaluwarsa, kadaluwarsa_ktg, lama_pengambilan, Status, hasil, tglperiksa, volume, nolot_ktg, abs from stokkantong where noKantong='$_GET[kode]'");
echo '{"barang": ';
    while($barang1=mysql_fetch_assoc($barang)){
        $kode=$barang1['kode'];
        $nama=$barang1['produk'];
	$pendonor=$barang1['kodePendonor'];
	$goldarah=$barang1['gol_darah'];
	$rhesus=$barang1['RhesusDrh'];
	$tglaftap=$barang1['tgl_Aftap'];
	$kadaluwarsa=$barang1['kadaluwarsa'];
	$expirektg=$barang1['kadaluwarsa_ktg'];
	$durasi=$barang1['lama_pengambilan'];
	$status=$barang1['Status'];
	$hasilimltd=$barang1['hasil'];
	$tglperiksa=$barang1['tglperiksa'];
	$volume_asal=$barang1['volume'];
	$nolotktg=$barang1['nolot_ktg'];
	$abs1=$barang1['abs'];
        
        echo '{
            "kode":"'.$kode.'",
            "nama":"'.$nama.'",
	    "pendonor":"'.$pendonor.'",
	    "goldarah":"'.$goldarah.'",
	    "rhesus":"'.$rhesus.'",
	    "tglaftap":"'.$tglaftap.'",
	    "kadaluwarsa":"'.$kadaluwarsa.'",
	    "expirektg":"'.$expirektg.'",
	    "durasi":"'.$durasi.'",
	    "status":"'.$status.'",
	    "tglperiksa":"'.$tglperiksa.'",
	    "volume_asal":"'.$volume_asal.'",
	    "nolotktg":"'.$nolotktg.'",
	    "abs1":"'.$abs1.'",
	    "hasilimltd":"'.$hasilimltd.'"
            
        }';
    }
    echo '}';
?>
