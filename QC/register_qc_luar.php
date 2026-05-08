<?php
include('clogin.php');
include('config/db_connect.php');

$namauser = $_SESSION['namauser'];

function getJenisLabel($jenis)
{
	$map = array(
		'1' => 'Single',
		'2' => 'Double',
		'3' => 'Triple',
		'4' => 'Quadruple',
		'6' => 'Pediatrik'
	);

	$jenis = trim((string)$jenis);
	return isset($map[$jenis]) ? $map[$jenis] : $jenis;
}

function renderTempRows($namauser)
{
	$namauser = mysql_real_escape_string($namauser);
	$no = 1;
	$html = '';

	$q = mysql_query("
        SELECT t.*, u.nama AS nama_utd
        FROM registrasi_luarqc_temp t
        LEFT JOIN utd u ON u.id = t.asal_utd
        WHERE t.user_input='$namauser'
        ORDER BY t.id ASC
    ");

	while ($d = mysql_fetch_assoc($q)) {
		$asalDisplay = !empty($d['nama_utd']) ? $d['nama_utd'] : $d['asal_utd'];

		$html .= "<tr>
            <td><input type='checkbox' name='pilih[]' value='" . htmlspecialchars($d['id']) . "'></td>
            <td>" . $no++ . "</td>
            <td>" . htmlspecialchars($d['nokantong']) . "</td>
            <td>" . htmlspecialchars($d['volume']) . "</td>
            <td>" . htmlspecialchars($d['merk']) . "</td>
            <td>" . htmlspecialchars(getJenisLabel($d['jenis'])) . "</td>
            <td>" . htmlspecialchars($asalDisplay) . "</td>
            <td>" . htmlspecialchars($d['produk']) . "</td>
            <td>" . htmlspecialchars($d['tglaftap']) . "</td>
            <td>" . htmlspecialchars($d['kadaluwarsa']) . "</td>
            <td>" . htmlspecialchars($d['tgl_pengolahan']) . "</td>
            <td>" . htmlspecialchars($d['goldarah']) . "</td>
            <td>" . htmlspecialchars($d['rhesus']) . "</td>
            <td>" . htmlspecialchars($d['pengirim']) . "</td>
        </tr>";
	}

	return $html;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>QC Dari Luar UTD</title>

    <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
    <link type="text/css" href="css/terima_qc_luar.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script type="text/javascript">
    function setFocus() {
        document.tambahkantong.nokantong.focus();
    }
    </script>
</head>

<body onload="setFocus()">
    <div class="page-wrap">
        <div class="page-card">
            <div class="page-header">
                <div class="header-flex">
                    <div>
                        <h1>QC Dari Luar UTD</h1>
                        <p>Input data lalu tekan Enter pada No Kantong untuk masuk ke tabel sementara.</p>
                    </div>
                    <div>
                        <a href="pmiqc.php?module=register_qc" class="swn_button_green">Kembali</a>
                    </div>
                </div>
            </div>

            <div class="page-body">
                <div id="notif" class="notif"></div>

                <form name="tambahkantong" id="form-qc" method="POST" action="javascript:void(0);">
                    <div class="main-grid">
                        <div class="form-box">
                            <div class="form-title">Data Input</div>

                            <div class="input-grid">
                                <div class="input-col">
                                    <div class="input-field">
                                        <label>Merk</label>
                                        <select name="merk" id="merk" class="select2 control">
                                            <option value="" selected>--Pilih Merk--</option>
                                            <?php
											$permintaan1 = "SELECT * FROM merk_kantong";
											$do1 = mysql_query($permintaan1);
											while ($data1 = mysql_fetch_assoc($do1)) {
											?>
                                            <option value="<?= htmlspecialchars($data1['mk_merk']) ?>">
                                                <?= htmlspecialchars($data1['mk_merk']) ?>
                                            </option>
                                            <?php } ?>
                                            <option value="lainnya">Lainnya...</option>
                                        </select>
                                        <input type="text" name="merk_lainnya" id="merk_lainnya"
                                            placeholder="Masukkan merk lainnya" style="display:none;"
                                            class="control sub-input">
                                    </div>

                                    <div class="input-field">
                                        <label>Jenis Kantong</label>
                                        <select name="jenis2" id="jenis2" class="control">
                                            <option value="1">Single</option>
                                            <option value="2">Double</option>
                                            <option value="3">Triple</option>
                                            <option value="4">Quadruple</option>
                                            <option value="6">Pediatrik</option>
                                        </select>
                                    </div>

                                    <div class="input-field">
                                        <label>Jenis Produk</label>
                                        <select name="produk" id="produk" class="select2 control">
                                            <option value="" selected>--Pilih Produk--</option>
                                            <?php
											$permintaan1 = "SELECT * FROM produk ORDER BY Nama DESC";
											$do1 = mysql_query($permintaan1);
											while ($data1 = mysql_fetch_assoc($do1)) {
											?>
                                            <option value="<?= htmlspecialchars($data1['Nama']) ?>">
                                                <?= htmlspecialchars($data1['Nama']) ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="row-two">
                                        <div class="input-field">
                                            <label>Golongan Darah</label>
                                            <select name="goldarah" id="goldarah" class="control">
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="O">O</option>
                                                <option value="AB">AB</option>
                                            </select>
                                        </div>

                                        <div class="input-field">
                                            <label>Rhesus</label>
                                            <select name="rh" id="rh" class="control">
                                                <option value="+">Positif</option>
                                                <option value="-">Negatif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-col">
                                    <div class="input-field">
                                        <label>Volume</label>
                                        <input type="text" name="volume" id="volume" class="control">
                                    </div>

                                    <div class="input-field">
                                        <label>Tgl Aftap</label>
                                        <input type="text" name="tglaftap" id="tglaftap" class="datetime control">
                                    </div>

                                    <div class="input-field">
                                        <label>Tgl Pengolahan</label>
                                        <input type="text" name="tglolah" id="tglolah" class="datetime control">
                                    </div>

                                    <div class="input-field">
                                        <label>Tgl Kadaluarsa</label>
                                        <input type="text" name="tglkad" id="tglkad" class="datetime control">
                                    </div>
                                </div>

                                <div class="input-col">
                                    <div class="input-field">
                                        <label>Asal Sampel</label>
                                        <select name="asal_sampel" id="asal_sampel" class="select2 control">
                                            <option value="" selected>--Pilih UDD--</option>
                                            <?php
											$ql = mysql_query("SELECT * FROM utd ORDER BY daerah ASC");
											while ($rowl1 = mysql_fetch_array($ql)) {
												echo "<option value='" . htmlspecialchars($rowl1['id']) . "'>" . htmlspecialchars($rowl1['nama']) . "</option>";
											}
											?>
                                            <option value="lainnya">Lainnya...</option>
                                        </select>
                                        <input type="text" name="asal_sampel_lainnya" id="asal_sampel_lainnya"
                                            placeholder="Masukkan asal sampel lainnya" style="display:none;"
                                            class="control sub-input">
                                    </div>

                                    <div class="input-field">
                                        <label>Nama Pengirim</label>
                                        <input type="text" name="pengirim" id="pengirim" class="control">
                                    </div>

                                    <div class="input-field" style="display: none;">
                                        <label>Jumlah Cetak Barcode</label>
                                        <input type="text" name="cetakkantong" id="cetakkantong" value="2"
                                            class="control">
                                    </div>

                                    <div class="input-field">
                                        <label>No Kantong</label>
                                        <input type="text" name="nokantong" id="nokantong"
                                            placeholder="Masukkan No.Kantong" class="control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-box">
                            <div class="table-title">Daftar Kantong</div>
                            <div class="table-wrap">
                                <table class="list" id="list-kantong">
                                    <thead>
                                        <tr class="field">
                                            <th></th>
                                            <th>No</th>
                                            <th>No Kantong</th>
                                            <th>Volume</th>
                                            <th>Merk</th>
                                            <th>Jenis</th>
                                            <th>Asal UTD</th>
                                            <th>Produk</th>
                                            <th>Tgl Aftap</th>
                                            <th>Tgl Kadaluarsa</th>
                                            <th>Tgl Pengolahan</th>
                                            <th>Gol Darah</th>
                                            <th>Rhesus</th>
                                            <th>Nama Pengirim</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-kantong">
                                        <?= renderTempRows($namauser); ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="actions" id="table-actions"
                                style="display:none; width:100%; justify-content:flex-end;">
                                <input type="button" value="Simpan" onclick="simpanFinal()" class="swn_button_blue">
                                <input type="button" value="Delete Row" onclick="deleteRow('list-kantong')"
                                    class="swn_button_red">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function showNotif(type, msg) {
        $('#notif').removeClass('sukses gagal').addClass(type).html(msg).show();
    }

    function hideNotif() {
        $('#notif').hide().text('');
    }

    function toggleTableActions() {
        var rowCount = $('#tbody-kantong tr').length;
        if (rowCount > 0) {
            $('#table-actions').css('display', 'flex');
        } else {
            $('#table-actions').hide();
        }
    }

    function deleteRow(tableID) {
        var ids = [];

        $('#tbody-kantong input[type="checkbox"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            showNotif('gagal', 'Pilih data yang mau dihapus terlebih dahulu.');
            return;
        }

        if (!confirm('Hapus data yang dipilih dari tabel sementara?')) {
            return;
        }

        $.ajax({
            url: 'QC/ajax_delete_temp.php',
            type: 'POST',
            dataType: 'json',
            data: {
                ids: ids
            },
            success: function(res) {
                console.log(res);

                if (res.status === 'success') {
                    $('#tbody-kantong').html(res.html);
                    toggleTableActions();
                    showNotif('sukses', res.msg);
                } else {
                    showNotif('gagal', res.msg);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                showNotif('gagal', 'AJAX ERROR: ' + status + ' | ' + error);
            }
        });
    }

    function getFormData() {
        return {
            merk: $('#merk').val(),
            merk_lainnya: $('#merk_lainnya').val(),
            jenis2: $('#jenis2').val(),
            produk: $('#produk').val(),
            goldarah: $('#goldarah').val(),
            rh: $('#rh').val(),
            volume: $('#volume').val(),
            asal_sampel: $('#asal_sampel').val(),
            asal_sampel_lainnya: $('#asal_sampel_lainnya').val(),
            pengirim: $('#pengirim').val(),
            tglaftap: $('#tglaftap').val(),
            tglkad: $('#tglkad').val(),
            tglolah: $('#tglolah').val(),
            cetakkantong: $('#cetakkantong').val(),
            nokantong: $('#nokantong').val()
        };
    }

    function validasiForm(data) {
        if (!data.merk) return 'Merk belum dipilih';
        if (!data.produk) return 'Produk belum dipilih';
        if (!data.volume) return 'Volume belum diisi';
        if (!data.asal_sampel) return 'Asal sampel belum dipilih';
        if (!data.pengirim) return 'Nama pengirim belum diisi';
        if (!data.tglaftap) return 'Tgl Aftap belum diisi';
        if (!data.tglkad) return 'Tgl Kadaluarsa belum diisi';
        if (!data.tglolah) return 'Tgl Pengolahan belum diisi';
        if (!data.nokantong) return 'No kantong belum diisi';
        return '';
    }

    function simpanTemp() {
        hideNotif();

        var data = getFormData();
        var cek = validasiForm(data);
        if (cek !== '') {
            showNotif('gagal', cek);
            return;
        }

        $.ajax({
            url: 'QC/ajax_simpan_temp.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(res) {
                console.log(res);

                if (res.status === 'success') {
                    $('#tbody-kantong').html(res.html);
                    toggleTableActions();
                    showNotif('sukses', res.msg);
                    $('#nokantong').val('').focus();
                } else {
                    showNotif('gagal', res.msg);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                showNotif('gagal', 'AJAX ERROR: ' + status + ' | ' + error);
            }
        });
    }

    function simpanFinal() {
        hideNotif();

        if (!confirm('Simpan semua data ke registrasi QC?')) {
            return;
        }

        $.ajax({
            url: 'QC/ajax_simpan_final_qc_luar.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function(res) {
                console.log('RESP FINAL:', res);
                console.log('HTML FINAL:', res.html);

                if (res.status === 'success' || res.status === 'partial') {
                    // update isi list sesuai sisa data temp
                    $('#tbody-kantong').html(res.html || '');

                    // pastikan tombol tampil/hilang sesuai isi tabel
                    toggleTableActions();

                    // kalau masih ada data gagal, tampilkan daftar gagal
                    if (res.status === 'partial' && res.gagal && res.gagal.length > 0) {
                        var html = '<div>' + res.msg + '</div>';
                        html += '<div style="margin-top:8px;font-weight:700;">Data yang gagal:</div>';
                        html += '<ul style="margin:6px 0 0 18px;padding:0;">';

                        for (var i = 0; i < res.gagal.length; i++) {
                            html += '<li><b>' + res.gagal[i].nokantong + '</b> - ' + res.gagal[i].alasan +
                                '</li>';
                        }

                        html += '</ul>';

                        $('#notif').removeClass('sukses gagal').addClass('gagal').html(html).show();
                    } else {
                        showNotif('sukses', res.msg);
                    }

                    return;
                }

                showNotif('gagal', res.msg);
            },
            error: function(xhr, status, error) {
                console.log('STATUS:', status);
                console.log('ERROR:', error);
                console.log('RESPONSE:', xhr.responseText);
                showNotif('gagal', 'AJAX ERROR: ' + status + ' | ' + error);
            }
        });
    }



    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            minimumResultsForSearch: 0
        });

        flatpickr(".datetime", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });

        $('#merk').on('change', function() {
            if ($(this).val() === 'lainnya') {
                $('#merk_lainnya').show().focus();
            } else {
                $('#merk_lainnya').hide().val('');
            }
        });

        $('#asal_sampel').on('change', function() {
            if ($(this).val() === 'lainnya') {
                $('#asal_sampel_lainnya').show().focus();
            } else {
                $('#asal_sampel_lainnya').hide().val('');
            }
        });

        $('#nokantong').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                simpanTemp();
            }
        });

        toggleTableActions();
    });
    </script>
</body>

</html>