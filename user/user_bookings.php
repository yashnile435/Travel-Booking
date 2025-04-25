<?php
require_once('../config/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's bookings
try {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Bappa Tours and Travels</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #54880e;
            --primary-light: rgba(84, 136, 14, 0.1);
            --text-color: #333;
            --border-color: #ddd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            line-height: 1.6;
            color: var(--text-color);
            background: #f8f9fa;
            padding-top: 80px;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .bookings-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--text-color);
            font-size: 2rem;
        }

        .bookings-list {
            display: grid;
            gap: 1.5rem;
        }

        .booking-item {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }

        .booking-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .car-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .booking-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #cce5ff;
            color: #004085;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .booking-details {
            display: grid;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .booking-timestamp {
            text-align: right;
            font-size: 0.8rem;
            color: #999;
            padding-top: 0.8rem;
            border-top: 1px solid var(--border-color);
            font-style: italic;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .detail-item i {
            color: var(--primary-color);
            width: 20px;
        }

        .no-bookings {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-bookings i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem auto;
            }

            .bookings-card {
                padding: 1.5rem;
            }

            .booking-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?php 
    include '../includes/user_navbar.php'; 
    ?>

    <div class="container">
        <div class="bookings-card">
            <h1 class="page-title">My Bookings</h1>
            
            <div class="bookings-list">
                <?php if (empty($bookings)): ?>
                    <div class="no-bookings">
                        <i class="fas fa-calendar-times"></i>
                        <h2>No Bookings Found</h2>
                        <p>You haven't made any bookings yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                        <div class="booking-item">
                            <div class="booking-header">
                                <h3 class="car-name"><?php echo htmlspecialchars($booking['car_name']); ?></h3>
                                <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo htmlspecialchars($booking['status']); ?>
                                </span>
                            </div>
                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="far fa-calendar"></i>
                                    <span>Date: <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="far fa-clock"></i>
                                    <span>Time: <?php echo date('g:i A', strtotime($booking['booking_time'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Pickup: <?php echo htmlspecialchars($booking['pickup_location']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-pin"></i>
                                    <span>Drop-off: <?php echo htmlspecialchars($booking['dropoff_location']); ?></span>
                                </div>
                            </div>
                            <div class="booking-timestamp">
                                <i class="fas fa-history"></i> Booking created on: <?php echo date('d M Y', strtotime($booking['created_at'])); ?> at <?php echo date('h:i A', strtotime($booking['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 