<?php
// api/request_room.php

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->room_number) && !empty($data->student_reg_no)) {
    // Check if room is available
    $checkQuery = "SELECT status, occupied, capacity FROM rooms WHERE room_number = :room_number";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':room_number', $data->room_number);
    $checkStmt->execute();
    
    $room = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        echo json_encode(['success' => false, 'message' => 'Room not found']);
        exit();
    }
    
    if ($room['status'] !== 'available') {
        echo json_encode(['success' => false, 'message' => 'Room is not available']);
        exit();
    }
    
    if ($room['occupied'] >= $room['capacity']) {
        echo json_encode(['success' => false, 'message' => 'Room is already full']);
        exit();
    }
    
    // Create room request
    $query = "INSERT INTO room_requests (student_reg_no, requested_room, status, requested_at) 
              VALUES (:student_reg_no, :requested_room, 'pending', NOW())";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':student_reg_no', $data->student_reg_no);
    $stmt->bindParam(':requested_room', $data->room_number);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room request submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Room number and student registration required']);
}
?>