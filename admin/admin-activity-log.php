<?php
include 'backend/auth.php';
include 'backend/db_connection.php';

function getAdminDisplayLabel($admin_id, $conn) {
    $admin_query = "SELECT full_name, role FROM admins WHERE id = ?";
    $stmt = mysqli_prepare($conn, $admin_query);
    mysqli_stmt_bind_param($stmt, "i", $admin_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        return htmlspecialchars($admin['full_name']) . ' (' . ucfirst($admin['role']) . ')';
    }
    return 'Unknown Admin';
}

function getAdminActionsLog($conn) {
    $activities = array();
    
    $query = "SELECT sa.approved_by, sa.status, sa.approved_at, u.fullname as user_name
          FROM shop_applications sa
          LEFT JOIN users u ON sa.user_id = u.id
          WHERE sa.status IN ('Approved', 'Rejected') AND sa.approved_by IS NOT NULL
          ORDER BY sa.approved_at DESC";
              
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = array(
            'admin_id' => $row['approved_by'],
            'action' => 'SHOP APPLICATION ' . strtoupper($row['status']),
            'description' => 'Acted on the application of ' . htmlspecialchars($row['user_name']),
            'date_time' => $row['approved_at']
        );
    }
    
    return $activities;
}

$adminLog = getAdminActionsLog($conn);
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Activity Log</title>
       <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/activity-log.css">
</head>
<body>
    <?php include 'include/offline-handler.php'; ?>
    
    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>
        <div class="content flex-grow-1">
            <?php include 'include/navbar.php'; ?>
            <div class="container-fluid p-4">
                <h1 class="mb-4">Admin Activity Log</h1>
                
                <div class="search-container mb-4">
                    <form method="get" class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" class="form-control" id="customSearchInput" name="search" placeholder="Search admin actions..." value="<?php echo htmlspecialchars($search_term); ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                                <?php if (!empty($search_term)): ?>
                                    <a href="admin-activity-log.php" class="btn btn-outline-secondary">Clear</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table id="adminLogTable" class="table table-striped table-bordered nowrap" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            foreach ($adminLog as $activity): 
                                $adminLabel = getAdminDisplayLabel($activity['admin_id'], $conn);
                                if (!empty($search_term)) {
                                    $search_match = false;
                                    if (stripos($adminLabel, $search_term) !== false) $search_match = true;
                                    if (stripos($activity['action'], $search_term) !== false) $search_match = true;
                                    if (stripos($activity['description'], $search_term) !== false) $search_match = true;
                                    if (!$search_match) continue;
                                }
                            ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><?php echo $adminLabel; ?></td>
                                <td><?php echo $activity['action']; ?></td>
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
    $(document).ready(function() {
        var table = $('#adminLogTable').DataTable({
            dom: 'Brtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            responsive: true,
            order: [[4, 'desc']],
            searching: true
        });
        
        $('#customSearchInput').on('keyup', function(){
            table.search(this.value).draw();
        });
    });
    </script>
</body>
</html>