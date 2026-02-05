			<font size="1"><a href="../dokumen/akses-user/dokumen-user.php" target="isiadmin" class="fisheyeItem"
					title="Document"><img src="dokumen/images/cari.png" />E-DOCUMENT</a></font>
			<font size="1"><a href="../dokumen/panduan_edokumen.php" target="isiadmin" class="fisheyeItem" title="Document"><img
						src="dokumen/images/panduan_edokumen.png" />Panduan E-Dokumen</a></font>
			<!-- <font size="1"><a href="../dokumen/akses-user/dokumen-eform-user.php" target="isiadmin" class="fisheyeItem" title="Document"><img src="dokumen/images/eform.png"/>E-FORMULIR</a></font> -->
			<!--
<font size="1"><a href="../dokumen/akses-user/kebijakan-user.php" target="isiadmin" class="fisheyeItem" title="Kebijakan"><img src="dokumen/images/kebijakan1.png"/>KEBIJAKAN</a></font>
			<font size="1"><a href="../dokumen/akses-user/pks-user.php" target="isiadmin" class="fisheyeItem" title="Standar Prosedur Operasional"><img src="dokumen/images/L2-new1.png" />SPO</a></font>
			<font size="1"><a href="../dokumen/akses-user/ik-user.php" target="isiadmin" class="fisheyeItem" title="Instruksi Kerja"><img src="dokumen/images/L3-new1.png" />IK</a></font>
			<font size="1"><a href="../dokumen/akses-user/ika-user.php" target="isiadmin" class="fisheyeItem" title="Instruksi Kerja Alat"><img src="dokumen/images/L3-new1.png" />IK ALAT</a></font>
			<font size="1"><a href="../dokumen/akses-user/pendukung-user.php" target="isiadmin" class="fisheyeItem" title="dokumen-user Pendukung"><img src="dokumen/images/L3-new1.png" />DOK.PD</a></font>
			<font size="1"><a href="../dokumen/akses-user/formulir-user.php" target="isiadmin" class="fisheyeItem" title="Formulir"><img src="dokumen/images/L4-new.png" />FORMULIR</a></font>
			<font size="1"><a href="../dokumen/akses-user/eksternal-user.php" target="isiadmin" class="fisheyeItem" title="dokumen-user Eksternal"><img src="dokumen/images/L5-new.png" />DOK.EXT</a></font>

-->

			<font size="1"><a href="pmidokumen.php?module=ganti_passwd&act=edituser&id=<?= $_SESSION['namauser'] ?>"
			        target="isiadmin" class="fisheyeItem"><img src="images/change_password.png" />Edit Profil</a></font>

			<? if ($_SESSION[multilevel] != '') { ?>
			<font size="1"><a href="pmidokumen.php?module=ganti_menu" target="isiadmin" class="fisheyeItem"><img
			            src="images/multi.png" />Ganti Level</a></font>
			<? } ?>
			<font size="1"><a href="javascript:logout();" class="fisheyeItem"><img src="images/out.png" />Logout</a></font>