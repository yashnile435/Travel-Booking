<?php
require_once('../config/config.php');

// Debug database connection
try {
    // Check database connection
    $pdo->query("SELECT 1");
    error_log("Database connection successful");
    
    // Check if bookings table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'bookings'");
    if ($stmt->rowCount() > 0) {
        error_log("Bookings table exists");
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE bookings");
        error_log("Bookings table structure: " . print_r($stmt->fetchAll(PDO::FETCH_ASSOC), true));
    } else {
        error_log("Bookings table does not exist!");
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
}

// Debug session
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'booking.php';
    header("Location: login.php");
    exit();
}

// Fetch user data for auto-filling
try {
    $stmt = $pdo->prepare("SELECT mobile FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug POST data
    error_log("POST data received: " . print_r($_POST, true));

    // Validate inputs
    $fullname = sanitize_input($_POST['fullname']);
    $mobile = sanitize_input($_POST['mobile']);
    $car_name = sanitize_input($_POST['car_name']);
    $booking_date = sanitize_input($_POST['booking_date']);
    $booking_time = sanitize_input($_POST['booking_time']);
    $pickup_location = sanitize_input($_POST['pickup_location']);
    $dropoff_location = sanitize_input($_POST['dropoff_location']);

    // Debug sanitized data
    error_log("Sanitized data: " . print_r([
        'fullname' => $fullname,
        'mobile' => $mobile,
        'car_name' => $car_name,
        'booking_date' => $booking_date,
        'booking_time' => $booking_time,
        'pickup_location' => $pickup_location,
        'dropoff_location' => $dropoff_location
    ], true));

    // Basic validation
    $errors = [];
    if (empty($fullname)) $errors[] = "Full name is required";
    if (!preg_match("/^[0-9]{10}$/", $mobile)) $errors[] = "Invalid mobile number";
    if (empty($car_name)) $errors[] = "Please select a car";
    if (empty($booking_date)) $errors[] = "Booking date is required";
    if (empty($booking_time)) $errors[] = "Booking time is required";
    if (empty($pickup_location)) $errors[] = "Pickup location is required";
    if (empty($dropoff_location)) $errors[] = "Drop-off location is required";

    // Check if booking date is in the future and within 6 months
    $booking_datetime = new DateTime($booking_date . ' ' . $booking_time);
    $now = new DateTime();
    $max_date = (new DateTime())->modify('+6 months');
    
    if ($booking_datetime <= $now) {
        $errors[] = "Booking date and time must be in the future";
    } elseif ($booking_datetime > $max_date) {
        $errors[] = "Booking date cannot be more than 6 months in advance";
    }

    // Check if booking time is at least 1 hour from now for same-day bookings
    if ($booking_date == $now->format('Y-m-d')) {
        $min_time = (new DateTime())->modify('+1 hour');
        if ($booking_datetime < $min_time) {
            $errors[] = "For same-day bookings, time must be at least 1 hour from now";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Prepare the SQL statement
            $sql = "INSERT INTO bookings (
                user_id, fullname, mobile, car_name, 
                booking_date, booking_time, pickup_location, 
                dropoff_location, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
            
            error_log("SQL Query: " . $sql);
            
            $stmt = $pdo->prepare($sql);
            
            $params = [
                $_SESSION['user_id'],
                $fullname,
                $mobile,
                $car_name,
                $booking_date,
                $booking_time,
                $pickup_location,
                $dropoff_location
            ];
            
            error_log("Parameters: " . print_r($params, true));
            
            // Execute the statement
            $result = $stmt->execute($params);
            
            if ($result) {
                $pdo->commit();
                $success_message = "Booking submitted successfully! We will contact you shortly.";
                error_log("Booking inserted successfully. ID: " . $pdo->lastInsertId());
                $_POST = array(); // Clear form
            } else {
                throw new PDOException("Execute returned false");
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Booking failed. Please try again.";
            error_log("Booking Error Details: " . $e->getMessage());
            error_log("SQL State: " . $e->errorInfo[0]);
            error_log("Error Code: " . $e->errorInfo[1]);
            error_log("Error Message: " . $e->errorInfo[2]);
            
            // More detailed error message for debugging
            if (isset($stmt) && $stmt->errorInfo()[2]) {
                error_log("Statement Error: " . print_r($stmt->errorInfo(), true));
            }
        }
    } else {
        $error_message = implode("<br>", $errors);
        error_log("Validation errors: " . print_r($errors, true));
    }
}

// Fetch available cars for dropdown
try {
    $stmt = $pdo->prepare("SELECT car_name FROM cars WHERE is_available = 1 ORDER BY car_name");
    $stmt->execute();
    $available_cars = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $available_cars = [
        "Swift Dzire",
        "Tavera",
        "Tempo Traveller",
        "Innova Crysta",
        "Ertiga"
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Car - Bappa Tours and Travels</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #54880e;
            --primary-light: rgba(84, 136, 14, 0.1);
            --text-color: #333;
            --border-color: #ddd;
            --white: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            width: 100%;
            max-width: 1200px;
            min-height: calc(100vh - 100px);
            background: var(--white);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border-radius: 20px;
            display: flex;
        }

        .img-box {
            position: relative;
            width: 50%;
            background: var(--primary-color);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            border-radius: 20px 0 0 20px;
            overflow: hidden;
        }

        .img-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(84, 136, 14, 0.7), rgba(84, 136, 14, 0.9));
            z-index: 1;
        }

        .img-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.8;
        }

        .img-box .overlay-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
        }

        .img-box .overlay-content img {
            position: relative;
            width: 200px;
            height: auto;
            margin-bottom: 20px;
            opacity: 1;
        }

        .img-box .overlay-content h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .img-box .overlay-content p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .form-box {
            position: relative;
            width: 50%;
            height: 100%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--text-color);
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 500;
            color: white;
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn:hover {
            /* background: #90ee91; */
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 968px) {
            .container {
                flex-direction: column;
                min-height: auto;
            }

            .img-box {
                width: 100%;
                height: 300px;
                border-radius: 20px 20px 0 0;
            }

            .form-box {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }

            .img-box {
                height: 200px;
            }

            .form-box {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .img-box {
                height: 120px;
                padding: 10px;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            .img-box img {
                display: none; /* Hide the background image in small screens */
            }

            .img-box .overlay-content img {
                display: none; /* Hide the logo image in small screens */
            }
            
            .img-box .overlay-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            
            .img-box .overlay-content h2 {
                font-size: 1.5rem;
                margin-top: 0;
                margin-bottom: 5px;
            }
            
            .img-box .overlay-content p {
                font-size: 0.9rem;
            }
        }

        .mobile-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .country-code {
            position: absolute;
            left: 12px;
            color: var(--text-color);
            font-weight: 500;
        }

        .with-country-code {
            padding-left: 45px !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            min-height: 20px;
        }
    </style>
</head>
<body>
    <?php 
    include '../includes/user_navbar.php'; 
    ?>

    <div class="container">
        <div class="img-box">
            <img src="../assets/images/car-bg.jpg" alt="">
            <div class="overlay-content">
                <img src="../assets/images/logo.jpg" alt="Bappa Tours and Travels">
                <h2>Bappa Tours & Travels</h2>
                <p>Your trusted travel partner</p>
            </div>
        </div>
        <div class="form-box">
            <h1 class="page-title">Book a Car</h1>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" class="form-control" required 
                               value="<?php echo isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mobile">Mobile Number</label>
                        <div class="mobile-input-wrapper">
                            <span class="country-code">+91</span>
                            <input type="tel" 
                                   id="mobile" 
                                   name="mobile" 
                                   class="form-control with-country-code" 
                                   required
                                   value="<?php echo isset($userData['mobile']) ? htmlspecialchars($userData['mobile']) : ''; ?>">
                        </div>
                        <div class="error-message" id="mobile-error"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="car_name">Select Car</label>
                    <select id="car_name" name="car_name" class="form-control" required>
                        <option value="">Choose a car...</option>
                        <?php foreach ($available_cars as $car): ?>
                            <option value="<?php echo htmlspecialchars($car); ?>"><?php echo htmlspecialchars($car); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="booking_date">Pickup Date</label>
                        <input type="date" id="booking_date" name="booking_date" class="form-control" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="booking_time">Pickup Time</label>
                        <input type="time" id="booking_time" name="booking_time" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pickup_location">Pickup Location</label>
                    <input type="text" id="pickup_location" name="pickup_location" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="dropoff_location">Drop-off Location</label>
                    <input type="text" id="dropoff_location" name="dropoff_location" class="form-control" required>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-car"></i> Book Now
                </button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const dateInput = document.getElementById('booking_date');
        const timeInput = document.getElementById('booking_time');
        
        // Set minimum and maximum dates
        const today = new Date();
        const maxDate = new Date();
        maxDate.setMonth(maxDate.getMonth() + 6);
        
        dateInput.min = today.toISOString().split('T')[0];
        dateInput.max = maxDate.toISOString().split('T')[0];

        // Update minimum time when date changes
        function updateMinTime() {
            const selectedDate = new Date(dateInput.value);
            const now = new Date();
            
            // Reset time input
            timeInput.value = '';
            
            // If selected date is today, set min time to 1 hour from now
            if (selectedDate.toDateString() === now.toDateString()) {
                const minHour = (now.getHours() + 1).toString().padStart(2, '0');
                const minMinute = now.getMinutes().toString().padStart(2, '0');
                timeInput.min = `${minHour}:${minMinute}`;
            } else {
                timeInput.min = '00:00'; // No minimum time for future dates
            }
        }

        dateInput.addEventListener('change', updateMinTime);
        
        const mobileInput = document.getElementById('mobile');
        const mobileError = document.getElementById('mobile-error');

        function showError(message) {
            mobileError.textContent = message;
            mobileInput.style.borderColor = '#dc3545';
        }

        function clearError() {
            mobileError.textContent = '';
            mobileInput.style.borderColor = '#ddd';
        }

        // Real-time validation
        mobileInput.addEventListener('input', function() {
            const mobilePattern = /^[6789][0-9]{0,9}$/;
            if (this.value.length === 0) {
                clearError();
            } else if (!mobilePattern.test(this.value)) {
                if (this.value.length !== 10) {
                    showError('Mobile number must be 10 digits');
                } else if (!/^[6789]/.test(this.value)) {
                    showError('Mobile number must start with 6, 7, 8, or 9');
                } else {
                    showError('Please enter a valid mobile number');
                }
            } else {
                clearError();
            }
        });

        // Prevent non-numeric input and enforce starting digits
        mobileInput.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.keyCode);
            const currentValue = this.value;
            
            // Only allow 6, 7, 8, or 9 as first digit
            if (currentValue.length === 0 && !['6','7','8','9'].includes(char)) {
                e.preventDefault();
                showError('Mobile number must start with 6, 7, 8, or 9');
                return;
            }
            
            // Only allow numbers and limit to 10 digits
            if (!/[0-9]/.test(char) || currentValue.length >= 10) {
                e.preventDefault();
            }
        });

        // Update form submission validation
        form.addEventListener('submit', function(e) {
            const errors = [];
            const mobilePattern = /^[6789][0-9]{9}$/;

            // Validate mobile number
            if (!mobilePattern.test(mobileInput.value)) {
                errors.push('Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9');
                showError('Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9');
            }

            // Validate booking date and time
            const selectedDate = new Date(dateInput.value);
            const selectedTime = timeInput.value;
            const now = new Date();
            const maxDate = new Date();
            maxDate.setMonth(maxDate.getMonth() + 6);
            
            // Create datetime for comparison
            const selectedDateTime = new Date(selectedDate.toDateString() + ' ' + selectedTime);

            // Check if date is within valid range
            if (selectedDateTime <= now) {
                errors.push('Booking date and time must be in the future');
            } else if (selectedDate > maxDate) {
                errors.push('Booking date cannot be more than 6 months in advance');
            }

            // Check minimum time for same-day bookings
            if (selectedDate.toDateString() === now.toDateString()) {
                const minTime = new Date(now.getTime() + (60 * 60 * 1000)); // 1 hour from now
                if (selectedDateTime < minTime) {
                    errors.push('For same-day bookings, time must be at least 1 hour from now');
                }
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        });

        // Initialize min time on page load
        updateMinTime();
    });
    </script>
</body>
</html> 