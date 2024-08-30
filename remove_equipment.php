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
$equipment_id = isset($_POST['equipment_id']) ? (int)$_POST['equipment_id'] : 0; 

// طباعة القيم للتصحيح
error_log("Device ID: " . $device_id);
error_log("Equipment ID: " . $equipment_id);

// التحقق من صحة المدخلات
if ($device_id > 0 && $equipment_id > 0) {
    // إعداد الاستعلام
    $sql = "UPDATE `equipment`
            SET `device_id` = NULL
            WHERE `id_eq` = ? AND `device_id` = ?";

    // تحضير الاستعلام
    if ($stmt = $conn->prepare($sql)) {
        // ربط المعلمات
        $stmt->bind_param('ii', $equipment_id, $device_id);

        // تنفيذ الاستعلام
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Equipment removed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove equipment']);
        }

        // إغلاق الاستعلام
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}

// إغلاق الاتصال
$conn->close();
?>
