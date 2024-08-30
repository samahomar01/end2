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


// استلام براميتر status_filter من طلب الـ GET
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : 'all';

// بناء استعلام SQL بناءً على الفلتر
if ($status_filter == 'pending_assigned_open') {
    $sql = "SELECT ticket_id, user_id, state, date, issue, lab_id, device_id FROM tickets WHERE state IN ('pending', 'assigned', 'open')";
} else {
    $sql = "SELECT ticket_id, user_id, state, date, issue, lab_id, device_id FROM tickets";
}

$result = $conn->query($sql);

$response = array();
$response['success'] = false;

if ($result->num_rows > 0) {
    $tickets = array();
    while($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
    $response['success'] = true;
    $response['tickets'] = $tickets;
} else {
    $response['error'] = "No tickets found";
}

$conn->close();

echo json_encode($response);
?>
