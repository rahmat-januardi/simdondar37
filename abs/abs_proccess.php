<?php
header("Content-Type: application/json");

session_start();

require_once("../config/dbi_connect.php");

$namauser = $_SESSION["namauser"];
$leveluser = $_SESSION["leveluser"];

$modul = addslashes(mysqli_escape_string($dbi, $_GET["mdl"]));

$output = array();

$status = 0;

function cekstatuskantong($status, $stattempat, $sah)
{
    $resulstatus = "";
    switch ($status) {
        case 0:
            $resulstatus = "Kosong";
            if ($stattempat == NULL) $resulstatus = "Kosong di logistik";
            if ($stattempat == "0") $resulstatus = "Kosong di Logistik";
            if ($stattempat == "1") $resulstatus = "Kosong di Aftap";
            break;
        case "1":
            if ($sah == "1") {
                $resulstatus = "Karantina";
            } else {
                $resulstatus = "Belum disahkan";
            }
            break;
        case "2":
            $resulstatus = "Sehat";
            break;
        case "3":
            $resulstatus = "Keluar";
            break;
        case "4":
            $resulstatus = "Rusak";
            break;
        case "5":
            $resulstatus = "Rusak-Gagal";
            break;
        case "6":
            $resulstatus = "Dimusnahkan";
            break;
        default:
            $resulstatus = "Musnah";
    }
    return $resulstatus;
}

function addlog($log_aksi)
{
    global $dbi;
    $idp = mysqli_query($dbi, "SELECT id, id1 from tempat_donor where active='1'");
    $idp1 = mysqli_fetch_assoc($idp);
    $tempat = $idp1["id1"];
    $log_mdl = strtoupper($_SESSION["leveluser"]);
    $namauser = $_SESSION["namauser"];
    $client_ip = $_SERVER["REMOTE_ADDR"];
    $time_aksi = date("Y-m-d H:i:s");
    mysqli_query($dbi, "INSERT INTO `user_log`(`time_aksi`, `komputer`, `user`, `modul`, `aksi_user`, `keterangan`, `tempat`) VALUES ('$time_aksi', '$client_ip', '$namauser', '$log_mdl', '$log_aksi', '', '$tempat')");
}

switch ($modul) {
    case md5("simpaninputdata_kgd"):
        $k_today = "K" . date("dmy") . "-";
        $query = "SELECT `NoKonfirmasi` FROM `dkonfirmasi` WHERE `NoKonfirmasi` LIKE '$k_today%' ORDER BY `NoKonfirmasi` DESC LIMIT 1";
        $idp = mysqli_query($dbi, $query);
        $idp2 = 0;
        if ($row = mysqli_fetch_assoc($idp)) {
            $idp2 = (int) substr($row["NoKonfirmasi"], 10, 3);
        }
        $notrans = $k_today . str_pad($idp2 + 1, 3, "0", STR_PAD_LEFT);
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        if (!$data) {
            echo json_encode(array("status" => 1, "message" => "Data input tidak valid"));
            exit();
        }
        parse_str($data["formData"], $formData);
        $tableData = json_decode($data["tableData"], true);
        if (!is_array($tableData) || empty($tableData)) {
            echo json_encode(array("status" => 1, "message" => "Data kantong darah tidak ditemukan"));
            exit();
        }
        $vtanggal = $formData["InpTanggalJam"];
        $vmetode = $formData["InpMetode"];
        $vnamareagen = $formData["Inpreagen"];
        $vAntiALot = $formData["nolota"];
        $vAntiAED = $formData["expa"];
        $vAntiBLot = $formData["nolotb"];
        $vAntiBED = $formData["expb"];
        $vAntiDLot = $formData["nolotd"];
        $vAntiDED = $formData["expd"];
        $vuser = $formData["InpUser"];
        $vchecker = $formData["InpChecker"];
        $vvalidator = $formData["InpValidator"];
        $output["datakantong"] = array();
        $detail_status = 0;
        foreach ($tableData as $row) {
            $d_no_kantong = $row["no_kantong"];
            $d_pendonor = $row["pendonor"];

            $d_gol = $row["gol_kgd"];
            $d_rh = $row["rh_kgd"];

            $gol_awal_abo = $row["gol_awal"];
            $gol_awal_rh = $row["rh_awal"];

            $kesesuaian = ($gol_awal_abo === $d_gol && $gol_awal_rh === $d_rh) ? "0" : "1";
            $anti_a = $row["anti_a"];
            $anti_b = $row["anti_b"];
            $tcell_a = $row["tcell_a"];
            $tcell_b = $row["tcell_b"];
            $tcell_o = $row["tcell_o"];
            $autoctrl = $row["autoctrl"];
            $anti_d = $row["anti_d"];
            $bovalbumin = $row["bovalbumin"];

            if ($autoctrl === "Neg") {
                $autoctrl = "1";
            } else {
                $autoctrl = "0";
            }

            if ($bovalbumin === "Neg") {
                $bovalbumin = "1";
            } else {
                $bovalbumin = "0";
            }

            $output["datakantong"][] = array(
                "kantong" => $row["no_kantong"],
                "gol_awal_abo" => $gol_awal_abo,
                "gol_awal_rh" => $gol_awal_rh,
                "gol_kgd" => $d_gol,
                "rh_kgd" => $d_rh,
                "kesesuaian" => $kesesuaian,
            );
            $sqlkgd = "INSERT INTO `dkonfirmasi`(`NoKonfirmasi`, `NoKantong`, `kode_donor`, `kode_pendonor`, `idsample`, `GolDarah`, `Rhesus`, `ket`, `Cocok`, `tgl`, `checker`, `petugas`, `operator`, `pengesah`, `goldarah_asal`, `rhesus_asal`, 
                        `metode`, `sel`, `antiA`, `antiB`, `antiO`, `serum`, `tA`, `tB`, `tsO`, `antiD`, `ac`, `ba`, `nolot_aa`, `expa`, `nolot_ab`, `expb`, `nolot_ad`, `expd`) 
                        VALUES ('$notrans', '$d_no_kantong', '$d_pendonor', '$d_pendonor', '$d_no_kantong', '$d_gol', '$d_rh', '$vnamareagen', '$kesesuaian', '$vtanggal', '$vchecker', '$vuser', '$vuser', '$vvalidator', '$gol_awal_abo', '$gol_awal_rh', 
                        '$vmetode', '1', '$anti_a', '$anti_b', '-', '1', '$tcell_a', '$tcell_b', '$tcell_o', '$anti_d', '$autoctrl', '$bovalbumin', '$vAntiALot', '$vAntiAED', '$vAntiBLot', '$vAntiBED', '$vAntiDLot', '$vAntiDED')";
            $querydkonfirmasi = mysqli_query($dbi, $sqlkgd);
            if (!$querydkonfirmasi) {
                $detail_status++;
                $error_message = mysqli_error($dbi);
                file_put_contents('debug.log', "Error for $d_no_kantong: $error_message\n", FILE_APPEND);
            }

            // $prefix_no_kantong = preg_replace("/[A-Z]+$/", "", $d_no_kantong);
            // $sl_updkantong = "UPDATE `stokkantong` SET `gol_darah`='$d_gol', `RhesusDrh`='$d_rh', `statKonfirmasi`='1' WHERE `noKantong` REGEXP '^" . $prefix_no_kantong . "[A-Z]$'";
            // $qryupd_kantong = mysqli_query($dbi, $sl_updkantong);
            // if (!$qryupd_kantong) {
            //     file_put_contents('debug.log', "Error updating stokkantong: " . mysqli_error($dbi) . "\n", FILE_APPEND);
            // }

            // Ambil semua kecuali 1 karakter terakhir
            $prefix = substr($d_no_kantong, 0, -1);

            // Update semua kantong yang memiliki prefix sama (kecuali huruf terakhir)
            $sl_updkantong = "UPDATE `stokkantong` 
                  SET `gol_darah` = ?, 
                      `RhesusDrh` = ?, 
                      `statKonfirmasi` = '1' 
                  WHERE SUBSTRING(noKantong, 1, LENGTH(noKantong) - 1) = ?";

            $stmt = $dbi->prepare($sl_updkantong);
            $stmt->bind_param("sss", $d_gol, $d_rh, $prefix);
            $qryupd_kantong = $stmt->execute();

            if (!$qryupd_kantong) {
                file_put_contents('debug.log', "Error updating stokkantong: " . $stmt->error . "\n", FILE_APPEND);
            }

            $sql_updpendonor = "UPDATE `pendonor` SET `GolDarah`='$d_gol', `Rhesus`='$d_rh' WHERE `Kode`='$d_pendonor'";
            $qryupddonor = mysqli_query($dbi, $sql_updpendonor);
            if (!$qryupddonor) {
                file_put_contents('debug.log', "Error updating pendonor: " . mysqli_error($dbi) . "\n", FILE_APPEND);
            }

            $prefix_no_kantong = preg_replace("/[A-Z]+$/", "", $d_no_kantong);
            $sl_pengolahan = "UPDATE `dpengolahan` SET `goldarah`='$d_gol', `rhesus`='$d_rh' WHERE `noKantong` REGEXP '^" . $prefix_no_kantong . "[A-Z]$'";
            $qryupd_pengolahan = mysqli_query($dbi, $sl_pengolahan);
            if (!$qryupd_pengolahan) {
                file_put_contents('debug.log', "Error updating dpengolahan: " . mysqli_error($dbi) . "\n", FILE_APPEND);
            }

            $log_aksi = "KGD: " . $d_no_kantong . ", transaksi:" . $notrans . ", Gol Awal: " . $gol_awal_abo . $gol_awal_rh . ", hasil: " . $d_gol . $d_rh;
            addlog($log_aksi);
        }
        if ($detail_status == 0) {
            echo json_encode(array("status" => 0, "message" => "Data Pemeriksaan Konfirmasi Golongan Darah sudah tersimpan dengan nomor transaksi " . $notrans));
        } else {
            echo json_encode(array("status" => 1, "message" => "Sejumlah " . $detail_status . " data tidak dapat disimpan"));
        }
        break;

    case md5("cekkantong_kgd"):
        $v_kantong = $_POST["kantong"];
        $nomor_kantong_utama = substr($v_kantong, 0, -1) . "A";
        $query = mysqli_query($dbi, "SELECT `noKantong`, `gol_darah`, `RhesusDrh`, `produk`, `kodePendonor`  
                                     FROM `stokkantong` 
                                     WHERE `noKantong`='$nomor_kantong_utama' 
                                     AND `sah`='1' AND `Status` > 0");
        $arr_kantong = array();
        if ($query && mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            $status = "success";
            $message = "Kantong ditemukan";
            $arr_kantong = array(
                "nokantong" => $row["noKantong"],
                "gol" => $row["gol_darah"],
                "rh" => $row["RhesusDrh"],
                "produk" => $row["produk"],
                "pendonor" => $row["kodePendonor"]
            );
        } else {
            $status = "error";
            $message = "Kantong TIDAK ditemukan";
        }
        echo json_encode(array("status" => $status, "message" => $message, "kantong" => $arr_kantong));
        break;

    case md5("simpaninputdata"):
        $k_today = "ABS" . date("dmy") . "-";
        $query = "SELECT `abs_notrans` FROM `abs` WHERE `abs_notrans` LIKE '$k_today%' ORDER BY `abs_notrans` DESC LIMIT 1";
        $idp = mysqli_query($dbi, $query);
        $idp2 = 0;
        if ($row = mysqli_fetch_assoc($idp)) {
            $idp2 = (int) substr($row["abs_notrans"], 10, 3);
        }
        $notrans = $k_today . str_pad($idp2 + 1, 3, "0", STR_PAD_LEFT);
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        if (!is_array($data)) {
            echo json_encode(array("status" => 1, "message" => "Tidak ada data yang diterima"));
            exit();
        }
        $formData = $data["formData"];
        $output["status"] = 0;
        $vtanggal = $formData[0]["value"];
        $vmetode = $formData[1]["value"];
        $vreagen = $formData[2]["value"];
        $vigglot = $formData[3]["value"];
        $vigged = $formData[4]["value"];
        $vcell1 = $formData[5]["value"];
        $vcell1ed = $formData[6]["value"];
        $vcell2 = $formData[7]["value"];
        $vcell2ed = $formData[8]["value"];
        $vuser = $formData[10]["value"];
        $vchecker = $formData[11]["value"];
        $vvalidator = $formData[12]["value"];
        $tableData = isset($data["tableData"]) ? $data["tableData"] : array();
        $output["datakantong"] = array();
        $dstatus_gell = 0;
        $dstatusabs = 0;
        foreach ($tableData as $row) {
            $d_no_kantong = $row["no_kantong"];
            $d_pendonor = $row["pendonor"];
            $d_gol = $row["gol"];
            $d_rh = $row["rh"];
            $d_hasil = $row["hasil"];
            $d_status = 0;
            preg_match("/\((\d+)\)/", $row["status"], $matches);
            $d_status = $matches[1];
            $d_metodemerger = $vmetode . " " . $vreagen;
            $sqlabs = "INSERT INTO `abs`(`abs_notrans`, `abs_tgl`, `abs_sample_id`, `abs_id_donor`, `abs_metode`, `abs_ref_id`, `abs_result`, `abs_kantong_status`, `abs_action`, `abs_checker`, `abs_user`, `abs_supervisor`) 
                     VALUES ('$notrans', '$vtanggal', '$d_no_kantong', '$d_pendonor', '$d_metodemerger', '0', '$d_hasil', '$d_status', '0', '$vchecker', '$vuser', '$vvalidator')";
            $sqlgell = "INSERT INTO `abs_gell`(`absg_trans`, `absg_tgl`, `absg_kantong`, `absg_sampleid`, `absg_donorid`, `absg_golda`, `absg_rh`, `absg_igg_lot`, `absg_igg_ed`, `absg_cell1_lot`, `absg_cell1_ed`, `absg_cell2_lot`, `absg_cell2_ed`, `absg_result`, `absg_cell_reac`, `absg_cell2_reac`, `absg_pemeriksa`, `absg_checker`, `absg_approve`, `absg_ket`) 
                   VALUES ('$notrans', '$vtanggal', '$d_no_kantong', '$d_no_kantong', '$d_pendonor', '$d_gol', '$d_rh', '$vigglot', '$vigged', '$vcell1', '$vcell1ed', '$vcell2', '$vcell2ed', '$d_hasil', '', '', '$vuser', '$vchecker', '$vvalidator', '$d_metodemerger')";
            $query_abs = mysqli_query($dbi, $sqlabs);
            if ($query_abs) {
                $log_aksi = "Antibody Screening : " . $d_metodemerger . ", No.Transaksi: " . $notrans . "; Kantong No:" . $d_no_kantong . ", hasil : " . $d_hasil;
                addlog($log_aksi);
                $prefix_no_kantong = preg_replace("/[A-Z]+$/", "", $d_no_kantong);
                $sl_updkantong = "UPDATE `stokkantong` SET `abs`='$d_hasil', `tgl_abs`='$vtanggal' WHERE `noKantong` REGEXP '^" . $prefix_no_kantong . "[A-Z]$'";
                $qryupd_kantong = mysqli_query($dbi, $sl_updkantong);
                $dstatusabs = 0;
                $query_gell = mysqli_query($dbi, $sqlgell);
                if ($query_gell) {
                    $dstatus_gell = 0;
                } else {
                    $dstatus_gell = 1;
                }
            } else {
                $dstatusabs = 1;
            }
            $output["datakantong"][] = array(
                "kantong" => $row["no_kantong"],
                "statusabs" => $dstatusabs,
                "statusgell" => $dstatus_gell,
                "sqlabd" => $sqlabs,
                "sqlgell" => $sqlgell
            );
        }
        if ($dstatus_gell == 0 && $dstatusabs == 0) {
            $status = 0;
            $message = "Data Pemeriksaan Antibody Screening sudah tersimpan dengan nomor transaksi " . $notrans;
        } else {
            $status = 1;
            $message = "Gagal dalam penyimpanan pemeriksaan Antinody Screening";
        }
        echo json_encode(array("status" => $status, "message" => $message));
        break;

    case md5("cekkantong"):
        $arrdata = array();
        $v_kantong = $_POST["kantong"];
        $nomor_kantong_utama = substr($v_kantong, 0, -1) . "A";
        $query = "SELECT `noKantong`, `Status`, `produk`, `sah`, `StatTempat`, `gol_darah`, `RhesusDrh`, `kodePendonor`, `tgl_Aftap`, `abs`, `tgl_abs` 
                  FROM `stokkantong` 
                  WHERE `noKantong` = '$nomor_kantong_utama' AND `Status` > 0";
        $result = mysqli_query($dbi, $query);
        if (!$result) {
            echo json_encode(array("status" => 1, "message" => "Query Error"));
            exit();
        }
        if (mysqli_num_rows($result) > 0) {
            $dt = mysqli_fetch_assoc($result);
            echo json_encode(array(
                "status" => 0,
                "data" => array(
                    "no_kantong" => $dt["noKantong"],
                    "status" => $dt["Status"],
                    "statusstr" => cekstatuskantong($dt["Status"], $dt["StatTempat"], $dt["sah"]),
                    "pendonor" => $dt["kodePendonor"],
                    "gol" => $dt["gol_darah"],
                    "rh" => $dt["RhesusDrh"],
                    "hasil" => $dt["sah"]
                )
            ));
        } else {
            echo json_encode(array("status" => 1, "message" => "Data tidak ditemukan"));
        }
        exit();

    default:
        echo json_encode(array("status" => 1, "message" => "No Action"));
        exit();
}
