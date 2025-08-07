<html>
<?
    /***********************************************
     * Author 	: suwena 
     * Date 	: 11 Sep 2018
     * Lembar Kerja Pelulusan Produk Darah
     * UTD BALI-RLS-L3-007
     * Versi 001
     ***********************************************/
    $nodokumen="UTD BALI-RLS-L3-007";
    include('config/db_connect.php');
    $today			=date("Y-m-d H:i:s");
    $namauser		=$_SESSION[namauser];
    $namauserlkp	=$_SESSION[nama_lengkap];
    $notransaksi    =$_GET['no'];
    $utd            =mysql_fetch_assoc(mysql_query("select upper(`nama`) as `nama` from `utd` where `aktif`='1'"));
    $utd            =$utd['nama'];
    //echo "$notransaksi";
?>

<title>SIMDONDAR</title>
<head>
<style type="text/css" media="print">
    @page
    {
        size: landscape;
        margin-bottom: 20mm;
        margin-left: 0mm;
        margin-right: 0mm;
        margin-top: 15mm;
        header : {display: none !important;}
    }
    html
    {
        background-color: #ffffff;
        margin: 3px;  /* this affects the margin on the html before sending to printer */
    }
    body
    {
        border: solid 0px #ffffff ;
        margin: 0mm 10mm 10mm 10mm; /* margin you want for the content */
    }
    table th td {text-align: left;}
</style>
</head>
<body onload="window.print()">

<?php
$v_tgl1    	=$_GET[tgl1];
$v_tgl2    	=$_GET[tgl2];
$v_ptgs		=$_GET[ptgs];
$v_stts		=$_GET[stts];
?>
<table class="list" border="0" cellpadding="2" cellspacing="2" width="100%" style="border-collapse:collapse">
    <tr style="font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;font-size: 10px;">
        <td style="text-align: left"><? echo $utd;?></td>
        <td style="text-align: right"><? echo $nodokumen;?></td>
    </tr>
    <tr style="font-family: 'trebuchet ms', Impact, Arial, Helvetica, sans-serif;font-size: 11px;">
        <td style="text-align: left">Lembar Kerja Pelulusan Produk Darah</td>
        <td style="text-align: right">Versi : 001</td>
    </tr>
</table>
<hr>
<font size="4">Lembar Kerja Pelulusan Produk Darah</font><br><br>
<table class="list" border=1 cellpadding="2" cellspacing="2" style="border-collapse:collapse" width="100%">
    <thead>
    <tr style="font-size:10px;">
			<th rowspan="2">NO.</th>
            <th rowspan="2">NO. RELEASE</th>
            <th rowspan="2">TGL. RELEASE</th>
			<th rowspan="2">NO. KANTONG</th>
            <th rowspan="2">JENIS PRODUK</th>
			<th rowspan="2">GOL. DARAH</th>
            <th colspan="5">SPESIFIKASI KANTONG & IDENTITAS</th>
            <th colspan="7">VISUAL</th>
            <th colspan="2">SELEKSI & AFTAP</th>
            <th rowspan="2">PENGO-<br>LAHAN</th>
            <th colspan="2">VOLUME PRODUK</th>
            <th colspan="2">PEMERIKSAAN LAB</th>
            <th rowspan="2">Hasil</th>
		</tr>
        <tr style="font-size:10px;">
            <th>Label & Idtts</th>
            <th>Kode Unik</th>
            <th>Tgl. Aftap</th>
            <th>Tgl. Produksi</th>
            <th>Tgl. ED</th>

            <th>Kebo<br>coran</th>
            <th>Selang</th>
            <th>Hemo<br>lysis</th>
            <th>Lipe<br>mik</th>
            <th>Ikte<br>rik</th>
            <th>Kehi<br>jauan</th>
            <th>Bekuan</th>

            <th>Seleksi</th>
            <th>Lama<br>Aftap</th>

            <th>Volume</th>
            <th>Sesuai?</th>
            <th>IMLTD & KGD</th>
            <th>History<br>Donor</th>
        </tr>
    </thead>
	<?php
	$no=0;
	$sql="SELECT `rid`, `shift`, `rnotrans`, `rnokantong`, `rberat_timbang`, `rkode_timbangan`, `rvolume`, `rspek_volume`,
          `rproduk`, `rgolda`,
          DATE_FORMAT(`rtgl`, '%d-%m-%Y %H:%i') as `rtgl`,
          DATE_FORMAT(`rtgl_aftap`, '%d-%m-%Y') as `rtgl_aftap`,
          DATE_FORMAT(`rtgl_olah`, '%d-%m-%Y') as `rtgl_olah`, `rtgl_ed`, `rspek_kantong`, `rselang`, `rkebocoran`, `rkode_unik`,
          `rhemolysis`, `rlipemik`, `rikterik`, `rkehijauan`, `rbekuan`, `rspek_seleksi`, `rspek_aftap`, `rspek_pengolahan`,
          `rspek_imltd`, `rjenis_imltd`, `rspek_imltd_old`, `rspek_kgd`, `rspek_kgd_old`, `rstatus`, `rsatus_ket`, `rnote`,
          `ruser`, `rchecker`, `rpengesah`, `on_insert`, `on_update`, `up_data` FROM `release`
		  WHERE DATE(rtgl)>='$v_tgl1' AND date(rtgl)<='$v_tgl2' and rstatus like '%$v_stts%' and `ruser` like '%$v_ptgs%' order by rnotrans asc";
	//echo "$sql";
	$qraw=mysql_query($sql);
    $statusrelease='';
	while($tmp=mysql_fetch_assoc($qraw)){$no++;
        switch ($tmp['status']){
            case '0' : $statusrelease='Lulus';break;
            case '1' : $statusrelease='Tidak Lulus';break;
            case '2' : $statusrelease='Lulus dengan Catatan';break;
            default  : $statusrelease='-';
        }
        $pemeriksa  = $tmp['ruser'];
        $checker    = $tmp['rchecker'];
        $pengesah   = $tmp['rpengesah'];
        ?>
        <tr style="font-size:10px; color:#000000; font-family:Verdana;">
            <td align="right"><?=$no.'.'?></td>
            <td align="left"><?=$tmp['rnotrans']?></td>
            <td align="left" nowrap><?=$tmp['rtgl']?></td>
            <td align="center" nowrap><?=$tmp['rnokantong']?></td>
            <td align="left" nowrap><?=$tmp['rproduk']?></td>
            <td align="center"><?=$tmp['rgolda']?></td>
            <?if ($tmp['rspek_kantong']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rkode_unik']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <td align="left" nowrap><?=$tmp['rtgl_aftap']?></td>
            <td align="left" nowrap><?=$tmp['rtgl_olah']?></td>
            <td align="left"\><?=$tmp['rtgl_ed']?></td>
            <?if ($tmp['rkebocoran']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rselang']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rhemolysis']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rlipemik']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rikterik']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rkehijauan']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rbekuan']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_seleksi']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_aftap']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_pengolahan']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>

            <td align="right" nowrap><?=number_format(round($tmp['rvolume'],2))?> ml</td>
            <?if ($tmp['rspek_volume']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_imltd']=='1'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <?if ($tmp['rspek_imltd_old']=='0'){?><td align="center">&radic;</td><?}else{?><td align="center" bgcolor="red"><font color="white">X</font></td><?}?>
            <td align="left" nowrap><?=$tmp['rsatus_ket']?></td>
        </tr>
    <?}
	if ($no==0){?>
    <tr style="font-size:14px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
        <td colspan=31 align="center">Tidak ada data release produk dari tanggal, petugas dan status yang dipilih</td>
    </tr>
    <?}?>
</table>
<br>
    <br>

<?php
$usr        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$pemeriksa'"));
$usr_pemeriksa   = $usr['nama_lengkap'];
$usr        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$checker'"));
$usr_check   = $usr['nama_lengkap'];
$usr        = mysql_fetch_assoc(mysql_query("select `nama_lengkap` from `user` where `id_user`='$pengesah'"));
$usr_sah   = $usr['nama_lengkap'];

?>
<table class="list" border=1 cellpadding="2" cellspacing="2" style="border-collapse:collapse" width="70%">
    <tr style="font-size:10px;">
            <td align="center" nowrap></td>
            <td align="center" nowrap>Nama</td>
            <td align="center" nowrap>Jabatan</td>
            <td align="center" nowrap>Tanda Tangan</td>
            <td align="center" nowrap>Tanggal</td>
            <td align="center" nowrap>Jam</td>
        </tr>
    <tr style="font-size:10px;">
            <td align="left" nowrap>Dicatat/diperiksa oleh</td>
            <td align="left" nowrap align="center" nowrap> <?php echo "$pemeriksa<br>$usr_pemeriksa";?></td>
            <td align="center" nowrap></td>
            <td align="center" nowrap></td>
            <td align="center" nowrap></td>
            <td align="center" nowrap></td>
        </tr>
    <tr style="font-size:10px;">
        <td align="left" nowrap>Dicek oleh</td>
        <td align="left" nowrap> <?php echo "$checker<br>$usr_check";?></td>
        <td align="center" nowrap></td>
        <td align="center" nowrap></td>
        <td align="center" nowrap></td>
        <td align="center" nowrap></td>
    </tr>
    <tr style="font-size:10px;">
        <td align="left" nowrap>Disahkan oleh</td>
        <td align="left" nowrap> <?php echo "$pengesah<br>$usr_sah";?></td>
        <td align="center" nowrap></td>
        <td align="center" nowrap></td>
        <td align="center" nowrap></td>
        <td align="center" nowrap></td>
    </tr>
    </table>
<? echo "<meta http-equiv='refresh' content='4;url=pmiqa.php?module=rekap_list'";?>

</body>
</html>
