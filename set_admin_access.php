<?php
session_start();

$_SESSION['admin_access_allowed'] = true;
$_SESSION['admin_access_time'] = time();

echo json_encode(['success' => true]);
