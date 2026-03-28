<?php
$lifetime = 30 * 24 * 60 * 60;
ini_set('session.gc_maxlifetime', $lifetime);
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: account/home"); 
    exit();
}

define('ACCESS_ALLOWED', true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iloilo Auto Repair Directory | Serbisyos</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <meta name="description"
        content="Need auto repair in Iloilo City? Instantly book appointments or request emergency help from trusted shops with Serbisyos. Browse services & read real reviews.">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Serbisyos",
  "url": "https://serbisyos.com/"
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Serbisyos",
  "url": "https://serbisyos.com/",
  "logo": "https://serbisyos.com/assets/img/favicon.png",
  "contactPoint": {
    "@type": "ContactPoint",
    "email": "support@serbisyos.com",
    "contactType": "Customer Support",
    "areaServed": "PH",
    "availableLanguage": "en"
  },
  "sameAs": [
    "https://www.facebook.com/profile.php?id=61582804589086",
    "https://www.tiktok.com/@serbisyos?_t=ZS-90qgugoEX0T&_r=1"
  ]
}
</script>
    <style>
        .toast-info {
            background-color: #17a2b8 !important;
            color: white !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            padding: 12px 16px;
            position: fixed !important;
            top: 20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            z-index: 9999;
            opacity: 1 !important;
            text-align: center;
            min-width: 250px;
            max-width: 90%;
            margin: 0 auto;
            transition: opacity 0.3s ease;
        }

        .partner-logos {
            padding: 80px 0;
            background-color: #f8f9fa;
            text-align: center;
        }

        .partner-logo-carousel {
            margin-top: 50px;
        }

        .logo-item-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 120px;
            padding: 10px;
            box-sizing: border-box;
            outline: none;
        }

        .logo-item-wrapper img {
            width: 100%;
            height: 100px;
            object-fit: contain;
            opacity: 0.8;
            transition: opacity 0.3s ease;
            border-radius: 5px;
        }

        .logo-item-wrapper img:hover {
            opacity: 1;
        }

        .logo-fallback-text {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            width: 90%;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            line-height: 1.3;
            text-align: center;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .logo-fallback-text:hover {
            opacity: 1;
        }
    </style>
</head>

<body>

    <?php include 'offline-handler.php'; ?>
    <?php include 'include/navbar.php'; ?>

    <div class="landing-page">

        <div class="hero-home">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0">
                        <div class="hero-text mx-auto ms-lg-0" id="secret-admin-trigger">
                            <h1>Serbisyos <span style="color:#ff9800;"><br>Your Trusted Auto Repair Shop Directory in
                                    Iloilo</span></h1>
                            <p>Quickly find verified auto repair shops in Iloilo, Philippines, book appointments,
                                request emergency assistance, chat with shop owners, and get AI-powered tips and
                                diagnostics—all in one secure platform.</p>
                            <div class="hero-cta-buttons d-flex justify-content-center justify-content-lg-start gap-3">

                                <?php
                                $is_logged_in = isset($_SESSION['user_id']);

                                if ($is_logged_in) {
                                    $dashboard_link = 'account/home';
                                    ?>

                                    <a href="<?php echo $dashboard_link; ?>" class="hero-btn">
                                        Go to Dashboard
                                    </a>

                                    <?php
                                } else {
                                    ?>

                                    <a href="signup.php" class="hero-btn">
                                        Get Started <i class="fas fa-arrow-right ms-2"></i>
                                    </a>

                                    <?php
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-image">
                            <img src="assets/img/partner/landing1.webp" alt="Mechanic working on a car engine"
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="how-it-works">
            <div class="container">
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle">Finding a trusted mechanic is as easy as 1-2-3.</p>
                <div class="how-it-works-grid">

                    <div class="how-it-works-card animate-on-scroll card-border-blue">
                        <div class="card-icon card-icon-blue"><i class="fas fa-search-location"></i></div>
                        <h3>Search for a Service</h3>
                        <p>Enter your location and the service you need. Browse through a list of verified and top-rated
                            auto repair shops near you.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Location-based searching</li>
                            <li>Verified & rated listings</li>
                        </ul>
                    </div>

                    <div class="how-it-works-card animate-on-scroll card-border-blue">
                        <div class="card-icon card-icon-blue"><i class="fas fa-balance-scale"></i></div>
                        <h3>Compare and Choose</h3>
                        <p>Read genuine reviews, and check the services offered to find the perfect shop that fits your
                            needs.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Genuine customer reviews</li>
                            <li>Detailed list of services offered</li>
                        </ul>
                    </div>

                    <div class="how-it-works-card animate-on-scroll card-border-blue">
                        <div class="card-icon card-icon-blue"><i class="fas fa-calendar-check"></i></div>
                        <h3>Book and Get Serviced</h3>
                        <p>Schedule an appointment directly through our platform or contact the shop for emergency
                            assistance. Get your car fixed hassle-free.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Direct appointment scheduling</li>
                            <li>Emergency contact option</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <?php
        include 'functions/db_connection.php';

        $partner_shops = [];
        $sql_partners = "SELECT shop_name, shop_logo FROM shop_applications WHERE status = 'Approved'";
        $result_partners = $conn->query($sql_partners);

        if ($result_partners && $result_partners->num_rows > 0) {
            while ($row = $result_partners->fetch_assoc()) {
                $partner_shops[] = $row;
            }
        }
        ?>

        <section class="partner-logos">
            <div class="container">
                <h2 class="section-title">Our Trusted Partner Shops</h2>

                <div class="partner-logo-carousel">

                    <?php
                    if (!empty($partner_shops)):
                        ?>
                        <?php
                        foreach ($partner_shops as $shop):
                            ?>
                            <div class="logo-item-wrapper"> <?php
                            if (!empty($shop['shop_logo'])):
                                $logoPath = 'account/uploads/shop_logo/' . htmlspecialchars($shop['shop_logo']);
                                ?>
                                    <img src="<?php echo $logoPath; ?>"
                                        alt="<?php echo htmlspecialchars($shop['shop_name']); ?> Logo">

                                <?php else: ?>
                                    <span class="logo-fallback-text">
                                        <?php echo htmlspecialchars($shop['shop_name']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <p>More trusted shops coming soon!</p>
                    <?php endif; ?>

                </div>
            </div>
        </section>

        <section class="emergency-section">
            <div class="container">
                <div class="emergency-layout">
                    <div class="emergency-content">
                        <h2 class="section-title">Emergency Roadside Assistance</h2>
                        <p class="section-subtitle">Stuck on the road? Find immediate help from our partner shops
                            offering emergency services, available when you need them most.</p>
                        <a href="emergency-help.php" class="emergency-btn btn-danger">Get Help Now</a>
                    </div>
                    <div class="emergency-image-container">
                        <img src="assets/img/partner/emergency.webp"
                            alt="Car broken down with a flat tire on the side of the road" />
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features">
            <div class="container">
                <h2 class="section-title">Why Choose Serbisyos?</h2>
                <p class="section-subtitle">Everything you need to find trusted auto repair services or grow your auto
                    repair business</p>

                <div class="features-grid">

                    <div class="feature-card animate-on-scroll card-border-green">
                        <div class="card-icon card-icon-green"><i class="fas fa-map-marked-alt"></i></div>
                        <h3>Find Nearby Shops</h3>
                        <p>Instantly find verified auto repair shops near your current location with our location-based
                            search and recommendations.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Location-based search</li>
                            <li>Instant recommendations</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-green">
                        <div class="card-icon card-icon-green"><i class="far fa-calendar-alt"></i></div>
                        <h3>Book in Advance</h3>
                        <p>Schedule your car maintenance and repair services ahead of time to fit your busy schedule. No
                            more waiting in long lines.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Online scheduling</li>
                            <li>Fit your busy schedule</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-green">
                        <div class="card-icon card-icon-green"><i class="fas fa-comments"></i></div>
                        <h3>Chat with Shop Owner</h3>
                        <p>Communicate directly with shop owners to ask questions, get quotes, and discuss your
                            vehicle's needs before you book.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Direct messaging</li>
                            <li>Get quotes easily</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-green">
                        <div class="card-icon card-icon-green"><i class="fas fa-star"></i></div>
                        <h3>Verified Reviews</h3>
                        <p>Read authentic reviews from real customers and make informed decisions. Only verified
                            partners are featured on our platform.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Authentic customer feedback</li>
                            <li>Network of trusted partners</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-green">
                        <div class="card-icon card-icon-green"><i class="fas fa-chart-line"></i></div>
                        <h3>Business Growth</h3>
                        <p>For auto repair shops: Join our platform for FREE and grow your customer base, manage your
                            profile, and boost your business visibility.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Free partner registration</li>
                            <li>Customer base expansion</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-green">
                        <div class="card-icon card-icon-green"><i class="fas fa-car"></i></div>
                        <h3>Complete Automotive</h3>
                        <p>From routine maintenance to emergency repairs, find specialized services for all vehicle
                            types through our trusted partner network.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Wide range of services</li>
                            <li>Support for all vehicle types</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="exclusive-features">
            <div class="container">
                <h2 class="section-title">Unlock Exclusive Features</h2>
                <p class="section-subtitle">Sign up for a free account to get access to powerful tools that make car
                    care even easier.</p>
                <div class="features-grid">

                    <div class="feature-card animate-on-scroll card-border-purple">
                        <div class="card-icon card-icon-purple"><i class="fas fa-robot"></i></div>
                        <h3>AI & Chat System</h3>
                        <p>Use our AI chatbot for quick diagnostics and chat directly for shop owner and seamless
                            communication.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>AI-powered diagnostics</li>
                            <li>Direct chat with shop owner</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-purple">
                        <div class="card-icon card-icon-purple"><i class="fas fa-star-half-alt"></i></div>
                        <h3>Write a Review</h3>
                        <p>Share your experience to help other car owners. Your feedback helps us maintain a
                            high-quality network of shops.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Share your experience</li>
                            <li>Contribute to community</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-purple">
                        <div class="card-icon card-icon-purple"><i class="fas fa-bell"></i></div>
                        <h3>Get Notifications</h3>
                        <p>Receive updates on your booking status and get recommendations for top-rated or nearby shops
                            tailored just for you.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Booking status updates</li>
                            <li>Personalized recommendations</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-purple">
                        <div class="card-icon card-icon-purple"><i class="fas fa-bookmark"></i></div>
                        <h3>Save Favorite Shops</h3>
                        <p>Keep a list of your preferred auto shops for quick and easy access the next time you need a
                            service.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Create a preferred list</li>
                            <li>Quick access to shops</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-purple">
                        <div class="card-icon card-icon-purple"><i class="fas fa-history"></i></div>
                        <h3>Track Service History</h3>
                        <p>Maintain a digital log of all your vehicle repairs and maintenance booked through Serbisyos
                            for easy reference.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Digital repair & maintenance log</li>
                            <li>Easy historical reference</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-purple">
                        <div class="card-icon card-icon-purple"><i class="fas fa-user-cog"></i></div>
                        <h3>Personalized Dashboard</h3>
                        <p>Manage your appointments, communications, and saved shops all in one convenient, easy-to-use
                            dashboard.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Appointment management</li>
                            <li>Unified control panel</li>
                        </ul>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 3rem;">
                    <a href="signup.php" class="btn-primary">Sign Up for Free</a>
                </div>
            </div>
        </section>

        <section class="for-partners">
            <div class="container">
                <h2 class="section-title">Become a Serbisyos Partner</h2>
                <p class="section-subtitle">Join our network and accelerate your business growth. We are currently
                    accepting partner applications from shops within Iloilo Province only. It's free to apply!</p>
                <div class="features-grid">

                    <div class="feature-card animate-on-scroll card-border-red">
                        <div class="card-icon card-icon-red"><i class="fas fa-bullhorn"></i></div>
                        <h3>Boost Your Visibility</h3>
                        <p>Get discovered by thousands of potential customers in your area who are actively looking for
                            auto repair services.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Showcase in local search</li>
                            <li>Attract new customers</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-red">
                        <div class="card-icon card-icon-red"><i class="fas fa-tasks"></i></div>
                        <h3>Streamline Bookings</h3>
                        <p>Manage your appointments and schedules effortlessly with our built-in booking system,
                            reducing no-shows and administrative work.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>Booking management system</li>
                            <li>Reduce no-shows & admin</li>
                        </ul>
                    </div>

                    <div class="feature-card animate-on-scroll card-border-red">
                        <div class="card-icon card-icon-red"><i class="fas fa-award"></i></div>
                        <h3>Earn Badges & Trust</h3>
                        <p>Build credibility with 'Top Rated' badges for 4-5 star reviews and 'Most Booked' badges for
                            10+ bookings, helping you stand out.</p>
                        <h4>Key Features:</h4>
                        <ul>
                            <li>'Top Rated' status badge</li>
                            <li>'Most Booked' recognition</li>
                        </ul>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 3rem;">
                    <a href="login.php?redirect=account/become-a-partner" class="btn-primary">Apply Now for Free</a>
                </div>
            </div>
        </section>

        <section class="contact-us">
            <div class="container">
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">Have a question or need support? We're here to help. Reach out to us
                    anytime.</p>
                <div class="contact-grid">
                    <div class="contact-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h4>Our Location</h4>
                                <p>5000 Rizal Street, Iloilo City Proper</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h4>Email Us</h4>
                                <p>support@serbisyos.com</p>
                            </div>
                        </div>
                        
                    </div>
                    <div class="contact-action">
                        <h3>Have Questions?</h3>
                        <p>Our team is ready to assist you. For any inquiries, please visit our dedicated contact page
                            where you can send us a detailed message.</p>
                        <a href="contact.php" class="btn-primary">Go to Contact Page</a>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'functions/quick-stats.php'; ?>

        <section class="stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $approvedShops; ?>+</div>
                        <div class="stat-label">Partner Shops</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $happyUsers; ?>+</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">AI Support</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Free to Join</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="join" class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2>Ready to Get Started?</h2>
                    <p>Whether you're looking for trusted auto repair services or want to grow your auto repair
                        business, Serbisyos is here to help you succeed.</p>
                    <div class="cta-buttons">
                        <a href="<?php echo BASE_URL; ?>/service" class="btn-primary">Find Services Now</a>
                        <a href="login.php?redirect=account/become-a-partner" class="btn-secondary">Apply as Partner
                            (FREE)</a>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <script>
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        function animateCounter(element, target, duration) {
            let start = 0;
            const increment = target / (duration / 16);

            function updateCounter() {
                start += increment;
                if (start < target) {
                    element.textContent = Math.floor(start) + (element.dataset.suffix || '');
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target + (element.dataset.suffix || '');
                }
            }
            updateCounter();
        }

        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = entry.target.querySelectorAll('.stat-number');
                    statNumbers.forEach(stat => {
                        const text = stat.textContent;

                        if (text.includes('/')) {
                            return;
                        }

                        stat.dataset.suffix = text.replace(/[0-9]/g, '');
                        const number = parseInt(text.replace(/\D/g, ''));
                        if (number && !stat.classList.contains('animated')) {
                            stat.classList.add('animated');
                            animateCounter(stat, number, 2000);
                        }
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        const statsSection = document.querySelector('.stats');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }
    </script>
    <script>
        let clickCount = 0;
        const secretTriggerElement = document.getElementById('secret-admin-trigger');

        if (secretTriggerElement) {
            secretTriggerElement.addEventListener('click', function () {
                clickCount++;

                if (clickCount >= 5) {
                    fetch('set_admin_access.php', { method: 'POST' })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = 'admin/login.php';
                            }
                        });
                }

                setTimeout(() => {
                    if (clickCount < 5) {
                        clickCount = 0;
                    }
                }, 3000);
            });
        }
    </script>

    <script>
        $(document).ready(function () {
            $('.partner-logo-carousel').slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: false,
                dots: false,
                pauseOnHover: true,
                infinite: true,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 4,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 3,
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        });
    </script>
  
</body>

</html>