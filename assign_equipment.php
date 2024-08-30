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

file_put_contents('debug_log.txt', print_r($data, true) . "\n", FILE_APPEND);

if (!isset($data['device_id']) || !isset($data['equipment_ids'])) {
    $error_message = 'Missing data';
    file_put_contents('debug_log.txt', "Error: $error_message\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
    exit;
}

$device_id = $data['device_id'];
$equipment_ids = $data['equipment_ids']; // This should be an array of equipment IDs

if (!is_array($equipment_ids)) {
    $error_message = 'Invalid equipment_ids format';
    file_put_contents('debug_log.txt', "Error: $error_message\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
    exit;
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("UPDATE `equipment` SET `device_id` = ? WHERE `id_eq` = ?");
    if ($stmt === false) {
        file_put_contents('debug_log.txt', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
        throw new Exception('Failed to prepare statement');
    }

    foreach ($equipment_ids as $equipment_id) {
        $stmt->bind_param('ii', $device_id, $equipment_id);

        if (!$stmt->execute()) {
            throw new Exception('Failed to assign equipment');
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    $conn->rollback();
    file_put_contents('debug_log.txt', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
