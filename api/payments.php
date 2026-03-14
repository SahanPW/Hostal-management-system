<?php
// api/payments.php

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$session = new SessionManager();
$session->requireLogin();

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT p.*, s.full_name, s.room_number 
              FROM payments p
              JOIN students s ON p.student_reg_no = s.reg_no
              ORDER BY p.due_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $payments
    ]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update payment status
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "UPDATE payments SET status = 'paid', paid_date = CURDATE() WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->payment_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Payment marked as paid']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update payment']);
    }
}
?>