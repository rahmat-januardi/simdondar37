<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        document.getElementById('minta1').focus();
    });

    function goBack() {
        window.history.back();
    }
</script>

<body OnLoad="document.mintadarah1.minta1.focus();">
    <?
    include('config/db_connect.php');
    $today = date('Y-m-d');
    $today1 = $today;
    $sampel = $_GET['kode'];

    //echo "$sampel";
    ?>
    <button onclick="goBack()">KEMBALI</button>

    <table border=1 cellpadding=5 cellspacing=5 style="border-collapse:collapse">
        <tr style="background-color:red; font-size:14px; color:#FFFFFF; font-family:Verdana;" align='center'>
            <td rowspan="2">No</td>
            <td rowspan="2">Tanggal <br>Sampel</td>
            <td rowspan="2">ID Sampel</td>
            <td rowspan="2">Pendonor</td>
            <td rowspan="2">Gol.<br>Darah</td>
            <td rowspan="2">No. Telp</td>
            <td rowspan="2">Jenis<br>Donor</td>
            <td colspan="3" style="background-color:#ffa600">Permintaan Darah</td>
            <td colspan="3" style="background-color:#5500ff">Titer</td>
            <td colspan="5" style="background-color:#a32103">Hematologi</td>
            <td rowspan="2" style="background-color:#33a303">Chlia</td>
            <td rowspan="2" style="background-color:#09e3b0">NAT</td>
            <td rowspan="2" style="background-color:#04b3bf">KGD</td>
            <td rowspan="2">AKSI</td>

        </tr>
        <tr style="background-color:red; font-size:14px; color:#FFFFFF; font-family:Verdana;" align='center'>
            <td style="background-color:#ffa600">Tgl.</td>
            <td style="background-color:#ffa600">Nama</td>
            <td style="background-color:#ffa600">Rumah Sakit</td>

            <td style="background-color:#5500ff">Sampel</td>
            <td style="background-color:#5500ff">Nilai</td>
            <td style="background-color:#5500ff">Hasil</td>

            <td style="background-color:#a32103">HB</td>
            <td style="background-color:#a32103">HCT</td>
            <td style="background-color:#a32103">TC</td>
            <td style="background-color:#a32103">LEU</td>
            <td style="background-color:#a32103">Hasil</td>



        </tr>
        <?php
        //pagination
        //require_once('config/dbi_connect.php');

        $sql = "SELECT * from samplekode WHERE sk_kode='$sampel'";
        $sq = mysql_query($sql);
        $no = 0;
        $jum = mysql_num_rows($sq);

        $file = basename($_SERVER['PHP_SELF']);

        if ($jum = 0) { ?>
            <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
                <td align="center" colspan="22">Tidak Ada Data Pemeriksaan</td>
            </tr>
            <?
        } else {
            while ($dt = mysql_fetch_assoc($sq)) {
                $no++;
            ?>
                <form method="post" action="<?php echo $file; ?>?module=jadwalambil">
                    <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='biasa'">
                        <td align="right"><?= $no ?></td>
                        <td align="left"><?= $dt[sk_tgl_plebotomi] ?></td>
                        <td align="left"><?= $dt[sk_kode] ?><input type="hidden" name="sampelp" value="<?= $dt[sk_kode] ?>"></td>
                        <? //cari donor
                        $donor = mysql_fetch_assoc(mysql_query("select Nama,telp2 from pendonor where Kode='$dt[sk_donor]' limit 1")); ?>
                        <td align="left"><?= $donor[Nama] ?><input type="hidden" name="namap" value="<?= $donor[Nama] ?>"></td>
                        <td align="left"><?= $dt[sk_gol] . $dt[sk_rh] ?><input type="hidden" name="golp" value="<?= $dt[sk_gol] . $dt[sk_rh] ?>"></td>

                        <td align="left"><?= $donor[telp2] ?></td>
                        <? //cari transaksi donor
                        $ht = mysql_fetch_assoc(mysql_query("select NoTrans,id_permintaan,CASE donor_tpk
                        WHEN '0' THEN 'APH'
                        WHEN '1' THEN 'TPK'
                        END AS donor_tpk ,CASE JenisDonor
                        WHEN '0' THEN 'DS'
                        WHEN '1' THEN 'DP'
                        END AS JenisDonor from htransaksi where KodePendonor='$dt[sk_donor]' order by insert_on DESC limit 1")); ?>
                        <td align="left"><?= $ht[JenisDonor] . ' (' . $ht[donor_tpk] . ')' ?><input type="hidden" name="transp" value="<?php echo $ht['NoTrans']; ?>"></td>
                        <? //cari pasien
                        $psn = mysql_fetch_assoc(mysql_query("select nama,NamaRs,umur, date(tgl_register) as tgl from v_caripasien where noform='$ht[id_permintaan]' limit 1"));
                        if ($ht[JenisDonor] == "DP") { ?>
                            <td align="left"><?= $psn[tgl] ?><input type="hidden" name="mintap" value="<?= $ht[id_permintaan] ?>"></td>
                            <td align="left"><?= $psn[nama] . ' (' . $psn[umur] . ' thn)' ?></td>
                            <td align="left"><?= $psn[NamaRs] ?></td>
                        <? } else { ?>
                            <td align="left">-</td>
                            <td align="left">-</td>
                            <td align="left">-<? } ?>
                                <? //cari titer
                                $titer = mysql_fetch_assoc(mysql_query("select cov_titer, CASE cov_vol
                            WHEN '0' THEN 'Rusak/Keruh'
                            WHEN '1' THEN 'Baik/Cukup'
                            ELSE '-'
                            END AS cov_vol
                            ,CASE cov_hasil
                            WHEN '0' THEN 'Tidak Lolos'
                            WHEN '1' THEN 'Lolos'
                            ELSE '-'
                            END AS cov_hasil from covid where cov_sampel='$dt[sk_kode]' order by on_insert DESC limit 1"));
                                ?>
                                <? if ($titer['cov_vol'] == "Baik/Cukup") {
                                    echo '<td align="center">Baik/Cukup</td>';
                                } else if ($titer['cov_vol'] == "Rusak/Keruh") {
                                    echo '<td align="center" style="background-color:#FF0000">Rusak/Keruh</td>';
                                } else {
                                    echo '<td align="center">-</td>';
                                } ?>
                            <td align="left"><?= $titer[cov_titer] ?></td>
                            <? if ($titer[cov_hasil] == "Lolos") {
                                echo '<td align="center">Lulus</td>';
                            } else if ($titer[cov_hasil] == "Tidak Lolos") {
                                echo '<td align="center" style="background-color:#FF0000">Tidak Lolos</td>';
                            } else {
                                echo '<td align="center">-</td>';
                            } ?>
                            <? //cari hemmatologi
                            $hm = mysql_fetch_assoc(mysql_query("select dl_hb,dl_hct,dl_plt,dl_leu, CASE dl_hasil
                            WHEN '0' THEN 'Tidak Lolos'
                            WHEN '1' THEN 'Lolos'
                            ELSE 'Cek Ulang'
                            END AS dl_hasil from hematologi where dl_sampel='$dt[sk_kode]' order by on_insert DESC limit 1"));
                            if ($hm['dl_hb'] == "") { ?>
                                <td align="left">-</td>
                                <td align="left">-</td>
                                <td align="left">-</td>
                                <td align="left">-</td>
                                <td align="left">-</td>
                            <? } else { ?>
                                <td align="left"><?= $hm[dl_hb] ?></td>
                                <td align="left"><?= $hm[dl_hct] ?></td>
                                <td align="left"><?= $hm[dl_plt] ?></td>
                                <td align="left"><?= $hm[dl_leu] ?></td>
                                <? if ($hm[dl_hasil] == "Cek Ulang") {
                                    echo '<td align="center" style="background-color:#FF0000">Cek Ulang</td>';
                                } else {
                                    echo '<td align="center">' . $hm[dl_hasil] . '</td>';
                                } ?>
                            <? } //cari IMLTD
                            $imltd = mysql_fetch_assoc(mysql_query("select noKantong, Hasil from hasilelisa where (noKantong='$dt[sk_kode]' or idsample='$dt[sk_kode]')  order by Hasil DESC limit 1"));
                            if ($imltd[Hasil] == "0") {
                                echo '<td align="center">NR</td>';
                            } else if ($imltd[Hasil] == "1") {
                                echo '<td align="center" style="background-color:#FF0000">R</td>';
                            } else if ($imltd[Hasil] == "2") {
                                echo '<td align="center" style="background-color:#FF0000">GZ</td>';
                            } else {
                                echo '<td align="center">-</td>';
                            } ?>
                            <? //cari NAT
                            $nat = mysql_fetch_assoc(mysql_query("select idsample,Hasil from hasilnat where idsample='$dt[sk_kode]'  order by Hasil DESC limit 1"));
                            if ($nat['Hasil'] == "0") {
                                echo '<td align="center">NR</td>';
                            } else if ($nat[Hasil] == "1") {
                                echo '<td align="center" style="background-color:#FF0000">R</td>';
                            } else if ($nat[Hasil] == "2") {
                                echo '<td align="center" style="background-color:#FF0000">GZ</td>';
                            } else {
                                echo '<td align="center">-</td>';
                            } ?>
                            <td align="left"><?= $dt[sk_gol] . $dt['sk_rh'] ?></td>
                            <? if (($dt[sk_hasil] == "1") || ($dt[sk_hasil] == "3")) { ?>
                                <td align="center"><input type="submit" class="swn_button_green" onclick="return confirm('Jadwalkan Pendonor')" value="JADWAL"><br>
                                    <a href="<?php echo $file; ?>?module=verifsampel&act=batal&kode=<?php echo $dt['sk_kode']; ?>&donor=<?php echo $dt[sk_donor]; ?>&trans=<?php echo $ht['NoTrans']; ?>"><br><input type="button" class="swn_button_red" onclick="return confirm('Batalkan Penjadwalan Donor ?')" value="BATALKAN"></a>
                                </td>
                </form>
            <? } else if ($dt[sk_hasil] == "0") { ?>
                </form>
                <td><a href="<?php echo $file; ?>?module=verifsampel&act=lanjut&kode=<?php echo $dt['sk_kode']; ?>&donor=<?php echo $dt[sk_donor]; ?>"><br><input type="button" class="swn_button_green" onclick="return confirm('Luluskan Sampel ?')" value="LULUS"></a>
                    <a href="<?php echo $file; ?>?module=verifsampel&act=ulang&kode=<?php echo $dt['sk_kode']; ?>&donor=<?php echo $dt[sk_donor]; ?>"><br><input type="button" class="swn_button_blue" onclick="return confirm('Cek Ulang Sampel ?')" value="ULANG"></a> &nbsp;
                    <a href="<?php echo $file; ?>?module=verifsampel&act=gagal&kode=<?php echo $dt['sk_kode']; ?>&donor=<?php echo $dt[sk_donor]; ?>&trans=<?php echo $ht['NoTrans']; ?>"><br><input type="button" class="swn_button_red" onclick="return confirm('Batalkan Penjadwalan Donor ?')" value="BATALKAN"></a>&nbsp;

                </td>
            <? } else if ($dt[sk_hasil] == "4") {
                                $jadwal = mysql_fetch_assoc(mysql_query("select date(start_event) as tgl, time(start_event) as jam from events where kd_sample='$dt[sk_kode]'"));
            ?>
                <td>Sudah Dijadwalkan <br><?= $jadwal[tgl] ?><br><?= $jadwal[jam] ?></td>
            <? } else {
                                echo "<td>selesai</td>";
                            } ?>

            </tr>
    <?php
            }
        } ?>
    </table>

    <?
    mysql_close();
    ?>