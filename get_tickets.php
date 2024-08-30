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

// استقبال البيانات المرسلة عبر الطلب (POST)
$data = json_decode(file_get_contents('php://input'), true);

// التحقق من صحة البيانات المستلمة
if (!$data || !isset($data['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

// استخراج معرف المستخدم من البيانات
$user_id = $data['user_id'];

// استعلام لاسترجاع بيانات التذاكر
$sql = "SELECT ticket_id, state AS status, date, issue FROM tickets WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// إعداد مصفوفة لتخزين البيانات المسترجعة
$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}

// إغلاق اتصال قاعدة البيانات
$conn->close();

// إرجاع البيانات كاستجابة JSON
echo json_encode(['success' => true, 'tickets' => $tickets]);
?>
