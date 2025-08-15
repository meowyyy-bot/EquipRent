<?php
session_start();
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

// Get equipment with filtering
function getEquipment($filters = []) {
    global $conn;
    
    $sql = "SELECT e.*, c.category_name, u.first_name, u.last_name, u.city, u.average_rating as owner_rating, u.total_reviews as owner_reviews 
            FROM equipment e 
            JOIN categories c ON e.category_id = c.category_id 
            JOIN users u ON e.owner_id = u.user_id 
            WHERE e.is_available = 1";
    
    $params = [];
    $types = "";
    
    // Apply filters
    if (!empty($filters['category'])) {
        $sql .= " AND c.category_name LIKE ?";
        $params[] = "%" . $filters['category'] . "%";
        $types .= "s";
    }
    
    if (!empty($filters['location'])) {
        $sql .= " AND u.city LIKE ?";
        $params[] = "%" . $filters['location'] . "%";
        $types .= "s";
    }
    
    if (!empty($filters['min_price'])) {
        $sql .= " AND e.daily_rate >= ?";
        $params[] = floatval($filters['min_price']);
        $types .= "d";
    }
    
    if (!empty($filters['max_price'])) {
        $sql .= " AND e.daily_rate <= ?";
        $params[] = floatval($filters['max_price']);
        $types .= "d";
    }
    
    if (!empty($filters['search'])) {
        $sql .= " AND (e.name LIKE ? OR e.description LIKE ? OR e.brand LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "sss";
    }
    
    // Apply sorting
    $sort = $filters['sort'] ?? 'created_at';
    $order = $filters['order'] ?? 'DESC';
    
    $allowedSorts = ['created_at', 'daily_rate', 'owner_rating', 'name'];
    $allowedOrders = ['ASC', 'DESC'];
    
    if (in_array($sort, $allowedSorts) && in_array(strtoupper($order), $allowedOrders)) {
        $sql .= " ORDER BY " . $sort . " " . strtoupper($order);
    } else {
        $sql .= " ORDER BY e.created_at DESC";
    }
    
    // Apply pagination
    $page = max(1, intval($filters['page'] ?? 1));
    $limit = min(50, max(1, intval($filters['limit'] ?? 12)));
    $offset = ($page - 1) * $limit;
    
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    try {
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $equipment = [];
        while ($row = $result->fetch_assoc()) {
            // Check if equipment is available today
            $row['is_available_today'] = checkAvailability($row['equipment_id'], date('Y-m-d'));
            
            // Format prices in PHP
            $row['daily_rate_formatted'] = '₱' . number_format($row['daily_rate'], 2);
            if ($row['hourly_rate']) {
                $row['hourly_rate_formatted'] = '₱' . number_format($row['hourly_rate'], 2);
            }
            if ($row['weekly_rate']) {
                $row['weekly_rate_formatted'] = '₱' . number_format($row['weekly_rate'], 2);
            }
            if ($row['monthly_rate']) {
                $row['monthly_rate_formatted'] = '₱' . number_format($row['monthly_rate'], 2);
            }
            
            $equipment[] = $row;
        }
        
        $stmt->close();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM equipment e 
                     JOIN categories c ON e.category_id = c.category_id 
                     JOIN users u ON e.owner_id = u.user_id 
                     WHERE e.is_available = 1";
        
        $countParams = [];
        $countTypes = "";
        
        // Apply same filters for count
        if (!empty($filters['category'])) {
            $countSql .= " AND c.category_name LIKE ?";
            $countParams[] = "%" . $filters['category'] . "%";
            $countTypes .= "s";
        }
        
        if (!empty($filters['location'])) {
            $countSql .= " AND u.city LIKE ?";
            $countParams[] = "%" . $filters['location'] . "%";
            $countTypes .= "s";
        }
        
        if (!empty($filters['min_price'])) {
            $countSql .= " AND e.daily_rate >= ?";
            $countParams[] = floatval($filters['min_price']);
            $countTypes .= "d";
        }
        
        if (!empty($filters['max_price'])) {
            $countSql .= " AND e.daily_rate <= ?";
            $countParams[] = floatval($filters['max_price']);
            $countTypes .= "d";
        }
        
        if (!empty($filters['search'])) {
            $countSql .= " AND (e.name LIKE ? OR e.description LIKE ? OR e.brand LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $countParams[] = $searchTerm;
            $countParams[] = $searchTerm;
            $countParams[] = $searchTerm;
            $countTypes .= "sss";
        }
        
        $countStmt = $conn->prepare($countSql);
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalCount = $countResult->fetch_assoc()['total'];
        $countStmt->close();
        
        // Calculate pagination info
        $totalPages = ceil($totalCount / $limit);
        
        return [
            'success' => true,
            'data' => $equipment,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total' => $totalCount,
                'limit' => $limit,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

// Get equipment by ID
function getEquipmentById($id) {
    global $conn;
    
    $sql = "SELECT e.*, c.category_name, u.first_name, u.last_name, u.average_rating as owner_rating, 
            u.total_reviews as owner_reviews, u.phone as owner_phone,
            u.address as owner_address, u.city as owner_city, u.province as owner_province
            FROM equipment e 
            JOIN categories c ON e.category_id = c.category_id
            JOIN users u ON e.owner_id = u.user_id 
            WHERE e.equipment_id = ? AND e.is_available = 1";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'error' => 'Equipment not found'
            ];
        }
        
        $equipment = $result->fetch_assoc();
        
        // Check if equipment is available today
        $equipment['is_available_today'] = checkAvailability($id, date('Y-m-d'));
        
        // Format prices in PHP
        $equipment['daily_rate_formatted'] = '₱' . number_format($equipment['daily_rate'], 2);
        if ($equipment['hourly_rate']) {
            $equipment['hourly_rate_formatted'] = '₱' . number_format($equipment['hourly_rate'], 2);
        }
        if ($equipment['weekly_rate']) {
            $equipment['weekly_rate_formatted'] = '₱' . number_format($equipment['weekly_rate'], 2);
        }
        if ($equipment['monthly_rate']) {
            $equipment['monthly_rate_formatted'] = '₱' . number_format($equipment['monthly_rate'], 2);
        }
        
        // Get availability for next 30 days
        $equipment['availability'] = getAvailabilityCalendar($id);
        
        // Get reviews
        $equipment['reviews'] = getEquipmentReviews($id);
        
        $stmt->close();
        
        return [
            'success' => true,
            'data' => $equipment
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

// Get categories
function getCategories() {
    global $conn;
    
    $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY category_name";
    
    try {
        $result = $conn->query($sql);
        $categories = [];
        
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return [
            'success' => true,
            'data' => $categories
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

// Check equipment availability for a specific date
function checkAvailability($equipmentId, $date) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM equipment_availability 
            WHERE equipment_id = ? AND date = ? AND is_available = 0";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $equipmentId, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'] === 0; // Available if no conflicts
    } catch (Exception $e) {
        return true; // Assume available if error
    }
}

// Get availability calendar for equipment
function getAvailabilityCalendar($equipmentId) {
    global $conn;
    
    $sql = "SELECT date, is_available FROM equipment_availability 
            WHERE equipment_id = ? AND date >= CURDATE() AND date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY date";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $equipmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $availability = [];
        while ($row = $result->fetch_assoc()) {
            $availability[$row['date']] = (bool)$row['is_available'];
        }
        
        $stmt->close();
        
        // Fill in missing dates as available
        $calendar = [];
        for ($i = 0; $i < 30; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            $calendar[$date] = isset($availability[$date]) ? $availability[$date] : true;
        }
        
        return $calendar;
        
    } catch (Exception $e) {
        return [];
    }
}

// Get equipment reviews
function getEquipmentReviews($equipmentId) {
    global $conn;
    
    $sql = "SELECT r.*, u.first_name, u.last_name, u.average_rating as reviewer_rating
            FROM reviews r
            JOIN users u ON r.reviewer_id = u.user_id
            JOIN rentals rt ON r.rental_id = rt.rental_id
            WHERE rt.equipment_id = ?
            ORDER BY r.created_at DESC
            LIMIT 10";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $equipmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        
        $stmt->close();
        return $reviews;
        
    } catch (Exception $e) {
        return [];
    }
}

// Handle API requests
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'test':
        // Simple test endpoint to verify database connection
        try {
            $testQuery = "SELECT COUNT(*) as count FROM equipment";
            $result = $conn->query($testQuery);
            $count = $result->fetch_assoc()['count'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Database connection working',
                'equipment_count' => $count,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'browse':
        $filters = [
            'category' => $_GET['category'] ?? '',
            'location' => $_GET['location'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'search' => $_GET['search'] ?? '',
            'sort' => $_GET['sort'] ?? 'created_at',
            'order' => $_GET['order'] ?? 'DESC',
            'page' => $_GET['page'] ?? 1,
            'limit' => $_GET['limit'] ?? 12
        ];
        
        echo json_encode(getEquipment($filters));
        break;
        
    case 'view':
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            echo json_encode(getEquipmentById($id));
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid equipment ID'
            ]);
        }
        break;
        
    case 'categories':
        echo json_encode(getCategories());
        break;
        
    case 'availability':
        $id = intval($_GET['id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');
        
        if ($id > 0) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'equipment_id' => $id,
                    'date' => $date,
                    'is_available' => checkAvailability($id, $date)
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid equipment ID'
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action'
        ]);
        break;
}
?>
