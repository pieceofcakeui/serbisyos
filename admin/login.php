<?php
session_start();

if (
    !isset($_SESSION['admin_access_allowed']) ||
    $_SESSION['admin_access_allowed'] !== true ||
    !isset($_SESSION['admin_access_time']) ||
    (time() - $_SESSION['admin_access_time']) > 20
) {
    header("Location: https://serbisyos.com/");
    exit;
}

unset($_SESSION['admin_access_allowed']);
unset($_SESSION['admin_access_time']);

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$toast_data = null;
if (isset($_SESSION['flash_message'])) {
    $toast_data = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

   <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 10px;
        }
        .toast-info {
    background-color: #17a2b8 !important;
    color: white !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    padding: 12px 16px;
    position: fixed !important;
    top: 20px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    z-index: 9999;
    opacity: 1 !important;
    text-align: center;
    min-width: 250px;
    max-width: 90%;
    margin: 0 auto;
    transition: opacity 0.3s ease;
}
    </style>
</head>

<body>
    <?php include 'include/offline-handler.php'; ?>

    <div class="adminlogin-container">
        <img src="../assets/img/logo/logo.webp" alt="adminlogo" class="adminlogo">
        <h2>Admin Login</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message" style="color:red; margin-bottom:10px;">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form method="POST" action="backend/function.php">
            <div class="adminform-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="adminform-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="toggle-password" id="toggle-password">
                    <i class="fa-regular fa-eye"></i>
                </span>
            </div>

            <button type="submit" name="admin_login" class="admin_btn">Login</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
    
    <script>
document.addEventListener('DOMContentLoaded', function() {

    <?php
    if (isset($toast_data) && $toast_data):
        $toast_type = $toast_data['type'];
        $toast_body = $toast_data['body'];
        $toast_title = $toast_data['title'];
    ?>

        var toastOptions = {
            "progressBar": true,
            "positionClass": "toast-top-center"
        };

        if ('<?php echo $toast_type; ?>' === 'info') {
            toastOptions.closeButton = false;
            toastOptions.timeOut = "3000";
        } else {
            toastOptions.closeButton = true;
            toastOptions.timeOut = "5000";
        }

        toastr.options = toastOptions;
        toastr['<?php echo $toast_type; ?>'](
            "<?php echo $toast_body; ?>",
            "<?php echo $toast_title; ?>"
        );

    <?php endif; ?>

});
</script>

</body>

</html>