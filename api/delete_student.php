<?php
// api/delete_student.php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['reg_no'])) {
    echo json_encode(['success' => false, 'message' => 'Registration number required']);
    exit;
}

// First delete related records
$queries = [
    "DELETE FROM complaints WHERE student_reg_no = :reg_no",
    "DELETE FROM payments WHERE student_reg_no = :reg_no",
    "DELETE FROM room_requests WHERE student_reg_no = :reg_no",
    "DELETE FROM students WHERE reg_no = :reg_no"
];

try {
    foreach ($queries as $query) {
        $stmt = $db->prepare($query);
        $stmt->bindParam(':reg_no', $data['reg_no']);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => 'Student deleted']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete']);
}
?>