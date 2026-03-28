<?php
session_start();
include 'backend/auth.php';
include 'backend/db_connection.php';

function getUserActivityData($conn, $adminId) {
    $data = ['state' => 'Active', 'days_inactive' => 0, 'last_activity' => null];
    
    $sessionSql = "SELECT last_activity FROM active_sessions WHERE user_id = ? ORDER BY last_activity DESC LIMIT 1";
    $sessionStmt = $conn->prepare($sessionSql);
    $sessionStmt->bind_param("i", $adminId);
    $sessionStmt->execute();
    $sessionResult = $sessionStmt->get_result();
    
    if ($sessionResult->num_rows > 0) {
        $session = $sessionResult->fetch_assoc();
        $lastActivity = new DateTime($session['last_activity']);
        $now = new DateTime();
        $inactiveDays = $now->diff($lastActivity)->days;
        $data['days_inactive'] = $inactiveDays;
        $data['last_activity'] = $session['last_activity'];
    }
    
    $userSql = "SELECT last_login, created_at, account_state FROM users WHERE id = ?";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param("i", $adminId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        $data['state'] = $user['account_state'];
        
        if (!$data['last_activity']) {
            $lastActivityDate = $user['last_login'] ?: $user['created_at'];
            $lastActivity = new DateTime($lastActivityDate);
            $now = new DateTime();
            $inactiveDays = $now->diff($lastActivity)->days;
            $data['days_inactive'] = $inactiveDays;
            $data['last_activity'] = $lastActivityDate;
        }
        
        if ($data['days_inactive'] >= 90 && $user['account_state'] === 'Active') {
            $updateSql = "UPDATE users SET account_state = 'Inactive' WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $adminId);
            $updateStmt->execute();
            $data['state'] = 'Inactive';
        }
    }
    
    return $data;
}

$totalUsersQuery = "SELECT COUNT(*) as total FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total'];

$totalOwnersQuery = "SELECT COUNT(*) as total FROM users WHERE profile_type = 'owner'";
$totalOwnersResult = $conn->query($totalOwnersQuery);
$totalOwners = $totalOwnersResult->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/user-management.css">
    <style>
        .user-profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'include/offline-handler.php'; ?>
    
    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>

        <div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content text-center">
                    <div class="modal-header justify-content-center border-0">
                        <h5 class="modal-title w-100" id="statusModalTitle">Confirm Action</h5>
                    </div>
                    <div class="modal-body px-4" id="statusModalBody">
                        Are you sure you want to perform this action?
                    </div>
                    <div class="modal-footer justify-content-center border-0 pb-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="statusModalForm" method="POST" action="" class="d-inline">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
                <div class="modal-content text-center">
                    <div class="modal-header justify-content-center border-0 pb-0">
                        <h5 class="modal-title w-100" id="deleteModalTitle">Confirm Deletion</h5>
                    </div>
                    <div class="modal-body px-4 pt-2" id="deleteModalBody">
                        <p class="mb-3">Are you sure you want to delete this account? <br><strong>This action cannot be undone.</strong></p>
                        <div class="mt-3">
                            <p class="mb-2">To confirm deletion, please type <strong>DELETE</strong> below:</p>
                            <input type="text" id="deleteVerificationInput" class="form-control text-center mb-2" placeholder="Type DELETE to confirm">
                            <div id="deleteVerificationError" class="text-danger small" style="display: none;">Text doesn't match. Please try again.</div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center border-0 pb-4">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteModalForm" method="POST" action="" class="d-inline">
                            <button type="button" id="deleteConfirmButton" class="btn btn-danger" disabled>Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="content flex-grow-1">
            <?php include 'include/navbar.php'; ?>

            <div class="container-fluid p-4">
                
                <div class="d-block d-md-flex justify-content-md-between align-items-md-center mb-4">
                    <h1 class="mb-2 mb-md-0">User Management</h1>
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search users...">
                        <button class="btn btn-outline-secondary" type="button" id="searchButton">
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="btn btn-outline-danger" type="button" id="clearButton" title="Clear search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text h2"><?php echo $totalUsers; ?></p>
                                <i class="fas fa-users text-muted position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h5 class="card-title">Shop Owners</h5>
                                <p class="card-text h2"><?php echo $totalOwners; ?></p>
                                <i class="fas fa-store text-muted position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-auto mb-4">
                    <ul class="nav nav-tabs flex-nowrap" id="userTabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-tab="all-users">All Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-tab="owners">Shop Owners</a>
                        </li>
                    </ul>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Inactive Days</th>
                                        <th>Last Activity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
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
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php include 'include/toast.php'; ?>
    <script src="js/script.js"></script>

    <script>
        $(document).ready(function () {
            $('#clearButton').hide();
            let currentTab = 'all-users';
            let usersTable;

            function toggleClearButton() {
                if ($('#searchInput').val().length > 0) {
                    $('#clearButton').show();
                } else {
                    $('#clearButton').hide();
                }
            }

            $('#searchInput').on('keyup input', function () {
                toggleClearButton();
            });

            function initializeDataTable(tab) {
                if ($.fn.DataTable.isDataTable('#usersTable')) {
                    usersTable.destroy();
                }

                usersTable = $('#usersTable').DataTable({
                    responsive: true,
                    searching: false,
                    ajax: {
                        url: 'backend/get_users.php',
                        dataSrc: '',
                        data: function (d) {
                            d.search = $('#searchInput').val();
                            d.tab = tab;
                            sessionStorage.setItem('userSearch', $('#searchInput').val());
                            sessionStorage.setItem('currentTab', tab);
                        }
                    },
                    columns: [
                        { 
                            data: null,
                            render: function (data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        { 
                            data: 'fullname',
                            render: function (data, type, row) {
                                const fullName = data || '';
                                const profileType = row.profile_type;
                                let profilePic = row.profile_picture;
                                let shopLogo = row.shop_logo;
                                
                                console.log('=== DEBUG INFO ===');
                                console.log('User:', fullName);
                                console.log('Profile Type:', profileType);
                                console.log('Profile Pic from DB:', profilePic);
                                console.log('Shop Logo from DB:', shopLogo);
                                
                                let defaultImgPath, imgSrc;
                                
                                if (profileType === 'owner') {
                                    defaultImgPath = '../../account/uploads/shop_logo/logo.jpg';
                                    imgSrc = defaultImgPath;
                                    
                                    if (shopLogo && shopLogo.trim() !== '') {
                                        shopLogo = shopLogo.trim();
                                        
                                        if (shopLogo.includes('../') || shopLogo.includes('account/')) {
                                            imgSrc = shopLogo;
                                        } else {
                                            imgSrc = '../../account/uploads/shop_logo/' + shopLogo;
                                        }
                                        console.log('Using shop logo:', imgSrc);
                                    } else {
                                        console.log('Using default shop logo');
                                    }
                                } else {
                                    defaultImgPath = '../assets/img/profile/profile-user.png';
                                    imgSrc = defaultImgPath;
                                    
                                    if (profilePic && profilePic.trim() !== '') {
                                        profilePic = profilePic.trim();
                                        
                                        if (profilePic.includes('../') || profilePic.includes('assets/')) {
                                            imgSrc = profilePic;
                                        } else {
                                            imgSrc = '../assets/img/profile/' + profilePic;
                                        }
                                        console.log('Using profile picture:', imgSrc);
                                    } else {
                                        console.log('Using default profile picture');
                                    }
                                }
                                
                                console.log('Final Image Path:', imgSrc);
                                console.log('==================');

                                return '<div class="d-flex align-items-center">' +
                                       '<img src="' + imgSrc + '" ' +
                                       'alt="Profile" ' +
                                       'class="user-profile-pic me-3" ' +
                                       'onerror="this.onerror=null; this.src=\'' + defaultImgPath + '\'; console.error(\'Image failed to load:\', this.src);">' +
                                       '<div>' +
                                       '<div class="fw-bold">' + fullName + '</div>' +
                                       '</div>' +
                                       '</div>';
                            }
                        },
                        { data: 'email' },
                        { 
                            data: 'profile_type',
                            render: function (data) {
                                const badgeClass = data === 'owner' ? 'bg-warning' : 'bg-primary';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'account_state',
                            render: function (data) {
                                const badgeClass = data === 'Active' ? 'bg-success' : 'bg-danger';
                                return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                            }
                        },
                        { 
                            data: 'days_inactive',
                            render: function (data, type, row) {
                                let daysClass = '';
                                if (data >= 60) daysClass = 'warning';
                                if (data >= 90) daysClass = 'danger';
                                return '<span class="inactive-days ' + daysClass + '">' + data + ' days</span>';
                            }
                        },
                        { 
                            data: 'last_activity',
                            render: function (data) {
                                return data ? new Date(data).toLocaleString() : 'Never';
                            }
                        },
                        { 
                            data: null,
                            orderable: false,
                            render: function (data, type, row) {
                                const isActive = data.account_state === 'Active';
                                return '<div class="btn-group gap-2" role="group">' +
                                       '<button type="button" class="btn btn-sm ' + (isActive ? 'btn-danger' : 'btn-success') + '" ' +
                                       'onclick="showStatusConfirmationModal(' +
                                       '\'' + (isActive ? 'Deactivate' : 'Activate') + ' User\', ' +
                                       '\'Are you sure you want to ' + (isActive ? 'deactivate' : 'activate') + ' this account?\', ' +
                                       '\'backend/update_account_state.php\', ' +
                                       '{user_id: \'' + data.id + '\', account_state: \'' + (isActive ? 'Inactive' : 'Active') + '\'} ' +
                                       ')">' +
                                       (isActive ? 'Deactivate' : 'Activate') +
                                       '</button>' +
                                       '<button type="button" class="btn btn-sm btn-danger" ' +
                                       'onclick="showDeleteConfirmationModal(' +
                                       '\'Delete User\', ' +
                                       '\'Are you sure you want to delete this user account? This action cannot be undone.\', ' +
                                       '\'backend/delete_user.php\', ' +
                                       '{user_id: \'' + data.id + '\'} ' +
                                       ')">' +
                                       '<i class="fas fa-trash"></i>' +
                                       '</button>' +
                                       '</div>';
                            }
                        }
                    ],
                    initComplete: function () {
                        var savedSearch = sessionStorage.getItem('userSearch');
                        if (savedSearch) {
                            $('#searchInput').val(savedSearch);
                            toggleClearButton();
                        }
                    }
                });
            }

            $('#userTabs a').on('click', function (e) {
                e.preventDefault();
                $('#userTabs a').removeClass('active');
                $(this).addClass('active');
                currentTab = $(this).data('tab');
                initializeDataTable(currentTab);
            });

            $('#searchButton').click(function () {
                usersTable.ajax.reload();
            });

            $('#searchInput').keyup(function (e) {
                if (e.keyCode === 13) {
                    usersTable.ajax.reload();
                }
            });

            $('#clearButton').click(function () {
                $('#searchInput').val('');
                sessionStorage.removeItem('userSearch');
                usersTable.ajax.reload();
                $(this).hide();
            });

            $('#searchInput').on('search', function () {
                if ($(this).val() === '') {
                    sessionStorage.removeItem('userSearch');
                    usersTable.ajax.reload();
                    $('#clearButton').hide();
                }
            });

            initializeDataTable(currentTab);
        });

        const statusConfirmModal = new bootstrap.Modal(document.getElementById('statusConfirmModal'));
        const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));

        function showStatusConfirmationModal(title, message, action, data) {
            document.getElementById('statusModalTitle').textContent = title;
            document.getElementById('statusModalBody').textContent = message;
            
            const form = document.getElementById('statusModalForm');
            form.action = action;
            
            const existingInputs = form.querySelectorAll('input[type="hidden"]');
            existingInputs.forEach(input => input.remove());
            
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data[key];
                    form.appendChild(input);
                }
            }
            
            statusConfirmModal.show();
        }

        function showDeleteConfirmationModal(title, message, action, data) {
            document.getElementById('deleteModalTitle').textContent = title;
            document.getElementById('deleteModalBody').firstElementChild.textContent = message;
            
            const form = document.getElementById('deleteModalForm');
            form.action = action;
            
            const existingInputs = form.querySelectorAll('input[type="hidden"]');
            existingInputs.forEach(input => input.remove());
            
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data[key];
                    form.appendChild(input);
                }
            }
            
            const verificationInput = document.getElementById('deleteVerificationInput');
            const errorElement = document.getElementById('deleteVerificationError');
            const deleteButton = document.getElementById('deleteConfirmButton');
            
            verificationInput.value = '';
            errorElement.style.display = 'none';
            deleteButton.disabled = true;
            
            verificationInput.addEventListener('input', function() {
                const userInput = this.value.trim();
                
                if (userInput === 'DELETE') {
                    deleteButton.disabled = false;
                    errorElement.style.display = 'none';
                } else {
                    deleteButton.disabled = true;
                    if (userInput.length > 0 && userInput !== 'DELETE') {
                        errorElement.style.display = 'block';
                    } else {
                        errorElement.style.display = 'none';
                    }
                }
            });
            
            deleteButton.onclick = function() {
                const userInput = verificationInput.value.trim();
                if (userInput === 'DELETE') {
                    form.submit();
                }
            };
            
            deleteConfirmModal.show();
        }

        document.getElementById('deleteVerificationInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('deleteConfirmButton').click();
            }
        });
    </script>
</body>
</html>