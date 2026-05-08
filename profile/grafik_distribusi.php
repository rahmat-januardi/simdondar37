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
            $title  = "GRAFIK HASIL UJI SILANG SERASI(".$tglawal.' - '.$tglakhir.')';
            $query  = mysql_query("SELECT stat2 AS `StatusCross`,
                    count(`NoKantong`) as jumlah
                    FROM `dtransaksipermintaan`
                    WHERE
                    Date(`tgl`)>='$tglawal' and Date(`tgl`)<='$tglakhir'
                    Group by `stat2`");
            while($res = mysql_fetch_array($query)){
                $komp = $res['StatusCross'];
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
                    data.addColumn('string', 'Hasil');
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
            $title  = "GRAFIK STATUS PEMERIKSAAN UJI SILANG SERASI  (".$tglawal.' - '.$tglakhir.')';
            $query  = mysql_query("SELECT CASE
                    WHEN `StatusCross`='1' THEN 'Compatible'
                    WHEN `StatusCross`='0' THEN 'Incompatible Boleh Keluar'
                    WHEN `StatusCross`='2' THEN 'Incompatible Tidak Boleh Keluar' END AS `StatusCross`,
                    count(`NoKantong`) as jumlah
                    FROM `dtransaksipermintaan`
                    WHERE
                    Date(`tgl`)>='$tglawal' and Date(`tgl`)<='$tglakhir'
                    Group by `StatusCross`");
            while($res = mysql_fetch_array($query)){
                $status = $res['StatusCross'];
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
                    var options = {'title':'','width':600,'height':300,is3D: true,left:0,titleTextStyle:{fontSize: 14, bold: true, italic: false },
                        legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"100%",height:"100%"},pieStartAngle: 0};
                    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                    chart.draw(data, options);
                };
                google.load('visualization', '1', {packages:['table']});
                google.setOnLoadCallback(drawTable);
                function drawTable() {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Kesimpulan');
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
            $title  = "GRAFIK PENDISTRIBUSIAN DARAH (".$tglawal.' - '.$tglakhir.')';
            $out_rs=mysql_query("SELECT count(h.`rs`) as jumlah
                FROM `htranspermintaan` h
                inner join `pasien` p on p.`no_rm`=h.`no_rm`
                inner join `dtransaksipermintaan` d on d.`NoForm`=h.`noform`
                inner join `rmhsakit` r on r.`Kode`=h.`rs`WHERE
                DATE(d.`tgl_keluar`)>='$tglawal' and DATE(d.`tgl_keluar`)<='$tglakhir' AND d.`Status`='0'");
            $out_rs=mysql_fetch_assoc($out_rs);
            $tujuan = 'Rumah Sakit';
            $jumlah= $out_rs['jumlah'];
            $data .= '["'.$tujuan.'",'.$jumlah.'],';

            $out_bdrs=mysql_query("SELECT count(k.`bdrs`) as jumlah
                FROM `kirimbdrs` k
                inner join `bdrs` b on b.`kode`=k.`bdrs`
                inner join `stokkantong` s on s.`noKantong`=k.`nokantong`
                inner join `user` u on u.`id_user`= k.`petugas`
                WHERE date(k.`tgl`)>='$tglawal' AND date(k.`tgl`)<='$tglakhir'");
            $out_bdrs=mysql_fetch_assoc($out_bdrs);
            $tujuan = 'BDRS';
            $jumlah= $out_bdrs['jumlah'];
            $data .= '["'.$tujuan.'",'.$jumlah.'],';
            $out_udd=mysql_query("SELECT count(k.`udd`) as jumlah
                FROM `kirimudd` k
                inner join `utd` b on b.`id`=k.`udd`
                inner join `stokkantong` s on s.`noKantong`=k.`nokantong`
                inner join `user` u on u.`id_user`= k.`petugas`
                WHERE date(k.`tgl`)>='$tglawal' AND date(k.`tgl`)<='$tglakhir'");
            $out_udd=mysql_fetch_assoc($out_udd);
            $tujuan = 'UDD PMI';
            $jumlah= $out_udd['jumlah'];
            $data .= '["'.$tujuan.'",'.$jumlah.'],';
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
                data.addColumn('string', 'Tujuan');
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
        $title  = "GRAFIK PENGELUARAN DARAH KE RS BERDASARKAN BAGIAN DI RS  (".$tglawal.' - '.$tglakhir.')';
        $query  = mysql_query("SELECT h.`bagian`, count(h.`bagian`) as jumlah
                FROM `htranspermintaan` h
                inner join `pasien` p on p.`no_rm`=h.`no_rm`
                inner join `dtransaksipermintaan` d on d.`NoForm`=h.`noform`
                inner join `rmhsakit` r on r.`Kode`=h.`rs`WHERE
                DATE(d.`tgl_keluar`)>='$tglawal' and DATE(d.`tgl_keluar`)<='$tglakhir' AND d.`Status`='0'
                GROUP by h.`bagian`");
        $total=0;
        while($res = mysql_fetch_array($query)){
            $status = $res['bagian'];
            $jumlah= $res['jumlah'];
            $total=$total+$jumlah;
            $data .= '["'.$status.'",'.$jumlah.'],';
        }
        $status='TOTAL';
        $data .= '["'.$status.'",'.$total.'],';
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
                data.addColumn('string', 'Bagian');
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
        $title  = "GRAFIK PENGELUARAN DARAH KE RS BERDASARKAN LETAK RUMAH SAKIT  (".$tglawal.' - '.$tglakhir.')';
        $query  = mysql_query("SELECT
                    Case
                    when r.`wilayah`='0' then 'Dalam Kota'
                    when r.`wilayah`='1' then 'Luar Kota'
                    Else 'N/A' end as wilayah,
                    COUNT(DISTINCT h.`rs`) AS jumlah
                    FROM `htranspermintaan` h inner join `rmhsakit` r on r.`Kode`=h.`rs`
                    WHERE
                    date(h.`tgl_register`)>='$tglawal' and date(h.`tgl_register`)<='$tglakhir'
                    group by r.`wilayah`");
        while($res = mysql_fetch_array($query)){
            $status = $res['wilayah'];
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
                var options = {'title':'','width':600,'height':300,is3D: true,left:0,titleTextStyle:{fontSize: 14, bold: true, italic: false },
                    legendtextStyle:{fontSize: 8, bold: false, italic: false },chartArea:{left:10,top:10,width:"100%",height:"100%"},pieStartAngle: 0};
                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            };
            google.load('visualization', '1', {packages:['table']});
            google.setOnLoadCallback(drawTable);
            function drawTable() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Bagian');
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
                    <h4>STATISTIK UJI SILANG SERASI DAN DISTRIBUSI DARAH</h4>
                </div>
                <form class="form-inline" method="POST" action="pmitatausaha.php?module=graphdistribusi">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            Jenis :
                            <select class="form-control" name="modelgraph">
                                <option value="1" <?=$s1?>>Grafik Hasil Uji Silang Serasi</option>
                                <option value="2" <?=$s2?>>Grafik Status Pemeriksaan Uji Silang Serasi</option>
                                <option value="3" <?=$s3?>>Grafik Sebaran Pendistribusian Darah</option>
                                <option value="4" <?=$s4?>>Grafik Pengeluaran Darah berdasarkan Bagian di Rumah Sakit</option>
                                <option value="5" <?=$s5?>>Grafik Pengeluaran Darah berdasarkan Wilayah Rumah Sakit</option>
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



