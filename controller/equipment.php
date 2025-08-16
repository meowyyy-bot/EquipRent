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
        'error' => 'You must be logged in to manage equipment'
    ]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
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
?>