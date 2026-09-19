<?php
session_start();
$today = date("Y-m-d");
$array_bulan = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des');
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
$aColumns = array('wa_time', 'wa_no', 'wa_text', 'id');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "outbox";
include("../config/db_connect_wa.php");
/* Database connection information */
$gaSql['user']       = $db_user;
$gaSql['password']   = $db_pass;
$gaSql['db']         = $db_name;
$gaSql['server']     = $db_host;

/* REMOVE THIS LINE (it just includes my SQL connection user/pass) */


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */

/* 
	 * MySQL connection
	 */
$gaSql['link'] =  mysql_pconnect($gaSql['server'], $gaSql['user'], $gaSql['password']) or
	die('Could not open connection to server');

mysql_select_db($gaSql['db'], $gaSql['link']) or
	die('Could not select database ' . $gaSql['db']);


/* 
	 * Paging
	 */
$sLimit = "";
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
	$sLimit = "LIMIT " . mysql_real_escape_string($_GET['iDisplayStart']) . ", " .
		mysql_real_escape_string($_GET['iDisplayLength']);
}


/*
	 * Ordering
	 */
$sOrder = "";
if (isset($_GET['iSortCol_0'])) {
	$sOrder = "ORDER BY  ";
	for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
		if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
			$sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
				 	" . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
		}
	}

	$sOrder = substr_replace($sOrder, "", -2);
	if ($sOrder == "ORDER BY") {
		$sOrder = "";
	}
}


/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
$today = date("Y-m-d");
$sWhere = "";
if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
	$sWhere = "WHERE ( ";
	for ($i = 0; $i < count($aColumns); $i++) {
		$sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
	}
	$sWhere = substr_replace($sWhere, "", -3);
	$sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
	if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
		if ($sWhere == "") {
			$sWhere = "WHERE ";
		} else {
			$sWhere .= " AND ";
		}
		if ($i != 3) $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
		if ($i == 3) $sWhere .= $aColumns[$i] . " = '" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "' ";
	}
}
/*
	if ( $sWhere == "" ){
		$sWhere .= "WHERE (Cekal='0' and (Length(telp)>4 or Length(telp2)>4) and tglkembali<'$today')"; // tambahan
	}else{
		$sWhere .= "AND (Cekal='0' and (Length(telp)>4 or Length(telp2)>4))"; // tambahan
	}*/

/*
	 * SQL queries
	 * Get data to display
	 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
$rResult = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());

/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
	";
$rResultFilterTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(" . $sIndexColumn . ")
		FROM   $sTable
	";
$rResultTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];


/*
	 * Output
	 */
$output = array(
	"sEcho" => intval($_GET['sEcho']),
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => array()
);

$referrer = ''; // Default
if (isset($_SESSION['leveluser'])) {
	switch ($_SESSION['leveluser']) {
		case 'p2d2s':
			$referrer = 'pmip2d2s';
			break;
		case 'kasir': // Tambah untuk pmikasir
			$referrer = 'pmikasir';
			break;
		// Tambah case lain jika ada level baru, misal case 'admin': $referrer = 'pmiadmin';
		default:
			$referrer = 'pmiadmin';
	}
}

while ($aRow = mysql_fetch_array($rResult)) {
	$row = array();
	for ($i = 0; $i < count($aColumns); $i++) {
		if ($aColumns[$i] == "version") {
			/* Special output formatting for 'version' column */
			$row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
		} else if ($aColumns[$i] != ' ') {
			if ($i == 0) {
				$tgl = $aRow[$aColumns[$i]];
				$bln11 = date("n", strtotime($tgl));
				$bln1 = $array_bulan[$bln11];
				$row[] = substr($tgl, 8, 2) . ' ' . $bln1 . ' ' . substr($tgl, 0, 4) . ' ' . substr($tgl, 11, 8);
			} elseif ($i == 3) {
				$id = $aRow[$aColumns[$i]];
				$row[] = $link . "<a href='" . $referrer . ".php?module=hapus_wa_outbox&Id=" . $id . "&referrer=" . $referrer . "' TITLE=\"Hapus Pesan ini\">[Hapus]</a>";
			} else {
				/* General output */
				$row[] = $aRow[$aColumns[$i]];
			}
		}
	}
	$output['aaData'][] = $row;
}

echo json_encode($output);