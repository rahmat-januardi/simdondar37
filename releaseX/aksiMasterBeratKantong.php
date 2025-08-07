<?php
// Debug mode (aktifkan saat pengembangan)
// ini_set('display_errors', 1); // untuk development.
ini_set('display_errors', 0); // untuk production.
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once "../config/dbi_connect.php";
$idudd = "3509";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aksi = $_POST['aksi'] ? $_POST['aksi'] : '';

    // file_put_contents(
    //     "log_inputan_form.txt",
    //     "[" . date("Y-m-d H:i:s") . "] " . json_encode($_POST, JSON_PRETTY_PRINT) . PHP_EOL,
    //     FILE_APPEND
    // );

    if ($aksi == "tambah") {

        $merk = $_POST['merk'] ? $_POST['merk'] : null;
        $jenis = $_POST['jenis'] ? $_POST['jenis'] : null;
        $lama_buka = $_POST['lama_buka'] ? $_POST['lama_buka'] : null;
        $vol = $_POST['volume'] ? $_POST['volume'] : null;
        $berat_ku = $_POST['berat_ku'] ? $_POST['berat_ku'] : null;
        $pr_utama = $_POST['pr_utama'] ? $_POST['pr_utama'] : null;
        $antikoagulant = $_POST['antikoagulant'] ? $_POST['antikoagulant'] : '0';
        $nmkantong = $_POST['namakantong'] ? $_POST['namakantong'] : null;
        $company = $_POST['company'] ? $_POST['company'] : null;
        $composition = $_POST['composition'] ? $_POST['composition'] : null;
        $texture = $_POST['texture'] ? $_POST['texture'] : null;
        $acname = $_POST['anticoagulant_name'] ? $_POST['anticoagulant_name'] : null;
        $standard_bag = $_POST['standard_bag'] ? $_POST['standard_bag'] : null;
        $standard_acd = $_POST['standard_acd'] ? $_POST['standard_acd'] : null;
        $licence = $_POST['license'] ? $_POST['license'] : null;
        $licenceby = $_POST['licenseby'] ? $_POST['licenseby'] : '-';
        $dimensi_tinggi = $_POST['dimensi_tinggi'] ? $_POST['dimensi_tinggi'] : null;
        $dimensi_lebar = $_POST['dimensi_lebar'] ? $_POST['dimensi_lebar'] : null;
        $panjangselang = $_POST['panjangselang'] ? $_POST['panjangselang'] : null;
        $beratkeseluruhan = $_POST['beratkeseluruhan'] ? $_POST['beratkeseluruhan'] : null;
        $ev_utdptanggal = $_POST['ev_utdptanggal'] ? $_POST['ev_utdptanggal'] : null;
        $ev_utdpnomor = $_POST['ev_utdpnomor'] ? $_POST['ev_utdpnomor'] : null;
        $expired = $_POST['expired'] ? $_POST['expired'] : null;

        $berat_satelit = array();
        $produk_satelit = array();

        $jumlahSatelit = 0;

        // Tentukan jumlah satelit berdasar jenis
        if ($jenis === 2)
            $jumlahSatelit = 1;
        elseif ($jenis === 3)
            $jumlahSatelit = 2;
        elseif ($jenis === 4 || $jenis === 5)
            $jumlahSatelit = 3;
        elseif ($jenis === 6)
            $jumlahSatelit = 7;

        // for ($i = 1; $i <= 7; $i++) {
        //     $beratKey = "berat_s$i";
        //     $prKey = "pr_s$i";

        //     $berat_satelit[$i] = isset($_POST[$beratKey]) && $_POST[$beratKey] !== '' ? $_POST[$beratKey] : '0.000';
        //     $produk_satelit[$i] = isset($_POST[$prKey]) && $_POST[$prKey] !== '' ? $_POST[$prKey] : null;
        // }

        // Loop untuk ambil data berat dan produk satelit
        for ($i = 1; $i <= 7; $i++) {
            $beratKey = "berat_s$i";
            $prKey = "pr_s$i";

            $berat_satelit[$i] = isset($_POST[$beratKey]) && $_POST[$beratKey] !== '' ? $_POST[$beratKey] : '0.000';
            $produk_satelit[$i] = isset($_POST[$prKey]) && $_POST[$prKey] !== '' ? $_POST[$prKey] : null;
        }

        // Fungsi untuk parsing string JSON produk dari Tagify jadi teks biasa
	/*
        function parseProduk($jsonString)
        {
            $produkArr = json_decode($jsonString, true);
            if (is_array($produkArr)) {
                return implode(",", array_column($produkArr, 'value'));
            }
            return "";
        }
	**/
	// Fungsi untuk parsing string JSON produk dari Tagify jadi teks biasa
	function parseProduk($jsonString)
	{
	    $produkArr = json_decode($jsonString, true);
	    if (is_array($produkArr)) {
	        $values = array();
	        foreach ($produkArr as $item) {
	            if (isset($item['value'])) {
	                $values[] = $item['value'];
	            }
	        }
	        return implode(",", $values);
	    }
	    return "";
	}

        // Ubah produk utama dan semua produk satelit jadi string teks biasa
        $pr_utama = parseProduk($pr_utama);
        for ($i = 1; $i <= 7; $i++) {
            $produk_satelit[$i] = strtoupper(parseProduk($produk_satelit[$i]));
        }

        // Logging hasil parsing
	/*
        file_put_contents(
            "log_inputan_form.txt",
            "[" . date("Y-m-d H:i:s") . "] " . json_encode(array(
                'pr_utama' => $pr_utama,
                'produk_satelit' => $produk_satelit,
                'berat_satelit' => $berat_satelit,
                // Tambahkan field lain jika perlu
            ), JSON_PRETTY_PRINT) . PHP_EOL,
            FILE_APPEND
        );
	*/

        // Validasi umum
        if (!$merk || !$jenis || !$lama_buka || !$vol) {
            echo json_encode(array("success" => false, "message" => "Field wajib tidak boleh kosong."));
            exit;
        }

        // Validasi field utama (jika ada)
        if (empty($_POST['pr_utama'])) {
            die(json_encode(array('status' => 'error', 'message' => 'Produk utama harus diisi')));
        }

        // Validasi field satelit
        $errors = array();
        for ($i = 1; $i <= $jumlahSatelit; $i++) {
            $beratKey = "berat_s{$i}";
            $prKey = "pr_s{$i}";

            // if (empty($_POST[$beratKey]) || !preg_match('/^\d+(\.\d{1,2})?$/', $_POST[$beratKey])) {

            if (empty($_POST[$beratKey]) || !preg_match('/^\d+(\.\d+)?$/', $_POST[$beratKey])) {
                $errors[] = "Berat kantong satelit #{$i} harus berupa angka desimal dengan titik, dan angka di belakang koma bila diperlukan.";
            }

            if (empty($_POST[$prKey]) || !preg_match('/^\d+(\.\d+)?$/', $_POST[$prKey])) {
                $errors[] = "Produk satelit #{$i} harus berupa angka desimal dengan titik, dan angka di belakang koma bila diperlukan.";
            }
        }

        // Jika ada error, hentikan proses
        if (!empty($errors)) {
            die(json_encode(array(
                'status' => 'error',
                'message' => implode("<br>", $errors)
            )));
        }

        // VALIDASI APABILA TERDAPAT MERK + JENIS + VOLUME YANG SAMA
        $stmt = $dbi->prepare("SELECT COUNT(*) as total FROM master_kantong WHERE merk = ? AND jenis = ? AND vol = ?");
        $stmt->bind_param("sis", $merk, $jenis, $vol); // s = string, i = integer

        $stmt->execute();
        //$result = $stmt->get_result();
        //$data = $result->fetch_assoc();
	$stmt->bind_result($total);
	$stmt->fetch();
	$stmt->close();

	$data = array('total' => $total);

        if ($data['total'] > 0) {
            die(json_encode(array(
                'status' => 'error',
                'message' => "Kombinasi merk, jenis, dan volume, ini sudah tersedia dalam data. Silakan periksa kembali."
            )));
        }

        $query = "INSERT INTO master_kantong (
            merk, jenis, vol, berat_ku, pr_utama, berat_s1, pr_s1, berat_s2, pr_s2, 
            berat_s3, pr_s3, berat_s4, pr_s4, berat_s5, pr_s5, berat_s6, pr_s6, berat_s7, pr_s7, 
            lama_buka, idudd, namakantong, company, composition, texture, anticoagulant_name, 
            standard_bag, standard_acd, license, licenseby, dimensi_tinggi, dimensi_lebar, 
            panjangselang, beratkeseluruhan, ev_utdptanggal, ev_utdpnomor, expired, antikoagulant
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $stmt = $dbi->prepare($query);
        if (!$stmt) {
            echo json_encode(array(
                'success' => false,
                'message' => "Gagal mempersiapkan query: {$dbi->error}"
            ));
            exit;
        }

        $stmt->bind_param(
            "sissssssssssssssssssssssssssssssssssss", // Ensure this matches the number of placeholders in the query
            $merk,
            $jenis,
            $vol,
            $berat_ku,
            $pr_utama,
            $berat_satelit[1],
            $produk_satelit[1],
            $berat_satelit[2],
            $produk_satelit[2],
            $berat_satelit[3],
            $produk_satelit[3],
            $berat_satelit[4],
            $produk_satelit[4],
            $berat_satelit[5],
            $produk_satelit[5],
            $berat_satelit[6],
            $produk_satelit[6],
            $berat_satelit[7],
            $produk_satelit[7],
            $lama_buka,
            $idudd,
            $nmkantong,
            $company,
            $composition,
            $texture,
            $acname,
            $standard_bag,
            $standard_acd,
            $licence,
            $licenceby,
            $dimensi_tinggi,
            $dimensi_lebar,
            $panjangselang,
            $beratkeseluruhan,
            $ev_utdptanggal,
            $ev_utdpnomor,
            $expired,
            $antikoagulant
        );
        $execute = $stmt->execute();

        if ($execute) {
            echo json_encode(array("success" => true));
        } else {
            // Logging error detail ke file
            file_put_contents(
                "log_stmt_error.txt",
                "[" . date("Y-m-d H:i:s") . "] " . $stmt->error . PHP_EOL,
                FILE_APPEND
            );

            echo json_encode(array(
                "success" => false,
                "message" => "Gagal menyimpan data baru."
            ));
        }
        $stmt->close();

    } elseif ($aksi == "edit") {

        //file_put_contents(
        //    "log_inputan_form_edit.txt",
        //    "[" . date("Y-m-d H:i:s") . "] " . json_encode($_POST, JSON_PRETTY_PRINT) . PHP_EOL,
        //    FILE_APPEND
        //);

        $id_kantong = $_POST['id'] ? $_POST['id'] : null;

        // Variables for edit form
        $editMerk = $_POST['editMerkHidden'] ? $_POST['editMerkHidden'] : null;
        $editJenis = $_POST['editJenisHidden'] ? $_POST['editJenisHidden'] : null;
        $editLamaBuka = $_POST['editLamaBuka'] ? $_POST['editLamaBuka'] : null;
        $editVolume = $_POST['editVolume'] ? $_POST['editVolume'] : null;
        $editBeratKu = $_POST['editBeratKU'] ? $_POST['editBeratKU'] : null;
        $edit_pr_utama = $_POST['edit_pr_utama'] ? $_POST['edit_pr_utama'] : null;
        $editAntikoagulant = $_POST['editAntikoagulant'] ? $_POST['editAntikoagulant'] : '0';
        $editNmkantong = $_POST['editNamaKantong'] ? $_POST['editNamaKantong'] : null;
        $editCompany = $_POST['editCompany'] ? $_POST['editCompany'] : null;
        $editComposition = $_POST['editComposition'] ? $_POST['editComposition'] : null;
        $editTexture = $_POST['editTexture'] ? $_POST['editTexture'] : null;
        $editAcname = $_POST['editAnticoagulantName'] ? $_POST['editAnticoagulantName'] : null;
        $editStandardBag = $_POST['editStandardBag'] ? $_POST['editStandardBag'] : null;
        $editStandardAcd = $_POST['editStandardAcd'] ? $_POST['editStandardAcd'] : null;
        $editLicence = $_POST['editLicense'] ? $_POST['editLicense'] : null;
        $editLicenceby = $_POST['editLicenseby'] ? $_POST['editLicenseby'] : '-';
        $editDimensiTinggi = $_POST['editDimensiTinggi'] ? $_POST['editDimensiTinggi'] : null;
        $editDimensiLebar = $_POST['editDimensiLebar'] ? $_POST['editDimensiLebar'] : null;
        $editPanjangSelang = $_POST['editPanjangSelang'] ? $_POST['editPanjangSelang'] : null;
        $editBeratKeseluruhan = $_POST['editBeratKeseluruhan'] ? $_POST['editBeratKeseluruhan'] : null;
        $editEvUtdptanggal = $_POST['editEvUtdptanggal'] ? $_POST['editEvUtdptanggal'] : null;
        $editEvUtdpnomor = $_POST['editEvUtdpnomor'] ? $_POST['editEvUtdpnomor'] : null;
        $editExpired = $_POST['editExpired'] ? $_POST['editExpired'] : null;

        $edit_berat_satelit = array();
        $edit_produk_satelit = array();

        $editJumlahSatelit = 0;

        if ($editJenis === 2)
            $editJumlahSatelit = 1;
        elseif ($editJenis === 3)
            $editJumlahSatelit = 2;
        elseif ($editJenis === 4 || $editJenis === 5)
            $editJumlahSatelit = 3;
        elseif ($editJenis === 6)
            $editJumlahSatelit = 7;

        for ($i = 1; $i <= 7; $i++) {
            $editBeratKey = "edit_berat_s$i";
            $editPrKey = "edit_pr_s$i";

            $edit_berat_satelit[$i] = isset($_POST[$editBeratKey]) && $_POST[$editBeratKey] !== '' ? $_POST[$editBeratKey] : '0.000';
            $edit_produk_satelit[$i] = isset($_POST[$editPrKey]) && $_POST[$editPrKey] !== '' ? $_POST[$editPrKey] : null;
        }

        // Fungsi untuk parsing string JSON produk dari Tagify jadi teks biasa
	/**
        function parseProduk($jsonString)
        {
            $editProdukArr = json_decode($jsonString, true);
            if (is_array($editProdukArr)) {
                return implode(",", array_column($editProdukArr, 'value'));
            }
            return "";
        }
	*/

	function parseProduk($jsonString)
	{
	    $editProdukArr = json_decode($jsonString, true);
	    if (is_array($editProdukArr)) {
	        $values = array();
	        foreach ($editProdukArr as $item) {
	            if (isset($item['value'])) {
	                $values[] = $item['value'];
	            }
	        }
	        return implode(",", $values);
	    }
	    return "";
	}


        // Ubah produk utama dan semua produk satelit jadi string teks biasa
        $edit_pr_utama = parseProduk($edit_pr_utama);
        for ($i = 1; $i <= 7; $i++) {
            $edit_produk_satelit[$i] = strtoupper(parseProduk($edit_produk_satelit[$i]));
        }

        // Logging hasil parsing
	/**
        file_put_contents(
            "log_inputan_form_edit.txt",
            "[" . date("Y-m-d H:i:s") . "] " . json_encode(array(
                'edit_pr_utama' => $edit_pr_utama,
                'dit_produk_satelit' => $edit_produk_satelit,
                'dit_berat_satelit' => $edit_berat_satelit,
                // Tambahkan field lain jika perlu
            ), JSON_PRETTY_PRINT) . PHP_EOL,
            FILE_APPEND
        );
	*/

        // Validasi umum
        if (!$editMerk || !$editJenis || !$editLamaBuka || !$editVolume) {
            echo json_encode(array("success" => false, "message" => "Field wajib tidak boleh kosong."));
            exit;
        }

        if (!$id_kantong) {
            echo json_encode(array("success" => false, "message" => "ID kantong tidak ditemukan."));
            exit;
        }

        $query = "UPDATE master_kantong SET 
            merk = ?, 
            jenis = ?, 
            vol = ?, 
            berat_ku = ?, 
            pr_utama = ?, 
            berat_s1 = ?, 
            pr_s1 = ?, berat_s2 = ?, pr_s2 = ?, 
            berat_s3 = ?, pr_s3 = ?, berat_s4 = ?, pr_s4 = ?, berat_s5 = ?, pr_s5 = ?, berat_s6 = ?, pr_s6 = ?, berat_s7 = ?, pr_s7 = ?, 
            lama_buka = ?, 
            namakantong = ?, 
            company = ?, 
            composition = ?, 
            texture = ?, 
            anticoagulant_name = ?, 
            standard_bag = ?, 
            standard_acd = ?, 
            license = ?, 
            licenseby = ?, 
            dimensi_tinggi = ?, 
            dimensi_lebar = ?, 
            panjangselang = ?, 
            beratkeseluruhan = ?, 
            ev_utdptanggal = ?, 
            ev_utdpnomor = ?, 
            expired = ?, 
            antikoagulant = ? 
            WHERE id = ?";
        $stmt = $dbi->prepare($query);
        $stmt->bind_param(
            "sisssssssssssssssssssssssssssssssssssi",
            $editMerk,
            $editJenis,
            $editVolume,
            $editBeratKu,
            $edit_pr_utama,
            $edit_berat_satelit[1],
            $edit_produk_satelit[1],
            $edit_berat_satelit[2],
            $edit_produk_satelit[2],
            $edit_berat_satelit[3],
            $edit_produk_satelit[3],
            $edit_berat_satelit[4],
            $edit_produk_satelit[4],
            $edit_berat_satelit[5],
            $edit_produk_satelit[5],
            $edit_berat_satelit[6],
            $edit_produk_satelit[6],
            $edit_berat_satelit[7],
            $edit_produk_satelit[7],
            $editLamaBuka,
            $editNmkantong,
            $editCompany,
            $editComposition,
            $editTexture,
            $editAcname,
            $editStandardBag,
            $editStandardAcd,
            $editLicence,
            $editLicenceby,
            $editDimensiTinggi,
            $editDimensiLebar,
            $editPanjangSelang,
            $editBeratKeseluruhan,
            $editEvUtdptanggal,
            $editEvUtdpnomor,
            $editExpired,
            $editAntikoagulant,
            $id_kantong
        );
        $execute = $stmt->execute();

        if ($execute) {
            // // Hapus berat satelit lama
            // $dbi->query("DELETE FROM kantong_satelit WHERE id_kantong = $id_kantong");

            // foreach ($berat_satelit as $index => $berat) {
            //     $queryS = "INSERT INTO kantong_satelit (id_kantong, nomor_satelit, berat) VALUES (?, ?, ?)";
            //     $stmtS = $dbi->prepare($queryS);
            //     $stmtS->bind_param("iid", $id_kantong, $index, $berat);
            //     $stmtS->execute();
            // }

            // Logging error detail ke file
            file_put_contents(
                "log_stmt_error.txt",
                "[" . date("Y-m-d H:i:s") . "] " . $stmt->error . PHP_EOL,
                FILE_APPEND
            );

            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "message" => "Gagal update data."));
        }
        $stmt->close();
    }
    $dbi->close();
}
