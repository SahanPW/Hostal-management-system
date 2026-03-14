<?php
// config/session.php

class SessionManager {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setUserSession($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['reg_no'] = $userData['reg_no'];
        $_SESSION['full_name'] = $userData['full_name'];
        $_SESSION['email'] = $userData['email'];
        $_SESSION['room_number'] = $userData['room_number'];
        $_SESSION['logged_in'] = true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header("Location: index.html");
            exit();
        }
    }
}
?>