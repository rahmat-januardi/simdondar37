<?php
function formatTanggal($inputTanggal)
{
    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    // Cek apakah input valid
    if (!$inputTanggal || !strtotime($inputTanggal)) {
        return "Tanggal tidak valid";
    }

    // Cek apakah formatnya hanya tanggal (YYYY-mm-dd) atau tanggal lengkap (YYYY-mm-dd 00:00:00)
    $isTimeIncluded = strpos($inputTanggal, '00:00:00') !== false;

    // Hanya tampilkan tanggal jika tidak ada waktu atau waktu adalah 00:00:00
    $split = explode('-', $inputTanggal);
    if (count($split) < 3) {
        return "Format tanggal tidak valid";
    }

    $dateTime = new DateTime($inputTanggal);

    // Jika waktu ada (00:00:00), format hanya tanggalnya saja
    if ($isTimeIncluded) {
        return $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y");
    }

    // Format lengkap jika waktu tidak 00:00:00
    // $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i") . " WIB";
    $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i");

    return $formattedDate;
}

function formatTanggalSaja($inputTanggal)
{
    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    // Cek apakah input valid
    if (!$inputTanggal || !strtotime($inputTanggal)) {
        return "Tanggal tidak valid";
    }

    // Cek apakah formatnya hanya tanggal (YYYY-mm-dd) atau tanggal lengkap (YYYY-mm-dd 00:00:00)
    $isTimeIncluded = strpos($inputTanggal, '00:00:00') !== false;

    // Hanya tampilkan tanggal jika tidak ada waktu atau waktu adalah 00:00:00
    $split = explode('-', $inputTanggal);
    if (count($split) < 3) {
        return "Format tanggal tidak valid";
    }

    $dateTime = new DateTime($inputTanggal);

    // Jika waktu ada (00:00:00), format hanya tanggalnya saja
    if ($isTimeIncluded) {
        return $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y");
    }

    // Format lengkap jika waktu tidak 00:00:00
    // $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i") . " WIB";
    $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y");

    return $formattedDate;
}

$labelJenis = array('HBsAg', 'HCV', 'HIV', 'Syphilis');
$jenisLengkap = array(0, 1, 2, 3);
function getHasilIMLTD($result, $labelJenis, $jenisLengkap)
{
    $pemeriksaan = array();
    $statusLengkap = true;
    $statusNR = true;
    // $statusGZ = false;
    $detailR = array();
    $tglPeriksa = '';

    while ($r = $result->fetch_assoc()) {
        $jenis = intval($r['jenisPeriksa']);
        $pemeriksaan[$jenis] = $r;
        $hasil = intval($r['Hasil']);

        if ($hasil == 1 || $hasil == 2) {
            $statusNR = false;
            $jenisName = isset($labelJenis[$jenis]) ? $labelJenis[$jenis] : 'Unknown';
            $detailR[] = "<span style='color:red;'>({$jenisName}) - (OD: {$r['OD']}, COV: {$r['COV']})</span>";
        }

        $tglPeriksa = $r['tglPeriksa'];
    }

    if (count($pemeriksaan) == 0) {
        return false;
    } elseif (count($pemeriksaan) < 4) {
        $kurang = array_diff($jenisLengkap, array_keys($pemeriksaan));
        $kurangLabel = array_map(function ($i) use ($labelJenis) {
            return $labelJenis[$i];
        }, $kurang);
        return 'Hasil pemeriksaan tdk lengkap.';
    } elseif (!$statusNR) {
        return "R";
    } else {
        return "NR";
    }
}

require_once '../config/dbi_connect.php';

if (isset($_GET['id'])) {
    $csd_id = isset($_GET['id']) ? $_GET['id'] : '';

    $query = "SELECT 
        c.dst_id AS 'id', 
        c.dst_notrans AS 'NT', 
        c.dst_no_aftap AS 'NTA', 
        c.dst_kodedonor AS 'kDonor', 
        c.dst_nokantong AS 'nK', 
        c.dst_golda AS 'gd', 
        c.dst_rh AS 'rh', 
        c.dst_volumektg AS 'volume',
        s.metoda,
        c.dst_produk AS 'produk',
        s.tglTerima, s.Status, s.merk, s.jenis, s.volumeasal, s.nolot_ktg, s.tgl_Aftap AS 'tglAftap', s.kadaluwarsa, s.position_bag AS 'posisi'
        FROM serahterima_detail_tmp AS c JOIN stokkantong AS s 
        ON c.dst_nokantong=s.noKantong 
        WHERE c.dst_id = ?";
    $stmt = $dbi->prepare($query);
    $stmt->bind_param("i", $csd_id);
    $stmt->execute();
    $stmt->store_result();
    $result = array();
    $stmt->bind_result(
        $id,
        $NT,
        $NTA,
        $kDonor,
        $nK,
        $gd,
        $rh,
        $volume,
        $metoda,
        $produk,
        $tglTerima,
        $Status,
        $merk,
        $jenis,
        $volumeasal,
        $nolot_ktg,
        $tglAftap,
        $kadaluwarsa,
        $posisi
    );
    while ($stmt->fetch()) {
        $result[] = array(
            'id' => $id,
            'NT' => $NT,
            'NTA' => $NTA,
            'kDonor' => $kDonor,
            'nK' => $nK,
            'gd' => $gd,
            'rh' => $rh,
            'volume' => $volume,
            'metoda' => $metoda,
            'produk' => $produk,
            'tglTerima' => $tglTerima,
            'Status' => $Status,
            'merk' => $merk,
            'jenis' => $jenis,
            'volumeasal' => $volumeasal,
            'nolot_ktg' => $nolot_ktg,
            'tglAftap' => $tglAftap,
            'kadaluwarsa' => $kadaluwarsa,
            'posisi' => $posisi
        );
    }

    if ($stmt->num_rows > 0) {
        $data = $result[0];

        $selST = "SELECT dst_oninsert FROM serahterima_detail WHERE dst_notrans LIKE 'ST%' AND dst_nokantong = ?";
        $stmtST = $dbi->prepare($selST);
        $stmtST->bind_param("s", $data['nK']);
        $stmtST->execute();
        $stmtST->bind_result($dst_oninsert);
        if ($stmtST->fetch()) {
            $data['oninsert'] = $dst_oninsert;
        } else {
            $data['oninsert'] = null;
        }
        $stmtST->close();

        $selHt = "SELECT NoTrans, Instansi FROM htransaksi WHERE NoKantong = ?";
        $stmtHt = $dbi->prepare($selHt);
        $stmtHt->bind_param("s", $data['nK']);
        $stmtHt->execute();
        $stmtHt->bind_result($NoTrans, $Instansi);
        if ($stmtHt->fetch()) {
            $data['noTrans'] = $NoTrans;
            $data['instansi'] = $Instansi;
        } else {
            $data['noTrans'] = null;
            $data['instansi'] = null;
        }

        if ($data['noTrans'] && strpos($data['noTrans'], 'D') !== 0) {
            $data['instansi'] = "MU - " . $data['instansi'];
        } else {
            $data['instansi'] = "DALAM GEDUNG";
        }
        $stmtHt->close();

        switch ($data['metoda']) {
            case 'TT':
                $data['metoda'] = "Top & Top [ TT ]";
                break;
            case 'TB':
                $data['metoda'] = "Top & Bottom [ TB ]";
                break;
            case 'TBF':
                $data['metoda'] = "Filter";
                break;
            default:
                $data['metoda'] = "";
                break;
        }

        switch ($data['jenis']) {
            case '1':
                $data['jenis'] = "Single Bag";
                break;
            case '2':
                $data['jenis'] = "Double Bag";
                break;
            case '3':
                $data['jenis'] = "Triple Bag";
                break;
            case '4':
                $data['jenis'] = "Quadruple Bag";
                break;
            case '6':
                $data['jenis'] = "Pediatrik Bag";
                break;
            default:
                $data['jenis'] = "-";
                break;
        }

        switch ($data['rh']) {
            case '+':
                $data['rh'] = "POS";
                break;
            case '-':
                $data['rh'] = "NEG";
                break;
            default:
                $data['rh'] = "UNKNOWN RHESUS";
                break;
        }

        // untuk TAB kedua dat adonor
        $query2 = "SELECT Kode, NoKTP, Nama, Alamat, CASE WHEN Jk = '0' THEN 'L' ELSE 'P' END AS Jk, 
            Pekerjaan, telp, TempatLhr, DATE(tglLhr) AS tglLhr, Status, GolDarah, Rhesus, 
            CASE WHEN Cekal = '0' THEN 'NEG' ELSE 'POS' END AS `StatusCK`,
            jumDonor, tglKembali
            FROM pendonor WHERE Kode = ?";
        $stmt2 = $dbi->prepare($query2);
        $stmt2->bind_param("s", $data['kDonor']);
        $stmt2->execute();
        $stmt2->bind_result(
            $Kode,
            $NoKTP,
            $Nama,
            $Alamat,
            $Jk,
            $Pekerjaan,
            $telp,
            $TempatLhr,
            $tglLhr,
            $Status,
            $GolDarah,
            $Rhesus,
            $StatusCK,
            $jumDonor,
            $tglKembali
        );
        if ($stmt2->fetch()) {
            $data['kDonor'] = $Kode;
            $data['noKTP'] = $NoKTP;
            $data['nama'] = $Nama;
            $data['alamat'] = $Alamat;
            $data['jk'] = $Jk;
            $data['pekerjaan'] = $Pekerjaan;
            $data['telp'] = $telp;
            $data['tempatLhr'] = $TempatLhr;
            $data['tglLahir'] = formatTanggalSaja($tglLhr);
            $data['status'] = $Status;
            $data['gd'] = $GolDarah;
            $data['rh'] = $Rhesus;
            $data['tglKembali'] = formatTanggalSaja($tglKembali);
            $data['jumDonor'] = $jumDonor;

            // Status Cekal
            if ($StatusCK == 'POS') {
                $data['cekal'] = "Ada riwayat cekal";
            } else {
                $data['cekal'] = "Tidak ada riwayat cekal";
            }
        } else {
            $data['kDonor'] = "-";
            $data['noKTP'] = "-";
            $data['nama'] = "-";
            $data['alamat'] = "-";
            $data['jk'] = "-";
            $data['tglLahir'] = "-";
            $data['pekerjaan'] = "-";
            $data['telp'] = "-";
            $data['tempatLhr'] = "-";
            $data['status'] = "-";
            $data['gd'] = "-";
            $data['rh'] = "-";
            $data['tglKembali'] = "-";
            $data['jumDonor'] = "-";
            $data['cekal'] = "Tidak ada riwayat cekal";
        }

        $selAktif = "SELECT KodePendonor
        FROM (
            SELECT KodePendonor, YEAR(Tgl) AS tahun, COUNT(*) AS jml_donor
            FROM htransaksi
            WHERE KodePendonor = ? AND Tgl >= DATE_SUB(CURDATE(), INTERVAL 4 YEAR)
            AND Pengambilan = '0'
            GROUP BY KodePendonor, YEAR(Tgl)
        ) AS per_tahun
        GROUP BY KodePendonor
        HAVING COUNT(DISTINCT tahun) = 4";
        $stmtAktif = $dbi->prepare($selAktif);
        $stmtAktif->bind_param("s", $data['kDonor']);
        $stmtAktif->execute();
        $stmtAktif->bind_result($KodePendonorAktif);
        $isAktif = $stmtAktif->fetch() ? "Aktif" : "Tidak Aktif";
        $stmtAktif->close();
        $data['status'] = $isAktif;
        $stmt2->close();
        ?>

        <div class="modal-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <!-- <li class="active"><a data-toggle="tab" href="#umum">🩸 Data Umum</a></li>
                <li><a data-toggle="tab" href="#riwayat">📜 Riwayat</a></li>
                <li><a data-toggle="tab" href="#catatan">🧾 Catatan</a></li> -->
                <li class="active"><a data-toggle="tab" href="#dtKantong">Data Kantong</a></li>
                <li><a data-toggle="tab" href="#dtDonor">Data Pendonor</a></li>
                <li><a data-toggle="tab" href="#riwayat">Anuu ..</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content" style="margin-top: 15px;">
                <div id="dtKantong" class="tab-pane fade in active">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <td><strong>Nomor Kantong</strong></td>
                                        <td>
                                            <?= $data['nK'] . ($data['metoda'] != "" ? " - <strong>" . $data['metoda'] . "</strong>" : ""); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><strong>Merk</strong></td>
                                        <td><?= $data['merk'] . ' - ' . $data['jenis'] . ' (' . $data['volumeasal'] . ' ml)' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nomor Lot KTG</strong></td>
                                        <td><?= $data['nolot_ktg'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Produk</strong></td>
                                        <td><?= $data['produk'] . ' - <strong>[ ' . $data['volume'] . ' ml ]</strong>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Golongan Darah</strong></td>
                                        <td><?= $data['gd'] . ' (' . $data['rh'] . ')' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lokasi</strong></td>
                                        <td><?= $data['posisi'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td class="status-kantong" data-status="<?= $data['Status'] ?>"></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-md-6">
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <td><strong>Asal Pengambilan</strong></td>
                                        <td><?= $data['instansi'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Input Logistik</strong></td>
                                        <td><?= formatTanggal($data['tglTerima']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Aftap</strong></td>
                                        <td><?= formatTanggal($data['tglAftap']) ?></td>
                                    </tr>
                                    <!-- <tr>
                                        <td><strong>Serah Terima</strong></td>
                                        <td><?= formatTanggal($data['oninsert']) ?></td>
                                    </tr> -->
                                    <tr>
                                        <td><strong>KGD</strong></td>
                                        <td><?= formatTanggal($data['kadaluwarsa']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>IMLTD</strong></td>
                                        <td><?= formatTanggal($data['kadaluwarsa']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pengolahan</strong></td>
                                        <td><?= formatTanggal($data['tglTerima']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Exp. Produk</strong></td>
                                        <td><?= formatTanggal($data['kadaluwarsa']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="dtDonor" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <td><strong>ID</strong></td>
                                        <td><?= $data['kDonor'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama</strong></td>
                                        <td><?= $data['nama'] . ' - [ ' . $data['jk'] . ' ]' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Alamat</strong></td>
                                        <td><?= $data['alamat'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>TTL</strong></td>
                                        <td><?= $data['tempatLhr'] . ', ' . $data['tglLahir'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <td><strong>Golongan Darah</strong></td>
                                        <td><?= $data['gd'] . ' (' . $data['rh'] . ')' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Donor Terbaru</strong></td>
                                        <td><?= $data['tglKembali'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><i>Screening</i></strong></td>
                                        <!-- <td class="status-kantong" data-status="<?= $data['cekal'] ?>"></td> -->
                                        <td><?= $data['cekal'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td><?= $data['status'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Riwayat Donor - <strong>(5 Data Terbaru)</strong>
                                - Jumlah Donor: <strong><?= $data['jumDonor'] ?> kali.</strong>
                            </h5>
                            <table id="riwayatDonorTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr style="background-color: #AFDDFF; color: #333;">
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Lokasi</th>
                                        <th class="text-center">No. Kantong</th>
                                        <th class="text-center">ABO (RH)</th>
                                        <th class="text-center">Volume</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center"><i>Screening</i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query untuk mendapatkan riwayat donor
                                    $queryRiwayat = "SELECT NoKantong, Tgl, 
                                        CASE 
                                            WHEN Pengambilan = '0' THEN 'Berhasil'
                                            WHEN Pengambilan = '2' THEN 'Gagal'
                                            ELSE 'Batal'
                                        END AS Pengambilan,
                                        CASE 
                                            WHEN Instansi IS NULL OR Instansi = '' THEN 'DALAM GEDUNG'
                                            ELSE Instansi
                                        END AS Instansi,
                                        gol_darah, 
                                        rhesus, Diambil, `Status` 
                                    FROM htransaksi 
                                    WHERE KodePendonor = ? 
                                    ORDER BY Tgl DESC 
                                    LIMIT 5
                                    ";
                                    $stmtRiwayat = $dbi->prepare($queryRiwayat);
                                    $stmtRiwayat->bind_param("s", $data['kDonor']);
                                    $stmtRiwayat->execute();
                                    $resultRiwayat = $stmtRiwayat->get_result();
                                    $no = 1;
                                    if ($resultRiwayat->num_rows > 0) {
                                        while ($rowRiwayat = $resultRiwayat->fetch_assoc()) {

                                            $hasilPeriksa = 'Tdk ditemukkan data pemeriksaan';

                                            $query = "SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode FROM hasilelisa WHERE noKantong = '$rowRiwayat[NoKantong]'";
                                            $resultP = $dbi->query($query);

                                            $hasilPeriksa = getHasilIMLTD($resultP, $labelJenis, $jenisLengkap);

                                            if ($hasilPeriksa === false) {
                                                $query2 = "SELECT noKantong, OD, COV, Hasil, notrans, jenisPeriksa, tglPeriksa, nolot, Metode FROM hasilnat WHERE noKantong = '$rowRiwayat[NoKantong]'";
                                                $resultU = $dbi->query($query2);
                                                $hasilPeriksa = getHasilIMLTD($resultU, $labelJenis, $jenisLengkap);

                                                if ($hasilPeriksa === false) {
                                                    $hasilPeriksa = 'Tdk ditemukkan data pemeriksaan';
                                                }
                                            }

                                            $rowClass = $no % 2 == 0
                                                // ? 'style="background-color:rgb(224, 247, 252)"'
                                                ? 'style="background-color:rgb(234, 243, 250)"'
                                                : 'style="background-color: #ffffff;"';

                                            $styleCK = $hasilPeriksa == 'R' ? 'style="background-color: #FFC1DA;"' : '';

                                            echo "<tr $rowClass>";
                                            echo "<td class='text-center'>" . $no++ . "</td>";
                                            echo "<td class='text-center'>" . formatTanggalSaja($rowRiwayat['Tgl']) . "</td>";
                                            echo "<td class='text-center'>" . $rowRiwayat['Instansi'] . "</td>";
                                            echo "<td class='text-center'>" . $rowRiwayat['NoKantong'] . "</td>";
                                            echo "<td class='text-center'>" . $rowRiwayat['gol_darah'] . " (" . $rowRiwayat['rhesus'] . ")</td>";
                                            echo "<td class='text-center'>" . (!empty($rowRiwayat['Diambil']) ? $rowRiwayat['Diambil'] . " ml" : "-") . "</td>";
                                            echo "<td class='text-center'>" . $rowRiwayat['Pengambilan'] . "</td>";
                                            echo "<td class='text-center' $styleCK>" . $hasilPeriksa . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>Tidak ada riwayat donor</td></tr>";
                                    }
                                    $stmtRiwayat->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <p style="background-color: #FFECDB; padding: 10px; border: 2px solid #FFD700; border-radius: 5px;">
                        <strong>Catatan / Validasi:
                            <br>
                        </strong>
                        Status Aktif:
                        <i>
                            Pendonor yang rutin mendonorkan darahnya setiap tahun minimal 1x (dalam 4 tahun terakhir)</i>
                    </p>
                </div>

                <div id="riwayat" class="tab-pane fade">
                    <p><i>(Riwayat lain-lain kedepan. Fitur ini menyusul menyesuaikan kebutuhan. Coming Soon)</i></p>
                </div>
            </div>
        </div>

        <!-- <div class="modal-footer">
            <button type="button" class="btn btn-warning" id="btnEditKantong">Edit</button>
            <button type="button" class="btn btn-success" id="btnSimpanKantong" style="display:none;">Simpan Perubahan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div> -->

        <?php
    } else {
        echo '<div class="modal-body"><p class="text-danger">Data tidak ditemukan.</p></div>';
    }
} else {
    echo '<div class="modal-body"><p class="text-warning">ID kantong tidak diberikan.</p></div>';
}
?>
