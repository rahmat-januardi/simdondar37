<link href="css/content.css" rel="stylesheet" type="text/css" />
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
    <div class="box-body">
        <div style='padding:10px;'>
            <embed src="uploads/Panduan Akses Dokumen melalui Menu E-DOCUMENT di SIMDONDAR.pdf" quality="high" name="fb"
                allowScriptAccess="always" allowFullScreen="true" pluginpage="http://www.adobe.com/go/getreader"
                type="application/pdf" width="140%" height="410"></embed>
        </div>
    </div>
    </ul>
    </ul>
</div>