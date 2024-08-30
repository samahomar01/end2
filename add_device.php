<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$data = json_decode(file_get_contents('php://input'), true);

$name = $data['deviceName'] ?? '';
$serial_no = $data['serialNumber'] ?? '';
$status = $data['status'] ?? '';
$brand = $data['brand'] ?? '';
$manufacturLot = $data['manufacturer'] ?? '';
$type = $data['type'] ?? '';
$language = $data['language'] ?? ''; // استلام اللغة
$lo_id = $data['labId'] ?? 0;

$stmt = $conn->prepare("INSERT INTO equipment (name, brand, serial_no, status, manufacturLot, lo_id, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssis", $name, $brand, $serial_no, $status, $manufacturLot, $lo_id, $type);

if ($stmt->execute()) {
    $deviceId = $stmt->insert_id;
    $stmt->close();

    $stmt = null;
    switch ($type) {
        case 'AccessPoint':
            $stmt = $conn->prepare("INSERT INTO accesspoint (eq_id, subtype, wireless, speed) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2]);
            break;
        case 'Case':
            $stmt = $conn->prepare("INSERT INTO `case` (eq_id, type, size, motherboard, processor, memory, harddiskcap, harddisktype) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $data['specificFields'][3], $data['specificFields'][4], $data['specificFields'][5], $data['specificFields'][6]);
            break;
        case 'Fax':
            $stmt = $conn->prepare("INSERT INTO fax (eq_id, type, subtype, speed, typeofpaper, refillcode) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $data['specificFields'][3], $data['specificFields'][4]);
            break;
            case 'Keyboard':
                // تعديل الاستعلام لإرسال $language إلى الحقل `aren`
                $stmt = $conn->prepare("INSERT INTO keyboard (eq_id, type, subtype, layout, aren, connectiontype) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $language, $data['specificFields'][4]);
                break;
            
        case 'Monitor':
            $stmt = $conn->prepare("INSERT INTO monitor (eq_id, type, subtype, size, maxresolution, coonnectiontype) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $data['specificFields'][3], $data['specificFields'][4]);
            break;
        case 'Mouse':
            $stmt = $conn->prepare("INSERT INTO mouse (eq_id, type, subtype, connectiontype) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2]);
            break;
        case 'Networking':
            $stmt = $conn->prepare("INSERT INTO networking (eq_id, type, subtype, networktype, networkname) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $data['specificFields'][3]);
            break;
        case 'Photocopier':
            $stmt = $conn->prepare("INSERT INTO photocopier (eq_id, type, subtype, documentfeeder, maxsizepaper) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $data['specificFields'][3]);
            break;
        case 'Printer':
            $stmt = $conn->prepare("INSERT INTO printer (eq_id, type, subtype, maxpapersize, refillcode, connectiontype) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $data['specificFields'][3], $data['specificFields'][4]);
            break;
        case 'Projector':
            $stmt = $conn->prepare("INSERT INTO projector (eq_id, type, subtype, maxprojectionsize) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2]);
            break;
        case 'Router':
            $stmt = $conn->prepare("INSERT INTO router (eq_id, subtype, totalnumports, poeports, wireless, speed) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $data['specificFields'][3], $data['specificFields'][4]);
            break;
        case 'Scanner':
            $stmt = $conn->prepare("INSERT INTO scanner (eq_id, type, subtype, maxsizetype) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2]);
            break;
        case 'Switch':
            $stmt = $conn->prepare("INSERT INTO `switch` (eq_id, subtype, totalports, speed) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2]);
            break;
        case 'UPS':
            $stmt = $conn->prepare("INSERT INTO ups (eq_id, type, power, runat, duration) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2], $data['specificFields'][3]);
            break;
        case 'Webcam':
            $stmt = $conn->prepare("INSERT INTO webcam (eq_id, type, resolution, connectiontype) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $deviceId, $data['specificFields'][0], $data['specificFields'][1], $data['specificFields'][2]);
            break;
        case 'Other':
            $stmt = $conn->prepare("INSERT INTO other (eq_id, type, description) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $deviceId, $data['specificFields'][0], $data['specificFields'][1]);
            break;
    }

    if ($stmt && $stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Device added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add device details', 'error' => $stmt ? $stmt->error : 'Unknown error']);
    }

    if ($stmt) {
        $stmt->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add device', 'error' => $stmt->error]);
}

$conn->close();
?>
