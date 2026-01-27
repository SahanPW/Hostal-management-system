<?php
// setup_rooms.php - Run this once to create 40 rooms

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Create 40 rooms with different statuses
for ($floor = 1; $floor <= 4; $floor++) {
    for ($room = 1; $room <= 10; $room++) {
        $room_number = ($floor * 100) + $room;
        
        // Random status for demo (in real system, this would be based on actual data)
        $statuses = ['available', 'occupied', 'maintenance'];
        $status = $statuses[array_rand($statuses)];
        
        // Random occupancy (0-4)
        $capacity = 4;
        $occupied = ($status == 'occupied') ? rand(1, 4) : 0;
        
        // Determine hostel block
        $blocks = ['A', 'B', 'C', 'D'];
        $block = $blocks[($floor - 1) % count($blocks)];
        
        $query = "INSERT INTO rooms (room_number, capacity, occupied, status, floor, hostel_block) 
                  VALUES (:room_number, :capacity, :occupied, :status, :floor, :hostel_block)
                  ON DUPLICATE KEY UPDATE 
                  capacity = :capacity, occupied = :occupied, status = :status, floor = :floor, hostel_block = :hostel_block";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':occupied', $occupied);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':floor', $floor);
        $stmt->bindParam(':hostel_block', $block);
        
        $stmt->execute();
    }
}

echo "✅ 40 rooms created/updated successfully!";
?>