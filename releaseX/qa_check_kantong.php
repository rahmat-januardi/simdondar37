<?php
require_once('clogin.php');
require_once('config/dbi_connect.php');
$namauser = $_SESSION['namauser'];
$namalengkap = $_SESSION['nama_lengkap'];
$level = $_GET['l'];
if (empty($level)) {
    $level = '0';
}
?>

<!DOCTYPE html>
<link type="text/css" href="../css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="../css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="../css/style.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<html>

<head>
    <style>
        tr {
            background-color: #ffffff;
        }

        .initial {
            background-color: #ffffff;
            color: #000000
        }

        .normal {
            background-color: #ffffff;
        }

        .highlight {
            background-color: #7CFC00
        }
    </style>

    <style>
        .control {
            font-family: arial;
            display: block;
            position: relative;
            padding-left: 30px;
            margin-bottom: 2px;
            padding-top: 3px;
            cursor: pointer;
            font-size: 16px;
        }

        .control input {
            position: absolute;
            z-index: -1;
            opacity: 0;
        }

        .control_indicator {
            position: absolute;
            top: 2px;
            left: 0;
            height: 20px;
            width: 20px;
            background: #e6e6e6;
            border: 0px solid #000000;
        }

        .control-radio .control_indicator {
            border-radius: undefined;
        }

        .control:hover input~.control_indicator,
        .control input:focus~.control_indicator {
            background: #cccccc;
        }

        .control input:checked~.control_indicator {
            background: #ff0000;
        }

        .control:hover input:not([disabled]):checked~.control_indicator,
        .control input:checked:focus~.control_indicator {
            background: #e6647d;
        }

        .control input:disabled~.control_indicator {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }

        .control_indicator:after {
            box-sizing: unset;
            content: '';
            position: absolute;
            display: none;
        }

        .control input:checked~.control_indicator:after {
            display: block;
        }

        .control-checkbox .control_indicator:after {
            left: 8px;
            top: 4px;
            width: 3px;
            height: 8px;
            border: solid #ffffff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .control-checkbox input:disabled~.control_indicator:after {
            border-color: #7b7b7b;
        }
    </style>

    <style type="text/css">
        .styled-select select {
            background-color: #F0FFFF;
            border: none;
            width: auto;
            padding: 3px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
    <style>
        table {
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid brown;
        }
    </style>
    <style>
        body {
            font-family: "Lato", sans-serif;
        }

        .tablink {
            background-color: red;
            color: white;
            float: left;
            border: 1px solid brown;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            font-size: 15px;
            width: 16.6%;
        }

        .tablink:hover {
            background-color: #777;
        }

        /* Style the tab content */
        .tabcontent {
            color: red;
            display: none;
            padding: 10px;
            border: 1px solid brown;
        }

        #visual {
            background-color: white;
        }

        #kantong {
            background-color: white;
        }

        #pemeriksaan {
            background-color: white;
        }

        #pengolahan {
            background-color: white;
        }

        #trace {
            background-color: white;
        }

        #history {
            background-color: white;
        }
    </style>

    <script type="text/javascript" language="JavaScript">
        document.forms['cekkantong'].elements['noktg'].focus();
    </script>

</head>

<body OnLoad="document.cekkantong.noktg.focus();">

    <form name="cekkantong" method=post>
        <font size="4" color=00008B>Cek Kantong </font>
        <INPUT type="text" name="noktg" style="text-transform:uppercase" minlength="5" required="">
        <input type=submit name=cari value=OK class="swn_button_blue">
    </form>


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
            switch ($stokkantong['jenis']) {
                case '1':
                    $jeniskantong = 'Single';
                    break;
                case '2':
                    $jeniskantong = 'Double';
                    break;
                case '3':
                    $jeniskantong = 'Triple';
                    break;
                case '4':
                    $jeniskantong = 'Quadruple';
                    break;
                case '5':
                    $jeniskantong = 'Quadruple T&B';
                    break;
                case '6':
                    $jeniskantong = 'Pediatrik';
                    break;
                default:
                    $jeniskantong = '';
            }
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
            <div>
                <font size="4" color=00008B>DATA PRODUK DARAH/KOMPONEN DARAH</font>
            </div>
            <table width="100%" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="50%">
                        <table width="100%">
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">No. Kantong</td>
                                <td><?php echo $nkt ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Asal UTD</td>
                                <td><?php echo $asalutd ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Produk</td>
                                <td><?php echo $produk . ' - ' . $namaproduk ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Golongan Darah</td>
                                <td><?php echo $stokkantong['gol_darah'] . ' Rh (', $stokkantong['RhesusDrh'] . ')' ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Volume</td>
                                <td><?php echo $volume_darah ?> ml</td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Status darah/komponen</td>
                                <td><?php echo $statuskantong ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Hasil Release</td>
                                <td><?php echo $hasilrelease ?></td>
                            </tr>

                        </table>
                    </td>
                    <td width="50%">
                        <table width="100%">
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Pengambilan</td>
                                <td><?php echo $stokkantong['tgl_Aftap'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Uji Saring IMLTD</td>
                                <td><?php echo $stokkantong['tglperiksa'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl KGD</td>
                                <td><?php echo $konfirmasi['tgl'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Pengolahan</td>
                                <td><?php echo $stokkantong['tglpengolahan'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Release</td>
                                <td><?php echo $stokkantong['tgl_release'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Kadaluarsa</td>
                                <td><?php echo $tgledkomponen ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Keluar</td>
                                <td><?php echo $stokkantong['tgl_keluar'] ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php
            //jika darah  keluar
            if ($stokkantong['Status'] == '3') {
                ?><br>
                <div>
                    <font size="4" color=00008B>DATA DISTRIBUSI</font>
                </div>
                <?php
                if (substr($stokkantong['stat2'], 0, 1) == 'b') {
                    //Distribusi ke BRDS
                    $q = "SELECT `id`,`nokantong`,`bdrs`,`tgl`,`petugas`,`nama`, `nama_lengkap`
                    FROM `kirimbdrs` k  inner join `bdrs` b on b.`kode`=k.`bdrs`  inner join `user` u on u.`id_user`=k.`petugas`
                    WHERE `nokantong`='$nkt'
                    ORDER by `id` DESC";
                    $kirimbdrs = mysqli_fetch_assoc(mysqli_query($dbi, $q));
                    ?>
                    <table width="100%" cellpadding="2" cellspacing="2">
                        <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td style="background-color: mistyrose">Tanggal dikeluarkan</td>
                            <td><?php echo $kirimbdrs['tgl'] ?></td>
                        </tr>
                        <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td style="background-color: mistyrose">BRDS Tujuan</td>
                            <td><?php echo $kirimbdrs['nama'] ?></td>
                        </tr>
                        <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td style="background-color: mistyrose">Petugas</td>
                            <td><?php echo $kirimbdrs['petugas'] . ' - ' . $kirimbdrs['nama_lengkap'] ?></td>
                        </tr>
                    </table>
                    <?php
                } elseif (substr($stokkantong['stat2'], 0, 1) > '0') {
                    //Distribusi ke UDD
                    $q = "SELECT k.`id`,k.`nokantong`,k.`udd`,k.`tgl`,k.`petugas`,b.`nama`, u.`nama_lengkap`
                    FROM `kirimudd` k inner join `utd` b on b.`id`=k.`udd` inner join `user` u on u.`id_user`=k.`petugas`
                    WHERE `nokantong`='$nkt' ORDER by k.`id` DESC";
                    $kirimbdrs = mysqli_fetch_assoc(mysqli_query($dbi, $q));
                    ?>
                    <table width="100%" cellpadding="2" cellspacing="2">
                        <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td style="background-color: mistyrose">Tanggal dikeluarkan</td>
                            <td><?php echo $kirimbdrs['tgl'] ?></td>
                        </tr>
                        <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td style="background-color: mistyrose">UDD Tujuan</td>
                            <td><?php echo $kirimbdrs['nama'] ?></td>
                        </tr>
                        <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td style="background-color: mistyrose">Petugas</td>
                            <td><?php echo $kirimbdrs['petugas'] . ' - ' . $kirimbdrs['nama_lengkap'] ?></td>
                        </tr>
                    </table>
                    <?php
                } else {
                    $s_dist = mysqli_query($dbi, "select * from dtransaksipermintaan where NoKantong='$nkt'");
                    $s_dist = mysqli_fetch_assoc($s_dist);
                    $data1 = mysqli_fetch_assoc(mysqli_query($dbi, "select * from htranspermintaan where noform='$s_dist[NoForm]'"));
                    if ($data1['jenis_permintaan'] == '0') {
                        $jenis_permintaan = 'Biasa';
                    } else {
                        $jenis_permintaan = $data1['jenis_permintaan'];
                    }
                    $s_pasien = "SELECT `no_rm`, `nama`, `alamat`, `gol_darah`, `rhesus`, `kelamin`, `keluarga`, `tgl_lahir`, `tlppasien`, `umur`, `insert_on` FROM `pasien` where `no_rm` ='$s_dist[no_rm]'";

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


                    if ($s_dist['Status'] == '0')
                        $status_bawa = 'Dibawa';
                    if ($s_dist['Status'] == '1')
                        $status_bawa = 'Dititip';
                    if ($s_dist['Status'] == 'B')
                        $status_bawa = 'Batal';

                    $q_dtransaksipermintaan = mysqli_query($dbi, "select * from dtransaksipermintaan  where `NoKantong`='$nkt'");
                    $cross = mysqli_fetch_assoc($q_dtransaksipermintaan);
                    $hasil_cross = 'Compatible dapat dikeluarkan';
                    if ($cross['StatusCross'] == '0')
                        $hasil_cross = 'Incompatible dapat dikeluarkan';
                    if ($cross['StatusCross'] == '2')
                        $hasil_cross = 'Incompatible Tidak dapat dikeluarkan';
                    $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$cross[petugas]'"));
                    $ptgs_cross = $usr['nama_lengkap'];
                    $usr = mysqli_fetch_assoc(mysqli_query($dbi, "select `nama_lengkap` from `user` where `id_user`='$cross[cheker]'"));
                    $ptgs_cek = $usr['nama_lengkap'];
                    ?>
                    <table width="100%" cellpadding="2" cellspacing="2">
                        <tr>
                            <td width="50%" valign="top">
                                <table width="100%" cellpadding="2" cellspacing="2">

                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Rumah Sakit</td>
                                        <td><?php echo $nmrs['NamaRs'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Bagian di RS</td>
                                        <td><?php echo $s_dist['bagian'] ?>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">No. Reg</td>
                                        <td><?php echo $data1['regrs'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Kode Pasien</td>
                                        <td><?php echo $pasien['no_rm'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Nama Pasien</td>
                                        <td><?php echo $pasien['nama'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Gol Darah Pasien</td>
                                        <td><?php echo $pasien['gol_darah'] ?>(<?php echo $pasien['rhesus'] ?>)
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Jenis Kelamin</td>
                                        <td><?php echo $kelamin ?>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Umur</td>
                                        <td><?php echo $data1['umur'] ?> Tahun
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Jenis Layanan</td>
                                        <td><?php echo $layanan['nama'] ?>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Diagnosa</td>
                                        <td><?php echo $data1['diagnosa'] ?>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Hemoglobin</td>
                                        <td><?php echo $data1['hb'] ?>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Alasan transfusi</td>
                                        <td><?php echo $data1['alasan'] ?>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Jenis Permintaan</td>
                                        <td><?php echo $jenis_permintaan ?>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Petugas Penerima Formulir</td>
                                        <td><?php echo $ptgs_terima_form ?>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" valign="top">
                                <table width="100%" cellpadding="2" cellspacing="2">
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Nomor Formulir</td>
                                        <td><?php echo $s_dist['NoForm'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Tanggal Permintaan</td>
                                        <td><?php echo $data1['tgl_register'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Tanggal Diperlukan</td>
                                        <td><?php echo $data1['tglminta'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Tanggal Uji Silang Serasi</td>
                                        <td><?php echo $cross['tgl'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Metode </td>
                                        <td><?php echo $cross['MetodeCross'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Aglutinasi</td>
                                        <td><?php echo $cross['aglutinasi'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Hasil</td>
                                        <td><?php echo $cross['stat2'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Keterangan</td>
                                        <td><?php echo $cross['ket'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Status pengeluaran</td>
                                        <td><?php echo $hasil_cross ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Status darah keluar</td>
                                        <td><?php echo $status_bawa ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Petugas Uji Silang Serasi</td>
                                        <td><?php echo $ptgs_cross ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Petugas Check</td>
                                        <td><?php echo $ptgs_cek ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Petugas Check</td>
                                        <td><?php echo $cross['mengesahkan'] ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <?php
                }
            }

            if ($stokkantong['Status'] > '4') {
                ?><br>
                <div>
                    <font size="4" color=00008B>DATA PEMUSNAHAN</font>
                </div>
                <?php
                $musnah = "SELECT * FROM `ar_stokkantong` where `noKantong`='$nkt'";
                $dtabuang = mysqli_fetch_assoc(mysqli_query($dbi, $musnah));
                switch ($dtabuang['alasan_buang']) {
                    case "0":
                        $alsn = "Gagal Aftap";
                        break;
                    case "1":
                        $alsn = "Lisis";
                        break;
                    case "2":
                        $alsn = "Kadaluarsa";
                        break;
                    case "3":
                        $alsn = "Plebotomi Terapi";
                        break;
                    case "4":
                        $alsn = "Reaktif Buang";
                        break;
                    case "5":
                        $alsn = "Lifemik";
                        break;
                    case "6":
                        $alsn = "Greyzone";
                        break;
                    case "7":
                        $alsn = "DCT Positif";
                        break;
                    case "8":
                        $alsn = "Kantong Bocor";
                        break;
                    case "9":
                        $alsn = "Satelit Rusak";
                        break;
                    case "10":
                        $alsn = "Bekas Pembuatan WE";
                        break;
                    case "11":
                        $alsn = "Reaktif Dirujuk Ke UTDP";
                        break;
                    case "12":
                        $alsn = "Hematokrit Tinggi";
                        break;
                    case "13":
                        $alsn = "Plasma Sisa PRC";
                        break;
                    case "14":
                        $alsn = "Leukosit Tinggi";
                        break;
                    case "15":
                        $alsn = "Produk Rusak";
                        break;
                    case "16":
                        $alsn = "Produk Sample QC";
                        break;
                    default:
                        $alsn = "";
                        break;
                }
                ?>
                <table width="100%" cellpadding="2" cellspacing="2">
                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                        <td style="background-color: mistyrose">Tanggal Dimusnahkan</td>
                        <td><?php echo $dtabuang['tgl_buang'] ?></td>
                    </tr>
                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                        <td style="background-color: mistyrose">Alasan Dimusnahkan</td>
                        <td><?php echo $alsn ?></td>
                    </tr>
                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                        <td style="background-color: mistyrose">Petugas Pemusnahan</td>
                        <td><?php echo $dtabuang['user'] ?></td>
                    </tr>
                </table>
                <?php
            }

            $ptg_barcode0 = "SELECT `l`.`time_aksi`,`l`.`user`, `u`.`nama_lengkap` FROM `user_log` l inner join `user` u on `u`.`id_user`=`l`.`user` WHERE `aksi_user` like '%barcode%$no_kantonga%'";

            $ptg_barcode = mysqli_fetch_assoc(mysqli_query($dbi, $ptg_barcode0));

            $ptg_mutasi0 = "SELECT `l`.`time_aksi`,`l`.`user`, `u`.`nama_lengkap` FROM `user_log` l inner join `user` u on `u`.`id_user`=`l`.`user` WHERE `aksi_user` like '%Pengesahan Kantong Logistik%$no_kantonga%'";

            $ptg_mutasi = mysqli_fetch_assoc(mysqli_query($dbi, $ptg_mutasi0));
            ?>
            <br><br>
            <div>
                <font size="4" color=00008B>DATA BAG LOGISTIK</font>
            </div>
            <table width="100%" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="50%">
                        <table width="100%" cellpadding="2" cellspacing="2">
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Jenis Kantong</td>
                                <td><?php echo $jeniskantong ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Merk Kantong</td>
                                <td><?php echo $stokkantong['merk'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Volume Kantong</td>
                                <td><?php echo $volumeasal ?> ml</td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Posisi Kantong</td>
                                <td><?php echo $posisikantong ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Nomor Lot</td>
                                <td><?php echo $stokkantong['nolot_ktg'] ?>
                            </tr>
                        </table>
                    </td>
                    <td width="50%">
                        <table width="100%" cellpadding="2" cellspacing="2">
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Input/Barcode </td>
                                <td><?php echo $stokkantong['tglTerima'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Pengesahan</td>
                                <td><?php echo $stokkantong['tglmutasi'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl ED Kantong</td>
                                <td><?php echo $stokkantong['kadaluwarsa_ktg'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas Barcode</td>
                                <td><?php echo '(' . $ptg_barcode['user'] . ') - ' . $ptg_barcode['nama_lengkap'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas Pengesahan</td>
                                <td><?php echo '(' . $ptg_mutasi['user'] . ') - ' . $ptg_mutasi['nama_lengkap'] ?></td>
                            </tr>
                            <!--tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';"><td style="background-color: mistyrose">Petugas Pengesahan</td>     <td>(koko) - Wiratmoko</td></tr-->
                        </table>
                    </td>
                </tr>
            </table>
            <br>
            <div>
                <font size="4" color=00008B>DATA PENGAMBILAN</font>
            </div>
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
            <table width="100%" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="50%">
                        <table width="100%" cellpadding="3.5" cellspacing="2">
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">No. Transaksi</td>
                                <td><?php echo $aftap['NoTrans'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tempat Pengambilan</td>
                                <td><?php echo $asaldonor . ' ' . $aftap['Instansi'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl & Waktu Registrasi</td>
                                <td><?php echo $aftap['Tgl'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tensi</td>
                                <td><?php echo $aftap['tensi'] ?> mmHg</td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Nadi</td>
                                <td><?php echo $aftap['nadi'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Suhu</td>
                                <td><?php echo $aftap['suhu'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Berat Badan</td>
                                <td><?php echo $aftap['beratBadan'] ?> kg</td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Hemoglobin</td>
                                <td><?php echo $jumlah_hb ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Golongan Darah</td>
                                <td><?php echo $aftap['gol_darah'] . ' Rh ' . $aftap['rhesus'] ?></td>
                            </tr>

                        </table>
                    </td>
                    <td width="50%">
                        <table width="100%" cellpadding="2" cellspacing="2">
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Jenis Pengambilan</td>
                                <td><?php echo $caraambil ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl & Waktu Pengambilan</td>
                                <td><?php echo $stokkantong['tgl_Aftap'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Volume Pengambilan</td>
                                <td><?php echo $aftap['volumekantong'] ?> ml</td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Lama Pengambilan</td>
                                <td><?php echo $lamaaftap ?> menit</td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Status Pengambilan</td>
                                <td><?php echo $status_aftap ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas Tensi</td>
                                <td><?php echo $ptgtensi . ' - ' . $qptensi['nama_lengkap'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas Anamnesa</td>
                                <td><?php echo $qpdokter['Nama'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas HB</td>
                                <td><?php echo $ptghb . ' - ' . $qphb['nama_lengkap'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas Aftap</td>
                                <td><?php echo $ptgaftap . ' - ' . $qpaftap['nama_lengkap'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas Input data</td>
                                <td><?php echo $ptgadmin . ' - ' . $qpinput['nama_lengkap'] ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <br>
            <div>
                <font size="4" color=00008B>DATA PENDONOR</font>
            </div>
            <?php
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
                <table width="100%" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="50%" valign="top">
                            <table width="100%" cellpadding="2" cellspacing="2">
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Kode Pendonor</td>
                                    <td><?php echo $aftap['KodePendonor'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Jenis Kelamin</td>
                                    <td><?php echo $jeniskelamin ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Jenis Donor</td>
                                    <td><?php echo $jenisdonor ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Status Donor</td>
                                    <td><?php echo $statusdonor ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Umur Donor</td>
                                    <td><?php echo $aftap['umur'] ?> tahun</td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Donor ke</td>
                                    <td><?php echo $aftap['donorke'] ?> kali</td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Pekerjaan</td>
                                    <td><?php echo $aftap['pekerjaan'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Gol Darah</td>
                                    <td><?php echo $pendonor['GolDarah'] . ' Rh ' . $pendonor['Rhesus'] ?></td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" valign="top">
                            <table width="100%" cellpadding="1" cellspacing="2">
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Nama Pendonor</td>
                                    <td><?php echo $pendonor['Nama'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Nomor Identitas</td>
                                    <td><?php echo $pendonor['NoKTP'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Alamat</td>
                                    <td><?php echo $pendonor['Alamat'] . ' ' . $pendonor['kelurahan'] . ' ' . $pendonor['kecamatan'] . ' ' . $pendonor['KodePos'] ?>
                                    </td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Wilayah</td>
                                    <td><?php echo $pendonor['Wilayah'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Tempat Lahir</td>
                                    <td><?php echo $pendonor['TempatLhr'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Tgl Lahir</td>
                                    <td><?php echo $pendonor['TglLhr'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Donasi</td>
                                    <td><?php echo $pendonor['jumDonor'] ?> Kali</td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Nomor Telp</td>
                                    <td><?php echo $pendonor['telp'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Nomor HP</td>
                                    <td><?php echo $pendonor['telp2'] ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <?php
            } else {
                ?>
                <table width="100%" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="50%" valign="top">
                            <table width="100%" cellpadding="2" cellspacing="2">
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Kode Pendonor</td>
                                    <td><?php echo $aftap['KodePendonor'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Jenis Kelamin</td>
                                    <td><?php echo $jeniskelamin ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Jenis Donor</td>
                                    <td><?php echo $jenisdonor ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Status Donor</td>
                                    <td><?php echo $statusdonor ?></td>
                                </tr>

                            </table>
                        </td>
                        <td width="50%" valign="top">
                            <table width="100%" cellpadding="1" cellspacing="2">
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Umur Donor</td>
                                    <td><?php echo $aftap['umur'] ?> tahun</td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Donor ke</td>
                                    <td><?php echo $aftap['donorke'] ?> kali</td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Pekerjaan</td>
                                    <td><?php echo $aftap['pekerjaan'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Gol Darah</td>
                                    <td><?php echo $pendonor['GolDarah'] . ' Rh ' . $pendonor['Rhesus'] ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <?php
            }
            $s_sr = "SELECT `hst_id`, `hst_notrans`, `hst_bagpengirim`, `hst_bagpenerima`, `hst_tgl`, `hst_asal`, `hst_jenis_st`, `hst_user`, `hst_pengirim`, `hst_penerima`, `hst_penerima2`, `hst_kode_alat`, `hst_suhuterima`, `hst_kondisiumum`, `hst_peruntukan`, `hst_modul`, `hst_shift_pengirim`, `hst_shift_penerima` ,
                CASE
                    WHEN (`dst_statusktg`='1' and `dst_sahktg`='0') THEN 'Aftap'
                    WHEN (`dst_statusktg`='1' and `dst_sahktg`='1') THEN 'Karantina'
                    WHEN (`dst_statusktg`='2') THEN 'Sehat'
                    WHEN (`dst_statusktg`='3') THEN 'Keluar'
                    WHEN (`dst_statusktg`='4') THEN 'Reaktif-Rusak'
                    WHEN (`dst_statusktg`='5') THEN 'Rusak-gagal'
                    WHEN (`dst_statusktg`='6') THEN 'Rusak-Dimusnahkan'
                    ELSE 'Tidak ada' end as `dst_statusktg`,
                CASE WHEN `dst_sample`='1' THEN 'Sesuai' ELSE 'Tdk Sesuai' END AS `dst_sample`,
                CASE WHEN `dst_sah`='1' THEN 'Sesuai' ELSE 'Tdk Sesuai' END AS `dst_sah`,
                `dst_nokantong`
                FROM `serahterima` inner join `serahterima_detail` on `serahterima_detail`.`dst_notrans`=`serahterima`.`hst_notrans` where `dst_nokantong`='$no_kantonga'";
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
            <table width="100%" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="50%">
                        <table width="100%" cellpadding="2" cellspacing="2">
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Tgl Serah Terima</td>
                                <td><?php echo $sr['hst_tgl'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">No Transaksi</td>
                                <td><?php echo $sr['hst_notrans'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Bagian Pengiriman</td>
                                <td><?php echo $sr['hst_bagpengirim'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Bagiann Penerima</td>
                                <td><?php echo $sr['hst_bagpenerima'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Asal Darah/Sample</td>
                                <td><?php echo $sr['hst_asal'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Kode Alat Pengiriman</td>
                                <td><?php echo $sr['hst_kode_alat'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Suhu saat diserahkan</td>
                                <td><?php echo $sr['hst_suhuterima'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Keadaan Umum</td>
                                <td><?php echo $sr['hst_kondisiumum'] ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%">
                        <table width="100%" cellpadding="4" cellspacing="2">
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Status Darah saat diterima</td>
                                <td><?php echo $sr['dst_statusktg'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Kesesuaian Kantong Darah</td>
                                <td><?php echo $sr['dst_sah'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Kesesuaian Sampel</td>
                                <td><?php echo $sr['dst_sample'] ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas Input Data</td>
                                <td><?php echo $sr['hst_user'] . ' - ' . $pencatat ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas Pengambilan</td>
                                <td><?php echo $sr['hst_pengirim'] . ' - ' . $pengirim ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas penerima darah</td>
                                <td><?php echo $sr['hst_penerima'] . ' - ' . $penerima ?></td>
                            </tr>
                            <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                <td style="background-color: mistyrose">Petugas penerima sampel</td>
                                <td><?php echo $sr['hst_penerima2'] . ' - ' . $penerima2 ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </table-->
                <br>
                <div>
                    <font size="4" color=00008B>DATA UJI SARING IMLTD</font>
                </div>
                <font size="2" color=black><b>Pemeriksaan metode ELISA/CLHIA</font></b>
                <?php
                //DATA PEMERIKSAAN IMLTDELISA ==========================================================================================
                $sq_elisa = mysqli_query($dbi, "SELECT `id`, `noKantong`, `OD`, `COV`, `notrans`,
                            case
                            when `jenisPeriksa`='0' then 'HBsAg'
                            when `jenisPeriksa`='1' then 'Anti HCV'
                            when `jenisPeriksa`='2' then 'Anti HIV'
                            when `jenisPeriksa`='3' then 'Syphilis' End As Parameter,
                            case
                            when `Hasil`='0' then 'Non Reaktif'
                            when `Hasil`='1' then 'Reaktif'
                            when `Hasil`='2' then 'Grayzone'  End As Hasil,
                            `tglPeriksa`, `dicatatOleh`, `dicekOleh`, `DisahkanOleh`, `noLot`, `Metode`, `ulang`, `up_data`, `insert_on`
                            FROM `hasilelisa` WHERE `noKantong`='$no_kantonga' order by `id`");
                ?>
                <table cellpadding=3 cellspacing=3 width="100%">
                    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                        <th rowspan="2">ID</th>
                        <th rowspan="2">Kantong<br> Utama</th>
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
                    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                        <th>Nama</th>
                        <th>Lot</th>
                        <th>ED</th>
                    </tr>
                    <?php
                    $no = "0";
                    while ($imltd = mysqli_fetch_assoc($sq_elisa)) {
                        $no++;
                        if (($imltd['Hasil'] == "Reaktif") or ($imltd['Hasil'] == "Grayzone")) {
                            $var_imltd = '1';
                        }
                        $sq_reagen = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `Nama`, `noLot`, `tglKad`  FROM `reagen` WHERE kode='$imltd[noLot]'"));
                        if ($sq_reagen['noLot'] == "") {
                            $sq_reagen = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `Nama`, `noLot`, `tglKad`  FROM `reagen` WHERE noLot='$imltd[noLot]'"));
                        }
                        ?>
                        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';"
                            onMouseOut="this.className='normal';">
                            <td class=input><?php echo $imltd['id'] ?></td>
                            <td class=input><?php echo $imltd['noKantong'] ?></td>
                            <td class=input><?php echo $imltd['notrans'] ?></td>
                            <td class=input><?php echo $imltd['tglPeriksa'] ?></td>
                            <td class=input><?php echo $imltd['Parameter'] ?></td>
                            <td class=input><?php echo $imltd['OD'] ?></td>
                            <?php if ($imltd['Hasil'] == "Non Reaktif") { ?><td align="left">
                                    <font color="black"><?php echo $imltd['Hasil'] ?></font>
                                </td><?php } else { ?>
                                <td align="left">
                                    <font color="red"><?php echo $imltd['Hasil'] ?></font>
                                </td><?php } ?>
                            <td class=input><?php echo $sq_reagen['Nama'] ?></td>
                            <td class=input><?php echo $sq_reagen['noLot'] ?></td>
                            <td class=input><?php echo $sq_reagen['tglKad'] ?></td>
                            <td class=input><?php echo $imltd['dicatatOleh'] ?></td>
                            <td class=input><?php echo $imltd['dicekOleh'] ?></td>
                            <td class=input><?php echo $imltd['DisahkanOleh'] ?></td>
                        </tr>
                    <?php }
                    if ($no == "0") {
                        ?>
                        <tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td colspan="13" class=input align="center">TIDAK ADA DATA PEMERIKSAAN IMLTD METODE ELISA</td>
                        </tr>
                    <?php }
                    ?>
                </table>
                <?php
                //DATA PEMERIKSAAN IMLTD RAPID================================================================================================
                $sq_rapid = mysqli_query($dbi, "SELECT `id`, `NoTrans`, `noKantong`, `Kontrol`,
						case
							when `jenisperiksa`='0' then 'HBsAg'
                            when `jenisperiksa`='1' then 'Anti HCV'
                            when `jenisperiksa`='2' then 'Anti HIV'
                            when `jenisperiksa`='3' then 'Syphilis' End As Parameter,
						case
                            when `Hasil`='1' then 'Non Reaktif'
                            when `Hasil`='0' then 'Reaktif' End As Hasil,`nolot`, `tgl_tes`, `dicatatoleh`, `dicekOleh`, `DisahkanOleh`, `Metode`, `ulang`, `up_data` 
                            FROM `drapidtest` WHERE `noKantong`='$nkt' order by `id`");
                ?>
                <font size="2" color=black><b>Pemeriksaan metode RAPID</font></b>
                <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
                    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
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
                    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                        <th>Nama</th>
                        <th>Lot</th>
                        <th>ED</th>
                    </tr>
                    <?php
                    $no = "0";
                    while ($imltd_r = mysqli_fetch_assoc($sq_rapid)) {
                        $no++;
                        if ($imltd_r['Hasil'] == "Reaktif") {
                            $var_imltd = '1';
                        }
                        $sq_reagen = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `Nama`, `noLot`, `tglKad`  FROM `reagen` WHERE kode='$imltd_r[nolot]'"));
                        ?>
                        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';"
                            onMouseOut="this.className='normal';">
                            <td class=input><?php echo $imltd_r['id'] ?></td>
                            <td class=input><?php echo $imltd_r['noKantong'] ?></td>
                            <td class=input><?php echo $imltd_r['NoTrans'] ?></td>
                            <td class=input><?php echo $imltd_r['tgl_tes'] ?></td>
                            <td class=input><?php echo $imltd_r['Parameter'] ?></td>
                            <td class=input><?php echo $imltd_r['Kontrol'] ?></td>
                            <?php if ($imltd_r['Hasil'] == "Non Reaktif") { ?><td align="left">
                                    <font color="black"><?php echo $imltd_r['Hasil'] ?></font>
                                </td><?php } else { ?>
                                <td align="left">
                                    <font color="red"><?php echo $imltd_r['Hasil'] ?></font>
                                </td><?php } ?>
                            <td class=input><?php echo $sq_reagen['Nama'] ?></td>
                            <td class=input><?php echo $sq_reagen['noLot'] ?></td>
                            <td class=input><?php echo $sq_reagen['tglKad'] ?></td>
                            <td class=input><?php echo $imltd_r['dicatatoleh'] ?></td>
                            <td class=input><?php echo $imltd_r['dicekOleh'] ?></td>
                            <td class=input><?php echo $imltd_r['DisahkanOleh'] ?></td>
                        </tr>
                    <?php }
                    if ($no == "0") {
                        ?>
                        <tr style="color:#000000; " onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td colspan="13" class=input align="center">TIDAK ADA DATA PEMERIKSAAN IMLTD METODE RAPID</td>
                        </tr>
                    <?php } ?>
                </table>
                <?php
                //DATA PEMERIKSAAN NAT===========================================================================================
        
                $sq_nat = mysqli_query($dbi, "SELECT *,
                                                        case 
                                                        when `Hasil`='0' then 'Non Reaktif' 
                                                        when `Hasil`='1' then 'Reaktif'
                                                        when `Hasil`='2' then 'Grayzone'  End As Hasil
                                                        FROM `hasilnat` WHERE `noKantong` = '$no_kantonga' order by `natid`");

                ?>
                <br>
                <div>
                    <font size="4" color=00008B>DATA PEMERIKSAAN NAT</font>
                </div>
                <table cellpadding=3 cellspacing=3 width="100%">
                    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                        <th rowspan="2">ID</th>
                        <th rowspan="2">Kantong<br> Utama</th>

                        <th rowspan="2">Tanggal</th>

                        <th rowspan="2">OD</th>
                        <th rowspan="2">Hasil</th>
                        <th colspan="3">Reagen</th>
                        <th rowspan="2">Pencatat</th>
                        <th rowspan="2">Di Cek</th>
                        <th rowspan="2">Disahkan</th>
                    </tr>
                    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                        <th>Nama</th>
                        <th>Lot</th>
                        <th>ED</th>
                    </tr>
                    <?php
                    $no = "0";
                    while ($imltdn = mysqli_fetch_assoc($sq_nat)) {
                        $no++;
                        if (($imltdn['Hasil'] == "Reaktif") or ($imltdn['Hasil'] == "Grayzone")) {
                            $var_imltd = '1';
                        }
                        $sq_reagen = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `Nama`, `noLot`, `tglKad`  FROM `reagen` WHERE kode='$imltd[noLot]'"));

                        ?>
                        <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';"
                            onMouseOut="this.className='normal';">
                            <td class=input><?php echo $imltdn['natid'] ?></td>
                            <td class=input><?php echo $imltdn['noKantong'] ?></td>

                            <td class=input><?php echo $imltdn['tglPeriksa'] ?></td>

                            <td class=input><?php echo $imltdn['OD'] ?></td>
                            <?php if ($imltdn['Hasil'] == "Non Reaktif") { ?><td align="left">
                                    <font color="black"><?php echo $imltdn['Hasil'] ?></font>
                                </td><?php } else { ?>
                                <td align="left">
                                    <font color="red"><?php echo $imltdn['Hasil'] ?></font>
                                </td><?php } ?>
                            <td class=input>Ultrio</td>
                            <td class=input><?php echo $imltdn['noLot'] ?></td>
                            <td class=input><?php echo $imltdn['ed'] ?></td>
                            <td class=input><?php echo $imltdn['dicatatOleh'] ?></td>
                            <td class=input><?php echo $imltdn['dicatatOleh'] ?></td>
                            <td class=input><?php echo $imltdn['DisahkanOleh'] ?></td>
                        </tr>
                    <?php }
                    if ($no == "0") {
                        ?>
                        <tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td colspan="13" class=input align="center">TIDAK ADA DATA PEMERIKSAAN NAT</td>
                        </tr>
                    <?php }
                    if ($no !== '0') {
                        $var_jenis_imltd = '0';
                    }
                    ?>
                </table>

                <br>
                <div>
                    <font size="4" color=00008B>DATA KONFIRMASI GOLONGAN DARAH</font>
                </div>
                <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
                    <tr style="background-color:mistyrose; font-size:10px; color:#000000;">
                        <td rowspan='3'>No</td>
                        <td rowspan='3'>Tanggal</td>
                        <td rowspan='3'>No Konfirmasi</td>
                        <td rowspan='3'>Kantong Utama</td>
                        <td rowspan='3'>Gol(Rh) Darah Asal</td>
                        <td rowspan='3'>Gol(Rh) Darah Baru</td>
                        <td rowspan='3'>Hasil</td>
                        <td rowspan='3'>Metode</td>
                        <td colspan='3'>Anti A</td>
                        <td colspan='3'>Anti B</td>
                        <td colspan='3'>Anti D</td>
                        <td rowspan='3'>TS-A</td>
                        <td rowspan='3'>TS-B</td>
                        <td rowspan='3'>TS-O</td>
                        <td rowspan='3'>AC</td>
                        <td rowspan='3'>BA 6%</td>
                        <td rowspan='3'>Petugas</td>
                    </tr>
                    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                        <td rowspan='2'>Nilai</td>
                        <td rowspan='2'>Nolot</td>
                        <td rowspan='2'>Epx.</td>

                        <td rowspan='2'>Nilai</td>
                        <td rowspan='2'>Nolot</td>
                        <td rowspan='2'>Epx.</td>

                        <td rowspan='2'>Nilai</td>
                        <td rowspan='2'>Nolot</td>
                        <td rowspan='2'>Epx.</td>
                    </tr>
                    <tr style="background-color:#FFF8DC; font-size:12px; color:#FFFFFF; font-family:Verdana;"
                        onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">

                    </tr>
                    <?php
                    $a = mysqli_query($dbi, "select * from dkonfirmasi where  NoKantong='$no_kantonga' order by NoKonfirmasi ASC");
                    $no = 1;
                    while ($a_dtransaksipermintaan = mysqli_fetch_assoc($a)) {
                        if (($a_dtransaksipermintaan['Cocok'] == '1')) {
                            $var_kgd = '1';
                        }
                        ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td align="right"><?php echo $no++ ?>.</td>
                            <?php
                            $cocok1 = '-';
                            if ($a_dtransaksipermintaan['Cocok'] == '0')
                                $cocok1 = 'Cocok';
                            if ($a_dtransaksipermintaan['Cocok'] == '1')
                                $cocok1 = 'Tidak Cocok';
                            if ($a_dtransaksipermintaan['sel'] == '0')
                                $sel = 'Ya';
                            if ($a_dtransaksipermintaan['sel'] == '1')
                                $sel = 'Tidak';
                            if ($a_dtransaksipermintaan['serum'] == '0')
                                $serum = 'Ya';
                            if ($a_dtransaksipermintaan['serum'] == '1')
                                $serum = 'Tidak';
                            if ($a_dtransaksipermintaan['ac'] == '0')
                                $ac = 'Pos';
                            if ($a_dtransaksipermintaan['ac'] == '1')
                                $ac = 'Neg';
                            if ($a_dtransaksipermintaan['ba'] == '0')
                                $ba = 'Pos';
                            if ($a_dtransaksipermintaan['ba'] == '1')
                                $ba = 'Neg';

                            $pengolahan = $a_dtransaksipermintaan['tgl'];
                            $tglkel0 = date("Y-m-d", strtotime($pengolahan));
                            ?>
                            <td><?php echo $tglkel0 ?></td>
                            <td><?php echo $a_dtransaksipermintaan['NoKonfirmasi'] ?></td>
                            <td><?php echo $a_dtransaksipermintaan['NoKantong'] ?></td>
                            <td align="center">
                                <?php echo $a_dtransaksipermintaan['goldarah_asal'] ?>(<?php echo $a_dtransaksipermintaan['rhesus_asal'] ?>)
                            </td>
                            <td align="center">
                                <?php echo $a_dtransaksipermintaan['GolDarah'] ?>(<?php echo $a_dtransaksipermintaan['Rhesus'] ?>)
                            </td>
                            <td align="center" nowrap><?php echo $cocok1 ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['metode'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['antiA'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['nolot_aa'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['expa'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['antiB'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['nolot_ab'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['expb'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['antiD'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['nolot_ad'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['expd'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['tA'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['tB'] ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['tsO'] ?></td>
                            <td class=input><?php echo $ac ?></td>
                            <td class=input><?php echo $ba ?></td>
                            <td class=input><?php echo $a_dtransaksipermintaan['petugas'] ?></td>
                        </tr>
                    <?php }
                    if ($no == "1") {
                        ?>
                        <tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                            <td colspan="23" class=input align="center">TIDAK ADA DATA PEMERERIKSAAN KONFIRMASI GOLONGAN DARAH</td>
                        </tr>
                    <?php } ?>
                </table>

                <br>
                <div>
                    <font size="4" color=00008B>DATA PENGOLAHAN DARAH</font>
                </div>
                <?php
                $a = mysqli_query($dbi, "SELECT  `id`, `noKantong`, `Produk`, `tgl`,
                        case when `cara`='0' then 'Manual' else 'Otomatis' end as cara,
                        case
                            when `pisah`='0' then 'Centrifuge 1'
                            when `pisah`='1' then 'Centrifuge 2'
                            when `pisah`='2' then 'Centrifuge 3'
                            when `pisah`='3' then 'Centrifuge 4'
                            when `pisah`='4' then 'Centrifuge 5'
                            when `pisah`='5' then 'Sedimentasi'
                         end as pisah, `petugas`, `nama_lengkap`,`lengkap`
                        FROM `dpengolahan` INNER JOIN `user` on `user`.`id_user`=`dpengolahan`.`petugas`
                        inner join `produk` on `produk`.`Nama`=`dpengolahan`.`Produk`
                        where `noKantong`='$nkt' order by tgl DESC");

                $komponen = mysqli_fetch_assoc($a);
                $t = mysqli_fetch_assoc(mysqli_query($dbi, "select * from hpengolahan where nokantong='$nkt'"));
                $dt = mysqli_fetch_assoc(mysqli_query($dbi, "select * from dpengolahan where noKantong='$nkt'"));
                ?>
                <table width="100%" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <table width="100%" cellpadding="3" cellspacing="2">
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Nomor Transaksi</td>
                                    <td><?php echo $dt['noTrans'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Tanggal Pengolahan</td>
                                    <td><?php echo $komponen['tgl'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Metode Pengolahan</td>
                                    <td><?php echo $komponen['cara'] ?></td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%">
                            <table width="100%" cellpadding="3" cellspacing="2">
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Centrifuge</td>
                                    <td><?php echo $komponen['pisah'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Nama Produk</td>
                                    <td><?php echo $komponen['Produk'] . ' - ' . $komponen['lengkap'] ?></td>
                                </tr>
                                <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                    <td style="background-color: mistyrose">Petugas Pengolahan</td>
                                    <td><?php echo $komponen['petugas'] . ' - ' . $komponen['nama_lengkap'] ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>
                <div>
                    <font size="4" color=00008B>DATA RELEASE</font>
                </div>
                <?php
                $rel = "select * FROM `release` where `rnokantong`='$nkt'";

                $tmp = mysqli_fetch_assoc(mysqli_query($dbi, $rel));
                $t = "select * from `timbang_darah` where nokantong='$nkt' order by id desc";
                $t = mysqli_fetch_assoc(mysqli_query($dbi, $t));
                $ptg_timbang = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$t[user]'"));
                $ptg_timbang = $ptg_timbang['nama_lengkap'];
                $ptg_prolis = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$tmp[ruser]'"));
                $ptg_prolis = $ptg_prolis['nama_lengkap'];
                $ptg_chek = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$tmp[rchecker]'"));
                $ptg_chek = $ptg_chek['nama_lengkap'];
                $ptg_sah = mysqli_fetch_assoc(mysqli_query($dbi, "select nama_lengkap from `user` where `id_user`='$tmp[rpengesah]'"));
                $ptg_sah = $ptg_sah['nama_lengkap'];
                if (strlen($tmp['rnotrans']) == 0) {
                    ?><br>
                    <div>
                        <font size="4" color=red><b>Darah belum di RELEASE</font></b>
                    </div>
                    <?php
                } else {
                    ?>
                    <table width="100%" cellpadding="2" cellspacing="2">
                        <tr>
                            <td width="50%" valign="top">
                                <table width="100%" cellpadding="3" cellspacing="2">
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Nomor Transaksi</td>
                                        <td><?php echo $tmp['rnotrans'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">Tgl Release</td>
                                        <td><?php echo $tmp['rtgl'] ?></td>
                                    </tr>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td colspan="2" style="background-color: mistyrose">SPESIFIKASI KANTONG</td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Label & Identitas sesuai spesifikasi</td>
                                        <?php if ($tmp['rspek_kantong'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Kode Unik/Barcode sesuai spesifikasi</td>
                                        <?php if ($tmp['rkode_unik'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td colspan="2" style="background-color: mistyrose">SELEKSI & PENGAMBILAN</td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Seleksi donor memenuhi kriteria</td>
                                        <?php if ($tmp['rspek_seleksi'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Waktu Pengambilan terpenuhi</td>
                                        <?php if ($tmp['rspek_aftap'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td colspan="2" style="background-color: mistyrose">PEMERIKSAAN VISUAL</td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Tidak ada kebocoran</td>
                                        <?php if ($tmp['rkebocoran'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Selang kantong sesuai spesifikasi</td>
                                        <?php if ($tmp['rselang'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Tidak Hemolysis</td>
                                        <?php if ($tmp['rhemolysis'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Tidak Lipemik</td> <?php if ($tmp['rlipemik'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Tidak Ikterik</td> <?php if ($tmp['rikterik'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Plasma tidak kehijauan</td>
                                        <?php if ($tmp['rkehijauan'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Tidak ada bekuan pada Sel Darah Merah</td>
                                        <?php if ($tmp['rbekuan'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>

                                </table>
                            </td>
                            <td width="50%" valign="top">
                                <table width="100%" cellpadding="3" cellspacing="2">
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td colspan="2" style="background-color: mistyrose">PEMERIKSAAN DAN PENGOLAHAN</td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Waktu Selesai Pengolahan terpenuhi</td>
                                        <?php if ($tmp['rspek_pengolahan'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Volume sesuai dengan spesifikasi</td>
                                        <?php if ($tmp['rspek_volume'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Hasil Pemeriksaan memenuhi spesifikasi</td>
                                        <?php if ($tmp['rspek_imltd'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Pemeriksaan donasi sebelumnya terpenuhi</td>
                                        <?php if ($tmp['rspek_imltd_old'] == '1') { ?>
                                            <td align="center">&radic;</td><?php } else { ?>
                                            <td align="center" bgcolor="red">
                                                <font color="white">X</font>
                                            </td><?php } ?>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td colspan="2" style="background-color: mistyrose">VOLUME</td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Berat Kantong (gram)</td>
                                        <td align="left" nowrap><?php echo number_format($tmp['rberat_timbang'], 2) ?> gr</td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Tanggal Penimbangan</td>
                                        <td align="left" nowrap><?php echo $t['waktu'] ?></td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Volume produk darah</td>
                                        <td align="left" nowrap><?php echo number_format(round($tmp['rvolume'], 2)) ?> ml</td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Petugas penimbangan</td>
                                        <td align="left" nowrap><?php echo $ptg_timbang ?></td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: mistyrose">HASIL RELEASE</td>
                                        <td align="left" nowrap><?php echo $tmp['rsatus_ket'] ?></td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Catatan</td>
                                        <td align="left"><?php echo $tmp['rnote'] ?></td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Petugas Release</td>
                                        <td align="left"><?php echo $ptg_prolis ?></td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Dicek oleh</td>
                                        <td align="left"><?php echo $ptg_chek ?></td>
                                    <tr onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                                        <td style="background-color: white">Disahkan oleh</td>
                                        <td align="left"><?php echo $ptg_sah ?></td>
                                </table>
                            </td>
                        </tr>
                    </table>
                <?php } ?>

                <br>
                <div>
                    <font size="4" color=00008B>REKAM JEJAK DATA KANTONG (AUDIT TRAIL)</font>
                </div>
                <table cellpadding=5 cellspacing=5 width="100%">
                    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                        <th align="center">Tanggal</th>
                        <th align="center">Jam</th>
                        <th align="center">Modul</th>
                        <th align="center">Proses</th>
                        <th align="center">Personil</th>
                    </tr>
                    <?php
                    //barcode
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
		      	 user_log.user, user_log.komputer,user_log.time_aksi,
		      	 case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
		      	 user_log.modul, user_log.aksi_user, `user`.nama_lengkap
		         from user_log left join user on `user`.`id_user`=user_log.user
		         where user_log.aksi_user like '%$nkt%' and user_log.aksi_user like '%barcode%' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) {
                        $waktu_bukakantong = $komp['time_aksi']; ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <td><?php echo $komp['tempat'] . $komp['aksi_user'] ?></td>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php } 
                    //mutasi kantong ke aftap
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
		      	 user_log.user, user_log.komputer,
		      	 case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
		      	 user_log.modul, user_log.aksi_user, `user`.nama_lengkap
		         from user_log left join user on `user`.`id_user`=user_log.user
		         where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%Pengesahan Kantong Logistik%' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) { ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <?php
                            if ($kantongke == 'A') {
                                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
                            } else {
                                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
                            }
                            ?>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php } 
                    //Pengambilan Darah
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
		      	 user_log.user, user_log.komputer,user_log.time_aksi,
		      	 case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
		      	 user_log.modul, user_log.aksi_user, `user`.nama_lengkap
		         from user_log left join user on `user`.`id_user`=user_log.user
		         where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%Pengambilan%' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) {
                        $waktu_aftap = $komp['time_aksi']; ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <?php
                            if ($kantongke == 'A') {
                                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
                            } else {
                                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
                            }
                            ?>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php } 
                    //Pengesahan ke karantina
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
             user_log.user, user_log.komputer,
             case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
             user_log.modul, user_log.aksi_user, `user`.nama_lengkap
             from user_log left join user on `user`.`id_user`=user_log.user
             where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%Serah terima (Pengesahan)%' order by time_aksi ASC";

                    $a = mysqli_query($dbi, $a1);

                    while ($komp = mysqli_fetch_assoc($a)) { ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <?php
                            if ($kantongke == 'A') {
                                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
                            } else {
                                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
                            }
                            ?>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php } 
                    //KGD
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
             user_log.user, user_log.komputer,user_log.time_aksi,
             case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
             user_log.modul, user_log.aksi_user, `user`.nama_lengkap
             from user_log left join user on `user`.`id_user`=user_log.user
             where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%KGD%' and user_log.modul='KONFIRMASI' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) { ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <?php
                            if ($kantongke == 'A') {
                                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
                            } else {
                                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
                            }
                            ?>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php } 
                    //IMLTD
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
             user_log.user, user_log.komputer,
             case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
             user_log.modul, user_log.aksi_user, `user`.nama_lengkap
             from user_log left join user on `user`.`id_user`=user_log.user
             where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%IMLTD%' and user_log.modul='IMLTD' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) { ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <?php
                            if ($kantongke == 'A') {
                                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
                            } else {
                                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
                            }
                            ?>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php }
                    //Penngolahan
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
             user_log.user, user_log.komputer, user_log.time_aksi,
             case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
             user_log.modul, user_log.aksi_user, `user`.nama_lengkap
             from user_log left join user on `user`.`id_user`=user_log.user
             where user_log.aksi_user like '%$nkt%' and user_log.aksi_user like '%Pengolahan%' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) {
                        $waktu_komponen = $komp['time_aksi']; ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <td><?php echo $komp['tempat'] . $komp['aksi_user'] ?></td>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php }
                    //Release
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
        user_log.user, user_log.komputer, user_log.time_aksi,
        case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
        user_log.modul, user_log.aksi_user, `user`.nama_lengkap
        from user_log left join user on `user`.`id_user`=user_log.user
        where user_log.aksi_user like '%$nkt%' and user_log.aksi_user like '%Release%' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) {
                        $waktu_komponen = $komp['time_aksi']; ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <td><?php echo $komp['tempat'] . $komp['aksi_user'] ?></td>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php }
                    //cross
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
        user_log.user, user_log.komputer, user_log.time_aksi,
        case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
        user_log.modul, user_log.aksi_user, `user`.nama_lengkap
        from user_log left join user on `user`.`id_user`=user_log.user
        where user_log.aksi_user like '%$nkt%' and user_log.aksi_user like '%crossmatch%' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) {
                        $waktu_komponen = $komp['time_aksi']; ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <td><?php echo $komp['tempat'] . $komp['aksi_user'] ?></td>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php }
                    //distribusi ke
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
        user_log.user, user_log.komputer, user_log.time_aksi,
        case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
        user_log.modul, user_log.aksi_user, `user`.nama_lengkap
        from user_log left join user on `user`.`id_user`=user_log.user
        where user_log.aksi_user like '%$nkt%' and user_log.aksi_user like '%Kirim ke%' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) {
                        $waktu_komponen = $komp['time_aksi']; ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <td><?php echo $komp['tempat'] . $komp['aksi_user'] ?></td>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php }
                    //Pemusnahan
                    $a1 = "SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
        user_log.user, user_log.komputer, user_log.time_aksi,
        case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
        user_log.modul, user_log.aksi_user, `user`.nama_lengkap
        from user_log left join user on `user`.`id_user`=user_log.user
        where user_log.aksi_user like '%$nkt%' and user_log.aksi_user like '%musnah%' order by time_aksi ASC";
                    $a = mysqli_query($dbi, $a1);
                    while ($komp = mysqli_fetch_assoc($a)) {
                        $waktu_komponen = $komp['time_aksi']; ?>
                        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
                            onMouseOut="this.className='normal'">
                            <td><?php echo $komp['tgl_aksi'] ?></td>
                            <td><?php echo $komp['jam_aksi'] ?></td>
                            <td><?php echo $komp['modul'] ?></td>
                            <td><?php echo $komp['tempat'] . $komp['aksi_user'] ?></td>
                            <td><?php echo $komp['nama_lengkap'] ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <?php
        } else {
            echo "<SCRIPT>alert('Produk/Komponen darah yang anda masukkan tidak terdaftar/tidak ada dalam SIMDONDAR');</SCRIPT>";
        }
    } ?>
</body>

</html>
