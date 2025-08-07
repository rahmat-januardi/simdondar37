<?php
require_once "dbi_connect.php";


$checkColumn = $dbi->query("SHOW COLUMNS FROM `dpengolahan` LIKE 'aPutar'");
if ($checkColumn->num_rows == 0) {
    // Jika kolom aPutar belum ada, jalankan ALTER TABLE
    $alterTable = "ALTER TABLE `dpengolahan` CHANGE `cara` `aPutar` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL";
    if ($dbi->query($alterTable) === TRUE) {
        // echo "Table altered successfully.";
    } else {
        // echo "Error altering table: " . $dbi->error;
    }
}
$checkColumn = $dbi->query("SHOW COLUMNS FROM `dpengolahan` LIKE 'aPisah'");
if ($checkColumn->num_rows == 0) {
    $alterTable1 = "ALTER TABLE `dpengolahan` ADD `aPisah` VARCHAR(100) NULL DEFAULT NULL AFTER `aPutar`;";
    $dbi->query($alterTable1);
}

/** versi 5.4 ++
$alterStatements = [
    'mulaiPutar' => "ALTER TABLE `dpengolahan` ADD `mulaiPutar` TIME NULL DEFAULT NULL AFTER `shift`;",
    'selesaiPutar' => "ALTER TABLE `dpengolahan` ADD `selesaiPutar` TIME NULL DEFAULT NULL AFTER `mulaiPutar`;",
    'mulaiPisah' => "ALTER TABLE `dpengolahan` ADD `mulaiPisah` TIME NULL DEFAULT NULL AFTER `selesaiPutar`;",
    'selesaiPisah' => "ALTER TABLE `dpengolahan` ADD `selesaiPisah` TIME NULL DEFAULT NULL AFTER `mulaiPisah`;",
];
*/

$alterStatements = array(
    'mulaiPutar' => "ALTER TABLE `dpengolahan` ADD `mulaiPutar` TIME NULL DEFAULT NULL AFTER `shift`;",
    'selesaiPutar' => "ALTER TABLE `dpengolahan` ADD `selesaiPutar` TIME NULL DEFAULT NULL AFTER `mulaiPutar`;",
    'mulaiPisah' => "ALTER TABLE `dpengolahan` ADD `mulaiPisah` TIME NULL DEFAULT NULL AFTER `selesaiPutar`;",
    'selesaiPisah' => "ALTER TABLE `dpengolahan` ADD `selesaiPisah` TIME NULL DEFAULT NULL AFTER `mulaiPisah`;",
);

foreach ($alterStatements as $column => $statement) {
    // Periksa apakah kolom sudah ada
    $checkColumn = $dbi->query("SHOW COLUMNS FROM `dpengolahan` LIKE '$column'");
    if ($checkColumn->num_rows == 0) {
        // Jika kolom belum ada, jalankan perintah ALTER TABLE
        if ($dbi->query($statement) !== TRUE) {
            // echo "Error menambahkan kolom $column: " . $dbi->error;
            // error_log("Error menambahkan kolom $column: " . $dbi->error);
        }
    }
}

$alterTable3 = "ALTER TABLE  `user_log` ADD  `unixID` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `aksi_user` ;
ALTER TABLE `user_log` ADD INDEX(`unixID`);";
$alterTable4 = "ALTER TABLE  `dpengolahan` CHANGE  `id`  `id1` INT( 11 ) NOT NULL ;";
$alterTable5 = "ALTER TABLE  `dpengolahan` ADD  `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;";
$alterTable6 = "ALTER TABLE  `dpengolahan` ADD  `tglupdate` DATETIME NULL DEFAULT NULL AFTER  `tgl` ;";
$alterTable7 = "ALTER TABLE `dpengolahan`  ADD `noseri` VARCHAR(50) NULL DEFAULT NULL AFTER `metode`;";
$alterTable8 = "ALTER TABLE `dpengolahan` CHANGE `selesai` `selesai` TIME NULL DEFAULT NULL;";
$alterTable9 = "ALTER TABLE `dpengolahan`  ADD `verifikator` VARCHAR(50) NULL DEFAULT NULL,  ADD `musnah` INT(1) NOT NULL COMMENT '1=Iya, 0=Tidak';";

if ($dbi->query($alterTable3) === TRUE && $dbi->query($alterTable4) === TRUE && $dbi->query($alterTable5) === TRUE && $dbi->query($alterTable6) === TRUE && $dbi->query($alterTable7) === TRUE && $dbi->query($alterTable8) === TRUE && $dbi->query($alterTable9) === TRUE) {
    // echo "Kolom noTrans berhasil diubah dan indeks ditambahkan.";
    // error_log("Kolom noTrans berhasil diubah dan indeks ditambahkan.");
} else {
    // echo "Error mengubah kolom noTrans atau menambahkan indeks: " . $dbi->error;
    // error_log("Error mengubah kolom noTrans atau menambahkan indeks: " . $dbi->error);
}


// TABEL MASTER KANTONG CHECKER
// prepare tabel sudah memiliki kolom yang diperlukan untuk produk kantong
$alterQueries = array(
    "ALTER TABLE `master_kantong` CHANGE `ket` `ket` VARCHAR(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;",
    "ALTER TABLE `master_kantong` ADD `pr_utama` VARCHAR(255) NULL DEFAULT NULL AFTER `berat_ku`;",
    "ALTER TABLE `master_kantong` ADD `pr_s1` VARCHAR(255) NULL DEFAULT NULL AFTER `berat_s1`;",
    "ALTER TABLE `master_kantong` ADD `pr_s2` VARCHAR(255) NULL DEFAULT NULL AFTER `berat_s2`;",
    "ALTER TABLE `master_kantong` ADD `pr_s3` VARCHAR(255) NULL DEFAULT NULL AFTER `berat_s3`;",
    "ALTER TABLE `master_kantong` ADD `pr_s4` VARCHAR(255) NULL DEFAULT NULL AFTER `berat_s4`;",
    "ALTER TABLE `master_kantong` ADD `pr_s5` VARCHAR(255) NULL DEFAULT NULL AFTER `berat_s5`;",
    "ALTER TABLE `master_kantong` ADD `pr_s6` VARCHAR(255) NULL DEFAULT NULL AFTER `berat_s6`;",
    "ALTER TABLE `master_kantong` ADD `pr_s7` VARCHAR(255) NULL DEFAULT NULL AFTER `berat_s7`;",
    "ALTER TABLE  `produk` ADD  `stats` INT( 1 ) NULL DEFAULT NULL COMMENT  '0:Aktif, 1:NonAktif';",
    "ALTER TABLE  `hasilelisa` ADD INDEX (  `noKantong` ) ;",
    "ALTER TABLE  `hasilelisa` ADD INDEX (  `Hasil` ) ;",
    "ALTER TABLE  `hasilelisa` ADD INDEX (  `jenisPeriksa` ) ;",
    "ALTER TABLE  `hasilelisa` ADD INDEX (  `tglPeriksa` ) ;",
    "ALTER TABLE  `hasilelisa` ADD INDEX (  `notrans` ) ;",
    "ALTER TABLE  `hasilelisa` ADD INDEX (  `noLot` ) ;",
    "ALTER TABLE  `hasilelisa` ADD INDEX (  `Metode` ) ;",
    "ALTER TABLE  `hasilelisa` ADD INDEX (  `jenisPeriksa` ) ;",
    "ALTER TABLE  `hasilnat` ADD INDEX (  `idsample` ) ;",
    "ALTER TABLE  `hasilnat` ADD INDEX (  `noKantong` ) ;",
    "ALTER TABLE  `hasilnat` ADD INDEX (  `Hasil` ) ;"
);

foreach ($alterQueries as $query) {
    mysqli_query($dbi, $query);
}

$columnsToCheck = array(
    'dpengolahan' => array(
        'NoTrans' => "VARCHAR(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL",
        'selesai' => "TIME NULL DEFAULT NULL"
    ),
    'dpengolahan_temp' => array(
        'musnah' => "INT(1) NULL DEFAULT NULL COMMENT '1=Iya, 0=Tidak'"
    )
);

foreach ($columnsToCheck as $tableName => $columns) {
    foreach ($columns as $columnName => $expectedDefinition) {
        $columnExists = false;
        $result = $dbi->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'");
        if ($result && $result->num_rows > 0) {
            $column = $result->fetch_assoc();
            if (strpos($column['Type'], strtok($expectedDefinition, ' ')) !== false) {
                $columnExists = true;
            }
        }

        if (!$columnExists) {
            $query = "ALTER TABLE `$tableName` CHANGE `$columnName` `$columnName` $expectedDefinition;";
            mysqli_query($dbi, $query);
        }
    }
}

// TABEL KANTONG MERK CHECKER
$tableName = "kantong_merk";

// Cek apakah tabel ada
$tableExists = false;
$result = $dbi->query("SHOW TABLES LIKE '$tableName'");
if ($result && $result->num_rows > 0) {
    $tableExists = true;
}

if (!$tableExists) {
    // Jika tabel belum ada, buat tabel dengan struktur yang diminta
    $createQuery = "
        CREATE TABLE `$tableName` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `merk` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
            `pemasok` VARCHAR(125) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
            `aktif` INT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        )
    ";
    if ($dbi->query($createQuery) === TRUE) {
        // echo "Tabel '$tableName' berhasil dibuat.<br>";
    } else {
        echo "Gagal membuat tabel: " . $dbi->error . "<br>";
    }
} else {
    // Jika tabel sudah ada, cek struktur kolomnya
    $columns = [];
    $res = $dbi->query("SHOW COLUMNS FROM `$tableName`");
    while ($row = $res->fetch_assoc()) {
        $columns[$row['Field']] = $row;
    }

    $alterQueries = [];

    // Cek dan buat query ALTER jika perlu
    if (!isset($columns['id']) || strpos($columns['id']['Type'], 'int') === false || $columns['id']['Extra'] != 'auto_increment') {
        $alterQueries[] = "MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
    }

    if (!isset($columns['merk'])) {
        $alterQueries[] = "ADD COLUMN `merk` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `id`";
    } elseif (strpos($columns['merk']['Type'], 'varchar(20)') === false) {
        $alterQueries[] = "MODIFY COLUMN `merk` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `id`";
    }

    if (!isset($columns['pemasok'])) {
        $alterQueries[] = "ADD COLUMN `pemasok` VARCHAR(125) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL AFTER `merk`";
    } elseif (strpos($columns['pemasok']['Type'], 'varchar(125)') === false) {
        $alterQueries[] = "MODIFY COLUMN `pemasok` VARCHAR(125) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL AFTER `merk`";
    }

    if (!isset($columns['aktif'])) {
        $alterQueries[] = "ADD COLUMN `aktif` INT(1) NOT NULL DEFAULT 0 COMMENT '0=Aktif, 1=Nonaktif' AFTER `pemasok`";
    } elseif (strpos($columns['aktif']['Type'], 'int(1)') === false) {
        $alterQueries[] = "MODIFY COLUMN `aktif` INT(1) NOT NULL DEFAULT 0 COMMENT '0=Aktif, 1=Nonaktif' AFTER `pemasok`";
    }


    // Jalankan ALTER jika ada perubahan
    if (!empty($alterQueries)) {
        $alterSQL = "ALTER TABLE `$tableName` " . implode(", ", $alterQueries);
        if ($dbi->query($alterSQL) === TRUE) {
            // echo "Struktur tabel '$tableName' telah diperbarui.<br>";
        } else {
            echo "Gagal memperbarui struktur tabel: " . $dbi->error . "<br>";
        }
    } else {
        // echo "Struktur tabel '$tableName' sudah sesuai.<br>";
    }
}

// $dbi->close();
?>