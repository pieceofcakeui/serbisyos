<?php
require_once '../functions/auth.php';
include 'backend/security_helper.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Tips</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/car-tips.css">

</head>

<body>

    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

     <div id="main-content" class="main-content">
    <div class="more-tips-section">
        <div class="tips-page-header">
            <div class="container">
                <h3>Vehicle Care Tips</h3>
                <p>Keep your car in top shape with these simple maintenance tips.</p>
            </div>
        </div>
        <div class="tips-main-content">
            <div class="container">
                <div class="tips-layout">
                    <aside class="tips-sidebar">
                        <div class="sidebar-widget">
                            <h4>Categories</h4>
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link active" id="v-pills-maintenance-tab" data-bs-toggle="pill" href="#v-pills-maintenance" role="tab" aria-controls="v-pills-maintenance" aria-selected="true">Maintenance</a>
                                <a class="nav-link" id="v-pills-safety-tab" data-bs-toggle="pill" href="#v-pills-safety" role="tab" aria-controls="v-pills-safety" aria-selected="false">Safety</a>
                                <a class="nav-link" id="v-pills-performance-tab" data-bs-toggle="pill" href="#v-pills-performance" role="tab" aria-controls="v-pills-performance" aria-selected="false">Performance</a>
                            </div>
                        </div>
                    </aside>
                    <div class="tips-article">
                        <div class="tips-nav-mobile">
                            <select class="form-select" onchange="showTabFromSelect(this.value)">
                                <option value="v-pills-maintenance-tab">Maintenance</option>
                                <option value="v-pills-safety-tab">Safety</option>
                                <option value="v-pills-performance-tab">Performance</option>
                            </select>
                        </div>
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-maintenance" role="tabpanel" aria-labelledby="v-pills-maintenance-tab">
                                <h2 id="maintenance">Regular Maintenance</h2>
                                <div class="tip-content-grid">
                                    <div>
                                        <div class="tip-item">
                                            <i class="fas fa-oil-can"></i>
                                            <div>
                                                <h5>Regular Oil Changes</h5>
                                                <p>Engine oil lubricates moving parts. Follow your car's manual for the recommended oil change interval to keep your engine healthy.</p>
                                            </div>
                                        </div>
                                        <div class="tip-item">
                                            <i class="fas fa-check-circle"></i>
                                            <div>
                                                <h5>Check Your Tire Pressure</h5>
                                                <p>Properly inflated tires are crucial for safety and fuel efficiency. Check the pressure at least once a month.</p>
                                            </div>
                                        </div>
                                        <div class="tip-item">
                                            <i class="fas fa-tint"></i>
                                            <div>
                                                <h5>Monitor Fluid Levels</h5>
                                                <p>Regularly check your car's essential fluids, including coolant, brake fluid, and windshield washer fluid.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="article-image">
                                        <img src="../assets/img/car-tips/maintenance.jpeg" alt="Maintenance">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-safety" role="tabpanel" aria-labelledby="v-pills-safety-tab">
                                <h2 id="safety">Safety Checks</h2>
                                <div class="tip-content-grid">
                                    <div>
                                        <div class="tip-item">
                                            <i class="fas fa-car-side"></i>
                                            <div>
                                                <h5>Inspect Your Brakes</h5>
                                                <p>Listen for any squeaking or grinding noises. If you notice any changes in brake performance, have them inspected immediately.</p>
                                            </div>
                                        </div>
                                        <div class="tip-item">
                                            <i class="fas fa-lightbulb"></i>
                                            <div>
                                                <h5>Check Your Lights</h5>
                                                <p>Walk around your car and check that all lights are working, including headlights, taillights, and turn signals.</p>
                                            </div>
                                        </div>
                                        <div class="tip-item">
                                            <i class="fas fa-car"></i>
                                            <div>
                                                <h5>Replace Wiper Blades</h5>
                                                <p>Worn-out wiper blades can be a major safety hazard during heavy rain. Replace them every 6 to 12 months.</p>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="article-image">
                                        <img src="../assets/img/car-tips/safety.jpeg" alt="Safety">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-performance" role="tabpanel" aria-labelledby="v-pills-performance-tab">
                                <h2 id="performance">Performance & Efficiency</h2>
                                <div class="tip-content-grid">
                                    <div>
                                        <div class="tip-item">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <div>
                                                <h5>Don't Ignore Warning Lights</h5>
                                                <p>If a warning light comes on, have it checked by a professional as soon as possible to prevent minor issues from becoming major problems.</p>
                                            </div>
                                        </div>
                                        <div class="tip-item">
                                            <i class="fas fa-leaf"></i>
                                            <div>
                                                <h5>Replace Air Filter</h5>
                                                <p>A clean air filter improves airflow to the engine, increasing performance and fuel efficiency. Check it during every oil change.</p>
                                            </div>
                                        </div>
                                        <div class="tip-item">
                                            <i class="fas fa-weight-hanging"></i>
                                            <div>
                                                <h5>Lighten the Load</h5>
                                                <p>Unnecessary weight in your car can reduce fuel efficiency. Remove any heavy items you don't need for your daily commute.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="article-image">
                                        <img src="../assets/img/car-tips/performance.jpeg" alt="Performance">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="emergency-prep-section">
            <div class="container">
                <h2 class="section-title">Emergency Preparedness</h2>
                <p class="section-subtitle">Be ready for anything on the road. Here's what you need to know.</p>
                <div class="prep-grid">
                    <div class="prep-item">
                        <img src="../assets/img/car-tips/kit.jpeg" alt="Emergency Kit">
                        <div>
                            <h4>Build an Emergency Kit</h4>
                            <ul>
                                <li>Jumper Cables</li>
                                <li>Flashlight & Batteries</li>
                                <li>First-Aid Kit</li>
                                <li>Basic Tool Set</li>
                                <li>Early Warning Device</li>
                            </ul>
                        </div>
                    </div>
                    <div class="prep-item">
                        <img src="../assets/img/car-tips/breakdown.jpg" alt="Breakdown">
                        <div>
                            <h4>What to Do in a Breakdown</h4>
                            <ul>
                                <li>Pull over to a safe location</li>
                                <li>Turn on your hazard lights</li>
                                <li>Stay inside your vehicle</li>
                                <li>Call for emergency assistance</li>
                                <li>Use the Serbisyos app to find help</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script>
        function showTabFromSelect(tabId) {
            var someTabTriggerEl = document.querySelector('#' + tabId)
            var tab = new bootstrap.Tab(someTabTriggerEl)
            tab.show()
        }
    </script>

</body>

</html>