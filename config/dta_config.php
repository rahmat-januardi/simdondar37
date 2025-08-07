<?php
$_h = "localhost";
$_u = "root";
$_p = "F201603907";
$_dkondolidasi = "pmi";
$dbi = new mysqli($_h, $_u, $_p, $_dkondolidasi);
if ($dbi->connect_errno) {
    die("ERROR : -> " . $dbi->connect_error);
}
try {
  $dbi_pdo = new PDO("mysql:host=$_h;dbname=$_dkondolidasi", $_u, $_p);
  $dbi_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage()."\n";
  }
?>
