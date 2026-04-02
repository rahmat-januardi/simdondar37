			<font size="1"><a href="pmilaboratorium.php?module=rekap_darah_titip" target="isiadmin" class="fisheyeItem"><img src="images/report_permintaan_darah.png" />Darah Titipan</a></font><p>
			<font size="1"><a href="pmilaboratorium.php?module=laborat_distribusi" target="isiadmin" class="fisheyeItem"><img src="images/distribusi_darah.png" />Distribusi Darah</a></font>
			<font size="1"><a href="pmilaboratorium.php?module=laborat_musnah" target="isiadmin" class="fisheyeItem"><img src="musnah/images/musnah_icon.png" />Pemusnahan Produk</a></font>
			<font size="1"><a href="pmilaboratorium.php?module=rekap" target="isiadmin" class="fisheyeItem"><img src="images/report_harian.png" />Rekap Transaksi</a></font><p>
			<font size="1"><a href="pmilaboratorium.php?module=laborat_permintaan" target="isiadmin" class="fisheyeItem"><img src="images/stock2.png" />Permintaan Brg Lab</a></font>
			<font size="1"><a href="pmilaboratorium.php?rstock=1" target="isiadmin" class="fisheyeItem"><img src="images/stok.png" />Check Stok</a></font>
			<font size="1"><a href="pmilaboratorium.php?rstock=3" target="isiadmin" class="fisheyeItem"><img src="images/aftap2.png" />Check Nokantong</a></font>
			<font size="1"><a href="pmilaboratorium.php?module=ganti_passwd&act=edituser&id=<?=$_SESSION['namauser']?>" target="isiadmin" class="fisheyeItem"><img src="images/change_password.png" />Edit Profil</a></font>
<? if ($_SESSION[multilevel]!='') { ?>
			<font size="1"><a href="pmilaboratorium.php?module=ganti_menu" target="isiadmin" class="fisheyeItem"><img src="images/multi.png" />Ganti Level</a></font>
<? } ?>
			<font size="1"><a href="javascript:logout();" class="fisheyeItem"><img src="images/out.png"/>Logout</a></font>



