<?php
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$tglsebelum = mktime(0,0,0,date("m"),1,date("Y"));
$tglawal=date("Y-m-d");
$hariini = date("Y-m-d");
?>
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" language="javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<script language=javascript src="util.js" type="text/javascript"> </script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<style>
    tr { background-color: #ffffff;}
    .initial { background-color: #ffffff; color:#000000 }
    .normal { background-color: #ffffff; }
    .highlight { background-color: #7CFC00 }
</style>
<style type="text/css">.styled-select select {background-color: #FCF9F9; border: none;width: auto;padding: 3px;font-size: 15px;cursor: pointer; }</style>
<style>
    table {
        border-collapse: collapse;
    }
    table, th, td {
        border: 1px solid brown;
    }
</style>
<html xmlns="http://www.w3.org/1999/xhtml">
<style>body {font-family: "Lato", sans-serif;}</style>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>SIMDONDAR</title>
</head>

<body>
	<?
		if (isset($_POST[waktu])) {$tglawal=$_POST[waktu];$hariini=$hariini;}
		if ($_POST[waktu1]!='') $hariini=$_POST[waktu1];
        $status=$_POST['status'];
        $petugas=$_POST['petugas'];       
	?>
    <a name="atas" id="atas"></a>
	<font size="4" color=00008B>REKAP <b>PRODUK RELEASE</b></font><br><br>
	<form name="cari" method="POST" action="<?echo $PHPSELF?>">
		<table cellpadding=1 cellspacing="0" border="0">
            <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
				<td align="left" nowrap>Tanggal <input name="waktu" id="datepicker"  value="<?=$tglawal?>" type=text size="10" style="font-family:monospace"></td>
				<td align="right" nowrap>s/d <input name="waktu1" id="datepicker1" value="<?=$hariini?>" type=text size="10" style="font-family:monospace"></td>
				<td><input type=submit name=submit class="swn_button_blue" value="Tampilkan data">
               	<a href="pmiqa.php?module=input_qa"class="swn_button_blue">Kembali</a></td>
			</tr>
		</table>	
	</form>
    <br><font size="4" color=00008B>Produk yang diluluskan</b></font>
	<table border=1 cellpadding=4  style="border-collapse:collapse" >
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
			<th rowspan="3" width="25px">NO.</th>
            <th rowspan="3" width="300px">JENIS PRODUK</th>
            <th colspan="10">GOLONGAN DARAH</th>
			<th rowspan="3">TOTAL</th>
		</tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th colspan="5">RHESUS POSITIF</th>
            <th colspan="5">RHESUS NEGATIF</th>
        </tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th width="60px">A</th>
            <th width="60px">B</th>
            <th width="60px">O</th>
            <th width="60px">AB</th>
            <th width="60px">JML</th>
            <th width="60px">A</th>
            <th width="60px">B</th>
            <th width="60px">O</th>
            <th width="60px">AB</th>
            <th width="60px">JML</th>
        </tr>

	<?php
	$no=0;
	$sql="SELECT p.`no`, r.`rproduk`, p.`lengkap`,
          count(CASE WHEN r.`rgolda`='A+' then 1 else null end) as `A+`,
          count(CASE WHEN r.`rgolda`='B+' then 1 else null end) as `B+`,
          count(CASE WHEN r.`rgolda`='O+' then 1 else null end) as `O+`,
          count(CASE WHEN r.`rgolda`='AB+' then 1 else null end) as `AB+`,
          count(CASE WHEN r.`rgolda`='A-' then 1 else null end) as `A-`,
          count(CASE WHEN r.`rgolda`='B-' then 1 else null end) as `B-`,
          count(CASE WHEN r.`rgolda`='O-' then 1 else null end) as `O-`,
          count(CASE WHEN r.`rgolda`='AB-' then 1 else null end) as `AB-`,
          count(r.`rid`) as `jml`
          FROM `release` r INNER JOIN `produk` p on p.`Nama`=r.`rproduk`
          WHERE DATE(r.`rtgl`)>='$tglawal' AND date(r.`rtgl`)<='$hariini' AND r.`rstatus` in ('0','2')
          group by p.`no`, r.`rproduk`, p.`lengkap`";
	//echo "$sql";
	$qraw=mysql_query($sql);
    $jmlap=0;$jmlbp=0;$jmlop=0;$jmlabp=0;
    $jmlan=0;$jmlbn=0;$jmlon=0;$jmlabn=0;
    $jmlrhp=0;$jmlrhn=0;$jmlrow=0;
	while($tmp=mysql_fetch_assoc($qraw)){$no++;
        $jmlap=$jmlap+$tmp['A+'];
        $jmlbp=$jmlbp+$tmp['B+'];
        $jmlop=$jmlop+$tmp['O+'];
        $jmlabp=$jmlabp+$tmp['AB+'];
        $jmlan=$jmlan+$tmp['A-'];
        $jmlbn=$jmlbn+$tmp['B-'];
        $jmlon=$jmlon+$tmp['O-'];
        $jmlabn=$jmlabn+$tmp['AB-'];
        $jmlrhp=$jmlrhp+$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+'];
        $jmlrhn=$jmlrhn+$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];
        $jmlrow=$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+']+$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];
        ?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td align="right"><?=$no.'.'?></td>
			<td align="left" nowrap><?=$tmp['rproduk'].' - '.$tmp['lengkap']?></td>
            <td align="center"><?=$tmp['A+']?></td>
            <td align="center"><?=$tmp['B+']?></td>
            <td align="center"><?=$tmp['O+']?></td>
            <td align="center"><?=$tmp['AB+']?></td>
            <td style="background-color:mistyrose;" align="center"><?=$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+'];?></td>

            <td align="center"><?=$tmp['A-']?></td>
            <td align="center"><?=$tmp['B-']?></td>
            <td align="center"><?=$tmp['O-']?></td>
            <td align="center"><?=$tmp['AB-']?></td>
            <td style="background-color:mistyrose;" align="center"><?=$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];?></td>
            <td style="background-color:mistyrose; font-weight: bold" align="center"><?=$jmlrow?></td>

		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=31 align="center">TIDAK ADA DATA RELEASE PRODUK</td>
	<?}?>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th colspan="2"> TOTAL </th>
            <th> <?=$jmlap?> </th>
            <th> <?=$jmlbp?> </th>
            <th> <?=$jmlop?> </th>
            <th> <?=$jmlabp?> </th>
            <th> <?=$jmlrhp?> </th>

            <th> <?=$jmlan?> </th>
            <th> <?=$jmlbn?> </th>
            <th> <?=$jmlon?> </th>
            <th> <?=$jmlabn?> </th>
            <th> <?=$jmlrhn?> </th>

            <th> <?=$jmlrhp+$jmlrhn?> </th>
        </tr>
        </table>

    <br><font size="4" color=00008B>Produk tidak diluluskan</b></font>
    <table border=1 cellpadding=4  style="border-collapse:collapse">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th rowspan="3" width="25px">NO.</th>
            <th rowspan="3" width="300px">JENIS PRODUK</th>
            <th colspan="10">GOLONGAN DARAH</th>
            <th rowspan="3">TOTAL</th>
        </tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th colspan="5">RHESUS POSITIF</th>
            <th colspan="5">RHESUS NEGATIF</th>
        </tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th width="60px">A</th>
            <th width="60px">B</th>
            <th width="60px">O</th>
            <th width="60px">AB</th>
            <th width="60px">JML</th>
            <th width="60px">A</th>
            <th width="60px">B</th>
            <th width="60px">O</th>
            <th width="60px">AB</th>
            <th width="60px">JML</th>
        </tr>

        <?php
        $no=0;
        $sql="SELECT p.`no`, r.`rproduk`, p.`lengkap`,
          count(CASE WHEN r.`rgolda`='A+' then 1 else null end) as `A+`,
          count(CASE WHEN r.`rgolda`='B+' then 1 else null end) as `B+`,
          count(CASE WHEN r.`rgolda`='O+' then 1 else null end) as `O+`,
          count(CASE WHEN r.`rgolda`='AB+' then 1 else null end) as `AB+`,
          count(CASE WHEN r.`rgolda`='A-' then 1 else null end) as `A-`,
          count(CASE WHEN r.`rgolda`='B-' then 1 else null end) as `B-`,
          count(CASE WHEN r.`rgolda`='O-' then 1 else null end) as `O-`,
          count(CASE WHEN r.`rgolda`='AB-' then 1 else null end) as `AB-`,
          count(r.`rid`) as `jml`
          FROM `release` r INNER JOIN `produk` p on p.`Nama`=r.`rproduk`
          WHERE DATE(r.`rtgl`)>='$tglawal' AND date(r.`rtgl`)<='$hariini' AND r.`rstatus` ='1'
          group by p.`no`, r.`rproduk`, p.`lengkap`";
        //echo "$sql";
        $qraw=mysql_query($sql);
        $jmlap=0;$jmlbp=0;$jmlop=0;$jmlabp=0;
        $jmlan=0;$jmlbn=0;$jmlon=0;$jmlabn=0;
        $jmlrhp=0;$jmlrhn=0;$jmlrow=0;
        while($tmp=mysql_fetch_assoc($qraw)){$no++;
            $jmlap=$jmlap+$tmp['A+'];
            $jmlbp=$jmlbp+$tmp['B+'];
            $jmlop=$jmlop+$tmp['O+'];
            $jmlabp=$jmlabp+$tmp['AB+'];
            $jmlan=$jmlan+$tmp['A-'];
            $jmlbn=$jmlbn+$tmp['B-'];
            $jmlon=$jmlon+$tmp['O-'];
            $jmlabn=$jmlabn+$tmp['AB-'];
            $jmlrhp=$jmlrhp+$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+'];
            $jmlrhn=$jmlrhn+$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];
            $jmlrow=$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+']+$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];
            ?>
            <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
                <td align="right"><?=$no.'.'?></td>
                <td align="left" nowrap><?=$tmp['rproduk'].' - '.$tmp['lengkap']?></td>
                <td align="center"><?=$tmp['A+']?></td>
                <td align="center"><?=$tmp['B+']?></td>
                <td align="center"><?=$tmp['O+']?></td>
                <td align="center"><?=$tmp['AB+']?></td>
                <td style="background-color:mistyrose;" align="center"><?=$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+'];?></td>

                <td align="center"><?=$tmp['A-']?></td>
                <td align="center"><?=$tmp['B-']?></td>
                <td align="center"><?=$tmp['O-']?></td>
                <td align="center"><?=$tmp['AB-']?></td>
                <td style="background-color:mistyrose;" align="center"><?=$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];?></td>
                <td style="background-color:mistyrose; font-weight: bold" align="center"><?=$jmlrow?></td>

            </tr>
        <?}
        if ($no==0){?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td colspan=31 align="center">TIDAK ADA DATA PRODUK YANG TIDAK LULUS</td>
            <?}?>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th colspan="2"> TOTAL </th>
            <th> <?=$jmlap?> </th>
            <th> <?=$jmlbp?> </th>
            <th> <?=$jmlop?> </th>
            <th> <?=$jmlabp?> </th>
            <th> <?=$jmlrhp?> </th>

            <th> <?=$jmlan?> </th>
            <th> <?=$jmlbn?> </th>
            <th> <?=$jmlon?> </th>
            <th> <?=$jmlabn?> </th>
            <th> <?=$jmlrhn?> </th>

            <th> <?=$jmlrhp+$jmlrhn?> </th>
        </tr>
    </table>
	
	<br><font size="4" color=00008B>Petugas yang melakukan Produk Release</b></font>
	<table border=1 cellpadding=4  style="border-collapse:collapse">
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
			<th rowspan="3" width="25px">NO.</th>
            <th rowspan="3" width="300px">NAMA PETUGAS</th>
            <th colspan="10">GOLONGAN DARAH</th>
			<th rowspan="3">TOTAL</th>
		</tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th colspan="5">RHESUS POSITIF</th>
            <th colspan="5">RHESUS NEGATIF</th>
        </tr>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th width="60px">A</th>
            <th width="60px">B</th>
            <th width="60px">O</th>
            <th width="60px">AB</th>
            <th width="60px">JML</th>
            <th width="60px">A</th>
            <th width="60px">B</th>
            <th width="60px">O</th>
            <th width="60px">AB</th>
            <th width="60px">JML</th>
        </tr>    
	<?php
	$no=0;
	$sql="SELECT r.`ruser`, u.`nama_lengkap`,
		count(CASE WHEN r.`rgolda`='A+' then 1 else null end) as `A+`, 
		count(CASE WHEN r.`rgolda`='B+' then 1 else null end) as `B+`, 
		count(CASE WHEN r.`rgolda`='O+' then 1 else null end) as `O+`, 
		count(CASE WHEN r.`rgolda`='AB+' then 1 else null end) as `AB+`, 
		count(CASE WHEN r.`rgolda`='A-' then 1 else null end) as `A-`, 
		count(CASE WHEN r.`rgolda`='B-' then 1 else null end) as `B-`, 
		count(CASE WHEN r.`rgolda`='O-' then 1 else null end) as `O-`, 
		count(CASE WHEN r.`rgolda`='AB-' then 1 else null end) as `AB-`, 
		count(r.`rid`) as `jml` 
		FROM `release` r INNER JOIN `produk` p on p.`Nama`=r.`rproduk`  inner join `user` u on r.`ruser`=u.`id_user`
		WHERE DATE(r.`rtgl`)>='$tglawal' AND date(r.`rtgl`)<='$hariini'
		group by r.`ruser`, u.`nama_lengkap`";
	$qraw=mysql_query($sql);
    $jmlap=0;$jmlbp=0;$jmlop=0;$jmlabp=0;
    $jmlan=0;$jmlbn=0;$jmlon=0;$jmlabn=0;
    $jmlrhp=0;$jmlrhn=0;$jmlrow=0;
	while($tmp=mysql_fetch_assoc($qraw)){$no++;
        $jmlap=$jmlap+$tmp['A+'];
        $jmlbp=$jmlbp+$tmp['B+'];
        $jmlop=$jmlop+$tmp['O+'];
        $jmlabp=$jmlabp+$tmp['AB+'];
        $jmlan=$jmlan+$tmp['A-'];
        $jmlbn=$jmlbn+$tmp['B-'];
        $jmlon=$jmlon+$tmp['O-'];
        $jmlabn=$jmlabn+$tmp['AB-'];
        $jmlrhp=$jmlrhp+$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+'];
        $jmlrhn=$jmlrhn+$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];
        $jmlrow=$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+']+$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];
        ?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td align="right"><?=$no.'.'?></td>
			<td align="left" nowrap><?=$tmp['ruser'].' - '.$tmp['nama_lengkap']?></td>
            <td align="center"><?=$tmp['A+']?></td>
            <td align="center"><?=$tmp['B+']?></td>
            <td align="center"><?=$tmp['O+']?></td>
            <td align="center"><?=$tmp['AB+']?></td>
            <td style="background-color:mistyrose;" align="center"><?=$tmp['A+']+$tmp['B+']+$tmp['O+']+$tmp['AB+'];?></td>

            <td align="center"><?=$tmp['A-']?></td>
            <td align="center"><?=$tmp['B-']?></td>
            <td align="center"><?=$tmp['O-']?></td>
            <td align="center"><?=$tmp['AB-']?></td>
            <td style="background-color:mistyrose;" align="center"><?=$tmp['A-']+$tmp['B-']+$tmp['O-']+$tmp['AB-'];?></td>
            <td style="background-color:mistyrose; font-weight: bold" align="center"><?=$jmlrow?></td>

		</tr>
	<?}
	if ($no==0){?>
        <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
			<td colspan=31 align="center">TIDAK ADA DATA RELEASE PRODUK</td>
	<?}?>
        <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
            <th colspan="2"> TOTAL </th>
            <th> <?=$jmlap?> </th>
            <th> <?=$jmlbp?> </th>
            <th> <?=$jmlop?> </th>
            <th> <?=$jmlabp?> </th>
            <th> <?=$jmlrhp?> </th>

            <th> <?=$jmlan?> </th>
            <th> <?=$jmlbn?> </th>
            <th> <?=$jmlon?> </th>
            <th> <?=$jmlabn?> </th>
            <th> <?=$jmlrhn?> </th>

            <th> <?=$jmlrhp+$jmlrhn?> </th>
        </tr>
        </table>


    <div style="font-size: 10px;color: #000000">Update 2018-05-28</div>
</body>
</html>

