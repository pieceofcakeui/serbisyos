<?php require_once '../functions/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
</head>

<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

      <div id="main-content" class="main-content">
    <div class="terms-and-conditions">
        <section class="terms-section">
            <div class="terms-conditions">
                <h2 class="text-center mb-4">Terms and Conditions</h2>
                <p>Welcome to Serbisyos! By using our website, you agree to comply with and be bound by the following
                    terms and conditions of use. Please read these terms carefully before using this website.</p>
                <h4>1. General Terms</h4>
                <p>These terms and conditions apply to all users of the Serbisyos website, including visitors, service
                    providers, and customers. By accessing this website, you accept these terms and conditions in full.
                </p>

                <h4>2. Use of Services</h4>
                <p>Serbisyos provides a platform for users to explore various auto repair services offered by
                    third-party repair shops. We are not directly responsible for the services provided by these shops.
                    By engaging in any services, you acknowledge that the repair shops are solely responsible for their
                    services, including quality, pricing, and availability.</p>

                <h4>3. User Responsibilities</h4>
                <p>You agree not to use the website for any unlawful purposes. You will not interfere with or disrupt
                    the functioning of the website, including its servers or networks. You are responsible for ensuring
                    the accuracy of any information you submit to Serbisyos.</p>

                <h4>4. Service Availability</h4>
                <p>Serbisyos is not responsible for making with repair shops. All service arrangements must be handled
                    directly with the respective repair shop. The availability of services may vary depending on the
                    location and shop hours.</p>

                <h4>5. Pricing</h4>
                <p>Prices for services are determined by the individual repair shops and are subject to change without
                    notice. Serbisyos is not responsible for any pricing discrepancies or issues related to payments.
                </p>

                <h4>6. Limitation of Liability</h4>
                <p>Serbisyos is not liable for any damage, loss, or injury resulting from the use of our website or
                    services provided by third-party repair shops. You use our website at your own risk.</p>

                <h4>7. Third-Party Links</h4>
                <p>The website may contain links to third-party websites. Serbisyos is not responsible for the content
                    or accuracy of these external sites and does not endorse them in any way.</p>

                <h4>8. Privacy and Data Protection</h4>
               <p>We value your privacy and are committed to protecting your personal data. Please refer to our <a href="privacy-and-policy.php" title="Privacy Policy">Privacy Policy</a> for detailed information on how we collect, use, and protect your data.</p>

                <h4>9. Modification of Terms</h4>
                <p>Serbisyos reserves the right to update or modify these terms and conditions at any time. Any changes
                    will be posted on this page, and it is your responsibility to review them regularly.</p>

                <h4>10. Governing Law</h4>
                <p>These terms and conditions are governed by the laws of the jurisdiction where Serbisyos operates. Any
                    disputes arising from the use of the website will be subject to the exclusive jurisdiction of the
                    courts in that jurisdiction.</p>

                <h4>11. Contact Us</h4>
                <p>If you have any questions or concerns about our terms and conditions, please contact us at
                    support@serbisyos.com.</p>
            </div>
        </section>
    </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../assets/js/script.js"></script>
     <script src="../assets/js/navbar.js"></script>

</body>

</html>