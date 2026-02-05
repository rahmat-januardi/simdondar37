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
<script type="text/javascript" src="js/tgl_rekap.js"></script>
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


    $namauser        = $_SESSION['namauser'];
    $namauserlkp    = $_SESSION['nama_lengkap'];
    $level            = $_SESSION['leveluser'];
    $hariini        = date("Y-m-d");



    if (isset($_POST['minta1'])) {
        $today = $_POST['minta1'];
    } else {
        $today = $hariini;
    }
    if (isset($_POST['minta2'])) {
        $today2 = $_POST['minta2'];
    } else {
        $today2 = $hariini;
    }

    if ($_POST['shift'] != '') {
        $srcshift  = $_POST['shift'];
        $qshift       = " AND shift = '$srcshift' ";
    } else {
        $qshift    = "";
    }
    if ($_POST['alasan'] != '') {
        $srcalasan    = $_POST['alasan'];
        $qalasan       = " AND alasan_buang = '$srcalasan' ";
    } else {
        $qalasan    = "";
    }
    if ($_POST['petugas'] != '') {
        $srcpetugas    = $_POST['petugas'];
        $qpetugas       = " AND user like '%$srcpetugas%' ";
    } else {
        $qpetugas    = "";
    }
    if ($_POST['nomorf'] != '') {
        $srcnk    = $_POST['nomorf'];
        $qnk       = " AND noKantong = '$srcnk' ";
    } else {
        $qnk    = "";
    }
    if ($_POST['produk'] != '') {
        $srcproduk    = $_POST['produk'];
        $qproduk       = " AND produk = '$srcproduk' ";
    } else {
        $qproduk    = "";
    }
    if ($_POST['gol'] != '') {
        $srcgol    = $_POST['gol'];
        $qgol       = " AND gol_darah = '$srcgol' ";
    } else {
        $qgol    = "";
    }
    if ($_POST['jenis'] != '') {
        $srcjenis    = $_POST['jenis'];
        $qjenis      = " AND jenis = '$srcjenis' ";
    } else {
        $qjenis    = "";
    }





    if (isset($_POST[submit1])) {
        /*   $transaksipermintaan    = "select * from ar_stokkantong where CAST(tgl_buang as date)>='$today' and CAST(tgl_buang as date)<='$today2' $qshift $qalasan $qpetugas $qnk $qproduk $qgol $qjenis order by tgl_buang ASC ";

    $no=0;
    //CAri dari table temporary
    //echo $qry;
    //echo "$qry";
    $sql=mysqli_query($dbi,$transaksipermintaan);
    $no=mysqli_num_rows($sql)+1;
    //echo $transaksipermintaan;*/
    }
    if (isset($_POST[submit3])) {
    }
    if (isset($_POST[submit2])) {
    }


    ?>
    <a name="atas" id="atas"></a>
    <center>
        <div style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">RINCIAN PEMUSNAHAN PRODUK DARAH</div>
    </center>
    <p>
        <hr style="width: 100%;text-align:left;margin-left:0;color: #0099ff">

    <form name=sahdarah method=post>
        <table style="border-collapse: collapse;border: 2px solid #ff0000;width: 100%; box-shadow: 1px 2px 2px #800000;">
            <tr>
                <td style="vertical-align: top; width=100%;">
                    <table id="serahterima" style="width: 98%;">
                        <tr>
                            <th>Tanggal Pemusnahan</th>
                            <td><input type=text name=minta1 id=datepicker size=10 value=<?= $today ?>>
                                S/D <input type=text name=minta2 id=datepicker1 size=10 value=<?= $today2 ?>></td>
                        </tr>
                        <tr>
                            <th>Shift</th>
                            <td>
                                <select name="shift">
                                    <option value="">-SEMUA-</option>
                                    <option value="I">I Pagi</option>
                                    <option value="II">II Sore</option>
                                    <option value="I">III Malam</option>
                                    <option value="IV">IV Mid</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Alasan Pemusnahan</th>
                            <td>
                                <select name="alasan">
                                    <option value="">-SEMUA-</option>
                                    <option value="0" <?= $A ?>>Gagal Aftap</option>
                                    <option value="11" <?= $B ?>>Bekas Pembuatan TPK</option>
                                    <option value="4" <?= $C ?>>Reaktif Buang</option>
                                    <!--option value="11">Reaktif Dirujuk Ke UTDP</option-->
                                    <option value="6" <?= $D ?>>Greyzone</option>
                                    <option value="1" <?= $E ?>>Lisis</option>
                                    <option value="2" <?= $F ?>>Kadaluwarsa</option>
                                    <option value="5" <?= $G ?>>Lifemik</option>
                                    <option value="8" <?= $H ?>>Kantong Bocor</option>
                                    <option value="10" <?= $I ?>>Bekas Pembuatan WE</option>
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
                        </tr>
                        <tr>
                            <th>Petugas Pemusnahan</th>
                            <td><input type=text name="petugas"></td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top; width=100%;">
                    <table id="serahterima" style="width: 98%;">
                        <tr>
                            <th>Nomor Kantong</th>
                            <td><input type="text" name="nomorf" id="nomorf"></td>
                        </tr>
                        <tr>
                            <th>Produk Darah</th>
                            <td>

                                <select name="produk">
                                    <option value="" selected>-SEMUA-</option>
                                    <?php
                                    $ql = mysqli_query($dbi, "select * from produk ");

                                    while ($rowl1 = mysqli_fetch_array($ql)) {
                                        echo "<option value=$rowl1[Nama]>$rowl1[Nama]</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Gol. Darah</th>
                            <td>
                                <select name="gol">
                                    <option value="">-SEMUA-</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="O">O</option>
                                    <option value="AB">AB</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis Kantong</th>
                            <td>
                                <select name="jenis">
                                    <option value="">-SEMUA-</option>
                                    <option value="1">SINGLE</option>
                                    <option value="2">DOUBLE</option>
                                    <option value="3">TRIPLE</option>
                                    <option value="4">QUADRUPLE</option>
                                    <option value="5">PEDIATRIK</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td><input type="submit" name="submit1" value="CARI" class="swn_button_blue"></td>
            </tr>

        </table>

        <br>



        <br>

        <table id="serahterima" width="100%" style="border-collapse: collapse;border: 1px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr class="field">
                <th style="height: 40px;text-align: center;font-weight: bold">No</th>
                <th style="height: 40px;text-align: center;font-weight: bold">No Kantong</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jenis</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Gol & Rh</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Produk</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Volume</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Tgl Aftap</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Tgl Kadaluarsa</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Tgl Buang</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Alasan Buang</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Petugas Buang</th>
                <th style="height: 40px;text-align: center;font-weight: bold">No Transaksi</th>
            </tr>
            <?php
            $transaksipermintaan    = "select * from ar_stokkantong where CAST(tgl_buang as date)>='$today' and CAST(tgl_buang as date)<='$today2' $qshift $qalasan $qpetugas $qnk $qproduk $qgol $qjenis order by gol_darah ='' DESC, tgl_buang ASC, gol_darah ASC, produk ASC";
            $no = 0;
            //CAri dari table temporary
            //echo $transaksipermintaan;
            //echo "$qry";
            $sql = mysqli_query($dbi, $transaksipermintaan);
            $jml = mysqli_num_rows($sql);
            $no = mysqli_num_rows($sql) + 1;

            $no = 1;
            if ($jml > 0) {
                while ($datatransaksipermintaan = mysqli_fetch_assoc($sql)) {
            ?>
                    <tr style="font-size: 12px;height: 30px;">
                        <td align="center"><?= $no ?></td>
                        <td align="center"><?= $datatransaksipermintaan['noKantong'] ?></td>
                        <?
                        $jenis1 = 'Single';
                        if ($datatransaksipermintaan[jenis] == '2') $jenis1 = 'Double';
                        if ($datatransaksipermintaan[jenis] == '3') $jenis1 = 'Triple';
                        if ($datatransaksipermintaan[jenis] == '4') $jenis1 = 'Quadruple';
                        if ($datatransaksipermintaan[jenis] == '6') $jenis1 = 'Pediatrik';
                        ?>
                        <td align="center"><?= $jenis1 ?></td>
                        <td align="center"><?= $datatransaksipermintaan['gol_darah'] ?>(<?= $datatransaksipermintaan['RhesusDrh'] ?>)</td>
                        <td align="center"><?= $datatransaksipermintaan['produk'] ?></td>
                        <td align="center"><?= $datatransaksipermintaan['volume'] ?></td>
                        <td align="center"><?= $datatransaksipermintaan['tgl_Aftap'] ?></td>
                        <td align="center"><?= $datatransaksipermintaan['kadaluwarsa'] ?></td>
                        <td align="center"><?= $datatransaksipermintaan['tgl_buang'] ?></td>
                        <?
                        if ($datatransaksipermintaan[alasan_buang] == '2') $alasan1 = 'Kadaluwarsa';
                        if ($datatransaksipermintaan[alasan_buang] == '0') $alasan1 = 'Gagal Aftap';
                        if ($datatransaksipermintaan[alasan_buang] == '3') $alasan1 = 'Plebotomi';
                        if ($datatransaksipermintaan[alasan_buang] == '4') $alasan1 = 'Reaktif Dibuang';
                        if ($datatransaksipermintaan[alasan_buang] == '5') $alasan1 = 'Lifemik';
                        if ($datatransaksipermintaan[alasan_buang] == '6') $alasan1 = 'Greyzone';
                        if ($datatransaksipermintaan[alasan_buang] == '7') $alasan1 = 'DCT Positif';
                        if ($datatransaksipermintaan[alasan_buang] == '8') $alasan1 = 'Kantong Bocor';
                        if ($datatransaksipermintaan[alasan_buang] == '1') $alasan1 = 'Lisis';
                        if ($datatransaksipermintaan[alasan_buang] == '9') $alasan1 = 'Satelit Rusak';
                        if ($datatransaksipermintaan[alasan_buang] == '10') $alasan1 = 'Bekas Pembuatan WE';
                        if ($datatransaksipermintaan[alasan_buang] == '11') $alasan1 = 'Reaktif Rujuk keUDDP';
                        if ($datatransaksipermintaan[alasan_buang] == '12') $alasan1 = 'Hematokrit Tinggi';
                        if ($datatransaksipermintaan[alasan_buang] == '13') $alasan1 = 'Limbah Sisa PRC';
                        if ($datatransaksipermintaan[alasan_buang] == '14') $alasan1 = 'Leukosit Tinggi';
                        if ($datatransaksipermintaan[alasan_buang] == '15') $alasan1 = 'Produk Rusak';
                        if ($datatransaksipermintaan[alasan_buang] == '16') $alasan1 = 'Produk Sampel QC';
                        if ($datatransaksipermintaan[alasan_buang] == '17') $alasan1 = 'Plasma Kuning';
                        if ($datatransaksipermintaan[alasan_buang] == '18') $alasan1 = 'Plasma Merah';
                        if ($datatransaksipermintaan[alasan_buang] == '19') $alasan1 = 'Plasma Hijau';
                        if ($datatransaksipermintaan[alasan_buang] == '20') $alasan1 = 'Selang Pendek';
                        if ($datatransaksipermintaan[alasan_buang] == '21') $alasan1 = 'Selang Merah';
                        if ($datatransaksipermintaan[alasan_buang] == '22') $alasan1 = 'Volume Lebih';
                        if ($datatransaksipermintaan[alasan_buang] == '23') $alasan1 = 'Volume Kurang';
                        if ($datatransaksipermintaan[alasan_buang] == '24') $alasan1 = 'ABS Positif';
                        if ($datatransaksipermintaan[alasan_buang] == '25') $alasan1 = 'Menggumpal';
                        if ($datatransaksipermintaan[alasan_buang] == '26') $alasan1 = 'Clot';
                        if ($datatransaksipermintaan[alasan_buang] == '27') $alasan1 = 'Jejak IMLTD Reaktif';

                        ?>

                        <td align="center"><?= $alasan1 ?></td>
                        <td align="center"><?= $datatransaksipermintaan['user'] ?></td>
                        <td align="center"><a href="pmi<?php echo $level; ?>.php?module=musnah_rpt_view&notrans=<?= $datatransaksipermintaan['notrans'] ?>"><?= $datatransaksipermintaan['notrans'] ?></a></td>

                    </tr>
                <?php $no++;
                }
            } else { ?>
                <tr>
                    <td colspan="12" align="center"><?= $alasan1 ?> Tidak Ada Data</td>
                </tr>
            <?php }
            ?>

        </table>
        <hr style="width: 100%;text-align:left;margin-left:0; line-height: 1px">
        <?php
        $sum = "select 
        CASE
        WHEN (`alasan_buang`='0') THEN 'Gagal Aftap'
        WHEN (`alasan_buang`='1') THEN 'Lysis'
        WHEN (`alasan_buang`='2') THEN 'Kadaluwarsa'
        WHEN (`alasan_buang`='3') THEN 'Plebotomi Terapi'
        WHEN (`alasan_buang`='4') THEN 'Reaktif Buang'
        WHEN (`alasan_buang`='5') THEN 'Lifemik'
        WHEN (`alasan_buang`='6') THEN 'Greyzone'
        WHEN (`alasan_buang`='7') THEN 'DCT Positif'
        WHEN (`alasan_buang`='8') THEN 'Kantong Bocor'
        WHEN (`alasan_buang`='10') THEN 'Bekas Pembuatan WE'
        WHEN (`alasan_buang`='11') THEN 'Bekas Pembuatan TPK'
        WHEN (`alasan_buang`='12') THEN 'Hematokrit Tinggi'
        WHEN (`alasan_buang`='13') THEN 'Plasma Sisa PRC'
        WHEN (`alasan_buang`='14') THEN 'Leukosit Tinggi'
        WHEN (`alasan_buang`='15') THEN 'Produk Rusak'
        WHEN (`alasan_buang`='16') THEN 'Produk Sample QC'
		WHEN (`alasan_buang`='17') THEN 'Plasma Kuning'
		WHEN (`alasan_buang`='18') THEN 'Plasma Merah'
		WHEN (`alasan_buang`='19') THEN 'Plasma Hijau'
		WHEN (`alasan_buang`='20') THEN 'Selang Pendek'
		WHEN (`alasan_buang`='21') THEN 'Selang Merah'
		WHEN (`alasan_buang`='22') THEN 'Volume Lebih'
		WHEN (`alasan_buang`='23') THEN 'Volume Kurang'
		WHEN (`alasan_buang`='24') THEN 'ABS Positif'
		WHEN (`alasan_buang`='25') THEN 'Menggumpal'
		WHEN (`alasan_buang`='26') THEN 'Clot'
		WHEN (`alasan_buang`='27') THEN 'Jejak IMLTD Reaktif'
        ELSE 'Tidak ada' end as `alasan`, count(alasan_buang) as jml from ar_stokkantong where CAST(tgl_buang as date)>='$today' and CAST(tgl_buang as date)<='$today2' $qshift $qalasan $qpetugas $qnk $qproduk $qgol $qjenis GROUP BY alasan_buang";
        // echo $sum;
        $sumq    = mysqli_query($dbi, $sum);
        $sumnum  = mysqli_num_rows($sumq);
        ?>
        <br>
        <!--input type="submit" name="submit2" value="Simpan Transaksi Pemusnahan" onclick="return confirm('PERHATIAN \n \nSimpan transaksi pemusnahan darah ini?');" class="swn_button_blue"-->

        <br>
        <div style="background-color: #ffffff;font-size:16px; color:#0f0f0f;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">
            REKAPITULASI BERDASARKAN JENIS KANTONG</div>
        <br>
        <table id="serahterima" width="50%" style="border-collapse: collapse;border: 1px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr class="field">
                <th style="height: 40px;text-align: center;font-weight: bold">No.</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jenis Kantong</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jumlah</th>
            </tr>
            <?php
            $sum_jenis = "SELECT 
        CASE jenis
            WHEN '1' THEN 'Single'
            WHEN '2' THEN 'Double'
            WHEN '3' THEN 'Triple'
            WHEN '4' THEN 'Quadruple'
            WHEN '6' THEN 'Pediatrik'
            ELSE 'Lainnya'
        END as jenis_kantong,
        COUNT(*) as jml 
        FROM ar_stokkantong 
        WHERE CAST(tgl_buang as date)>='$today' AND CAST(tgl_buang as date)<='$today2' $qshift $qalasan $qpetugas $qnk $qproduk $qgol $qjenis 
        GROUP BY jenis 
        ORDER BY jml DESC";

            $sumq_jenis = mysqli_query($dbi, $sum_jenis);
            $jml_jenis  = mysqli_num_rows($sumq_jenis);

            if ($jml_jenis > 0) {
                $nojenis = 1;
                while ($dtjenis = mysqli_fetch_assoc($sumq_jenis)) { ?>
                    <tr style="font-size: 12px;height: 30px;">
                        <td align="center"><?= $nojenis ?></td>
                        <td><?= $dtjenis['jenis_kantong'] ?></td>
                        <td align="center"><?= $dtjenis['jml'] ?></td>
                    </tr>
                <?php $nojenis++;
                }
            } else { ?>
                <tr>
                    <td colspan="3" align="center" style="height: 40px;">Tidak Ada Data</td>
                </tr>
            <?php } ?>
        </table>

        <br><br>

        <div style="background-color: #ffffff;font-size:16px; color:#0f0f0f;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">
            REKAPITULASI BERDASARKAN GOLONGAN DARAH</div>
        <br>
        <table id="serahterima" width="50%" style="border-collapse: collapse;border: 1px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr class="field">
                <th style="height: 40px;text-align: center;font-weight: bold">No.</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Golongan Darah</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jumlah</th>
            </tr>
            <?php
            $sum_gol = "SELECT 
        CONCAT(gol_darah, '(', RhesusDrh, ')') as golongan, 
        COUNT(*) as jml 
        FROM ar_stokkantong 
        WHERE CAST(tgl_buang as date)>='$today' AND CAST(tgl_buang as date)<='$today2' $qshift $qalasan $qpetugas $qnk $qproduk $qgol $qjenis 
        GROUP BY gol_darah, RhesusDrh 
        ORDER BY jml DESC";

            $sumq_gol = mysqli_query($dbi, $sum_gol);
            $jml_gol  = mysqli_num_rows($sumq_gol);

            if ($jml_gol > 0) {
                $nogol = 1;
                while ($dtgol = mysqli_fetch_assoc($sumq_gol)) { ?>
                    <tr style="font-size: 12px;height: 30px;">
                        <td align="center"><?= $nogol ?></td>
                        <td><?= $dtgol['golongan'] ?></td>
                        <td align="center"><?= $dtgol['jml'] ?></td>
                    </tr>
                <?php $nogol++;
                }
            } else { ?>
                <tr>
                    <td colspan="3" align="center" style="height: 40px;">Tidak Ada Data</td>
                </tr>
            <?php } ?>
        </table>

        <div style="background-color: #ffffff;font-size:16px; color:#0f0f0f;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">
            REKAPITULASI PEMUSNAHAN PRODUK</div>
        <br>
        <table id="serahterima" width="50%" style="border-collapse: collapse;border: 1px solid #808080;box-shadow: 1px 2px 2px #000000;">
            <tr class="field">
                <th style="height: 40px;text-align: center;font-weight: bold">No.</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Pemusnahan Produk Darah</th>
                <th style="height: 40px;text-align: center;font-weight: bold">Jumlah</th>
            </tr>
            <?php
            $nosum = 1;
            if ($sumnum > 0) {
                while ($dtsum = mysqli_fetch_assoc($sumq)) { ?>
                    <tr style="font-size: 12px;height: 30px;">
                        <td><?php echo $nosum; ?></td>
                        <td><?php echo $dtsum['alasan']; ?></td>
                        <td><?php echo $dtsum['jml']; ?></td>
                    </tr>
                <?php $nosum++;
                }
            } else { ?>
                <tr>
                    <td colspan="3" align="center">Tidak Ada Data</td>
                </tr>
            <?php }
            ?>
        </table>
    </form>
    <div style="font-size:10px; color:#000000; font-family: Helvetica Neue, Helvetica, Arial, sans-serif;"><a href="" onclick=window.print()>Build : 21-08-2024</a></div>