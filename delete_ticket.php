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

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['ticket_id'])) {
    $ticket_id = intval($data['ticket_id']);

    // بدء المعاملة
    $conn->begin_transaction();

    try {
        // حذف السجلات المرتبطة بالبلاغ من الجداول الأخرى
        $conn->query("DELETE FROM notifications WHERE ticket_id = $ticket_id");
        $conn->query("DELETE FROM ticket_assignments WHERE ticket_id = $ticket_id");
        $conn->query("DELETE FROM report_status WHERE report_id = $ticket_id"); // حذف السجلات من جدول report_status
        $conn->query("DELETE FROM technician_replies WHERE ticket_id = $ticket_id"); // حذف السجلات من جدول technician_replies
        $conn->query("DELETE FROM ticket_assignments_history WHERE ticket_id = $ticket_id"); 
        // حذف البلاغ من جدول التذاكر
        $sql = "DELETE FROM tickets WHERE ticket_id = $ticket_id";
        if ($conn->query($sql) === TRUE) {
            // تأكيد المعاملة
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Ticket and related records deleted successfully']);
        } else {
            // إلغاء المعاملة في حال حدوث خطأ
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error deleting ticket: ' . $conn->error]);
        }
    } catch (Exception $e) {
        // إلغاء المعاملة في حال حدوث استثناء
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error deleting ticket: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid ticket ID']);
}

$conn->close();
?>
