<?php
header('Content-Type: application/json');

// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_techcare";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// استعلام SQL
$sql = "SELECT ticket_id, user_id, state, date, issue FROM tickets";
$result = $conn->query($sql);

$tickets = array();

if ($result->num_rows > 0) {
    // إخراج البيانات لكل صف
    while($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
} else {
    echo json_encode(array("success" => false, "message" => "No tickets found"));
    exit();
}

echo json_encode(array("success" => true, "tickets" => $tickets));

// إغلاق الاتصال
$conn->close();
?>
