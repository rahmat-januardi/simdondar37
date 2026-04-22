<?php
ini_set('max_execution_time', 600);
session_start();
require_once('../config/dbi_connect.php');
$leveluser = strtoupper($_SESSION['leveluser']);
$namauser = strtoupper($_SESSION['namauser']);
mysqli_query($dbi, "SET GLOBAL sql_mode = '';");
$q_udd = mysqli_fetch_assoc(mysqli_query($dbi, "select * from utd where aktif='1'"));
$zona_waktu = $q_udd['zonawaktu'];
date_default_timezone_set($zona_waktu);
$namaudd = $q_udd['nama'];
$id_udd = $q_udd['id'];
function cekstatuskantong($status, $stattempat, $sah)
{
    $resulstatus = "";
    switch ($status) {
        case '0':
            $resulstatus = "Kosong";
            if ($stattempat == NULL) $resulstatus = "Kosong di logistik";
            if ($stattempat == '0') $resulstatus = "Kosong di Logistik";
            if ($stattempat == '1') $resulstatus = "Kosong di Aftap";
            break;
        case '1':
            if ($sah == "1") {
                $resulstatus = 'Karantina';
            } else {
                $resulstatus = 'Belum disahkan';
            }
            break;
        case '2':
            $resulstatus = 'Sehat';
            break;
        case '3':
            $resulstatus = "Keluar";
            break;
        case '4':
            $resulstatus = 'Rusak';
            break;
        case '5':
            $resulstatus = 'Rusak-Gagal';
            break;
        case '6':
            $resulstatus = 'Dimusnahkan';
            break;
        case '6':
            $resulstatus = 'Reaktif';
            break;
        default:
            $resulstatus = 'Tidak ada';
            break;
    }
    return $resulstatus;
}
$output = "";
if ($_GET['m']) {
    $mode = $_GET['m'];
    switch ($mode) {
        case "sampel":
            $output = '';
            $v_sampel = addslashes(mysqli_escape_string($dbi, $_POST['InpBarcode']));
            $sqlnatimport = mysqli_query($dbi, "SELECT `ID`, `SB`, `OI`, `AT`, `WID`, `RDT`, `STAT`, `ICRLU`, `ICR`, `ARLU`, `ASCO`, `KI`, `OID`, `ICCO`, `ACO`, `NCAA`, `NCICA`, `IPCA`, `IPCICA`, 
                `CPCA`, `CPCICA`, `ML`, `MLD`, `ISN`, `ECR`, `SITE`, `STYPE`, `BPCA`, `BPCICA`, `TID`, `ADMV`, `VER`, `GUID`, `ENUM`, `TOI`, `CONFIRM`, `USRCONF`, `USRCHK`, `USRVER`, `NOTRX` 
                    FROM `nat_panther` WHERE `SB`='$v_sampel'");
            $sqlselect = mysqli_query($dbi, "SELECT `ID`, `SB`, `OI`, `AT`, `WID`, `RDT`, `STAT`, `ICRLU`, `ICR`, `ARLU`, `ASCO`, `KI`, `OID`, `ICCO`, `ACO`, `NCAA`, `NCICA`, `IPCA`, `IPCICA`, `CPCA`, `CPCICA`, 
                `ML`, `MLD`, `ISN`, `ECR`, `SITE`, `STYPE`, `BPCA`, `BPCICA`, `TID`, `ADMV`, `VER`, `GUID`, `ENUM`, `TOI`, `CONFIRM`, `USRCONF`, `USRCHK`, `USRVER`, `NOTRX` FROM `nat_panther` 
                WHERE  `SB`='$v_sampel'");
            if (mysqli_num_rows($sqlnatimport) > 0) {
                $output .= '<div class="col-md-8">
                            <div class="panel w3-border-theme">
                                <div class="panel-heading w3-theme-d3"><div class="panel-title">Data Pemeriksaan</div></div>
                                <div class="panel-body w3-card-4">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-responsive table-condensed">
                                            <thead>
                                                <tr class="w3-theme">
                                                    <th>No</th>
                                                    <th>Transaksi</th>
                                                    <th>Worklist</th>
                                                    <th>Run Time</th>
                                                    <th>Sample ID</th>
                                                    <th>IC RLU</th>
                                                    <th>IC Result</th>
                                                    <th>RLU</th>
                                                    <th>S/CO</th>
                                                    <th>RESULT</th>
                                                    <th>Flag</th>
                                                    <th>ASSAY</th>
                                                    <th>LOT Reagen</th>
                                                    <th>ED Reagen</th>
                                                    <th>Operator</th>
                                                    <th>Checker</th>
                                                    <th>Verifikator</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                $no = 0;
                while ($dt = mysqli_fetch_assoc($sqlnatimport)) {
                    $no++;
                    $output .= '<tr>
                                                            <td class="text-right" nowrap>' . $no . '.</td>
                                                            <td nowrap>' . $dt['NOTRX'] . '</td>
                                                            <td nowrap>' . $dt['WID'] . '</td>
                                                            <td nowrap>' . $dt['RDT'] . '</td>
                                                            <td nowrap>' . $dt['SB'] . '</td>
                                                            <td nowrap>' . $dt['ICRLU'] . '</td>
                                                            <td nowrap>' . $dt['ICR'] . '</td>
                                                            <td nowrap>' . $dt['ARLU'] . '</td>
                                                            <td nowrap>' . $dt['ASCO'] . '</td>
                                                            <td nowrap>' . $dt['OI'] . '</td>
                                                            <td nowrap>' . $dt['STAT'] . '</td>
                                                            <td nowrap>' . $dt['AT'] . '</td>
                                                            <td nowrap>' . $dt['ML'] . '</td>
                                                            <td nowrap>' . $dt['MLD'] . '</td>
                                                            <td nowrap>' . $dt['OID'] . '</td>
                                                            <td nowrap>' . $dt['USRCHK'] . '</td>
                                                            <td nowrap>' . $dt['USRVER'] . '</td>
                                                            </tr>';
                }
                $output .= '
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>';
                $sqlhasilnat = mysqli_query($dbi, "SELECT `id`, `noKantong`, `idsample`, `nat_goldarah`, `nat_rhesus`, `notrans`, `kodedonor`, 
                    CASE WHEN `dsdp`='0' THEN 'Donor Sukarela' WHEN `dsdp`='0' THEN 'Donor Pengganti' ELSE '-' END AS `dsdp`, 
                    CASE WHEN `barulama`='0' THEN 'Donor Rutin' WHEN `barulama`='1' THEN 'Donor Baru' ELSE '-' END AS barulama, `umur`, 
                    CASE WHEN `kel`='0' THEN 'Laki-laki' WHEN `kel`='1' THEN 'Perempuan' ELSE '-' END AS kel, `umur`, 
                    `OD`, `COV`, `Hasil`, `jenisPeriksa`, `tglPeriksa`, `dicatatOleh`, `dicekOleh`, `DisahkanOleh`, `noLot`, `Metode`, `reagen`, `ed`, `ulang`, `tempat_periksa`, `on_insert` FROM `hasilnat` WHERE `noKantong`='$v_sampel'");
                if (mysqli_num_rows($sqlhasilnat) > 0) {
                    $hasil = mysqli_fetch_assoc($sqlhasilnat);
                    $output .= '
                    <div class="col-md-4">
                        <div class="panel w3-border-theme">
                            <div class="panel-heading w3-theme-d3"><div class="panel-title">Data Donasi</div></div>
                            <div class="panel-body w3-card-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive">
                                        <tr><td>Nomor Kantong</td>       <td class="w3-hover-theme">' . $hasil['noKantong'] . '</td></tr>
                                        <tr><td>Kode Donor</td>       <td class="w3-hover-theme">' . $hasil['kodedonor'] . '</td></tr>
                                        <tr><td>Golongan Darah</td>           <td class="w3-hover-theme">' . $hasil['nat_goldarah'] . '</td></tr>
                                        <tr><td>Rhesus</td>             <td class="w3-hover-theme">' . $hasil['nat_rhesus'] . '</td></tr>
                                        <tr><td>Jenis Donor</td>             <td class="w3-hover-theme">' . $hasil['dsdp'] . '</td></tr>
                                        <tr><td>Donor Baru/Lama</td>                 <td class="w3-hover-theme">' . $hasil['barulama'] . '</td></tr>
                                        <tr><td>Umur</td>              <td class="w3-hover-theme">' . $hasil['umur'] . '</td></tr>
                                        <tr><td>Jenis Kelamin</td>        <td class="w3-hover-theme">' . $hasil['kel'] . '</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                $output = '
                <div class="col-md-12">
                    <div class="w3-panel w3-red w3-border-theme w3-card-4">
                        <h4>Nomor sampel <strong>' . $v_sampel . '</strong> <h4>
                        <h4>Tidak ditemukan!</h4>
                    </div>
                </div>';
            }
            echo $output;
            break;
        case "displaydata":
            $v_tgl1 = addslashes(mysqli_escape_string($dbi, $_GET['tgl1']));
            $v_tgl2 = addslashes(mysqli_escape_string($dbi, $_GET['tgl2']));
            $v_hasil = addslashes(mysqli_escape_string($dbi, $_GET['InpHasil']));
            $v_sampel = addslashes(mysqli_escape_string($dbi, $_GET['InpJenisSampel']));
            $v_operator = addslashes(mysqli_escape_string($dbi, $_GET['InpOperator']));
            $v_checker = addslashes(mysqli_escape_string($dbi, $_GET['InpKonfirmasi']));
            switch ($v_hasil) {
                case "0":
                    $whasil = "";
                    break;
                case "1":
                    $whasil = " AND (`OI`='Nonreactive') ";
                    break;
                case "2":
                    $whasil = " AND (`OI`='Reactive') ";
                    break;
                default:
                    $whasil = " AND (`OI`<>'Nonreactive') AND (`OI`<>'Reactive') ";
                    break;
            }
            switch ($v_sampel) {
                case "0":
                    $wsampel = "";
                    break;
                case "1":
                    $wsampel = " AND (`STYPE`='Specimen') ";
                    break;
                case "2":
                    $wsampel = " AND (`STYPE`='Calibrator') ";
                    break;
                default:
                    $wsampel = "";
                    break;
            }
            ($v_operator !== "") ? $woperator = " AND (`OID` LIKE '%$v_operator%') " : $woperator = "";
            ($v_checker !== "") ? $whecker = " AND (`USRCHK` LIKE '%$v_checker%') " : $whecker = "";
            $sqlselect = "SELECT `ID`, `SB`, `OI`, `AT`, `WID`, `RDT`, `STAT`, `ICRLU`, `ICR`, `ARLU`, `ASCO`, `KI`, `OID`, `ICCO`, `ACO`, `NCAA`, `NCICA`, `IPCA`, `IPCICA`, `CPCA`, `CPCICA`, 
                `ML`, `MLD`, `ISN`, `ECR`, `SITE`, `STYPE`, `BPCA`, `BPCICA`, `TID`, `ADMV`, `VER`, `GUID`, `ENUM`, `TOI`, `CONFIRM`, `USRCONF`, `USRCHK`, `USRVER`, `NOTRX` FROM `nat_panther` 
                WHERE  (STR_TO_DATE(`RDT`,'%Y-%m-%d') BETWEEN '$v_tgl1' AND '$v_tgl2') AND (`confirm`='1')" . $whasil . $wsampel . $woperator . $whecker;
            $output = '<div class="table-responsive">
                <table class="table table-bordered table-hover table-responsive table-condensed">
                    <thead>
                        <tr class="w3-theme">
                            <th>No</th>
                            <th>Transaksi</th>
                            <th>Worklist</th>
                            <th>Run Time</th>
                            <th>Sample ID</th>
                            <th>IC RLU</th>
                            <th>IC Result</th>
                            <th>RLU</th>
                            <th>S/CO</th>
                            <th>RESULT</th>
                            <th>Flag</th>
                            <th>ASSAY</th>
                            <th>LOT Reagen</th>
                            <th>ED Reagen</th>
                            <th>Operator</th>
                            <th>Checker</th>
                            <th>Verifikator</th>
                        </tr>
                    </thead>
                <tbody>';
            $qryselect = mysqli_query($dbi, $sqlselect);
            if (mysqli_num_rows($qryselect) > 0) {
                $no = 0;
                while ($dt = mysqli_fetch_assoc($qryselect)) {
                    $no++;
                    $output .= '<tr>
                        <td class="text-right">' . $no . '.</td>
                        <td>' . $dt['NOTRX'] . '</td>
                        <td>' . $dt['WID'] . '</td>
                        <td>' . $dt['RDT'] . '</td>
                        <td>' . $dt['SB'] . '</td>
                        <td>' . $dt['ICRLU'] . '</td>
                        <td>' . $dt['ICR'] . '</td>
                        <td>' . $dt['ARLU'] . '</td>
                        <td>' . $dt['ASCO'] . '</td>
                        <td>' . $dt['OI'] . '</td>
                        <td>' . $dt['STAT'] . '</td>
                        <td>' . $dt['AT'] . '</td>
                        <td>' . $dt['ML'] . '</td>
                        <td>' . $dt['MLD'] . '</td>
                        <td>' . $dt['OID'] . '</td>
                        <td>' . $dt['USRCHK'] . '</td>
                        <td>' . $dt['USRVER'] . '</td>
                        </tr>';
                }
            } else {
                $output .= '<tr><td class="text-center" colspan="17">Tidak ada data</td></tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
            break;
        case "display":
            $v_tgl1 = mysqli_escape_string($dbi, $_GET['tgl1']);
            $v_tgl2 = mysqli_escape_string($dbi, $_GET['tgl2']);
            $v_konfirm = mysqli_escape_string($dbi, $_GET['konfirm']);
            $v_operator = mysqli_escape_string($dbi, $_GET['operator']);
            $woperator = "";
            if ($v_operator !== "") {
                $woperator = " AND (`OID` like '%$v_operator%')";
            }
            $wkonfirm = "";
            if ($v_konfirm !== "2") {
                $wkonfirm = " AND (`CONFIRM`='$v_konfirm')";
            }
            $sqlst = "SELECT STR_TO_DATE(`RDT`,'%Y-%m-%d') AS TGL,
                    `WID`, `AT`, `OID`, `CONFIRM`, NOTRX,count(`ID`) as jml
                    FROM `nat_panther` WHERE `GUID`<>'' AND (STR_TO_DATE(`RDT`,'%Y-%m-%d') BETWEEN '$v_tgl1' AND '$v_tgl2') " . $wkonfirm . $woperator . "
                    GROUP BY  STR_TO_DATE(`RDT`,'%Y-%m-%d'), `WID`,`AT`,`OID`,`CONFIRM`,NOTRX";
            $output = '<div class="table-responsive">
                        <table class="table table-bordered table-hover table-responsive">
                            <thead>
                                <tr class="w3-theme">
                                <th>No</th>
                                <th>TANGGAL</th>
                                <th>WORKLIST ID</th>
                                <th>ASSAY</th>
                                <th>OPERATOR</th>
                                <th>JUMLAH</th>
                                <th>KONFIRMASI</th>
                                <th></th>
                                </tr>
                            </thead>
                        <tbody>';
            $sqldisplay = mysqli_query($dbi, $sqlst);
            if (mysqli_num_rows($sqldisplay) > 0) {
                $no = 0;
                while ($dt = mysqli_fetch_assoc($sqldisplay)) {
                    $no++;
                    ($dt['CONFIRM'] == '0') ? $stkonfirm = "Belum" : $stkonfirm = "Sudah";
                    $output .= '
                    <tr>
                        <td class="text-right">' . $no . '.</td>
                        <td class="text-center">' . $dt['TGL'] . '</td>
                        <td class="text-center">' . $dt['WID'] . '</td>
                        <td class="text-center">' . $dt['AT'] . '</td>
                        <td class="text-center">' . $dt['OID'] . '</td>
                        <td class="text-center">' . $dt['jml'] . '</td>
                        <td class="text-center">' . $stkonfirm . '</td>
                        <td class="text-center" nowrap>';
                    if ($dt['CONFIRM'] == '0') {
                        $output .= '
                            <a href="?module=panther_konfirm1&wid=' . $dt['WID'] . '&oid=' . $dt['OID'] . '&at=' . $dt['AT'] . '" class="konfirmasi w3-btn w3-theme w3-hover-green btn-sm">Konfirm</a>
                            <a href="#" id="' . $dt['WID'] . '*' . $dt['OID'] . '*' . $dt['AT'] . '" class="hapusdata w3-btn w3-theme w3-hover-red btn-sm">Hapus</a>';
                    } else {
                        $output .= '<a href="?module=panther_printrslt&notransaksi=' . $dt['NOTRX'] . '" class="lihatdata w3-btn w3-theme w3-hover-red btn-sm"><i class="fa fa-print"></i> ' . $dt['NOTRX'] . '</a>';
                    }
                    $output .= '
                        </td>
                    </tr>';
                }
            } else {
                $output .= '
                <tr>
                    <td class="text-center" colspan="8">Tidak ada data</td>
                </tr>';
            }
            $output .= '
                </tbody>
                </table>
                <div>';
            echo $output;
            break;
        case "delete":
            $param = addslashes(mysqli_escape_string($dbi, $_POST['delete_id']));
            $exparam = explode('*', $param);
            $v_wid = $exparam[0];
            $v_oid = $exparam[1];
            $v_at = $exparam[2];
            mysqli_query($dbi, "DELETE FROM `nat_panther` WHERE `WID`='$v_wid' AND `OID`='$v_oid' AND `AT`='$v_at';");
            break;
        case "konfirmasihasil":
            $k_today = $id_udd . "NATP" . date("dmy") . "-";
            $idp = mysqli_query($dbi, "SELECT `NOTRX`,RIGHT(`NOTRX`,3) as `nolast` from `nat_panther` where `NOTRX`  like '$k_today%'order by `NOTRX` DESC limit 1");
            $idp1 = mysqli_fetch_assoc($idp);
            $idp2 = $idp1['nolast'];
            $idp2 = (int)$idp2;
            if ($idp2 < 1) {
                $idp2 = "000";
            }
            $int_idp2 = (int)$idp2 + 1;
            $j_nol1 = 3 - (strlen(strval($int_idp2)));
            $idp4 = '';
            for ($n = 0; $n < $j_nol1; $n++) {
                $idp4 .= "0";
            }
            $v_notransaksi = $k_today . $idp4 . $int_idp2;
            $output = "";
            $status_proses = 0;
            $v_wid = $_POST['wid'];
            $v_oid = $_POST['oid'];
            $v_at = $_POST['at'];
            $v_parameter = $_POST['parameter'];
            $v_reagenlot = $_POST['raegenlot'];
            $v_reagened = $_POST['raegened'];
            $v_ptgoperator = $_POST['inpOperator'];
            $v_ptgkonfirmasi = $_POST['inpPtgKonfirmasi'];
            $v_ptgverifikasi = $_POST['inpPtgSah'];
            $v_sampelid = $_POST['sampleid'];
            $v_sampelguid = $_POST['guid'];
            $v_sampletype = $_POST['sampletipe'];
            $v_statuskantong = $_POST['statuskantong'];
            $v_aksi = $_POST['aksi'];
            $proses = "";
            $no = 0;
            $aksi = "";
            for ($i = 0; $i < count($v_sampelid); $i++) {
                $no++;
                switch ($v_aksi[$i]) {
                    case '1':
                        $aksi = "Konfirm";
                        break;
                    case '2':
                        $aksi = "Sehat";
                        break;
                    case '3':
                        $aksi = "Cekal";
                        break;
                    case '4':
                        $aksi = "Tunda";
                        break;
                }
                $proses .= '<br>' . $no . ' :  ' . $v_sampelid[$i] . ' - Status  ' . $v_statuskantong[$i] . ' - ' . $v_aksi[$i] . ' (' . $aksi . '); ';
                $d_sampel = $v_sampelid[$i];
                $d_guid = $v_sampelguid[$i];
                $d_jenis = $v_sampletype[$i];
                $d_statuskantong = $v_statuskantong[$i];
                $d_aksi = $v_aksi[$i];
                if ($d_aksi !== "4") {
                    $proses .= ' - tdk ditunda ';
                    $sqlceklis = "SELECT * , (STR_TO_DATE(`RDT`,'%Y-%m-%d')) as tglperiksa, (STR_TO_DATE(`MLD`,'%Y-%m-%d')) as edreagen FROM `nat_panther` WHERE `WID`='$v_wid' AND `SB`='$d_sampel' AND `GUID`='$d_guid' AND `OID`='$v_oid' AND `AT`='$v_at'";
                    $cekimport = mysqli_query($dbi, $sqlceklis);
                    $dtpanther = mysqli_fetch_assoc($cekimport);
                    $d_tglperiksa = $dtpanther['RDT'];
                    $d_resultod = $dtpanther['ASCO'];
                    $d_resultstr = $dtpanther['OI'];
                    $d_hasilnatkantong = '0';
                    switch ($d_resultstr) {
                        case "Nonreactive":
                            $d_hasil = '0';
                            $d_hasilnatkantong = '0';
                            break;
                        case "Reactive":
                            $d_hasil = '1';
                            $d_hasilnatkantong = '1';
                            break;
                        default:
                            $d_hasil = '2';
                            $d_hasilnatkantong = '3';
                            break;
                    }
                    $sqlupdkonfirm = "UPDATE `nat_panther` SET `CONFIRM`=1, `USRCONF`='$v_ptgoperator',`USRCHK`='$v_ptgkonfirmasi',`USRVER`='$v_ptgverifikasi',`NOTRX` ='$v_notransaksi' WHERE `WID`='$v_wid' AND `SB`='$d_sampel' AND `OID`='$v_oid' AND `GUID`='$d_guid'";
                    $sqlupd_panther = mysqli_query($dbi, $sqlupdkonfirm);
                    if ($sqlupd_panther) {
                        $proses .= ' Konfirm OK; ';
                    } else {
                        $proses .= ' Konfirm Err ' . mysqli_error($dbi) . '; ';
                    }
                    if ($d_statuskantong !== '0') {
                        $proses .= ' Status Kantong: ' . $d_statuskantong . ' ';
                        $sqldonasi = mysqli_query($dbi, "SELECT `KodePendonor`,`JenisDonor`,`NoKantong`,`gol_darah`,`rhesus`,`umur`,`donorbaru`,`jk`,`donorke` FROM `htransaksi` WHERE `NoKantong`='$d_sampel'");
                        if (mysqli_num_rows($sqldonasi) > 0) {
                            $dttrx = mysqli_fetch_assoc($sqldonasi);
                            $d_jenisdonor = $dttrx['JenisDonor'];
                            $d_golongan = $dttrx['gol_darah'];
                            $d_rhesus = $dttrx['rhesus'];
                            $d_barulama = $dttrx['donorbaru'];
                            $d_kelamin = $dttrx['jk'];
                            $d_umur = $dttrx['umur'];
                            $d_donasi = $dttrx['donorke'];
                            $d_kodedonor = $dttrx['KodePendonor'];
                            $sqlinsertnat = "INSERT INTO `hasilnat`(`noKantong`, `idsample`, `nat_goldarah`, `nat_rhesus`, `notrans`, `kodedonor`, `dsdp`, `barulama`, `umur`, `kel`, 
                                    `OD`, `Hasil`, `tglPeriksa`, `dicatatOleh`, `dicekOleh`, `DisahkanOleh`, `noLot`, `Metode`, `reagen`, `ed`, `tempat_periksa`) VALUES (
                                    '$d_sampel', '$d_sampel', '$d_golongan', '$d_rhesus', '$v_notransaksi', '$d_kodedonor', '$d_jenisdonor', '$d_barulama', '$d_umur', '$d_kelamin',
                                    '$d_resultod', '$d_hasil' ,'$d_tglperiksa' ,'$v_ptgoperator' ,'$v_ptgkonfirmasi', '$v_ptgverifikasi', '$v_reagenlot', 'OTOMATIS', '$v_parameter', '$v_reagened', '$namaudd')";
                            if ($d_hasil == '1') {
                                if ($d_statuskantong == '1' or $d_statuskantong == '2') {
                                    $updkantong = mysqli_query($dbi, "UPDATE `stokkantong` SET `Status`='7', `tgl_nat`='$d_tglperiksa',`hasilNAT` ='$d_hasilnatkantong' WHERE `noKantong`='$d_sampel';");
                                    if ($updkantong) {
                                        $proses .= ' Upd Kantong 7 (reaktif) OK; ';
                                    } else {
                                        $proses .= ' Upd Kantong  7 (reaktif) Err ' . mysqli_error($dbi) . '; ';
                                    }
                                } else {
                                    $updkantong = mysqli_query($dbi, "UPDATE `stokkantong` SET `tgl_nat`='$d_tglperiksa',`hasilNAT` ='$d_hasilnatkantong' WHERE `noKantong`='$d_sampel';");
                                    if ($updkantong) {
                                        $proses .= 'Upd Status NAT OK; ';
                                    } else {
                                        $proses .= ' Upd Status NAT Err ' . mysqli_error($dbi) . '; ';
                                    }
                                }
                                $sqlcekaldonor = "UPDATE `pendonor` SET `cekalNAT`=1,`Cekal`=1 WHERE `Kode`='$d_kodedonor'";
                                $qrycekaldonor = mysqli_query($dbi, $sqlcekaldonor);
                                if ($qrycekaldonor) {
                                    $proses .= ' Cekal NAT Pendonor OK';
                                } else {
                                    $proses .= ' Cekal NAT Pendonor Err:' . mysqli_error($dbi);
                                }
                                $proses .= ' status Stokkantong reaktif : 7 ';
                            } else {
                                $updkantong = mysqli_query($dbi, "UPDATE `stokkantong` SET `tgl_nat`='$d_tglperiksa',`hasilNAT` ='$d_hasilnatkantong' WHERE `noKantong`='$d_sampel';");
                                if ($updkantong) {
                                    $proses .= 'Upd Status Nat OK; ';
                                } else {
                                    $proses .= ' Upd Status NAT Err ' . mysqli_error($dbi) . '; ';
                                }
                            }
                        } else {
                            $sqlinsertnat = "INSERT INTO `hasilnat`(`noKantong`, `idsample`, `notrans`, `OD`, `Hasil`, `tglPeriksa`, `dicatatOleh`, `dicekOleh`, `DisahkanOleh`, `noLot`, 
                                    `Metode`, `reagen`, `ed`, `tempat_periksa`) VALUES (
                                    '$d_sampel', '$d_sampel', '$v_notransaksi', '$d_resultod', '$d_hasil', '$d_tglperiksa', '$v_ptgoperator', '$v_ptgkonfirmasi', '$v_ptgverifikasi', '$v_reagenlot',
                                    'OTOMATIS', '$v_reagenname','$v_reagened', '$namaudd')";
                        }
                        $qryinsertnat = mysqli_query($dbi, $sqlinsertnat);
                        if ($qryinsertnat) {
                            $proses .= ' NAT ok; ';
                        } else {
                            $proses .= ' NAT Err: ' . mysqli_error($dbi) . '; ';
                        }
                    } else {
                        $proses .= ' Status Kantong 0 atau tidak ada;  ';
                    }
                } else {
                    $proses .= ' - ditunda; ';
                }
            }
            $output = $proses . '|' . $status_proses . '|' . $v_notransaksi;
            echo $output;
            break;
    }
}
