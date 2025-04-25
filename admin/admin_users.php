<?php
require_once '../config/config.php';
$page_title = "Manage Users";

// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Handle user status updates
if (isset($_POST['update_status'])) {
    $user_id = $_POST['user_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $user_id]);
        $_SESSION['success_message'] = "User status updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating user status: " . $e->getMessage();
    }
    
    header('Location: admin_users.php');
    exit();
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    try {
        // Start transaction
        $pdo->beginTransaction();

        // First delete all bookings associated with this user
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Then delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success_message'] = "User and associated bookings deleted successfully";
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $_SESSION['error_message'] = "Failed to delete user";
        error_log("Error deleting user and bookings: " . $e->getMessage());
    }
    header("Location: admin_users.php");
    exit();
}

// Fetch all users
try {
    $stmt = $pdo->query("
        SELECT 
            users.*, 
            COUNT(bookings.id) as total_bookings,
            MAX(bookings.booking_date) as last_booking
        FROM users 
        LEFT JOIN bookings ON users.id = bookings.user_id
        GROUP BY users.id
        ORDER BY users.created_at DESC
    ");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    $users = [];
}

// Include the admin header
include 'admin_header.php';
?>

<style>
    :root {
        --primary-color: #54880e;
        --primary-light: rgba(84, 136, 14, 0.1);
        --text-color: #333;
        --text-light: #666;
        --danger: #dc2626;
        --success: #059669;
    }

    body {
        background: #f4f6f9;
        padding-top: 60px;
    }

    .users-container {
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
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .page-header h1 {
        font-size: 1.8rem;
        margin: 0;
    }

    .users-grid {
        display: grid;
        gap: 1.5rem;
    }

    .user-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .user-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: var(--primary-color);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .user-card:hover::before {
        opacity: 1;
    }

    .user-info {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .user-avatar {
        width: 60px;
        height: 60px;
        background: var(--primary-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .user-card:hover .user-avatar {
        transform: scale(1.1);
    }

    .user-avatar i {
        font-size: 24px;
        color: var(--primary-color);
    }

    .user-details h3 {
        color: var(--text-color);
        margin-bottom: 0.5rem;
        font-size: 1.2rem;
    }

    .user-details p {
        color: var(--text-light);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.3rem;
    }

    .user-stats {
        display: flex;
        gap: 2rem;
        padding: 1rem;
        background: var(--primary-light);
        border-radius: 10px;
    }

    .stat {
        text-align: center;
        padding: 0.5rem 1rem;
        min-width: 120px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 0.2rem;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-light);
    }

    .user-actions {
        display: flex;
        gap: 0.8rem;
    }

    .action-btn {
        padding: 0.8rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .delete-btn {
        background: #fee2e2;
        color: var(--danger);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        filter: brightness(0.95);
    }

    .status-badge {
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-active {
        background: #d1fae5;
        color: var(--success);
    }

    .status-inactive {
        background: #fee2e2;
        color: var(--danger);
    }

    @media (max-width: 1024px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .user-card {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
        }

        .user-info {
            flex-direction: column;
        }

        .user-stats {
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .stat {
            min-width: auto;
            width: 100%;
        }

        .user-actions {
            width: 100%;
            justify-content: center;
        }
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

    .user-card {
        animation: slideIn 0.3s ease-out forwards;
    }
</style>

<div class="users-container">
    <div class="page-header">
        <h1>Manage Users</h1>
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

    <?php if (isset($_SESSION['error_message'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?php echo $_SESSION['error_message']; ?>',
                showConfirmButton: true
            });
        </script>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="users-grid">
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['mobile']); ?></p>
                        <span class="status-badge status-<?php echo strtolower($user['status']); ?>">
                            <?php echo $user['status']; ?>
                        </span>
                    </div>
                </div>

                <div class="user-stats">
                    <div class="stat">
                        <div class="stat-value"><?php echo $user['total_bookings']; ?></div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">
                            <?php echo $user['last_booking'] ? date('d M Y', strtotime($user['last_booking'])) : 'Never'; ?>
                        </div>
                        <div class="stat-label">Last Booking</div>
                    </div>
                </div>

                <div class="user-actions">
                    <button class="action-btn delete-btn" onclick="confirmDelete(<?php echo $user['id']; ?>)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function confirmDelete(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete user'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin_users.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_user';
                input.value = 'true';
                
                const userIdInput = document.createElement('input');
                userIdInput.type = 'hidden';
                userIdInput.name = 'user_id';
                userIdInput.value = userId;
                
                form.appendChild(input);
                form.appendChild(userIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
</body>
</html> 