
<body>
	


<?
require_once('config/db_connect.php');
if ($_POST['submit']!=""){
$STOKA=$_POST['STOKA'];
$STOKB=$_POST['STOKB'];
$STOKO=$_POST['STOKO'];
$STOKAB=$_POST['STOKAB'];
$ANTRIANA=$_POST['ANTRIANA'];
$ANTRIANB=$_POST['ANTRIANB'];
$ANTRIANO=$_POST['ANTRIANO'];
$ANTRIANAB=$_POST['ANTRIANAB'];

    
$query = mysql_query("INSERT INTO `STOK_DARAH_PK`(`stoka`, `stokb`, `stoko`, `stokab`, `antriana`, `antrianb`, `antriano`, `antrianab`) VALUES ('$STOKA','$STOKB','$STOKO','$STOKAB','$ANTRIANA','$ANTRIANB','$ANTRIANO','$ANTRIANAB')");

if($query){echo "berhasil di update<br>";}
else {echo "gagal diupdate<br>";}

$NOW=date('Y-m-d H:i:S');
$db_host = 'pmikotasemarang.or.id'; // Nama Server Website
$db_user = 'u7934619_wp884'; // User Server
$db_pass = 'UB-2@2S54p'; // Password Server
$db_name = 'u7934619_wp884'; // Nama Database

$conn_web = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn_web) {
	die ('Gagal terhubung MySQL: ' . mysqli_connect_error());	
}
$sql_pk = "INSERT INTO `STOK_DARAH_PK`(`waktu_insert`,`stoka`, `stokb`, `stoko`, `stokab`, `antriana`, `antrianb`, `antriano`, `antrianab`) VALUES ('$NOW','$STOKA','$STOKB','$STOKO','$STOKAB','$ANTRIANA','$ANTRIANB','$ANTRIANO','$ANTRIANAB')";
$query = mysqli_query($conn_web,$sql_pk);
if (!$query) {
	die ('KONEKSI KE pmikotasemarang.or.id GAGAL !!!' . mysqli_error($conn_web));
}
else{
	echo 'Data Stok Darah PK Berhasil Di Upload Ke pmikotasemarang.or.id'.PHP_EOL;
}
}
?>
<font size="4" color="red" font-family="Arial">UPDATE STOK DARAH UNTUK STOK DAN ANTRIAN PLASMA KONVALESEN</font></br>
<form name="update_stok" method="POST" action="<?=$PHPSELF?>">
<table border="1" class="form" cellspacing="2" cellpadding="2" align="left">
<tr>
<td style="font-size:16px;align:center">KETERANGAN</td>
<td style="font-size:16px;align:center">A</td>
<td style="font-size:16px;align:center">B</td>
<td style="font-size:16px;align:center">O</td>
<td style="font-size:16px;align:center">AB</td>
</tr>
<tr>
<td style="font-size:16px">STOK DARAH</td>
<td><input type='number' name="STOKA" id="STOKA" min="0" required></input></td>
<td><input type='number' name="STOKB" id="STOKB" min="0" required></input></td>
<td><input type='number' name="STOKO" id="STOKO" min="0" required></input></td>
<td><input type='number' name="STOKAB" id="STOKAB" min="0" required></input></td>
</tr>
<tr>
<td style="font-size:16px">ANTRIAN PASIEN</td>
<td><input type='number' name="ANTRIANA" id="ANTRIANA" min="0" required></input></td>
<td><input type='number' name="ANTRIANB" id="ANTRIANB" min="0" required></input></td>
<td><input type='number' name="ANTRIANO" id="ANTRIANO" min="0" required></input></td>
<td><input type='number' name="ANTRIANAB" id="ANTRIANAB" min="0" required></input></td>
</tr>
<tr>
<td colspan="5"><input type='submit' name="submit" value="UPDATE" id="submit" style="background-color:Chartreuse;color:black;width:120px;height:40px;"></input>	</td>
</tr>
</table>
</br>

</form>
</body>

