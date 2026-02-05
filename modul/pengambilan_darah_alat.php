<?php
// Sambungkan ke database
require('config/db_connect.php');

// Pastikan parameter 'NoTrans' ada sebelum digunakan
$notrans = isset($_GET['NoTrans']) ? $_GET['NoTrans'] : null;
$namauser   = $_SESSION['namauser'];

$data_combo = mysql_fetch_array(mysql_query("select * from tempudd where modul='MU CHECKUP'"));
$check      = mysql_query("select * from htransaksi where (NoTrans='$_GET[NoTrans]' or NoTrans='$_POST[NoTrans]')");
$check1     = mysql_fetch_assoc($check);
$q_dok      = mysql_query("select Nama from dokter_periksa where kode='$check1[NamaDokter]'");
$a_dok      = mysql_fetch_assoc($q_dok);
$check1[KodePendonor]=str_replace("'","\'",$check1[KodePendonor]);
$data       = mysql_query("select Nama,GolDarah,Rhesus from pendonor where Kode='$check1[KodePendonor]'");
$data1      = mysql_fetch_array($data);

if ($check1[jumHB]==0) $jnscuso4='-';
if ($check1[jumHB]==1) $jnscuso4='Tenggelam';

// Jika formulir disubmit
if (isset($_POST['submit']) && $_POST['submit'] != "") {
    $noKantong = $_POST['cekKantong'];

    // Jalankan query untuk memeriksa nomor kantong
    $qCekKantong = mysql_query("SELECT * FROM `timbang_aftap` WHERE `no_kantong`='$noKantong'");

    // Hitung jumlah hasil
    $qhasil = mysql_num_rows($qCekKantong);

    // Jika ditemukan, ambil data
    if ($qhasil == 1) {
        $dataKantong = mysql_fetch_array($qCekKantong);
    }
}

// Jika formulir disimpan
if (isset($_POST['simpan']) && $_POST['simpan'] != "") {
    $kdl            = mktime(0,0,0,date("m"),date("d")+14,date("Y"));
    $kadaluwarsa    = date('Y-m-d H:i:s',$kdl);
    $kembali0       = mktime(0,0,0,date("m"),date("d")+60,date("Y"));
    $kembali        = date('Y-m-d',$kembali0);
    $idp1           = mysql_fetch_assoc(mysql_query("select * from tempat_donor where active='1'"));
    $today          = date('Y-m-d H:i:s');
    $today1         = date("Y-m-d");
    $kodependonor   = $check1[KodePendonor];
    $golDarah       = $data1[GolDarah];
    $rhesus         = $data1[Rhesus];
    $keberhasilan   = $_POST['keberhasilan'];
    $volume_darah   = $_POST['volume_darah'];
    $catatan        = $_POST['catatan'];
    $reaksi         = $_POST['reaksi'];
    $caraambil      = $_POST['caraambil'];
    $noKantong      = $_POST['no_Kantong'];
    $noSelang       = $_POST['no_selang'];
    $ambil          = $_POST['jam_ambil'];
    $selesai        = $_POST['jam_selesai'];
    $petugas        = $_POST['petugas'];
    $tglAftap       = $_POST['tgl_aftap'];

    if (substr($idp1[id1],0,1)=="M") { 
        $mu="1";
    } else {
        $mu="";
    }

    // Perhitungan untuk mengambil menit dari selisih waktu ambil dan selesai

    $date_awal  = new DateTime($ambil);
    $date_akhir = new DateTime($selesai);
    $selisih = $date_akhir->diff($date_awal);

    $jam = $selisih->format('%h');
    $menit = $selisih->format('%i');
 
    if($menit >= 0 && $menit <= 9){
     $menit = "0".$menit;
    }
 
    $hasil = $menit;
    $hasil1 = number_format($hasil,2);
    
    $lama_pengambilan=$hasil;

    // Pengambil data Stok dan Pendonor
    $stok=mysql_query("select * from stokkantong where NoKantong='$noKantong' and Status='0' and StatTempat='1' and kadaluwarsa_ktg >'$today1' and ident='m'");
    $stok1=mysql_fetch_array($stok);
    $pendonor=mysql_query("select * from pendonor where Kode='$kodependonor' ");
    $pendonor1=mysql_fetch_assoc($pendonor);
    $kota=mysql_fetch_assoc(mysql_query("select * from utd where aktif='1'"));

    if ($stok1['Status']=="0"){
        if ($keberhasilan==0) {     //0=berhasil, 1=batal, 2=gagal 
            $tambah=mysql_query("UPDATE htransaksi
                    SET diambil='$volume_darah',no_selang='$noSelang',reaksi='$reaksi',Tgl='$tglAftap',
                        pengambilan='$keberhasilan',catatan='$catatan',ketBatal='-',jeniskantong='$stok1[jenis]',volumekantong='$stok1[volumeasal]',
                        nokantong='$noKantong',petugas='$petugas',
                        caraambil='$caraambil',status_test='2',Status='2',mu='$mu',gol_darah='$pendonor1[GolDarah]',jam_ambil='$ambil', jam_selesai='$selesai',rhesus='$pendonor1[Rhesus]',jk='$pendonor1[Jk]',pekerjaan='$pendonor1[Pekerjaan]',umur='$pendonor1[umur]',donorke='$pendonor1[jumDonor]+1' WHERE (Status='1' and NoTrans='$notrans')");

            $queriAlatAftap = mysql_query("UPDATE `timbang_aftap` SET `up_to_aftap`='1' WHERE `no_kantong`='$noKantong'");

            if($caraambil!=5){
                $kembali1=mysql_query("UPDATE pendonor
                        SET tglkembali='$kembali',jumDonor=jumDonor+1,mu='$mu',up=b'1',tglkembali_apheresis='$kembali'
                        WHERE Kode='$kodependonor'");

                $ono_kantong0=substr($noKantong,0,-1);

                $tambah2=mysql_query("UPDATE stokkantong
                    SET Status='1',no_selang='$noSelang', tgl_Aftap='$tglAftap',gol_darah='$golDarah',RhesusDrh='$rhesus',produk='WB',sah='0',
                    kodePendonor='$kodependonor',statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 34 day),mu='$mu',
                    lama_pengambilan='$lama_pengambilan' WHERE noKantong='$noKantong'");

                $tambah3=mysql_query("UPDATE htransaksi set donorke=donorke+1 where NoTrans='$notrans' ");

                $tambah4=mysql_query("UPDATE htransaksi set donorbaru='1' where NoTrans='$notrans' and donorke >'1' ");

                $tambah5=mysql_query("UPDATE stokkantong set lama_pengambilan='$lama_pengambilan', tgl_Aftap='$tglAftap',no_selang='$noSelang' WHERE noKantong like '$ono_kantong0%'");

                $histori=mysql_query("insert into histori (`username`,`waktu`,`action`,`level_editor`,`nokantong`,`up`) values ('$namauser','$today','Penyadapan Darah Pendonor dengan no. kantong $noKantong dengan durasi pengambilan $lama_pengambilan menit, jam aftap $ambil dan jam selesai aftap $selesai, petugas aftap $petugas','Aftap','$noKantong','1')");

                switch ($golDarah){
                    case 'A':
                    $update_wba=mysql_query("UPDATE stok SET wb_a = wb_a + 1 where status ='0'");
                    break;
                    case 'B':
                    $update_wbb=mysql_query("UPDATE stok SET wb_b = wb_b + 1 where status ='0'");
                    break;
                    case 'AB':
                    $update_wbab=mysql_query("UPDATE stok SET wb_ab = wb_ab + 1 where status ='0'");
                    break;
                    case 'O':
                    $update_wbo=mysql_query("UPDATE stok SET wb_o = wb_o + 1 where status ='0'");
                    break;
                    default:
                    $update_wbx=mysql_query("UPDATE stok SET wb_x = wb_x + 1 where status ='0'");
                }

            } else { //plebotomi
                $tambah2=mysql_query("UPDATE stokkantong
                    SET Status='6',no_selang='$noSelang',tgl_Aftap='$tglAftap',gol_darah='$golDarah',RhesusDrh='$rhesus',produk='WB',sah='0', kodePendonor='$kodependonor', statKonfirmasi='0',kadaluwarsa=(tgl_aftap + interval 34 day),mu='$mu',
                    lama_pengambilan='$lama_pengambilan' WHERE noKantong='$noKantong'");

                $histori=mysql_query("insert into histori (`username`,`waktu`,`action`,`level_editor`,`nokantong`,`up`) values ('$namauser','$today','Penyadapan Darah Pendonor dengan no. kantong $noKantong dengan durasi pengambilan $lama_pengambilan menit, jam aftap $ambil dan jam selesai aftap $selesai, petugas aftap $petugas','Aftap','$noKantong','1')");
            }
        }  elseif ($keberhasilan==2){ 
            $tambah=mysql_query("UPDATE htransaksi
                    SET diambil='$volume_darah',no_selang='$noSelang',reaksi='$reaksi', pengambilan='$keberhasilan',catatan='$catatan',ketBatal='12',jeniskantong='$stok1[jenis]',volumekantong='$volume_darah', Tgl='$tglaftap',nokantong='$noKantong', petugas='$petugas', caraambil='$caraambil',status_test='2',Status='2',mu='$mu',gol_darah='$pendonor1[GolDarah]',rhesus='$pendonor1[Rhesus]',jk='$pendonor1[Jk]',pekerjaan='$pendonor1[Pekerjaan]',umur='$pendonor1[umur]',donorke='$pendonor1[jumDonor]+1'
                    WHERE (Status='1' and NoTrans='$notrans')");

            $kembali1=mysql_query("UPDATE pendonor
                        SET tglkembali='$kembali',jumDonor=jumDonor+1,mu='$mu',up=b'1',tglkembali_apheresis='$kembali'
                        WHERE Kode='$kodependonor'");

            $tambah2=mysql_query("UPDATE stokkantong
                    SET Status='5',no_selang='$noSelang',hasil='5',tgl_Aftap='$today',gol_darah='$golDarah',RhesusDrh='$rhesus',sah='0', kodePendonor='$kodependonor',kadaluwarsa='$kadaluwarsa',mu='$mu'
                    WHERE noKantong='$noKantong'");

            $keluarkan=mysql_query("insert into ar_stokkantong (
                                noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,
                                produk,sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat, kodePendonor,statKonfirmasi, statQC, AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,stokcheck,alasan_buang,tgl_buang,user)
                           select noKantong,jenis,Status,tglTerima,volume,merk,kantongAsal,
                                'WB',sah,Isi,gol_darah,RhesusDrh,stat2,StatTempat,
                                kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,
                                kadaluwarsa,tglpengolahan,mu,stokcheck,'0','$today','$namauser'
                           from stokkantong where noKantong='$noKantong'");

            $tambah3=mysql_query("UPDATE htransaksi set donorke=donorke+1 where NoTrans='$notrans' ");

            $tambah4=mysql_query("UPDATE htransaksi set donorbaru='1' where NoTrans='$notrans' and donorke >'1' ");
        } elseif ($keberhasilan==1){ 
            $tambah=mysql_query("UPDATE htransaksi
                    SET pengambilan='$keberhasilan',no_selang='$noSelang',catatan='$catatan',ketBatal='Pendonor Pergi',petugas='$petugas',Tgl='$tglAftap', status_test='2',Status='2',mu='$mu',gol_darah='$pendonor1[GolDarah]',rhesus='$pendonor1[Rhesus]',jk='$pendonor1[Jk]', pekerjaan='$pendonor1[Pekerjaan]',umur='$pendonor1[umur]',donorke='$pendonor1[jumDonor]'
                    WHERE (Status='1' and NoTrans='$notrans')");

            $tambah4=mysql_query("UPDATE htransaksi set donorbaru='1' where NoTrans='$notrans' and donorke >'1' ");
        }

        //=======Audit Trial====================================================================================
        $log_mdl ='PENGAMBILAN';
        $log_aksi='Pengambilan darah: '.$notrans.' Pendonor: '.$kodependonor.' Kantong: '.$noKantong.' status: '.berhasil;
        include_once "user_log.php";
        //=====================================================================================================

        $tambah_tmp=mysql_query("UPDATE tempudd  SET petugas3='$petugas' where modul='MU CHECKUP'");

        if ($tambah) {
            echo "Data Telah berhasil dimasukkan<br>";
            switch ($_SESSION[leveluser]){
                case "aftap":
                    ?>
<META http-equiv="refresh" content="2; url=pmiaftap.php?module=check">
<?
                    break;
                case "mobile":
                    ?>
<META http-equiv="refresh" content="2; url=pmimobile.php?module=cari_pendonor">
<?
                    break;
                case "p2d2s":
                    ?>
<META http-equiv="refresh" content="2; url=pmip2d2s.php?module=search_pendonor">
<?
                    break;
                case "kasir":
                    ?>
<META http-equiv="refresh" content="2; url=pmikasir.php?module=check">
<?
                    break;
                default:
                    echo "Anda tidak memiliki hak akses";
                }
        }
    }  else { 
        echo "<script>alert('No. Kantong Tidak Sesuai Silahkan Cek Kantong')</script>";
        switch ($_SESSION[leveluser]){
            case "aftap":
                ?>
<META http-equiv="refresh" content="1; url=pmiaftap.php?module=spengambilan">
<?
                break;
            case "mobile":
                ?>
<META http-equiv="refresh" content="1; url=pmimobile.php?module=spengambilan">
<?
                break;
            case "p2d2s":
                ?>
<META http-equiv="refresh" content="1; url=pmip2d2s.php?module=transaksi">
<?
                break;
            case "kasir":
                ?>
<META http-equiv="refresh" content="1; url=pmikasir.php?module=spengambilan">
<?
                break;

            default:
                echo "Anda tidak memiliki hak akses";
        }
    }
    $_POST['periksa']="";
}

?>

<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    $('#cekKantong').focus();
});

window.onload = function() {
    document.getElementById('cekKantong').focus();
};
</script>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 10px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.card {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    overflow: hidden;
}

.card h3 {
    margin: 0;
    font-size: 24px;
    color: #fff;
    background-color: #ff4d4d;
    padding: 10px;
}

.card h5 {
    margin: 0;
    font-size: 16px;
    color: #fff;
    background-color: #FF6346;
    padding: 10px;
}

.card form {
    padding: 20px;
}

.form-group {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    min-width: 100px;
}

.form-group select,
.form-group input {
    flex: 0.3;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    margin-left: 10px;
}

.table-checkup td:hover {
    background-color: #0056b3;
}

.form-group button:hover {
    background-color: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th,
table td {
    border: 1px solid #ddd;
    padding: 5px;
    text-align: left;
    font-size: 14px;
    /* Perkecil font */
}

table th {
    background-color: #ff4d4d;
    color: #fff;
}

.error {
    color: #ff4d4d;
    font-weight: bold;
    margin-top: 10px;
}

.card form {
    margin: 0;
    /* Hilangkan margin */
    padding: 0;
    /* Hilangkan padding jika perlu */
}

.card table {
    margin: 0;
    /* Hilangkan margin antara tabel dan form */
    border-spacing: 0;
    /* Hilangkan jarak antar sel tabel */
    width: 100%;
    /* Pastikan tabel memanjang penuh */
}
</style>

<div class="card">
    <h3>FORM PENGAMBILAN DARAH MENGGUNAKAN ALAT
        <?= isset($dataKantong['nama_alat']) && $dataKantong['nama_alat'] !== '' ? $dataKantong['nama_alat'] : '' ?>
    </h3>
    <form action="" method="post">
        <div class="form-group" style="margin: 10px;">
            <div style="margin-right: 10px">
                <label for="notrans">Nomor Transaksi</label>
                <input type="text" disabled id="notrans" name="notrans" value="<?= htmlspecialchars($notrans) ?>">
            </div>
            <label for="cekKantong">Nomor Kantong</label>
            <input type="text" id="cekKantong" name="cekKantong" required>
            <button type="submit" name="submit" value="submit">Submit</button>
        </div>
    </form>
</div>

<!-- Card Medical Checkup -->
<div style="flex: 1; display: flex; gap: 10px;">
    <div class="card" style="flex: 1; max-width: 50%; padding: 0;">
        <h5>Data Medical Checkup</h5>
        <table class="table-checkup" style="font-size: 14px; width: 100%; border-collapse: collapse;">
            <tr>
                <th colspan="2">Nama Pendonor</th>
                <td colspan="2"><?=$data1[Nama]?></td>
            </tr>
            <tr>
                <th colspan="2">Golongan Darah</th>
                <td colspan="2"><?=$data1[GolDarah]."(".$data1[Rhesus].")"?></td>
            </tr>
            <tr>
                <th colspan="2">Nama Dokter</th>
                <td colspan="2"><?=$a_dok[Nama]?></td>
            </tr>
            <tr>
                <th colspan="2">Berat</th>
                <td colspan="2"><?=$check1[beratBadan]." Kg"?></td>
            </tr>
            <tr>
                <th colspan="2">CuSO<sub>4</sub></th>
                <td colspan="2"><?=$jnscuso4?></td>
            </tr>
            <!-- Hb dan HCT Sebaris -->
            <tr>
                <th>Hb</th>
                <td><?=$check1[Hb]?></td>
                <th>HCT</th>
                <td colspan="2"><?=$check1[Hct] . ($check1[Hct] != "" ? "%" : "-")?></td>
            </tr>
            <!-- Tensi, Suhu, dan Nadi Sebaris -->
            <tr>
                <th>Tensi</th>
                <td><?=$check1[tensi]?></td>
                <th>Suhu</th>
                <td><?=$check1[suhu]?></td>
                <th>Nadi</th>
                <td><?=$check1[nadi]?></td>
            </tr>
        </table>
    </div>

    <!-- Card Pengambilan Darah -->
    <?php if ($dataKantong['up_to_aftap'] == '0'): ?>
    <div class="card" style="flex: 1; max-width: 50%; padding: 0;">
        <h5>Form Pengambilan Darah</h5>
        <form method="POST" action="">
            <div class="form-group">
                <div style="overflow-x: auto;">
                    <table style="font-size: 14px; width: 100%; border-collapse: collapse;">
                        <tr>
                            <th colspan="2">Pengambilan</th>
                            <td colspan="2">
                                <input type="radio" name="keberhasilan" value="0" id="radio-berhasil" checked> Berhasil
                                <input type="radio" name="keberhasilan" value="2" id="radio-gagal"> Gagal
                                <input type="radio" name="keberhasilan" value="1" id="radio-batal"> Batal<br>

                                <select name="catatan" id="select-catatan" style="display: none;">
                                    <option value="">Pilih Jika Gagal</option>
                                    <option value="Mislek">Mislek</option>
                                    <option value="Saran Dokter">Saran Dokter</option>
                                    <option value="Permintaan Pendonor">Permintaan Pendonor</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">Tgl Aftap</th>
                            <td colspan="2"><input type="date" id="tgl_aftap" name="tgl_aftap"
                                    value="<?php echo date('Y-m-d');?>" required></td>
                        </tr>
                        <tr>
                            <th>Diambil Sebanyak</th>
                            <td>
                                <select name='volume_darah'>
                                    <option value="350"
                                        <?= (isset($dataKantong['target_volume']) && $dataKantong['target_volume'] == '350') ? 'selected' : ''; ?>>
                                        350</option>
                                    <option value="450"
                                        <?= (isset($dataKantong['target_volume']) && $dataKantong['target_volume'] == '450') ? 'selected' : ''; ?>>
                                        450</option>
                                </select> CC
                            </td>

                            <th>Volume Terkumpul</th>
                            <td><label><?=$dataKantong['coleted_volume']?> CC</label></td>
                        </tr>
                        <tr>
                            <th>Reaksi Donor</th>
                            <td>
                                <select name="reaksi">
                                    <option value="Normal"
                                        <?= (isset($dataKantong['reaksi']) && $dataKantong['reaksi'] == '100001') ? 'selected' : ''; ?>>
                                        Tidak Ada Keluhan</option>
                                    <option value="Mual"
                                        <?= (isset($dataKantong['reaksi']) && $dataKantong['reaksi'] == '100002') ? 'selected' : ''; ?>>
                                        Mual</option>
                                    <option value="Pusing"
                                        <?= (isset($dataKantong['reaksi']) && $dataKantong['reaksi'] == '100003') ? 'selected' : ''; ?>>
                                        Pusing</option>
                                    <option value="Pingsan"
                                        <?= (isset($dataKantong['reaksi']) && $dataKantong['reaksi'] == '100004') ? 'selected' : ''; ?>>
                                        Pingsan</option>
                                </select>
                            </td>
                            <th>Cara Ambil</th>
                            <td>
                                <select name="caraambil">
                                    <option selected value="0">Aftap</option>
                                    <option value="1">Tromboferesis</option>
                                    <option value="2">Leukaferesis</option>
                                    <option value="3">Plasmaferesis</option>
                                    <option value="4">Eritoferesis</option>
                                    <option value="6">Plasma Konvalesen</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>No Kantong</th>
                            <td><input type="text" id="no_Kantong" name="no_Kantong"
                                    value="<?=$dataKantong['no_kantong']?>" required></td>
                            <th>No. Selang</th>
                            <td>
                                <input type="text" id="no_selang" name="no_selang"
                                    value="<?= $dataKantong['no_selang'] == '' ? substr($dataKantong['no_kantong'], 0, -1) : $dataKantong['no_selang'] ?>"
                                    required>
                            </td>
                        </tr>
                        <tr>
                            <th>Jam Ambil</th>
                            <td>
                                <?php
                                $time_mulai = $dataKantong['collection_date']; // Waktu dari database
                                $time_mulai2 = date("H:i", strtotime($time_mulai)); // Format "Jam:Menit"
                                ?>
                                <input type="time" id="jam_ambil" name="jam_ambil" value="<?=$time_mulai2?>" required>
                            </td>
                            <th>Jam Selesai</th>
                            <td>
                                <?php
                                $time_selesai = $dataKantong['collection_stop_time']; // Waktu dari database
                                $time_selesai2 = date("H:i", strtotime($time_selesai)); // Format "Jam:Menit"
                                ?>
                                <input type="time" id="jam_selesai" name="jam_selesai" value="<?=$time_selesai2?>"
                                    required>
                            </td>

                        </tr>
                        <tr>
                            <th>Total Waktu</th>
                            <?php
                                // Ambil waktu dari database (misal "HH:MM:SS" atau "H:i")
                                $totalWaktu = $dataKantong['collection_time'];
                                // Ubah ke format jam:menit
                                $waktu = date("H:i", strtotime($totalWaktu));
                                // Pisahkan jam dan menit
                                // Pecah jadi jam dan menit
                                $parts       = explode(":", $waktu);
                                $jam         = isset($parts[0]) ? (int) $parts[0] : 0;
                                $menit       = isset($parts[1]) ? (int) $parts[1] : 0;

                                 // Bangun teks output
                                if ($jam == "00" && $menit == "00") {
                                    $tampilWaktu = "-";
                                } else {
                                    $tampilParts = array();
                                    if ($jam > 0) {
                                        $tampilParts[] = $jam . " Jam";
                                    }
                                    if ($menit > 0) {
                                        $tampilParts[] = $menit . " Menit";
                                    }
                                    $tampilWaktu = implode(" ", $tampilParts);
                                }
                                ?>
                            <!-- <td>
                                <span><?= $tampilWaktu ?></span>
                            </td> -->
                            <td>
                                <!-- Beri ID agar mudah diakses oleh JS -->
                                <span id="total_waktu"><?= $tampilWaktu /* nilai awal dari PHP */ ?></span>
                            </td>
                            <th>Petugas Aftap</th>
                            <td>
                                <select name="petugas">
                                    <?
                                  $dokter="select * from user where multi like '%aftap%' order by nama_lengkap";
                                  $do=mysql_query($dokter);
                                  while($data=mysql_fetch_array($do)){                  
                                      if ($data[nama_lengkap] == $namauser){
                                          echo "<option value=$data[nama_lengkap] selected>$data[nama_lengkap]</option>";
                                      }else{
                                          echo "<option value=$data[nama_lengkap]>$data[nama_lengkap]</option>";
                                      }
                                    } 
                                  ?>
                                    <option value="">-</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="4" style="text-align: right; background-color: #fff;">
                                <button type="submit" name="simpan" value="Simpan">Simpan</button>
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
    </div>
    <?php elseif ($dataKantong['up_to_aftap'] == '1'): ?>
    <div class="error">Nomor kantong sudah digunakan.</div>
    <?php elseif (isset($_POST['submit'])): ?>
    <div class="error">Nomor kantong tidak ditemukan.</div>
    <?php endif; ?>
</div>

<script type="text/javascript">
// Ambil elemen radio dan select
const radioGagal = document.getElementById('radio-gagal');
const radioBerhasil = document.getElementById('radio-berhasil');
const radioBatal = document.getElementById('radio-batal');
const selectCatatan = document.getElementById('select-catatan');

// Tambahkan event listener pada semua radio
document.querySelectorAll('input[name="keberhasilan"]').forEach(radio => {
    radio.addEventListener('change', () => {
        if (radioGagal.checked) {
            selectCatatan.style.display = 'inline-block'; // Tampilkan select
        } else {
            selectCatatan.style.display = 'none'; // Sembunyikan select
            selectCatatan.value = ""; // Reset value jika tersembunyi
        }
    });
});
</script>

<script>
// Fungsi untuk menghitung selisih waktu
function hitungTotalWaktu() {
    const jamMulai = document.getElementById('jam_ambil').value;
    const jamSelesai = document.getElementById('jam_selesai').value;
    const output = document.getElementById('total_waktu');

    if (!jamMulai || !jamSelesai) {
        output.textContent = '-';
        return;
    }


    // Parse ke menit sejak tengah malam
    const [h1, m1] = jamMulai.split(':').map(Number);
    const [h2, m2] = jamSelesai.split(':').map(Number);
    let menitMulai = h1 * 60 + m1;
    let menitSelesai = h2 * 60 + m2;

    // Jika selesai di esok hari, tambahkan 24 jam
    if (menitSelesai < menitMulai) {
        menitSelesai += 24 * 60;
    }

    let diff = menitSelesai - menitMulai;
    if (diff <= 0) {
        output.textContent = '-';
        return;
    }

    const jam = Math.floor(diff / 60);
    const menit = diff % 60;
    const parts = [];
    if (jam > 0) parts.push(jam + ' Jam');
    if (menit > 0) parts.push(menit + ' Menit');
    output.textContent = parts.join(' ') || '-';
}

// Pasang listener pada kedua input time
document.getElementById('jam_ambil').addEventListener('change', hitungTotalWaktu);
document.getElementById('jam_selesai').addEventListener('change', hitungTotalWaktu);

// Hitung sekali saat page load agar sesuai value awal
// window.addEventListener('DOMContentLoaded', hitungTotalWaktu);
</script>