<?php
include 'backend/auth.php';
include 'backend/db_connection.php';

$search = $_GET['search'] ?? '';
$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';

function getDeviceInfo($user_agent) {
    if (empty($user_agent)) {
        return [
            'browser' => 'Unknown',
            'browser_icon' => 'fas fa-globe',
            'os' => 'Unknown',
            'os_icon' => 'fas fa-laptop',
            'device_type' => 'Unknown',
            'display' => "<i class='fas fa-globe'></i> Unknown Browser <i class='fas fa-laptop'></i> Unknown Device"
        ];
    }

    $icons = [
        'browsers' => [
            'Chrome' => 'fab fa-chrome',
            'Firefox' => 'fab fa-firefox',
            'Safari' => 'fab fa-safari',
            'Edge' => 'fab fa-edge',
            'Opera' => 'fab fa-opera',
            'Internet Explorer' => 'fab fa-internet-explorer',
            'Brave' => 'fab fa-brave',
            'Default' => 'fas fa-globe'
        ],
        'os' => [
            'Windows' => 'fab fa-windows',
            'Mac' => 'fab fa-apple',
            'Linux' => 'fab fa-linux',
            'Android' => 'fab fa-android',
            'iPhone' => 'fab fa-apple',
            'iPad' => 'fab fa-apple',
            'Default' => 'fas fa-laptop'
        ]
    ];

    $browser = 'Browser';
    $browserIcon = $icons['browsers']['Default'];
    foreach ($icons['browsers'] as $b => $icon) {
        if ($b !== 'Default' && stripos($user_agent, $b) !== false) {
            $browser = $b;
            $browserIcon = $icon;
            break;
        }
    }

    $os = 'Device';
    $osIcon = $icons['os']['Default'];
    
    if (stripos($user_agent, 'iPhone') !== false) {
        $os = 'iPhone';
        $osIcon = 'fab fa-apple';
    } elseif (stripos($user_agent, 'iPad') !== false) {
        $os = 'iPad';
        $osIcon = 'fab fa-apple';
    } elseif (stripos($user_agent, 'Android') !== false) {
        $os = 'Android';
        $osIcon = 'fab fa-android';
    } else {
        foreach ($icons['os'] as $o => $icon) {
            if ($o !== 'Default' && stripos($user_agent, $o) !== false) {
                $os = $o;
                $osIcon = $icon;
                break;
            }
        }
    }

    $deviceType = (stripos($user_agent, 'Mobile') !== false || 
                  stripos($user_agent, 'iPhone') !== false || 
                  stripos($user_agent, 'Android') !== false) ? 'Mobile' : 'Desktop';

    return [
        'browser' => $browser,
        'browser_icon' => $browserIcon,
        'os' => $os,
        'os_icon' => $osIcon,
        'device_type' => $deviceType,
        'display' => "<i class='$browserIcon'></i> $browser <i class='$osIcon'></i> $os"
    ];
}

// Add location display function
function getLocationDisplay($location) {
    if (empty($location) || $location === 'Unknown') {
        return "<i class='fas fa-globe'></i> Unknown Location";
    }
    
    return "<i class='fas fa-map-marker-alt'></i> " . htmlspecialchars($location);
}

$query = "SELECT 
            al.id,
            al.user_id,
            COALESCE(u.profile_type, 'Unknown') as profile_type,
            COALESCE(u.fullname, 'Unknown User') AS name,
            COALESCE(u.email, 'Unknown Email') AS email,
            al.activity_type AS status,
            al.activity_time AS login_time,
            CASE 
                WHEN al.activity_type LIKE '%LOGIN SUCCESS%' OR al.activity_type LIKE '%GOOGLE LOGIN%' THEN
                    COALESCE(
                        (SELECT logout_time 
                         FROM active_sessions 
                         WHERE user_id = al.user_id 
                         AND ABS(TIMESTAMPDIFF(SECOND, login_time, al.activity_time)) <= 5
                         ORDER BY ABS(TIMESTAMPDIFF(SECOND, login_time, al.activity_time)) ASC
                         LIMIT 1), 
                        NULL
                    )
                WHEN al.activity_type LIKE '%LOGOUT%' THEN al.activity_time
                ELSE NULL
            END AS logout_time,
            al.ip_address,
            al.user_agent,
            al.device_info AS device,
            -- Add location data if available
            COALESCE(al.location, 'Unknown') as location
          FROM activity_log al
          LEFT JOIN users u ON al.user_id = u.id
          WHERE (al.activity_type LIKE '%LOGIN SUCCESS%' OR al.activity_type LIKE '%LOGIN FAILED%' OR al.activity_type LIKE '%LOGOUT%' OR al.activity_type LIKE '%ACCOUNT LOCKED%' OR al.activity_type LIKE '%GOOGLE LOGIN%' OR al.activity_type LIKE '%GOOGLE SIGNUP%')
          AND al.activity_type NOT LIKE '%LOGIN ATTEMPT%'";

$conditions = [];
$paramTypes = '';
$paramValues = [];

if (!empty($search)) {
    $conditions[] = "(u.email LIKE ? OR u.fullname LIKE ? OR al.ip_address LIKE ? OR al.location LIKE ?)";
    $searchParam = "%$search%";
    $paramTypes .= 'ssss';
    array_push($paramValues, $searchParam, $searchParam, $searchParam, $searchParam);
}

if (!empty($date_start)) {
    $conditions[] = "DATE(al.activity_time) >= ?";
    $paramTypes .= 's';
    array_push($paramValues, $date_start);
}

if (!empty($date_end)) {
    $conditions[] = "DATE(al.activity_time) <= ?";
    $paramTypes .= 's';
    array_push($paramValues, $date_end);
}

if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY al.activity_time DESC";

$stmt = $conn->prepare($query);

if (!empty($paramTypes)) {
    $stmt->bind_param($paramTypes, ...$paramValues);
}

$stmt->execute();
$result = $stmt->get_result();
$loginHistory = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login History</title>
       <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login-history.css">
    <style>
        #clearButton {
            border-left: none;
            display: <?= !empty($search) ? 'block': 'none' ?>;
        }
        .location-info {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .location-info i {
            margin-right: 5px;
            color: #dc3545;
        }
        /* Adjust table column widths */
        th:nth-child(1), td:nth-child(1) { width: 5%; }
        th:nth-child(2), td:nth-child(2) { width: 10%; }
        th:nth-child(3), td:nth-child(3) { width: 12%; }
        th:nth-child(4), td:nth-child(4) { width: 15%; }
        th:nth-child(5), td:nth-child(5) { width: 12%; }
        th:nth-child(6), td:nth-child(6) { width: 10%; }
        th:nth-child(7), td:nth-child(7) { width: 10%; }
        th:nth-child(8), td:nth-child(8) { width: 12%; }
        th:nth-child(9), td:nth-child(9) { width: 14%; }
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Login History</h1>
                    <div class="d-flex">
                        <div class="input-group me-3" style="width: 300px;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search history..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-danger" type="button" id="clearButton" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <a href="#" id="exportButton" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <form id="dateFilterForm" class="row g-3">
                            <div class="col-md-5">
                                <label for="date_start" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_start" name="date_start" value="<?= htmlspecialchars($date_start) ?>">
                            </div>
                            <div class="col-md-5">
                                <label for="date_end" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_end" name="date_end" value="<?= htmlspecialchars($date_end) ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" id="filterButton" class="btn btn-primary me-2">Filter</button>
                                <button type="button" id="resetButton" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="loginHistoryTable" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Profile Type</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Activity Status</th>
                                        <th>Login Time</th>
                                        <th>Logout Time</th>
                                        <th>IP Address</th>
                                        <th>Device</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($loginHistory)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No login history found</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($loginHistory as $index => $entry): 
                                        $deviceInfo = getDeviceInfo($entry['user_agent']);
                                        $locationDisplay = getLocationDisplay($entry['location'] ?? 'Unknown');
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= ucfirst($entry['profile_type']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($entry['name']) ?></td>
                                        <td class="email-cell" title="<?= htmlspecialchars($entry['email']) ?>">
                                            <?= htmlspecialchars($entry['email']) ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $badgeClass = 'bg-secondary';
                                            $statusText = $entry['status'];
                                            
                                            if (stripos($entry['status'], 'LOGIN SUCCESS') !== false) {
                                                $badgeClass = 'bg-success';
                                                $statusText = 'Login Success';
                                            } elseif (stripos($entry['status'], 'GOOGLE LOGIN') !== false) {
                                                $badgeClass = 'bg-success';
                                                $statusText = 'Google Login';
                                            } elseif (stripos($entry['status'], 'GOOGLE SIGNUP') !== false) {
                                                $badgeClass = 'bg-primary';
                                                $statusText = 'Google Signup';
                                            } elseif (stripos($entry['status'], 'LOGIN FAILED') !== false) {
                                                $badgeClass = 'bg-danger';
                                                $statusText = 'Login Failed';
                                            } elseif (stripos($entry['status'], 'LOGOUT') !== false) {
                                                $badgeClass = 'bg-warning';
                                                $statusText = 'Logout';
                                            } elseif (stripos($entry['status'], 'ACCOUNT LOCKED') !== false) {
                                                $badgeClass = 'bg-danger';
                                                $statusText = 'Account Locked';
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                                        </td>
                                        <td class="time-cell"><?= date('m/d H:i', strtotime($entry['login_time'])) ?></td>
                                        <td class="time-cell">
                                            <?php if ($entry['logout_time']): ?>
                                                <?= date('m/d H:i', strtotime($entry['logout_time'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="ip-cell"><?= htmlspecialchars($entry['ip_address']) ?></td>
                                        <td>
                                            <div class="device-info">
                                                <?= $deviceInfo['display'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="location-info">
                                                <?= $locationDisplay ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
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
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
    <script src="js/script.js"></script>
    <script>
    $(document).ready(function() {
        const table = $('#loginHistoryTable').DataTable({
            responsive: true,
            order: [[5, 'desc']],
            columnDefs: [
                { targets: [0], orderable: false },
                { targets: [3], className: "email-cell" },
                { targets: [5, 6], className: "time-cell" },
                { targets: [7], className: "ip-cell" },
                { targets: [9], className: "location-cell" },
            ],
            language: {
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)"
            }
        });

        $('#searchInput').on('input', function() {
            $('#clearButton').toggle(!!$(this).val());
        });

        $('#searchButton').click(function() {
            const searchTerm = $('#searchInput').val();
            const url = new URL(window.location.href);
            url.searchParams.set('search', searchTerm);
            window.location.href = url.toString();
        });

        $('#clearButton').click(function() {
            $('#searchInput').val('');
            $(this).hide();
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            window.location.href = url.toString();
        });

        $('#filterButton').click(function() {
            const dateStart = $('#date_start').val();
            const dateEnd = $('#date_end').val();
            const url = new URL(window.location.href);
            if (dateStart) url.searchParams.set('date_start', dateStart);
            else url.searchParams.delete('date_start');
            if (dateEnd) url.searchParams.set('date_end', dateEnd);
            else url.searchParams.delete('date_end');
            window.location.href = url.toString();
        });

        $('#resetButton').click(function() {
            const url = new URL(window.location.href);
            url.searchParams.delete('date_start');
            url.searchParams.delete('date_end');
            window.location.href = url.toString();
        });

        $('#exportButton').click(function() {
            alert('Export functionality will be implemented here');
        });

        flatpickr("#date_start", {
            dateFormat: "Y-m-d",
            maxDate: "today"
        });
        
        flatpickr("#date_end", {
            dateFormat: "Y-m-d",
            maxDate: "today"
        });

        $('#clearButton').toggle(!!$('#searchInput').val());
    });
    </script>
</body>
</html>