<?
    /***********************************************
     * Author 	: suwena 
     * Date 	: 9 Juni 2019
     ***********************************************/
session_start();
require_once('config/db_connect.php');
$tahun      = date("Y");
$bulan      = date('m');
$v_tahun    = $_GET['t'];
$v_bulan    = $_GET['b'];
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
//=================================TOTAL PERMINTAAN=======================================
$sql="SELECT
                                h.`rs` as rskode,
                                SUM(CASE WHEN h.`bagian`='ANAK' THEN t.`Jumlah` ELSE  0 END ) as anak,
                                SUM(CASE WHEN h.`bagian`='BEDAH' THEN t.`Jumlah` ELSE  0 END ) as bedah,
                                SUM(CASE WHEN h.`bagian`='DALAM' THEN t.`Jumlah` ELSE  0 END ) as interna,
                                SUM(CASE WHEN h.`bagian`='KANDUNGAN' THEN t.`Jumlah` ELSE  0 END ) as keb,
                                SUM(CASE WHEN h.`bagian`='HD' THEN t.`Jumlah` ELSE  0 END ) as tht,
				SUM(CASE WHEN h.`bagian`='HND' THEN t.`Jumlah` ELSE  0 END ) as hnd,
				SUM(CASE WHEN h.`bagian`='ICU' THEN t.`Jumlah` ELSE  0 END ) as icu,
                                SUM(CASE WHEN h.`bagian`='Lain - lain' THEN t.`Jumlah` ELSE  0 END ) as ll
                                FROM `htranspermintaan` h
                                left join `pasien` p on p.`no_rm`=h.`no_rm`
                                left join `dtranspermintaan` t on t.`NoForm`=h.`noform` WHERE
                                month(h.`tgl_register`)='$v_bulan' and year(h.`tgl_register`)='$v_tahun'";

//echo "$sql";
$qraw=mysql_query($sql);
			$jml_anak=0;
                        $jml_bedah=0;
                        $jml_interna=0;
                        $jml_keb=0;
                        $jml_tht=0;
			$jml_hnd=0;
			$jml_icu=0;
                        $jml_ll=0;
                        $jml_row=0;

$qraw1=mysql_fetch_assoc($qraw);
$jml_anak=$jml_anak + $qraw1['anak'];
$jml_bedah=$jml_bedah + $qraw1['bedah'];
$jml_interna=$jml_interna + $qraw1['interna'];
$jml_keb=$jml_keb + $qraw1['keb'];
$jml_tht=$jml_tht + $qraw1['tht'];
$jml_hnd=$jml_hnd + $qraw1['hnd'];
$jml_icu=$jml_icu + $qraw1['icu'];
$jml_ll=$jml_ll + $qraw1['ll'] + $qraw1['tht'] + $qraw1['hnd'] + $qraw1['icu'];
$row_ttl= $jml_anak + $jml_bedah + $jml_interna + $jml_keb + $jml_ll;
//=========================jumlah terpenuhi==============================================

$sql1="SELECT
                                h.`rs` as rskode,
                                SUM(CASE WHEN h.`bagian`='ANAK' THEN 1 ELSE  0 END ) as anak1,
                                SUM(CASE WHEN h.`bagian`='BEDAH' THEN 1 ELSE  0 END ) as bedah1,
                                SUM(CASE WHEN h.`bagian`='DALAM' THEN 1 ELSE  0 END ) as interna1,
                                SUM(CASE WHEN h.`bagian`='KANDUNGAN' THEN 1 ELSE  0 END ) as keb1,
                                SUM(CASE WHEN h.`bagian`='HD' THEN 1 ELSE  0 END ) as tht1,
				SUM(CASE WHEN h.`bagian`='HND' THEN 1 ELSE  0 END ) as hnd1,
				SUM(CASE WHEN h.`bagian`='ICU' THEN 1 ELSE  0 END ) as icu1,
                                SUM(CASE WHEN h.`bagian`='Lain - lain' THEN 1 ELSE  0 END ) as ll1
                                FROM `htranspermintaan` h
                                inner join `pasien` p on p.`no_rm`=h.`no_rm`
                                inner join `dtransaksipermintaan` d on d.`NoForm`=h.`noform` WHERE
                                month(d.`tgl`)='$v_bulan' and year(d.`tgl`)='$v_tahun'";
//echo "$sql";
$qraw1=mysql_query($sql1);
			$jml_anak1=0;
                        $jml_bedah1=0;
                        $jml_interna1=0;
                        $jml_keb1=0;
                        $jml_tht1=0;
			$jml_hnd1=0;
			$jml_icu1=0;
                        $jml_ll1=0;
$qraw4=mysql_fetch_assoc($qraw1);
$jml_anak1=$jml_anak1 + $qraw4['anak1'];
                        $jml_bedah1=$jml_bedah1 + $qraw4['bedah1'];
                        $jml_interna1=$jml_interna1 + $qraw4['interna1'];
                        $jml_keb1=$jml_keb1 + $qraw4['keb1'];
                        $jml_tht1=$jml_tht1 + $qraw4['tht1'];
			$jml_hnd1=$jml_hnd1 + $qraw4['hnd1'];
			$jml_icu1=$jml_icu1 + $qraw4['icu1'];
                        $jml_ll1=$jml_ll1 + $qraw4['ll1'] + $qraw4['tht1'] + $qraw4['hnd1'] + $qraw4['icu1'];
                        $row_ttl1 = $jml_anak1 + $jml_bedah1 + $jml_interna1 + $jml_keb1 + $jml_ll1;
//==========================JUMLAH TERPAKAI==================================================

$sql2="SELECT
                            h.`rs` as rskode,
                            SUM(CASE WHEN h.`bagian`='ANAK' THEN 1 ELSE  0 END ) as anak2,
                            SUM(CASE WHEN h.`bagian`='BEDAH' THEN 1 ELSE  0 END ) as bedah2,
                            SUM(CASE WHEN h.`bagian`='DALAM' THEN 1 ELSE  0 END ) as interna2,
                            SUM(CASE WHEN h.`bagian`='KANDUNGAN' THEN 1 ELSE  0 END ) as keb2,
                            SUM(CASE WHEN h.`bagian`='HD' THEN 1 ELSE  0 END ) as tht2,
			    SUM(CASE WHEN h.`bagian`='HND' THEN 1 ELSE  0 END ) as hnd2,
			    SUM(CASE WHEN h.`bagian`='ICU' THEN 1 ELSE  0 END ) as icu2,
                            SUM(CASE WHEN h.`bagian`='Lain - lain' THEN 1 ELSE  0 END ) as ll2
                            FROM `htranspermintaan` h
                            inner join `pasien` p on p.`no_rm`=h.`no_rm`
                            inner join `dtransaksipermintaan` d on d.`NoForm`=h.`noform` WHERE
                            month(d.`tgl_keluar`)='$v_bulan' and year(d.`tgl_keluar`)='$v_tahun' AND d.`Status`='0'";
//echo "$sql";
$qraw2=mysql_query($sql2);
			$jml_anak2=0;
                        $jml_bedah2=0;
                        $jml_interna2=0;
                        $jml_keb2=0;
                        $jml_tht2=0;
			$jml_hnd2=0;
			$jml_icu2=0;
                        $jml_ll2=0;

$qraw3=mysql_fetch_assoc($qraw2);
$jml_anak2=$jml_anak2 + $qraw3['anak2'];
                        $jml_bedah2=$jml_bedah2 + $qraw3['bedah2'];
                        $jml_interna2=$jml_interna2 + $qraw3['interna2'];
                        $jml_keb2=$jml_keb2 + $qraw3['keb2'];
                        $jml_tht2=$jml_tht2 + $qraw3['tht2'];
			$jml_hnd2=$jml_hnd2 + $qraw3['hnd2'];
			$jml_icu2=$jml_icu2 + $qraw3['icu2'];
                        $jml_ll2=$jml_ll2 + $qraw3['ll2'] + $qraw3['tht2'] + $qraw3['hnd2'] + $qraw3['icu2'];
                        $row_ttl2 = $jml_anak2 + $jml_bedah2 + $jml_interna2 + $jml_keb2 + $jml_ll2;
//PEMENUHAN
$penuhi_anak=$jml_anak1/$jml_anak*100;
$penuhi_bedah=$jml_bedah1/$jml_bedah*100;
$penuhi_interna=$jml_interna1/$jml_interna*100;
$penuhi_keb=$jml_keb1/$jml_keb*100;
$penuhi_ll=$jml_ll1/$jml_ll*100;
$penuhi_ttl=$row_ttl1/$row_ttl*100;

//PEMBULATAN
$persen_penuhi_anak=round($penuhi_anak,1);
$persen_penuhi_bedah=round($penuhi_bedah,1);
$persen_penuhi_interna=round($penuhi_interna,1);
$persen_penuhi_keb=round($penuhi_keb,1);
$persen_penuhi_ll=round($penuhi_ll,1);
$persen_penuhi_ttl=round($penuhi_ttl,1);

//PERSEN PEMAKAIAN
$pakai_anak=$jml_anak2/$jml_anak*100;
$pakai_bedah=$jml_bedah2/$jml_bedah*100;
$pakai_interna=$jml_interna2/$jml_interna*100;
$pakai_keb=$jml_keb2/$jml_keb*100;
$pakai_ll=$jml_ll2/$jml_ll*100;
$pakai_ttl=$row_ttl2/$row_ttl*100;

// PEMBULATAN
$persen_pakai_anak=round($pakai_anak,1);
$persen_pakai_bedah=round($pakai_bedah,1);
$persen_pakai_interna=round($pakai_interna,1);
$persen_pakai_keb=round($pakai_keb,1);
$persen_pakai_ll=round($pakai_ll,1);
$persen_pakai_ttl=round($pakai_ttl,1);
//============================JUMLAH_RS_YANG_DILAYANI=======================================================
$rs_dlm=mysql_query("SELECT COUNT(DISTINCT `rs`) AS rsd FROM `htranspermintaan` WHERE `wilayah`='0' AND
                        month(`tgl_register`)='$v_bulan' and year(`tgl_register`)='$v_tahun'");
$dlm_kota=mysql_fetch_assoc($rs_dlm);
$hasil_dlm_kota=0;
$hasil_dlm_kota= $hasil_dlm_kota + $dlm_kota['rsd'];

$rs_luar=mysql_query("SELECT COUNT(DISTINCT `rs`) AS rsl FROM `htranspermintaan` WHERE `wilayah`='1' AND
                        month(`tgl_register`)='$v_bulan' and year(`tgl_register`)='$v_tahun'");
$luar_kota=mysql_fetch_assoc($rs_luar);
$hasil_luar_kota=0;
$hasil_luar_kota= $hasil_luar_kota + $luar_kota['rsl'];

$total_rs=$hasil_dlm_kota + $hasil_luar_kota;
//====================DISTRIBUSI KOMPONEN DARAH================================
$cari=mysql_query("SELECT
                                        SUM(CASE WHEN k.`status`='0' THEN 1 ELSE 0 END) as bdrs
                                        FROM `kirimbdrs` k
                                        inner join `bdrs` b on b.`kode`=k.`bdrs`
                                        inner join `stokkantong` s on s.`noKantong`=k.`nokantong`
                                        inner join `user` u on u.`id_user`= k.`petugas`
                                        WHERE
                                        k.`status`='0' and `tglkembali` is null and `tglbatal` is null and
                                        month(k.`tgl`)='$v_bulan' AND year(k.`tgl`)='$v_tahun'");
$hasil_cari=mysql_fetch_assoc($cari);
$bdrs=0;
$bdrs=$bdrs + $hasil_cari['bdrs'];

$cari1=mysql_query("SELECT COUNT(DISTINCT `nokantong`) AS udd FROM `kirimudd` WHERE `status`='0' AND month(`tgl`)='$v_bulan' and year (`tgl`)='$v_tahun' ");
$hasil_cari1=mysql_fetch_assoc($cari1);
$udd=0;
$udd=$udd + $hasil_cari1['udd'];
$total_kirim = $bdrs + $udd;
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
            LAPORAN PERMINTAAN DARAH<br>
            <?php echo $dt_um['u_nama'];?><br>
            <?php echo 'BULAN '.$namaperiode.' '.$v_tahun;?>
        </td>
    </tr>
</table>
<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br><br>E.1. JUMLAH PERMINTAAN DARAH
</div>


<table class="list" border="1" cellpadding="3" cellspacing="2" width="100%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th rowspan="2" align="center">NO.</th>
        <th rowspan="2" align="center">BAGIAN PERAWATAN DI RS</th>
        <th rowspan="2" align="center">Jumlah Total Permintaan Darah (kantong)</th>
        <th rowspan="2" align="center">Jumlah Permintaan Darah Yang Dapat Dipenuhi (kantong)</th>
        <th rowspan="2" align="center">Jumlah Permintaan Darah Yang Terpakai (kantong)</th>
        <th colspan="2" align="center">PERSENTASE(%)</th>
    </tr>
    <tr>
        <th align="center">PEMENUHAN</th>
        <th align="center">TERPAKAI</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="center">1</td>
        <td>Anak</td>
        <td align="center"><?=$jml_anak;?></td>
        <td align="center"><?=$jml_anak1;?></td>
        <td align="center"><?=$jml_anak2;?></td>
        <td align="center"><?=$persen_penuhi_anak;?>%</td>
        <td align="center"><?=$persen_pakai_anak;?>%</td>
    </tr>
    <tr>
        <td align="center">2</td>
        <td>Bedah</td>
        <td align="center"><?=$jml_bedah;?></td>
        <td align="center"><?=$jml_bedah1;?></td>
        <td align="center"><?=$jml_bedah2;?></td>
        <td align="center"><?=$persen_penuhi_bedah;?>%</td>
        <td align="center"><?=$persen_pakai_bedah;?>%</td>
    </tr>
    <tr>
        <td align="center">3</td>
        <td>Penyakit Dalam</td>
        <td align="center"><?=$jml_interna;?></td>
        <td align="center"><?=$jml_interna1;?></td>
        <td align="center"><?=$jml_interna2;?></td>
        <td align="center"><?=$persen_penuhi_interna;?>%</td>
        <td align="center"><?=$persen_pakai_interna;?>%</td>
    </tr>
    <tr>
        <td align="center">4</td>
        <td>Kandungan</td>
        <td align="center"><?=$jml_keb;?></td>
        <td align="center"><?=$jml_keb1;?></td>
        <td align="center"><?=$jml_keb2;?></td>
        <td align="center"><?=$persen_penuhi_keb;?>%</td>
        <td align="center"><?=$persen_pakai_keb;?>%</td>
    </tr>
    <tr>
        <td align="center">5</td>
        <td>Lain-lain</td>
        <td align="center"><?=$jml_ll;?></td>
        <td align="center"><?=$jml_ll1;?></td>
        <td align="center"><?=$jml_ll2;?></td>
        <td align="center"><?=$persen_penuhi_ll;?>%</td>
        <td align="center"><?=$persen_pakai_ll;?>%</td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2" align="center"> TOTAL </th>
        <th align="center"> <?=$row_ttl;?> </th>
        <th align="center"> <?=$row_ttl1;?> </th>
        <th align="center"> <?=$row_ttl2;?> </th>
        <th align="center"> <?=$persen_penuhi_ttl;?>% </th>
        <th align="center"> <?=$persen_pakai_ttl;?>%</th>
    </tr>
    </tfoot>
</table>

<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br><br>E.2. JUMLAH RS YANG DILAYANI
</div>
<table class="list" border="1" cellpadding="3" cellspacing="2" width="50%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center" width="10px;">NO.</th>
        <th align="center">JENIS RS YANG DILAYANI BERDASARKAN LOKASI</th>
        <th align="center"  width="75px;">JUMLAH</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="center">1</td>
        <td align="left">Dalam Kota</td>
        <td align="center"><?=$hasil_dlm_kota;?></td>
    </tr>
    <tr>
        <td align="center">2</td>
        <td align="left">Luar Kota</td>
        <td align="center"><?=$hasil_luar_kota;?></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2" align="center"> JUMLAH </th>
        <th align="center"><?=$total_rs;?></th>
    </tr>
    </tfoot>
</table>

<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br><br>E.3. DISTRIBUSI KOMPONEN DARAH
</div>
<table class="list" border="1" cellpadding="3" cellspacing="2" width="50%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center" width="10px;">NO.</th>
        <th align="center">TUJUAN DISTRIBUSI</th>
        <th align="center"  width="75px;">JUMLAH</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="center">1</td>
        <td align="left">BDRS</td>
        <td align="center"><?=$bdrs;?></td>
    </tr>
    <tr>
        <td align="center">2</td>
        <td align="left">UDD lain</td>
        <td align="center"><?=$udd;?></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2" align="center"> JUMLAH </th>
        <th align="center"><?=$total_kirim;?></th>
    </tr>
    </tfoot>
</table>

<br><br>

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
<? echo "<meta http-equiv='refresh' content='2;url=pmitatausaha.php?module=permintaan&t=$v_tahun&b=$v_bulan'";?>
</body>
