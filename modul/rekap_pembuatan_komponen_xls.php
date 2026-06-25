<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_rekap_Pembuatan_Komponen.xls");
header("Pragma: no-cache");
header("Expires: 0");
include('../config/db_connect.php');

$pertgl  = $_POST['pertgl'];
$perbln  = $_POST['perbln'];
$perthn  = $_POST['perthn'];
$pertgl1 = $_POST['pertgl1'];
$perbln1 = $_POST['perbln1'];
$perthn1 = $_POST['perthn1'];
$today   = $perthn  . "-" . $perbln  . "-" . $pertgl;
$today1  = $_POST['today1'];
$trs     = $_POST['trs1'];
$shift   = $_POST['shift'];

$filterTrans = ($trs != '') ? "AND trans = '$trs'" : "AND (trans IS NULL OR trans LIKE '%%')";
$filterShift = ($shift != '') ? "AND shift = '$shift'" : "AND (shift IS NULL OR shift LIKE '%%')";
?>
<h5>Rekap Pembuatan Komponen Dari Tanggal : <?= $pertgl ?> - <?= $perbln ?> - <?= $perthn ?> sampai <?= $pertgl1 ?> - <?= $perbln1 ?> - <?= $perthn1 ?></h5>


<!-- ============================================================ -->
<!-- TABEL 1 : PER TANGGAL PEMBUATAN                              -->
<!-- ============================================================ -->
<br>
<table border=1 cellpadding=2 cellspacing=0>
	<tr>
		<td colspan=27><b>1. Rekap Per Tanggal Pembuatan</b></td>
	</tr>
	<tr>
		<td rowspan=2>No</td>
		<th rowspan=2>Tanggal</th>
		<th colspan=17>Jenis Komponen</th>
		<th rowspan=2>JML</th>
		<th colspan=4>GOL DARAH</th>
		<th colspan=2>RH</th>
		<th rowspan=2>JML</th>
	</tr>
	<tr>
		<th>PRC</th>
		<th>TC</th>
		<th>LP</th>
		<th>FFP</th>
		<th>FP</th>
		<th>FP 24</th>
		<th>FP 24 450</th>
		<th>FP 72</th>
		<th>PRC 450</th>
		<th>PRP</th>
		<th>WB</th>
		<th>WB 450</th>
		<th>AHF</th>
		<th>WE</th>
		<th>BC</th>
		<th>PRC LEUCO</th>
		<th>TC POOL</th>
		<th>A</th>
		<th>B</th>
		<th>O</th>
		<th>AB</th>
		<th>Pos</th>
		<th>Neg</th>
	</tr>
	<?php
	$rows0 = mysql_query("SELECT DATE(tgl) as tgl FROM dpengolahan
	  WHERE Produk IS NOT NULL AND Produk != ''
	    AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
	    $filterShift $filterTrans
	  GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC");
	$no = 1;
	while ($row = mysql_fetch_assoc($rows0)) {
		$d  = $row['tgl'];
		$b  = "FROM dpengolahan WHERE DATE(tgl)='$d' AND CAST(tgl as date)>='$today' AND CAST(tgl as date)<='$today1' $filterShift $filterTrans";
		$prc    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='PRC'"), 0);
		$tc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='TC'"), 0);
		$lp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='LP'"), 0);
		$ffp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FFP'"), 0);
		$fp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FP'"), 0);
		$fp24   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FP 24'"), 0);
		$fp24x  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FP 24 450'"), 0);
		$fp72   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FP 72'"), 0);
		$prc450 = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='PRC 450'"), 0);
		$prp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='PRP'"), 0);
		$wb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='WB'"), 0);
		$wb450  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='WB 450'"), 0);
		$ahf    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='AHF'"), 0);
		$we     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='WE'"), 0);
		$bc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='BC'"), 0);
		$leu    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='PRC Leucodepleted'"), 0);
		$tcp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='TC Pooled'"), 0);
		$ga     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND goldarah='A'"), 0);
		$gb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND goldarah='B'"), 0);
		$go     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND goldarah='O'"), 0);
		$gab    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND goldarah='AB'"), 0);
		$rp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND rhesus='+'"), 0);
		$rn     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND rhesus='-'"), 0);
		$tot    = $prc + $tc + $lp + $ffp + $fp + $fp24 + $fp24x + $fp72 + $prc450 + $prp + $wb + $wb450 + $ahf + $we + $bc + $leu + $tcp;
	?>
		<tr>
			<td><?= $no++ ?></td>
			<td><?= $d ?></td>
			<td><?= $prc ?></td>
			<td><?= $tc ?></td>
			<td><?= $lp ?></td>
			<td><?= $ffp ?></td>
			<td><?= $fp ?></td>
			<td><?= $fp24 ?></td>
			<td><?= $fp24x ?></td>
			<td><?= $fp72 ?></td>
			<td><?= $prc450 ?></td>
			<td><?= $prp ?></td>
			<td><?= $wb ?></td>
			<td><?= $wb450 ?></td>
			<td><?= $ahf ?></td>
			<td><?= $we ?></td>
			<td><?= $bc ?></td>
			<td><?= $leu ?></td>
			<td><?= $tcp ?></td>
			<td><?= $tot ?></td>
			<td><?= $ga ?></td>
			<td><?= $gb ?></td>
			<td><?= $go ?></td>
			<td><?= $gab ?></td>
			<td><?= $rp ?></td>
			<td><?= $rn ?></td>
			<td><?= $rp + $rn ?></td>
		</tr>
	<?php } ?>
</table>

<br><br>

<!-- ============================================================ -->
<!-- TABEL 2 : REKAP PER ALAT (Pembeku / Pemisah / Pemutar)       -->
<!-- ============================================================ -->
<table border=1 cellpadding=2 cellspacing=0>
	<tr>
		<td colspan=21><b>2. Rekap Per Alat</b></td>
	</tr>
	<tr>
		<td rowspan=2>No</td>
		<th rowspan=2>Kategori Alat</th>
		<th rowspan=2>Nama Alat</th>
		<th colspan=17>Jenis Komponen</th>
		<th rowspan=2>TOTAL</th>
	</tr>
	<tr>
		<th>PRC</th>
		<th>TC</th>
		<th>LP</th>
		<th>FFP</th>
		<th>FP</th>
		<th>FP 24</th>
		<th>FP 24 450</th>
		<th>FP 72</th>
		<th>PRC 450</th>
		<th>PRP</th>
		<th>WB</th>
		<th>WB 450</th>
		<th>AHF</th>
		<th>WE</th>
		<th>BC</th>
		<th>PRC LEUCO</th>
		<th>TC POOL</th>
	</tr>
	<?php
	$alatQuery = mysql_query("
		SELECT 'Pembeku' as kategori, aBeku as nama_alat
		FROM dpengolahan
		WHERE aBeku IS NOT NULL AND aBeku != ''
		  AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
		  $filterShift $filterTrans
		GROUP BY aBeku

		UNION

		SELECT 'Pemisah' as kategori, aPisah as nama_alat
		FROM dpengolahan
		WHERE aPisah IS NOT NULL AND aPisah != ''
		  AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
		  $filterShift $filterTrans
		GROUP BY aPisah

		UNION

		SELECT 'Pemutar' as kategori, aPutar as nama_alat
		FROM dpengolahan
		WHERE aPutar IS NOT NULL AND aPutar != ''
		  AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
		  $filterShift $filterTrans
		GROUP BY aPutar

		ORDER BY kategori, nama_alat
	");
	$no = 1;
	while ($row = mysql_fetch_assoc($alatQuery)) {
		$kategori = $row['kategori'];
		$namaAlat = $row['nama_alat'];

		$bAlat = "FROM dpengolahan WHERE CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1' $filterShift $filterTrans";
		if ($kategori == 'Pemutar')      $bAlat .= " AND aPutar='$namaAlat'";
		elseif ($kategori == 'Pemisah')  $bAlat .= " AND aPisah='$namaAlat'";
		elseif ($kategori == 'Pembeku')  $bAlat .= " AND aBeku='$namaAlat'";

		$prc    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='PRC'"), 0);
		$tc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='TC'"), 0);
		$lp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='LP'"), 0);
		$ffp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='FFP'"), 0);
		$fp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='FP'"), 0);
		$fp24   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='FP 24'"), 0);
		$fp24x  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='FP 24 450'"), 0);
		$fp72   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='FP 72'"), 0);
		$prc450 = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='PRC 450'"), 0);
		$prp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='PRP'"), 0);
		$wb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='WB'"), 0);
		$wb450  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='WB 450'"), 0);
		$ahf    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='AHF'"), 0);
		$we     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='WE'"), 0);
		$bc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='BC'"), 0);
		$leu    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='PRC Leucodepleted'"), 0);
		$tcp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bAlat AND Produk='TC Pooled'"), 0);
		$total  = $prc + $tc + $lp + $ffp + $fp + $fp24 + $fp24x + $fp72 + $prc450 + $prp + $wb + $wb450 + $ahf + $we + $bc + $leu + $tcp;
	?>
		<tr>
			<td><?= $no++ ?></td>
			<td><?= $kategori ?></td>
			<td><?= $namaAlat ?></td>
			<td><?= $prc ?></td>
			<td><?= $tc ?></td>
			<td><?= $lp ?></td>
			<td><?= $ffp ?></td>
			<td><?= $fp ?></td>
			<td><?= $fp24 ?></td>
			<td><?= $fp24x ?></td>
			<td><?= $fp72 ?></td>
			<td><?= $prc450 ?></td>
			<td><?= $prp ?></td>
			<td><?= $wb ?></td>
			<td><?= $wb450 ?></td>
			<td><?= $ahf ?></td>
			<td><?= $we ?></td>
			<td><?= $bc ?></td>
			<td><?= $leu ?></td>
			<td><?= $tcp ?></td>
			<td><?= $total ?></td>
		</tr>
	<?php } ?>
</table>

<br><br>

<!-- ============================================================ -->
<!-- TABEL 3 : REKAP PER GOLONGAN DARAH                           -->
<!-- ============================================================ -->
<table border=1 cellpadding=2 cellspacing=0>
	<tr>
		<td colspan=20><b>3. Rekap Per Golongan Darah</b></td>
	</tr>
	<tr>
		<th>GOL. DARAH</th>
		<th>PRC</th>
		<th>TC</th>
		<th>LP</th>
		<th>FFP</th>
		<th>FP</th>
		<th>FP 24</th>
		<th>FP 24 450</th>
		<th>FP 72</th>
		<th>PRC 450</th>
		<th>PRP</th>
		<th>WB</th>
		<th>WB 450</th>
		<th>AHF</th>
		<th>WE</th>
		<th>BC</th>
		<th>PRC LEUCO</th>
		<th>TC POOL</th>
		<th>RHESUS +</th>
		<th>RHESUS -</th>
	</tr>
	<?php
	$golList = array('A', 'B', 'O', 'AB');
	$totGol  = array(
		'prc' => 0,
		'tc' => 0,
		'lp' => 0,
		'ffp' => 0,
		'fp' => 0,
		'fp24' => 0,
		'fp24x' => 0,
		'fp72' => 0,
		'prc450' => 0,
		'prp' => 0,
		'wb' => 0,
		'wb450' => 0,
		'ahf' => 0,
		'we' => 0,
		'bc' => 0,
		'leu' => 0,
		'tcp' => 0,
		'rp' => 0,
		'rn' => 0
	);

	foreach ($golList as $gol) {
		$bGol   = "FROM dpengolahan WHERE CAST(tgl as date)>='$today' AND CAST(tgl as date)<='$today1' $filterShift $filterTrans AND goldarah='$gol'";
		$prc    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='PRC'"), 0);
		$tc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='TC'"), 0);
		$lp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='LP'"), 0);
		$ffp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='FFP'"), 0);
		$fp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='FP'"), 0);
		$fp24   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='FP 24'"), 0);
		$fp24x  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='FP 24 450'"), 0);
		$fp72   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='FP 72'"), 0);
		$prc450 = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='PRC 450'"), 0);
		$prp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='PRP'"), 0);
		$wb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='WB'"), 0);
		$wb450  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='WB 450'"), 0);
		$ahf    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='AHF'"), 0);
		$we     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='WE'"), 0);
		$bc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='BC'"), 0);
		$leu    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='PRC Leucodepleted'"), 0);
		$tcp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND Produk='TC Pooled'"), 0);
		$rp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND rhesus='+'"), 0);
		$rn     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $bGol AND rhesus='-'"), 0);

		$totGol['prc']    += $prc;
		$totGol['tc']     += $tc;
		$totGol['lp']     += $lp;
		$totGol['ffp']    += $ffp;
		$totGol['fp']     += $fp;
		$totGol['fp24']   += $fp24;
		$totGol['fp24x']  += $fp24x;
		$totGol['fp72']   += $fp72;
		$totGol['prc450'] += $prc450;
		$totGol['prp']    += $prp;
		$totGol['wb']     += $wb;
		$totGol['wb450']  += $wb450;
		$totGol['ahf']    += $ahf;
		$totGol['we']     += $we;
		$totGol['bc']     += $bc;
		$totGol['leu']    += $leu;
		$totGol['tcp']    += $tcp;
		$totGol['rp']     += $rp;
		$totGol['rn']     += $rn;
	?>
		<tr>
			<td>Gol. <?= $gol ?></td>
			<td><?= $prc ?></td>
			<td><?= $tc ?></td>
			<td><?= $lp ?></td>
			<td><?= $ffp ?></td>
			<td><?= $fp ?></td>
			<td><?= $fp24 ?></td>
			<td><?= $fp24x ?></td>
			<td><?= $fp72 ?></td>
			<td><?= $prc450 ?></td>
			<td><?= $prp ?></td>
			<td><?= $wb ?></td>
			<td><?= $wb450 ?></td>
			<td><?= $ahf ?></td>
			<td><?= $we ?></td>
			<td><?= $bc ?></td>
			<td><?= $leu ?></td>
			<td><?= $tcp ?></td>
			<td><?= $rp ?></td>
			<td><?= $rn ?></td>
		</tr>
	<?php } ?>
	<tr>
		<td><b>JUMLAH</b></td>
		<td><b><?= $totGol['prc'] ?></b></td>
		<td><b><?= $totGol['tc'] ?></b></td>
		<td><b><?= $totGol['lp'] ?></b></td>
		<td><b><?= $totGol['ffp'] ?></b></td>
		<td><b><?= $totGol['fp'] ?></b></td>
		<td><b><?= $totGol['fp24'] ?></b></td>
		<td><b><?= $totGol['fp24x'] ?></b></td>
		<td><b><?= $totGol['fp72'] ?></b></td>
		<td><b><?= $totGol['prc450'] ?></b></td>
		<td><b><?= $totGol['prp'] ?></b></td>
		<td><b><?= $totGol['wb'] ?></b></td>
		<td><b><?= $totGol['wb450'] ?></b></td>
		<td><b><?= $totGol['ahf'] ?></b></td>
		<td><b><?= $totGol['we'] ?></b></td>
		<td><b><?= $totGol['bc'] ?></b></td>
		<td><b><?= $totGol['leu'] ?></b></td>
		<td><b><?= $totGol['tcp'] ?></b></td>
		<td><b><?= $totGol['rp'] ?></b></td>
		<td><b><?= $totGol['rn'] ?></b></td>
	</tr>
</table>

<br><br>

<!-- ============================================================ -->
<!-- TABEL 4 : PER JENIS KANTONG                                  -->
<!-- ============================================================ -->
<table border=1 cellpadding=2 cellspacing=0>
	<tr>
		<td colspan=19><b>4. Rekap Per Jenis Kantong</b></td>
	</tr>
	<tr>
		<td rowspan=2>No</td>
		<th rowspan=2>Jenis Kantong</th>
		<th colspan=17>Jenis Komponen</th>
		<th rowspan=2>JML</th>
	</tr>
	<tr>
		<th>PRC</th>
		<th>TC</th>
		<th>LP</th>
		<th>FFP</th>
		<th>FP</th>
		<th>FP 24</th>
		<th>FP 24 450</th>
		<th>FP 72</th>
		<th>PRC 450</th>
		<th>PRP</th>
		<th>WB</th>
		<th>WB 450</th>
		<th>AHF</th>
		<th>WE</th>
		<th>BC</th>
		<th>PRC LEUCO</th>
		<th>TC POOL</th>
	</tr>
	<?php
	$rows6   = mysql_query("SELECT jenis FROM dpengolahan
	  WHERE Produk IS NOT NULL AND Produk != ''
	    AND jenis IS NOT NULL AND jenis != ''
	    AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
	    $filterShift $filterTrans
	  GROUP BY jenis ORDER BY jenis ASC");
	$no      = 1;
	$jenisMap = array('1' => 'Single', '2' => 'Double', '3' => 'Triple', '4' => 'Quadruple', '6' => 'Pediatrik');

	while ($row = mysql_fetch_assoc($rows6)) {
		$j     = $row['jenis'];
		$label = isset($jenisMap[$j]) ? $jenisMap[$j] : "Jenis $j";
		$b     = "FROM dpengolahan WHERE CAST(tgl as date)>='$today' AND CAST(tgl as date)<='$today1' AND jenis='$j' $filterShift $filterTrans";
		$prc    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='PRC'"), 0);
		$tc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='TC'"), 0);
		$lp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='LP'"), 0);
		$ffp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FFP'"), 0);
		$fp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FP'"), 0);
		$fp24   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FP 24'"), 0);
		$fp24x  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FP 24 450'"), 0);
		$fp72   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='FP 72'"), 0);
		$prc450 = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='PRC 450'"), 0);
		$prp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='PRP'"), 0);
		$wb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='WB'"), 0);
		$wb450  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='WB 450'"), 0);
		$ahf    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='AHF'"), 0);
		$we     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='WE'"), 0);
		$bc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='BC'"), 0);
		$leu    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='PRC Leucodepleted'"), 0);
		$tcp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b AND Produk='TC Pooled'"), 0);
		$tot    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $b"), 0);
	?>
		<tr>
			<td><?= $no++ ?></td>
			<td><?= $label ?></td>
			<td><?= $prc ?></td>
			<td><?= $tc ?></td>
			<td><?= $lp ?></td>
			<td><?= $ffp ?></td>
			<td><?= $fp ?></td>
			<td><?= $fp24 ?></td>
			<td><?= $fp24x ?></td>
			<td><?= $fp72 ?></td>
			<td><?= $prc450 ?></td>
			<td><?= $prp ?></td>
			<td><?= $wb ?></td>
			<td><?= $wb450 ?></td>
			<td><?= $ahf ?></td>
			<td><?= $we ?></td>
			<td><?= $bc ?></td>
			<td><?= $leu ?></td>
			<td><?= $tcp ?></td>
			<td><?= $tot ?></td>
		</tr>
	<?php } ?>
</table>

<?php mysql_close(); ?>