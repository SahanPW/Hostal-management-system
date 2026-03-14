<?php
// api/admin_rooms.php
require_once '../config/database.php';
require_once '../config/admin_session.php';

header('Content-Type: application/json');

$session = new AdminSessionManager();
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM rooms ORDER BY floor, room_number";
$stmt = $db->prepare($query);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $rooms]);
?>