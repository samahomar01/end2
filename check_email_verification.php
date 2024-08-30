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

$email = $data['email'] ?? '';

if (empty($email)) {
    die(json_encode(['success' => false, 'message' => 'Email is required']));
}

$sql = "SELECT email_verified FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if ($user['email_verified'] == 1) {
        $response['success'] = true;
        $response['message'] = 'Account is verified';
        $response['flag'] = 1; // إرجاع العلم بقيمة 1 إذا كان الحساب مفعلًا
    } else {
        $response['success'] = false;
        $response['message'] = 'Account is not verified';
        $response['flag'] = 0; // إرجاع العلم بقيمة 0 إذا لم يكن الحساب مفعلًا
    }
} else {
    $response['success'] = false;
    $response['message'] = 'User not found';
    $response['flag'] = 0; // إرجاع العلم بقيمة 0 إذا لم يتم العثور على المستخدم
}

echo json_encode($response);
$conn->close();
?>
