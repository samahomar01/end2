<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// التعامل مع طلبات OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No content
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "samahh";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

$response = [];

// التحقق من الاتصال
if ($conn->connect_error) {
    $response['success'] = false;
    $response['message'] = 'Database connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}
$data = json_decode(file_get_contents('php://input'), true);
error_log(print_r($data, true)); // Log the received data

$deviceId = isset($data['device_id']) ? intval($data['device_id']) : null;
$status = isset($data['status']) ? $data['status'] : null;

error_log("Received Device ID: " . $deviceId); // Log received device_id
error_log("Received Status: " . $status); // Log received status

if (!empty($deviceId) && !empty($status)) {
    $sql = "UPDATE devices SET status = ? WHERE device_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Prepare failed']);
        exit();
    }
    $stmt->bind_param("si", $status, $deviceId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Device status updated successfully']);
        error_log('Device status updated successfully');
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update device status']);
        error_log('Failed to update device status');
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    error_log("Invalid input: " . json_encode($data));
}

$conn->close();
?>
