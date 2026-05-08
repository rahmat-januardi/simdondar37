<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
    echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
    <center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";
}
if ($_SESSION['leveluser'] == 'kasir2') { ?>
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
            include "config/koneksi.php";
            include "config/fungsi_combobox.php";
            include "config/library.php";

            if ($_GET['module'] == 'home') {
                echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
            } elseif ($_GET['module'] == 'rekap') {
                include "receptionis_rekap2.php";
            } elseif ($_GET['module'] == 'cetak_minta') {
                include('color.inc');
                include "modul/mod_cetak_minta.php";
            } elseif ($_GET['module'] == 'permintaan') {
                include "modul/mod_permintaan.php";
            }
            //UPDATE 2021 online
            elseif ($_GET['module'] == 'verifantrirs') {
                include "modul/mod_addmintaonline.php";
            } elseif ($_GET['module'] == 'rsonline') {
                include "modul/mod_permintaanonline.php";
            } elseif ($_GET['module'] == 'tambah_permintaan') {
                include "modul/mod_tambah_permintaan.php";
            } elseif ($_GET['module'] == 'batalmintars') {
                include "modul/entry_batalpermintaanrs.php";
            } elseif ($_GET['module'] == 'tambah_permintaan') {
                include "modul/mod_tambah_permintaan.php";
            } elseif ($_GET['module'] == 'detailpermintaan') {
                include "modul/permintaandetail.php";
            } elseif ($_GET['module'] == 'editpermintaan') {
                include "modul/editpermintaan.php";
            } elseif ($_GET['module'] == 'editlayanan') {
                include "modul/editpembayaran.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar1') {
                include "modul/darahkeluarrs_rekap.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar') {
                include "modul/darahkeluarrs_rincian.php";
            }
            /*elseif ($_GET['module']=='rekap_darah_keluar1'){
                include "modul/rekap_darah_keluar1.php";
            }
             elseif ($_GET['module']=='rekap_darah_keluar'){
                include "modul/rekap_darah_keluar.php";
            }*/ elseif ($_GET['module'] == 'rekap_darah_keluar_lama') {
                include "modul/rekap_darah_keluar_lama.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_bdrs') {
                include "modul/rekap_darah_keluar_bdrs_new.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_udd') {
                include "modul/rekap_darah_keluar_udd_new.php";
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
            } elseif ($_GET['module'] == 'double_pasien') {
                include "modul/double_pasien.php";
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
            } elseif ($_GET['module'] == 'rekap_darah_titip') {
                include "modul/rekap_darah_titip.php";
            } elseif ($_GET['module'] == 'kembali') {
                include "modul/mod_kembali.php";
            } elseif ($_GET['module'] == 'stock') {
                include "modul/stock.php";
            } elseif ($_GET['module'] == 'tambah_dokter') {
                include "modul/tambah_dokter.php";
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
            } elseif ($_GET['module'] == 'receptionis_transaksi2') {
                include "receptionis_transaksi2.php";
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
            } elseif ($_GET['module'] == 'check_pk') {
                include "modul/search_med_check_pk.php";
            } elseif ($_GET['module'] == 'spengambilan') {
                include "modul/search_transaksi.php";
            } elseif ($_GET['module'] == 'gantikantong') {
                include "modul/search_transaksi_gk.php";
            } elseif ($_GET['module'] == 'checkup') {
                include "modul/medical_checkup.php";
            } elseif ($_GET['module'] == 'pengambilan') {
                include "modul/pengambilan_darah.php";
            } elseif ($_GET['module'] == 'eregistrasi_pasien_plebotomi') {
                include "modul/edit_registrasi_pasien_plebotomi.php";
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
                include "modul/pengambilan_darah_apheresis.php";
            } elseif ($_GET['module'] == 'rekap_donor') {
                include "modul/rekap_donor.php";
            } elseif ($_GET['module'] == 'tambah_kecamatan') {
                include "modul/tambah_kecamatan.php";
            } elseif ($_GET['module'] == 'tambah_kelurahan') {
                include "modul/tambah_kelurahan.php";
            } elseif ($_GET['module'] == 'rekap_permintaan_lama') {
                include "modul/rekap_permintaan_batal.php";
            } elseif ($_GET['module'] == 'searchpasien') {
                include "modul/search_pasien.php";
            } elseif ($_GET['module'] == 'epasien') {
                include "modul/editpasien.php";
            } elseif ($_GET['module'] == 'edit_datapasien') {
                include "modul/edit_permintaan.php";

            } elseif ($_GET['module'] == 'form_pengantar') {
                include "modul/pengantar_darah.php";

            } elseif ($_GET['module'] == 'permintaan1') {
                include "receptionis_permintaan.php";
            } elseif ($_GET['module'] == 'pasien_plebotomi') {
                include "modul/search_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'registrasi_pasien_plebotomi') {
                include "modul/registrasi_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'eregistrasi_pasien_plebotomi') {
                include "modul/edit_registrasi_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'sejarah_pasien_plebotomi') {
                include "modul/sejarah_pasien_plebotomi.php";
            } elseif ($_GET['module'] == 'laporan_pasien_plebotomi') {
                include "modul/laporan_pasien_plebotomi.php";
            }
            //Manual
            elseif ($_GET['module'] == 'manual_loket') {
                include "dokumentasipasien.php";
            }
            //23-12-2018
            elseif ($_GET['module'] == 'rekap_permintaanrs') {
                include "modul/rekap_permintaanrs.php";
            } elseif ($_GET['module'] == 'rekap_pengeluarandarah') {
                include "modul/rekap_pengeluarandarahrs.php";
            }
            //12-2019
            //NEW PERMINTAAN DROPPING=============================
            elseif ($_GET['module'] == 'listpermintaandropping') {
                include "modul/drop_req_list.php";
            } elseif ($_GET['module'] == 'permintaan_brds') {
                include "modul/drop_req_b.php";
            } elseif ($_GET['module'] == 'permintaan_v') {
                include "modul/drop_req_v.php";
            } elseif ($_GET['module'] == 'permintaan_udd') {
                include "modul/drop_req_u.php";
            } elseif ($_GET['module'] == 'drop_bdrs') {
                include "modul/drop_b.php";
            } elseif ($_GET['module'] == 'drop_udd') {
                include "modul/drop_u.php";
            } elseif ($_GET['module'] == 'drop_cb') {
                include "modul/drop_c_b.php";
            } elseif ($_GET['module'] == 'drop_cu') {
                include "modul/drop_c_u.php";
            } elseif ($_GET['module'] == 'drop_list') {
                include "modul/drop_kirim_list.php";
            } elseif ($_GET['module'] == 'drop_listby') {
                include "modul/drop_kirim_list_by.php";
            }
            //12-2019
            elseif ($_GET['module'] == 'bookminta') {
                include "modul/entry_bookpermintaan.php";
            } elseif ($_GET['module'] == 'batalminta') {
                include "modul/entry_batalpermintaan.php";
            }
            //TPK URGENT
            /* elseif ($_GET['module']=='rekap_permintaan_tpkbelum'){
                    include "tpksolo/rekap_permintaan_tpkbelum.php";
                }*/ elseif ($_GET['module'] == 'rekap_permintaan_tpkbelum') {
                include "tpksolo/api/frontmintapkgolsim.php";
            } elseif ($_GET['module'] == 'batalkan') {
                include "tpksolo/api/batalkan.php";
            } elseif ($_GET['module'] == 'rekap_permintaan_tpksudah') {
                include "modul/rekap_permintaan_tpksudah.php";
            } elseif ($_GET['module'] == 'rekap_permintaan_tpkbatal') {
                include "modul/rekap_permintaan_tpkbatal.php";
            } elseif ($_GET['module'] == 'rekap_catatan') {
                include "modul/rekap_catatan.php";
            } elseif ($_GET['module'] == 'rekap_edit') {
                include "modul/rekap_catatan_open.php";
            } elseif ($_GET['module'] == 'rekap_selesai') {
                include "modul/rekap_catatan_close.php";
            } elseif ($_GET['module'] == 'kwitansititer') {
                include "modul/mod_kwitansititer.php";
            } elseif ($_GET['module'] == 'rekap_pembayaranlain') {
                include "modul/rekap_pembayaran_lain.php";
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
            } elseif ($_GET['module'] == 'rekap_terimasampel') {
                include "modul/terima_sampel.php";
            } elseif ($_GET['module'] == 'pasienpmi') {
                include "tpksolo/view/pasienpmisim.php";
            }
            //distribusi darah
            elseif ($_GET['module'] == 'receptionis_distribusi') {
                include "receptionis_distribusi.php";
            } elseif ($_GET['module'] == 'form_bdrs') {
                include "modul/form_bdrs.php";
            } elseif ($_GET['module'] == 'form_bdrsxls') {
                include "modul/form_bdrsxls.php";
            } elseif ($_GET['module'] == 'dari_bdrs') {
                include "modul/terima_bdrs.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_bdrs') {
                include "modul/rekap_darah_keluar_bdrs_new.php";
            } elseif ($_GET['module'] == 'rekap_darah_keluar_udd') {
                include "modul/rekap_darah_keluar_udd_new.php";
            } elseif ($_GET['module'] == 'rekap_darah_terima_udd') {
                include "modul/rekap_darah_terima_udd_new.php";
            } elseif ($_GET['module'] == 'tambah_bdrs') {
                include "modul/tambah_bdrs_kasir.php";
            } elseif ($_GET['module'] == 'form_udd') {
                include "modul/form_udd.php";
            } elseif ($_GET['module'] == 'terima_dari_utd_lain') {
                include "modul/terima_dari_utd_lain.php";
            } elseif ($_GET['module'] == 'form_uddxls') {
                include "modul/form_uddxls.php";
            } elseif ($_GET['module'] == 'musnah') {
                include "modul/musnah.php";
            }

	//Inoput Stok Manual
	elseif ($_GET['module']=='inputstok')               {include  "distribusi/stok_inputmanual.php";}
        elseif ($_GET['module']=='inputstokrekap')          {include  "distribusi/stok_inputmanual_rekap.php";}

    }
}
?>
