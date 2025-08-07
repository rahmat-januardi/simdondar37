<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<link type="text/css" href="css/calender.css" rel="stylesheet" />
<link type="text/css" href="css/table1.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/tgl_rekap.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>

<script type="text/javascript">
  jQuery(document).ready(function(){
       document.getElementById('minta1').focus();});
</script>
<body OnLoad="document.mintadarah1.minta1.focus();">
<?
include('config/db_connect.php');
$today=date('Y-m-d');
$today1=$today;

if (isset($_POST[submit])) {$nkt=$_POST[minta1];
    $lastnkt=substr($nkt, -1);
   

    if($lastnkt=='A' && $lastnkt=='B' && $lastnkt=='C' && $lastnkt=='D' && $lastnkt=='E' && $lastnkt=='F' && $lastnkt=='G' && $lastnkt=='H'){
        $no_kantong0=substr($nkt,0,-1);
    }else{
        $no_kantong0=$nkt;
    }
        $komponen0=mysql_query("select * from stokkantong where noKantong = '$no_kantong0' order by noKantong ASC");
      $distribusi0=mysql_query("select * from dtransaksipermintaan where NoKantong like '$no_kantong0%' order by NoKantong ASC");
      $donasi0=mysql_query("select * from htransaksi where nokantong ='$nkt'");
    $donasi1=mysql_fetch_assoc(mysql_query("select kantongAsal from stokkantong where noKantong = '$nkt'"));
    $donasi2=mysql_query("select * from htransaksi where nokantong = '$donasi1[kantongAsal]'");

}
$perbln=substr($today,5,2);
$pertgl=substr($today,8,2);
$perthn=substr($today,0,4);
$perbln1=substr($today1,5,2);
$pertgl1=substr($today1,8,2);
$perthn1=substr($today1,0,4);
?>
<STYLE>
  tr { background-color: #fff388}
  .initial { background-color: #fff388; color:#000000 }
  .normal { background-color: #fff388 }
  .biasa { background-color: #fff388 }
  .highlight { background-color: #8888FF }
</style>

<div>
    <form name=mintadarah1 method=post onsubmit="return validasikantong()"><font size="3"> Masukkan Nomor Kantong / Sampel </font>
    <INPUT type="text"  name="minta1"  id="minta1" size='23' placeholder="Nokantong Bebas" >
    <input type=submit name=submit value=Submit>
    </form>
</div>
      <br>


    <?
    $no=1;
  if(mysql_num_rows($komponen0) > 0){
    while ($komponen=mysql_fetch_assoc($komponen0)) {

    ?>
<br><font size="4">DATA KANTONG / SAMPEL</font>
<table class=form border=1 cellpadding=0 cellspacing=0 width="100%">
    <tr tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center" >
        <td rowspan=2>No</td>
            <th rowspan=2>No Kantong</th>
        <th rowspan=2>Asal</th>
        <th rowspan=2>Merk</th>
        <th rowspan=2>Jenis</th>
        <th rowspan=2>Produk</th>
        <th rowspan=2>Vol/CC</th>
        <th rowspan=2>Darah</th>
        <th rowspan=2>Nolot</th>
        <th colspan=2>Status</th>
        <th colspan=8>Tanggal</th>
        <th colspan=3>Pengolahan</th>
        <th rowspan=2>Penge<br>sahan</th>
	<th rowspan=2>NAT</th>
        <th rowspan=2>KGD</th>
        <th rowspan=2>ABS</th>
        <th colspan=2>Release Produk</th>
    </tr>
          <tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center">

        <th> </th>
        <th> </th>
        <th>ED.Ktg</th>
        <th>Aftap</th>
        <th>Durasi</th>
        <th>IMLTD</th>
        <th>Diolah</th>
        <th>ED.Prod</th>
        <th>Keluar</th>
        <th>Musnah</th>
        <th>Alat</th>
        <th>Pemisahan</th>
        <th> </th>
        <th>Tanggal</th>
        <th>Hasil</th>
          </tr>

    <tr class="record" >
        <td><?=$no++?></td>
            <td class=input><?=$komponen[noKantong]?></td>
        <?
        $asalutd=mysql_fetch_assoc(mysql_query("select nama from utd where id='$komponen[AsalUTD]'"));
        $utdintern=mysql_fetch_assoc(mysql_query("select nama from utd where aktif='1'"));
        $bawa=mysql_fetch_assoc(mysql_query("select Status from dtransaksipermintaan where nokantong='$komponen[noKantong]'"));
        $utd=$asalutd[nama];
        if ($komponen[AsalUTD]==NULL) $utd=$utdintern[nama];
        ?>
        <td class=input><?=$utd?></td>
        <td class=input><?=$komponen[merk]?></td>
        <?
        switch ($komponen[Status]) {
        case 0:    $ckt_status="Kosong";
            if($komponen[StatTempat]==NULL) $ckt_status="Kosong Di logistik";
            if($komponen[StatTempat]=='0') $ckt_status="Kosong DI Logistik";
            if($komponen[StatTempat]=='1') $ckt_status="Kosong Di Aftap";
            break;
        case 1:    $ckt_status="Aftap";
            if ($komponen[sah]=='1') $ckt_status="Baru Isi/Karantina";break;
        case 2:    $ckt_status="Sehat";
            if (substr($komponen[stat2],0,1)=='b') $tempat=" (BDRS)";break;
        case 3:    $ckt_status="Keluar_Bawa";
            if ($bawa[Status]=='1') $ckt_status="Keluar_Titip";break;
        case 4:    $ckt_status="Rusak";break;
        case 5:    $ckt_status="Rusak-Gagal";break;
        case 6:    $ckt_status="Dimusnahkan";break;
        case 7: $ckt_status="Reaktif";break;
        case 8: $ckt_status="Darah Flebotomy";break;
        }
	
	switch ($komponen['hasilNAT']) {
        case '0':    $ckt_nat="-";
            break;
        case '1':    $ckt_nat="NR";
            break;
        case '2':    $ckt_nat="R";
            break;
        case '3':    $ckt_nat="Invalid";
            break;
        }

        switch ($komponen['metoda']){
//            case "BS":  $metkantong ="BIASA";        break;
//            case "FT":  $metkantong ="FILTER";       break;
            case "TT":  $metkantong ="TOP & TOP";    break;
            case "TB":  $metkantong ="TOP & BOTTOM"; break;
            case "TBF":  $metkantong ="TOP & BOTTOM (Filter)"; break;
        }
        switch($komponen[jenis]) {
        case '1':$jenis='Single';break;
        case '2':$jenis='Double';break;
        case '3':$jenis='Triple ('.$metkantong.')';break;
        case '4':$jenis='Quadruple ('.$metkantong.')';break;
        case '6':$jenis='Pediatrik';break;
        default:$jenis='';
        }
        switch ($komponen['hasil_release']) {
        case 'o':$release='-';break;
        case '1':$release='LULUS';break;
        case '2':$release='REJECT';break;
        case '3':$release='LULUS dgn CATATAN';break;
        default:$release='';
        }
        ?>
        <td class=input><?=$jenis?></td>
        <td class=input><?=$komponen[produk]?></td>
        <td class=input align="right"><?=$komponen[volume]?></td>
        <td class=input><?=$komponen[gol_darah]?>(<?=$komponen[RhesusDrh]?>)</td>
        <td class=input><?=$komponen[nolot_ktg]?></td>
        <td class=input><?=$ckt_status?></td>
<?
    $bdrs=mysql_fetch_assoc(mysql_query("select nama from bdrs where kode='$komponen[stat2]'"));
    $tujuan=mysql_fetch_assoc(mysql_query("select nama from utd where id='$komponen[stat2]'"));
    $tujuan1=mysql_fetch_assoc(mysql_query("select nama from bdrs where kode='$komponen[stat2]'"));
    $rmhskt=mysql_fetch_assoc(mysql_query("select NamaRs from rmhsakit where Kode='$ttp1[rs]'"));
    //if ($komponen[stat2]==NULL and $komponen[Status]==3) $ckt_tujuan="Rumah Sakit";
    if ($komponen[stat2]==NULL and $komponen[Status]==3) $rs="RS";
    if ($komponen[stat2]==NULL and $komponen[Status]!=3) $rs="";
    $buang=mysql_fetch_assoc(mysql_query("select * from ar_stokkantong where noKantong='$komponen[noKantong]'"));
?>
    <td class=input><?=$tujuan1[nama]?><?=$tujuan[nama]?><?=$rs?></td>

    <td class=input><?=$komponen[kadaluwarsa_ktg]?></td>
    <td class=input><?=$komponen[tgl_Aftap]?></td>
    <td class=input><?=$komponen[lama_pengambilan]?> menit</td>
    <td class=input><?=$komponen[tglperiksa]?></td>
    <td class=input><?=$komponen[tglpengolahan]?></td>
    <td class=input><?=$komponen[kadaluwarsa]?></td>
    <td class=input><?=$komponen[tgl_keluar]?></td>
    <td class=input><?=$buang[tgl_buang]?></td>
    <?
    $pengolahan=mysql_fetch_assoc(mysql_query("select Pisah,pemisahan from hpengolahan where noKantong='$komponen[noKantong]' "));
    $pisah='';if ($pengolahan[pemisahan]=='1') $pisah='Otomatis';if ($pengolahan[pemisahan]=='0') $pisah='Manual';
    ?>
    <td class=input><?=$pengolahan[Pisah]?></td>
    <td class=input><?=$pisah?></td>
<?
    $asalktg=mysql_fetch_assoc(mysql_query("select kantongAsal from stokkantong where noKantong='$komponen[noKantong]'"));
    if ($komponen[kantongAsal]!=NULL) $ak="Transfer Bag";
    if ($komponen[kantongAsal]==NULL) $ak="";
?>
    <td class=input><?=$ak?><br><?=$asalktg[kantongAsal]?></td>

    <?
    $sah='Belum';if ($komponen[sah]=='1') $sah='Sudah';
    $konfirm='Belum';if ($komponen[statKonfirmasi]=='1') $konfirm='Sudah';
    ?>
    <td class=input><?=$sah?></td>
    <td class=input><?=$ckt_nat?></td>
    <td class=input><?=$konfirm?></td>
    <td class=input><?=$komponen['abs']?></td>
    <td class=input><?=$komponen[tgl_release]?></td>
    <td class=input><?=$release?></td>
    </tr>

<?}?>
</table>

<br><font size="4">DATA DONASI</font>
<table class=form border=1 cellpadding=0 cellspacing=0 width="100%">
    <tr tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center" >
        <td rowspan='2'>No</td>
            <th rowspan=2>No Kantong</th>
        <th colspan=10>Pendonor</th>
        <th colspan=6>Aftap</th>
        <th colspan=6>Petugas</th>
    </tr>
        <tr tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center">
        <th>ID</th>
        <th>Nama</th>
        <th>JK</th>
        <th>Umur</th>
        <th>Gol</th>
        <th>Donor</th>
        <th>BB</th>
        <th>Tensi</th>
        <th>HB</th>
        <th>Ket</th>
        <th>Jam<br>Ambil</th>
        <th>Jam <br>selesai</th>
        <th>Jenis</th>
        <th>Asal</th>
        <th>Instansi</th>
        <th>Status</th>
        <th>Dokter</th>
        <th>Tensi</th>
        <th>HB</th>
        <th>Aftap</th>
        <th>Input</th>
        <th>Tgl<br>Input</th>
    </tr>
<?
    if ($donasi1[kantongAsal]==NULL){
$no=1;
while ($donasi=mysql_fetch_assoc($donasi0)) {
?>
<tr class="record">
    <?
    $asalktg=mysql_fetch_assoc(mysql_query("select kantongAsal from stokkantong where noKantong='$donasi[NoKantong]'"));
    ?>
    <td><?=$no++?></td>
        <td class=input><?=$donasi[NoKantong]?></td>
    <td class=input><?=$donasi[KodePendonor]?></td>
    <?
    $pendonor=mysql_fetch_assoc(mysql_query("select Nama from pendonor where Kode='$donasi[KodePendonor]'"));
    ?>
    <td class=input><?=$pendonor[Nama]?></td>
    <?
    if ($donasi[jk]=='0') $jk='Laki-laki';
    if ($donasi[jk]=='1') $jk='Perempuan';
    if ($donasi[jumHB]=='1') $hb='tenggelam';
    if ($donasi[jumHB]=='2') $hb='Melayang';
    if ($donasi[jumHB]=='3') $hb='Mengapung';
    if ($donasi[donorbaru]=='0') $baru='Baru';
    if ($donasi[donorbaru]=='1') $baru='Ulang';
    ?>
    <td class=input><?=$jk?></td>
    <td class=input><?=$donasi[umur]?>th</td>
    <td class=input><?=$donasi[gol_darah]?>(<?=$donasi[rhesus]?>)</td>
    <td class=input><?=$donasi[donorke]?> kali</td>
    <td class=input><?=$donasi[beratBadan]?></td>
    <td class=input><?=$donasi[tensi]?></td>
    <td class=input><?=$donasi[Hb]?> (<?=$hb?>)</td>
    <td class=input><?=$baru?></td>
    <td class=input><?=$donasi[jam_ambil]?></td>
    <td class=input><?=$donasi[jam_selesai]?></td>

    <?
    if ($donasi[JenisDonor]=='0') $ds='DS';
    if ($donasi[JenisDonor]=='1') $ds='DP';
    if ($donasi[JenisDonor]=='2') $ds='Autologus';
    if ($donasi[tempat]=='M') $tempat1='MU';
    if ($donasi[tempat]!='M') $tempat1='DG';
    ?>
    <td class=input><?=$ds?></td>
    <td class=input><?=$tempat1?></td>
    <td class=input><?=$donasi[Instansi]?></td>
    <?
    if ($donasi[Pengambilan]=='0') $status='Berhasil';
    if ($donasi[Pengambilan]=='2') $status='Gagal/Mislek';
    ?>
    <td class=input><?=$status?></td>
    <?
    $dokter=mysql_fetch_assoc(mysql_query("select Nama from dokter_periksa where kode='$donasi[NamaDokter]'"));
    ?>
    <td class=input><?=$dokter[Nama]?></td>
    <td class=input><?=$donasi[petugasTensi]?></td>
    <td class=input><?=$donasi[petugasHB]?></td>
    <td class=input><?=$donasi[petugas]?></td>
    <td class=input><?=$donasi[user]?></td>
    <td class=input><?=$donasi[Tgl]?></td>
    </tr>
    <?}
} else {?>
    <!--jika kantong transfer-->
<?
    $no=1;
while ($donasi3=mysql_fetch_assoc($donasi2)) {
?>
<tr class="record">
    <?
    //$asalktg=mysql_fetch_assoc(mysql_query("select kantongAsal from stokkantong where noKantong='$donasi[NoKantong]'"));
    ?>
    <td><?=$no++?></td>
        <td class=input><?=$donasi3[NoKantong]?></td>
    <td class=input><?=$donasi3[KodePendonor]?></td>
    <?
    $pendonor=mysql_fetch_assoc(mysql_query("select Nama from pendonor where Kode='$donasi3[KodePendonor]'"));
    ?>
    <td class=input><?=$pendonor[Nama]?></td>
    <?
    if ($donasi3[jk]=='0') $jk='Laki-laki';
    if ($donasi3[jk]=='1') $jk='Perempuan';
    if ($donasi3[jumHB]=='1') $hb='tenggelam';
    if ($donasi3[jumHB]=='2') $hb='Melayang';
    if ($donasi3[jumHB]=='3') $hb='Mengapung';
    if ($donasi3[donorbaru]=='0') $baru='Baru';
    if ($donasi3[donorbaru]=='1') $baru='Ulang';
    ?>
    <td class=input><?=$jk?></td>
    <td class=input><?=$donasi3[umur]?>th</td>
    <td class=input><?=$donasi3[gol_darah]?>(<?=$donasi[rhesus]?>)</td>
    <td class=input><?=$donasi3[donorke]?> kali</td>
    <td class=input><?=$donasi3[beratBadan]?></td>
    <td class=input><?=$donasi3[tensi]?></td>
    <td class=input><?=$donasi3[Hb]?> (<?=$hb?>)</td>
    <td class=input><?=$baru?></td>
    <td class=input><?=$donasi3[jam_ambil]?></td>
    <td class=input><?=$donasi3[jam_selesai]?></td>

    <?
    if ($donasi3[JenisDonor]=='0') $ds='DS';
    if ($donasi3[JenisDonor]=='1') $ds='DP';
    if ($donasi3[JenisDonor]=='2') $ds='Autologus';
    if ($donasi3[tempat]=='M') $tempat1='MU';
    if ($donasi3[tempat]!='M') $tempat1='DG';
    ?>
    <td class=input><?=$ds?></td>
    <td class=input><?=$tempat1?></td>
    <td class=input><?=$donasi3[Instansi]?></td>
    <?
    if ($donasi3[Pengambilan]=='0') $status='Berhasil';
    if ($donasi3[Pengambilan]=='2') $status='Gagal/Mislek';
    ?>
    <td class=input><?=$status?></td>
    <?
    $dokter=mysql_fetch_assoc(mysql_query("select Nama from dokter_periksa where kode='$donasi[NamaDokter]'"));
    ?>
    <td class=input><?=$dokter3[Nama]?></td>
    <td class=input><?=$donasi3[petugasTensi]?></td>
    <td class=input><?=$donasi3[petugasHB]?></td>
    <td class=input><?=$donasi3[petugas]?></td>
    <td class=input><?=$donasi3[user]?></td>
    <td class=input><?=$donasi3[Tgl]?></td>
    </tr>
    <!--Update 31032020 -->
<?}}?>

</table>


<br><font size="4">DATA DISTRIBUSI RUMAH SAKIT</font>
<table class=form border=1 cellpadding=0 cellspacing=0 width="100%">
    <tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;"  onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center" >
        <td rowspan='2'>No</td>
            <th rowspan=2>No Kantong</th>
        <th rowspan=2>No Form</th>
        <th colspan=2>Rumah Sakit</th>
        <th colspan=7>Pasien</th>
    </tr>
          <tr style="background-color:#FF6346; font-size:12px; color:#FFFFFF; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'" align="center">
        <th>Nama RS</th>
        <th>No RM</th>
        <th>ID</th>
        <th>Nama</th>
        <th>Gol(rh)</th>
        <th>Kelamin</th>
        <th>Umur</th>
        <th>Layanan</th>
        <th>Status</th>
      </tr>
    <?
    //$komponen1=mysql_query("select tgl as tgl,produk,pisah from dpengolahan where Produk!='' and pisah!='' and CAST(tgl as date)>='$today' and CAST(tgl as date)<='$today1' group by pisah order by pisah ASC");
    $no=1;
    while ($distribusi=mysql_fetch_assoc($distribusi0)) {
    ?>
        <tr class="record">
            <td><?=$no++?></td>
            <td class=input><?=$distribusi[NoKantong]?></td>
            <td class=input><?=$distribusi[NoForm]?></td>
            <?
            $data1=mysql_fetch_assoc(mysql_query("select * from htranspermintaan where noform='$distribusi[NoForm]'"));
            $pasien=mysql_fetch_assoc(mysql_query("select * from pasien where no_rm ='$distribusi[no_rm]'"));
            $nmrs=mysql_fetch_assoc(mysql_query("select NamaRs from rmhsakit where Kode='$data1[rs]'"));
            $layanan=mysql_fetch_assoc(mysql_query("select nama from jenis_layanan where kode='$data1[jenis]'"));
            ?>
                  <td class=input><?=$nmrs[NamaRs]?></td>
            <td class=input><?=$data1[regrs]?></td>
            <td class=input><?=$pasien[no_rm]?></td>
            <td class=input><?=$pasien[nama]?></td>
            <td class=input align="center"><?=$pasien[gol_darah]?>(<?=$pasien[rhesus]?>)</td>
            <?
            if ($pasien[kelamin]=='L') $kelamin='Laki-laki';
            if ($pasien[kelamin]=='P') $kelamin='Perempuan';
            if ($distribusi[Status]=='0') $stat='DiBawa';
            if ($distribusi[Status]=='1') $stat='Titip';
            if ($distribusi[Status]=='B') $stat='Batal';
            ?>
            <td class=input><?=$kelamin?></td>
            <td class=input><?=$data1[umur]?></td>
            <td class=input><?=$layanan[nama]?></td>
            <td class=input><?=$stat?></td>
        </tr>

    <?}?>
</table>
<?
mysql_close();
?>
<table width="100%" border="0">
    <tr style="background-color:#FFFFFF;"> <td align="right"><font size="1">Versi 002 : 26-03-2018</font> </td><tr>
</table>

<script type="text/javascript">
    function validasikantong()
    {
        if (document.getElementById('minta1').value.length <= 3)
        {
            alert('Masukkan No.Kantong ..');return false;
        }
    }
</script>
<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
      
      
<?}else{?>
    <table border=1 cellpadding=5 cellspacing=5 style="border-collapse:collapse" >
        <tr style="background-color:red; font-size:14px; color:#FFFFFF; font-family:Verdana;"  align='center'>
            <td rowspan="2">No</td>
            <td rowspan="2">Tanggal <br>Sampel</td>
            <td rowspan="2">ID Sampel</td>
            <td rowspan="2">Pendonor</td>
            <td rowspan="2">Gol.<br>Darah</td>
            <td rowspan="2">No. Telp</td>
            <td rowspan="2">Jenis<br>Donor</td>
            <td colspan="3" style="background-color:#ffa600">Permintaan Darah</td>
            <td colspan="3" style="background-color:#5500ff">Titer</td>
            <td colspan="5" style="background-color:#a32103">Hematologi</td>
            <td rowspan="2" style="background-color:#33a303">Chlia</td>
            <td rowspan="2" style="background-color:#09e3b0">NAT</td>
            <td rowspan="2" style="background-color:#04b3bf">KGD</td>
            
        </tr>
        <tr style="background-color:red; font-size:14px; color:#FFFFFF; font-family:Verdana;"  align='center'>
            <td style="background-color:#ffa600">Tgl.</td>
            <td style="background-color:#ffa600">Nama</td>
            <td style="background-color:#ffa600">Rumah Sakit</td>
            
            <td style="background-color:#5500ff">Sampel</td>
            <td style="background-color:#5500ff">Nilai</td>
            <td style="background-color:#5500ff">Hasil</td>
          
            <td style="background-color:#a32103">HB</td>
            <td style="background-color:#a32103">HCT</td>
            <td style="background-color:#a32103">TC</td>
            <td style="background-color:#a32103">LEU</td>
            <td style="background-color:#a32103">Hasil</td>
        
            
        </tr>
            <?
            //pagination
            //require_once('config/dbi_connect.php');

            $sql="SELECT * from samplekode WHERE sk_kode='$nkt'";
            $sq=mysql_query($sql);
            $no=0;
            $jum = mysql_num_rows($sq);
    
          if ($jum = 0){?>
              <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
              <td align="center" colspan="22">Tidak Ada Data Pemeriksaan</td>
              </tr>
          <?
          } else {
            while($dt=mysql_fetch_assoc($sq)){
                $no++;
                ?>
                    <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='biasa'">
                            <td align="right"><?=$no?></td>
                            <td align="left"><?=$dt[sk_on_insert]?></td>
                            <td align="left"><?=$dt[sk_kode]?></td>
                        <?//cari donor
                            $donor = mysql_fetch_assoc(mysql_query("select Nama,telp2 from pendonor where Kode='$dt[sk_donor]' limit 1"));?>
                            <td align="left"><?=$donor[Nama]?></td>
                            <td align="left"><?=$dt[sk_gol].$dt[sk_rh]?></td>
                            
                            <td align="left"><?=substr($donor[telp2], 0, -5) . 'xxxxx'?></td>
                        <?//cari transaksi donor
                        $ht = mysql_fetch_assoc(mysql_query("select id_permintaan,CASE donor_tpk
                        WHEN '0' THEN 'APH'
                        WHEN '1' THEN 'TPK'
                        END AS donor_tpk ,CASE JenisDonor
                        WHEN '0' THEN 'DS'
                        WHEN '1' THEN 'DP'
                        END AS JenisDonor from htransaksi where KodePendonor='$dt[sk_donor]' order by insert_on DESC limit 1"));?>
                            <td align="left"><?=$ht[JenisDonor].' ('.$ht[donor_tpk].')'?></td>
                        <?//cari pasien
                        $psn = mysql_fetch_assoc(mysql_query("select nama,NamaRs,umur, date(tgl_register) as tgl from v_caripasien where noform='$ht[id_permintaan]' limit 1"));
                            if ($ht[JenisDonor]=="DP"){?>
                            <td align="left"><?=$psn[tgl]?></td>
                            <td align="left"><?=$psn[nama].' ('.$psn[umur].' thn)'?></td>
                            <td align="left"><?=$psn[NamaRs]?></td>
                                <?}else {?>
                            <td align="left">-</td>
                            <td align="left">-</td>
                            <td align="left">-<?}?>
                        <?//cari titer
                            $titer = mysql_fetch_assoc(mysql_query("select cov_titer, CASE cov_vol
                            WHEN '0' THEN 'Rusak/Keruh'
                            WHEN '1' THEN 'Baik/Cukup'
                            ELSE '-'
                            END AS cov_vol
                            ,CASE cov_hasil
                            WHEN '0' THEN 'Tidak Lolos'
                            WHEN '1' THEN 'Lolos'
                            ELSE '-'
                            END AS cov_hasil from covid where cov_sampel='$dt[sk_kode]' order by on_insert DESC limit 1"));
                            ?>
                        <?if ($titer['cov_vol']=="Baik/Cukup" ) {
                            echo '<td align="center">Baik/Cukup</td>';
                            } else if ($titer['cov_vol']=="Rusak/Keruh" ) {
                            echo '<td align="center" style="background-color:#FF0000">Rusak/Keruh</td>';
                            } else {
                            echo '<td align="center">-</td>';
                            }?>
                            <td align="left"><?=$titer[cov_titer]?></td>
                        <?if ($titer[cov_hasil]=="Lolos" ) {
                            echo '<td align="center">Lulus</td>';
                            } else if ($titer[cov_hasil]=="Tidak Lolos" ) {
                            echo '<td align="center" style="background-color:#FF0000">Tidak Lolos</td>';
                            } else {
                            echo '<td align="center">-</td>';
                            }?>
                        <?//cari hemmatologi
                            $hm = mysql_fetch_assoc(mysql_query("select dl_hb,dl_hct,dl_plt,dl_leu, CASE dl_hasil
                            WHEN '0' THEN 'Tidak Lolos'
                            WHEN '1' THEN 'Lolos'
                            ELSE 'Cek Ulang'
                            END AS dl_hasil from hematologi where dl_sampel='$dt[sk_kode]' order by on_insert DESC limit 1"));
                            if ($hm['dl_hb']==""){?>
                            <td align="left">-</td>
                            <td align="left">-</td>
                            <td align="left">-</td>
                            <td align="left">-</td>
                            <td align="left">-</td>
                        <?}else {?>
                            <td align="left"><?=$hm[dl_hb]?></td>
                            <td align="left"><?=$hm[dl_hct]?></td>
                            <td align="left"><?=$hm[dl_plt]?></td>
                            <td align="left"><?=$hm[dl_leu]?></td>
                            <?if ($hm[dl_hasil]=="Cek Ulang" ) {
                            echo '<td align="center" style="background-color:#FF0000">Cek Ulang</td>';
                            } else {
                            echo '<td align="center">'.$hm[dl_hasil].'</td>';
                            }?>
                        <?}//cari IMLTD
                            $imltd = mysql_fetch_assoc(mysql_query("select noKantong, Hasil from hasilelisa where (noKantong='$dt[sk_kode]' or idsample='$dt[sk_kode]')  order by Hasil DESC limit 1"));
                            if ($imltd[Hasil]=="0" ) {
                            echo '<td align="center">NR</td>';
                            } else if ($imltd[Hasil]=="1" ) {
                            echo '<td align="center" style="background-color:#FF0000">R</td>';
                            } else if ($imltd[Hasil]=="2" ) {
                            echo '<td align="center" style="background-color:#FF0000">GZ</td>';
                            } else {
                            echo '<td align="center">-</td>';
                            }?>
                        <?//cari NAT
                             $nat = mysql_fetch_assoc(mysql_query("select idsample,Hasil from hasilnat where idsample='$dt[sk_kode]'  order by Hasil DESC limit 1"));
                             if ($nat['Hasil']=="0" ) {
                             echo '<td align="center">NR</td>';
                             } else if ($nat[Hasil]=="1" ) {
                             echo '<td align="center" style="background-color:#FF0000">R</td>';
                                } else if ($nat[Hasil]=="2" ) {
                                 echo '<td align="center" style="background-color:#FF0000">GZ</td>';
                                 } else {
                                 echo '<td align="center">-</td>';
                                 }?>
                            <td align="left"><?=$dt[sk_gol].$dt['sk_rh']?></td>
                        
                                                
                    </tr>
                    <?php
                                                                  }}?>
    </table>
<?}///////////TABLE SAMPEL
                                                                mysql_close();
                                                                ?>
