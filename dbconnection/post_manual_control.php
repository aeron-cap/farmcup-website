<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("config.php");

$data = json_decode(file_get_contents("php://input"), true);

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("UPDATE manual_control SET relay1_duration = ?, relay2_duration = ?, relay3_duration = ?, relay4_duration = ?, relay5_duration = ?, relay6_duration = ?, relay7_duration = ?, relay8_duration = ? WHERE id = ?");
$stmt->bind_param("iiiiiiiii", $relay1_duration, $relay2_duration, $relay3_duration, $relay4_duration, $relay5_duration, $relay6_duration, $relay7_duration, $relay8_duration, $id);


$relay1_duration = isset($data['relay1_duration']) ? $data['relay1_duration'] : null;
$relay2_duration = isset($data['relay2_duration']) ? $data['relay2_duration'] : null;
$relay3_duration = isset($data['relay3_duration']) ? $data['relay3_duration'] : null;
$relay4_duration = isset($data['relay4_duration']) ? $data['relay4_duration'] : null;
$relay5_duration = isset($data['relay5_duration']) ? $data['relay5_duration'] : null;
$relay6_duration = isset($data['relay6_duration']) ? $data['relay6_duration'] : null;
$relay7_duration = isset($data['relay7_duration']) ? $data['relay7_duration'] : null;
$relay8_duration = isset($data['relay8_duration']) ? $data['relay8_duration'] : null;
$id = 1; 


if ($stmt->execute()) {
    echo "Data updated successfully";
} else {
    echo "Error updating data: " . $conn->error;
}


$stmt->close();
$conn->close();
?>
