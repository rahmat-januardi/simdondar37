// File: assets/js/irawanDB.js

$(document).ready(function () {
  loadKantongTable();
  loadRingkasanData();
  reloadFormFinal();

  $("#nomor_kantong").on("keydown", function (e) {
    if (e.which === 13 || e.which === 9) {
      // ENTER atau TAB
      e.preventDefault();
      insertKantong();
    }
  });

  // Load Kantong tanpa status-kantong.js
  // function loadKantongTable() {
  //   $("#tableContainer").load("ajax/pengirimanData.php");
  // }

  function reloadFormFinal() {
    $("#formFinal").closest("form").html("<div>Loading...</div>");
    $("#formFinal").load(" #formFinal > *", function () {
    });
  }

  function loadKantongTable() {
    $("#tableContainer").load("modul/st_komponen_rilis/ajax/pengirimanData-Only.php", function () {
      const statusCells = document.querySelectorAll(".status-kantong");
      //statusCells.forEach((cell) => {
        //const kode = cell.dataset.status;
        //const label = getStatus(kode);
	for (var i = 0; i < statusCells.length; i++) {
	var cell = statusCells[i];
	var kode = cell.getAttribute("data-status");
        var label = getStatus(kode);
        var style = getStatusClass(kode);
        cell.textContent = label;
        cell.setAttribute("style", style);
      //});
	}
      // STATUS KGD
      //const kgdCells = document.querySelectorAll(".status-kgd");
      //kgdCells.forEach((cell) => {
      //  const result = cell.dataset.kgd;
      //  const cssClass = getStatusKGD(result);
      //  cell.classList.add(...cssClass.split(" "));
      //});

      // STATUS ABS
      const absCells = document.querySelectorAll(".status-abs");
      absCells.forEach((cell) => {
        const result = cell.dataset.abs;
        const cssClass = getStatusABS(result);
        cell.classList.add(...cssClass.split(" "));
      });

    });
  }

  window.insertKantong = function () {
    var nomor = $("#nomor_kantong").val();
    if (nomor === "") {
      // alert("Silakan isi nomor kantong terlebih dahulu.");
      pesanInfo("Silakan isi nomor kantong terlebih dahulu.");
      $("#nomor_kantong").focus();
      return;
    }

    var fieldNames = {
      suhu_pengiriman: "Suhu Pengiriman",
      keadaan: "Keadaan",
      kode_alat: "Kode Alat",
      // tambahkan field lainnya sesuai kebutuhan
    };

    var formPengirimanData = {};
    var isValid = true;
    var missingFields = [];

    $("#formPengiriman")
      .serializeArray()
      .forEach(function (field) {
        if (field.value === "") {
          isValid = false;
          missingFields.push(field.name);
        }
        formPengirimanData[field.name] = field.value;
      });

    if (!isValid) {
      var missingFieldNames = missingFields.map(function (field) {
        return fieldNames[field] || field;
      });

      pesanWarning(
        "Silakan isi semua field yang diperlukan: " +
          missingFieldNames.join(", ")
      );
      return;
    }

    formPengirimanData["nomor_kantong"] = nomor;

    $.post(
      "modul/st_komponen_rilis/ajax/tambahDataPengiriman.php",
      // { nomor_kantong: nomor },
      formPengirimanData,
      function (res) {
        // console.log(res);
        if (res.status === "error") {
          // pesanWarning(res.message || "Kantong sudah ada di dalam daftar.");
          pesanError(res.message || "Kantong sudah ada di dalam daftar.");
        } else if (res.status === "success") {
          $("#nomor_kantong").val("").focus();
          loadKantongTable();
          loadRingkasanData();
  	  reloadFormFinal();
        } else {
          pesanError("Terjadi kesalahan: " + res.message);
        }
      }
    );
  };

  $(document).on("change", "#checkAll", function () {
    $(".checkbox-row").prop("checked", this.checked);
  });

  window.hapusTerpilih = function () {
    var checked = [];
    $('input[name="ck_kantong[]"]:checked').each(function () {
      checked.push($(this).val());
    });

    if (checked.length === 0) return;

    // console.log("checked:", checked); // debug jumlah dicentang oleh user
    pesanKonfirmasi(
      "Apakah Anda yakin ingin menghapus " + checked.length + " data?"
    ).then(function (result) {
      // Changed arrow function to traditional function for PHP 5.3 compatibility
      // console.log(result); // debuigg
      if (result.isConfirmed) {
        $.post("modul/st_komponen_rilis/ajax/delRowPengiriman.php", { multi: checked }, function (res) {
          //console.log(res); //debug response
          loadKantongTable();
          loadRingkasanData();
  	  reloadFormFinal();
        });
      } else {
        // console.log("Penghapusan dibatalkan.");
      }
    });
  };

  //   $("#tableContainer").on("click", ".kantong-detail", function () {
  //     var id = $(this).data("id");
  //     $.get("ajax/dKantong.php", { id: id }, function (html) {
  //       $("#detailContent").html(html);
  //       $("#detailModal").modal("show");
  //     });
  //   });

  $(document).on("click", ".kantongDetail", function (e) {
    e.preventDefault();
    var csd_id = $(this).data("id");

    $("#detailModal").data("id", csd_id);

    $("#detailContent").html("Loading...");
    $("#detailModal").modal("show");

    $.ajax({
      url: "modul/st_komponen_rilis/ajax/dKantong.php",
      type: "GET",
      data: { id: csd_id },
      success: function (response) {
        $("#detailContent").html(response);
      },
      error: function () {
        $("#detailContent").html("Error loading data.");
      },
    });
  });

  $(document).on("click", "#btnEditKantong", function () {
    $(".view-only").hide();
    $(".edit-only").show();
    $("#btnEditKantong").hide();
    $("#btnSimpanKantong").show();
  });

  $(document).on("click", "#btnSimpanKantong", function () {
    const data = {
      id: $("#detailModal").data("id"),
      // no_kantong: $('input[name="no_kantong"]').val(),
      // kode_donor: $('input[name="kode_donor"]').val(),
      // gol_darah: $('input[name="gol_darah"]').val(),
      petugas: $('input[name="petugasInput"]').val(),
    };

    $.post(
      "modul/st_komponen_rilis/ajax/upDKantong.php",
      data,
      function (res) {
        if (res.status === "ok") {
          $("#detailModal").modal("hide");
          loadKantongTable();
          loadRingkasanData();
  	  reloadFormFinal();
        } else {
          // alert("Gagal menyimpan!");
          pesanError(res.message);
        }
      },
      "json"
    );
  });

  $("#tableContainer").on("click", ".pendonor-detail", function () {
    var id = $(this).data("id");
    $.get("modul/st_komponen_rilis/ajax/dDonor.php", { id: id }, function (html) {
      $("#detailContent").html(html);
      $("#detailModal").modal("show");
    });
  });

  $(document).on("click", ".pendonorDetail", function (e) {
    e.preventDefault();
    var csd_kodeDonor = $(this).data("id");

    $("#detailContent").html("Loading...");
    $("#detailModal").modal("show");

    $.ajax({
      url: "modul/st_komponen_rilis/ajax/dDonor.php",
      type: "GET",
      data: { id: csd_kodeDonor },
      success: function (response) {
        $("#detailContent").html(response);
      },
      error: function () {
        $("#detailContent").html("Error loading data.");
      },
    });
  });

  $("#tableContainer").on("click", ".hapus-satu", function () {
    // console.log($("#tableContainer").html());

    var id = $(this).data("kantong");
    if (!confirm("Yakin hapus data ini?")) return;
    $.post("modul/st_komponen_rilis/ajax/delRowPengiriman.php", { nomor_kantong: id }, function (res) {
      loadKantongTable();
      loadRingkasanData();
      reloadFormFinal();
    });
  });

  // function loadRingkasanData() {
  //   $.ajax({
  //     url: "ajax/rData.php",
  //     method: "GET",
  //     dataType: "json",
  //     success: function (data) {
  //       // Update jumlah
  //       $("#jmlTotal").text(data.total);
  //       $("#jmlNR").text(data.nr);
  //       $("#jmlR").text(data.r);

  //       // Update per produk
  //       let html = "<ul>";
  //       for (let produk in data.per_produk) {
  //         html += `<li>${produk}: <strong>${data.per_produk[produk]}</strong> kantong</li>`;
  //       }
  //       html += "</ul>";

  //       $("#perProduk").html(html);
  //     },
  //     error: function () {
  //       $("#perProduk").html("<span class='text-danger'>Gagal memuat data produk</span>");
  //     }
  //   });
  // }

  function loadRingkasanData() {
    $.ajax({
      url: "modul/st_komponen_rilis/ajax/rData.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        // Update jumlah total data
        $("#jmlTotal").text(data.total);
        $("#jmlNR").text(data.nr);
        $("#jmlR").text(data.r);
        // $("#jmlGZ").text(data.gz);

        let html = "";

        // let htmlProduk = "<ul>";
        let htmlProduk = "";
        for (let produk in data.per_produk) {
          htmlProduk += `<li>${produk}: <strong>${data.per_produk[produk]}</strong> kantong</li>`;
        }
        // htmlProduk += "</ul>";
        htmlProduk += "";

        $("#perProduk").html(htmlProduk);

        // Jenis Kantong
        let htmlJenis = "<ul>";
        for (let jenis in data.jenis_kantong) {
          htmlJenis += `<li>${jenis}: <strong>${data.jenis_kantong[jenis]}</strong></li>`;
        }
        htmlJenis += "</ul>";
        $("#jenisKantong").html(htmlJenis);

        // Metoda Kantong
        let htmlMetoda = "<ul>";
        for (let metode in data.metoda_kantong) {
          htmlMetoda += `<li>${metode}: <strong>${data.metoda_kantong[metode]}</strong></li>`;
        }
        htmlMetoda += "</ul>";
        $("#metodaKantong").html(htmlMetoda);

        html += '<div class="row text-center">';
        html += `
          <div class="col-md-6">
            <table class="table table-striped table-bordered text-center">
                <tbody>
                    <tr style="background-color: #d9534f; color:white; font-weight:bold;">
                      <td colspan="5"><strong>Jenis Ktg</strong></td>
                      <td colspan="4"><strong>Kategori</strong></td>
                    </tr>
                    <tr style="background-color: #d9534f; color:white;">
                      <td>SG</td>
                      <td>DB</td>
                      <td>TR</td>
                      <td>QD</td>
                      <td>PB</td>
                      <td>Biasa</td>
                      <td>TT</td>
                      <td>TB</td>
                      <td>Filter</td>
                    </tr>
                    <tr>
                      <td><strong>
                      ${data.jenis_kantong.Single ?? 0}
                      </strong></td>
                      <td><strong>
                      ${data.jenis_kantong.Double ?? 0}
                      </strong></td>
                      <td><strong>${
                        data.jenis_kantong.Tripple ?? 0
                      }</strong></td>
                      <td><strong>
                      ${data.jenis_kantong.Quadruple ?? 0}
                      </strong></td>
                      <td>
                      ${data.jenis_kantong.Pediatrik ?? 0}
                      </td>
                      <td><strong>
                      ${data.metoda_kantong.Lainnya ?? 0}
                      </strong></td>
                      <td><strong>
                      ${data.metoda_kantong["Top & Top (TT)"] ?? 0}
                      </strong></td>
                      <td><strong>
                      ${data.metoda_kantong["Top & Bottom (TB)"] ?? 0}
                      </strong></td>
                      <td><strong>
                      ${data.metoda_kantong.Filter ?? 0}
                      </strong></td>
                    </tr>
                </tbody>
            </table>
          </div>
        </div>`;

        // html += `
        //   <div class="col-md-6">
        //     <div class="panel panel-default">
        //       <div class="panel-heading">Single (SG)</div>
        //       <div class="panel-body"><strong>${
        //         data.jenis_kantong.Single ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        //   <div class="col-md-2">
        //     <div class="panel panel-default">
        //       <div class="panel-heading">Double (DB)</div>
        //       <div class="panel-body"><strong>${
        //         data.jenis_kantong.Double ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        //   <div class="col-md-2">
        //     <div class="panel panel-default">
        //       <div class="panel-heading">Tripple (TR)</div>
        //       <div class="panel-body"><strong>${
        //         data.jenis_kantong.Tripple ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        //   <div class="col-md-2">
        //     <div class="panel panel-default">
        //       <div class="panel-heading">Quadruple (QD)</div>
        //       <div class="panel-body"><strong>${
        //         data.jenis_kantong.Quadruple ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        //   <div class="col-md-2">
        //     <div class="panel panel-default">
        //       <div class="panel-heading">Pediatrik (PB)</div>
        //       <div class="panel-body"><strong>${
        //         data.jenis_kantong.Pediatrik ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        // </div>`;

        // // Bagian Kategori (Metoda)
        // html += `
        // <div class="row text-center" style="margin-top: 10px;">
        //   <div class="col-md-2">
        //     <div class="panel panel-info">
        //       <div class="panel-heading">Biasa</div>
        //       <div class="panel-body"><strong>${
        //         data.metoda_kantong.Lainnya ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        //   <div class="col-md-2">
        //     <div class="panel panel-success">
        //       <div class="panel-heading">Top & Top (TT)</div>
        //       <div class="panel-body"><strong>${
        //         data.metoda_kantong["Top & Top (TT)"] ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        //   <div class="col-md-2">
        //     <div class="panel panel-warning">
        //       <div class="panel-heading">Top & Bottom (TB)</div>
        //       <div class="panel-body"><strong>${
        //         data.metoda_kantong["Top & Bottom (TB)"] ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        //   <div class="col-md-2">
        //     <div class="panel panel-danger">
        //       <div class="panel-heading">Filter</div>
        //       <div class="panel-body"><strong>${
        //         data.metoda_kantong.Filter ?? 0
        //       }</strong></div>
        //     </div>
        //   </div>
        // </div>
        // `;

        // Header kolom (baris pertama dan kedua)
        // html += `
        // <div class="row text-center">
        //   <div class="col-md-10 col-md-offset-1">
        //     <div class="row">
        //       <div class="col-md-5"><strong>Jenis Kantong</strong></div>
        //       <div class="col-md-4 col-md-offset-1"><strong>Kategori</strong></div>
        //     </div>
        //     <div class="row" style="margin-bottom: 10px;">
        //       <div class="col-md-1">SG</div>
        //       <div class="col-md-1">DB</div>
        //       <div class="col-md-1">TR</div>
        //       <div class="col-md-1">QD</div>
        //       <div class="col-md-1">PB</div>
        //       <div class="col-md-1 col-md-offset-1">Biasa</div>
        //       <div class="col-md-1">TT</div>
        //       <div class="col-md-1">TB</div>
        //       <div class="col-md-1">Filter</div>
        //     </div>
        // `;

        // html += `
        //     <div class="row text-center">
        //       <div class="col-md-1">
        //         <div class="panel panel-default"><div class="panel-body">${data.jenis_kantong.Single ?? 0}</div></div>
        //       </div>
        //       <div class="col-md-1">
        //         <div class="panel panel-default"><div class="panel-body">${data.jenis_kantong.Double ?? 0}</div></div>
        //       </div>
        //       <div class="col-md-1">
        //         <div class="panel panel-default"><div class="panel-body">${data.jenis_kantong.Tripple ?? 0}</div></div>
        //       </div>
        //       <div class="col-md-1">
        //         <div class="panel panel-default"><div class="panel-body">${data.jenis_kantong.Quadruple ?? 0}</div></div>
        //       </div>
        //       <div class="col-md-1">
        //         <div class="panel panel-default"><div class="panel-body">${data.jenis_kantong.Pediatrik ?? 0}</div></div>
        //       </div>

        //       <div class="col-md-1 col-md-offset-1">
        //         <div class="panel panel-info"><div class="panel-body">${data.metoda_kantong.Lainnya ?? 0}</div></div>
        //       </div>
        //       <div class="col-md-1">
        //         <div class="panel panel-success"><div class="panel-body">${data.metoda_kantong["Top & Top (TT)"] ?? 0}</div></div>
        //       </div>
        //       <div class="col-md-1">
        //         <div class="panel panel-warning"><div class="panel-body">${data.metoda_kantong["Top & Bottom (TB)"] ?? 0}</div></div>
        //       </div>
        //       <div class="col-md-1">
        //         <div class="panel panel-danger"><div class="panel-body">${data.metoda_kantong.Filter ?? 0}</div></div>
        //       </div>
        //     </div>
        //   </div>
        // </div>
        // `;

        // canvas html chartjs kwkwkwk
        // html += `
        //   <div class="row" style="margin-top: 20px;">
        //     <div class="col-md-12">
        //       <canvas id="chartJenisKantong" height="100"></canvas>
        //     </div>
        //   </div>
        // `;

        $("#rekapKantong").html(html);

        // chartjs
        // new Chart(document.getElementById("chartJenisKantong"), {
        //   type: "bar",
        //   data: {
        //     labels: [
        //       "SG",
        //       "DB",
        //       "TR",
        //       "QD",
        //       "PB",
        //       "TT",
        //       "TB",
        //       "Filter",
        //       "Biasa",
        //     ],
        //     datasets: [
        //       {
        //         label: "Jumlah Kantong",
        //         data: [
        //           data.jenis_kantong.Single ?? 0,
        //           data.jenis_kantong.Double ?? 0,
        //           data.jenis_kantong.Tripple ?? 0,
        //           data.jenis_kantong.Quadruple ?? 0,
        //           data.jenis_kantong.Pediatrik ?? 0,
        //           data.metoda_kantong["Top & Top (TT)"] ?? 0,
        //           data.metoda_kantong["Top & Bottom (TB)"] ?? 0,
        //           data.metoda_kantong.Filter ?? 0,
        //           data.metoda_kantong.Lainnya ?? 0,
        //         ],
        //         backgroundColor: [
        //           "#3498db",
        //           "#2ecc71",
        //           "#9b59b6",
        //           "#f1c40f",
        //           "#e67e22",
        //           "#1abc9c",
        //           "#e74c3c",
        //           "#95a5a6",
        //           "#7f8c8d",
        //         ],
        //       },
        //     ],
        //   },
        //   options: {
        //     responsive: true,
        //     plugins: {
        //       legend: { display: false },
        //       title: { display: true, text: "Rekap Jenis dan Metoda Kantong" },
        //     },
        //     scales: {
        //       y: { beginAtZero: true, precision: 0 },
        //     },
        //   },
        // });
      },
      error: function () {
        $("#perProduk").html(
          "<span class='text-danger'>Gagal memuat data produk</span>"
        );
        $("#jenisKantong").html(
          "<span class='text-danger'>Gagal memuat data jenis kantong</span>"
        );
        $("#metodaKantong").html(
          "<span class='text-danger'>Gagal memuat data metoda kantong</span>"
        );
        $("#jmlTotal").text("0");
        $("#jmlNR").text("0");
        $("#jmlR").text("0");
        // $("#jmlGZ").text("0");
      },
    });
  }

  $("#formFinal").on("submit", function (e) {
    e.preventDefault();

    let formData = $(this).serialize();

    $.ajax({
      url: "modul/st_komponen_rilis/ajax/sData.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          pesanSukses(res.message);
          // window.location.reload();
        } else {
          pesanError(res.message);
        }
      },
      error: function (xhr) {
        pesanError("AJAX Error", "<br>Response: " + xhr.responseText);
        // console.error("XHR Response:", xhr.responseText);
      },
    });
  });
});
