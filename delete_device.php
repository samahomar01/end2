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

// التحقق من صحة المدخلات
if ($device_id > 0) {
    try {
        // تحديث السجلات المرتبطة في جدول tickets
        $sql = "UPDATE tickets SET device_id = NULL WHERE device_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $device_id);
        $stmt->execute();
        $stmt->close();

        // تحديث السجلات المرتبطة في جدول notifications
        $sql = "UPDATE notifications SET device_id = NULL WHERE device_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $device_id);
        $stmt->execute();
        $stmt->close();

        // حذف الجهاز من جدول devices
        $sql = "DELETE FROM devices WHERE device_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $device_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Device deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete device']);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid device ID']);
}

// إغلاق الاتصال
$conn->close();
?>
