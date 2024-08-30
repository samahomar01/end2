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

// Get date range from query parameters
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Validate date format
if (!DateTime::createFromFormat('Y-m-d', $startDate) || !DateTime::createFromFormat('Y-m-d', $endDate)) {
    echo json_encode(["success" => false, "message" => "Invalid date format"]);
    $conn->close();
    exit();
}

// Query to get tickets within the specified date range
$sql = $conn->prepare("SELECT ticket_id, date, state FROM tickets WHERE (state = 'open' OR state = 'closed') AND date BETWEEN ? AND ?");
$sql->bind_param('ss', $startDate, $endDate);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $tickets = [];
    while($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
    echo json_encode(["success" => true, "tickets" => $tickets]);
} else {
    echo json_encode(["success" => false, "message" => "No tickets found"]);
}

$conn->close();
?>
