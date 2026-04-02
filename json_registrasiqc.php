<?
include "config/db_connect.php";
$today=date("Y-m-d");
$bdrs=mysql_query("select gol_darah,RhesusDrh,produk,tgl_Aftap,kadaluwarsa,tglpengolahan from stokkantong where noKantong='$_GET[NoKantong]' and kadaluwarsa>='$today' and statQC='' ");
if (mysql_num_rows($bdrs)=='1') {
    echo '{"darah": ';
    while($bdrs1=mysql_fetch_assoc($bdrs)){
        $gol=$bdrs1['gol_darah'];
        $produk=$bdrs1['produk'];
	$rh=$bdrs1['RhesusDrh'];
	$aftap=$bdrs1['tgl_Aftap'];
	$kadaluwarsa=$bdrs1['kadaluwarsa'];
	
        echo '
            {
                "gol_darah":"'.$gol.'",
                "produk":"'.$produk.'",
		"RhesusDrh":"'.$rh.'",
		"tgl_Aftap":"'.$aftap.'",
		"kadaluwarsa":"'.$kadaluwarsa.'",
                "valid":"1"
            }';
    }
    echo '}';
} 
?>
