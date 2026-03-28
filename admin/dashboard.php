<?php
session_start();
include 'backend/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
   <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

    <link rel="stylesheet" href="css/styles.css">

</head>

<body>
    <?php include 'include/offline-handler.php'; ?>
    
    <div class="d-flex">

        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>

        <div class="content flex-grow-1">
            <?php include 'include/navbar.php'; ?>

            <?php include 'backend/dashboard.php'; ?>

            <div class="container-fluid p-4">
                <h1 class="mb-4">Dashboard</h1>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Applications</h5>
                                <p class="card-text h2"><?php echo $totalApplications; ?></p>
                                <i
                                    class="fas fa-file-alt text-muted position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h5 class="card-title">Approved</h5>
                                <p class="card-text h2"><?php echo $approvedApplications; ?></p>
                                <i
                                    class="fas fa-check text-muted position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h5 class="card-title">Pending</h5>
                                <p class="card-text h2"><?php echo $pendingApplications; ?></p>
                                <i
                                    class="fas fa-clock text-muted position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Application Trends (Last 6 Months)</h5>
                                <div class="chart-container">
                                    <canvas id="applicationsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Application Status</h5>
                                <div class="chart-container">
                                    <canvas id="applicationStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
              <?php include 'include/footer.php'; ?>
        </div>
    </div>

    <?php include 'include/back-to-top.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('mark-all-read-dropdown');
    const notificationBadge = document.getElementById('notification-badge');
    const notificationItems = document.querySelectorAll('#notification-menu .notification-item');

    if(markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('backend/mark_notifications_read.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=mark_all'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (notificationBadge) {
                        notificationBadge.remove();
                    }
                    notificationItems.forEach(item => {
                        item.classList.remove('notification-unread');
                    });
                }
            });
        });
    }

    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            if (this.classList.contains('notification-unread')) {
                const type = this.dataset.type;
                const id = this.dataset.id;
                
                fetch('backend/mark_notifications_read.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `type=${type}&id=${id}`
                });
            }
        });
    });
});
</script>
    <script src="js/script.js"></script>
    <script>
        const dashboardData = {
            chartData: <?php echo $chartData; ?>,
            approvedApplications: <?php echo $approvedApplications; ?>,
            pendingApplications: <?php echo $pendingApplications; ?>,
            totalApplications: <?php echo $totalApplications; ?>,
        };
    </script>
    <script src="js/dashboard.js"></script>


    <script>
        $(document).ready(function () {
            $('#exportBtn').on('click', function () {
                window.location.href = 'backend/export.php';
            });
        });
    </script>

</body>

</html>