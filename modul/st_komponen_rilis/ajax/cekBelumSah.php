<?php
header('Content-Type: application/json');
require_once '../config/dbi_connect.php';

$nT = isset($_POST['dst_nt']) ? $_POST['dst_nt'] : '';

if (empty($nT)) {
    echo json_encode(array('status' => 'error', 'message' => 'Nomor transaksi tidak ditemukan.'));
    exit;
}

// Ambil total dan belum sah
$q = $dbi->prepare("SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN dst_sah != 1 THEN 1 ELSE 0 END) AS belumSah
    FROM serahterima_detail WHERE dst_notrans = ?");
$q->bind_param("s", $nT);
$q->execute();
$q->bind_result($total, $belumSah);
$q->fetch();
$q->close();

echo json_encode(array(
    'status' => 'ok',
    'total' => (int) $total,
    'belumSah' => (int) $belumSah
));
