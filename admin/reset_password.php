<?php
require_once('../config/config.php');

try {
    $new_password = 'password';
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_password]);
    
    if ($stmt->rowCount() > 0) {
        echo "Admin password updated successfully to 'password'";
    } else {
        echo "No admin user found";
    }
} catch (PDOException $e) {
    echo "Error updating password: " . $e->getMessage();
}
?> 