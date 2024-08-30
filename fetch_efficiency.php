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

    $sqlClosedAssigned = "SELECT COUNT(*) as closedAssigned FROM tickets WHERE state = 'closed' AND previous_state = 'assigned' AND date BETWEEN '$start_date' AND '$end_date'";
    $sqlClosedPending = "SELECT COUNT(*) as closedPending FROM tickets WHERE state = 'closed' AND previous_state = 'pending' AND date BETWEEN '$start_date' AND '$end_date'";
    $sqlAssigned = "SELECT COUNT(*) as assignedTickets FROM tickets WHERE state = 'assigned' AND date BETWEEN '$start_date' AND '$end_date'";

    $resultClosedAssigned = $conn->query($sqlClosedAssigned);
    $resultClosedPending = $conn->query($sqlClosedPending);
    $resultAssigned = $conn->query($sqlAssigned);

    if ($resultClosedAssigned && $resultClosedPending && $resultAssigned) {
        $closedAssigned = $resultClosedAssigned->fetch_assoc()['closedAssigned'];
        $closedPending = $resultClosedPending->fetch_assoc()['closedPending'];
        $assignedTickets = $resultAssigned->fetch_assoc()['assignedTickets'];

        $totalClosed = $closedAssigned + $closedPending;

        if ($assignedTickets > 0) {
            $technicalEfficiency = $totalClosed / $assignedTickets;
        } else {
            $technicalEfficiency = 0;
        }

        echo json_encode([
            "success" => true, 
            "technicalEfficiency" => $technicalEfficiency, 
            "closedAssigned" => $closedAssigned, 
            "closedPending" => $closedPending,
            "assignedTickets" => $assignedTickets
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Error calculating efficiency"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid date range"]);
}

$conn->close();
?>
