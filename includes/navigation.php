<?php
// Global Navigation Component
// This file should be included in all pages that need navigation

// Ensure session is started (but don't start if already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine current page for active navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Check if user is logged in
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_name = $is_logged_in ? ($_SESSION['username'] ?? $_SESSION['full_name'] ?? 'User') : '';

// Navigation configuration
$nav_items = [
    'home' => [
        'url' => 'index.php',
        'label' => 'Home',
        'show_always' => true
    ],
    'browse' => [
        'url' => 'browse.php', 
        'label' => 'Browse',
        'show_always' => true
    ],
    'dashboard' => [
        'url' => 'dashboard.php',
        'label' => 'Dashboard', 
        'show_only_logged_in' => true
    ],
    'about' => [
        'url' => 'about.php',
        'label' => 'About',
        'show_always' => true
    ]
];
?>

<!-- Navigation -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <h2>EquipRent</h2>
        </div>
        <ul class="nav-menu">
            <?php foreach ($nav_items as $key => $item): ?>
                <?php 
                // Determine if this nav item should be shown
                $show_item = false;
                if (isset($item['show_always']) && $item['show_always']) {
                    $show_item = true;
                } elseif (isset($item['show_only_logged_in']) && $item['show_only_logged_in'] && $is_logged_in) {
                    $show_item = true;
                }
                
                if ($show_item):
                    // Determine if this is the current page
                    $is_active = false;
                    if ($key === 'home' && ($current_page === 'index' || $current_page === '')) {
                        $is_active = true;
                    } elseif ($key === $current_page) {
                        $is_active = true;
                    }
                    
                    $active_class = $is_active ? ' active' : '';
                ?>
                <li class="nav-item">
                    <a href="<?php echo htmlspecialchars($item['url']); ?>" class="nav-link<?php echo $active_class; ?>">
                        <?php echo htmlspecialchars($item['label']); ?>
                    </a>
                </li>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <!-- Mobile-only logout for logged in users -->
            <?php if ($is_logged_in): ?>
                <li class="nav-item mobile-only">
                    <button onclick="logout()" class="nav-link logout-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </li>
            <?php endif; ?>
        </ul>
        
        <div class="nav-actions">
            <!-- Search and Cart buttons (show on all pages except rent when not logged in) -->
            <?php if (!($current_page === 'rent' && !$is_logged_in)): ?>
                <button class="search-btn" aria-label="Search equipment"><i class="fas fa-search"></i></button>
                <button class="cart-btn" aria-label="View cart"><i class="fas fa-shopping-cart"></i><span class="cart-count">0</span></button>
            <?php endif; ?>
            
            <?php if ($is_logged_in): ?>
                <!-- Mobile welcome message -->
                <span class="welcome-text-mobile mobile-only">Welcome, <?php echo htmlspecialchars($user_name); ?>!</span>
                
                <!-- Desktop user menu -->
                <div class="user-menu desktop-only">
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($user_name); ?>!</span>
                    <button onclick="logout()" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            <?php else: ?>
                <!-- Login button for non-logged in users -->
                <button class="login-btn" onclick="openAuthModal()">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            <?php endif; ?>
        </div>
        
        <!-- Mobile hamburger menu -->
        <div class="hamburger" aria-label="Toggle navigation menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>
</nav>
