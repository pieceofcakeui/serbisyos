<?php 
header('Content-Type: application/json');
require_once 'db_connection.php';
require_once 'chatbotEncryption.php';

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

function checkDatabaseConnection() {
    global $conn;
    if (!$conn || !$conn->ping()) {
        require_once 'db_connection.php';
    }
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$userId = $_SESSION['user_id'];
$encryption = new MessageEncryption();

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

define('MAX_IMAGE_SIZE', 5 * 1024 * 1024);
define('UPLOAD_DIR', __DIR__ . '/../uploads/chat_images/');


if (!file_exists(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        error_log("Failed to create upload directory: " . UPLOAD_DIR);
    }
}

try {
    checkDatabaseConnection();

    $conn->query("SET SESSION wait_timeout = 28800");
    $conn->query("SET SESSION interactive_timeout = 28800");
    $conn->query("SET SESSION innodb_lock_wait_timeout = 50");

    switch ($action) {
        case 'get_titles':
            getChatTitles($userId, $encryption);
            break;
            
        case 'get_conversation':
            $chatId = $_GET['chat_id'] ?? $input['chat_id'] ?? null;
            if (!$chatId) {
                throw new Exception('Chat ID is required');
            }
            getConversation($userId, $chatId, $encryption);
            break;
            
        case 'add_message':
            $chatId = $input['chat_id'] ?? null;
            $role = $input['role'] ?? '';
            $message = $input['message'] ?? '';
            $imageData = $input['image_data'] ?? null;
            $originalFileName = $input['original_filename'] ?? null;
            
            if (empty($role) || (empty($message) && empty($imageData))) {
                throw new Exception('Role and either message or image are required');
            }

            if ($imageData) {
                $imageSize = strlen(base64_decode($imageData));
                if ($imageSize > MAX_IMAGE_SIZE) {
                    throw new Exception('Image size exceeds 5MB limit');
                }
            }
            
            addMessage($userId, $chatId, $role, $message, $encryption, $imageData, $originalFileName);
            break;
            
        case 'delete_chat':
            $chatId = $input['chat_id'] ?? null;
            if (!$chatId) {
                throw new Exception('Chat ID is required for deletion');
            }
            deleteChat($userId, $chatId);
            break;
            
        case 'update_chat_timestamp':
            $chatId = $input['chat_id'] ?? null;
            if (!$chatId) {
                throw new Exception('Chat ID is required for update');
            }
            updateChatTimestamp($userId, $chatId);
            break;
            
        case 'pin_chat':
            $chatId = $input['chat_id'] ?? null;
            if (!$chatId) {
                throw new Exception('Chat ID is required to pin');
            }
            togglePinChat($userId, $chatId);
            break;

        case 'test_encryption':
            $test = $encryption->test();
            echo json_encode($test);
            break;
            
        default:
            throw new Exception('Invalid action: ' . $action);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => 'chat_error'
    ]);
}

function togglePinChat($userId, $chatId) {
    global $conn;
    checkDatabaseConnection();

    $stmt = $conn->prepare("
        UPDATE chatbot 
        SET is_pinned = NOT is_pinned 
        WHERE id = ? AND user_id = ?
    ");
    if (!$stmt) {
        throw new Exception('Database error: Unable to prepare pin toggle statement');
    }
    
    $stmt->bind_param("ii", $chatId, $userId);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        
        $stmt = $conn->prepare("SELECT is_pinned FROM chatbot WHERE id = ?");
        $stmt->bind_param("i", $chatId);
        $stmt->execute();
        $result = $stmt->get_result();
        $newStatus = $result->fetch_assoc()['is_pinned'];
        $stmt->close();
        
        echo json_encode(['success' => true, 'is_pinned' => $newStatus]);
    } else {
        $stmt->close();
        throw new Exception('Chat not found or access denied');
    }
}


function deleteChat($userId, $chatId) {
    global $conn;
    checkDatabaseConnection();

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("SELECT id FROM chatbot WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chatId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result->fetch_assoc()) {
            $stmt->close();
            throw new Exception('Chat not found or access denied');
        }
        $stmt->close();

        $stmt = $conn->prepare("SELECT attachment FROM chatbot_messages WHERE chat_id = ? AND attachment IS NOT NULL");
        $stmt->bind_param("i", $chatId);
        $stmt->execute();
        $result = $stmt->get_result();
        $attachments = [];
        while ($row = $result->fetch_assoc()) {
            $attachments[] = $row['attachment'];
        }
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM chatbot_messages WHERE chat_id = ?");
        $stmt->bind_param("i", $chatId);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("DELETE FROM chatbot WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $chatId, $userId);
        $stmt->execute();
        $deletedRows = $stmt->affected_rows;
        $stmt->close();
        
        if ($deletedRows == 0) {
            throw new Exception('Chat not found or already deleted.');
        }
        
        foreach ($attachments as $filename) {
            if (empty($filename)) continue;
            $filePath = UPLOAD_DIR . $filename;
            if (file_exists($filePath) && !is_dir($filePath)) {
                if (!unlink($filePath)) {
                    error_log("Failed to delete chat image: " . $filePath);
                }
            } else {
                 error_log("Chat image file not found for deletion: " . $filePath);
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Chat deleted successfully']);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function updateChatTimestamp($userId, $chatId) {
    global $conn;
    checkDatabaseConnection();

    $stmt = $conn->prepare("UPDATE chatbot SET updated_at = NOW() WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        throw new Exception('Database error: Unable to update timestamp');
    }
    
    $stmt->bind_param("ii", $chatId, $userId);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => true, 'message' => 'Timestamp updated']);
}


function getChatTitles($userId, $encryption) {
    global $conn;
    checkDatabaseConnection();

    $stmt = $conn->prepare("
        SELECT c.id, c.title, c.is_pinned, c.created_at, c.updated_at,
               (SELECT COUNT(*) FROM chatbot_messages WHERE chat_id = c.id) as message_count
        FROM chatbot c
        WHERE c.user_id = ?
        ORDER BY c.is_pinned DESC, c.updated_at DESC
        LIMIT 50
    ");
    
    if (!$stmt) {
        throw new Exception('Database error: Unable to fetch chat titles');
    }
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $chats = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['message_count'] == 0) {
            continue;
        }

        $decryptedTitle = '';
        if (!empty($row['title']) && $row['title'] !== 'New Chat') {
            try {
                $decryptedTitle = $encryption->decrypt($row['title']);
            } catch (Exception $e) {
                $decryptedTitle = 'Encrypted Chat';
            }
        }

        if (empty($decryptedTitle) || $decryptedTitle === 'New Chat' || $row['title'] === 'New Chat') {
            $msgStmt = $conn->prepare("
                SELECT message FROM chatbot_messages 
                WHERE chat_id = ? AND role = 'user' AND message IS NOT NULL
                ORDER BY created_at ASC LIMIT 1
            ");
            $msgStmt->bind_param("i", $row['id']);
            $msgStmt->execute();
            $msgResult = $msgStmt->get_result();
            $firstMessageData = $msgResult->fetch_assoc();

            if ($firstMessageData && $firstMessageData['message']) {
                try {
                    $decryptedMessage = $encryption->decrypt($firstMessageData['message']);
                    $decryptedTitle = truncateMessage($decryptedMessage, 30);
                } catch (Exception $e) {
                    $decryptedTitle = 'Chat...';
                }
            } else {
                $imgStmt = $conn->prepare("SELECT COUNT(*) as count FROM chatbot_messages WHERE chat_id = ? AND role = 'user' AND attachment IS NOT NULL ORDER BY created_at ASC LIMIT 1");
                $imgStmt->bind_param("i", $row['id']);
                $imgStmt->execute();
                $imgResult = $imgStmt->get_result()->fetch_assoc();
                if ($imgResult['count'] > 0) {
                    $decryptedTitle = 'Image Analysis';
                }
            }
            $msgStmt->close();
        }

        $row['title'] = $decryptedTitle ?: 'New Chat';
        $row['is_pinned'] = (int)$row['is_pinned'];
        $chats[] = $row;
    }
    
    $stmt->close();
    echo json_encode($chats);
}

function getConversation($userId, $chatId, $encryption) {
    global $conn;
    checkDatabaseConnection();

    $stmt = $conn->prepare("SELECT id FROM chatbot WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        throw new Exception('Database error: Unable to verify chat ownership');
    }
    
    $stmt->bind_param("ii", $chatId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result->fetch_assoc()) {
        $stmt->close();
        throw new Exception('Chat not found or access denied');
    }
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT id, role, message, attachment, created_at 
        FROM chatbot_messages 
        WHERE chat_id = ? 
        ORDER BY created_at ASC
    ");
    
    if (!$stmt) {
        throw new Exception('Database error: Unable to fetch conversation');
    }
    
    $stmt->bind_param("i", $chatId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messageParts = [];
        
        if (!empty($row['message'])) {
            $decryptedMessage = '...';
            try {
                $decryptedMessage = $encryption->decrypt($row['message']);
            } catch (Exception $e) {
                $decryptedMessage = '[Error decrypting message]';
                error_log("Decryption error for msg " . $row['id'] . ": " . $e->getMessage());
            }
            $messageParts[] = ['text' => $decryptedMessage];
        }
        
        if (!empty($row['attachment'])) {
            $filePath = UPLOAD_DIR . $row['attachment'];
            if (file_exists($filePath)) {
                $mimeType = mime_content_type($filePath);
                if (!$mimeType) {
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    switch ($extension) {
                        case 'jpg':
                        case 'jpeg':
                            $mimeType = 'image/jpeg';
                            break;
                        case 'png':
                            $mimeType = 'image/png';
                            break;
                        case 'webp':
                            $mimeType = 'image/webp';
                            break;
                        default:
                            $mimeType = 'image/jpeg';
                    }
                }
                
                $imageData = base64_encode(file_get_contents($filePath));
                $messageParts[] = [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => $imageData
                    ]
                ];
            } else {
                error_log("Image file not found: " . $filePath);
                if (empty($row['message'])) { 
                    $messageParts[] = ['text' => '[Image not found]'];
                }
            }
        }

        if(empty($messageParts)) {
            continue;
        }

        $database_role = $row['role'];
        $api_role = ($database_role === 'bot') ? 'model' : $database_role;

        $messages[] = [
            'role' => $api_role,
            'parts' => $messageParts
        ];
    }
    
    $stmt->close();
    echo json_encode($messages);
}

function saveImageFile($base64Data, $originalFileName) {
    if (empty($originalFileName)) {
        $originalFileName = 'uploaded_image.jpg';
    }

    $fileInfo = pathinfo($originalFileName);
    $extension = isset($fileInfo['extension']) ? strtolower($fileInfo['extension']) : 'jpg';
    
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
        $extension = 'jpg';
    }

    $safeFilename = preg_replace('/[^a-zA-Z0-9\-_]/', '', $fileInfo['filename']);
    if (empty($safeFilename)) {
        $safeFilename = 'image';
    }
    
    $uniqueFilename = $safeFilename . '_' . uniqid() . '.' . $extension;
    
    $filePath = UPLOAD_DIR . $uniqueFilename;
    $imageData = base64_decode($base64Data);
    
    if ($imageData === false) {
        throw new Exception('Failed to decode base64 image data');
    }
    
    if (file_put_contents($filePath, $imageData) === false) {
        throw new Exception('Failed to save image file to ' . $filePath);
    }

    error_log("Image saved successfully: " . $filePath);
    
    return $uniqueFilename;
}

function addMessage($userId, $chatId, $role, $message, $encryption, $imageData = null, $originalFileName = null) {
    global $conn;
    checkDatabaseConnection();
    
    $conn->begin_transaction();
    
    try {
        if ($chatId) {
            $stmt = $conn->prepare("SELECT id FROM chatbot WHERE id = ? AND user_id = ?");
            if (!$stmt) {
                throw new Exception('Database error: Unable to verify chat');
            }
            
            $stmt->bind_param("ii", $chatId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if (!$result->fetch_assoc()) {
                $stmt->close();
                throw new Exception('Chat not found or access denied');
            }
            $stmt->close();

            $stmt = $conn->prepare("UPDATE chatbot SET updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $chatId);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("
                INSERT INTO chatbot (user_id, title, created_at, updated_at)
                VALUES (?, 'New Chat', NOW(), NOW())
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $chatId = $conn->insert_id;
            $stmt->close();
        }

        $encryptedMessage = !empty($message) ? $encryption->encrypt($message) : null;
        $attachment = null;

        if ($imageData) {
             if (empty($originalFileName)) {
                $originalFileName = 'chat-image.jpg';
            }
            try {
                $attachment = saveImageFile($imageData, $originalFileName);
            } catch (Exception $e) {
                throw new Exception('Failed to process image: ' . $e->getMessage());
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO chatbot_messages (chat_id, role, message, attachment, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        if (!$stmt) {
            throw new Exception('Database error: Unable to save message');
        }
        
        $stmt->bind_param("isss", $chatId, $role, $encryptedMessage, $attachment);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error: Unable to save message data - " . $stmt->error);
        }
        $messageId = $conn->insert_id;
        $stmt->close();

        if ($role === 'user' && (!empty($message) || !empty($imageData))) {
            $stmt = $conn->prepare("
                SELECT COUNT(*) as count FROM chatbot_messages 
                WHERE chat_id = ? AND role = 'user'
            ");
            $stmt->bind_param("i", $chatId);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            $stmt->close();
            
            if ($count == 1) {
                if (!empty($message)) {
                    $titleText = $message;
                } else {
                    $titleText = 'Image Analysis';
                }
                
                $title = truncateMessage($titleText, 30);
                $encryptedTitle = $encryption->encrypt($title);
                
                $stmt = $conn->prepare("UPDATE chatbot SET title = ? WHERE id = ?");
                $stmt->bind_param("si", $encryptedTitle, $chatId);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'message' => 'Message saved successfully'
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function truncateMessage($message, $length) {
    $message = strip_tags($message);
    $message = preg_replace('/\s+/', ' ', trim($message));
    
    if (strlen($message) > $length) {
        $message = substr($message, 0, $length) . '...';
    }
    
    return $message ?: 'New Chat';
}
?>