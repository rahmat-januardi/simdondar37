<?php
require_once '../config/dbi_connect.php';

if (!empty($_POST['id'])) {
    // $stmt = $dbi->prepare("UPDATE collectSite_d SET csd_noKantong = ?, csd_kodeDonor = ?, csd_golDarah = ?, csd_petugasInput = ? WHERE csd_id = ?");
    // $stmt->bind_param("ssssi", $_POST['no_kantong'], $_POST['kode_donor'], $_POST['gol_darah'], $_POST['petugas'], $_POST['id']);
    $stmt = $dbi->prepare("UPDATE collectSite_d SET csd_petugasInput = ? WHERE csd_id = ?");
    $stmt->bind_param("si",  $_POST['petugas'], $_POST['id']);
    $success = $stmt->execute();

    if ($success) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'fail', 'error' => $stmt->error]);
    }
}
?>
