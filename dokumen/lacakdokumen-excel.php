<?php
include "koneksi.php";
session_start();
$tgl_awal = $_GET['tgl_awal'];
$tgl_akhir = $_GET['tgl_akhir'];
$namauser = $_SESSION['namauser'];

$lacakdokumen = mysql_query("select * from lacakdokumen where CAST(tanggal_akses as date)>='$tgl_awal' and CAST(tanggal_akses as date)<='$tgl_akhir' and nama_pengakses !='' ");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_dokumen_$tgl_awal" . "_sd_$tgl_akhir.xls");

?>

<h2>Rekap Pelacakan Dokumen dari Tanggal <?= $tgl_awal ?> sampai <?= $tgl_akhir ?></h2>
<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse">
    <tr tr style="background-color:#006400; font-size:12px; color:#FFFFFF; font-family:Verdana;"
        onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center">
        <td>No</td>
        <th>Nama Pengakses</th>
        <th>Bidang</th>
        <th>Tanggal Akses</th>
        <th>Nama Dokumen</th>
        <th>Keterangan</th>
    </tr>
    <?
    $no = 1;
    while ($dokumen = mysql_fetch_array($lacakdokumen)) {
    ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= $dokumen['nama_pengakses']; ?></td>
        <td><?= $dokumen['level_pengakses']; ?></td>
        <td><?= $dokumen['tanggal_akses']; ?></td>
        <td><?= $dokumen['nama_dokumen']; ?></td>
        <td><?= $dokumen['keterangan']; ?></td>
    </tr>
    <? } ?>
</table>