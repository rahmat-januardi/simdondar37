		
		
			<font size="1"><a href="pmikasir2.php?module=permintaan1"  target="isiadmin" class="fisheyeItem"><img src="images/pasienminta.png"/>Permintaan Darah</a></font><p>
			<font size="1"><a href="pmikasir2.php?module=pasien_plebotomi" target="isiadmin" class="fisheyeItem"><img src="images/pasien_plebotomi1.png" />Pasien Plebotomi</a></font><p>

			<!--font size="1"><a href="pmikasir2.php?module=searchpasien"  target="isiadmin" class="fisheyeItem"><img src="images/pasienminta.png"/>Permintaan</a></font><p-->

			<!--font size="1"><a href="pmikasir2.php?module=double_pasien" target="isiadmin" class="fisheyeItem"><img src="images/pendonor2.png" />EDIT Pasien GANDA</a></font><p>
			<font size="1"><a href="pmikasir2.php?module=search_pendonor_calling" target="isiadmin" class="fisheyeItem"><img src="images/call_pendonor.png" />Donor Calling</a></font><p-->
			<font size="1"><a href="pmikasir2.php?module=receptionis_distribusi" target="isiadmin" class="fisheyeItem"><img src="images/distribusi_darah.png" />Distribusi Darah</a></font>
			<?php if ($_SESSION['namaudd'] == 'UDD PUSAT PMI') { ?>
		    <font size="1"><a href="pmikasir2.php?module=menu_sampel_panel" target="isiadmin" class="fisheyeItem"><img
		                src="images/sampel_panel.png" />Sampel Panel</a></font>
		    <? } ?>
			<font size="1"><a href="pmikasir2.php?module=receptionis_transaksi2" target="isiadmin" class="fisheyeItem"><img src="images/stock2.png" />Transaksi</a></font>
				<font size="1"><a href="pmikasir2.php?module=rekap" target="isiadmin" class="fisheyeItem"><img src="images/report_harian.png" />Rekap Transaksi</a></font><p>
            <font size="1"><a href="pmikasir2.php?module=tpk_menu" target="isiadmin" class="fisheyeItem"><img src="images/apheresis.png" />Apheresis</a></font>
                <font size="1"><a href="pmikasir2.php?module=rekap_catatan" target="isiadmin" class="fisheyeItem"><img src="images/permintaan_darah.png" />Catatan Petugas</a></font><p>
			<!--font size="1"><a href="pmikasir2.php?module=search_pendonor" target="isiadmin" class="fisheyeItem"><img src="images/pendonor2.png" />Pendonor</a></font><p>

			<font size="1"><a href="pmikasir2.php?module=aftap1" target="isiadmin" class="fisheyeItem"><img src="images/aftap_m.png" />Aftap</a></font><p-->
			<font size="1"><a href="pmikasir2.php?module=rekap_darah_titip" target="isiadmin" class="fisheyeItem"><img src="images/report_permintaan_darah.png" />Darah Titipan</a></font><p>
			<!--font size="1"><a href="pmikasir2.php?module=receptionis_cetak" target="isiadmin" class="fisheyeItem"><img src="images/print.png"/>Cetak</a></font-->

			<font size="1"><a href="logistik/transaksi_minta_barang.php" target="isiadmin" class="fisheyeItem"><img src="images/form_permintaan_baranghome.png" />Permintaan<br>Barang</a></font><p>
			<font size="1"><a href="pmikasir2.php?rstock=1" target="isiadmin" class="fisheyeItem"><img src="images/stok.png" />Check Stok</a></font>
			<font size="1"><a href="pmikasir2.php?rstock=3" target="isiadmin" class="fisheyeItem"><img src="images/aftap2.png" />Check Kantong</a></font>
			<font size="1"><a href="pmikasir2.php?module=ganti_passwd&act=edituser&id=<?=$_SESSION['namauser']?>" target="isiadmin" class="fisheyeItem"><img src="images/change_password.png" />Edit Profil</a></font>
			<font size="1"><a href="pmikasir2.php?module=dokumentasipasien" target="isiadmin" class="fisheyeItem"><img src="images/usermanual.png" />User Manual</a></font><p>
<? if ($_SESSION[multilevel]!='') { ?>
			<font size="1"><a href="pmikasir2.php?module=ganti_menu" target="isiadmin" class="fisheyeItem"><img src="images/multi.png" />Ganti Level</a></font>
<? } ?>
			<font size="1"><a href="javascript:logout();" class="fisheyeItem"><img src="images/out.png"/>Logout</a></font>
