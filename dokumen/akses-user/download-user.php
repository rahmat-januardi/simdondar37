<?php
session_start();
include "../koneksi.php";

$namauser = $_SESSION['nama_lengkap'];
$today = date('YmdHis');
$notrans = $today . "-" . $namauser;
$level = $_SESSION['bagian'];

if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];
    $back_dir = "../upload/";
    $file = $back_dir . $_GET['filename'];
    $nurdin = $file;
}

$tambah = mysql_query("insert into lacakdokumen (notrans, nama_pengakses, level_pengakses, tanggal_akses, nama_dokumen, keterangan) values ('$notrans', '$namauser', '$level', '$today', '$filename', '$namauser melakukan aktivitas PREVIEW, dokumen: $filename')");
?>

<!DOCTYPE html>
<html>

<head>
    <style>
    /* CSS untuk membuat embed full screen */
    html,
    body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
        /* Menghilangkan scrollbar */
    }

    .container {
        width: 100%;
        height: 100vh;
        /* Menggunakan tinggi viewport penuh */
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .box-body {
        flex: 1;
        /* Mengisi ruang yang tersedia */
        display: flex;
        flex-direction: column;
    }

    .pdf-container {
        flex: 1;
        /* Mengisi ruang yang tersedia */
        width: 100%;
        height: 100%;
    }

    embed {
        width: 100%;
        height: 100%;
        /* Mengisi tinggi container */
        border: none;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="box-body">
            <div class="pdf-container">
                <embed src="<?php echo $nurdin; ?>#toolbar=0" ondragstart="return false" quality="high" name="fb"
                    allowScriptAccess="always" allowFullScreen="true" pluginpage="http://www.adobe.com/go/getreader"
                    type="application/pdf">
            </div>
        </div>
    </div>
</body>

</html>