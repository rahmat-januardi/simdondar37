<?php
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');
require_once('config/koneksi.php');

$nkt = $_GET['noKantong'];
$no_kantonga = substr_replace($nkt, 'A', -1, 1);
$donasi = mysql_fetch_assoc(mysql_query("SELECT * FROM htransaksi WHERE NoKantong ='" . $no_kantonga . "'"));
$kantong = mysql_query("SELECT * FROM stokkantong WHERE noKantong='" . $_GET['noKantong'] . "'");
$kantong1 = mysql_fetch_assoc($kantong);
$donasi22 = mysql_fetch_assoc(mysql_query("SELECT * FROM htransaksi WHERE noKantong='" . $_GET['noKantong'] . "'"));
$kantonga = mysql_fetch_assoc(mysql_query("SELECT * FROM stokkantong WHERE noKantong='" . $no_kantonga . "'"));
$kantongnat = mysql_fetch_assoc(mysql_query("SELECT * FROM hasilnat WHERE noKantong='" . $no_kantonga . "'"));

// Info jam ambil
if ($kantong1['produk'] == 'WE') {
    $ambil1 = date("H:i", strtotime($kantong1['kadaluwarsa']));
} else {
    $ambil1 = date("H:i", strtotime($kantong1['tglpengolahan']));
}
if ($kantong1['produk'] == 'WB' && $donasi22['jam_ambil']) {
    $ambil1 = date("H:i", strtotime($donasi22['jam_ambil']));
}

function tglFormat($tgl)
{
    $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
    $d = date("d", strtotime($tgl));
    $m = date("n", strtotime($tgl));
    $y = date("Y", strtotime($tgl));
    // return $d . ' ' . $bulan[$m] . ' ' . $y;
    return $d . '-' . $m . '-' . $y;
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PMI');
$pdf->SetTitle('Label Kantong Darah');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(true, 0);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage('L', 'IDH');

$style = array('border' => false);
$udd = mysql_fetch_assoc(mysql_query("SELECT nama,alamat,telp FROM utd WHERE down='1' AND aktif='1'"));

// Header
// $pdf->SetXY(0, 0);
// $pdf->Cell(0, 12, 'PALANG MERAH INDONESIA', 0, 1, 'C');

$udd = mysql_fetch_assoc(mysql_query("select nama,alamat, telp from utd where down='1' and aktif='1'"));
$pdf->SetFont('helvetica', 'b', 12);
$pdf->SetXY(0, 1);
$pdf->Cell(0, 0, $udd[nama], 0, 1, 'C');
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(0, 0, $udd[alamat], 0, 1, 'C');
$pdf->Cell(0, 0, 'No. Telp. ' . $udd[telp], 0, 1, 'C');

$pdf->SetLineWidth(0.3);
$pdf->Line(0, 15, 110, 15);

// Status
$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(3, 18);
$pdf->SetFont('helvetica', 'B', 18);
if ($kantong1['hasil_release'] == '2') {
    $pdf->SetFillColor(0, 0, 0); // hitam
    $pdf->SetTextColor(255, 255, 255); // merah
    $pdf->Rect(3, 18, 48, 7, 'DF');
    $pdf->Cell(48, 0, 'R E J E C T', 0, 0, 'C', 0, '', 1);
} else {
    $pdf->SetFillColor(255, 255, 255); // putih
    $pdf->SetTextColor(0, 0, 0); // hitam
    $pdf->Rect(3, 18, 48, 7, 'DF');
    $pdf->Cell(48, 0, 'R E L E A S E D', 0, 0, 'C', 0, '', 1);
}

// Kanan
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(3, 27);
$pdf->Cell(20, 0, 'No. Kantong', 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 0, ': ' . $_GET['noKantong'], 0);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(3, 31);
$pdf->Cell(20, 0, 'No. Selang', 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 0, ': ' . $kantonga['noSelang'], 0);

$pdf->SetXY(4, 36);
$pdf->write1DBarcode(trim($_GET['noKantong']), 'C128', '', '', '48', 10, 0.35, $style, 'B');

$pdf->SetXY(55, 47);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(18, 0, 'Pengambilan ', 0);
$pdf->Cell(0, 0, ': ' . tglFormat($kantong1['tgl_Aftap']), 0);

$pdf->SetXY(55, 51);
$pdf->Cell(18, 0, 'Pengolahan', 0);
if ($kantong1['produk'] != 'WB') {
    $pdf->Cell(0, 0, ': ' . tglFormat($kantong1['tglpengolahan']), 0);
} else {
    $pdf->Cell(0, 0, ': -', 0);
}

$pdf->SetXY(55, 55);
$pdf->Cell(18, 0, 'Kedaluwarsa', 0);
$pdf->Cell(0, 0, ': ' . tglFormat($kantong1['kadaluwarsa']) . '  ' . $ambil1, 0);

// Fungsi untuk menghapus '450' dari nama produk
function cleanProductName($product)
{
    return preg_replace('/\s*450\b/', '', $product); // Menghapus '450' atau ' 450' di akhir string
}

// Terapkan pembersihan pada nama produk
$cleanedProduct = cleanProductName($kantong1['produk']);

$pdf->SetXY(3, 67);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(23, 0, 'Produk', 0);
$pdf->Cell(0, 0, ': ', 0);
$pdf->SetXY(3, 70);
$pdf->SetFont('helvetica', 'B', 35);
$pdf->Cell(0, 5, $cleanedProduct, 0);
$pdf->SetXY(3, 85);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(23, 0, 'Volume', 0);
$pdf->Cell(0, 0, ': ' . $kantong1['volume'] . ' cc', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(3, 90);
$pdf->Cell(23, 0, 'Suhu Simpan', 0);
if ($cleanedProduct == 'FFP') {
    $pdf->Cell(0, 0, ': <= -20°C', 0);
} elseif ($cleanedProduct == 'PRP' || $cleanedProduct == 'TC') {
    $pdf->Cell(0, 0, ': 20-24°C', 0);
} else {
    $pdf->Cell(0, 0, ': 2-6°C', 0);
}
//$pdf->Cell(0, 0, ': 2-6°C', 0);

// Kanan
$pdf->SetXY(55, 25);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 0, 'Golongan Darah ABO dan Rhesus :', 0);
$pdf->SetXY(65, 28);
$pdf->SetFont('helvetica', 'B', 40);
$pdf->Cell(0, 0, $kantong1['gol_darah'] . '' . $kantong1['RhesusDrh'], 0);
$pdf->SetXY(55, 47);
// $pdf->SetFont('helvetica', '', 8);
// if ($kantong1['abs'] == 'Pos' || $kantong1['abs'] == 'Neg') {
//     $pdf->Cell(0, 0, 'ABS : ' . strtoupper($kantong1['abs']), 0);
// } else {
//     $pdf->Cell(0, 0, 'ABS : -', 0);
// }
$pdf->SetXY(55, 67);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(0, 0, 'Non-Reaktif:', 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(55, 71);
$pdf->Cell(0, 0, '1. CHLIA (HIV, HCV, HBsAg, Sifilis)', 0);
$pdf->SetXY(55, 74);
if (!empty($kantongnat)) {
    $pdf->Cell(0, 0, '2. NAT (HIV, HCV, HBsAg)', 0);
}



$pdf->Output('label_kantong.pdf', 'I');