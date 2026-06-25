<link href="modul/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="modul/thickbox/thickbox.js"></script>
<script type="text/javascript" src="js/disable_enter.js"></script>

<script language="javascript">
function selectBayar(Kode) {
    $('input[name=kodeBia]').val(Kode);
    tb_remove();
}
</script>

<body OnLoad="document.cari.no_form.focus();">

    <?
include ("config/db_connect.php");
require_once("modul/background_process.php");
$rs=mysql_query("select rs from kwitansi");if (!$rs){mysql_query("ALTER TABLE `kwitansi` ADD `no_rm` VARCHAR( 15 ) NULL DEFAULT NULL ,
ADD `rs` VARCHAR( 7 ) NULL DEFAULT NULL ,
ADD `layanan` VARCHAR( 7 ) NULL DEFAULT NULL ");
mysql_query("update kwitansi as kw,dtransaksipermintaan dt set kw.no_rm=dt.no_rm,kw.rs=dt.rs,kw.layanan=dt.layanan where kw.noform=dt.noform");

}

$petugas = $_SESSION[nama_lengkap];
$tgl_permintaan=date("Y-m-d H:i:s");
$yesterday = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
$tgl_yesterday=date("Y-m-d",$yesterday);
$td0=php_uname('n');
$td0=substr($td0,0,3);
if (!isset($_POST[submit1])){
?>
    <h1 class="table">Cetak Ulang Kwitansi Pembayaran Darah</h1>
    <form name="cari" method="post" action="<?echo $PHPSELF?>">
        <table class="form" cellspacing="0" cellpadding="0">
            <tr>
                <td>No Formulir</td>
                <td class="input">
                    <input name="no_form" type="text" size="20" value="<?=$_GET[noform]?>" onChange="checkform()">
                </td>
            </tr>
        </table>
        <br>
        <input name="submit1" type="submit" value="Cari">
    </form>
    <?}

if (isset($_POST['submit2'])) {
	$noform=$_POST['noform'];
	$kode=$_POST['kodeBia'];
	
	$biaya=mysql_fetch_assoc(mysql_query("select * from biaya where Kode='$kode'"));
	for ($i=0;$i<$_POST[jumkantong];$i++) {
		$stat1="3";
		$stat=$_POST['status'][$i];
		$shift1=$_POST['shift'];
		$tempat=$_POST['tempat'];
		$nokan=$_POST['nokantong'][$i];
		if ($stat=="B") $stat1="2";
		
		//$histori=mysql_query("insert into histori (`username`,`waktu`,`action`,`bagian`,`nokantong`,`statkan`) values 				('$petugas','$tgl_permintaan','Distribusi Darah Ke Pasien','Loket Permintaan Pasien','$nokan','6')");

		$transaksi1_sql="UPDATE dtransaksipermintaan
				SET Status='$stat',shift_keluar='$shift1' where (NoKantong='$nokan' and NoForm='$noform')";

		$update_sql="UPDATE stokkantong SET Status='$stat1' where NoKantong='$nokan'";

		$pasien_sql="UPDATE daftarpasien set up='1' where noform='$noform'";

		$transaksi1=mysql_query($transaksi1_sql);
		$update=mysql_query($update_sql);
		$pasien_up=mysql_query($pasien_sql);

			$transaksi1_sql=base64_encode($transaksi1_sql.';');
			$update_sql=base64_encode($update_sql.';');
			$pasien_sql=base64_encode($pasiensql,';');

$myfile="bdrs/dari-$td0-$tgl_permintaan.zip";
if (file_exists($myfile)) { $fh=fopen($myfile,'a') or die ("Cant open file"); } else { $fh=fopen($myfile,'w') or die ("Cant open file"); }
		fwrite($fh,$transaksi1_sql);
		fwrite($fh,"\n");
		fwrite($fh,$update_sql);
		fwrite($fh,"\n");
		fwrite($fh,$pasien_sql);
		fwrite($fh,"\n");


$myfile1="bdrs/dari-$td0-$tgl_yesterday.zip";
if (file_exists($myfile1)) { $fh1=fopen($myfile1,'a') or die ("Cant open file"); } else { $fh1=fopen($myfile1,'w') or die ("Cant open file"); }
		fwrite($fh1,$transaksi1_sql);
		fwrite($fh1,"\n");
		fwrite($fh1,$update_sql);
		fwrite($fh1,"\n");
		fwrite($fh1,$pasien_sql);
		fwrite($fh1,"\n");


		$jumk=0;
		if ($stat=="1") {
			$jumk=$jumk;
			$biaya1=mysql_fetch_assoc(mysql_query("select * from biaya where NamaBiaya like '%CROSSMATCH%'")); 
			$kod=$biaya1[Kode];
			$bayar=$jumk;
			$nama_bia=$biaya1[NamaBiaya]; 
			$harga=$biaya1[Harga];
		}else if ($stat=="B") { 
			$jumk=$jumk+1;
			$biaya1=mysql_fetch_assoc(mysql_query("select * from biaya where NamaBiaya like '%CROSSMATCH%'")); 
			$kod=$biaya1[Kode];
			$bayar=$jumk*$biaya1[Harga];
			$nama_bia=$biaya1[NamaBiaya]; 
			$harga=$biaya1[Harga];
		}else{ 
			$jumk=$jumk+1;
			$kod=$biaya[Kode];
			$bayar=$jumk*$biaya[Harga]; 
			$nama_bia=$biaya[NamaBiaya];
			$harga=$biaya[Harga];
		}
		
		$pembayaran_sql="insert into dpembayaranpermintaan (
								notrans,kodeBrg,jum,subTotal,namabrg,harga,temphj,no_kantong,petugas,shift,tempat)
							values (
								'$noform','$kod','$jumk','$bayar','$nama_bia','$harga','$harga','$nokan','$petugas','$shift1','$tempat')
							ON DUPLICATE KEY UPDATE
								subTotal='$bayar',namabrg='$nama_bia',harga='$harga',temphj='$harga',petugas='$petugas',shift='$shift1'";
		$pembayaran=mysql_query($pembayaran_sql);
			$pembayaran_sql=base64_encode($pembayaran_sql.';');
		fwrite($fh,$pembayaran_sql);
		fwrite($fh,"\n");
		fwrite($fh1,$pembayaran_sql);
		fwrite($fh1,"\n");
	}
		fclose($fh);
		fclose($fh1);
	if ($transaksi1){ 
		//$noform10=explode('/',$noform);
		//$noform1=$noform10[0]."-".$noform10[1];
		$noform1=str_replace("/","-",$noform);
		//if (sizeof($noform10)<2) $noform1=$noform;
		echo ("Pembayaran No. <b>'$_POST[noform]'</b> telah diproses !!
		<meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"1; URL=pmikasir2.php?module=bayar_ulang&noform=$noform1\">");
		$_POST['periksa']="";
		backgroundPost('http://localhost/simudda/modul/background_up.php');
	}
}

if (isset($_POST[submit1])) {
	$tambah=mysql_query("select * from htranspermintaan where noform='$_POST[no_form]'");
	$nrow=0;
	if ($tambah) {
		$nrow=mysql_num_rows($tambah);
		$tambah1=mysql_fetch_assoc($tambah);
	$rs=mysql_fetch_assoc(mysql_query("select NamaRs from rmhsakit where Kode='$tambah1[rs]'"));
	$norm= $tambah1[no_rm];
	$pasien=mysql_fetch_assoc(mysql_query("select * from pasien where no_rm='$norm'"));
	}	
	if ($nrow<1){
	echo "Nomor formulir belum terdaftar, isi form permintaan dahulu";
	?>
    <META http-equiv="refresh" content="2; url=pmikasir2.php?module=permintaan">
    <?} else {
	$bayar=mysql_query("select * from dtransaksipermintaan where noForm='$_POST[no_form]' and (status='0' or status='1')");
	$nbayar=mysql_num_rows($bayar);
	if ($nbayar==0) {
	echo "Sudah dibayar atau darah belum dicrossmatch";
	?>
    <META http-equiv="refresh" content="2; url=pmikasir2.php?module=pembayaran_ulang">
    <?} else {
		?>
    <div class="table">FORM PEMBAYARAN</div>
    <table border="0" cellpadding="10">
        <tr valign="top">
            <td>
                <div class="table">Data Pasien</div>
                <table class="form" cellspacing="1" cellpadding="2">
                    <tr>
                        <td>No. Formulir</td>
                        <td class="input"><?=$tambah1[noform]?></td>
                    </tr>
                    <tr>
                        <td>Nama RS</td>
                        <td class="input"><?=$rs[NamaRs]?></td>
                    </tr>
                    <tr>
                        <td>Bagian</td>
                        <td class="input"><?=$tambah1[bagian]?></td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td class="input"><?=$tambah1[kelas]?></td>
                    </tr>
                    <tr>
                        <td>Jenis Layanan</td>
                        <? $layanan=mysql_fetch_assoc(mysql_query("select nama from jenis_layanan where kode='$tambah1[jenis]'"));
				
				?>

                        <td class="input"><?=$layanan[nama]?></td>
                    </tr>
                    <tr>
                        <td>Nama Dokter</td>
                        <td class="input"><?=$tambah1[namadokter]?></td>
                    </tr>
                    <tr>
                        <td>Nama Pasien</td>
                        <td class="input"><?=$pasien[nama]?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td class="input"><?=$pasien[alamat]?></td>
                    </tr>
                </table>
            </td>
            <td>
                <div class="table">LD</div>
                <form name=periksa method="post" action="<?=$PHP_SELF?>">
                    <table class="form" width="400" cellspacing="1" cellpadding="0" align="left">
                        <tr>
                            <td>No.</td>
                            <td>No Kantong</td>
                            <td>Metode</td>
                            <td>Produk</td>
                            <td>Status</td>
                            <td>Shift</td>
                            <td>Tempat</td>
                        </tr>
                        <?
				$produk=mysql_query("select * from dtransaksipermintaan where NoForm='$_POST[no_form]'"); 
				//$produk=mysql_query("select * from dtransaksipermintaan where NoForm='$_POST[no_form]' and status='1'");
				if ($produk) {
					$no=0;
					$jum=0;
					while ($produk1=mysql_fetch_assoc($produk)) {
						$jum=$jum+1;
						$nokantong=$produk1[NoKantong];	$namaproduk=$produk1[MetodeCross];
						$kantong=mysql_query("select * from stokkantong where noKantong='$nokantong'");
						$kantong1=mysql_fetch_assoc($kantong);
						$status=$produk1[Status]; $no++; $pilih0=""; $pilih1=""; $pilihB="";
						switch ($status) {
								case "0":
									$pilih0="selected";
									break;
								case "1":
									$pilih1="selected";
									break;
								case "B":
									$pilihB="selected";
										break;
						}
						echo "
				<tr>
					<td class=\"input\">$no</td>
					<td class=\"input\"><input name=nokantong[] value='$nokantong'></td>
					<td class=\"input\">$namaproduk</td>
					<td class=\"input\">$produk1[produk_darah]</td>";
				?>
                        <td class="input">
                            <select name='status[]'>
                                <option value='0' <?=$pilih0?>>Dibawa</option>
                                <option value='1' <?=$pilih1?>>Titip</option>
                                <option value='B' <?=$pilihB?>>Batal</option>
                            </select>
                        </td>


                        <!----td>Shift</td-->
                        <td class="styled-select" bgcolor="#ffa688">
                            <? $s1='';$s2='';$s3='';
						$waktu=date('H:i:s');
						$jam1=mysql_fetch_assoc(mysql_query("select * from shift where nama='I'"));
						$jam2=mysql_fetch_assoc(mysql_query("select * from shift where nama='II'"));	
						$jam3=mysql_fetch_assoc(mysql_query("select * from shift where nama='III'"));
						
						$sh1=$jam1[jam]; $sh2=$jam2[jam]; $sh3=$jam3[jam];
						if ($waktu >= $sh1 ){ $s1='selected';}
						if ($waktu >= $sh2 ){ $s2='selected';}
						if ($waktu >= $sh3 ){ $s3='selected';}
						if ($waktu < $sh1 ){ $s3='selected';}
					?>
                            <select name="shift">
                                <option value="1" <?=$s1?>>SHIFT I</option>
                                <option value="2" <?=$s2?>>SHIFT II</option>
                                <option value="3" <?=$s3?>>SHIFT III</option>
                            </select>
                        </td>
                        <td class="input">
                            <select name='tempat'>
                                <option>UDD</option>
                                <option>BDRS</option>
                            </select>
                        </td>
        </tr>


        <?
					} 
				}?>
        <tr>
            <td bgcolor="#FFFFFF" colspan=5>
                <div class="table">Pembayaran</div>
            </td>
        </tr>
        <tr>
            <td>Kode Biaya</td>
            <td class="input" colspan=5>
                <input name="kodeBia" type="text" size="15">
                <a href="modul/daftar_bayar.php?&width=500&height=400" class="thickbox">
                    <img src="images/button_search.png" border="0" />
                </a>
            </td>
        </tr>
        <tr>
            <td bgcolor="#FFFFFF" colspan=5>
                <input type="hidden" value="1" name="periksa">
                <input type="hidden" value="<?=$no?>" name="jumkantong">
                <input type="hidden" value="<?=$jum?>" name="jumlah">
                <input type="hidden" value="<?=$tambah1[noform]?>" name="noform">
                <input type="submit" name="submit2" value="Simpan">
            </td>
        </tr>
    </table>
    </td>
    </tr>
    <tr>
        <td>
            <?$jenis=mysql_query("select * from dtranspermintaan where NoForm='$_POST[no_form]'");
				while ($jenis1=mysql_fetch_assoc($jenis)) {?>
            <div class="table">Jumlah yang diminta</div>
            <table class="form" cellspacing="1" cellpadding="2">
                <tr>
                    <td>Jenis Darah</td>
                    <td class="input"><?=$jenis1[JenisDarah]?></td>
                </tr>
                <tr>
                    <td>Golongan Darah</td>
                    <td class="input"><?=$jenis1[GolDarah]?></td>
                </tr>
                <tr>
                    <td>Rhesus</td>
                    <td class="input"><?=$jenis1[Rhesus]?></td>
                </tr>
                <tr>
                    <td>Volume</td>
                    <td class="input"><?=$jenis1[cc]?></td>
                </tr>
            </table>
            <?
				}
				?>
        </td>
    </tr>
    </table>
    </form>
    <?  
}}}
?>