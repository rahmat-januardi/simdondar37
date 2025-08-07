<?php
require_once "../config/dbi_connect.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "SELECT * FROM master_kantong WHERE id = ?";
    $stmt = $dbi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Ambil metadata kolom
    $meta = $stmt->result_metadata();
    $fields = array();
    $row = array();

    // Siapkan bind_result dinamis
    while ($field = $meta->fetch_field()) {
        $fields[$field->name] = &$row[$field->name];
    }

    // Bind kolom ke variabel dalam $row
    call_user_func_array(array($stmt, 'bind_result'), $fields);

    // Fetch hasilnya
    if ($stmt->fetch()) {
        echo json_encode(array("success" => true, "data" => $row));
    } else {
        echo json_encode(array("success" => false, "message" => "Data tidak ditemukan."));
    }

    $stmt->close();
} else {
    echo json_encode(array("success" => false, "message" => "ID tidak diberikan."));
}

