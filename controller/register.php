<?php
session_start();
require_once __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $full_name = trim($_POST['name']); // matches name="name"
    $email = trim($_POST['email']); // matches name="email"
    $password = $_POST['password'];
    $confirm = $_POST['confirmPassword'];

    // Basic validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm)) {
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
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['register_error'] = "Email already registered.";
            header("Location: ../index.php");
            exit;
        } else {
            // Hash password and insert user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $full_name, $email, $password_hash);
            if ($stmt->execute()) {
                $_SESSION['register_success'] = "Registration successful! You can now log in.";
                header("Location: ../index.php");
                exit;
            } else {
                $_SESSION['register_error'] = "Registration failed. Please try again.";
                header("Location: ../index.php");
                exit;
            }
        }
        $stmt->close();
    }
}
?>