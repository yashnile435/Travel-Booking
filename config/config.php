<?php
// First, destroy any existing session
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Basic session configuration in php.ini style
ini_set('session.cookie_lifetime', '86400');
ini_set('session.gc_maxlifetime', '86400');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_samesite', 'Lax');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'bappa_travels');
define('DB_USER', 'root');  // Change this to your database username
define('DB_PASS', '');      // Change this to your database password

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
}

// Function to check if user is admin
function is_admin() {
    return isset($_SESSION['admin_id']);
}

// Function to redirect with message
function redirect($location, $message = '') {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: $location");
    exit();
}

// Create admin user if not exists
try {
    $stmt = $pdo->prepare("SELECT id FROM admin WHERE username = 'admin' LIMIT 1");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Create a new admin with password 'password'
        $admin_password = password_hash('password', PASSWORD_DEFAULT);
        error_log("Creating new admin account with password hash: " . $admin_password);
        
        $stmt = $pdo->prepare("INSERT INTO admin (username, password, email, fullname) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute(['admin', $admin_password, 'admin@bappatravels.com', 'Admin User']);
        
        if ($result) {
            error_log("Admin account created successfully");
        } else {
            error_log("Failed to create admin account");
        }
    } else {
        error_log("Admin account already exists");
    }
} catch (PDOException $e) {
    error_log("Error managing admin account: " . $e->getMessage());
}
?> 