<?php
include "../koneksi.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Filter
$filter_bidang = isset($_GET['filter_bidang']) ? $_GET['filter_bidang'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Daftar bidang
$bidangList = array(
    "Manajemen Kualitas", "Penyediaan Donor", "Kerjasama Hukum dan Humas", "Teknologi Informasi",
    "Penyediaan Darah", "Rujukan IMLTD", "Rujukan Imunohematologi", "Litbang",
    "Produksi", "Kalibrasi", "Pengawasan Mutu", "Pembinaan Kualitas",
    "Umum", "Logistik", "Sekretariat", "Rumah Tangga", "Wisma",
    "Kepegawaian", "Keuangan", "Diklat"
);

// Query filter
$where = "WHERE 1=1";
if ($filter_bidang != '') {
    $where .= " AND bidang = '".mysql_real_escape_string($filter_bidang)."'";
}
if ($search != '') {
    $where .= " AND nama_dokumen LIKE '%".mysql_real_escape_string($search)."%'";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Formulir</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f8f8f8; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #eaeaea; }
        .filter { margin-bottom: 15px; }
    </style>
</head>
<body>

<h2>Daftar Dokumen</h2>

<form method="get" class="filter">
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
</form>

<table>
    <tr>
        <th>No</th>
        <th>Nama Dokumen</th>
	<th>Format</th>
        <th>Bidang</th>
        <th>File</th>
        <th>Preview</th>
    </tr>
    <?php
    $no = 1;
    $res = mysql_query("SELECT * FROM master_eform $where ORDER BY id DESC");
    if (mysql_num_rows($res) == 0) {
        echo "<tr><td colspan='5'>Tidak ada dokumen ditemukan.</td></tr>";
    }
    while ($row = mysql_fetch_assoc($res)) {
        $preview = preg_match('/\.pdf$/i', $row['nama_file'])
            ? "<a href='../{$row['lokasi_file']}' target='_blank'>Preview</a>"
            : "<span style='color:gray'>Preview Tidak tersedia</span>";

	$format = strtoupper(pathinfo($row['nama_file'], PATHINFO_EXTENSION));

        echo "<tr>
                <td>$no</td>
                <td>{$row['nama_dokumen']}</td>
		<td>$format</td>
                <td>{$row['bidang']}</td>
                <td><a href='download-eform.php?file={$row['lokasi_file']}'>Download</a></td>
                <td>$preview</td>
              </tr>";
        $no++;
    }
    ?>
</table>

</body>
</html>