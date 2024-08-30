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

// جلب بيانات المدخلات بصيغة JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $response['success'] = false;
    $response['message'] = 'Invalid input data';
    echo json_encode($response);
    exit();
}

// استخراج البريد الإلكتروني وكلمة المرور من البيانات
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// التحقق من عدم خلو الحقول
if (empty($email) || empty($password)) {
    $response['success'] = false;
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

// تنفيذ استعلام التحقق من المستخدم
$sql = "SELECT id, email, password, role, email_verified FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // التحقق من صحة كلمة المرور
    if (password_verify($password, $user['password'])) {
        // التحقق من تفعيل البريد الإلكتروني
        if ($user['email_verified'] == 1) {
            unset($user['password']); // إزالة كلمة المرور من الاستجابة

            // استعلام لجلب التذاكر الخاصة بالمستخدم
            $sqlTickets = "SELECT * FROM tickets WHERE user_id = ?";
            $stmtTickets = $conn->prepare($sqlTickets);
            $stmtTickets->bind_param("i", $user['id']);
            $stmtTickets->execute();
            $resultTickets = $stmtTickets->get_result();
            $tickets = $resultTickets->fetch_all(MYSQLI_ASSOC);

            $response['success'] = true;
            $response['message'] = 'Login successful';
            $response['user'] = $user;
            $response['tickets'] = $tickets;
        } else {
            $response['success'] = false;
            $response['message'] = 'Email not verified. Please verify your email before logging in.';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid email or password';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid email or password';
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();

// إرسال الاستجابة النهائية
echo json_encode($response);
?>
