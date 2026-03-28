<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Serbisyos</title>
    
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            background-color: #f8f9fa;
            color: #495057;
        }

        .privacy-page-container {
            padding-top: 4rem;
            padding-bottom: 4rem;
        }

        .privacy-content-wrapper {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .privacy-content-wrapper h2 {
            font-weight: 700;
            color: #212529;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .privacy-content-wrapper .section-title {
            font-weight: 600;
            color: #343a40;
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .privacy-content-wrapper .section-title:first-of-type {
            margin-top: 0;
        }

        .privacy-content-wrapper h5 {
            font-weight: 600;
            color: #495057;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
        }

        .privacy-content-wrapper p, .privacy-content-wrapper li {
            line-height: 1.75;
            font-size: 1rem;
        }

        .privacy-content-wrapper ul {
            padding-left: 25px;
        }

        .privacy-content-wrapper ul li {
            margin-bottom: 0.75rem;
        }
        
        .privacy-content-wrapper ul li::marker {
            color: #0d6efd;
        }

        .privacy-content-wrapper a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }

        .privacy-content-wrapper a:hover {
            text-decoration: underline;
        }

        .privacy-nav.sticky-top {
            top: 100px;
        }

        .privacy-nav .nav-link {
            color: #6c757d;
            padding: 0.6rem 1rem;
            border-left: 3px solid transparent;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
        }

        .privacy-nav .nav-link:hover {
            color: #ffc107;
            background-color: #f8f9fa;
            border-left-color: #ffc107;
        }

        .privacy-nav .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
            border-left-color: #0d6efd;
        }
        
        @media (max-width: 768px) {
            .privacy-page-container {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }

            .privacy-content-wrapper {
                padding: 1.5rem;
            }

            .privacy-content-wrapper h2 {
                font-size: 1.75rem;
                margin-bottom: 1.5rem;
            }

            .privacy-content-wrapper .section-title {
                font-size: 1.25rem;
                margin-top: 2rem;
            }

            .privacy-content-wrapper h5 {
                font-size: 1.1rem;
            }

            .privacy-content-wrapper p, .privacy-content-wrapper li {
                font-size: 0.95rem;
                line-height: 1.7;
            }
            .privacy-page-container{
                margin-top: 80px;
            }
        }
    </style>
</head>

<body>

    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

    <div class="container privacy-page-container">
        <div class="row gx-lg-5">
            <div class="col-lg-3 d-none d-lg-block">
                <nav class="privacy-nav sticky-top">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#introduction">Introduction</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#information-collection">Information We Collect</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#how-we-use">How We Use Your Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#data-security">Data Security</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#your-rights">Your Rights and Choices</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#policy-changes">Changes to This Policy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact-us">Contact Us</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="col-lg-9">
                <div class="privacy-content-wrapper p-4 p-md-5">
                    <h2 class="text-center">Privacy Policy</h2>
                    <p class="text-muted text-center mb-5"><strong>Last Updated: October 5, 2025</strong></p>

                    <h4 id="introduction" class="section-title">1. Introduction</h4>
                    <p>Welcome to Serbisyos. This Privacy Policy explains how we collect, use, protect, and handle your personal information when you use our website and services. Serbisyos is an online directory service designed to help vehicle owners find and connect with auto repair shops. By using our platform, you agree to the collection and use of information in accordance with this policy.</p>

                    <h4 id="information-collection" class="section-title">2. Information We Collect</h4>
                    <p>We collect various types of information to provide and improve our service to you.</p>
                    <h5>A. Information You Provide to Us:</h5>
                    <ul>
                        <li><strong>Account Registration:</strong> When you create an account manually or via Google, we collect information such as your name and email address to set up your profile.</li>
                        <li><strong>Account Verification:</strong> To use features like booking and write a review, you must complete account verification. This process requires your full name, gender, birthday, address, a photo of a valid ID (National ID or Driver's License), and a live selfie.</li>
                        <li><strong>"Become a Partner" Application:</strong> When you apply to list a shop, we collect business information, contact details, location data, and official documents like a Business Permit for admin verification.</li>
                        <li><strong>Communications:</strong> We collect information you provide when you communicate with shop owners through our real-time chat system, submit feedback about the platform, or interact with our AI Chatbot.</li>
                    </ul>

                    <h5>B. Information Collected Automatically:</h5>
                    <ul>
                        <li><strong>Location Data:</strong> To recommend nearby shops, we use your device's GPS to pinpoint your location in real-time.</li>
                        <li><strong>Usage and Session Data:</strong> Our Active Session Management feature records the device used and the time of your login. We also maintain an activity log of user actions for security purposes.</li>
                        <li><strong>Chatbot Interaction Data:</strong> We collect your text queries and any images you upload for car part identification when you use the AI Chatbot.</li>
                    </ul>

                    <h4 id="how-we-use" class="section-title">3. How We Use Your Information</h4>
                    <p>We use the information we collect for the following purposes:</p>
                    <ul>
                        <li><strong>To Provide and Manage Services:</strong> To operate the platform, facilitate appointment bookings, process emergency requests, and display relevant shop information based on your location.</li>
                        <li><strong>To Ensure Security and Trust:</strong> To verify user accounts, verify the legitimacy of shop owners, and secure your account with features like OTP and Two-Factor Authentication (2FA).</li>
                        <li><strong>To Communicate With You:</strong> To send important notifications via your on-site account and email regarding booking confirmations, emergency requests, and new messages.</li>
                        <li><strong>To Prevent Fraud:</strong> We use Google reCAPTCHA v3 to prevent bot registrations and secure login and password recovery flows.</li>
                    </ul>

                    <h4 id="data-security" class="section-title">4. Data Security</h4>
                    <p>We are committed to protecting your data. We implement a multi-layered security framework that includes:</p>
                    <ul>
                        <li><strong>Encryption:</strong> We secure sensitive user data using AES-256 encryption in CBC mode.</li>
                        <li><strong>Authentication:</strong> We offer secure login via OTP for manual sign-ups and 2FA via authenticator apps for all users.</li>
                        <li><strong>Session Management:</strong> You can view all active login sessions for your account and have the option to remotely log out any specific session.</li>
                        <li><strong>Secure Deletion:</strong> When you choose to delete your account, you must confirm the action by typing "DELETE MY ACCOUNT" to prevent accidental deletion, after which all your associated data is permanently erased.</li>
                    </ul>

                    <h4 id="your-rights" class="section-title">5. Your Rights and Choices</h4>
                    <p>You have control over your personal information:</p>
                    <ul>
                        <li><strong>Data Download:</strong> You have the right to request and download a copy of your personal data. For shop owners, this includes their submitted shop application details.</li>
                        <li><strong>Account Deletion:</strong> You have the option to permanently erase your account and all associated data from our system at any time.</li>
                        <li><strong>Location Services:</strong> You can control location data sharing through your web browser or device settings. Please note that disabling location services will limit your ability to use core features like finding nearby shops.</li>
                    </ul>
                    
                    <h4 id="policy-changes" class="section-title">6. Changes to This Privacy Policy</h4>
                    <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>

                    <h4 id="contact-us" class="section-title">7. Contact Us</h4>
                    <p>If you have any questions about this Privacy Policy, please <a href="contact.php">contact us</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>

</body>

</html>