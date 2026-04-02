<?
include "config/db_connect.php";
$today=date("Y-m-d");

$beku=mysql_query("SELECT 
`hpengolahan`.`NoTrans`, `hpengolahan`.`user`, `hpengolahan`.`nokantong`, `hpengolahan`.`gd`, `hpengolahan`.`rh`, `hpengolahan`.`komponen`, `hpengolahan`.`volume`, `hpengolahan`.`musnah`, `hpengolahan`.`tglpembuatan`, `hpengolahan`.`tglupdate`, `hpengolahan`.`pemisahan`, `hpengolahan`.`metode`, `hpengolahan`.`Putar`, `hpengolahan`.`Pisah`,`stokkantong`.`tgl_Aftap`,`stokkantong`.`kadaluwarsa`, `stokkantong`.`lama_pengambilan`
FROM `hpengolahan` 
inner join `stokkantong` on `stokkantong`.`noKantong` = `hpengolahan`.`nokantong`
WHERE `hpengolahan`.`nokantong`='$_GET[NoKantong]'");


if (mysql_num_rows($beku)=='1') {
    echo '{"olah": ';
    while($dtbeku=mysql_fetch_assoc($beku)){

        $nokantong=$dtbeku['nokantong'];
        $produk=$dtbeku['komponen'];
        $NoTrans=$dtbeku['NoTrans'];
	$tglOlah=$dtbeku['tglpembuatan'];
	$tgl_Aftap=$dtbeku['tgl_Aftap'];
        $lama_pengambilan=$dtbeku['lama_pengambilan'];
	$kadaluwarsa=$dtbeku['kadaluwarsa'];
        $gd=$dtbeku['gd'];
	$rh=$dtbeku['rh'];
	$user=$dtbeku['user'];

        echo '
            {
                "nokantong":"'.$nokantong.'",
                "produk":"'.$produk.'",
                "NoTrans":"'.$NoTrans.'",
		"tglOlah":"'.$tglOlah.'",
		"tgl_Aftap":"'.$tgl_Aftap.'",
                "lama_pengambilan":"'.$lama_pengambilan.'",
		"kadaluwarsa":"'.$kadaluwarsa.'",
                "gd":"'.$gd.'",
		"rh":"'.$rh.'",
                "user":"'.$user.'",
		"valid":"1"
            }';

	
    }
    echo '}';
  
}

?>
