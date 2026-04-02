<?
require_once('config/db_connect.php');
session_start();
$namaudd=$_SESSION[namaudd];
$namauser=$_SESSION[namauser];


/*
$col5=mysql_query("SELECT `fase1` FROM `dtransaksipermintaan`");if (!$col5){mysql_query("
ALTER TABLE `dtransaksipermintaan` 
ADD `fase1` VARCHAR( 15 ) NOT NULL DEFAULT '-',
ADD `fase2` VARCHAR( 15 ) NOT NULL DEFAULT '-',
ADD `fase3` VARCHAR( 15 ) NOT NULL DEFAULT '-',
ADD `no_rm` VARCHAR( 20 ) NOT NULL DEFAULT '-'");}

$col4=mysql_query("SELECT `rs` FROM `dtransaksipermintaan`");if (!$col4){mysql_query("
ALTER TABLE `dtransaksipermintaan` 
ADD `rs` VARCHAR( 7 ) NULL DEFAULT NULL ,
ADD `wil_rs` CHAR( 2 ) NULL DEFAULT NULL ,
ADD `gol_darah` VARCHAR( 3 ) NULL DEFAULT NULL ,
ADD `rh_darah` VARCHAR( 2 ) NULL DEFAULT NULL ,
ADD `produk_darah` VARCHAR( 25 ) NULL DEFAULT NULL ");}

$col5=mysql_query("SELECT `antar` FROM `dtransaksipermintaan`");if(!$col5){mysql_query("ALTER TABLE `dtransaksipermintaan` ADD `antar` INT( 1 ) NOT NULL DEFAULT '0' COMMENT '0=blm dicetak pengantar 1=sdh dicetak pengantar'");}

$col6=mysql_query("SELECT `bagian` FROM `dtransaksipermintaan`");if (!$col6){mysql_query("
ALTER TABLE `dtransaksipermintaan` 
ADD `bagian` VARCHAR( 15 ) NULL DEFAULT NULL ,
ADD `layanan` VARCHAR( 10 ) NULL DEFAULT NULL ");}

$col7=mysql_query("SELECT `shift_keluar` FROM `dtransaksipermintaan`");if (!$col7){mysql_query("
ALTER TABLE `dtransaksipermintaan` 
ADD `shift_keluar` CHAR( 1 ) NULL DEFAULT NULL ");}

$col8=mysql_query("SELECT * FROM `dtransaksipermintaan` where `shift_keluar` is NULL");if ($col8){mysql_query("update `dtransaksipermintaan` set `shift_keluar`=`shift`");}

*/

?>


<script language=javascript src="js/jquery-latest.js" type="text/javascript"> </script>
<script language=javascript src="js/alert.js" type="text/javascript"> </script>
<script src="js/cookies.js"></script>
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script src="components/placeholder/jquery.placehold-0.2.min.js"></script>
<script src="js/html5forms.fallback.js"></script>
<script language=javascript src="js/crossmatch.js" type="text/javascript"> </script>
<script language="javascript">
	function setFocus(){document.periksa.NoKantong.focus();}
</script>
<body OnLoad="document.cek_form.NoForm.focus();">
<?
include ("config/db_connect.php");
require_once("modul/background_process.php");

$tgl_permintaan=date("Y-m-d H:i:s");
$yesterday = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
$tgl_yesterday=date("Y-m-d",$yesterday);
$jamsls=date("H:i:s");
$td0=php_uname('n');
$td0=substr($td0,0,3);
if ($_POST['periksa']=="1") {
	$NoForm=$_POST['NoForm'];
	$gol_darah=$_POST['goldarah1'];
	$rhpsn=$_POST['rhpsn'];
	$rhmnt=$_POST['rhmnt'];
	$rm=$_POST['norm2'];
	$jenisdarah=$_POST['jenisdarah'];
	$layanan=$_POST['layanan'];
	$golDrh=$_POST['golDrh'];
	
$myfile0="bdrs/dari-$td0-$tgl_permintaan.zip";
if (file_exists($myfile0)) { $fh0=fopen($myfile0,'a') or die ("Cant open file"); } else { $fh0=fopen($myfile0,'w') or die ("Cant open file"); }
$myfile="bdrs/dari-$td0-$tgl_yesterday.zip";
if (file_exists($myfile)) { $fh=fopen($myfile,'a') or die ("Cant open file"); } else { $fh=fopen($myfile,'w') or die ("Cant open file"); }
	$update_os_sql="update pasien set gol_darah ='$golDrh', rhesus ='$rhpsn' where no_rm='$rm'";
	$update_os=mysql_query($update_os_sql);
			$update_os_sql=base64_encode($update_os_sql.';');
			fwrite($fh0,$update_os_sql);
			fwrite($fh0,"\n");
			fwrite($fh,$update_os_sql);
			fwrite($fh,"\n");
/*
$myfile0="bdrs/dari-$td0-$tgl_permintaan.zip";
if (file_exists($myfile0)) { $fh0=fopen($myfile0,'a') or die ("Cant open file"); } else { $fh0=fopen($myfile0,'w') or die ("Cant open file"); }
$myfile="bdrs/dari-$td0-$tgl_yesterday.zip";
if (file_exists($myfile)) { $fh=fopen($myfile,'a') or die ("Cant open file"); } else { $fh=fopen($myfile,'w') or die ("Cant open file"); }*/
	$update_os_sql1="update dtranspermintaan set GolDarah ='$gol_darah', Rhesus='$rhmnt' where NoForm='$NoForm'";
	$update_os1=mysql_query($update_os_sql1);
			$update_os_sql1=base64_encode($update_os_sql1.';');
			fwrite($fh0,$update_os_sql1);
			fwrite($fh0,"\n");
			fwrite($fh,$update_os_sql1);
			fwrite($fh,"\n");
	for ($i=0;$i<count($_POST['no_kantong']);$i++) {
		$NoKantong=strtoupper($_POST['no_kantong'][$i]);
		$golongan=$_POST['gol_drh'][$i];
		$rhesus=$_POST['rh_gol'][$i];
		$produk=$_POST['jenis'][$i];

		$metode=$_POST['metode'][$i];
		$status=$_POST['status'][$i];
		$cross=$_POST['cross'][$i];
		$ket=$_POST['ket'][$i];
		$titip=$_POST['titip'][$i];
		$aglutinasi=$_POST['aglutinasi'][$i];
		$fase1=$_POST['fasea'][$i];
		$fase2=$_POST['faseb'][$i];
		$fase3=$_POST['fasec'][$i];
		$listcomb=$_POST['listcomb'][$i];
		$keluar=$_POST['keluar'][$i];
		$titip=$_POST['titip'][$i];
		$sah=$_POST['sah'];
		$sah1=$_POST['sah1'];
		$sah2=$_POST['sah2'];
		$tempat=$_POST['tempat'];
		$norm1=$_POST['norm2'];
		$shift=$_POST['shift'];
		$rs=$_POST['rs'];
		
		// $rs=mysql_fetch_assoc(mysql_query("select rs from htranspermintaan where noform='$NoForm"));
		//$wilrs=mysql_fetch_assoc(mysql_query("select wilayah from rmhsakit where Kode='$rs[rs]'"));
		$ckt=mysql_num_rows(mysql_query("select * from dtransaksipermintaan where NoKantong='$NoKantong' and NoForm='$NoForm'"));
		if ($ckt==0) {
			$tambah_sql="insert into dtransaksipermintaan (`NoForm`,`NoKantong`,`Status`,`MetodeCross`,`StatusCross`,
				    `stat2`,`Ket`,`aglutinasi`,`listcomb`,`tgl`,`tgl_keluar`,`petugas`,`cheker`,`mengesahkan`,`stat3`,`tempat`,
				    `shift`,`fase1`,`fase2`,`fase3`,`no_rm`,`gol_darah`,`rh_darah`,`produk_darah`) 
				    values ('$NoForm','$NoKantong','$titip','$metode','$status','$cross','$ket','$aglutinasi','$listcomb',
				    '$tgl_permintaan','$tgl_permintaan','$sah2','$sah1','$sah','$keluar','$tempat',
				    '$shift','$fase1','$fase2','$fase3','$rm','$golongan','$rhesus','$produk')";
				//=======Audit Trial====================================================================================
				$log_mdl ='CROSSMATCH';
				$log_aksi='Input crossmatch kantong: '.$NoKantong.' No formulir: '.$NoForm;
				include_once "user_log.php";
				//=====================================================================================================


			$tambah1_sql="update stokkantong set Status='3' where  (NoKantong='$NoKantong')";
			$tambah2_sql="update dtransaksipermintaan as d,htranspermintaan as h set d.rs=h.rs where  d.NoForm='$NoForm' and d.NoForm=h.noform";
			$tambah3_sql="update dtransaksipermintaan as d,rmhsakit as r set d.wil_rs=r.wilayah where  d.NoForm='$NoForm' and d.rs=r.Kode";
			$tambah4_sql="update dtransaksipermintaan as d,htranspermintaan as h set d.bagian=h.bagian where  d.NoForm='$NoForm' and d.NoForm=h.noform";
			$tambah5_sql="update dtransaksipermintaan as d,htranspermintaan as h set d.layanan=h.jenis where  d.NoForm='$NoForm' and d.NoForm=h.noform";
			$tambah6_sql="update daftarpasien set jamsls='$jamsls', status= 'Selesai Proses Lab'  where up=0 and noform='$NoForm' ";
			$tambah=mysql_query($tambah_sql);
			$tambah1=mysql_query($tambah1_sql);
			$tambah2=mysql_query($tambah2_sql);
			$tambah3=mysql_query($tambah3_sql);
			$tambah4=mysql_query($tambah4_sql);
			$tambah5=mysql_query($tambah5_sql);
			$tambah6=mysql_query($tambah6_sql);
			$tambah_sql=base64_encode($tambah_sql.';');
			$tambah1_sql=base64_encode($tambah1_sql.';');
			$tambah2_sql=base64_encode($tambah2_sql.';');
			$tambah3_sql=base64_encode($tambah3_sql.';');
			$tambah4_sql=base64_encode($tambah4_sql.';');
			$tambah5_sql=base64_encode($tambah5_sql.';');
			$tambah6_sql=base64_encode($tambah6_sql.';');
			fwrite($fh0,$tambah_sql);
			fwrite($fh0,"\n");
			fwrite($fh0,$tambah1_sql);
			fwrite($fh0,"\n");
			fwrite($fh0,$tambah2_sql);
			fwrite($fh0,"\n");
			fwrite($fh0,$tambah3_sql);
			fwrite($fh0,"\n");
			fwrite($fh0,$tambah4_sql);
			fwrite($fh0,"\n");
			fwrite($fh0,$tambah5_sql);
			fwrite($fh0,"\n");
			fwrite($fho,$tambah6_sql);
			fwrite($fho,"\n");

			fwrite($fh,$tambah_sql);
			fwrite($fh,"\n");
			fwrite($fh,$tambah1_sql);
			fwrite($fh,"\n");
			fwrite($fh,$tambah2_sql);
			fwrite($fh,"\n");
			fwrite($fh,$tambah3_sql);
			fwrite($fh,"\n");
			fwrite($fh,$tambah4_sql);
			fwrite($fh,"\n");
			fwrite($fh,$tambah5_sql);
			fwrite($fh,"\n");
			fwrite($fh,$tambah6_sql);
			fwrite($fh,"\n");
		}
	}
			fclose($fh0);
			fclose($fh);
	if ($tambah) {
    $wa = "SELECT\n".
        "    htranspermintaan.noform, \n".
        "    rmhsakit.NamaRs, \n".
        "    rmhsakit.gateway, \n".
        "    pasien.nama, \n".
        "    pasien.gol_darah, \n".
        "    pasien.rhesus\n".
        "FROM\n".
        "    htranspermintaan\n".
        "    INNER JOIN\n".
        "    rmhsakit\n".
        "    ON \n".
        "        htranspermintaan.rs = rmhsakit.Kode\n".
        "    INNER JOIN\n".
        "    pasien\n".
        "    ON \n".
        "        htranspermintaan.no_rm = pasien.no_rm\n".
        "    WHERE \n".
        "    htranspermintaan.noform = '$NoForm'";
                                                                                         
    $cariwa=mysql_fetch_assoc(mysql_query($wa));
   /* if ($cariwa[StatusCross]=='1') $ket='Compatible';
    if ($cariwa[StatusCross]=='0') $ket='Incompatible boleh keluar';
    if ($cariwa[StatusCross]=='2') $ket='Incompatible tidak boleh keluar';*/
        if ($cariwa[gateway] != ""){
        $sapa='Semangat Pagi';
        $form=$cariwa['noform'];
        $pesan=$sapa.' '.$cariwa[NamaRs].', kami informasikan Crossmatch Permintaan Darah Nomor : '.$cariwa[noform].' Atas nama Pasien '.$cariwa[nama].' | Gol. '.$cariwa[gol_darah].'/'.$cariwa[rhesus].' telah selesai' ;
                                     
                            // WA Petugas
                            $kirim=mysql_query("insert into wagw.outbox (wa_mode,wa_no,wa_text) values ('0','$cariwa[gateway]','$pesan')");
            
                            //CURL CLoud
                            $postData = array("no_trans" => $NoForm, "status" => "2");
                            
                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                              CURLOPT_URL => 'http://enigmeds.com/pmi_online/rs/valid.php',
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => '',
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 0,
                              CURLOPT_FOLLOWLOCATION => true,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => 'POST',
                              CURLOPT_POSTFIELDS =>json_encode($postData),
                              CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json'
                              ),
                            ));

                            $response = curl_exec($curl);
                            curl_close($curl);
                            //Curl End
                          }
        
        
    backgroundPost('http://localhost/simudda/modul/background_up.php');
		echo "Data berhasil dientry<br>";
		?> <META http-equiv="refresh" content="2; url=pmilaboratorium.php?module=label_cross&noform=<?=$NoForm?>"> <?
	}
	$_POST['periksa']="";
}
if ($_POST['periksa']=="") { ?>
	<form name="cek_form" method="post" action="<?=$PHPSELF?>">
		<font size="3" color="black">Masukkan No Formulir --->
		<input type="search" name="NoForm" value="<?=$_GET[noform]?>" onkeydown="chang(event,this);" onchange="cari_form(); this.form.submit();"> Tekan ENTER </font>
	</form>
	<font size="4" color="red">DATA CROSSMATCH (UJI SILANG SERASI)</font>
	<form name="periksa" id="periksa" onsubmit="return ok()" method="POST" action=<?=$PHPSELF?>>
	<?
    $check_h=mysql_query("select * from htranspermintaan where noform='$_POST[NoForm]'");
    $check_h1=mysql_fetch_assoc($check_h);
	$wilrs=mysql_fetch_assoc(mysql_query("select wilayah from rmhsakit where Kode='$check_h1[rs]'"));
    $norm= $check_h1[no_rm];
    $pasien=mysql_fetch_assoc(mysql_query("select * from pasien where no_rm='$norm'"));
    $check_d=mysql_query("select GolDarah,Rhesus,JenisDarah,sum(Jumlah) as Jumlah,sum(JTitip) as JTitip from dtranspermintaan where NoForm='$_POST[NoForm]'");
    $check_d1=mysql_fetch_assoc($check_d);
	$check_cross=0;
if (isset($_POST[NoForm])) $check_cross=mysql_num_rows(mysql_query("select * from dtransaksipermintaan where NoForm='$_POST[NoForm]' and Status !='B'"));	$check_batal=mysql_num_rows(mysql_query("select * from dtransaksipermintaan where NoForm='$_POST[NoForm]' and Status='B'"));



	$jcross0=$check_d1[Jumlah]-$check_cross;
    ?>
	<input type="hidden" name="jcross" id="jcross" value="<?=$jcross0?>">
	<input name="NoForm" type=hidden value="<?=$check_h1[noform]?>">
		<input name="norm2" type=hidden value="<?=$check_h1[no_rm]?>">
	<table class="form" border=0 cellspacing=1 cellpadding=2 width="700px">
		
		<tr>
			<td>No. Formulir</td><td class="input"><?=$check_h1[noform]?></td>
			<td>Kode Pasien</td><td class="input" ><?=$check_h1[no_rm]?></td>
			
		</tr>		
		<tr>
			<td>No. reg. RS</td><td class="input"><?=$check_h1[regrs]?></td>
			<td>Nama Pasien</td><td class="input"><?=$pasien[nama]?></td>
		</tr>
		<tr>
			<td>Nama RS</td>
				<?
				$rmhskt=mysql_fetch_assoc(mysql_query("select NamaRs from rmhsakit where Kode='$check_h1[rs]'"));
				?>
				<td class="input" name="rs"><?=$rmhskt[NamaRs]?></td>
			<td>Nama Dokter</td><td class="input"><?=$check_h1[namadokter]?></td>
		</tr>
		<tr>
			<td>Bagian</td><td class="input"><?=$check_h1[bagian]?></td>
				<?
				$jenis=mysql_fetch_assoc(mysql_query("select nama from jenis_layanan where kode='$check_h1[jenis]'"));
				?>
			<td>Layanan</td><td class="input"><?=$jenis[nama]?></td>
		</tr>
		<tr>
		<td>Gol. Darah Pasien</td>
			<td class="input">
				<? $type=$pasien[gol_darah];
				$selected[$type]="selected";?>
				<select name="golDrh">
				<option value="A" <?=$selected["A"]?>>A</option>
				<option value="B" <?=$selected["B"]?>>B</option>
				<option value="AB" <?=$selected["AB"]?>>AB</option>
				<option value="O" <?=$selected["O"]?>>O</option>
				
				</select>  Jum. HB :  <?=$check_h1[HB]?>  gr/dl
			</td>
			<td>Gol. Darah Diminta</td>
			<td class="input">
				<? $type=$check_d1[GolDarah];
				$selected[$type]="selected";?>
				<select name="goldarah1">
				<option value="A" <?=$selected["A"]?>>A</option>
				<option value="B" <?=$selected["B"]?>>B</option>
				<option value="AB" <?=$selected["AB"]?>>AB</option>
				<option value="O" <?=$selected["O"]?>>O</option>
				
				</select>
			</td>
		</tr>	
		<td>Rh. Darah Pasien</td>
			<td class="input">
				<? $type=$pasien[rhesus];
				$selected[$type]="selected";?>
				<select name="rhpsn">
				<option value="+" <?=$selected["+"]?>>+</option>
				<option value="-" <?=$selected["-"]?>>-</option>
				</select>
			</td>
			<td>Rh. Darah Diminta</td>
			<td class="input">
				<? $type=$check_d1[Rhesus];
				$selected[$type]="selected";?>
				<select name="rhmnt">
				<option value="+" <?=$selected["+"]?>>+</option>
				<option value="-" <?=$selected["-"]?>>-</option>
				</select>
			</td>
		</tr>	

		<!--tr>
			<td>Rh Darah Pasien</td><td class="input"><?=$pasien[rhesus].'('.$pasien[rhesus].')'?></td>
			<td>Gol. Darah Diminta</td><td class="input"><?=$check_d1[GolDarah]?></td>
		</tr-->
			<input name="goldarah" type=hidden value="<?=$check_d1[GolDarah]?>">
			<input name="layanan" type=hidden value="<?=$check_h1[jenis]?>">
			<input name="rhesus" type=hidden value="<?=$check_d1[Rhesus]?>">		
		<tr>
			<td>Kelas</td><td class="input"><?=$check_h1[kelas]?></td>
			<td></td>
		</tr>
		<tr>
			<td>Sudah Crossmatch</td><td class="input"><?=$check_cross?> Ktg OK,  <?=$check_batal?> Ktg Incompatible</td>
			<td>Jenis Komponen</td>
				<td class="input">
				      	<?
              				$query = "SELECT `ID`,`JenisDarah`,Jumlah FROM `dtranspermintaan` WHERE `NoForm`='$check_h1[noform]'";
							$hasil = mysql_query($query);
							while ($data=mysql_fetch_assoc($hasil)){ ?>
								|| <b><?=$data[JenisDarah]?>(<?=$data[Jumlah]?>)</b> ||<?
							}?></td>
		</tr>
		<tr>
			<td>Total Permintaan</td><td class="input"><?=$check_d1[Jumlah]-$check_d1[JTitip]?></td>
			<td>Titip</td><td class="input"><?=$check_d1[JTitip]?></td></tr>
		<tr>
			<td><b>Masukan No. Kantong</b></td>
				<td class="input"><input name="NoKantong" type="text" id="nokantong" onkeydown="chang(event,this);" onchange="focus(document.periksa.NoKantong);cari_kantong('box-table-b');"/> Jika diketik manual Tekan ENTER</td>
			<td>HB</td><td class="input"><?=$check_h1[hb]?></td>
		</tr>
	</table>
	
	<TABLE class="list" id="box-table-b">
		<tr class="field">
			<td rowspan='2'>Kantong</td>
			<td rowspan='2'>Aftap</td>
			<td rowspan='2'>Jenis</td>
			<td rowspan='2'>Gol</td>
			<td rowspan='2'>Rh</td>
			<td rowspan='2'>Metode</td>
			<td rowspan='2'>Status Crossmatch</td>
			<td rowspan='2'>Hasil Crossmatch</td>
			<td rowspan='2'>Ket</td>
			<td rowspan='2'>Aglutinasi<br>GEL TEST</td>
			<td rowspan='2'>No Listcomb</td>
			<td colspan='3'>Fase</td>
			<td rowspan='2'>Ket Keluar</td> </tr>
			<tr class="field">
			<td>I</td>
			<td>II</td>
			<td>III</td>
			</tr>
	</TABLE>
<tr>
</tr>
<table class="form" border=0 cellspacing=1 cellpadding=2 >
	<tr>
		<td>Dikerjakan Oleh</td>
		<td class="input"><select name="sah2" > <?
				$user2="select * from user where multi like '%laboratorium%' order by nama_lengkap ASC";
                $do2=mysql_query($user2);
					while($data2=mysql_fetch_assoc($do2)) {
						$select2=""; ?>
						<option value="<?=$data2[id_user]?>"<?=$select2?>><?=$data2[nama_lengkap]?></option>
				<? } ?>
			</select></td>
		<td>Dicek Oleh</td>
		<td class="input"><select name="sah1" > <?
			$user1="select * from user where multi like '%laboratorium%' order by nama_lengkap ASC";
            $do1=mysql_query($user1);
			while($data1=mysql_fetch_assoc($do1)) {
				$select1=""; ?>
				<option value="<?=$data1[id_user]?>"<?=$select1?>><?=$data1[nama_lengkap]?></option>
				<? } ?>
			</select></td>
		<td>Disahkan Oleh</td>
		<td class="input"><select name="sah" > <?
				$user="select * from dokter_periksa";
                $do=mysql_query($user);
					while($data=mysql_fetch_assoc($do)) {
						$select=""; ?>
						<option value="<?=$data[Nama]?>"<?=$select?>>
						<?=$data[Nama]?>
						</option>
				<? } ?>
			</select></td>
	</tr>
	<tr><td>Tempat</td>
		<td class="input">
			<select name="tempat">
				<option value="UDD">UDD</option>
				<option value="BDRS">BDRS</option>
				<option value="BDRS2">BDRS2</option>
				</select></td>

				<td>Shift</td>
				<td class="styled-select" bgcolor="#ffa688">
					<? $s1='';$s2='';$s3='';$s4='';
						$waktu=date('H:i:s');
						$jam1=mysql_fetch_assoc(mysql_query("select * from shift where nama='I'"));
						$jam2=mysql_fetch_assoc(mysql_query("select * from shift where nama='II'"));	
						$jam3=mysql_fetch_assoc(mysql_query("select * from shift where nama='III'"));
						$jam4=mysql_fetch_assoc(mysql_query("select * from shift where nama='IV'"));
						
						$sh1=$jam1[jam]; $sh2=$jam2[jam]; $sh3=$jam3[jam];$sh4=$jam4[jam];
						if ($waktu >= $sh1 ){ $s1='selected';}
						if ($waktu >= $sh2 ){ $s2='selected';}
						if ($waktu >= $sh3 ){ $s3='selected';}
						if ($waktu < $sh1 ){ $s4='selected';}
					?>
					<select name="shift">
						<option value="1" <?=$s1?>>SHIFT I</option>
						<option value="2" <?=$s2?>>SHIFT II</option>
						<option value="3" <?=$s3?>>SHIFT III</option>
						<option value="4" <?=$s4?>>SHIFT IV</option>
						
					</select></td>  
			</tr>
</table>
<input name="periksa" type=hidden value="1">
<input name="norm" type=hidden value="<?=$norm?>">

<input type="submit" name="submit1" value="Simpan" title="Proses kantong" class="swn_button_red">
</form>
<?}?>
</body>

<div class="alert" id="alert">
	<div id="kantong_tdk_sesuai" title="Kantong tidak sesuai..!">
		<p>Silahkan cek kembali kantong yang anda masukkan, atau masukkan kantong lain</p>
	</div>
	<div id="kantong_sudah_diinput" title="Kantong sudah diinput..!">
		<p>Silahkan masukkan kantong yang lain</p>
	</div>
	<div id="konfirmasi" title="Golongan darah tidak sama..!">
		<p>Golongan darah tidak sama dengan pasien. Apakah anda yakin?</p>
	</div>
	<div id="gol_darah_tdk_sesuai" title="Golongan darah tidak sama..!">
		<p>Golongan darah tidak sama dengan pasien. Apakah anda yakin?</p>
	</div>
	<div id="kantong_terpenuhi" title="Permintaan terpenuhi..!">
		<p>Jumlah Kantong Sudah terpenuhi!!!</p>
	</div>
</div>
