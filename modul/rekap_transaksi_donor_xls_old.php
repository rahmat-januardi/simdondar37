<?
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_transaksi_darah.xls");
header("Pragma: no-cache");
header("Expires: 0");

include('../config/db_connect.php');




$namauser=$_POST[namauser];
$today      	=$_POST[today];
$today1     	=$_POST[today1];
$src_nomorf   	=$_POST[instansi];
$src_status   	=$_POST[status];
$src_ambil    	=$_POST[ambil];
$src_shift    	=$_POST[shift];
$src_ktg      	=$_POST[ktg];
$src_drh      	=$_POST[drh];
$src_jk       	=$_POST[jk];
$src_rh      	=$_POST[rh];
$src_ds       	=$_POST[ds];
$src_baru       =$_POST[baru];
$hasil		=$_POST[hasil];

?>
<h1>REKAP TRANSAKSI DONOR</h1>
<h3>Dari Tanggal : <?=$today?>  s/d <?=$today1?></h1>


<?
$whereStatus = "";
if (isset($src_status) && $src_status !== "") {
    $status = $_POST['status'];

    switch ($status) {
        case "0":
            $whereStatus = "AND COALESCE(Pengambilan, '') = '0'";
            break;
        case "1":
            $whereStatus = "AND COALESCE(Pengambilan, '') = '1'";
            break;
        case "2":
            $whereStatus = "AND (COALESCE(Pengambilan, '') = '2' OR jumHB IN ('2','3','4'))";
            break;
        case "3":
            $whereStatus = "AND COALESCE(Pengambilan, '') = '3' AND jumHB = '1'";
            break;
        case "4":
            $whereStatus = "AND jumHB IN ('2','3','4')";
            break;
    }
}

$transaksipermintaan=mysql_query("select *from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' $whereStatus and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' order by NoTrans ASC ");
?>

<?
$countp=mysql_num_rows($transaksipermintaan);
echo"<br><br>";
echo"Sejumlah :  ";
echo"<b>";
echo $countp;
echo"</b>";
echo " data";
?>
</h1>

<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="100%">
<tr>          
	<!--th colspan=12><b>Total = <?$TRec?> Kantong</b></th></tr><tr class="field"-->	
	<td rowspan='2' align="center">No</td>
	<td rowspan='2' align="center">NoTrans</td>
	<td rowspan='2' align="center">Tanggal</td>
	<td colspan='11' align="center">Pendonor</td>
	<td colspan='10' align="center">Aftap</td>
	<td colspan='5' align="center">Piagam</td>	
	<td colspan='5' align="center">Petugas</td>
	
	
        </tr>
	<tr>
	<td align="center">ID</td>
	<td align="center">Nama</td>
	<td align="center">Alamat</td>		
	<td align="center">HP</td>
	<td align="center">Umur</td>
	<td align="center">Gol<br>(Rh)</td>
	<td align="center">JK</td>
	<td align="center">DS<br>DP</td>
	<td align="center">Baru<br>Ulang</td>
	<td align="center">Donor<br>Ke-</td>
	<td align="center">Jam Antri</td>

	<td align="center">Jenis</td>
	<td align="center">No<br>Kantong</td>
	<td align="center">Penge-<br>sahan</td>
	<td align="center">Jam Aftap</td>
	<td align="center">Status</td>
	<td align="center">Cara Ambil</td>
	<td align="center">CC</td>
	<td align="center">Shift</td>
	<td align="center">DG<br>MU</td>
	<td align="center">Instansi</td>
	

			<td align="center">10x</td>
			<td align="center">25x</td>
			<td align="center">50x</td>
			<td align="center">75x</td>
			<td align="center">100x</td>

	<td align="center">Dokter</td>
        <td align="center">Tensi</td>
	<td align="center">Hb</td>
	<td align="center">Aftap</td>
        <td align="center">Input</td>
        

	</tr>


<?
$no=1;
while ($datatransaksipermintaan=mysql_fetch_array($transaksipermintaan)){
?>


<tr>
	<td align="center"><?=$no?></td>
	<td align="center"><?=$datatransaksipermintaan['NoTrans']?></td>
	<td align="center"><?=$datatransaksipermintaan['Tgl']?></td>
	<td align="center"><?=$datatransaksipermintaan['KodePendonor']?></td>
	
	<? 	
	$kantong0=mysql_query("select * from stokkantong where noKantong='$datatransaksipermintaan[NoKantong]'");
	$kantong=mysql_fetch_array($kantong0);
	$pendonor0=mysql_query("select * from pendonor where Kode='$datatransaksipermintaan[KodePendonor]'");
	$pendonor=mysql_fetch_array($pendonor0);
	$jamantri=substr($datatransaksipermintaan[Tgl],11);
	$jamaftap=substr($kantong[tgl_Aftap],11);
	if ($datatransaksipermintaan[jk]==0) $jk='Pria';
	if ($datatransaksipermintaan[jk]==1) $jk='Wanita';
$peng='Antri';
if ($datatransaksipermintaan[jumHB]=='1') $peng='Lolos MCU';
if ($datatransaksipermintaan[jumHB]=='2') $peng='Gagal MCU';
if ($datatransaksipermintaan[jumHB]=='3') $peng='Gagal MCU';
if ($datatransaksipermintaan[jumHB]=='4') $peng='Gagal MCU';
if ($datatransaksipermintaan[Pengambilan]=='0') $peng='Berhasil';
if ($datatransaksipermintaan[Pengambilan]=='2') $peng='Gagal';
if ($datatransaksipermintaan[Pengambilan]=='1') $peng='Batal';

if ($datatransaksipermintaan[caraAmbil]=='0') $caraambil='Biasa';
if ($datatransaksipermintaan[caraAmbil]=='1') $caraambil='Tromboferesis';
if ($datatransaksipermintaan[caraAmbil]=='2') $caraambil='Leukaferesis';
if ($datatransaksipermintaan[caraAmbil]=='3') $caraambil='Plasmaferesis';
if ($datatransaksipermintaan[caraAmbil]=='4') $caraambil='Eritoferesis';

if ($datatransaksipermintaan[JenisDonor]=='0') $ds='DS';
if ($datatransaksipermintaan[JenisDonor]=='1') $ds='DP';
if ($datatransaksipermintaan[JenisDonor]=='2') $ds='Autologus';

if ($datatransaksipermintaan[donorbaru]=='0') $baru='Baru';
if ($datatransaksipermintaan[donorbaru]=='1') $baru='Ulang';

    switch ($datatransaksipermintaan['metoda']){
//            case "BS":  $metkantong ="BIASA";        break;
//            case "FT":  $metkantong ="FILTER";       break;
        case "TTB":  $metkantong ="TOP & TOP (Biasa)";    break;
        case "TTF":  $metkantong ="TOP & TOP (Filter)";    break;
        case "TBB":  $metkantong ="TOP & BOTTOM (Biasa)"; break;
        case "TBF":  $metkantong ="TOP & BOTTOM (Filter)"; break;
    }

switch($datatransaksipermintaan[jeniskantong]) {
case '1':
	$jenis='Single';
	break;
case '2':
	$jenis='Double';
	break;
case '3':
	$jenis='Triple';
	break;
case '4':
	$jenis='Quadruple ('.$metkantong.')';
	break;
case '6':
	$jenis='Pediatrik';
	break;
default:
	$jenis='';
}

if ($kantong[sah]=='0') $sah1='Belum';
if ($kantong[sah]==0 and $datatransaksipermintaan[NoKantong]==NULL) $sah1='-';
if ($kantong[sah]=='1') $sah1='Sudah';
	?>

	<td align="left"><?=$pendonor['Nama']?></td>
	<td align="center"><?=$pendonor['Alamat']?></td>
	<td align="center"><?=$pendonor['telp2']?></td>
	<td align="center"><?=$datatransaksipermintaan['umur']?></td>
	<td align="center"><?=$datatransaksipermintaan['gol_darah']?><?=$datatransaksipermintaan['rhesus']?></td>
	<td align="center"><?=$jk?></td>
	<td align="center"><?=$ds?></td>
	<td align="center"><?=$baru?></td>
	<td align="center"><?=$datatransaksipermintaan['donorke']?></td>
	<td align="center"><?=$jamantri?></td>	
	
	<td align="center"><?=$jenis?></td>
	<td align="center"><?=$datatransaksipermintaan['NoKantong']?></td>
	<!--? echo "<td align=center><a href=pmi".$_SESSION[leveluser].".php?module=updatekantong&noKantong=".$datatransaksipermintaan[NoKantong].">".$datatransaksipermintaan[NoKantong]."</a></td>"; ?-->
	<td align="center"><?=$sah1?></td>
	<td align="center"><?=$jamaftap?></td>
	<td align="center"><?=$peng?></td>
	<td align="center"><?=$caraambil?></td>
	<td align="center"><?=$datatransaksipermintaan['volumekantong']?></td> 
	<td align="center"><?=$datatransaksipermintaan['shift']?></td> 
	<?
	if ($datatransaksipermintaan[tempat]=='M') $tempat1='MU';
	if ($datatransaksipermintaan[tempat]!='M') $tempat1='DG';
	?>

	<td align="center"><?=$tempat1?></td>
	<td align="center"><?=$datatransaksipermintaan['Instansi']?></td>


<?
$p10='Sdh';
if ($pendonor['jumDonor'] >9 and $pendonor['p10']==0) $p10='Blm';
if ($pendonor['jumDonor'] <10) $p10='-';
$p25='Sdh';
if ($pendonor['jumDonor'] >24 and $pendonor['p25']=='0')$p25='Blm';
if ($pendonor['jumDonor']<25) $p25='-';
$p50='Sdh';
if ($pendonor['jumDonor'] >49 and $pendonor['p50']=='0')$p50='Blm';
if ($pendonor['jumDonor']<50) $p50='-';
$p75='Sdh';
if ($pendonor['jumDonor'] >74 and $pendonor['p75']=='0')$p75='Blm';
if ($pendonor['jumDonor']<75) $p75='-';
$p100='Sdh';
if ($pendonor['jumDonor'] >99 and $pendonor['p100']=='0')$p100='Blm';
if ($pendonor['jumDonor']<100) $p100='-';
?>
	<td class=input align="right"><?=$p10?></td>
	<td class=input align="right"><?=$p25?></td>
	<td class=input align="right"><?=$p50?></td>
	<td class=input align="right"><?=$p75?></td>
	<td class=input align="right"><?=$p100?></td>



<?
$dokter=mysql_fetch_assoc(mysql_query("select Nama from dokter_periksa where kode='$datatransaksipermintaan[NamaDokter]'"));
?>
	<td class=input><?=$dokter[Nama]?></td>

        <td align="center"><?=$datatransaksipermintaan['petugasTensi']?></td>
	<td align="center"><?=$datatransaksipermintaan['petugasHB']?></td>
	<!--? 	
	$kantong1=mysql_query("select * from stokkantong where NoKantong='$datatransaksipermintaan[NoKantong]'");
	$ambilkantong1=mysql_fetch_array($kantong1);
	
	?-->
	<td align="center"><?=$datatransaksipermintaan['petugas']?></td>
	<td align="center"><?=$datatransaksipermintaan['user']?></td>
	
	
</tr>
<? $no++;} ?>
</table>

<!--

<?
$jum=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as kod from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' 
and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' "));

//PENGAMBILAN BERHASIL DG

$golA=mysql_fetch_assoc(mysql_query("select count(gol_darah) as A from htransaksi where  CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='A' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' "));
$golB=mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='B' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$golAB=mysql_fetch_assoc(mysql_query("select count(gol_darah) as AB from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='AB' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$golO=mysql_fetch_assoc(mysql_query("select count(gol_darah) as O from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='O'  and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$golx=mysql_fetch_assoc(mysql_query("select count(gol_darah) as x from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='X' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$jkP=mysql_fetch_assoc(mysql_query("select count(jk) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and jk='0' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$jkW=mysql_fetch_assoc(mysql_query("select count(jk) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and jk='1' and Pengambilan='0' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));


//baru
$rhposA=mysql_fetch_assoc(mysql_query("select count(gol_darah) as A from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='A' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$rhposB=mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='B' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$rhposAB=mysql_fetch_assoc(mysql_query("select count(gol_darah) as AB from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='AB' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$rhposO=mysql_fetch_assoc(mysql_query("select count(gol_darah) as O from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='O' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));
$rhposx=mysql_fetch_assoc(mysql_query("select count(gol_darah) as x from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='x' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%'"));


$rhnegA=mysql_fetch_assoc(mysql_query("select count(gol_darah) as A from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='A' and rhesus='-' and NoTrans like 'DG%'"));
$rhnegB=mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='B' and rhesus='-' "));
$rhnegAB=mysql_fetch_assoc(mysql_query("select count(gol_darah) as AB from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='AB' and rhesus='-' "));
$rhnegO=mysql_fetch_assoc(mysql_query("select count(gol_darah) as O from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='O' and rhesus='-' "));
$rhnegx=mysql_fetch_assoc(mysql_query("select count(gol_darah) as x from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='x' and rhesus='-' "));

$rhposP=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jk='0' and rhesus='+'"));
$rhposW=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jk='1' and rhesus='+'"));
$rhnegP=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jk='0' and rhesus='-'"));
$rhnegW=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jk='1' and rhesus='-'"));

$rhpos=mysql_fetch_assoc(mysql_query("select count(rhesus) as pos from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and rhesus='+' "));
$rhneg=mysql_fetch_assoc(mysql_query("select count(rhesus) as neg from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and rhesus='-' "));


//CARA PENGAMBILAN
$biasa=mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='0'"));
$tromboferesis=mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='1' "));
$leukoferesis=mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='2' "));
$plasmaferesis=mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='3' "));
$eritoferesis=mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='4' "));

/*
$dspos=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan='0' and JenisDonor='0' and rhesus='+' and NoTrans like 'DG%'"));
$dsneg=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan='0' and JenisDonor='0' and rhesus='-' and NoTrans like 'DG%'"));
$dppos=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan='0' and JenisDonor='1' and rhesus='+' and NoTrans like 'DG%'"));
$dpneg=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan='0' and JenisDonor='1' and rhesus='-' and NoTrans like 'DG%'"));

*/
//Jenis Kantong DG

$single=mysql_fetch_assoc(mysql_query("select count(kodependonor) as S from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='1' "));
$double=mysql_fetch_assoc(mysql_query("select count(kodependonor) as D from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='2' "));
$triple=mysql_fetch_assoc(mysql_query("select count(kodependonor) as T from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='3' "));
$quadruple=mysql_fetch_assoc(mysql_query("select count(kodependonor) as Q from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='4' "));
$pediatrik=mysql_fetch_assoc(mysql_query("select count(kodependonor) as P from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='6' "));

/*
$single_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as S from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='1' and Pengambilan like '%$src_status%' "));
$double_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as D from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='2' and Pengambilan like '%$src_status%' "));
$triple_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as T from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='3' and Pengambilan like '%$src_status%' "));
$quadruple_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as Q from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='4' and Pengambilan like '%$src_status%' "));
$pediatrik_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as P from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='6' and Pengambilan like '%$src_status%' "));*/

//BARU dan ULANG MU
$db=mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%'  and donorbaru='0'"));
$du=mysql_fetch_assoc(mysql_query("select count(gol_darah) as U from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%'  and donorbaru='1'"));

$db_g=mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%'  and donorbaru='0'"));
$du_g=mysql_fetch_assoc(mysql_query("select count(gol_darah) as U from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and donorbaru='1'"));

$db_b=mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and donorbaru='0'"));
$du_b=mysql_fetch_assoc(mysql_query("select count(gol_darah) as U from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and donorbaru='1'"));

//DS dan DP
$ds=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='0'"));
$dsb=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='0'"));
$dsg=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='0' "));

$dp=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='1' "));
$dpb=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='1' "));
$dpg=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='1' "));

//DG dan MU
//DS dan DP
$dg=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and notrans like 'DG%' "));

$mu=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and notrans like 'M%' "));

$mu_m=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and notrans like 'M%' and kendaraan='0' "));

$mu_b=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and notrans like 'M%' and kendaraan='1' "));

?>
<br>
<table><tr>

<td>
<table class=form border=1 cellpadding=0 cellspacing=0>
<th colspan=3><b>Rekap AFTAP </b></th>
<tr class="field">
<td><b>Gol Darah </b></td>
<td><b>JML </b></td>
<td><b>Rhesus </b></td>
</tr>
<tr><td><b> A </td>
<td class=input><?=$golA[A]?></td><td>Pos: <?=$rhposA[A]?>   Neg: <?=$rhnegA[A]?></td></tr>
<tr><td><b> B </td>
<td class=input><?=$golB[B]?></td><td>Pos: <?=$rhposB[B]?>   Neg: <?=$rhnegB[B]?></td></tr>
<tr><td><b> AB </td>
<td class=input><?=$golAB[AB]?></td><td>Pos: <?=$rhposAB[AB]?>  Neg: <?=$rhnegAB[AB]?></td></tr>
<tr><td><b> O </td>
<td class=input><?=$golO[O]?></td><td>Pos: <?=$rhposO[O]?>   Neg: <?=$rhnegO[O]?></td></tr>
<tr><td><b> X </td>
<td class=input><?=$golx[x]?></td><td>Pos: <?=$rhposx[x]?>   Neg: <?=$rhnegx[x]?></tr>
<tr><td>Laki-Laki</td>
<td class=input><?=$jkP[P]?></td><td>Pos: <?=$rhposP[P]?>   Neg: <?=$rhnegP[P]?></td></tr>
<tr><td>Perempuan</td>
<td class=input><?=$jkW[W]?></td><td>Pos: <?=$rhposW[W]?>   Neg: <?=$rhnegW[W]?></td></tr>
<tr><td><b>JML TTL</b></td>
<td><b><?=$jum[kod]?></td><td><b>Pos: <?=$rhpos[pos]?>  Neg: <?=$rhneg[neg]?></td></tr>
</table>
</td>

<td>
<table class=form border=1 cellpadding=0 cellspacing=0>
<th colspan=2><b>Rekap AFTAP Dalam Gedung CARA AMBIL</b></th>
<tr class="field">
<td><b>Cara Ambil</b></td>
<td><b>Jumlah </b></td>
</tr>
<tr><td> Biasa </td>
<td class=input><?=$biasa[kod]?></td></tr>
<th colspan='2' >AFERESIS</th>
<tr><td> Tromboferesis </td>
<td class=input><?=$tromboferesis[kod]?></td></tr>
<tr><td> Leukaferesis </td>
<td class=input><?=$leukoferesis[kod]?></td></tr>
<tr><td> Plasmaferesis </td>
<td class=input><?=$plasmaferesis[kod]?></td></tr>
<tr><td>Eritoferesis</td>
<td class=input><?=$eritoferesis[kod]?></td></tr>
<tr><td>JML TTL</td>
<td class=input><?=$eritoferesis[kod]+$plasmaferesis[kod]+$leukoferesis[kod]+$tromboferesis[kod]+$biasa[kod]?></td></tr>
</table>
</td>


<td>
<table class=form border=1 cellpadding=0 cellspacing=0>
<th colspan=2><b>Rekap Jenis Kantong</b></th>
<tr class="field">
<td><b>Jenis Kantong</b></td>
<td><b>Jumlah </b></td>
</tr>

<!--th><b>Berhasil</b></th><th><b>Gagal Aftap</b></th>

<tr><td>Single</td><td class=input><?=$single[S]?></td></tr>
<tr><td>Double</td><td class=input><?=$double[D]?></td></tr>
<tr><td>Triple</td><td class=input><?=$triple[T]?></td></tr>
<tr><td>Quadruple</td><td class=input><?=$quadruple[Q]?></td></tr>
<tr><td>Pediatrik</td><td class=input><?=$pediatrik[P]?></td></tr>
<tr><td>JML TTL</td><td class=input><?=$single[S]+$double[D]+$triple[T]+$quadruple[Q]+$pediatrik[P]?></td></tr>
</table>
</td>

<td>
<table class=form border=1 cellpadding=0 cellspacing=0>
<th colspan=2><b>Rekap Jenis Pendonor</b></th>
<tr class="field">
<td><b>Jenis Pendonor</b></td>
<td><b>Jumlah </b></td>
</tr>

<tr><td>Baru</td>
<td class=input><?=$db[B]?></td></tr>
<tr><td>Ulang</td>
<td class=input><?=$du[U]?></td></tr>
<tr><td>DS</td>
<td class=input><?=$ds[P]?></td></tr>
<tr><td>DP</td>
<td class=input><?=$dp[W]?></td>
<tr><td>JML TTL</td><td class=input><?=$db[B]+$du[U]?></td></tr>
</table>
</td>

<td>
<table class=form border=1 cellpadding=0 cellspacing=0>
<th colspan=4><b>Rekap TEMPAT AFTAP</b></th>
<tr class="field">
<td rowspan='2'><b>Tempat Donor</b></td>
<th class ="field" colspan='3'><b>Jumlah Aftap</b></th>
</tr>
<th><b>Jumlah</b></th><th><b>Mobil</b></th><th><b>Bus Donor</b></th>

<tr><td>DG</td>
<td class=input align='center'><?=$dg[P]?></td><td class=input><?='-'?></td><td class=input><?='-'?></td></tr>
<tr><td>MU</td>
<td class=input><?=$mu[P]?></td><td class=input><?=$mu_m[P]?></td><td class=input><?=$mu_b[P]?></td></tr>
<!--tr><td>Mobil Donor</td>
<td class=input><?=$ds[P]?></td><td class=input><?=$dsg[P]?></td><td class=input><?=$dsb[P]?></td></tr>
<tr><td>BUS Donor</td>
<td class=input><?=$dp[W]?><td class=input><?=$dpg[W]?></td><td class=input><?=$dpb[W]?></td>
<tr><td>JML TTL</td><td class=input><?=$dg[P]+$mu[P]?></td><td class=input><?=$mu_m[P]?></td><td class=input><?=$mu_b[P]?></td></tr>
</table>
</td>


</tr>
</table>
-->
<table>

<?
$udd=mysql_fetch_assoc(mysql_query("select nama from utd where down='1' and aktif='1'"));
?>
<tr><td></td><td colspan="4" align="center"><?=$udd[nama]?>, <?=$today?></td></tr>
<tr><td></td><td align="center">Yang Menerima</td><td></td><td></td><td align="center">Yang Menyerahkan</td></tr>
<tr><td></td><td></td><td></td></tr>
<tr><td></td><td></td><td></td></tr>
<tr><td></td><td></td><td></td></tr>
<tr><td></td><td align="center">(.......................)</td><td></td><td></td><td align="center">(<? echo $namauser;?>)</td></tr>
</table>

<?
mysql_close();
?>
