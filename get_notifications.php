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

// تعيين معرف المستخدم مباشرة لاختبار العرض
$user_id = 1;

// الاستعلام لجلب جميع الإشعارات للمستخدم المحدد، بترتيب تنازلي حسب تاريخ الإنشاء
$sql = "SELECT `id`, `ticket_id`, `lab_id`, `device_id`, `message`, `created_at`, `is_read`, `user_id` 
        FROM `notifications` 
        WHERE `user_id` = ? 
        ORDER BY `created_at` DESC"; // ترتيب تنازلي حسب تاريخ الإنشاء
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// جمع الإشعارات في مصفوفة
$notifications = array();
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// إرسال الاستجابة
$response = array("success" => true, "notifications" => $notifications);
echo json_encode($response);

// إغلاق الاتصال
$stmt->close();
$conn->close();
?>
