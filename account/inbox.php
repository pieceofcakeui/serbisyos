<?php
require_once '../functions/auth.php';
include 'inbox-backend/config.php';
include 'backend/db_connection.php';

$user_id = $_SESSION['user_id'];
$other_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if ($user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shop = $shopResult->fetch_assoc();

    if ($shop) {
        $shop_id = $shop['id'];

        $emergencyQuery = $conn->prepare("
            SELECT er.id, u.fullname, er.issue_description, er.created_at 
            FROM emergency_requests er
            JOIN users u ON er.user_id = u.id
            WHERE er.shop_id = ? AND er.seen_emergency_request = 0
            ORDER BY er.created_at DESC LIMIT 1
        ");
        $emergencyQuery->bind_param("i", $shop_id);
        $emergencyQuery->execute();
        $emergencyResult = $emergencyQuery->get_result();
        $emergency = $emergencyResult->fetch_assoc();

        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
        }
    }
}

function decrypt_data($data)
{
    if (empty($data))
        return '';
    return openssl_decrypt($data, ENCRYPT_METHOD, SECRET_KEY, 0, SECRET_IV);
}

function getProfilePicture($user)
{
    $is_shop = !empty($user['shop_name']);

    if ($is_shop) {
        $logo = !empty($user['shop_logo']) ? $user['shop_logo'] : '';

        if (!empty($logo) && $logo !== 'logo.jpg') {
            $logo_path = 'uploads/shop_logo/' . $logo;
            if (file_exists($logo_path)) {
                return $logo_path;
            }
        }
        return 'uploads/shop_logo/logo.jpg';
    } else {
        $picture = !empty($user['profile_picture']) ? $user['profile_picture'] : '';

        if (!empty($picture) && $picture !== 'profile-user.png') {
            $picture_path = '../assets/img/profile/' . $picture;
            if (file_exists($picture_path)) {
                return $picture_path;
            }
        }
        return '../assets/img/profile/profile-user.png';
    }
}

function getMessageReaction($message_id, $user_id)
{
    global $conn;
    $query = "SELECT reaction_type FROM reactions WHERE message_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $message_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['reaction_type'];
    }
    return null;
}

function getReactionEmoji($type)
{
    $emojis = [
        'like' => '👍',
        'heart' => '❤️',
        'haha' => '😄',
        'sad' => '😢',
        'angry' => '😠',
        'wow' => '😲'
    ];
    return $emojis[$type] ?? '';
}

$unread_count = 0;
$sql_unread = "SELECT COUNT(*) AS unread_count FROM messages WHERE receiver_id = ? AND is_read = 0 AND deleted_by_receiver = 0"; // <-- Binago dito
$stmt_unread = $conn->prepare($sql_unread);
$stmt_unread->bind_param("i", $user_id);
$stmt_unread->execute();
$result_unread = $stmt_unread->get_result();
if ($result_unread && $row_unread = $result_unread->fetch_assoc()) {
    $unread_count = $row_unread['unread_count'];
}
$stmt_unread->close();

$sql_conversations = "
    SELECT 
        u.id AS user_id, 
        u.fullname, 
        u.profile_picture, 
        u.profile_type,
        s.shop_name, 
        s.shop_logo, 
        latest_msg.message, 
        latest_msg.attachment, 
        latest_msg.created_at, 
        latest_msg.is_read, 
        latest_msg.sender_id,
        (SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND sender_id = u.id AND is_read = 0 AND deleted_by_receiver = 0) AS unread_count
    FROM (
        SELECT 
            CASE 
                WHEN sender_id = ? THEN receiver_id 
                ELSE sender_id 
            END AS other_user_id,
            MAX(id) as max_id
        FROM messages 
        WHERE (sender_id = ? AND deleted_by_sender = 0) OR (receiver_id = ? AND deleted_by_receiver = 0)
        GROUP BY other_user_id
    ) conv
    JOIN messages latest_msg ON conv.max_id = latest_msg.id
    JOIN users u ON u.id = conv.other_user_id
    LEFT JOIN shop_applications s ON u.id = s.user_id 
    GROUP BY u.id
    ORDER BY latest_msg.created_at DESC
";

$stmt_conv = $conn->prepare($sql_conversations);
$stmt_conv->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt_conv->execute();
$conversations = $stmt_conv->get_result();

$messages = null;
$other_user = null;
if ($other_id) {
    $mark_read_sql = "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ? AND deleted_by_receiver = 0";
    $mark_read_stmt = $conn->prepare($mark_read_sql);
    $mark_read_stmt->bind_param("ii", $user_id, $other_id);
    $mark_read_stmt->execute();
    $mark_read_stmt->close();

    $sql_messages = "
        SELECT m.*, u.fullname, u.profile_picture, u.profile_type, s.shop_name, s.shop_logo 
        FROM messages m 
        JOIN users u ON m.sender_id = u.id 
        LEFT JOIN shop_applications s ON u.id = s.user_id 
        WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)) AND m.is_deleted = 0
        ORDER BY m.created_at ASC
    ";

    $stmt_msg = $conn->prepare($sql_messages);
    $stmt_msg->bind_param("iiii", $user_id, $other_id, $other_id, $user_id);
    $stmt_msg->execute();
    $messages = $stmt_msg->get_result();

    $user_sql = "SELECT u.*, s.shop_name, s.shop_logo FROM users u LEFT JOIN shop_applications s ON u.id = s.user_id WHERE u.id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $other_id);
    $user_stmt->execute();
    $other_user = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="../assets/css/users/messages.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">


   <style>
        #autoMessagesModal .modal-dialog {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        #autoMessagesModal .modal-content {
            max-height: calc(100vh - 2rem);

            display: flex;
            flex-direction: column;
        }

        #autoMessagesModal .modal-header {
            flex-shrink: 0;
        }

        #autoMessagesModal .modal-body {
            flex-grow: 1;
            overflow-y: auto;
        }

        #autoMessagesModal .modal-footer {
            flex-shrink: 0;
        }
    </style>
    </head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/modal-inbox.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <main id="main-content" class="main-content">
        <div class="main-container">
            <div class="inbox-container" id="inboxContainer">
                <?php
                $profile_type = '';
                $stmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $profile_type = $row['profile_type'];
                }
                ?>

                <div class="inbox-header d-flex align-items-center justify-content-between">
                    <div class="inbox-title d-flex align-items-center">
                        <h1 class="mb-0 me-2">Inbox</h1>
                        <?php if ($profile_type === 'owner'): ?>
                            <div class="inbox-settings ms-2">
                                <button class="btn p-0 border-0 bg-transparent" data-bs-toggle="modal"
                                    data-bs-target="#settingsModal" title="Settings">
                                    <i class="fas fa-cog fa-lg"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search messages"
                            oninput="filterConversations()">
                    </div>
                </div>

                <ul class="message-list" id="messageList">
                    <?php
                    if ($conversations->num_rows > 0):
                        while ($conversation = $conversations->fetch_assoc()):
                            $message = decrypt_data($conversation['message']);
                            $is_read = $conversation['is_read'];
                            $is_sender = $conversation['sender_id'] == $user_id;
                            $unread_count = $conversation['unread_count'];
                            $display_name = $conversation['shop_name'] ?? $conversation['fullname'];
                            $profile_picture = getProfilePicture($conversation);

                            $is_shop = !empty($conversation['shop_name']);

                            $actual_profile_picture = '';
                            if ($is_shop) {
                                if (
                                    !empty($conversation['shop_logo']) &&
                                    $conversation['shop_logo'] !== 'logo.jpg' &&
                                    file_exists('uploads/shop_logo/' . $conversation['shop_logo'])
                                ) {
                                    $actual_profile_picture = 'uploads/shop_logo/' . $conversation['shop_logo'];
                                } else {
                                    $actual_profile_picture = 'uploads/shop_logo/logo.jpg';
                                }
                            } else {
                                if (
                                    !empty($conversation['profile_picture']) &&
                                    $conversation['profile_picture'] !== 'profile-user.png' &&
                                    file_exists('../assets/img/profile/' . $conversation['profile_picture'])
                                ) {
                                    $actual_profile_picture = '../assets/img/profile/' . $conversation['profile_picture'];
                                } else {
                                    $actual_profile_picture = '../assets/img/profile/profile-user.png';
                                }
                            }
                            ?>
                            <li class="message-item <?= $unread_count > 0 ? 'unread' : '' ?>" onclick="openChat(this)"
                                data-user-id="<?= $conversation['user_id'] ?>"
                                data-name="<?= htmlspecialchars($conversation['fullname'], ENT_QUOTES, 'UTF-8') ?>"
                                data-display-name="<?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') ?>"
                                data-profile-picture="<?= htmlspecialchars($actual_profile_picture, ENT_QUOTES, 'UTF-8') ?>"
                                data-profile-type="<?= htmlspecialchars($conversation['profile_type'], ENT_QUOTES, 'UTF-8') ?>"
                                data-search-name="<?= strtolower($display_name) ?>"
                                data-search-message="<?= strtolower(htmlspecialchars(mb_strimwidth($message, 0, 50, '...'))) ?>">

                                <img src="<?= $actual_profile_picture ?>" alt="Profile" class="message-avatar-img"
                                    onerror="this.src='<?= $is_shop ? 'uploads/shop_logo/logo.jpg' : '../assets/img/profile/profile-user.png' ?>'">

                                <div class="message-content">
                                    <div class="message-header">
                                        <div class="message-sender"><?= htmlspecialchars($display_name) ?></div>
                                        <div class="message-time"><?= date('h:i A', strtotime($conversation['created_at'])) ?>
                                        </div>
                                    </div>
                                    <div class="message-subject">
                                        <?= $is_sender ? 'You: ' : '' ?>        <?= htmlspecialchars(mb_strimwidth($message, 0, 50, '...')) ?>
                                    </div>
                                    <?php if ($unread_count > 0): ?>
                                        <span class="unread-badge"><?= $unread_count ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-messages-placeholder">
                            <i class="fas fa-comments"></i>
                            <h2>No Messages Yet</h2>
                            <p>Your inbox is empty. Start a new conversation!</p>
                        </div>
                    <?php endif; ?>

                </ul>
            </div>

            <div class="chat-panel hidden" id="chatPanel">
                <div class="chat-header">
                    <div class="chat-user-info">
                        <button class="close-chat" onclick="closeChat()">
                            <i class="fas fa-arrow-left"></i>
                        </button>

                        <?php if ($other_id): ?>
                            <img src="<?= getProfilePicture($other_user) ?>" alt="Profile" class="chat-avatar-img"
                                id="chatAvatarImg">
                        <?php else: ?>
                            <img src="" alt="Profile" class="chat-avatar-img" id="chatAvatarImg" style="display: none;">
                        <?php endif; ?>

                        <div class="chat-user-details">
                            <h3 id="chatUserName">
                                <?= $other_id ? htmlspecialchars($other_user['shop_name'] ?? $other_user['fullname']) : '' ?>
                            </h3>
                            <p id="chatUserStatus">Active now</p>
                        </div>
                    </div>
                    <div class="chat-settings">
                        <button class="chat-settings-btn" onclick="toggleSettingsMenu()">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="chat-settings-menu" id="chatSettingsMenu">
                            <div class="chat-settings-item delete" onclick="showDeleteConversationModal()">
                                <i class="fas fa-trash"></i> Delete Conversation
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <?php if ($messages && $messages->num_rows > 0): ?>
                        <?php while ($message = $messages->fetch_assoc()):
                            $message_content = decrypt_data($message['message']);
                            $is_sender = $message['sender_id'] == $user_id;
                            $profile_picture = getProfilePicture($message);
                            $attachment = !empty($message['attachment']) ? decrypt_data($message['attachment']) : '';
                            $reaction = $message['reaction'];
                            ?>
                            <div class="message-bubble <?= $is_sender ? 'sent' : 'received' ?>"
                                data-message-id="<?= $message['id'] ?>" onclick="showReactionPicker(<?= $message['id'] ?>)">
                                <?php if (!empty($attachment)): ?>
                                    <div class="image-content" onclick="event.stopPropagation();">
                                        <img src="<?= $attachment ?>" alt="Shared image"
                                            onclick="viewImageFullScreen('<?= $attachment ?>')"
                                            style="max-width: 200px; max-height: 200px; border-radius: 8px; cursor: pointer;">
                                    </div>
                                <?php endif; ?>
                                <div class="text-content"><?= htmlspecialchars($message_content) ?></div>
                                <div class="time"><?= date('h:i A', strtotime($message['created_at'])) ?></div>
                                <?php if ($reaction): ?>
                                    <div class="reaction-container">
                                        <div class="reaction">
                                            <?= getReactionEmoji($reaction) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-messages">No messages yet</div>
                    <?php endif; ?>
                </div>

                <div class="chat-input-container" id="chatInputContainer">
                    <div class="chat-input-wrapper">
                        <input type="file" id="fileAttachment" onchange="handleFileSelect(event)" accept="image/*"
                            style="display: none;">
                        <button class="chat-action-button" onclick="document.getElementById('fileAttachment').click()"
                            title="Attach file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <textarea class="chat-input" id="chatInput" placeholder="Type a message..." rows="1"
                            onkeydown="handleKeyDown(event)" oninput="autoResize(this)"></textarea>
                        <button class="chat-action-button send-button" onclick="sendMessage()" id="sendButton">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>

        <div class="image-viewer" id="imageViewer">
            <div class="image-viewer-controls">
                <button class="image-viewer-btn" onclick="downloadImage()" title="Download">
                    <i class="fas fa-download"></i>
                </button>
                <button class="image-viewer-btn" onclick="closeImageViewer()" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <img id="fullScreenImage" src="" alt="Full screen image">
        </div>

        <div class="reaction-picker-backdrop" id="reactionPickerBackdrop"></div>
        <div class="reaction-picker" id="reactionPicker">
            <span class="reaction-option" onclick="addReaction('like')">👍</span>
            <span class="reaction-option" onclick="addReaction('heart')">❤️</span>
            <span class="reaction-option" onclick="addReaction('haha')">😄</span>
            <span class="reaction-option" onclick="addReaction('sad')">😢</span>
            <span class="reaction-option" onclick="addReaction('angry')">😠</span>
            <span class="reaction-option" onclick="addReaction('wow')">😲</span>
        </div>


        <?php

        $auto_messages = include 'inbox-backend/get_auto_messages.php';
        ?>

        <!-- Auto Messages Modal -->
       <div class="modal fade" id="autoMessagesModal" tabindex="-1" aria-labelledby="autoMessagesModalLabel" aria-hidden="true">
            
            <div class="modal-dialog modal-dialog-centered">
                
                <div class="modal-content rounded-4 shadow-sm" style="border: 1px solid #dee2e6;">

                    <div class="modal-header">
                        <h5 class="modal-title" id="autoMessagesModalLabel">Automated Messages Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="autoMessagesForm" action="inbox-backend/save_auto_messages.php" method="POST">

                        <div class="modal-body">

                            <div class="mb-4">
                                <label for="welcomeMessage" class="form-label fw-semibold">Welcome Message</label>
                                <textarea class="form-control" name="welcome_message" id="welcomeMessage" rows="3" placeholder="Hi! Welcome to our shop. How can we help you today?" required><?php echo htmlspecialchars($auto_messages['welcome_message']); ?></textarea>
                                <div class="form-text">This message is sent when a customer starts a chat.</div>
                            </div>

                            <label class="form-label fw-semibold">Quick Reply Options
                                <span class="badge bg-primary ms-2" id="filledCountBadge">
                                    <?php echo $auto_messages['quick_replies']['total_filled']; ?>/5 filled
                                </span>
                            </label>

                            <div class="accordion" id="quickReplyAccordion">
                                <?php
                                $accordionItems = ['One', 'Two', 'Three', 'Four', 'Five'];
                                for ($i = 0; $i < 5; $i++):
                                    $option_num = $i + 1;
                                    $option = $auto_messages['quick_replies']['options'][$i] ?? [
                                        'option_number' => $option_num,
                                        'label' => '',
                                        'response' => '',
                                        'is_filled' => false
                                    ];
                                    $is_first = ($i === 0);
                                ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?php echo $is_first ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $accordionItems[$i]; ?>" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $accordionItems[$i]; ?>">
                                                <span>Option <?php echo $option_num; ?></span>
                                                <span class="badge <?php echo $option['is_filled'] ? 'bg-success' : 'bg-secondary'; ?> ms-2" id="statusBadge<?php echo $option_num; ?>">
                                                    <?php echo $option['is_filled'] ? 'Filled' : 'Empty'; ?>
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo $accordionItems[$i]; ?>" class="accordion-collapse collapse <?php echo $is_first ? 'show' : ''; ?>" data-bs-parent="#quickReplyAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <label for="option<?php echo $option_num; ?>Label" class="form-label">Button Label</label>
                                                    <input type="text" class="form-control option-input" name="option<?php echo $option_num; ?>_label" id="option<?php echo $option_num; ?>Label" data-option="<?php echo $option_num; ?>"
                                                        value="<?php echo htmlspecialchars($option['label']); ?>"
                                                        placeholder="e.g., <?php
                                                                            $placeholders = ['Book an appointment', 'Ask about pricing', 'Check availability', 'Ask another question', 'Contact support'];
                                                                            echo $placeholders[$i];
                                                                            ?>">
                                                </div>
                                                <div>
                                                    <label for="option<?php echo $option_num; ?>Response" class="form-label">Automated Response</label>
                                                    <textarea class="form-control option-input" name="option<?php echo $option_num; ?>_response" id="option<?php echo $option_num; ?>Response" rows="2" data-option="<?php echo $option_num; ?>"
                                                        placeholder="e.g., <?php
                                                                            $response_placeholders = [
                                                                                'Let us know your preferred date/time...',
                                                                                'Please share details about what you need...',
                                                                                'We\'re open Monday-Friday from 9am-6pm...',
                                                                                'Sure! Please type your question below...',
                                                                                'Our support team will contact you shortly.'
                                                                            ];
                                                                            echo $response_placeholders[$i];
                                                                            ?>"><?php echo htmlspecialchars($option['response']); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveSettingsBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span>Save Settings</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
  

<div class="modal result-modal" id="resultModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="result-icon success d-none" id="successIcon">
                            <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path class="checkmark-path" d="M14 27l7 7 16-16" stroke="currentColor" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="result-icon error d-none" id="errorIcon">
                            <svg viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path class="cross-path" d="M16 16l20 20M36 16L16 36" stroke="currentColor"
                                    stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </div>
                       <h4 class="mt-3 mb-2" id="resultTitle">Settings Saved!</h4>
                        <p class="text-muted mb-4" id="resultMessage">Your automated messages have been updated successfully.</p>
                        <button type="button" class="btn btn-primary" id="resultOkBtn">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .quick-reply-options .card-header {
                background-color: #f8f9fa;
                font-weight: 500;
            }
        </style>

        <?php include 'include/emergency-modal.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


        <?php include 'include/toast.php'; ?>
        <script src="../assets/js/inbox.js"></script>
        <script src="../assets/js/script.js"></script>
        <script src="../assets/js/navbar.js"></script>

</body>

</html>