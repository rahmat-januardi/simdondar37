<?php

/**
 * ksl_terima_proses.php
 *
 * CHANGELOG:
 *  - Blok source=download diperluas: membaca raw_json dari staging,
 *    lalu insert ke SEMUA tabel (serahterima, serahterima_detail,
 *    stokkantong, pendonor, htransaksi) persis seperti path online.
 *  - Tidak ada perubahan pada blok online.
 */
session_start();
include '../config/dbi_connect.php';

header("Content-Type: application/json");

if (!isset($_POST['noTransaksi'])) {
    echo json_encode(array("status" => "error", "message" => "Parameter tidak lengkap"));
    exit;
}

$udd = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama`,`id` FROM `utd` WHERE `aktif`='1';"));
$id_uddaktif   = $udd['id'];
$nama_uddaktif = $udd['nama'];

$g_noserahterima = mysqli_real_escape_string($dbi, trim($_POST['noTransaksi']));
$source          = isset($_POST['source']) ? trim($_POST['source']) : 'online';

$shift_terima = mysqli_fetch_assoc(mysqli_query(
    $dbi,
    "SELECT `nama` FROM `shift` WHERE `jam`<=current_time() AND `sampai_jam`>=current_time()"
));
$shift     = ($shift_terima && isset($shift_terima['nama'])) ? $shift_terima['nama'] : '';

$arr_sr       = array();
$arr_srd      = array();
$arr_kantong  = array();
$arr_pendonor = array();
$arr_ht       = array();

$tgl       = date('Ymd');
$token     = "17091945" . $tgl;
$sekarang  = date("Y-m-d H:i:s");
$jam_donor = date("H:i:s");
$tpk       = '';

// ══════════════════════════════════════════════════════════════════════════════
// HANDLER SOURCE = DOWNLOAD
// Baca raw_json dari staging → insert ke semua tabel → update status=1
// ══════════════════════════════════════════════════════════════════════════════
if ($source === 'download') {

    $notrans_esc = $g_noserahterima;

    // Cek data ada dan belum diproses
    $cekRow = mysqli_fetch_assoc(mysqli_query(
        $dbi,
        "SELECT * FROM `ksl_import_antrian`
         WHERE `notrans`='$notrans_esc' AND `status`=0 LIMIT 1"
    ));
    if (!$cekRow) {
        echo json_encode(array("status" => "error", "message" => "Data tidak ditemukan atau sudah diproses sebelumnya."));
        exit;
    }

    // ── Parse JSON lengkap dari kolom raw_json ────────────────────────────────
    $fullData = json_decode($cekRow['raw_json'], true);
    if (!$fullData) {
        echo json_encode(array("status" => "error", "message" => "Gagal membaca raw_json dari staging. Silakan import ulang file JSON."));
        exit;
    }

    $arr_sr       = isset($fullData['serahterima'])       ? $fullData['serahterima']       : array();
    $arr_srd      = isset($fullData['serahterimadetail']) ? $fullData['serahterimadetail'] : array();
    $arr_kantong  = isset($fullData['stokkantong'])       ? $fullData['stokkantong']       : array();
    $arr_pendonor = isset($fullData['pendonor'])          ? $fullData['pendonor']          : array();
    $arr_ht       = isset($fullData['htransaksi'])        ? $fullData['htransaksi']        : array();

    // ── Helper escape ─────────────────────────────────────────────────────────
    // Ambil nilai dari array dengan fallback '', lalu escape
    function esc($dbi, $arr, $key)
    {
        return mysqli_real_escape_string($dbi, isset($arr[$key]) ? (string)$arr[$key] : '');
    }

    // ── 1. INSERT serahterima ─────────────────────────────────────────────────
    $v_hst_notrans        = esc($dbi, $arr_sr, 'hst_notrans');
    $v_hst_bagpengirim    = esc($dbi, $arr_sr, 'hst_bagpengirim');
    $v_hst_bagpenerima    = esc($dbi, $arr_sr, 'hst_bagpenerima');
    $v_hst_tgl            = esc($dbi, $arr_sr, 'hst_tgl');
    $v_hst_asal           = esc($dbi, $arr_sr, 'hst_asal');
    $v_hst_jenis_st       = esc($dbi, $arr_sr, 'hst_jenis_st');
    $v_hst_user           = esc($dbi, $arr_sr, 'hst_user');
    $v_hst_pengirim       = esc($dbi, $arr_sr, 'hst_pengirim');
    $v_hst_penerima       = esc($dbi, $arr_sr, 'hst_penerima');
    $v_hst_penerima2      = esc($dbi, $arr_sr, 'hst_penerima2');
    $v_hst_penerima3      = esc($dbi, $arr_sr, 'hst_penerima3');
    $v_hst_kode_alat      = esc($dbi, $arr_sr, 'hst_kode_alat');
    $v_hst_suhuterima     = esc($dbi, $arr_sr, 'hst_suhuterima');
    $v_hst_kondisiumum    = esc($dbi, $arr_sr, 'hst_kondisiumum');
    $v_hst_peruntukan     = esc($dbi, $arr_sr, 'hst_peruntukan');
    $v_hst_modul          = esc($dbi, $arr_sr, 'hst_modul');
    $v_hst_termometer     = esc($dbi, $arr_sr, 'hst_termometer');
    $v_hst_dariudd        = esc($dbi, $arr_sr, 'hst_dariudd');
    $v_hst_keudd          = esc($dbi, $arr_sr, 'hst_keudd');

    // Jika hst_notrans dari JSON kosong, pakai notrans dari staging
    if ($v_hst_notrans === '') {
        $v_hst_notrans = mysqli_real_escape_string($dbi, $cekRow['notrans']);
    }
    // hst_bagpengirim fallback ke udd_asal_id dari staging
    if ($v_hst_bagpengirim === '') {
        $v_hst_bagpengirim = mysqli_real_escape_string($dbi, $cekRow['udd_asal_id']);
    }
    // hst_tgl fallback ke hst_tgl dari staging
    if ($v_hst_tgl === '') {
        $v_hst_tgl = mysqli_real_escape_string($dbi, $cekRow['hst_tgl']);
    }
    // hst_asal fallback ke hst_asal dari staging
    if ($v_hst_asal === '') {
        $v_hst_asal = mysqli_real_escape_string($dbi, $cekRow['hst_asal']);
    }

    $querySR = "INSERT INTO `serahterima`
        (`hst_notrans`,`hst_bagpengirim`,`hst_bagpenerima`,`hst_tgl`,`hst_asal`,
         `hst_jenis_st`,`hst_user`,`hst_pengirim`,`hst_penerima`,`hst_penerima2`,`hst_penerima3`,
         `hst_kode_alat`,`hst_suhuterima`,`hst_kondisiumum`,`hst_peruntukan`,`hst_modul`,
         `hst_shift_penerima`,`hst_termometer`,`hst_dariudd`,`hst_keudd`)
        VALUES
        ('$v_hst_notrans','$v_hst_bagpengirim','$v_hst_bagpenerima','$v_hst_tgl','$v_hst_asal',
         '$v_hst_jenis_st','$v_hst_user','$v_hst_pengirim','$v_hst_penerima','$v_hst_penerima2','$v_hst_penerima3',
         '$v_hst_kode_alat','$v_hst_suhuterima','$v_hst_kondisiumum','$v_hst_peruntukan','$v_hst_modul',
         '$shift','$v_hst_termometer','$v_hst_dariudd','$v_hst_keudd')";

    $resultSR = mysqli_query($dbi, $querySR);
    if (!$resultSR && mysqli_errno($dbi) != 1062) {
        // 1062 = duplicate key, lanjutkan saja
        echo json_encode(array("status" => "error", "message" => "Gagal insert serahterima: " . mysqli_error($dbi)));
        exit;
    }

    // ── 2. INSERT serahterima_detail ──────────────────────────────────────────
    $inserted_rows = 0;
    foreach ($arr_srd as $itemskantong) {
        $dst_no_aftap          = esc($dbi, $itemskantong, 'dst_no_aftap');
        $dst_tglaftap          = esc($dbi, $itemskantong, 'dst_tglaftap');
        $dst_notrans           = esc($dbi, $itemskantong, 'dst_notrans');
        $dst_nokantong         = esc($dbi, $itemskantong, 'dst_nokantong');
        $dst_produk            = esc($dbi, $itemskantong, 'dst_produk');
        $dst_statusktg         = esc($dbi, $itemskantong, 'dst_statusktg');
        $st_statusktg_new      = esc($dbi, $itemskantong, 'st_statusktg_new');
        $dst_old_position      = esc($dbi, $itemskantong, 'dst_old_position');
        $dst_new_position      = esc($dbi, $itemskantong, 'dst_new_position');
        $dst_sahktg            = esc($dbi, $itemskantong, 'dst_sahktg');
        $dst_sahktg_new        = esc($dbi, $itemskantong, 'dst_sahktg_new');
        $dst_merk              = esc($dbi, $itemskantong, 'dst_merk');
        $dst_golda             = esc($dbi, $itemskantong, 'dst_golda');
        $dst_rh                = esc($dbi, $itemskantong, 'dst_rh');
        $dst_kodedonor         = esc($dbi, $itemskantong, 'dst_kodedonor');
        $dst_berat             = esc($dbi, $itemskantong, 'dst_berat');
        $dst_volumektg         = esc($dbi, $itemskantong, 'dst_volumektg');
        $dst_jenisktg          = esc($dbi, $itemskantong, 'dst_jenisktg');
        $dst_sample            = esc($dbi, $itemskantong, 'dst_sample');
        $dst_sah               = esc($dbi, $itemskantong, 'dst_sah');
        $dst_dsdp              = esc($dbi, $itemskantong, 'dst_dsdp');
        $dst_lamabaru          = esc($dbi, $itemskantong, 'dst_lamabaru');
        $dst_umur              = esc($dbi, $itemskantong, 'dst_umur');
        $dst_lama_aftap        = esc($dbi, $itemskantong, 'dst_lama_aftap');
        $dst_statuspengambilan = esc($dbi, $itemskantong, 'dst_statuspengambilan');
        $dst_kel               = esc($dbi, $itemskantong, 'dst_kel');
        $dst_ptgaftap          = esc($dbi, $itemskantong, 'dst_ptgaftap');
        $dst_volambil          = esc($dbi, $itemskantong, 'dst_volambil');
        $dst_receive1          = esc($dbi, $itemskantong, 'dst_receive1');
        $dst_stat_receive1     = esc($dbi, $itemskantong, 'dst_stat_receive1');
        $dst_date_receive1     = esc($dbi, $itemskantong, 'dst_date_receive1');
        $dst_shift_receive1    = esc($dbi, $itemskantong, 'dst_shift_receive1');
        $dst_receive2          = esc($dbi, $itemskantong, 'dst_receive2');
        $dst_stat_receive2     = esc($dbi, $itemskantong, 'dst_stat_receive2');
        $dst_date_receive2     = esc($dbi, $itemskantong, 'dst_date_receive2');
        $dst_shift_receive2    = esc($dbi, $itemskantong, 'dst_shift_receive2');
        $dst_receive3          = esc($dbi, $itemskantong, 'dst_receive3');
        $dst_stat_receive3     = esc($dbi, $itemskantong, 'dst_stat_receive3');
        $dst_date_receive3     = esc($dbi, $itemskantong, 'dst_date_receive3');
        $dst_shift_receive3    = esc($dbi, $itemskantong, 'dst_shift_receive3');
        $simltd                = esc($dbi, $itemskantong, 'simltd');
        $skgd                  = esc($dbi, $itemskantong, 'skgd');
        $snat                  = esc($dbi, $itemskantong, 'snat');
        $packing               = esc($dbi, $itemskantong, 'packing');
        $label                 = esc($dbi, $itemskantong, 'label');
        $splasma               = esc($dbi, $itemskantong, 'splasma');
        $sserum                = esc($dbi, $itemskantong, 'sserum');
        $swb                   = esc($dbi, $itemskantong, 'swb');
        $volket                = esc($dbi, $itemskantong, 'volket');
        $lisis                 = esc($dbi, $itemskantong, 'lisis');
        $dokumen               = esc($dbi, $itemskantong, 'dokumen');
        $infoklinis            = esc($dbi, $itemskantong, 'infoklinis');
        $no_cb                 = esc($dbi, $itemskantong, 'no_cb');
        $suhu_cb               = esc($dbi, $itemskantong, 'suhu_cb');
        $up_data               = esc($dbi, $itemskantong, 'up_data');
        $dst_donasi            = esc($dbi, $itemskantong, 'dst_donasi');
        $dst_samplevol         = esc($dbi, $itemskantong, 'dst_samplevol');
        $dst_samplejml         = esc($dbi, $itemskantong, 'dst_samplejml');
        $dst_tglolah           = esc($dbi, $itemskantong, 'dst_tglolah');
        $dst_tgled             = esc($dbi, $itemskantong, 'dst_tgled');
        $dst_tglrelease        = esc($dbi, $itemskantong, 'dst_tglrelease');
        $dts_hasilrelease      = esc($dbi, $itemskantong, 'dts_hasilrelease');
        $dst_suhusimpan        = esc($dbi, $itemskantong, 'dst_suhusimpan');
        $dst_reposisijarum     = esc($dbi, $itemskantong, 'dst_reposisijarum');
        $dst_caraambil         = esc($dbi, $itemskantong, 'dst_caraambil');
        $dst_tempataftap       = esc($dbi, $itemskantong, 'dst_tempataftap');
        $dst_imltd             = esc($dbi, $itemskantong, 'dst_imltd');
        $dst_kgd               = esc($dbi, $itemskantong, 'dst_kgd');

        // dst_notrans dari JSON mungkin kosong, pakai dari staging
        if ($dst_notrans === '') {
            $dst_notrans = mysqli_real_escape_string($dbi, $cekRow['notrans']);
        }

        $inserted_rows++;
        $querysrd = "INSERT INTO `serahterima_detail`
            (`dst_no_aftap`,`dst_tglaftap`,`dst_notrans`,`dst_nokantong`,`dst_produk`,
             `dst_statusktg`,`st_statusktg_new`,`dst_old_position`,`dst_new_position`,`dst_sahktg`,
             `dst_sahktg_new`,`dst_merk`,`dst_golda`,`dst_rh`,`dst_kodedonor`,`dst_berat`,`dst_volumektg`,
             `dst_jenisktg`,`dst_sample`,`dst_sah`,`dst_dsdp`,`dst_lamabaru`,`dst_umur`,`dst_lama_aftap`,
             `dst_statuspengambilan`,`dst_kel`,`dst_ptgaftap`,`dst_volambil`,`dst_receive1`,`dst_stat_receive1`,
             `dst_date_receive1`,`dst_shift_receive1`,`dst_receive2`,`dst_stat_receive2`,`dst_date_receive2`,
             `dst_shift_receive2`,`dst_receive3`,`dst_stat_receive3`,`dst_date_receive3`,`dst_shift_receive3`,
             `simltd`,`skgd`,`snat`,`packing`,`label`,`splasma`,`sserum`,`swb`,`volket`,`lisis`,`dokumen`,
             `infoklinis`,`no_cb`,`suhu_cb`,`up_data`,`dst_donasi`,`dst_samplevol`,`dst_samplejml`,`dst_tglolah`,
             `dst_tgled`,`dst_tglrelease`,`dts_hasilrelease`,`dst_suhusimpan`,`dst_reposisijarum`,
             `dst_caraambil`,`dst_tempataftap`,`dst_imltd`,`dst_kgd`)
        VALUES
            ('$dst_no_aftap','$dst_tglaftap','$dst_notrans','$dst_nokantong','$dst_produk',
             '$dst_statusktg','$st_statusktg_new','$dst_old_position','$dst_new_position','$dst_sahktg',
             '$dst_sahktg_new','$dst_merk','$dst_golda','$dst_rh','$dst_kodedonor','$dst_berat','$dst_volumektg',
             '$dst_jenisktg','$dst_sample','$dst_sah','$dst_dsdp','$dst_lamabaru','$dst_umur','$dst_lama_aftap',
             '$dst_statuspengambilan','$dst_kel','$dst_ptgaftap','$dst_volambil','$dst_receive1','$dst_stat_receive1',
             '$dst_date_receive1','$dst_shift_receive1','$dst_receive2','$dst_stat_receive2','$dst_date_receive2',
             '$dst_shift_receive2','$dst_receive3','$dst_stat_receive3','$dst_date_receive3','$dst_shift_receive3',
             '$simltd','$skgd','$snat','$packing','$label','$splasma','$sserum','$swb','$volket','$lisis','$dokumen',
             '$infoklinis','$no_cb','$suhu_cb','$up_data','$dst_donasi','$dst_samplevol','$dst_samplejml','$dst_tglolah',
             '$dst_tgled','$dst_tglrelease','$dts_hasilrelease','$dst_suhusimpan','$dst_reposisijarum',
             '$dst_caraambil','$dst_tempataftap','$dst_imltd','$dst_kgd')";
        mysqli_query($dbi, $querysrd);
    }

    // ── 3. INSERT stokkantong ─────────────────────────────────────────────────
    $insertedkantong_rows = 0;
    foreach ($arr_kantong as $itemskantong) {
        $noKantong          = esc($dbi, $itemskantong, 'noKantong');
        $jenis              = esc($dbi, $itemskantong, 'jenis');
        $Status             = esc($dbi, $itemskantong, 'Status');
        $tglTerima          = esc($dbi, $itemskantong, 'tglTerima');
        $tglEDBuka          = esc($dbi, $itemskantong, 'tglEDBuka');
        $volume             = esc($dbi, $itemskantong, 'volume');
        $merk               = esc($dbi, $itemskantong, 'merk');
        $kantongAsal        = esc($dbi, $itemskantong, 'kantongAsal');
        $produk             = esc($dbi, $itemskantong, 'produk');
        $sah                = esc($dbi, $itemskantong, 'sah');
        $position           = esc($dbi, $itemskantong, 'position');
        $opname_count       = esc($dbi, $itemskantong, 'opname_count');
        $opname_lasttime    = esc($dbi, $itemskantong, 'opname_lasttime');
        $Isi                = esc($dbi, $itemskantong, 'Isi');
        $gol_darah          = esc($dbi, $itemskantong, 'gol_darah');
        $RhesusDrh          = esc($dbi, $itemskantong, 'RhesusDrh');
        $stat2              = esc($dbi, $itemskantong, 'stat2');
        $StatTempat         = esc($dbi, $itemskantong, 'StatTempat');
        $kodePendonor       = esc($dbi, $itemskantong, 'kodePendonor');
        $kodePendonor_lama  = esc($dbi, $itemskantong, 'kodePendonor_lama');
        $statKonfirmasi     = esc($dbi, $itemskantong, 'statKonfirmasi');
        $tgl_konfirmasi     = esc($dbi, $itemskantong, 'tgl_konfirmasi');
        $statQC             = esc($dbi, $itemskantong, 'statQC');
        $AsalUTD            = esc($dbi, $itemskantong, 'AsalUTD');
        $tgl_Aftap          = esc($dbi, $itemskantong, 'tgl_Aftap');
        $kadaluwarsa        = esc($dbi, $itemskantong, 'kadaluwarsa');
        $pengambilan        = esc($dbi, $itemskantong, 'pengambilan');
        $tglpengolahan      = esc($dbi, $itemskantong, 'tglpengolahan');
        $tglperiksa         = esc($dbi, $itemskantong, 'tglperiksa');
        $metoda             = esc($dbi, $itemskantong, 'metoda');
        $mu                 = esc($dbi, $itemskantong, 'mu');
        $stokcheck          = esc($dbi, $itemskantong, 'stokcheck');
        $ident              = esc($dbi, $itemskantong, 'ident');
        $volumeasal         = esc($dbi, $itemskantong, 'volumeasal');
        $tgl_keluar         = esc($dbi, $itemskantong, 'tgl_keluar');
        $tglmutasi          = esc($dbi, $itemskantong, 'tglmutasi');
        $hasil              = esc($dbi, $itemskantong, 'hasil');
        $kadaluwarsa_ktg    = esc($dbi, $itemskantong, 'kadaluwarsa_ktg');
        $nolot_ktg          = esc($dbi, $itemskantong, 'nolot_ktg');
        $hasilNAT           = esc($dbi, $itemskantong, 'hasilNAT');
        $keterangan         = esc($dbi, $itemskantong, 'keterangan');
        $up_data            = esc($dbi, $itemskantong, 'up_data');
        $insert_on          = esc($dbi, $itemskantong, 'insert_on');
        $tgl_release        = esc($dbi, $itemskantong, 'tgl_release');
        $prolis             = esc($dbi, $itemskantong, 'prolis');
        $hasil_release      = esc($dbi, $itemskantong, 'hasil_release');
        $kodebarang         = esc($dbi, $itemskantong, 'kodebarang');
        $tglbeli            = esc($dbi, $itemskantong, 'tglbeli');
        $lama_pengambilan   = esc($dbi, $itemskantong, 'lama_pengambilan');
        $abs                = esc($dbi, $itemskantong, 'abs');
        $tgl_abs            = esc($dbi, $itemskantong, 'tgl_abs');
        $donor_tpk          = esc($dbi, $itemskantong, 'donor_tpk');
        $no_produk_tpk      = esc($dbi, $itemskantong, 'no_produk_tpk');
        $position_bag       = esc($dbi, $itemskantong, 'position_bag');
        $user_barcode       = esc($dbi, $itemskantong, 'user_barcode');
        $user_mutasi        = esc($dbi, $itemskantong, 'user_mutasi');
        $tgl_nat            = esc($dbi, $itemskantong, 'tgl_nat');
        $noSelang           = esc($dbi, $itemskantong, 'noSelang');
        $puf_status         = esc($dbi, $itemskantong, 'puf_status');

        if ($noKantong === '') continue;
        $insertedkantong_rows++;

        $querykantong = "INSERT INTO `stokkantong`
            (`noKantong`,`jenis`,`Status`,`tglTerima`,`tglEDBuka`,`volume`,`merk`,`kantongAsal`,
             `produk`,`sah`,`position`,`opname_count`,`opname_lasttime`,`Isi`,`gol_darah`,`RhesusDrh`,
             `stat2`,`StatTempat`,`kodePendonor`,`kodePendonor_lama`,`statKonfirmasi`,`tgl_konfirmasi`,
             `statQC`,`AsalUTD`,`tgl_Aftap`,`kadaluwarsa`,`pengambilan`,`tglpengolahan`,`tglperiksa`,
             `metoda`,`mu`,`stokcheck`,`ident`,`volumeasal`,`tgl_keluar`,`tglmutasi`,`hasil`,
             `kadaluwarsa_ktg`,`nolot_ktg`,`hasilNAT`,`keterangan`,`up_data`,`insert_on`,`tgl_release`,
             `prolis`,`hasil_release`,`kodebarang`,`tglbeli`,`lama_pengambilan`,`abs`,`tgl_abs`,
             `donor_tpk`,`no_produk_tpk`,`position_bag`,`user_barcode`,`user_mutasi`,`tgl_nat`,
             `noSelang`,`puf_status`)
        VALUES
            ('$noKantong','$jenis','$Status','$tglTerima','$tglEDBuka','$volume','$merk','$kantongAsal',
             '$produk','$sah','$position','$opname_count','$opname_lasttime','$Isi','$gol_darah','$RhesusDrh',
             '$stat2','$StatTempat','$kodePendonor','$kodePendonor_lama','$statKonfirmasi','$tgl_konfirmasi',
             '$statQC','$AsalUTD','$tgl_Aftap','$kadaluwarsa','$pengambilan','$tglpengolahan','$tglperiksa',
             '$metoda','$mu','$stokcheck','$ident','$volumeasal','$tgl_keluar','$tglmutasi','$hasil',
             '$kadaluwarsa_ktg','$nolot_ktg','$hasilNAT','$keterangan','$up_data','$insert_on','$tgl_release',
             '$prolis','$hasil_release','$kodebarang','$tglbeli','$lama_pengambilan','$abs','$tgl_abs',
             '$donor_tpk','$no_produk_tpk','$position_bag','$user_barcode','$user_mutasi','$tgl_nat',
             '$noSelang','$puf_status')";
        mysqli_query($dbi, $querykantong);
    }

    // ── 4. INSERT pendonor ────────────────────────────────────────────────────
    $insertpendonor_rows = 0;
    foreach ($arr_pendonor as $itemskantong) {
        $pkode                  = esc($dbi, $itemskantong, 'Kode');
        $pkodelama              = esc($dbi, $itemskantong, 'Kode_lama');
        $pnoktp                 = esc($dbi, $itemskantong, 'NoKTP');
        $pnama                  = esc($dbi, $itemskantong, 'Nama');
        $palamat                = esc($dbi, $itemskantong, 'Alamat');
        $pkelurahan             = esc($dbi, $itemskantong, 'kelurahan');
        $pkecamatan             = esc($dbi, $itemskantong, 'kecamatan');
        $pwilayah               = esc($dbi, $itemskantong, 'wilayah');
        $pkodepos               = esc($dbi, $itemskantong, 'KodePos');
        $ptempatlahir           = esc($dbi, $itemskantong, 'TempatLhr');
        $ptgllahir              = esc($dbi, $itemskantong, 'TglLhr');
        $pumur                  = esc($dbi, $itemskantong, 'umur');
        $pgoldarah              = esc($dbi, $itemskantong, 'GolDarah');
        $prhesus                = esc($dbi, $itemskantong, 'Rhesus');
        $pketdarah              = esc($dbi, $itemskantong, 'ketdarah');
        $pjk                    = esc($dbi, $itemskantong, 'Jk');
        $pstatus                = esc($dbi, $itemskantong, 'Status');
        $psukubangsa            = esc($dbi, $itemskantong, 'sukubangsa');
        $ppekerjaan             = esc($dbi, $itemskantong, 'Pekerjaan');
        $ptelp1                 = esc($dbi, $itemskantong, 'telp');
        $ptelp2                 = esc($dbi, $itemskantong, 'telp2');
        $pibukandung            = esc($dbi, $itemskantong, 'ibukandung');
        $pjmldonor              = esc($dbi, $itemskantong, 'jumDonor');
        $pcall                  = esc($dbi, $itemskantong, 'Call');
        $papheresis             = esc($dbi, $itemskantong, 'apheresis');
        $pcekal                 = esc($dbi, $itemskantong, 'Cekal');
        $pjns                   = esc($dbi, $itemskantong, 'jns');
        $ptglkembali            = esc($dbi, $itemskantong, 'tglkembali');
        $ptglkembaliapheresis   = esc($dbi, $itemskantong, 'tglkembali_apheresis');
        $pmu                    = esc($dbi, $itemskantong, 'mu');
        $p10                    = esc($dbi, $itemskantong, 'p10');
        $p25                    = esc($dbi, $itemskantong, 'p25');
        $p50                    = esc($dbi, $itemskantong, 'p50');
        $p75                    = esc($dbi, $itemskantong, 'p75');
        $p100                   = esc($dbi, $itemskantong, 'p100');
        $psatya                 = esc($dbi, $itemskantong, 'psatya');
        $pprov                  = esc($dbi, $itemskantong, 'pprov');
        $pinstansi              = esc($dbi, $itemskantong, 'instansi');
        $donor_tpk              = esc($dbi, $itemskantong, 'donor_tpk');

        if ($pkode === '') continue;
        $insertpendonor_rows++;

        $querypendonor = "INSERT INTO `pendonor`
            (`Kode`,`NoKTP`,`Nama`,`Alamat`,`Jk`,`Pekerjaan`,
             `telp`,`TempatLhr`,`TglLhr`,`Status`,`GolDarah`,
             `Rhesus`,`Call`,`kelurahan`,`kecamatan`,`wilayah`,`jumDonor`,`title`,
             `telp2`,`umur`,`tglkembali`,`tglkembali_apheresis`,
             `pencatat`,`mu`,`cekal`,`up`,`waktu_update`,`tanggal_entry`,`apheresis`)
        VALUES
            ('$pkode','$pnoktp','$pnama','$palamat','$pjk','$ppekerjaan',
             '$ptelp1','$ptempatlahir','$ptgllahir','$pstatus','$pgoldarah',
             '$prhesus','1','$pkelurahan','$pkecamatan','$pwilayah','$pjmldonor','-',
             '$ptelp2','$pumur','$ptglkembali','$ptglkembaliapheresis',
             'Admin','','$pcekal','','$sekarang','$sekarang','$papheresis')
        ON DUPLICATE KEY UPDATE
            `NoKTP`                 = '$pnoktp',
            `Nama`                  = '$pnama',
            `Alamat`                = '$palamat',
            `Jk`                    = '$pjk',
            `Pekerjaan`             = '$ppekerjaan',
            `telp`                  = '$ptelp1',
            `TempatLhr`             = '$ptempatlahir',
            `TglLhr`                = '$ptgllahir',
            `Status`                = '$pstatus',
            `GolDarah`              = '$pgoldarah',
            `Rhesus`                = '$prhesus',
            `Call`                  = '1',
            `kelurahan`             = '$pkelurahan',
            `kecamatan`             = '$pkecamatan',
            `wilayah`               = '$pwilayah',
            `jumDonor`              = '$pjmldonor',
            `title`                 = '-',
            `telp2`                 = '$ptelp2',
            `umur`                  = '$pumur',
            `tglkembali`            = '$ptglkembali',
            `tglkembali_apheresis`  = '$ptglkembaliapheresis',
            `pencatat`              = 'Admin',
            `mu`                    = '',
            `cekal`                 = '$pcekal',
            `up`                    = '',
            `waktu_update`          = '$sekarang',
            `tanggal_entry`         = '$sekarang',
            `apheresis`             = '$papheresis'";
        mysqli_query($dbi, $querypendonor);
    }

    // ── 5. INSERT htransaksi ──────────────────────────────────────────────────
    $insertht_rows = 0;
    foreach ($arr_ht as $itemskantong) {
        $htnotrans          = esc($dbi, $itemskantong, 'NoTrans');
        $htkodependonor     = esc($dbi, $itemskantong, 'KodePendonor');
        $htkodependonorlama = esc($dbi, $itemskantong, 'KodePendonor_lama');
        $httgl              = esc($dbi, $itemskantong, 'Tgl');
        $htnoantri          = esc($dbi, $itemskantong, 'NoAntri');
        $htjenisdonor       = esc($dbi, $itemskantong, 'JenisDonor');
        $htdiambil          = esc($dbi, $itemskantong, 'Diambil');
        $htreaksi           = esc($dbi, $itemskantong, 'Reaksi');
        $htpengambilan      = esc($dbi, $itemskantong, 'Pengambilan');
        $htcatatan          = esc($dbi, $itemskantong, 'Catatan');
        $htkodedokter       = esc($dbi, $itemskantong, 'NamaDokter');
        $htoKantong         = esc($dbi, $itemskantong, 'NoKantong');
        $httatus            = esc($dbi, $itemskantong, 'Status');
        $htnopol            = esc($dbi, $itemskantong, 'Nopol');
        $htnoform           = esc($dbi, $itemskantong, 'NoForm');
        $htstatdonor        = esc($dbi, $itemskantong, 'StatDonor');
        $httempat           = esc($dbi, $itemskantong, 'tempat');
        $htuseraftap        = esc($dbi, $itemskantong, 'petugas');
        $htuserregister     = esc($dbi, $itemskantong, 'user');
        $htpaketdonor       = esc($dbi, $itemskantong, 'ketPaket');
        $htketbatal         = esc($dbi, $itemskantong, 'ketBatal');
        $htuserhb           = esc($dbi, $itemskantong, 'petugasHB');
        $htusertensi        = esc($dbi, $itemskantong, 'petugasTensi');
        $htjumhb            = esc($dbi, $itemskantong, 'jumHB');
        $htberatbadan       = esc($dbi, $itemskantong, 'beratBadan');
        $htinstansi         = esc($dbi, $itemskantong, 'Instansi');
        $httahun            = esc($dbi, $itemskantong, 'tahun');
        $httensi            = esc($dbi, $itemskantong, 'tensi');
        $htsuhu             = esc($dbi, $itemskantong, 'suhu');
        $htnadi             = esc($dbi, $itemskantong, 'nadi');
        $hthb               = esc($dbi, $itemskantong, 'Hb');
        $hthct              = esc($dbi, $itemskantong, 'Hct');
        $htjnsperiksa       = esc($dbi, $itemskantong, 'jnsperiksa');
        $htcaraambil        = esc($dbi, $itemskantong, 'caraAmbil');
        $htshift            = esc($dbi, $itemskantong, 'shift');
        $htkota             = esc($dbi, $itemskantong, 'kota');
        $htidpermintaan     = esc($dbi, $itemskantong, 'id_permintaan');
        $htmu               = esc($dbi, $itemskantong, 'mu');
        $htstatustest       = esc($dbi, $itemskantong, 'status_test');
        $htgoldarah         = esc($dbi, $itemskantong, 'gol_darah');
        $htrhesus           = esc($dbi, $itemskantong, 'rhesus');
        $htjeniskantong     = esc($dbi, $itemskantong, 'jeniskantong');
        $htvolumekantong    = esc($dbi, $itemskantong, 'volumekantong');
        $htumur             = esc($dbi, $itemskantong, 'umur');
        $htdonorbaru        = esc($dbi, $itemskantong, 'donorbaru');
        $htpekerjaan        = esc($dbi, $itemskantong, 'pekerjaan');
        $htjk               = esc($dbi, $itemskantong, 'jk');
        $htdonorke          = esc($dbi, $itemskantong, 'donorke');
        $htapheresis        = esc($dbi, $itemskantong, 'apheresis');
        $hthematokrit       = esc($dbi, $itemskantong, 'hematokrit');
        $hthemoglobin       = esc($dbi, $itemskantong, 'hemoglobin');
        $httrombosit        = esc($dbi, $itemskantong, 'trombosit');
        $htleukosit         = esc($dbi, $itemskantong, 'leukosit');
        $htsisadarah        = esc($dbi, $itemskantong, 'sisadarah');
        $htkendaraan        = esc($dbi, $itemskantong, 'kendaraan');
        $htmesinapheresis   = esc($dbi, $itemskantong, 'mesin_apheresis');
        $htrs               = esc($dbi, $itemskantong, 'rs');
        $donor_tpk_ht       = esc($dbi, $itemskantong, 'donor_tpk');

        if ($htnotrans === '') continue;
        $insertht_rows++;

        $q_htrans = "INSERT INTO `htransaksi`
            (`NoTrans`,`KodePendonor`,`KodePendonor_lama`,`Tgl`,`Pengambilan`,`ketBatal`,`tempat`,
             `Instansi`,`JenisDonor`,`id_permintaan`,`Status`,`Nopol`,`apheresis`,`kendaraan`,
             `shift`,`kota`,`umur`,`donorbaru`,`jk`,`gol_darah`,`rhesus`,`pekerjaan`,`donorke`,
             `user`,`jam_mulai`,`rs`,`donor_tpk`,`Diambil`,`NoKantong`,`StatDonor`,`jumHB`,
             `beratBadan`,`tensi`,`suhu`,`nadi`,`Hb`,`Hct`,`jnsperiksa`,`caraAmbil`,
             `petugasHB`,`petugasTensi`)
        VALUES
            ('$htnotrans','$htkodependonor','$htkodependonorlama','$httgl','$htpengambilan',
             '$htketbatal','$httempat','$htinstansi','$htjenisdonor','','$httatus','-',
             '$htapheresis','','$htshift','$htkota','$htumur','$htdonorbaru','$htjk',
             '$htgoldarah','$htrhesus','$htpekerjaan','$htdonorke','admin','$jam_donor','',
             '$donor_tpk_ht','$htdiambil','$htoKantong','$htstatdonor','$htjumhb',
             '$htberatbadan','$httensi','$htsuhu','$htnadi','$hthb','$hthct',
             '$htjnsperiksa','$htcaraambil','$htuserhb','$htusertensi')";
        mysqli_query($dbi, $q_htrans);
    }

    // ── Update status staging → 1 (hilang dari antrian list) ─────────────────
    mysqli_query(
        $dbi,
        "UPDATE `ksl_import_antrian` SET `status`=1 WHERE `notrans`='$notrans_esc'"
    );

    // ── Notifikasi ke dbdonor (opsional, boleh diabaikan jika offline) ─────────
    $curltr = curl_init();
    curl_setopt_array($curltr, array(
        CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_proses_transaksi.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => "POST",
        CURLOPT_POSTFIELDS     => array('mode' => 'terima', 'trans' => $notrans_esc),
    ));
    curl_exec($curltr);
    curl_close($curltr);

    echo json_encode(array(
        "status"  => "success",
        "message" => "Data konsolidasi $notrans_esc berhasil diterima (via Download). "
            . "Detail: $inserted_rows kantong, $insertedkantong_rows stok, "
            . "$insertpendonor_rows pendonor, $insertht_rows transaksi."
    ));
    exit;
}
// ══════════════════════════════════════════════════════════════════════════════
// END HANDLER DOWNLOAD
// ══════════════════════════════════════════════════════════════════════════════


// ══════════════════════════════════════════════════════════════════════════════
// HANDLER SOURCE = ONLINE (tidak ada perubahan)
// ══════════════════════════════════════════════════════════════════════════════

// Cari Serahterima
$curlsr = curl_init();
curl_setopt_array($curlsr, array(
    CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_terima_trans.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => "",
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => "POST",
    CURLOPT_POSTFIELDS     => array('trans' => $g_noserahterima, 'mode' => 'sr'),
));
$responsesr = curl_exec($curlsr);
curl_close($curlsr);
$datasr = json_decode($responsesr, true);

for ($a = 0; $a < count($datasr['data']); $a++) {
    $chkdata = strlen($datasr['data'][$a]['hst_notrans']);
    if ($chkdata > 0) {
        $arr_sr = $datasr['data'][0];
    }
}
if (!$arr_sr) {
    $arr_sr = array();
}

// Curl serahterima detail
$curlsrd = curl_init();
curl_setopt_array($curlsrd, array(
    CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_terima_trans.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => "",
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => "POST",
    CURLOPT_POSTFIELDS     => array('trans' => $g_noserahterima, 'mode' => 'srd'),
));
$responsesrd = curl_exec($curlsrd);
curl_close($curlsrd);
$datasrd = json_decode($responsesrd, true);

for ($a = 0; $a < count($datasrd['data']); $a++) {
    $chkdatasrd = strlen($datasrd['data'][$a]['dst_notrans']);
    if ($chkdatasrd > 0) {
        $arr_srd[] = $datasrd['data'][$a];
    }
}
if (!$arr_srd) {
    $arr_srd = array();
}

// Curl stokkantong
$curlsrk = curl_init();
curl_setopt_array($curlsrk, array(
    CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_terima_trans.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => "",
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => "POST",
    CURLOPT_POSTFIELDS     => array('trans' => $g_noserahterima, 'mode' => 'srk'),
));
$responsesrk = curl_exec($curlsrk);
curl_close($curlsrk);
$datasrk = json_decode($responsesrk, true);

for ($b = 0; $b < count($datasrk['data']); $b++) {
    $chkdatasrk = strlen($datasrk['data'][$b]['noKantong']);
    if ($chkdatasrk > 0) {
        $arr_kantong[] = $datasrk['data'][$b];
    }
}
if (!$arr_kantong) {
    $arr_kantong = array();
}

// Curl pendonor
$curlsrp = curl_init();
curl_setopt_array($curlsrp, array(
    CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_terima_trans.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => "",
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => "POST",
    CURLOPT_POSTFIELDS     => array('trans' => $g_noserahterima, 'mode' => 'srp'),
));
$responsesrp = curl_exec($curlsrp);
curl_close($curlsrp);
$datasrp = json_decode($responsesrp, true);

for ($c = 0; $c < count($datasrp['data']); $c++) {
    $chkdatasrp = strlen($datasrp['data'][$c]['pkode']);
    if ($chkdatasrp > 0) {
        $arr_pendonor[] = $datasrp['data'][$c];
    }
}
if (!$arr_pendonor) {
    $arr_pendonor = array();
}

// Curl htransaksi
$curlsrh = curl_init();
curl_setopt_array($curlsrh, array(
    CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_terima_trans.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => "",
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => "POST",
    CURLOPT_POSTFIELDS     => array('trans' => $g_noserahterima, 'mode' => 'ht'),
));
$responsesrh = curl_exec($curlsrh);
curl_close($curlsrh);
$datasrh = json_decode($responsesrh, true);

for ($d = 0; $d < count($datasrh['data']); $d++) {
    $chkdatasrh = strlen($datasrh['data'][$d]['htnotrans']);
    if ($chkdatasrh > 0) {
        $arr_ht[] = $datasrh['data'][$d];
    }
}
if (!$arr_ht) {
    $arr_ht = array();
}

$arr_kirim = array(
    'serahterima'       => $arr_sr,
    'serahterimadetail' => $arr_srd,
    'stokkantong'       => $arr_kantong,
    'pendonor'          => $arr_pendonor,
    'htransaksi'        => $arr_ht
);

$json_data = json_encode($arr_kirim);
if (!$json_data) {
    echo json_encode(array("status" => "error", "message" => "Gagal mengonversi data ke JSON"));
    exit;
}

$file_path = 'terima_data.json';
file_put_contents($file_path, $json_data);

if ($json_data) {
    $json_datasr = file_get_contents("terima_data.json");
    $split       = explode("\n", $json_datasr);
    $datasr      = json_decode($json_datasr, true);

    $v_hst_id             = $datasr["serahterima"]["hst_id"];
    $v_hst_notrans        = $datasr["serahterima"]["hst_notrans"];
    $v_hst_bagpengirim    = $datasr["serahterima"]["hst_bagpengirim"];
    $v_hst_bagpenerima    = $datasr["serahterima"]["hst_bagpenerima"];
    $v_hst_tgl            = $datasr["serahterima"]["hst_tgl"];
    $v_hst_asal           = $datasr["serahterima"]["hst_asal"];
    $v_hst_jenis_st       = $datasr["serahterima"]["hst_jenis_st"];
    $v_hst_user           = $datasr["serahterima"]["hst_user"];
    $v_hst_pengirim       = $datasr["serahterima"]["hst_pengirim"];
    $v_hst_penerima       = $datasr["serahterima"]["hst_penerima"];
    $v_hst_penerima2      = $datasr["serahterima"]["hst_penerima2"];
    $v_hst_penerima3      = $datasr["serahterima"]["hst_penerima3"];
    $v_hst_kode_alat      = $datasr["serahterima"]["hst_kode_alat"];
    $v_hst_suhuterima     = $datasr["serahterima"]["hst_suhuterima"];
    $v_hst_kondisiumum    = $datasr["serahterima"]["hst_kondisiumum"];
    $v_hst_peruntukan     = $datasr["serahterima"]["hst_peruntukan"];
    $v_hst_modul          = $datasr["serahterima"]["hst_modul"];
    $v_hst_shift_penerima = $datasr["serahterima"]["hst_shift_penerima"];
    $v_hst_termometer     = $datasr["serahterima"]["hst_termometer"];
    $v_hst_dariudd        = $datasr["serahterima"]["hst_dariudd"];
    $v_hst_keudd          = $datasr["serahterima"]["hst_keudd"];

    $query = "INSERT INTO serahterima
    (hst_notrans, hst_bagpengirim, hst_bagpenerima, hst_tgl, hst_asal, hst_jenis_st, hst_user,
    hst_pengirim, hst_penerima, hst_penerima2, hst_penerima3, hst_kode_alat, hst_suhuterima,
    hst_kondisiumum, hst_peruntukan, hst_modul, hst_shift_penerima, hst_termometer, hst_dariudd, hst_keudd)
    VALUES
    ('$v_hst_notrans', '$v_hst_bagpengirim', '$v_hst_bagpenerima', '$v_hst_tgl', '$v_hst_asal', '$v_hst_jenis_st',
    '$v_hst_user', '$v_hst_pengirim', '$v_hst_penerima', '$v_hst_penerima2', '$v_hst_penerima3', '$v_hst_kode_alat',
    '$v_hst_suhuterima', '$v_hst_kondisiumum', '$v_hst_peruntukan','$v_hst_modul', '$shift', '$v_hst_termometer', '$v_hst_dariudd', '$v_hst_keudd')";

    $resultst = mysqli_query($dbi, $query);

    // INSERT DETAIL SERAH TERIMA
    $inserted_rows = 0;
    foreach ($datasr['serahterimadetail'] as $itemskantong) {
        $dst_no_aftap          = $itemskantong["dst_no_aftap"];
        $dst_tglaftap          = $itemskantong["dst_tglaftap"];
        $dst_notrans           = $itemskantong["dst_notrans"];
        $dst_nokantong         = $itemskantong["dst_nokantong"];
        $dst_produk            = $itemskantong["dst_produk"];
        $dst_statusktg         = $itemskantong["dst_statusktg"];
        $st_statusktg_new      = $itemskantong["st_statusktg_new"];
        $dst_old_position      = $itemskantong["dst_old_position"];
        $dst_new_position      = $itemskantong["dst_new_position"];
        $dst_sahktg            = $itemskantong["dst_sahktg"];
        $dst_sahktg_new        = $itemskantong["dst_sahktg_new"];
        $dst_merk              = $itemskantong["dst_merk"];
        $dst_golda             = $itemskantong["dst_golda"];
        $dst_rh                = $itemskantong["dst_rh"];
        $dst_kodedonor         = $itemskantong["dst_kodedonor"];
        $dst_berat             = $itemskantong["dst_berat"];
        $dst_volumektg         = $itemskantong["dst_volumektg"];
        $dst_jenisktg          = $itemskantong["dst_jenisktg"];
        $dst_sample            = $itemskantong["dst_sample"];
        $dst_sah               = $itemskantong["dst_sah"];
        $dst_dsdp              = $itemskantong["dst_dsdp"];
        $dst_lamabaru          = $itemskantong["dst_lamabaru"];
        $dst_umur              = $itemskantong["dst_umur"];
        $dst_lama_aftap        = $itemskantong["dst_lama_aftap"];
        $dst_statuspengambilan = $itemskantong["dst_statuspengambilan"];
        $dst_kel               = $itemskantong["dst_kel"];
        $dst_ptgaftap          = $itemskantong["dst_ptgaftap"];
        $dst_volambil          = $itemskantong["dst_volambil"];
        $dst_receive1          = $itemskantong["dst_receive1"];
        $dst_stat_receive1     = $itemskantong["dst_stat_receive1"];
        $dst_date_receive1     = $itemskantong["dst_date_receive1"];
        $dst_shift_receive1    = $itemskantong["dst_shift_receive1"];
        $dst_receive2          = $itemskantong["dst_receive2"];
        $dst_stat_receive2     = $itemskantong["dst_stat_receive2"];
        $dst_date_receive2     = $itemskantong["dst_date_receive2"];
        $dst_shift_receive2    = $itemskantong["dst_shift_receive2"];
        $dst_receive3          = $itemskantong["dst_receive3"];
        $dst_stat_receive3     = $itemskantong["dst_stat_receive3"];
        $dst_date_receive3     = $itemskantong["dst_date_receive3"];
        $dst_shift_receive3    = $itemskantong["dst_shift_receive3"];
        $simltd                = $itemskantong["simltd"];
        $skgd                  = $itemskantong["skgd"];
        $snat                  = $itemskantong["snat"];
        $packing               = $itemskantong["packing"];
        $label                 = $itemskantong["label"];
        $splasma               = $itemskantong["splasma"];
        $sserum                = $itemskantong["sserum"];
        $swb                   = $itemskantong["swb"];
        $volket                = $itemskantong["volket"];
        $lisis                 = $itemskantong["lisis"];
        $dokumen               = $itemskantong["dokumen"];
        $infoklinis            = $itemskantong["infoklinis"];
        $no_cb                 = $itemskantong["no_cb"];
        $suhu_cb               = $itemskantong["suhu_cb"];
        $up_data               = $itemskantong["up_data"];
        $dst_donasi            = $itemskantong["dst_donasi"];
        $dst_samplevol         = $itemskantong["dst_samplevol"];
        $dst_samplejml         = $itemskantong["dst_samplejml"];
        $dst_tglolah           = $itemskantong["dst_tglolah"];
        $dst_tgled             = $itemskantong["dst_tgled"];
        $dst_tglrelease        = $itemskantong["dst_tglrelease"];
        $dts_hasilrelease      = $itemskantong["dts_hasilrelease"];
        $dst_suhusimpan        = $itemskantong["dst_suhusimpan"];
        $dst_reposisijarum     = $itemskantong["dst_reposisijarum"];
        $dst_caraambil         = $itemskantong["dst_caraambil"];
        $dst_tempataftap       = $itemskantong["dst_tempataftap"];
        $dst_imltd             = $itemskantong["dst_imltd"];
        $dst_kgd               = $itemskantong["dst_kgd"];

        $inserted_rows++;

        $querysrd = "INSERT INTO serahterima_detail
        (dst_no_aftap, dst_tglaftap, dst_notrans, dst_nokantong, dst_produk,
        dst_statusktg, st_statusktg_new, dst_old_position, dst_new_position, dst_sahktg,
        dst_sahktg_new, dst_merk, dst_golda, dst_rh, dst_kodedonor, dst_berat, dst_volumektg,
        dst_jenisktg, dst_sample, dst_sah, dst_dsdp, dst_lamabaru, dst_umur, dst_lama_aftap,
        dst_statuspengambilan, dst_kel, dst_ptgaftap, dst_volambil, dst_receive1, dst_stat_receive1,
        dst_date_receive1, dst_shift_receive1, dst_receive2, dst_stat_receive2, dst_date_receive2,
        dst_shift_receive2, dst_receive3, dst_stat_receive3, dst_date_receive3, dst_shift_receive3,
        simltd, skgd, snat, packing, label, splasma, sserum, swb, volket, lisis, dokumen,
        infoklinis, no_cb, suhu_cb, up_data, dst_donasi, dst_samplevol, dst_samplejml, dst_tglolah,
        dst_tgled, dst_tglrelease, dts_hasilrelease, dst_suhusimpan, dst_reposisijarum,
        dst_caraambil, dst_tempataftap, dst_imltd, dst_kgd)
        VALUES
        ('$dst_no_aftap', '$dst_tglaftap', '$dst_notrans', '$dst_nokantong', '$dst_produk',
        '$dst_statusktg', '$st_statusktg_new', '$dst_old_position', '$dst_new_position', '$dst_sahktg',
        '$dst_sahktg_new', '$dst_merk', '$dst_golda', '$dst_rh', '$dst_kodedonor', '$dst_berat', '$dst_volumektg',
        '$dst_jenisktg', '$dst_sample', '$dst_sah', '$dst_dsdp', '$dst_lamabaru', '$dst_umur', '$dst_lama_aftap',
        '$dst_statuspengambilan', '$dst_kel', '$dst_ptgaftap', '$dst_volambil', '$dst_receive1', '$dst_stat_receive1',
        '$dst_date_receive1', '$dst_shift_receive1', '$dst_receive2', '$dst_stat_receive2', '$dst_date_receive2',
        '$dst_shift_receive2', '$dst_receive3', '$dst_stat_receive3', '$dst_date_receive3', '$dst_shift_receive3',
        '$simltd', '$skgd', '$snat', '$packing', '$label', '$splasma', '$sserum', '$swb', '$volket', '$lisis', '$dokumen',
        '$infoklinis', '$no_cb', '$suhu_cb', '$up_data', '$dst_donasi', '$dst_samplevol', '$dst_samplejml', '$dst_tglolah',
        '$dst_tgled', '$dst_tglrelease', '$dts_hasilrelease', '$dst_suhusimpan', '$dst_reposisijarum',
        '$dst_caraambil', '$dst_tempataftap', '$dst_imltd', '$dst_kgd')";

        $resulstdt = mysqli_query($dbi, $querysrd);
    }

    $insertedkantong_rows = 0;
    foreach ($datasr['stokkantong'] as $itemskantong) {
        $noKantong          = $itemskantong['noKantong'];
        $jenis              = $itemskantong['jenis'];
        $Status             = $itemskantong['Status'];
        $tglTerima          = $itemskantong['tglTerima'];
        $tglEDBuka          = $itemskantong['tglEDBuka'];
        $volume             = $itemskantong['volume'];
        $merk               = $itemskantong['merk'];
        $kantongAsal        = $itemskantong['kantongAsal'];
        $produk             = $itemskantong['produk'];
        $sah                = $itemskantong['sah'];
        $position           = $itemskantong['position'];
        $opname_count       = $itemskantong['opname_count'];
        $opname_lasttime    = $itemskantong['opname_lasttime'];
        $Isi                = $itemskantong['Isi'];
        $gol_darah          = $itemskantong['gol_darah'];
        $RhesusDrh          = $itemskantong['RhesusDrh'];
        $stat2              = $itemskantong['stat2'];
        $StatTempat         = $itemskantong['StatTempat'];
        $kodePendonor       = $itemskantong['kodePendonor'];
        $kodePendonor_lama  = $itemskantong['kodePendonor_lama'];
        $statKonfirmasi     = $itemskantong['statKonfirmasi'];
        $tgl_konfirmasi     = $itemskantong['tgl_konfirmasi'];
        $statQC             = $itemskantong['statQC'];
        $AsalUTD            = $itemskantong['AsalUTD'];
        $tgl_Aftap          = $itemskantong['tgl_Aftap'];
        $kadaluwarsa        = $itemskantong['kadaluwarsa'];
        $pengambilan        = $itemskantong['pengambilan'];
        $tglpengolahan      = $itemskantong['tglpengolahan'];
        $tglperiksa         = $itemskantong['tglperiksa'];
        $metoda             = $itemskantong['metoda'];
        $mu                 = $itemskantong['mu'];
        $stokcheck          = $itemskantong['stokcheck'];
        $ident              = $itemskantong['ident'];
        $volumeasal         = $itemskantong['volumeasal'];
        $tgl_keluar         = $itemskantong['tgl_keluar'];
        $tglmutasi          = $itemskantong['tglmutasi'];
        $hasil              = $itemskantong['hasil'];
        $kadaluwarsa_ktg    = $itemskantong['kadaluwarsa_ktg'];
        $nolot_ktg          = $itemskantong['nolot_ktg'];
        $hasilNAT           = $itemskantong['hasilNAT'];
        $keterangan         = $itemskantong['keterangan'];
        $up_data            = $itemskantong['up_data'];
        $insert_on          = $itemskantong['insert_on'];
        $tgl_release        = $itemskantong['tgl_release'];
        $prolis             = $itemskantong['prolis'];
        $hasil_release      = $itemskantong['hasil_release'];
        $kodebarang         = $itemskantong['kodebarang'];
        $tglbeli            = $itemskantong['tglbeli'];
        $lama_pengambilan   = $itemskantong['lama_pengambilan'];
        $abs                = $itemskantong['abs'];
        $tgl_abs            = $itemskantong['tgl_abs'];
        $donor_tpk          = $itemskantong['donor_tpk'];
        $no_produk_tpk      = $itemskantong['no_produk_tpk'];
        $position_bag       = $itemskantong['position_bag'];
        $user_barcode       = $itemskantong['user_barcode'];
        $user_mutasi        = $itemskantong['user_mutasi'];
        $tgl_nat            = $itemskantong['tgl_nat'];
        $noSelang           = $itemskantong['noSelang'];
        $puf_status         = $itemskantong['puf_status'];

        $insertedkantong_rows++;

        $querykantong = "INSERT INTO `stokkantong`(`noKantong`, `jenis`, `Status`, `tglTerima`, `tglEDBuka`, `volume`, `merk`, `kantongAsal`,
                   `produk`, `sah`, `position`, `opname_count`, `opname_lasttime`, `Isi`, `gol_darah`, `RhesusDrh`, `stat2`, `StatTempat`,
                   `kodePendonor`, `kodePendonor_lama`, `statKonfirmasi`, `tgl_konfirmasi`, `statQC`, `AsalUTD`, `tgl_Aftap`, `kadaluwarsa`,
                   `pengambilan`, `tglpengolahan`, `tglperiksa`, `metoda`, `mu`, `stokcheck`, `ident`, `volumeasal`, `tgl_keluar`, `tglmutasi`,
                   `hasil`, `kadaluwarsa_ktg`, `nolot_ktg`, `hasilNAT`, `keterangan`, `up_data`, `insert_on`, `tgl_release`, `prolis`, `hasil_release`,
                   `kodebarang`, `tglbeli`, `lama_pengambilan`, `abs`, `tgl_abs`, `donor_tpk`, `no_produk_tpk`, `position_bag`, `user_barcode`,
                   `user_mutasi`, `tgl_nat`, `noSelang`, `puf_status`)
                   VALUES (
                   '$noKantong', '$jenis', '$Status', '$tglTerima', '$tglEDBuka', '$volume', '$merk', '$kantongAsal',
                   '$produk', '$sah', '$position', '$opname_count', '$opname_lasttime', '$Isi', '$gol_darah', '$RhesusDrh', '$stat2', '$StatTempat',
                   '$kodePendonor', '$kodePendonor_lama', '$statKonfirmasi', '$tgl_konfirmasi', '$statQC', '$AsalUTD', '$tgl_Aftap', '$kadaluwarsa',
                   '$pengambilan', '$tglpengolahan', '$tglperiksa', '$metoda', '$mu', '$stokcheck', '$ident', '$volumeasal', '$tgl_keluar', '$tglmutasi',
                   '$hasil', '$kadaluwarsa_ktg', '$nolot_ktg', '$hasilNAT', '$keterangan', '$up_data', '$insert_on', '$tgl_release', '$prolis', '$hasil_release',
                   '$kodebarang', '$tglbeli', '$lama_pengambilan', '$abs', '$tgl_abs', '$donor_tpk', '$no_produk_tpk', '$position_bag', '$user_barcode',
                   '$user_mutasi', '$tgl_nat', '$noSelang', '$puf_status')";

        $resultktg = mysqli_query($dbi, $querykantong);
    }

    $insertpendonor_rows = 0;
    foreach ($datasr['pendonor'] as $itemskantong) {
        $pkode                = $itemskantong['pkode'];
        $pkodelama            = $itemskantong['pkodelama'];
        $pnoktp               = $itemskantong['pnoktp'];
        $pnama                = $itemskantong['pnama'];
        $palamat              = $itemskantong['palamat'];
        $pkelurahan           = $itemskantong['pkelurahan'];
        $pkecamatan           = $itemskantong['pkecamatan'];
        $pwilayah             = $itemskantong['pwilayah'];
        $pprovinsi            = $itemskantong['pprovinsi'];
        $pkodepos             = $itemskantong['pkodepos'];
        $ptempatlahir         = $itemskantong['ptempatlahir'];
        $ptgllahir            = $itemskantong['ptgllahir'];
        $pumur                = $itemskantong['pumur'];
        $pgoldarah            = $itemskantong['pgoldarah'];
        $prhesus              = $itemskantong['prhesus'];
        $pketdarah            = $itemskantong['pketdarah'];
        $pjk                  = $itemskantong['pjk'];
        $pstatus              = $itemskantong['pstatus'];
        $psukubangsa          = $itemskantong['psukubangsa'];
        $ppekerjaan           = $itemskantong['ppekerjaan'];
        $ptelp1               = $itemskantong['ptelp1'];
        $ptelp2               = $itemskantong['ptelp2'];
        $pibukandung          = $itemskantong['pibukandung'];
        $pjmldonor            = $itemskantong['pjmldonor'];
        $pcall                = $itemskantong['pcall'];
        $papheresis           = $itemskantong['papheresis'];
        $pcekal               = $itemskantong['pcekal'];
        $pjns                 = $itemskantong['pjns'];
        $ptglkembali          = $itemskantong['ptglkembali'];
        $ptglkembaliapheresis = $itemskantong['ptglkembaliapheresis'];
        $pmu                  = $itemskantong['pmu'];
        $p10                  = $itemskantong['p10'];
        $p25                  = $itemskantong['p25'];
        $p50                  = $itemskantong['p50'];
        $p75                  = $itemskantong['p75'];
        $p100                 = $itemskantong['p100'];
        $psatya               = $itemskantong['psatya'];
        $pprov                = $itemskantong['pprov'];
        $pinstansi            = $itemskantong['pinstansi'];
        $ppencatat            = $itemskantong['ppencatat'];
        $on_insert            = $itemskantong['on_insert'];
        $on_update            = $itemskantong['on_update'];
        $userfoto             = $itemskantong['userfoto'];
        $token                = $itemskantong['token'];
        $donor_tpk            = $itemskantong['donor_tpk'];
        $verif_tpk            = $itemskantong['verif_tpk'];

        $insertpendonor_rows++;

        $querypendonor = "insert into pendonor
        (`Kode`,`NoKTP`,`Nama`,`Alamat`,`Jk`,`Pekerjaan`,
        `telp`,`TempatLhr`,`TglLhr`,`Status`,`GolDarah`,
        `Rhesus`,`Call`,`kelurahan`,`kecamatan`,`wilayah`,`jumDonor`,`title`,
        `telp2`,`umur`,`tglkembali`,`tglkembali_apheresis`,
        `pencatat`,`mu`,`cekal`,`up`,`waktu_update`,`tanggal_entry`,`apheresis`)
        values ('$pkode','$pnoktp','$pnama','$palamat','$pjk','$ppekerjaan',
        '$ptelp2','$ptempatlahir','$ptgllahir','$pstatus','$pgoldarah',
        '$prhesus','1','$pkelurahan','$pkecamatan','$pwilayah','$pjmldonor','-',
        '$ptelp2','$pumur','$ptglkembali','$ptglkembaliapheresis',
        'Admin','','$pcekal','','$sekarang','$sekarang','$papheresis')
         ON DUPLICATE KEY UPDATE
            `NoKTP`                 = '$pnoktp',
            `Nama`                  = '$pnama',
            `Alamat`                = '$palamat',
            `Jk`                    = '$pjk',
            `Pekerjaan`             = '$ppekerjaan',
            `telp`                  = '$ptelp2',
            `TempatLhr`             = '$ptempatlahir',
            `TglLhr`                = '$ptgllahir',
            `Status`                = '$pstatus',
            `GolDarah`              = '$pgoldarah',
            `Rhesus`                = '$prhesus',
            `Call`                  = '1',
            `kelurahan`             = '$pkelurahan',
            `kecamatan`             = '$pkecamatan',
            `wilayah`               = '$pwilayah',
            `jumDonor`              = '$pjmldonor',
            `title`                 = '-',
            `telp2`                 = '$ptelp2',
            `umur`                  = '$pumur',
            `tglkembali`            = '$ptglkembali',
            `tglkembali_apheresis`  = '$ptglkembaliapheresis',
            `pencatat`              = 'Admin',
            `mu`                    = '',
            `cekal`                 = '$pcekal',
            `up`                    = '',
            `waktu_update`          = '$sekarang',
            `tanggal_entry`         = '$sekarang',
            `apheresis`             = '$papheresis'";

        $resultpendonor = mysqli_query($dbi, $querypendonor);
    }

    $insertht_rows = 0;
    foreach ($datasr['htransaksi'] as $itemskantong) {
        $htutd              = $itemskantong['htutd'];
        $htnotrans          = $itemskantong['htnotrans'];
        $htkodependonorlama = $itemskantong['htkodependonorlama'];
        $htkodependonor     = $itemskantong['htkodependonor'];
        $httgl              = $itemskantong['httgl'];
        $htnoantri          = $itemskantong['htnoantri'];
        $htjenisdonor       = $itemskantong['htjenisdonor'];
        $htdiambil          = $itemskantong['htdiambil'];
        $htreaksi           = $itemskantong['htreaksi'];
        $htpengambilan      = $itemskantong['htpengambilan'];
        $htcatatan          = $itemskantong['htcatatan'];
        $htkodedokter       = $itemskantong['htkodedokter'];
        $htoKantong         = $itemskantong['htoKantong'];
        $httatus            = $itemskantong['httatus'];
        $htnopol            = $itemskantong['htnopol'];
        $htnoform           = $itemskantong['htnoform'];
        $htstatdonor        = $itemskantong['htstatdonor'];
        $httempat           = $itemskantong['httempat'];
        $htuseraftap        = $itemskantong['htuseraftap'];
        $htuserregister     = $itemskantong['htuserregister'];
        $htpaketdonor       = $itemskantong['htpaketdonor'];
        $htketbatal         = $itemskantong['htketbatal'];
        $htuserhb           = $itemskantong['htuserhb'];
        $htusertensi        = $itemskantong['htusertensi'];
        $htjumhb            = $itemskantong['htjumhb'];
        $htberatbadan       = $itemskantong['htberatbadan'];
        $htinstansi         = $itemskantong['htinstansi'];
        $httahun            = $itemskantong['httahun'];
        $httensi            = $itemskantong['httensi'];
        $htsuhu             = $itemskantong['htsuhu'];
        $htnadi             = $itemskantong['htnadi'];
        $hthb               = $itemskantong['hthb'];
        $hthct              = $itemskantong['hthct'];
        $htjnsperiksa       = $itemskantong['htjnsperiksa'];
        $htcaraambil        = $itemskantong['htcaraambil'];
        $htshift            = $itemskantong['htshift'];
        $htkota             = $itemskantong['htkota'];
        $htidpermintaan     = $itemskantong['htidpermintaan'];
        $htmu               = $itemskantong['htmu'];
        $htstatustest       = $itemskantong['htstatustest'];
        $htgoldarah         = $itemskantong['htgoldarah'];
        $htrhesus           = $itemskantong['htrhesus'];
        $htjeniskantong     = $itemskantong['htjeniskantong'];
        $htvolumekantong    = $itemskantong['htvolumekantong'];
        $htumur             = $itemskantong['htumur'];
        $htdonorbaru        = $itemskantong['htdonorbaru'];
        $htpekerjaan        = $itemskantong['htpekerjaan'];
        $htjk               = $itemskantong['htjk'];
        $htdonorke          = $itemskantong['htdonorke'];
        $htapheresis        = $itemskantong['htapheresis'];
        $hthematokrit       = $itemskantong['hthematokrit'];
        $hthemoglobin       = $itemskantong['hthemoglobin'];
        $httrombosit        = $itemskantong['httrombosit'];
        $htleukosit         = $itemskantong['htleukosit'];
        $htsisadarah        = $itemskantong['htsisadarah'];
        $htkendaraan        = $itemskantong['htkendaraan'];
        $htmesinapheresis   = $itemskantong['htmesinapheresis'];
        $htlamaaftap        = $itemskantong['htlamaaftap'];
        $htrs               = $itemskantong['htrs'];
        $on_insert          = $itemskantong['on_insert'];
        $on_update          = $itemskantong['on_update'];
        $httiter_abs        = $itemskantong['httiter_abs'];

        $insertht_rows++;

        $q_htrans = "insert into htransaksi
        (`NoTrans`,`KodePendonor`,`KodePendonor_lama`,`Tgl`,`Pengambilan`,`ketBatal`,`tempat`,`Instansi`, `JenisDonor`,
        `id_permintaan`,`Status`,`Nopol`,`apheresis`,`kendaraan`,`shift`,`kota`,`umur`,`donorbaru`,`jk`, `gol_darah`,`rhesus`,
        `pekerjaan`,`donorke`,`user`,`jam_mulai`,`rs`, `donor_tpk`, `Diambil`,`NoKantong`,`StatDonor`,`jumHB`,`beratBadan`,`tensi`,
        `suhu`,`nadi`,`Hb`,`Hct`,`jnsperiksa`,`caraAmbil`,`petugasHB`,`petugasTensi`)
        values
        ('$htnotrans','$htkodependonor','$htkodependonor','$httgl','$htpengambilan','$htketbatal','$httempat','$htinstansi',
        '$htjenisdonor','','$httatus','-','$htapheresis','','$htshift','$htkota','$htumur','$htdonorbaru','$htjk','$htgoldarah','$htrhesus',
        '$htpekerjaan','$htdonorke','admin','$jam_donor','','$tpk','$htdiambil','$htoKantong','$htstatdonor','$htjumhb','$htberatbadan',
        '$httensi','$htsuhu','$htnadi','$hthb','$hthct','$htjnsperiksa','$htcaraambil','$htuserhb','$htusertensi')";

        $resultht = mysqli_query($dbi, $q_htrans);
    }

    if ($resultst) {
        $curltr = curl_init();
        curl_setopt_array($curltr, array(
            CURLOPT_URL            => "https://dbdonor.pmi.or.id/konsolidasi/get_proses_transaksi.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => array('mode' => 'terima', 'trans' => $v_hst_notrans),
        ));
        $responsetr = curl_exec($curltr);
        curl_close($curltr);

        echo json_encode(array("status" => "success", "message" => "Data konsolidasi sudah diterima"));
        exit;
    } else {
        echo json_encode(array("error" => "success", "message" => "Data konsolidasi gagal diterima"));
        exit;
    }
}
