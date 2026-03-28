<?php
include 'backend/auth.php';
include 'backend/db_connection.php';
include 'backend/user-activity-log.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity Log</title>
       <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="css/styles.css">
     <link rel="stylesheet" href="css/user-activity-log.css">

</head>

<body>
    <?php include 'include/offline-handler.php'; ?>
    
    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>
        <div class="content flex-grow-1">
            <?php include 'include/navbar.php'; ?>
            <div class="container-fluid p-4">
                <h1 class="mb-4">User Activity Log</h1>

                <div class="search-container">
                    <form method="get" class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search activities..."
                                    value="<?php echo htmlspecialchars($search_term); ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                                <?php if (!empty($search_term)): ?>
                                    <a href="?category=<?php echo $current_category; ?>"
                                        class="btn btn-outline-secondary">Clear</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <input type="hidden" name="category" value="<?php echo $current_category; ?>">
                    </form>
                </div>

                <ul class="nav category-tabs mb-4">
                    <?php foreach ($categories as $key => $name): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_category === $key ? 'active' : ''; ?>"
                                href="?category=<?php echo $key; ?><?php echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>">
                                <?php echo $name; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="table-responsive">
                    <table id="activityLogTable" class="table table-striped table-bordered nowrap" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>User Type</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            foreach ($activityLog as $activity):
                                if ($current_category !== 'all' && $activity['category'] !== $current_category)
                                    continue;
                                if (!empty($search_term)) {
                                    $search_match = false;
                                    foreach ($activity as $value) {
                                        if (is_string($value) && stripos($value, $search_term) !== false) {
                                            $search_match = true;
                                            break;
                                        }
                                    }
                                    if (!$search_match)
                                        continue;
                                }
                                $userLabel = getUserTypeLabel($activity['user_id'], $activity['profile_type'], $conn);
                                $rowClass = '';
                                if ($activity['category'] === 'authentication') {
                                    switch ($activity['action']) {
                                        case 'LOGIN SUCCESS':
                                            $rowClass = 'auth-login';
                                            break;
                                        case 'LOGOUT':
                                            $rowClass = 'auth-logout';
                                            break;
                                        case 'GOOGLE LOGIN':
                                            $rowClass = 'auth-google';
                                            break;
                                        case 'LOGIN ATTEMPT':
                                            $rowClass = 'auth-attempt';
                                            break;
                                        case 'LOGIN FAILED':
                                            $rowClass = 'auth-failed';
                                            break;
                                        case 'ACCOUNT LOCKED':
                                            $rowClass = 'auth-locked';
                                            break;
                                    }
                                }
                                ?>
                                <tr class="<?php echo $rowClass; ?>">
                                    <td><?php echo $counter++; ?></td>
                                    <td><?php echo $userLabel; ?></td>
                                    <td><?php echo str_replace('_', ' ', $activity['action']); ?></td>
                                    <td><?php echo $activity['description']; ?></td>
                                    <td><?php echo date('Y-m-d h:i A', strtotime($activity['date_time'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
              <?php include 'include/footer.php'; ?>
        </div>
    </div>

    <?php include 'include/back-to-top.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    
    <script src="js/script.js"></script>
    
    <script>
        $(document).ready(function () {
            $('#activityLogTable').DataTable({
                dom: 'Brtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                responsive: true,
                order: [[4, 'desc']],
                createdRow: function (row, data, dataIndex) {
                    if (data[2] === 'LOGIN SUCCESS') $(row).addClass('auth-login');
                    else if (data[2] === 'LOGOUT') $(row).addClass('auth-logout');
                    else if (data[2] === 'GOOGLE LOGIN') $(row).addClass('auth-google');
                    else if (data[2] === 'LOGIN ATTEMPT') $(row).addClass('auth-attempt');
                    else if (data[2] === 'LOGIN FAILED') $(row).addClass('auth-failed');
                    else if (data[2] === 'ACCOUNT LOCKED') $(row).addClass('auth-locked');
                }
            });
        });
    </script>
</body>

</html>