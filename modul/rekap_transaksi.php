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

<form name="dinstansi" method="POST" action="<?php echo $PHPSELF; ?>">
    <table class="form" cellspacing="0" cellpadding="0">
        <tr>
            <td>Bulan Transaksi : </td>
            <td>
                <input class="input" name="waktu" id="datepicker" type="text" size="10" autocomplete="off"> Sampai
                <input class="input" name="waktu1" id="datepicker1" type="text" size="10" autocomplete="off">
            </td>
        </tr>
    </table>
    <input type="submit" name="submit" value="Search">
</form>

<?php
function count_donor($start, $end, $instansi, $pengambilan = null, $gol = null, $rhesus = null, $jk = null)
{
      $sql = "SELECT COUNT(DISTINCT KodePendonor) AS jml
            FROM htransaksi
            WHERE CAST(Tgl AS DATE) >= '" . $start . "'
              AND CAST(Tgl AS DATE) <= '" . $end . "'
              AND Instansi = '" . mysql_real_escape_string($instansi) . "'";

      if ($pengambilan !== null) $sql .= " AND Pengambilan = '" . $pengambilan . "'";
      if ($gol !== null)         $sql .= " AND gol_darah = '" . $gol . "'";
      if ($rhesus !== null)      $sql .= " AND rhesus = '" . $rhesus . "'";
      if ($jk !== null)          $sql .= " AND jk = '" . $jk . "'";

      $q = mysql_query($sql);
      $r = mysql_fetch_assoc($q);

      return (int)$r['jml'];
}

if (isset($_POST['submit'])) {
      $waktu  = $_POST['waktu'];
      $waktu1 = $_POST['waktu1'];

      $tglA = explode('-', $waktu);
      $tglB = explode('-', $waktu1);

      $perthn  = $tglA[0];
      $perbln  = $tglA[1];
      $pertgl  = $tglA[2];

      $perthn1 = $tglB[0];
      $perbln1 = $tglB[1];
      $pertgl1 = $tglB[2];

      $statusList = array(
            '0' => 'berhasil',
            '2' => 'gagal',
            '1' => 'batal'
      );

      $golList = array('A', 'B', 'AB', 'O', 'X');

      $tgldonor0 = mysql_query("
        SELECT CAST(Tgl AS DATE) AS tgl, Instansi
        FROM htransaksi
        WHERE  instansi != ''
          AND CAST(Tgl AS DATE) >= '" . $waktu . "'
          AND CAST(Tgl AS DATE) <= '" . $waktu1 . "'
        GROUP BY Instansi
        ORDER BY Tgl
    ");
?>
<h1 class="table">
    Periode <?php echo $pertgl; ?> - <?php echo $perbln; ?> - <?php echo $perthn; ?> sampai dengan
    <?php echo $pertgl1; ?> - <?php echo $perbln1; ?> - <?php echo $perthn1; ?>
</h1>

<table class="form" border="1" cellpadding="0" cellspacing="0">
    <tr>
        <td rowspan="4" align="center">No</td>
        <td rowspan="4" align="center">TGL DONOR</td>
        <td rowspan="4" align="center">NAMA INSTANSI</td>
        <td colspan="10" align="center">DONOR BERHASIL</td>
        <td colspan="10" align="center">DONOR GAGAL</td>
        <td colspan="10" align="center">DONOR BATAL</td>
        <td rowspan="4" align="center">JML <br>PENDONOR</td>
    </tr>
    <tr>
        <td colspan="7">DARAH</td>
        <td colspan="2" rowspan="2" align="center">JK</td>
        <td rowspan="3" align="center">JML</td>

        <td colspan="7" align="center">DARAH</td>
        <td colspan="2" rowspan="2" align="center">JK</td>
        <td rowspan="3" align="center">JML</td>

        <td colspan="7" align="center">DARAH</td>
        <td colspan="2" rowspan="2" align="center">JK</td>
        <td rowspan="3" align="center">JML</td>
    </tr>
    <tr>
        <td colspan="5" align="center">GOLONGAN</td>
        <td colspan="2" align="center">RHESUS</td>

        <td colspan="5" align="center">GOLONGAN</td>
        <td colspan="2" align="center">RHESUS</td>

        <td colspan="5" align="center">GOLONGAN</td>
        <td colspan="2" align="center">RHESUS</td>
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
    <?php
            $no = 1;

            while ($tgldonor = mysql_fetch_assoc($tgldonor0)) {
                  $tanggal = date('d-m-Y', strtotime($tgldonor['tgl']));
                  $instansi = $tgldonor['Instansi'];

                  $hasil = array();

                  foreach ($statusList as $pengambilan => $label) {
                        $hasil[$label] = array(
                              'gol' => array(),
                              'rhesus' => array(),
                              'jk' => array(),
                              'total' => 0
                        );

                        foreach ($golList as $gol) {
                              $hasil[$label]['gol'][$gol] = count_donor($waktu, $waktu1, $instansi, $pengambilan, $gol);
                        }

                        $hasil[$label]['rhesus']['+'] = count_donor($waktu, $waktu1, $instansi, $pengambilan, null, '+');
                        $hasil[$label]['rhesus']['-'] = count_donor($waktu, $waktu1, $instansi, $pengambilan, null, '-');
                        $hasil[$label]['jk']['0'] = count_donor($waktu, $waktu1, $instansi, $pengambilan, null, null, '0');
                        $hasil[$label]['jk']['1'] = count_donor($waktu, $waktu1, $instansi, $pengambilan, null, null, '1');
                        $hasil[$label]['total']   = count_donor($waktu, $waktu1, $instansi, $pengambilan);
                  }

                  $jumtot = count_donor($waktu, $waktu1, $instansi);
            ?>
    <tr class="record">
        <td class="input"><?php echo $no++; ?></td>
        <td class="input"><?php echo $tanggal; ?></td>
        <td class="input"><?php echo $instansi; ?></td>

        <?php foreach ($statusList as $pengambilan => $label) { ?>
        <?php foreach ($golList as $gol) { ?>
        <td class="input"><?php echo $hasil[$label]['gol'][$gol]; ?></td>
        <?php } ?>
        <td class="input"><?php echo $hasil[$label]['rhesus']['+']; ?></td>
        <td class="input"><?php echo $hasil[$label]['rhesus']['-']; ?></td>
        <td class="input"><?php echo $hasil[$label]['jk']['0']; ?></td>
        <td class="input"><?php echo $hasil[$label]['jk']['1']; ?></td>
        <td class="input"><?php echo $hasil[$label]['total']; ?></td>
        <?php } ?>

        <td class="input"><?php echo $jumtot; ?></td>
    </tr>
    <?php
            }
            ?>
</table>

<br />

<form name="xls" method="post" action="modul/rekap_transaksi_xls.php">
    <input type="hidden" name="pertgl" value="<?php echo $pertgl; ?>">
    <input type="hidden" name="perbln" value="<?php echo $perbln; ?>">
    <input type="hidden" name="perthn" value="<?php echo $perthn; ?>">
    <input type="hidden" name="pertgl1" value="<?php echo $pertgl1; ?>">
    <input type="hidden" name="perbln1" value="<?php echo $perbln1; ?>">
    <input type="hidden" name="perthn1" value="<?php echo $perthn1; ?>">
    <input type="hidden" name="waktu" value="<?php echo $waktu; ?>">
    <input type="hidden" name="waktu1" value="<?php echo $waktu1; ?>">
    <input type="submit" name="submit2" value="Print Rekap Transaksi Donor MU (.XLS)">
</form>
<?php } ?>