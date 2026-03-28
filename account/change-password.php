<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/emergency-modal';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
     <?php include 'include/modalForSignOut.php'; ?>
     <?php include 'include/offline-handler.php'; ?>

    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="account_details.php" class="text-dark d-flex align-items-center"
            style="text-decoration: none; font-size: 18px; font-weight: 500;">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>

    <?php include 'backend/change-password.php'; ?>

    <div class="edit-profile-section">
        <div class="edit-profile-card text-center">
            <h2 class="mb-4">Change Password</h2>

            <form method="POST" action="../functions/functions.php">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" name="change_password" class="btn btn-primary action-btn me-2">
                        <i class="fas fa-key me-2"></i> Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"></script>

    <script src="../assets/js/script.js"></script>
     <script src="../assets/js/navbar.js"></script>

</body>

</html>