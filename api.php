<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "samahh";

$conn = new mysqli($servername, $username, $password, $dbname);
$response = [];

if ($conn->connect_error) {
    $response['success'] = false;
    $response['message'] = 'Database connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : '';
$role = 'User';

if (empty($name) || empty($email) || empty($password)) {
    die(json_encode(['success' => false, 'message' => 'All fields are required']));
}

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit();
}

$verification_code = bin2hex(random_bytes(16)); // إنشاء رمز تحقق عشوائي
$email_verified = 0; // البريد الإلكتروني غير مفعل

$sql = "INSERT INTO users (name, email, password, role, email_verified, verification_code) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $name, $email, $password, $role, $email_verified, $verification_code);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    
    // إعداد رابط التفعيل
    $activation_link = "http://192.168.1.6/myprojectt/verify.php?code=" . $verification_code;

    $subject = 'تفعيل حسابك';
    $body = 'مرحبًا ' . $name . '،<br><br>شكراً لتسجيلك. يرجى الضغط على الرابط أدناه لتفعيل حسابك:<br><a href="' . $activation_link . '">تفعيل الحساب</a>';

    // استدعاء الدالة لإرسال البريد الإلكتروني
    require_once 'mail.php';
    if (sendVerificationEmail($email, $subject, $body)) {
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully. Please check your email to activate your account.',
            'user_id' => $user_id // إضافة user_id إلى الاستجابة
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration successful, but email sending failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt->error]);
}

$conn->close();
?>
