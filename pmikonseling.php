<?php
session_start();

if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
    echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>\n";
    echo "<center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href='index.php' target='_top'><b>LOGIN</b></a></center>";
    exit;
}

if ($_SESSION['leveluser'] == "konseling") {
    echo "<!doctype html>\n";
    echo "<html>\n";
    echo "<head>\n";
    echo "<title>SIMDONDAR</title>\n";
    echo "<script language='javascript' src='idcard.js' type='text/javascript'></script>\n";
    echo "<script language='javascript' src='util.js' type='text/javascript'></script>\n";
    echo "<link href='css/style.css' rel='stylesheet' type='text/css' />\n";
    echo "</head>\n";

    switch ($_GET['act']) {
        default:
            if ($_GET["rstock"] == "1") include "modul/stock.php";
            if ($_GET["rstock"] == "3") include "modul/stock1.php";

            include "config/koneksi.php";
            include "config/fungsi_combobox.php";
            include "config/library.php";

            if ($_GET['module'] == "home") {
                echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src='images/donate.jpg'>";
            } elseif ($_GET['module'] == "rekap") {
                include "modul/rekap_konseling.php";
            } elseif ($_GET['module'] == "rekap_darah_keluar") {
                include "modul/rekap_darah_keluar.php";
            } elseif ($_GET['module'] == "rekap_darah_keluar_lama") {
                include "modul/rekap_darah_keluar_lama.php";
            } elseif ($_GET['module'] == "rekap_darah_keluar_bdrs") {
                include "modul/rekap_darah_keluar_bdrs.php";
            } elseif ($_GET['module'] == "rekap_darah_keluar_udd") {
                include "modul/rekap_darah_keluar_udd.php";
            } elseif ($_GET['module'] == "musnah") {
                include "modul/musnah.php";
            } elseif ($_GET['module'] == "keluar") {
                include "modul/keluar.php";
            } elseif ($_GET['module'] == "rekap_darah_titip") {
                include "modul/rekap_darah_titip.php";
            } elseif ($_GET['module'] == "rekap_transaksi") {
                include "modul/rekap_transaksi_harian.php";
            } elseif ($_GET['module'] == "rekap_permintaan") {
                include "modul/rekap_permintaan_harian.php";
            } elseif ($_GET['module'] == "rekap_permintaan_lama") {
                include "modul/rekap_permintaan_harian_lama.php";
            } elseif ($_GET['module'] == "komponen") {
                include "modul/komponen.php";
            } elseif ($_GET['module'] == "penambahan_kantong") {
                include "modul/penambahan_kantong.php";
            } elseif ($_GET['module'] == "pengesahan_kantong") {
                include "modul/pengesahan_kantong.php";
            } elseif ($_GET['module'] == "skantong0") {
                include "modul/skantong0.php";
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
            } elseif ($_GET['module'] == "elisabayang") {
                include "modul/elisabayang.php";
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
            } elseif ($_GET['module'] == "chek_imltd") {
                include "modul/chek_imltd.php";
            } elseif ($_GET['module'] == "pindah_titipan") {
                include "modul/pindah_titipan.php";
            } elseif ($_GET['module'] == "laborat_konfirmasi") {
                include "laborat_konfirmasi.php";
            } elseif ($_GET['module'] == "laborat_komponen") {
                include "laborat_komponen.php";
            } elseif ($_GET['module'] == "laborat_distribusi") {
                include "laborat_distribusi.php";
            } elseif ($_GET['module'] == "konseling_ujisaring") {
                include "konseling_ujisaring.php";
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
            } elseif (
                $_GET['module'] == "update_dari_rujukan" ||
                $_GET['module'] == "update_dari_udd" ||
                $_GET['module'] == "update_dari_bdrs"
            ) {
                include "modul/decode.php";
            } elseif ($_GET['module'] == "kembali") {
                include "modul/mod_kembali.php";
            } elseif ($_GET['module'] == "form_rujukan") {
                include "modul/form_rujukan.php";
            } elseif ($_GET['module'] == "form_udd") {
                include "modul/form_udd.php";
            } elseif ($_GET['module'] == "form_bdrs") {
                include "modul/form_bdrs.php";
            } elseif ($_GET['module'] == "form_bdrsxls") {
                include "modul/form_bdrsxls.php";
            } elseif ($_GET['module'] == "ganti_passwd") {
                include "modul/ganti_passwd.php";
            } elseif ($_GET['module'] == "konfirmasi_gol_darah") {
                include "modul/konfirmasi_gol_darah1.php";
            } elseif ($_GET['module'] == "rekap_reaktif_konseling") {
                require_once("color.inc");
                include "modul/rekap_reaktif_konseling.php";
            } elseif ($_GET['module'] == "rekap_nonreaktif") {
                require_once("color.inc");
                include "modul/rekap_nonreaktif.php";
            } elseif ($_GET['module'] == "rekap_reaktifrapid") {
                require_once("color.inc");
                include "modul/rekap_reaktifrapid.php";
            } elseif ($_GET['module'] == "rekap_reaktif1_konseling") {
                require_once("color.inc");
                include "modul/rekap_reaktif1_konseling.php";
            } elseif ($_GET['module'] == "rekap_reaktif1_xls") {
                require_once("color.inc");
                include "modul/rekap_reaktif1_xls.php";
            } elseif ($_GET['module'] == "rekap_komponen") {
                include "modul/rekap_pembuatan_komponen.php";
            } elseif ($_GET['module'] == "rekap_konfirmasi") {
                include "modul/rekap_konfirmasi.php";
            } elseif ($_GET['module'] == "terima_dari_utd_lain") {
                require_once("color.inc");
                include "modul/terima_dari_utd_lain.php";
            } elseif ($_GET['module'] == "rekap_terima_darah_udd_lain") {
                require_once("color.inc");
                include "modul/rekap_terima_darah_udd_lain.php";
            } elseif ($_GET['module'] == "laporan_darah_buang") {
                include "modul/rekap_darah_buang.php";
            } elseif ($_GET['module'] == "updatekantong") {
                include "modul/update_sah_kantong.php";
            } elseif ($_GET['module'] == "rincian_minta_barang") {
                include "logistik/rincian_transaksi_minta_barang.php";
            } elseif ($_GET['module'] == "set_stok_sos") {
                include "modul/setting_stok_emergency.php";
            } elseif ($_GET['module'] == "rekap_cross") {
                include "modul/rekap_hasil_crossmatch.php";
            } elseif ($_GET['module'] == "kirim_rujukan") {
                require_once("color.inc");
                include "modul/kirim_rujukan.php";
            } elseif ($_GET['module'] == "rekap_rujukan") {
                include "modul/rekap_rujukan.php";
            } elseif ($_GET['module'] == "update_test_reagen") {
                include "modul/update_jml_reagen.php";
            } elseif ($_GET['module'] == "import") {
                include "laborat_imltd_import.php";
            } elseif ($_GET['module'] == "import_etimax3000") {
                include "modul/imltd_import_etimax3000.php";
            } elseif ($_GET['module'] == "import_etimax3000hbsag") {
                include "modul/imltd_import_etimax3000_hbsag.php";
            } elseif ($_GET['module'] == "import_etimax3000hcv") {
                include "modul/imltd_import_etimax3000_hcv.php";
            } elseif ($_GET['module'] == "import_etimax3000hiv") {
                include "modul/imltd_import_etimax3000_hiv.php";
            } elseif ($_GET['module'] == "import_etimax3000syp") {
                include "modul/imltd_import_etimax3000_syp.php";
            } elseif ($_GET['module'] == "import_etimax3000konfirmasi") {
                include "modul/imltd_import_etimax3000_konfirm.php";
            } elseif ($_GET['module'] == "import_etimax3000manual") {
                include "modul/imltd_import_etimax3000_manual.php";
            } elseif ($_GET['module'] == "import_etimax3000manualinput") {
                include "modul/imltd_import_etimax3000_manual_input.php";
            } elseif ($_GET['module'] == "cetakhasil") {
                include "modul/cetak_hasil_imltd.php";
            } elseif ($_GET['module'] == "cetakhasil_imltd") {
                include "modul/cetak_hasil_imltd_print.php";
            } elseif ($_GET['module'] == "cetakhasil_group") {
                include "modul/cetak_hasil_imltd_group.php";
            } elseif ($_GET['module'] == "tampilhasil_imltd") {
                include "modul/cetak_hasil_imltd_group_tampilhasil.php";
            } elseif ($_GET['module'] == "import_davinci") {
                include "modul/imltd_import_davinci.php";
            } elseif ($_GET['module'] == "import_davincihbsag") {
                include "modul/imltd_import_davinci_hbsag.php";
            } elseif ($_GET['module'] == "import_davincihcv") {
                include "modul/imltd_import_davinci_hcv.php";
            } elseif ($_GET['module'] == "import_davincihiv") {
                include "modul/imltd_import_davinci_hiv.php";
            } elseif ($_GET['module'] == "import_davincisyp") {
                include "modul/imltd_import_davinci_syp.php";
            } elseif ($_GET['module'] == "import_davincikonfirmasi") {
                include "modul/imltd_import_davinci_konfirm.php";
            } elseif ($_GET['module'] == "import_davincimanual") {
                include "modul/imltd_import_davinci_manual.php";
            } elseif ($_GET['module'] == "import_davincimanualinput") {
                include "modul/imltd_import_davinci_manual_input.php";
            } elseif ($_GET['module'] == "laporan") {
                include "laporan/filter_laporan.php";
            } elseif ($_GET['module'] == "lap_lttd1") {
                include "laporan/lttd1.php";
            } elseif ($_GET['module'] == "lap_lttd2") {
                include "laporan/lttd2.php";
            } elseif ($_GET['module'] == "lap_lttd3") {
                include "laporan/lttd3.php";
            } elseif ($_GET['module'] == "lap_lttd31") {
                include "laporan/lttd3_aftap.php";
            } elseif ($_GET['module'] == "lap_lttd32") {
                include "laporan/lttd3_baruulang.php";
            } elseif ($_GET['module'] == "lap_lttd33") {
                include "laporan/lttd3_aftap_baruulang.php";
            } elseif ($_GET['module'] == "lap_lttd4") {
                include "laporan/lttd4.php";
            } elseif ($_GET['module'] == "lap_lttd5") {
                include "laporan/lttd5.php";
            } elseif ($_GET['module'] == "lap_lttd6") {
                include "laporan/lttd6.php";
            } elseif ($_GET['module'] == "graphdonor") {
                include "laporan/graph_donor.php";
            } elseif ($_GET['module'] == "graphdonasi") {
                include "laporan/graph_penyumbangan.php";
            } elseif ($_GET['module'] == "graphtrendbulanan") {
                include "laporan/graph_bulanan.php";
            } elseif ($_GET['module'] == "graphtrendbulanan_dsdp") {
                include "laporan/graph_bulanan_dsdp.php";
            } elseif ($_GET['module'] == "graphtrendbulanan_kel") {
                include "laporan/graph_bulanan_kel.php";
            } elseif ($_GET['module'] == "graphtrendbulanan_lokasi") {
                include "laporan/graph_bulanan_lokasi.php";
            } elseif ($_GET['module'] == "graphtrendbulanan_lamabaru") {
                include "laporan/graph_bulanan_lamabaru.php";
            } elseif ($_GET['module'] == "graphtrendbulanan_golabo") {
                include "laporan/graph_bulanan_golabo.php";
            } elseif ($_GET['module'] == "graphtrendbulanan_rh") {
                include "laporan/graph_bulanan_rh.php";
            } elseif ($_GET['module'] == "penulusuran_pasien") {
                include "modul/check_data_pasien.php";
            } elseif ($_GET['module'] == "lacak_pasien") {
                include "modul/lacak_pasien.php";
            } elseif ($_GET['module'] == "search_pendonor") {
                include "modul/search_pendonor.php";
            } elseif ($_GET['module'] == "registrasi") {
                include "modul/registrasi.php";
            } elseif ($_GET['module'] == "cetak_id") {
                include "color.inc";
                include "modul/cetak_id.php";
            } elseif ($_GET['module'] == "sejarah") {
                include "modul/sejarah_pendonor.php";
            } elseif ($_GET['module'] == "list_sejarah") {
                include "modul/sejarah.php";
            } elseif ($_GET['module'] == "rekap_sejarah") {
                include "modul/sejarah_donor_xls.php";
            } elseif ($_GET['module'] == "rekap_transaksi_sum") {
                include "modul/rekap_transaksi_harian_summary.php";
            } elseif ($_GET['module'] == "form_konseling") {
                include "modul/transaksi_konseling.php";
            } elseif ($_GET['module'] == "history_konseling") {
                include "modul/sejarah_konseling.php";
            } elseif ($_GET['module'] == "history") {
                include "modul/sejarah_donor.php";
            }
    }

    echo "\n";
}
