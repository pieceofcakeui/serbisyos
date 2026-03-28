<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/become-a-partner.php';

$categories_result = $conn->query("SELECT * FROM service_categories ORDER BY display_order, name");
$subcategories_result = $conn->query("SELECT * FROM service_subcategories ORDER BY name");
$services_result = $conn->query("SELECT * FROM services ORDER BY name");

$services_data = [];
while ($cat = $categories_result->fetch_assoc()) {
    $services_data[$cat['id']] = $cat;
    $services_data[$cat['id']]['subcategories'] = [];
}
while ($sub = $subcategories_result->fetch_assoc()) {
    if (isset($services_data[$sub['category_id']])) {
        $services_data[$sub['category_id']]['subcategories'][$sub['id']] = $sub;
        $services_data[$sub['category_id']]['subcategories'][$sub['id']]['services'] = [];
    }
}
while ($ser = $services_result->fetch_assoc()) {
    foreach ($services_data as $cat_id => $category) {
        if (isset($category['subcategories'][$ser['subcategory_id']])) {
            $services_data[$cat_id]['subcategories'][$ser['subcategory_id']]['services'][] = $ser;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Partner</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/become-a-partner.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            overflow-y: scroll !important;
        }

        .become-a-partner {
            margin-top: 20px !important;
        }

        .benefits-list {
            list-style: none;
            padding-left: 0;
        }

        .intro-apply {
            margin-top: -40px;
        }

        .form-narrow-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .partner-intro .card {
            border-radius: 0;
        }

        .partner-intro .card {
            border: none;
            background: #fff;
        }

        .partner-intro h2 {
            font-weight: 700;
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .partner-intro p.lead {
            font-size: 1.05rem;
            color: #555;
            line-height: 1.6;
        }

        .partner-intro .benefits-list {
            list-style: none;
            padding-left: 0;
            margin: 1rem 0;
        }

        .partner-intro .benefits-list li {
            font-size: 1rem;
            color: #444;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }

        .partner-intro .benefits-list i {
            font-size: 1.2rem;
            margin-right: 8px;
            color: #28a745;
        }

        .partner-intro img {
            max-height: 320px;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .partner-intro .btn-warning {
            border-radius: 50px;
            font-size: 1.1rem;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
            background-color: #ffc107;
            border: none;
        }

        .partner-intro .btn-warning:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
        }

        .category-grid .category-card {
            border: 1px solid #dee2e6;
            border-radius: .375rem;
            padding: 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .category-grid .category-card:hover {
            transform: translateY(-5px);
            border-color: #ffc107;
        }

        .category-card i {
            font-size: 1.5rem;
            color: #ffc107;
            margin-bottom: 0.5rem;
        }

        .category-card-title {
            font-weight: 600;
            font-size: 0.85rem;
            color: #333;
        }

        #modal-summary-content .summary-category {
            font-size: 1rem;
            font-weight: bold;
        }

        #modal-summary-content .summary-subcategory {
            font-size: 0.9rem;
            font-weight: 500;
            padding-left: 1rem;
        }

        #modal-summary-content .summary-service-list {
            list-style: disc;
            padding-left: 2.5rem;
        }

        .subcategory-list .list-group-item-button {
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            padding: .75rem 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            cursor: pointer;
        }

        .subcategory-list .list-group-item-button:last-child {
            border-bottom: none;
        }

        .subcategory-list .list-group-item-button:hover {
            background-color: #f8f9fa;
        }

        .modal-backdrop:nth-of-type(n+2) {
            z-index: 1056;
        }

        .modal:nth-of-type(n+2) {
            z-index: 1057;
        }

        .summary-button-link {
            font-size: 0.95rem;
            padding: 0.25rem 0;
        }

        .summary-button-link:hover,
        .summary-button-link:focus {
            color: #0a58ca;
            box-shadow: none;
        }

        .summary-button-link .badge {
            font-size: 0.8rem;
        }

        .form-narrow-container {
            display: none;
        }

        .form-step {
            display: none;
            padding-top: 1.5rem;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        #summary-container dt {
            font-weight: 600;
        }

        #summary-container dd {
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .file-name-display {
            font-style: italic;
            color: #6c757d;
        }

        .main-progress-indicator {
            font-family: 'Montserrat', sans-serif;
        }

        .top-bar {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }

        .step-counter {
            font-size: 0.9rem;
            color: #555;
            white-space: nowrap;
        }

        .progress-line {
            width: 100%;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-line-fill {
            width: 0%;
            height: 100%;
            background-color: #e53935;
            border-radius: 4px;
            transition: width 0.4s ease-in-out;
        }

        .section-title-display {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title-circle {
            width: 40px;
            height: 40px;
            background-color: #e53935;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .section-title-heading {
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
            margin: 0;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: center;
            align-items: center;
        }

        .form-buttons button {
            flex: 1;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .prev-sub-btn {
            background-color: #6c757d;
            color: white;
        }

        .prev-btn:hover {
            background-color: #5a6268;
        }

        .submit-btn {
            background-color: #ffc107;
            color: black;
        }

        .submit-btn:hover {
            background-color: #e0a800;
        }

        .form-buttons #submitBtn {
            margin-top: 0;
        }

        #map {
            height: 400px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 15px;
        }

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

        @media (max-width: 768px) {
            .form-buttons {
                flex-direction: column;
            }

            .form-buttons button {
                width: 100%;
            }
        }
    </style>

</head>

<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1151;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="become-a-partner container mt-5">
            <?php if (empty($status)): ?>
                <div class="partner-intro mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="intro-apply text-center" style="margin-top: 10px;">Grow Your Business with Serbisyos</h2>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="lead">Are you a shop owner looking to reach more customers? Join our platform for free and get listed in our service directory!</p>
                                    <ul class="benefits-list list-unstyled">
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Increase your visibility to thousands of potential customers</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Free to apply and list your services</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Manage your profile and services easily</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Get customer reviews and build your reputation</li>
                                    </ul>
                                </div>
                                <div class="col-md-6 text-center"><img src="../assets/img/partner/become.webp" alt="Users Icon" class="img-fluid" style="max-height: 350px;"></div>
                            </div>
                            <div class="text-center mt-4"><a href="#partnerForm" class="btn btn-warning btn-lg px-4 text-white text-center fw-bold">Apply Now <i class="fas fa-arrow-right ms-2 text-white"></i></a></div>
                        </div>
                    </div>
                </div>

                <div class="form-narrow-container">
                    <form id="partnerForm" action="backend/submit_partner.php" method="POST" enctype="multipart/form-data" novalidate>
                        <div class="alert alert-info" id="application-requirements">
                            <h5><i class="bi bi-info-circle-fill me-2"></i>Application Requirements</h5>
                            <p class="mb-1">Ensure you meet the following requirements before applying:</p>
                            <ul class="mb-0">
                                <li>Valid business registration documents</li>
                                <li>Tax identification number (TIN)</li>
                                <li>Physical business location</li>
                                <li>At least 1 year of operation (based on the form)</li>
                            </ul>
                        </div>

                        <div class="main-progress-indicator mb-4 mt-4">
                            <div class="top-bar">
                                <span id="step-counter-text" class="step-counter"></span>
                                <div class="progress-line">
                                    <div id="progress-line-fill" class="progress-line-fill"></div>
                                </div>
                            </div>
                            <div class="section-title-display">
                                <div class="section-title-circle">
                                    <span id="section-title-number"></span>
                                </div>
                                <h3 id="section-title-text" class="section-title-heading"></h3>
                            </div>
                        </div>

                        <div class="form-step active" id="step-1">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Shop Name *</label><input type="text" name="shop_name" class="form-control" required></div>
                                <div class="col-md-6"><label class="form-label">Owner Name *</label><input type="text" name="owner_name" class="form-control" required></div>
                                <div class="col-md-6"><label class="form-label">Years Operation *</label><input type="number" name="years_operation" class="form-control" required min="1"></div>
                                <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
                                <div class="col-md-12"><label class="form-label">Contact Number *</label><input type="tel" name="phone" class="form-control" required pattern="\d{11}" maxlength="11" minlength="11" title="Please enter exactly 11 digits"></div>
                                <div class="col-12 text-end mt-4">
                                    <button type="button" class="btn btn-warning next-btn">Next <i class="fas fa-arrow-right ms-1"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="form-step" id="step-2">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">City *</label><input type="text" name="town_city" class="form-control" required></div>
                                <div class="col-md-6"><label class="form-label">Province *</label><input type="text" name="province" class="form-control" required></div>
                                <div class="col-md-6"><label class="form-label">Country *</label><input type="text" name="country" class="form-control" value="Philippines" required></div>
                                <div class="col-md-6"><label class="form-label">Postal Code *</label><input type="text" name="postal_code" class="form-control" required></div>
                                
                                <div class="col-12">
                                    <label class="form-label">Pin Your Exact Shop Location *</label>
                                    <input type="text" id="address-search" class="form-control" placeholder="Search for your shop address or a nearby landmark">
                                    <input type="hidden" name="shop_location" id="shop_location" required>
                                    <small class="text-muted">Search your address first, then drag the red pin to the exact rooftop of your shop.</small>
                                </div>
                                
                                <div class="col-12">
                                    <div id="map"></div>
                                </div>
                                
                                <input type="hidden" name="latitude" id="latitude" required>
                                <input type="hidden" name="longitude" id="longitude" required>

                                <div class="col-12 d-grid gap-1 d-md-flex gap-md-3 mt-4">
                                    <button type="button" class="btn btn-secondary prev-btn flex-md-fill">
                                        <i class="fas fa-arrow-left me-1"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-warning next-btn flex-md-fill">
                                        Next <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>

                            </div>
                        </div>

                        <div class="form-step" id="step-3">
                            <div class="row">
                                <div class="col-md-6 col-12 mb-3"><label class="form-label">Business Permit / Mayor's Permit Number  *</label><input type="text" name="business_permit" class="form-control" required><small class="text-muted">Example: <em>MP-2023-123456</em></small></div>
                                <div class="col-md-6 col-12 mb-3"><label class="form-label">Tax Identification Number *</label><input type="text" name="tax_id" class="form-control" required><small class="text-muted">Format: <em>123-456-789-000</em></small></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12 mb-3"><label class="form-label">Upload Business Permit *</label><input type="file" name="business_permit_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required><small class="text-muted">Accepted: <em>Mayor's Permit/Business License</em></small></div>
                                <div class="col-md-6 col-12 mb-3"><label class="form-label">Upload TIN Document *</label><input type="file" name="tax_id_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required><small class="text-muted">Accepted: <em>BIR Certificate of Registration</em></small></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12 mb-3"><label class="form-label">DTI/SEC Registration Number *</label><input type="text" name="dti_sec_number" class="form-control" required><small class="text-muted">Example: <em>DN-123456 (DTI)</em></small></div>
                                <div class="col-md-6 col-12 mb-3"><label class="form-label">Upload DTI/SEC Certificate *</label><input type="file" name="dti_sec_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required><small class="text-muted">Accepted: <em>DTI/SEC Certificate</em></small></div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-12 mb-3"><label class="form-label">Owner/Manager's Valid ID Type *</label><select name="valid_id_type" class="form-control" required>
                                        <option value="">Select ID Type</option>
                                        <option value="Philippine National ID">Philippine National ID</option>
                                        <option value="Driver's License">Driver's License</option>
                                    </select></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12 mb-3"><label class="form-label">Upload ID Front (Clear Photo) *</label><input type="file" name="valid_id_front" class="form-control" accept=".jpg,.jpeg,.png,.webp" required><small class="text-muted">Max 5MB. Show full ID.</small></div>
                                <div class="col-md-6 col-12 mb-3"><label class="form-label">Upload ID Back (if applicable)</label><input type="file" name="valid_id_back" class="form-control" accept=".jpg,.jpeg,.png,.webp"><small class="text-muted">Required for National ID, etc.</small></div>
                            </div>
                            <p class="text-danger small mt-3">⚠️ <strong>Falsified documents</strong> will result in <u>permanent ban</u> and legal action.</p>
                            <div class="col-12 d-grid gap-1 d-md-flex gap-md-3 mt-4">
                                <button type="button" class="btn btn-secondary prev-btn flex-md-fill">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-warning next-btn flex-md-fill">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-step" id="step-4">
                            <div class="row g-3">
                                <div class="col-12"><label class="form-label">Select the services your shop offers *</label>
                                    <p class="text-muted small">Click on a category to select your services. You must select at least one.</p>
                                    <div class="category-grid row g-3"><?php foreach ($services_data as $category): ?><div class="col-lg-4 col-md-4 col-6">
                                                <div class="category-card" data-bs-toggle="modal" data-bs-target="#subcategoriesModal" data-category-id="<?php echo $category['id']; ?>"><i class="fas <?php echo htmlspecialchars($category['icon']); ?>"></i><span class="category-card-title"><?php echo htmlspecialchars($category['name']); ?></span></div>
                                            </div><?php endforeach; ?></div>
                                </div>
                                <div class="col-12 mt-3 text-center"><button type="button" class="btn btn-link text-decoration-underline fw-bold summary-button-link" data-bs-toggle="modal" data-bs-target="#summaryModal">View Selected Services <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis ms-1" id="selected-count">0</span></button></div>
                                <div class="col-md-12"><label class="form-label">Brands Serviced *</label>
                                    <div id="brands_container"><input type="text" class="form-control mb-2 brand-input" placeholder="e.g., Toyota or All Brands" required></div><button type="button" id="add_brand" class="btn btn-sm btn-secondary mt-2"><i class="fas fa-plus"></i> Add More Brands</button>
                                </div>
                                <div class="col-12 d-grid gap-1 d-md-flex gap-md-3 mt-4">
                                    <button type="button" class="btn btn-secondary prev-btn flex-md-fill">
                                        <i class="fas fa-arrow-left me-1"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-warning next-btn flex-md-fill">
                                        Next <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-step" id="step-5">
                            <div class="row g-3">
                                <div class="col-12 mb-2">
                                    <h4>Your Business Hours</h4>
                                    <p class="form-text text-muted">Set your shop's schedule. You must select at least one day.</p>
                                </div>
                                <div class="col-md-6 mb-3"><label class="form-label"><b>Morning:</b> Opening Time *</label><input type="time" name="opening_time_am" class="form-control" required></div>
                                <div class="col-md-6 mb-3"><label class="form-label"><b>Morning:</b> Closing Time *</label><input type="time" name="closing_time_am" class="form-control" required></div>
                                <div class="col-12">
                                    <p class="form-text text-muted"><b>For businesses with a lunch break:</b> Leave the "Afternoon" fields blank if you operate continuously.</p>
                                </div>
                                <div class="col-md-6 mb-3"><label class="form-label"><b>Afternoon:</b> Opening Time</label><input type="time" name="opening_time_pm" class="form-control"></div>
                                <div class="col-md-6 mb-3"><label class="form-label"><b>Afternoon:</b> Closing Time</label><input type="time" name="closing_time_pm" class="form-control"></div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Days Open *</label>
                                    <div class="row"><?php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                                        foreach ($days as $day): ?><div class="col-md-3 col-6 mb-2">
                                                <div class="form-check"><input class="form-check-input" type="checkbox" name="days_open[]" value="<?= strtolower($day) ?>" id="day_<?= strtolower($day) ?>"><label class="form-check-label" for="day_<?= strtolower($day) ?>"><?= $day ?></label></div>
                                            </div><?php endforeach; ?></div>
                                </div>
                                <div class="col-12 d-grid gap-1 d-md-flex gap-md-3 mt-4">
                                    <button type="button" class="btn btn-secondary prev-btn flex-md-fill">
                                        <i class="fas fa-arrow-left me-1"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-warning next-btn flex-md-fill">
                                        Next <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-step" id="step-6">
                            <p class="mt-2">Please review all the information you have provided before submitting.</p>
                            <div id="summary-container" class="mt-4 p-3 border rounded">
                                <div class="text-center text-muted">Loading summary...</div>
                            </div>
                            <div id="hidden-checkboxes-container" style="display: none;"></div>
                            <div class="col-12 form-check mt-4"><input type="checkbox" class="form-check-input" name="terms" id="termsCheck" required><label class="form-check-label">I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a> and confirm all information is accurate.*</label></div>
                            <div class="form-buttons">
                                <button type="button" class="prev-sub-btn prev-btn">
                                    <i class="fas fa-arrow-left"></i> Previous
                                </button>
                                <button type="submit" id="submitBtn" class="submit-btn">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-center align-items-center vh-100" style="margin-top: -120px;">
                    <div class="card shadow-lg border-0 p-4 text-center" style="max-width: 500px; width: 100%;">
                        <div class="card-body"><?php if (strtolower($status) == "pending"): ?><i class="bi bi-hourglass-split text-warning mb-3" style="font-size: 3rem;"></i>
                                <h4 class="fw-bold text-primary mb-2">Application Received</h4>
                                <p class="text-muted mb-4">Thank you for submitting your application. Our team is currently reviewing your details.</p>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 75%"></div>
                                </div><small class="text-muted d-block"><i class="bi bi-clock-history me-1"></i>Status: <?php echo ucfirst($status); ?></small><?php elseif (strtolower($status) == "approved"): ?><i class="bi bi-check-circle-fill text-success mb-3" style="font-size: 3rem;"></i>
                                <h4 class="fw-bold text-success mb-2">Congratulations!</h4>
                                <p class="text-muted mb-4">Your application has been approved. Welcome ka Serbisyos!</p>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                </div><small class="text-muted d-block"><i class="bi bi-clock-history me-1"></i>Status: <?php echo ucfirst($status); ?></small><?php else: ?><i class="bi bi-x-circle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                                <h4 class="fw-bold text-danger mb-2">Application Rejected</h4>
                                <p class="text-muted mb-4">Unfortunately, your application was not approved. You may reapply after reviewing our requirements.</p>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"></div>
                                </div><small class="text-muted d-block mb-3"><i class="bi bi-clock-history me-1"></i>Status: <?php echo ucfirst($status); ?></small><a href="become-a-partner.php?reapply=true" class="btn btn-outline-danger rounded-pill px-4"><i class="bi bi-arrow-repeat me-1"></i> Reapply Now</a><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="subcategoriesModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subcategoriesModalTitle"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group subcategory-list" id="subcategory-list-container"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="servicesModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="servicesModalTitle"></h5><button type="button" class="btn-close" id="servicesModalCloseBtn"></button>
                </div>
                <div class="modal-body">
                    <div id="service-list-container"></div>
                </div>
                <div class="modal-footer justify-content-between"><button type="button" class="btn btn-outline-secondary" data-bs-target="#subcategoriesModal" data-bs-toggle="modal"><i class="fas fa-arrow-left"></i> Back</button><button type="button" class="btn btn-warning text-white" id="servicesModalDoneBtn">Done</button></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="summaryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Your Selected Services</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-summary-content">
                        <p class="text-muted">No services selected yet.</p>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
            <div class="modal-content border-0 rounded-3 shadow-sm">
                <div class="modal-body p-4 position-relative"><button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="mb-3">Partnership Agreement</h5>
                    <p>By submitting this application, you agree to the following terms:</p>
                    <ol class="mb-4 ps-3">
                        <li>You certify that all information provided is accurate and complete.</li>
                        <li>You agree to maintain proper business licenses and insurance.</li>
                        <li>You will provide quality service to customers referred by our platform.</li>
                        <li>You agree to our commission structure for services booked through our platform.</li>
                        <li>False information may result in immediate termination of partnership.</li>
                    </ol>
                    <h5 class="mb-3">Service Standards</h5>
                    <ul class="ps-3">
                        <li>Maintain a minimum 4-star average rating from customer reviews</li>
                        <li>Respond to service requests within 24 hours</li>
                        <li>Honor all quotes provided through the platform</li>
                        <li>Maintain a clean and professional work environment</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="processingModal" tabindex="-1" aria-labelledby="processingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="processingModalLabel">Submitting Your Application</h5>
                </div>
                <div class="modal-body text-center">
    <p class="status-text mb-1" id="processingStatusText">
        Please wait, we are processing your submission...
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</toastr-body-container>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/js/become-a-partner.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places&callback=initMap" async defer></script>

    <script>
        const servicesData = <?php echo json_encode($services_data); ?>;
        document.addEventListener('DOMContentLoaded', function() {
            const servicesModalEl = document.getElementById('servicesModal');
            const subcategoriesModalEl = document.getElementById('subcategoriesModal');
            const servicesModal = new bootstrap.Modal(servicesModalEl);
            document.getElementById('servicesModalCloseBtn').addEventListener('click', () => servicesModal.hide());
            document.getElementById('servicesModalDoneBtn').addEventListener('click', () => servicesModal.hide());

            const scrollToServicesSection = () => {
                const servicesSection = document.getElementById('services-section-title');
                if (servicesSection) {
                    servicesSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            };
            if (subcategoriesModalEl) {
                subcategoriesModalEl.addEventListener('hidden.bs.modal', scrollToServicesSection);
            }
            if (servicesModalEl) {
                servicesModalEl.addEventListener('hidden.bs.modal', scrollToServicesSection);
            }

            document.querySelector('.category-grid').addEventListener('click', function(e) {
                const card = e.target.closest('.category-card');
                if (!card) return;
                const categoryId = card.dataset.categoryId;
                const category = servicesData[categoryId];
                const subcatContainer = document.getElementById('subcategory-list-container');
                document.getElementById('subcategoriesModalTitle').textContent = `Category: ${category.name}`;
                subcatContainer.innerHTML = '';
                if (category.subcategories && Object.keys(category.subcategories).length > 0) {
                    for (const subId in category.subcategories) {
                        const sub = category.subcategories[subId];
                        const subItem = document.createElement('button');
                        subItem.type = 'button';
                        subItem.className = 'list-group-item-button';
                        subItem.textContent = sub.name;
                        subItem.dataset.subcategoryId = sub.id;
                        subItem.dataset.categoryId = categoryId;
                        subItem.dataset.bsToggle = 'modal';
                        subItem.dataset.bsTarget = '#servicesModal';
                        subcatContainer.appendChild(subItem);
                    }
                } else {
                    subcatContainer.innerHTML = '<p class="text-muted p-3">No subcategories available.</p>';
                }
            });
            document.getElementById('subcategory-list-container').addEventListener('click', function(e) {
                e.preventDefault();
                const subItem = e.target.closest('.list-group-item-button');
                if (!subItem) return;
                const subId = subItem.dataset.subcategoryId;
                const catId = subItem.dataset.categoryId;
                const subcategory = servicesData[catId].subcategories[subId];
                const mainServiceContainer = document.getElementById('service-list-container');
                document.getElementById('servicesModalTitle').textContent = `Subcategory: ${subcategory.name}`;
                mainServiceContainer.querySelectorAll('.service-group').forEach(group => group.style.display = 'none');
                const groupContainerId = `services-for-sub-${subId}`;
                let groupContainer = document.getElementById(groupContainerId);
                if (groupContainer) {
                    groupContainer.style.display = 'block';
                } else {
                    groupContainer = document.createElement('div');
                    groupContainer.id = groupContainerId;
                    groupContainer.className = 'service-group';

                    if (subcategory.services && subcategory.services.length > 0) {
                        const selectAllHtml = `
                            <div class="form-check form-check-inline mb-2 border-bottom pb-2">
                                <input class="form-check-input" type="checkbox" id="select-all-${subId}">
                                <label class="form-check-label fw-bold" for="select-all-${subId}">Select All Services</label>
                            </div>
                        `;
                        groupContainer.innerHTML = selectAllHtml;

                        const hiddenCheckboxesContainer = document.getElementById('hidden-checkboxes-container');
                        subcategory.services.forEach(service => {
                            let checkbox = hiddenCheckboxesContainer.querySelector(`#hidden-service-${service.id}`);
                            if (!checkbox) {
                                checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.name = 'services_offered[]';
                                checkbox.value = service.id;
                                checkbox.id = `hidden-service-${service.id}`;
                                hiddenCheckboxesContainer.appendChild(checkbox);
                            }
                            const div = document.createElement('div');
                            div.className = 'form-check';
                            div.innerHTML = `<input class="form-check-input" type="checkbox" value="${service.id}" id="service-${service.id}" onchange="syncCheckboxes(this, 'hidden-service-${service.id}')" ${checkbox.checked ? 'checked' : ''}><label class="form-check-label" for="service-${service.id}">${service.name}</label>`;
                            groupContainer.appendChild(div);
                        });

                        const allServiceCheckboxes = groupContainer.querySelectorAll('.form-check-input[id^="service-"]');
                        const selectAllCheckbox = groupContainer.querySelector(`#select-all-${subId}`);
                        if (selectAllCheckbox) {
                            const allChecked = allServiceCheckboxes.length > 0 && Array.from(allServiceCheckboxes).every(cb => cb.checked);
                            selectAllCheckbox.checked = allChecked;

                            selectAllCheckbox.addEventListener('change', function() {
                                const isChecked = this.checked;
                                const serviceCheckboxes = this.closest('.service-group').querySelectorAll('.form-check-input[id^="service-"]');
                                
                                serviceCheckboxes.forEach(cb => {
                                    cb.checked = isChecked;
                                    window.syncCheckboxes(cb, `hidden-service-${cb.value}`); 
                                });
                            });
                        }

                    } else {
                        groupContainer.innerHTML = '<p class="text-muted">No services available.</p>';
                    }
                    mainServiceContainer.appendChild(groupContainer);
                }
            });

            function updateServicesSummary() {
                const summaryModalContainer = document.getElementById('modal-summary-content');
                const summaryButtonCounter = document.getElementById('selected-count');
                const checkedInputs = document.querySelectorAll('#hidden-checkboxes-container input[name="services_offered[]"]:checked');
                summaryButtonCounter.textContent = checkedInputs.length;
                if (checkedInputs.length === 0) {
                    summaryModalContainer.innerHTML = '<p class="text-muted">No services selected yet.</p>';
                    return;
                }
                const summary = {};
                checkedInputs.forEach(input => {
                    const serviceId = input.value;
                    for (const catId in servicesData) {
                        for (const subId in servicesData[catId].subcategories) {
                            const service = servicesData[catId].subcategories[subId].services.find(s => s.id == serviceId);
                            if (service) {
                                const catName = servicesData[catId].name;
                                const subName = servicesData[catId].subcategories[subId].name;
                                if (!summary[catName]) summary[catName] = {};
                                if (!summary[catName][subName]) summary[catName][subName] = [];
                                summary[catName][subName].push(service.name);
                                return;
                            }
                        }
                    }
                });
                let html = '';
                for (const catName in summary) {
                    html += `<div class="summary-category mb-2">${catName}</div>`;
                    for (const subName in summary[catName]) {
                        html += `<div class="summary-subcategory">${subName}</div>`;
                        html += `<ul class="summary-service-list">`;
                        summary[catName][subName].forEach(serviceName => {
                            html += `<li>${serviceName}</li>`;
                        });
                        html += `</ul>`;
                    }
                }
                summaryModalContainer.innerHTML = html;
            }

            window.syncCheckboxes = function(visibleCheckbox, hiddenCheckboxId) {
                const hiddenCheckbox = document.querySelector(`#hidden-checkboxes-container #${hiddenCheckboxId}`);
                if (hiddenCheckbox) {
                    hiddenCheckbox.checked = visibleCheckbox.checked;
                    updateServicesSummary();
                }
                const groupContainer = visibleCheckbox.closest('.service-group');
                if (groupContainer) {
                    const selectAllCheckbox = groupContainer.querySelector('[id^="select-all-"]');
                    const allServiceCheckboxes = groupContainer.querySelectorAll('.form-check-input[id^="service-"]');
                    
                    if (selectAllCheckbox && allServiceCheckboxes.length > 0) {
                        const allChecked = Array.from(allServiceCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                }
            }
        });
    </script>
</body>

</html>