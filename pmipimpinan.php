<?php
session_start();

// Cek apakah user sudah login
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
    echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>\n";
    echo "<center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href='index.php' target='_top'><b>LOGIN</b></a></center>";
}

// Hanya jalan jika level user adalah pimpinan
if ($_SESSION['leveluser'] == "pimpinan") {

    echo "<head>\n";
    echo "<title>SIMDONDAR</title>\n";
    echo "<script language=\"javascript\" src=\"idcard.js\" type=\"text/javascript\"></script>\n";
    echo "<script language=\"javascript\" src=\"util.js\" type=\"text/javascript\"></script>\n";
    echo "<link href=\"css/style.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
    echo "</head>\n";

    switch ($_GET['act']) {

        default:

            if ($_GET['rstock'] == "1") {
                include "modul/stock.php";
            }
            if ($_GET['rstock'] == "3") {
                include "modul/stock1.php";
            }

            include "config/koneksi.php";
            include "config/fungsi_combobox.php";
            include "config/library.php";

            if ($_GET['module'] == "home") {
                echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
            } elseif ($_GET['module'] == "musnah") {
                include "modul/musnah.php";
            } elseif ($_GET['module'] == "keluar") {
                include "modul/keluar.php";
            } elseif ($_GET['module'] == "rekap") {
                include "pimpinan_rekap.php";
            } elseif ($_GET['module'] == "rekap_transaksi") {
                include "modul/rekap_transaksi_donor.php";
            } elseif ($_GET['module'] == "rekap_transaksi1") {
                include "modul/rekap_transaksi_donor.php";
            } elseif ($_GET['module'] == "rekap_transaksi2") {
                include "modul/rekap_transaksi_donor1.php";
            } elseif ($_GET['module'] == "rekap_permintaan") {
                include "modul/rekap_permintaan_harian.php";
            } elseif ($_GET['module'] == "pimpinan_imltd") {
                include "pimpinan_imltd.php";
            } elseif ($_GET['module'] == "pimpinan_laporan") {
                include "pimpinan_laporan.php";
            } elseif ($_GET['module'] == "komponen") {
                include "modul/komponen.php";
            } elseif ($_GET['module'] == "penambahan_kantong") {
                include "modul/penambahan_kantong.php";
            } elseif ($_GET['module'] == "pengesahan_kantong") {
                include "modul/pengesahan_kantong.php";
            } elseif ($_GET['module'] == "skantong") {
                include "modul/skantong.php";
            } elseif ($_GET['module'] == "skantong1") {
                include "modul/skantong1.php";
            } elseif ($_GET['module'] == "reagen") {
                include "modul/entry_reagen.php";
            } elseif ($_GET['module'] == "dreagen") {
                include "modul/daftar_pegawai.php";
            } elseif ($_GET['module'] == "supplier") {
                include "modul/entry_suplier.php";
            } elseif ($_GET['module'] == "permintaan") {
                include "modul/mod_permintaan.php";
            } elseif ($_GET['module'] == "hasil_lab") {
                include "modul/hasil_lab.php";
            } elseif ($_GET['module'] == "shasil_labl") {
                require_once("color.inc");
                include "modul/label_lab.php";
            } elseif ($_GET['module'] == "shasil_lab") {
                include "modul/shasil_lab.php";
            } elseif ($_GET['module'] == "elisa") {
                include "modul/elisa.php";
            } elseif ($_GET['module'] == "update_elisa") {
                include "modul/update_elisa.php";
            } elseif ($_GET['module'] == "update_rapidtest") {
                include "modul/update_rapidtest.php";
            } elseif ($_GET['module'] == "hlab_bonus") {
                include "modul/lab_bonus.php";
            } elseif ($_GET['module'] == "pengesahan") {
                include "modul/pengesahan_rapidtest.php";
            } elseif ($_GET['module'] == "label_cross") {
                require_once("color.inc");
                include "modul/label_cross.php";
            } elseif ($_GET['module'] == "reagen_nonaktif") {
                include "modul/reagen_nonaktif.php";
            } elseif ($_GET['module'] == "reagen_aktif") {
                include "modul/reagen_aktif.php";
            } elseif ($_GET['module'] == "sarancrossmatch") {
                include "modul/mod_sarancross.php";
            } elseif ($_GET['module'] == "crossmatch") {
                include "modul/mod_crossmatch.php";
            } elseif ($_GET['module'] == "user") {
                include "modul/registrasi.php";
            } elseif ($_GET['module'] == "cetak_id") {
                include "modul/cetak_id.php";
            } elseif ($_GET['module'] == "transaksi") {
                include "modul/search_pendonor.php";
            } elseif ($_GET['module'] == "eregistrasi") {
                include "modul/edit_registrasi.php";
            } elseif ($_GET['module'] == "transaksi_donor") {
                include "transaksi_donor.php";
            } elseif ($_GET['module'] == "spengambilan") {
                include "modul/search_transaksi.php";
            } elseif ($_GET['module'] == "pengambilan") {
                include "modul/pengambilan_darah.php";
            } elseif ($_GET['module'] == "pengesahan_pengambilan") {
                include "modul/pengesahan_ambil_darah.php";
            } elseif ($_GET['module'] == "aturuser") {
                include "modul/mod_user.php";
            } elseif ($_GET['module'] == "aturagenda") {
                include "modul/mod_agendamn.php";
            } elseif ($_GET['module'] == "agendaedit") {
                include "modul/mod_agendamn1.php";
            } elseif ($_GET['module'] == "updateagenda") {
                include "modul/mod_updateagenda.php";
            } elseif ($_GET['module'] == "tambahagenda") {
                include "modul/mod_agendamn2.php";
            } elseif ($_GET['module'] == "entryagenda") {
                include "modul/mod_insertagenda.php";
            } elseif ($_GET['module'] == "agendahapus") {
                include "modul/mod_hapusagenda.php";
            } elseif ($_GET['module'] == "agendalist") {
                include "modul/mod_agendalist.php";
            } elseif ($_GET['module'] == "smsgroup") {
                include "modul/sms2/sms2.php";
            } elseif ($_GET['module'] == "smsidi") {
                include "modul/sms2/sms2.php";
            } elseif ($_GET['module'] == "rtransaksi") {
                include "modul/rtransaksi.php";
            } elseif ($_GET['module'] == "stock") {
                include "modul/stock.php";
            } elseif ($_GET['module'] == "pindah_titipan") {
                include "modul/pindah_titipan.php";
            } elseif ($_GET['module'] == "laborat_komponen") {
                include "laborat_komponen.php";
            } elseif ($_GET['module'] == "laborat_distribusi") {
                include "laborat_distribusi.php";
            } elseif ($_GET['module'] == "laborat_ujisaring") {
                include "laborat_ujisaring.php";
            } elseif ($_GET['module'] == "laborat_permintaan") {
                include "laborat_permintaan.php";
            } elseif ($_GET['module'] == "laborat_cetak") {
                include "laborat_cetak.php";
            } elseif ($_GET['module'] == "laborat_update") {
                include "laborat_update.php";
            } elseif ($_GET['module'] == "ganti_menu") {
                include "ganti_menu.php";
            } elseif ($_GET['module'] == "form_minta") {
                include "modul/form_minta.php";
            } elseif ($_GET['module'] == "form_bdrs") {
                include "modul/form_bdrs.php";
            } elseif ($_GET['module'] == "form_bdrsxls") {
                include "modul/form_bdrsxls.php";
            } elseif ($_GET['module'] == "ganti_passwd") {
                include "modul/ganti_passwd.php";
            } elseif ($_GET['module'] == "konfirmasi_gol_darah") {
                include "modul/konfirmasi_gol_darah1.php";
            } elseif ($_GET['module'] == "rekap_reaktif") {
                require_once("color.inc");
                include "modul/rekap_reaktif.php";
            } elseif ($_GET['module'] == "sejarah") {
                include "modul/sejarah_pendonor.php";
            } elseif ($_GET['module'] == "list_sejarah") {
                include "modul/sejarah.php";
            } elseif ($_GET['module'] == "admin_sms") {
                include "admin_sms.php";
            } elseif ($_GET['module'] == "admin_laporan") {
                include "admin_laporan.php";
            } elseif ($_GET['module'] == "admin_utility") {
                include "admin_utility.php";
            } elseif ($_GET['module'] == "laporan_kegiatan") {
                include "modul/lap_kegiatan.php";
            } elseif ($_GET['module'] == "laporan_peng_darah") {
                include "modul/lap_peng_darah.php";
            } elseif ($_GET['module'] == "laporan_uji_sharing") {
                include "modul/lap_uji_sharing.php";
            } elseif ($_GET['module'] == "aktif_udd") {
                include "modul/aktif_udd.php";
            } elseif ($_GET['module'] == "laporan_buang_darah") {
                include "modul/lap_buang_darah.php";
            } elseif ($_GET['module'] == "updatekantong") {
                include "modul/update_sah_kantong.php";
            } elseif ($_GET['module'] == "rincian_minta_barang") {
                include "logistik/rincian_transaksi_minta_barang.php";
            } elseif ($_GET['module'] == "form_donor") {
                include "modul/data_pendonor2.php";
            } elseif ($_GET['module'] == "history") {
                include "modul/sejarah_donor.php";
            } elseif ($_GET['module'] == "transaksi_donor_lama") {
                include "modul/input_transaksi_donor.php";
            } elseif ($_GET['module'] == "laporan") {
                include "laporan/filter_laporan.php";
            } elseif ($_GET['module'] == "lap_lttd1") {
                include "laporan/lttd1.php";
            } elseif ($_GET['module'] == "lap_lttd2") {
                include "laporan/lttd2.php";
            } elseif ($_GET['module'] == "lap_lttd3") {
                include "laporan/lttd3.php";
            } elseif ($_GET['module'] == "lap_lttd4") {
                include "laporan/lttd4.php";
            } elseif ($_GET['module'] == "lap_lttd5") {
                include "laporan/lttd5.php";
            } elseif ($_GET['module'] == "lap_lttd6") {
                include "laporan/lttd6.php";
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
            // } elseif ($_GET['module'] == "graphdonasi") {
            //     include "profile/grafik_donasi.php";
            // } elseif ($_GET['module'] == "graphdonor") {
            //     include "profile/grafik_trend_donasi.php";
            // } elseif ($_GET['module'] == "graphpengujian") {
            //     include "profile/grafik_pengujian.php";
            // } elseif ($_GET['module'] == "graphkomponen") {
            //     include "profile/grafik_komponen.php";
            // } elseif ($_GET['module'] == "graphdistribusi") {
            //     include "profile/grafik_distribusi.php";
            // }
            // elseif ($_GET['module'] == "audit_trial") {
            //     include "modul/log_user.php";
            // }
            elseif ($_GET['module'] == "audit_trial") {
                include "pmf/pmf_audittrail.php";
            } elseif ($_GET['module'] == "login_data") {
                include "modul/data_login.php";
            } elseif ($_GET['module'] == "manual") {
                include "pimpinan_manual.php";
            } elseif ($_GET['module'] == "manual_p2dds") {
                include "dokumentasippdds.php";
            } elseif ($_GET['module'] == "manual_aftap") {
                include "dokumentasiaftap.php";
            } elseif ($_GET['module'] == "manual_logistik") {
                include "dokumentasilogistik.php";
            } elseif ($_GET['module'] == "manual_seleksi") {
                include "dokumentasidns.php";
            } elseif ($_GET['module'] == "manual_mu") {
                include "dokumentasimobile.php";
            } elseif ($_GET['module'] == "manual_kgd") {
                include "dokumentasikonfirmasi.php";
            } elseif ($_GET['module'] == "manual_imltd") {
                include "dokumentasiimltd.php";
            } elseif ($_GET['module'] == "manual_komponen") {
                include "dokumentasikomponen.php";
            } elseif ($_GET['module'] == "manual_loket") {
                include "dokumentasipasien.php";
            } elseif ($_GET['module'] == "manual_cross") {
                include "dokumentasicross.php";
            } elseif ($_GET['module'] == "rincian_kegiatan") {
                include "modul/kegiatan.php";
            } elseif ($_GET['module'] == "laporan_pmk") {
                include "laporan_pmk.php";
            } elseif ($_GET['module'] == "pmk_lap_donasi") {
                include "laporan/laporan_bulanan_wb.php";
            } elseif ($_GET['module'] == "pmk_lap_aphe") {
                include "laporan/laporan_bulanan_aph.php";
            } elseif ($_GET['module'] == "pmk_lap_imltd") {
                include "laporan/laporan_bulanan_ujisaring.php";
            } elseif ($_GET['module'] == "pmk_lap_permdarah") {
                include "laporan/laporan_bulanan_permdarah.php";
            } elseif ($_GET['module'] == "pmk_lap_komponen") {
                include "laporan/laporan_bulanan_komponen.php";
            } elseif ($_GET['module'] == "pmk_lap_jumdonor") {
                include "laporan/laporan_th_donor.php";
            } elseif ($_GET['module'] == "pmk_lap_baruulang") {
                include "laporan/laporan_th_donorbaruulang.php";
            }

            break;
    }

    echo "\n";
}