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

var gol_darah0 = "";
var produk = "";
var valid_periksa0 = "";
var volumeasal = "";
var jeniskantong = "";
var RhesusDrh = "";
var tgl_Aftap = "";
var kadaluwarsa = "";
var tglpengolahan = "";
var pesan0 = "";

function addRow(tableID) {
  var NoKantong = document.bdrs.nokantong.value;
  var table = document.getElementById(tableID);
  var tbody = table.getElementsByTagName("tbody")[0];

  if (NoKantong == "") {
    alert("No.Kantong belum diisi");
    return false;
  }

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
  RhesusDrh = tgl_Aftap = kadaluwarsa = tglpengolahan = "";
  pesan0 = "";

  check3(NoKantong);
  console.log("Validasi:", valid_periksa0, "Pesan:", pesan0);
  // Tampilkan alert sesuai valid
  if (valid_periksa0 == "0") {
    alert(pesan0 || "No kantong tidak ditemukan");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  } else if (valid_periksa0 == "2") {
    alert(pesan0 || "Status kantong tidak sesuai");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  } else if (valid_periksa0 == "3") {
    alert(pesan0 || "Kantong belum didistribusi ke QC");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  } else if (valid_periksa0 == "4") {
    alert(pesan0 || "Kantong belum dikonfirmasi");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  } else if (valid_periksa0 == "5") {
    alert(pesan0 || "Sudah serah terima QC, belum melakukan QC");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  } else if (valid_periksa0 == "6") {
    alert(pesan0 || "Kantong sudah pernah QC");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  } else if (valid_periksa0 == "7") {
    alert(pesan0 || "Hasil NAT tidak memenuhi");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  } else if (valid_periksa0 == "8") {
    alert(pesan0 || "Belum release");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  }

  if (valid_periksa0 != "1") {
    alert(pesan0 || "Data tidak valid");
    clearForm();
    document.bdrs.nokantong.focus();
    return false;
  }

  // Sisipkan row baru
  var row = tbody.insertRow(-1);

  var chkCell = row.insertCell(0);
  var chk = document.createElement("input");
  chk.type = "checkbox";
  chkCell.appendChild(chk);

  var cell1 = row.insertCell(1);
  var pNo = document.createElement("p");
  pNo.innerHTML = tbody.rows.length;
  cell1.appendChild(pNo);

  var hidden = document.createElement("input");
  hidden.type = "hidden";
  hidden.name = "nk[]";
  hidden.value = NoKantong;
  cell1.appendChild(hidden);

  var cell2 = row.insertCell(2);
  var p2 = document.createElement("p");
  p2.innerHTML = NoKantong;
  cell2.appendChild(p2);

  var cell3 = row.insertCell(3);
  var p3 = document.createElement("p");
  p3.innerHTML = gol_darah0;
  cell3.appendChild(p3);

  var cell4 = row.insertCell(4);
  var p4 = document.createElement("p");
  p4.innerHTML = RhesusDrh;
  cell4.appendChild(p4);

  var cell5 = row.insertCell(5);
  var p5 = document.createElement("p");
  p5.innerHTML = produk;
  cell5.appendChild(p5);

  var cell6 = row.insertCell(6);
  var p6 = document.createElement("p");
  p6.innerHTML = tgl_Aftap;
  cell6.appendChild(p6);

  var cell7 = row.insertCell(7);
  var p7 = document.createElement("p");
  p7.innerHTML = kadaluwarsa;
  cell7.appendChild(p7);

  var cell8 = row.insertCell(8);
  var p8 = document.createElement("p");
  p8.innerHTML = tglpengolahan;
  cell8.appendChild(p8);

  var cell9 = row.insertCell(9);
  var p9 = document.createElement("p");
  p9.innerHTML = jeniskantong;
  cell9.appendChild(p9);

  var cell10 = row.insertCell(10);
  var p10 = document.createElement("p");
  p10.innerHTML = volumeasal;
  cell10.appendChild(p10);

  var cell11 = row.insertCell(11);
  var suhuWrapper = document.createElement("div");
  suhuWrapper.style.display = "flex";
  suhuWrapper.style.alignItems = "center";
  suhuWrapper.style.gap = "4px";

  var inputSuhu = document.createElement("input");
  inputSuhu.type = "text";
  inputSuhu.name = "suhu_terima[]";
  inputSuhu.placeholder = "mis. -2.5";
  inputSuhu.style.width = "100px";

  var derajat = document.createElement("span");
  derajat.innerHTML = "&deg;C";
  derajat.style.fontSize = "12px";

  suhuWrapper.appendChild(inputSuhu);
  suhuWrapper.appendChild(derajat);

  cell11.appendChild(suhuWrapper);

  var note = document.createElement("div");
  note.innerHTML =
    "<small><i>Gunakan titik, bukan koma (mis. 36.5)</i></small>";
  note.style.marginTop = "2px";
  note.style.fontSize = "10px";
  note.style.color = "#666";
  cell11.appendChild(note);

  var cell12 = row.insertCell(12);
  var inputCatatan = document.createElement("textarea");
  inputCatatan.name = "catatan[]";
  inputCatatan.placeholder = "Tambah Catatan";
  inputCatatan.rows = "2";
  inputCatatan.cols = "20";
  cell12.appendChild(inputCatatan);

  clearForm();
  document.bdrs.nokantong.focus();
}

function deleteRow(tableID) {
  try {
    var table = document.getElementById(tableID);
    var tbody = table.getElementsByTagName("tbody")[0];

    for (var i = 0; i < tbody.rows.length; i++) {
      var row = tbody.rows[i];
      var chkbox = row.cells[0] && row.cells[0].childNodes[0];
      if (chkbox && chkbox.checked) {
        tbody.deleteRow(i);
        i--;
      }
    }

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
    url: "json_registrasiqc.php?NoKantong=" + encodeURIComponent(NoKantong),
    async: false,
    dataType: "json",
    success: function (json) {
      valid_periksa0 = json.valid || "0";
      pesan0 = json.pesan || "";

      if (valid_periksa0 == "1" && json.darah) {
        gol_darah0 = json.darah.gol_darah || "";
        RhesusDrh = json.darah.RhesusDrh || "";
        produk = json.darah.produk || "";
        tgl_Aftap = json.darah.tgl_Aftap || "";
        kadaluwarsa = json.darah.kadaluwarsa || "";
        tglpengolahan = json.darah.tglpengolahan || "";
        volumeasal = json.darah.volumeasal || "";
        jeniskantong = json.darah.jeniskantong || "";
      }
    },
    error: function () {
      valid_periksa0 = "0";
      pesan0 = "Terjadi kesalahan saat mengambil data kantong";
    },
  });
}
