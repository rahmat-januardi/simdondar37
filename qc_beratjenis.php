<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/disable_enter.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/blitzer/suwena.css" rel="stylesheet" />
<link type="text/css" href="css/blitzer/jquery-ui-1.8.9m.custom.css" rel="stylesheet" />
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<script>
    $(".angka").on("keyup", function(){
        var valid = /^d{0,15}(.d{0,2})?$/.test(this.value),
            val = this.value;

        if(!valid){
            this.value = val.substring(0, val.length - 1);
        }
    });
</script>
<?php
  include('clogin.php');
  include('config/db_connect.php');
  session_start();
  $namauser=$_SESSION[namauser];
  
if ($_POST[submit]) {
   $beratjenis	= $_POST['beratjenis'];
   $suhu  		= $_POST['suhu'];
   $hari  		= $_POST['hari'];
   $jam  		= $_POST['jam'];
   $volmin 		= $_POST['volmin'];
   $volmax 		= $_POST['volmax'];
   $nama  	    = $_POST['nama'];
   $lengkap     = $_POST['lengkap'];
   $nomor  	    = $_POST['nomor'];
   $olah        = $_POST['olah'];
   $aftap       = $_POST['aftap'];
   for ($i=0;$i<count($nomor);$i++) {
    $simpan=mysql_query("update produk set
                        umurhari    ='$hari[$i]',
                        umurjam     ='$jam[$i]',
                        suhusimpan  ='$suhu[$i]',
                        beratjenis  ='$beratjenis[$i]',
                        vol_min     ='$volmin[$i]',
                        vol_max     ='$volmax[$i]',
                        waktu_pengolahan = '$olah[$i]',
                        lama_pengambilan = '$aftap[$i]'
                        where `no`='$nomor[$i]'");
  }
    //=======Audit Trial====================================================================================
    $log_mdl ='PROLIS';
    $log_aksi='Mengedit data berat jenis kantong';
    include_once "user_log.php";
    //=====================================================================================================
}
$sq=mysql_query("SELECT `Nama`, `no`, `kantongbaru`, `lengkap`, `umurhari`, `umurjam`, `suhusimpan`,
                `volume`, `beratjenis`, `vol_min`, `vol_max`,waktu_pengolahan,lama_pengambilan  FROM `produk` order by `no`");
?>
<h1>SETTING KOMPONEN DARAH</h1>
<form name='transaksi'  method='post' action='<?=$PHPSELF?>'>
    <table class="list" border="1" cellspacing="4" cellpadding="5" style="border-collapse:collapse">
      <tr class="field">
		<td align="center" rowspan="2">NO</td>
		<td align="center" rowspan="2">NAMA KOMPONEN DARAH</td>
		<td align="center" rowspan="2">SINGKATAN</td>
		<td align="center" rowspan="2">BERAT JENIS</td>
		<td align="center" rowspan="2">SUHU SIMPAN<br>(<sup>o</sup>C)</td>
		<td align="center" colspan="2">MASA KADALUARSA</td>
		<td align="center" colspan="2">VOLUME RELEASE (ml)</td>
          <td align="center" colspan="2">WAKTU</td>
      </tr> 
      <tr class="field">
		<td align="center">HARI</td>
		<td align="center">JAM</td>
		<td align="center">MINIMAL</td>
		<td align="center">MAKSIMAL</td>
        <td align="center">AFTAP (menit)</td>
        <td align="center">PENGOLAHAN (Jam)</td>
      </tr>
      <?
      $no=0;
      while ($dtrans = mysql_fetch_assoc($sq)){
	  $no++; ?>
	  <tr class='record'>
    		<td align=center><?=$dtrans['no']?></td><input type=hidden name='nomor[]' value=<?=$dtrans['no']?>>
		    <td align=left><?=$dtrans['lengkap']?></td><input type=hidden name='namalengkap[]' value=<?=$dtrans['lengkap']?>>
		    <td align=left><?=$dtrans['Nama']?></td><input type=hidden name='nama[]' value=<?=$dtrans['Nama']?>>
			<td class='input'><input name='beratjenis[]' type='text' size='7'   value=<?=$dtrans['beratjenis']?>  style='text-align:right'></td>
			<td class='input'><input name='suhu[]' type='text' size='7'         value=<?=$dtrans['suhusimpan']?> style='text-align:right'></td>
			<td class='input'><input name='hari[]' type='text' size='5'         value=<?=$dtrans['umurhari']?> style='text-align:right'></td>
			<td class='input'><input name='jam[]' type='text' size='5'          value=<?=$dtrans['umurjam']?> style='text-align:right'></td>
			<td class='input'><input name='volmin[]' type='text' size='6'       value=<?=$dtrans['vol_min']?> style='text-align:right'></td>
			<td class='input'><input name='volmax[]' type='text' size='6'       value=<?=$dtrans['vol_max']?> style='text-align:right'></td>
            <td class='input'><input name='aftap[]' type='text' size='3'        value=<?=$dtrans['lama_pengambilan']?> style='text-align:right'></td>
            <td class='input'><input name='oleh[]' type='text' size='3'         value=<?=$dtrans['waktu_pengolahan']?> style='text-align:right'></td>
		</tr>
  <?}?>
</table>
<p>Gunakan . (titik) untuk desimal</p>
  <br>
<input name="submit" type="submit" value="Simpan" class="swn_button_blue">
<a href="pmiqa.php?module=input_qa"class="swn_button_blue">Kembali</a>
<a href="pmiqa.php?module=cetak_density"class="swn_button_blue">Cetak</a>
</form>


  

