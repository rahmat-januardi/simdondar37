<?php
include('clogin.php');
include('config/db_connect.php');
require_once("modul/background_process.php");

$today = date("Y-m-d");
$today3 = date('Y-m-d H:i:s');
$tgl1 = date("d", strtotime($today));
$bln1 = date("n", strtotime($today));
$thn1 = date("Y", strtotime($today));
$bulan = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
$bln11 = $bulan[$bln1];
$udd = mysql_fetch_assoc(mysql_query("select id,nama,alamat,telp,daerah from utd where down='1' and aktif='1'"));

$namauser = $_SESSION['nama_lengkap'];
$tglSerahTerima = $_POST['tglSerahTerima'];
$v_petugas2 = $_POST['petugas2'];

if (isset($_POST['submit'])) {

	for ($i = 0; $i < count($_POST['nk']); $i++) {
		$nkantong = $_POST['nk'][$i];
		$suhu     = $_POST['suhu_terima'][$i];
		$catatan  = $_POST['catatan'][$i];

		$pd = mysql_fetch_assoc(mysql_query("select * from stokkantong where noKantong='$nkantong'"));
		$asalUDD = $pd['AsalUTD'] == null ? $udd[id] : $pd['AsalUTD'];
		$jns_asal = $udd['id'] == $asalUDD ? '0' : '1';
		$bdrs_sql1 = "insert into registrasi_qc (nokantong,produk,volume,goldarah,rhesus,tgl,tglaftap,kadaluwarsa,tgl_pengolahan,petugas_terima,petugas_serah,asal_utd, suhu, jns_asal, catatan) values ('$pd[noKantong]','$pd[produk]','$pd[volumeasal]','$pd[gol_darah]','$pd[RhesusDrh]','$tglSerahTerima','$pd[tgl_Aftap]','$pd[kadaluwarsa]','$pd[tglpengolahan]','$namauser','$v_petugas2','$asalUDD', '$suhu', '$jns_asal', '$catatan')";
		// echo $bdrs_sql1;
		$bdrs_sql = mysql_query($bdrs_sql1);
		if ($bdrs_sql) {
			$tambah = mysql_query("update stokkantong set statQC='0' where noKantong='$nkantong'");
			$histori = mysql_query("insert into histori (`username`,`waktu`,`action`,`level_editor`,`nokantong`) values ('$namauser','$today3','Registrasi QC produk komponen darah','QC','$nkantong')");

			$log_mdl = 'UJI MUTU';
			$log_aksi = 'Registrasi QC produk komponen darah: ' . $nkantong . ' - ' . $pd['produk'];
			include_once __DIR__ . '/user_log.php';
		}
	}

	if ($bdrs_sql) {
		echo "<div class='notif sukses'>Data telah berhasil dimasukkan.</div>";
	} else {
		echo "<div class='notif gagal'>Terjadi kesalahan saat menyimpan data. Silakan coba lagi.</div>";
	}
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <script src="js/jquery-latest.js" type="text/javascript"></script>
    <script language="javascript" src="js/alert.js" type="text/javascript"></script>
    <script type="text/javascript" src="js/tgl_rekap.js"></script>
    <script type="text/javascript" src="js/registrasi_qc1.js?v=<?= time() ?>"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>

    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
    <link type="text/css" href="css/terima_qc.css" rel="stylesheet" />

    <script type="text/javascript">
    jQuery(document).ready(function() {
        $('#instansi').autocomplete({
            source: 'modul/suggest_bdrs.php',
            minLength: 2
        });
    });
    </script>
</head>

<body onload="document.getElementById('nokantong').focus();">
    <div class="page-wrap">
        <div class="page-card">
            <div class="page-header">
                <div class="header-flex">
                    <div>
                        <h1>Penerimaan Sample QC</h1>
                        <p>Silakan isi nomor kantong, cek data, lalu simpan hasil penerimaan QC.</p>
                    </div>

                    <div>
                        <a href="pmiqc.php?module=register_qc_luar" class="swn_button_green">
                            Terima Sampel Dari UTD Lain
                        </a>
                    </div>
                </div>
            </div>

            <div class="page-body">
                <form name="bdrs" id="bdrs" onsubmit="return ok()" method="POST" action="<?= $PHPSELF ?>">
                    <div class="form-grid">
                        <div class="form-box">
                            <label for="datepicker">Tanggal Serah Terima</label>
                            <input type="text" name="tglSerahTerima" id="datepicker" value="<?= $today ?>" size="10">
                        </div>

                        <div class="form-box">
                            <label for="nokantong">No. Kantong</label>
                            <input type="text" name="nokantong" id="nokantong" onkeydown="chang(event,this);"
                                onchange="addRow('dtable')" autofocus>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table id="dtable">
                            <thead>
                                <tr>
                                    <th>Cek List</th>
                                    <th>No.</th>
                                    <th>No.Kantong</th>
                                    <th>Gol. Darah</th>
                                    <th>Rhesus Darah</th>
                                    <th>Komponen Darah</th>
                                    <th>Tgl Aftap</th>
                                    <th>Tgl Kadaluwarsa</th>
                                    <th>Tgl Pengolahan</th>
                                    <th>Jenis Kantong</th>
                                    <th>Volume Asal</th>
                                    <th>Suhu saat diterima</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <div class="info-grid">
                        <div class="info-card">
                            <div class="label">Petugas Yang Menyerahkan</div>
                            <div class="select-wrapper">
                                <select id="petugas2" name="petugas2" class="select-modern" required>
                                    <option value="">-- Pilih Petugas --</option>
                                    <?
									$user1 = "select * from user where level like '%komponen%' order by nama_lengkap ASC";
									$do1 = mysql_query($user1);
									while ($data1 = mysql_fetch_assoc($do1)) {
										if ($data1[id_user] == $data_combo[petugas1]) {
											$select = " selected";
										} else {
											$select = "";
										} ?>
                                    <option value="<?= $data1[nama_lengkap] ?>" <?= $select ?>>
                                        <?= $data1[nama_lengkap] ?>
                                    </option>
                                    <?
									} ?>
                                </select>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="label">Petugas Yang Menerima</div>
                            <div class="value"><?= $namauser; ?></div>
                        </div>
                    </div>

                    <div class="actions">
                        <div class="actions-right">
                            <input type="submit" value="Simpan Data" name="submit" class="swn_button_blue">
                            <input type="button" value="Hapus Baris" onclick="deleteRow('dtable')"
                                class="swn_button_red">
                        </div>
                    </div>

                    <div class="alert" id="alert" style="margin-top:20px;">
                        <div id="kantong_tdk_sesuai" title="Kantong tidak sesuai..!">
                            <p>Silahkan cek kembali kantong yang anda masukkan, atau masukkan kantong lain</p>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
</body>

</html>