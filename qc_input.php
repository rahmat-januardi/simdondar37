<?php
require_once('config/db_connect.php');
session_start();
$namaudd = $_SESSION[namaudd];

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

    //Swap Image on Click
    //	$("ul.thumb li a").click(function() {

    //		var mainImage = $(this).attr("href"); //Find Image Name
    //		$("#main_view img").attr({ src: mainImage });
    //		return false;		
    //	});

});
</script>
</head>

<div class="container">
    <ul class="thumb">
        <li><a href="pmiqc.php?module=qc_produk"><img src="images/wb1.png" alt="" /></a></li>
        <li><a href="pmiqc.php?module=qcwb2"><img src="images/wb1.png" alt="" /></a></li>
        <li><a href="pmiqc.php?module=menu_prc"><img src="images/prc2.png" alt="" /></a></li>
        <li><a href="pmiqc.php?module=menu_tc"><img src="images/tc1.png" alt="" /></a></li>
        <li><a href="pmiqc.php?module=menu_ffp"><img src="images/ffp1.png" alt="" /></a></li>
        <!--<li><a href="pmiqc.php?module=qclp"><img src="images/LP2.png" alt=""/></a></li>-->




    </ul>
    </ul>
    </ul>
</div>