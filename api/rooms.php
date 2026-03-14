<?php
// api/rooms.php - Updated with floor filtering

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$session = new SessionManager();
$session->requireLogin();

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $floor = isset($_GET['floor']) ? $_GET['floor'] : null;
    
    $query = "SELECT r.*, 
              (SELECT GROUP_CONCAT(s.full_name) 
               FROM students s 
               WHERE s.room_number = r.room_number) as occupants
              FROM rooms r";
    
    if ($floor) {
        $query .= " WHERE r.floor = :floor";
    }
    
    $query .= " ORDER BY r.floor, r.room_number";
    
    $stmt = $db->prepare($query);
    if ($floor) {
        $stmt->bindParam(':floor', $floor);
    }
    $stmt->execute();
    
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate statistics
    $totalRooms = count($rooms);
    $availableRooms = 0;
    $occupiedRooms = 0;
    $vacantBeds = 0;
    
    foreach ($rooms as $room) {
        if ($room['status'] === 'available' && $room['occupied'] < $room['capacity']) {
            $availableRooms++;
        }
        if ($room['status'] === 'occupied') {
            $occupiedRooms++;
        }
        $vacantBeds += ($room['capacity'] - $room['occupied']);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $rooms,
        'statistics' => [
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRooms,
            'occupied_rooms' => $occupiedRooms,
            'vacant_beds' => $vacantBeds
        ]
    ]);
}
?>