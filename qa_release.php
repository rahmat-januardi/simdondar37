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
});

</script> 
</head>

<div class="container">
<ul class="thumb">
	<!--li><a href="pmiqa.php?module=release"><img src="images/qa_input.png" alt=""/></a></li-->
	<li><a href="pmiqa.php?module=timbang"><img src="images/qa_timbang.png" alt=""/></a></li>
	<li><a href="pmiqa.php?module=density"><img src="images/qa_berat_jenis.png" alt=""/></a></li>
	<li><a href="pmiqa.php?module=kantong_kosong"><img src="images/qa_kantongkosong.png" alt=""/></a></li>
	<li><a href="pmiqa.php?module=cetak_release"><img src="images/qa_releaselabel.png" alt=""/></a></li>
	<li><a href="pmiqa.php?module=rekap_release"><img src="images/qa_rekap_release.png" alt=""/></a></li>
	<li><a href="pmiqa.php?module=ubahstatus_qa"><img src="images/ubah_status_kantong.png" alt="" /></a></li>
</ul>
</div>

