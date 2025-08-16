<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Equipment - EquipRent</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/browse.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔧</text></svg>">
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
                    <a href="index.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="browse.php" class="nav-link active">Browse</a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link">About</a>
                </li>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <li class="nav-item mobile-only">
                        <button onclick="logout()" class="nav-link logout-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="nav-actions">
                <button class="search-btn" aria-label="Search equipment"><i class="fas fa-search"></i></button>
                <button class="cart-btn" aria-label="View cart"><i class="fas fa-shopping-cart"></i><span class="cart-count">0</span></button>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <span class="welcome-text-mobile mobile-only">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? $_SESSION['full_name']); ?>!</span>
                    <div class="user-menu desktop-only">
                        <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? $_SESSION['full_name']); ?>!</span>
                        <button onclick="logout()" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                <?php else: ?>
                    <button onclick="openAuthModal()" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                <?php endif; ?>
            </div>
            <div class="hamburger" aria-label="Toggle navigation menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <!-- Browse Header -->
    <section class="browse-header">
        <div class="container">
            <h1>Browse Equipment</h1>
            <p>Find the perfect tools and equipment for your next project</p>
            
            <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
                <div class="login-notice" style="background: #e7f3ff; color: #0d6efd; padding: 1rem; border-radius: 8px; margin-top: 1rem; text-align: center; border: 1px solid #b3d9ff;">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Login Required:</strong> You need to be logged in to rent equipment. 
                    <button onclick="openAuthModal()" style="background: #0d6efd; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; margin-left: 1rem; cursor: pointer;">
                        <i class="fas fa-sign-in-alt"></i> Log In
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success']) && $_GET['success'] === 'rental_created'): ?>
                <div class="success-message" style="background: #10b981; color: white; padding: 1rem; border-radius: 8px; margin-top: 1rem; text-align: center;">
                    <i class="fas fa-check-circle"></i> Rental request created successfully! Please wait for owner confirmation.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" style="background: #ef4444; color: white; padding: 1rem; border-radius: 8px; margin-top: 1rem; text-align: center;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Filters and Search -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-container">
                <!-- Search Bar -->
                <div class="search-container">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search equipment, brands, or descriptions...">
                        <button type="button" id="searchBtn" class="search-btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="filter-controls">
                    <div class="filter-group">
                        <label for="categoryFilter">Category</label>
                        <select id="categoryFilter" class="filter-select">
                            <option value="">All Categories</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="locationFilter">Location</label>
                        <select id="locationFilter" class="filter-select">
                            <option value="">All Locations</option>
                            <option value="Manila">Manila</option>
                            <option value="Quezon City">Quezon City</option>
                            <option value="Makati">Makati</option>
                            <option value="Taguig">Taguig</option>
                            <option value="Pasig">Pasig</option>
                            <option value="Marikina">Marikina</option>
                            <option value="Caloocan">Caloocan</option>
                            <option value="Las Piñas">Las Piñas</option>
                            <option value="Parañaque">Parañaque</option>
                            <option value="Muntinlupa">Muntinlupa</option>
                            <option value="Malabon">Malabon</option>
                            <option value="Navotas">Navotas</option>
                            <option value="San Juan">San Juan</option>
                            <option value="Mandaluyong">Mandaluyong</option>
                            <option value="Valenzuela">Valenzuela</option>
                            <option value="Pateros">Pateros</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="priceRangeFilter">Price Range</label>
                        <select id="priceRangeFilter" class="filter-select">
                            <option value="">Any Price</option>
                            <option value="0-500">Under ₱500/day</option>
                            <option value="500-1000">₱500 - ₱1,000/day</option>
                            <option value="1000-2000">₱1,000 - ₱2,000/day</option>
                            <option value="2000-5000">₱2,000 - ₱5,000/day</option>
                            <option value="5000+">Over ₱5,000/day</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="sortFilter">Sort By</label>
                        <select id="sortFilter" class="filter-select">
                            <option value="created_at-DESC">Newest First</option>
                            <option value="created_at-ASC">Oldest First</option>
                            <option value="daily_rate-ASC">Price: Low to High</option>
                            <option value="daily_rate-DESC">Price: High to Low</option>
                            <option value="owner_rating-DESC">Highest Rated</option>
                            <option value="item_name-ASC">Name: A to Z</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="button" id="clearFilters" class="btn btn-outline">
                            <i class="fas fa-times"></i> Clear Filters
                        </button>
                        <button type="button" id="applyFilters" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="results-section">
        <div class="container">
            <!-- Results Header -->
            <div class="results-header">
                <div class="results-info">
                    <span id="resultsCount">0</span> equipment found
                </div>
                <div class="view-options">
                    <button type="button" class="view-btn active" data-view="grid">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="view-btn" data-view="list">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="loading-state" style="display: none;">
                <div class="loading-spinner"></div>
                <p>Loading equipment...</p>
            </div>

            <!-- No Results State -->
            <div id="noResultsState" class="no-results-state" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>No equipment found</h3>
                <p>Try adjusting your filters or search terms</p>
                <button type="button" id="resetSearch" class="btn btn-primary">Reset Search</button>
            </div>

            <!-- Equipment Grid -->
            <div id="equipmentGrid" class="equipment-grid">
                <!-- Equipment items will be loaded here -->
            </div>

            <!-- Pagination -->
            <div id="pagination" class="pagination" style="display: none;">
                <!-- Pagination controls will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content quick-view-modal">
            <span class="close" onclick="closeQuickViewModal()">&times;</span>
            <div id="quickViewContent">
                <!-- Quick view content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Auth Modal (reused from index.php) -->
    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAuthModal()">&times;</span>

            <!-- Tab Navigation -->
            <div class="tab-container">
                <button class="tab-btn active" onclick="switchTab('login')" data-tab="login">Login</button>
                <button class="tab-btn" onclick="switchTab('register')" data-tab="register">Register</button>
            </div>

            <!-- Login Form -->
            <div id="loginForm" class="form-container active" style="display: block;">
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
            <div id="registerForm" class="form-container" style="display: none;">
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>EquipRent</h3>
                    <p>Your trusted partner for equipment rental needs.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Follow us on Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Follow us on Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Follow us on Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Follow us on LinkedIn"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="browse.php">Browse</a></li>
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
                    <p><i class="fas fa-phone"></i> +63 (2) 1234-5678</p>
                    <p><i class="fas fa-envelope"></i> info@equiprent.ph</p>
                    <p><i class="fas fa-map-marker-alt"></i> Metro Manila, Philippines</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 EquipRent. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Include JavaScript -->
    <script src="JS/modal-utils.js"></script>
    <script src="JS/auth.js"></script>
    <script src="JS/browse.js"></script>
    
    <script>
        // Set global login status for JavaScript
        window.isUserLoggedIn = <?php echo (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) ? 'true' : 'false'; ?>;
    </script>
</body>
</html>
