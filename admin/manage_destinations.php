<?php
require_once('../config/config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = $_GET['delete'];
    
    try {
        // Get image path before deleting
        $stmt = $pdo->prepare("SELECT image_url FROM destinations WHERE id = ?");
        $stmt->execute([$id]);
        $destination = $stmt->fetch();
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM destinations WHERE id = ?");
        $stmt->execute([$id]);
        
        // Delete image file if it exists
        if ($destination && file_exists('../' . $destination['image_url'])) {
            unlink('../' . $destination['image_url']);
        }
        
        $success_message = "Destination deleted successfully!";
    } catch (PDOException $e) {
        $error_message = "Failed to delete destination: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $location = sanitize_input($_POST['location']);
    $description = sanitize_input($_POST['description']);
    $highlights = sanitize_input($_POST['highlights']);
    $visit_date = sanitize_input($_POST['visit_date']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $id = isset($_POST['destination_id']) ? $_POST['destination_id'] : null;
    
    // Validate inputs
    $errors = [];
    if (empty($title)) $errors[] = "Title is required";
    if (empty($location)) $errors[] = "Location is required";
    if (empty($description)) $errors[] = "Description is required";
    if (empty($visit_date)) $errors[] = "Visit date is required";

    if (empty($errors)) {
        try {
            // Process image upload
            $image_url = '';
            $upload_success = false;
            
            if (!empty($_FILES['image']['name'])) {
                // Create directory if it doesn't exist
                $upload_dir = '../assets/images/destinations/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                $target_path = $upload_dir . $image_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_url = 'assets/images/destinations/' . $image_name;
                    $upload_success = true;
                } else {
                    $errors[] = "Failed to upload image";
                }
            }
            
            // Update existing destination
            if ($id) {
                $stmt = $pdo->prepare("SELECT image_url FROM destinations WHERE id = ?");
                $stmt->execute([$id]);
                $current_destination = $stmt->fetch();
                
                // Use existing image if no new one uploaded
                if (!$upload_success && $current_destination) {
                    $image_url = $current_destination['image_url'];
                } elseif ($upload_success && $current_destination && file_exists('../' . $current_destination['image_url'])) {
                    // Delete old image if new one uploaded
                    unlink('../' . $current_destination['image_url']);
                }
                
                $sql = "UPDATE destinations SET 
                        title = ?, location = ?, description = ?, 
                        highlights = ?, visit_date = ?, is_featured = ?";
                
                $params = [$title, $location, $description, $highlights, $visit_date, $is_featured];
                
                if ($upload_success) {
                    $sql .= ", image_url = ?";
                    $params[] = $image_url;
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $id;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                $success_message = "Destination updated successfully!";
            } 
            // Add new destination
            else {
                if (!$upload_success) {
                    $errors[] = "Image is required for new destinations";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO destinations 
                        (title, location, description, image_url, visit_date, highlights, is_featured) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $location, $description, $image_url, $visit_date, $highlights, $is_featured]);
                    
                    $success_message = "Destination added successfully!";
                }
            }
            
            // Clear form if no errors
            if (empty($errors)) {
                $_POST = array();
            }
            
        } catch (PDOException $e) {
            $error_message = "Operation failed: " . $e->getMessage();
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Get destination for editing
$edit_destination = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $edit_destination = $stmt->fetch();
    } catch (PDOException $e) {
        $error_message = "Failed to fetch destination: " . $e->getMessage();
    }
}

// Fetch all destinations
try {
    $stmt = $pdo->query("SELECT * FROM destinations ORDER BY created_at DESC");
    $destinations = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Failed to fetch destinations: " . $e->getMessage();
    $destinations = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations - Admin Dashboard</title>
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
        }

        .admin-container {
            display: flex;
            margin-left: 0;
            position: relative;
            width: 100%;
            min-height: calc(100vh - 60px);
            transition: all 0.3s ease;
            justify-content: center;
        }
        
        .admin-content {
            flex: 1;
            padding: 20px;
            margin-top: 10px;
            width: 100%;
            transition: all 0.3s ease;
            max-width: 1400px;
        }
        
        .admin-content.fullwidth {
            margin-left: 0;
        }
        
        .content-header {
            margin-bottom: 20px;
            text-align: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .content-header h2 {
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.8rem;
            margin-bottom: 0;
            justify-content: center;
        }
        
        .content-header h2 i {
            color: var(--primary-color);
        }
        
        .content-body {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: fadeIn 0.5s ease;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert::before {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 1.2rem;
        }
        
        .alert-success::before {
            content: "\f058"; /* fa-check-circle */
            color: #155724;
        }
        
        .alert-danger::before {
            content: "\f057"; /* fa-times-circle */
            color: #721c24;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #446f0b;
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        
        .toggle-form {
            margin-bottom: 20px;
            font-weight: 500;
            display: block;
            margin: 0 auto 20px;
            min-width: 200px;
        }
        
        .form-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
            margin-bottom: 20px;
        }
        
        .form-container.show {
            max-height: 2000px;
        }
        
        .card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            background-color: white;
        }
        
        .card-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .card-header h3 {
            margin: 0;
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(84, 136, 14, 0.2);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .checkbox-container input {
            cursor: pointer;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .destination-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .destination-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #eee;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .destination-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }
        
        .destination-img {
            height: 180px;
            width: 100%;
            overflow: hidden;
            position: relative;
        }
        
        .destination-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .destination-card:hover .destination-img img {
            transform: scale(1.05);
        }
        
        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary-color);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .destination-info {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .destination-title {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: var(--text-color);
        }
        
        .destination-location {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .destination-location i {
            color: var(--primary-color);
            margin-right: 5px;
        }
        
        .destination-date {
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .destination-date i {
            color: var(--primary-color);
            margin-right: 5px;
        }
        
        .destination-info p {
            margin-bottom: 15px;
            color: var(--text-light);
            flex-grow: 1;
        }
        
        .destination-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: auto;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .btn-edit {
            background: #4a6fdc;
            color: white;
            margin-right: 5px;
        }
        
        .btn-edit:hover {
            background: #3a5ec7;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .img-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }
        
        /* Improve responsive design */
        @media (max-width: 1200px) {
            .admin-content {
                max-width: 100%;
                padding: 20px;
            }
            
            .destination-list {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }
        
        @media (max-width: 992px) {
            .destination-list {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .admin-content {
                padding: 15px;
            }
            
            .destination-list {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }
            
            .form-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .destination-list {
                grid-template-columns: 1fr;
            }
            
            .content-header h2 {
                font-size: 1.5rem;
            }
            
            .content-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="admin-container">
        <div class="admin-content fullwidth">
            <div class="content-header">
                <h2><i class="fas fa-map-marked-alt"></i> Manage Destinations</h2>
            </div>
            
            <div class="content-body">
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
                
                <button class="btn btn-primary toggle-form" id="toggleForm">
                    <i class="fas fa-plus"></i> <?php echo $edit_destination ? 'Edit Destination' : 'Add New Destination'; ?>
                </button>
                
                <div class="form-container <?php echo ($edit_destination || !empty($error_message)) ? 'show' : ''; ?>" id="formContainer">
                    <div class="card">
                        <div class="card-header">
                            <h3><?php echo $edit_destination ? 'Edit Destination' : 'Add New Destination'; ?></h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($edit_destination): ?>
                                    <input type="hidden" name="destination_id" value="<?php echo $edit_destination['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" id="title" name="title" class="form-control" 
                                               value="<?php echo $edit_destination ? htmlspecialchars($edit_destination['title']) : ''; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <input type="text" id="location" name="location" class="form-control" 
                                               value="<?php echo $edit_destination ? htmlspecialchars($edit_destination['location']) : ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" class="form-control" required><?php echo $edit_destination ? htmlspecialchars($edit_destination['description']) : ''; ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="highlights">Highlights (comma separated)</label>
                                    <textarea id="highlights" name="highlights" class="form-control"><?php echo $edit_destination ? htmlspecialchars($edit_destination['highlights']) : ''; ?></textarea>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="visit_date">Visit Date</label>
                                        <input type="date" id="visit_date" name="visit_date" class="form-control" 
                                               value="<?php echo $edit_destination ? htmlspecialchars($edit_destination['visit_date']) : ''; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="image">Image</label>
                                        <input type="file" id="image" name="image" class="form-control" <?php echo !$edit_destination ? 'required' : ''; ?> accept="image/*">
                                        <?php if ($edit_destination && !empty($edit_destination['image_url'])): ?>
                                            <p>Current image:</p>
                                            <img src="../<?php echo htmlspecialchars($edit_destination['image_url']); ?>" class="img-preview" alt="Destination Image">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="checkbox-container">
                                        <input type="checkbox" name="is_featured" <?php echo ($edit_destination && $edit_destination['is_featured']) ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                        Feature this destination
                                    </label>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?php echo $edit_destination ? 'Update Destination' : 'Add Destination'; ?>
                                    </button>
                                    
                                    <?php if ($edit_destination): ?>
                                        <a href="manage_destinations.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="destination-list">
                    <?php if (empty($destinations)): ?>
                        <p>No destinations found. Add your first destination!</p>
                    <?php else: ?>
                        <?php foreach ($destinations as $destination): ?>
                            <div class="destination-card">
                                <div class="destination-img">
                                    <img src="../<?php echo htmlspecialchars($destination['image_url']); ?>" alt="<?php echo htmlspecialchars($destination['title']); ?>">
                                    <?php if ($destination['is_featured']): ?>
                                        <span class="featured-badge">Featured</span>
                                    <?php endif; ?>
                                </div>
                                <div class="destination-info">
                                    <h3 class="destination-title"><?php echo htmlspecialchars($destination['title']); ?></h3>
                                    <div class="destination-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($destination['location']); ?>
                                    </div>
                                    <div class="destination-date">
                                        <i class="far fa-calendar-alt"></i> Visited on: <?php echo date("d M Y", strtotime($destination['visit_date'])); ?>
                                    </div>
                                    <p><?php echo mb_substr(htmlspecialchars($destination['description']), 0, 100); ?>...</p>
                                    <div class="destination-actions">
                                        <a href="manage_destinations.php?edit=<?php echo $destination['id']; ?>" class="btn btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="manage_destinations.php?delete=<?php echo $destination['id']; ?>" 
                                           class="btn btn-sm btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this destination?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleForm');
            const formContainer = document.getElementById('formContainer');
            
            toggleBtn.addEventListener('click', function() {
                formContainer.classList.toggle('show');
            });
            
            // Preview image before upload
            const imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        const currentPreview = document.querySelector('.img-preview');
                        
                        reader.onload = function(e) {
                            if (currentPreview) {
                                currentPreview.src = e.target.result;
                            } else {
                                const preview = document.createElement('img');
                                preview.src = e.target.result;
                                preview.className = 'img-preview';
                                imageInput.parentNode.appendChild(preview);
                            }
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html> 