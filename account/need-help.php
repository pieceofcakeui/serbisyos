<?php
require_once '../functions/auth.php';
include 'backend/emergency-modal.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Need help</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/need-help.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">

</head>
<body>

<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>
<?php include 'include/navbar.php'; ?>
<?php include 'include/modalForSignOut.php'; ?>
<?php include 'include/offline-handler.php'; ?>

<div id="main-content" class="main-content">

    <div class="help-support">
        <h1>Serbisyos Help & Support Center</h1>
        <p class="lead">Your comprehensive guide to using the Serbisyos Auto Repair Shop Directory. Find answers to common questions or contact our support team for assistance.</p>
        <section>
            <h2><i class="fas fa-question-circle"></i> Frequently Asked Questions</h2>
            
            <h3>For Customers</h3>
            <div class="faq-item">
                <div class="question">How do I search for auto repair shops in my area?</div>
                <p>Use our search feature at the top of the homepage. Enter your location (barangay, city, or address) and optionally specify the type of service you need. You can filter results by rating, or services offered. Click "Apply Filter" to see a list of nearby auto repair shops with their address and ratings.</p>
            </div>
            <div class="faq-item">
                <div class="question">How do reviews and ratings work?</div>
                <p>Customers can leave ratings (1-5 stars) and detailed reviews after creating an account and verifying their visit. Our system averages these ratings and displays them prominently. We have strict anti-fraud measures to ensure authentic reviews. You can sort shops by rating and read detailed customer experiences.</p>
            </div>
            <div class="faq-item">
                <div class="question">Can I book appointments through Serbisyos?</div>
                <p>Many shops offer direct booking through our platform. Look for the "Book Appointment" button on their profile. For shops without this feature, we provide direct contact information so you can schedule service directly. Some shops also offer real-time availability calendars.</p>
            </div>
            
            <h3>For Business Owners</h3>
            <div class="faq-item">
                <div class="question">How can I add my auto repair shop to the directory?</div>
                <p>Business owners can register for a business account through our <a href="become-a-partner.php">Become A Partner</a> page. You'll need to provide: business license information, contact details, services offered, hours of operation, and certifications. Our verification team typically processes new listings within 2-3 business days, and this service is free.</p>
            </div>
            <div class="faq-item">
                <div class="question">How do I update my shop's information?</div>
                <p>Logged-in business owners can access their profile and select "Edit Shop Profile" to make updates. You can instantly update photos, service offerings, and promotional information.</p>
            </div>
            <div class="faq-item">
                <div class="question">How can I respond to customer reviews?</div>
                <p>Business owners can claim their profile to respond to reviews. Once verified, you'll see a "Respond" button under each review. Professional, courteous responses are encouraged.</p>
            </div>
            
            <h3>Technical Support</h3>
            <div class="faq-item">
                <div class="question">How do I reset my password?</div>
                <p>Click "Forgot Password" on the login page and enter your registered email address. You'll receive a otp to verify your email and after verify it direct to create a new password. If you don't see the email, check your spam folder. For additional security, we recommend enabling two-factor authentication in your account settings.</p>
            </div>
            <div class="faq-item">
                <div class="question">How do I report incorrect information or abuse?</div>
                <p>Each listing has a "Report" button for flagging incorrect information or policy violations. For urgent matters, please contact our support team directly with details. We investigate all reports and typically respond within 24 hours regarding the status of your report.</p>
            </div>
        </section>
    </div>

    <div class="contacts-section">
        <div class="container">
            <div class="contact-header" style="text-align: center; margin-bottom: 40px;">
                <h1>Get In Touch</h1>
                <p style="font-size: 1.2rem; color: #666; max-width: 700px; margin: 1rem auto 0;">Still have questions? Send us a message and our team will get back to you shortly.</p>
            </div>
            <div class="contact-container">
                <div class="contact-info">
                    <h2>Contact Information</h2>
                    <p>We're here to help! Whether you have a question about our services or need support, feel free to reach out to us.</p>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>5000 Rizal Street, Iloilo City Proper</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span>support@serbisyos.com</span>
                    </div>
                </div>
                <div class="contact-form">
                    <h2>Send us a Message</h2>
                    <form id="contactForm" action="#" method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control-contact" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control-contact" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control-contact" name="subject" placeholder="Subject" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control-contact" name="message" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn-submit">
                            <span class="submit-text">Submit Message</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div> <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div id="modalIcon" class="mb-3">
                    <i class="fas fa-spinner fa-spin fa-3x text-warning" id="loadingIcon"></i>
                    <i class="fas fa-check-circle fa-3x text-success d-none" id="successIcon"></i>
                    <i class="fas fa-times-circle fa-3x text-danger d-none" id="errorIcon"></i>
                </div>
                <h5 id="modalTitle" class="mb-3">Processing Your Message</h5>
                <p id="modalMessage" class="mb-4">We're sending your message...</p>
                <div class="d-flex justify-content-center">
                    <button id="modalOkBtn" type="button" class="btn btn-warning px-4" data-bs-dismiss="modal" style="display: none;">Got It</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/emergency-modal.php'; ?>
<?php include 'include/help-toggle.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="../assets/js/script.js"></script>
<script src="../assets/js/navbar.js"></script>
<script src="../assets/js/contact.js"></script>

</body>
</html>