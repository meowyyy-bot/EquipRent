// Browse Equipment JavaScript

class EquipmentBrowser {
    constructor() {
        this.currentPage = 1;
        this.currentFilters = {
            search: '',
            category: '',
            location: '',
            min_price: '',
            max_price: '',
            sort: 'created_at',
            order: 'DESC',
            page: 1,
            limit: 12
        };
        this.currentView = 'grid';
        this.equipment = [];
        this.categories = [];
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        this.loadCategories();
        this.bindEvents();
        this.loadEquipment();
        this.setupMobileNavigation();
    }
    
    bindEvents() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(() => {
                this.currentFilters.search = searchInput.value.trim();
                this.currentPage = 1;
                this.currentFilters.page = 1;
                this.loadEquipment();
            }, 500));
            
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.currentFilters.search = searchInput.value.trim();
                    this.currentPage = 1;
                    this.currentFilters.page = 1;
                    this.loadEquipment();
                }
            });
        }
        
        if (searchBtn) {
            searchBtn.addEventListener('click', () => {
                this.currentFilters.search = searchInput.value.trim();
                this.currentPage = 1;
                this.currentFilters.page = 1;
                this.loadEquipment();
            });
        }
        
        // Filter controls
        const categoryFilter = document.getElementById('categoryFilter');
        const locationFilter = document.getElementById('locationFilter');
        const priceRangeFilter = document.getElementById('priceRangeFilter');
        const sortFilter = document.getElementById('sortFilter');
        
        if (categoryFilter) {
            categoryFilter.addEventListener('change', () => {
                this.currentFilters.category = categoryFilter.value;
                this.currentPage = 1;
                this.currentFilters.page = 1;
                this.loadEquipment();
            });
        }
        
        if (locationFilter) {
            locationFilter.addEventListener('change', () => {
                this.currentFilters.location = locationFilter.value;
                this.currentPage = 1;
                this.currentFilters.page = 1;
                this.loadEquipment();
            });
        }
        
        if (priceRangeFilter) {
            priceRangeFilter.addEventListener('change', () => {
                const range = priceRangeFilter.value;
                if (range) {
                    const [min, max] = range.split('-');
                    this.currentFilters.min_price = min;
                    this.currentFilters.max_price = max === '+' ? '' : max;
                } else {
                    this.currentFilters.min_price = '';
                    this.currentFilters.max_price = '';
                }
                this.currentPage = 1;
                this.currentFilters.page = 1;
                this.loadEquipment();
            });
        }
        
        if (sortFilter) {
            sortFilter.addEventListener('change', () => {
                const [sort, order] = sortFilter.value.split('-');
                this.currentFilters.sort = sort;
                this.currentFilters.order = order;
                this.currentPage = 1;
                this.currentFilters.page = 1;
                this.loadEquipment();
            });
        }
        
        // Filter actions
        const clearFiltersBtn = document.getElementById('clearFilters');
        const applyFiltersBtn = document.getElementById('applyFilters');
        
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }
        
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', () => {
                this.loadEquipment();
            });
        }
        
        // View options
        const viewBtns = document.querySelectorAll('.view-btn');
        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.switchView(btn.dataset.view);
            });
        });
        
        // Reset search
        const resetSearchBtn = document.getElementById('resetSearch');
        if (resetSearchBtn) {
            resetSearchBtn.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }
    }
    
    setupMobileNavigation() {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        
        if (hamburger && navMenu) {
            hamburger.addEventListener('click', function() {
                hamburger.classList.toggle('active');
                navMenu.classList.toggle('active');
                
                if (navMenu.classList.contains('active')) {
                    document.body.classList.add('modal-open');
                } else {
                    document.body.classList.remove('modal-open');
                }
            });
            
            // Close mobile menu when clicking on a link
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    hamburger.classList.remove('active');
                    navMenu.classList.remove('active');
                    document.body.classList.remove('modal-open');
                });
            });
        }
    }
    
    async loadCategories() {
        try {
            const response = await fetch('controller/equipment.php?action=categories');
            const data = await response.json();
            
            if (data.success) {
                this.categories = data.data;
                this.populateCategoryFilter();
            }
        } catch (error) {
            console.error('Failed to load categories:', error);
        }
    }
    
    populateCategoryFilter() {
        const categoryFilter = document.getElementById('categoryFilter');
        if (!categoryFilter) return;
        
        // Clear existing options except "All Categories"
        while (categoryFilter.children.length > 1) {
            categoryFilter.removeChild(categoryFilter.lastChild);
        }
        
        // Add category options
        this.categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.category_name.toLowerCase();
            option.textContent = category.category_name;
            categoryFilter.appendChild(option);
        });
    }
    
    async loadEquipment() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoadingState();
        
        console.log('Loading equipment with filters:', this.currentFilters);
        
        try {
            const queryParams = new URLSearchParams();
            Object.entries(this.currentFilters).forEach(([key, value]) => {
                if (value) {
                    queryParams.append(key, value);
                }
            });
            
            const url = `controller/equipment.php?action=browse&${queryParams.toString()}`;
            console.log('Fetching from URL:', url);
            
            const response = await fetch(url);
            console.log('Response status:', response.status);
            
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                this.equipment = data.data;
                console.log('Equipment loaded:', this.equipment.length, 'items');
                this.displayEquipment();
                this.updateResultsCount(data.pagination.total);
                this.displayPagination(data.pagination);
                this.hideLoadingState();
                
                if (this.equipment.length === 0) {
                    this.showNoResultsState();
                } else {
                    this.hideNoResultsState();
                }
            } else {
                console.error('API error:', data.error);
                this.showError(data.error || 'Failed to load equipment');
                this.hideLoadingState();
            }
        } catch (error) {
            console.error('Failed to load equipment:', error);
            this.showError('Network error. Please try again.');
            this.hideLoadingState();
        }
        
        this.isLoading = false;
    }
    
    displayEquipment() {
        const grid = document.getElementById('equipmentGrid');
        if (!grid) return;
        
        grid.innerHTML = '';
        
        this.equipment.forEach(item => {
            const card = this.createEquipmentCard(item);
            grid.appendChild(card);
        });
    }
    
    createEquipmentCard(item) {
        const card = document.createElement('div');
        card.className = `equipment-card ${this.currentView === 'list' ? 'list-view' : ''}`;
        card.dataset.equipmentId = item.equipment_id;
        
        // Get first photo or use placeholder (we don't have photos yet, so use placeholder)
        const photoUrl = null; // item.photos && item.photos.length > 0 ? item.photos[0] : null;
        
        card.innerHTML = `
            <div class="equipment-image">
                ${photoUrl ? 
                    `<img src="${photoUrl}" alt="${item.name}" loading="lazy">` :
                    `<div class="equipment-placeholder">
                        <i class="fas fa-tools"></i>
                    </div>`
                }
                <div class="equipment-badges">
                    ${item.is_available_today ? 
                        '<span class="badge badge-available">Available Today</span>' : 
                        '<span class="badge badge-unavailable">Not Available</span>'
                    }
                    ${item.condition_status ? 
                        `<span class="badge badge-condition badge-${item.condition_status}">${item.condition_status}</span>` : 
                        ''
                    }
                </div>
            </div>
            <div class="equipment-info">
                <h3 class="equipment-name">${item.name}</h3>
                <p class="equipment-description">${this.truncateText(item.description, 100)}</p>
                
                <div class="equipment-meta">
                    <div class="equipment-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${item.city || 'Location not specified'}</span>
                    </div>
                    <div class="equipment-owner">
                        <i class="fas fa-user"></i>
                        <span>${item.first_name} ${item.last_name}</span>
                        ${item.owner_rating ? 
                            `<div class="owner-rating">
                                <i class="fas fa-star"></i>
                                <span>${item.owner_rating}</span>
                                <span class="rating-count">(${item.owner_reviews || 0})</span>
                            </div>` : 
                            ''
                        }
                    </div>
                </div>
                
                <div class="equipment-pricing">
                    <div class="price-main">
                        <span class="price-amount">${item.daily_rate_formatted}</span>
                        <span class="price-period">/day</span>
                    </div>
                    ${item.weekly_rate ? 
                        `<div class="price-secondary">₱${item.weekly_rate}/week</div>` : 
                        ''
                    }
                    ${item.monthly_rate ? 
                        `<div class="price-secondary">₱${item.monthly_rate}/month</div>` : 
                        ''
                    }
                </div>
                
                <div class="equipment-actions">
                    <button class="btn btn-outline quick-view-btn" onclick="equipmentBrowser.quickView(${item.equipment_id})">
                        <i class="fas fa-eye"></i> Quick View
                    </button>
                    ${this.isUserLoggedIn() ? 
                        `<button class="btn btn-primary rent-btn" onclick="equipmentBrowser.rentEquipment(${item.equipment_id})">
                            <i class="fas fa-calendar-plus"></i> Rent Now
                        </button>` :
                        `<button class="btn btn-primary rent-btn" onclick="equipmentBrowser.openAuthModal()">
                            <i class="fas fa-sign-in-alt"></i> Login to Rent
                        </button>`
                    }
                </div>
            </div>
        `;
        
        return card;
    }
    
    async quickView(equipmentId) {
        try {
            const response = await fetch(`controller/equipment.php?action=view&id=${equipmentId}`);
            const data = await response.json();
            
            if (data.success) {
                this.showQuickViewModal(data.data);
            } else {
                this.showError(data.error || 'Failed to load equipment details');
            }
        } catch (error) {
            console.error('Failed to load equipment details:', error);
            this.showError('Network error. Please try again.');
        }
    }
    
    showQuickViewModal(equipment) {
        const modal = document.getElementById('quickViewModal');
        const content = document.getElementById('quickViewContent');
        
        if (!modal || !content) return;
        
        // Get first photo or use placeholder
        const photoUrl = equipment.photos && equipment.photos.length > 0 ? equipment.photos[0] : null;
        
        content.innerHTML = `
            <div class="quick-view-content">
                <div class="quick-view-image">
                    ${photoUrl ? 
                        `<img src="${photoUrl}" alt="${equipment.name}">` :
                        `<div class="equipment-placeholder">
                            <i class="fas fa-tools"></i>
                        </div>`
                    }
                </div>
                <div class="quick-view-details">
                    <h2>${equipment.name}</h2>
                    <p class="description">${equipment.description}</p>
                    
                    <div class="quick-view-meta">
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${equipment.city || 'Location not specified'}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span>${equipment.first_name} ${equipment.last_name}</span>
                        </div>
                        ${equipment.brand ? 
                            `<div class="meta-item">
                                <i class="fas fa-tag"></i>
                                <span>${equipment.brand}</span>
                            </div>` : ''
                        }
                        ${equipment.condition_status ? 
                            `<div class="meta-item">
                                <i class="fas fa-info-circle"></i>
                                <span>${equipment.condition_status}</span>
                            </div>` : ''
                        }
                    </div>
                    
                    <div class="quick-view-pricing">
                        <div class="price-main">
                            <span class="price-amount">${equipment.daily_rate_formatted}</span>
                            <span class="price-period">/day</span>
                        </div>
                        ${equipment.weekly_rate ? 
                            `<div class="price-option">₱${equipment.weekly_rate}/week</div>` : ''
                        }
                        ${equipment.monthly_rate ? 
                            `<div class="price-option">₱${equipment.monthly_rate}/month</div>` : ''
                        }
                    </div>
                    
                    <div class="quick-view-actions">
                        ${this.isUserLoggedIn() ? 
                            `<button class="btn btn-primary" onclick="equipmentBrowser.rentEquipment(${equipment.equipment_id})">
                                <i class="fas fa-calendar-plus"></i> Rent This Equipment
                            </button>` :
                            `<button class="btn btn-primary" onclick="equipmentBrowser.openAuthModal()">
                                <i class="fas fa-sign-in-alt"></i> Login to Rent
                            </button>`
                        }
                        <button class="btn btn-outline" onclick="equipmentBrowser.viewFullDetails(${equipment.equipment_id})">
                            <i class="fas fa-external-link-alt"></i> View Full Details
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');
    }
    
    closeQuickViewModal() {
        const modal = document.getElementById('quickViewModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }
    
    rentEquipment(equipmentId) {
        console.log('rentEquipment called with ID:', equipmentId);
        
        // Check if user is logged in using the global variable
        if (!this.isUserLoggedIn()) {
            // Show login required notification
            this.showError('Please log in first to rent equipment. Click the login button in the navigation.');
            return;
        }
        
        // User is logged in, proceed to rental page
        const rentUrl = `rent.php?equipment_id=${equipmentId}`;
        console.log('Redirecting to:', rentUrl);
        
        window.location.href = rentUrl;
    }
    
    viewFullDetails(equipmentId) {
        // Redirect to full equipment details page
        window.location.href = `equipment.php?id=${equipmentId}`;
    }
    
    openAuthModal() {
        const modal = document.getElementById('authModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.classList.add('modal-open');
        }
    }
    
    switchView(view) {
        this.currentView = view;
        
        // Update view buttons
        const viewBtns = document.querySelectorAll('.view-btn');
        viewBtns.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });
        
        // Update equipment grid
        const grid = document.getElementById('equipmentGrid');
        if (grid) {
            grid.className = `equipment-grid ${view === 'list' ? 'list-view' : ''}`;
        }
        
        // Re-render equipment with new view
        this.displayEquipment();
    }
    
    clearAllFilters() {
        // Reset filter values
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const locationFilter = document.getElementById('locationFilter');
        const priceRangeFilter = document.getElementById('priceRangeFilter');
        const sortFilter = document.getElementById('sortFilter');
        
        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = '';
        if (locationFilter) locationFilter.value = '';
        if (priceRangeFilter) priceRangeFilter.value = '';
        if (sortFilter) sortFilter.value = 'created_at-DESC';
        
        // Reset current filters
        this.currentFilters = {
            search: '',
            category: '',
            location: '',
            min_price: '',
            max_price: '',
            sort: 'created_at',
            order: 'DESC',
            page: 1,
            limit: 12
        };
        
        this.currentPage = 1;
        this.loadEquipment();
    }
    
    updateResultsCount(count) {
        const resultsCount = document.getElementById('resultsCount');
        if (resultsCount) {
            resultsCount.textContent = count.toLocaleString();
        }
    }
    
    displayPagination(pagination) {
        const paginationContainer = document.getElementById('pagination');
        if (!paginationContainer) return;
        
        if (pagination.total_pages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }
        
        paginationContainer.style.display = 'block';
        
        let paginationHTML = '<div class="pagination-controls">';
        
        // Previous button
        if (pagination.current_page > 1) {
            paginationHTML += `<button class="pagination-btn prev" onclick="equipmentBrowser.goToPage(${pagination.current_page - 1})">
                <i class="fas fa-chevron-left"></i> Previous
            </button>`;
        }
        
        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `<button class="pagination-btn ${i === pagination.current_page ? 'active' : ''}" 
                onclick="equipmentBrowser.goToPage(${i})">${i}</button>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.total_pages) {
            paginationHTML += `<button class="pagination-btn next" onclick="equipmentBrowser.goToPage(${pagination.current_page + 1})">
                Next <i class="fas fa-chevron-right"></i>
            </button>`;
        }
        
        paginationHTML += '</div>';
        
        paginationContainer.innerHTML = paginationHTML;
    }
    
    goToPage(page) {
        this.currentPage = page;
        this.currentFilters.page = page;
        this.loadEquipment();
        
        // Scroll to top of results
        const resultsSection = document.querySelector('.results-section');
        if (resultsSection) {
            resultsSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    showLoadingState() {
        const loadingState = document.getElementById('loadingState');
        const equipmentGrid = document.getElementById('equipmentGrid');
        
        if (loadingState) loadingState.style.display = 'block';
        if (equipmentGrid) equipmentGrid.style.display = 'none';
    }
    
    hideLoadingState() {
        const loadingState = document.getElementById('loadingState');
        const equipmentGrid = document.getElementById('equipmentGrid');
        
        if (loadingState) loadingState.style.display = 'none';
        if (equipmentGrid) equipmentGrid.style.display = 'grid';
    }
    
    showNoResultsState() {
        const noResultsState = document.getElementById('noResultsState');
        if (noResultsState) {
            noResultsState.style.display = 'block';
        }
    }
    
    hideNoResultsState() {
        const noResultsState = document.getElementById('noResultsState');
        if (noResultsState) {
            noResultsState.style.display = 'none';
        }
    }
    
    showError(message) {
        // Create and show error notification
        const notification = document.createElement('div');
        notification.className = 'notification notification-error';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-exclamation-circle"></i>
                <span>${message}</span>
            </div>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
            font-weight: 500;
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 5 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
    
    truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    isUserLoggedIn() {
        // Check if user is logged in using the global variable set by PHP
        return window.isUserLoggedIn === true;
    }
    
    refreshEquipmentDisplay() {
        // Re-render equipment to update button states after login/logout
        this.displayEquipment();
    }
    
    refreshLoginStatus() {
        // Check if login status has changed and refresh if needed
        const currentStatus = this.isUserLoggedIn();
        if (currentStatus !== window.isUserLoggedIn) {
            window.isUserLoggedIn = currentStatus;
            this.refreshEquipmentDisplay();
        }
    }
    
    async checkSessionStatus() {
        try {
            const response = await fetch('controller/auth.php?action=check_status');
            const data = await response.json();
            
            if (data.success && data.logged_in !== window.isUserLoggedIn) {
                window.isUserLoggedIn = data.logged_in;
                this.refreshEquipmentDisplay();
            }
        } catch (error) {
            console.error('Failed to check session status:', error);
        }
    }
}

// Global functions for modal interactions
function closeQuickViewModal() {
    if (window.equipmentBrowser) {
        window.equipmentBrowser.closeQuickViewModal();
    }
}

function openAuthModal() {
    if (window.equipmentBrowser) {
        window.equipmentBrowser.openAuthModal();
    }
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        // Refresh equipment display to update button states
        if (window.equipmentBrowser) {
            setTimeout(() => {
                window.equipmentBrowser.refreshEquipmentDisplay();
            }, 100);
        }
    }
}

function updateLoginStatus(isLoggedIn) {
    // Update global login status
    window.isUserLoggedIn = isLoggedIn;
    
    // Refresh equipment display to update button states
    if (window.equipmentBrowser) {
        window.equipmentBrowser.refreshEquipmentDisplay();
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
            // Update global login status
            updateLoginStatus(false);
            
            // Redirect to homepage
            window.location.href = data.redirect;
        }
    } catch (error) {
        console.error('Logout error:', error);
        // Fallback redirect
        window.location.href = '/New folder/hackathin/index.php';
    }
}

function switchTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.form-container');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedTabContent = document.getElementById(`${tabName}Form`);
    if (selectedTabContent) {
        selectedTabContent.classList.add('active');
    }
    
    // Activate selected tab button
    const selectedTab = document.querySelector(`[onclick="switchTab('${tabName}')"]`);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const quickViewModal = document.getElementById('quickViewModal');
    const authModal = document.getElementById('authModal');
    
    if (event.target === quickViewModal) {
        closeQuickViewModal();
    }
    
    if (event.target === authModal) {
        closeAuthModal();
    }
});

// Close modals on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeQuickViewModal();
        closeAuthModal();
    }
});

// Initialize the equipment browser when the page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Browse page loaded, initializing EquipmentBrowser...');
    
    // Create global instance
    window.equipmentBrowser = new EquipmentBrowser();
    
    console.log('EquipmentBrowser initialized:', window.equipmentBrowser);
    
    // Set up periodic login status check
    setInterval(() => {
        if (window.equipmentBrowser) {
            window.equipmentBrowser.checkSessionStatus();
        }
    }, 2000); // Check every 2 seconds
});
