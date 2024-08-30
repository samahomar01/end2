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

// استعلام SQL
$sql = "SELECT ticket_id, user_id, state, date, issue FROM tickets";
$result = $conn->query($sql);

$tickets = array();

if ($result->num_rows > 0) {
    // إخراج البيانات لكل صف
    while($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
} else {
    echo json_encode(array("success" => false, "message" => "No tickets found"));
    exit();
}

echo json_encode(array("success" => true, "tickets" => $tickets));

// إغلاق الاتصال
$conn->close();
?>
