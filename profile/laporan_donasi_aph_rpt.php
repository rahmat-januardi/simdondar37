<?
    /***********************************************
     * Author 	: suwena 
     * Date 	: 9 Juni 2019
     ***********************************************/
session_start();
require_once('config/db_connect.php');
$tahun      = date("Y");
$bulan      = date('m');
$v_tahun    = $_GET['thn'];
$v_bulan    = $_GET['bln'];
if (empty($v_tahun)){$v_tahun=$tahun;}
if (empty($v_bulan)){$v_bulan=$bulan;}

$utd            = mysql_fetch_assoc(mysql_query("select * from `utd` where `aktif`='1'"));
$dt_um          = "SELECT `u_id`, `u_tahun`, `u_id_udd`, `u_nama`, `u_alamat`, `u_kab`, `u_prov`, `u_kpos`, `u_telp`,
                   `u_email`, `u_ka_nama`, `u_ka_hp`, `u_ka_email`,
                   `u_komite_mdk`, `u_distr_terbuka`, `u_distr_cold_chain`,
                   `u_jml_dokter_kompeten`, `u_ptgs_komptn`, `u_inform_c`, `u_lbr_mon_td`, `u_jml_pasien_td`, `u_jml_pasien_rtd`,
                   `u_periksa_kgd`, `u_kgd_auto`, `u_kgd_semi`, `u_kgd_manual`, `u_periksa_abs`, `u_abs_auto`, `u_abs_semi`,
                   `u_abs_manual`, `u_periksa_iab`, `u_iab_auto`, `u_iab_semi`, `u_iab_manual`, `u_periksa_cross`, `u_cross_auto`,
                   `u_cross_semi`, `u_cross_manual`,day(`u_tgl_laporan`) as tgl_lap, month(`u_tgl_laporan`) as bln_lap, year(`u_tgl_laporan`) as thn_lap
                   FROM `rpt_data_umum` WHERE `u_id_udd`='$utd[id]'";
$dt_um          = mysql_fetch_assoc(mysql_query($dt_um));
switch($dt_um['bln_lap']){
    case '1'  : $bln_lap='Januari';break;
    case '2'  : $bln_lap='Februari';break;
    case '3'  : $bln_lap='Maret';break;
    case '4'  : $bln_lap='April';break;
    case '5'  : $bln_lap='Mei';break;
    case '6'  : $bln_lap='Juni';break;
    case '7'  : $bln_lap='Juli';break;
    case '8'  : $bln_lap='Agustus';break;
    case '9'  : $bln_lap='September';break;
    case '10' : $bln_lap='Oktober';break;
    case '11' : $bln_lap='Nopember';break;
    case '12' : $bln_lap='Desember';break;
}

switch ($v_bulan){
    case '1' : $namaperiode="JANUARI";break;
    case '2' : $namaperiode="FEBRUARI";break;
    case '3' : $namaperiode="MARET";break;
    case '4' : $namaperiode="APRIL";break;
    case '5' : $namaperiode="MEI";break;
    case '6' : $namaperiode="JUNI";break;
    case '7' : $namaperiode="JULI";break;
    case '8' : $namaperiode="AGUSTUS";break;
    case '9' : $namaperiode="SEPTEMBER";break;
    case '10' :$namaperiode="OKTOBER";break;
    case '11' :$namaperiode="NOVEMBER";break;
    case '12' :$namaperiode="DESEMBER";break;
}
?>

<title>Laporan UDD</title>
<head>
<style type="text/css" media="print">
    @page
    {
        size: landscape;
        margin-bottom: 20mm;
        margin-left: 0mm;
        margin-right: 0mm;
        margin-top: 15mm;
        header : {display: none !important;}
    }
    html
    {
        background-color: #ffffff;
        margin: 3px;  /* this affects the margin on the html before sending to printer */
    }
    body
    {
        border: solid 0px #ffffff ;
        margin: 0mm 15mm 10mm 10mm; /* margin you want for the content */
    }
    table th td {text-align: left;}
</style>
</head>
<body onload="window.print()">
<table class="list" border="0" cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr>
        <td style="height: 40px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            LAPORAN DONASI DARAH APHERESIS<br>
            <?php echo $dt_um['u_nama'];?><br>
            <?php echo 'BULAN '.$namaperiode.' '.$v_tahun;?>
        </td>
    </tr>
</table>
<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br>A.2.a. DONASI (Jumlah kantong darah yang didapatkan dari pendonor darah)
</div>
<?php
$q_dnr="SELECT
                                sum(case when (h.`JenisDonor`='0' and h.`tempat`='0' and h.`donorbaru`='0') then 1 else 0 end ) as dg_ds_br,
                                sum(case when (h.`JenisDonor`='0' and h.`tempat`='0' and h.`donorbaru`='1') then 1 else 0 end ) as dg_ds_ul,
                                sum(case when (h.`JenisDonor`='1' and h.`tempat`='0') then 1 else 0 end ) as dg_dp,
                                sum(case when (h.`tempat` in ('2','3','M') and h.`donorbaru`='0') then 1 else 0 end ) as mu_ds_br,
                                sum(case when (h.`tempat` in ('2','3','M') and h.`donorbaru`='1') then 1 else 0 end ) as mu_ds_ul,
                                sum(case when (h.`jk`='0') then 1 else 0 end ) as lk,
                                sum(case when (h.`jk`='1') then 1 else 0 end ) as pr,
                                sum(case when (h.`umur`<=17) then 1 else 0 end ) as u17,
                                sum(case when (h.`umur`>=18 and h.`umur`<=24) then 1 else 0 end ) as u1824,
                                sum(case when (h.`umur`>=25 and h.`umur`<=44) then 1 else 0 end ) as u2544,
                                sum(case when (h.`umur`>=45 and h.`umur`<=64) then 1 else 0 end ) as u4564,
                                sum(case when (h.`umur`>=65) then 1 else 0 end ) as u65,
                                sum(case when (h.`gol_darah`='A' and h.`rhesus`='+') then 1 else 0 end ) as a_pos,
                                sum(case when (h.`gol_darah`='B' and h.`rhesus`='+') then 1 else 0 end ) as b_pos,
                                sum(case when (h.`gol_darah`='O' and h.`rhesus`='+') then 1 else 0 end ) as o_pos,
                                sum(case when (h.`gol_darah`='AB' and h.`rhesus`='+') then 1 else 0 end ) as ab_pos,
                                sum(case when (h.`gol_darah`='A' and h.`rhesus`='-') then 1 else 0 end ) as a_neg,
                                sum(case when (h.`gol_darah`='B' and h.`rhesus`='-') then 1 else 0 end ) as b_neg,
                                sum(case when (h.`gol_darah`='O' and h.`rhesus`='-') then 1 else 0 end ) as o_neg,
                                sum(case when (h.`gol_darah`='AB' and h.`rhesus`='-') then 1 else 0 end ) as ab_neg,
                                sum(case when (LENGTH(h.`KodePendonor`)>0) then 1 else 0 end ) as total
                                FROM `htransaksi` h inner join `pendonor` p on p.`Kode`=h.`KodePendonor`
                                WHERE
                                year(h.`Tgl`)='$v_tahun' and month(h.`Tgl`)='$v_bulan' and h.`pengambilan`='0' and h.`caraAmbil` in ('1','2','3','4','5')";
$q_dnr=mysql_fetch_assoc(mysql_query($q_dnr));
?>
<table class="list" border="1" cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center" colspan="4" style="vertical-align: middle;">Jml Donasi Dalam Gedung yg berasal dari</th>
        <th align="center" colspan="2" style="vertical-align: middle;">Jml Donasi Sukarela dari Kegiatan MU</th>
        <th align="center" rowspan="3" style="vertical-align: middle;">Jml Total Donasi</th>
        <th align="center" colspan="2" style="vertical-align: middle;">Jml Donasi Darah Menurut Jenis Kelamin</th>
        <th align="center" colspan="5" style="vertical-align: middle;">Jml Donasi Darah Menurut Kelompok Umur</th>
        <th align="center" colspan="8" style="vertical-align: middle;">Jml Donasi Darah Menurut Golongan dan Rhesus Darahr</th>
    </tr>
    <tr>
        <th align="center" colspan="2" style="vertical-align: middle;">Pendonor Sukarela</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">Pendonor Pengganti</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">Pendonor Bayaran</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">Baru</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">Ulang</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">Laki-laki</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">Wanita</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">17 Tahun</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">18-24 Tahun</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">25-44 Tahun</th>
        <th align="center" rowspan="2" style="vertical-align: middle;">45-64 Tahun</th>
        <th align="center" rowspan="2" style="vertical-align: middle;"> <u>></u>65 Tahun </th>
        <th align="center" colspan="2" style="vertical-align: middle;">A</th>
        <th align="center" colspan="2" style="vertical-align: middle;">B</th>
        <th align="center" colspan="2" style="vertical-align: middle;">O</th>
        <th align="center" colspan="2" style="vertical-align: middle;">AB</th>
    </tr>
    <tr>
        <th align="center" style="vertical-align: middle;">Baru</th>
        <th align="center" style="vertical-align: middle;">Ulang</th>
        <th align="center" style="vertical-align: middle;">Pos</th>
        <th align="center" style="vertical-align: middle;">Neg</th>
        <th align="center" style="vertical-align: middle;">Pos</th>
        <th align="center" style="vertical-align: middle;">Neg</th>
        <th align="center" style="vertical-align: middle;">Pos</th>
        <th align="center" style="vertical-align: middle;">Neg</th>
        <th align="center" style="vertical-align: middle;">Pos</th>
        <th align="center" style="vertical-align: middle;">Neg</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="center">&nbsp;<br><?php echo $q_dnr['dg_ds_br'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['dg_ds_ul'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['dg_dp'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br>0<br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['mu_ds_br'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['mu_ds_ul'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['total'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['lk'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['pr'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['u17'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['u1824'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['u2544'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['u4564'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['u65'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['a_pos'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['a_neg'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['b_pos'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['b_neg'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['o_pos'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['o_neg'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['ab_pos'];?><br>&nbsp;</td>
        <td align="center">&nbsp;<br><?php echo $q_dnr['ab_neg'];?><br>&nbsp;</td>
    </tr>
    </tbody>
</table>

<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br>A.2.b. JUMLAH DONASI BERDASARKAN ALASAN PENDONOR DITOLAK
</div>
<?php
$$q_btl="SELECT
                                count(case when (h.`pengambilan`='2' and h.`ketBatal`='12') then 1 END) AS a7_gagal_aftap,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='0') then 1 END) AS a4_tensi_rendah,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='1') then 1 END) AS a4_tensi_tinggi,
                                count(case when (h.`pengambilan`='1' and (h.`ketBatal`='2' or h.`ketBatal`='3')) then 1 END) AS a3_hb_rendah,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='4') then 1 END) AS a4_hb_tinggi,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='5') then 1 END) AS a1_bb_kurang,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='6') then 1 END) AS a4_obat,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='7') then 1 END) AS a6_bepergian,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='8') then 1 END) AS a4_medis,
                                count(case when (h.`pengambilan`='1' and h.`ketBatal`='9') then 1 END) AS a5_prilaku,
                                count(case when ((h.`ketBatal`='10' or h.`ketBatal`='') and h.`pengambilan`='1') then 1 END) AS a7_lain_lain
                                FROM `htransaksi` h inner join `pendonor` p on p.`Kode`=h.`KodePendonor`
                                where year(h.`Tgl`)='$v_tahun' and month(`Tgl`)='$v_bulan' and (h.`caraAmbil` IN ('1','2','3','4','5'))";
$q_btl=mysql_fetch_assoc(mysql_query($q_btl));
?>
<table class="list" border="1" cellpadding="2" cellspacing="2" width="80%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center" style="vertical-align: middle;">No</th>
        <th align="center" style="vertical-align: middle;">Alasan Penolakan</th>
        <th align="center" style="vertical-align: middle;">Jumlah</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="right">1.</td><td align="left">Berat Badan Kurang (< 45 Kg)</td>
        <td align="center"><?php echo $q_btl['a1_bb_kurang'];?></td>
    </tr>
    <tr>
        <td align="right">2.</td><td align="left">Usia < 17 tahun</td>
        <td align="center">0</td>
    </tr>
    <tr>
        <td align="right">3.</td><td align="left">Kadar Hb Rendah ( < 12,5 Gr/dl)</td>
        <td align="center"><?php echo $q_btl['a3_hb_rendah'];?></td>
    </tr>
    <tr>
        <td align="right">4.</td><td align="left">Riwayat Medis Lain (Hipertensi, Hipotensi, Minum Obat, Pasca Operasi, Kadar Hb Tinggi > 17 Gr/dl)</td>
        <td align="center"><?php echo $q_btl['a4_tensi_rendah']+$q_btl['a4_tensi_tinggi']+$q_btl['a4_hb_tinggi']+$q_btl['a4_obat']+$q_btl['a4_medis'];?></td>
    </tr>
    <tr>
        <td align="right">5.</td><td align="left">Perilaku Beresiko Tinggi (Homo Seksual, Tato/Tindik Kurang dari 6 Bulan, Sex Bebas, Penasun, Napi)</td>
        <td align="center"><?php echo $q_btl['a5_prilaku'];?></td>
    </tr>
    <tr>
        <td align="right">6.</td><td align="left">Riwayat Bepergian ( Daerah Endemis Malaria, Negara dengan Kasus HIV Tinggi, Negara Dengan Kasus Sapi Gila)</td>
        <td align="center"><?php echo $q_btl['a6_bepergian'];?></td>
    </tr>
    <tr>
        <td align="right">7.</td><td align="left">Alasan Lain (Gagal pengambilan darah)</td>
        <td align="center"><?php echo $q_btl['a7_gagal_aftap']+$q_btl['a7_lain_lain'];?></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th align="center" colspan="2">Jumlah</th>
        <th align="center">
            <?php echo
                $q_btl['a7_gagal_aftap']+
                $q_btl['a4_tensi_rendah']+
                $q_btl['a4_tensi_tinggi']+
                $q_btl['a3_hb_rendah']+
                $q_btl['a4_hb_tinggi']+
                $q_btl['a1_bb_kurang']+
                $q_btl['a4_obat']+
                $q_btl['a6_bepergian']+
                $q_btl['a4_medis']+
                $q_btl['a5_prilaku']+
                $q_btl['a7_lain_lain'];
            ?></th>
    </tr>
    </tfoot>
</table>


<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br>A.2.c. TERIMA DONASI DARI UDD LAIN
</div>
<?php
$terima="SELECT t.`udd`, u.`nama`, count(t.`nokantong`) as jumlah
                                FROM `terimaudd` t inner join `utd` u on u.`id`=t.`udd`
                                INNER JOIN `stokkantong` s on s.`noKantong`=t.`nokantong`
                                WHERE YEAR(t.`tgl`)='$v_tahun' and MONTH(t.`Tgl`)='$v_bulan' AND s.`produk` like '%aph%'";
$terima=mysql_query($terima);
?>
<table class="list" border="1" cellpadding="2" cellspacing="2" width="60%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center" style="vertical-align: middle">No</th>
        <th align="center" style="vertical-align: middle">Nama UDD</th>
        <th align="center" style="vertical-align: middle">Jumlah</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $no=0;
    $total=0;
    while ($t_udd=mysql_fetch_assoc($terima)){
        $no++;
	$total1=$total+$t_udd['jumlah'];
        echo '<tr>';
        echo '<td align="right">'.$no.'.</td>';
        echo '<td align="left">'.$t_udd[nama].'</td>';
        echo '<td align="center">'.$t_udd[jumlah].'</td>';
        echo '</tr>';
    }
    if ($no==0){
        echo '<tr>';
        echo '<td align="right">1.</td>';
        echo '<td align="left">-</td>';
        echo '<td align="center"> </td>';
        echo '</tr>';
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td></td>
        <td align="center">Jumlah</td>
        <td align="center"><?php echo $total1;?></td>
    </tr>
    </tfoot>
</table>
<br>

<table border="0" width="100%">
    <tr style="background-color: #ffffff;font-wight:none; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td width="25%"></td>
        <td width="25%"></td>
        <td width="25%" nowrap>
            <?php echo $dt_um['u_kab'].', '.$dt_um['tgl_lap'].' '.$bln_lap.' '.$dt_um['thn_lap'];?>
            <br>KEPALA <?php echo $dt_um['u_nama'].',';?><br><br><br><br>
        </td>
    </tr>
    <tr style="background-color: #ffffff;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td width="25%"></td>
        <td width="25%"></td>
        <td width="25%" nowrap>
            <?php echo $dt_um['u_ka_nama'];?>
        </td>
    </tr>
</table>
<? echo "<meta http-equiv='refresh' content='2;url=pmitatausaha.php?module=lap_donasi_aph&tahun=$v_tahun&bulan=$v_bulan'";?>
</body>
