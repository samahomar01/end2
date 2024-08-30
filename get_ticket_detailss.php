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

// التحقق من الاتصال
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['ticket_id'])) {
    $ticket_id = intval($data['ticket_id']);
    
    // استرجاع تفاصيل التذكرة من جدول tickets
    $sql = "SELECT device_id, lab_id FROM tickets WHERE ticket_id = $ticket_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $ticket_details = $result->fetch_assoc();
        echo json_encode($ticket_details);
    } else {
        echo json_encode(['success' => false, 'message' => 'No ticket found with this ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ticket ID not provided']);
}

$conn->close();
?>
