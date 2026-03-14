<?php
// api/admin_students.php
require_once '../config/database.php';
require_once '../config/admin_session.php';

header('Content-Type: application/json');

$session = new AdminSessionManager();
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM students ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $students]);
?>