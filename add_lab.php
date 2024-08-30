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

// التحقق من أن طلب POST يحتوي على بيانات
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['labName']) || empty($data['physicalLocation'])) {
    echo json_encode(['error' => 'Lab Name and Physical Location are required']);
    $conn->close();
    exit();
}

$labName = $data['labName'];
$physicalLocation = $data['physicalLocation'];
$publicAccess = isset($data['publicAccess']) ? $data['publicAccess'] : 0;

// استعلام الإدخال
$sql = "INSERT INTO location (name, physicalLocation, publicAccess) VALUES ('$labName', '$physicalLocation', '$publicAccess')";

// تنفيذ الاستعلام والتحقق من النجاح
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error: ' . $conn->error]);
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>
