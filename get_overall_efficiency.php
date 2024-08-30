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

    $sqlClosed = "SELECT COUNT(*) as closedTickets FROM tickets WHERE state = 'closed' AND date BETWEEN '$start_date' AND '$end_date'";
    $sqlOpen = "SELECT COUNT(*) as openTickets FROM tickets WHERE (state = 'open' OR state = 'assigned' OR state = 'pending') AND date BETWEEN '$start_date' AND '$end_date'";

    $resultClosed = $conn->query($sqlClosed);
    $resultOpen = $conn->query($sqlOpen);

    if ($resultClosed && $resultOpen) {
        $closedTickets = $resultClosed->fetch_assoc()['closedTickets'];
        $openTickets = $resultOpen->fetch_assoc()['openTickets'];

        $totalTickets = $closedTickets + $openTickets;

        if ($totalTickets > 0) {
            $overallEfficiency = $closedTickets / $totalTickets;
        } else {
            $overallEfficiency = 0;
        }

        echo json_encode([
            "success" => true, 
            "overallEfficiency" => $overallEfficiency, 
            "openTickets" => $openTickets, 
            "closedTickets" => $closedTickets
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Error calculating efficiency"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid date range"]);
}

$conn->close();
?>
