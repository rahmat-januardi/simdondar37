<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_lahir_minta.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		$('#instansi').autocomplete({
			source: 'modul/suggest_user.php',
			minLength: 2
		});

		$('#cariinstansi').autocomplete({
			source: 'modul/suggest_zip.php',
			minLength: 2,
			select: function(event, ui) {
				const nama = ui.item.value;
				$("#cariinstansi").val(nama);
				$("#jadwal").submit();
			}
		});
	});

	$(function() {
		$("#butuh").datepicker({
			yearRange: '2020:3000',
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			showOn: 'focus'
		});
	});
</script>
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />

<?php
include('clogin.php');
include('config/db_connect.php');
$namauser   = $_SESSION[namauser];
$namabagian = $_SESSION[namabagian];
$levelusr   = $_SESSION[leveluser];

$today = date("Y-m-d");
?>
<?
if ($_GET[aksi] == 'hapus') {
	$hapus_dr = mysql_query("delete from kegiatan where NoTrans='$_GET[id]'");
	if ($hapus_dr) {
		echo ("Data Jadwal MU telah dihapus !!
        <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=pmip2d2s.php?module=data_jadwal_mobile\">");
	}
}
if ($_GET[aksi] == 'pilih') {
	$tgp = date("Y-m-d H:i:s");
	$mulai = date("H:i:s");
	$td0 = php_uname('n');
	$td0 = strtoupper($td0);
	$td0 = substr($td0, 0, 2);
	$ganti = mysql_query("update tempat_donor set active='0'");
	$ganti = mysql_query("update tempat_donor set active='1' where id1='$td0'");
	$hapus_dr = mysql_query("update detailinstansi set aktif='0'");
	$hapus_dr = mysql_query("update detailinstansi set aktif='1' where KodeDetail='$_GET[kd]'");
	$hapus_dr = mysql_query("update kegiatan set TglPelaksanaan='$tgp' ,mu='1',jammulai='$mulai'  where NoTrans='$_GET[id]'");
	if ($hapus_dr) {
		echo ("Instansi telah dipilih dan Tanggal Pelaksanaan telah ditentukan !!
        <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=pmip2d2s.php?module=data_jadwal_mobile\">");
	}
}
if (isset($_POST[upload1])) {
	$_POST[upload1] = "";
	$nama		= $_POST[nama];
	$jabatan	= $_POST[jabatan];
	$shift		= $_POST[shift];
	$petugasmu 	= mysql_query("insert INTO petugasmu (NoTrans,nama,jabatan,shift) values ('$_POST[notrans]','$nama','$jabatan','$shift')");
	if ($petugasmu)

		//Eksekusi SMS
		//---Minta Nama Petugas--
		$pendonor = mysql_query("select nama_lengkap,telp from user where nama_lengkap='$nama'");
	$datapendonor = mysql_fetch_assoc($pendonor);
	$idpendonor = $datapendonor[nama_lengkap];
	$tgj = $_POST[tanggal1] . ' ' . $_POST[jam];
	$namainstansimu = $_POST[namainstansi];
	//---End Minta Id pendonor untuk set data pendonor----
	//kirim ucapan tugas mobileunit
	$sapa = 'Semangat Pagi';
	$ud = mysql_fetch_assoc(mysql_query("select pesan from sms_setting where id='5'"));
	$pesan = $sapa . ' ' . $idpendonor . ', ' . $ud[pesan] . ' ' . $namainstansimu . ', pada tanggal ' . $_POST[tanggal1] . ' jam ' . $_POST[jam];
	//SMS Petugas
	//$kirim=mysql_query("insert into sms.outbox (DestinationNumber,TextDecoded,CreatorID) 
	//values ('$datapendonor[telp]','$pesan','1')");
	// WA Petugas
	$kirim = mysql_query("insert into wagw.outbox (wa_mode,wa_no,wa_text) 
				values ('0','$datapendonor[telp]','Yth. $namanya, $pesan')");

	// end ucapan

	echo ("Data Jadwal MU telah diedit !!
<meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; URL=pmip2d2s.php?module=data_jadwal_mobile&aksi=edit&id=$_POST[notrans]\">");
} else if (isset($_POST[submit1])) {
	$_POST[submit1] = "";
	if ($_GET[aksi] == 'edit1') {
		$tgj = $_POST[tanggal1] . ' ' . $_POST[jam];
		$namainstansi = $_POST[namainstansi];
		$jammulai = $_POST[jam];
		$status1 = $_POST[status];
		$estimasi = $_POST[jumlah];
		$doktermu = $_POST[dokter];
		$sopirmu = $_POST[sopir];
		$adminmu = $_POST[admin];
		$atd1mu = $_POST[atd1];
		$atd2mu = $_POST[atd2];
		$atd3mu = $_POST[atd3];
		$kendaraanmu = $_POST[kendaraan];
		$jamselesai = $_POST[jam1];
		$survey = $_POST[survei];
		$tgls = $_POST[tanggal2];
		$cp1 = $_POST[cp];
		$hpcp1 = $_POST[hpcp];
		$tempat1 = $_POST[tempat];
		$surveyor1 = $_POST[surveyor];
		$layak1 = $_POST[layak];
		$ket1 = $_POST[ket];
		//survei 0=Belum 1=Sudah	tglsurvei 	cp 	hpcp 	tempat 	surveyor 	layak
		$tambah = mysql_query("update kegiatan
			set `TglPenjadwalan`='$tgj',
			status		='$status1',
			jumlah 		='$estimasi',
			dokter 		='$doktermu',
			sopir  		='$sopirmu',
			admin  		='$adminmu',
			atd1   		='$atd1mu',
			atd2   		='$atd2mu',
			atd3   		='$atd3mu',
			kendaraan   	='$kendaraanmu',
			jammulai	='$jammulai',
			jamselesai	='$jamselesai',
			survei		='$survey',
			tglsurvei	='$tgls',
			cp		='$cp1',
			hpcp		='$hpcp1',
			tempat		='$tempat1',
			surveyor	='$surveyor1',
			layak		='$layak1',
			ket		='$ket1'
			where NoTrans='$_POST[notrans]'");
		//=======Audit Trial====================================================================================
		$log_mdl = $levelusr;
		$log_aksi = 'Edit Jadwal: ' . $_POST[notrans] . ', Instansi: ' . $namainstansi . ', Tanggal: ' . $tgj;
		include_once "user_log.php";
		//=====================================================================================================
	}
}


if ($tambah)
	echo ("Data Jadwal MU telah diedit !!
    <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; URL=pmip2d2s.php?module=data_jadwal_mobile&aksi=edit&id=$_POST[notrans]\">");

if ($_GET[aksi] == 'edit') {
	$q_edit = mysql_query("SELECT NoTrans, TglPenjadwalan, TglPelaksanaan, kodeinstansi, Status, jumlah, id_udd, lat, lng,
                        dokter, upper(sopir) as sopir, upper(atd1) as atd1, upper(atd2) as atd2, upper(atd3) as atd3, upper(admin) as admin,upper(cp) as cp,hpcp,
			jammulai,jamselesai, kendaraan, tglsurvei, tempat, surveyor, ket
			from kegiatan
			where NoTrans='$_GET[id]'
			order by TglPenjadwalan ASC");

	//$q_edit=mysql_query("select * from kegiatan where NoTrans='$_GET[id]' ORDER BY TglPenjadwalan ASC");
	$a_edit = mysql_fetch_assoc($q_edit);
	$a_edit1 = mysql_fetch_assoc(mysql_query("select * from detailinstansi where KodeDetail='$a_edit[kodeinstansi]'"));
?>
	<h1 class="table">FORM EDIT JADWAL MU</h1>
	<form method="post" action="pmip2d2s.php?module=data_jadwal_mobile&aksi=edit1">
		<table class="form" cellspacing="1" cellpadding="2">
			<tr>
				<td>
					<table>
			</tr>
			<tr>
				<td>Survei Tempat</td>
				<td><input type="checkbox" name="survei" id="survei" value="1"> Sudah</td>
			<tr>
				<script type="text/javascript">
					$(document).ready(function() {
						$('#survei').click(function() {
							if (this.checked) {
								$('#datepicker').removeAttr("disabled");
								$('#datepicker1').removeAttr("disabled");
								$('#status').removeAttr("disabled");
								$('#jam').removeAttr("disabled");
								$('#jam1').removeAttr("disabled");

								$('#instansi').removeAttr("disabled");
								$('#tempat').removeAttr("disabled");
								$('#jumlah').removeAttr("disabled");

								$('#dokter').removeAttr("disabled");
								$('#admin').removeAttr("disabled");
								$('#atd1').removeAttr("disabled");
								$('#atd2').removeAttr("disabled");
								$('#atd3').removeAttr("disabled");
								$('#sopir').removeAttr("disabled");
								$('#layak').removeAttr("disabled");
								$('#kendaraan').removeAttr("disabled");
								$('#ket').removeAttr("disabled");

								$('#cp').removeAttr("disabled");
								$('#hpcp').removeAttr("disabled");
								$('#id_mu1').removeAttr("disabled");
								$('#surveyor').removeAttr("disabled");
							} else {
								$("#datepicker").attr("disabled", true);
								$("#datepicker1").attr("disabled", true);
								$("#status").attr("disabled", true);
								$('#jam').Attr("disabled", true);
								$('#jam1').Attr("disabled", true);

								$("#instansi").attr("disabled", true);
								$("#tempat").attr("disabled", true);
								$("#jumlah").attr("disabled", true);

								$("#dokter").attr("disabled", true);
								$("#admin").attr("disabled", true);
								$("#atd1").attr("disabled", true);
								$("#atd2").attr("disabled", true);
								$("#atd3").attr("disabled", true);
								$("#sopir").attr("disabled", true);
								$("#layak").attr("disabled", true);
								$("#kendaraan").attr("disabled", true);
								$("#ket").attr("disabled", true);

								$("#cp").attr("disabled", true);
								$("#hpcp").attr("disabled", true);
								$("#id_mu1").attr("disabled", true);
								$("#surveyor").attr("disabled", true);
							}
						});
					});
				</script>
			</tr>
			<tr>
				<td>Nama Instansi</td>
				<input type=hidden name=notrans value="<?= $a_edit[NoTrans] ?>">
				<td class="input"><?= $a_edit1[nama] ?>
					<input type=hidden name=namainstansi value="<?= $a_edit1[nama] ?>">
				</td>
			</tr>
			<tr>
				<td>Alamat</td>
				<td class="input"><?= $a_edit1[alamat] ?>
				</td>

		</table>
		</td>
		<td>
			<table>
				<tr>
					<td>Tgl Survei</td>
					<td class="input">
						<input name="tanggal2" id="datepicker1" type="text" size="10" placeholder="Klik disini" value="<?= $a_edit[tglsurvei] ?>">
					</td>
				</tr>
				<tr>
					<td>Petugas Survei</td>
					<td><select name="surveyor" disabled="disabled" id="surveyor">
							<? $user1 = "select * from user order by nama_lengkap ASC";
							$do1 = mysql_query($user1);
							while ($data1 = mysql_fetch_assoc($do1)) {
								if ($data1[id_user] == $a_edit[surveyor]) $select = 'selected'; ?>
								<option value="<?= $data1[id_user] ?>" <?= $select ?>><?= $data1[id_user] ?></option><?
																																																			$select = "";
																																																		} ?>
						</select></td>
		</td>
		</tr>
		<? $tempatacara = mysql_fetch_assoc(mysql_query("select nama from detailinstansi where Kodedetail='$a_edit[tempat]'"));
		?>
		<tr>
			<td>Tempat Acara</td>
			<td><input type='text' placeholder='tempat acara ini' name='tempat' id='tempat' value="<?= $tempatacara[nama] ?>"></td>
		</tr>
		<tr>
			<td>Nama CP</td>
			<td><input type='text' placeholder='Koordinator Kegiatan ini' disabled="disabled" name='cp' id='cp' value="<?= $a_edit[cp] ?>"></td>
		</tr>
		<tr>
			<td>No HP</td>
			<td><input type='text' placeholder='NO Telp CP' disabled="disabled" name='hpcp' id='hpcp' value="<?= $a_edit[hpcp] ?>"></td>
		</tr>
		</tr>
		</table>
		</td>
		<td>
			<table>
				<tr>
					<td>Pelaksanaan</td>
					<td class="styled-select" bgcolor="#ffa688">
						<? $s1 = '';
						$s2 = '';
						$s3 = '';
						$s4 = '';

						$status2 = mysql_fetch_assoc(mysql_query("select * from kegiatan where NoTrans='$a_edit[NoTrans]'"));

						if ($status2[Status] == "0") {
							$s1 = 'selected';
						}
						if ($status2[Status] == "1") {
							$s2 = 'selected';
						}
						if ($status2[Status] == "2") {
							$s3 = 'selected';
						}
						if ($status2[Status] == "3") {
							$s4 = 'selected';
						}
						if ($status2[Status] == "4") {
							$s5 = 'selected';
						}
						//$sh1=$status2[Status]; $sh2=$status2[Status]; $sh3=$status2[Status];$sh4=$status2[Status];
						?>
						<select name="status" id='status' disabled="disabled">
							<option value="0" <?= $s1 ?>>Terjadual</option>
							<option value="1" <?= $s2 ?>>Fixed</option>
							<option value="2" <?= $s3 ?>>Selesai</option>
							<option value="3" <?= $s4 ?>>Ditunda</option>
							<option value="4" <?= $s5 ?>>Batal</option>

						</select>
					</td>
				</tr>
				<tr>
					<td>Tanggal</td>
					<td class="input">
						<input name="tanggal1" id="datepicker" type="text" size="10" value="<?= substr($a_edit[TglPenjadwalan], 0, 10) ?>">
					</td>
				</tr>
				<tr>
					<td>Jam Mulai</td>
					<td class="input">
						<input name="jam" id='jam' type="text" size="7" value="<?= $a_edit[jammulai] ?>">
					</td>
				</tr>
				<tr>

					<td> Jam Selesai</td>
					<td class="input">
						<input name="jam1" id='jam1' disabled="disabled" type="text" size="7" value="<?= $a_edit[jamselesai] ?>">
					</td>
				</tr>
				<tr>
					<td>Jumlah</td>
					<td class="input">
						<input name="jumlah" id='jumlah' disabled="disabled" type="text" size="5" value="<?= $a_edit[jumlah] ?>">
					</td>
				</tr>
			</table>
		</td>
		<!--td>
	<table>
            <tr>
		<td>Dokter</td>
		<td class="input">
                    <input name="dokter" type="text" size="15" id="dokter" disabled="disabled" value="<?= $a_edit[dokter] ?>">
		</td>
            </tr>
           
	    <tr>
               <td>Admin</td>
	       <td class="input">
                    <input name="admin" type="text" size="15" id="admin" disabled="disabled" value="<?= $a_edit[admin] ?>">
		</td>
            </tr>
	<tr>
               <td>HB</td>
		<td class="input">
                    <input name="atd1" type="text" size="15" id="atd1" disabled="disabled" value="<?= $a_edit[atd1] ?>">
		</td>
            </tr>
            <tr>
                <td>Tensi</td>
		<td class="input">
                    <input name="atd2" type="text" size="15" id="atd2" disabled="disabled" value="<?= $a_edit[atd2] ?>">
		</td>
            </tr>
	    <tr>
                <td>Aftap</td>
		<td class="input">
                    <input name="atd3" type="text" size="20" id="atd3" disabled="disabled" value="<?= $a_edit[atd3] ?>">
		</td>
            </tr>
	 <tr>
                <td>Sopir</td>
		<td class="input">
                    <input name="sopir" type="text" size="15" id="sopir" disabled="disabled" value="<?= $a_edit[sopir] ?>">
		</td>
		</td>
            </tr>
        </table>
        </td-->
		<td>
			<table>
				<tr>
					<td>Kendaraan</td>
					<td class="styled-select" bgcolor="#ffa688">
						<? $s6 = '';
						$s7 = '';
						if ($status2[kendaraan] == "0") {
							$s6 = 'selected';
						}
						if ($status2[kendaraan] == "1") {
							$s7 = 'selected';
						}
						?>
						<select name="kendaraan" id="kendaraan" disabled="disabled">
							<option value="0" <?= $s6 ?>>BUS MU</option>
							<option value="1" <?= $s7 ?>>Mobil MU</option>

						</select>
					</td>
				</tr>
				<tr>
					<td>Status</td>
					<td class="styled-select" bgcolor="#ffa688">
						<? $s8 = '';
						$s9 = '';
						if ($status2[layak] == "0") {
							$s8 = 'selected';
						}
						if ($status2[layak] == "1") {
							$s9 = 'selected';
						}
						?>
						<select name="layak" id="layak" disabled="disabled">
							<option value="0" <?= $s8 ?>>Layak</option>
							<option value="1" <?= $s9 ?>>Tidak Layak</option>

						</select>
					</td>
				</tr>




				<tr>
					<td>Ket.</td>
					<td class="input">
						<input name="ket" type="text" size="15" id="ket" disabled="disabled" value="<?= $a_edit[ket] ?>">
					</td>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		<br> <input border=0 type="submit" name="submit1" value="Simpan">

		<h1 class="table">FORM EDIT PETUGAS MU</h1>
		<!--EDIT-->

		<td>
			<table>
				<tr>

				<tr>
					<td>Nama Petugas </td>
					<td class="styled-select" bgcolor="#ffa688"><input type='text' name="nama" id='instansi' size='20'></td>
					</tr< /td>
					<td></td>



					<td>Jabatan</td>
					<td class="styled-select" bgcolor="#ffa688">
						<select name="jabatan" id="jabatan">
							<option value="">--Pilih--</option>
							<option value="1">Dokter</option>
							<option value="2">PJMU</option>
							<option value="3">Aftap</option>
							<option value="4">HB/Tensi</option>
							<option value="5">Admin</option>
							<option value="6">Driver</option>
							<option value="7">Lain-lain</option>
						</select>
					</td>
					<td></td>




					<td>Shift</td>
					<td class="styled-select" bgcolor="#ffa688">
						<select name="shift" id="shift">
							<option value="">--Pilih--</option>
							<option value="Pagi">Pagi</option>
							<option value="Siang">Siang</option>
							<option value="Malam">Malam</option>
							<option value="Libur">Libur</option>
							<option value="On Call">On Call</option>
							<option value="On Call Akb">On Call Akb</option>
							<option value="On Call PKL">On Call PKL</option>
							<option value="Relawan">Relawan</option>
						</select>
					</td>
					<td></td>

		</td>
		<td><input border=0 type="submit" name="upload1" value="Tambah"></td>
		</tr>
		</table>

		<?
		?>

		<?
		$ptgs = mysql_query("SELECT * from petugasmu where NoTrans='$_GET[id]' order by jabatan asc");
		?>
		<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="600px">
			<tr class="ui-widget-header">
				<th>No.</th>
				<th>Nama Petugas</th>
				<th>Jabatan</th>
				<th>Shift</th>
				<th>Keterangan</th>
				<th>Aksi</th>
			</tr>
			<?
			while ($a_ptgs = mysql_fetch_assoc($ptgs)) {
				$no++;
				if ($a_ptgs[jabatan] == '1') $jk = 'Dokter';
				if ($a_ptgs[jabatan] == '2') $jk = 'PJMU';
				if ($a_ptgs[jabatan] == '3') $jk = 'Aftap';
				if ($a_ptgs[jabatan] == '4') $jk = 'HB/Tensi';
				if ($a_ptgs[jabatan] == '5') $jk = 'Admin';
				if ($a_ptgs[jabatan] == '6') $jk = 'Driver';
				if ($a_ptgs[jabatan] == '7') $jk = 'Lain-lain';



				echo "<tr>";
				echo "<td >" . $no . "</td>" .
					"<td>" . $a_ptgs[nama] . "</td>" .
					"<td>" . $jk . "</td>" .
					"<td>" . $a_ptgs[shift] . "</td>" .
					"<td>" . $a_dr[ket] . "</td>";
			?>

				<td><a href=pmip2d2s.php?module=delpetugas&NoTrans=<? echo $_GET[id] ?>&petugas=<? echo $a_ptgs[nama] ?>>Hapus</a></td>

			<?

				/*echo "<td>
		<a href=\"".$PHP_SELF."?module=hapuspetugas&id=".NoTrans='$_GET[id]'."&pt=".$a_ptgs[nama]'."\">
       		<li class=\"ui-state-default ui-corner-all\" title=\"Hapus\">
       		<span class=\"ui-icon ui-icon-closethick\"></span></li>
       		</a>";
	echo "</td>";*/


				echo "</tr>";
			} ?>



		</table>

		<!--EDIT-->

	</form>

<?php
}
?>
<br>
<?php if ($_GET[aksi] != 'edit'): ?>
	<form method=post action="pmip2d2s.php?module=data_jadwal_mobile&aksi=baru" name="jadwal" id="jadwal">
		<table class="ui-widget ui-widget-content">
			<tr class="ui-widget-header">
				<td class="input">Tanggal</td>
				<td class="input"><input name=tanggal id=butuh type=text onchange="submit()" placeholder="YYYY-MM-DD" autocomplete="off"></td>
			</tr>
			<tr class="ui-widget-header">
				<td class="input">Instansi</td>
				<td class="input"><input name=cariinstansi id=cariinstansi type=text placeholder="Nama Instansi" autocomplete="off"></td>
			</tr>
		</table>
	</form>
	<? if ($_GET[aksi] == 'baru') {

		//$q_dr=mysql_query("select * from kegiatan where cast(TglPenjadwalan as date)='$_POST[tanggal]'");
		if ($_POST[tanggal]) {
			$q_dr = mysql_query("SELECT NoTrans, TglPenjadwalan, TglPelaksanaan, kodeinstansi, Status, jumlah, id_udd, lat, lng,
		dokter, upper(sopir) as sopir, upper(atd1) as atd1, upper(atd2) as atd2, upper(atd3) as atd3, upper(admin) as admin, kendaraan
from kegiatan
where cast(TglPenjadwalan as date)='$_POST[tanggal]' and Status < 4 ");
		}
		if ($_POST[cariinstansi]) {
			$q_dr = mysql_query("SELECT NoTrans, TglPenjadwalan, TglPelaksanaan, kodeinstansi, Status, jumlah, id_udd, lat, lng,
		dokter, upper(sopir) as sopir, upper(atd1) as atd1, upper(atd2) as atd2, upper(atd3) as atd3, upper(admin) as admin, kendaraan
from kegiatan
where kodeinstansi='$_POST[cariinstansi]' and Status < 4 ");
		}
	} else {
		//$q_dr=mysql_query("select * from kegiatan where  cast(TglPenjadwalan as date)>='$today' ORDER BY TglPenjadwalan ASC");

		// $q_dr=mysql_query("SELECT NoTrans, TglPenjadwalan, TglPelaksanaan, kodeinstansi, Status, jumlah, id_udd, lat, lng,
		//                         dokter, upper(sopir) as sopir, upper(atd1) as atd1, upper(atd2) as atd2, upper(atd3) as atd3, upper(admin) as admin,upper(cp) as cp,hpcp,
		// 			 kendaraan, jammulai, jamselesai,tempat,surveyor,layak, ket
		// 			from kegiatan
		// 			where cast(TglPenjadwalan as date)>='$today' and Status < 4 ORDER BY TglPenjadwalan ASC");

	}
	?>
	<?php if ($q_dr): ?>
		<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse">
			<tr class="ui-widget-header">
				<th rowspan='2'>No.</th>
				<th rowspan='2'>Tanggal</th>
				<th colspan='2'>Jam</th>
				<th rowspan='2'>Instansi</th>
				<th rowspan='2'>Tempat</th>
				<th colspan='2'>Kontak Person</th>

				<th rowspan='2'>Jumlah</th>
				<!--th rowspan='2'>Dokter</th>
	<th rowspan='2'>Sopir</th>
	<th rowspan='2'>Admin</th>
	<th rowspan='2'>HB</th>
	<th rowspan='2'>Tensi</th>
	<th rowspan='2'>Aftap</th-->
				<th rowspan='2'>Kendaraan</th>
				<th rowspan='2'>Status</th>
				<th rowspan='2'>Edit & Cetak Jadwal</th>
			</tr>
			<tr class="ui-widget-header">

				<th>Mulai</th>
				<th>Selesai</th>
				<th>Nama</th>
				<th>Telp/HP</th>
			</tr>
			<?php
			while ($a_dr = mysql_fetch_assoc($q_dr)) {
				$tgl = explode(' ', $a_dr[TglPenjadwalan]);
				$tgl1 = substr($tgl[0], 8);
				$tgl1 = (int)$tgl1;
				$tgl10 = substr($tgl[0], 5, 2);
				$tgl10 = (int)$tgl10;
				$bulan = $array_bulan[$tgl10];
				$bln = substr($tgl[0], 5, 2);
				$thn = substr($tgl[0], 0, 4);
				$th  = substr($tgl[0], 2, 2);
				$tg  = substr($tgl[0], 8, 2);
				//$tgl2=$tgl1.' '.$bulan.' '.$thn;
				$tgl2 = $tg . ' - ' . $bln . ' - ' . $thn;

				$cp = mysql_fetch_assoc(mysql_query("select * from detailinstansi where KodeDetail='$a_dr[kodeinstansi]'"));
				$no++;
				$kendaraan = "Bus MU";
				if ($a_dr[kendaraan] == "1") $kendaraan = "Mobil MU";
				$status3 = "Terjadual";
				if ($a_dr[Status] == "1") $status3 = "Fixed";
				if ($a_dr[Status] == "2") $status3 = "Selesai";
				if ($a_dr[Status] == "3") $status3 = "Ditunda";
				if ($a_dr[Status] == "4") $status3 = "Batal";
				echo "<tr>";
				echo "<td >" . $no . "</td>" .
					"<td>" . $tgl2 . "</td>" .
					//  "<td>".substr($a_dr[TglPenjadwalan],11,5)."</td>".
					"<td>" . $a_dr[jammulai] . "</td>" .
					"<td>" . $a_dr[jamselesai] . "</td>" .
					"<td>" . $cp[nama] . "</td>" .
					"<td>" . $a_dr[tempat] . "</td>" .
					"<td>" . $a_dr[cp] . "</td>" .
					"<td>" . $a_dr[hpcp] . "</td>" .
					"<td>" . $a_dr[jumlah] . "</td>" .
					/*"<td>".$a_dr[dokter]."</td>".
	    "<td>".$a_dr[sopir]."</td>".
	    "<td>".$a_dr[admin]."</td>".
	    "<td>".$a_dr[atd1]."</td>".
	    "<td>".$a_dr[atd2]."</td>".
	    "<td>".$a_dr[atd3]."</td>".*/
					"<td>" . $kendaraan . "</td>" .
					"<td>" . $status3 . "</td>";
				echo "<td>
	    <ul id=\"icons\" class=\"ui-widget ui-helper-clearfix\">";
				$td0 = php_uname('n');
				$td0 = strtoupper($td0);
				$td0 = substr($td0, 0, 1);
				if ($td0 != 'M') {
					//   echo "<a href=\"".$PHP_SELF."?module=data_jadwal_mobile&aksi=hapus&id=".$a_dr[NoTrans]."\">
					//     <li class=\"ui-state-default ui-corner-all\" title=\"Hapus\">
					//   <span class=\"ui-icon ui-icon-closethick\"></span></li>
					//</a>";
					echo "
                    <a href=\"" . $PHP_SELF . "?module=data_jadwal_mobile&aksi=edit&id=" . $a_dr[NoTrans] . "\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Ubah\">
                        <span class=\"ui-icon ui-icon-pencil\"></span></li>
                    </a>";
					echo "	    <a href=\"" . $PHP_SELF . "?module=cetak_jadwal&id=" . $a_dr[NoTrans] . "\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Cetak Jadwal\">
                        <span class=\"ui-icon ui-icon-print\"></span></li>
                    </a>";
					echo "	    <a href=\"" . $PHP_SELF . "?module=cetak_surat&id=" . $a_dr[NoTrans] . "\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Cetak Surat Tugas\">
                        <span class=\"ui-icon ui-icon-print\"></span></li>
                    </a>";
				}
				if ($td0 == 'M') {
					if ($cp[aktif] == '0') {
						echo        "<a href=\"" . $PHP_SELF . "?module=data_jadwal_mobile&aksi=pilih&kd=" . $a_dr[kodeinstansi] . "&id=" . $a_dr[NoTrans] . "\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Pilih\">
                        <span class=\"ui-icon ui-icon-unlocked\"></span></li>
                    </a>";
					} else {
						echo        "<a href=\"" . $PHP_SELF . "?module=data_jadwal_mobile&aksi=pilih0&kd=" . $a_dr[kodeinstansi] . "&id=" . $a_dr[NoTrans] . "\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Pilih\">
                        <span class=\"ui-icon ui-icon-locked\"></span></li>
                    </a>";
					}
				}
				echo "</ul>";
				echo "</td>";
				echo "</tr>";
			}
			?>
		</table>
	<?php else: ?>
		<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse">
			<tr class="ui-widget-header">
				<td colspan='2'>Silahkan masukan tanggal atau instansi untuk menampilkan hasil.</td>
			</tr>
		</table>
	<?php endif; ?>
<?php endif; ?>