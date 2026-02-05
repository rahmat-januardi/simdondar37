<?php
require_once('adm/config.php');


if (@$_GET['page'] == '' || @$_GET['page'] == 'index') {
    include "view/index.php";
} else  if (@$_GET['page'] == 'dash') {
    include "view/dashboard.php";
} else  if (@$_GET['page'] == 'caridonor') {
    include "view/caridonormu.php";
} else  if (@$_GET['page'] == 'histori') {
    include "view/sejarah_donor.php";
} else  if (@$_GET['page'] == 'logout') {
    include "view/logout.php";
} else  if (@$_GET['page'] == 'edits') {
    include "view/editdonorlokal.php";
} else  if (@$_GET['page'] == 'editn') {
    include "view/editdonornas.php";
} else  if (@$_GET['page'] == 'sinkron') {
    include "view/sinkronnasional.php";
} else  if (@$_GET['page'] == 'donorbaru') {
    include "view/registrasi.php";
} else  if (@$_GET['page'] == 'searchmcu') {
    include "view/searchmcu.php";
} else  if (@$_GET['page'] == 'searchaftap') {
    include "view/searchaftap.php";
} else  if (@$_GET['page'] == 'aftap') {
    include "view/pengambilan.php";
} else  if (@$_GET['page'] == 'mcu') {
    include "view/medicalcu.php";
} else  if (@$_GET['page'] == 'gantikantong') {
    include "view/gantikantong.php";
} else  if (@$_GET['page'] == 'rekap') {
    include "view/search_rekap.php";
} else  if (@$_GET['page'] == 'check') {
    include "view/cek_kantong.php";
} else  if (@$_GET['page'] == 'rekap_validktg') {
    include "view/rekap_verifikasi_kantong.php";
}
