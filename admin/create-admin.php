<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$feedbackType = '';
$feedbackMessage = '';
$fromModal = false;

if (isset($_SESSION['modal_error'])) {
    $feedbackType = 'error';
    $feedbackMessage = $_SESSION['modal_error'];
    $fromModal = true;
    unset($_SESSION['modal_error']);
}
if (isset($_SESSION['modal_success'])) {
    $feedbackType = 'success';
    $feedbackMessage = $_SESSION['modal_success'];
    unset($_SESSION['modal_success']);
}

include 'backend/auth.php';
include 'backend/db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        #searchButton,
        #clearButton {
            padding: 0.375rem 0.75rem;
        }

        #clearButton {
            border-left: none;
        }

        .input-group>.btn-outline-danger {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        #searchInput:focus {
            outline: none;
            box-shadow: none;
        }
    </style>
</head>

<body>
    <?php include 'include/offline-handler.php'; ?>
    
    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>

        <div class="content flex-grow-1">
            <?php include 'include/navbar.php'; ?>

            <div class="container-fluid p-4">
                
                <!-- 
                  FIX: Replaced d-flex justify-content-between with responsive flex classes.
                  - flex-column: Stacks elements vertically on extra-small screens.
                  - flex-md-row: Switches to horizontal layout on medium screens and up.
                  - justify-content-md-between: Applies space-between only on medium screens and up.
                  - align-items-start: Aligns items to the start (left) on small screens.
                  - align-items-md-center: Centers items vertically on medium screens and up.
                -->
                <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-start align-items-md-center mb-4">
                    <!-- Added margin-bottom for spacing when stacked, removed on medium+ screens -->
                    <h1 class="mb-3 mb-md-0">Admin Accounts</h1>
                    
                    <!-- 
                      This inner container also stacks controls vertically on small screens (flex-column)
                      and switches to horizontal on small screens+ (flex-sm-row).
                      It takes full width on small screens (w-100) and auto-width on medium+ (w-md-auto).
                    -->
                    <div class="d-flex flex-column flex-sm-row w-100 w-md-auto">
                        <!-- 
                          FIX: Removed fixed 'width: 300px;' and replaced with 'min-width: 250px;'
                          Added margins for spacing when stacked (mb-2, mb-sm-0, me-sm-3).
                        -->
                        <div class="input-group mb-2 mb-sm-0 me-sm-3" style="min-width: 250px;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search admins...">
                            <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-danger" type="button" id="clearButton" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            <i class="fas fa-plus me-2"></i>Add New Admin
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="adminsTable" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Status</th>
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

    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdminModalLabel">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addAdminForm" action="backend/add_admin.php" method="POST" autocomplete="off" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required
                                value="<?= isset($_SESSION['form_data']['fullName']) ? htmlspecialchars($_SESSION['form_data']['fullName']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="create-admin">Create Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" id="feedbackDialog" style="max-width: 360px;">
            <div class="modal-content text-center p-4 shadow-sm">
                <div id="feedbackIcon" class="mb-3"></div>
                <h6 id="feedbackMessage" class="mb-3 fw-semibold text-wrap px-2"></h6>
                <button type="button" class="btn btn-primary btn-sm px-4 mx-auto" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>

    <?php include 'include/back-to-top.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

     <script src="js/script.js"></script>

    <script>
        $(document).ready(function () {
            $('#clearButton').hide();

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

            var table = $('#adminsTable').DataTable({
                responsive: true,
                searching: false,
                ajax: {
                    url: 'backend/get_admins.php',
                    dataSrc: '',
                    data: function (d) {
                        d.search = $('#searchInput').val();
                        sessionStorage.setItem('adminSearch', $('#searchInput').val());
                    }
                },
                columns: [{
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                }, {
                    data: 'full_name',
                    render: function (data) {
                        return data;
                    }
                }, {
                    data: 'email'
                }, {
                    data: 'created_at',
                    render: function (data) {
                        return new Date(data).toLocaleString();
                    }
                }, {
                    data: null,
                    orderable: false,
                    render: function (data) {
                        if (data.is_current) {
                            return '<span class="text-success fst-italic">Current User</span>';
                        } else {
                            return '<span class="text-dark fst-italic">Not Your Account</span>';
                        }
                    }
                }],
                initComplete: function () {
                    var savedSearch = sessionStorage.getItem('adminSearch');
                    if (savedSearch) {
                        $('#searchInput').val(savedSearch);
                        table.ajax.reload();
                    }
                    toggleClearButton();
                }
            });

            $('#searchButton').click(function () {
                table.ajax.reload();
            });

            $('#searchInput').keyup(function (e) {
                if (e.keyCode === 13) {
                    table.ajax.reload();
                }
            });

            $('#clearButton').click(function () {
                $('#searchInput').val('');
                sessionStorage.removeItem('adminSearch');
                table.ajax.reload();

                $(this).hide();
            });

            $('#searchInput').on('search', function () {
                if ($(this).val() === '') {
                    sessionStorage.removeItem('adminSearch');
                    table.ajax.reload();
                    $('#clearButton').hide();
                }
            });

            $('#addAdminModal').on('hidden.bs.modal', function () {
                table.ajax.reload();
            });

            const type = <?= json_encode($feedbackType) ?>;
            const message = <?= json_encode($feedbackMessage) ?>;
            const fromModal = <?= json_encode($fromModal) ?>;

            if (message) {
                let iconHTML = '';
                let dialog = document.getElementById('feedbackDialog');

                if (type === 'success') {
                    iconHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.08.022l3.992-3.99a.75.75 0 0 0-1.06-1.06L7.5 9.44 5.53 7.47a.75.75 0 0 0-1.06 1.06l2.5 2.5z"/>
                    </svg>`;
                    const addModal = bootstrap.Modal.getInstance(document.getElementById('addAdminModal'));
                    if (addModal) addModal.hide();
                    document.getElementById('addAdminForm').reset();
                }

                if (type === 'error') {
                    iconHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#dc3545" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>`;
                    const addModal = new bootstrap.Modal(document.getElementById('addAdminModal'));
                    addModal.show();
                }

                document.getElementById('feedbackIcon').innerHTML = iconHTML;
                document.getElementById('feedbackMessage').textContent = message;

                const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'), {
                    backdrop: false
                });
                feedbackModal.show();
            }

            $(document).on('click', '.password-toggle', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var container = $(this).closest('.password-input-container');
                var input = container.find('input');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    container.addClass('show-password');
                } else {
                    input.attr('type', 'password');
                    container.removeClass('show-password');
                }
                input.focus();
            });

            function resetAddAdminForm() {
                $('#addAdminForm')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.password-input-container').removeClass('show-password');
                $('#password, #confirmPassword').attr('type', 'password');
            }

            $('#addAdminModal').on('hidden.bs.modal', resetAddAdminForm);
        });
    </script>
</body>

</html>
