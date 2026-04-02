function clearForm() {
        document.bdrs.nokantong.value="";
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
var valid_periksa0;
		function addRow(tableID) {
			var NoKantong = document.bdrs.nokantong.value;
			//var lkantong = NoKantong.length-1;
			gol_darah0 = "";
			valid_periksa0 = "";
            check3(NoKantong);
			var table = document.getElementById(tableID);

			if (valid_periksa0 == "1") {
			var rowCount = table.rows.length;
			var row = table.insertRow(rowCount);

			var cell1 = row.insertCell(0);
			var element1 = document.createElement("p");
			element1.innerHTML = rowCount-1;
			cell1.appendChild(element1);
            var element1 = document.createElement("input");
            element1.type = "hidden";
            element1.name = "nk[]";
            element1.value = NoKantong;
            cell1.appendChild(element1);

		

			var cell2 = row.insertCell(1);
			var element2 = document.createElement("p");
			element2.innerHTML = NoKantong;
			cell2.appendChild(element2);

			var cell3 = row.insertCell(2);
			var element3 = document.createElement("p");
			element3.innerHTML = gol_darah0;
			cell3.appendChild(element3);
			
			var cell4 = row.insertCell(3);
			var element4 = document.createElement("p");
			element4.innerHTML = RhesusDrh;
			cell4.appendChild(element4);

			var cell5 = row.insertCell(4);
			var element5 = document.createElement("p");
			element5.innerHTML = produk;
			cell5.appendChild(element5);


			var cell6 = row.insertCell(5);
			var element6 = document.createElement("p");
			element6.innerHTML = tgl_Aftap;
			cell6.appendChild(element6);

			var cell7 = row.insertCell(6);
			var element7 = document.createElement("p");
			element7.innerHTML = kadaluwarsa;
			cell7.appendChild(element7);

			
			var cell8 = row.insertCell(7);
			var element8 = document.createElement("p");
			element8.innerHTML = 'NR';
			cell8.appendChild(element8);
            
			var cell9 = row.insertCell(8);
			var element9 = document.createElement("p");
			element9.innerHTML = 'NR';
			cell9.appendChild(element9);
            
			var cell10 = row.insertCell(9);
			var element10 = document.createElement("p");
			element10.innerHTML = 'NR';
			cell10.appendChild(element10);
            
			var cell11 = row.insertCell(10);
			var element11 = document.createElement("p");
			element11.innerHTML = 'NR';
			cell11.appendChild(element11);

			
			var cell0 = row.insertCell(0);
		        var element0 = document.createElement("input");
			element0.type = "checkbox";
			cell0.appendChild(element0);

			} else {
			$( "#kantong_tdk_sesuai" ).dialog( "open" );
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
		function formSubmit()
		{
			document.getElementById("dtable").submit();
		}

function check3(NoKantong)
  {
    $.ajax({
        url: "json_registrasiqc.php?NoKantong="+NoKantong,
        async: false,
        dataType: 'json',
        success: function(json) {
	gol_darah0 = json.darah.gol_darah;
	RhesusDrh = json.darah.RhesusDrh;
	produk = json.darah.produk;
	tgl_Aftap = json.darah.tgl_Aftap;
	kadaluwarsa = json.darah.kadaluwarsa;
	valid_periksa0 = json.darah.valid;
        }
    });
}


