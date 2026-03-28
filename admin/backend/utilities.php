<?php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

function sendPushNotification($conn, $user_id, $title, $body, $url) {
    
    $stmt = $conn->prepare("SELECT endpoint, p256dh, auth FROM push_subscriptions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subscriptions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($subscriptions)) {
        return;
    }

    $auth = [
        'VAPID' => [
            'subject' => $_ENV['VAPID_SUBJECT'],
            'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
            'privateKey' => $_ENV['VAPID_PRIVATE_KEY'],
        ],
    ];

    $webPush = new WebPush($auth);
    
    $payload = json_encode([
        'title' => $title,
        'body' => $body,
        'url' => 'https://serbisyos.com' . $url,
        'icon' => 'https://serbisyos.com/assets/img/serbisyos-logo-192.png',
        'badge' => 'https://serbisyos.com/assets/img/serbisyos-badge-mono.png'
    ]);

    foreach ($subscriptions as $sub) {
        $subscription = Subscription::create([
            'endpoint' => $sub['endpoint'],
            'publicKey' => $sub['p256dh'],
            'authToken' => $sub['auth'],
        ]);
        $webPush->queueNotification($subscription, $payload);
    }

    foreach ($webPush->flush() as $report) {
        if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            $delete_stmt = $conn->prepare("DELETE FROM push_subscriptions WHERE endpoint = ?");
            $delete_stmt->bind_param("s", $endpoint);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    }
}


/**
 * Calculates the distance between two geographical points in kilometers.
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $lat1 = (float)$lat1;
    $lon1 = (float)$lon1;
    $lat2 = (float)$lat2;
    $lon2 = (float)$lon2;
    
    $earth_radius = 6371;
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) + 
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earth_radius * $c;
}