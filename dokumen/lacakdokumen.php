<?
include "koneksi.php";
session_start();
$tgl_awal = date('Y-m-d');
$tgl_akhir = $tgl_awal;
$namauser = $_SESSION['namauser'];

if (isset($_POST['tgl_awal'])) {
    $tgl_awal = $_POST['tgl_awal'];
    $tgl_akhir = $tgl_awal;
}
if ($_POST['tgl_akhir'] != '') $tgl_akhir = $_POST['tgl_akhir'];

$lacakdokumen = mysql_query("select * from lacakdokumen 
    where CAST(tanggal_akses as date)>='$tgl_awal' 
    and CAST(tanggal_akses as date)<='$tgl_akhir' 
    and nama_pengakses !='' ");

$hapusTanpaNama = mysql_query("delete from lacakdokumen where nama_pengakses='' ");
?>

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<style>
body {
    background: #f7f9fb;
    font-family: "Segoe UI", sans-serif;
}

.title {
    color: #4a5568;
    font-weight: 600;
}

.card-filter {
    background: #fef6e4;
    /* pastel cream */
    border: 1px solid #f7e9c4;
}

.btn-pastel {
    background: #156a91ff;
    color: #fff;
    border: none;
}

.btn-pastel:hover {
    background: #24ade3ff;
}

/* Table Header Pastel */
table.dataTable thead {
    background-color: #a7d8c9;
    /* pastel green */
    color: #00332a;
}

/* Hover baris table */
table.dataTable tbody tr:hover td {
    background-color: #fff3cd !important;
}

.excel-link {
    background: #03801aff !important;
    color: #ffffff !important;
    border-radius: 6px;
    padding: 7px 12px;
    font-size: 14px;
    text-decoration: none;
}

.excel-link:hover {
    background: #59e673ff !important;
}
</style>

<div class="container mt-4">
    <h3 class="text-center title">REKAP LACAK DOKUMEN</h3>
    <hr>

    <!-- FILTER FORM -->
    <div class="card card-filter p-3 shadow-sm mb-4">
        <form method="post">
            <div class="row align-items-end">

                <div class="col-md-3">
                    <label class="form-label"><b>Tanggal Awal</b></label>
                    <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="form-control form-control-sm"
                        onfocus="this.showPicker()">
                </div>

                <div class="col-md-3">
                    <label class="form-label"><b>Tanggal Akhir</b></label>
                    <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="form-control form-control-sm"
                        onfocus="this.showPicker()">
                </div>

                <div class="col-md-3">
                    <button type="submit" name="submit" class="btn btn-pastel w-100 mt-3">
                        Lihat
                    </button>
                </div>

            </div>
        </form>
    </div>

    <?
    $countDokumen = mysql_num_rows($lacakdokumen);
    if ($countDokumen > 0) { ?>
    <a class="excel-link" href="lacakdokumen-excel.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>"
        target="_blank">
        Print Rekap Lacak Dokumen (.XLS)
    </a>
    <? } ?>

    <!-- TABLE -->
    <div class="table-responsive mt-3">
        <table id="tabelDokumen" class="table table-bordered table-striped table-sm">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Pengakses</th>
                    <th>Bidang</th>
                    <th>Tanggal Akses</th>
                    <th>Nama Dokumen</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?
                $no = 1;
                while ($dokumen = mysql_fetch_array($lacakdokumen)) {
                ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $dokumen['nama_pengakses']; ?></td>
                    <td><?= $dokumen['level_pengakses']; ?></td>
                    <td><?= $dokumen['tanggal_akses']; ?></td>
                    <td><?= $dokumen['nama_dokumen']; ?></td>
                    <td><?= $dokumen['keterangan']; ?></td>
                </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelDokumen').DataTable({
        "pageLength": 10,
        "ordering": true,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "paginate": {
                "previous": "Sebelumnya",
                "next": "Berikutnya"
            }
        }
    });
});
</script>