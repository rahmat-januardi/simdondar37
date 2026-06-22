			<!--font size="1"><a href="pmikomponen.php?module=sahkan_kantong" target="isiadmin" class="fisheyeItem"><img src="images/pengesahan3.png" />Pengesahan</a></font-->
			<font size="1"><a href="pmikomponen.php?module=serahterima" target="isiadmin" class="fisheyeItem"><img
			            src="images/serahterima1.png" />Serah Terima Kantong</a></font>
			<font size="1"><a href="pmikomponen.php?module=laborat_komponen" target="isiadmin" class="fisheyeItem"><img
			            src="images/update_stock.png" />Pembuatan Komponen</a></font>
			<p>

			    <font size="1"><a href="pmikomponen.php?module=komponen_musnah" target="isiadmin" class="fisheyeItem"><img
			                src="musnah/images/musnah_icon.png" />Pemusnahan Produk</a></font>
			    <font size="1"><a href="pmikomponen.php?module=komponen_permintaan" target="isiadmin" class="fisheyeItem"><img
			                src="images/stock2.png" />Permintaan Lab</a></font>
			    <font size="1"><a href="pmikomponen.php?module=komponen_distribusi" target="isiadmin" class="fisheyeItem"><img
			                src="images/distribusi_darah.png" />Distribusi Darah</a></font>
			    <font size="1"><a href="pmikomponen.php?rstock=1" target="isiadmin" class="fisheyeItem"><img
			                src="images/stok.png" />Check Stok</a></font>
			    <font size="1"><a href="pmikomponen.php?rstock=3" target="isiadmin" class="fisheyeItem"><img
			                src="images/aftap2.png" />Check Kantong</a></font>
			    <font size="1"><a href="pmikomponen.php?module=ganti_passwd&act=edituser&id=<?= $_SESSION['namauser'] ?>"
			            target="isiadmin" class="fisheyeItem"><img src="images/change_password.png" />Edit Profil</a></font>
			    <? if ($_SESSION[multilevel] != '') { ?>
			    <font size="1"><a href="pmikomponen.php?module=ganti_menu" target="isiadmin" class="fisheyeItem"><img
			                src="images/multi.png" />Ganti Level</a></font>
			    <? } ?>
			    <font size="1"><a href="javascript:logout();" class="fisheyeItem"><img src="images/out.png" />Logout</a></font>