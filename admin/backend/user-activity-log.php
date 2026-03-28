<?php
function getUserTypeLabel($user_id, $profile_type, $conn)
{
    $user_query = "SELECT fullname FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $user_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result)) {
        $user = mysqli_fetch_assoc($result);
        return ($profile_type === 'owner' ? 'Owner' : 'User') . ': ' . $user['fullname'];
    }
    return 'Unknown User';
}

function getShopName($conn, $shop_id)
{
    $query = "SELECT shop_name FROM shop_applications WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $shop_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['shop_name'] : 'Shop';
}

function getDeviceInfo($user_agent)
{
    $device_info = 'Unknown';
    if (strpos($user_agent, 'Mobile') !== false) {
        if (strpos($user_agent, 'Android') !== false)
            $device_info = 'Android Mobile';
        elseif (strpos($user_agent, 'iPhone') !== false)
            $device_info = 'iPhone';
        else
            $device_info = 'Mobile Device';
    } elseif (strpos($user_agent, 'Tablet') !== false)
        $device_info = 'Tablet';
    elseif (strpos($user_agent, 'Windows') !== false)
        $device_info = 'Windows PC';
    elseif (strpos($user_agent, 'Macintosh') !== false)
        $device_info = 'Mac';
    elseif (strpos($user_agent, 'Linux') !== false)
        $device_info = 'Linux PC';
    return $device_info;
}

function getActivityLogData($conn)
{
    $activities = array();

    $query = "SELECT al.id, al.user_id, al.activity_type, al.device_info, al.ip_address, 
                     al.user_agent, al.activity_time, al.attempts_remaining,
                     u.profile_type, u.fullname, u.login_attempts
              FROM activity_log al
              LEFT JOIN users u ON al.user_id = u.id
              WHERE al.activity_type IN ('LOGIN ATTEMPT','LOGIN SUCCESS','LOGIN FAILED','ACCOUNT LOCKED','LOGOUT','GOOGLE LOGIN')
              ORDER BY al.activity_time DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $device_info = getDeviceInfo($row['user_agent']);
        $description = $row['fullname'] . ' ' . strtolower(str_replace('_', ' ', $row['activity_type']));

        if ($row['activity_type'] === 'LOGIN FAILED') {
            if ($row['attempts_remaining'] !== null) {
                $description .= ' - ' . $row['attempts_remaining'] . ' attempts remaining';
            }
        }

        $description .= ' from ' . $device_info . ' (IP: ' . $row['ip_address'] . ')';
        $activities[] = array(
            'user_id' => $row['user_id'],
            'profile_type' => $row['profile_type'],
            'action' => $row['activity_type'],
            'description' => $description,
            'date_time' => $row['activity_time'],
            'category' => 'authentication'
        );
    }

    $query = "SELECT sa.id, sa.user_id, sa.date_created, u.profile_type, u.fullname
              FROM shop_applications sa
              LEFT JOIN users u ON sa.user_id = u.id
              ORDER BY sa.date_created DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = array(
            'user_id' => $row['user_id'],
            'profile_type' => $row['profile_type'],
            'action' => 'SHOP_APPLICATION',
            'description' => $row['fullname'] . ' applied for shop owner status',
            'date_time' => $row['date_created'],
            'category' => 'applications'
        );
    }

    $query = "SELECT sb.user_id, sb.shop_id, sb.created_at, u.profile_type, u.fullname 
              FROM services_booking sb
              LEFT JOIN users u ON sb.user_id = u.id
              ORDER BY sb.created_at DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $shop_name = getShopName($conn, $row['shop_id']);
        $activities[] = array(
            'user_id' => $row['user_id'],
            'profile_type' => $row['profile_type'],
            'action' => 'BOOK_SERVICE',
            'description' => $row['fullname'] . ' booked service at ' . $shop_name,
            'date_time' => $row['created_at'],
            'category' => 'bookings'
        );
    }

    $query = "SELECT er.user_id, er.shop_id, er.created_at, u.profile_type, u.fullname 
              FROM emergency_requests er
              LEFT JOIN users u ON er.user_id = u.id
              ORDER BY er.created_at DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $shop_name = getShopName($conn, $row['shop_id']);
        $activities[] = array(
            'user_id' => $row['user_id'],
            'profile_type' => $row['profile_type'],
            'action' => 'EMERGENCY_REQUEST',
            'description' => $row['fullname'] . ' requested emergency service at ' . $shop_name,
            'date_time' => $row['created_at'],
            'category' => 'emergencies'
        );
    }

    $query = "SELECT sr.user_id, sr.shop_id, sr.created_at, u.profile_type, u.fullname
              FROM shop_ratings sr
              LEFT JOIN users u ON sr.user_id = u.id
              ORDER BY sr.created_at DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $shop_name = getShopName($conn, $row['shop_id']);
        $activities[] = array(
            'user_id' => $row['user_id'],
            'profile_type' => $row['profile_type'],
            'action' => 'SHOP_RATING',
            'description' => $row['fullname'] . ' rated ' . $shop_name,
            'date_time' => $row['created_at'],
            'category' => 'ratings'
        );
    }

    $query = "SELECT m.receiver_id, m.sender_id, m.created_at, u.profile_type, u.fullname
              FROM messages m
              LEFT JOIN users u ON m.sender_id = u.id
              ORDER BY m.created_at DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = array(
            'user_id' => $row['sender_id'],
            'profile_type' => $row['profile_type'],
            'action' => 'SEND_MESSAGE',
            'description' => $row['fullname'] . ' sent a message',
            'date_time' => $row['created_at'],
            'category' => 'messages'
        );
    }

    $query = "SELECT u2fa.user_id, u2fa.is_enabled, u2fa.created_at, u2fa.updated_at, u.profile_type, u.fullname
              FROM user_2fa u2fa
              LEFT JOIN users u ON u2fa.user_id = u.id
              ORDER BY GREATEST(u2fa.created_at, u2fa.updated_at) DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $action = ($row['is_enabled'] == 1) ? 'ENABLE_2FA' : 'DISABLE_2FA';
        $activities[] = array(
            'user_id' => $row['user_id'],
            'profile_type' => $row['profile_type'],
            'action' => $action,
            'description' => $row['fullname'] . ' ' . ($row['is_enabled'] == 1 ? 'enabled' : 'disabled') . ' two-factor authentication',
            'date_time' => ($row['updated_at']) ? $row['updated_at'] : $row['created_at'],
            'category' => 'security'
        );
    }

    $query = "SELECT ubc.user_id, ubc.used_at, u.profile_type, u.fullname
              FROM user_2fa_backup_codes ubc
              LEFT JOIN users u ON ubc.user_id = u.id
              WHERE ubc.is_used = 1
              ORDER BY ubc.used_at DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = array(
            'user_id' => $row['user_id'],
            'profile_type' => $row['profile_type'],
            'action' => 'USE_BACKUP_CODE',
            'description' => $row['fullname'] . ' used a 2FA backup code',
            'date_time' => $row['used_at'],
            'category' => 'security'
        );
    }

    $query = "SELECT ss.user_id, ss.shop_id, ss.saved_at, u.profile_type, u.fullname
              FROM save_shops ss
              LEFT JOIN users u ON ss.user_id = u.id
              ORDER BY ss.saved_at DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $shop_name = getShopName($conn, $row['shop_id']);
        $activities[] = array(
            'user_id' => $row['user_id'],
            'profile_type' => $row['profile_type'],
            'action' => 'SAVE_SHOP',
            'description' => $row['fullname'] . ' saved ' . $shop_name,
            'date_time' => $row['saved_at'],
            'category' => 'saved'
        );
    }

    usort($activities, function ($a, $b) {
        return strtotime($b['date_time']) - strtotime($a['date_time']);
    });

    return $activities;
}

$activityLog = getActivityLogData($conn);
$categories = [
    'all' => 'All Activities',
    'authentication' => 'Authentication Logs',
    'applications' => 'Shop Applications',
    'bookings' => 'Service Bookings',
    'emergencies' => 'Emergency Requests',
    'ratings' => 'Shop Ratings',
    'messages' => 'Messages',
    'security' => 'Security',
    'subscriptions' => 'Subscriptions',
    'saved' => 'Saved Shops'
];
$current_category = isset($_GET['category']) && array_key_exists($_GET['category'], $categories) ? $_GET['category'] : 'all';
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';