<?
include "config/db_connect.php";
$today = date("Y-m-d");
$bdrs = mysql_query("select gol_darah,RhesusDrh,produk,tgl_Aftap,kadaluwarsa,tglpengolahan, jenis, volumeasal from stokkantong where noKantong='$_GET[NoKantong]' and (Status='2' or Status='3') and (stat2 is null or stat2='0') and statKonfirmasi='1' and statQC='' ");

if (mysql_num_rows($bdrs) == '1') {
    echo '{"darah": ';
    while ($bdrs1 = mysql_fetch_assoc($bdrs)) {

        //tgl aftap
        if ($bdrs1[tgl_Aftap] == NULL) $aftap = '-';
        if ($bdrs1[tgl_Aftap]) $aftap = date("Y-m-d", strtotime($bdrs1[tgl_Aftap]));

        //tgl kadaluwarsa produk
        if ($bdrs1[kadaluwarsa] == NULL) $kadaluwarsa = '-';
        if ($bdrs1[kadaluwarsa]) $kadaluwarsa = date("Y-m-d", strtotime($bdrs1[kadaluwarsa]));

        //tgl pengolahan
        if ($bdrs1[tglpengolahan] == NULL) $tglpengolahan = '-';
        if ($bdrs1[tglpengolahan]) $tglpengolahan = date("Y-m-d", strtotime($bdrs1[tglpengolahan]));

        switch ($bdrs1[jenis]) {
            case '1':
                $jenisKantong = 'Single';
                break;
            case '2':
                $jenisKantong = 'Double';
                break;
            case '3':
                $jenisKantong = 'Triple';
                break;
            default:
                // code...
                break;
        }


        $gol = $bdrs1['gol_darah'];
        $produk = $bdrs1['produk'];
        $rh = $bdrs1['RhesusDrh'];
        $volumeasal = $bdrs1['volumeasal'];


        echo '
            {
                "gol_darah":"' . $gol . '",
                "produk":"' . $produk . '",
		"RhesusDrh":"' . $rh . '",
		"tgl_Aftap":"' . $aftap . '",
		"kadaluwarsa":"' . $kadaluwarsa . '",
		"tglpengolahan":"' . $tglpengolahan . '",
		"volumeasal":"' . $volumeasal . '",
		"jeniskantong":"' . $jenisKantong . '",
                "valid":"1"
            }';
    }
    echo '}';
}