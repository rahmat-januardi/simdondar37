<?php
require_once('config/db_connect.php');
session_start();
$namaudd=$_SESSION[namaudd];
$col6=mysql_query("SELECT * FROM `htransaksi` ");if ($col4){mysql_query ("ALTER TABLE `htransaksi` CHANGE `notrans` `NoTrans` VARCHAR( 25 ) NOT NULL DEFAULT '-' ");}
$col7=mysql_query("SELECT * FROM `htransaksi` WHERE `NoTrans`=' ' ");if($col7){mysql_query("DELETE FROM `htransaksi` WHERE `NoTrans` =' ' ");}	
$td0=php_uname('n');
$td0=strtoupper($td0);
$td0=substr($td0,0,1);
	if ($td0=="M") { ?>
			<font size="1"><a href="pmimobile.php?module=mobile_transfer" target="isiadmin" class="fisheyeItem"><img src="images/mobile1.png" />Transfer Data</a></font><p>
			<font size="1"><a href="pmimobile.php?module=kunci_instansi" target="isiadmin" class="fisheyeItem"><img src="images/kunci_instansi.png" width=25 height=70/>Pilih Instansi</a></font>
	<? } ?>
	<font size="1"><a href="pmimobile.php?module=pendonor" target="isiadmin" class="fisheyeItem"><img src="images/pendonor21.png" />Pendonor</a></font><p>
	<font size="1"><a href="pmimobile.php?module=check" target="isiadmin" class="fisheyeItem"><img src="images/medical_m.png" />Medical Checkup</a></font>
	<font size="1"><a href="pmimobile.php?module=spengambilan" target="isiadmin" class="fisheyeItem"><img src="images/aftap6.png" />MCU &<br>Aftap</a></font>
	<font size="1"><a href="pmimobile.php?module=mobile_transaksi" target="isiadmin" class="fisheyeItem"><img src="images/stock2.png"/>Transaksi</a></font>		
	<font size="1"><a href="pmimobile.php?module=rekap" target="isiadmin" class="fisheyeItem"><img src="images/report_harian.png" />Rekap Transaksi</a></font><p>
	<font size="1"><a href="pmimobile.php?module=mobile_cetak" target="isiadmin" class="fisheyeItem"><img src="images/print.png"/>Cetak</a></font>
	<font size="1"><a href="pmimobile.php?rstock=1" target="isiadmin" class="fisheyeItem"><img src="images/stok.png" />Check Stok</a></font>
	<font size="1"><a href="pmimobile.php?rstock=3" target="isiadmin" class="fisheyeItem"><img src="images/aftap2.png" />Check Kantong</a></font>
	<font size="1"><a href="pmimobile.php?module=ganti_passwd&act=edituser&id=<?=$_SESSION['namauser']?>" target="isiadmin" class="fisheyeItem"><img src="images/change_password.png" />Edit Profil</a></font>
<? if ($_SESSION[multilevel]!='') { ?>
	<font size="1"><a href="pmimobile.php?module=ganti_menu" target="isiadmin" class="fisheyeItem"><img src="images/multi.png" />Ganti Level</a></font>
<? } ?>
	<font size="1"><a href="javascript:logout();" class="fisheyeItem"><img src="images/out.png"/>Logout</a></font>
