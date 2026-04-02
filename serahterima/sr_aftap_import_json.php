<?php
session_start();
include '../config/dbi_connect.php';

header('Content-Type: application/json');

if (!isset($_FILES['fileJson']) || $_FILES['fileJson']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('status' => 'error', 'message' => 'File JSON tidak ditemukan atau gagal diupload'));
    exit;
}

// PHP 5.3 compatible: gunakan isset() + ternary
$aksi = isset($_POST['aksiDuplikat']) ? $_POST['aksiDuplikat'] : 'skip';
$file = $_FILES['fileJson']['tmp_name'];
$json_content = file_get_contents($file);

if (!$json_content) {
    echo json_encode(array('status' => 'error', 'message' => 'File kosong atau tidak bisa dibaca'));
    exit;
}

$data = json_decode($json_content, true);

// PHP 5.3 compatible: json_last_error_msg() belum ada, gunakan json_last_error() saja
if (json_last_error() !== JSON_ERROR_NONE) {
    $error_messages = array(
        JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
        JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
        JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters'
    );
    $error_code = json_last_error();
    $error_msg = isset($error_messages[$error_code]) ? $error_messages[$error_code] : 'Unknown error';
    echo json_encode(array('status' => 'error', 'message' => 'Format JSON tidak valid: ' . $error_msg));
    exit;
}

if (empty($data['notransaksi']) || empty($data['serahterima']) || empty($data['serahterimadetail'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Struktur JSON tidak lengkap (kurang notransaksi / serahterima / detail)'));
    exit;
}

$noTrans = mysqli_real_escape_string($dbi, $data['notransaksi']);
$inserted = 0;
$updated = 0;
$skipped = 0;

// PHP 5.3 compatible: gunakan autocommit dan START TRANSACTION manual
mysqli_autocommit($dbi, false);

try {
    // 1. Serahterima (header)
    $head = $data['serahterima'];
    unset($head['hst_id']); // Hapus hst_id jika ada di JSON, biar auto-increment

    $qCheckHead = mysqli_query($dbi, "SELECT hst_notrans FROM serahterima WHERE hst_notrans = '$noTrans'");
    $existsHead = mysqli_num_rows($qCheckHead) > 0;

    if ($existsHead) {
        if ($aksi === 'skip') {
            // skip header & semua detailnya
            throw new Exception("Data dengan nomor transaksi $noTrans sudah ada. Lewati.");
        } else if ($aksi === 'update' || $aksi === 'replace') {
            // update header
            $set = array();
            foreach ($head as $k => $v) {
                if ($k !== 'hst_notrans' && $k !== 'hst_id') { // jangan update primary key
                    $set[] = "`$k` = '" . mysqli_real_escape_string($dbi, $v) . "'";
                }
            }
            if (!empty($set)) {
                $sqlHead = "UPDATE serahterima SET " . implode(', ', $set) . " WHERE hst_notrans = '$noTrans'";
                if (!mysqli_query($dbi, $sqlHead)) {
                    throw new Exception("Gagal update header: " . mysqli_error($dbi));
                }
                $updated++;
            }
        }
    } else {
        // insert baru
        $fields = implode('`,`', array_keys($head));

        // PHP 5.3: gunakan function biasa, bukan arrow function
        $escaped_values = array();
        foreach (array_values($head) as $val) {
            $escaped_values[] = mysqli_real_escape_string($dbi, $val);
        }
        $values = "'" . implode("','", $escaped_values) . "'";

        $sqlHead = "INSERT INTO serahterima (`$fields`) VALUES ($values)";
        if (!mysqli_query($dbi, $sqlHead)) {
            throw new Exception("Gagal insert header: " . mysqli_error($dbi));
        }
        $inserted++;
    }

    // 2. Detail, stokkantong, pendonor, htransaksi
    foreach ($data['serahterimadetail'] as $detail) {
        unset($detail['dst_iddetail']); // Hapus dst_iddetail jika ada di JSON, biar auto-increment

        $nokantong = mysqli_real_escape_string($dbi, $detail['dst_nokantong']);
        $kodedonor = mysqli_real_escape_string($dbi, $detail['dst_kodedonor']);
        $no_aftap  = mysqli_real_escape_string($dbi, $detail['dst_no_aftap']);

        // Cek apakah detail sudah ada
        $qCheckDetail = mysqli_query($dbi, "SELECT dst_notrans FROM serahterima_detail 
                                           WHERE dst_notrans = '$noTrans' AND dst_nokantong = '$nokantong'");
        $existsDetail = mysqli_num_rows($qCheckDetail) > 0;

        if ($existsDetail && $aksi === 'skip') {
            $skipped++;
            continue;
        }

        // Proses pendonor
        if (!empty($data['pendonor'])) {
            foreach ($data['pendonor'] as $pd) {
                if ($pd['Kode'] == $kodedonor) {
                    $qCheckPd = mysqli_query($dbi, "SELECT Kode FROM pendonor WHERE Kode='$kodedonor'");
                    $existsPd = mysqli_num_rows($qCheckPd) > 0;

                    if ($existsPd && ($aksi === 'update' || $aksi === 'replace')) {
                        $setPd = array();
                        foreach ($pd as $k => $v) {
                            if ($k !== 'Kode') {
                                $setPd[] = "`$k` = '" . mysqli_real_escape_string($dbi, $v) . "'";
                            }
                        }
                        if (!empty($setPd)) {
                            mysqli_query($dbi, "UPDATE pendonor SET " . implode(', ', $setPd) . " WHERE Kode='$kodedonor'");
                        }
                    } else if (!$existsPd) {
                        $fieldsPd = implode('`,`', array_keys($pd));

                        // PHP 5.3 compatible
                        $escaped_pd = array();
                        foreach (array_values($pd) as $val) {
                            $escaped_pd[] = mysqli_real_escape_string($dbi, $val);
                        }
                        $valuesPd = "'" . implode("','", $escaped_pd) . "'";

                        mysqli_query($dbi, "INSERT INTO pendonor (`$fieldsPd`) VALUES ($valuesPd)");
                    }
                    break;
                }
            }
        }

        // Proses htransaksi
        if (!empty($data['htransaksi'])) {
            foreach ($data['htransaksi'] as $ht) {
                if ($ht['NoTrans'] == $no_aftap) {
                    $qCheckHt = mysqli_query($dbi, "SELECT NoTrans FROM htransaksi WHERE NoTrans='$no_aftap'");
                    $existsHt = mysqli_num_rows($qCheckHt) > 0;

                    if ($existsHt && ($aksi === 'update' || $aksi === 'replace')) {
                        $setHt = array();
                        foreach ($ht as $k => $v) {
                            if ($k !== 'NoTrans') {
                                $setHt[] = "`$k` = '" . mysqli_real_escape_string($dbi, $v) . "'";
                            }
                        }
                        if (!empty($setHt)) {
                            mysqli_query($dbi, "UPDATE htransaksi SET " . implode(', ', $setHt) . " WHERE NoTrans='$no_aftap'");
                        }
                    } else if (!$existsHt) {
                        $fieldsHt = implode('`,`', array_keys($ht));

                        // PHP 5.3 compatible
                        $escaped_ht = array();
                        foreach (array_values($ht) as $val) {
                            $escaped_ht[] = mysqli_real_escape_string($dbi, $val);
                        }
                        $valuesHt = "'" . implode("','", $escaped_ht) . "'";

                        mysqli_query($dbi, "INSERT INTO htransaksi (`$fieldsHt`) VALUES ($valuesHt)");
                    }
                    break;
                }
            }
        }

        // Proses stokkantong
        if (!empty($data['stokkantong'])) {
            foreach ($data['stokkantong'] as $kantong) {
                $noKantong = mysqli_real_escape_string($dbi, $kantong['noKantong']);
                $qCheckKantong = mysqli_query($dbi, "SELECT noKantong FROM stokkantong WHERE noKantong='$noKantong'");
                $existsKantong = mysqli_num_rows($qCheckKantong) > 0;

                if ($existsKantong && ($aksi === 'update' || $aksi === 'replace')) {
                    $setK = array();
                    foreach ($kantong as $k => $v) {
                        if ($k !== 'noKantong') {
                            $setK[] = "`$k` = '" . mysqli_real_escape_string($dbi, $v) . "'";
                        }
                    }
                    if (!empty($setK)) {
                        mysqli_query($dbi, "UPDATE stokkantong SET " . implode(', ', $setK) . " WHERE noKantong='$noKantong'");
                    }
                } else if (!$existsKantong) {
                    $fieldsK = implode('`,`', array_keys($kantong));

                    // PHP 5.3 compatible
                    $escaped_k = array();
                    foreach (array_values($kantong) as $val) {
                        $escaped_k[] = mysqli_real_escape_string($dbi, $val);
                    }
                    $valuesK = "'" . implode("','", $escaped_k) . "'";

                    mysqli_query($dbi, "INSERT INTO stokkantong (`$fieldsK`) VALUES ($valuesK)");
                }
            }
        }

        // Terakhir: serahterima_detail
        if ($existsDetail && $aksi === 'update') {
            $setD = array();
            foreach ($detail as $k => $v) {
                if ($k !== 'dst_notrans' && $k !== 'dst_nokantong' && $k !== 'dst_iddetail') {
                    $setD[] = "`$k` = '" . mysqli_real_escape_string($dbi, $v) . "'";
                }
            }
            if (!empty($setD)) {
                if (!mysqli_query($dbi, "UPDATE serahterima_detail SET " . implode(', ', $setD) . " 
                                   WHERE dst_notrans='$noTrans' AND dst_nokantong='$nokantong'")) {
                    throw new Exception("Gagal update detail: " . mysqli_error($dbi));
                }
                $updated++;
            }
        } else if (!$existsDetail || $aksi === 'replace') {
            // DELETE dulu data lama
            if (!mysqli_query($dbi, "DELETE FROM serahterima_detail 
                       WHERE dst_notrans='$noTrans' AND dst_nokantong='$nokantong'")) {
                throw new Exception("Gagal delete detail lama: " . mysqli_error($dbi));
            }

            // Baru INSERT data baru
            $fieldsD = implode('`,`', array_keys($detail));
            $escaped_d = array();
            foreach (array_values($detail) as $val) {
                $escaped_d[] = mysqli_real_escape_string($dbi, $val);
            }
            $valuesD = "'" . implode("','", $escaped_d) . "'";

            if (!mysqli_query($dbi, "INSERT INTO serahterima_detail (`$fieldsD`) VALUES ($valuesD)")) {
                throw new Exception("Gagal insert detail: " . mysqli_error($dbi));
            }
            $inserted++;
        }
    }

    // Commit transaksi (PHP 5.3 compatible)
    mysqli_commit($dbi);
    mysqli_autocommit($dbi, true);

    echo json_encode(array(
        'status'  => 'success',
        'message' => 'Import selesai dengan sukses',
        'detail'  => array(
            'inserted' => $inserted,
            'updated'  => $updated,
            'skipped'  => $skipped
        )
    ));
} catch (Exception $e) {
    // Rollback transaksi (PHP 5.3 compatible)
    mysqli_rollback($dbi);
    mysqli_autocommit($dbi, true);

    echo json_encode(array(
        'status'  => 'error',
        'message' => $e->getMessage()
    ));
}
