<?php
// This navbar is specifically for user-related pages like user_profile, user_bookings and booking form
?>
<nav class="navbar">
    <a href="../index.php" class="logo">
        <img src="../assets/images/logo.jpg" alt="Bappa Tours & Travels" class="logo-img">
        <span class="company-name">Bappa Tours & Travels</span>
    </a>
    <button class="menu-toggle" id="menuToggle">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </button>
    <div class="navigation-menu" id="navigationMenu">
        <ul class="nav-links">
            <li><a href="../index.php" class="nav-link">Home</a></li>
            <li><a href="../index.php#cars" class="nav-link">Cars</a></li>
            <li><a href="../index.php#destinations" class="nav-link">Destinations</a></li>
            <li><a href="../index.php#about" class="nav-link">About</a></li>
            
            <li><a href="../index.php#contact" class="nav-link">Contact</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="booking.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'booking.php' ? 'active' : ''; ?>">Book Now</a></li>
                <li class="dropdown">
                    <a href="#" class="nav-link user-dropdown-toggle">
                        <i class="fas fa-user-circle"></i> 
                        <?php echo isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : 'My Account'; ?> 
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a href="user_profile.php" class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'user_profile.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                        <a href="user_bookings.php" class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'user_bookings.php' ? 'active' : ''; ?>">
                            <i class="fas fa-calendar-check"></i> My Bookings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
/* Navbar Container */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 1.5rem;
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
    z-index: 1000;
    backdrop-filter: blur(10px);
    height: 60px;
    font-family: "Times New Roman", Times, serif;
}

/* Add primary color variable */
:root {
    --primary-color: #54880e;
    --primary-light: rgba(84, 136, 14, 0.1);
}

/* Logo Styles */
.logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    gap: 0.5rem;
}

.logo-img {
    height: 35px;
    width: auto;
    object-fit: contain;
}

.company-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: #54880e;
    white-space: nowrap;
    font-family: "Times New Roman", Times, serif;
}

/* Navigation Menu */
.navigation-menu {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 1.5rem;
    margin: 0;
    padding: 0;
}

.nav-link {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    transition: all 0.3s ease;
    font-size: 1rem;
    position: relative;
    font-family: "Times New Roman", Times, serif;
}

.nav-link:hover {
    color: #54880e;
    background: rgba(84, 136, 14, 0.1);
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: #54880e;
    transition: width 0.3s ease;
}

.nav-link:hover::after,
.nav-link.active::after {
    width: 100%;
}

.nav-link.active {
    color: #54880e;
    background: rgba(84, 136, 14, 0.1);
}

/* Mobile Menu Toggle */
.menu-toggle {
    display: none;
    flex-direction: column;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.4rem;
}

.bar {
    width: 22px;
    height: 2px;
    background: #54880e;
    transition: all 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar {
        padding: 0.4rem 1rem;
        height: 55px;
    }

    .logo-img {
        height: 30px;
    }

    .company-name {
        font-size: 1.2rem;
    }

    .menu-toggle {
        display: flex;
    }

    .navigation-menu {
        position: fixed;
        top: 55px;
        left: -100%;
        width: 100%;
        height: calc(100vh - 55px);
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        padding: 1.5rem;
        flex-direction: column;
        transition: 0.3s ease;
    }

    .navigation-menu.active {
        left: 0;
    }

    .nav-links {
        flex-direction: column;
        align-items: center;
        width: 100%;
        gap: 1rem;
    }

    .nav-link {
        width: 100%;
        text-align: center;
        padding: 0.8rem;
        font-size: 1.1rem;
    }

    .nav-link::after {
        display: none;
    }
    
    .nav-link.active {
        background: rgba(84, 136, 14, 0.15);
        color: #54880e;
        font-weight: 600;
    }
}

/* Hide company name on very small screens */
@media (max-width: 360px) {
    .company-name {
        display: none;
    }
}

/* Dropdown Menu */
.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 220px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
    display: none;
    z-index: 1000;
    margin-top: 0.5rem;
}

.dropdown-menu.show {
    display: block;
    animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #333;
    text-decoration: none;
    font-size: 1rem;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.dropdown-item.active {
    background: rgba(84, 136, 14, 0.1);
    color: #54880e;
    font-weight: 500;
}

.dropdown-item i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
    color: var(--primary-color);
}

.dropdown-divider {
    height: 1px;
    background: rgba(0, 0, 0, 0.1);
    margin: 0.5rem 0;
}

.user-dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1.2rem;
    background: var(--primary-color);
    color: white;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.user-dropdown-toggle i.fa-chevron-down {
    font-size: 0.75rem;
    transition: transform 0.2s ease;
}

.user-dropdown-toggle.active i.fa-chevron-down {
    transform: rotate(180deg);
}

/* Mobile Responsive Dropdown */
@media (max-width: 768px) {
    .dropdown-menu {
        position: static;
        width: 100%;
        box-shadow: none;
        margin-top: 0;
        background: transparent;
        padding: 0;
        border-radius: 0;
        display: none;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        padding: 0.8rem;
        text-align: center;
        justify-content: center;
    }

    .dropdown-divider {
        margin: 0.2rem 0;
    }
    
    .user-dropdown-toggle {
        width: 100%;
        justify-content: center;
        margin-top: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const navigationMenu = document.getElementById('navigationMenu');
    const dropdownToggle = document.querySelector('.user-dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    // Hamburger menu functionality
    if (menuToggle && navigationMenu) {
        menuToggle.addEventListener('click', function() {
            navigationMenu.classList.toggle('active');
            
            // Animate hamburger to X
            const bars = this.querySelectorAll('.bar');
            bars[0].style.transform = navigationMenu.classList.contains('active') 
                ? `rotate(45deg) translate(6px, 6px)` 
                : 'none';
            bars[1].style.opacity = navigationMenu.classList.contains('active') 
                ? '0' 
                : '1';
            bars[2].style.transform = navigationMenu.classList.contains('active') 
                ? `rotate(-45deg) translate(8px, -8px)` 
                : 'none';
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navigationMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                navigationMenu.classList.remove('active');
                const bars = menuToggle.querySelectorAll('.bar');
                bars[0].style.transform = 'none';
                bars[1].style.opacity = '1';
                bars[2].style.transform = 'none';
            }
        });
        
        // Close menu when clicking a link
        document.querySelectorAll('.nav-link:not(.user-dropdown-toggle)').forEach(link => {
            link.addEventListener('click', function() {
                navigationMenu.classList.remove('active');
                const bars = menuToggle.querySelectorAll('.bar');
                bars[0].style.transform = 'none';
                bars[1].style.opacity = '1';
                bars[2].style.transform = 'none';
            });
        });
    }

    // User dropdown functionality
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            dropdownMenu.classList.toggle('show');
            this.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                dropdownToggle.classList.remove('active');
            }
        });

        // Close dropdown when clicking a dropdown item
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
                dropdownToggle.classList.remove('active');
            });
        });
    }
});
</script> 