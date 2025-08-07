<?php
session_start();
require_once '../config/dbi_connect.php';
// require_once 'config/dbi_connect.php';
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


$labelJenis = array('HBsAg', 'HCV', 'HIV', 'Syphilis');
$jenisLengkap = array(0, 1, 2, 3);

// // DENGAN HASIL GZ
// function getHasilIMLTD($result, $labelJenis, $jenisLengkap)
// {
//     $pemeriksaan = array();
//     $statusLengkap = true;
//     $statusNR = true;
//     $statusGZ = false;
//     $detailR = array();
//     $tglPeriksa = '';

//     while ($r = $result->fetch_assoc()) {
//         $jenis = intval($r['jenisPeriksa']);
//         $pemeriksaan[$jenis] = $r;
//         $hasil = intval($r['Hasil']);

//         if ($hasil == 1) {
//             $statusNR = false;
//             $jenisName = $labelJenis[$jenis] ?? 'Unknown';
//             $detailR[] = "<span style='color:red;'>({$jenisName}) - (OD: {$r['OD']}, COV: {$r['COV']})</span>";
//         } elseif ($hasil == 2) {
//             $statusNR = false;
//             $statusGZ = true;
//             $jenisName = $labelJenis[$jenis] ?? 'Unknown';
//             $detailR[] = "<span style='color:orange;'>({$jenisName}) - GZ (OD: {$r['OD']}, COV: {$r['COV']})</span>";
//         }

//         $tglPeriksa = $r['tglPeriksa'];
//     }

//     if (count($pemeriksaan) == 0) {
//         return false;
//     } elseif (count($pemeriksaan) < 4) {
//         $kurang = array_diff($jenisLengkap, array_keys($pemeriksaan));
//         $kurangLabel = array_map(fn($i) => $labelJenis[$i], $kurang);
//         return 'Hasil Pemeriksaan Tdk Lengkap (Kurang: ' . implode(', ', $kurangLabel) . ')';
//     } elseif (!$statusNR) {
//         if ($statusGZ && !in_array(1, array_column($pemeriksaan, 'Hasil'))) {
//             // GZ tanpa R
//             return "GZ " . implode(', ', $detailR);
//         } else {
//             // Ada R
//             return "R " . implode(', ', $detailR);
//         }
//     } else {
//         return "NR";
//     }

// 0, 0, 0, 0 → NR
// 0, 2, 0, 0 → GZ
// 0, 1, 0, 2 → R
// 2, 2, 0, 0 → GZ
// 1, 0, 0, 0 → R
// }
// DENGAN HASIL GZ


function getHasilIMLTD($rows, $labelJenis, $jenisLengkap)
{
    $pemeriksaan = array();
    $statusNR = true;
    $detailR = array();
    $tglPeriksa = '';

    foreach ($rows as $r) {
        $jenis = intval($r['jenisPeriksa']);
        $pemeriksaan[$jenis] = $r;
        $hasil = intval($r['Hasil']);

        if ($hasil == 1 || $hasil == 2) {
            $statusNR = false;
            $jenisName = isset($labelJenis[$jenis]) ? $labelJenis[$jenis] : 'Unknown';
            $detailR[] = "<span class='warna-hasil'> - " . strtoupper($r['Metode']) . " - ({$jenisName}) - (OD: {$r['OD']}, COV: {$r['COV']})</span>";
        }

        $tglPeriksa = $r['tglPeriksa'];
    }

    if (count($pemeriksaan) == 0) {
        return 'Tdk ditemukkan data pemeriksaan';
        // return false;
    } elseif (count($pemeriksaan) < 4) {
        $kurang = array_diff($jenisLengkap, array_keys($pemeriksaan));
        $kurangLabel = array_map(function ($i) use ($labelJenis) {
            return $labelJenis[$i];
        }, $kurang);
        // return 'Hasil Pemeriksaan Tdk Lengkap (Kurang: ' . implode(', ', $kurangLabel) . ')';
        return 'Hasil pemeriksaan tdk lengkap.';
    } elseif (!$statusNR) {
        return "R " . implode(', ', $detailR);
    } else {
        $metodeList = array();
        foreach ($pemeriksaan as $p) {
            if (isset($p['Metode'])) {
                $metodeList[] = strtoupper($p['Metode']);
            }
        }
        $metodeList = array_unique($metodeList);
        return "NR - " . implode(', ', $metodeList);
    }
}


// TANPA HASIL GZ
// function getHasilIMLTD($result, $labelJenis, $jenisLengkap)
// {
//     $pemeriksaan = array();
//     $statusLengkap = true;
//     $statusNR = true;
//     // $statusGZ = false;
//     $detailR = array();
//     $tglPeriksa = '';

//     while ($r = $result->fetch_assoc()) {
//         $jenis = intval($r['jenisPeriksa']);
//         $pemeriksaan[$jenis] = $r;
//         $hasil = intval($r['Hasil']);

//         if ($hasil == 1 || $hasil == 2) {
//             $statusNR = false;
//             $jenisName = isset($labelJenis[$jenis]) ? $labelJenis[$jenis] : 'Unknown';
//             $detailR[] = "<span style='color:red;'>({$jenisName}) - (OD: {$r['OD']}, COV: {$r['COV']})</span>";
//         }

//         $tglPeriksa = $r['tglPeriksa'];
//     }

//     if (count($pemeriksaan) == 0) {
//         return false;
//     } elseif (count($pemeriksaan) < 4) {
//         $kurang = array_diff($jenisLengkap, array_keys($pemeriksaan));
//         $kurangLabel = array_map(function ($i) use ($labelJenis) {
//             return $labelJenis[$i];
//         }, $kurang);
//         return 'Hasil Pemeriksaan Tdk Lengkap (Kurang: ' . implode(', ', $kurangLabel) . ')';
//     } elseif (!$statusNR) {
//         return "R " . implode(', ', $detailR);
//     } else {
//         return "NR";
//     }
// }

$iPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : 'irawanDB';
// $iPetugas = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : 'ppgd_resti';

$query = "SELECT * FROM serahterima_detail_tmp WHERE `dst_user` = '$iPetugas' ORDER BY dst_id DESC";
$result = $dbi->query($query);
if ($result->num_rows > 0) {

    echo '<table id="tabelPengiriman" class="table table-bordered table-striped table-fixed">';
    echo '<thead>
        <tr class="text-center align-middle" style="vertical-align: middle !important;">
            <th class="text-center" rowspan="2"><input type="checkbox" id="checkAll"></th>
            <th class="text-center" rowspan="2">No.</th>
            <th class="text-center" rowspan="2">No Kantong</th>
            <th class="text-center" rowspan="2">Jns Ktg</th>
            <th class="text-center" rowspan="2">Merk</th>
            <th class="text-center" rowspan="2">ABO (RH)</th>
            <th class="text-center" rowspan="2">Produk</th>
            <th class="text-center" rowspan="2">Tgl Olah</th>
            <th class="text-center" rowspan="2">Status Kantong</th>
            <th class="text-center" rowspan="2">ED</th>
            <th class="text-center">KGD</th>
            <th class="text-center">ABS</th>
            <th class="text-center">IMLTD</th>
            <th class="text-center" rowspan="2">Aksi</th>
        </tr>
    </thead>';
    echo '<tbody>';
    $no = 1;

    while ($row = $result->fetch_assoc()) {

        $noKantong = $row['dst_nokantong'];

        if (strlen($noKantong) > 0) {
            $tanpaSatelite = substr($noKantong, 0, -1);
            $nkSaja = $tanpaSatelite . '%';
        } else {
            error_log("Nomor kantong tidak valid.");
        }

        $hasilPeriksa = 'Tdk ditemukkan data pemeriksaan';

        $query = "
                (
                SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode 
                FROM hasilelisa 
                WHERE noKantong LIKE ? AND jenisPeriksa = 0 ORDER BY id DESC LIMIT 1
                )
                UNION ALL
                (
                SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode 
                FROM hasilelisa 
                WHERE noKantong LIKE ? AND jenisPeriksa = 1 ORDER BY id DESC LIMIT 1
                )
                UNION ALL
                (
                SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode 
                FROM hasilelisa 
                WHERE noKantong LIKE ? AND jenisPeriksa = 2 ORDER BY id DESC LIMIT 1
                )
                UNION ALL
                (
                SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode 
                FROM hasilelisa 
                WHERE noKantong LIKE ? AND jenisPeriksa = 3 ORDER BY id DESC LIMIT 1
                )";

        $stmt = $dbi->prepare($query);

        $stmt->bind_param('ssss', $nkSaja, $nkSaja, $nkSaja, $nkSaja);
        $stmt->execute();
        $resultP = array();
        $stmt->store_result();
        $meta = $stmt->result_metadata();
        $fields = $meta->fetch_fields();
        $row1 = array();
        $bindParams = array();
        foreach ($fields as $field) {
            $bindParams[] = &$row1[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $bindParams);
        while ($stmt->fetch()) {
            $resultP[] = array_map(function ($value) {
                return $value;
            }, $row1);
        }
        $hasilPeriksa = getHasilIMLTD($resultP, $labelJenis, $jenisLengkap);
        $stmt->close();

        if ($hasilPeriksa === false) {
            $query2 = "SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode FROM hasilnat WHERE noKantong LIKE '$tanpaSatelite%' ORDER BY tglPeriksa DESC";
            $resultU = $dbi->query($query2);
            $hasilPeriksa = getHasilIMLTD($resultU, $labelJenis, $jenisLengkap);

            if ($hasilPeriksa === false) {
                $hasilPeriksa = 'Tdk ditemukkan data pemeriksaan';
            }
        }

        $selST = "SELECT `Status`, metoda, tglpengolahan, kadaluwarsa FROM stokkantong WHERE noKantong LIKE '$tanpaSatelite%' ORDER BY tglpengolahan DESC";
        $resultST = $dbi->query($selST);
        $rowST = $resultST->fetch_assoc();

        $selKGD = "SELECT GolDarah, Rhesus, ket, Cocok, goldarah_asal, rhesus_asal, metode FROM `dkonfirmasi` WHERE NoKantong LIKE '$tanpaSatelite%' ORDER BY tgl DESC";
        $resultKGD = $dbi->query($selKGD);
        $rowKGD = $resultKGD->fetch_assoc();

        $statusKGD = isset($rowKGD['Cocok']) && $rowKGD['Cocok'] == 0 ? 'Cocok' : 'Tidak Cocok';

        $warnaBackground = $statusKGD === 'Cocok' ? '#27548A' : '';
        $style = $warnaBackground ? "style='background-color: {$warnaBackground};'" : '';

        $isiTeks = $statusKGD === 'Cocok'
            ? "<span style='color: white;'>Cocok</span>"
            : "<span style='color:red;'><b>Tidak Cocok</b></span>";

        $selABS = "SELECT abs_metode, abs_result FROM `abs` WHERE abs_sample_id LIKE '$tanpaSatelite%' ORDER BY abs_tgl DESC";
        $resultABS = $dbi->query($selABS);
        $rowABS = $resultABS->fetch_assoc();
        $absResult = isset($rowABS['abs_result']) ? $rowABS['abs_result'] : 'tdk diketahui';

        $rowABS['abs_result'] = isset($rowABS['abs_result']) && $rowABS['abs_result'] == "Pos"
            ? "<span style='color:red;'><b>Pos</b></span>"
            : (isset($rowABS['abs_result']) && $rowABS['abs_result'] == "Neg"
                ? "<span>Neg</span>"
                : "<span>tdk diketahui</span>");

        // switch ($rowST['Status']) {
        //     case '0':
        //         $rowST['Status'] = 'Kosong';
        //         break;
        //     case '1':
        //         $rowST['Status'] = 'Karantina';
        //         break;
        //     case '2':
        //         $rowST['Status'] = 'Sehat';
        //         break;
        //     case '3':
        //         $rowST['Status'] = 'Keluar';
        //         break;
        //     default:
        //         $rowST['Status'] = 'Bukan Darah Produksi';
        //         break;
        // }

        switch ($row['dst_rh']) {
            case '+':
                $row['dst_rh'] = 'POS';
                break;
            case '-':
                $row['dst_rh'] = 'NEG';
                break;
            default:
                $row['dst_rh'] = 'Unknown Rhesus';
                break;
        }
        switch ($row['dst_jenisktg']) {
            case '1':
                $row['dst_jenisktg'] = 'SG';
                break;
            case '2':
                $row['dst_jenisktg'] = 'DB';
                break;
            case '3':
                $row['dst_jenisktg'] = 'TR';
                if ($rowST['metoda'] == 'TT') {
                    $row['dst_jenisktg'] = 'TR (TT)';
                } elseif ($rowST['metoda'] == 'TB') {
                    $row['dst_jenisktg'] = 'TR (TB)';
                } elseif ($rowST['metoda'] == 'TBF') {
                    $row['dst_jenisktg'] = 'TR (Filter)';
                } else {
                    $row['dst_jenisktg'] = 'TR';
                }
                break;
            case '4':
                $row['dst_jenisktg'] = 'QD';
                if ($rowST['metoda'] == 'TT') {
                    $row['dst_jenisktg'] = 'QD (TT)';
                } elseif ($rowST['metoda'] == 'TB') {
                    $row['dst_jenisktg'] = 'QD (TB)';
                } elseif ($rowST['metoda'] == 'TBF') {
                    $row['dst_jenisktg'] = 'QD (Filter)';
                } else {
                    $row['dst_jenisktg'] = 'QD';
                }
                break;
            case '6':
                $row['dst_jenisktg'] = 'PB';
                if ($rowST['metoda'] == 'TT') {
                    $row['dst_jenisktg'] = 'PB (TT)';
                } elseif ($rowST['metoda'] == 'TB') {
                    $row['dst_jenisktg'] = 'PB (TB)';
                } elseif ($rowST['metoda'] == 'TBF') {
                    $row['dst_jenisktg'] = 'PB (Filter)';
                } else {
                    $row['dst_jenisktg'] = 'PB';
                }
                break;
            default:
                $row['dst_jenisktg'] = 'Unknown Rhesus';
                break;
        }

        if (strpos($row['dst_jenisktg'], '(') !== false) {
            $row['dst_jenisktg'] = preg_replace('/\((.*?)\)/', '<strong>($1)</strong>', $row['dst_jenisktg']);
        }
        if (strpos($hasilPeriksa, '(') !== false) {
            $hasilPeriksa = preg_replace('/\((.*?)\)/', '<strong>($1)</strong>', $hasilPeriksa);
        }


        // $rowClass = '';
        // $rowClass = $no % 2 == 0 ? 'style="background-color:rgb(253, 227, 229);"' : 'style="background-color: #ffffff;"';
        $hasilClean = strip_tags($hasilPeriksa);
        // $rowClass = $no % 2 == 0
        //     ? 'style="background-color:rgb(253, 227, 229);"'
        //     : 'style="background-color: #ffffff;"';
        $rowClass = $no % 2 == 0
            // ? 'style="background-color: #DBFFCB"'
            ? 'style="background-color:rgb(224, 247, 252)"'
            : 'style="background-color: #ffffff;"';
        $extraClass = ''; // tambahan class

        if (strpos($hasilClean, 'R ') !== false) {
            $extraClass = 'blink-blink-mas-theo';
        }
        if (strpos($hasilClean, 'NR ') !== false) {
            $extraClass = '';
        }
        if (strpos($hasilClean, 'GZ ') !== false) {
            $extraClass = 'blink-blink-mas-theo';
        }

        echo '<tr class="text-center align-middle" ' . $rowClass . '>';
        echo '<td class="text-center align-middle"><input type="checkbox" class="checkbox-row" name="ck_kantong[]" value="' . $row['dst_id'] . '"></td>';
        echo '<td class="text-center align-middle">' . $no . '</td>';
        echo '<td class="text-center align-middle ' . $extraClass . '">' . $row['dst_nokantong'] . '</td>';
        echo '<td class="text-center align-middle">' . $row['dst_jenisktg'] . '</td>';
        echo '<td class="text-center align-middle">' . $row['dst_merk'] . '</td>';
        echo '<td class="text-center align-middle">' . $row['dst_golda'] . ' (' . $row['dst_rh'] . ')</td>';
        echo '<td class="text-center align-middle">' . $row['dst_produk'] . '</td>';
        echo '<td class="text-center align-middle">' . formatTanggal($rowST['tglpengolahan']) . '</td>';
        echo '<td class="status-kantong text-center align-middle" data-status="' . $rowST['Status'] . '"></td>';
        echo '<td class="text-center align-middle">' . formatTanggal($rowST['kadaluwarsa']) . '</td>';
        // echo '<td class="text-center align-middle">' . $rowKGD['Cocok'] . '</td>';
        // echo '<td class="text-center align-middle">' . $rowABS['abs_result'] . '</td>';
        echo "<td class='status-kgd text-center align-middle' data-kgd='{$statusKGD}' {$style}>{$isiTeks}</td>";
        // echo '<td class="status-abs text-center align-middle" data-abs="' . $rowABS['abs_result'] . '">' . $rowABS['abs_result'] . '</td>';
        echo '<td class="status-abs text-center align-middle" data-abs="' . strip_tags($rowABS['abs_result']) . '">' . $rowABS['abs_result'] . '</td>';
        echo '<td class="text-center align-middle ' . $extraClass . '">' . $hasilPeriksa . '</td>';

        // echo '<td class="text-center align-middle"><a href="#" class="kantongDetail" data-id="' . $row['csd_id'] . '">' . $row['csd_noTrans'] . '</a></td>';
        // echo '<td class="text-center align-middle"><a href="#" class="pendonorDetail" data-id="' . $row['csd_kodeDonor'] . '">' . $row['csd_kodeDonor'] . '</a></td>';

        echo "<td><button class='btn btn-danger btn-xs hapus-satu' data-kantong='{$row['dst_id']}'>Hapus</button></td>";
        echo '</tr>';
        $no++;
    }
    echo '</tbody></table>';
    // echo '<button id="hapusTerpilih" class="btn btn-danger btn-sm">Hapus yang Dipilih</button>';
} else {
    echo '<div class="alert alert-warning">Belum ada data.</div>';
}

