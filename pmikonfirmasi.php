<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
    echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
        <center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";
}

$act = isset($_GET['act']) ? $_GET['act'] : '';
$rstock = isset($_GET['rstock']) ? $_GET['rstock'] : '';

if (($_SESSION['leveluser']) == 'konfirmasi') {
    switch ($act) {
        default:
            if ($rstock == '1')
                include "modul/stock.php";
            if ($rstock == '2')
                // include "modul/stock2.php";
                include "modul/cek_kantong_new.php";
            include "config/koneksi.php";
            include "config/dbi_connect.php";
            include "config/fungsi_combobox.php";
            include "config/library.php";
            if ($_GET['module'] == 'home') {
                echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
            } elseif ($_GET['module'] == 'rekap') {
                include "laborat_rekap.php";
            } elseif ($_GET['module'] == 'hasil_lab') {
                include "modul/hasil_lab.php";
            } elseif ($_GET['module'] == 'shasil_labl') {
                require_once('color.inc');
                include "modul/label_lab.php";
            } elseif ($_GET['module'] == 'shasil_lab') {
                include "modul/shasil_lab.php";
            } elseif ($_GET['module'] == 'aturuser') {
                include "modul/mod_user.php";
            } elseif ($_GET['module'] == 'laborat_konfirmasi') {
                include "laborat_konfirmasi.php";
            } elseif ($_GET['module'] == 'ganti_menu') {
                include "ganti_menu.php";
            } elseif ($_GET['module'] == 'ganti_passwd') {
                include "modul/ganti_passwd.php";
            }

            // elseif ($_GET['module']=='konfirmasi_gol_darah'){include  "modul/konfirmasi_gol_darah1.php";}
            // diganti 2025-02-08
            elseif ($_GET['module'] == 'konfirmasi_gol_darah') {
                include "abs/abd_manualinput.php";
            } elseif ($_GET['module'] == 'konfirmasi_laporan') {
                include "konfirmasi_laporan.php";
            } elseif ($_GET['module'] == 'admin_laporan') {
                include "admin_laporan.php";
            } elseif ($_GET['module'] == 'cetakhasil') {
                include "modul/cetak_hasil_imltd.php";
            } elseif ($_GET['module'] == 'cetakhasil_imltd') {
                include "modul/cetak_hasil_imltd_print.php";
            } elseif ($_GET['module'] == 'cetakhasil_group') {
                include "modul/cetak_hasil_imltd_group.php";
            }

            //Qwalys===========================================
            elseif ($_GET['module'] == 'qwalys') {
                include "qwalys/qwalys.php";
            } elseif ($_GET['module'] == 'konfirm_abs') {
                include "qwalys/qwalys_abs_to_konfirm.php";
            } elseif ($_GET['module'] == 'konfirm_abs1') {
                include "qwalys/qwalys_abs_konfirm.php";
            } elseif ($_GET['module'] == 'qwalys_view_confirm') {
                include "qwalys/qwalys_abs_konfirm_view.php";
            } elseif ($_GET['module'] == 'qwalys_view_confirmp') {
                include "qwalys/qwalys_abs_konfirm_viewprint.php";
            } elseif ($_GET['module'] == 'qwalys_process') {
                include "qwalys/qwalys_process.php";
            } elseif ($_GET['module'] == 'abs_data') {
                include "qwalys/qwalys_abs_data.php";
            } elseif ($_GET['module'] == 'abs_to_data') {
                include "qwalys/qwalys_abs_select_trans.php";
            } elseif ($_GET['module'] == 'konfirm_abd') {
                include "qwalys/qwalys_abd_to_konfirm.php";
            } elseif ($_GET['module'] == 'konfirm_abd1') {
                include "qwalys/qwalys_abd_konfirm.php";
            } elseif ($_GET['module'] == 'qwalys_view_confirm_abd') {
                include "qwalys/qwalys_abd_konfirm_view.php";
            } elseif ($_GET['module'] == 'qwalys_view_confirm_abdp') {
                include "qwalys/qwalys_abd_konfirm_viewprint.php";
            } elseif ($_GET['module'] == 'abd_to_data') {
                include "qwalys/qwalys_abd_select_trans.php";
            } elseif ($_GET['module'] == 'qwalys_srcid') {
                include "qwalys/qwalys_search_sample.php";
            }
            //Qwalys=========================================== 
            //IH1000===========================================
            elseif ($_GET['module'] == 'ih1000') {
                include "ih1000/qwalys.php";
            } elseif ($_GET['module'] == 'konfirm_ih1000') {
                include "ih1000/qwalys_abs_to_konfirm.php";
            } elseif ($_GET['module'] == 'konfirm_ih1000TPK') {
                include "ih1000/qwalys_abs_to_konfirmtpk.php";
            } elseif ($_GET['module'] == 'konfirm_abs1') {
                include "qwalys/qwalys_abs_konfirm.php";
            } elseif ($_GET['module'] == 'qwalys_view_confirm') {
                include "qwalys/qwalys_abs_konfirm_view.php";
            } elseif ($_GET['module'] == 'qwalys_process') {
                include "qwalys/qwalys_process.php";
            } elseif ($_GET['module'] == 'konfirm_abd') {
                include "qwalys/qwalys_abd_to_konfirm.php";
            } elseif ($_GET['module'] == 'abs_reag') {
                include "qwalys/qwalys_abs_reagen.php";
            } elseif ($_GET['module'] == 'abd_reag') {
                include "qwalys/qwalys_abd_reagen.php";
            } elseif ($_GET['module'] == 'qwalys_srcid') {
                include "qwalys/qwalys_search_sample.php";
            }

            //=========SERAH TERIMA=======
            elseif ($_GET['module'] == 'serahterima') {
                include "konfirmasi_serahterima.php";
            } elseif ($_GET['module'] == 'sr_aftap') {
                include "serahterima/sr_aftap_komponen.php";
            } elseif ($_GET['module'] == 'delrow') {
                include "serahterima/sr_proces.php";
            } elseif ($_GET['module'] == 'batal') {
                include "serahterima/sr_proces.php";
            } elseif ($_GET['module'] == 'sr_aftap_list') {
                include "serahterima/sr_aftap_komponen_list.php";
            } elseif ($_GET['module'] == 'sr_rpt_ktg') {
                include "serahterima/sr_aftap_komponen_print_ktg.php";
            } elseif ($_GET['module'] == 'sr_rpt_imltd') {
                include "serahterima/sr_aftap_komponen_print_imltd.php";
            } elseif ($_GET['module'] == 'sr_rpt_kgd') {
                include "serahterima/sr_aftap_komponen_print_kgd.php";
            } elseif ($_GET['module'] == 'sr_rpt_nat') {
                include "serahterima/sr_aftap_komponen_print_nat.php";
            } elseif ($_GET['module'] == 'sr_rpt_view') {
                include "serahterima/sr_aftap_komponen_view.php";
            } elseif ($_GET['module'] == 'sr_imltd') {
                include "serahterima/sr_imltd.php";
            } elseif ($_GET['module'] == 'sr_list') {
                include "serahterima/sr_list.php";
            } elseif ($_GET['module'] == 'sr_konfirmasi') {
                include "serahterima/sr_konfirmasi.php";
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
            //manual
            elseif ($_GET['module'] == 'manual_kgd') {
                include "dokumentasikonfirmasi.php";
            }
            //Rekapitulasi Pemeriksaan Konfirmasi Golongan Darah
            elseif ($_GET['module'] == 'rkp_kgd') {
                include "modul/rekap_kgd.php";
            }

            // elseif ($_GET['module']=='rekap_konfirmasi'){include "modul/rekap_konfirmasi.php";}
            // diganti ke model baru 2025-02-09
            elseif ($_GET['module'] == 'rekap_konfirmasi') {
                include "abs/abd_manualrekap.php";
            } elseif ($_GET['module'] == 'lot_kgd') {
                include "modul/konfirmasi_rincian_lot.php";
            }
            //KGD Sample utk Apheresis dan TPK==============================
            elseif ($_GET['module'] == 'kgd_sample') {
                include "tpk/kgd_sample.php";
            } elseif ($_GET['module'] == 'antibodycovid') {
                include "modul/abs_goldar.php";
            } elseif ($_GET['module'] == 'rkp_abs') {
                include "modul/rekap_abs.php";
            } elseif ($_GET['module'] == 'rekapcovid') {
                include "tpk/rekap_covid.php";
            } elseif ($_GET['module'] == 'hematologi') {
                include "tpk/hematologi_input.php";
            } elseif ($_GET['module'] == 'hematologir') {
                include "tpk/rekap_hematology.php";
            }

            // ABS Manual 01-02-2025
            elseif ($_GET['module'] == 'laborat_abs') {
                include "laborat_abs.php";
            } elseif ($_GET['module'] == 'abs_manual') {
                include "abs/abs_manualinput.php";
            } elseif ($_GET['module'] == 'abs_rekapmanual') {
                include "abs/abs_manualrekap.php";
            }
    }
}
