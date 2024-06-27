<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_techcare";

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$sql = "SELECT id, name, physicalLocation, publicAccess FROM location";
$result = $conn->query($sql);

$labs = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $labs[] = $row;
    }
} else {
    echo json_encode(['message' => 'No labs found']);
}

echo json_encode($labs);

$conn->close();
?>
