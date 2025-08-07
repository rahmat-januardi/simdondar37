<?php
function anuu($stmt, $label = 'Query')
{
    // Cek jika object tidak valid
    if (!$stmt) {
        $dbi = $GLOBALS['dbi'];
        die("[$label] Error: (" . mysqli_errno($dbi) . ") " . mysqli_error($dbi));
    }

    // Kalau object valid, tapi ada error dalam eksekusi (misal execute)
    if (method_exists($stmt, 'errno') && $stmt->errno) {
        die("[$label] Error: (" . $stmt->errno . ") " . $stmt->error);
    }
}
