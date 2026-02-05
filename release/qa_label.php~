<head>
<script language=javascript src="idhasil.js" type="text/javascript"> </script>
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script language=javascript src="util.js" type="text/javascript"> </script>
</head>
<body OnLoad="document.label_lab.nokantong.focus();">
<form name="label_lab" method="POST" action="<?=$PHPSELF?>">
	<table border=0>
	<tr>
		<td>Label Release Produk</td>
		<td><input name="nokantong" type="text"></td>
		<td colspan=2><input name="submit" type="submit" value="Submit" class="swn_button_blue"></td>
	</tr>
</form>
<? 
require_once('config/db_connect.php');
if (isset($_POST[submit])) {
    //$kantong=mysql_query("select * from `release` where rnokantong='$_POST[nokantong]' order by rid desc");
	
	$kantong=mysql_query("SELECT * ,\n".
							"stokkantong.`Status` as stktg\n".
							"FROM\n".
							"`release`\n".
							"INNER JOIN stokkantong ON `release`.rnokantong = stokkantong.noKantong\n".
							"WHERE\n".
							"`release`.rnokantong = '$_POST[nokantong]' and stokkantong.`Status` =2\n".
							"ORDER BY\n".
							"`release`.rid DESC");

    $kantong1=mysql_num_rows($kantong);
    if ($kantong1>0) {
        $kantong1=mysql_fetch_assoc($kantong);
        ?>
	    <tr><td>No Kantong </td><td><?=$kantong1[rnokantong]?></td></tr>
	    <tr><td>Produk</td><td><?=$kantong1[rproduk]?></td></tr>
	    <tr><td>Gol Darah</td><td><?=$kantong1[rgolda]?></td></tr>
	    <tr><td>Hasil Release</td><td><?=$kantong1[rsatus_ket]?></td></tr><?
    }else {
        ?>
        <tr><td></td></tr>
	    <tr><td>Produk Tidak dapat dicetak label ulang</td><td><?=$kantong1[rproduk]?></td></tr>
	    <tr><td>Silahkan Cek Status Kantong</td></tr><?
    }
}

if (isset($kantong1[rnokantong])) {
?>
<tr><td>
	<!--input name=cetak type=button value="Label Besar" class="swn_button_blue" onclick="$.fn.colorbox({href:'qa_label_105.php?noKantong=<?=$kantong1[rnokantong]?>', iframe:true, innerWidth:500, innerHeight:250},function(){ $().bind('cbox_closed', function(){window.location ='pmiqa.php?module=cetak_release'})});">
	<input name=cetak type=button value="Label Kecil" class="swn_button_blue" onclick="$.fn.colorbox({href:'qa_label_73.php?noKantong=<?=$kantong1[rnokantong]?>', iframe:true, innerWidth:500, innerHeight:250},function(){ $().bind('cbox_closed', function(){window.location ='pmiqa.php?module=cetak_release'})});"-->

	<input name=cetak type=button value="Label Besar" class="swn_button_blue" onclick="$.fn.colorbox({href:'qa_label_cetak.php?noKantong=<?=$kantong1[rnokantong]?>', iframe:true, innerWidth:500, innerHeight:250},function(){ $().bind('cbox_closed', function(){window.location ='pmiqa.php?module=cetak_release'})});">
	<input name=cetak type=button value="Label Kecil" class="swn_button_blue" onclick="$.fn.colorbox({href:'qa_label_cetak.php?noKantong=<?=$kantong1[rnokantong]?>', iframe:true, innerWidth:500, innerHeight:250},function(){ $().bind('cbox_closed', function(){window.location ='pmiqa.php?module=cetak_release'})});">
	</td><td></td></tr>
<? } ?>
</table>
</body>
