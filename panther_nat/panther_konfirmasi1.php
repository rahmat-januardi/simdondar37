<?php include('clogin.php');
include('config/dbi_connect.php'); ?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootsrap337/w3.css">
    <link rel="stylesheet" href="panther_nat/panther.css">
    <link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
    <link href="bootsrap337/datepicker/css/bootstrap-datepicker.css" rel="stylesheet">
    <link rel="stylesheet" href="bootsrap337/chosen/chosen.css">
    <style>
        .shadow {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .shadow-xx {
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.19);
        }

        .form-group {
            margin-top: 3px;
            margin-bottom: 2px;
        }

        .modal-header {
            padding: 9px 15px;
            border-bottom: 1px solid #eee;
            background-color: red;
            -webkit-border-top-left-radius: 5px;
            -webkit-border-top-right-radius: 5px;
            -moz-border-radius-topleft: 5px;
            -moz-border-radius-topright: 5px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        .table thead th {
            height: 40px;
            font-size: 14px;
            text-align: center;
            vertical-align: middle !important;
        }

        a {
            text-decoration: none !important;
        }

        .table-condensed {
            font-size: 14px;
        }

        .table tbody td {
            white-space: nowrap;
        }

        #loading {
            width: 50px;
            height: 50px;
            border-radius: 100%;
            border: 5px solid #ccc;
            border-top-color: #ff6a00;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            z-index: 99;
            animation: sp 2s ease infinite;
        }

        @keyframes sp {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<?php
$leveluser = strtoupper($_SESSION['leveluser']);
$namauser = strtoupper($_SESSION['namauser']);
$tanggal = date('Y-m-d');
$v_wid = $_GET['wid'];
$v_oid = $_GET['oid'];
$v_at = $_GET['at'];
function cekstatuskantong($status, $stattempat, $sah)
{
    $resulstatus = "";
    switch ($status) {
        case '0':
            $resulstatus = "Kosong";
            if ($stattempat == NULL) $resulstatus = "Kosong di logistik";
            if ($stattempat == '0') $resulstatus = "Kosong di Logistik";
            if ($stattempat == '1') $resulstatus = "Kosong di Aftap";
            break;
        case '1':
            if ($sah == "1") {
                $resulstatus = 'Karantina';
            } else {
                $resulstatus = 'Belum disahkan';
            }
            break;
        case '2':
            $resulstatus = 'Sehat';
            break;
        case '3':
            $resulstatus = "Keluar";
            break;
        case '4':
            $resulstatus = 'Rusak';
            break;
        case '5':
            $resulstatus = 'Rusak-Gagal';
            break;
        case '6':
            $resulstatus = 'Dimusnahkan';
            break;
        case '6':
            $resulstatus = 'Reaktif';
            break;
        default:
            $resulstatus = 'Tidak ada';
            break;
    }
    return $resulstatus;
}
$sql = "SELECT (STR_TO_DATE(`RDT`,'%Y-%m-%d')) as tglperiksa, (STR_TO_DATE(`MLD`,'%Y-%m-%d')) as edreagen, `ID`, `SB`, `OI`, `AT`, `WID`, `RDT`, `STAT`, `ICRLU`, `ICR`, `ARLU`, `ASCO`, `KI`, `OID`, `ICCO`, `ACO`, `NCAA`, `NCICA`, `IPCA`, `IPCICA`, `CPCA`, `CPCICA`, `ML`, `MLD`, `ISN`, `ECR`, `SITE`, `STYPE`, `BPCA`, `BPCICA`, `TID`, `ADMV`, `VER`, `GUID`, `ENUM`, `TOI`, `CONFIRM` FROM `nat_panther` WHERE `WID`='$v_wid' AND `OID`='$v_oid' AND `AT`='$v_at' AND `CONFIRM`='0'"; ?>

<body>
    <div id="loading"></div>
    <div class="container-fluif" style="margin: 20px;">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel w3-border-theme">
                    <div class="panel-heading w3-theme-d5">
                        <div class="panel-title">
                            <span style="font-size:120%;">Konfirmasi Hasil NAT Worklist ID:<strong>
                                    <?php echo $v_wid; ?></strong></span>
                            <span class="pull-right">
                                <a href="?module=panther_konfirm" id="mnukembali"
                                    class="w3-btn w3-theme-l3 w3-hover-red btn-sm w3-card">Kembali</a>
                            </span>
                        </div>
                    </div>
                    <form name="frmKonfirmasi" id="frmKonfirmasi" method="POST" action="">
                        <input type="hidden" name="wid" value="<?php echo $v_wid; ?>">
                        <input type="hidden" name="oid" value="<?php echo $v_oid; ?>">
                        <input type="hidden" name="at" value="<?php echo $v_at; ?>">
                        <div class="panel-body">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive table-condensed table-hover">
                                        <thead>
                                            <tr class="w3-theme-d3">
                                                <th>No</th>
                                                <th>SAMPLE BARCODE</th>
                                                <th>IC RLU</th>
                                                <th>IC RESULT</th>
                                                <th>RLU</th>
                                                <th>S/CO</th>
                                                <th>RESULT</th>
                                                <th>STYPE</th>
                                                <th>FLAG</th>
                                                <th>KANTONG</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $query = mysqli_query($dbi, $sql);
                                            $no = 0;
                                            $v_reagenname = $v_reagenlot = $v_reagened = $v_operator = "";
                                            while ($dt = mysqli_fetch_assoc($query)) {
                                                $no++;
                                                $stt_kantong = '0';
                                                $sampleid = $dt['SB'];
                                                $statuskantong = "Tidak ada";
                                                $kantong = mysqli_query($dbi, "SELECT `noKantong`,`Status`,`sah`,`StatTempat` FROM `stokkantong` WHERE `noKantong`='$sampleid';");
                                                if (mysqli_num_rows($kantong) > 0) {
                                                    $dtkantong = mysqli_fetch_assoc($kantong);
                                                    $statuskantong = cekstatuskantong($dtkantong['Status'], $dtkantong['sah'], $dtkantong['StatTempat']);
                                                    $stt_kantong = $dtkantong['Status'];
                                                }
                                                $lot_reagen = $dt['ML'];
                                                $exp_reagen = $dt['edreagen'];
                                                $parameter = $dt['AT'];
                                                $sel1 = "selected";
                                                $class_row = $sel2 = $sel3 = $sel4 = "";
                                                if ($dt['OI'] == "Reactive") {
                                                    $class_row = "w3-theme-l4";
                                                    $sel3 = "Selected";
                                                }
                                                if (($dt['OI'] == "Invalid") or ($dt['OI'] == "Error")) {
                                                    $class_row = "w3-theme-l5";
                                                }
                                                if (($dt['OI'] == "Nonreactive") and ($stt_kantong == '1')) {
                                                    $sel2 = 'selected';
                                                }
                                                echo '<tr class="' . $class_row . '">
                                                    <td class="text-right">' . $no . '.</td>
                                                    <td>' . $dt['SB'] . ' <input type="hidden" name="sampleid[]" value="' . $dt['SB'] . '"><input type="hidden" name="guid[]" value="' . $dt['GUID'] . '"></td>
                                                    <td>' . $dt['ICRLU'] . '</td>
                                                    <td>' . $dt['ICR'] . '</td>
                                                    <td>' . $dt['ARLU'] . '</td>
                                                    <td>' . $dt['ASCO'] . '</td>
                                                    <td>' . $dt['OI'] . '</td>
                                                    <td>' . $dt['STYPE'] . '<input type="hidden" name="sampletipe[]" value="' . $dt['STYPE'] . '"></td>
                                                    <td>' . $dt['STAT'] . '</td>
                                                    <td>' . $statuskantong . ' <input type="hidden" name="statuskantong[]" value="' . $stt_kantong . '"> </td>
                                                    <td>
                                                        <select name="aksi[]" class="chosen-select form-control input-sm">
                                                            <option value="1" ' . $sel1 . '>-</option>
                                                            <option value="2" ' . $sel2 . '>Sehat</option>
                                                            <option value="3" ' . $sel3 . '>Cekal</option>
                                                            <option value="4" ' . $sel4 . '>Tunda</option>
                                                        </select>
                                                    </td>
                                                </tr>';
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive table-condensed">
                                        <tr>
                                            <td class="w3-theme-l3">Parameter</td>
                                            <td class="w3-theme"><?php echo $parameter; ?><input type="hidden"
                                                    name="parameter" value="<?php echo $parameter; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td class="w3-theme-l3">Nomor Lot</td>
                                            <td class="w3-theme"><?php echo $lot_reagen; ?><input type="hidden"
                                                    name="raegenlot" value="<?php echo $lot_reagen; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td class="w3-theme-l3">Tanggal ED</td>
                                            <td class="w3-theme"><?php echo $exp_reagen; ?><input type="hidden"
                                                    name="raegened" value="<?php echo $exp_reagen; ?>"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive table-condensed">
                                        <tr>
                                            <td class="w3-theme-l3">Operator Panther</td>
                                            <td class="w3-theme">
                                                <select class="form-control input-sm w3-theme" name="inpOperator"
                                                    id="inpOperator" required>
                                                    <option value="" selected>-</option>
                                                    <?php
                                                    $operator = mysqli_query($dbi, "SELECT `id_user`,`nama_lengkap` FROM `user` WHERE `aktif`='0' AND (`bagian` LIKE '%IMLTD%' OR `bagian` LIKE '%PENGUJIAN%' OR `bagian` LIKE '%LABORATORIUM%' OR `bagian` LIKE '%UJI SARING%')");
                                                    while ($usr = mysqli_fetch_assoc($operator)) {
                                                        echo '<option value="' . $usr['id_user'] . '">' . $usr['nama_lengkap'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w3-theme-l3">Petugas Konfirmasi</td>
                                            <td class="w3-theme">
                                                <select class="form-control input-sm w3-theme" name="inpPtgKonfirmasi"
                                                    id="inpPtgKonfirmasi" required>
                                                    <option value="" selected>-</option>
                                                    <?php
                                                    $operator = mysqli_query($dbi, "SELECT `id_user`,`nama_lengkap` FROM `user` WHERE `aktif`='0' AND (`bagian` LIKE '%IMLTD%' OR `bagian` LIKE '%PENGUJIAN%' OR `bagian` LIKE '%LABORATORIUM%' OR `bagian` LIKE '%UJI SARING%')");
                                                    while ($usr = mysqli_fetch_assoc($operator)) {
                                                        echo '<option value="' . $usr['id_user'] . '">' . $usr['nama_lengkap'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="w3-theme-l3">Disahkan oleh</td>
                                            <td class="w3-theme">
                                                <select class="form-control input-sm w3-theme" name="inpPtgSah"
                                                    id="inpPtgSah" required>
                                                    <option value="" selected>-</option>
                                                    <?php
                                                    $operator = mysqli_query($dbi, "SELECT `id_user`,`nama_lengkap` FROM `user` WHERE `aktif`='0' AND (`bagian` LIKE '%PENGOLAHAN DARAH%' OR `bagian` LIKE '%IMLTD%' OR `bagian` LIKE '%PENGUJIAN%' OR `bagian` LIKE '%LABORATORIUM%' OR `bagian` LIKE '%UJI SARING%')");
                                                    while ($usr = mysqli_fetch_assoc($operator)) {
                                                        echo '<option value="' . $usr['id_user'] . '">' . $usr['nama_lengkap'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-center">
                            <input type="submit" class="w3-btn w3-theme w3-hover-red w3-card" id="simpankonfirmasi"
                                name="simpankonfirmasi" value="Konfirmasi Hasil">
                            <a href="?module=panther_konfirm" id="kembali"
                                class="w3-btn w3-theme-d3 w3-hover-red w3-card">Kembali</a>
                            <a href="?module=panther" id="menuawal"
                                class="w3-btn w3-theme-d3 w3-hover-red w3-card">Kembali ke awal</a>
                            <div class="pesan"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="mProgress" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="text-center">
                            <h4>Tunggu... sedang proses</h4>
                        </div>
                        <div id="mtload" class="text-center" style="display: block;"><img
                                src="release22/images/loading.gif"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="bootsrap337/datepicker/custom.js"></script>
    <script src="bootsrap337/chosen/chosen.jquery.js" type="text/javascript"></script>
    <script>
        $('.chosen-select').chosen({
            width: "100%"
        });
    </script>
    <script>
        $(document).ready(function() {
            function validasiPetugas() {
                var operator = $('#inpOperator').val();
                var konfirmasi = $('#inpPtgKonfirmasi').val();
                var sah = $('#inpPtgSah').val();

                if (operator === "" || konfirmasi === "" || sah === "") {
                    return false;
                }

                if (operator === konfirmasi || operator === sah || konfirmasi === sah) {
                    alert('tidak boleh sama pada setiap pilihan itu');
                    return false;
                }
                return true;
            }

            $('#frmKonfirmasi').submit(function(e) {
                e.preventDefault();

                if (!validasiPetugas()) {
                    return false;
                }

                $("#simpankonfirmasi").prop('value', 'Proses data......');
                $('#simpankonfirmasi').attr('disabled', 'disabled');
                $('#kembali').hide();
                $('#menuawal').hide();
                $('#mnukembali').hide();

                $.ajax({
                    type: 'POST',
                    async: true,
                    url: 'panther_nat/panther_proses.php?m=konfirmasihasil',
                    data: $('#frmKonfirmasi').serialize(),
                    beforeSend: function() {
                        $('#mProgress').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                    },
                    success: function(data) {
                        result = data.split('|');
                        statuius = result[1];
                        notransaksi = result[2];
                        $('.pesan').html(result[0]);
                        $('#kembali').show();
                        $('#menuawal').show();
                        $('#mnukembali').show();
                        $('#mProgress').modal('hide');
                        if (status == 0) {
                            setTimeout(function() {
                                window.location.replace(
                                    '?module=panther_printrslt&notransaksi=' +
                                    notransaksi);
                            }, 500);
                        }
                    }
                });

                return false;
            });

            $('#inpOperator, #inpPtgKonfirmasi, #inpPtgSah').on('change', function() {
                validasiPetugas();
            });
        })
        var load = document.getElementById("loading");
        window.addEventListener('load', function() {
            load.style.display = "none";
        });
    </script>

</body>