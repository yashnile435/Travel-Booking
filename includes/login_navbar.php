<?php
if (!isset($isLoginPage)) {
    $isLoginPage = false;
}
?>
<nav class="navbar">
    <a href="../index.php" class="logo">
        <img src="../assets/images/logo.jpg" alt="Bappa Tours & Travels" class="logo-img">
        <span class="company-name">Bappa Tours & Travels</span>
    </a>
    <div class="navigation-menu" id="navigationMenu">
        <ul class="nav-links">
            <li><a href="../index.php" class="nav-link">Home</a></li>
            <li><a href="../index.php#cars" class="nav-link">Cars</a></li>
            <li><a href="../index.php#destinations" class="nav-link">Destinations</a></li>
            <li><a href="../index.php#about" class="nav-link">About</a></li>
            <li><a href="../index.php#contact" class="nav-link">Contact</a></li>
        </ul>
    </div>
    <button class="menu-toggle" id="menuToggle">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>
</nav>

<style>
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
}

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
}

.navigation-menu {
    display: flex;
    align-items: center;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 2rem;
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
}

/* Disable hover effect */
/*
.nav-link:hover {
    color: #54880e;
    background: rgba(84, 136, 14, 0.1);
}
*/

.menu-toggle {
    display: none;
    padding: 0;
    width: 30px;
    height: 30px;
    justify-content: center;
    align-items: center;
    border: none;
    background: transparent;
    cursor: pointer;
}

.bar {
    width: 22px;
    height: 2px;
    background: #54880e;
    margin: 2px 0;
    transition: all 0.3s ease;
}

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
        flex-direction: column;
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
        gap: 1.2rem;
    }

    .nav-link {
        width: 100%;
        text-align: center;
        padding: 0.8rem;
        font-size: 1.1rem;
    }
}

@media (max-width: 360px) {
    .company-name {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const navigationMenu = document.getElementById('navigationMenu');

    menuToggle.addEventListener('click', function() {
        navigationMenu.classList.toggle('active');
        
        // Animate hamburger menu
        const bars = this.getElementsByClassName('bar');
        for(let bar of bars) {
            bar.classList.toggle('active');
        }
    });

    // Close menu when clicking a link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            navigationMenu.classList.remove('active');
            const bars = menuToggle.getElementsByClassName('bar');
            for(let bar of bars) {
                bar.classList.remove('active');
            }
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navigationMenu.contains(e.target) && !menuToggle.contains(e.target)) {
            navigationMenu.classList.remove('active');
            const bars = menuToggle.getElementsByClassName('bar');
            for(let bar of bars) {
                bar.classList.remove('active');
            }
        }
    });
});
</script> 