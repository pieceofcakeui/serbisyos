<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/auth_check.php';

$shop_id = null;
$shop_slug = '';
$has_active_booking = false;

if (!isset($_GET['shop']) || empty($_GET['shop'])) {
    header("Location: booking-provider.php?error=no_shop_specified");
    exit();
}

$shop_slug = $_GET['shop'];

try {
    $stmt = $conn->prepare("SELECT id FROM shop_applications WHERE shop_slug = ? AND status = 'Approved'");
    $stmt->bind_param("s", $shop_slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: booking-provider.php?error=shop_not_found");
        exit();
    } else {
        $shop = $result->fetch_assoc();
        $shop_id = $shop['id'];
    }
    $stmt->close();
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: booking-provider.php?error=database_error");
    exit();
}

if (isset($_SESSION['user_id']) && $shop_id) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id FROM services_booking WHERE user_id = ? AND shop_id = ? AND booking_status IN ('Pending', 'Accepted')");
    $stmt->bind_param("ii", $user_id, $shop_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $has_active_booking = true;
    }
    $stmt->close();
}

$transmission_types = [];
$fuel_types = [];
$service_types_ids = [];
$vehicle_types = [];
$business_hours = [];
$organized_services = [];

if ($shop_id) {
    try {
        $settings_stmt = $conn->prepare("SELECT transmission_types, fuel_types, vehicle_types, service_types, business_hours FROM shop_booking_form WHERE shop_id = ?");
        $settings_stmt->bind_param("i", $shop_id);
        $settings_stmt->execute();
        $settings_result = $settings_stmt->get_result();
        if ($settings_result->num_rows > 0) {
            $settings = $settings_result->fetch_assoc();
            $transmission_types = json_decode($settings['transmission_types'] ?? '[]', true) ?: [];
            $fuel_types = json_decode($settings['fuel_types'] ?? '[]', true) ?: [];
            $service_types_ids = json_decode($settings['service_types'] ?? '[]', true) ?: [];
            $business_hours = json_decode($settings['business_hours'] ?? '[]', true) ?: [];
            $vehicle_types = json_decode($settings['vehicle_types'] ?? '[]', true) ?: [];
            $parsed_business_hours = [];
            foreach ($business_hours as $day_slot) {
                if (preg_match('/([^,]+),\s*([\d:]+)\s*-\s*([\d:]+)\s*\((\d+)\s*slots\)/', $day_slot, $matches)) {
                    $day = trim($matches[1]);
                    $start_time = $matches[2];
                    $end_time = $matches[3];
                    $slots = (int)$matches[4];
                    if ($slots > 0) {
                        $parsed_business_hours[$day] = ['start' => $start_time, 'end' => $end_time, 'slots' => $slots];
                    }
                }
            }
            $business_hours = $parsed_business_hours;
        }
        $settings_stmt->close();

        if (!empty($service_types_ids)) {
            $placeholders = implode(',', array_fill(0, count($service_types_ids), '?'));
            $types = str_repeat('i', count($service_types_ids));
            
            $sql_services = "
                SELECT 
                    s.id, 
                    s.name AS service_name,
                    ssc.name AS subcategory_name,
                    sc.name AS category_name,
                    sc.icon AS category_icon
                FROM services s
                JOIN service_subcategories ssc ON s.subcategory_id = ssc.id
                JOIN service_categories sc ON ssc.category_id = sc.id
                WHERE s.id IN ($placeholders)
                ORDER BY sc.display_order, sc.name, ssc.name, s.name
            ";
            
            $stmt_services = $conn->prepare($sql_services);
            $stmt_services->bind_param($types, ...$service_types_ids);
            $stmt_services->execute();
            $result_services = $stmt_services->get_result();

            while ($service_row = $result_services->fetch_assoc()) {
                $category_name = $service_row['category_name'];
                $subcategory_name = $service_row['subcategory_name'];
                
                if (!isset($organized_services[$category_name])) {
                    $organized_services[$category_name] = [
                        'icon' => $service_row['category_icon'],
                        'subcategories' => []
                    ];
                }
                if (!isset($organized_services[$category_name]['subcategories'][$subcategory_name])) {
                    $organized_services[$category_name]['subcategories'][$subcategory_name] = [];
                }
                
                $organized_services[$category_name]['subcategories'][$subcategory_name][] = [
                    'id' => $service_row['id'],
                    'service_name' => $service_row['service_name']
                ];
            }
            $stmt_services->close();
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/book-now.css">
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

        .service-accordion-container .accordion-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
        }
        .service-accordion-container .accordion-header {
            border-bottom: 0;
        }
        .service-accordion-container .accordion-button {
            font-weight: 600;
            color: #212529;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
        }
        .service-accordion-container .accordion-button:not(.collapsed) {
            background-color: #e9ecef;
            box-shadow: none;
        }
        .service-accordion-container .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .service-accordion-container .accordion-body {
            padding: 1rem 1.25rem;
        }
        .service-subcategory-list {
            list-style: none;
            padding-left: 0;
        }
        .service-subcategory-list li {
            margin-bottom: 10px;
        }
        .service-subcategory-list h6 {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .service-accordion-container .form-check {
            margin-bottom: 0.5rem;
        }
        .service-accordion-container .form-check-label {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-start mb-3"><a href="javascript:history.back()" class="px-4 py-2 text-black text-decoration-none"><i class="fas fa-arrow-left me-2"></i> Back</a></div>

            <?php if (!isset($has_active_booking) || !$has_active_booking): ?>
                 <div id="form-intro" class="row justify-content-center mb-4">
                    <div class="col-md-8 col-lg-6 text-center">
                        <h2 class="mb-3">Schedule Your Auto Service</h2>
                        <p class="lead">Complete this simple 4-step form to book your appointment. And We'll confirm your booking details via email or notification of your serbisyos account.</p>
                        <div class="alert alert-info mt-3" role="alert"><strong>Reminder:</strong> Prices may vary depending on your vehicle’s condition. Message the shop first to confirm availability and get an estimated cost before booking.</div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="booking-container">
                        <div class="form-header">
                            <h1 class="fs-4 mb-3"><i class="fas fa-calendar-alt me-2"></i>Schedule Auto Service</h1>

                            <?php if (isset($has_active_booking) && $has_active_booking): ?>
                                <div class="d-flex justify-content-center mt-3">
                                    <div class="alert alert-warning bg-white text-dark border border-warning text-center w-100 w-md-75 w-lg-50">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        You can't book another service at <strong>this shop</strong> because you already have a pending or accepted booking here. You can still book at other shops.
                                        <hr>
                                        <p class="mb-2">Check the status of your current booking:</p>
                                        <a href="my-booking.php" class="btn btn-dark btn-sm">Go to My Bookings</a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-center">
                                <div class="alert bg-white w-100 w-md-75 w-lg-50 text-center" id="successAlert" style="display: none;">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div>
                                            <h4 class="alert-heading">Appointment Confirmed!</h4>
                                            <p>We've sent details to your email. Our team will contact you if we need additional information.</p>
                                            <div class="confirmation-details mt-2 p-3 bg-light rounded">
                                                <p class="mb-1"><strong>Date:</strong> <span id="confirmationDate"></span></p>
                                                <p class="mb-0"><strong>Vehicle:</strong> <span id="confirmationVehicle"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <div class="alert alert-danger w-100 w-md-75 w-lg-50 text-center" id="errorAlert" style="display: none;">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div>
                                            <h4 class="alert-heading">Booking Failed</h4>
                                            <p id="errorMessage">There was an error processing your booking. Please try again.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!isset($has_active_booking) || !$has_active_booking): ?>
                                <div id="modern-progress-bar-container" class="my-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span id="step-counter" class="step-counter-text">Step 1 of 4</span>
                                    </div>
                                    <div class="progress">
                                        <div id="progress-bar-inner" class="progress-bar" role="progressbar" style="width: 25%;"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!isset($has_active_booking) || !$has_active_booking): ?>
                            <form id="bookingForm" method="POST" action="bookings.php">
                                <input type="hidden" id="shopId" name="shop_id" value="<?php echo $shop_id; ?>">
                                <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

                                <div class="booking-step" id="step1">
                                    <h5 data-step-number="1">Your Contact Information</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6"><label for="customerName" class="form-label">Full Name</label><input type="text" class="form-control" id="customerName" name="customer_name" 
value="<?php echo htmlspecialchars($user['fullname']); ?>" 
placeholder="John Doe" required>
</div>
                                        <div class="col-md-6"><label for="customerPhone" class="form-label">Phone</label><input type="tel" class="form-control" id="customerPhone" name="customer_phone" value="<?php echo htmlspecialchars($user['contact_number']); ?>" placeholder="09XXXXXXXXX" pattern="\d{11}" minlength="11" maxlength="11" required title="Phone number must be exactly 11 digits (e.g. 09123456789)"></div>
                                        <div class="col-12"><label for="customerEmail" class="form-label">Email</label><input type="email" class="form-control" id="customerEmail" name="customer_email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="your@email.com" required></div>
                                    </div>
                                </div>

                                <div class="booking-step d-none" id="step2">
                                    <h5 data-step-number="2">Vehicle & Service Details</h5>
                                    <div class="row g-4">
                                        <div class="col-md-6"><label for="vehicleMake" class="form-label">Vehicle Make</label><input type="text" class="form-control" id="vehicleMake" name="vehicle_make" placeholder="Brand/Manufacturer" required></div>
                                        <div class="col-md-6"><label for="vehicleModel" class="form-label">Vehicle Model</label><input type="text" class="form-control" id="vehicleModel" name="vehicle_model" placeholder="Specific model" required></div>
                                        <div class="col-md-6"><label for="vehicleYear" class="form-label">Year</label><select class="form-select" id="vehicleYear" name="vehicle_year" required>
                                                <option value="" disabled selected>Select year</option>
                                            </select></div>
                                            <div class="col-md-6"><label for="plateNumber" class="form-label">Plate Number</label><input type="text" class="form-control" id="plateNumber" name="plate_number" placeholder="e.g., ABC 1234" required></div>
                                        <div class="col-md-12"><label for="transmissionType" class="form-label">Transmission</label><select class="form-select" id="transmissionType" name="transmission_type" required>
                                                <option value="" selected disabled>Select transmission</option><?php foreach ($transmission_types as $type): ?><option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option><?php endforeach; ?>
                                            </select></div>
                                        <div class="col-md-12"><label for="vehicleType" class="form-label">Vehicle Type</label><select class="form-select" id="vehicleType" name="vehicle_type" required>
                                                <option value="" selected disabled>Select vehicle type</option><?php foreach ($vehicle_types as $type): ?><option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option><?php endforeach; ?>
                                            </select></div>
                                        <div class="col-md-12"><label for="fuelType" class="form-label">Fuel Type</label><select class="form-select" id="fuelType" name="fuel_type" required>
                                                <option value="" selected disabled>Select fuel type</option><?php foreach ($fuel_types as $fuel): ?><option value="<?= htmlspecialchars($fuel) ?>"><?= htmlspecialchars($fuel) ?></option><?php endforeach; ?>
                                            </select></div>
                                        
                                        <div class="col-12 mt-3">
                                            <label class="form-label d-block text-center mb-2">Select Services Needed</label>
                                            <div class="service-accordion-container accordion" id="servicesAccordion">
                                                <?php if (empty($organized_services)): ?>
                                                    <div class="alert alert-light text-center">No bookable services found for this shop.</div>
                                                <?php else: ?>
                                                    <?php $cat_index = 0; ?>
                                                    <?php foreach ($organized_services as $category_name => $category_data): ?>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="heading-cat-<?php echo $cat_index; ?>">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-cat-<?php echo $cat_index; ?>" aria-expanded="false" aria-controls="collapse-cat-<?php echo $cat_index; ?>">
                                                                    <i class="fas <?php echo htmlspecialchars($category_data['icon'] ?? 'fa-cog'); ?> me-2"></i>
                                                                    <?php echo htmlspecialchars($category_name); ?>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse-cat-<?php echo $cat_index; ?>" class="accordion-collapse collapse" aria-labelledby="heading-cat-<?php echo $cat_index; ?>" data-bs-parent="#servicesAccordion">
                                                                <div class="accordion-body">
                                                                    <ul class="service-subcategory-list">
                                                                        <?php foreach ($category_data['subcategories'] as $subcategory_name => $services): ?>
                                                                            <li>
                                                                                <h6><?php echo htmlspecialchars($subcategory_name); ?></h6>
                                                                                <?php foreach ($services as $service): ?>
                                                                                    <div class="form-check">
                                                                                        <input class="form-check-input" type="checkbox" name="services[]" value="<?php echo htmlspecialchars($service['service_name']); ?>" id="service-<?php echo $service['id']; ?>">
                                                                                        <label class="form-check-label" for="service-<?php echo $service['id']; ?>">
                                                                                            <?php echo htmlspecialchars($service['service_name']); ?>
                                                                                        </label>
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                            </li>
                                                                        <?php endforeach; ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php $cat_index++; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12"><label for="additionalNotes" class="form-label">Additional Notes</label><textarea class="form-control" id="additionalNotes" name="additional_notes" rows="2" placeholder="Any special requests or additional services needed?"></textarea></div>
                                    </div>
                                </div>

                                <div class="booking-step d-none" id="step3">
                                    <h5 data-step-number="3">Appointment Details</h5>
                                    <div class="row g-3">
                                        <div class="col-12"><label for="preferredDateTime" class="form-label">Preferred Date & Time</label><select class="form-select" id="preferredDateTime" name="preferred_datetime" required>
                                                <option value="" selected disabled><?php if (empty($business_hours)) {
                                                                                        echo "No available time slots - please contact the shop";
                                                                                    } else {
                                                                                        echo "Select Date and Time";
                                                                                    } ?></option><?php if (!empty($business_hours)) {
                                                                                                        foreach ($business_hours as $day => $time_range) {
                                                                                                            if (isset($time_range['start']) && isset($time_range['end']) && isset($time_range['slots'])) {
                                                                                                                $start_time = date("g:i A", strtotime($time_range['start']));
                                                                                                                $end_time = date("g:i A", strtotime($time_range['end']));
                                                                                                                $slots = $time_range['slots'];
                                                                                                                $time_slot = "$start_time - $end_time";
                                                                                                                $value = "$day, $time_slot";
                                                                                                                $display_text = "$day, $time_slot ($slots slots)";
                                                                                                                if ($slots > 0) {
                                                                                                                    echo "<option value='$value' data-day='$day' data-start='{$time_range['start']}' data-end='{$time_range['end']}' data-slots='$slots'>$display_text</option>";
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    } ?>
                                            </select></div>
                                        <input type="hidden" id="selectedDay" name="selected_day"><input type="hidden" id="selectedStartTime" name="selected_start_time"><input type="hidden" id="selectedEndTime" name="selected_end_time">
                                        <?php if (empty($business_hours)): ?><div class="alert alert-warning mt-2"><i class="fas fa-exclamation-circle me-2"></i>This shop hasn't set their available hours yet. Please contact them directly to schedule an appointment.</div><?php endif; ?>
                                        <div class="col-12"><label for="vehicleIssues" class="form-label">Describe Issues</label><textarea class="form-control" id="vehicleIssues" name="vehicle_issues" rows="2" placeholder="Any symptoms or specific problems?"></textarea></div>
                                    </div>
                                </div>

                                <div class="booking-step d-none" id="step4">
                                    <h5 data-step-number="4">Confirm Your Booking</h5>
                                    <div class="confirmation-details p-4 bg-light rounded">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h6 class="fw-bold">Personal Information</h6>
                                                <p id="confirm-name"></p>
                                                <p id="confirm-phone"></p>
                                                <p id="confirm-email"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="fw-bold">Vehicle Details</h6>
                                                <p id="confirm-vehicle"></p>
                                                <p id="confirm-year"></p>
                                                <p id="confirm-plate-number"></p>
                                                <p id="confirm-transmission"></p>
                                                <p id="confirm-fuel"></p>
                                                <p id="confirm-vehicle-type"></p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <h6 class="fw-bold">Selected Services</h6>
                                                <ul id="confirm-services" class="list-unstyled"></ul>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="fw-bold">Appointment Details</h6>
                                                <p id="confirm-date-time"></p>
                                                <p id="confirm-issues"></p>
                                                <p id="confirm-notes"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="finalConfirm" name="final_confirm" required><label class="form-check-label" for="finalConfirm">I confirm all the information is correct and I want to proceed with the booking</label></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center gap-2 mt-4">
                                    <button type="button" class="btn btn-back" id="prevStepBtn" disabled><i class="fas fa-arrow-left me-2"></i>Back</button>
                                    <button type="button" class="btn btn-next px-4" id="nextStepBtn">Next<i class="fas fa-arrow-right ms-2"></i></button>
                                    <button type="submit" class="btn btn-confirm px-4 d-none" id="submitBooking">Confirm</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100"></div>

    <div class="modal fade" id="unsavedBookingModal" tabindex="-1" aria-labelledby="unsavedBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unsavedBookingModalLabel"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Incomplete Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You have not finished your booking. If you leave now, your progress will be lost.</p>
                    <p>Are you sure you want to leave this page?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stay</button>
                    <button type="button" class="btn btn-danger" id="confirmBookingLeaveBtn">Leave Anyway</button>
                </div>
            </div>
        </div>
    </div>

   <div class="modal fade" id="processingModal" tabindex="-1" aria-labelledby="processingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="processingModalLabel">Submitting Your Booking</h5>
            </div>

            <div class="modal-body text-center">
                <p class="status-text mb-1" id="processingStatusText">
                    Please wait, confirming your slot...
                </p>

                <p class="text-muted mb-2" style="font-size: 14px;">
                    This may take 2–3 minutes.
                </p>

                <div class="progress mb-2" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
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
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/book-now.js"></script>
    <script src="../assetsjs/navbar.js"></script>

    <script>
        document.getElementById('preferredDateTime').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const day = selectedOption.getAttribute('data-day');
            const startTime = selectedOption.getAttribute('data-start');
            const endTime = selectedOption.getAttribute('data-end');
            const slots = selectedOption.getAttribute('data-slots');

            document.getElementById('selectedDay').value = day || '';
            document.getElementById('selectedStartTime').value = startTime || '';
            document.getElementById('selectedEndTime').value = endTime || '';

            this.classList.remove('is-invalid');
            const errorMsg = this.parentElement.querySelector('.invalid-feedback');
            if (errorMsg) errorMsg.remove();

            if (slots && parseInt(slots) <= 0) {
                showFieldError('preferredDateTime', 'This time slot is no longer available. Please select another slot.');
                this.value = '';
                document.getElementById('selectedDay').value = '';
                document.getElementById('selectedStartTime').value = '';
                document.getElementById('selectedEndTime').value = '';
            }
        });

        function refreshSlotAvailability() {
            const shopId = document.getElementById('shopId').value;
            if (!shopId) return;

            const dateTimeSelect = document.getElementById('preferredDateTime');
            if (dateTimeSelect) {
                const lastRefresh = dateTimeSelect.getAttribute('data-last-refresh');
                const now = Date.now();

                if (lastRefresh && (now - parseInt(lastRefresh)) > 300000) {
                    const warningDiv = document.createElement('div');
                    warningDiv.className = 'alert alert-info mt-2';
                    warningDiv.innerHTML = '<i class="fas fa-info-circle me-2"></i>Slot availability may have changed. <a href="javascript:location.reload()" class="alert-link">Refresh page</a> for latest availability.';

                    if (!dateTimeSelect.parentElement.querySelector('.alert')) {
                        dateTimeSelect.parentElement.appendChild(warningDiv);
                    }
                }
            }
        }

        setInterval(refreshSlotAvailability, 120000);

        const dateTimeSelect = document.getElementById('preferredDateTime');
        if (dateTimeSelect) {
            dateTimeSelect.setAttribute('data-last-refresh', Date.now().toString());
        }
    </script>
</body>

</html>