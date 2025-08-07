<?php
include '../config/dbi_connect.php'; // Sesuaikan path ke koneksi database Anda

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID merk tidak ditemukan.</div>";
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM master_kantong WHERE id = $id";
$result = $dbi->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    switch ($row['jenis']) {
        case 1:
            $row['jenis'] = 'Single';
            break;
        case 2:
            $row['jenis'] = 'Double';
            break;
        case 3:
            $row['jenis'] = 'Triple';
            break;
        case 4:
            $row['jenis'] = 'Quadruple';
            break;
        default:
            $row['jenis'] = 'Unknown';
            break;
    }
    ?>
    <div class="container-fluid border p-3">
        <div class="row mb-2 align-items-center border-bottom pb-2">
            <div class="col-sm-1 font-weight-bold text-truncate"><strong>Merk</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['merk']) ?></div>
            <div class="col-sm-1 font-weight-bold text-truncate"><strong>Jenis</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['jenis']) ?></div>
            <div class="col-sm-1 font-weight-bold text-truncate"><strong>Volume</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['vol']) ?></div>
            <div class="col-sm-2 font-weight-bold text-truncate"><strong>Antikoagulant</strong></div>
            <div class="col-sm-1 text-truncate">: <?= htmlspecialchars($row['antikoagulant']) ?></div>
        </div>
        <div class="row mb-2 align-items-center border-bottom pb-2">
        </div>
        <div class="row mb-2 align-items-center border-bottom pb-2">
            <div class="col-sm-2 font-weight-bold text-truncate"><strong>Nama Kantong</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['namakantong']) ?></div>
            <div class="col-sm-2 font-weight-bold text-truncate"><strong>Company</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['company']) ?></div>
            <div class="col-sm-2 font-weight-bold text-truncate"><strong>Composition</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['composition']) ?></div>
        </div>
        <div class="row mb-2 align-items-center border-bottom pb-2">
        </div>
        <div class="row mb-2 align-items-center border-bottom pb-2">
            <div class="col-sm-2 font-weight-bold text-truncate"><strong>Texture</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['texture']) ?></div>
            <div class="col-sm-3 font-weight-bold text-truncate"><strong>Anticoagulant Name</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['anticoagulant_name']) ?></div>
            <div class="col-sm-2 font-weight-bold text-truncate"><strong>Standard Bag</strong></div>
            <div class="col-sm-1 text-truncate">: <?= htmlspecialchars($row['standard_bag']) ?></div>
        </div>
        <div class="row mb-2 align-items-center border-bottom pb-2">
        </div>
        <div class="row mb-2 align-items-center border-bottom pb-2">
            <div class="col-sm-2 font-weight-bold text-truncate"><strong>Standard ACD</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['standard_acd']) ?></div>
            <div class="col-sm-1 font-weight-bold text-truncate"><strong>Lisence</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['license']) ?></div>
            <div class="col-sm-1 font-weight-bold text-truncate"><strong>Oleh</strong></div>
            <div class="col-sm-2 text-truncate">: <?= htmlspecialchars($row['licenseby']) ?></div>
        </div>
        <div class="row mb-2 align-items-center border-bottom pb-2">
        </div>
        <?php
        $produk = array(
            'Produk Utama' => !empty($row['pr_utama']) ? $row['pr_utama'] : 'Kosong',
            'Produk Satelit 1' => !empty($row['pr_s1']) ? $row['pr_s1'] : 'Kosong',
            'Produk Satelit 2' => !empty($row['pr_s2']) ? $row['pr_s2'] : 'Kosong',
            'Produk Satelit 3' => !empty($row['pr_s3']) ? $row['pr_s3'] : 'Kosong',
            'Produk Satelit 4' => !empty($row['pr_s4']) ? $row['pr_s4'] : 'Kosong',
            'Produk Satelit 5' => !empty($row['pr_s5']) ? $row['pr_s5'] : 'Kosong',
            'Produk Satelit 6' => !empty($row['pr_s6']) ? $row['pr_s6'] : 'Kosong',
            'Produk Satelit 7' => !empty($row['pr_s7']) ? $row['pr_s7'] : 'Kosong',
        );

        $berat = array(
            'Berat Utama' => !empty($row['berat_ku']) ? $row['berat_ku'] : 'Kosong',
            'Berat Satelit 1' => !empty($row['berat_s1']) ? $row['berat_s1'] : 'Kosong',
            'Berat Satelit 2' => !empty($row['berat_s2']) ? $row['berat_s2'] : 'Kosong',
            'Berat Satelit 3' => !empty($row['berat_s3']) ? $row['berat_s3'] : 'Kosong',
            'Berat Satelit 4' => !empty($row['berat_s4']) ? $row['berat_s4'] : 'Kosong',
            'Berat Satelit 5' => !empty($row['berat_s5']) ? $row['berat_s5'] : 'Kosong',
            'Berat Satelit 6' => !empty($row['berat_s6']) ? $row['berat_s6'] : 'Kosong',
            'Berat Satelit 7' => !empty($row['berat_s7']) ? $row['berat_s7'] : 'Kosong',
        );
        foreach ($produk as $label => $value) {
            $beratLabel = str_replace('Produk', 'Berat', $label);
            $beratValue = isset($berat[$beratLabel]) ? $berat[$beratLabel] : 'Kosong';
            ?>
            <div class="row mb-2 align-items-center border-bottom pb-2">
                <div class="col-sm-2 font-weight-bold"><strong><?= htmlspecialchars($beratLabel) ?></strong></div>
                <div class="col-sm-2 text-left">: <?= htmlspecialchars($beratValue) ?></div>
                <div class="col-sm-2 font-weight-bold"><strong><?= htmlspecialchars($label) ?></strong></div>
                <div class="col-sm-2 text-left">: <?= htmlspecialchars($value) ?></div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
} else {
    echo "<div class='alert alert-warning'>Data tidak ditemukan untuk ID $id.</div>";
}
?>
