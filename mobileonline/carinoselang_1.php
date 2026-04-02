<?php

/**
 * Created by PhpStorm.
 * Date: 4/14/14
 * Time: 10:28 AM
 */

include("adm/config.php");

if (isset($_POST['ktg'])) {
    $no_selang = mysqli_real_escape_string($con, $_POST['ktg']);

    $sql = "
        SELECT noSelang, merk, volume, jenis
        FROM stokkantong
        WHERE noKantong = '$no_selang'
    ";
    $check_selang = mysqli_query($con, $sql);

    if (mysqli_num_rows($check_selang) > 0) {
        $data = mysqli_fetch_assoc($check_selang);

        // === BARU: Ambil tanggal buka kemasan dari verifikasi sebelumnya ===
        $sql_verif = "
            SELECT tanggal_buka 
            FROM verifikasi_kantong 
            WHERE no_kantong = '$no_selang' 
            ORDER BY tanggal DESC 
            LIMIT 1
        ";
        $check_verif = mysqli_query($con, $sql_verif);
        $tanggal_buka = '';

        if (mysqli_num_rows($check_verif) > 0) {
            $verif = mysqli_fetch_assoc($check_verif);
            if (!empty($verif['tanggal_buka'])) {
                // Format khusus untuk <input type="datetime-local">
                $dt = new DateTime($verif['tanggal_buka']);
                $tanggal_buka = $dt->format('Y-m-d\TH:i');
            }
        }

        // kalau mau echo satu-satu
        echo $data['noSelang'] . '|'
            . $data['merk'] . '|'
            . $data['volume'] . '|'
            . $data['jenis'] . '|'
            . $tanggal_buka;
    } else {
        // jika tidak ditemukan, fallback dari input
        echo $_POST['ktg'] . '||||';
    }
}

//echo $test;