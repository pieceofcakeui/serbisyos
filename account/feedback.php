<?php
require_once 'backend/auth.php';
include 'backend/emergency-modal.php';
require_once 'backend/db_connection.php';

$user_id = $_SESSION['user_id'];
$user_name = '';
$user_email = '';

$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $user_name = $row['full_name'];
    $user_email = $row['email'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/feedback.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="feedback">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="section-header">
                            <h2>Share Your Feedback</h2>
                            <p>Help us improve Serbisyos - your auto repair directory. We value your experience with our
                                platform and partner shops.</p>
                        </div>

                        <div class="feedback-card">
                            <form id="contactForm">

                                <input type="hidden" id="feedbackName" name="name" value="<?php echo htmlspecialchars($user_name); ?>">
                                <input type="hidden" id="feedbackEmail" name="email" value="<?php echo htmlspecialchars($user_email); ?>">

                                <div class="form-group">
                                    <label for="feedbackSubject" class="form-label">Feedback Subject</label>
                                    <select class="form-select" id="feedbackSubject" name="subject" required>
                                        <option value="" selected disabled>Choose a category...</option>
                                        <option value="Bug Report">Bug Report (I found something broken)</option>
                                        <option value="Feature Suggestion">Feature Suggestion (I have an idea)</option>
                                        <option value="Shop Feedback">Shop Feedback (Praise or complaint about a shop)</option>
                                        <option value="Praise">Praise (I love Serbisyos!)</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="form-group" id="otherSubjectContainer" style="display: none;">
                                    <label for="otherSubject" class="form-label">Please specify:</label>
                                    <input type="text" class="form-control" id="otherSubject" name="other_subject">
                                </div>
                                <div class="form-group">
                                    <label for="feedbackMessage" class="form-label">Your Feedback</label>
                                    <textarea class="form-control" id="feedbackMessage" name="message"
                                        placeholder="Tell us about your experience with Serbisyos or our partner auto repair shops..."
                                        rows="5" required></textarea>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-warning btn-lg px-4 fw-bold">
                                        <span class="submit-text">Submit Feedback</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status"
                                            aria-hidden="true"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="value-section">
                    <div class="container">
                        <div class="section-header">
                            <h2>Why Your Feedback Matters</h2>
                            <p>Your input helps us improve Serbisyos and the auto repair shops in our directory</p>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="value-card">
                                    <div class="value-icon">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <h4>Better Auto Services</h4>
                                    <p>Your feedback helps us maintain quality standards for repair shops in our directory.
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="value-card">
                                    <div class="value-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <h4>Improve Platform</h4>
                                    <p>Suggestions help us add features that make finding reliable auto repair easier.</p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="value-card">
                                    <div class="value-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h4>Community Trust</h4>
                                    <p>Shared experiences help other car owners find trustworthy mechanics.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <h5 id="modalTitle" class="mb-3">Processing Your Feedback</h5>
                    <p id="modalMessage" class="mb-4">We're sending your message...</p>
                    <div class="d-flex justify-content-center">
                        <button id="modalOkBtn" type="button" class="btn btn-warning px-4" data-bs-dismiss="modal"
                            style="display: none;">Got It</button>
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
    <script src="../assets/js/feedback.js"></script>
    <script src="../assets/js/navbar.js"></script>

</body>

</html>