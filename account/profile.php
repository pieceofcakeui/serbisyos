<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/security_helper.php';

require_once __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY']);
define('ENCRYPTION_METHOD', 'AES-256-CBC');

function decryptData($data)
{
    if (empty($data) || strpos($data, ':') === false) {
        return $data;
    }
    $parts = explode(':', $data, 2);
    if (count($parts) !== 2) {
        return false;
    }

    $iv = base64_decode($parts[0]);
    $encrypted = base64_decode($parts[1]);

    if ($iv === false || $encrypted === false) {
        return false;
    }

    return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_review') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Authentication required. Please log in again.']);
        exit;
    }
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];
    if (!$review_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid review ID.']);
        exit;
    }
    $stmt = $conn->prepare("SELECT user_id FROM shop_ratings WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Review not found.']);
        exit;
    }
    $review = $result->fetch_assoc();
    $stmt->close();
    if ($review['user_id'] != $user_id) {
        echo json_encode(['success' => false, 'message' => 'You are not authorized to delete this review.']);
        exit;
    }
    $conn->begin_transaction();
    try {
        $stmt1 = $conn->prepare("DELETE FROM review_likes WHERE review_id = ?");
        $stmt1->bind_param("i", $review_id);
        $stmt1->execute();
        $stmt1->close();
        $stmt2 = $conn->prepare("DELETE FROM respond_reviews WHERE review_id = ?");
        $stmt2->bind_param("i", $review_id);
        $stmt2->execute();
        $stmt2->close();
        $stmt3 = $conn->prepare("DELETE FROM shop_ratings WHERE id = ? AND user_id = ?");
        $stmt3->bind_param("ii", $review_id, $user_id);
        $stmt3->execute();
        $stmt3->close();
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Review deleted successfully.']);
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        error_log("Review Deletion Error: " . $exception->getMessage());
        echo json_encode(['success' => false, 'message' => 'A database error occurred. Failed to delete review.']);
    }
    exit;
}
$user_id = $_SESSION['user_id'];
if ($user_id) {
    $shopQuery = $conn->prepare("SELECT id FROM shop_applications WHERE user_id = ?");
    $shopQuery->bind_param("i", $user_id);
    $shopQuery->execute();
    $shopResult = $shopQuery->get_result();
    $shop = $shopResult->fetch_assoc();
    if ($shop) {
        $shop_id = $shop['id'];
        $emergencyQuery = $conn->prepare("
            SELECT er.id, u.fullname, er.issue_description, er.created_at
            FROM emergency_requests er
            JOIN users u ON er.user_id = u.id
            WHERE er.shop_id = ? AND er.seen_emergency_request = 0
            ORDER BY er.created_at DESC LIMIT 1
        ");
        $emergencyQuery->bind_param("i", $shop_id);
        $emergencyQuery->execute();
        $emergencyResult = $emergencyQuery->get_result();
        $emergency = $emergencyResult->fetch_assoc();
        if ($emergency) {
            $updateQuery = $conn->prepare("UPDATE emergency_requests SET seen_emergency_request = 1 WHERE id = ?");
            $updateQuery->bind_param("i", $emergency['id']);
            $updateQuery->execute();
        }
    }
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login first";
    header("Location: ../login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    header('Content-Type: application/json');
    try {
        $user_id = $_SESSION['user_id'];
        $is_verified_backend = false;
        $statusQuery = $conn->prepare("SELECT status FROM verification_submissions WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        if ($statusQuery) {
            $statusQuery->bind_param("i", $user_id);
            $statusQuery->execute();
            $statusResult = $statusQuery->get_result();
            if ($statusRow = $statusResult->fetch_assoc()) {
                if (trim(strtolower($statusRow['status'])) === 'verified') {
                    $is_verified_backend = true;
                }
            }
            $statusQuery->close();
        }
        $new_filename = null;
        $contact_number = $_POST['contact_number'];
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['profile_picture']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array(strtolower($filetype), $allowed)) {
                echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG & WEBP files are allowed.']);
                exit;
            }
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = '../assets/img/profile/' . $new_filename;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $old_picture = $stmt->get_result()->fetch_assoc()['profile_picture'];
                if ($old_picture && $old_picture != 'profile-user.png' && file_exists('../assets/img/profile/' . $old_picture)) {
                    unlink('../assets/img/profile/' . $old_picture);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload profile picture.']);
                exit;
            }
        }
        $update_fields = ['contact_number = ?'];
        $bind_types = 's';
        $bind_params = [&$contact_number];
        if ($new_filename) {
            $update_fields[] = 'profile_picture = ?';
            $bind_types .= 's';
            $bind_params[] = &$new_filename;
        }
        if (!$is_verified_backend) {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $update_fields = array_merge($update_fields, ['fullname = ?', 'email = ?']);
            $bind_types .= 'ss';
            $temp_params = array_merge($bind_params, [&$fullname, &$email]);
            $bind_params = $temp_params;
        } else {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
        }
        $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = ?";
        $bind_types .= 'i';
        $bind_params[] = &$user_id;
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($bind_types, ...$bind_params);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . $conn->error]);
            exit;
        }
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $stmt->error]);
            exit;
        }
        $response_data = [
            'contact_number' => $contact_number,
            'fullname' => $fullname
        ];
        $response = ['success' => true, 'message' => 'Profile updated successfully!', 'data' => $response_data];
        if ($new_filename) {
            $response['profile_picture'] = $new_filename;
        }
        echo json_encode($response);
        exit;
    } catch (Exception $e) {
        error_log("Profile Update Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        exit;
    }
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$profile_user = null;
$verification_status = 'unverified';
$page_title_name = 'User';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT fullname, email, contact_number, profile_picture, auth_provider, profile_type FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $base_user = $result->fetch_assoc();
    $stmt->close();

    if ($base_user) {
        $page_title_name = $base_user['fullname'];
        $profile_user = [
            'email' => $base_user['email'],
            'contact_number' => $base_user['contact_number'],
            'profile_picture' => $base_user['profile_picture'],
            'auth_provider' => $base_user['auth_provider'],
            'profile_type' => $base_user['profile_type'],
            'fullname' => $base_user['fullname'],
        ];

        $verifyStmt = $conn->prepare("SELECT status, full_name, gender, birthday, address_barangay, address_town_city, address_province, address_postal_code FROM verification_submissions WHERE user_id = ? ORDER BY submission_date DESC, id DESC LIMIT 1");
        $verifyStmt->bind_param("i", $user_id);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        $verifyData = $verifyResult->fetch_assoc();
        $verifyStmt->close();

        if ($verifyData) {
            $verification_status = $verifyData['status'];
            if (trim(strtolower($verification_status)) === 'verified') {
                $profile_user['fullname'] = decryptData($verifyData['full_name']);
                $profile_user['gender'] = $verifyData['gender'];
                $profile_user['birthday'] = decryptData($verifyData['birthday']);
                $profile_user['address_barangay'] = decryptData($verifyData['address_barangay']);
                $profile_user['address_town_city'] = decryptData($verifyData['address_town_city']);
                $profile_user['address_province'] = decryptData($verifyData['address_province']);
                $profile_user['address_postal_code'] =  decryptData($verifyData['address_postal_code']);
            }
        }
    }
}
$savedShops = [];
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
    // MODIFIED: Added sa.shop_slug to the query
    $sql_saved = "SELECT sa.id, sa.shop_name, sa.shop_slug, sa.shop_logo, sa.shop_location FROM save_shops ss INNER JOIN shop_applications sa ON ss.shop_id = sa.id WHERE ss.user_id = ? ORDER BY ss.saved_at DESC";
    $stmt_saved = $conn->prepare($sql_saved);
    $stmt_saved->bind_param("i", $current_user_id);
    $stmt_saved->execute();
    $result_saved = $stmt_saved->get_result();
    while ($row = $result_saved->fetch_assoc()) {
        $savedShops[] = $row;
    }
    $stmt_saved->close();
}
$myReviews = [];
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
    // MODIFIED: Added sa.shop_slug to the query
    $sql_reviews = "SELECT sr.id, sr.shop_id, sr.rating, sr.comment, sr.created_at, sa.shop_name, sa.shop_logo, sa.shop_slug, (SELECT COUNT(*) FROM review_likes WHERE review_id = sr.id) AS like_count, rr.response AS owner_response, rr.created_at AS response_created_at FROM shop_ratings sr JOIN shop_applications sa ON sr.shop_id = sa.id LEFT JOIN respond_reviews rr ON sr.id = rr.review_id AND rr.shop_owner_id = sa.user_id WHERE sr.user_id = ? ORDER BY sr.created_at DESC";
    $stmt_reviews = $conn->prepare($sql_reviews);
    $stmt_reviews->bind_param("i", $current_user_id);
    $stmt_reviews->execute();
    $result_reviews = $stmt_reviews->get_result();
    while ($row = $result_reviews->fetch_assoc()) {
        $myReviews[] = $row;
    }
    $stmt_reviews->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title_name); ?> - Profile</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/profile.css">
    <style>
        .verification-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }

        .verification-badge {
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        .verification-badge.verified {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .verification-badge.unverified {
            background-color: #e2e3e5;
            color: #41464b;
        }

        .verification-badge.under-review {
            background-color: #fff3cd;
            color: #664d03;
        }

        .verification-badge.rejected {
            background-color: #f8d7da;
            color: #58151c;
        }

        .verify-link-icon {
            color: #0d6efd;
            font-size: 1rem;
            line-height: 1;
            transition: transform 0.2s ease-in-out;
        }

        .verify-link-icon:hover {
            transform: scale(1.2);
        }
    </style>
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>
    <div id="main-content" class="main-content">
        <div class="profile-section">
            <div class="container">
                <div class="profile-layout">
                    <aside class="profile-sidebar">
                        <div class="profile-header">
                            <img src="../assets/img/profile/<?php echo htmlspecialchars($profile_user['profile_picture'] ? $profile_user['profile_picture'] : 'profile-user.png'); ?>" alt="Profile Picture" class="profile-picture">
                            <h4><?php echo htmlspecialchars($profile_user['fullname']); ?></h4>
                            <p><?php echo $profile_user['profile_type'] === 'owner' ? 'Shop Owner' : 'Vehicle Owner'; ?></p>
                            <?php if ($profile_user['profile_type'] !== 'owner') : ?>
                                <div class="verification-status">
                                    <?php if (trim(strtolower($verification_status)) === 'verified') : ?>
                                        <span class="verification-badge verified"><i class="fas fa-check-circle me-1"></i> Verified</span>
                                    <?php elseif (trim(strtolower($verification_status)) === 'pending') : ?>
                                        <span class="verification-badge under-review"><i class="fas fa-clock me-1"></i> Under Review</span>
                                    <?php elseif (trim(strtolower($verification_status)) === 'rejected') : ?>
                                        <span class="verification-badge rejected"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                        <a href="verify-account.php" class="verify-link-icon" title="Submit Verification Again"><i class="fas fa-shield-alt"></i></a>
                                    <?php else :  ?>
                                        <span class="verification-badge unverified"><i class="fas fa-exclamation-circle me-1"></i> Unverified</span>
                                        <a href="verify-account.php" class="verify-link-icon" title="Verify Your Account"><i class="fas fa-shield-alt"></i></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <nav class="nav flex-column nav-pills profile-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile" role="tab"><i class="fas fa-user-circle"></i> Profile Details</a>
                            <a class="nav-link" id="v-pills-save-shops-tab" data-bs-toggle="pill" href="#v-pills-save-shops" role="tab"><i class="fas fa-bookmark"></i> Saved Shops</a>
                            <a class="nav-link" id="v-pills-reviews-tab" data-bs-toggle="pill" href="#v-pills-reviews" role="tab"><i class="fas fa-star"></i> My Reviews</a>
                            <a class="nav-link" href="settings-and-privacy.php"><i class="fas fa-cog"></i> Settings</a>
                        </nav>
                        <div class="profile-nav-mobile">
                            <a class="nav-link active" id="v-pills-profile-tab-mobile" data-bs-toggle="pill" href="#v-pills-profile" role="tab">Profile Details</a>
                            <div class="dropdown">
                                <button class="btn btn-light" type="button" id="profileMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileMenuButton">
                                    <li><a class="dropdown-item" data-bs-toggle="pill" href="#v-pills-profile">Profile Details</a></li>
                                    <li><a class="dropdown-item" data-bs-toggle="pill" href="#v-pills-save-shops">My Saved Shops</a></li>
                                    <li><a class="dropdown-item" data-bs-toggle="pill" href="#v-pills-reviews">My Reviews</a></li>
                                    <li><a class="dropdown-item" href="settings-and-privacy.php">Settings</a></li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                    <main class="profile-content">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                                <h2>Profile Information</h2>
                                <div id="profile-view">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-group"><label>Full Name</label>
                                                <p id="view-name"><?php echo htmlspecialchars($profile_user['fullname']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group"><label>Email Address</label>
                                                <p id="view-email"><?php echo htmlspecialchars($profile_user['email']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group"><label>Phone Number</label>
                                                <p id="view-phone"><?php if (!empty($profile_user['contact_number'])) : ?><?php echo htmlspecialchars($profile_user['contact_number']); ?><?php else : ?><span class="placeholder-text">Not Provided</span><?php endif; ?></p>
                                            </div>
                                        </div>
                                        <?php if (trim(strtolower($verification_status)) === 'verified') : ?>
                                            <div class="col-md-6">
                                                <div class="info-group"><label>Birthday</label>
                                                    <p id="view-birthdate"><?php if (!empty($profile_user['birthday'])) : ?><?php echo htmlspecialchars(date('F j, Y', strtotime($profile_user['birthday']))); ?><?php else : ?><span class="placeholder-text">Not Provided</span><?php endif; ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group"><label>Gender</label>
                                                    <p id="view-gender"><?php if (!empty($profile_user['gender'])) : ?><?php echo htmlspecialchars(ucfirst($profile_user['gender'])); ?><?php else : ?><span class="placeholder-text">Not Provided</span><?php endif; ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label>Address</label>
                                                <p id="view-address">
                                                    <?php
                                                    $address_parts = array_filter([
                                                        $profile_user['address_barangay'] ?? null,
                                                        $profile_user['address_town_city'] ?? null,
                                                        $profile_user['address_province'] ?? null,
                                                        ($profile_user['address_postal_code'] ?? null) ? 'Philippines ' . $profile_user['address_postal_code'] : null
                                                    ]);

                                                    if (!empty($address_parts)) {
                                                        echo htmlspecialchars(implode(', ', $address_parts));
                                                    } else {
                                                        echo '<span class="placeholder-text">Not Provided</span>';
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mt-3" id="edit-profile-btn">Edit Profile</button>
                                </div>
                                <div id="profile-edit" style="display: none;">
                                    <?php
                                    $is_verified = (trim(strtolower($verification_status)) === 'verified');
                                    $readonly_attr = $is_verified ? 'readonly' : '';
                                    ?>
                                    <?php if ($is_verified) : ?>
                                        <div class="alert alert-info" role="alert"><i class="fas fa-info-circle me-2"></i>As a verified user, some of your details are locked. You can only update your profile picture and phone number.</div>
                                    <?php else : ?>
                                        <div class="alert alert-warning" role="alert"><i class="fas fa-exclamation-triangle me-2"></i>Your personal details like birthday, gender, and address can only be added by verifying your account. <a href="verify-account.php" class="alert-link">Verify Now</a>.</div>
                                    <?php endif; ?>
                                    <form id="edit-profile-form" method="POST" enctype="multipart/form-data">
                                        <div class="mb-4 d-flex justify-content-center">
                                            <div class="form-profile-picture-container">
                                                <img id="profile-preview" src="../assets/img/profile/<?php echo htmlspecialchars($profile_user['profile_picture'] ? $profile_user['profile_picture'] : 'profile-user.png'); ?>" alt="Profile Picture">
                                                <label for="edit-profile-pic" class="camera-icon"><i class="fas fa-camera"></i></label>
                                                <input type="file" class="d-none" id="edit-profile-pic" name="profile_picture" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3"><label class="form-label">Full Name</label><input type="text" class="form-control" name="fullname" value="<?php echo htmlspecialchars($profile_user['fullname']); ?>" <?php echo $readonly_attr; ?>></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Email Address</label><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($profile_user['email']); ?>" readonly></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Phone Number</label><input type="text" class="form-control" name="contact_number" placeholder="09XXXXXXXXX" maxlength="11" pattern="\d{11}" inputmode="numeric" value="<?php echo htmlspecialchars($profile_user['contact_number'] ?? ''); ?>"></div>

                                            <div class="col-md-6 mb-3"><label class="form-label">Birthday</label><input type="text" class="form-control" value="<?php echo htmlspecialchars(isset($profile_user['birthday']) ? date('F j, Y', strtotime($profile_user['birthday'])) : 'Not Provided'); ?>" readonly></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Gender</label><input type="text" class="form-control" value="<?php echo htmlspecialchars(isset($profile_user['gender']) ? ucfirst($profile_user['gender']) : 'Not Provided'); ?>" readonly></div>

                                            <div class="col-md-6 mb-3"><label for="edit-province" class="form-label">Province</label><input type="text" class="form-control" id="edit-province" name="address_province" value="<?php echo htmlspecialchars($profile_user['address_province'] ?? 'Not Provided'); ?>" readonly></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Town/Municipality</label><input type="text" class="form-control" name="address_town_city" value="<?php echo htmlspecialchars($profile_user['address_town_city'] ?? 'Not Provided'); ?>" readonly></div>
                                            <div class="col-md-6 mb-3"><label class="form-label">Barangay</label><input type="text" class="form-control" name="address_barangay" value="<?php echo htmlspecialchars($profile_user['address_barangay'] ?? 'Not Provided'); ?>" readonly></div>
                                            <div class="col-md-12 mb-3"><label class="form-label">Postal Code</label><input type="text" class="form-control" name="address_postal_code" value="<?php echo htmlspecialchars($profile_user['address_postal_code'] ?? 'Not Provided'); ?>" readonly></div>
                                        </div>
                                        <button type="submit" name="update_profile" class="btn btn-primary mt-3" style="font-weight: bold;" id="updateBtn"><i class="fas fa-save me-2" style="color: black;"></i><span class="btn-text">Save Changes</span><span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span></button>
                                        <button type="button" class="btn btn-secondary mt-3" id="cancel-edit-btn">Cancel</button>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-save-shops" role="tabpanel">
                                <h2>Saved Shops</h2>
                                <div class="row g-3" id="favorite-shops-list">
                                    <?php if (!empty($savedShops)) : ?>
                                        <?php foreach ($savedShops as $shop) : ?>
                                            <div class="col-12 shop-card-wrapper">
                                                <div class="saved-shop-item">
                                                    <div class="d-sm-flex align-items-center">
                                                        <img src="uploads/shop_logo/<?php echo htmlspecialchars($shop['shop_logo'] ? $shop['shop_logo'] : 'logo.jpg'); ?>" class="saved-shop-logo mb-3 mb-sm-0" alt="<?php echo htmlspecialchars($shop['shop_name']); ?> Logo">
                                                        <div class="shop-details ms-sm-3 text-center text-sm-start">
                                                            <a href="shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>" class="shop-name-link">
                                                                <h5 class="mb-1"><?php echo htmlspecialchars($shop['shop_name']); ?></h5>
                                                            </a>
                                                            <p class="mb-0 text-muted small"><?php echo htmlspecialchars($shop['shop_location']); ?></p>
                                                        </div>
                                                    </div>
                                                    <button class="btn-unfavorite" title="Remove from saved shops" data-shop-id="<?php echo htmlspecialchars($shop['id']); ?>"><i class="fas fa-bookmark"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <div class="col-12">
                                            <p class="text-center text-muted mt-3 no-save-shops-message">You have no saved shops yet.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-reviews" role="tabpanel">
                                <h2>My Reviews</h2>
                                <div id="my-reviews-list">
                                    <?php if (!empty($myReviews)) : ?>
                                        <?php foreach ($myReviews as $review) : ?>
                                            <div class="review-item-container" data-review-id-container="<?php echo $review['id']; ?>">
                                                <div class="review-item">
                                                    <img src="uploads/shop_logo/<?php echo htmlspecialchars($review['shop_logo'] ? $review['shop_logo'] : 'logo.jpg'); ?>" alt="<?php echo htmlspecialchars($review['shop_name']); ?> Logo">
                                                    <div class="w-100">
                                                        <h5>Review for <a href="shop/<?php echo htmlspecialchars($review['shop_slug']); ?>" class="text-decoration-none" style="color: inherit;"><strong><?php echo htmlspecialchars($review['shop_name']); ?></strong></a></h5>
                                                        <p class="text-warning mb-1"><?php for ($i = 1; $i <= 5; $i++) : ?><i class="<?php echo ($i <= $review['rating']) ? 'fas' : 'far'; ?> fa-star"></i><?php endfor; ?></p>
                                                        <p class="text-muted small mb-2"><?php echo date("F j, Y, g:i A", strtotime($review['created_at'])); ?></p>
                                                        <p>"<?php echo nl2br(htmlspecialchars($review['comment'])); ?>"</p>
                                                        <div class="review-actions mt-3 d-flex justify-content-between align-items-center">
                                                            <div class="text-muted"><i class="fas fa-thumbs-up text-primary"></i> <?php echo $review['like_count']; ?> Likes</div>
                                                            <button class="btn btn-sm btn-outline-danger delete-review-btn" data-review-id="<?php echo $review['id']; ?>"><i class="fas fa-trash"></i> Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if (!empty($review['owner_response'])) : ?>
                                                    <div class="owner-response mt-3 ms-md-5 ps-4">
                                                        <h6><strong>Shop Owner's Response:</strong></h6>
                                                        <p class="response-text mb-1">"<?php echo nl2br(htmlspecialchars($review['owner_response'])); ?>"</p>
                                                        <p class="text-muted small mb-0"><?php echo date("F j, Y, g:i A", strtotime($review['response_created_at'])); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <div class="text-center py-5 no-reviews-message">
                                            <p class="text-muted">You haven't written any reviews yet.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="removeShopModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <p class="fw-bold">Are you sure you want to remove this shop from your Saved Shops?</p>
                    <div class="d-flex justify-content-center gap-2 mt-4"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger confirm-remove" style="height: 39px; padding: 0 10px;">Remove</button></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteReviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <p class="fw-bold">Are you sure you want to permanently delete this review?</p>
                    <p class="text-muted small">This action cannot be undone.</p>
                    <div class="d-flex justify-content-center gap-2 mt-4"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="confirmDeleteReviewBtn" style="height: 39px; padding: 0 10px;">Delete</button></div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'include/emergency-modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script>
        $(document).ready(function() {
            if (window.location.hash) {
                let triggerEl = document.querySelector('a[data-bs-toggle="pill"][href="' + window.location.hash + '"]');
                if (triggerEl) {
                    let tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            }
            document.querySelectorAll('a[data-bs-toggle="pill"]').forEach((tabEl) => {
                tabEl.addEventListener('shown.bs.tab', event => {
                    history.pushState(null, null, event.target.getAttribute('href'));
                });
            });
            toastr.options = {
                "closeButton": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "timeOut": 2000,
                "extendedTimeOut": 1500,
                "preventDuplicates": true
            };
            $('#edit-profile-btn').on('click', function() {
                $('#profile-view').hide();
                $('#profile-edit').fadeIn();
            });
            $('#cancel-edit-btn').on('click', function() {
                $('#edit-profile-form')[0].reset();
                $('#profile-preview').attr('src', $('.profile-picture').attr('src'));
                $('#profile-edit').hide();
                $('#profile-view').fadeIn();
            });
            $('#edit-profile-form').on('submit', function(e) {
                e.preventDefault();
                const updateBtn = $('#updateBtn');
                updateBtn.prop('disabled', true);
                updateBtn.find('.btn-text').text('Saving...');
                updateBtn.find('.spinner-border').removeClass('d-none');
                var formData = new FormData(this);
                formData.append('update_profile', '1');
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            if (response.data) {
                                $('#view-name, .profile-header h4').text(response.data.fullname);
                                $('#view-phone').html(response.data.contact_number || '<span class="placeholder-text">Not Provided</span>');
                                let address = [response.data.barangay, response.data.town, response.data.province, response.data.postal_code ? 'Philippines ' + response.data.postal_code : null].filter(Boolean).join(', ');
                                $('#view-address').html(address || '<span class="placeholder-text">Not Provided</span>');
                            }
                            if (response.profile_picture) {
                                var newPicSrc = '../assets/img/profile/' + response.profile_picture + '?' + new Date().getTime();
                                $('#profile-preview, .profile-picture').attr('src', newPicSrc);
                            }
                            toastr.success(response.message);
                            $('#profile-edit').hide();
                            $('#profile-view').show();
                        } else {
                            toastr.error(response.message || 'Error updating profile. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('A server error occurred. Please try again later.');
                        console.error('AJAX Error:', xhr.responseText);
                    },
                    complete: function() {
                        updateBtn.prop('disabled', false);
                        updateBtn.find('.btn-text').text('Save Changes');
                        updateBtn.find('.spinner-border').addClass('d-none');
                    }
                });
            });
            let shopIdToRemove;
            let shopCardWrapperToRemove;
            $('#v-pills-save-shops').on('click', '.btn-unfavorite', function(e) {
                e.preventDefault();
                shopIdToRemove = $(this).data('shop-id');
                shopCardWrapperToRemove = $(this).closest('.shop-card-wrapper');
                $('#removeShopModal').modal('show');
            });
            $('#removeShopModal').on('click', '.confirm-remove', function() {
                $('#removeShopModal').modal('hide');
                if (!shopIdToRemove) return;
                $.ajax({
                    url: 'backend/delete_save_shop.php',
                    type: 'POST',
                    data: {
                        shop_id: shopIdToRemove
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.save === false) {
                            toastr.success('Shop removed from your Saved Shops.');
                            shopCardWrapperToRemove.fadeOut(400, function() {
                                $(this).remove();
                                if ($('#favorite-shops-list .shop-card-wrapper').length === 0) {
                                    $('#favorite-shops-list').html('<div class="col-12"><p class="text-center text-muted mt-3 no-save-shops-message">You have no saved shops yet.</p></div>');
                                }
                            });
                        } else {
                            toastr.error(response.message || 'Could not remove shop.');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('An error occurred. Please try again.');
                        console.error("AJAX Error:", status, error);
                    },
                    complete: function() {
                        shopIdToRemove = null;
                        shopCardWrapperToRemove = null;
                    }
                });
            });
            let reviewIdToDelete = null;
            const deleteReviewModal = new bootstrap.Modal(document.getElementById('deleteReviewModal'));
            $('#my-reviews-list').on('click', '.delete-review-btn', function() {
                reviewIdToDelete = $(this).data('review-id');
                deleteReviewModal.show();
            });
            $('#confirmDeleteReviewBtn').on('click', function() {
                if (!reviewIdToDelete) return;
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: {
                        action: 'delete_review',
                        review_id: reviewIdToDelete
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $(`div[data-review-id-container="${reviewIdToDelete}"]`).fadeOut(400, function() {
                                $(this).remove();
                                if ($('#my-reviews-list .review-item-container').length === 0) {
                                    $('#my-reviews-list').html('<div class="text-center py-5 no-reviews-message"><p class="text-muted">You haven\'t written any reviews yet.</p></div>');
                                }
                            });
                        } else {
                            toastr.error(response.message || 'Could not delete review.');
                        }
                    },
                    error: function() {
                        toastr.error('A server error occurred. Please try again.');
                    },
                    complete: function() {
                        deleteReviewModal.hide();
                        reviewIdToDelete = null;
                    }
                });
            });
            $('#edit-profile-pic').on('change', function(event) {
                const [file] = event.target.files;
                if (file) {
                    $('#profile-preview').attr('src', URL.createObjectURL(file));
                }
            });
            $('.profile-nav-mobile .dropdown-item').on('click', function() {
                const target = $(this).attr('href');
                const targetTab = new bootstrap.Tab(document.querySelector(`a[data-bs-toggle="pill"][href="${target}"]`));
                targetTab.show();
                $('.profile-nav-mobile > .nav-link').text($(this).text());
            });
            $('#v-pills-profile-tab').on('shown.bs.tab', function() {
                $('.profile-nav-mobile > .nav-link').text('Profile Details');
            });
        });
    </script>
</body>

</html>