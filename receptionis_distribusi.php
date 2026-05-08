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
<?
$today = date("Y-m-d");
?>
<div class="container">
	<ul class="thumb">
		<!--li><a href="pmilaboratorium.php?module=crossmatch"><img src="images/crossmatch1.png" alt="" /></a></li>
        <li><a href="pmilaboratorium.php?module=label_cross"><img src="images/cetak_crossmatch.png" alt=""/></a></li>
    	<li><a href="pmilaboratorium.php?module=pindah_titipan"><img src="images/pemindahan_titipan.png" alt="" /></a></li>
	<li><a href="pmilaboratorium.php?module=musnah"><img src="images/pemusnahan_kantong.png" alt="" /></a></li-->

		<li><a href="pmikasir2.php?module=form_bdrs"><img src="images/kirim_bdrs2.png" alt="" /></a></li>
		<li><a href="pmikasir2.php?module=dari_bdrs"><img src="images/terima_bdrs.png" alt="" /></a></li>
		<li><a href="pmikasir2.php?module=rekap_darah_keluar_bdrs"><img src="images/rekap_bdrs.png" alt="" /></a></li>
		<!--li><a href="pmilaboratorium.php?module=update_dari_bdrs"><img src="images/update_d_bdrs.png" alt=""/></a></li>
	<li><a href="pmilaboratorium.php?module=kembali"><img src="images/darah_kembali1.png" alt="" /></a></li-->
		<li><a href="pmikasir2.php?module=form_udd"><img src="images/kirim_utd.png" alt="" /></a></li>
		<!--li><a href="pmilaboratorium.php?module=update_dari_udd"><img src="images/update_d_utd_lain.png" alt=""/></a></li>
	<li><a href="pmilaboratorium.php?module=form_rujukan"><img src="images/kirim_sampel_rujukan.png" alt=""/></a></li>
	<li><a href="pmilaboratorium.php?module=update_dari_rujukan"><img src="images/update_hasil_rujukan.png" alt=""/></a></li-->
		<li><a href="pmikasir2.php?module=terima_dari_utd_lain"><img src="images/terima_utd.png" alt="" /></a></li>
		<li><a href="pmikasir2.php?module=rekap_darah_keluar_udd"><img src="images/rekap_utd.png" alt="" /></a></li>
		<li><a href="pmikasir2.php?module=rekap_darah_terima_udd"><img src="images/rekapterimautd.png" alt="" /></a></li>
		<li><a href="pmikasir2.php?module=tambah_bdrs"><img src="images/bdrs_utility.png" alt="" /></a></li>
		<li><a href="pmikasir2.php?module=musnah"><img src="images/pemusnahan_kantong.png" alt="" /></a></li>
		<!--li><a href="pmilaboratorium.php?module=penulusuran_pasien"><img src="images/lacak_pasien.png" alt=""/></a></li>
    <!--<li><a href="pmilaboratorium.php?module=skantong0"><img src="images/pencocokan_stok.png" alt=""/></a></li>-->
		<!--<li><a href="bdrs/<?= $today ?>.zip"><img src="images/pencocokan_stok.png" alt=""/></a></li>-->
		<li><a href="pmikasir2.php?module=permintaan_bdrs_list"><img src="images/IconBDRS.png" alt="" /></a></li>
		<!-- <li><a href="pmikasir2.php?module=konsolidasi_terima"><img src="images/konsolidasi_terima.png" alt="" /></a></li>
		<li><a href="pmikasir2.php?module=konsolidasi_kirim"><img src="images/konsolidasi_kirim.png" alt="" /></a></li> -->
	</ul>
</div>