<?php
// api/students.php

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$session = new SessionManager();
$session->requireLogin();

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT reg_no, full_name, email, room_number, phone, faculty, created_at 
              FROM students ORDER BY id DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $students
    ]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new student
    $data = json_decode(file_get_contents("php://input"));

    $query = "INSERT INTO students (reg_no, full_name, email, room_number, phone, faculty, password) 
              VALUES (:reg_no, :full_name, :email, :room_number, :phone, :faculty, :password)";

    $stmt = $db->prepare($query);

    // Plain text password (NOT encrypted)
    $plain_password = 'default123';

    $stmt->bindParam(':reg_no', $data->reg_no);
    $stmt->bindParam(':full_name', $data->full_name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':room_number', $data->room_number);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':faculty', $data->faculty);
    $stmt->bindParam(':password', $plain_password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Student added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add student']);
    }
}

?>