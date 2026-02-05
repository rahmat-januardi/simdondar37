<HEAD>
<script language=javascript src="./js/terimakantong.js" type="text/javascript"> </script>
<script language=javascript src="./js/util.js" type="text/javascript"> </script>
<script language="javascript" src="./js/AjaxRequest.js" type="text/javascript"></script>
<style type="text/css">
@import url("css/stok_darah.css");
</style>
 <script language="javascript">
function setFocus(){document.tambahkantong.nokantong.focus();}
</script>
<SCRIPT LANGUAGE="JavaScript" SRC="CalendarPopup.js"></SCRIPT>

<!-- This javascript is only used for the show/hide source on my example page.
     It is not used by the Calendar Popup script -->
<SCRIPT LANGUAGE="JavaScript" SRC="common.js"></SCRIPT>

<!-- This prints out the default stylehseets used by the DIV style calendar.
     Only needed if you are using the DIV style popup -->
<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>

<!-- These styles are here only as an example of how you can over-ride the default
     styles that are included in the script itself. -->
<SCRIPT LANGUAGE="JavaScript" ID="jscal1xx">
var cal1xx = new CalendarPopup("testdiv1");
cal1xx.showNavigationDropdowns();
</SCRIPT>
<link href="modul/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
 <script language="javascript" src="js/jquery.js"></script>
 <script language="javascript" src="modul/thickbox/thickbox.js"></script>
	
<script language="javascript">
function selectutd(id){
	  $('input[@name=kodeSup]').val(id);
		tb_remove(); 
}
</script>
<STYLE>
        .TESTcpYearNavigation,
        .TESTcpMonthNavigation
                        {
                        background-color:#6677DD;
                        text-align:center;
                        vertical-align:center;
                        text-decoration:none;
                        color:#FFFFFF;
                        font-weight:bold;
                        }
        .TESTcpDayColumnHeader,
        .TESTcpYearNavigation,
        .TESTcpMonthNavigation,
        .TESTcpCurrentMonthDate,
        .TESTcpCurrentMonthDateDisabled,
 .TESTcpOtherMonthDate,
        .TESTcpOtherMonthDateDisabled,
        .TESTcpCurrentDate,
        .TESTcpCurrentDateDisabled,
        .TESTcpTodayText,
        .TESTcpTodayTextDisabled,
        .TESTcpText
                        {
                        font-family:arial;
                        font-size:8pt;
                        }
        TD.TESTcpDayColumnHeader
                        {
                        text-align:right;
                        border:solid thin #6677DD;
                        border-width:0 0 1 0;
                        }
        .TESTcpCurrentMonthDate,
        .TESTcpOtherMonthDate,
        .TESTcpCurrentDate
                        {
                        text-align:right;
                        text-decoration:none;
                        }
        .TESTcpCurrentMonthDateDisabled,
        .TESTcpOtherMonthDateDisabled,
        .TESTcpCurrentDateDisabled
                        {
                        color:#D0D0D0;
       text-align:right;
                        text-decoration:line-through;
                        }
        .TESTcpCurrentMonthDate
                        {
                        color:#6677DD;
                        font-weight:bold;
                        }
        .TESTcpCurrentDate
                        {
                        color: #FFFFFF;
                        font-weight:bold;
                        }
        .TESTcpOtherMonthDate
                        {
                        color:#808080;
                        }
        TD.TESTcpCurrentDate
                        {
                        color:#FFFFFF;
                        background-color: #6677DD;
                        border-width:1;
                        border:solid thin #000000;
                        }
        TD.TESTcpCurrentDateDisabled
                        {
                        border-width:1;
                        border:solid thin #FFAAAA;
                        }
TD.TESTcpTodayText,
        TD.TESTcpTodayTextDisabled
                        {
                        border:solid thin #6677DD;
                        border-width:1 0 0 0;
                        }
        A.TESTcpTodayText,
        SPAN.TESTcpTodayTextDisabled
                        {
                        height:20px;
                        }
        A.TESTcpTodayText
                        {
                        color:#6677DD;
                        font-weight:bold;
                        }
        SPAN.TESTcpTodayTextDisabled
                        {
                        color:#D0D0D0;
                        }
        .TESTcpBorder
                        {
                        border:solid thin #6677DD;
                        }
</STYLE>
<style>
   body,table,input{
   	font-size:12px
   }
 </style>
</HEAD>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />

<?
include('clogin.php');
include('config/db_connect.php');
$namauser=$_SESSION[namauser];
$nkt1="";

if (isset($_POST[submit])) {
	for ($i=0; $i<sizeof($_POST[merk1]); $i++) {
		$nmr=$_POST[merk1][$i]; 				$njn=$_POST[jenis1][$i];	
		$nst=$_POST[status]; 					$nvo=$_POST[volume1][$i];
		$nkt=$_POST[no_kantong][$i];				$nkt1 .=$nkt.",";
		$nkt=ereg_replace("[^A-Za-z0-9]", "",strtoupper($nkt));	$prod=$_POST[produk];
		$today=date("Y-m-d H:i:s");					$utd=$_POST[kodeSup];
		$gol=$_POST[gol1][$i];					$tglaftap=$_POST[tglaftap1][$i];
		$exp=$_POST[tglkad1][$i];					$rh=$_POST[rh1][$i];
       		 $tambah=mysql_query("insert into
				stokkantong (noKantong,jenis,Status,tglTerima,volume,merk,volumeasal,produk,gol_darah,RhesusDrh,AsalUTD,tgl_Aftap,kadaluwarsa,sah,stattempat,
		statkonfirmasi,tglperiksa,tglpengolahan,hasil) 
				values ('$nkt','$njn','$nst','$today','$nvo','$nmr','$nvo','$prod','$gol','$rh','$utd','$tglaftap','$exp','1','1','1','$tglaftap','$tglaftap','$nst') ");
	$lap_tambah=mysql_query("insert into terimaudd (nokantong,udd,tgl,petugas)values('$nkt','$utd','$today','$namauser')");
	}
	if ($tambah) {
        echo "Data Telah berhasil dimasukkan. ";?>
	<?}
} ?>
	<body onLoad=setFocus()>
	<form name="tambahkantong" onsubmit="return ok()" method="POST" action="<?=$PHPSELF?>">
	<table align=top>
	<tr>
			<td>
<INPUT type="button" value="Delete Row" onclick="deleteRow('box-table-b')" />
				<input name="submit" type="submit" value="Simpan">
<!--input type="button" value="Add" onclick="addRow('box-table-b');"-->
			</td>
			<td> </td>
		</tr>
		<tr>
			<td valign=top>
				<table class="form" border="0" align=top>
					<tr>
						<td>Merk</td>
						<td class="input">
							<select name="merk">
							<?
							$select1='';	$select2='';
							$select3='';	$select4='';
							if ($_POST[merk]=='KARMI') $select1='selected';
							if ($_POST[merk]=='TERUMO') $select2='selected';
							if ($_POST[merk]=='JMS') $select3='selected';
							if ($_POST[merk]=='iControl') $select11='selected';
							if ($_POST[merk]=='JML') $select4='selected';
							if ($_POST[merk]=='COMPOFLEX') $select5='selected';
							if ($_POST[merk]=='GREENCROSS') $selected6='selected';
							if ($_POST[merk]=='HAEMONETIC') $select7='selected';
							if ($_POST[merk]=='Amicore') $select8='selected';
							if ($_POST[merk]=='COM.TECH') $select9='selected';
							if ($_POST[merk]=='Produk Demo') $select10='selected';
							?>
							<option value="iControl" <?=$select11?>>iControl</option>
							<option value="KARMI" <?=$select1?>>KARMI</option>
							<option value="TERUMO" <?=$select2?>>TERUMO</option>
							<option value="JMS" <?=$select3?>>JMS</option>
							<option value="JML" <?=$select4?>>JML</option>
							<option value="COMPOFLEX" <?=$select5?>>COMPOFLEX</option>
							<option value="GREENCROSS" <?=$select6?>>GREENCROSS</option>
							<option value="HAEMONETIC" <?=$select7?>>HAEMONETIC</option>
							<option value="Amicore" <?=$select8?>>AMICORE</option>
							<option value="Com.Tech" <?=$select9?>>COM.TECH</option>
							<option value="Produk Demo" <?=$select10?>>Produk DEMO</option>
							
							</select>
						</td>
					</tr>
					
						<tr> 
			<td>Jenis Darah</font></td>
			<td class="input">
				<select name="produk" >
					<option selected>WB</option>
					<?php
						$permintaan1="select * from produk";
						$do1=mysql_query($permintaan1);
						while($data1=mysql_fetch_assoc($do1)){
							$select1="";?>
					<option value="<?=$data1[Nama]?>"<?=$select1?>>
						<?=$data1[Nama]?>
					</option>
						<?}?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Golongan Darah</td>
			<td class="input">
				<select name="goldarah">
					<option value="O">O</option>
					<option value="A">A</option>
					<option value="B">B</option>
					<option value="AB">AB</option>
					</select>
			</td>
		</tr>

		<tr>
			<td>Rhesus Darah</td>
			<td class="input">
				<select name="rh">
					<option value="+">Positip</option>
					<option value="-">Negatip</option>
					</select>
			</td>
		</tr>

					<tr>
						<td>Volume</td>	
						<td class="input">
							<select name="volume" >
							<option value="350"  selected> 350 CC</option>
							<option value="250">250 CC</option>
							<option value="450">450 CC</option>
							</select>
						</td>
					<tr>
						<td>Jenis Kantong</td>
						<td class="input">
							<select name="jenis">
							<?
							$select1=''; 	$select2='';
							$select3='';	$select4='';
							$select6='';
							if ($_POST[jenis]=='1') $select1='selected';
							if ($_POST[jenis]=='2') $select2='selected';
							if ($_POST[jenis]=='3') $select3='selected';
							if ($_POST[jenis]=='4') $select4='selected';
							if ($_POST[jenis]=='6') $select6='selected';
							?>
							<option value="1" <?=$select1?>>Single</option>
							<option value="2" <?=$select2?>>Double</option>
							<option value="3" <?=$select3?>>Triple</option>
							<option value="4" <?=$select4?>>Quadruple</option>
							<option value="6" <?=$select6?>>Pediatrik</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Status</td>	
						<td class="input">
							<select name="status" >
							<option value="2"  selected>Sehat</option>
							<option value="1">Karantina</option>
							</select>
						</td>
					</tr>

	<tr>
<td>Kode UDD Asal</td>
	<td class="input"><input name="kodeSup" type="text" size="5" >Klik<a href="modul/daftar_udd.php?&width=500&height=400" class="thickbox"><img src="images/button_search.png" border="0" /></a>untuk lihat kode</td>
	</tr>
	<!--td>Tgl Aftap th-bln-tgl</td>
						<td class="input"><INPUT size=15 type="text"  name="tglaftap" >
						</td>
					</tr>
					<!--tr>
						<td>Tgl Kadaluarsa th-bln-tgl</td>
						<td class="input"><INPUT size=15 type="text"  name="tglexp" >
						</td>
					</tr-->
<td>Tgl Aftap</td>
        <td class="input"><INPUT TYPE="text" NAME="tglaftap" VALUE="" SIZE=8>
<A HREF="#" onClick="cal1xx.select(document.forms[0].tglaftap,'anchor1xx','yyyy-MM-dd'); return false;" TITLE="cal1xx.select(document.forms[0].tglaftap,'anchor1xx','yyyy-MM-dd'); return false;" NAME="anchor1xx" ID="anchor1xx">klik</A></td>
    </tr>

<td>Tgl Kadaluarsa</td>
        <td class="input"><INPUT TYPE="text" NAME="tglkad" VALUE="" SIZE=8>
<A HREF="#" onClick="cal1xx.select(document.forms[0].tglkad,'anchor1xx','yyyy-MM-dd'); return false;" TITLE="cal1xx.select(document.forms[0].tglkad,'anchor1xx','yyyy-MM-dd'); return false;" NAME="anchor1xx" ID="anchor1xx">klik</A></td>
    </tr>

					<tr>
						<td>Jumlah Cetak</td>
						<? if (!isset($_POST[cetakkantong])) $_POST[cetakkantong]='4';?>
						<td class="input"><INPUT size=2 type="text"  name="cetakkantong" id="cetakkantong" value="<?=$_POST[cetakkantong]?>">
						</td>
					</tr>

					<tr>
						<td>No Kantong</td>
						<td class="input"><INPUT type="text"  name="nokantong" id="nokantong"  placeholder="tanpa pakai A   ENTER"
							onkeydown="chang(event,this);" onchange="cari_kantong('box-table-b');">
						</td>
					</tr>
				</table>
			</td>
			<td valign=top>
				<table class="list" id="box-table-b" width=350px align=top>
					<tr class="field">
						<td align='center'></td>
						<td align='center'>No</td>
						<td align='center'>No Kantong</td>
						<td align='center'>Volume</td>
						<td align='center'>Merk</td>
						<td align='center'>Jenis</td>
						<td align='center'>Tgl Aftap</td>
						<td align='center'>Tgl Kadaluarsa</td>
						<td align='center'>Gol Darah</td>
						<td align='center'>Rhesus</td>
					</tr>
				</table>
			<!--<INPUT type="button" value="Delete Row" onclick="deleteRow('box-table-b')" />

<input name="submit" type="submit" value="Simpan">
<!--<input type="button" value="Add" onclick="addRow('box-table-b');">--> 
			</td>


		</tr>
	
	</table>
<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
</form>
	
