<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Serbisyos</title>

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
    <link rel="stylesheet" href="assets/css/about.css">

</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

    <div class="about-section">
        <div class="about-header">
            <div class="container">
                <h1>About Serbisyos</h1>
                <p>Serbisyos was founded with a clear mission to bridge the gap between vehicle owners and trustworthy local auto shops. We understand the challenges of finding reliable car care, which is why we've created a comprehensive directory focused on verified and high-quality service providers in Iloilo.</p>
                <p>Our platform is designed to be user-friendly and transparent, ensuring you can make informed decisions for your vehicle with confidence. For our partner shops, we provide the tools they need to grow their business and reach more customers in the digital world.</p>
            </div>
        </div>

        <div class="how-we-help-section">
            <div class="container">
                <h2 class="section-title" style="color: white;">How We Can Help</h2>
                <p class="section-subtitle">Whether you're a car owner or a shop owner, we have solutions for you.</p>
                <div class="help-grid">
                    <div class="help-card">
                        <i class="fas fa-car"></i>
                        <h4>For Car Owners</h4>
                        <p>Easily find verified shops, book appointments in advance, request emergency assistance, and chat directly with mechanics. Sign up for a free account to save your favorite shops, track your service history, and get personalized recommendations.</p>
                    </div>
                    <div class="help-card">
                        <i class="fas fa-tools"></i>
                        <h4>For Shop Owners</h4>
                        <p>Join our platform for free to boost your online visibility, streamline your booking process, and connect with a wider customer base in Iloilo. Earn badges for great service and build a trusted reputation in the community.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center mb-5">
                        <h2 class="section-title display-5 fw-bold">Frequently Asked Questions</h2>
                        <p class="lead" style="color: #666;">Find answers to common questions about our services</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button fw-semibold fs-5 py-3 collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false"
                                        aria-controls="collapseOne">
                                        What makes Serbisyos different from other auto service platforms?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse"
                                    aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Serbisyos goes beyond just listing auto repair shops—we focus on building trust and providing real value to vehicle owners. Our platform features only verified and reputable repair shops, complete with user reviews, service details, and contact information. Unlike other platforms, some of our partner shops also offer online booking and emergency assistance services, giving users added convenience when it matters most. With Serbisyos, you’re not just finding a repair shop—you’re finding peace of mind.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed fw-semibold fs-5 py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                        How do I find the right service provider for my needs?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Our platform allows you to filter providers by location, services offered, and
                                        customer ratings. Each profile includes detailed information about specialties,
                                        address, and customer reviews to help you make an informed decision.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="headingTFive">
                                    <button class="accordion-button collapsed fw-semibold fs-5 py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTFive" aria-expanded="false"
                                        aria-controls="collapseTFive">
                                        Is Serbisyos free to use?
                                    </button>
                                </h2>
                                <div id="collapseTFive" class="accordion-collapse collapse"
                                    aria-labelledby="headingTFive" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes! Using Serbisyos to find a service provider is completely free.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button collapsed fw-semibold fs-5 py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false"
                                        aria-controls="collapseFive">
                                        How do I schedule an appointment with a shop?
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        You can schedule an appointment directly through the shop’s profile page on our platform, if they have enabled the booking feature. Simply click the "Book Now" button, choose your preferred time and service, and send your request. If a shop does not offer online booking, you can use the contact information provided on their page to reach out directly.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="headingSix">
                                    <button class="accordion-button collapsed fw-semibold fs-5 py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false"
                                        aria-controls="collapseSix">
                                        What should I do if my car breaks down and I need immediate assistance?
                                    </button>
                                </h2>
                                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        For roadside emergencies, go to the emergency assistance section on our platform. You can find shops that offer emergency services, and our map will show you the closest available providers. You can also chat directly with mechanics to get help on the spot.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="headingSeven">
                                    <button class="accordion-button collapsed fw-semibold fs-5 py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false"
                                        aria-controls="collapseSeven">
                                        Can I review a shop I've used through Serbisyos?
                                    </button>
                                </h2>
                                <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes, we highly encourage it! After your service is complete, you will have the option to leave a rating and write a review on the shop’s profile. Your feedback is crucial for helping other users make informed decisions and for us to ensure service quality.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="headingEight">
                                    <button class="accordion-button collapsed fw-semibold fs-5 py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false"
                                        aria-controls="collapseEight">
                                        How does Serbisyos protect my personal information?
                                    </button>
                                </h2>
                                <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We take your privacy seriously. Serbisyos uses secure data encryption and follows strict privacy protocols to protect your personal information. We never share your details with third parties without your explicit consent. For more information, please read our <a href="privacy-policy.php">Privacy Policy.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="./assets/js/script.js"></script>
    <script src="js/script.js"></script>
    <script src="js/filter-modal.js"></script>
</body>

</html>