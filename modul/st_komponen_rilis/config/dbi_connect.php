<?php
include_once "db_config.php";
$dbi=mysqli_connect($host,$usr,$pwd,$db_sim);
if (mysqli_connect_errno()) {
        echo "Connect failed: %s\n", mysqli_connect_error().'<br>';
}
