<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>

<?php
include('config/db_connect.php');
$today  = date('Y-m-d');
$today1 = $today;

$shift = isset($_POST['gol_shift']) ? $_POST['gol_shift'] : '';
$trs   = (isset($_POST['transaksi']) && $_POST['transaksi'] != '') ? $_POST['transaksi'] : '';

if (isset($_POST['minta1']) && $_POST['minta1'] != '') {
	$today  = $_POST['minta1'];
	$today1 = $today;
}
if (isset($_POST['minta2']) && $_POST['minta2'] != '') {
	$today1 = $_POST['minta2'];
}

$perbln  = substr($today,  5, 2);
$pertgl  = substr($today,  8, 2);
$perthn  = substr($today,  0, 4);
$perbln1 = substr($today1, 5, 2);
$pertgl1 = substr($today1, 8, 2);
$perthn1 = substr($today1, 0, 4);

$filterTrans = ($trs != '')
	? "AND trans = '$trs'"
	: "AND (trans IS NULL OR trans LIKE '%%')";

$filterShift = ($shift != '')
	? "AND shift = '$shift'"
	: "AND (shift IS NULL OR shift LIKE '%%')";

$shiftLabel = $shift == '' ? 'Semua Shift' : 'Shift ' . $shift;
$transLabel = $trs   == '' ? 'Semua Transaksi' : 'No. Trans ' . $trs;
?>

<style>
	*,
	*::before,
	*::after {
		box-sizing: border-box;
		margin: 0;
		padding: 0;
	}

	:root {
		--pmi-red: #D0021B;
		--pmi-red-dark: #A80016;
		--pmi-red-light: #FF1A35;
		--pmi-red-soft: #FFF0F2;
		--pmi-red-mid: #FFD6DA;
		--white: #FFFFFF;
		--gray-50: #F9FAFB;
		--gray-100: #F3F4F6;
		--gray-200: #E5E7EB;
		--gray-400: #9CA3AF;
		--gray-600: #4B5563;
		--gray-800: #1F2937;
		--shadow-sm: 0 1px 3px rgba(0, 0, 0, .08), 0 1px 2px rgba(0, 0, 0, .05);
		--shadow-md: 0 4px 16px rgba(0, 0, 0, .10);
		--shadow-lg: 0 8px 32px rgba(0, 0, 0, .12);
		--radius: 12px;
		--radius-sm: 8px;
	}

	body,
	.rekap-wrap * {
		font-family: 'Plus Jakarta Sans', sans-serif;
	}

	.rekap-wrap {
		background: var(--gray-50);
		min-height: 100vh;
		padding: 0 0 48px;
	}

	/* ── PAGE HEADER ── */
	.rekap-hero {
		background: linear-gradient(135deg, var(--pmi-red-dark) 0%, var(--pmi-red) 60%, var(--pmi-red-light) 100%);
		padding: 28px 32px 36px;
		position: relative;
		overflow: hidden;
	}

	.rekap-hero::before {
		content: '';
		position: absolute;
		top: -40px;
		right: -40px;
		width: 220px;
		height: 220px;
		border-radius: 50%;
		background: rgba(255, 255, 255, .07);
	}

	.rekap-hero::after {
		content: '';
		position: absolute;
		bottom: -60px;
		left: 30%;
		width: 160px;
		height: 160px;
		border-radius: 50%;
		background: rgba(255, 255, 255, .05);
	}

	.rekap-hero-inner {
		position: relative;
		z-index: 1;
	}

	.rekap-hero h1 {
		color: var(--white);
		font-size: 20px;
		font-weight: 800;
		letter-spacing: -0.3px;
		line-height: 1.3;
	}

	.rekap-hero-sub {
		margin-top: 8px;
		display: flex;
		gap: 10px;
		flex-wrap: wrap;
	}

	.rekap-badge {
		display: inline-flex;
		align-items: center;
		gap: 5px;
		background: rgba(255, 255, 255, .18);
		border: 1px solid rgba(255, 255, 255, .25);
		color: var(--white);
		font-size: 12px;
		font-weight: 600;
		padding: 4px 10px;
		border-radius: 99px;
		backdrop-filter: blur(4px);
	}

	.rekap-badge svg {
		width: 13px;
		height: 13px;
		opacity: .85;
	}

	/* ── FILTER CARD ── */
	.rekap-body {
		padding: 24px 32px 0;
	}

	.filter-card {
		background: var(--white);
		border-radius: var(--radius);
		box-shadow: var(--shadow-sm);
		border: 1px solid var(--gray-200);
		padding: 20px 24px;
		margin-bottom: 24px;
	}

	.filter-card-title {
		font-size: 12px;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: .08em;
		color: var(--gray-400);
		margin-bottom: 14px;
	}

	.filter-row {
		display: flex;
		gap: 12px;
		flex-wrap: wrap;
		align-items: flex-end;
	}

	.filter-group {
		display: flex;
		flex-direction: column;
		gap: 5px;
	}

	.filter-group label {
		font-size: 12px;
		font-weight: 600;
		color: var(--gray-600);
	}

	.filter-group input[type=text],
	.filter-group select {
		height: 38px;
		padding: 0 12px;
		border: 1.5px solid var(--gray-200);
		border-radius: var(--radius-sm);
		font-size: 13px;
		font-family: inherit;
		color: var(--gray-800);
		background: var(--gray-50);
		outline: none;
		transition: border-color .15s;
		min-width: 120px;
	}

	.filter-group input[type=text]:focus,
	.filter-group select:focus {
		border-color: var(--pmi-red);
		background: var(--white);
	}

	.btn-submit {
		height: 38px;
		padding: 0 22px;
		background: var(--pmi-red);
		color: var(--white);
		border: none;
		border-radius: var(--radius-sm);
		font-size: 13px;
		font-weight: 700;
		font-family: inherit;
		cursor: pointer;
		transition: background .15s, transform .1s;
		display: inline-flex;
		align-items: center;
		gap: 6px;
		align-self: flex-end;
	}

	.btn-submit:hover {
		background: var(--pmi-red-dark);
		transform: translateY(-1px);
	}

	.btn-submit:active {
		transform: translateY(0);
	}

	/* ── SECTION CARD ── */
	.section-card {
		background: var(--white);
		border-radius: var(--radius);
		box-shadow: var(--shadow-md);
		border: 1px solid var(--gray-200);
		margin-bottom: 24px;
		overflow: hidden;
		animation: fadeUp .35s ease both;
	}

	@keyframes fadeUp {
		from {
			opacity: 0;
			transform: translateY(12px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.section-card:nth-child(2) {
		animation-delay: .05s;
	}

	.section-card:nth-child(3) {
		animation-delay: .10s;
	}

	.section-card:nth-child(4) {
		animation-delay: .15s;
	}

	.section-header {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 16px 20px;
		background: var(--pmi-red-soft);
		border-bottom: 2px solid var(--pmi-red-mid);
	}

	.section-icon {
		width: 36px;
		height: 36px;
		background: var(--pmi-red);
		border-radius: var(--radius-sm);
		display: flex;
		align-items: center;
		justify-content: center;
		flex-shrink: 0;
	}

	.section-icon svg {
		width: 18px;
		height: 18px;
		fill: var(--white);
	}

	.section-title {
		font-size: 14px;
		font-weight: 800;
		color: var(--pmi-red-dark);
		letter-spacing: -0.2px;
	}

	.section-subtitle {
		font-size: 11px;
		color: var(--gray-400);
		font-weight: 500;
		margin-top: 1px;
	}

	/* ── TABLE ── */
	.table-wrap {
		overflow-x: auto;
		padding: 0 4px 4px;
	}

	table.rtable {
		width: 100%;
		border-collapse: collapse;
		font-size: 12.5px;
	}

	table.rtable thead tr th {
		background: var(--gray-800);
		color: var(--white);
		font-weight: 700;
		font-size: 11px;
		text-transform: uppercase;
		letter-spacing: .05em;
		padding: 10px 12px;
		text-align: center;
		white-space: nowrap;
		border: 1px solid rgba(255, 255, 255, .08);
	}

	table.rtable thead tr.sub-head th {
		background: var(--pmi-red);
		font-size: 10px;
		padding: 7px 10px;
	}

	table.rtable tbody tr {
		transition: background .12s;
	}

	table.rtable tbody tr:nth-child(even) {
		background: var(--gray-50);
	}

	table.rtable tbody tr:hover {
		background: var(--pmi-red-soft);
	}

	table.rtable tbody td {
		padding: 9px 12px;
		text-align: center;
		color: var(--gray-800);
		border-bottom: 1px solid var(--gray-100);
		border-right: 1px solid var(--gray-100);
		white-space: nowrap;
	}

	table.rtable tbody td:first-child {
		font-weight: 600;
		color: var(--gray-400);
	}

	table.rtable tbody td.td-label {
		text-align: left;
		font-weight: 600;
		color: var(--gray-800);
	}

	table.rtable tbody td.td-num {
		font-weight: 700;
		color: var(--pmi-red-dark);
	}

	table.rtable tbody tr.tr-total td {
		background: linear-gradient(90deg, var(--pmi-red-dark), var(--pmi-red));
		color: var(--white) !important;
		font-weight: 800;
		font-size: 13px;
	}

	table.rtable tbody tr.tr-gol td {
		font-weight: 600;
	}

	/* Gol darah row colors */
	.gol-a td {
		border-left: 3px solid #3B82F6;
	}

	.gol-b td {
		border-left: 3px solid #F59E0B;
	}

	.gol-o td {
		border-left: 3px solid #10B981;
	}

	.gol-ab td {
		border-left: 3px solid #8B5CF6;
	}

	/* ── PRINT BTN ── */
	.btn-xls {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 10px 20px;
		background: #166534;
		color: var(--white);
		border: none;
		border-radius: var(--radius-sm);
		font-size: 13px;
		font-weight: 700;
		font-family: inherit;
		cursor: pointer;
		transition: background .15s;
		margin: 8px 20px 20px;
	}

	.btn-xls:hover {
		background: #14532d;
	}

	.btn-xls svg {
		width: 16px;
		height: 16px;
		fill: var(--white);
	}

	/* ── EMPTY STATE ── */
	.empty-state {
		padding: 32px;
		text-align: center;
		color: var(--gray-400);
		font-size: 13px;
		font-weight: 500;
	}

	/* ================== PERBAIKAN UKURAN TANGGAL ================== */

	/* Input tanggal lebih besar tapi bersih */
	.filter-group input[type="text"][id^="datepicker"] {
		height: 50px !important;
		font-size: 15px !important;
		padding: 0 14px !important;
		font-weight: 500;
		border-radius: 8px !important;
	}

	/* Kalender popup (jQuery UI) - dibuat lebih besar & bersih */
	.ui-datepicker {
		font-size: 14.5px !important;
		width: 280px !important;
	}

	.ui-datepicker td {
		padding: 10px 5px !important;
		font-size: 13.5px !important;
	}

	.ui-datepicker .ui-state-highlight {
		background: #ffeb3b !important;
	}

	/* Tombol Tampilkan ikut lebih besar supaya serasi */
	.btn-submit {
		height: 50px !important;
		font-size: 15px !important;
		padding: 0 24px !important;
	}
</style>

<div class="rekap-wrap">

	<!-- ── HERO HEADER ── -->
	<div class="rekap-hero">
		<div class="rekap-hero-inner">
			<h1>Rekap Pembuatan Komponen Darah</h1>
			<div class="rekap-hero-sub">
				<span class="rekap-badge">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
						<rect x="3" y="4" width="18" height="18" rx="2" />
						<path d="M16 2v4M8 2v4M3 10h18" />
					</svg>
					<?php echo $pertgl ?>-<?php echo $perbln ?>-<?php echo $perthn ?> s/d <?php echo $pertgl1 ?>-<?php echo $perbln1 ?>-<?php echo $perthn1 ?>
				</span>
				<span class="rekap-badge">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
						<circle cx="12" cy="12" r="10" />
						<path d="M12 6v6l4 2" />
					</svg>
					<?php echo $shiftLabel ?>
				</span>
				<span class="rekap-badge">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
						<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2" />
					</svg>
					<?php echo $transLabel ?>
				</span>
			</div>
		</div>
	</div>

	<div class="rekap-body">

		<!-- ── FILTER CARD ── -->
		<div class="filter-card">
			<div class="filter-card-title">Filter Data</div>
			<form name="mintadarah1" method="post">
				<div class="filter-row">
					<div class="filter-group">
						<label>Tanggal Mulai</label>
						<input type="text" name="minta1" id="datepicker" placeholder="yyyy-mm-dd" value="<?php echo $today ?>">
					</div>
					<div class="filter-group">
						<label>Tanggal Sampai</label>
						<input type="text" name="minta2" id="datepicker1" placeholder="yyyy-mm-dd" value="<?php echo $today1 ?>">
					</div>
					<div class="filter-group">
						<label>No. Transaksi</label>
						<select name="transaksi">
							<option value="">SEMUA</option>
							<?php for ($i = 1; $i <= 25; $i++) echo "<option value='$i'" . ($trs == $i ? ' selected' : '') . ">$i</option>"; ?>
						</select>
					</div>
					<div class="filter-group">
						<label>Shift</label>
						<select name="gol_shift">
							<option value="">- SEMUA -</option>
							<option value="1" <?php echo $shift == '1' ? 'selected' : '' ?>>Shift I</option>
							<option value="2" <?php echo $shift == '2' ? 'selected' : '' ?>>Shift II</option>
							<option value="3" <?php echo $shift == '3' ? 'selected' : '' ?>>Shift III</option>
							<option value="4" <?php echo $shift == '4' ? 'selected' : '' ?>>Shift IV</option>
						</select>
					</div>
					<button type="submit" name="submit" class="btn-submit">
						<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<circle cx="11" cy="11" r="8" />
							<path d="m21 21-4.35-4.35" />
						</svg>
						Tampilkan
					</button>
				</div>
			</form>
		</div>

		<!-- ═══════════════════════════════════════════════════ -->
		<!-- TABEL 1: PER TANGGAL PEMBUATAN                      -->
		<!-- ═══════════════════════════════════════════════════ -->
		<div class="section-card">
			<div class="section-header">
				<div class="section-icon">
					<svg viewBox="0 0 24 24">
						<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
					</svg>
				</div>
				<div>
					<div class="section-title">Rekap Per Tanggal Pembuatan</div>
					<div class="section-subtitle">Jumlah komponen berdasarkan tanggal produksi</div>
				</div>
			</div>
			<div class="table-wrap">
				<table class="rtable">
					<thead>
						<tr>
							<th rowspan="2">No</th>
							<th rowspan="2">Tanggal</th>
							<th colspan="17">Jenis Komponen</th>
							<th rowspan="2">Total</th>
							<th colspan="4">Gol. Darah</th>
							<th colspan="2">Rhesus</th>
							<th rowspan="2">Total</th>
						</tr>
						<tr class="sub-head">
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
							<th>PRC Leuco</th>
							<th>TC Pool</th>
							<th>A</th>
							<th>B</th>
							<th>O</th>
							<th>AB</th>
							<th>Pos</th>
							<th>Neg</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$komponen0 = mysql_query("
    SELECT DATE(tgl) as tgl FROM dpengolahan
    WHERE (Produk IS NOT NULL AND Produk != '')
      AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
      $filterShift $filterTrans
    GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC");

						$no = 1;
						$hasRow = false;
						while ($komponen = mysql_fetch_assoc($komponen0)) {
							$hasRow = true;
							$d = $komponen['tgl'];
							$base = "FROM dpengolahan WHERE DATE(tgl)='$d' AND CAST(tgl as date)>='$today' AND CAST(tgl as date)<='$today1' $filterShift $filterTrans";
							$prc    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='PRC'"), 0);
							$tc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='TC'"), 0);
							$lp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='LP'"), 0);
							$ffp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='FFP'"), 0);
							$fp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='FP'"), 0);
							$fp24   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='FP 24'"), 0);
							$fp24x  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='FP 24 450'"), 0);
							$fp72   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='FP 72'"), 0);
							$prc450 = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='PRC 450'"), 0);
							$prp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='PRP'"), 0);
							$wb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='WB'"), 0);
							$wb450  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='WB 450'"), 0);
							$ahf    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='AHF'"), 0);
							$we     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='WE'"), 0);
							$bc     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='BC'"), 0);
							$leu    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='PRC Leucodepleted'"), 0);
							$tcp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND Produk='TC Pooled'"), 0);
							$ga     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND goldarah='A'"), 0);
							$gb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND goldarah='B'"), 0);
							$go     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND goldarah='O'"), 0);
							$gab    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND goldarah='AB'"), 0);
							$rp     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND rhesus='+'"), 0);
							$rn     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base AND rhesus='-'"), 0);
							$tot    = $prc + $tc + $lp + $ffp + $fp + $fp24 + $fp24x + $fp72 + $prc450 + $prp + $wb + $wb450 + $ahf + $we + $bc + $leu + $tcp;
						?>
							<tr>
								<td><?php echo $no++ ?></td>
								<td class="td-label"><?php echo $d ?></td>
								<td class="td-num"><?php echo $prc ?></td>
								<td class="td-num"><?php echo $tc ?></td>
								<td class="td-num"><?php echo $lp ?></td>
								<td class="td-num"><?php echo $ffp ?></td>
								<td class="td-num"><?php echo $fp ?></td>
								<td class="td-num"><?php echo $fp24 ?></td>
								<td class="td-num"><?php echo $fp24x ?></td>
								<td class="td-num"><?php echo $fp72 ?></td>
								<td class="td-num"><?php echo $prc450 ?></td>
								<td class="td-num"><?php echo $prp ?></td>
								<td class="td-num"><?php echo $wb ?></td>
								<td class="td-num"><?php echo $wb450 ?></td>
								<td class="td-num"><?php echo $ahf ?></td>
								<td class="td-num"><?php echo $we ?></td>
								<td class="td-num"><?php echo $bc ?></td>
								<td class="td-num"><?php echo $leu ?></td>
								<td class="td-num"><?php echo $tcp ?></td>
								<td class="td-num"><?php echo $tot ?></td>
								<td><?php echo $ga ?></td>
								<td><?php echo $gb ?></td>
								<td><?php echo $go ?></td>
								<td><?php echo $gab ?></td>
								<td><?php echo $rp ?></td>
								<td><?php echo $rn ?></td>
								<td><?php echo $rp + $rn ?></td>
							</tr>
						<?php }
						if (!$hasRow) echo '<tr><td colspan="27" class="empty-state">Tidak ada data pada periode ini</td></tr>'; ?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- ═══════════════════════════════════════════════════ -->
		<!-- TABEL 2: REKAP PER ALAT (Pemutar + Pemisah + Pembeku) -->
		<!-- ═══════════════════════════════════════════════════ -->
		<div class="section-card">
			<div class="section-header">
				<div class="section-icon">
					<svg viewBox="0 0 24 24">
						<path d="M12 2a10 10 0 100 20A10 10 0 0012 2zm0 0v10m0 0l-4-4m4 4l4-4" />
					</svg>
				</div>
				<div>
					<div class="section-title">Rekap Per Alat</div>
					<div class="section-subtitle">Jumlah komponen berdasarkan Alat Pemutar, Pemisah, dan Pembeku</div>
				</div>
			</div>
			<div class="table-wrap">
				<table class="rtable">
					<thead>
						<tr>
							<th rowspan="2">No</th>
							<th rowspan="2">Kategori Alat</th>
							<th rowspan="2">Nama Alat</th>
							<th colspan="17">Jenis Komponen</th>
							<th rowspan="2">Total</th>
						</tr>
						<tr class="sub-head">
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
							<th>PRC Leuco</th>
							<th>TC Pool</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// Gabungkan semua alat
						$alatQuery = mysql_query("
                    SELECT 'Pemutar' as kategori, aPutar as nama_alat 
                    FROM dpengolahan 
                    WHERE aPutar IS NOT NULL AND aPutar != '' 
                      AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
                      $filterShift $filterTrans
                    GROUP BY aPutar
                    
                    UNION
                    
                    SELECT 'Pemisah' as kategori, aPisah as nama_alat 
                    FROM dpengolahan 
                    WHERE aPisah IS NOT NULL AND aPisah != '' 
                      AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
                      $filterShift $filterTrans
                    GROUP BY aPisah
                    
                    UNION
                    
                    SELECT 'Pembeku' as kategori, aBeku as nama_alat 
                    FROM dpengolahan 
                    WHERE aBeku IS NOT NULL AND aBeku != '' 
                      AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
                      $filterShift $filterTrans
                    GROUP BY aBeku
                    ORDER BY kategori, nama_alat
                ");

						$no = 1;
						$hasRow = false;

						while ($row = mysql_fetch_assoc($alatQuery)) {
							$hasRow = true;
							$kategori = $row['kategori'];
							$namaAlat = $row['nama_alat'];

							$baseAlat = "
                        FROM dpengolahan 
                        WHERE CAST(tgl as date) >= '$today' 
                          AND CAST(tgl as date) <= '$today1'
                          $filterShift $filterTrans
                    ";

							if ($kategori == 'Pemutar') {
								$baseAlat .= " AND aPutar = '$namaAlat'";
							} elseif ($kategori == 'Pemisah') {
								$baseAlat .= " AND aPisah = '$namaAlat'";
							} elseif ($kategori == 'Pembeku') {
								$baseAlat .= " AND aBeku = '$namaAlat'";
							}

							$prc  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='PRC'"), 0);
							$tc   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='TC'"), 0);
							$lp   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='LP'"), 0);
							$ffp  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='FFP'"), 0);
							$fp   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='FP'"), 0);
							$fp24   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='FP 24'"), 0);
							$fp24x  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='FP 24 450'"), 0);
							$fp72   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='FP 72'"), 0);
							$prc450 = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='PRC 450'"), 0);
							$prp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='PRP'"), 0);
							$wb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='WB'"), 0);
							$wb450  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='WB 450'"), 0);
							$ahf  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='AHF'"), 0);
							$we   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='WE'"), 0);
							$bc   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='BC'"), 0);
							$leu  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='PRC Leucodepleted'"), 0);
							$tcp  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $baseAlat AND Produk='TC Pooled'"), 0);

							$total = $prc + $tc + $lp + $ffp + $fp + $fp24 + $fp24x + $fp72 + $prc450 + $prp + $wb + $wb450 + $ahf + $we + $bc + $leu + $tcp;
						?>
							<tr>
								<td><?php echo $no++ ?></td>
								<td class="td-label"><strong><?php echo $kategori ?></strong></td>
								<td class="td-label"><?php echo $namaAlat ?></td>
								<td class="td-num"><?php echo $prc ?></td>
								<td class="td-num"><?php echo $tc ?></td>
								<td class="td-num"><?php echo $lp ?></td>
								<td class="td-num"><?php echo $ffp ?></td>
								<td class="td-num"><?php echo $fp ?></td>
								<td class="td-num"><?php echo $fp24 ?></td>
								<td class="td-num"><?php echo $fp24x ?></td>
								<td class="td-num"><?php echo $fp72 ?></td>
								<td class="td-num"><?php echo $prc450 ?></td>
								<td class="td-num"><?php echo $prp ?></td>
								<td class="td-num"><?php echo $wb ?></td>
								<td class="td-num"><?php echo $wb450 ?></td>
								<td class="td-num"><?php echo $ahf ?></td>
								<td class="td-num"><?php echo $we ?></td>
								<td class="td-num"><?php echo $bc ?></td>
								<td class="td-num"><?php echo $leu ?></td>
								<td class="td-num"><?php echo $tcp ?></td>
								<td class="td-num"><strong><?php echo $total ?></strong></td>
							</tr>
						<?php }

						if (!$hasRow) {
							echo '<tr><td colspan="21" class="empty-state">Tidak ada data pada periode ini</td></tr>';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- ═══════════════════════════════════════════════════ -->
		<!-- TABEL 3: PER GOL. DARAH                             -->
		<!-- ═══════════════════════════════════════════════════ -->
		<div class="section-card">
			<div class="section-header">
				<div class="section-icon">
					<svg viewBox="0 0 24 24">
						<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z" />
					</svg>
				</div>
				<div>
					<div class="section-title">Rekap Per Golongan Darah</div>
					<div class="section-subtitle">Distribusi komponen berdasarkan golongan A / B / O / AB</div>
				</div>
			</div>
			<div class="table-wrap">
				<table class="rtable">
					<thead>
						<tr>
							<th>Gol. Darah</th>
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
							<th>PRC Leuco</th>
							<th>TC Pool</th>
							<th>Rhesus +</th>
							<th>Rhesus -</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$golList = array('A', 'B', 'O', 'AB');
						$golClass = array('A' => 'gol-a', 'B' => 'gol-b', 'O' => 'gol-o', 'AB' => 'gol-ab');
						$totals = array('prc' => 0, 'tc' => 0, 'lp' => 0, 'ffp' => 0, 'fp' => 0, 'fp24' => 0, 'fp24x' => 0, 'fp72' => 0, 'prc450' => 0, 'prp' => 0, 'wb' => 0, 'wb450' => 0, 'ahf' => 0, 'we' => 0, 'leu' => 0, 'tcp' => 0, 'rp' => 0, 'rn' => 0);

						foreach ($golList as $gol) {
							$base3 = "FROM dpengolahan WHERE CAST(tgl as date)>='$today' AND CAST(tgl as date)<='$today1' $filterShift $filterTrans AND goldarah='$gol'";
							$prc  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='PRC'"), 0);
							$tc   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='TC'"), 0);
							$lp   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='LP'"), 0);
							$ffp  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='FFP'"), 0);
							$fp   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='FP'"), 0);
							$fp24   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='FP 24'"), 0);
							$fp24x  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='FP 24 450'"), 0);
							$fp72   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='FP 72'"), 0);
							$prc450 = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='PRC 450'"), 0);
							$prp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='PRP'"), 0);
							$wb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='WB'"), 0);
							$wb450  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='WB 450'"), 0);
							$ahf  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='AHF'"), 0);
							$we   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='WE'"), 0);
							$leu  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='PRC Leucodepleted'"), 0);
							$tcp  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND Produk='TC Pooled'"), 0);
							$rp   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND rhesus='+'"), 0);
							$rn   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base3 AND rhesus='-'"), 0);
							$totals['prc'] += $prc;
							$totals['tc'] += $tc;
							$totals['lp'] += $lp;
							$totals['ffp'] += $ffp;
							$totals['fp'] += $fp;
							$totals['fp24'] += $fp24;
							$totals['fp24x'] += $fp24x;
							$totals['fp72'] += $fp72;
							$totals['prc450'] += $prc450;
							$totals['prp'] += $prp;
							$totals['wb'] += $wb;
							$totals['wb450'] += $wb450;
							$totals['ahf'] += $ahf;
							$totals['we'] += $we;
							$totals['leu'] += $leu;
							$totals['tcp'] += $tcp;
							$totals['rp'] += $rp;
							$totals['rn'] += $rn;
							echo "<tr class='tr-gol {$golClass[$gol]}'>
        <td class='td-label'>Gol. $gol</td>
        <td class='td-num'>$prc</td><td class='td-num'>$tc</td><td class='td-num'>$lp</td>
        <td class='td-num'>$ffp</td><td class='td-num'>$fp</td><td class='td-num'>$fp24</td>
        <td class='td-num'>$fp24x</td><td class='td-num'>$fp72</td><td class='td-num'>$prc450</td>
        <td class='td-num'>$prp</td><td class='td-num'>$wb</td><td class='td-num'>$wb450</td>
        <td class='td-num'>$ahf</td><td class='td-num'>$we</td>
        <td class='td-num'>$leu</td><td class='td-num'>$tcp</td>
        <td>$rp</td><td>$rn</td>
    </tr>";
						}
						?>
						<tr class="tr-total">
							<td>JUMLAH</td>
							<td><?php echo $totals['prc'] ?></td>
							<td><?php echo $totals['tc'] ?></td>
							<td><?php echo $totals['lp'] ?></td>
							<td><?php echo $totals['ffp'] ?></td>
							<td><?php echo $totals['fp'] ?></td>
							<td><?php echo $totals['fp24'] ?></td>
							<td><?php echo $totals['fp24x'] ?></td>
							<td><?php echo $totals['fp72'] ?></td>
							<td><?php echo $totals['prc450'] ?></td>
							<td><?php echo $totals['prp'] ?></td>
							<td><?php echo $totals['wb'] ?></td>
							<td><?php echo $totals['wb450'] ?></td>
							<td><?php echo $totals['ahf'] ?></td>
							<td><?php echo $totals['we'] ?></td>
							<td><?php echo $totals['leu'] ?></td>
							<td><?php echo $totals['tcp'] ?></td>
							<td><?php echo $totals['rp'] ?></td>
							<td><?php echo $totals['rn'] ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<!-- ═══════════════════════════════════════════════════ -->
		<!-- TABEL 4: PER JENIS KANTONG                          -->
		<!-- ═══════════════════════════════════════════════════ -->
		<div class="section-card">
			<div class="section-header">
				<div class="section-icon">
					<svg viewBox="0 0 24 24">
						<path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zm-9 8H7v-2h4v2zm6-4H7v-2h10v2z" />
					</svg>
				</div>
				<div>
					<div class="section-title">Rekap Per Jenis Kantong</div>
					<div class="section-subtitle">Single / Double / Triple / Quadruple / Pediatrik</div>
				</div>
			</div>
			<div class="table-wrap">
				<table class="rtable">
					<thead>
						<tr>
							<th rowspan="2">No</th>
							<th rowspan="2">Jenis Kantong</th>
							<th colspan="16">Jenis Komponen</th>
							<th rowspan="2">Total</th>
						</tr>
						<tr class="sub-head">
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
							<th>PRC Leuco</th>
							<th>TC Pool</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$komponen6 = mysql_query("
    SELECT jenis FROM dpengolahan
    WHERE (Produk IS NOT NULL AND Produk != '')
      AND (jenis IS NOT NULL AND jenis != '')
      AND CAST(tgl as date) >= '$today' AND CAST(tgl as date) <= '$today1'
      $filterShift $filterTrans
    GROUP BY jenis ORDER BY jenis ASC");

						$no = 1;
						$hasRow = false;
						$jenisMap = array('1' => 'Single', '2' => 'Double', '3' => 'Triple', '4' => 'Quadruple', '6' => 'Pediatrik');
						while ($k7 = mysql_fetch_assoc($komponen6)) {
							$hasRow = true;
							$j = $k7['jenis'];
							$label = isset($jenisMap[$j]) ? $jenisMap[$j] : "Jenis $j";
							$base4 = "FROM dpengolahan WHERE CAST(tgl as date)>='$today' AND CAST(tgl as date)<='$today1' AND jenis='$j' $filterShift $filterTrans";
							$prc  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='PRC'"), 0);
							$tc   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='TC'"), 0);
							$lp   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='LP'"), 0);
							$ffp  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='FFP'"), 0);
							$fp   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='FP'"), 0);
							$fp24   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='FP 24'"), 0);
							$fp24x  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='FP 24 450'"), 0);
							$fp72   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='FP 72'"), 0);
							$prc450 = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='PRC 450'"), 0);
							$prp    = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='PRP'"), 0);
							$wb     = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='WB'"), 0);
							$wb450  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='WB 450'"), 0);
							$ahf  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='AHF'"), 0);
							$we   = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='WE'"), 0);
							$leu  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='PRC Leucodepleted'"), 0);
							$tcp  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4 AND Produk='TC Pooled'"), 0);
							$tot  = mysql_result(mysql_query("SELECT COUNT(DISTINCT noKantong) $base4"), 0);
						?>
							<tr>
								<td><?php echo $no++ ?></td>
								<td class="td-label"><?php echo $label ?></td>
								<td class="td-num"><?php echo $prc ?></td>
								<td class="td-num"><?php echo $tc ?></td>
								<td class="td-num"><?php echo $lp ?></td>
								<td class="td-num"><?php echo $ffp ?></td>
								<td class="td-num"><?php echo $fp ?></td>
								<td class="td-num"><?php echo $fp24 ?></td>
								<td class="td-num"><?php echo $fp24x ?></td>
								<td class="td-num"><?php echo $fp72 ?></td>
								<td class="td-num"><?php echo $prc450 ?></td>
								<td class="td-num"><?php echo $prp ?></td>
								<td class="td-num"><?php echo $wb ?></td>
								<td class="td-num"><?php echo $wb450 ?></td>
								<td class="td-num"><?php echo $ahf ?></td>
								<td class="td-num"><?php echo $we ?></td>
								<td class="td-num"><?php echo $leu ?></td>
								<td class="td-num"><?php echo $tcp ?></td>
								<td class="td-num"><?php echo $tot ?></td>
							</tr>
						<?php }
						if (!$hasRow) echo '<tr><td colspan="19" class="empty-state">Tidak ada data pada periode ini</td></tr>'; ?>
					</tbody>
				</table>
			</div>

			<!-- Tombol Print XLS -->
			<form name="xls" method="post" action="modul/rekap_pembuatan_komponen_xls.php">
				<input type="hidden" name="pertgl" value="<?php echo $pertgl ?>">
				<input type="hidden" name="perbln" value="<?php echo $perbln ?>">
				<input type="hidden" name="perthn" value="<?php echo $perthn ?>">
				<input type="hidden" name="pertgl1" value="<?php echo $pertgl1 ?>">
				<input type="hidden" name="perbln1" value="<?php echo $perbln1 ?>">
				<input type="hidden" name="trs1" value="<?php echo $trs ?>">
				<input type="hidden" name="shift" value="<?php echo $shift ?>">
				<input type="hidden" name="perthn1" value="<?php echo $perthn1 ?>">
				<input type="hidden" name="today1" value="<?php echo $today1 ?>">
				<button type="submit" name="submit2" class="btn-xls">
					<svg viewBox="0 0 24 24">
						<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8 13h8v1.5H8V13zm0 3h8v1.5H8V16zm0-6h3v1.5H8V10z" />
					</svg>
					Print Rekap Komponen (.XLS)
				</button>
			</form>
		</div>

	</div><!-- /rekap-body -->
</div><!-- /rekap-wrap -->

<?php mysql_close(); ?>