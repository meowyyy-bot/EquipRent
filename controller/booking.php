<?php
session_start();
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

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create_rental':
        createRental();
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
        $sql = "INSERT INTO rentals (equipment_id, renter_id, start_date, end_date, pickup_location, return_location, special_instructions, total_amount, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissssd", 
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
        $stmt->bind_param("idsdd", 
            $rental_id, 
            $total, 
            'RENT_' . $rental_id, 
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
?>
