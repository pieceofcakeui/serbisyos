<?php
require_once '../functions/auth.php';
include 'backend/base-path.php';
include 'backend/db_connection.php';

$user_id = $_SESSION['user_id'];

$shopQuery = $conn->prepare("SELECT id, shop_name FROM shop_applications WHERE user_id = ? AND status = 'Approved'");
$shopQuery->bind_param("i", $user_id);
$shopQuery->execute();
$shopResult = $shopQuery->get_result();
$shopData = $shopResult->fetch_assoc();

if (!$shopData) {

    include 'include/navbar.php';
    echo "<div class='container text-center py-5'>
            <h3 class='text-muted'>No active shop found for your account.</h3>
            <p>Please make sure your shop application has been approved.</p>
          </div>";
    exit();
}
$shop_id = $shopData['id'];
$shop_name = $shopData['shop_name'];

function get_change_indicator($current, $previous, $period_text)
{
    if ($previous > 0) {
        $change = (($current - $previous) / $previous) * 100;
        $icon_class = $change >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger';
        $prefix = $change >= 0 ? '+' : '';
        return "<small class='text-muted'><i class='fas $icon_class me-1'></i>{$prefix}" . number_format($change, 1) . "% vs. prev. $period_text</small>";
    } elseif ($current > 0) {
        return "<small class='text-muted'><i class='fas fa-star text-success me-1'></i> New activity</small>";
    } else {
        return "<small class='text-muted'>No change</small>";
    }
}


$daily_stmt = $conn->prepare("SELECT COUNT(id) AS count FROM shop_profile_visits WHERE shop_id = ? AND DATE(visit_timestamp) = CURDATE()");
$daily_stmt->bind_param("i", $shop_id);
$daily_stmt->execute();
$daily_visits = $daily_stmt->get_result()->fetch_assoc()['count'];

$weekly_current_stmt = $conn->prepare("SELECT COUNT(id) AS count FROM shop_profile_visits WHERE shop_id = ? AND visit_timestamp >= CURDATE() - INTERVAL 6 DAY");
$weekly_current_stmt->bind_param("i", $shop_id);
$weekly_current_stmt->execute();
$weekly_visits_current = $weekly_current_stmt->get_result()->fetch_assoc()['count'];

$weekly_previous_stmt = $conn->prepare("SELECT COUNT(id) AS count FROM shop_profile_visits WHERE shop_id = ? AND visit_timestamp BETWEEN CURDATE() - INTERVAL 13 DAY AND CURDATE() - INTERVAL 7 DAY");
$weekly_previous_stmt->bind_param("i", $shop_id);
$weekly_previous_stmt->execute();
$weekly_visits_previous = $weekly_previous_stmt->get_result()->fetch_assoc()['count'];
$weekly_change_html = get_change_indicator($weekly_visits_current, $weekly_visits_previous, '7 days');

$monthly_current_stmt = $conn->prepare("SELECT COUNT(id) AS count FROM shop_profile_visits WHERE shop_id = ? AND visit_timestamp >= CURDATE() - INTERVAL 29 DAY");
$monthly_current_stmt->bind_param("i", $shop_id);
$monthly_current_stmt->execute();
$monthly_visits_current = $monthly_current_stmt->get_result()->fetch_assoc()['count'];

$monthly_previous_stmt = $conn->prepare("SELECT COUNT(id) AS count FROM shop_profile_visits WHERE shop_id = ? AND visit_timestamp BETWEEN CURDATE() - INTERVAL 59 DAY AND CURDATE() - INTERVAL 30 DAY");
$monthly_previous_stmt->bind_param("i", $shop_id);
$monthly_previous_stmt->execute();
$monthly_visits_previous = $monthly_previous_stmt->get_result()->fetch_assoc()['count'];
$monthly_change_html = get_change_indicator($monthly_visits_current, $monthly_visits_previous, '30 days');

$saves_current_stmt = $conn->prepare("SELECT COUNT(id) AS count FROM save_shops WHERE shop_id = ? AND saved_at >= CURDATE() - INTERVAL 29 DAY");
$saves_current_stmt->bind_param("i", $shop_id);
$saves_current_stmt->execute();
$saves_current = $saves_current_stmt->get_result()->fetch_assoc()['count'];

$saves_previous_stmt = $conn->prepare("SELECT COUNT(id) AS count FROM save_shops WHERE shop_id = ? AND saved_at BETWEEN CURDATE() - INTERVAL 59 DAY AND CURDATE() - INTERVAL 30 DAY");
$saves_previous_stmt->bind_param("i", $shop_id);
$saves_previous_stmt->execute();
$saves_previous = $saves_previous_stmt->get_result()->fetch_assoc()['count'];
$saves_change_html = get_change_indicator($saves_current, $saves_previous, '30 days');

$visits_chart_labels = [];
$visits_chart_data = [];
$visits_by_date = [];

$visits_chart_query = $conn->prepare("
    SELECT DATE_FORMAT(visit_timestamp, '%Y-%m-%d') as visit_date, COUNT(id) as visit_count
    FROM shop_profile_visits
    WHERE shop_id = ? AND visit_timestamp >= CURDATE() - INTERVAL 6 DAY
    GROUP BY visit_date ORDER BY visit_date ASC
");
$visits_chart_query->bind_param("i", $shop_id);
$visits_chart_query->execute();
$visits_chart_result = $visits_chart_query->get_result();
while ($row = $visits_chart_result->fetch_assoc()) {
    $visits_by_date[$row['visit_date']] = $row['visit_count'];
}
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $visits_chart_labels[] = date('M d', strtotime($date));
    $visits_chart_data[] = $visits_by_date[$date] ?? 0;
}

$saves_chart_labels = [];
$saves_chart_data = [];

for ($i = 3; $i >= 0; $i--) {
    $start_of_week = date('Y-m-d', strtotime("-$i week last monday"));
    $end_of_week = date('Y-m-d', strtotime("-$i week next sunday"));
    $saves_chart_labels[] = 'Week of ' . date('M d', strtotime($start_of_week));

    $end_of_week_full = $end_of_week . ' 23:59:59';
    $saves_chart_query = $conn->prepare("SELECT COUNT(id) as save_count FROM save_shops WHERE shop_id = ? AND saved_at BETWEEN ? AND ?");
    $saves_chart_query->bind_param("iss", $shop_id, $start_of_week, $end_of_week_full);
    $saves_chart_query->execute();
    $result = $saves_chart_query->get_result()->fetch_assoc();
    $saves_chart_data[] = $result['save_count'] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Insights - <?php echo htmlspecialchars($shop_name); ?></title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/navbar.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content { padding-left: 1rem; padding-right: 1rem; }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            border: 1px solid #e9ecef;
        }
        .card:hover { transform: translateY(-5px); }
        .card-icon { font-size: 2.5rem; opacity: 0.7; }
        .card-title { font-weight: 500; color: #6c757d; }
        .card-text { color: #212529; }
        .chart-container { position: relative; height: 350px; }
    </style>
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="container py-4">
            <h1 class="mb-4 h3">Profile Insights for "<?php echo htmlspecialchars($shop_name); ?>"</h1>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Today's Visits</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="card-text fs-2 fw-bold mb-0"><?php echo $daily_visits; ?></p>
                                <i class="fas fa-calendar-day card-icon text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Last 7 Days Visits</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="card-text fs-2 fw-bold mb-0"><?php echo $weekly_visits_current; ?></p>
                                <i class="fas fa-chart-line card-icon text-primary"></i>
                            </div>
                            <?php echo $weekly_change_html; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Last 30 Days Visits</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="card-text fs-2 fw-bold mb-0"><?php echo $monthly_visits_current; ?></p>
                                <i class="fas fa-calendar-alt card-icon text-warning"></i>
                            </div>
                             <?php echo $monthly_change_html; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">New Saves (30 Days)</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="card-text fs-2 fw-bold mb-0"><?php echo $saves_current; ?></p>
                                <i class="fas fa-bookmark card-icon text-danger"></i>
                            </div>
                            <?php echo $saves_change_html; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-3">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Last 7 Days Profile Visits</h5>
                            <div class="chart-container">
                                <canvas id="visitsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">New Saves (Last 4 Weeks)</h5>
                            <div class="chart-container">
                                <canvas id="savesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include 'include/emergency-modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/navbar.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const visitsCtx = document.getElementById('visitsChart').getContext('2d');
            const visitsChartLabels = <?php echo json_encode($visits_chart_labels); ?>;
            const visitsChartData = <?php echo json_encode($visits_chart_data); ?>;
            new Chart(visitsCtx, {
                type: 'line',
                data: {
                    labels: visitsChartLabels,
                    datasets: [{
                        label: 'Profile Visits',
                        data: visitsChartData,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                    plugins: { legend: { display: false } }
                }
            });

            const savesCtx = document.getElementById('savesChart').getContext('2d');
            const savesChartLabels = <?php echo json_encode($saves_chart_labels); ?>;
            const savesChartData = <?php echo json_encode($saves_chart_data); ?>;
            new Chart(savesCtx, {
                type: 'bar',
                data: {
                    labels: savesChartLabels,
                    datasets: [{
                        label: 'New Saves',
                        data: savesChartData,
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
</body>
</html>