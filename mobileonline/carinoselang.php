<?php

/**
 * Created by PhpStorm.
 * Date: 4/14/14
 * Time: 10:28 AM
 */

include 'adm/config.php';

if (isset($_POST['ktg'])) {
    $no_selang  = mysqli_real_escape_string($con, $_POST['ktg']);

    $test = "SELECT noSelang FROM stokkantong WHERE noKantong='$no_selang'";
    $check_selang = mysqli_query($con, "SELECT noSelang FROM stokkantong WHERE noKantong='$no_selang'");


    if (mysqli_num_rows($check_selang)) {
        $noselang = mysqli_fetch_assoc($check_selang);
        echo $noselang['noSelang'];
    } else {
        echo $_POST['ktg'];
    }
}
//echo $test;
