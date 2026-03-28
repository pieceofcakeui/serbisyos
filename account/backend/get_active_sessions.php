<?php
require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}
function formatTimestamp($datetime, $type = 'last_active')
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($type === 'login_time') {
        if ($diff < 60) return "Just now";
        if ($diff < 3600) return floor($diff / 60) . " minutes ago";
        if ($diff < 86400) return floor($diff / 3600) . " hours ago";
        return date('M d, Y h:i A', $time);
    } else {
        if ($diff < 60) return "Just now";
        if ($diff < 3600) return floor($diff / 60) . " minutes ago";
        if ($diff < 86400) return floor($diff / 3600) . " hours ago";
        if ($diff < 2592000) return floor($diff / 86400) . " days ago";
        return date('M d, Y h:i A', $time);
    }
}

function getDeviceInfo($user_agent)
{
    $icons = [
        'browsers' => [
            'Chrome' => 'fab fa-chrome',
            'Firefox' => 'fab fa-firefox',
            'Safari' => 'fab fa-safari',
            'Edge' => 'fab fa-edge',
            'Opera' => 'fab fa-opera',
            'Internet Explorer' => 'fab fa-internet-explorer',
            'Brave' => 'fab fa-brave',
            'Default' => 'fas fa-globe'
        ],
        'os' => [
            'Windows' => 'fab fa-windows',
            'Mac' => 'fab fa-apple',
            'Linux' => 'fab fa-linux',
            'Android' => 'fab fa-android',
            'iPhone' => 'fab fa-apple',
            'iPad' => 'fab fa-apple',
            'Default' => 'fas fa-laptop'
        ]
    ];

    $browser = 'Browser';
    $browserIcon = $icons['browsers']['Default'];
    foreach ($icons['browsers'] as $b => $icon) {
        if (stripos($user_agent, $b) !== false) {
            $browser = $b;
            $browserIcon = $icon;
            break;
        }
    }

    $os = 'Device';
    $osIcon = $icons['os']['Default'];
    foreach ($icons['os'] as $o => $icon) {
        if (stripos($user_agent, $o) !== false) {
            $os = $o;
            $osIcon = $icon;
            break;
        }
    }

    if (stripos($user_agent, 'iPhone') !== false) {
        $os = 'iPhone';
        $osIcon = 'fab fa-apple';
    } elseif (stripos($user_agent, 'iPad') !== false) {
        $os = 'iPad';
        $osIcon = 'fab fa-apple';
    } elseif (stripos($user_agent, 'Android') !== false) {
        $os = 'Android';
        $osIcon = 'fab fa-android';
    }

    return [
        'browser' => $browser,
        'browser_icon' => $browserIcon,
        'os' => $os,
        'os_icon' => $osIcon,
        'device_type' => (stripos($user_agent, 'Mobile') !== false) ? 'mobile' : 'desktop'
    ];
}

$stmt = $conn->prepare("SELECT id, session_id, device_info, ip_address, user_agent, login_time, last_activity 
                        FROM active_sessions 
                        WHERE user_id = ?
                        ORDER BY last_activity DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$output = [];
foreach ($sessions as $session) {
    $deviceInfo = getDeviceInfo($session['user_agent']);
    $is_current = ($session['session_id'] === ($_SESSION['session_id'] ?? ''));

    $output[] = [
        'id' => $session['id'],
        'session_id' => $session['session_id'],
        'browser' => $deviceInfo['browser'],
        'browser_icon' => $deviceInfo['browser_icon'],
        'os' => $deviceInfo['os'],
        'os_icon' => $deviceInfo['os_icon'],
        'device_type' => $deviceInfo['device_type'],
        'ip' => $session['ip_address'],
        'login_time' => formatTimestamp($session['login_time'], 'login_time'),
        'last_active' => formatTimestamp($session['last_activity']),
        'is_current' => $is_current
    ];
}

header('Content-Type: application/json');
echo json_encode($output);
?>