<?php
include('config/db_connect.php');
session_start();

$namauser = $_SESSION['namauser'];
$today2 = date('Y-m-d H:i:s');

$notif = "";
$notif_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $nkt = strtoupper(trim($_POST['minta1']));
    $catatan = mysql_real_escape_string($_POST['catatan']);

    $komponen0 = mysql_query("SELECT * FROM stokkantong 
                          WHERE nokantong='$nkt' LIMIT 1");


    $komponen = mysql_fetch_assoc($komponen0);

    if (!$komponen) {
        $notif = "No Kantong tidak ditemukan!";
        $notif_type = "error";
    } else {

        $ubah = '0';

        switch ($komponen['Status']) {
            case 0:
                $ubah = '1';
                $caption = 'Masih kosong dilogistik';
                break;
            case 3:
                $ubah = '3';
                $caption = 'Sudah Keluar';
                break;
            case 5:
                $ubah = '5';
                $caption = 'Sudah Rusak';
                break;
            case 6:
                $ubah = '6';
                $caption = 'Sudah Musnah';
                break;
        }

        if ($ubah === '1' or $ubah === '3' or $ubah === '5' or $ubah === '6') {
            $notif = "Status kantong $nkt $caption!";
            $notif_type = "error";
        } else {
            $upd_ktga = mysql_query("UPDATE stokkantong set hasil_release='0', tgl_release=NULL where NoKantong='$nkt'");

            mysql_query("INSERT INTO release_ulang
             (nokantong, catatan, petugas, created)
             VALUES
             ('$nkt','$catatan','$namauser','$today2')");

            $notif = "No Kantong $nkt berhasil di Release Ulang!";
            $notif_type = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Release Ulang Produk</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <style>
    body {
        background: #f4f6f9;
    }

    .card-header {
        font-weight: bold;
    }

    .table th {
        background: #dc3545;
        color: white;
    }
    </style>
</head>

<body onload="document.mintadarah1.minta1.focus();">

    <div class="container mt-5">

        <h3 class="text-danger mb-4">
            RELEASE ULANG PRODUK KOMPONEN DARAH
        </h3>

        <div class="row">

            <!-- FORM -->
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        Form Release Ulang
                    </div>
                    <div class="card-body">

                        <form name="mintadarah1" method="post" id="formRelease">

                            <div class="mb-3">
                                <label>No Kantong</label>
                                <input type="text" name="minta1" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Catatan</label>
                                <textarea name="catatan" class="form-control" rows="4" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-danger w-100">

                                Proses Release
                            </button>

                        </form>

                    </div>
                </div>
            </div>

            <!-- HISTORI -->
            <div class="col-md-7">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        Histori Release Ulang
                    </div>
                    <div class="card-body table-responsive">

                        <table id="tabelQA" class="table table-bordered table-striped display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Kantong</th>
                                    <th>Catatan</th>
                                    <th>Petugas</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $data = $query = mysql_query("SELECT id, nokantong, catatan, petugas, created  FROM release_ulang  ORDER BY created DESC LIMIT 200");
                                while ($row = mysql_fetch_assoc($data)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= $row['nokantong']; ?></strong></td>
                                    <td><?= $row['catatan']; ?></td>
                                    <td><?= $row['petugas']; ?></td>
                                    <td><?= $row['created']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    document.getElementById("formRelease").onsubmit = function(e) {
        e.preventDefault();

        var frm = this;

        Swal.fire({
            title: 'Yakin ingin Release Ulang?',
            text: "Pastikan No Kantong sudah benar!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                frm.submit(); // sekarang sudah aman
            }
        });

        return false;
    };
    </script>

    <?php if ($notif != "") { ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: '<?= $notif_type ?>',
            title: '<?= ($notif_type == "success") ? "Berhasil" : "Gagal" ?>',
            text: '<?= $notif ?>',
            confirmButtonColor: '#d33'
        });
    });
    </script>
    <?php } ?>

    <script>
    $(document).ready(function() {
        $('#tabelQA').DataTable({
            "pageLength": 10,
            "order": [
                [4, "desc"]
            ], // sort by tanggal
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Berikutnya"
                }
            }
        });
    });
    </script>



</body>

</html>