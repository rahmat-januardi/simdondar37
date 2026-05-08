<!DOCTYPE html>
<?php
session_start();
require_once('clogin.php');
require_once('config/db_connect.php');
$title="";
$s1=""; $s2="";$s3="";$s4=""; $s5="";$s6="";$s7="";$s8="";$s9="";

$tglawal=$_POST['tgl1'];
$tglakhir=$_POST['tgl2'];
$awalbulan=date("Y-m-01");
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
    $title2 ='';
    switch ($model){
        case '1';
            $s1='selected';
            $title  = "PIE CHAT PEMBUATAN KOMPONEN DARAH (".$tglawal.' - '.$tglakhir.')';
            $query  = mysql_query("SELECT
                        p.`lengkap` as produk,
                        count(d.`Produk`) as jumlah
                        FROM `dpengolahan` d inner join `produk` p on p.`Nama`=d.`Produk`
                        WHERE
                        date(d.`tgl`)>='$tglawal' AND date(d.`tgl`)<='$tglakhir'
                        GROUP BY p.`lengkap`");
            while($res = mysql_fetch_array($query)){
                $komp = $res['produk'];
                $jumlah= $res['jumlah'];
                $data .= '["'.$komp.'",'.$jumlah.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
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
                        legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"100%",height:"100%"},pieStartAngle: 0};
                    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                    chart.draw(data, options);
                };
                google.load('visualization', '1', {packages:['table']});
                google.setOnLoadCallback(drawTable);
                function drawTable() {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Komponen');
                    data.addColumn('number', 'Jumlah');
                    data.addRows([<?php echo $data; ?>]);
                    var options = {'title':''};
                    var table = new google.visualization.Table(document.getElementById('table_div'));
                    var formatter = new google.visualization.NumberFormat({prefix: '', negativeColor: 'red', negativeParens: true,fractionDigits:0,groupingSymbol:'.'});
                    formatter.format(data, 1); // Apply formatter to second column
                    table.draw(data, {allowHtml: true, showRowNumber: true});
                };
            </script>
            <?php
            break;
        case '2':
            $s2='selected';
            $title  = "PIE CHAT ALASAN PEMUSNAHAN KOMPONEN DARAH (".$tglawal.' - '.$tglakhir.')';
            $query  = mysql_query("SELECT
                                    CASE
                                    WHEN m.`alasan_buang`='0' THEN 'Gagal Aftap'
                                    WHEN m.`alasan_buang`='1' THEN 'Lisis'
                                    WHEN m.`alasan_buang`='2' THEN 'Kadaluarsa'
                                    WHEN m.`alasan_buang`='3' THEN 'Plebotomi Terapi'
                                    WHEN m.`alasan_buang`='4' THEN 'Reaktif IMLTD'
                                    WHEN m.`alasan_buang`='5' THEN 'Lifemik'
                                    WHEN m.`alasan_buang`='6' THEN 'Greyzone'
                                    WHEN m.`alasan_buang`='7' THEN 'DCT Positif'
                                    WHEN m.`alasan_buang`='8' THEN 'Kantong Bocor'
                                    WHEN m.`alasan_buang`='9' THEN 'Satelit Rusak'
                                    WHEN m.`alasan_buang`='10' THEN 'Bekas Pembuatan WE'
                                    WHEN m.`alasan_buang`='11' THEN 'Reaktif Dirujuk Ke UTDP'
                                    WHEN m.`alasan_buang`='12' THEN 'Hematokrit Tinggi'
                                    WHEN m.`alasan_buang`='13' THEN 'Plasma Sisa PRC'
                                    WHEN m.`alasan_buang`='14' THEN 'Leukosit Tinggi'
                                    WHEN m.`alasan_buang`='15' THEN 'Produk Rusak'
                                    WHEN m.`alasan_buang`='16' THEN 'Produk Sample QC' END as Alasan,
                                    count(m.`noKantong`) as jumlah
                                    FROM `ar_stokkantong` m inner join `stokkantong` s on s.`noKantong`=m.`noKantong`
                                    WHERE
                                    date(m.`tgl_buang`)>='$tglawal' AND date(m.`tgl_buang`)<='$tglakhir'
                                    group by m.`alasan_buang`");
            while($res = mysql_fetch_array($query)){
                $alasan = $res['Alasan'];
                $jumlah= $res['jumlah'];
                $data .= '["'.$alasan.'",'.$jumlah.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
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
                        legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"100%",height:"100%"},pieStartAngle: 0};
                    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                    chart.draw(data, options);
                };
                google.load('visualization', '1', {packages:['table']});
                google.setOnLoadCallback(drawTable);
                function drawTable() {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Alasan');
                    data.addColumn('number', 'Jumlah');
                    data.addRows([<?php echo $data; ?>]);
                    var options = {'title':''};
                    var table = new google.visualization.Table(document.getElementById('table_div'));
                    var formatter = new google.visualization.NumberFormat({prefix: '', negativeColor: 'red', negativeParens: true,fractionDigits:0,groupingSymbol:'.'});
                    formatter.format(data, 1); // Apply formatter to second column
                    table.draw(data, {allowHtml: true, showRowNumber: true});
                };
            </script>
            <?php
            break;
        case '3':
            $s3='selected';
            $title  = "PIE CHAT JENIS KOMPONEN DIMUSNAHKAN (".$tglawal.' - '.$tglakhir.')';
            $query  = mysql_query("SELECT
                        p.lengkap as komponen,
                        count(m.`noKantong`) as jumlah
                        FROM `ar_stokkantong` m inner join `stokkantong` s on s.`noKantong`=m.`noKantong`
                        inner join `produk` p on p.`Nama`=m.`produk`
                        WHERE
                        date(`tgl_buang`)>='$tglawal' AND
                        date(`tgl_buang`)<='$tglakhir'
                        group by p.`lengkap`");
            while($res = mysql_fetch_array($query)){
                $alasan = $res['komponen'];
                $jumlah= $res['jumlah'];
                $data .= '["'.$alasan.'",'.$jumlah.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
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
                    pieSliceText: 'value-and-percentage',
                    legend: {
                        position: 'labeled'
                    },
                    legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"90%",height:"90%"},pieStartAngle: 45};
                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            };
            google.load('visualization', '1', {packages:['table']});
            google.setOnLoadCallback(drawTable);
            function drawTable() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Komponen Darah');
                data.addColumn('number', 'Jumlah');
                data.addRows([<?php echo $data; ?>]);
                var options = {'title':''};
                var table = new google.visualization.Table(document.getElementById('table_div'));
                var formatter = new google.visualization.NumberFormat({prefix: '', negativeColor: 'red', negativeParens: true,fractionDigits:0,groupingSymbol:'.'});
                formatter.format(data, 1); // Apply formatter to second column
                table.draw(data, {allowHtml: true, showRowNumber: true});
            };
        </script>
        <?php
        break;
    case '4':
        $s4='selected';
        $title  = "PIE CHAT PELULUSAN KOMPONEN DARAH (".$tglawal.' - '.$tglakhir.')';
        $query  = mysql_query("SELECT
                            CASE
                            WHEN `rstatus`='0' THEN 'Release'
                            WHEN `rstatus`='1' THEN 'Rejected'
                            WHEN `rstatus`='2' THEN 'Release by note'
                            End as status,
                            count(`rid`) as jumlah
                            FROM `release`
                            WHERE
                            DATE(`rtgl`)>='$tglawal' AND DATE(`rtgl`)<='$tglakhir'
                            GROUP BY `rstatus`");
        while($res = mysql_fetch_array($query)){
            $status = $res['status'];
            $jumlah= $res['jumlah'];
            $data .= '["'.$status.'",'.$jumlah.'],';
        }
        $data = substr($data,0,(strlen($data)-1));
        ?>
        <script type="text/javascript">
            google.load('visualization', '1.0', {'packages':['corechart']});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Slices');
                data.addRows([<?php echo $data; ?>]);
                var options = {
                    'title':'',
                    'width':600,
                    'height':300,
                    is3D: true,
                    left:0,
                    titleTextStyle:{fontSize: 14, bold: true, italic: false },
                    pieSliceText: 'value-and-percentage',
                    legend: {
                        position: 'labeled'
                    },
                    legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"90%",height:"90%"},pieStartAngle: 45};
                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            };
            google.load('visualization', '1', {packages:['table']});
            google.setOnLoadCallback(drawTable);
            function drawTable() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Status');
                data.addColumn('number', 'Jumlah');
                data.addRows([<?php echo $data; ?>]);
                var options = {'title':''};
                var table = new google.visualization.Table(document.getElementById('table_div'));
                var formatter = new google.visualization.NumberFormat({prefix: '', negativeColor: 'red', negativeParens: true,fractionDigits:0,groupingSymbol:'.'});
                formatter.format(data, 1); // Apply formatter to second column
                table.draw(data, {allowHtml: true, showRowNumber: true});
            };
        </script>
        <?php
        break;
    }
}?>


<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <br>
            <div class="panel with-nav-tabs panel-primary" id="shadow1">
                <div class="panel-heading">
                    <h4>STATISTIK PENGOLAHAN DAN PEMUSNAHAN DARAH</h4>
                </div>
                <form class="form-inline" method="POST" action="pmitatausaha.php?module=graphkomponen">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            Jenis :
                            <select class="form-control" name="modelgraph">
                                <option value="1" <?=$s1?>>Pie Chart Pengolahan Komponen Darah</option>
                                <option value="4" <?=$s4?>>Pie Chart Pelulusan Komponen Darah</option>
                                <option value="2" <?=$s2?>>Pie Chart Alasan Darah dimusnahkan</option>
                                <option value="3" <?=$s3?>>Pie Chart Jenis Komponen dimusnahkan</option>
                            </select>
                            <input class="form-control datepicker" name="tgl1" id="datepicker" value="<?=$tglawal?>" type=date size=10>
                            <input class="form-control datepicker" name="tgl2" id="datepicker1" value="<?=$tglakhir?>" type=date size=10>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12"><h4 style="color: red;font-weight: bold;"><?php echo $title.' '.$title2;?></h4></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8" id="chart_div"></div>
                        <div class="col-lg-4" id="table_div"></div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" name="submit"  id="btn_upload" class="btn btn-default class_shadow2" ><i class="fa fa-bar-chart" aria-hidden="true"></i>&nbsp;&nbsp;Tampilkan Grafik</button>
                    <button type="button" class="btn btn-default class_shadow2" onclick="saveAsImg(document.getElementById('chart_div'));"><i class="fa  fa-save" aria-hidden="true"></i>&nbsp;&nbsp;Simpan Grafik</button>
                    <a href="pmitatausaha.php?module=statistik" class="btn btn-default class_shadow2" title="Kembali"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
                </div>
                </form>
        </div>
    </div>
</div>

  </body>
</html>



