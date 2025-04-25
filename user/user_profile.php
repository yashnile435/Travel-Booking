<?php
require_once('../config/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Fetch current user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $error_message = "Error fetching user data";
    error_log($e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $mobile = sanitize_input($_POST['mobile']); // Changed from 'phone' to 'mobile'
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    try {
        // If updating password
        if (!empty($current_password) && !empty($new_password)) {
            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, mobile = ?, password = ? WHERE id = ?");
                $stmt->execute([$fullname, $email, $mobile, $hashed_password, $user_id]);
            } else {
                $error_message = "Current password is incorrect";
            }
        } else {
            // Update without password change
            $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, mobile = ? WHERE id = ?");
            $stmt->execute([$fullname, $email, $mobile, $user_id]);
        }
        
        if (!$error_message) {
            $success_message = "Profile updated successfully!";
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            // Update session name if changed
            $_SESSION['fullname'] = $fullname;
        }
    } catch (PDOException $e) {
        $error_message = "Error updating profile";
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Bappa Tours and Travels</title>
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            transition: all 0.3s ease;
            border: 2px solid var(--primary-color);
        }

        .profile-avatar i {
            font-size: 3rem;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .profile-avatar:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(84, 136, 14, 0.3);
        }

        .profile-avatar:hover i {
            color: white;
        }

        .profile-title {
            font-size: 1.5rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .profile-subtitle {
            color: #666;
            font-size: 0.9rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #446f0b;
        }

        .section-title {
            font-size: 1.2rem;
            color: var(--text-color);
            margin: 2rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-light);
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem auto;
            }

            .profile-card {
                padding: 1.5rem;
            }
        }

        .profile-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 1000;
        }

        .nav-left, .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .back-btn, .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-color);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .back-btn:hover, .nav-link:hover {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        .back-btn i, .nav-link i {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .profile-navbar {
                padding: 0 1rem;
            }

            .nav-title {
                display: none;
            }

            .back-btn span, .nav-link span {
                display: none;
            }

            .back-btn, .nav-link {
                padding: 0.5rem;
            }

            .back-btn i, .nav-link i {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <?php 
    include '../includes/user_navbar.php'; 
    ?>

    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h1 class="profile-title"><?php echo htmlspecialchars($user['fullname']); ?></h1>
                <p class="profile-subtitle">Member since <?php echo date('F Y'); ?></p>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <h2 class="section-title">Personal Information</h2>
                <div class="form-group">
                    <label class="form-label" for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" 
                           value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="mobile">Mobile Number</label>
                    <input type="tel" id="mobile" name="mobile" class="form-control" 
                           value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
                </div>

                <h2 class="section-title">Change Password</h2>
                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <script>
        // Add form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const currentPassword = document.getElementById('current_password').value;

            if (newPassword || confirmPassword || currentPassword) {
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New passwords do not match!');
                    return;
                }

                if (!currentPassword) {
                    e.preventDefault();
                    alert('Please enter your current password to change password!');
                    return;
                }
            }
        });
    </script>
</body>
</html> 