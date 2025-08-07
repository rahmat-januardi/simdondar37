<?php
require_once('clogin.php');
require_once('config/db_connect.php');
session_start();
$namauser	= $_SESSION[namauser];

$notrans	= $_GET[notrans];
$script		= "DELETE FROM lis_pmi.eflexys WHERE  notrans='$notrans'";

echo $script;

$delete	= mysql_query("DELETE FROM lis_pmi.eflexys WHERE  notrans WHERE  notrans='$notrans'");
echo "<meta http-equiv='refresh' content='5;url=pmikonfirmasi.php?module=listkonfirm'>";

?>