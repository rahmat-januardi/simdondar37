<!DOCTYPE html>
<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$title="";
$s1=""; $s2="";$s3="";$s1=""; $s4="";$s5="";$s6=""; $s7="";$s8="";
$level_user=$_SESSION['leveluser'];
$tahunini=date("Y");
$v_tahun=$_POST['tahun'];

if (empty($v_tahun)){$v_tahun=$tahunini;}

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
    <link type="text/css" href="../css/blitzer/suwena.css" rel="stylesheet" />
</head>
<body>
<?php
if (isset($_POST['submit'])) {
    $model=$_POST['modelgraph'];
    switch ($model){
        case '1' :
            $title  = "GRAFIK PENDONOR BERDASARKAN PEKERJAAN";
            $query   = mysql_query("SELECT pekerjaan, COUNT( Kode ) AS jml FROM pendonor GROUP BY pekerjaan");
            while($res = mysql_fetch_array($query)){$pekerjaan = $res['pekerjaan'];$jumlah= $res['jml'];$data .= '["'.$pekerjaan.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s1="selected";
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
                        legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"90%",height:"90%"},pieStartAngle: 0};
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
            </script><?
            break;
        case '2':
            $title  = "GRAFIK PENDONOR BERDASARKAN DONASI";
            $query   = mysql_query("SELECT CASE WHEN (jumDonor) BETWEEN  1 AND 10 THEN '1-10 Kali' WHEN (jumDonor) BETWEEN  11 AND 25 THEN '11-25 Kali' WHEN (jumDonor) BETWEEN  26 AND 50 THEN '26-50 Kali'
                WHEN (jumDonor) BETWEEN  51 AND 75 THEN '51-75 Kali' WHEN (jumDonor) BETWEEN  76 AND 100 THEN '76-100 Kali' WHEN (jumDonor) > 100 THEN '>100 Kali' END as donasi,
                COUNT(Kode) AS jml FROM pendonor where jumDonor>0 GROUP BY CASE WHEN (jumDonor) BETWEEN  1 AND 10 THEN '1-10 Kali' WHEN (jumDonor) BETWEEN  11 AND 25 THEN '11-25 Kali'
                WHEN (jumDonor) BETWEEN  26 AND 50 THEN '26-50 Kali' WHEN (jumDonor) BETWEEN  51 AND 75 THEN '51-75 Kali'
                WHEN (jumDonor) BETWEEN  76 AND 100 THEN '76-100 Kali' WHEN (jumDonor) > 100 THEN '>100 Kali' END");
            while($res = mysql_fetch_array($query)){$umur = $res['donasi'];$jumlah= $res['jml'];$data .= '["'.$umur.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s2="selected";
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
                            legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"90%",height:"90%"},pieStartAngle: 0};
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
                </script><?
                break;
        case '3':
            $title  = "GRAFIK  PENDONOR BERDASARKAN RENTANG UMUR";
            $query   = mysql_query("SELECT
                CASE  WHEN FLOOR((TO_DAYS(NOW())- TO_DAYS(TglLhr)) / 365.25) BETWEEN  18 AND 24 THEN 'Umur 18-24 Tahun'
                      WHEN FLOOR((TO_DAYS(NOW())- TO_DAYS(TglLhr)) / 365.25) BETWEEN  25 AND 44 THEN 'Umur 25-44 Tahun'
                      WHEN FLOOR((TO_DAYS(NOW())- TO_DAYS(TglLhr)) / 365.25) BETWEEN  45 AND 64 THEN 'Umur 45-64 Tahun'
                      WHEN FLOOR((TO_DAYS(NOW())- TO_DAYS(TglLhr)) / 365.25) >= 65 THEN 'Umur 60 Tahun ke atas'
                      ELSE 'Umur 17 Tahun' END AS Umur,
                COUNT(Kode) AS jml
                FROM pendonor
                GROUP BY
                CASE  WHEN FLOOR((TO_DAYS(NOW())- TO_DAYS(TglLhr)) / 365.25) BETWEEN  18 AND 24 THEN 'Umur 18-24 Tahun'
                      WHEN FLOOR((TO_DAYS(NOW())- TO_DAYS(TglLhr)) / 365.25) BETWEEN  25 AND 44 THEN 'Umur 25-44 Tahun'
                      WHEN FLOOR((TO_DAYS(NOW())- TO_DAYS(TglLhr)) / 365.25) BETWEEN  45 AND 64 THEN 'Umur 45-64 Tahun'
                      WHEN FLOOR((TO_DAYS(NOW())- TO_DAYS(TglLhr)) / 365.25) >= 65 THEN 'Umur 60 Tahun ke atas'
                      ELSE 'Umur 17 Tahun' END");
            while($res = mysql_fetch_array($query)){$umur = $res['Umur'];$jumlah= $res['jml'];$data .= '["'.$umur.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s3="selected";
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
                            legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"90%",height:"90%"},pieStartAngle: 90};
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
                </script><?
                break;
        case '4' :
            $title  = "GRAFIK PENDONOR BERDASARKAN GOLONGAN DARAH ABO";
            $query   = mysql_query("SELECT GolDarah, COUNT( Kode ) AS jml FROM pendonor where GolDarah<>'X' GROUP BY GolDarah");
            while($res = mysql_fetch_array($query)){$golongan = $res['GolDarah'];$jumlah= $res['jml'];$data .= '["'.$golongan.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s4="selected";
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
                        legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"90%",height:"90%"},pieStartAngle: 30};
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
            </script><?
            break;
        case '5' :
            $title  = "GRAFIK PENDONOR BERDASARKAN GOLONGAN DARAH RHESUS";
            $query   = mysql_query("SELECT case when Rhesus='-' then 'Rhesus Negatif(-)' else 'Rhesus Positif(+)' end as Rh , COUNT( Kode ) AS jml FROM pendonor GROUP BY case when Rhesus='-' then 'Rh Negatif' else 'Rhesus Negatif' end ");
            while($res = mysql_fetch_array($query)){$golongan = $res['Rh'];$jumlah= $res['jml'];$data .= '["'.$golongan.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s5="selected";
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
                </script><?
                break;
        case '6' :
            $title  = "GRAFIK PENDONOR BERDASARKAN JENIS KELAMIN";
            $query   = mysql_query("SELECT case when Jk='0' then 'Laki-laki' else 'Perempuan' end as kel, COUNT( Kode ) AS jml FROM pendonor GROUP BY case when Jk='0' then 'Laki-laki' else 'Perempuan' end");
            while($res = mysql_fetch_array($query)){$kelamin = $res['kel'];$jumlah= $res['jml'];$data .= '["'.$kelamin.'",'.$jumlah.'],';}
            $data = substr($data,0,(strlen($data)-1));$s6="selected";
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
                            legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"90%",height:"90%"},pieStartAngle: 60};
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
                </script><?
                break;
        case '7' :
            $title  = "GRAFIK PERBANDINGAN TARGET MOBILE UNIT DENGAN PEROLEHAN DARAH";
            $title2 = " TAHUN ".$v_tahun;
            $query   =mysql_query("SELECT ELT(MONTH(`TglPelaksanaan`), 'Januari','Februari','Maret', 'April','Mei','Juni', 'Juli', 'Agustus', 'September','Oktober','November','Desember') As Bulan,
                sum(`jumlah`) as target,
                sum(`sukses`) as donasi
                FROM `kegiatan`
                WHERE year(`TglPelaksanaan`)='$v_tahun'
                group by month(`TglPelaksanaan`)");
            while($res = mysql_fetch_array($query)){
                $bulan = $res['Bulan'];
                $target= $res['target'];
                $donasi= $res['donasi'];
                $data .= '["'.$bulan.'",'.$target.','.$donasi.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
            $s7="selected";
            ?>
            <script type="text/javascript">
            google.load('visualization', '1.0', {packages:['columnchart']});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
            var data = google.visualization.arrayToDataTable([['Bulan', 'Target','Donasi'],<?php echo $data;?>]);
            var options = {'title':'',
            width:750,height:300,
            left:0,
            is3D: true,
            legend:'bottom',
            titleY:'Jumlah',
            titleX:'Bulan'
            };
            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);
            };
            google.load('visualization', '1', {packages:['table']});
            google.setOnLoadCallback(drawTable);
            function drawTable() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Bulan');
            data.addColumn('number', 'Target');
            data.addColumn('number', 'Donasi');
            data.addRows([<?php echo $data; ?>]);
            var options = {'title':''};
            var table = new google.visualization.Table(document.getElementById('table_div'));
            var formatter = new google.visualization.NumberFormat({prefix: '', negativeColor: 'red', negativeParens: true,fractionDigits:0,groupingSymbol:'.'});
            formatter.format(data, 1);
            formatter.format(data, 2);
            table.draw(data, {allowHtml: true, showRowNumber: true});
            };
            </script>
            <?php
            break;
        case '8' :
            $title  = "GRAFIK JUMLAH KEGIATAN MOBILE UNIT PER BULAN";
            $title2 = "SELAMA TAHUN ".$v_tahun;
            $query   =mysql_query("SELECT ELT(MONTH(`TglPelaksanaan`), 'Januari','Februari','Maret', 'April','Mei','Juni', 'Juli', 'Agustus', 'September','Oktober','November','Desember') As Bulan,
                    COUNT(`NoTrans`) AS jml
                    FROM `kegiatan`
                    WHERE
                    year(`TglPelaksanaan`)='$v_tahun' group by month(`TglPelaksanaan`)");
            while($res = mysql_fetch_array($query)){
                $bulan = $res['Bulan'];
                $jumlah= $res['jml'];
                $data .= '["'.$bulan.'",'.$jumlah.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
            $s8="selected";
            ?>
        <script type="text/javascript">
            google.load('visualization', '1.0', {packages:['columnchart']});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([['Bulan', 'Jumlah'],<?php echo $data;?>]);
                var options = {'title':'',
                    width:750,height:300,
                    left:0,
                    is3D: true,
                    legend:'bottom',
                    titleY:'Jumlah',
                    titleX:'Bulan'
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            };
            google.load('visualization', '1', {packages:['table']});
            google.setOnLoadCallback(drawTable);
            function drawTable() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Bulan');
                data.addColumn('number', 'Jumlah');
                data.addRows([<?php echo $data; ?>]);
                var options = {'title':''};
                var table = new google.visualization.Table(document.getElementById('table_div'));
                var formatter = new google.visualization.NumberFormat({prefix: '', negativeColor: 'red', negativeParens: true,fractionDigits:0,groupingSymbol:'.'});
                formatter.format(data, 1);
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
                    <h4>STATISTIK PENCARIAN & PELESTARIAN DONOR DARAH SUKARELA</h4>
                </div>
                <form class="form-inline" method="POST" action="pmitatausaha.php?module=graphdonor">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            Jenis Grafik:
                            <select class="form-control" name="modelgraph">
                                <option value="1" <?=$s1?>>Grafik Jumlah Pendonor Berdasarkan Pekerjaan</option>
                                <option value="2" <?=$s2?>>Grafik Jumlah Pendonor Berdasarkan Donasi</option>
                                <option value="3" <?=$s3?>>Grafik Jumlah Pendonor Berdasarkan Rentang Umur</option>
                                <option value="4" <?=$s4?>>Grafik Jumlah Pendonor Berdasarkan Golongan Darah ABO</option>
                                <option value="5" <?=$s5?>>Grafik Jumlah Pendonor Berdasarkan Golongan Darah Rhesus</option>
                                <option value="6" <?=$s6?>>Grafik Jumlah Pendonor Berdasarkan Jenis Kelamin</option>
                                <option value="7" <?=$s7?>>Grafik Perbandingan Target Mobile Unit dengan Perolehan Darah</option>
                                <option value="8" <?=$s8?>>Grafik Jumlah Kegiatan Mobile Unit per Bulan</option>
                            </select>
                            Tahun
                            <input class="form-control" name="tahun" id="datepicker" value="<?=$v_tahun?>" type=date size=10><small>Tidak berlaku untuk seluruh grafik</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12"><h4 style="color: red;font-weight: bold;"><?php echo $title.' '.$title2;?></h4></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8" id="chart_div">

                        </div>
                        <div class="col-lg-4" id="table_div">

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" name="submit"  id="btn_upload" class="btn btn-default class_shadow2" ><i class="fa fa-pie-chart" aria-hidden="true"></i>&nbsp;&nbsp;Tampilkan Grafik</button>
                    <button type="button" class="btn btn-default class_shadow2" onclick="saveAsImg(document.getElementById('chart_div'));"><i class="fa  fa-save" aria-hidden="true"></i>&nbsp;&nbsp;Simpan Grafik</button>
                    <a href="pmitatausaha.php?module=statistik" class="btn btn-default class_shadow2" title="Kembali"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Kembali</a>
                </div>
                </form>
        </div>
    </div>
</div>

  </body>
</html>
