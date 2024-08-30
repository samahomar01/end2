<?php
header('Content-Type: application/json');

// إعداد الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "samahh";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// الحصول على البريد الإلكتروني من البيانات الواردة
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

// التحقق من البريد الإلكتروني
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit();
}

// التحقق مما إذا كان البريد الإلكتروني موجودًا في قاعدة البيانات
$sql = "SELECT * FROM users WHERE email = ? AND email_verified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // إعادة إرسال رسالة التحقق
    $row = $result->fetch_assoc();
    $user_id = $row['id'];

    // توليد رمز التحقق
    $verification_code = md5(uniqid(rand(), true));

    // تخزين رمز التحقق في قاعدة البيانات
    $sql = "UPDATE users SET verification_code = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $verification_code, $user_id);
    $stmt->execute();

    // إعداد البريد الإلكتروني
    $to = $email;
    $subject = "Verify Your Email Address";
    $message = "Please click the link below to verify your email address:\n";
    $message .= "http://yourdomain.com/verify_email.php?code=$verification_code";
    $headers = "From: no-reply@yourdomain.com";

    // إرسال البريد الإلكتروني
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Verification email sent']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send verification email']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Email not found or already verified']);
}

$stmt->close();
$conn->close();
?>
