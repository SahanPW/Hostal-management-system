<?php
// setup_admin.php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Set admin password (admin123)
$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);

$query = "UPDATE admin_users SET password = :password WHERE username = 'admin'";
$stmt = $db->prepare($query);
$stmt->bindParam(':password', $hashed_password);

if ($stmt->execute()) {
    echo "✅ Admin password has been set to: <strong>admin123</strong><br>";
    echo "Username: <strong>admin</strong><br>";
    echo "<a href='admin_login.html'>Go to Admin Login</a>";
} else {
    echo "❌ Error setting password";
}
?>