<?php
session_start();

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

if ($isAjax) {
    // Return JSON response for AJAX requests
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully',
        'redirect' => '../index.php'
    ]);
    exit;
}

// Redirect to home page for regular requests
header("Location: ../index.php");
exit();
?>