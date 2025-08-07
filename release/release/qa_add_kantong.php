<link type="text/css" href="../css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<link type="text/css" href="../css/style.css" rel="stylesheet" />
<link type="text/css" href="../css/blitzer/suwena.css" rel="stylesheet" />
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<script>
	function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
</script>
<style type="text/css">.styled-select select {background-color: #FCF9F9; border: none;width: auto;padding: 3px;font-size: 15px;cursor: pointer; }</style>

<html xmlns="http://www.w3.org/1999/xhtml">
<?
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$tglsebelum = mktime(0,0,0,date("m"),1,date("Y"));
$tglawal=date("Y-m-d");
$hariini = date("Y-m-d");

if((isset($_POST[submit])) and ($_POST[berat_ku]!=="")){
	$sql="INSERT INTO `master_kantong`
          (`merk`, `jenis`, `vol`, `berat_ku`, `berat_s1`, `berat_s2`, `berat_s3`, `berat_s4`, `berat_s5`, `berat_s6`, `berat_s7`)
          VALUES
          ('$_POST[merk]','$_POST[jenis]', '$_POST[vol]','$_POST[berat_ku]','$_POST[berat_s1]','$_POST[berat_s2]','$_POST[berat_s3]','$_POST[berat_s4]',
          '$_POST[berat_s5]','$_POST[berat_s6]','$_POST[berat_s7]')";
	$sqladd=mysql_query($sql);
	
	if ($sqladd){
        //=======Audit Trial====================================================================================
        $log_mdl ='PROLIS';
        $log_aksi='Menambah data Berat kantong kosong: '.$_POST[merk].' - '.$_POST[jenis];
        include_once "user_log.php";
        //=====================================================================================================
		echo "<br><br>Penambahan data berhasil</b><br>";
		echo "<meta http-equiv=\"refresh\" content=\"1; URL=pmiqa.php?module=kantong_kosong\">";
	} else {
		echo "<br><br>Update Sukses!</b><br>";
		echo "<meta http-equiv=\"refresh\" content=\"1; URL=pmiqa.php?module=kantong_kosong\">";
	}
}else{?>
    <font size="4" color="red" face="Trebuchet MS"><b>TAMBAH MASTER BERAT KANTONG</b></font>
	<form name="setting" method="post" action="<? $PHP_SELF ?>">
	<table class="form" cellspacing="1" cellpadding="0" border="1">
        <tr>
            <td style="font-size: 15px">MERK</td>
            <td class="styled-select">
                <select name="merk">
                    <?
                    $select1='';	$select2='';	$select9='';
                    $select3='';	$select4='';
                    $select5='';	$select6='';
                    $select7='';	$select8='';
                    if ($_POST[merk]=='KARMI') $select1='selected';
                    if ($_POST[merk]=='TERUMO') $select2='selected';
                    if ($_POST[merk]=='JMS') $select3='selected';
                    if ($_POST[merk]=='JML') $select4='selected';
                    if ($_POST[merk]=='HLHAEMOPACK') $select5='selected';
                    if ($_POST[merk]=='COMTEC') $select6='selected';
                    if ($_POST[merk]=='GREENCROSS') $select7='selected';
                    if ($_POST[merk]=='AMICUS') $select9='selected';	
		    if ($_POST[merk]=='Produk DEMO') $select8='selected';
		    
                    ?>
                    <option value="TERUMO" <?=$select2?>>TERUMO</option>
                    <option value="KARMI" <?=$select1?>>KARMI</option>
                    <option value="JMS" <?=$select3?>>JMS</option>
                    <option value="JML" <?=$select4?>>JML</option>
                    <option value="HLHAEMOPACK" <?=$select5?>>HLHAEMOPACK</option>
                    <option value="COMTEC" <?=$select6?>>COM.TECH</option>
                    <option value="GREENCROSS" <?=$select7?>>GREEN CROSS</option>
		    <option value="AMICUS" <?=$select8?>>AMICUS</option>	
                    <option value="Produk DEMO" <?=$select8?>>Produk DEMO</option>
		    
                </select>
            </td>
        </tr>
        <tr>
            <td style="font-size: 15px">JENIS KANTONG</td>
            <td class="styled-select">
                <select name="jenis">
                    <?
                    $select1=''; 	$select2='';	$select7='';
                    $select3='';	$select4='';
                    $select6='';	$select5='';
                    if ($_POST[jenis]=='1') $select1='selected';
                    if ($_POST[jenis]=='2') $select2='selected';
                    if ($_POST[jenis]=='3') $select3='selected';
                    if ($_POST[jenis]=='4') $select4='selected';
                    if ($_POST[jenis]=='5') $select5='selected';
                    if ($_POST[jenis]=='6') $select6='selected';
		    if ($_POST[jenis]=='7') $select7='selected';
                    ?>
                    <option value="1" <?=$select1?>>Single</option>
                    <option value="2" <?=$select2?>>Double</option>
                    <option value="3" <?=$select3?>>Triple</option>
                    <option value="4" <?=$select4?>>Quadruple</option>
                    <option value="5" <?=$select5?>>Quadruple T&B</option>
                    <option value="6" <?=$select6?>>Pediatrik</option>
		    <option value="7" <?=$select6?>>Quadruple T&T</option>	
                </select>
            </td>
        </tr>
<tr>
            <td style="font-size: 15px">Volume</td>
            <td class="input"><input name="vol" type="text" size="15"></td>
        </tr>
		<tr>
			<td style="font-size: 15px">Barat Kantong Utama</td>
			<td class="input"><input name="berat_ku" type="text" size="10"></td>
		</tr>
		<tr>
			<td colspan="2" style="font-size: 15px">Berat Kantong Satelite</td>
		</tr>
		<tr>
			<td style="font-size: 15px">Satelite 1</td>
			<td class="input"><input name="berat_s1" type="text" size="10"></td>
		</tr>
        <tr>
            <td style="font-size: 15px">Satelite 2</td>
            <td class="input"><input name="berat_s2" type="text" size="10"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 3</td>
            <td class="input"><input name="berat_s3" type="text" size="10"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 4</td>
            <td class="input"><input name="berat_s4" type="text" size="10"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 5</td>
            <td class="input"><input name="berat_s5" type="text" size="10"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 6</td>
            <td class="input"><input name="berat_s6" type="text" size="10"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 7</td>
            <td class="input"><input name="berat_s7" type="text" size="10"></td>
        </tr>
        
	</table>
    <p>Gunakan . (titik) untuk desimal</p>
	<button type="submit" value="Simpan" name="submit" class="swn_button_blue">Simpan</button>
    <a href="pmiqa.php?module=kantong_kosong"class="swn_button_blue">Kembali</a>
    </form>
    
<?}
?>
