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
$bk		= $_POST['beratkantong'];
$srstat         = $_POST['sr_status'];
$utd            =mysql_fetch_assoc(mysql_query("select upper(`nama`) as `nama` from `utd` where `aktif`='1'"));
$utd            =$utd['nama'];
$ck=mysql_fetch_assoc(mysql_query("SELECT
h.`shift`
FROM
`stokkantong` s LEFT JOIN `htransaksi` h on
s.`noKantong`=h.`NoKantong`
WHERE
s.`noKantong`='$_POST[nomorkantong]'"));
//echo "$notransaksi";

if(isset($_POST['nomorkantong'])){





    $supd=mysql_query("SELECT
    dst_nokantong
    FROM serahterima_detail
    WHERE
    dst_notrans='$notransaksi' AND
    dst_nokantong='$_POST[nomorkantong]' AND
    dst_receive1 ='' AND
    dst_stat_receive1='0' AND
    dst_date_receive1 IS NULL");

    $supd1=mysql_query("SELECT
    dst_nokantong
    FROM serahterima_detail
    WHERE
    dst_notrans='$notransaksi' AND
    dst_nokantong='$_POST[nomorkantong]' AND
    dst_receive1 IS NOT NULL AND
    dst_stat_receive1 !='0' AND
    dst_date_receive1 IS NOT NULL");

//Theo 210121 Ubah berat menjadi vol (parameter tb master_kantong)
	
$cariktg	= mysql_fetch_assoc(mysql_query("SELECT merk,jenis from stokkantong where noKantong='$nknk' limit 1"));
$merk		= $cariktg['merk'];
$jenis		= $cariktg['jenis'];
$msktg		= mysql_fetch_assoc(mysql_query("SELECT (`berat_ku`+`berat_s1`+`berat_s2`+`berat_s3`+`berat_s4`+`berat_s5`+`berat_s6`+`berat_s7`+`antikoagulant`) as bkantong FROM `master_kantong` WHERE `merk`='$merk' AND `jenis`='$jenis' limit 1")); 
$btemp		= $msktg['bkantong'];
$bktg		= round($btemp,0);
$volcc		= ($bk-$bktg)/1.055;
$volktg		= round($volcc,0);
$stupd		= mysql_query("UPDATE stokkantong set volume='$volktg' where noKantong='$nknk'");
//********************************

    if(mysql_num_rows($supd)==1){


echo "$merk<br>";
echo "$jenis<br>";
echo "$btemp<br>";
echo "$bktg<br>";
echo "$volcc<br>";
echo "$volktg<br>";
        $updatedst=mysql_query("UPDATE
        serahterima_detail
        SET
        dst_receive1='$namauser',
        dst_stat_receive1='$srstat',
        dst_date_receive1='$today',
        dst_shift_receive1='$ck[shift]'
        WHERE
        dst_notrans='$notransaksi' AND
        dst_nokantong='$_POST[nomorkantong]'");

        if($updatedst){
            $msg="Nomor Kantong berhasil diproses!";
        }
    } else if(mysql_num_rows($supd1)==1) {

	$updatedst=mysql_query("UPDATE
        serahterima_detail
        SET
        dst_receive1='$namauser',
        dst_stat_receive1='$srstat',
        dst_date_receive1='$today',
        dst_shift_receive1='$ck[shift]'
        WHERE
        dst_notrans='$notransaksi' AND
        dst_nokantong='$_POST[nomorkantong]'");

        if($updatedst){
            $msg="Nomor Kantong berhasil diupdate!";
        }
    } else {
        $msg="Data kantong yang Anda masukkan tidak ditemukan dalam daftar ini.";
    }
    
    $lastnkt=substr($nknk, -1); 
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
        $ups=mysql_query("UPDATE stokkantong SET sah='1' WHERE noKantong = '$nknk'");
    }

}
?>
<head>
    <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<style>
    .awesomeText {
        color: #000;
        font-size: 100%;
    }
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

    #serahterima th {
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
<script language="javascript">
    function setFocus(){document.serahterima.nomorkantong.focus();}


    /***********************************************
     * Disable "Enter" key in Form script- By Nurul Fadilah(nurul@REMOVETHISvolmedia.com)
     * This notice must stay intact for use
     * Visit http://www.dynamicdrive.com/ for full source code
     ***********************************************/

    function handleEnter (field, event) {
        var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
        if (keyCode == 13) {
            var i;
            for (i = 0; i < field.form.elements.length; i++)
                if (field == field.form.elements[i])
                    break;
            i = (i + 1) % field.form.elements.length;
            field.form.elements[i].focus();
            return false;
        }
        else
            return true;
    }

</script>

<body onLoad=setFocus();>

<?php

if(isset($_POST['sim'])){
    echo "TRANSAKSI SERAH TERIMA SUKSES, KANTONG BERHASIL DIPROSES.";
    echo "<meta http-equiv='refresh' content='2;url=pmikomponen.php?module=sr_list'";
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
        <td style="text-align: left">Formulir Serah Terima Darah Komponen</td>
        <td style="text-align: right">Versi : 001</td>
    </tr>
</table>
<hr>
<form name="serahterima" method="post">
<table id="serahterima" class="list" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
    <tr>
        <td style="height: 40px;font-size: 16px;font-weight: bold; text-align: center; font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;" colspan="2">SERAH TERIMA DARAH</td>
    </tr>
    <tr style="font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td style="vertical-align: top; width=50%;">
            <table id="serahterima" class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
                <tr><td style="text-align: left">Tanggal transaksi</td>          <td><?php echo $sql_h1['hst_tgl']; ?></td></tr>
                <tr><td style="text-align: left">No. transaksi</td>              <td><?php echo $sql_h1['hst_notrans']; ?></td></tr>
                <tr><td style="text-align: left">Bagian yang mengirimkan</td>    <td><?php echo $sql_h1['hst_bagpengirim']; ?></td></tr>
                <tr><td style="text-align: left">Bagian yang menerima</td>       <td>KOMPONEN</td></tr>
            </table>
        </td>
        <td style="vertical-align: top;width=50%">
            <table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
                <tr><td style="text-align: left">Asal Kantong Darah</td>         <td><?php echo $sql_h1['hst_asal']; ?></td></tr>
                <tr><td style="text-align: left">Kode alat pengiriman</td>       <td><?php echo $sql_h1['hst_kode_alat']; ?></td></tr>
                <tr><td style="text-align: left">Suhu saat diterima</td>         <td><?php echo $sql_h1['hst_suhuterima']; ?><sup>o</sup>C</td></tr>
                <tr><td style="text-align: left">Keadaan umum</td>               <td><?php echo $sql_h1['hst_kondisiumum']; ?></td></tr>
            </table>
        </td>
    </tr>
</table>
<br>
<table id="entrybox" style="border-collapse: collapse;border: 2px solid #ff0000;width: 100%; box-shadow: 1px 2px 2px #800000;">
    <tr>
        <td>Status serah terima</td>
        <td><select name="sr_status" id="sr_status" onkeypress="return handleEnter(this, event)">
                <option value="1">Sah</option>
                <option value="2">Tidak Sah</option>
            </select></td>
        <td>Masukkan Nomor Kantong</td>
        <td><input type=text name=nomorkantong id=nomorkantong autofocus onkeypress="return handleEnter(this, event)"></td>
	<td>Volume Kantong (ml)</td>
        <td><input type=text name=beratkantong id=beratkantong value="350" onkeypress="return handleEnter(this, event)"></td>
        <td><input type="submit" name="submit1" value="Ok" class="swn_button_blue" style="color: #ffffff"></td>
    </tr>
    <tr>
        <td style="height: 30px">Status Proses</td><td colspan="7"><?=$msg?></td>
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
        WHEN dst_stat_receive1='0' THEN 'Belum dilakukan'
        WHEN `dst_stat_receive1`='1' THEN 'Sesuai'
        ELSE 'Tidak Sah' END AS `dst_stat_receive1`,
        CASE WHEN `dst_sah`='1' THEN 'Sesuai' ELSE 'Tdk Sesuai' END AS `dst_sah`,
        CASE WHEN `dst_lamabaru`='0' THEN 'BR' ELSE 'UL' END AS `dst_lamabaru`,
        CASE WHEN `dst_kel`='0' THEN 'LK' ELSE 'PR' END AS `dst_kel`,
        CASE WHEN `dst_dsdp`='0' THEN 'DS' ELSE 'DP' END AS `dst_dsdp`,
        `dst_umur`, `dst_lama_aftap`, `dst_statuspengambilan`, `dst_ptgaftap`, `dst_volambil`, dst_receive1, dst_date_receive1 FROM `serahterima_detail`
        WHERE `dst_notrans`='$notransaksi'";
//echo "$sql_d<br>";
$sql_d1=mysql_query($sql_d);
?>
<table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <thead style="background-color:#DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <tr style="background-color: #FF9999;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <th style="text-align: center; height: 40px">No</th>
        <th style="text-align: center; height: 40px">Nomor Kantong</th>
        <th style="text-align: center; height: 40px">Nomor Aftap</th>
        <th style="text-align: center; height: 40px">Jenis<br>Kantong</th>
        <th style="text-align: center; height: 40px">Lama Aftap<br>(Menit)</th>
        <th style="text-align: center; height: 40px">Merk</th>
        <th style="text-align: center; height: 40px">Status Kantong</th>
        <th style="text-align: center; height: 40px">Ptgs Aftap</th>
        <th style="text-align: center; height: 40px">Gol</th>
        <th style="text-align: center; height: 40px">Rh</th>
        <th style="text-align: center; height: 40px">Jns Donor</th>
        <th style="text-align: center; height: 40px">Keberterimaan Kantong</th>
        <th style="text-align: center; height: 40px">Hasil</th>
    </tr>
    </thead>
    <tbody>
    <?
    $no=0;
    while ($sgd=mysql_fetch_assoc($sql_d1)){
        $no++;
        ?>
        <tr style="background-color: #FFE6E6; font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td style="text-align: right;"> <?php echo $no.'.';?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_nokantong'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_no_aftap'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_jenisktg'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_lama_aftap'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_merk'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_statusktg'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_ptgaftap'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_golda'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_rh'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_dsdp'];?> </td>
            <td style="text-align: center;"> <?php echo $sgd['dst_stat_receive1'];?> </td>
            <?php
            if(empty($sgd['dst_receive1']) AND empty($sgd['dst_date_receive1'])){
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
$sq_k="SELECT  `dst_jenisktg`,
    COUNT( CASE  WHEN (`dst_golda`='A' and `dst_rh`='+') THEN 1 ELSE NULL END) AS 'Apos',
	COUNT( CASE  WHEN (`dst_golda`='B' and `dst_rh`='+') THEN 1 ELSE NULL END) AS 'Bpos',
	COUNT( CASE  WHEN (`dst_golda`='O' and `dst_rh`='+') THEN 1 ELSE NULL END) AS 'Opos',
	COUNT( CASE  WHEN (`dst_golda`='AB' and `dst_rh`='+') THEN 1 ELSE NULL END) AS 'ABpos',
	COUNT( CASE  WHEN (`dst_golda`='A' and `dst_rh`='-') THEN 1 ELSE NULL END) AS 'Aneg',
	COUNT( CASE  WHEN (`dst_golda`='B' and `dst_rh`='-') THEN 1 ELSE NULL END) AS 'Bneg',
	COUNT( CASE  WHEN (`dst_golda`='O' and `dst_rh`='-') THEN 1 ELSE NULL END) AS 'Oneg',
	COUNT( CASE  WHEN (`dst_golda`='AB' and `dst_rh`='-') THEN 1 ELSE NULL END) AS 'ABneg'
    FROM `serahterima_detail` WHERE `dst_notrans`='$notransaksi' group by `dst_jenisktg`";
$sqk=mysql_query($sq_k);
$no=0;
?>
<table class="list" border=1 cellpadding="2" cellspacing="2" width="50%"  style="border-collapse:collapse">
    <thead style="background-color:#DCDCDC;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
    <tr style="background-color: #FF9999;font-wight:bold; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <th style="text-align: center;" rowspan="2">No</th>
        <th style="text-align: center;" rowspan="2">Jenis Kantong</th>
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

    switch ($sq_k1['dst_jenisktg']){
        case 1:$jenis='Kantong Single';break;
        case 2:$jenis='Kantong Double';break;
        case 3:$jenis='Kantong Triple';break;
        case 4:$jenis='Kantong Quadruple';break;
        case 6:$jenis='Kantong Pediatrik';break;
        default:$jenis="--";
    }

?>
        <tr style="background-color: #FFE6E6; font-size:12px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
            <td style="text-align: right;"> <?php echo $no.'.';?> </td>
            <td style="text-align: left;"> <?php echo $jenis;?> </td>
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
        <td colspan="2" style="text-align: center">Jumlah</td>
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
$usr1        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_pengirim]'"));
$pengirim   = $usr1['nama_lengkap'];
$usr2        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_penerima]'"));
$penerima   = $usr2['nama_lengkap'];
$usr3        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$sql_h1[hst_penerima2]'"));
$penerima2   = $usr3['nama_lengkap'];
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
        <td rowspan="4" style="vertical-align:top;">
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
        <td nowrap>Petugas Penerima Bag Komponen</td>
        <td nowrap><?php echo $penerima; ?></td>
		<td></td>
        <td></td>
    </tr>
    <tr style="background-color: #FFE6E6; font-size:14px; color:#000000; font-family:'trebuchet ms', Impact, Arial, Helvetica, sans-serif;">
        <td nowrap>Petugas Penerima Bag IMLTD</td>
        <td nowrap><?php echo $sql_h1['hst_penerima2']; ?></td>
		<td></td>
        <td></td>
    </tr>
</table>
<hr style="width: 100%;text-align:left;margin-left:0; line-height: 1px" >
<input type="submit" name="sim" value="Simpan Proses Terima" class="swn_button_blue">
</form>
</body>
