<?php
/*
SIMDONDAR 3.7
barcodeproses.php - versi dirapikan untuk PHP 5.3
*/

session_start();
require_once("../config/dbi_connect.php");

mysqli_query($dbi, "SET GLOBAL sql_mode = ''");

$leveluser = isset($_SESSION['leveluser']) ? strtoupper($_SESSION['leveluser']) : '';
$namauser  = isset($_SESSION['namauser']) ? strtoupper($_SESSION['namauser']) : '';

function post($key, $default = '')
{
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

function get($key, $default = '')
{
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function jeniskantong($jenis)
{
    $arr = array(
        '1'  => 'Single',
        '2'  => 'Double',
        '3'  => 'Triple',
        '4'  => 'Quadruple',
        '5'  => 'Pediatrik/Quintuple',
        '6'  => 'Sextuple',
        '7'  => 'Septuple',
        '8'  => 'Octuple',
        '9'  => 'Nonuple',
        '10' => 'Decuple'
    );

    return isset($arr[(string)$jenis]) ? $arr[(string)$jenis] : '';
}

function cekstatuskantong($status, $stattempat, $sah)
{
    $status = (string)$status;
    $stattempat = (string)$stattempat;
    $sah = (string)$sah;

    switch ($status) {
        case '0':
            if ($stattempat === '' || $stattempat === '0') {
                return 'Logistik';
            }
            if ($stattempat === '1') {
                return 'Kosong di Aftap';
            }
            return 'Kosong';

        case '1':
            return ($sah === '1') ? 'Karantina' : 'Belum disahkan';

        case '2':
            return 'Sehat';
        case '3':
            return 'Keluar';
        case '4':
            return 'Rusak';
        case '5':
            return 'Rusak-Gagal';
        case '6':
            return 'Dimusnahkan';
        default:
            return 'Musnah';
    }
}

function render_iframe($src, $zoom = 120)
{
    $src = h($src);
    return '<div class="container-frame embed-responsive embed-responsive-16by9">'
        . '<iframe class="responsive-iframe" src="' . $src . '&#zoom=' . (int)$zoom . '&pagemode=none" frameborder="0" allowtransparency="true">Browser tidak mendukung</iframe>'
        . '</div>';
}

function render_table($headers, $rows)
{
    $html = '<div class="table-responsive">';
    $html .= '<table class="table table-responsive table-bordered w3-card-2">';
    $html .= '<thead><tr class="w3-red">';
    foreach ($headers as $head) {
        $html .= '<th class="text-center">' . h($head) . '</th>';
    }
    $html .= '</tr></thead><tbody>';
    $html .= $rows;
    $html .= '</tbody></table></div>';
    return $html;
}

function save_temp_barcode($dbi, $jenislabel, $jmlcetak, $tgled, $merk, $lot, $volume, $tipebarcode, $jenisktg)
{
    $sql = "INSERT INTO `tempudd`(`modul`, `dokter`, `petugas1`, `petugas2`, `petugas3`, `alamat`, `kelurahan`, `kecamatan`, `wilayah`, `kodepos`)
            VALUES ('BARCODE','" . mysqli_real_escape_string($dbi, $jenislabel) . "',
                    '" . mysqli_real_escape_string($dbi, $jmlcetak) . "',
                    '" . mysqli_real_escape_string($dbi, $tgled) . "',
                    '" . mysqli_real_escape_string($dbi, $merk) . "',
                    '" . mysqli_real_escape_string($dbi, $lot) . "',
                    '" . mysqli_real_escape_string($dbi, $volume) . "',
                    '" . mysqli_real_escape_string($dbi, $tipebarcode) . "',
                    '" . mysqli_real_escape_string($dbi, $jenisktg) . "',
                    '')";
    $res = mysqli_query($dbi, $sql);

    if (!$res) {
        $sql = "UPDATE `tempudd`
                SET `dokter`='" . mysqli_real_escape_string($dbi, $jenislabel) . "',
                    `petugas1`='" . mysqli_real_escape_string($dbi, $jmlcetak) . "',
                    `petugas2`='" . mysqli_real_escape_string($dbi, $tgled) . "',
                    `petugas3`='" . mysqli_real_escape_string($dbi, $merk) . "',
                    `alamat`='" . mysqli_real_escape_string($dbi, $lot) . "',
                    `kelurahan`='" . mysqli_real_escape_string($dbi, $volume) . "',
                    `kecamatan`='" . mysqli_real_escape_string($dbi, $tipebarcode) . "',
                    `wilayah`='" . mysqli_real_escape_string($dbi, $jenisktg) . "'
                WHERE `modul`='BARCODE'";
        $res = mysqli_query($dbi, $sql);
    }

    return $res;
}

function get_lama_buka($dbi, $merk, $jenis, $volume = null)
{
    $merk = mysqli_real_escape_string($dbi, $merk);
    $jenis = mysqli_real_escape_string($dbi, $jenis);
    $volumeSql = '';
    if ($volume !== null && $volume !== '') {
        $volumeSql = " AND `vol`='" . mysqli_real_escape_string($dbi, $volume) . "'";
    }

    $sql = "SELECT `lama_buka` FROM `master_kantong` WHERE `merk`='$merk' AND `jenis`='$jenis' $volumeSql";
    $res = mysqli_query($dbi, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if ($row['lama_buka'] !== '' && $row['lama_buka'] !== '0') {
            return (int)$row['lama_buka'];
        }
    }
    return 1;
}

function add_days($date, $days)
{
    $days = (int)$days;
    $dt = date_create($date);
    if ($dt) {
        date_add($dt, date_interval_create_from_date_string($days . ' days'));
        return date_format($dt, 'Y-m-d');
    }
    return $date;
}

function get_udt_active($dbi)
{
    $res = mysqli_query($dbi, "SELECT nama,id FROM utd WHERE aktif='1' LIMIT 1");
    return ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : array('nama' => '', 'id' => '');
}

function next_kantong_kode($dbi)
{
    $udd = get_udt_active($dbi);
    $id_udd = isset($udd['id']) ? $udd['id'] : '';
    $year = date('y');
    $prefix = $id_udd . $year;

    $sql = "SELECT `noKantong`,
                   CAST(MID(`noKantong`,7,6) AS SIGNED) AS `IntNomor`
            FROM `stokkantong`
            WHERE LEFT(`noKantong`,6)='$prefix'
            ORDER BY `noKantong` DESC
            LIMIT 1";
    $res = mysqli_query($dbi, $sql);

    $current = 0;
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $current = (int)$row['IntNomor'];
    }

    $next = $current + 1;
    return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
}

function next_kantong_hexa($dbi)
{
    $udd = get_udt_active($dbi);
    $id_udd = isset($udd['id']) ? $udd['id'] : '';
    $year = date('y');
    $prefix = $id_udd . $year;

    $sql = "SELECT `noKantong`,
                   MID(`noKantong`,7,4) AS no_hexa,
                   CAST(MID(`noKantong`,7,4) AS SIGNED) AS IntNomor
            FROM `stokkantong`
            WHERE LEFT(`noKantong`,6)='$prefix'
            ORDER BY `noKantong` DESC
            LIMIT 1";
    $res = mysqli_query($dbi, $sql);

    $current_hexa = '0000';
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $current_hexa = $row['no_hexa'];
    }

    $current_no = hexdec($current_hexa);
    $next = $current_no + 1;
    $converted = strtoupper(dechex($next));

    return $id_udd . $year . str_pad($converted, 4, '0', STR_PAD_LEFT) . 'A';
}

$mode = get('m', '');
$output = '';

switch ($mode) {
    case 'generatednokantonghexa':
        echo next_kantong_hexa($dbi);
        break;

    case 'generatedkantong':
        echo next_kantong_kode($dbi);
        break;

    case 'cetakbarkodeselang':
        $v_tanggal     = post('tanggal');
        $v_tipebarcode = post('tipebarcode');
        $v_jenislabel  = post('jenislabel');
        $v_merk        = post('merk');
        $v_volume      = post('volume');
        $v_jenis       = post('jenis');
        $v_lot         = post('lot');
        $v_tgled       = post('tgled');
        $v_jmlcetak    = post('jmlcetak');
        $v_noselang    = post('noSelang');
        $v_metode      = post('metode');
        $v_labelktg    = isset($_POST['ChkLabelKantong']) ? $_POST['ChkLabelKantong'] : '0';
        $v_chkinfo     = isset($_POST['ChkInfo']) ? $_POST['ChkInfo'] : '0';

        save_temp_barcode($dbi, $v_jenislabel, $v_jmlcetak, $v_tgled, $v_merk, $v_lot, $v_volume, $v_tipebarcode, $v_jenis);

        $query = mysqli_query($dbi, "SELECT * FROM `stokkantong` WHERE `noSelang`='" . mysqli_real_escape_string($dbi, $v_noselang) . "' ORDER BY `noKantong`");
        if ($query && mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            $statuskantong = cekstatuskantong($row['Status'], $row['StatTempat'], $row['sah']);
            echo '<div class="row text-center">';
            echo '<span class="glyphicon glyphicon-warning-sign w3-text-red" style="font-size:600%;"></span>';
            echo '<h3 class="text-center">Nomor selang <span class="w3-text-red">' . h($row['noSelang']) . '</span> sudah ada! <br>';
            echo 'Nomor kantong: <span class="w3-text-red">' . h($row['noKantong']) . '</span> <br>';
            echo 'Tanggal: <span class="w3-text-red">' . h($row['tglTerima']) . '</span>, <br>';
            echo 'Status: <span class="w3-text-red">' . h($statuskantong) . '</span></h3>';
            echo '</div>';
            break;
        }

        $standarlamabukakantong = get_lama_buka($dbi, $v_merk, $v_jenis, $v_volume);
        $edbuka = add_days($v_tanggal, $standarlamabukakantong);

        $file = ($v_jenislabel == '1')
            ? 'barcodekantong/barcode1baris2023manual.php'
            : 'barcodekantong/barcode2baris2023manual.php';

        $parameterpdf = 'tgl=' . urlencode($v_tanggal)
            . '&tipe=' . urlencode($v_tipebarcode)
            . '&lbl=' . urlencode($v_jenislabel)
            . '&merk=' . urlencode($v_merk)
            . '&vol=' . urlencode($v_volume)
            . '&jenisktg=' . urlencode($v_jenis)
            . '&lot=' . urlencode($v_lot)
            . '&usr=' . urlencode($namauser)
            . '&ed=' . urlencode($v_tgled)
            . '&jmlcetak=' . urlencode($v_jmlcetak)
            . '&noselang=' . urlencode($v_noselang)
            . '&lambuka=' . urlencode($standarlamabukakantong)
            . '&edbuka=' . urlencode($edbuka)
            . '&usrlevel=' . urlencode($leveluser)
            . '&chkinfo=' . urlencode($v_chkinfo)
            . '&metode=' . urlencode($v_metode)
            . '&lblktg=' . urlencode($v_labelktg);

        echo render_iframe($file . '?' . $parameterpdf, ($v_jenislabel == '1') ? 120 : 100);
        echo '<h4 class="text-center w3-text-blue">Barcode Kantong & Nomor Selang</h4>';
        break;

    case 'cetakbarcodeauto':
        $v_tanggal     = post('tanggal');
        $v_tipebarcode = post('tipebarcode');
        $v_jenislabel  = post('jenislabel');
        $v_merk        = post('merk');
        $v_volume      = post('volume');
        $v_jenis       = post('jenis');
        $v_lot         = post('lot');
        $v_tgled       = post('tgled');
        $v_jmlcetak    = post('jmlcetak');
        $v_jmlkantong  = post('jmlkantong');
        $v_metode      = post('metode');
        $v_labelktg    = isset($_POST['ChkLabelKantong']) ? $_POST['ChkLabelKantong'] : '0';
        $v_chkinfo     = isset($_POST['ChkInfo']) ? $_POST['ChkInfo'] : '0';
        $v_chkpemisah  = isset($_POST['ChkLabelPemisah']) ? $_POST['ChkLabelPemisah'] : '0';

        save_temp_barcode($dbi, $v_jenislabel, $v_jmlcetak, $v_tgled, $v_merk, $v_lot, $v_volume, $v_tipebarcode, $v_jenis);

        $standarlamabukakantong = get_lama_buka($dbi, $v_merk, $v_jenis, $v_volume);
        $edbuka = add_days($v_tanggal, $standarlamabukakantong);

        if ($v_jenislabel == '1') {
            $file = 'barcodekantong/barcode1baris2023auto.php';
            $parameterpdf = 'tgl=' . urlencode($v_tanggal)
                . '&tipe=' . urlencode($v_tipebarcode)
                . '&lbl=' . urlencode($v_jenislabel)
                . '&merk=' . urlencode($v_merk)
                . '&vol=' . urlencode($v_volume)
                . '&jenisktg=' . urlencode($v_jenis)
                . '&lot=' . urlencode($v_lot)
                . '&usr=' . urlencode($namauser)
                . '&ed=' . urlencode($v_tgled)
                . '&jmlcetak=' . urlencode($v_jmlcetak)
                . '&jmlktg=' . urlencode($v_jmlkantong)
                . '&lambuka=' . urlencode($standarlamabukakantong)
                . '&edbuka=' . urlencode($edbuka)
                . '&usrlevel=' . urlencode($leveluser)
                . '&chkinfo=' . urlencode($v_chkinfo)
                . '&pemisah=' . urlencode($v_chkpemisah)
                . '&metode=' . urlencode($v_metode)
                . '&lblktg=' . urlencode($v_labelktg);
        } else {
            $file = 'barcodekantong/barcode2baris2023auto.php';
            $parameterpdf = 'tgl=' . urlencode($v_tanggal)
                . '&tipe=' . urlencode($v_tipebarcode)
                . '&lbl=' . urlencode($v_jenislabel)
                . '&merk=' . urlencode($v_merk)
                . '&vol=' . urlencode($v_volume)
                . '&jenisktg=' . urlencode($v_jenis)
                . '&lot=' . urlencode($v_lot)
                . '&usr=' . urlencode($namauser)
                . '&ed=' . urlencode($v_tgled)
                . '&jmlcetak=' . urlencode($v_jmlcetak)
                . '&jmlktg=' . urlencode($v_jmlkantong)
                . '&lambuka=' . urlencode($standarlamabukakantong)
                . '&edbuka=' . urlencode($edbuka)
                . '&usrlevel=' . urlencode($leveluser)
                . '&chkinfo=' . urlencode($v_chkinfo)
                . '&pemisah=' . urlencode($v_chkpemisah)
                . '&metode=' . urlencode($v_metode)
                . '&lblktg=' . urlencode($v_labelktg);
        }

        echo render_iframe($file . '?' . $parameterpdf, ($v_jenislabel == '1') ? 120 : 100);
        echo '<h4 class="text-center w3-text-blue">Barcode Kantong Otomatis</h4>';
        break;

    case 'cetakbarcode':
        $v_tipebarcode = post('tipebarcode');
        $v_jenislabel  = post('jenislabel');
        $v_nokantong   = post('nokantong');
        $v_jmlcetak    = post('jmlcetak');
        $v_tgled       = post('tgled');
        $v_lot         = post('lot');
        $v_jenis       = post('jenis');
        $v_volume      = post('volume');
        $v_merk        = post('merk');
        $v_tanggal     = post('tanggal');
        $v_labelktg    = isset($_POST['ChkLabelKantong']) ? $_POST['ChkLabelKantong'] : '0';

        if (substr($v_nokantong, -1) === 'A') {
            $v_nokantong0 = substr($v_nokantong, 0, -1);
        } else {
            $v_nokantong0 = $v_nokantong;
            $v_nokantong = $v_nokantong0 . 'A';
        }

        save_temp_barcode($dbi, $v_jenislabel, $v_jmlcetak, $v_tgled, $v_merk, $v_lot, $v_volume, $v_tipebarcode, $v_jenis);

        $sqlcek = "SELECT `noKantong`,`Status`,`tglTerima`,`kadaluwarsa_ktg`,`jenis`,`nolot_ktg`,`sah`,`StatTempat`,`merk`,`volume`
                   FROM `stokkantong`
                   WHERE `noKantong`='" . mysqli_real_escape_string($dbi, $v_nokantong) . "'";
        $cek = mysqli_query($dbi, $sqlcek);

        if (!$cek || mysqli_num_rows($cek) <= 0) {
            echo '<h1 class="text-center">No Kantong <br><span class="w3-text-red"><b>' . h($v_nokantong) . '</b></span><br> tidak ditemukan</h1>';
            break;
        }

        $dtkantong = mysqli_fetch_assoc($cek);
        $statuskantong = cekstatuskantong($dtkantong['Status'], $dtkantong['StatTempat'], $dtkantong['sah']);

        $v_jenis = $dtkantong['jenis'];
        $v_lot   = $dtkantong['nolot_ktg'];
        $v_tgled = $dtkantong['kadaluwarsa_ktg'];
        $v_merk  = $dtkantong['merk'];
        $v_volume = $dtkantong['volume'];

        $msg = '<h4 class="text-center w3-text-red">Kantong ' . h($dtkantong['noKantong']) . ' sudah ada, tanggal : ' . h($dtkantong['tglTerima']) . '</h4>';
        $msg .= '<h4 class="text-center w3-text-red">' . h(jeniskantong($v_jenis)) . ', Status: ' . h($statuskantong) . ', Lot: ' . h($dtkantong['nolot_ktg']) . ', ED: ' . h($dtkantong['kadaluwarsa_ktg']) . '</h4>';

        if ($v_jenislabel == '1') {
            $src = 'logistik22/logistik_pdfbarcode1baris.php?kantong=' . urlencode($v_nokantong) . '&c=' . urlencode($v_jmlcetak) . '&j=' . urlencode($v_jenis) . '&tipe=' . urlencode($v_tipebarcode) . '&ed=' . urlencode($v_tgled) . '&lot=' . urlencode($v_lot) . '&merk=' . urlencode($v_merk) . '&usr=' . urlencode($namauser) . '&vol=' . urlencode($v_volume) . '&lblktg=' . urlencode($v_labelktg);
        } elseif ($v_jenislabel == '2') {
            $src = 'logistik22/logistik_pdfbarcode2baris.php?kantong=' . urlencode($v_nokantong) . '&c=' . urlencode($v_jmlcetak) . '&j=' . urlencode($v_jenis) . '&tipe=' . urlencode($v_tipebarcode) . '&ed=' . urlencode($v_tgled) . '&lot=' . urlencode($v_lot) . '&merk=' . urlencode($v_merk) . '&usr=' . urlencode($namauser) . '&vol=' . urlencode($v_volume) . '&lblktg=' . urlencode($v_labelktg);
        } else {
            $src = '';
        }

        if ($src !== '') {
            echo render_iframe($src, ($v_jenislabel == '1') ? 120 : 100);
        }
        echo $msg;
        break;

    case 'cetaksatulabel':
        $v_nokantong   = post('nokantong');
        $v_jmlcetak    = post('jmlcetak');
        $v_jenislabel  = post('jenislabel');
        $v_tipebarcode = post('tipebarcode');
        $v_labelktg    = isset($_POST['ChkLabelKantong']) ? $_POST['ChkLabelKantong'] : '0';

        $cek = mysqli_query($dbi, "SELECT * FROM `stokkantong` WHERE `noKantong`='" . mysqli_real_escape_string($dbi, $v_nokantong) . "'");
        if (!$cek || mysqli_num_rows($cek) <= 0) {
            echo '<h1 class="text-center">No Kantong <br><span class="w3-text-red"><b>' . h($v_nokantong) . '</b></span><br> tidak ditemukan</h1>';
            break;
        }

        $row = mysqli_fetch_assoc($cek);
        if ($v_jenislabel == '1') {
            $src = 'barcodekantong/barcode1baris2023_ulang.php?kantong=' . urlencode($v_nokantong) . '&c=' . urlencode($v_jmlcetak) . '&j=' . urlencode($row['jenis']) . '&tipe=' . urlencode($v_tipebarcode) . '&ed=' . urlencode($row['kadaluwarsa_ktg']) . '&lot=' . urlencode($row['nolot_ktg']) . '&merk=' . urlencode($row['merk']) . '&usr=' . urlencode($row['user_barcode']) . '&vol=' . urlencode($row['volume']) . '&selang=' . urlencode($row['noSelang']) . '&metode=' . urlencode($row['metoda']) . '&lblktg=' . urlencode($v_labelktg);
            echo render_iframe($src, 120);
        } else {
            $src = 'barcodekantong/barcode2baris2023_ulang.php?kantong=' . urlencode($v_nokantong) . '&c=' . urlencode($v_jmlcetak) . '&j=' . urlencode($row['jenis']) . '&tipe=' . urlencode($v_tipebarcode) . '&ed=' . urlencode($row['kadaluwarsa_ktg']) . '&lot=' . urlencode($row['nolot_ktg']) . '&merk=' . urlencode($row['merk']) . '&usr=' . urlencode($row['user_barcode']) . '&vol=' . urlencode($row['volume']) . '&selang=' . urlencode($row['noSelang']) . '&metode=' . urlencode($row['metoda']) . '&lblktg=' . urlencode($v_labelktg);
            echo render_iframe($src, 100);
        }
        break;

    case 'hapuskantong':
    case 'hapuskantonglogistik':
        $delete_id = post('delete_id');
        $ex_id = explode('|', $delete_id);
        $v_kantonga = isset($ex_id[0]) ? $ex_id[0] : '';
        $v_jenis = isset($ex_id[1]) ? (int)$ex_id[1] : 0;
        $v_kantong0 = substr($v_kantonga, 0, -1);
        $sufixkantong = 'A';

        for ($x = 1; $x <= $v_jenis; $x++) {
            $delkantong = $v_kantong0 . $sufixkantong;
            mysqli_query($dbi, "DELETE FROM `stokkantong` WHERE `noKantong`='" . mysqli_real_escape_string($dbi, $delkantong) . "'");
            $sufixkantong++;
        }

        if ($mode === 'hapuskantong') {
            $log_mdl = $leveluser;
            $log_aksi = "Menghapus kantong: " . $v_kantonga;
        } else {
            $log_mdl = "LOGISTIK";
            $log_aksi = "Menghapus Kantong Logistik: " . $v_kantonga;
        }

        @include_once "../modul/user_log.php";

        $sql = "SELECT `noKantong`, `jenis`, `Status`, `tglTerima`, `merk`, `nolot_ktg`, `kadaluwarsa_ktg`, `StatTempat`, `ident`, `volume`
                FROM `stokkantong`
                WHERE `Status`='0' AND (`StatTempat` IS NULL OR `StatTempat`='0') AND `ident`='m'";
        $data = mysqli_query($dbi, $sql);

        $rows = '';
        $no = 0;
        if ($data && mysqli_num_rows($data) > 0) {
            while ($r = mysqli_fetch_assoc($data)) {
                $no++;
                $rows .= '<tr>';
                $rows .= '<td class="text-right">' . $no . '.</td>';
                $rows .= '<td class="text-center" nowrap>' . h($r['tglTerima']) . '</td>';
                $rows .= '<td class="text-center" nowrap>';
                $rows .= '<a class="w3-btn btn-xs w3-border-red w3-hover-purple" title="Proses permintaan barang" href="?module=logmintaproses&notrans=' . h($r['noKantong']) . '">' . h($r['noKantong']) . '</a>';
                $rows .= '<a href="#" id="' . h($r['noKantong']) . '|' . h($r['jenis']) . '" class="w3-btn btn-xs w3-hover-red w3-text-red hapusdata"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
                $rows .= '</td>';
                $rows .= '<td class="text-left" nowrap>' . h($r['merk']) . '</td>';
                $rows .= '<td class="text-left">' . h(jeniskantong($r['jenis'])) . '</td>';
                $rows .= '<td class="text-left">' . h($r['volume']) . '</td>';
                $rows .= '<td class="text-left">' . h($r['nolot_ktg']) . '</td>';
                $rows .= '<td class="text-left">' . h($r['kadaluwarsa_ktg']) . '</td>';
                $rows .= '</tr>';
            }
        } else {
            $rows = '<tr><td class="text-center" colspan="8">Tidak ada data kantong untuk dimutasikan</td></tr>';
        }

        echo render_table(array('No', 'Tanggal', 'No Kantong', 'Merk', 'Jenis', 'Volume', 'Lot', 'ED'), $rows);
        break;

    case 'listmutasikantong':
        $sql = "SELECT `noKantong`, `jenis`, `Status`, `tglTerima`, `merk`, `nolot_ktg`, `kadaluwarsa_ktg`, `StatTempat`, `ident`, `volume`
                FROM `stokkantong`
                WHERE `Status`='0' AND (`StatTempat` IS NULL OR `StatTempat`='0') AND `ident`='m'";
        $data = mysqli_query($dbi, $sql);

        $rows = '';
        $no = 0;
        if ($data && mysqli_num_rows($data) > 0) {
            while ($r = mysqli_fetch_assoc($data)) {
                $no++;
                $rows .= '<tr>';
                $rows .= '<td class="text-right">' . $no . '.</td>';
                $rows .= '<td class="text-center" nowrap>' . h($r['tglTerima']) . '</td>';
                $rows .= '<td class="text-center" nowrap>';
                $rows .= '<a class="w3-btn btn-xs w3-border-red w3-hover-purple mutasikantong" title="Mutasikan kantong" href="#" id="' . h($r['noKantong']) . '">' . h($r['noKantong']) . '</a>';
                $rows .= '<a href="#" id="' . h($r['noKantong']) . '|' . h($r['jenis']) . '" class="w3-btn btn-xs w3-hover-red w3-text-red hapusdata" title="Hapus kantong"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
                $rows .= '</td>';
                $rows .= '<td class="text-left" nowrap>' . h($r['merk']) . '</td>';
                $rows .= '<td class="text-left">' . h(jeniskantong($r['jenis'])) . '</td>';
                $rows .= '<td class="text-left">' . h($r['volume']) . '</td>';
                $rows .= '<td class="text-left">' . h($r['nolot_ktg']) . '</td>';
                $rows .= '<td class="text-left">' . h($r['kadaluwarsa_ktg']) . '</td>';
                $rows .= '</tr>';
            }
        } else {
            $rows = '<tr><td class="text-center" colspan="8">Tidak ada data kantong untuk dimutasikan</td></tr>';
        }

        echo render_table(array('No', 'Tanggal', 'No Kantong', 'Merk', 'Jenis', 'Volume', 'Lot', 'ED'), $rows);
        break;

    case 'mutasikankantong':
        $v_kantonga = mysqli_real_escape_string($dbi, post('nokantong'));
        $cekkantong = mysqli_query($dbi, "SELECT `noKantong`,`jenis`,`Status`,`StatTempat`,`sah` FROM `stokkantong` WHERE `noKantong`='$v_kantonga'");

        if (!$cekkantong || mysqli_num_rows($cekkantong) <= 0) {
            echo '<h4 class="w3-text-red">Nomor kantong yang dimasukkan tidak ada</h4>';
            break;
        }

        $cekstatus = mysqli_fetch_assoc($cekkantong);
        $statuskantong = cekstatuskantong($cekstatus['Status'], $cekstatus['StatTempat'], $cekstatus['sah']);

        if ($cekstatus['StatTempat'] === '1') {
            echo '<h4 class="w3-text-red">Nomor kantong ' . h($v_kantonga) . ' tidak dapat dimutasikan, status kantong : ' . h($statuskantong) . '</h4>';
            break;
        }

        $jenis = (int)$cekstatus['jenis'];
        $v_kantong0 = substr($v_kantonga, 0, -1);
        $sufixkantong = 'A';
        $ada_error = false;
        $today = date('Y-m-d H:i:s');

        for ($x = 1; $x <= $jenis; $x++) {
            $proses_kantongmutasi = $v_kantong0 . $sufixkantong;
            $qry = "UPDATE `stokkantong`
                    SET `StatTempat`='1',
                        `tglmutasi`='" . mysqli_real_escape_string($dbi, $today) . "'
                    WHERE `noKantong`='" . mysqli_real_escape_string($dbi, $proses_kantongmutasi) . "'";
            if (!mysqli_query($dbi, $qry)) {
                $ada_error = true;
            }
            $sufixkantong++;
        }

        $log_mdl = $leveluser;
        $log_aksi = "Pengesahan Kantong Logistik: " . $v_kantonga;
        @include_once "../modul/user_log.php";

        if ($ada_error) {
            echo '<h4 class="w3-text-red">Proses mutasi kantong ' . h($v_kantonga) . ' tidak berhasil</h4>';
        } else {
            echo '<h4 class="w3-text-blue">Proses mutasi kantong ' . h($v_kantonga) . ' BERHASIL</h4>';
        }

        $sql = "SELECT `noKantong`, `jenis`, `Status`, `tglTerima`, `merk`, `nolot_ktg`, `kadaluwarsa_ktg`, `StatTempat`, `ident`, `volume`
                FROM `stokkantong`
                WHERE `Status`='0' AND (`StatTempat` IS NULL OR `StatTempat`='0') AND `ident`='m'";
        $data = mysqli_query($dbi, $sql);
        $rows = '';
        $no = 0;
        if ($data && mysqli_num_rows($data) > 0) {
            while ($r = mysqli_fetch_assoc($data)) {
                $no++;
                $rows .= '<tr>';
                $rows .= '<td class="text-right">' . $no . '.</td>';
                $rows .= '<td class="text-center" nowrap>' . h($r['tglTerima']) . '</td>';
                $rows .= '<td class="text-center" nowrap>';
                $rows .= '<a class="w3-btn btn-xs w3-border-red w3-hover-purple mutasikantong" title="Mutasikan kantong" href="#" id="' . h($r['noKantong']) . '">' . h($r['noKantong']) . '</a>';
                $rows .= '<a href="#" id="' . h($r['noKantong']) . '|' . h($r['jenis']) . '" class="w3-btn btn-xs w3-hover-red w3-text-red hapusdata" title="Hapus kantong"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
                $rows .= '</td>';
                $rows .= '<td class="text-left" nowrap>' . h($r['merk']) . '</td>';
                $rows .= '<td class="text-left">' . h(jeniskantong($r['jenis'])) . '</td>';
                $rows .= '<td class="text-left">' . h($r['volume']) . '</td>';
                $rows .= '<td class="text-left">' . h($r['nolot_ktg']) . '</td>';
                $rows .= '<td class="text-left">' . h($r['kadaluwarsa_ktg']) . '</td>';
                $rows .= '</tr>';
            }
        } else {
            $rows = '<tr><td class="text-center" colspan="8">Tidak ada data kantong untuk dimutasikan</td></tr>';
        }

        echo render_table(array('No', 'Tanggal', 'No Kantong', 'Merk', 'Jenis', 'Volume', 'Lot', 'ED'), $rows);
        break;

    case 'rekapbarcode':
        $v_tgl1 = get('tgl1', date('Y-m-d'));
        $v_tgl2 = get('tgl2', date('Y-m-d'));
        $v_detail = get('detail', '0');

        $wjenis = '';
        $wstatus = '';
        $sql = "SELECT merk, volumeasal, jenis,
                       CASE
                           WHEN jenis='1' THEN 'Single'
                           WHEN jenis='2' THEN 'Double'
                           WHEN jenis='3' THEN 'Triple'
                           WHEN jenis='4' THEN 'Quadruple'
                           WHEN jenis='5' THEN 'Quintuple'
                           WHEN jenis='6' THEN 'Sextuple/Pediatric'
                           WHEN jenis='7' THEN 'Septuple'
                           WHEN jenis='8' THEN 'Octuple'
                           WHEN jenis='9' THEN 'Nonuple'
                           WHEN jenis='10' THEN 'Decuple'
                       END AS namajenis,
                       COUNT(noKantong) AS jml
                FROM stokkantong
                WHERE (DATE(tglTerima) BETWEEN '$v_tgl1' AND '$v_tgl2')
                  AND (ident='m') $wjenis $wstatus
                GROUP BY merk, volumeasal, jenis";

        $data = mysqli_query($dbi, $sql);
        $rows = '';
        $total = 0;
        if ($data && mysqli_num_rows($data) > 0) {
            while ($r = mysqli_fetch_assoc($data)) {
                $total += (int)$r['jml'];
                $rows .= '<tr>'
                    . '<td class="text-right">' . h($r['merk']) . '</td>'
                    . '<td class="text-center">' . h($r['volumeasal']) . '</td>'
                    . '<td class="text-left">' . h($r['namajenis']) . '</td>'
                    . '<td class="text-center">' . number_format((int)$r['jml'], 0, '.', ',') . '</td>'
                    . '</tr>';
            }
        } else {
            $rows = '<tr><td colspan="4" class="text-center">TIDAK ADA DATA</td></tr>';
        }

        $html = '<div class="row"><div class="col-md-12"><div class="table-responsive">';
        $html .= '<table class="table table-responsive table-bordered table-hover">';
        $html .= '<thead><tr class="w3-theme-d4"><th class="text-center">Merk</th><th class="text-center">Volume</th><th class="text-center">Jenis</th><th class="text-center">Jumlah</th></tr></thead>';
        $html .= '<tbody>' . $rows . '<tr><td colspan="3" class="text-right">Total Barcode</td><td class="text-center">' . number_format($total, 0, '.', ',') . '</td></tr></tbody>';
        $html .= '</table></div></div></div>';
        echo $html;

        if ($v_detail == '1') {
            $sel = "SELECT `noKantong`, `noSelang`, `jenis`, `Status`, `tglTerima`,`volume`,`volumeasal`, `merk`,`sah`,`StatTempat`,`nolot_ktg`,`kadaluwarsa_ktg`,`tglmutasi`,
                           CASE
                               WHEN jenis='1' THEN 'Single'
                               WHEN jenis='2' THEN 'Double'
                               WHEN jenis='3' THEN 'Triple'
                               WHEN jenis='4' THEN 'Quadruple'
                               WHEN jenis='5' THEN 'Quintuple'
                               WHEN jenis='6' THEN 'Sextuple/Pediatric'
                               WHEN jenis='7' THEN 'Septuple'
                               WHEN jenis='8' THEN 'Octuple'
                               WHEN jenis='9' THEN 'Nonuple'
                               WHEN jenis='10' THEN 'Decuple'
                           END AS namajenis,
                           `user_barcode`, `user_mutasi`
                    FROM stokkantong
                    WHERE (DATE(tglTerima) BETWEEN '$v_tgl1' AND '$v_tgl2') AND (ident='m') $wjenis $wstatus
                    ORDER BY `insert_on` ASC, `merk`,`volumeasal`,`jenis`";
            $rincian = mysqli_query($dbi, $sel);

            $rows = '';
            $no = 0;
            if ($rincian && mysqli_num_rows($rincian) > 0) {
                while ($row = mysqli_fetch_assoc($rincian)) {
                    $no++;
                    $cekhapus = '';
                    $delkantong = '';
                    if ($row['StatTempat'] != '1') {
                        $cekhapus = '<input type="checkbox" class="abc" name="chkitem[]" value="' . h($row['noKantong']) . '|' . h($row['jenis']) . '" style="cursor:pointer;">';
                        $delkantong = '<a href="#" id="' . h($row['noKantong']) . '|' . h($row['jenis']) . '" class="w3-btn btn-xs w3-hover-red w3-text-red hapusdata" title="Hapus kantong"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
                    }

                    $status_ktg = cekstatuskantong($row['Status'], $row['StatTempat'], $row['sah']);

                    $rows .= '<tr>'
                        . '<td class="text-center">' . $cekhapus . '</td>'
                        . '<td class="text-right">' . $no . '.</td>'
                        . '<td class="text-left">' . h($row['noKantong']) . $delkantong . '</td>'
                        . '<td class="text-left">' . h($row['noSelang']) . '</td>'
                        . '<td class="text-left">' . h($row['merk']) . '</td>'
                        . '<td class="text-center">' . h($row['volumeasal']) . '</td>'
                        . '<td class="text-left">' . h($row['namajenis']) . '</td>'
                        . '<td class="text-left">' . h($status_ktg) . '</td>'
                        . '<td class="text-left">' . h($row['nolot_ktg']) . '</td>'
                        . '<td class="text-center">' . h($row['kadaluwarsa_ktg']) . '</td>'
                        . '<td class="text-center">' . h($row['tglTerima']) . '</td>'
                        . '<td class="text-center">' . h($row['user_barcode']) . '</td>'
                        . '<td class="text-center">' . h($row['tglmutasi']) . '</td>'
                        . '<td class="text-center">' . h($row['user_mutasi']) . '</td>'
                        . '</tr>';
                }
            } else {
                $rows = '<tr><td colspan="14" class="text-center">TIDAK ADA DATA</td></tr>';
            }

            echo '<br><div class="row"><div class="col-md-12"><div class="table-responsive"><table class="table table-responsive table-hover table-bordered" style="white-space:nowrap;"><thead><tr class="w3-theme-d4">'
                . '<th style="width:50px;" class="text-center"><input class="abc" type="checkbox" id="allcb"></th>'
                . '<th class="text-center">No</th><th class="text-center">Kantong</th><th class="text-center">No Selang</th><th class="text-center">Merk</th><th class="text-center">Vol</th><th class="text-center">Jenis</th><th class="text-center">Status</th><th class="text-center">Lot</th><th class="text-center">ED</th><th class="text-center">Tgl Barcode</th><th class="text-center">Ptgs Barcode</th><th class="text-center">Tgl Mutasi</th><th class="text-center">Ptgs Mutasi</th>'
                . '</tr></thead><tbody>' . $rows . '</tbody></table></div></div></div>';
            echo '<script>$("#allcb").change(function () { $("tbody tr td input[type=checkbox]").prop("checked", $(this).prop("checked")); });</script>';
        }
        break;

    case 'mutasiadd':
        $v_tgl1 = mysqli_real_escape_string($dbi, post('InpTgl'));
        $v_tujuan = mysqli_real_escape_string($dbi, post('InpTujuan'));
        $v_kantong = mysqli_real_escape_string($dbi, post('kantong'));
        $v_kantong = substr($v_kantong, 0, -1) . 'A';
        $v_user = $namauser;

        $pesan = '';
        $simpan = '0';
        $detail_rows = '';

        $ceklist = mysqli_query($dbi, "SELECT `mt_id` FROM `stokkantong_mutasi` WHERE `mt_transaksi`='$v_user' AND `mt_nokantong`='$v_kantong'");
        if ($ceklist && mysqli_num_rows($ceklist) > 0) {
            $pesan = 'Nomor kantong ' . $v_kantong . ' sudah ada dalam list mutasi ini';
        } else {
            $qkantong = mysqli_query($dbi, "SELECT `noKantong`, `volumeasal`, `merk`, `jenis`, `Status`, `tglTerima`, `sah`,`StatTempat`, `tglmutasi`, `kadaluwarsa_ktg`, `nolot_ktg`, `user_barcode`, `user_mutasi`
                                           FROM `stokkantong`
                                           WHERE `noKantong`='$v_kantong'");
            if ($qkantong && mysqli_num_rows($qkantong) > 0) {
                $dtkantong = mysqli_fetch_assoc($qkantong);
                $k_status = $dtkantong['Status'];
                $k_tempat = $dtkantong['StatTempat'];
                $k_lot = $dtkantong['nolot_ktg'];
                $k_ed = $dtkantong['kadaluwarsa_ktg'];
                $k_tglbarcode = $dtkantong['tglTerima'];
                $k_jenis = $dtkantong['jenis'];
                $k_merk = $dtkantong['merk'];
                $k_volume = $dtkantong['volumeasal'];
                $statuskantong = cekstatuskantong($k_status, $k_tempat, $dtkantong['sah']);

                $masterkantong = mysqli_query($dbi, "SELECT `merk`,`jenis`,`vol`,`lama_buka` FROM `master_kantong` WHERE `merk`='$k_merk' AND `jenis`='$k_jenis' AND `vol`='$k_volume'");
                if ($masterkantong && mysqli_num_rows($masterkantong) > 0) {
                    $st_dtmaster = mysqli_fetch_assoc($masterkantong);
                    $st_lamabuka = $st_dtmaster['lama_buka'];
                    $dtglbuka = date_create($k_tglbarcode);
                    date_add($dtglbuka, date_interval_create_from_date_string($st_lamabuka . ' days'));
                    $edbuka = date_format($dtglbuka, 'Y-m-d');
                } else {
                    $st_lamabuka = 1;
                    $edbuka = date('Y-m-d', strtotime('+1 day'));
                }

                if ($k_tempat == '1') {
                    $pesan = 'Nomor kantong <b>' . h($v_kantong) . '</b> tidak dapat dimutasikan, status kantong : <b>' . h($statuskantong) . '</b> tanggal mutasi <b>' . h($dtkantong['tglmutasi']) . '</b>';
                } else {
                    $sqlinsert = "INSERT INTO `stokkantong_mutasi`
                                  (`mt_transaksi`, `mt_nokantong`, `mt_box`, `mt_tanggal`, `mt_tujuan`, `mt_lot`, `mt_edkantong`, `mt_tglbarcode`, `mt_user`, `mt_status`, `mt_merk`, `mt_jenis`, `mt_volume`, `mt_standarlamabuka`, `mt_edbuka`)
                                  VALUES ('$v_user', '$v_kantong', '-', '" . mysqli_real_escape_string($dbi, $v_tgl1) . "', '$v_tujuan', '$k_lot', '$k_ed', '$k_tglbarcode', '$v_user', '0', '$k_merk', '$k_jenis', '$k_volume', '$st_lamabuka', '$edbuka')";
                    $insertmutasitemp = mysqli_query($dbi, $sqlinsert);
                    if ($insertmutasitemp) {
                        $pesan = 'Kantong <b>' . h($v_kantong) . '</b> sudah dimasukan dalam list mutasi kantong';
                    } else {
                        $pesan = 'Kantong <b>' . h($v_kantong) . '</b> tidak dapat dimasukan dalan list mutasi kantong <small>' . h(mysqli_error($dbi)) . '</small>';
                    }
                }
            } else {
                $pesan = 'Kantong <b>' . h($v_kantong) . '</b> tidak ditemukan';
            }
        }

        $detailtemporary = mysqli_query($dbi, "SELECT sm.mt_id, sm.mt_transaksi, sm.mt_nokantong, sm.mt_box, sm.mt_tanggal, sm.mt_tujuan,
                                                     sm.mt_lot, sm.mt_edkantong, sm.mt_tglbarcode, sm.mt_user, sm.mt_status, sm.mt_merk, sm.mt_jenis, sm.mt_volume, sm.mt_oninsert,
                                                     mk.lama_buka,
                                                     CASE WHEN mk.lama_buka IS NULL THEN '1' ELSE mk.lama_buka END AS lamabuka,
                                                     CASE WHEN mk.lama_buka IS NULL THEN DATE_ADD(sm.mt_tglbarcode, INTERVAL 1 DAY) ELSE DATE_ADD(sm.mt_tglbarcode, INTERVAL mk.lama_buka DAY) END AS ED_buka
                                              FROM master_kantong AS mk
                                              RIGHT JOIN stokkantong_mutasi AS sm
                                                ON mk.merk = sm.mt_merk AND mk.jenis = sm.mt_jenis AND mk.vol = sm.mt_volume
                                              WHERE sm.mt_transaksi='$v_user'");

        $no = 0;
        $idtable = '<table class="table table-responsive table-bordered"><thead><tr class="w3-theme-d4"><th>No</th><th>No Kantong</th><th>Merk</th><th>Jenis</th><th>Volume</th><th>Nomor Lot</th><th>ED Kantong</th><th>Tgl Barcode</th><th>Standar Lama Buka</th></tr></thead><tbody>';
        if ($detailtemporary && mysqli_num_rows($detailtemporary) > 0) {
            while ($dttmp = mysqli_fetch_assoc($detailtemporary)) {
                $no++;
                $idtable .= '<tr>'
                    . '<td>' . $no . '</td>'
                    . '<td>' . h($dttmp['mt_nokantong']) . '</td>'
                    . '<td>' . h($dttmp['mt_merk']) . '</td>'
                    . '<td>' . h(jeniskantong($dttmp['mt_jenis'])) . '</td>'
                    . '<td>' . h($dttmp['mt_volume']) . '</td>'
                    . '<td>' . h($dttmp['mt_lot']) . '</td>'
                    . '<td>' . h($dttmp['mt_edkantong']) . '</td>'
                    . '<td>' . h($dttmp['mt_tglbarcode']) . '</td>'
                    . '<td>' . h($dttmp['lamabuka']) . '</td>'
                    . '</tr>';
            }
            $simpan = '1';
        } else {
            $idtable .= '<tr><td class="text-center" colspan="9">Belum ada data transaksi mutasi kantong</td></tr>';
        }
        $idtable .= '</tbody></table>';
        $pesan .= '. JUMLAH KANTONG (<b>' . $no . '</b>)';
        $output = $pesan . '|' . $idtable . '|' . $simpan;
        echo $output;
        break;

    case 'mutasisave':
        $v_tujuan = mysqli_real_escape_string($dbi, post('InpTujuan'));
        $v_user = $namauser;
        $pesan = '';
        $idtable = '';
        $simpan = '0';

        $k_today = 'MS' . date('ym');
        $kd = mysqli_query($dbi, "SELECT `mt_transaksi` FROM `stokkantong_mutasi` WHERE `mt_transaksi` LIKE '" . mysqli_real_escape_string($dbi, $k_today) . "%' ORDER BY `mt_transaksi` DESC LIMIT 1");

        if ($kd && mysqli_num_rows($kd) > 0) {
            $kd1 = mysqli_fetch_assoc($kd);
            $kd2 = substr($kd1['mt_transaksi'], 8, 6);
        } else {
            $kd2 = '000000';
        }

        if ((int)$kd2 < 1) {
            $kd2 = '000000';
        }

        $int_kd2 = (int)$kd2 + 1;
        $i_zero = 6 - strlen((string)$int_kd2);
        $kd3 = '';
        for ($n = 0; $n < $i_zero; $n++) {
            $kd3 .= '0';
        }

        $notrans = $k_today . $kd3 . $int_kd2;
        $updmutasi = mysqli_query($dbi, "UPDATE `stokkantong_mutasi` SET `mt_transaksi`='$notrans' WHERE `mt_transaksi`='$v_user'");

        if ($updmutasi) {
            $pesan = 'Proses pemindahan kantong ke ' . $v_tujuan . ' No Transaksi ' . $notrans . ' BERHASIL';

            $sqlmutasi = mysqli_query($dbi, "SELECT `mt_nokantong`,`mt_tanggal`,`mt_user`,`mt_transaksi`,`mt_jenis` FROM `stokkantong_mutasi` WHERE `mt_transaksi`='$notrans'");
            if ($sqlmutasi && mysqli_num_rows($sqlmutasi) > 0) {
                while ($setmutasi = mysqli_fetch_assoc($sqlmutasi)) {
                    $m_nokantong = $setmutasi['mt_nokantong'];
                    $m_tanggal = $setmutasi['mt_tanggal'];
                    $m_usermutasi = $setmutasi['mt_user'];
                    $m_jenis = (int)$setmutasi['mt_jenis'];
                    $v_kantong0 = substr($m_nokantong, 0, -1);
                    $sufixkantong = 'A';

                    for ($x = 1; $x <= $m_jenis; $x++) {
                        $proses_kantongmutasi = $v_kantong0 . $sufixkantong;
                        mysqli_query($dbi, "UPDATE `stokkantong`
                                            SET `StatTempat`='1',
                                                `tglmutasi`='" . mysqli_real_escape_string($dbi, $m_tanggal) . "',
                                                `user_mutasi`='" . mysqli_real_escape_string($dbi, $m_usermutasi) . "'
                                            WHERE `noKantong`='" . mysqli_real_escape_string($dbi, $proses_kantongmutasi) . "'");
                        $sufixkantong++;
                    }

                    $log_mdl = 'LOGISTIK';
                    $log_aksi = 'Pengesahan Kantong Logistik: ' . $m_nokantong;
                    $tmp = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `id`,`id1` FROM `tempat_donor` WHERE `active`='1'"));
                    $tempat = isset($tmp['id1']) ? $tmp['id1'] : '';
                    $client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                    $time_aksi = date('Y-m-d H:i:s');
                    @include_once "../modul/user_log.php";
                }
            }

            $sqlsum = mysqli_query($dbi, "SELECT `mt_merk`,`mt_jenis`,`mt_volume`,`mt_tujuan`, COUNT(`mt_nokantong`) AS jml
                                          FROM `stokkantong_mutasi`
                                          WHERE `mt_transaksi`='$notrans'
                                          GROUP BY `mt_merk`,`mt_jenis`,`mt_volume`,`mt_tujuan`");
            if ($sqlsum && mysqli_num_rows($sqlsum) > 0) {
                $idtable = '<table class="table table-responsive table-bordered"><thead><tr class="w3-theme-d4"><th>No</th><th>Merk</th><th>Volume</th><th>Jenis</th><th>Jumlah</th></tr></thead><tbody>';
                $no = 0;
                while ($st = mysqli_fetch_assoc($sqlsum)) {
                    $no++;
                    $idtable .= '<tr>'
                        . '<td>' . $no . '</td>'
                        . '<td>' . h($st['mt_merk']) . '</td>'
                        . '<td>' . h($st['mt_volume']) . '</td>'
                        . '<td>' . h(jeniskantong($st['mt_jenis'])) . '</td>'
                        . '<td>' . h($st['jml']) . '</td>'
                        . '</tr>';
                }
                $idtable .= '</tbody></table>';
            }
        } else {
            $pesan = 'Proses pemindahan kantong ke ' . $v_tujuan . ' No Transaksi ' . $notrans . ' TIDAK BERHASIL';
        }

        $output = $pesan . '|' . $idtable . '|' . $simpan;
        echo $output;
        break;

    case 'rekapdokumenmutasi':
        $v_tgl1 = mysqli_real_escape_string($dbi, get('tgl1', date('Y-m-d')));
        $v_tgl2 = mysqli_real_escape_string($dbi, get('tgl2', date('Y-m-d')));
        $v_tujuan = mysqli_real_escape_string($dbi, get('tujuan', ''));
        $v_user = mysqli_real_escape_string($dbi, get('user', ''));

        if ($v_tgl1 > $v_tgl2) {
            $v_tgl2 = $v_tgl1;
        }

        $wtujuan = ($v_tujuan !== '') ? " AND (`mt_tujuan` LIKE '%$v_tujuan%') " : '';
        $wuser   = ($v_user !== '') ? " AND (`mt_user` LIKE '%$v_user%') " : '';
        $sql = "SELECT `mt_transaksi`,`mt_tanggal`,`mt_tujuan`,`mt_user`, COUNT(`mt_id`) AS `jml_kantong`
                FROM `stokkantong_mutasi`
                WHERE DATE(`mt_tanggal`) BETWEEN '$v_tgl1' AND '$v_tgl2' $wtujuan $wuser
                GROUP BY `mt_transaksi`,`mt_tanggal`,`mt_tujuan`,`mt_user`";

        $qrylist = mysqli_query($dbi, $sql);
        $detailtable = '';
        if ($qrylist && mysqli_num_rows($qrylist) > 0) {
            $no = 0;
            while ($dt = mysqli_fetch_assoc($qrylist)) {
                $no++;
                $detailtable .= '<tr>'
                    . '<td class="text-center">' . $no . '</td>'
                    . '<td class="text-center"><a href="#" data-id="' . h($dt['mt_transaksi']) . '" data-toggle="modal" data-target="#mPrintDokumen" class="w3-hover-theme cetakdokumen" title="Cetak Dokumen Serah Terima Kantong">' . h($dt['mt_transaksi']) . '</a></td>'
                    . '<td class="text-center">' . h($dt['mt_tanggal']) . '</td>'
                    . '<td class="text-center">' . h($dt['mt_tujuan']) . '</td>'
                    . '<td class="text-center">' . h($dt['mt_user']) . '</td>'
                    . '<td class="text-center">' . h($dt['jml_kantong']) . '</td>'
                    . '</tr>';
            }
        } else {
            $detailtable = '<tr><td colspan="6" class="text-center">TIDAK ADA DATA</td></tr>';
        }

        echo '<div class="row"><div class="col-md-12"><div class="table-responsive"><table class="table table-responsive table-bordered table-hover">'
            . '<thead><tr class="w3-theme-d4"><th class="text-center">No</th><th class="text-center">No Transaksi</th><th class="text-center">Tanggal</th><th class="text-center">Tujuan</th><th class="text-center">User</th><th class="text-center">Jumlah</th></tr></thead>'
            . '<tbody>' . $detailtable . '</tbody></table></div></div></div>';
        break;

    case 'cetakdokumenmutasi':
        $v_nomortransaksi = mysqli_real_escape_string($dbi, get('param', ''));
        echo render_iframe('barcodekantong/barcode_cetakmutasi.php?param=' . urlencode($v_nomortransaksi), 120);
        break;

    case 'rekappakai':
        $v_tgl1 = get('tgl1', date('Y-m-d'));
        $v_tgl2 = get('tgl2', date('Y-m-d'));
        $wstatus = '';
        $wjenis = '';
        $sel = "SELECT merk, volumeasal, jenis,
                       CASE
                           WHEN jenis='1' THEN 'Single'
                           WHEN jenis='2' THEN 'Double'
                           WHEN jenis='3' THEN 'Triple'
                           WHEN jenis='4' THEN 'Quadruple'
                           WHEN jenis='5' THEN 'Quintuple'
                           WHEN jenis='6' THEN 'Sextuple/Pediatric'
                           WHEN jenis='7' THEN 'Septuple'
                           WHEN jenis='8' THEN 'Octuple'
                           WHEN jenis='9' THEN 'Nonuple'
                           WHEN jenis='10' THEN 'Decuple'
                       END AS namajenis,
                       COUNT(noKantong) AS jml
                FROM stokkantong
                WHERE (DATE(`tglTerima`) BETWEEN '$v_tgl1' AND '$v_tgl2') AND (ident='m') $wjenis $wstatus
                GROUP BY `merk`,`volumeasal`,`jenis`";
        $data = mysqli_query($dbi, $sel);

        $rows = '';
        $no = 0;
        $total = 0;
        if ($data && mysqli_num_rows($data) > 0) {
            while ($d = mysqli_fetch_assoc($data)) {
                $no++;
                $total += (int)$d['jml'];
                $rows .= '<tr><td class="text-right">' . $no . '.</td><td class="text-left">' . h($d['merk']) . '</td><td class="text-center">' . h($d['volumeasal']) . '</td><td class="text-left">' . h($d['namajenis']) . '</td><td class="text-center">' . number_format((int)$d['jml'], 0, '.', ',') . '</td></tr>';
            }
        } else {
            $rows = '<tr><td colspan="5" class="text-center">TIDAK ADA DATA</td></tr>';
        }

        echo '<div class="row"><div class="col-md-12"><div class="table-responsive"><table class="table table-responsive table-bordered table-hover">'
            . '<thead><tr class="w3-theme-d4"><th>No</th><th>Merk</th><th>Volume</th><th>Jenis</th><th>Jumlah</th></tr></thead>'
            . '<tbody>' . $rows . '<tr><td colspan="4" class="text-right">Total Barcode</td><td class="text-center">' . number_format($total, 0, ".", ",") . '</td></tr></tbody></table></div></div></div>';
        break;

    default:
        echo '';
        break;
}
