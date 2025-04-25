<?php
require_once('../config/config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../user/login.php");
    exit();
}

// Initialize variables
$cars = [];
$error = '';
$success = '';

// Handle form submissions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle car addition
    if (isset($_POST['add_car'])) {
        $car_name = sanitize_input($_POST['car_name']);
        $description = sanitize_input($_POST['description']);
        $passengers = (int)sanitize_input($_POST['passengers']);
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        // Image upload handling
        $image_url = '';
        if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
            $target_dir = "../assets/images/";
            $file_extension = pathinfo($_FILES['car_image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'car_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Check if image file is valid
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES['car_image']['tmp_name'], $target_file)) {
                    $image_url = 'assets/images/' . $new_filename;
                } else {
                    $error = "Failed to upload image. Please try again.";
                }
            } else {
                $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }
        
        // If no errors, insert into database
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO cars (car_name, description, passengers, image_url, is_available) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$car_name, $description, $passengers, $image_url, $is_available]);
                $success = "Car added successfully!";
            } catch (PDOException $e) {
                $error = "Error adding car: " . $e->getMessage();
            }
        }
    }
    
    // Handle car update
    if (isset($_POST['update_car'])) {
        $car_id = (int)sanitize_input($_POST['car_id']);
        $car_name = sanitize_input($_POST['car_name']);
        $description = sanitize_input($_POST['description']);
        $passengers = (int)sanitize_input($_POST['passengers']);
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        // Check if there's an existing image
        $current_image = '';
        try {
            $stmt = $pdo->prepare("SELECT image_url FROM cars WHERE id = ?");
            $stmt->execute([$car_id]);
            $row = $stmt->fetch();
            $current_image = $row['image_url'];
        } catch (PDOException $e) {
            $error = "Error fetching current image: " . $e->getMessage();
        }
        
        // Image upload handling for update
        $image_url = $current_image; // Default to current image
        if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
            $target_dir = "../assets/images/";
            $file_extension = pathinfo($_FILES['car_image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'car_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Check if image file is valid
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES['car_image']['tmp_name'], $target_file)) {
                    $image_url = 'assets/images/' . $new_filename;
                } else {
                    $error = "Failed to upload image. Please try again.";
                }
            } else {
                $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }
        
        // If no errors, update the database
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("UPDATE cars SET car_name = ?, description = ?, passengers = ?, image_url = ?, is_available = ? WHERE id = ?");
                $stmt->execute([$car_name, $description, $passengers, $image_url, $is_available, $car_id]);
                $success = "Car updated successfully!";
            } catch (PDOException $e) {
                $error = "Error updating car: " . $e->getMessage();
            }
        }
    }
    
    // Handle car deletion
    if (isset($_POST['delete_car'])) {
        $car_id = (int)sanitize_input($_POST['car_id']);
        
        try {
            $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
            $stmt->execute([$car_id]);
            $success = "Car deleted successfully!";
        } catch (PDOException $e) {
            $error = "Error deleting car: " . $e->getMessage();
        }
    }
}

// Fetch all cars from the database
try {
    $stmt = $pdo->query("SELECT * FROM cars ORDER BY car_name");
    $cars = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching cars: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #54880e;
            --primary-hover: #446e0b;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --border-color: #e0e0e0;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
        }
        
        body {
            background-color: #f5f5f5;
            padding-top: 60px;
        }
        
        .content-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .page-title {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 2rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #bd2130;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--text-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .form-check-input {
            margin-right: 10px;
        }
        
        /* Car List Styles */
        .car-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .car-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid var(--border-color);
        }
        
        .car-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .car-title {
            font-size: 18px;
            color: var(--text-color);
            font-weight: bold;
            margin: 0;
        }
        
        .availability-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .available {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--success-color);
        }
        
        .unavailable {
            background-color: rgba(220, 53, 69, 0.2);
            color: var(--danger-color);
        }
        
        .car-details {
            padding: 15px;
        }
        
        .car-description {
            margin-bottom: 10px;
            color: var(--text-color);
            font-size: 14px;
        }
        
        .car-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .car-info {
            display: flex;
            align-items: center;
            color: var(--text-color);
            font-size: 14px;
        }
        
        .car-info i {
            margin-right: 5px;
            color: var(--primary-color);
        }
        
        .car-actions {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
            padding-top: 80px;
        }
        
        .modal-content {
            background-color: white;
            margin: auto;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: modalopen 0.3s;
        }
        
        .modal-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            margin: 0;
            color: var(--primary-color);
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: var(--text-color);
        }
        
        .modal-body {
            padding: 15px;
        }
        
        .modal-footer {
            padding: 15px;
            border-top: 1px solid var(--border-color);
            text-align: right;
        }
        
        @keyframes modalopen {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .car-list {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
            }
            
            .form-container,
            .card {
                padding: 15px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="content-container">
        <h1 class="page-title">Manage Cars</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Add Car Button -->
        <button class="btn btn-primary" id="addCarBtn">
            <i class="fas fa-plus"></i> Add New Car
        </button>
        
        <!-- Car List -->
        <div class="car-list">
            <?php foreach ($cars as $car): ?>
                <div class="car-card">
                    <?php if (!empty($car['image_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['car_name']); ?>" class="car-image">
                    <?php else: ?>
                        <div class="car-image-placeholder" style="height: 200px; background-color: #eee; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-car" style="font-size: 50px; color: #ccc;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="car-header">
                        <h3 class="car-title"><?php echo htmlspecialchars($car['car_name']); ?></h3>
                        <span class="availability-badge <?php echo $car['is_available'] ? 'available' : 'unavailable'; ?>">
                            <?php echo $car['is_available'] ? 'Available' : 'Unavailable'; ?>
                        </span>
                    </div>
                    
                    <div class="car-details">
                        <p class="car-description"><?php echo htmlspecialchars($car['description']); ?></p>
                        <div class="car-meta">
                            <div class="car-info">
                                <i class="fas fa-users"></i>
                                <span><?php echo $car['passengers']; ?> Passengers</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="car-actions">
                        <button class="btn btn-warning edit-car" data-id="<?php echo $car['id']; ?>" data-name="<?php echo htmlspecialchars($car['car_name']); ?>" data-description="<?php echo htmlspecialchars($car['description']); ?>" data-passengers="<?php echo $car['passengers']; ?>" data-available="<?php echo $car['is_available']; ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger delete-car" data-id="<?php echo $car['id']; ?>" data-name="<?php echo htmlspecialchars($car['car_name']); ?>">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($cars)): ?>
                <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 30px;">
                    <i class="fas fa-car" style="font-size: 50px; color: #ccc; margin-bottom: 15px;"></i>
                    <h3>No Cars Found</h3>
                    <p>Add your first car by clicking the 'Add New Car' button above.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Add Car Modal -->
    <div id="addCarModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Car</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="car_name" class="form-label">Car Name</label>
                        <input type="text" id="car_name" name="car_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="passengers" class="form-label">Number of Passengers</label>
                        <input type="number" id="passengers" name="passengers" class="form-control" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="car_image" class="form-label">Car Image</label>
                        <input type="file" id="car_image" name="car_image" class="form-control">
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" id="is_available" name="is_available" class="form-check-input" checked>
                        <label for="is_available" class="form-check-label">Available for Booking</label>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger close-modal">Cancel</button>
                        <button type="submit" name="add_car" class="btn btn-primary">Add Car</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Car Modal -->
    <div id="editCarModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Car</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="edit_car_id" name="car_id">
                    
                    <div class="form-group">
                        <label for="edit_car_name" class="form-label">Car Name</label>
                        <input type="text" id="edit_car_name" name="car_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_passengers" class="form-label">Number of Passengers</label>
                        <input type="number" id="edit_passengers" name="passengers" class="form-control" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_car_image" class="form-label">Car Image (Leave empty to keep current image)</label>
                        <input type="file" id="edit_car_image" name="car_image" class="form-control">
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" id="edit_is_available" name="is_available" class="form-check-input">
                        <label for="edit_is_available" class="form-check-label">Available for Booking</label>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger close-modal">Cancel</button>
                        <button type="submit" name="update_car" class="btn btn-primary">Update Car</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Car Modal -->
    <div id="deleteCarModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Delete Car</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the car "<span id="delete_car_name"></span>"?</p>
                <p>This action cannot be undone.</p>
                
                <form action="" method="POST">
                    <input type="hidden" id="delete_car_id" name="car_id">
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary close-modal">Cancel</button>
                        <button type="submit" name="delete_car" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal functionality
            const addCarModal = document.getElementById('addCarModal');
            const editCarModal = document.getElementById('editCarModal');
            const deleteCarModal = document.getElementById('deleteCarModal');
            const addCarBtn = document.getElementById('addCarBtn');
            
            // Open Add Car Modal
            addCarBtn.addEventListener('click', function() {
                addCarModal.style.display = 'block';
            });
            
            // Edit Car Button Click
            document.querySelectorAll('.edit-car').forEach(button => {
                button.addEventListener('click', function() {
                    const carId = this.getAttribute('data-id');
                    const carName = this.getAttribute('data-name');
                    const description = this.getAttribute('data-description');
                    const passengers = this.getAttribute('data-passengers');
                    const isAvailable = this.getAttribute('data-available') === '1';
                    
                    document.getElementById('edit_car_id').value = carId;
                    document.getElementById('edit_car_name').value = carName;
                    document.getElementById('edit_description').value = description;
                    document.getElementById('edit_passengers').value = passengers;
                    document.getElementById('edit_is_available').checked = isAvailable;
                    
                    editCarModal.style.display = 'block';
                });
            });
            
            // Delete Car Button Click
            document.querySelectorAll('.delete-car').forEach(button => {
                button.addEventListener('click', function() {
                    const carId = this.getAttribute('data-id');
                    const carName = this.getAttribute('data-name');
                    
                    document.getElementById('delete_car_id').value = carId;
                    document.getElementById('delete_car_name').textContent = carName;
                    
                    deleteCarModal.style.display = 'block';
                });
            });
            
            // Close Modals
            document.querySelectorAll('.close, .close-modal').forEach(element => {
                element.addEventListener('click', function() {
                    addCarModal.style.display = 'none';
                    editCarModal.style.display = 'none';
                    deleteCarModal.style.display = 'none';
                });
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === addCarModal) {
                    addCarModal.style.display = 'none';
                }
                if (event.target === editCarModal) {
                    editCarModal.style.display = 'none';
                }
                if (event.target === deleteCarModal) {
                    deleteCarModal.style.display = 'none';
                }
            });
            
            // Success message fade out
            const alertSuccess = document.querySelector('.alert-success');
            if (alertSuccess) {
                setTimeout(function() {
                    alertSuccess.style.opacity = '0';
                    alertSuccess.style.transition = 'opacity 0.5s';
                    setTimeout(function() {
                        alertSuccess.style.display = 'none';
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>
</html> 