<?php
$session_path = __DIR__ . '/sessions';

if (!is_dir($session_path)) {
    @mkdir($session_path, 0755, true); 
}

ini_set('session.save_path', $session_path); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>