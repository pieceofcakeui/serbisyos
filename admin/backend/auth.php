<?php
include 'base-path.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    $_SESSION['flash_message'] = [
        'type' => 'info',
        'title' => 'Session Expired',
        'body' => 'Your session has expired. Please log in again.'
     ];
  header("Location: ./login.php");
    exit();
}

if (isset($REQUIRED_ROLE)) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $REQUIRED_ROLE) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'title' => 'Access Denied',
            'body' => 'You do not have permission to access this page.'
        ];
       header("Location: ./login.php");
        exit();
    }
}

return;
?>