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

// تسجيل البيانات المستلمة للتأكد من صحتها
file_put_contents('debug_log.txt', print_r($data, true) . "\n", FILE_APPEND);

if (!isset($data['lab_id']) || !isset($data['device_name'])) {
    $error_message = 'Missing data';
    file_put_contents('debug_log.txt', "Error: $error_message\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
    exit;
}

$lab_id = $data['lab_id'];
$device_name = $data['device_name'];

// إدخال الجهاز الجديد في قاعدة البيانات
$stmt = $conn->prepare("INSERT INTO `devices` (`name`, `lab_id`, `status`) VALUES (?, ?, 'active')");
if ($stmt === false) {
    file_put_contents('debug_log.txt', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement']);
    exit;
}
$stmt->bind_param('si', $device_name, $lab_id);

if ($stmt->execute()) {
    $device_id = $stmt->insert_id; // الحصول على معرف الجهاز الجديد
    echo json_encode(['status' => 'success', 'device_id' => $device_id]);

    // استرداد معرفات الفنيين
    $technician_role = 'technician';
    $technicians = $conn->query("SELECT `id` FROM `users` WHERE `role` = '$technician_role'");

    if ($technicians->num_rows > 0) {
        while ($technician = $technicians->fetch_assoc()) {
            $technician_id = $technician['id'];
            $message_technician = "A new device has been installed in lab number $lab_id";

            // إدخال الإشعار لكل فني
            $notification_sql = "INSERT INTO notifications (ticket_id, message, user_id, lab_id, device_id, created_at, is_read) 
                                 VALUES (NULL, '$message_technician', $technician_id, $lab_id, $device_id, NOW(), 0)";
            
            if ($conn->query($notification_sql) === false) {
                file_put_contents('debug_log.txt', "Error inserting notification for technician ID $technician_id: " . $conn->error . "\n", FILE_APPEND);
            }
        }
    } else {
        file_put_contents('debug_log.txt', "No technicians found\n", FILE_APPEND);
    }

} else {
    $error_message = 'Failed to create device';
    file_put_contents('debug_log.txt', "Error: " . $stmt->error . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}

$stmt->close();
$conn->close();
?>
