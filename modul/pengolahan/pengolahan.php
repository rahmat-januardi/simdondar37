<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Table</title>

    <link href="modul/pengolahan/css/bootstrap/4.5.2/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="../css/bootstrap/4.5.2/bootstrap.min.css" rel="stylesheet"> -->

    <!-- jQuery (Required if using jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- ClockPicker CSS -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">

    <!-- ClockPicker JS -->
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>

    <!-- ClockPicker JS -->
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>

    <link rel="stylesheet" href="modul/pengolahan/css/pengolahan.css">
    <!-- <link rel="stylesheet" href="../css/pengolahan.css"> -->

    <script>
    window.onload = function() {
        document.getElementById('nomorKantong').focus();
    };
    </script>

    <style>
    .table th,
    td {
        padding: 0.1rem;
    }

    .bstatus-slider {
        -webkit-appearance: none;
        appearance: none;
        width: 30%;
        height: 10px;
        border-radius: 4px;
        background: #ccc;
        /* Default warna abu-abu */
        outline: none;
        transition: 0.3s;
    }

    /* Gaya tombol slider */
    .bstatus-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: white;
        cursor: pointer;
        border-radius: 50%;
        transition: 0.3s;
    }


    .form-control {
        width: 100%;
        box-sizing: border-box;
        font-size: 0.8rem;
    }

    .custom-select {
        width: 100%;
        border-radius: 4px;
        font-size: 10px;
        padding: .375rem 1.75rem .375rem .75rem;
        color: #333;
        background-color: #f9f9f9;
    }
    </style>

</head>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("config/dbi_connect.php");
// require_once("../config/dbi_connect.php");
$petugas = $_SESSION['namauser'];

$sql = "SELECT `aPutar`, `aPisah`, `mulaiPutar`, `selesaiPutar`, `mulaiPisah`, `selesaiPisah` FROM dpengolahan_temp WHERE petugas = '$petugas' ORDER BY `id` DESC";
$result = $dbi->query($sql);

//Shift Pengolahan
$shift = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama`,`jam`,`sampai_jam` FROM `shift` WHERE time(now()) between time(`jam`) AND time(`sampai_jam`)"));
$sf = $shift['nama'];

switch ($sf) {
    case 'PAGI':
        $shft = 1;
        break;
    case 'SORE':
        $shft = 2;
        break;
    case 'MALAM':
        $shft = 3;
        break;
    case 'MALAM 2':
        $shft = 4;
        break;
    default:
        $shft = 0;
        break;
}

$isDisabled = ($result->num_rows <= 0) ? 'disabled' : '';
// $mPutar = $sPutar = $mPisah = $sPisah = '00:00'; 
// $mPutar = $sPutar = $mPisah = $sPisah = '';

// versi 5.4 ++
/**
$defaultValues = [
    'mPutar' => '',
    'sPutar' => '',
    'mPisah' => '',
    'sPisah' => ''
];
 */

$defaultValues = array(
    'aPutar' => '',
    'aPisah' => '',
    'mPutar' => '',
    'sPutar' => '',
    'mPisah' => '',
    'sPisah' => ''
);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $aPutar = $row['aPutar'];
    $aPisah = $row['aPisah'];
    $mPutar = substr($row['mulaiPutar'], 0, 5);
    $sPutar = substr($row['selesaiPutar'], 0, 5);
    $mPisah = substr($row['mulaiPisah'], 0, 5);
    $sPisah = substr($row['selesaiPisah'], 0, 5);
} else {
    $aPutar = $defaultValues['aPutar'];
    $aPisah = $defaultValues['aPisah'];
    $mPutar = $defaultValues['mPutar'];
    $sPutar = $defaultValues['sPutar'];
    $mPisah = $defaultValues['mPisah'];
    $sPisah = $defaultValues['sPisah'];
}

if ($aPutar == '') {
    $selaPutar = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pemutaran%'");
    $selaPutar = mysqli_fetch_assoc($selaPutar);
    $alatPutar = $selaPutar['kode'] . " - " . $selaPutar['nama_barang'];
} else {
    $selaPutar = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pemutaran%' AND kode = '$aPutar'");
    $selaPutar = mysqli_fetch_assoc($selaPutar);
    $alatPutar = $selaPutar['kode'] . " - " . $selaPutar['nama_barang'];
}

if ($aPisah == '') {
    $selaPisah = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pemisahan%'");
    $selaPisah = mysqli_fetch_assoc($selaPisah);
    $alatPemisah = $selaPisah['kode'] . " - " . $selaPisah['nama_barang'];
} else {
    $selaPisah = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pemisahan%' AND kode = '$aPisah'");
    $selaPisah = mysqli_fetch_assoc($selaPisah);
    $alatPemisah = $selaPisah['kode'] . " - " . $selaPisah['nama_barang'];
}
?>

<body>
    <div class="container-fluid">
        <h1 class="text-center my-5">Pengolahan Darah (Konvensional)</h1>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="alatPemutaran">Alat Pemutaran:</label>
                    <select class='custom-select' id="alatPemutaran" name="alatPemutaran">
                        <?php
                        $aPutarOptions = mysqli_query($dbi, "SELECT id, kode, nama_barang FROM logbook_h WHERE fungsi LIKE '%pemutaran%'");
                        while ($aP = mysqli_fetch_assoc($aPutarOptions)) {
                            $selected = ($aP['kode'] == $aPutar) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $aP['kode']; ?>" <?php echo $selected; ?>>
                            <?php echo $aP['kode'] . " - " . $aP['nama_barang']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="alatPemisahan">Alat Pemisahan:</label>
                    <select class='custom-select' id="alatPemisahan" name="alatPemisahan">
                        <?php
                        $aPisahOptions = mysqli_query($dbi, "SELECT id, kode, nama_barang FROM logbook_h WHERE fungsi LIKE '%pemisahan%'");
                        while ($aPs = mysqli_fetch_assoc($aPisahOptions)) {
                            $selected = ($aPs['kode'] == $aPisah) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $aPs['kode']; ?>" <?php echo $selected; ?>>
                            <?php echo $aPs['kode'] . " - " . $aPs['nama_barang']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <input id="shift" type="hidden" name="shift" value="<?php echo ($shft); ?>" />
        </div>
        <div class="row">
            <div class="col-sm-2">
                <label for="clockpicker">Pilih Waktu Mulai Pemutaran:</label>
                <input id="jMPutar" type="text" name="jamMulaiPutar" class="form-control"
                    placeholder="Waktu Mulai Pemutaran"
                    value="<?php echo htmlspecialchars($mPutar) ? htmlspecialchars($mPutar) : '00:00'; ?>">
            </div>

            <div class="col-sm-2">
                <label for="clockpicker">Selesai Pemutaran:</label>
                <input id="jSPutar" type="text" name="jamSelesaiPutar" class="form-control"
                    placeholder="Selesai Pemutaran"
                    value="<?php echo htmlspecialchars($sPutar) ? htmlspecialchars($sPutar) : '00:00'; ?>">
                <!-- <label for="jamSelesai" class="form-label">Jam Selesai</label>
                <div id="jamSelesai" class="time-picker" data-coreui-locale="en-US" data-coreui-seconds="false" data-coreui-toggle="time-picker"></div> -->
            </div>
            <div class="col-sm-2">
                <label for="clockpicker">Pilih Waktu Mulai Pemisahan:</label>
                <input id="jMPisah" type="text" name="jamMulaiPisah" class="form-control"
                    placeholder="Waktu Mulai Pemisahan"
                    value="<?php echo htmlspecialchars($mPisah) ? htmlspecialchars($mPisah) : '00:00'; ?>">
            </div>

            <div class="col-sm-2">
                <label for="clockpicker">Selesai Pemisahan:</label>
                <input id="jSPisah" type="text" name="jamSelesaiPisah" class="form-control"
                    placeholder="Selesai Pemisahan"
                    value="<?php echo htmlspecialchars($sPisah) ? htmlspecialchars($sPisah) : '00:00'; ?>">
                <!-- <label for="jamSelesai" class="form-label">Jam Selesai</label>
                <div id="jamSelesai" class="time-picker" data-coreui-locale="en-US" data-coreui-seconds="false" data-coreui-toggle="time-picker"></div> -->
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="nomorKantong">Nomor Kantong:</label>
                    <input type="text" class="form-control" name="nomorKantong" id="nomorKantong"
                        onkeypress="handleKeyPress(event)">
                </div>
            </div>
        </div>

        <form name="pengolahanForm" id="pengolahanForm">
            <table class="table table-striped table-bordered table-responsive" style="font-size: 0.7rem;">
                <thead>
                    <tr>
                        <!-- <th rowspan="2"></th> -->
                        <th rowspan="2" style="vertical-align: middle"></th>
                        <th rowspan="2" style="vertical-align: middle">No.</th>
                        <th rowspan="2" style="vertical-align: middle">No. Kantong</th>
                        <th rowspan="2" style="vertical-align: middle">Tanggal Pengambilan</th>
                        <th rowspan="2" style="vertical-align: middle">Gol. Darah</th>
                        <th colspan="3" style="vertical-align: middle">Jenis</th>
                        <th colspan="4" style="vertical-align: middle">Pemutaran</th>
                        <th rowspan="2" style="vertical-align: middle">Volume</th>
                        <th colspan="4" style="vertical-align: middle">Pengolahan</th>
                        <th colspan="2" style="vertical-align: middle">Pembekuan</th>
                    </tr>
                    <tr>
                        <th>Kantong</th>
                        <th>Komponen</th>
                        <th>Kadaluwarsa Produk</th>
                        <th>Alat</th>
                        <th>Kecepatan (Xg)</th>
                        <th>Suhu (°C)</th>
                        <th>Waktu (menit)</th>
                        <th>Metode</th>
                        <th>Alat</th>
                        <th>Mulai (hh:mm)</th>
                        <th>Selesai (hh:mm)</th>
                        <th>Dilakukan?</th>
                        <th>Suhu Inti</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php include 'pengolahan_data.php'; ?>
                </tbody>
            </table>
            <button class="btn btn-success mb-3" type="button" onclick="simpanDanLanjutkan()"
                <?php echo $isDisabled; ?>>
                Simpan dan Lanjutkan
            </button>
            &NonBreakingSpace;
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="cetakLabelOption" id="cetakLabel1Kolom"
                    value="1Kolom">
                <label class="form-check-label" for="cetakLabel1Kolom">Cetak Label 1 Kolom</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="cetakLabelOption" id="cetakLabel2Kolom"
                    value="2Kolom" checked>
                <label class="form-check-label" for="cetakLabel2Kolom">Cetak Label 2 Kolom</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="cetakLabelOption" id="tidakCetakLabel"
                    value="tidakCetak" checked>
                <label class="form-check-label" for="tidakCetakLabel">Tidak Cetak Label</label>
            </div>
        </form>
    </div>

    <div class="modal fade" id="suksesModal" tabindex="-1" role="dialog" aria-labelledby="suksesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Berhasil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Pesan kesalahan boleh diisi di sini gess -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Terjadi Kesalahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Pesan kesalahan boleh diisi di sini gess -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">HAPUS DATA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus baris Nomor Kantong <b id="modalNoKantong"></b> ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script src="modul/pengolahan/js/aksiPengolahan.js" defer></script>
    <!-- <script src="../js/aksiPengolahan.js" defer></script> -->

    <script>
    function getSelectedCetakLabelOption() {
        return document.querySelector('input[name="cetakLabelOption"]:checked').value;
    }

    function simpanDanLanjutkan() {
        var formData = new FormData(document.getElementById('pengolahanForm'));

        $.ajax({
            url: 'modul/pengolahan/prosesPengolahan.php',
            // url: 'prosesPengolahan.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var jsonResponse = response;

                    if (jsonResponse.status === 'success') {
                        $('#suksesModal .modal-body').html(jsonResponse.message);
                        $('#suksesModal').modal('show');

                        $('#suksesModal').on('hidden.bs.modal', function() {
                            // var selectedOption = $('input[name="cetakLabelOption"]:checked').val();
                            var selectedOption = getSelectedCetakLabelOption();

                            if (jsonResponse.noTrans) {
                                var selectedOption = getSelectedCetakLabelOption();
                                if (selectedOption === 'tidakCetak') {
                                    window.location.href = 'pmikomponen.php?module=pengolahan';
                                    return;
                                } else {
                                    var labelUrl = selectedOption === '1Kolom' ?
                                        'labelPengolahan1Kolom.php?nT=' + encodeURIComponent(
                                            jsonResponse.noTrans) +
                                        '&barcode=C128&transaksi=transaksi' :
                                        'labelPengolahan2Kolom.php?nT=' + encodeURIComponent(
                                            jsonResponse.noTrans) +
                                        '&barcode=C128&transaksi=transaksi';
                                }

                            } else {
                                console.error('Error: noTrans value is missing in the response.');
                                return;
                            }

                            var cetakLabelModal = `
                                <div class="modal fade" id="cetakLabelModal" tabindex="-1" role="dialog" aria-labelledby="cetakLabelModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #912200; color: white;">
                                                <h5 class="modal-title text-center w-100" id="cetakLabelModalLabel">Label Komponen</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                            </div>
                                            <div class="modal-body">
                                                <iframe src="${labelUrl}" frameborder="0" style="width: 100%; height: 400px;"></iframe>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                `;
                            $('body').append(cetakLabelModal);
                            $('#cetakLabelModal').modal('show');

                            $('#cetakLabelModal').on('hidden.bs.modal', function() {
                                window.location.href = 'pmikomponen.php?module=pengolahan';
                            });
                        });
                    } else {
                        $('#errorModal .modal-body').html(jsonResponse.message);
                        $('#errorModal').modal('show');
                    }
                } catch (e) {
                    $('#errorModal .modal-body').html('Terjadi kesalahan saat memproses respons.');
                    $('#errorModal').modal('show');
                }

            }
        });
    }
    </script>

    <script>
    $(document).ready(function() {
        $('#jMPutar, #jSPutar, #jMPisah, #jSPisah').clockpicker({
            autoclose: true,
            placement: 'bottom',
            align: 'left',
            donetext: 'Selesai',
            twelvehour: false
        });
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        updateSliders();

        document.querySelectorAll(".bstatus-slider").forEach(function(slider) {
            slider.addEventListener("input", function() {
                updateSliderColor(this);
            });
        });
    });

    // Fungsi untuk memperbarui warna slider
    function updateSliders() {
        document.querySelectorAll(".bstatus-slider").forEach(updateSliderColor);
    }

    function updateSliderColor(slider) {
        if (slider.value == "1") {
            slider.style.background = "#2196F3";
        } else {
            slider.style.background = "red";
        }
    }
    </script>


</body>

</html>