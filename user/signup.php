<?php
session_start();
require_once '../config/config.php';

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $mobile = sanitize_input($_POST['mobile']);
    $password = $_POST['password'];

    // Validate input
    if (empty($fullname) || empty($email) || empty($mobile) || empty($password)) {
        $error = "All fields are required.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $error = "Email already registered!";
            } else {
                // Check if mobile already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE mobile = ?");
                $stmt->execute([$mobile]);
                if ($stmt->rowCount() > 0) {
                    $error = "Mobile number already registered!";
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new user
                    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, mobile, password) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$fullname, $email, $mobile, $hashed_password]);
                    
                    $_SESSION['success_message'] = "Registration successful! Please login.";
                    header("Location: login.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $error = "Registration failed. Please try again.";
            error_log($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
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
            background: #fff;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border-radius: 20px;
            display: flex;
        }

        .user {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            transition: 0.5s ease-in-out;
        }

        .img-box {
            position: relative;
            width: 50%;
            height: 100%;
            transition: 0.5s;
            background: #54880e;
            display: flex;
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
            z-index: 2;
        }

        .form-box {
            position: relative;
            width: 50%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-box form {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .form-box h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .input-box {
            position: relative;
            margin-bottom: 25px;
        }

        .input-box input {
            width: 90%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-left: 35px;
        }

        .input-box i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #aaa;
        }

        .signup-link {
            text-align: center;
            margin-top: 10px;
        }

        .signup-link a {
            color: #54880e;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        button {
            padding: 10px;
            background: #54880e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #446f0b;
        }

        .input-box input.error {
            border-color: red;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            margin-left: 35px;
        }

        /* Media queries for responsive design */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                max-width: 500px;
                height: auto;
            }

            .user {
                position: relative;
                height: auto;
            }

            .img-box {
                display: none; /* Hide image box in responsive mode */
            }

            .form-box {
                width: 100%;
                padding: 30px 20px;
            }

            .input-box input {
                width: 100%;
                margin-left: 0;
                padding-left: 40px;
            }

            .input-box i {
                left: 15px;
            }

            .error-message {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php 
    $isLoginPage = true; // Flag to handle paths in navbar
    include '../includes/navbar.php'; 
    ?>

    <div class="container">
        <div class="user signinBx">
            <div class="img-box">
                <img src="../assets/images/logo.jpg" alt="Bappa Tours and Travels">
            </div>
            <div class="form-box">
                <form method="POST" action="">
                    <h2>Signup</h2>
                    
                    <?php if ($error): ?>
                        <p style="color: red;"><?php echo $error; ?></p>
                    <?php endif; ?>

                    <div class="input-box">
                        <input type="text" name="fullname" placeholder="Full Name" required>
                        <i class="fas fa-user"></i>
                    </div>
                    
                    <div class="input-box">
                        <input type="email" name="email" placeholder="Email" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                    
                    <div class="input-box">
                        <input type="text" name="mobile" placeholder="Mobile Number" required>
                        <i class="fas fa-phone"></i>
                    </div>
                    
                    <div class="input-box">
                        <input type="password" name="password" placeholder="Password" required>
                        <i class="fas fa-lock"></i>
                    </div>
                    
                    <button type="submit">Signup</button>
                    
                    <p class="signup-link">
                        Already have an account? <a href="../user/login.php">Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const fullnameInput = document.querySelector('input[name="fullname"]');
        const emailInput = document.querySelector('input[name="email"]');
        const mobileInput = document.querySelector('input[name="mobile"]');
        const passwordInput = document.querySelector('input[name="password"]');

        // Add error message display
        function showError(input, message) {
            const errorDiv = input.parentElement.querySelector('.error-message');
            if (!errorDiv) {
                const div = document.createElement('div');
                div.className = 'error-message';
                div.style.color = 'red';
                div.style.fontSize = '12px';
                div.style.marginTop = '5px';
                div.style.marginLeft = '35px';
                input.parentElement.appendChild(div);
            }
            input.parentElement.querySelector('.error-message').textContent = message;
            input.style.borderColor = 'red';
        }

        // Clear error message
        function clearError(input) {
            const errorDiv = input.parentElement.querySelector('.error-message');
            if (errorDiv) {
                errorDiv.textContent = '';
            }
            input.style.borderColor = '#ccc';
        }

        // Validate full name
        function validateFullname() {
            const fullname = fullnameInput.value.trim();
            if (fullname.length < 3) {
                showError(fullnameInput, 'Name must be at least 3 characters long');
                return false;
            }
            if (!/^[a-zA-Z\s]+$/.test(fullname)) {
                showError(fullnameInput, 'Name should contain only letters and spaces');
                return false;
            }
            clearError(fullnameInput);
            return true;
        }

        // Validate email
        function validateEmail() {
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError(emailInput, 'Please enter a valid email address');
                return false;
            }
            clearError(emailInput);
            return true;
        }

        // Validate mobile number
        function validateMobile() {
            const mobile = mobileInput.value.trim();
            if (!/^[0-9]{10}$/.test(mobile)) {
                showError(mobileInput, 'Please enter a valid 10-digit mobile number');
                return false;
            }
            clearError(mobileInput);
            return true;
        }

        // Validate password
        function validatePassword() {
            const password = passwordInput.value;
            if (password.length < 6) {
                showError(passwordInput, 'Password must be at least 6 characters long');
                return false;
            }
            if (!/[A-Z]/.test(password)) {
                showError(passwordInput, 'Password must contain at least one uppercase letter');
                return false;
            }
            if (!/[a-z]/.test(password)) {
                showError(passwordInput, 'Password must contain at least one lowercase letter');
                return false;
            }
            if (!/[0-9]/.test(password)) {
                showError(passwordInput, 'Password must contain at least one number');
                return false;
            }
            clearError(passwordInput);
            return true;
        }

        // Real-time validation
        fullnameInput.addEventListener('input', validateFullname);
        emailInput.addEventListener('input', validateEmail);
        mobileInput.addEventListener('input', validateMobile);
        passwordInput.addEventListener('input', validatePassword);

        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Clear all previous errors first
            clearError(fullnameInput);
            clearError(emailInput);
            clearError(mobileInput);
            clearError(passwordInput);

            // Validate all fields
            if (!validateFullname()) isValid = false;
            if (!validateEmail()) isValid = false;
            if (!validateMobile()) isValid = false;
            if (!validatePassword()) isValid = false;

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Prevent non-numeric input in mobile field
        mobileInput.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>