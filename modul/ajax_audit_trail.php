<?php
require_once('clogin.php');
require_once('config/dbi_connect.php');

$nkt = isset($_GET['nkt']) ? trim($_GET['nkt']) : '';
$no_kantonga = isset($_GET['no_kantonga']) ? trim($_GET['no_kantonga']) : '';
$kantongke = isset($_GET['kantongke']) ? trim($_GET['kantongke']) : '';

if ($nkt === '' || $no_kantonga === '') {
    echo '<div class="alert alert-warning mb-0">Parameter kantong tidak lengkap.</div>';
    exit;
}

$nkt_sql = mysqli_real_escape_string($dbi, $nkt);
$no_kantonga_sql = mysqli_real_escape_string($dbi, $no_kantonga);

function auditPrefix($kantongke)
{
    return ($kantongke !== 'A') ? 'Kantong Utama : ' : '';
}

$prefix = auditPrefix($kantongke);

$sql = "
SELECT *
FROM (
    SELECT
        DATE_FORMAT(ul.time_aksi, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(ul.time_aksi, '%H:%i') AS jam_aksi,
        ul.time_aksi AS sort_time,
        ul.modul AS modul,
        CONCAT(CASE WHEN SUBSTRING(ul.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END, ul.aksi_user) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM user_log ul
    LEFT JOIN user u ON u.id_user = ul.user
    WHERE ul.aksi_user LIKE '%$nkt_sql%'
      AND ul.aksi_user LIKE '%barcode%'

    UNION ALL

    SELECT
        DATE_FORMAT(ul.time_aksi, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(ul.time_aksi, '%H:%i') AS jam_aksi,
        ul.time_aksi AS sort_time,
        ul.modul AS modul,
        CONCAT(CASE WHEN SUBSTRING(ul.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END, ul.aksi_user) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM user_log ul
    LEFT JOIN user u ON u.id_user = ul.user
    WHERE ul.aksi_user LIKE '%$nkt_sql%'
      AND ul.aksi_user LIKE '%Release%'

    UNION ALL

    SELECT
        DATE_FORMAT(ul.time_aksi, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(ul.time_aksi, '%H:%i') AS jam_aksi,
        ul.time_aksi AS sort_time,
        ul.modul AS modul,
        CONCAT(CASE WHEN SUBSTRING(ul.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END, ul.aksi_user) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM user_log ul
    LEFT JOIN user u ON u.id_user = ul.user
    WHERE ul.aksi_user LIKE '%$nkt_sql%'
      AND ul.aksi_user LIKE '%crossmatch%'

    UNION ALL

    SELECT
        DATE_FORMAT(ul.time_aksi, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(ul.time_aksi, '%H:%i') AS jam_aksi,
        ul.time_aksi AS sort_time,
        ul.modul AS modul,
        CONCAT(CASE WHEN SUBSTRING(ul.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END, ul.aksi_user) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM user_log ul
    LEFT JOIN user u ON u.id_user = ul.user
    WHERE ul.aksi_user LIKE '%$no_kantonga_sql%'
      AND ul.aksi_user LIKE '%KGD%'
      AND ul.modul = 'KONFIRMASI'

    UNION ALL

    SELECT
        DATE_FORMAT(ul.time_aksi, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(ul.time_aksi, '%H:%i') AS jam_aksi,
        ul.time_aksi AS sort_time,
        ul.modul AS modul,
        CONCAT(CASE WHEN SUBSTRING(ul.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END, ul.aksi_user) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM user_log ul
    LEFT JOIN user u ON u.id_user = ul.user
    WHERE ul.aksi_user LIKE '%$no_kantonga_sql%'
      AND (ul.aksi_user LIKE '%IMLTD%' OR ul.modul='IMLTD')

    UNION ALL

    SELECT
        DATE_FORMAT(d.tglPengerjaan, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(d.tglPengerjaan, '%H:%i') AS jam_aksi,
        d.tglPengerjaan AS sort_time,
        'PENGOLAHAN' AS modul,
        CONCAT('Pengolahan (', p.lengkap, ') No.Trans: ', d.NoTrans) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM dpengolahan d
    LEFT JOIN user u ON u.id_user = d.petugas
    LEFT JOIN produk p ON p.Nama = d.Produk
    WHERE d.noKantong = '$nkt_sql'

    UNION ALL

    SELECT
        DATE_FORMAT(ul.time_aksi, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(ul.time_aksi, '%H:%i') AS jam_aksi,
        ul.time_aksi AS sort_time,
        ul.modul AS modul,
        CONCAT(CASE WHEN SUBSTRING(ul.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END, ul.aksi_user) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM user_log ul
    LEFT JOIN user u ON u.id_user = ul.user
    WHERE ul.aksi_user LIKE '%$nkt_sql%'
      AND ul.aksi_user LIKE '%Serah terima (Pengesahan)%'

    UNION ALL

    SELECT
        DATE_FORMAT(ul.time_aksi, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(ul.time_aksi, '%H:%i') AS jam_aksi,
        ul.time_aksi AS sort_time,
        ul.modul AS modul,
        CONCAT(CASE WHEN SUBSTRING(ul.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END, ul.aksi_user) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM user_log ul
    LEFT JOIN user u ON u.id_user = ul.user
    WHERE ul.aksi_user LIKE '%$nkt_sql%'
      AND ul.aksi_user LIKE '%Kirim ke%'

    UNION ALL

    SELECT
        DATE_FORMAT(ul.time_aksi, '%d/%m/%Y') AS tgl_aksi,
        DATE_FORMAT(ul.time_aksi, '%H:%i') AS jam_aksi,
        ul.time_aksi AS sort_time,
        ul.modul AS modul,
        CONCAT(CASE WHEN SUBSTRING(ul.tempat, 1, 1)='M' THEN 'Mobile Unit-' ELSE '' END, ul.aksi_user) AS proses,
        u.nama_lengkap AS nama_lengkap
    FROM user_log ul
    LEFT JOIN user u ON u.id_user = ul.user
    WHERE ul.aksi_user LIKE '%$nkt_sql%'
      AND ul.aksi_user LIKE '%musnah%'
) x
ORDER BY sort_time ASC
";

$q = mysqli_query($dbi, $sql);

echo '<div class="table-responsive">';
echo '<table class="table table-bordered table-sm mb-0">';
echo '<thead style="background-color: mistyrose; color: #000;">';
echo '<tr>';
echo '<th class="text-center">Tanggal</th>';
echo '<th class="text-center">Jam</th>';
echo '<th class="text-center">Modul</th>';
echo '<th class="text-center">Proses</th>';
echo '<th class="text-center">Personil</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

if (!$q || mysqli_num_rows($q) == 0) {
    echo '<tr><td colspan="5" class="text-center">TIDAK ADA DATA AUDIT TRAIL</td></tr>';
    echo '</tbody></table></div>';
    exit;
}

while ($komp = mysqli_fetch_assoc($q)) {
    $proses = $komp['proses'];
    if ($prefix !== '') {
        $proses = $prefix . $proses;
    }

    echo '<tr>';
    echo '<td>' . $komp['tgl_aksi'] . '</td>';
    echo '<td>' . $komp['jam_aksi'] . '</td>';
    echo '<td>' . $komp['modul'] . '</td>';
    echo '<td>' . $proses . '</td>';
    echo '<td>' . $komp['nama_lengkap'] . '</td>';
    echo '</tr>';
}

echo '</tbody></table></div>';