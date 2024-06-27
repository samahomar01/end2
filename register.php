<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// بقية الكود يبقى كما هو...
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_techcare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

$email = $data['email'] ?? '';
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$confirmPassword = $data['confirmPassword'] ?? '';
$role = 'admin'; // تعيين الدور إلى 'مشرف' تلقائيًا

if (empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
    die(json_encode(['success' => false, 'message' => 'All fields are required']));
}

if ($password !== $confirmPassword) {
    die(json_encode(['success' => false, 'message' => 'Passwords do not match']));
}

$sql = "SELECT id, email, password, role FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'User already exists']);
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
    http_response_code(500);
    exit();
}

$stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User registered successfully']);
    http_response_code(201);
} else {
    echo json_encode(['success' => false, 'message' => 'Error registering user: ' . $stmt->error]);
    http_response_code(500);
}

$stmt->close();
$conn->close();
?>
