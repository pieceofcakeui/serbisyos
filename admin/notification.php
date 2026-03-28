<?php
include 'backend/auth.php';
include 'backend/db_connection.php';
include 'backend/security_helper.php';

$shop_notifications_all = [];
$query_shop_all = "SELECT sa.id, u.fullname, sa.applied_at as timestamp_col, 'shop' as type, sa.is_read_admin
                  FROM shop_applications sa JOIN users u ON sa.user_id = u.id";
if ($result = $conn->query($query_shop_all)) {
    while ($row = $result->fetch_assoc()) $shop_notifications_all[] = $row;
}
$verification_notifications_all = [];
$query_verify_all = "SELECT vs.id, u.fullname, vs.submission_date as timestamp_col, 'verification' as type, vs.is_read_admin
                       FROM verification_submissions vs JOIN users u ON vs.user_id = u.id";
if ($result = $conn->query($query_verify_all)) {
    while ($row = $result->fetch_assoc()) $verification_notifications_all[] = $row;
}

$all_notifications = array_merge($shop_notifications_all, $verification_notifications_all);
usort($all_notifications, function ($a, $b) {
    return strtotime($b['timestamp_col']) - strtotime($a['timestamp_col']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications</title>
       <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .notification-item {
            text-decoration: none;
            color: inherit;
            transition: background-color 0.2s ease-in-out;
        }
        .notification-item:hover { background-color: #f8f9fa; }
        .notification-item.notification-unread {
            border-left: 3px solid #ffc107;
        }
        .form-btn {
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            width: 100%;
            text-align: left;
            font: inherit;
            cursor: pointer;
        }
@media (max-width: 576px) {
  #mark-all-read-page {
    width: 100%;
  }
}


    </style>
</head>
<body>
    <?php include 'include/offline-handler.php'; ?>
    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <div class="content w-100 d-flex flex-column" style="min-height: 100vh;">
            <?php include 'include/navbar.php'; ?>
            
            <main class="container-fluid p-4" style="flex: 1;">
                <div class="card">
<div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
    <h2 class="card-title mb-0">All Notifications</h2>
    <button class="btn btn-warning btn-sm" id="mark-all-read-page">Mark all as read</button>
</div>


                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="notification-list-page">
                            <?php if (empty($all_notifications)) : ?>
                                <div class="text-center p-5">
                                    <i class="fas fa-bell-slash fa-3x text-muted"></i>
                                    <p class="mt-3">You have no notifications.</p>
                                </div>
                            <?php else : ?>
                               <?php foreach ($all_notifications as $notification) :
    $is_shop = ($notification['type'] === 'shop');
    $action_url = $is_shop ? 'application.php' : 'user-verification.php';
    $id_name = $is_shop ? 'app_id' : 'sub_id';
    $encrypted_id = URLSecurity::encryptId($notification['id']);
    $unread_class = $notification['is_read_admin'] == 0 ? 'notification-unread' : '';
?>
    <div class="list-group-item notification-item <?php echo $unread_class; ?>" 
         data-type="<?php echo $notification['type']; ?>" 
         data-id="<?php echo $notification['id']; ?>">
        
        <form action="<?php echo $action_url; ?>" method="POST" class="m-0">
            <input type="hidden" name="<?php echo $id_name; ?>" value="<?php echo $encrypted_id; ?>">
            <button type="submit" class="form-btn p-0">
                <div class="d-flex w-100 justify-content-between">
                    <div>
                        <i class="<?php echo $is_shop ? 'fas fa-store me-2 text-primary' : 'fas fa-user-check me-2 text-success'; ?>"></i>
                        <?php echo $is_shop ? 'New shop request from ' : 'Verification request from '; ?>
                        <strong><?php echo htmlspecialchars($notification['fullname']); ?></strong>
                    </div>
                    <small class="text-muted flex-shrink-0 ms-3"><?php echo date('M d, Y', strtotime($notification['timestamp_col'])); ?></small>
                </div>
            </button>
        </form>
    </div>
<?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
            <?php include 'include/footer.php'; ?>
        </div>
    </div>
    
      <?php include 'include/back-to-top.php'; ?>
      
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const markAllReadBtnPage = document.getElementById('mark-all-read-page');
        const notificationListPage = document.querySelectorAll('#notification-list-page .notification-item');

        if(markAllReadBtnPage) {
            markAllReadBtnPage.addEventListener('click', function() {
                fetch('backend/mark_notifications_read.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=mark_all'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            });
        }

        notificationListPage.forEach(item => {
            // This event is now for marking as read, not for navigation
            // Navigation is handled by the form submission
            item.addEventListener('click', function(event) {
                if (this.classList.contains('notification-unread')) {
                    const type = this.dataset.type;
                    const id = this.dataset.id;
                    fetch('backend/mark_notifications_read.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `type=${type}&id=${id}`
                    });
                }
            });
        });
    });
    </script>
</body>
</html>