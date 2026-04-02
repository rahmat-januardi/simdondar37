<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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

    // Cek apakah input valid
    if (!$inputTanggal || !strtotime($inputTanggal)) {
        return "Tanggal tidak valid";
    }

    $split = explode('-', $inputTanggal);

    // Cek apakah hasil explode menghasilkan 3 elemen (tahun-bulan-tanggal)
    if (count($split) < 3) {
        return "Format tanggal tidak valid";
    }

    $dateTime = new DateTime($inputTanggal);
    $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i") . " WIB";

    return $formattedDate;
}

$sql = "SELECT * FROM dpengolahan_temp WHERE petugas = '$petugas' ORDER BY `id` ASC";
$result = $dbi->query($sql);

//Shift Pengolahan
$shift = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT `nama`,`jam`,`sampai_jam` FROM `shift` WHERE time(now()) between time(`jam`) AND time(`sampai_jam`)"));
$sf = $shift['nama'];

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

//$sData = "SELECT jenis, produk, tglpengolahan, Status, volume, merk, lama_pengambilan FROM stokkantong WHERE noKantong = ''";
//$rData = $dbi->query($sData);
//$sD = fetch_assoc($rData);
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
        $tgl_aftap0 = substr($row['tglAftap'], 0, 10);
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

        // Fungsi Menghitung durasi menjadi Satuan Menit Gess
        $start = new DateTime($row['mulaiPutar']);
        $end = new DateTime($row['selesaiPutar']);
        $interval = $start->diff($end);
        $waktuPutar = ($interval->h * 60) + $interval->i;

        if (!function_exists('createOptions')) {
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
                $sql = "SELECT berat_ku, berat_s1, berat_s2, berat_s3, berat_s4, berat_s5, berat_s6, berat_s7, pr_utama, pr_s1, pr_s2, pr_s3, pr_s4, pr_s5, pr_s6, pr_s7, antikoagulant FROM master_kantong 
                WHERE merk = '$merk0' AND vol = '$volasal0' AND jenis = '$jenis_kantong0' LIMIT 1";
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
                            break;
                        case 'C':
                            $produkString = $row['pr_s2'];
                            $beratKantongKosong = $row['berat_s2'];
                            break;
                        case 'D':
                            $produkString = $row['pr_s3'];
                            $beratKantongKosong = $row['berat_s3'];
                            break;
                        case 'E':
                            $produkString = $row['pr_s4'];
                            $beratKantongKosong = $row['berat_s4'];
                            break;
                        case 'F':
                            $produkString = $row['pr_s5'];
                            $beratKantongKosong = $row['berat_s5'];
                            break;
                        case 'G':
                            $produkString = $row['pr_s6'];
                            $beratKantongKosong = $row['berat_s6'];
                            break;
                        case 'H':
                            $produkString = $row['pr_s7'];
                            $beratKantongKosong = $row['berat_s7'];
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

                    //$produkArray = array_unique(array_filter(array_map('trim', explode(',', $produkString))));

                    // Dapatkan tgl aftap dari kantong A
                    $tglAftap = '';
                    $sqlAftap = "SELECT tgl_Aftap FROM stokkantong WHERE noKantong LIKE '$nKA' AND noKantong LIKE '%A' LIMIT 1";
                    $resAftap = $dbi->query($sqlAftap);
                    if ($resAftap && $resAftap->num_rows > 0) {
                        $rowAftap = $resAftap->fetch_assoc();
                        $tglAftap = $rowAftap['tgl_Aftap'];
                    }

                    foreach ($produkArray as $option) {
                        $selected = ($option == $selProduk) ? "selected" : "";

                        // Ambil data produk
                        $qProduk = "SELECT beratjenis, umurhari, umurjam, volume, suhusimpan AS psuhu, waktu_pengolahan AS pcepat 
                                    FROM produk WHERE Nama = '$option' LIMIT 1";
                        $resProduk = $dbi->query($qProduk);
                        $umurhari = $umurjam = $volume = '';

                        $pCepat = 5000;
                        $bSuhu = 4;

                        if ($resProduk && $resProduk->num_rows > 0) {
                            $rowp = $resProduk->fetch_assoc();
                            $umurhari = $rowp['umurhari'] ? $rowp['umurhari'] : 0;
                            $umurjam = $rowp['umurjam'] ? $rowp['umurjam'] : 0;
                            $volume = $rowp['volume'] ? $rowp['volume'] : 0;
                            $beratjenis = $rowp['beratjenis'] ? $rowp['beratjenis'] : 0;
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
                                case "PCLS": // Plasma Kaya Trombosit (Platet Rich Plasma)
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

                        // Dapat Perhitungan Volume
                        // Jika Tidak WB : Volume = (Berat kantong (gram) - Berat Kantong Kosong) : berat jenis
                        // Jika WB : Volume = ((Berat kantong (gram) - Berat Kantong Kosong) : berat jenis) - antikoagulan

                        $ambilNK = substr($nKA, 0, -1);
                        $nKantong = $ambilNK . $tipe;
                        $dataTimbangdarah = mysqli_fetch_assoc(mysqli_query($dbi, "SELECT berat_ukur FROM timbang_darah WHERE nokantong = '$nKantong' ORDER BY id DESC LIMIT 1"));
                        $beratKantongDarah = isset($dataTimbangdarah['berat_ukur']) ? $dataTimbangdarah['berat_ukur'] : 0;

                        if (stripos($option, 'wb') !== false) {
                            $hitungVolume = ($beratKantongDarah - $beratKantongKosong) / $beratjenis;
                            $resultVolume = round($hitungVolume - $antikoagulan);
                        } else {
                            $resultVolume = round(($beratKantongDarah - $beratKantongKosong) / $beratjenis);
                        }


                        $optionsHTML .= "<option value=\"$option\" $selected 
                            data-umurhari=\"$umurhari\" 
                            data-umurjam=\"$umurjam\" 
                            data-tgl-aftap=\"$tglAftap\"
                            data-volume=\"$resultVolume\"
                            data-psuhu=\"$bSuhu\"
                            data-pcepat=\"$pCepat\">
                            $option</option>\n";
                    }
                }
                return $optionsHTML;
            }
        }

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
        // echo "<option value='-'>-- Pilih Produk --</option>";
        echo $options;
        echo "</select>";
        echo "</td>";


        echo "<td>
                <input id='ed_produk_$no' style='text-align: center' type='text' name='ed_produk[]' value=''>
                </td>";
        echo "<td>&plusmn; 
                <input id='volume_$no' style='text-align: center' type='text' name='volume[]' value='' size='1'/> cc</td>";
        echo "<td>" . $row["aPutar"] . "</td>";
        // echo "<td>" . $jarak . "</td>";
        //echo "<td>" . $row["pcepat"] . "</td>";
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

        // echo "<td style='text-align: center;'>
        // <label class='slider-label'>Tidak</label>
        // <input type='range' class='bstatus-slider' name='bstatus[$index]' min='0' max='1' step='1' value='" . $row['bstatus'] . "'>
        // <label class='slider-label'>Iya</label>
        // </td>";

        echo "<td>" . $row["aBeku"] . "</td>";
        echo "<td>" . substr($row["mulaiBeku"], 0, 5) . "</td>";
        echo "<td>" . substr($row["selesaiBeku"], 0, 5) . "</td>";

        echo "<td>
        <input style='text-align: center;min-width:40px;' type='text' name='bsuhu[]' value='" . $row["bsuhu"] . "' size='2'>
        </td>";
        echo "</tr>";

        $index++;
    }
} else {
    echo "<tr>";
    echo "<td colspan='22'><b>TIDAK ADA DATA</b> untuk ditampilkan.</td>";
    echo "</tr>";
}
?>
<script>
    function updateFields(selectElement) {
        const row = selectElement.closest('tr');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const edProdukInput = row.querySelector('[name="ed_produk[]"]');

        const pcepatInput = row.querySelector('[name="pcepat[]"]');
        const psuhuInput = row.querySelector('[name="psuhu[]"]');
        const volumeInput = row.querySelector('[name="volume[]"]');

        const umurhari = parseInt(selectedOption.getAttribute('data-umurhari')) || 0;
        const umurjam = parseInt(selectedOption.getAttribute('data-umurjam')) || 0;
        const tglAftapStr = selectedOption.getAttribute('data-tgl-aftap');

        if (tglAftapStr && edProdukInput) {
            const tgl = new Date(tglAftapStr);
            tgl.setDate(tgl.getDate() + umurhari);
            tgl.setHours(tgl.getHours() + umurjam);

            // Format jadi: YYYY-MM-DD HH:mm
            const yyyy = tgl.getFullYear();
            const mm = String(tgl.getMonth() + 1).padStart(2, '0');
            const dd = String(tgl.getDate()).padStart(2, '0');
            const hh = String(tgl.getHours()).padStart(2, '0');
            const min = String(tgl.getMinutes()).padStart(2, '0');
            const edFormatted = `${yyyy}-${mm}-${dd} ${hh}:${min}`;

            edProdukInput.value = edFormatted;

            if (pcepatInput) pcepatInput.value = selectedOption.getAttribute('data-pcepat') || '';
            if (psuhuInput) psuhuInput.value = selectedOption.getAttribute('data-psuhu') || '';
            if (volumeInput) volumeInput.value = selectedOption.getAttribute('data-volume') || '';


            return;
        }

        const selectedValue = selectElement.value;
        const noKantongInput = row.querySelector('input[name="nK[]"]');

        if (!noKantongInput) {
            console.warn("?? noKantong input tidak ditemukan di baris ini.");
            return;
        }

        $.ajax({
            url: 'modul/pengolahan/pengolahanOnChange.php',
            // url: 'pengolahanOnChange.php',
            type: 'POST',
            data: {
                produk: selectedValue,
                jKantong: noKantongInput.value
            },
            success: function(response) {
                const data = JSON.parse(response);

                //logging
                // console.log(response);
                // console.log(data);

                if (!data.error) {
                    if (edProdukInput) edProdukInput.value = data.tglEd;
                    if (pcepatInput) pcepatInput.value = data.pcepat;
                    if (psuhuInput) psuhuInput.value = data.psuhu;
                    if (volumeInput) volumeInput.value = data.volume;

                } else {
                    console.error("? Gagal mengambil data produk dari server:", data.produk);
                }
            },
            error: function(xhr, status, error) {
                console.error("? AJAX Error:", error);
            }
        });
    }

    function deleteRow(button) {
        var row = button.closest('tr');
        var id = row.querySelector('input[name="idOlah"]').value;
        var noKantong = row.querySelector('input[name="nK[]"]').value;

        document.getElementById('modalNoKantong').textContent = noKantong;

        $('#confirmDeleteModal').modal('show');

        document.getElementById('confirmDeleteButton').onclick = function() {
            $.ajax({
                url: 'modul/pengolahan/hapusPengolahanTemp.php',
                // url: 'hapusPengolahanTemp.php',
                type: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            // row.remove(); // hanya menghapus dari tampilan HTML
                            location.reload();
                        } else {
                            alert('Gagal menghapus data: ' + jsonResponse.message);
                        }
                    } catch (e) {
                        alert('Gagal memproses response dari server.');
                    }
                    // Sembunyikan modal setelah penghapusan
                    $('#confirmDeleteModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat menghapus data: ' + error);
                    $('#confirmDeleteModal').modal('hide');
                }
            });
        };
    }
</script>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll("select[name='produk[]']").forEach(sel => {
            const row = sel.closest('tr');
            const edInput = row.querySelector("input[name='ed_produk[]']");
            if (edInput && !edInput.value) {
                updateFields(sel);
            }
        });
    });
</script>