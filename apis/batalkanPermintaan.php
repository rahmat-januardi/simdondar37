<?php
session_start();
require_once("config/dbi_connect.php");

function logMessage($message)
{
    $logFile = 'logs/reject_handler.log';
    if (!is_dir('logs')) {
        mkdir('logs', 0777, true);
    }
    $fp = fopen($logFile, 'a');
    if ($fp) {
        $timestamp = date('Y-m-d H:i:s');
        fwrite($fp, "[$timestamp] $message\n");
        fclose($fp);
    }
}

$d = date('d');
$m = date('m');
$y = date('Y');
$authToken = 'bdrs.or.id' . $d . $m . $y;
logMessage("Authorization token: Bearer " . $authToken);

$data = json_decode(file_get_contents('php://input'), true);
$nt = isset($data['id']) ? $data['id'] : null;

if (!$nt || !preg_match('/^NA\d{6}-\d{4}-\d{3}$/', $nt)) {
    logMessage("Error: Invalid NT format received.");
    echo json_encode(array('success' => false, 'message' => 'Invalid NT format.'));
    exit;
}

logMessage("NT received: $nt");

mysqli_autocommit($dbi, false); // Nonaktifkan autocommit

try {
    $query = "UPDATE dpermintaan_darah SET `status` = 2 WHERE noTrans = ?";
    $stmt = mysqli_prepare($dbi, $query);
    mysqli_stmt_bind_param($stmt, "s", $nt);
    if (!mysqli_stmt_execute($stmt)) {
    logMessage("GAGAL Local database updated for NT: $nt");
        throw new Exception('Failed to update local database: ' . mysqli_stmt_error($stmt));
    }
    logMessage("Local database updated for NT: $nt");

    //$apiUrl = 'https://bdrs.jblood.or.id/apiBatalMinta.php?id=' . $nt;
    $apiUrl = 'https://bdrs.or.id/apiBatalMinta.php';
    $payload = json_encode(array('id' => $nt, 'status' => 'rejected'));
    logMessage("API URL adalah: $apiUrl");
    logMessage("Sending payload to API: $payload");

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload),
        'Authorization: Bearer ' . $authToken
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$apiResponse) {
        logMessage("Unexpected HTTP code: $httpCode. Response: $apiResponse");
        throw new Exception('Failed to update server: Unexpected HTTP response');
    }

    logMessage("API response: $apiResponse");

    $apiResult = json_decode($apiResponse, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        logMessage("Invalid JSON response: $apiResponse");
        throw new Exception('Failed to parse server response.');
    }

    if (!$apiResult['success']) {
        throw new Exception('Server rejected the update: ' . $apiResult['message']);
    }

    mysqli_commit($dbi);
    logMessage("Transaction committed successfully.");
    echo json_encode(array('success' => true, 'message' => 'Data successfully rejected'));
} catch (Exception $e) {
    mysqli_rollback($dbi);
    logMessage("Error occurred: " . $e->getMessage());
    echo json_encode(array('success' => false, 'message' => $e->getMessage()));
}
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_autocommit($dbi, true);
if (mysqli_commit($dbi)) {
    logMessage("Transaction committed successfully.");
} else {
    logMessage("Transaction commit failed.");
}

    mysqli_close($dbi);

