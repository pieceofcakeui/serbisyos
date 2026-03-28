<?php
require_once './functions/base-path.php';
?>
<style>
  .navbar-custom {
    background: white;
    color: #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 30px;
    position: fixed;
    top: 0;
    z-index: 1000;
    width: 100%;
    box-sizing: border-box;
  }

  .logo {
    display: flex;
    align-items: center;
    flex-shrink: 0;
  }

  .logo img {
    height: 60px;
    width: auto;
    vertical-align: middle;
  }

  .nav-center {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    margin-left: 40px;
    min-width: 0;
  }

  .nav-links {
    list-style: none;
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
  }

  .nav-links li {
    margin: 0 15px;
    position: relative;
    display: flex;
    align-items: center;
    flex-shrink: 0;
  }

  .nav-links a {
    color: #333;
    text-decoration: none;
    font-size: 16px;
    padding: 8px 12px;
    transition: color 0.3s, font-size 0.3s, padding 0.3s;
    font-weight: 500;
    white-space: nowrap;
  }

  .nav-links a:hover {
    color: #ffc107;
  }

  .nav-links .active-li>a,
  .nav-link.active {
    color: #ffc107;
    font-weight: 600;
  }

  .nav-right {
    display: flex;
    align-items: center;
    flex-shrink: 0;
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

  .nav-services-dropdown {
    display: none;
    position: absolute;
    top: 40px;
    background: white;
    color: #333;
    min-width: 240px;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
  }

  .nav-services-dropdown a {
    display: block;
    padding: 12px 15px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
    transition: background 0.3s;
  }

  .nav-services-dropdown a:hover {
    background: #f5f5f5;
    color: #ffc107;
  }

  .nav-services-li.active .nav-services-dropdown {
    display: block;
  }

  .join-us {
    background-color: #ffc107;
    color: #fff;
    padding: 10px 20px;
    border-radius: 2px;
    text-decoration: none;
    display: inline-block;
    font-weight: 500;
    transition: background 0.3s ease, padding 0.3s;
    margin-left: 15px;
    white-space: nowrap;
  }

  .join-us:hover {
    background-color: #e0a800;
    color: #fff;
  }

  .nav-links .nav-icon {
    display: none;
    margin-right: 8px;
  }

  @media (max-width: 768px) {
    .nav-links .nav-icon {
      display: inline-block;
    }
  }

  .menu-toggle,
  .menu-btn,
  .search-icon-mobile,
  .sidebar-header {
    display: none;
  }

  @media (max-width: 1100px) {
    .nav-links li {
      margin: 0 8px;
    }
    .nav-links a {
      font-size: 15px;
      padding: 8px 6px;
    }
    .join-us {
      padding: 8px 15px;
    }
  }

  @media (max-width: 992px) {
    .navbar-custom {
      padding: 10px 20px;
    }
    .nav-links li {
      margin: 0 4px;
    }
     .nav-links a {
      font-size: 14px;
    }
    .search-box {
      width: 250px;
    }
  }

  @media (max-width: 768px) {
    .navbar-custom {
      padding: 15px 20px;
    }

    .nav-center {
      display: none;
    }

    .nav-search {
      display: none;
    }

    .search-icon-mobile,
    .menu-btn {
      display: block;
    }

    .menu-btn {
      margin-left: 10px;
      cursor: pointer;
      z-index: 1002;
      position: relative;
    }

    .nav-right {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .nav-right .icon-container,
    .nav-right .user-img {
      margin: 0;
    }

    .nav-right .icon-container {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .desktop-search-link {
      display: none !important;
    }

    .join-us {
      display: none;
    }

    .nav-links-left {
      position: fixed;
      top: 0;
      left: -100vw;
      width: 100vw;
      height: 100vh;
      background: #fff;
      flex-direction: column;
      padding: 0;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      transition: left 0.3s ease;
      z-index: 1001;
      align-items: flex-start;
    }

    .nav-links-left.active {
      left: 0;
    }

    .sidebar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      padding: 5px 10px !important;
      margin-bottom: -30px;
    }

    .sidebar-header .close-btn {
      font-size: 1.5em;
      cursor: pointer;
      color: #555;
      padding: 5px;
    }

    .nav-links-left li {
      margin: 0;
      width: 100%;
      flex-direction: column;
      align-items: flex-start;
    }

    .nav-links-left>li:first-of-type {
      margin-top: 40px;
    }

    .nav-links-left>li>a {
      display: block;
      width: 100%;
      padding: 15px 20px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .nav-sidebar-active .nav-right .icon-container,
    .nav-sidebar-active .nav-right .user-img,
    .nav-sidebar-active .nav-right .menu-btn {
      display: none;
    }

    .nav-services-dropdown {
      position: static;
      background: none;
      box-shadow: none;
      padding-left: 20px;
      width: 100%;
    }

    .nav-services-li.active .nav-services-dropdown {
      display: block;
    }

    .mobile-search {
      width: 100%;
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
    }

    .mobile-search .search-container {
      width: 100%;
    }

    .mobile-search .search-box {
      width: 100%;
    }

    .mobile-join-us {
      width: 100%;
      padding: 15px 20px;
    }

    .mobile-join-us .join-us {
      display: inline-block;
      margin: 0;
    }
  }
</style>
  <nav id="main-navbar" class="navbar-custom">
    <a href="<?php echo BASE_URL; ?>/home" class="logo">
      <img src="<?php echo BASE_URL; ?>/assets/img/logo/logo.webp" alt="My Logo">
    </a>

    <div class="nav-center">
      <ul class="nav-links">
        <li>
          <a href="<?php echo BASE_URL; ?>/home" class="nav-link <?= basename($_SERVER['PHP_SELF']) == '/home' ? 'active' : '' ?>">
            Home
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>/about" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">
            About Us
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>/contact" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">
            Contact Us
          </a>
        </li>
        <li class="nav-services-li" id="nav-services-toggle">
          <a class="services-toggle-link <?= basename($_SERVER['PHP_SELF']) == '/service' ? 'active' : '' ?>">
            Services <i class="fas fa-caret-down"></i>
          </a>
          <div class="nav-services-dropdown">
            <a href="<?php echo BASE_URL; ?>/service">All Services</a>
            <a href="<?php echo BASE_URL; ?>/booking-provider">Booking Providers</a>
            <a href="<?php echo BASE_URL; ?>/emergency-provider">Emergency Providers</a>
          </div>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>/become-a-partner">
            Become a Partner
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>/search">
            Search
          </a>
        </li>
      </ul>
    </div>

    <div class="nav-right">
      <a href="<?php echo BASE_URL; ?>/search" class="icon-container search-icon-mobile"><i class="fas fa-search"></i></a>
      <a href="<?php echo BASE_URL; ?>/login" class="join-us">Join Us</a>
      <label for="menu-toggle" class="menu-btn" id="hamburger-menu">
        <i class="fas fa-bars"></i>
      </label>
    </div>
  </nav>
</div>

<ul class="nav-links nav-links-left" id="mobile-sidebar">
  <div class="sidebar-header">
    <a href="<?php echo BASE_URL; ?>/home" class="logo">
      <img src="<?php echo BASE_URL; ?>/assets/img/logo/logo.webp" alt="My Logo">
    </a>
    <i class="fas fa-xmark close-btn" id="sidebar-close-btn"></i>
  </div>

  <li>
    <a href="<?php echo BASE_URL; ?>/home" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : '' ?>">
      <i class="fas fa-home nav-icon"></i> Home
    </a>
  </li>
  <li>
    <a href="<?php echo BASE_URL; ?>/about" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">
      <i class="fas fa-info-circle nav-icon"></i> About Us
    </a>
  </li>
  <li>
    <a href="<?php echo BASE_URL; ?>/contact" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">
      <i class="fas fa-envelope nav-icon"></i> Contact Us
    </a>
  </li>
  <li class="nav-services-li" id="nav-services-toggle-mobile">
    <a  class="services-toggle-link <?= basename($_SERVER['PHP_SELF']) == 'service.php' ? 'active' : '' ?>">
      <i class="fas fa-tools nav-icon"></i> Services <i class="fas fa-caret-down"></i>
    </a>
    <div class="nav-services-dropdown">
      <a href="<?php echo BASE_URL; ?>/service">All Services</a>
      <a href="<?php echo BASE_URL; ?>/booking-provider">Booking Providers</a>
      <a href="<?php echo BASE_URL; ?>/emergency-provider">Emergency Providers</a>
    </div>
  </li>
  <li>
    <a href="<?php echo BASE_URL; ?>/become-a-partner">
      <i class="fas fa-handshake nav-icon"></i> Become a Partner
    </a>
  </li>
  
<li class="mobile-join-us">
  <a href="<?php echo BASE_URL; ?>/login" class="join-us" style="color: #000000;">
    <i class="fas fa-user-plus nav-icon" style="color: #000000; margin-right: 8px;"></i>
    Join Us
  </a>
</li>

</ul>

<input type="checkbox" id="menu-toggle" class="menu-toggle">

<script>
  document.addEventListener('DOMContentLoaded', function() {
    try {
      const currentPage = window.location.pathname.split("/").pop() || "/home";
      const navLinks = document.querySelectorAll('.nav-links-left a');
      navLinks.forEach(link => {
        const linkPage = link.getAttribute('href').split("/").pop() || "/home";
        if (currentPage === linkPage) {
          const parentLi = link.closest('li');
          if (parentLi) parentLi.classList.add('active-li');
          const dropdown = link.closest('.nav-services-dropdown');
          if (dropdown) dropdown.closest('.nav-services-li').classList.add('active-li');
        }
      });
    } catch (e) {
      console.error("Error setting active link:", e);
    }

    const hamburgerMenu = document.getElementById('hamburger-menu');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const mainNavbar = document.getElementById('main-navbar');
    const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
    const servicesDropdownToggle = document.getElementById('nav-services-toggle');
    const servicesDropdownToggleMobile = document.getElementById('nav-services-toggle-mobile');
    const userToggle = document.getElementById('user-toggle');

    hamburgerMenu.addEventListener('click', function(event) {
      event.preventDefault();
      mobileSidebar.classList.toggle('active');
      mainNavbar.classList.toggle('nav-sidebar-active');
      if (servicesDropdownToggle) servicesDropdownToggle.classList.remove('active');
      if (servicesDropdownToggleMobile) servicesDropdownToggleMobile.classList.remove('active');
      if (userToggle) userToggle.classList.remove('active');
    });

    sidebarCloseBtn.addEventListener('click', function(event) {
      event.preventDefault();
      mobileSidebar.classList.remove('active');
      mainNavbar.classList.remove('nav-sidebar-active');
    });

    if (servicesDropdownToggle) {
      const servicesLink = servicesDropdownToggle.querySelector('.services-toggle-link');
      servicesDropdownToggle.addEventListener('mouseenter', function() {
        if (window.innerWidth > 768) {
          this.classList.add('active');
          if (userToggle) userToggle.classList.remove('active');
        }
      });
      servicesDropdownToggle.addEventListener('mouseleave', function() {
        if (window.innerWidth > 768) this.classList.remove('active');
      });
      servicesDropdownToggle.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
          if (event.target === servicesLink || servicesLink.contains(event.target)) event.preventDefault();
          this.classList.toggle('active');
          if (userToggle) userToggle.classList.remove('active');
        }
      });
    }

    if (servicesDropdownToggleMobile) {
      const servicesLinkMobile = servicesDropdownToggleMobile.querySelector('.services-toggle-link');
      servicesDropdownToggleMobile.addEventListener('click', function(event) {
        if (event.target === servicesLinkMobile || servicesLinkMobile.contains(event.target)) event.preventDefault();
        this.classList.toggle('active');
      });
    }

    if (userToggle) {
      userToggle.addEventListener('click', function(event) {
        const userDropdown = this.querySelector('.user-dropdown');
        const isClickInsideDropdown = userDropdown && userDropdown.contains(event.target);
        if (isClickInsideDropdown) {
          const clickedLink = event.target.closest('a');
          if (clickedLink && clickedLink.getAttribute('href') && clickedLink.getAttribute('href') !== '#') return;
          else if (clickedLink && clickedLink.getAttribute('href') === '#') {
            event.preventDefault();
            return;
          }
        }
        if (!isClickInsideDropdown) {
          event.preventDefault();
          this.classList.toggle('active');
          if (servicesDropdownToggle) servicesDropdownToggle.classList.remove('active');
        }
      });
    }

    document.addEventListener('click', function(event) {
      if (userToggle && !userToggle.contains(event.target)) userToggle.classList.remove('active');
      if (window.innerWidth > 768) {
        if (servicesDropdownToggle && !servicesDropdownToggle.contains(event.target)) servicesDropdownToggle.classList.remove('active');
      }
    });

    const searchBoxes = document.querySelectorAll('.search-box');
    const searchButtons = document.querySelectorAll('.search-btn');
    searchButtons.forEach(button => {
      button.addEventListener('click', function(event) {
        event.preventDefault();
        const searchBox = this.parentNode.querySelector('.search-box');
        const searchTerm = searchBox.value.trim();
        if (searchTerm) window.location.href = `search.php?q=${encodeURIComponent(searchTerm)}`;
      });
    });
    searchBoxes.forEach(searchBox => {
      searchBox.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          const searchTerm = this.value.trim();
          if (searchTerm) window.location.href = `search.php?q=${encodeURIComponent(searchTerm)}`;
        }
      });
    });
  });
</script>