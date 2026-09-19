<?php
/*
SIMDONDAR 3.7
05-2025
*/

ob_start();
session_start();

require_once("../tcpdf/tcpdf.php");
require_once("../config/dbi_connect.php");

mysqli_query($dbi, "SET GLOBAL sql_mode = '';");

$leveluser = isset($_SESSION["leveluser"]) ? strtoupper($_SESSION["leveluser"]) : "";
$namauser  = isset($_SESSION["namauser"]) ? strtoupper($_SESSION["namauser"]) : "";

function getJenisKantongKode($jenis)
{
    switch ((string) $jenis) {
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
        case "7":
            return array("ST", "Septuple");
        case "8":
            return array("OP", "Octuple");
        case "9":
            return array("NN", "Nonuple");
        case "10":
            return array("DC", "Decuple");
        default:
            return array("", "");
    }
}

function renderBarcodePage($pdf, $namaudd, $kodejenis, $noKantong, $tipeBarcode, $addX, $tampilkanInfo, $volume, $lot, $tglEd)
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
        "text" => true,
        "font" => "helvetica",
        "fontsize" => 11,
        "stretchtext" => 1
    );

    $style1 = array(
        "position" => "S",
        "align" => "L",
        "stretch" => false,
        "fitwidth" => true,
        "cellfitalign" => "",
        "border" => false,
        "hpadding" => "1",
        "vpadding" => "1",
        "fgcolor" => array(0, 0, 0),
        "bgcolor" => false,
        "text" => false,
        "font" => "helvetica",
        "fontsize" => 11,
        "stretchtext" => 1
    );

    $pdf->AddPage();
    $pdf->SetFont("helvetica", "", 8);
    $pdf->SetXY(1, 1);
    $pdf->Cell(1, 0, $namaudd, 0, 0);
    $pdf->SetFont("helvetica", "", 7);
    $pdf->SetXY(49, 1);
    $pdf->Cell(0, 0, $kodejenis, 0, 0, "R");
    $pdf->SetXY(2, 4);
    $pdf->write1DBarcode(strtoupper($noKantong), $tipeBarcode, "", "", "46", 9 + $addX, 0.4, $style1, "N");
    $pdf->SetFont("helvetica", "", 11);
    $pdf->SetXY(1, 12 + $addX);
    $pdf->Cell(0, 0, strtoupper($noKantong), 0, 0);

    if ($tampilkanInfo != "0") {
        $pdf->SetFont("helvetica", "", 6);
        $pdf->SetXY(1, 16);
        $pdf->Cell(0, 0, $volume . "ml, LOT:" . $lot . ", ED:" . $tglEd, 0, 0, "R");
    }
}

function renderInfoPage($pdf, $noKantong, $namajenis, $tanggalbuka, $v_user, $v_merk, $v_volume, $lamaBuka)
{
    $pdf->AddPage();
    $pdf->SetFont("helvetica", "b", 8);
    $pdf->SetXY(1, 0);
    $pdf->Cell(0, 0, $noKantong, 0, 0);
    $pdf->SetXY(20, 0);
    $pdf->Cell(0, 0, ":" . $namajenis, 0, 0);

    $pdf->SetFont("helvetica", "", 8);
    $pdf->SetXY(1, 3);
    $pdf->Cell(0, 0, "Tanggal", 0, 0);
    $pdf->SetFont("helvetica", "b", 8);
    $pdf->SetXY(15, 3);
    $pdf->Cell(0, 0, ":" . $tanggalbuka, 0, 0);

    $pdf->SetFont("helvetica", "", 8);
    $pdf->SetXY(1, 6);
    $pdf->Cell(0, 0, "Petugas", 0, 0);
    $pdf->SetFont("helvetica", "b", 8);
    $pdf->SetXY(15, 6);
    $pdf->Cell(0, 0, ":" . $v_user, 0, 0);

    $pdf->SetFont("helvetica", "", 8);
    $pdf->SetXY(1, 9);
    $pdf->Cell(0, 0, "Merk", 0, 0);
    $pdf->SetFont("helvetica", "b", 8);
    $pdf->SetXY(15, 9);
    $pdf->Cell(0, 0, ":" . $v_merk . " " . $v_volume, 0, 0);

    $pdf->SetFont("helvetica", "", 8);
    $pdf->SetXY(1, 12);
    $pdf->Cell(0, 0, "Lama buka", 0, 0);
    $pdf->SetFont("helvetica", "b", 8);
    $pdf->SetXY(15, 12);
    $pdf->Cell(0, 0, ":" . $lamaBuka . " hr", 0, 0);
}

$v_tgl         = isset($_GET["tgl"]) ? $_GET["tgl"] : "";
$v_tipebarcode  = isset($_GET["tipe"]) ? $_GET["tipe"] : "C128";
$v_jenislabel   = isset($_GET["lbl"]) ? $_GET["lbl"] : "1";
$v_merk        = isset($_GET["merk"]) ? $_GET["merk"] : "";
$v_volume      = isset($_GET["vol"]) ? $_GET["vol"] : "";
$v_jenisktg    = isset($_GET["jenisktg"]) ? $_GET["jenisktg"] : "1";
$v_lot         = isset($_GET["lot"]) ? $_GET["lot"] : "";
$v_user        = isset($_GET["usr"]) ? $_GET["usr"] : $namauser;
$v_tgled       = isset($_GET["ed"]) ? $_GET["ed"] : "";
$v_jmlcetak    = isset($_GET["jmlcetak"]) ? (int) $_GET["jmlcetak"] : 1;
$v_jmlkantong  = isset($_GET["jmlktg"]) ? (int) $_GET["jmlktg"] : 1;
$v_lamabuka    = isset($_GET["lambuka"]) ? $_GET["lambuka"] : "";
$v_edbuka      = isset($_GET["edbuka"]) ? $_GET["edbuka"] : "";
$v_usrlevel    = isset($_GET["usrlevel"]) ? $_GET["usrlevel"] : $leveluser;
$v_chkinfo     = isset($_GET["chkinfo"]) ? $_GET["chkinfo"] : "0";
$v_pemisah     = isset($_GET["pemisah"]) ? $_GET["pemisah"] : "0";
$v_metode      = isset($_GET["metode"]) ? trim($_GET["metode"]) : "";
$v_labelktg    = isset($_GET["lblktg"]) ? $_GET["lblktg"] : "1";

list($kodejenis, $namajenis) = getJenisKantongKode($v_jenisktg);

if ($v_metode != "") {
    $kodejenis .= "/" . $v_metode;
}

$add_x = ($v_labelktg == "0") ? 3 : 0;

$pdf = new TCPDF("L", "mm", array(50, 20), true, "UTF-8", false);
$pdf->SetTitle("SIMDONDAR - Barcode Label Auto");
$pdf->SetSubject("SIMDONDAR");
$pdf->SetAuthor("TIM SIMDONDAR");
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(0, 0, 0);

$udd = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT id, nama, daerah FROM utd WHERE aktif='1'"));
$namaudd = isset($udd["nama"]) ? $udd["nama"] : "";
$id_udd   = isset($udd["id"]) ? $udd["id"] : "";
$year     = date("y");
$prefix   = $id_udd . $year;

$current_no = 0;
$sql_last = "SELECT `noKantong`,
                    SUBSTRING(`noKantong`, 7, CHAR_LENGTH(`noKantong`) - 6 - 1) AS `no_hexa`,
                    CAST(CONV(SUBSTRING(`noKantong`, 7, CHAR_LENGTH(`noKantong`) - 6 - 1), 16, 10) AS UNSIGNED) AS `IntNomor`
             FROM `stokkantong`
             WHERE LEFT(`noKantong`, 6) = '" . mysqli_real_escape_string($dbi, $prefix) . "'
             ORDER BY `IntNomor` DESC
             LIMIT 1";
$cekktg = mysqli_query($dbi, $sql_last);
if (mysqli_num_rows($cekktg) > 0) {
    $curr_no_code = mysqli_fetch_assoc($cekktg);
    $current_no = (int) $curr_no_code["IntNomor"];
}

$ada_error = 0;

for ($set = 1; $set <= $v_jmlkantong; $set++) {
    $current_no++;
    $converted = strtoupper(dechex($current_no));
    $sufix = str_pad($converted, 5, "0", STR_PAD_LEFT);
    $nokantong0 = $prefix . $sufix;
    $nokantonga = $nokantong0 . "A";

    $sql_insert = "INSERT INTO `stokkantong`
                    (`noKantong`,`jenis`,`Status`,`tglTerima`,`volume`,`merk`,`volumeasal`,`kadaluwarsa_ktg`,`nolot_ktg`, `ident`, `user_barcode`, `metoda`)
                   VALUES
                    ('" . mysqli_real_escape_string($dbi, $nokantonga) . "',
                     '" . mysqli_real_escape_string($dbi, $v_jenisktg) . "',
                     '0',
                     '" . mysqli_real_escape_string($dbi, $v_tgl) . "',
                     '" . mysqli_real_escape_string($dbi, $v_volume) . "',
                     '" . mysqli_real_escape_string($dbi, $v_merk) . "',
                     '" . mysqli_real_escape_string($dbi, $v_volume) . "',
                     '" . mysqli_real_escape_string($dbi, $v_tgled) . "',
                     '" . mysqli_real_escape_string($dbi, $v_lot) . "',
                     'm',
                     '" . mysqli_real_escape_string($dbi, $v_user) . "',
                     '" . mysqli_real_escape_string($dbi, $v_metode) . "')";
    $tambah = mysqli_query($dbi, $sql_insert);

    if (!$tambah) {
        $ada_error = 1;
    } else {
        error_reporting(0);
        $log_mdl = strtoupper($v_usrlevel);
        $log_aksi = "Barcode kantong " . $v_merk . " : " . $nokantonga;

        $idp = mysqli_query($dbi, "SELECT `id`,`id1` FROM `tempat_donor` WHERE `active`='1'");
        $idp1 = mysqli_fetch_assoc($idp);
        $tempat = isset($idp1["id1"]) ? $idp1["id1"] : "";

        $client_ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
        $time_aksi = date("Y-m-d H:i:s");

        mysqli_query(
            $dbi,
            "INSERT INTO `user_log`
                (`time_aksi`,`komputer`, `user`, `modul`, `aksi_user`, `keterangan`, `tempat`)
             VALUES
                ('" . mysqli_real_escape_string($dbi, $time_aksi) . "',
                 '" . mysqli_real_escape_string($dbi, $client_ip) . "',
                 '" . mysqli_real_escape_string($dbi, $namauser) . "',
                 '" . mysqli_real_escape_string($dbi, $log_mdl) . "',
                 '" . mysqli_real_escape_string($dbi, $log_aksi) . "',
                 '',
                 '" . mysqli_real_escape_string($dbi, $tempat) . "')"
        );
    }

    for ($copy = 1; $copy <= $v_jmlcetak; $copy++) {
        renderBarcodePage($pdf, $namaudd, $kodejenis, $nokantonga, $v_tipebarcode, $add_x, $v_labelktg, $v_volume, $v_lot, $v_tgled);
    }

    if ((int) $v_jenisktg > 1) {
        for ($x = 1; $x <= ((int) $v_jenisktg - 1); $x++) {
            $letter = chr(64 + $x + 1); // B, C, D, ...
            $nokantongcetak = $nokantong0 . $letter;

            $sql_insert_extra = "INSERT INTO `stokkantong`
                (`noKantong`,`jenis`,`Status`,`tglTerima`,`volume`,`merk`,`volumeasal`,`kadaluwarsa_ktg`,`nolot_ktg`, `user_barcode`, `metoda`)
                VALUES
                ('" . mysqli_real_escape_string($dbi, $nokantongcetak) . "',
                 '" . mysqli_real_escape_string($dbi, $v_jenisktg) . "',
                 '0',
                 '" . mysqli_real_escape_string($dbi, $v_tgl) . "',
                 '" . mysqli_real_escape_string($dbi, $v_volume) . "',
                 '" . mysqli_real_escape_string($dbi, $v_merk) . "',
                 '" . mysqli_real_escape_string($dbi, $v_volume) . "',
                 '" . mysqli_real_escape_string($dbi, $v_tgled) . "',
                 '" . mysqli_real_escape_string($dbi, $v_lot) . "',
                 '" . mysqli_real_escape_string($dbi, $v_user) . "',
                 '" . mysqli_real_escape_string($dbi, $v_metode) . "')";
            $tambah = mysqli_query($dbi, $sql_insert_extra);

            if (!$tambah) {
                $ada_error = 1;
            }

            renderBarcodePage($pdf, $namaudd, $kodejenis, $nokantongcetak, $v_tipebarcode, $add_x, $v_labelktg, $v_volume, $v_lot, $v_tgled);
        }
    }

    if ($v_chkinfo == "1") {
        $standarlamabukakantong = 1;
        $masterkantong = mysqli_query($dbi, "SELECT `lama_buka` FROM `master_kantong` WHERE `merk`='" . mysqli_real_escape_string($dbi, $v_merk) . "' AND `jenis`='" . mysqli_real_escape_string($dbi, $v_jenisktg) . "'");
        if (mysqli_num_rows($masterkantong) > 0) {
            $row_master = mysqli_fetch_assoc($masterkantong);
            $standarlamabukakantong = isset($row_master["lama_buka"]) ? $row_master["lama_buka"] : 1;
        }

        if ($standarlamabukakantong === "" || $standarlamabukakantong == "0") {
            $standarlamabukakantong = 1;
        }

        $row_kantong = mysqli_fetch_assoc(
            mysqli_query($dbi, "SELECT `tglTerima` FROM `stokkantong` WHERE `noKantong`='" . mysqli_real_escape_string($dbi, $nokantonga) . "'")
        );
        $tanggalbuka = isset($row_kantong["tglTerima"]) ? $row_kantong["tglTerima"] : $v_tgl;
        $dtglbuka = date_create($tanggalbuka);
        if ($dtglbuka) {
            date_add($dtglbuka, date_interval_create_from_date_string($standarlamabukakantong . " days"));
            $edbuka = date_format($dtglbuka, "Y-m-d");
        } else {
            $edbuka = $v_edbuka;
        }

        renderInfoPage($pdf, $nokantonga, $namajenis, $tanggalbuka, $v_user, $v_merk, $v_volume, $standarlamabukakantong);
    }

    if ($v_pemisah == "1") {
        $pdf->AddPage();
    }
}

ob_clean();
$pdf->IncludeJS("print();");
$pdf->Output("barcode.pdf", "I");
