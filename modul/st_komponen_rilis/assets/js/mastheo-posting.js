// kalau suskes
function pesanSukses(message = "Berhasil!") {
  Swal.fire({
    icon: "success",
    title: "<strong>Berhasil!</strong><br><br>",
    html: '<p style="font-size:16px;">' + message + "</p>",
    confirmButtonColor: " #28a745",
    confirmButtonText: "OK",
    width: "650px",
    padding: "1.5em",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.reload(); // Reload page after user closes the success message
    }
  });
}

// error
function pesanError(message = "Terjadi kesalahan.") {
  Swal.fire({
    icon: "error",
    title: "<strong>Gagal!</strong><br><br>",
    // html: '<p style="font-size:16px; color: #d9534f;">' + message + "</p>",
    html:
      '<p style="font-size:16px; color:rgb(29, 29, 29);">' + message + "</p>",
    confirmButtonColor: " #dc3545",
    confirmButtonText: "Tutup",
    width: "550px",
    padding: "1.5em",
  });
}

// warning
function pesanWarning(message = "Perhatian!") {
  Swal.fire({
    icon: "warning",
    title: "<strong>Warning!</strong><br><br>",
    html: '<p style="font-size:16px;">' + message + "</p>",
    confirmButtonColor: " #ffc107",
    confirmButtonText: "Mengerti",
    width: "450px",
    padding: "1.5em",
  });
}

// info saja
function pesanInfo(message = "Informasi.") {
  Swal.fire({
    icon: "info",
    title: "<strong>Info</strong><br><br>",
    html: '<p style="font-size:16px;">' + message + "</p>",
    confirmButtonColor: " #17a2b8",
    confirmButtonText: "Oke",
    width: "450px",
    padding: "1.5em",
  });
}

// konfirmasi pake tombol "Ya" dan "Tidak"
function pesanKonfirmasi(message = "Apakah Anda yakin?") {
  return Swal.fire({
    icon: "question",
    title: "<strong>Konfirmasi</strong><br><br>",
    html: '<p style="font-size:16px;">' + message + "</p>",
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonColor: " #28a745", // Tombol "Ya" hijau
    cancelButtonColor: " #dc3545", // Tombol "Tidak" merah
    confirmButtonText: "Ya",
    cancelButtonText: "Tidak",
    width: "450px",
    padding: "1.5em",
  });
}
