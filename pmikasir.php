<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
    echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";
}
if ($_SESSION['leveluser'] == 'kasir' or $_SESSION['leveluser'] == 'bdrs') { ?>
    <!doctype html>
    <html>
    <title>SIMDONDAR</title>

    <head>
        <script language=javascript src="idcard.js" type="text/javascript"> </script>
        <script language=javascript src="util.js" type="text/javascript"> </script>
        <link href="css/style.css" rel="stylesheet" type="text/css" />
    </head>
    <?php
    //require_once('color.inc');
    switch (@$_GET['act']) {
        default:
            if (@$_GET['rstock'] == '1')
                include "modul/stock.php";
            if (@$_GET['rstock'] == '3')
                // include "modul/stock1.php";
                include "modul/cek_kantong_new.php";
            include "config/koneksi.php";
            include "config/fungsi_combobox.php";
            include "config/library.php";

            if ($_GET['module'] == 'home') {
                echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
            } elseif ($_GET['module'] == 'rekap') {
                include "receptionis_rekap.php";
            } elseif ($_GET['module'] == 'rekap_transaksi') {
                include "modul/rekap_transaksi_harian.php";
            } elseif ($_GET['module'] == 'rekap_transaksi1') {
                include "modul/rekap_transaksi_donor.php";
            } elseif ($_GET['module'] == 'rekap_transaksi2') {
                include "modul/rekap_transaksi_donor1.php";
            } elseif ($_GET['module'] == 'cetak_minta') {
                include('color.inc');
                include "modul/mod_cetak_minta.php";
            } elseif ($_GET['module'] == 'permintaan') {
                include "modul/mod_permintaan.php";
            } elseif ($_GET['module'] == 'tambah_permintaan') {
                include "modul/mod_tambah_permintaan.php";
            } elseif ($_GET['module'] == 'detailpermintaan') {
                include "modul/permintaandetail.php";
            } elseif ($_GET['module'] == 'editpermintaan') {
                include "modul/editpermintaan.php";
            } elseif ($_GET['module'] == 'editlayanan') {
                include "modul/editpembayaran.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar') {
                include "modul/rekap_darah_keluar.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_lama') {
                include "modul/rekap_darah_keluar_lama.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_bdrs') {
                include "modul/rekap_darah_keluar_bdrs.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_udd') {
                include "modul/rekap_darah_keluar_udd.php";
            } elseif ($_GET['module'] == 'rekap_permintaan') {
                include "modul/rekap_permintaan_harian.php";
            } elseif ($_GET['module'] == 'rekap_transaksi') {
                include "modul/rekap_transaksi_harian.php";
            } elseif ($_GET['module'] == 'piagam') {
                include "modul/piagam.php";
            } elseif ($_GET['module'] == 'registrasi') {
                include "modul/registrasi.php";
            } elseif ($_GET['module'] == 'cetak_id') {
                include "color.inc";
                include "modul/cetak_id.php";
            } elseif ($_GET['module'] == 'search_pendonor_calling') {
                include "modul/search_pendonor_calling.php";
            } elseif ($_GET['module'] == 'search_pendonor') {
                include "modul/search_pendonor.php";
            } elseif ($_GET['module'] == 'double_pendonor') {
                include "modul/double_pendonor.php";
            } elseif ($_GET['module'] == 'eregistrasi') {
                include "modul/edit_registrasi.php";
            } elseif ($_GET['module'] == 'epasien') {
                include "modul/editpasien.php";
            } elseif ($_GET['module'] == 'addpermintaan') {
                include "modul/addpermintaan.php";
            } elseif ($_GET['module'] == 'transaksi_donor') {
                include "modul/transaksi_donor.php";
            } elseif ($_GET['module'] == 'transaksi_donor_edit') {
                include "modul/transaksi_donor_edit.php";
            } elseif ($_GET['module'] == 'nota1') {
                include "nota1.php";
            } elseif ($_GET['module'] == 'bayar') {
                include "color.inc";
                include "modul/mod_cetak_pembayaran.php";
            } elseif ($_GET['module'] == 'pembayaran') {
                include "color.inc";
                include "modul/mod_pembayaran.php";
            } elseif ($_GET['module'] == 'kembali') {
                include "modul/mod_kembali.php";
            } elseif ($_GET['module'] == 'stock') {
                include "modul/stock.php";
            } elseif ($_GET['module'] == 'tambah_dokter') {
                include "modul/tambah_dokter.php";
            } elseif ($_GET['module'] == 'tambah_dokter_periksa') {
                include "modul/tambah_dokter_periksa.php";
            } elseif ($_GET['module'] == 'tambah_bagian') {
                include "modul/tambah_bagian.php";
            } elseif ($_GET['module'] == 'tambah_kelas') {
                include "modul/tambah_kelas.php";
            } elseif ($_GET['module'] == 'tambah_layanan') {
                include "modul/tambah_jenislayanan.php";
            } elseif ($_GET['module'] == 'tambah_rs') {
                include "modul/tambah_rmhsakit.php";
            } elseif ($_GET['module'] == 'tambah_bdrs') {
                include "modul/tambah_bdrs_kasir.php";
            } elseif ($_GET['module'] == 'receptionis_pendonor') {
                include "receptionis_pendonor.php";
            } elseif ($_GET['module'] == 'receptionis_transaksi') {
                include "receptionis_transaksi.php";
            } elseif ($_GET['module'] == 'receptionis_cetak') {
                include "receptionis_cetak.php";
            } elseif ($_GET['module'] == 'ganti_menu') {
                include "ganti_menu.php";
            } elseif ($_GET['module'] == 'ganti_passwd') {
                include "modul/ganti_passwd.php";
            } elseif ($_GET['module'] == 'ganti_shift') {
                include "modul/ganti_shift.php";
            } elseif ($_GET['module'] == 'sejarah') {
                include "modul/sejarah_pendonor.php";
            } elseif ($_GET['module'] == 'list_sejarah') {
                include "modul/sejarah.php";
            } elseif ($_GET['module'] == 'upload_foto') {
                include "modul/upload_foto.php";
            } elseif ($_GET['module'] == 'edit_harga') {
                include "modul/edit_harga.php";
            } elseif ($_GET['module'] == 'pasien_plebotomi') {
                include "modul/search_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'registrasi_pasien_plebotomi') {
                include "modul/registrasi_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'eregistrasi_pasien_plebotomi') {
                include "modul/edit_registrasi_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'transaksi_plebotomi') {
                include "modul/transaksi_plebotomi.php";
            } elseif ($_GET['module'] == 'sejarah_pasien_plebotomi') {
                include "modul/sejarah_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'laporan_pasien_plebotomi') {
                include "modul/laporan_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'piagam') {
                include "modul/piagam.php";
            } elseif ($_GET['module'] == 'ajukan_piagam') {
                include "modul/ajukan_piagam.php";
            } elseif ($_GET['module'] == 'edit_piagam') {
                include "modul/edit_piagam.php";
            } elseif ($_GET['module'] == 'edit_piagam1') {
                include "modul/edit_piagam1.php";
            } elseif ($_GET['module'] == 'laporan_piagam') {
                include "modul/laporan_piagam.php";
            } elseif ($_GET['module'] == 'pengajuan_piagam') {
                include "modul/rekap_pengajuan_piagam.php";
            } elseif ($_GET['module'] == 'rekap_pembayaran') {
                include "modul/rekap_pembayaran.php";
            } elseif ($_GET['module'] == 'updatekantong') {
                include "modul/update_sah_kantong.php";
            } elseif ($_GET['module'] == 'rincian_minta_barang') {
                include "logistik/rincian_transaksi_minta_barang.php";
            }

            //aftap level kasir
            elseif ($_GET['module'] == 'aftap1') {
                include "aftap2.php";
            } elseif ($_GET['module'] == 'check') {
                include "modul/search_med_check.php";
            } elseif ($_GET['module'] == 'spengambilan') {
                include "modul/search_transaksi.php";
            } elseif ($_GET['module'] == 'gantikantong') {
                include "modul/search_transaksi_gk.php";
            } elseif ($_GET['module'] == 'checkup') {
                include "modul/medical_checkup.php";
            } elseif ($_GET['module'] == 'pengambilan') {
                include "modul/pengambilan_darah23.php";
            } elseif ($_GET['module'] == 'pengambilan_plebotomi') {
                include "modul/pengambilan_darah_plebotomi.php";
            } elseif ($_GET['module'] == 'laporan_pasien_plebotomi') {
                include "modul/laporan_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'daftar_permintaan_plebotomi') {
                include "modul/daftar_permintaan_plebotomi_1.php";
            } elseif ($_GET['module'] == 'rekap_sejarah') {
                include "modul/sejarah_donor_xls.php";
            } elseif ($_GET['module'] == 'deltransaksi') {
                include "modul/del_transaksi.php";
            } elseif ($_GET['module'] == 'delmedical') {
                include "modul/del_med_check.php";
            } elseif ($_GET['module'] == 'rekap_pembayaran1') {
                include "modul/rekap_pembayaran_new.php";
            } elseif ($_GET['module'] == 'rekap_piagam') {
                include "modul/rekap_piagam.php";
            } elseif ($_GET['module'] == 'laporan_cetak_kartu') {
                include "modul/rekap_cetak_kartu.php";
            } elseif ($_GET['module'] == 'historycetak') {
                include "modul/historycetak.php";
            } elseif ($_GET['module'] == 'pengambilan_apheresis') {
                include "modul/pengambilan_darahaph23.php";
            } elseif ($_GET['module'] == 'rekap_donor') {
                include "modul/rekap_donor.php";
            } elseif ($_GET['module'] == 'tambah_kecamatan') {
                include "modul/tambah_kecamatan.php";
            } elseif ($_GET['module'] == 'tambah_kelurahan') {
                include "modul/tambah_kelurahan.php";
            } elseif ($_GET['module'] == 'rekap_permintaan_lama') {
                include "modul/rekap_permintaan_harian_lama.php";
            } elseif ($_GET['module'] == 'searchpasien') {
                include "modul/search_pasien.php";
            } elseif ($_GET['module'] == 'epasien') {
                include "modul/editpasien.php";
            } elseif ($_GET['module'] == 'edit_datapasien') {
                include "modul/edit_permintaan.php";

            }
            //cetak formulir
            elseif ($_GET['module'] == 'form_donor') {
                include "modul/data_pendonor2.php";

            }

            //history donor
            elseif ($_GET['module'] == 'history') {
                include "modul/sejarah_donor.php";

            }

            //input transaksi  donor
            elseif ($_GET['module'] == 'transaksi_donor_lama') {
                include "modul/input_transaksi_donor.php";

            }
            //Cari Pendonor
            elseif ($_GET['module'] == 'sahkan_kantong') {
                include "modul/sahkan_kantong_donor.php";
            }

            //Cari Pendonor
            elseif ($_GET['module'] == 'cari_pendonor') {
                include "modul/cari_pendonor.php";
            }
            // Pendonor
            elseif ($_GET['module'] == 'pendonor') {
                include "receptionis_donor.php";
            }
            // donor baru
            elseif ($_GET['module'] == 'donor_baru') {
                include "modul/registrasi.php";
            }
            /* donor ulang
            elseif ($_GET['module']=='donor_ulang'){
                             include "modul/cari_pendonor.php";
                }*/
            //EDIT PMF 2023
            elseif ($_GET['module'] == 'donor_ulang') {
                include "modul/cari_pendonor2023.php";
            }
            //Manual
            elseif ($_GET['module'] == 'manual_seleksi') {
                include "dokumentasidns.php";
            } elseif ($_GET['module'] == 'cari_donor_apheresis') {
                include "modul/cari_pendonor_apheresis.php";
            }
            // luarkota
            elseif ($_GET['module'] == 'luarkota') {
                include "modul/luar_kota22.php";
            } elseif ($_GET['module'] == 'eregistrasiluar') {
                include "modul/edit_registrasiluar.php";
            } elseif ($_GET['module'] == 'history_luar') {
                include "modul/sejarah_donor_luar.php";
            }
            //MOBILE APP
//17-08-2020
            elseif ($_GET['module'] == 'mobile_app') {
                include "p2d2s_mobileapp.php";
            } elseif ($_GET['module'] == 'mobile_antrean') {
                include "mobile/mobile_list_antrean.php";
            } elseif ($_GET['module'] == 'mobile_antreanmu') {
                include "mobile/mobile_list_antrean_mu.php";
            } elseif ($_GET['module'] == 'mobile_transaksi') {
                include "mobile/mobile_antrekan.php";
            } elseif ($_GET['module'] == 'mobile_ubahstatus') {
                include "mobile/mobile_update_status_antrean.php";
            } elseif ($_GET['module'] == 'mobile_ubahstatusmu') {
                include "mobile/mobile_update_status_antreanmu.php";
            } elseif ($_GET['module'] == 'mobile_antrean_proses') {
                include "mobile/mobile_proses_antrean.php";
            } elseif ($_GET['module'] == 'mobile_transaksimu') {
                include "mobile/mobile_transaksimu.php";
            }

            //UPDATE MEDICAL CEHCK UP, APHERESIS
//23-08-2020
            elseif ($_GET['module'] == 'labelsampel') {
                require_once('color.inc');
                include "modul/label_sample.php";
            } elseif ($_GET['module'] == 'checkup_aph') {
                include "tpk/medical_check_up_aph.php";
            } elseif ($_GET['module'] == 'swab') {
                include "tpk/pcr_covid_srcdonor.php";
            } elseif ($_GET['module'] == 'swab1') {
                include "tpk/pcr_covid_input.php";
            } elseif ($_GET['module'] == 'swabrekap') {
                include "tpk/pcr_covid_rekap.php";
            } elseif ($_GET['module'] == 'samplekode') {
                include "color.inc";
                include "tpk/kodesample.php";
            } elseif ($_GET['module'] == 'samplerekap') {
                include "color.inc";
                include "tpk/kodesample_rekap.php";
            } elseif ($_GET['module'] == 'ambilsampel') {
                include "color.inc";
                include "tpk/ambil_sampletpk.php";
            } elseif ($_GET['module'] == 'inputpengambilansample') {
                include "color.inc";
                include "tpk/ambil_sampleinput.php";
            } elseif ($_GET['module'] == 'hasilsampel') {
                include "color.inc";
                include "tpk/rekap_hasilsampel.php";
            } elseif ($_GET['module'] == 'verifsampel') {
                include "color.inc";
                include "tpk/verif_hasilsampel.php";
            } elseif ($_GET['module'] == 'sampellulus') {
                include "color.inc";
                include "tpk/sampel_lulus.php";
            } elseif ($_GET['module'] == 'sampelgagal') {
                include "color.inc";
                include "tpk/sampel_gagal.php";
            } elseif ($_GET['module'] == 'jadwalambil') {
                include "color.inc";
                include "jadwaltpk/jadwal.php";
            } elseif ($_GET['module'] == 'jadwalsampel') {
                include "color.inc";
                include "tpk/jadwalpengambilan.php";
            } elseif ($_GET['module'] == 'tpk_menu') {
                include "color.inc";
                include "tpk_transaksi.php";
            } elseif ($_GET['module'] == 'bataljadwal') {
                include "color.inc";
                include "tpk/bataljadwal.php";
            } elseif ($_GET['module'] == 'edit_transaksi_donor') {
                include "modul/edit_transaksi_donor.php";
            } elseif ($_GET['module'] == 'testlulus') {
                include "tpk/sampel_lulus_new.php";
            } elseif ($_GET['module'] == 'ceklulus') {
                include "tpk/cek_sampel.php";
            } elseif ($_GET['module'] == 'ceksampeldds') {
                include "tpk/cek_sampeldds.php";
            }

            //EDIT PMF 2023 ----START
            elseif ($_GET['module'] == 'dokter') {
                include "modul/search_med_check2023.php";
            } elseif ($_GET['module'] == 'hb') {
                include "modul/search_med_check2023.php";
            } elseif ($_GET['module'] == 'check_up') {
                include "modul/medical_check_up.php";
            } elseif ($_GET['module'] == 'hb_gol') {
                include "modul/medical_hbgol.php";
            } elseif ($_GET['module'] == 'historynas') {
                include "modul/sejarah_donornas.php";
            }
        //EDIT PMF 2023 ----END
    }

    ?>

    <?php
}
?>