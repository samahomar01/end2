<?php
// update_equipment_status.php
header("Content-Type: application/json");
require 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$lab_id = $data['lab_id'];
$equipment_ids = $data['equipment_ids']; // Array of equipment IDs

foreach ($equipment_ids as $equipment_id) {
    $sql = "UPDATE equipment SET is_available = FALSE WHERE id = ? AND lab_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $equipment_id, $lab_id);
    $stmt->execute();
}

$response = array("status" => "success", "message" => "Equipment status updated successfully.");
echo json_encode($response);
?>
