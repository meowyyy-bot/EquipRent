<?php
session_start();
require_once 'db_connect.php';

// Debug database connection
error_log("Database connection status: " . ($conn->connect_error ? "Failed: " . $conn->connect_error : "Success"));
error_log("Database server info: " . $conn->server_info);
error_log("Database host info: " . $conn->host_info);

// Test database functionality
$test_result = $conn->query("SELECT 1");
if ($test_result === false) {
    error_log("Basic query test failed: " . $conn->error);
} else {
    error_log("Basic query test passed");
    $test_result->free();
}

// Check if this is a session status check request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check_status') {
    header('Content-Type: application/json');
    
    $response = [
        'success' => true,
        'logged_in' => isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true,
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null
    ];
    
    echo json_encode($response);
    exit;
}

// Debug: Log the request
error_log("Auth request: " . $_SERVER['REQUEST_METHOD'] . " - Action: " . ($_POST['action'] ?? 'none'));
error_log("POST data: " . print_r($_POST, true));

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Please fill in all fields.';
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Please fill in all fields.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = 'Please enter a valid email address.';
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Please enter a valid email address.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    }

    // Check if user exists - using the correct column names from your database
    $stmt = $conn->prepare("SELECT user_id, username, email, password_hash, user_role, verification_status FROM users WHERE email = ?");
    
    // Add error handling for prepare statement
    if ($stmt === false) {
        error_log("Prepare failed for SELECT users: " . $conn->error);
        error_log("SQL State: " . $conn->sqlstate);
        error_log("Error Code: " . $conn->errno);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Database error occurred. Please try again.'
            ]);
            exit;
        }
        
        $_SESSION['login_error'] = "Database error occurred. Please try again.";
        header("Location: ../index.php");
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        error_log("User found: " . $row['username'] . " - Password hash: " . $row['password_hash']);
        
        // Check verification status
        if ($row['verification_status'] === 'rejected') {
            $_SESSION['login_error'] = "Your account has been rejected. Please contact support.";
            
            // Check if this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Your account has been rejected. Please contact support.'
                ]);
                exit;
            }
            
            header("Location: ../index.php");
            exit;
        }
        
        // Verify password - handle both hashed and plain text passwords for existing data
        $password_valid = false;
        
        // First try password_verify for hashed passwords
        if (password_verify($password, $row['password_hash'])) {
            error_log("Password verified with password_verify");
            $password_valid = true;
        }
        // If that fails, check if it's a plain text password (for existing data)
        elseif ($password === $row['password_hash']) {
            error_log("Password matched as plain text");
            $password_valid = true;
            // Update the password to be hashed for security
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            
            // Add error handling for prepare statement
            if ($update_stmt === false) {
                error_log("Prepare failed for UPDATE users: " . $conn->error);
                error_log("SQL State: " . $conn->sqlstate);
                error_log("Error Code: " . $conn->errno);
                // Continue without updating the password
            } else {
                $update_stmt->bind_param("si", $new_hash, $row['user_id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
        } else {
            error_log("Password verification failed. Input: '$password', Hash: '" . $row['password_hash'] . "'");
        }
        
        if ($password_valid) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['full_name'] = $row['username']; // Use username as full_name for compatibility
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_role'] = $row['user_role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_success'] = "Welcome back, " . $row['username'] . "!";
            
            // Check if this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => '../index.php'
                ]);
                exit;
            }
            
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Invalid password.";
            
            // Check if this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid password.'
                ]);
                exit;
            }
            
            header("Location: ../index.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = "User not found.";
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'User not found.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    }
    $stmt->close();
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['name']); // This will be stored as username
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirmPassword'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $_SESSION['register_error'] = "All fields are required.";
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'All fields are required.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = "Invalid email address.";
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Invalid email address.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    } elseif ($password !== $confirm) {
        $_SESSION['register_error'] = "Passwords do not match.";
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Passwords do not match.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    } elseif (strlen($password) < 6) {
        $_SESSION['register_error'] = "Password must be at least 6 characters long.";
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Password must be at least 6 characters long.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    
    // Add error handling for prepare statement
    if ($stmt === false) {
        error_log("Prepare failed for SELECT users (registration): " . $conn->error);
        error_log("SQL State: " . $conn->sqlstate);
        error_log("Error Code: " . $conn->errno);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Database error occurred. Please try again.'
            ]);
            exit;
        }
        
        $_SESSION['register_error'] = "Database error occurred. Please try again.";
        header("Location: ../index.php");
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email already registered.";
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Email already registered.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    }
    $stmt->close();

    // Hash password and insert user - using the correct column names
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, user_role, verification_status) VALUES (?, ?, ?, 'customer', 'pending')");
    
    // Add error handling for prepare statement
    if ($stmt === false) {
        error_log("Prepare failed for INSERT users: " . $conn->error);
        error_log("SQL State: " . $conn->sqlstate);
        error_log("Error Code: " . $conn->errno);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Database error occurred. Please try again.'
            ]);
            exit;
        }
        
        $_SESSION['register_error'] = "Database error occurred. Please try again.";
        header("Location: ../index.php");
        exit;
    }
    
    $stmt->bind_param("sss", $username, $email, $password_hash);
    
    if ($stmt->execute()) {
        $_SESSION['register_success'] = "Registration successful! You can now log in.";
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful! You can now log in.',
                'redirect' => '../index.php'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['register_error'] = "Registration failed. Please try again.";
        
        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Registration failed. Please try again.'
            ]);
            exit;
        }
        
        header("Location: ../index.php");
        exit;
    }
    $stmt->close();
}

// If no valid action, redirect back
header("Location: ../index.php");
exit;
?>
