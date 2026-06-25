<?php
session_start();
if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser])){
  echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";

  }
if (($_SESSION[leveluser])=='qa'){
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
elseif ($_GET[module]=='input_qa'){
       include "qa_release.php";
}
elseif ($_GET[module]=='stock'){
   include  "modul/stock.php";
}
elseif ($_GET[module]=='qa_komponen'){
   include  "qa_komponen.php";
}
elseif ($_GET[module]=='qa_permintaan'){
   include  "komponen_permintaan.php";
}

elseif ($_GET[module]=='ganti_menu'){
        include  "ganti_menu.php";
}
elseif ($_GET[module]=='form_minta'){
   include  "modul/form_minta.php";
}
elseif ($_GET[module]=='ganti_passwd'){
   include  "modul/ganti_passwd.php";
}
elseif ($_GET[module]=='rincian_minta_barang'){
                 include "logistik/rincian_transaksi_minta_barang.php";
}
//NEW MODUL RELEASE BUILD 10-09-2017============
elseif ($_GET[module]=='release_proses'){
   include  "release/qa_release.php";
}
elseif ($_GET[module]=='release'){
   include  "release/qa_release_inputkantong.php";
}
elseif ($_GET[module]=='kantong_kosong'){
   include  "release/qa_list_berat_kantong_kosong.php";
}
elseif ($_GET[module]=='timbang'){
   include  "release/qa_list_timbang_darah.php";
}
elseif ($_GET[module]=='density'){
   include  "release/qa_beratjenis.php";
}
elseif ($_GET[module]=='kantong'){
   include  "release/qa_add_kantong.php";
}
elseif ($_GET[module]=='edit_beratkantong'){
   include  "release/qa_edit_kantong.php";
}
elseif ($_GET[module]=='rekap_release'){
    include  "release/qa_list_release.php";
}
elseif ($_GET[module]=='cetak_release'){
    require_once('color.inc');
    include "release/qa_label.php";
}
//==============================================
}

?>

<?php
}
?>
