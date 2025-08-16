<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

// Get action from GET or POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Get JSON input for POST actions
$input = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $action;
}

switch ($action) {
    case 'browse':
        browseEquipment();
        break;
        
    case 'categories':
        getCategories();
        break;
        
    case 'view':
        viewEquipment();
        break;
        
    case 'delete':
        deleteEquipment();
        break;
        
    case 'update':
        updateEquipment();
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action'
        ]);
        break;
}

function deleteEquipment() {
    global $conn, $input;
    
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        echo json_encode([
            'success' => false,
            'error' => 'You must be logged in to delete equipment'
        ]);
        return;
    }
    
    $equipment_id = intval($input['equipment_id'] ?? 0);
    $user_id = $_SESSION['user_id'];
    
    if ($equipment_id <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid equipment ID'
        ]);
        return;
    }
    
    try {
        // Check if equipment belongs to user
        $stmt = $conn->prepare("SELECT owner_id FROM equipment WHERE equipment_id = ?");
        $stmt->bind_param("i", $equipment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Equipment not found'
            ]);
            return;
        }
        
        $equipment = $result->fetch_assoc();
        if ($equipment['owner_id'] != $user_id) {
            echo json_encode([
                'success' => false,
                'error' => 'You can only delete your own equipment'
            ]);
            return;
        }
        
        // Check if equipment has active rentals
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM rentals WHERE equipment_id = ? AND rental_status IN ('confirmed', 'active')");
        $stmt->bind_param("i", $equipment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rental_count = $result->fetch_assoc()['count'];
        
        if ($rental_count > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Cannot delete equipment with active rentals'
            ]);
            return;
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        // Delete equipment availability records
        $stmt = $conn->prepare("DELETE FROM equipment_availability WHERE equipment_id = ?");
        $stmt->bind_param("i", $equipment_id);
        $stmt->execute();
        
        // Delete the equipment
        $stmt = $conn->prepare("DELETE FROM equipment WHERE equipment_id = ?");
        $stmt->bind_param("i", $equipment_id);
        
        if ($stmt->execute()) {
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Equipment deleted successfully'
            ]);
        } else {
            $conn->rollback();
            echo json_encode([
                'success' => false,
                'error' => 'Failed to delete equipment'
            ]);
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'error' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
}

function updateEquipment() {
    global $conn, $input;
    
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        echo json_encode([
            'success' => false,
            'error' => 'You must be logged in to update equipment'
        ]);
        return;
    }
    
    $equipment_id = intval($input['equipment_id'] ?? 0);
    $user_id = $_SESSION['user_id'];
    
    if ($equipment_id <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid equipment ID'
        ]);
        return;
    }
    
    // Check if equipment belongs to user
    $stmt = $conn->prepare("SELECT owner_id FROM equipment WHERE equipment_id = ?");
    $stmt->bind_param("i", $equipment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Equipment not found'
        ]);
        return;
    }
    
    $equipment = $result->fetch_assoc();
    if ($equipment['owner_id'] != $user_id) {
        echo json_encode([
            'success' => false,
            'error' => 'You can only update your own equipment'
        ]);
        return;
    }
    
    // For now, just return success - full update functionality can be added later
    echo json_encode([
        'success' => true,
        'message' => 'Update functionality coming soon!'
    ]);
}

function browseEquipment() {
    global $conn;
    
    // Get filter parameters
    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';
    $location = $_GET['location'] ?? '';
    $min_price = $_GET['min_price'] ?? '';
    $max_price = $_GET['max_price'] ?? '';
    $sort = $_GET['sort'] ?? 'created_at';
    $order = $_GET['order'] ?? 'DESC';
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 12);
    
    // Calculate offset
    $offset = ($page - 1) * $limit;
    
    // Build WHERE clause
    $where_conditions = ['e.is_available = 1'];
    $params = [];
    $param_types = '';
    
    if (!empty($search)) {
        $where_conditions[] = '(e.name LIKE ? OR e.description LIKE ? OR e.brand LIKE ?)';
        $search_term = '%' . $search . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $param_types .= 'sss';
    }
    
    if (!empty($category)) {
        $where_conditions[] = 'c.category_name LIKE ?';
        $params[] = '%' . $category . '%';
        $param_types .= 's';
    }
    
    if (!empty($location)) {
        $where_conditions[] = '(e.location LIKE ? OR u.city LIKE ?)';
        $location_term = '%' . $location . '%';
        $params[] = $location_term;
        $params[] = $location_term;
        $param_types .= 'ss';
    }
    
    if (!empty($min_price)) {
        $where_conditions[] = 'e.daily_rate >= ?';
        $params[] = floatval($min_price);
        $param_types .= 'd';
    }
    
    if (!empty($max_price)) {
        $where_conditions[] = 'e.daily_rate <= ?';
        $params[] = floatval($max_price);
        $param_types .= 'd';
    }
    
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    
    // Validate sort field
    $allowed_sorts = ['created_at', 'daily_rate', 'name', 'owner_rating'];
    if (!in_array($sort, $allowed_sorts)) {
        $sort = 'created_at';
    }
    
    // Validate order
    $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
    
    // Map sort field to actual column
    $sort_field = $sort;
    if ($sort === 'name') {
        $sort_field = 'e.name';
    } elseif ($sort === 'daily_rate') {
        $sort_field = 'e.daily_rate';
    } elseif ($sort === 'owner_rating') {
        $sort_field = 'u.average_rating';
    } else {
        $sort_field = 'e.created_at';
    }
    
    try {
        // Get total count
        $count_sql = "SELECT COUNT(*) as total 
                      FROM equipment e 
                      JOIN categories c ON e.category_id = c.category_id 
                      JOIN users u ON e.owner_id = u.user_id 
                      $where_clause";
        
        if (!empty($params)) {
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->bind_param($param_types, ...$params);
            $count_stmt->execute();
            $total_result = $count_stmt->get_result();
        } else {
            $total_result = $conn->query($count_sql);
        }
        
        $total_count = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_count / $limit);
        
        // Get equipment data
        $sql = "SELECT e.*, c.category_name, u.first_name, u.last_name, u.city, u.average_rating as owner_rating, u.total_reviews
                FROM equipment e 
                JOIN categories c ON e.category_id = c.category_id 
                JOIN users u ON e.owner_id = u.user_id 
                $where_clause
                ORDER BY $sort_field $order
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        $param_types .= 'ii';
        
        $stmt = $conn->prepare($sql);
        if (!empty($param_types)) {
            $stmt->bind_param($param_types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $equipment = [];
        while ($row = $result->fetch_assoc()) {
            $equipment[] = [
                'equipment_id' => $row['equipment_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'daily_rate' => $row['daily_rate'],
                'daily_rate_formatted' => '₱' . number_format($row['daily_rate'], 2),
                'weekly_rate' => $row['weekly_rate'],
                'monthly_rate' => $row['monthly_rate'],
                'brand' => $row['brand'],
                'model' => $row['model'],
                'condition_status' => $row['condition_status'],
                'location' => $row['location'],
                'city' => $row['city'],
                'category_name' => $row['category_name'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'owner_rating' => $row['owner_rating'],
                'owner_reviews' => $row['total_reviews'],
                'is_available' => $row['is_available'],
                'is_available_today' => $row['is_available'], // Simplified for now
                'created_at' => $row['created_at']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $equipment,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total' => $total_count,
                'per_page' => $limit
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

function getCategories() {
    global $conn;
    
    try {
        $sql = "SELECT category_id, category_name, category_icon, description FROM categories WHERE is_active = 1 ORDER BY category_name";
        $result = $conn->query($sql);
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

function viewEquipment() {
    global $conn;
    
    $equipment_id = intval($_GET['id'] ?? 0);
    
    if ($equipment_id <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid equipment ID'
        ]);
        return;
    }
    
    try {
        $sql = "SELECT e.*, c.category_name, u.first_name, u.last_name, u.city, u.average_rating as owner_rating, u.total_reviews
                FROM equipment e 
                JOIN categories c ON e.category_id = c.category_id 
                JOIN users u ON e.owner_id = u.user_id 
                WHERE e.equipment_id = ? AND e.is_available = 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $equipment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Equipment not found or not available'
            ]);
            return;
        }
        
        $row = $result->fetch_assoc();
        
        $equipment = [
            'equipment_id' => $row['equipment_id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'daily_rate' => $row['daily_rate'],
            'daily_rate_formatted' => '₱' . number_format($row['daily_rate'], 2),
            'weekly_rate' => $row['weekly_rate'],
            'monthly_rate' => $row['monthly_rate'],
            'brand' => $row['brand'],
            'model' => $row['model'],
            'condition_status' => $row['condition_status'],
            'location' => $row['location'],
            'city' => $row['city'],
            'category_name' => $row['category_name'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'owner_rating' => $row['owner_rating'],
            'owner_reviews' => $row['total_reviews'],
            'is_available' => $row['is_available'],
            'photos' => [], // Photos not implemented yet
            'created_at' => $row['created_at']
        ];
        
        echo json_encode([
            'success' => true,
            'data' => $equipment
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
}
?>