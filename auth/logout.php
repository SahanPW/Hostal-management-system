<?php
// auth/logout.php

require_once '../config/session.php';

$session = new SessionManager();
$session->logout();

header("Location: ../index.html");
exit();
?>