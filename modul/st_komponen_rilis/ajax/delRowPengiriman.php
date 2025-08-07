<?php
require_once '../config/dbi_connect.php';

if (!empty($_POST['nomor_kantong'])) {
    $nomor = $_POST['nomor_kantong'];
    $stmt = $dbi->prepare("DELETE FROM serahterima_detail_tmp WHERE dst_id = ?");
    $stmt->bind_param("i", $nomor);
    $stmt->execute();
    $stmt->close();
    echo json_encode(array("status" => "ok", "deleted" => 1));
}

if (!empty($_POST['multi']) && is_array($_POST['multi'])) {
    $kantongs = $_POST['multi'];
    // UNTUK PHP 5.4++
    //$placeholders = implode(',', array_fill(0, count($kantongs), '?'));
    //$stmt = $dbi->prepare("DELETE FROM serahterima_detail_tmp WHERE dst_id IN ($placeholders)");
    //$stmt->bind_param(str_repeat('i', count($kantongs)), ...$kantongs);
    //$stmt->execute();
    //$stmt->close();

    // UNTUK PHP 5.3
    $placeholders = implode(',', array_fill(0, count($kantongs), '?'));
    $types = str_repeat('i', count($kantongs));
    $sql = "DELETE FROM serahterima_detail_tmp WHERE dst_id IN ($placeholders)";
    $stmt = $dbi->prepare($sql);
    if ($stmt) {
        // Gabungkan tipe dan parameter ke dalam satu array
        $params = array_merge(array($types), $kantongs);

        // Konversi ke reference (dibutuhkan oleh call_user_func_array)
        $refs = array();
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }

        // Panggil bind_param secara dinamis
        call_user_func_array(array($stmt, 'bind_param'), $refs);

        if ($stmt->execute()) {
            echo "Data berhasil dihapus.";
        } else {
            echo "Error saat eksekusi: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Prepare failed: " . $dbi->error;
    }

    // Susun parameter dengan referensi (khusus PHP 5.3)
    // $params = array_merge(array($types), $kantongs);
    // $bind_names = array();
    // foreach ($params as $key => $value) {
    //     $bind_names[$key] = &$params[$key];
    // }
    // call_user_func_array(array($stmt, 'bind_param'), $bind_names);
    // $stmt->execute();

    echo json_encode(array("status" => "ok", "deleted" => count($kantongs)));
}

if ($dbi->error) {
    echo json_encode(array("status" => "fail", "error" => $dbi->error));
} else {
    $selRow = $dbi->query("SELECT COUNT(*) as total FROM serahterima_detail_tmp");
    if ($selRow === false) {
        echo json_encode(array("status" => "fail", "error" => $dbi->error));
        exit;
    }
    $row = $selRow->fetch_assoc();
    $total = $row['total'];
    if ($total > 0) {
        // echo json_encode(array("status" => "ok", "deleted" => $total));
        exit;
    }
    // Jika tidak ada data, lakukan TRUNCATE
    $truncateTable = $dbi->query("TRUNCATE TABLE serahterima_detail_tmp");
    if ($truncateTable === false) {
        echo json_encode(array("status" => "fail", "error" => $dbi->error));
        exit;
    }
    echo json_encode(array("status" => "ok", "deleted" => 0));
}
