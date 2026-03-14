<?php
// api/admin_dashboard.php
require_once '../config/database.php';
require_once '../config/admin_session.php';

header('Content-Type: application/json');

$session = new AdminSessionManager();
$database = new Database();
$db = $database->getConnection();

// Get dashboard statistics
$queries = [
    'total_students' => "SELECT COUNT(*) as count FROM students",
    'total_rooms' => "SELECT COUNT(*) as count FROM rooms",
    'pending_complaints' => "SELECT COUNT(*) as count FROM complaints WHERE status = 'pending'",
    'pending_payments' => "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'pending'"
];

$data = [];
foreach ($queries as $key => $query) {
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $data[$key] = $result['count'] ?? $result['total'] ?? 0;
}

echo json_encode(['success' => true, 'data' => $data]);
?>