<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<?php
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
$nodokumen="-";
include('config/db_connect.php');
$today			=date("Y-m-d H:i:s");
$namauser		=$_SESSION['namauser'];
$namauserlkp	=$_SESSION['nama_lengkap'];
$modul			="KARANTINA";
$bag_pengirim	="AFTAP";
$bag_penerima	="KOMPONEN";
$notransaksi    =$_GET['no'];
$nknk           = $_POST['nomorkantong'];
$srsamp         = $_POST['sr_sample'];
$srstat         = $_POST['sr_status'];

//Post Logbook Sample BadBoy 210121
$simltd         = (isset($_POST['simltd'])) ? 1 : 0;
$skgd         	= (isset($_POST['skgd'])) ? 1 : 0;
$snat         	= (isset($_POST['snat'])) ? 1 : 0;
$pack         	= (isset($_POST['pack'])) ? 1 : 0;
$label         	= (isset($_POST['label'])) ? 1 : 0;
$splasma        = (isset($_POST['splasma'])) ? 1 : 0;
$sserum         = (isset($_POST['sserum'])) ? 1 : 0;
$swb         	= (isset($_POST['swb'])) ? 1 : 0;
$volckp         = (isset($_POST['volckp'])) ? 1 : 0;
$lisis         	= (isset($_POST['lisis'])) ? 1 : 0;
$dokumen        = (isset($_POST['dokumen'])) ? 1 : 0;
$infoklinis     = (isset($_POST['infoklinis'])) ? 1 : 0;
//************************************

$utd            =mysql_fetch_assoc(mysql_query("select upper(`nama`) as `nama` from `utd` where `aktif`='1'"));
$utd            =$utd['nama'];
$ck=mysql_fetch_assoc(mysql_query("SELECT h.`shift` FROM `stokkantong` s LEFT JOIN `htransaksi` h on s.`noKantong`=h.`NoKantong` WHERE s.`noKantong`='$_POST[nomorkantong]'"));
//echo "$notransaksi";

if(isset($_POST['nomorkantong'])){
    $supd=mysql_query("SELECT
    dst_nokantong
    FROM serahterima_detail
    WHERE
    dst_notrans='$notransaksi' AND
    dst_nokantong='$_POST[nomorkantong]' AND
    dst_receive2 ='' AND
    dst_stat_receive2='0' AND
    dst_date_receive2 IS NULL");

    $supd1=mysql_query("SELECT
    dst_nokantong
    FROM serahterima_detail
    WHERE
    dst_notrans='$notransaksi' AND
    dst_nokantong='$_POST[nomorkantong]' AND
    dst_receive2 IS NOT NULL AND
    dst_stat_receive2 !='0' AND
    dst_date_receive2 IS NOT NULL");

   /* if(mysql_num_rows($supd)==1){
        $updatedst=mysql_query("UPDATE
        serahterima_detail
        SET
        dst_receive2='$namauser',
        dst_stat_receive2='$srstat',
        dst_date_receive2='$today',
        dst_shift_receive2='$ck[shift]',
	dst_receive3='$namauser',
        dst_stat_receive3='$srstat',
        dst_date_receive3='$today',
        dst_shift_receive3='$ck[shift]',
	simltd	= '$simltd',
	skgd	= '$skgd',
	snat	= '$snat',
	packing	= '$pack',
	label	= '$label',
	splasma	= '$splasma',
	sserum	= '$sserum',
	swb	= '$swb',
	volket	= '$volckp',
	lisis	= '$lisis',
	dokumen	= '$dokumen',
	infoklinis= '$infoklinis'
        WHERE
        dst_notrans='$notransaksi' AND
        dst_nokantong='$_POST[nomorkantong]'");  Aslinya*/ 
	
	if(mysql_num_rows($supd)==1){
        $updatedst=mysql_query("UPDATE
        serahterima_detail
        SET
        dst_receive2='$namauser',
        dst_stat_receive2='$srstat',
        dst_date_receive2='$today',
        dst_shift_receive2='$ck[shift]',
	simltd	= '$simltd',
	skgd	= '$skgd',
	snat	= '$snat',
	packing	= '$pack',
	label	= '$label',
	splasma	= '$splasma',
	sserum	= '$sserum',
	swb	= '$swb',
	volket	= '$volckp',
	lisis	= '$lisis',
	dokumen	= '$dokumen',
	infoklinis= '$infoklinis'
        WHERE
        dst_notrans='$notransaksi' AND
        dst_nokantong='$_POST[nomorkantong]'");

        if($updatedst){
            $msg="Nomor Kantong berhasil diproses!";
        }
    } else if(mysql_num_rows($supd1)==1) {
        $msg="Update Belum Berhasil, Data Kantong telah diproses sebelumnya.";
    } else {
        $msg="Data kantong yang Anda masukkan tidak ditemukan dalam daftar ini.";
    }
    $nokan2 = substr($nknk,0,-1);
    $selkomp=mysql_query("SELECT dst_nokantong FROM serahterima_detail
    WHERE
    dst_notrans='$notransaksi' AND
    dst_nokantong='$nknk' AND
    dst_receive1 IS NOT NULL AND
    dst_stat_receive1='1' AND
    dst_receive2 IS NOT NULL AND
    dst_stat_receive2='1' AND
    dst_receive3 IS NOT NULL AND
    dst_stat_receive3='1'");
    $selkomp1=mysql_fetch_assoc($selkomp);
    if(mysql_num_rows($selkomp)==1){
    $ups=mysql_query("UPDATE stokkantong SET sah='1' WHERE `noKantong` like '$nokan2%'");
    }

}
?>

<title>SIMDONDAR</title>
<head>
    <style>
        #serahterima {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size: 14px;
            border-collapse: collapse;
        }

        #serahterima td, #serahterima th {
            border: 1px solid #ddd;
            padding: 3px;
        }

        #serahterima tr:nth-child(even){background-color: #ffe6e6;}

        #serahterima tr:hover {background-color: #ddd;}

        #serahterima tr td {
            padding-top: 2px;
            padding-bottom: 2px;
            text-align: left;
            font-weight: lighter;
            background-color: #ff9999;
            color: #000000;
        }
        #serahterima input{
            padding-top: 2px;
            padding-bottom: 2px;
            text-align: left;
            background-color: lightyellow;
            color: #000000;
        }
    </style>
    <style>
        #entrybox {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size: 14px;
            border-collapse: collapse;
        }

        #entrybox td, #entrybox th {
            border: 1px solid #ddd;
            background-color: #ffe6e6;
            padding: 3px;
        }

        #entrybox th {
            padding-top: 2px;
            padding-bottom: 2px;
            text-align: left;
            font-weight: lighter;
            background-color: #ffe6e6;
            color: #000000;
        }
        #entrybox input{
            padding-top: 2px;
            padding-bottom: 2px;
            text-align: left;
            font-weight: bold;
            background-color: #e6ffe6;
            color: #000000;
        }
    </style>
	<style>
tr { background-color: #FDF5E6}
  .initial { background-color: #FDF5E6; color:#000000 }
  .normal { background-color: #FDF5E6 }
  .highlight { background-color: #7FFF00 }
</style>
<script language="javascript">
    function setFocus(){document.serahterima.nomorkantong.focus();}
</script>
</head>
<?php

if(isset($_POST['sim'])){
    echo "TRANSAKSI SERAH TERIMA SUKSES, SAMPLE BERHASIL DIPROSES.";
    echo "<meta http-equiv='refresh' content='2;url=pmiimltd.php?module=sr_list'";
}
?>
<body onload="setFocus()">

<?php
$sql_h="SELECT `hst_id`, `hst_notrans`, `hst_bagpengirim`, `hst_bagpenerima`, `hst_tgl`, `hst_asal`, `hst_jenis_st`,
            `hst_user`, `hst_pengirim`, `hst_penerima`, `hst_penerima2`, `hst_kode_alat`, `hst_suhuterima`, `hst_kondisiumum`,
            `hst_peruntukan`, `hst_modul`, `hst_shift_pengirim`, `hst_shift_penerima` FROM `serahterima`
            WHERE `hst_notrans`='$notransaksi'";
$sql_h1=mysql_fetch_assoc(mysql_query($sql_h));
?>
<table class="list" border="0" cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr style="font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;font-size: 10px;">
        <td style="text-align: left"><? echo $utd;?></td>
        <td style="text-align: right"><? echo $nodokumen;?></td>
    </tr>
    <tr style="font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;font-size: 11px;">
        <td style="text-align: left">Formulir Serah Terima Sample Darah IMLTD</td>
        <td style="text-align: right">Versi : 001</td>
    </tr>
</table>
<hr>
<form name="serahterima" method="post">
<table id="serahterima" class="list" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
    <tr>
        <td style="height: 40px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;" colspan="2">SERAH TERIMA SAMPLE (IMLTD & KGD)</td>
    </tr>
    <tr style="font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td style="vertical-align: top; width=50%;">
            <table id="serahterima" class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
                <tr><td style="text-align: left">Tanggal transaksi</td>          <td><?php echo $sql_h1['hst_tgl']; ?></td></tr>
                <tr><td style="text-align: left">No. transaksi</td>              <td><?php echo $sql_h1['hst_notrans']; ?></td></tr>
                <tr><td style="text-align: left">Bagian yang mengirimkan</td>    <td><?php echo $sql_h1['hst_bagpengirim']; ?></td></tr>
                <tr><td style="text-align: left">Bagian yang menerima</td>       <td>IMLTD</td></tr>
            </table>
        </td>
        <td style="vertical-align: top;width=50%">
            <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
                <tr><td style="text-align: left">Asal Sample</td>         <td><?php echo $sql_h1['hst_asal']; ?></td></tr>
                <!--tr><td style="text-align: left">Kode alat pengiriman</td>       <td><?php echo $sql_h1['hst_kode_alat']; ?></td></tr>
                <tr><td style="text-align: left">Suhu saat diterima</td>         <td><?php echo $sql_h1['hst_suhuterima']; ?><sup>o</sup>C</td></tr-->
                <tr><td style="text-align: left">Keadaan umum</td>               <td><?php echo $sql_h1['hst_kondisiumum']; ?></td></tr>
            </table>
        </td>
    </tr>
</table>
<br>
<table id="entrybox" style="border-collapse: collapse;border: 2px solid #ff0000;width: 100%; box-shadow: 1px 2px 2px #800000;">
    

<td style="height: 40px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif; " colspan="7" >PENGAMATAN VISUAL</td>
</tr>
    <tr valign="top">


                <td style="background-color: #ff9191" colspan="2">
                   	<input type="checkbox" checked="checked" name="simltd"/>
                        <label class="control control-checkbox">Sampel IMLTD
                            <div class="control_indicator"></div></label>
			<input type="checkbox" checked="checked" name="skgd"/>
                        <label class="control control-checkbox">Sampel KGD/ABS
                            <div class="control_indicator"></div></label>
			<input type="checkbox" name="snat"/>
			<label class="control control-checkbox">Sample NAT
                            <div class="control_indicator"></div></label>
</td>
<td style="background-color: #ff9191"> 
                        
			<input type="checkbox" checked="checked" name="pack"/>
			 <label class="control control-checkbox">Pengepakan Baik
                            <div class="control_indicator"></div></label>
			<input type="checkbox" checked="checked" name="label"/>
                        <label class="control control-checkbox">Label Sesuai
                            <div class="control_indicator"></div></label>
</td>
<td style="background-color: #ff9191" >                        
			<input type="checkbox"  name="splasma"/>			
			<label class="control control-checkbox">Sample Plasma
                            <div class="control_indicator"></div></label>
			<input type="checkbox" name="sserum"/>
                        <label class="control control-checkbox">Sample Serum
                            <div class="control_indicator"></div></label>
			<input type="checkbox" checked="checked" name="swb"/>
                        <label class="control control-checkbox">Sample WB
                            <div class="control_indicator"></div></label>
</td>
<td style="background-color: #ff9191">
			<input type="checkbox" checked="checked" name="volckp" />
                        <label class="control control-checkbox">Volume Cukup
                            <div class="control_indicator"></div></label>
			<input type="checkbox" checked="checked" name="lisis" />
                        <label class="control control-checkbox">Sampel Tidak Lisis
                            <div class="control_indicator"></div></label>
</td>
<td style="background-color: #ff9191" colspan="2">
			<input type="checkbox" checked="checked" name="dokumen"/>
                        <label class="control control-checkbox">Dokumen Ada
                            <div class="control_indicator"></div></label>
			<input type="checkbox" name="infoklinis" />
                        <label class="control control-checkbox">Info Klinis Ada
                            <div class="control_indicator"></div></label>
                   

                    
                </td>

</tr>
<tr >
        <td>Status Sample</td>
        <td><select name="sr_sample" id="sr_sample" onkeypress="return handleEnter(this, event)">
                <option value="1">Sesuai</option>
                <option value="0">Tidak Sesuai</option>
            </select></td>
        <td>Status serah terima</td>
        <td><select name="sr_status" id="sr_status" onkeypress="return handleEnter(this, event)">
                <option value="1">Sah</option>
                <option value="2">Tidak Sah</option>
            </select></td>
        <td>Masukkan Nomor Sample</td>
        <td><input type=text name=nomorkantong id=nomorkantong autofocus onkeypress="return handleEnter(this, event)"></td>
        <td ><input type="submit" name="submit1" value="Ok" class="swn_button_blue" style="color: #ffffff"></td>
    </tr>
<tr>
<tr>
        <td style="height: 30px">Status Proses</td><td style="height: 40px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif; color:red" colspan="7" ><?=$msg?></td>
    </tr>

</table>
<?php
$sql_d="SELECT `dst_iddetail`, `dst_no_aftap`, `dst_tglaftap`, `dst_notrans`, `dst_nokantong`,
        CASE
        WHEN (`dst_statusktg`='1' and `dst_sahktg`='0') THEN 'Aftap'
        WHEN (`dst_statusktg`='1' and `dst_sahktg`='1') THEN 'Karantina'
        WHEN (`dst_statusktg`='2') THEN 'Sehat'
        WHEN (`dst_statusktg`='3') THEN 'Keluar'
        WHEN (`dst_statusktg`='4') THEN 'Reaktif-Rusak'
        WHEN (`dst_statusktg`='5') THEN 'Rusak-gagal'
        WHEN (`dst_statusktg`='6') THEN 'Rusak-Dimusnahkan'
        ELSE 'Tidak ada' end as `dst_statusktg`,
        `st_statusktg_new`, `dst_old_position`, `dst_new_position`,
        `dst_sahktg_new`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_kodedonor`, `dst_berat`, `dst_volumektg`,
        CASE
        WHEN (`dst_jenisktg`='1') THEN 'SB'
        WHEN (`dst_jenisktg`='2') THEN 'DB'
        WHEN (`dst_jenisktg`='3') THEN 'TR'
        WHEN (`dst_jenisktg`='4') THEN 'QD'
        WHEN (`dst_jenisktg`='6') THEN 'PB'
        END AS `dst_jenisktg`,
        CASE
        WHEN dst_stat_receive2='0' THEN 'Belum dilakukan'
        WHEN `dst_stat_receive2`='1' THEN 'Sesuai'
        ELSE 'Tidak Sah' END AS `dst_stat_receive2`,
        CASE WHEN `dst_sah`='1' THEN 'Sesuai' ELSE 'Tdk Sesuai' END AS `dst_sah`,
        CASE WHEN `dst_lamabaru`='0' THEN 'BR' ELSE 'UL' END AS `dst_lamabaru`,
        CASE WHEN `dst_kel`='0' THEN 'LK' ELSE 'PR' END AS `dst_kel`,
        CASE WHEN `dst_dsdp`='0' THEN 'DS' ELSE 'DP' END AS `dst_dsdp`,
        `dst_umur`, `dst_lama_aftap`, `dst_statuspengambilan`, `dst_ptgaftap`, `dst_volambil`, dst_receive2, dst_date_receive2, `no_cb`, `suhu_cb` FROM `serahterima_detail`
        WHERE `dst_notrans`='$notransaksi'";
//echo "$sql_d<br>";
$sql_d1=mysql_query($sql_d);
?>
<table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <thead style="background-color:#DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <tr style="background-color: #FF9999;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <th style="text-align: center; height: 40px">No</th>
        <th style="text-align: center; height: 40px">Nomor Kantong</th>
        <!--th style="text-align: center; height: 40px">Nomor<br>Coolbox</th>
        <th style="text-align: center; height: 40px">Suhu<br>(<sup>o</sup>C)</th-->
        <th style="text-align: center; height: 40px">Gol</th>
        <th style="text-align: center; height: 40px">Rh</th>
        <th style="text-align: center; height: 40px">Umur Donor</th>
        <th style="text-align: center; height: 40px">Jns Kel</th>
        <th style="text-align: center; height: 40px">Jns Donor</th>
        <th style="text-align: center; height: 40px">Donor UL/BR</th>
        <th style="text-align: center; height: 40px">Status Kantong</th>
        <th style="text-align: center; height: 40px">Nomor Aftap</th>
        <th style="text-align: center; height: 40px">Kode Donor</th>
        <th style="text-align: center; height: 40px">Ptgs Aftap</th>
        <th style="text-align: center; height: 40px">Keberterimaan Sample</th>
        <th style="text-align: center; height: 40px">Hasil</th>
    </tr>
    </thead>
    <tbody>
    <?
    $no=0;
    while ($sgd=mysql_fetch_assoc($sql_d1)){
        $no++;
        ?>
		<tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
        <!--tr style="background-color: #FFE6E6; font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'"-->
            <td style="text-align: right;"> <?php echo $no.'.';?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_nokantong'];?> </td>
            <!--td style="text-align: center;"> <?php echo $sgd['no_cb'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['suhu_cb'];?> </td-->
            <td style="text-align: center;"> <?php echo $sgd['dst_golda'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_rh'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_umur'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_kel'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_dsdp'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_lamabaru'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_statusktg'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_no_aftap'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_kodedonor'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_ptgaftap'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_stat_receive2'];?> </td>
            <?php
            if(empty($sgd['dst_receive2']) AND empty($sgd['dst_date_receive2'])){
            ?>
                <td style="text-align: center;"><input type="checkbox" onclick="return false" readonly></td>
            <?php
            }else{
            ?>
                <td style="text-align: center;"><input type="checkbox" onclick="return false" checked readonly></td>
            <?php
            }
            ?>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<br>
<?php
$sq_k="SELECT  `dst_dsdp`,`dst_lamabaru`,
    COUNT( CASE  WHEN (`dst_golda`='A' and `dst_rh`='+') THEN 1 ELSE NULL END) AS 'Apos',
	COUNT( CASE  WHEN (`dst_golda`='B' and `dst_rh`='+') THEN 1 ELSE NULL END) AS 'Bpos',
	COUNT( CASE  WHEN (`dst_golda`='O' and `dst_rh`='+') THEN 1 ELSE NULL END) AS 'Opos',
	COUNT( CASE  WHEN (`dst_golda`='AB' and `dst_rh`='+') THEN 1 ELSE NULL END) AS 'ABpos',
	COUNT( CASE  WHEN (`dst_golda`='A' and `dst_rh`='-') THEN 1 ELSE NULL END) AS 'Aneg',
	COUNT( CASE  WHEN (`dst_golda`='B' and `dst_rh`='-') THEN 1 ELSE NULL END) AS 'Bneg',
	COUNT( CASE  WHEN (`dst_golda`='O' and `dst_rh`='-') THEN 1 ELSE NULL END) AS 'Oneg',
	COUNT( CASE  WHEN (`dst_golda`='AB' and `dst_rh`='-') THEN 1 ELSE NULL END) AS 'ABneg'
    FROM `serahterima_detail` WHERE `dst_notrans`='$notransaksi' group by `dst_dsdp`, `dst_lamabaru`";
$sqk=mysql_query($sq_k);
$no=0;
?>
<table class="list" border=1 cellpadding="2" cellspacing="2" width="50%"  style="border-collapse:collapse">
    <thead style="background-color:#DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <tr style="background-color: #FF9999;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <th style="text-align: center;" rowspan="2">No</th>
        <th style="text-align: center;" rowspan="2" colspan="2">Jenis Donor</th>
        <th style="text-align: center;" colspan="5">Rhesus Positif</th>
        <th style="text-align: center;" colspan="5">Rhesue Negatif</th>
        <th style="text-align: center;" rowspan="2">Jml</th>
    </tr>
    <tr style="background-color: #FF9999;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
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
    $jmlap=0;$jmlbp=0;$jmlop=0;$jmlabp=0;
    $jmlan=0;$jmlbn=0;$jmlon=0;$jmlabn=0;
    $jmlrhp=0;$jmlrhn=0;$jmlrow=0;
    while ($sq_k1=mysql_fetch_assoc($sqk)){
        $no++;
        $jmlap  = $jmlap + $sq_k1['Apos'];
        $jmlbp  = $jmlbp + $sq_k1['Bpos'];
        $jmlop  = $jmlop + $sq_k1['Opos'];
        $jmlabp = $jmlabp + $sq_k1['ABpos'];
        $jmlan  = $jmlan + $sq_k1['Aneg'];
        $jmlbn  = $jmlbn + $sq_k1['Bneg'];
        $jmlon  = $jmlon + $sq_k1['Oneg'];
        $jmlabn = $jmlabn + $sq_k1['ABneg'];

        $jmlrhp = $sq_k1['Apos']+$sq_k1['Bpos']+$sq_k1['Opos']+$sq_k1['ABpos'];
        $jmlrhn = $sq_k1['Aneg']+$sq_k1['Bneg']+$sq_k1['Oneg']+$sq_k1['ABneg'];

        $jmlrow = $jmlrhp+$jmlrhn;

        switch ($sq_k1['dst_dsdp']){
            case 0:$jenis='Donor Sukarela';break;
            case 1:$jenis='Donor Pengganti';break;
        }
        switch ($sq_k1['dst_lamabaru']){
            case 0:$jenis1='Donor Ulang';break;
            case 1:$jenis1='Donor Baru';break;
        }?>
        <tr style="background-color: #FFE6E6; font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td style="text-align: right;"> <?php echo $no.'.';?> </td>
            <td style="text-align: left;"> <?php echo $jenis;?> </td>
            <td style="text-align: left;"> <?php echo $jenis1;?> </td>
            <td style="text-align: center;"> <?php echo $sq_k1['Apos'];?> </td>
            <td style="text-align: center;"> <?php echo $sq_k1['Bpos'];?> </td>
            <td style="text-align: center;"> <?php echo $sq_k1['Opos']?> </td>
            <td style="text-align: center;"> <?php echo $sq_k1['ABpos'];?> </td>
            <td style="text-align: center;"> <?php echo $jmlrhp;?> </td>
            <td style="text-align: center;"> <?php echo $sq_k1['Aneg'];?> </td>
            <td style="text-align: center;"> <?php echo $sq_k1['Bneg'];?> </td>
            <td style="text-align: center;"> <?php echo $sq_k1['Oneg']?> </td>
            <td style="text-align: center;"> <?php echo $sq_k1['ABneg'];?> </td>
            <td style="text-align: center;"> <?php echo $jmlrhn;?> </td>
            <td style="text-align: center;"> <?php echo $jmlrow;?> </td>
        </tr>
    <?
    }
    $jmlrhp = $jmlap + $jmlbp + $jmlop + $jmlabp;
    $jmlrhn = $jmlan + $jmlbn + $jmlon + $jmlabn;
    $jmlrow = $jmlrhp + $jmlrhn;
    ?>

    <tr style="font-size:12px; color:#000000;background-color: #FF9999; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td colspan="3" style="text-align: center">Jumlah</td>
        <td style="text-align: center"> <? echo $jmlap;?> </td>
        <td style="text-align: center"> <? echo $jmlbp;?> </td>
        <td style="text-align: center"> <? echo $jmlop;?> </td>
        <td style="text-align: center"> <? echo $jmlabp;?> </td>
        <td style="text-align: center"> <? echo $jmlrhp;?> </td>
        <td style="text-align: center"> <? echo $jmlan;?> </td>
        <td style="text-align: center"> <? echo $jmlbn;?> </td>
        <td style="text-align: center"> <? echo $jmlon;?> </td>
        <td style="text-align: center"> <? echo $jmlabn;?> </td>
        <td style="text-align: center"> <? echo $jmlrhn;?> </td>
        <td style="text-align: center"> <? echo $jmlrow;?> </td>
    </tr>

    </tbody>
</table>
<?
$usr        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_user]'"));
$pencatat   = $usr['nama_lengkap'];
$usr        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_pengirim]'"));
$pengirim   = $usr['nama_lengkap'];
$usr        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_penerima2]'"));
$penerima   = $usr['nama_lengkap'];
?>
<br>
<table class="list" border=1 cellpadding="5" cellspacing="5" style="border-collapse:collapse" width="100%">
    <thead
    <tr style="background-color: #FF9999;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <th style="text-align: center;height: 30px;"></th>
        <th style="text-align: center;height: 30px;" nowrap>Nama Petugas</th>
        <th style="text-align: center;height: 30px;" nowrap>Tanda Tangan</th>
        <th style="text-align: center;height: 30px;" nowrap>Tanggal dan Jam</th>
        <th style="text-align: center;height: 30px;">Catatan</th>
    </tr>
    </thead>
    <tr style="background-color: #FFE6E6; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td nowrap>Petugas Pencatat</td>
        <td nowrap><?php echo $pencatat; ?></td>
        <td></td>
        <td></td>
        <td rowspan="3" style="vertical-align:top;">
            <ol style="font-size:10px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
                <li>Jenis Donor = DS: Donor Sukarela; DP : Donor Pengganti</li>
                <li>Donor UL/BR (Donor Ulang/Donor Baru) = UL: Donor Ulang; BR : Donor Baru</li>
                <li>Jenis Kel (Kelamin) = LK: Laki-Laki; PR : Perempuan</li>
                <li>Status kantong : adalah status pada saat serah terima dilakukan</li>
            </ol>
        </td>

    </tr>
    <tr style="background-color: #FFE6E6; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td nowrap>Petugas Pengirim</td>
        <td nowrap><?php echo $pengirim; ?></td>
        <td></td>
        <td></td>

    </tr>
    <tr style="background-color: #FFE6E6; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td nowrap>Petugas Penerima</td>
        <td nowrap><?php echo $penerima; ?></td>
        <td></td>
        <td></td>

    </tr>
</table>
<hr style="width: 100%;text-align:left;margin-left:0; line-height: 1px" >
<input type="submit" name="sim" value="Simpan Proses Terima" class="swn_button_blue">
</form>
</body>
