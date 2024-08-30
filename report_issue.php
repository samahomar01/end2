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
$state = $data['state'] ?? 'open'; // Default state for the ticket
$date = date('Y-m-d H:i:s'); // Current date and time
$issue = $data['issue'] ?? '';
$lab_id = $data['lab_id'] ?? null;
$device_id = $data['device_id'] ?? null;

if (empty($user_id) || empty($issue)) {
    die(json_encode(['success' => false, 'message' => 'User ID and Issue are required']));
}

// Get the name of the user who created the ticket
$user_name = '';
$user_sql = "SELECT name FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if ($user_result->num_rows > 0) {
    $user_row = $user_result->fetch_assoc();
    $user_name = $user_row['name'];
}

// Insert the new ticket
$sql = "INSERT INTO tickets (user_id, state, date, issue, lab_id, device_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $user_id, $state, $date, $issue, $lab_id, $device_id);

if ($stmt->execute()) {
    // Get the ID of the newly inserted ticket
    $ticket_id = $stmt->insert_id;

 


    // Insert the ticket status in the report_status table
    $status_sql = "INSERT INTO report_status (report_id, status_date, status) VALUES (?, ?, ?)";
    $status_stmt = $conn->prepare($status_sql);
    $status_stmt->bind_param("iss", $ticket_id, $date, $state);
    $status_stmt->execute();

    // Insert the new record into the ticket_history table
    $history_sql = "INSERT INTO ticket_history (ticket_id, user_id, status, status_date) VALUES (?, ?, ?, ?)";
    $history_stmt = $conn->prepare($history_sql);
    $history_stmt->bind_param("iiss", $ticket_id, $user_id, $state, $date);
    $history_stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Issue reported, notifications sent, status recorded, and history updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Report failed: ' . $stmt->error]);
}

$conn->close();
?>
