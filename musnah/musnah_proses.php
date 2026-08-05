<?php
//require_once('clogin.php');
include('config/dbi_connect.php');
//session_start();
$namauser = $_SESSION['namauser'];
$op =   $_GET['op'];

if ($op == 'del') {
    $kantong    = $_GET['ktg'];
    $user        = $_GET['usr'];
    $level        = $_GET['bagian'];

    $sq_del       = "DELETE FROM `ar_stokkantongtemp` WHERE `noKantong`='$kantong' AND  `bagian`='$level'";
    $sql_delete  = mysqli_query($dbi, $sq_del);
    if ($sql_delete) {
        echo "<script>alert('Penghapusan data BERHASIL dilakukan.');</script>";
    } else {
        echo "<script>alert('Penghapusan data GAGAL.');</script>";
    }

    header("Location: pmi" . $level . ".php?module=musnah");

    exit;
}

if ($op == 'batal') {
    $user        = $_GET['usr'];
    $level        = $_GET['bagian'];
    $sq_del       = "DELETE FROM `ar_stokkantongtemp` WHERE `bagian`='$level'";
    echo $sq_del;

    $sql_delete  = mysqli_query($dbi, $sq_del);


    header("Location: pmi" . $level . ".php?module=musnahlist");
}