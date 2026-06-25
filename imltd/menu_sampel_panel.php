<?php
require_once('config/db_connect.php');
session_start();
$level = $_SESSION['leveluser'];


?>

<link href="css/content2.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-latest.js"></script>
<script type="text/javascript">
$(document).ready(function() {

    //Larger thumbnail preview 

    $("ul.thumb li").hover(function() {
        $(this).css({
            'z-index': '10'
        });
        $(this).find('img').addClass("hover").stop()
            .animate({
                marginTop: '-110px',
                marginLeft: '-110px',
                top: '55%',
                left: '55%',
                width: '140px',
                height: '155px',
                padding: '20px'
            }, 200);

    }, function() {
        $(this).css({
            'z-index': '0'
        });
        $(this).find('img').removeClass("hover").stop()
            .animate({
                marginTop: '0',
                marginLeft: '0',
                top: '0',
                left: '0',
                width: '100px',
                height: '111px',
                padding: '5px'
            }, 400);
    });


});
</script>
</head>

<div class="container">
    <ul class="thumb">
        <?php
        if ($level == 'imltd') {
            echo '<li><a href="pmiimltd.php?module=permintaan_sampel_panel"><img src="images/minta_sampel.png" alt="" /></a></li>';
            echo '<li><a href="pmiimltd.php?module=konfirmasi_sampel_panel"><img src="images/verifikasi_sampel_panel.png" alt="" /></a></li>';
            echo '<li><a href="pmiimltd.php?module=rekap_sampel_panel"><img src="images/rekap_sampel_panel.png" alt="" /></a></li>';
        } else {
            echo '<li><a href="pmikasir2.php?module=proses_permintaan_sampel_panel"><img src="images/proses_sampel.png" alt="" /></a></li>';
            echo '<li><a href="pmikasir2.php?module=rekap_sampel_panel"><img src="images/rekap_sampel_panel.png" alt="" /></a></li>';
        }
        ?>
    </ul>
</div>