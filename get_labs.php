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

$sql = "SELECT id, name, physicalLocation, publicAccess FROM location";
$result = $conn->query($sql);

$response = ['success' => true, 'labs' => []];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['id'] = (int) $row['id'];
        $row['publicAccess'] = (int) $row['publicAccess'];
        $response['labs'][] = $row;
    }
} else {
    $response['success'] = false;
    $response['message'] = 'No labs found';
}

echo json_encode($response);

$conn->close();
?>
