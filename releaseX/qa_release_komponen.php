<table class="list" border=1 cellpadding="5" cellspacing="5" width="100%" style="border-collapse:collapse">
    <tr style="background-color:mistyrose; font-size:16px; color:#000000;">
        <td>No</td>
        <td>ID</td>
        <td>Tanggal</td>
        <td>Jenis Komponen</td>
        <td>Metode</td>
        <td>Centrifugasi</td>
        <td>Petugas</td>
    </tr>

    <?php
    /**
    $a=mysql_query("SELECT  `notrans`,`pemisahan`, `putar`, `komponen`, `tglpembuatan`,
			case when `pemisahan`='0' then 'Manual' else 'Otomatis' end as metode,
			case
			when `putar`='RC1' then 'Centrifuge 1'
			when `putar`='RC2' then 'Centrifuge 2'
			when `putar`='RC3' then 'Centrifuge 3'
			when `putar`='RC4' then 'Centrifuge 4'
			when `putar`='RC5' then 'Centrifuge 5'
			when `putar`='' then 'Sedimentasi'
			end as pisah, `user` 
			FROM `hpengolahan` where `nokantong`='$nkt' order by tglpembuatan ASC");
    */
    $a=mysql_query("SELECT `NoTrans` AS 'notrans',`pisah`, `aPutar` as 'putar', `Produk` AS 'komponen', `tgl` as 'tglpembuatan',
                        case when `pisah`='0' then 'Manual' else 'Otomatis' end as metode,
                        `aPisah` as pisah, petugas as 'user' 
                        FROM `dpengolahan` where `noKantong`='$nkt' order by id ASC");
    $no=0;
    while($komp=mysql_fetch_assoc($a)){
    $nmLengkap = mysql_fetch_assoc(mysql_query("SELECT nama_lengkap FROM `user` WHERE id_user = '$komp[user]'"));
    $petugas = isset($nmLengkap['nama_lengkap']) ? $nmLengkap['nama_lengkap'] : $komp['user'];
    ?>
        <tr style="font-size:16px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <?$no++;?>
            <td align="right"><?php echo $no; ?></td>
            <td align="left"><?php echo $komp['notrans']; ?></td>
            <td><?php echo $komp['tglpembuatan']; ?></td>
            <td><?php echo $komp['komponen']; ?></td>
            <td><?php echo $komp['metode']; ?></td>
            <td><?php echo $komp['pisah']; ?></td>
            <td><?php echo $petugas; ?></td>
        </tr>
    <?php }
    if ($no=="0"){
        ?><tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="23" class=input align="center">TIDAK ADA DATA PENGOLAHAN DARAH</td>
        </tr>
    <?php } ?>
</table>
