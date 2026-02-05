<?
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
?>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />

<style>
    #serahterima {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 16px;
        border-collapse: collapse;
    }

    #serahterima td, #serahterima th {
        border: 1px solid #ddd;
        padding: 5px;
    }

    #serahterima tr:nth-child(even){background-color: #ffe6e6;}

    #serahterima tr:hover {background-color: #ddd;}

    #serahterima th {
        padding-top: 3px;
        padding-bottom: 3px;
        text-align: left;
        font-weight: lighter;
        background-color: #ff9999;
        color: #000000;
    }
    #serahterima input{
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: left;
        background-color: lightyellow;
        color: #000000;
    }
</style>

<?php
include ('config/dbi_connect.php');
$namauser       = $_SESSION['namauser'];
$namalengkap    = $_SESSION['nama_lengkap'];
$level	        = $_SESSION['leveluser'];
$tglawal        = date("Y-m-d");
$hariini        = date("Y-m-d");
$notransaksi="";
?>

<body>
<a name="atas" id="atas"></a>
<div style="background-color: #ffffff;font-size:24px; color:#0099ff;text-shadow: 1px 1px 1px #000000; font-family:Verdana;">DAFTAR TRANSAKSI PEMUSNAHAN PRODUK DARAH</div>

<br>
<?php
if (isset($_POST[waktu])) {$tglawal=$_POST[waktu];$hariini=$hariini;}
if ($_POST[waktu1]!='') $hariini=$_POST[waktu1];

if ($_POST['shift']!='') {
                $srcshift  = $_POST['shift'];
                $qshift       = " AND shift = '$srcshift' ";
                            } else {$qshift    ="";}
if ($_POST['transaksimusnah']!='') {
                $srctrans    = $_POST['transaksimusnah'];
                $qtrans       = " AND notrans = '$srctrans' ";
                } else {$qtrans    ="";}
if ($_POST['pengambilan']!='') {
                $srcpengambilan    = $_POST['pengambilan'];
                $qambil       = " AND stat = '$srcpengambilan' ";
                } else {$qtrans    ="";}
                
?>
<form name="cari" method="POST" action="<?echo $PHPSELF?>">
    <table id="serahterima" cellpadding=1 cellspacing="0" border="0">
        <tr class="field">
            <td align="left" nowrap>Dari tanggal :</td>
            <td><input name="waktu" id="datepicker"  value="<?=$tglawal?>" type=text size=10></td>
            <td align="right" nowrap>sampai tanggal :</td>
            <td><input name="waktu1" id="datepicker1" value="<?=$hariini?>" type=text size=10></td>
            <td align="right" nowrap>Shift :</td>
            <td><select name="shift">
                <option value="">Semua</option>
                <option value="I">I</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
            </td>
            <td align="right" nowrap>Status :</td>
            <td><select name="pengambilan">
                <option value="">Semua</option>
                <option value="0">Belum Diambil</option>
                <option value="1">Sudah Diambil</option>
            </td>
           
            <td align="right" nowrap>No. Transaksi :</td>
            <td><input name="transaksimusnah" type="text"></td>
            <td><input type=submit name=submit class="swn_button_blue" value="Ok"></td>
        </tr>
    </table>
</form>

    <table id="serahterima" width="100%" style="border-collapse: collapse;border: 2px solid #808080;box-shadow: 1px 2px 2px #000000;">
        <tr style="font-size: 12px">
            <th  style="height: 40px;text-align: center;font-weight: bold">No</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">No. Transaksi</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">Aksi</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">Tanggal</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">Shift</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">Petugas Pemusnahan</th>
	    <th  style="height: 40px;text-align: center;font-weight: bold">Lampiran Berita Acara</th>

            
            <th  style="height: 40px;text-align: center;font-weight: bold">Instansi<br>Pengelola Limbah</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">Petugas<br>Pengelola</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">Status</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">Jumlah Produk</th>
            <th  style="height: 40px;text-align: center;font-weight: bold">Berat (Kg)</th>
            
        </tr>
        
        <?php
        $no=0;
        $qry    = "SELECT * from ar_stokkantong_trans WHERE  (DATE(`tgl`)>='$tglawal' and date(`tgl`)<='$hariini') $qshift $qambil $qtrans ORDER BY `tgl` ASC";
		//echo "$qry";
        $sql    = mysqli_query($dbi, $qry);
        $no=0;
	    $jmltotal=0;
        $berat=0.00;
        while ($tmp = mysqli_fetch_assoc($sql)){
            $no++;
            $script     = "SELECT count(`notrans`) as jmltrans from `ar_stokkantong` where `notrans`='$tmp[notrans]'";
            $jmltrans   = mysqli_fetch_assoc(mysqli_query($dbi, $script));
            //echo $script;

            //Status Diambil atau Belum
            switch ($tmp['stat']) {
                case '1':$ckt_status="Sudah Diambil"; $bg = "bgcolor='green'";break;
                default : $ckt_status="Belum Diambil"; $bg = "bgcolor='red'";
                break;
            }

            //Nama PT Pengelola Limbah
            $PT =  mysqli_fetch_assoc(mysqli_query($dbi,"SELECT Nama from `supplier` where `Kode`='$tmp[pengelola]'"));
            if ($PT['Nama'] ==""){$PT = "-";}else{$PT = $PT['Nama'];}
            
            ?>
            <tr style="font-size: 12px;height: 30px;">
                <td align="right"><?=$no?>.</td>
                <td style="text-align: center"><?=$tmp['notrans']?></td>
                <td style="text-align: left">
                    <?php if($tmp['stat'] == "0"){?>
                        <a href="pmi<?php echo $level;?>.php?module=musnah_serahterima&no=<?=$tmp['notrans']?>"><input type="button"  class="swn_button_red" onclick="return confirm('Proses Pengiriman Limbah Produk <?=$tmp['notrans']?> ?')" value="Proses"/></a> &nbsp;
                        <!--a href="pmi<?php echo $level;?>.php?module=sr_komponen&no=<?=$tmp['hst_notrans']?>">PROSES</a--> 
                    <?php } ?>
                    <a href="pmi<?php echo $level;?>.php?module=musnah_cetakberita&notrans=<?php echo $tmp['notrans'];?>"><input type="button"  class="swn_button_blue"  value="Cetak"/></a> &nbsp;
                    <!--a href="pmi<?php echo $level;?>.php?module=musnah_cetakberita&notrans=<?php echo $tmp['notrans'];?>">CETAK</a--> 
                    <a href="pmi<?php echo $level;?>.php?module=musnah_rpt_view&notrans=<?=$tmp['notrans']?>"><input type="button"  class="swn_button_green"  value="Lihat"/></a> &nbsp;
                    <!--a href="pmi<?php echo $level;?>.php?module=musnah_rpt_view&notrans=<?=$tmp['notrans']?>">LIHAT</a-->
                </td>
                <td style="text-align: center"><?=$tmp['tgl']?></td>
                <td style="text-align: center"><?=$tmp['shift']?></td>
                <td style="text-align: center"><?=$tmp['ptgs_musnah']?></td>
		<td style="text-align: center"><?php
if (empty($tmp['file_berita_acara'])) {
    echo '-';
} else {
    $file = $tmp['file_berita_acara'];
    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $url  = 'musnah/file_berita_acara/' . $file;

    echo '<a href="' . $url . '" target="_blank">';

    if (in_array($ext, array('jpg','jpeg','png'))) {
        echo 'Lihat Gambar';
    } else {
        echo 'Lihat PDF';
    }

    echo '</a>';
}
?>
				
                <td style="text-align: center"><?=$PT?></td>
                <td style="text-align: center"><?=$tmp['ptgs_limbah']?></td>
                <td style="text-align: center; color: white;" <?php echo $bg;?>><?=$ckt_status?></td>
                <td style="text-align: right"><b><?=$jmltrans['jmltrans']?></b></td>
                <td style="text-align: right"><?=$tmp['berat']?></td>
                
            </tr>
            <?php
	    $jmltotal   =$jmltotal+$jmltrans['jmltrans'];
        $brtotal    =$berat+$tmp['berat'];
        }
         if ($no=="0"){?>
                <tr style="font-size: 16px;height: 40px; text-align: center;">
                        <td colspan="11">Tidak ada data</td>
                </tr>
            <?php
        } ?>
        <tr style="font-size: 12px;height: 40px; text-align: center;">
            <td style="text-align: right" colspan="10"><b>TOTAL</b></td>
            <td style="text-align: right"><b><?=$jmltotal?></b></td>
            <td style="text-align: right"><b><?=$brtotal?></b></td>
        </tr>
    </table>
<br>
<div style="font-size:10px; color:#000000; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;">Build : 21-08-2024</div>
