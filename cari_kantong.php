<?php

include "config/db_connect.php";

$barang = mysql_query("
    SELECT 
        noKantong,
        produk,
        kodePendonor,
        gol_darah,
        RhesusDrh,
        tgl_Aftap,
        kadaluwarsa,
        kadaluwarsa_ktg,
        lama_pengambilan,
        Status,
        hasil,
        tglperiksa,
        volume,
        nolot_ktg,
        abs
    FROM stokkantong 
    WHERE noKantong='$_GET[kode]'
");

echo '{"barang":';

while ($barang1 = mysql_fetch_assoc($barang)) {

    echo '{
        "kode":"'.$barang1['noKantong'].'",
        "nama":"'.$barang1['produk'].'",
        "pendonor":"'.$barang1['kodePendonor'].'",
        "goldarah":"'.$barang1['gol_darah'].'",
        "rhesus":"'.$barang1['RhesusDrh'].'",
        "tglaftap":"'.$barang1['tgl_Aftap'].'",
        "kadaluwarsa":"'.$barang1['kadaluwarsa'].'",
        "expirektg":"'.$barang1['kadaluwarsa_ktg'].'",
        "durasi":"'.$barang1['lama_pengambilan'].'",
        "status":"'.$barang1['Status'].'",
        "tglperiksa":"'.$barang1['tglperiksa'].'",
        "volume":"'.$barang1['volume'].'",
        "nolotktg":"'.$barang1['nolot_ktg'].'",
        "abs":"'.$barang1['abs'].'",
        "hasilimltd":"'.$barang1['hasil'].'"
    }';
}
echo '}';