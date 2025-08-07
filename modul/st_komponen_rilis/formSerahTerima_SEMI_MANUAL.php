<?php
require('fpdf186/fpdf.php');

class PDF extends FPDF
{
    // Header halaman
    function Header()
    {
        // Background Header
        // $this->SetFillColor(145, 34, 0); // Warna #912200
        // $this->Rect(0, 0, 297, 20, 'F'); // Untuk landscape A4 width 297mm
        // $this->SetTextColor(255, 255, 255); // Warna teks putih

        $this->SetFont('Arial', '', 10);

        $this->Cell(0, 5, 'NAMA UDD', 0, 0, 'L');
        $this->Cell(0, 5, 'No. Dokumen: UDDSLM-PD-L4-011-2025', 0, 1, 'R');

        $this->Cell(0, 5, 'Formulir Serah Terima Komponen Darah', 0, 0, 'L');
        $this->Cell(0, 5, 'Versi: 002.', 0, 1, 'R');
        $this->Ln(1);

        $x1 = 10; // jarak dari kiri
        $x2 = 287; // jarak dari kiri
        $y1 = $this->GetY(); // posisi sekarang Y
        $y2 = $y1 + 10; // panjang garis ke bawah
        $this->SetDrawColor(0, 0, 0); // warna hitam
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
            $this->Cell(0, 7, 'Informasi Pengiriman', 1, 1, 'L', true);
            $this->Ln(2);

            // Box
            $this->SetFont('Arial', '', 9);
            $this->SetFillColor(245, 245, 245); // Abu abu muda

            // Data Dummy Informasi
            $info1 = [
                ['Tangal', '27 April 2025 12:09'],
                ['No. Transaksi', 'KR240425-317D-0007'],
                ['Bagian yang Mengirimkan', 'Pengolahan Darah'],
                ['Peruntukan', 'POSTING HASIL PENGOLAHAN DARAH'],
                ['Jenis Serah Terima', 'Kantong (Komponen Darah)'],
            ];
            $info2 = [
                ['Bagian yang Menerima', 'RILIS'],
                ['Suhu Pengiriman', '2-6 C'],
                ['Suhu Saat Pengiriman', '4-8 C'],
                ['Kondisi Saat Pengiriman', 'Baik'],
                ['Kode Alat Pengiriman', 'K001'],
            ];

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

        // Table Header
        // $this->SetFont('Arial', 'B', 9);
        // $this->SetFillColor(200, 220, 255);
        // $this->Cell(8, 6, 'No', 1, 0, 'C', true);
        // $this->Cell(25, 6, 'No Kantong', 1, 0, 'C', true);
        // $this->Cell(12, 6, 'Jns Ktg', 1, 0, 'C', true);
        // $this->Cell(25, 6, 'Merk', 1, 0, 'C', true);
        // $this->Cell(20, 6, 'ABO (RH)', 1, 0, 'C', true);
        // $this->Cell(20, 6, 'Produk', 1, 0, 'C', true);
        // $this->Cell(30, 6, 'Tgl Olah', 1, 0, 'C', true);
        // $this->Cell(25, 6, 'Status Kantong', 1, 0, 'C', true);
        // $this->Cell(30, 6, 'ED', 1, 0, 'C', true);
        // $this->Cell(15, 6, 'KGD', 1, 0, 'C', true);
        // $this->Cell(20, 6, 'ABS', 1, 0, 'C', true);
        // $this->Cell(30, 6, 'IMLTD', 1, 0, 'C', true);
        // $this->Cell(30, 6, 'Aksi', 1, 1, 'C', true);

        if ($this->PageNo() == 1) {
            $this->SetY(85);
        }

        $this->SetFillColor(200, 200, 200);
        $this->SetFont('Arial', 'B', 8);
        $header = ['No.', 'No Kantong', 'Jns Ktg', 'Merk', 'ABO (RH)', 'Produk', 'Tgl Olah', 'Status Kantong', 'ED', 'KGD', 'ABS', 'IMLTD'];
        $w = [8, 32, 17, 20, 20, 24, 34, 25, 34, 15, 19, 29];

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
        $this->Cell(0, 10, 'Dicetak pada: ' . date('d-m-Y H:i') . ' oleh: ', 0, 0, 'L');
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

$w = [8, 32, 17, 20, 20, 24, 34, 25, 34, 15, 19, 29];

$pdf->SetFont('Arial', '', 9);

// Data dummy komponen darah
$kantongData = [
    ['1', 'S6237212A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 16:48', 'Keluar', '27 Jan 2025 - 11:27', 'Cocok', 'tdk diketahui', 'NR'],
    ['2', 'S6258775A', 'DB', 'IControl', 'A (POS)', 'PRC', '05 Nov 2024 - 18:04', 'Not for Produksi', '10 Des 2024 - 10:57', 'Cocok', 'tdk diketahui', 'GZ (HCV)'],
    ['3', 'S6181407A', 'DB', 'IControl', 'AB (POS)', 'PRC', '06 Des 2024 - 15:40', 'Not for Produksi', '10 Jan 2025 - 13:26', 'Cocok', 'tdk diketahui', 'GZ (Syphilis)'],
    ['4', 'S6381619A', 'DB', 'IControl', 'A (POS)', 'WB', '21 Des 2024 - 17:54', 'Not for Produksi', '25 Jan 2025 - 14:52', 'Cocok', 'tdk diketahui', 'R (HIV)'],
    ['5', 'S5455615A', 'TR', 'IControl', 'B (POS)', 'WB', '21 Des 2024 - 17:53', 'Not for  Produksi', '25 Jan 2025 - 11:55', 'Cocok', 'tdk diketahui', 'R (HBsAg)'],
    ['6', 'S6349741A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 17:12', 'Keluar', '27 Jan 2025 - 10:19', 'Cocok', 'tdk diketahui', 'NR'],
    ['7', 'S6358266A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 16:48', 'Keluar', '27 Jan 2025 - 11:42', 'Cocok', 'tdk diketahui', 'NR'],
    ['8', 'S5456109A', 'TR', 'IControl', 'B (POS)', 'WB', '23 Des 2024 - 15:43', 'Not for Produksi', '27 Jan 2025 - 10:57', 'Cocok', 'tdk diketahui', 'R (Syphilis)'],
    ['9', 'S6381619A', 'DB', 'IControl', 'A (POS)', 'WB', '21 Des 2024 - 17:54', 'Not for Produksi', '25 Jan 2025 - 14:52', 'Cocok', 'tdk diketahui', 'R (HIV)'],
    ['10', 'S5455615A', 'TR', 'IControl', 'B (POS)', 'WB', '21 Des 2024 - 17:53', 'Not for Produksi', '25 Jan 2025 - 11:55', 'Cocok', 'tdk diketahui', 'R (HBsAg)'],
    ['11', 'S6349741A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 17:12', 'Keluar', '27 Jan 2025 - 10:19', 'Cocok', 'tdk diketahui', 'NR'],
    ['12', 'S6358266A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 16:48', 'Keluar', '27 Jan 2025 - 11:42', 'Cocok', 'tdk diketahui', 'NR'],
    ['13', 'S5456109A', 'TR', 'IControl', 'B (POS)', 'WB', '23 Des 2024 - 15:43', 'Not for Produksi', '27 Jan 2025 - 10:57', 'Cocok', 'tdk diketahui', 'R (Syphilis)'],
    ['14', 'S6381619A', 'DB', 'IControl', 'A (POS)', 'WB', '21 Des 2024 - 17:54', 'Not for Produksi', '25 Jan 2025 - 14:52', 'Cocok', 'tdk diketahui', 'R (HIV)'],
    ['15', 'S5455615A', 'TR', 'IControl', 'B (POS)', 'WB', '21 Des 2024 - 17:53', 'Not for Produksi', '25 Jan 2025 - 11:55', 'Cocok', 'tdk diketahui', 'R (HBsAg)'],
    ['16', 'S6349741A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 17:12', 'Keluar', '27 Jan 2025 - 10:19', 'Cocok', 'tdk diketahui', 'NR'],
    ['17', 'S6358266A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 16:48', 'Keluar', '27 Jan 2025 - 11:42', 'Cocok', 'tdk diketahui', 'NR'],
    ['18', 'S5456109A', 'TR', 'IControl', 'B (POS)', 'WB', '23 Des 2024 - 15:43', 'Not for Produksi', '27 Jan 2025 - 10:57', 'Cocok', 'tdk diketahui', 'R (Syphilis)'],
    ['19', 'S6381619A', 'DB', 'IControl', 'A (POS)', 'WB', '21 Des 2024 - 17:54', 'Not for Produksi', '25 Jan 2025 - 14:52', 'Cocok', 'tdk diketahui', 'R (HIV)'],
    ['20', 'S5455615A', 'TR', 'IControl', 'B (POS)', 'WB', '21 Des 2024 - 17:53', 'Not for Produksi', '25 Jan 2025 - 11:55', 'Cocok', 'tdk diketahui', 'R (HBsAg)'],
    ['21', 'S6349741A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 17:12', 'Keluar', '27 Jan 2025 - 10:19', 'Cocok', 'tdk diketahui', 'NR'],
    ['22', 'S6358266A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 16:48', 'Keluar', '27 Jan 2025 - 11:42', 'Cocok', 'tdk diketahui', 'NR'],
    ['23', 'S5456109A', 'TR', 'IControl', 'B (POS)', 'WB', '23 Des 2024 - 15:43', 'Not for Produksi', '27 Jan 2025 - 10:57', 'Cocok', 'tdk diketahui', 'R (Syphilis)'],
    ['24', 'S6381619A', 'DB', 'IControl', 'A (POS)', 'WB', '21 Des 2024 - 17:54', 'Not for Produksi', '25 Jan 2025 - 14:52', 'Cocok', 'tdk diketahui', 'R (HIV)'],
    ['25', 'S5455615A', 'TR', 'IControl', 'B (POS)', 'WB', '21 Des 2024 - 17:53', 'Not for Produksi', '25 Jan 2025 - 11:55', 'Cocok', 'tdk diketahui', 'R (HBsAg)'],
    ['26', 'S6349741A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 17:12', 'Keluar', '27 Jan 2025 - 10:19', 'Cocok', 'tdk diketahui', 'NR'],
    ['27', 'S6358266A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 16:48', 'Keluar', '27 Jan 2025 - 11:42', 'Cocok', 'tdk diketahui', 'NR'],
    ['28', 'S5456109A', 'TR', 'IControl', 'B (POS)', 'WB', '23 Des 2024 - 15:43', 'Not for Produksi', '27 Jan 2025 - 10:57', 'Cocok', 'tdk diketahui', 'R (Syphilis)'],
    ['29', 'S6364706A', 'DB', 'IControl', 'O (POS)', 'PRC', '23 Des 2024 - 16:25', 'Keluar', '27 Jan 2025 - 11:46', 'Cocok', 'tdk diketahui', 'NR'],
];

// $pdf->SetFillColor(235, 235, 235); // Abu abu muda
$pdf->SetFillColor(245, 245, 245); // Warna abu muda untuk baris striping
$fill = false;

foreach ($kantongData as $row) {
    foreach ($row as $key => $col) {
        $pdf->Cell($w[$key], 6, $col, 1, 0, 'C', $fill);
    }
    $pdf->Ln();
    $fill = !$fill;
}


// Contoh Data Kantong
$pdf->SetFont('Arial', '', 8);

// Ringkasan Data
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(100, 6, 'Ringkasan Data Kantong Darah', 1, 1, 'L', true);
// $pdf->Cell(50, 6, 'Ringkasan Data Kantong Darah', 0, 1);

$pdf->SetFont('Arial', '', 9);
// $pdf->Cell(50, 6, 'Total Data', 1, 0);
// $pdf->Cell(50, 6, '9 kantong', 1, 1);

// $pdf->Cell(50, 6, 'Non-Reaktif', 1, 0);
// $pdf->Cell(50, 6, '4', 1, 1);

// $pdf->Cell(50, 6, 'GreyZone', 1, 0);
// $pdf->Cell(50, 6, '2', 1, 1);

// $pdf->Cell(50, 6, 'Reaktif', 1, 0);
// $pdf->Cell(50, 6, '3', 1, 1);

// Ringkasan Count
$stats = [
    'Total Data' => count($kantongData),
    'Non-Reaktif' => count(array_filter($kantongData, fn($data) => $data[11] === 'NR')),
    'GreyZone' => count(array_filter($kantongData, fn($data) => $data[11] === 'GZ')),
    'Reaktif' => count(array_filter($kantongData, fn($data) => $data[11] === 'R')),
];

foreach ($stats as $key => $value) {
    $pdf->Cell(50, 6, $key, 1, 0);
    $pdf->Cell(50, 6, $value, 1, 1);
}

$pdf->Ln(3);

$pdf->Cell(100, 6, 'Jumlah Per Produk', 1, 1, 'L', true);
$pdf->Cell(50, 6, 'PRC: 6 kantong', 1, 1);
$pdf->Cell(50, 6, 'WB: 3 kantong', 1, 1);

// // QRCode
// $pdf->Ln(4);
// QRcode::png('https://www.bloodconnect.com', 'qrcode.png');
// $pdf->Image('qrcode.png', 150, 245, 30, 30);

$pdf->Output();
