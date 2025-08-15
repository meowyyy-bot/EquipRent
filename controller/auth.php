<?php
session_start();
require_once __DIR__ . '/db_connect.php';

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
        header("Location: ../index.php");
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = 'Please enter a valid email address.';
        header("Location: ../index.php");
        exit;
    }

    // Check if user exists - using the correct column names from your database
    $stmt = $conn->prepare("SELECT user_id, username, email, password_hash, user_role, verification_status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        error_log("User found: " . $row['username'] . " - Password hash: " . $row['password_hash']);
        
        // Check verification status
        if ($row['verification_status'] === 'rejected') {
            $_SESSION['login_error'] = "Your account has been rejected. Please contact support.";
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
            $update_stmt->bind_param("si", $new_hash, $row['user_id']);
            $update_stmt->execute();
            $update_stmt->close();
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
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Invalid password.";
            header("Location: ../index.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = "User not found.";
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
        header("Location: ../index.php");
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = "Invalid email address.";
        header("Location: ../index.php");
        exit;
    } elseif ($password !== $confirm) {
        $_SESSION['register_error'] = "Passwords do not match.";
        header("Location: ../index.php");
        exit;
    } elseif (strlen($password) < 6) {
        $_SESSION['register_error'] = "Password must be at least 6 characters long.";
        header("Location: ../index.php");
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email already registered.";
        header("Location: ../index.php");
        exit;
    }
    $stmt->close();

    // Hash password and insert user - using the correct column names
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, user_role, verification_status) VALUES (?, ?, ?, 'customer', 'pending')");
    $stmt->bind_param("sss", $username, $email, $password_hash);
    
    if ($stmt->execute()) {
        $_SESSION['register_success'] = "Registration successful! You can now log in.";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['register_error'] = "Registration failed. Please try again.";
        header("Location: ../index.php");
        exit;
    }
    $stmt->close();
}

// If no valid action, redirect back
header("Location: ../index.php");
exit;
?>
