<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode([
        'success' => false,
        'error' => 'You must be logged in to enlist equipment'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $item_name = trim($_POST['item_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $daily_rate = floatval($_POST['daily_rate'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $terms = isset($_POST['terms']) ? true : false;
    
    // Basic validation
    if (empty($item_name) || empty($category) || empty($description) || empty($daily_rate) || empty($location)) {
        echo json_encode([
            'success' => false,
            'error' => 'All fields are required'
        ]);
        exit;
    }
    
    if (!$terms) {
        echo json_encode([
            'success' => false,
            'error' => 'You must agree to the terms and conditions'
        ]);
        exit;
    }
    
    if ($daily_rate <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Daily rate must be greater than 0'
        ]);
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
    
    // Map category names to category IDs
    $category_map = [
        'construction' => 1,
        'kitchen' => 2,
        'heavy_equipment' => 3,
        'electronics' => 4,
        'furniture' => 5,
        'gardening' => 6,
        'sports' => 7,
        'party' => 8,
        'cleaning' => 9,
        'medical' => 10,
        'other' => 1  // Default to Tools
    ];
    
    $category_id = $category_map[$category] ?? 1;
    
    // Insert equipment into database
    try {
        // Insert the equipment using the proper schema
        $stmt = $conn->prepare("INSERT INTO equipment (owner_id, category_id, name, description, daily_rate, location, is_available) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("iissds", $user_id, $category_id, $item_name, $description, $daily_rate, $location);
        
        if ($stmt->execute()) {
            $equipment_id = $conn->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'Equipment enlisted successfully! Your item is now available for rental.',
                'equipment_id' => $equipment_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to enlist equipment. Please try again.'
            ]);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}
?>
