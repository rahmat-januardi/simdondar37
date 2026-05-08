<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<!-- <link href="css/style.css" rel="stylesheet" type="text/css" /> -->
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script language="javascript" src="modul/thickbox/thickbox.js"></script>
<link href="modul/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
<style type="text/css">
  @media print {
    .hidden-print {
      display: none !important;
    }
  }

  .border-table-color {
    border: 1px solid #000 !important;
    border-collapse: collapse !important;
    padding: 2px;
  }

  table.list tr.field {
    background-color: #ffaeae;
    color: #000;
  }

  a {
    text-decoration: none;
  }
</style>
<?
include('clogin.php');
include('config/db_connect.php');
?>
<h3 class="list">Rekap IMLTD NAT Reaktif </h3>
<form name="cari" method="POST" action="<? echo $PHPSELF ?>" class="hidden-print">
  <table>
    <tr>
      <td>
        Instansi :
      </td>
      <td>
        <input name="instansi" id="instansi" type=text size=35 autocomplete=off>
      </td>
    </tr>
    <tr>
      <td>
        Pilih Periode :
      </td>
      <td>
        <input name="waktu" id="datepicker" type=text size=10 autocomplete=off required>
        Sampai Dengan
        <input name="waktu1" id="datepicker1" type=text size=10 autocomplete=off required>
      </td>
      <td>
        <input id="submit" type=submit name=submit value="Submit">
      </td>
    </tr>
  </table>
</form>
<? if (isset($_POST[submit])) {
  $namauser = $_SESSION[namauser];
  $perbln = substr($_POST[waktu], 5, 2);
  $pertgl = substr($_POST[waktu], 8, 2);
  $perthn = substr($_POST[waktu], 0, 4);

  $perbln1 = substr($_POST[waktu1], 5, 2);
  $pertgl1 = substr($_POST[waktu1], 8, 2);
  $perthn1 = substr($_POST[waktu1], 0, 4);
?>
  <h3 class="list">Hasil Pemeriksaan dari Tgl : <?= $pertgl ?> - <?= $perbln ?> - <?= $perthn ?> s/d Tgl: <?= $pertgl1 ?> - <?= $perbln1 ?> - <?= $perthn1 ?> / <?= $_POST[instansi] ? $_POST[instansi] : 'SEMUA INSTANSI' ?> </h3>
  <!--form rekap-->
  <?
  // $hasil = mysql_query("select dr.noKantong as nk,dr.tglPeriksa as tgl,dr.jenisPeriksa,sk.tgl_Aftap as ta,sk.gol_darah as gd,sk.RhesusDrh as rh,sk.statKonfirmasi as kon from hasilelisa as dr,stokkantong as sk where dr.hasil='1' and dr.tglPeriksa>='$_POST[waktu]' and dr.tglPeriksa<='$_POST[waktu1]' and sk.noKantong=dr.noKantong group by dr.nokantong order by tgl_Aftap ASC");
  $hasil = mysql_query("SELECT
	dr.noKantong AS nk,
	dr.tglPeriksa AS tgl,
	dr.jenisPeriksa,
	sk.tgl_Aftap AS ta,
	sk.gol_darah AS gd,
	sk.RhesusDrh AS rh,
	sk.statKonfirmasi AS kon
FROM
	hasilelisa AS dr
	JOIN stokkantong AS sk ON sk.noKantong = dr.noKantong
WHERE
  dr.tglPeriksa BETWEEN '$_POST[waktu]' AND '$_POST[waktu1]'
GROUP BY
	dr.nokantong
ORDER BY
	tgl_Aftap ASC");
  $TRec = mysql_num_rows($hasil);
  ?>

  <br>
  <table class="list border-table-color" id="box-table-b">
    <? if ($_SESSION['leveluser'] == "komponen") { ?>
      <tr class="field border-table-color">
        <th rowspan=2 class="border-table-color"><b>No</b></th>
        <th rowspan=2 class="border-table-color"><b>Asal Darah</b></th>
        <th rowspan=2 class="border-table-color"><b>No Donor</b></th>
        <th rowspan=2 class="border-table-color"><b>No Kantong</b></th>
        <th rowspan=2 class="border-table-color"><b>Jns Kantong</b></th>
        <th colspan=2 class="border-table-color"><b>Hasil Periksa</b></th>
        <th rowspan=2 class="border-table-color"><b>Tgl Aftap</b></th>
        <th rowspan=2 class="border-table-color"><b>GD</b></th>
        <th rowspan=2 class="border-table-color"><b>Rh</b></th>
        <th rowspan=2 class="border-table-color"><b>KGD</b></th>
      </tr>
      <tr class="field border-table-color">
        <th class="border-table-color"><b>IMLTD</b></th>
        <th class="border-table-color"><b>NAT</b></th>
      </tr>
    <? } else { ?>
      <tr class="field border-table-color">
        <th rowspan=2 class="border-table-color"><b>No</b></th>
        <th rowspan=2 class="border-table-color"><b>Asal Darah</b></th>
        <th rowspan=2 class="border-table-color"><b>No Donor</b></th>
        <th rowspan=2 class="border-table-color"><b>No Kantong</b></th>
        <th rowspan=2 class="border-table-color"><b>Jns Kantong</b></th>
        <th colspan=5 class="border-table-color"><b>Hasil Periksa</b></th>
        <th rowspan=2 class="border-table-color"><b>Tgl Aftap</b></th>
        <th rowspan=2 class="border-table-color"><b>GD</b></th>
        <th rowspan=2 class="border-table-color"><b>Rh</b></th>
        <th rowspan=2 class="border-table-color"><b>KGD</b></th>
      </tr>
      <tr class="field border-table-color">
        <th class="border-table-color"><b>HBsAg</b></th>
        <th class="border-table-color"><b>HCV</b></th>
        <th class="border-table-color"><b>HIV</b></th>
        <th class="border-table-color"><b>Syp</b></th>
        <th class="border-table-color"><b>NAT</b></th>
      </tr>
      <? }
    $no = 1;
    while ($baris = mysql_fetch_assoc($hasil)) {
      $kon = 'Belum';
      if ($baris[kon] == '1') $kon = 'Sudah';

      $ckt = mysql_fetch_assoc(mysql_query("select * from stokkantong where noKantong='$baris[nk]'"));
      $cpd = mysql_fetch_assoc(mysql_query("SELECT pd.Kode, pd.Nama, pd.GolDarah, pd.Alamat, ht.Instansi, ht.tempat FROM htransaksi AS ht JOIN pendonor AS pd ON pd.Kode = ht.KodePendonor WHERE ht.NoKantong = '$ckt[noKantong]'"));
      $hHBsAg = "";
      $hHCV = "";
      $hHIV = "";
      $hSyp = "";
      $reaktif = false;
      for ($jenis = 0; $jenis < 4; $jenis++) {
        $reak0 = mysql_query("select Hasil,tglPeriksa,jenisPeriksa,OD from hasilelisa where nokantong='$baris[nk]' and jenisPeriksa='$jenis' ORDER BY id DESC limit 1");
        if (mysql_num_rows($reak0) == '1') {
          $reak = mysql_fetch_assoc($reak0);
          if ($reak[jenisPeriksa] == '0') {
            $hHBsAg = 'NonReaktif<br>' . $reak[OD];
            if ($reak[Hasil] == '1') {
              $reaktif = true;
              $hHBsAg = '<b style="color: #ff0000;">Reaktif<br>' . $reak[OD] . '</b>';
            }
          }
          if ($reak[jenisPeriksa] == '1') {
            $hHCV = 'NonReaktif<br>' . $reak[OD];
            if ($reak[Hasil] == '1') {
              $reaktif = true;
              $hHCV = '<b style="color: #ff0000;">Reaktif<br>' . $reak[OD] . '</b>';
            }
          }
          if ($reak[jenisPeriksa] == '2') {
            $hHIV = 'NonReaktif<br>' . $reak[OD];
            if ($reak[Hasil] == '1') {
              $reaktif = true;
              $hHIV = '<b style="color: #ff0000;">Reaktif<br>' . $reak[OD] . '</b>';
            }
          }
          if ($reak[jenisPeriksa] == '3') {
            $hSyp = 'NonReaktif<br>' . $reak[OD];
            if ($reak[Hasil] == '1') {
              $reaktif = true;
              $hSyp = '<b style="color: #ff0000;">Reaktif<br>' . $reak[OD] . '</b>';
            }
          }
        }
      }

      $hNAT = "-";
      $hNAT_komponen = "-";
      $reak1 = mysql_query("select Hasil,tglPeriksa,OD from hasilnat where noKantong='$baris[nk]' ORDER BY id DESC limit 1");
      if (mysql_num_rows($reak1) == '1') {
        $reak = mysql_fetch_assoc($reak1);
        $hNAT = 'NonReaktif<br>' . $reak[OD];
        $hNAT_komponen = 'NonReaktif';
        if ($reak[Hasil] == '1') {
          $reaktif = true;
          $hNAT = '<b style="color: #ff0000;">Reaktif<br>' . $reak[OD] . '</b>';
          $hNAT_komponen = '<b style="color: #ff0000;">Reaktif</b>';
        }
      }

      // Kesimpulan IMLTD gabungan (HBsAg+HCV+HIV+Syp) tanpa rasio, untuk level komponen
      $imltdAnyReaktif = ($hHBsAg !== "" && strpos($hHBsAg, 'Reaktif<br>') !== false)
        || ($hHCV   !== "" && strpos($hHCV,   'Reaktif<br>') !== false)
        || ($hHIV   !== "" && strpos($hHIV,   'Reaktif<br>') !== false)
        || ($hSyp   !== "" && strpos($hSyp,   'Reaktif<br>') !== false);
      $imltdAda = ($hHBsAg !== "" || $hHCV !== "" || $hHIV !== "" || $hSyp !== "");
      if (!$imltdAda) {
        $hIMTLD_komponen = "-";
      } elseif ($imltdAnyReaktif) {
        $hIMTLD_komponen = '<b style="color: #ff0000;">Reaktif</b>';
      } else {
        $hIMTLD_komponen = 'NonReaktif';
      }

      switch ($ckt[jenis]) {
        case '1':
          $jenis = 'Single';
          break;
        case '2':
          $jenis = 'Double';
          break;
        case '3':
          $jenis = 'Triple';
          break;
        case '4':
          $jenis = 'Quadruple';
          break;
        case '5':
          $jenis = 'Pediatrik/Quintuple';
          break;
        case '6':
          $jenis = 'Sextuple';
          break;
        case '7':
          $jenis = 'Septuple';
          break;
        case '8':
          $jenis = 'Octuple';
          break;
        case '9':
          $jenis = 'Nonuple';
          break;
        case '10':
          $jenis = 'Decuple';
          break;
        default:
          $jenis = '';
      }

      $instansi = 'DALAM GEDUNG';
      if ($cpd[Instansi] != '') {
        $instansi = $cpd[Instansi];
      }

      $found = true;
      if ($_POST[instansi]) {
        $found = false;
        if ($_POST[instansi] == $instansi) {
          $found = true;
        }
      }
      if ($reaktif && $found) {
        if ($_SESSION['leveluser'] == "komponen") {
      ?>
          <tr class="record border-table-color">
            <td class="border-table-color">
              <div align="center">
                <font size="2">
                  <?= $no ?>
                </font>
              </div>
            </td>
            <td class="border-table-color" style="text-align: left;"><?= $instansi ?></td>
            <td class="border-table-color" style="text-align: left;"><?= $cpd[Kode] ?></td>
            <td class="border-table-color">
              <a href="modul/detail_nonreaktif.php?nokan=<?= $baris[nk] ?>&width=430&height=250" class="thickbox"><?= $baris[nk] ?></a>
            </td>
            <td class="border-table-color"><?= $jenis . ' ' . $ckt[metoda] ?></td>
            <td class="border-table-color"><?= $hIMTLD_komponen ?></td>
            <td class="border-table-color"><?= $hNAT_komponen ?></td>
            <td class="border-table-color"><?= $baris[ta] ?></td>
            <td class="border-table-color"><?= $baris[gd] ?></td>
            <td class="border-table-color"><?= $baris[rh] ?></td>
            <td class="border-table-color"><?= $kon ?></td>
          </tr>
        <? } else { ?>
          <tr class="record border-table-color">
            <td class="border-table-color">
              <div align="center">
                <font size="2">
                  <?= $no ?>
                </font>
              </div>
            </td>
            <td class="border-table-color" style="text-align: left;"><?= $instansi ?></td>
            <td class="border-table-color" style="text-align: left;"><?= $cpd[Kode] ?></td>
            <td class="border-table-color">
              <a href="modul/detail_nonreaktif.php?nokan=<?= $baris[nk] ?>&width=430&height=250" class="thickbox"><?= $baris[nk] ?></a>
            </td>
            <td class="border-table-color"><?= $jenis . ' ' . $ckt[metoda] ?></td>
            <td class="border-table-color"><?= $hHBsAg ?></td>
            <td class="border-table-color"><?= $hHCV ?></td>
            <td class="border-table-color"><?= $hHIV ?></td>
            <td class="border-table-color"><?= $hSyp ?></td>
            <td class="border-table-color"><?= $hNAT ?></td>
            <td class="border-table-color"><?= $baris[ta] ?></td>
            <td class="border-table-color"><?= $baris[gd] ?></td>
            <td class="border-table-color"><?= $baris[rh] ?></td>
            <td class="border-table-color"><?= $kon ?></td>
          </tr>
    <? }
        $no++;  // dipindah ke luar if/else agar nomor naik untuk semua level
      }
    } ?>
  </table>
  </br>
  <button onclick="window.print()" class="hidden-print">Cetak</button>
<? } ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[name="cari"]');
    const datepicker = document.getElementById('datepicker');
    const datepicker1 = document.getElementById('datepicker1');
    const submitBtn = document.getElementById('submit');

    form.addEventListener('submit', function() {
      // Jangan disable tombol submit sebelum kirim, bisa menyebabkan data tidak terkirim
      // Alihkan tampilan saja
      if (datepicker) datepicker.readOnly = true;
      if (datepicker1) datepicker1.readOnly = true;

      if (submitBtn) {
        submitBtn.value = 'Mohon menunggu...';
        // Delay disable supaya name/value tetap terkirim
        setTimeout(() => {
          submitBtn.disabled = true;
        }, 100);
      }
    });
  });

  jQuery(document).ready(function() {
    $('#instansi').autocomplete({
      source: 'modul/suggest_instansi_transaksi.php',
      minLength: 2,
      select: function(event, ui) {
        const nama = ui.item.value;
        $("#instansi").val(nama);
      }
    });
  });
</script>