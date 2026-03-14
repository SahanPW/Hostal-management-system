<?php
// api/get_payments.php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$query = "SELECT p.*, s.full_name FROM payments p 
          LEFT JOIN students s ON p.student_reg_no = s.reg_no 
          ORDER BY p.due_date DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $payments]);
?>