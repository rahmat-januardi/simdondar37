<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengolahan Komponen</title>

    <link href="modul/pengolahan/css/bootstrap/4.5.2/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="../css/bootstrap/4.5.2/bootstrap.min.css" rel="stylesheet"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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

$sql = "SELECT `aPutar`, `aPisah`, `aBeku`, `mulaiPutar`, `selesaiPutar`, `mulaiPisah`, `selesaiPisah`, `mulaiBeku`, `selesaiBeku`, `tglPengerjaan` FROM dpengolahan_temp WHERE petugas = '$petugas' ORDER BY `id` DESC";
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

// $defaultValues = [
//     'mPutar' => '',
//     'sPutar' => '',
//     'mPisah' => '',
//     'sPisah' => ''
// ];

$defaultValues = array(
    'aPutar' => '',
    'aPisah' => '',
    'aBeku'  => '',
    'tglPengerjaan' => '',
    'mPutar' => '',
    'sPutar' => '',
    'mPisah' => '',
    'sPisah' => '',
    'mBeku'  => '',
    'sBeku'  => ''
);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $aPutar = $row['aPutar'];
    $aPisah = $row['aPisah'];
    $aBeku  = $row['aBeku'];
    $tglPengerjaan = $row['tglPengerjaan'];
    $mPutar = substr($row['mulaiPutar'], 0, 5);
    $sPutar = substr($row['selesaiPutar'], 0, 5);
    $mPisah = substr($row['mulaiPisah'], 0, 5);
    $sPisah = substr($row['selesaiPisah'], 0, 5);
    $mBeku = $row['mulaiBeku'] ? substr($row['mulaiBeku'], 0, 5) : '00:00';
    $sBeku = $row['selesaiBeku'] ? substr($row['selesaiBeku'], 0, 5) : '00:00';
} else {
    $aPutar = $defaultValues['aPutar'];
    $aPisah = $defaultValues['aPisah'];
    $aBeku  = $defaultValues['aBeku'];
    $tglPengerjaan = $defaultValues['tglPengerjaan'];
    $mPutar = $defaultValues['mPutar'];
    $sPutar = $defaultValues['sPutar'];
    $mPisah = $defaultValues['mPisah'];
    $sPisah = $defaultValues['sPisah'];
    $mBeku  = $defaultValues['mBeku'];
    $sBeku  = $defaultValues['sBeku'];
}

// if ($aPutar == '') {
//     $selaPutar = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pemutaran%'");
//     $selaPutar = mysqli_fetch_assoc($selaPutar);
//     $alatPutar = $selaPutar['kode'] . " - " . $selaPutar['nama_barang'];
// } else {
//     $selaPutar = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pemutaran%' AND kode = '$aPutar'");
//     $selaPutar = mysqli_fetch_assoc($selaPutar);
//     $alatPutar = $selaPutar['kode'] . " - " . $selaPutar['nama_barang'];
// }

// if ($aPisah == '') {
//     $selaPisah = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pemisahan%'");
//     $selaPisah = mysqli_fetch_assoc($selaPisah);
//     $alatPemisah = $selaPisah['kode'] . " - " . $selaPisah['nama_barang'];
// } else {
//     $selaPisah = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pemisahan%' AND kode = '$aPisah'");
//     $selaPisah = mysqli_fetch_assoc($selaPisah);
//     $alatPemisah = $selaPisah['kode'] . " - " . $selaPisah['nama_barang'];
// }

// if ($aBeku == '') {
//     $selaBeku = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pembekuan%'");
//     $selaBeku = mysqli_fetch_assoc($selaBeku);
//     $alatBeku = $selaBeku['kode'] . " - " . $selaBeku['nama_barang'];
// } else {
//     $selaBeku = mysqli_query($dbi, "SELECT `id`, `kode`, `nama_barang` FROM logbook_h WHERE `fungsi` LIKE '%pembekuan%' AND kode = '$aBeku'");
//     $selaBeku = mysqli_fetch_assoc($selaBeku);
//     $alatBeku = $selaBeku['kode'] . " - " . $selaBeku['nama_barang'];
// }

// Tentukan mode default berdasarkan query
$modeChecked = "lengkap"; // default

if ($mBeku == "00:00" && $mPutar != "00:00") {
    $modeChecked = "putarPisah";
} elseif ($mBeku == "00:00" && $mPutar == "00:00") {
    $modeChecked = "wb";
}
?>

<body>
    <div class="container-fluid">
        <h1 class="text-center my-5">Pengolahan Darah (Konvensional)</h1>

        <div class="row mb-3">
            <div class="col-sm-12">
                <label><b>Pilih Mode Pengerjaan:</b></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="modePengerjaan" id="mode1" value="wb"
                        <?php echo ($modeChecked == "wb" ? "checked" : ""); ?>>
                    <label class="form-check-label" for="mode1">WB</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="modePengerjaan" id="mode2" value="putarPisah"
                        <?php echo ($modeChecked == "putarPisah" ? "checked" : ""); ?>>
                    <label class="form-check-label" for="mode2">Putar + Pisah</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="modePengerjaan" id="mode3" value="lengkap"
                        <?php echo ($modeChecked == "lengkap" ? "checked" : ""); ?>>
                    <label class="form-check-label" for="mode3">Putar + Pisah + Beku</label>
                </div>
            </div>
        </div>


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
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="alatPembekuan">Alat Pembekuan:</label>
                    <select class='custom-select' id="alatPembekuan" name="alatPembekuan">
                        <?php
                        $aBekuOptions = mysqli_query($dbi, "SELECT id, kode, nama_barang FROM logbook_h WHERE fungsi LIKE '%pembekuan%'");
                        while ($aBk = mysqli_fetch_assoc($aBekuOptions)) {
                            $selected = ($aBk['kode'] == $aBeku) ? 'selected' : ''; // ? benar

                        ?>
                            <option value="<?php echo $aBk['kode']; ?>" <?php echo $selected; ?>>
                                <?php echo $aBk['kode'] . " - " . $aBk['nama_barang']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <input id="shift" type="hidden" name="shift" value="<?php echo ($shft); ?>" />
        </div>
        <div class="row">
            <!-- Bagian Waktu Pemutaran -->
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

            <!-- Bagian Waktu Pemisahan -->
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

            <!-- Bagian Waktu Pembekuan dan suhu -->
            <div class="col-sm-2">
                <label for="clockpicker">Pilih Waktu Mulai Pembekuan:</label>
                <input id="jMBeku" type="text" name="jamMulaiBeku" class="form-control"
                    placeholder="Waktu Mulai Pembekuan"
                    value="<?php echo htmlspecialchars($mBeku) ? htmlspecialchars($mBeku) : '00:00'; ?>">
            </div>
            <div class="col-sm-2">
                <label for="clockpicker">Selesai Pemebkuan:</label>
                <input id="jSBeku" type="text" name="jamSelesaiBeku" class="form-control"
                    placeholder="Selesai Pembekuan"
                    value="<?php echo htmlspecialchars($sBeku) ? htmlspecialchars($sBeku) : '00:00'; ?>">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="tglPengerjaan">Tanggal & Waktu Pengerjaan:</label>
                    <input type="text" id="tglPengerjaan" name="tglPengerjaan" class="form-control"
                        value="<?php echo htmlspecialchars($tglPengerjaan); ?>">


                </div>
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
                    <tr style="vertical-align: middle">
                        <!-- <th rowspan="2"></th> -->
                        <th rowspan="2"></th>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">No. Kantong</th>
                        <th rowspan="2">Tanggal Pengambilan</th>
                        <th rowspan="2">Tanggal Pengerjaan</th>
                        <th rowspan="2">Gol. Darah</th>
                        <th colspan="3">Jenis</th>
                        <th rowspan="2">Volume</th>
                        <th colspan="4" class="col-pemutaran">Pemutaran</th>
                        <th colspan="4" class="col-pengolahan">Pengolahan</th>
                        <th colspan="4" class="col-pembekuan">Pembekuan</th>
                    </tr>
                    <tr>
                        <th>Kantong</th>
                        <th>Komponen</th>
                        <th>Kadaluwarsa Produk</th>

                        <!-- Subheader pakai class -->
                        <th class="col-pemutaran">Alat</th>
                        <th class="col-pemutaran">Kecepatan (Xg)</th>
                        <th class="col-pemutaran">Suhu (°C)</th>
                        <th class="col-pemutaran">Waktu (menit)</th>

                        <th class="col-pengolahan">Metode</th>
                        <th class="col-pengolahan">Alat</th>
                        <th class="col-pengolahan">Mulai (hh:mm)</th>
                        <th class="col-pengolahan">Selesai (hh:mm)</th>

                        <th class="col-pembekuan">Alat</th>
                        <th class="col-pembekuan">Mulai (hh:mm)</th>
                        <th class="col-pembekuan">Selesai (hh:mm)</th>
                        <th class="col-pembekuan">Suhu Inti</th>
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
            $('#jMPutar, #jSPutar, #jMPisah, #jSPisah, #jMBeku, #jSBeku').clockpicker({
                autoclose: true,
                placement: 'bottom',
                align: 'left',
                donetext: 'Selesai',
                twelvehour: false
            });
        });

        document.getElementById("tglPengerjaan").addEventListener("click", function() {
            this.showPicker(); // Memaksa menampilkan kalender
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

            // // Ambil waktu sekarang
            // let now = new Date();

            // // Format YYYY-MM-DDTHH:MM (format standar untuk datetime-local)
            // let year = now.getFullYear();
            // let month = String(now.getMonth() + 1).padStart(2, '0');
            // let day = String(now.getDate()).padStart(2, '0');
            // let hour = String(now.getHours()).padStart(2, '0');
            // let minute = String(now.getMinutes()).padStart(2, '0');

            // let formatted = `${year}-${month}-${day}T${hour}:${minute}`;

            // // Set ke input
            // document.getElementById("tglPengerjaan").value = formatted;
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

    <script>
        $(document).ready(function() {
            function updateMode() {
                let mode = $('input[name="modePengerjaan"]:checked').val();

                // Reset semua kolom ke tampil terlebih dahulu
                $('th, td').show();

                // Indeks kolom untuk masing-masing bagian (1-based index untuk nth-child)
                const pemutaranCols = [11, 12, 13, 14]; // Kolom Pemutaran (Alat, Kecepatan, Suhu, Waktu)
                const pemisahanCols = [15, 16, 17, 18]; // Kolom Pemisahan (Metode, Alat, Mulai, Selesai)
                const pembekuanCols = [19, 20, 21, 22]; // Kolom Pembekuan (Alat, Mulai, Selesai, Suhu Inti)


                if (mode === "wb") {
                    // Sembunyikan input form
                    $("#alatPemutaran, #alatPemisahan, #alatPembekuan").closest(".form-group").hide();
                    $("#jMPutar, #jSPutar, #jMPisah, #jSPisah, #jMBeku, #jSBeku").closest("div").hide();

                    // Hide semua kolom pemutaran + pemisahan + pembekuan
                    [...pemutaranCols, ...pemisahanCols, ...pembekuanCols].forEach(col => {
                        $(`thead tr:nth-child(2) th:nth-child(${col}), tbody td:nth-child(${col})`).hide();
                    });

                    // Hide header utama (pakai class)
                    $('.col-pemutaran, .col-pengolahan, .col-pembekuan').hide();
                } else if (mode === "putarPisah") {
                    // Tampilkan input untuk Pemutaran dan Pemisahan
                    $("#alatPemutaran, #alatPemisahan").closest(".form-group").show();
                    $("#jMPutar, #jSPutar, #jMPisah, #jSPisah").closest("div").show();
                    // Sembunyikan input untuk Pembekuan
                    $("#alatPembekuan").closest(".form-group").hide();
                    $("#jMBeku, #jSBeku").closest("div").hide();

                    // Hide pembekuan
                    pembekuanCols.forEach(col => {
                        $(`thead tr:nth-child(2) th:nth-child(${col}), tbody td:nth-child(${col})`).hide();
                    });
                    $('.col-pembekuan').hide();

                    // Show pemutaran & pemisahan
                    $('.col-pemutaran, .col-pengolahan').show();

                } else { // lengkap
                    // Tampilkan semua input form
                    $("#alatPemutaran, #alatPemisahan, #alatPembekuan").closest(".form-group").show();
                    $("#jMPutar, #jSPutar, #jMPisah, #jSPisah, #jMBeku, #jSBeku").closest("div").show();

                    // Show semua header utama
                    $('.col-pemutaran, .col-pengolahan, .col-pembekuan').show();
                }
            }

            // Panggil pertama kali saat halaman dimuat
            updateMode();

            // Panggil saat radio button berubah
            $('input[name="modePengerjaan"]').on("change", function() {
                updateMode();
            });
        });
    </script>
    <script>
        flatpickr("#tglPengerjaan", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
    </script>


</body>

</html>