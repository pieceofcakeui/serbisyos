<div id="sidebar" class="sidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center px-3 py-2">
        <img src="img/logo/logo.png" alt="Logo" style="width: 150px; height: 50px;">
        <button class="btn d-md-none" id="sidebarClose" style="font-size: 1.5rem; background: none; border: none;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <hr>
    <div class="d-flex flex-column px-3">
        <a href="dashboard.php" class="sidebar-link">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="application.php" class="sidebar-link active">
            <i class="fas fa-file-alt"></i> Applications
        </a>
        <a href="analytics.php" class="sidebar-link">
            <i class="fas fa-chart-bar"></i> Analytics
        </a>
        <a href="user-verification.php" class="sidebar-link">
            <i class="fas fa-id-card"></i> User Verification
        </a>
        <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

        <div id="sidebarAccordion">

            <li class="sidebar-item">
                <a href="#manageSubMenu" data-bs-toggle="collapse" class="sidebar-link <?= in_array($currentPage, ['manage-services.php', 'manage-emergency-services.php']) ? '' : 'collapsed' ?>">
                    <i class="fas fa-cogs"></i> Manage Services
                    <i class="fas fa-chevron-down float-end mt-1 ms-2 collapse-icon"></i>
                </a>
                <ul id="manageSubMenu" class="collapse ps-4 <?= in_array($currentPage, ['manage-services.php', 'manage-emergency-services.php']) ? 'show' : '' ?>" data-bs-parent="#sidebarAccordion">
                    <li>
                        <a href="manage-services.php" class="sidebar-link <?= $currentPage === 'manage-services.php' ? 'active-custom' : '' ?>">
                            <i class="fas fa-cogs me-2"></i> Auto Repair Services
                        </a>
                    </li>
                    <li>
                        <a href="manage-emergency-services.php" class="sidebar-link <?= $currentPage === 'manage-emergency-services.php' ? 'active-custom' : '' ?>">
                            <i class="fas fa-exclamation-triangle me-2"></i> Emergency Services
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item">
                <a href="#usersSubMenu" data-bs-toggle="collapse" class="sidebar-link <?= in_array($currentPage, ['user-management.php', 'create-admin.php', 'shop-reports.php']) ? '' : 'collapsed' ?>">
                    <i class="fas fa-user-friends"></i> Users Management
                    <i class="fas fa-chevron-down float-end mt-1 ms-2 collapse-icon"></i>
                </a>
                <ul id="usersSubMenu" class="collapse ps-4 <?= in_array($currentPage, ['user-management.php', 'create-admin.php', 'shop-reports.php']) ? 'show' : '' ?>" data-bs-parent="#sidebarAccordion">
                    <li>
                        <a href="user-management.php" class="sidebar-link <?= $currentPage === 'user-management.php' ? 'active-custom' : '' ?>">
                            <i class="fas fa-user-friends me-2"></i> All Users / Owners
                        </a>
                    </li>
                    <li>
                        <a href="create-admin.php" class="sidebar-link <?= $currentPage === 'create-admin.php' ? 'active-custom' : '' ?>">
                            <i class="fas fa-user-plus me-2"></i> Create Admin
                        </a>
                    </li>
                    <li>
                        <a href="shop-reports.php" class="sidebar-link <?= $currentPage === 'shop-reports.php' ? 'active-custom' : '' ?>">
                            <i class="fas fa-flag me-2"></i> Shop Reports
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item">
                <a href="#activitySubMenu" data-bs-toggle="collapse" class="sidebar-link <?= in_array($currentPage, ['user-activity-log.php', 'admin-activity-log.php']) ? '' : 'collapsed' ?>">
                    <i class="fas fa-history"></i> Activity Log
                    <i class="fas fa-chevron-down float-end mt-1 ms-2 collapse-icon"></i>
                </a>
                <ul id="activitySubMenu" class="collapse ps-4 <?= in_array($currentPage, ['user-activity-log.php', 'admin-activity-log.php', 'login-history.php']) ? 'show' : '' ?>" data-bs-parent="#sidebarAccordion">
                    <a href="user-activity-log.php" class="sidebar-link <?= $currentPage === 'user-activity-log.php' ? 'active-custom' : '' ?>">
                        <i class="fas fa-user-clock me-2"></i> Users Activity Log
                    </a>
                    <a href="admin-activity-log.php" class="sidebar-link <?= $currentPage === 'admin-activity-log.php' ? 'active-custom' : '' ?>">
                        <i class="fas fa-user-shield me-2"></i> Admin Activity Log
                    </a>
                    <a href="login-history.php" class="sidebar-link <?= $currentPage === 'login-history.php' ? 'active-custom' : '' ?>">
                        <i class="fas fa-sign-in-alt me-2"></i> Login History
                    </a>
                </ul>
            </li>

        </div>
    </div>
</div>

<style>
    #usersSubMenu,
    #activitySubMenu,
    #manageSubMenu {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }

    #usersSubMenu li,
    #activitySubMenu li,
    #manageSubMenu li {
        list-style-type: none;
        list-style: none;
    }

    .sidebar-item {
        list-style: none;
    }

    .sidebar-link.active-custom {
        background-color: #f0f0f0;
        color: #000 !important;
    }

    .sidebar-link:not(.collapsed) .collapse-icon {
        transform: rotate(180deg);
        transition: transform 0.2s ease;
    }

    .collapse-icon {
        transition: transform 0.2s ease;
    }
</style>

<style>
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-250px);
        }

        .sidebar.active {
            transform: translateX(0);
        }
    }

    #sidebarClose {
        color: black;
        background: none;
        border: none;
    }

    #sidebarClose:hover {
        color: #ffc107;
    }

    #sidebarToggle {
        color: black;
    }

    #sidebarToggle:hover {
        color: #ffc107;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarAccordion = document.getElementById('sidebarAccordion');

        sidebarAccordion.addEventListener('show.bs.collapse', function(event) {
            const openCollapses = sidebarAccordion.querySelectorAll('.collapse.show');

            for (let i = 0; i < openCollapses.length; i++) {
                const collapseInstance = bootstrap.Collapse.getInstance(openCollapses[i]);
                if (collapseInstance) {
                    collapseInstance.hide();
                }
            }
        });
    });
</script>