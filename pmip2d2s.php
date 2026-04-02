<?php

session_start();

if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
    echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>\n";
    echo "<center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";
}

if ($_SESSION['leveluser'] == "p2d2s") {
    echo "<!doctype html>\n";
    echo "<html>\n";
    echo "<head>\n";
    echo "<title>SIMDONDAR</title>\n";
    echo "<script language=javascript src=\"idcard.js\" type=\"text/javascript\"></script>\n";
    echo "<script language=javascript src=\"util.js\" type=\"text/javascript\"></script>\n";
    echo "<link href=\"css/style.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
    echo "</head>\n";

    switch ($_GET['act']) {
        default:
            if ($_GET["rstock"] == "1") {
                include "modul/stock.php";
            }
            if ($_GET["rstock"] == "3") {
                include "modul/stock1.php";
            }

            include "config/koneksi.php";
            include "config/fungsi_combobox.php";
            include "config/library.php";

            if ($_GET['module'] == "home") {
                echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
            } elseif ($_GET['module'] == "rekap_transaksi_harian") {
                include "modul/rekap_transaksi_donor.php";
            } elseif ($_GET['module'] == "rekap_transaksi_harian1") {
                include "p2d2s_rekap.php";
            } elseif ($_GET['module'] == "rekap_transaksi_sum") {
                include "modul/rekap_transaksi_harian_summary.php";
            } elseif ($_GET['module'] == "mobile_cetak") {
                include "color.inc";
                include "modul/cetak_id.php";
            } elseif ($_GET['module'] == "cetak_id") {
                include "color.inc";
                include "modul/cetak_id.php";
            } elseif ($_GET['module'] == "keluar") {
                include "modul/keluar.php";
            } elseif ($_GET['module'] == "search_pendonor") {
                include "modul/search_pendonor.php";
            } elseif ($_GET['module'] == "double_pendonor") {
                include "modul/double_pendonor.php";
            } elseif ($_GET['module'] == "registrasi") {
                include "modul/registrasi.php";
            } elseif ($_GET['module'] == "spendonor") {
                include "modul/search_pendonor_edit.php";
            } elseif ($_GET['module'] == "cetak_jadwal") {
                include "modul/jadwal_mu.php";
            } elseif ($_GET['module'] == "cetak_surat") {
                include "modul/surat_mu.php";
            } elseif ($_GET['module'] == "eregistrasi") {
                include "modul/edit_registrasi.php";
            } elseif ($_GET['module'] == "transaksi_donor") {
                include "modul/transaksi_donor.php";
            } elseif ($_GET['module'] == "spengambilan") {
                include "modul/search_transaksi.php";
            } elseif ($_GET['module'] == "pengambilan") {
                include "modul/pengambilan_darah.php";
            } elseif ($_GET['module'] == "pengesahan_pengambilan") {
                include "modul/pengesahan_ambil_darah.php";
            } elseif ($_GET['module'] == "pengambilan_apheresis") {
                include "modul/pengambilan_darah_apheresis.php";
            } elseif ($_GET['module'] == "downloadtogerai") {
                include "modul/downto_gerai.php";
            } elseif ($_GET['module'] == "download") {
                include "modul/downto_mu.php";
            } elseif ($_GET['module'] == "kantong_mu") {
                include "modul/list_kantong_mu.php";
            } elseif ($_GET['module'] == "uploadfromgerai") {
                include "modul/upload_from_gerai.php";
            } elseif ($_GET['module'] == "upload") {
                include "modul/upload_mu_server.php";
            } elseif ($_GET['module'] == "aturuser") {
                include "modul/mod_user.php";
            } elseif ($_GET['module'] == "rtransaksi") {
                include "modul/rtransaksi.php";
            } elseif ($_GET['module'] == "stock") {
                include "modul/stock.php";
            } elseif ($_GET['module'] == "tambah_instansi") {
                include "modul/tambah_instansi1.php";
            } elseif ($_GET['module'] == "data_jadwal_mobile_now") {
                include "modul/data_jadwal_mobile_now_new.php";
            } elseif ($_GET['module'] == "data_jadwal_mobile") {
                include "modul/data_jadwal_mobile.php";
            } elseif ($_GET['module'] == "entry_jadwal_mobile") {
                include "modul/add_load_edit.php";
            } elseif ($_GET['module'] == "jadwal_mobile") {
                include "mobile_jadwal.php";
            } elseif ($_GET['module'] == "delpetugas") {
                include "modul/del_petugas.php";
            } elseif ($_GET['module'] == "mobile_transfer") {
                include "mobile_transfer.php";
            } elseif ($_GET['module'] == "mobile_pendonor") {
                include "mobile_pendonor.php";
            } elseif ($_GET['module'] == "p2d2s_transaksi") {
                include "p2d2s_transaksi.php";
            } elseif ($_GET['module'] == "laporan_catatandonor") {
                include "modul/laporan_catatandonor.php";
            } elseif ($_GET['module'] == "pendonor_cekal_instansi") {
                include "modul/donor_cekal_instansi.php";
            } elseif ($_GET['module'] == "ganti_menu") {
                include "ganti_menu.php";
            } elseif ($_GET['module'] == "minta_barang") {
                include "modul/form_minta_mobile.php";
            } elseif ($_GET['module'] == "minta_paket") {
                include "form_minta_paket.php";
            } elseif ($_GET['module'] == "lap_transaksi") {
                include "modul/lap_transaksi.php";
            } elseif ($_GET['module'] == "rekap_transaksi") {
                include "modul/rekap_transaksi.php";
            } elseif ($_GET['module'] == "rekap_transaksi1") {
                include "modul/rekap_transaksi_donor.php";
            } elseif ($_GET['module'] == "rekap_transaksi2") {
                include "modul/rekap_transaksi_donor1.php";
            } elseif ($_GET['module'] == "edit_instansi") {
                include "modul/edit_instansi.php";
            } elseif ($_GET['module'] == "edit_instansi2") {
                include "modul/edit_instansi2.php";
            } elseif ($_GET['module'] == "piagam") {
                include "modul/piagam.php";
            } elseif ($_GET['module'] == "ajukan_piagam") {
                include "modul/ajukan_piagam.php";
            } elseif ($_GET['module'] == "edit_piagam") {
                include "modul/edit_piagam.php";
            } elseif ($_GET['module'] == "edit_piagam1") {
                include "modul/edit_piagam1.php";
            } elseif ($_GET['module'] == "laporan_piagam") {
                include "modul/laporan_piagam.php";
            } elseif ($_GET['module'] == "ganti_passwd") {
                include "modul/ganti_passwd.php";
            } elseif ($_GET['module'] == "checkup") {
                include "modul/medical_checkup.php";
            } elseif ($_GET['module'] == "sejarah") {
                include "modul/sejarah_pendonor.php";
            } elseif ($_GET['module'] == "list_sejarah") {
                include "modul/sejarah.php";
            } elseif ($_GET['module'] == "check") {
                include "modul/search_med_check.php";
            } elseif ($_GET['module'] == "pendonor_instansi") {
                include "modul/donor_instansi.php";
            } elseif ($_GET['module'] == "updatekantong") {
                include "modul/update_sah_kantong.php";
            } elseif ($_GET['module'] == "sahkantong") {
                include "modul/pengesahankantong.php";
            } elseif ($_GET['module'] == "deltransaksi") {
                include "modul/del_transaksi.php";
            } elseif ($_GET['module'] == "delmedical") {
                include "modul/del_med_check.php";
            } elseif ($_GET['module'] == "rincian_minta_barang") {
                include "logistik/rincian_transaksi_minta_barang.php";
            } elseif ($_GET['module'] == "sms_inbox") {
                include "modul/sms_inbox.php";
            } elseif ($_GET['module'] == "sms_pending") {
                include "modul/sms_outbox.php";
            } elseif ($_GET['module'] == "sms_setting") {
                include "modul/sms_setting.php";
            } elseif ($_GET['module'] == "sms_broadcast") {
                include "modul/sms_broadcast.php";
            } elseif ($_GET['module'] == "rekap_sms") {
                include "modul/fungsi_indotgl.php";
                include "modul/rekap_sms.php";
            } elseif ($_GET['module'] == "smsidi") {
                include "sms.php";
            } elseif ($_GET['module'] == "mobile_sms") {
                include "mobile_sms.php";
            } elseif ($_GET['module'] == "balas_sms") {
                include "modul/sms_balas.php";
            } elseif ($_GET['module'] == "kosongkan_outbox") {
                include "modul/sms_kosongkan_outbox.php";
            } elseif ($_GET['module'] == "sms_broadcast_ultah") {
                include "modul/sms_broadcast_ultah.php";
            } elseif ($_GET['module'] == "broadcast_donor") {
                include "modul/cron_sms.php";
            } elseif ($_GET['module'] == "double_pendonor") {
                include "modul/double_pendonor.php";
            } elseif ($_GET['module'] == "hapus_sms_inbox") {
                include "modul/sms_inbox_hapus.php";
            } elseif ($_GET['module'] == "sms_staf") {
                include "modul/sms_staf.php";
            } elseif ($_GET['module'] == "sms_manual") {
                include "modul/sms_manual.php";
            } elseif ($_GET['module'] == "cek_pulsa") {
                include "modul/cek_pulsa.php";
            } elseif ($_GET['module'] == "historycetak") {
                include "modul/historycetak.php";
            } elseif ($_GET['module'] == "rekap_sejarah") {
                include "modul/sejarah_donor_xls.php";
            } elseif ($_GET['module'] == "tambah_kategori") {
                include "modul/tambah_header_instansi.php";
            } elseif ($_GET['module'] == "pengajuan_piagam") {
                include "modul/rekap_pengajuan_piagam.php";
            } elseif ($_GET['module'] == "upload_ulang") {
                include "modul/upload_mu_server_ulang.php";
            } elseif ($_GET['module'] == "form_donor") {
                include "modul/data_pendonor2.php";
            } elseif ($_GET['module'] == "cari_pendonor") {
                include "modul/cari_pendonor.php";
            } elseif ($_GET['module'] == "logpendonor") {
                include "modul/entry_logpendonor.php";
            } elseif ($_GET['module'] == "del_logpendonor") {
                include "modul/del_logpendonor.php";
            } elseif ($_GET['module'] == "history") {
                include "modul/sejarah_donor.php";
            } elseif ($_GET['module'] == "history_donor_instansi") {
                include "modul/history_donor_instansi.php";
            } elseif ($_GET['module'] == "sahkan_kantong") {
                include "modul/sahkan_kantong_donor.php";
            } elseif ($_GET['module'] == "transaksi_donor_lama") {
                include "modul/input_transaksi_donor.php";
            } elseif ($_GET['module'] == "rincian_kegiatan") {
                include "modul/kegiatan.php";
            } elseif ($_GET['module'] == "manual_p2dds") {
                include "dokumentasippdds.php";
            } elseif ($_GET['module'] == "admin_wa") {
                include "whatsapp/admin_wa.php";
            } elseif ($_GET['module'] == "wa_manual") {
                include "whatsapp/wa_manual.php";
            } elseif ($_GET['module'] == "wa_broadcast") {
                include "whatsapp/wa_broadcast.php";
            } elseif ($_GET['module'] == "wa_inbox") {
                include "whatsapp/wa_inbox.php";
            } elseif ($_GET['module'] == "balas_wa") {
                include "whatsapp/wa_manual.php";
            } elseif ($_GET['module'] == "wa_outbox") {
                include "whatsapp/wa_outbox.php";
            } elseif ($_GET['module'] == "wa_sent") {
                include "whatsapp/fungsi_indotgl.php";
                include "whatsapp/rekap_wa.php";
            } elseif ($_GET['module'] == "hapus_wa_inbox") {
                include "whatsapp/wa_inbox_hapus.php";
            } elseif ($_GET['module'] == "hapus_wa_outbox") {
                include "whatsapp/wa_outbox_hapus.php";
            } elseif ($_GET['module'] == "kosongkan_wa_inbox") {
                include "whatsapp/wa_inbox_kosong.php";
            } elseif ($_GET['module'] == "kosongkan_wa_outbox") {
                include "whatsapp/wa_outbox_kosong.php";
            } elseif ($_GET['module'] == "wa_ultah") {
                include "whatsapp/wa_broadcast_ultah.php";
            } elseif ($_GET['module'] == "wa_setting") {
                include "whatsapp/wa_setting.php";
            } elseif ($_GET['module'] == "wa_instansi") {
                include "whatsapp/wa_donor_ins.php";
            } elseif ($_GET['module'] == "dtd") {
                include "dtd.php";
            } elseif ($_GET['module'] == "dtd_caridonor") {
                include "dtd/dtd_pendonor.php";
            } elseif ($_GET['module'] == "dtd_konfirmasi") {
                include "dtd/dtd_list_to_konfirm.php";
            } elseif ($_GET['module'] == "dtd_transaksi") {
                include "dtd/dtd_list_to_transaksi_donor.php";
            } elseif ($_GET['module'] == "dtd_transaksi_donor") {
                include "dtd/dtd_transaksi_donor.php";
            } elseif ($_GET['module'] == "mobile_app") {
                include "p2d2s_mobileapp.php";
            } elseif ($_GET['module'] == "mobile_antrean") {
                include "mobile/mobile_list_antrean.php";
            } elseif ($_GET['module'] == "mobile_antreanmu") {
                include "mobile/mobile_list_antrean_mu.php";
            } elseif ($_GET['module'] == "mobile_transaksi") {
                include "mobile/mobile_antrekan.php";
            } elseif ($_GET['module'] == "mobile_ubahstatus") {
                include "mobile/mobile_update_status_antrean.php";
            } elseif ($_GET['module'] == "mobile_ubahstatusmu") {
                include "mobile/mobile_update_status_antreanmu.php";
            } elseif ($_GET['module'] == "mobile_antrean_proses") {
                include "mobile/mobile_proses_antrean.php";
            } elseif ($_GET['module'] == "mobile_transaksimu") {
                include "mobile/mobile_transaksimu.php";
            }
    }
}