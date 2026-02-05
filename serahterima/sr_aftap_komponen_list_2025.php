<?php
session_start();
$msg="";
require_once('clogin.php');
require_once('config/dbi_connect.php');
$leveluser=$_SESSION['level'];
$namauser=$_SESSION['namauser'];
$namalengkap=$_SESSION['nama_lengkap'];
$udd=mysqli_fetch_assoc(mysqli_query($dbi,"SELECT `nama`,`id` FROM `utd` WHERE `aktif`='1';"));
$id_uddaktif=$udd['id'];
$nama_uddaktif=$udd['nama'];

(isset($_SESSION['tanggal1'])) ? $f_tanggal1=$_SESSION['tanggal1'] : $f_tanggal1 = date('Y-m-d');
(isset($_SESSION['tanggal2'])) ? $f_tanggal2=$_SESSION['tanggal2'] : $f_tanggal2 = date('Y-m-d');
(isset($_SESSION['status'])) ? $f_status=$_SESSION['status'] : $f_status = "";

if(isset($_POST['vfilter'])){
    $f_status       =$_SESSION['status']    = $_POST['fltstatus'];
    $f_tanggal1     =$_SESSION['tanggal1']  = $_POST['fltTanggal1'];
    $f_tanggal2     =$_SESSION['tanggal2']    = $_POST['fltTanggal2'];
}
if(isset($_POST['vreset'])){
    $f_status        = "";
    $f_tanggal1     = date('Y-m-d');
    $f_tanggal2     = date('Y-m-d');
}
?>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="bootsrap337/bspmi.css">
        <link rel="stylesheet" href="bootsrap337/w3.css">
        <link rel="stylesheet" href="pmf/pmfstyle.css">
        <link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
        <link href="bootsrap337/datepicker/css/bootstrap-datepicker.css" rel="stylesheet">
        <link rel="stylesheet" href="bootsrap337/chosen/chosen.css">
        <link href="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.css" rel="stylesheet">
        <style>
             .shadow{box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);}
            .btn-pref .btn {
                border-radius:0 !important;
            }
            .modal-fullscreen {
                width: 98%;
                padding: 0;
            }

            .modal-content {
                /* min-height: 90%; */
                width: 100%;
                border-radius: 25;
                margin: 0 0;
            }
            .modal-footer {
                border-radius: 25;
                bottom:0px;
                position:relative;
                width:100%;
            }
            .form-group{margin-top: 1px;margin-bottom: 1px;}
            .table thead th {
                height: 40px;
                padding: 2px !important;
                text-align: center !important;
                vertical-align: middle !important;
                text-shadow: 1px 1px  2px black;
                font-size : 1.2em;
                /* word-break:break-all; */
            }
            .table tbody td {
                font-size:1em;
                white-space: nowrap;
                vertical-align: middle !important;
            }
            .text-vertical{
                vertical-align: middle;
                text-align: center;
                transform: rotate(-90deg);
                white-space: nowrap;
            }
            #loading {
                    width: 50px;
                    height: 50px;
                    border-radius: 100%;
                    border: 5px solid #ccc;
                    border-top-color: #ff6a00;
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    margin: auto;
                    z-index: 99;
                    animation: sp 2s ease infinite;
                }
                @keyframes sp {
                    from {transform: rotate(0deg);
                    } to {transform: rotate(360deg);
                    }
                }
                a{
                    text-decoration: none !important;
                }
                .table td.text {
                    max-width: 300px;
                }
                .table td.text span {
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    display: inline-block;
                    max-width: 100%;
                }

            .swal2-popup {font-size: 1.6rem !important;}
            .swal-footer {text-align: center;}
            .custom-swal {
                background: linear-gradient(to bottom, white, red) !important;
                border-radius: 15px !important;
                box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.5) !important;
                width: 800px !important;
                max-width: 95% !important;
                padding: 20px !important;
                overflow: hidden;
            }
        </style>
    </head>
    
    <body>
        <div id="loading"></div>
        <div class="container-fluid" style="margin: 30px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel w3-border-theme shadow">
                        <div class="panel-heading w3-theme-d5 clearfix">
                            <div class="col-lg-9 col-md-8 col-sm-7 col-xs-8 text-left text-shadow" style="font-size: 150%;font-weight: bold;">DATA FORMULIR SERAH TERIMA DARAH & SAMPEL</div>
                            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-4 text-right">
                                <a href="" class="w3-btn w3-theme w3-hover-yellow" data-toggle="modal" data-target="#mFilter" >Filter</a>
                                <a href="?module=serahterima" class="w3-btn w3-theme w3-hover-yellow" >Kembali</a>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12"><?php echo $msg;?></div>
                                <div class="col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-responsive table-bordered table-striped table-md table-hover table-md display"  id="dtaudittrail">
                                            <thead class="w3-theme-d4" style="height: 40px;">
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th>Transaksi</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <th>Asal</th>
                                                    <th>Suhu</th>
                                                    <th>Jumlah</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $qry="SELECT h.`hst_notrans`, h.`hst_tgl`, DATE_FORMAT(h.`hst_tgl`,'%d-%m-%Y') as `dt`, DATE_FORMAT(h.`hst_tgl`,'%H:%i') as `tm` ,h.`hst_asal`,h.`hst_user`, h.`hst_kode_alat`,h.`hst_suhuterima`,
                                                        h.`hst_kondisiumum`, count(d.`dst_notrans`) as `jumlah`, h.up_data
                                                        FROM `serahterima` h inner join serahterima_detail d on d.`dst_notrans`=h.`hst_notrans`
                                                        WHERE `hst_modul`='KARANTINA' and (DATE(h.`hst_tgl`) BETWEEN '$f_tanggal1' AND '$f_tanggal2')
                                                        GROUP BY h.`hst_notrans`, h.`hst_tgl`, h.`hst_asal`,h.`hst_user`, h.`hst_kode_alat`,h.`hst_suhuterima`, h.`hst_kondisiumum`";
                                                    $query=mysqli_query($dbi,$qry);
                                                    $no=0;
                                                    while($row=mysqli_fetch_assoc($query)){
                                                        $no++;
                                                        echo '<tr>';
                                                        echo '<td class="text-right">'.$no.'</td>';
                                                        echo '<td>'.$row['hst_notrans'].'</td>';
                                                        echo '<td>'.$row['dt'].'</td>';
                                                        echo '<td>'.$row['tm'].'</td>';
                                                        echo '<td>'.$row['hst_asal'].'</td>';
                                                        echo '<td>'.$row['hst_suhuterima'].'<sup>o</sup>C</td>';
                                                        echo '<td class="text-center">'.$row['jumlah'].'</td>';
                                                        echo '<td>';
                                                            echo '<a href="?module=sr_rpt_ktg&no='.$row['hst_notrans'].'">Kantong</a> | ';
                                                            echo '<a href="?module=sr_rpt_imltd&no='.$row['hst_notrans'].'">IMLTD</a> | ';
                                                            echo '<a href="?module=sr_rpt_kgd&no='.$row['hst_notrans'].'">KGD</a> | ';
                                                            echo '<a href="?module=sr_rpt_nat&no='.$row['hst_notrans'].'">NAT</a> ';
                                                            if($row['up_data']=='0'){
                                                                echo '<a href="#" class="btn-kirim" data-id="'.$row['hst_notrans'].'" data-toggle="modal" data-target="#mKirim"> | KIRIM KONSOLIDASI</a>';
                                                            }
                                                        echo '</td>';
                                                        echo '</tr>';
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mFilter" role="dialog">
            <div class="modal-dialog" role="document">   
                <div class="modal-content">
                    <div class="modal-header w3-theme shadow">
                        <button type="button" class="close " data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" style="color:white;">Filter Data</h4>
                    </div>
                    <div class="modal-body">
                        <form name="mFrmFilter" class="form-horizontal" id="mFrmFilter" action="" method="POST">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="fltTanggal1">Tanggal</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <input type="text" class="form-control startdate" value="<?php echo $f_tanggal1;?>"  name="fltTanggal1" id="fltTanggal1"/>
                                            <span class="input-group-addon input-sm">s/d</span>
                                        <input type="text" class="form-control enddate" value="<?php echo $f_tanggal2;?>"  name="fltTanggal2" id="fltTanggal2"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="fltStatus">Status</label>
                                <div class="col-sm-9">
                                    <select name="fltStatus" class="form-control input-sm chosen-select">
										<?php 
                                        $arr_status=array("0"=>"Semua", "1"=>"Terkirim");
                                        foreach($arr_status as $val=>$cap){
                                            if($val==$f_status){
                                                echo '<option value="'.$val.'" selected>'.$cap.'</option>';
                                            }else{
                                                echo '<option value="'.$val.'">'.$cap.'</option>';
                                            }
                                        }
										?>
										</select>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="vfilter" id="vfilter" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">OK</button>
                        <button type="submit" name="vreset" id="vreset" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">Reset</button>
                        <button class="w3-btn w3-theme w3-hover-indigo w3-card" data-dismiss="modal">Batal</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mKirim" role="dialog">
            <div class="modal-dialog" role="document">   
                <div class="modal-content">
                    <div class="modal-header w3-theme shadow">
                        <button type="button" class="close " data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" style="color:white;">Pengiriman Kantong</h4>
                    </div>
                    <div class="modal-body">
                        <form name="mFrmKirim" class="form-horizontal" id="mFrmKirim" action="" method="POST">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="InpNomor">No Transaksi</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" name="InpNotransaksi" id="InpNotransaksi" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="InpDari">Dari</label>
                                <div class="col-md-9">
                                    <input type="hidden" class="form-control" value="<?php echo $id_uddaktif;?>"  name="InpDari" id="InpDari">
                                    <input type="text" class="form-control input-sm" value="<?php echo $nama_uddaktif;?>"  name="InpDariNama" id="InpDariNama" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="InpKirimke">Kirim ke</label>
                                <div class="col-sm-9">
                                    <select name="InpKirimke" class="form-control chosen-select">
									    <?php 
                                        $query_udd=mysqli_query($dbi,"SELECT `nama`,`id` FROM `utd` WHERE `aktif`='0';");
                                        while($dt=mysqli_fetch_assoc($query_udd)){
                                            if($dt['id']=='317D'){
                                                echo '<option value="'.$dt['id'].'" selected>'.$dt['nama'].'</option>';
                                            }else{
                                                echo '<option value="'.$dt['id'].'">'.$dt['nama'].'</option>';
                                            }
                                        }
										?>	
									</select>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="vKirim" id="vKirim" class="w3-btn w3-theme-d4 w3-hover-indigo w3-card">Kirim Kantong</button>
                        <button class="w3-btn w3-theme w3-hover-indigo w3-card" data-dismiss="modal">Batal</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </body>

<script src="bootsrap337/js/jquery.min.js"></script>
<script src="bootsrap337/js/bootstrap.min.js"></script>
<script src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="bootsrap337/datepicker/custom.js"></script>
<script src="bootsrap337/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/v/bs/dt-1.13.8/datatables.min.js"></script>
<script src="bootsrap337/sweetalert2/sweetalert2@11"></script>
<script>
    $(document).ready(function(){
        setDateRangePicker(".startdate", ".enddate")
        var table = $('#dtaudittrail').DataTable( { 
	        lengthMenu: [
                [5,10, 15, 25, 50, -1],
                [5,10, 15, 25, 50, 'All']
            ]
	    });
        var load = document.getElementById("loading");window.addEventListener('load', function(){load.style.display = "none";});
        $('.btn-kirim').on('click', function() {
            var noTrans = $(this).data('id'); 
            $('#InpNotransaksi').val(noTrans);
        });

        $('#vKirim').on('click', function() {
            var noTransaksi = $('#InpNotransaksi').val();
            var dari = $('#InpDari').val();
            var kirimKe = $('select[name="InpKirimke"]').val();
            if (!noTransaksi || !dari || !kirimKe) {
                Swal.fire({
                    title: "Gagal!",
                    text: "Harap isi semua data sebelum mengirim.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                return;
            }
            $('#mKirim').modal('hide');
            Swal.fire({
                title: "Mengirim Data...",
                text: "Harap tunggu, data sedang dikirim.",
                icon: "info",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'serahterima/sr_aftap_kirimkantong.php',
                type: 'POST',
                data: {
                    noTransaksi: noTransaksi,
                    dari: dari,
                    kirimKe: kirimKe
                },
                dataType: "json",
                success: function(response) {
                    console.log("Response dari server:", response); 
                    Swal.close();

                    if (response && response.status === "success") {
                        Swal.fire({
                            title: "Sukses!",
                            text: response.message || "Data berhasil dikirim.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: response.message || "Terjadi kesalahan saat mengirim data.",
                            icon: "error",
                            confirmButtonText: "Coba Lagi"
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);

                    let errorMessage = "Terjadi kesalahan saat menghubungi server.";
                    if (jqXHR.responseText) {
                        try {
                            let errorResponse = JSON.parse(jqXHR.responseText);
                            errorMessage = errorResponse.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing JSON response:", e);
                        }
                    }

                    Swal.close();
                    Swal.fire({
                        title: "Gagal!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: "Coba Lagi"
                    });
                }
            });
        });
    });

    $('.chosen-select').chosen({width: "100%"});

    function setDateRangePicker(start, end) {
        $(start).datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        }).on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());
            $(end).datepicker('setStartDate', startDate);
            if ($(end).val() === '') {
                $(end).datepicker('setDate', startDate);
            }
        });

        $(end).datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        }).on('changeDate', function (selected) {
            var endDate = new Date(selected.date.valueOf());
            $(start).datepicker('setEndDate', endDate);
        });
    }
</script>
<?php 

?>