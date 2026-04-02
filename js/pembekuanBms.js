function clearForm() {
        document.pembekuan.nokantong.value="";
}

var cle;
 function detect(Event) {
  // Event appears to be passed by Mozilla
  // IE does not appear to pass it, so lets use global var
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
  while(quoi!=null)
        {
        quoi = quoi.nextSibling;
        if(quoi.tagName=='INPUT')
                {
                quoi.focus(nokantong);
                quoi=null;
                }

 }
}
 function ok() {
 if(cle != '13') return true;
 else return false;
 }
var gol_darah0;
var produk;
var valid_olah;
function addRow(tableID) {

	var NoKantong = document.pembekuan.nokantong.value;
	var csf = document.pembekuan.csf.value;

	valid_olah = "";
	CekOlah(NoKantong);

	var table = document.getElementById(tableID);
	if (valid_olah == "1") {
	CekProduk(produk);
	   if (valid_produk=="1"){
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		
		if (rowCount<=30){
		var cell0 = row.insertCell(0);
		var element0 = document.createElement("p");
		element0.innerHTML = rowCount;
		cell0.appendChild(element0);

		var cell1 = row.insertCell(1);
		var element1 = document.createElement("p");
		element1.innerHTML = NoTrans;
		cell1.appendChild(element1);

		var cell2 = row.insertCell(2);
		var element2 = document.createElement("p");
		element2.innerHTML = NoKantong;
		cell2.appendChild(element2);

		var cell3 = row.insertCell(3);
		var element3 = document.createElement("p");
		element3.innerHTML = produk;
		cell3.appendChild(element3);

		var cell4 = row.insertCell(4);
		var element4 = document.createElement("p");
		element4.innerHTML = gd+"("+rh+")";
		cell4.appendChild(element4);

		var cell5 = row.insertCell(5);
		var element5 = document.createElement("p");
		element5.innerHTML = tglOlah;
		cell5.appendChild(element5);

		var cell6 = row.insertCell(6);
		var element6 = document.createElement("p");
		element6.innerHTML = tgl_Aftap;
		cell6.appendChild(element6);

		var cell7 = row.insertCell(7);
		var element7 = document.createElement("p");
		element7.innerHTML = lama_pengambilan+" menit";
		cell7.appendChild(element7);

		var cell8 = row.insertCell(8);
		var element8 = document.createElement("p");
		element8.innerHTML = kadaluwarsa ;
		cell8.appendChild(element8);

		var cell9 = row.insertCell(9);
		var element9 = document.createElement("p");
		element9.innerHTML = user ;
		cell9.appendChild(element9);

                var element1 = document.createElement("input");
                element1.type = "hidden";
                element1.name = "nokantong[]";
                element1.value = nokantong;
                cell2.appendChild(element1);
		}else{
		alert("Jumlah Melebihi Batas Maksimal Alat");
		}
	   }else{
		alert("Produk "+produk+" tidak sesusai ketentuan!!!");
	   }
	} else {
		alert("Produk belum melalui proses Pengolahan");
		clearForm();
		return false;
	}
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

function CekOlah(NoKantong)
  {

    $.ajax({
        url: "json_pembekuanBms.php?NoKantong="+NoKantong,
        async: false,
        dataType: 'json',
        success: function(json) {
	nokantong = json.olah.nokantong;
	produk = json.olah.produk;
	NoTrans = json.olah.NoTrans;
	tglOlah = json.olah.tglOlah;
	tgl_Aftap = json.olah.tgl_Aftap;
	lama_pengambilan = json.olah.lama_pengambilan;
	kadaluwarsa = json.olah.kadaluwarsa;
	gd = json.olah.gd;
	rh = json.olah.rh;
	user = json.olah.user;
	valid_olah = json.olah.valid;
        }
    });

}


function CekProduk(produkOlah){
if (produkOlah=="FP 24" || produkOlah=="FFP" || produkOlah=="AHF"){
valid_produk="1";
}else{
valid_produk="0";
}
}
