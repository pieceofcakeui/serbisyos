<?php
session_start();
include 'db_connection.php';
header('Content-Type: text/html');

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['user_id'])) {
    die('<div class="alert alert-danger">Session expired. Please login again.</div>');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div class="alert alert-danger">Invalid booking ID</div>');
}

$bookingId = (int) $_GET['id'];
$userId = $_SESSION['user_id'];

try {
    $query = "SELECT 
        sb.id,
        sb.customer_name,
        sb.customer_phone,
        sb.customer_email,
        sb.vehicle_make,
        sb.vehicle_model,
        sb.plate_number,
        sb.vehicle_year,
        sb.transmission_type,
        sb.fuel_type,
        sb.vehicle_type,
        sb.vehicle_issues,
        sb.service_type,
        sb.preferred_datetime,
        sb.customer_notes,
        sb.created_at,
        u.fullname AS customer_fullname,
        sa.shop_name
    FROM services_booking sb
    JOIN users u ON sb.user_id = u.id
    JOIN shop_applications sa ON sb.shop_id = sa.id
    WHERE sb.id = ? AND sb.shop_id IN (SELECT id FROM shop_applications WHERE user_id = ?)";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $bookingId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die('<div class="alert alert-warning">No booking found with ID: ' . htmlspecialchars((string)$bookingId) . '</div>');
    }

    $booking = $result->fetch_assoc();

    $service_types = json_decode($booking['service_type'] ?? '');
    $formatted_services = is_array($service_types) ? implode(', ', $service_types) : str_replace(['[', ']', '"', "'"], '', $booking['service_type'] ?? '');

    $datetime_display = htmlspecialchars($booking['preferred_datetime'] ?? '');
    $datetime_display = str_replace(['"', "'", "[", "]"], '', $datetime_display);
    ?>
    <style>
.request-details-container {
    background: #f9fafb;
    border-radius: 12px;
    padding: 25px;
    max-width: 900px;
    margin: 30px auto;
    font-family: "Poppins", sans-serif;
    color: #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.detail-section {
    background: #fff;
    border-radius: 10px;
    padding: 20px 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #0d6efd;
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
}

.section-title i {
    margin-right: 10px;
    color: #0d6efd;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 12px 25px;
}

.detail-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px 15px;
}

.detail-label {
    font-weight: 600;
    color: #495057;
}

.detail-value {
    color: #212529;
    margin-left: 5px;
}

.description-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    line-height: 1.6;
    color: #444;
}

.description-content em {
    color: #6c757d;
}

@media (max-width: 600px) {
    .request-details-container {
        padding: 15px;
    }
    .detail-section {
        padding: 15px;
    }
    .section-title {
        font-size: 16px;
    }
}
</style>

    <div class="request-details-container">
        <section class="detail-section">
            <h3 class="section-title">
                <i class="fas fa-user"></i>
                Customer Information
            </h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Name: </span>
                    <span class="detail-value"><?= htmlspecialchars($booking['customer_name'] ?? 'Not provided') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Phone: </span>
                    <span class="detail-value"><?= htmlspecialchars($booking['customer_phone'] ?? 'Not provided') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email: </span>
                    <span class="detail-value"><?= htmlspecialchars($booking['customer_email'] ?? 'Not provided') ?></span>
                </div>
            </div>
        </section>

        <section class="detail-section">
            <h3 class="section-title">
                <i class="fas fa-car"></i>
                Vehicle Information
            </h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Make/Model: </span>
                    <span class="detail-value">
                        <?= htmlspecialchars(($booking['vehicle_make'] ?? '') . ' ' . ($booking['vehicle_model'] ?? '')) ?>
                    </span>
                </div>
                 <div class="detail-item">
                    <span class="detail-label">Vehicle Plate Number: </span>
                    <span class="detail-value"><?= htmlspecialchars($booking['plate_number'] ?? 'Not specified') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Year: </span>
                    <span class="detail-value"><?= htmlspecialchars($booking['vehicle_year'] ?? 'Not specified') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Vehicle Type: </span>
                    <span class="detail-value"><?= htmlspecialchars($booking['vehicle_type'] ?? 'Not specified') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Transmission: </span>
                    <span class="detail-value"><?= htmlspecialchars($booking['transmission_type'] ?? 'Not specified') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Fuel Type: </span>
                    <span class="detail-value"><?= htmlspecialchars($booking['fuel_type'] ?? 'Not specified') ?></span>
                </div>
            </div>
        </section>

        <section class="detail-section">
            <h3 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Booking Information
            </h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Service Type: </span>
                    <span class="detail-value"><?= htmlspecialchars($formatted_services ?? 'Not specified') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Notes: </span>
                    <span class="detail-value">
                        <?php
                        $notes = isset($booking['customer_notes']) ? (string) $booking['customer_notes'] : '';
                        $notes = trim($notes);
                        if (!empty($notes)) {
                            echo nl2br(htmlspecialchars($notes));
                        } else {
                            echo '<em>No notes provided</em>';
                        }
                        ?>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date and Time: </span>
                    <span class="detail-value"><?= $datetime_display ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Booking Created: </span>
                    <span class="detail-value">
                        <?= date('M j, Y g:i A', strtotime($booking['created_at'] ?? 'now')) ?>
                    </span>
                </div>
            </div>
        </section>

<section class="detail-section">
    <h3 class="section-title">
        <i class="fas fa-exclamation-circle"></i>
        Vehicle Issues
    </h3>
    <div class="description-content">
        <?php
        $issues = trim($booking['vehicle_issues'] ?? '');
        echo !empty($issues)
            ? nl2br(htmlspecialchars($issues))
            : '<em>Not Provided</em>';
        ?>
    </div>
</section>

    </div>
    <?php

} catch (Exception $e) {
    die('<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>');
}
?>