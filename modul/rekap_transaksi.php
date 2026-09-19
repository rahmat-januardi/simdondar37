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

<h1 class="table">Rekap Transaksi Donor Darah MOBIL UNIT</h1>
<form name="dinstansi" method="POST" action="<? echo $PHPSELF ?>">
    <table class="form" cellspacing="0" cellpadding="0">
        <tr>
            <td>Bulan Transaksi : </td>
            <td>
                <input class=input name="waktu" id="datepicker" type=text size=10 autocomplete=off> Sampai
                <input class=input name="waktu1" id="datepicker1" type=text size=10 autocomplete=off>
            </td>
        </tr>
    </table>
    <input type=submit name=submit value="Search">
</form>
<? if (isset($_POST['submit'])) {
      $waktu = isset($_POST['waktu']) ? trim($_POST['waktu']) : '';
      $waktu1 = isset($_POST['waktu1']) ? trim($_POST['waktu1']) : '';

      $perbln = substr($waktu, 5, 2);
      $pertgl = substr($waktu, 8, 2);
      $perthn = substr($waktu, 0, 4);

      $perbln1 = substr($waktu1, 5, 2);
      $pertgl1 = substr($waktu1, 8, 2);
      $perthn1 = substr($waktu1, 0, 4);

      $waktu_sql = mysql_real_escape_string($waktu);
      $waktu1_sql = mysql_real_escape_string($waktu1);
?>
<h1 class="table">Periode <?= $pertgl ?> - <?= $perbln ?> - <?= $perthn ?> sampai dengan
    <?= $pertgl1 ?> - <?= $perbln1 ?> - <?= $perthn1 ?></h1>
<table class=form border=1 cellpadding=0 cellspacing=0>
    <tr>
        <td rowspan='4' align=center>No</td>
        <td rowspan='4' align="center">TGL DONOR</td>
        <td rowspan='4' align="center">NAMA INSTANSI</td>
        <td colspan='10' align=center>DONOR BERHASIL</td>
        <td colspan='10' align=center>DONOR GAGAL</td>
        <td colspan='10' align=center>DONOR BATAL</td>
        <td rowspan='4' align=center>JML <br>PENDONOR</td>
    </tr>
    <tr>
        <td colspan='7'>DARAH</td>
        <td colspan='2' rowspan='2' align="center">JK</td>
        <td rowspan='3' align="center">JML</td>

        <td colspan='7' align="center">DARAH</td>
        <td colspan='2' rowspan='2' align="center">JK</td>
        <td rowspan='3' align="center">JML</td>

        <td colspan='7' align="center">DARAH</td>
        <td colspan='2' rowspan='2' align="center">JK</td>
        <td rowspan='3' align="center">JML</td>
    </tr>
    <tr>
        <td colspan='5' align="center">GOLONGAN</td>
        <td colspan='2' align="center">RHESUS</td>

        <td colspan='5' align="center">GOLONGAN</td>
        <td colspan='2' align="center">RHESUS</td>

        <td colspan='5' align="center">GOLONGAN</td>
        <td colspan='2' align="center">RHESUS</td>
    </tr>

    <tr>
        <td>A</td>
        <td>B</td>
        <td>AB</td>
        <td>O</td>
        <td>X</td>
        <td>POS</td>
        <td>NEG</td>
        <td>Laki<br>laki</td>
        <td>Perem<br>puan</td>

        <td>A</td>
        <td>B</td>
        <td>AB</td>
        <td>O</td>
        <td>X</td>
        <td>POS</td>
        <td>NEG</td>
        <td>Laki<br>laki</td>
        <td>Perem<br>puan</td>

        <td>A</td>
        <td>B</td>
        <td>AB</td>
        <td>O</td>
        <td>X</td>
        <td>POS</td>
        <td>NEG</td>
        <td>Laki<br>laki</td>
        <td>Perem<br>puan</td>

    </tr>
    <?
            $no = 1;
            $query = "SELECT
                        MIN(CAST(Tgl AS DATE)) AS tgl,
                        Instansi,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND gol_darah='A' THEN KodePendonor END) AS berhasil_a,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND gol_darah='B' THEN KodePendonor END) AS berhasil_b,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND gol_darah='AB' THEN KodePendonor END) AS berhasil_ab,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND gol_darah='O' THEN KodePendonor END) AS berhasil_o,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND gol_darah='X' THEN KodePendonor END) AS berhasil_x,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND rhesus='+' THEN KodePendonor END) AS berhasil_pos,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND rhesus='-' THEN KodePendonor END) AS berhasil_neg,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND jk='0' THEN KodePendonor END) AS berhasil_laki,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' AND jk='1' THEN KodePendonor END) AS berhasil_perem,
                        COUNT(DISTINCT CASE WHEN Pengambilan='0' THEN KodePendonor END) AS berhasil_total,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND gol_darah='A' THEN KodePendonor END) AS gagal_a,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND gol_darah='B' THEN KodePendonor END) AS gagal_b,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND gol_darah='AB' THEN KodePendonor END) AS gagal_ab,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND gol_darah='O' THEN KodePendonor END) AS gagal_o,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND gol_darah='X' THEN KodePendonor END) AS gagal_x,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND rhesus='+' THEN KodePendonor END) AS gagal_pos,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND rhesus='-' THEN KodePendonor END) AS gagal_neg,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND jk='0' THEN KodePendonor END) AS gagal_laki,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' AND jk='1' THEN KodePendonor END) AS gagal_perem,
                        COUNT(DISTINCT CASE WHEN Pengambilan='2' THEN KodePendonor END) AS gagal_total,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND gol_darah='A' THEN KodePendonor END) AS batal_a,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND gol_darah='B' THEN KodePendonor END) AS batal_b,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND gol_darah='AB' THEN KodePendonor END) AS batal_ab,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND gol_darah='O' THEN KodePendonor END) AS batal_o,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND gol_darah='X' THEN KodePendonor END) AS batal_x,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND rhesus='+' THEN KodePendonor END) AS batal_pos,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND rhesus='-' THEN KodePendonor END) AS batal_neg,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND jk='0' THEN KodePendonor END) AS batal_laki,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' AND jk='1' THEN KodePendonor END) AS batal_perem,
                        COUNT(DISTINCT CASE WHEN Pengambilan='1' THEN KodePendonor END) AS batal_total
                      FROM htransaksi
                      WHERE NoTrans LIKE 'M%' AND Instansi != ''
                        AND CAST(Tgl AS DATE) >= '$waktu_sql' AND CAST(Tgl AS DATE) <= '$waktu1_sql'
                      GROUP BY Instansi
                      ORDER BY MIN(CAST(Tgl AS DATE)), Instansi";

            $tgldonor0 = mysql_query($query);
            while ($tgldonor = mysql_fetch_assoc($tgldonor0)) {
                  $tanggal = !empty($tgldonor['tgl']) ? date('d-m-Y', strtotime($tgldonor['tgl'])) : '-';
            ?>
    <tr class="record">
        <td class=input><?= $no++ ?></td>
        <td class=input><?= $tanggal ?></td>
        <td class=input><?= $tgldonor['Instansi'] ?></td>
        <td class=input><?= intval($tgldonor['berhasil_a']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_b']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_ab']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_o']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_x']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_pos']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_neg']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_laki']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_perem']) ?></td>
        <td class=input><?= intval($tgldonor['berhasil_total']) ?></td>

        <td class=input><?= intval($tgldonor['gagal_a']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_b']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_ab']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_o']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_x']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_pos']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_neg']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_laki']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_perem']) ?></td>
        <td class=input><?= intval($tgldonor['gagal_total']) ?></td>

        <td class=input><?= intval($tgldonor['batal_a']) ?></td>
        <td class=input><?= intval($tgldonor['batal_b']) ?></td>
        <td class=input><?= intval($tgldonor['batal_ab']) ?></td>
        <td class=input><?= intval($tgldonor['batal_o']) ?></td>
        <td class=input><?= intval($tgldonor['batal_x']) ?></td>
        <td class=input><?= intval($tgldonor['batal_pos']) ?></td>
        <td class=input><?= intval($tgldonor['batal_neg']) ?></td>
        <td class=input><?= intval($tgldonor['batal_laki']) ?></td>
        <td class=input><?= intval($tgldonor['batal_perem']) ?></td>
        <td class=input><?= intval($tgldonor['batal_total']) ?></td>

        <td class=input><?= intval($tgldonor['berhasil_total'] + $tgldonor['gagal_total'] + $tgldonor['batal_total']) ?>
        </td>
    </tr>
    <?
            }

            ?>
</table>
</br>
<form name=xls method=post action=modul/rekap_transaksi_xls.php>
    <input type=hidden name=pertgl value='<?= $pertgl ?>'>
    <input type=hidden name=perbln value='<?= $perbln ?>'>
    <input type=hidden name=perthn value='<?= $perthn ?>'>
    <input type=hidden name=pertgl1 value='<?= $pertgl1 ?>'>
    <input type=hidden name=perbln1 value='<?= $perbln1 ?>'>
    <input type=hidden name=perthn1 value='<?= $perthn1 ?>'>
    <input type=hidden name=waktu value='<?= $_POST[waktu] ?>'>
    <input type=hidden name=waktu1 value='<?= $_POST[waktu1] ?>'>
    <input type=submit name=submit2 value='Print Rekap Transaksi Donor MU (.XLS)'>
</form>
<?
}
?>