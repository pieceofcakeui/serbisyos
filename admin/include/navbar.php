<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom" id="navbar">
    <div class="container-fluid">
        <button class="btn d-md-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="ms-auto d-flex align-items-center">
            <?php
            include __DIR__ . '/../backend/db_connection.php';
            require_once __DIR__ . '/../backend/security_helper.php';

            $shop_notifications = [];
            $query_shop = "SELECT sa.id, u.fullname, sa.applied_at as timestamp_col, 'shop' as type, sa.is_read_admin
                             FROM shop_applications sa JOIN users u ON sa.user_id = u.id";
            if ($result_shop = $conn->query($query_shop)) {
                while ($row = $result_shop->fetch_assoc()) $shop_notifications[] = $row;
            }

            $verification_notifications = [];
            $query_verify = "SELECT vs.id, u.fullname, vs.submission_date as timestamp_col, 'verification' as type, vs.is_read_admin
                               FROM verification_submissions vs JOIN users u ON vs.user_id = u.id";
            if ($result_verify = $conn->query($query_verify)) {
                while ($row = $result_verify->fetch_assoc()) $verification_notifications[] = $row;
            }

            $combined_notifications = array_merge($shop_notifications, $verification_notifications);
            usort($combined_notifications, function($a, $b) {
                return strtotime($b['timestamp_col']) - strtotime($a['timestamp_col']);
            });
            $notifications_to_display = array_slice($combined_notifications, 0, 5);

            $count_shop = $conn->query("SELECT COUNT(id) as count FROM shop_applications WHERE is_read_admin = 0")->fetch_assoc()['count'];
            $count_verify = $conn->query("SELECT COUNT(id) as count FROM verification_submissions WHERE is_read_admin = 0")->fetch_assoc()['count'];
            $total_unread_notifications = $count_shop + $count_verify;
            ?>

            <div class="dropdown d-flex align-items-center me-3">
                <button class="btn p-0 position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fs-5"></i>
                    <?php if ($total_unread_notifications > 0) : ?>
                        <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;">
                            <?php echo $total_unread_notifications > 99 ? '99+' : $total_unread_notifications; ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" id="notification-menu" aria-labelledby="notificationDropdown" style="width: 300px;">
                    <?php if (!empty($combined_notifications)) : ?>
                        <li><a class="dropdown-item text-end small" id="mark-all-read-dropdown" href="#">Mark all as read</a></li>
                        <li><hr class="dropdown-divider mt-0"></li>
                        
                        <?php foreach ($notifications_to_display as $notification) :
                            $is_shop = ($notification['type'] === 'shop');
                            $encrypted_id = URLSecurity::encryptId($notification['id']);
                            $unread_class = $notification['is_read_admin'] == 0 ? 'notification-unread' : '';

                            if ($is_shop) {
                                $action_url = "application.php";
                                $input_name = "app_id";
                                $icon_class = "fas fa-store me-2 text-primary";
                                $text       = "New shop request from";
                            } else {
                                $action_url = "user-verification.php";
                                $input_name = "sub_id";
                                $icon_class = "fas fa-user-check me-2 text-success";
                                $text       = "Verification request from";
                            }
                        ?>
                        <li>
                            <form action="<?php echo $action_url; ?>" method="POST" class="d-inline">
                                <input type="hidden" name="<?php echo $input_name; ?>" value="<?php echo $encrypted_id; ?>">
                                <button type="submit" class="dropdown-item notification-item d-flex align-items-center py-2 <?php echo $unread_class; ?>" data-type="<?php echo $notification['type']; ?>" data-id="<?php echo $notification['id']; ?>">
                                    <i class="<?php echo $icon_class; ?>"></i>
                                    <div class="small lh-sm">
                                        <div><?php echo $text; ?></div>
                                        <strong><?php echo htmlspecialchars($notification['fullname']); ?></strong>
                                    </div>
                                </button>
                            </form>
                        </li>
                        <?php endforeach; ?>

                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center small text-muted" href="notification.php">See all notifications</a></li>
                    <?php else : ?>
                        <li><p class="dropdown-item text-center small text-muted mb-0">No new notifications</p></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="dropdown d-flex align-items-center">
                 <button class="btn p-0" type="button" id="profileDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <?php
                    $adminId = $_SESSION['id'];
                    $query = "SELECT profile_pic FROM admins WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $adminId);
                    $stmt->execute();
                    $stmt->bind_result($profilePic);
                    $stmt->fetch();
                    $stmt->close();
                    $imagePath = !empty($profilePic) ? "./img/profile/$profilePic" : "./img/profile/profile-user.png";
                    ?>
                    <img src="<?php echo $imagePath; ?>" alt="Profile" class="rounded-circle" width="35" height="35">
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="#profileSettings" data-bs-toggle="modal"><i class="fas fa-user-cog me-2"></i>Profile Settings</a></li>
                    <li><a class="dropdown-item" href="#changePassword" data-bs-toggle="modal"><i class="fas fa-lock me-2"></i>Change Password</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#signOutModal"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('mark-all-read-dropdown');
    const notificationBadge = document.getElementById('notification-badge');
    const notificationItems = document.querySelectorAll('#notification-menu .notification-item');

    if(markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('./backend/mark_notifications_read.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=mark_all'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (notificationBadge) {
                        notificationBadge.remove();
                    }
                    notificationItems.forEach(item => {
                        item.classList.remove('notification-unread');
                    });
                }
            });
        });
    }

    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            if (this.classList.contains('notification-unread')) {
                const type = this.dataset.type;
                const id = this.dataset.id;
                
                fetch('./backend/mark_notifications_read.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `type=${type}&id=${id}`
                });
            }
        });
    });
});
</script>

<style>
.notification-item.notification-unread {
    border-left: 3px solid #ffc107;
    background-color: #f8f9fa;
}
</style>

<div class="modal fade" id="profileSettings" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="profileForm" action="./backend/update_admin_profile.php" enctype="multipart/form-data" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Profile Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="profileAlert" class="alert d-none" role="alert"></div>

                    <?php
                    include './backend/db_connection.php';
                    $adminId = $_SESSION['id'];
                    $query = "SELECT full_name, email, phone_number, profile_pic FROM admins WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $adminId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $adminData = $result->fetch_assoc();
                    $stmt->close();

                    $imagePath = !empty($adminData['profile_pic']) ? "./img/profile/" . htmlspecialchars($adminData['profile_pic']) : "./img/profile/profile-user.png";
                    ?>

                    <div class="mb-3 text-center">
                        <img src="<?php echo $imagePath; ?>" alt="Profile" class="rounded-circle mb-3" width="100" height="100" id="previewImage">
                        <div>
                            <input type="file" name="profile_pic" accept="image/*" class="form-control mb-2" onchange="previewProfileImage(event)">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="full_name" value="<?php echo htmlspecialchars($adminData['full_name'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($adminData['email'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phoneNumber" name="phone_number" value="<?php echo htmlspecialchars($adminData['phone_number'] ?? ''); ?>">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="update_profile" id="saveProfileBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="saveSpinner"></span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('profileForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const saveBtn = document.getElementById('saveProfileBtn');
        const spinner = document.getElementById('saveSpinner');
        const alertBox = document.getElementById('profileAlert');
        const formData = new FormData(this);

        saveBtn.disabled = true;
        spinner.classList.remove('d-none');
        alertBox.classList.add('d-none');

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                saveBtn.disabled = false;
                spinner.classList.add('d-none');

                alertBox.textContent = data.message;
                alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
                alertBox.classList.add(data.success ? 'alert-success' : 'alert-danger');

                if (data.success && data.profile_pic) {
                    document.getElementById('previewImage').src = './img/profile/' + data.profile_pic + '?t=' + new Date().getTime();
                }

                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('profileSettings'));
                    modal.hide();

                    document.getElementById('profileSettings').addEventListener('hidden.bs.modal', function () {
                        window.location.reload();
                    }, { once: true });
                }, 2000);
            })
            .catch(error => {
                saveBtn.disabled = false;
                spinner.classList.add('d-none');
                alertBox.textContent = 'An error occurred. Please try again.';
                alertBox.classList.remove('d-none', 'alert-success');
                alertBox.classList.add('alert-danger');
                console.error('Error:', error);

                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('profileSettings'));
                    modal.hide();

                    document.getElementById('profileSettings').addEventListener('hidden.bs.modal', function () {
                        window.location.reload();
                    }, { once: true });
                }, 2000);
            });
    });

    function previewProfileImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('previewImage');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<div class="modal fade" id="changePassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="passwordAlert"></div>
                <form action="./backend/change_admin_password.php" method="POST">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('#changePassword form').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        const alertDiv = document.getElementById('passwordAlert');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Changing...';

        alertDiv.innerHTML = '';
        alertDiv.className = '';

        const formData = new FormData(form);
        formData.append('change_password', 'true');

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                alertDiv.className = `alert alert-${data.success ? 'success' : 'danger'}`;
                alertDiv.textContent = data.message;

                if (data.success) {
                    form.reset();
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('changePassword'));
                        modal.hide();
                    }, 1500);
                }
            })
            .catch(error => {
                alertDiv.className = 'alert alert-danger';
                alertDiv.textContent = 'An error occurred. Please try again.';
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
    });
</script>
<style>
    .navbar .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 40px;
        width: 40px;
        border: none;
        cursor: pointer;
        color: #555;
        transition: all 0.3s ease;
        position: relative;
    }

    .navbar .btn i {
        line-height: 1;
    }

    .navbar .dropdown-toggle::after {
        display: none;
    }
    #navbar {
        padding: 21px;
    }
</style>