<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
	echo "<link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
 			<center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=index.php target=\"_top\"><b>LOGIN</b></a></center>";
}
if (($_SESSION['leveluser']) == 'admin') {
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
	switch (@$_GET['act']) {
		default:
			if (@$_GET['rstock'] == '1')
				include "modul/stock.php";
			if (@$_GET['rstock'] == '3')
				include "modul/stock1.php";
			include "config/koneksi.php";
			include "config/fungsi_combobox.php";
			include "config/library.php";
			$tgll = date("Ymd");
			if ($_GET['module'] == 'home') {
				echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
			} elseif ($_GET['module'] == 'cekkantong') {
				include "penyimpanan/cek_kantong.php";
			} elseif ($_GET['module'] == 'cekstok') {
				include "penyimpanan/cek_stok.php";
			} elseif ($_GET['module'] == 'stokuddlain') {
				include "penyimpanan/info_stok_uddlain.php";
			} elseif ($_GET['module'] == 'skantong') {
				include "modul/skantong.php";
			} elseif ($_GET['module'] == 'musnah') {
				include "modul/musnah.php";
			} elseif ($_GET['module'] == 'cek_pulsa') {
				include "modul/cek_pulsa.php";
			} elseif ($_GET['module'] == 'keluar') {
				include "modul/keluar.php";
			} elseif ($_GET['module'] == 'komponen') {
				include "modul/komponen.php";
			} elseif ($_GET['module'] == 'penambahan_kantong') {
				include "modul/penambahan_kantong.php";
			} elseif ($_GET['module'] == 'pengesahan_kantong') {
				include "modul/pengesahan_kantong.php";
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
				include "modul/label_lab.php";
			} elseif ($_GET['module'] == 'shasil_lab') {
				include "modul/shasil_lab.php";
			} elseif ($_GET['module'] == 'hlab_bonus') {
				include "modul/lab_bonus.php";
			} elseif ($_GET['module'] == 'pengesahan') {
				include "modul/pengesahan_rapidtest.php";
			} elseif ($_GET['module'] == 'scrossmatch') {
				include "modul/search_crossmatch.php";
			} elseif ($_GET['module'] == 'label_cross') {
				include "modul/label_cross.php";
			} elseif ($_GET['module'] == 'sarancrossmatch') {
				include "modul/mod_sarancross.php";
			} elseif ($_GET['module'] == 'crossmatch') {
				include "modul/mod_crossmatch.php";
			} elseif ($_GET['module'] == 'user') {
				include "modul/registrasi.php";
			} elseif ($_GET['module'] == 'cetak_idp') {
				include "modul/cetak_idp.php";
			} elseif ($_GET['module'] == 'cetak_id') {
				include "modul/cetak_id.php";
			} elseif ($_GET['module'] == 'cabut_cekal') {
				include "modul/cabut_cekal.php";
			} elseif ($_GET['module'] == 'double_pendonor') {
				include "modul/double_pendonor.php";
			} elseif ($_GET['module'] == 'hapus_pendonor') {
				include "modul/hapus_pendonor.php";
			} elseif ($_GET['module'] == 'search_pendonor') {
				include "modul/search_pendonor.php";
			} elseif ($_GET['module'] == 'transaksi') {
				include "modul/search_pendonor.php";
			} elseif ($_GET['module'] == 'spendonor') {
				include "modul/search_pendonor_edit.php";
			} elseif ($_GET['module'] == 'ajukan_piagam') {
				include "modul/ajukan_piagam.php";
			} elseif ($_GET['module'] == 'edit_piagam') {
				include "modul/edit_piagam.php";
			} elseif ($_GET['module'] == 'edit_piagam1') {
				include "modul/edit_piagam1.php";
			} elseif ($_GET['module'] == 'laporan_piagam') {
				include "modul/laporan_piagam.php";
			} elseif ($_GET['module'] == 'eregistrasi') {
				include "modul/edit_registrasi.php";
			} elseif ($_GET['module'] == 'tambah_dokter_periksa') {
				include "modul/tambah_dokter_periksa.php";
			} elseif ($_GET['module'] == 'transaksi_donor') {
				include "modul/transaksi_donor.php";
			} elseif ($_GET['module'] == 'spengambilan') {
				include "modul/search_transaksi.php";
			} elseif ($_GET['module'] == 'pengambilan') {
				include "modul/pengambilan_darah.php";
			} elseif ($_GET['module'] == 'pengesahan_pengambilan') {
				include "modul/pengesahan_ambil_darah.php";
			} elseif ($_GET['module'] == 'aturuser') {
				include "admin/user_list.php";
			} elseif ($_GET['module'] == 'aksiuser') {
				include "admin/user_aksi.php";
			} elseif ($_GET['module'] == 'aturagenda') {
				include "modul/mod_agendamn.php";
			} elseif ($_GET['module'] == 'agendaedit') {
				include "modul/mod_agendamn1.php";
			} elseif ($_GET['module'] == 'updateagenda') {
				include "modul/mod_updateagenda.php";
			} elseif ($_GET['module'] == 'tambah_bdrs') {
				include "modul/tambah_bdrs.php";
			} elseif ($_GET['module'] == 'tambahagenda') {
				include "modul/mod_agendamn2.php";
			} elseif ($_GET['module'] == 'entryagenda') {
				include "modul/mod_insertagenda.php";
			} elseif ($_GET['module'] == 'agendahapus') {
				include "modul/mod_hapusagenda.php";
			} elseif ($_GET['module'] == 'agendalist') {
				include "modul/mod_agendalist.php";
			} elseif ($_GET['module'] == 'sms_inbox') {
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
			} elseif ($_GET['module'] == 'rtransaksi') {
				include "modul/rtransaksi.php";
			} elseif ($_GET['module'] == 'stock') {
				include "modul/stock.php";
			} elseif ($_GET['module'] == 'lacak_kantong') {
				include "modul/lacak_kantong.php";
			} elseif ($_GET['module'] == 'tambah_pengumuman') {
				include "modul/tambah_pengumuman.php";
			} elseif ($_GET['module'] == 'lacak_pasien') {
				include "modul/lacak_pasien.php";
			} elseif ($_GET['module'] == 'sejarah') {
				include "modul/lacak_pendonor.php";
			} elseif ($_GET['module'] == 'list_sejarah') {
				include "modul/sejarah.php";
			} elseif ($_GET['module'] == 'admin_sms') {
				include "admin_sms.php";
			} elseif ($_GET['module'] == 'admin_laporan') {
				include "admin_laporan.php";
			} elseif ($_GET['module'] == 'admin_utility') {
				include "admin_utility.php";
			} elseif ($_GET['module'] == 'filebackup') {
				include "filebackup.php";
			} elseif ($_GET['module'] == 'laporan_kegiatan') {
				include "modul/lap_kegiatan.php";
			} elseif ($_GET['module'] == 'lpdr') {
				include "lpdr_$tgll.html";
			} elseif ($_GET['module'] == 'lkr') {
				include "lkr_$tgll.html";
			} elseif ($_GET['module'] == 'lusr') {
				include "lusr_$tgll.html";
			} elseif ($_GET['module'] == 'laporan_peng_darah') {
				include "modul/lap_peng_darah.php";
			} elseif ($_GET['module'] == 'laporan_uji_sharing') {
				include "modul/lap_uji_sharing.php";
			} elseif ($_GET['module'] == 'aktif_udd') {
				include "modul/aktif_udd.php";
			} elseif ($_GET['module'] == 'laporan_buang_darah') {
				include "modul/lap_buang_darah.php";
			} elseif ($_GET['module'] == 'edit_harga') {
				include "modul/edit_harga.php";
			} elseif ($_GET['module'] == 'ganti_menu') {
				include "ganti_menu.php";
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
			} elseif ($_GET['module'] == 'backup_data') {
				include "modul/databackup.php";
			} elseif ($_GET['module'] == 'restore_data') {
				include "modul/datarestore.php";
			} elseif ($_GET['module'] == 'updatekantong') {
				include "modul/update_sah_kantong.php";
			} elseif ($_GET['module'] == 'deltransaksi') {
				include "modul/del_transaksi.php";
			} elseif ($_GET['module'] == 'delmedical') {
				include "modul/del_med_check.php";
			} elseif ($_GET['module'] == 'balas_sms') {
				include "modul/sms_balas.php";
			} elseif ($_GET['module'] == 'hapus_sms_inbox') {
				include "modul/sms_inbox_hapus.php";
			} elseif ($_GET['module'] == 'kosongkan_inbox') {
				include "modul/sms_kosongkan_inbox.php";
			} elseif ($_GET['module'] == 'kosongkan_outbox') {
				include "modul/sms_kosongkan_outbox.php";
			} elseif ($_GET['module'] == 'sms_staf') {
				include "modul/sms_staf.php";
			} elseif ($_GET['module'] == 'broadcast_donor') {
				include "modul/cron_sms.php";
			} elseif ($_GET['module'] == 'sms_manual') {
				include "modul/sms_manual.php";
			} elseif ($_GET['module'] == 'sms_broadcast_ultah') {
				include "modul/sms_broadcast_ultah.php";
			} elseif ($_GET['module'] == 'login_data') {
				include "modul/data_login.php";
			} elseif ($_GET['module'] == 'rincian_minta_barang') {
				include "logistik/rincian_transaksi_minta_barang.php";
			} elseif ($_GET['module'] == 'historycetak') {
				include "modul/historycetak.php";
			} elseif ($_GET['module'] == 'opname_sehat') {
				include "modul/opname_sehat.php";
			} elseif ($_GET['module'] == 'opname_karantina') {
				include "modul/opname_karantina.php";
			} elseif ($_GET['module'] == 'backup_sms') {
				include "modul/databackup_sms.php";
			} elseif ($_GET['module'] == 'restore_sms') {
				include "modul/datarestore_sms.php";
			} elseif ($_GET['module'] == 'history_revisi') {
				include "modul/history_tim_source.php";
			} elseif ($_GET['module'] == 'update') {
				include "modul/update.php";
			} elseif ($_GET['module'] == 'uploadserverpmi') {
				include "modul/upload_data_to_pmiserver.php";
			} elseif ($_GET['module'] == 'settingserver') {
				include "modul/setting_server_pusat.php";
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
			elseif ($_GET['module'] == 'updatepekerjaan') {
				include "modul/update_pekerjaan.php";
			}
			//SHIFT
			elseif ($_GET['module'] == 'tambah_shift') {
				include "modul/tambah_shift.php";
			}

			//rekap data pendonor eksport ke excel
			elseif ($_GET['module'] == 'rekap_pendonor') {
				include "modul/rekap_pendonor.php";
			}

			//history donor
			elseif ($_GET['module'] == 'history') {
				include "modul/sejarah_donor.php";
			}

			//Audit trial
			// elseif ($_GET['module']=='audit_trial'){include "modul/log_user.php";} ganti ke fornt end baru
			elseif ($_GET['module'] == 'audit_trial') {
				include "pmf/pmf_audittrail.php";
			}

			// 01-01-2024 --> PMF=================================================
			elseif ($_GET['module'] == 'pmf_audittrail') {
				include "pmf/pmf_audittrail.php";
			} elseif ($_GET['module'] == 'pmf_dokumen') {
				include "pmf/dokumen_mutu.php";
			} elseif ($_GET['module'] == 'pmf_reagen') {
				include "pmf/pmf_masterreagen.php";
			}
			// --------------------------------------

			//Audit trial
			elseif ($_GET['module'] == 'kuisioner') {
				include "modul/kuisioner.php";
			}
			//Manual SIMDONDAR ===============================
			elseif ($_GET['module'] == 'manual') {
				include "admin_manual.php";
			} elseif ($_GET['module'] == 'manual_p2dds') {
				include "dokumentasippdds.php";
			} elseif ($_GET['module'] == 'manual_aftap') {
				include "dokumentasiaftap.php";
			} elseif ($_GET['module'] == 'manual_logistik') {
				include "dokumentasilogistik.php";
			} elseif ($_GET['module'] == 'manual_seleksi') {
				include "dokumentasidns.php";
			} elseif ($_GET['module'] == 'manual_mu') {
				include "dokumentasimobile.php";
			} elseif ($_GET['module'] == 'manual_kgd') {
				include "dokumentasikonfirmasi.php";
			} elseif ($_GET['module'] == 'manual_imltd') {
				include "dokumentasiimltd.php";
			} elseif ($_GET['module'] == 'manual_komponen') {
				include "dokumentasikomponen.php";
			} elseif ($_GET['module'] == 'manual_loket') {
				include "dokumentasipasien.php";
			} elseif ($_GET['module'] == 'manual_cross') {
				include "dokumentasicross.php";
			} elseif ($_GET['module'] == 'changestatus') {
				include "modul/ubah_status_kantong.php";
			} elseif ($_GET['module'] == 'sop') {
				include "sop.php";
			} elseif ($_GET['module'] == 'instalasi') {
				include "sopinstalasi.php";
			} elseif ($_GET['module'] == 'migrasi') {
				include "sopmigrasi.php";
			} elseif ($_GET['module'] == 'backup') {
				include "sopbackup.php";
			} elseif ($_GET['module'] == 'levelakses') {
				include "soplevel.php";
			} elseif ($_GET['module'] == 'testinglive') {
				include "soptesting.php";
			} elseif ($_GET['module'] == 'mulfunction') {
				include "sopmulfunction.php";
			} elseif ($_GET['module'] == 'validasi') {
				include "sopvalidasi.php";
			} elseif ($_GET['module'] == 'training') {
				include "soptraining.php";
			}

			// =============================== Whatsap badBoy 15112020
			elseif ($_GET['module'] == 'admin_wa') {
				include "whatsapp/admin_wa.php";
			} elseif ($_GET['module'] == 'wa_manual') {
				include "whatsapp/wa_manual.php";
			} elseif ($_GET['module'] == 'wa_broadcast') {
				include "whatsapp/wa_broadcast.php";
			} elseif ($_GET['module'] == 'wa_inbox') {
				include "whatsapp/wa_inbox.php";
			} elseif ($_GET['module'] == 'balas_wa') {
				include "whatsapp/wa_balas.php";
			} elseif ($_GET['module'] == 'wa_outbox') {
				include "whatsapp/wa_outbox.php";
			} elseif ($_GET['module'] == 'wa_sent') {
				include "whatsapp/fungsi_indotgl.php";
				include "whatsapp/rekap_wa.php";
			} elseif ($_GET['module'] == 'hapus_wa_inbox') {
				include "whatsapp/wa_inbox_hapus.php";
			} elseif ($_GET['module'] == 'hapus_wa_outbox') {
				include "whatsapp/wa_outbox_hapus.php";
			} elseif ($_GET['module'] == 'kosongkan_wa_inbox') {
				include "whatsapp/wa_inbox_kosong.php";
			} elseif ($_GET['module'] == 'kosongkan_wa_outbox') {
				include "whatsapp/wa_outbox_kosong.php";
			} elseif ($_GET['module'] == 'wa_ultah') {
				include "whatsapp/wa_broadcast_ultah.php";
			} elseif ($_GET['module'] == 'wa_setting') {
				include "whatsapp/wa_setting.php";
			} elseif ($_GET['module'] == 'wa_instansi') {
				include "whatsapp/wa_donor_ins.php";
			} elseif ($_GET[module] == 'edit_kantong') {
				include "modul/edit_kantong.php";
			} elseif ($_GET[module] == 'edit_transaksi') {
				include "modul/edit_transaksi.php";
			} elseif ($_GET[module] == 'edit_cross') {
				include "modul/edit_cross.php";
			}
	}
}

if (($_SESSION['leveluser']) == 'konseling') {
	switch (@$_GET['act']) {
		default:
			include "config/koneksi.php";
			include "config/fungsi_combobox.php";
			include "config/library.php";
			$tgll = date("Ymd");
			if ($_GET['module'] == 'cabut_cekal') {
				include "modul/cabut_cekal.php";
			}
	}
}
?>