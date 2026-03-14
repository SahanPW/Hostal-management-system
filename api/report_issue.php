<?php
// api/report_issue.php

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->room_number) && !empty($data->issue)) {
    // Create maintenance issue
    $query = "INSERT INTO maintenance_issues (room_number, issue_description, reported_by, status, reported_at) 
              VALUES (:room_number, :issue, :reported_by, 'reported', NOW())";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':room_number', $data->room_number);
    $stmt->bindParam(':issue', $data->issue);
    $stmt->bindParam(':reported_by', $data->reported_by);
    
    if ($stmt->execute()) {
        // Update room status to maintenance if needed
        $updateQuery = "UPDATE rooms SET status = 'maintenance' WHERE room_number = :room_number";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':room_number', $data->room_number);
        $updateStmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Issue reported successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to report issue']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Room number and issue description required']);
}
?>