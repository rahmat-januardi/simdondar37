<?php
// koneksi mas theo x bu guru
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Pengambilan Darah</title>
</head>
<body>
    <!-- <form onsubmit="return diff();"> -->
    <form>
        <label for="jam_mulai">Jam Mulai Pengambilan:</label>
        <input type="text" id="jam_mulai" name="jam_mulai" onblur="jammulai(this.value)" placeholder="HH:MM" required>
        <br><br>
        <label for="jam_selesai">Jam Selesai Pengambilan:</label>
        <input type="text" id="jam_selesai" name="jam_selesai" onblur="jamselesai(this.value)" placeholder="HH:MM" required>
        <br><br>
        <!-- <button type="submit">Submit</button> -->
        <input type="submit" name="submit" value="Simpan" onclick="return diff()">
    </form>
</body>
</html>

<script type="text/javascript">
function jammulai(val){
        var jm = val.length;
        var jm0 = document.getElementById("jam_mulai").value;
                if (jm < 5)
                        { alert('Jam MULAI PENGAMBILAN harus diisi dengan Benar ..');return false; }
}
                
function jamselesai(val){
        var js = val.length;
        var js0 = document.getElementById("jam_selesai").value;
                if (js < 5)
                        { alert('Jam SELESAI PENGAMBILAN harus diisi dengan Benar ..');return false; }
}

function diff() {
var start = document.getElementById("jam_mulai").value;
var end = document.getElementById("jam_selesai").value;

    start = start.split(":");
    end = end.split(":");
    var startDate = new Date(0, 0, 0, start[0], start[1], 0);
    var endDate = new Date(0, 0, 0, end[0], end[1], 0);
    var diff = endDate.getTime() - startDate.getTime();
    var hours = Math.floor(diff / 1000 / 60 / 60);
    /* diff -= hours * 1000 * 60 * 60 */;
    var minutes = Math.floor(diff / 1000 / 60);

    if(minutes <= 0){
    
//    alert ("Durasi Pengambilan Darah Donor " + minutes + " Menit, Apakah Anda Yakin ?");
    return confirm("Durasi Pengambilan Darah Donor " + minutes + "</b> Menit, Apakah Anda Yakin ?");
    
//    return (hours < 9 ? "0" : "") + hours + ":" + (minutes < 9 ? "0" : "") + minutes;
    }
}

//document.getElementById("diff").value = diff(start, end);
</script>
