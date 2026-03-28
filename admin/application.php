<?php
include 'backend/auth.php';
include 'backend/db_connection.php';
include 'backend/application.php';
include 'backend/security_helper.php';

$specific_app_id = 0;

if (isset($_POST['app_id'])) {
    $encrypted_id = $_POST['app_id'];
    $decrypted_id = URLSecurity::decryptId($encrypted_id);

    if ($decrypted_id > 0) {
        $specific_app_id = $decrypted_id;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Management</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }

        .empty-state i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .empty-state h4 {
            color: #495057;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #6c757d;
            max-width: 500px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .actions-cell {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                white-space: nowrap;
            }
        }

        .toast-success,
        .toast-error {
            color: white !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            padding: 12px 16px;
            position: fixed !important;
            top: 20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            z-index: 9999;
            text-align: center;
            min-width: 250px;
            max-width: 90%;
            margin: 0 auto;
            transition: top 0.5s ease, opacity 0.5s ease;
        }

        .toast-success {
            background-color: #388e3c !important;
        }

        .toast-error {
            background-color: #d32f2f !important;
        }

        .shop-logo-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .shop-name-cell {
            display: flex;
            align-items: center;
        }
    </style>
</head>

<body>
    <?php include 'include/offline-handler.php'; ?>

    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php' ?>

        <div class="content flex-grow-1">
            <?php include 'include/navbar.php'; ?>

            <div class="container-fluid p-4">
                <h1>Application Management</h1>
                
                <div class="mb-3 d-block d-md-flex justify-content-md-between align-items-md-center">
                    <div class="overflow-auto">
                        <ul class="nav nav-tabs flex-nowrap">
                            <li class="nav-item">
                                <a class="nav-link <?= $status_filter == 'all' ? 'active' : '' ?>" href="?status=all&date_start=<?= $date_start ?>&date_end=<?= $date_end ?>">All</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status_filter == 'Approved' ? 'active' : '' ?>" href="?status=Approved&date_start=<?= $date_start ?>&date_end=<?= $date_end ?>">Approved</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status_filter == 'Pending' ? 'active' : '' ?>" href="?status=Pending&date_start=<?= $date_start ?>&date_end=<?= $date_end ?>">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status_filter == 'Rejected' ? 'active' : '' ?>" href="?status=Rejected&date_start=<?= $date_start ?>&date_end=<?= $date_end ?>">Rejected</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-2 mt-md-0 d-grid d-md-block">
                        <a href="?export=csv&status=<?= $status_filter ?>&date_start=<?= $date_start ?>&date_end=<?= $date_end ?>" class="btn btn-success"><i class="fas fa-file-export"></i> Export</a>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <form id="dateFilterForm" method="get" class="row g-3">
                            <input type="hidden" name="status" value="<?= $status_filter ?>">
                            <div class="col-md-5">
                                <label for="date_start" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_start" name="date_start" value="<?= $date_start ?>">
                            </div>
                            <div class="col-md-5">
                                <label for="date_end" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_end" name="date_end" value="<?= $date_end ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="?status=<?= $status_filter ?>" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            $query = "SELECT sa.*, u.fullname 
                                      FROM shop_applications sa
                                      LEFT JOIN users u ON sa.user_id = u.id";
                            $where = [];

                            if ($status_filter != 'all') {
                                $where[] = "sa.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
                            }

                            if (!empty($date_start)) {
                                $where[] = "sa.applied_at >= '" . mysqli_real_escape_string($conn, $date_start) . " 00:00:00'";
                            }

                            if (!empty($date_end)) {
                                $where[] = "sa.applied_at <= '" . mysqli_real_escape_string($conn, $date_end) . " 23:59:59'";
                            }

                            if (!empty($where)) {
                                $query .= " WHERE " . implode(' AND ', $where);
                            }

                            $query .= " ORDER BY sa.id ASC";
                            $result = mysqli_query($conn, $query);
                            $row_count = mysqli_num_rows($result);

                            if ($row_count > 0) {
                            ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Shop Name</th>
                                            <th>Owner</th>
                                            <th>Submitted By</th>
                                            <th>Email</th>
                                            <th>App Status</th>
                                            <th>Shop Status</th>
                                            <th>Applied At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            if (empty($row['status'])) {
                                                $row['status'] = 'Pending';
                                            }

                                            $statusClass = '';
                                            $statusText = $row['status'];

                                            switch ($row['status']) {
                                                case 'Pending':
                                                    $statusClass = 'warning';
                                                    break;
                                                case 'Approved':
                                                    $statusClass = 'success';
                                                    break;
                                                case 'Rejected':
                                                    $statusClass = 'danger';
                                                    break;
                                            }

                                            $shopStatus = $row['shop_status'] ?? 'N/A';
                                            $shopStatusClass = 'secondary';
                                            $shopStatusText = 'N/A';

                                            switch ($shopStatus) {
                                                case 'open':
                                                    $shopStatusClass = 'success';
                                                    $shopStatusText = 'Open';
                                                    break;
                                                case 'temporarily_closed':
                                                    $shopStatusClass = 'warning';
                                                    $shopStatusText = 'Temp. Closed';
                                                    break;
                                                case 'permanently_closed':
                                                    $shopStatusClass = 'danger';
                                                    $shopStatusText = 'Closed';
                                                    break;
                                                default:
                                                    $shopStatusText = htmlspecialchars($shopStatus);
                                                    if ($shopStatus === 'N/A') $shopStatusText = 'N/A';
                                                    break;
                                            }

                                            $applied_at = date('M d, Y h:i A', strtotime($row['applied_at']));
                                            $shop_location_attr = htmlspecialchars($row['shop_location'] ?? '', ENT_QUOTES);

                                            $logoSrc = '';
                                            $defaultImg = '../account/uploads/shop_logo/logo.jpg';
                                            $shopLogo = $row['shop_logo'] ?? '';
                                            
                                            if (!empty($shopLogo) && trim($shopLogo) !== '') {
                                                $logoSrc = '../account/uploads/shop_logo/' . trim($shopLogo);
                                            } else {
                                                $logoSrc = $defaultImg;
                                            }

                                            echo "<tr data-id='{$row['id']}' data-location='{$shop_location_attr}'>
                                            <td>
                                                <div class='shop-name-cell'>
                                                    <img src='{$logoSrc}' 
                                                         alt='Logo' 
                                                         class='shop-logo-img'
                                                         onerror=\"this.onerror=null; this.src='{$defaultImg}';\">
                                                    <span>{$row['shop_name']}</span>
                                                </div>
                                            </td>
                                            <td>{$row['owner_name']}</td>
                                            <td>" . htmlspecialchars($row['fullname'] ?? 'N/A') . "</td>
                                            <td>{$row['email']}</td>
                                            <td><span class='badge bg-{$statusClass}'>{$statusText}</span></td>
                                            <td><span class='badge bg-{$shopStatusClass}'>{$shopStatusText}</span></td>
                                            <td>{$applied_at}</td>
                                            <td class='actions-cell'>
                                                <button class='btn btn-primary btn-sm view-btn' 
                                                        data-id='{$row['id']}' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#viewModal'>
                                                    <i class='fas fa-eye'></i>
                                                </button>";

                                            if ($row['status'] === 'Pending') {
                                                echo "<button class='btn btn-success btn-sm ms-1' onclick='updateStatus({$row["id"]}, \"Approved\")'><i class='fas fa-check'></i></button>
                                                      <button class='btn btn-danger btn-sm ms-1' onclick='initiateRejection({$row["id"]})'><i class='fas fa-times'></i></button>";
                                            }
                                            echo "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            <?php
                            } else {
                                $emptyMessage = "";
                                $emptyIcon = "fas fa-inbox";

                                switch ($status_filter) {
                                    case 'all':
                                        $emptyMessage = "No applications found for the selected date range.";
                                        break;
                                    case 'Approved':
                                        $emptyMessage = "No approved applications found for the selected date range.";
                                        $emptyIcon = "fas fa-check-circle";
                                        break;
                                    case 'Pending':
                                        $emptyMessage = "No pending applications found for the selected date range.";
                                        $emptyIcon = "fas fa-clock";
                                        break;
                                    case 'Rejected':
                                        $emptyMessage = "No rejected applications found for the selected date range.";
                                        $emptyIcon = "fas fa-times-circle";
                                        break;
                                }
                            ?>
                                <div class="empty-state">
                                    <i class="<?php echo $emptyIcon; ?>"></i>
                                    <h4>No Applications Found</h4>
                                    <p><?php echo $emptyMessage; ?></p>
                                    <?php if (!empty($date_start) || !empty($date_end)) : ?>
                                        <a href="?status=<?= $status_filter ?>" class="btn btn-primary mt-3">Clear Date Filters</a>
                                    <?php endif; ?>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'include/footer.php'; ?>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center rounded-3 shadow border-0">
                <div class="modal-body p-4 pt-5 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 me-3 mt-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="fw-bold mb-3" id="confirmationModalLabel">Confirm Action</h5>
                    <p class="mb-4" id="confirmationMessage">Are you sure you want to proceed with this action?</p>

                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill w-100" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary rounded-pill w-100" id="confirmActionButton">Yes, Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="rejectionModalLabel">Reject Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this application. This will be sent to the user via email.</p>
                    <form id="rejectionForm">
                        <input type="hidden" id="reject_app_id">
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_reason" rows="4" placeholder="e.g., Incomplete requirements, blurred documents..." required></textarea>
                            <div class="invalid-feedback">
                                Please provide a reason.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmRejection()">Confirm Rejection</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="feedbackToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-info-circle rounded me-2"></i>
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
            </div>
        </div>
    </div>

    <div id="customToast" style="display: none;">
        <span id="customToastMessage"></span>
    </div>

    <?php include 'include/back-to-top.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script 
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"
    async defer></script>
    <script src="js/script.js"></script>
    <script src="js/application.js"></script>

    <script>
        $(document).ready(function() {

            $('.view-btn').on('click', function() {
                var appId = $(this).data('id');
                var modalBody = $('#modal-body-content');

                $.ajax({
                    url: 'backend/get_application_details.php',
                    type: 'GET',
                    data: {
                        id: appId
                    },
                    beforeSend: function() {
                        modalBody.html('<div class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                    },
                    success: function(response) {

                        modalBody.html(response);
                    },
                    error: function() {
                        modalBody.html('<div class="alert alert-danger">Failed to load application details. Please try again.</div>');
                    }
                });
            });

            const specificIdToShow = <?php echo json_encode($specific_app_id); ?>;
            if (specificIdToShow > 0) {
                const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
                $.ajax({
                    url: 'backend/get_application_details.php',
                    type: 'GET',
                    data: {
                        id: specificIdToShow
                    },
                    beforeSend: function() {
                        $('#modal-body-content').html('<div class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                    },
                    success: function(response) {
                        $('#modal-body-content').html(response);
                    },
                    error: function() {
                        $('#modal-body-content').html('<div class="alert alert-danger">Failed to load application details.</div>');
                    }
                });
                viewModal.show();
            }
        });
    </script>
</body>

</html>