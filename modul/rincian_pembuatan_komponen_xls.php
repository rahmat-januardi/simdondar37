<?
header("Content-Type: application/vnd.ms-excel");
header("Pragma: no-cache");
header("Expires: 0");
include('../config/db_connect.php');
$tgl_awal = isset($_POST['tgl_awal']) ? $_POST['tgl_awal'] : date('Y-m-d');
$tgl_akhir = isset($_POST['tgl_akhir']) ? $_POST['tgl_akhir'] : date('Y-m-d');
$shift = !empty($_POST['shift2']) ? "AND shift='" . $_POST['shift2'] . "'" : "";
$komponen = !empty($_POST['produk2']) ? "AND Produk='" . $_POST['komponen2'] . "'" : "";
$petugas = !empty($_POST['petugas2']) ? "AND petugas LIKE '%" . $_POST['petugas2'] . "%'" : "";

$pertgl = substr($tgl_awal, 8, 2);
$perbln = substr($tgl_awal, 5, 2);
$perthn = substr($tgl_awal, 0, 4);

$pertgl1 = substr($tgl_akhir, 8, 2);
$perbln1 = substr($tgl_akhir, 5, 2);
$perthn1 = substr($tgl_akhir, 0, 4);

header("Content-Disposition: attachment; filename=Laporan_rincian_Pembuatan_Komponen $pertgl-$perbln-$perthn sampai $pertgl1-$perbln1-$perthn1.xls");

$query = "SELECT * FROM dpengolahan WHERE DATE(tgl) BETWEEN '$tgl_awal' AND '$tgl_akhir' $shift $komponen $petugas ORDER BY tgl ASC";
$result = mysql_query($query);
?>



<h5 colspan='9' class="table">Rincian Pembuatan Komponen Dari Tanggal : <?= $pertgl ?> - <?= $perbln ?> - <?= $perthn ?>
    sampai <?= $pertgl1 ?> - <?= $perbln1 ?> - <?= $perthn1 ?>
    <br>
</h5>

<table border=1>
    <thead>
        <tr>
            <th rowspan="2" class="text-center align-middle">No</th>
            <th colspan="11" class="text-center align-middle">Data Pengolahan</th>
            <th colspan="3" class="text-center align-middle">Metode</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>No. Kantong</th>
            <th>Jenis</th>
            <th>ABO (Rh)</th>
            <th>Komponen</th>
            <th>Tgl Aftap</th>
            <th>Tgl Kedaluwarsa</th>
            <th>Tgl Periksa</th>
            <th>Status</th>
            <th>Petugas</th>
            <th>Shift</th>
            <th>Mesin</th>
            <th>No. Seri</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        while ($row = mysql_fetch_assoc($result)) {
            $selSK = "SELECT `Status`, `jenis`, `tgl_Aftap`, `kadaluwarsa`, `tglPeriksa` FROM stokkantong WHERE `NoKantong` = '" . $row['noKantong'] . "'";
            $queSK = mysql_query($selSK);
            $datSK = mysql_fetch_assoc($queSK);

            switch ($datSK['jenis']) {
                case '1':
                    $jenis = 'Single';
                    break;
                case '2':
                    $jenis = 'Double';
                    break;
                case '3':
                    $jenis = 'Triple';
                    break;
                case '4':
                    $jenis = 'Quadruple';
                    break;
                case '6':
                    $jenis = 'Pediatrik';
                    break;
                default:
                    $jenis = 'Tidak Diketahui';
                    break;
            }

            switch ($datSK['Status']) {
                case '0':
                    $statSK = 'Kosong/diLogistik';
                    break;
                case '1':
                    $statSK = 'Baru Isi/Karantina';
                    break;
                case '2':
                    $statSK = 'Sehat';
                    break;
                case '3':
                    $statSK = 'Keluar';
                    break;
                case '4':
                    $statSK = 'Rusak';
                    break;
                case '5':
                    $statSK = 'Rusak-Gagal';
                    break;
                case '6':
                    $statSK = 'Musnah';
                    break;
                case '7':
                    $statSK = 'Reaktif';
                    break;
                default:
                    $statSK = 'Tidak Diketahui';
                    break;
            }
            echo "<tr class='text-center'>
                        <td>{$no}</td>
                        <td>{$row['tgl']}</td>
                        <td>{$row['noKantong']}</td>
                        <td>{$jenis}</td>
                        <td>$row[goldarah] ($row[rhesus])</td>
                        <td>{$row['Produk']}</td>
                        <td>{$datSK['tgl_Aftap']}</td>
                        <td>{$datSK['kadaluwarsa']}</td>
                        <td>{$datSK['tglPeriksa']}</td>
                        <td>{$statSK}</td>
                        <td>{$row['petugas']}</td>
                        <td>{$row['shift']}</td>
                        <td>{$row['aPutar']}</td>
                        <td>{$row['aPisah']}</td>
                        <td>{$row['metode']}</td>
                </tr>";
            $no++;
        }
        ?>
    </tbody>


    <?
    mysql_close();
    ?>