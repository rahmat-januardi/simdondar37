			<font size="1"><a href="pmiimltd.php?module=serahterima" target="isiadmin" class="fisheyeItem"><img
			            src="images/serahterima1.png" />Serah Terima</a></font>
			<p>
			    <font size="1"><a href="pmiimltd.php?module=laborat_ujisaring" target="isiadmin" class="fisheyeItem"><img
			                src="images/imltd1.png" />IMLTD</a></font>
			<p>
			    <font size="1"><a href="pmiimltd.php?module=imltd_nat" target="isiadmin" class="fisheyeItem"><img
			                src="images/nat2020.png" />IMLTD<br>NAT</a></font>
			<p>
			    <font size="1"><a href="pmiimltd.php?module=import" target="isiadmin" class="fisheyeItem"><img
			                src="images/imltd3.png" />IMLTD KONEKSI</a></font>
			<p>
			    <font size="1"><a href="pmiimltd.php?module=rekap" target="isiadmin" class="fisheyeItem"><img
			                src="images/report_harian.png" />Rekap Transaksi</a></font>
			<p>
			    <font size="1"><a href="pmiimltd.php?module=menu_sampel_panel" target="isiadmin" class="fisheyeItem"><img
			                src="images/sampel_panel.png" />Sampel Panel</a></font>
			    <font size="1"><a href="pmiimltd.php?module=komponen_musnah" target="isiadmin" class="fisheyeItem"><img
			                src="musnah/images/musnah_icon.png" />Pemusnahan Produk</a></font>
			    <font size="1"><a href="pmiimltd.php?module=imltd_permintaan" target="isiadmin" class="fisheyeItem"><img
			                src="images/stock2.png" />Permintaan Brg Lab</a></font>
			    <font size="1"><a href="pmiimltd.php?rstock=2" target="isiadmin" class="fisheyeItem"><img
			                src="images/aftap2.png" />Check Kantong</a></font>
			    <font size="1"><a href="pmiimltd.php?module=ganti_passwd&act=edituser&id=<?= $_SESSION['namauser'] ?>"
			            target="isiadmin" class="fisheyeItem"><img src="images/change_password.png" />Edit Profil</a></font>
			    <? if ($_SESSION[multilevel] != '') { ?>
			    <font size="1"><a href="pmiimltd.php?module=ganti_menu" target="isiadmin" class="fisheyeItem"><img
			                src="images/multi.png" />Ganti Level</a></font>
			    <? } ?>
			    <font size="1"><a href="javascript:logout();" class="fisheyeItem"><img src="images/out.png" />Logout</a></font>