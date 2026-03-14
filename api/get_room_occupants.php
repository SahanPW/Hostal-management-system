<?php
// api/get_room_occupants.php

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$room_number = isset($_GET['room_number']) ? $_GET['room_number'] : '';

if (!empty($room_number)) {
    $query = "SELECT reg_no, full_name, email, phone, faculty 
              FROM students 
              WHERE room_number = :room_number 
              ORDER BY full_name";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':room_number', $room_number);
    $stmt->execute();
    
    $occupants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'room_number' => $room_number,
        'occupants' => $occupants
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Room number is required']);
}
?>