<?php
require './backend/db_connection.php';
include './backend/auth.php';
require_once './backend/base-path.php';
include './backend/navbar.php';

date_default_timezone_set('Asia/Manila');
$current_hour = (int)date('G');

$shop_name = null;
$shop_logo_filename = null; 

if ($user['profile_type'] === 'owner') {
    $query = "SELECT shop_name, shop_logo FROM shop_applications WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stmt->bind_result($shop_name, $shop_logo_filename);
    $stmt->fetch();
    $stmt->close();
}

if ($user['profile_type'] === 'owner') {
    if (!empty($shop_logo_filename)) {
        $profile_image_source = BASE_URL . '/account/uploads/shop_logo/' . htmlspecialchars($shop_logo_filename);
    } else {
        $profile_image_source = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
    }
    $user_name = htmlspecialchars($shop_name ? $shop_name : $user['fullname']);
} elseif (!empty($user['fullname'])) {
    $profile_image_source = BASE_URL . '/assets/img/profile/' . htmlspecialchars($user['profile_picture'] ? $user['profile_picture'] : 'profile-user.png');
    $user_name = htmlspecialchars($user['fullname']);
} else {
    $profile_image_source = BASE_URL . '/assets/img/profile/profile-user.png';
    $user_name = "Valued User";
}

if ($current_hour >= 0 && $current_hour < 12) {
    $greeting = "Good morning, " . $user_name;
} elseif ($current_hour >= 12 && $current_hour < 18) {
    $greeting = "Good afternoon, " . $user_name;
} else {
    $greeting = "Good evening, " . $user_name;
}

$request_uri = $_SERVER['REQUEST_URI'];

$is_home_active = true;

$active_paths = [
    '/services.php',
    '/booking-provider.php',
    '/emergency-provider.php',
    '/emergency-help.php',
    '/my-booking.php',
    '/my-emergency-request.php',
    '/booking.php',
    '/emergency-request.php',
    '/save-shops.php',
    '/chatbot.php',
    '/inbox.php',
    '/become-a-partner.php'
];

foreach ($active_paths as $path) {
    if (strpos($request_uri, $path) !== false) {
        $is_home_active = false;
        break;
    }
}

if ($is_home_active && strpos($request_uri, '/home') === false && rtrim($request_uri, '/') !== '') {
    $is_home_active = false;
}
if (strpos($request_uri, '/home') !== false || rtrim($request_uri, '/') === '') {
    $is_home_active = true;
}

$show_services_icon_active = strpos($request_uri, '/booking-provider.php') !== false || strpos($request_uri, '/emergency-provider.php') !== false;
$show_messages_icon_active = strpos($request_uri, '/chatbot.php') !== false || strpos($request_uri, '/inbox.php') !== false;
?>

<style>
    :root {
        --primary-color: #ffc107;
        --dark-gray: #1F2937;
        --medium-gray: #6B7280;
        --light-gray: #F3F4F6;
        --border-color: #E5E7EB;
        --body-bg: #FFFFFF;
        --danger-color: #DC3545;
    }

    body {
        overflow-x: hidden;
        background-color: var(--body-bg);
        font-family: 'Montserrat', sans-serif;
        color: var(--dark-gray);
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100vh;
        z-index: 1040;
        transition: all 0.3s ease-in-out;
        transform: translateX(-250px);
        background-color: #fffbeb;
        border-right: 1px solid #ffecb3;
    }

    .sidebar.active {
        transform: translateX(0);
    }

    #page-header {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1020;
        transition: left 0.3s ease-in-out;
        background-color: var(--body-bg);
        border-bottom: 1px solid var(--border-color);
    }

    .main-content {
        transition: margin-left 0.3s ease-in-out;
        padding-top: 60px;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1030;
        display: none;
    }

    .overlay.active {
        display: block;
    }

    #mobile-open-btn {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        position: relative;
        top: 8px;
        background-color: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        outline: none !important;
        box-shadow: none !important;
        color: var(--medium-gray);
    }

    #mobile-open-btn:hover,
    #mobile-open-btn:focus,
    #mobile-open-btn:active {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
    }

    @media (min-width: 992px) {
        .sidebar {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 250px;
        }

        #page-header {
            left: 250px;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-brand-wrapper,
        .sidebar.collapsed .sidebar-section-label {
            display: none;
        }

        .sidebar.collapsed .sidebar-section-header {
            display: none;
        }

        .sidebar.collapsed .nav-pills {
            gap: 0.5rem;
        }

        .sidebar.collapsed .sidebar-header,
        .sidebar.collapsed .nav-link {
            justify-content: center !important;
        }

        .sidebar.collapsed .nav-link .bi,
        .sidebar.collapsed .nav-link .partner-icon,
        .partner-icon {
            margin-right: 0;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        #page-header.collapsed {
            left: 80px;
        }

        .sidebar.collapsed .nav-link {
            position: relative;
        }

        .sidebar.collapsed .nav-link>.badge {
            position: absolute;
            top: 4px;
            right: 22px;
            font-size: 0.6em;
            padding: 0.2em 0.45em;
            line-height: 1;
        }

        .sidebar.collapsed .dropdown-indicator {
            display: none;
        }

        .sidebar.collapsed .nav-link.active .bi {
            color: #000 !important;
        }
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: #ffda6a;
        border-radius: 10px;
    }

    .sidebar .nav-link {
        color: #5a5a5a;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        color: #000 !important;
        background-color: rgba(255, 193, 7, 0.6);
    }

    .sidebar .nav-link .bi,
    .sidebar .nav-link .partner-icon,
    .partner-icon {
        margin-right: 0.75rem;
    }

    .sidebar-header {
        border-color: #ffecb3 !important;
    }

    .sidebar-header .btn:focus,
    #mobile-open-btn:focus {
        box-shadow: none !important;
        outline: none !important;
    }

    .header-greeting h5 {
        color: var(--dark-gray);
    }

    .dropdown-menu {
        border-radius: 12px;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: .5rem 1rem;
    }

    .dropdown-item .bi {
        font-size: 1.1rem;
    }

    .dropdown-item-highlight {
        background-color: #fffbeb;
        border: 1px solid #ffecb3;
        border-radius: 0.375rem;
        font-weight: 500;
    }

    .icon-container {
        position: relative;
        font-size: 1.2em;
        cursor: pointer;
        color: #555;
        text-decoration: none !important;
        transition: background-color 0.3s, color 0.3s, transform 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .icon-badge,
    .profile-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background-color: #ff3b30;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 11px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        border: 2px solid white;
        z-index: 1;
    }

    .icon-container:hover {
        background-color: #f0f0f0;
        color: #333;
        text-decoration: none !important;
    }

    .icon-container:focus,
    .icon-container:active,
    .icon-container:visited {
        text-decoration: none !important;
    }

    .nav-link.sub-link.active {
        background-color: rgba(255, 193, 7, 0.3);
        color: #000 !important;
    }

    #services-nav-link.active,
    #messages-nav-link.active {
        background-color: transparent !important;
        color: #5a5a5a !important;
    }

    #services-nav-link.active .dropdown-indicator,
    #messages-nav-link.active .dropdown-indicator {
        color: #5a5a5a !important;
    }

    .sidebar.collapsed .collapse {
        display: none !important;
    }

    .sidebar.collapsed .nav-link.active {
        background-color: var(--primary-color) !important;
    }

    #desktop-toggle-btn:focus,
    #desktop-toggle-btn:focus-visible,
    #desktop-toggle-btn:focus-within,
    #close-sidebar-btn:focus,
    #close-sidebar-btn:focus-visible,
    #close-sidebar-btn:focus-within {
        outline: none;
        box-shadow: none;
    }

    .sidebar.collapsed .sidebar-badge {
        position: absolute;
        top: 4px;
        right: 22px;
        font-size: 0.6em;
        padding: 0.2em 0.45em;
        line-height: 1;
        margin-right: 0 !important;
    }

    .sidebar.collapsed .nav-link .position-relative {
        display: contents;
    }

    .sidebar-section-label {
        letter-spacing: 0.5px;
        font-size: 0.7rem;
    }

<?php if ($is_home_active): ?>
    #mobile-open-btn {
        top: 0 !important;
    }
    <?php endif; ?>
</style>


<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-header d-flex align-items-start justify-content-between p-3 border-bottom">
        <div class="sidebar-brand-wrapper">
            <a href="<?php echo BASE_URL; ?>/account/home" id="sidebar-logo-link" class="d-flex align-items-center text-dark text-decoration-none">
                <img src="<?php echo BASE_URL; ?>/assets/img/logo/logo.webp" alt="Serbisyos" class="fs-5 fw-bold sidebar-brand-text" style="width: 150px; height: 50px;">
            </a>
        </div>

        <button id="desktop-toggle-btn" class="btn d-none d-lg-block" style="outline: none !important; box-shadow: none !important; border: none; background: none;">
            <i class="bi bi-list"></i>
        </button>

        <button id="close-sidebar-btn" class="btn d-lg-none" style="outline: none !important; box-shadow: none !important; border: none; background: none;">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="flex-grow-1 overflow-y-auto sidebar-scroll">
        <ul class="nav nav-pills flex-column p-2">
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>/account/home" id="home-sidebar-link" class="nav-link <?php echo $is_home_active ? 'active' : ''; ?> d-flex align-items-center">
                    <i class="bi bi-house-door"></i> <span class="sidebar-text">Home</span>
                </a>
            </li>

            <li class="nav-item mt-3 sidebar-section-header">
                <div class="sidebar-section-label text-uppercase small fw-bold text-muted px-3 mb-1">Services</div>
            </li>
            <li class="nav-item mt-1">
                <a href="<?php echo BASE_URL; ?>/account/service" class="nav-link <?php echo (strpos($request_uri, '/service.php') !== false) ? 'active' : ''; ?> d-flex align-items-center">
                    <i class="bi bi-tools"></i><span class="sidebar-text">Shop Services</span>
                </a>
            </li>
            <li class="nav-item mt-1">
                <a href="#servicesSubmenu" id="services-nav-link" data-bs-toggle="collapse" class="nav-link d-flex align-items-center justify-content-between <?php echo ($show_services_icon_active ? 'active' : ''); ?>" aria-expanded="<?php echo ($show_services_icon_active) ? 'true' : 'false'; ?>">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-gear"></i> <span class="sidebar-text"> Service Providers</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar-text dropdown-indicator"></i>
                </a>
                <ul class="collapse list-unstyled <?php echo ($show_services_icon_active) ? 'show' : ''; ?>" id="servicesSubmenu">
                    <li><a href="<?php echo BASE_URL; ?>/account/booking-provider" class="nav-link sub-link <?php echo (strpos($request_uri, '/booking-provider.php') !== false) ? 'active' : ''; ?>"><span class="sidebar-text">Booking Providers</span></a></li>
                    <li><a href="<?php echo BASE_URL; ?>/account/emergency-provider" class="nav-link sub-link <?php echo (strpos($request_uri, '/emergency-provider.php') !== false) ? 'active' : ''; ?>"><span class="sidebar-text">Emergency Providers</span></a></li>
                </ul>
            </li>
            <li class="nav-item mt-1">
                <a href="<?php echo BASE_URL; ?>/account/emergency-help" class="nav-link d-flex align-items-center <?php echo (strpos($request_uri, '/emergency-help.php') !== false) ? 'active' : ''; ?>">
                    <i class="bi bi-car-front-fill"></i> <span class="sidebar-text">Emergency Auto Help</span>
                </a>
            </li>

            <li class="nav-item mt-3 sidebar-section-header">
                <div class="sidebar-section-label text-uppercase small fw-bold text-muted px-3 mb-1">My Activities</div>
            </li>
            <?php if ($user['profile_type'] === 'user') : ?>
                <li class="nav-item mt-1">
                    <a href="<?php echo BASE_URL; ?>/account/my-booking" class="nav-link d-flex align-items-center <?php echo (strpos($request_uri, '/my-booking.php') !== false) ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-check"></i> <span class="sidebar-text">My Booking</span>
                    </a>
                </li>
                <li class="nav-item mt-1">
                    <a href="<?php echo BASE_URL; ?>/account/my-emergency-request" class="nav-link d-flex align-items-center <?php echo (strpos($request_uri, '/my-emergency-request.php') !== false) ? 'active' : ''; ?>">
                        <i class="bi bi-exclamation-triangle"></i> <span class="sidebar-text">My Emergency Request</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item mt-1">
                <a href="<?php echo BASE_URL; ?>/account/save-shops" class="nav-link d-flex align-items-center <?php echo (strpos($request_uri, '/save-shops.php') !== false) ? 'active' : ''; ?>">
                    <i class="bi bi-bookmark"></i> <span class="sidebar-text">My Saved Shops</span>
                </a>
            </li>

            <?php if ($user['profile_type'] === 'owner') : ?>
                <?php
                if (isset($user['id'])) {
                    $notificationCounts = getNotificationCounts($conn, $user['id'], $user['profile_type']);
                    $new_bookings = $notificationCounts['owner_bookings'];
                    $new_emergencies = $notificationCounts['owner_emergencies'];
                } else {
                    $new_bookings = 0;
                    $new_emergencies = 0;
                }
                ?>
                <li class="nav-item mt-3 sidebar-section-header">
                    <div class="sidebar-section-label text-uppercase small fw-bold text-muted px-3 mb-1">Shop Management</div>
                </li>
                <li class="nav-item mt-1">
                    <a href="<?php echo BASE_URL; ?>/account/booking" class="nav-link d-flex align-items-center justify-content-between <?php echo (strpos($request_uri, '/booking.php') !== false && strpos($request_uri, '/booking-provider.php') === false) ? 'active' : ''; ?>">
                        <div>
                            <i class="bi bi-calendar-plus"></i>
                            <span class="sidebar-text">Booking</span>
                        </div>
                        <?php if ($new_bookings > 0) : ?>
                            <span class="badge bg-danger rounded-pill"><?php echo $new_bookings; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item mt-1">
                    <a href="<?php echo BASE_URL; ?>/account/emergency-request" class="nav-link d-flex align-items-center justify-content-between <?php echo (strpos($request_uri, '/emergency-request.php') !== false) ? 'active' : ''; ?>">
                        <div>
                            <i class="bi bi-plus-circle"></i>
                            <span class="sidebar-text">Emergency Requests</span>
                        </div>
                        <?php if ($new_emergencies > 0) : ?>
                            <span class="badge bg-danger rounded-pill"><?php echo $new_emergencies; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item mt-3 sidebar-section-header">
                <div class="sidebar-section-label text-uppercase small fw-bold text-muted px-3 mb-1">Communication</div>
            </li>
            <li class="nav-item mt-1">
                <a href="#messagesSubmenu" id="messages-nav-link" data-bs-toggle="collapse" class="nav-link d-flex align-items-center justify-content-between <?php echo ($show_messages_icon_active ? 'active' : ''); ?>" aria-expanded="<?php echo ($show_messages_icon_active) ? 'true' : 'false'; ?>">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-chat-dots"></i> <span class="sidebar-text">Conversations</span>
                    </div>
                    <div class="d-flex align-items-center position-relative">
                        <i class="bi bi-chevron-down sidebar-text dropdown-indicator"></i>
                    </div>
                </a>
                <ul class="collapse list-unstyled <?php echo ($show_messages_icon_active) ? 'show' : ''; ?>" id="messagesSubmenu">
                    <li><a href="<?php echo BASE_URL; ?>/account/chatbot" class="nav-link sub-link <?php echo (strpos($request_uri, '/chatbot.php') !== false) ? 'active' : ''; ?>"><span class="sidebar-text">Chatbot</span></a></li>
                    <li><a href="<?php echo BASE_URL; ?>/account/inbox" class="nav-link sub-link <?php echo (strpos($request_uri, '/inbox.php') !== false) ? 'active' : ''; ?>"><span class="sidebar-text">Inbox</span></a></li>
                </ul>
            </li>

            <li class="nav-item mt-3 sidebar-section-header">
                <div class="sidebar-section-label text-uppercase small fw-bold text-muted px-3 mb-1">Partnership</div>
            </li>
            <li class="nav-item mt-1">
                <a href="<?php echo BASE_URL; ?>/account/become-a-partner" class="nav-link d-flex align-items-center <?php echo (strpos($request_uri, '/become-a-partner.php') !== false) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-handshake partner-icon" style="font-weight: lighter; font-size: 14px;"></i>
                    <span class="sidebar-text">Become A Partner</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div id="overlay" class="overlay"></div>
<header id="page-header" class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <button id="mobile-open-btn" class="btn me-2 d-lg-none"><i class="bi bi-list fs-5"></i></button>
            <div class="header-greeting d-none d-sm-block">
                <h5 class="mb-0 fw-bold"><?php echo $greeting; ?>!</h5>
            </div>
        </div>
        <div class="ms-auto d-flex align-items-center">
            <a href="<?php echo BASE_URL; ?>/account/search" class="icon-container nav-link text-secondary me-1">
                <i class="bi bi-search fs-5"></i>
            </a>

            <a href="<?php echo BASE_URL; ?>/account/inbox" class="nav-link icon-container text-secondary me-1">
                <i class="bi bi-chat-dots fs-5"></i>
                <?php if ($unread_count > 0) : ?>
                    <span class="icon-badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>

            <a href="<?php echo BASE_URL; ?>/account/notification" class="icon-container nav-link text-secondary me-1">
                <i class="bi bi-bell fs-5"></i>
                <?php include './backend/notification-count-badge.php'; ?>
                <?php if ($show_badge && $notification_count > 0) : ?>
                    <span class="icon-badge"><?php echo $notification_count; ?></span>
                <?php endif; ?>
            </a>

            <div class="dropdown">
                <a href="#" class="d-block link-dark text-decoration-none" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $profile_image_source; ?>" alt="<?php echo $user['profile_type'] === 'owner' ? 'Shop Logo' : 'User Avatar'; ?>" width="32" height="32" class="rounded-circle">
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-small shadow p-2" aria-labelledby="dropdownUser">
                    <?php if ($user['profile_type'] == 'user') : ?>
                    <li>
                        <a class="dropdown-item" href="<?php echo BASE_URL; ?>/account/profile?name=<?php echo urlencode($user['fullname']); ?>">
                            <i class="bi bi-person"></i><span>View Profile</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <hr class="dropdown-divider">
                    </li>
                    <?php if ($user['profile_type'] == 'owner') : ?>
                        <li class="mt-1"><a class="dropdown-item dropdown-item-highlight" href="<?php echo BASE_URL; ?>/account/manage-shop"><i class="bi bi-shop-window"></i><span>Manage Shop</span></a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/account/settings-and-privacy"><i class="bi bi-gear"></i><span>Settings & Privacy</span></a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/account/feedback"><i class="bi bi-envelope"></i> Feedback</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/account/about"><i class="bi bi-info-circle"></i> About Us</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/account/need-help"><i class="bi bi-question-circle"></i> Need Help? </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#signOutModal"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const pageHeader = document.getElementById('page-header');
        const mobileOpenBtn = document.getElementById('mobile-open-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');
        const desktopToggleBtn = document.getElementById('desktop-toggle-btn');
        const overlay = document.getElementById('overlay');
        const servicesNavLink = document.getElementById('services-nav-link');
        const servicesSubmenu = document.getElementById('servicesSubmenu');
        const messagesNavLink = document.getElementById('messages-nav-link');
        const messagesSubmenu = document.getElementById('messagesSubmenu');

        const isHomePage = <?php echo $is_home_active ? 'true' : 'false'; ?>;
        const isCollapsePage = <?php echo $show_messages_icon_active ? 'true' : 'false'; ?>;

        const isMobileScreen = () => window.innerWidth < 992;

        const closeAllDropdowns = () => {
            [servicesSubmenu, messagesSubmenu].forEach(submenu => {
                if (submenu) {
                    const bsCollapse = bootstrap.Collapse.getInstance(submenu);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });
        };

        const updateIconActiveState = () => {
            if (sidebar.classList.contains('collapsed')) {
                const servicesActive = document.querySelector('#servicesSubmenu .nav-link.sub-link.active');
                if (servicesActive && servicesNavLink) servicesNavLink.classList.add('active');
                const messagesActive = document.querySelector('#messagesSubmenu .nav-link.sub-link.active');
                if (messagesActive && messagesNavLink) messagesNavLink.classList.add('active');
            } else {
                if (servicesNavLink) servicesNavLink.classList.remove('active');
                if (messagesNavLink) messagesNavLink.classList.remove('active');
            }
        };

        const applyCollapsedState = () => {
            if (isMobileScreen()) return;
            sidebar.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('collapsed');
            if (pageHeader) pageHeader.classList.add('collapsed');
            closeAllDropdowns();
            setTimeout(updateIconActiveState, 300);
        };

        const applyExpandedState = () => {
            if (isMobileScreen()) return;
            sidebar.classList.remove('collapsed');
            if (mainContent) mainContent.classList.remove('collapsed');
            if (pageHeader) pageHeader.classList.remove('collapsed');
            setTimeout(updateIconActiveState, 300);
        };

        const openMobileSidebar = () => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        };

        const closeMobileSidebar = () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        };

        if (mobileOpenBtn) mobileOpenBtn.addEventListener('click', openMobileSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeMobileSidebar);
        if (overlay) overlay.addEventListener('click', closeMobileSidebar);

        if (desktopToggleBtn) {
            desktopToggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (sidebar.classList.contains('collapsed')) {
                    applyExpandedState();
                } else {
                    applyCollapsedState();
                }
            });
        }

        sidebar.addEventListener('click', (e) => {
            if (isMobileScreen()) return;

            const link = e.target.closest('a');
            if (!link || e.target.closest('#desktop-toggle-btn')) {
                return;
            }

            const isLogo = e.target.closest('#sidebar-logo-link');
            const isHome = e.target.closest('#home-sidebar-link');
            const isChatbot = link.href && link.href.includes('chatbot');
            const isInbox = link.href && link.href.includes('inbox');
            const isMessagesSubmenu = link.closest('#messagesSubmenu');
            
            const isCollapseLink = isChatbot || isInbox || isMessagesSubmenu;

            if (isCollapseLink) {
                applyCollapsedState();
            } else if (isLogo || isHome) {
                applyExpandedState();
                if (isHomePage) {
                    e.preventDefault();
                }
            } else if (link.matches('.nav-link') && !link.matches('[data-bs-toggle="collapse"]')) {
                applyExpandedState();
            }
        });

        document.addEventListener('click', (e) => {
            if (isMobileScreen()) return;

            if (!sidebar.contains(e.target)) {
                applyCollapsedState();
            }
        });

        const handleViewState = () => {
            if (isMobileScreen()) {
                sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('collapsed');
                if (pageHeader) pageHeader.classList.remove('collapsed');
                closeMobileSidebar();
            } else {
                if (isCollapsePage) {
                    applyCollapsedState();
                } else {
                    applyExpandedState();
                }
            }
            updateIconActiveState();
        };

        handleViewState();
        window.addEventListener('resize', handleViewState);

        document.querySelectorAll('.sidebar .nav-link:not([data-bs-toggle="collapse"])').forEach(link => {
            link.addEventListener('click', (event) => {
                if (isMobileScreen()) {
                    closeMobileSidebar();
                }
            });
        });

        const setupDropdownClick = (navLink, submenu) => {
            if (navLink) {
                navLink.addEventListener('click', function(event) {
                    if (sidebar.classList.contains('collapsed')) {
                        event.preventDefault();
                        applyExpandedState();
                        setTimeout(() => {
                            if (submenu) {
                                let bsCollapse = bootstrap.Collapse.getInstance(submenu);
                                if (!bsCollapse) {
                                    bsCollapse = new bootstrap.Collapse(submenu);
                                }
                                bsCollapse.show();
                            }
                        }, 250);
                    }
                    event.stopPropagation();
                });
            }
        };

        setupDropdownClick(servicesNavLink, servicesSubmenu);
        setupDropdownClick(messagesNavLink, messagesSubmenu);

        const sidebarCollapses = document.querySelectorAll('#sidebar .collapse');
        sidebarCollapses.forEach(collapseEl => {
            collapseEl.addEventListener('show.bs.collapse', () => {
                sidebarCollapses.forEach(otherCollapseEl => {
                    if (otherCollapseEl !== collapseEl) {
                        const bsCollapse = bootstrap.Collapse.getInstance(otherCollapseEl);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                });
            });
        });
    });
</script>

<script src="/assets/account/js/push-manager.js" defer></script>