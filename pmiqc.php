<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
    echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";
}
if (($_SESSION['leveluser']) == 'qc') {
?>

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
            if ($_GET['rstock'] == '4')
                include "release/qa_check_kantong.php";
            include "config/koneksi.php";
            include "config/fungsi_combobox.php";
            include "config/library.php";

            if ($_GET['module'] == 'home') {
                echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
            } elseif ($_GET['module'] == 'rekap') {
                include "laborat_rekap.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar') {
                include "modul/rekap_darah_keluar.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_lama') {
                include "modul/rekap_darah_keluar_lama.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_bdrs') {
                include "modul/rekap_darah_keluar_bdrs.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_udd') {
                include "modul/rekap_darah_keluar_udd.php";
            } elseif ($_GET['module'] == 'musnah') {
                include "modul/musnah.php";
            } elseif ($_GET['module'] == 'rincian_darah_buang') {
                include "modul/rekap_darah_buang.php";
            } elseif ($_GET['module'] == 'rekap_darah_buang') {
                include "modul/rekap_pemusnahan_darah.php";
            } elseif ($_GET['module'] == 'keluar') {
                include "modul/keluar.php";
            } elseif ($_GET['module'] == 'rekap_darah_titip') {
                include "modul/rekap_darah_titip.php";
            } elseif ($_GET['module'] == 'rekap_transaksi') {
                include "modul/rekap_transaksi_harian.php";
            } elseif ($_GET['module'] == 'rekap_permintaan') {
                include "modul/rekap_permintaan_harian.php";
            } elseif ($_GET['module'] == 'rekap_permintaan_lama') {
                include "modul/rekap_permintaan_harian_lama.php";
            } elseif ($_GET['module'] == 'komponen') {
                include "modul/komponen.php";
            } elseif ($_GET['module'] == 'input_qc') {
                include "qc_input.php";
            } elseif ($_GET['module'] == 'qc_laporan') {
                include "qc_laporan.php";
            } elseif ($_GET['module'] == 'penambahan_kantong') {
                include "modul/penambahan_kantong.php";
            } elseif ($_GET['module'] == 'pengesahan_kantong') {
                include "modul/pengesahan_kantong.php";
            } elseif ($_GET['module'] == 'skantong0') {
                include "modul/skantong0.php";
            } elseif ($_GET['module'] == 'skantong') {
                include "modul/skantong.php";
            } elseif ($_GET['module'] == 'skantong1') {
                include "modul/skantong1.php";
            } elseif ($_GET['module'] == 'reagen') {
                include "modul/entry_reagen.php";
            } elseif ($_GET['module'] == 'dreagen') {
                include "modul/daftar_pegawai.php";
            } elseif ($_GET['module'] == 'supplier') {
                include "modul/entry_suplier.php";
            } elseif ($_GET['module'] == 'permintaan') {
                include "modul/mod_permintaan.php";
            } elseif ($_GET['module'] == 'hasil_lab') {
                include "modul/hasil_lab.php";
            } elseif ($_GET['module'] == 'shasil_labl') {
                require_once('color.inc');
                include "modul/label_lab.php";
            } elseif ($_GET['module'] == 'shasil_lab') {
                include "modul/shasil_lab.php";
            } elseif ($_GET['module'] == 'elisa') {
                include "modul/elisa.php";
                //include "konfirmasi.php";
            } elseif ($_GET['module'] == 'elisabayang') {
                include "modul/elisabayang.php";
                //include "konfirmasi.php";

            } elseif ($_GET['module'] == 'update_elisa') {
                include "modul/update_elisa.php";
                //include "konfirmasi.php";
            } elseif ($_GET['module'] == 'update_rapidtest') {
                include "modul/update_rapidtest.php";
                //include "konfirmasi.php";
            } elseif ($_GET['module'] == 'hlab_bonus') {
                include "modul/lab_bonus.php";
            } elseif ($_GET['module'] == 'pengesahan') {
                include "modul/pengesahan_rapidtest.php";
            }

            //elseif ($_GET['module']=='scrossmatch'){
            //                 include "modul/search_crossmatch.php";
            //}
            elseif ($_GET['module'] == 'label_cross') {
                require_once('color.inc');
                include "modul/label_cross.php";
            } elseif ($_GET['module'] == 'reagen_nonaktif') {
                include "modul/reagen_nonaktif.php";
            } elseif ($_GET['module'] == 'reagen_aktif') {
                include "modul/reagen_aktif.php";
            } elseif ($_GET['module'] == 'sarancrossmatch') {
                include "modul/mod_sarancross.php";
            } elseif ($_GET['module'] == 'crossmatch') {
                include "modul/mod_crossmatch.php";
            } elseif ($_GET['module'] == 'user') {
                include "modul/registrasi.php";
            } elseif ($_GET['module'] == 'cetak_id') {
                include "modul/cetak_id.php";
            } elseif ($_GET['module'] == 'transaksi') {
                include "modul/search_pendonor.php";
            } elseif ($_GET['module'] == 'eregistrasi') {
                include "modul/edit_registrasi.php";
            } elseif ($_GET['module'] == 'transaksi_donor') {
                include "transaksi_donor.php";
            } elseif ($_GET['module'] == 'spengambilan') {
                include "modul/search_transaksi.php";
            } elseif ($_GET['module'] == 'pengambilan') {
                include "modul/pengambilan_darah.php";
            } elseif ($_GET['module'] == 'pengesahan_pengambilan') {
                include "modul/pengesahan_ambil_darah.php";
            } elseif ($_GET['module'] == 'aturuser') {
                include "modul/mod_user.php";
            } elseif ($_GET['module'] == 'aturagenda') {
                include "modul/mod_agendamn.php";
            } elseif ($_GET['module'] == 'agendaedit') {
                include "modul/mod_agendamn1.php";
            } elseif ($_GET['module'] == 'updateagenda') {
                include "modul/mod_updateagenda.php";
            } elseif ($_GET['module'] == 'tambahagenda') {
                include "modul/mod_agendamn2.php";
            } elseif ($_GET['module'] == 'entryagenda') {
                include "modul/mod_insertagenda.php";
            } elseif ($_GET['module'] == 'agendahapus') {
                include "modul/mod_hapusagenda.php";
            } elseif ($_GET['module'] == 'agendalist') {
                include "modul/mod_agendalist.php";
            } elseif ($_GET['module'] == 'smsgroup') {
                include "modul/sms2/sms2.php";
            } elseif ($_GET['module'] == 'smsidi') {
                include "modul/sms2/sms2.php";
            } elseif ($_GET['module'] == 'rtransaksi') {
                include "modul/rtransaksi.php";
            } elseif ($_GET['module'] == 'stock') {
                include "modul/stock.php";
            } elseif ($_GET['module'] == 'chek_imltd') {
                include "modul/chek_imltd.php";
            } elseif ($_GET['module'] == 'pindah_titipan') {
                include "modul/pindah_titipan.php";
            } elseif ($_GET['module'] == 'laborat_konfirmasi') {
                include "laborat_konfirmasi.php";
            } elseif ($_GET['module'] == 'qa_komponen') {
                include "qa_komponen.php";
            } elseif ($_GET['module'] == 'laborat_distribusi') {
                include "laborat_distribusi.php";
            } elseif ($_GET['module'] == 'laborat_ujisaring') {
                include "laborat_ujisaring.php";
            } elseif ($_GET['module'] == 'komponen_permintaan') {
                include "komponen_permintaan.php";
            } elseif ($_GET['module'] == 'laborat_cetak') {
                include "laborat_cetak.php";
            } elseif ($_GET['module'] == 'laborat_update') {
                include "laborat_update.php";
            } elseif ($_GET['module'] == 'ganti_menu') {
                include "ganti_menu.php";
            } elseif ($_GET['module'] == 'form_minta') {
                include "modul/form_minta.php";
            } elseif ($_GET['module'] == 'update_dari_rujukan') {
                include "modul/decode.php";
            } elseif ($_GET['module'] == 'update_dari_udd') {
                include "modul/decode.php";
            } elseif ($_GET['module'] == 'update_dari_bdrs') {
                //include  "modul/update_dari_bdrs.php";
                include "modul/decode.php";
            } elseif ($_GET['module'] == 'kembali') {
                include "modul/mod_kembali.php";
            } elseif ($_GET['module'] == 'form_rujukan') {
                include "modul/form_rujukan.php";
            } elseif ($_GET['module'] == 'form_udd') {
                include "modul/form_udd.php";
            } elseif ($_GET['module'] == 'form_bdrs') {
                include "modul/form_bdrs.php";
            } elseif ($_GET['module'] == 'form_bdrsxls') {
                include "modul/form_bdrsxls.php";
            } elseif ($_GET['module'] == 'ganti_passwd') {
                include "modul/ganti_passwd.php";
            } elseif ($_GET['module'] == 'konfirmasi_gol_darah') {
                include "modul/konfirmasi_gol_darah1.php";
            } elseif ($_GET['module'] == 'rekap_reaktif') {
                require_once('color.inc');
                include "modul/rekap_reaktif.php";
            } elseif ($_GET['module'] == 'rekap_nonreaktif') {
                require_once('color.inc');
                include "modul/rekap_nonreaktif.php";
            } elseif ($_GET['module'] == 'rekap_reaktifrapid') {
                require_once('color.inc');
                include "modul/rekap_reaktifrapid.php";
            } elseif ($_GET['module'] == 'rekap_reaktif1') {
                require_once('color.inc');
                include "modul/rekap_reaktif1.php";
            } elseif ($_GET['module'] == 'rekap_reaktif1_xls') {
                require_once('color.inc');
                include "modul/rekap_reaktif1_xls.php";
            } elseif ($_GET['module'] == 'rincian_komponen') {
                include "modul/rekap_pembuatan_komponen1.php";
            } elseif ($_GET['module'] == 'rekap_komponen') {
                include "modul/rekap_pembuatan_komponen.php";
            } elseif ($_GET['module'] == 'rekap_konfirmasi') {
                include "modul/rekap_konfirmasi.php";
            } elseif ($_GET['module'] == 'terima_dari_utd_lain') {
                require_once('color.inc');
                include "modul/terima_dari_utd_lain.php";
            } elseif ($_GET['module'] == 'rekap_terima_darah_udd_lain') {
                require_once('color.inc');
                include "modul/rekap_terima_darah_udd_lain.php";
            } elseif ($_GET['module'] == 'laporan_darah_buang') {
                include "modul/rekap_darah_buang.php";
            } elseif ($_GET['module'] == 'updatekantong') {
                include "modul/update_sah_kantong.php";
            } elseif ($_GET['module'] == 'rincian_minta_barang') {
                include "logistik/rincian_transaksi_minta_barang.php";
            } elseif ($_GET['module'] == 'set_stok_sos') {
                include "modul/setting_stok_emergency.php";
            } elseif ($_GET['module'] == 'rekap_cross') {
                include "modul/rekap_hasil_crossmatch.php";
            } elseif ($_GET['module'] == 'kirim_rujukan') {
                require_once('color.inc');
                include "modul/kirim_rujukan.php";
            } elseif ($_GET['module'] == 'rekap_rujukan') {
                include "modul/rekap_rujukan.php";
            }

            #lttd level laborat
            elseif ($_GET['module'] == 'laborat_laporan') {
                include "laborat_laporan.php";
            } elseif ($_GET['module'] == 'admin_laporan') {
                include "admin_laporan.php";
            } elseif ($_GET['module'] == 'admin_utility') {
                include "admin_utility.php";
            } elseif ($_GET['module'] == 'laporan_kegiatan') {
                include "modul/lap_kegiatan.php";
            } elseif ($_GET['module'] == 'laporan_peng_darah') {
                include "modul/lap_peng_darah.php";
            } elseif ($_GET['module'] == 'laporan_uji_sharing') {
                include "modul/lap_uji_sharing.php";
            } elseif ($_GET['module'] == 'aktif_udd') {
                include "modul/aktif_udd.php";
            } elseif ($_GET['module'] == 'laporan_buang_darah') {
                include "modul/lap_buang_darah.php";
            } elseif ($_GET['module'] == 'laporan_lttd4') {
                include "modul/laporan_lttd4.php";
            } elseif ($_GET['module'] == 'lttd4') {
                include "lttd4_$tgll.html";
            } elseif ($_GET['module'] == 'laporan_lttd5') {
                include "modul/laporan_lttd5.php";
            } elseif ($_GET['module'] == 'lttd5') {
                include "lttd5_$tgll.html";
            } elseif ($_GET['module'] == 'laporan_lttd6') {
                include "modul/laporan_lttd6.php";
            } elseif ($_GET['module'] == 'lttd6') {
                include "lttd6_$tgll.html";
            } elseif ($_GET['module'] == 'check_hasil_imltd') {
                include "modul/check_imltd_kantong.php";
            } elseif ($_GET['module'] == 'rekap_cross') {
                include "modul/rekap_hasil_crossmatch.php";
            } elseif ($_GET['module'] == 'kirim_rujukan') {
                require_once('color.inc');
                include "modul/kirim_rujukan.php";
            } elseif ($_GET['module'] == 'rekap_rujukan') {
                include "modul/rekap_rujukan.php";
            }
            //update test reagen
            elseif ($_GET['module'] == 'update_test_reagen') {
                include "modul/update_jml_reagen.php";
            }
            //IMPORT DIASORIN ETI-MAX 3000====================================
            elseif ($_GET['module'] == 'import') {
                include "laborat_imltd_import.php";
            } elseif ($_GET['module'] == 'import_etimax3000') {
                include "modul/imltd_import_etimax3000.php";
            } elseif ($_GET['module'] == 'import_etimax3000hbsag') {
                include "modul/imltd_import_etimax3000_hbsag.php";
            } elseif ($_GET['module'] == 'import_etimax3000hcv') {
                include "modul/imltd_import_etimax3000_hcv.php";
            } elseif ($_GET['module'] == 'import_etimax3000hiv') {
                include "modul/imltd_import_etimax3000_hiv.php";
            } elseif ($_GET['module'] == 'import_etimax3000syp') {
                include "modul/imltd_import_etimax3000_syp.php";
            } elseif ($_GET['module'] == 'import_etimax3000syp') {
                include "modul/imltd_import_etimax3000_syp.php";
            } elseif ($_GET['module'] == 'import_etimax3000konfirmasi') {
                include "modul/imltd_import_etimax3000_konfirm.php";
            } elseif ($_GET['module'] == 'import_etimax3000manual') {
                include "modul/imltd_import_etimax3000_manual.php";
            } elseif ($_GET['module'] == 'import_etimax3000manualinput') {
                include "modul/imltd_import_etimax3000_manual_input.php";
            }
            //END OF IMPORT DIASORIN ETI-MAX 3000=============================
            elseif ($_GET['module'] == 'cetakhasil') {
                include "modul/cetak_hasil_imltd.php";
            } elseif ($_GET['module'] == 'cetakhasil_imltd') {
                include "modul/cetak_hasil_imltd_print.php";
            } elseif ($_GET['module'] == 'cetakhasil_group') {
                include "modul/cetak_hasil_imltd_group.php";
            } elseif ($_GET['module'] == 'tampilhasil_imltd') {
                include "modul/cetak_hasil_imltd_group_tampilhasil.php";
            }

            //IMPORT BIOMERIEUX DAVINCI QUATRO===============================
            elseif ($_GET['module'] == 'import_davinci') {
                include "modul/imltd_import_davinci.php";
            } elseif ($_GET['module'] == 'import_davincihbsag') {
                include "modul/imltd_import_davinci_hbsag.php";
            } elseif ($_GET['module'] == 'import_davincihcv') {
                include "modul/imltd_import_davinci_hcv.php";
            } elseif ($_GET['module'] == 'import_davincihiv') {
                include "modul/imltd_import_davinci_hiv.php";
            } elseif ($_GET['module'] == 'import_davincisyp') {
                include "modul/imltd_import_davinci_syp.php";
            } elseif ($_GET['module'] == 'import_davincikonfirmasi') {
                include "modul/imltd_import_davinci_konfirm.php";
            } elseif ($_GET['module'] == 'import_davincimanual') {
                include "modul/imltd_import_davinci_manual.php";
            } elseif ($_GET['module'] == 'import_davincimanualinput') {
                include "modul/imltd_import_davinci_manual_input.php";
            }

            //===============================================================


            //LAPORAN LTTD & Grafik by TIM SC
            elseif ($_GET['module'] == 'laporan') {
                include "laporan/filter_laporan.php";
            } elseif ($_GET['module'] == 'lap_lttd1') {
                include "laporan/lttd1.php";
            } elseif ($_GET['module'] == 'lap_lttd2') {
                include "laporan/lttd2.php";
            }
            //LAPORAN LTTD & Grafik by TIM SC
            elseif ($_GET['module'] == 'laporan') {
                include "laporan/filter_laporan.php";
            } elseif ($_GET['module'] == 'lap_lttd1') {
                include "laporan/lttd1.php";
            } elseif ($_GET['module'] == 'lap_lttd2') {
                include "laporan/lttd2.php";
            }

            //LAPORAN LTTTD DS & DP dari tanggal Aftap
            elseif ($_GET['module'] == 'lap_lttd3') {
                include "laporan/lttd3.php";
            }

            //LAPORAN LTTTD DS & DP dari tanggal pengmbilan 
            elseif ($_GET['module'] == 'lap_lttd31') {
                include "laporan/lttd3_aftap.php";
            }

            //LAPORAN LTTTD BARU & ULANG dari tanggal Aftap
            elseif ($_GET['module'] == 'lap_lttd32') {
                include "laporan/lttd3_baruulang.php";
            }

            //LAPORAN LTTTD BARU & ULANG dari tanggal pengmbilan
            elseif ($_GET['module'] == 'lap_lttd33') {
                include "laporan/lttd3_aftap_baruulang.php";
            } elseif ($_GET['module'] == 'lap_lttd4') {
                include "laporan/lttd4.php";
            } elseif ($_GET['module'] == 'lap_lttd5') {
                include "laporan/lttd5.php";
            } elseif ($_GET['module'] == 'lap_lttd6') {
                include "laporan/lttd6.php";
            } elseif ($_GET['module'] == 'graphdonor') {
                include "laporan/graph_donor.php";
            } elseif ($_GET['module'] == 'graphdonasi') {
                include "laporan/graph_penyumbangan.php";
            } elseif ($_GET['module'] == 'graphtrendbulanan') {
                include "laporan/graph_bulanan.php";
            } elseif ($_GET['module'] == 'graphtrendbulanan_dsdp') {
                include "laporan/graph_bulanan_dsdp.php";
            } elseif ($_GET['module'] == 'graphtrendbulanan_kel') {
                include "laporan/graph_bulanan_kel.php";
            } elseif ($_GET['module'] == 'graphtrendbulanan_lokasi') {
                include "laporan/graph_bulanan_lokasi.php";
            } elseif ($_GET['module'] == 'graphtrendbulanan_lamabaru') {
                include "laporan/graph_bulanan_lamabaru.php";
            } elseif ($_GET['module'] == 'graphtrendbulanan_golabo') {
                include "laporan/graph_bulanan_golabo.php";
            } elseif ($_GET['module'] == 'graphtrendbulanan_rh') {
                include "laporan/graph_bulanan_rh.php";
            }
            //==================================
            elseif ($_GET['module'] == 'penulusuran_pasien') {
                include "modul/check_data_pasien.php";
            } elseif ($_GET['module'] == 'lacak_pasien') {
                include "modul/lacak_pasien.php";
            }

            /*akses pendonor level laborat*/ elseif ($_GET['module'] == 'search_pendonor') {
                include "modul/search_pendonor.php";
            } elseif ($_GET['module'] == 'eregistrasi') {
                include "modul/edit_registrasi.php";
            } elseif ($_GET['module'] == 'transaksi_donor') {
                include "modul/transaksi_donor.php";
            } elseif ($_GET['module'] == 'registrasi') {
                include "modul/registrasi.php";
            } elseif ($_GET['module'] == 'cetak_id') {
                include "color.inc";
                include "modul/cetak_id.php";
            } elseif ($_GET['module'] == 'sejarah') {
                include "modul/sejarah_pendonor.php";
            } elseif ($_GET['module'] == 'list_sejarah') {
                include "modul/sejarah.php";
            } elseif ($_GET['module'] == 'rekap_sejarah') {
                include "modul/sejarah_donor_xls.php";
            } elseif ($_GET['module'] == 'rekap_transaksi_sum') {
                include "modul/rekap_transaksi_harian_summary.php";
            }

            //menu QC
            elseif ($_GET['module'] == 'qcwb') {
                include "modul/qcwb.php";
            } elseif ($_GET['module'] == 'qcprc') {
                include "modul/qcprc.php";
            } elseif ($_GET['module'] == 'qcffp') {
                include "modul/qcffp.php";
            } elseif ($_GET['module'] == 'qctc') {
                include "modul/qctc.php";
            } elseif ($_GET['module'] == 'laborat_qc') {
                include "modul/laborat_qc.php";
            }
            //logbook=====================================
            elseif ($_GET['module'] == 'qc_logbook') {
                include "qc_logbook.php";
            } elseif ($_GET['module'] == 'entry_logbook') {
                include "color.inc";
                include "modul/entry_logbook.php";
            } elseif ($_GET['module'] == 'rekap_logbook') {
                include "color.inc";
                include "modul/rekap_logbook.php";
            } elseif ($_GET['module'] == 'entry_aksilogbook') {
                include "color.inc";
                include "modul/entry_aksilogbook.php";
            } elseif ($_GET['module'] == 'history_logbook') {
                include "color.inc";
                include "modul/history_logbook.php";
            } elseif ($_GET['module'] == 'rincian_logbook') {
                include "color.inc";
                include "modul/rincian_logbook.php";
            } elseif ($_GET['module'] == 'new_logbook') {
                include "color.inc";
                include "modul/logbook_inputqr.php";
            }

            //NEW MODUL RELEASE BUILD 01-10-2019============
            elseif ($_GET['module'] == 'qcwb_proses') {
                include "QC/qcwb_proses.php";
            } elseif ($_GET['module'] == 'cetak_rekap') {
                include "QC/reportqc-wb_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap_all') {
                include "QC/reportqc-wball_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-prc') {
                include "QC/reportqc-prc_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-prcall') {
                include "QC/reportqc-prc-all_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-leucoreduction') {
                include "QC/reportqc-leucoreduction_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-leucoreduction-all') {
                include "QC/reportqc-leucoreduction-all_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-leucodepleted') {
                include "QC/reportqc-leucodepleted_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-leucodepleted-all') {
                include "QC/reportqc-leucodepleted-all_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-tc') {
                include "QC/reportqc-tc_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-ffp') {
                include "QC/reportqc-ffp_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-ffp-all') {
                include "QC/reportqc-ffp-all_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-lpaferesis') {
                include "QC/reportqc-lpaferesis_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-lpaferesis-all') {
                include "QC/reportqc-lpaferesis-all_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-ahf') {
                include "QC/reportqc-ahf_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-ahf-all') {
                include "QC/reportqc-ahf-all_xls.php";
            } elseif ($_GET['module'] == 'hasilqc-wb') {
                include "QC/reportqc.php";
            } elseif ($_GET['module'] == 'hasilqc-prc') {
                include "QC/reportprc.php";
            } elseif ($_GET['module'] == 'hasilqc-prc2') {
                include "QC/reportprc_leucoreduction.php";
            } elseif ($_GET['module'] == 'hasilqc-prc3') {
                include "QC/reportprc_leucodepleted.php";
            } elseif ($_GET['module'] == 'hasilqc-tc') {
                include "QC/reporttc.php";
            } elseif ($_GET['module'] == 'cetak_rekap-tc') {
                include "QC/reportqc-tc_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-tc-all') {
                include "QC/reportqc-tcall_xls.php";
            } elseif ($_GET['module'] == 'hasilqc-tcaf') {
                include "QC/reporttc-af.php";
            } elseif ($_GET['module'] == 'cetak_rekap-tcaf') {
                include "QC/reportqc-tcaf_xls.php";
            } elseif ($_GET['module'] == 'cetak_rekap-tcafall') {
                include "QC/reportqc-tcaf-all_xls.php";
            } elseif ($_GET['module'] == 'hasilqc-ffp') {
                include "QC/report-ffp.php";
            } elseif ($_GET['module'] == 'hasilqc-lpaferesis') {
                include "QC/report-lpaferesis.php";
            } elseif ($_GET['module'] == 'hasilqc-ahf') {
                include "QC/report-ahf.php";
            } elseif ($_GET['module'] == 'hasilqc-ffp_lama') {
                include "QC/reportffp.php";
            } elseif ($_GET['module'] == 'qcprc_proses') {
                include "QC/qcprc_proses.php";
            } elseif ($_GET['module'] == 'leucoreduction_proses') {
                include "QC/leucoreduction_proses.php";
            } elseif ($_GET['module'] == 'qctc_proses') {
                include "QC/qctc_proses.php";
            } elseif ($_GET['module'] == 'qctc_prosesafe') {
                include "QC/qctc_prosesafe.php";
            } elseif ($_GET['module'] == 'qctc_prosestrom') {
                include "QC/qctc_prosestrom.php";
            } elseif ($_GET['module'] == 'qcffp_proses') {
                include "QC/qcffp_proses.php";
            } elseif ($_GET['module'] == 'qcffp_prosesahf') {
                include "QC/qcffp_prosesahf.php";
            } elseif ($_GET['module'] == 'qcffp_prosespa') {
                include "QC/qcffp_prosespa.php";
            } elseif ($_GET['module'] == 'qcwb2') {
                include "QC/qcwb_inputkantong.php";
            } elseif ($_GET['module'] == 'menu_lapprc') {
                include "QC/lapprc_menu.php";
            } elseif ($_GET['module'] == 'menu_laptc') {
                include "QC/laptc_menu.php";
            } elseif ($_GET['module'] == 'menu_lapffp') {
                include "QC/lapffp_menu.php";
            } elseif ($_GET['module'] == 'menu_prc') {
                include "QC/qcprc_menu.php";
            } elseif ($_GET['module'] == 'menu_tc') {
                include "QC/qctc_menu.php";
            } elseif ($_GET['module'] == 'menu_ffp') {
                include "QC/qcffp_menu.php";
            } elseif ($_GET['module'] == 'qcprc2') {
                include "QC/qcprc_inputkantong.php";
            } elseif ($_GET['module'] == 'qcprc3') {
                include "QC/qcprc_leucoreduction.php";
            } elseif ($_GET['module'] == 'qcprc4') {
                include "QC/qcprc_leucodepleted.php";
            } elseif ($_GET['module'] == 'qctc2') {
                include "QC/qctc_inputkantong.php";
            } elseif ($_GET['module'] == 'qctc3') {
                include "QC/qctc_aferesis.php";
            } elseif ($_GET['module'] == 'qctc4') {
                include "QC/qctc_tromboferesis.php";
            } elseif ($_GET['module'] == 'qcffp2') {
                include "QC/qcffp_inputkantong.php";
            } elseif ($_GET['module'] == 'qcffp3') {
                include "QC/qcffp_ahf.php";
            } elseif ($_GET['module'] == 'qcffp4') {
                include "QC/qcffp_plasma.php";
            } elseif ($_GET['module'] == 'historydonasi') {
                include "modul/history_don.php";
            } elseif ($_GET['module'] == 'register_qc') {
                include "QC/register_qc.php";
            } elseif ($_GET['module'] == 'register_qc_luar') {
                require_once('color.inc');
                include "QC/register_qc_luar.php";
            } elseif ($_GET['module'] == 'rekap_register') {
                include "QC/rek_reg_qc.php";
            } elseif ($_GET['module'] == 'master_kantong') {
                include "QC/qc_list_berat_kantong_kosong.php";
            } elseif ($_GET['module'] == 'kantong') {
                include "QC/qc_add_kantong.php";
            } elseif ($_GET['module'] == 'edit_beratkantong') {
                include "QC/qc_edit_kantong.php";
            } elseif ($_GET['module'] == 'density1') {
                include "QC/qc_beratjenis.php";
            } elseif ($_GET['module'] == 'qc_produk') {
                include "QC/qc_input_produk.php";
            }
            //==============================================

    }
}
?>