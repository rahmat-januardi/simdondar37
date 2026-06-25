			<font size="1"><a href="pmiqc.php?module=register_qc" target="isiadmin" class="fisheyeItem"><img
			            src="images/update_stock.png" />Penerimaan Sampel QC</a></font>
			<p>
			    <font size="1"><a href="pmiqc.php?module=rekap_register" target="isiadmin" class="fisheyeItem"><img
			                src="images/rekap_reg.png" />Rekap Sampel QC</a></font>
			<p>
			    <font size="1"><a href="pmiqc.php?module=input_qc" target="isiadmin" class="fisheyeItem"><img
			                src="images/qc2.png" />Input QC</a></font>
			<p>
			    <font size="1"><a href="pmiqc.php?module=qc_laporan" target="isiadmin" class="fisheyeItem"><img
			                src="images/lapqc2.png" />Laporan QC</a></font>
			<p>
			    <font><a href="pmiqc.php?module=master_kantong" target="isiadmin" class="fisheyeItem"><img
			                src="images/qc_kantong_kosong.png" />Kantong Kosong</a></font>
			    <font><a href="pmiqc.php?module=density1" target="isiadmin" class="fisheyeItem"><img
			                src="images/berat_jenis.png" />Berat Jenis</a></font>
			    <font size="1"><a href="pmiqc.php?module=qc_logbook" target="isiadmin" class="fisheyeItem"><img
			                src="images/report_harian.png" />Logbook</a></font>
			<p>
			    <font size="1"><a href="pmiqc.php?module=komponen_permintaan" target="isiadmin" class="fisheyeItem"><img
			                src="images/stock2.png" />Permintaan Barang</a></font>
			    <font size="1"><a href="pmiqc.php?rstock=1" target="isiadmin" class="fisheyeItem"><img
			                src="images/stok.png" />Check Stok</a></font>
			    <font size="1"><a href="pmiqc.php?rstock=4" target="isiadmin" class="fisheyeItem"><img
			                src="images/aftap2.png" />Check Kantong</a></font>
			    <font size="1"><a href="dokumentasiqc.php" target="isiadmin" class="fisheyeItem"><img
			                src="images/manual.png" />User Manual</a></font>
			    <font size="1"><a href="pmiqc.php?module=ganti_passwd&act=edituser&id=<?= $_SESSION['namauser'] ?>"
			            target="isiadmin" class="fisheyeItem"><img src="images/change_password.png" />Edit Profil</a></font>
			    <? if ($_SESSION[multilevel] != '') { ?>
			    <font size="1"><a href="pmiqc.php?module=ganti_menu" target="isiadmin" class="fisheyeItem"><img
			                src="images/multi.png" />Ganti Level</a></font>
			    <? } ?>
			    <font size="1"><a href="javascript:logout();" class="fisheyeItem"><img src="images/out.png" />Logout</a></font>