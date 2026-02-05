<?php

/***********************************************
 * Author 	: suwena 
 * Date 	: 26 Mei 2018
 * Fungsi	: Form Serah Terima Darah dari Aftap/Mobile unit utk Karantina
 * Keterangan Modul : 
 * 		Pengganti pengesahan kantong
 * 		Sekaligus membuat formulir Serah Terima ke 
 *			- Bag Karantina atau Komponen
 *			- Bag Uji Saring Darah IMLTD
 *			- Bag Uji Konfirmasi Golongan Darah
 * 		Status Darah yang sah langsung menjadi KARANTINA
 * 		Stok Position : PENYIMPANAN DARAH KARANTINA
 * Table terkait : 
 *		- Select : stokkantong join htransaksi
 *		- exec   : serahterima_h, serahterima_detail, serahterima_detail_tmp
 ***********************************************/
$nodokumen = "-";
?>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<style>
    .awesomeText {
        color: #000;
        font-size: 100%;
    }
</style>

<style>
    #serahterima {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 14px;
        border-collapse: collapse;
    }

    #serahterima td,
    #serahterima th {
        border: 1px solid #ddd;
        padding: 3px;
    }

    #serahterima tr:nth-child(even) {
        background-color: #ffe6e6;
    }

    #serahterima tr:hover {
        background-color: #ddd;
    }

    #serahterima th {
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: left;
        font-weight: lighter;
        background-color: #ff9999;
        color: #000000;
    }

    #serahterima input {
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: left;
        background-color: lightyellow;
        color: #000000;
    }
</style>
<style>
    #entrybox {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 14px;
        border-collapse: collapse;
    }

    #entrybox td,
    #entrybox th {
        border: 1px solid #ddd;
        background-color: #ffe6e6;
        padding: 3px;
    }

    #entrybox th {
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: left;
        font-weight: lighter;
        background-color: #ffe6e6;
        color: #000000;
    }

    #entrybox input {
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: left;
        font-weight: bold;
        background-color: #e6ffe6;
        color: #000000;
    }
</style>
<script language="javascript">
    function setFocus() {
        document.sahdarah.nomorkantong.focus();
    }
</script>
<script type="text/javascript">
    /***********************************************
     * Disable "Enter" key in Form script- By Nurul Fadilah(nurul@REMOVETHISvolmedia.com)
     * This notice must stay intact for use
     * Visit http://www.dynamicdrive.com/ for full source code
     ***********************************************/

    function handleEnter(field, event) {
        var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
        if (keyCode == 13) {
            var i;
            for (i = 0; i < field.form.elements.length; i++)
                if (field == field.form.elements[i])
                    break;
            i = (i + 1) % field.form.elements.length;
            field.form.elements[i].focus();
            return false;
        } else
            return true;
    }
</script>

<body onLoad=setFocus();>
    <?php

    include('config/dbi_connect.php');
    date_default_timezone_set("Asia/Jakarta");
    $now            = date("dmyHi");

    $today            = date("Y-m-d H:i:s");
    $namauser        = $_SESSION['namauser'];
    $namauserlkp    = $_SESSION['nama_lengkap'];
    $level            = $_SESSION['leveluser'];
    if ($level == "komponen") {
        $trans = 'KP-' . $now;
    } else {
        $trans = 'PR-' . $now;
    }
    $modul            = "KARANTINA";
    $bag_pengirim    = "AFTAP";
    $bag_penerima    = "KOMPONEN, IMLTD & KGD";
    if (isset($_POST['asal'])) {
        $asal_sample = $_POST['asal'];
    } else {
        $asal_sample = "";
    }
    if (isset($_POST['kodealat'])) {
        $kode_alat = $_POST['kodealat'];
    } else {
        $kode_alat = "";
    }
    if (isset($_POST['suhu'])) {
        $suhu = $_POST['suhu'];
    } else {
        $suhu = "";
    }
    if (isset($_POST['keadaan'])) {
        $keadaan = $_POST['keadaan'];
    } else {
        $keadaan = "";
    }
    if (isset($_POST['alasan'])) {
        $alasan = $_POST['alasan'];
    }
    if (isset($_POST['trans'])) {
        $trans = $_POST['trans'];
    }
    $shift_terima = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT nama FROM `shift` WHERE `jam`<=current_time() and `sampai_jam`>=current_time()"));
    //echo "Alasan Anda ".$alasan;

    if (isset($_POST['submit1'])) {
        //Semua input harus terisi, bila blm lengkap, Alert dan balik
        $no_kantong = mysql_real_escape_string($_POST['nomorkantong']);
        $trans    = $_POST['trans'];
        $alasan   = $_POST['alasan'];
        if ((strlen($no_kantong) == 0) or (empty($no_kantong))) {
            $nokantong_kosong = "1";
        } else {
            $nokantong_kosong = "0";
        }

        if ($nokantong_kosong == "0") {

            $cek = "SELECT `noKantong` from `ar_stokkantongtemp` WHERE `noKantong`='$no_kantong'";
            $cek1 = mysqli_fetch_assoc(mysqli_query($dbi, $cek));
            if ($no_kantong == $cek1['noKantong']) {
                $message = "Nomor <b>$no_kantong SUDAH ADA</b> dalam list";
            } else {
                $cari = "SELECT s.`noKantong`, s.`mu`, s.`Status`,s.`stat2`,s.`StatTempat`,s.`tglpengolahan`,s.`tglTerima`,s.`kodePendonor`,s.`jenis`, s.`produk`, s.`gol_darah`,s.`RhesusDrh`,s.`merk`,s.`tgl_Aftap`,s.`sah`,s.`tglperiksa`,s.`kadaluwarsa`,s.`volume` FROM `stokkantong` s LEFT JOIN `htransaksi` h on s.`noKantong`=h.`NoKantong` WHERE  s.`noKantong`='$no_kantong'";
                $ck = mysqli_fetch_assoc(mysqli_query($dbi, $cari));
                //echo $cari;

		
                if (($ck['Status'] == "0") or ($ck['Status'] == "1") or ($ck['Status'] == "2") or ($ck['Status'] == "7") or ($ck['Status'] == "4") or ($ck['Status'] == "5")) {
                    

		    $sql_tmp = "INSERT INTO `ar_stokkantongtemp` (notrans,bagian,noKantong,jenis,`Status`,tglTerima,volume,merk,kantongAsal,produk,sah,gol_darah,RhesusDrh,stat2,StatTempat,kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,alasan_buang, tgl_buang, user)VALUES('$trans','$level','$no_kantong','$ck[jenis]', '$ck[Status]', '$ck[tglTerima]','$ck[volume]', '$ck[merk]', '3372', '$ck[produk]','$ck[sah]', '$ck[gol_darah]', '$ck[RhesusDrh]',  '$ck[stat2]', '$ck[StatTempat]','$ck[kodePendonor]', '1', '1', '3372', '$ck[tgl_Aftap]', '$ck[kadaluwarsa]', '$ck[tglpengolahan]',  '$ck[mu]', '$alasan', '$today', '$namauser')";
                    //echo "$sql_tmp";
                    $add = mysqli_query($dbi, $sql_tmp);
                    $message = "Nomor <b>$no_kantong Berhasil</b> dimasukkan dalam list";
                } else {
                    $message = "Nomor <b>$no_kantong </b> tidak dapat dimasukkan dalam list, silahkan cek kantong";
                }
            }
        } else {
            $message = "Nomor kantong <b>TIDAK BOLEH</b> kosong";
        }
    }
    if (isset($_POST[submit3])) {
        echo "Transaksi Serah Terima Darah dan Sampel Darah : DIBATALKAN<br>";
        //echo "<meta http-equiv='rupdateefresh' content='2;url=pmiaftap.php?module=serahterima'";
        if ($level == "komponen") {
            echo "<meta http-equiv='refresh' content='2;url=pmikomponen.php?module=musnahlist'";
        } else if ($level == "qa") {
            echo "<meta http-equiv='refresh' content='2;url=pmiqa.php?module=musnahlist'";
        } else if ($level == "imltd") {
            echo "<meta http-equiv='refresh' content='2;url=pmiimltd.php?module=musnahlist'";
        }
    }
    if (isset($_POST[submit2])) {
        //Generated NoTransaksi===============================================
        $trans = $_POST['trans'];
        //END Generate no transaksi===============================================
        $instansi       = $_POST['instansi'];
        $ptg_penerima   = $_POST['ptg_penerima'];
        $shift          = $shift_terima['nama'];
	
	$nama_file_ba = NULL;
        		    
	if (isset($_FILES['upload_berita_acara']) && $_FILES['upload_berita_acara']['error'] == 0) {

                        $file_name = $_FILES['upload_berita_acara']['name'];
                        $file_tmp  = $_FILES['upload_berita_acara']['tmp_name'];
                        $file_size = $_FILES['upload_berita_acara']['size'];
                        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        // Validasi ekstensi
                        $allowed_ext = array('jpg', 'jpeg', 'png', 'pdf');
                        if (!in_array($file_ext, $allowed_ext)) {
                            $message = "Format Berita Acara tidak valid (jpg, jpeg, png, pdf)";
                            goto end_submit;
                        }

                        // Validasi ukuran 5MB
                        if ($file_size > 5 * 1024 * 1024) {
                            $message = "Ukuran Berita Acara maksimal 5MB";
                            goto end_submit;
                        }

                        // Folder upload
                        $upload_dir = __DIR__ . "/file_berita_acara/";
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        // Rename file
                        $nama_file_ba = "BA_" . $trans . "_" . time() . "." . $file_ext;
                        $upload_path = $upload_dir . $nama_file_ba;

                        // Upload
                        if (!move_uploaded_file($file_tmp, $upload_path)) {
                            $message = "Gagal upload Berita Acara";
                            goto end_submit;
                        }
                    }
	
	if($nama_file_ba == null){
	   $message= "Wajib Upload Berita Acara";
	   goto end_submit;
	 }
	
        $sa = "INSERT INTO `ar_stokkantong_trans`(notrans, tgl, bagian, ptgs_musnah, ptgs_limbah, pengelola, shift, file_berita_acara)
    	    VALUES ('$trans','$today', '$level', '$namauser', '$ptg_penerima', '$instansi', '$shift', " . ($nama_file_ba ? "'$nama_file_ba'" : "NULL") . ")";
        //echo "$sa<br>";
        $a  = mysqli_query($dbi, $sa);
        $sq = "SELECT * FROM `ar_stokkantongtemp` WHERE `bagian`='$level'";
        $tmp = mysqli_query($dbi, $sq);
        $no = 0;
        while ($dta = mysqli_fetch_assoc($tmp)) {
            $no++;
            //echo "Proses : $no $dta[dst_nokantong]<br>";
            //insert serahterima_detail
            $q_detail = "INSERT INTO `ar_stokkantong`(notrans,bagian,noKantong,jenis,`Status`,tglTerima,volume,merk,kantongAsal,produk,sah,gol_darah,RhesusDrh,stat2,StatTempat,kodePendonor,statKonfirmasi,statQC,AsalUTD,tgl_Aftap,kadaluwarsa,tglpengolahan,mu,alasan_buang, tgl_buang, user)VALUES ( '$trans','$level','$dta[noKantong]','$dta[jenis]', '$dta[Status]', '$dta[tglTerima]','$dta[volume]', '$dta[merk]', '3372', '$dta[produk]','$dta[sah]', '$dta[gol_darah]', '$dta[RhesusDrh]',  '$dta[stat2]', '$dta[StatTempat]','$dta[kodePendonor]', '1', '1', '3372', '$dta[tgl_Aftap]', '$dta[kadaluwarsa]', '$dta[tglpengolahan]',  '$dta[mu]', '$dta[alasan_buang]', '$today', '$namauser')";


            //echo "$q_detail<br>";
            $add_d  = mysqli_query($dbi, $q_detail);
            //Update Stokkantong menjadi musnah
            $updatektg3 = mysqli_query($dbi, "update `stokkantong` set  `Status`='6'  where `noKantong`='$dta[noKantong]'");

            //=======Audit Trial====================================================================================
            $log_mdl = $level;
            $log_aksi = 'Pemusnahan Produk Darah : ' . $dta['noKantong'] . '  No. transaksi: ' . $trans . ' Kode Pemusnahan : ' . $dta['alasan_buang'];
            include "user_log.php";
            //=====================================================================================================
        }
        //PRINT FORMULIR SERAH TERIMA
        //Hapus temporary
        $sq_del = mysqli_query($dbi, "DELETE FROM `ar_stokkantongtemp` WHERE `notrans`='$trans' AND `bagian`='$level' AND `user`='$namauser'");
        echo "TRANSAKSI PEMUSNAHAN SUKSES, Kantong kantong berpindah ke PEMUSNAHAN";

        /*
        if ($level == "komponen"){
            echo "<meta http-equiv='refresh' content='2;url=pmikomponen.php?module=musnahlist'";
       }else {
            echo "<meta http-equiv='refresh' content='2;url=pmiqa.php?module=musnahlist'";
       }
        */
        echo "<meta http-equiv='refresh' content='2;url=musnah_label.php?notrans=$trans'";
	
	end_submit: // <-- hanya dieksekusi jika ada error

    if (isset($message) && $message != '') {
        echo "<script>alert('".addslashes($message)."');</script>";
        // tetap di halaman upload
        echo "<meta http-equiv='refresh' content='0'>";
        exit;
    }

    }


    ?>
    <a name="atas" id="atas"></a>
    <center>
        <div style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">PEMUSNAHAN PRODUK DARAH</div>
    </center>
    <p>
        <hr style="width: 100%;text-align:left;margin-left:0;color: #0099ff">
        <?php
        $sr = mysql_fetch_assoc(mysql_query("SELECT  `dst_asal`, `dst_kodealat`,  `dst_suhu`, `dst_keadaan` FROM `serahterima_detail_tmp` WHERE `dst_modul`='$modul' AND `dst_user`='$namauser'"));
        $keadaan      = $sr['dst_keadaan'];
        $suhu         = $sr['dst_suhu'];
        $kode_alat    = $sr['dst_kodealat'];
        $asal_sample  = $sr['dst_asal'];
        ?>
    <form name=sahdarah method=post enctype="multipart/form-data">
        <table style="width: 100%; border-collapse: collapse;border: 2px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr>
                <td style="vertical-align: top; width=100%;">
                    <table id="serahterima" style="width: 98%;">
                        <tr>
                            <th>Nomor Transaksi</th>
                            <td><input type="hidden" name="trans" value=<?php echo $trans; ?>><?php echo $trans; ?></td>
                        </tr>
                        <tr>
                            <th>Asal Pemusnahan</th>
                            <td><?php echo strtoupper($level); ?></td>
                        </tr>
                        <tr>
                            <th>Petugas Pemusnahan</th>
                            <td><?php echo strtoupper($namauserlkp); ?></td>
                        </tr>
                    </table>
                </td>

            </tr>
        </table>

        <br>
        <table id="entrybox" width="100%" style="border-collapse: collapse;border: 2px solid #ff0000;width: 100%; box-shadow: 1px 2px 2px #800000;">
            <tr>
                <td>Alasan Pemusnahan</td>
                <?php
                $A = '';
                $B = '';
                $C = '';
                $D = '';
                $E = '';
                $F = '';
                $G = '';
                $H = '';
                $I = '';
                $J = '';
                $K = '';
                $L = '';
                $M = '';
                $N = '';
                $O = '';
                $P = '';
                $Q = '';
                if ($alasan == '0') $A = 'selected';
                if ($alasan == '11') $B = 'selected';
                if ($alasan == '4') $C = 'selected';
                if ($alasan == '6') $D = 'selected';
                if ($alasan == '1') $E = 'selected';
                if ($alasan == '2') $F = 'selected';
                if ($alasan == '5') $G = 'selected';
                if ($alasan == '8') $H = 'selected';
                if ($alasan == '10') $I = 'selected';
                if ($alasan == '13') $J = 'selected';
                if ($alasan == '9') $K = 'selected';
                if ($alasan == '7') $L = 'selected';
                if ($alasan == '12') $M = 'selected';
                if ($alasan == '3') $N = 'selected';
                if ($alasan == '14') $O = 'selected';
                if ($alasan == '15') $P = 'selected';
                if ($alasan == '16') $Q = 'selected';

                if ($alasan == '17') $R = 'selected';
                if ($alasan == '18') $S = 'selected';
                if ($alasan == '19') $T = 'selected';
                if ($alasan == '20') $U = 'selected';
                if ($alasan == '21') $V = 'selected';
                if ($alasan == '22') $W = 'selected';
                if ($alasan == '23') $X = 'selected';
                if ($alasan == '24') $Y = 'selected';
                if ($alasan == '25') $Z = 'selected';
                if ($alasan == '26') $AA = 'selected';
                if ($alasan == '27') $AB = 'selected';
                ?>
                <td>
                    <select name="alasan" onchange="document.musnah.nomorkantong.focus()">
                        <option value="">Pilih Alasan</option>
                        <option value="0" <?= $A ?>>Gagal Aftap</option>
                        <option value="11" <?= $B ?>>Bekas Pembuatan TPK</option>
                        <option value="4" <?= $C ?>>Reaktif Buang</option>
                        <!--option value="11">Reaktif Dirujuk Ke UTDP</option-->
                        <option value="6" <?= $D ?>>Greyzone</option>
                        <option value="1" <?= $E ?>>Lisis</option>
                        <option value="2" <?= $F ?>>Kadaluwarsa</option>
                        <option value="5" <?= $G ?>>Lifemik</option>
                        <option value="8" <?= $H ?>>Kantong Bocor</option>
                        <option value="10" <?= $I ?>>Pembuatan Leucodepleted</option>
                        <option value="13" <?= $J ?>>Plasma Sisa PRC</option>
                        <option value="9" <?= $K ?>>Satelit Rusak</option>
                        <option value="7" <?= $L ?>>DCT Positif</option>
                        <option value="12" <?= $M ?>>Hematokrit Tinggi</option>
                        <option value="3" <?= $N ?>>Plebotomi Terapi</option>
                        <option value="14" <?= $O ?>>Leukosit Tinggi</option>
                        <option value="15" <?= $P ?>>Produk Rusak</option>
                        <option value="16" <?= $Q ?>>Produk Sample QC</option>
                        <option value="17" <?= $R ?>>Plasma Kuning</option>
                        <option value="18" <?= $S ?>>Plasma Merah</option>
                        <option value="19" <?= $T ?>>Plasma Hijau</option>
                        <option value="20" <?= $U ?>>Selang Pendek</option>
                        <option value="21" <?= $V ?>>Selang Merah</option>
                        <option value="22" <?= $W ?>>Volume Lebih</option>
                        <option value="23" <?= $X ?>>Volume Kurang</option>
                        <option value="24" <?= $Y ?>>ABS Positif</option>
                        <option value="25" <?= $Z ?>>Menggumpal</option>
                        <option value="26" <?= $AA ?>>Clot</option>
                        <option value="27" <?= $AB ?>>Jejak IMLTD Reaktif</option>
                    </select>
                </td>
                <td>Masukkan Nomor Kantong</td>
                <!--td><input type=text name="nomorkantong" id="nomorkantong" autofocus onkeypress="return handleEnter(this, event)"></td-->
                <td><input type=text name="nomorkantong" id="nomorkantong"></td>

                <td><input type="submit" name="submit1" value="Ok" class="swn_button_red" style="color: #ffff00"></td>
            </tr>
            <tr>
                <td style="height: 30px">Status Proses</td>
                <td colspan="7"><?= $message ?></td>
            </tr>
        </table>


        <br>
        <table id="serahterima" width="100%" style="border-collapse: collapse;border: 1px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr style="font-size: 12px">




                <th style="height: 40px;text-align: center;font-weight: bold">No</th>
                <th style="height: 40px;text-align: center;font-weight: bold">No Kantong</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jenis<br>Ktg</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Merk</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Status</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Gol<br>Drh</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Tgl<br>Aftap</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Kode Donor</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Alasan Pemusnahan</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Aksi</th>

            </tr>
            <?php
            $no = 0;
            //CAri dari table temporary
            $qry = "SELECT * from ar_stokkantongtemp where bagian='$level' order by inserr_on DESC";
            //echo $qry;
            //echo "$qry";
            $sql = mysqli_query($dbi, $qry);
            $no = mysqli_num_rows($sql) + 1;
            while ($tmp = mysqli_fetch_assoc($sql)) {
                $no--;
                switch ($tmp['Status']) {
                    case '0':
                        $ckt_status = "Kosong";
                        break;
                    case '1':
                        if ($tmp['dst_sahktg'] == "0") {
                            $ckt_status = "Aftap";
                        } else {
                            $ckt_status = "Karantina";
                        }
                        break;
                    case '2':
                        $ckt_status = "Sehat";
                        break;
                    case '3':
                        $ckt_status = "Keluar";
                        break;
                    case '4':
                        $ckt_status = "Rusak-Reaktif";
                        break;
                    case '5':
                        $ckt_status = "Rusak-Gagal";
                        break;
                    case '6':
                        $ckt_status = "Rusak-Dimusnahkan";
                        break;
                    default:
                        $ckt_status = "Kantong Belum Terdaftar";
                        break;
                }

                switch ($tmp['alasan_buang']) {
                    case '0':
                        $alsn = "Gagal Aftap";
                        break;
                    case '1':
                        $alsn = "Lisis";
                        break;
                    case '2':
                        $alsn = "Kadaluwarsa";
                        break;
                    case '3':
                        $alsn = "Plebotomi";
                        break;
                    case '4':
                        $alsn = "Reaktif Buang";
                        break;
                    case '5':
                        $alsn = "Lifemik";
                        break;
                    case '6':
                        $alsn = "Greyzone";
                        break;
                    case '7':
                        $alsn = "DCT Positif";
                        break;
                    case '8':
                        $alsn = "Kantong Bocor";
                        break;
                    case '9':
                        $alsn = "Satelit Rusak";
                        break;
                    case '10':
                        $alsn = "Bekas Pembuatan WE";
                        break;
                    case '11':
                        $alsn = "Reaktif Dirujuk Ke UTDP";
                        break;
                    case '12':
                        $alsn = "Hematokrit Tinggi";
                        break;
                    case '13':
                        $alsn = "Plasma Sisa PRC";
                        break;
                    case '14':
                        $alsn = "Leukosit Tinggi";
                        break;
                    case '15':
                        $alsn = "Produk Rusak";
                        break;
                    case '16':
                        $alsn = "Produk Sample QC";
                        break;
                    case '17':
                        $alsn = "Plasma Kuning";
                        break;
                    case '18':
                        $alsn = "Plasma Merah";
                        break;
                    case '19':
                        $alsn = "Plasma Hijau";
                        break;
                    case '20':
                        $alsn = "Selang Pendek";
                        break;
                    case '21':
                        $alsn = "Selang Merah";
                        break;
                    case '22':
                        $alsn = "Volume Lebih";
                        break;
                    case '23':
                        $alsn = "Volume Kurang";
                        break;
                    case '24':
                        $alsn = "ABS Positif";
                        break;
                    case '25':
                        $alsn = "Menggumpal";
                        break;
                    case '26':
                        $alsn = "Clot";
                        break;
                    case '27':
                        $alsn = "Jejak IMLTD Reaktif";
                        break;
                    default:
                        $alsn = "Kantong Belum Terdaftar";
                        break;
                }

            ?>
                <tr style="font-size: 12px">
                    <td align="right"><?= $no ?>.</td>
                    <td><?= $tmp['noKantong'] ?></td>
                    <td align="center"><?= $tmp['jenis'] ?></td>
                    <td><?= $tmp['merk'] ?></td>
                    <td><?= $ckt_status ?></td>
                    <td style="text-align: center"><?= $tmp['gol_darah'] . $tmp['RhesusDrh'] ?></td>
                    <td align="center"><?= $tmp['tgl_Aftap'] ?></td>
                    <td align="center"><?= $tmp['kodePendonor'] ?></td>
                    <td align="center"><?= $alsn ?></td>
                    <?php if ($level == "komponen") { ?>
                        <td><a href="pmikomponen.php?module=musnahdelrow&op=del&ktg=<?= $tmp['noKantong'] ?>&usr=<?= $namauser ?>&bagian=<?= $level ?>" onclick="return confirm('PERHATIAN \n \nYakin akan menghapus Nomor kantong \n<?= $tmp['noKantong'] ?> ?');">Hapus</a></td>
                    <?php } else { ?>
                        <td><a href="pmiqa.php?module=musnahdelrow&op=del&ktg=<?= $tmp['noKantong'] ?>&usr=<?= $namauser ?>&bagian=<?= $level ?>" onclick="return confirm('PERHATIAN \n \nYakin akan menghapus Nomor kantong \n<?= $tmp['noKantong'] ?> ?');">Hapus</a></td>
                    <?php } ?>


                </tr>
            <?php
            }
            ?>
            <tr>
                <td style="vertical-align: top;" colspan="7">
                    <table id="serahterima">
			<tr>
			    <th>Lampiran Berita Acara<label style="color:red;">*</label></th>
			    <td><input type="file" name="upload_berita_acara" accept=".jpg, .jpeg, .png, .pdf"/><br>
                    	<label>Format File: .PDF, .JPEG, .JPG, .PNG</label><br/>
			<label>Max File Size 5MB</label></td>
			</tr>

                        <tr>
                            <th>Instansi Pengelola Limbah</th>
                            <td><select name="instansi" id="ptg_menyerahkan">
                                    <option value="">-</option>
                                    <?
                                    $usr    = mysqli_query($dbi, "select * from supplier where jenis='4' order by Nama Asc");
                                    while ($usr1    = mysqli_fetch_assoc($usr)) {
                                    ?><option value="<?= $usr1[Kode] ?>"><?= $usr1['Nama'] ?><?
                                                                                            }
                                                                                                ?>
                                </select></td>
                        </tr>
                        <tr>
                            <th>Petugas Instansi Pengelola Limbah</th>
                            <td><input name="ptg_penerima" type="text"></td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top;" colspan="11">
                    <table id="serahterima" style="border: 0px; width: 100%;">
                        <tr>
                            <th colspan="2"><b>CATATAN</b></th>
                        </tr>
                        <tr>
                            <th>Instansi Pengelola Limbah</th>
                            <td>Jika Pilihan Instansi Pengelola Limbah Tidak/Belum Ada, Silahkan Input Data terlebih Dahulu di Level Logistik - Menu Transaksi - Sub Menu Data Kontak</td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
        <hr style="width: 100%;text-align:left;margin-left:0; line-height: 1px">
        <input type="submit" name="submit2" value="Simpan Transaksi Pemusnahan" onclick="return confirm('PERHATIAN \n \nSimpan transaksi pemusnahan darah ini?');" class="swn_button_blue">
        <a href="pmi<?php echo $level; ?>.php?module=musnahbatal&op=batal&usr=<?= $namauser ?>&bagian=<?= $level ?>" onclick="return confirm('PERHATIAN \n \nYakin akan membatalkan transaksi pemusnahan darah ini?');" class="swn_button_blue">Batalkan Transaksi Pemusnahan</a>
    </form>
    <div style="font-size:10px; color:#000000; font-family: " Helvetica Neue", Helvetica, Arial, sans-serif;">Build : 21-08-2024</div>