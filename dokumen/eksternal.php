<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

include "koneksi.php";
session_start();

$namauser = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';

$uploadDir = dirname(__FILE__) . "/upload/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function bersihkanNamaFile($nama)
{
    return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $nama);
}

function uploadFile($inputName, $uploadDir)
{
    if (!isset($_FILES[$inputName]) || empty($_FILES[$inputName]['name'])) {
        return false;
    }

    $namaAsli = $_FILES[$inputName]['name'];
    $namaBersih = bersihkanNamaFile($namaAsli);
    $namaFinal = time() . "_" . $namaBersih;
    $tujuan = $uploadDir . $namaFinal;

    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $tujuan)) {
        return $namaFinal;
    }

    return false;
}

/* ==================== TAMBAH DATA ==================== */
if (isset($_POST['tambah'])) {

    $nama    = isset($_POST['nama']) ? mysql_real_escape_string($_POST['nama']) : '';
    $tingkat = isset($_POST['tingkat']) ? mysql_real_escape_string($_POST['tingkat']) : '';
    $nomor   = isset($_POST['no_tahun']) ? mysql_real_escape_string($_POST['no_tahun']) : '';
    $petugas = isset($_POST['petugas']) ? mysql_real_escape_string($_POST['petugas']) : '';

    if (empty($_FILES['fileku']['name'])) {
        echo "<script>alert('Silakan pilih file terlebih dahulu');</script>";
    } else {
        $fileku = uploadFile('fileku', $uploadDir);

        if ($fileku !== false) {
            mysql_query("INSERT INTO eksternal (nama, tingkat, no_tahun_dokumen, petugas, fileku, aktif)
                VALUES ('$nama', '$tingkat', '$nomor', '$petugas', '$fileku', '0')");

            echo "<script>alert('Dokumen berhasil ditambahkan');window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal upload file! Cek permission folder upload.');</script>";
        }
    }
}

/* ==================== HAPUS DATA ==================== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $q = mysql_query("SELECT fileku FROM eksternal WHERE id='$id'");
    $d = mysql_fetch_array($q);

    if ($d && !empty($d['fileku']) && file_exists($uploadDir . $d['fileku'])) {
        unlink($uploadDir . $d['fileku']);
    }

    mysql_query("DELETE FROM eksternal WHERE id='$id'");

    echo "<script>alert('Dokumen berhasil dihapus');window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
}

/* ==================== AMBIL DATA EDIT ==================== */
$editMode = false;
$editData = array();

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = intval($_GET['edit']);

    $q = mysql_query("SELECT * FROM eksternal WHERE id='$id'");
    $editData = mysql_fetch_array($q);

    if (!$editData) {
        $editMode = false;
        $editData = array();
    }
}

/* ==================== UPDATE DATA ==================== */
if (isset($_POST['update'])) {

    $id      = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama    = isset($_POST['nama']) ? mysql_real_escape_string($_POST['nama']) : '';
    $tingkat = isset($_POST['tingkat']) ? mysql_real_escape_string($_POST['tingkat']) : '';
    $nomor   = isset($_POST['no_tahun']) ? mysql_real_escape_string($_POST['no_tahun']) : '';
    $petugas = isset($_POST['petugas']) ? mysql_real_escape_string($_POST['petugas']) : '';
    $filelama = isset($_POST['file_lama']) ? $_POST['file_lama'] : '';
    $fileku = $filelama;

    if (!empty($_FILES['fileku']['name'])) {

        if (!empty($filelama) && file_exists($uploadDir . $filelama)) {
            unlink($uploadDir . $filelama);
        }

        $uploadBaru = uploadFile('fileku', $uploadDir);

        if ($uploadBaru !== false) {
            $fileku = $uploadBaru;
        } else {
            echo "<script>alert('Gagal upload file baru!');</script>";
            exit;
        }
    }

    mysql_query("UPDATE eksternal SET
        nama='$nama',
        tingkat='$tingkat',
        no_tahun_dokumen='$nomor',
        petugas='$petugas',
        fileku='$fileku'
        WHERE id='$id'");

    echo "<script>alert('Perubahan berhasil disimpan');window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dokumen Eksternal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
        }

        .card {
            border-radius: 10px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .readonly {
            background: #e9ecef;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="container mt-4 mb-5">

        <div class="card shadow-sm mb-4">
            <div class="card-body text-center">
                <h3 class="mb-0"><b>Dokumen Eksternal (L5)</b></h3>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <?php echo $editMode ? "Edit Dokumen" : "Tambah Dokumen"; ?>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <?php if ($editMode) { ?>
                        <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                        <input type="hidden" name="file_lama" value="<?php echo $editData['fileku']; ?>">
                    <?php } ?>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Judul Dokumen</label>
                            <input type="text" name="nama" class="form-control" required
                                value="<?php echo $editMode ? $editData['nama'] : ''; ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>No & Tahun Dokumen</label>
                            <input type="text" name="no_tahun" class="form-control" required
                                value="<?php echo $editMode ? $editData['no_tahun_dokumen'] : ''; ?>">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label>Tingkat</label>
                            <input type="text" name="tingkat" class="form-control readonly"
                                value="<?php echo $editMode ? $editData['tingkat'] : 'L5'; ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Petugas</label>
                            <input type="text" name="petugas" class="form-control readonly"
                                value="<?php echo $editMode ? $editData['petugas'] : $namauser; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>File Dokumen</label>
                        <input type="file" name="fileku" class="form-control">
                        <small class="text-muted">Format bebas (PDF/DOC/XLS/Gambar) - Maksimal 5MB</small>

                        <?php if ($editMode && !empty($editData['fileku'])) { ?>
                            <br>
                            <small class="text-success">File sekarang: <b><?php echo $editData['fileku']; ?></b></small>
                        <?php } ?>
                    </div>

                    <button type="submit" name="<?php echo $editMode ? 'update' : 'tambah'; ?>" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        <?php echo $editMode ? 'Update Dokumen' : 'Simpan Dokumen'; ?>
                    </button>

                    <?php if ($editMode) { ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    <?php } ?>
                </form>
            </div>
        </div>

        <div class="mb-3">
            <input id="myInput" type="text" class="form-control" placeholder="Cari dokumen...">
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th width="60">No</th>
                            <th>Judul Dokumen</th>
                            <th width="100">Tingkat</th>
                            <th width="180">Nomor & Tahun</th>
                            <th width="250">File</th>
                            <th width="150">Petugas</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="myTable">
                        <?php
                        $q = mysql_query("SELECT * FROM eksternal WHERE aktif='0' ORDER BY id DESC");
                        $no = 1;
                        while ($data = mysql_fetch_array($q)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $data['nama']; ?></td>
                                <td><?php echo $data['tingkat']; ?></td>
                                <td><?php echo $data['no_tahun_dokumen']; ?></td>
                                <td>
                                    <?php if (!empty($data['fileku'])) { ?>
                                        <a href="download.php?filename=<?php echo $data['fileku']; ?>" target="_blank">
                                            <?php echo $data['fileku']; ?>
                                        </a>
                                    <?php } else { ?>
                                        <span class="text-danger">Tidak ada file</span>
                                    <?php } ?>
                                </td>
                                <td><?php echo $data['petugas']; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $data['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $data['id']; ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus dokumen ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
</body>

</html>