function clearForm() {
  document.bdrs.nokantong.value = "";
}

var cle;
function detect(Event) {
  if (Event == null) {
    alert("null");
    Event = event;
  }
  cle = Event.keyCode;
}
function chang(Event, quoi) {
  detect(Event);
  setTimeout('cle=""', 100);
  if (cle == "13")
    while (quoi != null) {
      quoi = quoi.nextSibling;
      if (quoi.tagName == "INPUT") {
        quoi.focus(nokantong);
        quoi = null;
      }
    }
}
function ok() {
  return cle != "13";
}

var gol_darah0, produk, valid_periksa0, volumeasal, jeniskantong;

function addRow(tableID) {
  var NoKantong = document.bdrs.nokantong.value;
  var table = document.getElementById(tableID);
  var tbody = table.getElementsByTagName("tbody")[0];

  // Cek duplikat
  for (var i = 0; i < tbody.rows.length; i++) {
    var cellNo = tbody.rows[i].cells[2];
    if (cellNo && cellNo.textContent === NoKantong) {
      $("#kantong_sudah_diinput").dialog("open");
      clearForm();
      document.bdrs.nokantong.focus();
      return false;
    }
  }

  // Ambil data
  gol_darah0 = produk = valid_periksa0 = volumeasal = jeniskantong = "";
  check3(NoKantong);

  if (valid_periksa0 !== "1") {
    $("#kantong_tdk_sesuai").dialog("open");
    clearForm();
    return false;
  }

  // Sisipkan row baru
  var row = tbody.insertRow(-1);

  // Checkbox
  var chkCell = row.insertCell(0);
  var chk = document.createElement("input");
  chk.type = "checkbox";
  chkCell.appendChild(chk);

  // Nomor urut
  var cell1 = row.insertCell(1);
  var pNo = document.createElement("p");
  pNo.innerHTML = tbody.rows.length;
  cell1.appendChild(pNo);
  var hidden = document.createElement("input");
  hidden.type = "hidden";
  hidden.name = "nk[]";
  hidden.value = NoKantong;
  cell1.appendChild(hidden);

  // No.Kantong
  var cell2 = row.insertCell(2);
  var p2 = document.createElement("p");
  p2.innerHTML = NoKantong;
  cell2.appendChild(p2);

  // Golongan Darah
  var cell3 = row.insertCell(3);
  var p3 = document.createElement("p");
  p3.innerHTML = gol_darah0;
  cell3.appendChild(p3);

  // Rhesus
  var cell4 = row.insertCell(4);
  var p4 = document.createElement("p");
  p4.innerHTML = RhesusDrh;
  cell4.appendChild(p4);

  // Komponen Darah
  var cell5 = row.insertCell(5);
  var p5 = document.createElement("p");
  p5.innerHTML = produk;
  cell5.appendChild(p5);

  // Tgl Aftap
  var cell6 = row.insertCell(6);
  var p6 = document.createElement("p");
  p6.innerHTML = tgl_Aftap;
  cell6.appendChild(p6);

  // Tgl Kadaluwarsa
  var cell7 = row.insertCell(7);
  var p7 = document.createElement("p");
  p7.innerHTML = kadaluwarsa;
  cell7.appendChild(p7);

  // Tgl Pengolahan
  var cell8 = row.insertCell(8);
  var p8 = document.createElement("p");
  p8.innerHTML = tglpengolahan;
  cell8.appendChild(p8);

  // Jenis Kantong
  var cell9 = row.insertCell(9);
  var p9 = document.createElement("p");
  p9.innerHTML = jeniskantong;
  cell9.appendChild(p9);

  // Volume Asal
  var cell10 = row.insertCell(10);
  var p10 = document.createElement("p");
  p10.innerHTML = volumeasal;
  cell10.appendChild(p10);

  // Suhu saat terima
  var cell11 = row.insertCell(11);
  // Bungkus input dan teks dalam div
  var suhuWrapper = document.createElement("div");
  suhuWrapper.style.display = "flex";
  suhuWrapper.style.alignItems = "center";
  suhuWrapper.style.gap = "4px";
  // Input suhu
  var inputSuhu = document.createElement("input");
  inputSuhu.type = "text";
  inputSuhu.name = "suhu_terima[]";
  inputSuhu.placeholder = "mis. -2.5";
  inputSuhu.style.width = "100px";
  // Teks °C
  var derajat = document.createElement("span");
  derajat.innerHTML = "&deg;C"; // simbol derajat
  derajat.style.fontSize = "12px";
  // Tambahkan input dan teks derajat ke wrapper
  suhuWrapper.appendChild(inputSuhu);
  suhuWrapper.appendChild(derajat);
  // Tambahkan wrapper ke cell
  cell11.appendChild(suhuWrapper);
  // Teks keterangan kecil di bawah input
  var note = document.createElement("div");
  note.innerHTML =
    "<small><i>Gunakan titik, bukan koma (mis. 36.5)</i></small>";
  note.style.marginTop = "2px";
  note.style.fontSize = "10px";
  note.style.color = "#666";
  // Tambahkan keterangan ke cell
  cell11.appendChild(note);

  // catatan
  var cell12 = row.insertCell(12);
  var inputCatatan = document.createElement("textarea");
  inputCatatan.name = "catatan[]";
  inputCatatan.placeholder = "Tambah Catatan";
  inputCatatan.rows = "2";
  inputCatatan.cols = "20";
  cell12.appendChild(inputCatatan);

  clearForm();
}

function deleteRow(tableID) {
  try {
    var table = document.getElementById(tableID);
    var tbody = table.getElementsByTagName("tbody")[0];
    // Hapus baris tercentang
    for (var i = 0; i < tbody.rows.length; i++) {
      var row = tbody.rows[i];
      var chkbox = row.cells[0] && row.cells[0].childNodes[0];
      if (chkbox && chkbox.checked) {
        tbody.deleteRow(i);
        i--; // adjust index
      }
    }
    // Reindex nomor urut
    for (var j = 0; j < tbody.rows.length; j++) {
      var cellNo = tbody.rows[j].cells[1];
      if (cellNo && cellNo.childNodes[0]) {
        cellNo.childNodes[0].innerHTML = j + 1;
      }
    }
  } catch (e) {
    alert(e);
  }
}

function formSubmit() {
  document.getElementById("dtable").submit();
}

function check3(NoKantong) {
  $.ajax({
    url: "json_registrasiqc.php?NoKantong=" + NoKantong,
    async: false,
    dataType: "json",
    success: function (json) {
      gol_darah0 = json.darah.gol_darah;
      RhesusDrh = json.darah.RhesusDrh;
      produk = json.darah.produk;
      tgl_Aftap = json.darah.tgl_Aftap;
      kadaluwarsa = json.darah.kadaluwarsa;
      tglpengolahan = json.darah.tglpengolahan;
      volumeasal = json.darah.volumeasal;
      jeniskantong = json.darah.jeniskantong;
      valid_periksa0 = json.darah.valid;
    },
  });
}
