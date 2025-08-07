function getStatusKGD(result) {
    if (!result) return { class: "bg-secondary", style: "" };
    const val = result.toLowerCase();
    if (val === "cocok") {
        return { class: "", style: "background-color: #27548A;" };
    } else if (val === "tdk cocok" || val === "tidak cocok") {
        return { class: "bg-warning", style: "" };
    }
    return { class: "bg-secondary", style: "" };
}


function getStatusABS(result) {
    if (!result) return "bg-secondary";
    const val = result.toLowerCase();
    if (["neg", "inherent", "inheret"].includes(val)) {
        return "bg-normal";
    } else if (["tdk diketahui", "pos", "positif"].includes(val)) {
        return "bg-warning";
    }
    return "bg-secondary";
}

function getStatus(kode) {
    const statusKantong = {
        0: "Kosong/di Logistik",
        1: "Baru isi/Karantina",
        2: "Sehat",
        3: "Keluar",
        4: "Kosong/di Logistik",
        5: "Kosong/di Logistik",
        6: "Musnah",
        7: "Polycythemia",
        8: "Lisis",
        9: "Lipek",
        10: "DCT",
        11: "Rusak Duplo",
        12: "Rusak Konfirmasi",
        13: "Kantong Bocor",
        14: "Selit Rusak",
        15: "Bekas Pemb. WE",
        16: "Reaktif Rujuk ke UTD",
        17: "Hematokrit Tinggi",
        18: "Limbah Sisap RC",
        19: "Leukosit Tinggi",
        20: "Produksi Rusak",
        21: "Produksi Sample QC",
        22: "Klot Gumpalan",
        23: "Waktu Olah",
        24: "Volume Kurang",
        25: "Kantong Pecah",
        26: "Tdk Dikenali",
        27: "Tdk Diketahui",
    };

    return statusKantong[kode] || "-";
}

function getStatusClass(kode) {
    // kode = parseInt(kode);
    kode = parseInt(kode, 10);

    if (
        [
        4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23,
        24, 25, 26, 27,
        ].includes(kode)
    ) {
        return "blink-blink-mas-theo";
    }

    switch (kode) {
        case 0:
            return "background-color: gray; color: white;";
        case 1:
            return "background-color: #FFFBDE; color: black;";
        case 2:
            return "background-color: #27548A; color: white;";
        case 3:
            return "background-color: #E55050; color: white;";
        default:
            return "background-color: #6c757d; color: white;";
    }

}
