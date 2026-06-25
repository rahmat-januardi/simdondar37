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
    <li><a href="pmiaftap.php?module=dokter&jenis=0"><img src="images/periksa_dokter.png" alt=""/></a></li>
    <li><a href="pmiaftap.php?module=hb&jenis=1"><img src="images/periksa_gol.png" alt=""/></a></li>
    <!--li><a href="pmiaftap.php?module=check"><img src="images/medical_checkup.png" alt=""/></a></li-->
	<li><a href="pmiaftap.php?module=spengambilan"><img src="images/aftap_pengambilandarah1.png" alt=""/></a></li>
	<li><a href="pmiaftap.php?module=gantikantong"><img src="images/pergantian_kantong.png" alt=""/></a></li>
	<li><a href="pmiaftap.php?module=daftar_permintaan_plebotomi"><img src="images/transaksi_plebotomi.png" alt=""/></a></li>
	<!--li><a href="pmiaftap.php?module=daftar_permintaan_plebotomi"><img src="images/a.png" alt=""/></a></li-->
	<!--li><a href="pmiaftap.php?module=pengesahan_pengambilan"><img src="images/pengesahan.png" alt="" /></a></li>
	<li><a href="pmiaftap.php?module=rekap_pengesahan"><img src="images/rekap_pengesahan.png" alt="" /></a></li-->
	<li><a href="pmiaftap.php?module=epengambilan"><img src="images/pengambilan_edit.png" alt=""/></a></li>

</ul>
</ul>
</ul>
</div>

