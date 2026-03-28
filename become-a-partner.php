<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become A Partner</title>
<link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/become-a-partner.css">
 
</head>

<body>

    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

    <div class="become-partner-section">
        <div class="partner-hero">
            <div class="container">
                <h1>Grow Your Business with Serbisyos</h1>
                <p>Join our network of trusted auto repair shops in Iloilo and connect with thousands of car owners looking for your services.</p>
                <a href="login.php?redirect=account/become-a-partner" class="btn-partner">Apply Now for Free</a>
            </div>
        </div>

      <div class="container benefits-section">
    <h2 class="section-title">Why Partner with Us?</h2>
    <p class="section-subtitle">Joining our network opens up new opportunities for growth and success.</p>
    
    <div class="row g-4 justify-content-center">

        <div class="col-lg-4 ">
            <div class="benefit-card">
                <div class="card-header-flex">
                          <div class="card-top-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Expand Your Reach</h4>
                </div>
                <p>Enter your location and the service you need. Browse through a list of verified and top-rated auto repair shops near you.</p>
            </div>
        </div>

        <div class="col-lg-4 ">
            <div class="benefit-card">
                <div class="card-header-flex">
                          <div class="card-top-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4>Seamless Integration</h4>
              
                </div>
                <p>Read genuine reviews, and check the services offered to find the perfect shop that fits your needs.</p>
            </div>
        </div>

        <div class="col-lg-4 ">
            <div class="benefit-card">
                <div class="card-header-flex">
                       <div class="card-top-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Trusted Partnership</h4>
                 
                </div>
                <p>Schedule an appointment directly through our platform or contact the shop for emergency assistance. Get your car fixed hassle-free.</p>
            </div>
        </div>

    </div>
</div>

        <div class="how-it-works-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <img src="assets/img/how-it-works.webp" class="img-fluid rounded-3 shadow" alt="Mechanic writing on a clipboard">
                    </div>
                    <div class="col-lg-6">
                        <h2 class="section-title text-start">How It Works</h2>
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h5>Create Your Account</h5>
                                <p>First, sign up for a free user account on our platform.</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h5>Become a Partner</h5>
                                <p>Once you have an account, go to the "Become a Partner" page and fill out all the necessary requirements for your shop.</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h5>Submit for Review</h5>
                                <p>After submitting your application, our team will review your information. Once approved, you'll become a trusted partner, and customers will be able to find your shop on Serbisyos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <div class="container">
                <h2>Ready to Join?</h2>
                <p>Start your journey with Serbisyos today and take your business to the next level. It's free, simple, and effective.</p>
                <a href="signup.php?redirect=account/become-a-partner" class="btn-partner">Sign Up Now</a>
            </div>
        </div>
    </div>

     <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="js/script.js"></script>

</body>

</html>
