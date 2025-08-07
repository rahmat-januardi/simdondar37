<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config/dbi_connect.php";
$petugas = $_SESSION['namauser'];

// include "../config/dbi_connect.php";
// $petugas = "irawanDB";

$checkColumn = $dbi->query("SHOW COLUMNS FROM `dpengolahan` LIKE 'aPutar'");
if ($checkColumn->num_rows == 0) {
    // Jika kolom aPutar belum ada, jalankan ALTER TABLE
    $alterTable = "ALTER TABLE `dpengolahan` CHANGE `cara` `aPutar` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL";
    if ($dbi->query($alterTable) === TRUE) {
        // echo "Table altered successfully.";
    } else {
        // echo "Error altering table: " . $dbi->error;
    }
}
$checkColumn = $dbi->query("SHOW COLUMNS FROM `dpengolahan` LIKE 'aPisah'");
if ($checkColumn->num_rows == 0) {
    $alterTable1 = "ALTER TABLE `dpengolahan` ADD `aPisah` VARCHAR(100) NULL DEFAULT NULL AFTER `aPutar`;";
    $dbi->query($alterTable1);
}

/** versi 5.4 ++
$alterStatements = [
    'mulaiPutar' => "ALTER TABLE `dpengolahan` ADD `mulaiPutar` TIME NULL DEFAULT NULL AFTER `shift`;",
    'selesaiPutar' => "ALTER TABLE `dpengolahan` ADD `selesaiPutar` TIME NULL DEFAULT NULL AFTER `mulaiPutar`;",
    'mulaiPisah' => "ALTER TABLE `dpengolahan` ADD `mulaiPisah` TIME NULL DEFAULT NULL AFTER `selesaiPutar`;",
    'selesaiPisah' => "ALTER TABLE `dpengolahan` ADD `selesaiPisah` TIME NULL DEFAULT NULL AFTER `mulaiPisah`;",
];
*/

$alterStatements = array(
    'mulaiPutar' => "ALTER TABLE `dpengolahan` ADD `mulaiPutar` TIME NULL DEFAULT NULL AFTER `shift`;",
    'selesaiPutar' => "ALTER TABLE `dpengolahan` ADD `selesaiPutar` TIME NULL DEFAULT NULL AFTER `mulaiPutar`;",
    'mulaiPisah' => "ALTER TABLE `dpengolahan` ADD `mulaiPisah` TIME NULL DEFAULT NULL AFTER `selesaiPutar`;",
    'selesaiPisah' => "ALTER TABLE `dpengolahan` ADD `selesaiPisah` TIME NULL DEFAULT NULL AFTER `mulaiPisah`;",
);

foreach ($alterStatements as $column => $statement) {
    // Periksa apakah kolom sudah ada
    $checkColumn = $dbi->query("SHOW COLUMNS FROM `dpengolahan` LIKE '$column'");
    if ($checkColumn->num_rows == 0) {
        // Jika kolom belum ada, jalankan perintah ALTER TABLE
        if ($dbi->query($statement) !== TRUE) {
            // echo "Error menambahkan kolom $column: " . $dbi->error;
            // error_log("Error menambahkan kolom $column: " . $dbi->error);
        }
    }
}

$alterTable3 = "ALTER TABLE  `user_log` ADD  `unixID` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `aksi_user` ;
ALTER TABLE `user_log` ADD INDEX(`unixID`);";
$alterTable4 = "ALTER TABLE  `dpengolahan` CHANGE  `id`  `id1` INT( 11 ) NOT NULL ;";
$alterTable5 = "ALTER TABLE  `dpengolahan` ADD  `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;";
$alterTable6 = "ALTER TABLE  `dpengolahan` ADD  `tglupdate` DATETIME NULL DEFAULT NULL AFTER  `tgl` ;";
$alterTable7 = "ALTER TABLE `dpengolahan`  ADD `noseri` VARCHAR(50) NULL DEFAULT NULL AFTER `metode`;";
$alterTable8 = "ALTER TABLE `dpengolahan` CHANGE `selesai` `selesai` TIME NULL DEFAULT NULL;";
$alterTable9 = "ALTER TABLE `dpengolahan`  ADD `verifikator` VARCHAR(50) NULL DEFAULT NULL,  ADD `musnah` INT(1) NOT NULL COMMENT '1=Iya, 0=Tidak';";

if ($dbi->query($alterTable3) === TRUE && $dbi->query($alterTable4) === TRUE && $dbi->query($alterTable5) === TRUE && $dbi->query($alterTable6) === TRUE && $dbi->query($alterTable7) === TRUE && $dbi->query($alterTable8) === TRUE && $dbi->query($alterTable9) === TRUE) {
    // echo "Kolom noTrans berhasil diubah dan indeks ditambahkan.";
    // error_log("Kolom noTrans berhasil diubah dan indeks ditambahkan.");
} else {
    // echo "Error mengubah kolom noTrans atau menambahkan indeks: " . $dbi->error;
    // error_log("Error mengubah kolom noTrans atau menambahkan indeks: " . $dbi->error);
}

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
    $dateTime = new DateTime($inputTanggal);
    $split = explode('-', $inputTanggal);
    // return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];

    // Format tanggal dengan zona waktu WIB.
    // $formattedDate = $dateTime->format("d ") . $bulan[(int)$split[1]] . $dateTime->format(" Y - H:i") . " WIB";

    // tanpa zona waktu ..
    $formattedDate = $dateTime->format("d ") . $bulan[(int) $split[1]] . $dateTime->format(" Y - H:i");

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

        // if (!function_exists('createOptions')) {
        //     function createOptions($merk0, $jenis_kantong0, $tipe, $jarak, $selProduk)
        //     {
        //         $options = array();

        //         if ($merk0 == "HAEMONETIC" || $merk0 == "COM.TECH" || $merk0 == "AMICORE") {
        //             $options[] = "TC Apheresis";
        //             $options[] = "LP Apheresis";
        //             $options[] = "PRC Apheresis";
        //             $options[] = "FFP Konvalesen";
        //             // $tgl0 = strtotime($tgl_aftap0 . ' +365 days'); // jika perlu digunakan
        //         } elseif ($jenis_kantong0 == "2" && $tipe == "A") {
        //             $options[] = "PRC";
        //             $options[] = "WE";
        //             $options[] = "WB";
        //         } elseif ($jenis_kantong0 == "2" && $tipe == "B") {
        //             $options[] = "FP";
        //             $options[] = "LP";
        //             //if ($jarak > 24) {
        //             if ($jarak < 24) {
        //             	$options[] = "FFP";
        //                 $options[] = "FP24";
        //             } elseif ($jarak > 24 && $jarak < 72) {
        //                 $options[] = "FP72";
        //             }
        //         } elseif ($jenis_kantong0 == "3" && $tipe == "A") {
        //             $options[] = "PRC";
        //             $options[] = "WE";
        //         } elseif ($jenis_kantong0 == "3" && $tipe == "B") {
        //             $options[] = "TC";
        //         } elseif ($jenis_kantong0 == "3" && $tipe == "C") {
        //             $options[] = "FP";
        //             $options[] = "LP";
        //             $options[] = "AHF";
        //             //if ($jarak > 24) {
        //             if ($jarak < 24) {
        //             	$options[] = "FFP";
        //                 // $options[] = "FP24";
        //             } elseif ($jarak > 24 && $jarak < 72) {
        //                 $options[] = "FP72";
        //             }
        //         } elseif ($jenis_kantong0 == "4" && $tipe == "A") {
        //             $options[] = "PRC";
        //             $options[] = "PRC Leucodepletet";
        //             $options[] = "WB Leucodepletet";
        //         } elseif ($jenis_kantong0 == "4" && $tipe == "B") {
        //             $options[] = "TC";
        //             $options[] = "TC Leucodepletet";
        //         } elseif ($jenis_kantong0 == "4" && $tipe == "C") {
        //             $options[] = "LP";
        //             $options[] = "LP Leucodepletet";
        //             //if ($jarak > 24) {
        //             if ($jarak < 24) {
        //             	$options[] = "FFP";
        //             	$options[] = "FFP Leucodepletet";
        //                 // $options[] = "FP24";
        //             } elseif ($jarak > 24 && $jarak < 72) {
        //                 $options[] = "FP72";
        //             }
        //         } elseif ($jenis_kantong0 == "4" && $tipe == "D") {
        //             $options[] = "BC";
        //         }

        //         // foreach ($options as $option) {
        //         //     echo "<option value=\"$option\">$option</option>\n";
        //         // }
        //         foreach ($options as $option) {
        //             $selected = ($option == $selProduk) ? "selected" : "";
        //             echo "<option value=\"$option\" $selected>$option</option>\n";
        //         }
        //     }
        // }

        if (!function_exists('createOptions')) {
            function createOptions($merk0, $volasal0, $jenis_kantong0, $tipe, $selProduk, $dbi)
            {
                // echo "<!-- DEBUG [createOptions]:
                // merk0 = $merk0,
                // jenis_kantong0 = $jenis_kantong0,
                // tipe = $tipe,
                // selProduk = $selProduk -->";

                $options = array();

                // Ambil data master_kantong berdasarkan ID
                $sql = "SELECT pr_utama, pr_s1, pr_s2, pr_s3, pr_s4, pr_s5, pr_s6, pr_s7 
                FROM master_kantong 
                WHERE merk = '$merk0' AND vol = '$volasal0' AND jenis = '$jenis_kantong0'";
                $result = $dbi->query($sql);

                $pr_utama = $pr_s1 = $pr_s2 = $pr_s3 = $pr_s4 = $pr_s5 = $pr_s6 = $pr_s7 = null;

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // echo "<!-- DEBUG: row fetched from master_kantong: " . json_encode($row) . " -->";

                        $pr_utama = $row['pr_utama'];
                        $pr_s1 = $row['pr_s1'];
                        $pr_s2 = $row['pr_s2'];
                        $pr_s3 = $row['pr_s3'];
                        $pr_s4 = $row['pr_s4'];
                        $pr_s5 = $row['pr_s5'];
                        $pr_s6 = $row['pr_s6'];
                        $pr_s7 = $row['pr_s7'];
                    }
                }

                // Petakan tipe ke kolom
                switch ($tipe) {
                    case 'A':
                        $produkString = $pr_utama;
                        break;
                    case 'B':
                        $produkString = $pr_s1;
                        break;
                    case 'C':
                        $produkString = $pr_s2;
                        break;
                    case 'D':
                        $produkString = $pr_s3;
                        break;
                    case 'E':
                        $produkString = $pr_s4;
                        break;
                    case 'F':
                        $produkString = $pr_s5;
                        break;
                    case 'G':
                        $produkString = $pr_s6;
                        break;
                    case 'H':
                        $produkString = $pr_s7;
                        break;
                    default:
                        $produkString = '';
                        break;
                }

                // echo "<!-- DEBUG: produkString = $produkString -->";

                // Ubah produk menjadi array, bersihkan, dan hilangkan duplikat
                if (!empty($produkString)) {
                    $produkArray = array_unique(array_map('trim', explode(',', $produkString)));
                    foreach ($produkArray as $option) {
                        $selected = ($option == $selProduk) ? "selected" : "";
                        echo "<option value=\"$option\" $selected>$option</option>\n";
                        $options[] = $option;
                    }
                }
            }
        }


        createOptions($merk0, $volasal0, $jenis_kantong0, $tipe, $selProduk, $dbi);

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
        echo "<td>" . formatTanggal($row["tglAftap"]) . "</td>";
        echo "<td>" . $row["goldarah"] . " (" . $row["rhesus"] . ")</td>";
        echo "<td>" . $jK . "</td>";
        // echo "<td>
        // <select class='custom-select' name='produk[]'>
        //     <option value='-'>-- Pilih Produk --</option>";
        echo "<td>
        <select class='custom-select' style='min-width:70px;' name='produk[]' onchange='updateFields(this)'>";
        // DEBUG
        // echo "<!-- DEBUG: merk0 = $merk0 -->";
        // echo "<!-- DEBUG: volasal0 = $volasal0 -->";
        // echo "<!-- DEBUG: jenis_kantong0 = $jenis_kantong0 -->";
        // echo "<!-- DEBUG: tipe = $tipe -->";

        createOptions($merk0, $volasal0, $jenis_kantong0, $tipe, $selProduk, $dbi);

        echo "</select>
            </td>";
        echo "<td>
                <input id='ed_produk_$no' style='text-align: center' type='text' name='ed_produk[]' value='" . $row["ed_produk"] . "'>
                </td>";
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
        echo "<td>± 
                <input id='volume_$no' style='text-align: center' type='text' name='volume[]' value='" . $pVol . "' size='1'/> cc</td>";
        echo "<td>
            <select class='custom-select' name='metode[]' style='min-width:70px;'>
                <option value='0'>Manual</option>
                <option value='1'>Otomatis</option>
            </select>
            </td>";
        echo "<td>" . $row["aPisah"] . "</td>";
        echo "<td>" . substr($row["mulaiPisah"], 0, 5) . "</td>";
        echo "<td>" . substr($row["selesaiPisah"], 0, 5) . "</td>";

        // echo "<td>
        // <input type='hidden' name='bstatus[$index]' value='0'>
        // <label class='switch'>
        //     <input type='checkbox' class='bstatus-checkbox' name='bstatus[$index]' value='1' " . ($row['bstatus'] == '1' ? 'checked' : '') . ">
        //     <span class='slider'>
        //     <span class='switch-label switch-label-off'>Tidak</span>
        //     <span class='switch-label switch-label-on'>Iya</span>
        //     </span>
        // </label>
        // </td>";

        echo "<td style='text-align: center;'>
        <label class='slider-label'>Tidak</label>
        <input type='range' class='bstatus-slider' name='bstatus[$index]' min='0' max='1' step='1' value='" . $row['bstatus'] . "'>
        <label class='slider-label'>Iya</label>
        </td>";

        echo "<td>
        <input style='text-align: center;min-width:40px;' type='text' name='bsuhu[]' value='" . $row["bsuhu"] . "' size='2'>
        </td>";
        echo "</tr>";

        $index++;
    }
} else {
    echo "<tr>";
    echo "<td colspan='19'><b>TIDAK ADA DATA</b> untuk ditampilkan.</td>";
    echo "</tr>";
}
?>
<script>
    function updateFields(selectElement) {
        var row = selectElement.closest('tr');
        var selectedValue = selectElement.value;
        var noKantong = row.querySelector('input[name="nK[]"]').value;

        $.ajax({
            url: 'modul/pengolahan/pengolahanOnChange.php', // URL ke file PHP yang memproses permintaan
            // url: 'pengolahanOnChange.php', // URL ke file PHP yang memproses permintaan
            type: 'POST',
            data: {
                produk: selectedValue,
                jKantong: noKantong
            },
            success: function (response) {
                // console.log("Selected product:", selectedValue);

                //logging
                // console.log(response);
                var data = JSON.parse(response);
                // console.log(data);

                // Update nilai pada input fields
                var edProdukInput = row.querySelector('[name="ed_produk[]"]');
                var pcepatInput = row.querySelector('[name="pcepat[]"]');
                var psuhuInput = row.querySelector('[name="psuhu[]"]');
                var volumeInput = row.querySelector('[name="volume[]"]');

                // Parsing response JSON
                var data = JSON.parse(response);

                // kalau parsing json error maka tidak akan mengupdate field
                if (!data.error) {
                    // Update values
                    if (edProdukInput) {
                        edProdukInput.value = data.tglEd;
                    }
                    if (pcepatInput) {
                        pcepatInput.value = data.pcepat;
                    }
                    if (psuhuInput) {
                        psuhuInput.value = data.psuhu;
                    }
                    if (volumeInput) {
                        volumeInput.value = data.volume;
                    }
                } else {
                    console.log(data.produk);
                }
            }
        });
    }

    function deleteRow(button) {
        var row = button.closest('tr');
        var id = row.querySelector('input[name="idOlah"]').value; // Mendapatkan idOlah
        var noKantong = row.querySelector('input[name="nK[]"]').value; // Mendapatkan noKantong

        // Set nomor kantong di dalam modal
        document.getElementById('modalNoKantong').textContent = noKantong;

        // Tampilkan modal
        $('#confirmDeleteModal').modal('show');

        // Set up tindakan untuk tombol "Hapus" di modal
        document.getElementById('confirmDeleteButton').onclick = function () {
            // Lanjutkan dengan penghapusan setelah konfirmasi
            $.ajax({
                url: 'modul/pengolahan/hapusPengolahanTemp.php',
                // url: 'hapusPengolahanTemp.php',
                type: 'POST',
                data: {
                    id: id
                },
                success: function (response) {
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
                error: function (xhr, status, error) {
                    alert('Terjadi kesalahan saat menghapus data: ' + error);
                    $('#confirmDeleteModal').modal('hide');
                }
            });
        };
    }
</script>
