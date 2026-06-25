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

    function enterToSubmit(event) {
        if (event.key === "Enter") {
            document.getElementsByName('submit1')[0].click();
            return false;
        }
    }
</script>

<body onLoad=setFocus();>
    <?php
    include("config/db_connect.php");
    date_default_timezone_set("Asia/Jakarta");
    $today            = date("Y-m-d H:i:s");
    $namauser        = $_SESSION[namauser];
    $namauserlkp    = $_SESSION[nama_lengkap];
    $modul            = "KARANTINA";
    $bag_pengirim    = "AFTAP";
    $bag_penerima    = "KOMPONEN, IMLTD & KGD";
    $q_utd          = mysql_query("select id from utd where aktif='1'");
    $utd            = mysql_fetch_assoc($q_utd);
    $idudd          = $utd['id'];

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


    if (isset($_POST[submit1])) {
        //Semua input harus terisi, bila blm lengkap, Alert dan balik
        $no_kantong = strtoupper(mysql_real_escape_string($_POST['nomorkantong']));
        if ((strlen($no_kantong) == 0) or (empty($no_kantong))) {
            $nokantong_kosong = "1";
        } else {
            $nokantong_kosong = "0";
        }
        $v_sample   = $_POST['sr_sample'];
        $v_sah      = $_POST['sr_status'];
        $v_asal     = $_POST['asal'];
        $v_kodealat = $_POST['kodealat'];
        $v_suhu     = $_POST['suhu'];
        $v_keadaan  = $_POST['keadaan'];
        if ($nokantong_kosong == "0") {
            $shift_terima = mysql_fetch_assoc(mysql_query("SELECT nama FROM `shift` WHERE `jam`<=current_time() and `sampai_jam`>=current_time()"));
            $cek = "SELECT `dst_nokantong` from `serahterima_detail_tmp` WHERE `dst_nokantong`='$no_kantong' AND `dst_modul`='$modul' and `dst_user`='$namauser'";
            $cek1 = mysql_fetch_assoc(mysql_query($cek));
            if ($no_kantong == $cek1['dst_nokantong']) {
                $message = "Nomor <b>$no_kantong</b> SUDAH ADA dalam list";

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                        Swal.fire({
                            icon: 'warning',
                            html: 'Nomor <b>$no_kantong</b> SUDAH ADA dalam list',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            document.getElementById('nomorkantong').focus();
                            document.getElementById('nomorkantong').select();
                        });
                    </script>";
            } else {
                $ck = mysql_fetch_assoc(mysql_query("
                        SELECT 
                            s.`noKantong` AS s_noKantong, s.`jenis`, s.`Status`, s.`gol_darah`, s.`RhesusDrh`, 
                            s.`kodePendonor` AS s_KodePendonor, s.`sah`, s.`merk`, s.`position`, s.`tgl_Aftap`, s.`volume`,
                            h.`KodePendonor` AS h_KodePendonor, h.`NoKantong` AS h_NoKantong, h.`JenisDonor`, h.`donorbaru`, 
                            h.`umur`, h.`Pengambilan`, s.`lama_pengambilan`, h.`jk`, h.`petugas`, h.`Diambil`, 
                            h.`NoTrans`, h.`shift`
                        FROM `stokkantong` s 
                        LEFT JOIN `htransaksi` h ON s.`noKantong` = h.`NoKantong` 
                        WHERE s.`noKantong` = '$no_kantong'
                    "));

                if (
                    ($ck['Status'] == "1" || $ck['Status'] == "2" || $ck['Status'] == "5") &&
                    ($ck['sah'] == "0" || $ck['sah'] == "")
                ) {
                    if (
                        !isset($ck['s_KodePendonor']) || trim($ck['s_KodePendonor']) == '' ||
                        !isset($ck['h_NoKantong']) || trim($ck['h_NoKantong']) == ''
                    ) {
                        // Beri notifikasi dan hentikan proses
                        $message = "Nomor <b>$no_kantong</b> <span style='color:red;'>tidak dapat diproses</span> karena belum ditransaksi atau transaksi gagal...";
                    } else {
                        // Kondisi valid, boleh insert
                        $sql_tmp = "INSERT INTO `serahterima_detail_tmp` (
                                `dst_nokantong`, `dst_statusktg`, `dst_old_position`, `dst_golda`, `dst_rh`, `dst_kodedonor`, 
                                `dst_volumektg`, `dst_jenisktg`, `dst_asal`, `dst_kodealat`, `dst_suhu`, `dst_keadaan`, 
                                `dst_statuspengambilan`, `dst_sample`, `dst_sah`, `dst_modul`, `dst_user`, `dst_sahktg`, 
                                `dst_merk`, `dst_dsdp`, `dst_lamabaru`, `dst_umur`, `dst_lama_aftap`, `dst_kel`, 
                                `dst_ptgaftap`, `dst_volambil`, `dst_no_aftap`, `dst_tglaftap`, `dst_shift_pengirim`, 
                                `dst_shift_penerima`
                            ) VALUES (
                                '$no_kantong', '{$ck['Status']}', '{$ck['position']}', '{$ck['gol_darah']}', '{$ck['RhesusDrh']}', '{$ck['s_KodePendonor']}', 
                                '{$ck['volume']}', '{$ck['jenis']}', '$v_asal', '$v_kodealat', '$v_suhu', '$v_keadaan', 
                                '{$ck['Pengambilan']}', '$v_sample', '$v_sah', '$modul', '$namauser', '{$ck['sah']}', 
                                '{$ck['merk']}', '{$ck['JenisDonor']}', '{$ck['donorbaru']}', '{$ck['umur']}', '{$ck['lama_pengambilan']}', '{$ck['jk']}', 
                                '{$ck['petugas']}', '{$ck['Diambil']}', '{$ck['NoTrans']}', '{$ck['tgl_Aftap']}', '{$ck['shift']}', '{$shift_terima['nama']}'
                            )";
                        $add = mysql_query($sql_tmp);
                        $message = "Nomor <b>$no_kantong</b> <span style='color:green;'>Berhasil</span> dimasukkan dalam list";
                    }
                } else {
                    // Gagal karena status/sah tidak sesuai
                    $message = "Nomor <b>$no_kantong</b> tidak dapat dimasukkan dalam list, karena statusnya sudah disahkan atau kosong di Aftap";
                }
            }
        } else {
            $message = "Nomor kantong <b>TIDAK BOLEH</b> kosong";
        }
    }
    if (isset($_POST[submit3])) {
        echo "Transaksi Serah Terima Darah dan Sampel Darah : DIBATALKAN<br>";
        echo "<meta http-equiv='rupdateefresh' content='2;url=pmiaftap.php?module=serahterima'";
    }
    if (isset($_POST['submit2'])) {
        //Generated NoTransaksi===============================================
        $k_today = $idudd . "-ST" . date("dmy") . "-"; //ST270518-0001
        $kd     = mysql_query("select `hst_notrans` from `serahterima` where `hst_notrans` like '$k_today%' order by `hst_notrans` DESC limit 1");
        $kd1    = mysql_fetch_assoc($kd);
        //$kd2	= substr($kd1['hst_notrans'],9,4);
        $kd2    = substr($kd1['hst_notrans'], 15, 4);
        if ($kd2 < 1) {
            $kd2 = "0000";
        }
        $int_kd2 = (int)$kd2 + 1;
        $i_zero = 4 - (strlen(strval($int_kd2)));
        $kd3 = '';
        for ($n = 0; $n < $i_zero; $n++) {
            $kd3 .= "0";
        }
        $notrans = $k_today . $kd3 . $int_kd2;
        //END Generate no transaksi===============================================

        echo "<div style ='font:16px Arial,tahoma,sans-serif;color:#ff0000'>" . "No Transaksi: " . $notrans . ")</div>";
        $sq = "SELECT `dst_asal`, `dst_kodealat`,`dst_suhu`,`dst_keadaan`,`dst_modul`, `dst_shift_pengirim`, `dst_shift_penerima` FROM `serahterima_detail_tmp`
			 WHERE `dst_modul`='$modul' AND `dst_user`='$namauser' and `dst_asal`<>'' AND `dst_kodealat` <>'' AND `dst_suhu`<>'' AND `dst_keadaan`<>''
 		     GROUP BY `dst_modul`";
        $ck = mysql_fetch_assoc(mysql_query($sq));
        if ($ck == '0') {
            echo "<div style ='font:16px Arial,tahoma,sans-serif;color:#ff0000'>" . "Proses tdk bisa dilanjutkan, BELUM ADA DATA!!!!!" . ")</div>";
            echo "<meta http-equiv='refresh' content='2;url=pmiaftap.php?module=sr_aftap'>";
        } else {
            //echo "Insert data Master - ";
            $usr_pengirim = $_POST['ptg_menyerahkan'];
            $usr_penerima1 = $_POST['ptg_penerima'];
            $usr_penerima2 = $_POST['ptg_sah'];
            $usr_penerima3 = $_POST['ptg_sah_kgd'];
            $sq     = mysql_query($sq);
            $head = mysql_fetch_assoc($sq);
            $sa = "INSERT INTO `serahterima`(`hst_notrans`, `hst_bagpengirim`, `hst_bagpenerima`, `hst_tgl`, `hst_asal`,
    	    `hst_jenis_st`, `hst_user`, `hst_pengirim`, `hst_penerima`, `hst_penerima2`, `hst_penerima3`, `hst_kode_alat`, `hst_suhuterima`,
    	    `hst_kondisiumum`, `hst_peruntukan`, `hst_modul`, `hst_shift_pengirim`, `hst_dariudd`)
    	    VALUES ('$notrans','$bag_pengirim','$bag_penerima',NOW(),'$head[dst_asal]',
    	    'Kantong dan Sample Aftap','$namauser','$usr_pengirim','$usr_penerima1','$usr_penerima2','$usr_penerima3', '$head[dst_kodealat]','$head[dst_suhu]',
    	    '$head[dst_keadaan]','Pengolahan Darah, Pemeriksaan IMLTD & KGD','$modul', '$head[dst_shift_pengirim]', '$idudd')";
            //echo "$sa<br>";
            $a = mysql_query($sa);
            $sq = "SELECT * FROM `serahterima_detail_tmp` WHERE `dst_user`='$namauser' AND `dst_modul`='KARANTINA'";
            $tmp = mysql_query($sq);
            $no = 0;
            while ($dta = mysql_fetch_assoc($tmp)) {
                $no++;
                echo "Proses : $no $dta[dst_nokantong]<br>";
                //insert serahterima_detail
                $q_detail = "INSERT INTO `serahterima_detail`(
            `dst_no_aftap`, `dst_tglaftap`, `dst_notrans`, `dst_nokantong`,`dst_statusktg`, `st_statusktg_new`, `dst_old_position`,
            `dst_new_position`, `dst_sahktg`, `dst_sahktg_new`, `dst_merk`, `dst_golda`, `dst_rh`, `dst_kodedonor`, `dst_berat`,
            `dst_volumektg`, `dst_jenisktg`, `dst_sample`, `dst_sah`, `dst_dsdp`, `dst_lamabaru`, `dst_umur`, `dst_lama_aftap`, `dst_statuspengambilan`,
            `dst_kel`, `dst_ptgaftap`, `dst_volambil`)VALUES (
            '$dta[dst_no_aftap]','$dta[dst_tglaftap]','$notrans','$dta[dst_nokantong]', '$dta[dst_statusktg]','1', '$dta[dst_old_position]',
            '1','$dta[dst_sahktg]','1','$dta[dst_merk]','$dta[dst_golda]','$dta[dst_rh]','$dta[dst_kodedonor]','$dta[dst_berat]',
            '$dta[dst_volumektg]','$dta[dst_jenisktg]','$dta[dst_sample]','$dta[dst_sah]','$dta[dst_dsdp]','$dta[dst_lamabaru]','$dta[dst_umur]','$dta[dst_lama_aftap]', '$dta[dst_statuspengambilan]',
            '$dta[dst_kel]','$dta[dst_ptgaftap]','$dta[dst_volambil]')";
                //echo "$q_detail<br>";
                $add_d = mysql_query($q_detail);
                $kantongall = substr($dta[dst_nokantong], 0, -1);
                //update stokkantong : sah dan posisi=1 (1=KARANTINA)
                //            $updatektg=mysql_query("update `stokkantong` set `sah`='1',`position`='1'  where `noKantong`='$dta[dst_nokantong]'");
                $updatektg = mysql_query("update `stokkantong` set  `sah`='1',`position`='1'  where `noKantong` like '$kantongall%'");
                $updatektg3 = mysql_query("update `stokkantong` set  `sah`='1',`position`='1'  where `noKantong`='$dta[dst_nokantong]'");
                //insert tbl pengesahan
                $pengesahan = mysql_query("insert into pengesahan (nokantong,tgl,ygmenyerahkan,jns,ket,shift)
                                    value ('$dta[dst_nokantong]','$today','$namauser','$dta[dst_jenisktg]','$dta[dst_statuspengambilan]','$cek2[shift]')");
                //Kirim Whatsapp Ucapan Terima Kasih ke Pendonor
                $dk = mysql_query("select nama,telp,telp2 from pendonor where Kode='$dta[dst_kodedonor]' and LENGTH(telp2)>9");
                if (mysql_num_rows($dk) == 1) {
                    $dk1 = mysql_fetch_assoc($dk);
                    $ud = mysql_fetch_assoc(mysql_query("select pesan from sms_setting where id='3'"));
                    $telp = $dk1[telp2];
                    $pesan = 'Yth. ' . $dk1[nama] . ', ' . $ud[pesan];
                    $kirim = mysql_query("INSERT into wagw.outbox(`wa_mode`,`wa_no`,`wa_text`) values('0','$telp','$pesan')");
                }
                if ($dta['dst_sah'] == '1') {
                    $sahkantong = "Sesuai";
                } else {
                    $sahkantong = "Tidak Sesuai";
                }
                //=======Audit Trial====================================================================================
                $log_mdl = 'AFTAP';
                $log_aksi = 'Serah terima (Pengesahan) Kantong Darah & sampel : ' . $dta['dst_nokantong'] . ' dari AFTAP ke KOMPONEN, IMLTD & KGD; No. transaksi: ' . $notrans . ' status : ' . $sahkantong;
                include "user_log.php";
                //=====================================================================================================
            }
            //PRINT FORMULIR SERAH TERIMA

            //Hapus temporary
            $sq_del = mysql_query("DELETE FROM `serahterima_detail_tmp` WHERE `dst_user`='$namauser' AND `dst_modul`='KARANTINA'");
            echo "TRANSAKSI SERAH TERIMA SUKSES, Kantong Sudah Disahkan dan Posisi kantong berpindah ke PENYIMPANAN KARANTINA";
            echo "<meta http-equiv='refresh' content='2;url=pmiaftap.php?module=sr_aftap_list'";
        }
    }


    ?>
    <a name="atas" id="atas"></a>
    <div style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">SERAH TERIMA KANTONG & SAMPLE DARI AFTAP/MOBILE UNIT</div>
    <hr style="width: 100%;text-align:left;margin-left:0;color: #0099ff">
    <?php
    $sr = mysql_fetch_assoc(mysql_query("SELECT  `dst_asal`, `dst_kodealat`,  `dst_suhu`, `dst_keadaan` FROM `serahterima_detail_tmp` WHERE `dst_modul`='$modul' AND `dst_user`='$namauser'"));
    $keadaan      = $sr['dst_keadaan'];
    $suhu         = $sr['dst_suhu'];
    $kode_alat    = $sr['dst_kodealat'];
    $asal_sample  = $sr['dst_asal'];
    ?>
    <form name=sahdarah method=post>
        <table style="width: 100%; border-collapse: collapse;border: 2px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr>
                <td style="vertical-align: top; width=50%;">
                    <table id="serahterima" style="width: 98%;">
                        <tr>
                            <th>Bagian yang mengirimkan</th>
                            <td><input name="bag_pengirim" id="bag_pengirim" type="text" disabled value="<?= $bag_pengirim ?>"></td>
                        </tr>
                        <tr>
                            <th>Jenis Serah Terima</th>
                            <td>Kantong Aftap & Sample</td>
                        </tr>
                        <tr>
                            <th>Bagian yang menerima</th>
                            <td><input name="bag_penerima" id="bag_penerima" type="text" disabled value="<?= $bag_penerima ?>"></td>
                        </tr>
                        <tr>
                            <th>Asal Kantong & Sample</th>
                            <td><input name="asal" id="asal" type="text" required onkeypress="return handleEnter(this, event)" value="<?= $asal_sample ?>"></td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top;width=50%" align="right">
                    <table id="serahterima" style="width: 98%;">
                        <tr>
                            <th>Kode alat pengiriman</th>
                            <td><input name="kodealat" id="kodealat" required onkeypress="return handleEnter(this, event)" type="text" value="<?= $kode_alat ?>"></td>
                        </tr>
                        <tr>
                            <th>Suhu pada saat diterima</th>
                            <td><input name="suhu" id="suhu" type="text" required onkeypress="return handleEnter(this, event)" size="3" value="<?= $suhu ?>"><sup>o</sup>C</td>
                        </tr>
                        <tr>
                            <th>Keadaan umum saat diterima</th>
                            <td><input name="keadaan" id="keadaan" type="text" required onkeypress="return handleEnter(this, event)"" value=" <?= $keadaan ?>"></td>
                        </tr>
                        <tr>
                            <th>Peruntukan</th>
                            <td>Pengolahan, Pemeriksaan IMLTD & KGD
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <br>
        <table id="entrybox" style="border-collapse: collapse;border: 2px solid #ff0000;width: 100%; box-shadow: 1px 2px 2px #800000;">
            <tr>
                <td>Masukkan Nomor Kantong</td>
                <td><input type=text name=nomorkantong id=nomorkantong autofocus onkeypress="return enterToSubmit(event)"></td>
                <td>Status Sample</td>
                <td><select name="sr_sample" id="sr_sample" onkeypress="return handleEnter(this, event)">
                        <option value="1">Sesuai</option>
                        <option value="0">Tidak Sesuai</option>
                    </select></td>
                <td>Status serah terima</td>
                <td><select name="sr_status" id="sr_status" onkeypress="return handleEnter(this, event)">
                        <option value="1">Sesuai</option>
                        <option value="0">Tidak Sah</option>
                    </select></td>
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
                <th style="height: 40px;text-align: center;font-weight: bold">Gol<br>Drh</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Tgl<br>Aftap</th>
                <th style="height: 40px;text-align: center;font-weight: bold">No Aftap</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Status<br>Kantong</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Lama<br>Aftap</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Kode Donor</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jenis<br>Donor</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Dnr Ulang/<br>Baru</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Umur<br>Donor</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jenis<br>Kel</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Ptgs<br>Aftap</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Sample<br>Darah</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Status<br>Terima</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Aksi</th>
            </tr>
            <?php
            $no = 0;
            $qry = "SELECT sd.`dst_id`, sd.`dst_nokantong`, sd.`dst_statusktg`, sd.`dst_golda`, sd.`dst_rh`, sd.`dst_kodedonor`, sd.`dst_berat`, sd.`dst_volumektg`, sd.`dst_sahktg`, 
                        sd.`dst_merk`, sd.`dst_ptgaftap`, sd.`dst_volambil`, sd.`dst_no_aftap`, 
                        DATE(sd.`dst_tglaftap`) AS `dst_tglaftap`,
                        CASE WHEN sd.`dst_dsdp` = '0' THEN 'DS' ELSE 'DP' END AS `dst_dsdp`,
                    CASE WHEN sd.`dst_lamabaru` = '0' THEN 'BR' ELSE 'UL' END AS `dst_lamabaru`,
                    CASE WHEN sd.`dst_kel` = '0' THEN 'LK' ELSE 'PR' END AS `dst_kel`,
                    sd.`dst_umur`, 
                    sd.`dst_lama_aftap`,
                    CASE
                        WHEN sd.`dst_jenisktg` = '1' THEN 'SB'
                        WHEN sd.`dst_jenisktg` = '2' THEN 'DB'
                        WHEN sd.`dst_jenisktg` = '3' AND st.`jenis` = '3' AND st.`metoda` = '' THEN 'TR'
                        WHEN sd.`dst_jenisktg` = '3' AND st.`jenis` = '3' AND st.`metoda` = 'TB' THEN 'TB'
                        WHEN sd.`dst_jenisktg` = '4' THEN 'QD'
                        WHEN sd.`dst_jenisktg` = '5' AND st.`jenis` = '5' AND st.`metoda` = 'TTF' THEN 'LR'
                        WHEN sd.`dst_jenisktg` = '5' AND st.`jenis` = '5' AND st.`metoda` = 'TT' THEN 'NLR'
                        WHEN sd.`dst_jenisktg` = '6' THEN 'PB'
                    END AS `dst_jenisktg`,
                    CASE WHEN sd.`dst_sample` = '1' THEN 'Sesuai' ELSE 'Tdk Sesuai' END AS `dst_sample`,
                    CASE WHEN sd.`dst_sah` = '0' THEN 'Tdk Sesuai' ELSE 'Sesuai' END AS `dst_sah`
                    FROM `serahterima_detail_tmp` sd
                    LEFT JOIN `stokkantong` st ON sd.`dst_nokantong` = st.`noKantong`
                    WHERE sd.`dst_modul` = 'KARANTINA' AND sd.`dst_user` = '$namauser'
                    ORDER BY sd.`dst_id` DESC";
            //echo "$qry";
            $sql = mysql_query($qry);
            $no = mysql_num_rows($sql) + 1;
            while ($tmp = mysql_fetch_assoc($sql)) {
                $no--;
                switch ($tmp[dst_statusktg]) {
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
            ?>
                <tr style="font-size: 12px">
                    <td align="right"><?= $no ?>.</td>
                    <td><?= $tmp['dst_nokantong'] ?></td>
                    <td align="center"><?= $tmp['dst_jenisktg'] ?></td>
                    <td><?= $tmp['dst_merk'] . ' ' . $tmp['dst_volumektg'] ?> ml</td>
                    <td style="text-align: center"><?= $tmp['dst_golda'] . $tmp['dst_rh'] ?></td>
                    <td align="center"><?= $tmp['dst_tglaftap'] ?></td>
                    <td><?= $tmp['dst_no_aftap'] ?></td>
                    <td><?= $ckt_status ?></td>
                    <td><?= $tmp['dst_lama_aftap'] ?> mnt</td>

                    <td><?= $tmp['dst_kodedonor'] ?></td>
                    <td style="text-align: center"><?= $tmp['dst_dsdp'] ?></td>
                    <td style="text-align: center"><?= $tmp['dst_lamabaru'] ?></td>
                    <td style="text-align: center"><?= $tmp['dst_umur'] ?></td>
                    <td style="text-align: center"><?= $tmp['dst_kel'] ?></td>
                    <td style="text-align: center"><?= $tmp['dst_ptgaftap'] ?></td>
                    <td><?= $tmp['dst_sample'] ?></td>
                    <td style="text-align: center"><?= $tmp['dst_sah'] ?></td>

                    <td><a href="pmiaftap.php?module=delrow&op=del&ktg=<?= $tmp['dst_nokantong'] ?>&usr=<?= $namauser ?>&mdl=KARANTINA" onclick="return confirm('PERHATIAN \n \nYakin akan menghapus Nomor kantong \n<?= $tmp['dst_nokantong'] ?> ?');">Hapus</a></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td style="vertical-align: top;" colspan="7">
                    <table id="serahterima">
                        <tr>
                            <th>Petugas yang menyerahkan</th>
                            <td><select name="ptg_menyerahkan" id="ptg_menyerahkan">
                                    <?
                                    $usr = mysql_query("select id_user, nama_lengkap from user WHERE (upper(bagian) like '%AFTAP%' or upper(bagian) like '%PENGAMBILAN%') and aktif='0' order by nama_lengkap");
                                    while ($usr1 = mysql_fetch_assoc($usr)) {
                                    ?><option value="<?= $usr1[id_user] ?>"><?= $usr1['nama_lengkap'] ?><?
                                                                                                    }
                                                                                                        ?>
                                </select></td>
                        </tr>
                        <tr>
                            <th>Petugas yang menerima darah</th>
                            <td><select name="ptg_penerima" id="ptg_penerima">
                                    <?
                                    $usr = mysql_query("select id_user, nama_lengkap from user WHERE (upper(bagian) like '%KOMPONEN%' or upper(bagian) like '%PENGOLAHAN%' or upper(bagian) like '%KARANTINA%' or upper(bagian) like '%PENYIMPANAN%') and aktif='0' order by nama_lengkap");
                                    while ($usr1 = mysql_fetch_assoc($usr)) {
                                    ?><option value="<?= $usr1[id_user] ?>"><?= $usr1['nama_lengkap'] ?><?
                                                                                                    }
                                                                                                        ?>
                                </select></td>
                        </tr>
                        <tr>
                            <th>Petugas yang menerima sampel (IMLTD)</th>
                            <td><select name="ptg_sah" id="ptg_sah">
                                    <?
                                    $usr = mysql_query("select id_user, nama_lengkap from user WHERE (upper(bagian) like '%UJI SARING%' or upper(bagian) like '%IMLTD%' or upper(bagian) like '%SCREENING%') and aktif='0' order by nama_lengkap");
                                    while ($usr1 = mysql_fetch_assoc($usr)) {
                                    ?><option value="<?= $usr1[id_user] ?>"><?= $usr1['nama_lengkap'] ?><?
                                                                                                    }
                                                                                                        ?>
                                </select></td>
                        </tr>
                        <tr>
                            <th>Petugas yang menerima sampel (KGD)</th>
                            <td><select name="ptg_sah_kgd" id="ptg_sah_kgd">
                                    <?
                                    $usrk = mysql_query("select id_user, nama_lengkap from user WHERE (upper(bagian) like '%SCREENING%' or upper(bagian) like '%IMLTD%' or upper(bagian) like '%KONFIRMASI%' or upper(bagian) like '%KGD%') and aktif='0' order by nama_lengkap");
                                    while ($usrkgd = mysql_fetch_assoc($usrk)) {
                                    ?><option value="<?= $usrkgd['id_user'] ?>"><?= $usrkgd['nama_lengkap'] ?><?
                                                                                                            }
                                                                                                                ?>
                                </select></td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top;" colspan="11">
                    <table id="serahterima" style="border: 0px; width: 100%;">
                        <tr>
                            <th colspan="2">CATATAN</th>
                        </tr>
                        <tr>
                            <th>Jenis Ktg (Kantong)</th>
                            <td>SB : Single; DB : Double; TR : Tripple; QD : Quadrupple; PB : Pediatrik</td>
                        </tr>
                        <tr>
                            <th>Jenis Donor</th>
                            <td>DS:Donor Sukarela; DP : Donor Pengganti</td>
                        </tr>
                        <tr>
                            <th>Dnr (Donor) Baru/Ulang</th>
                            <td>BR : Donor BARU; UL : Donor ULANG</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>LK : Laki-laki; PR : Perempuan</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <hr style="width: 100%;text-align:left;margin-left:0; line-height: 1px">
        <input type="submit" name="submit2" value="Simpan Proses Serah Terima" class="swn_button_blue">
        <a href="pmiaftap.php?module=batal&op=batal&usr=<?= $namauser ?>&mdl=<?= $modul ?>" onclick="return confirm('PERHATIAN \n \nYakin akan membatalkan transaksi Serah Terima ini?');" class="swn_button_blue">Batalkan Proses Serah Terima</a>
    </form>
    <div style="font-size:10px; color:#000000; font-family: " Helvetica Neue", Helvetica, Arial, sans-serif;">Build : 26-05-2018</div>