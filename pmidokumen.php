<?php
session_start();
if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser])){
  echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";

  }
if (($_SESSION[leveluser])=='dokumen'){
?>
<head>
<title>SIMDONDAR</title>
<script language=javascript src="idcard.js" type="text/javascript"> </script>
<script language=javascript src="util.js" type="text/javascript"> </script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<?php


switch($_GET[act]){

default:
if ($_GET['rstock']=='1') include "modul/stock.php";
if ($_GET['rstock']=='4') include "modul/stock1.php";
include "config/koneksi.php";
include "config/fungsi_combobox.php";
include "config/library.php";

if ($_GET[module]=='home'){
          echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
}
elseif ($_GET[module]=='history_dokumen'){
                 include "modul/history_dokumen.php";
	}
elseif ($_GET[module]=='rekap_perubahan'){
                 include "modul/rekap_perubahan.php";
	}
elseif ($_GET[module]=='kebijakan'){
                 include "dokumen/kebijakan.php";
}
elseif ($_GET[module]=='dokumen_baru'){
                 include "modul/dokumen_baru.php";
}
elseif ($_GET[module]=='edit_dokumen'){
                 include "modul/edit_dokumen.php";
}
elseif ($_GET[module]=='stock'){
   include  "modul/stock.php";
}
elseif ($_GET[module]=='ganti_menu'){
        include  "ganti_menu.php";
}
elseif ($_GET[module]=='ganti_passwd'){
   include  "modul/ganti_passwd.php";
}




//wa support
elseif ($_GET[module]=='wa_support'){
    	include  "maintenance/wa_support.php";
   }
}

?>

<?php
}
?>
