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
    die("Invalid verification code");
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
        echo "Your account has been activated successfully.";
    } else {
        echo "Failed to activate your account.";
    }
} else {
    echo "Invalid or already activated verification code.";
}

$conn->close();
?>
