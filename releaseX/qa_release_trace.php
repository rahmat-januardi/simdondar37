   <table cellpadding=5 cellspacing=5 width="100%">
    <tr style="background-color:mistyrose; font-size:12px; color:#000000;">
        <th align="center">Tanggal</th>
        <th align="center">Jam</th>
        <th align="center">Modul</th>
        <th align="center">Proses</th>
        <th align="center">Personil</th>
    </tr>
    <?
    $waktu_bukakantong='';
    $waktu_aftap='';
    $waktu_komponen='';
    $kantongke =strtoupper(substr($nkt, -1));
    $no_kantonga=substr_replace($nkt,'A',-1,1);
    //barcode
    $a1="SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
		      	 user_log.user, user_log.komputer,user_log.time_aksi,
		      	 case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
		      	 user_log.modul, user_log.aksi_user, `user`.nama_lengkap
		         from user_log left join `user` on `user`.`id_user`=user_log.user
		         where user_log.aksi_user LIKE '%$no_kantonga%' and user_log.aksi_user LIKE  'Barcode%' order by time_aksi ASC";
    //echo $a1;
    $a=mysql_query($a1);
    while($komp=mysql_fetch_assoc($a)){
        $waktu_bukakantong=$komp['time_aksi'];
	?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td><?=$komp[tgl_aksi]?></td>
            <td><?=$komp[jam_aksi]?></td>
            <td><?=$komp[modul]?></td>
            <td><?=$komp[tempat].$komp[aksi_user]?></td>
            <td><?=$komp[nama_lengkap]?></td>
        </tr>
    <?}?>
    <?
    //mutasi kantong ke aftap
    $a1="SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
		      	 user_log.user, user_log.komputer,
		      	 case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
		      	 user_log.modul, user_log.aksi_user, `user`.nama_lengkap
		         from user_log left join user on `user`.`id_user`=user_log.user
		         where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%Pengesahan Kantong Logistik%' order by time_aksi ASC";
    $a=mysql_query($a1);
    while($komp=mysql_fetch_assoc($a)){?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td><?=$komp[tgl_aksi]?></td>
            <td><?=$komp[jam_aksi]?></td>
            <td><?=$komp[modul]?></td>
            <?
            if ($kantongke=='A'){
                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
            } else {
                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
            }
            ?>
            <td><?=$komp[nama_lengkap]?></td>
        </tr>
    <?}?>
    <?
    //Pengambilan Darah
    $a1="SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
		      	 user_log.user, user_log.komputer,user_log.time_aksi,
		      	 case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
		      	 user_log.modul, user_log.aksi_user, `user`.nama_lengkap
		         from user_log left join user on `user`.`id_user`=user_log.user
		         where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%Pengambilan%' order by time_aksi ASC";
    $a=mysql_query($a1);
    while($komp=mysql_fetch_assoc($a)){
        $waktu_aftap=$komp['time_aksi'];?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td><?=$komp[tgl_aksi]?></td>
            <td><?=$komp[jam_aksi]?></td>
            <td><?=$komp[modul]?></td>
            <?
            if ($kantongke=='A'){
                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
            } else {
                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
            }
            ?>
            <td><?=$komp[nama_lengkap]?></td>
        </tr>
    <?}?>
    <?
    //Pengesahan ke karantina
    $a1="SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
             user_log.user, user_log.komputer,
             case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
             user_log.modul, user_log.aksi_user, `user`.nama_lengkap
             from user_log left join user on `user`.`id_user`=user_log.user
             where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%Pengesahan kantong aftap%' order by time_aksi ASC";
    $a=mysql_query($a1);
    while($komp=mysql_fetch_assoc($a)){?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td><?=$komp[tgl_aksi]?></td>
            <td><?=$komp[jam_aksi]?></td>
            <td><?=$komp[modul]?></td>
            <?
            if ($kantongke=='A'){
                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
            } else {
                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
            }
            ?>
            <td><?=$komp[nama_lengkap]?></td>
        </tr>
    <?}?>
    <?
    //KGD
    $a1="SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
             user_log.user, user_log.komputer,user_log.time_aksi,
             case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
             user_log.modul, user_log.aksi_user, `user`.nama_lengkap
             from user_log left join user on `user`.`id_user`=user_log.user
             where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%KGD%' order by time_aksi ASC";
    $a=mysql_query($a1);
    while($komp=mysql_fetch_assoc($a)){?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td><?=$komp[tgl_aksi]?></td>
            <td><?=$komp[jam_aksi]?></td>
            <td><?=$komp[modul]?></td>
            <?
            if ($kantongke=='A'){
                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
            } else {
                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
            }
            ?>
            <td><?=$komp[nama_lengkap]?></td>
        </tr>
    <?}?>
    <?
    //IMLTD
    $a1="SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
             user_log.user, user_log.komputer,
             case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
             user_log.modul, user_log.aksi_user, `user`.nama_lengkap
             from user_log left join user on `user`.`id_user`=user_log.user
             where user_log.aksi_user like '%$no_kantonga%' and user_log.aksi_user like '%IMLTD%' order by time_aksi ASC";
    $a=mysql_query($a1);
    while($komp=mysql_fetch_assoc($a)){?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td><?=$komp[tgl_aksi]?></td>
            <td><?=$komp[jam_aksi]?></td>
            <td><?=$komp[modul]?></td>
            <?
            if ($kantongke=='A'){
                echo "<td>$komp[tempat]$komp[aksi_user]</td>";
            } else {
                echo "<td>Kantong Utama : $komp[tempat]$komp[aksi_user]</td>";
            }
            ?>
            <td><?=$komp[nama_lengkap]?></td>
        </tr>
    <?}
    //Penngolahan
    $a1="SELECT DATE_FORMAT(user_log.time_aksi, '%H:%i') as jam_aksi, DATE_FORMAT(user_log.time_aksi, '%d/%m/%Y') as tgl_aksi,
             user_log.user, user_log.komputer, user_log.time_aksi,
             case when SUBSTRING(user_log.tempat, 1, 1)='M' then 'Mobile Unit-' else '' end as tempat,
             user_log.modul, user_log.aksi_user, `user`.nama_lengkap
             from user_log left join user on `user`.`id_user`=user_log.user
             where user_log.aksi_user like '%$nkt%' and user_log.aksi_user like '%Pengolahan%' order by time_aksi ASC";
    $a=mysql_query($a1);
    while($komp=mysql_fetch_assoc($a)){
        $waktu_komponen=$komp['time_aksi'];?>
        <tr style="font-size:12px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <td><?=$komp[tgl_aksi]?></td>
            <td><?=$komp[jam_aksi]?></td>
            <td><?=$komp[modul]?></td>
            <td><?=$komp[tempat].$komp[aksi_user]?></td>
            <td><?=$komp[nama_lengkap]?></td>
        </tr>
    <?php }?>
    <?php
    $lama_kantong_buka = strtotime($waktu_aftap) - strtotime($waktu_bukakantong);
    $lama_hari_buka    = floor($lama_kantong_buka / (60 * 60 * 24))+1;
    $lama_diolah       = strtotime($waktu_komponen) - strtotime($waktu_aftap);
    $lama_jam_diolah   = floor($lama_diolah /(60*60));
    $lama_menit_diolah = $lama_diolah-$lama_jam_diolah * (60*60);
    $lama_menit_diolah = floor($lama_menit_diolah/60);
    
    ?>
    <tr><td colspan="5"></td></tr>
    <tr>
        <td colspan="4">Lama kantong terbuka (dari barcode sampai pengambilan darah)</td>
        <td colspan="1"><?=$lama_hari_buka?> hari</td>
    </tr>
    <tr>
        
	<? 
		$cariktg=mysql_fetch_assoc(mysql_query("select produk from stokkantong where noKantong='$nkt'"));
		$asalktg=mysql_fetch_assoc(mysql_query("select kantongAsal from stokkantong where noKantong='$nkt'"));
		if($asalktg[kantongAsal]==NULL){		
		if($cariktg[produk]=='WB'){?>
		<td colspan="4">Waktu pengambilan sampai dengan selesai pembuatan komponen</td>
		<td colspan="1">Tidak Ada Pengolahan Komponen</td>
		<?}else{?>
		<td colspan="4">Waktu pengambilan sampai dengan selesai pembuatan komponen</td>
		<td colspan="1"><?=$lama_jam_diolah?> Jam, <?=$lama_menit_diolah?> mnt</td>        
		<?}}else{?>
		
		<?}?>
	
    </tr>
</table>
