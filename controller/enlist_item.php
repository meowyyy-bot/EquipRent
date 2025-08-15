<?php
session_start();
require_once __DIR__ . '/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['enlist_error'] = "You must be logged in to enlist equipment.";
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $item_name = trim($_POST['item_name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $daily_rate = floatval($_POST['daily_rate']);
    $location = trim($_POST['location']);
    $terms = isset($_POST['terms']) ? true : false;
    
    // Basic validation
    if (empty($item_name) || empty($category) || empty($description) || empty($daily_rate) || empty($location)) {
        $_SESSION['enlist_error'] = "All fields are required.";
        header("Location: ../index.php");
        exit;
    }
    
    if (!$terms) {
        $_SESSION['enlist_error'] = "You must agree to the terms and conditions.";
        header("Location: ../index.php");
        exit;
    }
    
    if ($daily_rate <= 0) {
        $_SESSION['enlist_error'] = "Daily rate must be greater than 0.";
        header("Location: ../index.php");
        exit;
    }
    
    // Handle file uploads if any
    $photo_paths = [];
    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $upload_dir = '../uploads/equipment/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                $file_name = $_FILES['photos']['name'][$key];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Check file type
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($file_ext, $allowed_types)) {
                    $new_file_name = uniqid() . '_' . $user_id . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $photo_paths[] = 'uploads/equipment/' . $new_file_name;
                    }
                }
            }
        }
    }
    
    // Insert equipment into database
    try {
        // First, create equipment table if it doesn't exist
        $create_table_sql = "CREATE TABLE IF NOT EXISTS equipment (
            equipment_id INT AUTO_INCREMENT PRIMARY KEY,
            owner_id INT NOT NULL,
            item_name VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            daily_rate DECIMAL(10,2) NOT NULL,
            location VARCHAR(255) NOT NULL,
            photo_paths TEXT,
            status ENUM('available', 'rented', 'maintenance', 'inactive') DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE
        )";
        
        $conn->query($create_table_sql);
        
        // Insert the equipment
        $photo_paths_json = !empty($photo_paths) ? json_encode($photo_paths) : null;
        
        $stmt = $conn->prepare("INSERT INTO equipment (owner_id, item_name, category, description, daily_rate, location, photo_paths) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdss", $user_id, $item_name, $category, $description, $daily_rate, $location, $photo_paths_json);
        
        if ($stmt->execute()) {
            $_SESSION['enlist_success'] = "Equipment enlisted successfully! Your item is now available for rental.";
        } else {
            $_SESSION['enlist_error'] = "Failed to enlist equipment. Please try again.";
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        $_SESSION['enlist_error'] = "An error occurred: " . $e->getMessage();
    }
    
} else {
    $_SESSION['enlist_error'] = "Invalid request method.";
}

header("Location: ../index.php");
exit;
?>
