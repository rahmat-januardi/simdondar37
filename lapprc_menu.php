<?php
require_once('config/db_connect.php');
session_start();
$namaudd=$_SESSION[namaudd];

//hapus table htranspermintaan lama
$qa=mysql_query("SELECT `notrans` FROM `qc`");if(!$qa){
//buat tabel htranspermintaan baru
mysql_query("CREATE TABLE  `qc` (
 `notrans` varchar(15) NOT NULL DEFAULT '',
  `nokantong` varchar(15) NOT NULL DEFAULT '',
  `merk` varchar(10) DEFAULT NULL,
  `gol_darah` varchar(3) DEFAULT NULL,
  `RhesusDrh` varchar(2) DEFAULT NULL,
  `produk` varchar(10) DEFAULT NULL,
  `tglpengolahan` datetime NOT NULL,
  `kadaluwarsa` datetime NOT NULL,
  `tglperiksa` date NOT NULL,
  `berat` varchar(8) NOT NULL,
  `volume` varchar(8) NOT NULL,
  `swirling` varchar(8) DEFAULT NULL,
  `ph` varchar(8) DEFAULT NULL,
  `jmltrombosit` varchar(10) DEFAULT NULL,
  `inspeksihemolisis` varchar(5) DEFAULT NULL,
  `hematokrit` varchar(8) DEFAULT NULL,
  `hemoglobin` varchar(5) DEFAULT NULL,
  `lekosit` varchar(5) DEFAULT NULL,
  `trombosit` varchar(8) DEFAULT NULL,
  `sdm` varchar(8) DEFAULT NULL,
  `vaktorviii` varchar(10) DEFAULT NULL,
  `aerob` varchar(10) NOT NULL,
  `anaerob` varchar(10) NOT NULL,
  `inputer` varchar(50) NOT NULL,
  `pengesah` varchar(50) NOT NULL,
  `bulanqc` varchar(3) NOT NULL,
  `tahunqc` year(4) NOT NULL,
  `tgl_input` date NOT NULL,
  PRIMARY KEY (`notrans`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;" );}
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
	
	<li><a href="pmiqc.php?module=hasilqc-prc"><img src="images/lap-prc.png" alt=""/></a></li>
	<li><a href="pmiqc.php?module=hasilqc-prc2"><img src="images/lap-prc2.png" alt=""/></a></li>
	<li><a href="pmiqc.php?module=hasilqc-prc3"><img src="images/lap-prc3" alt=""/></a></li>
	
		
	
	

</ul>
</ul>
</ul>
</div>

