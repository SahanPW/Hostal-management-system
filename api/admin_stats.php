<?php
// api/admin_stats.php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

// Get statistics
$stats = [];

// Total students
$query = "SELECT COUNT(*) as count FROM students";
$stmt = $db->query($query);
$stats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total rooms
$query = "SELECT COUNT(*) as count FROM rooms";
$stmt = $db->query($query);
$stats['total_rooms'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Pending complaints
$query = "SELECT COUNT(*) as count FROM complaints WHERE status = 'pending'";
$stmt = $db->query($query);
$stats['pending_complaints'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Pending payments
$query = "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'pending'";
$stmt = $db->query($query);
$stats['pending_payments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

echo json_encode(['success' => true] + $stats);
?>