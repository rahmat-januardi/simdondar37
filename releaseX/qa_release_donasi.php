<?php

$query_combo = "select * from tempudd where modul='PROLIS'";
$hasil_combo = mysql_query($query_combo);
$data_combo = mysql_fetch_assoc($hasil_combo);
$nkt=$_GET['nokantong'];
$mode_kembali=$_GET['mode'];
$kantongke =strtoupper(substr($nkt, -1));
$no_kantonga=substr_replace($nkt,'A',-1,1);

switch($kantongke){
    case 'A':$posisikantong='Kantong Utama';break;
    case 'B':$posisikantong='Kantong Satelite 1';break;
    case 'C':$posisikantong='Kantong Satelite 2';break;
    case 'D':$posisikantong='Kantong Satelite 3';break;
    case 'E':$posisikantong='Kantong Satelite 4';break;
    case 'F':$posisikantong='Kantong Satelite 5';break;
    case 'G':$posisikantong='Kantong Satelite 6';break;
    case 'H':$posisikantong='Kantong Satelite 7';break;
    default:$posisikantong="";
}
$stokkantong=mysql_query("select * from stokkantong where nokantong='$nkt' and Status='2' and sah='1'");
$datakantong=mysql_fetch_assoc($stokkantong);
switch($datakantong['jenis']){
    case '1': $jeniskantong='Single';break;
    case '2': $jeniskantong='Double';break;
    case '3': $jeniskantong='Triple';break;
    case '4': $jeniskantong='Quadruple';break;
    case '5': $jeniskantong='Quadruple T&B';break;
    case '6': $jeniskantong='Pediatrik';break;
    default:  $jeniskantong='';
}
$statuskantong='';
$status_ktg=$datakantong['Status'];
switch ($status_ktg){
    case '0' :  $statuskantong='Kosong';
        if ($datakantong[StatTempat]==NULL) $statuskantong='Kosong - di Logistik';
        if ($datakantong[StatTempat]=='0')  $statuskantong='Kosong - di Logistik';
        if ($datakantong[StatTempat]=='1')  $statuskantong='Kosong - di Aftap';
        break;
    case '1' :  if ($datakantong['sah']=="1"){
        $statuskantong='Karantina';
    } else{
        $statuskantong='Belum disahkan';
    }
        break;
    case '2' :  $statuskantong='Sehat';
        if (substr($datakantong[stat2],0,1)=='b') $tempat=" (BDRS)";
        break;
    case '3' : $statuskantong='Keluar';break;
    case '4' : $statuskantong='Rusak';break;
    case '5' : $statuskantong='Rusak-Gagal';break;
    case '6' : $statuskantong='Dimusnahkan';break;
    default  : $statuskantong='-';
}
$merk           =$datakantong['merk'];
$tglinputlogistik=$datakantong['tglTerima'];
$volumeasal     =$datakantong['volumeasal'];
$tglmutasi      =$datakantong['tglmutasi'];
$lotkantong     =$datakantong['nolot_ktg'];
$asalk		=$datakantong['kantongAsal'];
$sttgaf         =$datakantong['tgl_Aftap'];
$stgol          =$datakantong['gol_darah'];
$strh           =$datakantong['RhesusDrh'];
$edkantong      =$datakantong['kadaluwarsa_ktg'];
$jeniskomponen  =$datakantong['produk'];
$absfix		=$datakantong['abs'];
$tglpengolahankomponen=$datakantong['tglpengolahan'];
$tgledkomponen=$datakantong['kadaluwarsa'];
$var_ed_kantong='0';
if ($kantongke=="A"){
	$lamaaftap=$datakantong['lama_pengambilan'];
} else {
	
	$st_k=mysql_query("select * from stokkantong where nokantong='$no_kantonga'");
	$dt_k=mysql_fetch_assoc($st_k);
	$lamaaftap=$dt_k['lama_pengambilan'];
}

if ($edkantong<$hariini){$var_ed_kantong='1';}
    
//TRANSAKSI DONOR
    $donasi=mysql_fetch_assoc(mysql_query("select * from htransaksi where nokantong ='$no_kantonga'"));
    $notrans_aftap      =$donasi['NoTrans'];
    $asaldonor          =substr($notrans_aftap,0,1);
        if($asaldonor=='M'){$asaldonor="Mobile Unit";}else{$asaldonor="Dalam Gedung";}
    $tglaftap           =$donasi['Tgl'];
    $kodependonor       =$donasi['KodePendonor'];
    if($donasi['JenisDonor']=='0'){$jenisdonor='Donor Sukarela';}else{$jenisdonor='Donor Pengganti';};
    if($donasi['donorbaru']=='0'){$donorbaru='Donor Baru';}else{$donorbaru='Donor Ulang';};
    if($donasi['jk']=='0'){$jeniskelamin='Laki-laki';}else{$jeniskelamin='Perempuan';};
    $umurdonor          =$donasi['umur'];
    $donasidonor        =$donasi['donorke'];
    $pekerjaan          =$donasi['pekerjaan'];
    $golongandarah_donasi=$donasi['gol_darah'];
    $rhesus_donasi      =$donasi['rhesus'];
    $ptghb              =$donasi['petugasHB'];
    $ptgaftap           =$donasi['petugas'];
    $ptgtensi           =$donasi['petugasTensi'];
    $ptgadmin           =$donasi['user'];
    $tensi              =$donasi['tensi'];
    $hb                 =$donasi['Hb'];
    $nadi               =$donasi['nadi'];
    $suhu               =$donasi['suhu'];
    $bb                 =$donasi['beratBadan'];
    $volumeambil        =$donasi['Diambil'];
    $kodedokter         =$donasi['NamaDokter'];
    if ($donasi[jumHB]=='1') $hb1='Tenggelam';
    if ($donasi[jumHB]=='2') $hb1='Melayang';
    if ($donasi[jumHB]=='3') $hb1='Mengapung';
//

$masterkantong0="select * from master_kantong where merk='$merk' and jenis='$datakantong[jenis]' and vol='$volumeambil'";
//echo "select * from master_kantong where merk='$merk' and jenis='$datakantong[jenis]'";
$masterkantong1=mysql_fetch_assoc(mysql_query($masterkantong0));
$lamabukakantong=$masterkantong1['lama_buka'];
$vol_jenisktg=$masterkantong1['vol'];
$antikoagulant=$masterkantong1['antikoagulant'];
switch($kantongke){
    case 'A' : $beratkantongkosong=$masterkantong1['berat_ku'];break;
    case 'B' : $beratkantongkosong=$masterkantong1['berat_s1'];break;
    case 'C' : $beratkantongkosong=$masterkantong1['berat_s2'];break;
    case 'D' : $beratkantongkosong=$masterkantong1['berat_s3'];break;
    case 'E' : $beratkantongkosong=$masterkantong1['berat_s4'];break;
    case 'F' : $beratkantongkosong=$masterkantong1['berat_s5'];break;
    case 'G' : $beratkantongkosong=$masterkantong1['berat_s6'];break;
    case 'H' : $beratkantongkosong=$masterkantong1['berat_s7'];break;
    default  : $beratkantongkosong='0';
}
$produk=mysql_fetch_assoc(mysql_query("select * from produk where Nama='$jeniskomponen'"));
$beratjenis=$produk['beratjenis'];
$namakomponen=$produk['lengkap'];

if ($vol_jenisktg=="450"){
	$vol_min=round($produk['vol_min450'],0);
	$vol_max=round($produk['vol_max450'],0);
} else {
	$vol_min=round($produk['vol_min'],0);
	$vol_max=round($produk['vol_max'],0);
}

$timbang=mysql_fetch_assoc(mysql_query("SELECT t.`id`, t.`waktu`, t.`user`, u.`nama_lengkap`, t.`bagian`, t.`nokantong`, t.`berat_ukur`,
            case when t.`konfirm`='0' then 'Belum' else 'Sudah' end as konfirm,
            t.`waktu_konfirm`, t. `notrans`,  s.status, s.sah, s.StatTempat, s.stat2
            FROM `timbang_darah` t left join `user` u on t.`user`=u.`id_user`
            left join stokkantong s on t.nokantong=s.noKantong
		    WHERE   t.`nokantong`='$nkt' order by `id` DESC"));
$berattimbang       =$timbang['berat_ukur'];
$tgltimbang         =$timbang['waktu'];
$usertimbang        =$timbang['user'];
$namapetugastimbang = $timbang['nama_lengkap'];
if ($jeniskomponen=="WB"){
	$volumekomponendarah=(($berattimbang-$beratkantongkosong)/$beratjenis)-$antikoagulant;
} else {
	$volumekomponendarah=($berattimbang-$beratkantongkosong)/$beratjenis;
}
$volumekomponendarah=round($volumekomponendarah,3);
if ($volumekomponendarah<0){$volumekomponendarah="0";}
$var_volume_kantong='0';
if ($volumekomponendarah < $vol_min){$var_volume_kantong='1';}
if ($volumekomponendarah > $vol_max){$var_volume_kantong='1';}

?>
<table cellpadding="3" cellspacing="3" width="100%" border="0px">
    <tr>
        <td valign="top">
            <table width="100%" cellpadding="5" cellspacing="5">
                <tr><td style="background-color: mistyrose">Merk kantong</td>           <td><?=$merk?></td></tr>
                <tr><td style="background-color: mistyrose">Standar lama kantong terbuka</td><td><?=$lamabukakantong?> Hari</td></tr>
                <tr><td style="background-color: mistyrose">Volume kantong</td>         <td><?=$volumeasal?> ml</td></tr>
                <tr><td style="background-color: mistyrose">Jenis Kantong</td>          <td><?=$jeniskantong?></td></tr>
                <tr><td style="background-color: mistyrose">Nomor LOT Kantong</td>      <td><?=$lotkantong?></td></tr>
                <tr><td style="background-color: mistyrose">Tgl. ED Kantong</td><td><?=$edkantong?></td></tr>
                <tr><td style="background-color: mistyrose">Tanggal input logistik</td> <td><?=$tglinputlogistik?></td></tr>
                <tr><td style="background-color: mistyrose">Tanggal mutasi ke aftap</td><td><?=$tglmutasi?></td></tr>
            </table>
        </td>
        <td valign="top">
        <table width="100%" cellpadding="3" cellspacing="5">
            <?
            $qpdokter=mysql_fetch_assoc(mysql_query("select Nama from dokter_periksa where kode='$kodedokter'"));
            $qptensi=mysql_fetch_assoc(mysql_query("select nama_lengkap from `user` where `id_user`='$ptgtensi'"));
            $qpaftap=mysql_fetch_assoc(mysql_query("select nama_lengkap from `user` where `id_user`='$ptgaftap'"));
            $qphb=mysql_fetch_assoc(mysql_query("select nama_lengkap from `user` where `id_user`='$ptghb'"));
            $qpinput=mysql_fetch_assoc(mysql_query("select nama_lengkap from `user` where `id_user`='$ptgadmin'"));
            	
		//Cari Asal Kantong
		if ($asalk ==NULL){
	    ?>
            <tr><td style="background-color: mistyrose">Tempat Pengambilan</td> <td><?=$asaldonor?></td></tr>
            <tr><td style="background-color: mistyrose">Kode Pendonor</td>      <td><?=$kodependonor?></td></tr>
	    <tr><td style="background-color: mistyrose">Jenis Kelamin</td>      <td><?=$jeniskelamin?></td></tr>
	    <tr><td style="background-color: mistyrose">Donor Ke</td>      <td><?=$donasidonor?> kali</td></tr>
            <tr><td style="background-color: mistyrose">Tgl Pengambilan</td>    <td><?=$datakantong['tgl_Aftap']?></td><input type="hidden" name="tgl_aftap" value="<?=$datakantong['tgl_Aftap']?>"> </tr>
	    <tr><td style="background-color: mistyrose">Lama Pengambilan</td>   <td><?=$lamaaftap?> Menit</td></tr>				
            <tr><td style="background-color: mistyrose">Volume pengambilan</td> <td><?=$volumeambil.' ml.'?></td></tr>
            <tr><td style="background-color: mistyrose">Golongan Darah</td>     <td><?=$datakantong['gol_darah']?>(<?=$datakantong['RhesusDrh']?>)</td></tr>
            <tr><td style="background-color: mistyrose">HB & BB</td>            <td><?='Hb: '.$hb.' g/dl,  BB: '.$bb.' kg'?></td></tr>
            <tr><td style="background-color: mistyrose">Tensi, Nadi & Suhu</td> <td><?='Tensi: '.$tensi.' mmHg, Nadi: '.$nadi.'/mnt, Suhu: '.$suhu.'<sup>O</sup>C'?></td></tr>
            <tr><td style="background-color: mistyrose">Petugas Tensi</td>      <td><?=$ptgtensi.' - '.$qptensi['nama_lengkap']?></td></tr>
            <tr><td style="background-color: mistyrose">Dokter</td>             <td><?=$qpdokter['Nama']?></td></tr>
            <tr><td style="background-color: mistyrose">Petugas HB</td>         <td><?=$ptghb.' - '.$qphb['nama_lengkap']?></td></tr>
            <tr><td style="background-color: mistyrose">Petugas Aftap</td>      <td><?=$ptgaftap.' - '.$qpaftap['nama_lengkap']?></td></tr>
            <tr><td style="background-color: mistyrose">Petugas Input data</td> <td><?=$ptgadmin.' - '.$qpinput['nama_lengkap']?></td></tr>
		<?} else {
		$donasi=mysql_fetch_assoc(mysql_query("select * from htransaksi where nokantong ='$asalk'"));
		$notrans_aftap      =$donasi['NoTrans'];
		$asaldonor          =substr($notrans_aftap,0,1);
		    if($asaldonor=='M'){$asaldonor="Mobile Unit";}else{$asaldonor="Dalam Gedung";}
		$tglaftap           =$donasi['Tgl'];
		$kodependonor       =$donasi['KodePendonor'];
		if($donasi['JenisDonor']=='0'){$jenisdonor='Donor Sukarela';}else{$jenisdonor='Donor Pengganti';};
		if($donasi['donorbaru']=='0'){$donorbaru='Donor Baru';}else{$donorbaru='Donor Ulang';};
		if($donasi['jk']=='0'){$jeniskelamin='Laki-laki';}else{$jeniskelamin='Perempuan';};
		$umurdonor          =$donasi['umur'];
		$donasidonor        =$donasi['donorke'];
		$pekerjaan          =$donasi['pekerjaan'];
		$golongandarah_donasi=$donasi['gol_darah'];
		$rhesus_donasi      =$donasi['rhesus'];
		$ptghb              =$donasi['petugasHB'];
		$ptgaftap           =$donasi['petugas'];
		$ptgtensi           =$donasi['petugasTensi'];
		$ptgadmin           =$donasi['user'];
		$tensi              =$donasi['tensi'];
		$hb                 =$donasi['Hb'];
		$nadi               =$donasi['nadi'];
		$suhu               =$donasi['suhu'];
		$bb                 =$donasi['beratBadan'];
		$volumeambil        =$donasi['Diambil'];
		$kodedokter         =$donasi['NamaDokter'];
		if ($donasi[jumHB]=='1') $hb1='Tenggelam';
		if ($donasi[jumHB]=='2') $hb1='Melayang';
		if ($donasi[jumHB]=='3') $hb1='Mengapung';		
		?>
	    <tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="2" class=input align="center">DATA PENGAMBILAN KANTONG ASAL <br>( <?=$asalk?> )</td>
	    
	    <tr><td style="background-color: mistyrose">Tempat Pengambilan</td> <td><?=$asaldonor?></td></tr>
            <tr><td style="background-color: mistyrose">Kode Pendonor</td>      <td><?=$kodependonor?></td></tr>
	    <tr><td style="background-color: mistyrose">Jenis Kelamin</td>      <td><?=$jeniskelamin?></td></tr>
	    <tr><td style="background-color: mistyrose">Donor Ke</td>      <td><?=$donasidonor?> kali</td></tr>
            <tr><td style="background-color: mistyrose">Tgl Pengambilan</td>    <td><?=$datakantong['tgl_Aftap']?></td><input type="hidden" name="tgl_aftap" value="<?=$datakantong['tgl_Aftap']?>"> </tr>
	    				
            <tr><td style="background-color: mistyrose">Volume pengambilan</td> <td><?=$volumeambil.' ml.'?></td></tr>
            <tr><td style="background-color: mistyrose">Golongan Darah</td>     <td><?=$datakantong['gol_darah']?>(<?=$datakantong['RhesusDrh']?>)</td></tr>
            <tr><td style="background-color: mistyrose">HB & BB</td>            <td><?='Hb: '.$hb.' g/dl,  BB: '.$bb.' kg'?></td></tr>
            <tr><td style="background-color: mistyrose">Tensi, Nadi & Suhu</td> <td><?='Tensi: '.$tensi.' mmHg, Nadi: '.$nadi.'/mnt, Suhu: '.$suhu.'<sup>O</sup>C'?></td></tr>
            <tr><td style="background-color: mistyrose">Petugas Tensi</td>      <td><?=$ptgtensi.' - '.$qptensi['nama_lengkap']?></td></tr>
            <tr><td style="background-color: mistyrose">Dokter</td>             <td><?=$qpdokter['Nama']?></td></tr>
            <tr><td style="background-color: mistyrose">Petugas HB</td>         <td><?=$ptghb.' - '.$qphb['nama_lengkap']?></td></tr>
            <tr><td style="background-color: mistyrose">Petugas Aftap</td>      <td><?=$ptgaftap.' - '.$qpaftap['nama_lengkap']?></td></tr>
            <tr><td style="background-color: mistyrose">Petugas Input data</td> <td><?=$ptgadmin.' - '.$qpinput['nama_lengkap']?></td></tr>
<?}?>
        </table>
        </td>
    </tr>
</table>
<input type="hidden" name="tgl_komponen" value="<?=$tglpengolahankomponen?>">
<input type="hidden" name="tgl_ed_produk" value="<?=$tgledkomponen?>">
<input type="hidden" name="golda" value="<?=$datakantong['gol_darah'].$datakantong['RhesusDrh']?>">
