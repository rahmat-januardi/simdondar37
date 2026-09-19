<?php

/*
|--------------------------------------------------------------------------
| SETUP
|--------------------------------------------------------------------------
*/
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session di-start di sini sendiri (aman dipanggil dobel / dari include).
if (session_id() == '') {
    session_start();
}

include(__DIR__ . '/../config/db_connect.php');

$lv0 = 'pmi' . (isset($_SESSION['leveluser']) ? $_SESSION['leveluser'] : '');


/*
|--------------------------------------------------------------------------
| HELPER JSON RESPONSE
|--------------------------------------------------------------------------
*/
function jsonResponse($success, $message, $data = array())
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'data'    => $data,
    ));
    exit;
}


/*
|--------------------------------------------------------------------------
| ENDPOINT AJAX
|--------------------------------------------------------------------------
*/
if (isset($_POST['action'])) {

    // ---------- CEK TRANSAKSI ----------
    if ($_POST['action'] === 'cek_transaksi') {

        $notrans = isset($_POST['notrans']) ? trim($_POST['notrans']) : '';

        if ($notrans === '') {
            jsonResponse(false, 'Nomor transaksi belum diisi.');
        }

        $safe = mysql_real_escape_string($notrans);

        $result = mysql_query("SELECT NoTrans FROM htransaksi WHERE NoTrans = '$safe' LIMIT 1");

        if (!$result) {
            jsonResponse(false, 'Query database gagal.<br><br><strong>MySQL Error:</strong><br>' .
                htmlspecialchars(mysql_error(), ENT_QUOTES, 'UTF-8'));
        }

        $data = mysql_fetch_assoc($result);

        if (!$data) {
            jsonResponse(false, 'Nomor transaksi <strong>' .
                htmlspecialchars($notrans, ENT_QUOTES, 'UTF-8') .
                '</strong> tidak ditemukan di tabel htransaksi.');
        }

        jsonResponse(true, 'Nomor transaksi ditemukan.', array('NoTrans' => $data['NoTrans']));
    }

    // ---------- UPDATE SEMUA TRANSAKSI ----------
    if ($_POST['action'] === 'update_transaksi') {

        $list = isset($_POST['notrans_list']) ? $_POST['notrans_list'] : array();

        if (!is_array($list) || count($list) === 0) {
            jsonResponse(false, 'Belum ada nomor transaksi yang akan diupdate.');
        }

        $clean = array();
        foreach ($list as $n) {
            $n = trim($n);
            if ($n !== '') {
                $clean[] = $n;
            }
        }
        $clean = array_values(array_unique($clean));

        if (count($clean) === 0) {
            jsonResponse(false, 'Nomor transaksi tidak valid.');
        }

        mysql_query('START TRANSACTION');

        $berhasil = array();
        $gagal    = array();

        foreach ($clean as $notrans) {

            $safe = mysql_real_escape_string($notrans);

            $cek = mysql_query("SELECT NoTrans FROM htransaksi WHERE NoTrans = '$safe' LIMIT 1");

            if (!$cek || !mysql_fetch_assoc($cek)) {
                $gagal[] = array(
                    'notrans' => $notrans,
                    'alasan'  => $cek ? 'Nomor transaksi tidak ditemukan.' : ('Query gagal: ' . mysql_error()),
                );
                continue;
            }

            $upd = mysql_query("
                UPDATE htransaksi
                SET Pengambilan = '-', Status = 0, jnsperiksa = NULL, cek_hb = 0, cek_dokter = 0
                WHERE NoTrans = '$safe'
                LIMIT 1
            ");

            if (!$upd) {
                $gagal[] = array(
                    'notrans' => $notrans,
                    'alasan'  => 'Gagal update database: ' . mysql_error(),
                );
                continue;
            }

            $berhasil[] = $notrans;
        }

        if (count($gagal) > 0) {
            mysql_query('ROLLBACK');

            $detail = array();
            foreach ($gagal as $g) {
                $detail[] = '<strong>' . htmlspecialchars($g['notrans'], ENT_QUOTES, 'UTF-8') . '</strong>: ' .
                    htmlspecialchars($g['alasan'], ENT_QUOTES, 'UTF-8');
            }

            jsonResponse(false, 'Proses update dibatalkan karena ada transaksi yang gagal.<br><br>' .
                implode('<br>', $detail) . '<br><br><strong>Tidak ada perubahan yang disimpan.</strong>',
                array('berhasil' => $berhasil, 'gagal' => $gagal));
        }

        mysql_query('COMMIT');

        jsonResponse(true, 'Sebanyak <strong>' . count($berhasil) . '</strong> nomor transaksi berhasil diperbarui.',
            array('berhasil' => $berhasil));
    }

    jsonResponse(false, 'Aksi tidak dikenali.');
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Update Data Transaksi Donasi</title>
<script src="js/jquery.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    :root {
        --accent: #2f9e74;
        --accent-dark: #22795a;
        --danger: #d64545;
        --ink: #1f2937;
        --muted: #6b7280;
        --border: #e5e7eb;
        --bg: #f6f8fa;
    }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: var(--bg);
        color: var(--ink);
    }
    .wrap {
        max-width: 760px;
        margin: 0 auto;
        padding: 32px 20px 60px;
    }
    h1 {
        font-size: 20px;
        margin: 0 0 4px;
    }
    .subtitle {
        color: var(--muted);
        font-size: 13.5px;
        margin-bottom: 24px;
    }
    .card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .card-header {
        padding: 14px 18px;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid var(--border);
        background: #fafbfc;
    }
    .card-body { padding: 18px; }

    .input-row { display: flex; gap: 10px; }
    input#notrans {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }
    input#notrans:focus { border-color: var(--accent); }

    button {
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        cursor: pointer;
        transition: opacity .15s;
    }
    button:disabled { opacity: .45; cursor: not-allowed; }
    button:not(:disabled):hover { opacity: .9; }

    .btn-primary { background: var(--accent); color: #fff; }
    .btn-success { background: var(--accent); color: #fff; width: 100%; padding: 12px; font-size: 15px; }
    .btn-danger { background: #fdecec; color: var(--danger); padding: 6px 12px; font-size: 12.5px; }

    table { width: 100%; border-collapse: collapse; }
    thead th {
        text-align: left;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: var(--muted);
        padding: 10px 12px;
        border-bottom: 1px solid var(--border);
    }
    tbody td {
        padding: 12px;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
    }
    tbody tr:last-child td { border-bottom: none; }
    .empty {
        text-align: center;
        color: var(--muted);
        padding: 36px 12px;
        font-size: 14px;
    }

    .bottom-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
        border-top: 1px solid var(--border);
        background: #fafbfc;
    }
    .counter { font-size: 13px; color: var(--muted); }
    .counter strong { color: var(--ink); font-size: 15px; }
</style>
</head>
<body>

<div class="wrap">

    <h1>Update Data Transaksi Donasi</h1>
    <div class="subtitle">Tambahkan satu atau beberapa nomor transaksi, lalu perbarui sekaligus.</div>

    <div class="card">
        <div class="card-header">Tambah Nomor Transaksi</div>
        <div class="card-body">
            <div class="input-row">
                <input type="text" id="notrans" placeholder="Contoh: DG220826-317P-0001" autocomplete="off">
                <button type="button" id="btnTambah" class="btn-primary">+ Tambah</button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Transaksi</div>
        <table>
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>No. Transaksi</th>
                    <th width="90">Aksi</th>
                </tr>
            </thead>
            <tbody id="tbodyList"></tbody>
        </table>
        <div class="bottom-action">
            <div class="counter">Total transaksi: <strong id="totalTransaksi">0</strong></div>
            <button type="button" id="btnUpdate" class="btn-success" style="width:auto;" disabled>
                &#10003; Update Semua Transaksi
            </button>
        </div>
    </div>

</div>

<script>
(function () {

    var ajaxUrl = 'modul/edit_mcu.php';

    // Daftar transaksi yang mau diupdate - cukup array string NoTrans.
    var daftar = [];

    var elNotrans   = document.getElementById('notrans');
    var elBtnTambah = document.getElementById('btnTambah');
    var elBtnUpdate = document.getElementById('btnUpdate');
    var elTbody     = document.getElementById('tbodyList');
    var elTotal     = document.getElementById('totalTransaksi');

    function el(tag, className, text) {
        var node = document.createElement(tag);
        if (className) node.className = className;
        if (typeof text !== 'undefined') node.textContent = text;
        return node;
    }

    function setDisabled(node, disabled) {
        node.disabled = disabled;
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER TABEL (murni DOM, tanpa jQuery, supaya tidak tergantung
    | fitur jQuery versi tertentu yang mungkin tidak didukung)
    |--------------------------------------------------------------------------
    */
    function renderTable() {
        // Kosongkan tbody
        while (elTbody.firstChild) {
            elTbody.removeChild(elTbody.firstChild);
        }

        if (daftar.length === 0) {
            var trEmpty = document.createElement('tr');
            var tdEmpty = document.createElement('td');
            tdEmpty.colSpan = 3;
            tdEmpty.className = 'empty';
            tdEmpty.textContent = 'Belum ada nomor transaksi yang ditambahkan.';
            trEmpty.appendChild(tdEmpty);
            elTbody.appendChild(trEmpty);
        } else {
            for (var i = 0; i < daftar.length; i++) {
                var notrans = daftar[i];

                var tr = document.createElement('tr');

                var tdNomor = document.createElement('td');
                tdNomor.textContent = String(i + 1);
                tr.appendChild(tdNomor);

                var tdNotrans = document.createElement('td');
                var strong = document.createElement('strong');
                strong.textContent = notrans;
                tdNotrans.appendChild(strong);
                tr.appendChild(tdNotrans);

                var tdAksi = document.createElement('td');
                var btnHapus = document.createElement('button');
                btnHapus.type = 'button';
                btnHapus.className = 'btn-danger';
                btnHapus.textContent = 'Hapus';
                (function (idx) {
                    btnHapus.addEventListener('click', function () {
                        hapusTransaksi(idx);
                    });
                })(i);
                tdAksi.appendChild(btnHapus);
                tr.appendChild(tdAksi);

                elTbody.appendChild(tr);
            }
        }

        elTotal.textContent = String(daftar.length);
        setDisabled(elBtnUpdate, daftar.length === 0);
    }

    function hapusTransaksi(idx) {
        var notrans = daftar[idx];
        if (typeof notrans === 'undefined') return;

        Swal.fire({
            icon: 'question',
            title: 'Hapus transaksi?',
            html: 'Nomor <strong>' + escapeHtml(notrans) + '</strong> akan dihapus dari daftar sementara.',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d64545',
            reverseButtons: true
        }).then(function (result) {
            if (!result.isConfirmed) return;

            daftar.splice(idx, 1);
            renderTable();
        });
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /*
    |--------------------------------------------------------------------------
    | BANGUN QUERY STRING MANUAL untuk notrans_list[]
    |--------------------------------------------------------------------------
    |
    | Tidak mengandalkan serialisasi array otomatis dari jQuery ($.ajax
    | data:{}), karena versi jQuery yang dipakai di project ini terbukti
    | tidak konsisten menyerialisasikan array ke format "key[]=val" yang
    | dibutuhkan PHP untuk membaca $_POST sebagai array. Jadi string
    | POST-nya kita rakit sendiri secara eksplisit.
    |--------------------------------------------------------------------------
    */
    function buildUpdatePostData(list) {
        var parts = ['action=' + encodeURIComponent('update_transaksi')];

        for (var i = 0; i < list.length; i++) {
            parts.push('notrans_list[]=' + encodeURIComponent(list[i]));
        }

        return parts.join('&');
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH
    |--------------------------------------------------------------------------
    */
    function tambahTransaksi() {
        var notrans = elNotrans.value.replace(/^\s+|\s+$/g, '');

        if (notrans === '') {
            Swal.fire({ icon: 'warning', title: 'Nomor transaksi belum diisi', confirmButtonText: 'Mengerti' });
            elNotrans.focus();
            return;
        }

        if (daftar.indexOf(notrans) !== -1) {
            Swal.fire({ icon: 'info', title: 'Sudah ada di daftar', confirmButtonText: 'OK' });
            elNotrans.value = '';
            elNotrans.focus();
            return;
        }

        setDisabled(elBtnTambah, true);

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: { action: 'cek_transaksi', notrans: notrans },

            success: function (res) {
                if (!res.success) {
                    Swal.fire({ icon: 'error', title: 'Transaksi tidak ditemukan', html: res.message, confirmButtonText: 'Tutup' });
                    return;
                }

                daftar.push(res.data.NoTrans);
                renderTable();

                elNotrans.value = '';
                elNotrans.focus();

                Swal.fire({
                    icon: 'success',
                    title: 'Ditambahkan',
                    timer: 1100,
                    showConfirmButton: false
                });
            },

            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Request gagal',
                    html: 'Status HTTP: ' + xhr.status + (xhr.responseText ? ('<br><br><div style="text-align:left;background:#f8f9fa;padding:10px;border-radius:6px;font-size:11px;max-height:200px;overflow:auto;">' + escapeHtml(xhr.responseText) + '</div>') : ''),
                    width: 650,
                    confirmButtonText: 'Tutup'
                });
            },

            complete: function () {
                setDisabled(elBtnTambah, false);
            }
        });
    }

    elBtnTambah.addEventListener('click', tambahTransaksi);

    elNotrans.addEventListener('keypress', function (e) {
        if (e.which === 13 || e.keyCode === 13) {
            e.preventDefault();
            tambahTransaksi();
        }
    });

    /*
    |--------------------------------------------------------------------------
    | UPDATE SEMUA
    |--------------------------------------------------------------------------
    */
    elBtnUpdate.addEventListener('click', function () {
        if (daftar.length === 0) return;

        var jumlah = daftar.length;

        Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi Update',
            html: 'Anda akan memperbarui <strong>' + jumlah + '</strong> nomor transaksi sekaligus.' +
                '<br><br><div style="text-align:left;background:#f8fafc;padding:12px;border-radius:8px;">' +
                '<strong>Perubahan:</strong><br>Pengambilan = -<br>Status = 0<br>jnsperiksa = NULL<br>cek_hb = 0<br>cek_dokter = 0' +
                '</div><br><strong>Apakah Anda yakin ingin melanjutkan?</strong>',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update Semua',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2f9e74',
            reverseButtons: true
        }).then(function (result) {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Sedang memperbarui data',
                html: 'Mohon tunggu... memproses <strong>' + jumlah + '</strong> transaksi.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function () { Swal.showLoading(); }
            });

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: buildUpdatePostData(daftar),

                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Update Berhasil',
                            html: res.message,
                            confirmButtonText: 'Selesai',
                            confirmButtonColor: '#2f9e74'
                        }).then(function () {
                            daftar = [];
                            renderTable();
                            elNotrans.focus();
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Update Gagal',
                        html: res.message,
                        width: 650,
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#d64545'
                    });
                },

                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update AJAX Gagal',
                        html: 'Status HTTP: ' + xhr.status + (xhr.responseText ? ('<br><br><div style="text-align:left;background:#f8f9fa;padding:10px;border-radius:6px;font-size:11px;max-height:200px;overflow:auto;">' + escapeHtml(xhr.responseText) + '</div>') : ''),
                        width: 650,
                        confirmButtonText: 'Tutup'
                    });
                }
            });
        });
    });

    elNotrans.focus();
    renderTable();

})();
</script>

</body>
</html>
