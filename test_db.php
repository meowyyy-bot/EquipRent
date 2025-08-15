<?php
require_once 'controller/db_connect.php';

echo "<h2>Database Connection Test</h2>";

if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    echo "<p>Server: " . $conn->server_info . "</p>";
    echo "<p>Database: " . $dbname . "</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
}

// Test if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Users table exists</p>";
    
    // Count users
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "<p>Total users: " . $row['count'] . "</p>";
    
    // Show all users (without passwords)
    $result = $conn->query("SELECT user_id, username, email, user_role, verification_status, created_at FROM users ORDER BY user_id");
    if ($result->num_rows > 0) {
        echo "<h3>All Users in Database:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th>";
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            $status_color = $row['verification_status'] === 'verified' ? 'green' : 
                           ($row['verification_status'] === 'rejected' ? 'red' : 'orange');
            echo "<tr>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . $row['user_role'] . "</td>";
            echo "<td style='color: " . $status_color . ";'>" . $row['verification_status'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>✗ Users table does not exist</p>";
    echo "<p><a href='controller/setup_db.php'>Click here to create the database table</a></p>";
}

$conn->close();
?>

<br>
<a href="index.php">Go to main page</a> | 
<a href="controller/setup_db.php">Setup Database</a>
