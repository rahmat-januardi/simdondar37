<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser = $_SESSION[namauser];
$namalengkap = $_SESSION[nama_lengkap];
$tglsebelum = mktime(0, 0, 0, date("m"), 1, date("Y"));
$tglawal = date("Y-m-d", $tglsebelum);
$hariini = date("Y-m-d");
$sr = mysql_query("SELECT DISTINCT arc_serial FROM imltd_arc_qc");
$sn_r = mysql_fetch_assoc($sr);
$nomorseri = $sn_r['arc_serial'];
?>
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<style type="text/css">
	@import url("topstyle.css");

	tr {
		background-color: #FFF8DC
	}

	.initial {
		background-color: #FFF8DC;
		color: #000000
	}

	.normal {
		background-color: #FFF8DC
	}

	.highlight {
		background-color: #7FFF00
	}
</style>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>SIMDONDAR</title>
</head>

<body>
	<?
	if (isset($_POST[waktu])) {
		$tglawal = $_POST[waktu];
		$hariini = $hariini;
	}
	if ($_POST[waktu1] != '') $hariini = $_POST[waktu1];
	$nomorseri = $_POST['noseri'];
	?>
	<font size="4" color=00008B>DATA KONTROL Architect i2000SR</b></font><br><br>
	<form name="cari" method="POST" action="<? echo $PHPSELF ?>">
		<table class="list" cellpadding=cellspacing="0" border="0" width="750px">
			<tr class="field">
				<td align="left" nowrap>Dari tanggal :</td>
				<td><input name="waktu" id="datepicker" value="<?= $tglawal ?>" type=text size=10></td>
				<td align="right" nowrap>s/d tanggal :</td>
				<td><input name="waktu1" id="datepicker1" value="<?= $hariini ?>" type=text size=10></td>
				<td align="right" nowrap>Serial Number:</td>
				<td>
					<select name="noseri"> <?
											$qsn = "SELECT DISTINCT `arc_serial` FROM `imltd_arc_qc`";
											$do = mysql_query($qsn); ?>
						<option value="-">-</option>
						<?
						while ($data = mysql_fetch_assoc($do)) {
							if ($data[arc_serial] == $nomorseri) {
								$select = " selected";
							} else {
								$select = "";
							} ?>?>
						<option value="<?= $data[arc_serial] ?>" <?= $select ?>><?= $data[arc_serial] ?></option>
					<? } ?>
					</select>
				</td>
				<td><input type=submit name=submit class="swn_button_blue" value="Ok"></td>
			</tr>
		</table>
	</form>

	<table class="list" border=1 cellpadding=5 cellspacing=3 style="border-collapse:collapse" width="750px">
		<tr class="field">
			<td rowspan="2">NO.</td>
			<td rowspan="2">TANGGAL</td>
			<td colspan="2">HBsAgQ2</td>
			<td colspan="2">Anti-HCV</td>
			<td colspan="4">HIV Ag/Ab</td>
			<td colspan="2">Syphilis</td>
		</tr>
		<tr class="field">
			<td>NEG</td>
			<td>POS</td>
			<td>NEG</td>
			<td>POS</td>
			<td>NEG</td>
			<td>POS1</td>
			<td>POS2</td>
			<td>POS3</td>
			<td>NEG</td>
			<td>POS</td>
		</tr>

		<?php
		$no = 0;
		$c_neg = "0";
		$c_pos = "0";
		$b_neg = "0";
		$b_pos = "0";
		$s_neg = "0";
		$s_pos = "0";
		$i_neg = "0";
		$i_pos1 = "0";
		$i_pos2 = "0";
		$i_pos3 = "0";
		$sqlqc = "SELECT DISTINCT date(`run_time`) as tanggal FROM `imltd_arc_qc` WHERE date(`run_time`)>='$tglawal' and date(`run_time`)<='$hariini' AND 
    		`arc_serial` like '$nomorseri'";

		$q_qc = mysql_query($sqlqc);
		while ($tmp = mysql_fetch_assoc($q_qc)) {
			$no++;
			$sqlqc1 = "SELECT `id_tes`,`abs` FROM `imltd_arc_qc` WHERE date(`run_time`)='$tmp[tanggal]' and `arc_serial`='$nomorseri'";
			$qc = mysql_query($sqlqc1);
			while ($tmp1 = mysql_fetch_assoc($qc)) {

				switch ($tmp1['id_tes']) {
					case "anti-HCVIINEGATIVE":
						$c_neg = $tmp1['abs'];
						break;
					case "anti-HCVIIPOSITIVE":
						$c_pos = $tmp1['abs'];
						break;
					case "HBsAgQ2NEGATIVE":
						$b_neg = $tmp1['abs'];
						break;
					case "HBsAgQ2POSITIVE":
						$b_pos = $tmp1['abs'];
						break;
					case "SyphilisTPNEGATIVE":
						$s_neg = $tmp1['abs'];
						break;
					case "SyphilisTPPOSITIVE":
						$s_pos = $tmp1['abs'];
						break;
					case "_HIV Ag/AbNEGATIVE":
						$i_neg = $tmp1['abs'];
						break;
					case "_HIV Ag/AbPOSITIVE 1":
						$i_pos1 = $tmp1['abs'];
						break;
					case "_HIV Ag/AbPOSITIVE 2":
						$i_pos2 = $tmp1['abs'];
						break;
					case "_HIV Ag/AbPOSITIVE 3":
						$i_pos3 = $tmp1['abs'];
						break;
					default:
						break;
				}
			}
		?>
			<tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
				<td align="right"><?= $no . '.' ?></td>
				<td align="left"><?= $tmp['tanggal'] ?></td>
				<td align="right"><?= $b_neg ?></td>
				<td align="right"><?= $b_pos ?></td>
				<td align="right"><?= $c_neg ?></td>
				<td align="right"><?= $c_pos ?></td>
				<td align="right"><?= $i_neg ?></td>
				<td align="right"><?= $i_pos1 ?></td>
				<td align="right"><?= $i_pos2 ?></td>
				<td align="right"><?= $i_pos3 ?></td>
				<td align="right"><?= $s_neg ?></td>
				<td align="right"><?= $s_pos ?></td>
			</tr>
		<? }
		if ($no == 0) { ?>
			<tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
				<td colspan=12 align="center">Tidak ada data Kontrol Reagen Arcitect <i>i2000SR</i></td>
			<? } ?>
	</table><br>
	<a href="architec/imltd_arc_cetakqc.php?tgl1=<?= $tglawal ?>&tgl2=<?= $hariini ?>&sn=<?= $nomorseri ?>" class="swn_button_blue" target="_blank">Cetak</a>
	<a href="pmiimltd.php?module=import_arc2000" class="swn_button_blue">Kembali</a>
	<?
	?>
</body>

</html>