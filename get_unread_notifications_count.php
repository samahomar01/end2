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

if (isset($data['user_id'])) {
    $user_id = $data['user_id'];

    $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = array();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['count'] = $row['count'];
    } else {
        $response['success'] = false;
        $response['message'] = 'No unread notifications found';
    }
} else {
    $response = array("success" => false, "message" => "user_id not provided");
}

echo json_encode($response);
$conn->close();
?>