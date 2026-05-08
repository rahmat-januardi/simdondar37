<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />

<style>
    #serahterima {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 16px;
        border-collapse: collapse;
    }

    #serahterima td,
    #serahterima th {
        border: 1px solid #ddd;
        padding: 5px;
    }

    #serahterima tr:nth-child(even) {
        background-color: #ffe6e6;
    }

    #serahterima tr:hover {
        background-color: #ddd;
    }

    #serahterima th {
        padding-top: 3px;
        padding-bottom: 3px;
        text-align: left;
        font-weight: lighter;
        background-color: #ff9999;
        color: #000000;
    }

    #serahterima input {
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: left;
        background-color: lightyellow;
        color: #000000;
    }
</style>

<?php
include('config/dbi_connect.php');
$namauser       = $_SESSION['namauser'];
$namalengkap    = $_SESSION['nama_lengkap'];
$level            = $_SESSION['leveluser'];
$tglawal        = date("Y-m-01");
$hariini        = date("Y-m-d");
$notransaksi    = $_GET['no'];
?>

<body>
    <a name="atas" id="atas"></a>
    <div
        style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">
        DAFTAR TRANSAKSI PEMUSNAHAN PRODUK DARAH</div>

    <br>
    <?php

    if (isset($_POST['submit2'])) {
        $sekarang   = date("Y-m-d H:i:s");
        $instansi   = $_POST['instansi'];
        $ptg_limbah = $_POST['ptg_penerima'];
        $berat  = $_POST['berat'];

        // Upload Berita Acara============================
        $nama_file_ba = null;

        if (isset($_FILES['upload_berita_acara']) && $_FILES['upload_berita_acara']['error'] == 0) {

            $file_name = $_FILES['upload_berita_acara']['name'];
            $file_tmp  = $_FILES['upload_berita_acara']['tmp_name'];
            $file_size = $_FILES['upload_berita_acara']['size'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validasi ekstensi
            $allowed_ext = array('jpg', 'jpeg', 'png', 'pdf');
            if (!in_array($file_ext, $allowed_ext)) {
                $message = "Format Berita Acara tidak valid (jpg, jpeg, png, pdf)";
                goto end_submit;
            }

            // Validasi ukuran 5MB
            if ($file_size > 5 * 1024 * 1024) {
                $message = "Ukuran Berita Acara maksimal 5MB";
                goto end_submit;
            }

            // Folder upload
            $upload_dir = __DIR__ . "/file_berita_acara/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Rename file
            $nama_file_ba = "BA_" . $notransaksi . "_" . time() . "." . $file_ext;
            $upload_path = $upload_dir . $nama_file_ba;

            // Upload
            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $message = "Gagal upload Berita Acara";
                goto end_submit;
            }
        }

        if ($nama_file_ba == null) {
            $message = "Wajib Upload Berita Acara";
            goto end_submit;
        }

        // Upload Lampiran Pengelola Limbah (Optional)============================
        $nama_file_limbah = NULL;
        if (isset($_FILES['upload_pengelola_limbah']) && $_FILES['upload_pengelola_limbah']['error'] == 0) {

            $file_name_limbah = $_FILES['upload_pengelola_limbah']['name'];
            $file_tmp_limbah  = $_FILES['upload_pengelola_limbah']['tmp_name'];
            $file_size_limbah = $_FILES['upload_pengelola_limbah']['size'];
            $file_ext_limbah  = strtolower(pathinfo($file_name_limbah, PATHINFO_EXTENSION));

            // Validasi ekstensi
            $allowed_ext = array('jpg', 'jpeg', 'png', 'pdf');
            if (!in_array($file_ext_limbah, $allowed_ext)) {
                $message = "Format Lampiran Pengelola Limbah tidak valid (jpg, jpeg, png, pdf)";
            }

            // Validasi ukuran 5MB
            if ($file_size_limbah > 5 * 1024 * 1024) {
                $message = "Ukuran Lampiran Pengelola Limbah maksimal 5MB";
            }

            // Folder upload
            $upload_dir_limbah = __DIR__ . "/file_pengelola_limbah/";
            if (!is_dir($upload_dir_limbah)) {
                mkdir($upload_dir_limbah, 0777, true);
            }

            // Rename file
            $nama_file_limbah = "LIMBAH_" . $trans . "_" . time() . "." . $file_ext_limbah;
            $upload_path_limbah = $upload_dir_limbah . $nama_file_limbah;

            // Upload
            if (!move_uploaded_file($file_tmp_limbah, $upload_path_limbah)) {
                $message = "Gagal upload Lampiran Pengelola Limbah";
            }
        } else {
            $nama_file_limbah = NULL; // Tidak ada file yang diupload
        }

        $sa = "UPDATE `ar_stokkantong_trans`set `ptgs_kirim` ='$namalengkap',`pengelola`='$instansi',`ptgs_limbah`='$ptg_limbah', `diambil`='$sekarang', `berat`='$berat', `stat`='1', `file_berita_acara`='$nama_file_ba', `file_pengelola_limbah`='$nama_file_limbah' where notrans='$notransaksi'";
        //echo "$sa<br>";
        $a  = mysqli_query($dbi, $sa);

        //=======Audit Trial====================================================================================
        $log_mdl = $level;
        $log_aksi = 'Serah Terima Limbah Produk Darah  No. transaksi: ' . $notransaksi . ' Instansi Pengelola : ' . $instansi;
        include "user_log.php";
        //=====================================================================================================

        echo "TRANSAKSI SERAH SETRIMA LIMBAH KE  SUKSES ";

        echo "<meta http-equiv='refresh' content='2;url=pmi$level.php?module=musnahlist'";

        end_submit: // <-- hanya dieksekusi jika ada error

        if (isset($message) && $message != '') {
            echo "<script>alert('" . addslashes($message) . "');</script>";
            // tetap di halaman upload
            echo "<meta http-equiv='refresh' content='0'>";
            exit;
        }
    }


    $sql_h  = "SELECT *,  DATE_FORMAT(`tgl`, '%d %M %Y %H:%i') as `tglmusnah` from ar_stokkantong_trans  WHERE `notrans`='$notransaksi'";
    $sql_h1 = mysqli_fetch_assoc(mysqli_query($dbi, $sql_h));

    ?>


    <form name="cari" method="POST" action="<? echo $PHPSELF ?>" enctype="multipart/form-data">
        <table
            style="width: 100%; border-collapse: collapse;border: 2px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr>
                <td style="vertical-align: top; width=100%;">

                    <table id="serahterima" style="width: 98%;">
                        <tr>
                            <th>Nomor Transaksi</th>
                            <td><input type="hidden" name="trans" value=<?php echo $notransaksi; ?>
                                    readonly><?php echo $notransaksi; ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal pemusnahan
                </td>
                <td><?php echo $sql_h1['tglmusnah']; ?></td>
            </tr>
            <tr>
                <th>Asal Pemusnahan</th>
                <td><?php echo $sql_h1['bagian']; ?></td>
            </tr>
            <tr>
                <th>Petugas Pemusnahan</th>
                <td><?php echo $sql_h1['ptgs_musnah']; ?></td>
            </tr>
            <tr>
                <th>Lampiran Berita Acara<label style="color:red;">*</label></th>
                <td><input type="file" name="upload_berita_acara" accept=".jpg, .jpeg, .png, .pdf" /><br>
                    <label>Format File: .PDF, .JPEG, .JPG, .PNG</label><br />
                    <label>Max File Size 5MB</label>
                </td>
            </tr>
            <tr>
                <th>Berat (Kg)</th>
                <td><input name="berat" type="text" required></td>
            </tr>
            <tr>
                <th>Instansi Pengelola Limbah</th>
                <td>
                    <select name="instansi" id="ptg_menyerahkan" required>
                        <option value="">Pilih Instansi</option>
                        <?
                        $usr    = mysqli_query($dbi, "select * from supplier where jenis='4' order by Nama Asc");
                        while ($usr1    = mysqli_fetch_assoc($usr)) {
                        ?>
                            <option value="<?= $usr1[Kode] ?>"><?= $usr1['Nama'] ?>
                            <?
                        }
                            ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Petugas Instansi Pengelola Limbah</th>
                <td><input name="ptg_penerima" type="text" required></td>
            </tr>
            <tr>
                <th>Lampiran dari Instansi Pengelola Limbah</th>
                <td><input type="file" name="upload_pengelola_limbah" accept=".jpg, .jpeg, .png, .pdf" /><br>
                    <label>Format File: .PDF, .JPEG, .JPG, .PNG</label><br />
                    <label>Max File Size 5MB</label>
                </td>
            </tr>
        </table>

        </td>
        </tr>
        </table>
        <p>
            <input type="submit" name="submit2" value="Simpan Transaksi Pemusnahan"
                onclick="return confirm('PERHATIAN \n \nSimpan transaksi pemusnahan darah ini?');"
                class="swn_button_green">
            <a href="pmi<?php echo $level; ?>.php?module=musnahlist"><input type="button" class="swn_button_blue"
                    value="Kembali"></a>
    </form>


    <br>
    <div style="font-size:10px; color:#000000; font-family: " Helvetica Neue", Helvetica, Arial, sans-serif;">Build :
        16-04-2026</div>