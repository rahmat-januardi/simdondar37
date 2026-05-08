<!DOCTYPE html>
<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$title="";
$s1=""; $s2="";$s3="";$s4=""; $s5="";$s6="";$s7="";$s8="";$s9="";

$tglawal=$_POST['tgl1'];
$tglakhir=$_POST['tgl2'];
$awalbulan=date("Y-01-01");
$hariini = date("Y-m-d");
if (empty($tglawal)){$tglawal=$awalbulan;}
if (empty($tglakhir)){$tglakhir=$hariini;}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMDONDAR</title>
    <link href="bootsrap337/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootsrap337/js/html5shiv.min.js"></script>
    <script src="bootsrap337/js/respond.min.js"></script>
    <link href="bootsrap337/bspmi.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
    <script src="bootsrap337/js/jquery.min.js"></script>
    <script src="bootsrap337/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="/js/rgbcolor.js"></script>
    <script type="text/javascript" src="/js/canvg.js"></script>
    <link rel="stylesheet" type="text/css" href="bootsrap337/datepicker/css/bootstrap-datepicker.css" >
    <script type="text/javascript" src="bootsrap337/datepicker/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript">
        $(function(){
            $(".datepicker").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            });
        });
    </script>

    <script>
      function getImgData(chartContainer) {
        var chartArea = chartContainer.getElementsByTagName('svg')[0].parentNode;
        var svg = chartArea.innerHTML;
        var doc = chartContainer.ownerDocument;
        var canvas = doc.createElement('canvas');
        canvas.setAttribute('width', chartArea.offsetWidth);
        canvas.setAttribute('height', chartArea.offsetHeight);
        
        
        canvas.setAttribute(
            'style',
            'position: absolute; ' +
            'top: ' + (-chartArea.offsetHeight * 2) + 'px;' +
            'left: ' + (-chartArea.offsetWidth * 2) + 'px;');
        doc.body.appendChild(canvas);
        canvg(canvas, svg);
        var imgData = canvas.toDataURL("image/png");
        canvas.parentNode.removeChild(canvas);
        return imgData;
      }
      
      function saveAsImg(chartContainer) {
        var imgData = getImgData(chartContainer);
        
        // Replacing the mime-type will force the browser to trigger a download
        // rather than displaying the image in the browser window.
        window.location = imgData.replace("image/png", "image/octet-stream");
      }
      
      function toImg(chartContainer, imgContainer) { 
        var doc = chartContainer.ownerDocument;
        var img = doc.createElement('img');
        img.src = getImgData(chartContainer);
        
        while (imgContainer.firstChild) {
          imgContainer.removeChild(imgContainer.firstChild);
        }
        imgContainer.appendChild(img);
      }
    </script>
   <script type="text/javascript" src="http://www.google.com/jsapi"></script>

</head>
<body>
<?php
if (isset($_POST['submit'])) {

    $model=$_POST['modelgraph'];
    $t1 = date("d M Y", strtotime($tglawal));
    $t2 = date("d M Y", strtotime($tglakhir));
    $title2 =' ('.$t1.' - '.$t2.')';
    switch ($model){
        case '1' :
            $s1='selected';
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN JENIS DONOR";
            $query   = mysql_query("select case when htransaksi.JenisDonor='0' THEN 'Donor Sukarela' else 'Donor Pengganti' end as keterangan,
                        count(htransaksi.KodePendonor) as jml from htransaksi where (date(htransaksi.Tgl)>='$tglawal' and date(htransaksi.Tgl)<='$tglakhir') and
                        (htransaksi.Pengambilan ='0' or htransaksi.Pengambilan ='2') group by case when htransaksi.JenisDonor='0' THEN 'Donor Sukarela' else 'Donor Pengganti' end");
            while($res = mysql_fetch_array($query)){
                $jenisdonor = $res['keterangan'];
                $jumlah= $res['jml'];
                $data .= '["'.$jenisdonor.'",'.$jumlah.'],';
            }
            $data = substr($data,0,(strlen($data)-1));$s1="selected";break;
        case '2':
            $s2='selected';
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN LOKASI PENYUMBANGAN";
            $query   = mysql_query("select case when left(htransaksi.NoTrans,1)='D' THEN 'Di UDD' else 'Mobile Unit' END as keterangan,
                        count(htransaksi.KodePendonor) as jml from htransaksi where (date(htransaksi.Tgl)>='$tglawal' and date(htransaksi.Tgl)<='$tglakhir') and
                        (htransaksi.Pengambilan ='0' or htransaksi.Pengambilan ='2') group by case when left(htransaksi.NoTrans,1)='D' THEN 'Di UDD' else 'Mobile Unit' END");
            while($res = mysql_fetch_array($query)){$lokasi = $res['keterangan'];$jumlah= $res['jml'];$data .= '["'.$lokasi.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s2="selected";break;
        case '3':
            $s3='selected';
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN GOLONGAN DARAH ABO";
            $query   = mysql_query("select pendonor.GolDarah as keterangan, count(htransaksi.KodePendonor) as jml
                        from htransaksi inner join pendonor on pendonor.Kode=htransaksi.KodePendonor
                        where (date(htransaksi.Tgl)>='$tglawal' and date(htransaksi.Tgl)<='$tglakhir') and
                        (htransaksi.Pengambilan ='0' or htransaksi.Pengambilan ='2') and pendonor.GolDarah<>'X' group by pendonor.GolDarah");
            while($res = mysql_fetch_array($query)){$lokasi = $res['keterangan'];$jumlah= $res['jml'];$data .= '["'.$lokasi.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s3="selected";break;
        case '4':
            $s4='selected';
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN GOLONGAN DARAH RHESUS";
            $query   = mysql_query("select case when pendonor.Rhesus='+' Then 'Rhesus Positif(+)' Else 'Rhesus Negatif(-)' End as keterangan, count(htransaksi.KodePendonor) as jml
                        from htransaksi inner join pendonor on pendonor.Kode=htransaksi.KodePendonor
                        where (date(htransaksi.Tgl)>='$tglawal' and date(htransaksi.Tgl)<='$tglakhir') and
                        (htransaksi.Pengambilan ='0' or htransaksi.Pengambilan ='2') group by pendonor.Rhesus");
            while($res = mysql_fetch_array($query)){$lokasi = $res['keterangan'];$jumlah= $res['jml'];$data .= '["'.$lokasi.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s4="selected";break;
        case '5':
            $s5='selected';
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN JENIS KELAMIN";
            $query   = mysql_query("select case when pendonor.Jk='0' then 'Laki-laki' else 'Perempuan' end as keterangan, count(htransaksi.KodePendonor) as jml
                        from htransaksi inner join pendonor on pendonor.Kode=htransaksi.KodePendonor
                        where (date(htransaksi.Tgl)>='$tglawal' and date(htransaksi.Tgl)<='$tglakhir') and
                        (htransaksi.Pengambilan ='0' or htransaksi.Pengambilan ='2') group by case when pendonor.Jk='0' then 'Laki-laki' else 'Perempuan' end");
            while($res = mysql_fetch_array($query)){$lokasi = $res['keterangan'];$jumlah= $res['jml'];$data .= '["'.$lokasi.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s5="selected";break;
        case '6':
            $s6='selected';
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN PEKERJAAN PENDONOR";
            $query   = mysql_query("select pendonor.pekerjaan as keterangan, count(htransaksi.KodePendonor) as jml
                        from htransaksi inner join pendonor on pendonor.Kode=htransaksi.KodePendonor
                        where (date(htransaksi.Tgl)>='$tglawal' and date(htransaksi.Tgl)<='$tglakhir') and
                        (htransaksi.Pengambilan ='0' or htransaksi.Pengambilan ='2') group by pendonor.pekerjaan");
            while($res = mysql_fetch_array($query)){$lokasi = $res['keterangan'];$jumlah= $res['jml'];$data .= '["'.$lokasi.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s6="selected";break;
        case '7':
            $s7='selected';
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN DONOR LAMA/BARU";
            $query   = mysql_query("select case when pendonor.jumDonor<3 then 'Donor Lama' else 'Donor Baru' End as keterangan, count(htransaksi.KodePendonor) as jml
                        from htransaksi inner join pendonor on pendonor.Kode=htransaksi.KodePendonor where (date(htransaksi.Tgl)>='$tglawal' and date(htransaksi.Tgl)<='$tglakhir') and
                        (htransaksi.Pengambilan ='0' or htransaksi.Pengambilan ='2') group by case when pendonor.jumDonor<3 then 'Donor Lama' else 'Donor Baru' End");
            $query   = mysql_query("SELECT
                        CASE WHEN `donorbaru`='0' THEN 'Donor Baru' ELSE 'Donor Lama' END As keterangan,
                        Count(NoTrans) as jml
                        FROM `htransaksi`
                        WHERE (Pengambilan='0' or Pengambilan='2') AND (date(Tgl)>='$tglawal' AND date(Tgl)<='$tglakhir')
                        group by donorbaru");
            while($res = mysql_fetch_array($query)){$lokasi = $res['keterangan'];$jumlah= $res['jml'];$data .= '["'.$lokasi.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s7="selected";break;
        case '8':
            $s8='selected';
            $title  = "GRAFIK PERBANDINGAN PENYUMBANGAN BERHASIL, GAGAL DAN BATAL";
            $query   = mysql_query("SELECT
                        CASE  WHEN `Pengambilan`='0' THEN 'BERHASIL DIAMBIL'
                              WHEN (`Pengambilan`='1' AND `ketBatal` in ('0','1','2','4','6','7','8','9','10'))  THEN 'BATAL/DITOLAK'
                              WHEN `Pengambilan`='2' THEN 'GAGAL PENGAMBILAN' END AS keterangan,
                        count(`NoTrans`) as jml
                        FROM `htransaksi` WHERE
                        (`Tgl`>='$tglawal' and `Tgl`<='$tglakhir') and pengambilan in ('1','0','2')
                        group by pengambilan");
            while($res = mysql_fetch_array($query)){$lokasi = $res['keterangan'];$jumlah= $res['jml'];$data .= '["'.$lokasi.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s7="selected";break;
        case '9':
            $s9='selected';
            $title  = "GRAFIK PERBANDINGAN ALASAN PENOLAKAN DONOR";
            //merubah ket batal 3 (HB Mengapung) dijadikan 2 (HB Rendah)
            $upd_htr=mysql_query("UPDATE `htransaksi` SET `ketBatal`='2' WHERE `ketBatal`='3'");
            $query   = mysql_query("SELECT
                                    case
                                    when (`ketBatal`='0') then 'Tensi Rendah'
                                    when (`ketBatal`='1') then 'Tensi Tinggi'
                                    when (`ketBatal`='2') then 'HB Rendah'
                                    when (`ketBatal`='4') then 'HB Tinggi'
                                    when (`ketBatal`='5') then 'Berat Badan Kurang'
                                    when (`ketBatal`='6') then 'Konsumsi Obat-obatan'
                                    when (`ketBatal`='7') then 'Status Bepergian'
                                    when (`ketBatal`='8') then 'Alasan Medis'
                                    when (`ketBatal`='9') then 'Resiko Prilaku'
                                    WHEN (`ketBatal`='10') then  'Lain-Lain' END AS keterangan,
                                    Count(`NoTrans`) as jml
                                    from htransaksi h
                                    where `Tgl`>='$tglawal' AND `Tgl`<='$tglakhir' and  pengambilan ='1' and `ketBatal` IN ('0','1','2','4','6','7','8','9','10')
                                    group by `ketbatal`");
            while($res = mysql_fetch_array($query)){$lokasi = $res['keterangan'];$jumlah= $res['jml'];$data .= '["'.$lokasi.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s7="selected";break;
    }
    ?>
        <script type="text/javascript">
          google.load('visualization', '1.0', {'packages':['corechart']});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Slices');
                data.addRows([<?php echo $data; ?>]);
            var options = {'title':'','width':600,'height':300,is3D: true,left:0,titleTextStyle:{fontSize: 14, bold: true, italic: false },
                           legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"90%",height:"90%"},pieStartAngle: 45};
            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
          };
          google.load('visualization', '1', {packages:['table']});
          google.setOnLoadCallback(drawTable);
          function drawTable() {
            var data = new google.visualization.DataTable();
                data.addColumn('string', 'Keterangan');
                data.addColumn('number', 'Jumlah');
                data.addRows([<?php echo $data; ?>]);
            var options = {'title':''};
            var table = new google.visualization.Table(document.getElementById('table_div'));
            var formatter = new google.visualization.NumberFormat({prefix: '', negativeColor: 'red', negativeParens: true,fractionDigits:0,groupingSymbol:'.'});
                formatter.format(data, 1); // Apply formatter to second column
                table.draw(data, {allowHtml: true, showRowNumber: true});
          };
        </script>
<?}?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <br>
            <div class="panel with-nav-tabs panel-primary" id="shadow1">
                <div class="panel-heading">
                    <h4>STATISTIK PENYUMBANGAN DARAH (DONASI)</h4>
                </div>
                <form class="form-inline" method="POST" action="pmitatausaha.php?module=graphdonasi">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            Jenis Grafik:
                            <select class="form-control" name="modelgraph">
                                <option value="1" <?=$s1?>>Pie Chart Donor Sukarela/Pengganti</option>
                                <option value="2" <?=$s2?>>Pie Chart Lokasi penyumbangan</option>
                                <option value="3" <?=$s3?>>Pie Chart Golongan Darah ABO</option>
                                <option value="4" <?=$s4?>>Pie Chart Golongan Darah Rhesus</option>
                                <option value="5" <?=$s5?>>Pie Chart Jenis Kelamin</option>
                                <option value="6" <?=$s6?>>Pie Chart berdasarkan Pekerjaan</option>
                                <option value="7" <?=$s7?>>Pie Chart Donor Lama/Baru</option>
                                <option value="8" <?=$s8?>>Pie Chart Penyumbangan berhasil/gagal/batal</option>
                                <option value="9" <?=$s9?>>Pie Chart Alasan Penolakan Donor</option>
                            </select>
                            Dari tanggal:
                                <input class="form-control datepicker" name="tgl1" id="datepicker" value="<?=$tglawal?>" type=date size=10>
                                <input class="form-control datepicker" name="tgl2" id="datepicker1" value="<?=$tglakhir?>" type=date size=10>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12"><h4 style="color: red;font-weight: bold;"><?php echo $title.' '.$title2;?></h4></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6" id="chart_div">

                        </div>
                        <div class="col-lg-6" id="table_div">

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" name="submit"  id="btn_upload" class="btn btn-default class_shadow2" ><i class="fa  fa-pie-chart" aria-hidden="true"></i>&nbsp;&nbsp;Tampilkan Grafik</button>
                    <button type="button" class="btn btn-default class_shadow2" onclick="saveAsImg(document.getElementById('chart_div'));"><i class="fa  fa-save" aria-hidden="true"></i>&nbsp;&nbsp;Simpan Grafik</button>
                    <a href="pmitatausaha.php?module=statistik" class="btn btn-default class_shadow2" title="Kembali"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
                </div>
                </form>
        </div>
    </div>
</div>

  </body>
</html>
