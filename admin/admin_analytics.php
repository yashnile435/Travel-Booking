<?php
require_once '../config/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Fetch analytics data
try {
    // Total Users
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE status = 'Active'");
    $totalUsers = $stmt->fetch()['total_users'];

    // Total Bookings
    $stmt = $pdo->query("SELECT COUNT(*) as total_bookings FROM bookings");
    $totalBookings = $stmt->fetch()['total_bookings'];

    // Monthly User Signups
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as signup_count
        FROM users
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6
    ");
    $monthlySignups = $stmt->fetchAll();

    // Monthly Bookings with Revenue
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(booking_date, '%Y-%m') as month,
            COUNT(*) as booking_count,
            COUNT(CASE WHEN status = 'Confirmed' THEN 1 END) as confirmed_count,
            COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_count
        FROM bookings
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6
    ");
    $monthlyBookings = $stmt->fetchAll();

    // Popular Routes
    $stmt = $pdo->query("
        SELECT 
            CONCAT(pickup_location, ' to ', dropoff_location) as route,
            COUNT(*) as trip_count
        FROM bookings
        GROUP BY pickup_location, dropoff_location
        ORDER BY trip_count DESC
        LIMIT 5
    ");
    $popularRoutes = $stmt->fetchAll();

    // Peak Booking Hours
    $stmt = $pdo->query("
        SELECT 
            HOUR(booking_time) as hour,
            COUNT(*) as booking_count
        FROM bookings
        GROUP BY HOUR(booking_time)
        ORDER BY booking_count DESC
        LIMIT 5
    ");
    $peakHours = $stmt->fetchAll();

    // Get Pending and Completed Bookings for Stats Grid
    $stmt = $pdo->query("SELECT COUNT(*) as pending_bookings FROM bookings WHERE status = 'Pending'");
    $pendingBookings = $stmt->fetch()['pending_bookings'];

    $stmt = $pdo->query("SELECT COUNT(*) as completed_bookings FROM bookings WHERE status = 'Completed'");
    $completedBookings = $stmt->fetch()['completed_bookings'];

} catch (PDOException $e) {
    error_log("Analytics Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            padding-top: 70px;
            font-family: "Times New Roman", Times, serif;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 15px;
        }

        .page-header {
            background: linear-gradient(135deg, #54880e, #2e4d08);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            font-size: 1.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2rem;
            color: #54880e;
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: #666;
            font-size: 0.9rem;
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

        .admin-logout-btn {
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

        .admin-logout-btn:hover {
            background: #446f0b;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem auto;
                padding: 0 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .admin-navbar {
                padding: 0.8rem 1rem;
                flex-wrap: wrap;
            }

            .admin-nav-links {
                order: 3;
                width: 100%;
                justify-content: space-between;
                margin-top: 1rem;
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }

            .admin-nav-link {
                font-size: 0.9rem;
                padding: 0.4rem 0.8rem;
            }

            body {
                padding-top: 120px;
            }
        }

        .routes-list {
            margin-top: 1rem;
        }

        .route-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem;
            border-bottom: 1px solid #eee;
        }

        .route-item:last-child {
            border-bottom: none;
        }

        .route-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .route-info i {
            color: var(--primary-color);
        }

        .route-count {
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.9rem;
        }

        .progress-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .progress-bar {
            flex: 1;
            height: 6px;
            background: #eee;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
<?php include 'admin_navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Website Analytics</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?php echo $totalUsers; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-check"></i>
                <h3><?php echo $totalBookings; ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?php echo $pendingBookings; ?></h3>
                <p>Pending Bookings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3><?php echo $completedBookings; ?></h3>
                <p>Completed Bookings</p>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h2><i class="fas fa-user-plus"></i> Monthly User Signups</h2>
                <canvas id="signupsChart"></canvas>
            </div>
            
            <div class="chart-card">
                <h2><i class="fas fa-chart-line"></i> Monthly Bookings</h2>
                <canvas id="bookingsChart"></canvas>
            </div>

            <div class="chart-card">
                <h2><i class="fas fa-route"></i> Popular Routes</h2>
                <div class="routes-list">
                    <?php foreach ($popularRoutes as $route): ?>
                        <div class="route-item">
                            <div class="route-info">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($route['route']); ?></span>
                            </div>
                            <div class="route-count"><?php echo $route['trip_count']; ?> trips</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="chart-card">
                <h2><i class="fas fa-clock"></i> Peak Booking Hours</h2>
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Monthly Signups Chart
        const signupsData = <?php echo json_encode(array_reverse($monthlySignups)); ?>;
        new Chart(document.getElementById('signupsChart'), {
            type: 'bar',
            data: {
                labels: signupsData.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('default', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'New Users',
                    data: signupsData.map(item => item.signup_count),
                    backgroundColor: 'rgba(84, 136, 14, 0.8)',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Monthly Bookings Chart
        const bookingsData = <?php echo json_encode(array_reverse($monthlyBookings)); ?>;
        new Chart(document.getElementById('bookingsChart'), {
            type: 'line',
            data: {
                labels: bookingsData.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('default', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Total Bookings',
                    data: bookingsData.map(item => item.booking_count),
                    borderColor: '#54880e',
                    backgroundColor: 'rgba(84, 136, 14, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Confirmed Bookings',
                    data: bookingsData.map(item => item.confirmed_count),
                    borderColor: '#059669',
                    borderDash: [5, 5],
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Peak Hours Chart
        const peakHoursData = <?php echo json_encode($peakHours); ?>;
        new Chart(document.getElementById('peakHoursChart'), {
            type: 'bar',
            data: {
                labels: peakHoursData.map(item => {
                    const hour = parseInt(item.hour);
                    return `${hour % 12 || 12}${hour < 12 ? 'AM' : 'PM'}`;
                }),
                datasets: [{
                    label: 'Bookings',
                    data: peakHoursData.map(item => item.booking_count),
                    backgroundColor: 'rgba(84, 136, 14, 0.8)',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 