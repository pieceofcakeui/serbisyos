<?php
include 'db_connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$user_id = $_SESSION['user_id'];

if ($user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shop = $shopResult->fetch_assoc();
    $shopQuery->close();

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
        $emergencyQuery->close();

        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
            $updateQuery->close();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['cancel_booking'])) {
        $booking_id = intval($_POST['booking_id']);

        $bookingQuery = $conn->prepare("
            SELECT sb.id, sb.shop_id, sb.user_id, sb.booking_status
            FROM services_booking sb
            WHERE sb.id = ? AND sb.user_id = ?
            LIMIT 1
        ");
        $bookingQuery->bind_param("ii", $booking_id, $user_id);
        $bookingQuery->execute();
        $bookingResult = $bookingQuery->get_result();
        $bookingRow = $bookingResult->fetch_assoc();
        $bookingQuery->close();

        if ($bookingRow) {
            $current_status = strtolower(trim($bookingRow['booking_status']));

            if ($current_status === 'pending' || $current_status === 'accept' || $current_status === 'accepted') {
                $updateQuery = "UPDATE services_booking SET booking_status = 'Cancelled' WHERE id = ? AND user_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ii", $booking_id, $user_id);

                if ($updateStmt->execute()) {
                    $shop_id = (int)$bookingRow['shop_id'];

                    $ownerQuery = $conn->prepare("SELECT user_id FROM shop_applications WHERE id = ?");
                    $ownerQuery->bind_param("i", $shop_id);
                    $ownerQuery->execute();
                    $ownerResult = $ownerQuery->get_result();
                    $ownerRow = $ownerResult->fetch_assoc();
                    $ownerQuery->close();

                    $notification_type = 'booking_cancelled';
                    $status = 'Cancelled';
                    $distance = 0;
                    $is_read = 0;
                    $delete_notification = 0;

                    // Notify shop owner
                    if ($ownerRow) {
                        $shop_owner_id = (int)$ownerRow['user_id'];

                        if ($shop_owner_id > 0) {
                            $notifOwnerQuery = $conn->prepare("
                                INSERT INTO notifications 
                                (user_id, shop_id, notification_type, related_id, status, distance, is_read, delete_notification, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                            ");
                            $notifOwnerQuery->bind_param(
                                "iisisdii",
                                $shop_owner_id,
                                $shop_id,
                                $notification_type,
                                $booking_id,
                                $status,
                                $distance,
                                $is_read,
                                $delete_notification
                            );
                            $notifOwnerQuery->execute();
                            $notifOwnerQuery->close();
                        }
                    }

                    // Notify user who cancelled
                    $notifUserQuery = $conn->prepare("
                        INSERT INTO notifications 
                        (user_id, shop_id, notification_type, related_id, status, distance, is_read, delete_notification, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $notifUserQuery->bind_param(
                        "iisisdii",
                        $user_id,
                        $shop_id,
                        $notification_type,
                        $booking_id,
                        $status,
                        $distance,
                        $is_read,
                        $delete_notification
                    );
                    $notifUserQuery->execute();
                    $notifUserQuery->close();

                    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Booking cancelled successfully'];
                } else {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to cancel booking'];
                }

                $updateStmt->close();
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'This booking can no longer be cancelled'];
            }
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Booking not found'];
        }

    } elseif (isset($_POST['delete_booking'])) {
        $booking_id = intval($_POST['booking_id']);

        $softDeleteQuery = "UPDATE services_booking SET deleted_at = NOW() WHERE id = ? AND user_id = ?";
        $deleteStmt = $conn->prepare($softDeleteQuery);
        $deleteStmt->bind_param("ii", $booking_id, $user_id);

        if ($deleteStmt->execute()) {
            $updateRead = $conn->prepare("UPDATE services_booking SET is_read = 0 WHERE id = ?");
            $updateRead->bind_param("i", $booking_id);
            $updateRead->execute();
            $updateRead->close();

            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Booking removed successfully'];
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to remove booking'];
        }

        $deleteStmt->close();
    }

    header("Location: my-booking.php");
    exit();
}

$query = "SELECT 
            sb.*, 
            sa.shop_name,
            sa.shop_logo,
            sa.barangay
          FROM services_booking sb
          LEFT JOIN shop_applications sa ON sb.shop_id = sa.id
          WHERE sb.user_id = ? AND sb.deleted_at IS NULL
          ORDER BY sb.preferred_datetime DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$stmt->close();
$conn->close();
?>