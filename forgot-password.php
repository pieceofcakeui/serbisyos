<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: account/home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Serbisyos</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
   <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/forgotpass.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://www.google.com/recaptcha/api.js?render=6Lek9SwrAAAAADET9vu0zqwyo3iyyZK47dMfFrgA"></script>

    <style>
        body,
        body *:not(i):not(.fas):not(.fa):not([class^="icon-"]):not([class*=" icon-"]) {
            font-family: 'Montserrat', sans-serif !important;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn:disabled {
            background-color: #ccc !important;
            color: #666 !important;
            cursor: not-allowed !important;
        }
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #nprogress .bar {
    display: block !important;
    background: #212529 !important;
    height: 3px !important;       
    z-index: 2000 !important;     
}

#nprogress .spinner {
    display: none !important;
}

#nprogress .peg {
    box-shadow: none !important;
}

    </style>
</head>

<body>

    <?php include 'offline-handler.php'; ?>

    <section class="forgot-password">
        <div class="forgot-password-container">
            <h2>Forgot Password</h2>
            <p class="form-subtitle">Enter your email to receive a reset code.</p>
            <form id="forgot-password-form" class="form" method="POST" action="functions/forgot-pass-email-verify.php">
                
                <input type="hidden" name="forgot_password" value="true">
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" required id="email">
                </div>
                <button type="submit" id="continue-btn" class="btn">
                    <span class="spinner" id="loading-spinner"></span>
                    <span id="btn-text">Continue</span>
                </button>
            </form>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'include/toast.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const forgotForm = document.getElementById('forgot-password-form');
        const continueBtn = document.getElementById('continue-btn');
        const loadingSpinner = document.getElementById('loading-spinner');
        const btnText = document.getElementById('btn-text');

        if (forgotForm) {
            forgotForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const email = document.getElementById('email').value.trim();
                if (!email) {
                    toastr.error('Please enter your email address.');
                    return;
                }

                setLoadingState(true);

                grecaptcha.ready(function() {
                    grecaptcha.execute('6Lek9SwrAAAAADET9vu0zqwyo3iyyZK47dMfFrgA', { action: 'forgot_password' }).then(function(token) {
                        document.getElementById('recaptcha_token').value = token;
                        forgotForm.submit();
                    }).catch(function(error) {
                        console.error("reCAPTCHA execution error:", error);
                        toastr.error('reCAPTCHA verification failed. Please try again.');
                        setLoadingState(false);
                    });
                });
            });
        }

        function setLoadingState(loading) {
            if (loading) {
                continueBtn.disabled = true;
                loadingSpinner.style.display = 'inline-block';
                btnText.textContent = 'Processing...';
            } else {
                continueBtn.disabled = false;
                loadingSpinner.style.display = 'none';
                btnText.textContent = 'Continue';
            }
        }
    });
    </script>
</body>
</html>