<?php
session_start();
include 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$type = $_POST['type'] ?? '';
$id = $_POST['id'] ?? 0;

if ($action === 'mark_all') {
    $sql1 = "UPDATE shop_applications SET is_read_admin = 1 WHERE is_read_admin = 0";
    $sql2 = "UPDATE verification_submissions SET is_read_admin = 1 WHERE is_read_admin = 0";

    $conn->query($sql1);
    $conn->query($sql2);

    echo json_encode(['success' => true]);
    exit;
}

if ($type && $id > 0) {
    $table = '';
    if ($type === 'shop') {
        $table = 'shop_applications';
    } elseif ($type === 'verification') {
        $table = 'verification_submissions';
    }

    if ($table) {
        $stmt = $conn->prepare("UPDATE $table SET is_read_admin = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid Request']);
?>