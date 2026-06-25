<?php
	include "koneksi.php";
	
	$hapus = $_GET['hapus'];
	
	$sql="delete from ika where kontrol2='$hapus'";
	$result=mysql_query($sql);

	$hapus_nomor=mysql_query("delete from master_nomorik where kontrol2='$hapus'");

	$hapus_riwayat=mysql_query("delete from riwayat where kontrol2='$hapus'");
	
	include "ika.php";
?>
