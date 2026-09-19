/* script.js */

function handleKeyPress(event) {
  var nomorKantong = document.getElementById("nomorKantong").value;
  // var tglPengerjaan = document.getElementById("tglPengerjaan").value;
  var alatPemutaran = document.getElementById("alatPemutaran").value;
  var alatPemisahan = document.getElementById("alatPemisahan").value;
  var alatPembekuan = document.getElementById("alatPembekuan").value;
  var jMPutar = document.getElementById("jMPutar").value.trim();
  var jSPutar = document.getElementById("jSPutar").value.trim();
  var jMPisah = document.getElementById("jMPisah").value.trim();
  var jSPisah = document.getElementById("jSPisah").value.trim();
  var jMBeku = document.getElementById("jMBeku").value.trim();
  var jSBeku = document.getElementById("jSBeku").value.trim();
  var shift = document.getElementById("shift").value;

  // Normalisasi tglPengerjaan ke format Y-m-d H:i:s
  let tglPengerjaanRaw = document.getElementById("tglPengerjaan").value;
  let tglPengerjaan = "";
  if (tglPengerjaanRaw) {
    let m = tglPengerjaanRaw.match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?$/);
    if (m) {
      tglPengerjaan =
        m[1] +
        "-" +
        m[2] +
        "-" +
        m[3] +
        " " +
        (m[4] || "00") +
        ":" +
        (m[5] || "00") +
        ":" +
        (m[6] || "00");
    }
  }

  // Cek apakah semua field waktu telah diisi
  if (!jMPutar || !jSPutar || !jMPisah || !jSPisah) {
    event.preventDefault(); // Mencegah input pada field nomorKantong
    alert("Silakan isi semua field waktu, sebelum memasukkan nomor kantong.");
    return; // Keluar dari fungsi jika field waktu belum diisi
  }

  if (event.key === "Enter") {
    nomorKantong = event.target.value.trim(); // Trim untuk menghapus spasi ekstra
    console.log("Nomor Kantong:", nomorKantong);
    if (nomorKantong) {
      if (isValidNomorKantong(nomorKantong)) {
        insertData(
          nomorKantong,
          tglPengerjaan,
          alatPemutaran,
          alatPemisahan,
          alatPembekuan,
          jMPutar,
          jSPutar,
          jMPisah,
          jSPisah,
          jMBeku,
          jSBeku,
          shift
        );
      } else {
        // showAlert("Nomor Kantong tidak valid. Harap periksa kembali.");
        showModal("Nomor Kkantong tidak valid. Harap periksa kembali.");
      }
    }
  }
}

function isValidNomorKantong(nomorKantong) {
  // Contoh validasi: nomor kantong harus terdiri dari angka
  //return /^\d+$/.test(nomorKantong); // apabila hanya angka saja nomor kantongnya
  return /^[a-zA-Z0-9]+$/.test(nomorKantong); // apabila huruf dan angka nomor kantongnya
}

function insertData(
  nomorKantong,
  tglPengerjaan,
  alatPemutaran,
  alatPemisahan,
  alatPembekuan,
  jMPutar,
  jSPutar,
  jMPisah,
  jSPisah,
  jMBeku,
  jSBeku,
  shift
) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "modul/pengolahan/pengolahan_temp.php", true);
  // xhr.open("POST", "pengolahan_temp.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    if (xhr.status === 200) {
      try {
        const response = JSON.parse(xhr.responseText);
        if (response.status === "error") {
          showModal(response.message); // Tampilkan modal dengan pesan error
        } else if (response.status === "success") {
          if (response.sudahDiolah) {
            // Kantong sudah pernah diolah sebelumnya -> notifikasi mode UPDATE
            showInfoModal(
              "Kantong <b>" +
                nomorKantong +
                "</b> sudah pernah diolah sebelumnya.<br>" +
                "Data sebelumnya akan diperbarui (mode UPDATE) setelah disimpan.",
              function () {
                window.location.reload();
              }
            );
          } else {
            window.location.reload(); // Refresh halaman jika sukses
          }
        }
      } catch (e) {
        console.error("JSON Parse error:", e);
        showModal("Kesalahan saat memproses data dari server.");
      }
    } else {
      console.error("JSON Parse error:", e);
      showModal("Terjadi kesalahan saat menyimpan data.");
    }
  };

  xhr.onerror = function () {
    showModal("Terjadi kesalahan koneksi. Mohon coba lagi.");
  };

  xhr.send(
    "nomorKantong=" +
      encodeURIComponent(nomorKantong) +
      "&tglPengerjaan=" +
      encodeURIComponent(tglPengerjaan) +
      "&alatPemutaran=" +
      encodeURIComponent(alatPemutaran) +
      "&alatPemisahan=" +
      encodeURIComponent(alatPemisahan) +
      "&alatPembekuan=" +
      encodeURIComponent(alatPembekuan) +
      "&jamMulaiPutar=" +
      encodeURIComponent(jMPutar) +
      "&jamSelesaiPutar=" +
      encodeURIComponent(jSPutar) +
      "&jamMulaiPisah=" +
      encodeURIComponent(jMPisah) +
      "&jamSelesaiPisah=" +
      encodeURIComponent(jSPisah) +
      "&jamMulaiBeku=" +
      encodeURIComponent(jMBeku) +
      "&jamSelesaiBeku=" +
      encodeURIComponent(jSBeku) +
      "&shift=" +
      encodeURIComponent(shift)
  ); // Pastikan untuk encode parameter
}

function showModal(message) {
  $("#errorModal .modal-body").html(message);
  $("#errorModal").modal("show");
}

function showInfoModal(message, onClose) {
  $("#infoModal .modal-body").html(message);
  $("#infoModal")
    .off("hidden.bs.modal")
    .on("hidden.bs.modal", function () {
      if (typeof onClose === "function") {
        onClose();
      }
    });
  $("#infoModal").modal("show");
}
