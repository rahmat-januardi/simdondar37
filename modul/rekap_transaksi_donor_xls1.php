<?
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_transaksi_darah_kirimbagian.xls");
header("Pragma: no-cache");
header("Expires: 0");

include('../config/db_connect.php');




$namauser = $_POST[namauser];
$today      	= $_POST[today];
$today1     	= $_POST[today1];
$src_nomorf   	= $_POST[instansi];
$src_status   	= $_POST[status];
$src_ambil    	= $_POST[ambil];
$src_shift    	= $_POST[shift];
$src_ktg      	= $_POST[ktg];
$src_drh      	= $_POST[drh];
$src_jk       	= $_POST[jk];
$src_rh      	= $_POST[rh];
$src_ds       	= $_POST[ds];
$src_baru       = $_POST[baru];
$src_ketbatal	= $_POST[ketbatal];
$hasil		= $_POST[hasil];

$tempatmu = "";
if ($hasil == "M") $tempatmu = "MOBILE UNIT";
?>
<h2 colspan=5 align="center">REKAP TRANSAKSI DONOR <?= $tempatmu ?></h2>
<h2 colspan=5 align="center"><?= $src_nomorf ?></h2>
<h3 colspan=5 align="center">Tanggal : <?= $today ?> s/d <?= $today1 ?></h1>
	<?
	$whereStatus = "";
	if (isset($_POST['status']) && $_POST['status'] !== "") {
		$status = $_POST['status'];

		switch ($status) {
			case "0":
				$whereStatus = "AND COALESCE(Pengambilan, '') = '0'";
				break;
			case "1":
				$whereStatus = "AND COALESCE(Pengambilan, '') = '1'";
				break;
			case "2":
				$whereStatus = "AND (COALESCE(Pengambilan, '') = '2' OR jumHB IN ('2','3','4'))";
				break;
			case "3":
				$whereStatus = "AND COALESCE(Pengambilan, '') = '3' AND jumHB = '1'";
				break;
			case "4":
				$whereStatus = "AND jumHB IN ('2','3','4')";
				break;
		}
	}

	$whereKetbatal = "";
	if (isset($src_ketbatal) && $src_ketbatal !== "") {
		$whereKetbatal = " AND COALESCE(ketBatal,'') = '$src_ketbatal' ";
	}

	$transaksipermintaan = mysql_query("select * from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' $whereStatus and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal order by gol_darah ASC ");
	?>

	<?
	$countp = mysql_num_rows($transaksipermintaan);

	echo "Sejumlah :  ";
	echo "<b>";
	echo $countp;
	echo "</b>";
	echo " data";
	?>
	<table border=1 cellpadding=5 cellspacing=1 style="border-collapse:collapse" width="100%">
		<tr>
			<!--th colspan=12><b>Total = <? $TRec ?> Kantong</b></th></tr><tr class="field"-->
			<td align="center">No</td>
			<td align="center">No<br>Kantong</td>
			<td align="center">Jenis</td>
			<td align="center">Gol<br>(Rh)</td>
			<td align="center">Penge-<br>sahan</td>
			<td align="center">Jam Entry</td>
			<td align="center">Durasi<br>Pengambilan</td>
			<td align="center">Status</td>
			<td align="center">Shift</td>



		</tr>


		<?
		$no = 1;
		while ($datatransaksipermintaan = mysql_fetch_array($transaksipermintaan)) {
		?>


			<tr>
				<td align="center"><?= $no ?></td>
				<!--td align="center"><?= $datatransaksipermintaan['NoTrans'] ?></td>
	<td align="center"><?= $datatransaksipermintaan['Tgl'] ?></td-->
				<?
				$kantong0 = mysql_query("select * from stokkantong where noKantong='$datatransaksipermintaan[NoKantong]'");
				$kantong = mysql_fetch_array($kantong0);
				$pendonor0 = mysql_query("select * from pendonor where Kode='$datatransaksipermintaan[KodePendonor]'");
				$pendonor = mysql_fetch_array($pendonor0);
				$jamantri = substr($datatransaksipermintaan[Tgl], 11);
				$jamaftap = substr($kantong[tgl_Aftap], 11);
				if ($datatransaksipermintaan[jk] == 0) $jk = 'Pria';
				if ($datatransaksipermintaan[jk] == 1) $jk = 'Wanita';
				$peng = 'Antri';
				if ($datatransaksipermintaan[jumHB] == '1') $peng = 'Lolos MCU';
				if ($datatransaksipermintaan[jumHB] == '2') $peng = 'Gagal MCU';
				if ($datatransaksipermintaan[jumHB] == '3') $peng = 'Gagal MCU';
				if ($datatransaksipermintaan[jumHB] == '4') $peng = 'Gagal MCU';
				if ($datatransaksipermintaan[Pengambilan] == '0') $peng = 'OK';
				if ($datatransaksipermintaan[Pengambilan] == '2') $peng = 'Gagal';
				if ($datatransaksipermintaan[Pengambilan] == '1') $peng = 'Batal';

				if ($datatransaksipermintaan[caraAmbil] == '0') $caraambil = 'Biasa';
				if ($datatransaksipermintaan[caraAmbil] == '1') $caraambil = 'Tromboferesis';
				if ($datatransaksipermintaan[caraAmbil] == '2') $caraambil = 'Leukaferesis';
				if ($datatransaksipermintaan[caraAmbil] == '3') $caraambil = 'Plasmaferesis';
				if ($datatransaksipermintaan[caraAmbil] == '4') $caraambil = 'Eritoferesis';

				if ($datatransaksipermintaan[JenisDonor] == '0') $ds = 'DS';
				if ($datatransaksipermintaan[JenisDonor] == '1') $ds = 'DP';
				if ($datatransaksipermintaan[JenisDonor] == '2') $ds = 'Autologus';

				if ($datatransaksipermintaan[donorbaru] == '0') $baru = 'Baru';
				if ($datatransaksipermintaan[donorbaru] == '1') $baru = 'Ulang';

				switch ($datatransaksipermintaan['metoda']) {
					//            case "BS":  $metkantong ="BIASA";        break;
					//            case "FT":  $metkantong ="FILTER";       break;
					case "TTB":
						$metkantong = "TOP & TOP (Biasa)";
						break;
					case "TTF":
						$metkantong = "TOP & TOP (Filter)";
						break;
					case "TBB":
						$metkantong = "TOP & BOTTOM (Biasa)";
						break;
					case "TBF":
						$metkantong = "TOP & BOTTOM (Filter)";
						break;
				}

				switch ($datatransaksipermintaan['jeniskantong']) {
					case '1':
						$jenis = 'Single';
						break;
					case '2':
						$jenis = 'Double';
						break;
					case '3':
						$jenis = 'Triple';
						break;
					case '4':
						$jenis = 'Quadruple (' . $metkantong . ')';
						break;
					case '6':
						$jenis = 'Pediatrik';
						break;
					default:
						$jenis = '';
				}

				if ($kantong[sah] == '0') $sah1 = 'Belum';
				if ($kantong[sah] == 0 and $datatransaksipermintaan[NoKantong] == NULL) $sah1 = '-';
				if ($kantong[sah] == '1') $sah1 = 'Sudah';
				?>

				<!--td align="left"><?= $pendonor['Nama'] ?></td>
	<td align="center"><?= $pendonor['Alamat'] ?></td>
	<td align="center"><?= $pendonor['telp2'] ?></td>
	<td align="center"><?= $datatransaksipermintaan['umur'] ?></td>
	
	<td align="center"><?= $jk ?></td>
	<td align="center"><?= $ds ?></td>
	<td align="center"><?= $baru ?></td>
	<td align="center"><?= $datatransaksipermintaan['donorke'] ?></td>
	<td align="center"><?= $jamantri ?></td-->


				<td align="center"><?= $datatransaksipermintaan['NoKantong'] ?></td>
				<td align="center"><?= $jenis ?></td>
				<td align="center"><?= $datatransaksipermintaan['gol_darah'] ?><?= $datatransaksipermintaan['rhesus'] ?>
				</td>
				<!--? echo "<td align=center><a href=pmi".$_SESSION[leveluser].".php?module=updatekantong&noKantong=".$datatransaksipermintaan[NoKantong].">".$datatransaksipermintaan[NoKantong]."</a></td>"; ?-->
				<td align="center"><?= $sah1 ?></td>
				<td align="center"><?= $jamaftap ?></td>
				<td align="center"><?= $kantong[lama_pengambilan] ?> Menit</td>
				<td align="center"><?= $peng ?></td>
				<td align="center"><?= $datatransaksipermintaan['shift'] ?></td>


			</tr>
		<? $no++;
		} ?>
	</table>

	<?
	$jum = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as kod from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' 
and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal "));

	//PENGAMBILAN BERHASIL DG

	$golA = mysql_fetch_assoc(mysql_query("select count(gol_darah) as A from htransaksi where  CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='A' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal "));
	$golB = mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='B' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$golAB = mysql_fetch_assoc(mysql_query("select count(gol_darah) as AB from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='AB' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$golO = mysql_fetch_assoc(mysql_query("select count(gol_darah) as O from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='O'  and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$golx = mysql_fetch_assoc(mysql_query("select count(gol_darah) as x from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and gol_darah='X' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$jkP = mysql_fetch_assoc(mysql_query("select count(jk) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and jk='0' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$jkW = mysql_fetch_assoc(mysql_query("select count(jk) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and jk='1' and Pengambilan='0' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));


	//baru
	$rhposA = mysql_fetch_assoc(mysql_query("select count(gol_darah) as A from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='A' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$rhposB = mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='B' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$rhposAB = mysql_fetch_assoc(mysql_query("select count(gol_darah) as AB from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='AB' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$rhposO = mysql_fetch_assoc(mysql_query("select count(gol_darah) as O from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='O' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));
	$rhposx = mysql_fetch_assoc(mysql_query("select count(gol_darah) as x from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and gol_darah='x' and rhesus='+' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' $whereKetbatal"));


	$rhnegA = mysql_fetch_assoc(mysql_query("select count(gol_darah) as A from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='A' and rhesus='-' and NoTrans like 'DG%'  $whereKetbatal"));
	$rhnegB = mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='B' and rhesus='-'  $whereKetbatal"));
	$rhnegAB = mysql_fetch_assoc(mysql_query("select count(gol_darah) as AB from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='AB' and rhesus='-'  $whereKetbatal"));
	$rhnegO = mysql_fetch_assoc(mysql_query("select count(gol_darah) as O from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='O' and rhesus='-'  $whereKetbatal"));
	$rhnegx = mysql_fetch_assoc(mysql_query("select count(gol_darah) as x from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and gol_darah='x' and rhesus='-' $whereKetbatal "));

	$rhposP = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jk='0' and rhesus='+' $whereKetbatal"));
	$rhposW = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jk='1' and rhesus='+' $whereKetbatal"));
	$rhnegP = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jk='0' and rhesus='-' $whereKetbatal"));
	$rhnegW = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jk='1' and rhesus='-' $whereKetbatal"));

	$rhpos = mysql_fetch_assoc(mysql_query("select count(rhesus) as pos from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and rhesus='+' $whereKetbatal "));
	$rhneg = mysql_fetch_assoc(mysql_query("select count(rhesus) as neg from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and rhesus='-'  $whereKetbatal"));


	//CARA PENGAMBILAN
	$biasa = mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='0' $whereKetbatal"));
	$tromboferesis = mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='1' $whereKetbatal"));
	$leukoferesis = mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='2' $whereKetbatal"));
	$plasmaferesis = mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='3' $whereKetbatal"));
	$eritoferesis = mysql_fetch_assoc(mysql_query("select count(kodependonor) as kod from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan like '%$src_status%' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and caraAmbil='4' $whereKetbatal"));

	/*
$dspos=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan='0' and JenisDonor='0' and rhesus='+' and NoTrans like 'DG%'"));
$dsneg=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan='0' and JenisDonor='0' and rhesus='-' and NoTrans like 'DG%'"));
$dppos=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan='0' and JenisDonor='1' and rhesus='+' and NoTrans like 'DG%'"));
$dpneg=mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and Pengambilan='0' and JenisDonor='1' and rhesus='-' and NoTrans like 'DG%'"));

*/
	//Jenis Kantong DG

	$single = mysql_fetch_assoc(mysql_query("select count(kodependonor) as S from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='1' $whereKetbatal"));
	$double = mysql_fetch_assoc(mysql_query("select count(kodependonor) as D from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='2' $whereKetbatal"));
	$triple = mysql_fetch_assoc(mysql_query("select count(kodependonor) as T from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='3'$whereKetbatal "));
	$quadruple = mysql_fetch_assoc(mysql_query("select count(kodependonor) as Q from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='4'$whereKetbatal "));
	$pediatrik = mysql_fetch_assoc(mysql_query("select count(kodependonor) as P from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and jeniskantong='6' $whereKetbatal"));

	/*
$single_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as S from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='1' and Pengambilan like '%$src_status%' "));
$double_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as D from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='2' and Pengambilan like '%$src_status%' "));
$triple_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as T from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%' and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='3' and Pengambilan like '%$src_status%' "));
$quadruple_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as Q from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='4' and Pengambilan like '%$src_status%' "));
$pediatrik_g=mysql_fetch_assoc(mysql_query("select count(kodependonor) as P from htransaksi  where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and jeniskantong='6' and Pengambilan like '%$src_status%' "));*/

	//BARU dan ULANG MU
	$db = mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%'  and donorbaru='0'$whereKetbatal"));
	$du = mysql_fetch_assoc(mysql_query("select count(gol_darah) as U from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%'  and donorbaru='1'$whereKetbatal"));

	$db_g = mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%'  and donorbaru='0'$whereKetbatal"));
	$du_g = mysql_fetch_assoc(mysql_query("select count(gol_darah) as U from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and donorbaru='1'$whereKetbatal"));

	$db_b = mysql_fetch_assoc(mysql_query("select count(gol_darah) as B from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and donorbaru='0'$whereKetbatal"));
	$du_b = mysql_fetch_assoc(mysql_query("select count(gol_darah) as U from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and donorbaru='1'$whereKetbatal"));

	//DS dan DP
	$ds = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='0' $whereKetbatal"));
	$dsb = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='0' $whereKetbatal"));
	$dsg = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='0'  $whereKetbatal"));

	$dp = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='1' $whereKetbatal "));
	$dpb = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='1'  $whereKetbatal"));
	$dpg = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as W from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and JenisDonor='1' $whereKetbatal "));

	//DG dan MU
	//DS dan DP
	$dg = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and notrans like 'DG%' $whereKetbatal"));

	$mu = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and notrans like 'M%'$whereKetbatal "));

	$mu_m = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and notrans like 'M%' and kendaraan='0'$whereKetbatal "));

	$mu_b = mysql_fetch_assoc(mysql_query("select count(KodePendonor) as P from htransaksi where CAST(Tgl as date)>='$today' and CAST(Tgl as date)<='$today1' and tempat like '%$hasil%' and shift like '%$src_shift%' and caraambil like '%$src_ambil%' and jeniskantong like '%$src_ktg%' and gol_darah like '%$src_drh%' and jk like '%$src_jk%' and Instansi like '%$src_nomorf%'  and rhesus like '%$src_rh%' and JenisDonor like '%$src_ds%' and donorbaru like '%$src_baru%' and Pengambilan like '%$src_status%' and notrans like 'M%' and kendaraan='1' $whereKetbatal"));

	?>
	<br>
	<td>
		<table>
			<tr>

				<td>
					<table class=form border=1 cellpadding=0 cellspacing=0>
						<th colspan=3><b>Rekap AFTAP </b></th>
						<tr class="field">
							<td><b>Gol<br>Darah </b></td>
							<td><b>JML </b></td>
							<td><b>Rhesus </b></td>
						</tr>
						<tr>
							<td><b> A </td>
							<td class=input><?= $golA[A] ?></td>
							<td>Pos: <?= $rhposA[A] ?> Neg: <?= $rhnegA[A] ?></td>
						</tr>
						<tr>
							<td><b> B </td>
							<td class=input><?= $golB[B] ?></td>
							<td>Pos: <?= $rhposB[B] ?> Neg: <?= $rhnegB[B] ?></td>
						</tr>
						<tr>
							<td><b> AB </td>
							<td class=input><?= $golAB[AB] ?></td>
							<td>Pos: <?= $rhposAB[AB] ?> Neg: <?= $rhnegAB[AB] ?></td>
						</tr>
						<tr>
							<td><b> O </td>
							<td class=input><?= $golO[O] ?></td>
							<td>Pos: <?= $rhposO[O] ?> Neg: <?= $rhnegO[O] ?></td>
						</tr>
						<tr>
							<td><b> X </td>
							<td class=input><?= $golx[x] ?></td>
							<td>Pos: <?= $rhposx[x] ?> Neg: <?= $rhnegx[x] ?>
						</tr>
						<tr>
							<td>Pria</td>
							<td class=input><?= $jkP[P] ?></td>
							<td>Pos: <?= $rhposP[P] ?> Neg: <?= $rhnegP[P] ?></td>
						</tr>
						<tr>
							<td>Wanita</td>
							<td class=input><?= $jkW[W] ?></td>
							<td>Pos: <?= $rhposW[W] ?> Neg: <?= $rhnegW[W] ?></td>
						</tr>
						<tr>
							<td><b>JML TTL</b></td>
							<td><b><?= $jum[kod] ?></td>
							<td><b>Pos: <?= $rhpos[pos] ?> Neg: <?= $rhneg[neg] ?></td>
						</tr>
					</table>
				</td>

				<td>
					<table class=form border=1 cellpadding=0 cellspacing=0>
						<th colspan=2><b>Rekap AFTAP <br>Dalam Gedung</th>
						<tr class="field">
							<td><b>Cara<br>Ambil</b></td>
							<td><b>Jumlah </b></td>
						</tr>
						<tr>
							<td> Biasa </td>
							<td class=input><?= $biasa[kod] ?></td>
						</tr>
						<th colspan='2'>AFERESIS</th>
						<tr>
							<td>PRC</td>
							<td class=input><?= $eritoferesis[kod] ?></td>
						</tr>
						<tr>
							<td>PLASMA</td>
							<td class=input><?= $plasmaferesis[kod] ?></td>
						</tr>
						<tr>
							<td>TC</td>
							<td class=input><?= $tromboferesis[kod] ?></td>
						</tr>
						<!--tr><td> Leukaferesis </td>
<td class=input><?= $leukoferesis[kod] ?></td></tr-->
						<tr>
							<td>JML TTL</td>
							<td class=input>
								<?= $eritoferesis[kod] + $plasmaferesis[kod] + $leukoferesis[kod] + $tromboferesis[kod] + $biasa[kod] ?>
							</td>
						</tr>
					</table>
				</td>

				<td>
					<table class=form border=1 cellpadding=0 cellspacing=0>
						<th colspan=2><b>Rekap <br>Jenis Kantong</b></th>
						<tr class="field">
							<td><b>Jenis Kantong</b></td>
							<td><b>Jml </b></td>
						</tr>

						<!--th><b>Berhasil</b></th><th><b>Gagal Aftap</b></th-->

						<tr>
							<td>Single</td>
							<td class=input><?= $single[S] ?></td>
						</tr>
						<tr>
							<td>Double</td>
							<td class=input><?= $double[D] ?></td>
						</tr>
						<tr>
							<td>Triple</td>
							<td class=input><?= $triple[T] ?></td>
						</tr>
						<tr>
							<td>Quadruple</td>
							<td class=input><?= $quadruple[Q] ?></td>
						</tr>
						<tr>
							<td>Pediatrik</td>
							<td class=input><?= $pediatrik[P] ?></td>
						</tr>
						<tr>
							<td>JML TTL</td>
							<td class=input><?= $single[S] + $double[D] + $triple[T] + $quadruple[Q] + $pediatrik[P] ?>
							</td>
						</tr>
					</table>
				</td>

			</tr>
		</table>

		<br>
		</br>
		<table>
			<tr>
				<td>
					<table class=form border=1 cellpadding=0 cellspacing=0>
						<th colspan=2><b>Rekap<br> Jenis <br>Pendonor</b></th>
						<tr class="field">
							<td><b>Jenis</b></td>
							<td><b>Jumlah </b></td>
						</tr>

						<tr>
							<td>Baru</td>
							<td class=input><?= $db[B] ?></td>
						</tr>
						<tr>
							<td>Ulang</td>
							<td class=input><?= $du[U] ?></td>
						</tr>
						<tr>
							<td>DS</td>
							<td class=input><?= $ds[P] ?></td>
						</tr>
						<tr>
							<td>DP</td>
							<td class=input><?= $dp[W] ?></td>
						<tr>
							<td>JML</td>
							<td class=input><?= $db[B] + $du[U] ?></td>
						</tr>
					</table>
				</td>

				<td>
					<table class=form border=1 cellpadding=0 cellspacing=0>
						<th colspan=4><b>Rekap <br>TEMPAT AFTAP</b></th>
						<tr class="field">
							<td rowspan='2'><b>Tempat <br>Donor</b></td>
							<th class="field" colspan='3'><b>Jumlah Aftap</b></th>
						</tr>
						<th><b>Jumlah</b></th>
						<th><b>Mobil</b></th>
						<th><b>Bus Donor</b></th>

						<tr>
							<td>DG</td>
							<td class=input align='center'><?= $dg[P] ?></td>
							<td class=input><?= '-' ?></td>
							<td class=input><?= '-' ?></td>
						</tr>
						<tr>
							<td>MU</td>
							<td class=input><?= $mu[P] ?></td>
							<td class=input><?= $mu_m[P] ?></td>
							<td class=input><?= $mu_b[P] ?></td>
						</tr>
						<!--tr><td>Mobil Donor</td>
<td class=input><?= $ds[P] ?></td><td class=input><?= $dsg[P] ?></td><td class=input><?= $dsb[P] ?></td></tr>
<tr><td>BUS Donor</td>
<td class=input><?= $dp[W] ?><td class=input><?= $dpg[W] ?></td><td class=input><?= $dpb[W] ?></td-->
						<tr>
							<td>JML TTL</td>
							<td class=input><?= $dg[P] + $mu[P] ?></td>
							<td class=input><?= $mu_m[P] ?></td>
							<td class=input><?= $mu_b[P] ?></td>
						</tr>
					</table>
				</td>


			</tr>
		</table>

		<table>

			<?
			$udd = mysql_fetch_assoc(mysql_query("select nama from utd where down='1' and aktif='1'"));
			?>
			<tr>
				<td></td>
				<td colspan="4" align="center"><?= $udd[nama] ?>, <?= $today ?></td>
			</tr>
			<tr>
				<td></td>
				<td align="center">Yang Menerima</td>
				<td></td>
				<td></td>
				<td align="center">Yang Menyerahkan</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td align="center">(.......................)</td>
				<td></td>
				<td></td>
				<td align="center">(
					<? echo $namauser; ?>)
				</td>
			</tr>
		</table>

		<?
		mysql_close();
		?>