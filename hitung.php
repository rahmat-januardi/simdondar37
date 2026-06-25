<head>
<script type="text/javascript">
function showHint(str)
{
if (str.length==0)
  { 
  document.getElementById("txtHint").innerHTML="";
  return;
  }
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
//xmlhttp.open("GET","QC/detail_hitung.php?q="+str,true);
xmlhttp.send();
}
</script>

<head>
<link href="modul/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="modul/thickbox/thickbox.js"></script>
<script language="javascript">
function selectSupplier(Kode){
	  $('input[@name=kodeSup]').val(Kode);
	  tb_remove(); 
}
function selectKode(Kode){
	  $('input[@name=kode]').val(Kode);
 	  tb_remove(); 
	  dbar(Kode);
}
</script>
</head>

<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<?
include('clogin.php');
include('config/db_connect.php');
$namauser=$_SESSION[namauser];
                
//Membuat nomor transaksi 5 digit ==============================================                               
$tgl    = date('Y-m-d');
$prefix   = "AJ"; //Invoice
$kdthn    = substr(date("Y"),2,2);
$kdprefix = $prefix.$kdthn;
$jumdigit = 6;
$kddata   = mysql_fetch_assoc(mysql_query("select notrans from hstok_transaksi where substring(notrans,1,4)='$kdprefix' order by notrans desc limit 1"));
$nodata   = substr($kddata[notrans],4,$jumdigit);
$no       = $nodata+1;
$j_nol   = $jumdigit-(strlen(strval($no)));
          for ($i=0; $i<$j_nol; $i++){
		    $jnol.="0";
	  }
$notrans  = $kdprefix.$jnol.$no;
$uraian="Penyesuan Barang-".$notrans."-".$namauser;
//Akhir pembuatan nomor transaksi otomatis======================================


if (isset($_POST[submit])) {
	$kode		=$_POST[kode];
     $qtytransaksi	=$_POST[qtytransaksi];
	$keterangan	=$_POST[keterangan];
	$tipe		=$_POST[korek];
	$reagenimltd	=$_POST[reagenujs];
	$kadaluwarsa	=$_POST[kadaluwarsa];
	$nolot		=$_POST[nolot];
	
	
	if ($tipe=="min"){
		$qtymasuk =0;
		$qtykeluar=$qtytransaksi;
		$update=mysql_query("update hstok set stoktotal=stoktotal-'$qtykeluar' where kode='$kode'");
	}else{
		$qtymasuk =$qtytransaksi;
		$qtykeluar=0;
		$update=mysql_query("update hstok set stoktotal=stoktotal+'$qtymasuk' where kode='$kode'");
		
		//Proses apabila Reagen IMLTD
		$sql        = mysql_query("select reagenujs,nama_reagen,metode,ketsatuan from hstok where kode='$kode'");
		$sql1       = mysql_fetch_array($sql);
		if($reagenimltd=='1'){
			$nama_reagen	= $sql1['nama_reagen'];
			$metode     	= $sql1['metode'];
			$jmlkit     	= $qtymasuk;
			$jmltest		= $sql1['ketsatuan'];
			$kodestok   	= $kode;
			$nm     		= substr($nama_reagen,0,2);
               $idp			= mysql_query("select max(kode) as kode from reagen where kode like '$nm%'");
               $idp1		= mysql_fetch_assoc($idp);
               $kdtp		= substr($idp1[kode],2);
			
               if ($kdtp<1) {
                    $kdtp="00000";
               }
               $kdtp2=(int)$kdtp;
			for ($jk=0;$jk<$jmlkit;$jk++){
                    $kdtp2++;
                    $j_nol1= 6-(strlen($kdtp2));
                    $idp4='';
                    for ($i=0; $i<$j_nol1; $i++){
                        $idp4 .="0";
                    }
                    $kodereagen=$nm.$idp4.$kdtp2;	
                    $sqlreagen="insert into reagen (kodeSup,Nama,noLot,jumTest,metode,tglKad,status,kode,keterangan, kodestok) values
                            ('UDD','$nama_reagen','$nolot','$jmltest','$metode','$kadaluwarsa','0','$kodereagen','$notrans','$kodestok')";
                    $insertreagen=mysql_query($sqlreagen);
                }
		}
	}
	//insert header transaksi
	$simpan=mysql_query("insert into hstok_transaksi(notrans, supplier, tanggal, jenis, petugas,uraian, noreferensi)
                        values ('$notrans','Logistik','$tgl','$prefix','$namauser','$uraian','$keterangan')");
	//insert detail transaksi
	$detail=mysql_query("insert into hstok_transaksi_detail (notrans, kode, qtytransaksi, qtymasuk, qtykeluar)
                        values ('$notrans','$kode','$qtytransaksi','$qtymasuk','$qtykeluar')");
	
	if ($update) echo ("Data telah ter-update !!
	  <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"1; URL=$PHP_SELF\">");
}

?>

<form name="masterbarang" method="POST" action="<?=$PHPSELF?>">
<h1 class="table">Penghitungan Volume</h1>
<table class="form" border="1" cellpadding=2 cellspacing=3>

<script>
function hitung(){
	var stok = eval(document.masterbarang.stoktotal.value);
	var update = eval(document.masterbarang.qtytransaksi.value);
	if (document.masterbarang.korek[0].checked==true) {
        var total = stok + update; } 
	if (document.masterbarang.korek[1].checked==true) {
        var total= stok - update; } 
        document.masterbarang.stokakhir.value=total;
}

</script>

<tr>
	<td>Jumlah Stok</td>
	<td class="input"><input name="stoktotal" type="text" size="5"  ></td>
	</tr>
<tr>
     <td>Jumlah</td> <td>
	<input type="radio" name="korek" value="plus" checked="checked">+
        <input type="radio" name="korek" value="min">-
	<input  onchange="hitung()" name="qtytransaksi" type="text" size="5" placeholder="Isi Angka"> Klik '+' jika menambah '-' jika mengurangi</td>
        </tr>

<tr>
        <td>Stok Total</td>
        <td class="input"><input name="stokakhir" type="text" size="5" readonly="readonly"></td>
	
        </tr>

</table>
<input name="submit" type="submit" value="Simpan Koreksi Stok">
</form>
<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
<p><span id="txtHint"></span></p>

