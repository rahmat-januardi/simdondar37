<?php
$idp = mysql_query("SELECT id, id1 FROM tempat_donor WHERE active='1'");
$idp1 = mysql_fetch_assoc($idp);
$tempat = isset($idp1['id1']) ? $idp1['id1'] : '';

$log_mdl = strtoupper($log_mdl);
if (empty($time_aksi)) {
    $time_aksi = date("Y-m-d H:i:s");
}

$client_ip = isset($_SESSION['client_ip']) ? $_SESSION['client_ip'] : '';
$namauser   = isset($_SESSION['namauser']) ? $_SESSION['namauser'] : '';

mysql_query("
    INSERT INTO `user_log`
    (`time_aksi`,`komputer`,`user`,`modul`,`aksi_user`,`keterangan`,`tempat`)
    VALUES
    ('$time_aksi','$client_ip','$namauser','$log_mdl','$log_aksi','','{$tempat}')
");