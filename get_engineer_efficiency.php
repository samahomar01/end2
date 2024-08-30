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

// قراءة تواريخ البداية والنهاية من الطلب
$startDate = $_POST['start_date'] ?? null;
$endDate = $_POST['end_date'] ?? null;

// تحقق من صحة التواريخ
if (!$startDate || !$endDate) {
    echo json_encode(["success" => false, "message" => "Please provide both start and end dates"]);
    $conn->close();
    exit();
}

// إعداد تواريخ للنطاق الكامل لليوم
$startDate = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
$endDate = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';

// استعلام لجلب جميع الفنيين مع تعداد التذاكر التي تم تعيينها لهم، المغلقة، والمعاد تعيينها
$sql = "
    SELECT 
        u.id AS user_id, 
        u.name AS engineerName,
        COUNT(CASE WHEN th.technician_role = u.id AND th.status = 'assigned' AND th.status_date BETWEEN ? AND ? THEN th.id END) AS assignedTickets,
        COUNT(CASE WHEN th.user_id = u.id AND th.status = 'closed' AND th.status_date BETWEEN ? AND ? THEN th.id END) AS closedTickets,
        COUNT(CASE WHEN th.technician_role = u.id AND th.status = 'reassigned' AND th.status_date BETWEEN ? AND ? THEN th.id END) AS reassignedTickets
    FROM users u
    LEFT JOIN ticket_history th ON u.id = th.technician_role OR u.id = th.user_id
    WHERE u.role = 'Technician'
    GROUP BY u.id, u.name;
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Failed to prepare SQL statement: " . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param('ssssss', $startDate, $endDate, $startDate, $endDate, $startDate, $endDate);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to execute SQL statement: " . $stmt->error]);
    $conn->close();
    exit();
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $engineerEfficiency = [];
    while ($row = $result->fetch_assoc()) {
        $engineerEfficiency[] = [
            "engineerName" => $row['engineerName'],
            "assignedTickets" => $row['assignedTickets'],
            "closedTickets" => $row['closedTickets'],
            "reassignedTickets" => $row['reassignedTickets']
        ];
    }
    echo json_encode(["success" => true, "engineerEfficiency" => $engineerEfficiency]);
} else {
    echo json_encode(["success" => false, "message" => "No data found"]);
}

$conn->close();
?>
