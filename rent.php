<?php
// Suppress warnings to prevent them from corrupting JSON responses
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

session_start();
require_once 'controller/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Required - EquipRent</title>
        <link rel="stylesheet" href="css/main.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔧</text></svg>">
        <style>
            .login-required-container {
                max-width: 600px;
                margin: 4rem auto;
                padding: 2rem;
                text-align: center;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            
            .login-icon {
                font-size: 4rem;
                color: #0d6efd;
                margin-bottom: 1rem;
            }
            
            .login-required-container h1 {
                color: #212529;
                margin-bottom: 1rem;
            }
            
            .login-required-container p {
                color: #6c757d;
                margin-bottom: 2rem;
                font-size: 1.1rem;
            }
            
            .btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 6px;
                font-size: 1rem;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
                text-align: center;
                transition: all 0.2s;
                margin: 0 0.5rem;
            }
            
            .btn-primary {
                background: #0d6efd;
                color: white;
            }
            
            .btn-primary:hover {
                background: #0b5ed7;
            }
            
            .btn-outline {
                background: transparent;
                color: #6c757d;
                border: 1px solid #6c757d;
            }
            
            .btn-outline:hover {
                background: #6c757d;
                color: white;
            }
        </style>
    </head>
    <body>
        <?php include 'includes/navigation.php'; ?>

        <div class="login-required-container">
            <div class="login-icon">
                <i class="fas fa-user-lock"></i>
            </div>
            
            <h1>Login Required</h1>
            <p>You need to be logged in to rent equipment. Please log in or create an account to continue.</p>
            
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="openAuthModal()">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
                <a href="browse.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Browse
                </a>
            </div>
        </div>

        <!-- Include auth modal -->
        <script src="JS/modal-utils.js"></script>
        <script src="JS/auth.js"></script>
        
        <script>
            function openAuthModal() {
                const modal = document.getElementById('authModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.classList.add('modal-open');
                }
            }
            
            async function logout() {
                try {
                    const response = await fetch('controller/logout.php', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Redirect to homepage
                        window.location.href = data.redirect;
                    }
                } catch (error) {
                    console.error('Logout error:', error);
                    // Fallback redirect
                    window.location.href = '/New folder/hackathin/index.php';
                }
            }
        </script>
    </body>
    </html>
    <?php
    exit();
}

// Get equipment ID from URL
$equipment_id = isset($_GET['equipment_id']) ? intval($_GET['equipment_id']) : 0;

if ($equipment_id <= 0) {
    header('Location: browse.php?error=invalid_equipment');
    exit();
}

// Get equipment details
<<<<<<< Updated upstream
$sql = "SELECT e.*, c.category_name, u.first_name, u.last_name, u.city, 
        COALESCE(u.average_rating, 0.00) as owner_rating, 
        COALESCE(u.total_reviews, 0) as total_reviews 
=======
$sql = "SELECT e.*, c.category_name, u.first_name, u.last_name, u.city, u.average_rating as owner_rating, u.total_reviews 
>>>>>>> Stashed changes
        FROM equipment e 
        JOIN categories c ON e.category_id = c.category_id 
        JOIN users u ON e.owner_id = u.user_id 
        WHERE e.equipment_id = ? AND e.is_available = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $equipment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: browse.php?error=equipment_not_found');
    exit();
}

$equipment = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Equipment - EquipRent</title>
    <link rel="stylesheet" href="css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔧</text></svg>">
    <style>
        .rent-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .rent-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .rent-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
        }
        
        .equipment-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }
        
        .equipment-image {
            width: 100%;
            height: 200px;
            background: #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .equipment-image i {
            font-size: 3rem;
            color: #6c757d;
        }
        
        .equipment-details h3 {
            margin: 0 0 0.5rem 0;
            color: #212529;
        }
        
        .equipment-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .pricing {
            background: #e7f3ff;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .price-main {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .rental-form {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #212529;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }
        
        .date-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .rental-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .summary-row.total {
            border-top: 1px solid #dee2e6;
            padding-top: 0.5rem;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #0d6efd;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0b5ed7;
        }
        
        .btn-outline {
            background: transparent;
            color: #6c757d;
            border: 1px solid #6c757d;
        }
        
        .btn-outline:hover {
            background: #6c757d;
            color: white;
        }
        
        .btn-block {
            width: 100%;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .rent-content {
                grid-template-columns: 1fr;
            }
            
            .date-inputs {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
        
        /* Reviews Section Styles */
        .reviews-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        
        .reviews-section h4 {
            margin: 0 0 1rem 0;
            color: #212529;
            font-size: 1.1rem;
        }
        
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .review-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .reviewer-name {
            font-weight: 500;
            color: #212529;
        }
        
        .review-rating {
            display: flex;
            gap: 2px;
        }
        
        .review-comment {
            margin: 0.5rem 0;
            color: #495057;
            line-height: 1.4;
        }
        
        .review-date {
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        .no-reviews {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px dashed #dee2e6;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>

    <div class="rent-container">
        <div class="rent-header">
            <h1>Rent Equipment</h1>
            <p>Complete your rental request for <?php echo htmlspecialchars($equipment['name']); ?></p>
        </div>

        <div class="rent-content">
            <!-- Equipment Summary -->
            <div class="equipment-summary">
                <div class="equipment-image">
                    <i class="fas fa-tools"></i>
                </div>
                
                <div class="equipment-details">
                    <h3><?php echo htmlspecialchars($equipment['name']); ?></h3>
                    <p><?php echo htmlspecialchars($equipment['description']); ?></p>
                    
                    <div class="equipment-meta">
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span><?php echo htmlspecialchars($equipment['category_name']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($equipment['city']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span><?php echo htmlspecialchars($equipment['first_name'] . ' ' . $equipment['last_name']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-star"></i>
                            <span><?php echo number_format($equipment['owner_rating'], 1); ?> (<?php echo $equipment['total_reviews']; ?> reviews)</span>
                        </div>
                    </div>
                    
                    <div class="pricing">
                        <div class="price-main">₱<?php echo number_format($equipment['daily_rate'] ?? 0, 2); ?> /day</div>
                        <?php if (isset($equipment['weekly_rate']) && $equipment['weekly_rate']): ?>
                            <div>₱<?php echo number_format($equipment['weekly_rate'], 2); ?> /week</div>
                        <?php endif; ?>
                        <?php if (isset($equipment['monthly_rate']) && $equipment['monthly_rate']): ?>
                            <div>₱<?php echo number_format($equipment['monthly_rate'], 2); ?> /month</div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Reviews Section -->
                    <div class="reviews-section">
                        <h4>Reviews</h4>
                        <?php
                        // Get reviews for this equipment
                        $reviews_sql = "SELECT r.rating, r.comment, r.created_at, u.first_name, u.last_name 
                                       FROM reviews r 
                                       JOIN users u ON r.reviewer_id = u.user_id 
                                       WHERE r.rental_id IN (
                                           SELECT rental_id FROM rentals WHERE equipment_id = ?
                                       ) 
                                       ORDER BY r.created_at DESC 
                                       LIMIT 5";
                        
                        $reviews_stmt = $conn->prepare($reviews_sql);
                        if ($reviews_stmt) {
                            $reviews_stmt->bind_param("i", $equipment_id);
                            $reviews_stmt->execute();
                            $reviews_result = $reviews_stmt->get_result();
                            
                            if ($reviews_result->num_rows > 0) {
                                echo '<div class="reviews-list">';
                                while ($review = $reviews_result->fetch_assoc()) {
                                    echo '<div class="review-item">';
                                    echo '<div class="review-header">';
                                    echo '<span class="reviewer-name">' . htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) . '</span>';
                                    echo '<span class="review-rating">';
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $review['rating']) {
                                            echo '<i class="fas fa-star" style="color: #ffc107;"></i>';
                                        } else {
                                            echo '<i class="far fa-star" style="color: #e4e5e9;"></i>';
                                        }
                                    }
                                    echo '</span>';
                                    echo '</div>';
                                    echo '<p class="review-comment">' . htmlspecialchars($review['comment']) . '</p>';
                                    echo '<small class="review-date">' . date('M j, Y', strtotime($review['created_at'])) . '</small>';
                                    echo '</div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<p class="no-reviews">No reviews yet. Be the first to review this equipment!</p>';
                            }
                            $reviews_stmt->close();
                        } else {
                            echo '<p class="no-reviews">Unable to load reviews at this time.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Rental Form -->
            <div class="rental-form">
                <form id="rentalForm" method="POST" action="controller/booking.php">
                    <input type="hidden" name="action" value="create_rental">
                    <input type="hidden" name="equipment_id" value="<?php echo $equipment_id; ?>">
                    
                    <div class="form-group">
                        <label for="start_date">Start Date *</label>
                        <input type="date" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">End Date *</label>
                        <input type="date" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="pickup_location">Pickup Location</label>
                        <input type="text" id="pickup_location" name="pickup_location" placeholder="Where would you like to pick up the equipment?">
                    </div>
                    
                    <div class="form-group">
                        <label for="return_location">Return Location</label>
                        <input type="text" id="return_location" name="return_location" placeholder="Where would you like to return the equipment?">
                    </div>
                    
                    <div class="form-group">
                        <label for="special_instructions">Special Instructions</label>
                        <textarea id="special_instructions" name="special_instructions" rows="3" placeholder="Any special requirements or instructions?"></textarea>
                    </div>
                    
                    <!-- Rental Summary -->
                    <div class="rental-summary">
                        <h4>Rental Summary</h4>
                        <div class="summary-row">
                            <span>Daily Rate:</span>
                            <span>₱<?php echo number_format($equipment['daily_rate'], 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Duration:</span>
                            <span id="duration">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Platform Fee (5%):</span>
                            <span id="platform_fee">-</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span id="total">-</span>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="browse.php" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-calendar-check"></i> Confirm Rental
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Calculate rental costs when dates change
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const dailyRate = <?php echo $equipment['daily_rate']; ?>;
        
        function calculateRental() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            if (startDate && endDate && startDate <= endDate) {
                const timeDiff = endDate.getTime() - startDate.getTime();
                const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // Include both start and end day
                
                const subtotal = dailyRate * daysDiff;
                const platformFee = subtotal * 0.05; // 5% platform fee
                const total = subtotal + platformFee;
                
                document.getElementById('duration').textContent = daysDiff + ' day(s)';
                document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
                document.getElementById('platform_fee').textContent = '₱' + platformFee.toFixed(2);
                document.getElementById('total').textContent = '₱' + total.toFixed(2);
            }
        }
        
        startDateInput.addEventListener('change', calculateRental);
        endDateInput.addEventListener('change', calculateRental);
        
        // Set minimum end date based on start date
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
        });
        
        // Handle form submission
        document.getElementById('rentalForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch('controller/booking.php', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid JSON response from server');
                }
                
                if (data.success) {
                    // Show success message
                    showMessage('success', data.message);
                    
                    // Redirect to browse page after a short delay
                    setTimeout(() => {
                        window.location.href = 'browse.php?success=rental_created';
                    }, 2000);
                } else {
                    showMessage('error', data.error || 'Failed to create rental request');
                }
            } catch (error) {
                console.error('Detailed error:', error);
                showMessage('error', `Network error: ${error.message}`);
            } finally {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
        
        function showMessage(type, message) {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('.message');
            existingMessages.forEach(msg => msg.remove());
            
            // Create message element
            const messageDiv = document.createElement('div');
            messageDiv.className = `message message-${type}`;
            messageDiv.innerHTML = `
                <div class="message-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            // Add styles
            messageDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                max-width: 400px;
                font-weight: 500;
            `;
            
            document.body.appendChild(messageDiv);
            
            // Animate in
            setTimeout(() => {
                messageDiv.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove after 5 seconds
            setTimeout(() => {
                messageDiv.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.parentNode.removeChild(messageDiv);
                    }
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>
