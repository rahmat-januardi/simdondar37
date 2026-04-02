var n_kt = [];
var table_id;
var produk;
var jcross0;
function clearForm() {
        document.periksa.NoKantong.value="";
}

var cle;
function detect(Event) {
  if(Event==null) {
    alert('null');
    Event=event;
}
  cle = Event.keyCode;
}

function chang(Event,quoi) {
    detect(Event);
    setTimeout('cle=""',100);
    if(cle=='13')
    while(quoi!=null){
        quoi = quoi.nextSibling;
        if(quoi.tagName=='INPUT'){
            quoi.focus(NoKantong);
            quoi=null;
        }
    }
}

function ok() {
    if(cle != '13') return true;
    else return false;
}
 
function cari_form(){
	var no_form = document.cek_form.NoForm.value;
    $.ajax({
        url: "json_formulir.php?no_form="+no_form,
        async: false,
        dataType: 'json',
        success: function(json) {
			no_form1 = json.form.no_form;
			gol_darah_pasien = json.form.gol_darah;
            
			jcross0 = json.form.cross;
			jsudahcross0 = json.form.sudahcross;
			jminta0 = json.form.minta;
            eraseCookie('jcross');
            createCookie('jcross',jcross0,7);
            createCookie('jsudahcross',jsudahcross0,7);
            createCookie('jminta',jminta0,7);
            createCookie('gol_drh_pas',gol_darah_pasien,7);
		}	
    });
	if (no_form1==''){
        alert("Formulir tidak ditemukan atau permintaan telah dipenuhi, Silahkan cek kembali nomor formulir");
        document.cek_form.NoForm.value='';
	}
	if (jsudahcross0>=jminta0 && no_form1!=='') {
        alert("Formulir permintaan telah dipenuhi, Silahkan cek kembali nomor formulir");
        document.cek_form.NoForm.value='';
	}
    document.getElementById('jcross').value=jcross0;
	var jcross1 = document.getElementById('jcross').value; 
	alert('CROSS HE HE: '+jcross1+' Tambahi: '+rowCount);
}


var n=0;
function cari_kantong(tableID){
	var no_label = document.periksa.NoKantong.value;
	var noform = document.periksa.NoForm.value;
	if (document.periksa.goldarah.value!=readCookie('gol_drh_pas')) {
		eraseCookie('gol_drh_pas');
		createCookie('gol_drh_pas',document.periksa.goldarah.value,7);
	}
    $.ajax({
        url: "cek_kantong_cross.php?no_label="+no_label+"&noform="+noform,
        async: false,
        dataType: 'json',
        success: function(json) {
			no_label1 = json.kantong.noKantong;
			gol_darah1 = json.kantong.gol_darah;
            		rhesus1 = json.kantong.rh_darah;
			tgl = json.kantong.tgl_aftap;
			produk = json.kantong.produk;
            createCookie('gol_drh_ktg',gol_darah1,7);
            createCookie('rh_ktg',rhesus1,7);
            createCookie('tgl_aftap',tgl,7);
            createCookie('jenis_darah',produk,7);
		}
    });
	if (no_label1!=''){
	    var i;
        n++;
        for(i=0; i<=n; i++){
            if (n_kt[i]==document.periksa.NoKantong.value){
                clearForm('document.periksa.NoKantong');
                document.periksa.NoKantong.focus();
                $( "#kantong_sudah_diinput" ).dialog( "open" );
                return false;
            }
        }
        n_kt.push(document.periksa.NoKantong.value);
        var x = readCookie('gol_drh_pas')
        if (gol_darah1!=x){
                table_id=tableID
                $( "#konfirmasi" ).dialog( "open" );
                return false;
        }
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length-1;
        var jcross1 = document.getElementById('jcross').value;
		if (rowCount>jcross1) {
            $( "#kantong_terpenuhi" ).dialog( "open" );
            return false;
		}
		addRow(tableID);
	} else {
        clearForm('document.periksa.NoKantong');
        $( "#kantong_tdk_sesuai" ).dialog( "open" );
        return false;
	}
}

$.fx.speeds._default = 1000;
$(function(){
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $('#konfirmasi').dialog({
        autoOpen: false,
        width: 300,
		height: 200,
        modal: true,
        resizable: false,
        hide: "explode",
        buttons: {
                "Yakin": function() {
                    addRow(table_id);
                    $(this).dialog("close");
                },
                "Batal": function() {
                    $(this).dialog("close");
                }
            }
        });
	$( "#gol_darah_tdk_sesuai" ).dialog({
            autoOpen: false,
			height: 200,
			modal: true,
            hide: "explode"
	});
});


function addRow(tableID) {
	var table = document.getElementById(tableID);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	var cell0 = row.insertCell(0);
    var element0 = document.createElement("input");
		element0.type = "text";
		element0.size = "10";
		element0.name = "no_kantong[]";
        element0.value = document.periksa.NoKantong.value;
		cell0.appendChild(element0);
            
	var cell1 = row.insertCell(1);
	var element1 = document.createElement("input");
        var tgl = readCookie('tgl_aftap')
		element1.type = "text";
		element1.size = "6";
		element1.name = "tgl[]";
        element1.value = tgl;
		cell1.appendChild(element1);
			
	var cell2 = row.insertCell(2);
		var element2 = document.createElement("input");
        var jenis = readCookie('jenis_darah')
		element2.type = "text";
		element2.size = "1";
		element2.name = "jenis[]";
        element2.value = jenis;
		cell2.appendChild(element2);
        
	var cell3 = row.insertCell(3);
		var element3 = document.createElement("input");
        var gol = readCookie('gol_drh_ktg');
        var rh = readCookie('rh_ktg');
		element3.type = "text";
		element3.size = "1";
		element3.name = "gol_drh[]";
        element3.value = gol;
		cell3.appendChild(element3);
    
    var cell31 = row.insertCell(4);
		var element31 = document.createElement("input");
        var rh = readCookie('rh_ktg');
		element31.type = "text";
		element31.size = "1";
		element31.name = "rh_gol[]";
        element31.value = rh;
		cell31.appendChild(element31);
		
    var cell4 = row.insertCell(5);
		var element4 = document.createElement("select");
            element4.name = "metode[]";	
        var element5 = document.createElement("option");
            element5.value = "Gel Test";
            element5.innerHTML = "Gel Test";
            element4.appendChild(element5);
        var element6 = document.createElement("option");
            element6.value = "Tube Test";
            element6.innerHTML = "Tube Test";
            element4.appendChild(element6);
        cell4.appendChild(element4);
        
    var cell5 = row.insertCell(6);
		var element4 = document.createElement("select");
            element4.name = "status[]";
		var element5 = document.createElement("option");
            element5.value = "1";
            element5.innerHTML = "Compatible";
            element4.appendChild(element5);
		var element6 = document.createElement("option");
            element6.value = "0";
            element6.innerHTML = "Incompatible Boleh Keluar";
            element4.appendChild(element6);
		var element7 = document.createElement("option");
            element7.value = "2";
            element7.innerHTML = "Incompatible tdk Boleh Keluar";
            element4.appendChild(element7);
		cell5.appendChild(element4);

	var cell6 = row.insertCell(7);
		var element4 = document.createElement("select");
            element4.name = "cross[]";
		var element5 = document.createElement("option");
            element5.value = "Cocok";
            element5.innerHTML = "Cocok";
            element4.appendChild(element5);
		var element6 = document.createElement("option");
            element6.value = "Minor Positif";
            element6.innerHTML = "Minor Positif";
            element4.appendChild(element6);
		var element7 = document.createElement("option");
            element7.value = "Mayor Positif";
            element7.innerHTML = "Mayor Positif";
            element4.appendChild(element7);
		var element8 = document.createElement("option");
            element8.value = "May Min Positif AK DCT Negatif";
            element8.innerHTML = "May Min Positif AK DCT Negatif";
            element4.appendChild(element8);
		/*
		var element9 = document.createElement("option");
            element9.value = "DCT Positif";
            element9.innerHTML = "DCT Positif";
            element4.appendChild(element9);
		var element10 = document.createElement("option");
            element10.value = "Mayor Minor Positif";
            element10.innerHTML = "Mayor Minor Positif";
            element4.appendChild(element10);*/
		var element11 = document.createElement("option");
            element11.value = "Mayor Minor AK Positif";
            element11.innerHTML = "Mayor Minor AK Positif";
            element4.appendChild(element11);
		var element12 = document.createElement("option");
            element12.value = "Mayor Minor AK DCT Positif";
            element12.innerHTML = "Mayor Minor AK DCT Positif";
            element4.appendChild(element12);
		var element13 = document.createElement("option");
            element13.value = "Minor AK DCT Positif";
            element13.innerHTML = "Minor AK DCT Positif";
            element4.appendChild(element13);
		var element14 = document.createElement("option");
            element14.value = "AK DCT Positif";
            element14.innerHTML = "AK DCT Positif";
            element4.appendChild(element14);
		var element15 = document.createElement("option");
            element15.value = "Minor Tanpa Test";
            element15.innerHTML = "Minor Tanpa Test";
            element4.appendChild(element15);
		cell6.appendChild(element4);
			
	var cell7 = row.insertCell(8);
		var element4 = document.createElement("select");
            element4.name = "ket[]";
		var element5 = document.createElement("option");
			element5.value = "-";
			element5.innerHTML = "-";
			element4.appendChild(element5);
        	var element6 = document.createElement("option");
			element6.value = "ACC Dokter UDD";
			element6.innerHTML = "ACC Dokter UDD";
			element4.appendChild(element6);
		var element7 = document.createElement("option");
			element7.value = "ACC Dokter RS";
			element7.innerHTML = "ACC Dokter RS";
			element4.appendChild(element7);
		cell7.appendChild(element4);

	var cell8 = row.insertCell(9);
		var element4 = document.createElement("select");
			element4.name = "aglutinasi[]";
		var element5 = document.createElement("option");
			element5.value = "Negatif";
			element5.innerHTML = "Negatif";
			element4.appendChild(element5);
		var element6 = document.createElement("option");
			element6.value = "1+";
			element6.innerHTML = "1+";
			element4.appendChild(element6);
		var element5 = document.createElement("option");
			element5.value = "2+";
			element5.innerHTML = "2+";
			element4.appendChild(element5);
		var element8 = document.createElement("option");
			element8.value = "3+";
			element8.innerHTML = "3+";
			element4.appendChild(element8);
		var element9 = document.createElement("option");
			element9.value = "4+";
			element9.innerHTML = "4+";
			element4.appendChild(element9);
		var element3 = document.createElement("option");
			element3.value = "tdl dilakukan";
			element3.innerHTML = "Tdk Dilakukan";
			element4.appendChild(element3);
			cell8.appendChild(element4);

	var cell9 = row.insertCell(10);
		var element1 = document.createElement("input");
			element1.type = "text";
			element1.size = "25";
			element1.name = "listcomb[]";
        	element1.value = " ";
			cell9.appendChild(element1);

	var cell10 = row.insertCell(11);
		var element4 = document.createElement("select");
			element4.name = "fasea[]";
		var element5 = document.createElement("option");
			element5.value = "tdk dilakukan";
			element5.innerHTML = "Tdk Dilakukan";
			element4.appendChild(element5);
		var element6 = document.createElement("option");
			element6.value = "negatif";
			element6.innerHTML = "Negatif";
			element4.appendChild(element6);
		var element7 = document.createElement("option");
			element7.value = "1+";
			element7.innerHTML = "1+";
			element4.appendChild(element7);
		var element8 = document.createElement("option");
			element8.value = "2+";
			element8.innerHTML = "2+";
			element4.appendChild(element8);
		var element9 = document.createElement("option");
			element9.value = "3+";
			element9.innerHTML = "3+";
			element4.appendChild(element9);
		var element3 = document.createElement("option");
			element3.value = "4+";
			element3.innerHTML = "4+";
			element4.appendChild(element3);
			cell10.appendChild(element4);

	var cell11 = row.insertCell(12);
		var element4 = document.createElement("select");
			element4.name = "faseb[]";
		var element5 = document.createElement("option");
			element5.value = "tdk dilakukan";
			element5.innerHTML = "Tdk Dilakukan";
			element4.appendChild(element5);
		var element6 = document.createElement("option");
			element6.value = "negatif";
			element6.innerHTML = "Negatif";
			element4.appendChild(element6);
		var element7 = document.createElement("option");
			element7.value = "1+";
			element7.innerHTML = "1+";
			element4.appendChild(element7);
		var element8 = document.createElement("option");
			element8.value = "2+";
			element8.innerHTML = "2+";
			element4.appendChild(element8);
		var element9 = document.createElement("option");
			element9.value = "3+";
			element9.innerHTML = "3+";
			element4.appendChild(element9);
		var element3 = document.createElement("option");
			element3.value = "4+";
			element3.innerHTML = "4+";
			element4.appendChild(element3);
			cell11.appendChild(element4);

	var cell12 = row.insertCell(13);
		var element4 = document.createElement("select");
			element4.name = "fasec[]";
		var element5 = document.createElement("option");
			element5.value = "tdk dilakukan";
			element5.innerHTML = "Tdk Dilakukan";
			element4.appendChild(element5);
		var element6 = document.createElement("option");
			element6.value = "negatif";
			element6.innerHTML = "Negatif";
			element4.appendChild(element6);
		var element7 = document.createElement("option");
			element7.value = "1+";
			element7.innerHTML = "1+";
			element4.appendChild(element7);
		var element8 = document.createElement("option");
			element8.value = "2+";
			element8.innerHTML = "2+";
			element4.appendChild(element8);
		var element9 = document.createElement("option");
			element9.value = "3+";
			element9.innerHTML = "3+";
			element4.appendChild(element9);
		var element3 = document.createElement("option");
			element3.value = "4+";
			element3.innerHTML = "4+";
			element4.appendChild(element3);
			cell12.appendChild(element4);


	var cell13 = row.insertCell(14);
		var element4 = document.createElement("select");
			element4.name = "keluar[]";
		var element5 = document.createElement("option");
			element5.value = "ok";
			element5.innerHTML = "Ok";
			element4.appendChild(element5);
		var element6 = document.createElement("option");
			element6.value = "tidak";
			element6.innerHTML = "Tidak";
			element4.appendChild(element6);
			cell13.appendChild(element4);

	var cell14 = row.insertCell(15);
		var element4 = document.createElement("input");
			element4.name = "titip[]";
			element4.type = "hidden";
			element4.value = "1";
			cell14.appendChild(element4);
			clearForm();
}

function deleteRow(tableID) {
	try {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[0].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
					table.deleteRow(i);
					rowCount--;
					i--;
			}
        }
	}catch(e) {
		alert(e);
	}
}

function formSubmit(){
	document.getElementById("dtable").submit();
}
