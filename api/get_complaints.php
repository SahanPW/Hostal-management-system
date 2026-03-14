<?php
// api/get_complaints.php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$status = $_GET['status'] ?? '';

$query = "SELECT * FROM complaints";
if ($status && $status !== 'all') {
    $query .= " WHERE status = :status";
}
$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);
if ($status && $status !== 'all') {
    $stmt->bindParam(':status', $status);
}
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $complaints]);
?>