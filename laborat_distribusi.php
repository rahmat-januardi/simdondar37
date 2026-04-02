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
<?
$today=date("Y-m-d");
?>
<div class="container">
<ul class="thumb">
	<li><a href="pmilaboratorium.php?module=crossmatch"><img src="images/crossmatch1.png" alt="" /></a></li>
        <li><a href="pmilaboratorium.php?module=label_cross"><img src="images/cetak_crossmatch.png" alt=""/></a></li>
    	<li><a href="pmilaboratorium.php?module=pindah_titipan"><img src="images/pemindahan_titipan.png" alt="" /></a></li>
	<li><a href="pmilaboratorium.php?module=pindah_stok"><img src="images/kembali_incom.png" alt="" /></a></li>

	<li><a href="pmilaboratorium.php?module=pindah_stok_salahbuang"><img src="images/salah_buang.png" alt="" /></a></li>
	<!--<li><a href="pmilaboratorium.php?module=musnah"><img src="images/pemusnahan_kantong.png" alt="" /></a></li>-->
	<!--li><a href="pmilaboratorium.php?module=form_bdrs"><img src="images/pindah_bdrs.png" alt=""/></a></li>
	<!--li><a href="pmilaboratorium.php?module=update_dari_bdrs"><img src="images/update_d_bdrs.png" alt=""/></a></li>
	<li><a href="pmilaboratorium.php?module=kembali"><img src="images/darah_kembali1.png" alt="" /></a></li>
	<li><a href="pmilaboratorium.php?module=form_udd"><img src="images/kirim_ke_utd_lain.png" alt=""/></a></li-->
	<!--li><a href="pmilaboratorium.php?module=update_dari_udd"><img src="images/update_d_utd_lain.png" alt=""/></a></li>
	<li><a href="pmilaboratorium.php?module=form_rujukan"><img src="images/kirim_sampel_rujukan.png" alt=""/></a></li>
	<li><a href="pmilaboratorium.php?module=update_dari_rujukan"><img src="images/update_hasil_rujukan.png" alt=""/></a></li>
	<li><a href="pmilaboratorium.php?module=terima_dari_utd_lain"><img src="images/terima_darah.png" alt=""/></a></li-->
	<li><a href="pmilaboratorium.php?module=penulusuran_pasien"><img src="images/lacak_pasien.png" alt=""/></a></li>
    <!--<li><a href="pmilaboratorium.php?module=skantong0"><img src="images/pencocokan_stok.png" alt=""/></a></li>-->
    <!--<li><a href="bdrs/<?=$today?>.zip"><img src="images/pencocokan_stok.png" alt=""/></a></li>-->
	<li><a href="pmilaboratorium.php?module=manual_cross"><img src="images/manual_cross.png" alt="" /></a></li>
</ul>
</div>

