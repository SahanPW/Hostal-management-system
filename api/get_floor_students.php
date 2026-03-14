<?php
// api/get_floor_students.php
require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$session = new SessionManager();
$database = new Database();
$db = $database->getConnection();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['floor'])) {
    echo json_encode(['success' => false, 'message' => 'Floor number is required']);
    exit;
}

$floor = $data['floor'];

// Get students on the specified floor
$query = "SELECT reg_no, full_name, email, phone, room_number, faculty 
          FROM students 
          WHERE floor = :floor 
          ORDER BY room_number, full_name";

$stmt = $db->prepare($query);
$stmt->bindParam(':floor', $floor);
$stmt->execute();

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'floor' => $floor,
    'students' => $students,
    'count' => count($students)
]);
?>