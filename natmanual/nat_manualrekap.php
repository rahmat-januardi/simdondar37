<?php
require_once("clogin.php");
require_once("config/dbi_connect.php");

$namauser = $_SESSION["namauser"];
$namalengkap = $_SESSION["nama_lengkap"];
$leveluser = $_SESSION["level"];

$f_tanggal1 = date("Y-m-d");
$f_tanggal2 = date("Y-m-d");

if (isset($_POST["vfilter"])) {
    $f_tanggal1 = $_POST["MdlTanggal1"];
    $f_tanggal2 = $_POST["MdlTanggal2"] ?: date("Y-m-d");
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

    .table thead th {
        padding: 2px !important;
        text-align: center;
        vertical-align: middle !important;
        text-shadow: 1px 1px 2px black;
        min-height: 30px;
    }

    .table tbody td {
        white-space: nowrap;
        vertical-align: middle !important;
    }

    a {
        text-decoration: none !important;
    }

    .table-condensed {
        font-size: 14px;
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
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 text-left text-shadow visible-xs" style="font-size: 150%;font-weight: bold;">NAT</div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 text-left text-shadow hidden-xs" style="font-size: 150%;font-weight: bold;">Pemeriksaan IMLTD NAT</div>
                        <div class="col-lg-5 col-md-4 col-sm-5 col-xs-5 text-right">
                            <a href="#" class="w3-btn w3-theme-l2 w3-hover-yellow" data-toggle="modal" data-target="#mFilter"><i class="glyphicon glyphicon-filter"></i> Filter</a>
                            <a href="?module=nat_manual" class="w3-btn w3-theme-l4 w3-hover-yellow hidden-xs"><i class="glyphicon glyphicon-plus"></i> Input</a>
                        </div>
                    </div>
                    <div class="panel-body" style="height: 85vh;overflow:auto;">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr class="w3-theme-d5">
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Transaksi</th>
                                            <th>Kantong</th>
                                            <th>Gol</th>
                                            <th>Metode</th>
                                            <th>OD</th>
                                            <th>Hasil</th>
                                            <th>Lot</th>
                                            <th>ED Reagen</th>
                                            <th>Petugas</th>
                                            <th>Checker</th>
                                            <th>Validator</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $sql_abs = "SELECT `id`, `noKantong`, `idsample`, `nat_goldarah`, `nat_rhesus`, `notrans`, `kodedonor`, `dsdp`, `barulama`, `umur`, `kel`, 
                                                    `OD`, `COV`, `Hasil`, `jenisPeriksa`, `tglPeriksa`, `dicatatOleh`, `dicekOleh`, `DisahkanOleh`, `noLot`, `Metode`, `reagen`, 
                                                    `ed`, `ulang`, `tempat_periksa`, `on_insert` FROM `hasilnat`
                                                    WHERE date(`tglPeriksa`) BETWEEN '$f_tanggal1' AND '$f_tanggal2'";
                                        $qryabs = mysqli_query($dbi, $sql_abs);
                                        while ($row = mysqli_fetch_assoc($qryabs)) {
                                            $no++;
                                            switch ($row["Hasil"]) {
                                                case "0":
                                                    $hasilstr = "Non Reaktif";
                                                    $warna = "";
                                                    break;
                                                case "1":
                                                    $hasilstr = "Reaktif";
                                                    $warna = "w3-red";
                                                    break;
                                                case "2":
                                                    $hasilstr = "Invalid";
                                                    $warna = "w3-light-blue";
                                                    break;
                                                default:
                                                    $hasilstr = "??";
                                                    $warna = "";
                                                    break;
                                            }
                                            echo "<tr class=\"$warna\">
                                                    <td class=\"text-center\">$no</td>
                                                    <td class=\"text-left\">{$row["tglPeriksa"]}</td>
                                                    <td class=\"text-left\">{$row["notrans"]}</td>
                                                    <td class=\"text-left\">{$row["noKantong"]}</td>
                                                    <td class=\"text-center\">{$row["nat_goldarah"]}{$row["nat_rhesus"]}</td>
                                                    <td class=\"text-center\">{$row["Metode"]}</td>
                                                    <td class=\"text-center\">{$row["OD"]}</td>
                                                    <td class=\"text-center\">$hasilstr</td>
                                                    <td class=\"text-center\">{$row["noLot"]}</td>
                                                    <td class=\"text-center\">{$row["ed"]}</td>
                                                    <td class=\"text-center\">{$row["dicatatOleh"]}</td>
                                                    <td class=\"text-center\">{$row["dicekOleh"]}</td>
                                                    <td class=\"text-center\">{$row["DisahkanOleh"]}</td>
                                                  </tr>";
                                        }
                                        ?>
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
                        <div class="form-group">
                            <label class="control-label col-md-3" for="MdlTanggal1">Tanggal Aftap</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control startdate" value="<?php echo $f_tanggal1; ?>" name="MdlTanggal1" id="MdlTanggal1" />
                                    <span class="input-group-addon input-sm">s/d</span>
                                    <input type="text" class="form-control enddate" value="<?php echo $f_tanggal2; ?>" name="MdlTanggal2" id="MdlTanggal2" />
                                </div>
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
    $(document).ready(function() {
        setDatePicker()
        setDateRangePicker(".startdate", ".enddate")
        setMonthPicker()
        setYearPicker()
        setYearRangePicker(".startyear", ".endyear");
    });

    function isNumberKey(evt, obj) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        var value = obj.value;
        var dotcontains = value.indexOf(".") != -1;
        if (dotcontains)
            if (charCode == 46) return false;
        if (charCode == 46) return true;
        if (charCode == 45 && value.length !== 0) return false;
        if (charCode == 45) return true;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    };

    $('.chosen-select').chosen({
        width: "100%"
    });
    var load = document.getElementById("loading");
    window.addEventListener('load', function() {
        load.style.display = "none";
    });
</script>