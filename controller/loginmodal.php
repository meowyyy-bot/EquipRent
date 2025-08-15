<?php
session_start();

// Check if user is already logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                handleLogin();
                break;
            case 'register':
                handleRegister();
                break;
        }
    }
}

function handleLogin() {
    global $conn;
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Basic validation
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields';
        return;
    }
    
    // Here you would typically:
    // 1. Connect to database
    // 2. Check user credentials
    // 3. Verify password hash
    // 4. Set session variables
    
    // For demo purposes, let's simulate a successful login
    if ($username === 'admin' && $password === 'password') {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Administrator';
        $_SESSION['success'] = 'Login successful!';
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid username or password';
    }
}

function handleRegister() {
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $terms = isset($_POST['terms']);
    
    // Basic validation
    if (empty($name) || empty($username) || empty($password) || empty($confirmPassword)) {
        $_SESSION['error'] = 'Please fill in all fields';
        return;
    }
    
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = 'Passwords do not match';
        return;
    }
    
    if (!$terms) {
        $_SESSION['error'] = 'You must agree to the terms and conditions';
        return;
    }
    
    // Here you would typically:
    // 1. Connect to database
    // 2. Check if username already exists
    // 3. Hash password
    // 4. Insert new user
    // 5. Set session variables
    
    // For demo purposes, let's simulate a successful registration
    $_SESSION['success'] = 'Account created successfully! You can now login.';
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hackathin - Login/Register</title>
    <link rel="stylesheet" href="../css/homepage.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar"
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Hackathin</h2>
            </div>
            <div class="nav-menu">
                <?php if ($isLoggedIn): ?>
                    <div class="user-menu">
                        <span class="welcome-text">Welcome, <?php echo htmlspecialchars($userName); ?>!</span>
                        <a href="?logout=1" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                <?php else: ?>
                    <button class="auth-btn" onclick="openAuthModal()">
                        <i class="fas fa-user"></i> Login / Register
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="hero-section">
            <h1>Welcome to Hackathin</h1>
            <p>Your platform for innovation and collaboration</p>
            <?php if (!$isLoggedIn): ?>
                <button class="cta-btn" onclick="openAuthModal()">Get Started</button>
            <?php else: ?>
                <div class="user-actions">
                    <a href="dashboard.php" class="cta-btn">Go to Dashboard</a>
                    <a href="profile.php" class="secondary-btn">View Profile</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="flash-message error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($_SESSION['error']); ?>
            <button onclick="this.parentElement.remove()" class="close-flash">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="flash-message success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($_SESSION['success']); ?>
            <button onclick="this.parentElement.remove()" class="close-flash">&times;</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Auth Modal -->
    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAuthModal()">&times;</span>
            
            <!-- Tab Navigation -->
            <div class="tab-container">
                <button class="tab-btn active" onclick="switchTab('login')">Login</button>
                <button class="tab-btn" onclick="switchTab('register')">Register</button>
            </div>

            <!-- Login Form -->
            <div id="loginForm" class="form-container active">
                <h2>Welcome Back</h2>
                <p>Sign in to your account</p>
                
                <form class="auth-form" method="POST" action="">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="loginUsername">Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="loginUsername" name="username" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="loginPassword" name="password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('loginPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form> 

            <!-- Register Form -->
            <div id="registerForm" class="form-container">
                <h2>Create Account</h2>
                <p>Join our community today</p>
                
                <form class="auth-form" method="POST" action="">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label for="registerName">Full Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="registerName" name="name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="registerUsername">Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-at"></i>
                            <input type="text" id="registerUsername" name="username" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="registerPassword">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="registerPassword" name="password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('registerPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="terms" required>
                            <span class="checkmark"></span>
                            I agree to the <a href="#">Terms & Conditions</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <div class="social-login">
                    <p>Or register with</p>
                    <div class="social-buttons">
                        <button type="button" class="social-btn google" onclick="handleGoogleLogin()">
                            <i class="fab fa-google"></i> Google
                        </button>
                        <button type="button" class="social-btn facebook" onclick="handleFacebookLogin()">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/auth.js"></script>
</body>
</html>
