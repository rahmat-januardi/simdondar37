<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser = $_SESSION[namauser];
$namalengkap = $_SESSION[nama_lengkap];
$tglsebelum = mktime(0, 0, 0, date("m"), 1, date("Y"));
$tglawal = date("Y-m-d");
$hariini = date("Y-m-d");
?>
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<script language=javascript src="util.js" type="text/javascript"> </script>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<style type="text/css">
.styled-select select {
    background-color: #FCF9F9;
    border: none;
    width: auto;
    padding: 3px;
    font-size: 15px;
    cursor: pointer;
}
</style>
<style>
table {
    border-collapse: collapse;
}
</style>
<html xmlns="http://www.w3.org/1999/xhtml">
<style>
body {
    font-family: "Lato", sans-serif;
}
</style>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SIMDONDAR</title>
</head>

<body>
    <?

    $filterTanggal = "";
    // Jika Tanggal Aftap diisi, gunakan filter berdasarkan tanggal aftap saja
    if (!empty($_POST['tglAftap']) && !empty($_POST['tglAftap1'])) {
        $startAftap = $_POST['tglAftap'];
        $endAftap = $_POST['tglAftap1'];
        $filterTanggal = "AND DATE(rtgl_aftap) >= '$startAftap' AND DATE(rtgl_aftap) <= '$endAftap'";
    }
    // Tambahan filter berdasarkan tanggal produksi jika diisi 
    else if (!empty($_POST['tglProduksi']) && !empty($_POST['tglProduksi1'])) {
        $startProduksi = $_POST['tglProduksi'];
        $endProduksi = $_POST['tglProduksi1'];
        $filterTanggal .= " AND DATE(rtgl_olah) >= '$startProduksi' AND DATE(rtgl_olah) <= '$endProduksi'";
    }
    // Tambahan filter berdasarkan tanggal kadaluwarsa jika diisi
    else if (!empty($_POST['tglKadaluwarsa']) && !empty($_POST['tglKadaluwarsa1'])) {
        $startKadaluwarsa = $_POST['tglKadaluwarsa'];
        $endKadaluwarsa = $_POST['tglKadaluwarsa1'];
        $filterTanggal .= " AND DATE(rtgl_ed) >= '$startKadaluwarsa' AND DATE(rtgl_ed) <= '$endKadaluwarsa'";
    } else {
        // Jika Tanggal Aftap tidak diisi, gunakan waktu umum
        if (isset($_POST['waktu'])) {
            $tglawal = $_POST['waktu'];
        }
        if (!empty($_POST['waktu1'])) {
            $hariini = $_POST['waktu1'];
        }

        $filterTanggal = "AND DATE(rtgl) >= '$tglawal' AND DATE(rtgl) <= '$hariini'";
    }


    if (isset($_POST[waktu])) {
        $tglawal = $_POST[waktu];
        $hariini = $hariini;
    }
    if ($_POST[waktu1] != '') $hariini = $_POST[waktu1];
    $status = $_POST['status'];
    $petugas = $_POST['petugas'];
    $jenisProduk = $_POST['jenisProduk'];
    $golDar = $_POST['golDar'];
    $rhesus = $_POST['rhesus'];
    $gold_rhe = $golDar . $rhesus;

    ?>
    <a name="atas" id="atas"></a>
    <font size="4" color=00008B><b>Rekap Hasil Perilisan Produk Komponen Darah</b></font><br><br>
    <form name="cari" method="POST" action="<? echo $PHPSELF ?>">

        <table cellpadding=1 cellspacing="0" border="0">
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Tanggal</td>
                <td><input name="waktu" id="datepicker" value="<?= $tglawal ?>" type=text size="10"
                        style="font-family:monospace"> s/d
                    <input name="waktu1" id="datepicker1" value="<?= $hariini ?>" type=text size="10"
                        style="font-family:monospace">
                </td>
                <td>Tanggal Produksi</td>
                <td><input name="tglProduksi" value="<?= $startProduksi ?>" type=date size="10"
                        style="font-family:monospace"> s/d
                    <input name="tglProduksi1" value="<?= $endProduksi ?>" type=date size="10"
                        style="font-family:monospace">
                </td>
            </tr>
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Inisial Petugas</td>
                <td> <input name="petugas" id="petugas" value="<?= $petugas ?>" type=text size="25"
                        style="font-family:monospace" placehol></td>
                <td>Tanggal Aftap</td>
                <td><input name="tglAftap" value="<?= $startAftap ?>" type=date size="10" style="font-family:monospace">
                    s/d
                    <input name="tglAftap1" value="<?= $endAftap ?>" type=date size="10" style="font-family:monospace">
                </td>
            </tr>
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Jenis Produk</td>
                <td>
                    <?
                    $prod1 = '';
                    $prod2 = '';
                    $prod3 = '';
                    $prod4 = '';
                    $prod5 = '';
                    $prod6 = '';
                    $prod0 = '0';
                    switch ($jenisProduk) {
                        case '':
                            $prod0 = 'selected';
                            break;
                        case 'PRC':
                            $prod1 = 'selected';
                            break;
                        case 'WB':
                            $prod2 = 'selected';
                            break;
                        case 'FFP':
                            $prod3 = 'selected';
                            break;
                        case 'FP72':
                            $prod4 = 'selected';
                            break;
                        case 'LP':
                            $prod5 = 'selected';
                            break;
                        case 'TC':
                            $prod6 = 'selected';
                            break;
                    }
                    ?>
                    <select name="jenisProduk" class="styled-select">
                        <option value="" <?= $prod0 ?>>SEMUA</option>
                        <option value="PRC" <?= $prod1 ?>>PRC</option>
                        <option value="WB" <?= $prod2 ?>>WB</option>
                        <option value="FFP" <?= $prod3 ?>>FFP</option>
                        <option value="FP 72" <?= $prod4 ?>>FP72</option>
                        <option value="LP" <?= $prod5 ?>>LP</option>
                        <option value="TC" <?= $prod6 ?>>TC</option>
                    </select>

                </td>
                <td>Tanggal Kadaluwarsa</td>
                <td><input name="tglKadaluwarsa" value="<?= $startKadaluwarsa ?>" type=date size="10"
                        style="font-family:monospace"> s/d
                    <input name="tglKadaluwarsa1" value="<?= $endKadaluwarsa ?>" type=date size="10"
                        style="font-family:monospace">
                </td>
            </tr>
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Golongan Darah</td>
                <td>
                    <?
                    $golDarA = '';
                    $golDarB = '';
                    $golDarAB = '';
                    $golDarO = '';
                    switch ($golDar) {
                        case 'A':
                            $golDarA = 'selected';
                            break;
                        case 'B':
                            $golDarB = 'selected';
                            break;
                        case 'AB':
                            $golDarAB = 'selected';
                            break;
                        case 'O':
                            $golDarO = 'selected';
                            break;
                    }

                    $rhesusPos = '';
                    $rhesusNeg = '';
                    switch ($rhesus) {
                        case '+':
                            $rhesusPos = 'selected';
                            break;
                        case '-':
                            $rhesusNeg = 'selected';
                            break;
                    }
                    ?>
                    <select name="golDar" class="styled-select">
                        <option value="">SEMUA</option>
                        <option value="A" <?= $golDarA ?>>A</option>
                        <option value="B" <?= $golDarB ?>>B</option>
                        <option value="AB" <?= $golDarAB ?>>AB</option>
                        <option value="O" <?= $golDarO ?>>O</option>
                    </select>
                    <select name="rhesus">
                        <option value="">-SEMUA-</option>
                        <option value="+" <?= $rhesusPos ?>>Positif</option>
                        <option value="-" <?= $rhesusNeg ?>>Negatif</option>
                    </select>
                </td>
            </tr>
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
                <td>Status Release</td>
                <td>
                    <?
                    $sel1 = '';
                    $sel2 = '';
                    $sel3 = '';
                    $sel4 = '0';
                    switch ($status) {
                        case '':
                            $sel4 = 'selected';
                            break;
                        case '0':
                            $sel1 = 'selected';
                            break;
                        case '1':
                            $sel2 = 'selected';
                            break;
                        case '2':
                            $sel3 = 'selected';
                            break;
                    }
                    ?>
                    <select name="status" class="styled-select">
                        <option value="0" <?= $sel1 ?>>LULUS</option>
                        <option value="1" <?= $sel2 ?>>TIDAK LULUS</option>
                        <option value="2" <?= $sel3 ?>>LULUS DENGAN CATATAN</option>
                        <option value="" <?= $sel4 ?>>SEMUA</option>
                    </select>

                </td>
                <td><input type=submit name=submit class="swn_button_blue" value="Tampilkan data">
                    <a href="#bawah" class="swn_button_blue">Ke bawah</a>
                    <a href="pmiqa.php?module=input_qa" class="swn_button_blue">Kembali</a>
                </td>
            </tr>
        </table>
    </form>
    <table border=1 cellpadding=4 style="border-collapse:collapse">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th rowspan="2">NO.</th>
            <th rowspan="2">NO. RELEASE</th>
            <th rowspan="2">TGL. RELEASE</th>
            <th rowspan="2">NO. KANTONG</th>
            <th rowspan="2">JENIS PRODUK</th>
            <th rowspan="2">GOL. DARAH</th>
            <th colspan="5">SPESIFIKASI KANTONG & IDENTITAS</th>
            <th colspan="7">VISUAL</th>
            <th colspan="2">SELEKSI & AFTAP</th>
            <th rowspan="2">PENGO-<br>LAHAN</th>
            <th colspan="3">VOLUME PRODUK</th>
            <th colspan="2">PEMERIKSAAN LAB</th>
            <th colspan="5">HASIL PRODUK PROLIS</th>
        </tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th>Label & Identitas</th>
            <th>Kode Unik</th>
            <th>Tgl. Aftap</th>
            <th>Tgl. Produksi</th>
            <th>Tgl. ED</th>

            <th>Kebocoran</th>
            <th>Selang</th>
            <th>Hemolysis</th>
            <th>Lipemik</th>
            <th>Ikterik</th>
            <th>Kehijauan</th>
            <th>Bekuan</th>

            <th>Seleksi</th>
            <th>Lama Aftap</th>

            <th>Berat</th>
            <th>Volume</th>
            <th>Standar Vol</th>
            <th>IMLTD & KGD</th>
            <th>History Donor</th>
            <th>Status</th>
            <th>Catatan</th>
            <th>Petugas</th>
            <th>Checker</th>
            <th>P.Jawab</th>
        </tr>

        <?php
        $no = 0;
        $sql = "SELECT * FROM `release`
    		  WHERE 1=1 $filterTanggal and rproduk like '%$jenisProduk%' and rgolda like '%$gold_rhe%' and rstatus like '%$status%' and `ruser` like '%$petugas%' order by rnotrans asc";
        //echo "$sql";
        $qraw = mysql_query($sql);
        $statusrelease = '';
        while ($tmp = mysql_fetch_assoc($qraw)) {
            $no++;
            switch ($tmp['status']) {
                case '0':
                    $statusrelease = 'Lulus';
                    break;
                case '1':
                    $statusrelease = 'Tidak Lulus';
                    break;
                case '2':
                    $statusrelease = 'Lulus dengan Catatan';
                    break;
                default:
                    $statusrelease = '-';
            }
        ?>
        <tr style="font-size:11px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
            onMouseOut="this.className='normal'">
            <td align="right"><?= $no . '.' ?></td>
            <td align="left"><?= $tmp['rnotrans'] ?></td>
            <td align="left" nowrap><?= $tmp['rtgl'] ?></td>
            <td align="center" nowrap><?= $tmp['rnokantong'] ?></td>
            <td align="left" nowrap><?= $tmp['rproduk'] ?></td>
            <td align="center"><?= $tmp['rgolda'] ?></td>
            <? if ($tmp['rspek_kantong'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rkode_unik'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <td align="left" nowrap><?= $tmp['rtgl_aftap'] ?></td>
            <td align="left" nowrap><?= $tmp['rtgl_olah'] ?></td>
            <td align="left" nowrap><?= $tmp['rtgl_ed'] ?></td>
            <? if ($tmp['rkebocoran'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rselang'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rhemolysis'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rlipemik'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rikterik'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rkehijauan'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rbekuan'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rspek_seleksi'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rspek_aftap'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rspek_pengolahan'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <td align="right" nowrap><?= number_format($tmp['rberat_timbang'] * 1000) ?> gr</td>
            <td align="right" nowrap><?= number_format(round($tmp['rvolume'], 2)) ?> ml</td>
            <? if ($tmp['rspek_volume'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rspek_imltd'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <? if ($tmp['rspek_imltd_old'] == '1') { ?>
            <td align="center">&radic;</td>
            <? } else { ?>
            <td align="center" bgcolor="red">
                <font color="white">X</font>
            </td>
            <? } ?>
            <td align="left" nowrap><?= $tmp['rsatus_ket'] ?></td>
            <td align="left"><?= $tmp['rnote'] ?></td>
            <td align="left"><?= $tmp['ruser'] ?></td>
            <td align="left"><?= $tmp['rchecker'] ?></td>
            <td align="left"><?= $tmp['rpengesah'] ?></td>
        </tr>
        <? }
        if ($no == 0) { ?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'"
            onMouseOut="this.className='normal'">
            <td colspan=31 align="center">Tidak ada data release produk dari tanggal, petugas dan status yang dipilih
            </td>
            <? } ?>
    </table><br>


    <table border=1 cellpadding=4 style="border-collapse:collapse">
        <?
        $golA = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as gola FROM `release` 
		  WHERE rgolda='A+' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $golB = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as golb FROM `release` 
		  WHERE rgolda='B+' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $golAB = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as golab FROM `release` 
		  WHERE rgolda='AB+' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $golO = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as golo FROM `release` 
		  WHERE rgolda='O+' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $golANEG = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as golaneg FROM `release` 
		  WHERE rgolda='A-' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $golBNEG = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as golbneg FROM `release` 
		  WHERE rgolda='B-' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $golABNEG = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as golabneg FROM `release` 
		  WHERE rgolda='AB-' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $golONEG = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as goloneg FROM `release` 
		  WHERE rgolda='O-' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $lulus = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as lulus FROM `release` 
		  WHERE rstatus='0' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $tklulus = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as tklulus FROM `release` 
		  WHERE rstatus='1' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $ctlulus = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as ctlulus FROM `release` 
		  WHERE rstatus='2' AND DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));

        $total = mysql_fetch_assoc(mysql_query("SELECT count(rnotrans) as total FROM `release` 
		  WHERE DATE(rtgl)>='$tglawal' AND date(rtgl)<='$hariini' and rstatus like '$status%' and `ruser` like '%$petugas%' order by rnotrans asc"));


        ?>

        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">

            <th colspan="8" align="center">Golongan Darah</th>
            <th colspan="3" align="center">Keterangan</th>
            <th rowspan="4" align="center">Total Pemeriksaan</th>
        </tr>

        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th colspan="4" align="center">Positif</th>
            <th colspan="4" align="center">Negatif</th>
            <th rowspan="3" align="center">Lulus</th>
            <th rowspan="3" align="center">Lulus Dengan Catatan</th>
            <th rowspan="3" align="center">Tidak Lulus</th>
        </tr>

        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th rowspan="2">A+</th>
            <th rowspan="2">B+</th>
            <th rowspan="2">O+</th>
            <th rowspan="2">AB+</th>
            <th rowspan="2">A-</th>
            <th rowspan="2">B-</th>
            <th rowspan="2">O-</th>
            <th rowspan="2">AB-</th>

        </tr>

        <tr></tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th rowspan="2"><?= $golA["gola"] ?></th>
            <th rowspan="2"><?= $golB["golb"] ?></th>
            <th rowspan="2"><?= $golO["golo"] ?></th>
            <th rowspan="2"><?= $golAB["golab"] ?></th>

            <th rowspan="2"><?= $golANEG["golaneg"] ?></th>
            <th rowspan="2"><?= $golBNEG["golbneg"] ?></th>
            <th rowspan="2"><?= $golONEG["goloneg"] ?></th>
            <th rowspan="2"><?= $golABNEG["golabneg"] ?></th>

            <th rowspan="2"><?= $lulus["lulus"] ?></th>
            <th rowspan="2"><?= $ctlulus["ctlulus"] ?></th>
            <th rowspan="2"><?= $tklulus["tklulus"] ?></th>
            <th rowspan="2"><?= $total["total"] ?></th>
        </tr>


    </table><br />
    <a href="pmiqa.php?module=input_qa" class="swn_button_blue">Kembali</a>
    <?
    if ($no !== 0) {
    ?><a
        href="pmiqa.php?module=cetak_rekap&tgl1=<?= $tglawal ?>&tgl2=<?= $hariini ?>&stts=<?= $status ?>&ptgs=<?= $petugas ?>"
        class="swn_button_blue">Export ke Excel</a>
    <!--a href="pmiqa.php?module=print_rekap&tgl1=<?= $tglawal ?>&tgl2=<?= $hariini ?>&stts=<?= $status ?>&ptgs=<?= $petugas ?>" class="swn_button_blue">Cetak</a-->
    <?
    }
    ?>
    <a href="#atas" class="swn_button_blue">Ke Atas</a>
    <a name="bawah" id="bawah"></a>
    <?
    ?>
</body>

</html>