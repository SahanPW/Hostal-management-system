<?php
// api/update_complaint.php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['complaint_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$query = "UPDATE complaints SET status = :status WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':status', $data['status']);
$stmt->bindParam(':id', $data['complaint_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Complaint updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update']);
}
?>