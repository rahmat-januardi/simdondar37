<?php
session_start();
if (empty($_SESSION['namauser']) and empty($_SESSION['passuser'])) {
    echo "<link rel='stylesheet' href='bootsrap337/css/bootstrap.min.css'>
        <link rel='stylesheet' href='bootsrap337/w3.css'>
        <link href='config/adminstyle.css' rel='stylesheet' type='text/css'>
        <div class='row'>
            <div class='col-sm-12 text-center'>
                <h4>Untuk mengakses modul, Anda harus login</h4>
                <a href=index.php target=\"_top\" class='w3-btn w3-theme w3-hover-red'><b>LOGIN</b></a></center>
            </div>
        </div>";
}
if ((@$_SESSION['leveluser']) == 'fraksionasi') {
    include "config/library.php";
    $tgll = date("Ymd");
    if ($_GET['module'] == 'home') {
        echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src=images/donate.jpg>";
    } elseif ($_GET['module'] == 'ganti_menu') {
        include  "ganti_menu.php";
    }
    // Cek Kantong
    elseif ($_GET['module'] == 'cekkantong') {
        include  "penyimpanan/cek_kantong.php";
    } elseif ($_GET['module'] == 'cekstok') {
        include  "penyimpanan/cek_stok.php";
    } elseif ($_GET['module'] == 'stokuddlain') {
        include  "penyimpanan/info_stok_uddlain.php";
    }

    // Plasma Master File for SIMDONDAR Versi 3.7
    elseif ($_GET['module'] == 'admin_pmf') {
        include "admin_pmf.php";
    } elseif ($_GET['module'] == 'pmf_uddmastert') {
        include "pmf/pmf_masterudd.php";
    } elseif ($_GET['module'] == 'pmf_uddmaster') {
        include "pmf/pmf_masteruddf.php";
    } elseif ($_GET['module'] == 'pmf_inspeksi') {
        include "pmf/pmf_inspeksi.php";
    } elseif ($_GET['module'] == 'pmf_iaudit') {
        include "pmf/pmf_audit_internal.php";
    } elseif ($_GET['module'] == 'pmf_reagen') {
        include "pmf/pmf_masterreagen.php";
    } elseif ($_GET['module'] == 'pmf_acd') {
        include "pmf/pmf_masterantikuagulasi.php";
    } elseif ($_GET['module'] == 'pmf_kantong') {
        include "pmf/pmf_masterkantong.php";
    } elseif ($_GET['module'] == 'pmf_lookback') {
        include "pmf/lookback.php";
    } elseif ($_GET['module'] == 'pmf_dokumen') {
        include "pmf/dokumen_mutu.php";
    } elseif ($_GET['module'] == 'pmf_audittrail') {
        include "pmf/pmf_audittrail.php";
    } elseif ($_GET['module'] == 'pmf_audittrailss') {
        include "pmf/pmf_audittrail_serverside.php";
    } elseif ($_GET['module'] == 'pmf_datapenolakan') {
        include "pmf/pmf_datapenolakandonor.php";
    } elseif ($_GET['module'] == 'pmf_imltdrr') {
        include "pmf/pmf_hasilreaktifimltd.php";
    } elseif ($_GET['module'] == 'pmf_prevalensi') {
        include "pmf/pmf_prevalensiimltd.php";
    } elseif ($_GET['module'] == 'pmf_prevalensijenisdonor') {
        include "pmf/pmf_prevalensiimltdjenisdonor.php";
    } elseif ($_GET['module'] == 'pmf_insidensi') {
        include "pmf/pmf_insidensiimltd.php";
    } elseif ($_GET['module'] == 'pmf_upload') {
        include "pmf/pmf_uploaddata.php";
    }

    // Barcode PMF Version
    elseif ($_GET['module'] == 'pmf_barcode_auto') {
        include "barcodekantong/barcodeauto_menu.php";
    } elseif ($_GET['module'] == 'barcode_automake') {
        include "barcodekantong/barcodekantong_auto.php";
    } elseif ($_GET['module'] == 'barcode_autorm') {
        include "barcodekantong/barcodekantong_rekap.php";
    } elseif ($_GET['module'] == 'barcode_autor') {
        include "barcodekantong/barcodekantong_rekap.php";
    } elseif ($_GET['module'] == 'barcode_mutasi') {
        include "barcodekantong/barcode_mutasikankantong.php";
    } elseif ($_GET['module'] == 'barcode_mutasirekap') {
        include "barcodekantong/barcode_rekapmutasi.php";
    } elseif ($_GET['module'] == 'barcode_rekappakai') {
        include "barcodekantong/barcode_pemakaiankantong.php";
    } elseif ($_GET['module'] == 'barcode__mutasiforprint') {
        include "barcodekantong/barcode_mutasicetak.php";
    } elseif ($_GET['module'] == 'barcode_reprint') {
        include "barcodekantong/barcode_reprintlabel.php";
    } elseif ($_GET['module'] == 'barcode_noselang') {
        include "barcodekantong/barcodekantong_noselang.php";
    }
    // PMF Label Release
    elseif ($_GET['module'] == 'pmf_labelrelease') {
        include  "barcodekantong/qa_release_label.php";
    }

    // Distribusi Produk 2023-02-21
    elseif ($_GET['module'] == 'fp_transport') {
        include  "puf/trans_menu.php";
    } elseif ($_GET['module'] == 'fp_input_drop') {
        include  "puf/trans_drop_input.php";
    } elseif ($_GET['module'] == 'fp_drop_data') {
        include  "puf/trans_drop_data.php";
    } elseif ($_GET['module'] == 'fp_drop_cek') {
        include  "puf/trans_cekkantong.php";
    } elseif ($_GET['module'] == 'fp_input_receive') {
        include  "puf/trans_receive_input.php";
    } elseif ($_GET['module'] == 'fp_data_drop') {
        include  "puf/trans_.php";
    } elseif ($_GET['module'] == 'fp_data_receive') {
        include  "puf/trans_drop_receive.php";
    } elseif ($_GET['module'] == 'fp_drop_donorlist') {
        include  "puf/trans_drop_donorlist.php";
    } elseif ($_GET['module'] == 'fp_packing_slip') {
        include  "puf/puf_packing_slip.php";
    } elseif ($_GET['module'] == 'fp_coq_plasma') {
        include  "puf/puf_coq_plasma.php";
    }

    // Stok PuF
    elseif ($_GET['module'] == 'puf_stokkemu') {
        include "puf/puf_stok_menu.php";
    } elseif ($_GET['module'] == 'puf_inputstok') {
        include "puf/puf_stok_input.php";
    } elseif ($_GET['module'] == 'puf_rekapinput') {
        include "puf/puf_stok_rekapinput.php";
    } elseif ($_GET['module'] == 'puf_stok') {
        include "puf/puf_stok_data.php";
    } elseif ($_GET['module'] == 'puf_dashboard') {
        include "puf/puf_dashboard.php";
    } elseif ($_GET['module'] == 'puf_stokxls') {
        include "puf/puf_stok_dataxls.php";
    }
}
