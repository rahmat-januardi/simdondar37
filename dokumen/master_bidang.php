<?php

session_start();

include "koneksi.php";

// Dummy session login (ganti sesuai loginmu)
if (!isset($_SESSION['namauser'])) {
    $_SESSION['namauser'] = "admin"; // default sementara
}

$petugas = $_SESSION['namauser'];
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error'], $_SESSION['success']);

$editData = null;

// Form submit
if (isset($_POST['submit'])) {
    $bidang = trim($_POST['bidang']);
    $kodeBidang = trim($_POST['kodeBidang']);

    if ($bidang == '' || $kodeBidang == '') {
        $_SESSION['error'] = "Bidang dan Kode Bidang tidak boleh kosong!";
    } else {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $id = (int)$_POST['id'];
            $query = "UPDATE master_bidang SET bidang='$bidang', kode_bidang='$kodeBidang', petugas_edit='$petugas', updated_time=NOW() WHERE id=$id";
        } else {
            $query = "INSERT INTO master_bidang (bidang, kode_bidang, petugas_input, created_time) VALUES ('$bidang', '$kodeBidang', '$petugas', NOW())";
        }

        if (mysql_query($query)) {
            $_SESSION['success'] = "Data berhasil disimpan.";
        } else {
            $_SESSION['error'] = "Gagal menyimpan data: " . mysql_error();
        }
    }
    header("Location: master_bidang.php");
    exit;
}

// Edit
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysql_query("SELECT * FROM master_bidang WHERE id=$id");
    $editData = mysql_fetch_assoc($res);
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysql_query("DELETE FROM master_bidang WHERE id=$id")) {
        $_SESSION['success'] = "Data berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . mysql_error();
    }
    header("Location: master_bidang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Master Bidang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
    .container {
        max-width: 800px;
        margin: auto;
        margin-top: 20px;
    }

    @media (max-width: 600px) {
        table {
            font-size: 13px;
        }

        .card-title {
            font-size: 16px;
        }
    }
    </style>
</head>

<body>
    <div class="container mt-3">
        <h4 class="text-center mb-3">Master Bidang</h4>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Form di sebelah kiri -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header py-2">
                        <strong><?php echo $editData ? 'Edit Bidang' : 'Tambah Bidang'; ?></strong>
                    </div>
                    <div class="card-body py-2">
                        <form method="post" autocomplete="off">
                            <?php if ($editData): ?>
                            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                            <?php endif; ?>
                            <div class="mb-2">
                                <label class="form-label">Nama Bidang</label>
                                <input type="text" name="bidang" class="form-control form-control-sm" required
                                    value="<?php echo $editData ? htmlspecialchars($editData['bidang']) : ''; ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Kode Bidang</label>
                                <input type="text" name="kodeBidang" class="form-control form-control-sm" required
                                    value="<?php echo $editData ? htmlspecialchars($editData['kode_bidang']) : ''; ?>">
                            </div>
                            <div class="text-end">
                                <button type="submit" name="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $editData ? 'Update' : 'Simpan'; ?>
                                </button>
                                <?php if ($editData): ?>
                                <a href="master_bidang.php" class="btn btn-sm btn-secondary">Batal</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel di sebelah kanan -->
            <div class="col-md-8 mt-3 mt-md-0">
                <div class="card">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <strong>Daftar Bidang</strong>
                        <input type="text" id="searchInput" class="form-control form-control-sm w-50"
                            placeholder="Cari...">
                    </div>
                    <div class="card-body py-2">
                        <table class="table table-bordered table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Nama Bidang</th>
                                    <th>Kode</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dataTable">
                                <?php
                                $no = 1;
                                $q = mysql_query("SELECT * FROM master_bidang ORDER BY bidang ASC");
                                while ($row = mysql_fetch_assoc($q)) {
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['bidang']); ?></td>
                                    <td><?php echo htmlspecialchars($row['kode_bidang']); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $row['id']; ?>"
                                            class="btn btn-warning btn-sm btn-edit" title="Edit"><i
                                                class="fas fa-edit"></i></a>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus data ini?')"><i
                                                class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php if (mysql_num_rows($q) == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Pencarian -->
    <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let val = this.value.toLowerCase();
        let rows = document.querySelectorAll('#dataTable tr');
        rows.forEach(function(row) {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(val) ? '' : 'none';
        });
    });
    </script>
</body>


</html>