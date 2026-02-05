<!DOCTYPE html>
<html lang="en">
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootsrap337/bspmi.css">
    <link rel="stylesheet" href="bootsrap337/w3.css">
    <link rel="stylesheet" href="pmf/pmfstyle.css">
    <link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
    <link href="bootsrap337/datepicker/css/bootstrap-datepicker.css" rel="stylesheet">
    <link rel="stylesheet" href="bootsrap337/chosen/chosen.css">
    <link href="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.css" rel="stylesheet">
    <style>
    .shadow {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .btn-pref .btn {
        border-radius: 0 !important;
    }

    .modal-fullscreen {
        width: 98%;
        padding: 0;
    }

    .modal-content {
        /* min-height: 90%; */
        width: 100%;
        border-radius: 25;
        margin: 0 0;
    }

    .modal-footer {
        border-radius: 25;
        bottom: 0px;
        position: relative;
        width: 100%;
    }

    .form-group {
        margin-top: 1px;
        margin-bottom: 1px;
    }

    .table thead th {
        height: 40px;
        padding: 2px !important;
        text-align: center !important;
        vertical-align: middle !important;
        text-shadow: 1px 1px 2px black;
        font-size: 1.2em;
        /* word-break:break-all; */
    }

    .table tbody td {
        font-size: 1em;
        white-space: nowrap;
        vertical-align: middle !important;
    }

    .text-vertical {
        vertical-align: middle;
        text-align: center;
        transform: rotate(-90deg);
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

    a {
        text-decoration: none !important;
    }

    .table td.text {
        max-width: 300px;
    }

    .table td.text span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        max-width: 100%;
    }

    .swal2-popup {
        font-size: 1.6rem !important;
    }

    .swal-footer {
        text-align: center;
    }

    .custom-swal {
        background: linear-gradient(to bottom, white, red) !important;
        border-radius: 15px !important;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.5) !important;
        width: 800px !important;
        max-width: 95% !important;
        padding: 20px !important;
        overflow: hidden;
    }
    </style>
</head>
<style>
.bayangan {
    border: 0.2px solid;
    padding: 1px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 1px 2px 0 rgba(0, 0, 0, 0.19);
    "

}
</style>

<?php
include('config/dbi_connect.php');

$td0 = php_uname('n');
$td0 = strtoupper($td0);
$td0 = substr($td0, 0, 1);
switch ($td0) {
    case 'M':$nt = "M";
        break;
    case 'S':$nt = "D";
        break;
    default: $nt = "";
        break;
}

$query = ("SELECT * FROM htransaksi where `Status` !='2' and `Status` !='-' and `cek_hb`='1' and `cek_dokter`='1' and jumHB='1' and  NoTrans like '$nt%' and date(Tgl)>= current_date -3 and date(Tgl) <= current_date order by Tgl DESC");
//if (substr($td0,0,1)=='M')  $query = ("SELECT * FROM htransaksi where NoTrans like '$td0%' and Status='1' and jumHB='1' ");
$hasil = mysqli_query($dbi, $query);
?>

<body>
    <div class="container-fluid" style="margin: 15px;">
        <div class="row">
            <div class="col-md-12">
                <div class="panel w3-border-theme shadow">
                    <div class="panel-heading w3-theme-d5 clearfix">
                        <div class="col-lg-9 col-md-8 col-sm-7 col-xs-8 text-left text-shadow"
                            style="font-size: 150%;font-weight: bold;">DATA ANTRIAN AFTAP (PENGAMBILAN DARAH)</div>

                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">

                                <table
                                    class="table table-responsive table-bordered table-striped table-md table-hover table-md display"
                                    id="dtaudittrail">
                                    <thead class="w3-theme-d4" style="height: 40px;">
                                        <tr>
                                            <td>No</td>
                                            <td>No Registrasi</td>
                                            <td>Nama Pendonor</td>
                                            <td>Kode Pendonor</td>
                                            <td>Tgl Lahir</td>
                                            <td>Gol Darah</td>
                                            <td>Jenis Donor</td>
                                            <td>Donor Ulang/Baru</td>
                                            <td>Jenis Pengambilan</td>
                                            <td>Tindakan</td>
                                            <td>Sampel</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
    $no = 0;
while ($data = mysqli_fetch_assoc($hasil)) {
    $no++;
    $jenis_pemngambilan = '0';
    if ($data['apheresis'] == '1') {
        $jenis_pemngambilan = '1';
    }
    if ($data['donor_tpk'] == '1') {
        $jenis_pemngambilan = '2';
    }
    switch ($jenis_pemngambilan) {
        case '0': $jenis_pemngambilan1 = 'Biasa';
            break;
        case '1': $jenis_pemngambilan1 = 'Apheresis';
            break;
        case '2': $jenis_pemngambilan1 = 'Plasma Konvalesen';
            break;
    }

    if ($data['JenisDonor'] == '1') {
        $dsdp = 'Pengganti';
    } else {
        $dsdp = 'Sukarela';
    }
    if ($data['donorbaru'] == '1') {
        $lmbr = 'Ulang';
    } else {
        $lmbr = 'Baru';
    }
    $data['KodePendonor'] = str_replace("'", "\'", $data['KodePendonor']);
    $query1 = mysqli_query($dbi, "SELECT * FROM pendonor where Kode='$data[KodePendonor]'");
    $hasil1 = mysqli_fetch_array($query1);
    echo "
            <tr class='record' style='height:30px;'>
                <td>".$no."</td>
                <td>".$data['NoTrans']."</td>
                <td align=left nowrap>";
    switch ($jenis_pemngambilan) {
        case '0':echo "<a href=pmi".$_SESSION['leveluser'].".php?module=pengambilan_alat&NoTrans=".$data['NoTrans']." title='Klik untuk melanjutkan ke pengambilan DONOR BIASA'>".$hasil1['Nama']."</a>";
            break;
        case '1':echo "<a href=pmi".$_SESSION['leveluser'].".php?module=pengambilan_apheresis&NoTrans=".$data['NoTrans']." title='Klik untuk melanjutkan ke pengambilan donor APHERESIS'>".$hasil1['Nama']."</a>";
            break;
        case '2':echo "<a href=pmi".$_SESSION['leveluser'].".php?module=pengambilan_apheresis&NoTrans=".$data['NoTrans']." title='Klik untuk melanjutkan ke pengambilan donor APHERESIS PLASMA KONVALESEN'>".$hasil1['Nama']."</a>";
            break;
    }
    echo "  </td>
                <td align=left>".$hasil1['Kode']."</td>
                <td>".$hasil1['TglLhr']."</td>
                <td>".$data['gol_darah'].$data['rhesus']."</td>
                <td>".$dsdp."</td>
                <td>".$lmbr."</td>
                <td align=left>".$jenis_pemngambilan1."</td>
                <td><a href=pmi".$_SESSION['leveluser'].".php?module=deltransaksi&NoTrans=".$data['NoTrans']." title='Batalkan transaksi pengambilan'>Batalkan</a></td>
                <td>".$data['id_sample']."</td>
            </tr>";
}
echo "</tbody></table>";
?>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="font-size: 10px;color: #ff0000;text-shadow: 0px 0px 1px #000000;">Update 2025-04-16</div>
            </div>
        </div>
    </div>
    </div>

</body>
<script src="bootsrap337/js/jquery.min.js"></script>
<script src="bootsrap337/js/bootstrap.min.js"></script>
<script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="bootsrap337/datepicker/custom.js"></script>
<script src="bootsrap337/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.js"></script>
<script src="bootsrap337/sweetalert2/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    setDateRangePicker(".startdate", ".enddate")
    var table = $('#dtaudittrail').DataTable({
        lengthMenu: [
            [10, 15, 25, 50, -1],
            [10, 15, 25, 50, 'All']
        ]
    });
});

$('.chosen-select').chosen({
    width: "100%"
});

function setDateRangePicker(start, end) {
    $(start).datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    }).on('changeDate', function(selected) {
        var startDate = new Date(selected.date.valueOf());
        $(end).datepicker('setStartDate', startDate);
        if ($(end).val() === '') {
            $(end).datepicker('setDate', startDate);
        }
    });

    $(end).datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    }).on('changeDate', function(selected) {
        var endDate = new Date(selected.date.valueOf());
        $(start).datepicker('setEndDate', endDate);
    });
}
</script>