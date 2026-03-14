<?php
// auth/login.php

require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

$session = new SessionManager();
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!empty($data->reg_no) && !empty($data->password)) {
        $query = "SELECT * FROM students WHERE reg_no = :reg_no";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':reg_no', $data->reg_no);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($data->password, $row['password'])) {
                $session->setUserSession($row);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => 'home.php'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid password'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required'
        ]);
    }
}
?>