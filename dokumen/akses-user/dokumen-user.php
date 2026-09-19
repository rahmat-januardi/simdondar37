<?php
include "../koneksi.php";

function tampilkanTabelDokumen($title, $dataResult, $withFormat = false, $isEksternal = false, $kodeField = 'kontrol2')
{
  static $tableIndex = 0;

  echo "<table>
    <thead>
      <tr>
        <th rowspan='2'>No</th>
        <th rowspan='2'>Bidang</th>
        <th rowspan='2'>Kode Dokumen</th>
        <th rowspan='2'>Judul Dokumen</th>";
  if ($withFormat) echo "<th rowspan='2'>Format Dokumen</th><th rowspan='2'>Aksi</th>";
  else echo "<th rowspan='2'>Aksi</th>";
  echo "</tr></thead><tbody class='documentRows'>";

  $no = 0;
  while ($data = mysql_fetch_array($dataResult)) {
    $no++;
    $kodeDokumen = isset($data[$kodeField]) ? $data[$kodeField] : '';
    $bidangTampil = htmlspecialchars(isset($data['bidang']) ? $data['bidang'] : '', ENT_QUOTES, 'UTF-8');
    $kodeTampil = htmlspecialchars($kodeDokumen, ENT_QUOTES, 'UTF-8');
    $namaTampil = htmlspecialchars(isset($data['nama1']) ? $data['nama1'] : '', ENT_QUOTES, 'UTF-8');
    echo "<tr><td>$no</td><td>$bidangTampil</td><td>$kodeTampil</td><td>$namaTampil</td>";

    if (!$withFormat) {
      $fileku = isset($data['fileku']) ? $data['fileku'] : '';
      $previewLink = $fileku !== '' ? "<a href='download-user.php?filename=" . urlencode($fileku) . "' target='_blank'><button class='btn-preview'>Preview</button></a>" : "<button class='btn-preview disabled' disabled>Preview Tidak Tersedia</button>";
      echo "<td>$previewLink
                </td>";
    } else {
      $files = !empty($data['semua_file']) ? explode(',', $data['semua_file']) : array();
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

$queries = array();

if (isset($_POST['submit']) && !empty($_POST['bidang'])) {
  $bidang = mysql_real_escape_string($_POST['bidang']);
  $namaBidang = '';
  $bidangResult = mysql_query("SELECT bidang FROM master_bidang WHERE kode_bidang='$bidang' LIMIT 1");
  if ($bidangResult && ($bidangData = mysql_fetch_assoc($bidangResult))) {
    $namaBidang = mysql_real_escape_string($bidangData['bidang']);
  }

  $whereBidang = "bidang='$bidang'";
  if ($namaBidang !== '') {
    $whereBidang .= " OR bidang='$namaBidang'";
  }

  $queries = array(
    array(
      'title' => 'KEBIJAKAN',
      'query' => "SELECT * FROM kebijakan WHERE aktif='0' AND ($whereBidang) ORDER BY RIGHT(kontrol,3)",
      'withFormat' => false,
      'kodeField' => 'kontrol'
    ),
    array(
      'title' => 'SPO',
      'query' => "SELECT * FROM pks WHERE aktif='0' AND ($whereBidang) ORDER BY kontrol2",
      'withFormat' => false
    ),
    array(
      'title' => 'IK',
      'query' => "SELECT * FROM ik WHERE aktif='0' AND ($whereBidang) ORDER BY kontrol2",
      'withFormat' => false
    ),
    array(
      'title' => 'IK ALAT',
      'query' => "SELECT * FROM ika WHERE aktif='0' AND ($whereBidang) ORDER BY kontrol2",
      'withFormat' => false
    ),
    array(
      'title' => 'DOKUMEN PENDUKUNG',
      'query' => "SELECT * FROM pendukung WHERE aktif='0' AND ($whereBidang) ORDER BY kontrol2",
      'withFormat' => false
    ),
    array(
      'title' => 'FORMULIR',
      'query' => "SELECT nama1, bidang, kontrol2, GROUP_CONCAT(fileku) AS semua_file FROM formulir WHERE aktif='0' AND ($whereBidang) GROUP BY nama1, kontrol2 ORDER BY kontrol2",
      'withFormat' => true
    ),
    array(
      'title' => 'DOKUMEN EKSTERNAL',
      'query' => "SELECT nama1, bidang, kontrol2, GROUP_CONCAT(fileku) AS semua_file FROM eksternal WHERE aktif='0' AND ($whereBidang) GROUP BY nama1, kontrol2 ORDER BY kontrol2",
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
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".documentRows tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
      });

      // Select2 init
      $('#bidangSelect').select2({
        placeholder: "-PILIH-",
        allowClear: true
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
    :root {
      --pmi-red: #c8102e;
      --pmi-red-dark: #9f1239;
      --pmi-red-soft: #fdecef;
      --pmi-border: #e2e5e9;
      --pmi-text: #343a40;
    }

    body {
      background: radial-gradient(circle at top right, rgba(200, 16, 46, .08), transparent 28%),
        linear-gradient(135deg, #f8f9fa 0%, #fff5f6 100%);
      color: var(--pmi-text);
      min-height: 100vh;
    }

    .page-header,
    .document-panel {
      background: rgba(255, 255, 255, .9);
      border: 1px solid var(--pmi-border);
      border-radius: 16px;
      box-shadow: 0 8px 28px rgba(52, 58, 64, .08);
    }

    .page-header {
      padding: 18px 22px;
    }

    .page-title {
      color: var(--pmi-red-dark);
      font-weight: 700;
    }

    .document-panel {
      padding: 22px;
      margin-bottom: 18px;
    }

    .table-responsive {
      border: 1px solid var(--pmi-border);
      border-radius: 12px;
      overflow-x: auto;
    }

    table {
      font-size: 12px;
      margin: auto;
      min-width: 850px;
      width: 100%;
    }

    th {
      background: var(--pmi-red) !important;
      border-color: var(--pmi-red-dark) !important;
      color: #fff;
      white-space: nowrap;
    }

    td,
    th {
      padding: 8px;
      text-align: center;
      vertical-align: middle;
    }

    tbody tr:hover {
      background-color: #fff5f6;
    }

    input[type=text] {
      width: 260px;
      box-sizing: border-box;
      border: 1px solid #ced4da;
      border-radius: 8px;
      font-size: 14px;
      padding: 9px 12px;
    }

    input[type=text]:focus {
      width: 100%;
      border-color: var(--pmi-red);
      box-shadow: 0 0 0 .15rem rgba(200, 16, 46, .12);
      outline: none;
    }

    .button,
    .btn-warm {
      padding: 10px 20px;
      font-size: 12px;
      text-align: center;
      cursor: pointer;
      outline: none;
      color: #fff;
      background-color: var(--pmi-red);
      border: none;
      border-radius: 8px;
    }

    .button:hover,
    .btn-warm:hover,
    .btn-warm:focus {
      background-color: var(--pmi-red-dark);
    }

    .button:active {
      background-color: var(--pmi-red-dark);
      transform: translateY(3px);
    }

    .btn-preview {
      background-color: #198754;
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
      background-color: var(--pmi-red);
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
      background-color: var(--pmi-red-dark);
    }

    .modal {
      z-index: 1055;
    }

    @media (max-width: 768px) {
      .document-panel {
        padding: 14px;
      }

      input[type=text] {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid py-4">
    <div class="page-header mb-3">
      <h4 class="page-title mb-1"><i class="bi bi-folder2-open me-2"></i>Skema E-Dokumen</h4>
      <div class="text-muted">Kebijakan, SPO, IK, formulir, dokumen pendukung, dan dokumen eksternal.</div>
    </div>
    <div class="document-panel">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <form method="post" class="d-flex align-items-center gap-2 flex-wrap">
          <label for="bidangSelect" class="form-label mb-0">Bidang:</label>
          <select name="bidang" id="bidangSelect" style="width:300px;">
            <option value="">-PILIH-</option>
            <?php
            $query = 'select * from master_bidang order by bidang';
            $hasil = mysql_query($query);
            while ($data = mysql_fetch_array($hasil)) {
              $kodeBidang = htmlspecialchars($data['kode_bidang'], ENT_QUOTES, 'UTF-8');
              $namaBidang = htmlspecialchars($data['bidang'], ENT_QUOTES, 'UTF-8');
              echo "<option value='$kodeBidang'>$namaBidang</option>";
            }
            ?>
          </select>
          <input type="submit" name="submit" value="Cari" class="btn btn-warm">
        </form>
        <input id="myInput" type="text" placeholder="Cari dokumen...">

      </div>

      <?php
      foreach ($queries as $q) {
        $result = mysql_query($q['query']);
        if ($result) {
          echo "<section class='document-section mb-4'><h5 class='page-title mb-3'>" . htmlspecialchars($q['title'], ENT_QUOTES, 'UTF-8') . "</h5><div class='table-responsive'>";
          tampilkanTabelDokumen($q['title'], $result, $q['withFormat'], isset($q['isEksternal']) ? $q['isEksternal'] : false, isset($q['kodeField']) ? $q['kodeField'] : 'kontrol2');
          echo '</div></section>';
        }
      }
      ?>
    </div>
  </div>
</body>

</html>