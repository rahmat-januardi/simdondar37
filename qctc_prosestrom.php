<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$jamskr=new DateTime(date("Y-m-d H:i:s"));
$hariini = date("Y-m-d");
?>
<!DOCTYPE html>
<link href="modul/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="../css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="../css/style.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="modul/thickbox/thickbox.js"></script>
<script language="javascript">

function selectSupplier(Kode){
	  $('input[@name=kodeSup]').val(Kode);
	  tb_remove(); 
}
function selectKode(Kode){
	  $('input[@name=kode]').val(Kode);
 	  tb_remove(); 
	  dbar(Kode);
}
</script>

<script type="text/javascript">
function dbar(browser){
     var brg1;
     var brg2;
     var brg3;
     var brg4;
     var brg5;
     var brg6;
     var brg7;
	
          $.ajax({
                    url: "nama_pasien.php?no_rm="+browser,
                    async: false,
                    dataType: 'json',
                    success: function(json) {
			      brg1 	= json.barang.no_rm;
			      
			      
                    
	  
                    }
                });
	 
	  
        }
</script>

<html>
<head>
    <style>
        tr { background-color: #ffffff;}
        .initial { background-color: #ffffff; color:#000000 }
        .normal { background-color: #ffffff; }
        .highlight { background-color: #7CFC00 }
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
            border-radius: undefined%;
        }

        .control:hover input ~ .control_indicator,
        .control input:focus ~ .control_indicator {
            background: #cccccc;
        }

        .control input:checked ~ .control_indicator {
            background: #ff0000;
        }
        .control:hover input:not([disabled]):checked ~ .control_indicator,
        .control input:checked:focus ~ .control_indicator {
            background: #0e6647d;
        }
        .control input:disabled ~ .control_indicator {
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
        .control input:checked ~ .control_indicator:after {
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
        .control-checkbox input:disabled ~ .control_indicator:after {
            border-color: #7b7b7b;
        }
    </style>

<style type="text/css">
	.styled-select select {
        background-color: #F0FFFF; border: none;width: auto;padding: 3px;font-size: 16px;cursor: pointer;
    }
</style>
<style>
    table {
    border-collapse: collapse;
    }
    table, th, td {
    border: 1px solid brown;
    }
</style>
<style>
body {font-family: "Lato", sans-serif;}
.tablink {
    background-color: red;
    color: white;
    float: left;
    border: 1px solid brown;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    font-size: 15px;
    width: 50%;
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
#visual {background-color:white;}
#kantong {background-color:white;}
#pemeriksaan {background-color:white;}
#pengolahan {background-color:white;}
#trace {background-color:white;}
#history {background-color:white;}
</style>

</head>
<body>
<?php
if(isset($_POST['Button']))  {
    $id_timbang=$_GET['id'];
    $nkt=$_GET['nokantong'];
    $v_rtgl = date("Y-m-d H:i:s");
    $v_rproduk=$_POST['produk'];
    $v_merk=$_POST['merk'];
    $v_jenis=$_POST['jenis'];
    $v_goldarah=$_POST['gol_darah'];
    $v_rhesus=$_POST['rhesus'];
    $v_tglaftap=$_POST['tglaftap'];
    $v_kadaluwarsa=$_POST['kadaluwarsa'];
    $v_tgledkantong=$_POST['tgledkantong'];
    $v_beratisi=$_POST['beratisi'];
    $v_beratkosong=$_POST['beratkosong'];
    $v_beratjenis=$_POST['beratjenis'];
    $v_antikogulan=$_POST['antikogulan'];
    $v_volume=$_POST['volume'];
    $v_hct=$_POST['hct'];
    $v_plasma=$_POST['plasma'];
    $v_ttlhb=$_POST['ttlhb'];
    $v_hemolisis=$_POST['hemolisis'];
 	$v_hemolisis1=$_POST['hemolisis1'];
    
    $v_volhem=$_POST['volhem'];
    $v_kadhb=$_POST['kadhb'];
    $v_hemoglobin=$_POST['hemoglobin'];

    $v_lekosit=$_POST['lekosit'];
    $v_aerob=$_POST['aerob'];
    $v_anaerob=$_POST['anaerob']; 
    $v_ihbs=$_POST['ihbs']; 
    $v_ihcv=$_POST['ihcv'];
    $v_ihiv=$_POST['ihiv'];
    $v_isyp=$_POST['isyp'];   
    
    
    $v_qcuser=$_POST['petugas']; 
    $v_qcchecker=$_POST['dicek'];
    $v_qcpengesah=$_POST['disah'];           

    $v_ruser=$_POST['petugas'];
    $v_rchecker=$_POST['dicekoleh'];
    $v_rpengesah=$_POST['disahkanoleh'];
    $v_rstatus=$_POST['hasilqc'];

    $v_rleukosit1=$_POST['leukosit'];
    $v_rleukosit2=$_POST['leukosit2'];

    $v_rtrombosit1=$_POST['trombosit'];
    $v_rtrombosit2=$_POST['trombosit2'];

    $v_ph=$_POST['ph'];
    $v_swirling=$_POST['swirling'];

    $vi_hemolisis=(isset($_POST['vi_hemolisis'])) ? 1 : 0;
    $vi_lipemik=(isset($_POST['vi_lipemik'])) ? 1 : 0;
    $vi_penggumpalan=(isset($_POST['vi_penggumpalan'])) ? 1 : 0;
    $vi_warna=(isset($_POST['vi_warna'])) ? 1 : 0;
    $vi_tdk=(isset($_POST['vi_tdk'])) ? 1 : 0;

    switch($v_rstatus){
        case '0':$v_rsatus_ket='Lulus';$v_statusktg='1';break;
        case '1':$v_rsatus_ket='Tidak Lulus';$v_statusktg='2';break;
    }

    $v_rhasil_fisik=$_POST['hasil_fisik'];
    switch($v_rhasil_fisik){
        case '0':$v_rfisik_ket='Lulus';$v_statusktg='1';break;
        case '1':$v_rfisik_ket='Tidak Lulus';$v_statusktg='2';break;
    }

    $v_rhasil_hematologi=$_POST['hasil_hematologi'];
    switch($v_rhasil_hematologi){
        case '0':$v_rhematologi='Lulus';$v_statusktg='1';break;
        case '1':$v_rhematologi='Tidak Lulus';$v_statusktg='2';break;
    }
   
   
$histori=mysql_query("insert into histori (`username`,`waktu`,`action`,`level_editor`,`nokantong`) values ('$namauser','$v_rtgl','Selesai QC Produk','QC','$nkt')");

	$cek="select `nokantong`,`notrans` from `qc` where `nokantong`='$nkt'";
	$cek1=mysql_fetch_assoc(mysql_query($cek));
	$notrans_upd=$cek1['notrans'];
	if ($cek1['rnokantong']==$nkt){

	    //Save Upd release table
	    $sql="UPDATE `qc` SET
	        `berat`     ='$v_berat',
		`volume`    ='$v_volume',
		`inspeksihemolisis` ='$v_hemolisis',
		`hemoglobin`	    ='$v_hemoglobin',
		`lekosit`	    ='$v_lekosit',
		`aerob`		    ='$v_aerob',
		`anaerob`	    ='$v_anaerob'
          	 WHERE
			`notrans`='$notrans_upd' and `nokantong`='$nkt'";

	    $qact=mysql_query($sql);
       	    echo "PROSES <i>UPDATE</i> PRODUK RELEASE BERHASIL";
	    }else{


	    //Generated NoTransaksi===============================================
    	$sql_r	= mysql_query("SELECT MAX(CONVERT(notrans, SIGNED INTEGER)) AS Kode FROM `qc`");
	    $dta_r	= mysql_fetch_assoc($sql_r);
	    $int_r  = (int)($dta_r[Kode]);
	    $int_no=$int_r;
	    $int_no_inc=(int)$int_no+1;
	    $j_nol= 10-(strlen(strval($int_no_inc)));
    	for ($i=0; $i<$j_nol; $i++){$no_tmp .="0";}
	    $v_rnotrans = $no_tmp.$int_no_inc;
    	//echo "No. Transaksi :  ".$notrans." Tanggal Periksa : ".$today1." (".date_default_timezone_get().")<br>";
	    //------------ END Generate no transaksi ---------------


    	//====Save Add release table======
	    $tambah=mysql_query("update stokkantong set statQC='$v_statusktg' where nokantong='$nkt'");

	    $sql="INSERT INTO `qc`(`notrans`,`qctgl`,`nokantong`,`jenis`,`merk`,`gol_darah`,`RhesusDrh`,`produk`,`tglaftap`,`kadaluwarsa`,
`tgledkantong`,`berat_isi`,`berat_kosong`,`berat_jenis`,`antikogulan`,`volume`,`hct`,`plasma`,`ttlhb`,`hemolisis`,`hemolisis_manual`,
`volhem`,`kadhb`,`hemoglobin`,`aerob`,`anaerob`,`i-hbs`,`i-hcv`,
`i-hiv`,`i-syp`,`qcuser`,`qcchecker`,`qcpengesah`,`qc_status`,`hsl_periksa_fisik`,`hasil_periksahem`,`v_hemolisis`,`v_lipemik`,`v_penggumpalan`,`v_warna`,`v_tdk`,`leukosit`,`leukosit_manual`,`trombosit`,`trombosit_manual`,`ph`,`swirling`)
          VALUES ('$v_rnotrans','$v_rtgl','$nkt','$v_jenis','$v_merk','$v_goldarah','$v_rhesus','$v_rproduk','$v_tglaftap','$v_kadaluwarsa',
'$v_tgledkantong','$v_beratisi','$v_beratkosong','$v_beratjenis','$v_antikogulan','$v_volume','$v_hct','$v_plasma','$v_ttlhb','$v_hemolisis','$v_hemolisis1','$v_volhem','$v_kadhb','$v_hemoglobin','$v_aerob','$v_anaerob','$v_ihbs',
'$v_ihcv','$v_ihiv','$v_isyp','$v_qcuser','$v_qcchecker',
'$v_qcpengesah','$v_rsatus_ket','$v_rfisik_ket','$v_rhematologi','$vi_hemolisis','$vi_lipemik','$vi_penggumpalan','$vi_warna','$vi_tdk','$v_rleukosit1','$v_rleukosit2','$v_rtrombosit1','$v_rtrombosit2','$v_ph','$v_swirling')";
	    $qact=mysql_query($sql);
        echo "Entry Data Berhasil";
    }

    
    if ($mode_kembali==1){
        echo "<meta http-equiv='refresh' content='2;url=pmiqc.php?module=menu_tc'>";
    } else{
        echo "<meta http-equiv='refresh' content='2;url=pmiqc.php?module=menu_tc'>";
    }

} //post
    ?>
    <button class="tablink" onclick="bukatab('kantong', this, 'Blue')" id="defaultOpen">Identitas Kantong</button>
    <!--<button class="tablink" onclick="bukatab('pemeriksaan', this, 'Blue')">Screening IMLTD & KGD</button>-->
    <button class="tablink" onclick="bukatab('visual', this, 'Blue')" >Pemeriksaan</button> 


<form name="form_prolis" align="left" method="post" action="<?echo $PHPSELF?>">

<script>
function hitung(){
	//perhitungan volume
	var stok = eval(document.form_prolis.beratisi.value);
	var update = eval(document.form_prolis.beratkosong.value);
        var update1 = eval(document.form_prolis.beratjenis.value);
	
	if (document.form_prolis.korek[1].checked==true) {
        var rumus1= stok - update; } 
        var rumus2= rumus1 / update1;
        document.form_prolis.volume.value=rumus2;

	//perhitungan hemolisis
	//var hct = eval(document.form_prolis.hct.value);
	//var plasma = eval(document.form_prolis.plasma.value);
	//var ttlhb = eval(document.form_prolis.ttlhb.value);

	//var totalhct= 100 - hct;
	//document.form_prolis.hct1.value=totalhct;
	//var totalhct2= totalhct * plasma / ttlhb;
	//document.form_prolis.hemolisis.value=totalhct2;

	
	//perhitungan hemoglobin	
	//var volhem = eval(document.form_prolis.volhem.value);
	//var kadhb = eval(document.form_prolis.kadhb.value);
	//var hemoglobin1= volhem * kadhb;
	//document.form_prolis.hemoglobin.value=hemoglobin1;

	//perhitungan leukosit
	var volhem = eval(document.form_prolis.volhem.value);
	var wbc = eval(document.form_prolis.wbc.value);
	var leukosit1= volhem * wbc;
	document.form_prolis.leukosit.value=leukosit1;

	//perhitungan trombosit
	var volhem = eval(document.form_prolis.volhem.value);
	var platelet = eval(document.form_prolis.platelet.value);
	var platelet1= volhem * platelet;
	document.form_prolis.trombosit.value=platelet1;
}
</script>


    <div id="kantong" class="tabcontent">
        <font size="4" color=00008B><br>DATA KANTONG DARAH</font>
        <? include "QC/qcprc_kantong.php";?>
    </div>

    <!--<div id="pemeriksaan" class="tabcontent">
        <font size="4" color=00008B><br>PEMERIKSAAN SCREENING IMLTD</font>
        <? include "QC/qcprc_hematologi.php";?>
    </div>-->

     
    <div id="visual" class="tabcontent">
        <font size="4" color=00008B><br>PEMERIKSAAN FISIK</font>
        <table cellpadding=4 cellspacing=4 width="100%" style="border: 0px; border-color: #ffffff;">
            <tr>
                    <td valign="top">
                    <table width="100%" cellpadding="5" cellspacing="5">
		    <input type='hidden' name='produk' value='<?=$jeniskomponen?>'>
		    <input type='hidden' name='jenis' value='<?=$jeniskantong?>'>
		    <input type='hidden' name='merk' value='<?=$merk?>'>
		    <input type='hidden' name='gol_darah' value='<?=$goldarah?>'>
		    <input type='hidden' name='rhesus' value='<?=$rhesusDrh?>'>
		    <input type='hidden' name='tglaftap' value='<?=$aftap?>'>
		    <input type='hidden' name='kadaluwarsa' value='<?=$kadaluwarsa?>'>
		    <input type='hidden' name='tgledkantong' value='<?=$edkantong?>'>
		    <input type='hidden' name='ihbs' value='Non Reaktif'>
		    <input type='hidden' name='ihcv' value='Non Reaktif'>
		    <input type='hidden' name='ihiv' value='Non Reaktif'>
		    <input type='hidden' name='isyp' value='Non Reaktif'>

<tr>
	
	<td style="background-color: mistyrose" colspan="2">Berat Kantong Isi</td>
	<td class="input"><input onchange="hitung()" name="beratisi" type="text" size="5" >gram</td>
	</tr>
<tr>
     <td style="background-color: mistyrose" colspan="2">Berat Kantong Kosong</td> <td>
	<input type="hidden" name="korek" value="plus">
        <input type="hidden" name="korek" value="min" checked="checked">
	<input  onchange="hitung()" name="beratkosong" value="<?=$beratkantongkosong?>" type="text" size="5" placeholder="Isi Angka" required="" readonly="readonly">gram</td>
        </tr>
<tr>
     <td style="background-color: mistyrose" colspan="2">Berat Jenis</td> <td>
	<input type="hidden" name="korek" value="plus">
        <input type="hidden" name="korek" value="min" checked="checked">
	<input  onchange="hitung()" name="beratjenis" type="text" size="5" value="<?=$beratjenis?>" placeholder="Isi Angka" required="" readonly="readonly">gram</td>
        </tr>


<tr>
        <td style="" colspan="2">Hasil Perhitungan Volume</td>
        <td class="input"><input name="volume" type="text" size="2" readonly="readonly">ml</td>
	
        </tr>

<tr>
	
	<td style="" colspan="2">Ph</td>
	<td class="input"><input type="text" name="ph" type="text" size="5" ></td>
	</tr>

<tr><td style="" colspan="2">Swirling</td>
                            <td>
                                    <select id="swirling" name="swirling" class="styled-select">
				    <option value="3">-</option>
                                    <option value="0">Ada</option>
                                    <option value="1">Tidak Ada</option>
                                </select>
                    </td></tr>


		    


		    
		    <!--<tr><td style="background-color: mistyrose" colspan="2">HCT</td> <td><input onchange="hitung()" type="text" size="5" placeholder="Isi Angka" name="hct">
		     <input onchange="hitung()" type="hidden" size="5" name="hct1" readonly="readonly"></td></tr>

		    <tr><td style="background-color: mistyrose" colspan="2">Total Plasma Low HB</td> <td><input onchange="hitung()" type="text" size="5" placeholder="Isi Angka" name="plasma"></td></tr>

		    <tr><td style="background-color: mistyrose" colspan="2">Total HB</td> <td><input onchange="hitung()" type="text" size="5" placeholder="Isi Angka" name="ttlhb"></td></tr>

		   <tr><td style="" colspan="2">Hasil Perhitungan Hemolisis</td> <td><input onchange="hitung()" type="text" size="2" name="hemolisis" readonly="readonly">%
                   <input type="text" size="35" placeholder="Isi Disini Bila Dilakukan Secara Manual" name="hemolisis1">%</td></tr>-->

		   <tr>
		   <td style="" colspan="2">Perubahan Visual</td>
                   <td>
		   <input type="checkbox" name="vi_hemolisis" id="vi_hemolisis" />Hemolisis<div class="control_indicator"></div><br/>
		   <input type="checkbox" name="vi_lipemik" id="vi_lipemik" />Lipemik<div class="control_indicator"></div><br/>
		   <input type="checkbox" name="vi_penggumpalan" id="vi_penggumpalan" />Penggumpalan<div class="control_indicator"></div><br/>
                   <input type="checkbox" name="vi_warna" id="vi_warna" />Perubahan Warna<div class="control_indicator"></div><br/>
		   <input type="checkbox" name="vi_tdk" id="vi_tdk" />Tidak Ada<div class="control_indicator"></div>
		   </td>
		   </tr>

		   <tr><td style="background-color: mistyrose" colspan="2">Hasil Pemeriksaan Fisik</td>
                            <td>
                                    <select id="hasil_fisik" name="hasil_fisik" class="styled-select">
				    <option value="3">-</option>
                                    <option value="0">Lulus</option>
                                    <option value="1">Tidak Lulus</option>
                                </select>
                    </td></tr>
		   
                    </table>

                </td>
            <tr>
        </table>


<br/><br/>
<font size="4" color=00008B><br>PEMERIKSAAN HEMATOLOGI</font>
<table cellpadding=4 cellspacing=4 width="100%" style="border: 0px; border-color: #ffffff;">
            <tr>
                    <td valign="top">
                    <table width="100%" cellpadding="5" cellspacing="5">
		     
		    <tr><td style="background-color: mistyrose" colspan="2">Volume</td> <td><input type="text" onchange="hitung()" placeholder="Isi Angka" name="volhem" required="" size="5">g/unit</td></tr>
		    <!--<tr><td style="background-color: mistyrose" colspan="2">Kadar HB</td> <td><input type="text" onchange="hitung()" placeholder="Isi Angka" name="kadhb" required="" size="5">g/unit</td></tr>
		    <tr><td style="" colspan="2">Hemoglobin</td> <td><input type="text" onchange="hitung()" readonly="readonly" name="hemoglobin" size="5">g/unit</td></tr>-->

		    
		    <tr><td style="background-color: mistyrose" colspan="2">Kadar WBC</td> <td><input type="text" onchange="hitung()" placeholder="Isi Angka" name="wbc" required="" size="5">g/unit</td></tr>
		    <tr><td style="" colspan="2">Leukosit</td> <td><input type="text" onchange="hitung()" readonly="readonly" name="leukosit" size="5">g/unit
		    <input type="text" size="35" placeholder="Isi Disini Bila Dilakukan Secara Manual" name="leukosit2">g/unit</td>
		    </tr>

		    <tr><td style="background-color: mistyrose" colspan="2">Kadar Platelet</td> <td><input type="text" onchange="hitung()" placeholder="Isi Angka" name="platelet" required="" size="5">g/unit</td></tr>
		    <tr><td style="" colspan="2">Trombosit</td> <td><input type="text" onchange="hitung()" readonly="readonly" name="trombosit" size="5">g/unit
		    <input type="text" size="35" placeholder="Isi Disini Bila Dilakukan Secara Manual" name="trombosit2">g/unit</td>
		    </tr>

		    <tr><td style="background-color: mistyrose" colspan="2">Hasil Pemeriksaan Hematologi</td>
                            <td>
                                    <select id="hasil_hematologi" name="hasil_hematologi" class="styled-select">
				    <option value="3">-</option>
                                    <option value="0">Lulus</option>
                                    <option value="1">Tidak Lulus</option>
                                </select>
                    </td></tr>

                    </table>
		    </table>


<br/><br/>
<font size="4" color=00008B><br>PEMERIKSAAN KONTAMINASI BAKTERI</font>
<table cellpadding=4 cellspacing=4 width="100%" style="border: 0px; border-color: #ffffff;">
            <tr>
                    <td valign="top">
                    <table width="100%" cellpadding="5" cellspacing="5">
		    <tr><td style="background-color: mistyrose" colspan="2">Aerob</td>
                            <td>
                                    <select id="aerob" name="aerob" class="styled-select">
				    <option value="-">-</option>
                                    <option value="positif">Positif</option>
                                    <option value="negatif">Negatif</option>
                                </select>
                    </td></tr>


		    <tr><td style="background-color: mistyrose" colspan="2">Anaerob</td>
                            <td>
                                    <select id="anaerob" name="anaerob" class="styled-select">
				    <option value="-">-</option>
                                    <option value="positif">Positif</option>
                                    <option value="negatif">Negatif</option>
                                </select>
                    </td></tr>


		    <tr><td style="background-color: mistyrose" colspan="2">Hasil QC</td>
                            <td>
                                    <select id="hasilqc" name="hasilqc" class="styled-select">
				    <option value="3">-</option>
                                    <option value="0">Lulus</option>
                                    <option value="1">Tidak Lulus</option>
                                </select>
                    </td></tr>

			<tr><td style="background-color: mistyrose" colspan="2">Petugas Yg Mengerjakan</td><td><?echo $namalengkap;?></td>
                            <input type="hidden" name="petugas" value=<?=$namalengkap?>></tr>
                        <tr><td style="background-color: mistyrose" colspan="2">Dicek Oleh</td>
                            <td>
                                <select name="dicek" class="styled-select"> <?
                                    $user1="select * from user where level like '%qc%' order by nama_lengkap ASC";
                                    $do1=mysql_query($user1);
                                    while($data1=mysql_fetch_assoc($do1)) {
                                        if ($data1[id_user]==$data_combo[petugas1]){
                                            $select=" selected";
                                        } else{
                                            $select="";
                                        }?>
                                        <option value="<?=$data1[nama_lengkap]?>"<?=$select?>><?=$data1[nama_lengkap]?></option><?
                                    }?>
                                </select>
                            </td></tr>

			<tr><td style="background-color: mistyrose" colspan="2">Disahkan Oleh</td>
                            <td>
                                <select name="disah" class="styled-select"> <?
                                    $user1="select * from user where level like '%qc%' order by nama_lengkap ASC";
                                    $do1=mysql_query($user1);
                                    while($data1=mysql_fetch_assoc($do1)) {
                                        if ($data1[id_user]==$data_combo[petugas1]){
                                            $select=" selected";
                                        } else{
                                            $select="";
                                        }?>
                                        <option value="<?=$data1[nama_lengkap]?>"<?=$select?>><?=$data1[nama_lengkap]?></option><?
                                    }?>
                                </select>
                            </td></tr>


                    </table>
                </td>
            <tr>
        </table>

    </div>
    <?
    if ($mode_kembali==1){
        ?><a href="pmiqc.php?module=menu_tc"class="swn_button_blue">Kembali</a><?
    }else{
        ?><a href="pmiqc.php?module=menu_tc"class="swn_button_blue">Kembali</a><?
    }
    ?>

    <!--<a href="pmiqc.php?module=input_qc"class="swn_button_blue">Kembali ke awal</a>-->
    <input type="submit" name="Button" value="Simpan" title="Proses kantong" class="swn_button_red">
    <? if ($data_combo['petugas3']=='1'){?>
        <!--<input type="checkbox" class="checkbox-custom" name="cetak" id="cetak" value="1" checked>
            <label for="cetak" class="checkbox-custom-label">Cetak Label saat menyimpan</label><br>-->
    <?} else {?>
        <!--<input type="checkbox" class="checkbox-custom" name="cetak" id="cetak" value="1">
        <label for="cetak" class="checkbox-custom-label">Cetak Label saat menyimpan</label><br>-->
    <?}?>
</form>

<script>
function bukatab(namatab,elmnt,color) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
    }
    document.getElementById(namatab).style.display = "block";
    elmnt.style.backgroundColor = color;
}
document.getElementById("defaultOpen").click();
</script>
</body>
</html>
