<?php
require_once __DIR__ . '/db_connect.php';

// Create users table if it doesn't exist - matching the structure from your SQL file
$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id int(11) NOT NULL AUTO_INCREMENT,
    username varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    password_hash varchar(255) NOT NULL,
    phone_number varchar(20) DEFAULT NULL,
    address text DEFAULT NULL,
    user_role enum('owner','customer','admin') DEFAULT 'customer',
    verification_status enum('pending','verified','rejected') DEFAULT 'pending',
    profile_photo varchar(255) DEFAULT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (user_id),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Check if test user exists
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$test_email = "test@example.com";
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Create test user
    $test_username = "Test User";
    $test_password = "password123";
    $password_hash = password_hash($test_password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, user_role, verification_status) VALUES (?, ?, ?, 'customer', 'verified')");
    $stmt->bind_param("sss", $test_username, $test_email, $password_hash);
    
    if ($stmt->execute()) {
        echo "Test user created successfully!<br>";
        echo "Username: Test User<br>";
        echo "Email: test@example.com<br>";
        echo "Password: password123<br>";
    } else {
        echo "Error creating test user: " . $stmt->error . "<br>";
    }
} else {
    echo "Test user already exists<br>";
}

// Show existing users
$result = $conn->query("SELECT user_id, username, email, user_role, verification_status FROM users");
if ($result->num_rows > 0) {
    echo "<h3>Existing Users:</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . $row['user_role'] . "</td>";
        echo "<td>" . $row['verification_status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$stmt->close();
$conn->close();

echo "<br><a href='../index.php'>Go back to main page</a>";
?>
