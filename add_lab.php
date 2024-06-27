<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_techcare";

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
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
