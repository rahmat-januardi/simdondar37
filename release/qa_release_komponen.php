<?php
$q = "SELECT `NoTrans` AS 'notrans',`pisah`, `aPutar` as 'putar', `abeku` as 'beku', `Produk` AS 'komponen', `tgl` as 'tglinput', `tglPengerjaan` as 'tglpembuatan',
                        case when `pisah`='0' then 'Manual' else 'Otomatis' end as metode,
                        `aPisah` as pisah, petugas as 'user', 'noKantong', `mulaiPisah`, `selesaiPisah`, `mulaiPutar`, `selesaiPutar`, `mulaiBeku`, `selesaiBeku`, `bsuhu`
                        FROM `dpengolahan` where `noKantong`='$nkt' order by id ASC";

$a = mysql_query($q);
$cekInput = mysql_fetch_assoc(mysql_query($q));

?>
<table class="list" border=1 cellpadding="5" cellspacing="5" width="100%" style="border-collapse:collapse">
    <tr style="background-color:mistyrose; font-size:16px; color:#000000;">
        <td rowspan="2">No</td>
        <td rowspan="2">ID</td>
        <td rowspan="2">No Kantong</td>
        <td rowspan="2">Tanggal <?= $cekInput['tglpembuatan'] == null ? 'Input' : 'Pengerjaan'; ?></td>
        <td rowspan="2">Total Waktu Pengolahan </td>
        <td rowspan="2">Jenis Komponen</td>
        <td rowspan="2">Metode</td>
        <td colspan="3">Pemutaran</td>
        <td colspan="3">Pemisahan</td>
        <td colspan="3">Pembekuan</td>
        <td rowspan="2">Suhu Beku</td>
        <td rowspan="2">Petugas</td>
    </tr>
    <tr style="background-color:mistyrose; font-size:16px; color:#000000;">
        <!-- Waktu Pemisahan -->
        <td>Alat</td>
        <td>Waktu Mulai</td>
        <td>Waktu Selesai</td>

        <!-- Waktu Pemutaran -->
        <td>Alat</td>
        <td>Waktu Mulai</td>
        <td>Waktu Selesai</td>

        <!-- Waktu Pembekuan -->
        <td>Alat</td>
        <td>Waktu Mulai</td>
        <td>Waktu Selesai</td>
    </tr>
    <?php
    $no = 0;
    while ($komp = mysql_fetch_assoc($a)) {
        $nmLengkap = mysql_fetch_assoc(mysql_query("SELECT nama_lengkap FROM `user` WHERE id_user = '$komp[user]'"));
        $logbookPisah = mysql_fetch_assoc(mysql_query("SELECT nama_barang FROM `logbook_h` WHERE kode = '$komp[pisah]'"));
        $logbookPutar = mysql_fetch_assoc(mysql_query("SELECT nama_barang FROM `logbook_h` WHERE kode = '$komp[putar]'"));
        $logbookBeku = mysql_fetch_assoc(mysql_query("SELECT nama_barang FROM `logbook_h` WHERE kode = '$komp[beku]'"));

        $pisah = isset($logbookPisah['nama_barang']) ? $logbookPisah['nama_barang'] : $komp['pisah'];
        $putar = isset($logbookPutar['nama_barang']) ? $logbookPutar['nama_barang'] : $komp['putar'];
        $beku = isset($logbookBeku['nama_barang']) ? $logbookBeku['nama_barang'] : $komp['beku'];

        // Untuk Menghitung Selisih Waktu Pengambilan dan Selesai Beku
        $lastChar = substr($nkt, -1); // ambil karakter terakhir
        $no_kantonga = substr_replace($nkt, 'A', -1, 1);
        $donasi = mysql_fetch_assoc(mysql_query("SELECT Tgl as tgl, jam_selesai from htransaksi where NoKantong ='$no_kantonga'"));
        $donasi1 = mysql_fetch_assoc(mysql_query("SELECT tgl_Aftap as tgl from stokkantong where noKantong ='$nkt'"));
        $tglKomponen = $komp['tglpembuatan'] == null ? substr($komp['tglinput'], 0, 10) : substr($komp['tglpembuatan'], 0, 10);
        $gbTglKomPisah = new DateTime($tglKomponen . ' ' . $komp['selesaiPisah']); // Tanggal dan jam selesai Pisah
        $gbTglKomBeku = new DateTime($tglKomponen . ' ' . $komp['selesaiBeku']); // Tanggal dan jam selesai beku
        $tglAftapSelesai = substr($donasi['tgl'], 0, 10);
        $tglAftapDT = new DateTime($tglAftapSelesai . ' ' . $donasi['jam_selesai']);
        $tglAftapDT = $donasi1['tgl'] ? new DateTime($donasi1['tgl']) : new DateTime($donasi['tgl']);
        // echo $tglAftapDT->format('Y-m-d H:i:s');

        $jenis = mysql_fetch_assoc(mysql_query("SELECT jenis FROM stokkantong where noKantong ='$nkt'"));

        $tampilkanBeku = (strtolower($lastChar) != 'a') || ($jenis['jenis'] == 1);

        // Hitung selisih
        $selisih = $tampilkanBeku
            ? $tglAftapDT->diff($gbTglKomBeku)
            : $tglAftapDT->diff($gbTglKomPisah);

        // Total jam
        $totalJam = ($selisih->days * 24) + $selisih->h;

        // Hasil akhir
        // $HasilSelisih = $totalJam . ' Jam ' . $selisih->i . ' Menit';
        $HasilSelisih = $selisih->days . ' Hari <br>' . $selisih->h . ' Jam <br>' . $selisih->i . ' Menit';

        $petugas = isset($nmLengkap['nama_lengkap']) ? $nmLengkap['nama_lengkap'] : $komp['user'];
    ?>
        <tr style="font-size:16px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
            onMouseOut="this.className='normal'">
            <? $no++; ?>
            <td align="right"><?php echo $no; ?></td>
            <td align="left"><?php echo $komp['notrans']; ?></td>
            <td><?= $nkt ?></td>
            <td><?= $tglKomponen; ?></td>
            <td><?= $HasilSelisih ?></td>
            <td><?= $komp['komponen']; ?></td>
            <td><?= $komp['metode']; ?></td>
            <td><?= $tampilkanBeku ? '-' : $putar; ?></td>
            <td><?= $tampilkanBeku ? '-' : date('H:i', strtotime($komp['mulaiPutar'])) ?></td>
            <td><?= $tampilkanBeku ? '-' : date('H:i', strtotime($komp['selesaiPutar'])) ?></td>
            <td><?= $tampilkanBeku ? '-' : $pisah; ?></td>
            <td><?= $tampilkanBeku ? '-' : date('H:i', strtotime($komp['mulaiPisah'])) ?></td>
            <td><?= $tampilkanBeku ? '-' : date('H:i', strtotime($komp['selesaiPisah'])) ?></td>
            <td><?= $tampilkanBeku ? $beku : '-'; ?></td>
            <td><?= $tampilkanBeku ? date('H:i', strtotime($komp['mulaiBeku'])) : '-' ?></td>
            <td><?= $tampilkanBeku ? date('H:i', strtotime($komp['selesaiBeku'])) : '-' ?>
            </td>
            <td><?= $tampilkanBeku ? $komp['bsuhu'] . '&deg;C' : '-'; ?></td>
            <td><?= $petugas; ?></td>
        </tr>
    <?php }
    if ($no == "0") {
    ?><tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="23" class=input align="center">TIDAK ADA DATA PENGOLAHAN DARAH</td>
        </tr>
    <?php } ?>
</table>