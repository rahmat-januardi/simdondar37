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
	<!--li><a href="pmikasir2.php?module=rekap_transaksi"><img src="images/rekap_transaksi0.png" alt=""/></a></li-->
	<li><a href="pmikasir2.php?module=rekap_permintaan"><img src="images/rekap_permintaan0.png" alt="" /></a></li>
    <li><a href="pmikasir2.php?module=rekap_permintaan_lama"><img src="images/rekap_permintaan_batal.png" alt="" /></a></li>
	<!--li><a href="pmikasir2.php?module=rekap_darah_keluar_lama"><img src="images/rekap_darah_klr_lama.png" alt="" /></a></li-->
    <li><a href="pmikasir2.php?module=rekap_terimasampel"><img src="images/rekap_sampel.png" alt="" /></a></li>
    <!--li><a href="pmikasir2.php?module=rsonline"><img src="images/rsonline.png" alt="" /></a></li-->
	<li><a href="pmikasir2.php?module=rekap_darah_keluar"><img src="images/rincian_darah_keluar.png" alt="" /></a></li>
	<li><a href="pmikasir2.php?module=rekap_darah_keluar1"><img src="images/rekap_darah_keluar.png" alt="" /></a></li>
	
<!--li><a href="pmikasir2.php?module=rekap_darah_keluar_bdrs"><img src="images/rekap_kirim_bdrs.png" alt="" /></a></li>
	<li><a href="pmikasir2.php?module=rekap_darah_keluar_udd"><img src="images/rekap_kirim_udd.png" alt="" /></a></li-->

    <li><a href="pmikasir2.php?module=rekap_pembayaran1"><img src="images/rekap_pembayaran.png" alt="" /></a></li>
    <li><a href="pmikasir2.php?module=rekap_pembayaranlain"><img src="images/rekap_bayar_lain.png" alt="" /></a></li>
	
<!--li><a href="pmikasir2.php?module=laporan_piagam"><img src="images/lap_ctk_piagam.png" alt=""/></a></li>
	<li><a href="pmikasir2.php?module=laporan_cetak_kartu"><img src="images/lap_ctk_kartu.png" alt=""/></a></li>
	<li><a href="pmikasir2.php?module=rekap_donor"><img src="images/rekap_donasi.png" alt=""/></a></li>
	
<!--li><a href="pmikasir2.php?module=rekap_permintaan_harian"><img src="images/rekap_permintaan0.png" alt="" /></a></li>
	<li><a href="pmikasir2.php?module=rekap_darah_keluar"><img src="images/rekap_darah_out.png" alt="" /></a></li-->
   
<!-- TPK -->
    <li><a href="pmikasir2.php?module=rekap_permintaan_tpkbelum"><img src="/images/mintadarahbelum.png" alt="" /></a></li>
    <li><a href="pmikasir2.php?module=rekap_permintaan_tpksudah"><img src="/images/mintadarahsudah.png" alt="" /></a></li>
    <li><a href="pmikasir2.php?module=cetak_ulang_kwitansi"><img src="images/kwitansi_ulang.png" alt="" /></a></li>
    <li><a href="pmikasir2.php?module=rekap_pembayaran"><img src="images/rincian_pembayaran.png" alt="" /></a></li>
    <!--li><a href="pmikasir2.php?module=rekap_permintaan_tpkbatal"><img src="/tpk/images/tpk_batal.png" alt="" /></a></li-->
    
    <!--li><a href="pmikasir2.php?module=jadwalambil"><img src="images/jadwalaph.png" alt="" /></a></li-->
</ul>
</ul>
</ul>
</div>

