<?php
if (!isset($isLoginPage)) {
    $isLoginPage = false;
}
if (!isset($isBookingPage)) {
    $isBookingPage = false;
}

// Add this debug line temporarily to check session
// error_log('Session data: ' . print_r($_SESSION, true));
?>
<nav class="navbar">
    <a href="<?php echo $isLoginPage ? '../index.php' : ($isBookingPage ? '../index.php' : ($isDestinationsPage ? 'index.php' : 'index.php')); ?>" class="logo">
        <img src="<?php echo $isLoginPage || $isBookingPage ? '../assets/images/logo.jpg' : 'assets/images/logo.jpg'; ?>" alt="Logo" class="logo-img">
        <span class="company-name">Bappa Tours & Travels</span>
    </a>
    <button class="menu-toggle" id="menuToggle">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </button>
    <div class="navigation-menu" id="navigationMenu">
        <ul class="nav-links">
            <li>
                <?php if (isset($isDestinationsPage) && $isDestinationsPage): ?>
                    <a href="index.php#home" class="nav-link">Home</a>
                <?php else: ?>
                    <a href="<?php echo $isLoginPage || $isBookingPage ? '../index.php#home' : '#home'; ?>" class="nav-link" data-section="home">Home</a>
                <?php endif; ?>
            </li>
            <li>
                <?php if (isset($isDestinationsPage) && $isDestinationsPage): ?>
                    <a href="index.php#cars" class="nav-link">Cars</a>
                <?php else: ?>
                    <a href="<?php echo $isLoginPage || $isBookingPage ? '../index.php#cars' : '#cars'; ?>" class="nav-link" data-section="cars">Cars</a>
                <?php endif; ?>
            </li>
            <li>
                <?php if (isset($isDestinationsPage) && $isDestinationsPage): ?>
                    <a href="#" class="nav-link active">Destinations</a>
                <?php else: ?>
                    <a href="<?php echo $isLoginPage || $isBookingPage ? '../index.php#destinations' : '#destinations'; ?>" class="nav-link" data-section="destinations">Destinations</a>
                <?php endif; ?>
            </li>
            <li>
                <?php if (isset($isDestinationsPage) && $isDestinationsPage): ?>
                    <a href="index.php#about" class="nav-link">About</a>
                <?php else: ?>
                    <a href="<?php echo $isLoginPage || $isBookingPage ? '../index.php#about' : '#about'; ?>" class="nav-link" data-section="about">About</a>
                <?php endif; ?>
            </li>
            <li>
                <?php if (isset($isDestinationsPage) && $isDestinationsPage): ?>
                    <a href="index.php#contact" class="nav-link">Contact</a>
                <?php else: ?>
                    <a href="<?php echo $isLoginPage || $isBookingPage ? '../index.php#contact' : '#contact'; ?>" class="nav-link" data-section="contact">Contact</a>
                <?php endif; ?>
            </li>
            <li>
                <?php if (isset($isDestinationsPage) && $isDestinationsPage): ?>
                    <a href="user/booking.php" class="nav-link">Book Now</a>
                <?php else: ?>
                    <a href="<?php echo $isLoginPage || $isBookingPage ? 'booking.php' : 'user/booking.php'; ?>" class="nav-link">Book Now</a>
                <?php endif; ?>
            </li>
        </ul>
        <div class="auth-buttons">
            <?php if(isset($_SESSION['user_id']) && isset($_SESSION['logged_in'])): ?>
                <div class="profile-dropdown">
                    <button class="profile-btn">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <?php if (isset($isDestinationsPage) && $isDestinationsPage): ?>
                            <a href="user/user_profile.php">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a href="user/user_bookings.php">
                                <i class="fas fa-calendar-check"></i> My Bookings
                            </a>
                            <a href="user/logout.php" class="logout-link">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $isLoginPage || $isBookingPage ? 'user_profile.php' : 'user/user_profile.php'; ?>">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a href="<?php echo $isLoginPage || $isBookingPage ? 'user_bookings.php' : 'user/user_bookings.php'; ?>">
                                <i class="fas fa-calendar-check"></i> My Bookings
                            </a>
                            <a href="<?php echo $isLoginPage || $isBookingPage ? 'logout.php' : 'user/logout.php'; ?>" class="logout-link">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php if(!$isLoginPage): ?>
                    <?php if (isset($isDestinationsPage) && $isDestinationsPage): ?>
                        <a href="user/login.php" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $isBookingPage ? 'login.php' : './user/login.php'; ?>" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
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

.nav-link.active {
    color: #54880e;
    background: rgba(84, 136, 14, 0.1);
}

/* Auth Buttons */
.auth-buttons {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.login-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1.2rem;
    background: #54880e;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 1rem;
    font-family: "Times New Roman", Times, serif;
}

/* Profile Dropdown */
.profile-dropdown {
    position: relative;
}

.profile-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1.2rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
    font-family: "Times New Roman", Times, serif;
}

.profile-btn i {
    font-size: 1.2rem;
}

.dropdown-content {
    position: absolute;
    top: 120%;
    right: 0;
    min-width: 200px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

.profile-dropdown:hover .dropdown-content {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-content a {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.8rem 1.2rem;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

/* Disable hover effect */
/*
.dropdown-content a:hover {
    background: rgba(84, 136, 14, 0.1);
    color: var(--primary-color);
}
*/

.dropdown-content a i {
    width: 20px;
    color: var(--primary-color);
}

.logout-link {
    border-top: 1px solid #eee;
}

.logout-link, 
.logout-link i {
    color: #dc3545 !important;
}

.logout-link:hover {
    background: rgba(220, 53, 69, 0.1) !important;
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

    .auth-buttons {
        width: 100%;
        justify-content: center;
        margin-top: 1rem;
    }

    .login-btn {
        width: 100%;
        justify-content: center;
        padding: 0.8rem;
    }

    .nav-link::after {
        display: none;
    }
    
    .nav-link.active {
        background: rgba(84, 136, 14, 0.15);
        color: #54880e;
        font-weight: 600;
    }

    .profile-dropdown {
        width: 100%;
    }

    .profile-btn {
        width: 100%;
        justify-content: center;
    }

    .dropdown-content {
        position: static;
        width: 100%;
        opacity: 1;
        visibility: visible;
        transform: none;
        box-shadow: none;
        margin-top: 0.5rem;
    }

    .dropdown-content a {
        padding: 1rem;
        justify-content: center;
    }
}

/* Hide company name on very small screens */
@media (max-width: 360px) {
    .company-name {
        font-size: 1.1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const navigationMenu = document.getElementById('navigationMenu');

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
        }
    });

    // Smooth scroll functionality
    document.querySelectorAll('.nav-link[data-section]').forEach(link => {
        link.addEventListener('click', function(e) {
            const sectionId = this.getAttribute('data-section');
            
            // Only handle smooth scroll if we're on the main page
            if (!window.location.pathname.includes('login')) {
                e.preventDefault();
                const section = document.getElementById(sectionId);
                if (section) {
                    // Close mobile menu if open
                    navigationMenu.classList.remove('active');
                    document.body.classList.remove('menu-open');

                    // Calculate header height for offset
                    const headerHeight = document.querySelector('.navbar').offsetHeight;
                    
                    // Smooth scroll to section
                    window.scrollTo({
                        top: section.offsetTop - headerHeight,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Highlight active section while scrolling
    window.addEventListener('scroll', function() {
        const sections = ['home', 'cars', 'about', 'destinations', 'contact'];
        const navLinks = document.querySelectorAll('.nav-link');
        const headerHeight = document.querySelector('.navbar').offsetHeight;
        
        let currentSection = '';
        
        sections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                const sectionTop = section.offsetTop - headerHeight - 100;
                const sectionBottom = sectionTop + section.offsetHeight;
                
                if (window.scrollY >= sectionTop && window.scrollY < sectionBottom) {
                    currentSection = sectionId;
                }
            }
        });
        
        // Update active state of nav links
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-section') === currentSection) {
                link.classList.add('active');
            }
        });
    });
});
</script> 