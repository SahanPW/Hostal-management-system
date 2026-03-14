<?php
// api/dashboard.php

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$session = new SessionManager();
$session->requireLogin();

$database = new Database();
$db = $database->getConnection();

$dashboardData = [];

// Get total students
$query = "SELECT COUNT(*) as total FROM students";
$stmt = $db->prepare($query);
$stmt->execute();
$dashboardData['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get total rooms
$query = "SELECT COUNT(*) as total FROM rooms";
$stmt = $db->prepare($query);
$stmt->execute();
$dashboardData['total_rooms'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get pending complaints
$query = "SELECT COUNT(*) as total FROM complaints WHERE status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$dashboardData['pending_complaints'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get pending payments
$query = "SELECT SUM(amount) as total FROM payments WHERE status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$dashboardData['pending_payments'] = $result['total'] ?: 0;

// Get recent students
$query = "SELECT reg_no, full_name, room_number,email FROM students ORDER BY id DESC LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$dashboardData['recent_students'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $dashboardData
]);
?>