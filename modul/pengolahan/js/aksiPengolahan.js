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
    let d = new Date(tglPengerjaanRaw);
    if (!isNaN(d.getTime())) {
      tglPengerjaan =
        d.getFullYear() +
        "-" +
        ("0" + (d.getMonth() + 1)).slice(-2) +
        "-" +
        ("0" + d.getDate()).slice(-2) +
        " " +
        ("0" + d.getHours()).slice(-2) +
        ":" +
        ("0" + d.getMinutes()).slice(-2) +
        ":" +
        ("0" + d.getSeconds()).slice(-2);
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
          shift,
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
  shift,
  force = 0,
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
          showModal(response.message);
        } else if (response.status === "confirm") {
          // tampilkan modal confirm
          $("#confirmModalBody").html(response.message);
          $("#confirmModal").modal("show");

          // tombol YA
          $("#confirmModalYa")
            .off("click")
            .on("click", function () {
              $("#confirmModal").modal("hide");

              // kirim ulang dengan force=1
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
                shift,
                1, // force
              );
            });
        } else if (response.status === "success") {
          window.location.reload();
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
      encodeURIComponent(shift) +
      "&force=" +
      encodeURIComponent(force),
  ); // Pastikan untuk encode parameter
}

function showModal(message) {
  $("#errorModal .modal-body").html(message);
  $("#errorModal").modal("show");
}
