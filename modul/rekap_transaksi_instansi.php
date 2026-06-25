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

    /* -- PAGE HEADER -- */
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

    /* -- FILTER CARD -- */
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

    /* -- SECTION CARD -- */
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

    /* -- TABLE -- */
    .table-wrap {
        overflow-x: auto;
        padding: 0 4px 4px;
    }

    table.rtable {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        table-layout: fixed;
    }

    table.rtable-ppdds col.col-no {
        width: 56px;
    }

    table.rtable-ppdds col.col-tgl {
        width: 130px;
    }

    table.rtable-ppdds col.col-instansi {
        width: auto;
    }

    table.rtable-ppdds col.col-jml {
        width: 150px;
    }

    table.rtable-ppdds col.col-stat {
        width: 100px;
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
        padding: 12px 14px;
        text-align: center;
        color: var(--gray-800);
        border-bottom: 1px solid var(--gray-200);
        border-right: 1px solid var(--gray-200);
        white-space: nowrap;
    }

    table.rtable tbody tr td:last-child {
        border-right: none;
    }

    table.rtable tfoot tr td {
        padding: 12px 14px;
        text-align: center;
        border-right: 1px solid rgba(255, 255, 255, .2);
    }

    table.rtable tfoot tr td:last-child {
        border-right: none;
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

    table.rtable tbody td.td-dg {
        text-align: left;
        font-weight: 700;
        color: var(--pmi-red);
    }

    table.rtable tbody tr.tr-total td,
    table.rtable tfoot tr.tr-total td {
        background: linear-gradient(90deg, var(--pmi-red-dark), var(--pmi-red));
        color: var(--white) !important;
        font-weight: 800;
        font-size: 13px;
    }

    /* -- PRINT BTN -- */
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

    /* -- EMPTY STATE -- */
    .empty-state {
        padding: 32px;
        text-align: center;
        color: var(--gray-400);
        font-size: 13px;
        font-weight: 500;
    }

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

    /* Badge kecil status berhasil/gagal/batal */
    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 99px;
    }

    .stat-berhasil {
        background: #DCFCE7;
        color: #166534;
    }

    .stat-gagal {
        background: #FEF3C7;
        color: #92400E;
    }

    .stat-batal {
        background: #FEE2E2;
        color: #991B1B;
    }
</style>

<div class="rekap-wrap">

    <!-- -- HERO HEADER -- -->
    <div class="rekap-hero">
        <div class="rekap-hero-inner">
            <h1>Rekap Kegiatan PPDDS (Mobile &amp; Dalam Gedung)</h1>
            <div class="rekap-hero-sub">
                <span class="rekap-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    <?php echo $pertgl ?>-<?php echo $perbln ?>-<?php echo $perthn ?> s/d <?php echo $pertgl1 ?>-<?php echo $perbln1 ?>-<?php echo $perthn1 ?>
                </span>
            </div>
        </div>
    </div>

    <div class="rekap-body">

        <!-- -- FILTER CARD -- -->
        <div class="filter-card">
            <div class="filter-card-title">Filter Data</div>
            <form name="mintaPpdds" method="post">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Tanggal Mulai</label>
                        <input type="text" name="minta1" id="datepicker" placeholder="yyyy-mm-dd" value="<?php echo $today ?>">
                    </div>
                    <div class="filter-group">
                        <label>Tanggal Sampai</label>
                        <input type="text" name="minta2" id="datepicker1" placeholder="yyyy-mm-dd" value="<?php echo $today1 ?>">
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

        <!-- =================================================== -->
        <!-- TABEL: REKAP KEGIATAN PPDDS PER TANGGAL & INSTANSI  -->
        <!-- =================================================== -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="section-title">Rekap Kegiatan PPDDS</div>
                    <div class="section-subtitle">Dalam Gedung dan Mobile/Instansi - per tanggal pelaksanaan</div>
                </div>
            </div>
            <div class="table-wrap">
                <table class="rtable rtable-ppdds">
                    <colgroup>
                        <col class="col-no">
                        <col class="col-tgl">
                        <col class="col-instansi">
                        <col class="col-jml">
                        <col class="col-stat">
                        <col class="col-stat">
                        <col class="col-stat">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Instansi</th>
                            <th>Jumlah Pendonor</th>
                            <th>Berhasil</th>
                            <th>Gagal</th>
                            <th>Batal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil rekap kegiatan: gabungan Dalam Gedung (NoTrans awal 'DG') dan Instansi (lainnya)
                        // dikelompokkan berdasarkan tanggal (DATE(Tgl)) + nama instansi/"Dalam Gedung"
                        $queryPpdds = mysql_query("
							SELECT
								DATE(Tgl) AS tgl_kegiatan,
								CASE WHEN NoTrans LIKE 'DG%' THEN 'Dalam Gedung' ELSE Instansi END AS nama_instansi,
								COUNT(*) AS jml_pendonor,
								SUM(CASE WHEN Pengambilan = 0 THEN 1 ELSE 0 END) AS jml_berhasil,
								SUM(CASE WHEN Pengambilan = 2 THEN 1 ELSE 0 END) AS jml_gagal,
								SUM(CASE WHEN Pengambilan = 1 THEN 1 ELSE 0 END) AS jml_batal
							FROM htransaksi
							WHERE CAST(Tgl AS date) >= '$today' AND CAST(Tgl AS date) <= '$today1'
							GROUP BY DATE(Tgl), nama_instansi
							ORDER BY DATE(Tgl) ASC, nama_instansi ASC
						");

                        $no = 1;
                        $hasRow = false;
                        $totalPendonor = 0;
                        $totalBerhasil = 0;
                        $totalGagal = 0;
                        $totalBatal = 0;

                        while ($row = mysql_fetch_assoc($queryPpdds)) {
                            $hasRow = true;
                            $tglKeg   = $row['tgl_kegiatan'];
                            $instansi = $row['nama_instansi'] != '' ? $row['nama_instansi'] : '(Tanpa Nama Instansi)';
                            $jml      = $row['jml_pendonor'];
                            $berhasil = $row['jml_berhasil'];
                            $gagal    = $row['jml_gagal'];
                            $batal    = $row['jml_batal'];

                            $totalPendonor += $jml;
                            $totalBerhasil += $berhasil;
                            $totalGagal    += $gagal;
                            $totalBatal    += $batal;

                            $isDG = ($instansi == 'Dalam Gedung');
                            $labelClass = $isDG ? 'td-dg' : 'td-label';
                        ?>
                            <tr>
                                <td><?php echo $no++ ?></td>
                                <td><?php echo $tglKeg ?></td>
                                <td class="<?php echo $labelClass ?>"><?php echo htmlspecialchars($instansi) ?></td>
                                <td class="td-num"><?php echo $jml ?></td>
                                <td><span class="stat-pill stat-berhasil"><?php echo $berhasil ?></span></td>
                                <td><span class="stat-pill stat-gagal"><?php echo $gagal ?></span></td>
                                <td><span class="stat-pill stat-batal"><?php echo $batal ?></span></td>
                            </tr>
                        <?php }

                        if (!$hasRow) {
                            echo '<tr><td colspan="7" class="empty-state">Tidak ada data pada periode ini</td></tr>';
                        }
                        ?>
                    </tbody>
                    <?php if ($hasRow) { ?>
                        <tfoot>
                            <tr class="tr-total">
                                <td colspan="3" style="text-align:left; padding-left:18px;">JUMLAH</td>
                                <td><?php echo $totalPendonor ?></td>
                                <td><?php echo $totalBerhasil ?></td>
                                <td><?php echo $totalGagal ?></td>
                                <td><?php echo $totalBatal ?></td>
                            </tr>
                        </tfoot>
                    <?php } ?>
                </table>
            </div>

            <!-- Tombol Print XLS -->
            <form name="xls" method="post" action="modul/rekap_ppdds_xls.php">
                <input type="hidden" name="pertgl" value="<?php echo $pertgl ?>">
                <input type="hidden" name="perbln" value="<?php echo $perbln ?>">
                <input type="hidden" name="perthn" value="<?php echo $perthn ?>">
                <input type="hidden" name="pertgl1" value="<?php echo $pertgl1 ?>">
                <input type="hidden" name="perbln1" value="<?php echo $perbln1 ?>">
                <input type="hidden" name="perthn1" value="<?php echo $perthn1 ?>">
                <input type="hidden" name="today1" value="<?php echo $today1 ?>">
                <button type="submit" name="submit2" class="btn-xls">
                    <svg viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8 13h8v1.5H8V13zm0 3h8v1.5H8V16zm0-6h3v1.5H8V10z" />
                    </svg>
                    Print Rekap PPDDS (.XLS)
                </button>
            </form>
        </div>

    </div><!-- /rekap-body -->
</div><!-- /rekap-wrap -->

<?php mysql_close(); ?>