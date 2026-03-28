<?php
session_start();
if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_email'])) {
    header("Location: signup.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/verify.css">
</head>
<body>
    <?php include 'offline-handler.php'; ?>

    <div class="verify">
        <form id="verify-form" method="POST" action="functions/functions.php">
            <h2>Verify Your OTP</h2>
            <label>Enter OTP</label>
            <input type="text" id="otp" name="otp" required maxlength="6" pattern="[0-9]{6}" placeholder="Enter OTP">
            <button type="submit" name="verify_otp">Verify OTP</button>
            <p>
                Didn't receive the code?
                <a href="#" id="resend-link">Resend OTP</a>
                <span id="countdown-timer" style="margin-left: 10px; color: red;"></span>
            </p>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <?php include 'include/toast.php'; ?>

    <script src="./assets/js/script.js"></script>
    <script>
        const otpLastSent = <?php echo isset($_SESSION['last_otp_sent']) ? $_SESSION['last_otp_sent'] : 0; ?>;
    </script>
    <script src="js/verify_otp.js"></script>
</body>
</html>