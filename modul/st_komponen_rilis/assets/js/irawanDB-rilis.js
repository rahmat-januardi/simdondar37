$(document).ready(function () {
  loadKantongTable();
  loadRingkasanData();

  $("#nomor_kantong").on("keydown", function (e) {
    if (e.which === 13 || e.which === 9) {
      // ENTER atau TAB
      e.preventDefault();
      insertKantong();
    }
  });

  function loadKantongTable() {
    const noTrans = $("#nttr").val();
    $("#tableContainer").load(
      "ajax/pengirimanData.php",
      { nT: noTrans },
      function () {
        const statusCells = document.querySelectorAll(".status-kantong");
        statusCells.forEach((cell) => {
          const kode = cell.dataset.status;
          const label = getStatus(kode);
          // const cssClass = getStatusClass(kode);
          const style = getStatusClass(kode);
          cell.textContent = label;

          // cell.classList.add(...cssClass.split(" "));
          cell.setAttribute("style", style);

          setTimeout(() => {
            const jmlSah = window.jumlahSah || 0;
            const jmlData = window.jumlahData || 0;

            if (jmlData === jmlSah) {
              // $("#btnTerimaTerpilih").prop("disabled", window.jumlahData === window.jumlahSah);
              $("#btnTerimaTerpilih").prop("disabled", true);

              if (jmlSah > 0) {
                $("#simpanLanjut").prop("disabled", false);
              } else {
                $("#simpanLanjut").prop("disabled", true);
              }

              // $("#checkAll").prop("disabled", true); // checkbox all
            } else {
              $("#btnTerimaTerpilih").prop("disabled", false);

              if (jmlSah > 0) {
                $("#simpanLanjut").prop("disabled", false);
              } else {
                $("#simpanLanjut").prop("disabled", true);
              }

              // $("#checkAll").prop("disabled", false);
            }
          }, 300);
        });

        // STATUS KGD
        // const kgdCells = document.querySelectorAll(".status-kgd");
        // kgdCells.forEach((cell) => {
        //   const result = cell.dataset.kgd;
        //   const cssClass = getStatusKGD(result);
        //   cell.classList.add(...cssClass.split(" "));
        // });

        // STATUS ABS
        const absCells = document.querySelectorAll(".status-abs");
        absCells.forEach((cell) => {
          const result = cell.dataset.abs;
          const cssClass = getStatusABS(result);
          cell.classList.add(...cssClass.split(" "));
        });
      }
    );
  }

  function validasiFormPengiriman() {
    var fieldNames = {
      suhu_pengiriman: "Suhu Penerimaan",
      keadaan: "Keadaan",
      kode_alat: "Kode Alat",
      // tambahkan jika ada field lain yang wajib
    };

    var isValid = true;
    var missingFields = [];

    $("#formPengiriman")
      .serializeArray()
      .forEach(function (field) {
        if (field.value === "" && fieldNames[field.name]) {
          isValid = false;
          missingFields.push(field.name);
        }
      });

    if (!isValid) {
      var missingFieldNames = missingFields.map(function (field) {
        return fieldNames[field] || field;
      });

      pesanWarning(
        "Silakan isi semua field yang diperlukan: " +
          missingFieldNames.join(", ")
      );

      return false;
    }

    return true;
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
      suhu_pengiriman: "Suhu Penerimaan",
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
      "ajax/terimaDataPengiriman.php",
      // { nomor_kantong: nomor },
      formPengirimanData,
      function (res) {
        $("#nomor_kantong").val("").focus();
        loadKantongTable();
        loadRingkasanData();
      }
    );
  };

  // memperngaruhi seluruhnya checkbox tanpa memperhatikan nilai dst_sah masih 0 atau sudah 1
  // $(document).on("change", "#checkAll", function () {
  //   $(".checkbox-row").prop("checked", this.checked);
  // });

  // Agar hanya memperngaruhi checkbox yang enabled atau dst_sah masih 0 saja
  $(document).on("change", "#checkAll", function () {
    $(".checkbox-row:not(:disabled)").prop("checked", this.checked);
  });

  window.terimaTerpilih = function () {
    var checked = [];
    // mengecek semua tanpa melihat DOM disabled atau enabel begitu lah
    // $('input[name="ck_kantong[]"]:checked').each(function () {
    $('input[name="ck_kantong[]"]:checked:not(:disabled)').each(function () {
      checked.push($(this).val());
    });

    if (checked.length === 0) return;

    // VALIDASI DULU
    if (!validasiFormPengiriman()) {
      return;
    }

    pesanKonfirmasi(
      "Apakah Anda yakin ingin memproses " + checked.length + " data?"
    ).then((result) => {
      // console.log(result); // debuigg
      if (result.isConfirmed) {
        // ambil field dengan nama suhu_pengirimaana didalam index
        const suhu = $('input[name="suhu_pengiriman"]').val();
        $.post(
          "ajax/terimaDataPengiriman.php",
          { multi: checked, suhu_pengiriman: suhu },
          function (res) {
            // console.log(res); //debug response
            loadKantongTable();
            loadRingkasanData();
          }
        );
      } else {
        // console.log("Penghapusan dibatalkan.");
      }
    });
  };

  $(document).on("click", ".kantongDetail", function (e) {
    e.preventDefault();
    var csd_id = $(this).data("id");

    $("#detailModal").data("id", csd_id);

    $("#detailContent").html("Loading...");
    $("#detailModal").modal("show");

    $.ajax({
      url: "ajax/dKantong-Rilis.php",
      type: "GET",
      data: { id: csd_id },
      success: function (response) {
        $("#detailContent").html(response);

        // Jalankan setelah konten dimuat ke DOM
        const statusCells = document.querySelectorAll(".status-kantong");
        statusCells.forEach((cell) => {
          const kode = cell.dataset.status;
          const label = getStatus(kode);
          // const cssClass = getStatusClass(kode);
          const style = getStatusClass(kode);
          cell.textContent = label;
          // cell.classList.add(...cssClass.split(" "));
          cell.setAttribute("style", style);
        });

        // STATUS KGD
        // const kgdCells = document.querySelectorAll(".status-kgd");
        // kgdCells.forEach((cell) => {
        //   const result = cell.dataset.kgd;
        //   const cssClass = getStatusKGD(result);
        //   cell.classList.add(...cssClass.split(" "));
        // });

        // STATUS ABS
        const absCells = document.querySelectorAll(".status-abs");
        absCells.forEach((cell) => {
          const result = cell.dataset.abs;
          const cssClass = getStatusABS(result);
          cell.classList.add(...cssClass.split(" "));
        });
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
      "ajax/upDKantong.php",
      data,
      function (res) {
        if (res.status === "ok") {
          $("#detailModal").modal("hide");
          loadKantongTable();
          loadRingkasanData();
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
    $.get("ajax/dDonor.php", { id: id }, function (html) {
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
      url: "ajax/dDonor.php",
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
    $.post("ajax/delRowPengiriman.php", { nomor_kantong: id }, function (res) {
      loadKantongTable();
      loadRingkasanData();
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
      url: "ajax/rData-Rilis.php",
      method: "GET",
      data: {
        nT: nT,
      },
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
        <table class="table table-striped table-bordered">
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
                    <td><strong>${data.jenis_kantong.Tripple ?? 0}</strong></td>
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

  // untuk btnSimpanLanjut
  $("#simpanLanjut").on("click", function () {
    $("#formFinal").trigger("submit");
  });

  $("#formFinal").on("submit", function (e) {
    e.preventDefault();

    // 1. Jalankan validasi terlebih dahulu
    if (!validasiFormPengiriman()) {
      return; // hentikan proses jika form tidak valid
    }

    const nT = $('input[name="dst_nt"]').val();
    const formData = $(this).serialize();

    $.post(
      "ajax/cekBelumSah.php",
      { dst_nt: nT },
      function (res) {
        if (res.status !== "ok") {
          Swal.fire("Gagal", res.message, "error");
          return;
        }

        const belumSah = parseInt(res.belumSah, 10);
        const total = parseInt(res.total, 10);
        const akanDiproses = total - belumSah;

        if (belumSah > 0) {
          Swal.fire({
            title: "Konfirmasi",
            html: `Masih terdapat <b>${belumSah}</b> Data belum dikonfirmasi dari Total <b>${total}</b> data.<br>Apakah Anda yakin untuk melanjutkan <b>${akanDiproses}</b> data untuk diproses?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Lanjutkan",
            cancelButtonText: "Batal",
          }).then((result) => {
            console.log("Konfirmasi Swal muncul, hasil:", result);
            if (result.isConfirmed) {
              lanjutkanFinalisasi(formData);
            }
          });
        } else {
          lanjutkanFinalisasi(formData);
        }

        console.log("belumSah:", belumSah);
        console.log("total:", total);
        console.log("akanDiproses:", akanDiproses);

        console.log("RESPON DARI cekBelumSah.php:", res);
        console.log("Tipe res:", typeof res);
      },
      "json"
    );
  });

  // function lanjutkanFinalisasi(formData) {
  //   const suhu = $('input[name="suhu_pengiriman"]').val();

  //   // Tambahkan suhu ke formData
  //   formData += `&suhu_pengiriman=${encodeURIComponent(suhu)}`;

  //   $.ajax({
  //     url: "ajax/fPengirimanData.php",
  //     type: "POST",
  //     data: formData,
  //     dataType: "json",
  //     success: function (res) {
  //       if (res.status === "success") {
  //         pesanSukses(res.message);
  //         // window.location.reload();
  //         // Swal.fire("Sukses", res.message, "success");
  //         // loadKantongTable();
  //       } else {
  //         pesanError(res.message);
  //         // Swal.fire("Gagal", res.message, "error");
  //       }
  //     },
  //     error: function (xhr) {
  //       pesanError("AJAX Error", "<br>Response: " + xhr.responseText);
  //       // console.error("XHR Response:", xhr.responseText);
  //     },

  //     // error: function (xhr) {
  //     //   Swal.fire("Error", "AJAX Error:<br>" + xhr.responseText, "error");
  //     // },
  //   });
  // }

  function lanjutkanFinalisasi(formData) {
    const suhu = $('input[name="suhu_pengiriman"]').val();
    formData += `&suhu_pengiriman=${encodeURIComponent(suhu)}`;

    $.post(
      "ajax/fPengirimanData.php",
      formData,
      function (res) {
        console.log(res);
        if (res.status === "ok") {
          // Swal.fire("Sukses", res.message, "success");
          // loadKantongTable();
          Swal.fire({
            title: "Sukses",
            text: res.message,
            icon: "success",
            confirmButtonText: "OK",
          }).then(() => {
            window.location = "../../pmiqa.php?module=sr_rilis";
          });
        } else {
          Swal.fire("Gagal", res.message, "error");
        }
      },
      "json"
    ).fail(function (jqXHR, textStatus, errorThrown) {
      console.error("AJAX Error:", textStatus, errorThrown);
      console.error("Response Text:", jqXHR.responseText);
      Swal.fire("Error", `Gagal memproses data: ${textStatus}`, "error");
    });
  }

  // $("#formFinal").on("submit", function (e) {
  //   e.preventDefault();

  //   let formData = $(this).serialize();

  //   $.ajax({
  //     url: "ajax/sData.php",
  //     type: "POST",
  //     data: formData,
  //     dataType: "json",
  //     success: function (res) {
  //       if (res.status === "success") {
  //         pesanSukses(res.message);
  //         // window.location.reload();
  //       } else {
  //         pesanError(res.message);
  //       }
  //     },
  //     error: function (xhr) {
  //       pesanError("AJAX Error", "<br>Response: " + xhr.responseText);
  //       // console.error("XHR Response:", xhr.responseText);
  //     },
  //   });
  // });
});

// $(document).on("submit", "#formFinal", function (e) {
//   e.preventDefault(); // hindari submit form default

//   const formData = $(this).serialize(); // ambil semua isi form (dst_us dan dst_nt)

//   Swal.fire({
//     title: "Konfirmasi",
//     text: "Yakin ingin menyimpan dan melanjutkan?",
//     icon: "question",
//     showCancelButton: true,
//     confirmButtonText: "Ya, lanjutkan",
//     cancelButtonText: "Batal"
//   }).then((result) => {
//     if (result.isConfirmed) {
//       $.post("ajax/fPengirimanData.php", formData, function (res) {
//         // bisa pakai res.status/res.message jika JSON
//         Swal.fire("Sukses", "Data berhasil disimpan dan dilanjutkan!", "success");
//         loadKantongTable();
//       }).fail(function () {
//         Swal.fire("Gagal", "Terjadi kesalahan saat menyimpan data.", "error");
//       });
//     }
//   });
// });

