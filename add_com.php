<?php
// add_device.php
header("Content-Type: application/json");
require 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$lab_id = $data['lab_id'];
$device_name = $data['device_name'];
$components = implode(',', $data['components']);

$sql = "INSERT INTO lab_devices (lab_id, device_name, components) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $lab_id, $device_name, $components);

if ($stmt->execute()) {
    $response = array("status" => "success", "message" => "Device added successfully.");
} else {
    $response = array("status" => "error", "message" => "Failed to add device.");
}

echo json_encode($response);
?>
