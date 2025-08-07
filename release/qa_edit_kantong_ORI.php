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
<?php
session_start();
require_once('clogin.php');
require_once('config/db_connect.php');
$namauser=$_SESSION[namauser];
$namalengkap=$_SESSION[nama_lengkap];
$lvl0=$_SESSION['leveluser'];
$tglsebelum = mktime(0,0,0,date("m"),1,date("Y"));
$tglawal=date("Y-m-d");
$hariini = date("Y-m-d");
$no_id=$_GET['id'];

if((isset($_POST[submit])) and ($_POST[berat_ku]!=="")){
	$sql="UPDATE `master_kantong`
	      SET
          `ket`     ='$_POST[ket]',
	  `antikoagulant`='$_POST[acd]',
          `berat_ku`='$_POST[berat_ku]',
          `berat_s1`='$_POST[berat_s1]',
          `berat_s2`='$_POST[berat_s2]',
          `berat_s3`='$_POST[berat_s3]',
          `berat_s4`='$_POST[berat_s4]',
          `berat_s5`='$_POST[berat_s5]',
          `berat_s6`='$_POST[berat_s6]',
          `berat_s7`='$_POST[berat_s7]',
          `lama_buka`='$_POST[lama_buka]'
          WHERE
          `id`= '$no_id'";
	$sqladd=mysql_query($sql);
	
	if ($sqladd){
		echo "<br><br>Penambahan data berhasil</b><br>";

		echo "<meta http-equiv='refresh' content='2;url=pmi$lvl0.php?module=kantong_kosong'>";
	} else {
		echo "<br><br>Tidak Berhasil</b><br>";
		echo "<meta http-equiv='refresh' content='2;url=pmi$lvl0.php?module=kantong_kosong'>";
	}
    //=======Audit Trial====================================================================================
    $log_mdl ='PROLIS';
    $log_aksi='Merubah data Berat kantong kosong: '.$_POST[merk].' - '.$_POST[jenis];
    include_once "user_log.php";
    //=====================================================================================================
}else{
    $sql="SELECT `id`, `merk`, `vol`, `jenis`,
          case `jenis`
            when '1' then 'Single'
            when '2' then 'Double'
            when '3' then 'Tripple'
            when '4' then 'Quadrupple'
            when '5' then 'Quadrupple T&B'
            when '6' then 'Pediatrik'
            end as jeniskantong,  `ket`, `antikoagulant`,
            `berat_ku`, `berat_s1`, `berat_s2`, `berat_s3`, `berat_s4`, `berat_s5`, `berat_s6`, `berat_s7`, lama_buka
          FROM `master_kantong` WHERE id='$no_id'";
    $sql1=mysql_query($sql);
    $sq10=mysql_fetch_assoc($sql1);
    ?>
    <font size="4" color="red" face="Trebuchet MS"><b>EDIT MASTER BERAT KANTONG</b></font>
	<form name="setting" method="post" action="<? $PHP_SELF ?>">
	<table class="form" cellspacing="1" cellpadding="0" border="1">
        <tr>
            <td style="font-size: 15px">MERK</td>
            <td style="font-size: 15px"><?=$sq10['merk']?></td>
            <input type="hidden" name="merk" value="<?=$sq10['merk']?>">
        </tr>
        <tr>
            <td style="font-size: 15px">Lama Buka Kantong</td>
            <td class="input"><input name="lama_buka" type="text" size="5" value="<?=$sq10[lama_buka]?>"> Hari</td>
        </tr>
        <tr>
            <td style="font-size: 15px">JENIS KANTONG</td>
            <td style="font-size: 15px"><?=$sq10['jeniskantong']?></td>
            <input type="hidden" name="jenis" value="<?=$sq10['jeniskantong']?>">
        </tr>
        <tr>
            <td style="font-size: 15px">Volume</td>
            <td class="input"><input name="vol" type="text" size="15" value="<?=$sq10['vol']?>"></td>
        </tr>
	<tr>
            <td style="font-size: 15px">Antikoagulant</td>
            <td class="input"><input name="acd" type="text" size="15" value="<?=$sq10[antikoagulant]?>"></td>
        </tr>
		<tr>
			<td style="font-size: 15px">Barat Kantong Utama</td>
			<td class="input"><input name="berat_ku" type="text" size="10" value="<?=$sq10[berat_ku]?>"></td>
		</tr>
		<tr>
			<td colspan="2" style="font-size: 15px">Berat Kantong Satelite</td>
		</tr>
		<tr>
			<td style="font-size: 15px">Satelite 1</td>
			<td class="input"><input name="berat_s1" type="text" size="10" value="<?=$sq10[berat_s1]?>"></td>
		</tr>
        <tr>
            <td style="font-size: 15px">Satelite 2</td>
            <td class="input"><input name="berat_s2" type="text" size="10" value="<?=$sq10[berat_s2]?>"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 3</td>
            <td class="input"><input name="berat_s3" type="text" size="10" value="<?=$sq10[berat_s3]?>"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 4</td>
            <td class="input"><input name="berat_s4" type="text" size="10" value="<?=$sq10[berat_s4]?>"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 5</td>
            <td class="input"><input name="berat_s5" type="text" size="10" value="<?=$sq10[berat_s5]?>"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 6</td>
            <td class="input"><input name="berat_s6" type="text" size="10" value="<?=$sq10[berat_s6]?>"></td>
        </tr>
        <tr>
            <td style="font-size: 15px">Satelite 7</td>
            <td class="input"><input name="berat_s7" type="text" size="10" value="<?=$sq10[berat_s7]?>"></td>
        </tr>

	</table>
        <p>Gunakan . (titik) untuk desimal</p>
	<button type="submit" value="Simpan" name="submit" class="swn_button_blue">Simpan</button>
    <a href="pmi<?=$lvl0?>.php?module=kantong_kosong"class="swn_button_blue">Kembali</a>
    </form>
    
<?}
?>
