<?php
if (!isset($isAdminPage)) {
    $isAdminPage = true;
}
?>

<nav class="admin-navbar">
    <div class="admin-nav-left">
        <a href="admin_dashboard.php" class="admin-brand">
            <i class="fas fa-car"></i>
            Admin Panel
        </a>
        <button class="menu-toggle" id="menuToggle">
            <div class="toggle-bar"></div>
            <div class="toggle-bar"></div>
            <div class="toggle-bar"></div>
        </button>
        <div class="admin-nav-links" id="adminNavLinks">
            <a href="admin_dashboard.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="admin_users.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="admin_bookings.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_bookings.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i>
                <span>Bookings</span>
            </a>
            <a href="admin_analytics.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_analytics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
            <a href="manage_destinations.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_destinations.php' ? 'active' : ''; ?>">
                <i class="fas fa-map-marker-alt"></i>
                <span>Destinations</span>
            </a>
            <a href="manage_cars.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_cars.php' ? 'active' : ''; ?>">
                <i class="fas fa-car"></i>
                <span>Cars</span>
            </a>
            <a href="admin_setting.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_setting.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="../user/logout.php" class="admin-nav-link mobile-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    <div class="admin-profile">
        <div class="admin-profile-info">
            <div class="admin-name">
                <?php echo htmlspecialchars($_SESSION['admin_username'] ?? $_SESSION['admin_fullname'] ?? 'Admin'); ?>
            </div>
            <div class="admin-role">Administrator</div>
        </div>
        <a href="../user/logout.php" class="logout-btn desktop-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</nav>

<style>
    /* Admin Navbar Styles */
    .admin-navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 0.8rem 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .admin-nav-left {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .admin-brand {
        font-size: 1.4rem;
        font-weight: 600;
        color: #54880e;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .admin-nav-links {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .admin-nav-link {
        text-decoration: none;
        color: #333;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .admin-nav-link:hover,
    .admin-nav-link.active {
        background: rgba(84, 136, 14, 0.1);
        color: #54880e;
    }

    .admin-nav-link i {
        font-size: 1.1rem;
    }

    .admin-profile {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .admin-profile-info {
        text-align: right;
    }

    .admin-name {
        font-weight: 500;
        color: #333;
    }

    .admin-role {
        font-size: 0.8rem;
        color: #666;
    }

    .logout-btn {
        padding: 0.5rem 1rem;
        background: #54880e;
        color: white;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background: #446f0b;
    }
    
    /* Hide mobile logout in desktop view */
    .mobile-logout {
        display: none;
    }

    /* Hamburger menu */
    .menu-toggle {
        display: none;
        flex-direction: column;
        justify-content: space-between;
        width: 30px;
        height: 21px;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        z-index: 10;
    }

    .toggle-bar {
        width: 100%;
        height: 3px;
        background-color: #54880e;
        border-radius: 10px;
        transition: all 0.3s ease-in-out;
    }

    @media (max-width: 1024px) {
        .admin-navbar {
            padding: 0.8rem 1rem;
            flex-wrap: wrap;
        }

        .menu-toggle {
            display: flex;
            margin-left: auto;
            margin-right: 1rem;
        }

        .admin-nav-left {
            width: 100%;
            justify-content: space-between;
        }

        .admin-nav-links {
            position: fixed;
            top: 60px;
            left: -100%;
            width: 100%;
            height: auto;
            flex-direction: column;
            background: white;
            padding: 1rem;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease-in-out;
            z-index: 999;
            align-items: flex-start;
        }

        .admin-nav-links.active {
            left: 0;
        }

        .admin-nav-link {
            width: 100%;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #eee;
        }

        .admin-nav-link:last-child {
            border-bottom: none;
        }
        
        /* Show mobile logout in hamburger menu */
        .mobile-logout {
            display: flex;
            margin-top: 0.5rem;
            color: #dc3545 !important;
            border-top: 2px solid #eee;
            padding-top: 1rem !important;
        }
        
        .mobile-logout i {
            color: #dc3545;
        }
        
        .mobile-logout:hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
        }

        .admin-profile {
            margin-left: auto;
        }
        
        /* Hide desktop logout on mobile */
        .desktop-logout {
            display: none;
        }

        .admin-profile-info {
            display: none;
        }

        /* Hamburger animation */
        .menu-toggle.active .toggle-bar:nth-child(1) {
            transform: translateY(9px) rotate(45deg);
        }

        .menu-toggle.active .toggle-bar:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active .toggle-bar:nth-child(3) {
            transform: translateY(-9px) rotate(-45deg);
        }
    }

    @media (max-width: 480px) {
        .admin-nav-link span {
            display: inline-block; /* Keep text visible in hamburger menu */
        }

        .logout-btn span {
            display: none;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menuToggle');
        const adminNavLinks = document.getElementById('adminNavLinks');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                menuToggle.classList.toggle('active');
                adminNavLinks.classList.toggle('active');
            });
            
            // Close menu when clicking a link
            const navLinks = document.querySelectorAll('.admin-nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    menuToggle.classList.remove('active');
                    adminNavLinks.classList.remove('active');
                });
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.admin-nav-links') && 
                    !event.target.closest('.menu-toggle') && 
                    adminNavLinks.classList.contains('active')) {
                    menuToggle.classList.remove('active');
                    adminNavLinks.classList.remove('active');
                }
            });
        }
    });
</script> 