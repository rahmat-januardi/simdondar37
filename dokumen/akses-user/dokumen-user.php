<?php
include "../koneksi.php";

function tampilkanTabelDokumen($title, $dataResult, $withFormat = false, $isEksternal = false)
{
  static $tableIndex = 0;

  echo "<table>
    <thead>
      <tr width='100%'><td colspan='" . ($withFormat ? 6 : 5) . "'><h3><b>$title</b></h3></td></tr>
      <tr>
        <th rowspan='2'>No</th>
        <th rowspan='2'>Bidang</th>
        <th rowspan='2'>Kode Dokumen</th>
        <th rowspan='2'>Judul Dokumen</th>";
  if ($withFormat) echo "<th rowspan='2'>Format Dokumen</th><th rowspan='2'>Aksi</th>";
  else echo "<th rowspan='2'>Aksi</th>";
  echo "</tr></thead><tbody id='myTable'>";

  $no = 0;
  while ($data = mysql_fetch_array($dataResult)) {
    $no++;
    echo "<tr><td>$no</td><td>{$data['bidang']}</td><td>{$data['kontrol2']}</td><td>{$data['nama1']}</td>";

    if (!$withFormat) {
      echo "<td><a href='download-user.php?filename=" . urlencode($data['fileku']) . "' target='_blank'>
                  <button class='btn-preview'>Preview</button>
                </a></td>";
    } else {
      $files = explode(',', $data['semua_file']);
      $formats = array();
      foreach ($files as $f) {
        $ext = strtoupper(pathinfo($f, PATHINFO_EXTENSION));
        if (!in_array($ext, $formats)) $formats[] = $ext;
      }
      $formatText = implode(', ', $formats);
      $modalId = "modalDownload" . ($isEksternal ? "Eksternal" : "") . $tableIndex;

      $pdfFile = null;
      foreach ($files as $f) {
        if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf') {
          $pdfFile = $f;
          break;
        }
      }

      echo "<td>$formatText</td><td>";
      if ($pdfFile) {
        echo "<a href='download-user.php?filename=" . urlencode($pdfFile) . "' target='_blank'>
                      <button class='btn-preview'>Preview</button>
                    </a> ";
      } else {
        echo "<button class='btn-preview disabled' disabled>Preview Tidak Tersedia</button> ";
      }

      echo "<button class='btn-download' onclick=\"openModal('$modalId')\">Download</button></td></tr>";

      echo "<div id='$modalId' class='modal'
                    style='display:none; position:fixed; top:20%; left:30%; background:#fff; padding:20px; border:1px solid #ccc;'>
                  <h4>Syarat & Ketentuan</h4>
                  <p>Dengan membaca pernyataan ini, saya menyatakan bahwa saya memahami dan menyetujui bahwa dokumen
                  internal UDDP PMI, berupa SPO, IK, Formulir, dan sejenisnya, hanya dapat disampaikan kepada pihak eksternal
                  melalui Sub Bidang Kontrol Dokumen. Saya bertanggung jawab sepenuhnya apabila mendistribusikan atau
                  mengirimkan dokumen tersebut kepada pihak eksternal tanpa keterlibatan Sub Bidang Kontrol Dokumen.</p>
                  <label><input type='checkbox' onchange=\"toggleDownload('$modalId')\" />
                  Saya sudah membaca dan menyetujui syarat dan ketentuan ini.</label>
                  <div id='downloadArea$modalId' style='margin-top:10px; display:none;'>";
      foreach ($files as $f) {
        $ext = strtoupper(pathinfo($f, PATHINFO_EXTENSION));
        echo "<a href='download-eform.php?file=" . urlencode($f) . "&edokumen=ya'>
                      <button class='btn-format'>Download $ext</button>
                    </a> ";
      }
      echo "</div><br><br><button onclick=\"closeModal('$modalId')\">Tutup</button></div>";
      $tableIndex++;
    }
    echo "</tr>";
  }

  echo "</tbody></table><br><br>";
}

if (isset($_POST['submit']) && !empty($_POST['bidang'])) {
  $bidang = mysql_real_escape_string($_POST['bidang']);

  $queries = array(
    array(
      'title' => 'KEBIJAKAN',
      'query' => "SELECT * FROM kebijakan WHERE aktif='0' AND bidang='$bidang' ORDER BY RIGHT(kontrol,3)",
      'withFormat' => false
    ),
    array(
      'title' => 'SPO',
      'query' => "SELECT * FROM pks WHERE aktif='0' AND bidang='$bidang' ORDER BY kontrol2",
      'withFormat' => false
    ),
    array(
      'title' => 'IK',
      'query' => "SELECT * FROM ik WHERE aktif='0' AND bidang='$bidang' ORDER BY kontrol2",
      'withFormat' => false
    ),
    array(
      'title' => 'IK ALAT',
      'query' => "SELECT * FROM ika WHERE aktif='0' AND bidang='$bidang' ORDER BY kontrol2",
      'withFormat' => false
    ),
    array(
      'title' => 'DOKUMEN PENDUKUNG',
      'query' => "SELECT * FROM pendukung WHERE aktif='0' AND bidang='$bidang' ORDER BY kontrol2",
      'withFormat' => false
    ),
    array(
      'title' => 'FORMULIR',
      'query' => "SELECT nama1, bidang, kontrol2, GROUP_CONCAT(fileku) AS semua_file FROM formulir WHERE aktif='0' AND bidang='$bidang' GROUP BY nama1, kontrol2 ORDER BY kontrol2",
      'withFormat' => true
    ),
    array(
      'title' => 'DOKUMEN EKSTERNAL',
      'query' => "SELECT nama1, bidang, kontrol2, GROUP_CONCAT(fileku) AS semua_file FROM eksternal WHERE aktif='0' AND bidang='$bidang' GROUP BY nama1, kontrol2 ORDER BY kontrol2",
      'withFormat' => true,
      'isEksternal' => true
    )
  );
}
?>

<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });

    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        document.querySelector('#' + id + ' input[type="checkbox"]').checked = false;
        document.getElementById('downloadArea' + id).style.display = 'none';
    }

    function toggleDownload(id) {
        var area = document.getElementById('downloadArea' + id);
        var checkbox = document.querySelector('#' + id + ' input[type="checkbox"]');
        area.style.display = checkbox.checked ? 'block' : 'none';
    }
    </script>
    <style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        font-size: 12px;
        margin: auto;
        width: 100%;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: center;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }

    input[type=text] {
        width: 130px;
        box-sizing: border-box;
        border: 2px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        background-color: white;
        background-image: url('searchicon.png');
        background-position: 10px 10px;
        background-repeat: no-repeat;
        padding: 12px 20px 12px 40px;
        -webkit-transition: width 0.4s ease-in-out;
        transition: width 0.4s ease-in-out;
    }

    input[type=text]:focus {
        width: 100%;
    }

    .button {
        padding: 10px 20px;
        font-size: 12px;
        text-align: center;
        cursor: pointer;
        outline: none;
        color: #fff;
        background-color: #ff5a0b;
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px #999;
    }

    .button:hover {
        background-color: #3e8e41;
    }

    .button:active {
        background-color: #3e8e41;
        box-shadow: 0 3px #666;
        transform: translateY(3px);
    }

    .btn-preview {
        background-color: #28a745;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        margin-right: 5px;
    }

    .btn-preview.disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }

    .btn-download {
        background-color: #007bff;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-format {
        background-color: #6c757d;
        color: white;
        padding: 6px 14px;
        margin: 5px 5px 0 0;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-format:hover {
        background-color: #3e8e41;
    }
    </style>
</head>

<body>
    <br />
    <input id="myInput" type="text" placeholder="Search..">
    <br><br>
    <p align="center"><b>SKEMA E-DOKUMEN <br> (KEBIJAKAN - SPO - IK - FORMULIR - DOKUMEN PENDUKUNG - DOKUMEN
            EKSTERNAL)</b></p>
    <br>
    <form method="post">
        <label style="margin:10px;">bidang : </label>
        <select name="bidang">
            <option value="">-PILIH-</option>
            <option value="Manajemen Kualitas">Manajemen Kualitas</option>
            <option value="Penyediaan Donor">Penyediaan Donor</option>
            <option value="Kerjasama Hukum dan Humas">Kerjasama Hukum dan Humas</option>
            <option value="Simdondar">Simdondar</option>
            <option value="Penyediaan Darah">Penyediaan Darah</option>
            <option value="Rujukan IMLTD">Rujukan IMLTD</option>
            <option value="Rujukan Imunohematologi">Rujukan Imunohematologi</option>
            <option value="Litbang">Litbang</option>
            <option value="Produksi">Produksi</option>
            <option value="Kalibrasi">Kalibrasi</option>
            <option value="Pengawasan Mutu">Pengawasan Mutu</option>
            <option value="Pembinaan Kualitas">Pembinaan Kualitas</option>
            <option value="Umum">Umum</option>
            <option value="Logistik">Logistik</option>
            <option value="Sekretariat">Sekretariat</option>
            <option value="Rumah Tangga">Rumah Tangga</option>
            <option value="Wisma">Wisma</option>
            <option value="Kepegawaian">Kepegawaian</option>
            <option value="Keuangan">Keuangan</option>
            <option value="Diklat">Diklat</option>
        </select>
        <input type="submit" name="submit" value="Cari"
            style="margin-left:10px; background-color: #04AA6D; border: none; color: white; padding: 5px 15px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 12px;">
    </form>
    <br><br>

    <?php
  foreach ($queries as $q) {
    $result = mysql_query($q['query']);
    if ($result) {
      tampilkanTabelDokumen($q['title'], $result, $q['withFormat'], isset($q['isEksternal']) ? $q['isEksternal'] : false);
    }
  }
  ?>
</body>

</html>