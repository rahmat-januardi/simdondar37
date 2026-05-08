<link href="css/content2.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-latest.js"></script> 
<script type="text/javascript"> 
$(document).ready(function(){
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
});
</script> 
</head>

<div class="container">
	<ul class="thumb">
		<!-- <li><a href="pmilogistik.php?module=penambahan_kantong"><img src="barcodekantong/images/logistik_barcode.png" alt=""/></a></li> -->
		<li><a href="pmilogistik.php?module=pmf_barcode_auto"><img src="barcodekantong/images/logistik_barcode.png" alt=""/></a></li>
		<li><a href="pmilogistik.php?module=rekap_kantong_baru"><img src="images/rekap_kantong.png" alt=""/></a></li>
		<li><a href="pmilogistik.php?module=pengesahan_kantong"><img src="images/update_stock2.png" alt=""/></a></li>
		<li><a href="pmilogistik.php?module=rekap_pindahan_kantong"><img src="images/rekap_pindahan_kantong.png" alt=""/></a></li>
		<li><a href="pmilogistik.php?module=rekap_sisa_kantong_diaftap"><img src="images/rekap_kantong_aftap.png" alt=""/></a></li>
		<li><a href="pmilogistik.php?module=penghapusan_kantong"><img src="images/penghapusan_kantong.png" alt=""/></a></li>
		<li><a href="pmilogistik.php?module=pindah_ke_lab"><img src="images/pindahan_reagen.png" alt=""/></a></li>
		<!-- <li><a href="pmilogistik.php?module=cetakulang_barcode"><img src="images/cetak_ulang_barcode.png" alt=""/></a></li> -->
		<!-- <li><a href="pmilogistik.php?module=penambahan_kantong_apheresis"><img src="images/kantong_apheresis.png" alt=""/></a></li> -->
		<li><a href="pmilogistik.php?module=kantong_kosong"><img src="images/qa_kantongkosong.png" alt=""/></a></li>
		<li><a href="pmilogistik.php?module=samplerekap"><img src="images/spuitnew.png" alt=""/></a></li>
		<li><a href="pmilogistik.php?module=manual_logistik"><img src="images/manual_logistik.png" alt=""/></a></li>
	</ul>
</div>

