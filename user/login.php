<?php
require_once('../config/config.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $login_id = isset($_POST['login_id']) ? sanitize_input($_POST['login_id']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Debug logging
    error_log("Login attempt - ID: $login_id");

    if (empty($login_id) || empty($password)) {
        $error = "Please enter both login ID and password";
    } else {
        try {
            // First check admin table
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$login_id, $login_id]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Set admin session variables
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_fullname'] = $admin['fullname'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['logged_in'] = true;

                header("Location: ../admin/admin_dashboard.php");
                exit();
            } else {
                // Check user table
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR mobile = ?");
                $stmt->execute([$login_id, $login_id]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['user_type'] = 'user';
                    $_SESSION['logged_in'] = true;
                    header("Location: user_profile.php");
                    exit();
                } else {
                    $error = "Invalid credentials!";
                }
            }
        } catch (PDOException $e) {
            $error = "Login failed. Please try again.";
            error_log("Login Error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bappa Tours and Travels</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #54880e;
            --primary-hover: #446e0b;
            --text-dark: #333;
            --text-light: #666;
            --white: #fff;
            --light-green: rgba(84,136,14,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", Times, serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #fff 0%, var(--light-green) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            padding-top: 80px;
        }

        .container {
            position: relative;
            width: 900px;
            height: 600px;
            background: var(--white);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border-radius: 20px;
            display: flex;
        }

        .img-box {
            position: relative;
            width: 50%;
            height: 100%;
            transition: 0.5s;
            background: var(--primary-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            border-radius: 20px 0 0 20px;
        }

        .img-box img {
            max-width: 80%;
            height: auto;
            transition: transform 0.5s;
            filter: brightness(1.1) contrast(1.1);
            object-fit: contain;
        }

        .company-title {
            color: white;
            font-size: 1.8rem;
            margin-top: 1.5rem;
            font-weight: bold;
            text-align: center;
        }
        .company-description{
            color: white;
            font-size: 1.5rem;
            margin-top: 1rem;
            font-weight: 500;
            text-align: center;
        }

        .form-box {
            position: relative;
            width: 50%;
            height: 100%;
            background: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            transition: 0.5s;
        }

        .form-box h2 {
            font-size: 32px;
            text-align: center;
            margin-bottom: 30px;
            color: var(--text-dark);
            font-weight: 600;
        }

        .input-box {
            position: relative;
            width: 100%;
            margin-bottom: 25px;
        }

        .input-box input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            color: var(--text-dark);
            outline: none;
            transition: 0.3s;
            padding-right: 45px;
        }

        .input-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 15px var(--light-green);
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            transition: 0.3s;
        }

        .input-box input:focus + i {
            color: var(--primary-color);
        }

        button {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .login-type {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .login-type span {
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 20px;
            transition: all 0.3s ease;
            color: var(--text-light);
        }

        .login-type span.active {
            background: var(--primary-color);
            color: white;
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
            color: var(--text-light);
        }

        .signup-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .container {
                width: 100%;
                max-width: 500px;
                height: auto;
                flex-direction: column;
            }

            .img-box {
                display: none; /* Hide the image box in responsive mode */
            }

            .form-box {
                width: 100%;
                padding: 30px 20px;
            }
        }

        /* Add these navbar styles before your existing styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }

        .logo-img {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .company-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--text-dark);
        }

        .navigation-menu {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link i {
            font-size: 1.1rem;
        }

        .nav-link.login-btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 6px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
        }

        .bar {
            width: 25px;
            height: 3px;
            background: var(--text-dark);
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .navigation-menu {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                flex-direction: column;
                background: white;
                padding: 2rem;
                transition: 0.3s;
            }

            .navigation-menu.active {
                left: 0;
            }

            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/login_navbar.php'; ?>

    <div class="container">
        <div class="img-box">
            <img src="../assets/images/logo.jpg" alt="Bappa Tours and Travels">
            <h1 class="company-title">Bappa Tours and Travels</h1>
            <h2 class="company-description">Your Trusted Travel Partner</h2>
        </div>
        <div class="form-box">
            <form method="POST" action="">
                <h2>Login</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="input-box">
                    <input type="text" name="login_id" placeholder="Email, Mobile, or Username" required>
                    <i class="fas fa-user"></i>
                </div>
                
                <div class="input-box">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
                </div>
                
                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                
                <p class="signup-link">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
