<?
if (isset($_GET[q])) {
include ('../config/db_connect.php');
$q=$_GET["q"];
$query = mysql_query("SELECT * FROM pasien_bdrs where nama like '%$q%' ");	
?>
<table bgcolor="#000000" cellspacing="1" cellpadding="3">	
	<tr bgcolor="#DDDDDD">
		<th>ID Pasien</th>
		<th>Nama Pasien</th>
		<th>Tgl Lahir</th>
		
	</tr>
	<? while($row = mysql_fetch_object($query)): ?>
	<tr bgcolor="#FFFFFF">
		<td align="center">tester</td>
		
		
	</tr>
	<? endwhile; ?>
</table>
<? }

