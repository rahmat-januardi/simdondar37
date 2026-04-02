<?php

/**
 * Created by PhpStorm.
 * Date: 4/14/14
 * Time: 10:28 AM
 */

include("config/koneksi.php");

if (isset($_POST['ktg'])) {
    $no_kantong = mysql_real_escape_string($_POST['ktg']);

    $sql = "
        SELECT noSelang, merk, volume, jenis
        FROM stokkantong
        WHERE noKantong = '$no_kantong'
    ";
    $check_selang = mysql_query($sql);

    if (mysql_num_rows($check_selang) > 0) {
        $data = mysql_fetch_assoc($check_selang);

        // === BARU: Ambil tanggal buka kemasan dari verifikasi sebelumnya ===
        $sql_verif = "
            SELECT tanggal_buka 
            FROM verifikasi_kantong 
            WHERE no_kantong = '$no_kantong' 
            ORDER BY tanggal DESC 
            LIMIT 1
        ";
        $check_verif = mysql_query($sql_verif);
        $tanggal_buka = '';

        if (mysql_num_rows($check_verif) > 0) {
            $verif = mysql_fetch_assoc($check_verif);
            if (!empty($verif['tanggal_buka'])) {
                // Format khusus untuk <input type="datetime-local">
                $dt = new DateTime($verif['tanggal_buka']);
                $tanggal_buka = $dt->format('Y-m-d\TH:i');
            }
        }

        // Format respons: noSelang|merk|volume|jenis|tanggal_buka
        echo $data['noSelang'] . '|'
            . $data['merk'] . '|'
            . $data['volume'] . '|'
            . $data['jenis'] . '|'
            . $tanggal_buka;
    } else {
        // fallback jika kantong tidak ditemukan
        echo $_POST['ktg'] . '||||';
    }
}
