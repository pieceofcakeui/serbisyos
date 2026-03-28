<?php
include 'db_connection.php';
include 'encrypt_loc.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['cancel_request'])) {
        $request_id = intval($_POST['request_id']);

        $updateQuery = "UPDATE emergency_requests SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $request_id, $user_id);

        if ($updateStmt->execute()) {
            $shopQuery = $conn->prepare("SELECT shop_id FROM emergency_requests WHERE id = ?");
            $shopQuery->bind_param("i", $request_id);
            $shopQuery->execute();
            $shopResult = $shopQuery->get_result();
            $shopRow = $shopResult->fetch_assoc();
            $shopQuery->close();

            if ($shopRow) {
                $shop_id = $shopRow['shop_id'];

                $ownerQuery = $conn->prepare("SELECT user_id FROM shop_applications WHERE id = ?");
                $ownerQuery->bind_param("i", $shop_id);
                $ownerQuery->execute();
                $ownerResult = $ownerQuery->get_result();
                $ownerRow = $ownerResult->fetch_assoc();
                $ownerQuery->close();

                if ($ownerRow) {
                    $shop_owner_id = $ownerRow['user_id'];

                    $notification_type = 'emergency_cancelled';
                    $related_id = $request_id;
                    $status = 'cancelled';
                    $distance = null;
                    $is_read = 0;
                    $delete_notification = 0;

                    $notifQuery = $conn->prepare("INSERT INTO notifications (user_id, shop_id, notification_type, related_id, status, distance, is_read, delete_notification) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $notifQuery->bind_param("iisisidi", $shop_owner_id, $shop_id, $notification_type, $related_id, $status, $distance, $is_read, $delete_notification);
                    $notifQuery->execute();
                    $notifQuery->close();
                }
            }

            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Emergency Request cancelled successfully'];
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to cancel emergency request'];
        }

        $updateStmt->close();
    } elseif (isset($_POST['delete_request'])) {
        $request_id = intval($_POST['request_id']);

        $softDeleteQuery = "UPDATE emergency_requests SET deleted_at = NOW() WHERE id = ? AND user_id = ?";
        $deleteStmt = $conn->prepare($softDeleteQuery);
        $deleteStmt->bind_param("ii", $request_id, $user_id);

        if ($deleteStmt->execute()) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Emergency Request removed successfully'];
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to remove emergency request'];
        }
        $deleteStmt->close();
    }

    header("Location: my-emergency-request.php");
    exit();
}

$query = "SELECT 
            er.id,
            er.vehicle_type,
            er.vehicle_model,
            er.issue_description,
            er.full_address,
            er.location,
            er.contact_number,
            er.urgent,
            er.latitude,
            er.longitude,
            er.status,
            er.created_at,
            er.updated_at,
            er.completed_at,
            er.video,
            sa.shop_name,
            sa.shop_logo
          FROM emergency_requests er
          LEFT JOIN shop_applications sa ON er.shop_id = sa.id
          WHERE er.user_id = ? AND er.deleted_at IS NULL
          ORDER BY er.created_at DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    if (!empty($row['full_address'])) {
        $decrypted_address = decryptData($row['full_address']);
        $row['decrypted_address'] = ($decrypted_address !== false && $decrypted_address !== null) ? $decrypted_address : 'Could not decrypt address';
    } else {
        $row['decrypted_address'] = 'Location not provided';
    }

    if (!empty($row['video'])) {
        $video_files = json_decode($row['video'], true);

        if (is_array($video_files) && !empty($video_files)) {
            $filename = $video_files[0];
            $row['video'] = 'uploads/emergency_videos/' . htmlspecialchars($filename);
        } else {
            $row['video'] = null;
        }
    }
    $requests[] = $row;
}

$stmt->close();
$conn->close();
?>