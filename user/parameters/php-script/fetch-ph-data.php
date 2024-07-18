<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../../dbconnection/config.php');
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// ---------------------------------- SQL Request for LATEST Data ----------------------------------------------
$sqlLatest = "SELECT pH, time_stamp FROM sensordata ORDER BY time_stamp DESC LIMIT 1;";
$resultLatest = $conn->query($sqlLatest);
while ($row = $resultLatest->fetch_assoc()) {
    $pHLatest = $row["pH"];
    $timestampLatest = $row["time_stamp"];

    $timestampLatestManila = new DateTime($timestampLatest, new DateTimeZone('UTC'));
    $timestampLatestManila->setTimezone(new DateTimeZone('Asia/Manila'));
    $formattedTimestamp = $timestampLatestManila->format("F d, Y | g:i A");
}

// -------------------- SQL Request for ALL pH Level DATA (need sa graph at table) -----------------------------
$sql = "SELECT time_stamp, pH FROM sensordata ORDER BY time_stamp";
$result = $conn->query($sql);

$dateLabels = array();
$timeLabels = array();
$pHData = array();


while ($row = $result->fetch_assoc()) {
    $timestampManila = new DateTime($row["time_stamp"], new DateTimeZone('UTC'));
    $timestampManila->setTimezone(new DateTimeZone('Asia/Manila'));

    $date = $timestampManila->format("F d, Y");
    $time = $timestampManila->format("g:i A");

    $dateLabels[] = $date;
    $timeLabels[] = $time;
    $pHData[] = $row["pH"];
}

// -------------------- SQL Request for TOGGLE STATUS -----------------------------
$sqlToggleStatus = "SELECT toggle FROM toggle_switch";
$resultToggleStatus = $conn->query($sqlToggleStatus);
if ($resultToggleStatus->num_rows > 0) {
    while ($row = $resultToggleStatus->fetch_assoc()) {
        $toggleStatus = $row["toggle"];
    }
} else {
    $toggleStatus = 0;
}

$conn->close();
