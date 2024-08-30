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
// استقبال تواريخ البداية والنهاية من الطلب
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// تحقق من وجود التواريخ قبل متابعة الاستعلام
if ($startDate && $endDate) {
    $sql = "
        SELECT 
            l.name AS lab_name,
            d.name AS device_name,
            COUNT(t.ticket_id) AS faults_number,
            MAX(t.issue) AS problem
        FROM 
            location l
        JOIN 
            devices d ON l.id = d.lab_id
        LEFT JOIN 
            tickets t ON d.device_id = t.device_id AND l.id = t.lab_id
        WHERE 
            t.date BETWEEN ? AND ?  -- تصفية النتائج بناءً على نطاق التواريخ
        GROUP BY 
            l.id, d.device_id
        ORDER BY 
            l.name, d.name";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate); // ربط المتغيرات التواريخ بالاستعلام
} else {
    // إذا لم يتم تقديم التواريخ، قم بتشغيل الاستعلام بدون تصفية
    $sql = "
        SELECT 
            l.name AS lab_name,
            d.name AS device_name,
            COUNT(t.ticket_id) AS faults_number,
            MAX(t.issue) AS problem
        FROM 
            location l
        JOIN 
            devices d ON l.id = d.lab_id
        LEFT JOIN 
            tickets t ON d.device_id = t.device_id AND l.id = t.lab_id
        GROUP BY 
            l.id, d.device_id
        ORDER BY 
            l.name, d.name";
    
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);

$conn->close();
?>
