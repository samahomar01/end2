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

// استلام تواريخ البداية والنهاية من الطلب
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// التحقق من وجود التواريخ
if (empty($startDate) || empty($endDate)) {
    echo json_encode(['success' => false, 'message' => 'Start date and end date are required.']);
    exit;
}

// التحقق من تنسيق التواريخ
if (!DateTime::createFromFormat('Y-m-d', $startDate) || !DateTime::createFromFormat('Y-m-d', $endDate)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format.']);
    exit;
}

// استعلام SQL لاسترجاع التذاكر من جدول tickets وردود الفنيين من جدول technician_replies
$sql = "
    SELECT t.ticket_id, t.date, t.state AS status, tr.diagnosis AS reason
    FROM tickets t
    LEFT JOIN technician_replies tr ON t.ticket_id = tr.ticket_id
    WHERE t.state = 'pending' AND t.date BETWEEN ? AND ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $startDate, $endDate);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}

$stmt->close();
$conn->close();

// إرجاع النتيجة بتنسيق JSON
echo json_encode(['success' => true, 'tickets' => $tickets]);
?>
