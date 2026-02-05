<link type="text/css" href="css/blitzer/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
<?php
include('clogin.php');
include('config/db_connect.php');
//print_r($_GET);
//print_r($_POST);

if ($_GET[aksi]=='hapus'){
    $hapus=mysql_query("delete from bdrs where kode='$_GET[kode]'");
    if($hapus){
        echo ("Data bdrs telah dihapus !!
        <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=$PHP_SELF\">");
    }
}

if (isset($_POST[submit1])) {
    $_POST[submit1]="";
    $tambah=mysql_query("INSERT INTO `bdrs` (`kode`,`nama`) values ('$_POST[kode]','$_POST[nama]')");
}
if ($tambah) 
    echo ("Bdrs telah ditambah !!
    <meta HTTP-EQUIV=\"REFRESH\" CONTENT=\"2; URL=$PHP_SELF\">");

if ($_GET[aksi]=='edit'){
    $q_edit=mysql_query("select * from bdrs where kode='$_GET[kode]'");
    $a_edit=mysql_fetch_assoc($q_edit);
    ?>
    <h1 class="table">FORM BDRS</h1>
    <form method="post" action="<?php $PHP_SELF ?>" >
    <table class="form" cellspacing="1" cellpadding="2">
    <tr>
                <td>Nama Bdrs</td>
                <td class="input">
                    <input name="nama" type="text" size="30" value="<?=$a_edit[nama]?>">
                    <input name="kode" type="hidden" value="<?=$a_edit[kode]?>">
                </td>
    </tr>
    </table>
        <input border=0 type="submit" name="submit1" value="Simpan">
    </form>

<?php
    $del=mysql_query("delete from bdrs where `kode`='$_GET[kode]'");
}else {
    $q=mysql_fetch_assoc(mysql_query("select * from bdrs order by kode desc"));
	$q1=substr($q[kode],1);
	$q1=(int)$q1+1;
	$kode=$q1;
	if (strlen($q1)==1) $kode='0'.$q1;
	$kode='b'.$kode;
?>
    <h1 class="table">FORM BDRS</h1>
    <form method="post" action="<?php $PHP_SELF ?>" >
    <table class="form" cellspacing="1" cellpadding="2">
    <tr>
                <td>Nama Bdrs</td>
                <td class="input">
                    <input name="nama" type="text" size="30">
                    <input name="kode" type="hidden" value="<?=$kode?>">
                </td>
    </tr>
    </table>
                    <input border=0 type="submit" name="submit1" value="Simpan">
    </form> 
<?php    
}
?>
<br>
<br>
<table class="ui-widget ui-widget-content">
    <tr class="ui-widget-header">
        <th>No.</th><th>Nama</th><th>Aksi</th>
    </tr>
<?php
$q_dr=mysql_query("select * from bdrs");
$jum_bdrs=mysql_numrows($q_dr);
while($a_dr=mysql_fetch_assoc($q_dr)){
    $no++;
    echo "<tr>";
        echo "<td>".$no."</td>".
            "<td>".$a_dr[nama]."</td>";
		echo "<td>
				<ul id=\"icons\" class=\"ui-widget ui-helper-clearfix\">
                    <a href=\"".$PHP_SELF."?module=tambah_bdrs&aksi=hapus&kode=".$a_dr[kode]."\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Hapus\">
                        <span class=\"ui-icon ui-icon-closethick\"></span></li>
                    </a>
                    <a href=\"".$PHP_SELF."?module=tambah_bdrs&aksi=edit&kode=".$a_dr[kode]."\">
                        <li class=\"ui-state-default ui-corner-all\" title=\"Ubah\">
                        <span class=\"ui-icon ui-icon-pencil\"></span></li>
                    </a>
            </ul></td>";
    echo "</tr>";
}
?>
</table>