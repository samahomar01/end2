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

// الحصول على قيم POST والتحقق منها
$device_id = isset($_POST['device_id']) ? (int)$_POST['device_id'] : 0;
$new_lab_id = isset($_POST['lab_id']) ? (int)$_POST['lab_id'] : 0;

// التحقق من صحة المعرفات
if ($device_id > 0 && $new_lab_id > 0) {
    // التحقق من وجود الجهاز
    $sql = "SELECT * FROM devices WHERE device_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $device_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // التحقق من وجود المعمل
        $sql = "SELECT * FROM location WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $new_lab_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // تحديث المعمل للجهاز
            $sql = "UPDATE devices SET lab_id = ? WHERE device_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $new_lab_id, $device_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Device updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update device']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid lab ID']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid device ID']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid device ID or lab ID']);
}

$conn->close();
?>
