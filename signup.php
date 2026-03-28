<?php
session_start();
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_signup'] = $_GET['redirect'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Serbisyos</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/signup.css">

    <script src="https://www.google.com/recaptcha/api.js?render=6Lek9SwrAAAAADET9vu0zqwyo3iyyZK47dMfFrgA"></script>

</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>
    <section class="signup-section">
        <div class="card-container">
            <div class="intro-card">
                <div class="logo-container">
                    <img src="assets/img/logo/logo.webp" alt="Serbisyos Logo">
                </div>
                <h2>Join Serbisyos Today</h2>
                <p>Discover trusted auto repair services near you. Book appointments, get emergency assistance, and receive shop recommendations based on your location — all in one platform.</p>
                <ul class="benefits-list">
                    <li><i class="fas fa-check"></i>Find and book nearby auto shops</li>
                    <li><i class="fas fa-check"></i>Get real-time emergency assistance</li>
                    <li><i class="fas fa-check"></i>Receive personalized recommendations</li>
                    <li><i class="fas fa-check"></i>Track and manage your service history</li>
                </ul>
            </div>
            <div class="form-card">
                <div class="form-content">
                    <h2>Create Account</h2>
                    <p class="form-subtitle">Enter your details to get started</p>
                    <form id="signup-form" class="form" method="POST" action="functions/functions.php">

                        <input type="hidden" name="sign_up" value="true">

                        <div class="row">
                            <div class="col-12">
                                <div class="input-group">
                                    <label for="fullname">Full Name</label>
                                    <input type="text" id="fullname" name="fullname" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group password-container">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" title="Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.">
                                    <span class="toggle-password" id="toggle-password"><i class="fa-regular fa-eye"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-check text-center my-3">
                            <input class="form-check-input" type="checkbox" id="agree" required>
                            <label class="form-check-label" for="agree" style="font-size: 12px;">
                                By signing up, you agree to Serbisyos
                                <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>
                                and
                                <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>.
                            </label>
                        </div>
                        <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                        <button type="submit" class="btn" id="signup-btn">
                            <span class="spinner" id="loading-spinner"></span>
                            <span id="btn-text">Sign Up</span>
                        </button>
                        <div class="divider"><span>OR</span></div>
                        <div class="google-login-container">
                            <a href="functions/google_signup.php?redirect=<?php echo urlencode($_SESSION['redirect_after_signup'] ?? 'account/home.php'); ?>" class="google-btn">
                                <img src="assets/img/google.png" alt="Google Logo" style="width: 20px; height: 20px; margin-right: 10px;">
                                Sign Up with Google
                            </a>
                        </div>
                        <p style="text-align: center; margin-top: 10px;">Already have an account? <a href="login.php" style="text-decoration: none;">Sign in here</a></p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Privacy Policy for Serbisyos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Last Updated: October 5, 2025</strong></p>

                    <h5>1. Introduction</h5>
                    <p>Welcome to Serbisyos. This Privacy Policy explains how we collect, use, protect, and handle your personal information when you use our website and services. Serbisyos is an online directory service designed to help vehicle owners find and connect with auto repair shops. By using our platform, you agree to the collection and use of information in accordance with this policy.</p>

                    <h5>2. Information We Collect</h5>
                    <p>We collect various types of information to provide and improve our service to you.</p>
                    <h6>A. Information You Provide to Us:</h6>
                    <ul>
                        <li><strong>Account Registration:</strong> When you create an account manually, we collect your name, email address, and password. If you register using a Google account, we receive information from Google to create your account.</li>
                        <li><strong>Account Verification:</strong> To use certain features like booking and emergency requests, you must complete account verification. This process requires you to provide your full name, gender, birthday, address, a clear photo of a valid government-issued ID (National ID or Driver's License), and a live selfie for identity matching.</li>
                        <li><strong>Shop Owner ("Partner") Application:</strong> When you apply to list your shop, we collect detailed business information, including contact details, location data, services offered, business permit, and Tax ID for verification by our administrators.</li>
                        <li><strong>Communications:</strong> We collect information you provide when you communicate with shop owners through our real-time chat system, submit feedback about the platform, or interact with our AI Chatbot.</li>
                    </ul>
                    <h6>B. Information Collected Automatically:</h6>
                    <ul>
                        <li><strong>Location Data:</strong> To provide our core services, such as recommending nearby shops, we use your device's GPS to pinpoint your location. The accuracy of this data can depend on your device and signal strength.</li>
                        <li><strong>Usage and Session Data:</strong> We automatically log information about your interactions with our platform. Our Active Session Management feature records the device used and the time of your login. We also maintain an activity log to record user actions for security and analytical purposes.</li>
                        <li><strong>Chatbot Interaction Data:</strong> When you use the AI Chatbot, we collect your chat queries and any images you upload for part identification.</li>
                    </ul>

                    <h5>3. How We Use Your Information</h5>
                    <ul>
                        <li><strong>To Provide and Manage Services:</strong> To operate the platform, facilitate appointment bookings, process emergency requests, and display relevant shop information based on your location.</li>
                        <li><strong>To Ensure Security and Trust:</strong> To verify user accounts before allowing access to high-trust features, verify the legitimacy of shop owners through the "Become a Partner" process, and secure your account with features like OTP and Two-Factor Authentication (2FA).</li>
                        <li><strong>To Communicate With You:</strong> To send you important notifications via your on-site account and email regarding booking confirmations, emergency request updates, and new messages.</li>
                        <li><strong>To Improve Our Platform:</strong> To analyze user feedback and usage patterns to improve the platform's functionality and user experience.</li>
                        <li><strong>To Prevent Fraud:</strong> We use tools like Google reCAPTCHA v3 to prevent bot registrations and secure login and password recovery flows.</li>
                    </ul>

                    <h5>4. Data Security</h5>
                    <p>We are committed to protecting your data. We implement a multi-layered security framework that includes:</p>
                    <ul>
                        <li><strong>Encryption:</strong> We secure sensitive user data using AES-256 encryption in CBC mode.</li>
                        <li><strong>Authentication:</strong> We offer secure login via OTP for manual sign-ups and 2FA for all users.</li>
                        <li><strong>Session Management:</strong> You can view all active sessions for your account and remotely log out of any session you do not recognize.</li>
                        <li><strong>Secure Deletion:</strong> When you choose to delete your account, you must confirm the action by typing "DELETE MY ACCOUNT" to prevent accidental deletion, after which all your data is permanently erased.</li>
                    </ul>

                    <h5>5. Your Rights and Choices</h5>
                    <ul>
                        <li><strong>Data Download:</strong> You have the right to request and download a copy of your personal data. For shop owners, this includes their submitted shop application details.</li>
                        <li><strong>Account Deletion:</strong> You have the option to permanently erase your account and all associated data from our system at any time.</li>
                        <li><strong>Location Services:</strong> You can control location data sharing through your web browser or device settings. Please note that disabling location services will limit your ability to use core features like finding nearby shops.</li>
                    </ul>
                    <h5>6. Changes to This Privacy Policy</h5>
                    <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                    <h5>7. Contact Us</h5>
                    <p>If you have any questions about this Privacy Policy, please <a href="contact.php" style="text-decoration: none;">contact us</a>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions for Serbisyos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Last Updated: October 5, 2025</strong></p>

                    <h5>1. Agreement to Terms</h5>
                    <p>By accessing or using the Serbisyos platform, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our services.</p>

                    <h5>2. User Accounts and Responsibilities</h5>
                    <ul>
                        <li><strong>Account Creation:</strong> You must provide accurate and complete information during registration. You are responsible for maintaining the confidentiality of your account password.</li>
                        <li><strong>Account Verification:</strong> To use the Appointment Booking and Emergency Request features, you must complete our identity verification process, which may require submitting a valid ID and a selfie.</li>
                        <li><strong>Email Verification:</strong> For manual registrations, you must verify your email via a One-Time Password (OTP) to access the platform. Accounts that are not verified within 24 hours will be automatically and permanently deleted.</li>
                        <li><strong>Account Security:</strong> You are responsible for all activities that occur under your account. We encourage you to enable Two-Factor Authentication (2FA) for enhanced security.</li>
                    </ul>

                    <h5>3. Platform Services and Disclaimers</h5>
                    <ul>
                        <li><strong>Directory Service:</strong> Serbisyos acts primarily as a directory and intermediary to connect vehicle owners with auto repair shops.</li>
                        <li><strong>No Guarantee of Service Quality:</strong> While we have a verification process for shops, we do not guarantee the quality, safety, or legality of the services provided by the listed shops. All transactions and interactions are solely between you and the shop owner.</li>
                        <li><strong>AI Chatbot:</strong> The AI Chatbot is provided for informational and preliminary purposes only. Its diagnostic tips and car part identification feature are not a substitute for a professional mechanical assessment. Serbisyos is not liable for any issues arising from advice given by the chatbot. The accuracy of the image recognition is not guaranteed.</li>
                    </ul>

                    <h5>4. User Content and Conduct</h5>
                    <ul>
                        <li><strong>Reviews and Ratings:</strong> You can submit reviews and ratings based on your experiences. You are solely responsible for the content you post.</li>
                        <li><strong>Shop Reporting:</strong> You can report shops for issues like inaccurate information, fake reviews, spam, scams, or inappropriate content. Reports will be reviewed by our administrators.</li>
                        <li><strong>Prohibited Activities:</strong> You agree not to use the platform to submit false information, harass other users, post fraudulent reviews, or engage in any illegal activities.</li>
                    </ul>

                    <h5>5. Terms for Shop Owners ("Partners")</h5>
                    <ul>
                        <li><strong>Application:</strong> To be listed, you must complete the "Become a Partner" application and submit verifiable business documents for admin approval.</li>
                        <li><strong>Responsibility:</strong> You are responsible for keeping your shop information accurate and up-to-date, including your business hours, services offered, and contact details.</li>
                        <li><strong>Feature Configuration:</strong> You are responsible for managing your booking form settings and your availability for emergency services.</li>
                        <li><strong>Platform Rules:</strong> Shop owners are prohibited from booking appointments or sending emergency requests to their own shop.</li>
                    </ul>

                    <h5>6. Termination</h5>
                    <ul>
                        <li><strong>By You:</strong> You may terminate your account at any time through the secure account deletion feature in your settings.</li>
                        <li><strong>By Serbisyos:</strong> We reserve the right to suspend or terminate your account if you violate these Terms and Conditions.</li>
                    </ul>

                    <h5>7. Modification of Terms</h5>
                    <p>Serbisyos reserves the right to update or modify these terms and conditions at any time. Any changes will be posted on this page, and it is your responsibility to review them regularly.</p>

                    <h5>8. Contact Us</h5>
                    <p>If you have any questions about this Terms and Conditions, please <a href="contact.php" style="text-decoration: none;">contact us</a>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <?php include 'include/toast.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const signupForm = document.getElementById('signup-form');
            const signupBtn = document.getElementById('signup-btn');
            const loadingSpinner = document.getElementById('loading-spinner');
            const btnText = document.getElementById('btn-text');

            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
                });
            }

            if (signupForm) {
                signupForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (!validateForm()) {
                        return;
                    }

                    setLoadingState(true);

                    grecaptcha.ready(function() {
                        grecaptcha.execute('6Lek9SwrAAAAADET9vu0zqwyo3iyyZK47dMfFrgA', {
                            action: 'signup'
                        }).then(function(token) {
                            document.getElementById('recaptcha_token').value = token;
                            signupForm.submit();
                        }).catch(function(error) {
                            console.error("reCAPTCHA execution error:", error);
                            toastr.error('reCAPTCHA verification failed. Please try again.');
                            setLoadingState(false);
                        });
                    });
                });
            }

            function validateForm() {
                const fullname = document.getElementById('fullname').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const agreeCheckbox = document.getElementById('agree');

                if (!fullname) {
                    toastr.error('Please enter your full name.');
                    return false;
                }
                if (!email) {
                    toastr.error('Please enter your email address.');
                    return false;
                }
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    toastr.error('Please enter a valid email address.');
                    return false;
                }
                if (!password) {
                    toastr.error('Please enter a password.');
                    return false;
                }
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
                if (!passwordRegex.test(password)) {
                    toastr.error('Password is not strong enough.');
                    return false;
                }
                if (!agreeCheckbox.checked) {
                    toastr.error('You must agree to the Terms & Conditions.');
                    return false;
                }
                return true;
            }

            function setLoadingState(loading) {
                if (loading) {
                    signupBtn.disabled = true;
                    loadingSpinner.style.display = 'inline-block';
                    btnText.textContent = 'Processing...';
                } else {
                    signupBtn.disabled = false;
                    loadingSpinner.style.display = 'none';
                    btnText.textContent = 'Sign Up';
                }
            }
        });
    </script>
</body>

</html>