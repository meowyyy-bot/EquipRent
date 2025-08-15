<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Debug - EquipRent</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .session-data { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #ffe7e7; color: #d32f2f; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #e7ffe7; color: #388e3c; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Session Debug Information</h1>
    
    <div class="debug-info">
        <h3>Basic Session Info</h3>
        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>Session Status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active'; ?></p>
        <p><strong>Session Name:</strong> <?php echo session_name(); ?></p>
        <p><strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
    
    <div class="session-data">
        <h3>Session Variables</h3>
        <?php if (empty($_SESSION)): ?>
            <p class="error">No session variables found!</p>
        <?php else: ?>
            <ul>
            <?php foreach ($_SESSION as $key => $value): ?>
                <li><strong><?php echo htmlspecialchars($key); ?>:</strong> 
                    <?php echo is_array($value) ? 'Array' : htmlspecialchars($value); ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    
    <div class="debug-info">
        <h3>Login Status Check</h3>
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <p class="success">✅ You are logged in!</p>
            <p><strong>User ID:</strong> <?php echo $_SESSION['user_id'] ?? 'Not set'; ?></p>
            <p><strong>Username:</strong> <?php echo $_SESSION['username'] ?? 'Not set'; ?></p>
            <p><strong>Email:</strong> <?php echo $_SESSION['email'] ?? 'Not set'; ?></p>
        <?php else: ?>
            <p class="error">❌ You are NOT logged in!</p>
            <p><strong>logged_in value:</strong> <?php echo isset($_SESSION['logged_in']) ? var_export($_SESSION['logged_in'], true) : 'Not set'; ?></p>
        <?php endif; ?>
    </div>
    
    <div class="debug-info">
        <h3>Test Links</h3>
        <p><a href="browse.php">Go to Browse Page</a></p>
        <p><a href="rent.php?equipment_id=1">Test Rent Page (Equipment ID 1)</a></p>
        <p><a href="index.php">Go to Homepage</a></p>
    </div>
    
    <div class="debug-info">
        <h3>Actions</h3>
        <p><a href="controller/logout.php">Logout</a></p>
        <p><a href="debug_session.php">Refresh This Page</a></p>
    </div>
</body>
</html>
