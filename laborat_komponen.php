<?php
require_once('config/db_connect.php');
// session_start();
$namaudd=$_SESSION['namaudd'];
//alter table dpengolahan
$table=mysql_query("SELECT `petugas` FROM `dpengolahan`");
if (!$table) {mysql_query("ALTER TABLE `dpengolahan` ADD `petugas` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `Produk` ,ADD `tgl` DATETIME NOT NULL AFTER `petugas` ,ADD `cara` CHAR( 1 ) NOT NULL DEFAULT '0' AFTER `tgl`, ADD PRIMARY KEY ( `noKantong` ), DROP `NoTrans`");}
$tabel1=mysql_query("SELECT `pisah` FROM `dpengolahan`");if (!$tabel1){mysql_query("ALTER TABLE `dpengolahan` ADD `pisah` CHAR( 1 ) NOT NULL DEFAULT '0'");}
?>

<link href="css/content2.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-latest.js"></script> 
<script type="text/javascript"> 

$(document).ready(function(){

//Larger thumbnail preview 

$("ul.thumb li").hover(function() {
	$(this).css({'z-index' : '10'});
	$(this).find('img').addClass("hover").stop()
		.animate({
			marginTop: '-110px', 
			marginLeft: '-110px', 
			top: '55%', 
			left: '55%', 
			width: '140px', 
			height: '155px',
			padding: '20px' 
		}, 200);
	
	} , function() {
	$(this).css({'z-index' : '0'});
	$(this).find('img').removeClass("hover").stop()
		.animate({
			marginTop: '0', 
			marginLeft: '0',
			top: '0', 
			left: '0', 
			width: '100px', 
			height: '111px', 
			padding: '5px'
		}, 400);
});

//Swap Image on Click
//	$("ul.thumb li a").click(function() {
		
//		var mainImage = $(this).attr("href"); //Find Image Name
//		$("#main_view img").attr({ src: mainImage });
//		return false;		
//	});
 
});
</script> 
</head>

<div class="container">
<ul class="thumb">
	<!--<li><a href="pmikomponen.php?module=komponen"><img src="images/pembuatan_komponen.png" alt=""/></a></li>-->
	<li><a href="pmikomponen.php?module=pengolahan"><img src="images/pembuatan_komponen.png" alt=""/></a></li>
	<li><a href="pmikomponen.php?module=komponen_split"><img src="images/split_komponen.png" alt=""/></a></li>
	<!--<li><a href="pmikomponen.php?module=shasil_labl"><img src="images/cetak_label.png" alt=""/></a></li>-->
	<li><a href="pmikomponen.php?module=rincian_komponen"><img src="images/rincian_komponen.png" alt=""/></a></li>
	<li><a href="pmikomponen.php?module=rekap_komponen"><img src="images/rekap_komponen.png" alt=""/></a></li>
	<li><a href="pmikomponen.php?module=set_stok_sos"><img src="images/set_sos.png" alt=""/></a></li>
	<li><a href="pmikomponen.php?module=cetak_label_komponen"><img src="images/cetak_label.png" alt=""/></a></li>
	<!--li><a href="pmikomponen.php?module=musnah"><img src="images/pemusnahan_kantong.png" alt="" /></a></li>
	<li><a href="pmikomponen.php?module=musnahlist"><img src="images/rekap_musnah.png" alt="" /></a></li>
	<li><a href="pmikomponen.php?module=rincian_darah_buang"><img src="images/rincian_musnah.png" alt="" /></a></li>
	<li><a href="pmikomponen.php?module=rekap_darah_buang"><img src="images/rekap_musnah.png" alt="" /></a></li-->
	<!--<li><a href="pmikomponen.php?module=manual_komponen"><img src="images/manual_komponen.png" alt="" /></a></li>-->
	<!--<li><a href="pmikomponen.php?module=cetakulang_barcode"><img src="images/cetak_ulang_barcode.png" alt=""/></a></li>-->
	<!--li><a href="pmikomponen.php?module=stok_sehat"><img src="images/stok_kembali_sehat.png" alt=""/></a></li-->
	<!--li><a href="pmikomponen.php?module=skantong"><img src="images/pencocokan_stok.png" alt=""/></a></li-->
</ul>
</ul>
</ul>
</div>

