<?php
session_start(); // Assuming session is needed based on usage of $_SESSION

require_once("clogin.php");
require_once("config/dbi_connect.php");

$namauser = $_SESSION["namauser"];
$nama_lengkap = $_SESSION["nama_lengkap"];
$leveluser = $_SESSION["level"];
$tanggal = date("Y-m-d");

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Antibody Screening</title>
    <link rel="stylesheet" href="bootsrap337/bspmi.css">
    <link rel="stylesheet" href="bootsrap337/w3.css">
    <link rel="stylesheet" href="abs/abs.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .swal2-popup { font-size: 15px !important; }
        .form-group { margin-top: 1px; margin-bottom: 1px; }
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
        a { text-decoration: none !important; }
        .table-condensed { font-size: 14px; }
        .modal-fullscreen {
            width: 90vw;
            padding: 0;
            height: 90vh;
        }
    </style>
</head>
<body>
    <div id="loading"></div>
    <div class="container-fluid" style="margin: 20px;">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel w3-border-theme shadow">
                    <form class="form-horizontal" method="POST" id="FrmAbs" name="FrmAbs">
                        <div class="panel-heading w3-theme-d5 clearfix">
                            <div class="col-lg-9 col-md-8 col-sm-7 col-xs-7 text-left text-shadow visible-xs" style="font-size: 150%; font-weight: bold;">ABS</div>
                            <div class="col-lg-9 col-md-8 col-sm-7 col-xs-7 text-left text-shadow hidden-xs" style="font-size: 150%; font-weight: bold;">Pemeriksaan Antibody Screening</div>
                            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-5 text-right">
                                <button type="submit" class="w3-btn w3-theme-l1 w3-hover-yellow" id="btnSimpan"><i class="glyphicon glyphicon-floppy-save"></i> Simpan</button>
                                <a href="?module=abs_rekapmanual" class="w3-btn w3-theme-l3 w3-hover-yellow"><i class="glyphicon glyphicon-list"></i> Rekap</a>
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
                                                    <input id="InpTanggalJam" name="InpTanggalJam" type="text" class="form-control input-sm datetimepicker" required="required" value="$tanggal H:i">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label col-xs-3 col-sm-3" for="InpMetode">Metode</label>
                                                <div class="col-xs-9 col-sm-9">
                                                    <select name="InpMetode" id="InpMetode" class="form-control" required="required">
                                                        <option value="Manual">Manual</option>
                                                        <option value="Automatic">Automatic</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label col-xs-3 col-sm-3" for="InpReagen">Reagen</label>
                                                <div class="col-xs-9 col-sm-9">
                                                    <input id="InpReagen" name="InpReagen" type="text" class="form-control input-sm" oninput="this.value = this.value.toUpperCase()" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="w3-panel w3-leftbar w3-border-theme w3-theme-l4"><h5>AHG (IgG+C3d)</h5></div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="InpIggLot">Lot</label>
                                                <div class="col-xs-9">
                                                    <input id="InpIggLot" name="InpIggLot" type="text" class="form-control input-sm" oninput="this.value = this.value.toUpperCase()" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="InpIggED">ED</label>
                                                <div class="col-xs-9">
                                                    <input id="InpIggED" name="InpIggED" type="text" class="form-control input-sm" required placeholder="yyyy-mm-dd">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="w3-panel w3-leftbar w3-border-theme w3-theme-l4"><h5>Cell 1</h5></div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="InpCell1Lot">Lot</label>
                                                <div class="col-xs-9">
                                                    <input id="InpCell1Lot" name="InpCell1Lot" type="text" class="form-control input-sm" oninput="this.value = this.value.toUpperCase()" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="InpCell1ED">ED</label>
                                                <div class="col-xs-9">
                                                    <input id="InpCell1ED" name="InpCell1ED" type="text" class="form-control input-sm" required placeholder="yyyy-mm-dd">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="w3-panel w3-leftbar w3-border-theme w3-theme-l4"><h5>Cell 2</h5></div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="InpCell2Lot">Lot</label>
                                                <div class="col-xs-9">
                                                    <input id="InpCell2Lot" name="InpCell2Lot" type="text" class="form-control input-sm" oninput="this.value = this.value.toUpperCase()" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-xs-3" for="InpCell2ED">ED</label>
                                                <div class="col-xs-9">
                                                    <input id="InpCell2ED" name="InpCell2ED" type="text" class="form-control input-sm" required placeholder="yyyy-mm-dd">
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
                                                <tr>
                                                    <th>X</th>
                                                    <th>No</th>
                                                    <th>No Kantong</th>
                                                    <th>Pendonor</th>
                                                    <th>Gol</th>
                                                    <th>Rh</th>
                                                    <th>Hasil</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
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
                                                    <label class="control-label col-xs-3" for="InpUser">User</label>
                                                    <div class="col-xs-9">
                                                        <input id="InpUser" name="InpUser" type="text" class="form-control input-sm" readonly value="$namauser">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label col-xs-3" for="InpChecker">Dicek oleh</label>
                                                    <div class="col-xs-9">
                                                        <select name="InpChecker" id="inputInpChecker" class="form-control input-sm select2" required="required">
HTML;

$query = mysqli_query($dbi, "SELECT id_user, nama_lengkap FROM `user` WHERE aktif=0");
while ($dt = mysqli_fetch_assoc($query)) {
    echo "<option value=\"" . $dt["id_user"] . "\">" . $dt["nama_lengkap"] . "</option>";
}

echo <<<HTML
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label col-xs-3" for="InpValidator">Disahkan oleh</label>
                                                    <div class="col-xs-9">
                                                        <select name="InpValidator" id="InpValidator" class="form-control input-sm select2" required="required">
HTML;

$query = mysqli_query($dbi, "SELECT id_user, nama_lengkap FROM `user` WHERE aktif=0");
while ($dt = mysqli_fetch_assoc($query)) {
    echo "<option value=\"" . $dt["id_user"] . "\">" . $dt["nama_lengkap"] . "</option>";
}

echo <<<HTML
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="bootsrap337/sweetalert2/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        \$(document).ready(function() {
            \$('.select2').select2();
            \$('.datetimepicker').datetimepicker({
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
            \$(window).keydown(function(event) {
                if (event.keyCode == 13) {
                    if (\$(event.target)[0] === \$('#InpBarcode')[0]) {
                        event.preventDefault();
                        var nokantong = \$('#InpBarcode').val();
                        addkantongpemeriksaan(nokantong);
                        return false;
                    } else {
                        event.preventDefault();
                        return false;
                    }
                }
            });

            \$('#SubmitBarcode').on('click', function() {
                var nokantong = \$('#InpBarcode').val();
                addkantongpemeriksaan(nokantong);
            });

            \$('#FrmAbs').on('submit', function(event) {
                event.preventDefault();
                let dataform = \$('#FrmAbs').serializeArray();
                let tableData = [];
                \$('#tabledata tbody tr').each(function() {
                    var row = \$(this);
                    var no_kantong = row.find("td:eq(2)").text();
                    var pendonor = row.find("td:eq(3)").text();
                    var gol = row.find("td:eq(4)").text();
                    var rh = row.find("td:eq(5)").text();
                    var hasil = row.find(".hasil-dropdown").val();
                    var status = row.find("td:eq(7)").text();
                    tableData.push({
                        no_kantong: no_kantong,
                        pendonor: pendonor,
                        gol: gol,
                        rh: rh,
                        hasil: hasil,
                        status: status
                    });
                });
                let payload = {
                    formData: dataform,
                    tableData: tableData
                };
                \$.ajax({
                    url: "abs/abs_proccess.php?mdl=
HTML;
echo md5("simpaninputdata");
echo <<<HTML
",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    beforeSend: function() {
                        \$('#pesan').html('...menyimpan data....');
                        \$("#tload").show();
                    },
                    success: function(response) {
                        if (response.status == '0') {
                            Swal.fire('Anti ABS', response.message, 'success');
                            document.getElementById("FrmAbs").reset();
                            \$("#tabledata tbody").empty();
                        } else {
                            Swal.fire("Info", response.message, "warning").then((result) => {
                                \$('#pesan').html(response.message);
                                \$("#tload").hide();
                            });
                        }
                    },
                    complete: function() {
                        \$("#tload").hide();
                        \$('#pesan').html(response.message);
                        toggleSimpanButton();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        });

        function addkantongpemeriksaan(nokantong) {
            nokantong = nokantong.toUpperCase();
            if (nokantong == null || nokantong == "") {
                Swal.fire({
                    title: "Error",
                    text: "Nomor kantong masih kosong, silahkan masukkan/scan kode kantong!!!!",
                    icon: "error",
                    timer: 3000,
                    timerProgressBar: true
                }).then(() => {
                    \$("#InpBarcode").select();
                });
            } else {
                \$.ajax({
                    type: 'POST',
                    url: "abs/abs_proccess.php?mdl=
HTML;
echo md5("cekkantong");
echo <<<HTML
",
                    dataType: "json",
                    data: { kantong: nokantong },
                    beforeSend: function() {
                        \$("#InpBarcode").val('');
                        \$('#pesan').html('');
                        \$("#tload").show();
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            Swal.fire({
                                title: "Error",
                                text: response.message,
                                icon: "error",
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            addRow(response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "Error",
                            text: "Ajax Error: " + error,
                            icon: "error",
                            timer: 3000,
                            timerProgressBar: true
                        });
                    },
                    complete: function() {
                        \$("#tload").hide();
                        \$("#InpBarcode").val('');
                        \$("#InpBarcode").select();
                    }
                });
            }
        }

        var load = document.getElementById("loading");
        window.addEventListener('load', function() {
            load.style.display = "none";
        });
        \$("#InpBarcode").select();

        function addRow(item) {
            var tbody = \$("#tabledata tbody");
            var exists = false;
            \$("#tabledata tbody tr").each(function() {
                var existingKantong = \$(this).find("td:eq(2)").text();
                if (existingKantong === item.no_kantong) {
                    exists = true;
                    return false;
                }
            });
            if (exists) {
                Swal.fire("Info", "Nomor kantong sudah ada di tabel!", "warning");
                return;
            }
            var row = "<tr>" +
                        "<td class='text-center'><a class='w3-tiny w3-hover-red' onclick='hapusRow(this)'><i class='glyphicon glyphicon-trash'></i></a></td>" +
                        "<td class='text-right'>" + (\$("#tabledata tbody tr").length + 1) + ".</td>" +
                        "<td>" + item.no_kantong + "</td>" +
                        "<td>" + item.pendonor + "</td>" +
                        "<td class='text-center'>" + item.gol + "</td>" +
                        "<td class='text-center'>" + item.rh + "</td>" +
                        "<td>" + "<select class='form-control hasil-dropdown input-sm'>" +
                                    "<option value='Neg'>Negatif</option>" +
                                    "<option value='Pos'>Positif</option>" +
                                    "<option value='x'>Tidak diketahui</option>" +
                                "</select>" + "</td>" +
                        "<td class='text-center'>" + item.statusstr + " (" + item.status + ")</td>" +
                      "</tr>";
            tbody.append(row);
            toggleSimpanButton();
        }

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
                    \$(button).closest("tr").remove();
                    Swal.fire('Dihapus!', 'Baris data telah dihapus.', 'success');
                    toggleSimpanButton();
                } else {
                    Swal.fire('Batal', 'Data tidak dihapus.', 'info');
                }
            });
        }

        function toggleSimpanButton() {
            if (\$("#tabledata tbody tr").length === 0) {
                \$("#btnSimpan").prop("disabled", true);
            } else {
                \$("#btnSimpan").prop("disabled", false);
            }
        }
    </script>
</body>
</html>
HTML;
?>s