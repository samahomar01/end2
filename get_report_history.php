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

if (!isset($data['report_id'])) {
    echo json_encode(['success' => false, 'message' => 'Ticket ID not provided']);
    exit();
}

$ticket_id = $data['report_id'];

$query = "SELECT status, status_date FROM ticket_history WHERE ticket_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

$response = array();
if ($result->num_rows > 0) {
    $response['success'] = true;
    $response['history'] = array();
    while ($row = $result->fetch_assoc()) {
        $response['history'][] = array(
            'status' => $row['status'],
            'status_date' => $row['status_date']
        );
    }
} else {
    $response['success'] = false;
    $response['message'] = 'No history found for this ticket';
}

echo json_encode($response);
$conn->close();
?>
