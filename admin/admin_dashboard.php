<?php
require_once '../config/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Debug logging
error_log("Admin Dashboard - Session data: " . print_r($_SESSION, true));

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    error_log("Admin access denied - Redirecting to login");
    header("Location: ../user/login.php");
    exit();
}

$isLoginPage = false; // Flag to handle paths in navbar
$isAdminPage = true; // Flag to indicate admin page

// Check for success message
$success_message = '';
if (isset($_SESSION['login_success'])) {
    $success_message = $_SESSION['welcome_message'];
    unset($_SESSION['login_success']);
    unset($_SESSION['welcome_message']);
}

// Fetch dashboard statistics
try {
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
    $total_users = $stmt->fetch()['total_users'];

    // Total bookings
    $stmt = $pdo->query("SELECT COUNT(*) as total_bookings FROM bookings");
    $total_bookings = $stmt->fetch()['total_bookings'];

    // Pending bookings
    $stmt = $pdo->query("SELECT COUNT(*) as pending_bookings FROM bookings WHERE status = 'Pending'");
    $pending_bookings = $stmt->fetch()['pending_bookings'];

    // Recent bookings
    $stmt = $pdo->query("
        SELECT b.*, u.fullname as user_fullname 
        FROM bookings b 
        LEFT JOIN users u ON b.user_id = u.id 
        ORDER BY b.created_at DESC 
        LIMIT 5
    ");
    $recent_bookings = $stmt->fetchAll();

    // Monthly booking statistics
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(booking_date, '%M %Y') as month,
               COUNT(*) as total
        FROM bookings
        GROUP BY DATE_FORMAT(booking_date, '%Y-%m')
        ORDER BY booking_date DESC
        LIMIT 6
    ");
    $monthly_stats = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bappa Tours and Travels</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Times, serif;
        }

        body {
            min-height: 100vh;
            background: #f5f7fa;
            padding-top: 70px; /* Adjust for fixed navbar */
        }

        /* Dashboard-specific styles */
        :root {
            --primary-color: #54880e;
            --primary-light: rgba(84, 136, 14, 0.1);
            --text-color: #333;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .welcome-header {
            background: linear-gradient(45deg, #54880e, #2e4d08);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .welcome-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .welcome-header p {
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stats-grid a {
            text-decoration: none;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            color: #54880e;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            text-decoration: none;
        }

        .stat-card p {
            color: #666;
            font-size: 0.9rem;
        }

        .stat-card i {
            font-size: 2rem;
            color: #54880e;
            margin-bottom: 1rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .chart-card h2 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .popular-cars {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .popular-cars h2 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .car-list {
            list-style: none;
        }

        .car-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem;
            border-bottom: 1px solid #eee;
        }

        .car-item:last-child {
            border-bottom: none;
        }

        .car-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .car-count {
            background: #54880e;
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 15px;
            font-size: 0.9rem;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-container {
                padding: 0 15px;
                margin: 1rem auto;
            }

            .welcome-header {
                padding: 1.5rem;
            }

            .welcome-header h1 {
                font-size: 1.5rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            body {
                padding-top: 60px;
            }
            
            .dashboard-card {
                padding: 1.2rem;
            }
            
            .dashboard-card h3 {
                font-size: 1.1rem;
            }
            
            .booking-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
            }
        }
        
        @media (max-width: 480px) {
            .welcome-header {
                padding: 1rem;
            }
            
            .welcome-header h1 {
                font-size: 1.3rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .booking-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .booking-meta {
                width: 100%;
                display: flex;
                justify-content: space-between;
            }
            
            .stats-chart {
                height: 150px;
            }
            
            .month {
                font-size: 0.7rem;
            }
            
            .value {
                font-size: 0.7rem;
            }
            
            .stat-icon {
                width: 50px;
                height: 50px;
            }
            
            .stat-icon i {
                font-size: 20px;
            }
            
            .number {
                font-size: 1.5rem;
            }
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            font-size: 24px;
            color: var(--primary-color);
        }

        .stat-details h3 {
            color: var(--text-color);
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .dashboard-card h3 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }

        .booking-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .booking-item:last-child {
            border-bottom: none;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        /* View booking button styles */
        .view-booking-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background-color: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .view-booking-btn:hover {
            background-color: #3a5e0a;
            transform: translateY(-2px);
        }

        .view-booking-btn i {
            font-size: 0.8rem;
        }

        /* Empty bookings style */
        .empty-bookings {
            padding: 20px;
            text-align: center;
            color: #666;
        }

        /* View all bookings link */
        .view-all-link-container {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .view-all-link {
            display: inline-block;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .view-all-link:hover {
            color: #3a5e0a;
            text-decoration: underline;
        }

        /* Responsive styles for booking items */
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .booking-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
            }
        }
        
        @media (max-width: 576px) {
            .booking-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .booking-meta {
                width: 100%;
                justify-content: space-between;
            }
        }

        .stats-chart {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            height: 200px;
            padding: 1rem 0;
        }

        .stat-bar {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .bar {
            width: 100%;
            background: var(--primary-color);
            border-radius: 5px;
            transition: height 0.3s ease;
        }

        .month {
            font-size: 0.75rem;
            color: var(--text-color);
            writing-mode: vertical-rl;
            text-orientation: mixed;
        }

        .value {
            font-size: 0.875rem;
            color: var(--primary-color);
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <?php if ($success_message): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Login Successful!',
            text: '<?php echo $success_message; ?>',
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    <?php endif; ?>

    <script>
        // Adjust body padding when hamburger menu is toggled
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const adminNavLinks = document.getElementById('adminNavLinks');
            
            if (menuToggle && adminNavLinks) {
                const bodyElement = document.body;
                const defaultPadding = parseInt(window.getComputedStyle(bodyElement).paddingTop);
                
                // Function to check and update body padding
                function updateBodyPadding() {
                    if (window.innerWidth <= 768 && adminNavLinks.classList.contains('active')) {
                        // Calculate the navbar height plus the navigation menu height
                        const navbarHeight = document.querySelector('.admin-navbar').offsetHeight;
                        const navLinksHeight = adminNavLinks.offsetHeight;
                        bodyElement.style.paddingTop = (navbarHeight + 10) + 'px';
                    } else {
                        bodyElement.style.paddingTop = defaultPadding + 'px';
                    }
                }
                
                // Listen for menu toggle clicks
                menuToggle.addEventListener('click', function() {
                    // Wait for the class to be toggled and transitions to complete
                    setTimeout(updateBodyPadding, 10);
                });
                
                // Listen for window resize
                window.addEventListener('resize', updateBodyPadding);
                
                // Initial check
                updateBodyPadding();
            }
        });
    </script>

    <div class="dashboard-container">
        <div class="welcome-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? $_SESSION['admin_fullname'] ?? 'Admin'); ?></h1>
            <p>Manage your travel business from here</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <h3>Total Users</h3>
                    <div class="number"><?php echo $total_users; ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-details">
                    <h3>Total Bookings</h3>
                    <div class="number"><?php echo $total_bookings; ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-details">
                    <h3>Pending Bookings</h3>
                    <div class="number"><?php echo $pending_bookings; ?></div>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Recent Bookings -->
            <div class="dashboard-card">
                <h3><i class="fas fa-list"></i> Recent Bookings</h3>
                <div class="booking-list">
                    <?php if(empty($recent_bookings)): ?>
                        <div class="empty-bookings">
                            <p>No recent bookings found</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_bookings as $booking): ?>
                            <div class="booking-item">
                                <div class="booking-info">
                                    <strong><?php echo htmlspecialchars($booking['user_fullname']); ?></strong>
                                    <span><?php echo htmlspecialchars($booking['car_name']); ?></span>
                                </div>
                                <div class="booking-meta">
                                    <span class="date"><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></span>
                                    <span class="status status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                    <a href="admin_bookings.php?id=<?php echo $booking['id']; ?>" class="view-booking-btn">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="view-all-link-container">
                            <a href="admin_bookings.php" class="view-all-link">View All Bookings</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Monthly Statistics -->
            <div class="dashboard-card">
                <h3><i class="fas fa-chart-bar"></i> Monthly Statistics</h3>
                <div class="stats-chart">
                    <?php foreach ($monthly_stats as $stat): ?>
                        <div class="stat-bar">
                            <div class="bar" style="height: <?php echo ($stat['total'] / $total_bookings) * 100; ?>%"></div>
                            <span class="month"><?php echo $stat['month']; ?></span>
                            <span class="value"><?php echo $stat['total']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 