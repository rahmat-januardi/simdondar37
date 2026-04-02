<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/db_connect.php');

$jenis = isset($_GET['jenis']) ? trim($_GET['jenis']) : '';

$map = array(
    '0' => 'SUP',
    '1' => 'CUS',
    '2' => 'UDD',
    '3' => 'OTH',
    '4' => 'LIM'
);

if (!isset($map[$jenis])) {
    exit('Jenis tidak valid');
}

$prefix = $map[$jenis];

$sql = "
SELECT MAX(SUBSTR(Kode,4,3)) AS max_angka
FROM supplier
WHERE TRIM(Kode) LIKE '$prefix%'
AND jenis = '$jenis'
";

$q = mysql_query($sql) or die("Query error: ".mysql_error());
$r = mysql_fetch_assoc($q);

$max = isset($r['max_angka']) ? (int)$r['max_angka'] : 0;

$next = $max > 0 ? $max + 1 : 1;

$kode_baru = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);

echo $kode_baru;