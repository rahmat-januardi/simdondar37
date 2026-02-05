<?php
/*
TIM IT 2024-10-13
*/

echo <<<HTML
<link href="css/content2.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-latest.js"></script>
<script type="text/javascript">
$(document).ready(function() {

    // Larger thumbnail preview
    $("ul.thumb li").hover(function() {
        $(this).css({'z-index': '10'});
        $(this).find('img').addClass("hover").stop()
            .animate({
                marginTop: '-100px',
                marginLeft: '-100px',
                top: '55%',
                left: '55%',
                width: '140px',
                height: '165px',
                padding: '20px'
            }, 200);
    }, function() {
        $(this).css({'z-index': '0'});
        $(this).find('img').removeClass("hover").stop()
            .animate({
                marginTop: '0',
                marginLeft: '0',
                top: '0',
                left: '0',
                width: '100px',
                height: '120px',
                padding: '5px'
            }, 1000);
    });

    // Swap Image on Click (commented out)
    // $("ul.thumb li a").click(function() {
    //     var mainImage = $(this).attr("href"); // Find Image Name
    //     $("#main_view img").attr({ src: mainImage });
    //     return false;
    // });
});
</script>
</head>

<div class="container">
    <ul class="thumb">
        <li><a href="?module=fp_input_drop"><img src="puf/images/input_pengiriman.png" alt="" /></a></li>
        <li><a href="?module=fp_drop_data"><img src="puf/images/data_pengiriman.png" alt="" /></a></li>
        <li><a href="?module=fp_drop_cek"><img src="puf/images/cek_kantong.png" alt="" /></a></li>
        <li><a href="?module=fp_drop_donorlist"><img src="puf/images/donor_list_2.png" alt="" /></a></li>
        <li><a href="?module=fp_coq_plasma"><img src="puf/images/CoQ.png" alt="" /></a></li>
        <li><a href="?module=fp_packing_slip"><img src="puf/images/packing_list_2.png" alt="" /></a></li>
    </ul>
</div>
HTML;
