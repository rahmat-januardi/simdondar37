<!DOCTYPE html>
<?php
session_start();
require_once('clogin.php');
require_once('config/db_connect.php');
$title="";
$s1=""; $s2="";$s3="";$s4=""; $s5="";$s6="";$s7="";$s8="";$s9="";$s10="";$parameter='';

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
            $title  = "GRAFIK PEMERIKSAAN KONFIRMASI GOLONGAN DARAH (".$tglawal.' - '.$tglakhir.')';
            $query  = mysql_query("SELECT  ELT(MONTH(`tgl`), 'Januari','Februari','Maret', 'April','Mei','Juni', 'Juli', 'Agustus', 'September','Oktober','November','Desember') As Bulan,
                        COUNT(CASE WHEN `Cocok`='0' THEN 1 END) as 'Cocok',
                        COUNT(CASE WHEN `Cocok`='1' THEN 1 END) as 'Tidak_Cocok'
                      FROM `dkonfirmasi`
                      WHERE
                      date(`tgl`)>='$tglawal' and date(`tgl`)<='$tglakhir'
                      group by year(`tgl`),month(`tgl`)");
            while($res = mysql_fetch_array($query)){
                $bulan = $res['Bulan'];
                $cck= $res['Cocok'];
                $tdkcck= $res['Tidak_Cocok'];
                $data .= '["'.$bulan.'",'.$cck.','.$tdkcck.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
            ?>
            <script type="text/javascript">
                google.load('visualization', '1.0', {packages:['columnchart']});
                google.setOnLoadCallback(drawChart);
                function drawChart() {
                    var data = google.visualization.arrayToDataTable([['Bulan', 'Sesuai/Cocok','Tidak Sesuai/Tidak Cocok'],<?php echo $data;?>]);
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
                    data.addColumn('number', 'Sesuai/Cocok');
                    data.addColumn('number', 'Tidak Sesuai/<br>Tidak Cocok');
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
        case '2':
            $s2='selected';
            $title  = "GRAFIK PERBANDINGAN HASIL KONFIRMASI GOLONGAN DARAH (".$tglawal.' - '.$tglakhir.')';
            $query   = mysql_query("SELECT
                        CASE
                        WHEN `Cocok`='0' THEN 'Sesuai/Cocok' ELSE 'Tidak Sesuai/Tidak Cocok' END as keterangan,
                        count(`id`) as jml
                        FROM `dkonfirmasi`
                        WHERE
                        date(`tgl`)>='$tglawal' and date(`tgl`)<='$tglakhir'
                        group by `Cocok`");
            while($res = mysql_fetch_array($query)){
                $jenishasil = $res['keterangan'];
                $jumlah= $res['jml'];
                $data .= '["'.$jenishasil.'",'.$jumlah.'],';
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
                    var options = {'title':'','width':600,'height':300,is3D: false,left:0,titleTextStyle:{fontSize: 14, bold: true, italic: false },
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
            <?php
            break;
        case '3':
            $s3='selected';
            $parameter='0';
            $title  = "GRAFIK UJI SARING IMLTD HEPATITIS B (".$tglawal.' - '.$tglakhir.')';
            $query   = mysql_query("SELECT
                            case
                            when `hasilelisa`.`Hasil`='0' then 'Non Reaktif'
                            when `hasilelisa`.`Hasil`='1' then 'Reaktif'
                            when `hasilelisa`.`Hasil`='2' then 'Greyzone' END as hasil,
                            count(distinct(`hasilelisa`.`noKantong`)) as jumlah
                            FROM `hasilelisa` inner join `stokkantong` on `stokkantong`.`noKantong`=`hasilelisa`.`noKantong`
                            WHERE
                            date(`hasilelisa`.`tglPeriksa`)>='$tglawal' AND date(`hasilelisa`.`tglPeriksa`)<='$tglakhir'
                            AND `hasilelisa`.`jenisPeriksa`='$parameter'
                            group by `hasilelisa`.`Hasil`");
            while($res = mysql_fetch_array($query)){
                $hasil = $res['hasil'];
                $jumlah= $res['jumlah'];
                $data .= '["'.$hasil.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Hasil IMLTD');
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
        $parameter='1';
        $title  = "GRAFIK UJI SARING IMLTD HEPATITIS C (".$tglawal.' - '.$tglakhir.')';
        $query   = mysql_query("SELECT
                            case
                            when `hasilelisa`.`Hasil`='0' then 'Non Reaktif'
                            when `hasilelisa`.`Hasil`='1' then 'Reaktif'
                            when `hasilelisa`.`Hasil`='2' then 'Greyzone' END as hasil,
                            count(distinct(`hasilelisa`.`noKantong`)) as jumlah
                            FROM `hasilelisa` inner join `stokkantong` on `stokkantong`.`noKantong`=`hasilelisa`.`noKantong`
                            WHERE
                            date(`hasilelisa`.`tglPeriksa`)>='$tglawal' AND date(`hasilelisa`.`tglPeriksa`)<='$tglakhir'
                            AND `hasilelisa`.`jenisPeriksa`='$parameter'
                            group by `hasilelisa`.`Hasil`");
        while($res = mysql_fetch_array($query)){
            $hasil = $res['hasil'];
            $jumlah= $res['jumlah'];
            $data .= '["'.$hasil.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Hasil IMLTD');
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
    case '5':
        $s5='selected';
        $parameter='2';
        $title  = "GRAFIK UJI SARING IMLTD ANTI-HIV (".$tglawal.' - '.$tglakhir.')';
        $query   = mysql_query("SELECT
                            case
                            when `hasilelisa`.`Hasil`='0' then 'Non Reaktif'
                            when `hasilelisa`.`Hasil`='1' then 'Reaktif'
                            when `hasilelisa`.`Hasil`='2' then 'Greyzone' END as hasil,
                            count(distinct(`hasilelisa`.`noKantong`)) as jumlah
                            FROM `hasilelisa` inner join `stokkantong` on `stokkantong`.`noKantong`=`hasilelisa`.`noKantong`
                            WHERE
                            date(`hasilelisa`.`tglPeriksa`)>='$tglawal' AND date(`hasilelisa`.`tglPeriksa`)<='$tglakhir'
                            AND `hasilelisa`.`jenisPeriksa`='$parameter'
                            group by `hasilelisa`.`Hasil`");
        while($res = mysql_fetch_array($query)){
            $hasil = $res['hasil'];
            $jumlah= $res['jumlah'];
            $data .= '["'.$hasil.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Hasil IMLTD');
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
    case '6':
        $s6='selected';
        $parameter='3';
        $title  = "GRAFIK UJI SARING IMLTD SIFILIS (".$tglawal.' - '.$tglakhir.')';
        $query   = mysql_query("SELECT
                            case
                            when `hasilelisa`.`Hasil`='0' then 'Non Reaktif'
                            when `hasilelisa`.`Hasil`='1' then 'Reaktif'
                            when `hasilelisa`.`Hasil`='2' then 'Greyzone' END as hasil,
                            count(distinct(`hasilelisa`.`noKantong`)) as jumlah
                            FROM `hasilelisa` inner join `stokkantong` on `stokkantong`.`noKantong`=`hasilelisa`.`noKantong`
                            WHERE
                            date(`hasilelisa`.`tglPeriksa`)>='$tglawal' AND date(`hasilelisa`.`tglPeriksa`)<='$tglakhir'
                            AND `hasilelisa`.`jenisPeriksa`='$parameter'
                            group by `hasilelisa`.`Hasil`");
        while($res = mysql_fetch_array($query)){
            $hasil = $res['hasil'];
            $jumlah= $res['jumlah'];
            $data .= '["'.$hasil.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Hasil IMLTD');
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
    case '7':
        $s7='selected';
        $parameter='0';
        $title  = "GRAFIK REAKTIF UJI SARING IMLTD HEPATITIS B (".$tglawal.' - '.$tglakhir.')';
        $query   = mysql_query("SELECT
                            CASE
                            WHEN ht.`donorbaru`='0' THEN 'Donor Baru'
                            WHEN ht.`donorbaru`='1' THEN 'Donor Lama' END AS jenisdonor,
                            count(distinct(he.`noKantong`)) as jumlah
                            FROM `hasilelisa` he  inner join `htransaksi` ht on ht.`Nokantong`=he.`noKantong`
                            WHERE
                            he.`Hasil` in ('1','2') AND `jenisPeriksa`='$parameter'
                            AND
                            DATE(he.`tglPeriksa`)>='$tglawal' and DATE(he.`tglPeriksa`)<='$tglakhir'
                            GROUP BY ht.`donorbaru`");
        while($res = mysql_fetch_array($query)){
            $hasil = $res['jenisdonor'];
            $jumlah= $res['jumlah'];
            $data .= '["'.$hasil.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Jenis Donor');
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
    case '8':
        $s8='selected';
        $parameter='1';
        $title  = "GRAFIK REAKTIF UJI SARING IMLTD HEPATITIS C (".$tglawal.' - '.$tglakhir.')';
        $query   = mysql_query("SELECT
                            CASE
                            WHEN ht.`donorbaru`='0' THEN 'Donor Baru'
                            WHEN ht.`donorbaru`='1' THEN 'Donor Lama' END AS jenisdonor,
                            count(distinct(he.`noKantong`)) as jumlah
                            FROM `hasilelisa` he  inner join `htransaksi` ht on ht.`Nokantong`=he.`noKantong`
                            WHERE
                            he.`Hasil` in ('1','2') AND `jenisPeriksa`='$parameter'
                            AND
                            DATE(he.`tglPeriksa`)>='$tglawal' and DATE(he.`tglPeriksa`)<='$tglakhir'
                            GROUP BY ht.`donorbaru`");
        while($res = mysql_fetch_array($query)){
            $hasil = $res['jenisdonor'];
            $jumlah= $res['jumlah'];
            $data .= '["'.$hasil.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Jenis Donor');
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
    case '9':
        $s9='selected';
        $parameter='2';
        $title  = "GRAFIK REAKTIF UJI SARING IMLTD ANTI-HIV (".$tglawal.' - '.$tglakhir.')';
        $query   = mysql_query("SELECT
                            CASE
                            WHEN ht.`donorbaru`='0' THEN 'Donor Baru'
                            WHEN ht.`donorbaru`='1' THEN 'Donor Lama' END AS jenisdonor,
                            count(distinct(he.`noKantong`)) as jumlah
                            FROM `hasilelisa` he  inner join `htransaksi` ht on ht.`Nokantong`=he.`noKantong`
                            WHERE
                            he.`Hasil` in ('1','2') AND `jenisPeriksa`='$parameter'
                            AND
                            DATE(he.`tglPeriksa`)>='$tglawal' and DATE(he.`tglPeriksa`)<='$tglakhir'
                            GROUP BY ht.`donorbaru`");
        while($res = mysql_fetch_array($query)){
            $hasil = $res['jenisdonor'];
            $jumlah= $res['jumlah'];
            $data .= '["'.$hasil.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Jenis Donor');
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
    case '10':
        $s10='selected';
        $parameter='3';
        $title  = "GRAFIK REAKTIF UJI SARING IMLTD SIFILIS (".$tglawal.' - '.$tglakhir.')';
        $query   = mysql_query("SELECT
                            CASE
                            WHEN ht.`donorbaru`='0' THEN 'Donor Baru'
                            WHEN ht.`donorbaru`='1' THEN 'Donor Lama' END AS jenisdonor,
                            count(distinct(he.`noKantong`)) as jumlah
                            FROM `hasilelisa` he  inner join `htransaksi` ht on ht.`Nokantong`=he.`noKantong`
                            WHERE
                            he.`Hasil` in ('1','2') AND `jenisPeriksa`='$parameter'
                            AND
                            DATE(he.`tglPeriksa`)>='$tglawal' and DATE(he.`tglPeriksa`)<='$tglakhir'
                            GROUP BY ht.`donorbaru`");
        while($res = mysql_fetch_array($query)){
            $hasil = $res['jenisdonor'];
            $jumlah= $res['jumlah'];
            $data .= '["'.$hasil.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Jenis Donor');
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
                    <h4>STATISTIK PENGUJIAN SAMPLE DARAH DONOR</h4>
                </div>
                <form class="form-inline" method="POST" action="pmitatausaha.php?module=graphpengujian">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            Jenis :
                            <select class="form-control" name="modelgraph">
                                <option value="1" <?=$s1?>>Trend Konfirmasi Golongan Darah</option>
                                <option value="2" <?=$s2?>>Perbandingan Hasil Konfirmasi</option>
                                <option value="3" <?=$s3?>>Pemeriksaan Uji Saring IMLTD Hepatitis B</option>
                                <option value="4" <?=$s4?>>Pemeriksaan Uji Saring IMLTD Hepatitis C</option>
                                <option value="5" <?=$s5?>>Pemeriksaan Uji Saring IMLTD HIV</option>
                                <option value="6" <?=$s6?>>Pemeriksaan Uji Saring IMLTD Sifilis</option>
                                <option value="7" <?=$s7?>>Grafik Reaktif Hepatitis B</option>
                                <option value="8" <?=$s8?>>Grafik Reaktif Hepatitis C</option>
                                <option value="9" <?=$s9?>>Grafik Reaktif ANTI-HIV</option>
                                <option value="10" <?=$s10?>>Grafik Reaktif Sifilis</option>
                            </select>
                            <input class="form-control datepicker" name="tgl1" id="datepicker" value="<?=$tglawal?>" type=date size=10>
                            <input class="form-control datepicker" name="tgl2" id="datepicker1" value="<?=$tglakhir?>" type=date size=10>
                            <br><small>Grafik Reaktif memerlukan waktu lama untuk proses query</small>
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
