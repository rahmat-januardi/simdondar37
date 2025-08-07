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
          (`merk`, `jenis`, `vol`, `berat_ku`, `berat_s1`, `berat_s2`, `berat_s3`, `berat_s4`, `berat_s5`, `berat_s6`, `berat_s7`, `lama_buka`,  `antikoagulant`)
          VALUES
          ('$_POST[merk]','$_POST[jenis]', '$_POST[vol]','$_POST[berat_ku]','$_POST[berat_s1]','$_POST[berat_s2]','$_POST[berat_s3]','$_POST[berat_s4]',
          '$_POST[berat_s5]','$_POST[berat_s6]','$_POST[berat_s7]','$_POST[lama_buka]','$_POST[acd]')";
	$sqladd=mysql_query($sql);
	
	if ($sqladd){
        //=======Audit Trial====================================================================================
        $log_mdl ='PROLIS';
        $log_aksi='Menambah data Berat kantong kosong: '.$_POST[merk].' - '.$_POST[jenis];
        include_once "user_log.php";
        //=====================================================================================================
		echo "<br><br>Penambahan data berhasil</b><br>";
	switch ($_SESSION[leveluser]){
				case "logistik":
				    echo "<meta http-equiv=\"refresh\" content=\"1; URL=pmilogistik.php?module=kantong_kosong\">";
					break;
				default:
				    echo "<meta http-equiv=\"refresh\" content=\"1; URL=pmiqa.php?module=kantong_kosong\">";
				}

	} else {
		echo "<br><br>Update Sukses!</b><br>";
	switch ($_SESSION[leveluser]){
				case "logistik":
				    echo "<meta http-equiv=\"refresh\" content=\"1; URL=pmilogistik.php?module=kantong_kosong\">";
					break;
				default:
				   echo "<meta http-equiv=\"refresh\" content=\"1; URL=pmiqa.php?module=kantong_kosong\">";
				}
		
	}
}else{?>
    <font size="4" color="red" face="Trebuchet MS"><b>TAMBAH MASTER BERAT KANTONG</b></font>
	<form name="setting" method="post" action="<? $PHP_SELF ?>">
	<table class="form" cellspacing="1" cellpadding="0" border="1">
        <tr>
            <td style="font-size: 15px">MERK</td>
            <td class="styled-select">
                <select name="merk">
                    <?php
                    $select1='';	$select2='';	$select3='';
                    $select4='';	$select5='';	$select6='';
                    $select7='';	$select8='';	$select9='';	
                    $select10='';	$select11='';	$select12='';	
                    $select13='';	
                    if ($_POST[merk]=='KARMI') $select1='selected';
                    if ($_POST[merk]=='TERUMO') $select2='selected';
                    if ($_POST[merk]=='iControl') $select3='selected';
                    if ($_POST[merk]=='JMS') $select4='selected';
                    if ($_POST[merk]=='JML') $select5='selected';
                    if ($_POST[merk]=='HLHAEMOPACK') $select6='selected';
                    if ($_POST[merk]=='COMTEC') $select7='selected';
                    if ($_POST[merk]=='GREENCROSS') $select8='selected';
                    if ($_POST[merk]=='DEMOTEK') $select9='selected';
                    if ($_POST[merk]=='AMICUS') $select10='selected';
                    if ($_POST[merk]=='COMPOFLEX') $select11='selected';	
                    if ($_POST[merk]=='HAEMONETICS') $select12='selected';
                    if ($_POST[merk]=='Produk Demo') $select13='selected';
                    if ($_POST[merk]=='ZONTIC') $select14='selected';
                    ?>
                        <option value="KARMI" <?=$select1?>>KARMI</option>
			<option value="TERUMO" <?=$select2?>>TERUMO</option>
                        <option value="IControl" <?=$select3?>>IControl</option>
                        <option value="JMS" <?=$select4?>>JMS</option>
                        <option value="JML" <?=$select5?>>JML</option>
                        <option value="HLHAEMOPACK" <?=$select6?>>HLHAEMOPACK</option>
                        <option value="COMTEC" <?=$select7?>>COM.TECH</option>
                        <option value="GREENCROSS" <?=$select8?>>GREEN CROSS</option>
                        <option value="ZONTIC" <?=$select14?>>ZONTIC</option>
                        <option value="DEMOTEK" <?=$select9?>>DEMOTEK</option>
                        <option value="AMICUS" <?=$select10?>>DEMOTEK</option>
                        <option value="COMPOFLEX" <?=$select11?>>COMPOFLEX</option>
                        <option value="HAEMONETICS" <?=$select12?>>HAEMONETICS</option>
                        <option value="Produk DEMO" <?=$select13?>>Produk DEMO</option>
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
                    <option value="8" <?=$select8?>>Quintuple</option>
                </select>
            </td>
        </tr>
		<tr>
            		<td style="font-size: 15px">Lama Buka</td>
            		<td class="input"><input name="lama_buka" type="text" size="15"> Hari</td>
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
		    <td style="font-size: 15px">Antikoagulant</td>
		    <td class="input"><input name="acd" type="text" size="15"></td>
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
	<? switch ($_SESSION[leveluser]){
				case "logistik":?>
				    <a href="pmilogistik.php?module=kantong_kosong"class="swn_button_blue">Kembali</a>
					<?break;
				default:?>
				    <a href="pmiqa.php?module=kantong_kosong"class="swn_button_blue">Kembali</a>
				<?}?>
    
    </form>
    
<?}
?>
