<?php
// api/update_room.php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['room_number']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$query = "UPDATE rooms SET status = :status WHERE room_number = :room_number";
$stmt = $db->prepare($query);
$stmt->bindParam(':status', $data['status']);
$stmt->bindParam(':room_number', $data['room_number']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update']);
}
?>