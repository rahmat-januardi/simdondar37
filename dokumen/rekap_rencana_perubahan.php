<?php
include "koneksi.php";
// include "index.php"; // aktifkan jika butuh session/auth

// =============================================
// FUNGSI BANTU
// =============================================
function format_indo($date)
{
    if (empty($date) || $date == '0000-00-00') return '-';
    $BulanIndo = array(
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"
    );
    $tahun = substr($date, 0, 4);
    $bulan = substr($date, 5, 2);
    $tgl   = substr($date, 8, 2);
    return $tgl . " " . $BulanIndo[(int)$bulan - 1] . " " . $tahun;
}

// =============================================
// AMBIL PARAMETER FILTER DARI GET
// =============================================
$mode_filter = isset($_GET['mode_filter']) ? $_GET['mode_filter'] : 'tahun';
$filter_tahun = isset($_GET['filter_tahun']) ? intval($_GET['filter_tahun']) : date('Y');
$filter_bulan = isset($_GET['filter_bulan']) ? intval($_GET['filter_bulan']) : 0;
$filter_dari  = isset($_GET['filter_dari'])  ? $_GET['filter_dari']  : '';
$filter_sampai = isset($_GET['filter_sampai']) ? $_GET['filter_sampai'] : '';
$filter_bidang = isset($_GET['filter_bidang']) ? $_GET['filter_bidang'] : '';
$filter_tingkat = isset($_GET['filter_tingkat']) ? $_GET['filter_tingkat'] : '';

// =============================================
// BANGUN KONDISI WHERE BERDASARKAN MODE FILTER
// =============================================
$where_kondisi = array();

if ($mode_filter == 'tahun') {
    // Filter hanya berdasarkan tahun (dari kolom tgl_setuju)
    $where_kondisi[] = "YEAR(tgl_setuju) = " . intval($filter_tahun);
} elseif ($mode_filter == 'bulan') {
    // Filter berdasarkan bulan + tahun tertentu
    $where_kondisi[] = "YEAR(tgl_setuju)  = " . intval($filter_tahun);
    if ($filter_bulan > 0) {
        $where_kondisi[] = "MONTH(tgl_setuju) = " . intval($filter_bulan);
    }
} elseif ($mode_filter == 'rentang') {
    // Filter berdasarkan rentang tanggal bebas
    if (!empty($filter_dari)) {
        $where_kondisi[] = "tgl_setuju >= '" . mysql_real_escape_string($filter_dari) . "'";
    }
    if (!empty($filter_sampai)) {
        $where_kondisi[] = "tgl_setuju <= '" . mysql_real_escape_string($filter_sampai) . "'";
    }
}
// else mode 'semua' => tidak ada filter tanggal

// Filter tambahan: bidang
if (!empty($filter_bidang)) {
    $where_kondisi[] = "bidang = '" . mysql_real_escape_string($filter_bidang) . "'";
}

// Filter tambahan: tingkat dokumen
if (!empty($filter_tingkat)) {
    $where_kondisi[] = "tingkat = '" . mysql_real_escape_string($filter_tingkat) . "'";
}

$sql_where = count($where_kondisi) > 0 ? "WHERE " . implode(" AND ", $where_kondisi) : "";

// =============================================
// EXPORT HANDLER
// =============================================
$export = isset($_GET['export']) ? $_GET['export'] : '';

if ($export == 'excel') {
    $sql_rekap = "SELECT * FROM riwayat $sql_where ORDER BY id_rp ASC";
    $hasil_rekap = mysql_query($sql_rekap);

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Rekap_Rencana_Perubahan_" . date('Ymd_His') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<html><head><meta charset='utf-8'></head><body>";

    // Judul Besar
    echo "<h3 style='font-family:Arial; text-align:center; font-size:18px; margin-bottom:20px;'>REKAP DAFTAR RENCANA PERUBAHAN DOKUMEN</h3>";

    // Tabel Data - FONT DIBESARKAN
    echo "<table border='1' style='border-collapse:collapse; font-family:Arial; font-size:13px; width:100%;'>";
    echo "<tr style='background:#556B2F; color:white; font-size:14px; font-weight:bold;'>
            <th>No</th>
            <th>No Referensi</th>
            <th>Bidang</th>
            <th>Judul Dokumen</th>
            <th>Tingkat</th>
            <th>No. Kontrol</th>
            <th>No. Versi</th>
            <th>Tgl Disetujui</th>
            <th>Tgl Berlaku</th>
            <th>Tgl Kaji Ulang</th>
            <th>Disusun Oleh</th>
            <th>Diperiksa Oleh</th>
            <th>Disetujui Oleh</th>
            <th>Disahkan Oleh</th>
          </tr>";

    $no = 1;
    while ($row = mysql_fetch_array($hasil_rekap)) {
        echo "<tr>";
        echo "<td style='text-align:center;'>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['id_rp']) . "</td>";
        echo "<td>" . htmlspecialchars($row['bidang']) . "</td>";
        echo "<td style='text-align:left;'>" . htmlspecialchars($row['nama1']) . "</td>";
        echo "<td style='text-align:center;'>" . htmlspecialchars($row['tingkat']) . "</td>";
        echo "<td>" . htmlspecialchars($row['kontrol2']) . "</td>";
        echo "<td style='text-align:center;'>" . htmlspecialchars($row['no_versi']) . "</td>";
        echo "<td>" . format_indo($row['tgl_setuju']) . "</td>";
        echo "<td>" . format_indo($row['tgl_pelaksanaan']) . "</td>";
        echo "<td>" . format_indo($row['tgl_peninjauan']) . "</td>";
        echo "<td>" . htmlspecialchars($row['pembuat']) . "</td>";
        echo "<td>" . htmlspecialchars($row['pemeriksa']) . "</td>";
        echo "<td>" . htmlspecialchars($row['pengesah']) . "</td>";
        echo "<td>" . htmlspecialchars($row['pengesah2']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // ==================== BAGIAN TANDA TANGAN ====================
    echo "<br><br><br>";
    echo "<table border='0' style='font-family:Arial; font-size:14px; width:100%; margin-top:50px;' cellpadding='10'>";
    echo "<tr>";
    echo "<td colspan='1'></td>";

    // Disusun Oleh
    echo "<td colspan='4' style='text-align:center; vertical-align:top;'>";
    echo "Disusun Oleh<br><br><br><br>";
    echo "<b>(.....................................................)</b><br>";
    echo "Tanggal: ................................";
    echo "</td>";

    echo "<td colspan='6'></td>";

    // Mengetahui
    echo "<td colspan='4' style='text-align:center; vertical-align:top;'>";
    echo "Mengetahui<br><br><br><br>";
    echo "<b>(.....................................................)</b><br>";
    echo "Tanggal: ................................";
    echo "</td>";

    echo "<td colspan='1'></td>";
    echo "</tr>";
    echo "</table>";

    echo "</body></html>";
    exit;
}

// =============================================
// EKSEKUSI QUERY UTAMA
// =============================================
$sql_rekap = "SELECT * FROM riwayat $sql_where ORDER BY id_rp ASC";
$hasil_rekap = mysql_query($sql_rekap);
$total_rekap = mysql_num_rows($hasil_rekap);

// Ambil daftar tahun yang tersedia (untuk dropdown)
$sql_tahun = "SELECT DISTINCT YEAR(tgl_setuju) as tahun FROM riwayat WHERE tgl_setuju IS NOT NULL AND tgl_setuju != '0000-00-00' ORDER BY tahun DESC";
$hasil_tahun = mysql_query($sql_tahun);

// Ambil daftar bidang yang tersedia (untuk dropdown)
$sql_bidang = "SELECT DISTINCT bidang FROM riwayat WHERE bidang IS NOT NULL AND bidang != '' ORDER BY bidang";
$hasil_bidang_list = mysql_query($sql_bidang);

// Ambil daftar tingkat dokumen
$sql_tingkat = "SELECT DISTINCT tingkat FROM riwayat WHERE tingkat IS NOT NULL AND tingkat != '' ORDER BY tingkat";
$hasil_tingkat_list = mysql_query($sql_tingkat);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekap Riwayat Perubahan Dokumen</title>

    <link rel="stylesheet" href="jquery-ui-1.10.3/themes/base/jquery.ui.all.css">
    <script src="jquery-ui-1.10.3/jquery-1.9.1.js"></script>
    <script src="jquery-ui-1.10.3/ui/jquery.ui.core.js"></script>
    <script src="jquery-ui-1.10.3/ui/jquery.ui.widget.js"></script>
    <script src="jquery-ui-1.10.3/ui/jquery.ui.datepicker.js"></script>
    <link rel="stylesheet" href="jquery-ui-1.10.3/demos.css">

    <script>
        $(function() {
            // Datepicker untuk filter rentang tanggal
            $("#dp_dari").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true
            });
            $("#dp_sampai").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true
            });

            // Tampilkan/sembunyikan panel filter sesuai mode yang dipilih
            function toggleFilterPanel() {
                var mode = $("input[name='mode_filter']:checked").val();
                $(".panel-filter").hide();
                if (mode == 'tahun') {
                    $("#panel-tahun").show();
                }
                if (mode == 'bulan') {
                    $("#panel-bulan").show();
                }
                if (mode == 'rentang') {
                    $("#panel-rentang").show();
                }
                // mode 'semua' tidak perlu panel tambahan
            }

            $("input[name='mode_filter']").on("change", toggleFilterPanel);
            toggleFilterPanel(); // jalankan saat halaman pertama load
        });

        // Cetak halaman (tanpa panel filter)
        function cetakHalaman() {
            window.print();
        }
    </script>

    <style type="text/css">
        /* =============================================
   UMUM
============================================= */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .COOPER {
            font-family: Cooper Black;
        }

        /* =============================================
   CONTAINER UTAMA
============================================= */
        .wrapper {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }

        /* =============================================
   PANEL FILTER
============================================= */
        .box-filter {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .box-filter h3 {
            margin: 0 0 12px 0;
            font-size: 13px;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .filter-row {
            display: inline-block;
            margin-right: 12px;
            margin-bottom: 8px;
            vertical-align: middle;
        }

        .filter-row label {
            font-weight: bold;
            margin-right: 5px;
        }

        .filter-row select,
        .filter-row input[type="text"] {
            padding: 4px 7px;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: 12px;
        }

        .panel-filter {
            display: none;
        }

        .mode-radio {
            margin-right: 14px;
            cursor: pointer;
        }

        /* =============================================
   TOMBOL
============================================= */
        .btn-filter {
            padding: 7px 18px;
            font-size: 12px;
            cursor: pointer;
            color: #fff;
            background-color: #556B2F;
            border: none;
            border-radius: 6px;
            box-shadow: 0 3px #999;
            margin-right: 6px;
        }

        .btn-filter:hover {
            background-color: #3d5022;
        }

        .btn-filter:active {
            box-shadow: 0 1px #666;
            transform: translateY(2px);
        }

        .btn-cetak {
            padding: 7px 18px;
            font-size: 12px;
            cursor: pointer;
            color: #fff;
            background-color: #1565C0;
            border: none;
            border-radius: 6px;
            box-shadow: 0 3px #999;
        }

        .btn-cetak:hover {
            background-color: #0d47a1;
        }

        /* =============================================
   INFO HASIL
============================================= */
        .info-hasil {
            background: #E8F5E9;
            border-left: 4px solid #556B2F;
            padding: 8px 14px;
            margin-bottom: 14px;
            border-radius: 0 4px 4px 0;
            font-size: 12px;
        }

        .info-hasil span {
            font-weight: bold;
            color: #556B2F;
        }

        /* =============================================
   TABEL
============================================= */
        .tabel-rekap {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
            border-radius: 6px;
            overflow: hidden;
        }

        .tabel-rekap th {
            background: #556B2F;
            color: #fff;
            padding: 9px 10px;
            text-align: center;
            font-size: 12px;
            border: 1px solid #4a5e28;
            white-space: nowrap;
        }

        .tabel-rekap td {
            border: 1px solid #ddd;
            padding: 7px 10px;
            text-align: center;
            font-size: 11px;
            vertical-align: middle;
        }

        .tabel-rekap tr:nth-child(even) td {
            background: #f9f9f9;
        }

        .tabel-rekap tr:hover td {
            background: #FFF9C4;
        }

        .tabel-rekap td.nama-dokumen {
            text-align: left;
        }

        .tabel-kosong td {
            text-align: center;
            color: #999;
            padding: 24px;
            font-style: italic;
        }

        /* =============================================
   JUDUL HALAMAN
============================================= */
        .judul-halaman {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-halaman h2 {
            font-family: Cooper Black;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .judul-halaman p {
            color: #777;
            font-size: 12px;
            margin: 0;
        }

        /* =============================================
   CETAK (Print CSS)
============================================= */
        @media print {

            .box-filter,
            .btn-cetak,
            .btn-filter {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .wrapper {
                max-width: 100%;
                margin: 0;
            }

            .tabel-rekap th {
                background: #ccc !important;
                color: #000 !important;
            }
        }

        /* =============================================
   DATEPICKER OVERRIDE
============================================= */
        .ui-datepicker {
            font-family: Arial;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <!-- ======= JUDUL ======= -->
        <div class="judul-halaman">
            <h2>REKAP DAFTAR RENCANA PERUBAHAN DOKUMEN</h2>
        </div>

        <!-- ======= PANEL FILTER ======= -->
        <div class="box-filter">
            <h3>&#128269; Filter Periode</h3>
            <form method="get" action="rekap_rencana_perubahan.php">

                <!-- Pilihan mode filter -->
                <div style="margin-bottom:12px;">
                    <label><input type="radio" name="mode_filter" value="tahun" class="mode-radio"
                            <?php echo ($mode_filter == 'tahun'  ? 'checked' : ''); ?> /> Per Tahun</label>
                    <label><input type="radio" name="mode_filter" value="bulan" class="mode-radio"
                            <?php echo ($mode_filter == 'bulan'  ? 'checked' : ''); ?> /> Per Bulan</label>
                    <label><input type="radio" name="mode_filter" value="rentang" class="mode-radio"
                            <?php echo ($mode_filter == 'rentang' ? 'checked' : ''); ?> /> Rentang Tanggal</label>
                    <label><input type="radio" name="mode_filter" value="semua" class="mode-radio"
                            <?php echo ($mode_filter == 'semua'  ? 'checked' : ''); ?> /> Semua Data</label>
                </div>

                <!-- Panel: Per Tahun -->
                <div id="panel-tahun" class="panel-filter">
                    <div class="filter-row">
                        <label>Tahun:</label>
                        <select name="filter_tahun">
                            <?php
                            // Tampilkan tahun dari database
                            $tahun_list = array();
                            while ($row_tahun = mysql_fetch_array($hasil_tahun)) {
                                $tahun_list[] = (int)$row_tahun['tahun'];
                            }
                            // Pastikan tahun sekarang selalu ada di daftar
                            $tahun_sekarang = (int)date('Y');
                            if (!in_array($tahun_sekarang, $tahun_list)) {
                                array_unshift($tahun_list, $tahun_sekarang);
                            }
                            // Jika masih kosong (database benar-benar kosong), isi default
                            if (empty($tahun_list)) {
                                $tahun_list = array($tahun_sekarang, $tahun_sekarang - 1, $tahun_sekarang - 2);
                            }
                            // Urutkan descending agar tahun terbaru di atas
                            rsort($tahun_list);
                            foreach ($tahun_list as $th) {
                                $sel = ($th == $filter_tahun) ? 'selected' : '';
                                echo "<option value='$th' $sel>$th</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Panel: Per Bulan -->
                <div id="panel-bulan" class="panel-filter">
                    <div class="filter-row">
                        <label>Bulan:</label>
                        <select name="filter_bulan">
                            <option value="0" <?php echo ($filter_bulan == 0 ? 'selected' : ''); ?>>-- Semua Bulan --</option>
                            <?php
                            $nama_bulan = array(
                                1 => "Januari",
                                2 => "Februari",
                                3 => "Maret",
                                4 => "April",
                                5 => "Mei",
                                6 => "Juni",
                                7 => "Juli",
                                8 => "Agustus",
                                9 => "September",
                                10 => "Oktober",
                                11 => "November",
                                12 => "Desember"
                            );
                            foreach ($nama_bulan as $nb => $nm) {
                                $sel = ($filter_bulan == $nb) ? 'selected' : '';
                                echo "<option value='$nb' $sel>$nm</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-row">
                        <label>Tahun:</label>
                        <select name="filter_tahun_bulan">
                            <?php
                            // Reset pointer hasil_tahun (query ulang karena sudah di-iterate)
                            $hasil_tahun2 = mysql_query($sql_tahun);
                            $tahun_list2  = array();
                            while ($row_t = mysql_fetch_array($hasil_tahun2)) {
                                $tahun_list2[] = (int)$row_t['tahun'];
                            }
                            // Pastikan tahun sekarang selalu ada
                            if (!in_array($tahun_sekarang, $tahun_list2)) {
                                array_unshift($tahun_list2, $tahun_sekarang);
                            }
                            if (empty($tahun_list2)) {
                                $tahun_list2 = array($tahun_sekarang, $tahun_sekarang - 1, $tahun_sekarang - 2);
                            }
                            rsort($tahun_list2);
                            foreach ($tahun_list2 as $th2) {
                                $sel = ($th2 == $filter_tahun) ? 'selected' : '';
                                echo "<option value='$th2' $sel>$th2</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Panel: Rentang Tanggal -->
                <div id="panel-rentang" class="panel-filter">
                    <div class="filter-row">
                        <label>Dari Tanggal:</label>
                        <input type="text" name="filter_dari" id="dp_dari"
                            value="<?php echo htmlspecialchars($filter_dari); ?>"
                            placeholder="yyyy-mm-dd" size="12" readonly />
                    </div>
                    <div class="filter-row">
                        <label>Sampai Tanggal:</label>
                        <input type="text" name="filter_sampai" id="dp_sampai"
                            value="<?php echo htmlspecialchars($filter_sampai); ?>"
                            placeholder="yyyy-mm-dd" size="12" readonly />
                    </div>
                </div>

                <!-- Filter Bidang & Tingkat -->
                <div style="margin-top:10px; border-top:1px solid #eee; padding-top:10px;">
                    <div class="filter-row">
                        <label>Filter Bidang:</label>
                        <select name="filter_bidang">
                            <option value="">-- Semua Bidang --</option>
                            <?php
                            while ($row_b = mysql_fetch_array($hasil_bidang_list)) {
                                $bd  = $row_b['bidang'];
                                $sel = ($bd == $filter_bidang) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($bd) . "' $sel>" . htmlspecialchars($bd) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="filter-row">
                        <label> Tingkat Dokumen:</label>
                        <select name="filter_tingkat">
                            <option value="">-- Semua Tingkat Dokumen --</option>
                            <?php
                            while ($row_t = mysql_fetch_array($hasil_tingkat_list)) {
                                $tingkat = $row_t['tingkat'];
                                $sel = ($tingkat == $filter_tingkat) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($tingkat) . "' $sel>" . htmlspecialchars($tingkat) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    &nbsp;
                    <button type="submit" class="btn-filter">&#128269; Tampilkan</button>
                    <a href="rekap_rencana_perubahan.php"><button type="button" class="btn-filter" style="background:#888;">&#8635; Reset</button></a>
                    <button type="button" class="btn-cetak" onclick="cetakHalaman()">🖨 Cetak</button>
                    <button type="button" class="btn-cetak" onclick="exportExcel()" style="background:#217346;">📊 Export Excel</button>
                    <!-- <button type="button" class="btn-cetak" onclick="exportPDF()" style="background:#d32f2f;">📕 Export PDF</button> -->
                </div>

            </form>
        </div><!-- end box-filter -->

        <!-- ======= INFO HASIL ======= -->
        <div class="info-hasil">
            Ditemukan <span><?php echo $total_rekap; ?></span> perubahan dokumen
            <?php
            if ($mode_filter == 'tahun') {
                echo "pada tahun <span>$filter_tahun</span>";
            } elseif ($mode_filter == 'bulan') {
                $nm_bln = $filter_bulan > 0 ? $nama_bulan[$filter_bulan] : 'semua bulan';
                echo "pada <span>$nm_bln $filter_tahun</span>";
            } elseif ($mode_filter == 'rentang') {
                $dari_txt   = !empty($filter_dari)   ? format_indo($filter_dari)   : '...';
                $sampai_txt = !empty($filter_sampai) ? format_indo($filter_sampai) : '...';
                echo "antara <span>$dari_txt</span> s/d <span>$sampai_txt</span>";
            } else {
                echo "(seluruh periode)";
            }
            if (!empty($filter_bidang)) {
                echo ", bidang <span>" . htmlspecialchars($filter_bidang) . "</span>";
            }
            ?>
        </div>

        <!-- ======= TABEL REKAP ======= -->
        <table class="tabel-rekap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Referensi</th>
                    <th>Bidang</th>
                    <th>Judul Dokumen</th>
                    <th>Tingkat</th>
                    <th>No. Kontrol</th>
                    <th>No. Versi</th>
                    <th>Tgl Disetujui</th>
                    <th>Tgl Berlaku</th>
                    <th>Tgl Kaji Ulang</th>
                    <th>Disusun Oleh</th>
                    <th>Diperiksa Oleh</th>
                    <th>Disetujui Oleh</th>
                    <th>Disahkan Oleh</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($total_rekap == 0) {
                    echo "<tr class='tabel-kosong'><td colspan='13'>Tidak ada data riwayat perubahan untuk filter yang dipilih.</td></tr>";
                } else {
                    $nourut = 0;
                    while ($row = mysql_fetch_array($hasil_rekap)) {
                        $nourut++;
                        // Tentukan warna baris berdasarkan bidang (opsional, memudahkan baca)
                ?>
                        <tr>
                            <td><?php echo $nourut; ?></td>
                            <td><?php echo htmlspecialchars($row['id_rp']); ?></td>
                            <td><?php echo htmlspecialchars($row['bidang']); ?></td>
                            <td class="nama-dokumen"><?php echo htmlspecialchars($row['nama1']); ?></td>
                            <td><?php echo htmlspecialchars($row['tingkat']); ?></td>
                            <td><?php echo htmlspecialchars($row['kontrol2']); ?></td>
                            <td><?php echo htmlspecialchars($row['no_versi']); ?></td>
                            <td><?php echo format_indo($row['tgl_setuju']); ?></td>
                            <td><?php echo format_indo($row['tgl_pelaksanaan']); ?></td>
                            <td><?php echo format_indo($row['tgl_peninjauan']); ?></td>
                            <td><?php echo htmlspecialchars($row['pembuat']); ?></td>
                            <td><?php echo htmlspecialchars($row['pemeriksa']); ?></td>
                            <td><?php echo htmlspecialchars($row['pengesah']); ?></td>
                            <td><?php echo htmlspecialchars($row['pengesah2']); ?></td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <br /><br />
        <!-- ======= BAGIAN TANDA TANGAN (untuk cetak) ======= -->
        <br><br><br><br>
        <table border="0" style="font-family:Arial; font-size:13px; width:100%; margin-top:40px;" cellpadding="8" class="ttd-cetak">
            <tr>
                <td width="10%"></td>

                <!-- Disusun Oleh -->
                <td width="40%" style="text-align:center; vertical-align:top;">
                    Disusun Oleh<br><br><br><br><br>
                    <b>(...........................................................)</b><br>
                    <div style="text-align:center; margin-top:10px;">
                        Tanggal: _______________________
                    </div>
                </td>

                <td width="10%"></td>

                <!-- Mengetahui -->
                <td width="40%" style="text-align:center; vertical-align:top;">
                    Mengetahui<br><br><br><br><br>
                    <b>(...........................................................)</b><br>
                    <div style="text-align:center; margin-top:10px;">
                        Tanggal: _______________________
                    </div>
                </td>

                <td width="10%"></td>
            </tr>
        </table>

    </div><!-- end wrapper -->
</body>

<script>
    // Export Excel
    function exportExcel() {
        // Ambil form asli
        var originalForm = document.querySelector('form');

        // Buat form tiruan (clone)
        var temporaryForm = originalForm.cloneNode(true);

        // Buat input hidden untuk export
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'export';
        input.value = 'excel';

        // Masukkan input ke dalam form tiruan
        temporaryForm.appendChild(input);

        // Sembunyikan form tiruan dan masukkan ke body agar bisa di-submit
        temporaryForm.style.display = 'none';
        document.body.appendChild(temporaryForm);

        // Kirim form tiruan
        temporaryForm.submit();

        // Hapus form tiruan dari dokumen setelah dikirim
        document.body.removeChild(temporaryForm);
    }

    // Export PDF (sementara pakai print dulu, bisa di-upgrade nanti)
    function exportPDF() {
        alert('Fitur Export PDF akan ditambahkan menggunakan mPDF/TCPDF.\n\nSementara ini gunakan tombol Cetak lalu simpan sebagai PDF.');
        // Bisa diarahkan ke halaman print
        window.print();
    }
</script>

</html>