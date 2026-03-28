<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action == "approve") {

        $updateQuery = "UPDATE shop_applications SET status = 'Approved' WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {

            $userQuery = "SELECT user_id FROM shop_applications WHERE id = ?";
            $userStmt = $conn->prepare($userQuery);
            $userStmt->bind_param("i", $id);
            $userStmt->execute();
            $userStmt->bind_result($user_id);
            $userStmt->fetch();
            $userStmt->close();

            if (!empty($user_id)) {
                $profileQuery = "UPDATE users SET profile_type = 'owner' WHERE id = ?";
                $profileStmt = $conn->prepare($profileQuery);
                $profileStmt->bind_param("i", $user_id);

                if ($profileStmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Application approved, profile updated"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to update profile type"]);
                }

                $profileStmt->close();
            } else {
                echo json_encode(["status" => "error", "message" => "User ID not found"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update application status"]);
        }

        $stmt->close();
    } elseif ($action == "deny") {
        $deleteQuery = "DELETE FROM shop_applications WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Application denied"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete application"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    }

    $conn->close();
}
?>
