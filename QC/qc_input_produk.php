<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser = $_SESSION['namauser'];

if (isset($_POST['submit'])) {
    $nokantong = $_POST['nokantong'];
    $sql = mysql_fetch_assoc(mysql_query("SELECT * FROM registrasi_qc WHERE nokantong='$nokantong'"));
    if (!$sql) {
        echo "<script>alert('Data tidak ditemukan. Pastikan nomor kantong benar.'); window.location.href='pmiqc.php?module=input_qc';</script>";
        exit;
    } elseif ($sql['up_data'] == '1') {
        echo "<script>alert('Data sedang proses QC.'); window.location.href='pmiqc.php?module=input_qc';</script>";
        exit;
    } elseif ($sql['up_data'] == '2') {
        echo "<script>alert('Data sudah selesai QC.'); window.location.href='pmiqc.php?module=input_qc';</script>";
        exit;
    } else {
        // Lanjutkan ke proses QC
        header("Location: pmiqc.php?module=qc_produk_proses&nokantong=$nokantong");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Input Produk</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <style>
    body {
        background: #f4f7fb;
    }
    </style>
</head>

<body OnLoad="document.input_qc.nokantong.focus();">
    <div class="card m-3">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Sample Panel</h4>
            <a href="pmiqc.php?module=input_qc" class="btn btn-primary">Kembali</a>
        </div>

        <div class="card-body">
            <form name="input_qc" method=post>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nokantong" class="form-label">Input Nomor Kantong</label>
                        <input type="text" class="form-control" name="nokantong" style="text-transform:uppercase"
                            minlength="5" required="">
                    </div>
                    <div class="col-md-3" style="padding-top:30px;">
                        <button class="btn btn-success" type="submit" value="submit" name="submit">Proses</button>
                    </div>
                </div>
            </form>
        </div>
</body>

</html>