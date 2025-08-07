			<font size="1"><a href="../dokumen/kebijakan.php" target="isiadmin" class="fisheyeItem" title="Kebijakan"><img
			            src="dokumen/images/kebijakan1.png" />KEBIJAKAN</a></font>
			<font size="1"><a href="../dokumen/pks.php" target="isiadmin" class="fisheyeItem"
			        title="Standar Prosedur Operasional"><img src="dokumen/images/L2-new1.png" />SPO</a></font>
			<font size="1"><a href="../dokumen/ik.php" target="isiadmin" class="fisheyeItem" title="Instruksi Kerja"><img
			            src="dokumen/images/L3-new1.png" />IK</a></font>
			<font size="1"><a href="../dokumen/ika.php" target="isiadmin" class="fisheyeItem" title="Instruksi Kerja Alat"><img
			            src="dokumen/images/L3-new1.png" />IK ALAT</a></font>
			<font size="1"><a href="../dokumen/pendukung.php" target="isiadmin" class="fisheyeItem"
			        title="Dokumen Pendukung"><img src="dokumen/images/L3-new1.png" />DOK.PD</a></font>
			<font size="1"><a href="../dokumen/formulir.php" target="isiadmin" class="fisheyeItem" title="Formulir"><img
			            src="dokumen/images/L4-new.png" />FORMULIR</a></font>
			<font size="1"><a href="../dokumen/eksternal.php" target="isiadmin" class="fisheyeItem"
			        title="Dokumen Eksternal"><img src="dokumen/images/L5-new.png" />DOK.EX</a></font>
			<font size="1"><a href="../dokumen/lacakdokumen.php" target="isiadmin" class="fisheyeItem"
			        title="Dokumen Eksternal"><img src="dokumen/images/logAktivitas.png" />Lacak Dokumen</a></font>
			<!-- <font size="1"><a href="../dokumen/eformulir.php" target="isiadmin" class="fisheyeItem" title="Dokumen Eksternal"><img src="dokumen/images/eform.png" />Master E-Formulir</a></font> -->
			<font size="1"><a href="../dokumen/master_bidang.php" target="isiadmin" class="fisheyeItem"
			        title="Master Bidang"><img src="dokumen/images/master_bidang.png" />Master Bidang</a></font>
			<font size="1"><a href="../dokumen/petugas_dokumen.php" target="isiadmin" class="fisheyeItem" title="Kebijakan"><img
			            src="dokumen/images/pegawai.png" />PETUGAS</a></font>


			<font size="1"><a href="pmimk.php?module=ganti_passwd&act=edituser&id=<?= $_SESSION['namauser'] ?>" target="isiadmin"
			        class="fisheyeItem"><img src="images/change_password.png" />Edit Profil</a></font>
			<!--<font size="1"><a href="pmimk.php?module=wa_support" target="isiadmin" class="fisheyeItem"><img src="maintenance/images/support2.png" />Tiket Support</a></font>-->
			<? if ($_SESSION[multilevel] != '') { ?>
			<font size="1"><a href="pmimk.php?module=ganti_menu" target="isiadmin" class="fisheyeItem"><img
			            src="images/multi.png" />Ganti Level</a></font>
			<? } ?>
			<font size="1"><a href="javascript:logout();" class="fisheyeItem"><img src="images/out.png" />Logout</a></font>