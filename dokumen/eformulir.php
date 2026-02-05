<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "koneksi.php";

$uploadDir = './upload/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$error = '';
$success = '';
$editData = null;

// === SIMPAN DOKUMEN ===
if (isset($_POST['submit'])) {
    $nama   = $_POST['nama'];
    $bidang = $_POST['bidang'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        if ($_FILES['file']['size'] > 5 * 1024 * 1024) {
            $error = "Ukuran file maksimal 5 MB!";
        } else {
            $originalName = basename($_FILES['file']['name']);
            $targetPath = $uploadDir . time() . '_' . $originalName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                mysql_query("INSERT INTO master_eform (nama_dokumen, bidang, nama_file, lokasi_file)
                             VALUES ('$nama', '$bidang', '$originalName', '$targetPath')");
                $success = "Dokumen berhasil disimpan.";
            } else {
                $error = "Gagal mengupload file.";
            }
        }
    } else {
        $error = "File tidak ditemukan atau terjadi error saat upload.";
    }
}

// === MODE EDIT ===
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysql_query("SELECT * FROM master_eform WHERE id=$id");
    $editData = mysql_fetch_assoc($res);
}

// === UPDATE ===
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama   = $_POST['nama'];
    $bidang = $_POST['bidang'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        if ($_FILES['file']['size'] > 5 * 1024 * 1024) {
            $error = "Ukuran file maksimal 5 MB!";
        } else {
            $originalName = basename($_FILES['file']['name']);
            $targetPath = $uploadDir . time() . '_' . $originalName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                $old = mysql_query("SELECT * FROM master_eform WHERE id=$id");
                $oldData = mysql_fetch_assoc($old);
                if ($oldData && file_exists($oldData['lokasi_file'])) {
                    unlink($oldData['lokasi_file']);
                }

                mysql_query("UPDATE master_eform SET
                    nama_dokumen='$nama', bidang='$bidang',
                    nama_file='$originalName', lokasi_file='$targetPath'
                    WHERE id=$id");

                $success = "Data berhasil diperbarui.";
                $editData = null;
            } else {
                $error = "Gagal mengupload file baru.";
            }
        }
    } else {
        mysql_query("UPDATE master_eform SET nama_dokumen='$nama', bidang='$bidang' WHERE id=$id");
        $success = "Data berhasil diperbarui.";
        $editData = null;
    }
}

// === HAPUS ===
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $res = mysql_query("SELECT * FROM master_eform WHERE id=$id");
    $data = mysql_fetch_assoc($res);

    if ($data && file_exists($data['lokasi_file'])) {
        unlink($data['lokasi_file']);
    }

    mysql_query("DELETE FROM master_eform WHERE id=$id");
    header("Location: /dokumen/eformulir.php");
    exit;
}

// === FILTER & SEARCH ===
$filter_bidang = isset($_GET['filter_bidang']) ? $_GET['filter_bidang'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$where = "WHERE 1=1";
if ($filter_bidang != '') {
    $where .= " AND bidang = '".mysql_real_escape_string($filter_bidang)."'";
}
if ($search != '') {
    $where .= " AND nama_dokumen LIKE '%".mysql_real_escape_string($search)."%'";
}

// Daftar bidang
$bidangList = array(
    "Manajemen Kualitas", "Penyediaan Donor", "Kerjasama Hukum dan Humas", "Teknologi Informasi",
    "Penyediaan Darah", "Rujukan IMLTD", "Rujukan Imunohematologi", "Litbang",
    "Produksi", "Kalibrasi", "Pengawasan Mutu", "Pembinaan Kualitas",
    "Umum", "Logistik", "Sekretariat", "Rumah Tangga", "Wisma",
    "Kepegawaian", "Keuangan", "Diklat"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Upload Dokumen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        input[type=text], select, input[type=file] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 10px;
            border: 1px solid #aaa;
        }
        button {
            padding: 8px 16px;
            background: #28a745;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #eee;
        }
        .actions a {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<h1 style="text-align: center;" >Master E-Formulir</h1>
<hr>

<h2><?php echo $editData ? 'Edit Dokumen' : 'Tambah Dokumen'; ?></h2>

<?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green'>$success</p>"; ?>

<form method="post" enctype="multipart/form-data">
    <?php if ($editData): ?>
        <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
    <?php endif; ?>
    <label>Nama Dokumen:</label>
    <input type="text" name="nama" required value="<?php echo $editData ? $editData['nama_dokumen'] : ''; ?>">

    <label>Bidang:</label>
    <select name="bidang" required>
        <option value="">-- Pilih Bidang --</option>
        <?php
        foreach ($bidangList as $b) {
            $selected = ($filter_bidang === $b) ? 'selected' : '';
            echo "<option value=\"$b\" $selected>$b</option>";
        }
        ?>
    </select>

    <label>File Dokumen (max 5 MB):</label>
    <input type="file" name="file" <?php echo $editData ? '' : 'required'; ?>>

    <button type="submit" name="<?php echo $editData ? 'update' : 'submit'; ?>">
        <?php echo $editData ? 'Update' : 'Simpan'; ?>
    </button>
    <?php if ($editData): ?>
        <a href="/dokumen/eformulir.php" style="margin-left:10px;">Batal</a>
    <?php endif; ?>
</form>

<h2>Daftar Dokumen</h2>

<form method="get" style="margin-bottom:10px;">
    <input type="text" name="search" placeholder="Cari nama dokumen" value="<?php echo htmlspecialchars($search); ?>">
    <select name="filter_bidang">
        <option value="">Semua Bidang</option>
        <?php
        foreach ($bidangList as $b) {
            $selected = ($filter_bidang === $b) ? 'selected' : '';
            echo "<option value=\"$b\" $selected>$b</option>";
        }
        ?>
    </select>
    <button type="submit">Filter</button>
    <a href="/dokumen/eformulir.php" style="margin-left:10px;">Reset</a>
</form>

<table>
    <tr>
        <th>No</th>
        <th>Nama Dokumen</th>
	<th>Format</th>
        <th>Bidang</th>
        <th>File</th>
        <th>Aksi</th>
    </tr>
    <?php
    $no = 1;
    $res = mysql_query("SELECT * FROM master_eform $where ORDER BY id DESC");
    while ($row = mysql_fetch_assoc($res)) {
        $preview = preg_match('/\\.pdf$/i', $row['nama_file'])
                   ? "<a href='{$row['lokasi_file']}' target='_blank'>Preview</a>"
                   : "<span style='color:gray'>Preview Tidak tersedia</span>";

	$format = strtoupper(pathinfo($row['nama_file'], PATHINFO_EXTENSION));

        echo "<tr>
                <td>$no</td>
                <td>{$row['nama_dokumen']}</td>
		<td>$format</td>
                <td>{$row['bidang']}</td>
                <td><a href='{$row['lokasi_file']}' target='_blank'>Download</a></td>
                <td class='actions'>
                    $preview |
                    <a href='?edit={$row['id']}'>Edit</a> |
                    <a href='?delete={$row['id']}' onclick=\"return confirm('Hapus data ini?')\">Hapus</a>
                </td>
              </tr>";
        $no++;
    }
    ?>
</table>

</body>
</html>