<?php
ob_start();
require_once("../tcpdf2/tcpdf.php");

function tgl_indo($tanggal)
{
    $bulan = array(
        1 => "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"
    );
    $pecahkan = explode("-", $tanggal);
    return $pecahkan[2] . " " . $bulan[(int)$pecahkan[1]] . " " . $pecahkan[0];
}

require_once("../config/dbi_connect.php");

$no_dokumen = "";
$nokantong = $_GET["kantong"];
$g_tipebarcode = $_GET["tipe"];
$kantong_a = substr($nokantong, 0, -1) . "A";

$sqlrelease = mysqli_query($dbi, "SELECT *, `rnokantong`, round(`rvolume`,0) as volume,
    `rproduk`, `rgolda`, DATE_FORMAT(`rtgl_aftap`, '%d-%m-%Y') as tglaftap,
    DATE_FORMAT(`rtgl_olah`, '%d-%m-%Y') as tglolah,
    DATE_FORMAT(`rtgl_ed`, '%d-%m-%Y') as tgled
    FROM `release` WHERE rnokantong='$nokantong'");

$queryrelease = mysqli_fetch_assoc($sqlrelease);

$pdf = new TCPDF("L", "mm", array(90, 70), true, "UTF-8", false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("SIMDOBDAR");
$pdf->SetTitle(strtoupper($queryrelease["rnokantong"]));
$pdf->SetSubject("Label Release");
$pdf->SetKeywords("Label Release");
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(true, 0);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont("helvetica", "", 10);

$style_br = array(
    "position" => "",
    "align" => "L",
    "stretch" => true,
    "fitwidth" => false,
    "cellfitalign" => "",
    "border" => false,
    "hpadding" => "0",
    "vpadding" => "0",
    "fgcolor" => array(0, 0, 0),
    "bgcolor" => false,
    "text" => false,
    "font" => "helvetica",
    "fontsize" => 8,
    "stretchtext" => 4
);

$style_qr = array(
    "border" => false,
    "padding" => 0,
    "fgcolor" => array(0, 0, 0),
    "bgcolor" => false
);

$volume_kantong = $queryrelease["volume"];
$gol_darah = $queryrelease["rgolda"];
$gol_abo = substr($gol_darah, 0, -1);
$gol_rh = substr($gol_darah, -1);
$rhesus = ($gol_rh == "-") ? "Rh NEGATIF" : "Rh POSITIF";
$namaproduk = $queryrelease["rproduk"];

$qryproduk = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT * FROM produk WHERE Nama='$namaproduk'"));
$namaproduklengkap = $qryproduk["lengkap"];

switch ($queryrelease["rstatus"]) {
    case "0":
        $ketlulus = "QA: LULUS";
        break;
    case "1":
        $ketlulus = "QA: TIDAK LULUS";
        break;
    case "2":
        $ketlulus = "QA: LULUS";
        break;
}

$qrykantong = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT * FROM `stokkantong` WHERE `noKantong`='$nokantong'"));
$volume_asal = $qrykantong["volumeasal"];
$nomor_selang = $qrykantong["noSelang"];
$NAT = $qrykantong["tgl_nat"];

$donasi = mysqli_query($dbi, "SELECT *, date(tgl) as tanggal_aftap FROM htransaksi WHERE NoKantong='$kantong_a'");
$donasi = mysqli_fetch_assoc($donasi);
$jenis_donor = ($donasi["JenisDonor"] == "0") ? "Donor Sukarela" : "Donor Pengganti";
$no_donasi = $donasi["NoTrans"];
$tgl_aftap = tgl_indo($donasi["tanggal_aftap"]);

$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);

$udd = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama`, `alamat` FROM `utd` WHERE `aktif`='1'"));

$pdf->SetFont("helvetica", "b", 11);
$pdf->SetXY(2, 2);
$pdf->Cell(0, 0, $udd["nama"], 0, 1, "C");

$pdf->SetFont("helvetica", "", 8);
$pdf->SetXY(2, 6);
$pdf->Cell(0, 0, $udd["alamat"], 0, 1, "C");

$margin = 2;
$width = 88;
$height = 58;
$centerX = 46;
$centerY = 39;

$pdf->SetLineWidth(0.5);
$pdf->Line($margin, 10, $width, 10);
$pdf->Line($margin, 68, $width, 68);
$pdf->Line($margin, 10, $margin, 68);
$pdf->Line($width, 10, $width, 68);

$pdf->SetLineWidth(0.3);
$pdf->Line($margin, $centerY, $centerX, $centerY);
$pdf->Line($centerX, 10, $centerX, 58);

$pdf->SetXY(45, 11);
$pdf->SetFont("helvetica", "b", 45);
$pdf->Cell(0, 0, $gol_abo, 0, 5, "C");

$pdf->SetXY(45, 28);
$pdf->SetFont("helvetica", "b", 12);
$pdf->Cell(0, 0, $rhesus, 0, 5, "C");

$pdf->Line($centerX, 39, $width, 39);

$pdf->SetXY(2, 10);
$pdf->SetFont("helvetica", "b", 10);
$pdf->Cell(44, 0, $ketlulus, 1, 1, "C");

$pj_nokantong = strlen($queryrelease["rnokantong"]);

$pdf->SetXY(1, 15);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(49, 0, "No. Kantong", 0, 1, "C");

if (strtoupper($queryrelease["rnokantong"]) == strtoupper($nomor_selang)) {
    $add_x = 3;
} else {
    if (trim($nomor_selang) == "") {
        $add_x = 3;
    } else {
        $add_x = 0;
        $pdf->SetXY(1, 33);
        $pdf->SetFont("helvetica", "b", 11);
        $pdf->Cell(49, 0, "Selang:" . strtoupper($nomor_selang), 0, 1, "C");
    }
}

$pdf->SetXY(4, 20);
$pdf->write1DBarcode(strtoupper($queryrelease["rnokantong"]), $g_tipebarcode, "", "", 40, 8 + $add_x, 0.28, $style_br, "L");

$pdf->SetFont("helvetica", "b", ($pj_nokantong > 10) ? 12 : 14);

$pdf->SetXY(1, 28 + $add_x);
$pdf->Cell(49, 0, strtoupper($queryrelease["rnokantong"]), 0, 1, "C");

$pdf->SetXY(46, 40);
$pdf->SetFont("helvetica", "", 9);
$pdf->Cell(0, 0, "Aftap", 0);

$pdf->SetXY(59, 40);
$pdf->Cell(0, 0, ":", 0);

$pdf->SetXY(60, 40);
$pdf->SetFont("helvetica", "b", 9);
$pdf->Cell(0, 0, $queryrelease["tglaftap"], 0);

$pdf->SetXY(46, 44);
$pdf->SetFont("helvetica", "", 9);
$pdf->Cell(0, 0, "Produksi", 0);

$pdf->SetXY(59, 44);
$pdf->Cell(0, 0, ":", 0);

$pdf->SetXY(60, 44);
$pdf->SetFont("helvetica", "b", 9);
$pdf->Cell(0, 0, $queryrelease["tglolah"], 0);

$pdf->SetXY(46, 48);
$pdf->SetFont("helvetica", "", 9);
$pdf->Cell(0, 0, "ED", 0);

$pdf->SetXY(59, 48);
$pdf->Cell(0, 0, ":", 0);

$pdf->SetXY(60, 48);
$pdf->SetFont("helvetica", "b", 9);
$pdf->Cell(0, 0, $queryrelease["tgled"], 0);

$sqlsuhukirim = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `suhutransport`, `suhusimpan` FROM `produk` WHERE `Nama`='$namaproduk'"));
$suhupengiriman = $sqlsuhukirim["suhutransport"];
$suhusimpan = $sqlsuhukirim["suhusimpan"];

$pdf->SetXY(46, 53);
$pdf->SetFont("helvetica", "", 9);
$pdf->Cell(0, 0, "Suhu simpan:", 0);

$pdf->SetXY(70, 53);
$pdf->SetFont("helvetica", "b", 9);
$pdf->Cell(0, 0, $suhusimpan . TCPDF_FONTS::unichr(186) . "C", 0, 0, "L");

$productName = $queryrelease["rproduk"];
$volumeText = $queryrelease["volume"] . " mL";
$maxWidth = 44;
$startY = 40;
$fontSize = 22;

$pdf->SetFont("helvetica", "b", $fontSize);
while ($pdf->GetStringWidth($productName) > $maxWidth && $fontSize > 8) {
    $fontSize--;
    $pdf->SetFont("helvetica", "b", $fontSize);
}

if ($fontSize == 8 && $pdf->GetStringWidth($productName) > $maxWidth) {
    $pdf->SetXY(2, $startY);
    $pdf->MultiCell($maxWidth, 5, $productName, 0, "C");
    $newY = $pdf->GetY();
    $pdf->SetXY(2, $newY + 2);
} else {
    $pdf->SetXY(2, $startY);
    $pdf->Cell($maxWidth, 0, $productName, 0, 0, "C");
    $pdf->SetXY(2, 48);
}

$pdf->SetFont("helvetica", "b", 18);
$pdf->Cell($maxWidth, 0, $volumeText, 0, 0, "C");

if ($NAT == null) {
    $ket = "Hasil Uji Saring NON REAKTIF terhadap Hepatitis B, Hepatitis C, HIV, Sifilis dengan metode ChLIA. Tidak ditemukan antibodi eritrosit pada darah donor";
} else {
    $ket = "Hasil Uji Saring NON REAKTIF terhadap Hepatitis B, Hepatitis C, HIV, Sifilis dengan metode ChLIA & NAT. Tidak ditemukan antibodi eritrosit pada darah donor";
}
$pdf->SetXY(2, 58);
$pdf->SetFont("helvetica", "", 7.5);
$pdf->MultiCell(86, 3, $ket, "T", "C", 0, 0, "", "", true, 0, 10);

ob_clean();
$pdf->IncludeJS("print();");
$pdf->Output($nokantong . ".pdf", "I");