<?php
include_once '../config/dbi_connect.php';

function formatTanggal($inputTanggal)
{
    $bulan = array(
        1 => 'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
        'Okt',
        'Nov',
        'Des'
    );

    // Cek apakah input valid
    if (!$inputTanggal || !strtotime($inputTanggal)) {
        return "Tanggal tidak valid";
    }

    // Cek apakah formatnya hanya tanggal (YYYY-mm-dd) atau tanggal lengkap (YYYY-mm-dd 00:00:00)
    $isTimeIncluded = strpos($inputTanggal, '00:00:00') !== false;

    // Hanya tampilkan tanggal jika tidak ada waktu atau waktu adalah 00:00:00
    $split = explode('-', $inputTanggal);
    if (count($split) < 3) {
        return "Format tanggal tidak valid";
    }

    $dateTime = new DateTime($inputTanggal);

    // Jika waktu ada (00:00:00), format hanya tanggalnya saja
    if ($isTimeIncluded) {
        return $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y");
    }

    // Format lengkap jika waktu tidak 00:00:00
    // $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i") . " WIB";
    $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i");

    return $formattedDate;
}

$tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tanggal_sampai = isset($_POST['tanggal_sampai']) ? $_POST['tanggal_sampai'] : '';
$unit = isset($_POST['unit']) ? $_POST['unit'] : '';

$data = array();
$selData = "SELECT 
                MIN(hst_id) as hst_id, 
                hst_notrans, 
                MIN(hst_tgl) as hst_tgl, 
                MIN(hst_user) as hst_user, 
                MIN(hst_suhuterima) as hst_suhuterima, 
                MIN(hst_asal) as hst_asal, 
                MIN(hst_kondisiumum) as hst_kondisiumum, 
                MIN(hst_bagpengirim) as hst_bagpengirim,
                MIN(hst_kode_alat) as hst_kodealat
            FROM serahterima
            WHERE DATE(hst_tgl) BETWEEN '$tanggal_mulai' AND '$tanggal_sampai'
            GROUP BY hst_notrans";

$resultData = $dbi->query($selData);
if (!$resultData) {
    echo json_encode(array('error' => 'Gagal mengambil data' . $dbi->error));
    exit;
}
$no = 1;
while ($rowData = $resultData->fetch_assoc()) {

    $jumDataKirim = "SELECT COUNT(*) as jumData FROM serahterima_detail WHERE dst_notrans = '" . $rowData['hst_notrans'] . "'";
    $resultJumDataKirim = $dbi->query($jumDataKirim);
    if (!$resultJumDataKirim) {
        echo json_encode(array('error' => 'Gagal mengambil data' . $dbi->error));
        exit;
    }
    $rowJumDataKirim = $resultJumDataKirim->fetch_assoc();

    $jumDataTerima = "SELECT COUNT(*) as jumData FROM serahterima_detail WHERE dst_notrans = '" . $rowData['hst_notrans'] . "' AND dst_sah = 1";
    $resultJumDataTerima = $dbi->query($jumDataTerima);
    if (!$resultJumDataTerima) {
        echo json_encode(array('error' => 'Gagal mengambil data' . $dbi->error));
        exit;
    }

    $rowJumDataTerima = $resultJumDataTerima->fetch_assoc();

    $rowData['jumDataKirim'] = $rowJumDataKirim['jumData'];
    $rowData['jumDataTerima'] = $rowJumDataTerima['jumData'];
    $rowData['hst_tgl'] = formatTanggal(date('d-m-Y H:i', strtotime($rowData['hst_tgl'])));
    $rowData['hst_user'] = $rowData['hst_user'] ?: '-';
    $rowData['hst_asal'] = $rowData['hst_asal'] ?: '-';
    $rowData['hst_bagpengirim'] = $rowData['hst_bagpengirim'] ?: '-';
    $rowData['jumDataKirim'] = $rowData['jumDataKirim'] ?: '-';
    $rowData['jumDataTerima'] = $rowData['jumDataTerima'] ?: '-';
    $rowData['hst_notrans'] = $rowData['hst_notrans'] ?: '-';
    $data[] = array(
        $rowData['hst_notrans'],
        $rowData['hst_tgl'],
        $rowData['hst_user'],
        $rowData['hst_suhuterima'],
        $rowData['hst_asal'],
        $rowData['hst_kondisiumum'],
        $rowData['hst_kodealat'],
        $rowData['jumDataKirim'],
        $rowData['jumDataTerima'],
    );
}

// $data = [
//     ['2025-04-25', 'Andi', 'Budi', 20, 'Unit 1'],
//     ['2025-04-26', 'Citra', 'Dina', 15, 'Unit 2'],
//     ['2025-04-27', 'Elly', 'Fahmi', 25, 'Unit 1'],
// ];

// Filter manual kalau pilih unit
if ($unit != '') {
    $data = array_filter($data, function ($row) use ($unit) {
        return $row[4] == $unit;
    });
}

// Outputkan tabel
echo '<table id="tabelRekap" class="display nowrap text-center" style="width:100%">';
echo '<thead>
        <tr>
        <th rowspan="2">No</th>
        <th rowspan="2">No. Transaksi</th>
        <th rowspan="2">Tanggal</th>
        <th rowspan="2">Petugas</th>
        <th rowspan="2">Suhu</th>
        <th rowspan="2">Asal</th>
        <th rowspan="2">Kondisi Umum</th>
        <th rowspan="2">Kode Alat</th>
        <th colspan="2">Jumlah</th>
        </tr>
        <tr>
        <th>Telah Proses</th>
        <th>dari Data</th>
        </tr>
    </thead><tbody>';

$no = 1;
foreach ($data as $row) {
    $rowClass = $no % 2 == 0
            ? 'style="background-color:rgb(224, 247, 252)"'
            : 'style="background-color: #ffffff;"';
    echo "<tr class='text-center align-middle' {$rowClass}>";
    echo "<td>{$no}</td>";
    echo "<td>
            <a href='modul/st_komponen_rilis/detailSTKomponen.php?nT={$row[0]}' title='Proses' class='link-text'>{$row[0]}</a>
        </td>";

    echo "
            <td>{$row[1]}</td>
            <td>{$row[2]}</td>
            <td>{$row[3]}</td>
            <td>{$row[4]}</td>
            <td>{$row[5]}</td>
            <td>{$row[6]}</td>
            <td>{$row[8]} kantong</td>
            <td>{$row[7]} kantong</td>
        </tr>";
    $no++;
}

echo '</tbody></table>';
