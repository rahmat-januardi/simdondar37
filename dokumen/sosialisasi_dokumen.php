<?php

/**
 * =====================================================================
 * Daftar Sosialisasi Dokumen - Single File CRUD
 * PHP 5.3 + ekstensi mysql_*
 * List (DataTable) + Tambah/Edit (Modal + Select2) + Hapus (SweetAlert2)
 *
 * Endpoint AJAX (file ini memanggil dirinya sendiri lewat parameter ?ajax=):
 *   ?ajax=list         -> data untuk DataTable (GET)
 *   ?ajax=get_dokumen  -> daftar dokumen berdasarkan jenis (GET, param: jenis)
 *   ?ajax=detail       -> detail 1 data untuk prefill modal edit (GET, param: id)
 *   ?ajax=simpan       -> simpan insert/update (POST)
 *   ?ajax=hapus        -> hapus data (GET, param: id)
 * =====================================================================
 */
session_start();
include "koneksi.php";

function amankan($koneksi, $val)
{
    return mysql_real_escape_string(trim($val), $koneksi);
}

function v($data, $key)
{
    return isset($data[$key]) ? $data[$key] : '';
}

/*
 * ================================================================
 * HELPER NAMA PENGAKSES
 * ================================================================
 *
 * Data baru disimpan sebagai JSON array agar koma pada gelar
 * seperti "M. Cahyo Apriyanto, S.Si" tidak dianggap pemisah nama.
 *
 * Data lama yang masih berbentuk string koma tetap dicoba dibaca
 * dengan parser kompatibilitas.
 */

function pengaksesApakahGelar($value)
{
    $value = trim($value);

    if ($value === '') {
        return false;
    }

    return preg_match(
        '/^(Dr\.?dr\.?|Dr\.?|dr\.?|Prof\.?|S\.Si\.?|S\.Kom\.?|S\.Kep\.?|S\.Farm\.?|S\.Pd\.?|S\.T\.?|S\.E\.?|S\.H\.?|S\.Sos\.?|S\.KM\.?|S\.M\.?|M\.Si\.?|M\.Kom\.?|M\.Biomed\.?|M\.Pd\.?|M\.Kes\.?|M\.Farm\.?|M\.Kep\.?|M\.T\.?|M\.E\.?|A\.Md\.?|A\.Md\.?\s+Kes\.?|A\.Kep\.?|A\.Farm\.?|Ns\.?|Ners\.?|Sp\.?[A-Za-z.]*?)$/i',
        $value
    );
}

function normalisasiPengakses($value)
{
    $value = trim($value);

    if ($value === '') {
        return array();
    }

    /*
     * FORMAT BARU:
     * ["Nama 1", "Nama 2, S.Si"]
     */
    $decoded = json_decode($value, true);

    if (is_array($decoded)) {
        $hasil = array();

        foreach ($decoded as $nama) {
            $nama = trim($nama);

            if ($nama !== '' && !in_array($nama, $hasil, true)) {
                $hasil[] = $nama;
            }
        }

        return $hasil;
    }

    /*
     * FORMAT LAMA:
     * Nama 1, Nama 2, S.Si, Nama 3, A.Md. Kes
     *
     * Koma sebelum token gelar dipertahankan.
     */
    $parts = preg_split('/\s*,\s*/', $value);

    $hasil = array();
    $buffer = '';

    foreach ($parts as $part) {
        $part = trim($part);

        if ($part === '') {
            continue;
        }

        if (pengaksesApakahGelar($part)) {
            if ($buffer !== '') {
                $buffer .= ', ' . $part;
            } else {
                $buffer = $part;
            }
        } else {
            if ($buffer !== '') {
                $hasil[] = trim($buffer);
            }

            $buffer = $part;
        }
    }

    if ($buffer !== '') {
        $hasil[] = trim($buffer);
    }

    if (count($hasil) === 0) {
        $hasil[] = $value;
    }

    return $hasil;
}

function pengaksesKeJson($value)
{
    return json_encode(normalisasiPengakses($value));
}

function pengaksesKeTampilan($value)
{
    return implode(', ', normalisasiPengakses($value));
}


// Mapping jenis dokumen -> tabel sumber, kolom nomor dokumen, urutan
// Sesuaikan nama kolom 'nama', 'versi', 'tgl_berlaku' jika struktur tabel Anda berbeda
$mapDokumen = array(
    'kebijakan' => array('table' => 'kebijakan', 'kolom_id' => 'nomor', 'label' => 'Kebijakan', 'kolom_no' => 'kontrol', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku', 'order_by' => 'RIGHT(kontrol,3)'),
    'pks'       => array('table' => 'pks', 'kolom_id' => 'nomor', 'label' => 'SPO', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku', 'order_by' => 'kontrol2'),
    'ik'        => array('table' => 'ik', 'kolom_id' => 'nomor', 'label' => 'IK (Instruksi Kerja)', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku', 'order_by' => 'kontrol2'),
    'ika'       => array('table' => 'ika', 'kolom_id' => 'nomor', 'label' => 'IKA (Instruksi Kerja Alat)', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku', 'order_by' => 'kontrol2'),
    'pendukung' => array('table' => 'pendukung', 'kolom_id' => 'nomor', 'label' => 'Dokumen Pendukung', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku', 'order_by' => 'kontrol2'),
    'formulir'  => array('table' => 'formulir', 'kolom_id' => 'nomor', 'label' => 'Formulir', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku', 'order_by' => 'kontrol2'),
    'eksternal' => array('table' => 'eksternal', 'kolom_id' => 'id', 'label' => 'Dokumen Eksternal', 'kolom_no' => 'no_tahun_dokumen', 'kolom_nama' => 'nama', 'kolom_versi' => 'tingkat', 'kolom_tgl' => '', 'kolom_file' => 'fileku', 'order_by' => 'id ASC'),
);

function ambilDetailDokumen($koneksi, $mapDokumen, $jenis, $dokumenId)
{
    if (!isset($mapDokumen[$jenis]) || (int) $dokumenId < 1) return array();

    $cfg = $mapDokumen[$jenis];
    $id = (int) $dokumenId;
    $res = mysql_query("SELECT * FROM `" . $cfg['table'] . "` WHERE `" . $cfg['kolom_id'] . "` = " . $id . " LIMIT 1", $koneksi);
    if (!$res) return array();

    $row = mysql_fetch_assoc($res);
    if (!$row) return array();

    $file = '';
    $kolomFile = array($cfg['kolom_file'], 'fileku', 'file', 'nama_file', 'filename');
    foreach ($kolomFile as $namaKolomFile) {
        if ($namaKolomFile !== '' && isset($row[$namaKolomFile]) && $row[$namaKolomFile] !== '') {
            $file = $row[$namaKolomFile];
            break;
        }
    }

    return array(
        'nomor_dokumen' => isset($row[$cfg['kolom_no']]) ? $row[$cfg['kolom_no']] : '-',
        'nama_dokumen' => isset($row[$cfg['kolom_nama']]) ? $row[$cfg['kolom_nama']] : '-',
        'versi_dokumen' => isset($row[$cfg['kolom_versi']]) ? $row[$cfg['kolom_versi']] : '-',
        'tgl_berlaku_dokumen' => ($cfg['kolom_tgl'] !== '' && isset($row[$cfg['kolom_tgl']])) ? $row[$cfg['kolom_tgl']] : '',
        'file_dokumen' => $file
    );
}


/* =====================================================================
 * ENDPOINT: ?export=excel -> export Excel
 * Tetap di file yang sama, sehingga tidak bergantung pada lokasi folder.
 * ===================================================================== */
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    /*
     * Export memakai query yang sama dengan DataTables.
     * Tambahkan validasi koneksi + error detail agar jika gagal,
     * penyebabnya terlihat jelas (bukan hanya "Query gagal:").
     */
    if (!isset($koneksi)) {
        header('Content-Type: text/plain; charset=utf-8');
        die('Variabel koneksi database $koneksi tidak ditemukan dari koneksi.php.');
    }

    $sql = "SELECT * FROM `sosialisasi_dokumen` ORDER BY `tanggal_sosialisasi` DESC, `id` DESC";
    $res = mysql_query($sql, $koneksi);

    if ($res === false) {
        $errNo  = function_exists('mysql_errno') ? mysql_errno($koneksi) : 0;
        $errMsg = function_exists('mysql_error') ? mysql_error($koneksi) : '';
        header('Content-Type: text/plain; charset=utf-8');
        die('Query export gagal.' . "\\n" .
            'MySQL Error No: ' . $errNo . "\\n" .
            'MySQL Error: ' . ($errMsg !== '' ? $errMsg : 'Tidak ada pesan error dari MySQL.') . "\\n" .
            'SQL: ' . $sql);
    }

    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="Daftar_Sosialisasi_Dokumen_' . date('Ymd_His') . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo "\xEF\xBB\xBF";
?>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Sosialisasi Dokumen</th>
                <th>Kategori Dokumen</th>
                <th>Poin Perubahan Dokumen</th>
                <th>Nomor Dokumen</th>
                <th>Nama Dokumen</th>
                <th>Versi Dokumen</th>
                <th>Tgl. Berlaku Dokumen</th>
                <th>Sasaran Audiens</th>
                <th>Nama Pengakses</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($row = mysql_fetch_assoc($res)): ?>
                <?php
                $detailDokumen = ambilDetailDokumen($koneksi, $mapDokumen, $row['jenis_dokumen'], $row['dokumen_id']);
                $versi = (isset($detailDokumen['versi_dokumen']) && $detailDokumen['versi_dokumen'] != '') ? $detailDokumen['versi_dokumen'] : '-';
                $tglSosialisasi = (isset($row['tanggal_sosialisasi']) && $row['tanggal_sosialisasi'] != '' && $row['tanggal_sosialisasi'] != '0000-00-00')
                    ? date('d-m-Y', strtotime($row['tanggal_sosialisasi']))
                    : '-';
                $tglBerlaku = (isset($detailDokumen['tgl_berlaku_dokumen']) && $detailDokumen['tgl_berlaku_dokumen'] != '' && $detailDokumen['tgl_berlaku_dokumen'] != '0000-00-00')
                    ? date('d-m-Y', strtotime($detailDokumen['tgl_berlaku_dokumen']))
                    : '-';
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo htmlspecialchars($tglSosialisasi); ?></td>
                    <td><?php echo ($row['kategori_dokumen'] === 'baru') ? 'Dokumen Baru' : 'Dokumen Revisi'; ?></td>
                    <td><?php echo isset($row['poin_perubahan']) && $row['poin_perubahan'] != '' ? htmlspecialchars($row['poin_perubahan']) : '-'; ?>
                    </td>
                    <td><?php echo htmlspecialchars($detailDokumen['nomor_dokumen']); ?></td>
                    <td><?php echo htmlspecialchars($detailDokumen['nama_dokumen']); ?></td>
                    <td><?php echo htmlspecialchars($versi); ?></td>
                    <td><?php echo htmlspecialchars($tglBerlaku); ?></td>
                    <td><?php
                        $daftarPengakses = normalisasiPengakses(isset($row['nama_pengakses']) ? $row['nama_pengakses'] : '');
                        if (count($daftarPengakses) === 0) {
                            echo '<span class="badge bg-secondary">Semua Audiens</span>';
                        } else {
                            echo implode('<br>', array_map('htmlspecialchars', $daftarPengakses));
                        }
                        ?>
                    </td>
                    <td><?php
                        $daftarPembaca = normalisasiPengakses(isset($row['pembaca']) ? $row['pembaca'] : '');
                        echo count($daftarPembaca) > 0
                            ? implode('<br>', array_map('htmlspecialchars', $daftarPembaca))
                            : '-';
                        ?>
                    </td>
                </tr>
            <?php $no++;
            endwhile; ?>
        </tbody>
    </table>
<?php
    exit;
}

$ajax = isset($_GET['ajax']) ? $_GET['ajax'] : '';

/* =====================================================================
 * ENDPOINT: ?ajax=list  -> data DataTable
 * ===================================================================== */
if ($ajax === 'list') {
    $sql = "SELECT * FROM sosialisasi_dokumen ORDER BY tanggal_sosialisasi DESC, id DESC";
    $res = mysql_query($sql, $koneksi);

    $data = array();
    $no   = 1;

    if ($res) {
        while ($row = mysql_fetch_assoc($res)) {
            $detailDokumen = ambilDetailDokumen($koneksi, $mapDokumen, $row['jenis_dokumen'], $row['dokumen_id']);
            $versi = (isset($detailDokumen['versi_dokumen']) && $detailDokumen['versi_dokumen'] != '') ? $detailDokumen['versi_dokumen'] : '-';
            $tglBerlaku = (isset($detailDokumen['tgl_berlaku_dokumen']) && $detailDokumen['tgl_berlaku_dokumen'] != '' && $detailDokumen['tgl_berlaku_dokumen'] != '0000-00-00')
                ? date('d-m-Y', strtotime($detailDokumen['tgl_berlaku_dokumen']))
                : '-';

            $daftarPengakses    = normalisasiPengakses(isset($row['nama_pengakses']) ? $row['nama_pengakses'] : '');
            $daftarPengaksesArr = array_map('htmlspecialchars', $daftarPengakses);
            $daftarPembaca      = normalisasiPengakses(isset($row['pembaca']) ? $row['pembaca'] : '');
            $daftarPembacaArr   = array_map('htmlspecialchars', $daftarPembaca);

            $data[] = array(
                'no'                  => $no,
                'id'                  => $row['id'],
                'tanggal_sosialisasi' => date('d-m-Y', strtotime($row['tanggal_sosialisasi'])),
                'kategori_dokumen'    => ($row['kategori_dokumen'] === 'baru') ? 'Dokumen Baru' : 'Dokumen Revisi',
                'nomor_dokumen'       => htmlspecialchars($detailDokumen['nomor_dokumen']),
                'nama_dokumen'        => htmlspecialchars($detailDokumen['nama_dokumen']),
                'versi_dokumen'       => htmlspecialchars($versi),
                'tgl_berlaku_dokumen' => $tglBerlaku,
                // 'nama_pengakses'/'pembaca': teks polos (dipakai untuk search & sort DataTable)
                // 'nama_pengakses_arr'/'pembaca_arr': array per-nama yang sudah benar dipisah
                //  (gelar tidak ikut kepotong) - dipakai untuk tampilan per-baris di render().
                'nama_pengakses'      => count($daftarPengaksesArr) === 0 ? 'Semua User' : implode(', ', $daftarPengaksesArr),
                'nama_pengakses_arr'  => $daftarPengaksesArr,
                'pembaca'             => count($daftarPembacaArr) === 0 ? '-' : implode(', ', $daftarPembacaArr),
                'pembaca_arr'         => $daftarPembacaArr,
            );
            $no++;
        }
    }

    header('Content-Type: application/json');
    echo json_encode(array('data' => $data));
    exit;
}

/* =====================================================================
 * ENDPOINT: ?ajax=get_semua_dokumen -> daftar dokumen untuk select2
 * ===================================================================== */
if ($ajax === 'get_semua_dokumen') {
    header('Content-Type: application/json');
    $result = array();

    foreach ($mapDokumen as $jenis => $cfg) {
        $sql = "SELECT * FROM `" . $cfg['table'] . "` WHERE aktif = '0' ORDER BY " . $cfg['order_by'];
        $res = mysql_query($sql, $koneksi);
        if (!$res) continue;

        while ($r = mysql_fetch_assoc($res)) {
            $dokumenId = isset($r[$cfg['kolom_id']]) ? $r[$cfg['kolom_id']] : '';
            $tgl = ($cfg['kolom_tgl'] !== '' && !empty($r[$cfg['kolom_tgl']]) && $r[$cfg['kolom_tgl']] != '0000-00-00')
                ? $r[$cfg['kolom_tgl']] : '';
            $file = '';
            $kolomFile = array($cfg['kolom_file'], 'fileku', 'file', 'nama_file', 'filename');
            foreach ($kolomFile as $namaKolomFile) {
                if ($namaKolomFile !== '' && isset($r[$namaKolomFile]) && $r[$namaKolomFile] !== '') {
                    $file = $r[$namaKolomFile];
                    break;
                }
            }
            $result[] = array(
                'key' => $jenis . ':' . $dokumenId,
                'id' => $dokumenId,
                'jenis' => $jenis,
                'jenis_label' => $cfg['label'],
                'nomor_dokumen' => isset($r[$cfg['kolom_no']]) ? $r[$cfg['kolom_no']] : '-',
                'nama_dokumen' => isset($r[$cfg['kolom_nama']]) ? $r[$cfg['kolom_nama']] : '-',
                'versi_dokumen' => isset($r[$cfg['kolom_versi']]) ? $r[$cfg['kolom_versi']] : '-',
                'tgl_berlaku_dokumen' => $tgl,
                'file_dokumen' => $file
            );
        }
    }

    echo json_encode($result);
    exit;
}

/* =====================================================================
 * ENDPOINT: ?ajax=get_pengakses -> suggest nama pengakses dari tabel user
 * ===================================================================== */
if ($ajax === 'get_pengakses') {
    header('Content-Type: application/json');
    $result = array();
    $qPengakses = isset($_GET['q']) ? amankan($koneksi, $_GET['q']) : '';
    $filterPengakses = $qPengakses !== '' ? " AND nama_lengkap LIKE '%" . $qPengakses . "%'" : '';
    $res = mysql_query("SELECT nama_lengkap FROM `user` WHERE nama_lengkap IS NOT NULL AND nama_lengkap <> ''" . $filterPengakses . "AND aktif=0 ORDER BY nama_lengkap", $koneksi);

    if ($res) {
        while ($row = mysql_fetch_assoc($res)) {
            $result[] = $row['nama_lengkap'];
        }
    }

    echo json_encode($result);
    exit;
}

/* =====================================================================
 * ENDPOINT: ?ajax=detail&id=xxx -> data 1 baris untuk prefill modal edit
 * ===================================================================== */
if ($ajax === 'detail') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    header('Content-Type: application/json');

    $sql = "SELECT * FROM sosialisasi_dokumen WHERE id = " . $id;
    $res = mysql_query($sql, $koneksi);

    if ($res && mysql_num_rows($res) > 0) {
        $row = mysql_fetch_assoc($res);
        echo json_encode(array('status' => 'success', 'data' => $row));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Data tidak ditemukan'));
    }
    exit;
}

/* =====================================================================
 * ENDPOINT: ?ajax=simpan -> simpan insert/update (dipanggil dari modal)
 * ===================================================================== */
if ($ajax === 'simpan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $id                  = isset($_POST['id']) ? $_POST['id'] : '';
    $tanggal_sosialisasi = isset($_POST['tanggal_sosialisasi']) ? $_POST['tanggal_sosialisasi'] : '';
    $jenis_dokumen       = isset($_POST['jenis_dokumen']) ? $_POST['jenis_dokumen'] : '';
    $dokumen_id          = isset($_POST['dokumen_id']) ? $_POST['dokumen_id'] : '';
    $kategori_dokumen    = isset($_POST['kategori_dokumen']) ? $_POST['kategori_dokumen'] : '';
    $poin_perubahan      = isset($_POST['poin_perubahan']) ? $_POST['poin_perubahan'] : '';
    $link_cara_pengisian = isset($_POST['link_cara_pengisian']) ? $_POST['link_cara_pengisian'] : '';
    $nama_pengakses = isset($_POST['nama_pengakses']) ? $_POST['nama_pengakses'] : '';

    /*
     * Nama Pengakses bersifat OPSIONAL.
     * Jika dikosongkan -> sosialisasi berlaku untuk SEMUA user
     * (disimpan sebagai array kosong '[]', bukan di-default ke nama session).
     */
    $nama_pengakses_json = pengaksesKeJson($nama_pengakses);

    if ($kategori_dokumen !== 'revisi') {
        $poin_perubahan = '';
    }

    if ($jenis_dokumen !== 'formulir') {
        $link_cara_pengisian = '';
    }

    if (
        $tanggal_sosialisasi == '' || $jenis_dokumen == '' || $dokumen_id == ''
        || $kategori_dokumen == '' || !isset($mapDokumen[$jenis_dokumen])
    ) {
        echo json_encode(array('status' => 'error', 'message' => 'Data tidak lengkap, silakan lengkapi form.'));
        exit;
    }

    $tanggal_sosialisasi_e = amankan($koneksi, $tanggal_sosialisasi);
    $jenis_dokumen_e       = amankan($koneksi, $jenis_dokumen);
    $dokumen_id_parts      = explode(':', $dokumen_id, 2);
    $dokumen_id_e          = (int) (count($dokumen_id_parts) === 2 ? $dokumen_id_parts[1] : $dokumen_id_parts[0]);

    if ($dokumen_id_e < 1) {
        echo json_encode(array('status' => 'error', 'message' => 'Dokumen yang disosialisasikan tidak valid.'));
        exit;
    }

    $kategori_dokumen_e    = amankan($koneksi, $kategori_dokumen);
    $poin_perubahan_e      = amankan($koneksi, $poin_perubahan);
    $link_cara_pengisian_e = amankan($koneksi, $link_cara_pengisian);
    $nama_pengakses_e      = amankan($koneksi, $nama_pengakses_json);

    if ($id != '') {
        $id_e = (int) $id;
        $now_e = date('Y-m-d H:i:s');
        $sql = "UPDATE sosialisasi_dokumen SET
                    tanggal_sosialisasi = '" . $tanggal_sosialisasi_e . "',
                    jenis_dokumen       = '" . $jenis_dokumen_e . "',
                    dokumen_id          = " . $dokumen_id_e . ",
                    kategori_dokumen    = '" . $kategori_dokumen_e . "',
                    poin_perubahan      = '" . $poin_perubahan_e . "',
                    link_cara_pengisian = '" . $link_cara_pengisian_e . "',
                    nama_pengakses      = '" . $nama_pengakses_e . "',
                    updated_at          = '" . $now_e . "'
                WHERE id = " . $id_e;
    } else {
        $sql = "INSERT INTO sosialisasi_dokumen
                    (tanggal_sosialisasi, jenis_dokumen, dokumen_id, kategori_dokumen,
                     link_cara_pengisian, nama_pengakses, poin_perubahan, created_by)
                VALUES
                    ('" . $tanggal_sosialisasi_e . "', '" . $jenis_dokumen_e . "', " . $dokumen_id_e . ",
                     '" . $kategori_dokumen_e . "', '" . $link_cara_pengisian_e . "',
                     '" . $nama_pengakses_e . "', '" . $poin_perubahan_e . "',
                     '" . $_SESSION['nama_lengkap'] . "')";
    }

    $exec = mysql_query($sql, $koneksi);

    if ($exec) {
        echo json_encode(array('status' => 'success', 'message' => 'Data berhasil disimpan.'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Gagal menyimpan data: ' . mysql_error()));
    }
    exit;
}

/* =====================================================================
 * ENDPOINT: ?ajax=hapus&id=xxx -> hapus data
 * ===================================================================== */
if ($ajax === 'hapus') {
    header('Content-Type: application/json');

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if (!$id) {
        echo json_encode(array('status' => 'error', 'message' => 'ID tidak ditemukan'));
        exit;
    }

    $sql  = "DELETE FROM sosialisasi_dokumen WHERE id = " . $id;
    $exec = mysql_query($sql, $koneksi);

    if ($exec) {
        echo json_encode(array('status' => 'success'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => mysql_error()));
    }
    exit;
}

/* =====================================================================
 * HALAMAN UTAMA (bukan AJAX) -> render list + modal
 * ===================================================================== */
$namaPengaksesDefault = '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Sosialisasi Dokumen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables (Bootstrap 5) -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --pmi-red: #c8102e;
            --pmi-red-dark: #9f1239;
            --pmi-red-soft: #fdecef;
            --pmi-bg: #f8f9fa;
            --pmi-surface: #ffffff;
            --pmi-border: #e2e5e9;
            --pmi-text: #343a40;
            --pmi-muted: #6c757d;
            --pmi-success: #198754;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(200, 16, 46, .08), transparent 28%),
                linear-gradient(135deg, #f8f9fa 0%, #fff5f6 100%);
            color: var(--pmi-text);
            min-height: 100vh;
        }

        .page-header {
            background: rgba(255, 255, 255, .88);
            border: 1px solid var(--pmi-border);
            border-radius: 16px;
            padding: 18px 22px;
            box-shadow: 0 5px 20px rgba(52, 58, 64, .07);
        }

        .page-title {
            font-weight: 700;
            color: var(--pmi-red-dark);
            letter-spacing: -.2px;
        }

        .page-subtitle {
            color: var(--pmi-muted);
            font-size: .9rem;
        }

        .title-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: var(--pmi-red-soft);
            color: var(--pmi-red);
            font-size: 1.25rem;
            flex: 0 0 auto;
        }

        .card {
            border: 1px solid var(--pmi-border);
            border-radius: 16px;
            background: var(--pmi-surface);
            box-shadow: 0 8px 28px rgba(52, 58, 64, .08);
            overflow: hidden;
        }

        .card-body {
            padding: 22px;
        }

        .toolbar {
            padding-bottom: 16px;
            border-bottom: 1px solid var(--pmi-border);
        }

        .btn-warm {
            background: var(--pmi-red);
            border-color: var(--pmi-red);
            color: #fff;
        }

        .btn-warm:hover,
        .btn-warm:focus {
            background: var(--pmi-red-dark);
            border-color: var(--pmi-red-dark);
            color: #fff;
        }

        .btn-export {
            background: #eef4ee;
            border: 1px solid #d6e3d7;
            color: var(--pmi-success);
        }

        .btn-export:hover {
            background: #e2eee3;
            color: #49654d;
        }

        .table-responsive {
            border: 1px solid var(--pmi-border);
            border-radius: 12px;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        table.dataTable {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            min-width: 1100px;
        }

        table.dataTable thead th {
            white-space: nowrap;
            background: var(--pmi-red) !important;
            color: #fff;
            border-bottom: 1px solid var(--pmi-red-dark) !important;
            font-weight: 650;
            font-size: .88rem;
        }

        table.dataTable tbody td {
            color: var(--pmi-text);
            vertical-align: middle;
        }

        table.dataTable tbody tr:hover {
            background-color: #fff5f6 !important;
        }

        .badge-kategori {
            display: inline-block;
            padding: .42rem .68rem;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 600;
        }

        .badge-baru {
            background: #e8f5ee;
            color: #146c43;
        }

        .badge-revisi {
            background: var(--pmi-red-soft);
            color: var(--pmi-red-dark);
        }

        .modal-content {
            border: 1px solid var(--pmi-border);
            border-radius: 16px;
            overflow: hidden;
        }

        #modalForm .modal-dialog {
            max-width: 900px;
            margin: .75rem auto;
        }

        #modalForm .modal-body {
            max-height: calc(100vh - 170px);
            overflow-y: auto;
            padding: 22px;
        }

        #modalForm .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        #modalForm .modal-body::-webkit-scrollbar-thumb {
            background: #e5a7b2;
            border-radius: 10px;
        }


        .modal-header {
            background: linear-gradient(135deg, #fff0f2, #ffffff);
            border-bottom: 1px solid var(--pmi-border);
        }

        .modal-title {
            color: var(--pmi-red-dark);
            font-weight: 700;
        }

        .form-label {
            color: #495057;
            font-weight: 600;
            font-size: .9rem;
        }

        .form-control,
        .form-select,
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #ced4da;
            border-radius: 9px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--pmi-red);
            box-shadow: 0 0 0 .2rem rgba(200, 16, 46, .15);
        }

        .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid var(--pmi-border);
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-color: #ced4da;
            border-radius: 8px;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--pmi-red);
            box-shadow: 0 0 0 .15rem rgba(200, 16, 46, .12);
            outline: none;
        }

        .select2-container {
            z-index: 2000;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 14px;
            }

            .page-header {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid py-4">
        <div class="page-header mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="title-icon">
                    <i class="bi bi-megaphone"></i>
                </div>
                <div>
                    <h4 class="page-title mb-1">Daftar Sosialisasi Dokumen</h4>
                    <div class="page-subtitle">Kelola dan dokumentasikan kegiatan sosialisasi dokumen secara tertib.
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <button type="button" class="btn btn-warm" id="btnTambah">
                            <i class="bi bi-plus-lg"></i> Buat Sosialisasi Dokumen
                        </button>
                        <a href="./sosialisasi_dokumen.php?export=excel" class="btn btn-export">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabelSosialisasi" class="table table-bordered table-striped align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Sosialisasi Dokumen</th>
                                <th>Kategori Dokumen</th>
                                <th>Nomor Dokumen</th>
                                <th>Nama Dokumen</th>
                                <th>Versi Dokumen</th>
                                <th>Tgl. Berlaku Dokumen</th>
                                <th>Sasaran Audiens</th>
                                <th>Nama Pengakses</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- ============================= MODAL FORM (Tambah / Edit) ============================= -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="formSosialisasi">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalFormTitle">Buat Sosialisasi Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="nomor_dokumen" id="nomor_dokumen">
                        <input type="hidden" name="nama_dokumen" id="nama_dokumen_hidden">
                        <input type="hidden" name="versi_dokumen" id="versi_dokumen">
                        <input type="hidden" name="tgl_berlaku_dokumen" id="tgl_berlaku_dokumen">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Sosialisasi <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_sosialisasi" id="tanggal_sosialisasi"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kategori Dokumen <span class="text-danger">*</span></label>
                                <select name="kategori_dokumen" id="kategori_dokumen" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="baru">Dokumen Baru</option>
                                    <option value="revisi">Dokumen Revisi</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Nama Pengakses</label>
                                <input type="hidden" name="nama_pengakses" id="nama_pengakses">
                                <select id="nama_pengakses_input" class="form-select" multiple></select>
                                <div class="form-text">Jika Sosialisasi untuk semua user cukup dikosongkan.</div>
                            </div>

                            <hr class="mt-4">

                            <div class="col-md-8">
                                <label class="form-label">Pilih Dokumen yang Disosialisasikan <span
                                        class="text-danger">*</span></label>
                                <select id="dokumen_id" name="dokumen_id" class="form-select" style="width:100%"
                                    required>
                                    <option value="">-- Pilih Dokumen --</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sumber Dokumen</label>
                                <input type="hidden" name="jenis_dokumen" id="jenis_dokumen_value">
                                <select id="jenis_dokumen" class="form-select" disabled>
                                    <option value="">-- Otomatis --</option>
                                    <?php foreach ($mapDokumen as $key => $cfg): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $cfg['label']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12" id="wrap_detail_dokumen" style="display:none;">
                                <div class="row g-3 p-3 rounded-3" style="background:#fffaf6;border:1px solid #eadfd5;">
                                    <div class="col-md-5">
                                        <label class="form-label">Nama Dokumen</label>
                                        <input type="text" id="nama_dokumen" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Versi Dokumen</label>
                                        <input type="text" id="versi_dokumen_view" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tgl. Berlaku Dokumen</label>
                                        <input type="text" id="tgl_berlaku_view" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Nama File Dokumen</label>
                                        <input type="text" id="file_dokumen_view" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" id="wrap_poin_perubahan" style="display:none;">
                                <label class="form-label">Poin Perubahan Dokumen <span
                                        class="text-danger">*</span></label>
                                <textarea name="poin_perubahan" id="poin_perubahan" class="form-control" rows="5"
                                    placeholder="Tuliskan poin/perubahan yang disosialisasikan pada dokumen revisi..."></textarea>
                                <div class="form-text">Isi ringkasan perubahan yang perlu diketahui peserta sosialisasi.
                                </div>
                            </div>

                            <div class="col-md-12" id="wrap_link" style="display:none;">
                                <label class="form-label">Cara Pengisian Dokumen (Khusus Formulir)</label>
                                <input type="url" name="link_cara_pengisian" id="link_cara_pengisian"
                                    class="form-control" placeholder="https://...">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ============================= /MODAL FORM ============================= -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {

            var namaPengaksesDefault = <?php echo json_encode($namaPengaksesDefault); ?>;

            // ---------------- DataTable ----------------
            var table = $('#tabelSosialisasi').DataTable({
                ajax: {
                    url: window.location.pathname + '?ajax=list',
                    dataSrc: 'data',
                    error: function(xhr) {
                        console.error('DataTables AJAX error:', xhr.status, xhr.responseText);
                        Swal.fire({
                            title: 'Gagal memuat data',
                            text: 'Server tidak mengembalikan data JSON yang valid. Silakan cek Console/Network untuk detail.',
                            icon: 'error'
                        });
                    }
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/id.json'
                },
                columns: [{
                        data: 'no'
                    },
                    {
                        data: 'tanggal_sosialisasi'
                    },
                    {
                        data: 'kategori_dokumen',
                        render: function(data) {
                            if (data === 'Dokumen Baru') {
                                return '<span class="badge-kategori badge-baru"><i class="bi bi-file-earmark-plus me-1"></i>' +
                                    data + '</span>';
                            }
                            return '<span class="badge-kategori badge-revisi"><i class="bi bi-arrow-repeat me-1"></i>' +
                                data + '</span>';
                        }
                    },
                    {
                        data: 'nomor_dokumen'
                    },
                    {
                        data: 'nama_dokumen'
                    },
                    {
                        data: 'versi_dokumen'
                    },
                    {
                        data: 'tgl_berlaku_dokumen'
                    },
                    {
                        data: 'nama_pengakses',
                        render: function(data, type, row) {
                            if (type !== 'display')
                                return data; // untuk sort/filter tetap teks polos
                            if (data === 'Semua User') {
                                return '<span class="badge bg-secondary">Semua User</span>';
                            }
                            if (row.nama_pengakses_arr && row.nama_pengakses_arr.length) {
                                return row.nama_pengakses_arr.join('<br>');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'pembaca',
                        render: function(data, type, row) {
                            if (type !== 'display') return data;
                            if (!row.pembaca_arr || !row.pembaca_arr.length) return '-';
                            return row.pembaca_arr.join('<br>');
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(id) {
                            return '<button type="button" class="btn btn-sm btn-warning btn-edit" data-id="' +
                                id + '" title="Edit">' +
                                '<i class="bi bi-pencil-square"></i></button> ' +
                                '<button type="button" class="btn btn-sm btn-danger btn-hapus" data-id="' +
                                id + '" title="Hapus">' +
                                '<i class="bi bi-trash"></i></button>';
                        }
                    }
                ]
            });

            // ---------------- Select2 (di dalam modal) ----------------
            $('#dokumen_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Dokumen --',
                width: '100%',
                dropdownParent: $('#modalForm')
            });

            $('#nama_pengakses_input').select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik nama lalu pilih dari suggest (kosongkan jika untuk semua user)',
                width: '100%',
                tags: true,
                tokenSeparators: [','],
                dropdownParent: $('#modalForm'),
                ajax: {
                    url: window.location.pathname + '?ajax=get_pengakses',
                    dataType: 'json',
                    delay: 150,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(res) {
                        return {
                            results: $.map(res, function(nama) {
                                return {
                                    id: nama,
                                    text: nama
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            function sinkronkanNamaPengakses() {
                var nama = $('#nama_pengakses_input').val() || [];

                /*
                 * Simpan sebagai JSON.
                 * Koma dalam gelar tidak akan menjadi separator.
                 */
                $('#nama_pengakses').val(JSON.stringify(nama));
            }

            $('#nama_pengakses_input').on('change', sinkronkanNamaPengakses);

            /*
             * Mengisi Select2 Nama Pengakses.
             *
             * Mendukung:
             * - data baru yang sudah JSON
             * - data lama yang masih dipisahkan koma
             */
            function isiNamaPengakses(value) {
                var daftarNama = [];
                var $select = $('#nama_pengakses_input');

                value = $.trim(value || '');

                if (value === '') {
                    $select.empty().trigger('change');
                    $('#nama_pengakses').val('[]');
                    return;
                }

                /*
                 * Coba JSON terlebih dahulu.
                 */
                try {
                    var parsed = JSON.parse(value);

                    if ($.isArray(parsed)) {
                        daftarNama = parsed;
                    }
                } catch (e) {
                    /*
                     * Compatibility untuk data lama.
                     */
                    var parts = value.split(',');
                    var buffer = '';

                    $.each(parts, function(i, part) {
                        part = $.trim(part || '');

                        if (part === '') {
                            return;
                        }

                        /*
                         * Jika bagian merupakan gelar, gabungkan ke
                         * nama sebelumnya.
                         */
                        var adalahGelar =
                            /^(Dr\.?dr\.?|Dr\.?|dr\.?|Prof\.?|S\.Si\.?|S\.Kom\.?|S\.Kep\.?|S\.Farm\.?|S\.Pd\.?|S\.T\.?|S\.E\.?|S\.H\.?|S\.Sos\.?|S\.KM\.?|S\.M\.?|M\.Si\.?|M\.Kom\.?|M\.Biomed\.?|M\.Pd\.?|M\.Kes\.?|M\.Farm\.?|M\.Kep\.?|M\.T\.?|M\.E\.?|A\.Md\.?|A\.Md\.?\s+Kes\.?|A\.Kep\.?|A\.Farm\.?|Ns\.?|Ners\.?|Sp\.?[A-Za-z.]*?)$/i
                            .test(part);

                        if (adalahGelar) {
                            if (buffer !== '') {
                                buffer += ', ' + part;
                            } else {
                                buffer = part;
                            }
                        } else {
                            if (buffer !== '') {
                                daftarNama.push($.trim(buffer));
                            }

                            buffer = part;
                        }
                    });

                    if (buffer !== '') {
                        daftarNama.push($.trim(buffer));
                    }
                }

                /*
                 * Hilangkan kosong dan duplikat.
                 */
                var hasil = [];
                var sudahAda = {};

                $.each(daftarNama, function(i, nama) {
                    nama = $.trim(nama || '');

                    if (nama !== '' && !sudahAda[nama]) {
                        hasil.push(nama);
                        sudahAda[nama] = true;
                    }
                });

                /*
                 * Select2 AJAX tidak otomatis mempunyai option
                 * untuk nama lama. Buat option secara manual.
                 */
                $select.empty();

                $.each(hasil, function(i, nama) {
                    var option = new Option(
                        nama,
                        nama,
                        true,
                        true
                    );

                    $select.append(option);
                });

                $select.val(hasil).trigger('change');

                $('#nama_pengakses').val(JSON.stringify(hasil));
            }

            function toggleLinkFormulir(jenis) {
                if (jenis === 'formulir') {
                    $('#wrap_link').stop(true, true).slideDown(150);
                    $('#link_cara_pengisian').attr('required', 'required');
                } else {
                    $('#wrap_link').stop(true, true).slideUp(150);
                    $('#link_cara_pengisian').removeAttr('required').val('');
                }
            }

            function togglePoinPerubahan(kategori) {
                if (kategori === 'revisi') {
                    $('#wrap_poin_perubahan').stop(true, true).slideDown(150);
                    $('#poin_perubahan').attr('required', 'required');
                } else {
                    $('#wrap_poin_perubahan').stop(true, true).slideUp(150);
                    $('#poin_perubahan').removeAttr('required').val('');
                }
            }

            function resetDetailDokumen() {
                $('#wrap_detail_dokumen').hide();
                $('#nomor_dokumen, #nama_dokumen_hidden, #versi_dokumen, #tgl_berlaku_dokumen').val('');
                $('#nama_dokumen, #versi_dokumen_view, #tgl_berlaku_view, #file_dokumen_view').val('');
            }

            function muatSemuaDokumen(selectedKey) {
                $.ajax({
                    url: window.location.pathname + '?ajax=get_semua_dokumen',
                    method: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        var options = '<option value="">-- Pilih Dokumen --</option>';
                        $.each(res, function(i, item) {
                            var nomor = item.nomor_dokumen ? item.nomor_dokumen : '';
                            var nama = item.nama_dokumen ? item.nama_dokumen : '';
                            var versi = item.versi_dokumen ? item.versi_dokumen : '';
                            var tglBerlaku = item.tgl_berlaku_dokumen ? item
                                .tgl_berlaku_dokumen : '';
                            var file = item.file_dokumen ? item.file_dokumen : '-';

                            var label = nomor && nama ? '[' + nomor + '] ' + nama : (nama || (
                                'Dokumen #' + item.id));
                            options += '<option value="' + item.key + '"' +
                                ' data-jenis="' + item.jenis + '"' +
                                ' data-nomor="' + $('<div>').text(nomor).html() + '"' +
                                ' data-nama="' + $('<div>').text(nama).html() + '"' +
                                ' data-versi="' + $('<div>').text(versi).html() + '"' +
                                ' data-tglberlaku="' + $('<div>').text(tglBerlaku).html() +
                                '"' +
                                ' data-file="' + $('<div>').text(file).html() +
                                '">' +
                                $('<div>').text('[' + item.jenis_label + '] ' + label).html() +
                                '</option>';
                        });
                        $('#dokumen_id').html(options).trigger('change');

                        if (selectedKey) {
                            $('#dokumen_id').val(String(selectedKey)).trigger('change');
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Gagal memuat daftar dokumen.', 'error');
                    }
                });
            }

            $('#kategori_dokumen').on('change', function() {
                togglePoinPerubahan($(this).val());
            });

            $('#dokumen_id').on('change', function() {
                var opt = $(this).find('option:selected');
                if (!opt.val()) {
                    resetDetailDokumen();
                    return;
                }
                var nomor = opt.attr('data-nomor') || '';
                var nama = opt.attr('data-nama') || '';
                var versi = opt.attr('data-versi') || '';
                var tgl = opt.attr('data-tglberlaku') || '';
                var file = opt.attr('data-file') || '-';
                var jenis = opt.attr('data-jenis') || '';
                $('#jenis_dokumen').val(jenis);
                $('#jenis_dokumen_value').val(jenis);
                toggleLinkFormulir(jenis);
                $('#nomor_dokumen').val(nomor);
                $('#nama_dokumen_hidden').val(nama);
                $('#nama_dokumen').val(nama);
                $('#versi_dokumen').val(versi);
                $('#versi_dokumen_view').val(versi);
                $('#tgl_berlaku_dokumen').val(tgl);
                $('#tgl_berlaku_view').val(tgl);
                $('#file_dokumen_view').val(file);
                $('#wrap_detail_dokumen').stop(true, true).slideDown(150);
            });

            // ---------------- Reset & buka modal Tambah ----------------
            function resetForm() {
                $('#formSosialisasi')[0].reset();
                $('#id').val('');
                $('#nomor_dokumen').val('');
                $('#nama_dokumen_hidden').val('');
                $('#versi_dokumen').val('');
                $('#tgl_berlaku_dokumen').val('');
                $('#nama_dokumen').val('');
                $('#versi_dokumen_view').val('');
                $('#tgl_berlaku_view').val('');
                $('#file_dokumen_view').val('');
                $('#jenis_dokumen').val('').prop('disabled', true);
                $('#jenis_dokumen_value').val('');
                muatSemuaDokumen(null);
                $('#wrap_link').hide();
                $('#link_cara_pengisian').removeAttr('required').val('');
                $('#wrap_poin_perubahan').hide();
                $('#poin_perubahan').removeAttr('required').val('');
                isiNamaPengakses(namaPengaksesDefault ? JSON.stringify([namaPengaksesDefault]) : '[]');
            }

            $('#btnTambah').on('click', function() {
                resetForm();
                $('#modalFormTitle').text('Buat Sosialisasi Dokumen');
                $('#modalForm').modal('show');
            });

            // ---------------- Buka modal Edit ----------------
            $('#tabelSosialisasi').on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: window.location.pathname + '?ajax=detail',
                    method: 'GET',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status !== 'success') {
                            Swal.fire('Gagal', 'Data tidak ditemukan.', 'error');
                            return;
                        }

                        resetForm();
                        var d = res.data;

                        $('#modalFormTitle').text('Edit Sosialisasi Dokumen');
                        $('#id').val(d.id);
                        $('#tanggal_sosialisasi').val(d.tanggal_sosialisasi);
                        $('#kategori_dokumen').val(d.kategori_dokumen);
                        togglePoinPerubahan(d.kategori_dokumen);
                        $('#poin_perubahan').val(d.poin_perubahan || '');
                        isiNamaPengakses(d.nama_pengakses);
                        $('#link_cara_pengisian').val(d.link_cara_pengisian);

                        toggleLinkFormulir(d.jenis_dokumen);
                        muatSemuaDokumen(d.jenis_dokumen + ':' + d.dokumen_id);

                        $('#modalForm').modal('show');
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Terjadi kesalahan server.', 'error');
                    }
                });
            });

            // ---------------- Submit form (Tambah / Edit) via AJAX ----------------
            $('#formSosialisasi').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: window.location.pathname + '?ajax=simpan',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#modalForm').modal('hide');
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal', 'Terjadi kesalahan server.', 'error');
                    }
                });
            });

            // ---------------- Hapus data ----------------
            $('#tabelSosialisasi').on('click', '.btn-hapus', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus data ini?',
                    text: 'Data yang sudah dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: window.location.pathname + '?ajax=hapus',
                            method: 'GET',
                            data: {
                                id: id
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (res.status === 'success') {
                                    Swal.fire('Terhapus!', 'Data berhasil dihapus.',
                                        'success');
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal', 'Data gagal dihapus.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Gagal', 'Terjadi kesalahan server.',
                                    'error');
                            }
                        });
                    }
                });
            });

        });
    </script>

</body>

</html>