<?php
include 'backend/auth.php';
require 'backend/db_connection.php';
include 'backend/analytics.php';

$reportsQuery = "SELECT reason, COUNT(*) as count FROM reports GROUP BY reason";
$reportsResult = mysqli_query($conn, $reportsQuery);
$reportReasons = [];
$reportCounts = [];
$totalReports = 0;
while ($row = mysqli_fetch_assoc($reportsResult)) {
    $reportReasons[] = $row['reason'];
    $reportCounts[] = $row['count'];
    $totalReports += $row['count'];
}

$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');
$thisWeekStart = date('Y-m-d', strtotime('monday this week'));
$thisMonthStart = date('Y-m-01');
$thisYearStart = date('Y-01-01');

$activeTodayQuery = "SELECT user_id, MAX(login_time) as latest_login FROM active_sessions 
                     WHERE is_current = 1 AND DATE(login_time) = '$today' 
                     GROUP BY user_id";
$activeTodayResult = mysqli_query($conn, $activeTodayQuery);
$activeToday = mysqli_num_rows($activeTodayResult);

$todayLoginsQuery = "SELECT COUNT(DISTINCT user_id) as count FROM active_sessions 
                     WHERE DATE(login_time) = '$today'";
$todayResult = mysqli_query($conn, $todayLoginsQuery);
$todayLogins = mysqli_fetch_assoc($todayResult)['count'];

$weekLoginsQuery = "SELECT COUNT(DISTINCT user_id) as count FROM active_sessions 
                    WHERE DATE(login_time) >= '$thisWeekStart'";
$weekResult = mysqli_query($conn, $weekLoginsQuery);
$weekLogins = mysqli_fetch_assoc($weekResult)['count'];

$monthLoginsQuery = "SELECT COUNT(DISTINCT user_id) as count FROM active_sessions 
                     WHERE DATE(login_time) >= '$thisMonthStart'";
$monthResult = mysqli_query($conn, $monthLoginsQuery);
$monthLogins = mysqli_fetch_assoc($monthResult)['count'];

$yearLoginsQuery = "SELECT COUNT(DISTINCT user_id) as count FROM active_sessions 
                    WHERE DATE(login_time) >= '$thisYearStart'";
$yearResult = mysqli_query($conn, $yearLoginsQuery);
$yearLogins = mysqli_fetch_assoc($yearResult)['count'];

$allTimeLoginsQuery = "SELECT COUNT(DISTINCT user_id) as count FROM active_sessions";
$allTimeResult = mysqli_query($conn, $allTimeLoginsQuery);
$allTimeLogins = mysqli_fetch_assoc($allTimeResult)['count'];

$dailyLoginData = [];
$dailyLoginLabels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dayQuery = "SELECT user_id, MAX(login_time) as latest_login FROM active_sessions 
                 WHERE is_current = 1 AND DATE(login_time) = '$date' 
                 GROUP BY user_id";
    $dayResult = mysqli_query($conn, $dayQuery);
    $dayCount = mysqli_num_rows($dayResult);
    
    $dailyLoginData[] = $dayCount;
    $dailyLoginLabels[] = date('M j', strtotime($date));
}

$weeklyLoginData = [];
$weeklyLoginLabels = [];
for ($i = 3; $i >= 0; $i--) {
    $weekStart = date('Y-m-d', strtotime("-$i weeks monday"));
    $weekEnd = date('Y-m-d', strtotime("-$i weeks sunday"));
    $weekQuery = "SELECT COUNT(DISTINCT user_id) as count FROM active_sessions 
                  WHERE DATE(login_time) BETWEEN '$weekStart' AND '$weekEnd'";
    $weekResult = mysqli_query($conn, $weekQuery);
    $weekCount = mysqli_fetch_assoc($weekResult)['count'];
    
    $weeklyLoginData[] = $weekCount;
    $weeklyLoginLabels[] = 'Week ' . date('M j', strtotime($weekStart));
}

$monthlyLoginData = [];
$monthlyLoginLabels = [];
for ($i = 5; $i >= 0; $i--) {
    $monthStart = date('Y-m-01', strtotime("-$i months"));
    $monthEnd = date('Y-m-t', strtotime("-$i months"));
    $monthQuery = "SELECT COUNT(DISTINCT user_id) as count FROM active_sessions 
                   WHERE DATE(login_time) BETWEEN '$monthStart' AND '$monthEnd'";
    $monthResult = mysqli_query($conn, $monthQuery);
    $monthCount = mysqli_fetch_assoc($monthResult)['count'];
    
    $monthlyLoginData[] = $monthCount;
    $monthlyLoginLabels[] = date('M Y', strtotime($monthStart));
}

$yearlyLoginData = [];
$yearlyLoginLabels = [];
for ($i = 2; $i >= 0; $i--) {
    $year = date('Y') - $i;
    $yearStart = "$year-01-01";
    $yearEnd = "$year-12-31";
    $yearQuery = "SELECT COUNT(DISTINCT user_id) as count FROM active_sessions 
                  WHERE DATE(login_time) BETWEEN '$yearStart' AND '$yearEnd'";
    $yearResult = mysqli_query($conn, $yearQuery);
    $yearCount = mysqli_fetch_assoc($yearResult)['count'];
    
    $yearlyLoginData[] = $yearCount;
    $yearlyLoginLabels[] = $year;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
       <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
     <link rel="stylesheet" href="css/analytics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <?php include 'include/offline-handler.php'; ?>
    
    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>

        <div class="content flex-grow-1">
        <?php include 'include/navbar.php'; ?>

            <div class="container-fluid p-4">
                <h1 class="mb-4">Analytics Dashboard</h1>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <div class="col">
                        <div class="card text-white bg-info h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h3><?php echo $totalUsers; ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Owners</h5>
                                <h3><?php echo $totalOwners; ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Regular Users</h5>
                                <h3><?php echo $totalRegularUsers; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">User Distribution</h5>
                        <div class="chart-container">
                            <canvas id="userChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-4">
                    <div class="col">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Shops</h5>
                                <h3><?php echo $totalShops; ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Approved Shops</h5>
                                <h3><?php echo $approvedShops; ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-warning h-100">
                            <div class="card-body">
                                <h5 class="card-title">Pending Shops</h5>
                                <h3><?php echo $pendingShops; ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-danger h-100">
                            <div class="card-body">
                                <h5 class="card-title">Rejected Shops</h5>
                                <h3><?php echo $rejectedShops; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Shop Applications Overview</h5>
                        <div class="chart-container">
                            <canvas id="shopChart"></canvas>
                        </div>
                    </div>
                </div>

                <h2 class="mt-5 mb-3">Login Analytics</h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-4">
                    <div class="col">
                        <div class="card text-white bg-success h-100 login-stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h5 class="card-title">Active Today</h5>
                                <h3><?php echo $activeToday; ?></h3>
                                <small>Currently Active</small>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-info h-100 login-stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-day fa-2x mb-2"></i>
                                <h5 class="card-title">Today</h5>
                                <h3><?php echo $todayLogins; ?></h3>
                                <small>Logins</small>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-info h-100 login-stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-week fa-2x mb-2"></i>
                                <h5 class="card-title">This Week</h5>
                                <h3><?php echo $weekLogins; ?></h3>
                                <small>Logins</small>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-warning h-100 login-stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                <h5 class="card-title">This Month</h5>
                                <h3><?php echo $monthLogins; ?></h3>
                                <small>Logins</small>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card text-white bg-danger h-100 login-stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar fa-2x mb-2"></i>
                                <h5 class="card-title">This Year</h5>
                                <h3><?php echo $yearLogins; ?></h3>
                                <small>Logins</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Login Trends</h5>

                        <ul class="nav nav-pills chart-tabs mb-3" id="loginTrendsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="daily-tab" data-bs-toggle="pill" data-bs-target="#daily" type="button" role="tab">
                                    Daily (7 days)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="weekly-tab" data-bs-toggle="pill" data-bs-target="#weekly" type="button" role="tab">
                                    Weekly (4 weeks)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="monthly-tab" data-bs-toggle="pill" data-bs-target="#monthly" type="button" role="tab">
                                    Monthly (6 months)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="yearly-tab" data-bs-toggle="pill" data-bs-target="#yearly" type="button" role="tab">
                                    Yearly (3 years)
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="loginTrendsContent">
                            <div class="tab-pane fade show active" id="daily" role="tabpanel">
                                <div class="chart-container">
                                    <canvas id="dailyLoginChart"></canvas>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="weekly" role="tabpanel">
                                <div class="chart-container">
                                    <canvas id="weeklyLoginChart"></canvas>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="monthly" role="tabpanel">
                                <div class="chart-container">
                                    <canvas id="monthlyLoginChart"></canvas>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="yearly" role="tabpanel">
                                <div class="chart-container">
                                    <canvas id="yearlyLoginChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 g-4">
                    <div class="col-lg-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Shop Report Reasons
                                    <span class="float-end">Total: <?php echo $totalReports; ?></span>
                                </h5>
                                <div class="chart-container">
                                    <canvas id="reportChart"></canvas>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
    const dashboardData = {

        totalShops: <?php echo $totalShops; ?>,
        approvedShops: <?php echo $approvedShops; ?>,
        pendingShops: <?php echo $pendingShops; ?>,
        rejectedShops: <?php echo $rejectedShops; ?>,
        totalOwners: <?php echo $totalOwners; ?>,
        totalRegularUsers: <?php echo $totalRegularUsers; ?>,
        reportReasons: <?php echo json_encode($reportReasons); ?>,
        reportCounts: <?php echo json_encode($reportCounts); ?>,
        reportPercentages: <?php echo json_encode(array_map(function($count) use ($totalReports) {
            return $totalReports > 0 ? round(($count/$totalReports)*100, 1) : 0;
        }, $reportCounts)); ?>,

        activeToday: <?php echo $activeToday; ?>,
        todayLogins: <?php echo $todayLogins; ?>,
        weekLogins: <?php echo $weekLogins; ?>,
        monthLogins: <?php echo $monthLogins; ?>,
        yearLogins: <?php echo $yearLogins; ?>,
        allTimeLogins: <?php echo $allTimeLogins; ?>,

        dailyLoginData: <?php echo json_encode($dailyLoginData); ?>,
        dailyLoginLabels: <?php echo json_encode($dailyLoginLabels); ?>,
        weeklyLoginData: <?php echo json_encode($weeklyLoginData); ?>,
        weeklyLoginLabels: <?php echo json_encode($weeklyLoginLabels); ?>,
        monthlyLoginData: <?php echo json_encode($monthlyLoginData); ?>,
        monthlyLoginLabels: <?php echo json_encode($monthlyLoginLabels); ?>,
        yearlyLoginData: <?php echo json_encode($yearlyLoginData); ?>,
        yearlyLoginLabels: <?php echo json_encode($yearlyLoginLabels); ?>
    };

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    const userCtx = document.getElementById('userChart').getContext('2d');
    const userChart = new Chart(userCtx, {
        type: 'doughnut',
        data: {
            labels: ['Owners', 'Regular Users'],
            datasets: [{
                data: [dashboardData.totalOwners, dashboardData.totalRegularUsers],
                backgroundColor: ['#28a745', '#007bff']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    const shopCtx = document.getElementById('shopChart').getContext('2d');
    const shopChart = new Chart(shopCtx, {
        type: 'bar',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                label: 'Shops',
                data: [dashboardData.approvedShops, dashboardData.pendingShops, dashboardData.rejectedShops],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: chartOptions
    });

    const reportCtx = document.getElementById('reportChart').getContext('2d');
    const reportChart = new Chart(reportCtx, {
        type: 'doughnut',
        data: {
            labels: dashboardData.reportReasons,
            datasets: [{
                data: dashboardData.reportCounts,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const percentage = dashboardData.reportPercentages[context.dataIndex];
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    const dailyLoginCtx = document.getElementById('dailyLoginChart').getContext('2d');
    const dailyLoginChart = new Chart(dailyLoginCtx, {
        type: 'line',
        data: {
            labels: dashboardData.dailyLoginLabels,
            datasets: [{
                label: 'Daily Logins',
                data: dashboardData.dailyLoginData,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true
            }]
        },
        options: chartOptions
    });

    const weeklyLoginCtx = document.getElementById('weeklyLoginChart').getContext('2d');
    const weeklyLoginChart = new Chart(weeklyLoginCtx, {
        type: 'bar',
        data: {
            labels: dashboardData.weeklyLoginLabels,
            datasets: [{
                label: 'Weekly Logins',
                data: dashboardData.weeklyLoginData,
                backgroundColor: '#17a2b8'
            }]
        },
        options: chartOptions
    });

    const monthlyLoginCtx = document.getElementById('monthlyLoginChart').getContext('2d');
    const monthlyLoginChart = new Chart(monthlyLoginCtx, {
        type: 'line',
        data: {
            labels: dashboardData.monthlyLoginLabels,
            datasets: [{
                label: 'Monthly Logins',
                data: dashboardData.monthlyLoginData,
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                fill: true
            }]
        },
        options: chartOptions
    });

    const yearlyLoginCtx = document.getElementById('yearlyLoginChart').getContext('2d');
    const yearlyLoginChart = new Chart(yearlyLoginCtx, {
        type: 'bar',
        data: {
            labels: dashboardData.yearlyLoginLabels,
            datasets: [{
                label: 'Yearly Logins',
                data: dashboardData.yearlyLoginData,
                backgroundColor: '#dc3545'
            }]
        },
        options: chartOptions
    });
    </script>
    <script src="js/analytics.js"></script>
</body>
</html>