<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
  echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";
}
if (($_SESSION['leveluser']) == 'mobile') {
  ?>
  <!doctype html>
  <html>

  <head>
    <title>SIMDONDAR</title>
    <script language=javascript src="idcard.js" type="text/javascript"> </script>
    <script language=javascript src="util.js" type="text/javascript"> </script>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
  </head>
  <?php
  //require_once('color.inc');
  switch ($_GET['act']) {
    default:
      if ($_GET['rstock'] == '1')
        include "modul/stock.php";
      if ($_GET['rstock'] == '3')
        // include "modul/stock1.php";
        include "modul/cek_kantong_new.php";
      //include "config/db_connect.php";
      include "config/koneksi.php";
      include "config/fungsi_combobox.php";
      include "config/library.php";

      if ($_GET['module'] == 'home') {
        echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
      } elseif ($_GET['module'] == 'rekap') {
        include "mobile_rekap.php";
      } elseif ($_GET['module'] == 'rekap_transaksi_harian') {
        include "modul/rekap_transaksi_donor.php";
      } elseif ($_GET['module'] == 'mobile_cetak') {
        include "mobile_cetak.php";
      } elseif ($_GET['module'] == 'cetak_id') {
        include "color.inc";
        include "modul/cetak_id.php";
      } elseif ($_GET['module'] == 'keluar') {
        include "modul/keluar.php";
      } elseif ($_GET['module'] == 'search_pendonor') {
        include "modul/search_pendonor.php";
      } elseif ($_GET['module'] == 'double_pendonor') {
        include "modul/double_pendonor.php";
      } elseif ($_GET['module'] == 'registrasi') {
        include "modul/registrasi.php";
      } elseif ($_GET['module'] == 'spendonor') {
        include "modul/search_pendonor_edit.php";

      } elseif ($_GET['module'] == 'eregistrasi') {
        include "modul/edit_registrasi.php";
      } elseif ($_GET['module'] == 'eregistrasiluar') {
        include "modul/edit_registrasiluar.php";
      } elseif ($_GET['module'] == 'transaksi_donor') {
        include "modul/transaksi_donor.php";
      } elseif ($_GET['module'] == 'spengambilan') {
        include "modul/search_transaksi.php";
      } elseif ($_GET['module'] == 'pengambilan') {
        include "modul/pengambilan_darah.php";
      } elseif ($_GET['module'] == 'pengesahan_pengambilan') {
        include "modul/pengesahan_ambil_darah.php";
      } elseif ($_GET['module'] == 'downloadtogerai') {
        include "modul/downto_gerai.php";
      } elseif ($_GET['module'] == 'download') {
        include "modul/downto_mu.php";
      } elseif ($_GET['module'] == 'kantong_mu') {
        include "modul/list_kantong_mu.php";
      } elseif ($_GET['module'] == 'uploadfromgerai') {
        include "modul/upload_from_gerai.php";
      } elseif ($_GET['module'] == 'upload') {
        include "modul/upload_mu_server.php";
      } elseif ($_GET['module'] == 'aturuser') {
        include "modul/mod_user.php";

      } elseif ($_GET['module'] == 'rtransaksi') {
        include "modul/rtransaksi.php";

      } elseif ($_GET['module'] == 'stock') {
        include "modul/stock.php";
      } elseif ($_GET['module'] == 'tambah_instansi') {
        include "modul/tambah_instansi1.php";
      } elseif ($_GET['module'] == 'data_jadwal_mobile_now') {
        include "modul/data_jadwal_mobile_now.php";
      } elseif ($_GET['module'] == 'data_jadwal_mobile') {
        include "modul/data_jadwal_mobile.php";
      } elseif ($_GET['module'] == 'rincian_jadwal_mobile') {
        include "modul/rincian_jadwal_mobile.php";
      } elseif ($_GET['module'] == 'entry_jadwal_mobile') {
        include "modul/add_load_edit.php";
      } elseif ($_GET['module'] == 'jadwal_mobile') {
        include "mobile_jadwal.php";

      } elseif ($_GET['module'] == 'mobile_transfer') {
        include "mobile_transfer.php";

      } elseif ($_GET['module'] == 'mobile_pendonor') {
        include "mobile_pendonor.php";
      } elseif ($_GET['module'] == 'mobile_transaksi') {
        include "mobile_transaksi.php";
      } elseif ($_GET['module'] == 'ganti_menu') {
        include "ganti_menu.php";
      } elseif ($_GET['module'] == 'minta_barang') {
        include "modul/form_minta_mobile.php";
      } elseif ($_GET['module'] == 'minta_paket') {
        include "form_minta_paket.php";
      } elseif ($_GET['module'] == 'lap_transaksi') {
        include "modul/lap_transaksi.php";
      } elseif ($_GET['module'] == 'rekap_transaksi') {
        include "modul/rekap_transaksi.php";
      } elseif ($_GET['module'] == 'rekap_transaksi1') {
        include "modul/rekap_transaksi_donor.php";
      } elseif ($_GET['module'] == 'rekap_transaksi2') {
        include "modul/rekap_transaksi_donor1.php";
      } elseif ($_GET['module'] == 'edit_instansi') {
        include "modul/edit_instansi.php";
      } elseif ($_GET['module'] == 'edit_instansi2') {
        include "modul/edit_instansi2.php";
      } elseif ($_GET['module'] == 'piagam') {
        include "modul/piagam.php";
      } elseif ($_GET['module'] == 'laporan_piagam') {
        include "modul/laporan_piagam.php";
      } elseif ($_GET['module'] == 'ganti_passwd') {
        include "modul/ganti_passwd.php";
      } elseif ($_GET['module'] == 'checkup') {
        include "modul/medical_checkup.php";
      } elseif ($_GET['module'] == 'sejarah') {
        include "modul/sejarah_pendonor.php";
      } elseif ($_GET['module'] == 'list_sejarah') {
        include "modul/sejarah.php";
      } elseif ($_GET['module'] == 'check') {
        include "modul/search_med_check.php";
      } elseif ($_GET['module'] == 'pendonor_instansi') {
        include "modul/donor_instansi.php";
      } elseif ($_GET['module'] == 'updatekantong') {
        include "modul/update_sah_kantong.php";
      } elseif ($_GET['module'] == 'sahkantong') {
        include "modul/pengesahankantong.php";
      } elseif ($_GET['module'] == 'deltransaksi') {
        include "modul/del_transaksi.php";
      } elseif ($_GET['module'] == 'delmedical') {
        include "modul/del_med_check.php";
      } elseif ($_GET['module'] == 'rincian_minta_barang') {
        include "logistik/rincian_transaksi_minta_barang.php";
      }

      //sms
      elseif ($_GET['module'] == 'sms_inbox') {
        include "modul/sms_inbox.php";
      } elseif ($_GET['module'] == 'sms_pending') {
        include "modul/sms_outbox.php";
      } elseif ($_GET['module'] == 'sms_setting') {
        include "modul/sms_setting.php";
      } elseif ($_GET['module'] == 'sms_broadcast') {
        include "modul/sms_broadcast.php";
      } elseif ($_GET['module'] == 'rekap_sms') {
        include "modul/fungsi_indotgl.php";
        include "modul/rekap_sms.php";
      } elseif ($_GET['module'] == 'smsidi') {
        //include  "modul/sms2/sms2.php";
        include "sms.php";

      } elseif ($_GET['module'] == 'mobile_sms') {
        include "mobile_sms.php";
      } elseif ($_GET['module'] == 'sms_broadcast_ultah') {
        include "modul/sms_broadcast_ultah.php";
      } elseif ($_GET['module'] == 'broadcast_donor') {
        include "modul/cron_sms.php";
      } elseif ($_GET['module'] == 'double_pendonor') {
        include "modul/double_pendonor.php";
      } elseif ($_GET['module'] == 'balas_sms') {
        include "modul/sms_balas.php";
      } elseif ($_GET['module'] == 'hapus_sms_inbox') {
        include "modul/sms_inbox_hapus.php";
      } elseif ($_GET['module'] == 'kosongkan_outbox') {
        include "modul/sms_kosongkan_outbox.php";
      } elseif ($_GET['module'] == 'sms_staf') {
        include "modul/sms_staf.php";
      } elseif ($_GET['module'] == 'sms_manual') {
        include "modul/sms_manual.php";
      } elseif ($_GET['module'] == 'cek_pulsa') {
        include "modul/cek_pulsa.php";
      } elseif ($_GET['module'] == 'historycetak') {
        include "modul/historycetak.php";
      }





      //sejarah donor
      elseif ($_GET['module'] == 'sejarah') {
        include "modul/sejarah_pendonor.php";
      } elseif ($_GET['module'] == 'list_sejarah') {
        include "modul/sejarah.php";
      } elseif ($_GET['module'] == 'rekap_sejarah') {
        include "modul/sejarah_donor_xls.php";
      } elseif ($_GET['module'] == 'tambah_kategori') {
        include "modul/tambah_header_instansi.php";
      }

      //apheresis
      elseif ($_GET['module'] == 'pengambilan_apheresis') {
        include "modul/pengambilan_darah_apheresis.php";
      } elseif ($_GET['module'] == 'pengajuan_piagam') {
        include "modul/rekap_pengajuan_piagam.php";
      }

      //upload ulang
      elseif ($_GET['module'] == 'upload_ulang') {
        include "modul/upload_mu_server_ulang.php";
      } elseif ($_GET['module'] == 'rekap_transaksi_sum') {
        include "modul/rekap_rencana_jadwal.php";
      }

      //cetakformdonor
      elseif ($_GET['module'] == 'form_donor') {
        include "modul/data_pendonor2.php";

      }
      //history donor
      elseif ($_GET['module'] == 'history') {
        include "modul/sejarah_donor.php";
      }
      //history luar
      elseif ($_GET['module'] == 'history_luar') {
        include "modul/sejarah_donor_luar.php";
      }

      //input transaksi  donor
      elseif ($_GET['module'] == 'transaksi_donor_lama') {
        include "modul/input_transaksi_donor.php";
      }
      //Cari Pendonor
      elseif ($_GET['module'] == 'cari_pendonor') {
        include "modul/cari_pendonor.php";
      }
      //kunci instansi
      elseif ($_GET['module'] == 'kunci_instansi') {
        include "modul/kunci_instansi.php";
      }
      // Pendonor
      elseif ($_GET['module'] == 'pendonor') {
        include "mobile_donor.php";
      }
      // donor baru
      elseif ($_GET['module'] == 'donor_baru') {
        include "modul/registrasi.php";
      }
      // donor ulang
      elseif ($_GET['module'] == 'donor_ulang') {
        include "modul/cari_pendonor.php";
      }
      // luar kota
      elseif ($_GET['module'] == 'luarkota') {
        include "modul/luar_kota22.php";
      }
      //manual
      elseif ($_GET['module'] == 'manual_mu') {
        include "dokumentasimobile.php";
      }
      //manual
      elseif ($_GET['module'] == 'mobile_online') {
        include "tpk/index.php";
      }
      //Rekap donasi PMK 83
      elseif ($_GET['module'] == 'rekap_mu') {
        include "modul/rekap_mobile_unit.php";
      } elseif ($_GET['module'] == 'rekap_mu1') {
        include "modul/rekap_mobile_unit_1.php";
      }
  }
}
?>