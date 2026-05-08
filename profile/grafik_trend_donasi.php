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
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN JENIS DONOR TAHUN ".$v_tahun;
            $query  = mysql_query("SELECT ELT(MONTH(Tgl), 'Januari','Februari','Maret', 'April','Mei','Juni', 'Juli', 'Agustus', 'September','Oktober','November','Desember') As Bulan,
                    COUNT(case when JenisDonor=0 THEN 1 END) As DS, COUNT(case when JenisDonor=1 THEN 1 END) As DP
                    from htransaksi where year(Tgl)='$v_tahun' and (Pengambilan='0' or Pengambilan='2') group by month(Tgl)");
            while($res = mysql_fetch_array($query)){
                $bulan = $res['Bulan'];
                $ds= $res['DS'];
                $dp= $res['DP'];
                $data .= '["'.$bulan.'",'.$ds.','.$dp.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
            ?>
            <script type="text/javascript">
                google.load('visualization', '1.0', {packages:['columnchart']});
                google.setOnLoadCallback(drawChart);
                function drawChart() {
                    var data = google.visualization.arrayToDataTable([['Bulan', 'Donor Sukarela','Donor Pengganti'],<?php echo $data;?>]);
                    var options = {'title':'',
                        width:750,height:300,
                        left:0,

                        is3D: true,
                        legend:'bottom',
                        titleY:'Jumlah Donor',
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
                    data.addColumn('number', 'Donor<br>Sukarela');
                    data.addColumn('number', 'Donor<br>Pengganti');
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
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN LOKASI DONOR TAHUN ".$v_tahun;
            $query  = mysql_query("SELECT ELT(MONTH(htransaksi.Tgl), 'Januari','Februari','Maret', 'April','Mei','Juni', 'Juli', 'Agustus', 'September','Oktober','November','Desember') As Bulan,
                                    COUNT(case when left(htransaksi.NoTrans,1)='M' THEN 1 END) As 'MU',
                                    COUNT(case when left(htransaksi.NoTrans,1)='D' THEN 1 END) As 'UDD'
                                    from htransaksi where year(htransaksi.Tgl)='$v_tahun' and (htransaksi.Pengambilan='0' or htransaksi.Pengambilan='2') group by month(htransaksi.Tgl)");
            while($res = mysql_fetch_array($query)){
                $bulan = $res['Bulan'];
                $ds= $res['MU'];
                $dp= $res['UDD'];
                $data .= '["'.$bulan.'",'.$ds.','.$dp.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
            ?>
            <script type="text/javascript">
                google.load('visualization', '1.0', {packages:['columnchart']});
                google.setOnLoadCallback(drawChart);
                function drawChart() {
                    var data = google.visualization.arrayToDataTable([['Bulan', 'Mobile Unt','UDD'],<?php echo $data;?>]);
                    var options = {'title':'',
                        width:750,height:300,
                        left:0,
                        is3D: true,
                        legend:'bottom',
                        titleY:'Jumlah Donor',
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
                    data.addColumn('number', 'Mobile Unit');
                    data.addColumn('number', 'Di UDD');
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
        case '3' :
            $s3='selected';
            $title  = "GRAFIK PENYUMBANGAN DARAH BERDASARKAN DONOR LAMA/BARU TAHUN ".$v_tahun;
            $query  = mysql_query("SELECT  ELT(MONTH(Tgl), 'Januari','Februari','Maret', 'April','Mei','Juni', 'Juli', 'Agustus', 'September','Oktober','November','Desember') As Bulan,
                        COUNT(CASE WHEN `donorbaru`='0' THEN 1 END) as 'Donor Baru',
                        COUNT(CASE WHEN `donorbaru`='1' THEN 1 END) as 'Donor Lama'
                      FROM `htransaksi`
                      WHERE (Pengambilan='0' or Pengambilan='2') AND YEAR(Tgl)='$v_tahun'
                      group by month(Tgl)");
            while($res = mysql_fetch_array($query)){
                $bulan = $res['Bulan'];
                $ds= $res['Donor Lama'];
                $dp= $res['Donor Baru'];
                $data .= '["'.$bulan.'",'.$ds.','.$dp.'],';
            }
            $data = substr($data,0,(strlen($data)-1));
            ?>
            <script type="text/javascript">
                google.load('visualization', '1.0', {packages:['columnchart']});
                google.setOnLoadCallback(drawChart);
                function drawChart() {
                    var data = google.visualization.arrayToDataTable([['Bulan', 'Donor Lama','Donor Baru'],<?php echo $data;?>]);
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
                    data.addColumn('number', 'Donor Lama');
                    data.addColumn('number', 'Donor Baru');
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

    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <br>
            <div class="panel with-nav-tabs panel-primary" id="shadow1">
                <div class="panel-heading">
                    <h4>TREND PENYUMBANGAN DARAH</h4>
                </div>
                <form class="form-inline" method="POST" action="pmitatausaha.php?module=graphtrendbulanan">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            Jenis :
                            <select class="form-control" name="modelgraph">
                                <option value="1" <?=$s1?>>Donor Sukarela/Pengganti</option>
                                <option value="2" <?=$s2?>>Lokasi penyumbangan</option>
                                <option value="3" <?=$s3?>>Donor Lama/Baru</option>
                            </select>
                            Tahun
                                <input class="form-control" name="tahun" id="datepicker" value="<?=$v_tahun?>" type=date size=10>
                            </td>
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
