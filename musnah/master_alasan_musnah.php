<?php
/*  musnah/master_alasan_musnah.php
    PHP 5.3 + mysql_* + Bootstrap 3
    Single file CRUD: list, tambah, edit, hapus, search, pagination, modal
    Dipanggil via router:
    } elseif ($_GET['module'] == 'master_musnah') {
        include "musnah/master_alasan_musnah.php";
    }
*/

include('config/dbi_connect.php');

// =========================
// Helper
// =========================
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

function module_url($extra = array())
{
    $params = array(
        'module' => 'master_musnah'
    );

    if (isset($_GET['q']) && trim($_GET['q']) != '') {
        $params['q'] = trim($_GET['q']);
    }

    if (isset($_GET['page']) && intval($_GET['page']) > 0) {
        $params['page'] = intval($_GET['page']);
    }

    foreach ($extra as $k => $v) {
        if ($v === null) {
            if (isset($params[$k])) {
                unset($params[$k]);
            }
        } else {
            $params[$k] = $v;
        }
    }

    return 'pmiadmin.php?' . http_build_query($params);
}

// =========================
// Inisialisasi
// =========================
$alert = '';
$open_modal = ''; // modalTambah / modalEdit
$add_nomor = '';
$add_alasan = '';

$edit_nomor_lama = '';
$edit_nomor = '';
$edit_alasan = '';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
    $page = 1;
}

$per_page = 10;

$q_max = mysql_query("SELECT MAX(nomor) AS max_nomor FROM alasan_musnah");
$d_max = mysql_fetch_assoc($q_max);

$nomor_baru = 1;
if ($d_max && $d_max['max_nomor'] !== null) {
    $nomor_baru = intval($d_max['max_nomor']) + 1;
}

// =========================
// Hapus data
// =========================
if (isset($_GET['act']) && $_GET['act'] == 'delete') {
    $nomor = isset($_GET['nomor']) ? trim($_GET['nomor']) : '';

    if ($nomor !== '') {
        $nomor_sql = mysql_real_escape_string($nomor);
        $hapus = mysql_query("DELETE FROM alasan_musnah WHERE nomor = '$nomor_sql'");
        if (!$hapus) {
            die('Gagal hapus: ' . mysql_error());
        }
    }

    header('Location: ' . module_url(array('msg' => 'deleted')));
    exit;
}

// =========================
// Simpan data baru
// =========================
if (isset($_POST['act']) && $_POST['act'] == 'save') {
    $add_nomor = isset($_POST['nomor']) ? trim($_POST['nomor']) : '';
    $add_alasan = isset($_POST['alasan']) ? trim($_POST['alasan']) : '';

    if ($add_nomor === '' || $add_alasan === '') {
        $alert = '<div class="alert alert-danger">Nomor dan alasan wajib diisi.</div>';
        $open_modal = 'modalTambah';
    } else {
        $nomor_sql = mysql_real_escape_string($add_nomor);
        $alasan_sql = mysql_real_escape_string($add_alasan);

        $cek = mysql_query("SELECT nomor FROM alasan_musnah WHERE nomor = '$nomor_sql' LIMIT 1");
        if (!$cek) {
            die('Gagal cek data: ' . mysql_error());
        }

        if (mysql_num_rows($cek) > 0) {
            $alert = '<div class="alert alert-danger">Nomor sudah ada.</div>';
            $open_modal = 'modalTambah';
        } else {
            $simpan = mysql_query("INSERT INTO alasan_musnah (nomor, alasan) VALUES ('$nomor_sql', '$alasan_sql')");
            if (!$simpan) {
                die('Gagal simpan: ' . mysql_error());
            }

            header('Location: ' . module_url(array('msg' => 'saved')));
            exit;
        }
    }
}

// =========================
// Update data
// =========================
if (isset($_POST['act']) && $_POST['act'] == 'update') {
    $edit_nomor_lama = isset($_POST['nomor_lama']) ? trim($_POST['nomor_lama']) : '';
    $edit_nomor = isset($_POST['nomor']) ? trim($_POST['nomor']) : '';
    $edit_alasan = isset($_POST['alasan']) ? trim($_POST['alasan']) : '';

    if ($edit_nomor_lama === '' || $edit_nomor === '' || $edit_alasan === '') {
        $alert = '<div class="alert alert-danger">Nomor dan alasan wajib diisi.</div>';
        $open_modal = 'modalEdit';
    } else {
        $nomor_lama_sql = mysql_real_escape_string($edit_nomor_lama);
        $nomor_sql = mysql_real_escape_string($edit_nomor);
        $alasan_sql = mysql_real_escape_string($edit_alasan);

        if ($edit_nomor_lama != $edit_nomor) {
            $cek = mysql_query("SELECT nomor FROM alasan_musnah WHERE nomor = '$nomor_sql' LIMIT 1");
            if (!$cek) {
                die('Gagal cek data: ' . mysql_error());
            }

            if (mysql_num_rows($cek) > 0) {
                $alert = '<div class="alert alert-danger">Nomor baru sudah dipakai data lain.</div>';
                $open_modal = 'modalEdit';
            } else {
                $update = mysql_query("UPDATE alasan_musnah SET nomor = '$nomor_sql', alasan = '$alasan_sql' WHERE nomor = '$nomor_lama_sql'");
                if (!$update) {
                    die('Gagal update: ' . mysql_error());
                }

                header('Location: ' . module_url(array('msg' => 'updated')));
                exit;
            }
        } else {
            $update = mysql_query("UPDATE alasan_musnah SET alasan = '$alasan_sql' WHERE nomor = '$nomor_lama_sql'");
            if (!$update) {
                die('Gagal update: ' . mysql_error());
            }

            header('Location: ' . module_url(array('msg' => 'updated')));
            exit;
        }
    }
}

// =========================
// Pesan status
// =========================
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'saved') {
        $alert = '<div class="alert alert-success">Data berhasil disimpan.</div>';
    } elseif ($_GET['msg'] == 'updated') {
        $alert = '<div class="alert alert-success">Data berhasil diupdate.</div>';
    } elseif ($_GET['msg'] == 'deleted') {
        $alert = '<div class="alert alert-success">Data berhasil dihapus.</div>';
    }
}

// =========================
// Query list + search + pagination
// =========================
$where = '';
if ($q !== '') {
    $q_sql = mysql_real_escape_string($q);
    $where = "WHERE nomor LIKE '%$q_sql%' OR alasan LIKE '%$q_sql%'";
}

$sql_count = mysql_query("SELECT COUNT(*) AS total FROM alasan_musnah $where");
if (!$sql_count) {
    die('Gagal hitung data: ' . mysql_error());
}

$row_count = mysql_fetch_assoc($sql_count);
$total_rows = isset($row_count['total']) ? intval($row_count['total']) : 0;
$total_pages = ($total_rows > 0) ? ceil($total_rows / $per_page) : 1;

if ($page > $total_pages) {
    $page = $total_pages;
}

$offset = ($page - 1) * $per_page;
if ($offset < 0) {
    $offset = 0;
}

$sql_data = mysql_query("SELECT nomor, alasan FROM alasan_musnah $where ORDER BY nomor ASC LIMIT $offset, $per_page");
if (!$sql_data) {
    die('Gagal ambil data: ' . mysql_error());
}

// =========================
// Ambil data edit dari klik tombol Edit
// =========================
if (isset($_GET['act']) && $_GET['act'] == 'edit' && isset($_GET['nomor'])) {
    $nomor_edit = trim($_GET['nomor']);
    if ($nomor_edit !== '') {
        $nomor_edit_sql = mysql_real_escape_string($nomor_edit);
        $q_edit = mysql_query("SELECT nomor, alasan FROM alasan_musnah WHERE nomor = '$nomor_edit_sql' LIMIT 1");
        if (!$q_edit) {
            die('Gagal ambil data edit: ' . mysql_error());
        }

        if (mysql_num_rows($q_edit) > 0) {
            $d_edit = mysql_fetch_assoc($q_edit);
            $edit_nomor_lama = $d_edit['nomor'];
            $edit_nomor = $d_edit['nomor'];
            $edit_alasan = $d_edit['alasan'];
            $open_modal = 'modalEdit';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Alasan Musnah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">

    <style>
    body {
        padding: 20px;
        background: #f7f7f7;
    }

    .panel {
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    }

    .table>thead>tr>th,
    .table>tbody>tr>td {
        vertical-align: middle !important;
    }

    .form-inline .form-group {
        margin-right: 8px;
    }

    .pagination {
        margin: 0;
    }

    .btn-space {
        margin-right: 5px;
    }

    .label-total {
        font-size: 13px;
        margin-left: 10px;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <h3 class="panel-title" style="line-height: 30px;">Data Alasan Musnah</h3>
            </div>
            <div class="panel-body">

                <?php echo $alert; ?>

                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTambah">
                            <span class="glyphicon glyphicon-plus"></span> Tambah Data
                        </button>
                        <span class="label label-info label-total">
                            Total Data: <?php echo intval($total_rows); ?>
                        </span>
                    </div>

                    <div class="col-md-6 text-right">
                        <form method="get" class="form-inline" style="display:inline-block;"
                            action="<?php echo e(module_url(array('q' => null, 'page' => null, 'msg' => null, 'act' => null, 'nomor' => null))); ?>">
                            <input type="hidden" name="module" value="master_musnah">
                            <div class="form-group">
                                <input type="text" name="q" class="form-control" placeholder="Cari nomor / alasan"
                                    value="<?php echo e($q); ?>">
                            </div>
                            <input type="hidden" name="page" value="1">
                            <button type="submit" class="btn btn-primary">
                                <span class="glyphicon glyphicon-search"></span> Cari
                            </button>
                            <a href="<?php echo e(module_url(array('q' => null, 'page' => 1, 'msg' => null, 'act' => null, 'nomor' => null))); ?>"
                                class="btn btn-default">
                                <span class="glyphicon glyphicon-refresh"></span> Reset
                            </a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="90">Nomor</th>
                                <th>Alasan</th>
                                <th width="170">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysql_num_rows($sql_data) > 0) {
                                while ($row = mysql_fetch_assoc($sql_data)) {
                                    $nomor_show = $row['nomor'];
                                    $alasan_show = $row['alasan'];
                            ?>
                            <tr>
                                <td><?php echo e($nomor_show); ?></td>
                                <td><?php echo e($alasan_show); ?></td>
                                <td>
                                    <a href="<?php echo e(module_url(array('act' => 'edit', 'nomor' => $nomor_show, 'q' => $q, 'page' => $page))); ?>"
                                        class="btn btn-warning btn-xs btn-space">
                                        <span class="glyphicon glyphicon-edit"></span> Edit
                                    </a>
                                    <a href="<?php echo e(module_url(array('act' => 'delete', 'nomor' => $nomor_show, 'q' => $q, 'page' => $page))); ?>"
                                        class="btn btn-danger btn-xs"
                                        onclick="return confirm('Yakin mau hapus data ini?');">
                                        <span class="glyphicon glyphicon-trash"></span> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="3" class="text-center">Belum ada data</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="text-muted" style="padding-top: 8px;">
                            Menampilkan <?php echo ($total_rows > 0 ? ($offset + 1) : 0); ?> -
                            <?php echo min($offset + $per_page, $total_rows); ?>
                            dari <?php echo intval($total_rows); ?> data
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <nav>
                            <ul class="pagination">
                                <?php
                                $prev_page = $page - 1;
                                $next_page = $page + 1;

                                if ($page > 1) {
                                    echo '<li><a href="' . e(module_url(array('page' => 1))) . '">&laquo; First</a></li>';
                                    echo '<li><a href="' . e(module_url(array('page' => $prev_page))) . '">&lsaquo; Prev</a></li>';
                                } else {
                                    echo '<li class="disabled"><span>&laquo; First</span></li>';
                                    echo '<li class="disabled"><span>&lsaquo; Prev</span></li>';
                                }

                                $start = $page - 2;
                                if ($start < 1) {
                                    $start = 1;
                                }

                                $end = $start + 4;
                                if ($end > $total_pages) {
                                    $end = $total_pages;
                                }

                                for ($i = $start; $i <= $end; $i++) {
                                    if ($i == $page) {
                                        echo '<li class="active"><span>' . intval($i) . '</span></li>';
                                    } else {
                                        echo '<li><a href="' . e(module_url(array('page' => $i))) . '">' . intval($i) . '</a></li>';
                                    }
                                }

                                if ($page < $total_pages) {
                                    echo '<li><a href="' . e(module_url(array('page' => $next_page))) . '">Next &rsaquo;</a></li>';
                                    echo '<li><a href="' . e(module_url(array('page' => $total_pages))) . '">Last &raquo;</a></li>';
                                } else {
                                    echo '<li class="disabled"><span>Next &rsaquo;</span></li>';
                                    echo '<li class="disabled"><span>Last &raquo;</span></li>';
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo e(module_url(array('q' => $q, 'page' => $page))); ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalTambahLabel">Tambah Alasan Musnah</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="act" value="save">

                        <div class="form-group">
                            <label>Nomor</label>
                            <input type="number" name="nomor" class="form-control" value="<?php echo e($nomor_baru); ?>"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Alasan</label>
                            <input type="text" name="alasan" class="form-control" value="<?php echo e($add_alasan); ?>"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="glyphicon glyphicon-floppy-disk"></span> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo e(module_url(array('q' => $q, 'page' => $page))); ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalEditLabel">Edit Alasan Musnah</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="act" value="update">
                        <input type="hidden" name="nomor_lama" value="<?php echo e($edit_nomor_lama); ?>">

                        <div class="form-group">
                            <label>Nomor</label>
                            <input type="number" name="nomor" class="form-control" value="<?php echo e($edit_nomor); ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Alasan</label>
                            <input type="text" name="alasan" class="form-control" value="<?php echo e($edit_alasan); ?>"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <span class="glyphicon glyphicon-edit"></span> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        <?php if ($open_modal == 'modalTambah') { ?>
        $('#modalTambah').modal('show');
        <?php } elseif ($open_modal == 'modalEdit') { ?>
        $('#modalEdit').modal('show');
        <?php } ?>
    });
    </script>
</body>

</html>