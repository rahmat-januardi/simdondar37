<script src="js/registrasi_qc1.js?v=<?= time() ?>"></script>
<script language=javascript src="js/jquery-latest.js" type="text/javascript"> </script>
<script language=javascript src="js/alert.js" type="text/javascript"> </script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>

<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<style type="text/css">
.alert {
    font: 62.5% "Trebuchet MS", sans-serif;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
    $('#instansi').autocomplete({
        source: 'modul/suggest_bdrs.php',
        minLength: 2
    });
});
</script>

<?
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

$namauser = $_SESSION[nama_lengkap];
$tglSerahTerima = $_POST['tglSerahTerima'];
$v_petugas2 = $_POST['petugas2'];
//------------------------------ Submit ----------------------------//
if (isset($_POST[submit])) {
    $nk1 = '';
    $rs = $_POST[rmhskt];
    $pdbdrs = '';
    $myfile = "bdrs/$rs-$today.zip";
    if (file_exists($myfile)) {
        $fh = fopen($myfile, 'a') or die("Cant open file");
    } else {
        $fh = fopen($myfile, 'w') or die("Cant open file");
    }
    for ($i = 0; $i < count($_POST[nk]); $i++) {
        $nkantong = $_POST[nk][$i];
        $suhu      = $_POST['suhu_terima'][$i];
        $catatan   = $_POST['catatan'][$i];
        $tambah = mysql_query("update stokkantong set statQC='0' where noKantong='$nkantong'");
        $pd = mysql_fetch_assoc(mysql_query("select * from stokkantong where noKantong='$nkantong'"));
        $qbdrs = mysql_fetch_assoc(mysql_query("select nama from bdrs where kode='$rs'"));
        //=======Audit Trial====================================================================================
        $log_mdl = 'KOMPONEN';
        $log_aksi = 'Kirim ke: ' . $qbdrs[nama] . ' Kantong: ' . $nkantong . ' - ' . $pd[produk];
        include("user_log.php");



        //------------insert ke tabel registrasi----------------//
        $bdrs_sql = mysql_query("insert into registrasi_qc (nokantong,produk,goldarah,rhesus,tgl,tglaftap,kadaluwarsa,tgl_pengolahan,petugas_terima,petugas_serah,asal_utd, suhu, catatan) values ('$pd[noKantong]','$pd[produk]','$pd[gol_darah]','$pd[RhesusDrh]','$tglSerahTerima','$pd[tgl_Aftap]','$pd[kadaluwarsa]','$pd[tglpengolahan]','$namauser','$v_petugas2','$udd[id]', '$suhu', '$catatan')");


        $histori = mysql_query("insert into histori (`username`,`waktu`,`action`,`level_editor`,`nokantong`) values ('$namauser','$today3','Registrasi QC produk komponen darah','QC','$nkantong')");
        $pd_sql = base64_encode($pd_sql . ';');

        $nk1 = $nk1 . ";" . $nkantong;
        fwrite($fh, $pd_sql);
        fwrite($fh, "\n");
    }
    fclose($fh);
    //-----------------------------------
    if ($tambah) {
        echo "Data Telah berhasil dimasukkan $rs<br>";
        backgroundPost('http://localhost/simudda/modul/background_up.php');
?>

<?
    }
}
//------------------------------ END Submit ----------------------------//
?>

<body onload="document.getElementById('nokantong').focus();">

    <h1 class="table">Penerimaan Sample QC Produk Komponen Darah</h1>
    <br>
    <form name="bdrs" id="bdrs" onsubmit="return ok()" method="POST" action="<?= $PHPSELF ?>">
        <table>

            <tr>
                <td>Tanggal :</td>
                <td><input type=text name=tglSerahTerima id=datepicker size=10 value=<?= $today ?>></td>
            </tr>
            <tr>
                <td>No Kantong :</td>
                <td>
                    <INPUT type="text" name="nokantong" id="nokantong" onkeydown="chang(event,this);" autofocus
                        onchange="addRow('dtable')" />
                </td>
            </tr>
        </table>
        <!--INPUT name="nokantong" type="text" id="nokantong" onkeydown="chang(event,this);" onchange="addRow('box-table-b0');focus(document.periksa.nokantong);"/></td></tr></table-->


        <table id="dtable" border=1 cellpadding=4 style="border-collapse:collapse">
            <thead>
                <tr class="field" style="background-color:mistyrose; font-size:12px; color:#000000;">
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
                <!-- row data akan disisipkan di sini -->
            </tbody>
        </table>

        <!-- <input type="button" value="Delete Row" onclick="deleteRow('dtable')"> -->
        <table>
            <tr>
                <td style="background-color: mistyrose" colspan="2">Petugas Yang Menyerahkan</td>
                <td>

                    <select id="petugas2" name="petugas2" class="styled-select">
                        <?
                        $user1 = "select * from user where level like '%komponen%' order by nama_lengkap ASC";
                        $do1 = mysql_query($user1);
                        while ($data1 = mysql_fetch_assoc($do1)) {
                            if ($data1[id_user] == $data_combo[petugas1]) {
                                $select = " selected";
                            } else {
                                $select = "";
                            } ?>
                        <option value="<?= $data1[nama_lengkap] ?>" <?= $select ?>><?= $data1[nama_lengkap] ?></option>
                        <?
                        } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td style="background-color: mistyrose" colspan="2">Petugas Yang Menerima</td>
                <td>
                    <? echo $namauser; ?>
                </td>
            </tr>
        </table>
        <br>
        <input type="submit" value="Submit" name="submit" class="swn_button_blue">
        <INPUT type="button" value="Delete Row" onclick="deleteRow('box-table-b0')" class="swn_button_red" />
        <a href="pmiqc.php?module=register_qc_luar" class="swn_button_green">Terima Sampel Dari UTD Lain</a></td>
    </form>



    <div class="alert" id="alert">
        <div id="pilih_tes" title="Pilih jenis tes..!">
            <p>Silahkan pilih jenis tes</p>
        </div>
        <div id="ganti_reagen" title="Waktu Ganti Reagen..!">
            <p>Silahkan isikan hasil test dan submit terlebih dahulu. Ganti reagen yang telah habis</p>
        </div>
        <div id="kantong_tdk_sesuai" title="Kantong tidak sesuai..!">
            <p>Silahkan cek kembali kantong yang anda masukkan, atau masukkan kantong lain</p>
        </div>
        <div id="pilih_reagen" title="Pilih reagen..!">
            <p>Silahkan pilih reagen terlebih dahulu sebelum memasukkan nomor kantong</p>
        </div>
        <div id="kantong_sudah_diinput" title="Kantong sudah diinput..!">
            <p>Silahkan masukkan kantong yang lain</p>
        </div>
        <div id="kantong_reaktif" title="Kantong sudah ditest...!">
            <p>Silahkan masukkan kantong yang lain</p>
        </div>
    </div>
</body>