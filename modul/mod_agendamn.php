<?php
include('config/db_connect.php');
session_start();
$namauser   = $_SESSION['namauser'];
$namabagian = $_SESSION['namabagian'];
$levelusr   = $_SESSION['leveluser'];

$act = isset($_GET['act']) ? $_GET['act'] : '';

/* ============================================================
    PROSES SIMPAN (tambah)
==============================================================*/
if ($act == "simpan") {

        $tema        = $_POST['tema'];
        $isi_agenda  = $_POST['isi_agenda'];
        $tempat      = $_POST['tempat'];
        $tgl_mulai   = $_POST['tgl_mulai'];
        $tgl_selesai = $_POST['tgl_selesai'];
        $id_user     = $namauser;
        $tgl_posting = date("Y-m-d");

        $q = mysql_query("INSERT INTO agenda
    (tema, isi_agenda, tempat, tgl_mulai, tgl_selesai, tgl_posting, id_user)
    VALUES
    ('$tema','$isi_agenda','$tempat','$tgl_mulai','$tgl_selesai','$tgl_posting','$id_user')");

        echo "<script>
            localStorage.setItem('alertmsg','tambah');
            window.location='pmiadmin.php?module=aturagenda';
          </script>";
        exit();
}

/* ============================================================
    PROSES UPDATE
==============================================================*/
if ($act == "update") {

        $id = $_POST['id_agenda'];

        mysql_query("UPDATE agenda SET
        tema='$_POST[tema]',
        isi_agenda='$_POST[isi_agenda]',
        tempat='$_POST[tempat]',
        tgl_mulai='$_POST[tgl_mulai]',
        tgl_selesai='$_POST[tgl_selesai]',
        id_user='$namauser'
        WHERE id_agenda=$id
    ");

        echo "<script>
            localStorage.setItem('alertmsg','update');
            window.location='pmiadmin.php?module=aturagenda';
          </script>";
        exit();
}

/* ============================================================
    PROSES HAPUS
==============================================================*/
if ($act == "hapus") {

        mysql_query("DELETE FROM agenda WHERE id_agenda=" . intval($_GET['id']));

        echo "<script>
            localStorage.setItem('alertmsg','hapus');
            window.location='pmiadmin.php?module=aturagenda';
          </script>";
        exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Agenda CRUD Bootstrap</title>

    <!-- BOOTSTRAP 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- SWEETALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS + jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
    body {
        background: #f3f4f7;
    }

    .card {
        border-radius: 12px;
    }

    .table thead th {
        background: #ED6161;
        color: white;
    }

    .btn-round {
        border-radius: 20px;
    }
    </style>
</head>

<body>

    <div>

        <?php
                /* ============================================================
    TAMPIL DATA
==============================================================*/
                if ($act == "") {
                ?>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Daftar Agenda</h4>
            </div>

            <div class="card-body">
                <a href="?module=aturagenda&act=tambah" class="btn btn-success btn-round mb-3">
                    + Tambah Agenda
                </a>

                <table id="agendaTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tema</th>
                            <th>Isi Agenda</th>
                            <th>Tempat</th>
                            <th>Tgl Mulai</th>
                            <th>Tgl Selesai</th>
                            <th>Created</th>
                            <th width="130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                                        $sql = mysql_query("SELECT * FROM agenda ORDER BY id_agenda DESC");
                                                        $no = 1;
                                                        while ($r = mysql_fetch_array($sql)) {
                                                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $r['tema']; ?></td>
                            <td><?= $r['tempat']; ?></td>
                            <td><?= $r['isi_agenda']; ?></td>
                            <td><?= $r['tgl_mulai']; ?></td>
                            <td><?= $r['tgl_selesai']; ?></td>
                            <td><?= $r['id_user']; ?></td>
                            <td>
                                <a href="?module=aturagenda&act=edit&id=<?= $r['id_agenda']; ?>"
                                    class="btn btn-warning btn-sm btn-round">
                                    Edit
                                </a>

                                <button class="btn btn-danger btn-sm btn-round"
                                    onclick="hapusData(<?= $r['id_agenda']; ?>)">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>

        <?php
                }

                /* ============================================================
    FORM TAMBAH
==============================================================*/ else if ($act == "tambah") {
                ?>

        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Tambah Agenda</h4>
            </div>

            <div class="card-body">

                <form method="POST" action="?module=aturagenda&act=simpan">

                    <div class="form-group">
                        <label>Tema</label>
                        <input type="text" name="tema" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Isi Agenda</label>
                        <textarea name="isi_agenda" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tempat</label>
                        <input type="text" name="tempat" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary btn-round">Simpan</button>
                    <a href="pmiadmin.php?module=aturagenda" class="btn btn-secondary btn-round">Batal</a>

                </form>

            </div>
        </div>

        <?php
                }

                /* ============================================================
    FORM EDIT
==============================================================*/ else if ($act == "edit") {

                        $id = intval($_GET['id']);
                        $data = mysql_fetch_array(mysql_query("SELECT * FROM agenda WHERE id_agenda=$id"));
                ?>
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0">Edit Agenda</h4>
            </div>

            <div class="card-body">

                <form method="POST" action="?module=aturagenda&act=update">
                    <input type="hidden" name="id_agenda" value="<?= $data['id_agenda']; ?>">

                    <div class="form-group">
                        <label>Tema</label>
                        <input type="text" name="tema" value="<?= $data['tema']; ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Isi Agenda</label>
                        <textarea name="isi_agenda" class="form-control"><?= $data['isi_agenda']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tempat</label>
                        <input type="text" name="tempat" value="<?= $data['tempat']; ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" value="<?= $data['tgl_mulai']; ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" value="<?= $data['tgl_selesai']; ?>" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary btn-round">Update</button>
                    <a href="pmiadmin.php?module=aturagenda" class="btn btn-secondary btn-round">Batal</a>

                </form>

            </div>
        </div>

        <?php } ?>

    </div>


    <script>
    // ===========================================
    // SWEETALERT UNTUK HAPUS
    // ===========================================
    function hapusData(id) {
        Swal.fire({
            title: "Hapus Agenda?",
            text: "Data yang sudah dihapus tidak dapat dikembalikan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = "?module=aturagenda&act=hapus&id=" + id;
            }
        })
    }

    // ===========================================
    // TAMPILKAN SWEETALERT JIKA ADA AKSI
    // ===========================================
    window.onload = function() {
        let msg = localStorage.getItem("alertmsg");
        if (msg == "tambah") {
            Swal.fire("Berhasil!", "Agenda berhasil ditambahkan", "success");
        }
        if (msg == "update") {
            Swal.fire("Berhasil!", "Agenda telah diperbarui", "success");
        }
        if (msg == "hapus") {
            Swal.fire("Berhasil!", "Agenda telah dihapus", "success");
        }
        localStorage.removeItem("alertmsg");
    };

    // ===========================================
    // DATEPICKER klik area field (Chrome fix)
    // ===========================================
    document.querySelectorAll('input[type="date"]').forEach(function(el) {
        el.addEventListener('click', function() {
            this.showPicker();
        });
    });
    </script>
    <script>
    // ===========================================
    // BATAS TANGGAL: tgl_selesai tidak boleh sebelum tgl_mulai
    // ===========================================

    const mulai = document.querySelector("input[name='tgl_mulai']");
    const selesai = document.querySelector("input[name='tgl_selesai']");

    if (mulai && selesai) {

        // Ketika tanggal mulai diganti
        mulai.addEventListener("change", function() {
            selesai.min = this.value;

            // Jika tanggal selesai < tanggal mulai ? reset
            if (selesai.value < this.value) {
                selesai.value = this.value;
            }
        });

        // Ketika klik tanggal selesai ? set batas min
        selesai.addEventListener("click", function() {
            if (mulai.value !== "") {
                this.min = mulai.value;
            }
        });
    }
    </script>
    <script>
    $(document).ready(function() {
        $('#agendaTable').DataTable({
            "paging": true, // Pagination aktif
            "lengthChange": true, // Pilihan jumlah baris
            "searching": true, // Fitur pencarian
            "ordering": true, // Bisa klik header untuk sort
            "info": true, // Menampilkan info "Showing x of y entries"
            "autoWidth": false
        });
    });
    </script>


</body>

</html>