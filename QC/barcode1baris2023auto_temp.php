<?php
/*
Preview barcode untuk QC luar
Sumber data: registrasi_luarqc_temp
Tidak insert ke tabel mana pun
*/

ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once dirname(__FILE__) . '/../tcpdf/tcpdf.php';
include_once dirname(__FILE__) . '/../config/db_connect.php';

$namauser = isset($_SESSION["namauser"]) ? $_SESSION["namauser"] : "";

function getJenisKantongKode($jenis)
{
    switch ((string)$jenis) {
        case "1":
            return array("SG", "Single");
        case "2":
            return array("DB", "Double");
        case "3":
            return array("TR", "Triple");
        case "4":
            return array("QD", "Quadruple");
        case "5":
            return array("QT", "Pediatrik/Quintuple");
        case "6":
            return array("SX", "Sextuple");
        default:
            return array("", "");
    }
}

function renderBarcodePage($pdf, $namaudd, $kodejenis, $noKantong, $tipeBarcode, $volume, $tglEd, $merk, $produk)
{
    $style = array(
        "position" => "",
        "align" => "L",
        "stretch" => false,
        "fitwidth" => true,
        "cellfitalign" => "",
        "border" => false,
        "hpadding" => "auto",
        "vpadding" => "auto",
        "fgcolor" => array(0, 0, 0),
        "bgcolor" => false,
        "text" => false,
        "font" => "helvetica",
        "fontsize" => 11,
        "stretchtext" => 1
    );

    $pdf->AddPage();
    $pdf->SetFont("helvetica", "", 7);
    $pdf->SetXY(1, 1);
    $pdf->Cell(1, 0, $namaudd, 0, 0);

    $pdf->SetFont("helvetica", "", 7);
    $pdf->SetXY(49, 1);
    $pdf->Cell(0, 0, $kodejenis, 0, 0, "R");

    $pdf->SetXY(2, 4);
    $pdf->write1DBarcode(strtoupper($noKantong), $tipeBarcode, "", "", "46", 9, 0.4, $style, "N");

    $pdf->SetFont("helvetica", "", 9);
    $pdf->SetXY(1, 12);
    $pdf->Cell(0, 0, strtoupper($noKantong), 0, 0);

    $info = trim($produk . " | " . $volume . " ml | ED:" . $tglEd);
    if ($info !== " | ml | ED:") {
        $pdf->SetFont("helvetica", "", 6);
        $pdf->SetXY(1, 16);
        $pdf->Cell(0, 0, $info, 0, 0, "R");
    }
}

if ($namauser === "") {
    echo "Session user tidak ditemukan.";
    exit;
}

mysql_query("SET NAMES utf8");

$query = "
    SELECT t.*, u.nama AS nama_utd
    FROM registrasi_luarqc_temp t
    LEFT JOIN utd u ON u.id = t.asal_utd
    WHERE t.user_input = '" . mysql_real_escape_string($namauser) . "'
    ORDER BY t.id ASC
";

$q = mysql_query($query);

if (!$q) {
    echo "Query gagal: " . mysql_error();
    exit;
}

if (mysql_num_rows($q) == 0) {
    echo "Tidak ada data sementara untuk dipreview.";
    exit;
}

$pdf = new TCPDF("L", "mm", array(50, 20), true, "UTF-8", false);
$pdf->SetTitle("Preview Barcode QC Luar");
$pdf->SetSubject("Preview Barcode");
$pdf->SetAuthor("SIMDONDAR");
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(0, 0, 0);

while ($d = mysql_fetch_assoc($q)) {
    list($kodejenis, $namajenis) = getJenisKantongKode(isset($d["jenis"]) ? $d["jenis"] : "");
    $namaudd = !empty($d["nama_utd"]) ? $d["nama_utd"] : $d["asal_utd"];

    renderBarcodePage(
        $pdf,
        $namaudd,
        $kodejenis,
        isset($d["nokantong"]) ? $d["nokantong"] : "",
        "C128",
        isset($d["volume"]) ? $d["volume"] : "",
        isset($d["kadaluwarsa"]) ? date("d/m/Y", strtotime($d["kadaluwarsa"])) : "",
        isset($d["merk"]) ? $d["merk"] : "",
        isset($d["produk"]) ? $d["produk"] : ""
    );
}

ob_clean();
$pdf->Output("barcode_preview.pdf", "I");
exit;
