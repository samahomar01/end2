<?php
// get_lab_devices.php
header("Content-Type: application/json");
require 'db_connection.php';

$lab_id = $_GET['lab_id'];

$sql = "SELECT * FROM lab_devices WHERE lab_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lab_id);
$stmt->execute();
$result = $stmt->get_result();

$devices = array();
while($row = $result->fetch_assoc()) {
    $devices[] = $row;
}

echo json_encode($devices);
?>
