<?php
require_once 'db_connection.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['session_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad Request']);
    exit();
}

$user_id = $_SESSION['user_id'];
$session_id = $_POST['session_id'];

try {
    $is_current = false;
    if (isset($_SESSION['session_id']) && $_SESSION['session_id'] === $session_id) {
        $is_current = true;
    }

    $stmt = $conn->prepare("DELETE FROM active_sessions WHERE user_id = ? AND session_id = ?");
    $stmt->bind_param("is", $user_id, $session_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete session");
    }

    if ($is_current) {
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        
        session_destroy();
    }

    echo json_encode([
        'success' => true,
        'is_current_session' => $is_current
    ]);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}
?>