<?php
include "koneksi.php";
session_start();
$namauser = $_SESSION['namauser'];

// ==================== CREATE ====================
if (isset($_POST['tambah'])) {

	$nama   = $_POST['nama'];
	$tingkat = $_POST['tingkat'];
	$nomor  = $_POST['no_tahun'];
	$petugas = $_POST['petugas'];

	// Pastikan folder upload ada
if (!is_dir("upload")) {
    mkdir("upload", 0777, true);
}

// ambil file lama
$fileku = $_POST['file_lama'];

// jika ada file baru
if (!empty($_FILES['fileku']['name'])) {

    // hapus file lama jika ada
    if (!empty($fileku) && file_exists("upload/" . $fileku)) {
        unlink("upload/" . $fileku);
    }

    // BERSIHKAN nama file baru (Wajib)
    $nama_asli = $_FILES['fileku']['name'];
    $nama_bersih = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $nama_asli);

    // Buat nama final
    $fileku = time() . "_" . $nama_bersih;

    // Upload
    if (move_uploaded_file($_FILES['fileku']['tmp_name'], "upload/" . $fileku)) {
        mysql_query("INSERT INTO eksternal (nama, tingkat, no_tahun_dokumen, petugas, fileku, aktif)
			VALUES ('$nama', '$tingkat', '$nomor', '$petugas', '$fileku', '0')");


	echo "<script>alert('Dokumen berhasil ditambahkan');window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        echo "<script>alert('GAGAL upload file!');</script>";
    }
}

	
}

// ==================== DELETE ====================
if (isset($_GET['delete'])) {

	$id = $_GET['delete'];

	// ambil nama file
	$q = mysql_query("SELECT fileku FROM eksternal WHERE id='$id'");
	$d = mysql_fetch_array($q);

	if (!empty($d['fileku']) && file_exists("upload/" . $d['fileku'])) {
		unlink("upload/" . $d['fileku']);
	}

	mysql_query("DELETE FROM eksternal WHERE id='$id'");

	echo "<script>alert('Dokumen berhasil dihapus');window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
}

// ==================== GET DATA FOR EDIT ====================
$editMode = false;
$editData = array();

if (isset($_GET['edit'])) {
	$editMode = true;
	$id = $_GET['edit'];
	$q = mysql_query("SELECT * FROM eksternal WHERE id='$id'");
	$editData = mysql_fetch_array($q);
}

// ==================== UPDATE ====================
if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$nama = $_POST['nama'];
	$tingkat = $_POST['tingkat'];
	$nomor = $_POST['no_tahun'];
	$petugas = $_POST['petugas'];


	// cek file baru
	$fileku = $_POST['file_lama'];
	if (!empty($_FILES['fileku']['name'])) {

		// hapus file lama
		if (!empty($fileku) && file_exists("upload/" . $fileku)) {
			unlink("upload/" . $fileku);
		}

		$fileku = time() . "_" . $_FILES['fileku']['name'];
		move_uploaded_file($_FILES['fileku']['tmp_name'], "upload/" . $fileku);
	}

	mysql_query("UPDATE eksternal SET
             nama='$nama',
             tingkat='$tingkat',
             no_tahun_dokumen='$nomor',
             petugas='$petugas',
             fileku='$fileku'
             WHERE id='$id'");


	echo "<script>alert('Perubahan berhasil disimpan');window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
    .no-edit {
        pointer-events: none;
        background-color: #e9ecef;
    }
    </style>
</head>

<body class="container mt-4">

    <h4 class="text-center mb-4">
        <b>Dokumen Eksternal (L5)</b>
    </h4>

    <!-- ===================== FORM TAMBAH / EDIT ===================== -->
    <div class="card p-3 mb-4">
        <h5><?= $editMode ? "Edit Dokumen" : "Tambah Dokumen" ?></h5>
        <form method="post" enctype="multipart/form-data">

            <?php if ($editMode) { ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <input type="hidden" name="file_lama" value="<?= $editData['fileku'] ?>">
            <?php } ?>

            <div class="row mb-2">
                <div class="col-md-4">
                    <label>Judul Dokumen</label>
                    <input type="text" name="nama" class="form-control"
                        value="<?= $editMode ? $editData['nama'] : "" ?>" required>
                </div>

                <div class="col-md-3">
                    <label>No & Tahun Dokumen</label>
                    <input type="text" name="no_tahun" class="form-control"
                        value="<?= $editMode ? $editData['no_tahun_dokumen'] : "" ?>" required>
                </div>

                <div class="col-md-2">
                    <label>Tingkat</label>
                    <input type="text" name="tingkat" class="form-control no-edit"
                        value="<?= $editMode ? $editData['tingkat'] : "L5" ?>">
                </div>

                <div class="col-md-3">
                    <label>Petugas</label>
                    <input type="text" name="petugas" class="form-control no-edit"
                        value="<?= $editMode ? $editData['petugas'] : "$namauser" ?>">
                </div>
            </div>


            <div class="mb-2">
                <label>File Dokumen (PDF/DOC/Excel/GAMBAR)</label>
                <input type="file" name="fileku" class="form-control">
                <small>Maximal File bisa upload 5MB</small>

                <?php if ($editMode && !empty($editData['fileku'])) { ?>
                <small>File sekarang: <?= $editData['fileku'] ?></small>
                <?php } ?>
            </div>

            <button type="submit" name="<?= $editMode ? 'update' : 'tambah' ?>" class="btn btn-primary mt-2">
                <?= $editMode ? "Update Dokumen" : "Simpan Dokumen" ?>
            </button>

            <?php if ($editMode) { ?>
            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary mt-2">Batal</a>
            <?php } ?>
        </form>
    </div>

    <!-- ===================== TABEL ===================== -->
    <input id="myInput" type="text" class="form-control mb-3" placeholder="Search...">

    <table class="table table-bordered table-striped">
        <thead>
            <tr class="table-secondary">
                <th width="50">No</th>
                <th>Judul Dokumen</th>
                <th width="120">Tingkat</th>
                <th width="150">Nomor & Tahun</th>
                <th width="180">File</th>
                <th>Petugas</th>
                <th width="120">Aksi</th>
            </tr>
        </thead>

        <tbody id="myTable">
            <?php
			$q = mysql_query("SELECT * FROM eksternal WHERE aktif='0' ORDER BY id ASC");
			$no = 1;
			while ($data = mysql_fetch_array($q)) {
			?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $data['nama'] ?></td>
                <td><?= $data['tingkat'] ?></td>
                <td><?= $data['no_tahun_dokumen'] ?></td>
                <td>
                    <a href="download.php?filename=<?= $data['fileku'] ?>">
                        <?= $data['fileku'] ?>
                    </a>
                </td>
                <td><?= $data['petugas'] ?></td>
                <td>
                    <a href="?edit=<?= $data['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="?delete=<?= $data['id'] ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Hapus dokumen ini?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

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

</body>

</html>