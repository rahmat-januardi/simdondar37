<?php
session_start();

// Mengatur variabel dari session
$namudd = $_SESSION["namaudd"];

// Mengatur pengaturan error
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(E_ALL ^ E_NOTICE);

// Mendapatkan hostname dan menentukan tipe host
$hostname = gethostname();
$host_tipe = substr($hostname, 0, 1);

// Mengatur variabel untuk query level
$qrylevel = "qrylevel";

// Output HTML awal
echo '<link rel="shortcut icon" href="images/index.ico" type="image/x-icon"/>
<title>SIMDONDAR 3.7</title>
<link rel="stylesheet" href="bootsrap337/css/bootstrap.min.css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js1/interface.js"></script>
<link type="text/css" href="css/interface-fisheye.css" rel="stylesheet">
<script src="bs4/js/sweetalert.min.js"></script>
<style>
    
		:root {
			--pmi1	: #E4002b;
			--pmi2	: #ff7276;
			--pmi3	: #ff8674;
			--pmi4	: #f5deda;
			--pmi5	: #ffe6e6;
			--pmiblue	: #001a70;
			--hungryred : #ED000A;
		}
		.header0 {
			width: 100.0%;
			height:100%;
			background:var(--pmi5);
			background-image: linear-gradient(to right, var(--hungryred) , var(--pmi5));
			position:fixed;
			left:0px;
			top:0px;
		}
		.headersdw{
			width:97.1%;
			height: 130px;
			position: absolute;
			left:1.4%;
			top:5px;
			-webkit-border-top-left-radius: 7px;
			-moz-border-radius-topleft: 7px;
			border-top-left-radius: 7px;
			-webkit-border-bottom-left-radius: 7px;
			-moz-border-radius-bottomleft: 7px;
			border-bottom-left-radius: 7px;
			font-family:arial;
			font-size:3.5em;
			color:#ffffff;
			text-shadow: 2px 4px 2px #000;
			font-weight:bold;
			box-shadow: 5px 5px 7px 2px rgba(0,0,0,0.33);
			-webkit-box-shadow: 5px 5px 7px 2px rgba(0,0,0,0.33);
			-moz-box-shadow: 5px 5px 7px 2px rgba(0,0,0,0.33);
		}
		.header01 {
			width: 32%;
			height: 130px;
			background: var(--pmi1);
			position: relative;
			left:1.4%;
			top:5px;
			-webkit-border-top-left-radius: 7px;
			-moz-border-radius-topleft: 7px;
			border-top-left-radius: 7px;
			-webkit-border-bottom-left-radius: 7px;
			-moz-border-radius-bottomleft: 7px;
			border-bottom-left-radius: 7px;
			font-family:arial;
			font-size:3.5em;
			color:#ffffff;
			text-shadow: 1px 2px 1px #000;
			font-weight:bold;
		}
		.header02 {
			width: 29%;
			height: 130px;
			background: var(--pmi2);
			position: relative;
			left: 33.4%;
			top:-125px;
		}
		.header03 {
			width: 13%;
			height: 130px;
			background: var(--pmi3);
			position: relative;
			left: 62.4%;
			top:-255px;
		}
		.header04 {
			width: 10%;
			height: 130px;
			background: var(--pmi4);
			position: relative;
			left: 75.4%;
			top:-385px;
			-webkit-border-top-right-radius: 7px;
			-moz-border-radius-topright: 7px;
			border-top-right-radius: 7px;
			-webkit-border-bottom-right-radius: 7px;
			-moz-border-radius-bottomright: 7px;
			border-bottom-right-radius: 7px;
		}
		.header05 {
			width: 11.45%;
			height: 100px;
			background-repeat:no-repeat;
			position: relative;
			left: 85%;
			top:-420px;
		}
		.header06 {
			width: 13.6%;
			height: 130px;
			background: #ffffff;
			background-repeat:no-repeat;
			position: relative;
			left: 85%;
			background-image: url(images/pmi.png);
			background-position: center, center;
			background-repeat: no-repeat, no-repeat;
			background-size: 80%;
			vertical-align: middle;
			text-align:center;
			top:-515px;
			-webkit-border-top-right-radius: 7px;
			-moz-border-radius-topright: 7px;
			border-top-right-radius: 7px;
			-webkit-border-bottom-right-radius: 7px;
			-moz-border-radius-bottomright: 7px;
			border-bottom-right-radius: 7px;
		}
		.header051 {
			width:94px;
			height:61px;
			position: relative;
			left:88%;
			top:-550px;
		}
		.header100 {
			width:98.8%;
			height:98%;
			position: relative;
			left: 0.5%;
			top: 1%;
			background:#ED000A;
			background-image: linear-gradient(to right, var(--pmi4) , var(--hungryred));
			border-top: 1px solid #636D65;
			border-bottom: 1px solid #636D65;
			border-right: 1px solid #636D65;
			-webkit-border-top-left-radius: 7px;
			-moz-border-radius-topleft: 7px;
			border-top-left-radius: 7px;
			-webkit-border-top-right-radius: 7px;
			-moz-border-radius-topright: 7px;
			border-top-right-radius: 7px;
			-webkit-border-bottom-left-radius: 7px;
			-moz-border-radius-bottomleft: 7px;
			border-bottom-left-radius: 7px;
			-webkit-border-bottom-right-radius: 7px;
			-moz-border-radius-bottomright: 7px;
			border-bottom-right-radius: 7px;
			box-shadow: 3px 3px 5px 2px rgba(0,0,0,0.33);
			-webkit-box-shadow: 3px 3px 5px 2px rgba(0,0,0,0.33);
			-moz-box-shadow: 3px 3px 5px 2px rgba(0,0,0,0.33);
		}
		.header100kiri {
			width:10px;
			height:86%;
			background:#ED000A;
			position:relative;
			left:15px;
			top:-490px;
		}
		.header10 {
			width:97.1%;
			height:calc(100% - 185px);;
			position: relative;
			left: 1.4%;
			top: -500px;
			background:#FFFFFF;
			border-top: 1px solid #636D65;
			border-left: 1px solid #636D65;
			border-bottom: 1px solid #636D65;
			border-right: 1px solid #636D65;
			-webkit-border-top-left-radius: 10px;
			-moz-border-radius-topleft: 10px;
			border-top-left-radius: 10px;
			-webkit-border-top-right-radius: 10px;
			-moz-border-radius-topright: 10px;
			border-top-right-radius: 10px;
			-webkit-border-bottom-left-radius: 10px;
			-moz-border-radius-bottomleft: 10px;
			border-bottom-left-radius: 10px;
			-webkit-border-bottom-right-radius: 10px;
			-moz-border-radius-bottomright: 10px;
			border-bottom-right-radius: 10px;
			box-shadow: 6px 6px 7px -5px rgba(0,0,0,0.53) inset;
		-webkit-box-shadow: 6px 6px 7px -5px rgba(0,0,0,0.53) inset;
		-moz-box-shadow: 6px 6px 7px -5px rgba(0,0,0,0.53) inset;
		}
		.nama {
			width: 86.4%;
			height:40px;
			position:relative;
			left:30px;
			top:-495px;
			color: var(--pmiblue);
			font-size:1em;
			font-family:sans-serif;
			text-shadow: 1px 2px 1px var(--pmi4);
			font-weight:bold;
		}
		.info {
			width: 110px;
			height:40px;
			position: fixed;
			bottom: 0;
			right: 0;
			color:#FFFFFF;
			font-size:1em;
			font-family:sans-serif;
			text-shadow: 1px 2px 1px #000;
			font-weight:bold;
		}
		.fisheye {
			position:relative;
			top:-96%;
			left:40px;
		}
		img {
			-webkit-filter: drop-shadow(4px 8px 10px rgba(0, 0, 0, 0.5));
    		filter: drop-shadow(4px 8px 10px rgba(0, 0, 0, 0.5));
		}
		a{
			font-family: Arial, Helvetica, sans-serif;
			text-transform: uppercase;
			font-size:10px;
			text-shadow: 2px 2px 3px rgba(150, 150, 150, 1);
		}
		.dropbtn {
			padding: 5px; 
			font-size: 16px;
			border: none;
		}
		.dropup {
			position: relative;
			display: inline-block;
			border: var(--pmi1) solid 1px;
			padding-left: 15px;
			padding-right: 15px;
			border-radius: 10px;
			background:var(--pmi4);
			cursor: pointer;
			box-shadow: 1px 1px 2px 2px rgba(0,0,0,0.2);
		}
		.dropup-content {
			display: none;
			position: absolute;
			background: linear-gradient(225deg, var(--pmi5), #ffffff);
			min-width: 300px;
			bottom: 15px;
			left: 5px;
			border: var(--pmi1) solid 2px;
			z-index: 1;
			box-shadow: 6px 4px 28px -6px rgba(255,0,0,0.82);
			-webkit-box-shadow: 6px 4px 28px -6px rgba(255,0,0,0.82);
			-moz-box-shadow: 6px 4px 28px -6px rgba(255,0,0,0.82);
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 10px;
			max-height:450px;
			overflow:auto;
		}
		.dropup-content a {
			font-size: 1em;
			font-weight: lighter;
			color: black;
			padding: 12px 16px;
			text-decoration: none;
			display: block;
			text-shadow: none;
			text-transform: none !important;
		}
		.dropup-content a:hover {background-color:var(--pmi3);color:white;}
		.dropup:hover .dropup-content {display: block;}
		.dropup:hover .dropbtn {background-color: var(--pmiblue);}
		.swal2-popup {font-size: 1.6rem !important;}
		.swal-footer {text-align: center;}
		.fisheye{
			left: 60px !important;
			width: 200px !important;
		}
	
</style>
<script type="text/javascript" language="JavaScript1.2">
    function get_fresh() {
        document.location.reload();
    }
    function logout() {
        swal({
            title: "Konfirmasi",
            text: "Apakah anda yakin akan keluar dari SIMDONDAR?",
            icon: "warning",
            buttons: true,
            buttons: ["Tidak", "Yakin"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                document.location.href = "logout.php";
            }
        });
    }
</script>';

// Koneksi database
include "config/dbi_connect.php";

// Pengecekan tipe host dan level pengguna
if ($host_tipe == "M") {
	if (($_SESSION["leveluser"] == "mobile") || (strpos($_SESSION["multilevel"], "mobile") !== false)) {
		$_SESSION["leveluser"] = "mobile";
	} else {
		die("ANDA TIDAK PUNYA AKSES MOBILE UNIT!!!<br> Klik <b> <a href=index.php>DI SINI</a> </b> untuk login kembali");
	}
}
if ($host_tipe == "B" && $_SESSION["namauser"] != "admin") {
	if (($_SESSION["leveluser"] == "bdrs") || (strpos($_SESSION["multilevel"], "bdrs") !== false)) {
		$_SESSION["leveluser"] = "bdrs";
	} else {
		die("ANDA TIDAK PUNYA AKSES BDRS UNIT!!!<br> Klik <b> <a href=index.php>DI SINI</a> </b> untuk login kembali");
	}
}

// Pengecekan dan pengaturan level dari parameter GET
if (isset($_GET["level"])) {
	$level_array = explode(",", $_SESSION["multilevel"]);
	for ($i = 0; $i < sizeof($level_array); $i++) {
		if ($level_array[$i] == $_GET["level"]) {
			$_SESSION["leveluser"] = $level_array[$i];
		}
	}
}

// Output struktur HTML utama
echo '<div class="header0">
    <div class="header100">
        <div class="header01"></div>
        <div class="header02"></div>
        <div class="header03"></div>
        <div class="header04"></div>
        <div class="header06"></div>
        <div class="header10">';

// Menentukan iframe berdasarkan parameter GET
if ($_GET["module"] == "home" && $_GET["level"] == "laboratorium") {
	echo '<iframe name="isiadmin" width="100%" height="100%" frameborder="0"></iframe>';
} elseif ($_GET["module"] == "home" && $_GET["level"] == "aftap") {
	echo '<iframe src="pmiaftap.php?module=rekap_transaksi_sum" name="isiadmin" width="100%" height="100%" frameborder="0"></iframe>';
} elseif ($_GET["module"] == "home" && $_GET["level"] == "mobile") {
	echo '<iframe src="pmimobile.php?module=rekap_transaksi_sum" name="isiadmin" width="100%" height="100%" frameborder="0"></iframe>';
} elseif ($_GET["module"] == "home" && $_GET["level"] == "p2d2s") {
	echo '<iframe name="isiadmin" width="100%" height="100%" frameborder="0"></iframe>';
} elseif ($_GET["module"] == "home" && $_GET["level"] == "penyimpanan") {
	echo '<iframe src="pmipenyimpanan.php?module=komponen_dash" name="isiadmin" width="100%" height="100%" frameborder="0"></iframe>';
} elseif ($_GET["module"] == "home" && $_GET["level"] == "fraksionasi") {
	echo '<iframe src="pmifraksionasi.php?module=puf_dashboard" name="isiadmin" width="100%" height="100%" frameborder="0"></iframe>';
} elseif ($_GET["module"] == "home" && $_GET["level"] == "mk") {
	echo '<iframe src="pmimk.php?module=notifikasi_dokumen" name="isiadmin" width="100%" height="100%" frameborder="0"></iframe>';
} else {
	echo '<iframe name="isiadmin" width="100%" height="100%" frameborder="0"></iframe>';
}

echo '        </div>
        <div class="nama">';
echo $_SESSION["namauser"] . " (" . $_SESSION["nama_lengkap"] . "), " . strtoupper($_SESSION["leveluser"]);
echo ' <span id="area"></span></div>
    </div>
    <div id="fisheye" class="fisheye">
        <div class="fisheyeContainer">';

// Query dan include file berdasarkan level
$qrylevel = mysqli_query($dbi, "SELECT `urutan`, `level`, `alias`, `menucaption`, `indexfile`, `menustart`, `kodetrx` FROM `level`;");
while ($dtlevel = mysqli_fetch_array($qrylevel)) {
	if ($_SESSION["leveluser"] == $dtlevel["level"]) {
		include $dtlevel["indexfile"] . ".php";
	}
}

echo '        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(
        function() {
            $("#fisheye").Fisheye(
                {
                    maxWidth: 30,
                    items: "a",
                    itemsText: "span",
                    container: ".fisheyeContainer",
                    itemWidth: 65,
                    proximity: 90,
                    halign: "left",
                }
            )
        }
    );
    window.addEventListener("load", function(event) {
        const {
            clientWidth,
            clientHeight
        } = document.body;
        document.getElementById("area").innerText = " (" + clientWidth + " x " + clientHeight + ")";
    });
    window.onresize = () => {
        const {
            clientWidth,
            clientHeight
        } = document.body;
        document.getElementById("area").innerText = " (" + clientWidth + " x " + clientHeight + ")";
    }
</script>
</div>';