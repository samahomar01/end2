<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_techcare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

$user_id = $data['user_id'] ?? '';
$state = $data['state'] ?? 'open'; // تعيين الحالة الافتراضية للتذكرة
$date = date('Y-m-d H:i:s'); // تعيين التاريخ الحالي
$issue = $data['issue'] ?? '';

if (empty($user_id) || empty($issue)) {
    die(json_encode(['success' => false, 'message' => 'User ID and Issue are required']));
}

$sql = "INSERT INTO tickets (user_id, state, date, issue) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $user_id, $state, $date, $issue);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Issue reported successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Report failed: ' . $stmt->error]);
}

$conn->close();
?>
