<link href="css/content.css" rel="stylesheet" type="text/css" />
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
});

</script> 
</head>

<div class="container">
<ul class="thumb">
	<li><a href="pmiaftap.php?module=rekap_transaksi1"><img src="images/rekap_transaksi_donor.png" alt=""/></a></li>
    <li><a href="pmiaftap.php?module=rekap_transaksi2"><img src="images/rekap_transaksi0.png" alt=""/></a></li>
    <li><a href="pmiaftap.php?module=rekap_apheresis"><img src="images/rekap_epheresis.png" alt="" /></a></li>
    <li><a href="pmiaftap.php?module=laporan&jenis=7"><img src="images/rekap_bus.png" alt="" /></a></li>
    

    <li><a href="pmiaftap.php?module=rekap_donor_apheresis"><img src="images/donor_aph_aktif.png" alt=""/></a></li>
    <li><a href="pmiaftap.php?module=rekap_donor_apheresis_all"><img src="images/donor_aph.png" alt=""/></a></li>
    <li><a href="pmiaftap.php?module=rekap_transaksi"><img src="images/rekap_transaksidonor.png" alt=""/></a></li>
    <!--li><a href="pmiaftap.php?module=laporan_aftap"><img src="images/laporan_aftap.png" alt=""/></a></li-->
    <!--li><a href="pmiaftap.php?module=pmk_lap_donasi"><img src="laporan/images/laporan_bulanan_wb.png" alt="" /></a></li-->
    <!--li><a href="pmiaftap.php?module=pmk_lap_aphe"><img src="laporan/images/laporan_bulanan_aph.png" alt="" /></a></li-->
    <!--li><a href="pmiaftap.php?module=rekap_batal"><img src="images/donor_ditolak.png" alt=""/></a></li-->
    <!--li><a href="pmiaftap.php?module=rekap_validktg"><img src="aftap/validasiktg.png" alt=""/></a></li-->
    <li><a href="pmiaftap.php?module=rekap_validktg"><img src="images/verifikasiktg.png" alt="" /></a></li>

</ul>
</ul>
</ul>
</div>

