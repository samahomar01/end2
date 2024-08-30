<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "samahh";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'assignTechnician') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['ticket_id']) && isset($data['technician_id']) && isset($data['device_id']) && isset($data['lab_id'])) {
        $ticket_id = intval($data['ticket_id']);
        $technician_id = intval($data['technician_id']);
        $device_id = intval($data['device_id']);
        $lab_id = intval($data['lab_id']);
        $assigned_by = 1; // معرف المستخدم الذي قام بالتعيين

        // تحديث التذكرة بمعرف الجهاز ومعرف المعمل وتحديث الحالة إلى 'assigned'
        $update_ticket_sql = "UPDATE tickets SET device_id = $device_id, lab_id = $lab_id, state = 'assigned' WHERE ticket_id = $ticket_id";
        $update_device_sql = "UPDATE devices SET status = 'inactive' WHERE device_id  = $device_id";

        if ($conn->query($update_ticket_sql) === TRUE) {
            if ($conn->query($update_device_sql) === TRUE) {
                // إدراج التعيين في جدول التعيينات
                $insert_assignment_sql = "INSERT INTO ticket_assignments (ticket_id, user_id, assigned_by, assigned_date) 
                                          VALUES ($ticket_id, $technician_id, $assigned_by, NOW())";
                $conn->query($insert_assignment_sql);

             // إدراج إدخال في جدول ticket_history
$technician_role = $technician_id; // باعتبار أن معرف الفني هو الذي يمثل دوره في هذا السياق
$insert_ticket_history_sql = "INSERT INTO ticket_history (ticket_id, user_id, status, status_date, technician_role) 
                              VALUES ($ticket_id, $assigned_by, 'assigned', NOW(), $technician_role)";
$conn->query($insert_ticket_history_sql);


                // تحديث حالة البلاغ في جدول report_status
                $update_status_sql = "INSERT INTO report_status (report_id, status_date, status) 
                                      VALUES ($ticket_id, NOW(), 'assigned')";
                $conn->query($update_status_sql);

                // الحصول على معرف المستخدم الذي قدم البلاغ
                $get_user_sql = "SELECT user_id FROM tickets WHERE ticket_id = $ticket_id";
                $user_result = $conn->query($get_user_sql);
                if ($user_result->num_rows > 0) {
                    $user_row = $user_result->fetch_assoc();
                    $reporter_id = $user_row['user_id'];

                    // إرسال إشعار للفني
                    $technician_sql = "SELECT name FROM users WHERE id = $technician_id";
                    $result = $conn->query($technician_sql);
                    if ($result->num_rows > 0) {
                        $technician_row = $result->fetch_assoc();
                        $technician_name = $technician_row['name'];

                        $message_technician = "You have been assigned to handle a new ticket. Ticket number: $ticket_id. Please start the process as soon as possible.";
                        $notification_sql = "INSERT INTO notifications (ticket_id, message, user_id, lab_id, device_id, created_at, is_read) 
                                             VALUES ($ticket_id, '$message_technician', $technician_id, $lab_id, $device_id, NOW(), 0)";
                        $conn->query($notification_sql);

                        // إرسال إشعار للمشرف
                        $message_supervisor = "Technician $technician_name has been assigned to ticket number: $ticket_id.";
                        $notification_supervisor_sql = "INSERT INTO notifications (ticket_id, message, user_id, lab_id, device_id, created_at, is_read) 
                                                        VALUES ($ticket_id, '$message_supervisor', $assigned_by, $lab_id, $device_id, NOW(), 0)";
                        $conn->query($notification_supervisor_sql);

                        // إرسال إشعار للمستخدم الذي قدم البلاغ
                        $message_reporter = "Technician $technician_name has been assigned to your ticket number: $ticket_id.";
                        $notification_reporter_sql = "INSERT INTO notifications (ticket_id, message, user_id, lab_id, device_id, created_at, is_read) 
                                                      VALUES ($ticket_id, '$message_reporter', $reporter_id, $lab_id, $device_id, NOW(), 0)";
                        $conn->query($notification_reporter_sql);

                        echo json_encode(['success' => true, 'message' => 'Technician assigned and notifications sent successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Technician not found']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Reporter not found']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating device status: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating ticket: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Incomplete data. Please provide ticket_id, technician_id, device_id, and lab_id.']);
    }
}


if ($action == 'reassignTechnician') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['ticket_id']) && isset($data['technician_id']) && isset($data['device_id']) && isset($data['lab_id'])) {
        $ticket_id = intval($data['ticket_id']);
        $technician_id = intval($data['technician_id']);
        $device_id = intval($data['device_id']);
        $lab_id = intval($data['lab_id']);
        $assigned_by = 1; // معرف المستخدم الذي قام بالتعيين

        // حذف التعيين القديم من جدول التعيينات
        $delete_old_assignment_sql = "DELETE FROM ticket_assignments WHERE ticket_id = $ticket_id";
        if ($conn->query($delete_old_assignment_sql) === TRUE) {
            // إدراج التعيين الجديد في جدول التعيينات
            $insert_new_assignment_sql = "INSERT INTO ticket_assignments (ticket_id, user_id, assigned_by, assigned_date) 
                                          VALUES ($ticket_id, $technician_id, $assigned_by, NOW())";
            if ($conn->query($insert_new_assignment_sql) === TRUE) {
                // إدراج التعيين الجديد في جدول ticket_history
                $technician_role = $technician_id; // قم بتعيين دور الفني هنا
                $insert_ticket_history_sql = "INSERT INTO ticket_history (ticket_id, user_id, status, status_date, technician_role) 
                                              VALUES ($ticket_id, $assigned_by, 'reassigned', NOW(), $technician_role)";
                $conn->query($insert_ticket_history_sql);


                // الحصول على معرف المستخدم الذي قدم البلاغ
                $get_user_sql = "SELECT user_id FROM tickets WHERE ticket_id = $ticket_id";
                $user_result = $conn->query($get_user_sql);
                if ($user_result->num_rows > 0) {
                    $user_row = $user_result->fetch_assoc();
                    $reporter_id = $user_row['user_id'];

                    // إرسال إشعار للفني الجديد
                    $technician_sql = "SELECT name FROM users WHERE id = $technician_id";
                    $result = $conn->query($technician_sql);
                    if ($result->num_rows > 0) {
                        $technician_row = $result->fetch_assoc();
                        $technician_name = $technician_row['name'];

                        $message_technician = "You have been reassigned to handle ticket number: $ticket_id. Please start the process as soon as possible.";
                        $notification_sql = "INSERT INTO notifications (ticket_id, message, user_id, lab_id, device_id, created_at, is_read) 
                                             VALUES ($ticket_id, '$message_technician', $technician_id, $lab_id, $device_id, NOW(), 0)";
                        $conn->query($notification_sql);

                        // إرسال إشعار للمشرف
                        $message_supervisor = "Technician $technician_name has been reassigned to ticket number: $ticket_id.";
                        $notification_supervisor_sql = "INSERT INTO notifications (ticket_id, message, user_id, lab_id, device_id, created_at, is_read) 
                                                        VALUES ($ticket_id, '$message_supervisor', $assigned_by, $lab_id, $device_id, NOW(), 0)";
                        $conn->query($notification_supervisor_sql);

                        // إرسال إشعار للمستخدم الذي قدم البلاغ
                        $message_reporter = "Technician $technician_name has been reassigned to your ticket number: $ticket_id.";
                        $notification_reporter_sql = "INSERT INTO notifications (ticket_id, message, user_id, lab_id, device_id, created_at, is_read) 
                                                      VALUES ($ticket_id, '$message_reporter', $reporter_id, $lab_id, $device_id, NOW(), 0)";
                        $conn->query($notification_reporter_sql);

                        echo json_encode(['success' => true, 'message' => 'Technician reassigned and notifications sent successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Technician not found']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Reporter not found']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error inserting new assignment: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting old assignment: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Incomplete data. Please provide ticket_id, technician_id, device_id, and lab_id.']);
    }
}




if ($action == 'getLabs') {
    $sql = "SELECT id, name FROM location";
    $result = $conn->query($sql);
    
    $labs = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $labs[] = $row;
        }
    }
    
    echo json_encode($labs);
    $conn->close();
    exit();
}

if ($action == 'getDevices') {
    $lab_id = intval($_GET['lab_id']);
    $sql = "SELECT device_id, name FROM devices WHERE lab_id = $lab_id";
    $result = $conn->query($sql);
    
    $devices = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $devices[] = $row;
        }
    }
    
    echo json_encode($devices);
    $conn->close();
    exit();
}

if ($action == 'getTechnicians') {
    $sql = "SELECT id, name FROM users WHERE role = 'Technician'";
    $result = $conn->query($sql);
    
    $technicians = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $technicians[] = $row;
        }
    }
    
    echo json_encode($technicians);
    $conn->close();
    exit();
}

if ($_GET['action'] == 'getLabAndDeviceDetails') {
    $ticket_id = $_GET['ticket_id'];
    $sql = "SELECT lab_id, device_id FROM tickets WHERE ticket_id = $ticket_id";
    $result = mysqli_query($conn, $sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['lab_id' => null, 'device_id' => null]);
    }
    exit;
}


$conn->close();
?>
