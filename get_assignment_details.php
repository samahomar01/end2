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

if (!$data || !isset($data['ticket_id'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

$ticket_id = $data['ticket_id'];

$sql = "SELECT ta.assigned_by, ta.assigned_date, u.name as assigned_by_name
        FROM ticket_assignments ta
        JOIN users u ON ta.assigned_by = u.id
        WHERE ta.ticket_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'assigned_by' => $row['assigned_by_name'], 'assigned_date' => $row['assigned_date']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No assignment details found']);
}

$conn->close();
?>
