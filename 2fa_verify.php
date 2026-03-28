<?php 
session_start(); 

$showBackupFormOnLoad = false;
if (isset($_SESSION['show_backup_form']) && $_SESSION['show_backup_form']) {
    $showBackupFormOnLoad = true;
    unset($_SESSION['show_backup_form']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/2fa_verify.css">
    <style>
        body, 
        body *:not(i):not(.fas):not(.fa):not([class^="icon-"]):not([class*=" icon-"]) {
            font-family: 'Montserrat', sans-serif !important;
        }
        .backup-form {
            display: none;
        }
    </style>
</head>

<body>
     <?php include 'offline-handler.php'; ?>
     
    <div class="auth-container">
        <h1>Two-Factor Authentication</h1>
        <div id="error-message" class="error-message"></div>
        <div id="main-form">
            <p class="description">Please enter the 6-digit code from your authenticator app</p>
            <form id="totp-form" action="functions/verify_2fa.php" method="post">
                <input type="hidden" name="verification_code" id="verification_code">
                <div class="code-inputs">
                    <input type="text" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        name="digit1" required>
                    <input type="text" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        name="digit2" required>
                    <input type="text" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        name="digit3" required>
                    <input type="text" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        name="digit4" required>
                    <input type="text" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        name="digit5" required>
                    <input type="text" class="code-input" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        name="digit6" required>
                </div>
                <button type="submit" class="verify-btn">Verify Code</button>
                <div class="divider">OR</div>
                <a href="#" id="use-backup-link" class="backup-link">Use backup code instead</a>
            </form>
        </div>
        <div id="backup-form" class="backup-form">
            <h2>Enter Backup Code</h2>
            <p class="description">Enter one of your 8-digit backup codes</p>
            <form id="backup-code-form" action="functions/verify_backup_code.php" method="post">
                <input type="text" class="backup-input" name="backup_code" placeholder="1234-5678"
                    pattern="\d{4}-?\d{4}" required>
                <button type="submit" class="verify-btn">Verify Backup Code</button>
                <div class="divider">OR</div>
                <a href="#" id="use-totp-link" class="backup-link">Use authenticator app instead</a>
            </form>
        </div>
    </div>

      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    
    <script src="js/2fa_verify.js"></script>
    <?php include 'include/toast.php'; ?>

    <?php if ($showBackupFormOnLoad): ?>
    <script>
        $(document).ready(function() {
            $('#main-form').hide();
            $('#backup-form').css('display', 'block');
        });
    </script>
    <?php endif; ?>


</body>

</html>
