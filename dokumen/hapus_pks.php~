<?php
	include "koneksi.php";
	
	$hapus = $_GET['hapus'];
	
	$sql="delete from pks where kontrol2='$hapus'";
	$result=mysql_query($sql);

	$hapus_riwayat=mysql_query("delete from riwayat where kontrol2='$hapus'");
	
	
	include "pks.php";
?>
