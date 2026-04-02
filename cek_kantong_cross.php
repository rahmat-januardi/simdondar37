
<?php
require_once('config/koneksi.php');
$no_label = $_GET['no_label'];
$noform = $_GET['noform'];
	// Escape User Input to help prevent SQL Injection
$no_label = mysql_real_escape_string($no_label);
$noform= mysql_real_escape_string($noform);
	//build query
$query = "SELECT noKantong,gol_darah,tgl_Aftap,produk,rhesusDrh FROM stokkantong WHERE noKantong='$no_label' and Status='2' and (hasil_release='1' or hasil_release='3')";
	//Execute query
$qry_result = mysql_query($query) or die(mysql_error());

	//Build Result String
$qk=mysql_num_rows(mysql_query("select * from dtransaksipermintaan where NoForm='$noform' and NoKantong='$no_label' and Status='1'"));
if ($qk==0) {
$row = mysql_fetch_assoc($qry_result);
$pjg_ktg = strlen($row[noKantong]);
//$no= substr($row[noKantong],0,$pjg_ktg-1);
$no= $row[noKantong];
$pjg_tgl = strlen($row[tgl_Aftap]);
$tgl= substr($row[tgl_Aftap],0,$pjg_tgl-9);
echo '{"kantong":';
echo '{"noKantong":"'.$no.'"';
echo ',"gol_darah":"'.$row[gol_darah].'"';
echo ',"rh_darah":"'.$row[rhesusDrh].'"';
echo ',"tgl_aftap":"'.$tgl.'"';
echo ',"produk":"'.$row[produk].'"';
echo '}';
echo '}';
} else
{
echo '{"kantong": ';
echo '{
"noKantong":"",
"valid":"0"
}';
echo '}';
}
?>
