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

    <?
    $a=mysql_query("SELECT  `id`, `noKantong`, `Produk`, `tgl`,
                        case when `cara`='0' then 'Manual' else 'Otomatis' end as cara,
                        case
                            when `pisah`='0' then 'Centrifuge 1'
                            when `pisah`='1' then 'Centrifuge 2'
                            when `pisah`='2' then 'Centrifuge 3'
                            when `pisah`='3' then 'Centrifuge 4'
                            when `pisah`='4' then 'Centrifuge 5'
                            when `pisah`='5' then 'Sedimentasi'
                         end as pisah, `petugas`
                        FROM `dpengolahan` where `noKantong`='$nkt' order by tgl ASC");
    $no=0;
    while($komp=mysql_fetch_assoc($a)){?>
        <tr style="font-size:16px; color:#000000; font-family:Verdana;" onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'">
            <?$no++;?>
            <td align="right"><?=$no?></td>
            <td align="right"><?=$komp[id]?>.</td>
            <td><?=$komp[tgl]?></td>
            <td><?=$komp[Produk]?></td>
            <td><?=$komp[cara]?></td>
            <td><?=$komp[pisah]?></td>
            <td><?=$komp[petugas]?></td>
        </tr>
    <?}?>
    <?
    if ($no=="0"){
        ?><tr style="color:#000000;" onMouseOver="this.className='highlight';" onMouseOut="this.className='normal';">
            <td colspan="23" class=input align="center">TIDAK ADA DATA PENGOLAHAN DARAH</td>
        </tr>
    <?}
    ?>
</table>