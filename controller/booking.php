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
        'error' => 'You must be logged in to create a rental'
    ]);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create_rental':
        createRental();
        break;
        
    case 'my_rentals':
        getMyRentals();
        break;
        
    case 'my_equipment':
        getMyEquipment();
        break;
        
    case 'rental_stats':
        getRentalStats();
        break;
        
    case 'equipment_stats':
        getEquipmentStats();
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action'
        ]);
        break;
}

function createRental() {
    global $conn;
    
    // Get form data
    $equipment_id = intval($_POST['equipment_id'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $pickup_location = $_POST['pickup_location'] ?? '';
    $return_location = $_POST['return_location'] ?? '';
    $special_instructions = $_POST['special_instructions'] ?? '';
    
    // Validate required fields
    if ($equipment_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid equipment ID']);
        return;
    }
    
    if (empty($start_date) || empty($end_date)) {
        echo json_encode(['success' => false, 'error' => 'Start and end dates are required']);
        return;
    }
    
    // Validate dates
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $today = new DateTime();
    
    if ($start < $today) {
        echo json_encode(['success' => false, 'error' => 'Start date cannot be in the past']);
        return;
    }
    
    if ($end < $start) {
        echo json_encode(['success' => false, 'error' => 'End date must be after start date']);
        return;
    }
    
    // Check if equipment is available for the selected dates
    if (!checkEquipmentAvailability($equipment_id, $start_date, $end_date)) {
        echo json_encode(['success' => false, 'error' => 'Equipment is not available for the selected dates']);
        return;
    }
    
    // Get equipment details for pricing
    $equipment = getEquipmentDetails($equipment_id);
    if (!$equipment) {
        echo json_encode(['success' => false, 'error' => 'Equipment not found']);
        return;
    }
    
    // Calculate rental cost
    $days = $start->diff($end)->days + 1; // Include both start and end day
    $subtotal = $equipment['daily_rate'] * $days;
    $platform_fee = $subtotal * 0.05; // 5% platform fee
    $total = $subtotal + $platform_fee;
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Create rental record
        $sql = "INSERT INTO rentals (equipment_id, renter_id, start_date, end_date, pickup_location, return_location, special_instructions, total_amount, rental_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssssd", 
            $equipment_id, 
            $_SESSION['user_id'], 
            $start_date, 
            $end_date, 
            $pickup_location, 
            $return_location, 
            $special_instructions, 
            $total
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create rental record');
        }
        
        $rental_id = $conn->insert_id;
        
        // Create payment record
        $sql = "INSERT INTO payments (rental_id, amount, currency, payment_reference, platform_fee, owner_amount, status, created_at) 
                VALUES (?, ?, 'PHP', ?, ?, ?, 'pending', NOW())";
        
        $stmt = $conn->prepare($sql);
        $payment_reference = 'RENT_' . $rental_id;
        $stmt->bind_param("idsdd", 
            $rental_id, 
            $total, 
            $payment_reference, 
            $platform_fee, 
            $subtotal
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create payment record');
        }
        
        // Update equipment availability for the rental period
        $sql = "INSERT INTO equipment_availability (equipment_id, date, is_available, rental_id) VALUES (?, ?, 0, ?)";
        $stmt = $conn->prepare($sql);
        
        $current_date = clone $start;
        while ($current_date <= $end) {
            $date_str = $current_date->format('Y-m-d');
            $stmt->bind_param("isi", $equipment_id, $date_str, $rental_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update equipment availability');
            }
            
            $current_date->add(new DateInterval('P1D'));
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Rental request created successfully!',
            'rental_id' => $rental_id,
            'total_amount' => $total
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create rental: ' . $e->getMessage()
        ]);
    }
}

function checkEquipmentAvailability($equipment_id, $start_date, $end_date) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM equipment_availability 
            WHERE equipment_id = ? AND date BETWEEN ? AND ? AND is_available = 0";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $equipment_id, $start_date, $end_date);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] === 0; // Available if no conflicts
}

function getEquipmentDetails($equipment_id) {
    global $conn;
    
    $sql = "SELECT daily_rate, weekly_rate, monthly_rate FROM equipment WHERE equipment_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $equipment_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    return $result->fetch_assoc();
}

function getMyRentals() {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT r.*, e.name as equipment_name, e.daily_rate, u.first_name, u.last_name, u.city
            FROM rentals r 
            JOIN equipment e ON r.equipment_id = e.equipment_id 
            JOIN users u ON e.owner_id = u.user_id 
            WHERE r.renter_id = ? 
            ORDER BY r.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rentals = [];
    while ($row = $result->fetch_assoc()) {
        $start_date = new DateTime($row['start_date']);
        $end_date = new DateTime($row['end_date']);
        $days = $start_date->diff($end_date)->days + 1;
        
        $rentals[] = [
            'rental_id' => $row['rental_id'],
            'equipment_name' => $row['equipment_name'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'total_amount' => $row['total_amount'],
            'total_price_formatted' => '₱' . number_format($row['total_amount'], 2),
            'rental_status' => $row['rental_status'],
            'days' => $days,
            'owner_name' => $row['first_name'] . ' ' . $row['last_name'],
            'owner_city' => $row['city'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $rentals
    ]);
}

function getMyEquipment() {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT e.*, c.category_name,
            (SELECT COUNT(*) FROM rentals r WHERE r.equipment_id = e.equipment_id AND r.rental_status IN ('confirmed', 'active')) as active_rentals,
            (SELECT COUNT(*) FROM rentals r WHERE r.equipment_id = e.equipment_id) as total_rentals
            FROM equipment e 
            JOIN categories c ON e.category_id = c.category_id 
            WHERE e.owner_id = ? 
            ORDER BY e.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $equipment = [];
    while ($row = $result->fetch_assoc()) {
        $status = 'available';
        if ($row['active_rentals'] > 0) {
            $status = 'rented';
        } elseif (!$row['is_available']) {
            $status = 'maintenance';
        }
        
        $equipment[] = [
            'equipment_id' => $row['equipment_id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'daily_rate' => $row['daily_rate'],
            'daily_rate_formatted' => '₱' . number_format($row['daily_rate'], 2),
            'category_name' => $row['category_name'],
            'location' => $row['location'],
            'status' => $status,
            'is_available' => $row['is_available'],
            'active_rentals' => $row['active_rentals'],
            'total_rentals' => $row['total_rentals'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $equipment
    ]);
}

function getRentalStats() {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    
    // Get rental statistics
    $sql = "SELECT 
                COUNT(*) as total_rentals,
                SUM(CASE WHEN rental_status IN ('confirmed', 'active') THEN 1 ELSE 0 END) as active_rentals,
                SUM(CASE WHEN rental_status = 'pending' THEN 1 ELSE 0 END) as pending_rentals,
                SUM(CASE WHEN rental_status = 'completed' THEN 1 ELSE 0 END) as completed_rentals,
                SUM(CASE WHEN rental_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_rentals,
                SUM(total_amount) as total_spent
            FROM rentals 
            WHERE renter_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_rentals' => (int)$stats['total_rentals'],
            'active_rentals' => (int)$stats['active_rentals'],
            'pending_rentals' => (int)$stats['pending_rentals'],
            'completed_rentals' => (int)$stats['completed_rentals'],
            'cancelled_rentals' => (int)$stats['cancelled_rentals'],
            'total_spent' => (float)$stats['total_spent'],
            'total_spent_formatted' => '₱' . number_format($stats['total_spent'] ?? 0, 2)
        ]
    ]);
}

function getEquipmentStats() {
    global $conn;
    
    $user_id = $_SESSION['user_id'];
    
    // Get equipment statistics
    $sql = "SELECT 
                COUNT(*) as total_equipment,
                SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available_equipment,
                SUM(CASE WHEN is_available = 0 THEN 1 ELSE 0 END) as maintenance_equipment,
                (SELECT COUNT(DISTINCT r.equipment_id) FROM rentals r 
                 JOIN equipment e ON r.equipment_id = e.equipment_id 
                 WHERE e.owner_id = ? AND r.rental_status IN ('confirmed', 'active')) as rented_equipment,
                (SELECT SUM(r.total_amount) FROM rentals r 
                 JOIN equipment e ON r.equipment_id = e.equipment_id 
                 WHERE e.owner_id = ? AND r.rental_status = 'completed') as total_earnings
            FROM equipment 
            WHERE owner_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_equipment' => (int)$stats['total_equipment'],
            'available_equipment' => (int)$stats['available_equipment'],
            'rented_equipment' => (int)$stats['rented_equipment'],
            'maintenance_equipment' => (int)$stats['maintenance_equipment'],
            'total_earnings' => (float)$stats['total_earnings'],
            'total_earnings_formatted' => '₱' . number_format($stats['total_earnings'] ?? 0, 2)
        ]
    ]);
}
?>
