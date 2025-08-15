<?php
session_start();
require_once __DIR__ . '/db_connect.php';

$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']); // This is the email field
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
        } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id, full_name, email, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verify password
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['logged_in'] = true;
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
}
?>