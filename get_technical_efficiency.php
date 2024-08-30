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

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['start_date']) && isset($data['end_date'])) {
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];

    // حساب عدد التذاكر في حالة 'open'
    $sqlOpen = "SELECT COUNT(*) as openTickets FROM tickets WHERE state = 'open' AND date BETWEEN '$start_date' AND '$end_date'";
    
    // حساب عدد التذاكر في حالة 'closed'
    $sqlClosed = "SELECT COUNT(*) as closedTickets FROM tickets WHERE state = 'closed' AND date BETWEEN '$start_date' AND '$end_date'";
    
    // حساب عدد التذاكر في حالة 'assigned'
    $sqlAssigned = "SELECT COUNT(*) as assignedTickets FROM tickets WHERE state = 'assigned' AND date BETWEEN '$start_date' AND '$end_date'";
    
    // حساب عدد التذاكر في حالة 'pending'
    $sqlPending = "SELECT COUNT(*) as pendingTickets FROM tickets WHERE state = 'pending' AND date BETWEEN '$start_date' AND '$end_date'";

    $resultOpen = $conn->query($sqlOpen);
    $resultClosed = $conn->query($sqlClosed);
    $resultAssigned = $conn->query($sqlAssigned);
    $resultPending = $conn->query($sqlPending);

    if ($resultOpen && $resultClosed && $resultAssigned && $resultPending) {
        $openTickets = $resultOpen->fetch_assoc()['openTickets'];
        $closedTickets = $resultClosed->fetch_assoc()['closedTickets'];
        $assignedTickets = $resultAssigned->fetch_assoc()['assignedTickets'];
        $pendingTickets = $resultPending->fetch_assoc()['pendingTickets'];

        // حساب الكفاءة الفنية كنسبة مئوية
        if (($closedTickets + $pendingTickets + $assignedTickets) > 0) {
            $technicalEfficiency = ($closedTickets / ($closedTickets + $pendingTickets + $assignedTickets)) * 100; // حساب النسبة المئوية
            $technicalEfficiencyFormatted = number_format($technicalEfficiency, 2); // تنسيق النسبة مع فاصلتين عشريتين
        } else {
            $technicalEfficiencyFormatted = 0; // لا تذاكر، تعيين الكفاءة كـ 0
        }

        echo json_encode([
            "success" => true, 
            "technicalEfficiency" => $technicalEfficiencyFormatted, 
            "openTickets" => $openTickets,
            "closedTickets" => $closedTickets,
            "assignedTickets" => $assignedTickets,
            "pendingTickets" => $pendingTickets
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Error calculating efficiency"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid date range"]);
}

$conn->close();
?>
