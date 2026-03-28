<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';


if (!isset($_SESSION['backup_codes'])) {
    header("Location: 2fa_setup.php");
    exit();
}

$backup_codes = $_SESSION['backup_codes'];
unset($_SESSION['backup_codes']);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Successfully Enabled</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/users/2fa_success.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, 
        body *:not(i):not(.fas):not(.fa):not([class^="icon-"]):not([class*=" icon-"]) {
            font-family: 'Montserrat', sans-serif !important;
        }
    </style>
    
</head>
<body>
    <?php include 'include/offline-handler.php'; ?>
    
  
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header text-white">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt icon-lg"></i>
                            <div>
                                <h2 class="h5 mb-0">Two-Factor Authentication Enabled</h2>
                                <p class="small mb-0 opacity-75">Your account security has been upgraded</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="alert alert-success mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 text-success"></i>
                                <div>
                                    <h3 class="h5 mb-1">Success!</h3>
                                    <p class="mb-0">Two-factor authentication is now active on your account.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle me-3 text-warning"></i>
                                <div>
                                    <h3 class="h5 mb-2">Save Your Backup Codes</h3>
                                    <p class="mb-2">These one-time use codes allow access to your account if you can't use your authenticator app.</p>
                                    <p class="mb-0 fw-bold">Store them securely - they won't be shown again.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="backup-code-card print-section">
                            <h4 class="h6 text-muted mb-3">YOUR BACKUP CODES</h4>
                            <div class="row">
                                <?php foreach (array_chunk($backup_codes, ceil(count($backup_codes)/2)) as $column): ?>
                                <div class="col-md-6">
                                    <?php foreach ($column as $code): ?>
                                    <div class="backup-code d-block mb-2"><?php echo htmlspecialchars($code); ?></div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="divider no-print"></div>
                        
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 no-print">
                            <div class="d-flex gap-2">
                                <button id="print-codes" class="btn btn-outline-secondary">
                                    <i class="fas fa-print me-2"></i>Print Codes
                                </button>
                                <button id="copy-codes" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Copy to clipboard">
                                    <i class="fas fa-copy me-2"></i>Copy All
                                </button>
                            </div>
                            <a href="settings-and-privacy.php" class="btn btn-success">
                                <i class="fas fa-cog me-2"></i>Continue to Settings
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 text-center text-muted small no-print">
                    <p>For security reasons, these codes will not be shown again. Please save them now.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            document.getElementById('print-codes').addEventListener('click', function() {
                window.print();
            });

            document.getElementById('copy-codes').addEventListener('click', function() {
                const codes = `<?php echo implode("\n", $backup_codes); ?>`;
                navigator.clipboard.writeText(codes).then(() => {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>