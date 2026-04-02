<style type="text/css" title="currentStyle">
			@import "css/dt_page.css";
			@import "css/dt_table.css";
			@import "css/dt_table_jui.css";
		</style>
		<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
		<link type="text/css" href="css/TableTools_JUI.css" rel="stylesheet" />
		<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
		<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<?

//untuk menu mobil unit
	$instan0=mysql_query("select * from detailinstansi where aktif='1'");
	$instan01=mysql_fetch_assoc($instan0);
	$td0=php_uname('n');
	$td0=strtoupper($td0);
	$td0=substr($td0,0,1);
	if ($td0=='M') {
		$ninstan = mysql_num_rows($instan0);
		if ($ninstan!=1)  { echo $pesan="<h1>SILAHKAN <b><a href=pmimobile.php?module=data_jadwal_mobile>PILIH INSTANSI</a></b>  SEBELUM MELANJUTKAN!!!</h1>";}
		else { 

$pesan=$instan01[nama];
?>
<h1>Instansi telah di pilih <? echo $pesan ?></h1>
<br>
<h2>Untuk Merubah Instansi Silahkan ulangi proses Download dengan Klik <a href="pmimobile?module=download">DISINI</a> </h2>





<?
//		$tempat=mysql_query("update detailinstansi set aktif='0' where aktif='1'");
		};
	};

?>
