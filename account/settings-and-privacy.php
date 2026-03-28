<?php
require_once '../functions/auth.php';
require_once 'backend/security_helper.php';
include 'backend/settings-and-privacy.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings & Privacy</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/settings-and-privacy.css">
    <style>
        .password-toggle-icon {
            position: absolute;
            right: 12px;
            top: 42px;
            cursor: pointer;
            color: #6c757d;
            z-index: 5;
        }
    </style>
</head>

<body>
    <div id="toast-container" class="position-fixed top-0 start-50 translate-middle-x p-3"
        style="z-index: 1100; margin-top: 65px;"></div>
    <div class="overlay"></div>

    <?php include 'include/navbar.php' ?>
    <?php include 'include/modalForSignOut.php' ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <main>
            <div class="settings-container">
                <div class="settings-tabs-container">
                    <ul class="nav nav-tabs" id="settingsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="#security" data-bs-toggle="tab" data-bs-target="#security"
                                role="tab">
                                <i class="fas fa-shield-alt me-1"></i> Security
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="#notifications" data-bs-toggle="tab"
                                data-bs-target="#notifications" role="tab">
                                <i class="fas fa-bell me-1"></i> Notifications
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="#privacy" data-bs-toggle="tab" data-bs-target="#privacy"
                                role="tab">
                                <i class="fas fa-lock me-1"></i> Privacy
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="row g-0">
                    <div class="col-12 settings-content">
                        <div class="tab-content" id="settingsTabContent">
                            <div class="tab-pane fade" id="security" role="tabpanel">
                                <div class="settings-card">
                                    <h5><i class="fas fa-key"></i> Password Settings</h5>

                                    <?php if ($googleUser): ?>
                                        <div class="alert"
                                            style="background-color: #ffecb5; color: #664d03; border: 1px solid #ffe69c;">
                                            <i class="fas fa-info-circle me-2"></i> This account was created using Google.
                                            <a href="#" class="alert-link" data-bs-toggle="modal"
                                                data-bs-target="#setPasswordModal">
                                                Set a password to allow manual login
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($manualUser): ?>
                                        <button id="show-password-form-btn" class="btn btn-serbisyos">
                                            <i class="fas fa-edit"></i> Change Password
                                        </button>

                                        <div id="password-change-form-container" style="display: none;">
                                            <form action="backend/update_password.php" method="POST">
                                                <div class="mb-3 position-relative">
                                                    <label for="currentPassword" class="form-label">Current Password</label>
                                                    <input type="password" class="form-control" id="currentPassword"
                                                        name="currentPassword" required>
                                                    <span class="password-toggle-icon" id="toggleCurrentPassword">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </span>
                                                </div>
                                                <div class="mb-3 position-relative">
                                                    <label for="newPassword" class="form-label">New Password</label>
                                                    <input type="password" class="form-control" id="newPassword"
                                                        name="newPassword" required
                                                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$"
                                                        title="Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.">
                                                    <span class="password-toggle-icon" id="toggleNewPassword">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </span>
                                                    <small class="text-muted">Password must be at least 8 characters with
                                                        uppercase,
                                                        lowercase, number, and special character.</small>

                                                    <div id="passwordStrength" class="mt-2"
                                                        style="height: 5px; width: 100%; background-color: #e0e0e0; border-radius: 5px;">
                                                        <div id="passwordStrengthBar"
                                                            style="height: 100%; width: 0%; background-color: red; border-radius: 5px; transition: width 0.4s, background-color 0.4s;">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3 position-relative">
                                                    <label for="confirmPassword" class="form-label">Confirm New
                                                        Password</label>
                                                    <input type="password" class="form-control" id="confirmPassword"
                                                        name="confirmPassword" required>
                                                    <span class="password-toggle-icon" id="toggleConfirmPassword">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </span>
                                                    <div id="passwordMatchMessage" class="mt-2" style="font-size: 14px;">
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-serbisyos"><i class="fas fa-save"></i>
                                                    Update
                                                    Password</button>
                                                <button type="button" id="cancel-password-change-btn"
                                                    class="btn btn-secondary ms-2">Cancel</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php
                                if (!isset($user_id)) {
                                    $user_id = $_SESSION['user_id'] ?? 0;
                                }

                                $twofa_enabled = false;
                                $stmt = $conn->prepare("SELECT is_enabled FROM user_2fa WHERE user_id = ?");
                                $stmt->bind_param("i", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    $twofa_enabled = $result->fetch_assoc()['is_enabled'] == 1;
                                }
                                ?>

                                <div class="settings-card">
                                    <h5><i class="fas fa-shield-alt"></i> Two-Factor Authentication</h5>
                                    <p>Add an extra layer of security to your account.</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Status:</strong>
                                            <?php if ($twofa_enabled): ?>
                                                <span class="text-success">Enabled</span>
                                            <?php else: ?>
                                                <span class="text-muted">Disabled</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($twofa_enabled): ?>
                                         <a href="2fa_disable.php" class="btn-disable-2fa">Disable 2FA</a>
                                        <?php else: ?>
                                            <a href="../functions/enable_2fa.php" class="btn-enable-2fa">Enable 2FA</a>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($twofa_enabled): ?>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Two-factor authentication is currently protecting your account.
                                            </small>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Enable two-factor authentication for an additional layer of account
                                                security.
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="settings-card">
                                    <h5><i class="fas fa-laptop"></i> Active Sessions</h5>
                                    <p>Manage devices that are logged into your account.</p>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-muted small">
                                            <span id="active-sessions-count">Loading sessions...</span>
                                        </div>
                                        <button class="logout-all-btn" id="logout-all-btn" disabled>
                                            <span class="spinner-border spinner-border-sm d-none"
                                                id="logout-all-spinner"></span>
                                            <i class="fas fa-sign-out-alt"></i> Logout All Devices
                                        </button>
                                    </div>

                                    <div class="list-group" id="active-sessions-container">
                                        <div class="text-center py-3">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            include 'backend/db_connection.php';

                            $user_id = $_SESSION['user_id'] ?? null;
                            $is_checked = '';

                            if ($user_id) {

                                $stmt = $conn->prepare("SELECT enable_notifications FROM users WHERE id = ?");

                                $stmt->bind_param("i", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($row = $result->fetch_assoc()) {
                                    $is_checked = ($row['enable_notifications'] == 1) ? 'checked' : '';
                                }

                                $stmt->close();
                            }
                            ?>
                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                <div class="settings-card">
                                    <h5><i class="fas fa-bell"></i> Notification Preferences</h5>
                                    <p>Choose how you receive notifications from Serbisyos Auto Repair.</p>

                                    <form id="notificationSettingsForm">
                                        <div class="form-check form-switch mb-3">

                                            <input type="checkbox" class="form-check-input"
                                                id="enableNotificationsToggle" name="enable_notifications" value="1"
                                                <?php echo $is_checked; ?>>

                                            <label class="form-check-label" for="enableNotificationsToggle">
                                                Enable Notifications
                                            </label>
                                        </div>

                                        <button type="submit" class="btn btn-warning mt-3">
                                            Save Preferences
                                        </button>
                                        <div id="settingsStatus" class="mt-2 small"></div>
                                    </form>

    <h5 style="margin-top: 25px;"><i class="fas fa-satellite-dish"></i> Real-time Push Notifications</h5>
    <p>
        Enable browser push notifications to get instant alerts on your device, 
        even when the Serbisyos website is closed.
    </p>

    <button id="enable-notifications-btn" class="btn btn-serbisyos">
        <i class="fas fa-power-off me-2"></i>Enable Push Notifications
    </button>
                                </div>
                            </div>

                            <?php
                            include 'backend/db_connection.php';

                            $user_id = $_SESSION['user_id'] ?? null;
                            $data_checked = '';
                            $marketing_checked = '';

                            if ($user_id) {
                                $stmt = $conn->prepare("SELECT data_collection, marketing_email FROM users WHERE id = ?");
                                $stmt->bind_param("i", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($row = $result->fetch_assoc()) {
                                    $data_checked = ($row['data_collection'] == 1) ? 'checked' : '';
                                    $marketing_checked = ($row['marketing_email'] == 1) ? 'checked' : '';
                                }

                                $stmt->close();
                            }
                            ?>


                            <div class="tab-pane fade" id="privacy" role="tabpanel">
                                <div class="settings-card">
                                    <h5><i class="fas fa-user-secret"></i> Privacy Settings</h5>
                                    <p>Control how your information is shared and used.</p>

                                    <div class="mb-4">
                                        <form id="privacySettingsForm">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="dataCollection"
                                                    name="data_collection" <?= $data_checked ?>>
                                                <label class="form-check-label" for="dataCollection">
                                                    Allow data collection for service improvement
                                                </label>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="marketingEmails"
                                                    name="marketing_email" <?= $marketing_checked ?>>
                                                <label class="form-check-label" for="marketingEmails">
                                                    Receive marketing emails
                                                </label>
                                            </div>

                                            <button type="submit"  style="background-color: #ffc107; padding: 8px; border: none; border-radius: 5px;">Update Privacy
                                                Settings</button>
                                            <div id="privacyStatus" class="mt-2 small"></div>
                                        </form>

                                    </div>


                                    <?php
                                    $profile_type = '';
                                    if (isset($user_id) && $user_id) {
                                        $stmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
                                        $stmt->bind_param("i", $user_id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $profile = $result->fetch_assoc();
                                        $profile_type = $profile['profile_type'] ?? '';
                                        $stmt->close();
                                    }
                                    ?>

                                    <div class="settings-card">
                                        <h5><i class="fas fa-database"></i> Data Management</h5>
                                        <p>Download or delete your personal data.</p>

                                        <div
                                            class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3">
                                            <div class="mb-2 mb-sm-0">
                                                <strong>Download Your Data</strong><br>
                                                <samll class="text-muted">This data includes your account information
                                                    and
                                                    shop information.<br>Exporting may take some time. The download link
                                                    will be valid for 7 days.</small>
                                            </div>
                                            <?php if (isset($user_id) && $user_id): ?>
                                                <?php
                                                $stmt = $conn->prepare("SELECT * FROM data_download_requests WHERE user_id = ? AND status IN ('pending', 'processing') ORDER BY request_date DESC LIMIT 1");
                                                $stmt->bind_param("i", $user_id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $pendingRequest = $result->fetch_assoc();
                                                $stmt->close();

                                                if ($pendingRequest): ?>
                                                    <button class="btn btn-outline-secondary" disabled>Request Pending</button>
                                                <?php else: ?>
                                                    <button class="btn-warning btn-outline-primary"
                                                        style="background-color: #ffc107; padding: 8px; border: none; border-radius: 5px;"
                                                        data-bs-toggle="modal" data-bs-target="#requestDataModal">Request
                                                        Data</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (isset($user_id) && $user_id): ?>
                                            <?php
                                            $stmt = $conn->prepare("SELECT * FROM data_download_requests WHERE user_id = ? ORDER BY request_date DESC");
                                            $stmt->bind_param("i", $user_id);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if ($result->num_rows > 0): ?>
                                                <div class="existing-requests mb-4">
                                                    <h6 class="mb-3">Your Data Requests</h6>
                                                    <?php
                                                    $firstRequest = true;
                                                    while ($row = $result->fetch_assoc()):
                                            $status = $row['status'];

                                            $is_expired = false;
                                            $current_utc_time = new DateTime('now', new DateTimeZone('UTC'));

                                            if ($status === 'completed' && !empty($row['expires_at'])) {
                                                try {
                                                    $expires_at_time = new DateTime($row['expires_at'], new DateTimeZone('UTC'));
                                                    if ($current_utc_time > $expires_at_time) {
                                                        $is_expired = true;
                                                        $status = 'expired';
                                                    }
                                                } catch (Exception $e) {
                                                    error_log('Invalid expires_at format for request ID ' . $row['id']);
                                                }
                                            }

                                            if ($status === 'pending') {
                                                $badgeClass = 'warning';
                                                $statusText = 'Pending';
                                            } elseif ($status === 'processing') {
                                                $badgeClass = 'info';
                                                $statusText = 'Processing';
                                            } elseif ($status === 'completed') {
                                                $badgeClass = 'success';
                                                $statusText = 'Completed';
                                            } elseif ($status === 'failed') {
                                                $badgeClass = 'danger';
                                                $statusText = 'Failed';
                                            } elseif ($status === 'expired') { 
                                                $badgeClass = 'secondary';
                                                $statusText = 'Expired';
                                            } else {
                                                $badgeClass = 'secondary';
                                                $statusText = ucfirst($status);
                                            }

                                                        $dataTypesDisplay = '';
                                                        if (!empty($row['data_types'])) {
                                                            $dataTypes = json_decode($row['data_types'], true);
                                                            if (is_array($dataTypes)) {
                                                                $typesArray = isset($dataTypes['types']) ? $dataTypes['types'] : $dataTypes;
                                                                if (is_array($typesArray)) {
                                                                    $dataTypesDisplay = implode(', ', array_map(function ($type) {
                                                                        return ucwords(str_replace('_', ' ', $type));
                                                                    }, $typesArray));
                                                                }
                                                            }
                                                        }

                                                        if (empty($dataTypesDisplay)) {
                                                            $dataTypesDisplay = 'Unknown';
                                                        }
                                                        $encrypted_id = URLSecurity::encryptId($row['id']);
                                                        ?>
                                                        <div
                                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center border-bottom py-2">
                                                            <div class="mb-2 mb-md-0">
                                                                <?php if ($firstRequest && ($status === 'pending' || $status === 'processing')): ?>
                                                                    <p class='text-muted mb-2'>Your request is being processed. Please
                                                                        wait
                                                                        for
                                                                        it to complete.</p>
                                                                <?php endif; ?>
                                                                <strong>Data Types:
                                                                    <?= htmlspecialchars($dataTypesDisplay) ?></strong><br>
                                                                <small class="text-muted">Status: <span
                                                                        class="badge bg-<?= $badgeClass ?>"><?= $statusText ?></span></small><br>
                                                                <?php

                                                                $request_date_obj = new DateTime($row['request_date'], new DateTimeZone('UTC'));

                                                                $request_date_obj->setTimezone(new DateTimeZone('Asia/Manila'));

                                                                $request_date_display = $request_date_obj->format('M d, Y g:i A');
                                                                ?>

                                                                <small class="text-muted">Requested:
                                                                    <?= htmlspecialchars($request_date_display) ?>
                                                                </small>
                                                                <?php if ($status === 'completed' && !empty($row['completion_date'])): ?>
                                                                    <?php

                                                                    $completion_date_obj = new DateTime($row['completion_date'], new DateTimeZone('UTC'));
                                                                    $completion_date_obj->setTimezone(new DateTimeZone('Asia/Manila'));
                                                                    $completion_date_display = $completion_date_obj->format('M d, Y g:i A');
                                                                    ?>
                                                                    <br><small class="text-muted">Completed:
                                                                        <?= htmlspecialchars($completion_date_display) ?>
                                                                    </small>
                                                                <?php endif; ?>


                                                            </div>
                                                            <div class="btn-group" role="group">
                                                                <?php if ($status === 'completed' && !empty($row['download_url'])): ?>
                                                                    <a href="backend/secure_download.php?id=<?= htmlspecialchars($encrypted_id) ?>"
                                                                        class="btn btn-sm btn-success me-2 download-and-refresh-link"
                                                                        target="_blank">Download</a>
                                                                <?php elseif ($status === 'failed'): ?>
                                                                    <button class="btn btn-sm btn-outline-danger me-2"
                                                                        disabled>Failed</button>
                                                                <?php elseif ($status === 'processing'): ?>
                                                                    <div class="spinner-border spinner-border-sm text-primary me-2"
                                                                        role="status">
                                                                        <span class="visually-hidden">Processing...</span>
                                                                    </div>
                                                                <?php elseif ($status === 'expired'): ?>
                                                                    <button class="btn btn-sm btn-outline-warning me-2"
                                                                        disabled>Expired</button>
                                                                <?php endif; ?>
                                                                <a href="#"
                                                                    class="btn btn-sm btn-danger me-2 d-inline-flex align-items-center justify-content-center"
                                                                    style="height: 40px; padding: 0 10px;" data-bs-toggle="modal"
                                                                    data-bs-target="#deleteRequestModal"
                                                                    data-request-id="<?= $row['id'] ?>"
                                                                    data-delete-url="request_data.php?delete_request=<?= $row['id'] ?>">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <?php $firstRequest = false; ?>
                                                    <?php endwhile; ?>
                                                </div>
                                                <?php $stmt->close(); ?>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <div
                                            class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                            <div class="mb-2 mb-sm-0">
                                                <strong>Delete Your Account</strong><br>
                                                <small class="text-muted">Permanently remove your account and all
                                                    associated
                                                    data</small>
                                            </div>
                                            <button
                                                style="color: #dc3545; border: 1px solid #dc3545; background-color: transparent; padding: 0.375rem 0.75rem; border-radius: 0.25rem;"
                                                data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                                Delete Account
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5"></div>
        </main>
    </div>

    <div class="modal fade" id="requestDataModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="request_data.php">
                <div class="modal-content">
                    <div class="modal-body text-center px-4 py-4">
                        <h5 class="mb-3">Request Your Data</h5>
                        <p>Select which data you'd like to request:</p>
                        <div class="form-check text-start mb-2">
                            <input class="form-check-input" type="checkbox" name="data_types[]" value="personal"
                                id="personalData" checked>
                            <label class="form-check-label" for="personalData">Personal Info</label>
                        </div>
                        <?php if ($profile_type === 'owner'): ?>
                            <div class="form-check text-start mb-3">
                                <input class="form-check-input" type="checkbox" name="data_types[]"
                                    value="shop_applications" id="shopApplicationsData">
                                <label class="form-check-label" for="shopApplicationsData">Shop Applications</label>
                            </div>
                        <?php endif; ?>
                        <hr class="my-3">
                        <div class="text-start mb-3">
                            <label class="form-label"><strong>Download Format:</strong></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="formatJson" value="json"
                                    checked>
                                <label class="form-check-label" for="formatJson">
                                    <i class="fas fa-code me-1"></i> JSON Format
                                    <small class="text-muted d-block">Machine-readable format, good for
                                        developers</small>
                                </label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="format" id="formatHtml" value="html">
                                <label class="form-check-label" for="formatHtml">
                                    <i class="fas fa-file-alt me-1"></i> HTML Format
                                    <small class="text-muted d-block">Human-readable format, easy to view in
                                        browser</small>
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-info text-start" style="font-size: 0.875rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Email Notification:</strong> You'll receive an email when your data is ready for
                            download.
                        </div>
                        <div class="mt-4 d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning" id="submitRequestBtn"
                                style="height: 40px; padding: 0 15px;">
                                <span class="btn-content">
                                    Submit
                                </span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                        aria-hidden="true"></span>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/modal-setting.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
   

    <?php include 'include/toast.php'; ?>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/settings-and-privacy.js"></script>
    <script src="../assets/js/active-session.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script src="../assets/js/push-manager.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const requestForm = document.querySelector('#requestDataModal form');
            const submitBtn = document.getElementById('submitRequestBtn');
            const btnContent = submitBtn.querySelector('.btn-content');
            const btnLoading = submitBtn.querySelector('.btn-loading');

            if (requestForm && submitBtn) {
                requestForm.addEventListener('submit', function (e) {
                    btnContent.classList.add('d-none');
                    btnLoading.classList.remove('d-none');
                    submitBtn.disabled = true;

                    setTimeout(() => {
                        btnContent.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                        submitBtn.disabled = false;
                    }, 10000);
                });
            }

            function createPasswordToggle(inputId, toggleId) {
                const passwordInput = document.getElementById(inputId);
                const toggleIconContainer = document.getElementById(toggleId);

                if (passwordInput && toggleIconContainer) {
                    toggleIconContainer.addEventListener('click', function () {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);

                        const icon = this.querySelector('i');
                        if (type === 'password') {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    });
                }
            }

            createPasswordToggle('currentPassword', 'toggleCurrentPassword');
            createPasswordToggle('newPassword', 'toggleNewPassword');
            createPasswordToggle('confirmPassword', 'toggleConfirmPassword');

            const showBtn = document.getElementById('show-password-form-btn');
            const cancelBtn = document.getElementById('cancel-password-change-btn');
            const formContainer = document.getElementById('password-change-form-container');

            if (showBtn && formContainer) {
                showBtn.addEventListener('click', function () {
                    formContainer.style.display = 'block';
                    showBtn.style.display = 'none';
                });
            }

            if (cancelBtn && formContainer && showBtn) {
                cancelBtn.addEventListener('click', function () {
                    formContainer.style.display = 'none';
                    showBtn.style.display = 'block';
                    const form = formContainer.querySelector('form');
                    if (form) {
                        form.reset();
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const settingsForm = document.getElementById('notificationSettingsForm');
            const settingsStatus = document.getElementById('settingsStatus');

            settingsForm.addEventListener('submit', function (event) {
                event.preventDefault();

                settingsStatus.textContent = 'Saving...';
                settingsStatus.className = 'mt-2 small text-muted';

                const formData = new FormData(settingsForm);

                fetch('backend/update_notification_settings.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            settingsStatus.textContent = data.message;
                            settingsStatus.className = 'mt-2 small text-success';
                        } else {
                            settingsStatus.textContent = data.message;
                            settingsStatus.className = 'mt-2 small text-danger';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        settingsStatus.textContent = 'An error occurred. Please try again.';
                        settingsStatus.className = 'mt-2 small text-danger';
                    });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const privacyForm = document.getElementById('privacySettingsForm');
            const statusMessage = document.getElementById('privacyStatus');

            privacyForm.addEventListener('submit', function (event) {
                event.preventDefault();

                statusMessage.textContent = 'Saving...';
                statusMessage.className = 'mt-2 small text-muted';

                const formData = new FormData(privacyForm);

                fetch('backend/update_privacy_settings.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            statusMessage.textContent = data.message;
                            statusMessage.className = 'mt-2 small text-success';
                        } else {
                            statusMessage.textContent = data.message;
                            statusMessage.className = 'mt-2 small text-danger';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        statusMessage.textContent = 'An error occurred. Please try again.';
                        statusMessage.className = 'mt-2 small text-danger';
                    });
            });
        });
    </script>

</body>

</html>