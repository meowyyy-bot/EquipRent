<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="EquipRent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="msapplication-tap-highlight" content="no">
    <title>About Us - EquipRent</title>
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/modal.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔧</text></svg>">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>EquipRent</h2>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php#home" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="index.php#products" class="nav-link">Browse</a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link active">About</a>
                </li>
            </ul>
            <div class="nav-actions">
                <button class="search-btn" aria-label="Search equipment"><i class="fas fa-search"></i></button>
                <button class="cart-btn" aria-label="View cart"><i class="fas fa-shopping-cart"></i><span
                        class="cart-count">0</span></button>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <div class="user-menu">
                        <span class="welcome-text">Welcome,
                            <?php echo htmlspecialchars($_SESSION['username'] ?? $_SESSION['full_name']); ?>!</span>
                        <button onclick="logout()" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                <?php else: ?>
                    <button class="login-btn" onclick="openAuthModal()">Login</button>
                <?php endif; ?>
            </div>
            <div class="hamburger" aria-label="Toggle navigation menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <!-- About Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="about-hero-content">
                <h1 class="about-hero-title">About EquipRent</h1>
                <p class="about-hero-subtitle">Connecting communities through shared resources and sustainable equipment rental solutions.</p>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="our-story">
        <div class="container">
            <div class="story-grid">
                <div class="story-content">
                    <h2>Our Story</h2>
                    <p>Founded in 2024, EquipRent was born from a simple observation: valuable equipment sits idle while others struggle to access the tools they need. We believe that sharing resources creates stronger, more sustainable communities.</p>
                    <p>What started as a local initiative has grown into a trusted platform connecting equipment owners with renters across the country. Our mission is to make professional-grade equipment accessible to everyone, from DIY enthusiasts to small businesses.</p>
                    <div class="story-stats">
                        <div class="stat-item">
                            <span class="stat-number">10,000+</span>
                            <span class="stat-label">Happy Customers</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">5,000+</span>
                            <span class="stat-label">Equipment Items</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Cities Served</span>
                        </div>
                    </div>
                </div>
                <div class="story-image">
                    <div class="story-placeholder">
                        <i class="fas fa-handshake" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How EquipRent Works</h2>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </div>
                    <h3>Browse Equipment</h3>
                    <p>Search through thousands of verified equipment items in your area, from power tools to heavy machinery.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i>
                    </div>
                    <h3>Book & Reserve</h3>
                    <p>Select your dates, review terms, and book securely through our platform with instant confirmation.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-tools" aria-hidden="true"></i>
                    </div>
                    <h3>Pick Up & Use</h3>
                    <p>Meet with the owner, inspect the equipment, and get to work on your project with confidence.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-star" aria-hidden="true"></i>
                    </div>
                    <h3>Return & Review</h3>
                    <p>Return equipment on time, leave a review, and help build our trusted community.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">What Our Community Says</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"EquipRent saved me hundreds on my home renovation project. The equipment was in perfect condition and the owner was incredibly helpful."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </div>
                        <div class="author-info">
                            <h4>David Thompson</h4>
                            <span>Homeowner</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"As a small contractor, I can't afford to buy all the equipment I need. EquipRent gives me access to professional tools without the investment."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </div>
                        <div class="author-info">
                            <h4>Lisa Martinez</h4>
                            <span>Contractor</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"I've been renting out my equipment for years and EquipRent makes it so easy. Great insurance coverage and excellent customer support."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </div>
                        <div class="author-info">
                            <h4>Robert Kim</h4>
                            <span>Equipment Owner</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>EquipRent</h3>
                    <p>Your trusted partner for equipment rental needs.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Follow us on Facebook"><i class="fab fa-facebook"
                                aria-hidden="true"></i></a>
                        <a href="#" aria-label="Follow us on Twitter"><i class="fab fa-twitter"
                                aria-hidden="true"></i></a>
                        <a href="#" aria-label="Follow us on Instagram"><i class="fab fa-instagram"
                                aria-hidden="true"></i></a>
                        <a href="#" aria-label="Follow us on LinkedIn"><i class="fab fa-linkedin"
                                aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php#home">Home</a></li>
                        <li><a href="index.php#products">Products</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Contact Support</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-phone" aria-hidden="true"></i> +1 (555) 123-4567</p>
                    <p><i class="fas fa-envelope" aria-hidden="true"></i> info@equiprent.com</p>
                    <p><i class="fas fa-map-marker-alt" aria-hidden="true"></i> 123 Equipment St, City, State</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 EquipRent. All rights reserved.</p>
            </div>
        </div>
    </footer>

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

                <form class="auth-form" method="POST" action="controller/auth.php">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="loginEmail">Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="loginEmail" name="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="loginPassword" placeholder="Password" name="password" required >
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

                <?php if (isset($_SESSION['login_error']) && !empty($_SESSION['login_error'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($_SESSION['login_error']); ?></div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['login_success']) && !empty($_SESSION['login_success'])): ?>
                    <div class="success-message"><?php echo htmlspecialchars($_SESSION['login_success']); ?></div>
                    <?php unset($_SESSION['login_success']); ?>
                <?php endif; ?>
            </div>

            <!-- Register Form -->
            <div id="registerForm" class="form-container">
                <h2>Create Account</h2>
                <p>Join our community today</p>

                <form class="auth-form" method="POST" action="controller/auth.php">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label for="registerName">Full Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="registerName" placeholder="Full Name" name="name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="registerEmail">Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="registerEmail" placeholder="Email" name="email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="registerPassword">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="registerPassword" placeholder="Password" name="password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('registerPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirmPassword" placeholder="Confirm Password" name="confirmPassword" required>
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

                <?php if (isset($_SESSION['register_error']) && !empty($_SESSION['register_error'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($_SESSION['register_error']); ?></div>
                    <?php unset($_SESSION['register_error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['register_success']) && !empty($_SESSION['register_success'])): ?>
                    <div class="success-message"><?php echo htmlspecialchars($_SESSION['register_success']); ?></div>
                    <?php unset($_SESSION['register_success']); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="JS/main.js"></script>
    <script src="JS/auth.js"></script>
</body>

</html>
