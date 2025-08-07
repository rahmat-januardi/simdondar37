<?php

/***********************************************
 * Author 	: suwena 
 * Date 	: 26 Mei 2018
 * Modifier 	: irawan
 * Date 	: 29 Januari 2024
 * Fungsi	: Daftar Permintaan Dropping Darah BDRS (Online)
 * Keterangan Modul : 
 * 		MENU BARU
 * Table terkait : 
 *		- Select 	: stokkantong, dpermintaan_darah JOIN hpermintaan_darah
 *		- exec (local)  : stokkantong (UPDATE), kirim_bdrs (INSERT)
 *		- exec (online) : b_minta (UPDATE), b_mintad (UPDATE), b_stok (INSERT)
 ***********************************************/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
    <link type="text/css" href="css/calender.css" rel="stylesheet" />
    <!--<link type="text/css" href="css/table1.css" rel="stylesheet" />-->
    <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
    <script type="text/javascript" src="js/tgl_rekap.js"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
    <link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />

    <style>
        #serahterima {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size: 16px;
            border-collapse: collapse;
        }

        #serahterima td,
        #serahterima th {
            border: 1px solid #ddd;
            padding: 5px;
        }

        #serahterima tr:nth-child(even) {
            background-color: #ffe6e6;
        }

        #serahterima tr:hover {
            background-color: #ddd;
        }

        #serahterima th {
            padding-top: 3px;
            padding-bottom: 3px;
            text-align: left;
            font-weight: lighter;
            background-color: #ff9999;
            color: #000000;
        }

        #serahterima input {
            padding-top: 2px;
            padding-bottom: 2px;
            text-align: left;
            background-color: lightyellow;
            color: #000000;
        }
    </style>
</head>

<?php
require_once('clogin.php');
require_once('config/dbi_connect.php');
$namauser = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];
$tglawal = date("Y-m-d");
$hariini = date("Y-m-d");
$notransaksi = "";

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
    $dateTime = new DateTime($inputTanggal);
    $split = explode('-', $inputTanggal);
    $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i") . " WIB";

    return $formattedDate;
}

?>

<body>
    <div class="container col-lg">
        <!-- membuat tulisan alert berwarna hijau dengan tulisan di tengah  -->
        <h3 class="alert alert-info text-center" role="alert">
            DAFTAR PERMINTAAN DROPPING DARAH (BDRS) - <i>Online</i>
        </h3>
        <br>
        <!-- membuat card untuk membungkus tabel bootstrap  -->
        <div class="card">
            <div class="card-body">

                <!--
                <a name="atas" id="atas"></a>
                <div style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">DAFTAR PERMINTAAN DROPPING DARAH (BDRS) - <i>Online</i></div>
                <br>
        -->
                <?php
                if (isset($_POST['waktu'])) {
                    $tglawal = $_POST['waktu'];
                    $hariini = "";
                }
                if ($_POST['waktu1'] != '') {
                    $hariini = $_POST['waktu1'];
                }
                ?>
                <form name="cari" method="POST" action="">
                    <table class="list" cellpadding=1 cellspacing="0" border="0">
                        <tr class="field">
                            <td align="left" nowrap>Dari tanggal :</td>
                            <td><input name="waktu" id="datepicker" value="<?= $tglawal ?>" type=text size=10></td>
                            <td align="right" nowrap>sampai tanggal :</td>
                            <td><input name="waktu1" id="datepicker1" value="<?= $hariini ?>" type=text size=10></td>
                            <td><input type=submit name=submit class="swn_button_blue" value="Ok"></td>
                        </tr>
                    </table>
                </form>

                <table id="serahterima"
                    style="border-collapse: collapse; border: 2px solid #808080; box-shadow: 1px 2px 2px #000000; width: 100%;">
                    <tr style="font-size: 12px">
                        <th style="height: 40px; text-align: center; font-weight: bold" rowspan="3">No</th>
                        <th style="height: 40px; text-align: center; font-weight: bold" rowspan="3">No. Transaksi</th>
                        <th style="height: 40px; text-align: center; font-weight: bold" rowspan="3">Jenis Permintaan</th>
                        <th style="height: 40px; text-align: center; font-weight: bold" rowspan="3">Tgl. Permintaan</th>
                        <!-- <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">Waktu</th> -->
                        <th style="height: 40px; text-align: center; font-weight: bold" rowspan="3">Asal</th>
                        <th style="height: 40px; text-align: center; font-weight: bold" rowspan="3">Petugas</th>
                        <th style="height: 40px; text-align: center; font-weight: bold" colspan="8">Jumlah</th>
                        <!-- <th style="height: 40px; text-align: center; font-weight: bold" rowspan="2">Lihat Formulir<br>Permintaan Dropping</th> -->
                        <th style="height: 40px; text-align: center; font-weight: bold" rowspan="3">Aksi</th>
                    </tr>
                    <tr style="font-size: 12px">
                        <th style="height: 40px; text-align: center; font-weight: bold" colspan="2">WB</th>
                        <th style="height: 40px; text-align: center; font-weight: bold" colspan="2">PRC</th>
                        <th style="height: 40px; text-align: center; font-weight: bold" colspan="2">TC</th>
                        <th style="height: 40px; text-align: center; font-weight: bold" colspan="2">FFP</th>
                    </tr>
                    <tr style="font-size: 12px">
                        <th style="height: 40px; text-align: center; font-weight: bold">Permintaan</th>
                        <th style="height: 40px; text-align: center; font-weight: bold">Terpenuhi</th>
                        <th style="height: 40px; text-align: center; font-weight: bold">Permintaan</th>
                        <th style="height: 40px; text-align: center; font-weight: bold">Terpenuhi</th>
                        <th style="height: 40px; text-align: center; font-weight: bold">Permintaan</th>
                        <th style="height: 40px; text-align: center; font-weight: bold">Terpenuhi</th>
                        <th style="height: 40px; text-align: center; font-weight: bold">Permintaan</th>
                        <th style="height: 40px; text-align: center; font-weight: bold">Terpenuhi</th>
                    </tr>
                    <?php
                    $no = 0;
                    $qry = "SELECT 
                    d.`id_permintaan`, 
                    d.`jenisMinta`, 
                    d.`bdrs_udd`, 
                    d.`noTrans`, 
                    d.`kodeBDRS`, 
                    d.`nosurat`, 
                    d.`ptgMinta`, 
                    DATE_FORMAT(d.`tgl`,'%d-%m-%Y') as tgl, 
                    d.`auth1`, 
                    d.`date1`, 
                    d.`stat1`, 
                    d.`auth2`, 
                    d.`date2`, 
                    d.`stat2`, 
                    d.`tgl_proses`, 
                    d.`status` 
                    FROM `dpermintaan_darah` d 
                    WHERE (DATE(d.`tgl`)>='$tglawal' AND DATE(d.`tgl`)<='$hariini') 
                    AND d.`noTrans` IS NOT NULL 
                    AND d.`kodeBDRS` IS NOT NULL";
                    //echo "$qry";
                    $sql = mysqli_query($dbi, $qry);
                    $no = 0;
                    $jmltotal = 0;
                    while ($tmp = mysqli_fetch_assoc($sql)) {
                        $no++;

			switch ($tmp['jenisMinta']) {
			    case '0':
				$jM = "DROPPING";
				break;
			    case '1':
				$jM = "RETUR";
				break;
			    default:
				$jM = "-";
				break;
			}

                        switch ($tmp['status']) {
                            case '0':
                                $stts = "Belum diProses";
                                break;
                            case '1':
                                $stts = "Selesai";
                                break;
                            case '2':
                                $stts = "Dibatalkan";
                                break;
                            default:
                                $stts = "-";
                                break;
                        }

                        $selBdrs = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama` FROM `bdrs` WHERE `kd_online` = '$tmp[kodeBDRS]'"));

                        $jumWB = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COALESCE(SUM(`jumlah`), 0) AS Jumlah FROM `hpermintaan_darah` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'WB'"));
                        $jumPRC = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COALESCE(SUM(`jumlah`), 0) AS Jumlah FROM `hpermintaan_darah` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'PRC'"));
                        $jumTC = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COALESCE(SUM(`jumlah`), 0) AS Jumlah FROM `hpermintaan_darah` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'TC'"));
                        $jumFFP = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COALESCE(SUM(`jumlah`), 0) AS Jumlah FROM `hpermintaan_darah` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'FFP'"));

                        $jumBeriWB = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COUNT(`nokantong`) AS Jumlah FROM `kirim_bdrs` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'WB'"));
                        $jumBeriPRC = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COUNT(`nokantong`) AS Jumlah FROM `kirim_bdrs` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'PRC'"));
                        $jumBeriTC = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COUNT(`nokantong`) AS Jumlah FROM `kirim_bdrs` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'TC'"));
                        $jumBeriFFP = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT COUNT(`nokantong`) AS Jumlah FROM `kirim_bdrs` WHERE `id_permintaan` = '$tmp[id_permintaan]' AND `produk` = 'FFP'"));
                        ?>
                        <tr style="text-align: center; font-size: 12px;height: 30px;">
                            <td align="right"><?php echo $no; ?>.</td>
                            <td style="text-align: center"><?php echo $tmp['noTrans']; ?></td>
                            <td style="text-align: center"><?php echo $jM; ?></td>
                            <td style="text-align: center"><?php echo $tmp['tgl']; ?></td>
                            <td style="text-align: center"><?php echo $selBdrs['nama']; ?></td>
                            <td style="text-align: center"><?php echo $tmp['ptgMinta']; ?></td>
                            <td style="text-align: center"><?php echo $jumWB['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jumBeriWB['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jumPRC['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jumBeriPRC['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jumTC['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jumBeriTC['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jumFFP['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jumBeriFFP['Jumlah']; ?></td>
                            <!--<td style="text-align: center"><a href="#">Lihat</a></td> -->
                            <td style="text-align: center">
                                <?php if ($tmp['status'] == 0) {
                                    $selAD = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `stat1`, `date2`, `stat2` FROM `dpermintaan_darah` WHERE `noTrans` = '$tmp[noTrans]'"));
					?>
                                        <a href="pmiqa.php?module=form_bdrs_ol&nt=<?= $tmp['noTrans'] ?>">
                                            <button class="btn-accept btn-success">
                                                Proses
                                            </button>
                                        </a>
                                        |
                                        <button class="btn-reject btn-danger"
                                            data-id="<?php echo $tmp['noTrans']; ?>">Reject</button>
                                <?php } else {
                                    echo $stts;
                                }
                                ?>
                            </td>

                        </tr>
                        <?php
                        $jmltotalWB = isset($jmltotalWB) ? $jmltotalWB + $jumWB['Jumlah'] : $jumWB['Jumlah'];
                        $jmltotalPRC = isset($jmltotalPRC) ? $jmltotalPRC + $jumPRC['Jumlah'] : $jumPRC['Jumlah'];
                        $jmltotalTC = isset($jmltotalTC) ? $jmltotalTC + $jumTC['Jumlah'] : $jumTC['Jumlah'];
                        $jmltotalFFP = isset($jmltotalFFP) ? $jmltotalFFP + $jumFFP['Jumlah'] : $jumFFP['Jumlah'];

                        $jmltotal = $jmltotal + $jumWB['Jumlah'] + $jumPRC['Jumlah'] + $jumTC['Jumlah'] + $jumFFP['Jumlah'];
                        $jmltotalBeri = $jmltotal + $jumBeriWB['Jumlah'] + $jumBeriPRC['Jumlah'] + $jumBeriTC['Jumlah'] + $jumBeriFFP['Jumlah'];
                    }
                    if ($no == "0") {
                        ?>
                        <tr style="font-size: 16px;height: 40px; text-align: center;">
                            <td colspan="16">Tidak ada data</td>
                        </tr>
                        <?php
                    } else { ?>
                        <tr style="font-size: 12px;height: 40px; text-align: center;">
                            <td style="text-align: center" colspan="8">TOTAL</td>
                            <td style="text-align: center"><?php echo $jmltotalWB; ?></td>
                            <td style="text-align: center"><?php echo $jumBeriWB['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jmltotalPRC; ?></td>
                            <td style="text-align: center"><?php echo $jumBeriPRC['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jmltotalTC; ?></td>
                            <td style="text-align: center"><?php echo $jumBeriTC['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo $jmltotalFFP; ?></td>
                            <td style="text-align: center"><?php echo $jumBeriFFP['Jumlah']; ?></td>
                            <td style="text-align: center"><?php echo "Permintaan: $jmltotal, Terpenuhi: $jmltotalBeri"; ?>
                            </td>
                        </tr>

                    <?php } ?>
                </table>
                <br>
            </div>
        </div>
        <div style="font-size:10px; color:#000000; font-family: Helvetica Neue, Helvetica, Arial, sans-serif;">Build :
            19-01-2024</div>
    </div>


    <!-- Modal Detail -->
    <div class="modal fade" id="mdInfo" aria-hidden="true" aria-labelledby="exampleOptionalLarge" role="dialog"
        tabindex="-1">
        <div class="modal-dialog modal-simple modal-center modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <p>
                    <h4 class="modal-title">Detail Permintaan - </h4>
                    <h4 class="modal-title" id="infoNt"></h4>
                    </p>
                </div>
                <!-- <form action="" method="POST"> -->
                <div class="modal-body">
                    <!-- <h4>Detail Permintaan Darah</h4> -->
                    <table>
                        <tr>
                            <td style="font-size: 12pt">No. Transaksi</td>
                            <td>:</td>
                            <td style="text-align: left; font-size: 12pt; font-weight: bold;" id="infoNt">No. Transaksi
                            </td>
                        </tr>
                        <tr>
                            <td>Asal</td>
                            <td>:</td>
                            <td style="text-align: left;" id="infoAsal">Asal</td>
                        </tr>
                        <tr>
                            <td>Tujuan</td>
                            <td>:</td>
                            <td style="text-align: left;" id="infoTujuan">Tujuan</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12pt">Jenis Permintaan</td>
                            <td>:</td>
                            <td style="text-align: left; font-size: 12pt; font-weight: bold;" id="infoJenis">Jenis
                                Permintaan</td>
                        </tr>
                        <tr>
                            <td>Tanggal Permintaan</td>
                            <td>:</td>
                            <td style="text-align: left;" id="infoTglMinta">Tanggal Permintaan</td>
                        </tr>
                        <tr>
                            <td>Petugas BDRS</td>
                            <td>:</td>
                            <td style="text-align: left;" id="infoPtgMinta">Petugas BDRS</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12pt">Status Permintaan</td>
                            <td>:</td>
                            <td style="text-align: left; font-size: 12pt; font-weight: bold;" id="infoStats">Status
                                Permintaan</td>
                        </tr>
                    </table>
                    <input type="hidden" name="infoID" id="infoID">
                    <br><br>
                    <table class="table table-striped" border="1px">
                        <thead>
                            <tr>
                                <th colspan="6" class="text-center text-middle">Komponen Darah yang Diminta</th>
                            </tr>
                            <tr>
                                <th rowspan="2" class="text-center text-middle">Komponen Darah</th>
                                <th colspan="5" class="text-center text-middle">Golongan Darah</th>
                            </tr>
                            <tr>
                                <th class="text-center text-middle">A</th>
                                <th class="text-center text-middle">B</th>
                                <th class="text-center text-middle">O</th>
                                <th class="text-center text-middle">AB</th>
                                <th class="text-center text-middle">JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>WB (<i>Whole Blood</i>)</td>
                                <td class="text-center text-middle" id="infoWba"></td>
                                <td class="text-center text-middle" id="infoWbb"></td>
                                <td class="text-center text-middle" id="infoWbo"></td>
                                <td class="text-center text-middle" id="infoWbab"></td>
                                <td class="text-center text-middle" id="infoJumWB"></td>
                            </tr>
                            <tr>
                                <td>PRC (<i>Packet Red Cells</i>)</td>
                                <td class="text-center text-middle" id="infoPrca"></td>
                                <td class="text-center text-middle" id="infoPrcb"></td>
                                <td class="text-center text-middle" id="infoPrco"></td>
                                <td class="text-center text-middle" id="infoPrcab"></td>
                                <td class="text-center text-middle" id="infoJumPRC"></td>
                            </tr>
                            <tr>
                                <td>TC (<i>Thrombocyte</i>)</td>
                                <td class="text-center text-middle" id="infoTca"></td>
                                <td class="text-center text-middle" id="infoTcb"></td>
                                <td class="text-center text-middle" id="infoTco"></td>
                                <td class="text-center text-middle" id="infoTcab"></td>
                                <td class="text-center text-middle" id="infoJumTC"></td>
                            </tr>
                            <tr>
                                <td>FFP (<i>Fresh Frozen Plasma</i>)</td>
                                <td class="text-center text-middle" id="infoFfpa"></td>
                                <td class="text-center text-middle" id="infoFfpb"></td>
                                <td class="text-center text-middle" id="infoFfpo"></td>
                                <td class="text-center text-middle" id="infoFfpab"></td>
                                <td class="text-center text-middle" id="infoJumFFP"></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <!-- <button type="submit" name="infoData" class="btn btn-primary">IYA</button> 
                                <button type="button" class="btn btn-default btn-pure" data-dismiss="modal">TIDAK</button> -->
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>
    <!-- End Modal Detail -->


    <script>
        $(document).ready(function () {
            $("#mdInfo").on('show.bs.modal', function (e) {
                var id = $(e.relatedTarget).attr('data-id');
                var nt = id.split('*');
                $('#mdInfo #infoID').val(nt[0]);
                $('#mdInfo #infoNt').text(nt[1]);

                $.ajax({
                    url: "view/detailMinta.php",
                    type: 'POST',
                    data: {
                        id: nt[0],
                        nt: nt[1]
                    },
                    success: function (data) {
                        $('#mdInfo #infoAsal').text(data.asal);
                        $('#mdInfo #infoTujuan').text(data.tujuan);
                        $('#mdInfo #infoJenis').text(data.jenis).addClass(data.textJenis);
                        $('#mdInfo #infoTglMinta').text(data.tglMinta);
                        $('#mdInfo #infoPtgMinta').text(data.petugasMinta);
                        $('#mdInfo #infoStats').text(data.statsMinta).addClass(data.textStats);
                        $('#mdInfo #infoWba').text(data.wbA);
                        $('#mdInfo #infoWbb').text(data.wbB);
                        $('#mdInfo #infoWbo').text(data.wbO);
                        $('#mdInfo #infoWbab').text(data.wbAB);
                        $('#mdInfo #infoJumWB').text(data.jumWB);
                        $('#mdInfo #infoPrca').text(data.prcA);
                        $('#mdInfo #infoPrcb').text(data.prcB);
                        $('#mdInfo #infoPrco').text(data.prcO);
                        $('#mdInfo #infoPrcab').text(data.prcAB);
                        $('#mdInfo #infoJumPRC').text(data.jumPRC);
                        $('#mdInfo #infoTca').text(data.tcA);
                        $('#mdInfo #infoTcb').text(data.tcB);
                        $('#mdInfo #infoTco').text(data.tcO);
                        $('#mdInfo #infoTcab').text(data.tcAB);
                        $('#mdInfo #infoJumTC').text(data.jumTC);
                        $('#mdInfo #infoFfpa').text(data.ffpA);
                        $('#mdInfo #infoFfpb').text(data.ffpB);
                        $('#mdInfo #infoFfpo').text(data.ffpO);
                        $('#mdInfo #infoFfpab').text(data.ffpAB);
                        $('#mdInfo #infoJumFFP').text(data.jumFFP);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                    }
                });

            });

            $("#mdInfo form").submit(function (e) {
                e.preventDefault(); // Menghentikan pengiriman formulir biasa

                // Ambil data dari formulir
                var infoId = $('#infoID').val();
                var infoNt = $('#infoNt').text();

            });
        });
    </script>

    <script>
        document.querySelectorAll('.btn-reject').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                // Tampilkan konfirmasi
                const confirmed = confirm('Apakah Anda yakin ingin menolak data ini?');
                if (confirmed) {
                    // Kirim permintaan ke server
                    fetch('modul/dropping/batalkanPermintaan.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Data permintaan dropping berhasil ditolak!');
                                window.location.href = 'pmiqa.php?module=permintaan_bdrs_list';
                            } else {
                                alert('Gagal menolak data: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>


</body>
