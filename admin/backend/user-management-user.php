<?php
include 'db_connection.php';

$sql = "SELECT id, fullname, email, profile_type, status FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id'];
        $initials = strtoupper(substr($row['fullname'], 0, 1));
        $badgeClass = ($row['profile_type'] == 'owner') ? 'bg-warning' : 'bg-primary';
        $statusClass = ($row['status'] == 'Active') ? 'bg-success' : 'bg-secondary';

        if ($row['status'] === 'Active') {
            $actionLabel = 'Deactivate';
            $buttonClass = 'btn-warning';
            $newStatus = 'Inactive';
        } else {
            $actionLabel = 'Reactivate';
            $buttonClass = 'btn-success';
            $newStatus = 'Active';
        }

        echo "<tr>
                <td>
                    <div class='d-flex align-items-center'>
                        <div class='user-avatar me-3'>$initials</div>
                        <div>
                            <div class='fw-bold'>{$row['fullname']}</div>
                        </div>
                    </div>
                </td>
                <td>{$row['email']}</td>
                <td><span class='badge $badgeClass'>{$row['profile_type']}</span></td>
                <td><span class='badge $statusClass'>{$row['status']}</span></td>
                <td>
                    <!-- Deactivate or Reactivate -->
                    <form action='backend/update_user_status.php' method='POST' class='d-inline' onsubmit='return confirm(\"Are you sure you want to {$actionLabel} this user?\");'>
                        <input type='hidden' name='user_id' value='{$userId}'>
                        <input type='hidden' name='new_status' value='{$newStatus}'>
                        <button type='submit' class='btn btn-sm {$buttonClass}'>{$actionLabel}</button>
                    </form>

                    <!-- Delete -->
                    <form action='backend/delete_user.php' method='POST' class='d-inline' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>
                        <input type='hidden' name='user_id' value='{$userId}'>
                        <button type='submit' class='btn btn-sm btn-danger'>Delete</button>
                    </form>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No users found</td></tr>";
}
$conn->close();
?>
