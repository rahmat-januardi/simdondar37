<?

/***********************************************
 * Author 	: suwena 
 * Date 	: 26 Mei 2018
 * Fungsi	: Form Serah Terima Darah dari Aftap/Mobile unit utk Karantina
 * Keterangan Modul : 
 * 		Pengganti pengesahan kantong
 * 		Sekaligus membuat formulir Serah Terima ke 
 *			- Bag Karantina atau Komponen
 *			- Bag Uji Saring Darah IMLTD
 *			- Bag Uji Konfirmasi Golongan Darah
 * 		Status Darah yang sah langsung menjadi KARANTINA
 * 		Stok Position : PENYIMPANAN DARAH KARANTINA
 * Table terkait : 
 *		- Select : stokkantong join htransaksi
 *		- exec   : serahterima_h, serahterima_detail, serahterima_detail_tmp
 ***********************************************/
$nodokumen = "No. Dokumen: UDDP-PD-L4-011-2022";
include('config/db_connect.php');
$today            = date("Y-m-d H:i:s");
$namauser        = $_SESSION['namauser'];
$namauserlkp    = $_SESSION['nama_lengkap'];
$modul            = "KARANTINA";
$bag_pengirim    = "AFTAP";
$bag_penerima    = "KOMPONEN";
$notransaksi    = $_GET['no'];
$utd            = mysql_fetch_assoc(mysql_query("select upper(`nama`) as `nama` from `utd` where `aktif`='1'"));
$utd            = $utd['nama'];
//echo "$notransaksi";
?>

<title>SIMDONDAR</title>

<head>
    <style type="text/css" media="print">
        @page {
            size: landscape;
            margin-bottom: 20mm;
            margin-left: 0mm;
            margin-right: 0mm;
            margin-top: 15mm;

            header : {
                display: none !important;
            }
        }

        html {
            background-color: #ffffff;
            margin: 3px;
            /* this affects the margin on the html before sending to printer */
        }

        body {
            border: solid 0px #ffffff;
            margin: 0mm 15mm 10mm 10mm;
            /* margin you want for the content */
        }

        table th td {
            text-align: left;
        }
    </style>
</head>

<body onload="window.print()">

    <?php
    $sql_h = "SELECT `hst_id`, `hst_notrans`, `hst_bagpengirim`, `hst_bagpenerima`, `hst_tgl`, `hst_asal`, `hst_jenis_st`,
            `hst_user`, `hst_pengirim`, `hst_penerima`, `hst_penerima2`, `hst_penerima3`, `hst_kode_alat`, `hst_suhuterima`, `hst_kondisiumum`,
            `hst_peruntukan`, `hst_modul`, `hst_shift_pengirim`, `hst_shift_penerima` FROM `serahterima`
            WHERE `hst_notrans`='$notransaksi'";
    $sql_h1 = mysql_fetch_assoc(mysql_query($sql_h));
    ?>
    <?php
    /*if (($_SESSION[leveluser])=='aftap'){?>
    <img src="../images/solo/koppengesahan.png" width="100%"><br>
    <?php } else if (($_SESSION[leveluser])=='komponen'){?>
    <img src="../images/solo/sahkomponen.jpg" width="100%"><br>
    <?php } else if (($_SESSION[leveluser])=='imltd'){?>
    <img src="../images/solo/sahimltd.jpg" width="100%"><br>
    <?php }*/ ?>

    <table class="list" border="0" cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
        <tr style="font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;font-size: 10px;">
            <td style="text-align: left">
                <? echo $utd; ?>
            </td>
            <td style="text-align: right">
                <? echo $nodokumen; ?>
            </td>
            <td style="text-align: right"></td>
        </tr>
        <tr style="font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;font-size: 11px;">
            <td style="text-align: left">Formulir Serah Terima Sampel & Kantong Darah</td>
            <td style="text-align: right">Versi : 001</td>
        </tr>
    </table>
    <hr>
    <table class="list" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
        <tr>
            <td style="height: 40px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;"
                colspan="2">SERAH TERIMA SAMPEL & KANTONG DARAH</td>
        </tr>
        <tr style="font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td style="vertical-align: top; width=50%;">
                <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%"
                    style="border-collapse:collapse">
                    <tr>
                        <td style="text-align: left">Tanggal transaksi</td>
                        <td><?php echo $sql_h1['hst_tgl']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">No. transaksi</td>
                        <td><?php echo $sql_h1['hst_notrans']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Bagian yang mengirimkan</td>
                        <td><?php echo $sql_h1['hst_bagpengirim']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Bagian yang menerima</td>
                        <td><?php echo $_SESSION['leveluser']; ?></td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;width=50%">
                <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%"
                    style="border-collapse:collapse">
                    <tr>
                        <td style="text-align: left">Asal Kantong Darah</td>
                        <td><?php echo $sql_h1['hst_asal']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Kode alat pengiriman</td>
                        <td><?php echo $sql_h1['hst_kode_alat']; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Suhu saat diterima</td>
                        <td><?php echo $sql_h1['hst_suhuterima']; ?><sup>o</sup>C</td>
                    </tr>
                    <tr>
                        <td style="text-align: left">Keadaan umum</td>
                        <td><?php echo $sql_h1['hst_kondisiumum']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?
    $sql_d = "SELECT sd.`dst_iddetail`, sd.`dst_no_aftap`, sd.`dst_tglaftap`, sd.`dst_notrans`, sd.`dst_nokantong`,

    CASE
        WHEN (sd.`dst_statusktg` = '1' AND sd.`dst_sahktg` = '0') THEN 'Aftap'
        WHEN (sd.`dst_statusktg` = '1' AND sd.`dst_sahktg` = '1') THEN 'Karantina'
        WHEN (sd.`dst_statusktg` = '2') THEN 'Sehat'
        WHEN (sd.`dst_statusktg` = '3') THEN 'Keluar'
        WHEN (sd.`dst_statusktg` = '4') THEN 'Reaktif-Rusak'
        WHEN (sd.`dst_statusktg` = '5') THEN 'Rusak-gagal'
        WHEN (sd.`dst_statusktg` = '6') THEN 'Rusak-Dimusnahkan'
        ELSE 'Tidak ada'
    END AS `dst_statusktg`,

    sd.`st_statusktg_new`, sd.`dst_old_position`, sd.`dst_new_position`,
    sd.`dst_sahktg_new`, sd.`dst_merk`, sd.`dst_golda`, sd.`dst_rh`, sd.`dst_kodedonor`, sd.`dst_berat`, sd.`dst_volumektg`,

    CASE
        WHEN sd.`dst_jenisktg` = '1' THEN 'SB'
        WHEN sd.`dst_jenisktg` = '2' THEN 'DB'
        WHEN sd.`dst_jenisktg` = '3' AND st.`jenis` = '3' AND (st.`metoda` IS NULL OR st.`metoda` = '') THEN 'TR'
        WHEN sd.`dst_jenisktg` = '3' AND st.`jenis` = '3' AND st.`metoda` = 'TB' THEN 'TB'
        WHEN sd.`dst_jenisktg` = '4' THEN 'QD'
        WHEN sd.`dst_jenisktg` = '5' AND st.`jenis` = '5' AND st.`metoda` = 'TTF' THEN 'LR'
        WHEN sd.`dst_jenisktg` = '5' AND st.`jenis` = '5' AND st.`metoda` = 'TT' THEN 'NLR'
        WHEN sd.`dst_jenisktg` = '6' THEN 'PB'
    END AS `dst_jenisktg`,

    sd.`dst_sample`,
    CASE WHEN sd.`dst_sah` = '1' THEN 'Sesuai' ELSE 'Tdk Sesuai' END AS `dst_sah`,
    CASE WHEN sd.`dst_lamabaru` = '0' THEN 'BR' ELSE 'UL' END AS `dst_lamabaru`,
    CASE WHEN sd.`dst_kel` = '0' THEN 'LK' ELSE 'PR' END AS `dst_kel`,
    CASE WHEN sd.`dst_dsdp` = '0' THEN 'DS' ELSE 'DP' END AS `dst_dsdp`,

    sd.`dst_umur`, sd.`dst_lama_aftap`, sd.`dst_statuspengambilan`, sd.`dst_ptgaftap`, sd.`dst_volambil`

FROM `serahterima_detail` sd
LEFT JOIN `stokkantong` st ON sd.`dst_nokantong` = st.`noKantong`
WHERE sd.`dst_notrans` = '$notransaksi'
ORDER BY dst_jenisktg ASC, dst_golda ASC";
    //echo "$sql_d<br>";
    $sql_d1 = mysql_query($sql_d);
    ?>
    <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
        <thead
            style="background-color:#DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <tr
                style="background-color: #DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                <th style="text-align: center; height: 40px">No</th>
                <th style="text-align: center; height: 40px">Nomor<br>Kantong</th>
                <th style="text-align: center; height: 40px">Nomor<br>Aftap</th>
                <th style="text-align: center; height: 40px">Jenis<br>Kantong</th>
                <th style="text-align: center; height: 40px">Volume</th>
                <th style="text-align: center; height: 40px">Lama Aftap <br>(menit)</th>
                <th style="text-align: center; height: 40px">Merk</th>
                <th style="text-align: center; height: 40px">Status<br>Kantong</th>
                <th style="text-align: center; height: 40px">Ptgs<br>Aftap</th>
                <th style="text-align: center; height: 40px">Gol</th>
                <th style="text-align: center; height: 40px">Rh</th>
                <th style="text-align: center; height: 40px">Jns<br>Donor</th>
                <th style="text-align: center; height: 40px">Sesuai?</th>
            </tr>
        </thead>
        <tbody>
            <?
            $no = 0;
            while ($sgd = mysql_fetch_assoc($sql_d1)) {
                $no++;
            ?>
                <tr
                    style="font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                    <td style="text-align: right;"> <?php echo $no . '.'; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_nokantong']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_no_aftap']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_jenisktg']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_volumektg']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_lama_aftap'] ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_merk']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_statusktg']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_ptgaftap']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_golda']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_rh']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_dsdp']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sgd['dst_sah']; ?> </td>
                </tr>
            <?
            }
            ?>
        </tbody>
    </table>
    <br>
    <?
    $sq_k = "SELECT 
    sd.`dst_jenisktg`,
    sk.`metoda`,
    COUNT(CASE WHEN (sd.`dst_golda`='A' AND sd.`dst_rh`='+') THEN 1 ELSE NULL END) AS 'Apos',
    COUNT(CASE WHEN (sd.`dst_golda`='B' AND sd.`dst_rh`='+') THEN 1 ELSE NULL END) AS 'Bpos',
    COUNT(CASE WHEN (sd.`dst_golda`='O' AND sd.`dst_rh`='+') THEN 1 ELSE NULL END) AS 'Opos',
    COUNT(CASE WHEN (sd.`dst_golda`='AB' AND sd.`dst_rh`='+') THEN 1 ELSE NULL END) AS 'ABpos',
    COUNT(CASE WHEN (sd.`dst_golda`='A' AND sd.`dst_rh`='-') THEN 1 ELSE NULL END) AS 'Aneg',
    COUNT(CASE WHEN (sd.`dst_golda`='B' AND sd.`dst_rh`='-') THEN 1 ELSE NULL END) AS 'Bneg',
    COUNT(CASE WHEN (sd.`dst_golda`='O' AND sd.`dst_rh`='-') THEN 1 ELSE NULL END) AS 'Oneg',
    COUNT(CASE WHEN (sd.`dst_golda`='AB' AND sd.`dst_rh`='-') THEN 1 ELSE NULL END) AS 'ABneg'
    FROM 
        serahterima_detail sd
    LEFT JOIN 
        stokkantong sk ON sk.noKantong = sd.dst_nokantong
    WHERE 
        sd.`dst_notrans`='$notransaksi'
    GROUP BY 
        sd.`dst_jenisktg`, sk.`metoda`";
    $sqk = mysql_query($sq_k);
    $no = 0;
    ?>
    <table class="list" border=1 cellpadding="2" cellspacing="2" width="50%" style="border-collapse:collapse">
        <thead
            style="background-color:#DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <tr
                style="background-color: #DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                <th style="text-align: center;" rowspan="2">No</th>
                <th style="text-align: center;" rowspan="2">Jenis Kantong</th>
                <th style="text-align: center;" colspan="5">Rhesus Positif</th>
                <th style="text-align: center;" colspan="5">Rhesus Negatif</th>
                <th style="text-align: center;" rowspan="2">Jml</th>
            </tr>
            <tr
                style="background-color: #DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                <th style="text-align: center;">A</th>
                <th style="text-align: center;">B</th>
                <th style="text-align: center;">O</th>
                <th style="text-align: center;">AB</th>
                <th style="text-align: center;">Jml</th>
                <th style="text-align: center;">A</th>
                <th style="text-align: center;">B</th>
                <th style="text-align: center;">O</th>
                <th style="text-align: center;">AB</th>
                <th style="text-align: center;">Jml</th>
            </tr>
        </thead>
        <tbody>
            <?
            $jmlap = 0;
            $jmlbp = 0;
            $jmlop = 0;
            $jmlabp = 0;
            $jmlan = 0;
            $jmlbn = 0;
            $jmlon = 0;
            $jmlabn = 0;
            $jmlrhp = 0;
            $jmlrhn = 0;
            $jmlrow = 0;
            while ($sq_k1 = mysql_fetch_assoc($sqk)) {
                $no++;
                $jmlap  = $jmlap + $sq_k1['Apos'];
                $jmlbp  = $jmlbp + $sq_k1['Bpos'];
                $jmlop  = $jmlop + $sq_k1['Opos'];
                $jmlabp = $jmlabp + $sq_k1['ABpos'];
                $jmlan  = $jmlan + $sq_k1['Aneg'];
                $jmlbn  = $jmlbn + $sq_k1['Bneg'];
                $jmlon  = $jmlon + $sq_k1['Oneg'];
                $jmlabn = $jmlabn + $sq_k1['ABneg'];

                $jmlrhp = $sq_k1['Apos'] + $sq_k1['Bpos'] + $sq_k1['Opos'] + $sq_k1['ABpos'];
                $jmlrhn = $sq_k1['Aneg'] + $sq_k1['Bneg'] + $sq_k1['Oneg'] + $sq_k1['ABneg'];

                $jmlrow = $jmlrhp + $jmlrhn;

                switch ($sq_k1['dst_jenisktg']) {
                    case 1:
                        $jenis = 'Kantong Single';
                        break;
                    case 2:
                        $jenis = 'Kantong Double';
                        break;
                    case 3:
                        $metoda = isset($sq_k1['metoda']) ? trim($sq_k1['metoda']) : '';
                        if ($metoda == '' || is_null($metoda)) {
                            $jenis = 'Kantong TRIPLE';
                        } elseif (strtoupper($metoda) == 'TB') {
                            $jenis = 'Kantong TB';
                        } else {
                            $jenis = '-';
                        }
                        break;
                    case 4:
                        $jenis = 'Kantong Quadruple';
                        break;
                    case 5:
                        $metoda = isset($sq_k1['metoda']) ? trim($sq_k1['metoda']) : '';
                        if ($metoda == 'TTF' || is_null($metoda)) {
                            $jenis = 'Kantong LR';
                        } elseif (strtoupper($metoda) == 'TT') {
                            $jenis = 'Kantong NLR';
                        } else {
                            $jenis = '-';
                        }
                        break;
                    case 6:
                        $jenis = 'Kantong Pediatrik';
                        break;
                    default:
                        $jenis = "--";
                } ?>
                <tr
                    style="font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                    <td style="text-align: right;"> <?php echo $no . '.'; ?> </td>
                    <td style="text-align: left;"> <?php echo $jenis; ?> </td>
                    <td style="text-align: center;"> <?php echo $sq_k1['Apos']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sq_k1['Bpos']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sq_k1['Opos'] ?> </td>
                    <td style="text-align: center;"> <?php echo $sq_k1['ABpos']; ?> </td>
                    <td style="text-align: center;"> <?php echo $jmlrhp; ?> </td>
                    <td style="text-align: center;"> <?php echo $sq_k1['Aneg']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sq_k1['Bneg']; ?> </td>
                    <td style="text-align: center;"> <?php echo $sq_k1['Oneg'] ?> </td>
                    <td style="text-align: center;"> <?php echo $sq_k1['ABneg']; ?> </td>
                    <td style="text-align: center;"> <?php echo $jmlrhn; ?> </td>
                    <td style="text-align: center;"> <?php echo $jmlrow; ?> </td>
                </tr>
            <?
            }
            $jmlrhp = $jmlap + $jmlbp + $jmlop + $jmlabp;
            $jmlrhn = $jmlan + $jmlbn + $jmlon + $jmlabn;
            $jmlrow = $jmlrhp + $jmlrhn;
            ?>

            <tr
                style="font-size:12px; color:#000000;background-color: #DCDCDC; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                <td colspan="2" style="text-align: center">Jumlah</td>
                <td style="text-align: center">
                    <? echo $jmlap; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlbp; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlop; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlabp; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlrhp; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlan; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlbn; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlon; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlabn; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlrhn; ?>
                </td>
                <td style="text-align: center">
                    <? echo $jmlrow; ?>
                </td>
            </tr>

        </tbody>
    </table>
    <?
    $usr        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_user]'"));
    $pencatat   = $usr['nama_lengkap'];
    $usr1        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_pengirim]'"));
    $pengirim   = $usr1['nama_lengkap'];
    $usr2        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_penerima]'"));
    $penerima   = $usr2['nama_lengkap'];
    $usr3        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_penerima2]'"));
    $penerima2   = $usr3['nama_lengkap'];
    $usr4        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_penerima3]'"));
    $penerima3   = $usr4['nama_lengkap'];
    ?>
    <br>
    <table class="list" border=1 cellpadding="5" cellspacing="5" style="border-collapse:collapse" width="100%">
        <thead <tr
            style="background-color: #DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <th style="text-align: center;height: 30px;"></th>
            <th style="text-align: center;height: 30px;" nowrap>Nama Petugas</th>
            <th style="text-align: center;height: 30px;" nowrap>Tanda Tangan</th>
            <th style="text-align: center;height: 30px;" nowrap>Tanggal dan Jam</th>
            <th style="text-align: center;height: 30px;">Catatan</th>
            </tr>
        </thead>
        <tr style="font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td nowrap>Petugas Pencatat</td>
            <td nowrap><?php echo $pencatat; ?></td>
            <td></td>
            <td></td>
            <td rowspan="5" style="vertical-align:top;">
                <ol
                    style="font-size:10px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                    <li>Jenis Kantong Darah = BS : Kantong Single; DB : Kantong Double; TR: Kantong Triple, QD:Kantong
                        Quadruple; PB: Kantong Pediatrik</li>
                    <li>Status kantong : adalah status pada saat serah terima dilakukan</li>
                </ol>
            </td>

        </tr>
        <!--tr style="font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td nowrap>Petugas Pengirim</td>
        <td nowrap><?php echo $pengirim; ?></td>
        <td></td>
        <td></td>

    </tr>
    <tr style="font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td nowrap>Petugas Penerima</td>
        <td nowrap><?php echo $sql_h1['hst_penerima2']; ?></td>
        <td></td>
        <td></td>

    </tr-->
        <tr style="font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td nowrap>Petugas Pengirim</td>
            <td nowrap><?php echo $pengirim; ?></td>
            <td></td>
            <td></td>
        </tr>
        <tr style="font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td nowrap>Petugas Penerima Bag Komponen</td>
            <td nowrap><?php echo $penerima; ?></td>
            <td></td>
            <td></td>
        </tr>
        <tr style="font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td nowrap>Petugas Penerima Bag IMLTD</td>
            <td nowrap><?php echo $penerima2; ?></td>
            <td></td>
            <td></td>
        </tr>
        <tr style="font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td nowrap>Petugas Penerima Bag Konfirmasi</td>
            <td nowrap><?php echo $penerima3; ?></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <? echo "<meta http-equiv='refresh' content='2;url=pmiaftap.php?module=sr_aftap_list'"; ?>
</body>