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

// استقبال البيانات المرسلة عبر الطلب (POST)
$data = json_decode(file_get_contents('php://input'), true);

// التحقق من صحة البيانات المستلمة
if (!$data || !isset($data['ticket_id'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

// استخراج معرف التذكرة من البيانات
$ticket_id = $data['ticket_id'];

// استعلام لاسترجاع تفاصيل التذكرة ومعلومات المستخدم، المختبر، والجهاز
$sql = "
    SELECT 
        tickets.ticket_id, 
        tickets.date, 
        tickets.issue AS description, 
        users.name, 
        users.email, 
        location.name AS lab_name, 
        location.physicalLocation, 
        devices.name AS device_name 
    FROM 
        tickets 
    JOIN 
        users ON tickets.user_id = users.id 
    JOIN 
        location ON tickets.lab_id = location.id 
    JOIN 
        devices ON tickets.device_id = devices.device_id 
    WHERE 
        tickets.ticket_id = ?
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

// التحقق من وجود بيانات مسترجعة
if ($result->num_rows > 0) {
    $ticketDetails = $result->fetch_assoc();
    echo json_encode(['success' => true, 'ticket_details' => $ticketDetails]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ticket not found']);
}

// إغلاق اتصال قاعدة البيانات
$conn->close();
?>
