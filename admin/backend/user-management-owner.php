<?php
include 'db_connection.php';
$sql = "SELECT id, fullname, email, status FROM users WHERE profile_type='owner'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id'];
        $statusClass = ($row['status'] == 'Verified') ? 'bg-success' : 'bg-success';

        echo "<tr>
                                                       <td>
                                                            <div class='d-flex align-items-center'>
                                                                <div class='user-avatar me-3'>" . strtoupper(substr($row['fullname'], 0, 1)) . "</div>
                                                                <div>
                                                                    <div class='fw-bold'>{$row['fullname']}</div>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td>{$row['email']}</td>
                                                        <td><span class='badge $statusClass'>{$row['status']}</span></td>
                                                        <td>
                                                            <form action='backend/delete_user.php' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>
                                                                <input type='hidden' name='user_id' value='{$userId}'>
                                                                <button type='submit' class='btn btn-danger'>Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>";
    }
} else {
    echo "<tr><td colspan='4' class='text-center'>No shop owners found</td></tr>";
}
$conn->close();
?>