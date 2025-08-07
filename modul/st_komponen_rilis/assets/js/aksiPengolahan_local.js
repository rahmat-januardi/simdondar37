/* script.js */

function handleKeyPress(event) {
  var alatPemutaran = document.getElementById("alatPemutaran").value;
  var alatPemisahan = document.getElementById("alatPemisahan").value;
  var jMPutar = document.getElementById("jMPutar").value.trim();
  var jSPutar = document.getElementById("jSPutar").value.trim();
  var jMPisah = document.getElementById("jMPisah").value.trim();
  var jSPisah = document.getElementById("jSPisah").value.trim();
  var shift = document.getElementById("shift").value;

  // Cek apakah semua field waktu telah diisi
  if (!jMPutar || !jSPutar || !jMPisah || !jSPisah) {
    event.preventDefault(); // Mencegah input pada field nomorKantong
    alert("Silakan isi semua field waktu, sebelum memasukkan nomor kantong.");
    return; // Keluar dari fungsi jika field waktu belum diisi
  }

  if (event.key === "Enter") {
    const nomorKantong = event.target.value.trim(); // Trim untuk menghapus spasi ekstra
    if (nomorKantong) {
      if (isValidNomorKantong(nomorKantong)) {
        insertData(nomorKantong, alatPemutaran, alatPemisahan, jMPutar, jSPutar, jMPisah, jSPisah, shift);
      } else {
        // showAlert("Nomor Kantong tidak valid. Harap periksa kembali.");
        showModal("Nomor Kantong tidak valid. Harap periksa kembali.");
      }
    }
  }
}

function isValidNomorKantong(nomorKantong) {
  // Contoh validasi: nomor kantong harus terdiri dari angka
  //return /^\d+$/.test(nomorKantong); // apabila hanya angka saja nomor kantongnya
  return /^[a-zA-Z0-9]+$/.test(nomorKantong); // apabila huruf dan angka nomor kantongnya
}

function insertData(nomorKantong, alatPemutaran, alatPemisahan, jMPutar, jSPutar, jMPisah, jSPisah, shift) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "pengolahan_temp.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    if (xhr.status === 200) {
      const response = JSON.parse(xhr.responseText);
      if (response.status === "error") {
        showModal(response.message); // Tampilkan modal dengan pesan error
      } else if (response.status === "success") {
        window.location.reload(); // Refresh halaman jika sukses
      }
    } else {
      showModal("Terjadi kesalahan saat menyimpan data.");
    }
  };

  xhr.onerror = function () {
    showModal("Terjadi kesalahan koneksi. Mohon coba lagi.");
  };

  xhr.send(
    "nomorKantong=" +
      encodeURIComponent(nomorKantong) +
      "&alatPemutaran=" +
      encodeURIComponent(alatPemutaran) +
      "&alatPemisahan=" +
      encodeURIComponent(alatPemisahan) +
      "&jamMulaiPutar=" +
      encodeURIComponent(jMPutar) +
      "&jamSelesaiPutar=" +
      encodeURIComponent(jSPutar) +
      "&jamMulaiPisah=" +
      encodeURIComponent(jMPisah) +
      "&jamSelesaiPisah=" +
      encodeURIComponent(jSPisah) +
      "&shift=" +
      encodeURIComponent(shift)
  ); // Pastikan untuk encode parameter
}

function showModal(message) {
  // Implementasikan modal bootstrap atau buat modal custom sesuai kebutuhan
  $("#errorModal .modal-body").html(message);
  $("#errorModal").modal("show");
}
