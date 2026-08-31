<?php
include "koneksi.php";
//include "index.php";

if (isset($_GET['export_excel'])) {
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="dokumen_pendukung.xls"');

    function export_excel_escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    function export_excel_date($date)
    {
        if (empty($date) || $date == '0000-00-00') return '-';
        return date('d-m-Y', strtotime($date));
    }

    $exportQuery = mysql_query("select * from pendukung where aktif='0' order by kontrol");
    $today = date('Y-m-d');

    echo '<meta charset="UTF-8">';
    echo '<style>body{font-family:Calibri,Arial,sans-serif;color:#1f2937}h2{color:#1f4e78;font-size:18pt}table{border-collapse:collapse;width:100%;font-size:11pt}th{background-color:#1f4e78;color:#ffffff;font-weight:bold;text-align:center;vertical-align:middle;padding:9px;border:1px solid #9eafbf}td{padding:7px;border:1px solid #b8c4ce;vertical-align:middle}tr:nth-child(even) td{background-color:#f3f6f8}.center{text-align:center}.status-valid{background-color:#3e8e41;color:#ffffff;font-weight:bold;text-align:center}.status-expired{background-color:#b22222;color:#ffffff;font-weight:bold;text-align:center}.status-review{background-color:#ffff00;font-weight:bold;text-align:center}</style>';
    echo '<h2>Dokumen Pendukung (L3)</h2>';
    echo '<table>';
    echo '<tr>'
        . '<th>No</th><th>Bidang</th><th>Judul Dokumen</th>'
        . '<th>Identitas Dokumen Sebelumnya</th><th>Tingkat Dokumen</th>'
        . '<th>No. Kontrol Dokumen</th><th>Periode Kaji Ulang (bln)</th>'
        . '<th>No. Versi</th><th>Tanggal Disahkan</th><th>Tanggal Berlaku</th>'
        . '<th>Tanggal Kaji Ulang</th><th>File</th><th>Masih Berlaku</th>'
        . '<th>Habis Masa Kadaluwarsa</th><th>Peninjauan Kembali</th></tr>';

    $exportNo = 0;
    while ($exportData = mysql_fetch_array($exportQuery)) {
        $exportNo++;
        $masihBerlaku = $exportData['tgl_peninjauan'] > $today ? 'Masih Berlaku' : '-';
        $habisMasaBerlaku = $exportData['tgl_peninjauan'] < $today ? 'Habis Masa Berlaku' : '-';
        $peninjauanKembali = $exportData['tgl_notif'] <= $today
            ? 'Waktunya Peninjauan Kembali'
            : '-';
        $validClass = $masihBerlaku !== '-' ? 'status-valid' : 'center';
        $expiredClass = $habisMasaBerlaku !== '-' ? 'status-expired' : 'center';
        $reviewClass = $peninjauanKembali !== '-' ? 'status-review' : 'center';

        echo '<tr>'
            . '<td class="center">' . $exportNo . '</td>'
            . '<td>' . export_excel_escape($exportData['bidang']) . '</td>'
            . '<td>' . export_excel_escape($exportData['nama1']) . '</td>'
            . '<td>' . export_excel_escape($exportData['nama2']) . '</td>'
            . '<td class="center">' . export_excel_escape($exportData['tingkat']) . '</td>'
            . '<td class="center">' . export_excel_escape($exportData['kontrol2']) . '</td>'
            . '<td class="center">' . export_excel_escape($exportData['periode']) . '</td>'
            . '<td class="center">' . export_excel_escape($exportData['no_versi']) . '</td>'
            . '<td class="center">' . export_excel_date($exportData['tgl_setuju']) . '</td>'
            . '<td class="center">' . export_excel_date($exportData['tgl_pelaksanaan']) . '</td>'
            . '<td class="center">' . export_excel_date($exportData['tgl_peninjauan']) . '</td>'
            . '<td>' . export_excel_escape($exportData['fileku']) . '</td>'
            . '<td class="' . $validClass . '">' . $masihBerlaku . '</td>'
            . '<td class="' . $expiredClass . '">' . $habisMasaBerlaku . '</td>'
            . '<td class="' . $reviewClass . '">' . $peninjauanKembali . '</td>'
            . '</tr>';
    }
    echo '</table>';
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
    </script>
    <style>
    .button2 {
        padding: 10px 20px;
        font-size: 12px;
        text-align: center;
        cursor: pointer;
        outline: none;
        color: #fff;
        background-color: #ff5a0b;
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px #999;
    }

    .button1 {
        padding: 10px 20px;
        font-size: 12px;
        text-align: center;
        cursor: pointer;
        outline: none;
        color: #fff;
        background-color: #ff0000;
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px #999;
    }

    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        font-size: 12px;
        margin: auto;
        width: 100%;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: center;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
    </style>
    <style>
    input[type=text] {
        width: 130px;
        box-sizing: border-box;
        border: 2px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        background-color: white;
        background-image: url('searchicon.png');
        background-position: 10px 10px;
        background-repeat: no-repeat;
        padding: 12px 20px 12px 40px;
        -webkit-transition: width 0.4s ease-in-out;
        transition: width 0.4s ease-in-out;
    }

    input[type=text]:focus {
        width: 100%;
    }
    </style>
    <style>
    .button {
        padding: 10px 20px;
        font-size: 12px;
        text-align: center;
        cursor: pointer;
        outline: none;
        color: #fff;
        background-color: #4CAF50;
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px #999;
    }

    .button:hover {
        background-color: #3e8e41
    }

    .button:active {
        background-color: #3e8e41;
        box-shadow: 0 3px #666;
        transform: translateY(3px);
    }
    </style>
</head>

<body>
    <br />
    <input id="myInput" type="text" placeholder="Search..">
    <br><br>
    <a href="input_pendukung.php"><button class="button2">Tambah Dokumen</button></a>
    <a href="rekap_musnah_pendukung.php"><button class="button1">Daftar Penarikan Dokumen</button></a>
    <a href="?export_excel=1">
        <button class="button1" style="background-color: #3e8e41;">
            Export Excel
        </button>
    </a>
    <br><br>
    <p align="center"><b> Dokumen Pendukung (L3)</b></p>
    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Bidang</th>
                <th rowspan="2">Judul Dokumen</th>
                <th rowspan="2" style="width:10px">Identitas Dokumen Sebelumnya</th>
                <th rowspan="2" style="width:10px">Tingkat Dokumen</th>
                <th rowspan="2">No. Kontrol Dokumen</th>
                <th rowspan="2" style="width:10px">Periode Kaji Ulang (bln)</th>
                <th rowspan="2" style="width:10px">No. Versi</th>
                <th rowspan="2">Tanggal Disahkan</th>
                <th rowspan="2">Tanggal Berlaku</th>
                <th rowspan="2">Tanggal Kaji Ulang</th>
                <th rowspan="2">File</th>
                <th colspan="3">Keterangan Masa Aktif Dokumen</th>
                <th rowspan="2">Pengeluaran Dokumen</th>
                <th rowspan="2">Peninjauan Kembali Dokumen</th>
                <th rowspan="2">Penarikan Dokumen</th>
            </tr>

            <tr>
                <th>Masih Berlaku</th>
                <th>Habis Masa Kadaluwarsa</th>
                <th>Peninjauan Kembali</th>
            </tr>
        </thead>
        <tbody id="myTable">
            <?php
            $donor = "select * from pendukung where aktif='0' order by kontrol";
            $proses = mysql_query($donor);

            // awal Konversi tanggal ke bahasa indonesia
            function format_indo($date)
            {
                $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

                $tahun = substr($date, 0, 4);
                $bulan = substr($date, 5, 2);
                $tgl   = substr($date, 8, 2);
                $result = $tgl . " " . $BulanIndo[(int)$bulan - 1] . " " . $tahun;
                return ($result);
            }
            // akhir Konversi tanggal ke bahasa indonesia


            $nourut = 0;
            while ($data = mysql_fetch_array($proses)) {
                $nourut++;

                //awal penanda dokumen
                $today = date('Y-m-d');
                if ($data['tgl_peninjauan'] >= "$today") $pengerjaan1 = '<span style="background-color:#DEB887">Masih Berlaku</span>';
                if ($data['tgl_peninjauan'] <= "$today") $pengerjaan1 = '<span>-</span>';
                if ($data['tgl_peninjauan'] == "$today") $pengerjaan1 = '<span>-</span>';

                if ($data['tgl_peninjauan'] <= "$today") $pengerjaan2 = '<span style="background-color:#B22222"><font style="color:white">Habis Masa Berlaku</font></span>';
                if ($data['tgl_peninjauan'] >= "$today") $pengerjaan2 = '<span>-</span>';
                if ($data['tgl_peninjauan'] == "$today") $pengerjaan2 = '<span>-</span>';


                if ($data['tgl_notif'] >= "$today") $pengerjaan3 = '<span">-</span>';
                if ($data['tgl_notif'] <= "$today") $pengerjaan3 = '<span style="background-color:yellow">Waktunya Peninjauan Kembali</span>';
                if ($data['tgl_notif'] == "$today") $pengerjaan3 = '<span style="background-color:yellow">Waktunya Peninjauan Kembali</span>';
                //akhir penanda dokumen

            ?>
            <tr>
                <td>
                    <div align="center"><?php echo $nourut; ?></div>
                </td>
                <td>
                    <div align="left"><?php echo $data['bidang']; ?></div>
                </td>
                <td>
                    <div align="left"><?php echo $data['nama1']; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo $data['nama2']; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo $data['tingkat']; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo $data['kontrol2']; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo $data['periode']; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo $data['no_versi']; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo format_indo($data['tgl_setuju']); ?></div>
                </td>
                <td>
                    <div align="center"><?php echo format_indo($data['tgl_pelaksanaan']); ?></div>
                </td>
                <td>
                    <div align="center"><?php echo format_indo($data['tgl_peninjauan']); ?></div>
                </td>
                <td><a href="download.php?filename=<?= $data['fileku'] ?>"><?php echo $data['fileku']; ?></a></td>
                <td>
                    <div align="center"><?php echo $pengerjaan1; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo $pengerjaan2; ?></div>
                </td>
                <td>
                    <div align="center"><?php echo $pengerjaan3; ?></div>
                </td>
                <td><a href="keluar_pendukung.php?detail=<?php echo $data['kontrol2']; ?>"><img src="images/clip.png"
                            width="30" height="30"></img>
                    </a></td>
                <td><a href="detail_pendukung.php?detail=<?php echo $data['kontrol2']; ?>"><img src="images/revisi.png"
                            width="30" height="30"></img></a></td>
                <td><a href="musnah_pendukung.php?detail=<?php echo $data['nomor']; ?>"><img src="images/musnah.png"
                            width="30" height="30"></img></a></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</body>

</html>