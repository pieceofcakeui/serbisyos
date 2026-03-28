<?php
session_start();

if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}

$toast_data = null;
if (isset($_SESSION['flash_message'])) {
    $toast_data = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Serbisyos</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://www.google.com/recaptcha/api.js?render=6Lek9SwrAAAAADET9vu0zqwyo3iyyZK47dMfFrgA"></script>

</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

    <section class="login-section">
        <div class="card-container">
            <div class="intro-card">
                <div class="logo-container">
                    <img src="assets/img/logo/logo.webp" alt="Serbisyos Logo">
                </div>
                <h2>Welcome Back</h2>
                <p>To stay connected, please log in with your personal info.</p>
            </div>

            <div class="form-card">
                <div class="form-content">
                    <h2 class="desktop-title">LOGIN</h2>
                    <h2 class="mobile-title">Welcome</h2>
                    <p class="form-subtitle">Login to your account to continue</p>

                    <form id="login-form" class="form" method="POST" action="functions/functions.php">
                        <input type="hidden" name="signin" value="true">

                        <div class="input-group">
                            <input type="email" id="email" name="email" placeholder="Email" required autocomplete="off">
                        </div>
                        <div class="input-group">
                            <input type="password" id="password" name="password" placeholder="Password" required>
                            <span class="toggle-password" id="toggle-password">
                                <i class="fa-regular fa-eye"></i>
                            </span>
                        </div>

                        <div class="options-group">
                            <div class="remember-me">
                                <input type="checkbox" id="remember_me" name="remember_me">
                                <label for="remember_me">Remember me</label>
                            </div>
                            <div class="forgot-password">
                                <a href="forgot-password.php">Forgot Password?</a>
                            </div>
                        </div>

                        <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                        <button type="submit" id="signin-btn" class="btn-custom">
                            <span class="spinner" id="loading-spinner"></span>
                            <span id="btn-text">Sign In</span>
                        </button>

                        <div class="divider">
                            <span>OR</span>
                        </div>

                        <div class="google-login-container">
                            <a href="functions/google_login.php?redirect=<?php echo urlencode($_SESSION['redirect_after_login'] ?? 'account/home.php'); ?>" class="google-btn">
                                <img src="assets/img/google.png" alt="Google Logo">
                                Sign In with Google
                            </a>
                        </div>

                        <p class="signup-link">Don't have an account? <a href="signup.php">Sign up here</a></p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <?php include 'include/toast.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            <?php
            if (isset($toast_data) && $toast_data):
                $toast_type = $toast_data['type'];
                $toast_body = $toast_data['body'];
                $toast_title = $toast_data['title'];
            ?>

                var toastOptions = {
                    "progressBar": true,
                    "positionClass": "toast-top-center"
                };

                if ('<?php echo $toast_type; ?>' === 'info') {
                    toastOptions.closeButton = false;
                    toastOptions.timeOut = "3000";
                } else {
                    toastOptions.closeButton = true;
                    toastOptions.timeOut = "5000";
                }

                toastr.options = toastOptions;
                toastr['<?php echo $toast_type; ?>']("<?php echo $toast_body; ?>", "<?php echo $toast_title; ?>");

            <?php
            endif;
            ?>

            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
                });
            }

            const loginForm = document.getElementById('login-form');
            const signinBtn = document.getElementById('signin-btn');
            const loadingSpinner = document.getElementById('loading-spinner');
            const btnText = document.getElementById('btn-text');

            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const email = document.getElementById('email').value.trim();
                    const password = document.getElementById('password').value;

                    if (!email || !password) {
                        toastr.error('Please enter both email and password.');
                        return;
                    }

                    setLoadingState(true);

                    grecaptcha.ready(function() {
                        grecaptcha.execute('6Lek9SwrAAAAADET9vu0zqwyo3iyyZK47dMfFrgA', {
                            action: 'login'
                        }).then(function(token) {
                            document.getElementById('recaptcha_token').value = token;
                            loginForm.submit();
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
                    signinBtn.disabled = true;
                    loadingSpinner.style.display = 'inline-block';
                    btnText.textContent = 'Signing In...';
                } else {
                    signinBtn.disabled = false;
                    loadingSpinner.style.display = 'none';
                    btnText.textContent = 'Sign In';
                }
            }
        });
    </script>

</body>
</html>