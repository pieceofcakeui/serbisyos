<?php
require_once '../functions/auth.php';
include 'backend/edit-shop-profile.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - <?php echo htmlspecialchars($shop_name); ?></title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/edit-shop-profile.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">

    <style>
        .edit-shop-profile-add-datetime-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
        }

        .edit-shop-profile-add-datetime-form > div,
        .edit-shop-profile-add-datetime-form .datetime-buttons {
            flex: 1 1 150px;
            min-width: 120px;
        }

        .edit-shop-profile-add-datetime-form .datetime-buttons {
            display: flex;
            gap: 10px;
            flex-basis: 100%;
        }
        
        .edit-shop-profile-add-datetime-form .datetime-buttons button {
            flex: 1;
        }
        
        .edit-shop-profile-add-item-form-multi {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .edit-shop-profile-add-item-form-multi select,
        .edit-shop-profile-add-item-form-multi button {
            flex: 1 1 150px;
            min-width: 150px;
        }
        
        .edit-shop-profile-add-item-form {
            display: flex;
            gap: 10px;
        }
        
        .edit-shop-profile-add-item-form input[type="text"] {
            flex: 1;
            min-width: 150px;
        }

        .modal-body .edit-shop-profile-dynamic-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }

        #predefinedTransmissionsList,
        #predefinedFuelTypesList,
        #predefinedVehicleTypesList {
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 10px;
        }
        #emergencyConfigTabs .nav-link{
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1200;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <h1 class="edit-shop-profile-header-title">Shop Management</h1>
        <p class="edit-shop-profile-header-subtitle">Manage your shop information and settings</p>
        <div class="edit-shop-profile-main-container">
            <div class="edit-shop-profile-card-header">


                <div class="edit-shop-profile-tab-nav">
                    <div class="edit-shop-profile-tab-indicator" id="tabIndicator"></div>
<button class="edit-shop-profile-tab-btn edit-shop-profile-active" onclick="switchTab(event, 'information')">Shop Information</button>
<button class="edit-shop-profile-tab-btn" onclick="switchTab(event, 'settings')">Shop Settings</button>
                </div>
            </div>

            <div class="edit-shop-profile-content-area">
                <div class="edit-shop-profile-tab-content edit-shop-profile-active" id="informationTab">
                    <form id="shopInfoForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
                        <input type="file" name="shop_logo" id="shopLogoInput" style="display: none;" accept="image/*">

                        <div class="edit-shop-profile-shop-avatar-section">
                            <div class="edit-shop-profile-shop-avatar">
                                <img src="<?php echo htmlspecialchars($shop_logo); ?>" alt="Shop Logo" class="edit-shop-profile-avatar-img" id="shopImgPreview">
                                <button type="button" class="edit-shop-profile-avatar-upload" id="uploadLogoBtn" style="background-color: #f0f0f0; border: none; border-radius: 50%; padding: 10px; cursor: pointer;">
                                    <i class="fas fa-camera" style="font-size: 16px; color: #333;"></i>
                                </button>
                            </div>
                        </div>

                        <div class="edit-shop-profile-form-grid">
                            <div class="edit-shop-profile-form-group">
                                <label class="edit-shop-profile-form-label" for="shopName">Shop Name</label>
                                <input type="text" id="shopName" name="shop_name" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($shop_name); ?>">
                            </div>
                            <div class="edit-shop-profile-form-group">
                                <label class="edit-shop-profile-form-label" for="shopPhone">Phone Number</label>
                                <input type="tel" id="shopPhone" name="phone" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($shop_phone); ?>">
                            </div>
                        </div>

                            <div class="edit-shop-profile-form-group edit-shop-profile-full-width">
                                <label class="edit-shop-profile-form-label" for="yearsOperation">Years in Operation</label>
                                <input type="number" id="yearsOperation" name="years_operation" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($years_operation); ?>" min="0" step="1" placeholder="e.g., 5">
                            </div>

                        <div class="edit-shop-profile-form-group edit-shop-profile-full-width">
                            <label class="edit-shop-profile-form-label" for="shopEmail">Email Address</label>
                            <input type="email" id="shopEmail" name="email" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($shop_email); ?>">
                        </div>

                        <div class="edit-shop-profile-form-group edit-shop-profile-full-width">
                            <label class="edit-shop-profile-form-label">Address</label>
                            <div class="edit-shop-profile-form-grid">
                                <div class="edit-shop-profile-form-group">
                                    <label class="edit-shop-profile-form-label" for="postal_code">Postal Code</label>
                                    <input type="text" id="postal_code" name="postal_code" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($shop_postal_code); ?>">
                                </div>
                                <div class="edit-shop-profile-form-group">
                                    <label class="edit-shop-profile-form-label" for="barangay">Barangay</label>
                                    <input type="text" id="barangay" name="barangay" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($barangay); ?>">
                                </div>
                            </div>
                            <div class="edit-shop-profile-form-grid">
                                <div class="edit-shop-profile-form-group">
                                    <label class="edit-shop-profile-form-label" for="town_city">Town/City</label>
                                    <input type="text" id="town_city" name="town_city" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($town_city); ?>">
                                </div>
                                <div class="edit-shop-profile-form-group">
                                    <label class="edit-shop-profile-form-label" for="province">Province</label>
                                    <input type="text" id="province" name="province" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($province); ?>">
                                </div>
                            </div>

                        </div>

                        <div class="edit-shop-profile-form-group edit-shop-profile-full-width">
                            <label class="edit-shop-profile-form-label" for="shop_location">Shop Location</label>
                            <input type="text" id="shop_location" name="shop_location" class="edit-shop-profile-form-input" readonly value="<?php echo htmlspecialchars($shop_location); ?>">
                            <small class="form-text text-muted" style="font-size: 0.875rem; margin-top: 5px;">
                                To update your shop location, please email <a href="mailto:support@serbisyos.com">support@serbisyos.com</a>.
                            </small>
                        </div>

                        <div class="edit-shop-profile-form-group edit-shop-profile-full-width">
                            <label class="edit-shop-profile-form-label">Social Media & Website</label>
                            <div class="edit-shop-profile-social-inputs">
                                <div class="edit-shop-profile-social-input-group">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.32 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96C18.34 21.21 22 17.06 22 12.06C22 6.53 17.5 2.04 12 2.04Z" />
                                    </svg>
                                    <input type="url" name="facebook" class="edit-shop-profile-form-input" placeholder="Facebook URL" value="<?php echo htmlspecialchars($facebook); ?>">
                                </div>
                                <div class="edit-shop-profile-social-input-group">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" />
                                    </svg>
                                    <input type="url" name="instagram" class="edit-shop-profile-form-input" placeholder="Instagram URL" value="<?php echo htmlspecialchars($instagram); ?>">
                                </div>
                                <div class="edit-shop-profile-social-input-group">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L14.4,17.6C12.9,16.7 11.3,15.1 10.4,13.6L11.8,11.8C12,11.6 12.1,11.3 12,11.1L10.2,6.1C10.1,5.8 9.8,5.5 9.5,5.5H8.5C8.2,5.5 8,5.7 8,6C8.2,10.2 9.8,14.2 12,16C12.2,16.2 12.5,16.2 12.8,16L17,14.2C17.3,14.1 17.5,13.8 17.5,13.5V12.5C17.5,12.2 17.3,12 17,12L16.2,11.8C16,11.7 15.7,11.8 15.5,12L14.5,13C13.5,11.5 12.5,10.5 11,9.5L12,8.5C12.2,8.3 12.3,8 12.2,7.8L11.8,7C11.6,6.8 11.3,6.7 11.1,6.8L10.4,7.1C9.3,7.6 8.4,8.5 7.9,9.6L7.1,10.4C6.8,10.7 6.7,11 6.8,11.3L7,11.8C7.2,12 7.5,12.2 7.8,12.2L8.5,12C8.8,12 9,11.8 9.2,11.5L9.6,10.9C10.1,10.1 10.9,9.5 11.8,9.2L12.9,8.8C13.1,8.7 13.4,8.8 13.5,9L13.9,9.6C14.1,9.8 14,10.1 13.8,10.3L13,11.1C12.8,11.3 12.8,11.6 13,11.8L14.2,13C15.4,14.2 16.7,15.1 18,15.4L18.5,15.5C18.8,15.5 19,15.3 19,15V14C19,13.7 18.8,13.5 18.5,13.5H17.5C17.2,13.5 16.9,13.6 16.7,13.8L16.2,14.5C15.1,13.8 14.2,12.9 13.5,11.8L14.2,11.1C14.4,10.9 14.7,10.8 14.9,10.9L15.9,11.3C16.1,11.4 16.4,11.3 16.5,11.1L16.9,10.5C17.1,10.3 17,10 16.8,9.8L16,9C15.8,8.8 15.8,8.5 16,8.3L17.2,7.1C17.4,6.9 17.7,6.8 17.9,6.9L18.6,7.2C19.7,7.7 20.6,8.6 21.1,9.7L21.2,10C21.3,10.2 21.2,10.5 21,10.7L20.2,11.5C20,11.7 20,12 20.2,12.2L21.5,13.5C21.7,13.7 21.8,14 21.7,14.2L21.3,15.2C21.2,15.5 20.9,15.7 20.6,15.6L19.6,15.2C19.4,15.1 19.1,15.2 18.9,15.4L18.2,16.1C18,16.3 17.7,16.4 17.5,16.3L16.2,16.2Z" />
                                    </svg>
                                    <input type="url" name="website" class="edit-shop-profile-form-input" placeholder="Website URL" value="<?php echo htmlspecialchars($website); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="edit-shop-profile-form-group edit-shop-profile-full-width">
                            <label class="edit-shop-profile-form-label" for="shopDescription">Shop Description</label>
                            <textarea id="shopDescription" name="description" class="edit-shop-profile-form-input edit-shop-profile-form-textarea" placeholder="Describe your shop..."><?php echo htmlspecialchars($shop_description); ?></textarea>
                        </div>

                        <div class="edit-shop-profile-form-group edit-shop-profile-full-width" id="businessHoursContainer">
                            <label class="edit-shop-profile-form-label">Business Hours</label>
                            <p class="form-text text-muted" style="font-size: 0.875rem; margin-top: -5px; margin-bottom: 15px;">Set your shop's schedule. Morning hours are required.</p>

                            <div class="edit-shop-profile-form-grid">
                                <div class="edit-shop-profile-form-group">
                                    <label class="edit-shop-profile-form-label" for="openingTimeAm"><b>Morning:</b> Opening Time *</label>
                                    <input type="time" id="openingTimeAm" name="opening_time_am" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($shop['opening_time_am'] ?? ''); ?>" required>
                                </div>
                                <div class="edit-shop-profile-form-group">
                                    <label class="edit-shop-profile-form-label" for="closingTimeAm"><b>Morning:</b> Closing Time *</label>
                                    <input type="time" id="closingTimeAm" name="closing_time_am" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($shop['closing_time_am'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <p class="form-text text-muted" style="font-size: 0.875rem; margin-top: 10px;"><b>For businesses with a lunch break:</b> Set afternoon hours. Leave blank if you operate continuously.</p>

                            <div class="edit-shop-profile-form-grid" style="margin-top: 15px;">
                                <div class="edit-shop-profile-form-group">
                                    <label class="edit-shop-profile-form-label" for="openingTimePm"><b>Afternoon:</b> Opening Time</label>
                                    <input type="time" id="openingTimePm" name="opening_time_pm" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($shop['opening_time_pm'] ?? ''); ?>">
                                </div>
                                <div class="edit-shop-profile-form-group">
                                    <label class="edit-shop-profile-form-label" for="closingTimePm"><b>Afternoon:</b> Closing Time</label>
                                    <input type="time" id="closingTimePm" name="closing_time_pm" class="edit-shop-profile-form-input" value="<?php echo htmlspecialchars($shop['closing_time_pm'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="edit-shop-profile-form-group edit-shop-profile-full-width" id="businessDaysContainer">
                            <label class="edit-shop-profile-form-label">Business Days</label>
                            <div class="edit-shop-profile-day-selector" id="daySelector">
                                <?php
                                $rawDaysOpen = $shop['days_open'] ?? 'monday,tuesday,wednesday,thursday,friday';
                                $daysOpen = array_map('strtolower', array_map('trim', explode(',', $rawDaysOpen)));

                                $days = [
                                    'Mon' => 'Monday',
                                    'Tue' => 'Tuesday',
                                    'Wed' => 'Wednesday',
                                    'Thu' => 'Thursday',
                                    'Fri' => 'Friday',
                                    'Sat' => 'Saturday',
                                    'Sun' => 'Sunday'
                                ];

                                foreach ($days as $abbr => $full):
                                    $isChecked = in_array(strtolower($full), $daysOpen);
                                ?>
                                    <div class="edit-shop-profile-day-item">
                                        <input type="checkbox" id="day-<?php echo strtolower($abbr); ?>" name="days_open[]" value="<?php echo strtolower($full); ?>" <?php echo $isChecked ? 'checked' : ''; ?>>
                                        <label for="day-<?php echo strtolower($abbr); ?>"><?php echo $abbr; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" id="updateInfoBtn" class="edit-shop-profile-btn edit-shop-profile-btn-primary" disabled>Update Shop Information</button>
                    </form>
                </div>

                <div class="edit-shop-profile-tab-content" id="settingsTab">

                    <div class="edit-shop-profile-form-group edit-shop-profile-full-width" style="margin-top: 20px;">
                        <label class="edit-shop-profile-form-label" for="shopStatusSelect">Shop Status</label>
                        <select id="shopStatusSelect" class="edit-shop-profile-form-input edit-shop-profile-form-select">
                            <option value="open" <?php echo (isset($shop['shop_status']) && $shop['shop_status'] == 'open') ? 'selected' : ''; ?>>
                                Open
                            </option>
                            <option value="temporarily_closed" <?php echo (isset($shop['shop_status']) && $shop['shop_status'] == 'temporarily_closed') ? 'selected' : ''; ?>>
                                Temporarily Closed
                            </option>
                            <option value="permanently_closed" <?php echo (isset($shop['shop_status']) && $shop['shop_status'] == 'permanently_closed') ? 'selected' : ''; ?>>
                                Permanently
                            </option>
                        </select>

                        <div id="permanentCloseWarning" style="color: #dc3545; font-size: 0.875rem; margin-top: 10px; display: none; padding: 10px; border: 1px solid #dc3545; border-radius: 5px; background-color: #f8d7da;">
                            <strong><i class="fas fa-exclamation-triangle"></i> Warning:</strong>
                            Setting your shop to <strong>"Permanently Closed"</strong> is <u>irreversible</u>.
                            <ul style="margin-top: 8px; margin-bottom: 8px; padding-left: 20px;">
                                <li>Your shop will still be visible in search results.</li>
                                <li>Customers can view your profile, but booking, emergency services, and write a review will be disabled.</li>
                                <li>Once you confirm, you will no longer be able to manage your shop account.</li>
                            </ul>
                        </div>

                        <div id="permanentClosedInfo" style="color: #856404; font-size: 0.875rem; margin-top: 10px; display: none; padding: 10px; border: 1px solid #ffeeba; border-radius: 5px; background-color: #fff3cd;">
                            <strong><i class="fas fa-info-circle"></i> Shop Status:</strong>
                            Your shop is <strong>Permanently Closed</strong>.<br>
                            <li>This status is irreversible and cannot be changed.</li><br>
                            <li>Booking, emergency services, and write a review for your shop are disabled.</li>
                        </div>
                    </div>

                    <div class="edit-shop-profile-toggle-group" style="margin-top: 20px;">

                        <div class="edit-shop-profile-toggle-info">
                            <div class="edit-shop-profile-toggle-title">Enable Booking System</div>
                            <div class="edit-shop-profile-toggle-desc">Allow customers to make reservations in advance</div>
                        </div>
                        <div class="edit-shop-profile-toggle-switch" id="bookingToggle">
                            <div class="edit-shop-profile-toggle-slider"></div>
                        </div>
                    </div>

                    <div class="edit-shop-profile-toggle-group">
                        <div class="edit-shop-profile-toggle-info">
                            <div class="edit-shop-profile-toggle-title">Enable Emergency Assistance</div>
                            <div class="edit-shop-profile-toggle-desc">Allow customers to request urgent help from your shop during emergencies</div>
                        </div>
                        <div class="edit-shop-profile-toggle-switch" id="emergencyToggle">
                            <div class="edit-shop-profile-toggle-slider"></div>
                        </div>
                    </div>

                    <div class="collapsible-section" id="bookingConfigSection" style="display: none;">
                        <button type="button" class="collapsible-header">
                            <div class="w-100">
                                <h3 class="mb-1">Booking Configuration</h3>
                                <small class="text-muted d-block">Set up the required details, services, and available times for customer bookings.</small>
                            </div>
                            <i class="fas fa-chevron-down collapsible-icon"></i>
                        </button>

                        <div class="collapsible-content">
                            <div class="edit-shop-profile-booking-form" id="bookingForm">
                                <h4>Always Included</h4>
                                <div class="edit-shop-profile-included-field"><span>Full Name</span><span class="edit-shop-profile-badge-required">Required</span></div>
                                <div class="edit-shop-profile-included-field"><span>Email</span><span class="edit-shop-profile-badge-required">Required</span></div>
                                <div class="edit-shop-profile-included-field"><span>Phone</span><span class="edit-shop-profile-badge-required">Required</span></div>
                                <div class="edit-shop-profile-included-field"><span>Vehicle Make/Model/Year</span><span class="edit-shop-profile-badge-required">Required</span></div>
                                <div class="edit-shop-profile-included-field"><span>Vehicle Plate Number</span><span class="edit-shop-profile-badge-required">Required</span></div>
                                <div class="edit-shop-profile-included-field"><span>Vehicle Issues</span><span class="edit-shop-profile-badge-optional">Optional</span></div>
                                <div class="edit-shop-profile-included-field"><span>Customer Notes</span><span class="edit-shop-profile-badge-optional">Optional</span></div>

                                <h4>Booking Details</h4>

                                <div class="edit-shop-profile-dynamic-list-container">
                                    <label class="edit-shop-profile-form-label">Service Types</label>
                                    <small class="text-muted d-block mb-2">Select the specific services your shop offers for booking from your main services list.</small>
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#selectBookingServicesModal">
                                        <i class="fas fa-tasks me-2"></i> Manage Service Types
                                    </button>
                                    <div id="bookingServicesSummary" class="text-muted small mt-2 text-center">
                                        No services selected.
                                    </div>
                                </div>

                                <div class="edit-shop-profile-dynamic-list-container">
                                    <label class="edit-shop-profile-form-label">Transmission Types</label>
                                    <small class="text-muted d-block mb-2">Specify the types of vehicle transmissions your shop can handle.</small>
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#transmissionTypesModal">
                                        <i class="fas fa-cogs me-2"></i> Manage Transmission Types
                                    </button>
                                    <div id="transmissionTypesSummary" class="text-muted small mt-2 text-center">
                                        No types selected.
                                    </div>
                                </div>

                                <div class="edit-shop-profile-dynamic-list-container">
                                    <label class="edit-shop-profile-form-label">Fuel Types</label>
                                    <small class="text-muted d-block mb-2">List the fuel types you service (e.g., Gasoline, Diesel, Electric).</small>
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#fuelTypesModal">
                                        <i class="fas fa-gas-pump me-2"></i> Manage Fuel Types
                                    </button>
                                    <div id="fuelTypesSummary" class="text-muted small mt-2 text-center">
                                        No types selected.
                                    </div>
                                </div>

                                <div class="edit-shop-profile-dynamic-list-container">
                                    <label class="edit-shop-profile-form-label">Vehicle Types</label>
                                    <small class="text-muted d-block mb-2">List the vehicle types you service (e.g., Sedan, SUV, Motorcycle, Truck).</small>
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#vehicleTypesModal">
                                        <i class="fas fa-car me-2"></i> Manage Vehicle Types
                                    </button>
                                    <div id="vehicleTypesSummary" class="text-muted small mt-2 text-center">
                                        No types selected.
                                    </div>
                                </div>
                                <div class="edit-shop-profile-dynamic-list-container">
                                    <label class="edit-shop-profile-form-label">Preferred Dates and Times</label>
                                    <small class="text-muted d-block mb-2">Define the days and time slots when your shop accepts bookings, along with how many customers can book per slot.</small>
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#preferredDateTimesModal">
                                        <i class="fas fa-calendar-alt me-2"></i> Manage Booking Slots
                                    </button>
                                    <div id="preferredDateTimesSummary" class="text-muted small mt-2 text-center">
                                        No slots defined.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="collapsible-section" id="emergencyConfigSection" style="display: none;">
                        <button type="button" class="collapsible-header">
                            <div class="w-100">
                                <h3 class="mb-1">Emergency Configuration</h3>
                                <small class="text-muted d-block">Specify your shop’s availability and services offered for urgent assistance requests during emergencies.</small>
                            </div>
                            <i class="fas fa-chevron-down collapsible-icon"></i>
                        </button>
                        <div class="collapsible-content">
                            <div class="edit-shop-profile-dynamic-list-container">
                                <small class="text-muted d-block mb-2">Set specific days, times, and services when your shop is available to respond to emergency assistance requests.</small>
                                <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#emergencyConfigModal">
                                    <i class="fas fa-exclamation-circle me-2"></i> Manage Emergency Configuration
                                </button>
                                <div id="emergencyConfigSummary" class="text-muted small mt-2 text-center">
                                    No configuration set.
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="saveSettingsBtn" class="edit-shop-profile-btn edit-shop-profile-btn-primary" onclick="saveSettings()" disabled>Save Settings</button>

                </div>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>

    <div class="modal fade" id="unsavedChangesModal" tabindex="-1" aria-labelledby="unsavedChangesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unsavedChangesModalLabel"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Unsaved Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You have unsaved changes in your Shop Settings. If you leave now, your changes will be lost.</p>
                    <p>Are you sure you want to leave?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stay</button>
                    <button type="button" class="btn btn-danger" id="confirmLeaveBtn">Leave Anyway</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="selectBookingServicesModal" tabindex="-1" aria-labelledby="selectBookingServicesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectBookingServicesModalLabel">Select Booking Services</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs flex-nowrap" id="bookingEditServiceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="booking-select-services-tab" data-bs-toggle="tab" data-bs-target="#booking-select-services-pane" type="button" role="tab" aria-controls="booking-select-services-pane" aria-selected="true">
                                Select Services
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="booking-summary-tab" data-bs-toggle="tab" data-bs-target="#booking-summary-pane" type="button" role="tab" aria-controls="booking-summary-pane" aria-selected="false">
                                Summary
                                <span class="badge bg-primary rounded-pill ms-2" id="booking-summary-count">0</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content pt-3" id="bookingEditServiceTabsContent">
                        <div class="tab-pane fade show active" id="booking-select-services-pane" role="tabpanel" aria-labelledby="booking-select-services-tab" tabindex="0">
                            <p class="text-muted small">Expand each category and sub-category to select your services.</p>
                            <div class="accordion" id="bookingCategoryAccordion">
                                <div class="text-center p-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading services...</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="booking-summary-pane" role="tabpanel" aria-labelledby="booking-summary-tab" tabindex="0">
                            <div id="booking-edit-summary-container" style="max-height: 500px; overflow-y: auto;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transmissionTypesModal" tabindex="-1" aria-labelledby="transmissionTypesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transmissionTypesModalLabel">Manage Transmission Types</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="edit-shop-profile-form-label">Select from examples</label>
                    <div class="form-check form-check-inline border-bottom pb-2 mb-2 w-100">
                        <input class="form-check-input" type="checkbox" id="selectAllTransmissions">
                        <label class="form-check-label fw-bold" for="selectAllTransmissions">Select All</label>
                    </div>
                    <div id="predefinedTransmissionsList" class="mb-3">
                    </div>
                    
                    <label class="edit-shop-profile-form-label">Add custom type</label>
                    <div class="edit-shop-profile-add-item-form mb-3">
                        <input type="text" id="newTransmissionType" class="edit-shop-profile-form-input" placeholder="Add a transmission type...">
                        <button type="button" onclick="addManualItem('transmissionTypes')">Add</button>
                    </div>

                    <label class="edit-shop-profile-form-label">Your Selected Types</label>
                    <div id="transmissionTypesList" class="edit-shop-profile-dynamic-list">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fuelTypesModal" tabindex="-1" aria-labelledby="fuelTypesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fuelTypesModalLabel">Manage Fuel Types</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="edit-shop-profile-form-label">Select from examples</label>
                    <div class="form-check form-check-inline border-bottom pb-2 mb-2 w-100">
                        <input class="form-check-input" type="checkbox" id="selectAllFuelTypes">
                        <label class="form-check-label fw-bold" for="selectAllFuelTypes">Select All</label>
                    </div>
                    <div id="predefinedFuelTypesList" class="mb-3">
                    </div>
                    
                    <label class="edit-shop-profile-form-label">Add custom type</label>
                    <div class="edit-shop-profile-add-item-form mb-3">
                        <input type="text" id="newFuelType" class="edit-shop-profile-form-input" placeholder="Add a fuel type...">
                        <button type="button" onclick="addManualItem('fuelTypes')">Add</button>
                    </div>

                    <label class="edit-shop-profile-form-label">Your Selected Types</label>
                    <div id="fuelTypesList" class="edit-shop-profile-dynamic-list">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="vehicleTypesModal" tabindex="-1" aria-labelledby="vehicleTypesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vehicleTypesModalLabel">Manage Vehicle Types</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="edit-shop-profile-form-label">Select from examples</label>
                    <div class="form-check form-check-inline border-bottom pb-2 mb-2 w-100">
                        <input class="form-check-input" type="checkbox" id="selectAllVehicleTypes">
                        <label class="form-check-label fw-bold" for="selectAllVehicleTypes">Select All</label>
                    </div>
                    <div id="predefinedVehicleTypesList" class="mb-3">
                    </div>
                    
                    <label class="edit-shop-profile-form-label">Add custom type</label>
                    <div class="edit-shop-profile-add-item-form mb-3">
                        <input type="text" id="newVehicleType" class="edit-shop-profile-form-input" placeholder="Add a vehicle type...">
                        <button type="button" onclick="addManualItem('vehicleTypes')">Add</button>
                    </div>

                    <label class="edit-shop-profile-form-label">Your Selected Types</label>
                    <div id="vehicleTypesList" class="edit-shop-profile-dynamic-list">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="preferredDateTimesModal" tabindex="-1" aria-labelledby="preferredDateTimesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="preferredDateTimesModalLabel">Manage Booking Slots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="edit-shop-profile-form-label">Add New Slot</label>
                    <small class="text-muted d-block mb-2">Define the days and time slots when your shop accepts bookings.</small>
                    <div class="edit-shop-profile-add-datetime-form mb-3">
                        <div>
                            <label for="newPreferredDate" class="edit-shop-profile-form-label" style="font-size: 0.875rem;">Date</label>
                            <input type="date" id="newPreferredDate" class="edit-shop-profile-form-input">
                        </div>
                        <div>
                            <label for="newPreferredOpenTime" class="edit-shop-profile-form-label" style="font-size: 0.875rem;">Open</label>
                            <input type="time" id="newPreferredOpenTime" class="edit-shop-profile-form-input" placeholder="Open">
                        </div>
                        <div>
                            <label for="newPreferredCloseTime" class="edit-shop-profile-form-label" style="font-size: 0.875rem;">Close</label>
                            <input type="time" id="newPreferredCloseTime" class="edit-shop-profile-form-input" placeholder="Close">
                        </div>
                        <div>
                            <label for="newSlots" class="edit-shop-profile-form-label" style="font-size: 0.875rem;">Slots</label>
                            <input type="number" id="newSlots" class="edit-shop-profile-form-input" placeholder="Slots" min="1">
                        </div>
                        <div class="datetime-buttons">
                            <button type="button" class="btn btn-primary w-100" onclick="addDateTimeItem()">Add</button>
                            <button type="button" id="cancelDateTimeEdit" class="btn btn-secondary w-100" onclick="cancelDateTimeEdit()" style="display: none;">Cancel</button>
                        </div>
                    </div>
                    
                    <label class="edit-shop-profile-form-label">Your Available Slots</label>
                    <div id="preferredDateTimesList" class="edit-shop-profile-dynamic-list">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="emergencyConfigModal" tabindex="-1" aria-labelledby="emergencyConfigModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emergencyConfigModalLabel">Manage Emergency Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs flex-nowrap" id="emergencyConfigTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="emergency-hours-tab" data-bs-toggle="tab" data-bs-target="#emergency-hours-pane" type="button" role="tab" aria-controls="emergency-hours-pane" aria-selected="true">
                                <span class="d-none d-sm-inline">Emergency </span>Hours
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="emergency-services-tab" data-bs-toggle="tab" data-bs-target="#emergency-services-pane" type="button" role="tab" aria-controls="emergency-services-pane" aria-selected="false">
                                <span class="d-none d-sm-inline">Offered </span>Services
                                <span class="badge bg-primary rounded-pill ms-2" id="emergency-summary-count-2">0</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="emergencyConfigTabsContent">
                        <div class="tab-pane fade show active" id="emergency-hours-pane" role="tabpanel" aria-labelledby="emergency-hours-tab" tabindex="0">
                            <div class="edit-shop-profile-dynamic-list-container">
                                <label class="edit-shop-profile-form-label">Emergency Hours</label>
                                <small class="text-muted d-block mb-2">Set specific days and times when your shop is available to respond to emergency assistance requests.</small>
                                <div class="edit-shop-profile-add-datetime-form mb-3">
                                    <select id="newEmergencyDay" class="edit-shop-profile-form-input edit-shop-profile-form-select">
                                        <option value="" disabled selected>Day</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                    <input type="time" id="newEmergencyOpenTime" class="edit-shop-profile-form-input" placeholder="Open">
                                    <input type="time" id="newEmergencyCloseTime" class="edit-shop-profile-form-input" placeholder="Close">
                                    <div class="datetime-buttons">
                                        <button type="button" class="btn btn-primary" onclick="addEmergencyHour()">Add</button>
                                        <button type="button" id="cancelEmergencyEdit" class="btn btn-secondary" onclick="cancelEmergencyEdit()" style="display: none;">Cancel</button>
                                    </div>
                                </div>
                                <label class="edit-shop-profile-form-label">Your Available Hours</label>
                                <div id="emergencyHoursList" class="edit-shop-profile-dynamic-list">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="emergency-services-pane" role="tabpanel" aria-labelledby="emergency-services-tab" tabindex="0">
                            <div class="edit-shop-profile-dynamic-list-container">
                                <small class="text-muted d-block mb-2">Select the specific emergency services your shop provides.</small>
                                
                                <ul class="nav nav-tabs flex-nowrap" id="emergencyEditServiceTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="emergency-select-services-tab" data-bs-toggle="tab" data-bs-target="#emergency-select-services-pane-inner" type="button" role="tab" aria-controls="emergency-select-services-pane-inner" aria-selected="true">
                                            Select Services
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="emergency-summary-tab" data-bs-toggle="tab" data-bs-target="#emergency-summary-pane" type="button" role="tab" aria-controls="emergency-summary-pane" aria-selected="false">
                                            Summary
                                            <span class="badge bg-primary rounded-pill ms-2" id="emergency-summary-count">0</span>
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content pt-3" id="emergencyEditServiceTabsContent">
                                    <div class="tab-pane fade show active" id="emergency-select-services-pane-inner" role="tabpanel" aria-labelledby="emergency-select-services-tab" tabindex="0">
                                        <div class="accordion" id="emergencyCategoryAccordion">
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="emergency-summary-pane" role="tabpanel" aria-labelledby="emergency-summary-tab" tabindex="0">
                                        <div id="emergency-edit-summary-container" style="max-height: 400px; overflow-y: auto;">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"></script>
    
    <?php include 'include/toast.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script src="../assets/js/shop-settings.js"></script>
    <script src="../assets/js/edit-shop-profile-tab.js"></script>
    <script src="../assets/js/update-shop-profile.js"></script>

</body>

</html>