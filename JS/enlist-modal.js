// Enlist Modal JavaScript

// Global variables for focus management
let enlistModalFocusableElements = [];
let enlistFirstFocusableElement = null;
let enlistLastFocusableElement = null;

// Tab switching functionality
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
    
    // Update focus management for the new tab
    updateTabFocus(tabName);
    
    // Mobile optimization for the new tab
    const modal = document.getElementById('enlistModal');
    if (modal && window.innerWidth <= 768) {
        optimizeForMobile(modal);
    }
}

function updateTabFocus(tabName) {
    const modal = document.getElementById('enlistModal');
    if (!modal) return;
    
    // Update focusable elements for the current tab
    const activeTabContent = document.getElementById(`${tabName}-tab`);
    if (activeTabContent) {
        enlistModalFocusableElements = activeTabContent.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (enlistModalFocusableElements.length > 0) {
            enlistFirstFocusableElement = enlistModalFocusableElements[0];
            enlistLastFocusableElement = enlistModalFocusableElements[enlistModalFocusableElements.length - 1];
        }
    }
}

// Enlist Modal Functions
function openEnlistModal() {
    const modal = document.getElementById('enlistModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');
        
        // Prevent background scrolling
        preventBackgroundScroll();
        
        // Set up focus management
        setupEnlistModalFocus(modal);
        
        // Focus on first input
        setTimeout(() => {
            if (enlistFirstFocusableElement) {
                enlistFirstFocusableElement.focus();
            }
        }, 100);
        
        // Add event listeners for focus trapping
        addEnlistModalEventListeners(modal);
        
        // Mobile optimizations
        optimizeForMobile(modal);
        
        // Initialize tab functionality
        initializeTabs();
    }
}

function closeEnlistModal() {
    const modal = document.getElementById('enlistModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        // Restore background scrolling
        restoreBackgroundScroll();
        
        // Remove event listeners
        removeEnlistModalEventListeners(modal);
        
        // Reset form
        const form = modal.querySelector('.enlist-form');
        if (form) {
            form.reset();
        }
        
        // Reset to dashboard tab
        switchTab('dashboard');
        
        // Clean up mobile optimizations
        cleanupMobileOptimizations(modal);
    }
}

// Enhanced enlist modal focus management
function setupEnlistModalFocus(modal) {
    // Get all focusable elements within the modal
    enlistModalFocusableElements = modal.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    
    if (enlistModalFocusableElements.length > 0) {
        enlistFirstFocusableElement = enlistModalFocusableElements[0];
        enlistLastFocusableElement = enlistModalFocusableElements[enlistModalFocusableElements.length - 1];
    }
    
    // Store the element that had focus before modal opened
    modal.dataset.previousFocus = document.activeElement ? document.activeElement.id || 'body' : 'body';
}

function addEnlistModalEventListeners(modal) {
    // Focus trap with Tab key
    modal.addEventListener('keydown', handleEnlistModalKeydown);
    
    // Prevent clicks outside modal from closing it (unless clicking on backdrop)
    modal.addEventListener('click', handleEnlistModalClick);
    
    // Handle window resize to ensure modal stays properly positioned
    window.addEventListener('resize', () => handleEnlistModalResize(modal));
    
    // Handle orientation change for mobile
    window.addEventListener('orientationchange', () => handleOrientationChange(modal));
}

function removeEnlistModalEventListeners(modal) {
    modal.removeEventListener('keydown', handleEnlistModalKeydown);
    modal.removeEventListener('click', handleEnlistModalClick);
    window.removeEventListener('resize', () => handleEnlistModalResize(modal));
    window.removeEventListener('orientationchange', () => handleOrientationChange(modal));
}

function handleEnlistModalKeydown(event) {
    if (event.key === 'Tab') {
        if (event.shiftKey) {
            // Shift + Tab: go to previous element
            if (document.activeElement === enlistFirstFocusableElement) {
                event.preventDefault();
                enlistLastFocusableElement.focus();
            }
        } else {
            // Tab: go to next element
            if (document.activeElement === enlistLastFocusableElement) {
                event.preventDefault();
                enlistFirstFocusableElement.focus();
            }
        }
    } else if (event.key === 'Escape') {
        // Close modal on Escape
        closeEnlistModal();
    }
}

function handleEnlistModalClick(event) {
    // Only close if clicking on the modal backdrop (not on modal content)
    if (event.target === event.currentTarget) {
        closeEnlistModal();
    }
}

function handleEnlistModalResize(modal) {
    // Ensure modal stays centered and properly sized on window resize
    if (modal.style.display === 'flex') {
        // Force a reflow to ensure proper positioning
        modal.style.display = 'none';
        modal.offsetHeight; // Trigger reflow
        modal.style.display = 'flex';
        
        // Re-apply mobile optimizations if needed
        if (window.innerWidth <= 768) {
            optimizeForMobile(modal);
        }
    }
}

function handleOrientationChange(modal) {
    // Wait for orientation change to complete
    setTimeout(() => {
        if (modal.style.display === 'flex') {
            // Recalculate modal dimensions
            const modalContent = modal.querySelector('.enlist-modal');
            if (modalContent) {
                // Force a reflow to ensure proper sizing
                modalContent.style.display = 'none';
                modalContent.offsetHeight; // Trigger reflow
                modalContent.style.display = 'flex';
            }
            
            // Update mobile optimizations
            optimizeForMobile(modal);
        }
    }, 100);
}

// Mobile optimizations for enlist modal
function optimizeForMobile(modal) {
    // Check if device is mobile
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Ensure modal takes full screen on mobile
        modal.style.alignItems = 'flex-start';
        modal.style.justifyContent = 'flex-start';
        
        // Add mobile-specific event listeners
        addMobileEventListeners(modal);
        
        // Optimize table scrolling on mobile
        const tableContainer = modal.querySelector('.enlist-table-container');
        if (tableContainer) {
            tableContainer.style.webkitOverflowScrolling = 'touch';
            tableContainer.style.overscrollBehavior = 'contain';
        }
        
        // Optimize form scrolling on mobile
        const formContainer = modal.querySelector('.enlist-form-container');
        if (formContainer) {
            formContainer.style.webkitOverflowScrolling = 'touch';
            formContainer.style.overscrollBehavior = 'contain';
        }
    }
}

function cleanupMobileOptimizations(modal) {
    // Reset modal positioning
    modal.style.alignItems = '';
    modal.style.justifyContent = '';
    
    // Remove mobile event listeners
    removeMobileEventListeners(modal);
}

function addMobileEventListeners(modal) {
    // Handle mobile-specific interactions
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Add touch event handling for better mobile scrolling
        modal.addEventListener('touchstart', handleMobileTouchStart, { passive: true });
        modal.addEventListener('touchmove', handleMobileTouchMove, { passive: false });
        modal.addEventListener('touchend', handleMobileTouchEnd, { passive: true });
    }
}

function removeMobileEventListeners(modal) {
    modal.removeEventListener('touchstart', handleMobileTouchStart);
    modal.removeEventListener('touchmove', handleMobileTouchMove);
    modal.removeEventListener('touchend', handleMobileTouchEnd);
}

// Mobile touch handling
let touchStartY = 0;
let touchStartX = 0;

function handleMobileTouchStart(event) {
    const touch = event.touches[0];
    touchStartY = touch.clientY;
    touchStartX = touch.clientX;
}

function handleMobileTouchMove(event) {
    const touch = event.touches[0];
    const touchY = touch.clientY;
    const touchX = touch.clientX;
    const deltaY = touchY - touchStartY;
    const deltaX = touchX - touchStartX;
    
    // Find the scrollable container
    const target = event.target;
    const scrollableContainer = target.closest('.enlist-table-container, .enlist-form-container');
    
    if (scrollableContainer) {
        const isAtTop = scrollableContainer.scrollTop === 0;
        const isAtBottom = scrollableContainer.scrollTop + scrollableContainer.clientHeight >= scrollableContainer.scrollHeight;
        
        // Prevent overscroll bounce
        if ((isAtTop && deltaY > 0) || (isAtBottom && deltaY < 0)) {
            event.preventDefault();
        }
    }
}

function handleMobileTouchEnd(event) {
    touchStartY = 0;
    touchStartX = 0;
}

// Enlist form functions - Updated for tab system
function showEnlistForm() {
    // Switch to enlist tab
    switchTab('enlist');
    
    // Focus on first form input
    setTimeout(() => {
        const firstInput = document.querySelector('#enlist-tab input, #enlist-tab select, #enlist-tab textarea');
        if (firstInput) {
            firstInput.focus();
        }
    }, 100);
}

function hideEnlistForm() {
    // Switch back to equipment tab
    switchTab('equipment');
    
    // Focus on first table element
    setTimeout(() => {
        const firstFocusable = document.querySelector('#equipment-tab button, #equipment-tab [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            firstFocusable.focus();
        }
    }, 100);
}

function submitEnlistForm() {
    // Get form data
    const form = document.querySelector('.enlist-form');
    if (form) {
        const formData = new FormData(form);
        
        // Basic validation
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#ef4444';
                field.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
            } else {
                field.style.borderColor = '#d1d5db';
                field.style.boxShadow = 'none';
            }
        });
        
        if (isValid) {
            // Show success message
            showNotification('Equipment added successfully!', 'success');
            
            // Reset form
            form.reset();
            
            // Switch back to equipment tab
            switchTab('equipment');
            
            // Refresh table data (you would implement this)
            // refreshEquipmentTable();
        } else {
            showNotification('Please fill in all required fields', 'error');
        }
    }
}

// Equipment management functions
function editEquipment(id) {
    showNotification(`Editing equipment ${id}`, 'info');
    // Here you would implement the edit functionality
    // For now, just show a notification
}

function deleteEquipment(id) {
    if (confirm('Are you sure you want to delete this equipment?')) {
        showNotification(`Equipment ${id} deleted`, 'success');
        // Here you would implement the delete functionality
        // For now, just show a notification
    }
}

// Function to show sample data for demonstration
function showSampleData() {
    const noDataRow = document.querySelector('.enlist-table tr.no-data');
    const sampleRow = document.querySelector('.enlist-table tr.sample-equipment');
    
    if (noDataRow && sampleRow) {
        noDataRow.style.display = 'none';
        sampleRow.style.display = 'table-row';
        
        // Update summary
        const summaryItems = document.querySelectorAll('.enlist-summary .summary-item span');
        if (summaryItems.length >= 2) {
            summaryItems[1].textContent = 'Total Equipment: 1';
            summaryItems[2].textContent = 'Showing: 1 of 1';
        }
    }
}

// Function to hide sample data
function hideSampleData() {
    const noDataRow = document.querySelector('.enlist-table tr.no-data');
    const sampleRow = document.querySelector('.enlist-table tr.sample-equipment');
    
    if (noDataRow && sampleRow) {
        noDataRow.style.display = 'table-row';
        sampleRow.style.display = 'none';
        
        // Reset summary
        const summaryItems = document.querySelectorAll('.enlist-summary .summary-item span');
        if (summaryItems.length >= 2) {
            summaryItems[1].textContent = 'Total Equipment: 0';
            summaryItems[2].textContent = 'Showing: 0 of 0';
        }
    }
}

// Initialize enlist modal functionality
function initializeEnlistModal() {
    const filterCategory = document.getElementById('filterCategory');
    const filterStatus = document.getElementById('filterStatus');
    const searchInput = document.getElementById('searchEquipment');
    const filterBtn = document.querySelector('.btn-filter');
    const clearBtn = document.querySelector('.btn-clear');

    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            // Implement filter logic here
            console.log('Filtering by:', {
                category: filterCategory ? filterCategory.value : '',
                status: filterStatus ? filterStatus.value : '',
                search: searchInput ? searchInput.value : ''
            });
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (filterCategory) filterCategory.value = '';
            if (filterStatus) filterStatus.value = '';
            if (searchInput) searchInput.value = '';
        });
    }
    
    // Initialize form submission
    const form = document.querySelector('.enlist-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEnlistForm();
        });
    }
    
    // Initialize file upload functionality
    const fileUploadArea = document.querySelector('.file-upload-area');
    const fileInput = document.querySelector('input[type="file"]');
    
    if (fileUploadArea && fileInput) {
        fileUploadArea.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileName = this.files[0].name;
                const uploadPlaceholder = fileUploadArea.querySelector('.upload-placeholder');
                if (uploadPlaceholder) {
                    uploadPlaceholder.innerHTML = `
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <p>File selected: ${fileName}</p>
                        <span>Click to change file</span>
                    `;
                }
            }
        });
    }
}

// Close enlist modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('enlistModal');
    if (event.target === modal) {
        closeEnlistModal();
    }
});

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeEnlistModal();
});

// Missing utility functions
function preventBackgroundScroll() {
    document.body.style.overflow = 'hidden';
}

function restoreBackgroundScroll() {
    document.body.style.overflow = '';
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
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
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
