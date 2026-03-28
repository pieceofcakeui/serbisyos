<?php
include 'functions/verify.php';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/verify-email.css">
    <style>
        body, 
        body *:not(i):not(.fas):not(.fa):not([class^="icon-"]):not([class*=" icon-"]) {
            font-family: 'Montserrat', sans-serif !important;
        }

    </style>

</head>

<body>
     <?php include 'offline-handler.php'; ?>
     
    <div class="container">
        <h1>Email Verification</h1>
        <p id="message" class="<?php echo $status; ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php if ($status === 'success'): ?>
            <p>You can now receive updates from Serbisyos.</p>
        <?php endif; ?>
    </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>

</html>