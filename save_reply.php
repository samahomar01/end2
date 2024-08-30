<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// التعامل مع طلبات OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
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

if (!$data || !isset($data['ticket_id']) || !isset($data['diagnosis']) || !isset($data['solution']) || !isset($data['status']) || !isset($data['equipment'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid input data']));
}

$ticket_id = $data['ticket_id'];
$diagnosis = $data['diagnosis'];
$solution = $data['solution'];
$status = $data['status'];
$equipment = $data['equipment'];

// Fetch user_id (technician) from ticket_assignments table
$technicianQuery = $conn->prepare("SELECT user_id FROM ticket_assignments WHERE ticket_id = ?");
$technicianQuery->bind_param("i", $ticket_id);

if (!$technicianQuery->execute()) {
    die(json_encode(['success' => false, 'message' => 'Failed to fetch technician data: ' . $technicianQuery->error]));
}

$technicianResult = $technicianQuery->get_result()->fetch_assoc();
if (!$technicianResult) {
    die(json_encode(['success' => false, 'message' => 'No technician found for the given ticket_id']));
}

$technician_id = $technicianResult['user_id'];

// إذا كانت الحالة "solved" قم بتحديثها إلى "closed"
$original_status = $status;
if ($status == 'solved') {
    $status = 'closed';
}

// Insert the technician's reply
$sql = "INSERT INTO technician_replies (ticket_id, diagnosis, solution, status, equipment, user_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]));
}

$stmt->bind_param("issssi", $ticket_id, $diagnosis, $solution, $original_status, $equipment, $technician_id);

if ($stmt->execute()) {
    // Check if the new status is different from the current status
    $checkStatusSql = "SELECT status FROM ticket_history WHERE ticket_id = ? ORDER BY status_date DESC LIMIT 1";
    $checkStatusStmt = $conn->prepare($checkStatusSql);
    $checkStatusStmt->bind_param("i", $ticket_id);
    $checkStatusStmt->execute();
    $checkStatusResult = $checkStatusStmt->get_result();
    $currentStatus = $checkStatusResult->fetch_assoc();

    if ($currentStatus && $currentStatus['status'] === $status) {
        echo json_encode(['success' => false, 'message' => 'The status is already ' . $status]);
    } else {
        // Update the ticket status in the ticket_history table
        $updateStatusSql = "INSERT INTO ticket_history (ticket_id, status_date, status, user_id) VALUES (?, NOW(), ?, ?)";
        $updateStmt = $conn->prepare($updateStatusSql);
        if ($updateStmt === false) {
            die(json_encode(['success' => false, 'message' => 'Failed to prepare update statement: ' . $conn->error]));
        }
        $updateStmt->bind_param("isi", $ticket_id, $status, $technician_id);
        if (!$updateStmt->execute()) {
            die(json_encode(['success' => false, 'message' => 'Failed to update ticket_history: ' . $updateStmt->error]));
        }

        // Update the ticket status in the tickets table
        $updateTicketSql = "UPDATE tickets SET state = ? WHERE ticket_id = ?";
        $updateTicketStmt = $conn->prepare($updateTicketSql);
        if ($updateTicketStmt === false) {
            die(json_encode(['success' => false, 'message' => 'Failed to prepare update statement for tickets: ' . $conn->error]));
        }
        $updateTicketStmt->bind_param("si", $status, $ticket_id);
        if (!$updateTicketStmt->execute()) {
            die(json_encode(['success' => false, 'message' => 'Failed to update tickets: ' . $updateTicketStmt->error]));
        }

        // إذا كانت الحالة "closed"، قم بتحديث حالة الجهاز إلى "active"
        if ($status == 'closed') {
            $updateEquipmentSql = "UPDATE devices SET status = 'active' WHERE device_id = (SELECT device_id FROM tickets WHERE ticket_id = ?)";
            $updateEquipmentStmt = $conn->prepare($updateEquipmentSql);
            if ($updateEquipmentStmt === false) {
                die(json_encode(['success' => false, 'message' => 'Failed to prepare update equipment statement: ' . $conn->error]));
            }
            $updateEquipmentStmt->bind_param("i", $ticket_id);
            if (!$updateEquipmentStmt->execute()) {
                die(json_encode(['success' => false, 'message' => 'Failed to update equipment status: ' . $updateEquipmentStmt->error]));
            } else {
                $response['success'] = true;
                $response['message'] = 'Device status updated to active.';
            }
        }

        // Fetch user_id, lab_id, and device_id from tickets table for notifications
        $ticketQuery = $conn->prepare("SELECT user_id, lab_id, device_id FROM tickets WHERE ticket_id = ?");
        if ($ticketQuery === false) {
            die(json_encode(['success' => false, 'message' => 'Failed to prepare select statement: ' . $conn->error]));
        }
        $ticketQuery->bind_param("i", $ticket_id);
        if ($ticketQuery->execute()) {
            $ticketResult = $ticketQuery->get_result()->fetch_assoc();
            if ($ticketResult) {
                $ticket_user_id = $ticketResult['user_id'];
                $lab_id = $ticketResult['lab_id'];
                $device_id = $ticketResult['device_id'];

                if ($ticket_user_id !== null && $lab_id !== null && $device_id !== null) {
                    // إرسال إشعار لمقدم البلاغ
                    $message_user = "Your ticket $ticket_id has been updated to $status.";
                    $notificationSqlUser = "INSERT INTO notifications (ticket_id, lab_id, device_id, message, created_at, is_read, user_id) VALUES (?, ?, ?, ?, NOW(), 0, ?)";
                    $notificationStmtUser = $conn->prepare($notificationSqlUser);
                    if ($notificationStmtUser === false) {
                        die(json_encode(['success' => false, 'message' => 'Failed to prepare notification statement for user: ' . $conn->error]));
                    }
                    $notificationStmtUser->bind_param("iiisi", $ticket_id, $lab_id, $device_id, $message_user, $ticket_user_id);

                    if (!$notificationStmtUser->execute()) {
                        die(json_encode(['success' => false, 'message' => 'Failed to send notifications to user: ' . $notificationStmtUser->error]));
                    }

                    // إرسال إشعار للمشرف
                    $message_supervisor = "Ticket number $ticket_id has been updated to $status.";
                    $supervisor_id = 1;
                    $notificationSqlSupervisor = "INSERT INTO notifications (ticket_id, lab_id, device_id, message, created_at, is_read, user_id) VALUES (?, ?, ?, ?, NOW(), 0, ?)";
                    $notificationStmtSupervisor = $conn->prepare($notificationSqlSupervisor);
                    if ($notificationStmtSupervisor === false) {
                        die(json_encode(['success' => false, 'message' => 'Failed to prepare notification statement for supervisor: ' . $conn->error]));
                    }
                    $notificationStmtSupervisor->bind_param("iiisi", $ticket_id, $lab_id, $device_id, $message_supervisor, $supervisor_id);

                    if (!$notificationStmtSupervisor->execute()) {
                        die(json_encode(['success' => false, 'message' => 'Failed to send notifications to supervisor: ' . $notificationStmtSupervisor->error]));
                    }

                    // إرسال إشعار للفني الذي أغلق التذكرة
                    $message_technician = "You have closed ticket $ticket_id.";
                    $notificationSqlTechnician = "INSERT INTO notifications (ticket_id, lab_id, device_id, message, created_at, is_read, user_id) VALUES (?, ?, ?, ?, NOW(), 0, ?)";
                    $notificationStmtTechnician = $conn->prepare($notificationSqlTechnician);
                    if ($notificationStmtTechnician === false) {
                        die(json_encode(['success' => false, 'message' => 'Failed to prepare notification statement for technician: ' . $conn->error]));
                    }
                    $notificationStmtTechnician->bind_param("iiisi", $ticket_id, $lab_id, $device_id, $message_technician, $technician_id);

                    if (!$notificationStmtTechnician->execute()) {
                        die(json_encode(['success' => false, 'message' => 'Failed to send notifications to technician: ' . $notificationStmtTechnician->error]));
                    }

                    $response['success'] = true;
                    $response['message'] = 'Ticket status updated, and notifications sent successfully.';
                }
            }
        } else {
            die(json_encode(['success' => false, 'message' => 'Failed to fetch ticket details: ' . $ticketQuery->error]));
        }
    }

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to insert technician reply: ' . $stmt->error]);
}

$conn->close();
?>
