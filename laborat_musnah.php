<?php
require_once('config/db_connect.php');
session_start();
$namaudd    = $_SESSION['namaudd'];
$level	    = $_SESSION['leveluser'];
//alter table dpengolahan
$table = mysql_query("SELECT `petugas` FROM `dpengolahan`");
if (!$table) {
	mysql_query("ALTER TABLE `dpengolahan` ADD `petugas` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `Produk` ,ADD `tgl` DATETIME NOT NULL AFTER `petugas` ,ADD `cara` CHAR( 1 ) NOT NULL DEFAULT '0' AFTER `tgl`, ADD PRIMARY KEY ( `noKantong` ), DROP `NoTrans`");
}
$tabel1 = mysql_query("SELECT `pisah` FROM `dpengolahan`");
if (!$tabel1) {
	mysql_query("ALTER TABLE `dpengolahan` ADD `pisah` CHAR( 1 ) NOT NULL DEFAULT '0'");
}
?>

<link href="css/content2.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-latest.js"></script>
<script type="text/javascript">
	$(document).ready(function() {

		//Larger thumbnail preview 

		$("ul.thumb li").hover(function() {
			$(this).css({
				'z-index': '10'
			});
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

		}, function() {
			$(this).css({
				'z-index': '0'
			});
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
		<?php if ($level != "tatausaha") { ?>
			<li><a href="pmi<?php echo $level; ?>.php?module=rekap_reaktif"><img src="images/rekap_imltd_rr.png" alt="" /></a></li>
			<li><a href="pmi<?php echo $level; ?>.php?module=musnah"><img src="musnah/images/musnah_entry.png" alt="" /></a></li>
		<?php } ?>
		<li><a href="pmi<?php echo $level; ?>.php?module=musnahlist"><img src="musnah/images/musnah_list.png" alt="" /></a></li>
		<li><a href="pmi<?php echo $level; ?>.php?module=rincian_darah_buang"><img src="musnah/images/musnah_rekap.png" alt="" /></a></li>
	</ul>
	</ul>
	</ul>
</div>