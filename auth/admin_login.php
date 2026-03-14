<?php
// auth/admin_login.php
require_once '../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Username and password required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Check admin user (using plain text for now - you should use password_hash in production)
$query = "SELECT username FROM admin_users WHERE username = :username AND password = :password";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $data['username']);
$stmt->bindParam(':password', $data['password']);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    session_start();
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $data['username'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Admin login successful',
        'redirect' => '../admin_dashboard.php'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
?>