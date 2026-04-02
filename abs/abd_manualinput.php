<?php
require_once("clogin.php");
require_once("config/dbi_connect.php");

session_start(); // Pastikan session dimulai jika belum

$namauser = $_SESSION["namauser"];
$namalengkap = $_SESSION["nama_lengkap"];
$leveluser = $_SESSION["level"];
$tanggal = date("Y-m-d");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pemeriksaan Konfirmasi Golongan Darah</title>
    <link rel="stylesheet" href="bootsrap337/bspmi.css">
    <link rel="stylesheet" href="bootsrap337/w3.css">
    <link rel="stylesheet" href="abs/abs.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
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

        select {
            padding: 0px;
            min-width: 70px !important;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
</head>

<body>
    <div id="loading"></div>
    <div class="container-fluid" style="margin: 20px;">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel w3-border-theme shadow">
                    <form class="form-horizontal" method="POST" id="FrmAbd" name="FrmAbd">
                        <div class="panel-heading w3-theme-d5 clearfix">
                            <div class="col-lg-9 col-md-8 col-sm-7 col-xs-7 text-left text-shadow visible-xs" style="font-size: 150%; font-weight: bold;">KGD</div>
                            <div class="col-lg-9 col-md-8 col-sm-7 col-xs-7 text-left text-shadow hidden-xs" style="font-size: 150%; font-weight: bold;">Pemeriksaan Konfirmasi Golongan Darah</div>
                            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-5 text-right">
                                <button type="submit" class="w3-btn w3-theme-l1 w3-hover-yellow" id="btnSimpan"><i class="glyphicon glyphicon-floppy-save"></i> Simpan</button>
                                <a href="?module=rekap_konfirmasi" class="w3-btn w3-theme-l3 w3-hover-yellow"><i class="glyphicon glyphicon-list"></i> Rekap</a>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="panel w3-border-theme">
                                <div class="panel-body w3-card-2">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label col-xs-3 col-sm-3" for="InpTanggalJam">Tanggal</label>
                                                <div class="col-xs-9 col-sm-9">
                                                    <input id="InpTanggalJam" name="InpTanggalJam" type="text" class="form-control input-sm datetimepicker" required="required" value="<?php echo date("Y-m-d H:i"); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label col-xs-3 col-sm-3" for="InpMetode">Metode</label>
                                                <div class="col-xs-9 col-sm-9">
                                                    <select name="InpMetode" id="InpMetode" class="form-control" required="required">
                                                        <option value="Tube Test">Tube Test</option>
                                                        <option value="Bioplat">Bioplat</option>
                                                        <option value="Otomatis">Otomatis</option>
                                                        <option value="Slide Test">Slide Test</option>
                                                        <option value="Gel Test">Gel Test</option>
                                                        <option value="Microplate Test">Microplate Test</option>
                                                        <option value="SPRCA">Solid Phase Red Cell Adherence</option>
                                                        <option value="Flow Cytometry">Flow Cytometry</option>
                                                        <option value="PCR">Molecular Testing</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label col-xs-3 col-sm-3" for="Inpreagen">Reagen</label>
                                                <div class="col-xs-9 col-sm-9">
                                                    <input id="Inpreagen" name="Inpreagen" type="text" class="form-control input-sm" oninput="this.value = this.value.toUpperCase()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="w3-panel w3-leftbar w3-border-theme w3-theme-l4">
                                                <h5>Anti A</h5>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="nolota">Lot</label>
                                                <div class="col-xs-9">
                                                    <input id="nolota" name="nolota" type="text" class="form-control input-sm" oninput="this.value = this.value.toUpperCase()" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="expa">ED</label>
                                                <div class="col-xs-9">
                                                    <input id="expa" name="expa" type="text" class="form-control input-sm" required placeholder="yyyy-mm-dd">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="w3-panel w3-leftbar w3-border-theme w3-theme-l4">
                                                <h5>Anti B</h5>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="nolotb">Lot</label>
                                                <div class="col-xs-9">
                                                    <input id="nolotb" name="nolotb" type="text" class="form-control input-sm" oninput="this.value = this.value.toUpperCase()" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="expb">ED</label>
                                                <div class="col-xs-9">
                                                    <input id="expb" name="expb" type="text" class="form-control input-sm" required placeholder="yyyy-mm-dd">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="w3-panel w3-leftbar w3-border-theme w3-theme-l4">
                                                <h5>Anti D</h5>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="nolotd">Lot</label>
                                                <div class="col-xs-9">
                                                    <input id="nolotd" name="nolotd" type="text" class="form-control input-sm" oninput="this.value = this.value.toUpperCase()" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="expd">ED</label>
                                                <div class="col-xs-9">
                                                    <input id="expd" name="expd" type="text" class="form-control input-sm" required placeholder="yyyy-mm-dd">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="w3-panel w3-card w3-theme-l2" style="margin-top: 10px;">
                                        <div class="form-group">
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon w3-theme-l4">Barcode</span>
                                                    <input id="InpBarcode" name="InpBarcode" type="text" class="form-control" placeholder="Nomor kantong" autofocus autocomplete="off" style="font-size: 140%; font-weight: bold; color: red;">
                                                    <span class="input-group-btn">
                                                        <a id="SubmitBarcode" name="SubmitBarcode" class="w3-btn w3-theme-d4 w3-hover-yellow w3-small"><i class="glyphicon glyphicon-download w3-large"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 text-left">
                                                <div id="tload" style="display: none;"><img src="profile/simdondar_loading1.gif"></div>
                                                <div id="pesan" style="font-size: 120%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-responsive table-bordered table-condensed" id="tabledata">
                                            <thead class="w3-theme-d4" style="height: 45px;">
                                                <th>X</th>
                                                <th>No</th>
                                                <th>No Kantong</th>
                                                <th>Pendonor</th>
                                                <th>Gol Darah<br>Awal</th>
                                                <th>Rhesus<br>Awal</th>
                                                <th>Gol Darah<br>Konfirmasi</th>
                                                <th>Rhesus</th>
                                                <th>Anti<br>A</th>
                                                <th>Anti<br>B</th>
                                                <th>Tes Cell<br>A</th>
                                                <th>Tes Cell<br>B</th>
                                                <th>Tes Cell<br>O</th>
                                                <th>Auto<br>Ctrl</th>
                                                <th>Anti<br>D</th>
                                                <th>Bovin <br>Albumin 22%</th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel w3-border-theme">
                                        <div class="panel-body w3-card-2">
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label col-xs-3" for="InpUser">Pemeriksa</label>
                                                    <div class="col-xs-9">
                                                        <input id="InpUser" name="InpUser" type="text" class="form-control input-sm" readonly value="<?php echo $namauser; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label col-xs-3" for="InpChecker"><i>Checker</i></label>
                                                    <div class="col-xs-9">
                                                        <select name="InpChecker" id="InpChecker" class="form-control input-sm select2" required="required">
                                                            <?php
                                                            $query = mysqli_query($dbi, "SELECT id_user, nama_lengkap FROM `user` WHERE aktif=0");
                                                            while ($dt = mysqli_fetch_assoc($query)) {
                                                                echo "<option value=\"" . $dt["id_user"] . "\">" . $dt["nama_lengkap"] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label col-xs-3" for="InpValidator"><i>Validator</i></label>
                                                    <div class="col-xs-9">
                                                        <select name="InpValidator" id="InpValidator" class="form-control input-sm select2" required="required">
                                                            <?php
                                                            $query = mysqli_query($dbi, "SELECT id_user, nama_lengkap FROM `user` WHERE aktif=0");
                                                            while ($dt = mysqli_fetch_assoc($query)) {
                                                                echo "<option value=\"" . $dt["id_user"] . "\">" . $dt["nama_lengkap"] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="bootsrap337/sweetalert2/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('.datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
                icons: {
                    time: 'glyphicon glyphicon-time',
                    date: 'glyphicon glyphicon-calendar',
                    up: 'glyphicon glyphicon-chevron-up',
                    down: 'glyphicon glyphicon-chevron-down',
                    previous: 'glyphicon glyphicon-chevron-left',
                    next: 'glyphicon glyphicon-chevron-right',
                    today: 'glyphicon glyphicon-crosshair',
                    clear: 'glyphicon glyphicon-trash',
                    close: 'glyphicon glyphicon-remove'
                }
            });

            toggleSimpanButton();
            $(window).keydown(function(event) {
                if (event.keyCode == 13) {
                    if ($(event.target)[0] == $('#InpBarcode')[0]) {
                        event.preventDefault();
                        var nokantong = $('#InpBarcode').val();
                        addkantongpemeriksaan(nokantong);
                        return false;
                    } else {
                        event.preventDefault();
                        return false;
                    }
                }
            });

            $('#SubmitBarcode').on('click', function() {
                var nokantong = $('#InpBarcode').val();
                addkantongpemeriksaan(nokantong);
            })

            $('#FrmAbd').on('submit', function(event) {
                event.preventDefault();
                let isValid = true;
                let errorMessage = "";

                function isValidDate(dateString) {
                    let regex = /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/;
                    if (!regex.test(dateString)) {
                        return false;
                    }
                    let parts = dateString.split("-");
                    let year = parseInt(parts[0], 10);
                    let month = parseInt(parts[1], 10);
                    let day = parseInt(parts[2], 10);
                    let dateObj = new Date(year, month - 1, day);
                    return dateObj.getFullYear() === year &&
                        dateObj.getMonth() === month - 1 &&
                        dateObj.getDate() === day;
                }
                let expa = $('#expa').val()?.trim() || "";
                let expb = $('#expb').val()?.trim() || "";
                let expd = $('#expd').val()?.trim() || "";
                if (!isValidDate(expa) || !isValidDate(expb) || !isValidDate(expd)) {
                    isValid = false;
                    errorMessage = "Tanggal harus dalam format yyyy-mm-dd dan merupakan tanggal yang valid!";
                }
                let user = $('#InpUser').val();
                let checker = $('#InpChecker').val();
                let validator = $('#InpValidator').val();

                if (user === checker || user === validator || checker === validator) {
                    isValid = false;
                    errorMessage = "User, Checker, dan Validator harus orang yang berbeda!";
                }

                if (!isValid) {
                    Swal.fire({
                        title: "Validasi Gagal",
                        text: errorMessage,
                        icon: "error"
                    });
                    return;
                }
                let dataform = $('#FrmAbd').serialize();
                let tableData = [];
                let hasUnknownBloodType = false;

                $('#tabledata tbody tr').each(function() {
                    var row = $(this);
                    var no_kantong = row.find("td:eq(2)").text();
                    var pendonor = row.find("td:eq(3)").text();
                    var gol_awal = row.find("td:eq(4)").text(); // Gol Darah Awal (A/B/AB/O)
                    var rh_awal = row.find("td:eq(5)").text(); // Rhesus Awal (+/-)
                    var gol_kgd = row.find(".gol-darah-konfirmasi select").val();
                    var rh_kgd = row.find(".rhesus-konfirmasi select").val();
                    var anti_a = row.find(".anti-a select").val();
                    var anti_b = row.find(".anti-b select").val();
                    var tcell_a = row.find(".tes-cell-a select").val();
                    var tcell_b = row.find(".tes-cell-b select").val();
                    var tcell_o = row.find(".tes-cell-o select").val();
                    var autoctrl = row.find(".auto-ctrl select").val();
                    var anti_d = row.find(".anti-d select").val();
                    var bovalbumin = row.find(".bovin-albumin select").val();

                    // Validasi sederhana untuk gol_awal dan rh_awal (opsional, tapi direkomendasikan)
                    if (!['A', 'B', 'AB', 'O'].includes(gol_awal)) {
                        isValid = false;
                        errorMessage = "Golongan darah awal invalid!";
                        return false; // Stop loop jika invalid
                    }
                    if (!['+', '-'].includes(rh_awal)) {
                        isValid = false;
                        errorMessage = "Rhesus awal invalid!";
                        return false;
                    }

                    if (gol_kgd === "?" || rh_kgd === "?") {
                        hasUnknownBloodType = true;
                    }

                    tableData.push({
                        no_kantong: no_kantong,
                        pendonor: pendonor,
                        gol_awal: gol_awal, // Kirim ABO terpisah (A/B/AB/O)
                        rh_awal: rh_awal, // Kirim Rhesus terpisah (+/-)
                        gol_kgd: gol_kgd,
                        rh_kgd: rh_kgd,
                        anti_a: anti_a,
                        anti_b: anti_b,
                        tcell_a: tcell_a,
                        tcell_b: tcell_b,
                        tcell_o: tcell_o,
                        autoctrl: autoctrl,
                        anti_d: anti_d,
                        bovalbumin: bovalbumin
                    });
                });

                if (!isValid) {
                    Swal.fire({
                        title: "Validasi Gagal",
                        text: errorMessage,
                        icon: "error"
                    });
                    return;
                }

                let payload = {
                    formData: dataform,
                    tableData: JSON.stringify(tableData)
                };

                if (hasUnknownBloodType) {
                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Ada hasil pemeriksaan yang belum dapat ditentukan. Apakah Anda ingin melanjutkan?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Lanjutkan",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sendData(payload);
                        }
                    });
                } else {
                    sendData(payload);
                }
            });
        });

        function sendData(payload) {
            $.ajax({
                url: "abs/abs_proccess.php?mdl=<?php echo md5("simpaninputdata_kgd"); ?>",
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                beforeSend: function() {
                    $('#pesan').html('...menyimpan data....');
                    $("#tload").show();
                },
                success: function(response) {
                    if (response.status == '0') {
                        Swal.fire('Pemeriksaan KGD', response.message, 'success');
                        document.getElementById("FrmAbd").reset();
                        $("#tabledata tbody").empty();
                    } else {
                        Swal.fire("Info", response.message, "warning").then((result) => {
                            $('#pesan').html(response.message);
                            $("#tload").hide();
                        });
                    }
                },
                complete: function() {
                    $("#tload").hide();
                    $('#pesan').html(response.message);
                    toggleSimpanButton();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        function addkantongpemeriksaan(nokantong) {
            nokantong = nokantong.toUpperCase();
            if (nokantong == null || nokantong == "") {
                Swal.fire({
                    title: "Error",
                    text: "Nomor kantong masih kosong, silahkan masukkan/scan kode kantong!!!!",
                    icon: "error",
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    $("#InpBarcode").select();
                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: "abs/abs_proccess.php?mdl=<?php echo md5("cekkantong_kgd"); ?>",
                    dataType: "json",
                    data: {
                        kantong: nokantong
                    },
                    beforeSend: function() {
                        $("#InpBarcode").val('');
                        $('#pesan').html('');
                        $("#tload").show();
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status === 'error') {
                            Swal.fire({
                                title: "Error",
                                text: response.message,
                                icon: "error",
                                timer: 2500,
                                timerProgressBar: true
                            });
                        } else {
                            addRow(response.kantong);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            title: "Error",
                            text: "Ajax Error: " + error,
                            icon: "error",
                            timer: 2500,
                            timerProgressBar: true
                        });
                    },
                    complete: function() {
                        $("#tload").hide();
                        $("#InpBarcode").val('');
                        $("#InpBarcode").select();
                        toggleSimpanButton();
                    }
                });
            }
        }

        var load = document.getElementById("loading");
        window.addEventListener('load', function() {
            load.style.display = "none";
        });
        $("#InpBarcode").select();

        function addRow(item) {
            var tbody = $("#tabledata tbody");
            var exists = false;
            $("#tabledata tbody tr").each(function() {
                var existingKantong = $(this).find("td:eq(2)").text();
                if (existingKantong === item.nokantong) {
                    exists = true;
                    return false;
                }
            });

            if (exists) {
                Swal.fire("Info", "Nomor kantong sudah ada di tabel!", "warning");
                return;
            }
            var golSelect = "<select class='form-control input-sm'>" +
                "<option value='A' " + (item.gol === "A" ? "selected" : "") + ">A</option>" +
                "<option value='B' " + (item.gol === "B" ? "selected" : "") + ">B</option>" +
                "<option value='O' " + (item.gol === "O" ? "selected" : "") + ">O</option>" +
                "<option value='AB' " + (item.gol === "AB" ? "selected" : "") + ">AB</option>" +
                "</select>";
            var rhSelect = "<select class='form-control input-sm'>" +
                "<option value='-' " + (item.rh === "-" ? "selected" : "") + ">Negatif</option>" +
                "<option value='+' " + (item.rh === "+" ? "selected" : "") + ">Positif</option>" +
                "</select>";

            function getDefaultAgglutination(gol, testType) {
                var result = {
                    "Anti A": "Neg",
                    "Anti B": "Neg",
                    "Tes Cell A": "Neg",
                    "Tes Cell B": "Neg",
                    "Tes Cell O": "Neg",
                    "Auto Ctrl": "Neg",
                    "Anti D": (item.rh === "+" ? "4+" : "Neg"),
                    "Bovine Albumin": "Neg"
                };

                if (gol === "A") {
                    result["Anti A"] = "4+";
                    result["Anti B"] = "Neg";
                    result["Tes Cell A"] = "Neg";
                    result["Tes Cell B"] = "4+";
                } else if (gol === "B") {
                    result["Anti A"] = "Neg";
                    result["Anti B"] = "4+";
                    result["Tes Cell A"] = "4+";
                    result["Tes Cell B"] = "Neg";
                } else if (gol === "AB") {
                    result["Anti A"] = "4+";
                    result["Anti B"] = "4+";
                    result["Tes Cell A"] = "Neg";
                    result["Tes Cell B"] = "Neg";
                } else if (gol === "O") {
                    result["Anti A"] = "Neg";
                    result["Anti B"] = "Neg";
                    result["Tes Cell A"] = "4+";
                    result["Tes Cell B"] = "4+";
                }

                return result[testType];
            }

            function createDropdown(testType) {
                var selectedValue = getDefaultAgglutination(item.gol, testType);
                return "<select class='form-control input-sm'>" +
                    "<option value='Neg' " + (selectedValue === "Neg" ? "selected" : "") + ">Neg</option>" +
                    "<option value='1+' " + (selectedValue === "1+" ? "selected" : "") + ">1+</option>" +
                    "<option value='2+' " + (selectedValue === "2+" ? "selected" : "") + ">2+</option>" +
                    "<option value='3+' " + (selectedValue === "3+" ? "selected" : "") + ">3+</option>" +
                    "<option value='4+' " + (selectedValue === "4+" ? "selected" : "") + ">4+</option>" +
                    "</select>";
            }
            var row = "<tr>" +
                "<td class='text-center'><a class='w3-tinny w3-hover-red' onclick='hapusRow(this)'><i class='glyphicon glyphicon-trash'></i></a></td>" +
                "<td class='text-right'>" + ($("#tabledata tbody tr").length + 1) + ".</td>" +
                "<td>" + item.nokantong + "</td>" +
                "<td>" + item.pendonor + "</td>" +
                "<td class='text-center'>" + item.gol + "</td>" + // Gol Darah Awal (A/B/AB/O)
                "<td class='text-center'>" + item.rh + "</td>" + // Rhesus Awal (+/-)
                "<td class='gol-darah-konfirmasi w3-theme-l4'>" + golSelect + "</td>" +
                "<td class='rhesus-konfirmasi w3-theme-l4'>" + rhSelect + "</td>" +
                "<td class='hasil-dropdown anti-a'>" + createDropdown("Anti A") + "</td>" +
                "<td class='hasil-dropdown anti-b'>" + createDropdown("Anti B") + "</td>" +
                "<td class='hasil-dropdown tes-cell-a'>" + createDropdown("Tes Cell A") + "</td>" +
                "<td class='hasil-dropdown tes-cell-b'>" + createDropdown("Tes Cell B") + "</td>" +
                "<td class='hasil-dropdown tes-cell-o'>" + createDropdown("Tes Cell O") + "</td>" +
                "<td class='hasil-dropdown auto-ctrl'>" + createDropdown("Auto Ctrl") + "</td>" +
                "<td class='hasil-dropdown anti-d'>" + createDropdown("Anti D") + "</td>" +
                "<td class='hasil-dropdown bovin-albumin'>" + createDropdown("Bovine Albumin") + "</td>" +
                "</tr>";

            tbody.append(row);
            toggleSimpanButton();
        }

        function updateBloodGroup(row) {
            var antiA = $(row).find(".anti-a select").val();
            var antiB = $(row).find(".anti-b select").val();
            var tesCellA = $(row).find(".tes-cell-a select").val();
            var tesCellB = $(row).find(".tes-cell-b select").val();
            var tesCellO = $(row).find(".tes-cell-o select").val();
            var antiD = $(row).find(".anti-d select").val();

            function isPositive(value) {
                return value !== "Neg";
            }
            var bloodType = "Tidak Valid";
            if (!isPositive(antiA) && !isPositive(antiB) && isPositive(tesCellA) && isPositive(tesCellB) && !isPositive(tesCellO)) {
                bloodType = "O";
            } else if (isPositive(antiA) && !isPositive(antiB) && !isPositive(tesCellA) && isPositive(tesCellB) && !isPositive(tesCellO)) {
                bloodType = "A";
            } else if (!isPositive(antiA) && isPositive(antiB) && isPositive(tesCellA) && !isPositive(tesCellB) && !isPositive(tesCellO)) {
                bloodType = "B";
            } else if (isPositive(antiA) && isPositive(antiB) && !isPositive(tesCellA) && !isPositive(tesCellB) && !isPositive(tesCellO)) {
                bloodType = "AB";
            }
            var rhesus = isPositive(antiD) ? "+" : "-";
            $(row).find(".rhesus-konfirmasi select").val(rhesus);
            var golonganSelect = $(row).find(".gol-darah-konfirmasi select");
            if (golonganSelect.find("option[value='?']").length === 0) {
                golonganSelect.append('<option value="?">?</option>');
            }
            if (bloodType === "Tidak Valid") {
                golonganSelect.val("?");
            } else {
                golonganSelect.val(bloodType);
            }
        }

        $(document).on("change", ".hasil-dropdown select", function() {
            var row = $(this).closest("tr");
            updateBloodGroup(row);
        });

        function hapusRow(button) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(button).closest("tr").remove();
                    Swal.fire('Dihapus!', 'Baris data telah dihapus.', 'success');
                    toggleSimpanButton();
                } else {
                    Swal.fire('Batal', 'Data tidak dihapus.', 'info');
                }
            });
        }

        function toggleSimpanButton() {
            if ($("#tabledata tbody tr").length === 0) {
                $("#btnSimpan").prop("disabled", true);
            } else {
                $("#btnSimpan").prop("disabled", false);
            }
        }

        function isValidDate(dateString) {
            const parts = dateString.split("-");
            const year = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1; // Bulan dalam JavaScript dimulai dari 0
            const day = parseInt(parts[2], 10);

            const date = new Date(year, month, day);
            return date.getFullYear() === year && date.getMonth() === month && date.getDate() === day;
        }
    </script>
</body>

</html>