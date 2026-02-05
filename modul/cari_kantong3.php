<?
if (isset($_GET[q])) {
include ('../config/db_connect.php');
$q=$_GET["q"];
$query = mysql_query("SELECT * FROM stokkantong where noKantong like '%$q%'");	
?>
<table bgcolor="#000000" cellspacing="2" cellpadding="3" align="center">	
	<tr bgcolor="#DDDDDD">
		<th align="center">No. Kantong</th>
		<th align="center">Tgl. Aftap</th>
		<th align="center">Produk</th>
		<th align="center">Gol. Darah & Rhesus</th>
	</tr>
	<? while($row = mysql_fetch_object($query)): ?>
	<tr bgcolor="#FFFFFF">
		<td align="left"><a href="javascript:selectKode('<?=$row->noKantong?>')"><?=$row->noKantong?></a></td>
		<td><?=$row->tgl_Aftap?></td>
		<td><?=$row->produk?></td>
		<td align="center"><?=$row->gol_darah?> <?=$row->RhesusDrh?></td>
		
	</tr>
	<? endwhile; ?>
</table>
<? }
