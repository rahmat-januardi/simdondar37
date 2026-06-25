<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=qc-tcaf.xls");
header("Pragma: no-cache");
header("Expires: 0");
require_once('clogin.php');
require_once('config/db_connect.php');

$tglawal    	=$_GET[tgl1];
$hariini    	=$_GET[tgl2];
$v_ptgs		=$_GET[ptgs];
$v_stts		=$_GET[stts];
$src_hasilqc	=$_GET[hasil];
$src_utd	=$_GET[utd];
?>
<style>
.str{mso-number-format:\@;}
</style>

<font size="4" color=00008B></b></font><br><br>
	<!--Awal Header kop surat-->
	<table border=1 cellpadding=4  style="border-collapse:collapse">
        <tr style="font-size:12px; color:#000000;">
	    <th rowspan="4" colspan="3" >UDD PUSAT PMI</th>
  	    <th colspan="2" colspan="8" >LEMBAR DATA ANALISA UJI MUTU KOMPONEN DARAH TROMBOSIT AFERESIS (TC AFERESIS)</th>
	    <th rowspan="4" align="left" style="width:155px;">Halaman <br/>Nomor <br/>Versi <br/>Tanggal Berlaku <br/>Tanggal Kajiulang</th>
	    <th rowspan="4" colspan="2" align="left" style="width:190px;">: 1 dari 1 <br/>: UDDP-PM-L3-051 <br/>: 004 <br/>: 01/07/2020 <br/>: 01/07/2022</th>
	    
	</tr>
	
        <tr style="font-size:12px; color:#000000;">
            <th rowspan="3" colspan="4" >Bidang Litbang & Produksi</th>
	    <th rowspan="3" colspan="4" >Sub. Bidang Pengawasan Mutu</th>
	    	
	 </tr>

	<tr style="font-size:12px; color:#000000;">
	 </tr>
	 </table><br/>
	 <!--Akhir Header kop surat-->

	<!--awal nama utd-->
	<?php
	
	$detail=mysql_fetch_assoc(mysql_query("SELECT q.`qcchecker`, q.`qcuser`, q.`qctgl`, q.`jenis`, q.`nokantong`, q.`gol_darah`, q.`RhesusDrh`, q.`tglaftap`,
        q.`kadaluwarsa`, q.`berat_isi`, q.`volume`, q.`ph`, q.`swirling`, q.`hemoglobin`, q.`aerob`, q.`anaerob`, 
	q.`anaerob` , a.`asal_utd`, a.`tgl`, s.`nama`, a.`tgl_pengolahan`
                FROM `qc` q
                LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong`
		LEFT JOIN `stokkantong` b on q.`nokantong`=b.`noKantong`
                LEFT JOIN utd s ON a.`asal_utd` = s.id
                WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' and q.`qc_status` like '$src_hasilqc%' and a.`asal_utd` like '$src_utd%'
                order by q.`notrans` asc"));
	//tgl terima
   	if ($detail[tgl]==NULL) $tglterima='-';
   	if ($detail[tgl]) $tglterima=date("d F Y",strtotime($detail[tgl]));

	//periode
   	if ($detail[tgl]==NULL) $periode='-';
   	if ($detail[tgl]) $periode=date("F Y",strtotime($detail[tgl]));

	// menghitung jumlah sample
	$detail_count=mysql_query("SELECT q.`qcchecker`, q.`qcuser`, q.`qctgl`, q.`jenis`, q.`nokantong`, q.`gol_darah`, q.`RhesusDrh`, q.`tglaftap`,
        q.`kadaluwarsa`, q.`berat_isi`, q.`volume`, q.`ph`, q.`swirling`, q.`hemoglobin`, q.`aerob`, q.`anaerob`, 
	q.`anaerob` , a.`asal_utd`, a.`tgl`, s.`nama`
                FROM `qc` q
                LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong`
                LEFT JOIN utd s ON a.`asal_utd` = s.id
                WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' and q.`qc_status` like '$src_hasilqc%' and a.`asal_utd` like '$src_utd%'
                order by q.`notrans` asc");
	

	?>
	<table style="border:0px;">
	<tr>
	<font size="2" color=black>Nama UDD &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b> <?=$detail['nama']?></b></font><br/>
	
	<font size="2" color=black>Tanggal Terima  : <b> <?=$tglterima?></b></font><br/>
	
	<font size="2" color=black>Periode &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :
	 <b>Bulan <?=$periode?></b></font><br/>
	
	<font size="2" color=black>Jumlah Sampel &nbsp;:
	 <b><?=mysql_num_rows($detail_count)?> Kantong</b></font>
	</tr>
	
	</table>
	<!--akhir nama utd-->


<table border=1 cellpadding=4>
<tr style="font-size:12px; color:#000000;">
	    <th rowspan="3">No.</th>
	    <th rowspan="3">Tanggal Periksa</th>
  	    <th colspan="9">Pemeriksaan Fisik</th>
	    <th rowspan="2" colspan="2">Pemeriksaan Hematologi</th>
	    <th colspan="2" rowspan="2">Pemeriksaan Kontaminasi Bakteri</th>
        </tr>

	<tr style="font-size:12px; color:#000000;">
            <th rowspan="2">Jenis Kantong</th>
	    <th rowspan="2">No. Kantong</th>
	    <th rowspan="2">Gol Darah</th>
	    <th rowspan="2">Tgl Pembuatan</th>
	    <th rowspan="2">Tgl Kedaluwarsa</th>
	    <th rowspan="2">Berat <br/> (gr)</th>
	    <th rowspan="2">Volume<br/> 100 - 400 mL</th>
	    <th rowspan="2">pH<br/> > 6,4</th>
	    <th rowspan="2">Swirling<br/> (Ada)</th>
	    
	 </tr>

         <tr style="font-size:12px; color:#000000;">
            <th>Trombosit / Unit<br/> ( x 10<sup>11</sup> ) </th>
	    <th>Leukosit<br/> ( x 10<sup>6</sup>)</th>
	    <th>Aerob</th>
	    <th>An-Aerob</th>
	    
	 </tr>


	<?php
	$no=0;
	$notrans     = $_GET['nokantong'];

	$sql="SELECT q.`qcchecker`, q.`qcuser`, q.`qctgl`, q.`jenis`, q.`nokantong`, q.`gol_darah`, q.`RhesusDrh`, q.`tglaftap`,
        q.`kadaluwarsa`, q.`berat_isi`, q.`volume`, q.`ph`, q.`swirling`, q.`hemoglobin`, q.`aerob`, q.`anaerob`, 
	q.`anaerob` , a.`asal_utd`, a.`tgl`, s.`nama`, a.`tgl_pengolahan`, q.`trombosit`, q.`leukosit`
                FROM `qc` q
                LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong`
		LEFT JOIN `stokkantong` b on q.`nokantong`=b.`noKantong`
                LEFT JOIN utd s ON a.`asal_utd` = s.id
                WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' and q.`qc_status` like '$src_hasilqc%' and a.`asal_utd` like '$src_utd%'
                order by q.`notrans` asc";


		  
	//echo "$sql";
	$qraw=mysql_query($sql);
    $statusrelease='';
	while($tmp=mysql_fetch_assoc($qraw)){$no++;
        switch ($tmp['status']){
            case '0' : $statusrelease='Lulus';break;
            case '1' : $statusrelease='Tidak Lulus';break;
            case '2' : $statusrelease='Lulus dengan Catatan';break;
            default  : $statusrelease='-';
        }

	//tgl pembuatan
   	if ($tmp[tgl_pengolahan]==NULL) $olah='-';
   	if ($tmp[tgl_pengolahan]) $olah=date("d-m-Y",strtotime($tmp[tgl_pengolahan]));

	//tgl kadaluwarsa
   	if ($tmp[kadaluwarsa]==NULL) $exp='-';
   	if ($tmp[kadaluwarsa]) $exp=date("d-m-Y",strtotime($tmp[kadaluwarsa]));

	//tgl periksa
   	if ($tmp[qctgl]==NULL) $periksa='-';
   	if ($tmp[qctgl]) $periksa=date("d-m-Y",strtotime($tmp[qctgl]));

	//swirling
	if ($tmp[swirling]==0) $swirling_ket='Ada';
	if ($tmp[swirling]==1) $swirling_ket='Tidak Ada';


		?>
        <tr style="font-size:11px; color:#000000; font-family:Verdana;">
		    
	    <td align="center"><?=$no.''?></td>
	    <td align="center"><?=$periksa?></td>
	    <td align="center"><?=$tmp['jenis']?></td>
	    <td align="center"><?=$tmp['nokantong']?></td>
	    <td align="center"><?=$tmp['gol_darah']?> <?=$tmp['RhesusDrh']?></td>
	    <td align="center"><?=$olah?></td>	
	    <td align="center"><?=$exp?></td>
	    <td class="str" align="center"><?=$tmp['berat_isi']?></td>
	    <td class="str" align="center"><?=$tmp['volume']?></td>
	    <td class="str" align="center"><?=$tmp['ph']?></td>
	    <td class="str" align="center"><?=$swirling_ket?></td>
	    <td class="str" align="center"><?=$tmp['trombosit']?></td>
	    <td class="str" align="center"><?=$tmp['leukosit']?></td>
	    <td align="center"><?=$tmp['aerob']?></td>
            <td align="center"><?=$tmp['anaerob']?></td>		
           
		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=31 align="center">Tidak ada data pemeriksaan QC</td>
	<?}?>

	    
	    <tr style="font-size:12px; color:#000000;">
            <th colspan="8" align="right">% Lulus</th>
	    <th>75%</th>
	    <th>75%</th>
	    <th>75%</th>
	    <th>75%</th>
	    <th>75%</th>
	    <th>100%</th>
	    <th>100%</th>
	    </tr>
	    
	    <?

$sqlwb=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
and q.`qc_status` like '$src_hasilqc%' and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$jumbariswb=mysql_num_rows($sqlwb);

//$sqlwb1=mysql_query("SELECT * FROM `qc`WHERE DATE(qctgl)>='$tglawal' AND date(qctgl)<='$hariini' and produk like '%WB%' and volume > 385 ") or die(mysql_error());
//$wb1=mysql_num_rows($sqlwb1);

//$sqlwb2=mysql_query("SELECT * FROM `qc`WHERE DATE(qctgl)>='$tglawal' AND date(qctgl)<='$hariini' and produk like '%WB%' and volume > 385 and volume > 385 ") or die(mysql_error());
//$vollebihwb=mysql_num_rows($sqlwb2);

//volume
$sqlwb3=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
AND q.`volume` >= 100 AND q.`volume` <=400 and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$volkurangwb=mysql_num_rows($sqlwb3);

//ph
$sqlwb4=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
AND q.`ph` >= 6.4 and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$ph=mysql_num_rows($sqlwb4);

//swirling
$sqlwb41=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
AND q.`swirling`='0' and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$swirling=mysql_num_rows($sqlwb41);

//hemoglobin
$sqlwb5=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
AND q.`hemoglobin` >= 45 and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$hbwb=mysql_num_rows($sqlwb5);


//aerob
$sqlwb6=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
AND q.`aerob`='negatif' and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$aerobwb=mysql_num_rows($sqlwb6);

//anaerob
$sqlwb7=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
AND q.`anaerob`='negatif' and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$anaerobwb=mysql_num_rows($sqlwb7);

//trombosit
$sqlwb8=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
AND q.`trombosit` >= 2 and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$hema1=mysql_num_rows($sqlwb8);

//leukosit
$sqlwb9=mysql_query("SELECT * FROM `qc` q LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong` 
WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' 
AND q.`leukosit` <= 1 and a.`asal_utd` like '$src_utd%'") or die(mysql_error());
$leuko1=mysql_num_rows($sqlwb9);


$jumkolomwb=2;
$persenwb=100;

//$kaliwb=$jumbariswb * $jumkolomwb;//
//$bagiwb= $persenwb / $kaliwb;//
//$ihwb=$wb1 * $bagiwb;
//$hslihwb=$persenwb - $ihwb;
//$volwb1=$vollebihwb * $bagiwb;
//$volwb2=$volkurangwb * $bagiwb;
//$hslwb1=$hslihwb - $volwb1;
//$hslakhrwb=$hslwb1-$volwb2;

//persentase hasil volume
$vol=$persenwb / $jumbariswb;
$vol1= $vol * $volkurangwb;

//persentase hasil ph
$phhasil=$persenwb / $jumbariswb;
$ph1= $phhasil * $ph;

//persentase hasil swirling
$swir=$persenwb / $jumbariswb;
$swir1= $swir * $swirling;


//persentase hasil trombosit
$persentrombo=$persenwb / $jumbariswb;
$hema2= $persentrombo * $hema1;

//persentase hasil leukosit
$persenleuko=$persenwb / $jumbariswb;
$leuko2= $persenleuko * $leuko1;

//persentase hasil aerob
$persenhb=$persenwb / $jumbariswb;
$aerobwb= $persenhb * $aerobwb;

//persentase hasil anaerob
$persenhb=$persenwb / $jumbariswb;
$anaerobwb= $persenhb * $anaerobwb;



?>
<tr style="font-size:12px; color:#000000;">
<td colspan="8" align="right"><b>Hasil</b></td>
<td align="center"><b><? echo number_format ($vol1,2); ?>%</b></td>
<td align="center"><b><? echo number_format ($ph1,2); ?>%</b></td>
<td align="center"><b><? echo number_format ($swir1,2); ?>%</b></td>
<td align="center"><b><? echo number_format ($hema2,2); ?>%</b></td>
<td align="center"><b><? echo number_format ($leuko2,2); ?>%</b></td>
<td align="center"><b><? echo number_format ($aerobwb,2); ?>%</b></td>
<td align="center"><b><? echo number_format ($anaerobwb,2); ?>%</b></td>

</tr>

</table>

<!--awal petugas-->
	<?php
	$petugas=mysql_fetch_assoc(mysql_query("SELECT q.`qcchecker`, q.`qcuser`, q.`qctgl`, q.`jenis`, q.`nokantong`, q.`gol_darah`, q.`RhesusDrh`, q.`tglaftap`,
        q.`kadaluwarsa`, q.`berat_isi`, q.`volume`, q.`ph`, q.`swirling`, q.`hemoglobin`, q.`aerob`, q.`anaerob`, 
	q.`anaerob` , a.`asal_utd`, a.`tgl`, s.`nama`
                FROM `qc` q
                LEFT JOIN `registrasi_qc` a on q.`nokantong`=a.`nokantong`
                LEFT JOIN utd s ON a.`asal_utd` = s.id
                WHERE DATE(q.`qctgl`)>='$tglawal' AND date(q.`qctgl`)<='$hariini' and q.`produk` like '%TC Aferesis' and q.`qc_status` like '$src_hasilqc%' and a.`asal_utd` like '$src_utd%'
                order by q.`notrans` asc"));
	?>
<br/>
<table align="right" border=1 cellpadding=4  style="border-collapse:collapse">
        <tr style="font-size:12px; color:#000000;">
	    <th align="left">Catatan : *Standar Kelulusan* <br/>Kadar Trombosit/Unit = > 2 x 10<sup>11</sup>
	    <br/>Kadar Leukosit = < 1 x 10<sup>6</sup></th>
	</tr><br>
</table>
<br/>

<table style="border:0px;">
<tr>
<font size="2" color=black>Diperiksa Oleh &nbsp; : <b> <?=$petugas['qcchecker']?></b></font><br><br>
</tr>
<tr>
<font size="2" color=black>Dicek Oleh &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <b> <?=$petugas['qcuser']?></b></font><br><br>
</tr>

<tr>
<font size="2" color=black>Hasil : </font><br>
</tr>
<tr>
<font size="2" color=black><b>* </font><br>
</tr>

<tr>
<font size="2" color=white>.</font>
</tr>

<tr>
<font size="2" color=black>Penanggung Jawab </font><br/>
<font size="2" color=black>Kepala Sub Bidang Pengawasan Mutu </font>
</tr>

<tr>
<font size="2" color=white>.</font><br/>
<font size="2" color=white>.</font><br/>
<font size="2" color=white>.</font><br/>
</tr>

<tr>
<font size="2" color=black>(________________________________) </font>
</tr>

</table><br>
<!--akhir petugas-->
</body>
