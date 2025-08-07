<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
   echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
   echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";

}
if (($_SESSION['leveluser']) == 'qa') {
   ?>

   <head>
      <title>SIMDONDAR</title>
      <script language=javascript src="idcard.js" type="text/javascript"> </script>
      <script language=javascript src="util.js" type="text/javascript"> </script>
      <link href="css/style.css" rel="stylesheet" type="text/css" />
   </head>
   <?php

   $act = isset($_GET['act']) ? $_GET['act'] : '';
   $rstock = isset($_GET['rstock']) ? $_GET['rstock'] : '';
   $module = isset($_GET['module']) ? $_GET['module'] : '';
   switch ($_GET['act']) {

      default:
         if ($rstock == '1')
            include "modul/stock.php";
         if ($rstock == '4')
            include "release/qa_check_kantong.php";
         include "config/koneksi.php";
         include "config/fungsi_combobox.php";
         include "config/library.php";

         if ($module == 'home') {
            echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
         } elseif ($module == 'input_qa') {
            include "qa_release.php";
         } elseif ($module == 'stock') {
            include "modul/stock.php";
         } elseif ($module == 'qa_komponen') {
            include "qa_komponen.php";
         } elseif ($module == 'qa_permintaan') {
            include "komponen_permintaan.php";
         } elseif ($module == 'ganti_menu') {
            include "ganti_menu.php";
         } elseif ($module == 'form_minta') {
            include "modul/form_minta.php";
         } elseif ($module == 'ganti_passwd') {
            include "modul/ganti_passwd.php";
         } elseif ($module == 'rincian_minta_barang') {
            include "logistik/rincian_transaksi_minta_barang.php";
         }

         //=========SERAH TERIMA=======
         elseif ($module == 'serahterima') {
            include "komponen_serahterima.php";
         } elseif ($module == 'sr_aftap') {
            include "serahterima/sr_aftap_komponen.php";
         } elseif ($module == 'delrow') {
            include "serahterima/sr_proces.php";
         } elseif ($module == 'batal') {
            include "serahterima/sr_proces.php";
         } elseif ($module == 'sr_aftap_list') {
            include "serahterima/sr_aftap_komponen_list.php";
         } elseif ($module == 'sr_rpt_ktg') {
            include "serahterima/sr_aftap_komponen_print_ktg.php";
         } elseif ($module == 'sr_rpt_imltd') {
            include "serahterima/sr_aftap_komponen_print_imltd.php";
         } elseif ($module == 'sr_rpt_kgd') {
            include "serahterima/sr_aftap_komponen_print_kgd.php";
         } elseif ($module == 'sr_rpt_nat') {
            include "serahterima/sr_aftap_komponen_print_nat.php";
         } elseif ($module == 'sr_rpt_rilis') {
            include "serahterima/sr_aftap_komponen_print_rilis.php";
         } elseif ($module == 'sr_rpt_view') {
            include "serahterima/sr_aftap_komponen_view.php";
         }

         //DISTRIBUSI ========
         elseif ($module == 'qa_distribusi') {
            include "qa_distribusi.php";
         } elseif ($module == 'form_bdrs') {
            include "modul/form_bdrs.php";
         } elseif ($module == 'form_bdrsxls') {
            include "modul/form_bdrsxls.php";
         } elseif ($module == 'update_dari_bdrs') {
            //include  "modul/update_dari_bdrs.php";
            include "modul/decode.php";
         } elseif ($module == 'dari_bdrs') {
            include "modul/terima_bdrs.php";
         } elseif ($module == 'rekap_darah_keluar_bdrs') {
            include "modul/rekap_darah_keluar_bdrs_new.php";
         } elseif ($module == 'rekap_darah_keluar_udd') {
            include "modul/rekap_darah_keluar_udd_new.php";
         } elseif ($module == 'tambah_bdrs') {
            include "modul/tambah_bdrs_kasir.php";
         } elseif ($module == 'terima_dari_utd_lain') {
            require_once('color.inc');
            include "modul/terima_dari_utd_lain.php";
         } elseif ($module == 'rekap_darah_keluar') {
            include "modul/rekap_darah_keluar.php";
         } elseif ($module == 'rekap_darah_keluar_lama') {
            include "modul/rekap_darah_keluar_lama.php";
         } elseif ($module == 'komponen_musnah') {
            include "laborat_musnah.php";
         } elseif ($module == 'musnah') {
            include "musnah/musnah24.php";
         } elseif ($module == 'musnahdelrow') {
            include "musnah/musnah_proses.php";
         } elseif ($module == 'musnahbatal') {
            include "musnah/musnah_proses.php";
         } elseif ($module == 'musnahlist') {
            include "musnah/musnah_list.php";
         } elseif ($module == 'musnah_rpt_view') {
            include "musnah/musnah_view.php";
         } elseif ($module == 'musnah_cetakberita') {
            include "musnah/musnah_cetakberita.php";
         } elseif ($module == 'musnah_serahterima') {
            include "musnah/musnah_serah.php";
         } elseif ($module == 'rincian_darah_buang') {
            include "musnah/musnah_rincian.php";
         } elseif ($module == 'keluar') {
            include "modul/keluar.php";
         } elseif ($module == 'form_udd') {
            include "modul/form_udd.php";
         } elseif ($module == 'tambah_bdrs') {
            include "modul/tambah_bdrs_kasir.php";
         }

         //NEW MODUL RELEASE BUILD 28-03-2018============
         elseif ($module == 'release_proses') {
            include "release/qa_release.php";
         } elseif ($module == 'release') {
            include "release/qa_release_inputkantong.php";
         } elseif ($module == 'kantong_kosong') {
            include "release/qa_list_berat_kantong_kosong.php";
         } elseif ($module == 'timbang') {
            include "release/qa_list_timbang_darah.php";
         } elseif ($module == 'density') {
            include "release/qa_beratjenis.php";
         } elseif ($module == 'kantong') {
            include "release/qa_add_kantong.php";
         } elseif ($module == 'edit_beratkantong') {
            include "release/qa_edit_kantong.php";
         } elseif ($module == 'rekap_release') {
            include "release/qa_list_release.php";
         } elseif ($module == 'cetak_rekap') {
            include "release/qa_list_release_xls.php";
         } elseif ($module == 'cetak_release') {
/*
            require_once('color.inc');
            include "release/qa_label.php";
*/
//2025-04-12
	 include "release/qa_release_label_2025.php";
         } elseif ($module == 'releaseload') {
            include "release/qa_release_inputkantongload.php";
         }
      //==============================================
   }

   ?>

   <?php
}
?>
