<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EquipRent</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/browse.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔧</text></svg>">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Welcome to Your Dashboard</h1>
            <p>Manage your equipment rentals and listings</p>
        </div>

        <div class="dashboard-grid">
            <!-- My Rentals Card -->
            <div class="dashboard-card">
                <h2><i class="fas fa-calendar-alt"></i> My Rentals</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number" id="totalRentals">0</span>
                        <span class="stat-label">Total</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="activeRentals">0</span>
                        <span class="stat-label">Active</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="pendingRentals">0</span>
                        <span class="stat-label">Pending</span>
                    </div>
                </div>
                <div class="rental-list" id="rentalList">
                    <div class="no-rentals">
                        <i class="fas fa-calendar-times"></i>
                        <p>No rentals yet</p>
                    </div>
                </div>
            </div>

            <!-- My Equipment Card -->
            <div class="dashboard-card">
                <h2><i class="fas fa-tools"></i> My Equipment</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number" id="totalEquipment">0</span>
                        <span class="stat-label">Total</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="availableEquipment">0</span>
                        <span class="stat-label">Available</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="rentedEquipment">0</span>
                        <span class="stat-label">Rented</span>
                    </div>
                </div>
                <div class="rental-list" id="equipmentList">
                    <div class="no-rentals">
                        <i class="fas fa-tools"></i>
                        <p>No equipment listed yet</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-actions">
            <a href="browse.php" class="dashboard-btn primary">
                <i class="fas fa-search"></i> Browse Equipment
            </a>
            <button class="dashboard-btn secondary" onclick="openEnlistModal()">
                <i class="fas fa-plus"></i> List Equipment
            </button>
            <a href="profile.php" class="dashboard-btn outline">
                <i class="fas fa-user"></i> Edit Profile
            </a>
        </div>
    </div>

    <!-- Enlist Modal (reused from index.php) -->
    <div id="enlistModal" class="modal">
        <div class="modal-content enlist-modal">
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

            <div id="equipment-tab" class="enlist-tab-content tab-equipment">
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
                        </tbody>
                    </table>
                </div>
            </div>

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
                                <label for="dailyRate">Daily Rate (₱)</label>
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

    <script src="JS/modal-utils.js"></script>
    <script src="JS/enlist-modal.js"></script>
    <script src="JS/main.js"></script>
    <script>
        // Dashboard functionality
        class Dashboard {
            constructor() {
                this.init();
            }
            
            init() {
                this.loadRentals();
                this.loadEquipment();
            }
            
            async loadRentals() {
                try {
                    const response = await fetch('controller/booking.php?action=my_rentals');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.displayRentals(data.data);
                        this.updateRentalStats(data.data);
                    }
                } catch (error) {
                    console.error('Failed to load rentals:', error);
                }
            }
            
            async loadEquipment() {
                try {
                    // This would load user's own equipment
                    // For now, we'll show placeholder data
                    this.displayEquipment([]);
                } catch (error) {
                    console.error('Failed to load equipment:', error);
                }
            }
            
            displayRentals(rentals) {
                const rentalList = document.getElementById('rentalList');
                
                if (rentals.length === 0) {
                    rentalList.innerHTML = `
                        <div class="no-rentals">
                            <i class="fas fa-calendar-times"></i>
                            <p>No rentals yet</p>
                        </div>
                    `;
                    return;
                }
                
                let rentalHTML = '';
                rentals.forEach(rental => {
                    const startDate = new Date(rental.start_date).toLocaleDateString();
                    const endDate = new Date(rental.end_date).toLocaleDateString();
                    
                    rentalHTML += `
                        <div class="rental-item">
                            <div class="rental-info">
                                <h4>${rental.item_name}</h4>
                                <p>${startDate} - ${endDate} • ${rental.total_price_formatted}</p>
                            </div>
                            <span class="rental-status status-${rental.rental_status}">
                                ${rental.rental_status}
                            </span>
                        </div>
                    `;
                });
                
                rentalList.innerHTML = rentalHTML;
            }
            
            displayEquipment(equipment) {
                const equipmentList = document.getElementById('equipmentList');
                
                if (equipment.length === 0) {
                    equipmentList.innerHTML = `
                        <div class="no-rentals">
                            <i class="fas fa-tools"></i>
                            <p>No equipment listed yet</p>
                        </div>
                    `;
                    return;
                }
                
                // Display equipment items
                let equipmentHTML = '';
                equipment.forEach(item => {
                    equipmentHTML += `
                        <div class="rental-item">
                            <div class="rental-info">
                                <h4>${item.item_name}</h4>
                                <p>${item.daily_rate_formatted}/day • ${item.location}</p>
                            </div>
                            <span class="rental-status status-${item.status}">
                                ${item.status}
                            </span>
                        </div>
                    `;
                });
                
                equipmentList.innerHTML = equipmentHTML;
            }
            
            updateRentalStats(rentals) {
                const total = rentals.length;
                const active = rentals.filter(r => ['confirmed', 'ongoing'].includes(r.rental_status)).length;
                const pending = rentals.filter(r => r.rental_status === 'pending').length;
                
                document.getElementById('totalRentals').textContent = total;
                document.getElementById('activeRentals').textContent = active;
                document.getElementById('pendingRentals').textContent = pending;
            }
        }
        
        // Global functions for modal interactions
        function openEnlistModal() {
            const modal = document.getElementById('enlistModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.classList.add('modal-open');
            }
        }
        
        function closeEnlistModal() {
            const modal = document.getElementById('enlistModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }
        
        function switchTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.enlist-tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.enlist-tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            const selectedTabContent = document.getElementById(`${tabName}-tab`);
            if (selectedTabContent) {
                selectedTabContent.classList.add('active');
            }
            
            // Activate selected tab button
            const selectedTab = document.querySelector(`[data-tab="${tabName}"]`);
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
        }
        
        function submitEnlistForm() {
            const form = document.querySelector('.enlist-form');
            if (form) {
                form.submit();
            }
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('enlistModal');
            if (event.target === modal) {
                closeEnlistModal();
            }
        });
        
        // Initialize dashboard when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            new Dashboard();
        });
    </script>
</body>
</html>
