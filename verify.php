<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "samahh";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$verification_code = $_GET['code'] ?? '';

if (empty($verification_code)) {
    echo "<html><body><h1>Invalid verification code</h1></body></html>";
    die();
}

$sql = "SELECT * FROM users WHERE verification_code = ? AND email_verified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $verification_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sql = "UPDATE users SET email_verified = 1 WHERE verification_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $verification_code);
    if ($stmt->execute()) {
        echo "<html><body><h1>Your account has been activated successfully.</h1></body></html>";
    } else {
        echo "<html><body><h1>Failed to activate your account.</h1></body></html>";
    }
} else {
    echo "<html><body><h1>Invalid or already activated verification code.</h1></body></html>";
}

$conn->close();
?>
