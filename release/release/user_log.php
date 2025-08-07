<?php
session_start();
logq=mysql_query("INSERT INTO `user_log`(`time_aksi`,`komputer`, `user`, `modul`, `aksi_user`, `keterangan`, `tempat`) VALUES
    ('$time_aksi','$_SESSION[client_ip]','$_SESSION[namauser]','$log_mdl','$log_aksi','','$tempat')");
?>
