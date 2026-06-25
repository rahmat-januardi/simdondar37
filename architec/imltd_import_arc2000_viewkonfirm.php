<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser = $_SESSION[namauser];
$namalengkap = $_SESSION[nama_lengkap];
?>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        @import url("topstyle.css");

        tr {
            background-color: #FFF8DC
        }

        .initial {
            background-color: #FFF8DC;
            color: #000000
        }

        .normal {
            background-color: #FFF8DC
        }

        .highlight {
            background-color: #7FFF00
        }
    </style>
    <style>
        .awesomeText {
            color: #000;
            font-size: 150%;
        }
    </style>
    <script language="javascript">
        < script >
            $(function() {
                $('a[href*=#]:not([href=#])').click(function() {
                    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                        var target = $(this.hash);
                        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                        if (target.length) {
                            $('html,body').animate({
                                scrollTop: target.offset().top
                            }, 1000);
                            return false;
                        }
                    }
                });
            });

        function printfile(nomortransaksi) {
            $.fn
                .colorbox({
                    href: 'architec/imltd_rpt_konfirm1.php?notrans=' + nomortransaksi,
                    iframe: true,
                    innerWidth: 800,
                    innerHeight: 450
                });
        }
    </script>

    <title>SIMDONDAR</title>
</head>

<body>
    <?php

    $notrans     = $_GET['notrans'];
    $Sq = mysql_query("SELECT `id`, `no_trans`, `instr`, `instr_v`, `scc_serial`, `interface_sn`, `arc_serial`, `trans_time`, `user`, 
		 `id_tes`, `carrier`, `position`, 
		 `b_lot_reag`, `b_id_raw`, `b_ed_reag`, `b_kode_reag`, `b_sn_reag`, `b_abs`, `b_run_time`, `b_hasil`, `b_ket_tes`, 
		 `c_lot_reag`, `c_id_raw`, `c_ed_reag`, `c_kode_reag`, `c_sn_reag`, `c_abs`, `c_run_time`, `c_hasil`, `c_ket_tes`, 
		 `i_lot_reag`, `i_id_raw`, `i_ed_reag`, `i_kode_reag`, `i_sn_reag`, `i_abs`, `i_run_time`, `i_hasil`, `i_ket_tes`, 
		 `s_lot_reag`, `s_id_raw`, `s_ed_reag`, `s_kode_reag`, `s_sn_reag`, `s_abs`, `s_run_time`, `s_hasil`, `s_ket_tes`, 
		 `konfirmer`, `koonfirm_time`, `disahkan`, `status_kantong`, `konfirm_action` FROM `imltd_arc_konfirm` WHERE no_trans='$notrans'");
    //$row = mysql_fetch_assoc($Sq);
    ?>
    <a name="atas" id="atas"></a>
    <table border=0 cellpadding="5" cellspacing="5" width="100%">
        <tr>
            <td align="left" style="background-color: #ffffff">
                <font size=5 color="blue"><b>DATA KONFIRMASI HASIL PEMERIKSAAN ABBOTT ARCHITECT <i>i</i>2000SR</b></font>
            </td>
            <td align="right" style="background-color: #ffffff"><a href="#bawah" class="swn_button_blue">Ke bawah</a></td>
        </tr>
    </table>
    <table class="list" border=1 cellpadding="5" cellspacing="1" width="100%" style="border-collapse:collapse">
        <tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td rowspan=2 align="center">No</td>
            <td rowspan=2 align="center">SampleID</td>
            <td colspan=6 align="center">HBsAg</td>
            <td colspan=6 align="center">HCV</td>
            <td colspan=6 align="center">HIV</td>
            <td colspan=6 align="center">Syphilis</td>
            <td rowspan=2 align="center">Status<br>Kantong</td>
            <td rowspan=2 align="center">Konfirm</td>
        </tr>
        <tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td align="center">OD</td>
            <td align="center">Hasil</td>
            <td align="center">Lot</td>
            <td align="center">ED</td>
            <td align="center">Run Time</td>
            <td align="center">Ket</td>
            <td align="center">OD</td>
            <td align="center">Hasil</td>
            <td align="center">Lot</td>
            <td align="center">ED</td>
            <td align="center">Run Time</td>
            <td align="center">Ket</td>
            <td align="center">OD</td>
            <td align="center">Hasil</td>
            <td align="center">Lot</td>
            <td align="center">ED</td>
            <td align="center">Run Time</td>
            <td align="center">Ket</td>
            <td align="center">OD</td>
            <td align="center">Hasil</td>
            <td align="center">Lot</td>
            <td align="center">ED</td>
            <td align="center">Run Time</td>
            <td align="center">Ket</td>
        </tr>
        <?
        $no = "0";
        $operator = "";
        $konfirmer = "";
        $pengesah = "";
        $waktuKonfirmiasi = "";
        $instrument = "";
        $nomor = "";
        while ($data = mysql_fetch_assoc($Sq)) {
            $no++;
            $nomor  = $data['no_trans'];
            $operator  = $data['user'];
            $konfirmer = $data['konfirmer'];
            $pengesah  = $data['disahkan'];
            $waktuKonfirmiasi = $data['koonfirm_time'];
            $instrument = $data['instr'] . ' - S/N:' . $data['arc_serial'];
            //Status Kantong Saat Konfirmasi
            switch ($data['status_kantong']) {
                case '0':
                    $statuskantong = 'Kosong';
                    break;
                case '1':
                    $statuskantong = 'Karantina';
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
                default:
                    $statuskantong = '-';
            }
            //Aksi User saat konfirmasi
            switch ($data['konfirm_action']) {
                case "0":
                    $aksik = '-';
                    break;
                case "1":
                    $aksik = 'Disehatkan';
                    break;
                case "2":
                    $aksik = 'Dicekal';
                    break;
                case "3":
                    $aksik = 'Ditunda';
                    break;
                default:
                    $aksik = "";
            }
            //Interpretasi Hasil
            switch ($data['b_hasil']) {
                case "0":
                    $hasilb = "NR";
                    break;
                case "1":
                    $hasilb = "R";
                    break;
                case "2":
                    $hasilb = "GZ";
                    break;
                default:
                    $hasilb = "";
            }
            switch ($data['c_hasil']) {
                case "0":
                    $hasilc = "NR";
                    break;
                case "1":
                    $hasilc = "R";
                    break;
                case "2":
                    $hasilc = "GZ";
                    break;
                default:
                    $hasilc = "";
            }
            switch ($data['i_hasil']) {
                case "0":
                    $hasili = "NR";
                    break;
                case "1":
                    $hasili = "R";
                    break;
                case "2":
                    $hasili = "GZ";
                    break;
                default:
                    $hasili = "";
            }
            switch ($data['s_hasil']) {
                case "0":
                    $hasils = "NR";
                    break;
                case "1":
                    $hasils = "R";
                    break;
                case "2":
                    $hasils = "GZ";
                    break;
                default:
                    $hasils = "";
            }
        ?>
            <tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
                <td><?= $no . '.' ?></td>
                <td><?= $data['id_tes'] ?></td>
                <? if ($hasilb == "") { ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <? } else { ?>
                    <td><?= $data['b_abs'] ?></td>
                    <? if ($hasilb == "NR") { ?><td align="center">
                            <font color="black"><?= $hasilb ?></font>
                        </td><? } else { ?><td align="center">
                            <font color="red"><?= $hasilb ?></font>
                        </td><? } ?>
                    <td><?= $data['b_lot_reag'] ?></td>
                    <td><?= $data['b_ed_reag'] ?></td>
                    <td><?= $data['b_run_time'] ?></td>
                    <td><?= $data['b_ket_tes'] ?></td>
                <? } ?>
                <? if ($hasilc == "") { ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <? } else { ?>
                    <td><?= $data['c_abs'] ?></td>
                    <? if ($hasilc == "NR") { ?><td align="center">
                            <font color="black"><?= $hasilc ?></font>
                        </td><? } else { ?><td align="center">
                            <font color="red"><?= $hasilc ?></font>
                        </td><? } ?>
                    <td><?= $data['c_lot_reag'] ?></td>
                    <td><?= $data['c_ed_reag'] ?></td>
                    <td><?= $data['c_run_time'] ?></td>
                    <td><?= $data['c_ket_tes'] ?></td>
                <? } ?>
                <? if ($hasili == "") { ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <? } else { ?>
                    <td><?= $data['i_abs'] ?></td>
                    <? if ($hasili == "NR") { ?><td align="center">
                            <font color="black"><?= $hasili ?></font>
                        </td><? } else { ?><td align="center">
                            <font color="red"><?= $hasili ?></font>
                        </td><? } ?>
                    <td><?= $data['i_lot_reag'] ?></td>
                    <td><?= $data['i_ed_reag'] ?></td>
                    <td><?= $data['i_run_time'] ?></td>
                    <td><?= $data['i_ket_tes'] ?></td>
                <? } ?>
                <? if ($hasils == "") { ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <? } else { ?>
                    <td><?= $data['s_abs'] ?></td>
                    <? if ($hasils == "NR") { ?><td align="center">
                            <font color="black"><?= $hasils ?></font>
                        </td><? } else { ?><td align="center">
                            <font color="red"><?= $hasils ?></font>
                        </td><? } ?>
                    <td><?= $data['s_lot_reag'] ?></td>
                    <td><?= $data['s_ed_reag'] ?></td>
                    <td><?= $data['s_run_time'] ?></td>
                    <td><?= $data['s_ket_tes'] ?></td>
                <? } ?>
                <td><?= $statuskantong ?></td>
                <td><?= $aksik ?></td>
            </tr>
        <? } ?>
        <tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td colspan="5" align="left">Instrument</td>
            <td colspan="7" align="left"><?= $instrument ?></td>
            <td rowspan="6" colspan="16" align="left">
                <b>Catatan :</b>
                <ol>
                    <li>Status Kantong adalah status kantong pada saat konfirmasi dilakukan (bukan status kantong saat ini)</li>
                    <li>Kolom "Konfirm" adalah aksi dari user saat proses konfirmasi dilakukan</li>
                    <li>Kolom ED Reagen sesuai dengan input Reagen oleh petugas pada saat konfirmasi </li>
                    <li>Kolom Hasil : NR (Non Reaktif); R (Reaktif) GZ (GrayZone) </li>
                </ol>
            </td>
        </tr>
        <?
        $opr_arc = mysql_fetch_assoc(mysql_query("select nama_lengkap from user where id_user='$operator'"));
        if ($opr_arc) {
            $operator_arc = $opr_arc[nama_lengkap];
        } else {
            $operator_arc = $operator;
        }
        $konfr = mysql_fetch_assoc(mysql_query("select nama_lengkap from user where id_user='$konfirmer'"));
        if ($konfr) {
            $konfirmer_arc = $konfr[nama_lengkap];
        } else {
            $konfirmer_arc = $konfirmer;
        }
        $sah_arc = mysql_fetch_assoc(mysql_query("select nama_lengkap from user where id_user='$pengesah'"));
        if ($sah_arc) {
            $pengesah_arc = $sah_arc[nama_lengkap];
        } else {
            $pengesah_arc = $pengesah;
        }
        ?>
        <tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td colspan="5" align="left">Nomor & Waktu Konfirmasi</td>
            <td colspan="7" align="left"><?= $nomor . ', ' . $waktuKonfirmiasi ?></td>
        </tr>
        <tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td colspan="5" align="left">Operator Architect</td>
            <td colspan="7" align="left"><?= $operator_arc ?></td>
        </tr>
        <tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td colspan="5" align="left">Petugas Konfirmasi</td>
            <td colspan="7" align="left"><?= $konfirmer_arc ?></td>
        </tr>
        <tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td colspan="5" align="left">Petugas Pengesahan</td>
            <td colspan="7" align="left"><?= $pengesah_arc ?></td>
        </tr>
    </table>

    <a href="#atas" class="swn_button_blue">Ke Atas</a><a name="bawah" id="bawah">
        <a href="architec/imltd_rpt_konfirm1.php?notrans=<?= $notrans ?>" class="swn_button_blue">Cetak</a>
        <a href="pmiimltd.php?module=arc_data" class="swn_button_blue">Kembali ke list data</a>
        <a href="pmiimltd.php?module=import_arc2000" class="swn_button_blue">Kembali ke Awal</a>
</body>

</html>