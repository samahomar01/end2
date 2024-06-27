<?php
// get_available_equipment.php
header("Content-Type: application/json");
require 'db_connection.php';

$lab_id = $_GET['lab_id'];

$sql = "SELECT * FROM equipment WHERE lab_id = ? AND is_available = TRUE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lab_id);
$stmt->execute();
$result = $stmt->get_result();

$equipment = array();
while($row = $result->fetch_assoc()) {
    $equipment[] = $row;
}

echo json_encode($equipment);
?>
