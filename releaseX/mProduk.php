<?php
require_once "../config/dbi_connect.php";

$term = $_GET['term'] ? $_GET['term'] : '';
$data = array();

if ($stmt = $dbi->prepare("SELECT Nama FROM produk WHERE (`stats` = 0 OR `stats` IS NULL) AND Nama LIKE CONCAT('%', ?, '%')")) {
    $stmt->bind_param("s", $term);
    $stmt->execute();

    // Bind kolom hasil ke variabel
    $stmt->bind_result($nama);

    // Fetch hasilnya satu per satu
    while ($stmt->fetch()) {
        $data[] = array('Nama' => $nama);
    }

    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($data);

