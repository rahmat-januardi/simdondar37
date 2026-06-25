<?php
require_once("clogin.php");
require_once("config/dbi_connect.php");

$namauser = $_SESSION["namauser"];
$namalengkap = $_SESSION["nama_lengkap"];
$leveluser = $_SESSION["level"];

$f_tanggal1 = date("Y-m-d");
$f_tanggal2 = date("Y-m-d");
$f_status   = "";

if (isset($_POST["vfilter"])) {
    $f_tanggal1 = $_POST["MdlTanggal1"];
    $f_tanggal2 = $_POST["MdlTanggal2"];
    if ($f_tanggal2 == "") {
        $f_tanggal2 = date("Y-m-d");
    }
    $f_status = $_POST["HasilPeriksa"];
}

if (isset($_POST["vreset"])) {
    $f_tanggal1 = date("Y-m-d");
    $f_tanggal2 = date("Y-m-d");
    $f_status   = "";
}

?>
<link rel="stylesheet" href="bootsrap337/bspmi.css">
<link rel="stylesheet" href="bootsrap337/w3.css">
<link rel="stylesheet" href="abs/abs.css">
<link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
<link href="bootsrap337/datepicker/css/bootstrap-datepicker.css" rel="stylesheet">
<link rel="stylesheet" href="bootsrap337/chosen/chosen.css">

<style>
.swal2-popup {
    font-size: 15px !important;
}

.form-group {
    margin-top: 1px;
    margin-bottom: 1px;
}

table {
    white-space: nowrap;
}

.table thead th {
    padding: 2px !important;
    text-align: center;
    vertical-align: middle !important;
    text-shadow: 1px 1px 2px black;
    min-height: 30px;
}

.table tbody th {
    white-space: nowrap;
    vertical-align: middle !important;
}

a {
    text-decoration: none !important;
}

.table-condensed {
    font-size: 12px;
}

.modal-fullscreen {
    width: 90vw;
    padding: 0;
    height: 90vh;
}
</style>

<body style="font-size:13px;">
    <div id="loading"></div>
    <div class="container-fluid" style="margin: 25px;">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel w3-border-theme shadow">
                    <div class="panel-heading w3-theme-d5 clearfix">
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 text-left text-shadow visible-xs"
                            style="font-size: 150%;font-weight: bold;">KGD</div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 text-left text-shadow hidden-xs"
                            style="font-size: 150%;font-weight: bold;">Pemeriksaan Konfirmasi Golongan Darah</div>
                        <div class="col-lg-5 col-md-4 col-sm-5 col-xs-5 text-right">
                            <a href="#" class="w3-btn w3-theme-l2 w3-hover-yellow" data-toggle="modal"
                                data-target="#mFilter"><i class="glyphicon glyphicon-filter"></i> Filter</a>
                            <a href="?module=konfirmasi_gol_darah"
                                class="w3-btn w3-theme-l4 w3-hover-yellow hidden-xs"><i
                                    class="glyphicon glyphicon-plus"></i> Input</a>
                            <a href="#" onclick="exportToExcel()"
                                class="w3-btn w3-theme-l4 w3-hover-yellow hidden-xs"><i
                                    class="glyphicon glyphicon-share-alt"></i> Export</a>
                        </div>
                    </div>

                    <div class="panel-body" style="height: 85vh;overflow:auto;">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover table-condensed" id="idtable">
                                    <thead>
                                        <tr class="w3-theme-d5">
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Tanggal</th>
                                            <th rowspan="2">No Konfirmasi</th>
                                            <th rowspan="2">No Kantong</th>
                                            <th rowspan="2">Gol(Rh)<br>Darah Asal</th>
                                            <th rowspan="2">Gol(Rh)<br>Darah Baru</th>
                                            <th rowspan="2">Hasil</th>
                                            <th rowspan="2">Metode</th>
                                            <th colspan="3">Anti A</th>
                                            <th colspan="3">Anti B</th>
                                            <th colspan="3">Anti D</th>
                                            <th rowspan="2">Tes Sel<br>A</th>
                                            <th rowspan="2">Tes Sel<br>B</th>
                                            <th rowspan="2">Tes Sel<br>O</th>
                                            <th rowspan="2">Auto<br>Control</th>
                                            <th rowspan="2">Bovine<br>Albumin</th>
                                            <th rowspan="2">Petugas</th>
                                            <th rowspan="2">Checker</th>
                                            <th rowspan="2">Validator</th>
                                        </tr>
                                        <tr class="w3-theme-d5">
                                            <th>Nilai</th>
                                            <th>No Lot</th>
                                            <th>ED.</th>
                                            <th>Nilai</th>
                                            <th>No Lot</th>
                                            <th>ED.</th>
                                            <th>Nilai</th>
                                            <th>No Lot</th>
                                            <th>ED.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $wstatus = ($f_status == "") ? "" : " AND (`Cocok`='$f_status')";

                                        $sql_abd = "SELECT `id`, `NoKonfirmasi`, `NoKantong`, `kode_donor`, `idsample`, `GolDarah`, `Rhesus`, `ket`, `Cocok`,
                   CASE WHEN `Cocok`='0' THEN 'Cocok' ELSE 'Tidak Cocok' END AS `CocokStr`,
                   `tgl`, DATE(`tgl`) as tanggal, `petugas`, `operator`, `goldarah_asal`, `rhesus_asal`, `metode`,
                   `sel`, `antiA`, `antiB`, `antiO`, `serum`, `tA`, `tB`,
                   `tsO`, `antiD`,
                   CASE WHEN `ac`='1' THEN 'Neg' ELSE 'Pos' END AS `ac`,
                   CASE WHEN `ba`='1' THEN 'Neg' ELSE 'Pos' END AS `ba`,
                   `nolot_aa`, `expa`, `nolot_ab`,
                   `expb`, `nolot_ad`, `expd`,
                   `kode_pendonor`, `checker`, `pengesah`, `up_data`, `insert_on`
            FROM `dkonfirmasi`
            WHERE (DATE(`tgl`) BETWEEN '$f_tanggal1' AND '$f_tanggal2') " . $wstatus;

                                        $qryabd = mysql_query($sql_abd);

                                        if (!$qryabd) {
                                            echo '<tr><td colspan="25" class="text-center">Query gagal: ' . mysql_error() . '</td></tr>';
                                        } else {

                                            $rekap_goldarah = array(
                                                'A'  => 0,
                                                'B'  => 0,
                                                'O'  => 0,
                                                'AB' => 0
                                            );

                                            $rekap_rhesus = array(
                                                'positif' => 0,
                                                'negatif' => 0
                                            );

                                            $rekap_keterangan = array(
                                                'cocok' => 0,
                                                'tidak_cocok' => 0
                                            );

                                            $total_pemeriksaan = 0;

                                            while ($row = mysql_fetch_assoc($qryabd)) {
                                                $no++;
                                                $warna = ($row["Cocok"] == "0") ? "" : "w3-red";

                                                $gd = strtoupper(trim($row["GolDarah"]));
                                                if (isset($rekap_goldarah[$gd])) {
                                                    $rekap_goldarah[$gd]++;
                                                }

                                                $rh = strtoupper(trim($row["Rhesus"]));
                                                if ($rh == "+" || $rh == "POS" || $rh == "POSITIF") {
                                                    $rekap_rhesus["positif"]++;
                                                } elseif ($rh == "-" || $rh == "NEG" || $rh == "NEGATIF") {
                                                    $rekap_rhesus["negatif"]++;
                                                } else {
                                                    if (strpos($rh, "+") !== false || strpos($rh, "POS") !== false) {
                                                        $rekap_rhesus["positif"]++;
                                                    } else {
                                                        $rekap_rhesus["negatif"]++;
                                                    }
                                                }

                                                if ($row["Cocok"] == "0") {
                                                    $rekap_keterangan["cocok"]++;
                                                } else {
                                                    $rekap_keterangan["tidak_cocok"]++;
                                                }

                                                $total_pemeriksaan++;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td class="text-left"><?php echo $row["tanggal"]; ?></td>
                                            <td class="text-left"><?php echo $row["NoKonfirmasi"]; ?></td>
                                            <td class="text-left"><?php echo $row["NoKantong"]; ?></td>
                                            <td class="text-center">
                                                <?php echo $row["goldarah_asal"] . $row["rhesus_asal"]; ?></td>
                                            <td class="text-center"><?php echo $row["GolDarah"] . $row["Rhesus"]; ?>
                                            </td>
                                            <td class="text-center <?php echo $warna; ?>">
                                                <?php echo $row["CocokStr"]; ?></td>
                                            <td class="text-center"><?php echo $row["metode"]; ?></td>

                                            <td class="text-center"><?php echo $row["antiA"]; ?></td>
                                            <td class="text-center"><?php echo $row["nolot_aa"]; ?></td>
                                            <td class="text-center"><?php echo $row["expa"]; ?></td>

                                            <td class="text-center"><?php echo $row["antiB"]; ?></td>
                                            <td class="text-center"><?php echo $row["nolot_ab"]; ?></td>
                                            <td class="text-center"><?php echo $row["expb"]; ?></td>

                                            <td class="text-center"><?php echo $row["antiD"]; ?></td>
                                            <td class="text-center"><?php echo $row["nolot_ad"]; ?></td>
                                            <td class="text-center"><?php echo $row["expd"]; ?></td>

                                            <td class="text-center"><?php echo $row["tA"]; ?></td>
                                            <td class="text-center"><?php echo $row["tB"]; ?></td>
                                            <td class="text-center"><?php echo $row["tsO"]; ?></td>
                                            <td class="text-center"><?php echo $row["ac"]; ?></td>
                                            <td class="text-center"><?php echo $row["ba"]; ?></td>

                                            <td class="text-center"><?php echo $row["petugas"]; ?></td>
                                            <td class="text-center"><?php echo $row["checker"]; ?></td>
                                            <td class="text-center"><?php echo $row["pengesah"]; ?></td>
                                        </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <br>

                                <table class="table table-bordered table-condensed" id="idrekap">
                                    <thead>
                                        <tr class="w3-theme-d5">
                                            <th colspan="4">Golongan Darah</th>
                                            <th colspan="2">Rhesus</th>
                                            <th colspan="2">Keterangan</th>
                                            <th rowspan="2">Total Pemeriksaan</th>
                                        </tr>
                                        <tr class="w3-theme-d5">
                                            <th>A</th>
                                            <th>B</th>
                                            <th>O</th>
                                            <th>AB</th>
                                            <th>Positif</th>
                                            <th>Negatif</th>
                                            <th>Cocok</th>
                                            <th>Tidak Cocok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"><?php echo $rekap_goldarah["A"]; ?></td>
                                            <td class="text-center"><?php echo $rekap_goldarah["B"]; ?></td>
                                            <td class="text-center"><?php echo $rekap_goldarah["O"]; ?></td>
                                            <td class="text-center"><?php echo $rekap_goldarah["AB"]; ?></td>
                                            <td class="text-center"><?php echo $rekap_rhesus["positif"]; ?></td>
                                            <td class="text-center"><?php echo $rekap_rhesus["negatif"]; ?></td>
                                            <td class="text-center"><?php echo $rekap_keterangan["cocok"]; ?></td>
                                            <td class="text-center"><?php echo $rekap_keterangan["tidak_cocok"]; ?></td>
                                            <td class="text-center"><b><?php echo $total_pemeriksaan; ?></b></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mFilter" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form name="mFrmFilter" class="form-horizontal" id="mFrmFilter" action="" method="POST">
                    <div class="modal-header w3-theme-d4 shadow">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" style="color:white;">Filter Data</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="control-label col-md-3" for="MdlTanggal1">Tanggal Aftap</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control startdate" value="<?php echo $f_tanggal1; ?>"
                                        name="MdlTanggal1" id="MdlTanggal1" />
                                    <span class="input-group-addon input-sm">s/d</span>
                                    <input type="text" class="form-control enddate" value="<?php echo $f_tanggal2; ?>"
                                        name="MdlTanggal2" id="MdlTanggal2" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3" for="MdlStatus">Hasil</label>
                            <div class="col-md-9">
                                <select name="HasilPeriksa" id="inputHasilPeriksa" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="0" <?php echo ($f_status === "0") ? "selected" : ""; ?>>Cocok
                                    </option>
                                    <option value="1" <?php echo ($f_status === "1") ? "selected" : ""; ?>>Tidak Cocok
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="vfilter" id="vfilter"
                            class="w3-btn w3-theme-d5 w3-hover-yellow w3-card">OK</button>
                        <button type="submit" name="vreset" id="vreset"
                            class="w3-btn w3-theme-d4 w3-hover-yellow w3-card">Reset</button>
                        <button class="w3-btn w3-theme-d3 w3-hover-yellow w3-card" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

<script src="bootsrap337/js/jquery.min.js"></script>
<script src="bootsrap337/js/bootstrap.min.js"></script>
<script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="bootsrap337/datepicker/custom.js"></script>
<script src="bootsrap337/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="bootsrap337/sweetalert2/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    setDatePicker();
    setDateRangePicker(".startdate", ".enddate");
    setMonthPicker();
    setYearPicker();
    setYearRangePicker(".startyear", ".endyear");
});

function exportToExcel() {
    var table1 = document.getElementById("idtable").outerHTML;
    var table2 = document.getElementById("idrekap").outerHTML;

    var style = '' +
        '<style>' +
        'body{font-family:Arial;font-size:12px;}' +
        'h3{margin:0 0 10px 0;padding:0;}' +
        'table{border-collapse:collapse;width:100%;}' +
        'table, th, td{border:1px solid black;}' +
        'th, td{padding:5px;text-align:center;vertical-align:middle;}' +
        '.text-left{text-align:left;}' +
        '.text-center{text-align:center;}' +
        '</style>';

    var html = '' +
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" ' +
        'xmlns:x="urn:schemas-microsoft-com:office:excel" ' +
        'xmlns="http://www.w3.org/TR/REC-html40">' +
        '<head>' +
        '<meta charset="UTF-8">' +
        style +
        '</head>' +
        '<body>' +
        '<h3>Pemeriksaan Konfirmasi Golongan Darah</h3>' +
        table1 +
        '<br><br>' +
        '<h3>Jumlah Pemeriksaan</h3>' +
        table2 +
        '</body></html>';

    var today = new Date();
    var yyyy = today.getFullYear();
    var mm = String(today.getMonth() + 1).padStart(2, "0");
    var dd = String(today.getDate()).padStart(2, "0");
    var formattedDate = yyyy + mm + dd;
    var filename = "PemeriksaanKGD_" + formattedDate + ".xls";

    var blob = new Blob(["\ufeff" + html], {
        type: "application/vnd.ms-excel"
    });
    var link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}

$(".chosen-select").chosen({
    width: "100%"
});
var load = document.getElementById("loading");
window.addEventListener("load", function() {
    load.style.display = "none";
});
</script>