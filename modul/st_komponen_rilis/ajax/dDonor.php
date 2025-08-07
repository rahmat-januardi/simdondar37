<?php
require_once '../config/dbi_connect.php'; // pastikan koneksi sudah benar

// Ambil ID dari URL parameter
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $csd_kodeDonor = $_GET['id'];

    // Query untuk mendapatkan detail kantong darah
    $query = "SELECT * FROM collectSite_d WHERE csd_kodeDonor = ?";
    $stmt = $dbi->prepare($query);
    $stmt->bind_param("s", $csd_kodeDonor);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan, tampilkan di modal
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo '<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detail Kantong Darah - ' . $data['csd_noKantong'] . '</h4>
            </div>';
        echo '<div class="modal-body">';
        echo '<p><strong>No. Kantong:</strong> ' . $data['csd_noKantong'] . '</p>';
        echo '<p><strong>Kode Pendonor:</strong> ' . $data['csd_kodeDonor'] . '</p>';
        echo '<p><strong>Gol. Darah:</strong> ' . $data['csd_golDarah'] . '</p>';
        echo '<p><strong>Tanggal Kadaluarsa:</strong> ' . $data['on_insert'] . '</p>';
        echo '<p><strong>Petugas:</strong> ' . $data['csd_petugasInput'] . '</p>';
        echo '</div>';
        echo '<div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>';
    } else {
        echo '<p>Data tidak ditemukan.</p>';
    }
} else {
    echo '<p>ID kantong tidak diberikan.</p>';
    exit;
}
?>