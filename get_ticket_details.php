<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_techcare";

// الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// استقبال البيانات المرسلة عبر الطلب (POST)
$data = json_decode(file_get_contents('php://input'), true);

// التحقق من صحة البيانات المستلمة
if (!$data || !isset($data['ticket_id'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

// استخراج معرف التذكرة من البيانات
$ticket_id = $data['ticket_id'];

// استعلام لاسترجاع تفاصيل التذكرة ومعلومات المستخدم
$sql = "
    SELECT 
        tickets.ticket_id, 
        tickets.date, 
        tickets.issue AS description, 
        users.name, 
        users.email 
    FROM 
        tickets 
    JOIN 
        users 
    ON 
        tickets.user_id = users.id 
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
