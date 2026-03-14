<?php
// auth/register.php

require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    // Validation
    if (empty($data->reg_no) || empty($data->password) || empty($data->full_name) || empty($data->email) || empty($data->floor)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if (strlen($data->password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    // Check if user already exists
    $checkQuery = "SELECT id FROM students WHERE reg_no = :reg_no OR email = :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':reg_no', $data->reg_no);
    $checkStmt->bindParam(':email', $data->email);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Registration number or email already exists']);
        exit;
    }
    
    // Insert new student
    $query = "INSERT INTO students (reg_no, full_name, email, room_number, floor, password) 
              VALUES (:reg_no, :full_name, :email, :room_number, :floor, :password)";
    
    $stmt = $db->prepare($query);
    
    $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
    
    $stmt->bindParam(':reg_no', $data->reg_no);
    $stmt->bindParam(':full_name', $data->full_name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':room_number', $data->room);
    $stmt->bindParam(':floor', $data->floor);
    $stmt->bindParam(':password', $hashed_password);
    
    if ($stmt->execute()) {
        // Update room occupancy if room number is provided
        if (!empty($data->room)) {
            $roomQuery = "UPDATE rooms SET occupied = occupied + 1, floor = :floor WHERE room_number = :room_number";
            $roomStmt = $db->prepare($roomQuery);
            $roomStmt->bindParam(':room_number', $data->room);
            $roomStmt->bindParam(':floor', $data->floor);
            $roomStmt->execute();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'redirect' => 'index.html'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed'
        ]);
    }
}
?>