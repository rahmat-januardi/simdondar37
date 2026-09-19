<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

include "config/dbi_connect.php";
$petugas = $_SESSION['namauser'];

function formatTanggal($inputTanggal)
{
    $bulan = array(
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    if (!$inputTanggal || !strtotime($inputTanggal)) {
        return "Tanggal tidak valid";
    }

    $split = explode('-', $inputTanggal);

    if (count($split) < 3) {
        return "Format tanggal tidak valid";
    }

    $dateTime = new DateTime($inputTanggal);
    $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i") . " WIB";

    return $formattedDate;
}

$sql = "SELECT * FROM dpengolahan_temp WHERE petugas = '$petugas' ORDER BY `id` ASC";
$result = $dbi->query($sql);

// Shift Pengolahan
$shift = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama`,`jam`,`sampai_jam` FROM `shift` WHERE time(now()) between time(`jam`) AND time(`sampai_jam`)"));
$sf = isset($shift['nama']) ? $shift['nama'] : '';

switch ($sf) {
    case 'PAGI':
        $shft = 1;
        break;
    case 'SORE':
        $shft = 2;
        break;
    case 'MALAM':
        $shft = 3;
        break;
    case 'MALAM 2':
        $shft = 4;
        break;
    default:
        $shft = 0;
        break;
}

function createOptions($merk0, $volasal0, $jenis_kantong0, $tipe, $selProduk, $nKA, $dbi)
{
    $optionsHTML = '';

    $metodaKt = '';
    $sqlMetoda = "SELECT metoda FROM stokkantong WHERE noKantong LIKE '$nKA' AND noKantong LIKE '%A' LIMIT 1";
    $hsMetoda = $dbi->query($sqlMetoda);
    if ($hsMetoda && $hsMetoda->num_rows > 0) {
        $rowMetoda = $hsMetoda->fetch_assoc();
        $metodaKt = !empty($rowMetoda['metoda']) ? $rowMetoda['metoda'] : 'Unknown';
    }

    // Ambil data master_kantong
    $sql = "SELECT berat_ku, berat_s1, berat_s2, berat_s3, berat_s4, berat_s5, berat_s6, berat_s7, pr_utama, pr_s1, pr_s2, pr_s3, pr_s4, pr_s5, pr_s6, pr_s7, antikoagulant
            FROM master_kantong
            WHERE merk = '$merk0' AND vol = '$volasal0' AND jenis = '$jenis_kantong0'
            LIMIT 1";
    $result = $dbi->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        switch ($tipe) {
            case 'A':
                $produkString = $row['pr_utama'];
                $beratKantongKosong = $row['berat_ku'];
                $antikoagulan = $row['antikoagulant'];
                break;
            case 'B':
                $produkString = $row['pr_s1'];
                $beratKantongKosong = $row['berat_s1'];
                $antikoagulan = 0;
                break;
            case 'C':
                $produkString = $row['pr_s2'];
                $beratKantongKosong = $row['berat_s2'];
                $antikoagulan = 0;
                break;
            case 'D':
                $produkString = $row['pr_s3'];
                $beratKantongKosong = $row['berat_s3'];
                $antikoagulan = 0;
                break;
            case 'E':
                $produkString = $row['pr_s4'];
                $beratKantongKosong = $row['berat_s4'];
                $antikoagulan = 0;
                break;
            case 'F':
                $produkString = $row['pr_s5'];
                $beratKantongKosong = $row['berat_s5'];
                $antikoagulan = 0;
                break;
            case 'G':
                $produkString = $row['pr_s6'];
                $beratKantongKosong = $row['berat_s6'];
                $antikoagulan = 0;
                break;
            case 'H':
                $produkString = $row['pr_s7'];
                $beratKantongKosong = $row['berat_s7'];
                $antikoagulan = 0;
                break;
            default:
                $produkString = '';
                $beratKantongKosong = 0;
                $antikoagulan = 0;
                break;
        }

        $produkArrayRaw = array_map('trim', explode(',', $produkString));

        $produkKhusus = array('PCLR', 'PCLS', 'PCL-S', 'PCL-R', 'PRC Leucoreduce', 'PRC Leucodepleted', 'PRC Leucoreduced', 'Leucoreduce', 'Leucoreduced', 'Leucodepleted', 'Leucoreduction');
        $metodaValid = array('TB', 'TBF', 'TT', 'FT');

        $produkArray = array_filter($produkArrayRaw, function ($produk) use ($metodaKt, $produkKhusus, $metodaValid) {
            if (in_array($produk, $produkKhusus)) {
                return in_array($metodaKt, $metodaValid);
            }
            return true;
        });

        $produkArray = array_unique($produkArray);

        // Dapatkan tgl aftap dari kantong A
        $tglAftap = '';
        $sqlAftap = "SELECT tgl_Aftap FROM stokkantong WHERE noKantong LIKE '$nKA' AND noKantong LIKE '%A' LIMIT 1";
        $resAftap = $dbi->query($sqlAftap);
        if ($resAftap && $resAftap->num_rows > 0) {
            $rowAftap = $resAftap->fetch_assoc();
            $tglAftap = $rowAftap['tgl_Aftap'];
        }

        // Ambil berat timbang_darah hanya sekali per baris
        $ambilNK = substr($nKA, 0, -1);
        $nKantong = $ambilNK . $tipe;

        $qBerat = mysqli_query(
            $dbi,
            "SELECT berat_ukur
             FROM timbang_darah
             WHERE nokantong = '$nKantong'
             ORDER BY id DESC
             LIMIT 1"
        );

        $dBerat = mysqli_fetch_assoc($qBerat);
        $beratDatabase = '';
        if ($dBerat && isset($dBerat['berat_ukur'])) {
            $beratDatabase = $dBerat['berat_ukur'];
        }

        foreach ($produkArray as $option) {
            $selected = ($option == $selProduk) ? "selected" : "";

            // Ambil data produk
            $qProduk = "SELECT beratjenis, umurhari, umurjam, volume, suhusimpan AS psuhu, waktu_pengolahan AS pcepat
                        FROM produk
                        WHERE Nama = '$option'
                        LIMIT 1";
            $resProduk = $dbi->query($qProduk);

            $umurhari = 0;
            $umurjam = 0;
            $beratjenis = 1;
            $pCepat = 5000;
            $bSuhu = 4;

            if ($resProduk && $resProduk->num_rows > 0) {
                $rowp = $resProduk->fetch_assoc();

                $umurhari = (!empty($rowp['umurhari'])) ? $rowp['umurhari'] : 0;
                $umurjam = (!empty($rowp['umurjam'])) ? $rowp['umurjam'] : 0;

                if (isset($rowp['beratjenis']) && $rowp['beratjenis'] > 0) {
                    $beratjenis = $rowp['beratjenis'];
                } else {
                    $beratjenis = 1;
                }

                switch ($option) {
                    case "WE":
                        $pCepat = 5000;
                        $bSuhu = 4;
                        break;
                    case "TC":
                    case "BC":
                        $pCepat = ($nKA === 'A') ? 5000 : 4000;
                        $bSuhu = 4;
                        break;
                    case "FFP":
                    case "FP":
                    case "FP 72":
                    case "FFP Leucodepleted":
                        $pCepat = 5000;
                        $bSuhu = 4;
                        break;
                    case "AHF":
                    case "LP":
                    case "LPLS":
                    case "LP Apheresis":
                        $pCepat = 5000;
                        $bSuhu = 4;
                        break;
                    case "PRCLR":
                    case "PCLS":
                    case "WB Leucodepletet":
                        $pCepat = 5000;
                        $bSuhu = 4;
                        break;
                    case "PRC Apheresis":
                        $pCepat = 5000;
                        $bSuhu = 4;
                        break;
                    default:
                        $pCepat = 5000;
                        $bSuhu = 4;
                        break;
                }
            }

            $optionsHTML .= '
<option value="' . $option . '" ' . $selected . '
    data-umurhari="' . $umurhari . '"
    data-umurjam="' . $umurjam . '"
    data-tgl-aftap="' . $tglAftap . '"
    data-beratkosong="' . $beratKantongKosong . '"
    data-beratjenis="' . $beratjenis . '"
    data-antikoagulan="' . $antikoagulan . '"
    data-berat="' . $beratDatabase . '"
    data-psuhu="' . $bSuhu . '"
    data-pcepat="' . $pCepat . '">
    ' . $option . '
</option>';
        }
    }

    return $optionsHTML;
}

$index = 0;
$no = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $selKantong = "SELECT LEFT(noKantong, LENGTH(noKantong) - 1) AS tanpaSatelite, substring(noKantong, -1) as nK FROM stokkantong WHERE noKantong = '{$row['noKantong']}'";
        $resSK = $dbi->query($selKantong);
        $sK = $resSK->fetch_assoc();

        $nKA = $sK['tanpaSatelite'] . "A";

        $selKantongA = "SELECT merk, volumeasal, DATE_ADD(tgl_Aftap, INTERVAL 1 DAY) as besok, TIMESTAMPDIFF(HOUR, tgl_Aftap, NOW()) AS Jarak FROM stokkantong WHERE noKantong = '$nKA'";
        $resSKA = $dbi->query($selKantongA);
        $sKA = $resSKA->fetch_assoc();

        $merk0 = $sKA['merk'];
        $volasal0 = $sKA['volumeasal'];
        $jenis_kantong0 = $row['jenis'];
        $tipe = $sK['nK'];
        $jarak = $sKA['Jarak'];
        $selProduk = $row['Produk'];

        switch ((int) ($row['jenis'])) {
            case 1:
                $jK = "Single";
                break;
            case 2:
                $jK = "Double";
                break;
            case 3:
                $jK = "Triple";
                break;
            case 4:
                $jK = "Quadruple";
                break;
            case 6:
                $jK = "Pediatrik";
                break;
            default:
                $jK = "-";
                break;
        }

        if (is_null($row['volume']) || $row['volume'] == '' || $row['volume'] == '0') {
            switch ((int) ($row['jenis'])) {
                case 1:
                case 2:
                    $pVol = ($sK['nK'] == 'B') ? "150" : "200";
                    break;
                case 3:
                    if ($sK['nK'] == 'A') {
                        $pVol = "200";
                    } elseif ($sK['nK'] == 'B') {
                        $pVol = "150";
                    } else {
                        $pVol = "150";
                    }
                    break;
                case 6:
                case 4:
                    if ($sK['nK'] == 'A') {
                        $pVol = "200";
                    } elseif ($sK['nK'] == 'B') {
                        $pVol = "150";
                    } elseif ($sK['nK'] == 'C') {
                        $pVol = "150";
                    } else {
                        $pVol = "150";
                    }
                    break;
                default:
                    $pVol = "200";
                    break;
            }
        } else {
            $pVol = $row['volume'];
        }

        $start = new DateTime($row['mulaiPutar']);
        $end = new DateTime($row['selesaiPutar']);
        $interval = $start->diff($end);
        $waktuPutar = ($interval->h * 60) + $interval->i;

        $no++;

        echo "<tr>";
        echo "<td>
                    <button type='button' class='btn btn-link text-danger' onclick='deleteRow(this)' style='width: 30px; height: 30px; padding: 0; border: none; background: none;'>X</button>
                    <input id='idOlah' type='hidden' name='idOlah' value='" . $row["id"] . "'/>
                </td>";
        echo "<td>
                <input id='NoTrans' type='hidden' name='NoTrans' value='" . $row["noTrans"] . "'/>
                <input id='petugas' type='hidden' name='petugas' value='" . $petugas . "'/>
                <input id='shift' type='hidden' name='shift' value='" . $shft . "'/>
                " . $no . "</td>";
        echo "<td>
                <input type='hidden' name='nK[]' value='" . $row["noKantong"] . "'>" .
            $row["noKantong"] . "
                </td>";
        echo "<td>" . (!empty($row["tglAftap"]) && $row["tglAftap"] !== '0000-00-00 00:00:00' ? formatTanggal($row["tglAftap"]) : "Tidak ada data Tanggal (N/A)") . "</td>";
        echo "<td>" . (!empty($row["tglPengerjaan"]) ? $row["tglPengerjaan"] : "0000-00-00") . "</td>";
        echo "<td>" . $row["goldarah"] . " (" . $row["rhesus"] . ")</td>";
        echo "<td>" . $jK . "</td>";

        $options = createOptions($merk0, $volasal0, $jenis_kantong0, $tipe, $selProduk, $nKA, $dbi);
        echo "<td>";
        echo "<select class='custom-select' name='produk[]' onchange='updateFields(this)' style='min-width:70px;'>";
        echo $options;
        echo "</select>";
        echo "</td>";

        echo "<td>
                <input id='ed_produk_$no' style='text-align: center' type='text' name='ed_produk[]' value=''>
                </td>";
        echo "<td>
                <input id='berat_$no' style='text-align: center; width:70px;' type='text' name='berat[]' value=''>
                </td>";
        echo "<td>&plusmn;
                <input id='volume_$no' style='text-align: center; width:70px;' type='text' name='volume[]' value='' size='1'/> cc</td>";
        echo "<td>" . $row["aPutar"] . "</td>";
        echo "<td>
                <input style='min-width:40px;' id='pcepat_$no' style='text-align: center' type='text' name='pcepat[]' value='3000' size='1'/>
                </td>";
        echo "<td>
                <input id='psuhu_$no' style='text-align: center;min-width:40px;' type='text' name='psuhu[]' value='" . $row["psuhu"] . "' size='1'>
                </td>";
        echo "<td>" . $waktuPutar . "</td>";
        echo "<td>
            <select class='custom-select' name='metode[]' style='min-width:70px;'>
                <option value='0'>Manual</option>
                <option value='1'>Otomatis</option>
            </select>
            </td>";
        echo "<td>" . $row["aPisah"] . "</td>";
        echo "<td>" . substr($row["mulaiPisah"], 0, 5) . "</td>";
        echo "<td>" . substr($row["selesaiPisah"], 0, 5) . "</td>";
        echo "<td>" . $row["aBeku"] . "</td>";
        echo "<td>" . substr($row["mulaiBeku"], 0, 5) . "</td>";
        echo "<td>" . substr($row["selesaiBeku"], 0, 5) . "</td>";
        echo "<td>
        <input style='text-align: center;min-width:40px;' type='text' name='bsuhu[]' value='" . $row["bsuhu"] . "' size='2'>
        </td>";
        echo "</tr>";
    }
} else {
    echo "<tr>";
    echo "<td colspan='22'><b>TIDAK ADA DATA</b> untuk ditampilkan.</td>";
    echo "</tr>";
}
?>
<script type="text/javascript">
    function findClosestTr(el) {
        while (el && el.tagName && el.tagName.toUpperCase() !== 'TR') {
            el = el.parentNode;
        }
        return el;
    }

    function pad2(n) {
        n = parseInt(n, 10);
        if (isNaN(n)) {
            n = 0;
        }
        return (n < 10 ? '0' : '') + n;
    }

    function toNumber(value) {
        if (value === null || value === undefined) {
            return 0;
        }
        value = String(value).replace(',', '.').replace(/\s+/g, '').trim();
        var n = Number(value);
        return isNaN(n) ? 0 : n;
    }

    function updateFields(selectElement) {
        var row = findClosestTr(selectElement);
        var selectedOption = selectElement.options[selectElement.selectedIndex];

        if (!row || !selectedOption) {
            return;
        }

        var edProdukInput = row.querySelector('[name="ed_produk[]"]');
        var pcepatInput = row.querySelector('[name="pcepat[]"]');
        var psuhuInput = row.querySelector('[name="psuhu[]"]');
        var volumeInput = row.querySelector('[name="volume[]"]');
        var beratInput = row.querySelector('[name="berat[]"]');

        var umurhari = parseInt(selectedOption.getAttribute('data-umurhari'), 10) || 0;
        var umurjam = parseInt(selectedOption.getAttribute('data-umurjam'), 10) || 0;
        var tglAftapStr = selectedOption.getAttribute('data-tgl-aftap');

        var beratLama = '';
        if (beratInput) {
            beratLama = beratInput.value;
        }

        if (tglAftapStr && edProdukInput) {
            var tgl = new Date(tglAftapStr);
            if (!isNaN(tgl.getTime())) {
                tgl.setDate(tgl.getDate() + umurhari);
                tgl.setHours(tgl.getHours() + umurjam);

                var yyyy = tgl.getFullYear();
                var mm = pad2(tgl.getMonth() + 1);
                var dd = pad2(tgl.getDate());
                var hh = pad2(tgl.getHours());
                var min = pad2(tgl.getMinutes());

                edProdukInput.value = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + min;
            }

            if (pcepatInput) {
                pcepatInput.value = selectedOption.getAttribute('data-pcepat') || '';
            }
            if (psuhuInput) {
                psuhuInput.value = selectedOption.getAttribute('data-psuhu') || '';
            }

            if (beratInput) {
                if (beratLama === '') {
                    beratInput.value = selectedOption.getAttribute('data-berat') || '';
                } else {
                    beratInput.value = beratLama;
                }
            }

            hitungVolume(row);
            return;
        }

        var selectedValue = selectElement.value;
        var noKantongInput = row.querySelector('input[name="nK[]"]');

        if (!noKantongInput) {
            console.warn("noKantong input tidak ditemukan di baris ini.");
            return;
        }

        $.ajax({
            url: 'modul/pengolahan/pengolahanOnChange.php',
            type: 'POST',
            data: {
                produk: selectedValue,
                jKantong: noKantongInput.value
            },
            success: function(response) {
                var data;
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    console.error("Response JSON tidak valid:", response);
                    return;
                }

                if (!data.error) {
                    if (edProdukInput) {
                        edProdukInput.value = data.tglEd || '';
                    }
                    if (pcepatInput) {
                        pcepatInput.value = data.pcepat || '';
                    }
                    if (psuhuInput) {
                        psuhuInput.value = data.psuhu || '';
                    }

                    if (beratInput) {
                        if (beratInput.value === '') {
                            beratInput.value = data.berat || '';
                        }
                    }

                    hitungVolume(row);
                } else {
                    console.error("Gagal mengambil data produk dari server:", data.produk);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    function hitungVolume(row) {
        var produk = row.querySelector("select[name='produk[]']");
        var volumeInput = row.querySelector("[name='volume[]']");
        var beratInput = row.querySelector("[name='berat[]']");

        if (!produk || !volumeInput || !beratInput) {
            return;
        }

        var opt = produk.options[produk.selectedIndex];
        if (!opt) {
            return;
        }

        var beratText = beratInput.value;
        if (beratText === '') {
            volumeInput.value = '';
            return;
        }

        var berat = toNumber(beratText);
        var beratKosong = toNumber(opt.getAttribute('data-beratkosong'));
        var beratJenis = toNumber(opt.getAttribute('data-beratjenis'));
        var antikoagulan = toNumber(opt.getAttribute('data-antikoagulan'));

        if (beratJenis <= 0) {
            beratJenis = 1;
        }

        var volume = 0;

        if (opt.value.toUpperCase().indexOf("WB") !== -1) {
            volume = ((berat - beratKosong) / beratJenis) - antikoagulan;
        } else {
            volume = (berat - beratKosong) / beratJenis;
        }

        if (isNaN(volume) || !isFinite(volume) || volume < 0) {
            volume = 0;
        }

        volumeInput.value = volume.toFixed(2);
    }

    function deleteRow(button) {
        var row = findClosestTr(button);
        if (!row) {
            return;
        }

        var idInput = row.querySelector('input[name="idOlah"]');
        var noKantongInput = row.querySelector('input[name="nK[]"]');

        if (!idInput || !noKantongInput) {
            return;
        }

        var id = idInput.value;
        var noKantong = noKantongInput.value;

        document.getElementById('modalNoKantong').textContent = noKantong;

        $('#confirmDeleteModal').modal('show');

        document.getElementById('confirmDeleteButton').onclick = function() {
            $.ajax({
                url: 'modul/pengolahan/hapusPengolahanTemp.php',
                type: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            location.reload();
                        } else {
                            alert('Gagal menghapus data: ' + jsonResponse.message);
                        }
                    } catch (e) {
                        alert('Gagal memproses response dari server.');
                    }

                    $('#confirmDeleteModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat menghapus data: ' + error);
                    $('#confirmDeleteModal').modal('hide');
                }
            });
        };
    }

    window.addEventListener('DOMContentLoaded', function() {
        var selects = document.querySelectorAll("select[name='produk[]']");
        for (var i = 0; i < selects.length; i++) {
            var sel = selects[i];
            var row = findClosestTr(sel);
            if (!row) {
                continue;
            }

            var edInput = row.querySelector("input[name='ed_produk[]']");
            if (edInput && !edInput.value) {
                updateFields(sel);
            }
        }
    });

    document.addEventListener("input", function(e) {
        if (e && e.target && e.target.name == "berat[]") {
            var row = findClosestTr(e.target);
            if (row) {
                hitungVolume(row);
            }
        }
    });
</script>