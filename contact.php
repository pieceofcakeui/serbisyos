<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Serbisyos</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
   <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/contact.css">

</head>

<body>

    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

    <div class="contacts-section">
        <div class="container">
            <div class="contact-header" style="text-align: center; margin-bottom: 40px;">
                <h1>Get In Touch</h1>
                <p style="font-size: 1.2rem; color: #666; max-width: 700px; margin: 1rem auto 0;">We're here to help! Whether you have a question about our services, need support, or want to give feedback, feel free to reach out to us. Our team is ready to assist you.</p>
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
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="message" placeholder="Your Message" required></textarea>
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

    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
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
                        <button id="modalOkBtn" type="button" class="btn btn-warning px-4" data-bs-dismiss="modal"
                            style="display: none;">Got It</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="js/filter-modal.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="js/script.js"></script>
    <script src="js/contact.js"></script>

</body>

</html>