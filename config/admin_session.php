<?php
// config/admin_session.php

class AdminSessionManager {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setAdminUser($admin_id, $username, $email, $role) {
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_email'] = $email;
        $_SESSION['admin_role'] = $role;
        $_SESSION['admin_logged_in'] = true;
    }

    public function isAdminLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    public function requireAdminLogin() {
        if (!$this->isAdminLoggedIn()) {
            header("Location: ../admin_login.html");
            exit;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../admin_login.html");
        exit;
    }

    public function getAdminId() {
        return $_SESSION['admin_id'] ?? null;
    }

    public function getAdminUsername() {
        return $_SESSION['admin_username'] ?? null;
    }

    public function getAdminRole() {
        return $_SESSION['admin_role'] ?? null;
    }

    public function isSuperAdmin() {
        return $this->getAdminRole() === 'admin';
    }
}
?>