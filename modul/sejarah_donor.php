<?
if (isset($_GET[q])) {
include ('../config/db_connect.php');

$today=date('Y-m-d H:i:s');
$q=$_GET['q'];
$query = mysql_query("SELECT * FROM pendonor where Kode='$q' ");
$query1 = mysql_query("SELECT * FROM htransaksi where KodePendonor='$q' order by Tgl ASC");	

//$dtrans = mysql_fetch_array($trans);
$cekal1=mysql_fetch_assoc(mysql_query("select cekal,tglkembali,jumDonor,Rhesus,GolDarah,Nama from pendonor where Kode='$q' "));
$query2=mysql_fetch_assoc($quey);

$instansi=mysql_fetch_assoc(mysql_query("select * from detailinstansi where aktif='1'"));
$kendaraan=mysql_fetch_assoc(mysql_query("select k.kendaraan, d.KodeDetail,d.aktif from kegiatan as k, detailinstansi as d where k.kodeinstansi=d.KodeDetail and d.aktif='1'"));






//transaksidonor
//------------------------ set id transaksi ------------------------->
$udd1   = mysql_query("select id from utd where aktif='1'");
$udd    = mysql_fetch_assoc($udd1);
$idp	= mysql_query("select * from tempat_donor where active='1'");
$idp1	= mysql_fetch_assoc($idp);
$th		= substr(date("Y"),2,2);
$bl		= date("m");
$tgl	= date("d");
$kdtp	= substr($idp1[id1],0,2).$tgl.$bl.$th."-".$udd[id]."-";
$idp	= mysql_query("select NoTrans from htransaksi where NoTrans like '$kdtp%' order by NoTrans DESC");
$idp1	= mysql_fetch_assoc($idp);
$idp2	= substr($idp1[NoTrans],14,4);
if ($idp2<1) {$idp2="0000";}
$idp3	= (int)$idp2+1;
$id31	= strlen($idp2)-strlen($idp3);
$idp4	= "";
for ($i=0; $i<$id31; $i++){
	$idp4 .="0";
}
$id_transaksi_baru=$kdtp.$idp4.$idp3;
//------------------------ END set id transaksi ------------------------->






?>
<table bgcolor="#000012" cellspacing="1" cellpadding="3">	
	<tr bgcolor="#FF1000">
		<th colspan=4><b>INFO DATA PENDONOR</b></th></tr>
</table>
<table bgcolor="#000000" cellspacing="1" cellpadding="3">	
	<tr bgcolor="#FAEBD7">
		<th rowspan='2'>Kode Pendonor</th> 
		<th rowspan='2'>Nama Pendonor</th> 
		<th rowspan='2'>Alamat</th>
		<th colspan='2' align="center">No. Telp</th>  
		<th rowspan='2'>Gol Drh</th>
		<th rowspan='2'>Jml Donor</th>
		<th rowspan='2'>Tgl Donor Kembali</th>
		<th rowspan='2'>Status</th>
	</tr>
	<tr bgcolor="#FAEBD7">
		<th>Handphone</th> 
		<th>Rumah/Ktr</th> 

		<? while($row = mysql_fetch_object($query)): ?>
<?
$cekal='OK';
if ($cekal1[cekal]=='1') $cekal='Confirm';
?>
	<tr bgcolor="#FFFFFF">
		<td><?=$row->Kode?></td>
		<td><?=$row->Nama?></td>
		<td><?=$row->Alamat?></td>
		<td><?=$row->telp2?></td>
		<td><?=$row->telp?></td>
		<td><?=$row->GolDarah?>(<?=$row->Rhesus?>)</td>
		<td align="center"><?=$row->jumDonor?> Kali</td>
		<td align="center"><?=$row->tglkembali?></td>
		<td align="center"><?=$cekal?></td>
	<? endwhile; ?>
</table>
<table>
<? if ($cekal1[cekal] =="0" and $cekal1[tglkembali] <= $today) { ?>
<tr>
<td>
<form name="kirim" method="post" action="formulir_donor_PDF2.php?kp=<?=$q?>" target="_blank">
<input name="nokan" type="hidden" value="<?=$q?>">
<input name="notrans" type="hidden" value="<?=$id_transaksi_baru?>">
<input name="instansi" type="hidden" value="<?=$instansi[nama]?>">
<input name="kendaraan" type="hidden" value="<?=$kendaraan[kendaraan]?>">
<input name="submit" type="submit" value="Cetak Form Donor">
</form></td>
</tr>
<?}?>	
</table>

<br></br>
<table bgcolor="#000012" cellspacing="1" cellpadding="3">	
	<tr bgcolor="#FF1000">
		<th colspan=4 color="#DDDDDD"><b>INFO HISTORY DONOR</b></th>
<th><form name=xls method=post action=modul/rekap_sejarah_donor_xls.php>
<input type=hidden name=q1 value='<?=$q?>'>
<input type=hidden name=queryx value='<?=$query?>'>
<input type=hidden name=queryx2 value='<?=$query1?>'>
<input type=hidden name=cekalx value='<?=$cekal1?>'>
<input type=hidden name=instansix value='<?=$instansi?>'>
<input type=hidden name=kendaraanx value='<?=$kendaraan?>'>
<input type=submit name=submit2 value='Print History Donor (.XLS)'>
</form></th></tr>
</table>

<table bgcolor="#000000" cellspacing="1" cellpadding="3">	
	<tr bgcolor="#FAEBD7">
		<td rowspan='2'>No.</td>
		<td rowspan='2'>Tanggal</td>
		<td rowspan='2'>Donor Ke</td>
		<td rowspan='2'>BB</td>
		<td rowspan='2'>Tensi</td>
		<td rowspan='2'>Jenis</td>
<td rowspan='2'>Tempat</td>
<td rowspan='2'>Instansi</td>
<td rowspan='2'>Nokantong</td>
<td rowspan='2'>Status<br>Aftap</td>
<td colspan='7' align="center">petugas</td>
	</tr>
<tr bgcolor="#FAEBD7">
<td>Input</td>
<td>HB</td>
<td>Tensi</td>
<td>Aftap</td>
<td align="center">Transaksi</td>
<td align="center">Informed<br>Consent</td>
<td align="center">Aksi</td>
</tr>
<?
$no=1;
//$trans=mysql_query("select * from htransaksi where KodePendonor='$q' order by Tgl ASC");
/*SELECT fname, lname, addr FROM prospect
-> UNION
-> SELECT first_name, last_name, address FROM customer
-> UNION
-> SELECT company, '', street FROM vendor;
*/
$trans=mysql_query(" SELECT Kodependonor,Tgl,NoTrans,Pengambilan,beratBadan,tensi,JenisDonor,tempat,Instansi,NoKantong,petugasHB,petugasTensi,user,petugas,donorke FROM htransaksi where KodePendonor='$q'
		UNION
			SELECT Kodependonor,Tgl,id,Pengambilan,beratBadan,tensi,JenisDonor,tempat,Instansi,NoKantong,petugasHB,petugasTensi,user,petugas,'' FROM htransaksilama where KodePendonor='$q' order by Tgl ASC ");

/*$trans=mysql_query(" SELECT Kodependonor,NoTrans,Tgl,Pengambilan,beratBadan,tensi,JenisDonor,tempat,Instansi,NoKantong,petugasHB,petugasTensi,user,petugas,donorke FROM htransaksi where KodePendonor='$q' order by Tgl DESC ");*/
	 while ($dtrans = mysql_fetch_assoc($trans)):
$notr=$dtrans[NoTrans];
$jenis='DS';
	if ($dtrans[JenisDonor]=='1') $jenis='DP';
$tempat='DG';
	if ($dtrans[tempat]=='M') $tempat='MU';

	  
?>

	<tr bgcolor="#FFFFFF">
		<td><?=$no++?></td>
		<td><?=$dtrans[Tgl]?></td>
		<td><?=$dtrans[donorke]?> Kali</td>
		<td><?=$dtrans[beratBadan]?> kg</td>
		<td align="center"><?=$dtrans[tensi]?></td>
		<td align="center"><?=$jenis?></td>
		<td align="center"><?=$tempat?></td>
		<td align="center"><?=$dtrans[Instansi]?></td>
		<td align="center"><?=$dtrans[NoKantong]?></td>
<?
//$pengambilan=mysql_fetch_assoc(mysql_query("select Pengambilan from htransaksi where KodePendonor='$q'"));
	if ($dtrans[Pengambilan]=='0') $pengambilan1="Berhasil";
	if ($dtrans[Pengambilan]=='2') $pengambilan1="Gagal Aftap";
	if ($dtrans[Pengambilan]=='1') $pengambilan1="Batal";
?>
		<td align="center"><?=$pengambilan1?></td>
		<td align="center"><?=$dtrans[user]?></td>
		<td align="center"><?=$dtrans[petugasHB]?></td>
		<td align="center"><?=$dtrans[petugasTensi]?></td>
		<td align="center"><?=$dtrans[petugas]?></td>
		<td align="center"><?=$dtrans[NoTrans]?></td>
		<td align="center"><form name="kirim" method="post" action="formulir_donor_PDF3.php?kp=<? echo $dtrans['Kodependonor']?>&trans=<? echo $dtrans['NoTrans']?>" target="_blank">
<input name="submit" type="submit" value="Lihat Form Donor"></form>
</td>
		<? switch ($_SESSION[leveluser]){
	case "p2d2s":
            ?><td><a href=pmip2d2s.php?module=delhistory&NoTrans=<? echo $dtrans['NoTrans'] ?>&kode=<? echo $dtrans['Kodependonor'] ?>&tgl=<? echo $dtrans['tgl'] ?>><input type=submit name=submit4 value='Hapus'></a></td><?
            break;
        case "aftap":
            ?><td><a href=pmiaftap.php?module=delhistory&NoTrans=<? echo $dtrans['NoTrans'] ?>&kode=<? echo $dtrans['Kodependonor'] ?>&tgl=<? echo $dtrans['tgl'] ?>><input type=submit name=submit4 value='Hapus'></a></td><?
            break;
        case "admin":
            ?><td><a href=pmiadmin.php?module=delhistory&NoTrans=<? echo $dtrans['NoTrans'] ?>&kode=<? echo $dtrans['Kodependonor'] ?>&tgl=<? echo $dtrans['tgl'] ?>><input type=submit name=submit4 value='Hapus'></a></td><?
            break;
	case "kasir":
            ?><td><a href=pmikasir.php?module=delhistory&NoTrans=<? echo $dtrans['NoTrans'] ?>&kode=<? echo $dtrans['Kodependonor'] ?>&tgl=<? echo $dtrans['tgl'] ?>><input type=submit name=submit4 value='Hapus'></a></td><?
	    //echo "<a href=pmikasir.php?module=ic&NoTrans=".$data['NoTrans'].">".$hasil1['Nama']."</a>";
            break;
        default:
            echo "Anda tidak memiliki hak akses";
    }?></td>
		
	</tr>

	<? endwhile; ?>
</table>
	

<? 
}?>

