<?php
require_once("clogin.php");
require_once("config/dbi_connect.php");

$namauser = $_SESSION["namauser"];
$namalengkap = $_SESSION["nama_lengkap"];
$leveluser = $_SESSION["level"];

$f_tanggal1 = date("Y-m-d");
$f_tanggal2 = date("Y-m-d");
$f_status = "";

if (isset($_POST["vfilter"])) {
    $f_tanggal1 = $_POST["MdlTanggal1"];
    $f_tanggal2 = $_POST["MdlTanggal2"];
    if ($f_tanggal2 == "") {
        $f_tanggal2 = date("Y-m-d");
    }
    $f_status = $_POST["HasilPeriksa"];
}

echo '<link rel="stylesheet" href="bootsrap337/bspmi.css">
<link rel="stylesheet" href="bootsrap337/w3.css">
<link rel="stylesheet" href="abs/abs.css">
<link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
<link href="bootsrap337/datepicker/css/bootstrap-datepicker.css" rel="stylesheet">
<link rel="stylesheet" href="bootsrap337/chosen/chosen.css">
<style>
    .swal2-popup {font-size: 15px !important;}
    .form-group{margin-top: 1px;margin-bottom: 1px;}
    table{
        white-space: nowrap;
    }
    .table thead th {
        padding: 2px !important;
        text-align: center;
        vertical-align: middle !important;
        text-shadow: 1px 1px  2px black;
        min-height: 30px;
    }
    .table tbody th {
        white-space: nowrap;
        vertical-align: middle !important;
    }
    a{text-decoration: none !important;}
    .table-condensed{font-size: 12px;}
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
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 text-left text-shadow visible-xs" style="font-size: 150%;font-weight: bold;">KGD</div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 text-left text-shadow hidden-xs" style="font-size: 150%;font-weight: bold;">Pemeriksaan Konfirmasi Golongan Darah</div>
                        <div class="col-lg-5 col-md-4 col-sm-5 col-xs-5 text-right">
                            <a href="#" class="w3-btn w3-theme-l2 w3-hover-yellow" data-toggle="modal" data-target="#mFilter"><i class="glyphicon glyphicon-filter"></i> Filter</a>
                            <a href="?module=konfirmasi_gol_darah" class="w3-btn w3-theme-l4 w3-hover-yellow hidden-xs"><i class="glyphicon glyphicon-plus"></i> Input</a>
                            <a href="#" onclick="exportToExcel()" class="w3-btn w3-theme-l4 w3-hover-yellow hidden-xs"><i class="glyphicon glyphicon-share-alt"></i> Export</a>
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
                                            <th>Nolot</th>
                                            <th>ED.</th>
                                            <th>Nilai</th>
                                            <th>Nolot</th>
                                            <th>ED.</th>
                                            <th>Nilai</th>
                                            <th>Nolot</th>
                                            <th>ED.</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

$no = 0;
$wstatus = ($f_status == "") ? "" : " AND (`Cocok`='$f_status')";
$sql_abd = "SELECT `id`, `NoKonfirmasi`, `NoKantong`, `kode_donor`, `idsample`, `GolDarah`,`Rhesus`, `ket`, `Cocok`,
            CASE WHEN `Cocok`='0' THEN 'Cocok' ELSE 'Tidak Cocok' END AS `CocokStr`,
            `tgl`, date(`tgl`) as tanggal, `petugas`, `operator`, `goldarah_asal`, `rhesus_asal`, `metode`,
            `sel`, `antiA`, `antiB`, `antiO`, `serum`, `tA`, `tB`,
            `tsO`, `antiD`, 
            CASE WHEN `ac`='1' THEN 'Neg' ELSE 'Pos' END AS `ac`, 
            CASE WHEN `ba`='1' THEN 'Neg' ELSE 'Pos' END AS `ba`, 
            `nolot_aa`, `expa`, `nolot_ab`,
            `expb`, `nolot_ad`, `expd`,
            `kode_pendonor`, `checker`, `pengesah`, `up_data`, `insert_on`
            FROM `dkonfirmasi`
            WHERE (DATE(`tgl`) BETWEEN '$f_tanggal1' AND '$f_tanggal2') " . $wstatus;

$qryabd = mysqli_query($dbi, $sql_abd);
while ($row = mysqli_fetch_assoc($qryabd)) {
    $no++;
    $warna = ($row["Cocok"] == "0") ? "" : "w3-red";
    echo '<tr>
        <td class="text-center">' . $no . '</td>
        <td class="text-left">' . $row["tanggal"] . '</td>
        <td class="text-left">' . $row["NoKonfirmasi"] . '</td>
        <td class="text-left">' . $row["NoKantong"] . '</td>
        <td class="text-center">' . $row["goldarah_asal"] . $row["rhesus_asal"] . '</td>
        <td class="text-center">' . $row["GolDarah"] . $row["Rhesus"] . '</td>
        <td class="text-center ' . $warna . '">' . $row["CocokStr"] . '</td>
        <td class="text-center">' . $row["metode"] . '</td>

        <td class="text-center">' . $row["antiA"] . '</td>
        <td class="text-center">' . $row["nolot_aa"] . '</td>
        <td class="text-center">' . $row["expa"] . '</td>

        <td class="text-center">' . $row["antiB"] . '</td>
        <td class="text-center">' . $row["nolot_ab"] . '</td>
        <td class="text-center">' . $row["expb"] . '</td>

        <td class="text-center">' . $row["antiD"] . '</td>
        <td class="text-center">' . $row["nolot_ad"] . '</td>
        <td class="text-center">' . $row["expd"] . '</td>

        <td class="text-center">' . $row["tA"] . '</td>
        <td class="text-center">' . $row["tB"] . '</td>
        <td class="text-center">' . $row["tsO"] . '</td>
        <td class="text-center">' . $row["ac"] . '</td>
        <td class="text-center">' . $row["ba"] . '</td>

        <td class="text-center">' . $row["petugas"] . '</td>
        <td class="text-center">' . $row["checker"] . '</td>
        <td class="text-center">' . $row["pengesah"] . '</td>
    </tr>';
}
echo '                                    </tbody>
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
                                    <input type="text" class="form-control startdate" value="' . $f_tanggal1 . '" name="MdlTanggal1" id="MdlTanggal1"/>
                                    <span class="input-group-addon input-sm">s/d</span>
                                    <input type="text" class="form-control enddate" value="' . $f_tanggal2 . '" name="MdlTanggal2" id="MdlTanggal2"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3" for="MdlStatus">Hasil</label>
                            <div class="col-md-9">
                                <select name="HasilPeriksa" id="inputHasilPeriksa" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="0">Cocok</option>
                                    <option value="1">Tidak Cocok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="vfilter" id="vfilter" class="w3-btn w3-theme-d5 w3-hover-yellow w3-card">OK</button>
                        <button type="submit" name="vreset" id="vreset" class="w3-btn w3-theme-d4 w3-hover-yellow w3-card">Reset</button>
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
    $(document).ready(function(){
        setDatePicker()
        setDateRangePicker(".startdate", ".enddate")
        setMonthPicker()
        setYearPicker()
        setYearRangePicker(".startyear", ".endyear");        
    });

    function exportToExcel() {
        let table = document.querySelector("table").outerHTML;
        let style = `
            <style>
                table, th, td { border: 1px solid black; border-collapse: collapse; }
                th, td { padding: 5px; text-align: left; }
            </style>
        `;
        let excelContent = style + table;
        let today = new Date();
        let yyyy = today.getFullYear();
        let mm = String(today.getMonth() + 1).padStart(2, "0");
        let dd = String(today.getDate()).padStart(2, "0");
        let formattedDate = yyyy + mm + dd;
        let filename = `PemeriksaanKGD_${formattedDate}.xls`;
        let blob = new Blob(["\ufeff" + excelContent], { type: "application/vnd.ms-excel" });
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }

    $(".chosen-select").chosen({width: "100%"});
    var load = document.getElementById("loading");window.addEventListener("load", function(){load.style.display = "none";});
</script>';
