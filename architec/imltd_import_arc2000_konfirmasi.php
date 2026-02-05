<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser = $_SESSION[namauser];
$namalengkap = $_SESSION[nama_lengkap];
?>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		@import url("topstyle.css");

		tr {
			background-color: #FFF8DC
		}

		.initial {
			background-color: #FFF8DC;
			color: #000000
		}

		.normal {
			background-color: #FFF8DC
		}

		.highlight {
			background-color: #7FFF00
		}
	</style>
	<style>
		.awesomeText {
			color: #000;
			font-size: 150%;
		}
	</style>
	<script language="javascript">
		jum_select = 0;

		function show1(idreag) {
			var campur = document.getElementById('reagen1').value;
			var reag1 = campur.split('*');
			document.getElementById('nama1').innerHTML = reag1[0];
			document.getElementById('nolot1').innerHTML = reag1[1];
			document.getElementById('kode1').innerHTML = reag1[2];
			document.getElementById('sisa_test1').innerHTML = reag1[3];
			if (reag1[0] === "") {
				alert("Reagen HBsAg harus dipilih");
			}
		}

		function show2(idreag) {
			var campur = document.getElementById('reagen2').value;
			var reag2 = campur.split('*');
			document.getElementById('nama2').innerHTML = reag2[0];
			document.getElementById('nolot2').innerHTML = reag2[1];
			document.getElementById('kode2').innerHTML = reag2[2];
			document.getElementById('sisa_test2').innerHTML = reag2[3];
			if (reag2[0] === "") {
				alert("Reagen HCV harus dipilih");
			}
		}

		function show3(idreag) {
			var campur = document.getElementById('reagen3').value;
			var reag3 = campur.split('*');
			document.getElementById('nama3').innerHTML = reag3[0];
			document.getElementById('nolot3').innerHTML = reag3[1];
			document.getElementById('kode3').innerHTML = reag3[2];
			document.getElementById('sisa_test3').innerHTML = reag3[3];
			if (reag3[0] === "") {
				alert("Reagen HIV harus dipilih");
			}
		}

		function show4(idreag) {
			var campur = document.getElementById('reagen4').value;
			var reag4 = campur.split('*');
			document.getElementById('nama4').innerHTML = reag4[0];
			document.getElementById('nolot4').innerHTML = reag4[1];
			document.getElementById('kode4').innerHTML = reag4[2];
			document.getElementById('sisa_test4').innerHTML = reag4[3];
			if (reag4[0] === "") {
				alert("Reagen Trep harus dipilih");
			}
		}

		function nextproses(jmlperiksa) {
			var campur = document.getElementById('reagen').value;
			var masterreagen = campur.split('*');
			var parameter = masterreagen[8];
			var metode = masterreagen[5];
			var nolot = masterreagen[6];
			var kode_reagen = masterreagen[0];
			var sisa_tes = masterreagen[1];
			var reaktif = masterreagen[2];
			var nonreaktif = masterreagen[3];
			var greyzone = masterreagen[4];
			if (masterreagen[0] === "") {
				alert("Proses tidak bisa dilanjutkan, pilih dulu reagen yang digunakan!!!");
			} else if (sisa_tes < jmlperiksa) {
				alert("Proses konfirmasi tidak bisa dilanjutkan. Sisa test reagen kurang dari jumlah kantong yang diperiksa. Ganti dengan Reagen lain!!!");
			} else {
				var konfirmasi = confirm('Lanjutkan ke proses input hasil manual?');
				if (konfirmasi === true) {
					document.location.href = 'pmiimltd.php?module=import_etimax3000manualinput&parameter=' + parameter +
						'&nolot=' + nolot + '&metode=' + metode + '&kode_reagen=' + kode_reagen + '&sisa_tes=' + sisa_tes + '&reaktif=' + reaktif + '&nonreaktif=' + nonreaktif + '&greyzone=' + greyzone;
				}
			}
		}
	</script>
	<script>
		$(function() {
			$('a[href*=#]:not([href=#])').click(function() {
				if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
					if (target.length) {
						$('html,body').animate({
							scrollTop: target.offset().top
						}, 1000);
						return false;
					}
				}
			});
		});
	</script>

	<title>SIMDONDAR</title>
</head>

<body>
	<?php
	if (isset($_POST['Button'])) {
		$ptgKonfirmasi 	= $_POST[konfirmasi];
		$ptgSah			= $_POST[disahkan_oleh];
		$ptgOperator	= $_POST[operator];
		$instrument     = $_POST[instrument];
		$arc_serial     = $_POST[arc_serial];
		$instr_v        = $_POST[instr_v];
		$interface_sn   = $_POST[interface_sn];
		$scc_serial     = $_POST[scc_serial];
		$trans_time     = $_POST[trans_time];
		$user_instrument = $_POST[user];

		$lanjut = 0;
		$today           = date("Y-m-d");
		$today1          = date("Y-m-d H:i:s");
		$jumlah_sample   = count($_POST[sample]);
		$jml_test_b      = $_POST['jml_test_b'];
		$jml_test_c      = $_POST['jml_test_c'];
		$jml_test_i      = $_POST['jml_test_i'];
		$jml_test_s      = $_POST['jml_test_s'];
		echo "Jumlah Sample :" . $jumlah_sample . "; HbsAg: " . $jml_test_b . "; HCV: " . $jml_test_c . "; HIV: " . $jml_test_i . "Syphilis/TP: " . $jml_test_s . ";<br>";

		//PREAPRE REAGENSIA==========================================
		$reagen1 = $_POST[reagen1];
		$reag1_ex = explode('*', $reagen1);
		$reag1_nama = $reag1_ex[0];
		$reag1_nolot = $reag1_ex[1];
		$reag1_kode = $reag1_ex[2];
		$reag1_tes = $reag1_ex[3];
		$reag1_ed = $reag1_ex[4];
		$reagen2 = $_POST[reagen2];
		$reag2_ex = explode('*', $reagen2);
		$reag2_nama = $reag2_ex[0];
		$reag2_nolot = $reag2_ex[2];
		$reag2_kode = $reag2_ex[2];
		$reag2_tes = $reag2_ex[3];
		$reag2_ed = $reag2_ex[4];
		$reagen3 = $_POST[reagen3];
		$reag3_ex = explode('*', $reagen3);
		$reag3_nama = $reag3_ex[0];
		$reag3_nolot = $reag3_ex[1];
		$reag3_kode = $reag3_ex[2];
		$reag3_tes = $reag3_ex[3];
		$reag3_ed = $reag3_ex[4];
		$reagen4 = $_POST[reagen4];
		$reag4_ex = explode('*', $reagen4);
		$reag4_nama = $reag4_ex[0];
		$reag4_nolot = $reag4_ex[1];
		$reag4_kode = $reag4_ex[2];
		$reag4_tes = $reag4_ex[3];
		$reag4_ed = $reag4_ex[4];
		//END OF PREPARE REAGENSIA

		//Cek Reagen dan pilihan Petugas
		if (intval($reag1_tes) < $jml_test_b) {
			$lanjut = 1;
			echo "<SCRIPT>alert('Konfirmasi tidak bisa dilanjutkan. Jumlah test reagensia HBsAg tidak mencukupi.');</SCRIPT>";
		}
		if (intVal($reag2_tes) < $jml_test_c) {
			$lanjut = 2;
			echo "<SCRIPT>alert('Konfirmasi tidak bisa dilanjutkan. Jumlah test reagensia HCV tidak mencukupi');</SCRIPT>";
		}
		if (intval($reag3_tes) < $jml_test_i) {
			$lanjut = 3;
			echo "<SCRIPT>alert('Konfirmasi tidak bisa dilanjutkan. Jumlah test reagensia HIV tidak mencukupi');</SCRIPT>";
		}
		if (intval($reag4_tes) < $jml_test_s) {
			$lanjut = 4;
			echo "<SCRIPT>alert('Konfirmasi tidak bisa dilanjutkan. Jumlah test reagensia Treponema tidak mencukupi');</SCRIPT>";
		}
		if ($ptgKonfirmasi == "") {
			$lanjut = 5;
			echo "<SCRIPT>alert('Pilih Petugas yang mengkonfirmasi hasil.');</SCRIPT>";
		}
		if (($ptgSah == "") or ($ptgSah == "-")) {
			$lanjut = 6;
			echo "<SCRIPT>alert('Pilih petugas yang mengesahkan hasil');</SCRIPT>";
		}

		if ($lanjut == 0) {

			//Generated NoTransaksi===============================================
			$sql_elisa	= mysql_query("SELECT MAX(CONVERT(notrans, SIGNED INTEGER)) AS Kode FROM hasilelisa");
			$dta_elisa	= mysql_fetch_assoc($sql_elisa);
			$int_elisa  = (int)($dta_elisa[Kode]);
			$int_no = $int_elisa;
			$int_no_inc = (int)$int_no + 1;
			$j_nol = 8 - (strlen(strval($int_no_inc)));
			for ($i = 0; $i < $j_nol; $i++) {
				$no_tmp .= "0";
			}
			$notrans = $no_tmp . $int_no_inc;
			echo "No. Transaksi :  " . $notrans . " Tanggal Periksa : " . $today1 . " (" . date_default_timezone_get() . ")<br>";
			//------------ END Generate no transaksi ---------------


			//inisial jumlah real penggunaan reagan
			$jmlreag_b = 0;
			$jmlreag_c = 0;
			$jmlreag_i = 0;
			$jmlreag_s = 0;


			//Counting data
			for ($i = 0; $i < count($_POST[sample]); $i++) {
				$sample     = $_POST[sample][$i];
				$carrier    = $_POST[carrier][$i];
				$position   = $_POST[position][$i];
				$aksi_konfirm = $_POST[aksi][$i];
				$umur		= $_POST[umur][$i];
				$jnsdonor	= $_POST[jenis_donor][$i];
				$barulama	= $_POST[donorbaru][$i];
				$status_kantong = $_POST[statuskantong][$i];

				$idraw_b    = $_POST[id_b][$i];
				$run_time_b = $_POST[run_time_b][$i];
				$time_bs    = strtotime($run_time_b);
				$date_b     = date("Y/m/d", $time_bs);
				$od_b       = $_POST[hbsag_od][$i];
				$lotr_b     = $_POST[hbsag_lotr][$i];
				$snr_b      = $_POST[hbsag_snr][$i];
				$result_b   = $_POST[hbsag_result][$i];
				$ket_b      = $_POST[hbsag_ket][$i];
				$ckl_b      = $_POST[cekal_hbsag][$i];

				$idraw_c    = $_POST[id_c][$i];
				$run_time_c = $_POST[run_time_c][$i];
				$time_cs    = strtotime($run_time_c);
				$date_c     = date("Y/m/d", $time_cs);
				$od_c       = $_POST[hcv_od][$i];
				$lotr_c     = $_POST[hcv_lotr][$i];
				$snr_c      = $_POST[hcv_snr][$i];
				$result_c   = $_POST[hcv_result][$i];
				$ckl_c      = $_POST[cekal_hcv][$i];
				$ket_c      = $_POST[hcv_ket][$i];

				$idraw_i    = $_POST[id_i][$i];
				$run_time_i = $_POST[run_time_i][$i];
				$time_is    = strtotime($run_time_i);
				$date_i     = date("Y/m/d", $time_is);
				$od_i       = $_POST[hiv_od][$i];
				$lotr_i     = $_POST[hiv_lotr][$i];
				$snr_i      = $_POST[hiv_snr][$i];
				$result_i   = $_POST[hiv_result][$i];
				$ckl_i      = $_POST[cekal_hiv][$i];
				$ket_i      = $_POST[hiv_ket][$i];

				$idraw_s    = $_POST[id_s][$i];
				$run_time_s = $_POST[run_time_s][$i];
				$time_ss    = strtotime($run_time_s);
				$date_s     = date("Y/m/d", $time_ss);
				$od_s       = $_POST[syp_od][$i];
				$lotr_s     = $_POST[syp_lotr][$i];
				$snr_s      = $_POST[syp_snr][$i];
				$result_s   = $_POST[syp_result][$i];
				$ckl_s      = $_POST[cekal_syp][$i];
				$ket_s      = $_POST[syp_ket][$i];

				if ($idraw_b == "0") {
					$koder_b = null;
					$edr_b = null;
				} else {
					$koder_b = $reag1_kode;
					$edr_b = $reag1_ed;
				}
				if ($idraw_c == "0") {
					$koder_c = null;
					$edr_c = null;
				} else {
					$koder_c = $reag2_kode;
					$edr_c = $reag2_ed;
				}
				if ($idraw_i == "0") {
					$koder_i = null;
					$edr_i = null;
				} else {
					$koder_i = $reag3_kode;
					$edr_i = $reag3_ed;
				}
				if ($idraw_s == "0") {
					$koder_s = null;
					$edr_s = null;
				} else {
					$koder_s = $reag4_kode;
					$edr_s = $reag4_ed;
				}
				$catatan = "";
				if ($catatan !== "") {
					if ($idraw_b !== "0") {
						$catatan = $ket_b;
					}
				}
				if ($catatan !== "") {
					if ($idraw_c !== "0") {
						$catatan = $ket_c;
					}
				}
				if ($catatan !== "") {
					if ($idraw_i !== "0") {
						$catatan = $ket_s;
					}
				}
				if ($catatan !== "") {
					if ($idraw_s !== "0") {
						$catatan = $ket_i;
					}
				}
				$no = $i + 1;
				echo "$no. " . $_POST[sample][$i] . " - " . $aksi_konfirm . " - ";

				//Jika pilihan konfirmasi tidak sama dengan TUNDA
				if ($aksi_konfirm !== "3") {
					//PENGGUNAAN REAL REAGEN SESUAI DENGAN PILIHAN AKSI --> TUNDA --> Tidak mengurangi reagen
					if ($idraw_b !== "0") {
						$jmlreag_b++;
					}
					if ($idraw_c !== "0") {
						$jmlreag_c++;
					}
					if ($idraw_i !== "0") {
						$jmlreag_i++;
					}
					if ($idraw_s !== "0") {
						$jmlreag_s++;
					}

					echo "-Proses-";
					//2. insert record to imltd_arc_konfirm except "aksi=3"
					$q_konfirm = "INSERT INTO `imltd_arc_konfirm`(`no_trans`, `instr`, `instr_v`, `scc_serial`, `interface_sn`, `arc_serial`, `trans_time`, `user`,
                `id_tes`, `carrier`, `position`,
                `b_lot_reag`, `b_id_raw`, `b_ed_reag`, `b_kode_reag`, `b_sn_reag`, `b_abs`, `b_run_time`, `b_hasil`, `b_ket_tes`,
                `c_lot_reag`, `c_id_raw`, `c_ed_reag`, `c_kode_reag`, `c_sn_reag`, `c_abs`, `c_run_time`, `c_hasil`, `c_ket_tes`,
                `i_lot_reag`, `i_id_raw`, `i_ed_reag`, `i_kode_reag`, `i_sn_reag`, `i_abs`, `i_run_time`, `i_hasil`, `i_ket_tes`,
                `s_lot_reag`, `s_id_raw`, `s_ed_reag`, `s_kode_reag`, `s_sn_reag`, `s_abs`, `s_run_time`, `s_hasil`, `s_ket_tes`,
                `konfirmer`, `disahkan`, `status_kantong`, `konfirm_action`)
                VALUES ('$notrans', '$instrument', '$instr_v', '$scc_serial', '$interface_sn', '$arc_serial', '$trans_time', '$user_instrument',
                '$sample', '$carrier', '$position',
                '$lotr_b', '$idraw_b', '$edr_b', '$koder_b', '$snr_b', '$od_b', '$run_time_b', '$result_b', '$ket_b',
                '$lotr_c', '$idraw_c', '$edr_c', '$koder_c', '$snr_c', '$od_c', '$run_time_c', '$result_c', '$ket_c',
                '$lotr_i', '$idraw_i', '$edr_i', '$koder_i', '$snr_i', '$od_i', '$run_time_i', '$result_i', '$ket_i',
                '$lotr_s', '$idraw_s', '$edr_s', '$koder_s', '$snr_s', '$od_s', '$run_time_s', '$result_s', '$ket_s',
                '$ptgKonfirmasi', '$ptgSah', '$status_kantong', '$aksi_konfirm')";
					//echo "-$q_konfirm-<br>";
					$insert_konfirm = mysql_query($q_konfirm);
					if ($insert_konfirm) {
						echo "-table konfirm OK-";
					} else {
						echo "-table konfirm ERR-";
					}
					//=======Audit Trial====================================================================================
					$log_mdl = 'IMLTD';
					$log_aksi = 'IMLTD Architech :' . $notrans . '; ID:' . $sample . '; hasil HBV :' . $result_b . '; HCV:' . $result_c . '; HIV:' . $result_i . '; Shypilis:' . $result_s;
					include('user_log.php');
					//======================================================================================================		
					//3. insert to hasil elisa
					//tambahan field : umur, jenis donor, ket_hasil, catatan
					// Insert parameter HBsAg
					if ($idraw_b !== "0") {
						$sq_hbsag = "insert into hasilelisa (noKantong,OD,COV,Hasil,notrans,jenisPeriksa,tglPeriksa,
                               dicatatOleh,dicekOleh,DisahkanOleh,nolot,Metode, umur, jns_donor, baru_ulang, catatan)
						       values ('$sample','$od_b','1.00','$result_b','$notrans','0','$date_b',
						      '$user_instrument','$ptgKonfirmasi','$ptgSah','$koder_b','chlia','$umur', '$jnsdonor', '$barulama', '$catatan')";
						echo "- EIA B - ";
						$sql_hbsag = mysql_query($sq_hbsag);
					}
					// Insert parameter HCV
					if ($idraw_c !== "0") {
						$sq_hcv  = "insert into hasilelisa (noKantong,OD,COV,Hasil,notrans,jenisPeriksa,tglPeriksa,
                               dicatatOleh,dicekOleh,DisahkanOleh,nolot,Metode,umur, jns_donor, baru_ulang, catatan)
						       values ('$sample','$od_c','1.00','$result_c','$notrans','1','$date_c',
						      '$user_instrument','$ptgKonfirmasi','$ptgSah','$koder_c','chlia','$umur', '$jnsdonor', '$barulama', '$catatan')";
						echo "- EIA C - ";
						$sql_hcv = mysql_query($sq_hcv);
					}

					// Insert parameter HIV
					if ($idraw_i !== "0") {
						$sq_hiv  = "insert into hasilelisa (noKantong,OD,COV,Hasil,notrans,jenisPeriksa,tglPeriksa,
                               dicatatOleh,dicekOleh,DisahkanOleh,nolot,Metode,umur, jns_donor, baru_ulang, catatan)
						       values ('$sample','$od_i','1.00','$result_i','$notrans','2','$date_i',
						      '$user_instrument','$ptgKonfirmasi','$ptgSah','$koder_i','chlia','$umur', '$jnsdonor', '$barulama', '$catatan')";
						echo "- EIA I - ";
						$sql_hiv = mysql_query($sq_hiv);
					}

					// Insert parameter Sifilis
					if ($idraw_s !== "0") {
						$sq_syp  = "insert into hasilelisa (noKantong,OD,COV,Hasil,notrans,jenisPeriksa,tglPeriksa,
                               dicatatOleh,dicekOleh,DisahkanOleh,nolot,Metode,umur, jns_donor, baru_ulang, catatan)
						       values ('$sample','$od_s','1.00','$result_s','$notrans','3','$date_s',
						      '$user_instrument','$ptgKonfirmasi','$ptgSah','$koder_s','chlia','$umur', '$jnsdonor', '$barulama', '$catatan')";
						echo "- EIA S - ";
						$sql_syp = mysql_query($sq_syp);
					}

					//4. Merubah Status kantong (A,B,C,D,E,F,G.H.I)
					$kantong_0  = substr($sample, 0, -1);
					$nkantong_a	= $kantong_0 . 'A';
					$nkantong_b	= $kantong_0 . 'B';
					$nkantong_c	= $kantong_0 . 'C';
					$nkantong_d	= $kantong_0 . 'D';
					$nkantong_e	= $kantong_0 . 'E';
					$nkantong_f	= $kantong_0 . 'F';
					$nkantong_g	= $kantong_0 . 'G';
					//Kantong Sehat
					if ($aksi_konfirm == "1") {
						echo "-sehat-";
						//UPDATE KANTONG A-F
						$cek = "UPDATE stokkantong set Status='2',hasil='2',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong='$nkantong_a'";
						$upd_ktga = mysql_query("UPDATE stokkantong set Status='2',hasil='2',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong='$nkantong_a'");
						$upd_ktgb = mysql_query("UPDATE stokkantong set Status='2',hasil='2',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong='$nkantong_b'");
						$upd_ktgc = mysql_query("UPDATE stokkantong set Status='2',hasil='2',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong='$nkantong_c'");
						$upd_ktgd = mysql_query("UPDATE stokkantong set Status='2',hasil='2',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong='$nkantong_d'");
						$upd_ktge = mysql_query("UPDATE stokkantong set Status='2',hasil='2',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong='$nkantong_e'");
						$upd_ktgf = mysql_query("UPDATE stokkantong set Status='2',hasil='2',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong='$nkantong_f'");
						$upd_ktgg = mysql_query("UPDATE stokkantong set Status='2',hasil='2',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong='$nkantong_g'");
						//=======Audit Trial====================================================================================
						$log_mdl = 'IMLTD';
						$log_aksi = 'IMLTD Architech :' . $notrans . '; ID:' . $nkantong_a . '- Sehat';
						include('user_log.php');
						$log_aksi = 'IMLTD Architech :' . $notrans . '; ID:' . $nkantong_b . '- Sehat';
						include('user_log.php');
						$log_aksi = 'IMLTD Architech :' . $notrans . '; ID:' . $nkantong_c . '- Sehat';
						include('user_log.php');
						$log_aksi = 'IMLTD Architech :' . $notrans . '; ID:' . $nkantong_d . '- Sehat';
						include('user_log.php');
						$log_aksi = 'IMLTD Architech :' . $notrans . '; ID:' . $nkantong_e . '- Sehat';
						include('user_log.php');
						$log_aksi = 'IMLTD Architech :' . $notrans . '; ID:' . $nkantong_f . '- Sehat';
						include('user_log.php');
						$log_aksi = 'IMLTD Architech :' . $notrans . '; ID:' . $nkantong_g . '- Sehat';
						include('user_log.php');
						//======================================================================================================		
						echo "- Stokkantong $kantong_0 Sehat -";
						//UPDATE HTRANSAKSI
						//status_test 	hasil_hbsag 	 hasil_hcv 	hasil_hiv 	hasil_syp 	hasil_nat 	tglperiksa
						$sql_htrans = "UPDATE htransaksi SET `status_test`='0', `hasil_hbsag`='0', `hasil_hcv`='0', `hasil_hiv`='0', `hasil_syp`='0', `tglperiksa`='$today1' where NoKantong='$sample'";
						$sql_htransaksi = mysql_query($sql_htrans);
						echo " - HTrans - ";
					}

					//5. Update flag status_konfirmasi =1 di table imltd_arc_raw
					//------------------------------------------------------------------------------------------------------
					$sq_setstatus = "UPDATE `imltd_arc_raw` SET `status_konfirm`='1'
                               WHERE `id`='$idraw_b' OR `id`='$idraw_c' OR `id`='$idraw_i' OR `id`='$idraw_s'";
					$setstatus = mysql_query($sq_setstatus);
					if (setstatus) {
						echo "-RAW Sukses-";
					} else {
						echo "-RAW gagal-";
					}

					//6. aksi donor apabila kantong "cekal"
					//------------------------------------------------------------------------------------------------------
					if ($aksi_konfirm == "2") {
						echo "-Cekal-";
						//6.1 MENCEKAL PENDONOR BERDASARKAN INPUT NOMOR KANTONG
						$kantong	= $_POST[sample][$i];

						//Cari Kode Pendonornya
						$no_kantong	= substr($kantong, 0, -1);
						$no_kantong	= $no_kantong . 'A';
						$pendonor	= mysql_query("select kodePendonor as kode from htransaksi where NoKantong='$no_kantong'");
						$datapendonor = mysql_fetch_assoc($pendonor);
						$idpendonor	= $datapendonor['kode'];

						//6.2 update table pendonor
						$upd_donor_cekal = mysql_query("UPDATE pendonor SET Cekal='1' WHERE Kode='$idpendonor'");
						//=======Audit Trial====================================================================================
						$log_mdl = 'IMLTD';
						$log_aksi = 'IMLTD Architech :' . $notrans . '; Cekal pendonor:' . $idpendonor;
						include('user_log.php');
						//======================================================================================================		

						//6.3 Update Htransaksi
						$sq_htransaksi = mysql_query("UPDATE htransaksi SET `status_test`='0', `hasil_hbsag`='$ckl_b', `hasil_hcv`='$ckl_c', `hasil_hiv`='$ckl_i', `hasil_syp`='$ckl_s', `tglperiksa`='$today' where NoKantong='$sample'");
						echo "- Htrans -";
						//6.4 Update Stok kantong
						$tambah3s = mysql_query("UPDATE stokkantong set Status='7',hasil='4',sah='1',StatTempat='1', tglperiksa='$today1' where NoKantong like '$kantong_0%'");
						//=======Audit Trial====================================================================================
						$log_mdl = 'IMLTD';
						$log_aksi = 'IMLTD Architech :' . $notrans . '; Kantong Rusak Reaktif dimusnahkan:' . $kantong_0 . '(A,B,....';
						include('user_log.php');
						//======================================================================================================		
						echo "- stokkantong $kantong_0 Dimusnahkan-";
						//6.5 Musnahkan kantong ke ar_stokkantong
						/* $sq_ar_a="insert into ar_stokkantong (noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat,kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap, kadaluwarsa,tglpengolahan,mu,stokcheck)
						        select noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat, kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,stokcheck from stokkantong where noKantong='$nkantong_a'";
					$keluarkan_a=mysql_query($sq_ar_a);
                    if ($keluarkan_a){echo "- $nkantong_a Sukses Musnah-";}else{echo "- $nkantong_a GAGAL Musnah-";}
					$keluarkan_b=mysql_query("insert into ar_stokkantong (noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat,kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap, kadaluwarsa,tglpengolahan,mu,stokcheck)
						        select noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat, kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,stokcheck from stokkantong where noKantong='$nkantong_b'");
                    if ($keluarkan_b){echo "- $nkantong_b Sukses Musnah-";}else{echo "- $nkantong_b GAGAL Musnah-";}
					$keluarkan_c=mysql_query("insert into ar_stokkantong (noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat,kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap, kadaluwarsa,tglpengolahan,mu,stokcheck)
						        select noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat, kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,stokcheck from stokkantong where noKantong='$nkantong_c'");
                    if ($keluarkan_c){echo "- $nkantong_c Sukses Musnah-";}else{echo "- $nkantong_c GAGAL Musnah-";}
					$keluarkan_d=mysql_query("insert into ar_stokkantong (noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat,kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap, kadaluwarsa,tglpengolahan,mu,stokcheck)
						        select noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat, kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,stokcheck from stokkantong where noKantong='$nkantong_d'");
                    if ($keluarkan_d){echo "- $nkantong_d Sukses Musnah-";}else{echo "- $nkantong_d GAGAL Musnah-";}
					$keluarkan_e=mysql_query("insert into ar_stokkantong (noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat,kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap, kadaluwarsa,tglpengolahan,mu,stokcheck)
						        select noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat, kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,stokcheck from stokkantong where noKantong='$nkantong_e'");
                    if ($keluarkan_e){echo "- $nkantong_e Sukses Musnah-";}else{echo "- $nkantong_e GAGAL Musnah-";}
					$keluarkan_f=mysql_query("insert into ar_stokkantong (noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat,kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap, kadaluwarsa,tglpengolahan,mu,stokcheck)
						        select noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat, kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,stokcheck from stokkantong where noKantong='$nkantong_f'");
                    if ($keluarkan_f){echo "- $nkantong_f Sukses Musnah-";}else{echo "- $nkantong_f GAGAL Musnah-";}
                    $sq_ar_upd="update ar_stokkantong set alasan_buang='4', tgl_buang='$today1', user='$ptgKonfirmasi' where noKantong like '$kantong_0%'";
					$update=mysql_query($sq_ar_upd);
                    if ($update){echo "-update buang kantong $kantong_0% SUKSES";}else{echo "-update buang kantong $kantong_0% GAGAL";}*/

						//6.4 insert ke table cekal utk masing-masing parameter yang dicekal
						if ($upd_donor_cekal) {
							echo "- ID $idpendonor ($ckl_b $ckl_c $ckl_i $ckl_s) OK-";
							//6.3.1 insert ke table cekal per masing2 parameter reaktif

							if ($ckl_b == "1") {
								$tambah_cekal = mysql_query("INSERT INTO `cekal`(`kode_pendonor`, `nokantong`,`petugas`, `status`, `ket`, `notrans_imltd`)
							VALUES ('$idpendonor','$kantong', '$ptgKonfirmasi','1', '$ket_b', '$notrans')");
								if ($tambah_cekal) {
									echo "-Insert $idpendonor Cekal HBsAg, Sukses-";
								} else {
									echo "-Insert Cekal HBsAg $idpendonor Gagal-";
								}
							}
							if ($ckl_c == "1") {
								$tambah_cekal = mysql_query("INSERT INTO `cekal`(`kode_pendonor`,  `nokantong`,`petugas`, `status`, `ket`, `notrans_imltd`)
                            VALUES ('$idpendonor','$kantong', '$ptgKonfirmasi','2', '$ket_c', '$notrans')");
								if ($tambah_cekal) {
									echo "-Insert $idpendonor Cekal HCV Sukses-";
								} else {
									echo "-Insert Cekal HCV $idpendonor Gagal-";
								}
							}
							if ($ckl_i == "1") {
								$tambah_cekal = mysql_query("INSERT INTO `cekal`(`kode_pendonor`,  `nokantong`,`petugas`, `status`, `ket`, `notrans_imltd`)
                            VALUES ('$idpendonor','$kantong', '$ptgKonfirmasi','3', '$ket_i', '$notrans')");
								if ($tambah_cekal) {
									echo "-Insert $idpendonor Cekal HIV Sukses-";
								} else {
									echo "-Insert Cekal  HIV $idpendonor Gagal-";
								}
							}
							if ($ckl_s == "1") {
								$tambah_cekal = mysql_query("INSERT INTO `cekal`(`kode_pendonor`,  `nokantong`,`petugas`, `status`, `ket`, `notrans_imltd`)
                            VALUES ('$idpendonor','$kantong', '$ptgKonfirmasi','4', '$ket_s', '$notrans')");
								if ($tambah_cekal) {
									echo "-Insert $idpendonor Cekal Sypilis Sukses-";
								} else {
									echo "-Insert Cekal Sypilis $idpendonor Gagal-";
								}
							}
							echo "<br>";
						} else {
							echo "-Update Cekal pendonor $idpendonor GAGAL-";
						}
					} else {
						echo "<br>";
					}
				} else {
					echo " - DITUNDA<br>";
				}
			}
			//1. update reagen dilakukan disini setelah diketahui real pemeriksaan yang dikonfirmasi
			echo "PENGGUNAAN REAGAN TERGANTUNG JUMLAH SAMPLE YANG DIKONFIRMASI (TIDAK DITUNDA)<br>";
			echo "Penggunaan Reagan Hbsag : $jmlreag_b ; Reagan HCV : $jmlreag_c ;Reagan HIV : $jmlreag_i ;Reagan Trep : $jmlreag_s<br>";

			$sq_rb = mysql_query("update reagen set jumTest=jumTest-$jmlreag_b where kode='$reag1_kode'");
			//echo "update reagen set jumTest=jumTest-$jmlreag_b where kode='$koder_b'<br>";
			$sq_rc = mysql_query("update reagen set jumTest=jumTest-$jmlreag_c where kode='$reag2_kode'");
			//echo "update reagen set jumTest=jumTest-$jmlreag_b where kode='$koder_c'<br>";
			$sq_ri = mysql_query("update reagen set jumTest=jumTest-$jmlreag_i where kode='$reag3_kode'");
			//echo "update reagen set jumTest=jumTest-$jmlreag_b where kode='$koder_i'<br>";
			$sq_rs = mysql_query("update reagen set jumTest=jumTest-$jmlreag_s where kode='$reag4_kode'");
			//echo "update reagen set jumTest=jumTest-$jmlreag_b where kode='$koder_s'<br>";
			echo "<meta http-equiv='refresh' content='2;url=architec/imltd_rpt_konfirm1?notrans=$notrans'>";
		}
	}

	$g_inst 	= $_GET['ins'];
	$g_serial 	= $_GET['sn'];
	$g_tgl	 	= $_GET['dt'];
	$g_user 	= $_GET['usr'];
	$Sq = mysql_query("SELECT `id`, `instr`, `instr_v`, `scc_serial`, `interface_sn`, date(`trans_time`) as trans_time, `id_tes`,
                `user`, date(`run_time`) as run_time, `arc_serial`,  `keterangan`, `first_name`, `last_name`,`carrier`,`position` 
				FROM `imltd_arc_raw` WHERE `status_konfirm`='0' AND `instr`='$g_inst' AND DATE(`run_time`)='$g_tgl'
				AND `arc_serial`='$g_serial' AND `user`='$g_user' 
				Group by `id_tes`, `carrier`,`position`");
	$row = mysql_fetch_assoc($Sq);
	?>
	<a name="atas" id="atas"></a>
	<table border=0 cellpadding="5" cellspacing="5" width="100%">
		<tr>
			<td align="left" style="background-color: #ffffff">
				<font size=5 color="blue"><b>KONFIRMASI HASIL PEMERIKSAAN ABBOTT ARCHITECT <i>i</i>2000SR</b></font>
			</td>
			<td align="right" style="background-color: #ffffff"><a href="#bawah" class="swn_button_blue">Ke bawah</a></td>
		</tr>
	</table>
	<form name="manual_input" align="left" method="post" action="<? echo $PHPSELF ?>">
		<table class="list" border=1 cellpadding="2" cellspacing="2" style="border-collapse:collapse" width="100%">
			<tr class="field">
				<td colspan=4>Instrument</td>
				<td colspan=4>Serial</td>
				<td colspan=4>Tanggal Periksa</td>
				<td colspan=4>User</td>
			</tr>
			<tr class="record">
				<td colspan=4><?= $row['instr']; ?> <input type=hidden name=instrument value="<?= $row['instr'] ?>"></td>
				<td colspan=4><?= $row['arc_serial']; ?> <input type=hidden name=arc_serial value="<?= $row['arc_serial'] ?>"></td>
				<td colspan=4><?= $row['run_time']; ?> <input type=hidden name=trans_time value="<?= $row['trans_time'] ?>"></td>
				<td colspan=4><?= $row['user'];
								$userarc = $row['user']; ?> <input type=hidden name=user value="<?= $row['user'] ?>"></td>
				<input type=hidden name=instr_v value="<?= $row['instr_v'] ?>"></td>
				<input type=hidden name=interface_sn value="<?= $row['interface_sn'] ?>"></td>
				<input type=hidden name=scc_serial value="<?= $row['scc_serial'] ?>"></td>
			</tr>
			<tr class="field">
				<td colspan=4>Reagen HBsAg</td>
				<td colspan=4>Reagen HCV</td>
				<td colspan=4>Reagen HIV</td>
				<td colspan=4>Reagen Syphilis</td>
			</tr>
			<tr class="field">
				<td align="left" colspan=4>
					<select name="reagen1" id="reagen1" onChange="show1(1)">
						<option value="-">-</option>
						<?
						$jreagen1 = mysql_query("select * from reagen where Nama like '%Architect%Hbsag%' and aktif='1' and jumTest>0");
						while ($jreagen11 = mysql_fetch_assoc($jreagen1)) { ?>
							<option value="<?= $jreagen11[Nama] ?>*<?= $jreagen11[noLot] ?>*<?= $jreagen11[kode] ?>*<?= $jreagen11[jumTest] ?>*<?= $jreagen11[tglKad] ?>">
								<?= $jreagen11[Nama] ?>-<?= $jreagen11[noLot] ?>-<?= $jreagen11[jumTest] ?> T
							</option><?
									} ?>
					</select>
				</td>
				<td align="left" colspan=4>
					<select name="reagen2" id="reagen2" onChange="show2(2)">
						<option value="-">-</option>
						<?
						$jreagen1 = mysql_query("select * from reagen where Nama like '%Architect%hcv%' and aktif='1' and jumTest>0");
						while ($jreagen11 = mysql_fetch_assoc($jreagen1)) { ?>
							<option value="<?= $jreagen11[Nama] ?>*<?= $jreagen11[noLot] ?>*<?= $jreagen11[kode] ?>*<?= $jreagen11[jumTest] ?>*<?= $jreagen11[tglKad] ?>">
								<?= $jreagen11[Nama] ?>-<?= $jreagen11[noLot] ?>-<?= $jreagen11[jumTest] ?> T
							</option><?
									} ?>
					</select>
				</td>
				<td align="left" colspan=4>
					<select name="reagen3" id="reagen3" onChange="show3(3)">
						<option value="-">-</option>
						<?
						$jreagen1 = mysql_query("select * from reagen where Nama like '%Architect%HIV%' and aktif='1' and jumTest>0");
						while ($jreagen11 = mysql_fetch_assoc($jreagen1)) { ?>
							<option value="<?= $jreagen11[Nama] ?>*<?= $jreagen11[noLot] ?>*<?= $jreagen11[kode] ?>*<?= $jreagen11[jumTest] ?>*<?= $jreagen11[tglKad] ?>">
								<?= $jreagen11[Nama] ?>-<?= $jreagen11[noLot] ?>-<?= $jreagen11[jumTest] ?> T
							</option><?
									} ?>
					</select>
				</td>
				<td align="left" colspan=4>
					<select name="reagen4" id="reagen4" onChange="show4(4)">
						<option value="-">-</option>
						<?
						$jreagen1 = mysql_query("select * from reagen where Nama like '%Architect%Syphilis%' and aktif='1' and jumTest>0");
						while ($jreagen11 = mysql_fetch_assoc($jreagen1)) { ?>
							<option value="<?= $jreagen11[Nama] ?>*<?= $jreagen11[noLot] ?>*<?= $jreagen11[kode] ?>*<?= $jreagen11[jumTest] ?>*<?= $jreagen11[tglKad] ?>">
								<?= $jreagen11[Nama] ?>-<?= $jreagen11[noLot] ?>-<?= $jreagen11[jumTest] ?> T
							</option><?
									} ?>
					</select>
				</td>
			</tr>
			<tr class="record">
				<td>
					<div id="nama1"></div>
				</td>
				<td>
					<div id="kode1"></div>
				</td>
				<td>
					<div id="nolot1"></div>
				</td>
				<td>
					<div id="sisa_test1"></div>
				</td>
				<td>
					<div id="nama2"></div>
				</td>
				<td>
					<div id="kode2"></div>
				</td>
				<td>
					<div id="nolot2"></div>
				</td>
				<td>
					<div id="sisa_test2"></div>
				</td>
				<td>
					<div id="nama3"></div>
				</td>
				<td>
					<div id="kode3"></div>
				</td>
				<td>
					<div id="nolot3"></div>
				</td>
				<td>
					<div id="sisa_test3"></div>
				</td>
				<td>
					<div id="nama4"></div>
				</td>
				<td>
					<div id="kode4"></div>
				</td>
				<td>
					<div id="nolot4"></div>
				</td>
				<td>
					<div id="sisa_test4"></div>
				</td>
			</tr>
		</table>
		<table class="list" border=1 cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
			<tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
				<td rowspan=2 align="center">No</td>
				<td rowspan=2 align="center">SampleID</td>
				<td colspan=4 align="center">HBsAg</td>
				<td colspan=4 align="center">HCV</td>
				<td colspan=4 align="center">HIV</td>
				<td colspan=4 align="center">Syphilis</td>
				<td rowspan=2 align="center">Kantong</td>
				<td rowspan=2 align="center">Konfirm</td>
			</tr>
			<tr style="background-color:#FF6346; font-size:14px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
				<td align="center">OD</td>
				<td align="center">Lot</td>
				<td align="center">Hasil</td>
				<td align="center">Ket</td>
				<td align="center">OD</td>
				<td align="center">Lot</td>
				<td align="center">Hasil</td>
				<td align="center">Ket</td>
				<td align="center">OD</td>
				<td align="center">Lot</td>
				<td align="center">Hasil</td>
				<td align="center">Ket</td>
				<td align="center">OD</td>
				<td align="center">Lot</td>
				<td align="center">Hasil</td>
				<td align="center">Ket</td>
			</tr>
			<?
			$no				= 0;
			$valid			= 0;
			$jumlahproses	= 0;
			$jml_test_b		= 0;
			$jml_test_c		= 0;
			$jml_test_i		= 0;
			$jml_test_s		= 0;

			$sq_sample = mysql_query("SELECT `id`, `instr`, `instr_v`, `scc_serial`, `interface_sn`, date(`trans_time`) as trans_time, `id_tes`,
                `user`, `run_time`, `arc_serial`,  `keterangan`, `first_name`, `last_name`,`carrier`,`position` 
				FROM `imltd_arc_raw` WHERE `status_konfirm`='0' AND `instr`='$g_inst' AND DATE(`run_time`)='$g_tgl'
				AND `arc_serial`='$g_serial' AND `user`='$g_user' 
				Group by `id_tes`, `carrier`,`position`
				order by `id`");
			while ($data = mysql_fetch_assoc($sq_sample)) {
				$no++;

				$umur = "0";
				$jenisdonor = "0";
				$donorbaru = "0";
				$cek_transaksi = mysql_query("SELECT umur, donorbaru, JenisDonor FROM htransaksi WHERE NoKantong='$data[id_tes]'");
				$q_trx      = mysql_fetch_assoc($cek_transaksi);
				$umur       = $q_trx['umur'];
				$jenisdonor = $q_trx['JenisDonor'];
				$donorbaru  = $q_trx['donorbaru'];
				$cek_ktg = mysql_query("select Status, sah, StatTempat, stat2 from stokkantong where noKantong='$data[id_tes]'");
				$c_ktg = mysql_fetch_assoc($cek_ktg);
				$status_ktg = $c_ktg['Status'];
				$kantong_sah = $c_ktg['sah'];
				switch ($status_ktg) {
					case '0':
						$statuskantong = 'Kosong(' . $status_ktg . ')';
						if ($c_ktg[StatTempat] == NULL) $statuskantong = 'Kosong-Logistik(' . $status_ktg . ')';
						if ($c_ktg[StatTempat] == '0')  $statuskantong = 'Kosong-Logistik (' . $status_ktg . ')';
						if ($c_ktg[StatTempat] == '1')  $statuskantong = 'Kosong-Aftap(' . $status_ktg . ')';
						break;
					case '1':
						if ($c_ktg['sah'] == "1") {
							$statuskantong = 'Karantina(' . $status_ktg . ')';
						} else {
							$statuskantong = 'Belum disahkan(' . $status_ktg . ')';
						}
						break;
					case '2':
						$statuskantong = 'Sehat(' . $status_ktg . ')';
						if (substr($c_ktg[stat2], 0, 1) == 'b') $tempat = " (BDRS)";
						break;
					case '3':
						$statuskantong = 'Keluar(' . $status_ktg . ')';
						break;
					case '4':
						$statuskantong = 'Rusak(' . $status_ktg . ')';
						break;
					case '5':
						$statuskantong = 'Rusak-Gagal(' . $status_ktg . ')';
						break;
					case '6':
						$statuskantong = 'Dimusnahkan(' . $status_ktg . ')';
						break;
					default:
						$statuskantong = '-';
				}
				//Clear Var
				$cekal = '0';
				$id_raw_b = '0';
				$id_raw_c = '0';
				$id_raw_i = '0';
				$id_raw_s = '0';
				$hbsag_value = '';
				$hbsag_lotr = '';
				$hbsag_label = '';
				$hbsag_hasil = '';
				$hbsag_v = '';
				$sketperiksa1 = '';
				$hbsag_snr = '';
				$rtime_b = '';;
				$hcv_value = '';
				$hcv_lotr = '';
				$hcv_label = '';
				$hcv_hasil = '';
				$hcv_v = '';
				$sketperiksa2 = '';
				$hcv_snr = '';
				$rtime_c = '';
				$hiv_value = '';
				$hiv_lotr = '';
				$hiv_label = '';
				$hiv_hasil = '';
				$hiv_v = '';
				$sketperiksa3 = '';
				$hiv_snr = '';
				$rtime_i = '';
				$trep_value = '';
				$trep_lotr = '';
				$trep_label = '';
				$trep_hasil = '';
				$trep_v = '';
				$sketperiksa4 = '';
				$trep_snr = '';
				$rtime_s = '';

				if (($status_ktg == '1') and ($kantong_sah == '1')) {
					$valid++;
				}
				$sql = "SELECT `id`, `instr` , `id_tes` , `parameter` , `lot_reag` ,`sn_reag` , `abs` , `unit` , `range` , `user` , `run_time` ,
                  `arc_serial` , `hasil` , `status_konfirm` , `keterangan` , `first_name` , `last_name`, `carrier`, `position`
                  FROM `imltd_arc_raw` 
                  WHERE `id_tes` = '$data[id_tes]' AND `status_konfirm` = '0'  AND `carrier`='$data[carrier]' and `position`='$data[position]'     
                  ORDER BY `id_tes` , `parameter`,`carrier`, `position`";
				$row = mysql_query($sql);
				while ($row2 = mysql_fetch_assoc($row)) {

					switch ($row2['parameter']) {
						case 'HBsAgQ2':
							$hbsag_value = $row2['abs'];
							$hbsag_lotr  = $row2['lot_reag'];
							$hbsag_label = $row2['hasil'];
							$hbsag_snr   = $row2['sn_reag'];
							if ($row2['hasil'] == 'Nonreactive') {
								$hbsag_hasil = '0';
								$hbsag_v = 'NR';
							} elseif ($row2['hasil'] == 'Grayzone') {
								$hbsag_hasil = '2';
								$hbsag_v = 'GZ';
								$cekal = '1';
							} elseif ($row2['hasil'] == 'Reactive') {
								$hbsag_hasil = '1';
								$hbsag_v = 'R';
								$cekal = '1';
							} else {
								$hbsag_label == '??';
								$hbsag_hasil = '9';
								$hbsag_v = '?';
							}
							$sketperiksa1 = substr($row2['last_name'], 0, 5);
							$id_raw_b = $row2['id'];
							$rtime_b = $row2['run_time'];
							$jml_test_b++;
							break;
						case 'SyphilisTP':
						case 'Syphilis':
							$trep_value = $row2['abs'];
							$trep_label = $row2['hasil'];
							$trep_lotr = $row2['lot_reag'];
							$trep_snr = $row2['sn_reag'];
							if ($row2['hasil'] == "Nonreactive") {
								$trep_hasil = '0';
								$trep_v = 'NR';
							} elseif ($row2['hasil'] == "Grayzone") {
								$trep_hasil = '2';
								$trep_v = 'GZ';
								$cekal = '4';
							} elseif ($row2['hasil'] == "Reactive") {
								$trep_hasil = '1';
								$trep_v = 'R';
								$cekal = '4';
							} else {
								$trep_label == '??';
								$trep_hasil = '9';
								$trep_v = '?';
							}
							$sketperiksa4 = substr($row2['last_name'], 0, 5);
							$id_raw_s = $row2['id'];
							$rtime_s = $row2['run_time'];
							$jml_test_s++;
							break;
						case 'anti-HCVII':
							$hcv_value = $row2['abs'];
							$hcv_label = $row2['hasil'];
							$hcv_lotr = $row2['lot_reag'];
							$hcv_snr = $row2['sn_reag'];
							if ($row2['hasil'] == 'Nonreactive') {
								$hcv_hasil = '0';
								$hcv_v = 'NR';
							} elseif ($row2['hasil'] == "Grayzone") {
								$hcv_hasil = '2';
								$hcv_v = 'GZ';
								$cekal = '2';
							} elseif ($row2['hasil'] == "Reactive") {
								$hcv_hasil = '1';
								$hcv_v = 'R';
								$cekal = '2';
							} else {
								$hcv_label == '?';
								$hcv_hasil = '9';
								$hcv_v = '?';
							}
							$sketperiksa2 = substr($row2['last_name'], 0, 5);
							$id_raw_c = $row2['id'];
							$rtime_c = $row2['run_time'];
							$jml_test_c++;
							break;
						case '_HIV Ag/Ab':
							$hiv_value = $row2['abs'];
							$hiv_label = $row2['hasil'];
							$hiv_lotr = $row2['lot_reag'];
							$hiv_snr = $row2['sn_reag'];
							if ($row2['hasil'] == 'Nonreactive') {
								$hiv_hasil = '0';
								$hiv_v = 'NR';
							} elseif ($row2['hasil'] == "Grayzone") {;
								$hiv_hasil = '2';
								$hiv_v = 'GZ';
								$cekal = '3';
							} elseif ($row2['hasil'] == "Reactive") {;
								$hiv_hasil = '1';
								$hiv_v = 'R';
								$cekal = '3';
							} else {
								$hiv_label == '??';
								$hiv_hasil = '9';
								$hiv_v = '?';
							}
							$sketperiksa3 = substr($row2['last_name'], 0, 5);
							$id_raw_i = $row2['id'];
							$rtime_i = $row2['run_time'];
							$jml_test_i++;
							break;
					}
				}
				$nr_all = "0";
				if ((strlen($hbsag_label) > 0) and (strlen($hcv_label) > 0) and (strlen($hiv_label) > 0) and (strlen($trep_label) > 0)) {
					$all_param = '1';
				} else {
					$all_param = '0';
				}
			?>
				<tr style="font-size:13px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">

					<td align='right'> <input type="hidden" name=no[] value=<?= $no ?>> <?= $no . '.' ?></td>

					<input type=hidden name=sample[] value="<?= $data['id_tes'] ?>">
					<input type=hidden name=carrier[] value="<?= $data['carrier'] ?>">
					<input type=hidden name=position[] value="<?= $data['position'] ?>">

					<input type=hidden name=run_time_b[] value="<?= $rtime_b ?>">
					<input type=hidden name=run_time_c[] value="<?= $rtime_c ?>">
					<input type=hidden name=run_time_i[] value="<?= $rtime_i ?>">
					<input type=hidden name=run_time_s[] value="<?= $rtime_s ?>">

					<input type=hidden name=id_b[] value="<?= $id_raw_b ?>">
					<input type=hidden name=id_c[] value="<?= $id_raw_c ?>">
					<input type=hidden name=id_i[] value="<?= $id_raw_i ?>">
					<input type=hidden name=id_s[] value="<?= $id_raw_s ?>">

					<input type=hidden name=hbsag_snr[] value="<?= $hbsag_snr ?>">
					<input type=hidden name=hcv_snr[] value="<?= $hcv_snr ?>">
					<input type=hidden name=hiv_snr[] value="<?= $hiv_snr ?>">
					<input type=hidden name=syp_snr[] value="<?= $trep_snr ?>">

					<td align='left'> <input type="hidden" name=kantong[] value=<?= $data[id_tes] ?>> <?= $data['id_tes']; ?></td>

					<td align='right'> <input type="hidden" name=hbsag_od[] value=<?= $hbsag_value ?>> <?= $hbsag_value; ?></td>
					<td align='left'> <input type="hidden" name=hbsag_lotr[] value=<?= $hbsag_lotr ?>> <?= $hbsag_lotr; ?></td>
					<? if ($hbsag_label == "Reactive" or $hbsag_label == "Grayzone") { ?>
						<td align='center'> <input type="hidden" name=hbsag_result[] value=<?= $hbsag_hasil ?>>
							<font color="red"><b> <?= $hbsag_v; ?> </b></font>
						</td>
						<? $nr_all = "1"; ?><input type="hidden" name=cekal_hbsag[] value="1"><?
																							} else { ?>
						<td align='center'> <input type="hidden" name=hbsag_result[] value=<?= $hbsag_hasil ?>>
							<font color="black"> <?= $hbsag_v; ?> </font>
						</td>
						<input type="hidden" name=cekal_hbsag[] value="0">
					<? } ?>
					<td align='left'> <input type="hidden" name=hbsag_ket[] value=<?= $sketperiksa1 ?>> <?= $sketperiksa1; ?></td>

					<td align='right'> <input type="hidden" name=hcv_od[] value=<?= $hcv_value ?>> <?= $hcv_value; ?></td>
					<td align='left'> <input type="hidden" name=hcv_lotr[] value=<?= $hcv_lotr ?>> <?= $hcv_lotr; ?></td>

					<? if ($hcv_label == "Reactive" or $hcv_label == "Grayzone") { ?>
						<td align='center'> <input type="hidden" name=hcv_result[] value=<?= $hcv_hasil ?>>
							<font color="red"><b> <?= $hcv_v; ?> </b></font>
						</td>
						<? $nr_all = "2"; ?><input type="hidden" name=cekal_hcv[] value="1"><?
																						} else { ?>
						<td align='center'> <input type="hidden" name=hcv_result[] value=<?= $hcv_hasil ?>>
							<font color="black"> <?= $hcv_v; ?> </font>
						</td>
						<input type="hidden" name=cekal_hcv[] value="0">
					<? } ?>
					<td align='left'> <input type="hidden" name=hcv_ket[] value=<?= $sketperiksa2 ?>> <?= $sketperiksa2; ?></td>

					<td align='right'> <input type="hidden" name=hiv_od[] value=<?= $hiv_value ?>> <?= $hiv_value; ?></td>
					<td align='left'> <input type="hidden" name=hiv_lotr[] value=<?= $hiv_lotr ?>> <?= $hiv_lotr; ?></td>

					<? if ($hiv_label == "Reactive" or $hiv_label == "Grayzone") { ?>
						<td align='center'> <input type="hidden" name=hiv_result[] value=<?= $hiv_hasil ?>>
							<font color="red"><b> <?= $hiv_v; ?> </b></font>
						</td>
						<? $nr_all = "3"; ?><input type="hidden" name=cekal_hiv[] value="1"><?
																						} else { ?>
						<td align='center'> <input type="hidden" name=hiv_result[] value=<?= $hiv_hasil ?>>
							<font color="black"> <?= $hiv_v; ?> </font>
						</td>
						<input type="hidden" name=cekal_hiv[] value="0">
					<? } ?>
					<td align='left'> <input type="hidden" name=hiv_ket[] value=<?= $sketperiksa3 ?>> <?= $sketperiksa3; ?></td>

					<td align='right'> <input type="hidden" name=syp_od[] value=<?= $trep_value ?>> <?= $trep_value; ?></td>
					<td align='left'> <input type="hidden" name=syp_lotr[] value=<?= $trep_lotr ?>> <?= $trep_lotr; ?></td>

					<? if ($trep_label == "Reactive" or $trep_label == "Grayzone") { ?>
						<td align='center'> <input type="hidden" name=syp_result[] value=<?= $trep_hasil ?>>
							<font color="red"><b> <?= $trep_v; ?> </b></font>
						</td>
						<? $nr_all = "4"; ?><input type="hidden" name=cekal_syp[] value="1"><?
																						} else { ?>
						<td align='center'> <input type="hidden" name=syp_result[] value=<?= $trep_hasil ?>>
							<font color="black"> <?= $trep_v; ?> </font>
						</td>
						<input type="hidden" name=cekal_syp[] value="0">
					<? } ?>
					<td align='left'> <input type="hidden" name=syp_ket[] value=<?= $sketperiksa4 ?>> <?= $sketperiksa4; ?></td>
					<td align='left'> <input type="hidden" name=statuskantong[] value=<?= $status_ktg ?>> <?= $statuskantong ?>
						<input type="hidden" name=umur[] value=<?= $umur ?>>
						<input type="hidden" name=jenis_donor[] value=<?= $jenisdonor ?>>
						<input type="hidden" name=donorbaru[] value=<?= $donorbaru ?>>
					</td>

					<td align='left'>
						<?
						$sel0 = "";
						$sel1 = "";
						$sel2 = "";
						$sel3 = "";

						if ($nr_all !== "0") {
							$sel2 = "selected";
						}
						if (($status_ktg == "1") and ($c_ktg['sah'] == "1") and ($nr_all == "0")) {
							$sel1 = "selected";
						}
						//if (($status_ktg=="1") and ($c_ktg['sah']=="1") and ($nr_all!=="0")){$sel2="selected";}
						if ($status_ktg == "0") {
							$sel3 = "selected";
						}
						if ($cekal !== "0") {
							$sel2 = "selected";
						}
						?>
						<select name="aksi[]">
							<? if (($nr_all == "0") and ($all_param == "1")) {
								echo "<option value='1' $sel1>Sehat</option>";
							} ?>
							<option value="0" <?= $sel0 ?>>-</option>
							<option value="1" <?= $sel1 ?>>Sehat</option>
							<option value="2" <?= $sel2 ?>>Cekal</option>
							<option value="3" <?= $sel3 ?>>Tunda</option>
						</select>
					</td>

				</tr>
			<?
			} ?>
			<tr class="field">
				<td colspan="2" align="left">Dikonfirmasi oleh</td>
				<input type="hidden" name="konfirmasi" value="<?= $namauser ?>">
				<td colspan="8" align="left"> <? echo $namalengkap; ?></td>
				<td colspan="10" rowspan="3" align="left">
					<b>Catatan :</b>
					<ol>
						<li>Ket kolom <b>Hasil</b>:<u> <b>NR</b>:NonReactive, <b>GZ</b>:Grayzone, <b>R</b>:Reactive</u></li>
						<li>Reagen harus dipilih sesuai parameter, jumlah dan nomor lot pemeriksaan</li>
						<li>Kantong darah yang diproses sesuai dengan aksi konfirmasi pada pilihan kolom paling kanan</li>
					</ol>
				</td>
			</tr>
			<tr class="field">
				<td colspan="2" align="left">Operator Architect</td>
				<td colspan="8" align="left">
					<select name="operator"> <?
												$user1 = "select * from user where level like '%imltd%' order by nama_lengkap";
												$do1 = mysql_query($user1);
												while ($data1 = mysql_fetch_assoc($do1)) {
													if (strtoupper($data1[id_user]) == strtoupper($userarc)) {
														$select = " selected";
													} else {
														$select = "";
													} ?>
							<option value="<?= $data1[id_user] ?>" <?= $select ?>><?= $data1[nama_lengkap] ?></option><?
																													} ?>
					</select>
				</td>
			</tr>
			<tr class="field">
				<td colspan="2" align="left">Disahkan Oleh</td>
				<td colspan="8" align="left">
					<select name="disahkan_oleh"> <?
													$user = "select * from user  where (level like '%laboratorium%') or (level like '%pimpinan%') or (level like '%konseling%') or (level like '%imltd%') order by nama_lengkap";
													$do = mysql_query($user);
													while ($data = mysql_fetch_assoc($do)) {
														$select1 = ""; ?>
							<option value="<?= $data[id_user] ?>" <?= $select1 ?>><?= $data[nama_lengkap] ?></option>
						<? } ?>
					</select>
				</td>
			</tr>
		</table>
		<input type="hidden" name="jml_test_b" value=<?= $jml_test_b ?>>
		<input type="hidden" name="jml_test_c" value=<?= $jml_test_c ?>>
		<input type="hidden" name="jml_test_i" value=<?= $jml_test_i ?>>
		<input type="hidden" name="jml_test_s" value=<?= $jml_test_s ?>>
		<a href="#atas" class="swn_button_blue">Ke Atas</a><a name="bawah" id="bawah">
			<a href="pmiimltd.php?module=import_arc2000_to_konfirm" class="swn_button_blue">Kembali ke list data</a>
			<a href="pmiimltd.php?module=import_arc2000" class="swn_button_blue">Kembali ke Awal</a>
			<?
			if ($no !== 0) { ?>
				<input type="submit" name="Button" value="Proses Konfirmasi" title="Proses kantong" class="swn_button_red">
			<? } ?>
	</form>

</body>

</html>