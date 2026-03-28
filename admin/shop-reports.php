<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

include 'backend/auth.php';
$REQUIRED_ROLE = 'admin'; 

include 'backend/db_connection.php';
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;

if (!isset($conn) || $conn->connect_error) {
  error_log("Database connection failed in shop-reports.php");
}

$brevo_api_key = $_ENV['BREVO_API_KEY'] ?? '';
$no_reply_email = $_ENV['NO_REPLY_EMAIL'] ?? 'noreply@serbisyos.com';
$support_email = $_ENV['SUPPORT_EMAIL'] ?? 'support@serbisyos.com';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respond'])) {
    $report_id = filter_input(INPUT_POST, 'report_id', FILTER_VALIDATE_INT);
    $response = filter_input(INPUT_POST, 'response', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $status = 'resolved';

    if (!$report_id || !$response) {
        $_SESSION['error_message'] = "Missing report ID or response.";
        header("Location: shop-reports.php");
        exit();
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE reports SET response = ?, status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $response, $status, $report_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Database update failed: " . $stmt->error);
        }
        $stmt->close();

        $details_stmt = $conn->prepare("
            SELECT 
                u.fullname AS reporter_name,
                u.email AS reporter_email,
                s.shop_name,
                r.reason,
                r.description,
                r.created_at
            FROM reports r
            JOIN users u ON r.user_id = u.id
            JOIN shop_applications s ON r.shop_id = s.id
            WHERE r.id = ?
        ");
        $details_stmt->bind_param("i", $report_id);
        $details_stmt->execute();
        $details_result = $details_stmt->get_result();
        $report_details = $details_result->fetch_assoc();
        $details_stmt->close();
        
        if (!$report_details) {
            throw new Exception("Could not find report details for email.");
        }

        if (!empty($brevo_api_key)) {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $brevo_api_key);
            $apiInstance = new TransactionalEmailsApi(new GuzzleClient(), $config);

            $sendSmtpEmail = new SendSmtpEmail([
                'to' => [['email' => $report_details['reporter_email'], 'name' => $report_details['reporter_name']]],
                'templateId' => 26,
                'params' => [
                    'REPORTER_NAME' => $report_details['reporter_name'],
                    'SHOP_NAME' => $report_details['shop_name'],
                    'REPORT_REASON' => $report_details['reason'],
                    'USER_DESCRIPTION' => $report_details['description'],
                    'DATE_SUBMITTED' => date('F j, Y \a\t g:i A', strtotime($report_details['created_at'])),
                    'ADMIN_RESPONSE' => $response,
                    'SUPPORT_EMAIL' => $support_email,
                    'LOGO_URL' => '/assets/img/logo.png',
                ],
                'subject' => "Your Report Has Been Resolved - Serbisyos",
                'sender' => ['name' => 'Serbisyos Team', 'email' => $no_reply_email],
                'replyTo' => ['name' => 'Serbisyos Support', 'email' => $support_email]
            ]);

            $apiInstance->sendTransacEmail($sendSmtpEmail);
        }

        $conn->commit();
        $_SESSION['success_message'] = "Report resolved and customer has been notified.";
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Report Resolution Error: " . $e->getMessage());
        $_SESSION['error_message'] = "Failed to process report: " . $e->getMessage();
    }
    
    header("Location: shop-reports.php");
    exit();
}

$status = $_GET['status'] ?? 'all';
$order = $_GET['order'] ?? 'desc';
$order = in_array(strtolower($order), ['asc', 'desc']) ? strtolower($order) : 'desc';

$allowed_statuses_filter = ['all', 'pending', 'resolved', 'dismissed'];
$status_filter = in_array($status, $allowed_statuses_filter) ? $status : 'all';

$sql = "SELECT r.*, u.fullname, s.shop_name FROM reports r 
        JOIN users u ON r.user_id = u.id 
        LEFT JOIN shop_applications s ON r.shop_id = s.id";

if ($status_filter !== 'all') {
    $sql .= " WHERE r.status = ?";
}
$sql .= " ORDER BY r.created_at $order";

$stmt = $conn->prepare($sql);

if ($status_filter !== 'all') {
    $stmt->bind_param("s", $status_filter);
}

$stmt->execute();
$result = $stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Reports</title>
       <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .nav-tabs .nav-link.active {
            font-weight: bold;
        }
        .status-badge {
            min-width: 80px;
            display: inline-block;
            text-align: center;
        }
        .action-disabled {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <?php include 'include/offline-handler.php'; ?>
    
    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>
        
        <div class="content flex-grow-1">
            <?php include 'include/navbar.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-start align-items-md-center mb-4">
                    <h1 class="mb-3 mb-md-0">Shop Reports</h1>
                    
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center w-100 w-md-auto">
                        <span class="me-sm-2 mb-2 mb-sm-0">Sort:</span>
                        <div class="btn-group">
                            <a href="?status=<?= $status ?>&order=desc" class="btn btn-sm <?= $order === 'desc' ? 'btn-primary' : 'btn-outline-primary' ?>">Newest First</a>
                            <a href="?status=<?= $status ?>&order=asc" class="btn btn-sm <?= $order === 'asc' ? 'btn-primary' : 'btn-outline-primary' ?>">Oldest First</a>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?= $status === 'all' ? 'active' : '' ?>" href="?status=all&order=<?= $order ?>">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>" href="?status=pending&order=<?= $order ?>">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status === 'resolved' ? 'active' : '' ?>" href="?status=resolved&order=<?= $order ?>">Resolved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status === 'dismissed' ? 'active' : '' ?>" href="?status=dismissed&order=<?= $order ?>">Dismissed</a>
                    </li>
                </ul>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportsTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Shop</th>
                                        <th>Reported By</th>
                                        <th>Reason</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Response</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports as $index => $report): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($report['shop_name']) ?></td>
                                            <td><?= htmlspecialchars($report['fullname']) ?></td>
                                            <td><?= htmlspecialchars($report['reason']) ?></td>
                                            <td><?= htmlspecialchars($report['description']) ?></td>
                                            <td>
                                                <span class="badge status-badge <?= 
                                                    $report['status'] === 'pending' ? 'bg-warning' :
                                                    ($report['status'] === 'resolved' ? 'bg-success' :
                                                    ($report['status'] === 'dismissed' ? 'bg-secondary' : 'bg-light text-dark'))
                                                ?>">
                                                    <?= ucfirst($report['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y h:i A', strtotime($report['created_at'])) ?></td>
                                            <td><?= $report['response'] ? htmlspecialchars($report['response']) : 'No response yet' ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary <?= $report['status'] === 'resolved' ? 'action-disabled' : '' ?>" data-bs-toggle="modal" data-bs-target="#responseModal<?= $report['id'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="responseModal<?= $report['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Manage Report #<?= $index + 1 ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST" id="responseForm<?= $report['id'] ?>">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                                            <input type="hidden" name="status" value="resolved">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Response</label>
                                                                <textarea class="form-control" name="response" rows="4" required><?= htmlspecialchars($report['response'] ?? '') ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" name="respond" class="btn btn-primary btn-submit">
                                                                Submit
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'include/footer.php'; ?>
        </div>
    </div>

    <?php include 'include/back-to-top.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

      <script src="js/script.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#reportsTable').DataTable({
                order: [], 
                responsive: true
            });

            $('form[id^="responseForm"]').on('submit', function(e) {
                var submitBtn = $(this).find('.btn-submit');
                var originalText = submitBtn.text();
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...');
                submitBtn.prop('disabled', true);
            });
            
            // Display session messages if they exist (assuming a toast system)
            <?php if (isset($_SESSION['success_message'])): ?>
                // Logic to show success toast or alert
                console.log('Success: <?= addslashes($_SESSION['success_message']) ?>');
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                // Logic to show error toast or alert
                console.error('Error: <?= addslashes($_SESSION['error_message']) ?>');
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
