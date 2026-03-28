<?php
require_once '../functions/auth.php';
include 'backend/emergency-requests.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Assistance Requests</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/emergency-requests.css">
    <style>
        #processingModal .progress-bar {
            transition: width 0.3s ease-in-out;
        }

        #processingModal .percentage-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        #processingModal .status-text {
            font-size: 1rem;
            color: #555;
        }
    </style>
</head>

<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="container py-4">
            <div class="d-flex justify-content-start mb-3">
                <a href="javascript:history.back()" class="px-4 py-2 text-black text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>

            <div id="form-intro-header" class="row justify-content-center mb-4">
                <div class="col-md-10 col-lg-8 text-center">
                    <h2 class="mb-3">Emergency Roadside Assistance</h2>
                    <?php if ($can_request): ?>
                        <p class="lead">Need immediate help? Fill out this quick 3-step form and we'll dispatch assistance to your location right away.</p>
                        <div class="alert alert-danger d-inline-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            For life-threatening emergencies, please call local authorities first
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($emergency_hours)): ?>
                        <div style="text-align: center;">
                            <div class="alert alert-info d-inline-block" style="text-align: center;">
                                <div style="text-align: center;">
                                    <strong class="d-block mb-2">Shop Emergency Availability:</strong>
                                    <div class="d-flex flex-wrap justify-content-center gap-2" style="justify-content: center;">
                                        <?php foreach ($emergency_hours as $time_slot):
                                            $parts = explode(", ", $time_slot);
                                            $day = $parts[0];
                                            $time = isset($parts[1]) ? $parts[1] : '';
                                            $formatted_time = '';

                                            if (!empty($time)) {
                                                $time_range = explode(' - ', $time);
                                                if (count($time_range) === 2) {
                                                    $start = date("h:i A", strtotime($time_range[0]));
                                                    $end = date("h:i A", strtotime($time_range[1]));
                                                    $formatted_time = "$start - $end";
                                                } else {
                                                    $formatted_time = date("h:i A", strtotime($time));
                                                }
                                            }
                                        ?>
                                            <span class="badge bg-light text-dark border" style="text-align: center;">
                                                <strong><?php echo htmlspecialchars($day) ?>:</strong> <?php echo htmlspecialchars($formatted_time) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>



            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <?php if (!$can_request): ?>
                        <div class="alert alert-warning">
                            You can't request another service at this shop or other shop because you already have a pending or accepted emergency request.
                            Check your <a href="my-emergency-request.php" class="text-decoration-underline">emergency request page</a>
                            for the status of your current request.
                        </div>
                    <?php else: ?>
                        <div class="emergency-container" id="emergencyFormContainer">
                            <div class="emergency-header">
                                <div class="d-flex align-items-center w-100">
                                    <div class="flex-shrink-0">
                                        <div class="emergency-icon-circle">
                                            <i class="fas fa-exclamation-triangle fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="mb-0 fw-bold fs-6 text-dark">Emergency Assistance Request</h5>
                                        <p class="mb-0 small opacity-75">We'll dispatch help immediately</p>
                                    </div>
                                </div>
                            </div>

                            <div class="emergency-body">
                                <form id="emergencyRequestForm" novalidate onsubmit="return false;" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="shop_id" value="<?= htmlspecialchars($shop['id']) ?>">
                                    <input type="hidden" name="shop_user_id" value="<?= htmlspecialchars($shop['user_id']) ?>">
                                    <input type="hidden" id="emergencyLatitude" name="latitude">
                                    <input type="hidden" id="emergencyLongitude" name="longitude">

                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="text-muted small me-2" id="progressText">Step 1 of 3</div>
                                            <div class="flex-grow-1">
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar bg-danger" role="progressbar" id="formProgress" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 step-section" id="step1">
                                        <h6 class="fw-bold mb-2 d-flex align-items-center fs-6">
                                            <span class="step-number">1</span>
                                            Vehicle Information
                                        </h6>

                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label for="emergencyVehicleType" class="form-label fw-semibold small">Vehicle Type</label>
                                                <select class="form-select" id="emergencyVehicleType" name="vehicle_type" required>
                                                    <option value="" selected disabled>Select vehicle type</option>
                                                    <option value="Car">Car</option>
                                                    <option value="Motorcycle">Motorcycle</option>
                                                    <option value="Truck">Truck</option>
                                                    <option value="SUV">SUV</option>
                                                    <option value="Van">Van</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                <div class="invalid-feedback">Please select your vehicle type</div>
                                            </div>
                                            <div class="col-12">
                                                <label for="emergencyVehicleModel" class="form-label fw-semibold small">Vehicle Model</label>
                                                <input type="text" class="form-control" id="emergencyVehicleModel" name="vehicle_model" required placeholder="e.g., Toyota Vios 2020">
                                                <div class="invalid-feedback">Please enter your vehicle model</div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-danger rounded-pill px-3 py-2 ms-md-2" id="nextToStep2" style="height: 40px; padding: 0 10px;" disabled>
                                                Next <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3 step-section" id="step2" style="display: none;">
                                        <h6 class="fw-bold mb-2 d-flex align-items-center fs-6">
                                            <span class="step-number">2</span>
                                            Problem Details
                                        </h6>

                                        <label for="emergencyIssue" class="form-label fw-semibold small">Describe the Issue <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="emergencyIssue" name="issue_description" rows="3" required placeholder="Please describe the problem you're experiencing..."></textarea>
                                        <div class="invalid-feedback">Please describe your issue</div>

                                        <div class="mt-2">
                                            <label class="form-label fw-semibold small">Upload Video (Optional)</label>
                                            <div class="alert alert-info bg-info-light border-info small mb-2 py-2">
                                                <i class="fas fa-info-circle me-1"></i> You can upload 1 video (MP4 format) to help us understand the problem better (max 100MB)
                                            </div>

                                            <div class="dropzone border rounded-2 p-2 text-center" id="videoUploadArea">
                                                <input type="file" id="emergencyVideo" name="emergency_video" accept="video/mp4" style="display: none;">
                                                <div class="dz-message">
                                                    <i class="fas fa-video fa-2x text-muted mb-2"></i>
                                                    <h6 class="fw-bold text-muted small mb-1">Drag & drop video here</h6>
                                                    <p class="text-muted small mb-1">or click to browse</p>
                                                    <p class="text-muted small">MP4 format only (max 100MB)</p>
                                                </div>
                                                <div class="d-flex flex-wrap gap-2 mt-2" id="videoPreviewContainer"></div>
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="emergencyUrgent" name="urgent" checked>
                                                <label class="form-check-label fw-semibold text-danger small" for="emergencyUrgent">
                                                    <i class="fas fa-bolt me-1"></i> This is an urgent emergency
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2" id="backToStep1">
                                                <i class="fas fa-arrow-left me-1"></i> Back
                                            </button>
                                            <button type="button" class="btn btn-danger rounded-pill px-3 py-2 ms-md-2" id="nextToStep3" style="height: 40px; padding: 0 10px;" disabled>
                                                Next <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3 step-section" id="step3" style="display: none;">
                                        <h6 class="fw-bold mb-2 d-flex align-items-center fs-6">
                                            <span class="step-number">3</span>
                                            Your Location
                                        </h6>

                                        <div class="mb-2">
                                            <label for="emergencyLocation" class="form-label fw-semibold small">Current Location <span class="text-danger">*</span></label>
                                            <div class="position-relative w-100">
                                                <input type="text" class="form-control" id="emergencyLocation" name="full_address" required placeholder="Detecting your location..." style="padding-right: 2.5rem;">
                                                <button type="button" id="emergencyGetLocationBtn" class="position-absolute top-50 translate-middle-y" style="right: 10px; padding: 0; border: none; background: transparent; z-index: 10;">
                                                    <i class="fas fa-location-arrow text-danger fs-5"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback">Please provide your location</div>
                                            <small class="form-text text-muted mt-1 d-block small">
                                                <i class="fas fa-info-circle me-1"></i> We'll use this to send help to your exact location
                                            </small>
                                        </div>

                                        <div class="mb-2">
                                            <label for="emergencyContact" class="form-label fw-semibold small">Contact Number <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" id="emergencyContact" name="contact_number" required pattern="^\d{11}$" minlength="11" maxlength="11" placeholder="e.g., 09171234567">
                                            <div class="invalid-feedback">Please enter a valid 11-digit number</div>
                                        </div>

                                        <div id="emergencyMapContainer" class="mb-2 rounded-2 overflow-hidden border" style="height: 180px; display: none;">
                                            <div class="map-placeholder bg-light d-flex align-items-center justify-content-center text-center h-100">
                                                <div class="p-3">
                                                    <i class="fas fa-map-marked-alt fa-2x text-muted mb-2"></i>
                                                    <h6 class="fw-bold text-muted small">Location Detection</h6>
                                                    <p class="text-muted mb-0 small">Click the "Detect" button to show your location</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="locationVerificationSection" class="mb-2" style="display: none;">
                                            <div class="alert alert-danger border-danger bg-danger-light py-2">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-map-marker-alt mt-1 me-2"></i>
                                                    <div>
                                                        <p class="fw-semibold mb-1 small">Verify Your Location</p>
                                                        <p id="locationInstructions" class="mb-1 small">Please confirm that the marker on the map shows your exact location. You can drag it to adjust if needed.</p>
                                                        <p id="locationConfirmation" class="mb-1 d-none text-success fw-semibold small">
                                                            <i class="fas fa-check-circle me-1"></i> Location confirmed!
                                                        </p>
                                                        <div class="d-flex gap-1 mt-1">
                                                            <button type="button" class="btn btn-sm btn-danger py-1 px-2" id="confirmLocationBtn" style="height: 40px; padding: 0 10px;">
                                                                Confirm
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="updateLocationBtn" style="height: 40px; padding: 0 10px;">
                                                                Update
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-3">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 me-2 me-md-0" id="backToStep2">
                                                <i class="fas fa-arrow-left me-1"></i> Back
                                            </button>
                                            <button type="submit" class="btn btn-danger rounded-pill px-3 py-2 ms-md-2" id="emergencySubmitBtn" style="height: 40px; padding: 0 10px;" disabled>
                                                <i class="fas fa-paper-plane me-1"></i> Send
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="success-container text-center" id="successContainer" style="display: none;">
                            <h4 class="fw-bold mb-3">Emergency Request Sent</h4>
                            <p class="mb-4">Your emergency assistance request has been successfully submitted. Our team will contact you shortly.</p>

                            <div class="alert alert-warning bg-light-warning border-warning text-dark rounded-3 mb-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-exclamation-circle fs-4 mb-2"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Important Note</h6>
                                        <p class="mb-0 small">
                                            If you don't receive a call within 3–5 minutes, please call us directly at <strong><?= htmlspecialchars($shop['phone']) ?></strong> or message us on this platform for better communication.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="unsavedEmergencyModal" tabindex="-1" aria-labelledby="unsavedEmergencyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unsavedEmergencyModalLabel"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Incomplete Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You have not finished your emergency request. If you leave now, your progress will be lost.</p>
                    <p>Are you sure you want to leave this page?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stay</button>
                    <button type="button" class="btn btn-danger" id="confirmEmergencyLeaveBtn">Leave Anyway</button>
                </div>
            </div>
        </div>
    </div>

  <div class="modal fade" id="processingModal" tabindex="-1" aria-labelledby="processingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="processingModalLabel">Sending Emergency Request</h5>
            </div>

            <div class="modal-body text-center">
                <p class="status-text mb-1" id="processingStatusText">
                    Please wait, we are dispatching your request...
                </p>

                <p class="text-muted mb-2" style="font-size: 14px;">
                    This may take 2–3 minutes.
                </p>

                <div class="progress mb-2" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                         id="processingProgressBar" role="progressbar"
                         style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="percentage-text" id="processingPercentage">0%</div>
            </div>

            <div class="modal-footer border-0" id="processingModalFooter" style="display: none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places" async defer></script>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/emergency-requests.js"></script>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>