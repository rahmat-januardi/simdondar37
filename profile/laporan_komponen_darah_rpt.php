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

$q_wb = "SELECT COUNT(DISTINCT(CASE WHEN  `Produk`='WB' THEN `noKantong` END )) AS WB1
                                                FROM `stokkantong`
                                                WHERE
                                                month(`tgl_Aftap`)='$v_bulan' and year(`tgl_Aftap`)='$v_tahun'";
$q_komponen1=mysql_query($q_wb);
$komp1=mysql_fetch_assoc($q_komponen1);

$q_komponen = "SELECT
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='WB' THEN `noKantong` END )) AS WB,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='PRC' THEN `noKantong` END )) AS PRC,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='LP' THEN `noKantong` END )) AS LP,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='FFP' THEN `noKantong` END )) AS FFP,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='TC' THEN `noKantong` END )) AS TC,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='AHF' THEN `noKantong` END )) AS AHF,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='WE' THEN `noKantong` END )) AS WE,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Leucodepleted' THEN `noKantong` END )) AS LEUCO,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='TC Aferesis' THEN `noKantong` END )) AS TC_APH,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Buffycoat Removal' THEN `noKantong` END )) AS BUF_R,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Bedside Filter Leukosit' THEN `noKantong` END )) AS BF_LEUCO,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Lab Type Filter Leukosit' THEN `noKantong` END )) AS LTF_LEUCO,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='PRC Aferesis' THEN `noKantong` END )) AS PRC_APH,
                                                COUNT(DISTINCT(CASE WHEN  `Produk`='Plasma Aferesis' THEN `noKantong` END )) AS LP_APH
                                                FROM `dpengolahan`
                                                WHERE
                                                month(`tgl`)='$v_bulan' and year(`tgl`)='$v_tahun'";
$q_komponen=mysql_query($q_komponen);
$komp=mysql_fetch_assoc($q_komponen);
$tot_produksi= $komp1['WB1']+$komp['PRC']+$komp['LP']+$komp['FFP']+$komp['TC']+$komp['AHF']+$komp['WE']+$komp['LEUCO']+ $komp['TC_APH']+$komp['BUF_R']+$komp['BF_LEUCO']+$komp['LTF_LEUCO']+$komp['PRC_APH']+$komp['LP_APH'];
//mencari data apheresis pada table htransaksi karena apheresis tidak melaewati table pengolahan
$q_aph="SELECT
                                        count(case when `caraAmbil`='1' then `NoKantong` END) as TC_Aph,
                                        count(case when `caraAmbil`='3' then `NoKantong` END) as LP_Aph,
                                        count(case when `caraAmbil`='4' then `NoKantong` END) as PRC_Aph
                                        FROM `htransaksi`
                                        WHERE year(`Tgl`)='$v_tahun' and month(`Tgl`)='$v_bulan' and  `Pengambilan`='0' and `caraAmbil` in ('1','2','3','4')";
$q_aph=mysql_fetch_assoc(mysql_query($q_aph));
$aph_tc=$q_aph['TC_Aph'];
$aph_lpc=$q_aph['LP_Aph'];
$aph_prc=$q_aph['PRC_Aph'];
$tot_produksi=$tot_produksi+$aph_tc=$q_aph['TC_Aph']+$aph_tc=$q_aph['LP_Aph']+$aph_tc=$q_aph['PRC_Aph'];
//=========================BDRS===================================================
$sqlb="SELECT
                                        k.`bdrs` as bdrskode,
                                        SUM(CASE WHEN s.`produk`='PRC' THEN 1 ELSE 0 END) as prcb,
                                        SUM(CASE WHEN s.`produk`='TC' THEN 1 ELSE 0 END) as tcb,
                                        SUM(CASE WHEN s.`produk`='TC Aferesis' THEN 1 ELSE 0 END) as aphb,
                                        SUM(CASE WHEN s.`produk`='AHF' THEN 1 ELSE 0 END) as ahfb,
                                        SUM(CASE WHEN s.`produk`='FFP' THEN 1 ELSE 0 END) as ffpb,
                                        SUM(CASE WHEN s.`produk`='WB' THEN 1 ELSE 0 END) as wbb,
                                        SUM(CASE WHEN s.`produk`='WE' THEN 1 ELSE 0 END) as web,
                                        SUM(CASE WHEN s.`produk`='LP' THEN 1 ELSE 0 END) as lpb,
                                        SUM(CASE WHEN s.`produk`='FP' THEN 1 ELSE 0 END) as fpb,
                                        SUM(CASE WHEN s.`produk`='Leucodepleted' THEN 1 ELSE 0 END) as ldb,
                                        SUM(CASE WHEN s.`produk`='Buffycoat Removal' THEN 1 ELSE 0 END) as brb,
                                        SUM(CASE WHEN s.`produk`='Bedside Filter Leukosit' THEN 1 ELSE 0 END) as bflb,
                                        SUM(CASE WHEN s.`produk`='Lab Type Filter Leukosit' THEN 1 ELSE 0 END) as ltflb,
                                        SUM(CASE WHEN s.`produk`='PRC Aferesis' THEN 1 ELSE 0 END) as prcaphb,
                                        SUM(CASE WHEN s.`produk`='Plasma Aferesis' THEN 1 ELSE 0 END) as lpaphb
                                        FROM `kirimbdrs` k
                                        inner join `bdrs` b on b.`kode`=k.`bdrs`
                                        inner join `stokkantong` s on s.`noKantong`=k.`nokantong`
                                        WHERE month(k.`tgl`)='$v_bulan' AND year(k.`tgl`)='$v_tahun' AND k.`status`='0'";

//echo "$sql";
$drop=mysql_query($sqlb);
$jml_prcb=0;
$jml_tcb=0;
$jml_tcaphb=0;
$jml_ahfb=0;
$jml_ffpb=0;
$jml_wbb=0;
$jml_web=0;
$jml_lpb=0;
$jml_fpb=0;
$jml_ldb=0;
$jml_brb=0;
$jml_bflb=0;
$jml_ltflb=0;
$jml_prcaphb=0;
$jml_lpaphb=0;
$drop1=mysql_fetch_assoc($drop);
$jml_prcb=$jml_prcb + $drop1['prcb'];
$jml_tcb=$jml_tcb + $drop1['tcb'];
$jml_tcaphb=$jml_tcaphb + $drop1['aphb'];
$jml_ahfb=$jml_ahfb + $drop1['ahfb'];
$jml_ffpb=$jml_ffpb + $drop1['ffpb'];
$jml_wbb=$jml_wbb + $drop1['wbb'];
$jml_web=$jml_web + $drop1['web'];
$jml_lpb=$jml_lpb + $drop1['lpb'];
$jml_fpb=$jml_fpb + $drop1['fpb'];
$jml_ldb=$jml_ldb + $drop1['ldb'];
$jml_brb=$jml_brb + $drop1['brb'];
$jml_bflb=$jml_bflb + $drop1['bflb'];
$jml_ltflb=$jml_ltflb + $drop1['ltflb'];
$jml_prcaphb=$jml_prcaphb + $drop1['prcaphb'];
$jml_lpaphb=$jml_lpaphb + $drop1['lpaphb'];
$jml_drop=$jml_prcb + $jml_tcb + $jml_tcaphb + $jml_ahfb + $jml_ffpb + $jml_wbb + $jml_web + $jml_lpb + $jml_fpb + $jml_ldb+$jml_brb+$jml_bflb+$jml_ltflb+$jml_prcaphb+$jml_lpaphb;
//==================================DROPING UDD===========================================
$sqlu="SELECT
                                        k.`udd` as uddkode,
                                        SUM(CASE WHEN s.`produk`='PRC' THEN 1 ELSE 0 END) as prcu,
                                        SUM(CASE WHEN s.`produk`='TC' THEN 1 ELSE 0 END) as tcu,
                                        SUM(CASE WHEN s.`produk`='TC Aferesis' THEN 1 ELSE 0 END) as aphu,
                                        SUM(CASE WHEN s.`produk`='AHF' THEN 1 ELSE 0 END) as ahfu,
                                        SUM(CASE WHEN s.`produk`='FFP' THEN 1 ELSE 0 END) as ffpu,
                                        SUM(CASE WHEN s.`produk`='WB' THEN 1 ELSE 0 END) as wbu,
                                        SUM(CASE WHEN s.`produk`='WE' THEN 1 ELSE 0 END) as weu,
                                        SUM(CASE WHEN s.`produk`='LP' THEN 1 ELSE 0 END) as lpu,
                                        SUM(CASE WHEN s.`produk`='FP' THEN 1 ELSE 0 END) as fpu,
                                        SUM(CASE WHEN s.`produk`='Leucodepleted' THEN 1 ELSE 0 END) as ldu,
                                        SUM(CASE WHEN s.`produk`='Buffycoat Removal' THEN 1 ELSE 0 END) as brb,
                                        SUM(CASE WHEN s.`produk`='Bedside Filter Leukosit' THEN 1 ELSE 0 END) as bflu,
                                        SUM(CASE WHEN s.`produk`='Lab Type Filter Leukosit' THEN 1 ELSE 0 END) as ltflu,
                                        SUM(CASE WHEN s.`produk`='PRC Aferesis' THEN 1 ELSE 0 END) as prcaphu,
                                        SUM(CASE WHEN s.`produk`='Plasma Aferesis' THEN 1 ELSE 0 END) as lpaphu
                                        FROM `kirimudd` k
                                        inner join `utd` b on b.`id`=k.`udd`
                                        inner join `stokkantong` s on s.`noKantong`=k.`nokantong`
                                        WHERE month(k.`tgl`)='$v_bulan' AND year(k.`tgl`)='$v_tahun' AND k.`status`='0'";

//echo "$sql";
$qrawu=mysql_query($sqlu);
$jml_prcu=0;
$jml_tcu=0;
$jml_tcaphu=0;
$jml_ahfu=0;
$jml_ffpu=0;
$jml_wbu=0;
$jml_weu=0;
$jml_lpu=0;
$jml_fpu=0;
$jml_ldu=0;
$jml_bru=0;
$jml_bflu=0;
$jml_ltflu=0;
$jml_prcaphu=0;
$jml_lpaphu=0;
$dropu=mysql_fetch_assoc($qrawu);
$jml_prcu=$jml_prcu + $dropu['prcu'];
$jml_tcu=$jml_tcu + $dropu['tcu'];
$jml_tcaphu=$jml_tcaphu + $dropu['aphu'];
$jml_ahfu=$jml_ahfu + $dropu['ahfu'];
$jml_ffpu=$jml_ffpu + $dropu['ffpu'];
$jml_wbu=$jml_wbu + $dropu['wbu'];
$jml_weu=$jml_weu + $dropu['weu'];
$jml_lpu=$jml_lpu + $dropu['lpu'];
$jml_fpu=$jml_fpu + $dropu['fpu'];
$jml_ldu=$jml_ldu + $dropu['ldu'];
$jml_bru=$jml_bru + $dropu['bru'];
$jml_bflu=$jml_bflu + $dropu['bflu'];
$jml_ltflu=$jml_ltflu + $dropu['ltflu'];
$jml_prcaphu=$jml_prcaphu + $dropu['prcaphu'];
$jml_lpaphu=$jml_lpaphu + $dropu['lpaphu'];
$jml_dropu=$jml_prcu + $jml_tcu + $jml_tcaphu + $jml_ahfu + $jml_ffpu + $jml_wbu + $jml_weu + $jml_lpu + $jml_fpu + $jml_ldu+$jml_bru+$jml_bflu+$jml_ltflu+$jml_prcaphu+$jml_lpaphu;

// ===============================PERMINTAAN===========================================
$sql="SELECT
                                        h.`rs` as rskode,
                                        r.`NamaRS` as rsnama,
                                        SUM(CASE WHEN d.`JenisDarah`='PRC' THEN d.`Jumlah` ELSE  0 END ) as prc,
                                        SUM(CASE WHEN d.`JenisDarah`='AHF' THEN d.`Jumlah` ELSE  0 END ) as ahf,
                                        SUM(CASE WHEN d.`JenisDarah`='FFP' THEN d.`Jumlah` ELSE  0 END ) as ffp,
                                        SUM(CASE WHEN d.`JenisDarah`='FP' THEN d.`Jumlah` ELSE  0 END ) as fp,
                                        SUM(CASE WHEN d.`JenisDarah`='Leucodepleted' THEN d.`Jumlah` ELSE  0 END )as ld,
                                        SUM(CASE WHEN d.`JenisDarah`='LP' THEN d.`Jumlah` ELSE  0 END ) as lp,
                                        SUM(CASE WHEN d.`JenisDarah`='TC' THEN d.`Jumlah` ELSE  0 END ) as tc,
                                        SUM(CASE WHEN (d.`JenisDarah`='TC Aferesis' or d.`JenisDarah`='TC Apheresis' or d.`JenisDarah`='TC-APH' ) THEN d.`Jumlah` ELSE  0 END ) as aph,
                                        SUM(CASE WHEN d.`JenisDarah`='WB' THEN d.`Jumlah` ELSE  0 END ) as wb,
                                        SUM(CASE WHEN (d.`JenisDarah`='WE' or d.`JenisDarah`='WRC') THEN d.`Jumlah` ELSE  0 END ) as we,
                                        SUM(CASE WHEN (d.`JenisDarah`='--Pilih-' or d.`JenisDarah`='' or d.`JenisDarah`='PRP') THEN d.`Jumlah` ELSE  0 END ) as ll,
                                        SUM(CASE WHEN d.`JenisDarah`='Buffycoat Removal' THEN d.`Jumlah` ELSE  0 END ) as br,
                                        SUM(CASE WHEN d.`JenisDarah`='Bedside Filter Leukosit' THEN d.`Jumlah` ELSE  0 END ) as bfl,
                                        SUM(CASE WHEN d.`JenisDarah`='Lab Type Filter Leukosit' THEN d.`Jumlah` ELSE  0 END ) as ltfl,
                                        SUM(CASE WHEN d.`JenisDarah`='PRC Aferesis' THEN d.`Jumlah` ELSE  0 END ) as prcaph,
                                        SUM(CASE WHEN d.`JenisDarah`='Plasma Aferesis' THEN d.`Jumlah` ELSE  0 END )as lpaph
                                        FROM `htranspermintaan` h
                                        left join `pasien` p on p.`no_rm`=h.`no_rm`
                                        left join `dtranspermintaan` d on d.`NoForm`=h.`noform`
                                        left join `rmhsakit` r on r.`Kode`=h.`rs`WHERE
                                        month(h.`tgl_register`)='$v_bulan' and year(h.`tgl_register`)='$v_tahun'";
$qraw=mysql_query($sql);
$jml_prc=0;
$jml_tc=0;
$jml_tcaph=0;
$jml_ahf=0;
$jml_ffp=0;
$jml_wb=0;
$jml_we=0;
$jml_lp=0;
$jml_fp=0;
$jml_ld=0;
$jml_ll=0;
$jml_br=0;
$jml_bfl=0;
$jml_ltfl=0;
$jml_prcaph=0;
$jml_lpaph=0;
$qraw1=mysql_fetch_assoc($qraw);
$jml_prc   = $jml_prc + $qraw1['prc'] + $jml_prcb + $jml_prcu;
$jml_tc    = $jml_tc + $qraw1['tc'] + $jml_tcb + $jml_tcu;
$jml_tcaph = $jml_tcaph + $qraw1['aph'] + $jml_tcaphb + $jml_tcaphu;
$jml_ahf   = $jml_ahf + $qraw1['ahf'] + $jml_ahfb  + $jml_ahfu;
$jml_ffp   = $jml_ffp + $qraw1['ffp'] + $jml_ffpb + $jml_ffpu;
$jml_wb    = $jml_wb + $qraw1['wb'] + $jml_wbb + $jml_wbu;
$jml_we    = $jml_we + $qraw1['we'] + $jml_web + $jml_weu;
$jml_lp    = $jml_lp + $qraw1['lp'] + $jml_lpb + $jml_lpu;
$jml_fp    = $jml_fp + $qraw1['fp'] + $jml_fpb + $jml_fpu;
$jml_ld    = $jml_ld + $qraw1['ld'] + $jml_ldb + $jml_ldu;
$jml_ll    = $jml_ll + $qraw1['ll'] ;
$jml_br    = $jml_br + $qraw1['br'] + $jml_brb + $jml_bru;
$jml_bfl    = $jml_bfl + $qraw1['bfl'] + $jml_bflb + $jml_bflu;
$jml_ltfl    = $jml_ltfl + $qraw1['ltfl'] + $jml_ltflb + $jml_ltflu;
$jml_prcaph    = $jml_prcaph + $qraw1['pcraph'] + $jml_prcaphb + $jml_prcaphu;
$jml_lpaph    = $jml_lpaph + $qraw1['lpaph'] + $jml_lpaphb + $jml_lpaphu;

$totminta=$jml_prc + $jml_tc + $jml_tcaph + $jml_ahf + $jml_ffp + $jml_wb + $jml_we + $jml_lp + $jml_fp + $jml_ld + $jml_ll +$jml_br+$jml_bfl+$jml_ltfl+$jml_prcaph+$jml_lpaph;


//===========================PEMAKAIAN===============================================

$lap="SELECT
                                    h.`rs` as rskode,
                                    r.`NamaRS` as rsnama,
                                    SUM(CASE WHEN d.`produk_darah`='PRC' THEN 1 ELSE  0 END ) as prca,
                                    SUM(CASE WHEN d.`produk_darah`='AHF' THEN 1 ELSE  0 END ) as ahfa,
                                    SUM(CASE WHEN d.`produk_darah`='FFP' THEN 1 ELSE  0 END ) as ffpa,
                                    SUM(CASE WHEN d.`produk_darah`='FP' THEN 1 ELSE  0 END ) as fpa,
                                    SUM(CASE WHEN d.`produk_darah`='Leucodepleted' THEN 1 ELSE  0 END )as lda,
                                    SUM(CASE WHEN d.`produk_darah`='LP' THEN 1 ELSE  0 END ) as lpa,
                                    SUM(CASE WHEN d.`produk_darah`='TC' THEN 1 ELSE  0 END ) as tca,
                                    SUM(CASE WHEN (d.`produk_darah`='TC Aferesis' or d.`produk_darah`='TC Apheresis' or d.`produk_darah`='TC-APH' ) THEN 1 ELSE  0 END ) as apha,
                                    SUM(CASE WHEN d.`produk_darah`='WB' THEN 1 ELSE  0 END ) as wba,
                                    SUM(CASE WHEN (d.`produk_darah`='WE' or d.`produk_darah`='WRC') THEN 1 ELSE  0 END ) as wea,
                                    SUM(CASE WHEN (d.`produk_darah`='--Pilih-' or d.`produk_darah`='' or d.`produk_darah`='PRP') THEN 1 ELSE  0 END ) as lla,
                                    SUM(CASE WHEN d.`produk_darah`='Buffycoat Removal' THEN 1 ELSE  0 END ) as bra,
                                    SUM(CASE WHEN d.`produk_darah`='Bedside Filter Leukosit' THEN 1 ELSE  0 END ) as bfla,
                                    SUM(CASE WHEN d.`produk_darah`='Lab Type Filter Leukosit' THEN 1 ELSE  0 END )as ltfla,
                                    SUM(CASE WHEN d.`produk_darah`='PRC Aferesis' THEN 1 ELSE  0 END ) as prcapha,
                                    SUM(CASE WHEN d.`produk_darah`='Plasma Aferesis' THEN 1 ELSE  0 END ) as lpapha
                                    FROM `htranspermintaan` h
                                    inner join `pasien` p on p.`no_rm`=h.`no_rm`
                                    inner join `dtransaksipermintaan` d on d.`NoForm`=h.`noform`
                                    inner join `rmhsakit` r on r.`Kode`=h.`rs`
                                    WHERE
                                    month(d.`tgl_keluar`)='$v_bulan' and year(d.`tgl_keluar`)='$v_tahun'
                                    and d.`Status`='0'";

$pakai=mysql_query($lap);
$jml_prca=0;
$jml_tca=0;
$jml_tcapha=0;
$jml_ahfa=0;
$jml_ffpa=0;
$jml_wba=0;
$jml_wea=0;
$jml_lpa=0;
$jml_fpa=0;
$jml_lda=0;
$jml_lla=0;

$jml_bra=0;
$jml_bfla=0;
$jml_ltfla=0;
$jml_prcapha=0;
$jml_lpapha=0;
$pakai1=mysql_fetch_assoc($pakai);
$jml_prca   = $jml_prca + $pakai1['prca'] + $jml_prcb + $jml_prcu;
$jml_tca    = $jml_tca + $pakai1['tca'] + $jml_tcb + $jml_tcu;
$jml_tcapha = $jml_tcapha + $pakai1['apha'] + $jml_tcaphb + $jml_tcaphu;
$jml_ahfa   = $jml_ahfa + $pakai1['ahfa'] + $jml_ahfb  + $jml_ahfu;
$jml_ffpa   = $jml_ffpa + $pakai1['ffpa'] + $jml_ffpb + $jml_ffpu;
$jml_wba    = $jml_wba + $pakai1['wba'] + $jml_wbb + $jml_wbu;
$jml_wea    = $jml_wea + $pakai1['wea'] + $jml_web + $jml_weu;
$jml_lpa    = $jml_lpa + $pakai1['lpa'] + $jml_lpb + $jml_lpu;
$jml_fpa    = $jml_fpa + $pakai1['fpa'] + $jml_fpb + $jml_fpu;
$jml_lda    = $jml_lda + $pakai1['lda'] + $jml_ldb + $jml_ldu;
$jml_lla    = $jml_lla + $pakai1['lla'];

$jml_bra    = $jml_bra + $pakai1['bra'] + $jml_brb + $jml_bru;
$jml_bfla    = $jml_bfla + $pakai1['bfla'] + $jml_bflb + $jml_bflu;
$jml_ltfla    = $jml_ltfla + $pakai1['ltfla'] + $jml_ltflb + $jml_ltflu;
$jml_prcapha    = $jml_prcapha + $pakai1['prcapha'] + $jml_prcaphb + $jml_prcaphu;
$jml_lpapha    = $jml_lpapha + $pakai1['lpapha'] + $jml_lpaph + $jml_lpaphu;
$totpakai=$jml_prca + $jml_tca + $jml_tcapha + $jml_ahfa + $jml_ffpa + $jml_wba + $jml_wea + $jml_lpa + $jml_fpa + $jml_lda + $jml_lla + $jml_bra+$jml_bfla+$jml_prcapha+$jml_lpapha;

?>

<title>Laporan UDD</title>
<head>
<style type="text/css" media="print">
    @page
    {
        size: portrait;
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
            LAPORAN KOMPONEN DARAH<br>
            <?php echo $dt_um['u_nama'];?><br>
            <?php echo 'BULAN '.$namaperiode.' '.$v_tahun;?>
        </td>
    </tr>
</table>
<div style="font-size: 13px;font-weight: bold; text-align: left; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <br><br>D.KOMPONEN DARAH
</div>


<table class="list" border="1" cellpadding="3" cellspacing="2" width="100%" style="border-collapse:collapse">
    <thead class="pmi">
    <tr>
        <th align="center" colspan="2" rowspan="2">NAMA KOMPONEN DARAH</th>
        <th align="center" colspan="3">JUMLAH</th>
    </tr>
    <tr>
        <th align="center">PRODUKSI</th>
        <th align="center">PERMINTAAN</th>
        <th align="center">PEMAKAIAN</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td rowspan="7">BIASA</td>
        <td>Whole Blood (WB)</td>                   <td align="center"><?php echo $komp1['WB1'];?></td>     <td align="center"><?php echo $jml_wb;?></td>  <td align="center"><?php echo $jml_wba;?></td>
    </tr>
    <tr> <td>Packed Red cell (PRC)</td>             <td align="center"><?php echo $komp['PRC'];?></td>    <td align="center"><?php echo $jml_prc;?></td>  <td align="center"><?php echo $jml_prca;?></td></tr>
    <tr> <td>Plasma/ Liquid Plasma (LP)</td>        <td align="center"><?php echo $komp['LP'];?></td>     <td align="center"><?php echo $jml_lp;?></td>  <td align="center"><?php echo $jml_lpa;?></td></tr>
    <tr> <td>Fresh Frozen Plasma (FFP)</td>         <td align="center"><?php echo $komp['FFP'];?></td>    <td align="center"><?php echo $jml_ffp;?></td>  <td align="center"><?php echo $jml_ffpa;?></td></tr>
    <tr> <td>Trombocyte Concentrat (TC)</td>        <td align="center"><?php echo $komp['TC'];?></td>     <td align="center"><?php echo $jml_tc;?></td>  <td align="center"><?php echo $jml_tca;?></td></tr>
    <tr> <td>Cryo-precipitate/ AHF</td>             <td align="center"><?php echo $komp['AHF'];?></td>    <td align="center"><?php echo $jml_ahf;?></td>  <td align="center"><?php echo $jml_ahfa;?></td></tr>
    <tr> <td>Washed Erythrocytes (WE)</td>          <td align="center"><?php echo $komp['WE'];?></td>     <td align="center"><?php echo $jml_we;?></td>  <td align="center"><?php echo $jml_wea;?></td></tr>
    <tr>
        <td rowspan="4">LEUKODEPLETED</td>
        <td>Buffycoat Removal (Leukoreduced)</td>                       <td align="center"><?php echo $komp['BUF_R'];?></td>      <td align="center"><?php echo $jml_br;?></td>  <td align="center"><?php echo $jml_bra;?></td>
    </tr>
    <tr> <td>Inline Filter Leukosit (Pre-storage Leukodepleted)</td>    <td align="center"><?php echo $komp['LEUCO'];?></td>      <td align="center"><?php echo $jml_ld;?></td>  <td align="center"><?php echo $jml_lda;?></td></tr>
    <tr> <td>Bedside Filter Leukosit (Post-storage Leukodepleted)</td>  <td align="center"><?php echo $komp['BF_LEUCO'];?></td>   <td align="center"><?php echo $jml_bfl;?></td>  <td align="center"><?php echo $jml_bfla;?></td></tr>
    <tr> <td>Lab Type Filter Leukosit (Post-storage Leukodepleted)</td> <td align="center"><?php echo $komp['LTF_LEUCO'];?></td>  <td align="center"><?php echo $jml_ltfl;?></td>  <td align="center"><?php echo $jml_ltfla;?></td></tr>

    <tr>
        <td rowspan="3">APHERESIS</td>
        <td>Packed Red cell (PRC)</td>              <td align="center"><?php echo $komp['PRC_APH']+$q_aph['PRC_Aph'];?></td>         <td align="center"><?php echo $jml_prcaph;?></td>  <td align="center"><?php echo $jml_prcapha;?></td>
    </tr>
    <tr> <td>Trombocyte Concentrat (TC)</td>        <td align="center"><?php echo $komp['TC_APH']+$q_aph['TC_Aph'];?></td>     <td align="center"><?php echo $jml_tcaph;?></td>  <td align="center"><?php echo $jml_tcapha;?></td></tr>
    <tr> <td>Plasma</td>                            <td align="center"><?php echo $komp['LP_APH']+$q_aph['LP_Aph'];?></td>     <td align="center"><?php echo $jml_lpaph;?></td>  <td align="center"><?php echo $jml_lpapha;?></td></tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2" align="center">JUMLAH</th>
        <th align="center"><?php echo $tot_produksi;?></th>
        <th align="center"><?php echo $totminta;?></th>
        <th align="center"><?php echo $totpakai;?></th>
    </tr>
    </tfoot>
</table>
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
<? echo "<meta http-equiv='refresh' content='2;url=pmitatausaha.php?module=komponen&t=$v_tahun&b=$v_bulan'";?>
</body>
