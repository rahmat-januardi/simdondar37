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
	<li><a href="pmiadmin.php?module=update"><img src="images/update_simudda.png" alt=""/></a></li>
	<li><a href="pmiadmin.php?module=aturuser"><img src="images/atur_user.png" alt=""/></a></li>
	<li><a href="pmiadmin.php?module=aturberita"><img src="images/atur_berita.png" alt="" /></a></li>
 	<li><a href="pmiadmin.php?module=aturagenda"><img src="images/atur_agenda.png" alt="" /></a></li>
 	<li><a href="pmiadmin.php?module=aktif_udd"><img src="images/aktifkan_udd.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=tambah_pengumuman"><img src="images/update_pengumuman.png" alt=""/></a></li>
	<li><a href="pmiadmin.php?module=tambah_shift"><img src="images/ganti_shift.png" alt=""/></a></li>
	<li><a href="pmiadmin.php?module=tambah_bdrs"><img src="images/bdrs_utility.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=tambah_dokter_periksa"><img src="images/tambah_dokter_periksa.png" alt=""/></a></li>
	<li><a href="pmiadmin.php?module=backup_data"><img src="images/backup.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=login_data"><img src="images/data_login.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=audit_trial"><img src="images/audit_trial.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=history_revisi"><img src="images/history_revisi.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=uploadserverpmi"><img src="images/upload_to_server_pusat.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=settingserver"><img src="images/setting_server_pusat.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=updatepekerjaan"><img src="images/update_pekerjaan.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=changestatus"><img src="images/ubah_status_kantong.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=edit_kantong"><img src="images/edit_kantong.png" alt="" /></a></li>
	<li><a href="pmiadmin.php?module=edit_transaksi"><img src="images/edit_transaksi2.png" alt="" /></a></li>
</ul></div>

