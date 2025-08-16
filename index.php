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
    <title>EquipRent - Equipment Rental Platform</title>
    <link rel="stylesheet" href="css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/regular.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔧</text></svg>">
</head>

<body>
    <?php include 'includes/navigation.php'; ?>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <!-- Carousel Background -->
        <div class="hero-carousel">
            <div class="carousel-slide active"></div>
            <div class="carousel-slide"></div>
            <div class="carousel-slide"></div>
            <div class="carousel-slide"></div>
        </div>
        
        <!-- Carousel Navigation Dots -->
        <div class="carousel-dots">
            <span class="carousel-dot active" data-slide="0"></span>
            <span class="carousel-dot" data-slide="1"></span>
            <span class="carousel-dot" data-slide="2"></span>
            <span class="carousel-dot" data-slide="3"></span>
        </div>
        
        <div class="hero-container">
            <div class="hero-content">
                <h1 class="hero-title">Rent Equipment, Build Dreams</h1>
                <p class="hero-subtitle">Access professional-grade equipment without the hefty price tag. Quality tools
                    for every project.</p>
                <div class="hero-buttons">
                    <button class="btn btn-primary" onclick="window.location.href='browse.php'">Browse Equipment</button>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <button class="btn btn-secondary" onclick="openEnlistModal()">Enlist Your Item</button>
                    <?php else: ?>
                        <button class="btn btn-secondary">Learn More</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-placeholder">
                    <i class="fas fa-tools" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Popular Categories</h2>
            <div class="categories-grid">
				<?php
					require_once __DIR__ . '/controller/db_connect.php';
					$fetchedCategories = [];
					try {
						$result = $conn->query("SELECT category_name FROM categories WHERE is_active = 1 ORDER BY category_name LIMIT 8");
						if ($result) {
							while ($row = $result->fetch_assoc()) {
								$fetchedCategories[] = $row['category_name'];
							}
						}
					} catch (Exception $e) {
						// ignore and fallback
					}
					
					function getCategoryIconClass($name) {
						$map = [
							'Construction' => 'fas fa-hammer',
							'Painting' => 'fas fa-paint-brush',
							'Gardening' => 'fas fa-leaf',
							'Photography' => 'fas fa-camera',
							'Tools' => 'fas fa-tools',
							'Kitchen Appliances' => 'fas fa-blender',
							'Heavy Equipment' => 'fas fa-truck',
							'Electronics' => 'fas fa-plug',
						];
						return $map[$name] ?? 'fas fa-box';
					}
					
					if (count($fetchedCategories) === 0) {
						$fetchedCategories = ['Construction','Painting','Gardening','Photography'];
					}
					
					foreach ($fetchedCategories as $catName):
						$iconClass = getCategoryIconClass($catName);
						$url = 'browse.php?category=' . urlencode($catName);
					?>
					<div class="category-item" tabindex="0" role="button" aria-label="<?php echo htmlspecialchars($catName); ?> category" onclick="window.location.href='<?php echo $url; ?>'" title="View <?php echo htmlspecialchars($catName); ?> equipment">
						<div class="category-icon">
							<i class="<?php echo $iconClass; ?>" aria-hidden="true"></i>
						</div>
						<div class="category-content">
							<h3><?php echo htmlspecialchars($catName); ?></h3>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="products" class="featured-products">
        <div class="container">
            <h2 class="section-title">Browse equipment</h2>
            <div class="products-grid">
                <div class="product-card" tabindex="0" role="button" aria-label="Professional Drill Set product">
                    <div class="product-image">
                        <div class="product-placeholder">
                            <i class="fas fa-drill" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3>Professional Drill Set</h3>
                        <p class="product-description">Complete drill set with multiple attachments</p>
                        <div class="product-meta">
                            <span class="price">$25/day</span>
                            <span class="rating"><i class="fas fa-star" aria-hidden="true"></i> 4.8</span>
                        </div>
                        <button class="btn btn-outline">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card" tabindex="0" role="button" aria-label="Extension Ladder product">
                    <div class="product-image">
                        <div class="product-placeholder">
                            <i class="fas fa-ladder" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3>Extension Ladder</h3>
                        <p class="product-description">24ft aluminum extension ladder</p>
                        <div class="product-meta">
                            <span class="price">$15/day</span>
                            <span class="rating"><i class="fas fa-star" aria-hidden="true"></i> 4.6</span>
                        </div>
                        <button class="btn btn-outline">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card" tabindex="0" role="button" aria-label="Circular Saw product">
                    <div class="product-image">
                        <div class="product-placeholder">
                            <i class="fas fa-saw" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3>Circular Saw</h3>
                        <p class="product-description">7-1/4" circular saw with blade</p>
                        <div class="product-meta">
                            <span class="price">$20/day</span>
                            <span class="rating"><i class="fas fa-star" aria-hidden="true"></i> 4.7</span>
                        </div>
                        <button class="btn btn-outline">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card" tabindex="0" role="button" aria-label="Air Compressor product">
                    <div class="product-image">
                        <div class="product-placeholder">
                            <i class="fas fa-compress-arrows-alt" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3>Air Compressor</h3>
                        <p class="product-description">20-gallon air compressor</p>
                        <div class="product-meta">
                            <span class="price">$35/day</span>
                            <span class="rating"><i class="fas fa-star" aria-hidden="true"></i> 4.9</span>
                        </div>
                        <button class="btn btn-outline">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Get Started?</h2>
                <p>Join thousands of satisfied customers who trust EquipRent for their equipment needs.</p>
                <button class="btn btn-primary" onclick="openAuthModal()">Sign Up Now</button>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
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
                            <input type="email" id="loginEmail" name="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div class="input-wrapper">
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

    <!-- Enlist Messages -->
    <?php if (isset($_SESSION['enlist_error']) && !empty($_SESSION['enlist_error'])): ?>
        <div class="enlist-error-message"><?php echo htmlspecialchars($_SESSION['enlist_error']); ?></div>
        <?php unset($_SESSION['enlist_error']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['enlist_success']) && !empty($_SESSION['enlist_success'])): ?>
        <div class="enlist-success-message"><?php echo htmlspecialchars($_SESSION['enlist_success']); ?></div>
        <?php unset($_SESSION['enlist_success']); ?>
    <?php endif; ?>

    <!-- Enlist Item Modal -->
    <div id="enlistModal" class="modal">
        <div class="modal-content enlist-modal">
            <!-- Header Section -->
            <div class="enlist-header">
                <div class="enlist-title">
                    <i class="fas fa-users"></i>
                    <h2>Equipment Management</h2>
                </div>
                <div class="enlist-header-buttons">
                    <button type="button" class="btn-add" onclick="submitEnlistForm()">
                        <i class="fas fa-plus"></i> Add New Equipment
                    </button>
                    <button type="button" class="btn-back" onclick="closeEnlistModal()">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="enlist-tabs">
                <button class="enlist-tab active" data-tab="dashboard">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </button>
                <button class="enlist-tab" data-tab="equipment">
                    <i class="fas fa-tools"></i>
                    Equipment
                </button>
                <button class="enlist-tab" data-tab="enlist">
                    <i class="fas fa-plus-circle"></i>
                    Enlist New
                </button>
            </div>

            <!-- Tab Content -->
            <!-- Dashboard Tab -->
            <div id="dashboard-tab" class="enlist-tab-content tab-dashboard active">
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <i class="fas fa-tools"></i>
                        <span class="stat-number">0</span>
                        <span class="stat-label">Total Equipment</span>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-check-circle"></i>
                        <span class="stat-number">0</span>
                        <span class="stat-label">Available</span>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clock"></i>
                        <span class="stat-number">0</span>
                        <span class="stat-label">Rented Out</span>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-wrench"></i>
                        <span class="stat-number">0</span>
                        <span class="stat-label">Maintenance</span>
                    </div>
                </div>
                
                <div class="dashboard-actions">
                    <button type="button" class="dashboard-btn" onclick="switchTab('equipment')">
                        <i class="fas fa-tools"></i>
                        View All Equipment
                    </button>
                    <button type="button" class="dashboard-btn secondary" onclick="switchTab('enlist')">
                        <i class="fas fa-plus-circle"></i>
                        Add New Equipment
                    </button>
                </div>
            </div>

            <!-- Equipment Tab -->
            <div id="equipment-tab" class="enlist-tab-content tab-equipment">
                <!-- Filter and Search Section -->
                <div class="enlist-filters">
                    <div class="filter-group">
                        <label>Category:</label>
                        <select id="filterCategory" class="filter-select">
                            <option value="">All Categories</option>
                            <option value="construction">Construction</option>
                            <option value="painting">Painting</option>
                            <option value="gardening">Gardening</option>
                            <option value="photography">Photography</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status:</label>
                        <select id="filterStatus" class="filter-select">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Search:</label>
                        <input type="text" id="searchEquipment" class="search-input" placeholder="Search by name or description">
                    </div>
                    <div class="filter-actions">
                        <button type="button" class="btn-filter">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button type="button" class="btn-clear">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>

                <!-- Summary Bar -->
                <div class="enlist-summary">
                    <div class="summary-item">
                        <i class="fas fa-users"></i>
                        <span>Equipment Management System</span>
                    </div>
                    <div class="summary-item">
                        <i class="fas fa-list"></i>
                        <span>Total Equipment: 0</span>
                    </div>
                    <div class="summary-item">
                        <i class="fas fa-eye"></i>
                        <span>Showing: 0 of 0</span>
                    </div>
                </div>

                <!-- Equipment Table -->
                <div class="enlist-table-container">
                    <table class="enlist-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-tools"></i> Equipment Name</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                                <th><i class="fas fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody id="equipmentTableBody">
                            <tr class="equipment-item">
                                <td>
                                    <div class="equipment-name">
                                        <i class="fas fa-drill"></i>
                                        <span>Professional Drill Set</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-available">Available</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="equipment-item">
                                <td>
                                    <div class="equipment-name">
                                        <i class="fas fa-ladder"></i>
                                        <span>Extension Ladder</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-rented">Rented</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="equipment-item">
                                <td>
                                    <div class="equipment-name">
                                        <i class="fas fa-saw"></i>
                                        <span>Circular Saw</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-maintenance">Maintenance</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Enlist Tab -->
            <div id="enlist-tab" class="enlist-tab-content tab-enlist">
                <div class="form-header">
                    <h3>Enlist New Equipment</h3>
                    <button type="button" class="btn-close-form" onclick="switchTab('equipment')">
                        <i class="fas fa-arrow-left"></i> Back to Equipment
                    </button>
                </div>
                
                <form class="enlist-form" method="POST" action="controller/enlist_item.php">
                    <div class="form-section">
                        <h4>Basic Information</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="itemName">Equipment Name</label>
                                <input type="text" id="itemName" name="item_name" placeholder="Enter equipment name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="itemCategory">Category</label>
                                <select id="itemCategory" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="construction">Construction</option>
                                    <option value="painting">Painting</option>
                                    <option value="gardening">Gardening</option>
                                    <option value="photography">Photography</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="itemDescription">Description</label>
                            <textarea id="itemDescription" name="description" rows="3"
                                placeholder="Describe your equipment, features, and condition..." required></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Pricing & Location</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="dailyRate">Daily Rate ($)</label>
                                <input type="number" id="dailyRate" name="daily_rate" min="1" step="0.01" placeholder="25.00" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="itemLocation">Location</label>
                                <input type="text" id="itemLocation" name="location" placeholder="City, State" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Media & Terms</h4>
                        <div class="form-group full-width">
                            <label for="itemPhotos">Photos (Optional)</label>
                            <div class="file-upload-area">
                                <input type="file" id="itemPhotos" name="photos[]" multiple accept="image/*">
                                <div class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Click to upload or drag and drop</p>
                                    <span>PNG, JPG, GIF up to 10MB</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="checkbox-container">
                                <input type="checkbox" name="terms" required>
                                <span class="checkmark"></span>
                                I agree to the <a href="#">Equipment Rental Terms</a>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="switchTab('equipment')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i> Enlist Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content product-modal">
            <span class="close" onclick="closeProductModal()">&times;</span>
            
            <div class="product-modal-content">
                <div class="product-modal-image">
                    <div class="product-modal-placeholder">
                        <i class="fas fa-tools" aria-hidden="true"></i>
                    </div>
                    <div class="product-badges">
                        <span class="badge badge-available">Available</span>
                        <span class="badge badge-featured">Featured</span>
                    </div>
                </div>
                
                <div class="product-modal-details">
                    <div class="product-header">
                        <h2 class="product-title">Professional Drill Set</h2>
                        <div class="product-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="rating-text">4.8 (24 reviews)</span>
                        </div>
                    </div>
                    
                    <div class="product-price-section">
                        <div class="price-main">
                            <span class="price-amount">$25</span>
                            <span class="price-period">/day</span>
                        </div>
                        <div class="price-options">
                            <span class="price-option">$150/week</span>
                            <span class="price-option">$500/month</span>
                        </div>
                    </div>
                    
                    <div class="product-description-full">
                        <h3>Description</h3>
                        <p>Professional-grade drill set perfect for construction, woodworking, and DIY projects. This comprehensive kit includes multiple drill bits, screwdriver attachments, and a carrying case for easy transport and storage.</p>
                        
                        <div class="product-features">
                            <h4>Features</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> 20V Lithium-ion battery</li>
                                <li><i class="fas fa-check"></i> 15+ drill bits included</li>
                                <li><i class="fas fa-check"></i> Variable speed control</li>
                                <li><i class="fas fa-check"></i> LED work light</li>
                                <li><i class="fas fa-check"></i> Ergonomic grip design</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="product-specs">
                        <h4>Specifications</h4>
                        <div class="specs-grid">
                            <div class="spec-item">
                                <span class="spec-label">Brand</span>
                                <span class="spec-value">DeWalt</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Model</span>
                                <span class="spec-value">DCD777C2</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Power Source</span>
                                <span class="spec-value">Battery</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Weight</span>
                                <span class="spec-value">3.4 lbs</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-location">
                        <h4><i class="fas fa-map-marker-alt"></i> Location</h4>
                        <p>Downtown Equipment Hub - 123 Main St, City, State</p>
                        <span class="location-distance">2.3 miles away</span>
                    </div>
                    
                    <div class="product-actions">
                        <div class="rental-options">
                            <div class="rental-dates">
                                <div class="date-input">
                                    <label for="startDate">Start Date</label>
                                    <input type="date" id="startDate" min="">
                                </div>
                                <div class="date-input">
                                    <label for="endDate">End Date</label>
                                    <input type="date" id="endDate" min="">
                                </div>
                            </div>
                            <div class="rental-summary">
                                <span class="rental-days">0 days</span>
                                <span class="rental-total">Total: $0</span>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="addToCart()">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="btn btn-secondary" onclick="chatWithSeller()">
                                <i class="fas fa-comments"></i> Chat with Seller
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="JS/modal-utils.js"></script>
    <script src="JS/auth.js"></script>
    <script src="JS/enlist-modal.js"></script>
    <script src="JS/product-modal.js"></script>
    <script src="JS/main.js"></script>
</body>

</html>