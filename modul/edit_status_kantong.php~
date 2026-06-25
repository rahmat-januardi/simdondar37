<script language="javascript">
<!--
function setFocus(){document.editstatus.nokantong.focus();}
// -->
</script>
<?php
include('clogin.php');
include('config/db_connect.php');
//var_dump($_SESSION);
$today = date("Y-m-d");
$namauser = $_SESSION['namauser'];

// Inisialisasi session
if (!isset($_SESSION['list_kantong_edit'])) {
    $_SESSION['list_kantong_edit'] = array();
}

// Proses tambah ke list
if (isset($_POST['add'])) {
    $nkt = strtoupper(trim($_POST['nokantong']));
    if (!empty($nkt)) {
        $nkt0 = mysql_real_escape_string($nkt);
        $cek = mysql_query("SELECT noKantong FROM stokkantong WHERE noKantong='$nkt'");
        if (mysql_num_rows($cek) > 0) {
            if (!in_array($nkt, $_SESSION['list_kantong_edit'])) {
                $_SESSION['list_kantong_edit'][] = $nkt;
                echo "<div class=\"info\">Kantong $nkt telah ditambahkan ke list untuk edit status.</div>";
            } else {
                echo "<div class=\"warning\">Kantong $nkt sudah ada di list.</div>";
            }
        } else {
            echo "<div class=\"warning\">Kantong yang anda maksud tidak ada.</div>";
        }
    } else {
        echo "<div class=\"warning\">Masukkan nomor kantong!</div>";
    }
}

// Proses hapus dari list
if (isset($_POST['hapus'])) {
    $index = $_POST['index'];
    if (isset($_SESSION['list_kantong_edit'][$index])) {
        $removed = $_SESSION['list_kantong_edit'][$index];
        unset($_SESSION['list_kantong_edit'][$index]);
        $_SESSION['list_kantong_edit'] = array_values($_SESSION['list_kantong_edit']);
        echo "<div class=\"info\">Kantong $removed telah dihapus dari list.</div>";
    }
}

// Proses update
if (isset($_POST['proses'])) {
    if (!empty($_SESSION['list_kantong_edit'])) {
        foreach ($_SESSION['list_kantong_edit'] as $nkt) {
            $update = mysql_query("UPDATE stokkantong SET 
                Status = 0, produk = NULL, sah = NULL, gol_darah = NULL, RhesusDrh = NULL,
                StatTempat = 1, statKonfirmasi = NULL, tgl_aftap = NULL, kadaluwarsa = NULL,
                tglpengolahan = NULL, tglperiksa = NULL, lama_pengambilan = 0 
                WHERE noKantong = '$nkt'");
            
            if ($update) {
                $log_mdl = $_SESSION['leveluser'];
                $log_aksi = 'Reset status kantong: ' . $nkt;
                include_once "user_log.php";
                echo "<div class=\"info\">Status kantong $nkt telah diupdate.</div>";
            } else {
                echo "<div class=\"warning\">Gagal update status kantong $nkt.</div>";
            }
        }
        unset($_SESSION['list_kantong_edit']);
        $_SESSION['list_kantong_edit'] = array();
        echo "<meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=$PHP_SELF\">";
    }
}
?>

<body onLoad="setFocus()">

<!-- FORM UTAMA: Input + Tombol di Bawah -->
<form name="editstatus" method="POST" action="<?php echo $PHP_SELF; ?>">
    <table class="form" style="display: inline-table;">
        <tr>
            <td><strong>No Kantong</strong></td>
            <td class="input">
                <input type="text" name="nokantong" maxlength="25" tabindex="1" 
                       style="width: 200px; padding: 5px;" />
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <!-- Tombol Tambah -->
                <input name="add" type="submit" value="Tambah ke List" 
                       style="padding: 3px 4px; margin-right: 8px; font-weight: bold; cursor:pointer;">

                <!-- Tombol Proses Update (hanya jika ada list) -->
                <?php if (!empty($_SESSION['list_kantong_edit'])): ?>
                <input name="proses" type="submit" value="Proses Update" 
                       style="padding: 3px 4px; font-weight:bold; cursor:pointer;"
                       onclick="return confirm('Update status semua kantong di list?');">
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>

<br><br>

<!-- List Kantong dengan Tombol Hapus -->
<?php
if (!empty($_SESSION['list_kantong_edit'])) {
    echo "<h3>List Kantong untuk Edit Status</h3>";
    echo "<table class=\"list\" cellpadding=\"5\" cellspacing=\"1\">";
    echo "<tr class=\"field\">
            <td width=\"40\">No.</td>
            <td>No Kantong</td>
            <td width=\"80\">Aksi</td>
          </tr>";
    $no = 0;
    foreach ($_SESSION['list_kantong_edit'] as $index => $kantong) {
        $no++;
        echo "<tr class=\"record\">
                <td>$no</td>
                <td>$kantong</td>
                <td>
                    <form method=\"POST\" style=\"display:inline;\" onsubmit=\"return confirm('Hapus $kantong dari list?');\">
                        <input type=\"hidden\" name=\"index\" value=\"$index\">
                        <input name=\"hapus\" type=\"submit\" value=\"Hapus\" 
                               style=\" padding:3px 4px; font-size:11px; cursor:pointer;\">
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
}
?>
<script>window.confirm = function(){ return true; };</script>
