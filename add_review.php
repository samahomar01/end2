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

if (!$data) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

$user_id = $data['user_id'] ?? '';
$comment = $data['comment'] ?? '';

if (empty($user_id) || empty($comment)) {
    die(json_encode(['success' => false, 'message' => 'User ID and Comment are required']));
}

$sql = "INSERT INTO reviews (user_id, comment) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $comment);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Review added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add review: ' . $stmt->error]);
}

$conn->close();
?>
