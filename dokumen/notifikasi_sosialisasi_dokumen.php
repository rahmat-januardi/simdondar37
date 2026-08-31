<?php

/**
 * =====================================================================
 * Daftar Sosialisasi Dokumen (untuk User)
 * PHP 5.3 + ekstensi mysql_*
 *
 * Menampilkan daftar sosialisasi dokumen yang ditujukan untuk user yang
 * sedang login (dicek dari $_SESSION['nama_lengkap'] terhadap kolom
 * `nama_pengakses` pada tabel sosialisasi_dokumen), dipisah menjadi
 * 2 tab: "Belum Dibaca" dan "Sudah Dibaca".
 *
 * `nama_pengakses` bersifat OPSIONAL di form admin (sosialisasi_dokumen.php):
 * - Jika KOSONG ('' atau '[]')  -> sosialisasi berlaku untuk Semua Audiens.
 * - Jika ADA ISINYA             -> hanya untuk user yang namanya tercantum.
 *
 * Status "sudah dibaca" ditentukan dengan mengecek tabel `lacakdokumen`
 * pada kolom `keterangan`, apakah sudah ada baris dengan nama user +
 * "sudah membaca" + nomor dokumen terkait.
 *
 * Endpoint AJAX (file ini memanggil dirinya sendiri lewat ?ajax=):
 *   ?ajax=list           -> daftar dokumen per tab (GET, param: tab=belum|sudah)
 *   ?ajax=detail         -> detail 1 dokumen untuk modal (GET, param: id)
 *   ?ajax=tandai_dibaca  -> catat aktivitas "sudah membaca" ke lacakdokumen (POST, param: id)
 * =====================================================================
 */
session_start();

// Buffer semua output supaya notice/warning/whitespace tak sengaja dari
// koneksi.php atau file lain tidak ikut terkirim dan merusak response JSON.
ob_start();

include "koneksi.php";

function amankan($koneksi, $val)
{
    return mysql_real_escape_string(trim($val), $koneksi);
}

/*
 * ================================================================
 * HELPER NAMA (untuk kolom `pembaca`)
 * ================================================================
 * Sama persis dengan parser di sosialisasi_dokumen.php, supaya nama
 * dengan gelar (mis. "Arfat Lusinanto, S.Si") tidak ikut terpotong
 * jadi 2 entri terpisah saat disimpan/ditampilkan sebagai daftar.
 *
 * Data disimpan sebagai JSON array. Data lama (kalau ada, format
 * comma-string polos) tetap bisa dibaca lewat parser kompatibilitas.
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

/*
 * Mapping jenis dokumen -> tabel sumber. Disamakan dengan sosialisasi_dokumen.php
 * supaya cara membaca nomor/nama/versi/tgl berlaku/file dokumen konsisten.
 * Sesuaikan nama kolom di sini jika struktur tabel sumber Anda berbeda.
 */
$mapDokumen = array(
    'kebijakan' => array('table' => 'kebijakan', 'kolom_id' => 'nomor', 'label' => 'Kebijakan', 'kolom_no' => 'kontrol', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku'),
    'pks'       => array('table' => 'pks', 'kolom_id' => 'nomor', 'label' => 'SPO', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku'),
    'ik'        => array('table' => 'ik', 'kolom_id' => 'nomor', 'label' => 'IK (Instruksi Kerja)', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku'),
    'ika'       => array('table' => 'ika', 'kolom_id' => 'nomor', 'label' => 'IKA (Instruksi Kerja Alat)', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku'),
    'pendukung' => array('table' => 'pendukung', 'kolom_id' => 'nomor', 'label' => 'Dokumen Pendukung', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku'),
    'formulir'  => array('table' => 'formulir', 'kolom_id' => 'nomor', 'label' => 'Formulir', 'kolom_no' => 'kontrol2', 'kolom_nama' => 'nama1', 'kolom_versi' => 'no_versi', 'kolom_tgl' => 'tgl_pelaksanaan', 'kolom_file' => 'fileku'),
    'eksternal' => array('table' => 'eksternal', 'kolom_id' => 'id', 'label' => 'Dokumen Eksternal', 'kolom_no' => 'no_tahun_dokumen', 'kolom_nama' => 'nama', 'kolom_versi' => 'tingkat', 'kolom_tgl' => '', 'kolom_file' => 'fileku'),
);

/*
 * Folder tempat file dokumen (kolom `fileku`) disimpan.
 * File ini sendiri berada di ./dokumen/, dan folder upload ada di
 * ./dokumen/upload/ -> jadi path relatif (untuk URL di browser) cukup
 * './upload/', dan path fisik (untuk cek file_exists di server) dihitung
 * dari lokasi file PHP ini sendiri (__DIR__).
 */
define('FOLDER_DOKUMEN', './upload/');
define('FOLDER_DOKUMEN_FISIK', __DIR__ . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR);

function ambilDetailDokumen($koneksi, $mapDokumen, $jenis, $dokumenId, &$debug = null)
{
    if (!isset($mapDokumen[$jenis])) {
        $debug = array('alasan' => 'jenis_dokumen "' . $jenis . '" tidak ada di $mapDokumen.');
        return array();
    }
    if ((int) $dokumenId < 1) {
        $debug = array('alasan' => 'dokumen_id tidak valid: ' . var_export($dokumenId, true));
        return array();
    }

    $cfg = $mapDokumen[$jenis];
    $id = (int) $dokumenId;
    $sql = "SELECT * FROM `" . $cfg['table'] . "` WHERE `" . $cfg['kolom_id'] . "` = " . $id . " LIMIT 1";
    $res = mysql_query($sql, $koneksi);

    if (!$res) {
        $debug = array(
            'alasan'      => 'Query SQL gagal (kemungkinan nama tabel/kolom di $mapDokumen salah).',
            'sql'         => $sql,
            'mysql_error' => mysql_error($koneksi)
        );
        return array();
    }

    $row = mysql_fetch_assoc($res);
    if (!$row) {
        $debug = array(
            'alasan' => 'Query berhasil, tapi tidak ada baris dengan `' . $cfg['kolom_id'] . '` = ' . $id . ' di tabel `' . $cfg['table'] . '`.',
            'sql'    => $sql
        );
        return array();
    }

    $debug = array('alasan' => 'OK, data ditemukan.', 'sql' => $sql, 'kolom_tersedia' => array_keys($row));

    $file = '';
    $kolomFile = array($cfg['kolom_file'], 'fileku', 'file', 'nama_file', 'filename');
    foreach ($kolomFile as $namaKolomFile) {
        if ($namaKolomFile !== '' && isset($row[$namaKolomFile]) && $row[$namaKolomFile] !== '') {
            $file = $row[$namaKolomFile];
            break;
        }
    }

    return array(
        'jenis_label' => $cfg['label'],
        'nomor_dokumen' => isset($row[$cfg['kolom_no']]) ? $row[$cfg['kolom_no']] : '-',
        'nama_dokumen' => isset($row[$cfg['kolom_nama']]) ? $row[$cfg['kolom_nama']] : '-',
        'versi_dokumen' => isset($row[$cfg['kolom_versi']]) ? $row[$cfg['kolom_versi']] : '-',
        'tgl_berlaku_dokumen' => ($cfg['kolom_tgl'] !== '' && isset($row[$cfg['kolom_tgl']])) ? $row[$cfg['kolom_tgl']] : '',
        'file_dokumen' => $file
    );
}

/**
 * Cek apakah nilai nama_pengakses berarti "berlaku untuk Semua Audiens"
 * (kosong, atau JSON array kosong '[]').
 */
function pengaksesUntukSemua($namaPengaksesString)
{
    $v = trim($namaPengaksesString);
    if ($v === '' || $v === '[]') return true;

    $decoded = json_decode($v, true);
    if (is_array($decoded) && count($decoded) === 0) return true;

    return false;
}

/**
 * Cek apakah $namaSaya "ada" di kolom nama_pengakses, pakai pendekatan LIKE
 * (substring, case-insensitive) - konsisten dengan query SQL yang dipakai
 * untuk mengambil daftar sosialisasi dokumen milik user.
 * Kalau nama_pengakses kosong -> berarti sosialisasi untuk Semua Audiens,
 * jadi otomatis dianggap cocok untuk siapa saja.
 */
function namaAdaDiPengakses($namaPengaksesString, $namaSaya)
{
    if (pengaksesUntukSemua($namaPengaksesString)) return true;

    $namaSaya = trim($namaSaya);
    if ($namaSaya === '') return false;
    return stripos($namaPengaksesString, $namaSaya) !== false;
}

/**
 * Cek apakah user sudah membaca dokumen dengan nomor tertentu, dilihat
 * dari tabel lacakdokumen kolom `keterangan`.
 */
function markerSosialisasi($idSosialisasi)
{
    $id = (int) $idSosialisasi;
    if ($id < 1) {
        return '';
    }

    return '[sosialisasi_id=' . $id . ']';
}

function sudahDibaca($koneksi, $namaSaya, $nomorDokumen, $idSosialisasi = null)
{
    $namaE = amankan($koneksi, $namaSaya);
    $nomorE = amankan($koneksi, $nomorDokumen);
    if ($namaE === '' || $nomorE === '') return false;

    $idSosialisasi = (int) $idSosialisasi;
    $suffixWhere = "";

    if ($idSosialisasi > 0) {
        $marker = markerSosialisasi($idSosialisasi);
        $suffixWhere = " AND keterangan LIKE '%" . amankan($koneksi, $marker) . "%'";
    } else {
        $suffixWhere = " AND keterangan LIKE '%" . $nomorE . "%'";
    }

    $sql = "SELECT COUNT(*) AS jml FROM lacakdokumen
            WHERE nama_pengakses = '" . $namaE . "'
              AND keterangan LIKE '%sudah membaca%'
              " . $suffixWhere . "";
    $res = mysql_query($sql, $koneksi);
    if ($res && ($row = mysql_fetch_assoc($res))) {
        return ((int) $row['jml']) > 0;
    }
    return false;
}

function buatKeterangan($namaSaya, $kategoriDokumen, $nomorDokumen, $idSosialisasi = null)
{
    $marker = markerSosialisasi($idSosialisasi);
    $keterangan = $namaSaya . ' sudah membaca Sosialisasi dokumen ' . $kategoriDokumen . ' dengan nomor ' . $nomorDokumen;

    if ($marker !== '') {
        $keterangan .= ' ' . $marker;
    }

    return $keterangan;
}

/**
 * Tambahkan nama user ke kolom `pembaca` pada tabel sosialisasi_dokumen,
 * tanpa menghapus nama yang sudah ada dan tanpa duplikat (cek case-insensitive).
 * Disimpan sebagai JSON array (sama seperti kolom nama_pengakses) supaya
 * nama yang mengandung gelar (mis. "Arfat Lusinanto, S.Si") tidak ikut
 * terpotong jadi 2 entri terpisah.
 */
function tambahPembaca($koneksi, $idSosialisasi, $namaSaya)
{
    $namaSaya = trim($namaSaya);
    $idSosialisasi = (int) $idSosialisasi;
    if ($namaSaya === '' || $idSosialisasi < 1) return;

    $res = mysql_query("SELECT pembaca FROM sosialisasi_dokumen WHERE id = " . $idSosialisasi . " LIMIT 1", $koneksi);
    if (!$res) return;
    $row = mysql_fetch_assoc($res);
    if (!$row) return;

    $daftar = normalisasiPengakses(isset($row['pembaca']) ? $row['pembaca'] : '');

    // Cek apakah nama sudah ada (case-insensitive) supaya tidak double
    $sudahAda = false;
    foreach ($daftar as $d) {
        if (strcasecmp($d, $namaSaya) === 0) {
            $sudahAda = true;
            break;
        }
    }

    if (!$sudahAda) {
        $daftar[] = $namaSaya;
        $pembacaBaruJson = json_encode($daftar);
        $pembacaBaruE    = amankan($koneksi, $pembacaBaruJson);
        mysql_query("UPDATE sosialisasi_dokumen SET pembaca = '" . $pembacaBaruE . "' WHERE id = " . $idSosialisasi, $koneksi);
    }
}

/**
 * Cek apakah file dokumen (kolom `fileku`) benar-benar ada secara fisik
 * di folder dokumen/upload di server (bukan cuma tercatat di database).
 */
function fileDokumenAda($namaFile)
{
    $namaFile = trim($namaFile);
    if ($namaFile === '') return false;
    $pathFisik = FOLDER_DOKUMEN_FISIK . $namaFile;
    return is_file($pathFisik);
}

/**
 * Ambil seluruh sosialisasi_dokumen yang berlaku untuk user ini: baik yang
 * secara khusus mencantumkan namanya di nama_pengakses (LIKE), maupun yang
 * nama_pengakses-nya dikosongkan (berarti berlaku untuk Semua Audiens).
 * Hasilnya dipisah menjadi belum dibaca / sudah dibaca.
 */
function ambilDaftarSosialisasiSaya($koneksi, $mapDokumen, $namaSaya)
{
    $hasil = array('belum' => array(), 'sudah' => array());
    if (trim($namaSaya) === '') return $hasil;

    $namaE = amankan($koneksi, $namaSaya);
    $sql = "SELECT * FROM sosialisasi_dokumen
            WHERE nama_pengakses LIKE '%" . $namaE . "%'
               OR nama_pengakses = ''
               OR nama_pengakses = '[]'
               OR nama_pengakses IS NULL
            ORDER BY tanggal_sosialisasi DESC, id DESC";
    $res = mysql_query($sql, $koneksi);
    if (!$res) return $hasil;

    while ($row = mysql_fetch_assoc($res)) {
        if (!namaAdaDiPengakses($row['nama_pengakses'], $namaSaya)) continue;

        $detail = ambilDetailDokumen($koneksi, $mapDokumen, $row['jenis_dokumen'], $row['dokumen_id']);
        if (empty($detail)) continue;

        $item = array(
            'id'               => $row['id'],
            'nomor_dokumen'    => $detail['nomor_dokumen'],
            'nama_dokumen'     => $detail['nama_dokumen'],
            'kategori_dokumen' => $row['kategori_dokumen'],
            'untuk_semua'      => pengaksesUntukSemua($row['nama_pengakses']),
        );

        if (sudahDibaca($koneksi, $namaSaya, $detail['nomor_dokumen'], $row['id'])) {
            $hasil['sudah'][] = $item;
        } else {
            $hasil['belum'][] = $item;
        }
    }
    return $hasil;
}

$namaSaya = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : '';
$ajax     = isset($_GET['ajax']) ? $_GET['ajax'] : '';

/* =====================================================================
 * ENDPOINT (SEMENTARA, UNTUK TROUBLESHOOTING): ?ajax=debug&id=xxx
 * Menampilkan proses pengecekan 1 baris sosialisasi_dokumen secara detail:
 * apakah nama Anda cocok di nama_pengakses, query ke tabel sumber dokumen,
 * dan apakah dianggap sudah/belum dibaca. HAPUS blok ini setelah selesai
 * troubleshooting supaya tidak dipakai orang lain untuk melihat isi query.
 * ===================================================================== */
if ($ajax === 'debug') {
    ob_end_clean();
    header('Content-Type: application/json');

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $out = array('session_nama_lengkap' => $namaSaya);

    $res = mysql_query("SELECT * FROM sosialisasi_dokumen WHERE id = " . $id . " LIMIT 1", $koneksi);
    if (!$res || mysql_num_rows($res) < 1) {
        $out['error'] = 'Baris sosialisasi_dokumen dengan id=' . $id . ' tidak ditemukan.';
        echo json_encode($out);
        exit;
    }
    $row = mysql_fetch_assoc($res);
    $out['row_sosialisasi_dokumen'] = $row;

    $daftarNama = explode(',', $row['nama_pengakses']);
    $daftarNamaTrim = array();
    foreach ($daftarNama as $n) $daftarNamaTrim[] = trim($n);
    $out['daftar_nama_pengakses_setelah_split'] = $daftarNamaTrim;
    $out['nama_cocok'] = namaAdaDiPengakses($row['nama_pengakses'], $namaSaya);

    $debugDetail = null;
    $detail = ambilDetailDokumen($koneksi, $mapDokumen, $row['jenis_dokumen'], $row['dokumen_id'], $debugDetail);
    $out['mapping_dipakai'] = isset($mapDokumen[$row['jenis_dokumen']]) ? $mapDokumen[$row['jenis_dokumen']] : null;
    $out['hasil_ambilDetailDokumen'] = $detail;
    $out['debug_ambilDetailDokumen'] = $debugDetail;

    if (!empty($detail)) {
        $out['status_sudah_dibaca'] = sudahDibaca($koneksi, $namaSaya, $detail['nomor_dokumen'], $row['id']);
    }

    // JSON_PRETTY_PRINT baru ada di PHP 5.4+, jadi tidak dipakai di sini (server pakai PHP 5.3)
    echo json_encode($out);
    exit;
}

/* =====================================================================
 * ENDPOINT: ?ajax=list&tab=belum|sudah
 * ===================================================================== */
if ($ajax === 'list') {
    ob_end_clean();
    header('Content-Type: application/json');
    $tab    = isset($_GET['tab']) ? $_GET['tab'] : 'belum';
    $daftar = ambilDaftarSosialisasiSaya($koneksi, $mapDokumen, $namaSaya);
    $data   = ($tab === 'sudah') ? $daftar['sudah'] : $daftar['belum'];
    echo json_encode(array('status' => 'success', 'data' => array_values($data)));
    exit;
}

/* =====================================================================
 * ENDPOINT: ?ajax=detail&id=xxx -> detail dokumen untuk modal
 * ===================================================================== */
if ($ajax === 'detail') {
    ob_end_clean();
    header('Content-Type: application/json');
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    $res = mysql_query("SELECT * FROM sosialisasi_dokumen WHERE id = " . $id . " LIMIT 1", $koneksi);
    if (!$res || mysql_num_rows($res) < 1) {
        echo json_encode(array('status' => 'error', 'message' => 'Data tidak ditemukan.'));
        exit;
    }
    $row = mysql_fetch_assoc($res);

    if (!namaAdaDiPengakses($row['nama_pengakses'], $namaSaya)) {
        echo json_encode(array('status' => 'error', 'message' => 'Anda tidak memiliki akses ke dokumen ini.'));
        exit;
    }

    $detail = ambilDetailDokumen($koneksi, $mapDokumen, $row['jenis_dokumen'], $row['dokumen_id']);
    if (empty($detail)) {
        echo json_encode(array('status' => 'error', 'message' => 'Detail dokumen sumber tidak ditemukan.'));
        exit;
    }

    $tglBerlaku = (!empty($detail['tgl_berlaku_dokumen']) && $detail['tgl_berlaku_dokumen'] != '0000-00-00')
        ? date('d-m-Y', strtotime($detail['tgl_berlaku_dokumen']))
        : '-';

    $fileUrl = !empty($detail['file_dokumen']) ? FOLDER_DOKUMEN . rawurlencode($detail['file_dokumen']) : '';
    $fileAda = fileDokumenAda($detail['file_dokumen']);

    echo json_encode(array(
        'status' => 'success',
        'data'   => array(
            'id'                  => $row['id'],
            'jenis_dokumen'       => $row['jenis_dokumen'],
            'jenis_label'         => $detail['jenis_label'],
            'kategori_dokumen'    => $row['kategori_dokumen'],
            'poin_perubahan'      => $row['poin_perubahan'],
            'link_cara_pengisian' => $row['link_cara_pengisian'],
            'nama_dokumen'        => $detail['nama_dokumen'],
            'nomor_dokumen'       => $detail['nomor_dokumen'],
            'versi_dokumen'       => $detail['versi_dokumen'],
            'tgl_berlaku_dokumen' => $tglBerlaku,
            'file_url'            => $fileUrl,
            'file_ada'            => $fileAda,
            'sudah_dibaca'        => sudahDibaca($koneksi, $namaSaya, $detail['nomor_dokumen'], $row['id']),
        )
    ));
    exit;
}

/* =====================================================================
 * ENDPOINT: ?ajax=tandai_dibaca -> insert log ke lacakdokumen
 * Dipanggil saat tombol "Selesai" dikonfirmasi ("Ya") setelah user
 * mencentang pernyataan telah membaca & memahami dokumen.
 * ===================================================================== */
if ($ajax === 'tandai_dibaca' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_end_clean();
    header('Content-Type: application/json');
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id < 1 || trim($namaSaya) === '') {
        echo json_encode(array('status' => 'error', 'message' => 'Sesi login tidak valid.'));
        exit;
    }

    $res = mysql_query("SELECT * FROM sosialisasi_dokumen WHERE id = " . $id . " LIMIT 1", $koneksi);
    if (!$res || mysql_num_rows($res) < 1) {
        echo json_encode(array('status' => 'error', 'message' => 'Data tidak ditemukan.'));
        exit;
    }
    $row = mysql_fetch_assoc($res);

    if (!namaAdaDiPengakses($row['nama_pengakses'], $namaSaya)) {
        echo json_encode(array('status' => 'error', 'message' => 'Anda tidak memiliki akses ke dokumen ini.'));
        exit;
    }

    $detail = ambilDetailDokumen($koneksi, $mapDokumen, $row['jenis_dokumen'], $row['dokumen_id']);
    if (empty($detail)) {
        echo json_encode(array('status' => 'error', 'message' => 'Detail dokumen sumber tidak ditemukan.'));
        exit;
    }

    // Sudah pernah tercatat sebelumnya -> tidak perlu insert lagi (idempotent),
    // tapi tetap pastikan namanya ada di kolom pembaca (jaga-jaga data lama belum sinkron).
    if (sudahDibaca($koneksi, $namaSaya, $detail['nomor_dokumen'], $row['id'])) {
        tambahPembaca($koneksi, $id, $namaSaya);
        echo json_encode(array('status' => 'success', 'message' => 'Sudah tercatat sebelumnya.'));
        exit;
    }

    $namaDokumenGabung = trim($detail['nomor_dokumen'] . ' ' . $detail['nama_dokumen']);
    $keterangan         = buatKeterangan($namaSaya, $row['kategori_dokumen'], $detail['nomor_dokumen'], $row['id']);

    $notrans = date('YmdHis') . '-' . strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $namaSaya));
    $notrans = substr($notrans, 0, 50);

    $notransE       = amankan($koneksi, $notrans);
    $namaSayaE      = amankan($koneksi, $namaSaya);
    $levelE         = amankan($koneksi, isset($_SESSION['level']) ? $_SESSION['level'] : '-');
    $namaDokumenE   = amankan($koneksi, $namaDokumenGabung);
    $keteranganE    = amankan($koneksi, $keterangan);
    $now            = date('Y-m-d H:i:s');

    $sqlInsert = "INSERT INTO lacakdokumen
                    (notrans, nama_pengakses, level_pengakses, tanggal_akses, nama_dokumen, keterangan)
                  VALUES
                    ('" . $notransE . "', '" . $namaSayaE . "', '" . $levelE . "', '" . $now . "', '" . $namaDokumenE . "', '" . $keteranganE . "')";
    $exec = mysql_query($sqlInsert, $koneksi);

    if ($exec) {
        tambahPembaca($koneksi, $id, $namaSaya);
        echo json_encode(array('status' => 'success', 'message' => 'Berhasil dicatat.'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Gagal menyimpan: ' . mysql_error()));
    }
    exit;
}

/* =====================================================================
 * HALAMAN UTAMA (bukan AJAX)
 * ===================================================================== */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Sosialisasi Dokumen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    :root {
        --warm-primary: #9a5b35;
        --warm-primary-dark: #784329;
        --warm-accent: #d7a26e;
        --warm-bg: #fbf7f2;
        --warm-surface: #ffffff;
        --warm-border: #eadfd5;
        --warm-text: #3f342d;
        --warm-muted: #806f64;
        --warm-success: #5f7d63;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(215, 162, 110, .14), transparent 28%),
            linear-gradient(135deg, #fbf7f2 0%, #f7f1eb 100%);
        color: var(--warm-text);
        min-height: 100vh;
    }

    .page-header {
        background: rgba(255, 255, 255, .88);
        border: 1px solid var(--warm-border);
        border-radius: 16px;
        padding: 18px 22px;
        box-shadow: 0 5px 20px rgba(92, 59, 37, .07);
    }

    .page-title {
        font-weight: 700;
        color: var(--warm-primary-dark);
        letter-spacing: -.2px;
    }

    .page-subtitle {
        color: var(--warm-muted);
        font-size: .9rem;
    }

    .title-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f1dfcf;
        color: var(--warm-primary);
        font-size: 1.25rem;
        flex: 0 0 auto;
    }

    .card {
        border: 1px solid var(--warm-border);
        border-radius: 16px;
        background: var(--warm-surface);
        box-shadow: 0 8px 28px rgba(92, 59, 37, .08);
        overflow: hidden;
    }

    .card-body {
        padding: 22px;
    }

    /* ---- Tabs ---- */
    .nav-tabs-warm {
        border-bottom: 1px solid var(--warm-border);
        gap: 4px;
    }

    .nav-tabs-warm .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--warm-muted);
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 10px 10px 0 0;
    }

    .nav-tabs-warm .nav-link .badge {
        background: #f1dfcf;
        color: var(--warm-primary-dark);
        font-weight: 700;
    }

    .nav-tabs-warm .nav-link.active {
        color: var(--warm-primary-dark);
        border-bottom-color: var(--warm-primary);
        background: #fff9f4;
    }

    .nav-tabs-warm .nav-link.active .badge {
        background: var(--warm-primary);
        color: #fff;
    }

    .table-responsive {
        border: 1px solid var(--warm-border);
        border-radius: 12px;
        overflow: hidden;
    }

    table.table-warm {
        margin-bottom: 0;
    }

    table.table-warm thead th {
        white-space: nowrap;
        background: #f7efe8;
        color: #59463a;
        border-bottom: 1px solid var(--warm-border);
        font-weight: 650;
        font-size: .88rem;
    }

    table.table-warm tbody td {
        color: #4b4039;
        vertical-align: middle;
    }

    table.table-warm tbody tr:hover {
        background-color: #fff9f4;
    }

    .badge-kategori {
        display: inline-block;
        padding: .42rem .68rem;
        border-radius: 999px;
        font-size: .76rem;
        font-weight: 600;
    }

    .badge-baru {
        background: #f7e7d7;
        color: #8a4f2c;
    }

    .badge-revisi {
        background: #ece7f4;
        color: #67527f;
    }

    .badge-sudah {
        background: #e5f0e6;
        color: var(--warm-success);
        padding: .42rem .68rem;
        border-radius: 999px;
        font-size: .76rem;
        font-weight: 600;
    }

    .btn-warm {
        background: var(--warm-primary);
        border-color: var(--warm-primary);
        color: #fff;
    }

    .btn-warm:hover,
    .btn-warm:focus {
        background: var(--warm-primary-dark);
        border-color: var(--warm-primary-dark);
        color: #fff;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--warm-muted);
    }

    .empty-state i {
        font-size: 2.2rem;
        color: var(--warm-accent);
        display: block;
        margin-bottom: 10px;
    }

    /* ---- Modal detail dokumen ---- */
    .modal-content {
        border: 1px solid var(--warm-border);
        border-radius: 16px;
        overflow: hidden;
    }

    #modalDetail .modal-dialog {
        max-width: 640px;
    }

    .modal-header {
        background: linear-gradient(135deg, #f8eee5, #fffaf6);
        border-bottom: 1px solid var(--warm-border);
    }

    .modal-title {
        color: var(--warm-primary-dark);
        font-weight: 700;
    }

    .form-label {
        color: #5b473a;
        font-weight: 600;
        font-size: .9rem;
    }

    .form-control[readonly] {
        background-color: #fbf7f2;
        border-color: #ddcec2;
        border-radius: 9px;
    }

    .modal-footer {
        background: #fcf8f4;
        border-top: 1px solid var(--warm-border);
    }

    .btn-aksi-dokumen {
        width: 100%;
        min-height: 58px;
        border-radius: 12px;
        font-weight: 600;
        border: 1px solid var(--warm-border);
        background: #fff;
        color: var(--warm-primary-dark);
    }

    .btn-aksi-dokumen:hover:not(:disabled) {
        background: #fff3e9;
        border-color: var(--warm-accent);
    }

    .btn-aksi-dokumen:disabled {
        background: #f0ede9;
        color: #a79f95;
        border-color: #e2ddd6;
        cursor: not-allowed;
        opacity: 1;
    }

    .box-poin {
        background: #fffaf6;
        border: 1px solid #eadfd5;
        border-radius: 12px;
        padding: 12px 14px;
        white-space: pre-line;
        font-size: .92rem;
    }

    .form-check-konfirmasi {
        background: #fff9f4;
        border: 1px solid var(--warm-border);
        border-radius: 12px;
        padding: 14px;
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
                    <div class="page-subtitle">Daftar sosialisasi dokumen yang ditujukan untuk Anda.</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <?php if (trim($namaSaya) === ''): ?>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Sesi login tidak ditemukan. Silakan login kembali untuk melihat daftar sosialisasi dokumen Anda.
                </div>
                <?php else: ?>

                <ul class="nav nav-tabs nav-tabs-warm mb-3" id="tabSosialisasi" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-belum-btn" data-bs-toggle="tab"
                            data-bs-target="#tab-belum" type="button" role="tab">
                            Belum Dibaca <span class="badge rounded-pill ms-1" id="countBelum">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-sudah-btn" data-bs-toggle="tab" data-bs-target="#tab-sudah"
                            type="button" role="tab">
                            Sudah Dibaca <span class="badge rounded-pill ms-1" id="countSudah">0</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="tabSosialisasiContent">

                    <!-- ===================== TAB BELUM DIBACA ===================== -->
                    <div class="tab-pane fade show active" id="tab-belum" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-warm align-middle w-100">
                                <thead>
                                    <tr>
                                        <th style="width:30%">Nomor Dokumen</th>
                                        <th>Nama Dokumen</th>
                                        <th style="width:140px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyBelum">
                                    <tr>
                                        <td colspan="3" class="text-center py-4">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ===================== TAB SUDAH DIBACA ===================== -->
                    <div class="tab-pane fade" id="tab-sudah" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-warm align-middle w-100">
                                <thead>
                                    <tr>
                                        <th style="width:30%">Nomor Dokumen</th>
                                        <th>Nama Dokumen</th>
                                        <th style="width:140px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodySudah">
                                    <tr>
                                        <td colspan="3" class="text-center py-4">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- ============================= MODAL DETAIL SOSIALISASI DOKUMEN ============================= -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Sosialisasi Dokumen <span id="lblKategori">-</span></h5>
                        <span class="badge-kategori" id="badgeKategori">-</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="detailId">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama Dokumen</label>
                            <input type="text" id="detailNamaDokumen" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Dokumen</label>
                            <input type="text" id="detailNomorDokumen" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Versi Dokumen</label>
                            <input type="text" id="detailVersiDokumen" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tgl. Berlaku</label>
                            <input type="text" id="detailTglBerlaku" class="form-control" readonly>
                        </div>

                        <div class="col-md-12" id="wrapPoinPerubahan" style="display:none;">
                            <label class="form-label">Poin Utama Perubahan Dokumen</label>
                            <div class="box-poin" id="detailPoinPerubahan">-</div>
                        </div>

                        <div class="col-6">
                            <button type="button" class="btn-aksi-dokumen" id="btnTampilkanDokumen">
                                <i class="bi bi-file-earmark-text d-block mb-1"></i>
                                Tampilkan Dokumen
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn-aksi-dokumen" id="btnCaraPengisian">
                                <i class="bi bi-question-circle d-block mb-1"></i>
                                Cara Pengisian Dokumen <br><small>(khusus formulir)</small>
                            </button>
                        </div>

                        <div class="col-md-12" id="wrapKonfirmasi">
                            <div class="form-check-konfirmasi">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chkKonfirmasi">
                                    <label class="form-check-label" for="chkKonfirmasi">
                                        Dengan mengklik tombol di bawah ini, saya menyatakan bahwa saya telah membaca
                                        dan memahami isi dokumen.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="wrapSudahDibaca" style="display:none;">
                            <span class="badge-sudah"><i class="bi bi-check-circle me-1"></i> Anda sudah membaca
                                dokumen ini.</span>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" id="footerBelum">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-warm" id="btnSelesai" disabled>
                        <i class="bi bi-check-lg"></i> Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================= /MODAL DETAIL ============================= -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(function() {

        var modalDetail = new bootstrap.Modal(document.getElementById('modalDetail'));
        var dataSaatIni = null; // data dokumen yang sedang ditampilkan di modal
        var modeSudahDibaca = false; // true kalau dokumen ini sudah tercatat "sudah dibaca"

        /* ---------------- Muat daftar tabel (Belum / Sudah Dibaca) ---------------- */
        function renderBaris(item, tab) {
            var badgeKategori = (item.kategori_dokumen === 'baru') ?
                '<span class="badge-kategori badge-baru">Baru</span>' :
                '<span class="badge-kategori badge-revisi">Revisi</span>';

            var badgeSemua = item.untuk_semua ?
                ' <span class="badge bg-secondary">Semua Audiens</span>' : '';

            var labelTombol = (tab === 'sudah') ? 'Lihat Dokumen' : 'Tampilkan';

            return '<tr>' +
                '<td>' + $('<div>').text(item.nomor_dokumen).html() + '</td>' +
                '<td>' + $('<div>').text(item.nama_dokumen).html() + ' ' + badgeKategori + badgeSemua +
                '</td>' +
                '<td><button type="button" class="btn btn-sm btn-warm btn-buka-detail" data-id="' + item.id +
                '" data-tab="' + tab + '">' + labelTombol + '</button></td>' +
                '</tr>';
        }

        function muatDaftar(tab) {
            var $tbody = (tab === 'sudah') ? $('#tbodySudah') : $('#tbodyBelum');

            $.ajax({
                url: window.location.pathname + '?ajax=list',
                method: 'GET',
                data: {
                    tab: tab
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status !== 'success') {
                        $tbody.html(
                            '<tr><td colspan="3" class="text-center text-danger py-4">Gagal memuat data.</td></tr>'
                        );
                        return;
                    }

                    if (tab === 'sudah') {
                        $('#countSudah').text(res.data.length);
                    } else {
                        $('#countBelum').text(res.data.length);
                    }

                    if (res.data.length === 0) {
                        var pesan = (tab === 'sudah') ?
                            'Belum ada dokumen yang sudah Anda baca.' :
                            'Tidak ada daftar sosialisasi dokumen yang perlu dibaca.';
                        $tbody.html('<tr><td colspan="3">' +
                            '<div class="empty-state"><i class="bi bi-inbox"></i>' + pesan +
                            '</div>' +
                            '</td></tr>');
                        return;
                    }

                    var html = '';
                    $.each(res.data, function(i, item) {
                        html += renderBaris(item, tab);
                    });
                    $tbody.html(html);
                },
                error: function() {
                    $tbody.html(
                        '<tr><td colspan="3" class="text-center text-danger py-4">Terjadi kesalahan server.</td></tr>'
                    );
                }
            });
        }

        function muatSemuaTab() {
            muatDaftar('belum');
            muatDaftar('sudah');
        }

        <?php if (trim($namaSaya) !== ''): ?>
        muatSemuaTab();
        <?php endif; ?>

        /* ---------------- Buka modal detail ---------------- */
        $(document).on('click', '.btn-buka-detail', function() {
            var id = $(this).data('id');
            bukaModalDetail(id);
        });

        function resetModal() {
            dataSaatIni = null;
            modeSudahDibaca = false;
            $('#detailNamaDokumen, #detailNomorDokumen, #detailVersiDokumen, #detailTglBerlaku').val('');
            $('#detailPoinPerubahan').text('-');
            $('#wrapPoinPerubahan').hide();
            $('#chkKonfirmasi').prop('checked', false);
            $('#btnSelesai').prop('disabled', true);
            $('#btnTampilkanDokumen').prop('disabled', false);
            $('#btnCaraPengisian').prop('disabled', true);
            $('#wrapSudahDibaca').hide();
            $('#wrapKonfirmasi').show();
            $('#footerBelum').show();
        }

        function bukaModalDetail(id) {
            resetModal();

            $.ajax({
                url: window.location.pathname + '?ajax=detail',
                method: 'GET',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status !== 'success') {
                        Swal.fire('Gagal', res.message || 'Data tidak ditemukan.', 'error');
                        return;
                    }

                    var d = res.data;
                    dataSaatIni = d;

                    $('#detailId').val(d.id);
                    $('#lblKategori').text(d.kategori_dokumen === 'baru' ? 'Baru' : 'Revisi');
                    $('#badgeKategori').text(d.kategori_dokumen === 'baru' ? 'Dokumen Baru' :
                        'Dokumen Revisi');
                    $('#badgeKategori').attr('class', 'badge-kategori ' + (d.kategori_dokumen ===
                        'baru' ? 'badge-baru' : 'badge-revisi'));

                    $('#detailNamaDokumen').val(d.nama_dokumen);
                    $('#detailNomorDokumen').val(d.nomor_dokumen);
                    $('#detailVersiDokumen').val(d.versi_dokumen);
                    $('#detailTglBerlaku').val(d.tgl_berlaku_dokumen);

                    if (d.kategori_dokumen === 'revisi' && d.poin_perubahan) {
                        $('#detailPoinPerubahan').text(d.poin_perubahan);
                        $('#wrapPoinPerubahan').show();
                    } else {
                        $('#wrapPoinPerubahan').hide();
                    }

                    // Tombol "Cara Pengisian Dokumen" hanya aktif untuk formulir yang punya link.
                    // Jika kosong / bukan formulir -> abu-abu & tidak bisa diklik.
                    var adaLinkFormulir = (d.jenis_dokumen === 'formulir' && d
                        .link_cara_pengisian &&
                        d.link_cara_pengisian !== '');
                    $('#btnCaraPengisian').prop('disabled', !adaLinkFormulir);

                    if (d.sudah_dibaca) {
                        // Mode "Sudah Dibaca": tampilkan badge, sembunyikan checklist & tombol Selesai
                        modeSudahDibaca = true;
                        $('#wrapKonfirmasi').hide();
                        $('#wrapSudahDibaca').show();
                        $('#footerBelum').hide();
                    }

                    modalDetail.show();
                },
                error: function() {
                    Swal.fire('Gagal', 'Terjadi kesalahan server.', 'error');
                }
            });
        }

        /* ---------------- Tombol "Tampilkan Dokumen" ---------------- */
        // Ambil nama file dari kolom `fileku` (dikirim backend sebagai file_url),
        // cek apakah filenya benar-benar ada di folder dokumen/upload (file_ada),
        // baru dibuka di tab baru. Tidak mencatat "sudah membaca" di sini -
        // pencatatan hanya terjadi lewat tombol "Selesai".
        $('#btnTampilkanDokumen').on('click', function() {
            if (!dataSaatIni) return;

            if (!dataSaatIni.file_url) {
                Swal.fire('Info', 'File dokumen belum tersedia.', 'info');
                return;
            }

            if (!dataSaatIni.file_ada) {
                Swal.fire('File tidak ditemukan',
                    'File dokumen tidak ditemukan di folder upload. Silakan hubungi admin.',
                    'warning');
                return;
            }

            window.open(dataSaatIni.file_url, '_blank');
        });

        /* ---------------- Tombol "Cara Pengisian Dokumen" ---------------- */
        $('#btnCaraPengisian').on('click', function() {
            if ($(this).prop('disabled')) return;
            if (dataSaatIni && dataSaatIni.link_cara_pengisian) {
                window.open(dataSaatIni.link_cara_pengisian, '_blank');
            }
        });

        /* ---------------- Checkbox konfirmasi ---------------- */
        $('#chkKonfirmasi').on('change', function() {
            $('#btnSelesai').prop('disabled', !$(this).is(':checked'));
        });

        /* ---------------- Catat "sudah membaca" ke lacakdokumen ---------------- */
        function catatSudahDibaca(id, tampilkanNotifikasi) {
            $.ajax({
                url: window.location.pathname + '?ajax=tandai_dibaca',
                method: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        muatSemuaTab();
                        if (tampilkanNotifikasi) {
                            modalDetail.hide();
                            Swal.fire({
                                title: 'Terima kasih.',
                                text: 'Anda telah membaca dan memahami dokumen yang telah disosialisasikan.',
                                icon: 'success'
                            });
                        }
                    } else if (tampilkanNotifikasi) {
                        Swal.fire('Gagal', res.message || 'Gagal menyimpan data.', 'error');
                    }
                },
                error: function() {
                    if (tampilkanNotifikasi) {
                        Swal.fire('Gagal', 'Terjadi kesalahan server.', 'error');
                    }
                }
            });
        }

        /* ---------------- Tombol "Selesai" ---------------- */
        $('#btnSelesai').on('click', function() {
            if (!dataSaatIni || !$('#chkKonfirmasi').is(':checked')) return;

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Pastikan Anda benar-benar telah membaca dan memahami isi dokumen ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#9a5b35'
            }).then(function(result) {
                if (result.isConfirmed) {
                    catatSudahDibaca(dataSaatIni.id, true);
                }
            });
        });

        $('#modalDetail').on('hidden.bs.modal', function() {
            resetModal();
        });

    });
    </script>

</body>

</html>