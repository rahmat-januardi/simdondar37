<?php
/*
SIMDONDAR 3.7
05-2025
*/

error_reporting(E_ALL & ~E_NOTICE);

include 'clogin.php';
include 'config/dbi_connect.php';

if (!isset($_SESSION)) {
    session_start();
}

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function selected_attr($current, $value)
{
    return ((string)$current === (string)$value) ? ' selected="selected"' : '';
}

$tanggal1 = date('Y-m-d');

// Default values
$v_tanggal     = $tanggal1;
$v_tipebarcode = 'C128';
$v_jenislabel  = '1';
$v_merk        = '';
$v_volume      = '350';
$v_jenis       = '1';
$v_metode      = ' ';
$v_lot         = '';
$v_tgled       = '';
$v_jmlcetak    = '1';
$v_jmlbarcode  = '';
$v_nokantong   = '';

// Ambil data dari POST jika submit
if (isset($_POST['cetak'])) {
    $v_nokantong   = isset($_POST['nokantong']) ? $_POST['nokantong'] : '';
    $v_jenislabel  = isset($_POST['jenislabel']) ? $_POST['jenislabel'] : $v_jenislabel;
    $v_jmlcetak    = isset($_POST['jmlcetak']) ? $_POST['jmlcetak'] : $v_jmlcetak;
    $v_jmlbarcode  = isset($_POST['jmlbarcode']) ? $_POST['jmlbarcode'] : $v_jmlbarcode;
    $v_tgled       = isset($_POST['tgled']) ? $_POST['tgled'] : $v_tgled;
    $v_lot         = isset($_POST['lot']) ? $_POST['lot'] : $v_lot;
    $v_jenis       = isset($_POST['jenis']) ? $_POST['jenis'] : $v_jenis;
    $v_volume      = isset($_POST['volume']) ? $_POST['volume'] : $v_volume;
    $v_merk        = isset($_POST['merk']) ? $_POST['merk'] : $v_merk;
    $v_tanggal     = isset($_POST['tanggal']) ? $_POST['tanggal'] : $v_tanggal;
    $v_tipebarcode = isset($_POST['tipebarcode']) ? $_POST['tipebarcode'] : $v_tipebarcode;
    $v_metode      = isset($_POST['metode']) ? $_POST['metode'] : $v_metode;
} else {
    // Ambil data terakhir dari tempudd jika ada
    $temp = mysqli_query($dbi, "SELECT * FROM `tempudd` WHERE `modul`='BARCODE' LIMIT 1");
    if ($temp && mysqli_num_rows($temp) > 0) {
        $row = mysqli_fetch_assoc($temp);

        $v_jenislabel  = isset($row['dokter']) ? $row['dokter'] : $v_jenislabel;
        $v_jmlcetak    = isset($row['petugas1']) ? $row['petugas1'] : $v_jmlcetak;
        $v_tgled       = isset($row['petugas2']) ? $row['petugas2'] : $v_tgled;
        $v_lot         = isset($row['alamat']) ? $row['alamat'] : $v_lot;
        $v_jenis       = isset($row['wilayah']) ? $row['wilayah'] : $v_jenis;
        $v_volume      = isset($row['kelurahan']) ? $row['kelurahan'] : $v_volume;
        $v_merk        = isset($row['petugas3']) ? $row['petugas3'] : $v_merk;
        $v_tipebarcode = isset($row['kecamatan']) ? $row['kecamatan'] : $v_tipebarcode;
    }
}

if (isset($v_tanggal) && $v_tanggal !== '') {
    $tanggal1 = $v_tanggal;
}

$arr_tipebarcode = array(
    'C128'  => 'CODE 128 AUTO',
    'C128A' => 'CODE 128 A',
    'C128B' => 'CODE 128 B',
    'C39'   => 'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9',
    'C39E'  => 'CODE 39 EXTENDED',
    'C93'   => 'CODE 93 - USS-93'
);

$arr_jenislabel = array(
    '1' => '1 BARIS LABEL',
    '2' => '2 BARIS LABEL'
);

$arr_status = array(
    '1'  => 'Single',
    '2'  => 'Double',
    '3'  => 'Triple',
    '4'  => 'Quadruple',
    '5'  => 'Pediatrik/Quintuple',
    '6'  => 'Sextuple',
    '7'  => 'Septuple',
    '8'  => 'Octuple',
    '9'  => 'Nonuple',
    '10' => 'Decuple'
);

$arr_metode = array(
    ' '    => 'BIASA',
    'TT'   => 'TOP & TOP',
    'TTF'  => 'TOP & TOP FILTER',
    'TB'   => 'TOP & BOTTOM',
    'TBF'  => 'TOP & BOTTOM FILTER',
    'LR'   => 'Leukocyte Reduced',
    'NLR'  => 'Non-Leukocyte Reduced',
    'AP'   => 'APHERESIS',
    'APLR' => 'APHERESIS LEUKOCYTE REDUCED',
    'APTB' => 'APHERESIS TOP & BOTTOM',
    'APTBF' => 'APHERESIS TOP & BOTTOM FILTER'
);

$merk_result = mysqli_query($dbi, "SELECT `mk_merk` FROM `merk_kantong` ORDER BY `mk_merk` ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Barcode Kantong Otomatis Multi</title>

    <link rel="stylesheet" href="bootsrap337/w3.css">
    <link rel="stylesheet" href="pmf/pmfstyle.css">
    <link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootsrap337/datepicker/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="bootsrap337/chosen/chosen.css">
    <link rel="stylesheet" href="barcodekantong/loader.css">

    <style>
    .shadow {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .form-group {
        margin-top: 2px;
        margin-bottom: 2px;
    }

    .modal-header {
        padding: 9px 15px;
        border-bottom: 1px solid #eee;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .table thead th {
        height: 40px;
        font-size: 14px;
        text-align: center;
        vertical-align: middle !important;
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

    a {
        text-decoration: none !important;
    }

    .checkbox label:after {
        content: '';
        display: table;
        clear: both;
    }

    .checkbox .cr {
        position: relative;
        display: inline-block;
        border: 2px solid #3f51b5;
        border-radius: .25em;
        width: 2em;
        height: 2em;
        float: left;
        margin-right: 1em;
    }

    .checkbox .cr .cr-icon {
        position: absolute;
        font-size: 1.5em;
        line-height: 0;
        top: 50%;
        left: 0;
    }

    .checkbox label input[type="checkbox"] {
        display: none;
    }

    .checkbox label input[type="checkbox"]+.cr>.cr-icon {
        opacity: 0;
    }

    .checkbox label input[type="checkbox"]:checked+.cr>.cr-icon {
        opacity: 1;
    }

    .checkbox label input[type="checkbox"]:disabled+.cr {
        opacity: .5;
    }
    </style>
</head>

<body style="font-size:13px;">
    <div id="loading"></div>

    <div class="container-fluid" style="margin: 30px;">
        <div class="row">
            <div class="col-xs-12 col-sm-7 col-md-6 col-lg-6">
                <div class="panel w3-border-theme shadow">
                    <div class="panel-heading w3-theme-d5">
                        <div class="panel-title">
                            <span style="font-size:120%;">Barcode Kantong Otomatis Multi</span>
                            <span class="pull-right">
                                <a href="?module=pmf_barcode_auto"
                                    class="w3-btn w3-theme-l3 w3-hover-yellow w3-card btn-sm">Kembali</a>
                            </span>
                        </div>
                    </div>

                    <form class="form-horizontal" method="post" action="" id="frmkantong" name="frmkantong">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="tanggal" class="control-label col-xs-3">Tanggal</label>
                                <div class="col-xs-4">
                                    <input type="text" class="form-control input-sm w3-white" name="tanggal"
                                        id="tanggal" value="<?php echo h($tanggal1); ?>" readonly="readonly">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tipebarcode" class="control-label col-xs-3">Barcode</label>
                                <div class="col-xs-9">
                                    <select class="form-control chosen-select" name="tipebarcode" id="tipebarcode">
                                        <?php foreach ($arr_tipebarcode as $val => $cap) { ?>
                                        <option value="<?php echo h($val); ?>"
                                            <?php echo selected_attr($v_tipebarcode, $val); ?>><?php echo h($cap); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="jenislabel" class="control-label col-xs-3">Jenis label</label>
                                <div class="col-xs-9">
                                    <select class="form-control chosen-select" name="jenislabel" id="jenislabel">
                                        <?php foreach ($arr_jenislabel as $val => $cap) { ?>
                                        <option value="<?php echo h($val); ?>"
                                            <?php echo selected_attr($v_jenislabel, $val); ?>><?php echo h($cap); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="merk" class="control-label col-xs-3">Merk</label>
                                <div class="col-xs-9">
                                    <select class="form-control chosen-select" name="merk" id="merk">
                                        <option value="">-- PILIH MERK --</option>
                                        <?php
                                        if ($merk_result && mysqli_num_rows($merk_result) > 0) {
                                            while ($row_merk = mysqli_fetch_assoc($merk_result)) {
                                                $merk_nama = $row_merk['mk_merk'];
                                        ?>
                                        <option value="<?php echo h($merk_nama); ?>"
                                            <?php echo selected_attr($v_merk, $merk_nama); ?>>
                                            <?php echo h($merk_nama); ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="volume" class="control-label col-xs-3">Volume</label>
                                <div class="col-xs-9">
                                    <select class="form-control chosen-select" name="volume" id="volume">
                                        <?php for ($x = 350; $x <= 1000; $x += 50) { ?>
                                        <option value="<?php echo $x; ?>" <?php echo selected_attr($v_volume, $x); ?>>
                                            <?php echo $x; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="jenis" class="control-label col-xs-3">Jenis</label>
                                <div class="col-xs-9">
                                    <select class="form-control chosen-select" name="jenis" id="jenis">
                                        <?php foreach ($arr_status as $val => $cap) { ?>
                                        <option value="<?php echo h($val); ?>"
                                            <?php echo selected_attr($v_jenis, $val); ?>><?php echo h($cap); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="metode" class="control-label col-xs-3">Metode</label>
                                <div class="col-xs-9">
                                    <select class="form-control chosen-select" name="metode" id="metode">
                                        <?php foreach ($arr_metode as $val => $cap) { ?>
                                        <option value="<?php echo h($val); ?>"
                                            <?php echo selected_attr($v_metode, $val); ?>>
                                            <?php echo h(strtoupper($cap)); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lot" class="control-label col-xs-3">No LOT</label>
                                <div class="col-xs-4">
                                    <input id="lot" name="lot" type="text" class="form-control input-sm"
                                        value="<?php echo h($v_lot); ?>" required="required">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tgled" class="control-label col-xs-3">Tgl ED</label>
                                <div class="col-xs-4">
                                    <input id="tgled" name="tgled" type="text" class="form-control input-sm datepicker"
                                        value="<?php echo h($v_tgled); ?>" required="required">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="jmlcetak" class="control-label col-xs-3">Jml Label A</label>
                                <div class="col-xs-2">
                                    <input id="jmlcetak" name="jmlcetak" type="text"
                                        class="form-control input-sm onlynumber" value="<?php echo h($v_jmlcetak); ?>"
                                        required="required" min="1">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="jmlkantong" class="control-label col-xs-3">Jml Kantong</label>
                                <div class="col-xs-2">
                                    <input id="jmlkantong" name="jmlkantong" type="text"
                                        class="form-control input-sm onlynumber" value="10" required="required" min="1">
                                </div>
                                <div class="col-xs-2">
                                    <div class="loader" style="display:none;"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-xs-3"></label>
                                <div class="col-xs-9">
                                    <div class="checkbox">
                                        <label class="control-label" style="margin-left:-20px;">
                                            <input type="checkbox" checked="checked" value="1" name="ChkLabelKantong"
                                                id="ChkLabelKantong">
                                            <span class="cr w3-hover-theme"><i
                                                    class="cr-icon glyphicon glyphicon-ok"></i></span>
                                            <span class="w3-large w3-hover-text-theme">Informasi Kantong</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-xs-3"></label>
                                <div class="col-xs-9">
                                    <div class="checkbox">
                                        <label class="control-label" style="margin-left:-20px;">
                                            <input type="checkbox" checked="checked" value="1" name="ChkInfo"
                                                id="ChkInfo">
                                            <span class="cr w3-hover-theme"><i
                                                    class="cr-icon glyphicon glyphicon-ok"></i></span>
                                            <span class="w3-large w3-hover-text-theme">Label info</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-xs-3"></label>
                                <div class="col-xs-9">
                                    <div class="checkbox">
                                        <label class="control-label" style="margin-left:-20px;">
                                            <input type="checkbox" checked="checked" value="1" name="ChkLabelPemisah"
                                                id="ChkLabelPemisah">
                                            <span class="cr w3-hover-theme"><i
                                                    class="cr-icon glyphicon glyphicon-ok"></i></span>
                                            <span class="w3-large w3-hover-text-theme">Label pemisah (label
                                                kosong)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer">
                            <div class="form-group row">
                                <div class="col-xs-offset-3 col-xs-9">
                                    <button name="cetak" id="cetak" type="submit"
                                        class="w3-btn w3-theme-d3 w3-hover-yellow">Simpan &amp; Cetak</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlcetak" role="dialog">
        <div class="modal-dialog modal-xs" role="document">
            <div class="modal-content">
                <div class="modal-header w3-theme-d5 shadow">
                    <button type="button" class="close btn-sm btn-default" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="color:white;">Cetak Barcode</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-data"></div>
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
        width: '100%'
    });

    $(document).ready(function() {
        if (typeof setDatePicker === 'function') {
            setDatePicker();
        }
        if (typeof setDateRangePicker === 'function') {
            setDateRangePicker('.startdate', '.enddate');
        }
        if (typeof setMonthPicker === 'function') {
            setMonthPicker();
        }
        if (typeof setYearPicker === 'function') {
            setYearPicker();
        }
        if (typeof setYearRangePicker === 'function') {
            setYearRangePicker('.startyear', '.endyear');
        }

        $('#frmkantong').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                data: $('#frmkantong').serialize(),
                url: 'barcodekantong/barcodeproses.php?m=cetakbarcodeauto',
                beforeSend: function() {
                    $('.loader').show();
                },
                success: function(data) {
                    $('#mdlcetak .modal-data').html(data);
                    $('#mdlcetak').modal('show');
                },
                error: function(xhr) {
                    $('#mdlcetak .modal-data').html(xhr.responseText);
                    $('#mdlcetak').modal('show');
                }
            });
        });

        $('#mdlcetak').on('hidden.bs.modal', function() {
            $('.loader').hide();
        });
    });

    $('.onlynumber').on('keydown', function(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode !== 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    window.addEventListener('load', function() {
        var load = document.getElementById('loading');
        if (load) {
            load.style.display = 'none';
        }
    });
    </script>
</body>

</html>