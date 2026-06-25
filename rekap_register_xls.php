<?
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_penerimaan_sampel_QC.xls");
header("Pragma: no-cache");
header("Expires: 0");

include('../config/db_connect.php');


$today      =$_POST[today];
$today1     =$_POST[today1];
$src_bdrs 	=$_POST[bdrs];
$src_produk    	=$_POST[produk];
$src_status  	=$_POST[status];
$src_golongan  	=$_POST[golongan];
$src_rhesus  	=$_POST[rhesus];
$src_utd 	=$_POST[utd];

?>
<h3>RINCIAN PENERIMAAN SAMPLE QC PRODUK KOMPONEN DARAH</h3>
<h3>Dari Tanggal : <?=$today?>  s/d <?=$today1?></h1>
<?
$transaksipermintaan=mysql_query("select k.nokantong,k.goldarah,k.rhesus,k.produk,k.tglaftap,k.kadaluwarsa,k.tgl,k.petugas_terima,k.petugas_serah,k.asal_utd from stokkantong as s, registrasi_qc as k where CAST(k.tgl as date) >='$today' and CAST(k.tgl as date) <='$today1' and s.noKantong=k.nokantong and s.produk like '%$src_produk%' and s.gol_darah like '$src_golongan%' and s.RhesusDrh like '%$src_rhesus%' and k.asal_utd like '%$src_utd%' order by k.tgl ASC  ");
?>

<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="100%">
<tr style="background-color:white; font-size:11px; color:black; font-family:Verdana;" 
  onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">          
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">No</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">No. Kantong</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Gol Darah</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Rhesus</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Produk</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Tgl Aftap</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Tgl Kadaluwarsa</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Tgl Penerimaan Sampel</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Petugas Yg Menyerahkan</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Petugas Yg Menerima</td>
	    <td height="30" style="vertical-align:middle; text-align:center; font-weight:bold;">Asal Sampel</td>
        </tr>


<?
$no=1;
while ($datatransaksipermintaan=mysql_fetch_array($transaksipermintaan)){
?>
<tr style="background-color:white; font-size:11px; color:black; font-family:Verdana;" 
  onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
	    <td align="center"><?=$no?></td>
	    <td align="center"><?=$datatransaksipermintaan['nokantong']?></td>
	    <td align="center"><?=$datatransaksipermintaan['goldarah']?></td>
	    <td align="center"><?=$datatransaksipermintaan['rhesus']?></td>
	    <td align="center"><?=$datatransaksipermintaan['produk']?></td>
	    <td align="center"><?=$datatransaksipermintaan['tglaftap']?></td>
	    <td align="center"><?=$datatransaksipermintaan['kadaluwarsa']?></td>
	    <td align="center"><?=$datatransaksipermintaan['tgl']?></td>
	    <td align="center"><?=$datatransaksipermintaan['petugas_terima']?></td>
	    <td align="center"><?=$datatransaksipermintaan['petugas_serah']?></td>
	    <td align="center"><?=$datatransaksipermintaan['asal_utd']?></td>
</tr>
<? $no++;} ?>
</table>

<?
mysql_close();
?>
