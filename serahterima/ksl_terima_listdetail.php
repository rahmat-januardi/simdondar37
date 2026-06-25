<?php
session_start();
$msg = "";
require_once('clogin.php');
require_once('config/dbi_connect.php');
$leveluser   = $_SESSION['level'];
$namauser    = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];
$level       = $_SESSION['leveluser'];
$udd         = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama`,`id` FROM `utd` WHERE `aktif`='1';"));
$id_uddaktif   = $udd['id'];
$nama_uddaktif = $udd['nama'];

$tgl     = date('Ymd');
$token   = "17091945" . $tgl;
$notrans = isset($_GET['id'])     ? trim($_GET['id'])     : '';
$mode    = isset($_GET['mode'])   ? trim($_GET['mode'])   : 'proses';
$source  = isset($_GET['source']) ? trim($_GET['source']) : 'online';

if ($notrans === '') {
    header("Location: pmi" . $level . ".php?module=sr_aftap_kns");
    exit;
}

$notrans_esc = mysqli_real_escape_string($dbi, $notrans);

// ── Ambil data detail sesuai sumber ──────────────────────────────────────────
$detail_rows    = array();
$info_header    = array();
$curl_error_msg = '';

if ($source === 'download') {
    // SUMBER: lokal DB (via Download)
    $qryHdr = mysqli_query(
        $dbi,
        "SELECT * FROM `ksl_import_antrian` WHERE `notrans`='$notrans_esc' LIMIT 1"
    );
    if ($qryHdr) {
        $info_header = mysqli_fetch_assoc($qryHdr);
        if (!$info_header) $info_header = array();
    }

    $qryDet = mysqli_query(
        $dbi,
        "SELECT * FROM `ksl_import_antrian_detail` WHERE `notrans`='$notrans_esc' ORDER BY `id`"
    );
    if ($qryDet) {
        while ($rd = mysqli_fetch_assoc($qryDet)) {
            $detail_rows[] = $rd;
        }
    }
} else {
    // SUMBER: API Online dbdonor
    $curlD = curl_init();
    curl_setopt_array($curlD, array(
        CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_detail_preterima.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => "POST",
        CURLOPT_POSTFIELDS     => array('udd' => $notrans, 'key' => $token),
    ));
    $responseD      = curl_exec($curlD);
    $curl_error_msg = curl_error($curlD);
    curl_close($curlD);

    $dataD = json_decode($responseD, true);
    if (isset($dataD['data']) && is_array($dataD['data'])) {
        foreach ($dataD['data'] as $row) {
            if (strlen(isset($row['dst_nokantong']) ? $row['dst_nokantong'] : '') > 0) {
                $detail_rows[] = $row;
            }
        }
    }
}
?>

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
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, .2), 0 6px 20px 0 rgba(0, 0, 0, .19);
        }

        .modal-half {
            width: 70%;
            padding: 0;
            position: fixed;
            left: 15%;
        }

        .modal-content {
            width: 100%;
            margin: 0 0;
        }

        .modal-footer {
            bottom: 0;
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
        }

        .table tbody td {
            font-size: 1em;
            white-space: nowrap;
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
                transform: rotate(0deg)
            }

            to {
                transform: rotate(360deg)
            }
        }

        a {
            text-decoration: none !important;
        }

        .swal2-popup {
            font-size: 1.6rem !important;
        }

        .label-source {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .label-source-online {
            background: #1a8b3a;
            color: #fff;
        }

        .label-source-download {
            background: #1a6a8b;
            color: #fff;
        }

        .info-card {
            background: #fff8f8;
            border: 1px solid #e0c0c0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .info-card strong {
            color: #8b1a1a;
        }

        .success {
            background-color: #d4edda !important;
        }

        .chk-verifikasi {
            width: 10px;
            height: 10px;
        }
    </style>
</head>

<body>
    <div id="loading"></div>
    <div class="container-fluid" style="margin:30px;">
        <div class="row">
            <div class="col-md-12">
                <div class="panel w3-border-theme shadow">

                    <div class="panel-heading w3-theme-d5 clearfix">
                        <div class="col-lg-8 col-md-7 col-sm-6 col-xs-6 text-left text-shadow"
                            style="font-size:150%;font-weight:bold;">
                            KONFIRMASI PENERIMAAN DARAH KONSOLIDASI
                        </div>
                        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-6 text-right">
                            <input type="hidden" id="InpNotransaksi" value="<?php echo htmlspecialchars($notrans); ?>">
                            <input type="hidden" id="hdnSource" value="<?php echo htmlspecialchars($source); ?>">
                            <?php if ($mode === 'proses') { ?>
                                <button type="button" id="vKirim"
                                    class="w3-btn w3-theme w3-hover-green">PROSES</button>
                            <?php } else { ?>
                                <a href="?module=proseshapus" class="w3-btn w3-theme w3-hover-green">HAPUS</a>
                            <?php } ?>
                            <a href="?module=sr_aftap_kns" class="w3-btn w3-theme w3-hover-yellow">KEMBALI</a>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-xs-12"><?php echo $msg; ?></div>

                        <!-- Info header -->
                        <div class="info-card">
                            <strong>No. Transaksi :</strong> <?php echo htmlspecialchars($notrans); ?>
                            &nbsp;&nbsp;
                            <span class="label-source label-source-<?php echo $source; ?>">
                                <?php echo ($source === 'download') ? 'Via Download (Lokal)' : 'Via Online'; ?>
                            </span>
                            <?php if ($source === 'download' && !empty($info_header)) { ?>
                                &nbsp;&nbsp;
                                <strong>Asal UDD :</strong>
                                <?php echo htmlspecialchars($info_header['udd_asal_nama']); ?>
                                &nbsp;&nbsp;
                                <strong>Tgl. Serah :</strong>
                                <?php echo htmlspecialchars($info_header['hst_tgl']); ?>
                            <?php } ?>
                        </div>

                        <?php if ($curl_error_msg && $source === 'online') { ?>
                            <div class="alert alert-warning">
                                <strong>Perhatian:</strong> Gagal menghubungi server online.
                                (<?php echo htmlspecialchars($curl_error_msg); ?>)
                            </div>
                        <?php } ?>

                        <div class="row" style="margin-bottom:15px;">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-barcode"></i></span>
                                    <input type="text" id="barcodeInput" class="form-control input-lg"
                                        placeholder="Scan barcode kantong di sini..." autofocus>
                                </div>
                                <small class="text-muted">Barcode scanner akan otomatis terdeteksi</small>
                            </div>
                            <div class="col-md-6 text-right">
                                <h4>
                                    Terverifikasi: <strong id="countVerified">0</strong> /
                                    <strong id="countTotal"><?php echo count($detail_rows); ?></strong>
                                </h4>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover display"
                                id="dtaudittrail">
                                <thead class="w3-theme-d4">
                                    <tr>
                                        <th>No</th>
                                        <th>No. Kantong</th>
                                        <th>Tgl. Aftap</th>
                                        <th>Kode Pendonor</th>
                                        <th>Merk</th>
                                        <th>Volume</th>
                                        <th>Gol.</th>
                                        <th>Rhesus</th>
                                        <th>Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($detail_rows) === 0) {
                                        echo '<tr><td colspan="8" class="text-center" style="font-size:16px;">Tidak ada data kantong untuk transaksi ini</td></tr>';
                                    } else {
                                        for ($i = 0; $i < count($detail_rows); $i++) {
                                            $d = $detail_rows[$i];
                                            echo "<tr>";
                                            echo "<td class='text-right'>" . ($i + 1) . ".</td>";
                                            echo "<td>" . htmlspecialchars($d['dst_nokantong']) . "</td>";
                                            echo "<td>" . htmlspecialchars($d['dst_tglaftap'])  . "</td>";
                                            echo "<td>" . htmlspecialchars($d['dst_kodedonor']) . "</td>";
                                            echo "<td>" . htmlspecialchars($d['dst_merk'])      . "</td>";
                                            echo "<td>" . htmlspecialchars($d['dst_volambil'])  . "</td>";
                                            echo "<td>" . htmlspecialchars($d['dst_golda'])     . "</td>";
                                            echo "<td>" . htmlspecialchars($d['dst_rh'])        . "</td>";
                                            echo "<td class='text-center verifikasi-col'>";
                                            echo "<input type='checkbox' class='chk-verifikasi' 
             data-nokantong='" . htmlspecialchars($d['dst_nokantong']) . "' 
             disabled style='transform:scale(1.4); cursor: not-allowed;'>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- /panel-body -->
                </div>
            </div>
        </div>
    </div>

</body>

<script src="bootsrap337/js/jquery.min.js"></script>
<script src="bootsrap337/js/bootstrap.min.js"></script>
<script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="bootsrap337/datepicker/custom.js"></script>
<script src="bootsrap337/chosen/chosen.jquery.js"></script>
<script src="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.js"></script>
<script src="bootsrap337/sweetalert2/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var load = document.getElementById('loading');
        window.addEventListener('load', function() {
            load.style.display = 'none';
        });

        // ── Simpan barcode terverifikasi di Set (sumber kebenaran, bukan DOM) ──────
        const verifiedBarcodes = new Set();
        const totalCount = <?php echo count($detail_rows); ?>;

        function updateCounter() {
            var count = verifiedBarcodes.size;
            $('#countVerified').text(count);
            if (count === totalCount && totalCount > 0) {
                $('#vKirim').prop('disabled', false)
                    .removeClass('w3-theme').addClass('w3-green');
            } else {
                $('#vKirim').prop('disabled', true)
                    .removeClass('w3-green').addClass('w3-theme');
            }
        }

        // Re-apply visual state setelah DataTables re-render halaman
        function reapplyVerifiedState() {
            verifiedBarcodes.forEach(function(barcode) {
                var $cb = $('.chk-verifikasi[data-nokantong="' + barcode + '"]');
                if ($cb.length > 0) {
                    $cb.prop('checked', true);
                    $cb.closest('tr').addClass('success');
                }
            });
        }

        // Inisialisasi DataTable + pasang drawCallback
        var dtTable = $('#dtaudittrail').DataTable({
            lengthMenu: [
                [5, 10, 15, 25, 50, -1],
                [5, 10, 15, 25, 50, 'All']
            ],
            drawCallback: function() {
                // Setiap kali DataTables me-render ulang halaman, terapkan kembali state
                reapplyVerifiedState();
            }
        });

        // Event delegation: tangkap change dari semua checkbox .chk-verifikasi
        // (termasuk yang di-render ulang oleh DataTables di halaman lain)
        $(document).on('change', '.chk-verifikasi', function() {
            updateCounter();
        });

        $('#vKirim').on('click', function() {
            var noTransaksi = $('#InpNotransaksi').val();
            var source = $('#hdnSource').val();

            if (!noTransaksi) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'No. Transaksi tidak ditemukan.',
                    icon: 'error'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                html: 'Proses penerimaan darah konsolidasi<br><strong>' + noTransaksi + '</strong>?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a6a1a',
                confirmButtonText: 'Ya, Proses',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Memproses Data...',
                    text: 'Harap tunggu.',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: 'serahterima/ksl_terima_proses.php',
                    type: 'POST',
                    data: {
                        noTransaksi: noTransaksi,
                        source: source
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response && response.status === 'success') {
                            Swal.fire({
                                title: 'Sukses!',
                                text: response.message || 'Data berhasil diterima.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                window.location.href = 'pmi<?php echo $level; ?>.php?module=sr_aftap_kns';
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat memproses data.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        var msg = 'Terjadi kesalahan saat menghubungi server.';
                        try {
                            msg = JSON.parse(xhr.responseText).message || msg;
                        } catch (e) {}
                        Swal.fire({
                            title: 'Gagal!',
                            text: msg,
                            icon: 'error'
                        });
                    }
                });
            });
        });

        const $barcodeInput = $('#barcodeInput');

        // Auto focus ke input scan
        $barcodeInput.focus();

        // Event Scan Barcode
        $barcodeInput.on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                const barcode = $(this).val().trim();
                if (barcode === '') return;

                processBarcode(barcode);
                $(this).val('').focus();
            }
        });

        function processBarcode(barcode) {
            // Cek apakah sudah terverifikasi sebelumnya (pakai Set, bukan DOM)
            if (verifiedBarcodes.has(barcode)) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Sudah terverifikasi: ' + barcode,
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }

            // Cari checkbox dengan nomor kantong tersebut
            const $checkbox = $('.chk-verifikasi[data-nokantong="' + barcode + '"]');

            if ($checkbox.length > 0) {
                // Tambahkan ke Set terlebih dulu
                verifiedBarcodes.add(barcode);

                // Terapkan visual (checkbox + warna baris)
                $checkbox.prop('checked', true);
                $checkbox.closest('tr').addClass('success');

                // Update counter lewat Set
                updateCounter();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Terverifikasi: ' + barcode,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                // Barcode valid tapi tidak ada di halaman ini (mungkin ada di halaman lain DataTables)?
                // Cek dari semua data: jika barcode ada di data PHP tapi tidak ditemukan di DOM
                // → kemungkinan DataTables belum me-render halaman tersebut
                // Solusi: tambahkan ke Set dulu, visual akan diterapkan saat drawCallback
                var allBarcodes = [];
                $('.chk-verifikasi').each(function() {
                    allBarcodes.push($(this).data('nokantong'));
                });

                // Jika ditemukan di semua baris (termasuk yang tersembunyi DataTables)
                var $allCheckbox = $('input.chk-verifikasi[data-nokantong="' + barcode + '"]');
                if ($allCheckbox.length > 0) {
                    verifiedBarcodes.add(barcode);
                    $allCheckbox.prop('checked', true);
                    $allCheckbox.closest('tr').addClass('success');
                    updateCounter();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Terverifikasi: ' + barcode,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Kantong tidak ditemukan: ' + barcode,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            }
        }

        // Update counter (delegasi — tetap ada sebagai fallback)
        // Utamanya update dilakukan langsung dari processBarcode via updateCounter()

        // Inisialisasi counter
        updateCounter();
    });
</script>