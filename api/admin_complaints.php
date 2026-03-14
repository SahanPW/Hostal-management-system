<?php
// api/admin_complaints.php
require_once '../config/database.php';
require_once '../config/admin_session.php';

header('Content-Type: application/json');

$session = new AdminSessionManager();
$database = new Database();
$db = $database->getConnection();

$status = $_GET['status'] ?? 'all';

$query = "SELECT c.*, s.full_name 
          FROM complaints c 
          LEFT JOIN students s ON c.student_reg_no = s.reg_no";

if ($status !== 'all') {
    $query .= " WHERE c.status = :status";
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $db->prepare($query);
if ($status !== 'all') {
    $stmt->bindParam(':status', $status);
}
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $complaints]);
?>