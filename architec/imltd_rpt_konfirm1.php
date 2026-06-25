<?php
/*
Yudha T. Putra
*/

require('../tcpdf/tcpdf.php');
$link = mysql_connect('localhost', 'root', 'F201603907');
mysql_select_db('pmi');
$notrans 	= $_GET['notrans'];
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{

	//Page header
	public function Header()
	{
		$udd = mysql_fetch_assoc(mysql_query("SELECT `nama` FROM `utd` WHERE `aktif`='1'"));
		$namautd = $udd['nama'];
		$headerData = $this->getHeaderData();
		$this->SetFont('helvetica', 'B', 9);
		$this->Cell(0, 10, '' . $namautd . '', 0, false, 'L', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', '', 9);
		$this->Cell(0, 10, 'HASIL PEMERIKSAAN IMLTD No: ' . $_GET[notrans], 0, false, 'R', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', 'B', 9);
		$this->writeHTML($headerData['string']);
	}

	// Page footer
	public function Footer()
	{
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', '', 8);
		// Page number
		//$this->Cell(0, 10, 'No.Dok.:UTD BALI-UJS-L3-001', 0, false, 'L', 0, '', 0, false, 'T', 'M');
		//$this->Cell(0, 10, 'No.Dok.:UTD BALI-UJS-L3-001', 0, false, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 10, 'Hal: ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
}
// create new PDF document
//$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf = new MYPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->setHeaderData(
	$ln = '',
	$lw = 0,
	$ht = '',
	$hs = '',
	$tc = array(0, 0, 0),
	$lc = array(0, 0, 0)
);


// set document information
$pdf->SetTitle('Hasil Konfirmasi IMLTD - SIMDONDAR');
$pdf->SetSubject('Hasil Konfirmasi IMLTD - SIMDONDAR');

// set margins
$pdf->SetMargins(10, 15, 10, true);
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// add a page
$pdf->AddPage();

$sql = mysql_query("SELECT `id`, `no_trans`, `instr`, `instr_v`, `scc_serial`, `interface_sn`, `arc_serial`, `trans_time`, `user`,
		 `id_tes`, `carrier`, `position`,
		 `b_lot_reag`, `b_id_raw`, `b_ed_reag`, `b_kode_reag`, `b_sn_reag`, `b_abs`, `b_run_time`, `b_hasil`, `b_ket_tes`,
		 `c_lot_reag`, `c_id_raw`, `c_ed_reag`, `c_kode_reag`, `c_sn_reag`, `c_abs`, `c_run_time`, `c_hasil`, `c_ket_tes`,
		 `i_lot_reag`, `i_id_raw`, `i_ed_reag`, `i_kode_reag`, `i_sn_reag`, `i_abs`, `i_run_time`, `i_hasil`, `i_ket_tes`,
		 `s_lot_reag`, `s_id_raw`, `s_ed_reag`, `s_kode_reag`, `s_sn_reag`, `s_abs`, `s_run_time`, `s_hasil`, `s_ket_tes`,
		 `konfirmer`, `koonfirm_time`, `disahkan`, `status_kantong`, `konfirm_action` FROM `imltd_arc_konfirm` WHERE no_trans='$notrans'");
//$pdf->SetFont('helvetica', 'B', 14);
//$pdf->Write(0, 'DATA KONFIRMASI PEMERIKSAAN IMLTD', '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 9);

$tbl = <<<EOD
        <table border="0.1" cellpadding="3" cellspacing="0">
        <tr style="background-color:Silver">
            <td rowspan="2" align="center" valign="middle" width="25px">No</td>
            <td rowspan="2" align="center" valign="middle" width="75px">Sample<br>ID</td>
            <td colspan="4" align="center" valign="middle">HBsAg</td>
            <td colspan="4" align="center" valign="middle">HCV</td>
            <td colspan="4" align="center" valign="middle">HIV</td>
            <td colspan="4" align="center" valign="middle">Syphilis</td>
            <td rowspan="2" align="center" valign="middle">Status<br>Kantong</td>
            <td rowspan="2" align="center" valign="middle">Konfirm<br>user</td>
        </tr>
        <tr style="background-color:Silver">
            <td align="center">OD</td><td align="center">Hasil</td><td align="center">Run Time</td><td align="center">Ket</td>
            <td align="center">OD</td><td align="center">Hasil</td><td align="center">Run Time</td><td align="center">Ket</td>
            <td align="center">OD</td><td align="center">Hasil</td><td align="center">Run Time</td><td align="center">Ket</td>
            <td align="center">OD</td><td align="center">Hasil</td><td align="center">Run Time</td><td align="center">Ket</td>
        </tr>

EOD;


$no = 1;
while ($isi = mysql_fetch_array($sql)) {
	$color		= '0';
	$nomor      = $isi['no_trans'];
	$operator   = $isi['user'];
	$konfirmer  = $isi['konfirmer'];
	$pengesah   = $isi['disahkan'];
	$waktuKonfirmiasi = $isi['koonfirm_time'];
	$instrument = $isi['instr'] . ' - S/N:' . $isi['arc_serial'];
	if ($isi[b_lot_reag] !== "") {
		$reag_b     = 'Lot.:' . $isi['b_lot_reag'] . ', ED.:' . $isi['b_ed_reag'];
	}
	if ($isi[c_lot_reag] !== "") {
		$reag_c     = 'Lot.:' . $isi['c_lot_reag'] . ', ED.:' . $isi['c_ed_reag'];
	}
	if ($isi[i_lot_reag] !== "") {
		$reag_i     = 'Lot.:' . $isi['i_lot_reag'] . ', ED.:' . $isi['i_ed_reag'];
	}
	if ($isi[s_lot_reag] !== "") {
		$reag_s     = 'Lot.:' . $isi['s_lot_reag'] . ', ED.:' . $isi['s_ed_reag'];
	}
	// Format Run Time agar tampil 2 baris (tanggal & jam) supaya kolom tidak melebar
	$b_run_disp = $isi['b_run_time'] !== '' ? str_replace(' ', '<br>', $isi['b_run_time']) : '';
	$c_run_disp = $isi['c_run_time'] !== '' ? str_replace(' ', '<br>', $isi['c_run_time']) : '';
	$i_run_disp = $isi['i_run_time'] !== '' ? str_replace(' ', '<br>', $isi['i_run_time']) : '';
	$s_run_disp = $isi['s_run_time'] !== '' ? str_replace(' ', '<br>', $isi['s_run_time']) : '';
	//Status Kantong Saat Konfirmasi
	switch ($isi['status_kantong']) {
		case '0':
			$statuskantong = 'Kosong';
			break;
		case '1':
			$statuskantong = 'Karantina';
			break;
		case '2':
			$statuskantong = 'Sehat';
			break;
		case '3':
			$statuskantong = 'Keluar';
			break;
		case '4':
			$statuskantong = 'Rusak';
			break;
		case '5':
			$statuskantong = 'Gagal';
			break;
		case '6':
			$statuskantong = 'Musnah';
			break;
		default:
			$statuskantong = '-';
	}
	//Aksi User saat konfirmasi
	switch ($isi['konfirm_action']) {
		case "0":
			$aksik = '-';
			break;
		case "1":
			$aksik = 'Sehat';
			break;
		case "2":
			$aksik = 'Cekal';
			break;
		case "3":
			$aksik = 'Tunda';
			break;
		default:
			$aksik = "";
	}
	//Hasil
	switch ($isi['b_hasil']) {
		case "0":
			$hasilb = "NR";
			break;
		case "1":
			$hasilb = "R";
			$color = '1';
			break;
		case "2":
			$hasilb = "GZ";
			$color = '1';
			break;
		default:
			$hasilb = "";
	}
	switch ($isi['c_hasil']) {
		case "0":
			$hasilc = "NR";
			break;
		case "1":
			$hasilc = "R";
			$color = '1';
			break;
		case "2":
			$hasilc = "GZ";
			$color = '1';
			break;
		default:
			$hasilc = "";
	}
	switch ($isi['i_hasil']) {
		case "0":
			$hasili = "NR";
			break;
		case "1":
			$hasili = "R";
			$color = '1';
			break;
		case "2":
			$hasili = "GZ";
			$color = '1';
			break;
		default:
			$hasili = "";
	}
	switch ($isi['s_hasil']) {
		case "0":
			$hasils = "NR";
			break;
		case "1":
			$hasils = "R";
			$color = '1';
			break;
		case "2":
			$hasils = "GZ";
			$color = '1';
			break;
		default:
			$hasils = "";
	}
	if ($color == '0') {
		$tbl .= '
					<tr>
						<td align="right" width="25px">' . $no . '. ' . '</td>
						<td width="75px">' . $isi[id_tes] . '</td>
						<td align="right">' . $isi[b_abs] . '</td>
						<td align="center">' . $hasilb . '</td>
						<td align="center" style="font-size:15px">' . $b_run_disp . '</td>
						<td align="center">' . $isi[b_ket_tes] . '</td>
						<td align="right">' . $isi[c_abs] . '</td>
						<td align="center">' . $hasilc . '</td>
						<td align="center" style="font-size:15px">' . $c_run_disp . '</td>
						<td align="center">' . $isi[c_ket_tes] . '</td>
						<td align="right">' . $isi[i_abs] . '</td>
						<td align="center">' . $hasili . '</td>
						<td align="center" style="font-size:15px">' . $i_run_disp . '</td>
						<td align="center">' . $isi[i_ket_tes] . '</td>
						<td align="right">' . $isi[s_abs] . '</td>
						<td align="center">' . $hasils . '</td>
						<td align="center" style="font-size:15px">' . $s_run_disp . '</td>
						<td align="center">' . $isi[s_ket_tes] . '</td>
						<td>' . $statuskantong . '</td>
						<td>' . $aksik . '</td>
						';
		$tbl .= '</tr>';
	} else {
		$tbl .= '
					<tr style="background-color:Gainsboro">
						<td align="right" width="25px">' . $no . '. ' . '</td>
						<td width="75px">' . $isi[id_tes] . '</td>
						<td align="right">' . $isi[b_abs] . '</td>
						<td align="center">' . $hasilb . '</td>
						<td align="center" style="font-size:15px">' . $b_run_disp . '</td>
						<td align="center">' . $isi[b_ket_tes] . '</td>
						<td align="right">' . $isi[c_abs] . '</td>
						<td align="center">' . $hasilc . '</td>
						<td align="center" style="font-size:15px">' . $c_run_disp . '</td>
						<td align="center">' . $isi[c_ket_tes] . '</td>
						<td align="right">' . $isi[i_abs] . '</td>
						<td align="center">' . $hasili . '</td>
						<td align="center" style="font-size:15px">' . $i_run_disp . '</td>
						<td align="center">' . $isi[i_ket_tes] . '</td>
						<td align="right">' . $isi[s_abs] . '</td>
						<td align="center">' . $hasils . '</td>
						<td align="center" style="font-size:15px">' . $s_run_disp . '</td>
						<td align="center">' . $isi[s_ket_tes] . '</td>
						<td>' . $statuskantong . '</td>
						<td>' . $aksik . '</td>
						';
		$tbl .= '</tr>';
	}
	$no++;
}
$opr_arc = mysql_fetch_assoc(mysql_query("select nama_lengkap from user where id_user='$operator'"));
if ($opr_arc) {
	$operator_arc = $opr_arc[nama_lengkap];
} else {
	$operator_arc = $operator;
}
$konfr = mysql_fetch_assoc(mysql_query("select nama_lengkap from user where id_user='$konfirmer'"));
if ($konfr) {
	$konfirmer_arc = $konfr[nama_lengkap];
} else {
	$konfirmer_arc = $konfirmer;
}
$sah_arc = mysql_fetch_assoc(mysql_query("select nama_lengkap from user where id_user='$pengesah'"));
if ($sah_arc) {
	$pengesah_arc = $sah_arc[nama_lengkap];
} else {
	$pengesah_arc = $pengesah;
}
$tbl .= '
                <tr>
    	            <td colspan="3" align="left">Instrument</td>
    	            <td colspan="5" align="left">' . $instrument . '</td>
    	            <td colspan="12" align="left">INFORMASI REAGENSIA</td>

                </tr>
                <tr><td colspan="3" align="left">Nomor & Waktu Konfirmasi</td>
                    <td colspan="5" align="left">' . $nomor . ', ' . $waktuKonfirmiasi . '</td>
                    <td colspan="2" align="left">Reagen HbsAg</td>
    	            <td colspan="10" align="left">' . $reag_b . '</td>
                </tr>
    	        <tr><td colspan="3" align="left">Operator Architect</td>
    	            <td colspan="3" align="left">' . $operator_arc . '</td>
    	            <td colspan="2" align="left">Paraf:</td>
    	            <td colspan="2" align="left">Reagen HCV</td>
                    <td colspan="10" align="left">' . $reag_c . '</td>
    	        </tr>
    	        <tr><td colspan="3" align="left">Petugas Konfirmasi</td>
    	            <td colspan="3" align="left">' . $konfirmer_arc . '</td>
    	            <td colspan="2" align="left">Paraf:</td>
    	            <td colspan="2" align="left">Reagen HIV</td>
    	            <td colspan="10" align="left">' . $reag_i . '</td>
    	        </tr>
    	        <tr><td colspan="3" align="left">Petugas Pengesahan</td>
    	            <td colspan="3" align="left">' . $pengesah_arc . '</td>
    	            <td colspan="2" align="left">Paraf:</td>
    	            <td colspan="2" align="left">Reagen TPHA</td>
    	            <td colspan="10" align="left">' . $reag_s . '</td>
    	        </tr>
    	        <tr>
    	        <td rowspan="4" colspan="20" align="left" >
                    <b>Catatan :</b>
                        <ul>
                            <li>Kolom <b>Status Kantong</b> adalah status kantong pada saat konfirmasi dilakukan (<u>bukan status kantong saat ini</u>)</li>
                            <li>Kolom <b>Konfirm user</b> adalah aksi dari user saat proses konfirmasi dilakukan</li>
                            <li>Kolom Hasil : <b>NR=Non Reaktif; R=(Reaktif); GZ= GrayZone</b> </li>
                        </ul>
                    </td>
    	        </tr>';
$tbl .= '</table>';
$pdf->writeHTML($tbl, true, false, false, false, '');
$namaPDF = 'Data Konfirmasi-' . $notrans . '.pdf';
$pdf->Output($namaPDF, 'I');
