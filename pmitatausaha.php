<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
  echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";
}
if ($_SESSION['leveluser'] == 'tatausaha') { ?>
  <!doctype html>
  <html>

  <head>
    <title>SIMDONDAR</title>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
  </head>
<?php
  switch (@$_GET['act']) {
    default:
      if (@$_GET['rstock'] == '1')
        include "modul/stock_komponen.php";
      include "config/koneksi.php";
      include "config/fungsi_combobox.php";
      include "config/library.php";
      if ($_GET['module'] == 'home') {
        echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
      } elseif ($_GET['module'] == 'profile') {
        include "tatausaha_profile.php";
      } elseif ($_GET['module'] == 'statistik') {
        include "tatausaha_statistik.php";
      } elseif ($_GET['module'] == 'dataumum') {
        include "profile/data_umum.php";
      } elseif ($_GET['module'] == 'set_tanggal') {
        include "profile/set_tanggal_pelaporan.php";
      } elseif ($_GET['module'] == 'lap_umum') {
        include "profile/lap_data_umum.php";
      } elseif ($_GET['module'] == 'rpt_umum') {
        include "profile/lap_data_umum_rpt.php";
      } elseif ($_GET['module'] == 'datapelayanan') {
        include "profile/data_pelayanan.php";
      } elseif ($_GET['module'] == 'lap_pelayanan') {
        include "profile/lap_pelayanan.php";
      } elseif ($_GET['module'] == 'rpt_pelayanan') {
        include "profile/lap_pelayanan_rpt.php";
      } elseif ($_GET['module'] == 'dataimmunohematologi') {
        include "profile/data_immunohematologi.php";
      } elseif ($_GET['module'] == 'lap_immunohematologi') {
        include "profile/lap_immunohematologi.php";
      } elseif ($_GET['module'] == 'rpt_immunohematologi') {
        include "profile/lap_immunohematologi_rpt.php";
      } elseif ($_GET['module'] == 'databangunan') {
        include "profile/data_bangunan.php";
      } elseif ($_GET['module'] == 'tambahbangunan') {
        include "profile/tambah_bangunan.php";
      } elseif ($_GET['module'] == 'reaksi_transfusi') {
        include "profile/reaksi_td.php";
      } elseif ($_GET['module'] == 'detail_reaksitd') {
        include "profile/reaksi_td_detail.php";
      } elseif ($_GET['module'] == 'aksi') {
        include "profile/profile_action.php";
      } elseif ($_GET['module'] == 'laporan') {
        include "tatausaha_laporan.php";
      } elseif ($_GET['module'] == 'musnah') {
        include "profile/laporan_pemusnahan_darah.php";
      } elseif ($_GET['module'] == 'musnah_act') {
        include "profile/lap_musnah_act.php";
      } elseif ($_GET['module'] == 'rpt_musnah') {
        include "profile/laporan_pemusnahan_darah_rpt.php";
      } elseif ($_GET['module'] == 'personalia') {
        include "profile/data_sdm.php";
      } elseif ($_GET['module'] == 'tambahpersonalia') {
        include "profile/tambah_sdm.php";
      } elseif ($_GET['module'] == 'lap_personalia') {
        include "profile/lap_sdm.php";
      } elseif ($_GET['module'] == 'rpt_personalia') {
        include "profile/lap_sdm_rpt.php";
      } elseif ($_GET['module'] == 'komponen') {
        include "profile/laporan_komponen_darah.php";
      } elseif ($_GET['module'] == 'rpt_komponen') {
        include "profile/laporan_komponen_darah_rpt.php";
      } elseif ($_GET['module'] == 'permintaan') {
        include "profile/laporan_permintaan_darah.php";
      } elseif ($_GET['module'] == 'rpt_permintaan') {
        include "profile/laporan_permintaan_darah_rpt.php";
      } elseif ($_GET['module'] == 'pendonor') {
        include "profile/laporan_jumlah_pendonor.php";
      } elseif ($_GET['module'] == 'rpt_pendonor') {
        include "profile/laporan_jumlah_pendonor_rpt.php";
      } elseif ($_GET['module'] == 'imltd') {
        include "profile/laporan_imltd_all_sample.php";
      } elseif ($_GET['module'] == 'rpt_imltd') {
        include "profile/laporan_imltd_all_sample_rpt.php";
      } elseif ($_GET['module'] == 'imltd_donasi') {
        include "profile/laporan_imltd_only_donasi.php";
      } elseif ($_GET['module'] == 'rpt_imltd_donasi') {
        include "profile/laporan_imltd_only_donasi_rpt.php";
      } elseif ($_GET['module'] == 'lap_donasi_wb') {
        include "profile/laporan_donasi_wb.php";
      } elseif ($_GET['module'] == 'rpt_donasi_wb') {
        include "profile/laporan_donasi_wb_rpt.php";
      } elseif ($_GET['module'] == 'lap_donasi_aph') {
        include "profile/laporan_donasi_aph.php";
      } elseif ($_GET['module'] == 'rpt_donasi_aph') {
        include "profile/laporan_donasi_aph_rpt.php";
      } elseif ($_GET['module'] == 'upload') {
        include "profile/upload.php";
      } elseif ($_GET['module'] == 'cek_laporan') {
        include "profile/upload_cek.php";
      } elseif ($_GET['module'] == 'ganti_menu') {
        include "ganti_menu.php";
      } elseif ($_GET['module'] == 'ganti_passwd') {
        include "modul/ganti_passwd.php";
      } elseif ($_GET['module'] == 'graphdonor') {
        include  "profile/grafik_pendonor.php";
      } elseif ($_GET['module'] == 'graphdonasi') {
        include  "profile/grafik_donasi.php";
      } elseif ($_GET['module'] == 'graphtrendbulanan') {
        include  "profile/grafik_trend_donasi.php";
      } elseif ($_GET['module'] == 'graphpengujian') {
        include  "profile/grafik_pengujian.php";
      } elseif ($_GET['module'] == 'graphkomponen') {
        include  "profile/grafik_komponen.php";
      } elseif ($_GET['module'] == 'graphdistribusi') {
        include  "profile/grafik_distribusi.php";
      }
      // elseif ($_GET['module'] == 'graphdonor') {
      //   include "laporan/graph_donor.php";
      // } elseif ($_GET['module'] == 'graphdonasi') {
      //   include "laporan/graph_penyumbangan.php";
      // } elseif ($_GET['module'] == 'graphtrendbulanan') {
      //   include "laporan/graph_bulanan.php";
      // } elseif ($_GET['module'] == 'graphtrendbulanan_dsdp') {
      //   include "laporan/graph_bulanan_dsdp.php";
      // } elseif ($_GET['module'] == 'graphtrendbulanan_kel') {
      //   include "laporan/graph_bulanan_kel.php";
      // } elseif ($_GET['module'] == 'graphtrendbulanan_lokasi') {
      //   include "laporan/graph_bulanan_lokasi.php";
      // } elseif ($_GET['module'] == 'graphtrendbulanan_lamabaru') {
      //   include "laporan/graph_bulanan_lamabaru.php";
      // } elseif ($_GET['module'] == 'graphtrendbulanan_golabo') {
      //   include "laporan/graph_bulanan_golabo.php";
      // } elseif ($_GET['module'] == 'graphtrendbulanan_rh') {
      //   include "laporan/graph_bulanan_rh.php";
      // } 
      elseif ($_GET['module'] == 'komponen_musnah') {
        include "laborat_musnah.php";
      }

      /*elseif ($_GET['module']=='musnah'){
                  include "musnah/musnah24.php";
      }*/ elseif ($_GET['module'] == 'musnahdelrow') {
        include "musnah/musnah_proses.php";
      } elseif ($_GET['module'] == 'musnahbatal') {
        include "musnah/musnah_proses.php";
      } elseif ($_GET['module'] == 'musnahlist') {
        include "musnah/musnah_list.php";
      } elseif ($_GET['module'] == 'musnah_rpt_view') {
        include "musnah/musnah_view.php";
      } elseif ($_GET['module'] == 'musnah_cetakberita') {
        include "musnah/musnah_cetakberita.php";
      } elseif ($_GET['module'] == 'musnah_serahterima') {
        include "musnah/musnah_serah.php";
      } elseif ($_GET['module'] == 'rincian_darah_buang') {
        include "musnah/musnah_rincian.php";
      }
  }
}
?>