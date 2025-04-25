<?php
require_once '../config/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Fetch admin details
try {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching admin details: " . $e->getMessage());
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        if (password_verify($current_password, $admin['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['admin_id']]);
                $_SESSION['success_message'] = "Password updated successfully!";
            } else {
                $_SESSION['error_message'] = "New passwords do not match!";
            }
        } else {
            $_SESSION['error_message'] = "Current password is incorrect!";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating password!";
        error_log("Password update error: " . $e->getMessage());
    }
    header("Location: admin_setting.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - Bappa Tours and Travels</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #54880e;
            --primary-light: rgba(84, 136, 14, 0.1);
            --text-color: #333;
            --text-light: #666;
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
            min-height: 100vh;
        }

        .settings-container {
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
        }
        
        .page-header h1 {
            font-size: 1.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .settings-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .settings-card h2 {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.2rem;
        }

        .profile-info {
            margin-bottom: 1.5rem;
        }

        .info-group {
            margin-bottom: 1rem;
        }

        .info-group label {
            display: block;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .info-group p {
            color: var(--text-color);
            font-weight: 500;
            padding: 0.5rem 0;
            word-break: break-word;
        }

        .password-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .submit-btn {
            background: var(--primary-color);
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #446f0b;
            transform: translateY(-2px);
        }

        .activity-list {
            list-style: none;
            padding: 0;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .activity-details {
            flex: 1;
        }

        .activity-time {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Responsive styles */
        @media (max-width: 1024px) {
            .settings-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 1.2rem;
            }
            
            .page-header {
                padding: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .settings-card {
                padding: 1.2rem;
            }
        }
        
        @media (max-width: 768px) {
            .settings-container {
                padding: 0 15px;
                margin: 1.5rem auto;
            }

            .settings-grid {
                grid-template-columns: 1fr;
                gap: 1.2rem;
            }
            
            .settings-card h2 {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 480px) {
            .page-header {
                padding: 1.2rem;
            }
            
            .page-header h1 {
                font-size: 1.3rem;
            }
            
            .settings-card {
                padding: 1rem;
            }
            
            .form-group input {
                padding: 0.7rem 0.8rem;
                font-size: 0.95rem;
            }
            
            .submit-btn {
                padding: 0.7rem;
            }
            
            .info-group label {
                font-size: 0.85rem;
            }
            
            .info-group p {
                font-size: 0.95rem;
            }
            
            .activity-item {
                gap: 0.8rem;
            }
            
            .activity-icon {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="settings-container">
        <div class="page-header">
            <h1><i class="fas fa-cog"></i> Admin Settings</h1>
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

        <div class="settings-grid">
            <div class="settings-card">
                <h2><i class="fas fa-user-circle"></i> Profile Information</h2>
                <div class="profile-info">
                    <div class="info-group">
                        <label>Username</label>
                        <p><?php echo htmlspecialchars($admin['username']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Email</label>
                        <p><?php echo htmlspecialchars($admin['email']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Full Name</label>
                        <p><?php echo htmlspecialchars($admin['fullname']); ?></p>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h2><i class="fas fa-lock"></i> Change Password</h2>
                <form class="password-form" method="POST" onsubmit="return validatePassword()">
                    <div class="form-group">
                        <input type="password" name="current_password" placeholder="Current Password" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
                    </div>
                    <button type="submit" name="change_password" class="submit-btn">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </form>
            </div>

            <div class="settings-card">
                <h2><i class="fas fa-history"></i> Recent Activity</h2>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="activity-details">
                            <p>Last Login</p>
                            <span class="activity-time"><?php echo date('d M Y, h:i A'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validatePassword() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'New passwords do not match!'
                });
                return false;
            }
            return true;
        }
    </script>
</body>
</html> 