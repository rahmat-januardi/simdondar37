<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_lahir_minta.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />


<?php
include('clogin.php');
include('config/db_connect.php');
$utd		= mysql_fetch_assoc(mysql_query("select * from utd where aktif=1"));
$namauser   = $_SESSION[namauser];
$namabagian = $_SESSION[namabagian];
$levelusr   = $_SESSION[leveluser];

$today = date("Y-m-d");

$notrans	= $_GET['id'];
$utd		= mysql_fetch_assoc(mysql_query("select * from utd where aktif=1"));
$petugas	= mysql_fetch_assoc(mysql_query("select * from petugasmu where NoTrans='$notrans' order by jabatan asc"));
$kegiatan	= mysql_fetch_assoc(mysql_query("select * from kegiatan where NoTrans='$notrans'"));
$cp		= mysql_fetch_assoc(mysql_query("select * from detailinstansi where KodeDetail='$kegiatan[kodeinstansi]'"));
$namalkp	= mysql_fetch_assoc(mysql_query("select * from user where id_user='$namauser'"));
$ptgs = mysql_query("SELECT * from petugasmu where NoTrans='$_GET[id]' order by jabatan asc");
$tgl		= date("d F Y", strtotime($kegiatan[TglPenjadwalan]));

function hari($hari)
{
    switch ($hari) {
        case 0: $hari = "Minggu";
            break;
        case 1: $hari = "Senin";
            break;
        case 2: $hari = "Selasa";
            break;
        case 3: $hari = "Rabu";
            break;
        case 4: $hari = "Kamis";
            break;
        case 5: $hari = "Jum'at";
            break;
        case 6: $hari = "Sabtu";
            break;
    }
    return $hari;
}
$day = date("w", strtotime($kegiatan[TglPenjadwalan])); //menampilkan angka hari yang akan diterjemahkan oleh fungsi hari dalam file hari.php
?>

<table width="750px">
    <tr>
        <td align=right>No. Dokumen : ______________
            <br>No. Form : __________________
        </td>
        <td align=center></td>
    </tr>
</table>
<table width="750px">
    <tr>
        <td align="center">
            <div style="display:inline-block; text-align:left;">
                <div style="text-align:center;">
                    <strong><u>
                            <font size="4"><b>SURAT TUGAS MOBILE UNIT</b></font>
                        </u></strong>
                </div>
                <div>
                    No :
                </div>
            </div>
        </td>
    </tr>
</table>
<font size="3">
    <br>Yang bertanda tangan di bawah ini, memberikan tugas sebagai Tim Mobile Unit <?=$utd[nama]?>
    <br>kepada :
</font>
<font size="3">
    <table width="750px">
        <tr>
            <th width="100"></th>
            <th width="10"></th>
            <th width="400"></th>
            <th width="100"></th>
            <th width="10"></th>
            <th width="200"></th>
        </tr>
        <tr>
            <td align="left">TANGGAL</td>
            <td>:</td>
            <td><?=hari($day).", ".$tgl?></td>
            <td align="left">BERANGKAT</td>
            <td>:</td>
            <td><?=$kegiatan[jammulai]?></td>
        </tr>
        <tr>
            <td align="left">INSTANSI</td>
            <td>:</td>
            <td><?=$cp[nama]?></td>
            <td align="left">PULANG</td>
            <td>:</td>
            <td>____________</td>
        </tr>
        <tr>
            <td align="left">ALAMAT</td>
            <td>:</td>
            <td><?=$cp[alamat]?></td>
            <td align="left">RENCANA</td>
            <td>:</td>
            <td><?=$kegiatan[jumlah]?> Kolf</td>
        </tr>
        <tr>
            <td align="left">CP</td>
            <td>:</td>
            <td><?=$kegiatan[cp].", ".$kegiatan[hpcp]?></td>
            <td align="left">HASIL</td>
            <td>:</td>
            <td>____ Kolf</td>
        </tr>
        <tr>
            <td align="left"></td>
            <td></td>
            <td></td>
            <td align="left">DITOLAK</td>
            <td>:</td>
            <td>____ Kolf</td>
        </tr>
    </table>
    <br><b>PERALATAN</b> (SESUAI CHECKLIST)
    &nbsp;&nbsp;&nbsp;<b>BERANGKAT</b> : LENGKAP/TIDAK
    &nbsp;&nbsp;&nbsp;<b>PULANG</b> : LENGKAP/TIDAK

    <table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="750px">
        <tr>
            <th width="10">NO.</th>
            <th>NAMA PETUGAS</th>
            <th>JABATAN</th>
            <!-- <th>TERLAMBAT/TDK</th> -->
            <th>KETERANGAN DINAS</th>
            <!--th >Edit</th-->
            <?php
    while ($a_ptgs = mysql_fetch_assoc($ptgs)) {
        $no++;
        if ($a_ptgs[jabatan] == '1') {
            $jk = 'Dokter';
        }
        if ($a_ptgs[jabatan] == '2') {
            $jk = 'PJMU';
        }
        if ($a_ptgs[jabatan] == '3') {
            $jk = 'Aftap';
        }
        if ($a_ptgs[jabatan] == '4') {
            $jk = 'HB/Tensi';
        }
        if ($a_ptgs[jabatan] == '5') {
            $jk = 'Admin';
        }
        if ($a_ptgs[jabatan] == '6') {
            $jk = 'Driver';
        }
        if ($a_ptgs[jabatan] == '7') {
            $jk = 'Lain-lain';
        }

        echo "<tr>";
        echo "<td >".$no."</td>".
            "<td>".$a_ptgs[nama]."</td>".
            "<td>".$jk."</td>".
                // "<td>".$a_dr[ket]."</td>".
            "<td>".$a_dr[ket]."</td>";
        /*echo "<td>
            <a href=\"".$PHP_SELF."?module=data_jadwal_mobile&aksi=hapus&id=".$a_dr[NoTrans]."\">
                   <li class=\"ui-state-default ui-corner-all\" title=\"Hapus\">
                   <span class=\"ui-icon ui-icon-closethick\"></span></li>
                   </a>";
        echo "<td>";*/


        echo "</tr>";
    }?>

    </table>
    <br>
    <table width="750px">
        <tr>
            <td width="500" align="left">
                <b>Keterangan :</b>
                <br>Yang bersangkutan telah datang untuk keperluan dinas,
                <br>Pada Tanggal&nbsp;:
                <br>Jam Datang&nbsp;&nbsp;&nbsp;:
                <br>Jam Kembali&nbsp;&nbsp;:
                <br>Jabatan dan tanda tangan pegawai di tempat tujuan
                <br>dibubuhi cap jabatan
                <br>Penanggung Jawab :
                <br>
                <br>
                <br>_________________
            </td>
            <td width="250" align="center"><?=$utd[nama]?>
                <br>Humas/Rekrutmen
                <br>
                <br>
                <br> ___________________________
                <br>
            </td>
        </tr>

    </table>



    <!--a href="jadwal_mu.php">
<button onClick="window.print();">Cetak</button>
</a-->
    <table>
        <?$waktu=date('d/m/Y');?>
        <font size="1">
            <td align="right"><a href="javascript:window.print()">Dicetak : <?=$waktu?></a></td>
        </font>
    </table>
    <!--tfoot>
<table width=750px>
  <tr>
    <?$waktu=date('d/m/Y');?>
    <td align=right><a href="javascript:window.print()"><?=$waktu?></a></td>
    <td align=center></td>
    </tr>
</table>
</tfoot-->