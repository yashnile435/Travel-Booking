<?php
require_once '../config/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Handle booking status updates
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $booking_id]);
        $_SESSION['success_message'] = "Booking status updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating booking status";
        error_log($e->getMessage());
    }
    header('Location: admin_bookings.php');
    exit();
}

// Handle booking deletion
if (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $_SESSION['success_message'] = "Booking deleted successfully";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Failed to delete booking";
        error_log($e->getMessage());
    }
    header("Location: admin_bookings.php");
    exit();
}

// Fetch all bookings with user details
try {
    $stmt = $pdo->query("
        SELECT 
            b.*,
            u.fullname as user_fullname,
            u.email as user_email,
            u.mobile as user_mobile
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        ORDER BY b.created_at DESC
    ");
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching bookings: " . $e->getMessage());
    $bookings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #54880e;
            --primary-light: rgba(84, 136, 14, 0.1);
            --text-color: #333;
            --text-light: #666;
            --danger: #dc2626;
            --success: #059669;
            --warning: #eab308;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            background: #f4f6f9;
            padding-top: 60px;
        }

        .bookings-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .page-header {
            background: linear-gradient(45deg, var(--primary-color), #446f0b);
            padding: 2rem;
            border-radius: 15px;
            color: white;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 1.8rem;
            margin: 0;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .booking-id {
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
            background: #fef3c7;
            color: var(--warning);
        }

        .status-confirmed {
            background: #d1fae5;
            color: var(--success);
        }

        .status-cancelled {
            background: #fee2e2;
            color: var(--danger);
        }
        
        .status-rejected {
            background: #fee2e2;
            color: var(--danger);
        }
        
        .status-completed {
            background: #dbeafe;
            color: #3b82f6;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .detail-group h4 {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .detail-group p {
            color: var(--text-color);
            font-weight: 500;
            word-break: break-word;
            margin-bottom: 0.5rem;
        }

        .booking-actions {
            display: flex;
            justify-content: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            margin-top: 1rem;
        }

        .booking-timestamp {
            text-align: right;
            font-size: 0.8rem;
            color: #999;
            margin-top: 0.5rem;
            font-style: italic;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .status-dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 200px;
        }

        .status-toggle {
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            width: 100%;
            justify-content: center;
        }

        .status-toggle i {
            transition: transform 0.3s ease;
        }

        .status-toggle.active i {
            transform: rotate(180deg);
        }

        .status-menu {
            position: absolute;
            bottom: 100%;
            left: 0;
            z-index: 10;
            background: white;
            border-radius: 8px;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.2);
            width: 200px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .status-menu.active {
            display: flex;
            animation: fadeIn 0.2s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-option {
            padding: 0.8rem 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-option:hover {
            background: var(--primary-light);
        }

        .status-option.confirmed {
            color: var(--success);
        }

        .status-option.pending {
            color: var(--warning);
        }

        .status-option.rejected {
            color: var(--danger);
        }

        .status-option.completed {
            color: #3b82f6;
        }

        .status-option.cancelled {
            color: var(--danger);
        }

        .status-toggle:hover {
            transform: translateY(-2px);
            filter: brightness(0.95);
        }

        .status-toggle i:last-child {
            transition: transform 0.3s ease;
        }

        .status-toggle.active i:last-child {
            transform: rotate(180deg);
        }

        .action-btn:hover,
        .status-toggle:hover {
            transform: translateY(-2px);
            filter: brightness(0.95);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .booking-card {
            animation: slideIn 0.3s ease-out forwards;
        }
        
        /* Responsive styles */
        @media (max-width: 1024px) {
            .page-header {
                padding: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .booking-details {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .booking-details {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .booking-actions {
                padding-top: 1rem;
            }

            .status-dropdown {
                max-width: 100%;
            }
            
            .status-menu {
                width: 100%;
                position: absolute;
                bottom: 100%;
                left: 0;
                box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.2);
                border: 1px solid #eee;
                margin-bottom: 0.5rem;
            }
            
            .bookings-container {
                padding: 0 15px;
                margin: 1rem auto;
            }
        }
        
        @media (max-width: 576px) {
            .booking-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }
            
            .booking-card {
                padding: 1.2rem;
            }
            
            .detail-group h4 {
                font-size: 0.85rem;
            }
            
            .detail-group p {
                font-size: 0.9rem;
            }
            
            .booking-status {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
            
            .page-header {
                padding: 1.2rem;
                text-align: center;
            }
            
            /* On very small screens, make it a dropdown again to avoid going off-screen */
            .status-menu {
                bottom: auto;
                top: 100%;
                margin-bottom: 0;
                margin-top: 0.5rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }
            
            /* Adjust animation for dropdown direction */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="bookings-container">
        <div class="page-header">
            <h1>Manage Bookings</h1>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?php echo $_SESSION['success_message']; ?>',
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php foreach ($bookings as $booking): ?>
            <div class="booking-card">
                <div class="booking-header">
                    <span class="booking-id">Booking #<?php echo $booking['id']; ?></span>
                    <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                        <?php echo $booking['status']; ?>
                    </span>
                </div>

                <div class="booking-details">
                    <div class="detail-group">
                        <h4>Customer Details</h4>
                        <p><?php echo htmlspecialchars($booking['user_fullname']); ?></p>
                        <p><?php echo htmlspecialchars($booking['user_mobile']); ?></p>
                    </div>

                    <div class="detail-group">
                        <h4>Car Details</h4>
                        <p><?php echo htmlspecialchars($booking['car_name']); ?></p>
                    </div>

                    <div class="detail-group">
                        <h4>Booking Date & Time</h4>
                        <p><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></p>
                        <p><?php echo date('h:i A', strtotime($booking['booking_time'])); ?></p>
                    </div>

                    <div class="detail-group">
                        <h4>Location</h4>
                        <p>From: <?php echo htmlspecialchars($booking['pickup_location']); ?></p>
                        <p>To: <?php echo htmlspecialchars($booking['dropoff_location']); ?></p>
                    </div>
                </div>

                <div class="booking-actions">
                    <div class="status-dropdown">
                        <button class="status-toggle" id="statusToggle-<?php echo $booking['id']; ?>">
                            <i class="fas fa-tasks"></i> Update Status <i class="fas fa-chevron-up"></i>
                        </button>
                        <div class="status-menu" id="statusMenu-<?php echo $booking['id']; ?>">
                            <div class="status-option confirmed" onclick="updateStatus(<?php echo $booking['id']; ?>, 'Confirmed')">
                                <i class="fas fa-check"></i> Confirm
                            </div>
                            <div class="status-option pending" onclick="updateStatus(<?php echo $booking['id']; ?>, 'Pending')">
                                <i class="fas fa-clock"></i> Pending
                            </div>
                            <div class="status-option rejected" onclick="updateStatus(<?php echo $booking['id']; ?>, 'Rejected')">
                                <i class="fas fa-times"></i> Reject
                            </div>
                            <div class="status-option completed" onclick="updateStatus(<?php echo $booking['id']; ?>, 'Completed')">
                                <i class="fas fa-check-double"></i> Complete
                            </div>
                            <div class="status-option cancelled" onclick="updateStatus(<?php echo $booking['id']; ?>, 'Cancelled')">
                                <i class="fas fa-ban"></i> Cancel
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="booking-timestamp">
                    <i class="fas fa-history"></i> Booking created on: <?php echo date('d M Y', strtotime($booking['created_at'])); ?> at <?php echo date('h:i A', strtotime($booking['created_at'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Add event listeners for the status dropdown toggles
        document.addEventListener('DOMContentLoaded', function() {
            // Fix dropdown menu toggling
            const toggles = document.querySelectorAll('.status-toggle');
            
            toggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const id = this.id.split('-')[1];
                    const menu = document.getElementById('statusMenu-' + id);
                    
                    // Close all other menus first
                    document.querySelectorAll('.status-menu').forEach(m => {
                        if (m.id !== 'statusMenu-' + id) {
                            m.classList.remove('active');
                        }
                    });
                    
                    document.querySelectorAll('.status-toggle').forEach(t => {
                        if (t.id !== 'statusToggle-' + id) {
                            t.classList.remove('active');
                        }
                    });
                    
                    // Toggle current menu
                    menu.classList.toggle('active');
                    this.classList.toggle('active');
                });
            });
            
            // Prevent dropdown from closing when clicking on a status option
            document.querySelectorAll('.status-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
            
            // Close menus when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.status-dropdown')) {
                    document.querySelectorAll('.status-menu').forEach(menu => {
                        menu.classList.remove('active');
                    });
                    
                    document.querySelectorAll('.status-toggle').forEach(toggle => {
                        toggle.classList.remove('active');
                    });
                }
            });
        });

        function updateStatus(bookingId, status) {
            Swal.fire({
                title: 'Confirm Action',
                text: `Are you sure you want to change this booking status to ${status.toLowerCase()}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#54880e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'admin_bookings.php';
                    
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'update_status';
                    statusInput.value = 'true';
                    
                    const bookingIdInput = document.createElement('input');
                    bookingIdInput.type = 'hidden';
                    bookingIdInput.name = 'booking_id';
                    bookingIdInput.value = bookingId;
                    
                    const statusValueInput = document.createElement('input');
                    statusValueInput.type = 'hidden';
                    statusValueInput.name = 'status';
                    statusValueInput.value = status;
                    
                    form.appendChild(statusInput);
                    form.appendChild(bookingIdInput);
                    form.appendChild(statusValueInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmDelete(bookingId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'admin_bookings.php';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'delete_booking';
                    input.value = 'true';
                    
                    const bookingIdInput = document.createElement('input');
                    bookingIdInput.type = 'hidden';
                    bookingIdInput.name = 'booking_id';
                    bookingIdInput.value = bookingId;
                    
                    form.appendChild(input);
                    form.appendChild(bookingIdInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html> 