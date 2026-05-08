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
	<li><a href="pmitatausaha.php?module=graphdonor"><img src="images/statistik_ppdds.png" alt="" /></a></li>
	<li><a href="pmitatausaha.php?module=graphdonasi"><img src="images/statistik_donasi.png" alt="" /></a></li>
	<li><a href="pmitatausaha.php?module=graphtrendbulanan"><img src="images/statistik_trend_donasi.png" alt="" /></a></li>
    	<li><a href="pmitatausaha.php?module=graphpengujian"><img src="images/statistik_pengujian.png" alt="" /></a></li>
	<li><a href="pmitatausaha.php?module=graphkomponen"><img src="images/statistik_pengolahan.png" alt="" /></a></li>
    	<li><a href="pmitatausaha.php?module=graphdistribusi"><img src="images/statistik_distribusi.png" alt="" /></a></li></ul>
</ul>
</ul>
</div>

