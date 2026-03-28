<?php
require_once '../functions/auth.php';
include 'backend/2fa_setup.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Two-Factor Authentication | Security</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/2fa_setup.css">
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

    <div id="main-content" class="main-content">
        <div class="content container py-4">
            <div class="auth-container">
                <div class="auth-header">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <a href="settings-and-privacy.php"
                                style="color: white; text-decoration: none;" class="verify-back-btn btn-sm mb-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>

                            <h2 class="mt-2">
                                <i class="fas fa-shield-alt me-2"></i> Two-Factor Authentication
                            </h2>
                            <p class="mb-0">Add an extra layer of security to your account</p>
                        </div>
                    </div>
                </div>


                <div class="auth-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                            <div>
                                <strong>Verification failed</strong><br>
                                <?php echo $_SESSION['2fa_error'] ?? 'The verification code you entered is invalid. Please try again.'; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="step-card">
                        <h5 class="mb-2"><span class="step-number">1</span> Set up your authenticator app</h5>
                        <p class="mb-0">Scan the QR code or enter the secret key manually</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="qr-container" id="qr-container">
                                <?php if (!$qrCodeError && !empty($qrCodeImage)): ?>
                                    <div class="qr-loading" id="qr-loading">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading QR Code...</span>
                                        </div>
                                    </div>
                                    <img src="<?php echo htmlspecialchars($qrCodeImage); ?>" alt="QR Code"
                                        class="img-fluid qr-code mb-3" id="qr-image" style="display: none;"
                                        onload="showQRCode()" onerror="handleQRError()">
                                    <p class="text-muted small mb-0" id="qr-success" style="display: none;">
                                        <i class="fas fa-qrcode me-1"></i> Scan this with your authenticator app
                                    </p>
                                <?php else: ?>
                                    <div class="alert alert-warning mb-0 text-start">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>QR code unavailable</strong>
                                        <div class="mt-2">
                                            <?php if (empty($secret)): ?>
                                                No secret key provided. Please contact support.
                                            <?php else: ?>
                                                QR code could not be generated. Please use manual entry below.
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="manual-entry">
                                <h5 class="d-flex align-items-center">
                                    <i class="fas fa-keyboard text-primary me-2"></i>
                                    <span>Manual Setup</span>
                                </h5>
                                <p class="text-muted">If you can't scan the QR code, enter this secret key manually:</p>

                                <div class="input-group mb-3">
                                    <div class="position-relative">
                                        <input type="text" class="form-control font-monospace pe-5"
                                            value="<?php echo htmlspecialchars($secret); ?>" readonly id="secret-key">

                                        <button class="btn position-absolute end-0 top-0 h-100 d-flex align-items-center justify-content-center 
                   bg-transparent border-0 copy-btn" type="button" data-clipboard-target="#secret-key"
                                            style="width: 48px; min-width: 40px;">
                                            <i class="fas fa-copy text-secondary fs-5" style="margin-bottom: 20px;"></i>
                                        </button>
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <p class="mb-2"><strong>Supported Apps:</strong></p>
                                    <div class="app-badges">
                                        <img src="../assets/img/authenticator/google.png" alt="Google Authenticator"
                                            class="app-badge" onerror="this.style.display='none'">
                                        <img src="../assets/img/authenticator/ms.png" alt="Microsoft Authenticator"
                                            class="app-badge" onerror="this.style.display='none'">
                                        <img src="../assets/img/authenticator/authy.png" alt="Authy" class="app-badge"
                                            onerror="this.style.display='none'">
                                        <img src="../assets/img/authenticator/lastpass.png" alt="LastPass" class="app-badge"
                                            onerror="this.style.display='none'">
                                    </div>
                                </div>

                                <div class="alert alert-custom-bg mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>After scanning, your app will display a 6-digit code that changes every 30
                                        seconds.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <h5 class="mb-2"><span class="step-number">2</span> Verify your authenticator app</h5>
                        <p class="mb-0">Enter the 6-digit code from your authenticator app to complete setup</p>
                    </div>

                    <form action="../functions/enable_2fa.php" method="POST" class="mt-3 needs-validation" novalidate>
                        <input type="hidden" name="secret" value="<?php echo htmlspecialchars($secret); ?>">

                        <div class="mb-4">
                            <label for="verification_code" class="form-label">6-Digit Verification Code</label>
                            <input type="text" class="form-control verification-input" id="verification_code"
                                name="verification_code" placeholder="••••••" required autocomplete="off"
                                inputmode="numeric" pattern="[0-9]{6}" maxlength="6">
                            <div class="form-text">The code will expire in 30 seconds</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                            </div>
                            <div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/feedback-form.js"></script>
    <script src="../assets/js/home.js"></script>
    <script src="../assets/js/2fa_setup.js"></script>
    <script src="../assets/js/navbar.js"></script>


</body>

</html>