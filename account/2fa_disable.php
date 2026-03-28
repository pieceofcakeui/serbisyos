<?php
require_once '../functions/auth.php';
require_once __DIR__ . '/backend/db_connection.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT email, auth_provider FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$auth_provider = $user['auth_provider'];
$user_email = $user['email'];

if ($user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shop = $shopResult->fetch_assoc();

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

        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disable Two-Factor Authentication</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/users/2fa_disable.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, 
        body *:not(i):not(.fas):not(.fa):not([class^="icon-"]):not([class*=" icon-"]) {
            font-family: 'Montserrat', sans-serif !important;
        }
    </style>

</head>

<body>
    <?php include 'include/offline-handler.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="card security-card">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-shield-alt me-2"></i>
                        <span>Disable Two-Factor Authentication</span>
                    </div>

                    <div class="card-body p-4">
                        <?php if (isset($_SESSION['2fa_disable_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php
                                echo $_SESSION['2fa_disable_error'];
                                unset($_SESSION['2fa_disable_error']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="alert security-alert d-flex mb-4">
                            <i class="fas fa-exclamation-triangle me-3 fs-4 mt-1"></i>
                            <div>
                                <h5 class="alert-heading">Security Warning</h5>
                                <p class="mb-0">Disabling two-factor authentication will reduce your account security.
                                    We strongly recommend keeping 2FA enabled to protect against unauthorized access.
                                </p>
                            </div>
                        </div>

                        <form action="<?php echo BASE_URL; ?>/functions/disable_2fa.php" method="POST" class="needs-validation" novalidate>
                            <?php if ($auth_provider === 'manual'): ?>
                                <div class="mb-4 password-toggle">
                                    <label for="password" class="form-label fw-bold text-gray-700">Your Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required
                                        placeholder="Enter your current password">
                                    <i class="fa-regular fa-eye password-toggle-icon" id="togglePassword"></i>
                                    <div class="invalid-feedback">
                                        Please enter your current password
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold text-gray-700">Your Email</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="email" name="email" required
                                            value="<?php echo htmlspecialchars($user_email); ?>" <?php echo $auth_provider === 'manual' ? 'readonly' : ''; ?>>
                                        <button type="button" class="btn btn-primary" id="verifyEmailBtn">
                                            <i class="fas fa-envelope me-1"></i> Send OTP
                                        </button>
                                    </div>
                                    <div id="emailVerificationResult" class="mt-2"></div>

                                    <div id="otpVerificationSection" class="mt-3" style="display: none;">
                                        <label for="otp" class="form-label fw-bold text-gray-700">OTP Verification</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="otp" name="otp"
                                                placeholder="Enter 6-digit OTP" maxlength="6">
                                            <button type="button" class="btn btn-success" id="verifyOtpBtn">
                                                <i class="fas fa-check-circle me-1"></i> Verify OTP
                                            </button>
                                        </div>
                                        <div id="otpVerificationResult" class="mt-2"></div>
                                        <div class="form-text text-muted mt-2">
                                            <i class="fas fa-info-circle me-1"></i> We've sent a 6-digit OTP to your email.
                                            It will expire in 5 minutes.
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-4">
                                <label for="verification_code" class="form-label fw-bold text-gray-700">Authentication
                                    Code</label>
                                <input type="text" class="form-control code-input" id="verification_code"
                                    name="verification_code" placeholder="••••••" required autocomplete="off"
                                    inputmode="numeric" pattern="[0-9]{6}" maxlength="6">
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Enter the 6-digit code from your
                                    authenticator app
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid 6-digit authentication code
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5">
                                <a href="javascript:history.back()" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-lock-open me-2"></i> Disable 2FA
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <a href="javascript:history.back()" class="text-decoration-none text-gray-600">
                                    <i class="fas fa-arrow-left me-2"></i> Changed your mind? Return to security
                                    settings
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/2fa_disable.js"></script>

</body>

</html>