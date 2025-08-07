<?php
require_once '../config/db_connect.php';

if (!empty($_POST['id'])) {
    $id = $_POST['id'];
    $tipe = $_POST['tipe']; // kantong atau pendonor

    if ($tipe == 'kantong') {
        $stmt = $dbi->prepare("SELECT * FROM `collectSite_d` WHERE `csd_id` = ?");
    } else {
        $stmt = $dbi->prepare("SELECT * FROM `pendonor` WHERE `Kode` = ?");
    }

    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    echo json_encode($data);
}

