<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Serbisyos</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            background-color: #f8f9fa;
            color: #495057;
        }

        .legal-page-container {
            padding-top: 4rem;
            padding-bottom: 4rem;
        }

        .legal-content-wrapper {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .legal-content-wrapper h2 {
            font-weight: 700;
            color: #212529;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .legal-content-wrapper .section-title {
            font-weight: 600;
            color: #343a40;
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .legal-content-wrapper .section-title:first-of-type {
            margin-top: 0;
        }

        .legal-content-wrapper p, .legal-content-wrapper li {
            line-height: 1.75;
            font-size: 1rem;
        }

        .legal-content-wrapper ul {
            padding-left: 25px;
        }

        .legal-content-wrapper ul li {
            margin-bottom: 0.75rem;
        }
        
        .legal-content-wrapper ul li::marker {
            color: #0d6efd;
        }

        .legal-content-wrapper a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }

        .legal-content-wrapper a:hover {
            text-decoration: underline;
        }

        .legal-nav.sticky-top {
            top: 100px;
        }

        .legal-nav .nav-link {
            color: #6c757d;
            padding: 0.6rem 1rem;
            border-left: 3px solid transparent;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
        }

        .legal-nav .nav-link:hover {
            color: #ffc107;
            background-color: #f8f9fa;
            border-left-color: #ffc107;
        }

        .legal-nav .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
            border-left-color: #0d6efd;
        }
        
        @media (max-width: 991px) {
             .legal-page-container {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }

            .legal-content-wrapper {
                padding: 1.5rem;
            }

            .legal-content-wrapper h2 {
                font-size: 1.75rem;
                margin-bottom: 1.5rem;
            }

            .legal-content-wrapper .section-title {
                font-size: 1.25rem;
                margin-top: 2rem;
            }

            .legal-content-wrapper p, .legal-content-wrapper li {
                font-size: 0.95rem;
                line-height: 1.7;
            }
            .legal-page-container {
                margin-top: 80px;
            }
        }
    </style>
</head>

<body>

    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

    <div class="container legal-page-container">
        <div class="row gx-lg-5">
            <div class="col-lg-3 d-none d-lg-block">
                <nav class="legal-nav sticky-top">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#agreement">Agreement to Terms</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#accounts">User Accounts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services">Platform Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#conduct">User Content & Conduct</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#partners">Terms for Shop Owners</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#termination">Termination</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#modification">Modification of Terms</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact Us</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="col-lg-9">
                <div class="legal-content-wrapper p-4 p-md-5">
                    <h2 class="text-center mb-4">Terms and Conditions</h2>
                    <p class="text-muted text-center mb-5"><strong>Last Updated: October 5, 2025</strong></p>

                    <h4 id="agreement" class="section-title">1. Agreement to Terms</h4>
                    <p>Welcome to Serbisyos! By accessing or using the Serbisyos platform, you agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree to these terms, please do not use our services.</p>

                    <h4 id="accounts" class="section-title">2. User Accounts and Responsibilities</h4>
                    <ul>
                        <li><strong>Account Creation:</strong> You must provide accurate and complete information during registration. You are responsible for maintaining the confidentiality of your account password.</li>
                        <li><strong>Account Verification:</strong> To use the Appointment Booking and Emergency Request features, you must complete our identity verification process, which may require submitting a valid ID and a selfie.</li>
                        <li><strong>Email Verification:</strong> For manual registrations, you must verify your email via a One-Time Password (OTP) to access the platform. Accounts that are not verified within 24 hours will be automatically and permanently deleted.</li>
                        <li><strong>Account Security:</strong> You are responsible for all activities that occur under your account. We encourage you to enable Two-Factor Authentication (2FA) for enhanced security.</li>
                    </ul>

                    <h4 id="services" class="section-title">3. Platform Services and Disclaimers</h4>
                    <ul>
                        <li><strong>Directory Service:</strong> Serbisyos acts primarily as a directory and intermediary to connect vehicle owners with auto repair shops.</li>
                        <li><strong>No Guarantee of Service Quality:</strong> While we have a verification process for shops, we do not guarantee the quality, safety, or legality of the services provided by the listed shops. All transactions and interactions are solely between you and the shop owner.</li>
                        <li><strong>AI Chatbot:</strong> The AI Chatbot is provided for informational and preliminary purposes only. Its diagnostic tips and car part identification feature are not a substitute for a professional mechanical assessment. Serbisyos is not liable for any issues arising from advice given by the chatbot. The accuracy of the image recognition is not guaranteed.</li>
                    </ul>

                    <h4 id="conduct" class="section-title">4. User Content and Conduct</h4>
                    <ul>
                        <li><strong>Reviews and Ratings:</strong> You can submit reviews and ratings based on your experiences. You are solely responsible for the content you post.</li>
                        <li><strong>Shop Reporting:</strong> You can report shops for issues like inaccurate information, fake reviews, spam, scams, or inappropriate content. Reports will be reviewed by our administrators.</li>
                        <li><strong>Prohibited Activities:</strong> You agree not to use the platform to submit false information, harass other users, post fraudulent reviews, or engage in any illegal activities.</li>
                    </ul>

                    <h4 id="partners" class="section-title">5. Terms for Shop Owners ("Partners")</h4>
                    <ul>
                        <li><strong>Application:</strong> To be listed, you must complete the "Become a Partner" application and submit verifiable business documents for admin approval.</li>
                        <li><strong>Responsibility:</strong> You are responsible for keeping your shop information accurate and up-to-date, including your business hours, services offered, and contact details.</li>
                        <li><strong>Feature Configuration:</strong> You are responsible for managing your booking form settings and your availability for emergency services.</li>
                        <li><strong>Platform Rules:</strong> Shop owners are prohibited from booking appointments or sending emergency requests to their own shop.</li>
                    </ul>

                    <h4 id="termination" class="section-title">6. Termination</h4>
                    <ul>
                        <li><strong>By You:</strong> You may terminate your account at any time through the secure account deletion feature in your settings.</li>
                        <li><strong>By Serbisyos:</strong> We reserve the right to suspend or terminate your account if you violate these Terms and Conditions.</li>
                    </ul>

                    <h4 id="modification" class="section-title">7. Modification of Terms</h4>
                    <p>Serbisyos reserves the right to update or modify these terms and conditions at any time. Any changes will be posted on this page, and it is your responsibility to review them regularly.</p>

                    <h4 id="contact" class="section-title">8. Contact Us</h4>
                    <p>If you have any questions about this Terms and Conditions, please <a href="contact.php">contact us</a>.</p>
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