<?php
require('fpdf186/fpdf.php');
require_once('config/dbi_connect.php');
// $nT = isset($_GET['nT']) ? $_GET['nT'] : 'KR240425-317D-0005';
$nT = isset($_GET['nT']) ? $_GET['nT'] : 'KR240425-317D-0007';
// $nT = isset($_GET['nT']) ? $_GET['nT'] : 'ST200824-0005';
// $nT = isset($_GET['nT']) ? $_GET['nT'] : 'ST210224-0003';
$iPetugas = isset($_GET['namauser']) ? $_GET['namauser'] : 'irawanDB';

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

    if (!$inputTanggal || !strtotime($inputTanggal)) {
        return "Tanggal tidak valid";
    }

    $isTimeIncluded = strpos($inputTanggal, '00:00:00') !== false;

    $split = explode('-', $inputTanggal);
    if (count($split) < 3) {
        return "Format tanggal tidak valid";
    }

    $dateTime = new DateTime($inputTanggal);

    if ($isTimeIncluded) {
        return $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y");
    }

    // $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i") . " WIB";
    $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i");

    return $formattedDate;
}

$labelJenis = array('HBsAg', 'HCV', 'HIV', 'Syphilis');
$jenisLengkap = array(0, 1, 2, 3);
function getHasilIMLTD($result, $labelJenis, $jenisLengkap)
{
    $pemeriksaan = array();
    $statusLengkap = true;
    $statusNR = true;
    // $statusGZ = false;
    $detailR = array();
    $tglPeriksa = '';

    while ($r = $result->fetch_assoc()) {
        $jenis = intval($r['jenisPeriksa']);
        $pemeriksaan[$jenis] = $r;
        $hasil = intval($r['Hasil']);

        if ($hasil == 1) {
            $statusNR = false;
            $jenisName = isset($labelJenis[$jenis]) ? $labelJenis[$jenis] : 'Unknown';
            $detailR[] = "({$jenisName})";
        } elseif ($hasil == 2) {
            $statusNR = false;
            // $statusGZ = true;
            $jenisName = $labelJenis[$jenis] ? $labelJenis[$jenis] : 'Unknown';
            $detailR[] = "({$jenisName})";
        }

        $tglPeriksa = $r['tglPeriksa'];
    }

    if (count($pemeriksaan) == 0) {
        return false;
    } elseif (count($pemeriksaan) < 4) {
        $kurang = array_diff($jenisLengkap, array_keys($pemeriksaan));
        //$kurangLabel = array_map(fn($i) => $labelJenis[$i], $kurang);
        $kurangLabel = array_map(function ($i) use ($labelJenis) {
            return $labelJenis[$i];
        }, $kurang);
        return 'Hasil Pemeriksaan Tdk Lengkap (Kurang: ' . implode(', ', $kurangLabel) . ')';
    } elseif (!$statusNR) {
        // if ($statusGZ && !in_array(1, array_column($pemeriksaan, 'Hasil'))) {
        //     // GZ tanpa R
        //     return "GZ " . implode(', ', $detailR);
        // } else {
        //     // Ada R
        //     return "R " . implode(', ', $detailR);
        // }
        
            // Ada R
            return "R " . implode(', ', $detailR);
        // }
    } else {
        return "NR";
    }

    // 0, 0, 0, 0 → NR
// 0, 2, 0, 0 → GZ
// 0, 1, 0, 2 → R
// 2, 2, 0, 0 → GZ
// 1, 0, 0, 0 → R
}
class PDF extends FPDF
{
    // Header halaman
    function Header()
    {
        // Background Header
        // $this->SetFillColor(145, 34, 0); // Warna #912200
        // $this->Rect(0, 0, 297, 20, 'F'); // Untuk landscape A4 width 297mm
        // $this->SetTextColor(255, 255, 255); // Warna teks putih
        global $dbi;
        global $nT;

        $sql = "SELECT hst_id, hst_notrans, hst_bagpengirim, hst_bagpenerima, hst_tgl, hst_asal, hst_jenis_st, hst_user, 
        hst_pengirim, hst_kode_alat, hst_kondisiumum, hst_peruntukan, hst_shift_pengirim
        FROM serahterima WHERE hst_notrans = '$nT'";

        $result = mysqli_query($dbi, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $bagPengirim = isset($row['hst_bagpengirim']) ? $row['hst_bagpengirim'] : '-';
            $bagPenerima = isset($row['hst_bagpenerima']) ? $row['hst_bagpenerima'] : '-';
            $tglKirim = formatTanggal($row['hst_tgl']);
            $hstAsal = isset($row['hst_asal']) ? $row['hst_asal'] : '-';
            $jenisKirim = isset($row['hst_jenis_st']) ? $row['hst_jenis_st'] : '-';
            $hstUser = isset($row['hst_user']) ? $row['hst_user'] : '-';
            $pengirim = isset($row['hst_pengirim']) ? $row['hst_pengirim'] : '-';
            $kodeAlat = isset($row['hst_kode_alat']) ? $row['hst_kode_alat'] : '-';
            $kondisiUmum = isset($row['hst_kondisiumum']) ? $row['hst_kondisiumum'] : '-';
            $peruntukan = isset($row['hst_peruntukan']) ? $row['hst_peruntukan'] : '-';
            $shiftPengirim = isset($row['hst_shift_pengirim']) ? $row['hst_shift_pengirim'] : '-';
        } else {
            echo "Error: " . mysqli_error($dbi);
        }

        $this->SetFont('Arial', '', 10);

        $this->Cell(0, 5, 'NAMA UDD', 0, 0, 'L');
        $this->Cell(0, 5, 'No. Dokumen: UDDSLM-PD-L4-011-2025', 0, 1, 'R');

        $this->Cell(0, 5, 'Formulir Serah Terima Komponen Darah', 0, 0, 'L');
        $this->Cell(0, 5, 'Versi: 002.', 0, 1, 'R');
        $this->Ln(1);

        $x1 = 10;
        $x2 = 287;
        $y1 = $this->GetY();
        $y2 = $y1 + 10;
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.5); // ketebalan garis
        $this->Line($x1, $y1, $x2, $y1);
        $this->Ln(2);

        $this->SetTextColor(0, 0, 0); // Warna teks putih
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, 'FORM PENGIRIMAN KOMPONEN DARAH - RILIS', 0, 1, 'C');
        $this->Ln(4);

        if ($this->PageNo() == 1) {

            // ========== BOX INFORMASI PENGIRIMAN ==========
            $this->SetFont('Arial', 'B', 11);
            $this->SetTextColor(0, 0, 0);
            $this->SetFillColor(230, 230, 230);
            $this->SetLineWidth(0.2); // ketebalan garis
            $this->Cell(0, 7, 'Informasi Pengiriman', 1, 1, 'L', true);
            $this->Ln(2);

            // Box
            $this->SetFont('Arial', '', 9);
            $this->SetFillColor(245, 245, 245); // Abu abu muda

            // Data Dummy Informasi
            $info1 = array(
                array('Tangal', $tglKirim),
                array('No. Transaksi', $nT),
                array('Bagian yang Mengirimkan', $bagPengirim),
                array('Peruntukan', $peruntukan),
                array('Jenis Serah Terima', $jenisKirim),
            );
            $info2 = array(
                array('Bagian yang Menerima', $bagPenerima),
                array('Kondisi Saat Pengiriman', $kondisiUmum),
                array('Kode Alat Pengiriman', $kodeAlat),
                array('Asal Pengiriman', $hstAsal),
                array('Petugas Pengiriman', $pengirim . ' (' . $shiftPengirim . ')'),
            );

            $w1 = 60;
            $w2 = 80;
            $w3 = 60;
            $w4 = 77;

            // Hitung jumlah baris terbanyak supaya tidak error
            $rows = max(count($info1), count($info2));

            // Tulis per baris
            for ($i = 0; $i < $rows; $i++) {
                if (isset($info1[$i])) {
                    $this->Cell($w1, 7, $info1[$i][0], 1, 0, 'L', true);
                    $this->Cell($w2, 7, $info1[$i][1], 1, 0, 'L');
                } else {
                    // Jika info1 kurang dari info2, buat kosong
                    $this->Cell($w1, 7, '', 1, 0, 'L', true);
                    $this->Cell($w2, 7, '', 1, 0, 'L');
                }

                if (isset($info2[$i])) {
                    $this->Cell($w3, 7, $info2[$i][0], 1, 0, 'L', true);
                    $this->Cell($w4, 7, $info2[$i][1], 1, 1, 'L');
                } else {
                    // Jika info2 kurang dari info1, buat kosong
                    $this->Cell($w3, 7, '', 1, 0, 'L', true);
                    $this->Cell($w4, 7, '', 1, 1, 'L');
                }
            }
            $this->Ln(25);
        }

        if ($this->PageNo() == 1) {
            $this->SetY(85);
        }

        $this->SetFillColor(200, 200, 200);
        $this->SetFont('Arial', 'B', 8);
        $header = array('No.', 'No Kantong', 'Jns Ktg', 'Merk', 'ABO (RH)', 'Produk', 'Tgl Olah', 'Status Kantong', 'ED', 'KGD', 'ABS', 'IMLTD');
        $w = array(8, 32, 17, 20, 20, 24, 34, 25, 34, 15, 19, 29);

        foreach ($header as $key => $col) {
            $this->Cell($w[$key], 7, $col, 1, 0, 'C', true);
        }
        $this->Ln();

    }

    // Footer halaman
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Dicetak pada: ' . formatTanggal(date('d-m-Y H:i')) . '. Oleh: ', 0, 0, 'L');
    }

    // function TableHeader()
    // {
    //     $this->SetFillColor(200, 200, 200);
    //     $this->SetFont('Arial', 'B', 8);
    //     $header = ['No.', 'No Kantong', 'Jns Ktg', 'Merk', 'ABO (RH)', 'Produk', 'Tgl Olah', 'Status Kantong', 'ED', 'KGD', 'ABS', 'IMLTD'];
    //     $w = [8, 32, 17, 20, 20, 24, 34, 25, 34, 15, 19, 29];

    //     foreach ($header as $key => $col) {
    //         $this->Cell($w[$key], 7, $col, 1, 0, 'C', true);
    //     }
    //     $this->Ln();
    // }
}

// $pdf = new PDF('L', 'mm', 'A4');
$pdf = new PDF('L', 'mm', array(210, 297));
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// Isi tabel data
// $pdf->SetFont('Arial', '', 8);
// $no = 1;
// foreach ($data as $row) {
//     $pdf->Cell(8, 6, $no++, 1, 0, 'C');
//     foreach ($row as $k => $value) {
//         $w = [25, 12, 25, 20, 20, 30, 25, 30, 15, 20, 30, 30];
//         $pdf->Cell($w[$k], 6, $value, 1, 0, 'C');
//     }
//     $pdf->Ln();
// }

// $pdf->TableHeader();

$w = array(8, 32, 17, 20, 20, 24, 34, 25, 34, 15, 19, 29);

$pdf->SetFont('Arial', '', 9);

$kantongData = array();

$selData = "SELECT `dst_nokantong`, `dst_notrans`, `dst_jenisktg`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_produk` FROM serahterima_detail WHERE dst_notrans = '$nT'";
$result = mysqli_query($dbi, $selData);
if ($result) {
    $no = 1;
    while ($rowData = mysqli_fetch_assoc($result)) {
        $noKantong = $rowData['dst_nokantong'];

        $query = "SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode FROM hasilelisa WHERE noKantong = '$noKantong'";
        $resultP = $dbi->query($query);

        $hasilPeriksa = getHasilIMLTD($resultP, $labelJenis, $jenisLengkap);

        if ($hasilPeriksa === false) {
            $query2 = "SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode FROM hasilnat WHERE noKantong = '$noKantong'";
            $resultU = $dbi->query($query2);
            $hasilPeriksa = getHasilIMLTD($resultU, $labelJenis, $jenisLengkap);

            if ($hasilPeriksa === false) {
                $hasilPeriksa = 'no data';
            }
        }

        $selST = "SELECT `Status`, metoda, tglpengolahan, kadaluwarsa FROM stokkantong WHERE noKantong = '$noKantong'";
        $resultST = $dbi->query($selST);
        $rowST = $resultST->fetch_assoc();
        $tglOlah = isset($rowST['tglpengolahan']) ? formatTanggal($rowST['tglpengolahan']) : '-';
        $tglED = isset($rowST['kadaluwarsa']) ? formatTanggal($rowST['kadaluwarsa']) : '-';

        $selKGD = "SELECT GolDarah, Rhesus, ket, Cocok, goldarah_asal, rhesus_asal, metode FROM `dkonfirmasi` WHERE NoKantong = '$noKantong'";
        $resultKGD = $dbi->query($selKGD);
        $rowKGD = $resultKGD->fetch_assoc();

        if ($rowKGD['Cocok'] == 0) {
            $rowKGD['Cocok'] = "Cocok";
        } else {
            $rowKGD['Cocok'] = "Tidak Cocok";
        }

        $selABS = "SELECT abs_metode, abs_result FROM `abs` WHERE abs_sample_id = '$noKantong'";
        $resultABS = $dbi->query($selABS);
        $rowABS = $resultABS->fetch_assoc();
        $absResult = isset($rowABS['abs_result']) ? $rowABS['abs_result'] : 'tdk diketahui';

        if ($absResult == "Neg") {
            $rowABS['abs_result'] = "Pos";
        } elseif ($absResult == "Pos") {
            $rowABS['abs_result'] = "Pos";
        } else {
            $rowABS['abs_result'] = "no data";
        }

        switch ($rowST['Status']) {
            case '0':
                $rowST['Status'] = 'Kosong';
                break;
            case '1':
                $rowST['Status'] = 'Karantina';
                break;
            case '2':
                $rowST['Status'] = 'Sehat';
                break;
            case '3':
                $rowST['Status'] = 'Keluar';
                break;
            default:
                $rowST['Status'] = '-';
                break;
        }

        switch ($rowData['dst_rh']) {
            case '+':
                $rowData['dst_rh'] = 'POS';
                break;
            case '-':
                $rowData['dst_rh'] = 'NEG';
                break;
            default:
                $rowData['dst_rh'] = 'Unknown Rhesus';
                break;
        }

        $aborh = $rowData['dst_golda'] . ' (' . $rowData['dst_rh'] . ')';

        switch ($rowData['dst_jenisktg']) {
            case '1':
                $rowData['dst_jenisktg'] = 'SG';
                break;
            case '2':
                $rowData['dst_jenisktg'] = 'DB';
                break;
            case '3':
                $rowData['dst_jenisktg'] = 'TR';
                if ($rowST['metoda'] == 'TT') {
                    $rowData['dst_jenisktg'] = 'TR (TT)';
                } elseif ($rowST['metoda'] == 'TB') {
                    $rowData['dst_jenisktg'] = 'TR (TB)';
                } elseif ($rowST['metoda'] == 'TBF') {
                    $rowData['dst_jenisktg'] = 'TR (Filter)';
                } else {
                    $rowData['dst_jenisktg'] = 'TR';
                }
                break;
            case '4':
                $rowData['dst_jenisktg'] = 'QD';
                if ($rowST['metoda'] == 'TT') {
                    $rowData['dst_jenisktg'] = 'QD (TT)';
                } elseif ($rowST['metoda'] == 'TB') {
                    $rowData['dst_jenisktg'] = 'QD (TB)';
                } elseif ($rowST['metoda'] == 'TBF') {
                    $rowData['dst_jenisktg'] = 'QD (Filter)';
                } else {
                    $rowData['dst_jenisktg'] = 'QD';
                }
                break;
            case '6':
                $rowData['dst_jenisktg'] = 'PB';
                if ($rowST['metoda'] == 'TT') {
                    $rowData['dst_jenisktg'] = 'PB (TT)';
                } elseif ($rowST['metoda'] == 'TB') {
                    $rowData['dst_jenisktg'] = 'PB (TB)';
                } elseif ($rowST['metoda'] == 'TBF') {
                    $rowData['dst_jenisktg'] = 'PB (Filter)';
                } else {
                    $rowData['dst_jenisktg'] = 'PB';
                }
                break;
            default:
                $rowData['dst_jenisktg'] = 'Unknown Rhesus';
                break;
        }

        $kantongData[] = array(
            $no++,
            $rowData['dst_nokantong'],
            // $rowData['dst_notrans'],
            $rowData['dst_jenisktg'],
            $rowData['dst_merk'],
            $aborh,
            $rowData['dst_produk'],
            $tglOlah,
            $rowST['Status'],
            $tglED,
            $rowKGD['Cocok'],
            $rowABS['abs_result'],
            $hasilPeriksa
        );
    }
} else {
    echo "Error: " . mysqli_error($dbi);
}

// data table
$pdf->SetFillColor(245, 245, 245); // Warna abu muda untuk baris striping
$fill = false;

foreach ($kantongData as $row) {
    foreach ($row as $key => $col) {
        $pdf->Cell($w[$key], 6, $col, 1, 0, 'C', $fill);
    }
    $pdf->Ln();
    $fill = !$fill;
}

$pdf->Ln(5);

// Koordinat awal
$startX = $pdf->GetX();
$startY = $pdf->GetY();

// Berapa tinggi yang dibutuhkan Ringkasan + Produk + QRCode?
$neededHeight = 30; // misal: 60mm untuk semua blok

// Hitung batas bawah kertas (210mm kertas A4 Landscape tingginya)
$pageHeight = 210;
$bottomMargin = 20; // Sesuai SetAutoPageBreak
$usableHeight = $pageHeight - $bottomMargin;

// Cek apakah muat?
if (($startY + $neededHeight) > $usableHeight) {
    $pdf->AddPage(); // Tambah halaman baru kalau tidak cukup


    // mmemberi jarak antar header repeater
    $pdf->Ln(5);
    //  ambil ulang posisi x dan y
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

}

$pdf->SetFont('Arial', 'B', 10);

// Header Ringkasan
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(90, 6, 'Ringkasan Data Kantong Darah', 1, 0, 'L', true);

// Spacer antar tabel
$pdf->Cell(5, 6, '', 0, 0);

// Header Jumlah Produk
$pdf->Cell(90, 6, 'Jumlah Per Produk', 1, 0, 'L', true);

// Spacer untuk QRCode
$pdf->Cell(5, 6, '', 0, 0);

// Untuk QRCode, kosongkan sementara
$pdf->Cell(30, 30, '', 1, 0); // area kosong buat QRCode (nanti diisi di bawah)

// Kembali ke baris baru
$pdf->Ln();

// Isi Ringkasan dan Jumlah Per Produk
$pdf->SetFont('Arial', '', 9);

// Ringkasan Data Kantong Darah
$stats = array(
    'Total Data' => count($kantongData),
    'Non-Reaktif' => count(array_filter($kantongData, function($data) { return $data[11] === 'NR'; })),
    'GreyZone' => count(array_filter($kantongData, function($data) { return $data[11] === 'GZ'; })),
    'Reaktif' => count(array_filter($kantongData, function($data) { return $data[11] === 'R'; })),
);

// Jumlah per Produk
$produk = array(
    'PRC' => 6,
    'WB' => 3,
);

// Karena data mungkin berbeda jumlah baris, cari jumlah baris terbesar
$maxRows = max(count($stats), count($produk));

// Ambil posisi sekarang
$currentX = $startX;
$currentY = $startY + 6; // setelah judul header 6pt

for ($i = 0; $i < $maxRows; $i++) {
    $pdf->SetXY($startX, $currentY + ($i * 6));

    // Kolom Ringkasan
    $keys = array_keys($stats);
    if (isset($keys[$i])) {
        $key = $keys[$i];
        $pdf->Cell(45, 6, $key, 1, 0);
        $pdf->Cell(45, 6, $stats[$key], 1, 0);
    } else {
        // Jika habis, tetap kosongkan sel
        $pdf->Cell(45, 6, '', 1, 0);
        $pdf->Cell(45, 6, '', 1, 0);
    }

    // Spacer
    $pdf->Cell(5, 6, '', 0, 0);

    // Kolom Produk
    $produkKeys = array_keys($produk);
    if (isset($produkKeys[$i])) {
        $prodKey = $produkKeys[$i];
        $pdf->Cell(45, 6, $prodKey, 1, 0);
        $pdf->Cell(45, 6, $produk[$prodKey] . ' kantong', 1, 0);
    } else {
        $pdf->Cell(45, 6, '', 1, 0);
        $pdf->Cell(45, 6, '', 1, 0);
    }
}

// Setelah tabel selesai, isi QRCode
require_once('fpdf186/phpqrcode/qrlib.php'); // pastikan Anda punya library QRCode

// Buat QRCode temporary ke file
$tempQRCode = tempnam(sys_get_temp_dir(), 'qrcode') . '.png';
QRcode::png($nT, $tempQRCode, QR_ECLEVEL_L, 3);

// Set posisi QRCode di kanan
$pdf->Image($tempQRCode, $startX + 90 + 5 + 90 + 5, $startY, 30, 30); // (X, Y, Width, Height)

// Hapus file temporary setelah digunakan
unlink($tempQRCode);


$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(90, 7, 'Petugas yang Mengirim', 1, 0, 'C', true);
$pdf->Cell(10, 0, '', 0, 0); // Spacer
$pdf->Cell(90, 7, 'Petugas yang Menerima', 1, 1, 'C', true);

// Header kolom
$pdf->SetFont('Arial', 'B', 9);
$header = array('Nama', 'Tanggal/Jam', 'Paraf');
$wCol = array(40, 30, 20);

foreach ($header as $i => $col) {
    $pdf->Cell($wCol[$i], 7, $col, 1, 0, 'C');
}
// $pdf->Ln();
$pdf->Cell(10, 0, '', 0, 0); // jarak atau spacer

foreach ($header as $i => $col) {
    $pdf->Cell($wCol[$i], 7, $col, 1, 0, 'C');
}

$pdf->Ln();

// Baris data kosong untuk diisi paraf
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($wCol[0], 14, '', 1, 0, 'C');
$pdf->Cell($wCol[1], 14, '', 1, 0, 'C');
$pdf->Cell($wCol[2], 14, '', 1, 0, 'C');
$pdf->Cell(10, 0, '', 0, 0); // Spacer antar tabel
$pdf->Cell($wCol[0], 14, '', 1, 0, 'C');
$pdf->Cell($wCol[1], 14, '', 1, 0, 'C');
$pdf->Cell($wCol[2], 14, '', 1, 0, 'C');
$pdf->Ln();

// // QRCode
// $pdf->Ln(4);
// QRcode::png('https://www.bloodconnect.com', 'qrcode.png');
// $pdf->Image('qrcode.png', 150, 245, 30, 30);

$pdf->Output();
