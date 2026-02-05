<?php
include "koneksi.php";
//include "index.php";

$detail = mysql_real_escape_string($_GET['detail']); // contoh ambil dari URL
$nomor =  $_GET['no'];

$sql = "SELECT formulir.*, pks.bidang as bidangSPO, pks.nama1 as namaSPO, pks.kontrol2 as kodeSPO FROM formulir JOIN pks ON formulir.terkait=pks.nama1 WHERE formulir.kontrol2='$detail' AND formulir.aktif='0' AND formulir.nomor='$nomor'";
$proses = mysql_query($sql);
$data = mysql_fetch_array($proses);

$query = "SELECT * FROM user ORDER BY nama_lengkap";
$hasilPembuat = mysql_query($query);
$hasilPemeriksa = mysql_query($query);
$hasilPengesah = mysql_query($query);
$hasilPengesah2 = mysql_query($query);
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Untitled Document</title>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (harus sebelum jQuery UI dan Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI (opsional, untuk datepicker) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(function() {
        $("#datepicker").datepicker();
        $("#datepicker").change(function() {
            $("#datepicker").datepicker("option", "dateFormat", "dd MM yy");
        });
    });
    </script>
    <script>
    $(function() {
        $("#datepicker2").datepicker();
        $("#datepicker2").change(function() {
            $("#datepicker2").datepicker("option", "dateFormat", "dd MM yy");
        });
    });
    </script>
    <script>
    $(function() {
        $("#datepicker3").datepicker();
        $("#datepicker3").change(function() {
            $("#datepicker3").datepicker("option", "dateFormat", "dd MM yy");
        });
    });
    </script>
    <style type="text/css">
    .ui-datepicker {
        font-family: Garamond;
        font-size: 12px;
        margin-left: 10px
    }

    .COOPER {
        font-family: Cooper Black;
    }

    .coper {
        font-family: Cooper Black;
    }
    </style>
    <style>
    .button1 {
        padding: 10px 20px;
        font-size: 12px;
        text-align: center;
        cursor: pointer;
        outline: none;
        color: #fff;
        background-color: #556B2F;
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px #999;
    }

    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        font-size: 12px;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: center;
        padding: 8px;
    }
    </style>
</head>

<body>
    <br />
    <p align="center" class="COOPER">
        <font size="5"><u>DOKUMEN FORMULIR<br /><?php echo $data['nama1']; ?></u></font>
    </p>

    <form method="post" action="edit_formulir.php" enctype="multipart/form-data">
        <input type="hidden" name="nomor" value="<?= $data['nomor'] ?>">
        <input type="hidden" name="kontrol1" value="<?= $data['kontrol1'] ?>">
        <input type="hidden" name="kontrol3" value="<?= $data['kontrol3'] ?>">
        <input type="hidden" name="terkait" value="<?= $data['terkait'] ?>">
        <input type="hidden" name="fileku" value="<?= $data['fileku'] ?>">
        <table align="center" border="1" cellpadding="10" cellspacing="1" bgcolor="#FFFFFF">
            <tr>
                <td valign="top">
                    <table align="center">
                        <tr>
                            <td>
                                <div align="right"><strong>SPO Terkait</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left">
                                    <strong>
                                        <?= $data['namaSPO']; ?>
                                        - <?= $data['kodeSPO']; ?>
                                    </strong>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        <tr>
                            <td>
                                <div align="right"><strong>Bidang</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <select name="bidang">
                                            <?php
                                            // Ambil nilai bidang dari data detail
                                            $selected_bidang = $data['bidang'];

                                            // Ambil semua opsi dari master_bidang
                                            $sql = "SELECT * FROM master_bidang ORDER BY bidang";
                                            $proses = mysql_query($sql);

                                            while ($row = mysql_fetch_array($proses)) {
                                                $bidang = $row['bidang'];

                                                // Cek apakah bidang ini yang dipilih
                                                $selected = ($bidang == $selected_bidang) ? "selected" : "";

                                                echo "<option value='$bidang' $selected>$bidang</option>";
                                            }
                                            ?>
                                        </select>
                                    </strong></div> <strong><text style="display:none">
                                        <div align="left">
                                            <input type="text" name="nomor" value="<?php echo $data['nomor']; ?>" />
                                        </div>
                                    </text>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right"><strong>Judul Dokumen</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <input type="text" name="nama1" size="50"
                                            value="<?php echo $data['nama1']; ?>" />
                                    </strong></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right"><strong>Judul Dokumen Sebelumnya</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <input type="text" name="nama2" size="50"
                                            value="<?php echo $data['nama2']; ?>" />
                                    </strong></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right"><strong>Tingkatan Dokumen</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <select name="tingkat">
                                            <option><?php echo $data['tingkat']; ?></option>
                                        </select>
                                    </strong></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right"><strong>No. Kontrol Dokumen</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <input type="text" style="background:yellow;" readonly="readonly"
                                            name="kontrol2" size="20" value="<?php echo $data['kontrol2']; ?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right"><strong>Periode Kaji Ulang (bulan)</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <input type="text" name="periode" size="5"
                                            value="<?php echo $data['periode']; ?>" />
                                    </strong></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right"><strong>No. Versi</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <input type="text" name="no_versi" size="10"
                                            value="<?php echo $data['no_versi']; ?>" />
                                    </strong></div>
                            </td>
                        </tr>
                        <tr style="display:none">
                            <td>
                                <div align="right"><strong>Tanggal Disahkan</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <input type="text" name="tgl_setuju" id=""
                                            value="<?php echo $data['tgl_setuju']; ?>" />
                                    </strong></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right"><strong>Tanggal Berlaku</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <input type="text" name="tgl_pelaksanaan" id=""
                                            value="<?php echo $data['tgl_pelaksanaan']; ?>" />
                                    </strong></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right"><strong>Tanggal Kaji Ulang</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <input type="text" name="tgl_peninjauan" id=""
                                            value="<?php echo $data['tgl_peninjauan']; ?>" />
                                    </strong></div>
                            </td>
                        </tr>
                        <tr style="display:none">
                            <td>
                                <div align="right"><strong>Disusun Oleh</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <select class="select2" name="pembuat" style="width:100%;">
                                            <?php
                                            // Ambil nilai bidang dari data detail
                                            $selected_pembuat = $data['pembuat'];

                                            while ($row = mysql_fetch_array($hasilPembuat)) {
                                                $pembuat = $row['nama_lengkap'];

                                                $selectedPembuat = ($pembuat == $selected_pembuat) ? "selected" : "";
                                                echo "<option value='$pembuat' $selectedPembuat>$pembuat</option>";
                                            }
                                            ?>
                                        </select>
                                    </strong></div>
                            </td>
                        </tr>
                        <tr style="display:none">
                            <td>
                                <div align="right"><strong>Diperiksa Oleh</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <select class="select2" style="width:100%;" name="pemeriksa">
                                            <?php
                                            // Ambil nilai bidang dari data detail
                                            $selected_pemeriksa = $data['pemeriksa'];

                                            while ($row = mysql_fetch_array($hasilPemeriksa)) {
                                                $pemeriksa = $row['nama_lengkap'];

                                                $selectedPemeriksa = ($pemeriksa == $selected_pemeriksa) ? "selected" : "";
                                                echo "<option value='$pemeriksa' $selectedPemeriksa>$pemeriksa</option>";
                                            }
                                            ?>
                                        </select>
                                    </strong></div>
                            </td>
                        </tr>
                        <tr style="display:none">
                            <td>
                                <div align="right"><strong>Disetujui Oleh</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <select class="select2" style="width:100%;" name="pengesah">
                                            <?php
                                            // Ambil nilai bidang dari data detail
                                            $selected_pengesah = $data['pengesah'];

                                            while ($row = mysql_fetch_array($hasilPengesah)) {
                                                $pengesah = $row['nama_lengkap'];

                                                $selectedPengesah = ($pengesah == $selected_pengesah) ? "selected" : "";
                                                echo "<option value='$pengesah' $selectedPengesah>$pengesah</option>";
                                            }
                                            ?>
                                        </select>
                                    </strong></div>
                            </td>
                        </tr>

                        <tr style="display:none">
                            <td>
                                <div align="right"><strong>Disahkan Oleh</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>
                                        <select class="select2" style="width:100%;" name="pengesah2">
                                            <?php
                                            // Ambil nilai bidang dari data detail
                                            $selected_pengesah2 = $data['pengesah2'];

                                            while ($row = mysql_fetch_array($hasilPengesah2)) {
                                                $pengesah2 = $row['nama_lengkap'];

                                                $selectedPengesah2 = ($pengesah2 == $selected_pengesah2) ? "selected" : "";
                                                echo "<option value='$pengesah2' $selectedPengesah2>$pengesah2</option>";
                                            }
                                            ?>
                                        </select>
                                    </strong></div>
                            </td>
                        </tr>

                        <!--awal upload-->
                        <tr>
                            <td>
                                <div align="right"><strong>Upload Dokumen</strong></div>
                            </td>
                            <td><strong>:</strong></td>
                            <td>
                                <div align="left"><strong>

                                        <input type="file" name="fileupload" id="fileupload" class="form-control" />
                                    </strong></div>
                            </td>
                        </tr>
                        <!--akhir upload-->

                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" align="right"><input class="button1" type="submit" name="upload"
                                    value="Update" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
    <br />
    <p align="center"><b> Riwayat Perubahan Formulir</b></p>
    <table align="center">
        <tr>
            <td><strong>No</strong></td>
            <td><strong>Bidang</strong></td>
            <td><strong>Judul Dokumen</strong></td>
            <td><strong>Tingkat Dokumen</strong></td>
            <td><strong>No. Kontrol Dokumen</strong></td>
            <td><strong>Periode Review</strong></td>
            <td><strong>No Versi</strong></td>
            <td style="display:none"><strong>Tgl Disetujui</strong></td>
            <td><strong>Tgl Berlaku</strong></td>
            <td><strong>Tgl Kaji Ulang</strong></td>
            <td style="display:none"><strong>Disusun Oleh</strong></td>
            <td style="display:none"><strong>Diperiksa Oleh</strong></td>
            <td style="display:none"><strong>Disetujui Oleh</strong></td>
            <td style="display:none"><strong>Disahkan Oleh</strong></td>
        </tr>
        <?php
        $detail = $_GET['detail'];
        $donor = "select * from riwayat where kontrol2='$detail'";
        //$donor = "select * from riwayat where nama1 like '%IKM%' order by nomor desc";

        // awal Konversi tanggal ke bahasa indonesia
        function format_indo($date)
        {
            $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

            $tahun = substr($date, 0, 4);
            $bulan = substr($date, 5, 2);
            $tgl   = substr($date, 8, 2);
            $result = $tgl . " " . $BulanIndo[(int)$bulan - 1] . " " . $tahun;
            return ($result);
        }
        // akhir Konversi tanggal ke bahasa indonesia

        $proses = mysql_query($donor);
        $nourut = 0;
        while ($data = mysql_fetch_array($proses)) {
            $nourut++;
        ?>
        <tr>
            <td><?php echo $nourut; ?></td>
            <td><?php echo $data['bidang']; ?></td>
            <td><?php echo $data['nama1']; ?></td>
            <td><?php echo $data['tingkat']; ?></td>
            <td><?php echo $data['kontrol2']; ?></td>
            <td><?php echo $data['periode']; ?></td>
            <td><?php echo $data['no_versi']; ?></td>
            <td style="display:none"><?php echo format_indo($data['tgl_setuju']); ?></td>
            <td><?php echo format_indo($data['tgl_pelaksanaan']); ?></td>
            <td><?php echo format_indo($data['tgl_peninjauan']); ?></td>
            <td style="display:none"><?php echo $data['pembuat']; ?></td>
            <td style="display:none"><?php echo $data['pemeriksa']; ?></td>
            <td style="display:none"><?php echo $data['pengesah']; ?></td>
            <td style="display:none"><?php echo $data['pengesah2']; ?></td>
        </tr>
        <?php
        }
        ?>
    </table>
    <br />
    <script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2({
            placeholder: "Pilih opsi",
            allowClear: true,
            width: 'resolve'
        });

        // Otomatis fokus ke kolom pencarian saat dropdown dibuka
        $(document).on('select2:open', () => {
            // Tunggu sedikit supaya elemen input sudah siap
            document.querySelector('.select2-search__field').focus();
        });
    });
    </script>

</body>

</html>