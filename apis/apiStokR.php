<?php
error_reporting(E_ALL);
$hariini    = DATE("Y-m-d H:i:s");
$d          = date('d');
$m          = date('m');
$y          = date('Y');
$apKey    = '123papaT5'.$m.$d.$y;

// Periksa API key yang dikirim oleh klien
/**
if (isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] === "Bearer $apKey") {
    // API key valid, izinkan akses ke API
} else {
    header("HTTP/1.0 401 Unauthorized");
    //echo json_encode(['error' => 'API key tidak valid']);
    echo json_encode(array('error' => 'API key tidak valid'));
    exit;
}
*/
include 'config/dbi_connect.php';

$query = "SELECT * FROM `v_stok_release_all`";
$result = mysqli_query($dbi, $query);

if ($result) {
    $data = array();

    while ($row = mysqli_fetch_assoc($result)) {
        // Ganti nilai NULL dengan "-"
        foreach ($row as $key => $value) {
            if ($value === NULL) {
                $row[$key] = '-';
            }
        }

        $data[] = $row;
    }

    // Buat respons JSON
    $response = json_encode($data);

    // Set header sebagai JSON
    header('Content-Type: application/json');

    // Tampilkan respons
    echo $response;
} else {
    // Handle kesalahan jika query gagal
echo json_encode(array('error' => 'Gagal mengambil data dari database'));

}

// Tutup koneksi database
//mysqli_close($con);
?>

