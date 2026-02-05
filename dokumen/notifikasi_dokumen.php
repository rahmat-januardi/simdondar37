<?php
session_start();
include "koneksi.php";
require_once('clogin.php');

// Panggil Query Peninjauan
$query_peninjauan = "SELECT *
FROM (
    SELECT 
        nomor, bidang, nama1, kontrol AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L1 - Kebijakan' AS jenis_dokumen
    FROM kebijakan

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L2 - SPO' AS jenis_dokumen
    FROM pks

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L3 - IK' AS jenis_dokumen
    FROM ik

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L3 - IKA' AS jenis_dokumen
    FROM ika

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L3 - Pendukung' AS jenis_dokumen
    FROM pendukung

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L4 - Formulir' AS jenis_dokumen
    FROM formulir
) AS semua_dokumen
WHERE semua_dokumen.aktif = '0'
  AND semua_dokumen.tgl_notif <= CURDATE() AND semua_dokumen.tgl_peninjauan > CURDATE()
ORDER BY semua_dokumen.jenis_dokumen, semua_dokumen.nomor;
";
$peninjauan_result = mysql_query($query_peninjauan);
if (!$peninjauan_result) {
    die('Query Peninjauan Error: ' . mysql_error());
}

// Panggil Query Expired
$query_expired = "SELECT *
FROM (
    SELECT 
        nomor, bidang, nama1, kontrol AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L1 - Kebijakan' AS jenis_dokumen
    FROM kebijakan

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L2 - SPO' AS jenis_dokumen
    FROM pks

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L3 - IK' AS jenis_dokumen
    FROM ik

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L3 - IKA' AS jenis_dokumen
    FROM ika

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L3 - Pendukung' AS jenis_dokumen
    FROM pendukung

    UNION ALL

    SELECT 
        nomor, bidang, nama1, kontrol2 AS kontrol_value,
        tgl_peninjauan, tgl_notif, aktif,
        'L4 - Formulir' AS jenis_dokumen
    FROM formulir

) AS semua_dokumen
WHERE semua_dokumen.aktif = '0'
  AND semua_dokumen.tgl_peninjauan <= CURDATE()
ORDER BY semua_dokumen.jenis_dokumen, semua_dokumen.nomor;
";
$expired_result = mysql_query($query_expired);
if (!$expired_result) {
    die('Query Expired Error: ' . mysql_error());
}

// Hitung jumlah data dari kedua query
$jumlah_peninjauan = mysql_num_rows($peninjauan_result);
$jumlah_expired = mysql_num_rows($expired_result);

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

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Dokumen</title>

    <link rel="stylesheet" href="bootsrap337/w3.css">
    <link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
    body {
        background-color: #f8f9fa;
        padding: 20px;
    }

    h1 {
        margin-bottom: 20px;
    }

    .table-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    th {
        text-align: center;
    }

    .swal2-checkbox {
        margin-right: 8px !important;
    }

    .dataTables_filter label {
        float: right;
    }
    </style>
</head>

<body>

    <body style="background-color:#f8f9fa;">

        <div class="container-fluid mt-4">
            <h1 class="text-center mb-2">
                <i class="glyphicon glyphicon-file"></i> Notifikasi Dokumen
            </h1>
            <p class="text-center text-muted">Halaman ini menampilkan dokumen yang perlu diperhatikan.</p>
            <hr>

            <div class="row mt-4">
                <!-- Peninjauan -->
                <div class="col-md-6">
                    <div class="table-container" style="border-top:5px solid #f4c542;">
                        <h4 class="mb-3 text-center" style="color:#b8860b;">?? Dokumen Perlu Peninjauan Kembali</h4>
                        <div class="table-responsive">
                            <table id="tabel-peninjauan" class="table table-striped table-bordered">
                                <thead class="thead-light" style="background-color:#fff7e6;">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dokumen</th>
                                        <th>Kode</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Bidang</th>
                                        <th>Tanggal Peninjauan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    mysql_data_seek($peninjauan_result, 0); // reset pointer
                                    while ($row = mysql_fetch_assoc($peninjauan_result)) {
                                        $edit_page = '';
                                        switch ($row['jenis_dokumen']) {
                                            case 'L1 - Kebijakan':
                                                $edit_page = 'detail_kebijakan.php';
                                                break;
                                            case 'L2 - SPO':
                                                $edit_page = 'detail_pks.php';
                                                break;
                                            case 'L3 - IK':
                                                $edit_page = 'detail_ik.php';
                                                break;
                                            case 'L3 - IKA':
                                                $edit_page = 'detail_ika.php';
                                                break;
                                            case 'L3 - Pendukung':
                                                $edit_page = 'detail_pendukung.php';
                                                break;
                                            case 'L4 - Formulir':
                                                $edit_page = 'detail_formulir.php';
                                                break;
                                        }

                                        $tglPeninjauan = format_indo($row['tgl_peninjauan']);
                                        echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['nama1']}</td>
                                        <td>{$row['kontrol_value']}</td>
                                        <td>{$row['jenis_dokumen']}</td>
                                        <td>{$row['bidang']}</td>
                                        <td>{$tglPeninjauan}</td>
                                        <td><a href='dokumen/{$edit_page}?detail={$row['kontrol_value']}&no={$row['nomor']}' class='btn btn-sm btn-warning'>Tinjau</a></td>
                                    </tr>";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Expired -->
                <div class="col-md-6">
                    <div class="table-container" style="border-top:5px solid #dc3545;">
                        <h4 class="mb-3 text-center text-danger">? Dokumen Habis Masa Berlaku</h4>
                        <div class="table-responsive">
                            <table id="tabel-expired" class="table table-striped table-bordered">
                                <thead class="thead-light" style="background-color:#ffe6e6;">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dokumen</th>
                                        <th>Kode</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Bidang</th>
                                        <th>Tanggal Habis Berlaku</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    mysql_data_seek($expired_result, 0);
                                    while ($row = mysql_fetch_assoc($expired_result)) {
                                        $edit_page = '';
                                        switch ($row['jenis_dokumen']) {
                                            case 'L1 - Kebijakan':
                                                $edit_page = 'detail_kebijakan.php';
                                                break;
                                            case 'L2 - SPO':
                                                $edit_page = 'detail_pks.php';
                                                break;
                                            case 'L3 - IK':
                                                $edit_page = 'detail_ik.php';
                                                break;
                                            case 'L3 - IKA':
                                                $edit_page = 'detail_ika.php';
                                                break;
                                            case 'L3 - Pendukung':
                                                $edit_page = 'detail_pendukung.php';
                                                break;
                                            case 'L4 - Formulir':
                                                $edit_page = 'detail_formulir.php';
                                                break;
                                        }

                                        $tglPeninjauan = format_indo($row['tgl_peninjauan']);
                                        echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['nama1']}</td>
                                        <td>{$row['kontrol_value']}</td>
                                        <td>{$row['jenis_dokumen']}</td>
                                        <td>{$row['bidang']}</td>
                                        <td>{$tglPeninjauan}</td>
                                        <td><a href='dokumen/{$edit_page}?detail={$row['kontrol_value']}&no={$row['nomor']}' class='btn btn-sm btn-danger'>Perbarui</a></td>
                                    </tr>";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="bootsrap337/js/jquery.min.js"></script>
        <script src="bootsrap337/js/bootstrap.min.js"></script>
        <script src="bootsrap337/sweetalert2/sweetalert2@11"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

        <script>
        $(document).ready(function() {
            // DataTables setup
            $('#tabel-peninjauan, #tabel-expired').DataTable({
                "pageLength": 10,
                "lengthChange": false,
                "autoWidth": false,
                "language": {
                    "search": "Cari:",
                    "paginate": {
                        "previous": "Sebelumnya",
                        "next": "Berikutnya"
                    },
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                }
            });

            var jumlahPeninjauan = <?php echo $jumlah_peninjauan; ?>;
            var jumlahExpired = <?php echo $jumlah_expired; ?>;

            if ((jumlahPeninjauan > 0 || jumlahExpired > 0) && !sessionStorage.getItem(
                    'hideNotificationAlert')) {
                let pesan =
                    `<p>Terdapat dokumen yang memerlukan perhatian:</p><ul style="text-align:left;display:inline-block">`;
                if (jumlahPeninjauan > 0) pesan +=
                    `<li><b>${jumlahPeninjauan}</b> dokumen perlu peninjauan kembali.</li>`;
                if (jumlahExpired > 0) pesan +=
                    `<li><b>${jumlahExpired}</b> dokumen telah habis masa berlakunya.</li>`;
                pesan += `</ul>
                <div style="display:flex;align-items:center;justify-content:center;margin-top:10px;">
                    <input type="checkbox" id="dontShowAgain">
                    <label for="dontShowAgain" style="margin-left:5px; margin-top:9px;">Jangan tampilkan lagi</label>
                </div>`;

                Swal.fire({
                    title: 'Peringatan Dokumen ??',
                    html: pesan,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    preConfirm: () => {
                        const dontShow = document.getElementById('dontShowAgain').checked;
                        if (dontShow) sessionStorage.setItem('hideNotificationAlert', true);
                    }
                });
            }
        });
        </script>
    </body>

</html>