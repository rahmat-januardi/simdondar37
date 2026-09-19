<?
if (isset($_GET[q])) {
    include('../config/db_connect.php');
    $q = $_GET["q"];
    $query = mysql_query("SELECT * FROM pendonor where Nama like '%$q%' ");
?>
<table bgcolor="#000000" cellspacing="2" cellpadding="3" align="center">
    <tr bgcolor="#DDDDDD">
        <th align="center">Id Pendonor</th>
        <th align="center">Nama</th>

    </tr>
    <? while ($row = mysql_fetch_object($query)): ?>
    <tr bgcolor="#FFFFFF">
        <td align="center"><?= $row->Kode ?></td>
        <td><?= $row->Nama ?></td>

    </tr>
    <? endwhile; ?>
</table>
<? }