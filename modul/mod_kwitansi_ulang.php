<link href="modul/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="modul/thickbox/thickbox.js"></script>
<script type="text/javascript" src="js/kantong.js"></script>

<?
include("config/db_connect.php");
$tgl_permintaan = date("Y-m-d H:i:s");
$yesterday = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
$tgl_yesterday = date("Y-m-d", $yesterday);
$td0 = php_uname('n');
$td0 = substr($td0, 0, 3);
if (isset($_POST[submit])) {
    //print_r($_POST);
    $nofo = $_POST[nofo];
    $jum = $_POST[jumlah];
    $biaya = $_POST[bia];
    $totpot = $_POST[tpot];
    $dibayar = $_POST[tbersih];
    $total_terbayar = $_POST[tbersih] + $_POST[terbayar];
    //$numcek=mysql_fetch_row(mysql_query("select * from dpembayaran where noForm='$nofo'"));
    //if ($numcek<1) {
    $bay_sql = "insert into dpembayaran (noForm,BiayaLD,tgl,TotPotongan,TotDibayar,totPeriksa)
					values ('$nofo','$biaya','$tgl_permintaan','$totpot','$total_terbayar','$jum')
					ON DUPLICATE KEY UPDATE
						BiayaLD='$biaya',TotPotongan='$totpot',TotDibayar='$total_terbayar',totPeriksa='$jum'
					";
    $bay = mysql_query($bay_sql);
    $q_pembayaran_sql = "insert into pembayaran (NoTrans,Tgl,Jumlah)
					values ('$nofo',NOW(),'$dibayar')";
    $q_bayar = mysql_query($q_pembayaran_sql);
    $bay_sql = base64_encode($bay_sql . ';');
    $q_pembayaran_sql = base64_encode($q_pembayaran_sql . ';');
    $myfile = "bdrs/dari-$td0-$tgl_permintaan.zip";
    if (file_exists($myfile)) {
        $fh = fopen($myfile, 'a') or die("Cant open file");
    } else {
        $fh = fopen($myfile, 'w') or die("Cant open file");
    }
    fwrite($fh, $bay_sql);
    fwrite($fh, "\n");
    fwrite($fh, $q_pembayaran_sql);
    fwrite($fh, "\n");
    fclose($fh);
    $myfile = "bdrs/dari-$td0-$tgl_yesterday.zip";
    if (file_exists($myfile)) {
        $fh = fopen($myfile, 'a') or die("Cant open file");
    } else {
        $fh = fopen($myfile, 'w') or die("Cant open file");
    }
    fwrite($fh, $bay_sql);
    fwrite($fh, "\n");
    fwrite($fh, $q_pembayaran_sql);
    fwrite($fh, "\n");
    fclose($fh);
    //}
}
$noform1 = $_GET[noform];
//$noform10=explode('/',$_GET[noform]);
//if (sizeof($noform10)>1) $noform1=$noform10[0]."-".$noform10[1];
//if (sizeof($noform10)<2) $noform1=$_GET[noform];
$pem1 = mysql_num_rows(mysql_query("select * from dpembayaranpermintaan where notrans='$_GET[noform]'"));
if ($pem1 < 1) $noform1 = str_replace("-", "/", $_GET[noform]);
$noform2 = $_GET[noform];
$pem = mysql_fetch_assoc(mysql_query("select * from dpembayaranpermintaan where notrans='$noform1'"));
?>
<script>
    function bayar() {
        var total = document.byr.bia.value - document.byr.tpot.value;
        document.byr.tbersih.value = total;
    }
</script>
<h1 class="table">Cetak Ulang Pembayaran</h1>
<form name="byr" method="post">
    <table class="form" cellspacing="0" cellpadding="0">
        <tr>
            <td>No. Formulir</td>
            <td>:</td>
            <td class="input"><?= $pem[notrans] ?></td>
        </tr>
        <tr>
            <td>Biaya Total</td>
            <td>:</td>
            <?
            $tt = mysql_fetch_assoc(mysql_query("select sum(subTotal) as tot,sum(jum) as jumlah from dpembayaranpermintaan where notrans='$pem[notrans]'"));
            ?>
            <td class="input">
                <?= $tt[tot] ?>
                <input type="hidden" name="bia" value="<?= $tt[tot] ?>">
            </td>
        </tr>
        <!--<tr>
		<td>Sudah dibayar</td><td>:</td>
		<?
        $terbayar = mysql_fetch_assoc(mysql_query("select * from dpembayaran where noForm='$pem[notrans]'"));
        $dibayar = $tt[tot] - $terbayar[TotDibayar];
        ?>
        <td class="input"> 
			<?= $terbayar[TotDibayar] ?>
			<input name="terbayar" type="hidden" value="<?= $terbayar[TotDibayar] ?>" size="20">
		</td>
	</tr>-->
        <? if (!isset($_POST[submit])) { ?>
            <tr>
                <td>Total Potongan</td>
                <td>:</td>
                <?
                $by = mysql_fetch_assoc(mysql_query("select * from dpembayaran where noForm='$pem[notrans]'"));
                ?>
                <td class="input">
                    <input onChange="bayar()" name="tpot" type="text" size="20" value="<?= $by[TotPotongan] ?>">
                </td>
            </tr>
            <tr>
                <td>Total Dibayar</td>
                <td>:</td>
                <td class="input">
                    <input name="tbersih" type="text" value="<?= $tt[tot] ?>" size="20">
                    <!--<input name="tbersih" type="text" value="<?= $dibayar ?>" size="20">-->
                </td>
            </tr>
            <tr>
                <td>Dibayar oleh</td>
                <td>:</td>
                <td class="input">
                    <input name="yby" type="text" size="20">
                </td>
    </table>
    <input name="nofo" type="hidden" value="<?= $pem[notrans] ?>">
    <input name="jumlah" type="hidden" value="<?= $tt[jumlah] ?>">
    <br>
<? }
        if (!isset($_POST[submit])) { ?>
    <input name="submit" type="submit" value="Simpan">
</form>
<? } else { ?>
    <input name=cetak type=button value="Cetak Nota"
        onclick="$.fn.colorbox({href:'nota_ulang.php?noform=<?= $pem[notrans] ?>&yby=<?= $_POST[yby] ?>',iframe:true,innerWidth:350,innerHeight:350},function(){ $().bind('cbox_closed',function(){window.location ='pmiadmin.php?module=nota1&noform=<?= $noform2 ?>'})});">
<? } ?>