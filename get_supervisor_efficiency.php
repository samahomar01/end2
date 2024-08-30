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

// حساب عدد التذاكر المعينة، المفتوحة، المعلقة، والمغلقة
$sql = "SELECT 
            SUM(CASE WHEN ticket_history.status IN ('assigned', 'closed', 'pending') THEN 1 ELSE 0 END) AS totalAssignedTickets,
            SUM(CASE WHEN tickets.state = 'open' THEN 1 ELSE 0 END) AS openTickets,
            SUM(CASE WHEN tickets.state = 'pending' THEN 1 ELSE 0 END) AS pendingTickets,
            SUM(CASE WHEN tickets.state = 'closed' THEN 1 ELSE 0 END) AS closedTickets
        FROM 
            ticket_history
        LEFT JOIN 
            tickets ON ticket_history.ticket_id = tickets.ticket_id
        WHERE 
            ticket_history.status IN ('assigned', 'closed', 'pending')";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $totalAssignedTickets = $data['totalAssignedTickets'];
    $openTickets = $data['openTickets'];
    $pendingTickets = $data['pendingTickets'];
    $closedTickets = $data['closedTickets'];

    // حساب كفاءة المشرف بناءً على المعادلة المعدلة
    if (($totalAssignedTickets + $openTickets + $pendingTickets) > 0) {
        $efficiencyPercentage = (($totalAssignedTickets + $closedTickets) / ($totalAssignedTickets + $openTickets + $pendingTickets)) * 100;
    } else {
        $efficiencyPercentage = 0;
    }

    echo json_encode([
        "success" => true, 
        "technicalEfficiency" => $efficiencyPercentage, 
        "totalAssignedTickets" => $totalAssignedTickets,
        "openTickets" => $openTickets,
        "pendingTickets" => $pendingTickets,
        "closedTickets" => $closedTickets
    ]);
} else {
    echo json_encode(["success" => false, "message" => "No data found"]);
}

$conn->close();
?>
