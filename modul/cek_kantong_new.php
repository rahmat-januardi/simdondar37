<?php
require_once('clogin.php');
require_once('config/dbi_connect.php');
$namauser = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];
$levelUser = $_SESSION['leveluser'];

$level = $_GET['l'];
if (empty($level)) {
    $level = '0';
}
?>

<?php
if (isset($_GET['ajax_kantong']) && $_GET['ajax_kantong'] == '1') {
    $nkt_ajax = mysqli_real_escape_string($dbi, $_GET['noktg']);

    $sql = "select * from stokkantong where upper(nokantong)=upper('$nkt_ajax')";
    $stokkantong = mysqli_fetch_assoc(mysqli_query($dbi, $sql));

    if (!strlen($stokkantong['noKantong'])) {
        echo '<div class="alert alert-warning mb-0">Data kantong tidak ditemukan.</div>';
        exit;
    }

    if (($stokkantong['AsalUTD'] == '-') || ($stokkantong['AsalUTD'] == '')) {
        $asalutd = mysqli_fetch_assoc(mysqli_query($dbi, "select nama from utd where aktif='1'"));
        $asalutd = $asalutd['nama'];
    } else {
        $asalutd = mysqli_fetch_assoc(mysqli_query($dbi, "select nama from utd where id='$stokkantong[AsalUTD]'"));
        $asalutd = $asalutd['nama'];
    }

    $produk = mysqli_fetch_assoc(mysqli_query($dbi, "select lengkap from produk where Nama='$stokkantong[produk]'"));
    $namaproduk = $produk['lengkap'];
    $produk = $stokkantong['produk'];

    $kantongke = strtoupper(substr($nkt_ajax, -1));
    $no_kantonga = substr_replace($nkt_ajax, 'A', -1, 1);

    switch ($kantongke) {
        case 'A':
            $posisikantong = 'Kantong Utama';
            break;
        case 'B':
            $posisikantong = 'Kantong Satelite 1';
            break;
        case 'C':
            $posisikantong = 'Kantong Satelite 2';
            break;
        case 'D':
            $posisikantong = 'Kantong Satelite 3';
            break;
        case 'E':
            $posisikantong = 'Kantong Satelite 4';
            break;
        case 'F':
            $posisikantong = 'Kantong Satelite 5';
            break;
        case 'G':
            $posisikantong = 'Kantong Satelite 6';
            break;
        case 'H':
            $posisikantong = 'Kantong Satelite 7';
            break;
        default:
            $posisikantong = '';
    }

    $jumlah_kantong = 1;
    switch ($stokkantong['jenis']) {
        case '1':
            $jeniskantong = 'Single';
            $jumlah_kantong = 1;
            break;
        case '2':
            $jeniskantong = 'Double';
            $jumlah_kantong = 2;
            break;
        case '3':
            $jeniskantong = 'Triple';
            $jumlah_kantong = 3;
            break;
        case '4':
            $jeniskantong = 'Quadruple';
            $jumlah_kantong = 4;
            break;
        case '5':
            $jeniskantong = 'Quadruple T&B';
            $jumlah_kantong = 5;
            break;
        case '6':
            $jeniskantong = 'Pediatrik';
            $jumlah_kantong = 1;
            break;
        default:
            $jeniskantong = '';
            $jumlah_kantong = 1;
    }

    $statuskantong = '';
    switch ($stokkantong['Status']) {
        case '0':
            $statuskantong = 'Kosong';
            if ($stokkantong['StatTempat'] == NULL || $stokkantong['StatTempat'] == '0') $statuskantong = 'Kosong - di Logistik';
            if ($stokkantong['StatTempat'] == '1') $statuskantong = 'Kosong - di Aftap';
            break;
        case '1':
            $statuskantong = ($stokkantong['sah'] == "1") ? 'Karantina' : 'Belum disahkan';
            break;
        case '2':
            $statuskantong = 'Sehat';
            break;
        case '3':
            $statuskantong = 'Keluar';
            break;
        case '4':
            $statuskantong = 'Rusak';
            break;
        case '5':
            $statuskantong = 'Rusak-Gagal';
            break;
        case '6':
            $statuskantong = 'Dimusnahkan';
            break;
        case '7':
            $statuskantong = 'Reaktif';
            break;
        default:
            $statuskantong = '-';
    }

    switch ($stokkantong['hasil_release']) {
        case '0':
            $hasilrelease = 'Tidak ada';
            break;
        case '1':
            $hasilrelease = 'Lulus';
            break;
        case '2':
            $hasilrelease = 'Tidak Lulus';
            break;
        case '3':
            $hasilrelease = 'Lulus dengan catatan';
            break;
        default:
            $hasilrelease = '';
    }

    if ($kantongke == "A") {
        $lamaaftap = $stokkantong['lama_pengambilan'];
    } else {
        $st_k = mysqli_query($dbi, "select * from stokkantong where nokantong='$no_kantonga'");
        $dt_k = mysqli_fetch_assoc($st_k);
        $lamaaftap = $dt_k['lama_pengambilan'];
    }

    $qrel = "SELECT * FROM `release` WHERE `rnokantong`='$nkt_ajax'";
    $release = mysqli_fetch_assoc(mysqli_query($dbi, $qrel));
    $qkgd = "select * from `dkonfirmasi` where `NoKantong` = '$no_kantonga' order by NoKonfirmasi desc";
    $konfirmasi = mysqli_fetch_assoc(mysqli_query($dbi, $qkgd));

    if ($hasilrelease == 'Tidak ada') {
        $volume_darah = $stokkantong['volume'];
    } else {
        $volume_darah = round($release['rvolume'], 0);
    }

    echo '<div class="table-box">';
    echo '<table class="info-table">';
    echo '<tr><td class="label">No. Kantong</td><td class="value">' . $nkt_ajax . '</td></tr>';
    echo '<tr><td class="label">Asal UTD</td><td class="value">' . $asalutd . '</td></tr>';
    echo '<tr><td class="label">Produk</td><td class="value">' . $produk . ' - ' . $namaproduk . '</td></tr>';
    echo '<tr><td class="label">Golongan Darah</td><td class="value">' . $stokkantong['gol_darah'] . ' Rh (' . $stokkantong['RhesusDrh'] . ')</td></tr>';
    echo '<tr><td class="label">Volume</td><td class="value">' . $volume_darah . ' ml</td></tr>';
    echo '<tr><td class="label">Status darah/komponen</td><td class="value">' . $statuskantong . '</td></tr>';
    echo '<tr><td class="label">Hasil Release</td><td class="value">' . $hasilrelease . '</td></tr>';
    echo '<tr><td class="label">Tgl Pengambilan</td><td class="value">' . $stokkantong['tgl_Aftap'] . '</td></tr>';
    echo '<tr><td class="label">Tgl Uji Saring IMLTD</td><td class="value">' . $stokkantong['tglperiksa'] . '</td></tr>';
    echo '<tr><td class="label">Tgl KGD</td><td class="value">' . $konfirmasi['tgl'] . '</td></tr>';
    echo '<tr><td class="label">Tgl Pengolahan</td><td class="value">' . $stokkantong['tglpengolahan'] . '</td></tr>';
    echo '<tr><td class="label">Tgl Release</td><td class="value">' . $stokkantong['tgl_release'] . '</td></tr>';
    echo '<tr><td class="label">Tgl Kadaluarsa</td><td class="value">' . $stokkantong['kadaluwarsa'] . '</td></tr>';
    echo '<tr><td class="label">Tgl Keluar</td><td class="value">' . $stokkantong['tgl_keluar'] . '</td></tr>';
    echo '</table>';
    echo '</div>';
    exit;
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Kantong</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link type="text/css" href="../css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
    <link type="text/css" href="../css/blitzer/suwena.css" rel="stylesheet" />
    <link type="text/css" href="../css/style.css" rel="stylesheet" />
    <link type="text/css" href="css/table1.css" rel="stylesheet" />

    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, Helvetica, sans-serif;
            padding: 10px;
        }

        .top-panel {
            background: linear-gradient(180deg, #b51d0b 0%, #a81808 100%);
            border-radius: 4px;
            padding: 10px 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .18);
            margin-bottom: 16px;
        }

        .search-wrap {
            max-width: 430px;
        }

        .search-wrap .input-group-text {
            background: #f7c4be;
            border: 0;
            color: #000;
            font-weight: 600;
            min-width: 80px;
            justify-content: center;
        }

        .search-wrap .form-control {
            border: 0;
            box-shadow: none;
            height: 42px;
            font-weight: 600;
        }

        .search-btn {
            background: #f7c4be;
            border: 0;
            color: #000;
            min-width: 45px;
            font-weight: 700;
        }

        .search-btn:hover {
            background: #efb0a8;
            color: #000;
        }

        .panel-card {
            border: 1px solid #d8d8d8;
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
        }

        .panel-card-header {
            background: linear-gradient(180deg, #c92010 0%, #c51e0e 100%);
            color: #fff;
            padding: 12px 14px;
            font-size: 22px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .badge-round {
            background: #fff;
            color: #111;
            border-radius: 20px;
            padding: 7px 14px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
        }

        .panel-body {
            padding: 16px;
            background: #fff;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #0a4aa3;
            margin: 10px 0 12px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .info-table td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            vertical-align: middle;
            font-size: 15px;
        }

        .info-table td.label {
            width: 42%;
            background: #fff6f6;
            color: #222;
            font-weight: 500;
        }

        .info-table td.value {
            width: 58%;
            color: #111;
            font-weight: 600;
        }

        .info-table tr:hover td {
            background: #fff1f1;
        }

        .table-box {
            border-radius: 3px;
            overflow: hidden;
        }

        .text-muted-empty {
            color: #888;
            font-weight: 500;
        }

        @media (max-width: 767px) {
            .panel-card-header {
                font-size: 18px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .search-wrap {
                max-width: 100%;
            }

            .info-table td {
                font-size: 14px;
                padding: 8px 10px;
            }

            .badge-kantong {
                border: none;
                cursor: pointer;
                outline: none;
            }
        }
    </style>
</head>

<body OnLoad="document.cekkantong.noktg.focus();" class="bg-light">

    <div class="top-panel">
        <form name="cekkantong" method="post" class="search-wrap">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Barcode</span>
                </div>
                <input type="text" name="noktg" class="form-control" placeholder="Nomor kantong"
                    style="text-transform:uppercase" minlength="5" required>
                <div class="input-group-append">
                    <button type="submit" name="cari" class="btn search-btn">⎙</button>
                </div>
            </div>
        </form>
    </div>


    <?php
    if (isset($_POST['cari'])) {
        $nkt = isset($_POST['noktg']) ? $_POST['noktg'] : '';
        $sql = "select * from stokkantong where upper(nokantong)=upper('$nkt')";
        $stokkantong = mysqli_fetch_assoc(mysqli_query($dbi, $sql));
        if (strlen($stokkantong['noKantong']) > 0) {
            if (($stokkantong['AsalUTD'] == '-') or ($stokkantong['AsalUTD'] == '')) {
                $asalutd = mysqli_fetch_assoc(mysqli_query($dbi, "select nama from utd where aktif='1'"));
                $asalutd = $asalutd['nama'];
            } else {
                $asalutd = mysqli_fetch_assoc(mysqli_query($dbi, "select nama from utd where id='$stokkantong[AsalUTD]'"));
                $asalutd = $asalutd['nama'];
            }
            $produk = mysqli_fetch_assoc(mysqli_query($dbi, "select lengkap from produk where Nama='$stokkantong[produk]'"));
            $namaproduk = $produk['lengkap'];
            $produk = $stokkantong['produk'];
            $kantongke = strtoupper(substr($nkt, -1));
            $no_kantonga = substr_replace($nkt, 'A', -1, 1);
            switch ($kantongke) {
                case 'A':
                    $posisikantong = 'Kantong Utama';
                    break;
                case 'B':
                    $posisikantong = 'Kantong Satelite 1';
                    break;
                case 'C':
                    $posisikantong = 'Kantong Satelite 2';
                    break;
                case 'D':
                    $posisikantong = 'Kantong Satelite 3';
                    break;
                case 'E':
                    $posisikantong = 'Kantong Satelite 4';
                    break;
                case 'F':
                    $posisikantong = 'Kantong Satelite 5';
                    break;
                case 'G':
                    $posisikantong = 'Kantong Satelite 6';
                    break;
                case 'H':
                    $posisikantong = 'Kantong Satelite 7';
                    break;
                default:
                    $posisikantong = "";
            }
            $jumlah_kantong = 1;

            switch ($stokkantong['jenis']) {
                case '1':
                    $jeniskantong = 'Single';
                    $jumlah_kantong = 1;
                    break;
                case '2':
                    $jeniskantong = 'Double';
                    $jumlah_kantong = 2;
                    break;
                case '3':
                    $jeniskantong = 'Triple';
                    $jumlah_kantong = 3;
                    break;
                case '4':
                    $jeniskantong = 'Quadruple';
                    $jumlah_kantong = 4;
                    break;
                case '5':
                    $jeniskantong = 'Quadruple T&B';
                    $jumlah_kantong = 5;
                    break;
                case '6':
                    $jeniskantong = 'Pediatrik';
                    $jumlah_kantong = 1;
                    break;
                default:
                    $jeniskantong = '';
                    $jumlah_kantong = 1;
            }

            $urutan_kantong = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
            $kantong_terkait = array();

            for ($i = 0; $i < $jumlah_kantong; $i++) {
                if (isset($urutan_kantong[$i]) && $urutan_kantong[$i] !== $kantongke) {
                    $kantong_terkait[] = $urutan_kantong[$i];
                }
            }

            $kantong_terkait_text = !empty($kantong_terkait) ? implode(', ', $kantong_terkait) : '-';
            $statuskantong = '';
            $status_ktg = $stokkantong['Status'];
            switch ($status_ktg) {
                case '0':
                    $statuskantong = 'Kosong';
                    if ($stokkantong['StatTempat'] == NULL)
                        $statuskantong = 'Kosong - di Logistik';
                    if ($stokkantong['StatTempat'] == '0')
                        $statuskantong = 'Kosong - di Logistik';
                    if ($stokkantong['StatTempat'] == '1')
                        $statuskantong = 'Kosong - di Aftap';
                    break;
                case '1':
                    if ($stokkantong['sah'] == "1") {
                        $statuskantong = 'Karantina';
                    } else {
                        $statuskantong = 'Belum disahkan';
                    }
                    break;
                case '2':
                    $statuskantong = 'Sehat';
                    if (substr($stokkantong['stat2'], 0, 1) == 'b')
                        $tempat = " (BDRS)";
                    break;
                case '3':
                    $statuskantong = "Keluar";
                    $bawa = mysqli_fetch_assoc(mysqli_query($dbi, "select Status from dtransaksipermintaan where nokantong='$nkt'"));
                    if ($bawa['Status'] == '1')
                        $statuskantong = "Keluar (dititip)";
                    break;
                case '4':
                    $statuskantong = 'Rusak';
                    break;
                case '5':
                    $statuskantong = 'Rusak-Gagal';
                    break;
                case '6':
                    $statuskantong = 'Dimusnahkan';
                    break;
                case '7':
                    $statuskantong = 'Reaktif';
                    break;
                default:
                    $statuskantong = '-';
            }
            switch ($stokkantong['hasil_release']) {
                case '0':
                    $hasilrelease = 'Tidak ada';
                    break;
                case '1':
                    $hasilrelease = 'Lulus';
                    break;
                case '2':
                    $hasilrelease = 'Tidak Lulus';
                    break;
                case '3':
                    $hasilrelease = 'Lulus dengan catatan';
                    break;
                default:
                    $hasilrelease = '';
            }
            $merk = $stokkantong['merk'];
            $tglinputlogistik = $stokkantong['tglTerima'];
            $volumeasal = $stokkantong['volumeasal'];
            $tglmutasi = $stokkantong['tglmutasi'];
            $lotkantong = $stokkantong['nolot_ktg'];
            $edkantong = $stokkantong['kadaluwarsa_ktg'];
            $jeniskomponen = $stokkantong['produk'];
            $tglpengolahankomponen = $stokkantong['tglpengolahan'];
            $tgledkomponen = $stokkantong['kadaluwarsa'];
            if ($kantongke == "A") {
                $lamaaftap = $stokkantong['lama_pengambilan'];
            } else {
                $st_k = mysqli_query($dbi, "select * from stokkantong where nokantong='$no_kantonga'");
                $dt_k = mysqli_fetch_assoc($st_k);
                $lamaaftap = $dt_k['lama_pengambilan'];
            }

            //Menampilkan data Kantong
            $qrel = "SELECT * FROM `release` WHERE `rnokantong`='$nkt'";
            $release = mysqli_fetch_assoc(mysqli_query($dbi, $qrel));
            $qkgd = "select * from `dkonfirmasi` where `NoKantong` = '$no_kantonga' order by NoKonfirmasi desc";
            $konfirmasi = mysqli_fetch_assoc(mysqli_query($dbi, $qkgd));
            if ($hasilrelease == 'Tidak ada') {
                $volume_darah = $stokkantong['volume'];
            } else {
                $volume_darah = round($release['rvolume'], 0);
            }
    ?>

            <div class="panel-card">
                <div class="panel-card-header">
                    <div>Data Kantong</div>
                    <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                        <span class="mr-2" style="font-size: 15px;">Kantong terkait</span>
                        <?php if (!empty($kantong_terkait)) : ?>
                            <?php foreach ($kantong_terkait as $ktg) :
                                $nokt_related = substr_replace($nkt, $ktg, -1, 1);
                            ?>
                                <button type="button" class="badge-round badge-kantong btn-kantong"
                                    data-nokantong="<?php echo $nokt_related; ?>">
                                    <?php echo $ktg; ?>
                                </button>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <span class="badge-round">-</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="panel-body row">
                    <div class="col-lg-6 mb-3">
                        <table class="info-table">
                            <tr>
                                <td class="label">Nomor kantong</td>
                                <td class="value"><?php echo $nkt; ?></td>
                            </tr>
                            <tr>
                                <td class="label">UDD PMI</td>
                                <td class="value"><?php echo $asalutd; ?></td>
                            </tr>
                            <tr>
                                <td class="label">Produk</td>
                                <td class="value"><?php echo $produk . ' - ' . $namaproduk; ?></td>
                            </tr>
                            <tr>
                                <td class="label">Golongan Darah</td>
                                <td class="value">
                                    <?php echo $stokkantong['gol_darah'] . ' Rh (' . $stokkantong['RhesusDrh'] . ')'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Volume</td>
                                <td class="value"><?php echo $volume_darah; ?> ml</td>
                            </tr>
                            <tr>
                                <td class="label">Status Kantong Darah</td>
                                <td class="value"><?php echo $statuskantong; ?></td>
                            </tr>
                            <tr>
                                <td class="label">Hasil Pelulusan</td>
                                <td class="value"><?php echo $hasilrelease; ?></td>
                            </tr>
                        </table>
                    </div>


                    <div class="col-lg-6 mb-3">
                        <table class="info-table">
                            <tr>
                                <td class="label">Tgl Pengambilan</td>
                                <td class="value"><?php echo $stokkantong['tgl_Aftap'] ? $stokkantong['tgl_Aftap'] : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Tgl Uji Saring IMLTD</td>
                                <td class="value"><?php echo $stokkantong['tglperiksa'] ? $stokkantong['tglperiksa'] : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Tgl KGD</td>
                                <td class="value"><?php echo $konfirmasi['tgl'] ? $konfirmasi['tgl'] : '-'; ?></td>
                            </tr>
                            <tr>
                                <td class="label">Tgl ABS</td>
                                <td class="value"><?php echo $stokkantong['tgl_abs'] ? $stokkantong['tgl_abs'] : '-'; ?></td>
                            </tr>
                            <tr>
                                <td class="label">Tgl NAT</td>
                                <td class="value"><?php echo $stokkantong['tgl_nat'] ? $stokkantong['tgl_nat'] : '-'; ?></td>
                            </tr>
                            <tr>
                                <td class="label">Tgl Pengolahan</td>
                                <td class="value">
                                    <?php echo $stokkantong['tglpengolahan'] ? $stokkantong['tglpengolahan'] : '-'; ?></td>
                            </tr>
                            <tr>
                                <td class="label">Tgl Release</td>
                                <td class="value"><?php echo $stokkantong['tgl_release'] ? $stokkantong['tgl_release'] : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Tgl Kadaluarsa</td>
                                <td class="value"><?php echo $tgledkomponen ? $tgledkomponen : '-'; ?></td>
                            </tr>
                            <tr>
                                <td class="label">Tgl Keluar</td>
                                <td class="value"><?php echo $stokkantong['tgl_keluar'] ? $stokkantong['tgl_keluar'] : '-'; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            if ($levelUser != 'logistik' && $levelUser != 'p2d2s') {
                // jika darah keluar
                if ($stokkantong['Status'] == '3' && $stokkantong['stat2'] != '') {
            ?>
                    <div class="panel-card mt-4">
                        <div class="panel-card-header">
                            <div>Data Distribusi</div>
                            <div></div>
                        </div>

                        <div class="panel-body">
                            <?php
                            if (substr($stokkantong['stat2'], 0, 1) == 'b') {
                                // Distribusi ke BRDS
                                $q = "SELECT `id`,`nokantong`,`bdrs`,`tgl`,`petugas`,`nama`, `nama_lengkap`
                      FROM `kirimbdrs` k
                      INNER JOIN `bdrs` b ON b.`kode` = k.`bdrs`
                      INNER JOIN `user` u ON u.`id_user` = k.`petugas`
                      WHERE `nokantong`='$nkt'
                      ORDER BY `id` DESC";
                                $kirimbdrs = mysqli_fetch_assoc(mysqli_query($dbi, $q));
                            ?>
                                <div class="table-box">
                                    <table class="info-table">
                                        <tr>
                                            <td class="label">Tanggal dikeluarkan</td>
                                            <td class="value"><?php echo $kirimbdrs['tgl']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">BRDS Tujuan</td>
                                            <td class="value"><?php echo $kirimbdrs['nama']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Petugas</td>
                                            <td class="value"><?php echo $kirimbdrs['petugas'] . ' - ' . $kirimbdrs['nama_lengkap']; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            <?php
                            } elseif (substr($stokkantong['stat2'], 0, 1) > '0') {
                                // Distribusi ke UDD
                                $q = "SELECT k.`id`,k.`nokantong`,k.`udd`,k.`tgl`,k.`petugas`,b.`nama`,u.`nama_lengkap`
                            FROM `kirimudd` k
                            INNER JOIN `utd` b ON b.`id` = k.`udd`
                            INNER JOIN `user` u ON u.`id_user` = k.`petugas`
                            WHERE `nokantong`='$nkt'
                            ORDER BY k.`id` DESC";
                                $kirimbdrs = mysqli_fetch_assoc(mysqli_query($dbi, $q));
                            ?>
                                <div class="table-box">
                                    <table class="info-table">
                                        <tr>
                                            <td class="label">Tanggal dikeluarkan</td>
                                            <td class="value"><?php echo $kirimbdrs['tgl']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">UDD Tujuan</td>
                                            <td class="value"><?php echo $kirimbdrs['nama']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Petugas</td>
                                            <td class="value"><?php echo $kirimbdrs['petugas'] . ' - ' . $kirimbdrs['nama_lengkap']; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            <?php
                            } else {
                                $s_dist = mysqli_fetch_assoc(mysqli_query($dbi, "select * from dtransaksipermintaan where NoKantong='$nkt'"));
                                $data1 = mysqli_fetch_assoc(mysqli_query($dbi, "select * from htranspermintaan where noform='$s_dist[NoForm]'"));

                                if ($data1['jenis_permintaan'] == '0') {
                                    $jenis_permintaan = 'Biasa';
                                } else {
                                    $jenis_permintaan = $data1['jenis_permintaan'];
                                }

                                $s_pasien = "SELECT `no_rm`, `nama`, `alamat`, `gol_darah`, `rhesus`, `kelamin`, `keluarga`, `tgl_lahir`, `tlppasien`, `umur`, `insert_on`
                            FROM `pasien`
                            WHERE `no_rm` ='$s_dist[no_rm]'";
                                $pasien = mysqli_fetch_assoc(mysqli_query($dbi, $s_pasien));

                                if ($pasien['kelamin'] == 'L') {
                                    $kelamin = 'Laki-laki';
                                } else {
                                    $kelamin = 'Perempuan';
                                }

                                $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$data1[petugas]'"));
                                $ptgs_terima_form = $usr['nama_lengkap'];

                                $nmrs = mysqli_fetch_assoc(mysqli_query($dbi, "select NamaRs from rmhsakit where Kode='$data1[rs]'"));
                                $layanan = mysqli_fetch_assoc(mysqli_query($dbi, "select nama from jenis_layanan where kode='$data1[jenis]'"));

                                if ($s_dist['Status'] == '0') $status_bawa = 'Dibawa';
                                if ($s_dist['Status'] == '1') $status_bawa = 'Dititip';
                                if ($s_dist['Status'] == 'B') $status_bawa = 'Batal';

                                $q_dtransaksipermintaan = mysqli_query($dbi, "select * from dtransaksipermintaan where `NoKantong`='$nkt'");
                                $cross = mysqli_fetch_assoc($q_dtransaksipermintaan);

                                $hasil_cross = 'Compatible dapat dikeluarkan';
                                if ($cross['StatusCross'] == '0') $hasil_cross = 'Incompatible dapat dikeluarkan';
                                if ($cross['StatusCross'] == '2') $hasil_cross = 'Incompatible Tidak dapat dikeluarkan';

                                $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$cross[petugas]'"));
                                $ptgs_cross = $usr['nama_lengkap'];

                                $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$cross[cheker]'"));
                                $ptgs_cek = $usr['nama_lengkap'];
                            ?>

                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <div class="table-box">
                                            <table class="info-table">
                                                <tr>
                                                    <td class="label">Rumah Sakit</td>
                                                    <td class="value"><?php echo $nmrs['NamaRs']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Bagian di RS</td>
                                                    <td class="value"><?php echo $s_dist['bagian']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">No. Reg</td>
                                                    <td class="value"><?php echo $data1['regrs']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Kode Pasien</td>
                                                    <td class="value"><?php echo $pasien['no_rm']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Nama Pasien</td>
                                                    <td class="value"><?php echo $pasien['nama']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Gol Darah Pasien</td>
                                                    <td class="value"><?php echo $pasien['gol_darah'] . '(' . $pasien['rhesus'] . ')'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Jenis Kelamin</td>
                                                    <td class="value"><?php echo $kelamin; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Umur</td>
                                                    <td class="value"><?php echo $data1['umur']; ?> Tahun</td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Jenis Layanan</td>
                                                    <td class="value"><?php echo $layanan['nama']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Diagnosa</td>
                                                    <td class="value"><?php echo $data1['diagnosa']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Hemoglobin</td>
                                                    <td class="value"><?php echo $data1['hb']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Alasan transfusi</td>
                                                    <td class="value"><?php echo $data1['alasan']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Jenis Permintaan</td>
                                                    <td class="value"><?php echo $jenis_permintaan; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Petugas Penerima Formulir</td>
                                                    <td class="value"><?php echo $ptgs_terima_form; ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <div class="table-box">
                                            <table class="info-table">
                                                <tr>
                                                    <td class="label">Nomor Formulir</td>
                                                    <td class="value"><?php echo $s_dist['NoForm']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Tanggal Permintaan</td>
                                                    <td class="value"><?php echo $data1['tgl_register']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Tanggal Diperlukan</td>
                                                    <td class="value"><?php echo $data1['tglminta']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Tanggal Uji Silang Serasi</td>
                                                    <td class="value"><?php echo $cross['tgl']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Metode</td>
                                                    <td class="value"><?php echo $cross['MetodeCross']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Aglutinasi</td>
                                                    <td class="value"><?php echo $cross['aglutinasi']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Hasil</td>
                                                    <td class="value"><?php echo $cross['stat2']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Keterangan</td>
                                                    <td class="value"><?php echo $cross['ket']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Status pengeluaran</td>
                                                    <td class="value"><?php echo $hasil_cross; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Status darah keluar</td>
                                                    <td class="value"><?php echo $status_bawa; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Petugas Uji Silang Serasi</td>
                                                    <td class="value"><?php echo $ptgs_cross; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Petugas Check</td>
                                                    <td class="value"><?php echo $ptgs_cek; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Petugas Check</td>
                                                    <td class="value"><?php echo $cross['mengesahkan']; ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
            <?php
                }
            }
            ?>

            <?php
            if ($levelUser != 'logistik' && $levelUser != 'p2d2s') {

                $musnah = "SELECT * FROM `ar_stokkantong` where `noKantong`='$nkt'";
                $dtabuang = mysqli_fetch_assoc(mysqli_query($dbi, $musnah));

                switch ($dtabuang['alasan_buang']) {
                    case '0':
                        $alsn = "Gagal Aftap";
                        break;
                    case '1':
                        $alsn = "Lisis";
                        break;
                    case '2':
                        $alsn = "Kadaluwarsa";
                        break;
                    case '3':
                        $alsn = "Plebotomi";
                        break;
                    case '4':
                        $alsn = "Reaktif Buang";
                        break;
                    case '5':
                        $alsn = "Lifemik";
                        break;
                    case '6':
                        $alsn = "Greyzone";
                        break;
                    case '7':
                        $alsn = "DCT Positif";
                        break;
                    case '8':
                        $alsn = "Kantong Bocor";
                        break;
                    case '9':
                        $alsn = "Satelit Rusak";
                        break;
                    case '10':
                        $alsn = "Bekas Pembuatan WE";
                        break;
                    case '11':
                        $alsn = "Reaktif Dirujuk Ke UTDP";
                        break;
                    case '12':
                        $alsn = "Hematokrit Tinggi";
                        break;
                    case '13':
                        $alsn = "Plasma Sisa PRC";
                        break;
                    case '14':
                        $alsn = "Leukosit Tinggi";
                        break;
                    case '15':
                        $alsn = "Produk Rusak";
                        break;
                    case '16':
                        $alsn = "Produk Sample QC";
                        break;
                    case '17':
                        $alsn = "Plasma Kuning";
                        break;
                    case '18':
                        $alsn = "Plasma Merah";
                        break;
                    case '19':
                        $alsn = "Plasma Hijau";
                        break;
                    case '20':
                        $alsn = "Selang Pendek";
                        break;
                    case '21':
                        $alsn = "Selang Merah";
                        break;
                    case '22':
                        $alsn = "Volume Lebih";
                        break;
                    case '23':
                        $alsn = "Volume Kurang";
                        break;
                    case '24':
                        $alsn = "ABS Positif";
                        break;
                    case '25':
                        $alsn = "Menggumpal";
                        break;
                    case '26':
                        $alsn = "Clot";
                        break;
                    case '27':
                        $alsn = "Jejak IMLTD Reaktif";
                        break;
                    default:
                        $alsn = "Kantong Belum Terdaftar";
                        break;
                }
                if ($dtabuang['noKantong'] == $nkt) {
            ?>
                    <div class="panel-card mt-4">
                        <div class="panel-card-header">
                            <div>Data Pemusnahan</div>
                            <div></div>
                        </div>

                        <div class="panel-body">
                            <div class="table-box">
                                <table class="info-table">
                                    <tr>
                                        <td class="label">Tanggal Dimusnahkan</td>
                                        <td class="value"><?php echo $dtabuang['tgl_buang']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Alasan Dimusnahkan</td>
                                        <td class="value"><?php echo $alsn; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Petugas Pemusnahan</td>
                                        <td class="value"><?php echo $dtabuang['user']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>

            <?php

            $ptg_barcode0 = "SELECT `l`.`time_aksi`,`l`.`user`, `u`.`nama_lengkap` FROM `user_log` l inner join `user` u on `u`.`id_user`=`l`.`user` WHERE `aksi_user` like '%barcode%$no_kantonga%'";

            $ptg_barcode = mysqli_fetch_assoc(mysqli_query($dbi, $ptg_barcode0));

            $ptg_mutasi0 = "SELECT `l`.`time_aksi`,`l`.`user`, `u`.`nama_lengkap` FROM `user_log` l inner join `user` u on `u`.`id_user`=`l`.`user` WHERE `aksi_user` like '%Pengesahan Kantong Logistik%$no_kantonga%'";

            $ptg_mutasi = mysqli_fetch_assoc(mysqli_query($dbi, $ptg_mutasi0));
            ?>
            <br><br>
            <div class="panel-card">
                <div class="panel-card-header">
                    <div>Data Logistik</div>
                    <div></div>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="table-box">
                                <table class="info-table">
                                    <tr>
                                        <td class="label">Jenis Kantong</td>
                                        <td class="value"><?php echo $jeniskantong; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Merk Kantong</td>
                                        <td class="value"><?php echo $stokkantong['merk']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Volume Kantong</td>
                                        <td class="value"><?php echo $volumeasal; ?> ml</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Posisi Kantong</td>
                                        <td class="value"><?php echo $posisikantong; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Nomor Lot</td>
                                        <td class="value"><?php echo $stokkantong['nolot_ktg']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <div class="table-box">
                                <table class="info-table">
                                    <tr>
                                        <td class="label">Tgl Input/Barcode</td>
                                        <td class="value"><?php echo $stokkantong['tglTerima']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Tgl Pengesahan</td>
                                        <td class="value"><?php echo $stokkantong['tglmutasi']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Tgl ED Kantong</td>
                                        <td class="value"><?php echo $stokkantong['kadaluwarsa_ktg']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Petugas Barcode</td>
                                        <td class="value">
                                            <?php echo '(' . $ptg_barcode['user'] . ') - ' . $ptg_barcode['nama_lengkap']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label">Petugas Pengesahan</td>
                                        <td class="value">
                                            <?php if ($ptg_mutasi) {
                                                echo '(' . $ptg_mutasi['user'] . ') - ' . $ptg_mutasi['nama_lengkap'];
                                            } else {
                                                echo $ptg_barcode['nama_lengkap'];
                                            }
                                            ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <?php
            $aftap = "select * from htransaksi where `NoKantong`='$no_kantonga'";
            $aftap = mysqli_fetch_assoc(mysqli_query($dbi, $aftap));
            $asaldonor = substr($aftap['NoTrans'], 0, 1);
            if ($asaldonor == 'M') {
                $asaldonor = "Mobile Unit";
            } else {
                $asaldonor = "Dalam Gedung";
            }
            $jumlah_hb = $aftap['Hb'] . ' gr/dl';
            if (($aftap['Hb'] == null) or ($aftap['Hb'] == '') or ($aftap['Hb'] == '0')) {
                $hb1 = '';
                if ($aftap['jumHB'] == '1')
                    $jumlah_hb = 'Tenggelam';
                if ($aftap['jumHB'] == '2')
                    $jumlah_hb = 'Melayang';
                if ($aftap['jumHB'] == '3')
                    $jumlah_hb = 'Mengapung';
            }
            $ptghb = $aftap['petugasHB'];
            $ptgaftap = $aftap['petugas'];
            $ptgtensi = $aftap['petugasTensi'];
            $ptgadmin = $aftap['user'];
            $kodedokter = $aftap['NamaDokter'];
            $qpdokter = mysqli_fetch_assoc(mysqli_query($dbi, "select Nama from dokter_periksa where kode='$kodedokter'"));
            $qptensi = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$ptgtensi'"));
            $qpaftap = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$ptgaftap'"));
            $qphb = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$ptghb'"));
            $qpinput = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$ptgadmin'"));
            switch ($aftap['Pengambilan']) {
                case '0':
                    $status_aftap = 'Berhasil';
                    break;
                case '1':
                    $status_aftap = 'Batal';
                    break;
                case '2':
                    $status_aftap = 'Gagal';
                    break;
            }
            switch ($aftap['caraAmbil']) {
                //0=biasa, 1=tromboferesis, 2=leukaferesis, 3 =plasmaferesis, 4=Eritoferesis, 5=plebotomi
                case '0':
                    $caraambil = 'Donor Biasa';
                    break;
                case '1':
                    $caraambil = 'Tromboferesis';
                    break;
                case '2':
                    $caraambil = 'Leukoferesis';
                    break;
                case '3':
                    $caraambil = 'Plasmaferesis';
                    break;
                case '4':
                    $caraambil = 'Eritroferesis';
                    break;
                case '5':
                    $caraambil = 'Plebotomi';
                    break;
            }
            ?>
            <div class="panel-card mt-4">
                <div class="panel-card-header">
                    <div>Data Pengambilan</div>
                    <div></div>
                </div>

                <div class="panel-body">
                    <?php if ($aftap['NoTrans'] != ''): ?>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="table-box">
                                    <table class="info-table">
                                        <tr>
                                            <td class="label">No. Transaksi</td>
                                            <td class="value"><?php echo $aftap['NoTrans']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Tempat Pengambilan</td>
                                            <td class="value"><?php echo $asaldonor . ' ' . $aftap['Instansi']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Tgl & Waktu Registrasi</td>
                                            <td class="value"><?php echo $aftap['Tgl']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Tensi</td>
                                            <td class="value"><?php echo $aftap['tensi']; ?> mmHg</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Nadi</td>
                                            <td class="value"><?php echo $aftap['nadi']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Suhu</td>
                                            <td class="value"><?php echo $aftap['suhu']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Berat Badan</td>
                                            <td class="value"><?php echo $aftap['beratBadan']; ?> kg</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Hemoglobin</td>
                                            <td class="value"><?php echo $jumlah_hb; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Golongan Darah</td>
                                            <td class="value"><?php echo $aftap['gol_darah'] . ' Rh ' . $aftap['rhesus']; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-6 mb-3">
                                <div class="table-box">
                                    <table class="info-table">
                                        <tr>
                                            <td class="label">Jenis Pengambilan</td>
                                            <td class="value"><?php echo $caraambil; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Tgl & Waktu Pengambilan</td>
                                            <td class="value"><?php echo $stokkantong['tgl_Aftap']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Volume Pengambilan</td>
                                            <td class="value"><?php echo $aftap['volumekantong']; ?> ml</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Lama Pengambilan</td>
                                            <td class="value"><?php echo $lamaaftap; ?> menit</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Status Pengambilan</td>
                                            <td class="value"><?php echo $status_aftap; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Petugas Tensi</td>
                                            <td class="value"><?php echo $ptgtensi . ' - ' . $qptensi['nama_lengkap']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Petugas Anamnesa</td>
                                            <td class="value"><?php echo $qpdokter['Nama']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Petugas HB</td>
                                            <td class="value"><?php echo $ptghb . ' - ' . $qphb['nama_lengkap']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Petugas Aftap</td>
                                            <td class="value"><?php echo $ptgaftap . ' - ' . $qpaftap['nama_lengkap']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Petugas Input data</td>
                                            <td class="value"><?php echo $ptgadmin . ' - ' . $qpinput['nama_lengkap']; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info" role="alert">
                                    Data pengambilan belum dilakukan
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <br>

            <?php
            if ($levelUser != 'logistik' && $levelUser != 'p2d2s') {

                if ($aftap['jk'] == '1') {
                    $jeniskelamin = 'Perempuan';
                } else {
                    $jeniskelamin = 'Laki-laki';
                }
                if ($aftap['donorbaru'] == '0') {
                    $statusdonor = 'Donor Baru';
                } else {
                    $statusdonor = 'Donor Ulang';
                }
                if ($aftap['JenisDonor'] == '1') {
                    $jenisdonor = 'Donor Pengganti';
                } else {
                    $jenisdonor = 'Donor Sukarela';
                }
                $s_donor = "select * from pendonor where Kode='$aftap[KodePendonor]'";
                $pendonor = mysqli_fetch_assoc(mysqli_query($dbi, $s_donor));
                if ($level == '1') {
            ?>
                    <div class="panel-card mt-4">
                        <div class="panel-card-header">
                            <div>Data Pendonor</div>
                            <div></div>
                        </div>

                        <div class="panel-body">

                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box mt-2">
                                        <table class="info-table">
                                            <tr>
                                                <td class="label">Nama Pendonor</td>
                                                <td class="value"><?php echo $pendonor['Nama']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Nomor Identitas</td>
                                                <td class="value"><?php echo $pendonor['NoKTP']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Alamat</td>
                                                <td class="value">
                                                    <?php echo $pendonor['Alamat'] . ' ' . $pendonor['kelurahan'] . ' ' . $pendonor['kecamatan'] . ' ' . $pendonor['KodePos']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Wilayah</td>
                                                <td class="value"><?php echo $pendonor['Wilayah']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tempat Lahir</td>
                                                <td class="value"><?php echo $pendonor['TempatLhr']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tgl Lahir</td>
                                                <td class="value"><?php echo $pendonor['TglLhr']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Donasi</td>
                                                <td class="value"><?php echo $pendonor['jumDonor']; ?> Kali</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Nomor Telp</td>
                                                <td class="value"><?php echo $pendonor['telp']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Nomor HP</td>
                                                <td class="value"><?php echo $pendonor['telp2']; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="panel-card mt-4">
                        <div class="panel-card-header">
                            <div>Data Pendonor</div>
                            <div></div>
                        </div>

                        <div class="panel-body">
                            <?php if ($aftap['NoTrans'] != ''): ?>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <div class="table-box">
                                            <table class="info-table">
                                                <tr>
                                                    <td class="label">Kode Pendonor</td>
                                                    <td class="value"><?php echo $aftap['KodePendonor']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Jenis Kelamin</td>
                                                    <td class="value"><?php echo $jeniskelamin; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Jenis Donor</td>
                                                    <td class="value"><?php echo $jenisdonor; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Status Donor</td>
                                                    <td class="value"><?php echo $statusdonor; ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <div class="table-box">
                                            <table class="info-table">
                                                <tr>
                                                    <td class="label">Umur Donor</td>
                                                    <td class="value"><?php echo $aftap['umur']; ?> tahun</td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Donor ke</td>
                                                    <td class="value"><?php echo $aftap['donorke']; ?> kali</td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Pekerjaan</td>
                                                    <td class="value"><?php echo $aftap['pekerjaan']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="label">Gol Darah</td>
                                                    <td class="value">
                                                        <?php echo $pendonor['GolDarah'] . ' Rh ' . $pendonor['Rhesus']; ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-danger" role="alert">
                                            Data pendonor belum dilakukan
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php
                }
            } ?>


            <?php
            if ($levelUser != 'logistik' && $levelUser != 'p2d2s' && $levelUser != 'mobile') {

                $s_sr = "SELECT `hst_id`, `hst_notrans`, `hst_bagpengirim`, `hst_bagpenerima`, `hst_tgl`, `hst_asal`, `hst_jenis_st`, `hst_user`, `hst_pengirim`, `hst_penerima`, `hst_penerima2`, `hst_kode_alat`, `hst_suhuterima`, `hst_kondisiumum`, `hst_peruntukan`, `hst_modul`, `hst_shift_pengirim`, `hst_shift_penerima`,
    CASE
        WHEN (`dst_statusktg`='1' and `dst_sahktg`='0') THEN 'Aftap'
        WHEN (`dst_statusktg`='1' and `dst_sahktg`='1') THEN 'Karantina'
        WHEN (`dst_statusktg`='2') THEN 'Sehat'
        WHEN (`dst_statusktg`='3') THEN 'Keluar'
        WHEN (`dst_statusktg`='4') THEN 'Reaktif-Rusak'
        WHEN (`dst_statusktg`='5') THEN 'Rusak-gagal'
        WHEN (`dst_statusktg`='6') THEN 'Rusak-Dimusnahkan'
        ELSE 'Tidak ada' END AS `dst_statusktg`,
    CASE WHEN `dst_sample`='1' THEN 'Sesuai' ELSE 'Tdk Sesuai' END AS `dst_sample`,
    CASE WHEN `dst_sah`='1' THEN 'Sesuai' ELSE 'Tdk Sesuai' END AS `dst_sah`,
    `dst_nokantong`
    FROM `serahterima`
    INNER JOIN `serahterima_detail` ON `serahterima_detail`.`dst_notrans`=`serahterima`.`hst_notrans`
    WHERE `dst_nokantong`='$no_kantonga'";

                $sr = mysqli_fetch_assoc(mysqli_query($dbi, $s_sr));
                $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$sr[hst_user]'"));
                $pencatat = $usr['nama_lengkap'];
                $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$sr[hst_pengirim]'"));
                $pengirim = $usr['nama_lengkap'];
                $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$sr[hst_penerima]'"));
                $penerima = $usr['nama_lengkap'];
                $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$sr[hst_penerima2]'"));
                $penerima2 = $usr['nama_lengkap'];
            ?>

                <div class="panel-card mt-4">
                    <div class="panel-card-header">
                        <div>Data Serah Terima</div>
                        <div></div>
                    </div>
                    <div class="panel-body">
                        <?php if ($sr['hst_notrans'] != '') : ?>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="table-box">
                                        <table class="info-table">
                                            <tr>
                                                <td class="label">Tgl Serah Terima</td>
                                                <td class="value"><?php echo $sr['hst_tgl']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">No Transaksi</td>
                                                <td class="value"><?php echo $sr['hst_notrans']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Bagian Pengiriman</td>
                                                <td class="value"><?php echo $sr['hst_bagpengirim']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Bagian Penerima</td>
                                                <td class="value"><?php echo $sr['hst_bagpenerima']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Asal Darah/Sample</td>
                                                <td class="value"><?php echo $sr['hst_asal']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Kode Alat Pengiriman</td>
                                                <td class="value"><?php echo $sr['hst_kode_alat']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Suhu saat diserahkan</td>
                                                <td class="value"><?php echo $sr['hst_suhuterima']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Keadaan Umum</td>
                                                <td class="value"><?php echo $sr['hst_kondisiumum']; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <div class="table-box">
                                        <table class="info-table">
                                            <tr>
                                                <td class="label">Status Darah saat diterima</td>
                                                <td class="value"><?php echo $sr['dst_statusktg']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Kesesuaian Kantong Darah</td>
                                                <td class="value"><?php echo $sr['dst_sah']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Kesesuaian Sampel</td>
                                                <td class="value"><?php echo $sr['dst_sample']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Petugas Input Data</td>
                                                <td class="value"><?php echo $sr['hst_user'] . ' - ' . $pencatat; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Petugas Pengambilan</td>
                                                <td class="value"><?php echo $sr['hst_pengirim'] . ' - ' . $pengirim; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Petugas penerima darah</td>
                                                <td class="value"><?php echo $sr['hst_penerima'] . ' - ' . $penerima; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Petugas penerima sampel</td>
                                                <td class="value"><?php echo $sr['hst_penerima2'] . ' - ' . $penerima2; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-warning" role="alert">
                                        Data serah terima belum dilakukan
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <? } ?>

            <br>
            <?php
            if ($levelUser != 'logistik' && $levelUser != 'p2d2s' && $levelUser != 'kasir' && $levelUser != 'aftap' && $levelUser != 'mobile'  && $levelUser != 'konfirmasi' && $levelUser != 'komponen'  && $levelUser != 'kasir2') {
            ?>
                <div class="panel-card mt-4">
                    <div class="panel-card-header">
                        <div>Data Uji Saring IMLTD</div>
                        <div></div>
                    </div>
                    <div class="panel-body">

                        <div class="section-title mb-2">Pemeriksaan metode ELISA/CLHIA</div>
                        <?php
                        $sq_elisa = mysqli_query($dbi, "SELECT `id`, `noKantong`, `OD`, `COV`, `notrans`,
                    CASE
                        WHEN `jenisPeriksa`='0' THEN 'HBsAg'
                        WHEN `jenisPeriksa`='1' THEN 'Anti HCV'
                        WHEN `jenisPeriksa`='2' THEN 'Anti HIV'
                        WHEN `jenisPeriksa`='3' THEN 'Syphilis'
                    END AS Parameter,
                    CASE
                        WHEN `Hasil`='0' THEN 'Non Reaktif'
                        WHEN `Hasil`='1' THEN 'Reaktif'
                        WHEN `Hasil`='2' THEN 'Grayzone'
                    END AS Hasil,
                    `tglPeriksa`, `dicatatOleh`, `dicekOleh`, `DisahkanOleh`, `noLot`, `Metode`, `ulang`, `up_data`, `insert_on`
                    FROM `hasilelisa`
                    WHERE `noKantong`='$no_kantonga'
                    ORDER BY `id`");
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-4">
                                <thead style="background-color: mistyrose; color: #000;">
                                    <tr>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2">Kantong<br>Utama</th>
                                        <th rowspan="2">Transaksi</th>
                                        <th rowspan="2">Tanggal</th>
                                        <th rowspan="2">Parameter</th>
                                        <th rowspan="2">OD</th>
                                        <th rowspan="2">Hasil</th>
                                        <th colspan="3">Reagen</th>
                                        <th rowspan="2">Pencatat</th>
                                        <th rowspan="2">Di Cek</th>
                                        <th rowspan="2">Disahkan</th>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Lot</th>
                                        <th>ED</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    while ($imltd = mysqli_fetch_assoc($sq_elisa)) {
                                        $no++;
                                        if (($imltd['Hasil'] == "Reaktif") or ($imltd['Hasil'] == "Grayzone")) {
                                            $var_imltd = '1';
                                        }
                                        $sq_reagen = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `Nama`, `noLot`, `tglKad` FROM `reagen` WHERE kode='$imltd[noLot]'"));
                                        if ($sq_reagen['noLot'] == "") {
                                            $sq_reagen = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `Nama`, `noLot`, `tglKad` FROM `reagen` WHERE noLot='$imltd[noLot]'"));
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo $imltd['id']; ?></td>
                                            <td><?php echo $imltd['noKantong']; ?></td>
                                            <td><?php echo $imltd['notrans']; ?></td>
                                            <td><?php echo $imltd['tglPeriksa']; ?></td>
                                            <td><?php echo $imltd['Parameter']; ?></td>
                                            <td><?php echo $imltd['OD']; ?></td>
                                            <td><?php echo $imltd['Hasil']; ?></td>
                                            <td><?php echo $sq_reagen['Nama']; ?></td>
                                            <td><?php echo $sq_reagen['noLot']; ?></td>
                                            <td><?php echo $sq_reagen['tglKad']; ?></td>
                                            <td><?php echo $imltd['dicatatOleh']; ?></td>
                                            <td><?php echo $imltd['dicekOleh']; ?></td>
                                            <td><?php echo $imltd['DisahkanOleh']; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($no == 0) { ?>
                                        <tr>
                                            <td colspan="13" class="text-center">TIDAK ADA DATA PEMERIKSAAN IMLTD METODE ELISA</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="section-title mb-2">Pemeriksaan metode RAPID</div>
                        <?php
                        $sq_rapid = mysqli_query($dbi, "SELECT `id`, `NoTrans`, `noKantong`, `Kontrol`,
                    CASE
                        WHEN `jenisperiksa`='0' THEN 'HBsAg'
                        WHEN `jenisperiksa`='1' THEN 'Anti HCV'
                        WHEN `jenisperiksa`='2' THEN 'Anti HIV'
                        WHEN `jenisperiksa`='3' THEN 'Syphilis'
                    END AS Parameter,
                    CASE
                        WHEN `Hasil`='1' THEN 'Non Reaktif'
                        WHEN `Hasil`='0' THEN 'Reaktif'
                    END AS Hasil,
                    `nolot`, `tgl_tes`, `dicatatoleh`, `dicekOleh`, `DisahkanOleh`, `Metode`, `ulang`, `up_data`
                    FROM `drapidtest`
                    WHERE `noKantong`='$nkt'
                    ORDER BY `id`");
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-4">
                                <thead style="background-color: mistyrose; color: #000;">
                                    <tr>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2">Kantong<br>Utama</th>
                                        <th rowspan="2">Transaksi</th>
                                        <th rowspan="2">Tanggal</th>
                                        <th rowspan="2">Parameter</th>
                                        <th rowspan="2">Kontrol</th>
                                        <th rowspan="2">Hasil</th>
                                        <th colspan="3">Reagen</th>
                                        <th rowspan="2">Pencatat</th>
                                        <th rowspan="2">Di Cek</th>
                                        <th rowspan="2">Disahkan</th>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Lot</th>
                                        <th>ED</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    while ($imltd_r = mysqli_fetch_assoc($sq_rapid)) {
                                        $no++;
                                        if ($imltd_r['Hasil'] == "Reaktif") {
                                            $var_imltd = '1';
                                        }
                                        $sq_reagen = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `Nama`, `noLot`, `tglKad` FROM `reagen` WHERE kode='$imltd_r[nolot]'"));
                                    ?>
                                        <tr>
                                            <td><?php echo $imltd_r['id']; ?></td>
                                            <td><?php echo $imltd_r['noKantong']; ?></td>
                                            <td><?php echo $imltd_r['NoTrans']; ?></td>
                                            <td><?php echo $imltd_r['tgl_tes']; ?></td>
                                            <td><?php echo $imltd_r['Parameter']; ?></td>
                                            <td><?php echo $imltd_r['Kontrol']; ?></td>
                                            <td><?php echo $imltd_r['Hasil']; ?></td>
                                            <td><?php echo $sq_reagen['Nama']; ?></td>
                                            <td><?php echo $sq_reagen['noLot']; ?></td>
                                            <td><?php echo $sq_reagen['tglKad']; ?></td>
                                            <td><?php echo $imltd_r['dicatatoleh']; ?></td>
                                            <td><?php echo $imltd_r['dicekOleh']; ?></td>
                                            <td><?php echo $imltd_r['DisahkanOleh']; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($no == 0) { ?>
                                        <tr>
                                            <td colspan="13" class="text-center">TIDAK ADA DATA PEMERIKSAAN IMLTD METODE RAPID</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="section-title mb-2">Data Pemeriksaan NAT</div>
                        <?php
                        $sq_nat = mysqli_query($dbi, "SELECT *,
                        CASE
                            WHEN `Hasil`='0' THEN 'Non Reaktif'
                            WHEN `Hasil`='1' THEN 'Reaktif'
                            WHEN `Hasil`='2' THEN 'Invalid'
                        END AS Hasil
                        FROM `hasilnat`
                        WHERE `noKantong` = '$no_kantonga'
                        ORDER BY `id`");
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead style="background-color: mistyrose; color: #000;">
                                    <tr>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2">Kantong<br>Utama</th>
                                        <th rowspan="2">Tanggal</th>
                                        <th rowspan="2">OD</th>
                                        <th rowspan="2">Hasil</th>
                                        <th colspan="3">Reagen</th>
                                        <th rowspan="2">Pencatat</th>
                                        <th rowspan="2">Di Cek</th>
                                        <th rowspan="2">Disahkan</th>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Lot</th>
                                        <th>ED</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    while ($imltdn = mysqli_fetch_assoc($sq_nat)) {
                                        $no++;
                                        if (($imltdn['Hasil'] == "Reaktif") or ($imltdn['Hasil'] == "Invalid")) {
                                            $var_imltd = '1';
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo $imltdn['id']; ?></td>
                                            <td><?php echo $imltdn['noKantong']; ?></td>
                                            <td><?php echo $imltdn['tglPeriksa']; ?></td>
                                            <td><?php echo $imltdn['OD']; ?></td>
                                            <td><?php echo $imltdn['Hasil']; ?></td>
                                            <td>Ultrio</td>
                                            <td><?php echo $imltdn['noLot']; ?></td>
                                            <td><?php echo $imltdn['ed']; ?></td>
                                            <td><?php echo $imltdn['dicatatOleh']; ?></td>
                                            <td><?php echo $imltdn['dicatatOleh']; ?></td>
                                            <td><?php echo $imltdn['DisahkanOleh']; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($no == 0) { ?>
                                        <tr>
                                            <td colspan="11" class="text-center">TIDAK ADA DATA PEMERIKSAAN NAT</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            <? } ?>

            <br>
            <?php
            if ($levelUser != 'logistik' && $levelUser != 'p2d2s' && $levelUser != 'kasir' && $levelUser != 'aftap' && $levelUser != 'mobile'  && $levelUser != 'imltd' && $levelUser != 'komponen'  && $levelUser != 'kasir2') {
            ?>
                <div class="panel-card mt-4">
                    <div class="panel-card-header">
                        <div>Data Konfirmasi Golongan Darah</div>
                        <div></div>
                    </div>

                    <div class="panel-body">
                        <?php
                        $a = mysqli_query($dbi, "select * from dkonfirmasi where NoKantong='$no_kantonga' order by NoKonfirmasi ASC");
                        $no = 1;
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead style="background-color: mistyrose; color: #000;">
                                    <tr>
                                        <th rowspan="3">No</th>
                                        <th rowspan="3">Tanggal</th>
                                        <th rowspan="3">No Konfirmasi</th>
                                        <th rowspan="3">Kantong Utama</th>
                                        <th rowspan="3">Gol(Rh) Darah Asal</th>
                                        <th rowspan="3">Gol(Rh) Darah Baru</th>
                                        <th rowspan="3">Hasil</th>
                                        <th rowspan="3">Metode</th>
                                        <th colspan="3">Anti A</th>
                                        <th colspan="3">Anti B</th>
                                        <th colspan="3">Anti D</th>
                                        <th rowspan="3">TS-A</th>
                                        <th rowspan="3">TS-B</th>
                                        <th rowspan="3">TS-O</th>
                                        <th rowspan="3">AC</th>
                                        <th rowspan="3">BA 6%</th>
                                        <th rowspan="3">Petugas</th>
                                    </tr>
                                    <tr style="background-color: mistyrose; color: #000;">
                                        <th rowspan="2">Nilai</th>
                                        <th rowspan="2">Nolot</th>
                                        <th rowspan="2">Epx.</th>

                                        <th rowspan="2">Nilai</th>
                                        <th rowspan="2">Nolot</th>
                                        <th rowspan="2">Epx.</th>

                                        <th rowspan="2">Nilai</th>
                                        <th rowspan="2">Nolot</th>
                                        <th rowspan="2">Epx.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($a_dtransaksipermintaan = mysqli_fetch_assoc($a)) {
                                        if ($a_dtransaksipermintaan['Cocok'] == '1') {
                                            $var_kgd = '1';
                                        }

                                        $cocok1 = '-';
                                        if ($a_dtransaksipermintaan['Cocok'] == '0') $cocok1 = 'Cocok';
                                        if ($a_dtransaksipermintaan['Cocok'] == '1') $cocok1 = 'Tidak Cocok';

                                        $sel = '';
                                        if ($a_dtransaksipermintaan['sel'] == '0') $sel = 'Ya';
                                        if ($a_dtransaksipermintaan['sel'] == '1') $sel = 'Tidak';

                                        $serum = '';
                                        if ($a_dtransaksipermintaan['serum'] == '0') $serum = 'Ya';
                                        if ($a_dtransaksipermintaan['serum'] == '1') $serum = 'Tidak';

                                        $ac = '';
                                        if ($a_dtransaksipermintaan['ac'] == '0') $ac = 'Pos';
                                        if ($a_dtransaksipermintaan['ac'] == '1') $ac = 'Neg';

                                        $ba = '';
                                        if ($a_dtransaksipermintaan['ba'] == '0') $ba = 'Pos';
                                        if ($a_dtransaksipermintaan['ba'] == '1') $ba = 'Neg';

                                        $pengolahan = $a_dtransaksipermintaan['tgl'];
                                        $tglkel0 = date("Y-m-d", strtotime($pengolahan));
                                    ?>
                                        <tr>
                                            <td><?php echo $no++; ?>.</td>
                                            <td><?php echo $tglkel0; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['NoKonfirmasi']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['NoKantong']; ?></td>
                                            <td class="text-center">
                                                <?php echo $a_dtransaksipermintaan['goldarah_asal']; ?>(<?php echo $a_dtransaksipermintaan['rhesus_asal']; ?>)
                                            </td>
                                            <td class="text-center">
                                                <?php echo $a_dtransaksipermintaan['GolDarah']; ?>(<?php echo $a_dtransaksipermintaan['Rhesus']; ?>)
                                            </td>
                                            <td class="text-center"><?php echo $cocok1; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['metode']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['antiA']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['nolot_aa']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['expa']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['antiB']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['nolot_ab']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['expb']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['antiD']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['nolot_ad']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['expd']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['tA']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['tB']; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['tsO']; ?></td>
                                            <td><?php echo $ac; ?></td>
                                            <td><?php echo $ba; ?></td>
                                            <td><?php echo $a_dtransaksipermintaan['petugas']; ?></td>
                                        </tr>
                                    <?php } ?>

                                    <?php if ($no == 1) { ?>
                                        <tr>
                                            <td colspan="23" class="text-center">TIDAK ADA DATA PEMERIKSAAN KONFIRMASI GOLONGAN
                                                DARAH
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <? } ?>

            <?php
            if ($levelUser != 'logistik' && $levelUser != 'p2d2s' && $levelUser != 'kasir' && $levelUser != 'aftap' && $levelUser != 'mobile' && $levelUser != 'konfirmasi'  && $levelUser != 'imltd' && $levelUser != 'kasir2') {
            ?>
                <div class="panel-card mt-4">
                    <div class="panel-card-header">
                        <div>Data Pengolahan Darah</div>
                    </div>

                    <div class="panel-body">
                        <?php
                        $a = mysqli_query($dbi, "SELECT  `id`, `noKantong`, `Produk`, `tgl`, `aPisah`, `aPutar`, `aBeku`,                   
                    CASE WHEN `cara`='0' THEN 'Manual' ELSE 'Otomatis' END AS cara,
                    CASE
                        WHEN `pisah`='0' THEN 'Centrifuge 1'
                        WHEN `pisah`='1' THEN 'Centrifuge 2'
                        WHEN `pisah`='2' THEN 'Centrifuge 3'
                        WHEN `pisah`='3' THEN 'Centrifuge 4'
                        WHEN `pisah`='4' THEN 'Centrifuge 5'
                        WHEN `pisah`='5' THEN 'Sedimentasi'
                    END AS pisah,
                    `petugas`, `nama_lengkap`, `lengkap`
                    FROM `dpengolahan`
                    INNER JOIN `user` ON `user`.`id_user`=`dpengolahan`.`petugas`
                    INNER JOIN `produk` ON `produk`.`Nama`=`dpengolahan`.`Produk`
                    WHERE `noKantong`='$nkt'
                    ORDER BY tgl DESC");

                        $komponen = mysqli_fetch_assoc($a);

                        // Cari nama alat di tabel logbook_h
                        $alat_putar = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama_barang` FROM `logbook_h` WHERE `kode`='$komponen[aPutar]'"));
                        $alat_pisah = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama_barang` FROM `logbook_h` WHERE `kode`='$komponen[aPisah]'"));
                        $alat_beku = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama_barang` FROM `logbook_h` WHERE `kode`='$komponen[aBeku]'"));
                        if ($komponen['Produk'] == 'WB') {
                            $alt = "Tidak dilakukan";
                        } elseif ($komponen['Produk'] == 'PRC') {
                            $alt_putar = $alat_putar['nama_barang'] ? $alat_putar['nama_barang'] : "-";
                            $alt_pisah = $alat_pisah['nama_barang'] ? $alat_pisah['nama_barang'] : "-";
                        } else {
                            $alt_putar = $alat_putar['nama_barang'] ? $alat_putar['nama_barang'] : "-";
                            $alt_pisah = $alat_pisah['nama_barang'] ? $alat_pisah['nama_barang'] : "-";
                            $alt_beku = $alat_beku['nama_barang'] ? $alat_beku['nama_barang'] : "-";
                        }
                        $t = mysqli_fetch_assoc(mysqli_query($dbi, "select * from hpengolahan where nokantong='$nkt'"));
                        $dt = mysqli_fetch_assoc(mysqli_query($dbi, "select * from dpengolahan where noKantong='$nkt'"));


                        if ($dt['NoTrans'] != "") {
                        ?>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="table-box">
                                        <table class="info-table">
                                            <tr>
                                                <td class="label">Nomor Transaksi</td>
                                                <td class="value"><?php echo $dt['NoTrans']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tanggal Pengolahan</td>
                                                <td class="value"><?php echo $komponen['tgl']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Nama Produk</td>
                                                <td class="value">
                                                    <?php echo $komponen['Produk'] . ' (' . $komponen['lengkap'] . ')'; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Metode Pengolahan</td>
                                                <td class="value"><?php echo $komponen['cara']; ?></td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <div class="table-box">
                                        <table class="info-table">
                                            <tr>
                                                <td class="label">Pemutaran/Sentrifugasi</td>
                                                <td class="value">
                                                    <?php echo $komponen['Produk'] === 'WB' ? 'Tidak dilakukan' : $alt_putar; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Pemisahan</td>
                                                <td class="value">
                                                    <?php echo $komponen['Produk'] == 'WB' ? 'Tidak dilakukan' : $alt_pisah; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Pembekuan</td>
                                                <td class="value">
                                                    <?php echo $komponen['Produk'] === 'WB' ? 'Tidak dilakukan' : ($komponen['Produk'] === 'PRC' ? 'Tidak dilakukan' : $alt_beku); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Petugas Pengolahan</td>
                                                <td class="value">
                                                    <?php echo $komponen['petugas'] . ' - ' . $komponen['nama_lengkap']; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-warning" role="alert">
                                Data pengolahan darah belum dilakukan.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <? } ?>

            <br>
            <?php
            if ($levelUser != 'logistik' && $levelUser != 'p2d2s' && $levelUser != 'kasir' && $levelUser != 'aftap' && $levelUser != 'imltd' && $levelUser != 'mobile' && $levelUser != 'konfirmasi' && $levelUser != 'komponen'  && $levelUser != 'kasir2') {
            ?>
                <div class="panel-card mt-4">
                    <div class="panel-card-header">
                        <div>Data Release</div>
                        <div></div>
                    </div>

                    <div class="panel-body">
                        <?php
                        $rel = "select * FROM `release` where `rnokantong`='$nkt'";
                        $tmp = mysqli_fetch_assoc(mysqli_query($dbi, $rel));

                        $t = "select * from `timbang_darah` where nokantong='$nkt' order by id desc";
                        $tmbng = mysqli_fetch_assoc(mysqli_query($dbi, $t));

                        $ptg_timbang0 = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `nama_lengkap` like '%$tmbng[user]%'"));
                        $ptg_timbang = $ptg_timbang0['nama_lengkap'];

                        $ptg_prolis = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$tmp[ruser]'"));
                        $ptg_prolis = $ptg_prolis['nama_lengkap'];

                        $ptg_chek = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$tmp[rchecker]'"));
                        $ptg_chek = $ptg_chek['nama_lengkap'];

                        $ptg_sah = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `nama_lengkap` like '%$tmp[rpengesah]%'"));
                        $ptg_sah = $ptg_sah['nama_lengkap'];

                        if (strlen($tmp['rnotrans']) == 0) {
                        ?>
                            <div class="alert alert-danger mb-0">
                                <strong>Darah belum di RELEASE</strong>
                            </div>
                        <?php
                        } else {
                        ?>

                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="table-box">
                                        <table class="info-table">
                                            <tr>
                                                <td class="label">Nomor Transaksi</td>
                                                <td class="value"><?php echo $tmp['rnotrans']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tgl Release</td>
                                                <td class="value"><?php echo $tmp['rtgl']; ?></td>
                                            </tr>

                                            <tr>
                                                <td class="label" colspan="2"
                                                    style="background:#f7cfc9;font-weight:700;text-align:center;">
                                                    SPESIFIKASI KANTONG
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Label & Identitas sesuai spesifikasi</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rspek_kantong'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Kode Unik/Barcode sesuai spesifikasi</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rkode_unik'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="label" colspan="2"
                                                    style="background:#f7cfc9;font-weight:700;text-align:center;">
                                                    SELEKSI & PENGAMBILAN
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Seleksi donor memenuhi kriteria</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rspek_seleksi'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Waktu Pengambilan terpenuhi</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rspek_aftap'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="label" colspan="2"
                                                    style="background:#f7cfc9;font-weight:700;text-align:center;">
                                                    PEMERIKSAAN VISUAL
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tidak ada kebocoran</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rkebocoran'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Selang kantong sesuai spesifikasi</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rselang'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tidak Hemolysis</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rhemolysis'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tidak Lipemik</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rlipemik'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tidak Ikterik</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rikterik'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Plasma tidak kehijauan</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rkehijauan'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tidak ada bekuan pada Sel Darah Merah</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rbekuan'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <div class="table-box">
                                        <table class="info-table">
                                            <tr>
                                                <td class="label" colspan="2"
                                                    style="background:#f7cfc9;font-weight:700;text-align:center;">
                                                    PEMERIKSAAN DAN PENGOLAHAN
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Waktu Selesai Pengolahan terpenuhi</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rspek_pengolahan'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Volume sesuai dengan spesifikasi</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rspek_volume'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Hasil Pemeriksaan memenuhi spesifikasi</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rspek_imltd'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Pemeriksaan donasi sebelumnya terpenuhi</td>
                                                <td class="value text-center">
                                                    <?php if ($tmp['rspek_imltd_old'] == '1') { ?>
                                                        &radic;
                                                    <?php } else { ?>
                                                        <span
                                                            style="display:inline-block;background:#dc3545;color:#fff;padding:2px 10px;border-radius:4px;">X</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="label" colspan="2"
                                                    style="background:#f7cfc9;font-weight:700;text-align:center;">
                                                    VOLUME
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Berat Kantong (gram)</td>
                                                <td class="value"><?php echo number_format($tmp['rberat_timbang'], 2); ?> gr</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tanggal Penimbangan</td>
                                                <td class="value"><?php echo $tmbng['waktu']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Volume produk darah</td>
                                                <td class="value"><?php echo number_format(round($tmp['rvolume'], 2)); ?> ml</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Petugas penimbangan</td>
                                                <td class="value"><?php echo $ptg_timbang; ?></td>
                                            </tr>

                                            <tr>
                                                <td class="label" style="background:#f7cfc9;font-weight:700;">HASIL RELEASE</td>
                                                <td class="value"><?php echo $tmp['rsatus_ket']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Catatan</td>
                                                <td class="value"><?php echo $tmp['rnote'] == "" ? "-" : $tmp['rnote']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Petugas Release</td>
                                                <td class="value"><?php echo $ptg_prolis; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Dicek oleh</td>
                                                <td class="value"><?php echo $ptg_chek; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label">Diverifikasi oleh</td>
                                                <td class="value"><?php echo $ptg_sah; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                </div>
                </div>
            <?php }; ?>

            <br>
            <div class="panel-card mt-4">
                <div class="panel-card-header">
                    <div>Rekam Jejak Data Kantong (Audit Trail)</div>
                    <div></div>
                </div>

                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead style="background-color: mistyrose; color: #000;">
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Modul</th>
                                    <th class="text-center">Proses</th>
                                    <th class="text-center">Personil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $ada_audit = 0;

                                // barcode
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer, user_log.time_aksi,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$nkt%' AND user_log.aksi_user LIKE '%barcode%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                    $waktu_bukakantong = $komp['time_aksi'];
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td><?php echo $komp['tempat'] . $komp['aksi_user']; ?></td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // mutasi kantong ke aftap
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$no_kantonga%' AND user_log.aksi_user LIKE '%Pengesahan Kantong Logistik%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td>
                                            <?php
                                            if ($kantongke == 'A') {
                                                echo $komp['tempat'] . $komp['aksi_user'];
                                            } else {
                                                echo 'Kantong Utama : ' . $komp['tempat'] . $komp['aksi_user'];
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // Pengambilan Darah
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer, user_log.time_aksi,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$no_kantonga%' AND user_log.aksi_user LIKE '%Pengambilan%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                    $waktu_aftap = $komp['time_aksi'];
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td>
                                            <?php
                                            if ($kantongke == 'A') {
                                                echo $komp['tempat'] . $komp['aksi_user'];
                                            } else {
                                                echo 'Kantong Utama : ' . $komp['tempat'] . $komp['aksi_user'];
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // Pengesahan ke karantina
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$no_kantonga%' AND user_log.aksi_user LIKE '%Serah terima (Pengesahan)%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td>
                                            <?php
                                            if ($kantongke == 'A') {
                                                echo $komp['tempat'] . $komp['aksi_user'];
                                            } else {
                                                echo 'Kantong Utama : ' . $komp['tempat'] . $komp['aksi_user'];
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // KGD
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer, user_log.time_aksi,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$no_kantonga%' AND user_log.aksi_user LIKE '%KGD%' AND user_log.modul='KONFIRMASI'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td>
                                            <?php
                                            if ($kantongke == 'A') {
                                                echo $komp['tempat'] . $komp['aksi_user'];
                                            } else {
                                                echo 'Kantong Utama : ' . $komp['tempat'] . $komp['aksi_user'];
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // IMLTD
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$no_kantonga%' AND user_log.aksi_user LIKE '%IMLTD%' AND user_log.modul='IMLTD'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td>
                                            <?php
                                            if ($kantongke == 'A') {
                                                echo $komp['tempat'] . $komp['aksi_user'];
                                            } else {
                                                echo 'Kantong Utama : ' . $komp['tempat'] . $komp['aksi_user'];
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // Pengolahan
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer, user_log.time_aksi,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$nkt%' AND user_log.aksi_user LIKE '%Pengolahan%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                    $waktu_komponen = $komp['time_aksi'];
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td><?php echo $komp['tempat'] . $komp['aksi_user']; ?></td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // Release
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer, user_log.time_aksi,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$nkt%' AND user_log.aksi_user LIKE '%Release%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                    $waktu_komponen = $komp['time_aksi'];
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td><?php echo $komp['tempat'] . $komp['aksi_user']; ?></td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // cross
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer, user_log.time_aksi,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$nkt%' AND user_log.aksi_user LIKE '%crossmatch%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                    $waktu_komponen = $komp['time_aksi'];
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td><?php echo $komp['tempat'] . $komp['aksi_user']; ?></td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // distribusi ke
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer, user_log.time_aksi,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$nkt%' AND user_log.aksi_user LIKE '%Kirim ke%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                    $waktu_komponen = $komp['time_aksi'];
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td><?php echo $komp['tempat'] . $komp['aksi_user']; ?></td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                // Pemusnahan
                                $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi,
                                  DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
                                  user_log.user, user_log.komputer, user_log.time_aksi,
                                  CASE WHEN SUBSTRING(user_log.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END as tempat,
                                  user_log.modul, user_log.aksi_user, `user`.nama_lengkap
                           FROM user_log
                           LEFT JOIN user ON `user`.`id_user`=user_log.user
                           WHERE user_log.aksi_user LIKE '%$nkt%' AND user_log.aksi_user LIKE '%musnah%'
                           ORDER BY time_aksi ASC";
                                $a = mysqli_query($dbi, $a1);
                                while ($komp = mysqli_fetch_assoc($a)) {
                                    $ada_audit = 1;
                                    $waktu_komponen = $komp['time_aksi'];
                                ?>
                                    <tr>
                                        <td><?php echo $komp['tgl_aksi']; ?></td>
                                        <td><?php echo $komp['jam_aksi']; ?></td>
                                        <td><?php echo $komp['modul']; ?></td>
                                        <td><?php echo $komp['tempat'] . $komp['aksi_user']; ?></td>
                                        <td><?php echo $komp['nama_lengkap']; ?></td>
                                    </tr>
                                <?php }

                                if ($ada_audit == 0) {
                                ?>
                                    <tr>
                                        <td colspan="5" class="text-center">TIDAK ADA DATA AUDIT TRAIL</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    <?php
        } else {
            echo "<SCRIPT>alert('Produk/Komponen darah yang anda masukkan tidak terdaftar/tidak ada dalam SIMDONDAR');</SCRIPT>";
        }
    } ?>

    <div class="modal fade" id="modalKantong" tabindex="-1" role="dialog" aria-labelledby="modalKantongLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalKantongLabel">Data Kantong <span id="modalKantongNo"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalKantongBody">
                    <div class="text-center py-4">Memuat...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
        document.forms['cekkantong'].elements['noktg'].focus();
    </script>
    <script>
        $(document).on('click', '.btn-kantong', function() {
            const nokantong = $(this).data('nokantong');

            $('#modalKantongNo').text(nokantong);
            $('#modalKantongBody').html('<div class="text-center py-4">Memuat...</div>');
            $('#modalKantong').modal('show');

            const url = window.location.href.split('#')[0];
            const joiner = url.indexOf('?') > -1 ? '&' : '?';

            $.get(url + joiner + 'ajax_kantong=1&noktg=' + encodeURIComponent(nokantong), function(html) {
                $('#modalKantongBody').html(html);
            }).fail(function() {
                $('#modalKantongBody').html(
                    '<div class="alert alert-danger mb-0">Gagal memuat data kantong.</div>');
            });
        });
    </script>
</body>

</html>